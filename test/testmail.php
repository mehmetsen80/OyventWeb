<?php 

error_reporting(-1);
ini_set('display_errors', 'On');




/*$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html\r\n";
$headers .= 'From: from@example.com' . "\r\n" .
'Reply-To: reply@example.com' . "\r\n" .
'X-Mailer: PHP/' . phpversion();

//$headers = implode("\r\n", $headers);
$mail = mail('mehmetsen80@gmail.com', 'My subject', 'test test test', $headers);
// send email
//$mail = mail('mehmetsen80@hotmail.com','Subject: My subject','test test test','From: noreply@oyvent.com');
//$mail = mail('mehmetsen80@gmail.com','My subject','test test test');

echo "mail:".$mail;*/


include("../class/PHPMailerAutoload.php");

$mail = new PHPMailer;

//$mail->SMTPDebug = 3;                               // Enable verbose debug output

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'localhost';  // Specify main and backup SMTP servers
/*$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'user@example.com';                 // SMTP username
$mail->Password = 'secret';                           // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 587;  */                                  // TCP port to connect to

$mail->From = 'info@oyvent.com';
$mail->FromName = 'Oyvent';
$mail->addAddress('mehmetsen80@gmail.com');     // Add a recipient
//$mail->addAddress('mehmetsen80@hotmail.com');               // Name is optional
$mail->addReplyTo('noreply@oyvent.com', 'NOREPLY');
$mail->addCC('mehmetsen80@hotmail.com');
//$mail->addBCC('bcc@example.com');

//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = 'Here is the subject';
$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Untitled Document</title>
</head>

<body>
</body>
</html>