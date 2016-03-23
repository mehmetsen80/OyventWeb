<?php

/**
 * Created by PhpStorm.
 * User: msen
 * Date: 3/23/16
 * Time: 5:34 PM
 */



require_once(dirname(dirname(__FILE__)) . '/lib/PhpMailer/PHPMailerAutoload.php');

use PHPMailer as PHPMailerClass;

class Mailer
{
    function __construct() {

    }

    //when the user registers, send a welcome message
    public function sendWelcomeMessage($email){

        $noreply = "noreply@oyvent.com";
        $subject = "Welcome to Oyvent";
        $message =  "Dear Oyvent user,<br><br>Xtalk allows you to share community experience at local level.<br>Welcome to Oyvent!".
            "<br><br><br>Best Regards,".
            "<br><br><br><b>Oyvent Team</b> ";


        $mail = new PHPMailerClass;
        $mail->isSMTP();
        $mail->Host = 'localhost';
        $mail->From = $noreply;
        $mail->FromName = 'Oyvent';
        $mail->addAddress($email);
        $mail->addReplyTo($noreply, 'NOREPLY');
        $mail->addBCC($email);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->send();

    }
}


?>