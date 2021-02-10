<?php
/**
 * Clinical instructions form.
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
$mileage = ( isset($check_res['mileage']) && $check_res['mileage'] ) ? $check_res['mileage'] : 'T2002';
$miles = ( isset($check_res['miles']) && $check_res['miles'] ) ? $check_res['miles'] : '';



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

            }
            @page {
              margin: 2cm;
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
                                        <input type="text"  id="name" class="form-control" value="<?php echo text($patient_full_name); ?>" readonly disabled>
                                        <input type="hidden" name="name" value="<?php echo text($patient_full_name); ?>" >
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="examiner" class="col-md-5 "><?php echo xlt('Case Manager'); ?></label>
                                    <div class="col-md-6">
                                        <input type="text" name="examiner" id="examiner" class="form-control" value="<?php echo text($check_res['examiner']); ?>" disabled>
                                        <small class="text-danger cbrs_error"></small>
                                    </div>   
                                    <div class="clearfix"></div>                                 
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="" class="col-md-5 "><?php echo xlt('Billing Code'); ?></label>
                                    <div class="col-md-6">
                                        <input type="text"  class="form-control" value="T1017 HN-CM" readonly disabled>
                                        <input type="hidden" name="billing_code" value="T1017 HN-CM">
                                    </div>   
                                    <div class="clearfix"></div>                                 
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="mileage" class="col-md-5 "><?php echo xlt('Mileage'); ?></label>
                                    <div class="col-md-6">
                                        <input type="text" name="mileage" class="form-control" value="<?php echo text($mileage); ?>" disabled>
                                    </div>        
                                    <div class="clearfix"></div>                            
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="service_miles" class="col-md-5 "><?php echo xlt('Miles'); ?></label>
                                    <div class="col-md-6">
                                        <input type="text" name="service_miles" class="form-control" value="<?php echo text($check_res['service_miles']); ?>" disabled>
                                    </div> 
                                    <div class="clearfix"></div>                                   
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="col-md-5"><?php echo xlt('Date of Service'); ?></label>
                                    <div class="col-md-6">
                                        <input type="text" name="dateofservice" id="dateofservice" class="form-control" value="<?php echo ($check_res['dateofservice']) ? text(date('m/d/Y', strtotime($check_res['dateofservice']))) : date('m/d/Y'); ?>" autocomplete="off" disabled>
                                        <small class="text-danger date_error"></small>
                                    </div>   
                                    <div class="clearfix"></div>                                 
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="starttime" class="col-md-5 "><?php echo xlt('Start Time'); ?></label>
                                    <div class="col-md-6">
                                        <input type="text" name="starttime" id="starttime" class="form-control" value="<?php echo text($check_res['starttime']); ?>" autocomplete="off" disabled>
                                        <small class="text-danger starttime_error"></small>
                                    </div>    
                                    <div class="clearfix"></div>                                
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="endtime" class="col-md-5 "><?php echo xlt('End Time'); ?></label>
                                    <div class="col-md-6">
                                        <input type="text" name="endtime" id="endtime" class="form-control " value="<?php echo text($check_res['endtime']); ?>" autocomplete="off" disabled>
                                        <small class="text-danger endtime_error"></small>
                                    </div>   
                                    <div class="clearfix"></div>                                 
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="duration" class="col-md-5 "><?php echo xlt('Duration'); ?></label>
                                    <div class="col-md-6">
                                        <input type="text" id="duration" class="form-control" name="duration" value="<?php echo ($check_res['duration']) ? text($check_res['duration']) : ''; ?>" disabled>
                                        <small class="text-danger duration_error"></small>
                                    </div>       
                                    <div class="clearfix"></div>                             
                                </div>
                            </div>

                            <div class="clearfix" style="width:100%;float:left;margin:20px 0 0"></div>

                            <div class="col-md-12 margin-top-40" >
                                <div class="form-group">
                                    <label for="" class="col-md-5 ">
                                        <?php echo xlt('(N) State where the services took place:'); ?>
                                    </label>
                                    <div class="col-md-7">
                                        <label class="radio-inline">
                                          <input type="radio" name="services_place" id="services_place1" value="home" <?php echo ($check_res['services_place'] == 'home') ? "checked": "";  ?> disabled> <?php echo xlt('Home'); ?>
                                        </label>
                                        <label class="radio-inline">
                                          <input type="radio" name="services_place" id="services_place2" value="community"  <?php echo ($check_res['services_place'] == 'community') ? "checked": "";  ?> disabled> <?php echo xlt('Community'); ?>
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
                                        <label class="radio-inline">
                                          <input type="radio" name="services_with" id="services_with1" value="client"  <?php echo ($check_res['services_with'] == 'client') ? "checked": "";  ?> disabled> <?php echo xlt('Client'); ?>
                                        </label>
                                        <label class="radio-inline">
                                          <input type="radio" name="services_with" id="services_with2" value="family" <?php echo ($check_res['services_with'] == 'family') ? "checked": "";  ?> disabled> <?php echo xlt('Family'); ?>
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
                                        <input type="text" class="form-control " name="goals_object_1" id="goals_object_1" style="width: 250px; float: left; margin-right: 20px" value="<?php echo text($check_res['goals_object_1']); ?>" disabled>
                                        <small class="text-danger goals_object_1_error" style="height: 24px; line-height: 24px;"></small>
                                    </div>
                                </div>

                                <div class="clearfix"></div>

                                <div class="col-md-12 margin-left-40" >
                                    <div class="form-group padding-left-18 full-width">
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="goals_object_1_status" id="goals_object_1a" value="completed" <?php echo ($check_res['goals_object_1_status'] == 'completed') ? "checked": "";  ?> disabled> <?php echo xlt('Completed/Maintenance'); ?>
                                        </label>
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="goals_object_1_status" id="goals_object_1b" value="substantial" <?php echo ($check_res['goals_object_1_status'] == 'substantial') ? "checked": "";  ?> disabled> <?php echo xlt('Substantial'); ?>
                                        </label>
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="goals_object_1_status" id="goals_object_1c" value="moderate" <?php echo ($check_res['goals_object_1_status'] == 'moderate') ? "checked": "";  ?> disabled> <?php echo xlt('Moderate'); ?>
                                        </label>
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="goals_object_1_status" id="goals_object_1d" value="minimal" <?php echo ($check_res['goals_object_1_status'] == 'minimal') ? "checked": "";  ?> disabled> <?php echo xlt('Minimal'); ?>
                                        </label>
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="goals_object_1_status" id="goals_object_1e" value="regression" <?php echo ($check_res['goals_object_1_status'] == 'regression') ? "checked": "";  ?> disabled> <?php echo xlt('Regression'); ?>
                                        </label>
                                        <small class="text-danger clearfix goals_object_1_status_error"></small>
                                    </div>  
                                </div>

                                <div class="clearfix "></div>

                                <div class="form-group margin-top-20">
                                    <label for="" class="col-md-3 control-label"><strong><?php echo xlt('Objective 2.1 (T1016):'); ?></strong></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="goals_object_2" id="goals_object_2" style="width: 250px; float: left; margin-right: 20px" value="<?php echo text($check_res['goals_object_2']); ?>" disabled>
                                        <small class="text-danger goals_object_2_error" style="height: 24px; line-height: 24px;"></small>
                                    </div>
                                </div>

                                <div class="clearfix"></div>

                                <div class="col-md-12 margin-left-40" >
                                    <div class="form-group padding-left-18">
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="goals_object_2_status" id="goals_object_2a" value="completed" <?php echo ($check_res['goals_object_2_status'] == 'completed') ? "checked": "";  ?> disabled> <?php echo xlt('Completed/Maintenance'); ?>
                                        </label>
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="goals_object_2_status" id="goals_object_2b" value="substantial" <?php echo ($check_res['goals_object_2_status'] == 'substantial') ? "checked": "";  ?> disabled> <?php echo xlt('Substantial'); ?>
                                        </label>
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="goals_object_2_status" id="goals_object_2c" value="moderate" <?php echo ($check_res['goals_object_2_status'] == 'moderate') ? "checked": "";  ?> disabled> <?php echo xlt('Moderate'); ?>
                                        </label>
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="goals_object_2_status" id="goals_object_2d" value="minimal" <?php echo ($check_res['goals_object_2_status'] == 'minimal') ? "checked": "";  ?> disabled> <?php echo xlt('Minimal'); ?>
                                        </label>
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="goals_object_2_status" id="goals_object_2e" value="regression" <?php echo ($check_res['goals_object_2_status'] == 'regression') ? "checked": "";  ?> disabled > <?php echo xlt('Regression'); ?>
                                        </label>
                                        <small class="text-danger clearfix goals_object_2_status_error"></small>
                                    </div>  
                                </div>

                                <div class="clearfix "></div>

                                <div class="form-group margin-top-20">
                                    <label for="" class="col-md-3 control-label"><strong><?php echo xlt('Objective 3.1 (T1016):'); ?></strong></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="goals_object_3" id="goals_object_3" style="width: 250px; float: left; margin-right: 20px" value="<?php echo text($check_res['goals_object_3']); ?>" disabled>
                                        <small class="text-danger goals_object_3_error" style="height: 24px; line-height: 24px;"></small>
                                    </div>
                                </div>

                                <div class="clearfix"></div>

                                <div class="col-md-12 margin-left-40" >
                                    <div class="form-group padding-left-18">
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="goals_object_3_status" id="goals_object_3a" value="completed" <?php echo ($check_res['goals_object_3_status'] == 'completed') ? "checked": "";  ?>  disabled> <?php echo xlt('Completed/Maintenance'); ?>
                                        </label>
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="goals_object_3_status" id="goals_object_3b" value="substantial" <?php echo ($check_res['goals_object_3_status'] == 'substantial') ? "checked": "";  ?> disabled> <?php echo xlt('Substantial'); ?>
                                        </label>
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="goals_object_3_status" id="goals_object_3c" value="moderate" <?php echo ($check_res['goals_object_3_status'] == 'moderate') ? "checked": "";  ?> disabled> <?php echo xlt('Moderate'); ?>
                                        </label>
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="goals_object_3_status" id="goals_object_3d" value="minimal" <?php echo ($check_res['goals_object_3_status'] == 'minimal') ? "checked": "";  ?>  disabled> <?php echo xlt('Minimal'); ?>
                                        </label>
                                        <label class="radio-inline margin-right-40">
                                          <input type="radio" name="goals_object_3_status" id="goals_object_3e" value="regression" <?php echo ($check_res['goals_object_3_status'] == 'regression') ? "checked": "";  ?> disabled> <?php echo xlt('Regression'); ?>
                                        </label>
                                        <small class="text-danger clearfix goals_object_3_status_error"></small>
                                    </div>  
                                </div>

                            </div>

                            <div class="clearfix"></div>

                            <div class="col-md-12 margin-top-20">
                                <label for="narrative"><?php echo xlt('Narrative of Service:'); ?></label>
                                <textarea name="narrative" id="narrative" rows="4" class="form-control" disabled><?php echo text($check_res['narrative']); ?></textarea>
                                <small class="text-danger narrative_services_error"></small>
                            </div>

                            <div class="clearfix"></div>

                            <div class="col-md-12 margin-top-20">
                                <div class="form-group">
                                    <label for="" class="col-md-3 "><?php echo xlt('(P) State the plan. I will meet with them: '); ?></label>
                                    <div class="col-md-3">
                                        <span class="col-md-3"><?php echo xlt('Date:'); ?> </span>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control " name="meet_again_date" id="meet_again_date" value="<?php echo text($check_res['meet_again_date']); ?>" autocomplete="off" disabled>
                                            <small class="text-danger meet_again_date_error"></small>
                                        </div>                                        
                                    </div>

                                    <div class="col-md-3">
                                        <span class="col-md-3"><?php echo xlt('Time:'); ?> </span>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control " name="meet_again_time" id="meet_again_time" value="<?php echo text($check_res['meet_again_time']); ?>" autocomplete="off" disabled>
                                            <small class="text-danger meet_again_time_error"></small>
                                        </div>
                                    </div>                                     
                                </div>
                            </div>
                            
                            <div class="clearfix"></div>

                            <div class="col-md-12 margin-top-20">
                                <label for="plan_details" class="col-md-2"><?php echo xlt('To work on:'); ?> </label>
                                <div >                                    
                                    <textarea name="plan_details" id="plan_details"  rows="4" class="form-control" disabled><?php echo text($check_res['plan_details']); ?></textarea>
                                    <small class="text-danger work_on_error"></small>
                                </div>
                            </div>

                            <div class="clearfix">&nbsp;</div>
                            
                    </fieldset>

                    

                    <div class="form-group clearfix">
                        <div class="col-sm-12 col-sm-offset-1 position-override">
                            <div class="btn-group oe-opt-btn-group-pinch" role="group">                                
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
                  defaultTime: null
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
