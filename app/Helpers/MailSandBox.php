<?php

namespace App\Helpers;


use App\Helpers\DBIP;
use App\Helpers\Helper;
use App\Helpers\Browser;
use App\Helpers\RandomStringGenerator;

use App\Models\Product;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailSandBox
{
  public static function sendMail($container, $to, $subject, $data)
  {
    header('Content-Type: text/html; charset=utf-8');

    $message ="
      <!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional //EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>

      <html xmlns='http://www.w3.org/1999/xhtml' xmlns:o='urn:schemas-microsoft-com:office:office'>

        <head>
                <meta charset='UTF-8'>
                <meta content='width=device-width, initial-scale=1' name='viewport'>
                <meta name='x-apple-disable-message-reformatting'>
                <meta http-equiv='X-UA-Compatible' content='IE=edge'>
                <meta content='telephone=no' name='format-detection'>
                <title></title>
                <!--[if (mso 16)]> <style type='text/css'> a {text-decoration: none;} </style> <![endif]-->
                <!--[if gte mso 9]><style>sup { font-size: 100% !important; }</style><![endif]-->
                <!--[if !mso]><!-- -->
                <link href='https://fonts.googleapis.com/css?family=Montserrat:500,800' rel='stylesheet'>
                <!--<![endif]-->
                <!--[if gte mso 9]>
                    <xml>
                    <o:OfficeDocumentSettings>
                    <o:AllowPNG></o:AllowPNG>
                    <o:PixelsPerInch>96</o:PixelsPerInch>
                    </o:OfficeDocumentSettings>
                </xml>
            <![endif]-->
                <style>
                /*
            CONFIG STYLES
            Please do not delete and edit CSS styles below
            */
            /* IMPORTANT THIS STYLES MUST BE ON FINAL EMAIL */
            #outlook a {
            padding: 0;
            }

            .ExternalClass {
            width: 100%;
            }

            .ExternalClass,
            .ExternalClass p,
            .ExternalClass span,
            .ExternalClass font,
            .ExternalClass td,
            .ExternalClass div {
            line-height: 100%;
            }

            .es-button {
            mso-style-priority: 100 !important;
            text-decoration: none !important;
            }

            a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
            }

            .es-desk-hidden {
            display: none;
            float: left;
            overflow: hidden;
            width: 0;
            max-height: 0;
            line-height: 0;
            mso-hide: all;
            }

            a.es-button:hover {
            border-color: #2CB543 !important;
            background: #2CB543 !important;
            }

            a.es-secondary:hover {
            border-color: #ffffff !important;
            background: #ffffff !important;
            }

            /*
            END OF IMPORTANT
            */
            s {
            text-decoration: line-through;
            }

            html,
            body {
            width: 100%;
            font-family: Montserrat, Helvetica, Roboto, Arial, sans-serif;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            }

            table {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            border-collapse: collapse;
            border-spacing: 0px;
            }

            table td,
            html,
            body,
            .es-wrapper {
            padding: 0;
            Margin: 0;
            }

            .es-content,
            .es-header,
            .es-footer {
            table-layout: fixed !important;
            width: 100%;
            }

            img {
            display: block;
            border: 0;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
            }

            table tr {
            border-collapse: collapse;
            }

            p,
            hr {
            Margin: 0;
            }

            h1,
            h2,
            h3,
            h4,
            h5 {
            Margin: 0;
            line-height: 120%;
            mso-line-height-rule: exactly;
            font-family: Montserrat, Helvetica, Roboto, Arial, sans-serif;
            }

            p,
            ul li,
            ol li,
            a {
            -webkit-text-size-adjust: none;
            -ms-text-size-adjust: none;
            mso-line-height-rule: exactly;
            }

            .es-left {
            float: left;
            }

            .es-right {
            float: right;
            }

            .es-p5 {
            padding: 5px;
            }

            .es-p5t {
            padding-top: 5px;
            }

            .es-p5b {
            padding-bottom: 5px;
            }

            .es-p5l {
            padding-left: 5px;
            }

            .es-p5r {
            padding-right: 5px;
            }

            .es-p10 {
            padding: 10px;
            }

            .es-p10t {
            padding-top: 10px;
            }

            .es-p10b {
            padding-bottom: 10px;
            }

            .es-p10l {
            padding-left: 10px;
            }

            .es-p10r {
            padding-right: 10px;
            }

            .es-p15 {
            padding: 15px;
            }

            .es-p15t {
            padding-top: 15px;
            }

            .es-p15b {
            padding-bottom: 15px;
            }

            .es-p15l {
            padding-left: 15px;
            }

            .es-p15r {
            padding-right: 15px;
            }

            .es-p20 {
            padding: 20px;
            }

            .es-p20t {
            padding-top: 20px;
            }

            .es-p20b {
            padding-bottom: 20px;
            }

            .es-p20l {
            padding-left: 20px;
            }

            .es-p20r {
            padding-right: 20px;
            }

            .es-p25 {
            padding: 25px;
            }

            .es-p25t {
            padding-top: 25px;
            }

            .es-p25b {
            padding-bottom: 25px;
            }

            .es-p25l {
            padding-left: 25px;
            }

            .es-p25r {
            padding-right: 25px;
            }

            .es-p30 {
            padding: 30px;
            }

            .es-p30t {
            padding-top: 30px;
            }

            .es-p30b {
            padding-bottom: 30px;
            }

            .es-p30l {
            padding-left: 30px;
            }

            .es-p30r {
            padding-right: 30px;
            }

            .es-p35 {
            padding: 35px;
            }

            .es-p35t {
            padding-top: 35px;
            }

            .es-p35b {
            padding-bottom: 35px;
            }

            .es-p35l {
            padding-left: 35px;
            }

            .es-p35r {
            padding-right: 35px;
            }

            .es-p40 {
            padding: 40px;
            }

            .es-p40t {
            padding-top: 40px;
            }

            .es-p40b {
            padding-bottom: 40px;
            }

            .es-p40l {
            padding-left: 40px;
            }

            .es-p40r {
            padding-right: 40px;
            }

            .es-menu td {
            border: 0;
            }

            .es-menu td a img {
            display: inline-block !important;
            }

            /*
            END CONFIG STYLES
            */
            a {
            font-family: Montserrat, Helvetica, Roboto, Arial, sans-serif;
            font-size: 16px;
            text-decoration: underline;
            }

            h1 {
            font-size: 32px;
            font-style: normal;
            font-weight: bold;
            color: #4a4a4a;
            }

            h1 a {
            font-size: 32px;
            }

            h2 {
            font-size: 24px;
            font-style: normal;
            font-weight: bold;
            color: #4a4a4a;
            }

            h2 a {
            font-size: 24px;
            }

            h3 {
            font-size: 20px;
            font-style: normal;
            font-weight: bold;
            color: #4A4A4A;
            }

            h3 a {
            font-size: 20px;
            }

            p,
            ul li,
            ol li {
            font-size: 16px;
            font-family: Montserrat, Helvetica, Roboto, Arial, sans-serif;
            line-height: 150%;
            }

            ul li,
            ol li {
            Margin-bottom: 15px;
            }

            .es-menu td a {
            text-decoration: none;
            display: block;
            }

            .es-wrapper {
            width: 100%;
            height: 100%;
            background-image: ;
            background-repeat: repeat;
            background-position: center top;
            }

            .es-wrapper-color {
            background-color: #F7F7F7;
            }

            .es-content-body {
            background-color: transparent;
            }

            .es-content-body p,
            .es-content-body ul li,
            .es-content-body ol li {
            color: #4A4A4A;
            }

            .es-content-body a {
            color: #3b2495;
            }

            .es-header {
            background-color: #34265f;
            background-repeat: repeat;
            background-position: center bottom;
            }

            .es-header-body {
            background-color: #34265f;
            }

            .es-header-body p,
            .es-header-body ul li,
            .es-header-body ol li {
            color: #ffffff;
            font-size: 14px;
            }

            .es-header-body a {
            color: #ffffff;
            font-size: 14px;
            }

            .es-footer {
            background-color: #f7f7f7;
            background-repeat: repeat;
            background-position: center top;
            background-image: url(https://ohvpkv.stripocdn.email/content/guids/CABINET_7dfb659af020be618a1cf3d530b28d98/images/75021564382669317.png);
            }

            .es-footer-body {
            background-color: #f7f7f7;
            }

            .es-footer-body p,
            .es-footer-body ul li,
            .es-footer-body ol li {
            color: #ffffff;
            font-size: 16px;
            }

            .es-footer-body a {
            color: #ffffff;
            font-size: 16px;
            }

            .es-infoblock,
            .es-infoblock p,
            .es-infoblock ul li,
            .es-infoblock ol li {
            line-height: 120%;
            font-size: 12px;
            color: #cccccc;
            }

            .es-infoblock a {
            font-size: 12px;
            color: #cccccc;
            }

            .es-button-border {
            border-style: solid solid solid solid;
            border-color: #3b2495 #3b2495 #3b2495 #3b2495;
            background: #3b2495;
            border-width: 0px 0px 0px 0px;
            display: inline-block;
            border-radius: 30px;
            width: auto;
            }

            /*
            RESPONSIVE STYLES
            Please do not delete and edit CSS styles below.

            If you don't need responsive layout, please delete this section.
            */
            @media only screen and (max-width: 600px) {
            u+#body {
                width: 100vw !important;
            }

            p,
            ul li,
            ol li,
            a {
                font-size: 16px !important;
                line-height: 150% !important;
            }

            h1 {
                font-size: 30px !important;
                text-align: center;
                line-height: 120% !important;
            }

            h2 {
                font-size: 26px !important;
                text-align: center;
                line-height: 120% !important;
            }

            h3 {
                font-size: 20px !important;
                text-align: center;
                line-height: 120% !important;
            }

            h1 a {
                font-size: 30px !important;
            }

            h2 a {
                font-size: 26px !important;
            }

            h3 a {
                font-size: 20px !important;
            }

            .es-menu td a {
                font-size: 16px !important;
            }

            .es-header-body p,
            .es-header-body ul li,
            .es-header-body ol li,
            .es-header-body a {
                font-size: 16px !important;
            }

            .es-footer-body p,
            .es-footer-body ul li,
            .es-footer-body ol li,
            .es-footer-body a {
                font-size: 16px !important;
            }

            .es-infoblock p,
            .es-infoblock ul li,
            .es-infoblock ol li,
            .es-infoblock a {
                font-size: 12px !important;
            }

            *[class='gmail-fix'] {
                display: none !important;
            }

            .es-m-txt-c,
            .es-m-txt-c h1,
            .es-m-txt-c h2,
            .es-m-txt-c h3 {
                text-align: center !important;
            }

            .es-m-txt-r,
            .es-m-txt-r h1,
            .es-m-txt-r h2,
            .es-m-txt-r h3 {
                text-align: right !important;
            }

            .es-m-txt-l,
            .es-m-txt-l h1,
            .es-m-txt-l h2,
            .es-m-txt-l h3 {
                text-align: left !important;
            }

            .es-m-txt-r img,
            .es-m-txt-c img,
            .es-m-txt-l img {
                display: inline !important;
            }

            .es-button-border {
                display: block !important;
            }

            .es-btn-fw {
                border-width: 10px 0px !important;
                text-align: center !important;
            }

            .es-adaptive table,
            .es-btn-fw,
            .es-btn-fw-brdr,
            .es-left,
            .es-right {
                width: 100% !important;
            }

            .es-content table,
            .es-header table,
            .es-footer table,
            .es-content,
            .es-footer,
            .es-header {
                width: 100% !important;
                max-width: 600px !important;
            }

            .es-adapt-td {
                display: block !important;
                width: 100% !important;
            }

            .adapt-img {
                width: 100% !important;
                height: auto !important;
            }

            .es-m-p0 {
                padding: 0px !important;
            }

            .es-m-p0r {
                padding-right: 0px !important;
            }

            .es-m-p0l {
                padding-left: 0px !important;
            }

            .es-m-p0t {
                padding-top: 0px !important;
            }

            .es-m-p0b {
                padding-bottom: 0 !important;
            }

            .es-m-p20b {
                padding-bottom: 20px !important;
            }

            .es-mobile-hidden,
            .es-hidden {
                display: none !important;
            }

            tr.es-desk-hidden,
            td.es-desk-hidden,
            table.es-desk-hidden {
                width: auto !important;
                overflow: visible !important;
                float: none !important;
                max-height: inherit !important;
                line-height: inherit !important;
            }

            tr.es-desk-hidden {
                display: table-row !important;
            }

            table.es-desk-hidden {
                display: table !important;
            }

            td.es-desk-menu-hidden {
                display: table-cell !important;
            }

            .es-menu td {
                width: 1% !important;
            }

            table.es-table-not-adapt,
            .esd-block-html table {
                width: auto !important;
            }

            table.es-social {
                display: inline-block !important;
            }

            table.es-social td {
                display: inline-block !important;
            }

            a.es-button,
            button.es-button {
                font-size: 16px !important;
                display: block !important;
                border-left-width: 0px !important;
                border-right-width: 0px !important;
            }
            }

            /*
            END RESPONSIVE STYLES
            */
            .es-p-default {
            padding-top: 20px;
            padding-right: 30px;
            padding-bottom: 0px;
            padding-left: 30px;
            }

            .es-p-all-default {
            padding: 0px;
            }

            a.es-button,
            button.es-button {
            border-style: solid;
            border-color: #3b2495;
            border-width: 12px 40px 13px 40px;
            display: inline-block;
            background: #3b2495;
            border-radius: 30px;
            font-size: 20px;
            font-family: Montserrat, Helvetica, Roboto, Arial, sans-serif;
            font-weight: normal;
            font-style: normal;
            line-height: 120%;
            color: #ffffff;
            text-decoration: none !important;
            width: auto;
            text-align: center;
            }
                </style>
                <script async custom-element='amp-list' src='https://cdn.ampproject.org/v0/amp-list-0.1.js'></script>
                <script async custom-template='amp-mustache' src='https://cdn.ampproject.org/v0/amp-mustache-0.2.js'></script>
                <script async custom-element='amp-bind' src='https://cdn.ampproject.org/v0/amp-bind-0.1.js'></script>
        </head>

      <body>
          <div class='es-wrapper-color'>
              <!--[if gte mso 9]>
            <v:background xmlns:v='urn:schemas-microsoft-com:vml' fill='t'>
              <v:fill type='tile' color='#F7F7F7'></v:fill>
            </v:background>
          <![endif]-->
              <table cellpadding='0' cellspacing='0' class='es-wrapper' width='100%' style='background-position: center top;'>
                  <tbody>
                      <tr>
                          <td valign='top' class='esd-email-paddings'>
                              <table cellpadding='0' cellspacing='0' class='es-content esd-footer-popover' align='center'>
                                  <tbody>
                                      <tr>
                                          <td class='esd-stripe' align='center'>
                                              <table bgcolor='#ffffff' class='es-content-body' align='center' cellpadding='0' cellspacing='0' width='600'>
                                                  <tbody>
                                                      <tr class='es-visible-amp-html-only'>
                                                          <td class='esd-structure es-p25t es-p20b es-p20r es-p20l' style='background-position: center bottom;' align='left'>
                                                              <table width='100%' cellspacing='0' cellpadding='0'>
                                                                  <tbody>
                                                                      <tr>
                                                                          <td class='esd-container-frame' width='558' valign='top' align='center'>
                                                                              <table width='100%' cellspacing='0' cellpadding='0'>
                                                                                  <tbody>
                                                                                      <tr>
                                                                                          <td class='esd-block-text es-p15t es-p15r es-p15l' align='left'>
                                                                                              <p><strong>Cher(ère) ".$data['first_name']."  ".$data['last_name']."</strong></p>
                                                                                          </td>
                                                                                      </tr>
                                                                                      <tr>
                                                                                          <td class='esd-block-text es-p10t es-p5b es-p15r es-p15l' align='left'>
                                                                                              <p>Nous avons le plaisir de vous confirmer votre inscription à la 6ème édition Cardiotech Sénégal du 16 au 18 décembre 2024 à l'adresse suivante : <br>Hôtel Radisson Blu de Daka<br></p>
                                                                                          </td>
                                                                                      </tr>
                                                                                      <tr>
                                                                                          <td class='esd-block-text es-p15b es-p15r es-p15l' esd-links-color='#333333' align='left'>
                                                                                          <p>Veuillez trouver ci-joint votre identifiant et votre code qr à imprimer ou à sauvegarder sur votre smartphone/tablette et à présenter à l'accueil du congrès pour impression de votre badge définitif.<br></p>
                                                                                          <p>Identifiant : ".$data['email']."</p>
                                                                                          <p>Mot de passe : ".$data['password']."</p>
                                                                                          <p>Lien d'accès : <a href='".$data['link']."'> Se connecter </a> <br><br></p>
                                                                                          </td>
                                                                                      </tr>
                                                                                      <tr>
                                                                                          <td align='center' class='esd-block-image' style='font-size: 0px;'><a target='_blank'><img class='adapt-img' src='".$container->domain_url."/".$container->qrcode_repertory.$data['qr_code']."' alt style='display: block;'></a></td>
                                                                                      </tr>
                                                                                  </tbody>
                                                                              </table>
                                                                          </td>
                                                                      </tr>
                                                                      <tr>
                                                                          <td class='esd-container-frame' width='558' valign='top' align='center'>
                                                                              <table width='100%' cellspacing='0' cellpadding='0'>
                                                                                  <tbody>
                                                                                      <tr>
                                                                                          <td class='esd-block-text es-p15b es-p15r es-p15l' esd-links-color='#333333' align='left'>
                                                                                              <p><br>Nous vous invitons à vous présenter au secrétariat dès votre arrivée munis de ce code.<br><br>Vos nom et prénom apparaîtront sur votre badge comme suit : ".$data['first_name']."  ".$data['last_name']."
                                                                                              <br>En cas d'erreur, merci de nous contacter par retour d'email.<br>Nous restons à votre disposition pour tout complément d'information.<br><br>Cordialement,<br>La Société Sénégalaise de Cardiologie</p>
                                                                                          </td>
                                                                                      </tr>
                                                                                  </tbody>
                                                                              </table>
                                                                          </td>
                                                                      </tr>
                                                                  </tbody>
                                                              </table>
                                                          </td>
                                                      </tr>
                                                  </tbody>
                                              </table>
                                          </td>
                                      </tr>
                                  </tbody>
                              </table>
                          </td>
                      </tr>
                  </tbody>
              </table>
          </div>
      </body>

      </html>

    ";


    return self::sendMailSmpt($container, $to, $subject, $message);

  }
  
  public static function sendMailPwdUser($container, $to, $subject, $data)
  {
    header('Content-Type: text/html; charset=utf-8');

    $message ="
        <!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional //EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>

        <html xmlns='http://www.w3.org/1999/xhtml' xmlns:o='urn:schemas-microsoft-com:office:office'>

        <head>
            <meta charset='UTF-8'>
            <meta content='width=device-width, initial-scale=1' name='viewport'>
            <meta name='x-apple-disable-message-reformatting'>
            <meta http-equiv='X-UA-Compatible' content='IE=edge'>
            <meta content='telephone=no' name='format-detection'>
            <title></title>
            <!--[if (mso 16)]> <style type='text/css'> a {text-decoration: none;} </style> <![endif]-->
            <!--[if gte mso 9]><style>sup { font-size: 100% !important; }</style><![endif]-->
            <!--[if !mso]><!-- -->
            <link href='https://fonts.googleapis.com/css?family=Montserrat:500,800' rel='stylesheet'>
            <!--<![endif]-->
            <!--[if gte mso 9]>
                <xml>
                    <o:OfficeDocumentSettings>
                    <o:AllowPNG></o:AllowPNG>
                    <o:PixelsPerInch>96</o:PixelsPerInch>
                    </o:OfficeDocumentSettings>
                </xml>
                <![endif]-->
                    <style>
                    /*
                CONFIG STYLES
                Please do not delete and edit CSS styles below
                */
                /* IMPORTANT THIS STYLES MUST BE ON FINAL EMAIL */
                #outlook a {
                padding: 0;
                }

                .ExternalClass {
                width: 100%;
                }

                .ExternalClass,
                .ExternalClass p,
                .ExternalClass span,
                .ExternalClass font,
                .ExternalClass td,
                .ExternalClass div {
                line-height: 100%;
                }

                .es-button {
                mso-style-priority: 100 !important;
                text-decoration: none !important;
                }

                a[x-apple-data-detectors] {
                color: inherit !important;
                text-decoration: none !important;
                font-size: inherit !important;
                font-family: inherit !important;
                font-weight: inherit !important;
                line-height: inherit !important;
                }

                .es-desk-hidden {
                display: none;
                float: left;
                overflow: hidden;
                width: 0;
                max-height: 0;
                line-height: 0;
                mso-hide: all;
                }

                a.es-button:hover {
                border-color: #2CB543 !important;
                background: #2CB543 !important;
                }

                a.es-secondary:hover {
                border-color: #ffffff !important;
                background: #ffffff !important;
                }

                /*
                END OF IMPORTANT
                */
                s {
                text-decoration: line-through;
                }

                html,
                body {
                width: 100%;
                font-family: Montserrat, Helvetica, Roboto, Arial, sans-serif;
                -webkit-text-size-adjust: 100%;
                -ms-text-size-adjust: 100%;
                }

                table {
                mso-table-lspace: 0pt;
                mso-table-rspace: 0pt;
                border-collapse: collapse;
                border-spacing: 0px;
                }

                table td,
                html,
                body,
                .es-wrapper {
                padding: 0;
                Margin: 0;
                }

                .es-content,
                .es-header,
                .es-footer {
                table-layout: fixed !important;
                width: 100%;
                }

                img {
                display: block;
                border: 0;
                outline: none;
                text-decoration: none;
                -ms-interpolation-mode: bicubic;
                }

                table tr {
                border-collapse: collapse;
                }

                p,
                hr {
                Margin: 0;
                }

                h1,
                h2,
                h3,
                h4,
                h5 {
                Margin: 0;
                line-height: 120%;
                mso-line-height-rule: exactly;
                font-family: Montserrat, Helvetica, Roboto, Arial, sans-serif;
                }

                p,
                ul li,
                ol li,
                a {
                -webkit-text-size-adjust: none;
                -ms-text-size-adjust: none;
                mso-line-height-rule: exactly;
                }

                .es-left {
                float: left;
                }

                .es-right {
                float: right;
                }

                .es-p5 {
                padding: 5px;
                }

                .es-p5t {
                padding-top: 5px;
                }

                .es-p5b {
                padding-bottom: 5px;
                }

                .es-p5l {
                padding-left: 5px;
                }

                .es-p5r {
                padding-right: 5px;
                }

                .es-p10 {
                padding: 10px;
                }

                .es-p10t {
                padding-top: 10px;
                }

                .es-p10b {
                padding-bottom: 10px;
                }

                .es-p10l {
                padding-left: 10px;
                }

                .es-p10r {
                padding-right: 10px;
                }

                .es-p15 {
                padding: 15px;
                }

                .es-p15t {
                padding-top: 15px;
                }

                .es-p15b {
                padding-bottom: 15px;
                }

                .es-p15l {
                padding-left: 15px;
                }

                .es-p15r {
                padding-right: 15px;
                }

                .es-p20 {
                padding: 20px;
                }

                .es-p20t {
                padding-top: 20px;
                }

                .es-p20b {
                padding-bottom: 20px;
                }

                .es-p20l {
                padding-left: 20px;
                }

                .es-p20r {
                padding-right: 20px;
                }

                .es-p25 {
                padding: 25px;
                }

                .es-p25t {
                padding-top: 25px;
                }

                .es-p25b {
                padding-bottom: 25px;
                }

                .es-p25l {
                padding-left: 25px;
                }

                .es-p25r {
                padding-right: 25px;
                }

                .es-p30 {
                padding: 30px;
                }

                .es-p30t {
                padding-top: 30px;
                }

                .es-p30b {
                padding-bottom: 30px;
                }

                .es-p30l {
                padding-left: 30px;
                }

                .es-p30r {
                padding-right: 30px;
                }

                .es-p35 {
                padding: 35px;
                }

                .es-p35t {
                padding-top: 35px;
                }

                .es-p35b {
                padding-bottom: 35px;
                }

                .es-p35l {
                padding-left: 35px;
                }

                .es-p35r {
                padding-right: 35px;
                }

                .es-p40 {
                padding: 40px;
                }

                .es-p40t {
                padding-top: 40px;
                }

                .es-p40b {
                padding-bottom: 40px;
                }

                .es-p40l {
                padding-left: 40px;
                }

                .es-p40r {
                padding-right: 40px;
                }

                .es-menu td {
                border: 0;
                }

                .es-menu td a img {
                display: inline-block !important;
                }

                /*
                END CONFIG STYLES
                */
                a {
                font-family: Montserrat, Helvetica, Roboto, Arial, sans-serif;
                font-size: 16px;
                text-decoration: underline;
                }

                h1 {
                font-size: 32px;
                font-style: normal;
                font-weight: bold;
                color: #4a4a4a;
                }

                h1 a {
                font-size: 32px;
                }

                h2 {
                font-size: 24px;
                font-style: normal;
                font-weight: bold;
                color: #4a4a4a;
                }

                h2 a {
                font-size: 24px;
                }

                h3 {
                font-size: 20px;
                font-style: normal;
                font-weight: bold;
                color: #4A4A4A;
                }

                h3 a {
                font-size: 20px;
                }

                p,
                ul li,
                ol li {
                font-size: 16px;
                font-family: Montserrat, Helvetica, Roboto, Arial, sans-serif;
                line-height: 150%;
                }

                ul li,
                ol li {
                Margin-bottom: 15px;
                }

                .es-menu td a {
                text-decoration: none;
                display: block;
                }

                .es-wrapper {
                width: 100%;
                height: 100%;
                background-image: ;
                background-repeat: repeat;
                background-position: center top;
                }

                .es-wrapper-color {
                background-color: #F7F7F7;
                }

                .es-content-body {
                background-color: transparent;
                }

                .es-content-body p,
                .es-content-body ul li,
                .es-content-body ol li {
                color: #4A4A4A;
                }

                .es-content-body a {
                color: #3b2495;
                }

                .es-header {
                background-color: #34265f;
                background-repeat: repeat;
                background-position: center bottom;
                }

                .es-header-body {
                background-color: #34265f;
                }

                .es-header-body p,
                .es-header-body ul li,
                .es-header-body ol li {
                color: #ffffff;
                font-size: 14px;
                }

                .es-header-body a {
                color: #ffffff;
                font-size: 14px;
                }

                .es-footer {
                background-color: #f7f7f7;
                background-repeat: repeat;
                background-position: center top;
                background-image: url(https://ohvpkv.stripocdn.email/content/guids/CABINET_7dfb659af020be618a1cf3d530b28d98/images/75021564382669317.png);
                }

                .es-footer-body {
                background-color: #f7f7f7;
                }

                .es-footer-body p,
                .es-footer-body ul li,
                .es-footer-body ol li {
                color: #ffffff;
                font-size: 16px;
                }

                .es-footer-body a {
                color: #ffffff;
                font-size: 16px;
                }

                .es-infoblock,
                .es-infoblock p,
                .es-infoblock ul li,
                .es-infoblock ol li {
                line-height: 120%;
                font-size: 12px;
                color: #cccccc;
                }

                .es-infoblock a {
                font-size: 12px;
                color: #cccccc;
                }

                .es-button-border {
                border-style: solid solid solid solid;
                border-color: #3b2495 #3b2495 #3b2495 #3b2495;
                background: #3b2495;
                border-width: 0px 0px 0px 0px;
                display: inline-block;
                border-radius: 30px;
                width: auto;
                }

                /*
                RESPONSIVE STYLES
                Please do not delete and edit CSS styles below.

                If you don't need responsive layout, please delete this section.
                */
                @media only screen and (max-width: 600px) {
                u+#body {
                    width: 100vw !important;
                }

                p,
                ul li,
                ol li,
                a {
                    font-size: 16px !important;
                    line-height: 150% !important;
                }

                h1 {
                    font-size: 30px !important;
                    text-align: center;
                    line-height: 120% !important;
                }

                h2 {
                    font-size: 26px !important;
                    text-align: center;
                    line-height: 120% !important;
                }

                h3 {
                    font-size: 20px !important;
                    text-align: center;
                    line-height: 120% !important;
                }

                h1 a {
                    font-size: 30px !important;
                }

                h2 a {
                    font-size: 26px !important;
                }

                h3 a {
                    font-size: 20px !important;
                }

                .es-menu td a {
                    font-size: 16px !important;
                }

                .es-header-body p,
                .es-header-body ul li,
                .es-header-body ol li,
                .es-header-body a {
                    font-size: 16px !important;
                }

                .es-footer-body p,
                .es-footer-body ul li,
                .es-footer-body ol li,
                .es-footer-body a {
                    font-size: 16px !important;
                }

                .es-infoblock p,
                .es-infoblock ul li,
                .es-infoblock ol li,
                .es-infoblock a {
                    font-size: 12px !important;
                }

                *[class='gmail-fix'] {
                    display: none !important;
                }

                .es-m-txt-c,
                .es-m-txt-c h1,
                .es-m-txt-c h2,
                .es-m-txt-c h3 {
                    text-align: center !important;
                }

                .es-m-txt-r,
                .es-m-txt-r h1,
                .es-m-txt-r h2,
                .es-m-txt-r h3 {
                    text-align: right !important;
                }

                .es-m-txt-l,
                .es-m-txt-l h1,
                .es-m-txt-l h2,
                .es-m-txt-l h3 {
                    text-align: left !important;
                }

                .es-m-txt-r img,
                .es-m-txt-c img,
                .es-m-txt-l img {
                    display: inline !important;
                }

                .es-button-border {
                    display: block !important;
                }

                .es-btn-fw {
                    border-width: 10px 0px !important;
                    text-align: center !important;
                }

                .es-adaptive table,
                .es-btn-fw,
                .es-btn-fw-brdr,
                .es-left,
                .es-right {
                    width: 100% !important;
                }

                .es-content table,
                .es-header table,
                .es-footer table,
                .es-content,
                .es-footer,
                .es-header {
                    width: 100% !important;
                    max-width: 600px !important;
                }

                .es-adapt-td {
                    display: block !important;
                    width: 100% !important;
                }

                .adapt-img {
                    width: 100% !important;
                    height: auto !important;
                }

                .es-m-p0 {
                    padding: 0px !important;
                }

                .es-m-p0r {
                    padding-right: 0px !important;
                }

                .es-m-p0l {
                    padding-left: 0px !important;
                }

                .es-m-p0t {
                    padding-top: 0px !important;
                }

                .es-m-p0b {
                    padding-bottom: 0 !important;
                }

                .es-m-p20b {
                    padding-bottom: 20px !important;
                }

                .es-mobile-hidden,
                .es-hidden {
                    display: none !important;
                }

                tr.es-desk-hidden,
                td.es-desk-hidden,
                table.es-desk-hidden {
                    width: auto !important;
                    overflow: visible !important;
                    float: none !important;
                    max-height: inherit !important;
                    line-height: inherit !important;
                }

                tr.es-desk-hidden {
                    display: table-row !important;
                }

                table.es-desk-hidden {
                    display: table !important;
                }

                td.es-desk-menu-hidden {
                    display: table-cell !important;
                }

                .es-menu td {
                    width: 1% !important;
                }

                table.es-table-not-adapt,
                .esd-block-html table {
                    width: auto !important;
                }

                table.es-social {
                    display: inline-block !important;
                }

                table.es-social td {
                    display: inline-block !important;
                }

                a.es-button,
                button.es-button {
                    font-size: 16px !important;
                    display: block !important;
                    border-left-width: 0px !important;
                    border-right-width: 0px !important;
                }
                }

                /*
                END RESPONSIVE STYLES
                */
                .es-p-default {
                padding-top: 20px;
                padding-right: 30px;
                padding-bottom: 0px;
                padding-left: 30px;
                }

                .es-p-all-default {
                padding: 0px;
                }

                a.es-button,
                button.es-button {
                border-style: solid;
                border-color: #3b2495;
                border-width: 12px 40px 13px 40px;
                display: inline-block;
                background: #3b2495;
                border-radius: 30px;
                font-size: 20px;
                font-family: Montserrat, Helvetica, Roboto, Arial, sans-serif;
                font-weight: normal;
                font-style: normal;
                line-height: 120%;
                color: #ffffff;
                text-decoration: none !important;
                width: auto;
                text-align: center;
                }
            </style>
            <script async custom-element='amp-list' src='https://cdn.ampproject.org/v0/amp-list-0.1.js'></script>
            <script async custom-template='amp-mustache' src='https://cdn.ampproject.org/v0/amp-mustache-0.2.js'></script>
            <script async custom-element='amp-bind' src='https://cdn.ampproject.org/v0/amp-bind-0.1.js'></script>
        </head>

        <body>
            <div class='es-wrapper-color'>
                <!--[if gte mso 9]>
                    <v:background xmlns:v='urn:schemas-microsoft-com:vml' fill='t'>
                        <v:fill type='tile' color='#F7F7F7'></v:fill>
                    </v:background>
                <![endif]-->
                <table cellpadding='0' cellspacing='0' class='es-wrapper' width='100%' style='background-position: center top;'>
                    <tbody>
                        <tr>
                            <td valign='top' class='esd-email-paddings'>
                                <table cellpadding='0' cellspacing='0' class='es-content esd-footer-popover' align='center'>
                                    <tbody>
                                        <tr>
                                            <td class='esd-stripe' align='center'>
                                                <table bgcolor='#ffffff' class='es-content-body' align='center' cellpadding='0' cellspacing='0' width='600'>
                                                    <tbody>
                                                        <tr class='es-visible-amp-html-only'>
                                                            <td class='esd-structure es-p25t es-p20b es-p20r es-p20l' style='background-position: center bottom;' align='left'>
                                                                <table width='100%' cellspacing='0' cellpadding='0'>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td class='esd-container-frame' width='558' valign='top' align='center'>
                                                                                <table width='100%' cellspacing='0' cellpadding='0'>
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td class='esd-block-text es-p15t es-p15r es-p15l' align='left'>
                                                                                                <p><strong>Cher(ère) ".$data['first_name']."  ".$data['last_name']."</strong></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td class='esd-block-text es-p10t es-p5b es-p15r es-p15l' align='left'>
                                                                                                <p>".$data['message']."<br></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td class='esd-block-text es-p15b es-p15r es-p15l' esd-links-color='#333333' align='left'>
                                                                                            <p>Veuillez trouver ci-joint votre identifiant pour accéder à votre compte.<br></p>
                                                                                            <p>Identifiant : ".$data['email']."</p>
                                                                                            <p>Mot de passe : ".$data['pwd']."</p>
                                                                                            <p style='display:none;'>Pour finaliser votre inscription, vous devez effeactuer le réglement des tarifs. <br>Les frais d'inscriptions peuvent être réglés en ligne à travers le site.</p>
                                                                                            <p>Lien d'accès : <a href='".$data['link']."'> Se connecter </a> <br><br></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class='esd-container-frame' width='558' valign='top' align='center'>
                                                                                <table width='100%' cellspacing='0' cellpadding='0'>
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td class='esd-block-text es-p15b es-p15r es-p15l' esd-links-color='#333333' align='left'>
                                                                                                <p><br>Si vous n'avez pas encore effectué le réglement des frais d'inscription, nous vous invitons à effectuer le paiement en ligne pour finaliser votre inscription. </p>
                                                                                                <br><a href='".$data['link_paiement']."' style='background-color:#2196f3; padding:7px 15px; border-radius:8px;color:#ffffff;text-decoration:none;'>Effectuer le paiment</a><br>
                                                                                                <br>En cas d'erreur, merci de nous contacter par retour d'email.<br>Nous restons à votre disposition pour tout complément d'information.<br><br>Cordialement,<br>La Société Sénégalaise de Cardiologie</p>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </body>

    </html>";


    return self::sendMailSmpt($container, $to, $subject, $message);

  }
  
  public static function sendMailWithoutPwdUser($container, $to, $subject, $data)
  {
    header('Content-Type: text/html; charset=utf-8');

    $message ="
        <!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional //EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>

        <html xmlns='http://www.w3.org/1999/xhtml' xmlns:o='urn:schemas-microsoft-com:office:office'>

        <head>
            <meta charset='UTF-8'>
            <meta content='width=device-width, initial-scale=1' name='viewport'>
            <meta name='x-apple-disable-message-reformatting'>
            <meta http-equiv='X-UA-Compatible' content='IE=edge'>
            <meta content='telephone=no' name='format-detection'>
            <title></title>
            <!--[if (mso 16)]> <style type='text/css'> a {text-decoration: none;} </style> <![endif]-->
            <!--[if gte mso 9]><style>sup { font-size: 100% !important; }</style><![endif]-->
            <!--[if !mso]><!-- -->
            <link href='https://fonts.googleapis.com/css?family=Montserrat:500,800' rel='stylesheet'>
            <!--<![endif]-->
            <!--[if gte mso 9]>
                <xml>
                    <o:OfficeDocumentSettings>
                    <o:AllowPNG></o:AllowPNG>
                    <o:PixelsPerInch>96</o:PixelsPerInch>
                    </o:OfficeDocumentSettings>
                </xml>
                <![endif]-->
                    <style>
                    /*
                CONFIG STYLES
                Please do not delete and edit CSS styles below
                */
                /* IMPORTANT THIS STYLES MUST BE ON FINAL EMAIL */
                #outlook a {
                padding: 0;
                }

                .ExternalClass {
                width: 100%;
                }

                .ExternalClass,
                .ExternalClass p,
                .ExternalClass span,
                .ExternalClass font,
                .ExternalClass td,
                .ExternalClass div {
                line-height: 100%;
                }

                .es-button {
                mso-style-priority: 100 !important;
                text-decoration: none !important;
                }

                a[x-apple-data-detectors] {
                color: inherit !important;
                text-decoration: none !important;
                font-size: inherit !important;
                font-family: inherit !important;
                font-weight: inherit !important;
                line-height: inherit !important;
                }

                .es-desk-hidden {
                display: none;
                float: left;
                overflow: hidden;
                width: 0;
                max-height: 0;
                line-height: 0;
                mso-hide: all;
                }

                a.es-button:hover {
                border-color: #2CB543 !important;
                background: #2CB543 !important;
                }

                a.es-secondary:hover {
                border-color: #ffffff !important;
                background: #ffffff !important;
                }

                /*
                END OF IMPORTANT
                */
                s {
                text-decoration: line-through;
                }

                html,
                body {
                width: 100%;
                font-family: Montserrat, Helvetica, Roboto, Arial, sans-serif;
                -webkit-text-size-adjust: 100%;
                -ms-text-size-adjust: 100%;
                }

                table {
                mso-table-lspace: 0pt;
                mso-table-rspace: 0pt;
                border-collapse: collapse;
                border-spacing: 0px;
                }

                table td,
                html,
                body,
                .es-wrapper {
                padding: 0;
                Margin: 0;
                }

                .es-content,
                .es-header,
                .es-footer {
                table-layout: fixed !important;
                width: 100%;
                }

                img {
                display: block;
                border: 0;
                outline: none;
                text-decoration: none;
                -ms-interpolation-mode: bicubic;
                }

                table tr {
                border-collapse: collapse;
                }

                p,
                hr {
                Margin: 0;
                }

                h1,
                h2,
                h3,
                h4,
                h5 {
                Margin: 0;
                line-height: 120%;
                mso-line-height-rule: exactly;
                font-family: Montserrat, Helvetica, Roboto, Arial, sans-serif;
                }

                p,
                ul li,
                ol li,
                a {
                -webkit-text-size-adjust: none;
                -ms-text-size-adjust: none;
                mso-line-height-rule: exactly;
                }

                .es-left {
                float: left;
                }

                .es-right {
                float: right;
                }

                .es-p5 {
                padding: 5px;
                }

                .es-p5t {
                padding-top: 5px;
                }

                .es-p5b {
                padding-bottom: 5px;
                }

                .es-p5l {
                padding-left: 5px;
                }

                .es-p5r {
                padding-right: 5px;
                }

                .es-p10 {
                padding: 10px;
                }

                .es-p10t {
                padding-top: 10px;
                }

                .es-p10b {
                padding-bottom: 10px;
                }

                .es-p10l {
                padding-left: 10px;
                }

                .es-p10r {
                padding-right: 10px;
                }

                .es-p15 {
                padding: 15px;
                }

                .es-p15t {
                padding-top: 15px;
                }

                .es-p15b {
                padding-bottom: 15px;
                }

                .es-p15l {
                padding-left: 15px;
                }

                .es-p15r {
                padding-right: 15px;
                }

                .es-p20 {
                padding: 20px;
                }

                .es-p20t {
                padding-top: 20px;
                }

                .es-p20b {
                padding-bottom: 20px;
                }

                .es-p20l {
                padding-left: 20px;
                }

                .es-p20r {
                padding-right: 20px;
                }

                .es-p25 {
                padding: 25px;
                }

                .es-p25t {
                padding-top: 25px;
                }

                .es-p25b {
                padding-bottom: 25px;
                }

                .es-p25l {
                padding-left: 25px;
                }

                .es-p25r {
                padding-right: 25px;
                }

                .es-p30 {
                padding: 30px;
                }

                .es-p30t {
                padding-top: 30px;
                }

                .es-p30b {
                padding-bottom: 30px;
                }

                .es-p30l {
                padding-left: 30px;
                }

                .es-p30r {
                padding-right: 30px;
                }

                .es-p35 {
                padding: 35px;
                }

                .es-p35t {
                padding-top: 35px;
                }

                .es-p35b {
                padding-bottom: 35px;
                }

                .es-p35l {
                padding-left: 35px;
                }

                .es-p35r {
                padding-right: 35px;
                }

                .es-p40 {
                padding: 40px;
                }

                .es-p40t {
                padding-top: 40px;
                }

                .es-p40b {
                padding-bottom: 40px;
                }

                .es-p40l {
                padding-left: 40px;
                }

                .es-p40r {
                padding-right: 40px;
                }

                .es-menu td {
                border: 0;
                }

                .es-menu td a img {
                display: inline-block !important;
                }

                /*
                END CONFIG STYLES
                */
                a {
                font-family: Montserrat, Helvetica, Roboto, Arial, sans-serif;
                font-size: 16px;
                text-decoration: underline;
                }

                h1 {
                font-size: 32px;
                font-style: normal;
                font-weight: bold;
                color: #4a4a4a;
                }

                h1 a {
                font-size: 32px;
                }

                h2 {
                font-size: 24px;
                font-style: normal;
                font-weight: bold;
                color: #4a4a4a;
                }

                h2 a {
                font-size: 24px;
                }

                h3 {
                font-size: 20px;
                font-style: normal;
                font-weight: bold;
                color: #4A4A4A;
                }

                h3 a {
                font-size: 20px;
                }

                p,
                ul li,
                ol li {
                font-size: 16px;
                font-family: Montserrat, Helvetica, Roboto, Arial, sans-serif;
                line-height: 150%;
                }

                ul li,
                ol li {
                Margin-bottom: 15px;
                }

                .es-menu td a {
                text-decoration: none;
                display: block;
                }

                .es-wrapper {
                width: 100%;
                height: 100%;
                background-image: ;
                background-repeat: repeat;
                background-position: center top;
                }

                .es-wrapper-color {
                background-color: #F7F7F7;
                }

                .es-content-body {
                background-color: transparent;
                }

                .es-content-body p,
                .es-content-body ul li,
                .es-content-body ol li {
                color: #4A4A4A;
                }

                .es-content-body a {
                color: #3b2495;
                }

                .es-header {
                background-color: #34265f;
                background-repeat: repeat;
                background-position: center bottom;
                }

                .es-header-body {
                background-color: #34265f;
                }

                .es-header-body p,
                .es-header-body ul li,
                .es-header-body ol li {
                color: #ffffff;
                font-size: 14px;
                }

                .es-header-body a {
                color: #ffffff;
                font-size: 14px;
                }

                .es-footer {
                background-color: #f7f7f7;
                background-repeat: repeat;
                background-position: center top;
                background-image: url(https://ohvpkv.stripocdn.email/content/guids/CABINET_7dfb659af020be618a1cf3d530b28d98/images/75021564382669317.png);
                }

                .es-footer-body {
                background-color: #f7f7f7;
                }

                .es-footer-body p,
                .es-footer-body ul li,
                .es-footer-body ol li {
                color: #ffffff;
                font-size: 16px;
                }

                .es-footer-body a {
                color: #ffffff;
                font-size: 16px;
                }

                .es-infoblock,
                .es-infoblock p,
                .es-infoblock ul li,
                .es-infoblock ol li {
                line-height: 120%;
                font-size: 12px;
                color: #cccccc;
                }

                .es-infoblock a {
                font-size: 12px;
                color: #cccccc;
                }

                .es-button-border {
                border-style: solid solid solid solid;
                border-color: #3b2495 #3b2495 #3b2495 #3b2495;
                background: #3b2495;
                border-width: 0px 0px 0px 0px;
                display: inline-block;
                border-radius: 30px;
                width: auto;
                }

                /*
                RESPONSIVE STYLES
                Please do not delete and edit CSS styles below.

                If you don't need responsive layout, please delete this section.
                */
                @media only screen and (max-width: 600px) {
                u+#body {
                    width: 100vw !important;
                }

                p,
                ul li,
                ol li,
                a {
                    font-size: 16px !important;
                    line-height: 150% !important;
                }

                h1 {
                    font-size: 30px !important;
                    text-align: center;
                    line-height: 120% !important;
                }

                h2 {
                    font-size: 26px !important;
                    text-align: center;
                    line-height: 120% !important;
                }

                h3 {
                    font-size: 20px !important;
                    text-align: center;
                    line-height: 120% !important;
                }

                h1 a {
                    font-size: 30px !important;
                }

                h2 a {
                    font-size: 26px !important;
                }

                h3 a {
                    font-size: 20px !important;
                }

                .es-menu td a {
                    font-size: 16px !important;
                }

                .es-header-body p,
                .es-header-body ul li,
                .es-header-body ol li,
                .es-header-body a {
                    font-size: 16px !important;
                }

                .es-footer-body p,
                .es-footer-body ul li,
                .es-footer-body ol li,
                .es-footer-body a {
                    font-size: 16px !important;
                }

                .es-infoblock p,
                .es-infoblock ul li,
                .es-infoblock ol li,
                .es-infoblock a {
                    font-size: 12px !important;
                }

                *[class='gmail-fix'] {
                    display: none !important;
                }

                .es-m-txt-c,
                .es-m-txt-c h1,
                .es-m-txt-c h2,
                .es-m-txt-c h3 {
                    text-align: center !important;
                }

                .es-m-txt-r,
                .es-m-txt-r h1,
                .es-m-txt-r h2,
                .es-m-txt-r h3 {
                    text-align: right !important;
                }

                .es-m-txt-l,
                .es-m-txt-l h1,
                .es-m-txt-l h2,
                .es-m-txt-l h3 {
                    text-align: left !important;
                }

                .es-m-txt-r img,
                .es-m-txt-c img,
                .es-m-txt-l img {
                    display: inline !important;
                }

                .es-button-border {
                    display: block !important;
                }

                .es-btn-fw {
                    border-width: 10px 0px !important;
                    text-align: center !important;
                }

                .es-adaptive table,
                .es-btn-fw,
                .es-btn-fw-brdr,
                .es-left,
                .es-right {
                    width: 100% !important;
                }

                .es-content table,
                .es-header table,
                .es-footer table,
                .es-content,
                .es-footer,
                .es-header {
                    width: 100% !important;
                    max-width: 600px !important;
                }

                .es-adapt-td {
                    display: block !important;
                    width: 100% !important;
                }

                .adapt-img {
                    width: 100% !important;
                    height: auto !important;
                }

                .es-m-p0 {
                    padding: 0px !important;
                }

                .es-m-p0r {
                    padding-right: 0px !important;
                }

                .es-m-p0l {
                    padding-left: 0px !important;
                }

                .es-m-p0t {
                    padding-top: 0px !important;
                }

                .es-m-p0b {
                    padding-bottom: 0 !important;
                }

                .es-m-p20b {
                    padding-bottom: 20px !important;
                }

                .es-mobile-hidden,
                .es-hidden {
                    display: none !important;
                }

                tr.es-desk-hidden,
                td.es-desk-hidden,
                table.es-desk-hidden {
                    width: auto !important;
                    overflow: visible !important;
                    float: none !important;
                    max-height: inherit !important;
                    line-height: inherit !important;
                }

                tr.es-desk-hidden {
                    display: table-row !important;
                }

                table.es-desk-hidden {
                    display: table !important;
                }

                td.es-desk-menu-hidden {
                    display: table-cell !important;
                }

                .es-menu td {
                    width: 1% !important;
                }

                table.es-table-not-adapt,
                .esd-block-html table {
                    width: auto !important;
                }

                table.es-social {
                    display: inline-block !important;
                }

                table.es-social td {
                    display: inline-block !important;
                }

                a.es-button,
                button.es-button {
                    font-size: 16px !important;
                    display: block !important;
                    border-left-width: 0px !important;
                    border-right-width: 0px !important;
                }
                }

                /*
                END RESPONSIVE STYLES
                */
                .es-p-default {
                padding-top: 20px;
                padding-right: 30px;
                padding-bottom: 0px;
                padding-left: 30px;
                }

                .es-p-all-default {
                padding: 0px;
                }

                a.es-button,
                button.es-button {
                border-style: solid;
                border-color: #3b2495;
                border-width: 12px 40px 13px 40px;
                display: inline-block;
                background: #3b2495;
                border-radius: 30px;
                font-size: 20px;
                font-family: Montserrat, Helvetica, Roboto, Arial, sans-serif;
                font-weight: normal;
                font-style: normal;
                line-height: 120%;
                color: #ffffff;
                text-decoration: none !important;
                width: auto;
                text-align: center;
                }
            </style>
            <script async custom-element='amp-list' src='https://cdn.ampproject.org/v0/amp-list-0.1.js'></script>
            <script async custom-template='amp-mustache' src='https://cdn.ampproject.org/v0/amp-mustache-0.2.js'></script>
            <script async custom-element='amp-bind' src='https://cdn.ampproject.org/v0/amp-bind-0.1.js'></script>
        </head>

        <body>
            <div class='es-wrapper-color'>
                <!--[if gte mso 9]>
                    <v:background xmlns:v='urn:schemas-microsoft-com:vml' fill='t'>
                        <v:fill type='tile' color='#F7F7F7'></v:fill>
                    </v:background>
                <![endif]-->
                <table cellpadding='0' cellspacing='0' class='es-wrapper' width='100%' style='background-position: center top;'>
                    <tbody>
                        <tr>
                            <td valign='top' class='esd-email-paddings'>
                                <table cellpadding='0' cellspacing='0' class='es-content esd-footer-popover' align='center'>
                                    <tbody>
                                        <tr>
                                            <td class='esd-stripe' align='center'>
                                                <table bgcolor='#ffffff' class='es-content-body' align='center' cellpadding='0' cellspacing='0' width='600'>
                                                    <tbody>
                                                        <tr class='es-visible-amp-html-only'>
                                                            <td class='esd-structure es-p25t es-p20b es-p20r es-p20l' style='background-position: center bottom;' align='left'>
                                                                <table width='100%' cellspacing='0' cellpadding='0'>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td class='esd-container-frame' width='558' valign='top' align='center'>
                                                                                <table width='100%' cellspacing='0' cellpadding='0'>
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td class='esd-block-text es-p15t es-p15r es-p15l' align='left'>
                                                                                                <p><strong>Cher(ère) ".$data['first_name']."  ".$data['last_name']."</strong></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td class='esd-block-text es-p10t es-p5b es-p15r es-p15l' align='left'>
                                                                                                <p>".$data['message']."<br></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td class='esd-block-text es-p15b es-p15r es-p15l' esd-links-color='#333333' align='left'>
                                                                                            <p style='display:none;'>Pour finaliser votre inscription, vous devez effeactuer le réglement des tarifs. <br>Les frais d'inscriptions peuvent être réglés en ligne à travers le site.</p>
                                                                                            <p>Lien d'accès : <a href='".$data['link']."'> Se connecter </a> <br><br></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class='esd-container-frame' width='558' valign='top' align='center'>
                                                                                <table width='100%' cellspacing='0' cellpadding='0'>
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td class='esd-block-text es-p15b es-p15r es-p15l' esd-links-color='#333333' align='left'>
                                                                                                <p><br>Si vous n'avez pas encore effectué le réglement des frais d'inscription, nous vous invitons à effectuer le paiement en ligne pour finaliser votre inscription. </p>
                                                                                                <br><a href='".$data['link_paiement']."' style='background-color:#2196f3; padding:7px 15px; border-radius:8px;color:#ffffff;text-decoration:none;'>Effectuer le paiment</a><br>
                                                                                                <br>En cas d'erreur, merci de nous contacter par retour d'email.<br>Nous restons à votre disposition pour tout complément d'information.<br><br>Cordialement,<br>La Société Sénégalaise de Cardiologie</p>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </body>

    </html>";


    return self::sendMailSmpt($container, $to, $subject, $message);

  }

  public static function sendMailAfterPayment($container, $to, $subject, $data)
  {
    
    header('Content-Type: text/html; charset=utf-8');

    $message ="
        <!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional //EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>

        <html xmlns='http://www.w3.org/1999/xhtml' xmlns:o='urn:schemas-microsoft-com:office:office'>

        <head>
            <meta charset='UTF-8'>
            <meta content='width=device-width, initial-scale=1' name='viewport'>
            <meta name='x-apple-disable-message-reformatting'>
            <meta http-equiv='X-UA-Compatible' content='IE=edge'>
            <meta content='telephone=no' name='format-detection'>
            <title></title>
            <!--[if (mso 16)]> <style type='text/css'> a {text-decoration: none;} </style> <![endif]-->
            <!--[if gte mso 9]><style>sup { font-size: 100% !important; }</style><![endif]-->
            <!--[if !mso]><!-- -->
            <link href='https://fonts.googleapis.com/css?family=Montserrat:500,800' rel='stylesheet'>
            <!--<![endif]-->
            <!--[if gte mso 9]>
                <xml>
                    <o:OfficeDocumentSettings>
                    <o:AllowPNG></o:AllowPNG>
                    <o:PixelsPerInch>96</o:PixelsPerInch>
                    </o:OfficeDocumentSettings>
                </xml>
                <![endif]-->
                    <style>
                    /*
                CONFIG STYLES
                Please do not delete and edit CSS styles below
                */
                /* IMPORTANT THIS STYLES MUST BE ON FINAL EMAIL */
                #outlook a {
                padding: 0;
                }

                .ExternalClass {
                width: 100%;
                }

                .ExternalClass,
                .ExternalClass p,
                .ExternalClass span,
                .ExternalClass font,
                .ExternalClass td,
                .ExternalClass div {
                line-height: 100%;
                }

                .es-button {
                mso-style-priority: 100 !important;
                text-decoration: none !important;
                }

                a[x-apple-data-detectors] {
                color: inherit !important;
                text-decoration: none !important;
                font-size: inherit !important;
                font-family: inherit !important;
                font-weight: inherit !important;
                line-height: inherit !important;
                }

                .es-desk-hidden {
                display: none;
                float: left;
                overflow: hidden;
                width: 0;
                max-height: 0;
                line-height: 0;
                mso-hide: all;
                }

                a.es-button:hover {
                border-color: #2CB543 !important;
                background: #2CB543 !important;
                }

                a.es-secondary:hover {
                border-color: #ffffff !important;
                background: #ffffff !important;
                }

                /*
                END OF IMPORTANT
                */
                s {
                text-decoration: line-through;
                }

                html,
                body {
                width: 100%;
                font-family: Montserrat, Helvetica, Roboto, Arial, sans-serif;
                -webkit-text-size-adjust: 100%;
                -ms-text-size-adjust: 100%;
                }

                table {
                mso-table-lspace: 0pt;
                mso-table-rspace: 0pt;
                border-collapse: collapse;
                border-spacing: 0px;
                }

                table td,
                html,
                body,
                .es-wrapper {
                padding: 0;
                Margin: 0;
                }

                .es-content,
                .es-header,
                .es-footer {
                table-layout: fixed !important;
                width: 100%;
                }

                img {
                display: block;
                border: 0;
                outline: none;
                text-decoration: none;
                -ms-interpolation-mode: bicubic;
                }

                table tr {
                border-collapse: collapse;
                }

                p,
                hr {
                Margin: 0;
                }

                h1,
                h2,
                h3,
                h4,
                h5 {
                Margin: 0;
                line-height: 120%;
                mso-line-height-rule: exactly;
                font-family: Montserrat, Helvetica, Roboto, Arial, sans-serif;
                }

                p,
                ul li,
                ol li,
                a {
                -webkit-text-size-adjust: none;
                -ms-text-size-adjust: none;
                mso-line-height-rule: exactly;
                }

                .es-left {
                float: left;
                }

                .es-right {
                float: right;
                }

                .es-p5 {
                padding: 5px;
                }

                .es-p5t {
                padding-top: 5px;
                }

                .es-p5b {
                padding-bottom: 5px;
                }

                .es-p5l {
                padding-left: 5px;
                }

                .es-p5r {
                padding-right: 5px;
                }

                .es-p10 {
                padding: 10px;
                }

                .es-p10t {
                padding-top: 10px;
                }

                .es-p10b {
                padding-bottom: 10px;
                }

                .es-p10l {
                padding-left: 10px;
                }

                .es-p10r {
                padding-right: 10px;
                }

                .es-p15 {
                padding: 15px;
                }

                .es-p15t {
                padding-top: 15px;
                }

                .es-p15b {
                padding-bottom: 15px;
                }

                .es-p15l {
                padding-left: 15px;
                }

                .es-p15r {
                padding-right: 15px;
                }

                .es-p20 {
                padding: 20px;
                }

                .es-p20t {
                padding-top: 20px;
                }

                .es-p20b {
                padding-bottom: 20px;
                }

                .es-p20l {
                padding-left: 20px;
                }

                .es-p20r {
                padding-right: 20px;
                }

                .es-p25 {
                padding: 25px;
                }

                .es-p25t {
                padding-top: 25px;
                }

                .es-p25b {
                padding-bottom: 25px;
                }

                .es-p25l {
                padding-left: 25px;
                }

                .es-p25r {
                padding-right: 25px;
                }

                .es-p30 {
                padding: 30px;
                }

                .es-p30t {
                padding-top: 30px;
                }

                .es-p30b {
                padding-bottom: 30px;
                }

                .es-p30l {
                padding-left: 30px;
                }

                .es-p30r {
                padding-right: 30px;
                }

                .es-p35 {
                padding: 35px;
                }

                .es-p35t {
                padding-top: 35px;
                }

                .es-p35b {
                padding-bottom: 35px;
                }

                .es-p35l {
                padding-left: 35px;
                }

                .es-p35r {
                padding-right: 35px;
                }

                .es-p40 {
                padding: 40px;
                }

                .es-p40t {
                padding-top: 40px;
                }

                .es-p40b {
                padding-bottom: 40px;
                }

                .es-p40l {
                padding-left: 40px;
                }

                .es-p40r {
                padding-right: 40px;
                }

                .es-menu td {
                border: 0;
                }

                .es-menu td a img {
                display: inline-block !important;
                }

                /*
                END CONFIG STYLES
                */
                a {
                font-family: Montserrat, Helvetica, Roboto, Arial, sans-serif;
                font-size: 16px;
                text-decoration: underline;
                }

                h1 {
                font-size: 32px;
                font-style: normal;
                font-weight: bold;
                color: #4a4a4a;
                }

                h1 a {
                font-size: 32px;
                }

                h2 {
                font-size: 24px;
                font-style: normal;
                font-weight: bold;
                color: #4a4a4a;
                }

                h2 a {
                font-size: 24px;
                }

                h3 {
                font-size: 20px;
                font-style: normal;
                font-weight: bold;
                color: #4A4A4A;
                }

                h3 a {
                font-size: 20px;
                }

                p,
                ul li,
                ol li {
                font-size: 16px;
                font-family: Montserrat, Helvetica, Roboto, Arial, sans-serif;
                line-height: 150%;
                }

                ul li,
                ol li {
                Margin-bottom: 15px;
                }

                .es-menu td a {
                text-decoration: none;
                display: block;
                }

                .es-wrapper {
                width: 100%;
                height: 100%;
                background-image: ;
                background-repeat: repeat;
                background-position: center top;
                }

                .es-wrapper-color {
                background-color: #F7F7F7;
                }

                .es-content-body {
                background-color: transparent;
                }

                .es-content-body p,
                .es-content-body ul li,
                .es-content-body ol li {
                color: #4A4A4A;
                }

                .es-content-body a {
                color: #3b2495;
                }

                .es-header {
                background-color: #34265f;
                background-repeat: repeat;
                background-position: center bottom;
                }

                .es-header-body {
                background-color: #34265f;
                }

                .es-header-body p,
                .es-header-body ul li,
                .es-header-body ol li {
                color: #ffffff;
                font-size: 14px;
                }

                .es-header-body a {
                color: #ffffff;
                font-size: 14px;
                }

                .es-footer {
                background-color: #f7f7f7;
                background-repeat: repeat;
                background-position: center top;
                background-image: url(https://ohvpkv.stripocdn.email/content/guids/CABINET_7dfb659af020be618a1cf3d530b28d98/images/75021564382669317.png);
                }

                .es-footer-body {
                background-color: #f7f7f7;
                }

                .es-footer-body p,
                .es-footer-body ul li,
                .es-footer-body ol li {
                color: #ffffff;
                font-size: 16px;
                }

                .es-footer-body a {
                color: #ffffff;
                font-size: 16px;
                }

                .es-infoblock,
                .es-infoblock p,
                .es-infoblock ul li,
                .es-infoblock ol li {
                line-height: 120%;
                font-size: 12px;
                color: #cccccc;
                }

                .es-infoblock a {
                font-size: 12px;
                color: #cccccc;
                }

                .es-button-border {
                border-style: solid solid solid solid;
                border-color: #3b2495 #3b2495 #3b2495 #3b2495;
                background: #3b2495;
                border-width: 0px 0px 0px 0px;
                display: inline-block;
                border-radius: 30px;
                width: auto;
                }

                /*
                RESPONSIVE STYLES
                Please do not delete and edit CSS styles below.

                If you don't need responsive layout, please delete this section.
                */
                @media only screen and (max-width: 600px) {
                u+#body {
                    width: 100vw !important;
                }

                p,
                ul li,
                ol li,
                a {
                    font-size: 16px !important;
                    line-height: 150% !important;
                }

                h1 {
                    font-size: 30px !important;
                    text-align: center;
                    line-height: 120% !important;
                }

                h2 {
                    font-size: 26px !important;
                    text-align: center;
                    line-height: 120% !important;
                }

                h3 {
                    font-size: 20px !important;
                    text-align: center;
                    line-height: 120% !important;
                }

                h1 a {
                    font-size: 30px !important;
                }

                h2 a {
                    font-size: 26px !important;
                }

                h3 a {
                    font-size: 20px !important;
                }

                .es-menu td a {
                    font-size: 16px !important;
                }

                .es-header-body p,
                .es-header-body ul li,
                .es-header-body ol li,
                .es-header-body a {
                    font-size: 16px !important;
                }

                .es-footer-body p,
                .es-footer-body ul li,
                .es-footer-body ol li,
                .es-footer-body a {
                    font-size: 16px !important;
                }

                .es-infoblock p,
                .es-infoblock ul li,
                .es-infoblock ol li,
                .es-infoblock a {
                    font-size: 12px !important;
                }

                *[class='gmail-fix'] {
                    display: none !important;
                }

                .es-m-txt-c,
                .es-m-txt-c h1,
                .es-m-txt-c h2,
                .es-m-txt-c h3 {
                    text-align: center !important;
                }

                .es-m-txt-r,
                .es-m-txt-r h1,
                .es-m-txt-r h2,
                .es-m-txt-r h3 {
                    text-align: right !important;
                }

                .es-m-txt-l,
                .es-m-txt-l h1,
                .es-m-txt-l h2,
                .es-m-txt-l h3 {
                    text-align: left !important;
                }

                .es-m-txt-r img,
                .es-m-txt-c img,
                .es-m-txt-l img {
                    display: inline !important;
                }

                .es-button-border {
                    display: block !important;
                }

                .es-btn-fw {
                    border-width: 10px 0px !important;
                    text-align: center !important;
                }

                .es-adaptive table,
                .es-btn-fw,
                .es-btn-fw-brdr,
                .es-left,
                .es-right {
                    width: 100% !important;
                }

                .es-content table,
                .es-header table,
                .es-footer table,
                .es-content,
                .es-footer,
                .es-header {
                    width: 100% !important;
                    max-width: 600px !important;
                }

                .es-adapt-td {
                    display: block !important;
                    width: 100% !important;
                }

                .adapt-img {
                    width: 100% !important;
                    height: auto !important;
                }

                .es-m-p0 {
                    padding: 0px !important;
                }

                .es-m-p0r {
                    padding-right: 0px !important;
                }

                .es-m-p0l {
                    padding-left: 0px !important;
                }

                .es-m-p0t {
                    padding-top: 0px !important;
                }

                .es-m-p0b {
                    padding-bottom: 0 !important;
                }

                .es-m-p20b {
                    padding-bottom: 20px !important;
                }

                .es-mobile-hidden,
                .es-hidden {
                    display: none !important;
                }

                tr.es-desk-hidden,
                td.es-desk-hidden,
                table.es-desk-hidden {
                    width: auto !important;
                    overflow: visible !important;
                    float: none !important;
                    max-height: inherit !important;
                    line-height: inherit !important;
                }

                tr.es-desk-hidden {
                    display: table-row !important;
                }

                table.es-desk-hidden {
                    display: table !important;
                }

                td.es-desk-menu-hidden {
                    display: table-cell !important;
                }

                .es-menu td {
                    width: 1% !important;
                }

                table.es-table-not-adapt,
                .esd-block-html table {
                    width: auto !important;
                }

                table.es-social {
                    display: inline-block !important;
                }

                table.es-social td {
                    display: inline-block !important;
                }

                a.es-button,
                button.es-button {
                    font-size: 16px !important;
                    display: block !important;
                    border-left-width: 0px !important;
                    border-right-width: 0px !important;
                }
                }

                /*
                END RESPONSIVE STYLES
                */
                .es-p-default {
                padding-top: 20px;
                padding-right: 30px;
                padding-bottom: 0px;
                padding-left: 30px;
                }

                .es-p-all-default {
                padding: 0px;
                }

                a.es-button,
                button.es-button {
                border-style: solid;
                border-color: #3b2495;
                border-width: 12px 40px 13px 40px;
                display: inline-block;
                background: #3b2495;
                border-radius: 30px;
                font-size: 20px;
                font-family: Montserrat, Helvetica, Roboto, Arial, sans-serif;
                font-weight: normal;
                font-style: normal;
                line-height: 120%;
                color: #ffffff;
                text-decoration: none !important;
                width: auto;
                text-align: center;
                }
            </style>
            <script async custom-element='amp-list' src='https://cdn.ampproject.org/v0/amp-list-0.1.js'></script>
            <script async custom-template='amp-mustache' src='https://cdn.ampproject.org/v0/amp-mustache-0.2.js'></script>
            <script async custom-element='amp-bind' src='https://cdn.ampproject.org/v0/amp-bind-0.1.js'></script>
        </head>

        <body>
            <div class='es-wrapper-color'>
                <!--[if gte mso 9]>
                    <v:background xmlns:v='urn:schemas-microsoft-com:vml' fill='t'>
                        <v:fill type='tile' color='#F7F7F7'></v:fill>
                    </v:background>
                <![endif]-->
                <table cellpadding='0' cellspacing='0' class='es-wrapper' width='100%' style='background-position: center top;'>
                    <tbody>
                        <tr>
                            <td valign='top' class='esd-email-paddings'>
                                <table cellpadding='0' cellspacing='0' class='es-content esd-footer-popover' align='center'>
                                    <tbody>
                                        <tr>
                                            <td class='esd-stripe' align='center'>
                                                <table bgcolor='#ffffff' class='es-content-body' align='center' cellpadding='0' cellspacing='0' width='600'>
                                                    <tbody>
                                                        <tr class='es-visible-amp-html-only'>
                                                            <td class='esd-structure es-p25t es-p20b es-p20r es-p20l' style='background-position: center bottom;' align='left'>
                                                                <table width='100%' cellspacing='0' cellpadding='0'>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td class='esd-container-frame' width='558' valign='top' align='center'>
                                                                                <table width='100%' cellspacing='0' cellpadding='0'>
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td class='esd-block-text es-p15t es-p15r es-p15l' align='left'>
                                                                                                <p><strong>Cher(ère) ".$data['first_name']."  ".$data['last_name']."</strong></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td class='esd-block-text es-p10t es-p5b es-p15r es-p15l' align='left'>".$data['message']."
                                                                                            </td>
                                                                                        </tr>

                                                                                        <tr>
                                                                                            <td class='esd-block-text es-p15b es-p15r es-p15l' esd-links-color='#333333' align='left'>
                                                                                                <p>Veuillez trouver ci-joint votre identifiant et votre code qr à imprimer ou à sauvegarder sur votre smartphone/tablette et à présenter à l'accueil du congrès pour impression de votre badge définitif.<br></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td align='center' class='esd-block-image' style='font-size: 0px;'><a target='_blank'><img class='adapt-img' src='".$container->domain_url."/".$container->qrcode_repertory.$data['qr_code']."' alt style='display: block;'></a></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td class='esd-block-text es-p15b es-p15r es-p15l' esd-links-color='#333333' align='left'>
                                                                                            <br><a href='".$data['link_login']."' style='background-color:#2196f3; padding:7px 15px; border-radius:8px;color:#ffffff;text-decoration:none;'>Accéder à mon espace</a><br><br><br>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class='esd-container-frame' width='558' valign='top' align='center'>
                                                                                <table width='100%' cellspacing='0' cellpadding='0'>
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td class='esd-block-text es-p15b es-p15r es-p15l' esd-links-color='#333333' align='left'>
                                                                                                <p>S'il vous plaît veuillez noter que ce courriel est destiné uniquement à la personne nommée ci-dessus. Si vous avez reçu ce courriel par erreur, veuillez en informer immédiatement l'expéditeur et supprimer ce courriel de votre système.<br>
                                                                                                <br>En cas d'erreur, merci de nous contacter par retour d'email.<br>Nous restons à votre disposition pour tout complément d'information.<br><br>Cordialement,<br>La Société Sénégalaise de Cardiologie</p>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </body>

    </html>";


    return self::sendMailSmpt($container, $to, $subject, $message);

  }

  public static function sendMailAbstract($container, $to, $subject, $data)
  {
    header('Content-Type: text/html; charset=utf-8');

    $message ="
      <!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional //EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>

      <html xmlns='http://www.w3.org/1999/xhtml' xmlns:o='urn:schemas-microsoft-com:office:office'>

        <head>
            <meta charset='UTF-8'>
            <meta content='width=device-width, initial-scale=1' name='viewport'>
            <meta name='x-apple-disable-message-reformatting'>
            <meta http-equiv='X-UA-Compatible' content='IE=edge'>
            <meta content='telephone=no' name='format-detection'>
            <title></title>
            <!--[if (mso 16)]> <style type='text/css'> a {text-decoration: none;} </style> <![endif]-->
            <!--[if gte mso 9]><style>sup { font-size: 100% !important; }</style><![endif]-->
            <!--[if !mso]><!-- -->
            <link href='https://fonts.googleapis.com/css?family=Montserrat:500,800' rel='stylesheet'>
            <!--<![endif]-->
            <!--[if gte mso 9]>
              <xml>
                  <o:OfficeDocumentSettings>
                  <o:AllowPNG></o:AllowPNG>
                  <o:PixelsPerInch>96</o:PixelsPerInch>
                  </o:OfficeDocumentSettings>
              </xml>
              <![endif]-->
                  <style>
                  /*
              CONFIG STYLES
              Please do not delete and edit CSS styles below
              */
              /* IMPORTANT THIS STYLES MUST BE ON FINAL EMAIL */
              #outlook a {
              padding: 0;
              }

              .ExternalClass {
              width: 100%;
              }

              .ExternalClass,
              .ExternalClass p,
              .ExternalClass span,
              .ExternalClass font,
              .ExternalClass td,
              .ExternalClass div {
              line-height: 100%;
              }

              .es-button {
              mso-style-priority: 100 !important;
              text-decoration: none !important;
              }

              a[x-apple-data-detectors] {
              color: inherit !important;
              text-decoration: none !important;
              font-size: inherit !important;
              font-family: inherit !important;
              font-weight: inherit !important;
              line-height: inherit !important;
              }

              .es-desk-hidden {
              display: none;
              float: left;
              overflow: hidden;
              width: 0;
              max-height: 0;
              line-height: 0;
              mso-hide: all;
              }

              a.es-button:hover {
              border-color: #2CB543 !important;
              background: #2CB543 !important;
              }

              a.es-secondary:hover {
              border-color: #ffffff !important;
              background: #ffffff !important;
              }

              /*
              END OF IMPORTANT
              */
              s {
              text-decoration: line-through;
              }

              html,
              body {
              width: 100%;
              font-family: Montserrat, Helvetica, Roboto, Arial, sans-serif;
              -webkit-text-size-adjust: 100%;
              -ms-text-size-adjust: 100%;
              }

              table {
              mso-table-lspace: 0pt;
              mso-table-rspace: 0pt;
              border-collapse: collapse;
              border-spacing: 0px;
              }

              table td,
              html,
              body,
              .es-wrapper {
              padding: 0;
              Margin: 0;
              }

              .es-content,
              .es-header,
              .es-footer {
              table-layout: fixed !important;
              width: 100%;
              }

              img {
              display: block;
              border: 0;
              outline: none;
              text-decoration: none;
              -ms-interpolation-mode: bicubic;
              }

              table tr {
              border-collapse: collapse;
              }

              p,
              hr {
              Margin: 0;
              }

              h1,
              h2,
              h3,
              h4,
              h5 {
              Margin: 0;
              line-height: 120%;
              mso-line-height-rule: exactly;
              font-family: Montserrat, Helvetica, Roboto, Arial, sans-serif;
              }

              p,
              ul li,
              ol li,
              a {
              -webkit-text-size-adjust: none;
              -ms-text-size-adjust: none;
              mso-line-height-rule: exactly;
              }

              .es-left {
              float: left;
              }

              .es-right {
              float: right;
              }

              .es-p5 {
              padding: 5px;
              }

              .es-p5t {
              padding-top: 5px;
              }

              .es-p5b {
              padding-bottom: 5px;
              }

              .es-p5l {
              padding-left: 5px;
              }

              .es-p5r {
              padding-right: 5px;
              }

              .es-p10 {
              padding: 10px;
              }

              .es-p10t {
              padding-top: 10px;
              }

              .es-p10b {
              padding-bottom: 10px;
              }

              .es-p10l {
              padding-left: 10px;
              }

              .es-p10r {
              padding-right: 10px;
              }

              .es-p15 {
              padding: 15px;
              }

              .es-p15t {
              padding-top: 15px;
              }

              .es-p15b {
              padding-bottom: 15px;
              }

              .es-p15l {
              padding-left: 15px;
              }

              .es-p15r {
              padding-right: 15px;
              }

              .es-p20 {
              padding: 20px;
              }

              .es-p20t {
              padding-top: 20px;
              }

              .es-p20b {
              padding-bottom: 20px;
              }

              .es-p20l {
              padding-left: 20px;
              }

              .es-p20r {
              padding-right: 20px;
              }

              .es-p25 {
              padding: 25px;
              }

              .es-p25t {
              padding-top: 25px;
              }

              .es-p25b {
              padding-bottom: 25px;
              }

              .es-p25l {
              padding-left: 25px;
              }

              .es-p25r {
              padding-right: 25px;
              }

              .es-p30 {
              padding: 30px;
              }

              .es-p30t {
              padding-top: 30px;
              }

              .es-p30b {
              padding-bottom: 30px;
              }

              .es-p30l {
              padding-left: 30px;
              }

              .es-p30r {
              padding-right: 30px;
              }

              .es-p35 {
              padding: 35px;
              }

              .es-p35t {
              padding-top: 35px;
              }

              .es-p35b {
              padding-bottom: 35px;
              }

              .es-p35l {
              padding-left: 35px;
              }

              .es-p35r {
              padding-right: 35px;
              }

              .es-p40 {
              padding: 40px;
              }

              .es-p40t {
              padding-top: 40px;
              }

              .es-p40b {
              padding-bottom: 40px;
              }

              .es-p40l {
              padding-left: 40px;
              }

              .es-p40r {
              padding-right: 40px;
              }

              .es-menu td {
              border: 0;
              }

              .es-menu td a img {
              display: inline-block !important;
              }

              /*
              END CONFIG STYLES
              */
              a {
              font-family: Montserrat, Helvetica, Roboto, Arial, sans-serif;
              font-size: 16px;
              text-decoration: underline;
              }

              h1 {
              font-size: 32px;
              font-style: normal;
              font-weight: bold;
              color: #4a4a4a;
              }

              h1 a {
              font-size: 32px;
              }

              h2 {
              font-size: 24px;
              font-style: normal;
              font-weight: bold;
              color: #4a4a4a;
              }

              h2 a {
              font-size: 24px;
              }

              h3 {
              font-size: 20px;
              font-style: normal;
              font-weight: bold;
              color: #4A4A4A;
              }

              h3 a {
              font-size: 20px;
              }

              p,
              ul li,
              ol li {
              font-size: 16px;
              font-family: Montserrat, Helvetica, Roboto, Arial, sans-serif;
              line-height: 150%;
              }

              ul li,
              ol li {
              Margin-bottom: 15px;
              }

              .es-menu td a {
              text-decoration: none;
              display: block;
              }

              .es-wrapper {
              width: 100%;
              height: 100%;
              background-image: ;
              background-repeat: repeat;
              background-position: center top;
              }

              .es-wrapper-color {
              background-color: #F7F7F7;
              }

              .es-content-body {
              background-color: transparent;
              }

              .es-content-body p,
              .es-content-body ul li,
              .es-content-body ol li {
              color: #4A4A4A;
              }

              .es-content-body a {
              color: #3b2495;
              }

              .es-header {
              background-color: #34265f;
              background-repeat: repeat;
              background-position: center bottom;
              }

              .es-header-body {
              background-color: #34265f;
              }

              .es-header-body p,
              .es-header-body ul li,
              .es-header-body ol li {
              color: #ffffff;
              font-size: 14px;
              }

              .es-header-body a {
              color: #ffffff;
              font-size: 14px;
              }

              .es-footer {
              background-color: #f7f7f7;
              background-repeat: repeat;
              background-position: center top;
              background-image: url(https://ohvpkv.stripocdn.email/content/guids/CABINET_7dfb659af020be618a1cf3d530b28d98/images/75021564382669317.png);
              }

              .es-footer-body {
              background-color: #f7f7f7;
              }

              .es-footer-body p,
              .es-footer-body ul li,
              .es-footer-body ol li {
              color: #ffffff;
              font-size: 16px;
              }

              .es-footer-body a {
              color: #ffffff;
              font-size: 16px;
              }

              .es-infoblock,
              .es-infoblock p,
              .es-infoblock ul li,
              .es-infoblock ol li {
              line-height: 120%;
              font-size: 12px;
              color: #cccccc;
              }

              .es-infoblock a {
              font-size: 12px;
              color: #cccccc;
              }

              .es-button-border {
              border-style: solid solid solid solid;
              border-color: #3b2495 #3b2495 #3b2495 #3b2495;
              background: #3b2495;
              border-width: 0px 0px 0px 0px;
              display: inline-block;
              border-radius: 30px;
              width: auto;
              }

              /*
              RESPONSIVE STYLES
              Please do not delete and edit CSS styles below.

              If you don't need responsive layout, please delete this section.
              */
              @media only screen and (max-width: 600px) {
              u+#body {
                  width: 100vw !important;
              }

              p,
              ul li,
              ol li,
              a {
                  font-size: 16px !important;
                  line-height: 150% !important;
              }

              h1 {
                  font-size: 30px !important;
                  text-align: center;
                  line-height: 120% !important;
              }

              h2 {
                  font-size: 26px !important;
                  text-align: center;
                  line-height: 120% !important;
              }

              h3 {
                  font-size: 20px !important;
                  text-align: center;
                  line-height: 120% !important;
              }

              h1 a {
                  font-size: 30px !important;
              }

              h2 a {
                  font-size: 26px !important;
              }

              h3 a {
                  font-size: 20px !important;
              }

              .es-menu td a {
                  font-size: 16px !important;
              }

              .es-header-body p,
              .es-header-body ul li,
              .es-header-body ol li,
              .es-header-body a {
                  font-size: 16px !important;
              }

              .es-footer-body p,
              .es-footer-body ul li,
              .es-footer-body ol li,
              .es-footer-body a {
                  font-size: 16px !important;
              }

              .es-infoblock p,
              .es-infoblock ul li,
              .es-infoblock ol li,
              .es-infoblock a {
                  font-size: 12px !important;
              }

              *[class='gmail-fix'] {
                  display: none !important;
              }

              .es-m-txt-c,
              .es-m-txt-c h1,
              .es-m-txt-c h2,
              .es-m-txt-c h3 {
                  text-align: center !important;
              }

              .es-m-txt-r,
              .es-m-txt-r h1,
              .es-m-txt-r h2,
              .es-m-txt-r h3 {
                  text-align: right !important;
              }

              .es-m-txt-l,
              .es-m-txt-l h1,
              .es-m-txt-l h2,
              .es-m-txt-l h3 {
                  text-align: left !important;
              }

              .es-m-txt-r img,
              .es-m-txt-c img,
              .es-m-txt-l img {
                  display: inline !important;
              }

              .es-button-border {
                  display: block !important;
              }

              .es-btn-fw {
                  border-width: 10px 0px !important;
                  text-align: center !important;
              }

              .es-adaptive table,
              .es-btn-fw,
              .es-btn-fw-brdr,
              .es-left,
              .es-right {
                  width: 100% !important;
              }

              .es-content table,
              .es-header table,
              .es-footer table,
              .es-content,
              .es-footer,
              .es-header {
                  width: 100% !important;
                  max-width: 600px !important;
              }

              .es-adapt-td {
                  display: block !important;
                  width: 100% !important;
              }

              .adapt-img {
                  width: 100% !important;
                  height: auto !important;
              }

              .es-m-p0 {
                  padding: 0px !important;
              }

              .es-m-p0r {
                  padding-right: 0px !important;
              }

              .es-m-p0l {
                  padding-left: 0px !important;
              }

              .es-m-p0t {
                  padding-top: 0px !important;
              }

              .es-m-p0b {
                  padding-bottom: 0 !important;
              }

              .es-m-p20b {
                  padding-bottom: 20px !important;
              }

              .es-mobile-hidden,
              .es-hidden {
                  display: none !important;
              }

              tr.es-desk-hidden,
              td.es-desk-hidden,
              table.es-desk-hidden {
                  width: auto !important;
                  overflow: visible !important;
                  float: none !important;
                  max-height: inherit !important;
                  line-height: inherit !important;
              }

              tr.es-desk-hidden {
                  display: table-row !important;
              }

              table.es-desk-hidden {
                  display: table !important;
              }

              td.es-desk-menu-hidden {
                  display: table-cell !important;
              }

              .es-menu td {
                  width: 1% !important;
              }

              table.es-table-not-adapt,
              .esd-block-html table {
                  width: auto !important;
              }

              table.es-social {
                  display: inline-block !important;
              }

              table.es-social td {
                  display: inline-block !important;
              }

              a.es-button,
              button.es-button {
                  font-size: 16px !important;
                  display: block !important;
                  border-left-width: 0px !important;
                  border-right-width: 0px !important;
              }
              }

              /*
              END RESPONSIVE STYLES
              */
              .es-p-default {
              padding-top: 20px;
              padding-right: 30px;
              padding-bottom: 0px;
              padding-left: 30px;
              }

              .es-p-all-default {
              padding: 0px;
              }

              a.es-button,
              button.es-button {
              border-style: solid;
              border-color: #3b2495;
              border-width: 12px 40px 13px 40px;
              display: inline-block;
              background: #3b2495;
              border-radius: 30px;
              font-size: 20px;
              font-family: Montserrat, Helvetica, Roboto, Arial, sans-serif;
              font-weight: normal;
              font-style: normal;
              line-height: 120%;
              color: #ffffff;
              text-decoration: none !important;
              width: auto;
              text-align: center;
              }
            </style>
            <script async custom-element='amp-list' src='https://cdn.ampproject.org/v0/amp-list-0.1.js'></script>
            <script async custom-template='amp-mustache' src='https://cdn.ampproject.org/v0/amp-mustache-0.2.js'></script>
            <script async custom-element='amp-bind' src='https://cdn.ampproject.org/v0/amp-bind-0.1.js'></script>
        </head>

        <body>
          <div class='es-wrapper-color'>
              <!--[if gte mso 9]>
            <v:background xmlns:v='urn:schemas-microsoft-com:vml' fill='t'>
              <v:fill type='tile' color='#F7F7F7'></v:fill>
            </v:background>
          <![endif]-->
              <table cellpadding='0' cellspacing='0' class='es-wrapper' width='100%' style='background-position: center top;'>
                  <tbody>
                      <tr>
                          <td valign='top' class='esd-email-paddings'>
                              <table cellpadding='0' cellspacing='0' class='es-content esd-footer-popover' align='left'>
                                  <tbody>
                                      <tr>
                                          <td class='esd-stripe' align='left'>
                                              <table bgcolor='#ffffff' class='es-content-body' align='left' cellpadding='0' cellspacing='0' width='600'>
                                                  <tbody>
                                                      <tr class='es-visible-amp-html-only'>
                                                          <td class='esd-structure es-p25t es-p20b es-p20r es-p20l' style='background-position: center bottom;' align='left'>
                                                              <table width='100%' cellspacing='0' cellpadding='0'>
                                                                  <tbody>
                                                                      <tr>
                                                                          <td class='esd-container-frame' width='558' valign='top' align='left'>
                                                                              <table width='100%' cellspacing='0' cellpadding='0'>
                                                                                  <tbody>
                                                                                      <tr>
                                                                                          <td class='esd-block-text es-p15t es-p15r es-p15l' align='left'>
                                                                                              <p><strong>Cher(ère) ".$data['name']." </strong></p>
                                                                                          </td>
                                                                                      </tr>
                                                                                      <tr>
                                                                                          <td class='esd-block-text es-p10t es-p5b es-p15r es-p15l' align='left'>
                                                                                              <p>Nous accusons bonne réception de votre article et vous reviendrons après validation.<br><br>Le comité scientifique.<br></p>
                                                                                          </td>
                                                                                      </tr>
                                                                                  </tbody>
                                                                              </table>
                                                                          </td>
                                                                      </tr>

                                                                  </tbody>
                                                              </table>
                                                          </td>
                                                      </tr>
                                                  </tbody>
                                              </table>
                                          </td>
                                      </tr>
                                  </tbody>
                              </table>
                          </td>
                      </tr>
                  </tbody>
              </table>
          </div>
      </body>

      </html>

    ";
    return self::sendMailSmpt($container, $to, $subject, $message);
  }


  public static function sendMailAtelierPratiqueOld($container, $to, $subject, $data)
  {
    header('Content-Type: text/html; charset=utf-8');

    $message ="
        <!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional //EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>

        <html xmlns='http://www.w3.org/1999/xhtml' xmlns:o='urn:schemas-microsoft-com:office:office'>

        <head>
            <meta charset='UTF-8'>
            <meta content='width=device-width, initial-scale=1' name='viewport'>
            <meta name='x-apple-disable-message-reformatting'>
            <meta http-equiv='X-UA-Compatible' content='IE=edge'>
            <meta content='telephone=no' name='format-detection'>
            <title></title>
            <!--[if (mso 16)]> <style type='text/css'> a {text-decoration: none;} </style> <![endif]-->
            <!--[if gte mso 9]><style>sup { font-size: 100% !important; }</style><![endif]-->
            <!--[if !mso]><!-- -->
            <link href='https://fonts.googleapis.com/css?family=Montserrat:500,800' rel='stylesheet'>
            <!--<![endif]-->
            <!--[if gte mso 9]>
                <xml>
                    <o:OfficeDocumentSettings>
                    <o:AllowPNG></o:AllowPNG>
                    <o:PixelsPerInch>96</o:PixelsPerInch>
                    </o:OfficeDocumentSettings>
                </xml>
                <![endif]-->
                    <style>
                    /*
                CONFIG STYLES
                Please do not delete and edit CSS styles below
                */
                /* IMPORTANT THIS STYLES MUST BE ON FINAL EMAIL */
                #outlook a {
                padding: 0;
                }

                .ExternalClass {
                width: 100%;
                }

                .ExternalClass,
                .ExternalClass p,
                .ExternalClass span,
                .ExternalClass font,
                .ExternalClass td,
                .ExternalClass div {
                line-height: 100%;
                }

                .es-button {
                mso-style-priority: 100 !important;
                text-decoration: none !important;
                }

                a[x-apple-data-detectors] {
                color: inherit !important;
                text-decoration: none !important;
                font-size: inherit !important;
                font-family: inherit !important;
                font-weight: inherit !important;
                line-height: inherit !important;
                }

                .es-desk-hidden {
                display: none;
                float: left;
                overflow: hidden;
                width: 0;
                max-height: 0;
                line-height: 0;
                mso-hide: all;
                }

                a.es-button:hover {
                border-color: #2CB543 !important;
                background: #2CB543 !important;
                }

                a.es-secondary:hover {
                border-color: #ffffff !important;
                background: #ffffff !important;
                }

                /*
                END OF IMPORTANT
                */
                s {
                text-decoration: line-through;
                }

                html,
                body {
                width: 100%;
                font-family: Montserrat, Helvetica, Roboto, Arial, sans-serif;
                -webkit-text-size-adjust: 100%;
                -ms-text-size-adjust: 100%;
                }

                table {
                mso-table-lspace: 0pt;
                mso-table-rspace: 0pt;
                border-collapse: collapse;
                border-spacing: 0px;
                }

                table td,
                html,
                body,
                .es-wrapper {
                padding: 0;
                Margin: 0;
                }

                .es-content,
                .es-header,
                .es-footer {
                table-layout: fixed !important;
                width: 100%;
                }

                img {
                display: block;
                border: 0;
                outline: none;
                text-decoration: none;
                -ms-interpolation-mode: bicubic;
                }

                table tr {
                border-collapse: collapse;
                }

                p,
                hr {
                Margin: 0;
                }

                h1,
                h2,
                h3,
                h4,
                h5 {
                Margin: 0;
                line-height: 120%;
                mso-line-height-rule: exactly;
                font-family: Montserrat, Helvetica, Roboto, Arial, sans-serif;
                }

                p,
                ul li,
                ol li,
                a {
                -webkit-text-size-adjust: none;
                -ms-text-size-adjust: none;
                mso-line-height-rule: exactly;
                }

                .es-left {
                float: left;
                }

                .es-right {
                float: right;
                }

                .es-p5 {
                padding: 5px;
                }

                .es-p5t {
                padding-top: 5px;
                }

                .es-p5b {
                padding-bottom: 5px;
                }

                .es-p5l {
                padding-left: 5px;
                }

                .es-p5r {
                padding-right: 5px;
                }

                .es-p10 {
                padding: 10px;
                }

                .es-p10t {
                padding-top: 10px;
                }

                .es-p10b {
                padding-bottom: 10px;
                }

                .es-p10l {
                padding-left: 10px;
                }

                .es-p10r {
                padding-right: 10px;
                }

                .es-p15 {
                padding: 15px;
                }

                .es-p15t {
                padding-top: 15px;
                }

                .es-p15b {
                padding-bottom: 15px;
                }

                .es-p15l {
                padding-left: 15px;
                }

                .es-p15r {
                padding-right: 15px;
                }

                .es-p20 {
                padding: 20px;
                }

                .es-p20t {
                padding-top: 20px;
                }

                .es-p20b {
                padding-bottom: 20px;
                }

                .es-p20l {
                padding-left: 20px;
                }

                .es-p20r {
                padding-right: 20px;
                }

                .es-p25 {
                padding: 25px;
                }

                .es-p25t {
                padding-top: 25px;
                }

                .es-p25b {
                padding-bottom: 25px;
                }

                .es-p25l {
                padding-left: 25px;
                }

                .es-p25r {
                padding-right: 25px;
                }

                .es-p30 {
                padding: 30px;
                }

                .es-p30t {
                padding-top: 30px;
                }

                .es-p30b {
                padding-bottom: 30px;
                }

                .es-p30l {
                padding-left: 30px;
                }

                .es-p30r {
                padding-right: 30px;
                }

                .es-p35 {
                padding: 35px;
                }

                .es-p35t {
                padding-top: 35px;
                }

                .es-p35b {
                padding-bottom: 35px;
                }

                .es-p35l {
                padding-left: 35px;
                }

                .es-p35r {
                padding-right: 35px;
                }

                .es-p40 {
                padding: 40px;
                }

                .es-p40t {
                padding-top: 40px;
                }

                .es-p40b {
                padding-bottom: 40px;
                }

                .es-p40l {
                padding-left: 40px;
                }

                .es-p40r {
                padding-right: 40px;
                }

                .es-menu td {
                border: 0;
                }

                .es-menu td a img {
                display: inline-block !important;
                }

                /*
                END CONFIG STYLES
                */
                a {
                font-family: Montserrat, Helvetica, Roboto, Arial, sans-serif;
                font-size: 16px;
                text-decoration: underline;
                }

                h1 {
                font-size: 32px;
                font-style: normal;
                font-weight: bold;
                color: #4a4a4a;
                }

                h1 a {
                font-size: 32px;
                }

                h2 {
                font-size: 24px;
                font-style: normal;
                font-weight: bold;
                color: #4a4a4a;
                }

                h2 a {
                font-size: 24px;
                }

                h3 {
                font-size: 20px;
                font-style: normal;
                font-weight: bold;
                color: #4A4A4A;
                }

                h3 a {
                font-size: 20px;
                }

                p,
                ul li,
                ol li {
                font-size: 16px;
                font-family: Montserrat, Helvetica, Roboto, Arial, sans-serif;
                line-height: 150%;
                }

                ul li,
                ol li {
                Margin-bottom: 15px;
                }

                .es-menu td a {
                text-decoration: none;
                display: block;
                }

                .es-wrapper {
                width: 100%;
                height: 100%;
                background-image: ;
                background-repeat: repeat;
                background-position: center top;
                }

                .es-wrapper-color {
                background-color: #F7F7F7;
                }

                .es-content-body {
                background-color: transparent;
                }

                .es-content-body p,
                .es-content-body ul li,
                .es-content-body ol li {
                color: #4A4A4A;
                }

                .es-content-body a {
                color: #3b2495;
                }

                .es-header {
                background-color: #34265f;
                background-repeat: repeat;
                background-position: center bottom;
                }

                .es-header-body {
                background-color: #34265f;
                }

                .es-header-body p,
                .es-header-body ul li,
                .es-header-body ol li {
                color: #ffffff;
                font-size: 14px;
                }

                .es-header-body a {
                color: #ffffff;
                font-size: 14px;
                }

                .es-footer {
                background-color: #f7f7f7;
                background-repeat: repeat;
                background-position: center top;
                background-image: url(https://ohvpkv.stripocdn.email/content/guids/CABINET_7dfb659af020be618a1cf3d530b28d98/images/75021564382669317.png);
                }

                .es-footer-body {
                background-color: #f7f7f7;
                }

                .es-footer-body p,
                .es-footer-body ul li,
                .es-footer-body ol li {
                color: #ffffff;
                font-size: 16px;
                }

                .es-footer-body a {
                color: #ffffff;
                font-size: 16px;
                }

                .es-infoblock,
                .es-infoblock p,
                .es-infoblock ul li,
                .es-infoblock ol li {
                line-height: 120%;
                font-size: 12px;
                color: #cccccc;
                }

                .es-infoblock a {
                font-size: 12px;
                color: #cccccc;
                }

                .es-button-border {
                border-style: solid solid solid solid;
                border-color: #3b2495 #3b2495 #3b2495 #3b2495;
                background: #3b2495;
                border-width: 0px 0px 0px 0px;
                display: inline-block;
                border-radius: 30px;
                width: auto;
                }

                /*
                RESPONSIVE STYLES
                Please do not delete and edit CSS styles below.

                If you don't need responsive layout, please delete this section.
                */
                @media only screen and (max-width: 600px) {
                u+#body {
                    width: 100vw !important;
                }

                p,
                ul li,
                ol li,
                a {
                    font-size: 16px !important;
                    line-height: 150% !important;
                }

                h1 {
                    font-size: 30px !important;
                    text-align: center;
                    line-height: 120% !important;
                }

                h2 {
                    font-size: 26px !important;
                    text-align: center;
                    line-height: 120% !important;
                }

                h3 {
                    font-size: 20px !important;
                    text-align: center;
                    line-height: 120% !important;
                }

                h1 a {
                    font-size: 30px !important;
                }

                h2 a {
                    font-size: 26px !important;
                }

                h3 a {
                    font-size: 20px !important;
                }

                .es-menu td a {
                    font-size: 16px !important;
                }

                .es-header-body p,
                .es-header-body ul li,
                .es-header-body ol li,
                .es-header-body a {
                    font-size: 16px !important;
                }

                .es-footer-body p,
                .es-footer-body ul li,
                .es-footer-body ol li,
                .es-footer-body a {
                    font-size: 16px !important;
                }

                .es-infoblock p,
                .es-infoblock ul li,
                .es-infoblock ol li,
                .es-infoblock a {
                    font-size: 12px !important;
                }

                *[class='gmail-fix'] {
                    display: none !important;
                }

                .es-m-txt-c,
                .es-m-txt-c h1,
                .es-m-txt-c h2,
                .es-m-txt-c h3 {
                    text-align: center !important;
                }

                .es-m-txt-r,
                .es-m-txt-r h1,
                .es-m-txt-r h2,
                .es-m-txt-r h3 {
                    text-align: right !important;
                }

                .es-m-txt-l,
                .es-m-txt-l h1,
                .es-m-txt-l h2,
                .es-m-txt-l h3 {
                    text-align: left !important;
                }

                .es-m-txt-r img,
                .es-m-txt-c img,
                .es-m-txt-l img {
                    display: inline !important;
                }

                .es-button-border {
                    display: block !important;
                }

                .es-btn-fw {
                    border-width: 10px 0px !important;
                    text-align: center !important;
                }

                .es-adaptive table,
                .es-btn-fw,
                .es-btn-fw-brdr,
                .es-left,
                .es-right {
                    width: 100% !important;
                }

                .es-content table,
                .es-header table,
                .es-footer table,
                .es-content,
                .es-footer,
                .es-header {
                    width: 100% !important;
                    max-width: 600px !important;
                }

                .es-adapt-td {
                    display: block !important;
                    width: 100% !important;
                }

                .adapt-img {
                    width: 100% !important;
                    height: auto !important;
                }

                .es-m-p0 {
                    padding: 0px !important;
                }

                .es-m-p0r {
                    padding-right: 0px !important;
                }

                .es-m-p0l {
                    padding-left: 0px !important;
                }

                .es-m-p0t {
                    padding-top: 0px !important;
                }

                .es-m-p0b {
                    padding-bottom: 0 !important;
                }

                .es-m-p20b {
                    padding-bottom: 20px !important;
                }

                .es-mobile-hidden,
                .es-hidden {
                    display: none !important;
                }

                tr.es-desk-hidden,
                td.es-desk-hidden,
                table.es-desk-hidden {
                    width: auto !important;
                    overflow: visible !important;
                    float: none !important;
                    max-height: inherit !important;
                    line-height: inherit !important;
                }

                tr.es-desk-hidden {
                    display: table-row !important;
                }

                table.es-desk-hidden {
                    display: table !important;
                }

                td.es-desk-menu-hidden {
                    display: table-cell !important;
                }

                .es-menu td {
                    width: 1% !important;
                }

                table.es-table-not-adapt,
                .esd-block-html table {
                    width: auto !important;
                }

                table.es-social {
                    display: inline-block !important;
                }

                table.es-social td {
                    display: inline-block !important;
                }

                a.es-button,
                button.es-button {
                    font-size: 16px !important;
                    display: block !important;
                    border-left-width: 0px !important;
                    border-right-width: 0px !important;
                }
                }

                /*
                END RESPONSIVE STYLES
                */
                .es-p-default {
                padding-top: 20px;
                padding-right: 30px;
                padding-bottom: 0px;
                padding-left: 30px;
                }

                .es-p-all-default {
                padding: 0px;
                }

                a.es-button,
                button.es-button {
                border-style: solid;
                border-color: #3b2495;
                border-width: 12px 40px 13px 40px;
                display: inline-block;
                background: #3b2495;
                border-radius: 30px;
                font-size: 20px;
                font-family: Montserrat, Helvetica, Roboto, Arial, sans-serif;
                font-weight: normal;
                font-style: normal;
                line-height: 120%;
                color: #ffffff;
                text-decoration: none !important;
                width: auto;
                text-align: center;
                }
            </style>
            <script async custom-element='amp-list' src='https://cdn.ampproject.org/v0/amp-list-0.1.js'></script>
            <script async custom-template='amp-mustache' src='https://cdn.ampproject.org/v0/amp-mustache-0.2.js'></script>
            <script async custom-element='amp-bind' src='https://cdn.ampproject.org/v0/amp-bind-0.1.js'></script>
        </head>

        <body>
            <div class='es-wrapper-color'>
                <!--[if gte mso 9]>
                    <v:background xmlns:v='urn:schemas-microsoft-com:vml' fill='t'>
                        <v:fill type='tile' color='#F7F7F7'></v:fill>
                    </v:background>
                <![endif]-->
                <table cellpadding='0' cellspacing='0' class='es-wrapper' width='100%' style='background-position: center top;'>
                    <tbody>
                        <tr>
                            <td valign='top' class='esd-email-paddings'>
                                <table cellpadding='0' cellspacing='0' class='es-content esd-footer-popover' align='center'>
                                    <tbody>
                                        <tr>
                                            <td class='esd-stripe' align='center'>
                                                <table bgcolor='#ffffff' class='es-content-body' align='center' cellpadding='0' cellspacing='0' width='600'>
                                                    <tbody>
                                                        <tr class='es-visible-amp-html-only'>
                                                            <td class='esd-structure es-p25t es-p20b es-p20r es-p20l' style='background-position: center bottom;' align='left'>
                                                                <table width='100%' cellspacing='0' cellpadding='0'>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td class='esd-container-frame' width='558' valign='top' align='center'>
                                                                                <table width='100%' cellspacing='0' cellpadding='0'>
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td class='esd-block-text es-p15t es-p15r es-p15l' align='left'>
                                                                                                <p><strong>Cher(ère) ".$data['first_name']."  ".$data['last_name']."</strong></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td class='esd-block-text es-p10t es-p5b es-p15r es-p15l' align='left'>
                                                                                                <p>".$data['message']."<br></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        
                                                                                    </tbody>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                       
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </body>

    </html>";


    return self::sendMailSmpt($container, $to, $subject, $message);

  }

  public static function sendMailAtelierPratique($container, $to, $subject, $data)
  {
    header('Content-Type: text/html; charset=utf-8');

    $message ='
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <style>
                    body {
                    font-family: \'Verdana\', sans-serif;
                    margin: 0;
                    padding: 0;
                    background-color: #fafafa;
                    }
                    .email-container {
                    max-width: 600px;
                    margin: 20px auto;
                    background: #914b90;
                    color: white;
                    border-radius: 8px;
                    overflow: hidden;
                    }
                    .email-header {
                    padding: 20px;
                    text-align: center;
                    font-size: 26px;
                    font-weight: bold;
                    }
                    .email-body {
                    background: white;
                    padding: 20px;
                    color: #333;
                    text-align: left;
                    }
                    .email-body h2 {
                    color: #914b90;
                    }
                    .email-body li {
                        margin-bottom:10px;
                        }
                    .email-footer {
                    padding: 15px;
                    text-align: center;
                    font-size: 12px;
                    background-color: #914b90;
                    color: #ffffff;
                    }
                    .button {
                    display: block;
                    width: fit-content;
                    margin: 20px auto;
                    padding: 10px 20px;
                    background-color: #914b90;
                    color: white !important;
                    text-decoration: none;
                    border-radius: 4px;
                    text-align: center;
                    font-size: 16px;
                    }
                    .button:hover {
                    background-color: #FB3567;
                    }
                </style>
            </head>
            <body>
                <div class="email-container">
                    <div class="email-header">Félicitations !</div>
                    <div class="email-body">
                    <h2>Votre inscription est confirmée</h2>
                    <p>Cher(ère) '.$data['first_name'].'  '.$data['last_name'].',</p>
                    <p>Merci pour votre inscription à nos ateliers pratiques. Nous avons hâte de vous accueillir !</p>
                    <p><strong>Détails de votre inscription :</strong></p>
                    <ul>';

    foreach ($data['activities'] as $key => $activity) {
        $message .='<li>Atelier : '.$activity['what'].' <br> Date : '.$activity['when'].'<br></li>';
    }

    $message .='
                       
                    </ul>
                    </div>
                    <div class="email-footer">
                    Cet email vous est envoyé par la SOSECAR.  
                    </div>
                </div>
            </body>
        </html>

        ';


    return self::sendMailSmpt($container, $to, $subject, $message);

  }

  public static function sendMailSmpt($container, $to, $subject, $message)
  {
    // Instantiation and passing `true` enables exceptions
      date_default_timezone_set('Etc/UTC');
      header('Content-Type: text/html; charset=utf-8');
      $mail = new PHPMailer(true);

      try {
        

          $mail->isSMTP();
          //$mail->Host = 'mail57.lwspanel.com';  //gmail SMTP server
          $mail->Host = 'smtp.ionos.com';  //gmail SMTP server
          $mail->SMTPAuth = true;
          //to view proper logging details for success and error messages
          //$mail->SMTPDebug = 1;
          $mail->Username = $container->notifMailAdress;   //email
          $mail->Password = $container->notifMailAdressPwd ;   //16 character obtained from app password created
          //$mail->Port = 465;                    //SMTP port
          $mail->Port = 587;                    //SMTP port
          //$mail->SMTPSecure = "SSL";
          //$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
          $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted


          $mail->CharSet = 'utf-8';
          $mail->Encoding = 'base64';


          //$mail->Encoding = "16bit";
          $mail->addCustomHeader('MIME-Version: 1.0');
          //$mail->addCustomHeader('Content-Type: text/html; charset=ISO-8859-1');

          //Recipients
         // $mail->setFrom($container->notifMailAdress, 'SOSECAR');
          $mail->setFrom($container->notifMailAdress, 'SOSECAR');
          $mail->addAddress($to);     // Add a recipient
          //$mail->addAddress('abdoulayedieye221@gmail.com');     // Add a recipient
          $mail->addReplyTo($container->notifMailAdress, 'SOSECAR');
          $mail->addBCC('pedredieye@gmail.com');

          // Attachments
          //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
          //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

          // Content
          $mail->isHTML(true);                                  // Set email format to HTML
          $mail->Subject = $subject;
          $mail->Body    = $message;
          //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

          $mail->send();
          return 1;
      } catch (Exception $e) {
          echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
          return 0;
      }


  }

  public static function sendMailSmptOld($container, $to, $subject, $message)
  {
    // Instantiation and passing `true` enables exceptions
      date_default_timezone_set('Etc/UTC');
      header('Content-Type: text/html; charset=utf-8');
      $mail = new PHPMailer(true);

      try {
          //Server settings
          //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
          $mail->SMTPDebug = 0;                      // Enable verbose debug output
          $mail->isSMTP();
          /*                                            // Send using SMTP
          $mail->Host       = 'mail33.lwspanel.com';                    // Set the SMTP server to send through
          $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
          $mail->Username   = $container->notifMailAdress;                     // SMTP username
          $mail->Password   = 'cH1*UJvTYQ';                               // SMTP password
          $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
          $mail->CharSet = 'utf-8';
          $mail->Encoding = 'base64';
          */



          $mail->Host       = "smtp.ionos.fr";//'mail33.lwspanel.com';                    // Set the SMTP server to send through
          //$mail->Host       = "mail.sunumemes.com";//'mail33.lwspanel.com';                    // Set the SMTP server to send through
          $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
          $mail->Username   = "p.dieye@outalma.com";                     // SMTP username
          //$mail->Username   = $container->notifMailAdress;                     // SMTP username
          $mail->Password   = "GddPUSqH89$"; //"vU3!W_ufgc";//'cH1*UJvTYQ';                               // SMTP password
          //$mail->Password   = ".NEZ43n;Go5f"; //"vU3!W_ufgc";//'cH1*UJvTYQ';                               // SMTP password
          //$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
          $mail->SMTPSecure="TLS";
          $mail->CharSet = 'utf-8';
          $mail->Encoding = 'base64';


          //cH1*UJvTYQ
          //mamadou.dieye@datas-impact.com

          //$mail->Encoding = "16bit";
          $mail->addCustomHeader('MIME-Version: 1.0');
          //$mail->addCustomHeader('Content-Type: text/html; charset=ISO-8859-1');
          $mail->Port       = 587;                                    // TCP port to connect to

          //Recipients
          $mail->setFrom($container->notifMailAdress, 'SOSECAR');
          $mail->addAddress($to);     // Add a recipient
          //$mail->addAddress('ellen@example.com');               // Name is optional
          $mail->addReplyTo($container->notifMailAdress, 'SOSECAR');
          //$mail->addCC('cc@example.com');
          //$mail->addBCC('bcc@example.com');
          $mail->addBCC('pedredieye@gmail.com');

          // Attachments
          //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
          //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

          // Content
          $mail->isHTML(true);                                  // Set email format to HTML
          $mail->Subject = $subject;
          $mail->Body    = $message;
          //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

          $mail->send();
          echo 'Message has been sent';
      } catch (Exception $e) {
          echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
      }


  }

  public static function sendSms($container, $to, $message)
  {
      //config
      $url        = $container->sms_api_url;
      $login      = $container->sms_api_login;    //votre identifant allmysms
      $apiKey     = $container->sms_api_key;    //votre mot de passe allmysms
      //$message    = 'Nouvelle livraison';    //le message SMS, attention pas plus de 160 caractères
      $sender     = $container->sms_api_sender;  //l'expediteur, attention pas plus de 11 caractères alphanumériques
      $msisdn     = $to;    //numéro de téléphone du destinataire
      $smsData    = "<DATA>
         <MESSAGE><![CDATA[".$message."]]></MESSAGE>
         <TPOA>$sender</TPOA>
         <SMS>
            <MOBILEPHONE>$msisdn</MOBILEPHONE>
         </SMS>
      </DATA>";

      $fields = array(
      'login'    => urlencode($login),
      'apiKey'      => urlencode($apiKey),
      'smsData'       => urlencode($smsData),
      );

      $fieldsString = "";
      foreach($fields as $key=>$value) {
          $fieldsString .= $key.'='.$value.'&';
      }
      rtrim($fieldsString, '&');

      try {

          $ch = curl_init();
          curl_setopt($ch,CURLOPT_URL, $url);
          curl_setopt($ch,CURLOPT_POST, count($fields));
          curl_setopt($ch,CURLOPT_POSTFIELDS, $fieldsString);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

          $result = curl_exec($ch);
          //echo $result;
          curl_close($ch);

      } catch (Exception $e) {
          echo 'Api allmysms injoignable ou trop longue a repondre ' . $e->getMessage();
      }
  }

}