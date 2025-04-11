<?php

namespace App\Controllers;

use App\Helpers\DBIP;
use App\Helpers\Helper;

use App\Helpers\SandBox;
use App\Helpers\RandomStringGenerator;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

use App\Helpers\Browser;
use App\Models\Country;
use App\Models\State;
use App\Models\User;
use App\Models\Participant;
use App\Models\Job;
use App\Models\Title;
use App\Models\PQrCode;
use App\Models\Session;
use App\Models\Scan;
use App\Models\Salle;
use App\Models\AbstractFile;

use Bes\Twig\Extension\MobileDetectExtension;
use Psr7Middlewares\Middleware\ClientIp;

use CodeItNow\BarcodeBundle\Utils\QrCode;

//use chillerlan\QRCode\{QRCode, QROptions};

//use chillerlan\QRCode;
//use chillerlan\QROptions;

class AbstractController extends Controller
{
    public function list($request, $response, $arg)
    {
      if(!$this->helper->checkConnexion())
        return $response->withRedirect($this->router->pathFor('login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

      if(!$this->helper->checkPrivillieges($this->usr['role_id'], [1,2,3,5])){
        $data = [
          'authorized'   => false,
        ];
        return $this->view->render($response, 'abstracts/list.twig', compact('data'));
      }

      $abstracts = AbstractFile::where('status','<',5)->get();
      $data = [
        'authorized'     => true,
        'abstracts'   => $abstracts
      ];
      return $this->view->render($response, 'abstracts/list.twig', compact('data'));
    }

    public function show($request, $response, $arg)
    {
      if(!$this->helper->checkConnexion())
        return $response->withRedirect($this->router->pathFor('login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

      if(!$this->helper->checkPrivillieges($this->usr['role_id'], [1,2,3,5])){
        $data = [
          'authorized'   => false,
        ];
        return $this->view->render($response, 'abstracts/show.twig', compact('data'));
      }

      try {
        $abstract = AbstractFile::where('id','=',$arg['id'])->firstOrFail();
      } catch (\Exception $e) {
        return $this->view->render($response->withStatus(404), 'errors/404.twig', compact('data'));
      }

      $data = [
        'authorized'     => true,
        'abstract'       => $abstract
      ];
      return $this->view->render($response, 'abstracts/show.twig', compact('data'));

    }

    public function publish($request, $response, $arg)
    {

            try {
              $abstract = AbstractFile::where('id','=',$arg['id'])->firstOrFail();

              if ($abstract->status == 1)
                return  $response->withStatus(200)->write('2');

            } catch (\Exception $e) {
              // L'asbtract n'existe pas
              return  $response->withStatus(200)->write('3');
            }

            $upd = AbstractFile::where('id','=',$arg['id'])->update([
              'status'       => 1,
              'updated_by'   => $this->usr['id']
            ]);


            if ($upd) {
              // Envoi du mail
              $data_email = [
                'name'            =>  $abstract['sender_name'],
                'email'           =>  $abstract['sender_email'],
                'message'         =>  'Votre acticle a été validé et publié sur le site <a href="https://sosecar.com/#" targe="_blank">sosecar.com</a>. <br> <br>Le comité scientifique.'
              ];

              $this->MailSandBox->sendMailAbstract($this , $to = $data_email['email'], $subject = "SOSECAR - Votre acticle a été publié.", $data = $data_email);

              return  $response->withStatus(200)->write('1');
            }

            return  $response->withStatus(200)->write('0');

    }

    public function disable($request, $response, $arg)
    {

          try {
            $abstract = AbstractFile::where('id','=',$arg['id'])->firstOrFail();

            if ($abstract->status == 2)
              return  $response->withStatus(200)->write('2');

          } catch (\Exception $e) {
            // L'asbtract n'existe pas
            return  $response->withStatus(200)->write('3');
          }

          $upd = AbstractFile::where('id','=',$arg['id'])->update([
            'status'       => 2,
            'updated_by'   => $this->usr['id']
          ]);


          if ($upd) {
            // Envoi du mail
            $data_email = [
              'name'            =>  $abstract['sender_name'],
              'email'           =>  $abstract['sender_email'],
              'message'         =>  'Votre acticle a été désactivé <a href="https://sosecar.com/#" targe="_blank">sosecar.com</a>. <br> <br>Le comité scientifique.'
            ];

            $this->MailSandBox->sendMailAbstract($this , $to = $data_email['email'], $subject = "SOSECAR - Votre acticle a été désactivé.", $data = $data_email);

            return  $response->withStatus(200)->write('1');
          }


          return  $response->withStatus(200)->write('0');

    }

    public function reject($request, $response, $arg)
    {

          try {
            $abstract = AbstractFile::where('id','=',$arg['id'])->firstOrFail();

            if ($abstract->status == -1)
              return  $response->withStatus(200)->write('2');

          } catch (\Exception $e) {
            // L'asbtract n'existe pas
            return  $response->withStatus(200)->write('3');
          }

          $upd = AbstractFile::where('id','=',$arg['id'])->update([
            'status'       => -1,
            'motif_rejet'  => $_POST['motif_rejet'],
            'updated_by'   => $this->usr['id']
          ]);


          if ($upd) {
            // Envoi du mail
            if ($_POST['motif_rejet']) {
              // code...
              $motif_rejet = "Motif de rejet : ".$_POST['motif_rejet'];
            }
            $data_email = [
              'name'            =>  $abstract['sender_name'],
              'email'           =>  $abstract['sender_email'],
              'message'         =>  'Votre acticle n\'a pas été validé. <br> '.$motif_rejet.'<br><br>Le comité scientifique.',
              //'motif_rejet'     =>  $motif_rejet,
            ];

            $this->MailSandBox->sendMailAbstract($this , $to = $data_email['email'], $subject = "SOSECAR - Votre acticle n'a pas été validé.", $data = $data_email);

            return  $response->withStatus(200)->write('1');
          }


          return  $response->withStatus(200)->write('0');

    }


}