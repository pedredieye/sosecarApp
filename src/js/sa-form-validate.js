

$("form[name='js-sa-form-newsletter']").validate({
   // Specify validation rules
   rules: {
     user_email: {
       required: true,
       email: true
     }
   },
   // Specify validation error messages
   messages: {
     user_email: "Please enter a valid email address"
   },
   // Make sure the form is submitted to the destination defined
   // in the "action" attribute of the form when valid
   submitHandler: function(form) {
     console.log("Registration ...");
     event.preventDefault();
     addNewsletterSubscription($("form[name='js-sa-form-newsletter']"));
   }
 });

$("form[name='js-sa-form-register']").validate({
   // Specify validation rules
   rules: {
     sa_new_user_full_name: "required",
     sa_new_user_email: {
       required: true,
       email: true
     },
     sa_new_user_pwd: {
       required: true,
       minlength: 5
     },
     sa_cond_check: {
       required: true
     }
   },
   // Specify validation error messages
   messages: {
     sa_new_user_full_name: "Please enter your name",
     lastname: "Please enter your lastname",
     sa_new_user_pwd: {
       required: "Please provide a password",
       minlength: "Your password must be at least 5 characters long"
     },
     sa_new_user_email: "Please enter a valid email address",
     sa_cond_check: "Please accept the CGU"
   },
   // Make sure the form is submitted to the destination defined
   // in the "action" attribute of the form when valid
   submitHandler: function(form) {
     console.log("Registration ...");
     event.preventDefault();
     nwUsr($("form[name='js-sa-form-register']"));
   }
 });



 $("form[name='js-sa-form-login']").validate({
    // Specify validation rules
    rules: {
      sa_user_email: {
        required: function(){
                 return $('input[name="sa_user_secret_code"]').val() == '';
            },
        email: true
      },
      sa_user_pwd: {
        required:  function(){
                 return $('input[name="sa_user_secret_code"]').val() == '';
            }
      },
      sa_user_secret_code: {
        required:  function(){
                 return $('input[name="sa_user_email"]').val() == '';
            },
      }
    },
    // Specify validation error messages
    messages: {
      sa_user_secret_code: "Please enter your secret code",
      sa_user_email: {
        required: "Please enter a valid email address",
        email: "Please enter a valid email address"
      },
      sa_user_pwd: "Empty password"
    },
    // Make sure the form is submitted to the destination defined
    // in the "action" attribute of the form when valid
    submitHandler: function(form) {
      console.log("Tentative de connexion ...");
      event.preventDefault();
      usrIn($("form[name='js-sa-form-login']"));
    }
  });
