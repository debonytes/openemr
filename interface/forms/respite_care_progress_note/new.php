<?php
/*
 * This program creates the respite_care_progress_note
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Terry Hill <terry@lilysystems.com>
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @author    Stephen Waite <stephen.waite@cmsvt.com>
 * @author    Jerry Padgett <sjpadgett@gmail.com>
 * @author    Ranganath Pathak <pathak@scrs1.org>
 * @copyright Copyright (C) 2007 Bo Huynh
 * @copyright Copyright (C) 2016 Terry Hill <terry@lillysystems.com>
 * @copyright Copyright (C) 2017-2019 Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (C) 2017-2019 Stephen Waite <stephen.waite@cmsvt.com>
 * @copyright Copyright (C) 2018 Jerry Padgett <sjpadgett@gmail.com>
 * @copyright Copyright (c) 2019 Ranganath Pathak <pathak@scrs1.org>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */


require_once("../../globals.php");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/encounter.inc");
require_once("$srcdir/group.inc");
require_once("$srcdir/api.inc");
require_once("$srcdir/acl.inc");
// require_once("date_qualifier_options.php");
require_once("$srcdir/user.inc");
require_once $GLOBALS['srcdir'].'/ESign/Api.php';

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Core\Header;
use OpenEMR\OeUI\OemrUI;
use ESign\Api;

$folderName = 'respite_care_progress_note';
$tableName = 'form_' . $folderName;

if (!$encounter) { // comes from globals.php
    die(xlt("Internal error: we do not seem to be in an encounter!"));
}



$green = '#2ecc71';
$gray = '#555555';
$color_90 = $green;
$color_180 = $green;
$color_270 = $green;
$color_360 = $green;

/* User Logged-in Info */
$user_id = ( isset($_SESSION['authUserID']) && $_SESSION['authUserID'] ) ? $_SESSION['authUserID'] : '';
$userData = ($user_id) ? getUserIDInfo($user_id) : '';
$user_firstname = ( isset($userData['fname']) && $userData['fname'] ) ? $userData['fname'] : '';
$user_lastname = ( isset($userData['lname']) && $userData['lname'] ) ? $userData['lname'] : '';
$userArrs =  array($user_firstname,$user_lastname);
$user_fullname = '';
if($userArrs && array_filter($userArrs)) {
  $user_fullname = implode(" ",array_filter($userArrs));
}

$formid = 0 + (isset($_GET['id']) ? $_GET['id'] : 0);
$check_res = $formid ? formFetch($tableName, $formid) : array();

$userid = $_SESSION['authUserID'];

/* checking the last record */
if( empty($check_res) ){
    $last_record_query = "SELECT * FROM {$tableName} WHERE pid=? ORDER BY date DESC LIMIT 1";
    $last_record = sqlQuery($last_record_query, array($pid));
} 

$data = '';
$obj = $data;
if( isset($obj['user']) && $obj['user'] ) {
  $user_id = $obj['user'];
  $savedUserData = ($user_id) ? getUserIDInfo($user_id) : '';
  $user_firstname = ( isset($savedUserData['fname']) && $savedUserData['fname'] ) ? $savedUserData['fname'] : '';
  $user_lastname = ( isset($savedUserData['lname']) && $savedUserData['lname'] ) ? $savedUserData['lname'] : '';
  $userArrs =  array($user_firstname,$user_lastname);
  if($userArrs && array_filter($userArrs)) {
    $user_fullname = implode(" ",array_filter($userArrs));
  }
}

$ninety_days_disabled = '';
$one_eighty_disabled = '';
$two_seventy_disabled = '';
$three_sixty_disabled = '';

if($pid){
    $patien_query = "SELECT CDA,date FROM patient_data WHERE pid = ?";
    $patient_data = sqlQuery($patien_query, array($pid));
    $cda_date = ($patient_data['CDA']) ? trim($patient_data['CDA']) : date('Y-m-d', strtotime($patient_data['date']));
    $today = date('Y-m-d');
    $ninety_days = date('Y-m-d', strtotime($cda_date . '+ 90 days'));
    $one_eighty = date('Y-m-d', strtotime($cda_date . '+ 180 days'));
    $two_seventy = date('Y-m-d', strtotime($cda_date . '+ 270 days'));
    $three_sixty = date('Y-m-d', strtotime($cda_date . '+ 360 days'));
    $after_one_year = date('Y-m-d', strtotime($cda_date . '+ 1 year'));

    $color_90 =  (strtotime($ninety_days) > strtotime($today) ) ? getDateColor($today, $ninety_days) : $gray;
    $color_180 = (strtotime($one_eighty) > strtotime($today) ) ? getDateColor($today, $one_eighty) : $gray;
    $color_270 = (strtotime($two_seventy) > strtotime($today) ) ? getDateColor($today, $two_seventy) : $gray;
    $color_360 = (strtotime($three_sixty) > strtotime($today) ) ? getDateColor($today, $three_sixty) : $gray;
    $color_cda = (strtotime($after_one_year) > strtotime($today) ) ? getCDADateColor($today, $after_one_year) : $gray;
    
    $ninety_days_disabled = (strtotime($ninety_days) < strtotime($today)) ? ' disabled ' : '';
    $one_eighty_disabled = (strtotime($one_eighty) < strtotime($today)) ? ' disabled' : '';
    $two_seventy_disabled = (strtotime($two_seventy) < strtotime($today)) ? ' disabled' : '';
    $three_sixty_disabled = (strtotime($three_sixty) < strtotime($today)) ? ' disabled' : '';
}

$is_group = ($attendant_type == 'gid') ? true : false;

$formStmt = "SELECT id FROM forms WHERE form_id=? AND formdir=?";
$form = sqlQuery($formStmt, array($formid, $folderName));

$esignApi = new Api();
// Create the ESign instance for this form
//$esign = $esignApi->createFormESign($iter['id'], $formdir, $encounter);

$esign = $esignApi->createFormESign($form['id'], $folderName, $encounter);

//fetch acl for category of given encounter
$pc_catid = fetchCategoryIdByEncounter($encounter);
$postCalendarCategoryACO = fetchPostCalendarCategoryACO($pc_catid);
if ($postCalendarCategoryACO) {
    $postCalendarCategoryACO = explode('|', $postCalendarCategoryACO);
    $authPostCalendarCategory = acl_check($postCalendarCategoryACO[0], $postCalendarCategoryACO[1]);
    $authPostCalendarCategoryWrite = acl_check($postCalendarCategoryACO[0], $postCalendarCategoryACO[1], '', 'write');
} else { // if no aco is set for category
    $authPostCalendarCategory = true;
    $authPostCalendarCategoryWrite = true;
}


?>
<!DOCTYPE html>
<html>
<head>
    <?php Header::setupHeader(['datetime-picker', 'opener','esign', 'common']); ?>
    <title><?php echo xlt('Respite Care Progress Note'); ?></title>
    <style>
        @media only screen and (max-width: 768px) {
            [class*="col-"] {
                width: 100%;
                text-align: left !Important;
            }
        }


    </style>
    <?php
    $arrOeUiSettings = array(
        'heading_title' => xl('Respite Care Progress Note'),
        'include_patient_name' => true,// use only in appropriate pages
        'expandable' => false,
        'expandable_files' => array(""),//all file names need suffix _xpd
        'action' => "",//conceal, reveal, search, reset, link or back
        'action_title' => "",
        'action_href' => "",//only for actions - reset, link or back
        'show_help_icon' => false,
        'help_file_name' => "" /* Path: openemr/Documentation/help_files */
    );
    $oemr_ui = new OemrUI($arrOeUiSettings);
    ?>
  <link rel="stylesheet" href="<?php echo $web_root; ?>/library/css/bootstrap-timepicker.min.css">
  <link rel="stylesheet" href="../../../style_custom.css">
  <style>
        .margin-left-40{
                margin-left: 300px;
            }
            .margin-right-40{
                margin-right: 40px;
            }
            .margin-top-20{
                margin-top: 20px;
            }
            @media print{
                .margin-left-40{
                    margin-left: 40px;
                }
                .margin-right-40{
                    margin-right: 20px;
                }
                .col-md-2 {
                    width: 16.66666667%;
                }
                .col-md-10 {
                    width: 83.33333333%;
                }
                .col-md-6 {
                    width: 50%;
                }
                .col-md-4 {
                    width: 33.3333%;
                }
                .col-md-3 {
                    width: 25%;
                }
                .col-md-8 {
                    width: 66.66666667%;
                }
                .col-md-9 {
                    width: 75%;
                }
                .col-md-12 {
                    width: 100%;
                }
                .form-group {
                    margin-bottom: 5px!important;
                }
                label {
                    padding: 0 5px!important;
                }
                label {
                    display: inline-block;
                    max-width: 100%;
                    margin-bottom: 5px;
                    font-weight: normal;
                }
                .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9 {
                    float: left;
                }
                .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9 {
                    float: left;
                }
                .row_other{
                    margin-top: 40px;
                }
                h3{
                    font-size: 20px;
                }
                .brief_mental_status{
                    margin-top: 40px;
                    padding-top: 20px;
                }
                .treatment_diagnostic_row{
                    margin-top: 30px;
                }
                input[type=text]{
                    border: none;
                    font-weight: bold;
                    border-bottom: 1px solid #333;
                    border-bottom-right-radius: 0;
                    border-bottom-left-radius: 0;
                }
                .form-control{
                    border: none;
                    font-size: 16px;
                    font-weight: bold;
                    background: none;
                }
                textarea.form-control{
                    border: 1px solid #333;
                }
                .session-focus{
                    margin-top: 100px;
                    padding-top: 40px;
                }
                .col-md-offset-2 {
                    margin-left: 0;
                }
                .checkbox-inline, .radio-inline {
                    position: relative;
                    display: inline-block;
                    padding-left: 20px;
                    margin-bottom: 0;
                    font-weight: 400;
                    vertical-align: middle;
                    cursor: pointer;
                }

                .full-width{
                    width: 100%;
                    margin: 0;
                }

                .line-block{
                    display: block;
                    width: 100%;
                }
                .date-time-align{
                    display: inline-block;
                    margin-right: 10px;
                }

                .work-on{
                    margin-top: 100px;
                }

            }
            @page {
              margin: 2cm;
            }

            .plan_review_90, .plan_review_90[disabled]{
                color: white;
                background-color: <?php echo $color_90; ?> !important;
            }

            .plan_review_180, .plan_review_180[disabled]{
                color: white;
                background-color: <?php echo $color_180; ?> !important;
            }
            .plan_review_270, .plan_review_270[disabled]{
                color: white;
                background-color: <?php echo $color_270; ?> !important;
            }

            .plan_review_360, .plan_review_360[disabled]{
                color: white;
                background-color: <?php echo $color_360; ?> !important;
            }

            .cda_date, .cda_date[disabled]{
                color: white;
                background-color: <?php echo $color_cda; ?> !important;
            }

            .margin-top-30 {
                margin-top: 30px;
            }

            .margin-top-40 {
                margin-top: 40px;
            }

            .date_completed{
                display: inline-block;
                float: left;
                padding-left: 10px;
            }
        </style>
</head>
<body class="body_top">
<div id="container_div" class="<?php echo attr($oemr_ui->oeContainer()); ?>">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-header clearfix">
                <?php echo  $oemr_ui->pageHeading() . "\r\n"; ?>
            </div>
        </div>
    </div>
   
    <div class="row">
        <div class="col-sm-12">

            <?php             

            $patient_id = ( isset($_SESSION['pid']) && $_SESSION['pid'] ) ? $_SESSION['pid'] : '';
            $user_id = ( isset($_SESSION['authUserID']) && $_SESSION['authUserID'] ) ? $_SESSION['authUserID'] : '';
            $webserver_root = dirname(dirname(__FILE__));
  
            $current_datetime = date('Y-m-d H:i:s');
            $datetime = ( isset($obj['date']) && $obj['date'] ) ? $obj['date'] : $current_datetime;
            $patient_full_name = '';

            if( $_SESSION['from_dashboard'] ){
                $patient_full_name = ($check_res['name']) ? $check_res['name'] : '';
            } else {

              if($patient_id) {
                $patient = getPatientData($patient_id);
                $patient_fname = ( isset($patient['fname']) && $patient['fname'] ) ? $patient['fname'] : '';
                $patient_mname = ( isset($patient['mname']) && $patient['mname'] ) ? $patient['mname'] : '';
                $patient_lname = ( isset($patient['lname']) && $patient['lname'] ) ? $patient['lname'] : '';
                $patientInfo = array($patient_fname,$patient_mname,$patient_lname);
                if($patientInfo && array_filter($patientInfo)) {
                  $patient_full_name = implode( ' ', array_filter($patientInfo) );
                } else {
                  $patient_full_name = ($check_res['name']) ? $check_res['name'] : '';
                }
              }

            }


            
            ?>
            <form method=post <?php echo "name='my_form' " . "action='$rootdir/forms/$folderName/save.php?id=" . attr_url($formid) . "'\n"; ?>>
              <input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
              <input type="hidden" name="date" value="<?php echo $datetime; ?>">
              <input type="hidden" name="pid" value="<?php echo $patient_id; ?>">
              <input type="hidden" name="encounter" value="<?php echo $encounter; ?>">
              <input type="hidden" name="user" value="<?php echo $user_id; ?>">
              <input type="hidden" name="authorized" value="<?php echo $userauthorized; ?>">
              <input type="hidden" name="activity" value="1">
              <fieldset class="form_content">

                <div class="field-group-wrapper">

                  <div class="field-row">

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="name" class="col-md-5 "><?php echo xlt('Member Name:'); ?></label>
                        <div class="col-md-6">
                          <input type="text" readonly disabled class="form-control" value="<?php echo $patient_full_name; ?>">
                          <input type="hidden" name="name" value="<?php echo $patient_full_name; ?>" >
                        </div>                        
                      </div>
                      <div class="clearfix"></div>
                      <div class="form-group">
                       
                        <label for="provider_id" class="col-md-5 "><?php echo xlt('Respite Provider:'); ?></label>
                        <div class="col-md-6">
                            <?php 
                                $examiner = ($check_res['provider_id']) ? $check_res['provider_id'] : $last_record['provider_id'];
                                $urows = get_providers_list(); 
                             ?>
                            <select name="provider_id" id="provider_id" class="form-control">
                                <?php  
                                    while($urow = sqlFetchArray($urows)){        
                                        echo "    <option value='" . attr($urow['id']) . "'";
                                        if ($userid) {
                                            if (($urow['id'] == $userid) || ($examiner == $urow['id'])) {
                                                echo " selected";
                                            }
                                        }
                                        echo ">" . text($urow['lname']);
                                        if ($urow['fname']) {
                                            echo ", " . text($urow['fname']);
                                        }
                                        echo "</option>\n";
                                    } 
                                 ?>
                            </select> 
                        </div>
                      </div>
                      <div class="clearfix"></div>
                      <div class="form-group">
                        <?php $billing_code =  ( $check_res['billing_code'] ) ? $check_res['billing_code'] : 'Respite S5150' ; ?>
                        <label for="billing_code" class="col-md-5 "><?php echo xlt('Billing Code:'); ?></label>
                        <div class="col-md-6">
                          <input type="text" readonly disabled class="form-control" value="<?php echo text($billing_code); ?>">
                          <input type="hidden" id="billing_code" class="form-control" name="billing_code" value="<?php echo text($billing_code); ?>">
                        </div>                        
                      </div>
                      <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="units_remaining" class="col-md-5 "><?php echo xlt('Units Remaining'); ?></label>
                                    <div class="col-md-6">
                                        <input type="text" name="units_remaining" id="units_remaining"  class="form-control" value="<?php echo ($check_res['units_remaining']) ? text($check_res['units_remaining']) : ''; ?>" readonly>
                                        
                                    </div>                                    
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="days_remaining" class="col-md-5 "><?php echo xlt('Days Remaining'); ?></label>
                                    <div class="col-md-6">
                                        <input type="text" name="days_remaining" id="days_remaining"  class="form-control" value="<?php echo ($check_res['days_remaining']) ? text($check_res['days_remaining']) : ''; ?>" readonly>
                                        
                                    </div>                                    
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="treatment_plan_end_date" class="col-md-5 "><?php echo xlt('Treatment Plan End Date'); ?></label>
                                    <div class="col-md-6">
                                        <input type="text" name="treatment_plan_end_date" id="treatment_plan_end_date"  class="form-control" value="<?php echo text(date('m/d/Y', strtotime($three_sixty))); ?>" readonly>
                                        
                                    </div>                                    
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="cda_expires" class="col-md-5 "><?php echo xlt('CDA Expires'); ?></label>
                                    <div class="col-md-6">
                                        <input type="text" name="cda_expires" id="cda_expires"  class="form-control cda_date" value="<?php echo text(date('m/d/Y', strtotime($after_one_year))); ?>" readonly>
                                        
                                    </div>                                    
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="cafas_expires" class="col-md-5 "><?php echo xlt('CAFAS/PECFAS Expires'); ?></label>
                                    <div class="col-md-6">
                                        <input type="text" name="cafas_expires" id="cafas_expires"  class="form-control" value="<?php echo ($check_res['cafas_expires']) ? text($check_res['cafas_expires']) : ''; ?>" readonly>
                                        
                                    </div>                                    
                                </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                       
                        <?php 
                        if($check_res['dateofservice']) {
                          $dateofservice = text(date('m/d/Y', strtotime($check_res['dateofservice']) ));
                        } elseif($last_record['dateofservice']){
                          $dateofservice = text(date('m/d/Y', strtotime($last_record['dateofservice']) ));
                        } else {
                          $dateofservice = text(date('m/d/Y'));
                        }
                       
                        ?>
                        <label for="dateofservice" class="col-md-5 "><?php echo xlt('Date of Service:'); ?></label>
                        <div class="col-md-6">
                          <input type="text" id="dateofservice" class="form-control datepicker" name="dateofservice" value="<?php echo text($dateofservice); ?>" autocomplete="off">
                        </div>                        
                      </div>

                      <div class="clearfix"></div>
                      <div class="form-group">
                        <?php 
                          $starttime =  ( $check_res['starttime'] ) ? text($check_res['starttime']) : text($last_record['starttime']) ; 
                          
                        ?>
                        <label for="starttime" class="col-md-5 "><?php echo xlt('Start Time:'); ?></label>
                        <div class="col-md-6">
                          <input type="text" id="starttime" class="form-control timepicker" name="starttime" value="<?php echo $starttime; ?>" autocomplete="off">
                        </div>                        
                      </div>
                        
                      <div class="clearfix"></div>                        
                      <div class="form-group">
                        <?php 
                          $endtime =  ( $check_res['endtime'] ) ? text($check_res['endtime']) : text($last_record['endtime']) ;                           
                        ?>
                        <label for="endtime" class="col-md-5 "><?php echo xlt('End Time:'); ?></label>
                        <div class="col-md-6">
                          <input type="text" id="endtime" class="form-control timepicker" name="endtime" value="<?php echo $endtime; ?>" autocomplete="off">
                        </div>                        
                      </div>
                        
                      <div class="clearfix"></div>
                      <div class="form-group">
                        <?php 
                          $duration =  ( $check_res['duration'] ) ? text($check_res['duration']) : text($last_record['duration']) ; 
                        ?>
                        <label for="endtime" class="col-md-5 "><?php echo xlt('Duration:'); ?></label>
                        <div class="col-md-6">
                          <input type="text" id="duration" class="form-control" name="duration" value="<?php echo $duration; ?>" autocomplete="off">
                        </div>                        
                      </div>

                      <div class="clearfix"></div>
                      <div class="form-group">
                          <label for="billable_hours" class="col-md-5 "><?php echo xlt('Billable Hours'); ?></label>
                          <div class="col-md-6">
                              <input type="text" id="billable_hours" class="form-control" name="billable_hours" value="<?php echo ($check_res['billable_hours']) ? text($check_res['billable_hours']) : text($last_record['billable_hours']); ?>">
                              <small class="text-danger duration_error"></small>
                          </div>                                    
                      </div>
                      <div class="clearfix"></div>
                      <div class="form-group">
                          <label for="billable_units" class="col-md-5 "><?php echo xlt('Billable Units'); ?></label>
                          <div class="col-md-6">
                              <input type="text" id="billable_units" class="form-control" name="billable_units" value="<?php echo ($check_res['billable_units']) ? text($check_res['billable_units']) : text($last_record['billable_units']); ?>">
                              <small class="text-danger duration_error"></small>
                          </div>                                    
                      </div>
                      <div class="clearfix"></div>
                      <div class="form-group">
                          <label for="avg_unit_week" class="col-md-5 "><?php echo xlt('Avg Unit / Week'); ?></label>
                          <div class="col-md-6">
                              <input type="text" id="avg_unit_week" class="form-control" name="avg_unit_week" value="<?php echo ($check_res['avg_unit_week']) ? text($check_res['avg_unit_week']) : text($last_record['avg_unit_week']); ?>">
                              <small class="text-danger duration_error"></small>
                          </div>                                    
                      </div>

                    </div>


                  </div>

                  <div class="clearfix"></div>

                  <div class="field-row margin-top-30">
                    <div class="col-lg-12">
                      <h4 class="field-heading">Descriptions of Intervention</h4>
                  
                      <div class="form-group form-checkbox-field margin-top-30">
                        <?php
                          $service_locations = array("Home","Community");
                          $selected_service_loc = ( $check_res['service_local'] ) ? $check_res['service_local'] : $last_record['service_local'];
                        ?>       
                          <div class="col-md-5">
                            <strong>State where the services took place:</strong>
                          </div>                 
                          
                          <div class="col-md-7" style="display: block;">
                            <?php foreach ($service_locations as $loc) { 
                              $is_selected_service_loc = ($loc == $selected_service_loc) ? ' checked': ($last_record['service_local'] == $loc) ? ' checked ' : '';
                              ?>
                              <label class="radio-inline margin-right-20" style="width: 20%; display: inline-block;" >
                                <input type="radio" name="service_local" value="<?php echo $loc; ?>"<?php echo $is_selected_service_loc; ?>> <?php echo $loc; ?>
                              </label> 
                            <?php } ?>
                          </div>
                        
                      </div>

                      <div class="clearfix"></div>
                      <div class="col-md-12 margin-top-40">
                                <h4><?php echo xlt('Tx Plan Review:'); ?></h4>
                                <div class="col-md-6">
                                    <div class="form-group">
                                            <label for="plan_review_90" class="col-sm-3 control-label"><?php echo xlt('90 Day:'); ?> </label>
                                            <div class="col-sm-9">
                                              <input type="text" class="form-control plan_review_90 pull-left" name="plan_review_90" id="plan_review_90" value="<?php echo ($ninety_days) ? date('m/d/Y', strtotime($ninety_days)) : ''; ?>" <?php echo $ninety_days_disabled; ?> style="width:120px; margin-right: 10px" readonly>
                                              <div class="date_completed">
                                                  <span class="pull-left" style="margin-right: 10px">Completed:</span>
                                                  <input type="text" name="completed_date_tx90" class="form-control datepicker" value="<?php echo ( $check_res['completed_date_tx90'] ) ? date('m/d/Y', strtotime($check_res['completed_date_tx90'])): '' ; ?>" style="width: 124px;" autocomplete="off">
                                              </div>
                                            </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                            <label for="plan_review_180" class="col-sm-3 control-label"><?php echo xlt('180 Day: '); ?></label>
                                            <div class="col-sm-9">
                                              <input type="text" class="form-control plan_review_180 pull-left" name="plan_review_180" id="plan_review_180" value="<?php echo ($one_eighty) ? date('m/d/Y', strtotime($one_eighty)) : ''; ?>"  <?php echo $one_eighty_disabled; ?> style="width:120px; margin-right: 10px" readonly>
                                              <div class="date_completed">
                                                  <span class="pull-left" style="margin-right: 10px">Completed:</span>
                                                  <input type="text" name="completed_date_tx180" class="form-control datepicker" value="<?php echo ( $check_res['completed_date_tx180'] ) ? date('m/d/Y', strtotime($check_res['completed_date_tx180'])): '' ; ?>" style="width: 124px;" autocomplete="off">
                                              </div>
                                            </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                            <label for="plan_review_270" class="col-sm-3 control-label"><?php echo xlt('270 Day:'); ?></label>
                                            <div class="col-sm-9">
                                              <input type="text" class="form-control plan_review_270 pull-left" name="plan_review_270" id="plan_review_270" value="<?php echo ($two_seventy) ? date('m/d/Y', strtotime($two_seventy)) : ''; ?>"  <?php echo $two_seventy_disabled; ?> style="width:120px; margin-right: 10px" readonly>
                                              <div class="date_completed">
                                                  <span class="pull-left" style="margin-right: 10px">Completed:</span>
                                                  <input type="text" name="completed_date_tx270" class="form-control datepicker" value="<?php echo ( $check_res['completed_date_tx270'] ) ? date('m/d/Y', strtotime($check_res['completed_date_tx270'])): '' ; ?>" style="width: 124px;" autocomplete="off">
                                              </div>
                                            </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                            <label for="plan_review_360" class="col-sm-3 control-label"><?php echo xlt('360 Day:'); ?></label>
                                            <div class="col-sm-9">
                                              <input type="text" class="form-control plan_review_360 pull-left" name="plan_review_360" id="plan_review_360" value="<?php echo ($three_sixty) ? date('m/d/Y', strtotime($three_sixty)) : ''; ?>"  <?php echo $three_sixty_disabled; ?> style="width:120px; margin-right: 10px" readonly>
                                              <div class="date_completed">
                                                  <span class="pull-left" style="margin-right: 10px">Completed:</span>
                                                  <input type="text" name="completed_date_tx360" class="form-control datepicker" value="<?php echo ( $check_res['completed_date_tx360'] ) ? date('m/d/Y', strtotime($check_res['completed_date_tx360'])): '' ; ?>" style="width: 124px;" autocomplete="off">
                                              </div>
                                            </div>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                      <div class="form-group" style="margin-top: 20px">
                        <p>
                          <strong>Objective(s) Addressed and Targeted Skill Area:</strong> Provide relief and de-escalation of stressful situations for the caregiver(s) as evidenced by child spending at least 5 hours per month with Respite Provider for the next 3 months.
                        </p>
                      </div>

                      <div class="clearfix"></div>
                      <div class="form-group margin-top-30">
                        <?php 
                          $intervention_type =  ( $check_res['intervention_type'] ) ? text($check_res['intervention_type']) : text($last_record['intervention_type']) ; 
                        ?>
                        <label>Type of Intervention:</label>
                        <textarea name="intervention_type" class="form-control" rows="4"><?php echo $intervention_type ?></textarea>
                      </div>

                      <div class="form-group">
                        
                        <label>Progress or Lack of Progress towards Treatment Goals:</label>
                        <span>Member Participated in Respite Program</span>
                        
                      </div>

                      <div class="clearfix"></div>
                      <div class="form-group margin-top-30">
                        <?php 
                          $progress_narrative =  ( $check_res['progress_narrative'] ) ? text($check_res['progress_narrative']) : text($last_record['progress_narrative']) ; 
                        ?>
                        <label>Narrative:</label>
                        <textarea name="progress_narrative" class="form-control" rows="4"><?php echo $progress_narrative ?></textarea>
                      </div>

                      <div class="clearfix">&nbsp;</div>
                      <div class="form-group margin-top-30" style="margin-top:20px;">
                        <?php
                          $critical_incidents_options = array(0=>"No",1=>"Yes");
                          //$crit_incidents = $check_res['crit_incidents'];
                        ?>
                          <span style="margin-right: 30px">
                            <strong>Critical Incidents/Interventions/Complaint/Grievance:</strong>
                          </span>
                          
                          <span class="span-field-group">
                            <?php foreach ($critical_incidents_options as $val => $label) { 
                              //$is_selected_crit_incidents = ($val === $crit_incidents) ? ' checked':'';
                              ?>
                              <label class="radio-inline" style="width: 50px">                              
                                <input type="radio" name="crit_incidents" value="<?php echo $val; ?>" <?php echo ($val == $check_res['crit_incidents']) ? ' checked' : ($val == $last_record['crit_incidents']) ? ' checked ' : ''; ?> >
                                <?php echo $label; ?>                              
                              </label> 
                            <?php } ?>
                          </span>
                        
                      </div>

                      <div class="form-group">
                        <?php 
                          $critical_incidents_explan =  ( $check_res['critical_incidents_explan'] ) ? text($check_res['critical_incidents_explan']) : text($last_record['critical_incidents_explan']);
                        ?>
                        <label>If yes please explain:</label>
                        <textarea name="critical_incidents_explan" class="form-control" rows="4"><?php echo $critical_incidents_explan ?></textarea>
                      </div>

                    </div>

                  </div> 

                </div>    
                  
              </fieldset>
            
              <div class="form-group clearfix">
                  <div class="col-sm-offset-1 col-sm-12 position-override">
                      <div class="btn-group btn-pinch" role="group">
                          <?php                                    
                                    if (($esign->isButtonViewable() and $is_group == 0 and $authPostCalendarCategoryWrite) or ($esign->isButtonViewable() and $is_group and acl_check("groups", "glog", false, 'write') and $authPostCalendarCategoryWrite)) {
                                        if (!$aco_spec || acl_check($aco_spec[0], $aco_spec[1], '', 'write')) {
                                            echo $esign->buttonHtml();
                                        }
                                    }
                          ?>
                          <!-- Save/Cancel buttons -->
                          <button type="submit" class="btn btn-default btn-save save"> <?php echo xla('Save'); ?></button>
                          <button type="button"
                                  class="dontsave btn btn-link btn-cancel btn-separate-left" onclick="form_close_tab()"><?php echo xla('Cancel'); ?></button>
                          <a href="#" class="btn btn-default" id="print" style="margin-left: 18px">Print</a>
                      </div>
                  </div>
              </div>
            </form>
            <br>
            <br>
        </div>
    </div>
</div><!--End of container div-->
<?php $oemr_ui->oeBelowContainerDiv();?>
<script src="<?php echo $web_root; ?>/library/js/bootstrap-timepicker.min.js"></script>
<script src="<?php echo $web_root; ?>/library/js/printThis.js"></script>
<script language="javascript">
    // jQuery stuff to make the page a little easier to use
    $(function () {
        $(".dontsave").click(function () {
            parent.closeTab(window.name, false);
        });

        $('.datepicker').datetimepicker({
          <?php $datetimepicker_timepicker = false; ?>
          <?php $datetimepicker_showseconds = false; ?>
          <?php $datetimepicker_formatInput = false; ?>
          <?php require($GLOBALS['srcdir'] . '/js/xl/jquery-datetimepicker-2-5-4.js.php'); ?>
          <?php // can add any additional javascript settings to datetimepicker here; need to prepend first setting with a comma ?>
        });

        $('.timepicker').timepicker({
          defaultTime: '12:00 PM',
        });

        var today = new Date();

        $("input#endtime, input#starttime").on("keypress change blur focusout",function(){
         calculate_duration();
        });


        // esign API
        var formConfig = <?php echo $esignApi->formConfigToJson(); ?>;
        $(".esign-button-form").esign(
            formConfig,
            {
                afterFormSuccess : function( response ) {
                    if ( response.locked ) {
                        var editButtonId = "form-edit-button-"+response.formDir+"-"+response.formId;
                        $("#"+editButtonId).replaceWith( response.editButtonHtml );
                    }

                    var logId = "esign-signature-log-"+response.formDir+"-"+response.formId;
                    $.post( formConfig.logViewAction, response, function( html ) {
                        $("#"+logId).replaceWith( html );
                    });
                }
            }
        );

        var encounterConfig = <?php echo $esignApi->encounterConfigToJson(); ?>;
        $(".esign-button-encounter").esign(
            encounterConfig,
            {
                afterFormSuccess : function( response ) {
                    // If the response indicates a locked encounter, replace all
                    // form edit buttons with a "disabled" button, and "disable" left
                    // nav visit form links
                    if ( response.locked ) {
                        // Lock the form edit buttons
                        $(".form-edit-button").replaceWith( response.editButtonHtml );
                        // Disable the new-form capabilities in left nav
                        top.window.parent.left_nav.syncRadios();
                        // Disable the new-form capabilities in top nav of the encounter
                        $(".encounter-form-category-li").remove();
                    }

                    var logId = "esign-signature-log-encounter-"+response.encounterId;
                    $.post( encounterConfig.logViewAction, response, function( html ) {
                        $("#"+logId).replaceWith( html );
                    });
                }
            }
        );

        $('.esign-button-form').css({"width": "110px", "height":"25px", "line-height":"20px", "vertical-align":"middle", "margin-right":"25px"});

        $('.esign-button-form span').html('Digitally Sign');

        $("#print").on('click', function(){
                    $('.form_content').printThis({
                        debug: false,               // show the iframe for debugging
                        importCSS: true,            // import parent page css
                        importStyle: true,         // import style tags
                        printContainer: false,       // print outer container/$.selector
                        loadCSS: "",                // path to additional css file - use an array [] for multiple
                        pageTitle: "Respite Care Progress Note",              // add title to print page
                        removeInline: false,        // remove inline styles from print elements
                        removeInlineSelector: "*",  // custom selectors to filter inline styles. removeInline must be true
                        printDelay: 333,            // variable print delay
                        header: "<h2>Respite Care Progress Note</h2>",               // prefix to html
                        footer: null,               // postfix to html
                        base: false,                // preserve the BASE tag or accept a string for the URL
                        formValues: true,           // preserve input/form values
                        canvas: false,              // copy canvas content
                        doctypeString: '<!DOCTYPE html>',       // enter a different doctype for older markup
                        removeScripts: false,       // remove script tags from print content
                        copyTagClasses: false,      // copy classes from the html & body tag
                        beforePrintEvent: null,     // function for printEvent in iframe
                        beforePrint: null,          // function called before iframe is filled
                        afterPrint: null            // function called before iframe is removed
                    });
                });

        calculate_duration();

    });

    function calculate_duration()
            {
                var today = new Date();
                var s = $("input#starttime").val();
                  var e = $("input#endtime").val();
                  var startTime = s.replace(/\s+/g, '').trim();
                  var endTime = e.replace(/\s+/g, '').trim();
                  if(startTime && endTime) {
                    
                    var date_today = today.getFullYear() + "-" + ('0' + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + (today.getDate())).slice(-2);
                    var date1 = new Date( date_today + " " + s ).getTime();
                    var date2 = new Date( date_today + " " + e ).getTime();
                    var msec = date2 - date1;
                    var total_in_minutes = Math.floor(msec / 60000);
                    var mins = Math.floor(msec / 60000);
                    var hrs = Math.floor(mins / 60);
                    var days = Math.floor(hrs / 24);
                    var yrs = Math.floor(days / 365);
                    var hours_text = '';
                    var hour_and_mins = '';

                    mins = mins % 60;
                    if(mins>1) {
                      hour_and_mins = hrs + "." + mins + ' hours';
                    } else {
                      if(hrs>1) {
                        hour_and_mins = hrs + ' hours';
                      } else {
                        hour_and_mins = hrs + ' hour';
                      }
                    }

                    /* 
                      1 hour = 4 units 
                      60 mins = 4 units
                      4 / 60 = 0.066 unit
                      1 min = 0.066 unit
                    */

                    var per_unit = 4/60;
                    var total_units = total_in_minutes * per_unit;
                    var unit_text = (total_units>0) ? total_units + ' units': total_units + ' unit';
                    var duration_text = hour_and_mins + " / " + unit_text;
                    $("input#duration").val(duration_text);

                    $('#billable_hours').val(hour_and_mins);
                    $('#billable_units').val(unit_text);
                    
                  }
            }


            function form_close_tab()
            {
                var session_dashboard = "<?php echo isset($_SESSION['from_dashboard']) ? $_SESSION['from_dashboard'] : ''; ?>";
                console.log('Session Dashboard: ' + session_dashboard);
                if(session_dashboard) {
                    //window.top.location.reload();
                    window.top.location.href = window.top.location;
                } else {
                   top.restoreSession(); 
                    parent.closeTab(window.name, false);
                }                
            }
</script>
<script>
    $(function () {
        $('select').addClass("form-control");
    });
</script>
</body>
</html>
