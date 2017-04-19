<?php

require 'PHPMailer-master/PHPMailerAutoload.php';

class PHP_Mailer_Library
{

    public $sHost = 'smtp1.example.com;smtp2.example.com';  // Specify main and backup SMTP servers
    public $bSMTPAuth = true;                               // Enable SMTP authentication;
    public $sSMTUser = 'user@example.com';                 // SMTP username
    public $sSMTPassword = 'secret';                       // SMTP password
    public $sSMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    public $iSMTPort = '25';                            // Enable TLS encryption, `ssl` also accepted

    public function useGoogleSMTP(){
        $this->sHost = 'smtp.gmail.com';
        $this->bSMTPAuth= true;
        $this->sSMTUser = SMTP_GOOGLE_USERNAME;
        $this->sSMTPassword = SMTP_GOOGLE_PASSWORD;

        $this->sSMTPSecure = 'tls'; $this->iSMTPort = 465;

        //$this->sSMTPSecure = 'ssl'; $this->iSMTPort = 587;
    }

    public function useVisionBotSMTP(){
        $this->sHost = SMTP_VISIONBOT_HOSTNAME;
        $this->bSMTPAuth= true;
        $this->sSMTUser = SMTP_VISIONBOT_USERNAME;
        $this->sSMTPassword = SMTP_VISIONBOT_PASSWORD;

        //$this->sSMTPSecure = 'tls'; $this->iSMTPort = 384;
        //$this->sSMTPSecure = 'tls'; $this->iSMTPort = 345;

        $this->sSMTPSecure = 'ssl'; $this->iSMTPort = 290;
    }

    public function sendEmail($sSubject, $sBody, $sBodyPlainText, $sFromEmail, $sFromName='', $sDestinationEmail='', $sDestinationName='')
    {
        $mail = new PHPMailer;

        //$mail->SMTPDebug = 3;                               // Enable verbose debug output

        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = $this->sHost;
        $mail->SMTPAuth = $this->bSMTPAuth;
        $mail->Username = $this->sSMTUser;
        $mail->Password = $this->sSMTPassword;
        $mail->SMTPSecure = $this->sSMTPSecure;
        $mail->Port = $this->iSMTPort;                        // TCP port to connect to

        /*var_dump($sSubject);
        var_dump($sBody);
        var_dump($sFromEmail);
        var_dump($sDestinationEmail);*/

        $mail->setFrom($sFromEmail, ($sFromName !='' ? $sFromName : $sFromEmail));
        $mail->addAddress($sDestinationEmail, ($sDestinationName != '' ? $sDestinationName : $sDestinationEmail));     // Add a recipient

        /*$mail->addReplyTo('info@example.com', 'Information');

        $mail->addCC('cc@example.com');
        $mail->addBCC('bcc@example.com');

        $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name*/

        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = $sSubject;
        $mail->Body    = $sBody;
        $mail->AltBody = ($sBodyPlainText != '' ? $sBodyPlainText : strip_tags($sBody));

        if(!$mail->send()) {
            echo $mail->ErrorInfo . '<br/>';
            throw new Exception($mail->ErrorInfo);
        } else {
            return true;
        }
    }
}