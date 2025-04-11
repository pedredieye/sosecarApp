

var domain = $("#domain").data("domain");
var urlRedirect = $("#domain").data("url-redirect");
var imgLoadingWhite = '<img src="'+domain+'/src/img/Rolling-1s-41px-white.svg" alt="loading" style="max-height: 25px;" height="15">';
var imgLoadingGreen = '<img src="'+domain+'/src/img/rolling.svg" alt="loading" style="max-height: 25px;" height="15">';




$('#if_invited').on('change', function() {
  paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

  if(document.getElementById("if_invited").checked == true){
    toggleRadioGroup("payment_method_bt", statut = true);
    toggleInput("recu", statut = false);
  }
  else{
    toggleRadioGroup("payment_method_bt", statut = false);
    if (paymentMethod == "cash") toggleInput("recu", statut = false);
    else toggleInput("recu", statut = true);

  }

})


const radioButtons = document.querySelectorAll('input[name="payment_method"]');
for(const radioButton of radioButtons){
    radioButton.addEventListener('change', checkInputRecu);
}    



$('#if_invited').on('change', function() {
  if(document.getElementById("if_invited").checked == true){

    toggleRadioGroup("payment_method_bt", statut = true);
  }
  else{
    toggleRadioGroup("payment_method_bt", statut = false);

  }
})


function toggleRadioGroup(className, statut = true) {
  var x = document.getElementsByClassName(className);
  var i;
  for (i = 0; i < x.length; i++) {
      x[i].disabled = statut;
  }
}

function toggleInput(idName, statut = true) {
    document.getElementById(idName).disabled = statut;
}

function checkInputRecu(e) {
  if (this.value == "cash") 
    toggleInput("recu", statut = false);
  else
   toggleInput("recu", statut = true);
}


// Select 2
/*$(".to-select2").select2();
$(".input-to-select2").select2();
$(".select2_demo_2").select2({
    placeholder: "Select a state",
    allowClear: true
});


$("#job").select2({
    placeholder: "Choisir une fonction"
});

$("#title").select2({
    placeholder: "Choisir un titre"
});
*/

function updateDataSelectLocation() {

  var countryId = $("#country option:selected").val();


  var formData = new FormData();
  formData.append("countryId", countryId);

  $.ajax({
    url: domain+'/states/init',
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    cache: false,
    beforeSend: function(){
    },
    statusCode: {
      200: function(responseObject, textStatus, errorThrown) {

      },
      500: function(responseObject, textStatus, errorThrown) {
      },
    }
  }).done(function( dataSrc ) {

    var dataSlct2 = JSON.parse(dataSrc);

    $("#state").empty().select2({
      data: dataSlct2
    });

    var phoneCode = $("#country").select2().find(":selected").data("phone-code");
    $("#ind-addon").html(phoneCode);
    console.log(phoneCode);
  });
}


function updateDataScan() {

  var countryId = $("#country option:selected").val();


  var formData = new FormData();
  formData.append("countryId", countryId);

  $.ajax({
    url: domain+'/scan/init',
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    cache: false,
    beforeSend: function(){
    },
    statusCode: {
      200: function(responseObject, textStatus, errorThrown) {

      },
      500: function(responseObject, textStatus, errorThrown) {
      },
    }
  }).done(function( dataSrc ) {

    var dataSlct2 = JSON.parse(dataSrc);

    $("#session").empty().select2({
      data: dataSlct2,
      laceholder: "Choisir une session",
    });

  });
}

function updateDataSelectLocationFromEdit(idState) {

  var countryId = $("#country option:selected").val();


  var formData = new FormData();
  formData.append("countryId", countryId);

  $.ajax({
    url: domain+'/states/init',
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    cache: false,
    beforeSend: function(){
    },
    statusCode: {
      200: function(responseObject, textStatus, errorThrown) {

      },
      500: function(responseObject, textStatus, errorThrown) {
      },
    }
  }).done(function( dataSrc ) {

    var dataSlct2 = JSON.parse(dataSrc);

    $("#state").empty().select2({
      data: dataSlct2
    }).val(idState).trigger('change');

    var phoneCode = $("#country").select2().find(":selected").data("phone-code");
    $("#ind-addon").html(phoneCode);
  });
}


$('#country').on('change', function() {
  updateDataSelectLocation();
})


function doLoader(button, state, val=""){
  $(button).prop("disabled", state);
  if(state == true)
    $(button).html("Traitement ...");
  else{
    $(button).prop("disabled", state);
    $(button).html(val);
  }
}

function doLoaderSmallBtn(button, state, val=""){
  $(button).prop("disabled", state);
  if(state == true)
    $(button).html("...");
  else{
    $(button).prop("disabled", state);
    $(button).html(val);
  }
}

function doNotification(id, message, type="danger") {
  $(id).removeClass('d-none');
  $(id).fadeIn('slow');
  if(type == "danger"){
      $(id).removeClass('alert-success');
      $(id).addClass('alert-danger');
      $(id).html(message);
      $(id).fadeIn('slow');
  }
  else {
      $(id).removeClass('alert-danger');
      $(id).addClass('alert-success');
      $(id).html(message);
      $(id).fadeIn('slow');
  }
  $("html, body").animate({ scrollTop: 0 }, 600);
  setTimeout(function () {
    $(id).fadeOut('slow');
  }, 15000);

}

function initForm(form) {
  $(form).find('input, input:text, input:password, input:file, select, textarea').val('');
    $(form).find('input:radio, input:checkbox')
         .removeAttr('checked').removeAttr('selected');

}


$("form[name='form_validation_participant']").submit(function (event) {
  event.preventDefault();
  var btnSubmit = $("#btn-validate-part");
  var userId = btnSubmit.data('user-id');
  var participantId = btnSubmit.data('participant-id');
  var participantTicket = btnSubmit.data('participant-ticket');
  validatePart(btnSubmit,participantId,participantTicket,userId);
})



$("form[name='form_validation_pre_participant']").submit(function (event) {
  event.preventDefault();
  var btnSubmit = $("#btn-validate-pre-part");
  var userId = btnSubmit.data('user-id');
  var participantId = btnSubmit.data('participant-id');
  var participantTicket = btnSubmit.data('participant-ticket');
  validatePartPreCongre(btnSubmit,participantId,participantTicket,userId);
})

// Login
$("form[name='adm-login']").submit(function (event) {
  event.preventDefault();
  if ($("form[name='adm-login']").valid()){
    login($(this));
  }
  else {
    doLoader($("#btn-login"),false,'CONENXION');
    doNotification('#notification_box', "Corriger les erreus avant de confirmer!");
  }
})



function login(f) {
    var formData = new FormData();

    var data = f.serializeArray();
    var formData = new FormData();

    jQuery.each( data, function( i, field ) {
      formData.append(field.name, field.value);
    });

    var btn = f.find(":button[type='submit']");
    var btnContent = $(btn).html();

    $.ajax({
      url: domain+"/login/check",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      cache: false,
      beforeSend: function(){
        doLoader(btn,true);
      },
      statusCode: {
        200: function(responseObject, textStatus, errorThrown) {
          console.log(responseObject);
          switch (responseObject) {
            case '1':
                doNotification('#notification_box', 'Connexion acceptée, redirection en cours ...','success');
                setTimeout(function () {
                  if(urlRedirect == '/' || urlRedirect == '' )
                    window.location.replace(domain + urlRedirect);
                  else
                    window.location.replace(urlRedirect);
                }, 2000);

              break;
            case '-1':
              doNotification('#notification_box', "Aucun compte n'est lié à cette adress mail, l'accès est refusé !");
              break;
            case '0':
              doNotification('#notification_box', 'Le mot de passe fournit est incorrect !');
              break;
            default:
          }
          doLoader(btn,false,'Connexion');
        },
        500: function(responseObject, textStatus, errorThrown) {
          console.log("error");
          doLoader(btn,false,'Connexion');
        },
      }
    }).done(function( code ) {
          /*
          if(code == 1){
            doNotification('#notification_box', 'Connexion acceptée, redirection en cours ...','success');
            setTimeout(function () {
              if(urlRedirect == '/' || urlRedirect == '' )
                window.location.replace(domain + urlRedirect);
              else
                window.location.replace(urlRedirect);
            }, 2000);


          }
          else if(code == -1){
            doNotification('#notification_box', "Aucun compte n'est lié à cette adress mail, l'accès est refusé !");
          }
          else if(code == 0) {
            doNotification('#notification_box', 'Le mot de passe fournit est incorrect !');
          }
          */
          //doLoader(btn,false,'Valider');
    });
}


$("form[name='sos-register']").submit(function (event) {
  event.preventDefault();
  addNewParticipant($(this));

})


$("form[name='sos-new-user']").submit(function (event) {
  event.preventDefault();
  addNewUser($(this));
})


$("form[name='sos-new-hostess']").submit(function (event) {
  event.preventDefault();
  addNewHostess($(this));
})


$("form[name='sos-edit-participant']").submit(function (event) {
  event.preventDefault();
  saveEditParticipant($(this), $('#idparticipant').val());

})


$("#btn-delete-part").on("click",function (event) {
  event.preventDefault();
  deleteParticipant($(this));
})


$("#btn-undo-delete-part").on("click",function (event) {
  event.preventDefault();
  undoDeleteParticipant($(this));
})


$(".btn-resend-email").on("click",function (event) {
  event.preventDefault();
  reSendEmail($(this));
})


$("#btn-validate-scan").on("click",function (event) {
  event.preventDefault();
  console.log("Tentative de validation de scan ...");
  //validateScan($(this));
})

$(".js_status_pres").on("change",function (event) {
  event.preventDefault();
  activatePresentation($(this));
})


$("#close_scan").on("click", function (event) {
  event.preventDefault();
  window.close();
})


$("form[name='sos-new-presentation']").submit(function (event) {
  event.preventDefault();
  addNewPresentation($(this));

})


function activatePresentation(btn) {
  var formData = new FormData();
  var presentation = btn.data('presentation-id');
  var status = btn.data('presentation-status');
  var btnVal = $(btn).val();

  $.ajax({
    url: domain + '/presentation/' + presentation + '/action/' + status,
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    cache: false,
    beforeSend: function () {
      console.log('Traitement en cours ...');
      console.log(status);
    },
    statusCode: {
      200: function (responseObject, textStatus, errorThrown) {
        //txt = "Participation enregistrée avec succès !<br>Un conseiller vous appelera pour la confirmation.";
        console.log('Traitement effectué');
        console.log(responseObject);
       txt = "<h4>Opération réussie!</h4>";
        switch (responseObject) {
          case '1':
            txt = "<h4>Opération réussie!</h4> La présentation a été modifiée !";
            console.log(parseInt(status));
            //btn.data('presentation-status', 3);
            btn.data('presentation-status', (1 - parseInt(status)));
            btn.attr("data-presentation-status", (1 - parseInt(status)));

            break;
          case '2':
            txt = "<h4>La présentation est déjà modifiée !</h4> ";
            break;
          case '3':
            txt = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
            break;
          case '4':
            txt = "Le scan a été déjà effectué !";
            break;
          default:
        }

        sosAlrt("#alert-form", txt, parseInt(responseObject));

      },
      500: function (responseObject, textStatus, errorThrown) {
        txt3 = "<h4>Opération échouée!</h4> Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
        console.log(responseObject);
        //ldgOff(btn,btnContent);
        sosAlrt("#alert-form", txt3, parseInt(responseObject));
      },
    }
  }).done(function (data) {
    //ldgOn(btn, false, btnContent);
  });
}

function addNewUser(f) {

  var data = f.serializeArray();
  var formData = new FormData();

  jQuery.each( data, function( i, field ) {
    formData.append(field.name, field.value);
  });

  var btn = f.find(":button[type='submit']");
  var btnContent = $(btn).html();

  $.ajax({
    url: domain+'/secretaires/new/save',
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    cache: false,
    beforeSend: function(){
      //console.log(formData);

      ldgOn(btn);
    },
    statusCode: {
      200: function(responseObject, textStatus, errorThrown) {
        //txt = "Participation enregistrée avec succès !<br>Un conseiller vous appelera pour la confirmation.";
        txt = "<h4>Opération réussie!</h4> Secrétaire ajouté(e) avec succès.";
        console.log(responseObject);
        switch (responseObject) {
          case '1':
            //txt = "Participation enregistrée avec succès !<br>Un conseiller vous appelera pour la confirmation.";
            txt = "<h4>Opération réussie!</h4> Secrétaire ajouté(e) avec succès.";
            break;
          case '2':
            //txt = "Une participation a été déjà ajoutée avec ce compte !<br> Veuillez choisir une autre adresse mail.";
            txt = "<h4>Opération échouée!</h4> Ce compte existe déjà !<br> Veuillez choisir une autre adresse mail.";
            break;
          case '3':
            txt = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
            break;
          default:
        }
        sosAlrt("#alert-form", txt, parseInt(responseObject));
        //showAlertNewsletter(f, true);
      },
      500: function(responseObject, textStatus, errorThrown) {
        //txt3 = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
        txt3 = "<h4>Opération échouée!</h4> Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
        console.log("error");
        ldgOff(btn,btnContent);
        sosAlrt("#alert-form", txt3, parseInt(responseObject));
      },
    }
  }).done(function( data ) {
    ldgOff(btn,btnContent);
  });

}


function addNewHostess(f) {

  var data = f.serializeArray();
  var formData = new FormData();

  jQuery.each( data, function( i, field ) {
    formData.append(field.name, field.value);
  });

  var btn = f.find(":button[type='submit']");
  var btnContent = $(btn).html();

  $.ajax({
    url: domain+'/hotesses/new/save',
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    cache: false,
    beforeSend: function(){
      //console.log(formData);

      ldgOn(btn);
    },
    statusCode: {
      200: function(responseObject, textStatus, errorThrown) {
        //txt = "Participation enregistrée avec succès !<br>Un conseiller vous appelera pour la confirmation.";
        txt = "<h4>Opération réussie!</h4> Hôtesse ajoutée avec succès.";
        console.log(responseObject);
        switch (responseObject) {
          case '1':
            //txt = "Participation enregistrée avec succès !<br>Un conseiller vous appelera pour la confirmation.";
            txt = "<h4>Opération réussie!</h4> Hôtesse ajoutée avec succès.";
            break;
          case '2':
            //txt = "Une participation a été déjà ajoutée avec ce compte !<br> Veuillez choisir une autre adresse mail.";
            txt = "<h4>Opération échouée!</h4> Ce compte existe déjà !<br> Veuillez choisir une autre adresse mail.";
            break;
          case '3':
            txt = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
            break;
          default:
        }
        sosAlrt("#alert-form", txt, parseInt(responseObject));
        //showAlertNewsletter(f, true);
      },
      500: function(responseObject, textStatus, errorThrown) {
        //txt3 = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
        txt3 = "<h4>Opération échouée!</h4> Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
        console.log("error");
        ldgOff(btn,btnContent);
        sosAlrt("#alert-form", txt3, parseInt(responseObject));
      },
    }
  }).done(function( data ) {
    ldgOff(btn,btnContent);
  });

}


function addNewParticipant(f) {

  var data = f.serializeArray();
  var formData = new FormData();

  jQuery.each( data, function( i, field ) {
    formData.append(field.name, field.value);
  });

  var btn = f.find(":button[type='submit']");
  var btnContent = $(btn).html();

  $.ajax({
    url: domain+'/participants/new/save',
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    cache: false,
    beforeSend: function(){
      //console.log(formData);

      ldgOn(btn);
    },
    statusCode: {
      200: function(responseObject, textStatus, errorThrown) {
        //txt = "Participation enregistrée avec succès !<br>Un conseiller vous appelera pour la confirmation.";
        console.log(responseObject);
        txt = "<h4>Opération réussie!</h4> L'inscription a été ajoutée avec succès.";
        console.log(responseObject);
        switch (responseObject) {
          case '1':
            //txt = "Participation enregistrée avec succès !<br>Un conseiller vous appelera pour la confirmation.";
            txt = "<h4>Opération réussie!</h4> L'inscription a été ajoutée avec succès.";
            break;
          case '2':
            //txt = "Une participation a été déjà ajoutée avec ce compte !<br> Veuillez choisir une autre adresse mail.";
            txt = "<h4>Opération échouée!</h4> Une participation a été déjà ajoutée avec ce compte !<br> Veuillez choisir une autre adresse mail.";
            break;
          case '3':
            txt = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
            break;
          default:
        }
        sosAlrt("#alert-form", txt, parseInt(responseObject));
        //showAlertNewsletter(f, true);
      },
      500: function(responseObject, textStatus, errorThrown) {
        //txt3 = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
        txt3 = "<h4>Opération échouée!</h4> Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
        console.log("error");
        console.log(responseObject);
        ldgOff(btn,btnContent);
        sosAlrt("#alert-form", txt3, parseInt(responseObject));
      },
    }
  }).done(function( data ) {
    //dgOffDebug(btn,btnContent);
    ldgOff(btn,btnContent, false);
  });

}



function saveEditParticipant(f, id) {

  var data = f.serializeArray();
  var formData = new FormData();

  jQuery.each( data, function( i, field ) {
    formData.append(field.name, field.value);
  });

  var btn = f.find(":button[type='submit']");
  var btnContent = $(btn).html();

  $.ajax({
    url: domain+'/participants/'+id+'/edit/save',
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    cache: false,
    beforeSend: function(){
      //console.log(formData);

      ldgOn(btn);
    },
    statusCode: {
      200: function(responseObject, textStatus, errorThrown) {
        //txt = "Participation enregistrée avec succès !<br>Un conseiller vous appelera pour la confirmation.";
        txt = "<h4>Opération réussie!</h4> La mise à jour a été effectuée avec succès.";
        console.log(responseObject);
        switch (responseObject) {
          case '1':
            //txt = "Participation enregistrée avec succès !<br>Un conseiller vous appelera pour la confirmation.";
            txt = "<h4>Opération réussie!</h4> La mise à jour a été effectuée avec succès.";
            break;
          case '2':
            //txt = "Une participation a été déjà ajoutée avec ce compte !<br> Veuillez choisir une autre adresse mail.";
            txt = "<h4>Opération échouée!</h4> Une participation a été déjà ajoutée avec ce compte !<br> Veuillez choisir une autre adresse mail.";
            break;
          case '3':
            txt = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
            break;
          default:
        }
        sosAlrt("#alert-form", txt, parseInt(responseObject));
        //showAlertNewsletter(f, true);
      },
      500: function(responseObject, textStatus, errorThrown) {
        //txt3 = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
        txt3 = "<h4>Opération échouée!</h4> Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
        console.log("error");
        ldgOff(btn,btnContent, false);
        sosAlrt("#alert-form", txt3, parseInt(responseObject));
      },
    }
  }).done(function( data ) {
    ldgOff(btn,btnContent, false);
  });

}



function validatePart(btn, participant, ticketNumber, userValidator) {

    var formData = new FormData();
    formData.append("id", participant);
    formData.append("validated_by", userValidator);
    formData.append("ticket_number", ticketNumber);
    formData.append("num_recu", $('#recu').val());
    formData.append("formule", $('#formule').val());
    formData.append("payment_method", $('#payment_method').val());
    formData.append("if_invited", $('#if_invited').is(':checked') );
    formData.append("labo", $('#labo').val());


    var btnContent = $(btn).html();
    console.log(btnContent);
    $.ajax({
      url: domain+'/participant/'+participant+'/'+ticketNumber+'/validate', // /participant/{id}/{ticketNumber}/validate
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      cache: false,
      beforeSend: function(){
        //console.log(formData);

        //ldgOn(btn);
        ldgOnSmallBtn(btn,false, imgLoadingGreen);
      },
      statusCode: {
        200: function(responseObject, textStatus, errorThrown) {
          //txt = "Participation enregistrée avec succès !<br>Un conseiller vous appelera pour la confirmation.";
          txt = "<h4>Opération réussie!</h4> L'inscription a été validée avec succès.";
          console.log(responseObject);
          switch (responseObject) {
            case '1':
              //txt = "Participation enregistrée avec succès !<br>Un conseiller vous appelera pour la confirmation.";
              txt = "<h4>Opération réussie!</h4> L'inscription a été validée avec succès.";
              break;
            case '2':
              //txt = "Une participation a été déjà ajoutée avec ce compte !<br> Veuillez choisir une autre adresse mail.";
              txt = "<h4>Opération échouée!</h4> ";
              break;
            case '3':
              txt = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
              break;
            default:
          }
          sosAlrt("#alert-form", txt, parseInt(responseObject));
          setTimeout(function () {
            //window.location.reload();
          }, 2000);
          //showAlertNewsletter(f, true);
          console.log(parseInt(responseObject));
        },
        500: function(responseObject, textStatus, errorThrown) {
          //txt3 = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
          txt3 = "<h4>Opération échouée!</h4> Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
          console.log("error");
          //ldgOff(btn,btnContent);
          ldgOnSmallBtn(btn,true,btnContent);
          sosAlrt("#alert-form", txt3, parseInt(responseObject));
        },
      }
    }).done(function( data ) {
      //ldgOff(btn,btnContent);
      console.log(data);
      ldgOnSmallBtn(btn,true,btnContent);
    });
}


function validatePartPreCongre(btn, participant, ticketNumber, userValidator) {

  var formData = new FormData();
  formData.append("id", participant);
  formData.append("validated_by", userValidator);
  formData.append("ticket_number", ticketNumber);
  formData.append("num_recu", $('#recu').val());
  formData.append("labo", $('#labo').val());


  var activite1 = $("#activite1").is(":checked") ? "checked" : "unchecked";
  var activite2 = $("#activite2").is(":checked") ? "checked" : "unchecked";

  formData.append("activite1", activite1);
  formData.append("activite2", activite2);
  formData.append("service", "precongres");

  //formData.append("formule", $('#formule').val());


  formData.append("payment_method", $('#payment_method').val());
  formData.append("if_invited", $('#if_invited').is(':checked') );


  var btnContent = $(btn).html();
  console.log(btnContent);
  $.ajax({
    url: domain+'/participant/'+participant+'/pre/'+ticketNumber+'/validate', 
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    cache: false,
    beforeSend: function(){
      //console.log(formData);

      //ldgOn(btn);
      ldgOnSmallBtn(btn,false, imgLoadingGreen);
    },
    statusCode: {
      200: function(responseObject, textStatus, errorThrown) {
        //txt = "Participation enregistrée avec succès !<br>Un conseiller vous appelera pour la confirmation.";
        txt = "<h4>Opération réussie!</h4> L'inscription a été validée avec succès.";
        console.log(responseObject);
        switch (responseObject) {
          case '1':
            //txt = "Participation enregistrée avec succès !<br>Un conseiller vous appelera pour la confirmation.";
            txt = "<h4>Opération réussie!</h4> L'inscription a été validée avec succès.";
            break;
          case '2':
            //txt = "Une participation a été déjà ajoutée avec ce compte !<br> Veuillez choisir une autre adresse mail.";
            txt = "<h4>Opération échouée!</h4> ";
            break;
          case '3':
            txt = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
            break;
          default:
        }
        sosAlrt("#alert-form", txt, parseInt(responseObject));
        setTimeout(function () {
          //window.location.reload();
        }, 2000);
        //showAlertNewsletter(f, true);
        console.log(parseInt(responseObject));
      },
      500: function(responseObject, textStatus, errorThrown) {
        //txt3 = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
        txt3 = "<h4>Opération échouée!</h4> Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
        console.log("error");
        //ldgOff(btn,btnContent);
        ldgOnSmallBtn(btn,true,btnContent);
        sosAlrt("#alert-form", txt3, parseInt(responseObject));
      },
    }
  }).done(function( data ) {
    //ldgOff(btn,btnContent);
    console.log(data);
    ldgOnSmallBtn(btn,true,btnContent);
  });
}


$("#dwl-bd").on('click', function (e) {
  e.preventDefault();
  downloadBadge($(this), type = "all");
})


$("#dwl-bdi").on('click', function (e) {
  e.preventDefault();
  downloadBadge($(this), type = "invited");
})


function downloadBadge(btn,  type = "all") {

  var formData = new FormData();
  formData.append("id", btn.data('id'));
  formData.append("type", type);

  console.log(type);

  var btnContent = btn.html();

  $.ajax({
    url: domain+'/../participant/badge/bulk/generate',
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    cache: false,
    beforeSend: function(){
      ldgOn(btn);
    },
    statusCode: {
      200: function(responseObject, textStatus, errorThrown) {
        console.log(responseObject.responseText);
        txt = "Opération réussie !<br> Votre badge est en cours de téléchargement ...";

        switch (responseObject) {
          case '1':
            txt = "Opération réussie !<br> Votre badge est en cours de téléchargement ...";
            //saAlrt("#gen-badge-notif", txt, parseInt(responseObject));

            $('body').append('<a id="link_badge" download="BADGES PARTICIPANTS" href="'+domain+'/../src/doc/badges/all.pdf'+'">&nbsp;</a>');
            $('#link_badge')[0].click();

            break;
          case '2':
            txt = "Une erreur a été rencontrée (No data received). Veuillez réessayer plus tard.";
            break;
          case '3':
            txt = "Une erreur a été rencontrée. Veuillez réessayer plus tard.";
            break;
          default:
        }
        console.log(responseObject);
        //saAlrt("#gen-badge-notif", txt, parseInt(responseObject));
      },
      500: function(responseObject, textStatus, errorThrown) {
        txt3 = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
        console.log(responseObject.responseText);
        console.log(errorThrown);
        
        ldgOff(btn,btnContent);
        //saAlrt("#gen-badge-notif", txt3, parseInt(responseObject));
      },
    }
  }).done(function( data ) {
    console.log(data);
    ldgOff(btn,btnContent);
  });
}


function deleteParticipant(btn) {

  var formData = new FormData();
  participant = btn.data('participant-id');
  var btnContent = $(btn).html();
  $.ajax({
    url: domain+'/participant/'+participant+'/del/save', // /participant/{id}/{ticketNumber}/validate
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    cache: false,
    beforeSend: function(){
      ldgOnSmallBtn(btn,false, imgLoadingGreen);
    },
    statusCode: {
      200: function(responseObject, textStatus, errorThrown) {
        //txt = "Participation enregistrée avec succès !<br>Un conseiller vous appelera pour la confirmation.";
        txt = "<h4>Opération réussie!</h4> L'inscription a été supprimée avec succès.";
        console.log(responseObject);
        switch (responseObject) {
          case '1':
            //txt = "Participation enregistrée avec succès !<br>Un conseiller vous appelera pour la confirmation.";
            txt = "<h4>Opération réussie!</h4> L'inscription a été supprimée avec succès.";
            setTimeout(function () {
              window.history.back();
            }, 2000);
            break;
          case '2':
            //txt = "Une participation a été déjà ajoutée avec ce compte !<br> Veuillez choisir une autre adresse mail.";
            txt = "<h4>Opération échouée!</h4> ";
            break;
          case '3':
            txt = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
            ldgOnSmallBtn(btn,false,btnContent);
            break;
          default:
        }
        sosAlrt("#alert-form", txt, parseInt(responseObject));
        ldgOnSmallBtn(btn,false,btnContent);

      },
      500: function(responseObject, textStatus, errorThrown) {
        txt3 = "<h4>Opération échouée!</h4> Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
        ldgOnSmallBtn(btn,false,btnContent);
        sosAlrt("#alert-form", txt3, parseInt(responseObject));
      },
    }
  }).done(function( data ) {
    ldgOnSmallBtn(btn,false,btnContent);
  });
}


function undoDeleteParticipant(btn) {

  var formData = new FormData();
  participant = btn.data('participant-id');
  var btnContent = $(btn).html();
  $.ajax({
    url: domain+'/participant/'+participant+'/del/undo', // /participant/{id}/{ticketNumber}/validate
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    cache: false,
    beforeSend: function(){
      ldgOnSmallBtn(btn,false, imgLoadingGreen);
    },
    statusCode: {
      200: function(responseObject, textStatus, errorThrown) {
        //txt = "Participation enregistrée avec succès !<br>Un conseiller vous appelera pour la confirmation.";
        txt = "<h4>Opération réussie!</h4> L'inscription a été réactivée avec succès.";
        console.log(responseObject);
        switch (responseObject) {
          case '1':
            //txt = "Participation enregistrée avec succès !<br>Un conseiller vous appelera pour la confirmation.";
            txt = "<h4>Opération réussie!</h4> L'inscription a été réactivée avec succès.";
            setTimeout(function () {
              window.location.reload();
            }, 2000);
            break;
          case '2':
            //txt = "Une participation a été déjà ajoutée avec ce compte !<br> Veuillez choisir une autre adresse mail.";
            txt = "<h4>Opération échouée!</h4> ";
            break;
          case '3':
            txt = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
            ldgOnSmallBtn(btn,false,btnContent);
            break;
          default:
        }
        sosAlrt("#alert-form", txt, parseInt(responseObject));
        ldgOnSmallBtn(btn,false,btnContent);

      },
      500: function(responseObject, textStatus, errorThrown) {
        txt3 = "<h4>Opération échouée!</h4> Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
        ldgOnSmallBtn(btn,false,btnContent);
        sosAlrt("#alert-form", txt3, parseInt(responseObject));
      },
    }
  }).done(function( data ) {
    ldgOnSmallBtn(btn,false,btnContent);
  });
}


function reSendEmail(btn) {

  var formData = new FormData();
  participant = btn.data('participant-id');
  var btnContent = $(btn).html();
  $.ajax({
    url: domain+'/participant/'+participant+'/mail/resend', // /participant/{id}/{ticketNumber}/validate
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    cache: false,
    beforeSend: function(){
      ldgOnSmallBtn(btn,false, imgLoadingGreen);
    },
    statusCode: {
      200: function(responseObject, textStatus, errorThrown) {
        //txt = "Participation enregistrée avec succès !<br>Un conseiller vous appelera pour la confirmation.";
        txt = "<h4>Opération réussie!</h4> Le mail a été envoyé avec succès.";
        console.log(responseObject);
        switch (responseObject) {
          case '1':
            //txt = "Participation enregistrée avec succès !<br>Un conseiller vous appelera pour la confirmation.";
            txt = "<h4>Opération réussie!</h4> Le mail a été envoyé avec succès.";
            setTimeout(function () {
              newBtnContent = '<div class="green darken-4 p-1 text-center">Mail envoyé</div>';
              putStatusAfterMailSent(btn, newBtnContent);
            }, 2000);
            break;
          case '2':
            //txt = "Une participation a été déjà ajoutée avec ce compte !<br> Veuillez choisir une autre adresse mail.";
            txt = "<h4>Opération échouée!</h4> ";
            break;
          case '3':
            txt = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
            ldgOnSmallBtn(btn,false,btnContent);
            break;
          default:
        }
        sosAlrt("#alert-form", txt, parseInt(responseObject));
        ldgOnSmallBtn(btn,false,btnContent);

      },
      500: function(responseObject, textStatus, errorThrown) {
        txt3 = "<h4>Opération échouée!</h4> Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
        ldgOnSmallBtn(btn,false,btnContent);
        sosAlrt("#alert-form", txt3, parseInt(responseObject));
      },
    }
  }).done(function( data ) {
    ldgOnSmallBtn(btn,false,btnContent);
  });
}

function removeBtnScan() {

  $("#btn-cancel-scan").fadeOut();
  $("#btn-cancel-scan").hide();
  $("#btn-cancel-scan").attr('disable','disable');
  $("#btn-validate-scan").attr('onclick','window.close();');
  $("#btn-validate-scan").attr('id','close_scan');
}

function validateScan(btn) {

  var formData = new FormData();
  var participant = btn.data('participant-id');
  var session = $("#session option:selected").val();
  var btnContent = $(btn).html();

  $.ajax({
    url: domain+'/participant/'+participant+'/'+session+'/scan/validate',
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    cache: false,
    beforeSend: function(){
      ldgOnSmallBtn(btn,false, imgLoadingGreen);
    },
    statusCode: {
      200: function(responseObject, textStatus, errorThrown) {
        //txt = "Participation enregistrée avec succès !<br>Un conseiller vous appelera pour la confirmation.";
        txt = "<h4>Opération réussie!</h4> Le scan a été enrégistré.";
        console.log(responseObject);
        switch (responseObject) {
          case '1':
            //txt = "Participation enregistrée avec succès !<br>Un conseiller vous appelera pour la confirmation.";
            btnContent = "Fermer";
            txt = "<h4>Opération réussie!</h4> Le scan a été enrégistré!";
            setTimeout(function () {
              removeBtnScan();
            },500);

            break;
          case '2':
            //txt = "Une participation a été déjà ajoutée avec ce compte !<br> Veuillez choisir une autre adresse mail.";
            txt = "<h4>Opération échouée!</h4> ";
            break;
          case '3':
            txt = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
            ldgOnSmallBtn(btn,false,btnContent);
            break;
          case '4':
            txt = "Le scan a été déjà effectué !";
            ldgOnSmallBtn(btn,false,btnContent);
            break;
          default:
        }
        sosAlrt("#alert-form", txt, parseInt(responseObject));
        ldgOnSmallBtn(btn,false,btnContent);

      },
      500: function(responseObject, textStatus, errorThrown) {
        txt3 = "<h4>Opération échouée!</h4> Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
        ldgOnSmallBtn(btn,false,btnContent);
        sosAlrt("#alert-form", txt3, parseInt(responseObject));
      },
    }
  }).done(function( data ) {
    ldgOnSmallBtn(btn,false,btnContent);
  });
}


function addNewPresentation(f) {
  var data = f.serializeArray();
  var formData = new FormData();

  jQuery.each(data, function (i, field) {
    formData.append(field.name, field.value);
  });

  var files = $('#pj')[0].files;
  // Check file selected or not
  if (files.length > 0)
    formData.append('file', files[0]);

  var btn = f.find(":button[type='submit']");
  var btnContent = $(btn).html();

  $.ajax({
    url: domain + '/presentation/new/save',
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    cache: false,
    beforeSend: function () {
      //console.log(formData);
      ldgOn(btn);
    },
    statusCode: {
      200: function (responseObject, textStatus, errorThrown) {
        //txt = "Participation enregistrée avec succès !<br>Un conseiller vous appelera pour la confirmation.";
        txt = "<h4>Opération réussie!</h4> Présentation ajoutée avec succès.";
        console.log(responseObject);
        switch (responseObject) {
          case '1':
            //txt = "Participation enregistrée avec succès !<br>Un conseiller vous appelera pour la confirmation.";
            txt = "<h4>Opération réussie!</h4> Présentation ajoutée avec succès.";
            break;
          case '2':
            //txt = "Une participation a été déjà ajoutée avec ce compte !<br> Veuillez choisir une autre adresse mail.";
            txt = "<h4>Opération échouée!</h4> Une présentation semble déjà exister  !<br> Veuillez choisir un autre titre.";
            break;
          case '3':
            txt = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
            break;
          default:
        }
        sosAlrt("#alert-form", txt, parseInt(responseObject));
        //showAlertNewsletter(f, true);
      },
      500: function (responseObject, textStatus, errorThrown) {
        //txt3 = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
        txt3 = "<h4>Opération échouée!</h4> Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
        console.log("error");
        ldgOff(btn, btnContent);
        sosAlrt("#alert-form", txt3, parseInt(responseObject));
      },
    }
  }).done(function (data) {
    ldgOff(btn, btnContent);
  });
}

function putStatusAfterMailSent(btn, content) {
  $(btn).removeClass('on-loading');
  $(btn).attr('disabled',false);
  $(btn).parent().html(content);

}

function ldgOn(btn, content="") {
  $(btn).addClass('on-loading');
  $(btn).attr('disabled',true);

  var w = $(btn).width();

  $(btn).html(imgLoadingWhite+content);

  if (w > 0)
    $(btn).width(w);
  $('input').prop('disabled', true);
  $('select').prop('disabled', true);
}


function ldgOff(btn, content, reset = true) {
  $(btn).removeClass('on-loading');
  $(btn).attr('disabled',false);
  $(btn).html(content);
  if (reset) {
    $('input').prop('disabled', false);
    $('select').prop('disabled', false);
  }
  else {
    setTimeout(function functionName() {
      window.location.reload();
    }, 1000);
  }


}



function ldgOffDebug(btn, content) {
  console.log("New loader");
  $(btn).removeClass('on-loading');
  $(btn).attr('disabled',false);
  $(btn).html(content);

}

function ldgOnSmallBtn(button, state, val=""){
  $(button).prop("disabled", state);
  if(state == true)
    $(button).html(val);
  else{
    $(button).prop("disabled", state);
    $(button).html(val);
  }
}

function sosAlrt(id, txt, st) {

  if(st == 1 ){
    $(id).removeClass('alert-danger');
    $(id).addClass('alert-success');
    var btn = $(":button[type='reset']");

    if (btn)
      btn.click();

  }
  else {
      $(id).removeClass('alert-success');
      $(id).addClass('alert-danger');
  }

  $(id).html(txt).fadeIn('slow').removeClass('d-none');
  $("html, body").animate({ scrollTop: 0 }, 600);



  setTimeout(function () {
    $(id).fadeOut('slow');
  }, 50000000);

}



/* ABTRACTS */

$("#btn-abstract-validate").click(function (event) {
  event.preventDefault();
  publishAbstract($(this));
})

$("#btn-abstract-disable").click(function (event) {
  event.preventDefault();
  disableAbstract($(this));
})

$("#btn-abstract-publish").click(function (event) {
  event.preventDefault();
  publishAbstract($(this));
})

$("#btn-abstract-reject").click(function (event) {
  event.preventDefault();
  rejectAbstract($(this));
})

$("#btn-abstract-reject-init").click(function (event) {
  event.preventDefault();
  $("#rejectModalTitle").html("Rejeter l'abstract n°"+$(this).data('abstract-id'));
})




function publishAbstract(btn) {
  var formData = new FormData();
  var abstract = btn.data('abstract-id');
  var btnContent = $(btn).html();

  $.ajax({
    url: domain+'/abstract/'+abstract+'/action/publish',
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    cache: false,
    beforeSend: function(){
      ldgOn(btn);
    },
    statusCode: {
      200: function(responseObject, textStatus, errorThrown) {
        //txt = "Participation enregistrée avec succès !<br>Un conseiller vous appelera pour la confirmation.";
        txt = "<h4>Opération réussie!</h4>";
        switch (responseObject) {
          case '1':
            btnContent = "Fermer";
            txt = "<h4>Opération réussie!</h4> L'abstract a été publié !";
            ldgOff(btn,btnContent, false);

            break;
          case '2':
            txt = "<h4>L'abstract est déjà publié !</h4> ";
            break;
          case '3':
            txt = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
            ldgOff(btn,btnContent);
            break;
          case '4':
            txt = "Une erreur a été rencontrée !";
            ldgOff(btn,btnContent);
            break;
          default:
        }
        sosAlrt("#alert-form", txt, parseInt(responseObject));
        ldgOff(btn,btnContent);

      },
      500: function(responseObject, textStatus, errorThrown) {
        txt3 = "<h4>Opération échouée!</h4> Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
        console.log(responseObject);
        ldgOff(btn,btnContent);
        sosAlrt("#alert-form", txt3, parseInt(responseObject));
      },
    }
  }).done(function( data ) {
    ldgOn(btn,false,btnContent);
  });
}


function disableAbstract(btn) {
  var formData = new FormData();
  var abstract = btn.data('abstract-id');
  var btnContent = $(btn).html();

  $.ajax({
    url: domain+'/abstract/'+abstract+'/action/disable',
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    cache: false,
    beforeSend: function(){
      ldgOn(btn);
    },
    statusCode: {
      200: function(responseObject, textStatus, errorThrown) {
        //txt = "Participation enregistrée avec succès !<br>Un conseiller vous appelera pour la confirmation.";
        txt = "<h4>Opération réussie!</h4>";
        switch (responseObject) {
          case '1':
            btnContent = "Fermer";
            txt = "<h4>Opération réussie!</h4> L'abstract a été désactivé !";
            ldgOff(btn,btnContent, false);
            break;
          case '2':
            txt = "<h4>L'abstract est déjà désactivé !</h4> ";
            break;
          case '3':
            txt = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
            ldgOff(btn,btnContent);
            break;
          case '4':
            txt = "Le scan a été déjà effectué !";
            ldgOff(btn,btnContent);
            break;
          default:
        }
        sosAlrt("#alert-form", txt, parseInt(responseObject));
        ldgOff(btn,btnContent);

      },
      500: function(responseObject, textStatus, errorThrown) {
        txt3 = "<h4>Opération échouée!</h4> Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
        console.log(responseObject);
        //ldgOff(btn,btnContent);
        sosAlrt("#alert-form", txt3, parseInt(responseObject));
      },
    }
  }).done(function( data ) {
    ldgOn(btn,false,btnContent);
  });
}


function rejectAbstract(btn) {
  var formData = new FormData();
  var abstract = btn.data('abstract-id');
  var btnContent = $(btn).html();

  formData.append("motif_rejet", $("#motif_rejet").val());

  $.ajax({
    url: domain+'/abstract/'+abstract+'/action/reject',
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    cache: false,
    beforeSend: function(){
      ldgOn(btn);
    },
    statusCode: {
      200: function(responseObject, textStatus, errorThrown) {
        //txt = "Participation enregistrée avec succès !<br>Un conseiller vous appelera pour la confirmation.";
        txt = "<h4>Opération réussie!</h4>";
        switch (responseObject) {
          case '1':
            btnContent = "Fermer";
            txt = "<h4>Opération réussie!</h4> L'abstract a été désactivé !";
            ldgOff(btn,btnContent, false);
            break;
          case '2':
            txt = "<h4>L'abstract est déjà désactivé !</h4> ";
            break;
          case '3':
            txt = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
            ldgOff(btn,btnContent);
            break;
          case '4':
            txt = "Le scan a été déjà effectué !";
            ldgOff(btn,btnContent);
            break;
          default:
        }
        sosAlrt("#alert-form", txt, parseInt(responseObject));
        ldgOff(btn,btnContent);

      },
      500: function(responseObject, textStatus, errorThrown) {
        txt3 = "<h4>Opération échouée!</h4> Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
        console.log(responseObject);
        //ldgOff(btn,btnContent);
        sosAlrt("#alert-form", txt3, parseInt(responseObject));
      },
    }
  }).done(function( data ) {
    ldgOn(btn,false,btnContent);
    $("#rejectModal").modal('toggle');
  });
}
