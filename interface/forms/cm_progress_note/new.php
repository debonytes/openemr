<?php
/**
 * Case Management form.
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Jacob T Paul <jacob@zhservices.com>
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2015 Z&H Consultancy Services Private Limited <sam@zhservices.com>
 * @copyright Copyright (c) 2017-2019 Brady Miller <brady.g.miller@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */


require_once("../../globals.php");
require_once("$srcdir/encounter.inc");
require_once("$srcdir/group.inc");
require_once("$srcdir/api.inc");
require_once("$srcdir/acl.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
require_once $GLOBALS['srcdir'].'/ESign/Api.php';

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Core\Header;
use ESign\Api;

$folderName = 'cm_progress_note';
$tableName = 'form_' . $folderName;


$returnurl = 'encounter_top.php';
$formid = 0 + (isset($_GET['id']) ? $_GET['id'] : 0);
$check_res = $formid ? formFetch($tableName, $formid) : array();


/* checking the last record */
if( empty($check_res) ){
    $last_record_query = "SELECT * FROM {$tableName} WHERE pid=? ORDER BY date DESC LIMIT 1";
    $last_record = sqlQuery($last_record_query, array($pid));
} 

$mileage = ( $check_res['mileage'] ) ? text($check_res['mileage']) : text($last_record['mileage']);
$miles = ( $check_res['miles'] ) ? text($check_res['miles']) : text($last_record['miles']);


$ninety_days_disabled = '';
$one_eighty_disabled = '';
$two_seventy_disabled = '';
$three_sixty_disabled = '';

if($pid){
    $patien_query = "SELECT CDA, date FROM patient_data WHERE pid = ?";
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
        <title><?php echo xlt("Case Management Progress Note"); ?></title>

        <?php Header::setupHeader(['datetime-picker', 'opener', 'esign']); ?>
        <link rel="stylesheet" href="<?php echo $web_root; ?>/library/css/bootstrap-timepicker.min.css">
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
        <div class="container">
            <div class="row">
                <div class="page-header">
                    <h2><?php echo xlt('Case Management Progress Note'); ?></h2>
                </div>
            </div>
            <?php
            $current_date = date('Y-m-d');

            if( $_SESSION['from_dashboard'] ){
                $patient_full_name = ($check_res['name']) ? $check_res['name'] : '';
            } else {

                $patient_id = ( $_SESSION['alert_notify_pid'] ) ? $_SESSION['alert_notify_pid'] : '';
                $pid = ( $_SESSION['pid'] ) ? $_SESSION['pid'] : 0;
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



            $duration = [
                0 => 'Select',
                1 => '0.25 hr / 1 unit',
                2 => '0.50 hr / 2 units',
                3 => '0.75 hr / 3 units',
                4 => '1 hr / 4 units',
                5 => '1.25 hrs / 5 units',
                6 => '1.50 hrs / 6 units',
                7 => '1.75 hrs / 7 units',
                8 => '2 hrs / 8 units',
                9 => '2.25 hrs / 9 units',
                10 => '2.50 hrs / 10 units',
                11 => '2.75 hrs / 11 units',
                12 => '3 hrs / 12 units',
                13 => '3.25 hrs / 13 units',
                14 => '3.50 hrs / 14 units',
                15 => '3.75 hrs / 15 units',
                16 => '4 hrs / 16 units',
            ];

            ?>
            <div class="row">
                
                <form method="post" id="my_progress_notes_form" name="my_progress_notes_form" action="<?php echo $rootdir; ?>/forms/<?php echo $folderName; ?>/save.php?id=<?php echo attr_url($formid); ?>">
            

                
                    <input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
                    <input type="hidden" name="pid" value="<?php echo $pid; ?>">
                    <input type="hidden" name="encounter" value="<?php echo $encounter; ?>">
                    <input type="hidden" name="user" value="<?php echo $_SESSION['authUser']; ?>">
                    <input type="hidden" name="authorized" value="<?php echo $userauthorized; ?>">
                    <input type="hidden" name="activity" value="1">

                    <fieldset style="padding-top: 20px!important;" class="form_content">
                       
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="col-md-5 "><?php echo xlt('Client Name'); ?></label>
                                    <div class="col-md-6">
                                        <input type="text"  id="name" class="form-control" value="<?php echo text($patient_full_name); ?>" readonly>
                                        <input type="hidden" name="name" value="<?php echo text($patient_full_name); ?>" >
                                    </div>                                    
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="examiner" class="col-md-5 "><?php echo xlt('Case Manager'); ?></label>
                                    <div class="col-md-6">
                                        <?php
                                            $examiner = ($check_res['examiner']) ? $check_res['examiner'] : $last_record['examiner'];
                                        ?>
                                        <select name="examiner" id="examiner" class="form-control">
                                                <?php echo get_examiner_name_dregree($examiner); ?>
                                        </select> 
                                        <small class="text-danger cbrs_error"></small>
                                    </div>                                                                        
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="" class="col-md-5 "><?php echo xlt('Billing Code'); ?></label>
                                    <div class="col-md-6">
                                        <input type="text"  class="form-control" value="T1017 HN-CM" readonly>
                                        <input type="hidden" name="billing_code" value="T1017 HN-CM">
                                    </div>                  
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="mileage" class="col-md-5 "><?php echo xlt('Mileage'); ?></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="mileage" value="<?php echo text($mileage); ?>">
                                    </div>              
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="service_miles" class="col-md-5 "><?php echo xlt('Miles'); ?></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="service_miles" value="<?php echo ($check_res['service_miles']) ? text($check_res['service_miles']) : text($last_record['service_miles']); ?>">
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


                            <!-- Right Side -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="col-md-5"><?php echo xlt('Date of Service'); ?></label>
                                    <div class="col-md-6">
                                        <input type="text" name="dateofservice" id="dateofservice" class="form-control datepicker" value="<?php echo ($check_res['dateofservice']) ? text(date('m/d/Y', strtotime($check_res['dateofservice']))) :  text(date('m/d/Y')); ?>" autocomplete="off">
                                        <small class="text-danger date_error"></small>
                                    </div>                     
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="auth_end_date" class="col-md-5 "><?php echo xlt('Authorization End Date'); ?></label>
                                    <div class="col-md-6">
                                        <input type="text" name="auth_end_date" id="auth_end_date"  class="form-control" value="<?php echo ($check_res['auth_end_date']) ? text($check_res['auth_end_date']) : ''; ?>" >
                                    </div>                                    
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="starttime" class="col-md-5 "><?php echo xlt('Start Time'); ?></label>
                                    <div class="col-md-6">
                                        <input type="text" name="starttime" id="starttime" class="form-control timepicker" value="<?php echo ($check_res['starttime']) ? text($check_res['starttime']) : text($last_record['starttime']); ?>" autocomplete="off">
                                        <small class="text-danger starttime_error"></small>
                                    </div>                      
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="endtime" class="col-md-5 "><?php echo xlt('End Time'); ?></label>
                                    <div class="col-md-6">
                                        <input type="text" name="endtime" id="endtime" class="form-control timepicker" value="<?php echo ($check_res['endtime']) ? text($check_res['endtime']) : text($last_record['endtime']); ?>" autocomplete="off">
                                        <small class="text-danger endtime_error"></small>
                                    </div>  
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="duration" class="col-md-5 "><?php echo xlt('Duration'); ?></label>
                                    <div class="col-md-6">
                                        <input type="text" id="duration" class="form-control" name="duration" value="<?php echo ($check_res['duration']) ? text($check_res['duration']) : text($last_record['duration']); ?>">
                                        <small class="text-danger duration_error"></small>
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

                            <div class="clearfix" style="width:100%;float:left;margin:20px 0 0"></div>

                            <div class="col-md-12 margin-top-40" >
                                <div class="form-group">
                                    <label for="" class="col-md-5 ">
                                        <?php echo xlt('(N) State where the services took place:'); ?>
                                    </label>
                                    <div class="col-md-7">
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="services_place" id="services_place1" value="home" <?php echo ($check_res['services_place'] == 'home') ? " checked ": ($last_record['services_place'] == 'home') ? " checked " : "";  ?> > <?php echo xlt('Home'); ?>
                                        </label>
                                        <label class="radio-inline">
                                          <input type="radio" name="services_place" id="services_place2" value="community"  <?php echo ($check_res['services_place'] == 'community') ? "checked": ($last_record['services_place'] == 'community') ? " checked " : "";  ?> > <?php echo xlt('Community'); ?>
                                        </label>
                                        <small class="text-danger services_place_error clearfix"></small>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="" class="col-md-5 ">
                                        <?php echo xlt('And who you were with:'); ?>
                                    </label>
                                    <div class="col-md-7">
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="services_with" id="services_with1" value="client"  <?php echo ($check_res['services_with'] == 'client') ? "checked": ($last_record['services_with'] == 'client') ? " checked " : "" ;  ?> > <?php echo xlt('Client'); ?>
                                        </label>
                                        <label class="radio-inline">
                                          <input type="radio" name="services_with" id="services_with2" value="family" <?php echo ($check_res['services_with'] == 'family') ? "checked": ($last_record['services_with'] == 'family') ? " checked " : "" ;  ?> > <?php echo xlt('Family'); ?>
                                        </label>
                                        <small class="text-danger services_with_error clearfix"></small>
                                    </div>
                                </div>
                            </div>

                            <div class="clearfix"></div>

                            <div class="col-md-12" style="margin-top:20px">
                                <p>
                                    <?php echo xlt('(A) State the goals and tasks set at the beginning of services for the client. You will not always work on all of them at each date of service.'); ?>
                                </p>

                                <div class="form-group margin-top-20">
                                    <label for="" class="col-md-3 control-label"><strong><?php echo xlt('Objective 1.1 (T1016):'); ?></strong></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control " name="goals_object_1" id="goals_object_1" style="width: 250px; float: left; margin-right: 20px" value="<?php echo ($check_res['goals_object_1']) ? text($check_res['goals_object_1']) : text($last_record['goals_object_1']) ; ?>">
                                        <small class="text-danger goals_object_1_error" style="height: 24px; line-height: 24px;"></small>
                                    </div>
                                </div>

                                <div class="clearfix"></div>

                                <div class="col-md-12 margin-left-40" >
                                    <div class="form-group padding-left-18 full-width">
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="goals_object_1_status" id="goals_object_1a" value="completed" <?php echo ($check_res['goals_object_1_status'] == 'completed') ? "checked": ($last_record['goals_object_1_status'] == 'completed') ? " checked " : "" ;  ?> > <?php echo xlt('Completed/Maintenance'); ?>
                                        </label>
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="goals_object_1_status" id="goals_object_1b" value="substantial" <?php echo ($check_res['goals_object_1_status'] == 'substantial') ? "checked": ($last_record['goals_object_1_status'] == 'substantial') ? " checked ":"";  ?> > <?php echo xlt('Substantial'); ?>
                                        </label>
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="goals_object_1_status" id="goals_object_1c" value="moderate" <?php echo ($check_res['goals_object_1_status'] == 'moderate') ? "checked": ($last_record['goals_object_1_status'] == 'moderate') ? " checked ":"";  ?> > <?php echo xlt('Moderate'); ?>
                                        </label>
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="goals_object_1_status" id="goals_object_1d" value="minimal" <?php echo ($check_res['goals_object_1_status'] == 'minimal') ? "checked": ($last_record['goals_object_1_status'] == 'minimal') ? " checked ":"";  ?> > <?php echo xlt('Minimal'); ?>
                                        </label>
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="goals_object_1_status" id="goals_object_1e" value="regression" <?php echo ($check_res['goals_object_1_status'] == 'regression') ? "checked": ($last_record['goals_object_1_status'] == 'regression') ? " checked ":"";  ?> > <?php echo xlt('Regression'); ?>
                                        </label>
                                        <small class="text-danger clearfix goals_object_1_status_error"></small>
                                    </div>  
                                </div>

                                <div class="clearfix "></div>

                                <div class="form-group margin-top-20">
                                    <label for="" class="col-md-3 control-label"><strong><?php echo xlt('Objective 2.1 (T1016):'); ?></strong></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="goals_object_2" id="goals_object_2" style="width: 250px; float: left; margin-right: 20px" value="<?php echo ($check_res['goals_object_2']) ? text($check_res['goals_object_2']) : text($last_record['goals_object_2']) ; ?>">
                                        <small class="text-danger goals_object_2_error" style="height: 24px; line-height: 24px;"></small>
                                    </div>
                                </div>

                                <div class="clearfix"></div>

                                <div class="col-md-12 margin-left-40" >
                                    <div class="form-group padding-left-18">
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="goals_object_2_status" id="goals_object_2a" value="completed" <?php echo ($check_res['goals_object_2_status'] == 'completed') ? "checked": ($last_record['goals_object_2_status'] == 'completed') ? " checked ":"";  ?> > <?php echo xlt('Completed/Maintenance'); ?>
                                        </label>
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="goals_object_2_status" id="goals_object_2b" value="substantial" <?php echo ($check_res['goals_object_2_status'] == 'substantial') ? "checked": ($last_record['goals_object_2_status'] == 'substantial') ? " checked ":"";  ?> > <?php echo xlt('Substantial'); ?>
                                        </label>
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="goals_object_2_status" id="goals_object_2c" value="moderate" <?php echo ($check_res['goals_object_2_status'] == 'moderate') ? "checked": ($last_record['goals_object_2_status'] == 'moderate') ? " checked ":"";  ?> > <?php echo xlt('Moderate'); ?>
                                        </label>
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="goals_object_2_status" id="goals_object_2d" value="minimal" <?php echo ($check_res['goals_object_2_status'] == 'minimal') ? "checked": ($last_record['goals_object_2_status'] == 'minimal') ? " checked ":"";  ?> > <?php echo xlt('Minimal'); ?>
                                        </label>
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="goals_object_2_status" id="goals_object_2e" value="regression" <?php echo ($check_res['goals_object_2_status'] == 'regression') ? "checked": ($last_record['goals_object_2_status'] == 'regression') ? " checked ":"";  ?> > <?php echo xlt('Regression'); ?>
                                        </label>
                                        <small class="text-danger clearfix goals_object_2_status_error"></small>
                                    </div>  
                                </div>

                                <div class="clearfix "></div>

                                <div class="form-group margin-top-20">
                                    <label for="" class="col-md-3 control-label"><strong><?php echo xlt('Objective 3.1 (T1016):'); ?></strong></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="goals_object_3" id="goals_object_3" style="width: 250px; float: left; margin-right: 20px" value="<?php echo ($check_res['goals_object_3']) ? text($check_res['goals_object_3']) : text($last_record['goals_object_3']); ?>">
                                        <small class="text-danger goals_object_3_error" style="height: 24px; line-height: 24px;"></small>
                                    </div>
                                </div>

                                <div class="clearfix"></div>

                                <div class="col-md-12 margin-left-40" >
                                    <div class="form-group padding-left-18">
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="goals_object_3_status" id="goals_object_3a" value="completed" <?php echo ($check_res['goals_object_3_status'] == 'completed') ? "checked": ($last_record['goals_object_3_status'] == 'completed') ? " checked ":"";  ?>  > <?php echo xlt('Completed/Maintenance'); ?>
                                        </label>
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="goals_object_3_status" id="goals_object_3b" value="substantial" <?php echo ($check_res['goals_object_3_status'] == 'substantial') ? "checked": ($last_record['goals_object_3_status'] == 'substantial') ? " checked ":"";  ?> > <?php echo xlt('Substantial'); ?>
                                        </label>
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="goals_object_3_status" id="goals_object_3c" value="moderate" <?php echo ($check_res['goals_object_3_status'] == 'moderate') ? "checked": ($last_record['goals_object_3_status'] == 'moderate') ? " checked ":"";  ?> > <?php echo xlt('Moderate'); ?>
                                        </label>
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="goals_object_3_status" id="goals_object_3d" value="minimal" <?php echo ($check_res['goals_object_3_status'] == 'minimal') ? "checked":  ($last_record['goals_object_3_status'] == 'minimal') ? " checked ":"";  ?>  > <?php echo xlt('Minimal'); ?>
                                        </label>
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="goals_object_3_status" id="goals_object_3e" value="regression" <?php echo ($check_res['goals_object_3_status'] == 'regression') ? "checked":  ($last_record['goals_object_3_status'] == 'regression') ? " checked ":"";  ?> > <?php echo xlt('Regression'); ?>
                                        </label>
                                        <small class="text-danger clearfix goals_object_3_status_error"></small>
                                    </div>  
                                </div>

                            </div>

                            <div class="clearfix"></div>
                            <div class="col-md-12 margin-top-40">
                                <h4><?php echo xlt('Tx Plan Review:'); ?></h4>
                                <div class="col-md-6">
                                    <div class="form-group">
                                            <label for="plan_review_90" class="col-sm-3 control-label"><?php echo xlt('90 Day:'); ?> </label>
                                            <div class="col-sm-9">
                                              <input type="text" class="form-control plan_review_90 pull-left" name="plan_review_90" id="plan_review_90" value="<?php echo ($ninety_days) ? date('m/d/Y', strtotime($ninety_days)) : ''; ?>" <?php echo $ninety_days_disabled; ?> style="width:150px; margin-right: 10px" readonly>
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
                                              <input type="text" class="form-control plan_review_180 pull-left" name="plan_review_180" id="plan_review_180" value="<?php echo ($one_eighty) ? date('m/d/Y', strtotime($one_eighty)) : ''; ?>"  <?php echo $one_eighty_disabled; ?> style="width:150px; margin-right: 10px" readonly>
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
                                              <input type="text" class="form-control plan_review_270 pull-left" name="plan_review_270" id="plan_review_270" value="<?php echo ($two_seventy) ? date('m/d/Y', strtotime($two_seventy)) : ''; ?>"  <?php echo $two_seventy_disabled; ?> style="width:150px; margin-right: 10px" readonly>
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
                                              <input type="text" class="form-control plan_review_360 pull-left" name="plan_review_360" id="plan_review_360" value="<?php echo ($three_sixty) ? date('m/d/Y', strtotime($three_sixty)) : ''; ?>"  <?php echo $three_sixty_disabled; ?> style="width:150px; margin-right: 10px" readonly>
                                              <div class="date_completed">
                                                  <span class="pull-left" style="margin-right: 10px">Completed:</span>
                                                  <input type="text" name="completed_date_tx360" class="form-control datepicker" value="<?php echo ( $check_res['completed_date_tx360'] ) ? date('m/d/Y', strtotime($check_res['completed_date_tx360'])): '' ; ?>" style="width: 124px;" autocomplete="off">
                                              </div>
                                            </div>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div class="col-md-12 margin-top-20">
                                <label for="narrative"><?php echo xlt('Narrative of Service:'); ?></label>
                                <textarea name="narrative" id="narrative" rows="4" class="form-control"><?php echo ($check_res['narrative']) ? text($check_res['narrative']) : text($last_record['narrative']); ?></textarea>
                                <small class="text-danger narrative_services_error"></small>
                            </div>

                            <div class="clearfix"></div>

                            <div class="col-md-12 margin-top-20">
                                <div class="form-group">
                                    <label for="" class="col-md-3 "><?php echo xlt('(P) State the plan. I will meet with them: '); ?></label>
                                    <div class="col-md-3">
                                        <span class="col-md-3"><?php echo xlt('Date:'); ?> </span>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control datepicker" name="meet_again_date" id="meet_again_date" value="<?php echo ($check_res['meet_again_date']) ? text($check_res['meet_again_date']) : text($last_record['meet_again_date']); ?>" autocomplete="off">
                                            <small class="text-danger meet_again_date_error"></small>
                                        </div>                                        
                                    </div>

                                    <div class="col-md-3">
                                        <span class="col-md-3"><?php echo xlt('Time:'); ?> </span>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control timepicker" name="meet_again_time" id="meet_again_time" value="<?php echo ($check_res['meet_again_time']) ? text($check_res['meet_again_time']) : text($last_record['meet_again_time']); ?>" autocomplete="off">
                                            <small class="text-danger meet_again_time_error"></small>
                                        </div>
                                    </div>                                     
                                </div>
                            </div>
                            
                            <div class="clearfix"></div>

                            <div class="col-md-12 margin-top-20">
                                <label for="plan_details" class="col-md-2"><?php echo xlt('To work on:'); ?> </label>
                                <div >                                    
                                    <textarea name="plan_details" id="plan_details"  rows="4" class="form-control"><?php echo ($check_res['plan_details']) ? text($check_res['plan_details']) : text($last_record['plan_details']); ?></textarea>
                                    <small class="text-danger work_on_error"></small>
                                </div>
                            </div>

                            <div class="clearfix">&nbsp;</div>
                            
                    </fieldset>

                    

                    <div class="form-group clearfix">
                        <div class="col-sm-12 col-sm-offset-1 position-override">
                            <div class="btn-group oe-opt-btn-group-pinch" role="group">
                                <?php                                    
                                    if (($esign->isButtonViewable() and $is_group == 0 and $authPostCalendarCategoryWrite) or ($esign->isButtonViewable() and $is_group and acl_check("groups", "glog", false, 'write') and $authPostCalendarCategoryWrite)) {
                                        if (!$aco_spec || acl_check($aco_spec[0], $aco_spec[1], '', 'write')) {
                                            echo $esign->buttonHtml();
                                        }
                                    }
                                ?>
                                <button type='submit'  class="btn btn-default btn-save" name="save_progress_notes"><?php echo xlt('Save'); ?></button>
                                <button type="button" class="btn btn-link btn-cancel oe-opt-btn-separate-left" onclick="top.restoreSession(); parent.closeTab(window.name, false);"><?php echo xlt('Cancel');?></button>
                                <a href="#" class="btn btn-default" id="print" style="margin-left: 18px">Print</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <script src="<?php echo $web_root; ?>/library/js/bootstrap-timepicker.min.js"></script>
        <script src="<?php echo $web_root; ?>/library/js/printThis.js"></script>
        <script language="javascript">
            $(document).ready(function(){

                $('.timepicker').timepicker({
                  defaultTime: '12:00 PM',
                });


                $('.datepicker').datetimepicker({
                  <?php $datetimepicker_timepicker = false; ?>
                  <?php $datetimepicker_showseconds = false; ?>
                  <?php $datetimepicker_formatInput = false; ?>
                  <?php require($GLOBALS['srcdir'] . '/js/xl/jquery-datetimepicker-2-5-4.js.php'); ?>
                  <?php // can add any additional javascript settings to datetimepicker here; need to prepend first setting with a comma ?>
                });

                var today = new Date();

                $("input#endtime, input#starttime").on("keypress change blur focusout",function(){
                  var s = $("input#starttime").val();
                  var e = $("input#endtime").val();
                  var startTime = s.replace(/\s+/g, '').trim();
                  var endTime = e.replace(/\s+/g, '').trim();
                  if(startTime && endTime) {
                    
                    //console.log('Start: ' + startTime + ' | End: ' + endTime);
                    var date_today = today.getFullYear() + "-" + ('0' + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + (today.getDate())).slice(-2);
                    //console.log('Date Today: ' + date_today);
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

                   // console.log('Date1: ' + date1 + ' | date2: ' + date2);

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
                    
                  }
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
                        pageTitle: "Case Management Progress Note",              // add title to print page
                        removeInline: false,        // remove inline styles from print elements
                        removeInlineSelector: "*",  // custom selectors to filter inline styles. removeInline must be true
                        printDelay: 333,            // variable print delay
                        header: "<h2>Case Management Progress Note</h2>",               // prefix to html
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

            });
        </script>
    </body>
</html>
