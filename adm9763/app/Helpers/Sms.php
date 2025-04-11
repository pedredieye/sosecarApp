<?php


namespace App\Helpers;

use App\Controllers\LocationController;
use App\Helpers\DBIP;
use App\Helpers\Browser;
use App\Helpers\RandomStringGenerator;

use CodeItNow\BarcodeBundle\Utils\QrCode;

class Sms
{
  /**



  * Permet de parser l'url envoye



  * @param $url à parser



  * @return tableau contenant les paramètres



  **/





  static $auth_tel = "781528375";

  static $url = "https://api.orange.com/smsmessaging/v1/outbound/tel%3A%2B221781528375/requests";
//  static $url = "https://api.orange.com/oauth/v3/token";
    //https://api.orange.com/smsmessaging/v1/outbound/tel%3A%2B


  static $client_id = null;

  static $client_secret = null;



  static $sms_header = "SOSECAR";



  static $api_info = [

    'access_token'=> '',

    'token_type'=>''

  ];










  static function getTokensFromApi()

  {


           //Helpers::dump(['gettokenfromApi'=>$data_client]);

          //REQUETE  CURL  VERS ORANGE

        $client_id     = self::$client_id;

        $client_secret = self::$client_secret ;

        $str = $client_id.":".$client_secret;


        $str =base64_encode($str);



         $url ="https://api.orange.com/oauth/v3/token";

         $request_headers[] = 'Authorization: Basic '.$str;

         $d = array();

         $d['grant_type'] = "client_credentials";

         $ch = curl_init();



                  curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

                  curl_setopt($ch, CURLOPT_URL, $url);

                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                  curl_setopt($ch, CURLOPT_POST, 1);

                  curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

                 $result = curl_exec($ch);

                 $result = json_decode($result,true);



         curl_close($ch);



        if (!isset($result['access_token']))

        {

          //var_dump($this->request->data);

          $result['access_token'] ='must be remove online';

          $result['token_type'] = 'Bearer';

          $result['expires_in'] = 125;

        }else{

          self::$api_info['access_token'] = $result['access_token'];
          self::$api_info['token_type']   = $result['token_type'];

        }



     return $result;

 }


  static function sendSms($tel,$message){



    $data = [

      "outboundSMSMessageRequest"=> [
              "address"=>"tel:+221".$tel,
              "outboundSMSTextMessage"=> ["message"=> "".$message],
              "senderAddress"=>"tel:+221".self::$auth_tel,
              "senderName"=> "".self::$sms_header

             ]
      ];
    //Helpers::dump(['dump' => $data]);

      $header = [];

      $header[] = "Authorization: ".self::$api_info['token_type'].' '.self::$api_info['access_token'];
      $header[] = "Content-Type: application/json";
      $header[] = "Accept: application/json";

      $sms_send = curl_init();

      curl_setopt($sms_send, CURLOPT_HTTPHEADER, $header);
      curl_setopt($sms_send, CURLOPT_URL, self::$url);
      curl_setopt($sms_send, CURLOPT_RETURNTRANSFER,1);
      curl_setopt($sms_send, CURLOPT_POSTFIELDS, json_encode($data));

      $send  =  curl_exec($sms_send);
      $send  = json_decode($send,true);
      return $send;
  }



  static $endpoint =  "https://gateway.intechsms.sn/api/send-sms";


  static function send($tel,$message)
  {
    # code...
    /*

    {
      "app_key":"63E236F1695DE63E236F1695DF",
      "sender":"SOSECAR",
      "content":"Ceci est un test",
      "msisdn":[
          "+221781528375",
          "+221773733331"
      ]
    }

    */

    $data = [
        "app_key"   =>  "63E236F1695DE63E236F1695DF",
        "sender"    =>  "SOSECAR",
        "content"   =>  "$message",
        "msisdn"    =>  [
          //"+221781528375",
          "+221".$tel
        ]
      ];




    $header = [];

    $header[] = "Authorization: ".self::$api_info['token_type'].' '.self::$api_info['access_token'];
    $header[] = "Content-Type: application/json";
    $header[] = "Accept: application/json";

    $sms_send = curl_init();

    curl_setopt($sms_send, CURLOPT_HTTPHEADER, $header);
    curl_setopt($sms_send, CURLOPT_URL, self::$endpoint);
    curl_setopt($sms_send, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($sms_send, CURLOPT_POSTFIELDS, json_encode($data));

    $send  =  curl_exec($sms_send);
    $send  = json_decode($send,true);
    return $send;



  }



}