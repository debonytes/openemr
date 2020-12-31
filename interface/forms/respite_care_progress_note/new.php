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
    <?php Header::setupHeader(['datetime-picker', 'opener','esign', 'common']); ?>
    <title><?php echo xlt('Respite Care Progress Note'); ?></title>
    <style>
        @media only screen and (max-width: 768px) {
            [class*="col-"] {
                width: 100%;
                text-align: left !Important;
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
            /* FROM $_SESSION
              Array
              (
                  [site_id] => default
                  [language_choice] => 1
                  [language_direction] => ltr
                  [authUser] => chris
                  [authPass] => $2a$05$QO2MWl/5j2PcGI16rroNJOTJODnhiDnXVK/wXLGW/K/xweNCqS9m2
                  [authGroup] => Default
                  [authUserID] => 15
                  [authProvider] => Default
                  [authId] => 15
                  [userauthorized] => 1
                  [last_update] => 1600928448
                  [csrf_private_key] => rC�m����4*iZGSHi�C�wT�`(��
                  [encounter] => 21
                  [frame1url] => ../../interface/patient_tracker/patient_tracker.php?skip_timeout_reset=1
                  [frame1target] => flb
                  [frame2url] => ../../interface/main/messages/messages.php?form_active=1
                  [frame2target] => msg
                  [pid] => 2
                  [alert_notify_pid] => 2
              )

              // echo "<pre>";
              // print_r( $_SESSION );
              // echo "</pre>";
            */

            $patient_id = ( isset($_SESSION['pid']) && $_SESSION['pid'] ) ? $_SESSION['pid'] : '';
            $user_id = ( isset($_SESSION['authUserID']) && $_SESSION['authUserID'] ) ? $_SESSION['authUserID'] : '';
            $webserver_root = dirname(dirname(__FILE__));
  
            $current_datetime = date('Y-m-d H:i:s');
            $datetime = ( isset($obj['date']) && $obj['date'] ) ? $obj['date'] : $current_datetime;
            $patient_full_name = '';

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
            
            ?>
            <form method=post <?php echo "name='my_form' " . "action='$rootdir/forms/$folderName/save.php?id=" . attr_url($formid) . "'\n"; ?>>
              <input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
              <input type="hidden" name="date" value="<?php echo $datetime; ?>">
              <input type="hidden" name="pid" value="<?php echo $patient_id; ?>">
              <input type="hidden" name="encounter" value="<?php echo $encounter; ?>">
              <input type="hidden" name="user" value="<?php echo $user_id; ?>">
              <input type="hidden" name="authorized" value="<?php echo $userauthorized; ?>">
              <input type="hidden" name="activity" value="1">
              <fieldset>

                <div class="field-group-wrapper">

                  <div class="field-row">

                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Member Name:</label>
                        <input type="text" readonly disabled class="form-control" value="<?php echo $patient_full_name; ?>">
                        <input type="hidden" name="member_name" value="<?php echo $patient_full_name; ?>" >
                      </div>
                      <div class="form-group">
                        <?php $provider_id =  ( isset($obj['provider_id']) && $obj['provider_id'] ) ? $obj['provider_id'] : '' ; ?>
                        <label>Respite Provider:</label>
                        <div class="select-field">
                          <input type="text" readonly disabled class="form-control" value="<?php echo $user_fullname; ?>">
                          <input type="hidden" id="provider_id" name="provider_id" value="<?php echo $user_id; ?>">
                        </div>
                      </div>
                      <div class="form-group">
                        <?php $billing_code =  ( isset($obj['billing_code']) && $obj['billing_code'] ) ? $obj['billing_code'] : 'Respite S5150' ; ?>
                        <label>Billing Code:</label>
                        <input type="text" readonly disabled class="form-control" value="<?php echo $billing_code; ?>">
                        <input type="hidden" id="billing_code" class="form-control" name="billing_code" value="<?php echo $billing_code; ?>">
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <?php 
                        $dateofservice =  ( $check_res['dateofservice'] ) ? $check_res['dateofservice'] : '' ; 
                        //$date_service_format = ($dateofservice) ? date('d/m/Y',strtotime($dateofservice)) : '';
                        ?>
                        <label>Date of Service:</label>
                        <input type="text" id="dateofservice" class="form-control datepicker" name="dateofservice" value="<?php echo text($dateofservice); ?>" autocomplete="off">
                      </div>

                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <?php 
                              $starttime =  ( $check_res['starttime'] ) ? text($check_res['starttime']) : '' ; 
                              //$start_time_format = ($starttime) ? date('h:i A',strtotime($starttime)) : '';
                            ?>
                            <label>Start Time:</label>
                            <input type="text" id="starttime" class="form-control timepicker" name="starttime" value="<?php echo $starttime; ?>" autocomplete="off">
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group">
                            <?php 
                              $endtime =  ( $check_res['endtime'] ) ? text($check_res['endtime']) : '' ; 
                              //$end_time_format = ($endtime) ? date('h:i A',strtotime($endtime)) : '';
                            ?>
                            <label>End Time:</label>
                            <input type="text" id="endtime" class="form-control timepicker" name="endtime" value="<?php echo $endtime; ?>" autocomplete="off">
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <?php 
                          $duration =  ( $check_res['duration'] ) ? text($check_res['duration']) : '' ; 
                        ?>
                        <label>Duration:</label>
                        <input type="text" id="duration" class="form-control" name="duration" value="<?php echo $duration; ?>" autocomplete="off">
                      </div>

                    </div>


                  </div>

                  <div class="field-row">
                    <div class="col-lg-12">
                      <h4 class="field-heading">Descriptions of Intervention</h4>
                  
                      <div class="form-group form-checkbox-field">
                        <?php
                          $service_locations = array("Home","Community");
                          $selected_service_loc = ( $check_res['service_local'] ) ? $check_res['service_local'] : '';
                        ?>       
                          <div class="col-sm-4">
                            <strong>State where the services took place:</strong>
                          </div>                 
                          
                          <div class="col-sm-8" style="display: block;">
                            <?php foreach ($service_locations as $loc) { 
                              $is_selected_service_loc = ($loc == $selected_service_loc) ? ' checked':'';
                              ?>
                              <label class="radio-inline" style="width: 20%; display: inline-block;" >
                                <input type="radio" name="service_local" value="<?php echo $loc; ?>"<?php echo $is_selected_service_loc; ?>> <?php echo $loc; ?>
                              </label> 
                            <?php } ?>
                          </div>
                        
                      </div>

                      <div class="clearfix"></div>

                      <div class="form-group" style="margin-top: 20px">
                        <p>
                          <strong>Objective(s) Addressed and Targeted Skill Area:</strong> Provide relief and de-escalation of stressful situations for the caregiver(s) as evidenced by child spending at least 5 hours per month with Respite Provider for the next 3 months.
                        </p>
                      </div>

                      <div class="form-group">
                        <?php 
                          $intervention_type =  ( $check_res['intervention_type'] ) ? text($check_res['intervention_type']) : '' ; 
                        ?>
                        <label>Type of Intervention:</label>
                        <textarea name="intervention_type" class="form-control" rows="4"><?php echo $intervention_type ?></textarea>
                      </div>

                      <div class="form-group">
                        
                        <label>Progress or Lack of Progress towards Treatment Goals:</label>
                        <span>Member Participated in Respite Program</span>
                        
                      </div>

                      <div class="form-group">
                        <?php 
                          $progress_narrative =  ( $check_res['progress_narrative'] ) ? text($check_res['progress_narrative']) : '' ; 
                        ?>
                        <label>Narrative:</label>
                        <textarea name="progress_narrative" class="form-control" rows="4"><?php echo $progress_narrative ?></textarea>
                      </div>

                      <div class="form-group " style="margin-top:20px;">
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
                                <input type="radio" name="crit_incidents" value="<?php echo $val; ?>" <?php echo ($val == $check_res['crit_incidents']) ? ' checked' : ''; ?> >
                                <?php echo $label; ?>                              
                              </label> 
                            <?php } ?>
                          </span>
                        
                      </div>

                      <div class="form-group">
                        <?php 
                          $critical_incidents_explan =  ( $check_res['critical_incidents_explan'] ) ? text($check_res['critical_incidents_explan']) : '';
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
                                  class="dontsave btn btn-link btn-cancel btn-separate-left"><?php echo xla('Cancel'); ?></button>
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
          defaultTime: null
        });

        var today = new Date();

        $("input#endtime, input#starttime").on("keypress change blur focusout",function(){
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
<script>
    $(function () {
        $('select').addClass("form-control");
    });
</script>
</body>
</html>
