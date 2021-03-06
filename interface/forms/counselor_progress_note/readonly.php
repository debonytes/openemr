<?php
/**
 * Progress Note form.
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

$folderName = 'counselor_progress_note';
$tableName = 'form_' . $folderName;


$returnurl = 'encounter_top.php';
$formid = 0 + (isset($_GET['id']) ? $_GET['id'] : 0);

$formStmt = "SELECT id FROM forms WHERE form_id=? AND formdir=?";
$form = sqlQuery($formStmt, array($formid, $folderName));

$GLOBALS['pid'] = empty($GLOBALS['pid']) ? $form['pid'] : $GLOBALS['pid'];

$check_res = $formid ? formFetch($tableName, $formid) : array();

$ninety_days_disabled = '';
$one_eighty_disabled = '';
$two_seventy_disabled = '';

if($pid){
    $patien_query = "SELECT CDA FROM patient_data WHERE pid = ?";
    $patient_data = sqlQuery($patien_query, array($pid));
    $cda_date = trim($patient_data['CDA']);
    $today = date('Y-m-d');
    $ninety_days = date('Y-m-d', strtotime($cda_date . '+ 90 days'));
    $one_eighty = date('Y-m-d', strtotime($cda_date . '+ 180 days'));
    $two_seventy = date('Y-m-d', strtotime($cda_date . '+ 270 days'));
    $after_one_year = date('Y-m-d', strtotime($cda_date . '+ 1 year'));

    $color_90 =  (strtotime($ninety_days) > strtotime($today) ) ? getDateColor($today, $ninety_days) : $gray;
    $color_180 = (strtotime($one_eighty) > strtotime($today) ) ? getDateColor($today, $one_eighty) : $gray;
    $color_270 = (strtotime($two_seventy) > strtotime($today) ) ? getDateColor($today, $two_seventy) : $gray;
    $color_cda = (strtotime($after_one_year) > strtotime($today) ) ? getCDADateColor($today, $after_one_year) : $gray;
    
    $ninety_days_disabled = (strtotime($ninety_days) < strtotime($today)) ? ' disabled ' : '';
    $one_eighty_disabled = (strtotime($one_eighty) < strtotime($today)) ? ' disabled' : '';
    $two_seventy_disabled = (strtotime($two_seventy) < strtotime($today)) ? ' disabled' : '';
}
?>
<html>
    <head>
        <title><?php echo xlt("Counselor Progress Note"); ?></title>

        <?php Header::setupHeader(['datetime-picker', 'opener', 'esign', 'common']); ?>
        <link rel="stylesheet" href="<?php echo $web_root; ?>/library/css/bootstrap-timepicker.min.css">
        <link rel="stylesheet" href="../../../style_custom.css">
        <style>
            @media print{
                .col-sm-2 {
                    width: 16.66666667%;
                }
                .col-sm-10 {
                    width: 83.33333333%;
                }
                .col-md-6 {
                    width: 50%;
                }
                .col-sm-4 {
                    width: 33.3333%;
                }
                .col-sm-3 {
                    width: 25%;
                }
                .col-sm-8 {
                    width: 66.66666667%;
                }
                .col-sm-9 {
                    width: 75%;
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
                    font-weight: 600;
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
                    padding-top: 60px;
                }
            }
            @page {
              margin: 2cm;
            }

            .margin-top-20 {
                margin-top: 20px;
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

            .cda_date, .cda_date[disabled]{
                color: white;
                background-color: <?php echo $color_cda; ?> !important;
            }

            .margin-top-20 {
                margin-top: 20px;
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
                    <h2><?php echo xlt('Counselor Progress Note'); ?></h2>
                </div>
            </div>
            
            <?php
            $current_date = date('Y-m-d');

            $patient_id = ( isset($_SESSION['alert_notify_pid']) && $_SESSION['alert_notify_pid'] ) ? $_SESSION['alert_notify_pid'] : '';
            $pid = ( isset($_SESSION['pid']) && $_SESSION['pid'] ) ? $_SESSION['pid'] : 0;
            if($patient_id) {
              $patient = getPatientData($patient_id);
              $patient_fname = ( $patient['fname'] ) ? $patient['fname'] : '';
              $patient_mname = ( $patient['mname'] ) ? $patient['mname'] : '';
              $patient_lname = ( $patient['lname'] ) ? $patient['lname'] : '';
              $patientInfo = array($patient_fname,$patient_mname,$patient_lname);
              if($patientInfo && array_filter($patientInfo)) {
                $patient_full_name = implode( ' ', array_filter($patientInfo) );
              } else {
                $patient_full_name = ($check_res['name']) ? $check_res['name'] : '';
              }
            }

            

            ?>
            <div class="row">
                
                <form method="post" id="my_progress_notes_form" name="my_progress_notes_form" action="<?php echo $rootdir; ?>/forms/<?php echo $folderName; ?>/save.php?id=<?php echo attr_url($formid); ?>">          

                
                    <input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
                    <input type="hidden" name="pid" value="<?php echo $pid; ?>">
                    <input type="hidden" name="encounter" value="<?php echo $encounter; ?>">
                    <input type="hidden" name="user" value="<?php echo $_SESSION['authUser']; ?>">
                    <input type="hidden" name="authorized" value="<?php echo $userauthorized; ?>">
                    <input type="hidden" name="activity" value="1">

                    <fieldset style="padding-top:20px!important" class="form_content">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="col-sm-3 "><?php echo xlt('Client Name'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text"  id="name" class="form-control" value="<?php echo text($patient_full_name); ?>" readonly disabled>
                                        <input type="hidden" name="name" value="<?php echo text($patient_full_name); ?>" >
                                    </div>                                    
                                </div>
                                <div class="form-group">
                                    <label for="counselor" class="col-sm-3 "><?php echo xlt('Counselor'); ?></label>
                                    <div class="col-sm-9">
                                        <?php $counselor = get_provider_details($check_res['counselor']); ?>
                                        <input type="text" name="counselor" id="counselor" class="form-control" value="<?php echo text($counselor['lname']) . ', ' . text($counselor['fname']) ; ?>" disabled>
                                        <small class="text-danger counselor_error"></small>
                                    </div>                                    
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-3 "><?php echo xlt('Location'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="location" id="location" class="form-control" value="<?php echo text(get_facility_by_id($check_res['location'])); ?>" disabled>
                                        <small class="text-danger location_error"></small>
                                    </div>                                    
                                </div>
                                <div class="form-group">
                                    <label for="cda_date" class="col-sm-3 "><?php echo xlt('CDA Date'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="cda_date" id="cda_date" class="form-control cda_date" value="<?php echo ($cda_date) ? date('m/d/Y', strtotime($cda_date)) : ''; ?>" disabled>
                                        <small class="text-danger location_error"></small>
                                    </div>                                    
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="col-sm-3 "><?php echo xlt('Date'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="date" id="date" class="form-control " value="<?php echo date('m/d/Y', strtotime($check_res['date'])); ?>" autocomplete="off" disabled>
                                        <small class="text-danger date_error"></small>
                                    </div>                                    
                                </div>

                                <div class="form-group">
                                    <label for="starttime" class="col-sm-3 "><?php echo xlt('Start Time'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="starttime" id="starttime" class="form-control " value="<?php echo text($check_res['starttime']); ?>" autocomplete="off" disabled>
                                        <small class="text-danger starttime_error"></small>
                                    </div>                                    
                                </div>

                                <div class="form-group">
                                    <label for="endtime" class="col-sm-3 "><?php echo xlt('End Time'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="endtime" id="endtime" class="form-control" value="<?php echo text($check_res['endtime']); ?>" autocomplete="off" disabled>
                                        <small class="text-danger endtime_error"></small>
                                    </div>                                    
                                </div>

                                <div class="form-group">
                                    <label for="session_number" class="col-sm-3 "><?php echo xlt('Session Number'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" id="session_number" class="form-control" name="session_number" value="<?php echo text($check_res['session_number']); ?>" disabled>
                                        <small class="text-danger session_number_error"></small>
                                    </div>                                    
                                </div>
                            </div>

                            <div class="clearfix"></div>

                            <div class="col-md-12 margin-top-20" style="margin-top: 30px" >
                                <table class="table table-bordered ">
                                    <thead>
                                        <tr>
                                            <th width="50%" rowspan="2" colspan="2" class="table-cell-center" style="text-align: center; vertical-align: middle;" ><?php echo xlt('GOALS'); ?></th>
                                            <th colspan="5" style="text-align: center"><?php echo xlt('PROGRESS'); ?></th>
                                        </tr>
                                        <tr>                                    
                                            <th width="10%" class="table-cell-center"><?php echo xlt('Completed / Maintained'); ?></th>
                                            <th width="10%" class="table-cell-center" style="text-align: center; vertical-align: middle;"><?php echo xlt('Substantial'); ?></th>
                                            <th width="10%" class="table-cell-center" style="text-align: center; vertical-align: middle;"><?php echo xlt('Moderate'); ?></th>
                                            <th width="10%" class="table-cell-center" style="text-align: center; vertical-align: middle;"><?php echo xlt('Minimal'); ?></th>
                                            <th width="10%" class="table-cell-center" style="text-align: center; vertical-align: middle;"><?php echo xlt('Regression'); ?></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr>
                                            <td width="4%">1</td>
                                            <td width="46%">                             
                                                <input type="text" name="goal_1" class="form-control" value="<?php echo text($check_res['goal_1']); ?>" disabled>
                                            </td>
                                            <td class="text-center">
                                                <label class="text-center">
                                                  <input type="radio" name="goal_1_answer" id="goal_1_answer_radio_1" value="1" <?php echo ($check_res['goal_1_answer'] == 1) ? 'checked': '';  ?> disabled>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="text-center">
                                                  <input type="radio" name="goal_1_answer" id="goal_1_answer_radio_2" value="2" <?php echo ($check_res['goal_1_answer'] == 2) ? 'checked': '';  ?> disabled>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="text-center">
                                                  <input type="radio" name="goal_1_answer" id="goal_1_answer_radio_3" value="3" <?php echo ($check_res['goal_1_answer'] == 3) ? 'checked': '';  ?> disabled>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="text-center">
                                                  <input type="radio" name="goal_1_answer" id="goal_1_answer_radio_4" value="4" <?php echo ($check_res['goal_1_answer'] == 4) ? 'checked': ''  ;  ?> disabled >
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="text-center">
                                                  <input type="radio" name="goal_1_answer" id="goal_1_answer_radio_5" value="5" <?php echo ($check_res['goal_1_answer'] == 5) ? 'checked': '' ;  ?>  disabled >
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>
                                                <input type="text" name="goal_2" class="form-control" value="<?php echo text($check_res['goal_2']); ?>" disabled>
                                            </td>
                                            <td class="text-center">
                                                <label >
                                                  <input type="radio" name="goal_2_answer" id="goal_2_answer_radio_1" value="1" <?php echo ($check_res['goal_2_answer'] == 1) ? 'checked': '';  ?> disabled >
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label >
                                                  <input type="radio" name="goal_2_answer" id="goal_2_answer_radio_2" value="2" <?php echo ($check_res['goal_2_answer'] == 2) ? 'checked': '';  ?> disabled >
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label >
                                                  <input type="radio" name="goal_2_answer" id="goal_2_answer_radio_3" value="3" <?php echo ($check_res['goal_2_answer'] == 3) ? 'checked': '' ;  ?> disabled >
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label >
                                                  <input type="radio" name="goal_2_answer" id="goal_2_answer_radio_4" value="4" <?php echo  ($check_res['goal_2_answer'] == 4) ? 'checked': '';  ?> disabled >
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label >
                                                  <input type="radio" name="goal_2_answer" id="goal_2_answer_radio_5" value="5" <?php echo ($check_res['goal_2_answer'] == 5) ? 'checked': '';  ?> disabled >
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>
                                                <input type="text" name="goal_3" class="form-control" value="<?php echo text($check_res['goal_3']); ?>" disabled>
                                            </td>
                                            <td class="text-center">
                                                <label >
                                                  <input type="radio" name="goal_3_answer" id="goal_3_answer_radio_1" value="1" <?php echo ($check_res['goal_3_answer'] == 1) ? 'checked': '';  ?> disabled >
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label >
                                                  <input type="radio" name="goal_3_answer" id="goal_3_answer_radio_2" value="2" <?php echo ($check_res['goal_3_answer'] == 2) ? 'checked': '';  ?> disabled>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label >
                                                  <input type="radio" name="goal_3_answer" id="goal_3_answer_radio_3" value="3"  <?php echo ($check_res['goal_3_answer'] == 3) ? 'checked': '';  ?> disabled >
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label >
                                                  <input type="radio" name="goal_3_answer" id="goal_3_answer_radio_4" value="4" <?php echo ($check_res['goal_3_answer'] == 4) ? 'checked': '';  ?> disabled >
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label >
                                                  <input type="radio" name="goal_3_answer" id="goal_3_answer_radio_5" value="5" <?php echo ($check_res['goal_3_answer'] == 5) ? 'checked': '';  ?> disabled >
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>
                                                <input type="text" name="goal_4" class="form-control" value="<?php echo text($check_res['goal_4']); ?>" disabled >
                                            </td>
                                            <td class="text-center">
                                                <label >
                                                  <input type="radio" name="goal_4_answer" id="goal_4_answer_radio_1" value="1" <?php echo ($check_res['goal_4_answer'] == 1) ? 'checked': '';  ?>  disabled >
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label >
                                                  <input type="radio" name="goal_4_answer" id="goal_4_answer_radio_2" value="2" <?php echo ($check_res['goal_4_answer'] == 2) ? 'checked': '' ;  ?> disabled >
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label >
                                                  <input type="radio" name="goal_4_answer" id="goal_4_answer_radio_3" value="3" <?php echo ($check_res['goal_4_answer'] == 3) ? 'checked': '';  ?>  disabled >
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label >
                                                  <input type="radio" name="goal_4_answer" id="goal_4_answer_radio_4" value="4"  <?php echo ($check_res['goal_4_answer'] == 4) ? 'checked': '';  ?> disabled >
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label >
                                                  <input type="radio" name="goal_4_answer" id="goal_4_answer_radio_5" value="5" <?php echo ($check_res['goal_4_answer'] == 5) ? 'checked': '';  ?> disabled >
                                                </label>
                                            </td>
                                        </tr>                                        
                                    </tbody>
                                </table>
                            </div>

                            <div class="clearfix"></div>

                            <div class="col-md-12 margin-top-20 treatment_diagnostic_row">
                                <div class="col-md-6">
                                    <h3><?php echo xlt('TREATMENT & DIAGNOSTIC CODING'); ?></h3>
                                   
                                    <div class="form-group">
                                        <label for="icd_code" class="col-sm-4 control-label"><?php echo xlt('ICD-10 Code (s):'); ?> </label>                         
                                        <div class="col-sm-8">
                                          <input type="text" class="form-control" name="icd_code" id="icd_code" value="<?php echo text($check_res['icd_code']); ?>" disabled>
                                        </div>
                                    </div>
                                    
                                    <div class="clearfix"></div>
                                    <div class="form-group">
                                        <label for="session_type" class="col-sm-4 control-label"><?php echo xlt('Session Type:'); ?> </label>                         
                                        <div class="col-sm-8">
                                          <input type="text" class="form-control" name="session_type" id="session_type" value="<?php echo text($check_res['session_type']); ?>" disabled>
                                        </div>
                                    </div>

                                    <div class="clearfix"></div>
                                    <div class="form-group">
                                        <label for="additional_session_type" class="col-sm-4 control-label"><?php echo xlt('Addtional Session Type:'); ?> </label>                         
                                        <div class="col-sm-8">
                                            <input type="checkbox" name="is_addtional_session_type" disabled id="is_addtional_session_type" class="form-control" style="width: 19px; height: 40px; float: left;" value="1" <?php echo !empty($check_res['additional_session_type']) ? 'checked' : ''; ?> > 
                                          <input type="text" class="form-control" disabled name="additional_session_type" id="additional_session_type" value="<?php echo text($check_res['additional_session_type']); ?>" style="width: 200px; float:right;">
                                        </div>
                                    </div>

                                   <div class="clearfix"></div>
                                    <div class="form-group">
                                            <label for="diagnosis" class="col-sm-4 control-label"><?php echo xlt('Diagnosis:'); ?> </label>                         
                                            <div class="col-sm-8">
                                              <input type="text" class="form-control" name="diagnosis" id="diagnosis" value="<?php echo text($check_res['diagnosis']); ?>" disabled>
                                            </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <h4><?php echo xlt('Tx Plan Review:'); ?></h4>

                                    <div class="form-group">
                                            <label for="plan_review_90" class="col-sm-3 control-label"><?php echo xlt('90 Day:'); ?> </label>
                                            <div class="col-sm-9">
                                              <input type="text" class="form-control plan_review_90 pull-left" name="plan_review_90" id="plan_review_90" value="<?php echo ($ninety_days) ? date('m/d/Y', strtotime($ninety_days)) : ''; ?>" disabled style="width:150px; margin-right: 10px">
                                              <div class="date_completed">
                                                  <span class="pull-left" style="margin-right: 10px">Completed:</span>
                                                  <input type="text" name="completed_date_tx90" class="form-control " value="<?php echo ( $check_res['completed_date_tx90'] ) ? date('m/d/Y', strtotime($check_res['completed_date_tx90'])): '' ; ?>" style="width: 124px;" disabled>
                                              </div>
                                            </div>
                                    </div>

                                    <div class="form-group">
                                            <label for="plan_review_180" class="col-sm-3 control-label"><?php echo xlt('180 Day: '); ?></label>
                                            <div class="col-sm-9">
                                              <input type="text" class="form-control plan_review_180 pull-left" name="plan_review_180" id="plan_review_180" value="<?php echo ($one_eighty) ? date('m/d/Y', strtotime($one_eighty)) : ''; ?>"  disabled style="width:150px; margin-right: 10px">
                                              <div class="date_completed">
                                                  <span class="pull-left" style="margin-right: 10px">Completed:</span>
                                                  <input type="text" name="completed_date_tx180" class="form-control " value="<?php echo ( $check_res['completed_date_tx180'] ) ? date('m/d/Y', strtotime($check_res['completed_date_tx180'])): '' ; ?>" style="width: 124px;" disabled>
                                              </div>
                                            </div>
                                    </div>

                                    <div class="form-group">
                                            <label for="plan_review_270" class="col-sm-3 control-label"><?php echo xlt('270 Day:'); ?></label>
                                            <div class="col-sm-9">
                                              <input type="text" class="form-control plan_review_270 pull-left" name="plan_review_270" id="plan_review_270" value="<?php echo ($two_seventy) ? date('m/d/Y', strtotime($two_seventy)) : ''; ?>"  disabled style="width:150px; margin-right: 10px">
                                              <div class="date_completed">
                                                  <span class="pull-left" style="margin-right: 10px">Completed:</span>
                                                  <input type="text" name="completed_date_tx270" class="form-control " value="<?php echo ( $check_res['completed_date_tx270'] ) ? date('m/d/Y', strtotime($check_res['completed_date_tx270'])): '' ; ?>" style="width: 124px;" disabled>
                                              </div>
                                            </div>
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <h3><?php echo xlt('RISK ASSESSMENT '); ?><small><?php echo xlt('(mark all that apply)'); ?></small></h3>
                                    <div class="col-sm-4">
                                        <?php $risk_self_harm = explode('|', $check_res['risk_self_harm']); ?>
                                        <h4><?php echo xlt('SELF-HARM'); ?></h4>
                                        <?php  
                                            $self_harm_arr = array('Client Denies', 'Ideation', 'Intent', 'Reported Without Injury', 'Reported With Injury');

                                            foreach($self_harm_arr as $self_harm ):
                                        ?>
                                            <div class="checkbox">
                                              <label>
                                                <input type="checkbox" value="<?php echo text($self_harm); ?>" name="risk_self_harm[]" <?php echo (in_array($self_harm, $risk_self_harm)) ? 'checked': ''; ?> disabled>
                                                <?php echo xlt($self_harm); ?>
                                              </label>
                                            </div>
                                        <?php endforeach; ?>                                        

                                    </div>
                                    <div class="col-sm-4">
                                        <h4><?php echo xlt('SUICIDALITY'); ?></h4>
                                        <?php $risk_suicidality = explode('|', $check_res['risk_suicidality']) ; ?>
                                        <?php
                                            $sucidality_arr = array('Cient Denies', 'Ideation', 'Plan', 'Means', 'Prior Attempt');
                                            foreach($sucidality_arr as $suicidal):
                                        ?>
                                        <div class="checkbox">
                                          <label>
                                            <input type="checkbox" value="<?php echo text($suicidal); ?>" name="risk_suicidality[]"  <?php echo (in_array($suicidal, $risk_suicidality)) ? 'checked': ''; ?> disabled>
                                            <?php echo xlt($suicidal); ?>
                                          </label>
                                        </div>
                                        <?php endforeach; ?>
                                        

                                    </div>
                                    <div class="col-sm-4">
                                        <h4><?php echo xlt('HOMICIDALITY'); ?></h4>
                                        <?php $risk_homicidality = explode('|', $check_res['risk_homicidality']);  

                                            $homidical_arr = array('Cient Denies', 'Ideation', 'Plan', 'Means', 'Prior Attempt');
                                            foreach($homidical_arr as $homicidal):
                                        ?>
                                            <div class="checkbox">
                                              <label>
                                                <input type="checkbox" value="<?php echo text($homicidal); ?>" name="risk_homicidality[]" <?php echo (in_array($homicidal, $risk_homicidality)) ? 'checked': ''; ?> disabled>
                                                <?php echo xlt($homicidal); ?>
                                              </label>
                                            </div>
                                        <?php endforeach; ?>                                        
                                    </div>
                                </div>
                            </div>

                            <div class="clearfix"></div>
                            
                            <div class="col-md-12 brief_mental_status">
                                <h3><?php echo xlt('Brief Mental Status Exam / Symptoms'); ?> <small><?php echo xlt('(mark all that apply)'); ?></small></h3>
                                <div class="col-sm-6">
                                    <div class="col-sm-4">
                                        <h4><?php echo xlt('Orientation'); ?></h4>
                                        <?php $symptoms_orientation = explode('|', $check_res['symptoms_orientation']); ?>
                                        <ul style="list-style-type: none; padding: 0">
                                            <?php  
                                                $orientation_arr = array('Time', 'Person', 'Place', 'Situation');
                                                foreach($orientation_arr as $orientation):
                                            ?>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_orientation_<?php echo $orientation; ?>" name="symptoms_orientation[]" value="<?php echo $orientation; ?>" <?php echo (in_array($orientation, $symptoms_orientation)) ? 'checked':''; ?> disabled > <?php echo xlt(ucwords($orientation)); ?>
                                                </label>
                                            </li>
                                            <?php endforeach; ?>

                                        </ul>
                                    </div>
                                    <div class="col-sm-4">
                                        <h4><?php echo xlt('Speech Rate / Volume'); ?></h4>
                                        <?php $symptoms_speech =  explode('|', $check_res['symptoms_speech']); ?>

                                        <ul style="list-style-type: none; padding: 0">
                                            <?php 
                                                $speech_arr = array('Slow', 'Rapid', 'Loud', 'Soft', 'Pauses', 'WNL');
                                                foreach($speech_arr as $speech):
                                             ?>
                                            <li>
                                                <label >
                                                  <input type="checkbox" id="symptoms_speech_<?php echo $speech; ?>" name="symptoms_speech[]" value="<?php echo $speech; ?>" <?php echo (in_array($speech, $symptoms_speech)) ? 'checked': ''; ?> disabled > <?php echo xlt($speech); ?>
                                                </label>
                                            </li>
                                            <?php endforeach; ?>                                            
                                        </ul>
                                    </div>
                                    <div class="col-sm-4">
                                        <h4><?php echo xlt('Mood'); ?></h4>
                                        <?php 
                                            $symptoms_mood = explode('|', $check_res['symptoms_mood']); 
                                            $mood_arr = array('Calm', 'Apathetic', 'Anxious', 'Angry', 'Distraught', 'Cheerful', 'Despodent/Sad', 'Irritable', 'Hopeless', 'Other:');
                                            foreach($mood_arr as $mood):
                                        ?>
                                        <label class="">
                                          <input type="checkbox"  name="symptoms_mood[]" value="<?php echo $mood; ?>" <?php echo (in_array($mood, $symptoms_mood)) ? 'checked': '';  ?>  disabled> <?php echo xlt($mood); ?>
                                        </label>
                                        <?php endforeach; ?>                                        
                                        <input type="text" name="symptoms_mood_other" value="<?php echo  text($check_res['symptoms_mood_other']); ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <h4><?php echo xlt('Thought Content'); ?></h4>
                                    <?php $symptoms_thought_content = explode('|', $check_res['symptoms_thought_content']); ?>
                                    <ul style="list-style-type: none; columns: 2;-webkit-columns: 2;  -moz-columns: 2;">
                                        <?php  
                                            $thought_arr = array('Appropriate', 'Ruminating', 'Worry', 'Self-Harm', 'Irrational', 'Guilt', 'Shame', 'Obsessions/Compulsions', 'Self-Worth', 'Fears/Phobias', 'Self-Confidence', 'Self-Esteem');
                                            foreach($thought_arr as $thought):
                                        ?>
                                        <li>
                                            <label class="">
                                              <input type="checkbox"  name="symptoms_thought_content[]" value="<?php echo $thought; ?>" <?php echo (in_array($thought, $symptoms_thought_content)) ? 'checked': ''; ?> disabled> <?php echo xlt($thought); ?>
                                            </label>
                                        </li>
                                        <?php endforeach; ?>                                        
                                        <li>
                                            <label class="">
                                              <input type="checkbox" id="symptoms_thought_content_other" name="symptoms_thought_content[]" value="Other" <?php echo (in_array('Other', $symptoms_thought_content)) ? 'checked': ''; ?> disabled> <?php echo xlt('Other'); ?>
                                            </label>
                                            <input type="text" name="symptoms_thought_content_other" value="<?php echo text($check_res['symptoms_thought_content_other']); ?>" disabled>
                                        </li>
                                    </ul>
                                </div>

                                <div class="clearfix"></div>

                                <div class="col-sm-6">
                                    <div class="col-sm-4" style="padding-left:5px; padding-right: 5px">
                                        <h4><?php echo xlt('Hygiene/Grooming'); ?></h4>
                                        <?php $symptoms_hygiene = explode('|', $check_res['symptoms_hygiene']); ?>
                                        <ul style="list-style-type: none; padding: 0">
                                            <?php
                                                $hygiene_arr = array('Dishelved', 'Poor Hygiene', 'Appropriate', 'Neat');
                                                foreach($hygiene_arr as $hygiene):
                                            ?>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" name="symptoms_hygiene[]" value="<?php echo text($hygiene); ?>" <?php echo (in_array($hygiene, $symptoms_hygiene)) ? 'checked':''; ?> disabled> <?php echo xlt($hygiene); ?>
                                                </label>
                                            </li>
                                            <?php endforeach; ?>                                            
                                        </ul>
                                    </div>
                                    <div class="col-sm-4">
                                        <h4><?php echo xlt('Motor Activity'); ?></h4>
                                        <?php $symptoms_motor  = explode('|', $check_res['symptoms_motor']); ?>
                                        <ul style="list-style-type: none; padding: 0">
                                            <?php
                                                $motor_arr = array('Normal', 'Decreased', 'Increased', 'Restless');
                                                foreach($motor_arr as $motor):
                                            ?>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox"  name="symptoms_motor[]" value="<?php echo text($motor); ?>" <?php echo (in_array($motor, $symptoms_motor)) ? 'checked': ''; ?> disabled> <?php echo xlt($motor); ?>
                                                </label>
                                            </li>
                                            <?php endforeach; ?>                                            
                                        </ul>
                                    </div>
                                    <div class="col-sm-4" style="padding-left: 5px; padding-right: 5px">
                                        <h4><?php echo xlt('Affect'); ?></h4>
                                        <?php $symptoms_affect  = explode('|', $check_res['symptoms_affect']); ?>
                                        <ul style="list-style-type: none; padding: 0">
                                            <?php
                                                $affect_arr = array('Congruent to Mood', 'Hostile', 'Agitated', 'Labile', 'Inappropriate', 'Blunted', 'Expansive', 'Tearful', 'Flat');
                                                foreach($affect_arr as $affect):
                                            ?>
                                                <li>
                                                    <label class="">
                                                      <input type="checkbox"  name="symptoms_affect[]" value="<?php echo text($affect); ?>" <?php echo (in_array($affect, $symptoms_affect)) ? 'checked' : ''; ?> disabled> <?php echo xlt($affect); ?>
                                                    </label>
                                                </li>
                                            <?php endforeach; ?>                                            
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_affect_other" name="symptoms_affect[]" value="Other" <?php echo (in_array('Other', $symptoms_affect)) ? 'checked' : ''; ?> disabled> <?php echo xlt('Other'); ?>
                                                </label>    
                                                <input type="text" name="symptoms_affect_other" value="<?php echo text($check_res['symptoms_affect_other']); ?>" disabled>                                            
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="col-sm-6">
                                        <h4><?php echo xlt('Perception'); ?></h4>
                                        <?php $symptoms_perception  = explode('|', $check_res['symptoms_perception']); ?>
                                        <ul style="list-style-type: none; padding: 0">
                                            <?php
                                                $perception_arr = array('Appropriate', 'Distorted', 'Delusions', 'Paranoid', 'Grandiose', 'Bizarre', 'Hallucinations', 'Auditory', 'Visual', 'Olfactory');
                                                foreach($perception_arr as $perception):
                                            ?>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox"  name="symptoms_perception[]" value="<?php echo text($perception); ?>" <?php echo (in_array($perception, $symptoms_perception)) ? 'checked' : ''; ?> disabled> <?php echo xlt($perception); ?>
                                                </label>
                                            </li>
                                            <?php endforeach; ?>                                           
                                        </ul>
                                    </div>
                                    <div class="col-sm-6">
                                        <h4><?php echo xlt('Thought Process'); ?></h4>
                                        <?php $symptoms_thought_process  = explode('|', $check_res['symptoms_thought_process']); ?>
                                        <ul style="list-style-type: none; padding: 0">
                                            <?php
                                                $thought_process_arr = array('Logical/Coherent', 'Vague', 'Disorganized', 'Incoherent', 'Repeated Thought', 'Bizarre', 'Delayed', 'Tangential');
                                                foreach($thought_process_arr as $thought_process):
                                            ?>
                                                <li>
                                                    <label class="">
                                                      <input type="checkbox"  name="symptoms_thought_process[]" value="<?php echo text($thought_process); ?>" <?php echo (in_array($thought_process, $symptoms_thought_process)) ? 'checked':''; ?> disabled> <?php echo xlt($thought_process); ?>
                                                    </label>
                                                </li>
                                            <?php endforeach; ?>                                            
                                        </ul>
                                    </div>


                                </div>

                                <div class="clearfix"></div>

                                <div class="col-sm-12 row_other">
                                        <h4><?php echo xlt('Other'); ?></h4>
                                        <?php $symptoms_other  = explode('|', $check_res['symptoms_other']); ?>
                                        <ul style="list-style-type: none; columns: 4;-webkit-columns: 4;  -moz-columns: 4;">
                                            <?php 
                                                $others_arr = array('Appetite Change', 'Insomnia', 'Hypersomnia', 'Energy', 'Nightmares', 'Motivation', 'Mania', 'Disordered Eating', 'Physical Pain', 'Flashbacks', 'Poor Impulse Control', 'Substance Use', 'Illegal Conduct', 'Relationship Problems', 'Vocational/School Problems', 'Sexual Concerns', 'Concentration', 'Social Problems', 'Memory Loss/Problems', 'Medical Problems');

                                                foreach($others_arr as $others):
                                             ?>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" name="symptoms_other[]" value="<?php echo text($others); ?>" <?php echo (in_array($others, $symptoms_other)) ? 'checked': '';  ?> disabled> <?php echo xlt($others); ?>
                                                </label>
                                            </li>
                                            <?php endforeach; ?>

                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_other_other" name="symptoms_other[]" value="Other" <?php echo (in_array('Other', $symptoms_other)) ? 'checked': '';  ?> disabled> <?php echo xlt('Other'); ?>
                                                </label>
                                                <input type="text" name="symptoms_other_other" value="<?php echo  text($check_res['symptoms_other_other']); ?>" disabled>
                                            </li>
                                        </ul>
                                    </div>

                            </div>

                            
                                    

                            <div class="clearfix"></div>

                            <div class="col-md-12 session-focus" style="padding-top: 30px">
                                <div class="form-group">
                                    <label for="translator_used" class="control-label col-md-4">Translator Used</label>
                                    <div class="col-md-6">
                                        <input type="text" name="translator_used" id="translator_used" class="form-control" disabled value="<?php echo ($check_res['translator_used']) ? text($check_res['translator_used']) : text($last_record['translator_used']); ?>">
                                    </div>  
                                    <div class="clearfix"></div>                                  
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div class="col-md-12 margin-top-20  " >
                                <h3><?php echo xlt('Session Focus and Interventions'); ?></h3>
                                <p><?php echo xlt('(clinical assessment, session focus, treatment interventions; collateral contact, psycho-educational activities, homework assignments, treatment plan update and review, other):'); ?></p>
                                <textarea name="session_focus" id="session_focus" rows="4" class="form-control" disabled><?php echo text($check_res['session_focus']); ?></textarea>
                                <small class="text-danger session_focus_error"></small>
                            </div>

                            <div class="clearfix"></div>

                            <div class="col-md-12 margin-top-20" style="margin-top: 20px">
                                <div class="form-group">
                                    <label for="" class="col-sm-3 "><?php echo xlt('Next Appointment: '); ?></label>
                                    <div class="col-sm-3">
                                        <span class="col-sm-3"><?php echo xlt('Date:'); ?> </span>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control " name="meet_again_date" id="meet_again_date" value="<?php echo ( isset($check_res['meet_again_date']) && $check_res['meet_again_date'] ) ? date('m/d/Y', strtotime($check_res['meet_again_date'])):''; ?>" autocomplete="off" disabled>
                                            <small class="text-danger meet_again_date_error"></small>
                                        </div>                                        
                                    </div>

                                    <div class="col-sm-3">
                                        <span class="col-sm-3"><?php echo xlt('Time:'); ?> </span>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control " name="meet_again_time" id="meet_again_time" value="<?php echo text($check_res['meet_again_time']); ?>" autocomplete="off" disabled>
                                            <small class="text-danger meet_again_time_error"></small>
                                        </div>
                                    </div>                                     
                                </div>
                            </div>

                            <div class="clearfix">&nbsp;</div>
                            
                    </fieldset>

                    <div class="clearfix">&nbsp;</div>

                    <div class="form-group clearfix">
                        <div class="col-sm-12 col-sm-offset-1 position-override">
                            <div class="btn-group oe-opt-btn-group-pinch" role="group">
                                
                                <button type="button" class="btn btn-link btn-cancel oe-opt-btn-separate-left" onclick="form_close_tab()"><?php echo xlt('Cancel');?></button>
                                <a href="#" class="btn btn-default" id="print" style="margin-left: 18px">Print</a>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix">&nbsp;</div>
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

                $('.newDatePicker').datetimepicker({
                    timepicker:false,
                    format:'m/d/Y'
                });

                $("#print").on('click', function(){
                    $('.form_content').printThis({
                        debug: false,               // show the iframe for debugging
                        importCSS: true,            // import parent page css
                        importStyle: true,         // import style tags
                        printContainer: false,       // print outer container/$.selector
                        loadCSS: "",                // path to additional css file - use an array [] for multiple
                        pageTitle: "Counselor Progress Note",              // add title to print page
                        removeInline: false,        // remove inline styles from print elements
                        removeInlineSelector: "*",  // custom selectors to filter inline styles. removeInline must be true
                        printDelay: 333,            // variable print delay
                        header: "<h2>Counselor Progress Note</h2>",               // prefix to html
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
    </body>
</html>
