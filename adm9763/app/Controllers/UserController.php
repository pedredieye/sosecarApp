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
use App\Helpers\MailSandBox;


class UserController extends Controller
{
    public function index($request, $response, $arg) {

      return $this->view->render($response, 'homeUser.twig', compact('user'));
    }


    public function newSecretaire($request, $response, $arg)
    {
      if(!$this->helper->checkConnexion())
        return $response->withRedirect($this->router->pathFor('login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

      if(!$this->helper->checkPrivillieges($this->usr['role_id'], [1,2])){
        $data = [
          'authorized'   => false,
        ];

        return $this->view->render($response, 'allUser.twig', compact('data'));
      }

      $countries = Country::all();
      $states = State::where('country_id','=',195)->get();
      $hospitals = Hospital::where('id','!=',1)->get();

      $data = [
        'authorized'   => true,
        'countries'    => $countries,
        'states'       => $states,
        'hospitals'    => $hospitals,
      ];

      return $this->view->render($response, 'newUser.twig', compact('data'));
    }

    public function saveSecretaire($request, $response, $arg)
    {
      if(isset($_POST)){
        if(empty($_POST['email']) || empty($_POST['phone']))
          return  $response->withStatus(200)->write('3');
      }
      $pwd_bt = $this->helper->genPwd();
      $pwd = $this->helper->genMdp($pwd_bt);

      $data = [
        'first_name'            =>  $_POST['fname'],
        'last_name'             =>  $_POST['lname'],
        'email'                 =>  filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
        'phone'                 =>  str_replace("221","",str_replace("+221","",str_replace("00221","",$_POST['phone']))),
        'password'              =>  $pwd,
        'role_id'               =>  3,
        'hospital_id'           =>  $_POST['hospital'],
        'created_at'            =>  \date("Y-m-d H:i:s"),
        'updated_at'            =>  \date("Y-m-d H:i:s")
      ];

      $em = User::where('email','=',$data['email'])->first();
      if($em)// L'utilisateur existe déjà
        return  $response->withStatus(200)->write('2');


      $new_user = User::insertGetId($data);

      if ($new_user) {


          $data_email = [
            'pwd'                   =>  $pwd_bt,
            'first_name'            =>  $data['first_name'],
            'last_name'             =>  $data['last_name'],
            'email'                 =>  $data['email'],
            'phone'                 =>  $data['phone'],
            'link'                  =>  $this->domain_url
          ];

          $send_mail = $this->MailSandBox->sendMailUser($this , $to = $data['email'], $subject = "SOSECAR - Votre inscription a été prise en compte.", $data = $data_email);
          if ($send_mail)
            $up = User::where('email','=',$data['email'])->update(['flag_email_after_creation' => 1]);

          return  $response->withStatus(200)->write('1');
      }

      return  $response->withStatus(200)->write('3');

    }

    public function listSecretaire($request, $response, $arg)
    {
      if(!$this->helper->checkConnexion())
        return $response->withRedirect($this->router->pathFor('login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

      if(!$this->helper->checkPrivillieges($this->usr['role_id'], [1,2])){
        $data = [
          'authorized'   => false,
        ];
        return $this->view->render($response, 'allUser.twig', compact('data'));
      }

      $users = User::where('role_id','<=',3)->with('role')->with('validations')->with('scans')->get();



      $data = [
        'authorized'  => true,
        'users'       => $users
      ];


      return $this->view->render($response, 'allUser.twig', compact('data'));
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


    public function newHostess($request, $response, $arg)
    {
      if(!$this->helper->checkConnexion())
        return $response->withRedirect($this->router->pathFor('login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

      if(!$this->helper->checkPrivillieges($this->usr['role_id'], [1,2])){
        $data = [
          'authorized'   => false,
        ];

        return $this->view->render($response, 'allUser.twig', compact('data'));
      }

      $countries = Country::all();
      $states = State::where('country_id','=',195)->get();

      $data = [
        'authorized'   => true,
        'countries'    => $countries,
        'states'       => $states,
      ];

      return $this->view->render($response, 'newHostess.twig', compact('data'));
    }

    public function saveHostess($request, $response, $arg)
    {
      if(isset($_POST)){
        if(empty($_POST['email']) || empty($_POST['phone']))
          return  $response->withStatus(200)->write('3');
      }
      $pwd_bt = $this->helper->genPwd();
      $pwd = $this->helper->genMdp($pwd_bt);

      $data = [
        'first_name'            =>  $_POST['fname'],
        'last_name'             =>  $_POST['lname'],
        'email'                 =>  filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
        'phone'                 =>  str_replace("221","",str_replace("+221","",str_replace("00221","",$_POST['phone']))),
        'password'              =>  $pwd,
        'role_id'               =>  4,
        'created_at'            =>  \date("Y-m-d H:i:s"),
        'updated_at'            =>  \date("Y-m-d H:i:s")
      ];

      $em = User::where('email','=',$data['email'])->first();
      if($em)// L'utilisateur existe déjà
        return  $response->withStatus(200)->write('2');


      $new_user = User::insertGetId($data);

      if ($new_user) {
          $data_email = [
            'pwd'                   =>  $pwd_bt,
            'first_name'            =>  $data['first_name'],
            'last_name'             =>  $data['last_name'],
            'email'                 =>  $data['email'],
            'phone'                 =>  $data['phone'],
            'link'                  =>  $this->domain_url
          ];

          $send_mail = $this->MailSandBox->sendMailHostess($this , $to = $data['email'], $subject = "SOSECAR - Votre compte administrateur a été créé.", $data = $data_email);
          if ($send_mail)
            $up = User::where('email','=',$data['email'])->update(['flag_email_after_creation' => 1]);


          return  $response->withStatus(200)->write('1');
      }

      return  $response->withStatus(200)->write('3');

    }

    public function listHostess($request, $response, $arg)
    {
      if(!$this->helper->checkConnexion())
        return $response->withRedirect($this->router->pathFor('login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

      if(!$this->helper->checkPrivillieges($this->usr['role_id'], [1,2])){
        $data = [
          'authorized'   => false,
        ];
        return $this->view->render($response, 'allUser.twig', compact('data'));
      }

      $users = User::where('role_id','=',4)->with('role')->with('scans')->get();
      $data = [
        'authorized'  => true,
        'users'       => $users
      ];


      return $this->view->render($response, 'allHostess.twig', compact('data'));
    }

    public function setConnected($container, $id, $role, $phone, $loggedWith = 1)
    {
      // usr_rf : User require 'file';eference
      // usr_tp : User Type Account
      // usr_pt : User Parent Id
      session_destroy();
      session_unset();
      session_start();

      setcookie('usr_id', $container->helper->encodeData($container, $id), time() + $container->maxTimeInactivity, $container->baseDir);
      setcookie('usr_role', $container->helper->encodeData($container, $role), time() + $container->maxTimeInactivity, $container->baseDir);
      setcookie('usr_phone', $container->helper->encodeData($container, $phone), time() + $container->maxTimeInactivity, $container->baseDir);
      setcookie('expire', $container->helper->encodeData($container, time() + $container->maxTimeInactivity), time() + $container->maxTimeInactivity, $container->baseDir);

      // Save session data on database
      $data = [
        'user_id'         =>  $id,
        'user_device'     =>  null,
        'user_browser'    =>  null,
        'user_ip'         =>  null,
        'created_at'      =>  \date("Y-m-d H:i:s"),
        'updated_at'      =>  \date("Y-m-d H:i:s")
      ];

      UserSession::insertGetId($data);

    }

    public function showLogin($request, $response, $arg)
    {
      $url_redirect = $_GET['redirect'];
      $pwd = $this->helper->genMdp('1019');
      //$this->helper->debug($pwd);
      return $this->view->render($response, 'login.twig', compact('url_redirect'));
    }

    public function login($request, $response, $arg)
    {
      if(isset($_POST)){

          if(!empty($_POST['inputPhone']) || !empty($_POST['inputPassword'])){
            $usr = User::where('phone',$_POST['inputPhone'])->first();
            if($usr){
              if($usr->password == $this->helper->genMdp($_POST['inputPassword'])){
                // Les accès fournis sont corrects
                self::setConnected($this, $usr->id, $usr->role_id, $usr->phone, 1);
                return  $response->withStatus(200)->write('1');
              }

              // Le mot de passe fourni n'est pas correct
              return  $response->withStatus(200)->write('0');
            }
            // Le numéro fourni n'est lié à aucun compte
            return  $response->withStatus(200)->write('-1');
          }
      }

      return  $response->withStatus(204)->write('-1');

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



}