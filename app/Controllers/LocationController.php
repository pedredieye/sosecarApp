<?php

namespace App\Controllers;

use App\Helpers\DBIP;
use App\Helpers\Helper;

use App\Helpers\SandBox;
use App\Helpers\RandomStringGenerator;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

use App\Helpers\Browser;
use App\Models\SaUser;

use Bes\Twig\Extension\MobileDetectExtension;
use Psr7Middlewares\Middleware\ClientIp;

class LocationController extends Controller
{
    public function index($request, $response, $arg) {

      exit();
      return $this->view->render($response, 'homePublic.twig', compact('courses'));
    }


    public static function getIpInfoFromipdata($container)
    {
      $ip = $_SERVER["REMOTE_ADDR"];
      $key = $container->ip_data_api_key;

      if(isset($_COOKIE['ipdata'])){
        $ipdata = json_decode($container->helper->saDecodeData($container, $_COOKIE['ipdata']), true);
      }
      else {
        $ipdata = self::ipdata($ip, $key);
        setcookie('ipdata', $container->helper->saEncodeData($container, json_encode($ipdata)), time() + $container->minTimeValidityIp, $container->baseDir);
      }

      return $ipdata;
    }

    public static function getUserCountry($container)
    {
        $ipdata = self::getIpInfoFromipdata($container);

        return $ipdata['country_code'];
    }

    public static function ifUserAuthorized($container)
    {
      return true;

      if(in_array(self::getUserCountry($container), $container->authorizedCountry))
        return true;

      return false;
    }
    public static function test()
    {
      // code...
      echo "Ok";
    }




    public static function ipdata($ip, $key){
       $curl = curl_init();

       $url = "https://api.ipdata.co/$ip?api-key=$key";
       // OPTIONS:
       curl_setopt($curl, CURLOPT_URL, $url);
       curl_setopt($curl, CURLOPT_HTTPHEADER, array(
          "Content-Type: application/json"
       ));
       curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
       // EXECUTE:
       $result = curl_exec($curl);
       if(!$result){die("Connection Failure");}
       curl_close($curl);
       return json_decode($result, true);
    }

    // DBIP

    public function ip($request, $response, $arg)
    {
      // code...
      $ip = $_SERVER["REMOTE_ADDR"];
      $this->helper->debug($ip);
      $addrInfo = DBIP::lookup($ip);

    }


    public function importBDIP($request, $response, $arg)
    {

      //require "dbip.class.php";

     //  $opts = getopt("f:d:t:b:u:p:");

      $opts = [
        "f"   =>  $this->domain_url."/uploads/dbip-city-lite-2020-07.csv",// filenename
        "d"   =>  "country-lite",// filenename
        "t"   =>  "dbip_lookup_lite",// table
        "b"   =>  "sa",// dbname
        "u"   =>  "sa_user",// username
        "p"   =>  'V2GDjh_e)j_$d'// password
      ];

      $this->helper->debug($opts);

      $filename = @$opts["f"];
      $type = @$opts["d"];

      $dbname = @$opts["b"]
      or $dbname = "dbip";

      $table = @$opts["t"]
      or $table = "dbip_lookup";

      $username = @$opts["u"]
      or $username = "root";

      $password = @$opts["p"]
      or $password = "";


      if (!isset($type) && preg_match('/dbip-(country-lite|city-lite|country|location|isp|full)/i', $filename, $res)) {
        $type = $res[1];
      }

      if (!isset($filename) || !isset($type)) {
        die("usage: {$argv[0]} -f <filename.csv[.gz]> [-d <country-lite|city-lite|country|location|isp|full>] [-b <database_name>] [-t <table_name>] [-u <username>] [-p <password>]\n");
      }

      switch (strtolower($type)) {
        case "country-lite":	$dbtype = DBIP::TYPE_COUNTRY_LITE;	break;
        case "city-lite":		$dbtype = DBIP::TYPE_CITY_LITE;		break;
        case "country":			$dbtype = DBIP::TYPE_COUNTRY;		break;
        case "location":		$dbtype = DBIP::TYPE_LOCATION;		break;
        case "isp":				$dbtype = DBIP::TYPE_ISP;			break;
        case "full":			$dbtype = DBIP::TYPE_FULL;			break;
        default:				echo "invalid database type\n"; exit(1);
      }


      try {
          // Connect to the database
          $this->helper->debug($dbname);
          $this->helper->debug($username);
          $this->helper->debug($password);
              $db = new \PDO("mysql:host=localhost;dbname={$dbname};charset=utf8mb4", $username, $password);
              // Alternatively connect to MySQL using the old interface
              // Comment the PDO statement above and uncomment the mysql_ calls
              // below if your PHP installation doesn't support PDO :
              // $db = mysql_connect("localhost", $username, $password);
              // mysql_select_db($dbname, $db);
              $this->helper->debug($db);

              // Instanciate a new DBIP object with the database connection
              $dbip = new DBIP($db);
              // Alternatively instanciate a DBIP_MySQL object
              // Comment the new statement above and uncomment below if your PHP
              // installation doesn't support PDO :
              // $dbip = new DBIPMySQL($db);

        $nrecs = $dbip->importFromCsv($filename, $dbtype, $table, function($progress) {
          echo "\r{$progress} ...";
        });
        echo "\rfinished importing " . number_format($nrecs) . " records\n";
      } catch (DBIPException $e) {
        echo "error: {$e->getMessage()}\n";
      }
    }

}
