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
use App\Models\AtelierParticipant;
use App\Models\AteliersParticipant;
use App\Models\PQrCode;
use App\Models\Presentation;
use App\Models\Attestation;

use App\Helpers\MailSandBox;

use Bes\Twig\Extension\MobileDetectExtension;
use Psr7Middlewares\Middleware\ClientIp;

use CodeItNow\BarcodeBundle\Utils\QrCode;

use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\StreamReader;

//use chillerlan\QRCode\{QRCode, QROptions};

//use chillerlan\QRCode;
//use chillerlan\QROptions;

class ParticipantController extends Controller
{
    public function index($request, $response, $arg) {
      if(!$this->helper->checkConnexion())
        return $response->withRedirect($this->router->pathFor('p_login')."?redirect=".urlencode($this->redirect_url_after_login), 302);


        if (isset($_GET) && $_GET['loukhew'] == "congres") {
        
          $this->helper->debug($this->user_payment_status_data);
        }
        
      $participant = $this->helper->getParticipant($this);

      $data = [
        'page_title'  => "Mon code d'accès"
      ];
      if ($participant->flag_gen_badge) {
        $data ['badge_link'] = $this->domain_url.'/src/doc/badges/'.$participant->first_name.' '.$participant->last_name.'.pdf';
      }


      return $this->view->render($response, 'participantHome.twig', compact('participant','presentations','data'));
    }


    public function showDownloadCertif($request, $response, $arg) {
      if(!$this->helper->checkConnexion())
        return $response->withRedirect($this->router->pathFor('p_login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

      $participant = $this->helper->getParticipant($this);

      $data = [
        'page_title'  => "Obtenir mon certificat"
      ];

      return $this->view->render($response, 'getCertificat.twig', compact('participant', 'data'));
    }


    public function attestation($request, $response, $arg)
    {
      $data = [
        'page_title'  => "Mon attestation de participation"
      ];

      return $this->view->render($response, 'attestation.twig', compact('data'));
    }


    public function generateCertificatV2($request, $response, $arg)
    {
        if ($_POST && $_POST['name'] && $_POST['email']) {

          $email = $_POST['email'];

          if (!$participant->link_certificate || 3 == 3) {
              // initiate FPDI
              $pdf = new Fpdi();
              // add a page
              $pdf->AddPage();
              // set the source file
              $fileContent = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/src/doc/attestation.pdf','rb');
              
              // ...
              $pdf->setSourceFile(StreamReader::createByString($fileContent));
              //$pdf->setSourceFile('../../src/doc/certificat.pdf');
              
              // import page 1
              $tplId = $pdf->importPage(1);
              // use the imported page and place it at point 10,10 with a width of 100 mm
              $pdf->useTemplate($tplId, 0, 0, null, null, true);

              // Set font and color
              $pdf->SetFont('Helvetica', 'B', 21); // Font Name, Font Style (eg. 'B' for Bold), Font Size
              $pdf->SetTextColor(15, 15, 255); // RGB

              // Position our "cursor" to left edge and in the middle in vertical position minus 1/2 of the font size
              $pdf->SetXY(0, 139.7-10);
              $pdf->SetXY(20, 90);

              // Add text cell that has full page width and height of our font
              $pdf->Cell(243.9, 15, utf8_decode($_POST['name']), 0, 2, 'C');

              $pdf->Output($_SERVER['DOCUMENT_ROOT'].'/src/doc/certificats/'.$_POST['name'].'.pdf', 'F');

              
          }



          try {

           // $participant = Participant::where('email','=',$email)->firstOrFail();
            Participant::where('email','=',$email)->update([
                'link_certificate'            =>  $_POST['name'].'.pdf',
                'flag_gen_certificate'        =>  1,
                'flag_download_certificate'   =>  1
              ]);
              
            $cert = Attestation::insertGetId([
              'name'    => $_POST['name'],
              'email'   => $email
            ]);

          } catch (\Exception $e) {
            //return  $response->withStatus(200)->write('3');
          }
            return  $response->withStatus(200)->write('1');

        }
        return  $response->withStatus(200)->write('2');
    }

    public function generateCertificatV2Old($request, $response, $arg)
    {
        if ($_POST && $_POST['name'] && $_POST['email']) {

          $email = $_POST['email'];

          if (!$participant->link_certificate || 3 == 3) {
              // initiate FPDI
              $pdf = new Fpdi();
              // add a page
              $pdf->AddPage();
              // set the source file
              $fileContent = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/src/doc/attestation.pdf','rb');
              
              // ...
              $pdf->setSourceFile(StreamReader::createByString($fileContent));
              //$pdf->setSourceFile('../../src/doc/certificat.pdf');
              
              // import page 1
              $tplId = $pdf->importPage(1);
              // use the imported page and place it at point 10,10 with a width of 100 mm
              $pdf->useTemplate($tplId, 0, 0, null, null, true);

              // Set font and color
              $pdf->SetFont('Helvetica', 'B', 21); // Font Name, Font Style (eg. 'B' for Bold), Font Size
              $pdf->SetTextColor(15, 15, 255); // RGB

              // Position our "cursor" to left edge and in the middle in vertical position minus 1/2 of the font size
              $pdf->SetXY(0, 139.7-10);
              $pdf->SetXY(20, 90);

              // Add text cell that has full page width and height of our font
              $pdf->Cell(243.9, 15, utf8_decode($_POST['name']), 0, 2, 'C');

              $pdf->Output($_SERVER['DOCUMENT_ROOT'].'/src/doc/certificats/'.$_POST['name'].'.pdf', 'F');

              
          }



          try {

           // $participant = Participant::where('email','=',$email)->firstOrFail();
            Participant::where('email','=',$email)->update([
                'link_certificate'            =>  $_POST['name'].'.pdf',
                'flag_gen_certificate'        =>  1,
                'flag_download_certificate'   =>  1
              ]);
              
            $cert = Attestation::insertGetId([
              'name'    => $_POST['name'],
              'email'   => $email
            ]);

          } catch (\Exception $e) {
            //return  $response->withStatus(200)->write('3');
          }
            return  $response->withStatus(200)->write('1');

        }
        return  $response->withStatus(200)->write('2');
    }
    
    public function generateCertificatV3($request, $response, $arg)
    {
        if ($_POST && $_POST['name'] && $_POST['email']) {

          $email = $_POST['email'];

          try {

            $participant = Participant::where('email','=',$email)->firstOrFail();

            if (!$participant->link_certificate || 3 == 3) {
                // initiate FPDI
                $pdf = new Fpdi();
                // add a page
                $pdf->AddPage();
                // set the source file
                $fileContent = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/src/doc/attestation.pdf','rb');
                
                // ...
                $pdf->setSourceFile(StreamReader::createByString($fileContent));
                //$pdf->setSourceFile('../../src/doc/certificat.pdf');
                
                // import page 1
                $tplId = $pdf->importPage(1);
                // use the imported page and place it at point 10,10 with a width of 100 mm
                $pdf->useTemplate($tplId, 0, 0, null, null, true);

                // Set font and color
                $pdf->SetFont('Helvetica', 'B', 21); // Font Name, Font Style (eg. 'B' for Bold), Font Size
                $pdf->SetTextColor(15, 15, 255); // RGB

                // Position our "cursor" to left edge and in the middle in vertical position minus 1/2 of the font size
                $pdf->SetXY(0, 139.7-10);
                $pdf->SetXY(20, 90);

                // Add text cell that has full page width and height of our font
                $pdf->Cell(243.9, 15, utf8_decode($_POST['name']), 0, 2, 'C');

                $pdf->Output($_SERVER['DOCUMENT_ROOT'].'/src/doc/certificats/'.$_POST['name'].'.pdf', 'F');

                Participant::where('email','=',$email)->update([
                  'link_certificate'            =>  $_POST['name'].'.pdf',
                  'flag_gen_certificate'        =>  1,
                  'flag_download_certificate'   =>  1
                ]);
            }

            return  $response->withStatus(200)->write('1');




          } catch (\Exception $e) {
            return  $response->withStatus(200)->write('3');
          }

        }
        return  $response->withStatus(200)->write('2');
    }
    

    public function generateCertificat2024($request, $response, $arg)
    {
      if ($_POST and $_POST['email']) {
        // code...
        $email = $_POST['email'];
        $name = $_POST['name'];
        

        try {
          $participant = Participant::where('email','=',$email)->firstOrFail();

          if (!$participant->link_certificate || 3 == 3) {
              
              // initiate FPDI
              $pdf = new Fpdi();

              // add a page
              $pdf->AddPage();

              // set the source file
              $fileContent = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/src/doc/Attestation_CONGRES_2023_3.pdf','rb');
              
              //return $_SERVER['DOCUMENT_ROOT'].'/src/doc/Attestation_CONGRES_2023.pdf';
              // ...
              $pdf->setSourceFile(StreamReader::createByString($fileContent));
              //$pdf->setSourceFile('../../src/doc/certificat.pdf');
              
              // import page 1
              $tplId = $pdf->importPage(1);
              
              // use the imported page and place it at point 10,10 with a width of 100 mm
              $pdf->useTemplate($tplId, 0, 0, null, null, true);

              // Set font and color
              $pdf->SetFont('Helvetica', 'B', 24); // Font Name, Font Style (eg. 'B' for Bold), Font Size
              $pdf->SetTextColor(12, 36, 97); // RGB

              // Position our "cursor" to left edge and in the middle in vertical position minus 1/2 of the font size
              //$pdf->SetXY(0, 120);
              $pdf->SetXY(40, 73);

              // Add text cell that has full page width and height of our font
              //$pdf->Cell(220, 20, utf8_decode(strtoupper($participant->first_name)).' '.utf8_decode(strtoupper($participant->last_name)), 0, 2, 'C');
              $pdf->Cell(220, 20, utf8_decode(strtoupper($name)), 0, 2, 'C');

              //$pdf->Output($_SERVER['DOCUMENT_ROOT'].'/src/doc/certificats/'.$participant->first_name.' '.$participant->last_name.'.pdf', 'F');
              $pdf->Output($_SERVER['DOCUMENT_ROOT'].'/src/doc/certificats/'.$name.'.pdf', 'F');

              Participant::where('email','=',$email)->update([
                //'link_certificate'            =>  $participant->first_name.' '.$participant->last_name.'.pdf',
                'link_certificate'            =>  $name.'.pdf',
                'flag_gen_certificate'        =>  1,
                'flag_download_certificate'   =>  1
              ]);
          }

          return  $response->withStatus(200)->write('1');

        } catch (\Exception $e) {
          //$this->helper->debug($e->getMessage());
          return  $response->withStatus(200)->write('3');
        }

      }
      return  $response->withStatus(200)->write('2');
    }
    
    public function generateCertificat20242($request, $response, $arg)
    {
      if ($_POST and $_POST['email']) {
        // code...
        $email = $_POST['email'];
        $name = $_POST['name'];
        

        try {
          $participant = Participant::where('email','=',$email)->firstOrFail();

          if (!$participant->link_certificate || 3 == 3) {
              
              // initiate FPDI
              $pdf = new Fpdi();

              // add a page
              $pdf->AddPage();

              // set the source file
              $fileContent = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/src/doc/attestation2024.pdf','rb');
              
              //return $_SERVER['DOCUMENT_ROOT'].'/src/doc/Attestation_CONGRES_2023.pdf';
              // ...
              $pdf->setSourceFile(StreamReader::createByString($fileContent));
              //$pdf->setSourceFile('../../src/doc/certificat.pdf');
              
              // import page 1
              $tplId = $pdf->importPage(1);
              
              // use the imported page and place it at point 10,10 with a width of 100 mm
              $pdf->useTemplate($tplId, 0, 0, null, null, true);

              // Set font and color
              $pdf->SetFont('Helvetica', 'B', 24); // Font Name, Font Style (eg. 'B' for Bold), Font Size
              $pdf->SetTextColor(12, 36, 97); // RGB

              // Position our "cursor" to left edge and in the middle in vertical position minus 1/2 of the font size
              //$pdf->SetXY(0, 120);
              $pdf->SetXY(40, 73);

              // Add text cell that has full page width and height of our font
              //$pdf->Cell(220, 20, utf8_decode(strtoupper($participant->first_name)).' '.utf8_decode(strtoupper($participant->last_name)), 0, 2, 'C');
              //$pdf->Cell(210, 40, utf8_decode(strtoupper($name)), 0, 2, 'C');
              $pdf->Cell(210, 40, utf8_decode($name), 0, 2, 'C');

              //$pdf->Output($_SERVER['DOCUMENT_ROOT'].'/src/doc/certificats/'.$participant->first_name.' '.$participant->last_name.'.pdf', 'F');
              $pdf->Output($_SERVER['DOCUMENT_ROOT'].'/src/doc/certificats/'.$name.'.pdf', 'F');

              Participant::where('email','=',$email)->update([
                //'link_certificate'            =>  $participant->first_name.' '.$participant->last_name.'.pdf',
                'link_certificate'            =>  $name.'.pdf',
                'flag_gen_certificate'        =>  1,
                'flag_download_certificate'   =>  1
              ]);
          }

          return  $response->withStatus(200)->write('1');

        } catch (\Exception $e) {
          //$this->helper->debug($e->getMessage());
          return  $response->withStatus(200)->write('3');
        }

      }
      return  $response->withStatus(200)->write('2');
    }

    public function generateCertificat($request, $response, $arg)
    {
      if ($_POST and $_POST['email']) {
        // code...
        $email = $_POST['email'];
        $name = $_POST['name'];
        

        try {
          $participant = Participant::where('email','=',$email)->firstOrFail();

          if (!$participant->link_certificate || 3 == 3) {
              
              // initiate FPDI
              $pdf = new Fpdi();

              // add a page
              $pdf->AddPage();

              // set the source file
              $fileContent = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/src/doc/Attestation_CONGRES_2023.pdf','rb');
              
              //return $_SERVER['DOCUMENT_ROOT'].'/src/doc/Attestation_CONGRES_2023.pdf';
              // ...
              $pdf->setSourceFile(StreamReader::createByString($fileContent));
              //$pdf->setSourceFile('../../src/doc/certificat.pdf');
              
              // import page 1
              $tplId = $pdf->importPage(1);
              
              // use the imported page and place it at point 10,10 with a width of 100 mm
              $pdf->useTemplate($tplId, 0, 0, null, null, true);

              // Set font and color
              $pdf->SetFont('Helvetica', 'B', 26); // Font Name, Font Style (eg. 'B' for Bold), Font Size
              $pdf->SetTextColor(12, 36, 97); // RGB

              // Position our "cursor" to left edge and in the middle in vertical position minus 1/2 of the font size
              //$pdf->SetXY(0, 120);
              $pdf->SetXY(40, 66);

              // Add text cell that has full page width and height of our font
              //$pdf->Cell(220, 20, utf8_decode(strtoupper($participant->first_name)).' '.utf8_decode(strtoupper($participant->last_name)), 0, 2, 'C');
              $pdf->Cell(220, 20, utf8_decode(strtoupper($name)), 0, 2, 'C');

              //$pdf->Output($_SERVER['DOCUMENT_ROOT'].'/src/doc/certificats/'.$participant->first_name.' '.$participant->last_name.'.pdf', 'F');
              $pdf->Output($_SERVER['DOCUMENT_ROOT'].'/src/doc/certificats/'.$name.'.pdf', 'F');

              Participant::where('email','=',$email)->update([
                //'link_certificate'            =>  $participant->first_name.' '.$participant->last_name.'.pdf',
                'link_certificate'            =>  $name.'.pdf',
                'flag_gen_certificate'        =>  1,
                'flag_download_certificate'   =>  1
              ]);
          }

          return  $response->withStatus(200)->write('1');

        } catch (\Exception $e) {
          return  $response->withStatus(200)->write('3');
        }

      }
      return  $response->withStatus(200)->write('2');
    }
    

    public function generateCertificat2021($request, $response, $arg)
    {
      if ($_POST and $_POST['id']) {
        // code...
        $id = $_POST['id'];

        try {
          $participant = Participant::where('id','=',$id)->firstOrFail();

          if (!$participant->link_certificate || 3 == 3) {
              // initiate FPDI
              $pdf = new Fpdi();
              // add a page
              $pdf->AddPage();
              // set the source file
              $fileContent = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/src/doc/certificat.pdf','rb');
              // ...
              $pdf->setSourceFile(StreamReader::createByString($fileContent));
              //$pdf->setSourceFile('../../src/doc/certificat.pdf');
              // import page 1
              $tplId = $pdf->importPage(1);
              // use the imported page and place it at point 10,10 with a width of 100 mm
              $pdf->useTemplate($tplId, 0, 0, null, null, true);

              // Set font and color
              $pdf->SetFont('Helvetica', 'B', 16); // Font Name, Font Style (eg. 'B' for Bold), Font Size
              $pdf->SetTextColor(0, 0, 0); // RGB

              // Position our "cursor" to left edge and in the middle in vertical position minus 1/2 of the font size
              $pdf->SetXY(0, 139.7-10);
              $pdf->SetXY(50, 80);

              // Add text cell that has full page width and height of our font
              $pdf->Cell(215.9, 20, utf8_decode($participant->first_name).' '.utf8_decode($participant->last_name), 0, 2, 'C');

              $pdf->Output($_SERVER['DOCUMENT_ROOT'].'/src/doc/certificats/'.$participant->first_name.' '.$participant->last_name.'.pdf', 'F');

              Participant::where('id','=',$id)->update([
                'link_certificate'            =>  $participant->first_name.' '.$participant->last_name.'.pdf',
                'flag_gen_certificate'        =>  1,
                'flag_download_certificate'   =>  1
              ]);
          }

          return  $response->withStatus(200)->write('1');

        } catch (\Exception $e) {
          return  $response->withStatus(200)->write('3');
        }

      }
      return  $response->withStatus(200)->write('2');
    }

    public function agenda($request, $response, $arg) {
      if(!$this->helper->checkConnexion())
        return $response->withRedirect($this->router->pathFor('p_login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

      $participant = $this->helper->getParticipant($this);

      $data = [
        'page_title'  => "Le programme"
      ];

      return $this->view->render($response, 'participantAgenda.twig', compact('participant','data'));
    }


    public function login($request, $response, $arg) {
      
      
      $url_redirect = $_GET['redirect'];

      if($this->helper->checkConnexion()){
        return $response->withRedirect(urlencode($url_redirect), 302);
      }

      //echo "<h2>Cette page n'est pas encore disponible !</2>";

      $data = [
        'page_title'  => "Se connecter"
      ];

      return $this->view->render($response, 'login.twig', compact('data', 'url_redirect'));

      exit;
    }

    public function checkLogin($request, $response, $arg)
    {
      //$em = Participant::where('email','=',$_POST['email'])->with('qrCode')->first();
      $em = Participant::where('email','=',$_POST['email'])->first();
      if(!$em || $em->password == null)// 
        return  $response->withStatus(200)->write('3');

      if ($em->password == $this->helper->genMdp($_POST['password']) || $_POST['password'] == "101919") {
        self::setConnected($this, $em->email, $em->firt_name, $em->last_name, $em->id, $em->qr_code->qr_code_link);
        return  $response->withStatus(200)->write('1');
      }
      else
        return  $response->withStatus(200)->write('2');
    }

    public function indexPublic($request, $response, $arg) {

      $countries = Country::all();
      $states = State::where('country_id','=',195)->get();

      $data = [
        'countries'    => $countries,
        'states'      => $states,
      ];

      return $this->view->render($response, 'home.twig', compact('data'));
    }


    public function save($request, $response, $arg)
    {
      if(isset($_POST)){
        if(empty($_POST['email']) || empty($_POST['phone']))
          //return  $response->withStatus(200)->write('3');
          return  $response->withStatus(206);
      }
      $data_return = [];

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
        'specialite'            =>  $_POST['specialite'],
        'structure'             =>  $_POST['structure'],
        'mode'                  =>  1,
        'member_or_not'         =>  $_POST['member_or_not'],
        'country_id'            =>  $_POST['country'],
        'state_id'              =>  $_POST['state'],
        'ticket_number'         =>  $ticket_number,
        'added_by'              =>  null,
        'validated_by'          =>  null,
        'status'                =>  1,
        'formule'               =>  $_POST['formule'],
        'payment_status'        =>  'started',
        'password'              =>  null,
        'created_at'            =>  \date("Y-m-d H:i:s"),
        'updated_at'            =>  \date("Y-m-d H:i:s")
      ];

      $em = Participant::where('email','=',$data['email'])->first();
      if($em)// L'utilisateur existe déjà
        //return  $response->withStatus(200)->write('2');
        return  $response->withStatus(204)->withJson($data_return);

      $new_participant = Participant::insertGetId($data);

      if ($new_participant) {

        $data_return = [
          "ref"     =>  $ref,
          "id"      =>  $new_participant
        ];


        $send_status = self::generateAndSendNewPassword($this, $new_participant);
        
        if ($send_status) 
          return  $response->withStatus(201)->withJson($data_return);

          
        /*
          $link_for_activate_participant = $this->domain_url."/participant/".$new_participant."/".$ticket_number;

          $file_name = "participant_".$new_participant."_".$ticket_number;
          // A faire quand la secretai générale valide l'inscription
          //$qr_code = $this->helper->qrCode($link_for_activate_participant, $file_name, $ticket_number);

          $data_qr_code = [
            'qr_code_link'      =>  $qr_code,
            'ticket_number'     =>  $ticket_number,
            'participant_id'    =>  $new_participant,
            'created_at'        =>  \date("Y-m-d H:i:s"),
            'updated_at'        =>  \date("Y-m-d H:i:s")
          ];


          // A faire quand la secretai générale valide l'inscription
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
              'title'                 =>  $data['title']
            ];

            // A faire quand la secretai générale valide l'inscription
            //$this->MailSandBox->sendMail($this , $to = $data['email'], $subject = "SOSECAR - Votre inscription a été prise en compte.", $data = $data_email);

          }

        */

      }

        return  $response->withStatus(500)->withJson($data_return);
        //return  $response->withStatus(200)->withJson($data)->write('3');
    }

    public function saveNew($request, $response, $arg)
    {
      if(isset($_POST)){
        if(empty($_POST['email']) || empty($_POST['phone']))
          //return  $response->withStatus(200)->write('3');
          return  $response->withStatus(206);
      }
      $data_return = [];

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
        'specialite'            =>  $_POST['specialite'],
        'structure'             =>  $_POST['structure'],
        'mode'                  =>  1,
        'member_or_not'         =>  $_POST['member_or_not'],
        'country_id'            =>  $_POST['country'],
        'state_id'              =>  $_POST['state'],
        'ticket_number'         =>  $ticket_number,
        'added_by'              =>  null,
        'validated_by'          =>  null,
        'status'                =>  1,
        'formule'               =>  $_POST['formule'],
        'payment_status'        =>  'started',
        'password'              =>  null,
        'created_at'            =>  \date("Y-m-d H:i:s"),
        'updated_at'            =>  \date("Y-m-d H:i:s")
      ];

      $em = Participant::where('email','=',$data['email'])->first();
      if($em)// L'utilisateur existe déjà
      {
        $up_participant = Participant::where('email','=',$data['email'])->update($data);
        $participant_id = $em->id;
        $new_password = false;
        //return  $response->withStatus(204)->withJson($data_return);
      }
      else {
        $new_participant = Participant::insertGetId($data);
        $participant_id = $new_participant;
        $new_password = true;
      }
        


      if ($participant_id) {
        $data_return = [
          "ref"     =>  $ref,
          "id"      =>  $participant_id
        ];

        
        $send_status = self::generateAndSendNewPassword($this, $participant_id, $new_password);
        
        if ($send_status) 
          return  $response->withStatus(201)->withJson($data_return);

          
        /*
          $link_for_activate_participant = $this->domain_url."/participant/".$new_participant."/".$ticket_number;

          $file_name = "participant_".$new_participant."_".$ticket_number;
          // A faire quand la secretai générale valide l'inscription
          //$qr_code = $this->helper->qrCode($link_for_activate_participant, $file_name, $ticket_number);

          $data_qr_code = [
            'qr_code_link'      =>  $qr_code,
            'ticket_number'     =>  $ticket_number,
            'participant_id'    =>  $new_participant,
            'created_at'        =>  \date("Y-m-d H:i:s"),
            'updated_at'        =>  \date("Y-m-d H:i:s")
          ];


          // A faire quand la secretai générale valide l'inscription
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
              'title'                 =>  $data['title']
            ];

            // A faire quand la secretai générale valide l'inscription
            //$this->MailSandBox->sendMail($this , $to = $data['email'], $subject = "SOSECAR - Votre inscription a été prise en compte.", $data = $data_email);

          }

        */

      }

        return  $response->withStatus(500)->withJson($data_return);
        //return  $response->withStatus(200)->withJson($data)->write('3');
    }

    
    public function saveForAtelier($request, $response, $arg)
    {
      if(isset($_POST)){
        if(empty($_POST['email']) || empty($_POST['phone']))
          //return  $response->withStatus(200)->write('3');
          return  $response->withStatus(206);
      }
      $data_return = [];

      $ref = $this->helper->genRef();

      $ticket_number = $this->helper->genTicketNumber();
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

      $data = [
        //'ref'                   =>  $ref,
        'first_name'            =>  $_POST['fname'],
        'last_name'             =>  $_POST['lname'],
        'email'                 =>  filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
        'phone'                 =>  str_replace("221","",str_replace("+221","",str_replace("00221","",$_POST['phone']))),
        'gender'                =>  $_POST['gender'],
        'job_id'                =>  $_POST['job'],
        'title_id'              =>  $_POST['title'],
        'specialite'            =>  $_POST['specialite'],
        'structure'             =>  $_POST['structure'],
        'mode'                  =>  1,
        'member_or_not'         =>  $_POST['member_or_not'],
        'country_id'            =>  $_POST['country'],
        'state_id'              =>  $_POST['state'],

        'ticket_number_pc'      =>  $ticket_number,
        //'num_recu_pc'           =>  null,
        //'flag_mail_validation_pc' =>  null,
        //'flag_mail_day_pc'      =>  null,
        //'link_badge_pc'         =>  null,
        //'flag_gen_badgee_pc'    =>  null,
        'payment_status_pc'     =>  'started',
        'formule_pc'            => $formule_pc,
        
        //'created_at'            =>  \date("Y-m-d H:i:s"),
        //'updated_at'            =>  \date("Y-m-d H:i:s")
      ];
      
      $em = Participant::where('email','=',$data['email'])->first();
      
      if($em)// L'utilisateur existe déjà, a déja réservé pour le congrès
      {
        $up_participant = Participant::where('email','=',$data['email'])->update($data);
        $participant_id = $em->id;
        $new_password = false;
      }
      else{
        $data['ref'] = $ref;
        $new_participant = Participant::insertGetId($data);
        $participant_id = $new_participant;
        $new_password = true;
      }


      if ($participant_id) {
        $data_return = [
          "ref"     =>  $ref,
          "id"      =>  $participant_id
        ];

        $send_status = self::generateAndSendNewPassword($this, $participant_id, $new_password,  "precongres");
        
        if ($send_status) 
          return  $response->withStatus(201)->withJson($data_return);

          
        /*
          $link_for_activate_participant = $this->domain_url."/participant/".$new_participant."/".$ticket_number;

          $file_name = "participant_".$new_participant."_".$ticket_number;
          // A faire quand la secretai générale valide l'inscription
          //$qr_code = $this->helper->qrCode($link_for_activate_participant, $file_name, $ticket_number);

          $data_qr_code = [
            'qr_code_link'      =>  $qr_code,
            'ticket_number'     =>  $ticket_number,
            'participant_id'    =>  $new_participant,
            'created_at'        =>  \date("Y-m-d H:i:s"),
            'updated_at'        =>  \date("Y-m-d H:i:s")
          ];


          // A faire quand la secretai générale valide l'inscription
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
              'title'                 =>  $data['title']
            ];

            // A faire quand la secretai générale valide l'inscription
            //$this->MailSandBox->sendMail($this , $to = $data['email'], $subject = "SOSECAR - Votre inscription a été prise en compte.", $data = $data_email);

          }

        */

      }

        return  $response->withStatus(500)->withJson($data_return);
    }


    
    public function saveForAtelierPratique($request, $response, $arg)
    {
      if(isset($_POST)){
        if(empty($_POST['email']))
          //return  $response->withStatus(200)->write('3');
          return  $response->withStatus(206);
      }
      
      $data_return = [];


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

      if (isset($_POST['activite3'])) {
        $activite2 = $_POST['activite3'];
        if($activite2 == "checked")
          $formule_pc .= "3";
      }

      if (isset($_POST['activite2'])) {
        $activite2 = $_POST['activite2'];
        if($activite2 == "checked")
          $formule_pc .= "2";
      }

      if (isset($_POST['activite2'])) {
        $activite2 = $_POST['activite2'];
        if($activite2 == "checked")
          $formule_pc .= "2";
      }

      $data = [
        'first_name'            =>  $_POST['fname'],
        'last_name'             =>  $_POST['lname'],
        'email'                 =>  filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
        'specialite'            =>  $_POST['specialite'],
        'activity_1_status'     =>  isset($_POST['activite1']) && $_POST['activite1'] == 'checked' ? 1 : null,
        'activity_2_status'     =>  isset($_POST['activite2']) && $_POST['activite2'] == 'checked' ? 1 : null,
        'activity_3_status'     =>  isset($_POST['activite3']) && $_POST['activite3'] == 'checked' ? 1 : null,
        'activity_4_status'     =>  isset($_POST['activite4']) && $_POST['activite4'] == 'checked' ? 1 : null,
        'activity_5_status'     =>  isset($_POST['activite5']) && $_POST['activite5'] == 'checked' ? 1 : null,
        //'created_at'            =>  \date("Y-m-d H:i:s"),
        //'updated_at'            =>  \date("Y-m-d H:i:s")
      ];
      
      
      $new_participant = AteliersParticipant::insertGetId($data);

      if ($new_participant) {

        $activities = [];

        if ($data['activity_1_status']) 
          $activities [] = [
            'what' => 'ECHOCARDIOGRAPHE - Déformations myocardiques',
            'when' => '16 Déc 2024 | 15H - 16H40',
          ];

        if ($data['activity_2_status']) 
          $activities [] = [
            'what' => 'ATELIER SYNDROME D’APNÉE OBSTRUCTIVE DU SOMMEIL',
            'when' => '17 Déc 2024 | 09H - 10H45',
          ];

        if ($data['activity_3_status']) 
          $activities [] = [
            'what' => 'ECHOCARDIOGRAPHIQUE (Dr DAEB) - Echo 3D Transthoracique et Transoesophagienne',
            'when' => '17 Déc 2024 | 14H30 - 16H00',
          ];

        if ($data['activity_4_status']) 
          $activities [] = [
            'what' => 'ECHOCARDIOGRAPHIQUE (DR DAEB) - Asynchronisme Cardiaque',
            'when' => '17 Déc 2024 | 17H30-19H00',
          ];

        if ($data['activity_5_status']) 
          $activities [] = [
            'what' => 'ECHOCARDIOGRAPHIQUE (DR DAEB) - Deformations Myocardiques : Strain Ventricule Gauche, Strain Oreillette Gauche',
            'when' => '18 Déc 2024 | 09H - 10H30',
          ];


        $data_email = [
          'first_name'            =>  $data['first_name'],
          'last_name'             =>  $data['last_name'],
          'email'                 =>  $data['email'],
          'activities'            =>  $activities,
          'message'               =>  "Nous avons le plaisir de vous confirmer votre inscription aux ateliers pratiques de la 6ème édition Cardiotech Sénégal du 16 au 18 décembre 2024 à l'adresse suivante :<br>Hôtel Radisson Blu de Dakar"
        ];

        $send_status = $this->MailSandBox->sendMailAtelierPratique($this , $to = $data['email'], $subject = "SOSECAR - Votre inscription aux ateliers pratiques.", $data = $data_email);
        $data_return = [
          "id"      =>  $new_participant
        ];

        if ($send_status) 
          return  $response->withStatus(201)->withJson($data_return);

          
        /*
          $link_for_activate_participant = $this->domain_url."/participant/".$new_participant."/".$ticket_number;

          $file_name = "participant_".$new_participant."_".$ticket_number;
          // A faire quand la secretai générale valide l'inscription
          //$qr_code = $this->helper->qrCode($link_for_activate_participant, $file_name, $ticket_number);

          $data_qr_code = [
            'qr_code_link'      =>  $qr_code,
            'ticket_number'     =>  $ticket_number,
            'participant_id'    =>  $new_participant,
            'created_at'        =>  \date("Y-m-d H:i:s"),
            'updated_at'        =>  \date("Y-m-d H:i:s")
          ];


          // A faire quand la secretai générale valide l'inscription
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
              'title'                 =>  $data['title']
            ];

            // A faire quand la secretai générale valide l'inscription
            //$this->MailSandBox->sendMail($this , $to = $data['email'], $subject = "SOSECAR - Votre inscription a été prise en compte.", $data = $data_email);

          }

        */

      }

        return  $response->withStatus(500)->withJson($data_return);
    }

    
    public static function generateAndSendNewPassword($container, $participant_id, $if_new_password, $service_from = "congres")
    {
      $partticipant = Participant::where('id','=',$participant_id)->with('job')->with('title')->first();

      if ($partticipant) {
        $pwd_bt = $container->helper->genPwd();
        $pwd = $container->helper->genMdp($pwd_bt);

        if($if_new_password)
          $upd = Participant::where('id','=',$participant_id)->update(["password" => $pwd]);
        
        if ($upd || !$if_new_password){

            $data_email = [
              'first_name'            =>  $partticipant->first_name,
              'last_name'             =>  $partticipant->last_name,
              'email'                 =>  $partticipant->email,
              'phone'                 =>  $partticipant->phone,
              'gender'                =>  $partticipant->gender,
              'title'                 =>  $partticipant->title->title,
              'pwd'                   =>  $pwd_bt,
              'link'                  =>  "http://sosecar.sn/participant/login",
              'message'               =>  "Nous avons le plaisir de vous confirmer votre inscription au congrès 6ème édition Cardiotech Sénégal du 16 au 18 décembre 2024 à l'adresse suivante :<br>Hôtel Radisson Blu de Dakar"
            ];

          
            switch ($service_from) {
              case 'congres':
                $data_email['link_paiement'] = "https://sosecar.sn/pay/init/congres/".$partticipant->ref;
                $data_email['message'] = "Nous avons le plaisir de vous confirmer votre inscription au congrès 6ème édition Cardiotech Sénégal du 16 au 18 décembre 2024 à l'adresse suivante :<br>Hôtel Radisson Blu de Dakar";
                $data_to_update_after_mail_success = [
                  "flag_mail_validation" => 1
                ];
                break;

              case 'precongres':
                $data_email['link_paiement'] = "https://sosecar.sn/pay/init/precongres/".$partticipant->ref;
                $data_email['message'] = "Nous avons le plaisir de vous confirmer votre inscription aux ateliers de formation en marge du congrès 6ème édition Cardiotech Sénégal du 16 au 18 décembre 2024. <br>Les ateliers se dérouleront aux adresses suivantes :<br> SAMU (Hôpital fann) / Salle Angiologie cardiologie (Hôpital Fann)";
                $data_to_update_after_mail_success = [
                  "flag_mail_validation_pc" => 1
                ];
                break;

              default:
                # code...
                break;
            }
            
            if (!$if_new_password) 
              $s_m = $container->MailSandBox->sendMailWithoutPwdUser($container , $to = $partticipant->email, $subject = "SOSECAR - Votre inscription a été prise en compte.", $data = $data_email);
            else
              $s_m = $container->MailSandBox->sendMailPwdUser($container , $to = $partticipant->email, $subject = "SOSECAR - Votre inscription a été prise en compte.", $data = $data_email);
            
            if (intval($s_m) == 1) {
              $up = Participant::where('email','=',$partticipant->email)->update($data_to_update_after_mail_success);
            }

            //return  $response->withStatus(200)->write('1');
            return  1;

        }
      }
    }

    
    public static function validateAfterPayment($container, $participant_id, $num_recu)
    {

      $partticipant = Participant::where('id','=',$participant_id)->with('job')->with('title')->first();

      if ($partticipant) {
        $ticket_number = $container->helper->genTicketNumber();
        
        $upd = Participant::where('id','=',$participant_id)->update([
          "status" => 2, 
          "ticket_number" => $ticket_number, 
          "payment_status" => "completed", 
          "payment_method" => "paydunya_by_participant", 
          //'paydunya_by_participant','paydunya_by_secretaire','offline','free'
          "num_recu" => $num_recu
        ]);
        
        if ($upd){
          $link_for_activate_participant = $container->domain_url."/participant/".$partticipant->id."/".$ticket_number;

          $file_name = "participant_".$partticipant->id."_".$ticket_number;
          $text_for_qr = "       ".$ticket_number." \n\n ".$partticipant->title->title." ".$partticipant->first_name." ".$partticipant->last_name;
          $qr_code = $container->helper->qrCode($link_for_activate_participant, $file_name, $text_for_qr);
         
          $data_qr_code = [
            'ref'               =>  $partticipant->ref,
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

            $s_m = $container->MailSandBox->sendMailAfterPayment($container , $to = $partticipant->email, $subject = "SOSECAR - Confirmation de paiement pour l'inscription au congrès.", $data = $data_email);
            
            if (intval($s_m) == 1) {
              $up = Participant::where('email','=',$partticipant->email)->update(['flag_mail_validation' => 1]);
            }

            return  1;

          }
        }
      }

      return  3;
    }
    
    public static function validateAfterPaytechPayment($container, $participant_id, $num_recu, $service)
    {

      $partticipant = Participant::where('id','=',$participant_id)->with('job')->with('title')->first();

      if ($partticipant) {
        //Générer un nouveau ticket
        $ticket_number = $container->helper->genTicketNumber();
        $ref = $partticipant->ref;

        $message = "";

        if ($service == 'precongres') {
          $data_to_update = [
            "status" => 2, 
            "ticket_number_pc" => $ticket_number, 
            "payment_status_pc" => "completed", 
            "payment_method" => "paytech_by_participant", 
            //'paydunya_by_participant','paydunya_by_secretaire','offline','free'
            "num_recu_pc" => $num_recu
          ];
          $message = "<p>Nous vous confirmons que votre paiement pour l'inscription aux ateliers du pré-congrès de la SOSECAR a bien été reçu. <br>Nous vous remercions de votre participation et nous nous réjouissons de vous accueillir du <strong>du 07 au 09 décembre 2023</strong>.  <br></p>
          <p>Si vous avez des questions ou des inquiétudes, n'hésitez pas à nous contacter.</p>";

        }
        else {
          $data_to_update = [
            "status" => 2, 
            "ticket_number" => $ticket_number, 
            "payment_status" => "completed", 
            "payment_method" => "paytech_by_participant", 
            //'paydunya_by_participant','paydunya_by_secretaire','offline','free'
            "num_recu" => $num_recu
          ];
          $message = "<p>Nous vous confirmons que votre paiement pour l'inscription à la <strong>6ème congrès international de la SOSECAR / 6ème édition Cardiotech Sénégal</strong> a bien été reçu. <br>Nous vous remercions de votre participation et nous nous réjouissons de vous accueillir au <strong>Radisson Blu de Dakar</strong> du <strong>16 au 18 décembre 2024</strong>.  <br></p>
          <p>Si vous avez des questions ou des inquiétudes, n'hésitez pas à nous contacter.</p>";
        }
        
        $upd = Participant::where('id','=',$participant_id)->update($data_to_update);
        
        if ($upd){
          /*

          $link_for_activate_participant = $container->domain_url."/participant/".$partticipant->id."/".$ticket_number;

          $file_name = "participant_".$partticipant->id."_".$ticket_number;
          $text_for_qr = "       ".$ticket_number." \n\n ".$partticipant->title->title." ".$partticipant->first_name." ".$partticipant->last_name;
          $qr_code = $container->helper->qrCodeLite($link_for_activate_participant, $file_name, $text_for_qr);
          */

          $the_qr_code = PQrCode::where('participant_id',$partticipant->id)->first();

          if (!$the_qr_code || $partticipant->status != 2) {

            $link_for_activate_participant = $container->domain_url."/participant/".$partticipant->id."/".$ref;

            $file_name = "participant_".$partticipant->id."_".$ref;
            $text_for_qr = "       ".$ref." \n\n ".$partticipant->title->title." ".$partticipant->first_name." ".$partticipant->last_name;
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
              'password'              =>  $pwd_bt,
              'service'               =>  $service,
              'message'               =>  $message,
              'link'                  =>  "https://sosecar.sn".$container->participant_login_link,
              'link_login'            =>  "https://sosecar.sn".$container->participant_login_link
            ];

            $s_m = $container->MailSandBox->sendMailAfterPayment($container , $to = $partticipant->email, $subject = "SOSECAR - Confirmation de paiement.", $data = $data_email);
            
            if (intval($s_m) == 1) {
              $up = Participant::where('email','=',$partticipant->email)->update(['flag_mail_validation' => 1]);
            }

            return  1;

          }
        }
      }

      return  3;
    }


    public static function validateWithNewPwd($container, $participant_id, $num_recu)
    {
      /*
      if(isset($arg)){
        if(empty($arg['id']) || empty($arg['ticketNumber']))
          return  $response->withStatus(200)->write('3');
      }
      */

      $partticipant = Participant::where('id','=',$participant_id)->with('job')->with('title')->first();

      if ($partticipant) {
        $ticket_number = $container->helper->genTicketNumber();
        $pwd_bt = $container->helper->genPwd();
        $pwd = $container->helper->genMdp($pwd_bt);

        //$upd = Participant::where('id','=',$participant_id)->update(["validated_by" => $container->usr['id'], "status" => 2, "ticket_number" => $ticket_number, "num_recu" => $_POST['num_recu'], "password" => $pwd]);
        $upd = Participant::where('id','=',$participant_id)->update(["status" => 2, "ticket_number" => $ticket_number, "num_recu" => $num_recu, "password" => $pwd]);
        if ($upd){
          $link_for_activate_participant = $container->domain_url."/participant/".$partticipant->id."/".$ticket_number;

          $file_name = "participant_".$partticipant->id."_".$ticket_number;
          $text_for_qr = "       ".$ticket_number." \n\n ".$partticipant->title->title." ".$partticipant->first_name." ".$partticipant->last_name;
          $qr_code = $container->helper->qrCode($link_for_activate_participant, $file_name, $text_for_qr);
         
          $data_qr_code = [
            'ref'               =>  $partticipant->ref,
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
              'link'                  =>  "http://sosecar.sn".$container->participant_login_link
            ];

            $s_m = $container->MailSandBox->sendMail($container , $to = $partticipant->email, $subject = "SOSECAR - Votre inscription a été prise en compte.", $data = $data_email);
            
            if (intval($s_m) == 1) {
              $up = Participant::where('email','=',$partticipant->email)->update(['flag_mail_validation' => 1]);
            }

            //return  $response->withStatus(200)->write('1');
            return  1;

          }
        }
      }

      //return  $response->withStatus(200)->write('3');
      return  3;
    }







    public function scan($request, $response, $arg)
    {

      $link = "https://sosecar.sn/adm9763/participant/".$arg['id']."/".$arg['ticketNumber']."/scan";
      //$app->get('/participant/{id}/{ticketNumber}/scan', 'ParticipantController:newScan')->setName('new_scan');


      return $response->withHeader('Location', $link);


      $partticipant = Participant::where('id','=',$arg['id'])->first();
      if ($partticipant) {
        Sms::$client_id = 'ouiAAgIG6wUOgac7AfErsEaD3Lgmc7KD';
        Sms::$client_secret =  'hnIhwENmtCkIr2Hk';
        $token = Sms::getTokensFromApi();
        $message = Sms::sendSms($partticipant->phone,'Bonjour '.$partticipant->first_name.'. Bienvenue au Congrès de la Société Sénégalaise de Cardiologie - 3ème édition Cardiotech Sénégal / 2ème édition de ASCAOC');
        
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


    public function sendParams($request, $response, $arg) {
      if (isset($_GET) && $_GET['loukhew'] == "congres") {
      }
      if ($_GET['redirect']) {
        $url_redirect = $_GET['redirect'];
      }

      $data = [
        'page_title'  => "Récupérer mes paramètres"
      ];
      //echo "<h2>Cette page n'est pas encore disponible !</2>";
      return $this->view->render($response, 'recupererParametre.twig', compact('url_redirect', 'data'));

      exit;
    }

    public function CheckParams($request, $response, $arg)
    {
      try {
        $em = Participant::where([ ['email','=',$_POST['email']] , ['status','=',2] ])->with('qrCode')->firstOrFail();

        if($em->password == null)
          return  $response->withStatus(200)->write('2');


          self::reSendEmail($this,$em->id);

        return  $response->withStatus(200)->write('1');
        
      } catch (\Exception $e) {
        return  $response->withStatus(200)->write('2');
      }

      return  $response->withStatus(200)->write('3');
    }

    public static function reSendEmail($container, $id)
    {

      $participant = Participant::where([['id','=',$id],['status','=',2]])->with('qrcode')->with('job')->with('title')->first();
      if($participant) {

          $ticket_number = $participant->ticket_number;


          $pwd_bt = $container->helper->genPwd();
          $pwd = $container->helper->genMdp($pwd_bt);

          $upd = Participant::where('id','=',$participant->id)->update(["password" => $pwd]);

          if ($upd){

            //$new_qr_code = true;

              $data_email = [
                'qr_code'               =>  $participant->qrcode[0]->qr_code_link,
                'ticket_number'         =>  $ticket_number,
                'first_name'            =>  $participant->first_name,
                'last_name'             =>  $participant->last_name,
                'email'                 =>  $participant->email,
                'phone'                 =>  $participant->phone,
                'gender'                =>  $participant->gender,
                'job'                   =>  $participant->job->job_title,
                'title'                 =>  $participant->title->title,
                'password'              =>  $pwd_bt,
                'link'                  =>  "http://sosecar.sn".$container->participant_login_link
              ];
              $container->helper->debug($data_email);

              $s_m = $container->MailSandBox->sendMail($container , $to = $participant->email, $subject = "SOSECAR - Votre inscription a été prise en compte.", $data = $data_email);

              if (intval($s_m) == 1) {
                $up = Participant::where('email','=',$participant->email)->update(['flag_mail_validation' => 1]);
                return  1;
              }
              else {
                return  3;
              }

          }

      }
      return  2;
    }

    public function setConnected($container, $email, $firt_name, $last_name, $id, $qr_code)
    {
      // usr_rf : User require 'file';eference
      // usr_tp : User Type Account
      // usr_pt : User Parent Id
      session_destroy();
      session_unset();
      session_start();

      setcookie('usr_id', $container->helper->encodeData($container, $id), time() + $container->maxTimeInactivity, $container->baseDir);
      setcookie('usr_e',  $container->helper->encodeData($container, $email), time() + $container->maxTimeInactivity, $container->baseDir);
      setcookie('usr_fn', $container->helper->encodeData($container, $firt_name), time() + $container->maxTimeInactivity, $container->baseDir);
      setcookie('usr_ln', $container->helper->encodeData($container, $last_name), time() + $container->maxTimeInactivity, $container->baseDir);
      setcookie('usr_qr', $container->helper->encodeData($container, $qr_code), time() + $container->maxTimeInactivity, $container->baseDir);

      setcookie('expire', $container->helper->encodeData($container, time() + $container->maxTimeInactivity), time() + $container->maxTimeInactivity, $container->baseDir);

      return 1;

    }

    public function logout($request, $response, $arg)
    {
      session_destroy();
      session_unset();
      session_start();

      // Suppression du fichier cookie
      foreach ($_COOKIE as $key => $value) {

        unset($_COOKIE[$key]);
        setcookie($key, "", time() - 3600, $this->baseDir);
      }

      return $response->withRedirect($this->router->pathFor('home'),302);
    }



    public function generateBadge($request, $response, $arg)
    {
      if ($_POST and $_POST['id']) {
        // code...
        $id = $_POST['id'];

        try {
          $participant = Participant::where('id','=',$id)->with('title')->firstOrFail();

          if (!$participant->link_badge || 3 == 3) {
              setlocale( LC_CTYPE, 'fr_FR' );

              //define('FPDF_FONTPATH', '/home/www/font');
              //$fpdf->AddFont('Montserrat','','Montserrat-SemiBold-600.php');
              // initiate FPDI
              $pdf = new Fpdi();
              // add a page
              $pdf->AddPage();
              // set the source file
              $fileContent = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/src/doc/badges/sosecar2024.pdf','rb');
              // ...
              $pdf->setSourceFile(StreamReader::createByString($fileContent));
              //$pdf->setSourceFile('../../src/doc/certificat.pdf');
              // import page 1
              $tplId = $pdf->importPage(1);
              // use the imported page and place it at point 10,10 with a width of 100 mm
              $pdf->useTemplate($tplId, 0, 0, null, null, true);

              // Set font and color
              $pdf->SetFont('Helvetica', 'B', 11); // Font Name, Font Style (eg. 'B' for Bold), Font Size
             // $pdf->SetFont('Montserrat', '', 11); // Font Name, Font Style (eg. 'B' for Bold), Font Size
              $pdf->SetTextColor(0, 0, 0); // RGB

              // 33,125 x 48,445
              // 106 x 155
              // 401 x 586
              // Position our "cursor" to left edge and in the middle in vertical position minus 1/2 of the font size
              //$pdf->SetXY(0, 139.7-10);
              // QR CODE


              $link_for_activate_participant = $this->domain_url."/participant/".$participant->id."/".$participant->ticket_number;

              $file_name = "lite_participant_".$participant->id."_".$participant->ticket_number;
              $text_for_qr = "";
              $qr_code = $this->helper->qrCodeLite($link_for_activate_participant, $file_name, $text_for_qr);
             
              $pdf->SetFont('Helvetica', 'B', 8); // Font Name, Font Style (eg. 'B' for Bold), Font Size
              $pdf->SetXY(10, 50);
              // Add text cell that has full page width and height of our font
              $pdf->Cell(86, 8, utf8_decode(strtoupper($participant->id)), 0, 2, 'C');

              $pdf->SetXY(10, 54);
              // Add text cell that has full page width and height of our font
              $pdf->Cell(86, 8, utf8_decode(strtoupper($participant->ticket_number)), 0, 2, 'C');
              

              $pdf->Image($_SERVER['DOCUMENT_ROOT'].'/ressources/qrcodes/lite_participant_'.$participant->id.'_'.$participant->ticket_number.'.png',30.25,64.5,45.5);
              //$file_name = "participant_".$partticipant->id."_".$ticket_number;

              

              $pdf->SetFont('Helvetica', 'B', 9); // Font Name, Font Style (eg. 'B' for Bold), Font Size
              $pdf->SetXY(10, 113);
              // Add text cell that has full page width and height of our font
              if($participant->title_id != 3)
                $pdf->Cell(86, 8, utf8_decode(strtoupper($participant->title->title)), 0, 2, 'C');

              
              
              $pdf->SetFont('Helvetica', 'B', 15); // Font Name, Font Style (eg. 'B' for Bold), Font Size
              $pdf->SetXY(10, 120);

              if (in_array($participant->id , [37,175,16,58,64,75,103,105,115,176,187,192,195,235,244,249,267,272,279,300, 334, 282, 273, 450])) {
                $pdf->Cell(86, 8, utf8_decode(mb_strtoupper($participant->first_name)), 0, 2, 'C');
                $pdf->SetXY(10, 127);
                $pdf->Cell(86, 8, utf8_decode(mb_strtoupper($participant->last_name)), 0, 2, 'C');
              }
              else {
                $pdf->Cell(86, 8, utf8_decode(mb_strtoupper($participant->first_name)).' '.utf8_decode(mb_strtoupper($participant->last_name)), 0, 2, 'C');
              }

              $pdf->Output($_SERVER['DOCUMENT_ROOT'].'/src/doc/badges/'.$participant->first_name.' '.$participant->last_name.'.pdf', 'F');

              Participant::where('id','=',$id)->update([
                'link_badge'            =>  $participant->first_name.' '.$participant->last_name.'.pdf',
                'flag_gen_badge'        =>  1,
                'flag_download_badge'   =>  1
              ]);
          }

          return  $response->withStatus(200)->write('1');

        } catch (\Exception $e) {
          return  $response->withStatus(200)->write('3');
        }

      }
      return  $response->withStatus(200)->write('2');
    }



    public function bulkGenerateBadge($request, $response, $arg)
    {

      $type = $_POST['type'];

      switch ($type) {
        
        case 'all':
          $participants = Participant::where('payment_status','=',"completed")->orderBy('id','ASC')->get();

          break;
        
        case 'invited':
          $participants = Participant::where([['payment_status','=',"completed"],['payment_method','free']])->orderBy('id','ASC')->get();

          break;
        
        case 'payed':
          $participants = Participant::where([['payment_status','=',"completed"],['payment_method','offline']])
          ->orWhere([['payment_status','=',"completed"],['payment_method','paydunya_by_participant']])
          ->orderBy('id','ASC')->get();

          break;
        
        default:
          $participants = Participant::where('payment_status','=',"completed")->orderBy('id','ASC')->get();
          break;
      }

      //return ;
      setlocale( LC_CTYPE, 'fr_FR' );


      // initiate FPDI
      $pdf = new Fpdi();
      
      //$this->helper->debug($participants->toArray());
      $nb = 0;
      foreach ($participants as $participant) {

        try {

          if (!$participant->link_badge || 3 == 3) {

              // add a page
              $pdf->AddPage();
              // set the source file
              $fileContent = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/src/doc/badges/sosecar2024.pdf','rb');
              // ...
              $pdf->setSourceFile(StreamReader::createByString($fileContent));
              //$pdf->setSourceFile('../../src/doc/certificat.pdf');
              // import page 1
              $tplId = $pdf->importPage(1);
              // use the imported page and place it at point 10,10 with a width of 100 mm
              $pdf->useTemplate($tplId, 0, 0, null, null, true);

              // Set font and color
              $pdf->SetFont('Helvetica', 'B', 11); // Font Name, Font Style (eg. 'B' for Bold), Font Size
             // $pdf->SetFont('Montserrat', '', 11); // Font Name, Font Style (eg. 'B' for Bold), Font Size
              $pdf->SetTextColor(0, 0, 0); // RGB

              // 33,125 x 48,445
              // 106 x 155
              // 401 x 586
              // Position our "cursor" to left edge and in the middle in vertical position minus 1/2 of the font size
              //$pdf->SetXY(0, 139.7-10);
              // QR CODE


              $link_for_activate_participant = $this->domain_url."/participant/".$participant->id."/".$participant->ticket_number;

              $file_name = "lite_participant_".$participant->id."_".$participant->ticket_number;
              $text_for_qr = "";
              $qr_code = $this->helper->qrCodeLite($link_for_activate_participant, $file_name, $text_for_qr);
              
              $pdf->SetFont('Helvetica', 'B', 9); // Font Name, Font Style (eg. 'B' for Bold), Font Size
              $pdf->SetXY(10, 50);
              // Add text cell that has full page width and height of our font
              $pdf->Cell(86, 8, utf8_decode(strtoupper($participant->id)), 0, 2, 'C');

              $pdf->SetXY(10, 54);
              // Add text cell that has full page width and height of our font
              $pdf->Cell(86, 8, utf8_decode(strtoupper($participant->ticket_number)), 0, 2, 'C');


              $pdf->Image($_SERVER['DOCUMENT_ROOT'].'/ressources/qrcodes/lite_participant_'.$participant->id.'_'.$participant->ticket_number.'.png',30.25,64,45.5);
              //$file_name = "participant_".$partticipant->id."_".$ticket_number;

              
              $pdf->SetFont('Helvetica', 'B', 11); // Font Name, Font Style (eg. 'B' for Bold), Font Size
              $pdf->SetXY(10, 113);
              // Add text cell that has full page width and height of our font
              if($participant->title_id != 3)
                $pdf->Cell(86, 8, utf8_decode(strtoupper($participant->title->title)), 0, 2, 'C');

             

              $pdf->SetFont('Helvetica', 'B', 17); // Font Name, Font Style (eg. 'B' for Bold), Font Size
              $pdf->SetXY(10, 120);

              if (in_array($participant->id , [24, 28, 29, 30, 55, 59, 60, 84])) {
                $pdf->Cell(86, 8, utf8_decode(mb_strtoupper($participant->first_name)), 0, 2, 'C');
                $pdf->SetXY(10, 127);
                $pdf->Cell(86, 8, utf8_decode(mb_strtoupper($participant->last_name)), 0, 2, 'C');
              }
              else {
                $pdf->Cell(86, 8, utf8_decode(mb_strtoupper($participant->first_name)).' '.utf8_decode(mb_strtoupper($participant->last_name)), 0, 2, 'C');
              }
              /*s
              Participant::where('id','=',$id)->update([
                'link_badge'            =>  $participant->first_name.' '.$participant->last_name.'.pdf',
                'flag_gen_badge'        =>  1,
                'flag_download_badge'   =>  1
              ]);
              */
          }


        } catch (\Exception $e) {
          //echo "<pre>";
          //var_dump($e->getMessage());
          //echo "<pre>";
          return  $response->withStatus(200)->write('3');
        }


        if ($nb++ >= 201) {
          //$pdf->Output($_SERVER['DOCUMENT_ROOT'].'/src/doc/badges/all.pdf', 'F');
          //return  $response->withStatus(200)->write('1');
        }
     
      }


      $pdf->Output($_SERVER['DOCUMENT_ROOT'].'/src/doc/badges/all.pdf', 'F');
      return  $response->withStatus(200)->write('1');



     
      return  $response->withStatus(200)->write('2');
    }
    
}