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
use App\Models\User;
use App\Models\Participant;
use App\Models\AteliersParticipant;
use App\Models\Job;
use App\Models\Title;
use App\Models\PQrCode;
use App\Models\Session;
use App\Models\Scan;
use App\Models\Salle;
use App\Models\Attestation;
use App\Models\AbstractFile;

use Bes\Twig\Extension\MobileDetectExtension;
use Psr7Middlewares\Middleware\ClientIp;

use CodeItNow\BarcodeBundle\Utils\QrCode;

use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\StreamReader;

use Illuminate\Support\Facades\DB;

use Slim\Psr7\Factory\StreamFactory;
use Nyholm\Psr7\Stream;


//use chillerlan\QRCode\{QRCode, QROptions};

//use chillerlan\QRCode;
//use chillerlan\QROptions;

class HomeController extends Controller
{
    public function indexPublic($request, $response, $arg) {

      return $this->view->render($response, 'homePublic.twig', compact('courses'));
    }

    public function index($request, $response, $arg) {
      if(!$this->helper->checkConnexion())
        return $response->withRedirect($this->router->pathFor('login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

      if(!$this->helper->checkPrivillieges($this->usr['role_id'], [1,2])){
        $data = [
          'authorized'   => false,
        ];
        return $this->view->render($response, 'home.twig', compact('data'));
      }



      if ($_GET['ad'] || 0 == 0) {

        // Obtention de statistiques
        $start_day = "2023-12-11";
        $end_day = date("Y-m-d");
        $days = $this->helper->getDatesFromRange($start_day, $end_day);
        //$participants = Participant::where([['created_at','>=', $start], ['created_at','<=', $end]])->get();
        $all = 0;
        $stats_days = [];
        $stats = [];
        $last_subscription = null;
        $last_subscription_web = null;
        $last_subscription_admin = null;
        $all_from_website = 0;
        $all_from_admin = 0;

        $total_certificate_download = 0;
        $total_certificate_download_yesterday = 0;
        $total_certificate_download_today = 0;
        $last_certificate_download = null;


        $arr_from_website = [];
        $arr_from_admin = [];
        $valide_participant = 0;
        $total_scan = 0;
        $total_scan_today = 0;
        $total_scan_yesterday = 0;
        $salles_stats = [
          'pleniere' => 0,
          'atelier' => 0,
        ];

        $sessions_stats = [];
        $users_stats = [];

        $sessions = Session::all();
        $users = User::where("role_id",'=',4)->get();


        foreach($sessions as $session) {
          $sessions_stats[] = [
            'id'          => $session->id,
            'title'       => $session->title,
            'date'        => date("d/m/Y", strtotime($session->start)),
            'heure'       => date("H:i:s", strtotime($session->start)),
            'scans'       => 0,
          ];
        }

        foreach($users as $user) {
          $users_stats[] = [
            'id'          => $user->id,
            'name'       => $user->first_name.' '.$user->last_name,
            'scans'       => 0,
          ];
        }


        foreach ($days as $key => $day ) {
            $start = date($day);
            $end =  date('Y-m-d', strtotime("+1 day", strtotime("$day")));

            $participants = Participant::where([['created_at','>=', $start], ['created_at','<=', $end]])->get();
            $all += count($participants);


            $from_website = 0;
            $from_admin = 0;
            foreach ($participants as $participant) {
                if ($participant->added_by == NULL){
                    ++$from_website;
                    $last_subscription_web = $participant->created_at;
                }
                else{
                    ++$from_admin;
                    $last_subscription_admin = $participant->created_at;
                }
                
                $last_subscription = $participant->created_at;

                if ($participant->status == 2) {
                    $valide_participant++;
                    $last_validation = $participant->updated_at;
                }

                // Stats pour les téléchargements de certificats
                if ($participant->flag_gen_certificate == 1) {
                    $total_certificate_download++;
                    if ($last_certificate_download <= $participant->updated_at)
                        $last_certificate_download = $participant->updated_at;
                }

                // Stats par jour
                if ($participant->updated_at >= date("Y-m-d") && $participant->flag_gen_certificate == 1) {
                    $total_certificate_download_today++;
                }

                if ($participant->updated_at < date("Y-m-d") && $participant->flag_gen_certificate == 1) {
                    $total_certificate_download_yesterday++;
                }
            }

            //$participants = Participant::where([['created_at','>=', $start], ['created_at','<=', $end]])->get();

            $arr_from_website[] = $from_website;
            $arr_from_admin[] = $from_admin;

            $all_from_website += $from_website;
            $all_from_admin += $from_admin;

            $stats_days[] = [
                "day"                           => $day,
                "all_subscriptions"             => count($participants),
                "subscriptions_from_website"    => $from_website,
                "subscriptions_by_admin"        => $from_admin,
            ];

            //$this->helper->debug("Du ".$start." au ".$end." : ".count($participants)."(".$from_admin."/".$from_website.") inscrits");

            $scans = Scan::where([['created_at','>=', $start], ['created_at','<=', $end]])
                ->with([
                'user',
                'session',
                'session.salles',
                ])
                ->get();

            foreach ($scans as $scan) {
                $last_scan = $scan->created_at;
                $total_scan++;


                // Stats par jour
                if ($scan->created_at >= date("Y-m-d")) {
                $total_scan_today++;
                }

                if ($scan->created_at < date("Y-m-d")) {
                $total_scan_yesterday++;
                }

                // Stats pour les salles
                if ($scan->session->salles->id == 1) {
                $salles_stats['pleniere']++;
                }
                else {
                $salles_stats['atelier']++;
                }

                // Stats pour les sessions
                foreach ($sessions_stats as $key => $stats) {
                if ($stats['id'] == $scan->session->id) {
                    $sessions_stats[$key]['scans']++;
                }
                }

                // Stats pour les hotesses
                foreach ($users_stats as $key => $user) {
                if ($user['id'] == $scan->user->id) {
                    $users_stats[$key]['scans']++;
                }
                }

                // Stats pour les sessions



            }


        }

        //$this->helper->debug($sessions_stats);

        //$participants = Participant::where([['created_at','>=', $start], ['created_at','<=', $end]])->get();

        //$this->helper->debug($participants->toArray);



        $attestations = Attestation::all();



        $participants = Participant::where([['status', 2]])->orwhere([['status', 1]])->get();

        $free = 0;
        $payed = 0;
        $congress_payed = 0;
        $precongress_payed= 0;
        $congress_waiting = 0;
        $precongress_waiting = 0;
        $payed_by_participant = 0;
        $congress_payed_by_participant = 0;
        $precongress_payed_by_participant = 0;
        $offline_payment = 0;
        $congress_offline_payment = 0;
        $precongress_offline_payment = 0;
        $formule1 = 0;
        $formule2 = 0;
        $formule3 = 0;

        $formule1_free = 0;
        $formule2_free = 0;
        $formule3_free = 0;


        $precongress_formule1_free = 0;
        $precongress_formule2_free = 0;
        $precongress_formules_free = 0;

        
        $formule1_payed = 0;
        $formule2_payed = 0;
        $formule3_payed = 0;

        $formule1_payed_online = 0;
        $formule2_payed_online = 0;
        $formule3_payed_online = 0;

        $formule1_payed_offline = 0;
        $formule2_payed_offline = 0;
        $formule3_payed_offline = 0;



        $precongress_formule1_payed = 0;
        $precongress_formule2_payed = 0;
        $precongress_formules_payed = 0;

        $precongress_formule1_payed_online = 0;
        $precongress_formule2_payed_online = 0;
        $precongress_formules_payed_online = 0;

        $precongress_formule1_payed_offline = 0;
        $precongress_formule2_payed_offline = 0;
        $precongress_formules_payed_offline = 0;


        $congress_formule1_waiting_payment = 0;
        $congress_formule2_waiting_payment = 0;
        $congress_formule3_waiting_payment = 0;


        $precongress_formule1_waiting_payment = 0;
        $precongress_formule2_waiting_payment = 0;
        $precongress_formules_waiting_payment = 0;



        $nb_participants_payed = 0;


        $nb_registed_congres = 0;
        $nb_registed_precongres = 0;
        $nb_registed_both = 0;
        

        foreach ($participants as $key => $participant) {

            if ($participant->status == 2) {

                $nb_participants_payed++;

                

                switch ($participant->payment_method) {
                    case 'free':
                        $free++;

                        switch ($participant->formule) {
                            case 1:
                                $formule1_free++;
                                break;
            
                            case 2:
                                $formule2_free++;
                                break;
            
                            case 3:
                                $formule3_free++;
                                break;
            
                            default:
                                # code...
                                break;
                        }

                        switch ($participant->formule_pc) {
                            case 1:
                                $precongress_formule1_free++;
                                break;
            
                            case 2:
                                $precongress_formule2_free++;
                                break;
            
                            case 12:
                                $precongress_formules_free++;
                                break;
                            case 21:
                                $precongress_formules_free++;
                                break;
            
                            default:
                                # code...
                                break;
                        }
                        

                        break;

                    case 'offline':
                        $offline_payment++;
                        $payed++;

                        if ($participant->formule >= 1) {
                            $congress_offline_payment++;
                            $congress_payed++;
                        }
                        
                        if ($participant->formule_pc >= 1) {
                            $precongress_offline_payment++;
                            $precongress_payed++;
                        }

                        switch ($participant->formule) {
                            case 1:
                                $formule1_payed++;
                                if ($participant->validated_by != null) 
                                    $formule1_payed_offline++;

                                break;
            
                            case 2:
                                $formule2_payed++;
                                if ($participant->validated_by != null) 
                                    $formule2_payed_offline++;

                                break;
            
                            case 3:
                                $formule3_payed++;
                                if ($participant->validated_by != null) 
                                    $formule3_payed_offline++;
                                
                                break;
            
                            default:
                                # code...
                                break;
                        }

                        switch ($participant->formule_pc) {
                            case 1:
                                $precongress_formule1_payed++;
                                if ($participant->validated_by != null) 
                                    $precongress_formule1_payed_offline++;

                                break;
            
                            case 2:
                                $precongress_formule2_payed++;
                                if ($participant->validated_by != null) 
                                    $precongress_formule2_payed_offline++;

                                break;
            
                            case 12:
                                $precongress_formules_payed++;
                                if ($participant->validated_by != null) 
                                    $precongress_formules_payed_offline++;
                                
                                break;

                            case 21:
                                $precongress_formules_payed++;
                                if ($participant->validated_by != null) 
                                    $precongress_formules_payed_offline++;
                                
                                break;

                            default:
                                # code...
                                break;
                        }
                        break;

                    case 'paydunya_by_participant':
                        $payed_by_participant++;
                        $payed++;

                        switch ($participant->formule) {
                            case 1:
                                $formule1_payed++;
                                $formule1_payed_online++;
                                break;
            
                            case 2:
                                $formule2_payed++;
                                $formule2_payed_online++;
                                break;
            
                            case 3:
                                $formule3_payed++;
                                $formule3_payed_online++;
                                break;
            
                            default:
                                # code...
                                break;
                        }

                        switch ($participant->formule_pc) {
                            case 1:
                                $precongress_formule1_payed++;
                                $precongress_formule1_payed_online++;
                                break;
            
                            case 2:
                                $precongress_formule2_payed++;
                                $precongress_formule2_payed_online++;
                                break;
            
                            case 12:
                                $precongress_formules_payed++;
                                $precongress_formules_payed_online++;
                                break;

                            case 21:
                                $precongress_formules_payed++;
                                $precongress_formules_payed_online++;
                                break;
            
                            default:
                                # code...
                                break;
                        }
                        break;

                    case 'paytech_by_participant':
                        $payed_by_participant++;
                        $payed++;
     
                        if ($participant->formule >= 1) {
                            $congress_payed_by_participant++;
                            $congress_payed++;
                        }
                        
                        if ($participant->formule_pc >= 1) {
                            $precongress_payed_by_participant++;
                            $precongress_payed++;
                        }


                        switch ($participant->formule) {
                            case 1:
                                $formule1_payed++;
                                $formule1_payed_online++;
                                break;
            
                            case 2:
                                $formule2_payed++;
                                $formule2_payed_online++;
                                break;
            
                            case 3:
                                $formule3_payed++;
                                $formule3_payed_online++;
                                break;
            
                            default:
                                # code...
                                break;
                        }

                        switch ($participant->formule_pc) {
                            case 1:
                                $precongress_formule1_payed++;
                                $precongress_formule1_payed_online++;
                                break;
            
                            case 2:
                                $precongress_formule2_payed++;
                                $precongress_formule2_payed_online++;
                                break;
            
                            case 12:
                                $precongress_formules_payed++;
                                $precongress_formules_payed_online++;
                                break;
                                
                            case 21:
                                $precongress_formules_payed++;
                                $precongress_formules_payed_online++;
                                break;
            
                            default:
                                # code...
                                break;
                        }
                        break;

                    default:
                        # code...
                        break;
                }
            }
            elseif ($participant->status <= 1) {


                if ($participant->formule != NULL) {
                    $congress_waiting++;
                }   
                
                if ($participant->formule_pc != NULL  && $participant->payment_status_pc != 'completed' ) {
                    $precongress_waiting++;
                }


                switch ($participant->formule) {
                    case 1:
                        $congress_formule1_waiting_payment++;
                        break;
    
                    case 2:
                        $congress_formule2_waiting_payment++;
                        break;
    
                    case 3:
                        $congress_formule2_waiting_payment++;
                        break;
    
                    default:
                        # code...
                        break;
                }

                switch ($participant->formule_pc) {
                    case 1:
                        $precongress_formule1_waiting_payment++;
                        break;
    
                    case 2:
                        $precongress_formule2_waiting_payment++;
                        break;
    
                    case 12:
                        $precongress_formules_waiting_payment++;
                        break;
                    case 21:
                        $precongress_formules_waiting_payment++;
                        break;
                    default:
                        # code...
                        break;
                }
            }


            switch ($participant->formule) {
                case 1:
                    $formule1++;
                    break;

                case 2:
                    $formule2++;
                    break;

                case 3:
                    $formule3++;
                    break;

                default:
                    # code...
                    break;
            }

            

            if ($participant->formule >= 1) {
                $nb_registed_congres++;
            }

            if ($participant->formule_pc >= 1) {
                $nb_registed_precongres++;
            }

            if ($participant->formule >= 1 && $participant->formule_pc >= 1) {
                $nb_registed_both++;
            }

            



        }
        



        $stats = [
          "all"                       => $all,
          'last_subscription'         => $last_subscription,
          'last_subscription_web'     => $last_subscription_web,
          'last_subscription_admin'   => $last_subscription_admin,
          'last_validation'           => $last_validation,
          'all_from_website'          => $all_from_website,
          'all_from_admin'            => $all_from_admin,
          "days"                      => $stats_days,
          "arr_from_website"          => $arr_from_website,
          "arr_from_admin"            => $arr_from_admin,
          "arr_scan"                  => $scans,
          "validated"                 => $valide_participant,
          "last_scan"                 => $last_scan,
          "total_scan"                => $total_scan,
          "total_scan_today"          => $total_scan_today,
          "total_scan_yesterday"      => $total_scan_yesterday,
          "salles_stats"              => $salles_stats,
          'sessions_stats'            => $sessions_stats,
          'users_stats'               => $users_stats,
          'total_certificate_download'            => $total_certificate_download,
          'total_certificate_download_yesterday'  => $total_certificate_download_yesterday,
          'total_certificate_download_today'      => $total_certificate_download_today,
          'last_certificate_download'  =>  $last_certificate_download,
          'pourcentage_pleniere'      => number_format(($salles_stats['pleniere'] / ($salles_stats['pleniere'] + $salles_stats['atelier']) * 100 ) , 2, '.', ''),
          'pourcentage_atelier'      => number_format(($salles_stats['atelier'] / ($salles_stats['pleniere'] + $salles_stats['atelier']) * 100 ) , 2, '.', ''),
          'total_gen_attestation'    => count($attestations),




          'nb_participants'       =>  count($participants),
          'nb_participants_payed' =>  $nb_participants_payed,
          'free'                  =>  $free,
          'payed'                 =>  $payed,
          'congress_payed'        =>  $congress_payed,
          'precongress_payed'     =>  $precongress_payed,
          'payed_by_participant'  =>  $payed_by_participant,
          'offline_payment'       =>  $offline_payment,


          'congress_offline_payment'       =>  $congress_offline_payment,
          'precongress_offline_payment'       =>  $precongress_offline_payment,

          'congress_payed_by_participant'  =>  $congress_payed_by_participant,
          'precongress_payed_by_participant'  =>  $precongress_payed_by_participant,
            
          'congress_waiting'        => $congress_waiting,
          'congress_formule1_waiting_payment' =>  $congress_formule1_waiting_payment,
          'congress_formule2_waiting_payment' =>  $congress_formule2_waiting_payment,
          'congress_formule3_waiting_payment' =>  $congress_formule3_waiting_payment,

          'precongress_waiting'        => $precongress_waiting,
          'precongress_formule1_waiting_payment' =>  $precongress_formule1_waiting_payment,
          'precongress_formule2_waiting_payment' =>  $precongress_formule2_waiting_payment,
          'precongress_formules_waiting_payment' => $precongress_formules_waiting_payment,

          'formule1'              =>  $formule1,
          'formule2'              =>  $formule2,
          'formule3'              =>  $formule3,

          'formule1_free'              =>  $formule1_free,
          'formule2_free'              =>  $formule2_free,
          'formule3_free'              =>  $formule3_free,

          'formule1_payed'              =>  $formule1_payed,
          'formule2_payed'              =>  $formule2_payed,
          'formule3_payed'              =>  $formule3_payed,


          'formule1_payed_online'              =>  $formule1_payed_online,
          'formule2_payed_online'              =>  $formule2_payed_online,
          'formule3_payed_online'              =>  $formule3_payed_online,


          'formule1_payed_offline'              =>  $formule1_payed_offline,
          'formule2_payed_offline'              =>  $formule2_payed_offline,
          'formule3_payed_offline'              =>  $formule3_payed_offline,





          'precongress_formule1_free'              =>  $precongress_formule1_free,
          'precongress_formule2_free'              =>  $precongress_formule2_free,
          'precongress_formules_free'              =>  $precongress_formule3_free,

          'precongress_formule1_payed'              =>  $precongress_formule1_payed,
          'precongress_formule2_payed'              =>  $precongress_formule2_payed,
          'precongress_formules_payed'              =>  $precongress_formules_payed,


          'precongress_formule1_payed_online'              =>  $precongress_formule1_payed_online,
          'precongress_formule2_payed_online'              =>  $precongress_formule2_payed_online,
          'precongress_formules_payed_online'              =>  $precongress_formules_payed_online,


          'precongress_formule1_payed_offline'              =>  $precongress_formule1_payed_offline,
          'precongress_formule2_payed_offline'              =>  $precongress_formule2_payed_offline,
          'precongress_formules_payed_offline'              =>  $precongress_formules_payed_offline,


          //'congress_payed_by_participant'     =>  $formule1_payed_online + $formule2_payed_online + $formule3_payed_online,
          //'precongress_payed_by_participant'  =>  $payed_by_participant,


          'nb_registed_precongres'   => $nb_registed_precongres,
          'nb_registed_congres'      => $nb_registed_congres,
          'nb_registed_both'         => $nb_registed_both,

        ];

        $data = [
          'authorized'    => true,
          'stats'         => $stats,
          'last_update'   => date("Y-m-d H:i:s")
        ];


        return $this->view->render($response, 'home.twig', compact('data'));
      }

      $partticipants = Participant::where('added_by','=',$this->usr['id'])->with('qrCode')->with('country')->with('state')->get();

      if ($this->usr['role_id'] <= 2)
        $partticipants = Participant::with('qrCode')->with('country')->with('state')->with('addedBy')->with('validatedBy')->get();


      $data = [
        'authorized'     => true,
        'participants'   => $partticipants
      ];

      return $this->view->render($response, 'allParticipant.twig', compact('data'));
    }


    public function participantStats($request, $response, $arg)
    {

        if(!$this->helper->checkConnexion())
        return $response->withRedirect($this->router->pathFor('login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

        if(!$this->helper->checkPrivillieges($this->usr['role_id'], [1,2,3,4,5])){
            $data = [
            'authorized'   => false,
            ];
            return $this->view->render($response, 'home.twig', compact('data'));
        }
        
        $participants = Participant::where([['status', 2]])->get();

        $free = 0;
        $payed = 0;
        $payed_by_participant = 0;
        $offline_payment = 0;
        $formule1 = 0;
        $formule2 = 0;
        $formule3 = 0;

        foreach ($participants as $key => $participant) {
            
            switch ($participant->payment_method) {
                case 'free':
                    $free++;
                    break;

                case 'offline':
                    $offline_payment++;
                    $payed++;
                    break;

                case 'paydunya_by_participant':
                    $payed_by_participant++;
                    $payed++;
                    break;

                default:
                    # code...
                    break;
            }


            switch ($participant->formule) {
                case 1:
                    $formule1++;
                    break;

                case 2:
                    $formule2++;
                    break;

                case 3:
                    $formule3++;
                    break;

                default:
                    # code...
                    break;
            }

        }
        

        $data = [
            'authorized'            => true,
            'stats' =>  [
                'nb_participants'       =>  count($participants),
                'free'                  =>  $free,
                'payed'                 =>  $payed,
                'payed_by_participant'  =>  $payed_by_participant,
                'offline_payment'       =>  $offline_payment,
                'formule1'       =>  $formule1,
                'formule2'       =>  $formule2,
                'formule3'       =>  $formule3,
            ]
        ];

        $this->helper->debug($data);
        
       // exit;
        return $this->view->render($response, 'statsInscriptions.twig', compact('data'));
        

    }

    public function statsCountries($request, $response, $arg)
    {
        if(!$this->helper->checkConnexion())
        return $response->withRedirect($this->router->pathFor('login')."?redirect=".urlencode($this->redirect_url_after_login), 302);

        if(!$this->helper->checkPrivillieges($this->usr['role_id'], [1,2,3,4,5])){
            $data = [
            'authorized'   => false,
            ];
            return $this->view->render($response, 'home.twig', compact('data'));
        }

        
        $participants = Participant::where('status','=', 1)->count();
        /*
        $results = Participant::join('countries', 'participants.country_id', '=', 'countries.id')
            ->select('countries.name as country' ,'count(*) as total')
            ->groupBy('participants.country_id')
            ->get();
            */

        $results = Participant::join('countries', 'participants.country_id', '=', 'countries.id')
            ->select('countries.name as pays', DB::raw('count(*) as total'))
            ->groupBy('participants.country_id')
            ->get();


        

        // SELECT c.name AS pays ,count(*) as total FROM `sos_participants` p, countries c WHERE p.country_id = c.id GROUP BY p.country_id 
        $nb_countries = 0;
        
        foreach ($results as $key => $line) {

            # code...
        }

        

        $data = [
            'authorized'            => true,
            'nb_participants'       =>  $participants,
            'nb_countries'          =>  count($results),
        ];

        
        return $this->view->render($response, 'statsCountries.twig', compact('data'));
        
    }





    public function qrCode($request, $response, $arg)
    {
      $qrCode = new QrCode();

      $qrCode
          ->setText('https://web.facebook.com/Soci%C3%A9t%C3%A9-s%C3%A9n%C3%A9galaise-de-Cardiologie-Sosecar-646132345518873/photos/?ref=page_internal')
          ->setSize(300)
          ->setPadding(10)
          ->setErrorCorrection('high')
          ->setForegroundColor(array('r' => 0, 'g' => 200, 'b' => 0, 'a' => 0))
          ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
          ->setLabel('CARDI TECH')
          ->setLabelFontSize(16)
          ->setImageType(QrCode::IMAGE_TYPE_PNG);

      $imgQrCode = $qrCode->getContentType().';base64,'.$qrCode->generate();

      $file = self::savePNG($imgQrCode, "moussa049" );


      $img2 = "src/images/about_1.jpg";



      //self::upddateTableCountries();

      return $this->view->render($response, 'qr.twig', compact('file'));
    }

    public static function savePNG($data, $file_name,  $upload_dir="ressources/qrcodes/" )
    {
      list($type, $data) = explode(';', $data);
      list(, $data)      = explode(',', $data);
      $data = base64_decode($data);

    	$file = $upload_dir. $file_name . '.png';

    	$success = file_put_contents($file, $data);
    	//print $success ? $file : 'Unable to save the file.';


      if (file_put_contents($file, $data)) {
        return $file;
      }

      return false;

    }

    public function initStates($request, $response, $arg)
    {
      if ($_POST) {
        $states = State::where('country_id','=',$_POST['countryId'])->get();

        $states = $states->map(function ($state) {
            return ['id' => $state->id, 'text' => $state->name];
        })->toArray();

      }
      //return $response->withJson($states);
      return json_encode($states);

    }

    public function initScanData($request, $response, $arg)
    {
      if ($_POST) {
        //$sessions = Session::with('salles')->orderBy('start','ASC')->get();
        $sessions = Session::where('end','>=',date('Y-m-d 00:00:00'))->with('salles')->orderBy('start','ASC')->get();


        $sessions = $sessions->map(function ($session) {
            return ['id' => $session->id, 'text' => date("d/m/Y", strtotime($session->start))." à partir de ".date("H:i",strtotime($session->start))." - ".$session->title ];
        })->toArray();

      }
      return json_encode($sessions);
    }

    public static function upddateTableCountries()
    {
      // code...

      $world = array(
                  array(
                      'id'        => 4,
                      'name'      => 'Afghanistan',
                      'alpha2'    => 'af',
                      'alpha3'    => 'afg'
                  ),
                  array(
                      'id'        => 710,
                      'name'      => 'Afrique du Sud',
                      'alpha2'    => 'za',
                      'alpha3'    => 'zaf'
                  ),
                  array(
                      'id'        => 248,
                      'name'      => 'Îles Åland',
                      'alpha2'    => 'ax',
                      'alpha3'    => 'ala'
                  ),
                  array(
                      'id'        => 8,
                      'name'      => 'Albanie',
                      'alpha2'    => 'al',
                      'alpha3'    => 'alb'
                  ),
                  array(
                      'id'        => 12,
                      'name'      => 'Algérie',
                      'alpha2'    => 'dz',
                      'alpha3'    => 'dza'
                  ),
                  array(
                      'id'        => 276,
                      'name'      => 'Allemagne',
                      'alpha2'    => 'de',
                      'alpha3'    => 'deu'
                  ),
                  array(
                      'id'        => 20,
                      'name'      => 'Andorre',
                      'alpha2'    => 'ad',
                      'alpha3'    => 'and'
                  ),
                  array(
                      'id'        => 24,
                      'name'      => 'Angola',
                      'alpha2'    => 'ao',
                      'alpha3'    => 'ago'
                  ),
                  array(
                      'id'        => 660,
                      'name'      => 'Anguilla',
                      'alpha2'    => 'ai',
                      'alpha3'    => 'aia'
                  ),
                  array(
                      'id'        => 10,
                      'name'      => 'Antarctique',
                      'alpha2'    => 'aq',
                      'alpha3'    => 'ata'
                  ),
                  array(
                      'id'        => 28,
                      'name'      => 'Antigua-et-Barbuda',
                      'alpha2'    => 'ag',
                      'alpha3'    => 'atg'
                  ),
                  array(
                      'id'        => 682,
                      'name'      => 'Arabie saoudite',
                      'alpha2'    => 'sa',
                      'alpha3'    => 'sau'
                  ),
                  array(
                      'id'        => 32,
                      'name'      => 'Argentine',
                      'alpha2'    => 'ar',
                      'alpha3'    => 'arg'
                  ),
                  array(
                      'id'        => 51,
                      'name'      => 'Arménie',
                      'alpha2'    => 'am',
                      'alpha3'    => 'arm'
                  ),
                  array(
                      'id'        => 533,
                      'name'      => 'Aruba',
                      'alpha2'    => 'aw',
                      'alpha3'    => 'abw'
                  ),
                  array(
                      'id'        => 36,
                      'name'      => 'Australie',
                      'alpha2'    => 'au',
                      'alpha3'    => 'aus'
                  ),
                  array(
                      'id'        => 40,
                      'name'      => 'Autriche',
                      'alpha2'    => 'at',
                      'alpha3'    => 'aut'
                  ),
                  array(
                      'id'        => 31,
                      'name'      => 'Azerbaïdjan',
                      'alpha2'    => 'az',
                      'alpha3'    => 'aze'
                  ),
                  array(
                      'id'        => 44,
                      'name'      => 'Bahamas',
                      'alpha2'    => 'bs',
                      'alpha3'    => 'bhs'
                  ),
                  array(
                      'id'        => 48,
                      'name'      => 'Bahreïn',
                      'alpha2'    => 'bh',
                      'alpha3'    => 'bhr'
                  ),
                  array(
                      'id'        => 50,
                      'name'      => 'Bangladesh',
                      'alpha2'    => 'bd',
                      'alpha3'    => 'bgd'
                  ),
                  array(
                      'id'        => 52,
                      'name'      => 'Barbade',
                      'alpha2'    => 'bb',
                      'alpha3'    => 'brb'
                  ),
                  array(
                      'id'        => 112,
                      'name'      => 'Biélorussie',
                      'alpha2'    => 'by',
                      'alpha3'    => 'blr'
                  ),
                  array(
                      'id'        => 56,
                      'name'      => 'Belgique',
                      'alpha2'    => 'be',
                      'alpha3'    => 'bel'
                  ),
                  array(
                      'id'        => 84,
                      'name'      => 'Belize',
                      'alpha2'    => 'bz',
                      'alpha3'    => 'blz'
                  ),
                  array(
                      'id'        => 204,
                      'name'      => 'Bénin',
                      'alpha2'    => 'bj',
                      'alpha3'    => 'ben'
                  ),
                  array(
                      'id'        => 60,
                      'name'      => 'Bermudes',
                      'alpha2'    => 'bm',
                      'alpha3'    => 'bmu'
                  ),
                  array(
                      'id'        => 64,
                      'name'      => 'Bhoutan',
                      'alpha2'    => 'bt',
                      'alpha3'    => 'btn'
                  ),
                  array(
                      'id'        => 68,
                      'name'      => 'Bolivie',
                      'alpha2'    => 'bo',
                      'alpha3'    => 'bol'
                  ),
                  array(
                      'id'        => 535,
                      'name'      => 'Pays-Bas caribéens',
                      'alpha2'    => 'bq',
                      'alpha3'    => 'bes'
                  ),
                  array(
                      'id'        => 70,
                      'name'      => 'Bosnie-Herzégovine',
                      'alpha2'    => 'ba',
                      'alpha3'    => 'bih'
                  ),
                  array(
                      'id'        => 72,
                      'name'      => 'Botswana',
                      'alpha2'    => 'bw',
                      'alpha3'    => 'bwa'
                  ),
                  array(
                      'id'        => 74,
                      'name'      => 'Île Bouvet',
                      'alpha2'    => 'bv',
                      'alpha3'    => 'bvt'
                  ),
                  array(
                      'id'        => 76,
                      'name'      => 'Brésil',
                      'alpha2'    => 'br',
                      'alpha3'    => 'bra'
                  ),
                  array(
                      'id'        => 96,
                      'name'      => 'Brunei',
                      'alpha2'    => 'bn',
                      'alpha3'    => 'brn'
                  ),
                  array(
                      'id'        => 100,
                      'name'      => 'Bulgarie',
                      'alpha2'    => 'bg',
                      'alpha3'    => 'bgr'
                  ),
                  array(
                      'id'        => 854,
                      'name'      => 'Burkina Faso',
                      'alpha2'    => 'bf',
                      'alpha3'    => 'bfa'
                  ),
                  array(
                      'id'        => 108,
                      'name'      => 'Burundi',
                      'alpha2'    => 'bi',
                      'alpha3'    => 'bdi'
                  ),
                  array(
                      'id'        => 136,
                      'name'      => 'Îles Caïmans',
                      'alpha2'    => 'ky',
                      'alpha3'    => 'cym'
                  ),
                  array(
                      'id'        => 116,
                      'name'      => 'Cambodge',
                      'alpha2'    => 'kh',
                      'alpha3'    => 'khm'
                  ),
                  array(
                      'id'        => 120,
                      'name'      => 'Cameroun',
                      'alpha2'    => 'cm',
                      'alpha3'    => 'cmr'
                  ),
                  array(
                      'id'        => 124,
                      'name'      => 'Canada',
                      'alpha2'    => 'ca',
                      'alpha3'    => 'can'
                  ),
                  array(
                      'id'        => 132,
                      'name'      => 'Cap-Vert',
                      'alpha2'    => 'cv',
                      'alpha3'    => 'cpv'
                  ),
                  array(
                      'id'        => 140,
                      'name'      => 'République centrafricaine',
                      'alpha2'    => 'cf',
                      'alpha3'    => 'caf'
                  ),
                  array(
                      'id'        => 152,
                      'name'      => 'Chili',
                      'alpha2'    => 'cl',
                      'alpha3'    => 'chl'
                  ),
                  array(
                      'id'        => 156,
                      'name'      => 'Chine',
                      'alpha2'    => 'cn',
                      'alpha3'    => 'chn'
                  ),
                  array(
                      'id'        => 162,
                      'name'      => 'Île Christmas',
                      'alpha2'    => 'cx',
                      'alpha3'    => 'cxr'
                  ),
                  array(
                      'id'        => 196,
                      'name'      => 'Chypre (pays)',
                      'alpha2'    => 'cy',
                      'alpha3'    => 'cyp'
                  ),
                  array(
                      'id'        => 166,
                      'name'      => 'Îles Cocos',
                      'alpha2'    => 'cc',
                      'alpha3'    => 'cck'
                  ),
                  array(
                      'id'        => 170,
                      'name'      => 'Colombie',
                      'alpha2'    => 'co',
                      'alpha3'    => 'col'
                  ),
                  array(
                      'id'        => 174,
                      'name'      => 'Comores (pays)',
                      'alpha2'    => 'km',
                      'alpha3'    => 'com'
                  ),
                  array(
                      'id'        => 178,
                      'name'      => 'République du Congo',
                      'alpha2'    => 'cg',
                      'alpha3'    => 'cog'
                  ),
                  array(
                      'id'        => 180,
                      'name'      => 'République démocratique du Congo',
                      'alpha2'    => 'cd',
                      'alpha3'    => 'cod'
                  ),
                  array(
                      'id'        => 184,
                      'name'      => 'Îles Cook',
                      'alpha2'    => 'ck',
                      'alpha3'    => 'cok'
                  ),
                  array(
                      'id'        => 410,
                      'name'      => 'Corée du Sud',
                      'alpha2'    => 'kr',
                      'alpha3'    => 'kor'
                  ),
                  array(
                      'id'        => 408,
                      'name'      => 'Corée du Nord',
                      'alpha2'    => 'kp',
                      'alpha3'    => 'prk'
                  ),
                  array(
                      'id'        => 188,
                      'name'      => 'Costa Rica',
                      'alpha2'    => 'cr',
                      'alpha3'    => 'cri'
                  ),
                  array(
                      'id'        => 384,
                      'name'      => 'Côte d\'Ivoire',
                      'alpha2'    => 'ci',
                      'alpha3'    => 'civ'
                  ),
                  array(
                      'id'        => 191,
                      'name'      => 'Croatie',
                      'alpha2'    => 'hr',
                      'alpha3'    => 'hrv'
                  ),
                  array(
                      'id'        => 192,
                      'name'      => 'Cuba',
                      'alpha2'    => 'cu',
                      'alpha3'    => 'cub'
                  ),
                  array(
                      'id'        => 531,
                      'name'      => 'Curaçao',
                      'alpha2'    => 'cw',
                      'alpha3'    => 'cuw'
                  ),
                  array(
                      'id'        => 208,
                      'name'      => 'Danemark',
                      'alpha2'    => 'dk',
                      'alpha3'    => 'dnk'
                  ),
                  array(
                      'id'        => 262,
                      'name'      => 'Djibouti',
                      'alpha2'    => 'dj',
                      'alpha3'    => 'dji'
                  ),
                  array(
                      'id'        => 214,
                      'name'      => 'République dominicaine',
                      'alpha2'    => 'do',
                      'alpha3'    => 'dom'
                  ),
                  array(
                      'id'        => 212,
                      'name'      => 'Dominique',
                      'alpha2'    => 'dm',
                      'alpha3'    => 'dma'
                  ),
                  array(
                      'id'        => 818,
                      'name'      => 'Égypte',
                      'alpha2'    => 'eg',
                      'alpha3'    => 'egy'
                  ),
                  array(
                      'id'        => 222,
                      'name'      => 'Salvador',
                      'alpha2'    => 'sv',
                      'alpha3'    => 'slv'
                  ),
                  array(
                      'id'        => 784,
                      'name'      => 'Émirats arabes unis',
                      'alpha2'    => 'ae',
                      'alpha3'    => 'are'
                  ),
                  array(
                      'id'        => 218,
                      'name'      => 'Équateur (pays)',
                      'alpha2'    => 'ec',
                      'alpha3'    => 'ecu'
                  ),
                  array(
                      'id'        => 232,
                      'name'      => 'Érythrée',
                      'alpha2'    => 'er',
                      'alpha3'    => 'eri'
                  ),
                  array(
                      'id'        => 724,
                      'name'      => 'Espagne',
                      'alpha2'    => 'es',
                      'alpha3'    => 'esp'
                  ),
                  array(
                      'id'        => 233,
                      'name'      => 'Estonie',
                      'alpha2'    => 'ee',
                      'alpha3'    => 'est'
                  ),
                  array(
                      'id'        => 840,
                      'name'      => 'États-Unis',
                      'alpha2'    => 'us',
                      'alpha3'    => 'usa'
                  ),
                  array(
                      'id'        => 231,
                      'name'      => 'Éthiopie',
                      'alpha2'    => 'et',
                      'alpha3'    => 'eth'
                  ),
                  array(
                      'id'        => 238,
                      'name'      => 'Malouines',
                      'alpha2'    => 'fk',
                      'alpha3'    => 'flk'
                  ),
                  array(
                      'id'        => 234,
                      'name'      => 'Îles Féroé',
                      'alpha2'    => 'fo',
                      'alpha3'    => 'fro'
                  ),
                  array(
                      'id'        => 242,
                      'name'      => 'Fidji',
                      'alpha2'    => 'fj',
                      'alpha3'    => 'fji'
                  ),
                  array(
                      'id'        => 246,
                      'name'      => 'Finlande',
                      'alpha2'    => 'fi',
                      'alpha3'    => 'fin'
                  ),
                  array(
                      'id'        => 250,
                      'name'      => 'France',
                      'alpha2'    => 'fr',
                      'alpha3'    => 'fra'
                  ),
                  array(
                      'id'        => 266,
                      'name'      => 'Gabon',
                      'alpha2'    => 'ga',
                      'alpha3'    => 'gab'
                  ),
                  array(
                      'id'        => 270,
                      'name'      => 'Gambie',
                      'alpha2'    => 'gm',
                      'alpha3'    => 'gmb'
                  ),
                  array(
                      'id'        => 268,
                      'name'      => 'Géorgie (pays)',
                      'alpha2'    => 'ge',
                      'alpha3'    => 'geo'
                  ),
                  array(
                      'id'        => 239,
                      'name'      => 'Géorgie du Sud-et-les îles Sandwich du Sud',
                      'alpha2'    => 'gs',
                      'alpha3'    => 'sgs'
                  ),
                  array(
                      'id'        => 288,
                      'name'      => 'Ghana',
                      'alpha2'    => 'gh',
                      'alpha3'    => 'gha'
                  ),
                  array(
                      'id'        => 292,
                      'name'      => 'Gibraltar',
                      'alpha2'    => 'gi',
                      'alpha3'    => 'gib'
                  ),
                  array(
                      'id'        => 300,
                      'name'      => 'Grèce',
                      'alpha2'    => 'gr',
                      'alpha3'    => 'grc'
                  ),
                  array(
                      'id'        => 308,
                      'name'      => 'Grenade (pays)',
                      'alpha2'    => 'gd',
                      'alpha3'    => 'grd'
                  ),
                  array(
                      'id'        => 304,
                      'name'      => 'Groenland',
                      'alpha2'    => 'gl',
                      'alpha3'    => 'grl'
                  ),
                  array(
                      'id'        => 312,
                      'name'      => 'Guadeloupe',
                      'alpha2'    => 'gp',
                      'alpha3'    => 'glp'
                  ),
                  array(
                      'id'        => 316,
                      'name'      => 'Guam',
                      'alpha2'    => 'gu',
                      'alpha3'    => 'gum'
                  ),
                  array(
                      'id'        => 320,
                      'name'      => 'Guatemala',
                      'alpha2'    => 'gt',
                      'alpha3'    => 'gtm'
                  ),
                  array(
                      'id'        => 831,
                      'name'      => 'Guernesey',
                      'alpha2'    => 'gg',
                      'alpha3'    => 'ggy'
                  ),
                  array(
                      'id'        => 324,
                      'name'      => 'Guinée',
                      'alpha2'    => 'gn',
                      'alpha3'    => 'gin'
                  ),
                  array(
                      'id'        => 624,
                      'name'      => 'Guinée-Bissau',
                      'alpha2'    => 'gw',
                      'alpha3'    => 'gnb'
                  ),
                  array(
                      'id'        => 226,
                      'name'      => 'Guinée équatoriale',
                      'alpha2'    => 'gq',
                      'alpha3'    => 'gnq'
                  ),
                  array(
                      'id'        => 328,
                      'name'      => 'Guyana',
                      'alpha2'    => 'gy',
                      'alpha3'    => 'guy'
                  ),
                  array(
                      'id'        => 254,
                      'name'      => 'Guyane',
                      'alpha2'    => 'gf',
                      'alpha3'    => 'guf'
                  ),
                  array(
                      'id'        => 332,
                      'name'      => 'Haïti',
                      'alpha2'    => 'ht',
                      'alpha3'    => 'hti'
                  ),
                  array(
                      'id'        => 334,
                      'name'      => 'Îles Heard-et-MacDonald',
                      'alpha2'    => 'hm',
                      'alpha3'    => 'hmd'
                  ),
                  array(
                      'id'        => 340,
                      'name'      => 'Honduras',
                      'alpha2'    => 'hn',
                      'alpha3'    => 'hnd'
                  ),
                  array(
                      'id'        => 344,
                      'name'      => 'Hong Kong',
                      'alpha2'    => 'hk',
                      'alpha3'    => 'hkg'
                  ),
                  array(
                      'id'        => 348,
                      'name'      => 'Hongrie',
                      'alpha2'    => 'hu',
                      'alpha3'    => 'hun'
                  ),
                  array(
                      'id'        => 833,
                      'name'      => 'Île de Man',
                      'alpha2'    => 'im',
                      'alpha3'    => 'imn'
                  ),
                  array(
                      'id'        => 581,
                      'name'      => 'Îles mineures éloignées des États-Unis',
                      'alpha2'    => 'um',
                      'alpha3'    => 'umi'
                  ),
                  array(
                      'id'        => 92,
                      'name'      => 'Îles Vierges britanniques',
                      'alpha2'    => 'vg',
                      'alpha3'    => 'vgb'
                  ),
                  array(
                      'id'        => 850,
                      'name'      => 'Îles Vierges des États-Unis',
                      'alpha2'    => 'vi',
                      'alpha3'    => 'vir'
                  ),
                  array(
                      'id'        => 356,
                      'name'      => 'Inde',
                      'alpha2'    => 'in',
                      'alpha3'    => 'ind'
                  ),
                  array(
                      'id'        => 360,
                      'name'      => 'Indonésie',
                      'alpha2'    => 'id',
                      'alpha3'    => 'idn'
                  ),
                  array(
                      'id'        => 364,
                      'name'      => 'Iran',
                      'alpha2'    => 'ir',
                      'alpha3'    => 'irn'
                  ),
                  array(
                      'id'        => 368,
                      'name'      => 'Irak',
                      'alpha2'    => 'iq',
                      'alpha3'    => 'irq'
                  ),
                  array(
                      'id'        => 372,
                      'name'      => 'Irlande (pays)',
                      'alpha2'    => 'ie',
                      'alpha3'    => 'irl'
                  ),
                  array(
                      'id'        => 352,
                      'name'      => 'Islande',
                      'alpha2'    => 'is',
                      'alpha3'    => 'isl'
                  ),
                  array(
                      'id'        => 376,
                      'name'      => 'Israël',
                      'alpha2'    => 'il',
                      'alpha3'    => 'isr'
                  ),
                  array(
                      'id'        => 380,
                      'name'      => 'Italie',
                      'alpha2'    => 'it',
                      'alpha3'    => 'ita'
                  ),
                  array(
                      'id'        => 388,
                      'name'      => 'Jamaïque',
                      'alpha2'    => 'jm',
                      'alpha3'    => 'jam'
                  ),
                  array(
                      'id'        => 392,
                      'name'      => 'Japon',
                      'alpha2'    => 'jp',
                      'alpha3'    => 'jpn'
                  ),
                  array(
                      'id'        => 832,
                      'name'      => 'Jersey',
                      'alpha2'    => 'je',
                      'alpha3'    => 'jey'
                  ),
                  array(
                      'id'        => 400,
                      'name'      => 'Jordanie',
                      'alpha2'    => 'jo',
                      'alpha3'    => 'jor'
                  ),
                  array(
                      'id'        => 398,
                      'name'      => 'Kazakhstan',
                      'alpha2'    => 'kz',
                      'alpha3'    => 'kaz'
                  ),
                  array(
                      'id'        => 404,
                      'name'      => 'Kenya',
                      'alpha2'    => 'ke',
                      'alpha3'    => 'ken'
                  ),
                  array(
                      'id'        => 417,
                      'name'      => 'Kirghizistan',
                      'alpha2'    => 'kg',
                      'alpha3'    => 'kgz'
                  ),
                  array(
                      'id'        => 296,
                      'name'      => 'Kiribati',
                      'alpha2'    => 'ki',
                      'alpha3'    => 'kir'
                  ),
                  array(
                      'id'        => 414,
                      'name'      => 'Koweït',
                      'alpha2'    => 'kw',
                      'alpha3'    => 'kwt'
                  ),
                  array(
                      'id'        => 418,
                      'name'      => 'Laos',
                      'alpha2'    => 'la',
                      'alpha3'    => 'lao'
                  ),
                  array(
                      'id'        => 426,
                      'name'      => 'Lesotho',
                      'alpha2'    => 'ls',
                      'alpha3'    => 'lso'
                  ),
                  array(
                      'id'        => 428,
                      'name'      => 'Lettonie',
                      'alpha2'    => 'lv',
                      'alpha3'    => 'lva'
                  ),
                  array(
                      'id'        => 422,
                      'name'      => 'Liban',
                      'alpha2'    => 'lb',
                      'alpha3'    => 'lbn'
                  ),
                  array(
                      'id'        => 430,
                      'name'      => 'Liberia',
                      'alpha2'    => 'lr',
                      'alpha3'    => 'lbr'
                  ),
                  array(
                      'id'        => 434,
                      'name'      => 'Libye',
                      'alpha2'    => 'ly',
                      'alpha3'    => 'lby'
                  ),
                  array(
                      'id'        => 438,
                      'name'      => 'Liechtenstein',
                      'alpha2'    => 'li',
                      'alpha3'    => 'lie'
                  ),
                  array(
                      'id'        => 440,
                      'name'      => 'Lituanie',
                      'alpha2'    => 'lt',
                      'alpha3'    => 'ltu'
                  ),
                  array(
                      'id'        => 442,
                      'name'      => 'Luxembourg (pays)',
                      'alpha2'    => 'lu',
                      'alpha3'    => 'lux'
                  ),
                  array(
                      'id'        => 446,
                      'name'      => 'Macao',
                      'alpha2'    => 'mo',
                      'alpha3'    => 'mac'
                  ),
                  array(
                      'id'        => 807,
                      'name'      => 'Macédoine du Nord',
                      'alpha2'    => 'mk',
                      'alpha3'    => 'mkd'
                  ),
                  array(
                      'id'        => 450,
                      'name'      => 'Madagascar',
                      'alpha2'    => 'mg',
                      'alpha3'    => 'mdg'
                  ),
                  array(
                      'id'        => 458,
                      'name'      => 'Malaisie',
                      'alpha2'    => 'my',
                      'alpha3'    => 'mys'
                  ),
                  array(
                      'id'        => 454,
                      'name'      => 'Malawi',
                      'alpha2'    => 'mw',
                      'alpha3'    => 'mwi'
                  ),
                  array(
                      'id'        => 462,
                      'name'      => 'Maldives',
                      'alpha2'    => 'mv',
                      'alpha3'    => 'mdv'
                  ),
                  array(
                      'id'        => 466,
                      'name'      => 'Mali',
                      'alpha2'    => 'ml',
                      'alpha3'    => 'mli'
                  ),
                  array(
                      'id'        => 470,
                      'name'      => 'Malte',
                      'alpha2'    => 'mt',
                      'alpha3'    => 'mlt'
                  ),
                  array(
                      'id'        => 580,
                      'name'      => 'Îles Mariannes du Nord',
                      'alpha2'    => 'mp',
                      'alpha3'    => 'mnp'
                  ),
                  array(
                      'id'        => 504,
                      'name'      => 'Maroc',
                      'alpha2'    => 'ma',
                      'alpha3'    => 'mar'
                  ),
                  array(
                      'id'        => 584,
                      'name'      => 'Îles Marshall (pays)',
                      'alpha2'    => 'mh',
                      'alpha3'    => 'mhl'
                  ),
                  array(
                      'id'        => 474,
                      'name'      => 'Martinique',
                      'alpha2'    => 'mq',
                      'alpha3'    => 'mtq'
                  ),
                  array(
                      'id'        => 480,
                      'name'      => 'Maurice (pays)',
                      'alpha2'    => 'mu',
                      'alpha3'    => 'mus'
                  ),
                  array(
                      'id'        => 478,
                      'name'      => 'Mauritanie',
                      'alpha2'    => 'mr',
                      'alpha3'    => 'mrt'
                  ),
                  array(
                      'id'        => 175,
                      'name'      => 'Mayotte',
                      'alpha2'    => 'yt',
                      'alpha3'    => 'myt'
                  ),
                  array(
                      'id'        => 484,
                      'name'      => 'Mexique',
                      'alpha2'    => 'mx',
                      'alpha3'    => 'mex'
                  ),
                  array(
                      'id'        => 583,
                      'name'      => 'États fédérés de Micronésie (pays)',
                      'alpha2'    => 'fm',
                      'alpha3'    => 'fsm'
                  ),
                  array(
                      'id'        => 498,
                      'name'      => 'Moldavie',
                      'alpha2'    => 'md',
                      'alpha3'    => 'mda'
                  ),
                  array(
                      'id'        => 492,
                      'name'      => 'Monaco',
                      'alpha2'    => 'mc',
                      'alpha3'    => 'mco'
                  ),
                  array(
                      'id'        => 496,
                      'name'      => 'Mongolie',
                      'alpha2'    => 'mn',
                      'alpha3'    => 'mng'
                  ),
                  array(
                      'id'        => 499,
                      'name'      => 'Monténégro',
                      'alpha2'    => 'me',
                      'alpha3'    => 'mne'
                  ),
                  array(
                      'id'        => 500,
                      'name'      => 'Montserrat',
                      'alpha2'    => 'ms',
                      'alpha3'    => 'msr'
                  ),
                  array(
                      'id'        => 508,
                      'name'      => 'Mozambique',
                      'alpha2'    => 'mz',
                      'alpha3'    => 'moz'
                  ),
                  array(
                      'id'        => 104,
                      'name'      => 'Birmanie',
                      'alpha2'    => 'mm',
                      'alpha3'    => 'mmr'
                  ),
                  array(
                      'id'        => 516,
                      'name'      => 'Namibie',
                      'alpha2'    => 'na',
                      'alpha3'    => 'nam'
                  ),
                  array(
                      'id'        => 520,
                      'name'      => 'Nauru',
                      'alpha2'    => 'nr',
                      'alpha3'    => 'nru'
                  ),
                  array(
                      'id'        => 524,
                      'name'      => 'Népal',
                      'alpha2'    => 'np',
                      'alpha3'    => 'npl'
                  ),
                  array(
                      'id'        => 558,
                      'name'      => 'Nicaragua',
                      'alpha2'    => 'ni',
                      'alpha3'    => 'nic'
                  ),
                  array(
                      'id'        => 562,
                      'name'      => 'Niger',
                      'alpha2'    => 'ne',
                      'alpha3'    => 'ner'
                  ),
                  array(
                      'id'        => 566,
                      'name'      => 'Nigeria',
                      'alpha2'    => 'ng',
                      'alpha3'    => 'nga'
                  ),
                  array(
                      'id'        => 570,
                      'name'      => 'Niue',
                      'alpha2'    => 'nu',
                      'alpha3'    => 'niu'
                  ),
                  array(
                      'id'        => 574,
                      'name'      => 'Île Norfolk',
                      'alpha2'    => 'nf',
                      'alpha3'    => 'nfk'
                  ),
                  array(
                      'id'        => 578,
                      'name'      => 'Norvège',
                      'alpha2'    => 'no',
                      'alpha3'    => 'nor'
                  ),
                  array(
                      'id'        => 540,
                      'name'      => 'Nouvelle-Calédonie',
                      'alpha2'    => 'nc',
                      'alpha3'    => 'ncl'
                  ),
                  array(
                      'id'        => 554,
                      'name'      => 'Nouvelle-Zélande',
                      'alpha2'    => 'nz',
                      'alpha3'    => 'nzl'
                  ),
                  array(
                      'id'        => 86,
                      'name'      => 'Territoire britannique de l\'océan Indien',
                      'alpha2'    => 'io',
                      'alpha3'    => 'iot'
                  ),
                  array(
                      'id'        => 512,
                      'name'      => 'Oman',
                      'alpha2'    => 'om',
                      'alpha3'    => 'omn'
                  ),
                  array(
                      'id'        => 800,
                      'name'      => 'Ouganda',
                      'alpha2'    => 'ug',
                      'alpha3'    => 'uga'
                  ),
                  array(
                      'id'        => 860,
                      'name'      => 'Ouzbékistan',
                      'alpha2'    => 'uz',
                      'alpha3'    => 'uzb'
                  ),
                  array(
                      'id'        => 586,
                      'name'      => 'Pakistan',
                      'alpha2'    => 'pk',
                      'alpha3'    => 'pak'
                  ),
                  array(
                      'id'        => 585,
                      'name'      => 'Palaos',
                      'alpha2'    => 'pw',
                      'alpha3'    => 'plw'
                  ),
                  array(
                      'id'        => 275,
                      'name'      => 'Palestine',
                      'alpha2'    => 'ps',
                      'alpha3'    => 'pse'
                  ),
                  array(
                      'id'        => 591,
                      'name'      => 'Panama',
                      'alpha2'    => 'pa',
                      'alpha3'    => 'pan'
                  ),
                  array(
                      'id'        => 598,
                      'name'      => 'Papouasie-Nouvelle-Guinée',
                      'alpha2'    => 'pg',
                      'alpha3'    => 'png'
                  ),
                  array(
                      'id'        => 600,
                      'name'      => 'Paraguay',
                      'alpha2'    => 'py',
                      'alpha3'    => 'pry'
                  ),
                  array(
                      'id'        => 528,
                      'name'      => 'Pays-Bas',
                      'alpha2'    => 'nl',
                      'alpha3'    => 'nld'
                  ),
                  array(
                      'id'        => 604,
                      'name'      => 'Pérou',
                      'alpha2'    => 'pe',
                      'alpha3'    => 'per'
                  ),
                  array(
                      'id'        => 608,
                      'name'      => 'Philippines',
                      'alpha2'    => 'ph',
                      'alpha3'    => 'phl'
                  ),
                  array(
                      'id'        => 612,
                      'name'      => 'Îles Pitcairn',
                      'alpha2'    => 'pn',
                      'alpha3'    => 'pcn'
                  ),
                  array(
                      'id'        => 616,
                      'name'      => 'Pologne',
                      'alpha2'    => 'pl',
                      'alpha3'    => 'pol'
                  ),
                  array(
                      'id'        => 258,
                      'name'      => 'Polynésie française',
                      'alpha2'    => 'pf',
                      'alpha3'    => 'pyf'
                  ),
                  array(
                      'id'        => 630,
                      'name'      => 'Porto Rico',
                      'alpha2'    => 'pr',
                      'alpha3'    => 'pri'
                  ),
                  array(
                      'id'        => 620,
                      'name'      => 'Portugal',
                      'alpha2'    => 'pt',
                      'alpha3'    => 'prt'
                  ),
                  array(
                      'id'        => 634,
                      'name'      => 'Qatar',
                      'alpha2'    => 'qa',
                      'alpha3'    => 'qat'
                  ),
                  array(
                      'id'        => 638,
                      'name'      => 'La Réunion',
                      'alpha2'    => 're',
                      'alpha3'    => 'reu'
                  ),
                  array(
                      'id'        => 642,
                      'name'      => 'Roumanie',
                      'alpha2'    => 'ro',
                      'alpha3'    => 'rou'
                  ),
                  array(
                      'id'        => 826,
                      'name'      => 'Royaume-Uni',
                      'alpha2'    => 'gb',
                      'alpha3'    => 'gbr'
                  ),
                  array(
                      'id'        => 643,
                      'name'      => 'Russie',
                      'alpha2'    => 'ru',
                      'alpha3'    => 'rus'
                  ),
                  array(
                      'id'        => 646,
                      'name'      => 'Rwanda',
                      'alpha2'    => 'rw',
                      'alpha3'    => 'rwa'
                  ),
                  array(
                      'id'        => 732,
                      'name'      => 'République arabe sahraouie démocratique',
                      'alpha2'    => 'eh',
                      'alpha3'    => 'esh'
                  ),
                  array(
                      'id'        => 652,
                      'name'      => 'Saint-Barthélemy',
                      'alpha2'    => 'bl',
                      'alpha3'    => 'blm'
                  ),
                  array(
                      'id'        => 659,
                      'name'      => 'Saint-Christophe-et-Niévès',
                      'alpha2'    => 'kn',
                      'alpha3'    => 'kna'
                  ),
                  array(
                      'id'        => 674,
                      'name'      => 'Saint-Marin',
                      'alpha2'    => 'sm',
                      'alpha3'    => 'smr'
                  ),
                  array(
                      'id'        => 663,
                      'name'      => 'Saint-Martin',
                      'alpha2'    => 'mf',
                      'alpha3'    => 'maf'
                  ),
                  array(
                      'id'        => 534,
                      'name'      => 'Saint-Martin',
                      'alpha2'    => 'sx',
                      'alpha3'    => 'sxm'
                  ),
                  array(
                      'id'        => 666,
                      'name'      => 'Saint-Pierre-et-Miquelon',
                      'alpha2'    => 'pm',
                      'alpha3'    => 'spm'
                  ),
                  array(
                      'id'        => 336,
                      'name'      => 'Saint-Siège (État de la Cité du Vatican)',
                      'alpha2'    => 'va',
                      'alpha3'    => 'vat'
                  ),
                  array(
                      'id'        => 670,
                      'name'      => 'Saint-Vincent-et-les-Grenadines',
                      'alpha2'    => 'vc',
                      'alpha3'    => 'vct'
                  ),
                  array(
                      'id'        => 654,
                      'name'      => 'Sainte-Hélène, Ascension et Tristan da Cunha',
                      'alpha2'    => 'sh',
                      'alpha3'    => 'shn'
                  ),
                  array(
                      'id'        => 662,
                      'name'      => 'Sainte-Lucie',
                      'alpha2'    => 'lc',
                      'alpha3'    => 'lca'
                  ),
                  array(
                      'id'        => 90,
                      'name'      => 'Salomon',
                      'alpha2'    => 'sb',
                      'alpha3'    => 'slb'
                  ),
                  array(
                      'id'        => 882,
                      'name'      => 'Samoa',
                      'alpha2'    => 'ws',
                      'alpha3'    => 'wsm'
                  ),
                  array(
                      'id'        => 16,
                      'name'      => 'Samoa américaines',
                      'alpha2'    => 'as',
                      'alpha3'    => 'asm'
                  ),
                  array(
                      'id'        => 678,
                      'name'      => 'Sao Tomé-et-Principe',
                      'alpha2'    => 'st',
                      'alpha3'    => 'stp'
                  ),
                  array(
                      'id'        => 686,
                      'name'      => 'Sénégal',
                      'alpha2'    => 'sn',
                      'alpha3'    => 'sen'
                  ),
                  array(
                      'id'        => 688,
                      'name'      => 'Serbie',
                      'alpha2'    => 'rs',
                      'alpha3'    => 'srb'
                  ),
                  array(
                      'id'        => 690,
                      'name'      => 'Seychelles',
                      'alpha2'    => 'sc',
                      'alpha3'    => 'syc'
                  ),
                  array(
                      'id'        => 694,
                      'name'      => 'Sierra Leone',
                      'alpha2'    => 'sl',
                      'alpha3'    => 'sle'
                  ),
                  array(
                      'id'        => 702,
                      'name'      => 'Singapour',
                      'alpha2'    => 'sg',
                      'alpha3'    => 'sgp'
                  ),
                  array(
                      'id'        => 703,
                      'name'      => 'Slovaquie',
                      'alpha2'    => 'sk',
                      'alpha3'    => 'svk'
                  ),
                  array(
                      'id'        => 705,
                      'name'      => 'Slovénie',
                      'alpha2'    => 'si',
                      'alpha3'    => 'svn'
                  ),
                  array(
                      'id'        => 706,
                      'name'      => 'Somalie',
                      'alpha2'    => 'so',
                      'alpha3'    => 'som'
                  ),
                  array(
                      'id'        => 729,
                      'name'      => 'Soudan',
                      'alpha2'    => 'sd',
                      'alpha3'    => 'sdn'
                  ),
                  array(
                      'id'        => 728,
                      'name'      => 'Soudan du Sud',
                      'alpha2'    => 'ss',
                      'alpha3'    => 'ssd'
                  ),
                  array(
                      'id'        => 144,
                      'name'      => 'Sri Lanka',
                      'alpha2'    => 'lk',
                      'alpha3'    => 'lka'
                  ),
                  array(
                      'id'        => 752,
                      'name'      => 'Suède',
                      'alpha2'    => 'se',
                      'alpha3'    => 'swe'
                  ),
                  array(
                      'id'        => 756,
                      'name'      => 'Suisse',
                      'alpha2'    => 'ch',
                      'alpha3'    => 'che'
                  ),
                  array(
                      'id'        => 740,
                      'name'      => 'Suriname',
                      'alpha2'    => 'sr',
                      'alpha3'    => 'sur'
                  ),
                  array(
                      'id'        => 744,
                      'name'      => 'Svalbard et ile Jan Mayen',
                      'alpha2'    => 'sj',
                      'alpha3'    => 'sjm'
                  ),
                  array(
                      'id'        => 748,
                      'name'      => 'Eswatini',
                      'alpha2'    => 'sz',
                      'alpha3'    => 'swz'
                  ),
                  array(
                      'id'        => 760,
                      'name'      => 'Syrie',
                      'alpha2'    => 'sy',
                      'alpha3'    => 'syr'
                  ),
                  array(
                      'id'        => 762,
                      'name'      => 'Tadjikistan',
                      'alpha2'    => 'tj',
                      'alpha3'    => 'tjk'
                  ),
                  array(
                      'id'        => 158,
                      'name'      => 'Taïwan / (République de Chine (Taïwan))',
                      'alpha2'    => 'tw',
                      'alpha3'    => 'twn'
                  ),
                  array(
                      'id'        => 834,
                      'name'      => 'Tanzanie',
                      'alpha2'    => 'tz',
                      'alpha3'    => 'tza'
                  ),
                  array(
                      'id'        => 148,
                      'name'      => 'Tchad',
                      'alpha2'    => 'td',
                      'alpha3'    => 'tcd'
                  ),
                  array(
                      'id'        => 203,
                      'name'      => 'Tchéquie',
                      'alpha2'    => 'cz',
                      'alpha3'    => 'cze'
                  ),
                  array(
                      'id'        => 260,
                      'name'      => 'Terres australes et antarctiques françaises',
                      'alpha2'    => 'tf',
                      'alpha3'    => 'atf'
                  ),
                  array(
                      'id'        => 764,
                      'name'      => 'Thaïlande',
                      'alpha2'    => 'th',
                      'alpha3'    => 'tha'
                  ),
                  array(
                      'id'        => 626,
                      'name'      => 'Timor oriental',
                      'alpha2'    => 'tl',
                      'alpha3'    => 'tls'
                  ),
                  array(
                      'id'        => 768,
                      'name'      => 'Togo',
                      'alpha2'    => 'tg',
                      'alpha3'    => 'tgo'
                  ),
                  array(
                      'id'        => 772,
                      'name'      => 'Tokelau',
                      'alpha2'    => 'tk',
                      'alpha3'    => 'tkl'
                  ),
                  array(
                      'id'        => 776,
                      'name'      => 'Tonga',
                      'alpha2'    => 'to',
                      'alpha3'    => 'ton'
                  ),
                  array(
                      'id'        => 780,
                      'name'      => 'Trinité-et-Tobago',
                      'alpha2'    => 'tt',
                      'alpha3'    => 'tto'
                  ),
                  array(
                      'id'        => 788,
                      'name'      => 'Tunisie',
                      'alpha2'    => 'tn',
                      'alpha3'    => 'tun'
                  ),
                  array(
                      'id'        => 795,
                      'name'      => 'Turkménistan',
                      'alpha2'    => 'tm',
                      'alpha3'    => 'tkm'
                  ),
                  array(
                      'id'        => 796,
                      'name'      => 'Îles Turques-et-Caïques',
                      'alpha2'    => 'tc',
                      'alpha3'    => 'tca'
                  ),
                  array(
                      'id'        => 792,
                      'name'      => 'Turquie',
                      'alpha2'    => 'tr',
                      'alpha3'    => 'tur'
                  ),
                  array(
                      'id'        => 798,
                      'name'      => 'Tuvalu',
                      'alpha2'    => 'tv',
                      'alpha3'    => 'tuv'
                  ),
                  array(
                      'id'        => 804,
                      'name'      => 'Ukraine',
                      'alpha2'    => 'ua',
                      'alpha3'    => 'ukr'
                  ),
                  array(
                      'id'        => 858,
                      'name'      => 'Uruguay',
                      'alpha2'    => 'uy',
                      'alpha3'    => 'ury'
                  ),
                  array(
                      'id'        => 548,
                      'name'      => 'Vanuatu',
                      'alpha2'    => 'vu',
                      'alpha3'    => 'vut'
                  ),
                  array(
                      'id'        => 862,
                      'name'      => 'Venezuela',
                      'alpha2'    => 've',
                      'alpha3'    => 'ven'
                  ),
                  array(
                      'id'        => 704,
                      'name'      => 'Viêt Nam',
                      'alpha2'    => 'vn',
                      'alpha3'    => 'vnm'
                  ),
                  array(
                      'id'        => 876,
                      'name'      => 'Wallis-et-Futuna',
                      'alpha2'    => 'wf',
                      'alpha3'    => 'wlf'
                  ),
                  array(
                      'id'        => 887,
                      'name'      => 'Yémen',
                      'alpha2'    => 'ye',
                      'alpha3'    => 'yem'
                  ),
                  array(
                      'id'        => 894,
                      'name'      => 'Zambie',
                      'alpha2'    => 'zm',
                      'alpha3'    => 'zmb'
                  ),
                  array(
                      'id'        => 716,
                      'name'      => 'Zimbabwe',
                      'alpha2'    => 'zw',
                      'alpha3'    => 'zwe'
                  ),
              );
      $count = 0;
      foreach ($world as $key => $country) {
        $one = Country::where('iso2','=',$country['alpha2'])->first();
        if ($one) {
          echo $one->emoji.' : ';
          $update = Country::where('iso2','=',$country['alpha2'])->update(['name'=>$country['name']]);
          if($update)
            echo ++$count.". Mise à jour effectuée pour le ".$country['name'].'('.$one->name.')<br>'.'<br>';
        }
        //echo $country['name'].'<br>';
      }
    }




    public function debug($request, $response, $arg)
    {
      Sms::$client_id = 'qVEnAkOiNBkOHSNnKWvrHahrQ6GYENZO';
      Sms::$client_secret =  'IEYK5eb3J2H1tiRC';


        //Sms::$client_id = 'ouiAAgIG6wUOgac7AfErsEaD3Lgmc7KD';
        //Sms::$client_secret =  'hnIhwENmtCkIr2Hk';
      $token = Sms::getTokensFromApi();


      $message1 = Sms::sendSms("781528375",'Bonjour , Bienvenue à la session !');
      $message2 = Sms::sendSms("773733331",'Bonjour , Bienvenue à la session !');
      $this->helper->debug($message1);
      $this->helper->debug($message2);
      if ($message) {
        echo 1;
      //  $upd = Scan::where([['session_id','=',$arg['session']], ['user_id','=',$arg['id']]])->update(['flag_sms' => 1]);
      }
      else {
        echo 0;
      }


    }




    public function backUpMail($request, $response, $arg)
    {
      //$participants = Participant::where([['status','=',2],['flag_mail_validation','=',0]])->with('job')->with('title')->get();
      //$participants = Participant::where([['status','=',2],['id','=',289]])->with('job')->with('title')->get();
      //$participants = Participant::where('status','=',2)->with('job')->with('title')->get();

      //Participant::where([['status','=',2],['flag_mail_day','=',0],['id','<=',179]])->update(['flag_mail_day'=>1]);

      $participants = Participant::where([['id','=',289],['status','=',2],['flag_mail_day','=',0],['id','>=',179]])->with('job')->with('title')->get();

      $this->helper->debug("A mettre à jour :".count($participants));

      foreach ($participants as $participant) {
          $this->helper->debug("Mise à jour pour : ".$participant->first_name." ".$participant->last_name);

          if ($participant->ticket_number) {
            $ticket_number = $participant->ticket_number;
          }
          else
            $ticket_number = $this->helper->genTicketNumber();

          $pwd_bt = $this->helper->genPwd();
          $pwd = $this->helper->genMdp($pwd_bt);
          //$upd = Participant::where('id','=',$participant->id)->update(["validated_by" => $this->usr['id'], "status" => 2, "ticket_number" => $ticket_number, "num_recu" => $participant->num_recu, "password" => $pwd]);

          //$upd = Participant::where('id','=',$participant->id)->update(["validated_by" => $this->usr['id'], "ticket_number" => $ticket_number, "num_recu" => $participant->num_recu, "password" => $pwd]);
          $upd = true;
          if ($upd){
            $link_for_activate_participant = $this->domain_url."/participant/".$participant->id."/".$ticket_number;

            $file_name = "participant_".$participant->id."_".$ticket_number;
            $text_for_qr = $ticket_number." - ".$participant->title->title." ".$participant->first_name." ".$participant->last_name;
            $qr_code = $this->helper->qrCode($link_for_activate_participant, $file_name, $text_for_qr);
            $this->helper->debug("Filename :".$file_name);
            $this->helper->debug("Link QrCode :".$link_for_activate_participant);
            $this->helper->debug("Texte du QrCode :".$text_for_qr);
            $this->helper->debug("Génération du QrCode :".$qr_code);
            $this->helper->debug("Validateur :".$this->usr['id']);


            $data_qr_code = [
              'qr_code_link'      =>  $qr_code,
              'ticket_number'     =>  $ticket_number,
              'participant_id'    =>  $participant->id,
              'created_at'        =>  \date("Y-m-d H:i:s"),
              'updated_at'        =>  \date("Y-m-d H:i:s")
            ];

            //$new_qr_code = PQrCode::insertGetId($data_qr_code);
            $new_qr_code = true;

            if($new_qr_code){
              $data_email = [
                'qr_code'               =>  $qr_code,
                'ticket_number'         =>  $ticket_number,
                'first_name'            =>  $participant->first_name,
                'last_name'             =>  $participant->last_name,
                'email'                 =>  $participant->email,
                'phone'                 =>  $participant->phone,
                'gender'                =>  $participant->gender,
                'job'                   =>  $participant->job->job_title,
                'title'                 =>  $participant->title->title,
                'link'                  =>  "http://sosecar.com".$this->participant_login_link
              ];

              $s_m = $this->MailSandBox->sendMailBackUp($this , $to = $participant->email, $subject = "SOSECAR - Le programme d'aujourd'hui", $data = $data_email);
              $this->helper->debug("Email :".$participant->email);
              $this->helper->debug("Envoi email :".$s_m);

              if (intval($s_m) == 1) {
                $up = Participant::where('email','=',$participant->email)->update(['flag_mail_day' => 1]);
              }
              echo "<br><br><br>";


            }
          }

      }
      return  $response->withStatus(200)->write('1');

    }

    public function testpdf($request, $response, $arg)
    {
      // code...
      echo '<a href="../../src/doc/certificat.pdf">test</a>';
      // initiate FPDI
      $pdf = new Fpdi();
      // add a page
      $pdf->AddPage();
      // set the source file
      //$fileContent = file_get_contents($_SERVER['DOCUMENT_ROOT'].'http://www.africau.edu/images/default/sample.pdf','rb');
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
      $pdf->Cell(215.9, 20, 'Massamba Diop', 0, 2, 'C');

      $pdf->Output($_SERVER['DOCUMENT_ROOT'].'/src/doc/new-file.pdf', 'F');
    }





















    public function sendMailAtelier($request, $response, $arg)
    {
      $participants = AteliersParticipant::where('activity_5_status', '=', 1)->get();

      $this->helper->debug("A envoyer :".count($participants));

      foreach ($participants as $participant) {
            $this->helper->debug("Mise à jour pour : ".$participant->first_name." ".$participant->last_name);
        
            $data_email = [
                'first_name'            =>  $participant->first_name,
                'last_name'             =>  $participant->last_name,
                'email'                 =>  $participant->email,
                'link'                  =>  "https://sosecar.com"
            ];

            $s_m = $this->MailSandBox->sendMailBackUpAtelier($this , $to = $participant->email, $subject = "Annulation de l'atelier pratique du 18 décembre", $data = $data_email);
            //$s_m = $this->MailSandBox->sendMailBackUpAtelier($this , $to = "pedredieye@gmail.com", $subject = "Annulation de l'atelier pratique du 18 décembre", $data = $data_email);
            
            $this->helper->debug("Email :".$participant->email);

            $this->helper->debug("Envoi email :".$s_m);

            echo "<br><br><br>";

      }

      return  $response->withStatus(200)->write('1');

    }


    public function sendMailAbstract($request, $response, $arg)
    {
      $abstracts = AbstractFile::whereNotIn('id', [5, 46, 58, 56, 19, 32, 41])->get();

      $this->helper->echo("Emails à envoyer :".count($abstracts));
      $test = 0;

      foreach ($abstracts as $abstract) {

            $this->helper->debug("Mise à jour pour n°".$abstract->id." : ".$abstract->title);
        
            $data_email = [
                'sender_name'           =>  $abstract->sender_name,
                'sender_email'          =>  $abstract->sender_email,
                'title'                 =>  $abstract->title,
                'link'                  =>  "https://sosecar.com"
            ];


            //$s_m = $this->MailSandBox->sendMailAbstractConfirmation($this , $to = $abstract->sender_email, $subject = "Votre abstract a été accepté", $data = $data_email);
            
            //$this->helper->debug($data_email);
        

            //$this->helper->echo("Statut d'envoi de l'email :".$s_m);

            echo "<br><br><br>";

      }

      return  $response->withStatus(200)->write('1');

    }
    

    public function sendMailBook($request, $response, $arg)
    {
      //$abstracts = Participant::whereNotIn('id', [5, 46, 58, 56, 19, 32, 41])->get();

      // ngorndeb2011@hotmail.fr
      // XBTB7267YQSX

      $participants = Participant::where([
        ['status','=',2],
        ['flag_mail_book','=',null],
        ['ticket_number','!=', null]
        ])->with('etat')->with('qrcode')->with('country')->with('state')->skip(0)->take(50)->get();

      $this->helper->echo("Emails à envoyer : ".count($participants));
      $test = 0;


      foreach ($participants as $participant) {

            $this->helper->debug("Mise à jour pour n°".$participant->id." : ".$participant->first_name." ".$participant->last_name);

            $data_email = [
                'name'           =>  $participant->first_name." ".$participant->last_name,
                'email'          =>  $participant->email,
                'link_book'      =>  "https://sosecar.sn/src/doc/Book_6EME_EDITION_CARDIOTECH_SENEGAL_FINAL.pdf",
                'link'           =>  "https://sosecar.sn"
            ];

            $s_m = $this->MailSandBox->sendMailBook($this , $to = $participant->email, $subject = "Votre Book Final du Congrès CardioTech 2024 est disponible !", $data = $data_email);
            //$s_m = $this->MailSandBox->sendMailBook($this , $to = "pedredieye@gmail.com", $subject = "Votre Book Final du Congrès CardioTech 2024 est disponible !", $data = $data_email);
            //exit;

           // $this->helper->debug($data_email);
            if ($s_m) {
                Participant::where('id','=', $participant->id)->update(['flag_mail_book' => $s_m]);
            }

            $this->helper->echo("Statut d'envoi de l'email :".$s_m);

            echo "<br><br><br>";

      }

      return  $response->withStatus(200)->write('1');

    }
    

    public function downloadAbstracts($request, $response, $arg)
    {
        $abstracts = AbstractFile::whereNotIn('id', [5, 46, 58, 56, 19, 32, 41])->get();

        $this->helper->echo("Abstracts trouvés :".count($abstracts));


        echo('================================');

    
        // Dossier de stockage
        $storageDir = '/var/www/sosecar.sn/zips'; 
        $zipFileName = 'abstracts_' . date('Ymd_His') . '.zip'; 
        $zipFilePath = $storageDir . '/' . $zipFileName;

        // Créer le répertoire si nécessaire
        if (!is_dir($storageDir)) {
            if (!mkdir($storageDir, 0755, true)) {
                return $response->withStatus(500)->write('Impossible de créer le dossier de stockage.');
            }
        }

        // Initialiser ZipArchive
        $zip = new \ZipArchive();
        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            // Exemple de fichiers à inclure
            $files = [];

            foreach ($abstracts as $abstract) {
                $files [] =  [
                    'title' => $abstract->title,
                    'file_path' => '/var/www/sosecar.sn/uploads/abstracts/'.$abstract->file,
                ];
                
            }

            // Ajouter les fichiers au ZIP
            foreach ($files as $file) {
                $filePath = $file['file_path'];
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, $file['title'] . '.' . pathinfo($filePath, PATHINFO_EXTENSION));
                }
            }

            // Fermer le fichier ZIP
            $zip->close();
        } else {
            return $response->withStatus(500)->write('Impossible de créer le fichier ZIP.');
        }

        // Lire le fichier ZIP pour le téléchargement
        if (!file_exists($zipFilePath)) {
            return $response->withStatus(404)->write('Fichier ZIP introuvable.');
        }



        // Lire et envoyer le fichier ZIP pour le téléchargement
        if (file_exists($zipFilePath)) {
            $response = $response
                ->withHeader('Content-Type', 'application/zip')
                ->withHeader('Content-Disposition', 'attachment; filename="' . basename($zipFilePath) . '"')
                ->withHeader('Content-Length', filesize($zipFilePath));

            // Lire le fichier dans le corps de la réponse
            $stream = fopen($zipFilePath, 'rb');
            $response->getBody()->write(stream_get_contents($stream));
            fclose($stream);

            return $response;
        }

        // Fichier ZIP introuvable
        return $response->withStatus(404)->write('Fichier ZIP introuvable.');

    }

}