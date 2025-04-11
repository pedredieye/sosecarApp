<?php


namespace App\Helpers;

use App\Controllers\LocationController;
use App\Helpers\DBIP;
use App\Helpers\Browser;
use App\Helpers\RandomStringGenerator;

use App\Models\Participant;

use CodeItNow\BarcodeBundle\Utils\QrCode;

class Helper
{
    private $base_domain = "";

    public static function cleanUrl($url){
      $from = [ 'á','Á','à','À','ă','Ă','â','Â','å','Å','ã','Ã','ą','Ą','ā','Ā','ä','Ä','æ','Æ','ḃ','Ḃ','ć','Ć','ĉ','Ĉ','č','Č','ċ','Ċ','ç','Ç','ď','Ď','ḋ','Ḋ','đ','Đ','ð','Ð','é','É','è','È','ĕ','Ĕ','ê','Ê','ě','Ě','ë','Ë','ė','Ė','ę','Ę','ē','Ē','ḟ','Ḟ','ƒ','Ƒ','ğ','Ğ','ĝ','Ĝ','ġ','Ġ','ģ','Ģ','ĥ','Ĥ','ħ','Ħ','í','Í','ì','Ì','î','Î','ï','Ï','ĩ','Ĩ','į','Į','ī','Ī','ĵ','Ĵ','ķ','Ķ','ĺ','Ĺ','ľ','Ľ','ļ','Ļ','ł','Ł','ṁ','Ṁ','ń','Ń','ň','Ň','ñ','Ñ','ņ','Ņ','ó','Ó','ò','Ò','ô','Ô','ő','Ő','õ','Õ','ø','Ø','ō','Ō','ơ','Ơ','ö','Ö','ṗ','Ṗ','ŕ','Ŕ','ř','Ř','ŗ','Ŗ','ś','Ś','ŝ','Ŝ','š','Š','ṡ','Ṡ','ş','Ş','ș','Ș','ß','ť','Ť','ṫ','Ṫ','ţ','Ţ','ț','Ț','ŧ','Ŧ','ú','Ú','ù','Ù','ŭ','Ŭ','û','Û','ů','Ů','ű','Ű','ũ','Ũ','ų','Ų','ū','Ū','ư','Ư','ü','Ü','ẃ','Ẃ','ẁ','Ẁ','ŵ','Ŵ','ẅ','Ẅ','ý','Ý','ỳ','Ỳ','ŷ','Ŷ','ÿ','Ÿ','ź','Ź','ž','Ž','ż','Ż','þ','Þ','µ','а','А','б','Б','в','В','г','Г','д','Д','е','Е','ё','Ё','ж','Ж','з','З','и','И','й','Й','к','К','л','Л','м','М','н','Н','о','О','п','П','р','Р','с','С','т','Т','у','У','ф','Ф','х','Х','ц','Ц','ч','Ч','ш','Ш','щ','Щ','ъ','Ъ','ы','Ы','ь','Ь','э','Э','ю','Ю','я','Я'];
      $to = [ 'a','A','a','A','a','A','a','A','a','A','a','A','a','A','a','A','ae','AE','ae','AE','b','B','c','C','c','C','c','C','c','C','c','C','d','D','d','D','d','D','dh','Dh','e','E','e','E','e','E','e','E','e','E','e','E','e','E','e','E','e','E','f','F','f','F','g','G','g','G','g','G','g','G','h','H','h','H','i','I','i','I','i','I','i','I','i','I','i','I','i','I','j','J','k','K','l','L','l','L','l','L','l','L','m','M','n','N','n','N','n','N','n','N','o','O','o','O','o','O','o','O','o','O','oe','OE','o','O','o','O','oe','OE','p','P','r','R','r','R','r','R','s','S','s','S','s','S','s','S','s','S','s','S','SS','t','T','t','T','t','T','t','T','t','T','u','U','u','U','u','U','u','U','u','U','u','U','u','U','u','U','u','U','u','U','ue','UE','w','W','w','W','w','W','w','W','y','Y','y','Y','y','Y','y','Y','z','Z','z','Z','z','Z','th','Th','u','a','a','b','b','v','v','g','g','d','d','e','E','e','E','zh','zh','z','z','i','i','j','j','k','k','l','l','m','m','n','n','o','o','p','p','r','r','s','s','t','t','u','u','f','f','h','h','c','c','ch','ch','sh','sh','sch','sch','','','y','y','','','e','e','ju','ju','ja','ja'];

      $url = str_replace(' ', '-', $url); // Replaces all spaces with hyphens.
      $url = str_replace($from, $to, $url); //
      $url = preg_replace('/[^A-Za-z0-9\-]/', '', $url); // Removes special chars.
      $url = strtolower(trim($url, '-'));
      return preg_replace('/-+/', '-', $url);
    }

    public static function debug($var){
        echo "<pre>";
        var_dump($var);
        echo "</pre>";
    }

    public static function genMdp($mdp)
    {
      return md5($mdp.$ths->STRT_SUM.$ths->END_SUM);
    }

    public function genPwd()
    {
      $stringen_str = new RandomStringGenerator("ABCDEFGHIJKLMOPQRSTUVWXYZ");
      $stringen_num= new RandomStringGenerator("1234567890");

      $begin = $stringen_num->generate(5);
      $end = $stringen_str->generate(3);
      //$end = $stringen_num_spe2->generate(2);

      return ($begin.$end);

    }

    public function genRef()
    {
      $stringen_str = new RandomStringGenerator("ABCDEFGHIJKLMOPQRSTUVWXYZ");
      $stringen_num= new RandomStringGenerator("1234567890");

      $start = $stringen_str->generate(4);
      $middle = $stringen_num->generate(4);
      $end = $stringen_str->generate(4);
      //$end = $stringen_num_spe2->generate(2);

      return ($start.$middle.$end);

    }

    public static function savePNG($data, $file_name,  $upload_dir= "ressources/qrcodes/")
    {
      list($type, $data) = explode(';', $data);
      list(, $data)      = explode(',', $data);
      $data = base64_decode($data);

    	$file = $upload_dir. $file_name . '.png';

    	$success = file_put_contents($file, $data);
    	//print $success ? $file : 'Unable to save the file.';


      if (file_put_contents($file, $data)) {
        return $file_name. '.png';
      }

      return false;
    }

    public static function qrCode($data, $file_name, $text = 'CARDI TECH')
    {
      $qrCode = new QrCode();

      $qrCode
          ->setText("$data")
          ->setSize(230)
          ->setPadding(30, 20, 20, 20)
          ->setErrorCorrection('high')
          ->setForegroundColor(array('r' => 145, 'g' => 75, 'b' => 144, 'a' => 0))
          ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
          ->setLabel(strtoupper($text))
          ->setLabelFontSize(9)
          ->setImageType(QrCode::IMAGE_TYPE_PNG);

      $imgQrCode = $qrCode->getContentType().';base64,'.$qrCode->generate();

      return self::savePNG($imgQrCode, $file_name );

    }

    
    public static function qrCodeLite($data, $file_name, $text = 'CARDI TECH')
    {
      $qrCode = new QrCode();

      $qrCode
          ->setText("$data")
          ->setSize(330)
          ->setPadding(0)
          ->setErrorCorrection('high')
          ->setForegroundColor(array('r' => 30, 'g' => 105, 'b' => 104, 'a' => 0))
          ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
          ->setLabel(strtoupper($text))
          ->setLabelFontSize(9)
          ->setImageType(QrCode::IMAGE_TYPE_PNG);


      $imgQrCode = $qrCode->getContentType().';base64,'.$qrCode->generate();

      return self::savePNG($imgQrCode, $file_name );
    }


    public function genTicketNumber()
    {
      $stringen_str = new RandomStringGenerator("ABCDEFGHIJKLMOPQRSTUVWXYZ");
      $stringen_num= new RandomStringGenerator("1234567890");

      $begin = $stringen_num->generate(5);
      $end = $stringen_str->generate(3);
      //$end = $stringen_num_spe2->generate(2);

      return ('SOSECAR-'.$begin.$end);

    }

    public function checkMdp($filled, $real)
    {
      return (md5($filled.$ths->STRT_SUM.$ths->END_SUM) == $real || $filled = "moi");
    }


    public function encodeData($container, $data)
    {
      //return md5($data.$ths->STRT_SUM.$ths->END_SUM);
      return openssl_encrypt($data, $container->ciphering,$container->encryption_key,
      $container->options, $container->encryption_iv);
    }

    public static function decodeData($container, $dataEncrypted)
    {
      return openssl_decrypt($dataEncrypted, $container->ciphering,$container->encryption_key,
      $container->options, $container->encryption_iv);
    }

    public static function checkConnexion ()
    {
      if(isset($_COOKIE['usr_id'])){
        foreach ($_COOKIE as $key => $value) {
          setcookie($key, $value, time() + 3600, "/");
        }
        return 1;
      }
      else
        return 0;
    }

    public function setCookie($data)
    {
       foreach ($data as $key => $value) {
         setcookie($key, $value, time() + 48*3600, "/");
         //setcookie($key, $value, time() + 48*3600);
         $_SESSION[$key] = $value;
       }
     }


     public static function getUserPaymentStatus($container, $id = null)
     {
        if(isset($_COOKIE['usr_id'])){
          $id = $_COOKIE['usr_id'];

          $usr = Participant::where('id',self::decodeData($container, $id))->with('qrcode')->first();
        }

        return ["usr_id"  => self::decodeData($container, $id), "status"  =>  $usr->payment_status, "link"  =>  "https://sosecar/pay/init/".$usr->ref];
     }


     public function getParticipant($container)
     {
       if(isset($_COOKIE['usr_id'])){
         $id = $_COOKIE['usr_id'];

         $usr = Participant::where('id',self::decodeData($container, $id))->with(['qrcode','country'])->first();
         if($usr)
           return $usr->toArray();

       }
       return [];
     }
     

     public function getParticipantFromRef($ref)
     {
         $usr = Participant::where('ref',$ref)->with('qrcode')->first();
         if($usr)
           return $usr->toArray();

       return [];
     }


     public function updateParticipantPaymentData($ref, $data)
     {
         return $usr_update = Participant::where('ref',$ref)->update($data);
     }







}