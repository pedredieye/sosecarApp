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


//require __DIR__ .'/../../vendor/paydunya/paydunya.php';


class PaiementController extends Controller
{

    public static function init($container)
    {

        $domain = $container->domain_url;
        
        \Paydunya\Setup::setMasterKey($container->pd_mainKey);
        \Paydunya\Setup::setPublicKey($container->pd_publicKey);
        \Paydunya\Setup::setPrivateKey($container->pd_privateKey);
        \Paydunya\Setup::setToken($container->pd_token);
        
        \Paydunya\Setup::setMode("live"); // Optionnel en mode test. Utilisez cette option pour les paiements tests.
       
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

    
    public static function initPayment($container, $ref) {
       
        $participant = $container->helper->getParticipantFromRef($ref);

        if (!$participant) {
            return 0;
        }
        

        $unique_price = self::getUniquePrice($participant['formule']);

        $data_payment = [
            'payment_from'          => 'cash',
            'participant_ref'       => $participant['ref'],
            'participant_id'        => $participant['id'],
            'customer_name'         => $participant['first_name']." ".$participant['last_name'],
            'customer_phone'        => $participant['phone'],
            'amount'                => $unique_price,
        ];

        try {
            $the_paiement = Payment::where('participant_ref', $data_payment['participant_ref'])->firstOrFail();
            $up_payment = Payment::where('participant_ref', $data_payment['participant_ref'])->increment('attempts');
        } catch (\Throwable $th) {
            $new_payment = Payment::insertGetId($data_payment);
        }
        
        return ["ref" => $ref, "data" => $data_payment];
    }



    public static function validatePayment($ref, $data, $method = "free")
    {

        $data_payment = [
            'customer_name'         => $data['customer']['name'],
            'customer_phone'        => $data['customer']['phone'],
            'customer_email'        => $data['customer']['email'],
            'token'                 => $data['token'],
            'status'                => $data['status'],
            'response_code'         => $data['response_code'],
            'response_text'         => $data['response_text'],
            'amount'                => $data['total_amount'],
            'receipt_url'           => $data['receipt_url'],
            'ticket_number'         => $data['ticket_number'],
       ];
       
       $up_payment = Payment::where('participant_ref', $ref)->update($data_payment);

       try {
           //$participant = Participant::where('ref','=',$data_payment['ref'])->firstOrFail();
           
           // Activation du compte du participant
           //$validate = ParticipantController::validateAfterPayment($this, $participant->id, $_POST['data']['token']);
           
           // Envoi de courriels

       } catch (\Throwable $th) {
           //throw $th;
       }
            
    }


    public static function onFile($data, $container)
    {
        try {
            $se = file_put_contents($container->domain_url."/uploads/newfile.txt", $data, FILE_APPEND | LOCK_EX );
            echo "<pre>";
            var_dump($se);
            var_dump($container->domain_url."/uploads/newfile.txt");
            echo "</pre>";
        } catch (\Throwable $th) {
            //throw $th;
        }

        $myfile = fopen($container->domain_url."/uploads/newfile.txt", "w") or die("Unable to open file!");
        fwrite($myfile, $data."\n");
        fclose($myfile);
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
        $pwd_bt = "7683";
        $pwd = $this->helper->genMdp($pwd_bt);

        $this->helper->debug($pwd);


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