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
    //$emailDestination = 'cgdebona@gmail.com';
    $firstNameDestination = "John";
    $lastNameDestination = "Doe";
    $formid = intval($_POST['formid']);
    $formdir = 'form_' . $_POST['formdir'];

    $query = "SELECT * FROM forms WHERE id = ?";
    $res = sqlQuery($query, array($formid)); 
    $examiner = '';
    $starttime = '';
    $endtime = '';

    if($res){
        $form_name = $res['form_name'];
        $formdir = $res['formdir'];
        $formid = $res['form_id'];
        $form_table = 'form_' . $formdir;
        $details = get_form_details_by_id($formid, $form_table);
        $fname = get_patient_info($pid, 'fname');
        $lname = get_patient_info($pid, 'lname');
        $date_received = ($details['dateofservice']) ? $details['dateofservice'] : $details['date'];
        if($details['examiner']) {
            $examiner = $details['examiner'];
        } elseif($details['counselor']){
            $examiner = $details['counselor'];
        } elseif($details['name_examiner']){
            $examiner = $details['name_examiner'];
        } else {
            $examiner = '';
        }
        $starttime =  ($details['starttime']) ? $details['starttime'] : '';
        $endtime =  ($details['endtime']) ? $details['endtime'] : '';

        $body .= "Name of Client: {$fname} {$lname} \n";
        $body .= "Name of Provider: {$examiner}\n";
        $body .= "Service Rendered: {$form_name}\n";
        $body .= "Date of Appointment: {$date_received}\n";
        $body .= "Start Time: {$starttime}\n";
        $body .= "End Time: {$endtime}\n";
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
