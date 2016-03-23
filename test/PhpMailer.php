<?php
/**
 * Created by PhpStorm.
 * User: msen
 * Date: 3/23/16
 * Time: 5:26 PM
 */


require_once(dirname(dirname(__FILE__)) . '/lib/PhpMailer/PHPMailerAutoload.php');

use PHPMailer as PHPMailerClass;

class Mailer
{
    function __construct() {

    }

    //when the user registers, send a welcome message
    public function sendWelcomeMessage($email){

        $noreply = "noreply@xtalkapp.com";
        $subject = "Welcome to Xtalk";
        $message =  "Dear Xtalk user,<br><br>Xtalk allows you to share community experience at local level.<br>Welcome to Xtalk!".
            "<br><br><br>Best Regards,".
            "<br><br><br><b>Xtalk Team</b> ";


        $mail = new PHPMailerClass;
        $mail->isSMTP();
        $mail->Host = 'localhost';
        $mail->From = $noreply;
        $mail->FromName = 'Xtalk';
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