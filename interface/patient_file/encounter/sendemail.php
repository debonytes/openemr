<?php
require_once("../../globals.php");
require_once "$srcdir/classes/postmaster.php";

$emailDestination = 'hermiebarit@gmail.com';
$firstNameDestination = 'Hermie';
$lastNameDestination = 'Barit';

$mail = new MyMailer();
$mail->From = 'hermie.ezelink@gmail.com';
$mail->FromName = 'Hermie';
$mail->Body = 'This is another test.';
$mail->Subject = 'Sample Subject';
$mail->AddAddress($emailDestination, $firstNameDestination.", ".$lastNameDestination);
if(!$mail->Send()) {
  error_log("There has been a mail error sending to " . $firstNameDestination .
   " " . $mail->ErrorInfo);
  }
else
  {
  echo "Email successfully sent to $emailDestination...";
}
/*
$to_email = "hermiebarit@gmail.com";
$subject = "Simple Email Test via PHP";
$body = "Hi,nn This is test email send by PHP Script";
$headers = "From: sender\'s email";
 
if (mail($to_email, $subject, $body, $headers)) {
    echo "Email successfully sent to $to_email...";
} else {
    echo "Email sending failed...";
}
*/