<?php

namespace App\Controllers;

use App\Helpers\DBIP;
use App\Helpers\Helper;
use App\Helpers\Sms;

use App\Helpers\SandBox;
use App\Helpers\RandomStringGenerator;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

use App\Helpers\Browser;
use App\Models\Country;
use App\Models\State;
use App\Models\Participant;
use App\Models\PQrCode;
use App\Models\Presentation;
use App\Models\Attestation;
use App\Models\Payment;

use App\Helpers\MailSandBox;
use App\Controllers\ParticipantController;



use Bes\Twig\Extension\MobileDetectExtension;
use Psr7Middlewares\Middleware\ClientIp;

use CodeItNow\BarcodeBundle\Utils\QrCode;

use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\StreamReader;

use App\Helpers\PayTech;


//require __DIR__ .'/../../vendor/paydunya/paydunya.php';


class PaiementController extends Controller
{

    /** PAYDUNYA */
    public static function init($container)
    {

        $domain = $container->domain_url;
        
        \Paydunya\Setup::setMasterKey($container->pd_mainKey);
        \Paydunya\Setup::setPublicKey($container->pd_publicKey);
        \Paydunya\Setup::setPrivateKey($container->pd_privateKey);
        \Paydunya\Setup::setToken($container->pd_token);
        
        \Paydunya\Setup::setMode("test"); // Optionnel en mode test. Utilisez cette option pour les paiements tests.
       
       //Configuration des informations de votre service/entreprise

        \Paydunya\Checkout\Store::setName("SOSECAR"); // Seul le nom est requis
        \Paydunya\Checkout\Store::setTagline("La Société Sénégalaise de Cardiologie");
        \Paydunya\Checkout\Store::setPhoneNumber("77 504 63 21");
        \Paydunya\Checkout\Store::setPostalAddress("Dakar");
        \Paydunya\Checkout\Store::setWebsiteUrl("http://www.sosecar.sn");
        \Paydunya\Checkout\Store::setLogoUrl("http://46.101.9.153/sosecar/2023/src/v3/images/sosecar-1.svg");


        \Paydunya\Checkout\Store::setCallbackUrl($domain.$container->pd_callbackUrl);
        \Paydunya\Checkout\Store::setCancelUrl($domain.$container->pd_cancelUrl);
        \Paydunya\Checkout\Store::setReturnUrl($domain.$container->pd_returnUrl);

        $invoice = new \Paydunya\Checkout\CheckoutInvoice();

        $invoice->setDescription("4ème Cardiotech Sénégal - Innovations dans la prise en charge des valvulopathies");

        return $invoice;
    }

    
    public function initPayment($request, $response, $arg) {

        $ref = $arg['ref'];

       
        $participant = $this->helper->getParticipantFromRef($ref);

        if (!$participant) {
            return $this->view->render($response->withStatus(404),'errors/404.twig');
        }

        $unique_price = self::getUniquePrice($participant['formule']);

        $data_participant_for_paiement = [
            'service'       =>  'congres',
            'ref'           =>  $participant['ref'],
            'name'          =>  $participant['first_name']." ".$participant['last_name'],
            'phone'         =>  $participant['phone'],
            'status'        =>  $participant['payment_status'],
            'unique_price'  =>  $unique_price,
            'price'         =>  $unique_price*1
        ];
      
        $invoice_call_status = self::startPayment($data_participant_for_paiement, $this);

        $data_payment = [
            'invoice_url'           => $invoice_call_status['url'],
            'participant_ref'       => $ref,
            'participant_id'        => $participant['id'],
            'customer_name'         => $data_participant_for_paiement['name'],
            'customer_phone'        => $data_participant_for_paiement['phone'],
            'amount'                => $data_participant_for_paiement['price'],
        ];

        switch ($participant['payment_status']) {
            case 'cancelled':
                $action_link = $invoice_call_status['url'];
                $icon = '<i class="fi fi-rr-cross"></i> ';
                $title = "Paiement annulé";
                $text_link = "Reprendre";
                $class = "error-not";
                $message = "Le paiement a été annulé. <br>Si vous rencontrez des soucis, contactez-nous au 77 000 00 00.";
                break;

            case 'failed':
                $action_link = $invoice_call_status['url'];
                $text_link = "Réessayer";
                $icon = '<i class="fi fi-rr-cross"></i> ';
                $title = "Echec paiement";
                $class = "error-not";
                $message = "Le paiement a échoué. <br>Veuillez réessayer ! <br>Si vous rencontrez des soucis, contactez-nous au 77 000 00 00.";
                break;

            case 'completed':
                $action_link = $this->router->pathFor('home');
                $title = "Paiement réussi";
                $icon = '<i class="fi fi-rr-check"></i></span> ';
                $text_link = null;
                $class = "success-not";
                $message = "Votre paiement a réussi. <br>Un courriel de confirmation vous sera envoyé avec vos paramètres de connexion.";
                break;
            
            default:
                $title = "Paiement en cours";
                $action_link = $invoice_call_status['url'];
                $icon = '<i class="fi fi-rr-check"></i></span> ';
                $text_link = null;
                $class = "success-not";
                $message = 'Votre paiement est en cours de traitement. <br>Si vous n\'êtes pas redirigé(e) dans quelques secondes, veuillez cliquer <a href="#">ici</a>';
                break;
        }


        try {
            $the_paiement = Payment::where('participant_ref', $data_payment['participant_ref'])->firstOrFail();
            $up_payment = Payment::where('participant_ref', $data_payment['participant_ref'])->increment('attempts');
        } catch (\Throwable $th) {
            $new_payment = Payment::insertGetId($data_payment);
        }
        
        if ($participant['payment_status'] != "completed" && $participant['payment_status'] != "cancelled") {
            //$this->helper->debug($invoice_call_status['url']);
            
            return $response->withHeader('Location', $invoice_call_status['url']);
        }

        $data = [
            'page_title'  =>    $title,
            'link'        =>    $action_link,
            'text_link'   =>    $text_link,
            'icon'        =>    $icon,
            'class'       =>    $class,
            'message'     =>    $message,
        ];

        return $this->view->render($response, 'paiementInit.twig', compact('participant','data'));
    }

    public function initPrePayment($request, $response, $arg) {

        $ref = $arg['ref'];

       
        $participant = $this->helper->getParticipantFromRef($ref);

        if (!$participant) {
            return $this->view->render($response->withStatus(404),'errors/404.twig');
        }

        $unique_price = self::getUniquePrice(1);//150000;
        

        $data_participant_for_paiement = [
            'service'       =>  'precongres',
            'ref'           =>  $participant['ref'],
            'name'          =>  $participant['first_name']." ".$participant['last_name'],
            'phone'         =>  $participant['phone'],
            'status'        =>  $participant['payment_status_pc'],
            'unique_price'  =>  $unique_price,
            'price'         =>  $unique_price*1
        ];
      
        $invoice_call_status = self::startPayment($data_participant_for_paiement, $this);

        $data_payment = [
            'invoice_url'           => $invoice_call_status['url'],
            'participant_ref'       => $ref,
            'participant_id'        => $participant['id'],
            'customer_name'         => $data_participant_for_paiement['name'],
            'customer_phone'        => $data_participant_for_paiement['phone'],
            'amount'                => $data_participant_for_paiement['price'],
        ];

        switch ($participant['payment_status_pc']) {
            case 'cancelled':
                $action_link = $invoice_call_status['url'];
                $icon = '<i class="fi fi-rr-cross"></i> ';
                $title = "Paiement annulé";
                $text_link = "Reprendre";
                $class = "error-not";
                $message = "Le paiement a été annulé. <br>Si vous rencontrez des soucis, contactez-nous au 77 000 00 00.";
                break;

            case 'failed':
                $action_link = $invoice_call_status['url'];
                $text_link = "Réessayer";
                $icon = '<i class="fi fi-rr-cross"></i> ';
                $title = "Echec paiement";
                $class = "error-not";
                $message = "Le paiement a échoué. <br>Veuillez réessayer ! <br>Si vous rencontrez des soucis, contactez-nous au 77 000 00 00.";
                break;

            case 'completed':
                $action_link = $this->router->pathFor('home');
                $title = "Paiement réussi";
                $icon = '<i class="bx bx-check"></i> ';
                $text_link = null;
                $class = "success-not";
                $message = "Votre paiement a réussi. <br>Un courriel de confirmation vous sera envoyé avec vos paramètres de connexion.";
                break;
            
            default:
                $title = "Paiement en cours";
                $action_link = $invoice_call_status['url'];
                $icon = '<i class="bx bx-check"></i> ';
                $text_link = null;
                $class = "success-not";
                $message = 'Votre paiement est en cours de traitement. <br>Si vous n\'êtes pas redirigé(e) dans quelques secondes, veuillez cliquer <a href="#">ici</a>';
                break;
        }


        try {
            $the_paiement = Payment::where('participant_ref', $data_payment['participant_ref'])->firstOrFail();
            $up_payment = Payment::where('participant_ref', $data_payment['participant_ref'])->increment('attempts');
        } catch (\Throwable $th) {
            $new_payment = Payment::insertGetId($data_payment);
        }
        
        if ($participant['payment_status_pc'] != "completed" && $participant['payment_status_pc'] != "cancelled") {
            //$this->helper->debug($invoice_call_status['url']);
            return $response->withHeader('Location', $invoice_call_status['url']);
        }

        $data = [
            'page_title'  =>    $title,
            'link'        =>    $action_link,
            'text_link'   =>    $text_link,
            'icon'        =>    $icon,
            'class'       =>    $class,
            'message'     =>    $message,
        ];

        return $this->view->render($response, 'paiementInit.twig', compact('participant','data'));
    }


    public function startPayment($data, $container)
    {
        $invoice = self::init($container);

        $invoice->addItem("Ticket", 1, $data['unique_price'], $data['price'], "");

        $invoice->addCustomData("ref",$data['ref']);
        $invoice->addCustomData("name",$data['name']);
        $invoice->addCustomData("phone", $data['phone']);
        $invoice->addCustomData("service", $data['service']);
        $invoice->setTotalAmount(floatVal($data['price']));
        
        if($invoice->create()) {
            // header("Location: ".$invoice->getInvoiceUrl());

            return ['message' => 'Paiement initilialisé !', 'url' => $invoice->getInvoiceUrl(), 'invoice' => $invoice];

        }else{
            echo $invoice->response_text;
        }
        return ['message' => $invoice->response_text,  'url' => null];
    }


    public function callback($request, $response, $arg)
    {

        try {
            //Prenez votre MasterKey, hashez la et comparez le résultat au hash reçu par IPN
            if($_POST['data']['hash'] === hash('sha512', $this->pd_mainKey)) {
          
            if ($_POST['data']['status'] == "completed") {
                //Faites vos traitements backoffice ici...
                $data_payment = [
                     'customer_name'         => $_POST['data']['customer']['name'],
                     'customer_phone'        => $_POST['data']['customer']['phone'],
                     'customer_email'        => $_POST['data']['customer']['email'],
                     'token'                 => $_POST['data']['token'],
                     'status'                => $_POST['data']['status'],
                     'response_code'         => $_POST['data']['response_code'],
                     'response_text'         => $_POST['data']['response_text'],
                     'amount'                => $_POST['data']['total_amount'],
                     'receipt_url'           => $_POST['data']['receipt_url'],
                ];

                switch ($_POST['data']['custom_data']['service']) {
                    case 'congres':
                        break;

                    case 'precongres':
                        break;
                    
                    default:
                        # code...
                        break;
                }
                
                $up_payment = Payment::where('participant_ref', $_POST['data']['custom_data']['ref'])->update($data_payment);

                try {
                    
                    $participant = Participant::where('ref','=',$_POST['data']['custom_data']['ref'])->firstOrFail();
                    
                    // Activation du compte du participant
                    $validate = ParticipantController::validateAfterPayment($this, $participant->id, $_POST['data']['token']);
                    
                    // Envoi de courriels

                } catch (\Throwable $th) {
                    //throw $th;
                }
                
            }
        
            } else {
                die("Cette requête n'a pas été émise par PayDunya");
            }
            
        } catch(Exception $e) {
            die();
        }
            
    }

    public function return($request, $response, $arg)
    {
        $token = $_GET['token'];
        
        $action_link = $this->router->pathFor('home');
        $title = "Paiement réussi";
        $icon = '<i class="fi fi-rr-check"></i></span> ';
        $text_link = null;
        $class = "success-not";
        $message = "Votre paiement a réussi. <br>Un courriel de confirmation vous sera envoyé avec vos paramètres de connexion.";


        $data = [
            'page_title'  =>    $title,
            'link'        =>    $action_link,
            'text_link'   =>    $text_link,
            'icon'        =>    $icon,
            'class'       =>    $class,
            'message'     =>    $message,
        ];


        return $this->view->render($response, 'paymentReturn.twig', compact('participant','data'));
    }


    public function cancel($request, $response, $arg)
    {
        $token = $_GET['token'];

        $participant = $this->helper->getParticipant($this);
            
        if ($participant) {
            $action_link = $this->router->pathFor('paiement_init', $participant['ref']);
            $text_link = "Réessayer";
        }
        else{
            $action_link = $this->router->pathFor('p_login');
            $text_link = "Reprendre";
        }

        $icon = '<i class="fi fi-rr-cross"></i> ';
        $title = "Paiement annulé";
        $class = "error-not";
        $message = "Le paiement a été annulé. <br>Si vous rencontrez des soucis, contactez-nous au 77 000 00 00.";

        $data = [
            'page_title'  =>    $title,
            'link'        =>    $action_link,
            'text_link'   =>    $text_link,
            'icon'        =>    $icon,
            'class'       =>    $class,
            'message'     =>    $message,
        ];

        return $this->view->render($response, 'paymentReturn.twig', compact('participant','data'));
    }

    /** END PAYDUNYA */




    /** PAYTECH */
    public function initPaytechPayment($request, $response, $arg) {
        $ref = $arg['ref'];
        
        $service = $arg['service'];
       
        $participant = $this->helper->getParticipantFromRef($ref);

        if (!$participant) {
            return $this->view->render($response->withStatus(404),'errors/404.twig');
        }

        $unique_price = $service == 'congres' ? self::getUniquePrice($participant['formule']) : self::getUniquePricePC($participant['formule_pc']);
        
        $ticket_number  = $service == 'congres' ? $participant['ticket_number'] :  $participant['ticket_number_pc'];
        
        $data_participant_for_paiement = [
            'service'       =>  $service,
            'ref'           =>  $participant['ref'],
            'ticket_number' =>  $ticket_number,
            'name'          =>  $participant['first_name']." ".$participant['last_name'],
            'phone'         =>  $participant['phone'],
            'email'         =>  $participant['email'],
            'status'        =>  $participant['payment_status'],
            'unique_price'  =>  $unique_price,
            'price'         =>  $unique_price*1
        ];

        $invoice_call_status = json_decode(self::startPaymentPayTech($data_participant_for_paiement, $this));

        $data_payment = [
            'payment_from'          => "paytech_by_user",
            'invoice_url'           => $invoice_call_status->redirect_url,
            'participant_ref'       => $ref,
            'ticket_number'         => $ticket_number,
            'service'               => $service,
            'participant_id'        => $participant['id'],
            'token'                 => $invoice_call_status->token,
            'customer_name'         => $data_participant_for_paiement['name'],
            'customer_phone'        => $data_participant_for_paiement['phone'],
            'customer_email'        => $data_participant_for_paiement['email'],
            'amount'                => $data_participant_for_paiement['price'],
        ];

        $column_payment_status = $service == 'congres' ? 'payment_status' : 'payment_status_pc';

        switch ($participant[$column_payment_status]) {
            case 'cancelled':
                $action_link = $invoice_call_status->redirect_url;
                $icon = '<i class="fi fi-rr-cross"></i> ';
                $title = "Paiement annulé";
                $text_link = "Reprendre";
                $class = "error-not";
                $message = "Le paiement a été annulé. <br>Si vous rencontrez des soucis, contactez-nous au 77 000 00 00.";
                break;

            case 'failed':
                $action_link = $invoice_call_status->redirect_url;
                $text_link = "Réessayer";
                $icon = '<i class="fi fi-rr-cross"></i> ';
                $title = "Echec paiement";
                $class = "error-not";
                $message = "Le paiement a échoué. <br>Veuillez réessayer ! <br>Si vous rencontrez des soucis, contactez-nous au 77 000 00 00.";
                break;

            case 'completed':
                $action_link = $this->router->pathFor('home');
                $title = "Paiement réussi";
                $icon = '<i class="fi fi-rr-check"></i></span> ';
                $text_link = null;
                $class = "success-not";
                $message = "Votre paiement a réussi. <br>Un courriel de confirmation vous sera envoyé avec vos paramètres de connexion.";
                break;
            
            default:
                $title = "Paiement en cours";
                $action_link = $invoice_call_status->redirect_url;
                $icon = '<i class="fi fi-rr-check"></i></span> ';
                $text_link = null;
                $class = "success-not";
                $message = 'Votre paiement est en cours de traitement. <br>Si vous n\'êtes pas redirigé(e) dans quelques secondes, veuillez cliquer <a href="#">ici</a>';
                break;
        }


        try {
            $the_paiement = Payment::where([
                ['participant_ref', $data_payment['participant_ref']],
                ['ticket_number', $ticket_number]
                ])->firstOrFail();

            $up_payment = Payment::where([
                ['participant_ref', $data_payment['participant_ref']],
                ['ticket_number', $ticket_number]
                ])->increment('attempts');
                
        } catch (\Throwable $th) {
            $new_payment = Payment::insertGetId($data_payment);
        }
        
        if ($participant[$column_payment_status] != "completed" && $participant[$column_payment_status] != "cancelled") {
            
            return $response->withHeader('Location', $invoice_call_status->redirect_url);
        }

        $data = [
            'page_title'  =>    $title,
            'link'        =>    $action_link,
            'text_link'   =>    $text_link,
            'icon'        =>    $icon,
            'class'       =>    $class,
            'message'     =>    $message,
        ];

        return $this->view->render($response, 'paiementInit.twig', compact('participant','data'));
    }


    public function startPaymentPayTech($data, $container)
    {
        $apiKey  = $container->paytech_api_key;
        $apiSecret = $container->paytech_secret_key;
        $base_url  = $container->paytech_sosecar_base_url;
        
        $ref = $data['ref'];
        $amount = $data['price'];

        $item =  (object) array(
            'name'          =>  "Achat d'un billet de ". $amount." FCFA",
            'ref'           =>  $ref,
            'ticket_number' =>  $data['ticket_number'],
            'price'         =>  $amount,
            'tel'           =>  $data['phone'],
            'currency'      =>  'XOF',
            'service'       =>  $data['service'],
        );
        

        $responseArray = (new PayTech($apiKey, $apiSecret))->setQuery([
            'item_name' => $item->name,
            'item_price' => $item->price,
            'command_name' => "Achat de billets pour la 6éme Edition du Cardiotech Sénégal",
        ])->setCustomeField([
            'ref_command'   => $item->ref,
            'time_command'  => time(),  
            'service'       => $item->service,  
            'ip_user'       => $_SERVER['REMOTE_ADDR'],
            'lang'          => $_SERVER['HTTP_ACCEPT_LANGUAGE']
        ])
        ->setTestMode(false)
        ->setCurrency($item->currency)
        ->setRefCommand(uniqid())
        ->setNotificationUrl([
            'ipn_url' => $base_url.'/pay/return/'.$item->service.'/'.$ref.'/callback', //only https   
            'success_url' => $base_url.'/pay/return/'.$item->service.'/success/'.$ref,
            'cancel_url' =>   $base_url.'/pay/return/'.$item->service.'/cancel/'.$ref
        ])->send();
  
        return json_encode($responseArray);
    }

    public function paytechReturn($request, $response, $arg) {
        
        $ref = $arg['ref'];
        
        $status = $arg['status'];

        $service = $arg['service'];

      
        $participant = $this->helper->getParticipantFromRef($ref);

        if (!$participant) {
            return $this->view->render($response->withStatus(404),'errors/404.twig');
        }


        switch ($status) {
            case 'cancel':

                    $action_link = $this->router->pathFor('paiement_paytech_init', ['ref' => $participant['ref'], 'service' => $service]);
                    $text_link = "Réessayer";
                    
                    $icon = "<i class='bx bx-x'></i>";
                    $title = "Paiement annulé";
                    $class = "error-not";
                    $message = "Le paiement a été annulé. <br>Si vous rencontrez des soucis, contactez-nous au 77 504 63 21.";

                    $data = [
                        'page_title'  =>    $title,
                        'link'        =>    $action_link,
                        'text_link'   =>    $text_link,
                        'icon'        =>    $icon,
                        'class'       =>    $class,
                        'message'     =>    $message,
                    ];

                    return $this->view->render($response, 'paymentReturn.twig', compact('participant','data'));
                break;

            case 'success':
                $action_link = $this->router->pathFor('home');
                $title = "Paiement réussi";
                $icon = "<i class='bx bx-check' ></i>";
                $text_link = null;
                $class = "success-not";
                $message = "Votre paiement a réussi. <br>Un courriel de confirmation vous sera envoyé avec vos paramètres de connexion.";


                $data = [
                    'page_title'  =>    $title,
                    'link'        =>    $action_link,
                    'text_link'   =>    $text_link,
                    'icon'        =>    $icon,
                    'class'       =>    $class,
                    'message'     =>    $message,
                ];

                break;

            default:

                $action_link = $this->router->pathFor('paiement_paytech_init', ['ref' => $participant['ref'], 'service' => $service]);
                $text_link = "Réessayer";
                
                $icon = "<i class='bx bx-x'></i>";
                $title = "Paiement échoué";
                $class = "error-not";
                $message = "Le paiement a échoué. <br>Si vous rencontrez des soucis, contactez-nous au 77 504 63 21.";

                $data = [
                    'page_title'  =>    $title,
                    'link'        =>    $action_link,
                    'text_link'   =>    $text_link,
                    'icon'        =>    $icon,
                    'class'       =>    $class,
                    'message'     =>    $message,
                ];

                    return $this->view->render($response, 'paymentReturn.twig', compact('participant','data'));
                break;
        }

        $data = [
            'page_title'  =>    $title,
            'link'        =>    $action_link,
            'text_link'   =>    $text_link,
            'icon'        =>    $icon,
            'class'       =>    $class,
            'message'     =>    $message,
        ];



        $relativePath = 'uploads/newfile.txt'; // Chemin relatif par rapport au répertoire du script
        $dataa = ["apple", "banana", "cherry"];
        



        return $this->view->render($response, 'paymentReturn.twig', compact('participant','data'));
    }

    public function paytechCancel($request, $response, $arg) {
        
    }


    public function paytechCallback($request, $response, $arg) {
        
        $type_event = self::getPostParam('type_event');
        $custom_field = json_decode(self::getPostParam('custom_field'), true);
        $ref_command = self::getPostParam('ref_command');
        $item_name = self::getPostParam('item_name');
        $item_price = self::getPostParam('item_price');
        $devise = self::getPostParam('devise');
        $command_name = self::getPostParam('command_name');
        $env = self::getPostParam('env');
        $token = self::getPostParam('token');
        $api_key_sha256 = self::getPostParam('api_key_sha256');
        $api_secret_sha256 = self::getPostParam('api_secret_sha256');
        $my_api_key = $this->paytech_api_key;
        $my_api_secret = $this->paytech_secret_key;

        $service = $custom_field['service'];
        $ref = $custom_field['ref_command'];
        $ticket_number = $custom_field['ticket_number'];
        

        $dataDump = [ 
            $type_event ,
            $custom_field,
            $ref_command,
            $ref,
            $item_name,
            $item_price,
            $devise,
            $command_name,
            $env,
            $token,
            $api_key_sha256,
            $api_secret_sha256,
            $my_api_key,
            $my_api_secret,
            $service
        ];


        self::varDumpToFile($dataDump);

        if (hash('sha256', $my_api_secret) === $api_secret_sha256 && hash('sha256', $my_api_key) === $api_key_sha256) {
            
            $participant = $this->helper->getParticipantFromRef($ref);

            self::varDumpToFile($participant, "uploads/file2.txt");
           
            $unique_price = $service == 'congres' ? self::getUniquePrice($participant['formule']) : self::getUniquePricePC($participant['formule_pc']);

            $data_participant_for_paiement = [
                'service'       =>  $service,
                'ref'           =>  $participant['ref'],
                'name'          =>  $participant['first_name']." ".$participant['last_name'],
                'phone'         =>  $participant['phone'],
                'status'        =>  $participant['payment_status'],
                'unique_price'  =>  $unique_price,
                'price'         =>  $unique_price*1
            ];

            self::varDumpToFile($data_participant_for_paiement, "uploads/file3.txt");

            if ($type_event == "sale_complete") {
               
                $data_payment = [
                    //'customer_name'         => $_POST['data']['customer']['name'],
                    //'customer_phone'        => $_POST['data']['customer']['phone'],
                    //'customer_email'        => $_POST['data']['customer']['email'],
                    'token'                 => $token,
                    'status'                => "complete",
                    'response_code'         => 200,
                    'response_text'         => "Paiement effectué via Paytech",
                    'amount'                => $item_price,
                    'ticket_number'         => $ticket_number,
                    'receipt_url'           => "",
               ];

               
               $up_payment = Payment::where([['participant_ref', $ref], ['service', $service]])->update($data_payment);
               
               self::varDumpToFile([$data_payment, $up_payment], "uploads/file4.txt");

               try {
                   $participant = Participant::where('ref','=',$ref)->firstOrFail();
                   
                   // Activation du compte du participant
                   $validate = ParticipantController::validateAfterPaytechPayment($this, $participant->id, $token, $service);
                   
                    self::varDumpToFile([$participant, $validate], "uploads/file5.txt");

                   // Envoi de courriels

               } catch (\Throwable $th) {
                    self::varDumpToFile($th->getMessage(), "uploads/file6.txt");
                    die();
                }

            }
            else{
                self::varDumpToFile("Paiement échoué", "uploads/file7.txt");

                die();
               
            }
        }
        else {
            self::varDumpToFile("Hash non conforme", "uploads/file8.txt");
            die();
        }


    }


    public static function getPostParam($name)
    {
        return !empty($_POST[$name]) ? $_POST[$name] : '';
    }

    /** END PAYTECH */
    
    
    public static function varDumpToFile($variable, $filename = "uploads/newfile.txt") {
        ob_start();
        var_dump($variable);
        $dump = ob_get_clean();
        
        $absolutePath = realpath($filename);
        
        if ($absolutePath) {
            file_put_contents($absolutePath, $dump);
            echo "Le fichier a été enregistré à : " . $absolutePath;
        } else {
            echo "Le chemin du fichier est invalide.";
        }
    }
    

    public static function onFile($data, $container)
    {
        try {
            //$se = file_put_contents($container->domain_url."/uploads/newfile.txt", $data, FILE_APPEND | LOCK_EX );
            $se = file_put_contents($container->domain_url."/uploads/newfile.txt", $data, FILE_APPEND );
            echo "<pre>";
            var_dump($se);
            var_dump($container->domain_url."/uploads/newfile.txt");
            echo "</pre>";
        } catch (\Throwable $th) {
            $container->helper->debug($th->getMessage());
            //throw $th;
        }

        //$myfile = fopen($container->domain_url."/uploads/newfile.txt", "w") or die("Unable to open file!");
        //fwrite($myfile, $data."\n");
        //fclose($myfile);
    }



    public static function getUniquePrice($forumule)
    {
        switch ($forumule) {
            case '1':
                return 150000;
                break;

            case '2':
                return 75000;
                break;

            case '3':
                return 50000;
                break;
            
            default:
                return 50000;
                break;
        }

    }


    public static function getUniquePricePC($forumule_pc)
    {
        switch ($forumule_pc) {
            case '1':
                return 60000;
                break;

            case '2':
                return 150000;
                break;

            case '12':
                return 210000;
                break;
            case '21':
                return 210000;
                break;
            
            default:
                return 60000;
                break;
        }

    }





    public function debug($request, $response, $arg)
    {
        # code...
        # code...
        $pwd_bt = "7683";
        $pwd_bt2 = "5629";
        $pwd = $this->helper->genMdp($pwd_bt);
        $pwd2 = $this->helper->genMdp($pwd_bt2);

        $this->helper->debug($pwd);
        $this->helper->debug($pwd2);


        exit;


        $participant_id = 1;
        $ticketNumber = null;
        $num_recu = "test_anwW7Iqpq0";

        $validate = ParticipantController::validateAfterPayment($this, $participant_id, $num_recu);
        //$activate = ParticipantController::generateAndSendNewPassword($this, $participant_id);
        
        $this->helper->debug($validate);
    }













    public function returnOld($request, $response, $arg)
    {
        $token = $_GET['token'];
        $this->helper->debug($token);

        $invoice = new \Paydunya\Checkout\CheckoutInvoice();
        
        $this->helper->debug($invoice->confirm($token));

        if ($invoice->confirm($token)) {

            $ref = $invoice->getCustomerInfo('ref');
            $participant = $this->helper->getParticipantFromRef($ref);

            if (!$participant) {
                return $this->view->render($response->withStatus(404),'errors/404.twig');
            }

            // Le statut du paiement peut être soit completed, pending, cancelled
            $payment_status =  $invoice->getStatus();

            $invoice_url = $invoice->getInvoiceUrl();
          
            $name = $invoice->getCustomerInfo('name');
            $phone = $invoice->getCustomerInfo('phone');
            $email = $invoice->getCustomerInfo('email');

            $data_payment = [
               // 'participant_ref'       => $ref,
                //'participant_id'        => $participant['id'],
                'customer_name'         => $name,
                'customer_phone'        => $phone,
                'customer_email'        => $email,
                'token'                 => $token,
                'status'                => $payment_status,
                'response_code'         => $invoice->response_code,
                'response_text'         => $invoice->response_text,
            ];

            switch ($payment_status) {
                case 'completed':
                    $receipt_url = $invoice->getReceiptUrl();
                   
                    //$name = $invoice->getCustomData("name");
                    //$phone = $invoice->getCustomData("phone");

                    $total_amount = $invoice->getTotalAmount();

                    $data_payment['amount'] = $total_amount;
                    $data_payment['receipt_url'] = $receipt_url;

                    $action_link = $this->router->pathFor('home');
                    $title = "Paiement réussi";
                    $icon = '<i class="fi fi-rr-check"></i></span> ';
                    $text_link = null;
                    $class = "success-not";
                    $message = "Votre paiement a réussi. <br>Un courriel de confirmation vous sera envoyé avec vos paramètres de connexion.";

                    break;
                
                case 'pending':
                    $title = "Paiement en cours";
                    $action_link = $invoice_url;
                    $icon = '<i class="fi fi-rr-check"></i></span> ';
                    $text_link = null;
                    $class = "success-not";
                    $message = 'Votre paiement est en cours de traitement. <br>Si vous n\'êtes pas redirigé(e) dans quelques secondes, veuillez cliquer <a href="#">ici</a>';
                    
                    break;

                case 'cancelled':
                    $action_link = $invoice_url;
                    $icon = '<i class="fi fi-rr-cross"></i> ';
                    $title = "Paiement annulé";
                    $text_link = "Reprendre";
                    $class = "error-not";
                    $message = "Le paiement a été annulé. <br>Si vous rencontrez des soucis, contactez-nous au 77 000 00 00.";
                    
                    break;
                
                default:
                    $action_link = $invoice_url;
                    $text_link = "Réessayer";
                    $icon = '<i class="fi fi-rr-cross"></i> ';
                    $title = "Echec paiement";
                    $class = "error-not";
                    $message = "Le paiement a échoué. <br>Veuillez réessayer ! <br>Si vous rencontrez des soucis, contactez-nous au 77 000 00 00.";
                    
                    break;
            }

            $up_payment = Payment::where('participant_ref', $ref)->update($data_payment);


        }else{

            echo $invoice->getStatus();
            echo $invoice->response_text;
            echo $invoice->response_code;
            
            $participant = $this->helper->getParticipant($this);
            
            if ($participant) {
                $action_link = $this->router->pathFor('paiement_init', $participant['ref']);
                $text_link = "Réessayer";
            }
            else{
                $action_link = $this->router->pathFor('p_login');
                $text_link = "Reprendre";
            }

            $ref = $invoice->getCustomerInfo('ref');
            $participantr = $this->helper->getParticipantFromRef($ref);

            $icon = '<i class="fi fi-rr-cross"></i> ';
            $title = "Paiement annulé";
            $class = "error-not";
            $message = "Le paiement a été annulé. <br>Si vous rencontrez des soucis, contactez-nous au 77 000 00 00.";
        }

        $data = [
            'page_title'  =>    $title,
            'link'        =>    $action_link,
            'text_link'   =>    $text_link,
            'icon'        =>    $icon,
            'class'       =>    $class,
            'message'     =>    $message,
        ];

        return $this->view->render($response, 'paiementInit.twig', compact('participant','data'));
    }
    
}