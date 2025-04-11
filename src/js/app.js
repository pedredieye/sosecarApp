
jQuery.validator.addMethod("notEqual", function(value, element, param) {
 return this.optional(element) || value != $(param).val();
}, "This has to be different...");

var validobj = $("form[name='sos-register']").validate({
   // Specify validation rules
   ignore: [],
   rules: {
       fname: {
           required: !0
       },
       lname: {
           required: !0
       },
       email: {
         required: !0,
         email: !0
       },
       year_of_birth: {
           required: !0
       },
       gender: {
           required: !0
       },
       country: {
           required: !0
       },
       state: {
           required: !0
       },
       phone: {
           required: !0
       },
       job: {
           required: !0
       },
       title: {
           required: !0,
           notEqual: "0"

       }
   },
   errorClass: "help-block error",
   highlight: function(e) {
       $(e).closest(".form-group.row > div").addClass("has-error");
   },
   unhighlight: function(e) {
       $(e).closest(".form-group.row > div").removeClass("has-error")

   },
   // Specify validation error messages
   messages: {
     fname: "Cette information est requise",
     lname: "Cette information est requise",
     email: "L'adresse mail fournie n'est pas valide",
     fname: "Cette information est requise",
     title: "Cette information est requise",
     job: "Cette information est requise",
   },
   // Make sure the form is submitted to the destination defined
   // in the "action" attribute of the form when valid
   submitHandler: function(form) {
    // event.preventDefault();

     //addNewParticipant($("form[name='sos-register']"));
     //addNewsletterSubscription($("form[name='js-sa-form-newsletter']"));
   }


 });

 var validobj = $("form[name='sos-atelier']").validate({
  // Specify validation rules
  ignore: [],
  rules: {
      fname: {
          required: !0
      },
      lname: {
          required: !0
      },
      email: {
        required: !0,
        email: !0
      },
      year_of_birth: {
          required: !0
      },
      gender: {
          required: !0
      },
      country: {
          required: !0
      },
      state: {
          required: !0
      },
      phone: {
          required: !0
      },
      job: {
          required: !0
      },
      title: {
          required: !0,
          notEqual: "0"

      }
  },
  errorClass: "help-block error",
  highlight: function(e) {
      $(e).closest(".form-group.row > div").addClass("has-error");
  },
  unhighlight: function(e) {
      $(e).closest(".form-group.row > div").removeClass("has-error")

  },
  // Specify validation error messages
  messages: {
    fname: "Cette information est requise",
    lname: "Cette information est requise",
    email: "L'adresse mail fournie n'est pas valide",
    fname: "Cette information est requise",
    title: "Cette information est requise",
    job: "Cette information est requise",
  },
  // Make sure the form is submitted to the destination defined
  // in the "action" attribute of the form when valid
  submitHandler: function(form) {
   // event.preventDefault();
    console.log("Aux ateliers");
    //addNewParticipantAtelier($("form[name='sos-atelier']"));
    //addNewsletterSubscription($("form[name='js-sa-form-newsletter']"));
  }


});

 var validobj = $("form[name='sos-login']").validate({
    // Specify validation rules
    ignore: [],
    rules: {
        email: {
          required: !0,
          email: !0
        },
        password: {
            required: !0
        }
    },
    errorClass: "help-block error",
    highlight: function(e) {
        $(e).closest(".form-group.row > div").addClass("has-error");
    },
    unhighlight: function(e) {
        $(e).closest(".form-group.row > div").removeClass("has-error")

    },
    // Specify validation error messages
    messages: {
      email: "L'adresse mail fournie n'est pas valide",
      password: "Cette information est requise"
    },
    // Make sure the form is submitted to the destination defined
    // in the "action" attribute of the form when valid
    submitHandler: function(form) {
      console.log("Login ...");
      //login($("form[name='sos-login']"), pageToLoad = domain+"/participant/home");

      //event.preventDefault();
      //addNewsletterSubscription($("form[name='js-sa-form-newsletter']"));
    }


  });


 var validobj = $("form[name='sos-get-params']").validate({
    // Specify validation rules
    ignore: [],
    rules: {
        email: {
          required: !0,
          email: !0
        }
    },
    errorClass: "help-block error",
    highlight: function(e) {
        $(e).closest(".form-group.row > div").addClass("has-error");
    },
    unhighlight: function(e) {
        $(e).closest(".form-group.row > div").removeClass("has-error")

    },
    // Specify validation error messages
    messages: {
      email: "L'adresse mail fournie n'est pas valide"
    },
    // Make sure the form is submitted to the destination defined
    // in the "action" attribute of the form when valid
    submitHandler: function(form) {
      console.log("Checking ...");
      //event.preventDefault();
      //addNewsletterSubscription($("form[name='js-sa-form-newsletter']"));
    }


  });


  var validobj = $("form[name='sos-add-abstract']").validate({
     // Specify validation rules
     ignore: [],
     rules: {
         abs_title: {
             required: !0
         },
         authors: {
             required: !0
         },
         address: {
           required: !0
         },
         resume: {
             required: !0
         },
         up: {
             required: !0
         },
         sendby: {
             required: !0
         },
         name_sender: {
             required: !0
         },
         address_sender: {
             required: !0
         },
         address_email_sender: {
             required: !0,
             email: !0
         }
     },
     errorClass: "help-block error",
     highlight: function(e) {
         $(e).closest(".form-group.row > div").addClass("has-error");
     },
     unhighlight: function(e) {
         $(e).closest(".form-group.row > div").removeClass("has-error")

     },
     // Specify validation error messages
     messages: {
       abs_title: "Cette information est requise",
       authors: "Cette information est requise",
       email: "L'adresse mail fournie n'est pas valide",
       address: "Cette information est requise",
       resume: "Cette information est requise",
       up: "Cette information est requise",
       sendby: "Cette information est requise",
       name_sender: "Cette information est requise",
       address_sender: "Cette information est requise",
       address_email_sender: "L'adresse mail fournie n'est pas valide",
     },
     // Make sure the form is submitted to the destination defined
     // in the "action" attribute of the form when valid
     submitHandler: function(form) {
        //event.preventDefault();
        //addNewAbstract($("form[name='sos-add-abstract']"));

       //addNewsletterSubscription($("form[name='js-sa-form-newsletter']"));
     }


   });



 $(document).on("change", ".select2-offscreen", function() {
      validobj.form();
 });

// Select 2
$(".to-select2").select2();
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

var monthNames = [
   "Janvier", "Février", "Mars",
   "Avril", "Mai", "Juin", "Juillet",
   "Aout", "Septembre", "Octobre",
   "Novembre", "Décembre"
 ];

$('.to-date-picker').datepicker({
    startView: 1,
    language: "fr_FR",
  //  defaultViewDate: today,
    todayBtn: "linked",
    keyboardNavigation: false,
    forceParse: false,
    autoclose: true,
    format: 'yyyy-mm-dd'
});

//////////////////////////////////////////////////////////////////////////////////////////

var domain = $("#domain").data("domain");
var urlRedirect = $("#domain").data("url-redirect");

// Add new user
$("form[name='sos-register']").submit(function (event) {
  console.log("Registration .....");
  event.preventDefault();
  if ($("form[name='sos-register']").valid()){
    addNewParticipant($(this));
  }
})


// Add new user
$("form[name='sos-atelier']").submit(function (event) {
  console.log("Registration aux ateliers.....");
  event.preventDefault();
  if ($("form[name='sos-atelier']").valid()){
    addNewParticipantAtelier($(this));
  }
})


$("form[name='sos-atelier-pratique']").submit(function (event) {
  console.log("Registration aux ateliers.....");
  event.preventDefault();
  if ($("form[name='sos-atelier-pratique']").valid()){
    addNewParticipantAtelierPratique($(this));
  }
})

// Login
$("form[name='sos-login']").submit(function (event) {
  event.preventDefault();
  console.log("On loading");
  if ($("form[name='sos-login']").valid()){
    login($(this), pageToLoad = domain+"/participant/home");
  }
})



// Get Params
$("form[name='sos-get-params']").submit(function (event) {
  event.preventDefault();
  if ($("form[name='sos-get-params']").valid()){
    sendParams($(this));
  }
})


// Add new abstract
$("form[name='sos-add-abstract']").submit(function (event) {
  console.log("Addind abstract .....");
  event.preventDefault();
  if ($("form[name='sos-add-abstract']").valid()){
    addNewAbstract($(this));
  }
})


// Access to live
$(".link-live").on("click", function (event) {
  console.log($(this));
    newAccessLive($(this));
})


// Get certififation
$("form[name='sos-attestation']").submit(function (event) {
  event.preventDefault();
  if ($("form[name='sos-attestation']").valid()) {
    generateCertificat($(this));
  }
})


function newAccessLive(btn) {
    //idSalle = btn.dataset.salleId;
    idSalle = btn.data("salle-id");

    var formData = new FormData();
    formData.append("salle", idSalle);

    $.ajax({
      url: domain+'/stats/live/new',
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      cache: false,
      beforeSend: function(){
        //console.log(formData);

      },
      statusCode: {
        200: function(responseObject, textStatus, errorThrown) {

          //console.log(responseObject);
        },
        500: function(responseObject, textStatus, errorThrown) {
          //console.log(responseObject);
        },
      }
    }).done(function( data ) {

    });
}

function login(f,  pageToLoad) {

  var data = f.serializeArray();
  var formData = new FormData();

  jQuery.each( data, function( i, field ) {
    formData.append(field.name, field.value);
  });

  var btn = f.find(":button[type='submit']");
  var btnContent = $(btn).html();

  $.ajax({
    url: domain+'/participant/login/check',
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    cache: false,
    beforeSend: function(){
      //console.log(formData);
      formData.forEach((item, i) => {
        console.log(item);
      });
      ldgOn(btn);
    },
    statusCode: {
      200: function(responseObject, textStatus, errorThrown) {

        txt = "Connexion validée !<br> Redirection en cours... <br> Si la page ne se recharge pas au bout d'un moment, Veuillez cliquer <a href='"+domain+"/participant/home'> ici</a>";
        console.log(responseObject);
        switch (responseObject) {
          case '1':
            txt = "Connexion validée !<br> Redirection en cours... <br> Si la page ne se recharge pas au bout d'un moment, Veuillez cliquer <a href='"+domain+"/participant/home'> ici</a>";
            saAlrt("#alert-login", txt, parseInt(responseObject));
            console.log(urlRedirect);

                
              setTimeout(function () {
                if(urlRedirect == '/' || urlRedirect == '' )
                  window.location.replace(pageToLoad);
                else
                  window.location.replace(urlRedirect);
              }, 2000);


            break;
          case '2':
            txt = "Les identifiants fournis ne sont pas valides";
            break;
          case '3':
            txt = "Aucun compte trouvé.";
            break;
          default:
        }
        //console.log(responseObject);
        saAlrt("#alert-login", txt, parseInt(responseObject));
      },
      500: function(responseObject, textStatus, errorThrown) {
        txt3 = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
        ldgOff(btn,btnContent);
        saAlrt("#alert-login", txt3, parseInt(responseObject));
      },
    }
  }).done(function( data ) {

    ldgOff(btn,btnContent);
  });

}

function sendParams(f) {


  var data = f.serializeArray();
  var formData = new FormData();

  jQuery.each( data, function( i, field ) {
    formData.append(field.name, field.value);
  });

  var btn = f.find(":button[type='submit']");
  var btnContent = $(btn).html();

  $.ajax({
    url: domain+'/participant/params/send/check',
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    cache: false,
    beforeSend: function(){
      formData.forEach((item, i) => {
      });
      ldgOn(btn);
    },
    statusCode: {
      200: function(responseObject, textStatus, errorThrown) {

        txt = "Paramètres envoyés !<br> Les paramètre de connexion ont été envoyés à l'adresse fournie.";

        switch (responseObject) {
          case '1':
            txt = "Paramètres envoyés !<br> Les paramètres de connexion ont été envoyés à l'adresse fournie.";
            saAlrt("#alert-params", txt, parseInt(responseObject));

            break;
          case '2':
            txt = "Aucun compte trouvé ou compte inactif.";
            break;
          case '3':
            txt = "Aucun compte trouvé ou compte inactif.";
            break;
          default:
        }
        saAlrt("#alert-params", txt, parseInt(responseObject));
      },
      500: function(responseObject, textStatus, errorThrown) {
        txt3 = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
        ldgOff(btn,btnContent);
        saAlrt("#alert-params", txt3, parseInt(responseObject));
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
  formData.append("service", "congres");

  var btn = f.find(":button[type='submit']");
  var btnContent = $(btn).html();


  $.ajax({
    //url: domain+'/participants/new/save',
    url: domain+'/participant/new',
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    cache: false,
    beforeSend: function(){
      //console.log(formData);
      formData.forEach((item, i) => {
        //console.log(item);
      });

      ldgOn(btn);
    },
    statusCode: {
      201: function(responseObject, textStatus, errorThrown) {
        console.log(responseObject.responseText);
        console.log(responseObject);
        console.log(responseObject.ref);
        txt = "Participation enregistrée avec succès !<br>Le secrétariat de la SOSECAR vous contactera pour la confirmation. <br><br> Une fois l'inscription validée, vous recevrez un mail de confirmation. <br> (Merci de vérifier vos spams.)";
        
        saAlrt("#alert-register", txt, 1);
        
        window.location.href = domain+"/pay/init/congres/"+responseObject.ref;

        //showAlertNewsletter(f, true);
      },
      204: function(responseObject, textStatus, errorThrown) {
        txt = "Une participation a été déjà ajoutée avec ce compte !<br> Veuillez choisir une autre adresse mail.";
        console.log(responseObject);
        saAlrt("#alert-register", txt, 2);
        //showAlertNewsletter(f, true);
      },
      206: function(responseObject, textStatus, errorThrown) {
        txt = "Des informations requises sont manquantes pour enregistrer votre participation, veuillez rééssayer.";
        saAlrt("#alert-register", txt, 3);
        //showAlertNewsletter(f, true);
      },
      500: function(responseObject, textStatus, errorThrown) {
        txt3 = "Des erreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
        console.log("error");
        console.log(responseObject.responseText);
        console.log(responseObject);
        ldgOff(btn,btnContent);
        saAlrt("#alert-register", txt3, parseInt(responseObject));
      },
    }
  }).done(function( data ) {
    ldgOff(btn,btnContent);
    console.log("done");
  });

}


function addNewParticipantAtelier(f) {

  var data = f.serializeArray();
  var formData = new FormData();

  jQuery.each( data, function( i, field ) {
    formData.append(field.name, field.value);
  });

  var activite1 = $("#activite1").is(":checked") ? "checked" : "unchecked";
  var activite2 = $("#activite2").is(":checked") ? "checked" : "unchecked";

  formData.append("activite1", activite1);
  formData.append("activite2", activite2);

  formData.append("service", "precongres");


  var btn = f.find(":button[type='submit']");
  var btnContent = $(btn).html();


  $.ajax({
    url: domain+'/participants/atelier/new/save',
    //url: domain+'/participant/new',
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    cache: false,
    beforeSend: function(){
      //console.log(formData);
      formData.forEach((item, i) => {
        //console.log(item);
      });

      ldgOn(btn);
    },
    statusCode: {
      201: function(responseObject, textStatus, errorThrown) {
        txt = "Participation enregistrée avec succès !<br>Le secrétariat de la SOSECAR vous contactera pour la confirmation. <br><br> Une fois l'inscription validée, vous recevrez un mail de confirmation. <br> (Merci de vérifier vos spams.)";
        console.log(responseObject.responseText);
        console.log(responseObject);
        console.log(responseObject.ref);
        
        saAlrt("#alert-register", txt, 1);
        
       //window.location.href = domain+"/pre/pay/init/"+responseObject.ref;
        //window.location.href = domain+"/pay/precongres/init/"+responseObject.ref;
        window.location.href = domain+"/pay/init/precongres/"+responseObject.ref;
        //showAlertNewsletter(f, true);
      },
      204: function(responseObject, textStatus, errorThrown) {
        txt = "Une participation a été déjà ajoutée avec ce compte !<br> Veuillez choisir une autre adresse mail.";
        console.log(responseObject);
        saAlrt("#alert-register", txt, 2);
        //showAlertNewsletter(f, true);
      },
      206: function(responseObject, textStatus, errorThrown) {
        txt = "Des informations requises sont manquantes pour enregistrer votre participation, veuillez rééssayer.";
        saAlrt("#alert-register", txt, 3);
        //showAlertNewsletter(f, true);
      },
      500: function(responseObject, textStatus, errorThrown) {
        txt3 = "Des erreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
        console.log("error");
        console.log(responseObject.responseText);
        console.log(responseObject);
        ldgOff(btn,btnContent);
        saAlrt("#alert-register", txt3, parseInt(responseObject));
      },
    }
  }).done(function( data ) {
    ldgOff(btn,btnContent);
    console.log("done");
  });

}



function addNewParticipantAtelierPratique(f) {

  var data = f.serializeArray();
  var formData = new FormData();

  jQuery.each( data, function( i, field ) {
    formData.append(field.name, field.value);
  });

  var activite1 = $("#activite1").is(":checked") ? "checked" : "unchecked";
  var activite2 = $("#activite2").is(":checked") ? "checked" : "unchecked";
  var activite3 = $("#activite3").is(":checked") ? "checked" : "unchecked";
  var activite4 = $("#activite4").is(":checked") ? "checked" : "unchecked";
  var activite5 = $("#activite5").is(":checked") ? "checked" : "unchecked";

  formData.append("activite1", activite1);
  formData.append("activite2", activite2);
  formData.append("activite3", activite3);
  formData.append("activite4", activite4);
  formData.append("activite5", activite5);

  formData.append("service", "ateliers");


  var btn = f.find(":button[type='submit']");
  var btnContent = $(btn).html();


  $.ajax({
    url: domain+'/participants/atelier/pratique/new/save',
    //url: domain+'/participant/new',
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    cache: false,
    beforeSend: function(){
      //console.log(formData);
      formData.forEach((item, i) => {
        //console.log(item);
      });

      ldgOn(btn);
    },
    statusCode: {
      201: function(responseObject, textStatus, errorThrown) {
        txt = "Participation enregistrée avec succès !<br>Le secrétariat de la SOSECAR vous contactera pour la confirmation. <br><br> Une fois l'inscription validée, vous recevrez un mail de confirmation. <br> (Merci de vérifier vos spams.)";
        console.log(responseObject.responseText);
        console.log(responseObject);
        console.log(responseObject.ref);
        
        saAlrt("#alert-register", txt, 1);
        
       //window.location.href = domain+"/pre/pay/init/"+responseObject.ref;
        //window.location.href = domain+"/pay/precongres/init/"+responseObject.ref;
        //window.location.href = domain+"/pay/init/precongres/"+responseObject.ref;
        //showAlertNewsletter(f, true);
      },
      204: function(responseObject, textStatus, errorThrown) {
        txt = "Une participation a été déjà ajoutée avec ce compte !<br> Veuillez choisir une autre adresse mail.";
        console.log(responseObject);
        saAlrt("#alert-register", txt, 2);
        //showAlertNewsletter(f, true);
      },
      206: function(responseObject, textStatus, errorThrown) {
        txt = "Des informations requises sont manquantes pour enregistrer votre participation, veuillez rééssayer.";
        saAlrt("#alert-register", txt, 3);
        //showAlertNewsletter(f, true);
      },
      500: function(responseObject, textStatus, errorThrown) {
        txt3 = "Des erreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
        console.log("error");
        console.log(responseObject.responseText);
        console.log(responseObject);
        ldgOff(btn,btnContent);
        saAlrt("#alert-register", txt3, parseInt(responseObject));
      },
    }
  }).done(function( data ) {
    ldgOff(btn,btnContent);
    console.log("done");
  });

}


function addNewAbstract(f) {

    var data = f.serializeArray();
    var formData = new FormData();

    jQuery.each( data, function( i, field ) {
      formData.append(field.name, field.value);
    });

    var files = $('#up')[0].files;
    // Check file selected or not
    if(files.length > 0 )
       formData.append('file',files[0]);

    var btn = f.find(":button[type='submit']");
    var btnContent = $(btn).html();

    $.ajax({
      url: domain+'/abstracts/new/save',
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      cache: false,
      beforeSend: function(){
        //console.log(formData);
        formData.forEach((item, i) => {
          //console.log(i+" -> "+item);
        });

        ldgOn(btn);
      },
      statusCode: {
        200: function(responseObject, textStatus, errorThrown) {
          //txt = "Nous accusons bonne réception de votre article et vous reviendrons après validation.<br>Le comité scientifique<br>";
          //console.log(responseObject.responseText);
          //console.log(responseObject);
          switch (responseObject) {
            case '1':
              txt = "Nous accusons bonne réception de votre article et vous reviendrons après validation.<br>Le comité scientifique<br>";
              break;
            case '2':
              txt = "Votre article ne respecte pas les normes indiquées. <br>Nous vous prions de le rectifier avant renvoi. Merci<br>Le comité scientifique";
              break;
            case '3':
              txt = "Votre article ne respecte pas les normes indiquées. <br>Nous vous prions de le rectifier avant renvoi. Merci<br>Le comité scientifique";
              //txt = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
              break;
            default:
              txt = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";

          }
          saAlrt("#alert-abstract", txt, parseInt(responseObject));
          console.log(responseObject);
          
          ldgOff(btn,btnContent);
          //clearForm(f);
          //showAlertNewsletter(f, true);
        },
        500: function(responseObject, textStatus, errorThrown) {
          txt3 = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
          //console.log(responseObject.responseText);
          //console.log(responseObject);
          ldgOff(btn,btnContent);
          saAlrt("#alert-abstract", txt3, parseInt(responseObject));
        },
      }
    }).done(function( data ) {
      //console.log(data);
      ldgOff(btn,btnContent);
      //console.log("done");
    });

}



function generateCertificat(f) {

  var data = f.serializeArray();
  var formData = new FormData();

  jQuery.each(data, function (i, field) {
    formData.append(field.name, field.value);
  });

  var btn = f.find(":button[type='submit']");
  var btnContent = $(btn).html();

  var nammefile = $('#name').val();

  console.log(nammefile);

  $.ajax({
    url: domain + '/participant/certificat/generate',
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    cache: false,
    beforeSend: function () {
      ldgOn(btn);
    },
    statusCode: {
      200: function (responseObject, textStatus, errorThrown) {
        console.log(responseObject.responseText);
        txt = "Opération réussie !<br> Votre certificat est en cours de téléchargement ...";
        
        switch (responseObject) {
          case '1':
            txt = "Opération réussie !<br> Votre certificat est en cours de téléchargement ...";
            saAlrt("#gen-certif-notif", txt, parseInt(responseObject));

            $('body').append('<a id="link_certif" download="' + nammefile + '.pdf" href="' + domain + '/src/doc/certificats/' + nammefile + '.pdf' + '">&nbsp;</a>');
            $('#link_certif')[0].click();
            setTimeout(() => {
              window.location.reload();
            }, 1000);
            break;

          case '2':
            txt = "Une erreur a été rencontrée (No data received). Veuillez réessayer plus tard.";
            break;

          case '3':
            txt = "Votre adresse mail n'est pas reconnue. Vous devez avoir un compte valide avant de télécharger un certificat.";
            break;

          default:

        }
        console.log(responseObject);
        saAlrt("#gen-certif-notif", txt, parseInt(responseObject));

      },
      500: function (responseObject, textStatus, errorThrown) {
        txt3 = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
        ldgOff(btn, btnContent);
        saAlrt("#gen-certif-notif", txt3, parseInt(responseObject));
      },
    }
  }).done(function (data) {
    console.log(data);
    ldgOff(btn, btnContent);
  });
}




$("#dwl-qr").on('click', function (e) {
  e.preventDefault();

  window.location.href = $(this).data('file-src');
})

$("#dwl-cr").on('click', function (e) {
  e.preventDefault();
  downloadCertificat($(this));

})


$("#dwl-bd").on('click', function (e) {
  e.preventDefault();
  downloadBadge($(this));

})


var imgLoadingWhite = '<img src="'+domain+'/src/img/Rolling-1s-41px-white.svg" alt="loading" class="sa-one-training-course-bloc-ico" width="25">';


function downloadCertificat(btn) {

    var formData = new FormData();
    formData.append("id", btn.data('id'));


    var btnContent = btn.html();

    $.ajax({
      url: domain+'/participant/certificat/generate',
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      cache: false,
      beforeSend: function(){
        formData.forEach((item, i) => {
          console.log(i+" -> "+item);
        });

        ldgOn(btn);
      },
      statusCode: {
        200: function(responseObject, textStatus, errorThrown) {
          console.log(responseObject.responseText);
          txt = "Opération réussie !<br> Votre certificat est en cours de téléchargement ...";

          switch (responseObject) {
            case '1':
              txt = "Opération réussie !<br> Votre certificat est en cours de téléchargement ...";
              saAlrt("#gen-certif-notif", txt, parseInt(responseObject));

              $('body').append('<a id="link_certif" download="'+btn.data('name')+'" href="'+domain+'/src/doc/certificats/'+btn.data('name')+'.pdf'+'">&nbsp;</a>');
              $('#link_certif')[0].click();

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
          saAlrt("#gen-certif-notif", txt, parseInt(responseObject));
        },
        500: function(responseObject, textStatus, errorThrown) {
          txt3 = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
          ldgOff(btn,btnContent);
          saAlrt("#gen-certif-notif", txt3, parseInt(responseObject));
        },
      }
    }).done(function( data ) {
      console.log(data);
      ldgOff(btn,btnContent);
    });
}


function downloadBadge(btn) {

  var formData = new FormData();
  formData.append("id", btn.data('id'));

  var btnContent = btn.html();

  $.ajax({
    url: domain+'/participant/badge/generate',
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    cache: false,
    beforeSend: function(){
      formData.forEach((item, i) => {
        console.log(i+" -> "+item);
      });

      ldgOn(btn);
    },
    statusCode: {
      200: function(responseObject, textStatus, errorThrown) {
        console.log(responseObject.responseText);
        txt = "Opération réussie !<br> Votre badge est en cours de téléchargement ...";

        switch (responseObject) {
          case '1':
            txt = "Opération réussie !<br> Votre badge est en cours de téléchargement ...";
            saAlrt("#gen-badge-notif", txt, parseInt(responseObject));

            $('body').append('<a id="link_badge" download="'+btn.data('name')+'" href="'+domain+'/src/doc/badges/'+btn.data('name')+'.pdf'+'">&nbsp;</a>');
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
        saAlrt("#gen-badge-notif", txt, parseInt(responseObject));
      },
      500: function(responseObject, textStatus, errorThrown) {
        txt3 = "Des érreurs ont été rencontrées.<br> Veuillez réessayer plus tard.";
        ldgOff(btn,btnContent);
        saAlrt("#gen-badge-notif", txt3, parseInt(responseObject));
      },
    }
  }).done(function( data ) {
    console.log(data);
    ldgOff(btn,btnContent);
  });
}

function ldgOn(btn, content="") {
  $(btn).addClass('on-loading');
  $(btn).attr('disabled',true);

  var w = $(btn).width();

  $(btn).html(imgLoadingWhite+content);

  if (w > 0)
    $(btn).width(w+2);

  $('input').prop('disabled', true);
  $('select').prop('disabled', true);
}

function ldgOff(btn, content) {
  $(btn).removeClass('on-loading');
  $(btn).attr('disabled',false);
  $(btn).html(content);
  $('input').prop('disabled', false);
  $('select').prop('disabled', false);
}

function saAlrt(id, txt, st) {
  if(st == 1 ){
    $(id).removeClass('alert-danger');
    $(id).addClass('alert-success');
  }
  else {
      $(id).removeClass('alert-success');
      $(id).addClass('alert-danger');
  }
  $(id).html(txt).fadeIn('slow').removeClass('d-none');

  //$("html, body").animate({ scrollTop: 0 }, 600);
  setTimeout(function () {
    $(id).fadeOut('slow');
  }, 5000000);

}


function clearForm(f) {
  f.find('input:text, input, select, textarea')
    .each(function () {
        $(this).val('');
    });
}


/********************************************************************************************************************/


$('.link-schedule').on('click', function(event){
  console.log("Click");
    event.preventDefault();
    $('html,body').animate({scrollTop:$("#myTabContent").offset().top-170}, 100);

});
