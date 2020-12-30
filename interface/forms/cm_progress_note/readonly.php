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
<html>
    <head>
        <title><?php echo xlt("Case Management Progress Note"); ?></title>

        <?php Header::setupHeader(['datetime-picker', 'opener']); ?>
        <link rel="stylesheet" href="<?php echo $web_root; ?>/library/css/bootstrap-timepicker.min.css">
    </head>
    <body class="body_top">
        <div class="container">
            <div class="row">
                <div class="page-header">
                    <h2><?php echo xlt('Case Management Progress Note - Review'); ?></h2>
                </div>
            </div>
            <?php
            $current_date = date('Y-m-d');

            $patient_id = ( isset($_SESSION['alert_notify_pid']) && $_SESSION['alert_notify_pid'] ) ? $_SESSION['alert_notify_pid'] : '';
            $pid = ( isset($_SESSION['pid']) && $_SESSION['pid'] ) ? $_SESSION['pid'] : 0;
            if($patient_id) {
              $patient = getPatientData($patient_id);
              $patient_fname = ( isset($patient['fname']) && $patient['fname'] ) ? $patient['fname'] : '';
              $patient_mname = ( isset($patient['mname']) && $patient['mname'] ) ? $patient['mname'] : '';
              $patient_lname = ( isset($patient['lname']) && $patient['lname'] ) ? $patient['lname'] : '';
              $patientInfo = array($patient_fname,$patient_mname,$patient_lname);
              if($patientInfo && array_filter($patientInfo)) {
                $patient_full_name = implode( ' ', array_filter($patientInfo) );
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
                
                <form method="post" id="my_progress_notes_form" name="my_progress_notes_form" action="">
            

                
                    <input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
                    <input type="hidden" name="pid" value="<?php echo $pid; ?>">
                    <input type="hidden" name="encounter" value="<?php echo $encounter; ?>">
                    <input type="hidden" name="user" value="<?php echo $_SESSION['authUser']; ?>">
                    <input type="hidden" name="authorized" value="<?php echo $userauthorized; ?>">
                    <input type="hidden" name="activity" value="1">

                    <fieldset style="padding-top: 20px!important;">
                       

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="col-sm-3 "><?php echo xlt('Client Name'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text"  id="name" class="form-control" value="<?php echo text($patient_full_name); ?>" readonly>
                                        <input type="hidden" name="name" value="<?php echo text($patient_full_name); ?>" >
                                    </div>                                    
                                </div>
                                <div class="form-group">
                                    <label for="provider_id" class="col-sm-3 "><?php echo xlt('Case Manager'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="provider_id" id="provider_id" class="form-control" value="<?php echo text($check_res['provider_id']); ?>" readonly>
                                        
                                    </div>                                    
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-3 "><?php echo xlt('Billing Code'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text"  class="form-control" value="T1017 HN-CM" readonly>
                                        <input type="hidden" name="billing_code" value="T1017 HN-CM">
                                    </div>                                    
                                </div>

                                <div class="form-group">
                                    <label for="mileage" class="col-sm-3 "><?php echo xlt('Mileage'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="mileage" value="<?php echo text($mileage); ?>" readonly>
                                    </div>                                    
                                </div>

                                <div class="form-group">
                                    <label for="miles" class="col-sm-3 "><?php echo xlt('Miles'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="miles" value="<?php echo text($check_res['miles']); ?>" readonly>
                                    </div>                                    
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="col-sm-3 "><?php echo xlt('Date of Service'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="dateofservice" id="dateofservice" class="form-control " value="<?php echo text($check_res['dateofservice']); ?>" autocomplete="off" readonly>
                                        <small class="text-danger date_error"></small>
                                    </div>                                    
                                </div>

                                <div class="form-group">
                                    <label for="starttime" class="col-sm-3 "><?php echo xlt('Start Time'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="starttime" id="starttime" class="form-control " value="<?php echo text($check_res['starttime']); ?>" autocomplete="off" readonly>
                                        <small class="text-danger starttime_error"></small>
                                    </div>                                    
                                </div>

                                <div class="form-group">
                                    <label for="endtime" class="col-sm-3 "><?php echo xlt('End Time'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="endtime" id="endtime" class="form-control " value="<?php echo text($check_res['endtime']); ?>" autocomplete="off" readonly>
                                        <small class="text-danger endtime_error"></small>
                                    </div>                                    
                                </div>

                                <div class="form-group">
                                    <label for="duration" class="col-sm-3 "><?php echo xlt('Duration'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" id="duration" class="form-control" name="duration" value="<?php echo text($check_res['duration']); ?>" readonly>
                                        <small class="text-danger duration_error"></small>
                                    </div>                                    
                                </div>
                            </div>

                            <div class="clearfix" style="width:100%;float:left;margin:20px 0 0"></div>

                            <div class="col-md-12 margin-top-40" >
                                <div class="form-group">
                                    <label for="" class="col-sm-4 ">
                                        <?php echo xlt('(N) State where the services took place:'); ?>
                                    </label>
                                    <div class="col-sm-8">
                                        <?php if($check_res['services_place'] == 'home'): ?>
                                        <label class="radio-inline">
                                          <input type="radio" name="services_place" id="services_place1" value="home" <?php echo ($check_res['services_place'] == 'home') ? "checked": "";  ?> > <?php echo xlt('Home'); ?>
                                        </label>
                                        <?php endif; ?>

                                        <?php if($check_res['services_place'] == 'community'): ?>
                                        <label class="radio-inline">
                                          <input type="radio" name="services_place" id="services_place2" value="community"  <?php echo ($check_res['services_place'] == 'community') ? "checked": "";  ?> > <?php echo xlt('Community'); ?>
                                        </label>
                                        <?php endif; ?>
                                        
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="" class="col-sm-4 ">
                                        <?php echo xlt('And who you were with:'); ?>
                                    </label>
                                    <div class="col-sm-8">
                                        <?php if($check_res['services_with'] == 'client'): ?>
                                        <label class="radio-inline">
                                          <input type="radio" name="services_with" id="services_with1" value="client"  <?php echo ($check_res['services_with'] == 'client') ? "checked": "";  ?> > <?php echo xlt('Client'); ?>
                                        </label>
                                        <?php endif; ?>

                                        <?php if($check_res['services_with'] == 'family'): ?>
                                        <label class="radio-inline">
                                          <input type="radio" name="services_with" id="services_with2" value="family" <?php echo ($check_res['services_with'] == 'family') ? "checked": "";  ?> > <?php echo xlt('Family'); ?>
                                        </label>
                                        <?php endif; ?>                                        
                                    </div>
                                </div>
                            </div>

                            <div class="clearfix"></div>

                            <div class="col-md-12" style="margin-top:20px">
                                <p>
                                    <?php echo xlt('(A) State the goals and tasks set at the beginning of services for the client. You will not always work on all of them at each date of service.'); ?>
                                </p>

                                <div class="form-group margin-top-20">
                                    <label for="" class="col-sm-2 control-label"><strong><?php echo xlt('Objective 1.1 (T1016):'); ?></strong></label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control " name="goals_object_1" id="goals_object_1" style="width: 250px; float: left; margin-right: 20px" value="<?php echo text($check_res['goals_object_1']); ?>" readonly>
                                        <small class="text-danger goals_object_1_error" style="height: 24px; line-height: 24px;"></small>
                                    </div>

                                    <div class="col-sm-4" >
                                        <div class="form-group padding-left-18">
                                            <?php if($check_res['goals_object_1_status'] == 'completed'): ?>
                                            <label class="radio-inline margin-right-40">
                                              <input type="radio" name="goals_object_1_status" id="goals_object_1a" value="completed" <?php echo ($check_res['goals_object_1_status'] == 'completed') ? "checked": "";  ?> > <?php echo xlt('Completed/Maintenance'); ?>
                                            </label>
                                            <?php endif; ?>

                                            <?php if($check_res['goals_object_1_status'] == 'substantial'): ?>
                                            <label class="radio-inline margin-right-40">
                                              <input type="radio" name="goals_object_1_status" id="goals_object_1b" value="substantial" <?php echo ($check_res['goals_object_1_status'] == 'substantial') ? "checked": "";  ?> > <?php echo xlt('Substantial'); ?>
                                            </label>
                                            <?php endif; ?>

                                            <?php if($check_res['goals_object_1_status'] == 'moderate'): ?>
                                            <label class="radio-inline margin-right-40">
                                              <input type="radio" name="goals_object_1_status" id="goals_object_1c" value="moderate" <?php echo ($check_res['goals_object_1_status'] == 'moderate') ? "checked": "";  ?> > <?php echo xlt('Moderate'); ?>
                                            </label>
                                            <?php endif; ?>

                                            <?php if($check_res['goals_object_1_status'] == 'minimal'): ?>
                                            <label class="radio-inline margin-right-40">
                                              <input type="radio" name="goals_object_1_status" id="goals_object_1d" value="minimal" <?php echo ($check_res['goals_object_1_status'] == 'minimal') ? "checked": "";  ?> > <?php echo xlt('Minimal'); ?>
                                            </label>
                                            <?php endif; ?>

                                            <?php if($check_res['goals_object_1_status'] == 'regression'): ?>
                                            <label class="radio-inline margin-right-40">
                                              <input type="radio" name="goals_object_1_status" id="goals_object_1e" value="regression" <?php echo ($check_res['goals_object_1_status'] == 'regression') ? "checked": "";  ?> > <?php echo xlt('Regression'); ?>
                                            </label>
                                            <?php endif; ?>
                                            
                                        </div>  
                                    </div>
                                </div>


                                

                                <div class="clearfix "></div>

                                <div class="form-group margin-top-20">
                                    <label for="" class="col-sm-2 control-label"><strong><?php echo xlt('Objective 2.1 (T1016):'); ?></strong></label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="goals_object_2" id="goals_object_2" style="width: 250px; float: left; margin-right: 20px" value="<?php echo text($check_res['goals_object_2']); ?>" readonly>
                                        
                                    </div>

                                    <div class="col-sm-4" >
                                        <div class="form-group padding-left-18">
                                            <?php if($check_res['goals_object_2_status'] == 'completed'): ?>
                                            <label class="radio-inline margin-right-40">
                                              <input type="radio" name="goals_object_2_status" id="goals_object_2a" value="completed" <?php echo ($check_res['goals_object_2_status'] == 'completed') ? "checked": "";  ?> > <?php echo xlt('Completed/Maintenance'); ?>
                                            </label>
                                            <?php endif; ?>

                                            <?php if($check_res['goals_object_2_status'] == 'substantial'): ?>
                                            <label class="radio-inline margin-right-40">
                                              <input type="radio" name="goals_object_2_status" id="goals_object_2b" value="substantial" <?php echo ($check_res['goals_object_2_status'] == 'substantial') ? "checked": "";  ?> > <?php echo xlt('Substantial'); ?>
                                            </label>
                                            <?php endif; ?>

                                            <?php if($check_res['goals_object_2_status'] == 'moderate'): ?>
                                            <label class="radio-inline margin-right-40">
                                              <input type="radio" name="goals_object_2_status" id="goals_object_2c" value="moderate" <?php echo ($check_res['goals_object_2_status'] == 'moderate') ? "checked": "";  ?> > <?php echo xlt('Moderate'); ?>
                                            </label>
                                            <?php endif; ?>

                                            <?php if($check_res['goals_object_2_status'] == 'minimal'): ?>
                                            <label class="radio-inline margin-right-40">
                                              <input type="radio" name="goals_object_2_status" id="goals_object_2d" value="minimal" <?php echo ($check_res['goals_object_2_status'] == 'minimal') ? "checked": "";  ?> > <?php echo xlt('Minimal'); ?>
                                            </label>
                                            <?php endif; ?>

                                            <?php if($check_res['goals_object_2_status'] == 'regression'): ?>
                                            <label class="radio-inline margin-right-40">
                                              <input type="radio" name="goals_object_2_status" id="goals_object_2e" value="regression" <?php echo ($check_res['goals_object_2_status'] == 'regression') ? "checked": "";  ?> > <?php echo xlt('Regression'); ?>
                                            </label>
                                            <?php endif; ?>
                                            
                                        </div>  
                                    </div>


                                </div>

                                

                                

                                <div class="clearfix "></div>

                                <div class="form-group margin-top-20">
                                    <label for="" class="col-sm-2 control-label"><strong><?php echo xlt('Objective 3.1 (T1016):'); ?></strong></label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="goals_object_3" id="goals_object_3" style="width: 250px; float: left; margin-right: 20px" value="<?php echo text($check_res['goals_object_3']); ?>" readonly>
                                        <small class="text-danger goals_object_3_error" style="height: 24px; line-height: 24px;"></small>
                                    </div>
                                    <div class="col-sm-4" >
                                        <div class="form-group padding-left-18">
                                            <?php if($check_res['goals_object_3_status'] == 'completed'): ?>
                                            <label class="radio-inline margin-right-40">
                                              <input type="radio" name="goals_object_3_status" id="goals_object_3a" value="completed" <?php echo ($check_res['goals_object_3_status'] == 'completed') ? "checked": "";  ?>  > <?php echo xlt('Completed/Maintenance'); ?>
                                            </label>
                                            <?php endif; ?>

                                            <?php if($check_res['goals_object_3_status'] == 'substantial'): ?>
                                            <label class="radio-inline margin-right-40">
                                              <input type="radio" name="goals_object_3_status" id="goals_object_3b" value="substantial" <?php echo ($check_res['goals_object_3_status'] == 'substantial') ? "checked": "";  ?> > <?php echo xlt('Substantial'); ?>
                                            </label>
                                            <?php endif; ?>

                                            <?php if($check_res['goals_object_3_status'] == 'moderate'): ?>
                                            <label class="radio-inline margin-right-40">
                                              <input type="radio" name="goals_object_3_status" id="goals_object_3c" value="moderate" <?php echo ($check_res['goals_object_3_status'] == 'moderate') ? "checked": "";  ?> > <?php echo xlt('Moderate'); ?>
                                            </label>
                                            <?php endif; ?>

                                            <?php if($check_res['goals_object_3_status'] == 'minimal'): ?>
                                            <label class="radio-inline margin-right-40">
                                              <input type="radio" name="goals_object_3_status" id="goals_object_3d" value="minimal" <?php echo ($check_res['goals_object_3_status'] == 'minimal') ? "checked": "";  ?>  > <?php echo xlt('Minimal'); ?>
                                            </label>
                                            <?php endif; ?>

                                            <?php if($check_res['goals_object_3_status'] == 'regression'): ?>
                                            <label class="radio-inline margin-right-40">
                                              <input type="radio" name="goals_object_3_status" id="goals_object_3e" value="regression" <?php echo ($check_res['goals_object_3_status'] == 'regression') ? "checked": "";  ?> > <?php echo xlt('Regression'); ?>
                                            </label>
                                            <?php endif; ?>
                                            
                                        </div>  
                                    </div>
                                </div>

                                

                                

                            </div>

                            <div class="clearfix"></div>

                            <div class="col-md-12 margin-top-20" style="margin-top: 20px">
                                <label for="narrative_services"><?php echo xlt('Narrative of Service:'); ?></label>
                                <textarea name="narrative_services" id="narrative_services" rows="4" class="form-control" readonly><?php echo text($check_res['narrative_services']); ?></textarea>
                                <small class="text-danger narrative_services_error"></small>
                            </div>

                            <div class="clearfix"></div>

                            <div class="col-md-12 margin-top-20">
                                <div class="form-group">
                                    <label for="" class="col-sm-3 "><?php echo xlt('(P) State the plan. I will meet with them: '); ?></label>
                                    <div class="col-sm-3">
                                        <span class="col-sm-3"><?php echo xlt('Date:'); ?> </span>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control " name="meet_again_date" id="meet_again_date" value="<?php echo text($check_res['meet_again_date']); ?>" autocomplete="off" readonly>
                                            <small class="text-danger meet_again_date_error"></small>
                                        </div>                                        
                                    </div>

                                    <div class="col-sm-3">
                                        <span class="col-sm-3"><?php echo xlt('Time:'); ?> </span>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control " name="meet_again_time" id="meet_again_time" value="<?php echo text($check_res['meet_again_time']); ?>" autocomplete="off" readonly>
                                            <small class="text-danger meet_again_time_error"></small>
                                        </div>
                                    </div>                                     
                                </div>
                            </div>
                            
                            <div class="clearfix"></div>

                            <div class="col-md-12 margin-top-20">
                                <label for="work_on" class="col-sm-2"><?php echo xlt('To work on:'); ?> </label>
                                <div >                                    
                                    <textarea name="work_on" id="work_on"  rows="4" class="form-control" readonly><?php echo text($check_res['work_on']); ?></textarea>
                                    <small class="text-danger work_on_error"></small>
                                </div>
                            </div>

                            <div class="clearfix">&nbsp;</div>
                            
                    </fieldset>

                    

                    <div class="form-group clearfix">
                        <div class="col-sm-12 col-sm-offset-1 position-override">
                            <div class="btn-group oe-opt-btn-group-pinch" role="group">
                                <button type="button" class="btn btn-link btn-cancel oe-opt-btn-separate-left" onclick="top.restoreSession(); parent.closeTab(window.name, false);"><?php echo xlt('Cancel');?></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <script src="<?php echo $web_root; ?>/library/js/bootstrap-timepicker.min.js"></script>
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
                    
                    var date_today = today.getFullYear() + "-" + ('0' + (today.getMonth() + 1)).slice(-2) + "-" + ('0' + (today.getDate() + 1)).slice(-2);
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

            });
        </script>
    </body>
</html>
