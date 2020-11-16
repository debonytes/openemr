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
require_once("$srcdir/api.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Core\Header;

$folderName = 'counselor_progress_note';
$tableName = 'form_' . $folderName;


$returnurl = 'encounter_top.php';
$formid = 0 + (isset($_GET['id']) ? $_GET['id'] : 0);
$check_res = $formid ? formFetch($tableName, $formid) : array();
?>
<html>
    <head>
        <title><?php echo xlt("Counselor Progress Note"); ?></title>

        <?php Header::setupHeader(['datetime-picker', 'opener']); ?>
        <link rel="stylesheet" href="<?php echo $web_root; ?>/library/css/bootstrap-timepicker.min.css">
        <link rel="stylesheet" href="../../../style_custom.css">
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
              $patient_fname = ( isset($patient['fname']) && $patient['fname'] ) ? $patient['fname'] : '';
              $patient_mname = ( isset($patient['mname']) && $patient['mname'] ) ? $patient['mname'] : '';
              $patient_lname = ( isset($patient['lname']) && $patient['lname'] ) ? $patient['lname'] : '';
              $patientInfo = array($patient_fname,$patient_mname,$patient_lname);
              if($patientInfo && array_filter($patientInfo)) {
                $patient_full_name = implode( ' ', array_filter($patientInfo) );
              }
            }

            

            ?>
            <div class="row">
                
                <form method="post" id="my_progress_notes_form" name="my_progress_notes_form" action="<?php echo $rootdir; ?>/forms/<?php echo $folderName; ?>/save.php?id=<?php echo attr_url($formid); ?>">          

                
                    <input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
                    <input type="hidden" name="pid" value="<?php echo $pid; ?>">
                    <input type="hidden" name="encounter" value="<?php echo $encounter; ?>">
                    <input type="hidden" name="user" value="<?php echo $user_id; ?>">
                    <input type="hidden" name="authorized" value="<?php echo $userauthorized; ?>">
                    <input type="hidden" name="activity" value="1">

                    <fieldset style="padding-top:20px!important">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="col-sm-3 "><?php echo xlt('Client Name'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text"  id="name" class="form-control" value="<?php echo text($patient_full_name); ?>" readonly>
                                        <input type="hidden" name="name" value="<?php echo text($patient_full_name); ?>" >
                                    </div>                                    
                                </div>
                                <div class="form-group">
                                    <label for="counselor" class="col-sm-3 "><?php echo xlt('Counselor'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="counselor" id="counselor" class="form-control" value="<?php echo text($check_res['counselor']); ?>">
                                        <small class="text-danger counselor_error"></small>
                                    </div>                                    
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-3 "><?php echo xlt('Location'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="location" id="location" class="form-control" value="<?php echo text($check_res['location']); ?>">
                                        <small class="text-danger location_error"></small>
                                    </div>                                    
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="col-sm-3 "><?php echo xlt('Date'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="date" id="date" class="form-control newDatePicker" value="<?php echo ( isset($check_res['date']) && $check_res['date'] ) ? date('m/d/Y', strtotime($check_res['date'])):''; ?>" autocomplete="off">
                                        <small class="text-danger date_error"></small>
                                    </div>                                    
                                </div>

                                <div class="form-group">
                                    <label for="starttime" class="col-sm-3 "><?php echo xlt('Start Time'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="starttime" id="starttime" class="form-control timepicker" value="<?php echo text($check_res['starttime']); ?>" autocomplete="off">
                                        <small class="text-danger starttime_error"></small>
                                    </div>                                    
                                </div>

                                <div class="form-group">
                                    <label for="endtime" class="col-sm-3 "><?php echo xlt('End Time'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="endtime" id="endtime" class="form-control timepicker" value="<?php echo text($check_res['endtime']); ?>" autocomplete="off">
                                        <small class="text-danger endtime_error"></small>
                                    </div>                                    
                                </div>

                                <div class="form-group">
                                    <label for="session_number" class="col-sm-3 "><?php echo xlt('Session Number'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" id="session_number" class="form-control" name="session_number" value="<?php echo text($check_res['session_number']); ?>">
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
                                                <input type="text" name="goal_1" class="form-control" value="<?php echo text($check_res['goal_1']); ?>">
                                            </td>
                                            <td class="text-center">
                                                <label class="text-center">
                                                  <input type="radio" name="goal_1_answer" id="goal_1_answer_radio_1" value="1" <?php echo ($check_res['goal_1_answer'] == 1) ? 'checked': '';  ?>>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="text-center">
                                                  <input type="radio" name="goal_1_answer" id="goal_1_answer_radio_2" value="2" <?php echo ($check_res['goal_1_answer'] == 2) ? 'checked': '';  ?>>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="text-center">
                                                  <input type="radio" name="goal_1_answer" id="goal_1_answer_radio_3" value="3" <?php echo ($check_res['goal_1_answer'] == 3) ? 'checked': '';  ?>>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="text-center">
                                                  <input type="radio" name="goal_1_answer" id="goal_1_answer_radio_4" value="4" <?php echo ($check_res['goal_1_answer'] == 4) ? 'checked': '';  ?>>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label class="text-center">
                                                  <input type="radio" name="goal_1_answer" id="goal_1_answer_radio_5" value="5" <?php echo ($check_res['goal_1_answer'] == 5) ? 'checked': '';  ?>>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>
                                                <input type="text" name="goal_2" class="form-control" value="<?php echo text($check_res['goal_2']); ?>">
                                            </td>
                                            <td class="text-center">
                                                <label >
                                                  <input type="radio" name="goal_2_answer" id="goal_2_answer_radio_1" value="1" <?php echo ($check_res['goal_2_answer'] == 1) ? 'checked': '';  ?>>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label >
                                                  <input type="radio" name="goal_2_answer" id="goal_2_answer_radio_2" value="2" <?php echo ($check_res['goal_2_answer'] == 2) ? 'checked': '';  ?>  >
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label >
                                                  <input type="radio" name="goal_2_answer" id="goal_2_answer_radio_3" value="3" <?php echo ($check_res['goal_2_answer'] == 3) ? 'checked': '';  ?> >
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label >
                                                  <input type="radio" name="goal_2_answer" id="goal_2_answer_radio_4" value="4" <?php echo ($check_res['goal_2_answer'] == 4) ? 'checked': '';  ?> >
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label >
                                                  <input type="radio" name="goal_2_answer" id="goal_2_answer_radio_5" value="5" <?php echo ($check_res['goal_2_answer'] == 5) ? 'checked': '';  ?> >
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>
                                                <input type="text" name="goal_3" class="form-control" value="<?php echo text($check_res['goal_3']); ?>">
                                            </td>
                                            <td class="text-center">
                                                <label >
                                                  <input type="radio" name="goal_3_answer" id="goal_3_answer_radio_1" value="1" <?php echo ($check_res['goal_3_answer'] == 1) ? 'checked': '';  ?> >
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label >
                                                  <input type="radio" name="goal_3_answer" id="goal_3_answer_radio_2" value="2" <?php echo ($check_res['goal_3_answer'] == 2) ? 'checked': '';  ?> >
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label >
                                                  <input type="radio" name="goal_3_answer" id="goal_3_answer_radio_3" value="3"  <?php echo ($check_res['goal_3_answer'] == 3) ? 'checked': '';  ?> >
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label >
                                                  <input type="radio" name="goal_3_answer" id="goal_3_answer_radio_4" value="4" <?php echo ($check_res['goal_3_answer'] == 4) ? 'checked': '';  ?> >
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label >
                                                  <input type="radio" name="goal_3_answer" id="goal_3_answer_radio_5" value="5" <?php echo ($check_res['goal_3_answer'] == 5) ? 'checked': '';  ?> >
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>
                                                <input type="text" name="goal_4" class="form-control" value="<?php echo text($check_res['goal_4']); ?>">
                                            </td>
                                            <td class="text-center">
                                                <label >
                                                  <input type="radio" name="goal_4_answer" id="goal_4_answer_radio_1" value="1" <?php echo ($check_res['goal_4_answer'] == 1) ? 'checked': '';  ?> >
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label >
                                                  <input type="radio" name="goal_4_answer" id="goal_4_answer_radio_2" value="2" <?php echo ($check_res['goal_4_answer'] == 2) ? 'checked': '';  ?> >
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label >
                                                  <input type="radio" name="goal_4_answer" id="goal_4_answer_radio_3" value="3" <?php echo ($check_res['goal_4_answer'] == 3) ? 'checked': '';  ?>  >
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label >
                                                  <input type="radio" name="goal_4_answer" id="goal_4_answer_radio_4" value="4"  <?php echo ($check_res['goal_4_answer'] == 4) ? 'checked': '';  ?> >
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <label >
                                                  <input type="radio" name="goal_4_answer" id="goal_4_answer_radio_5" value="5" <?php echo ($check_res['goal_4_answer'] == 5) ? 'checked': '';  ?> >
                                                </label>
                                            </td>
                                        </tr>                                        
                                    </tbody>
                                </table>
                            </div>

                            <div class="clearfix"></div>

                            <div class="col-md-12 margin-top-20">
                                <div class="col-md-6">
                                    <h3><?php echo xlt('TREATMENT & DIAGNOSTIC CODING'); ?></h3>
                                   
                                    <div class="form-group">
                                        <label for="icd_code" class="col-sm-4 control-label"><?php echo xlt('ICD-10 Code (s):'); ?> </label>                         
                                        <div class="col-sm-8">
                                          <input type="text" class="form-control" name="icd_code" id="icd_code" value="<?php echo text($check_res['icd_code']); ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="clearfix"></div>
                                    <div class="form-group">
                                        <label for="session_type" class="col-sm-4 control-label"><?php echo xlt('Session Type:'); ?> </label>                         
                                        <div class="col-sm-8">
                                          <input type="text" class="form-control" name="session_type" id="session_type" value="<?php echo text($check_res['session_type']); ?>">
                                        </div>
                                    </div>
                                   <div class="clearfix"></div>
                                    <div class="form-group">
                                            <label for="diagnosis" class="col-sm-4 control-label"><?php echo xlt('Diagnosis:'); ?> </label>                         
                                            <div class="col-sm-8">
                                              <input type="text" class="form-control" name="diagnosis" id="diagnosis" value="<?php echo text($check_res['diagnosis']); ?>">
                                            </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <h4><?php echo xlt('Tx Plan Review:'); ?></h4>

                                    <div class="form-group">
                                            <label for="plan_review_90" class="col-sm-2 control-label"><?php echo xlt('90 Day:'); ?> </label>
                                            <div class="col-sm-10">
                                              <input type="text" class="form-control" name="plan_review_90" id="plan_review_90" value="<?php echo text($check_res['plan_review_90']); ?>">
                                            </div>
                                    </div>

                                    <div class="form-group">
                                            <label for="plan_review_180" class="col-sm-2 control-label"><?php echo xlt('180 Day: '); ?></label>
                                            <div class="col-sm-10">
                                              <input type="text" class="form-control" name="plan_review_180" id="plan_review_180" value="<?php echo text($check_res['plan_review_180']); ?>">
                                            </div>
                                    </div>

                                    <div class="form-group">
                                            <label for="plan_review_270" class="col-sm-2 control-label"><?php echo xlt(''); ?>270 Day: </label>
                                            <div class="col-sm-10">
                                              <input type="text" class="form-control" name="plan_review_270" id="plan_review_270" value="<?php echo text($check_res['plan_review_270']); ?>">
                                            </div>
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <h3><?php echo xlt('RISK ASSESSMENT '); ?><small><?php echo xlt('(mark all that apply)'); ?></small></h3>
                                    <div class="col-sm-4">
                                        <?php $risk_self_harm = explode('|', $check_res['risk_self_harm']); ?>
                                        <h4><?php echo xlt('SELF-HARM'); ?></h4>
                                        <div class="checkbox">
                                          <label>
                                            <input type="checkbox" value="client_denies" name="risk_self_harm[]" <?php echo (in_array('client_denies', $risk_self_harm)) ? 'checked': ''; ?> >
                                            <?php echo xlt('Client Denies'); ?>
                                          </label>
                                        </div>
                                        <div class="checkbox">
                                          <label>
                                            <input type="checkbox" value="ideation" name="risk_self_harm[]" <?php echo (in_array('ideation', $risk_self_harm)) ? 'checked': ''; ?> >
                                            <?php echo xlt('Ideation'); ?>
                                          </label>
                                        </div>
                                        <div class="checkbox">
                                          <label>
                                            <input type="checkbox" value="intent" name="risk_self_harm[]" <?php echo (in_array('intent', $risk_self_harm)) ? 'checked': ''; ?> >
                                            <?php echo xlt('Intent'); ?>
                                          </label>
                                        </div>
                                        <div class="checkbox">
                                          <label>
                                            <input type="checkbox" value="without_injury" name="risk_self_harm[]" <?php echo (in_array('without_injury', $risk_self_harm)) ? 'checked': ''; ?> >
                                            <?php echo xlt('Reported Without Injury'); ?>
                                          </label>
                                        </div>
                                        <div class="checkbox">
                                          <label>
                                            <input type="checkbox" value="with_injury" name="risk_self_harm[]" <?php echo (in_array('with_injury', $risk_self_harm)) ? 'checked': ''; ?> >
                                            <?php echo xlt('Reported With Injury'); ?>
                                          </label>
                                        </div>

                                    </div>
                                    <div class="col-sm-4">
                                        <h4><?php echo xlt('SUICIDALITY'); ?></h4>
                                        <?php $risk_suicidality = explode('|', $check_res['risk_suicidality']); ?>
                                        <div class="checkbox">
                                          <label>
                                            <input type="checkbox" value="client_denies" name="risk_suicidality[]"  <?php echo (in_array('client_denies', $risk_suicidality)) ? 'checked': ''; ?> >
                                            <?php echo xlt('Cient Denies'); ?>
                                          </label>
                                        </div>
                                        <div class="checkbox">
                                          <label>
                                            <input type="checkbox" value="ideation" name="risk_suicidality[]" <?php echo (in_array('ideation', $risk_suicidality)) ? 'checked': ''; ?> >
                                            <?php echo xlt('Ideation'); ?>
                                          </label>
                                        </div>
                                        <div class="checkbox">
                                          <label>
                                            <input type="checkbox" value="plan" name="risk_suicidality[]" <?php echo (in_array('plan', $risk_suicidality)) ? 'checked': ''; ?> >
                                            <?php echo xlt('Plan'); ?>
                                          </label>
                                        </div>
                                        <div class="checkbox">
                                          <label>
                                            <input type="checkbox" value="means" name="risk_suicidality[]" <?php echo (in_array('means', $risk_suicidality)) ? 'checked': ''; ?> >
                                            <?php echo xlt('Means'); ?>
                                          </label>
                                        </div>
                                        <div class="checkbox">
                                          <label>
                                            <input type="checkbox" value="prior_attempt" name="risk_suicidality[]"  <?php echo (in_array('prior_attempt', $risk_suicidality)) ? 'checked': ''; ?> >
                                            <?php echo xlt('Prior Attempt'); ?>
                                          </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <h4><?php echo xlt('HOMICIDALITY'); ?></h4>
                                        <?php $risk_homicidality = explode('|', $check_res['risk_homicidality']);  ?>
                                        <div class="checkbox">
                                          <label>
                                            <input type="checkbox" value="client_denies" name="risk_homicidality[]" <?php echo (in_array('client_denies', $risk_homicidality)) ? 'checked': ''; ?> >
                                            <?php echo xlt('Cient Denies'); ?>
                                          </label>
                                        </div>
                                        <div class="checkbox">
                                          <label>
                                            <input type="checkbox" value="ideation" name="risk_homicidality[]" <?php echo (in_array('ideation', $risk_homicidality)) ? 'checked': ''; ?> >
                                            <?php echo xlt('Ideation'); ?>
                                          </label>
                                        </div>
                                        <div class="checkbox">
                                          <label>
                                            <input type="checkbox" value="plan" name="risk_homicidality[]" <?php echo (in_array('plan', $risk_homicidality)) ? 'checked': ''; ?> >
                                            <?php echo xlt('Plan'); ?>
                                          </label>
                                        </div>
                                        <div class="checkbox">
                                          <label>
                                            <input type="checkbox" value="means" name="risk_homicidality[]" <?php echo (in_array('means', $risk_homicidality)) ? 'checked': ''; ?> >
                                            <?php echo xlt('Means'); ?>
                                          </label>
                                        </div>
                                        <div class="checkbox">
                                          <label>
                                            <input type="checkbox" value="prior_attempt" name="risk_homicidality[]" <?php echo (in_array('prior_attempt', $risk_homicidality)) ? 'checked': ''; ?> >
                                            <?php echo xlt('Prior Attempt'); ?>
                                          </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="clearfix"></div>
                            
                            <div class="col-md-12">
                                <h3><?php echo xlt('Brief Mental Status Exam / Symptoms'); ?> <small><?php echo xlt('(mark all that apply)'); ?></small></h3>
                                <div class="col-sm-6">
                                    <div class="col-sm-4">
                                        <h4><?php echo xlt('Orientation'); ?></h4>
                                        <?php $symptoms_orientation = explode('|', $check_res['symptoms_orientation']); ?>
                                        <ul style="list-style-type: none; padding: 0">
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_orientation_time" name="symptoms_orientation[]" value="time" <?php echo (in_array('time', $symptoms_orientation)) ? 'checked':''; ?>  > <?php echo xlt('Time'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_orientation_person" name="symptoms_orientation[]" value="person" <?php echo (in_array('person', $symptoms_orientation)) ? 'checked':''; ?> > <?php echo xlt('Person'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_orientation_place" name="symptoms_orientation[]" value="place" <?php echo (in_array('place', $symptoms_orientation)) ? 'checked':''; ?> > <?php echo xlt('Place'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_orientation_situation" name="symptoms_orientation[]" value="situation" <?php echo (in_array('situation', $symptoms_orientation)) ? 'checked':''; ?> > <?php echo xlt('Situation'); ?>
                                                </label>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-sm-4">
                                        <h4><?php echo xlt('Speech Rate / Volume'); ?></h4>
                                        <?php $symptoms_speech =  explode('|', $check_res['symptoms_speech']); ?>

                                        <ul style="list-style-type: none; padding: 0">
                                            <li>
                                                <label >
                                                  <input type="checkbox" id="symptoms_speech_slow" name="symptoms_speech[]" value="slow" <?php echo (in_array('slow', $symptoms_speech)) ? 'checked': ''; ?>  > <?php echo xlt('Slow'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_speech_rapid" name="symptoms_speech[]" value="rapid" <?php echo (in_array('slow', $symptoms_speech)) ? 'checked': ''; ?> > <?php echo xlt('Rapid'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_speech_loud" name="symptoms_speech[]" value="loud" <?php echo (in_array('loud', $symptoms_speech)) ? 'checked': ''; ?> > <?php echo xlt('Loud'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_speech_soft" name="symptoms_speech[]" value="soft" <?php echo (in_array('soft', $symptoms_speech)) ? 'checked': ''; ?> > <?php echo xlt('Soft'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_speech_pauses" name="symptoms_speech[]" value="pauses" <?php echo (in_array('pauses', $symptoms_speech)) ? 'checked': ''; ?> > <?php echo xlt('Pauses'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_speech_wnl" name="symptoms_speech[]" value="WNL" <?php echo (in_array('WNL', $symptoms_speech)) ? 'checked': ''; ?> > <?php echo xlt('WNL'); ?>
                                                </label>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-sm-4">
                                        <h4><?php echo xlt('Mood'); ?></h4>
                                        <?php $symptoms_mood = explode('|', $check_res['symptoms_mood']); ?>
                                        <label class="">
                                          <input type="checkbox" id="symptoms_mood_calm" name="symptoms_mood[]" value="calm" <?php echo (in_array('calm', $symptoms_mood)) ? 'checked': '';  ?>  > <?php echo xlt('Calm'); ?>
                                        </label>
                                        <label class="">
                                          <input type="checkbox" id="symptoms_mood_apathetic" name="symptoms_mood[]" value="apathetic" <?php echo (in_array('apathetic', $symptoms_mood)) ? 'checked': '';  ?> > <?php echo xlt('Apathetic'); ?>
                                        </label>
                                        <label class="">
                                          <input type="checkbox" id="symptoms_mood_anxious" name="symptoms_mood[]" value="anxious" <?php echo (in_array('anxious', $symptoms_mood)) ? 'checked': '';  ?> > <?php echo xlt('Anxious'); ?>
                                        </label>
                                        <label class="">
                                          <input type="checkbox" id="symptoms_mood_angry" name="symptoms_mood[]" value="angry" <?php echo (in_array('angry', $symptoms_mood)) ? 'checked': '';  ?> > <?php echo xlt('Angry'); ?>
                                        </label>
                                        <label class="">
                                          <input type="checkbox" id="symptoms_mood_distraught" name="symptoms_mood[]" value="distraught" <?php echo (in_array('distraught', $symptoms_mood)) ? 'checked': '';  ?>  > <?php echo xlt('Distraught'); ?>
                                        </label>
                                        <label class="">
                                          <input type="checkbox" id="symptoms_mood_cheerful" name="symptoms_mood[]" value="cheerful" <?php echo (in_array('cheerful', $symptoms_mood)) ? 'checked': '';  ?> > <?php echo xlt('Cheerful'); ?>
                                        </label>
                                        <label class="">
                                          <input type="checkbox" id="symptoms_mood_sad" name="symptoms_mood[]" value="sad" <?php echo (in_array('sad', $symptoms_mood)) ? 'checked': '';  ?> > <?php echo xlt('Despodent/Sad'); ?>
                                        </label>
                                        <label class="">
                                          <input type="checkbox" id="symptoms_mood_irritable" name="symptoms_mood[]" value="irritable" <?php echo (in_array('irritable', $symptoms_mood)) ? 'checked': '';  ?> > <?php echo xlt('Irritable'); ?>
                                        </label>
                                        <label class="">
                                          <input type="checkbox" id="symptoms_mood_hopeless" name="symptoms_mood[]" value="hopeless" <?php echo (in_array('hopeless', $symptoms_mood)) ? 'checked': '';  ?> > <?php echo xlt('Hopeless'); ?>
                                        </label>
                                        <label class="">
                                          <input type="checkbox" id="symptoms_mood_other" name="symptoms_mood[]" value="other" <?php echo (in_array('other', $symptoms_mood)) ? 'checked': '';  ?> > <?php echo xlt('Other:'); ?>
                                        </label>
                                        <input type="text" name="symptoms_mood_other" value="<?php echo text($check_res['symptoms_mood_other']); ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <h4><?php echo xlt('Thought Content'); ?></h4>
                                    <?php $symptoms_thought_content = explode('|', $check_res['symptoms_thought_content']); ?>
                                    <ul style="list-style-type: none; columns: 2;-webkit-columns: 2;  -moz-columns: 2;">
                                        <li>
                                            <label class="">
                                              <input type="checkbox" id="symptoms_thought_content_appropriate" name="symptoms_thought_content[]" value="Appropriate" <?php echo (in_array('Appropriate', $symptoms_thought_content)) ? 'checked': ''; ?> > <?php echo xlt('Appropriate'); ?>
                                            </label>
                                        </li>
                                        <li>
                                            <label class="">
                                              <input type="checkbox" id="symptoms_thought_content_ruminating" name="symptoms_thought_content[]" value="Ruminating" <?php echo (in_array('Ruminating', $symptoms_thought_content)) ? 'checked': ''; ?> > <?php echo xlt('Ruminating'); ?>
                                            </label>
                                        </li>
                                        <li>
                                            <label class="">
                                              <input type="checkbox" id="symptoms_thought_content_worry" name="symptoms_thought_content[]" value="Worry" <?php echo (in_array('Worry', $symptoms_thought_content)) ? 'checked': ''; ?>  > <?php echo xlt('Worry'); ?>
                                            </label>
                                        </li>
                                        <li>
                                            <label class="">
                                              <input type="checkbox" id="symptoms_thought_content_self_harm" name="symptoms_thought_content[]" value="Self-Harm" <?php echo (in_array('Self-Harm', $symptoms_thought_content)) ? 'checked': ''; ?> > <?php echo xlt('Self-Harm'); ?>
                                            </label>
                                        </li>
                                        <li>
                                            <label class="">
                                              <input type="checkbox" id="symptoms_thought_content_irrational" name="symptoms_thought_content[]" value="Irrational" <?php echo (in_array('Irrational', $symptoms_thought_content)) ? 'checked': ''; ?> > <?php echo xlt('Irrational'); ?>
                                            </label>
                                        </li>
                                        <li>
                                            <label class="">
                                              <input type="checkbox" id="symptoms_thought_content_guilt" name="symptoms_thought_content[]" value="Guilt" <?php echo (in_array('Guilt', $symptoms_thought_content)) ? 'checked': ''; ?>  > <?php echo xlt('Guilt'); ?>
                                            </label>
                                        </li>
                                        <li>
                                            <label class="">
                                              <input type="checkbox" id="symptoms_thought_content_shame" name="symptoms_thought_content[]" value="Shame" <?php echo (in_array('Shame', $symptoms_thought_content)) ? 'checked': ''; ?> > <?php echo xlt('Shame'); ?>
                                            </label>
                                        </li>
                                        <li>
                                            <label class="">
                                              <input type="checkbox" id="symptoms_thought_content_obsessions" name="symptoms_thought_content[]" value="Obsessions" <?php echo (in_array('Obsessions', $symptoms_thought_content)) ? 'checked': ''; ?> > <?php echo xlt('Obsessions/Compulsions'); ?>
                                            </label>
                                        </li>
                                        <li>
                                            <label class="">
                                              <input type="checkbox" id="symptoms_thought_content_self_worth" name="symptoms_thought_content[]" value="Self-Worth" <?php echo (in_array('Self-Worth', $symptoms_thought_content)) ? 'checked': ''; ?> > <?php echo xlt('Self-Worth'); ?>
                                            </label>
                                        </li>
                                        <li>
                                            <label class="">
                                              <input type="checkbox" id="symptoms_thought_content_fears" name="symptoms_thought_content[]" value="Fears" <?php echo (in_array('Fears', $symptoms_thought_content)) ? 'checked': ''; ?> > <?php echo xlt('Fears/Phobias'); ?>
                                            </label>
                                        </li>
                                        <li>
                                            <label class="">
                                              <input type="checkbox" id="symptoms_thought_content_self_confidence" name="symptoms_thought_content[]" value="Self-Confidence" <?php echo (in_array('Self-Confidence', $symptoms_thought_content)) ? 'checked': ''; ?> > <?php echo xlt('Self-Confidence'); ?>
                                            </label>
                                        </li>
                                        <li>
                                            <label class="">
                                              <input type="checkbox" id="symptoms_thought_content_self_esteem" name="symptoms_thought_content[]" value="Self-Esteem" <?php echo (in_array('Self-Esteem', $symptoms_thought_content)) ? 'checked': ''; ?> > <?php echo xlt('Self-Esteem'); ?>
                                            </label>
                                        </li>
                                        <li>
                                            <label class="">
                                              <input type="checkbox" id="symptoms_thought_content_other" name="symptoms_thought_content[]" value="Other" <?php echo (in_array('Other', $symptoms_thought_content)) ? 'checked': ''; ?> > <?php echo xlt('Other'); ?>
                                            </label>
                                            <input type="text" name="symptoms_thought_content_other" value="<?php echo text($check_res['symptoms_thought_content_other']); ?>" >
                                        </li>
                                    </ul>
                                </div>

                                <div class="clearfix"></div>

                                <div class="col-sm-6">
                                    <div class="col-sm-4" style="padding-left:5px; padding-right: 5px">
                                        <h4><?php echo xlt('Hygiene/Grooming'); ?></h4>
                                        <?php $symptoms_hygiene = explode('|', $check_res['symptoms_hygiene']); ?>
                                        <ul style="list-style-type: none; padding: 0">
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_hygiene_dishelved" name="symptoms_hygiene[]" value="Dishelved" <?php echo (in_array('Dishelved', $symptoms_hygiene)) ? 'checked':''; ?> > <?php echo xlt('Dishelved'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_hygiene_poor_hygiene" name="symptoms_hygiene[]" value="Poor-Hygiene" <?php echo (in_array('Poor-Hygiene', $symptoms_hygiene)) ? 'checked':''; ?> > <?php echo xlt('Poor Hygiene'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_hygiene_appropriate" name="symptoms_hygiene[]" value="Appropriate" <?php echo (in_array('Appropriate', $symptoms_hygiene)) ? 'checked':''; ?> > <?php echo xlt('Appropriate'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_hygiene_neat" name="symptoms_hygiene[]" value="Neat" <?php echo (in_array('Neat', $symptoms_hygiene)) ? 'checked':''; ?> > <?php echo xlt('Neat'); ?>
                                                </label>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-sm-4">
                                        <h4><?php echo xlt('Motor Activity'); ?></h4>
                                        <?php $symptoms_motor  = explode('|', $check_res['symptoms_motor']); ?>
                                        <ul style="list-style-type: none; padding: 0">
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_motor_normal" name="symptoms_motor[]" value="Normal" <?php echo (in_array('Normal', $symptoms_motor)) ? 'checked': ''; ?> > <?php echo xlt('Normal'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_motor_decreased" name="symptoms_motor[]" value="Decreased" <?php echo (in_array('Decreased', $symptoms_motor)) ? 'checked': ''; ?>  > <?php echo xlt('Decreased'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_motor_increased" name="symptoms_motor[]" value="Increased" <?php echo (in_array('Increased', $symptoms_motor)) ? 'checked': ''; ?> > <?php echo xlt('Increased'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_motor_restless" name="symptoms_motor[]" value="Restless" <?php echo (in_array('Restless', $symptoms_motor)) ? 'checked': ''; ?> > <?php echo xlt('Restless'); ?>
                                                </label>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-sm-4" style="padding-left: 5px; padding-right: 5px">
                                        <h4><?php echo xlt('Affect'); ?></h4>
                                        <?php $symptoms_affect  = explode('|', $check_res['symptoms_affect']); ?>
                                        <ul style="list-style-type: none; padding: 0">
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_affect_congruent" name="symptoms_affect[]" value="Congruent" <?php echo (in_array('Congruent', $symptoms_affect)) ? 'checked' : ''; ?> > <?php echo xlt('Congruent to Mood'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_affect_hostile" name="symptoms_affect[]" value="Hostile" <?php echo (in_array('Hostile', $symptoms_affect)) ? 'checked' : ''; ?> > <?php echo xlt('Hostile'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_affect_agitated" name="symptoms_affect[]" value="Agitated" <?php echo (in_array('Agitated', $symptoms_affect)) ? 'checked' : ''; ?> > <?php echo xlt('Agitated'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_affect_labile" name="symptoms_affect[]" value="Labile" <?php echo (in_array('Labile', $symptoms_affect)) ? 'checked' : ''; ?> > <?php echo xlt('Labile'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_affect_inappropriate" name="symptoms_affect[]" value="Inappropriate" <?php echo (in_array('Inappropriate', $symptoms_affect)) ? 'checked' : ''; ?>  > <?php echo xlt('Inappropriate'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_affect_blunted" name="symptoms_affect[]" value="Blunted" <?php echo (in_array('Blunted', $symptoms_affect)) ? 'checked' : ''; ?> > <?php echo xlt('Blunted'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_affect_expansive" name="symptoms_affect[]" value="Expansive" <?php echo (in_array('Expansive', $symptoms_affect)) ? 'checked' : ''; ?> > <?php echo xlt('Expansive'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_affect_tearful" name="symptoms_affect[]" value="Tearful" <?php echo (in_array('Tearful', $symptoms_affect)) ? 'checked' : ''; ?> > <?php echo xlt('Tearful'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_affect_flat" name="symptoms_affect[]" value="Flat" <?php echo (in_array('Flat', $symptoms_affect)) ? 'checked' : ''; ?> > <?php echo xlt('Flat'); ?>
                                                </label>                                                
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_affect_other" name="symptoms_affect[]" value="Other" <?php echo (in_array('Other', $symptoms_affect)) ? 'checked' : ''; ?> > <?php echo xlt('Other'); ?>
                                                </label>    
                                                <input type="text" name="symptoms_affect_other" value="<?php echo xlt($check_res['symptoms_affect_other']); ?>" >                                            
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="col-sm-6">
                                        <h4><?php echo xlt('Perception'); ?></h4>
                                        <?php $symptoms_perception  = explode('|', $check_res['symptoms_perception']); ?>
                                        <ul style="list-style-type: none; padding: 0">
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_perception_appropriate" name="symptoms_perception[]" value="Appropriate" <?php echo (in_array('Appropriate', $symptoms_perception)) ? 'checked' : ''; ?> > <?php echo xlt('Appropriate'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_perception_distorted" name="symptoms_perception[]" value="Distorted" <?php echo (in_array('Distorted', $symptoms_perception)) ? 'checked' : ''; ?> > <?php echo xlt('Distorted'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_perception_delusions" name="symptoms_perception[]" value="Delusions" <?php echo (in_array('Delusions', $symptoms_perception)) ? 'checked' : ''; ?> > <?php echo xlt('Delusions'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_perception_paranoid" name="symptoms_perception[]" value="Paranoid" <?php echo (in_array('Paranoid', $symptoms_perception)) ? 'checked' : ''; ?> > <?php echo xlt('Paranoid'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_perception_grandiose" name="symptoms_perception[]" value="Grandiose" <?php echo (in_array('Grandiose', $symptoms_perception)) ? 'checked' : ''; ?> > <?php echo xlt('Grandiose'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_perception_bizarre" name="symptoms_perception[]" value="Bizarre" <?php echo (in_array('Bizarre', $symptoms_perception)) ? 'checked' : ''; ?> > <?php echo xlt('Bizarre'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_perception_hallucinations" name="symptoms_perception[]" value="Hallucinations" <?php echo (in_array('Hallucinations', $symptoms_perception)) ? 'checked' : ''; ?> > <?php echo xlt('Hallucinations'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_perception_auditory" name="symptoms_perception[]" value="Auditory" <?php echo (in_array('Auditory', $symptoms_perception)) ? 'checked' : ''; ?>  > <?php echo xlt('Auditory'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_perception_visual" name="symptoms_perception[]" value="Visual" <?php echo (in_array('Visual', $symptoms_perception)) ? 'checked' : ''; ?> > <?php echo xlt('Visual'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_perception_olfactory" name="symptoms_perception[]" value="Olfactory" <?php echo (in_array('Olfactory', $symptoms_perception)) ? 'checked' : ''; ?> > <?php echo xlt('Olfactory'); ?>
                                                </label>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-sm-6">
                                        <h4><?php echo xlt('Thought Process'); ?></h4>
                                        <?php $symptoms_thought_process  = explode('|', $check_res['symptoms_thought_process']); ?>
                                        <ul style="list-style-type: none; padding: 0">
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_thought_process_logical" name="symptoms_thought_process[]" value="Logical" <?php echo (in_array('Logical', $symptoms_thought_process)) ? 'checked':''; ?> > <?php echo xlt('Logical/Coherent'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_thought_process_vague" name="symptoms_thought_process[]" value="Vague" <?php echo (in_array('Vague', $symptoms_thought_process)) ? 'checked':''; ?> > <?php echo xlt('Vague'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_thought_process_disorganized" name="symptoms_thought_process[]" value="Disorganized" <?php echo (in_array('Disorganized', $symptoms_thought_process)) ? 'checked':''; ?> > <?php echo xlt('Disorganized'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_thought_process_incoherent" name="symptoms_thought_process[]" value="Incoherent" <?php echo (in_array('Incoherent', $symptoms_thought_process)) ? 'checked':''; ?> > <?php echo xlt('Incoherent'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_thought_process_repeated_thought" name="symptoms_thought_process[]" value="Repeated Thought" <?php echo (in_array('Repeated Thought', $symptoms_thought_process)) ? 'checked':''; ?> > <?php echo xlt('Repeated Thought'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_thought_process_bizarre" name="symptoms_thought_process[]" value="Bizarre" <?php echo (in_array('Bizarre', $symptoms_thought_process)) ? 'checked':''; ?> > <?php echo xlt('Bizarre'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_thought_process_delayed" name="symptoms_thought_process[]" value="Delayed" <?php echo (in_array('Delayed', $symptoms_thought_process)) ? 'checked':''; ?> > <?php echo xlt('Delayed'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_thought_process_tangential" name="symptoms_thought_process[]" value="Tangential" <?php echo (in_array('Tangential', $symptoms_thought_process)) ? 'checked':''; ?> > <?php echo xlt('Tangential'); ?>
                                                </label>
                                            </li>
                                        </ul>
                                    </div>


                                </div>

                                <div class="clearfix"></div>

                                <div class="col-sm-12">
                                        <h4><?php echo xlt('Other'); ?></h4>
                                        <?php $symptoms_other  = explode('|', $check_res['symptoms_other']); ?>
                                        <ul style="list-style-type: none; columns: 4;-webkit-columns: 4;  -moz-columns: 4;">
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_other_appetite_change" name="symptoms_other[]" value="Appetite Change" <?php echo (in_array('Appetite Change', $symptoms_other)) ? 'checked': '';  ?> > <?php echo xlt('Appetite Change'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_other_insomnia" name="symptoms_other[]" value="Insomnia" <?php echo (in_array('Insomnia', $symptoms_other)) ? 'checked': '';  ?> > <?php echo xlt('Insomnia'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_other_hypersomia" name="symptoms_other[]" value="Hypersomnia"  <?php echo (in_array('Hypersomnia', $symptoms_other)) ? 'checked': '';  ?> > <?php echo xlt('Hypersomnia'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_other_energy" name="symptoms_other[]" value="Energy" <?php echo (in_array('Energy', $symptoms_other)) ? 'checked': '';  ?> > <?php echo xlt('Energy'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_other_nightmares" name="symptoms_other[]" value="Nightmares" <?php echo (in_array('Nightmares', $symptoms_other)) ? 'checked': '';  ?> > <?php echo xlt('Nightmares'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_other_motivation" name="symptoms_other[]" value="Motivation" <?php echo (in_array('Motivation', $symptoms_other)) ? 'checked': '';  ?> > <?php echo xlt('Motivation'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_other_mania" name="symptoms_other[]" value="Mania" <?php echo (in_array('Mania', $symptoms_other)) ? 'checked': '';  ?> > <?php echo xlt('Mania'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_other_disordered_eating" name="symptoms_other[]" value="Disordered Eating" <?php echo (in_array('Disordered Eating', $symptoms_other)) ? 'checked': '';  ?> > <?php echo xlt('Disordered Eating'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_other_physical_pain" name="symptoms_other[]" value="Physical Pain" <?php echo (in_array('Physical Pain', $symptoms_other)) ? 'checked': '';  ?> > <?php echo xlt('Physical Pain'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_other_flashbacks" name="symptoms_other[]" value="Flashbacks" <?php echo (in_array('Flashbacks', $symptoms_other)) ? 'checked': '';  ?> > <?php echo xlt('Flashbacks'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_other_poor_impulse" name="symptoms_other[]" value="Poor Impulse Control" <?php echo (in_array('Poor Impulse Control', $symptoms_other)) ? 'checked': '';  ?> > <?php echo xlt('Poor Impulse Control'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_other_substance_use" name="symptoms_other[]" value="Substance Use" <?php echo (in_array('Substance Use', $symptoms_other)) ? 'checked': '';  ?> > <?php echo xlt('Substance Use'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_other_illegal_conduct" name="symptoms_other[]" value="Illegal Conduct" <?php echo (in_array('Illegal Conduct', $symptoms_other)) ? 'checked': '';  ?> > <?php echo xlt('Illegal Conduct'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_other_relationship_problem" name="symptoms_other[]" value="Relationship Problems" <?php echo (in_array('Relationship Problems', $symptoms_other)) ? 'checked': '';  ?> > <?php echo xlt('Relationship Problems'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_other_vocational_problem" name="symptoms_other[]" value="Vocational Problems" <?php echo (in_array('Vocational Problems', $symptoms_other)) ? 'checked': '';  ?> > <?php echo xlt('Vocational/School Problems'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_other_sexual_concerns" name="symptoms_other[]" value="Sexual Concerns" <?php echo (in_array('Sexual Concerns', $symptoms_other)) ? 'checked': '';  ?> > <?php echo xlt('Sexual Concerns'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_other_concentration" name="symptoms_other[]" value="Concentration" <?php echo (in_array('Concentration', $symptoms_other)) ? 'checked': '';  ?> > <?php echo xlt('Concentration'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_other_social_problems" name="symptoms_other[]" value="Social Problems" <?php echo (in_array('Social Problems', $symptoms_other)) ? 'checked': '';  ?> > <?php echo xlt('Social Problems'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_other_memory_loss" name="symptoms_other[]" value="Memory Loss" <?php echo (in_array('Memory Loss', $symptoms_other)) ? 'checked': '';  ?> > <?php echo xlt('Memory Loss/Problems'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_other_medical_problems" name="symptoms_other[]" value="Medical Problems" <?php echo (in_array('Medical Problems', $symptoms_other)) ? 'checked': '';  ?> > <?php echo xlt('Medical Problems'); ?>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="">
                                                  <input type="checkbox" id="symptoms_other_other" name="symptoms_other[]" value="Other" <?php echo (in_array('Other', $symptoms_other)) ? 'checked': '';  ?> > <?php echo xlt('Other'); ?>
                                                </label>
                                                <input type="text" name="symptoms_other_other" value="<?php echo text($check_res['symptoms_other_other']); ?>">
                                            </li>
                                        </ul>
                                    </div>

                            </div>

                            
                                    

                            <div class="clearfix"></div>

                            <div class="col-md-12 margin-top-20" style="margin-top: 30px">
                                <h3><?php echo xlt('Session Focus and Interventions'); ?></h3>
                                <p><?php echo xlt('(clinical assessment, session focus, treatment interventions; collateral contact, psycho-educational activities, homework assignments, treatment plan update and review, other):'); ?></p>
                                <textarea name="session_focus" id="session_focus" rows="4" class="form-control"><?php echo text($check_res['session_focus']); ?></textarea>
                                <small class="text-danger session_focus_error"></small>
                            </div>

                            <div class="clearfix"></div>

                            <div class="col-md-12 margin-top-20" style="margin-top: 20px">
                                <div class="form-group">
                                    <label for="" class="col-sm-3 "><?php echo xlt('Next Appointment: '); ?></label>
                                    <div class="col-sm-3">
                                        <span class="col-sm-3"><?php echo xlt('Date:'); ?> </span>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control newDatePicker" name="meet_again_date" id="meet_again_date" value="<?php echo ( isset($check_res['meet_again_date']) && $check_res['meet_again_date'] ) ? date('m/d/Y', strtotime($check_res['meet_again_date'])):''; ?>" autocomplete="off">
                                            <small class="text-danger meet_again_date_error"></small>
                                        </div>                                        
                                    </div>

                                    <div class="col-sm-3">
                                        <span class="col-sm-3"><?php echo xlt('Time:'); ?> </span>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control timepicker" name="meet_again_time" id="meet_again_time" value="<?php echo text($check_res['meet_again_time']); ?>" autocomplete="off">
                                            <small class="text-danger meet_again_time_error"></small>
                                        </div>
                                    </div>                                     
                                </div>
                            </div>

                            <div class="clearfix">&nbsp;</div>
                            
                    </fieldset>

                    

                    <div class="form-group clearfix">
                        <div class="col-sm-12 col-sm-offset-1 position-override">
                            <div class="btn-group oe-opt-btn-group-pinch" role="group">
                                <button type='submit'  class="btn btn-default btn-save" name="save_progress_notes"><?php echo xlt('Save'); ?></button>
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

                $('.newDatePicker').datetimepicker({
                    timepicker:false,
                    format:'m/d/Y'
                });

                

            });
        </script>
    </body>
</html>
