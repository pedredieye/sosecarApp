<?php

use App\Controllers\ConnectController;
use App\Controllers\HomeController;
use App\Controllers\LocationController;
use App\Controllers\ParticipantController;
use App\Controllers\UserController;
use App\Controllers\AbstractController;
use App\Controllers\PresentationController;

use App\Controllers\PaiementController;


use App\Helpers\Helper;
use App\Helpers\MailSandBox;
use Bes\Twig\Extension\MobileDetectExtension;
use Facebook\Facebook;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;

use Psr7Middlewares\Middleware;
use Psr7Middlewares\Middleware\EncodingNegotiator;
use Psr7Middlewares\Middleware\Gzip;
use Psr7Middlewares\Middleware\TrailingSlash;
use Psr7Middlewares\Transformers\Minifier;

use Slim\App;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

session_start();
// BD CONST


define('BD_HOSTNAME', 'localhost');
define('BD_USERNAME', 'bduser');
define('BD_PASSWORD', 'GddPUSqH89$');
define('BD_DB_NAME',  'sosecar_2024');


define('FB_SECRET_KEY', 'efe');
define('FB_APP_ID', 'ezze');
define('SERVER_DOMAIN', '');
define('STRT_SUM', '4eb895');
define('END_SUM', 'c6a81e');
define('CART_COOKIE_NAME', 'usr_cart_tk');
define('CART_COOKIE_IDS_NAME', 'usr_cart_ids_tk');
define('WISHLIST_COOKIE_IDS_NAME', 'usr_wishlist_ids_tk');
define('NEWSLETTER_ON_COOKIE_NAME', 'usr_newsletter');

define('WHATSAPP_LINK', 'https://api.whatsapp.com/send?phone=221p773386425&text=');

define('QRCODE_REPERTORY','../ressources/qrcodes/');
define('PARTICIPANT_LOGIN_LINK','/participant/login');
//define('notifMailAdress','contact@sosecar.com');
//define('notifMailAdress','mailtestify@sunumecano.com');


define('notifMailAdress','sosecar@pedhel.com');
define('notifMailAdressPwd','GddPUSqH89$');
/*
define('API_RAV_publicKey_LIVE', 'FLWPUBK-343ee9daa706116ca0166f593ab016fa-X');
define('API_RAV_secretKey_LIVE', 'FLWSECK-da154b47e44aec3e284a4bc34dacff17-X');
define('API_RAV_encryptKey_LIVE', 'da154b47e44a0edda123a1b1');

define('API_RAV_publicKey_TEST', 'FLWPUBK_TEST-0f5b47f2a4a4e9b84a11f4f30f409bdc-X');
define('API_RAV_secretKey_TEST', 'FLWSECK_TEST-9d5cec8b85b469b297a0a8d5f7c1e1ea-X');
define('API_RAV_encryptKey_TEST', 'FLWSECK_TEST7c27e52f70e3');
*/



define('DEMIO_API_KEY', 'cjV9E26ft1uNEc9apVXkSvrbDYnt2u41');
define('DEMIO_API_SECRET', 'ABFsV2zpqAPbU3sU');
define('IP_DATA_API_KEY', 'c82bce1fd7f2d6a81891ea7ddd5b6801627c78c9f4f3efbbc0d52c03');



//define('BASE_DIR', '/admin9763/');
define('BASE_DIR', '/');
//define('LINK_HOME','/home');
define('LINK_HOME','');
define('LINK_HOME_USER',LINK_HOME.'');
define('LINK_HOME_STUDENT',LINK_HOME.'');
define('PAYMENT_CALLBACK_URL','/payment');



define('PAYDUNYA_PUBLIC_KEY_TEST','test_public_oIy6CSjG3CYAnp0kTVi7V4uT42E');
define('PAYDUNYA_PRIVATE_KEY_TEST','test_private_kf0BgQ573zrtDK4sxzBorz2QfLb');
define('PAYDUNYA_TOKEN_KEY_TEST','dDrS3uKDEqYuhhfFAsqe');

define('PAYDUNYA_PUBLIC_KEY_LIVE','live_public_HZS8jVaw4TPMXZE2IqwiyktJ5aJ');
define('PAYDUNYA_PRIVATE_KEY_LIVE','live_private_PYPOS3nxTcxZk3LRY0xeyG3iEYp');
define('PAYDUNYA_TOKEN_KEY_LIVE','QbEtV7P1tPPPWcU89Snp');

define('PAYDUNYA_MAIN_KEY','YYTMYkyz-Ex4w-B6X2-XIXd-4fF4YC8QFT10');
define('PAYDUNYA_CALLBACK_URL','/pay/callback');
define('PAYDUNYA_CANCEL_URL','/pay/cancelled');
define('PAYDUNYA_RETURN_URL','/pay/return');



define('PAYMENT_STATUS',['started' => 'En attente','cancelled' => 'Annulé','completed' => 'Payé','failed' => 'Echoué']);
define('PAYMENT_STATUS_CLAAS',['started' => 'En attente','cancelled' => 'Annulé','completed' => 'Payé','failed' => 'Echoué']);


// Store the cipher method
define('SA_CIPHERING','AES-128-CTR');
define('CIPHERING','AES-128-CTR');

// Use OpenSSl Encryption method
define('SA_IV_LENGTH',openssl_cipher_iv_length(CIPHERING));
define('SA_ENCRYPTION_OPTION',0);

// Non-NULL Initialization Vector for encryption
define('SA_ENCRYPTION_IV','1234567891011121');

// Store the encryption key
define('SA_ENCRYPTION_KEY','ditakhmaadsoumpthiere');


define('FLAG_FOLDER','/src/img/flag/');

define('IMAGE_FOLDER','/src/images/products/');
define('BRAND_FOLDER','/src/images/products/');

require __DIR__ . '/../vendor/autoload.php';

$config = [
    'displayErrorDetails'     =>  true,
    'db'                      =>  [
                                  'driver'    =>  'mysql',
                                  'host'      =>  BD_HOSTNAME,
                                  'database'  =>  BD_DB_NAME,
                                  'username'  =>  BD_USERNAME,
                                  'password'  =>  BD_PASSWORD,
                                  'charset'   =>  'utf8mb4',
                                  'collation' =>  'utf8mb4_unicode_ci',
                                  'prefix'    =>  '',
                              ],
    'determineRouteBeforeAppMiddleware' => true,
];

date_default_timezone_set('Etc/GMT');

$app = new App(["settings" => $config]);

$checkProxyHeaders = true; // Note: Never trust the IP address for security processes!
$trustedProxies = ['10.0.0.1', '10.0.0.2']; // Note: Never trust the IP address for security processes!
$app->add(new \RKA\Middleware\IpAddress($checkProxyHeaders, $trustedProxies))
    ->add(new TrailingSlash(false))
    ->add(new Middleware\ClientIp())
    ->add(function ($request, $response, $next) use($app){ // Check if user is authorized to get in from his country
      $container = $app->getContainer();
      if(!$container->LocationController->ifUserAuthorized($container)){
        $route = $request->getAttribute('route');
        $name = $route->getName();
        if($name != 'home_maintenance')
         return $response->withRedirect($this->router->pathFor('home_maintenance'),302);
      }

      //Temporary to fix cart bug
      //$container->helper->initCartCookie($container);

      $response = $next($request, $response);
      return $response;
    });

    //\Psr\Http\Message\ResponseInterface

$container = $app->getContainer();

$capsule = new Capsule;
$capsule->addConnection($config['db']);
$capsule->setEventDispatcher(new Dispatcher(new Container));
$capsule->setAsGlobal();
$capsule->bootEloquent();



$container['flash'] = function ($container) {
    return new Messages;
};


// Ajouter les données de l'utilisateur connecté
$container['data_user'] = function($container){
    // Si l'utilisateur est connecté
    return $container->helper->getUsr($container);
};

$domaine_url = str_replace( 'http://', 'http://', $container->request->getUri()->getBaseUrl());
$uri = str_replace( 'http://', 'http://', $container->request->getUri());


$container['view'] = function ($container){
    $view = new Twig(__DIR__ . '/../ressources/views', [
        'cache'   =>  false,
    ]);

    $view->addExtension(new TwigExtension(
        $container->router,
        $container->request->getUri()
    ));
    $view->addExtension(new MobileDetectExtension());

    $view->addExtension(new \Twig\Extension\DebugExtension());


    $twigCleanUrl = new Twig_SimpleFilter('twig_clean_url', function ($data){
        return Helper::cleanUrl($data);
    });

    $dateInFrench = new Twig_SimpleFilter('date_in_french', function ($date, $format){
        return Helper::dateInFrench($date, $format);
    });

    $moneyFormat = new Twig_SimpleFilter('money_format', function ($money){
      return  number_format($money, 2, '.', ' ');
    });


    $view->getEnvironment()->addFilter(new Twig_SimpleFilter('date("d/m/Y")', 'date("d/m/Y")'));

    // Adding created custom filter to twig envirnment
    $view->getEnvironment()->addFilter($twigCleanUrl);
    $view->getEnvironment()->addFilter($dateInFrench);
    $view->getEnvironment()->addFilter($moneyFormat);
    $view->getEnvironment()->addGlobal('flash', $container->flash);
    $view->getEnvironment()->addGlobal('session', $_SESSION);
    $view->getEnvironment()->addGlobal('cookie', $_COOKIE);
    //$view->getEnvironment()->addGlobal('domain_url', $_SERVER['HTTP_HOST']);
    $view->getEnvironment()->addGlobal('defined_base_url', "https://".SERVER_DOMAIN);
    $view->getEnvironment()->addGlobal('defined_base_domain', SERVER_DOMAIN);

    $view->getEnvironment()->addGlobal('fb_app_id', FB_APP_ID);
    $domaine_url = str_replace( 'http://', 'http://', $container->request->getUri()->getBaseUrl());
    $domaine_url = $container->request->getUri()->getScheme().'://'.
    $container->request->getUri()->getHost().
    $container->request->getUri()->getBasePath();

    $uri = str_replace( 'http://', 'http://', $container->request->getUri());
    $view->getEnvironment()->addGlobal('get', $_GET);
    $view->getEnvironment()->addGlobal('domain_url', $domaine_url);
    $view->getEnvironment()->addGlobal('uri', $uri);

    $view->getEnvironment()->addGlobal('whatsapp_link', WHATSAPP_LINK);

    $view->getEnvironment()->addGlobal('LINK_HOME', LINK_HOME);
    $view->getEnvironment()->addGlobal('LINK_HOME_USER', LINK_HOME_USER);
    $view->getEnvironment()->addGlobal('LINK_HOME_STUDENT', LINK_HOME_STUDENT);
    $view->getEnvironment()->addGlobal('FLAG_FOLDER', FLAG_FOLDER);
    $view->getEnvironment()->addGlobal('baseDir', BASE_DIR);
    $view->getEnvironment()->addGlobal('qrcode_repertory', QRCODE_REPERTORY);
    $view->getEnvironment()->addGlobal('participant_login_link', PARTICIPANT_LOGIN_LINK);

    $view->getEnvironment()->addGlobal('usr', $container->helper->getUsr($container));


    $view->getEnvironment()->addGlobal('image_folder', "/src/images/products/");
    $view->getEnvironment()->addGlobal('brand_folder', "/src/images/brands/");
    $view->getEnvironment()->addGlobal('request_uri', $container->request->getUri()->getQuery());
    $view->getEnvironment()->addGlobal('payment_status', PAYMENT_STATUS);


    return $view;
};
//Domain App
$container['base_domain'] = SERVER_DOMAIN;
$container['image_folder'] = IMAGE_FOLDER;
$container['brand_folder'] = BRAND_FOLDER;
$container['whatsapp_link'] = WHATSAPP_LINK;
//$container['beginMd5'] = 'znekd';
//$container['endMd5'] = 'foorn';
$container['STRT_SUM'] = STRT_SUM;
$container['END_SUM'] = END_SUM;
$container['maxTimeInactivity'] = 3600 * 24; //seconds
$container['minTimeInactivityOption'] = 30*24*3600; //seconds
$container['minTimeValidityIp'] = 2*24*3600; //seconds
$container['baseDir'] = BASE_DIR;
$container['domain_url'] = str_replace( 'http://', 'http://', $container->request->getUri()->getBaseUrl());;
$container['redirect_url_after_login'] = $container->request->getUri()->getPath();

$container['ciphering'] = SA_CIPHERING;
$container['iv_length'] = SA_IV_LENGTH;
$container['options'] = SA_ENCRYPTION_OPTION;
$container['encryption_iv'] = SA_ENCRYPTION_IV;
$container['encryption_key'] = SA_ENCRYPTION_KEY;


$container['authorizedCountry'] = ['SN'];
$container['qrcode_repertory'] = QRCODE_REPERTORY;
$container['participant_login_link'] = PARTICIPANT_LOGIN_LINK;
$container['notifMailAdress'] = notifMailAdress;
$container['notifMailAdressPwd'] = notifMailAdressPwd;

$container['usr'] = function($container){
    // Si l'utilisateur est connecté
    return $container->helper->getUsr($container);
};





$paydunyaMode = "live";

$container['pd_publicKey'] = $paydunyaMode === 'test' ? PAYDUNYA_PUBLIC_KEY_TEST : PAYDUNYA_PUBLIC_KEY_LIVE;
$container['pd_privateKey'] = $paydunyaMode === 'test' ? PAYDUNYA_PRIVATE_KEY_TEST : PAYDUNYA_PRIVATE_KEY_LIVE;
$container['pd_token'] = $paydunyaMode === 'test' ? PAYDUNYA_TOKEN_KEY_TEST : PAYDUNYA_TOKEN_KEY_LIVE;
$container['pd_mainKey'] = PAYDUNYA_MAIN_KEY;

$container['pd_callbackUrl'] = PAYDUNYA_CALLBACK_URL;
$container['pd_returnUrl'] = PAYDUNYA_RETURN_URL;
$container['pd_cancelUrl'] = PAYDUNYA_CANCEL_URL;





//Facebook app config
$container['fb'] = function($container){
    return new Facebook([
        'app_id' => FB_APP_ID,
        'app_secret' => FB_SECRET_KEY,
        'default_graph_version' => 'v2.5',
    ]);
  };




$container['request_uri'] = function ($container) {
    return $container->request->getUri()->getQuery();
};
$container['HomeController'] = function ($container) {
    return new HomeController($container);
};
$container['UserController'] = function ($container) {
    return new UserController($container);
};
$container['ParticipantController'] = function ($container) {
    return new ParticipantController($container);
};
$container['LocationController'] = function ($container) {
    return new LocationController($container);
};
$container['AbstractController'] = function ($container) {
    return new AbstractController($container);
};
$container['PresentationController'] = function ($container) {
    return new PresentationController($container);
};
$container['PaiementController'] = function ($container) {
    return new PaiementController($container);
};
$container['ConnectController'] = function ($container) {
    return new ConnectController($container);
};
$container['MailSandBox'] = function ($container){
    return new MailSandBox($container);
};
$container['helper'] = function ($container){
    return new Helper($container);
};
$container['notFoundHandler'] = function($container)
{
    return function($request, $response) use ($container)
    {


      // Ensemble des données statiques traduites
      //$cms_content = $container->helper->getCmsContent($container, $lang_id);

      $data = [
        'cms_content'         =>  $cms_content,
      ];

        return $container['view']->render($response->withStatus(404), 'errors/404.twig', compact('data'));
    };
};

require_once __DIR__ . '/../app/routes.php';