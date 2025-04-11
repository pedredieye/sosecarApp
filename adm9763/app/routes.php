<?php
// Get links
$app->get('/qr', 'HomeController:qrCode');
$app->get('/home', 'HomeController:index');
$app->get('/', 'HomeController:index')->setName('home');
$app->get('/stats/countries', 'HomeController:statsCountries')->setName('stats_countries');
$app->get('/stats/inscriptions', 'HomeController:participantStats')->setName('stats_inscriptions');


$app->get('/participants/new', 'ParticipantController:new')->setName('new_participant');
$app->get('/participants/qrcodes/all', 'ParticipantController:allQrCodes')->setName('all_qr_codes');
$app->get('/participants/tag/all', 'ParticipantController:list')->setName('list_participant');
$app->get('/participants/tag/validated', 'ParticipantController:listValidated')->setName('list_participant_validated');
$app->get('/participants/tag/pending', 'ParticipantController:listPending')->setName('list_participant_pending');
$app->get('/participants/tag/deleted', 'ParticipantController:listDeleted')->setName('list_participant_deleted');
$app->get('/participants/{id}/{ticketNumber}/get', 'ParticipantController:show')->setName('one_participant');
$app->get('/participants/{id}/validate/init', 'ParticipantController:InitValidation')->setName('init_validation_participant');
$app->get('/participants/{id}/pre/validate/init', 'ParticipantController:InitValidationPreCongres')->setName('init_pre_congres_validation_participant');
$app->get('/participants/{id}/cancel/init', 'ParticipantController:InitCancel')->setName('init_cancel_participant');
$app->get('/participants/{id}/pre/cancel/init', 'ParticipantController:InitCancelPreCongres')->setName('init_cancel_pre_congres_participant');

$app->get('/participants/ateliers/all', 'ParticipantController:listForAtelier')->setName('list_participant_atelier');

$app->get('/participants/{id}/edit', 'ParticipantController:edit')->setName('edit_participant');
$app->get('/participants/{id}/del', 'ParticipantController:del')->setName('del_participant');


$app->get('/secretaires/new', 'UserController:newSecretaire')->setName('new_user');
$app->get('/secretaires/tag/all', 'UserController:listSecretaire')->setName('list_user');
$app->get('/secretaire/{id}', 'UserController:showSecretaire')->setName('one_user');


$app->get('/hotesses/new', 'UserController:newHostess')->setName('new_hostess');
$app->get('/hotesses/tag/all', 'UserController:listHostess')->setName('list_hostess');
$app->get('/hotesse/{id}', 'UserController:showHostess')->setName('one_hostess');


$app->get('/abstracts/tag/all', 'AbstractController:list')->setName('list_abstract');
$app->get('/abstract/{id}', 'AbstractController:show')->setName('show_abstract');
$app->post('/abstract/{id}/action/publish', 'AbstractController:publish')->setName('publish_abstract');
$app->post('/abstract/{id}/action/reject', 'AbstractController:reject')->setName('reject_abstract');
$app->post('/abstract/{id}/action/disable', 'AbstractController:disable')->setName('disable_abstract');


// Attestations 
$app->get('/attestation/list', 'ParticipantController:showAttestationsList')->setName('all_attestation_list');

// Scans
$app->get('/scans', 'ParticipantController:showScans')->setName('all_scan');
$app->get('/participant/{id}/{ticketNumber}/scan', 'ParticipantController:newScan')->setName('new_scan');
$app->get('/participant/{id}/{ticketNumber}', 'ParticipantController:newScan')->setName('scan');

$app->get('/scans/list', 'ParticipantController:showScansList')->setName('all_scan_list');

$app->post('/participant/{id}/{session}/scan/validate', 'ParticipantController:validateScan')->setName('validate_scan');


// Presentations 
$app->get('/presentation/list', 'PresentationController:list')->setName('list_presentation');
$app->get('/presentation/new', 'PresentationController:new')->setName('new_presentation');
$app->post('/presentation/new/save', 'PresentationController:save')->setName('save_presentation');
$app->post('/presentation/{id}/action/{status}', 'PresentationController:disable')->setName('disable_presentation');


// Forms
$app->post('/participants/new/save', 'ParticipantController:save')->setName('save_participant');
$app->post('/participant/{id}/{ticketNumber}/validate', 'ParticipantController:validate')->setName('validate_participant');
$app->post('/participant/{id}/{ticketNumber}/validation/undo', 'ParticipantController:undoValidate')->setName('undo_validation_participant');
$app->post('/participants/{id}/edit/save', 'ParticipantController:saveEdit')->setName('save_edit_participant');
$app->post('/participant/{id}/del/save', 'ParticipantController:saveDelete')->setName('save_del_participant');
$app->post('/participant/{id}/del/undo', 'ParticipantController:undoDelete')->setName('undo_del_participant');
$app->post('/participant/{id}/mail/resend', 'ParticipantController:reSendEmail')->setName('mail_resend_participant');


$app->post('/participant/{id}/pre/{ticketNumber}/validate', 'ParticipantController:validatePreCongre')->setName('validate_participant_precongre');
$app->post('/participant/{id}/pre/{ticketNumber}/validation/undo', 'ParticipantController:undoValidatePreCongre')->setName('undo_validation_participant_precongre');

$app->post('/participant/badge/generate', 'ParticipantController:generateAllBadge')->setName('p_gen_badge');


$app->post('/secretaires/new/save', 'UserController:saveSecretaire')->setName('save_user');
$app->post('/hotesses/new/save', 'UserController:saveHostess')->setName('save_hostess');


$app->post('/states/init', 'HomeController:initStates')->setName('init_states');
$app->post('/scan/init', 'HomeController:initScanData')->setName('init_scan_data');

$app->get('/precongres/email/send', 'ParticipantController:reSendEmailForPreAttendees')->setName('reSendEmailForPreAttendees');

$app->get('/followup/email/send', 'ParticipantController:sendEmailFollowUp')->setName('sendEmailFollowUp');


$app->get('/debug', 'HomeController:debug')->setName('debug');
$app->get('/backup/mail', 'HomeController:backUpMail')->setName('backup_mail');
$app->get('/pdf/test', 'HomeController:testpdf')->setName('test_pdf');
$app->get('/atelier/mail/send', 'HomeController:sendMailAtelier')->setName('sendMailAtelier');
$app->get('/abstract/mail/send', 'HomeController:sendMailAbstract')->setName('sendMailAbstract');
$app->get('/abstracts/dowload/all', 'HomeController:downloadAbstracts')->setName('downloadAbstracts');


$app->get('/book/mail/send', 'HomeController:sendMailBook')->setName('sendMailBook');


/// Login and Logout
$app->get('/login', 'UserController:showLogin')->setName('login');
$app->post('/login/check', 'UserController:login');
$app->get('/logout', 'UserController:logout')->setName('logout');