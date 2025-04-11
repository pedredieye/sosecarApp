<?php
// Get links
$app->get('/qr', 'HomeController:qrCode');
$app->get('/home', 'HomeController:index');
$app->get('/', 'HomeController:index')->setName('home');
$app->get('/live', 'HomeController:live')->setName('live');
//$app->get('/about', 'HomeController:about')->setName('about');
$app->get('/ateliers-pre-congres', 'HomeController:ateliers')->setName('ateliers-pre-congres');
$app->get('/ateliers-pratiques', 'HomeController:ateliersPratiques')->setName('ateliers-pratiques');
$app->get('/sosecar', 'HomeController:about')->setName('about');
$app->get('/welcome', 'HomeController:welcome')->setName('welcome');
$app->get('/le-programme', 'HomeController:leprogramme')->setName('leprogramme');
$app->get('/documents', 'HomeController:documents')->setName('documents');
$app->get('/presentations', 'HomeController:presentations')->setName('presentations');
$app->get('/abstract/new', 'AbstractController:new')->setName('new_abstract');
$app->post('/stats/live/new', 'HomeController:newAccessLive')->setName('new_access_live');
$app->get('/inscription', 'HomeController:tickets')->setName('tickets');
$app->get('/inscription/{formule}', 'HomeController:tickets')->setName('tickets_f');
$app->get('/informations_utiles', 'HomeController:info')->setName('infos');
$app->get('/gallerie', 'HomeController:gallery')->setName('gallery');

// Paiement Process
$app->get('/pay/init/{ref}', 'PaiementController:initPayment')->setName('paiement_init');
$app->get('/pre/pay/init/{ref}', 'PaiementController:initPrePayment')->setName('pre_paiement_init');
$app->post('/pay/callback', 'PaiementController:callback')->setName('paiement_callback');
$app->get('/pay/return', 'PaiementController:return')->setName('paiement_return');
$app->get('/pay/cancelled', 'PaiementController:cancel')->setName('paiement_cancelled');

$app->get('/pay/init/{service}/{ref}', 'PaiementController:initPaytechPayment')->setName('paiement_paytech_init');

$app->post('/pay/return/{service}/{ref}/callback', 'PaiementController:paytechCallback')->setName('paiement_paytech_callback');
$app->get('/pay/return/{service}/{status}/{ref}', 'PaiementController:paytechReturn')->setName('paiement_paytech_return');
//$app->get('/pay/return/{ref}/cancel', 'PaiementController:paytechReturn')->setName('paiement_paytech_cancel');

//7883442 

$app->get('/pay/debug', 'PaiementController:debug')->setName('paiement_debug');

$app->get('/participant/{id}/{ticketNumber}', 'ParticipantController:scan')->setName('scan');


$app->get('/pwd/get/{p}', 'HomeController:setpwd')->setName('setpwd');


$app->post('/participants/new/save', 'ParticipantController:save')->setName('save_participant');
$app->post('/participant/new', 'ParticipantController:saveNew')->setName('save_new_participant');
$app->post('/abstracts/new/save', 'AbstractController:save')->setName('save_abstract');

$app->post('/participants/atelier/new/save', 'ParticipantController:saveForAtelier')->setName('save_participant_for_atelier');
$app->post('/participants/atelier/pratique/new/save', 'ParticipantController:saveForAtelierPratique')->setName('save_participant_for_atelier_pratique');

$app->post('/states/init', 'HomeController:initStates')->setName('init_states');


$app->get('/debug', 'HomeController:debug')->setName('debug');


$app->get('/participant/login', 'ParticipantController:login')->setName('p_login');
$app->get('/participant/logout', 'ParticipantController:logout')->setName('p_logout');
$app->post('/participant/login/check', 'ParticipantController:checkLogin')->setName('p_login_check');
$app->get('/participant/home', 'ParticipantController:index')->setName('p_qr_code');
$app->get('/participant/certificat', 'ParticipantController:showDownloadCertif')->setName('p_get_certif');
$app->get('/participant/agenda', 'ParticipantController:agenda')->setName('p_agenda');

$app->get('/participant/parametres', 'ParticipantController:sendParams')->setName('p_init_params');
$app->post('/participant/params/send/check', 'ParticipantController:CheckParams')->setName('p_send_params');

$app->get('/attestation', 'ParticipantController:attestation')->setName('attestation');

//$app->post('/participant/certificat/generate', 'ParticipantController:generateCertificatV2')->setName('p_gen_certif');
$app->post('/participant/certificat/generate', 'ParticipantController:generateCertificat20242')->setName('p_gen_certif');
$app->post('/participant/badge/generate', 'ParticipantController:generateBadge')->setName('p_gen_badge');
$app->post('/participant/badge/bulk/generate', 'ParticipantController:bulkGenerateBadge')->setName('p_bulk_gen_badge');