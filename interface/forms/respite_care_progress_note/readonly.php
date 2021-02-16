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

                .margin-top-30{
                  margin-top: 30px;
                }

                .margin-right-20{
                  margin-right: 20px;
                }

            }
            @page {
              margin: 2cm;
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
                        <?php $billing_code =  ( $check_res['billing_code'] ) ? $check_res['billing_code'] : 'Respite S5150' ; ?>
                        <label>Billing Code:</label>
                        <input type="text" readonly disabled class="form-control" value="<?php echo text($billing_code); ?>">
                        <input type="hidden" id="billing_code" class="form-control" name="billing_code" value="<?php echo text($billing_code); ?>">
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <?php 
                        $dateofservice =  ( $check_res['dateofservice'] ) ? $check_res['dateofservice'] : '' ; 
                        //$date_service_format = ($dateofservice) ? date('d/m/Y',strtotime($dateofservice)) : '';
                        ?>
                        <label>Date of Service:</label>
                        <input type="text" id="dateofservice" class="form-control" name="dateofservice" disabled value="<?php echo text($dateofservice); ?>" autocomplete="off">
                      </div>

                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <?php 
                              $starttime =  ( $check_res['starttime'] ) ? text($check_res['starttime']) : '' ; 
                              //$start_time_format = ($starttime) ? date('h:i A',strtotime($starttime)) : '';
                            ?>
                            <label>Start Time:</label>
                            <input type="text" id="starttime" class="form-control " name="starttime" disabled value="<?php echo $starttime; ?>" autocomplete="off">
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group">
                            <?php 
                              $endtime =  ( $check_res['endtime'] ) ? text($check_res['endtime']) : '' ; 
                              //$end_time_format = ($endtime) ? date('h:i A',strtotime($endtime)) : '';
                            ?>
                            <label>End Time:</label>
                            <input type="text" id="endtime" class="form-control" disabled name="endtime" value="<?php echo $endtime; ?>" autocomplete="off">
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <?php 
                          $duration =  ( $check_res['duration'] ) ? text($check_res['duration']) : '' ; 
                        ?>
                        <label>Duration:</label>
                        <input type="text" id="duration" class="form-control" name="duration" disabled value="<?php echo $duration; ?>" autocomplete="off">
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
                          $selected_service_loc = ( $check_res['service_local'] ) ? $check_res['service_local'] : '';
                        ?>       
                          <div class="col-md-5">
                            <strong>State where the services took place:</strong>
                          </div>                 
                          
                          <div class="col-md-7" style="display: block;">
                            <?php foreach ($service_locations as $loc) { 
                              $is_selected_service_loc = ($loc == $selected_service_loc) ? ' checked': '';
                              ?>
                              <label class="radio-inline margin-right-20" style="width: 20%; display: inline-block;" >
                                <input type="radio" name="service_local" disabled value="<?php echo $loc; ?>"<?php echo $is_selected_service_loc; ?>> <?php echo $loc; ?>
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

                      <div class="clearfix"></div>
                      <div class="form-group margin-top-30">
                        <?php 
                          $intervention_type =  ( $check_res['intervention_type'] ) ? text($check_res['intervention_type']) : '' ; 
                        ?>
                        <label>Type of Intervention:</label>
                        <textarea name="intervention_type" class="form-control" rows="4" disabled><?php echo $intervention_type ?></textarea>
                      </div>

                      <div class="form-group">
                        
                        <label>Progress or Lack of Progress towards Treatment Goals:</label>
                        <span>Member Participated in Respite Program</span>
                        
                      </div>

                      <div class="clearfix"></div>
                      <div class="form-group margin-top-30">
                        <?php 
                          $progress_narrative =  ( $check_res['progress_narrative'] ) ? text($check_res['progress_narrative']) : '' ; 
                        ?>
                        <label>Narrative:</label>
                        <textarea name="progress_narrative" class="form-control" rows="4" disabled><?php echo $progress_narrative ?></textarea>
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
                                <input type="radio" name="crit_incidents" disabled value="<?php echo $val; ?>" <?php echo ($val == $check_res['crit_incidents']) ? ' checked' : ''; ?> >
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
                        <textarea name="critical_incidents_explan" class="form-control" rows="4" disabled><?php echo $critical_incidents_explan ?></textarea>
                      </div>

                    </div>

                  </div> 

                </div>    
                  
              </fieldset>
            
              <div class="form-group clearfix">
                  <div class="col-sm-offset-1 col-sm-12 position-override">
                      <div class="btn-group btn-pinch" role="group">                          
                          <button type="button"
                                  class="dontsave btn btn-link btn-cancel btn-separate-left"><?php echo xla('Cancel'); ?></button>
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

    });
</script>
<script>
    $(function () {
        $('select').addClass("form-control");
    });
</script>
</body>
</html>
