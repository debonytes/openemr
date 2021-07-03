<?php

require_once("user.inc");
require_once("patient.inc");
require_once("classes/postmaster.php");

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
    $dir = $_POST['formdir'];
    $formdir = 'form_' . $dir;

    $query = "SELECT * FROM forms WHERE form_id = ? AND formdir = ?";
    $res = sqlQuery($query, array($formid, $dir)); 
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
            $row = get_provider_details($details['examiner']);
            $examiner = text($row['lname']) . ', ' . text($row['fname']) ;
        } elseif($details['counselor']){
            $row = get_provider_details($details['counselor']);
            $examiner = $examiner = text($row['lname']) . ', ' . text($row['fname']) ;
        } elseif($details['name_examiner']){
            $row = get_provider_details($details['name_examiner']);
            $examiner = $examiner = text($row['lname']) . ', ' . text($row['fname']) ;
        } else {
            $examiner = '';
        }
        $starttime =  ($details['starttime']) ? $details['starttime'] : '';
        $endtime =  ($details['endtime']) ? $details['endtime'] : '';
        //$billing_code = $details['billing_code'];
        if($details['billing_code']){
            $billing_code = $details['billing_code'];
        } elseif($details['diagnosis_code']){
            $billing_code = $details['diagnosis_code'];
        } else {
            $billing_code = '';
        }

        if($details['icd_code']){
            $icd_code = $details['icd_code'];
        } else {
            $icd_code = '';
        }

        if($details['session_type']){
            $session_type = $details['session_type'];
        } else {
            $session_type = '';
        }

        if($details['translator_used']){
            $translator_used = $details['translator_used'];
        } else {
            $translator_used = '';
        }

        $body .= "Name of Client: {$fname} {$lname} \n";
        $body .= "Name of Provider: {$examiner}\n";
        $body .= "Service Rendered: {$form_name}\n";
        
        if($billing_code)
        $body .= "Billing Code: {$billing_code}\n";

        if($icd_code)
            $body .= "ICD Code: {$icd_code}\n";

        if($translator_used)
            $body .= "Translator Used: {$translator_used}\n";

        if($session_type)
            $body .= "Session Type: {$session_type}\n";

        $body .= "Date of Appointment: {$date_received}\n";
        $body .= "Start Time: {$starttime}\n";
        $body .= "End Time: {$endtime}\n";

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
    } else {
        echo "No email was sent.";
    }

    
}
