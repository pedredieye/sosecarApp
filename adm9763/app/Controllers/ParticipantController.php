<?php

namespace App\Controllers;

use App\Helpers\DBIP;
use App\Helpers\Helper;
use App\Helpers\Sms;
use App\Controllers\PaiementController;

use App\Helpers\SandBox;
use App\Helpers\RandomStringGenerator;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

use App\Helpers\Browser;
use App\Models\Country;
use App\Models\State;
use App\Models\Participant;
use App\Models\AteliersParticipant;
use App\Models\PQrCode;
use App\Models\Job;
use App\Models\Title;
use App\Models\Session;
use App\Models\Salle;
use App\Models\Scan;
use App\Models\User;
use App\Models\Attestation;
use App\Models\Payment;
use App\Models\Labo;

use App\Helpers\MailSandBox;

use Bes\Twig\Extension\MobileDetectExtension;
use Psr7Middlewares\Middleware\ClientIp;

use CodeItNow\BarcodeBundle\Utils\QrCode;

//use chillerlan\QRCode\{QRCode, QROptions};

//use chillerlan\QRCode;
//use chillerlan\QROptions;

class ParticipantController extends Controller
{
    public function indexPublic($request, $response, $arg) {

      return $this->view->render($response, 'homePublic.twig', compact('courses'));
    }

    public function oldsave($request, $response, $arg)
    {
      if(isset($_POST)){
        if(empty($_POST['email']) || empty($_POST['phone']))
          return  $response->withStatus(200)->write('3');
      }

      $status = 1;


      if ($_POST['validate_after_saved'] == "on"){
        $validated_by = $this->usr['id'];
        $status = 2;

        $pwd_bt = $this->helper->genPwd();
        $pwd = $this->helper->genMdp($pwd_bt);

        // voir si si c'est pour un visiteur ou si le paiement sera réglé par cash ou en ligne

      }

      $ref = $this->helper->genRef();

      $ticket_number = $this->helper->genTicketNumber();
      $data = [
        'ref'                   =>  $ref,
        'first_name'            =>  $_POST['fname'],
        'last_name'             =>  $_POST['lname'],
        //'year_of_birth'         =>  \date("Y-m-d", strtotime($_POST['year_of_birth']) ),
        'email'                 =>  filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
        'phone'                 =>  str_replace("221","",str_replace("+221","",str_replace("00221","",$_POST['phone']))),
        'gender'                =>  $_POST['gender'],
        'job_id'                =>  $_POST['job'],
        'title_id'              =>  $_POST['title'],
        'country_id'            =>  $_POST['country'],
        'state_id'              =>  $_POST['state'],
        'ticket_number'         =>  $ticket_number,
        'num_recu'              =>  $_POST['recu'],
        'added_by'              =>  $this->usr['id'],
        'validated_by'          =>  $validated_by,

        'status'                =>  $status,
        'password'              =>  $pwd,
        'created_at'            =>  \date("Y-m-d H:i:s"),
        'updated_at'            =>  \date("Y-m-d H:i:s")
      ];

      $em = Participant::where('email','=',$data['email'])->first();
      if($em)// L'utilisateur existe déjà
        return  $response->withStatus(200)->write('2');


      $new_participant = Participant::insertGetId($data);



      if ($new_participant) {


        // Si c'est pour un visiteur


        


        if ($status == 2) {
          $link_for_activate_participant = $this->domain_url."/participant/".$new_participant."/".$ticket_number;

          $file_name = "participant_".$new_participant."_".$ticket_number;
          $qr_code = $this->helper->qrCode($link_for_activate_participant, $file_name, $ticket_number);

          $data_qr_code = [
            'qr_code_link'      =>  $qr_code,
            'ticket_number'     =>  $ticket_number,
            'participant_id'    =>  $new_participant,
            'created_at'        =>  \date("Y-m-d H:i:s"),
            'updated_at'        =>  \date("Y-m-d H:i:s")
          ];

          $new_qr_code = PQrCode::insertGetId($data_qr_code);

          if($new_qr_code){
            $data_email = [
              'qr_code'               => $qr_code,
              'ticket_number'         =>  $ticket_number,
              'first_name'            =>  $data['first_name'],
              'last_name'             =>  $data['last_name'],
              'email'                 =>  $data['email'],
              'phone'                 =>  $data['phone'],
              'gender'                =>  $data['gender'],
              'job'                   =>  $data['job'],
              'title'                 =>  $data['title'],
              'password'              =>  $pwd_bt,
              'link'                  =>  "http://sosecar.sn".$this->participant_login_link,
              'link_login'            =>  "http://sosecar.sn".$this->participant_login_link
            ];

            $s_m = $this->MailSandBox->sendMail($this , $to = $data['email'], $subject = "SOSECAR - Votre inscription a été prise en compte.", $data = $data_email);
            if (intval($s_m) == 1) {
              $up = Participant::where('email','=',$data['email'])->update(['flag_mail_validation' => 1]);
            }
          }

        }





        return  $response->withStatus(200)->write('1');
      }

      return  $response->withStatus(200)->write('3');

    }

    public function save($request, $response, $arg)
    {
      // Vérifier si des données sont reçues
      if(isset($_POST)){
       // if(empty($_POST['email']) || empty($_POST['phone']))
        if(empty($_POST['email']) )
          return  $response->withStatus(200)->write('3');
      }


      // Vérifier si l'inscription est validée
      $status = 1;
      if ($_POST['validate_after_saved'] == "on" or $_POST['if_invited'] == "on" or $_POST['payment_method'] == "cash"){
        $validated_by = $this->usr['id'];
        $status = 2;
        $pwd_bt = $this->helper->genPwd();
        $pwd = $this->helper->genMdp($pwd_bt);
        // voir si si c'est pour un visiteur ou si le paiement sera réglé par cash ou en ligne
      }
      $status = 1;

      $ref = $this->helper->genRef();

      $ticket_number = $this->helper->genTicketNumber();
      
      $data_participant = [
        'ref'                   =>  $ref,
        'first_name'            =>  $_POST['fname'],
        'last_name'             =>  $_POST['lname'],
        //'year_of_birth'         =>  \date("Y-m-d", strtotime($_POST['year_of_birth']) ),
        'email'                 =>  filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
        'phone'                 =>  str_replace("221","",str_replace("+221","",str_replace("00221","",$_POST['phone']))),
        'gender'                =>  $_POST['gender'],
        'job_id'                =>  $_POST['job'],
        'title_id'              =>  $_POST['title'],
        'country_id'            =>  $_POST['country'],
        'state_id'              =>  $_POST['state'],
        'ticket_number'         =>  $ticket_number,
        'num_recu'              =>  $_POST['recu'],
        'added_by'              =>  $this->usr['id'],
        'validated_by'          =>  $validated_by,

        'formule'               =>  $_POST['formule'],
        'payment_status'        =>  "started",
        'password'              =>  null,

        'status'                =>  $status,
        //'password'              =>  $pwd,
        'created_at'            =>  \date("Y-m-d H:i:s"),
        'updated_at'            =>  \date("Y-m-d H:i:s")
      ];

      // On enregistre les informations du participant
        $em = Participant::where('email','=',$data_participant['email'])->first();
        
        // Vérifier si le participant n'existe pas déjà dans la base
        if($em)// L'utilisateur existe déjà
          //return  $response->withStatus(200)->write('2');
          return  $response->withStatus(204)->withJson($data_return);

        $new_participant = Participant::insertGetId($data_participant);
      
      
      // Fin ////On enregistre les informations du participant

      if ($new_participant) {
        $data_return = [
          "ref"     =>  $ref,
          "id"      =>  $new_participant
        ];


        if ($status == 2 or 3==3){
        //if ($status == 2 ){
          $data_v_payment = [
            'customer'    =>  [
              'ref'     =>  $ref,
              'name'    =>  $data_participant['first_name']." ".$data_participant['last_name'],
              'phone'   =>  $data_participant['phone'],
              'email'   =>  $data_participant['email']
            ],
            //'total_amount'    =>  $forumules[$data['formule']],
            'total_amount'    =>  PaiementController::getUniquePrice($data_participant['formule']),
            'receipt_url'     =>  null,
          ];


          // Si c'est pour un visiteur
          if ($_POST['if_invited'] == "on" ) {

            # On enregistre un paiement de 0xof puis envoyer email de validation paiement
            $data_v_payment ['token'] = $data_participant['ref'];
            $data_v_payment ['status'] = "completed";
            $data_v_payment ['response_code'] = 200;
            $data_v_payment ['response_text'] = "Ticket offert";
          
            $send_status_1 = self::generateAndSendNewPassword($this, $new_participant, $mehtod = "free");

            // Initier un paiement 
            $data_paiement = PaiementController::initPayment($this, $data_participant['ref']);

            self::validateAfterPayment($this, $new_participant, $_POST['recu'], $method = "free", 'congres',intval($_POST['labo']));
            
            PaiementController::validatePayment($ref, $data_v_payment);

          }
          else {
            // Le participant va payer

            switch ($_POST['payment_method']) {
              case 'cash':
                // Validation en mentionnant le montant
                $data_v_payment ['token'] = $data_participant['ref'];
                $data_v_payment ['status'] = "completed";
                $data_v_payment ['response_code'] = 200;
                $data_v_payment ['response_text'] = "Paiement via cash effectué";

                $send_status_1 = self::generateAndSendNewPassword($this, $new_participant, $mehtod = "cash");
                /*
                // Initier un paiement 
                $data_paiement = PaiementController::initPayment($this, $data_participant['ref']);
                
                self::validateAfterPayment($this, $new_participant, $_POST['recu'], $method = "offline", 'congres', intval($_POST['labo']));

                PaiementController::validatePayment($ref, $data_v_payment);
                */

                break;

              case 'online':
                // Validation en mentionnant le montant, et envoyer le lien de paiement
                
                $send_status_1 = self::generateAndSendNewPassword($this, $new_participant, $mehtod = "payed");
               
                //$this->helper->debug($data_v_payment);
                
                break;
              
              default:
                # code...
                break;
            }
            
          }

        }


        return  $response->withStatus(200)->write('1');
      }

      //return  $response->withStatus(200)->write('3');
      return  $response->withStatus(500)->withJson($data_return);

    }


    public static function generateAndSendNewPassword($container, $participant_id, $mehtod = "payed")
    {
      $partticipant = Participant::where('id','=',$participant_id)->with('job')->with('title')->first();

      if ($partticipant) {
        $pwd_bt = $container->helper->genPwd();
        $pwd = $container->helper->genMdp($pwd_bt);

        if ($mehtod == "free" || $mehtod == "cash") {
          $upd = Participant::where('id','=',$participant_id)->update([
            "password" => $pwd,
            'payment_method'    =>  null,
          ]);
        }
        else{
          $upd = Participant::where('id','=',$participant_id)->update([
            "password" => $pwd,
          ]);
        }
        
        if ($upd){

            $data_email = [
              'first_name'            =>  $partticipant->first_name,
              'last_name'             =>  $partticipant->last_name,
              'email'                 =>  $partticipant->email,
              'phone'                 =>  $partticipant->phone,
              'gender'                =>  $partticipant->gender,
              'title'                 =>  $partticipant->title->title,
              'pwd'                   =>  $pwd_bt,
              'link'                  =>  "http://sosecar.sn/participant/login",
              'link_paiement'         =>  "http://sosecar.sn/pay/init/".$partticipant->ref,
            ];

            if ($mehtod == "free") {
              $s_m = $container->MailSandBox->sendAccountCreationMailToUserInvited($container , $to = $partticipant->email, $subject = "SOSECAR - Votre inscription a été prise en compte.", $data = $data_email);
            }
            elseif ($mehtod == "cash") {
              $s_m = $container->MailSandBox->sendAccountCreationMailToUserPayedCash($container , $to = $partticipant->email, $subject = "SOSECAR - Votre inscription a été prise en compte.", $data = $data_email);
            }
            else{
              $s_m = $container->MailSandBox->sendAccountCreationMailToUser($container , $to = $partticipant->email, $subject = "SOSECAR - Votre inscription a été prise en compte.", $data = $data_email);
            }
            
            if (intval($s_m) == 1) {
              $up = Participant::where('email','=',$partticipant->email)->update(['flag_mail_validation' => 1]);
            }

            //return  $response->withStatus(200)->write('1');
            return  1;

        }
      }
    }


    public static function validateVisitorRegistration($container, $data_v_payment)
    {

      $partticipant = Participant::where('id','=',$participant_id)->with('job')->with('title')->first();

      if ($partticipant) {
        $ticket_number = $container->helper->genTicketNumber();
        
        $upd = Participant::where('id','=',$participant_id)->update([
          "status" => 2, 
          "ticket_number" => $ticket_number, 
          "payment_status" => "completed", 
          "payment_method" => $method, 
          //'paydunya_by_participant','paydunya_by_secretaire','offline','free'
          "num_recu" => $num_recu
        ]);
        
        if ($upd){
          $link_for_activate_participant = $container->domain_url."/participant/".$partticipant->id."/".$ticket_number;

          $file_name = "participant_".$partticipant->id."_".$ticket_number;
          $text_for_qr = "       ".$ticket_number." \n\n ".$partticipant->title->title." ".$partticipant->first_name." ".$partticipant->last_name;
          $qr_code = $container->helper->qrCode($link_for_activate_participant, $file_name, $text_for_qr);
         
          $data_qr_code = [
            'qr_code_link'      =>  $qr_code,
            'ticket_number'     =>  $ticket_number,
            'participant_id'    =>  $partticipant->id,
            'created_at'        =>  \date("Y-m-d H:i:s"),
            'updated_at'        =>  \date("Y-m-d H:i:s")
          ];

          $new_qr_code = PQrCode::insertGetId($data_qr_code);

          if($new_qr_code){

            $data_email = [
              'qr_code'               =>  $qr_code,
              'ticket_number'         =>  $ticket_number,
              'first_name'            =>  $partticipant->first_name,
              'last_name'             =>  $partticipant->last_name,
              'email'                 =>  $partticipant->email,
              'phone'                 =>  $partticipant->phone,
              'gender'                =>  $partticipant->gender,
              'job'                   =>  $partticipant->job->job_title,
              'title'                 =>  $partticipant->title->title,
              'password'              =>  $pwd_bt,
              'link'                  =>  "http://sosecar.sn".$container->participant_login_link,
              'link_login'            =>  "http://sosecar.sn".$container->participant_login_link
            ];

            $s_m = $container->MailSandBox->sendAccountValidationMailToUser($container , $to = $partticipant->email, $subject = "SOSECAR - Confirmation de paiement pour l'inscription au congrès.", $data = $data_email);
            
            if (intval($s_m) == 1) {
              $up = Participant::where('email','=',$partticipant->email)->update(['flag_mail_validation' => 1]);
            }

            return  1;

          }
        }
      }

      return  3;
    }


    public static function validateAfterPayment($container, $participant_id, $num_recu, $method = "free", $service, $labo = 0)
    {

      $partticipant = Participant::where('id','=',$participant_id)->with('job')->with('title')->first();

      if ($partticipant) {
        $ticket_number = $container->helper->genTicketNumber();
        $participant_status = $partticipant->status;
        $message = "";

        switch ($service) {
          case 'congres':
            $data_to_update = [
              "status" => 2, 
              "ticket_number" => $ticket_number, 
              "payment_status" => "completed", 
              "payment_method" => $method, 
              "num_recu" => $num_recu, 
              //'paydunya_by_participant','paydunya_by_secretaire','offline','free'
              "validated_by"  => $container->usr['id'],
            ];

            $message = "<p>Nous vous confirmons que votre paiement pour l'inscription à la <strong>6ème édition Cardiotech Sénégal</strong> a bien été reçu. <br>Nous vous remercions de votre participation et nous nous réjouissons de vous accueillir au <strong>Radisson Blu de Dakar</strong> du <strong>16 au 18 décembre 2024</strong>.  <br></p>
            <p>Si vous avez des questions ou des inquiétudes, n'hésitez pas à nous contacter.</p>";
            break;
          
          case 'precongres':
            $data_to_update = [
              "status" => 2, 
              "ticket_number_pc" => $ticket_number, 
              "payment_status_pc" => "completed", 
              "payment_method" => $method, 
              "num_recu_pc" => $num_recu, 
              //'paydunya_by_participant','paydunya_by_secretaire','offline','free'
              "validated_by"  => $container->usr['id'],
            ];


          $message = "<p>Nous vous confirmons que votre paiement pour l'inscription aux ateliers du pré-congrès de la SOSECAR a bien été reçu. <br>Nous vous remercions de votre participation et nous nous réjouissons de vous accueillir du <strong>du 16 au 18 décembre 2024</strong>.  <br></p>
          <p>Si vous avez des questions ou des inquiétudes, n'hésitez pas à nous contacter.</p>";

            break;

          default:
            $data_to_update = [
              "status" => 2, 
              "ticket_number" => $ticket_number, 
              "payment_status" => "completed", 
              "payment_method" => $method, 
              "num_recu" => $num_recu, 
              //'paydunya_by_participant','paydunya_by_secretaire','offline','free'
              "validated_by"  => $container->usr['id'],
            ];

            $message = "<p>Nous vous confirmons que votre paiement pour l'inscription à la <strong>6ème congrès international de la SOSECAR</strong> a bien été reçu. <br>Nous vous remercions de votre participation et nous nous réjouissons de vous accueillir au <strong>Radisson Blu de Dakar</strong> du <strong>16 au 18 décembre 2024</strong>.  <br></p>
            <p>Si vous avez des questions ou des inquiétudes, n'hésitez pas à nous contacter.</p>";
            break;
        }

        $data_to_update['from_labo'] = $labo != 0 ? $labo : null;

        $upd = Participant::where('id','=',$participant_id)->update($data_to_update);

        if ($participant_status == 2) {
          // Envoi de l'email pour confirmer le paiement pour l'activité

        }
        else {// Si le compte n'avait pas été validé
          $the_qr_code = PQrCode::where('participant_id',$partticipant->id)->first();

          if (!$the_qr_code || $partticipant->status != 2) {

              
            $link_for_activate_participant = $container->domain_url."/participant/".$partticipant->id."/".$ticket_number;

            $file_name = "participant_".$partticipant->id."_".$ticket_number;
            $text_for_qr = "       ".$ticket_number." \n\n ".$partticipant->title->title." ".$partticipant->first_name." ".$partticipant->last_name;
            $qr_code = $container->helper->qrCodeLite($link_for_activate_participant, $file_name, $text_for_qr);
          
            $data_qr_code = [
              'ref'               =>  $partticipant->ref,
              'qr_code_link'      =>  $qr_code,
              'ticket_number'     =>  $ticket_number,
              'participant_id'    =>  $partticipant->id,
              'created_at'        =>  \date("Y-m-d H:i:s"),
              'updated_at'        =>  \date("Y-m-d H:i:s")
            ];

            $new_qr_code = PQrCode::insertGetId($data_qr_code);
          }
          elseif($new_qr_code) {
            $new_qr_code = $new_qr_code; 
          }

          if($new_qr_code){

              $data_email = [
                'qr_code'               =>  $qr_code,
                'ticket_number'         =>  $ticket_number,
                'first_name'            =>  $partticipant->first_name,
                'last_name'             =>  $partticipant->last_name,
                'email'                 =>  $partticipant->email,
                'phone'                 =>  $partticipant->phone,
                'gender'                =>  $partticipant->gender,
                'job'                   =>  $partticipant->job->job_title,
                'title'                 =>  $partticipant->title->title,
                'message'               =>  $message,
                'password'              =>  $pwd_bt,
                'link'                  =>  "https://sosecar.sn".$container->participant_login_link,
                'link_login'            =>  "https://sosecar.sn".$container->participant_login_link
              ];
  
              $s_m = $container->MailSandBox->sendAccountValidationMailToUser($container , $to = $partticipant->email, $subject = "SOSECAR - Confirmation de paiement pour l'inscription au congrès.", $data = $data_email);
              
              if (intval($s_m) == 1) {
                $up = Participant::where('email','=',$partticipant->email)->update(['flag_mail_validation' => 1]);
              }
            
            return  1;
          }
          
        }
        
        

      }

      return  3;
    }




    public static function validateAfterPaymentOLD($container, $participant_id, $num_recu, $method = "free", $service)
    {

      $partticipant = Participant::where('id','=',$participant_id)->with('job')->with('title')->first();

      if ($partticipant) {
        $ticket_number = $container->helper->genTicketNumber();

        switch ($service) {
          case 'congres':
            # code...
            $data_tu_update = [
              "status" => 2, 
              "ticket_number" => $ticket_number, 
              "payment_status" => "completed", 
              "payment_method" => $method, 
              "num_recu" => $num_recu, 
              //'paydunya_by_participant','paydunya_by_secretaire','offline','free'
              "validated_by"  => $container->usr['id'],
            ];

            break;
          
          case 'precongres':
            # code...
            break;

          default:
            # code...
            break;
        }
        
        $upd = Participant::where('id','=',$participant_id)->update($data_tu_update);
        
        if ($upd){
          $link_for_activate_participant = $container->domain_url."/participant/".$partticipant->id."/".$ticket_number;

          $file_name = "participant_".$partticipant->id."_".$ticket_number;
          $text_for_qr = "       ".$ticket_number." \n\n ".$partticipant->title->title." ".$partticipant->first_name." ".$partticipant->last_name;
          $qr_code = $container->helper->qrCode($link_for_activate_participant, $file_name, $text_for_qr);
         
          $data_qr_code = [
            'qr_code_link'      =>  $qr_code,
            'ticket_number'     =>  $ticket_number,
            'participant_id'    =>  $partticipant->id,
            'created_at'        =>  \date("Y-m-d H:i:s"),
            'updated_at'        =>  \date("Y-m-d H:i:s")
          ];

          $new_qr_code = PQrCode::insertGetId($data_qr_code);

          if($new_qr_code){

            $data_email = [
              'qr_code'               =>  $qr_code,
              'ticket_number'         =>  $ticket_number,
              'first_name'            =>  $partticipant->first_name,
              'last_name'             =>  $partticipant->last_name,
              'email'                 =>  $partticipant->email,
              'phone'                 =>  $partticipant->phone,
              'gender'                =>  $partticipant->gender,
              'job'                   =>  $partticipant->job->job_title,
              'title'                 =>  $partticipant->title->title,
              'password'              =>  $pwd_bt,
              'link'                  =>  "https://sosecar.sn".$container->participant_login_link,
              'link_login'            =>  "https://sosecar.sn".$container->participant_login_link
            ];

            $s_m = $container->MailSandBox->sendAccountValidationMailToUser($container , $to = $partticipant->email, $subject = "SOSECAR - Confirmation de paiement pour l'inscription au congrès.", $data = $data_email);
            
            if (intval($s_m) == 1) {
              $up = Participant::where('email','=',$partticipant->email)->update(['flag_mail_validation' => 1]);
            }

            return  1;

          }
        }
      }

      return  3;
    }

   
    public function new($request, $response, $arg)
    {
      if(!$this->helper->checkConnexion())
        return $response->withRedirect($this->router->pathFor('login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

      if(!$this->helper->checkPrivillieges($this->usr['role_id'], [1,2,3])){
        $data = [
          'authorized'   => false,
        ];
        return $this->view->render($response, 'allParticipant.twig', compact('data'));
      }

      $countries = Country::all();
      $states = State::where('country_id','=',195)->get();
      $jobs = Job::all();
      $titles = Title::all();

      $data = [
        'authorized'   => true,
        'countries'    => $countries,
        'states'       => $states,
        'jobs'        => $jobs,
        'titles'      => $titles,
      ];

      return $this->view->render($response, 'newParticipant.twig', compact('data'));
    }

    public function validateOld($request, $response, $arg)
    {
      if(isset($arg)){
        if(empty($arg['id']) || empty($arg['ticketNumber']))
          return  $response->withStatus(200)->write('3');
      }

      $partticipant = Participant::where('id','=',$arg['id'])->with('job')->with('title')->first();

      if ($partticipant) {
        $ticket_number = $this->helper->genTicketNumber();
        $pwd_bt = $this->helper->genPwd();
        $pwd = $this->helper->genMdp($pwd_bt);

        $upd = Participant::where('id','=',$arg['id'])->update([
          "validated_by" => $this->usr['id'], 
          "status" => 2, 
          "payment_status" => "completed", 
           "payment_method" => "offline", 
          //"payment_method" => "paydunya_by_secretaire", 
          //'paydunya_by_participant','paydunya_by_secretaire','offline','free'
          "ticket_number" => $ticket_number, 
          "formule" => $_POST['formule'], 
          "num_recu" => $_POST['num_recu'], 
          "password" => $pwd
        ]);

        
        if ($upd){
          $link_for_activate_participant = $this->domain_url."/participant/".$partticipant->id."/".$ticket_number;

          $file_name = "participant_".$partticipant->id."_".$ticket_number;
          //$text_for_qr = $ticket_number." - ".$partticipant->title->title." ".$partticipant->first_name." ".$partticipant->last_name;
          $text_for_qr = "       ".$ticket_number." \n\n ".$partticipant->title->title." ".$partticipant->first_name." ".$partticipant->last_name;
          $qr_code = $this->helper->qrCode($link_for_activate_participant, $file_name, $text_for_qr);
          //$this->helper->debug($link_for_activate_participant);
          //$this->helper->debug($file_name);
          //$this->helper->debug($qr_code);

          $data_qr_code = [
            'qr_code_link'      =>  $qr_code,
            'ticket_number'     =>  $ticket_number,
            'participant_id'    =>  $partticipant->id,
            'created_at'        =>  \date("Y-m-d H:i:s"),
            'updated_at'        =>  \date("Y-m-d H:i:s")
          ];

          $new_qr_code = PQrCode::insertGetId($data_qr_code);

          if($new_qr_code){


            $data_email = [
              'qr_code'               =>  $qr_code,
              'ticket_number'         =>  $ticket_number,
              'first_name'            =>  $partticipant->first_name,
              'last_name'             =>  $partticipant->last_name,
              'email'                 =>  $partticipant->email,
              'phone'                 =>  $partticipant->phone,
              'gender'                =>  $partticipant->gender,
              'job'                   =>  $partticipant->job->job_title,
              'title'                 =>  $partticipant->title->title,
              'password'              =>  $pwd_bt,
              'link'                  =>  "https://sosecar.sn".$this->participant_login_link
            ];

            $s_m = $this->MailSandBox->sendAccountValidationMailToUser($this , $to = $partticipant->email, $subject = "SOSECAR - Votre inscription a été validée.", $data = $data_email);
            if (intval($s_m) == 1) {
              $up = Participant::where('email','=',$partticipant->email)->update(['flag_mail_validation' => 1]);
            }

            return  $response->withStatus(200)->write('1');

          }
        }
      }

      return  $response->withStatus(200)->write('3');
    }

    
    public function validate($request, $response, $arg)
    {
      if(isset($arg)){
        if(empty($arg['id']) || empty($arg['ticketNumber']))
          return  $response->withStatus(200)->write('3');
      }

      $partticipant = Participant::where('id','=',$arg['id'])->with('job')->with('title')->first();

      $validated_by = $this->usr['id'];
      $status = 2;


      $data_participant = [
        'num_recu'              =>  $_POST['num_recu'],
        'validated_by'          =>  $validated_by,
        'formule'               =>  $_POST['formule'],
        'payment_status'        =>  "completed",
        'status'                =>  $status
      ];

      if ($partticipant) {

        $data_v_payment = [
          'customer'    =>  [
            'ref'     =>  $partticipant->ref,
            'name'    =>  $partticipant->first_name." ".$partticipant->last_name,
            'phone'   =>  $partticipant->phone,
            'email'   =>  $partticipant->email
          ],
          'total_amount'    =>  PaiementController::getUniquePrice($_POST['formule']),
          'receipt_url'     =>  null,
          'ticket_number'   =>  $partticipant->ticket_number,
        ];

        // Si c'est pour un visiteur
        if ($_POST['if_invited'] == "true"  ) {
           // On enregistre un paiement de 0xof puis envoyer email de validation paiement
           $data_v_payment ['token'] = $partticipant->ref;
           $data_v_payment ['status'] = "completed";
           $data_v_payment ['response_code'] = 200;
           $data_v_payment ['response_text'] = "Ticket offert";
           $method = "free";
         
        }
        else {
          // Le participant va payer

            // Validation en mentionnant le montant
            $data_v_payment ['token'] = $partticipant->ref;
            $data_v_payment ['status'] = "completed";
            $data_v_payment ['response_code'] = 200;
            $data_v_payment ['response_text'] = "Paiement via cash effectué";
            $method = "offline";
        }

        // Valider l'inscription : qr code et mail
        self::validateAfterPayment($this, $partticipant->id, $_POST['num_recu'], $method, 'congres', intval($_POST['labo']) );

        // Valider le paeiement
        PaiementController::validatePayment($partticipant->ref, $data_v_payment);

        return  $response->withStatus(200)->write('1');
      }

      return  $response->withStatus(200)->write('3');

    }

    public function undoValidate($request, $response, $arg)
    {
      // code...
    }

     public function validatePreCongre($request, $response, $arg)
    {
      if(isset($arg)){
        if(empty($arg['id']) || empty($arg['ticketNumber']))
          return  $response->withStatus(200)->write('3');
      }

      $partticipant = Participant::where('id','=',$arg['id'])->with('job')->with('title')->first();

      $validated_by = $this->usr['id'];
      $status = 2;

      $formule_pc = "";

      if (isset($_POST['activite1'])) {
          $activite1 = $_POST['activite1'];
          if($activite1 == "checked")
            $formule_pc .= "1";
      }
      
      if (isset($_POST['activite2'])) {
          $activite2 = $_POST['activite2'];
          if($activite2 == "checked")
            $formule_pc .= "2";
      }

      $data_participant = [
        'num_recu'              =>  $_POST['num_recu'],
        'validated_by'          =>  $validated_by,
        'formule_pc'            =>  $formule_pc,
        'payment_status'        =>  "completed",
        'status'                =>  $status
      ];


      if ($partticipant) {

        $data_v_payment = [
          'customer'    =>  [
            'ref'     =>  $partticipant->ref,
            'name'    =>  $partticipant->first_name." ".$partticipant->last_name,
            'phone'   =>  $partticipant->phone,
            'email'   =>  $partticipant->email
          ],
          'total_amount'    =>  PaiementController::getUniquePricePC($formule_pc),
          'receipt_url'     =>  null,
          'ticket_number'   =>  $partticipant->ticket_number_pc,
        ];

        // Si c'est pour un visiteur
        if ($_POST['if_invited'] == "true"  ) {
           // On enregistre un paiement de 0xof puis envoyer email de validation paiement
           $data_v_payment ['token'] = $partticipant->ref;
           $data_v_payment ['status'] = "completed";
           $data_v_payment ['response_code'] = 200;
           $data_v_payment ['response_text'] = "Ticket offert";
           $method = "free";
         
        }
        else {
          // Le participant va payer

            // Validation en mentionnant le montant
            $data_v_payment ['token'] = $partticipant->ref;
            $data_v_payment ['status'] = "completed";
            $data_v_payment ['response_code'] = 200;
            $data_v_payment ['response_text'] = "Paiement via cash effectué";
            $method = "offline";
        }

        // Valider l'inscription : qr code et mail
        self::validateAfterPayment($this, $partticipant->id, $_POST['num_recu'], $method , 'precongres', intval($_POST['labo']));

        // Valider le paeiement
        PaiementController::validatePayment($partticipant->ref, $data_v_payment);

        return  $response->withStatus(200)->write('1');
      }

      return  $response->withStatus(200)->write('3');

    }


    public function list($request, $response, $arg)
    {
      if(!$this->helper->checkConnexion())
        return $response->withRedirect($this->router->pathFor('login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

      if(!$this->helper->checkPrivillieges($this->usr['role_id'], [1,2,3,6])){
        $data = [
          'authorized'   => false,
        ];
        return $this->view->render($response, 'allParticipant.twig', compact('data'));
      }

      $partticipants = Participant::where([['added_by','=',$this->usr['id']],['status','!=', 3],['ticket_number','!=', null]])->with('qrcode')->with('country')->orderBy('id','DESC')->get();

      if ($this->usr['role_id'] <= 3 or $this->usr['role_id'] == 6 )
        $partticipants = Participant::where([['status','!=', 3],['ticket_number','!=', null]])->orwhere([['status','!=', 3],['ticket_number_pc','!=', null]])->with('qrcode')->with('country')->with('etat')->with('state')->with('addedby')->with('validatedby')->orderBy('id','DESC')->get();
        //$partticipants = Participant::where([['status','!=', 3],['ticket_number','!=', null]])->with('qrcode')->with('country')->with('etat')->with('state')->with('addedby')->with('validatedby')->get();


      $forumules = [
        1 => 150000,
        2 => 75000,
        3 => 50000,
        4 => 60000,
      ];

      $forumulespc = [
        "1" => 60000,
        "2" => 150000,
        "12" => 210000,
        "21" => 210000
      ];

      

      $data = [
        'authorized'     => true,
        'participants'   => $partticipants,
        'formules'       => $forumules ,
        'forumulespc'    => $forumulespc 
      ];

      $deb = 0;
      if (isset($_GET['a']))
        $deb = 1;

      return $this->view->render($response, 'allParticipant.twig', compact('data', 'deb'));
    }


    public function listForAtelier($request, $response, $arg)
    {
      if(!$this->helper->checkConnexion())
        return $response->withRedirect($this->router->pathFor('login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

      if(!$this->helper->checkPrivillieges($this->usr['role_id'], [1,2,3,6])){
        $data = [
          'authorized'   => false,
        ];
        return $this->view->render($response, 'allParticipant.twig', compact('data'));
      }

      $participants = AteliersParticipant::orderBy('id','DESC')->get();

     
      $activities = [
        1 => "Déformations myocardiques.",
        2 => "ATELIER SYNDROME D’APNÉE OBSTRUCTIVE DU SOMMEIL",
        3 => "Echo 3D Transthoracique et Transoesophagienne",
        4 => "Asynchronisme Cardiaque",
        5 => "Deformations Myocardiques : Strain Ventricule Gauche, Strain Oreillette Gauche",
      ];

      

      $data = [
        'authorized'     => true,
        'participants'   => $participants,
        'activities'     => $activities 
      ];

      $deb = 0;
      if (isset($_GET['a']))
        $deb = 1;

     

      return $this->view->render($response, 'allParticipantAtelier.twig', compact('data', 'deb'));
    }

    public function InitValidation($request, $response, $arg)
    {
      if(!$this->helper->checkConnexion())
        return $response->withRedirect($this->router->pathFor('login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

      if(!$this->helper->checkPrivillieges($this->usr['role_id'], [1,2,3])){
        $data = [
          'authorized'   => false,
        ];
        return $this->view->render($response, 'validateParticipant.twig', compact('data'));
      }

      $partticipant = Participant::where('id',"=",$arg['id'])->with('title')->first();
      $labos = Labo::where('status', 1)->get();

      if ($partticipant) {
        $data = [
          'participant'     => $partticipant,
          'labos'           => $labos,
          'authorized'      => true,
        ];
      }

      return $this->view->render($response, 'validateParticipant.twig', compact('data'));
    }

    public function InitValidationPreCongres($request, $response, $arg)
    {
      if(!$this->helper->checkConnexion())
        return $response->withRedirect($this->router->pathFor('login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

      if(!$this->helper->checkPrivillieges($this->usr['role_id'], [1,2,3])){
        $data = [
          'authorized'   => false,
        ];
        return $this->view->render($response, 'validatePreCongres.twig', compact('data'));
      }

      $partticipant = Participant::where('id',"=",$arg['id'])->with('title')->first();
      $labos = Labo::where('status', 1)->get();
      
      if ($partticipant) {
        $data = [
          'participant'     => $partticipant,
          'labos'           => $labos,
          'authorized'      => true,
        ];
      }

      return $this->view->render($response, 'validatePreCongres.twig', compact('data'));
    }



    public function InitCancel($request, $response, $arg)
    {
      if(!$this->helper->checkConnexion())
        return $response->withRedirect($this->router->pathFor('login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

      if(!$this->helper->checkPrivillieges($this->usr['role_id'], [1,2,3])){
        $data = [
          'authorized'   => false,
        ];
        return $this->view->render($response, 'validateParticipant.twig', compact('data'));
      }

      $partticipant = Participant::where('id',"=",$arg['id'])->with('title')->first();
      if ($partticipant) {
        $data = [
          'participant'     => $partticipant,
          'authorized'      => true,
        ];
      }

      return $this->view->render($response, 'validateParticipant.twig', compact('data'));
    }
    
    public function InitCancelPreCongres($request, $response, $arg)
    {
      if(!$this->helper->checkConnexion())
        return $response->withRedirect($this->router->pathFor('login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

      if(!$this->helper->checkPrivillieges($this->usr['role_id'], [1,2,3])){
        $data = [
          'authorized'   => false,
        ];
        return $this->view->render($response, 'validateParticipant.twig', compact('data'));
      }

      $partticipant = Participant::where('id',"=",$arg['id'])->with('title')->first();
      if ($partticipant) {
        $data = [
          'participant'     => $partticipant,
          'authorized'      => true,
        ];
      }

      return $this->view->render($response, 'validateParticipant.twig', compact('data'));
    }

    

    public function show($request, $response, $arg)
    {

        if(!$this->helper->checkConnexion())
          return $response->withRedirect($this->router->pathFor('login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

        if(!$this->helper->checkPrivillieges($this->usr['role_id'], [1,2,3,6])){
          $data = [
            'authorized'   => false,
          ];
          return $this->view->render($response, 'showParticipant.twig', compact('data'));
        }


        return $this->view->render($response, 'showParticipant.twig', compact('data'));

    }

    public function listPending($request, $response, $arg)
    {
      if(!$this->helper->checkConnexion())
        return $response->withRedirect($this->router->pathFor('login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

      if(!$this->helper->checkPrivillieges($this->usr['role_id'], [1,2,3,6])){
        $data = [
          'authorized'   => false,
        ];
        return $this->view->render($response, 'pendingParticipant.twig', compact('data'));
      }

      $partticipants = Participant::where([['added_by','=',$this->usr['id']],['status','=',1],['ticket_number','!=', null]])->with('etat')->with('qrcode')->with('country')->with('state')->get();

      if ($this->usr['role_id'] <= 2)
        $partticipants = Participant::where([['status','=',1],['ticket_number','!=', null]])->with('qrcode')->with('etat')->with('country')->with('state')->with('addedby')->with('validatedby')->get();


      $forumules = [
        1 => 150000,
        2 => 75000,
        3 => 50000
      ];


      $data = [
        'authorized'     => true,
        'participants'   => $partticipants,
        'formules'       => $forumules 
      ];


      return $this->view->render($response, 'pendingParticipant.twig', compact('data'));

    }

    public function listValidated($request, $response, $arg)
    {

      if(!$this->helper->checkConnexion())
        return $response->withRedirect($this->router->pathFor('login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

      if(!$this->helper->checkPrivillieges($this->usr['role_id'], [1,2,3,6])){
        $data = [
          'authorized'   => false,
        ];
        return $this->view->render($response, 'validatedParticipant.twig', compact('data'));
      }

      $partticipants = Participant::where([['added_by','=',$this->usr['id']],['status','=',2],['ticket_number','!=', null]])->with('etat')->with('qrcode')->with('country')->with('state')->get();
      if ($this->usr['role_id'] <= 2)
        $partticipants = Participant::where([['status','=',2],['ticket_number','!=', null]])->with('qrcode')->with('etat')->with('country')->with('state')->with('addedby')->with('validatedby')->get();

      $data = [
        'authorized'     => true,
        'participants'   => $partticipants
      ];


      return $this->view->render($response, 'validatedParticipant.twig', compact('data'));
    }

    public function listDeleted($request, $response, $arg)
    {

      if(!$this->helper->checkConnexion())
        return $response->withRedirect($this->router->pathFor('login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

      if(!$this->helper->checkPrivillieges($this->usr['role_id'], [1,2,3,6])){
        $data = [
          'authorized'   => false,
        ];
        return $this->view->render($response, 'validatedParticipant.twig', compact('data'));
      }

      $partticipants = Participant::where([['added_by','=',$this->usr['id']],['status','=',3],['ticket_number','!=', null]])->with('etat')->with('qrcode')->with('country')->with('state')->get();
      if ($this->usr['role_id'] <= 2)
        $partticipants = Participant::where([['status','=',3],['ticket_number','!=', null]])->with('qrcode')->with('etat')->with('country')->with('state')->with('addedby')->with('validatedby')->get();

      $data = [
        'authorized'     => true,
        'participants'   => $partticipants
      ];


      return $this->view->render($response, 'deletedParticipant.twig', compact('data'));
    }

    public function edit($request, $response, $arg)
    {
      if(!$this->helper->checkConnexion())
        return $response->withRedirect($this->router->pathFor('login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

      if(!$this->helper->checkPrivillieges($this->usr['role_id'], [1,2,3])){
        $data = [
          'authorized'   => false,
        ];
        return $this->view->render($response, 'allParticipant.twig', compact('data'));
      }

      try {
        $participant = Participant::where('id','=',$arg['id'])->firstOrFail();
      } catch (\Exception $e) {

        return $this->view->render($response->withStatus(404), 'errors/404.twig', compact('data'));
      }


      $countries = Country::all();
      $states = State::all();
      $jobs = Job::all();
      $titles = Title::all();

      $data = [
        'authorized'  => true,
        'countries'   => $countries,
        'states'      => $states,
        'jobs'        => $jobs,
        'titles'      => $titles,
        'participant' => $participant,
      ];

      return $this->view->render($response, 'editParticipant.twig', compact('data'));
    }

    public function saveEdit($request, $response, $arg)
    {
      if(isset($_POST)){
        if(empty($_POST['email']) || empty($_POST['phone']))
          return  $response->withStatus(200)->write('3');
      }
      $id_participant = $_POST['idparticipant'];
      $status = 1;
      $pwd = $_POST['password'];
      if ($_POST['validated_by'] == "")
        $validated_by = NULL;
      else
        $validated_by = $_POST['validated_by'];

      if ($_POST['validate_after_saved'] == "on"){
        $validated_by = $this->usr['id'];
        $status = 2;

        $pwd_bt = $this->helper->genPwd();
        $pwd = $this->helper->genMdp($pwd_bt);
      }

      $data = [
        'first_name'            =>  $_POST['fname'],
        'last_name'             =>  $_POST['lname'],
        //'year_of_birth'         =>  \date("Y-m-d", strtotime($_POST['year_of_birth']) ),
        'email'                 =>  filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
        'phone'                 =>  str_replace("221","",str_replace("+221","",str_replace("00221","",$_POST['phone']))),
        'gender'                =>  $_POST['gender'],
        'job_id'                =>  $_POST['job'],
        'title_id'              =>  $_POST['title'],
        'country_id'            =>  $_POST['country'],
        'state_id'              =>  $_POST['state'],
        'num_recu'              =>  $_POST['recu'],
        'validated_by'          =>  $validated_by,
        'status'                =>  $status,
        'password'              =>  $pwd,
      ];

      if ($_POST['actual_status'] == 2 && $status == 1)
        $data['flag_mail_validation'] = 0;


      $update = Participant::where('id','=',$id_participant)->update($data);

      if ($update) {
        return  $response->withStatus(200)->write('1');
      }

      
      if ($update && false) {
        if ($status == 2) {
          $ticket_number = $_POST['ticket_number'];
          $link_for_activate_participant = $this->domain_url."/participant/".$id_participant."/".$ticket_number;

          $file_name = "participant_".$id_participant."_".$ticket_number;
          $qr_code = $this->helper->qrCode($link_for_activate_participant, $file_name, $ticket_number);

          $data_qr_code = [
            'qr_code_link'      =>  $qr_code,
            'ticket_number'     =>  $ticket_number,
            'participant_id'    =>  $id_participant,
            'created_at'        =>  \date("Y-m-d H:i:s"),
            'updated_at'        =>  \date("Y-m-d H:i:s")
          ];



          $new_qr_code = PQrCode::insertGetId($data_qr_code);

          if($new_qr_code){
            $data_email = [
              'qr_code'               => $qr_code,
              'ticket_number'         =>  $ticket_number,
              'first_name'            =>  $data['first_name'],
              'last_name'             =>  $data['last_name'],
              'email'                 =>  $data['email'],
              'phone'                 =>  $data['phone'],
              'gender'                =>  $data['gender'],
              'job'                   =>  $data['job'],
              'title'                 =>  $data['title'],
              'password'              =>  $pwd_bt,
              'link'                  =>  "http://sosecar.sn".$this->participant_login_link
            ];

            if ($_POST['actual_status'] == 1)
              $s_m = $this->MailSandBox->sendMail($this , $to = $data['email'], $subject = "SOSECAR - Votre inscription a été prise en compte.", $data = $data_email);



            if (intval($s_m) == 1 or true) {
              $up = Participant::where('email','=',$data['email'])->update(['flag_mail_validation' => 1]);
            }
          }

        }
        return  $response->withStatus(200)->write('1');
      }

      return  $response->withStatus(200)->write('3');

    }

    public function del($request, $response, $arg)
    {
      if(!$this->helper->checkConnexion())
        return $response->withRedirect($this->router->pathFor('login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

      if(!$this->helper->checkPrivillieges($this->usr['role_id'], [1,2,3])){
        $data = [
          'authorized'   => false,
        ];
        return $this->view->render($response, 'allParticipant.twig', compact('data'));
      }

      try {
        $participant = Participant::where('id','=',$arg['id'])->with('job')->with('title')->with('country')->firstOrFail();
      } catch (\Exception $e) {

        return $this->view->render($response->withStatus(404), 'errors/404.twig', compact('data'));
      }

      $data = [
        'authorized'  => true,
        'participant' => $participant,
      ];

      return $this->view->render($response, 'delParticipant.twig', compact('data'));
    }

    public function saveDelete($request, $response, $arg)
    {
        try {
          $participant = Participant::where('id','=',$arg['id'])->update(['status' => 3]);
        } catch (\Exception $e) {
          return  $response->withStatus(200)->write('3');
        }

        return  $response->withStatus(200)->write('1');

    }

    public function undoDelete($request, $response, $arg)
    {
        try {
          $participant = Participant::where('id','=',$arg['id'])->update(['status' => 1]);
        } catch (\Exception $e) {
          return  $response->withStatus(200)->write('3');
        }

        return  $response->withStatus(200)->write('1');

    }


    public function reSendEmail($request, $response, $arg)
    {
        //$participants = Participant::where([['status','=',2],['flag_mail_validation','=',0]])->with('job')->with('title')->get();
        $participant = Participant::where([['id','=',$arg['id']],['status','=',2],['flag_mail_validation','=',0]])->with('job')->with('title')->first();

        if($participant) {

            if ($participant->ticket_number) {
              $ticket_number = $participant->ticket_number;
            }
            else
              $ticket_number = $this->helper->genTicketNumber();

            $pwd_bt = $this->helper->genPwd();
            $pwd = $this->helper->genMdp($pwd_bt);
            //$upd = Participant::where('id','=',$participant->id)->update(["validated_by" => $this->usr['id'], "status" => 2, "ticket_number" => $ticket_number, "num_recu" => $participant->num_recu, "password" => $pwd]);

            $upd = Participant::where('id','=',$participant->id)->update(["validated_by" => $this->usr['id'], "ticket_number" => $ticket_number, "num_recu" => $participant->num_recu, "password" => $pwd]);

            if ($upd){
              $link_for_activate_participant = $this->domain_url."/participant/".$participant->id."/".$ticket_number;

              $file_name = "participant_".$participant->id."_".$ticket_number;
              $text_for_qr = $ticket_number." - ".$participant->title->title." ".$participant->first_name." ".$participant->last_name;
              $qr_code = $this->helper->qrCode($link_for_activate_participant, $file_name, $text_for_qr);

              $data_qr_code = [
                'qr_code_link'      =>  $qr_code,
                'ticket_number'     =>  $ticket_number,
                'participant_id'    =>  $participant->id,
                'created_at'        =>  \date("Y-m-d H:i:s"),
                'updated_at'        =>  \date("Y-m-d H:i:s")
              ];

              $new_qr_code = PQrCode::insertGetId($data_qr_code);
              //$new_qr_code = true;

              if($new_qr_code){
                $data_email = [
                  'qr_code'               =>  $qr_code,
                  'ticket_number'         =>  $ticket_number,
                  'first_name'            =>  $participant->first_name,
                  'last_name'             =>  $participant->last_name,
                  'email'                 =>  $participant->email,
                  'phone'                 =>  $participant->phone,
                  'gender'                =>  $participant->gender,
                  'job'                   =>  $participant->job->job_title,
                  'title'                 =>  $participant->title->title,
                  'password'              =>  $pwd_bt,
                  'link'                  =>  "http://sosecar.sn".$this->participant_login_link
                ];

                $s_m = $this->MailSandBox->sendMail($this , $to = $participant->email, $subject = "SOSECAR - Votre inscription a été prise en compte.", $data = $data_email);

                if (intval($s_m) == 1) {
                  $up = Participant::where('email','=',$participant->email)->update(['flag_mail_validation' => 1]);
                  return  $response->withStatus(200)->write('1');
                }
                else {
                  return  $response->withStatus(200)->write('3');
                }

              }
            }

        }
        return  $response->withStatus(200)->write('2');
    }



    public function reSendEmailForPreAttendees($request, $response, $arg)
    {
        //$participants = Participant::where([['status','=',2],['flag_mail_validation','=',0]])->with('job')->with('title')->get();
        
        $participants = Participant::where([['status','=',2], ['flag_mail_precongres','=',NULL], ['payment_status_pc','=','completed']])->with('job')->with('title')->get();

        $this->helper->debug("Email à envoyer : ".count($participants));

        foreach ($participants as $participant) {

            $this->helper->debug("Mise à jour pour : ".$participant->first_name." ".$participant->last_name);

            /*
            if ($participant->ticket_number_pc) {
              $ticket_number = $participant->ticket_number_pc;
            }
            else
              $ticket_number = $this->helper->genTicketNumber();
            */

            //$pwd_bt = $this->helper->genPwd();
            //$pwd = $this->helper->genMdp($pwd_bt);

            $upd = true;

            if ($upd){
              
              $link_for_activate_participant = $this->domain_url."/participant/".$participant->id."/".$ticket_number;
  
              //      $file_name = "participant_".$participant->id."_".$ticket_number;
              //      $text_for_qr = $ticket_number." - ".$participant->title->title." ".$participant->first_name." ".$participant->last_name;
              //$qr_code = $this->helper->qrCode($link_for_activate_participant, $file_name, $text_for_qr);
              $this->helper->debug("Filename :".$file_name);
              $this->helper->debug("Link QrCode :".$link_for_activate_participant);
              $this->helper->debug("Texte du QrCode :".$text_for_qr);
              $this->helper->debug("Génération du QrCode :".$qr_code);
              $this->helper->debug("Validateur :".$this->usr['id']);
  
  
              $data_qr_code = [
                //'qr_code_link'      =>  $qr_code,
                'ticket_number'     =>  $ticket_number,
                'participant_id'    =>  $participant->id,
                'created_at'        =>  \date("Y-m-d H:i:s"),
                'updated_at'        =>  \date("Y-m-d H:i:s")
              ];
  
              //$new_qr_code = PQrCode::insertGetId($data_qr_code);
              $new_qr_code = true;
  
              //if($new_qr_code){
                $data_email = [
                  //'qr_code'               =>  $qr_code,
                  'ticket_number_pc'      =>  $ticket_number,
                  'first_name'            =>  $participant->first_name,
                  'last_name'             =>  $participant->last_name,
                  'email'                 =>  $participant->email,
                  'phone'                 =>  $participant->phone,
                  'gender'                =>  $participant->gender,
                  'job'                   =>  $participant->job->job_title,
                  'title'                 =>  $participant->title->title,
                  'link'                  =>  "http://sosecar.com".$this->participant_login_link
                ];
  
                $s_m = $this->MailSandBox->sendMailBackPC($this , $to = $participant->email, $subject = "SOSECAR - Démarrage des activités du précongrès", $data = $data_email);
                //$s_m = $this->MailSandBox->sendMailBackPC($this , $to = "pedredieye@gmail.com", $subject = "SOSECAR - Démarrage des activités du précongrès", $data = $data_email);
                
                $this->helper->debug("Email :".$data_email);
                $this->helper->debug("Email :".$participant->email);
                $this->helper->debug("Envoi email :".$s_m);
  
                if (intval($s_m) == 1) {
                  $up = Participant::where('email','=',$participant->email)->update(['flag_mail_precongres' => 1]);
                }
                
                echo "<br><br><br>";
  
  
              //}
          }
  
        }
        return  $response->withStatus(200)->write('1');
  
    }

    public function sendEmailFollowUp($request, $response, $arg)
    {
        //$participants = Participant::where([['status','=',2],['flag_mail_validation','=',0]])->with('job')->with('title')->get();
        
        $participants = Participant::where([['status','=',2], ['flag_mail_followp','=',0], ['payment_status','=','completed']])->with('job')->with('title')->get();

        $this->helper->debug("Emails à envoyer : ".count($participants));
        echo "<br><br><br>";

        $nn = 0;

        foreach ($participants as $participant) {
            $this->helper->debug("Envoi pour : ".$participant->first_name." ".$participant->last_name);
            $link_for_certificate = "https://bit.ly/sosecar-certificat-6";
            $link_for_book = "https://bit.ly/sosecar-book-6";

            $data_email = [
              //'qr_code'               =>  $qr_code,
              'first_name'            =>  $participant->first_name,
              'last_name'             =>  $participant->last_name,
              'email'                 =>  $participant->email,
              'phone'                 =>  $participant->phone,
              'gender'                =>  $participant->gender,
              'job'                   =>  $participant->job->job_title,
              'title'                 =>  $participant->title->title,
              'link_for_certificate'  =>  $link_for_certificate,
              'link_for_book'         =>  $link_for_book
            ];

            $s_m = null;
            $s_m = $this->MailSandBox->sendMailFollow($this , $to = $participant->email, $subject = "Le book du congrès est disponible - SOSECAR", $data = $data_email);
            
           // if(++$nn == 1)
             // $s_m = $this->MailSandBox->sendMailFollow($this , $to = "pedredieye@gmail.com", $subject = "Le book du congrès est disponible - SOSECAR", $data = $data_email);
            
            $this->helper->debug("Email : ".$data_email['email']);
            $this->helper->debug("Envoi email :".$s_m);

            if (intval($s_m) == 1) {
              $up = Participant::where('email','=',$participant->email)->update(['flag_mail_followp' => 1]);
            }
            echo "<br><br><br>";
        }

        return  $response->withStatus(200)->write('1');
    }


    public function newScan($request, $response, $arg)
    {

      if(!$this->helper->checkConnexion())
        return $response->withRedirect($this->router->pathFor('login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

      if(!$this->helper->checkPrivillieges($this->usr['role_id'], [1,2,3,4])){
        $data = [
          'authorized'   => false,
        ];
        return $this->view->render($response, 'allParticipant.twig', compact('data'));
      }

      $participant = Participant::where('id','=',$arg['id'])->with('title')->with('job')->with('country')->first();

      //$sessions = Session::where('start','>=',date('Y-m-d H:i:s'))->with('salles')->orderBy('start','DESC')->get();

      //$this->helper->debug($sessions->toArray());

      $data = [
        'participant'     => $participant,
        'sessions'        => $sessions,
        'authorized'      => true,
      ];

      return $this->view->render($response, 'scanParticipant.twig', compact('data'));
    }

    public function validateScan($request, $response, $arg)
    {

      try {
        $participant = Participant::where('id','=',$arg['id'])->firstOrFail();

        try {
          $session = Session::where('id','=',$arg['session'])->firstOrFail();

          try {

            $scan = Scan::where([['session_id','=',$arg['session']], ['participant_id','=',$arg['id']]])->firstOrFail();
            return  $response->withStatus(200)->write('4');
            // Le scan a été déjà effectué !

          } catch (\Exception $e) {

            $data_scan = [
              'user_id'           =>  $this->usr['id'],
              'session_id'        =>  $session->id,
              'participant_id'    =>  $participant->id,
            ];

            $new_scan = Scan::insertGetId($data_scan);

            if($new_scan){
              

                //Sms::$client_id = 'ouiAAgIG6wUOgac7AfErsEaD3Lgmc7KD';
                //Sms::$client_secret =  'hnIhwENmtCkIr2Hk';
              //$token = Sms::getTokensFromApi();

              if (in_array($session->id, [1, 17, 32]) && $participant->phone ) {
                $message = Sms::send($participant->phone,'Bonjour '.$participant->first_name.'. Bienvenue a la session '.$session->id.'de la 5eme edition Cardiotech Senegal');
                
                if ($message) {
                  $upd = Scan::where([['session_id','=',$arg['session']], ['user_id','=',$arg['id']]])->update(['flag_sms' => 1]);
                }

              }

              return  $response->withStatus(200)->write('1');
            }

          }

        } catch (\Exception $e) {
          // La session n'existe pas !
          return  $response->withStatus(200)->write('2');
        }

      } catch (\Exception $e) {
        // Le participant n'existe pas
        return  $response->withStatus(200)->write('3');
      }

      

    }


    public function allQrCodes($request, $response, $arg)
    {


        if(!$this->helper->checkConnexion())
          return $response->withRedirect($this->router->pathFor('login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

        if(!$this->helper->checkPrivillieges($this->usr['role_id'], [1,2,3,4])){
          $data = [
            'authorized'   => false,
          ];
          return $this->view->render($response, 'allParticipant.twig', compact('data'));
        }

        $participants = Participant::where('status','=',2)
          ->with([
            'qrcode' => function($q) { $q->orderBy('created_at', 'DESC');},
            'etat',
            'country',
            'state',
            'addedby',
            'validatedby',
            ])->get();



        $data = [
          'participants'     => $participants,
          'authorized'       => true,
        ];

        return $this->view->render($response, 'allQrCodes.twig', compact('data'));

    }


    public function showScansList($request, $response, $arg)
    {

      if(!$this->helper->checkConnexion())
        return $response->withRedirect($this->router->pathFor('login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

      if(!$this->helper->checkPrivillieges($this->usr['role_id'], [1,2,3,4])){
        $data = [
          'authorized'   => false,
        ];
        return $this->view->render($response, 'allScans.twig', compact('data'));
      }

      //$scans = Scan::groupBy('participant_id')->orderBy('created_at', 'desc')->distinct('participant_id')->with(['participant','user','session'])->get(['participant_id']);
      $scans = Scan::selectRaw('count(session_id) as nb, participant_id')->orderBy('nb', 'DESC')->groupBy('participant_id')->distinct('participant_id')->with(['participant','user','session'])->get();

      $scans_01 = Scan::where([['created_at','>=','2024-12-16 00:00:00'],['created_at','<','2024-12-17 00:00:00'] ])->selectRaw('count(session_id) as nb, participant_id')->orderBy('nb', 'DESC')->groupBy('participant_id')->distinct('participant_id')->with(['participant','user','session'])->get();

      $scans_02 = Scan::where([['created_at','>=','2024-12-17 00:00:00'],['created_at','<','2024-12-18 00:00:00'] ])->selectRaw('count(session_id) as nb, participant_id')->orderBy('nb', 'DESC')->groupBy('participant_id')->distinct('participant_id')->with(['participant','user','session'])->get();

      $scans_03 = Scan::where([['created_at','>=','2024-12-18 00:00:00'],['created_at','<','2024-12-19 00:00:00'] ])->selectRaw('count(session_id) as nb, participant_id')->orderBy('nb', 'DESC')->groupBy('participant_id')->distinct('participant_id')->with(['participant','user','session'])->get();

      //$this->helper->debug($scans->toArray());
      //exit;

      $data = [
        'authorized'    => true,
        'scans'         => $scans,
        'scans_01'         => $scans_01,
        'scans_02'         => $scans_02,
        'scans_03'         => $scans_03
      ];


      return $this->view->render($response, 'allScans.twig', compact('data'));

    }



    public function showAttestationsList($request, $response, $arg)
    {

       if(!$this->helper->checkConnexion())
        return $response->withRedirect($this->router->pathFor('login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

      if(!$this->helper->checkPrivillieges($this->usr['role_id'], [1,2,3])){
        $data = [
          'authorized'   => false,
        ];
        return $this->view->render($response, 'allScans.twig', compact('data'));
      }


      $attestations = Attestation::all();


      $data = [
        'authorized'          => true,
        'attestations'        => $attestations,
      ];

      return $this->view->render($response, 'allCertificat.twig', compact('data'));
    }



    public function generateAllBadge($request, $response, $arg)
    {

      try {



        return  $response->withStatus(200)->write('1');

      } catch (\Exception $e) {
        /*
        echo "<pre>";
        var_dump($e->getMessage());
        echo "<pre>";
        */
        return  $response->withStatus(200)->write('3');
      }

      return  $response->withStatus(200)->write('2');
    }

    

    public function oldScan($request, $response, $arg)
    {

      $partticipant = Participant::where('id','=',$arg['id'])->first();
      if ($partticipant) {
        Sms::$client_id = 'ouiAAgIG6wUOgac7AfErsEaD3Lgmc7KD';
        Sms::$client_secret =  'hnIhwENmtCkIr2Hk';
        $token = Sms::getTokensFromApi();
        //$message = Sms::sendSms($partticipant->phone,'Bonjour '.$partticipant->first_name.'. Bienvenue au 5ème Congrès de la Société Sénégalaise de Cardiologie - 2ème édition Cardiotech Sénégal');
        echo "<br>Prénom(s) :".$partticipant->first_name;
        echo "<br>Nom :".$partticipant->last_name;
        echo "<br>Tel :".$partticipant->phone;
        //$this->helper->debug($message);
      }
      else {
        echo"<br><br><br>!!";
      }

      echo "<br><br><br>Cette page est en cours de développement !!";
    }

}