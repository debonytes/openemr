<?php
require_once("../../globals.php");
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
require_once "$srcdir/classes/postmaster.php";

$body = '';
$fname = '';
$lname = '';
$date_received = '';
$form_name = '';

if(isset($_POST['send_email'])){
    $pid = intval($_POST['pid']);
    $emailDestination = 'hermiebarit@gmail.com';
    $firstNameDestination = "John";
    $lastNameDestination = "Doe";
    $formid = intval($_POST['formid']);
    $formdir = 'form_' . $_POST['formdir'];

    $query = "SELECT * FROM forms WHERE id = ?";
    $res = sqlQuery($query, array($formid)); 

    if($res){
        $form_name = $res['form_name'];
        $fname = get_patient_info($pid, 'fname');
        $lname = get_patient_info($pid, 'lname');
        $date_received = $res['date'];

        $body .= "This person {$fname} {$lname} have been recieved a {$form_name} on {$date_received}";
    }

    $mail = new MyMailer();
    $mail->From = 'notifications@kunaempower.com';
    $mail->FromName = $fname . '' . $lname;
    $mail->Body = $body;
    $mail->Subject = $form_name . ' Received';
    $mail->AddAddress($emailDestination, $firstNameDestination.", ".$lastNameDestination);
    if(!$mail->Send()) {
      echo ("There has been a mail error sending to " . $firstNameDestination .
       " " . $mail->ErrorInfo);
      }
    else
      {
      echo "Email successfully sent to $emailDestination...";
    }
}
