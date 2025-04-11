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
use App\Models\AbstractFile;

use Bes\Twig\Extension\MobileDetectExtension;
use Psr7Middlewares\Middleware\ClientIp;

use CodeItNow\BarcodeBundle\Utils\QrCode;

//use chillerlan\QRCode\{QRCode, QROptions};

//use chillerlan\QRCode;
//use chillerlan\QROptions;

class AbstractController extends Controller
{


    public function new($request, $response, $arg)
    {
       //echo php_ini_loaded_file();
      //exit;
      $countries = Country::all();
      $states = State::where('country_id','=',195)->get();

      $data = [
        'countries'   => $countries,
        'states'      => $states,
        'jobs'        => $jobs,
        'titles'      => $titles,
        'page_title'  => "Ajouter un abstract"
      ];

      return $this->view->render($response, 'newAbstract.twig', compact('data'));

    }


    public function save($request, $response, $arg)
    {

      if(isset($_POST)){

        // Récupérer les données soumises par le formulaire
        $formData = [];

        // Récupérer tous les champs de texte
        foreach ($_POST as $key => $value) {
            $formData[$key] = $value;
        }

        // Enregistrer les données dans le fichier log
        self::logFormData($formData);
        
        if(empty($_POST['address_email_sender']))
          return  $response->withStatus(200)->write('3');
      }


      $upload_data = self::uploadFile($this, $_FILES["file"]);
     
        
      //$this->helper->debug($upload_data);

      $flag_upload = $upload_data['status'] ? 1: 0;
      if($flag_upload == 0)
        return  $response->withStatus(200)->write('3');
        
      
      $resume = trim($_POST['resume']);  // Enlever les espaces avant et après
      $resume = htmlspecialchars($resume, ENT_QUOTES, 'UTF-8');  // Convertir les caractères spéciaux en entités HTML

      $data = [
        'title'                   =>  $_POST['abs_title'],
        'authors'                 =>  $_POST['authors'],
        'address'                 =>  $_POST['address'],
        //'resume'                  =>  $resume,
        'file'                    =>  $upload_data['filename'],
        'flag_upload_file'        =>  $flag_upload,
        'sender_title'            =>  $_POST['sender_title'],
        'sender_name'             =>  $_POST['name_sender'],
        'sender_address'          =>  $_POST['address_sender'],
        'sender_email'            =>  filter_var($_POST['address_email_sender'], FILTER_SANITIZE_EMAIL),
      ];

      $em = AbstractFile::where('title','=',$data['title'])->first();
      if($em)
        return  $response->withStatus(200)->write('2');

      $new_abstract = AbstractFile::insertGetId($data);

      if ($new_abstract) {

          $data_email = [
            'name'            =>  $data['sender_name'],
            'email'           =>  $data['sender_email'],
          ];

          $this->MailSandBox->sendMailAbstract($this , $to = $data_email['email'], $subject = "SOSECAR - Votre article a été bien reçu.", $data = $data_email);
          return  $response->withStatus(200)->write('1');
      }
      return  $response->withStatus(200)->write('3');
    }


    public static function uploadFile($container, $file, $directory = "abstracts/")
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
          $uploadFileDir = './uploads/'.$directory;
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




    // Fonction pour enregistrer les données dans un fichier log
    public static function logFormData($formData) {
      // Définir le chemin du fichier log (assurez-vous que ce dossier existe et que PHP peut y écrire)
      $logFile = './logs/form_submission.log';

      // Obtenez l'heure actuelle au format Y-m-d H:i:s
      $timestamp = date("Y-m-d H:i:s");

      // Commencez à construire la chaîne du log
      $logEntry = "Timestamp: $timestamp\n";

      // Parcourir toutes les données du formulaire et les ajouter au log
      foreach ($formData as $key => $value) {
          if (is_array($value)) {
              $logEntry .= "$key: " . implode(", ", $value) . "\n";  // Pour gérer plusieurs valeurs dans un même champ (ex. checkboxes)
          } else {
              $logEntry .= "$key: $value\n";
          }
      }

      // Si un fichier a été téléchargé, enregistrez son nom et son chemin temporaire
      if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
          $logEntry .= "Fichier téléchargé: " . $_FILES['file']['name'] . "\n";
          $logEntry .= "Chemin temporaire: " . $_FILES['file']['tmp_name'] . "\n";
      } else {
          $logEntry .= "Aucun fichier téléchargé.\n";
      }

      // Ajoutez une ligne vide pour séparer les entrées
      $logEntry .= str_repeat("-", 50) . "\n";

      // Ouvrir le fichier log en mode append (ajouter à la fin du fichier)
      file_put_contents($logFile, $logEntry, FILE_APPEND);
    }
}