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

use App\Models\SaUser;
use App\Models\SaNewsletterEmail;
use App\Models\SaDemioRegistration;
use App\Models\SaUserSession;

use App\Controllers\DemioController;

class UserController extends Controller
{
    public function index($request, $response, $arg) {

      return $this->view->render($response, 'homeUser.twig', compact('user'));
    }





}
