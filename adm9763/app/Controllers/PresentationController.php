<?php

namespace App\Controllers;

use App\Helpers\DBIP;
use App\Helpers\Helper;

use App\Helpers\SandBox;
use App\Helpers\RandomStringGenerator;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

use App\Helpers\Browser;
use Bes\Twig\Extension\MobileDetectExtension;
use Psr7Middlewares\Middleware\ClientIp;

use App\Models\User;
use App\Models\Participant;
use App\Models\Country;
use App\Models\State;
use App\Models\Hospital;
use App\Models\UserSession;
use App\Models\Presentation;
use App\Helpers\MailSandBox;


class PresentationController extends Controller
{
    public function index($request, $response, $arg) {

      return $this->view->render($response, 'homeUser.twig', compact('user'));
    }




    public function new($request, $response, $arg)
    {
      if(!$this->helper->checkConnexion())
        return $response->withRedirect($this->router->pathFor('login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

      if(!$this->helper->checkPrivillieges($this->usr['role_id'], [1,2])){
        $data = [
          'authorized'   => false,
        ];

        return $this->view->render($response, 'newPresentation.twig', compact('data'));
      }


      $data = [
        'authorized'   => true,
      ];

      return $this->view->render($response, 'newPresentation.twig', compact('data'));
    }


    public function save($request, $response, $arg)
    {
      if(isset($_POST)){
        if(empty($_POST['title']) || empty($_POST['name']))
          return  $response->withStatus(200)->write('3');
      }

      $upload_data = self::uploadFile($this, $_FILES["file"]);
      //$this->helper->debug($upload_data);

      $flag_upload = $upload_data['status'] ? 1: 0;

      $data = [
        'title'            =>  $_POST['title'],
        'name'             =>  $_POST['name'],
        'subject'          =>  $_POST['subject'],
        'file'             =>  $upload_data['filename'],
      ];

      $em = Presentation::where('title','=',$data['title'])->first();
      if($em)// L'utilisateur existe déjà
        return  $response->withStatus(200)->write('2');




      $new_pre = Presentation::insertGetId($data);

      if ($new_pre) {
          $data_email = [
            'title'           =>  $data['title'],
            'name'            =>  $data['name'],
            'subject'         =>  $data['subject'],
            'file'            =>  $data['file'],
          ];

          //$send_mail = $this->MailSandBox->sendMailHostess($this , $to = $data['email'], $subject = "SOSECAR - Votre compte administrateur a été créé.", $data = $data_email);
          //if ($send_mail)
           // $up = User::where('email','=',$data['email'])->update(['flag_email_after_creation' => 1]);


          return  $response->withStatus(200)->write('1');
      }

      return  $response->withStatus(200)->write('3');

    }


     public static function uploadFile($container, $file, $directory = "presentations/")
    {

      if (isset($file) && $file['error'] === UPLOAD_ERR_OK)
      {
        // get details of the uploaded file
        $fileTmpPath = $file['tmp_name'];
        $fileName = $file['name'];
        $fileSize = $file['size'];
        $fileType = $file['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // sanitize file-name
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

        // check if file has one of the following extensions
        $allowedfileExtensions = array('doc', 'docx', 'pdf');
        $status = 0;
        if (in_array($fileExtension, $allowedfileExtensions))
        {
          // directory in which the uploaded file will be moved
          $uploadFileDir = '../uploads/'.$directory;
          $dest_path = $uploadFileDir . $newFileName;

          if(move_uploaded_file($fileTmpPath, $dest_path))
          {
            $message ='File is successfully uploaded.';
            $status = 1;
          }
          else
          {
            $message = 'There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.';
          }
        }
        else
        {
          $message = 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
        }
      }
      else
      {
        $message = 'There is some error in the file upload. Please check the following error.<br>';
        $message .= 'Error:' . $file['error'];
      }

      return ['status' => $status, 'filename' => $newFileName, 'message' => $message];


    }



    public function list($request, $response, $arg)
    {
      if(!$this->helper->checkConnexion())
        return $response->withRedirect($this->router->pathFor('login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

      if(!$this->helper->checkPrivillieges($this->usr['role_id'], [1,2])){
        $data = [
          'authorized'   => false,
        ];
        return $this->view->render($response, 'allPresentation.twig', compact('data'));
      }

      $presentations = Presentation::all();
      $data = [
        'authorized'      => true,
        'presentations'   => $presentations
      ];


      return $this->view->render($response, 'allPresentation.twig', compact('data'));
    }

    public function disable($request, $response, $arg)
    {

        $status = $arg['status'] == 1 ? 0 : 1;
          try {
            $presentation = Presentation::where('id','=',$arg['id'])->firstOrFail();

            if ($presentation->status == $status)
              return  $response->withStatus(200)->write('2');

          } catch (\Exception $e) {
            // L'asbtract n'existe pas
            return  $response->withStatus(200)->write('3');
          }

          $upd = Presentation::where('id','=',$arg['id'])->update([
            'status'       => $status
          ]);


          if ($upd) {
            // Envoi du mail
            $data_email = [
              'name'            =>  $presentation['name'],
              'title'           =>  $abstract['title'],
              'message'         =>  'Le status de la présentation a été mis à jour <a href="https://sosecar.com/#" targe="_blank">sosecar.com</a>. <br> <br>Le comité scientifique.'
            ];

            //$this->MailSandBox->sendMailAbstract($this , $to = $data_email['email'], $subject = "SOSECAR - Votre acticle a été désactivé.", $data = $data_email);

            return  $response->withStatus(200)->write('1');
          }


          return  $response->withStatus(200)->write('0');
    }


    public function showSecretaire($request, $response, $arg)
    {
        if(!$this->helper->checkConnexion())
          return $response->withRedirect($this->router->pathFor('login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

        if(!$this->helper->checkPrivillieges($this->usr['role_id'], [1,2])){
          $data = [
            'authorized'   => false,
          ];
          return $this->view->render($response, 'showUser.twig', compact('data'));
        }

        return $this->view->render($response, 'showUser.twig', compact('data'));
    }



}
