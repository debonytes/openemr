<?php
/**
 * The data will be stored in cm_treatment_plan form.
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
//require_once("date_qualifier_options.php");
require_once("$srcdir/options.inc.php");
require_once $GLOBALS['srcdir'].'/ESign/Api.php';

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Core\Header;
use ESign\Api;

$folderName = 'cm_treatment_plan';
$tableName = 'form_' . $folderName;


$returnurl = 'encounter_top.php';
$formid = 0 + (isset($_GET['id']) ? $_GET['id'] : 0);

$formStmt = "SELECT id FROM forms WHERE form_id=? AND formdir=?";
$form = sqlQuery($formStmt, array($formid, $folderName));
$GLOBALS['pid'] = empty($GLOBALS['pid']) ? $form['pid'] : $GLOBALS['pid'];

$check_res = $formid ? formFetch($tableName, $formid) : array();

/* checking the last record */
$last_record_query = "SELECT * FROM {$tableName} WHERE pid=? ORDER BY date DESC LIMIT 1";
$last_record = sqlQuery($last_record_query, array($pid));


$esignApi = new Api();
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
        <title><?php echo xlt("Case Management Treatment Plan"); ?></title>

        <?php Header::setupHeader(['datetime-picker', 'opener', 'esign', 'common']); ?>
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
                    margin-top: -5px;
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

                .margin-top-60{
                    margin-top: 60px;
                }

                .margin-top-40{
                    margin-top: 40px;
                }

                .margin-top-80{
                    margin-top: 80px;
                }

                .work-on{
                    margin-top: 100px;
                }

                .margin-right-30{
                    margin-right: 20px;
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
                    <h2><?php echo xlt('Case Management Treatment Plan'); ?></h2>
                </div>
            </div>
            <?php
            $current_date = date('Y-m-d');

            if( $_SESSION['from_dashboard'] ){
                $patient_full_name = ($check_res['name']) ? $check_res['name'] : '';
            } else {
                $patient_id = ( $_SESSION['alert_notify_pid'] ) ? $_SESSION['alert_notify_pid'] : '';
                $pid = ( $_SESSION['pid'] ) ? $_SESSION['pid'] : 0;
                $patient_full_name = '';
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

            
            $patient_DOB = ( isset($patient['DOB']) && $patient['DOB'] ) ? $patient['DOB'] : '';
            $patient_Age = '';
            if($patient_DOB) {
                $dob = strtotime($patient_DOB);       
                $tdate = time();
                $patient_Age = date('Y', $tdate) - date('Y', $dob);
            }
            


            $progress_codes = [
                0 => 'Select',
                1 => 'Objective Reached',
                2 => 'Major Progress',
                3 => 'Some Progress',
                4 => 'Unchanged',
                5 => 'Some Deterioration',
                6 => 'Major Deterioration',
                7 => 'Re-Evaluation Target',
            ];

            ?>
            <div class="row">
                
                <form method="post" id="encform" name="encform" >
                
                    <input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
                    <input type="hidden" name="pid" value="<?php echo $pid; ?>">
                    <input type="hidden" name="encounter" value="<?php echo $encounter; ?>">
                    <input type="hidden" name="user" value="<?php echo $_SESSION['authUser']; ?>">

                    <fieldset style="padding-top: 20px!important;" class="form_content">
                            <div class="col-md-12" style="margin-bottom:30px;">
                                <div class="inner">
                                    <div class="col-md-3 center_align review_box" style="text-align: center; border: 1px solid #333;  padding: 3px 5px 5px;">
                                        <label class="radio-inline">
                                            <input type="radio" name="review" value="90 Day Review" id="review1" <?php echo ($check_res['review'] == '90 Day Review') ? "checked": ""; ?> > <?php echo xlt('90 Day Review'); ?>
                                        </label>                    
                                    </div>
                                    <div class="col-md-3 center_align review_box" style="text-align: center; border-right: 1px solid #333; border-top: 1px solid #333; border-bottom: 1px solid #333; padding: 3px 5px 5px;">
                                        <label class="radio-inline">
                                            <input type="radio" name="review"  value="180 Day Review" id="review2" <?php echo ($check_res['review'] == '180 Day Review') ? "checked": ""; ?>> <?php echo xlt('180 Day Review'); ?>
                                        </label>
                                    </div>
                                    <div class="col-md-3 center_align review_box" style="text-align: center;  border-top: 1px solid #333; border-bottom: 1px solid #333; padding: 3px 5px 5px;">
                                        <label class="radio-inline">
                                            <input type="radio" name="review" value="270 Day Review" id="review3" <?php echo ($check_res['review'] == '270 Day Review') ? "checked": ""; ?>> <?php echo xlt('270 Day Review'); ?>
                                        </label>
                                    </div>
                                    <div class="col-md-3 center_align review_box" style="text-align: center; border: 1px solid #333;  padding: 3px 5px 5px;">
                                        <label class="radio-inline">
                                            <input type="radio" name="review" value="Other Review" id="review4" <?php echo ($check_res['review'] == 'Other Review') ? "checked": ""; ?>> <?php echo xlt('Other Review'); ?>
                                        </label>
                                    </div>
                                    <div class="center_align text-danger review_error" style="width: 100%"></div>
                                </div>
                            </div>

                            <div class="clearfix"></div>

                            <div class="margin-top-30">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="participant_name" class="col-md-5 "><?php echo xlt('Name'); ?></label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" readonly value="<?php echo text($patient_full_name); ?>">
                                            <input type="hidden" class="form-control" id="participant_name" name="participant_name" value="<?php echo text($patient_full_name); ?>">
                                          <small class="text-danger participant_name_error"></small>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="form-group">
                                        <label for="medical_id" class="col-md-5"><?php echo xlt('Medical ID#'); ?></label>
                                        <div class="col-md-6">
                                          <input type="text" class="form-control" id="medical_id" name="medical_id" value="<?php echo text($check_res['medical_id']); ?>">
                                          <small class="text-danger medical_id_error"></small>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="form-group">
                                        <label for="examiner" class="col-md-5"><?php echo xlt('Examiner'); ?></label>
                                        <div class="col-md-6">
                                          <input type="text" class="form-control" id="examiner" name="examiner" value="<?php echo text($check_res['examiner']); ?>">
                                          <small class="text-danger examiner_error"></small>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="form-group">
                                        <label for="practitioner_id" class="col-md-5"><?php echo xlt('Practitioner ID'); ?></label>
                                        <div class="col-md-6">
                                          <input type="text" class="form-control" id="practitioner_id" name="practitioner_id" value="<?php echo text($check_res['practitioner_id']); ?>">
                                          <small class="text-danger practitioner_id_error"></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date" class="col-md-5"><?php echo xlt('Date'); ?></label>
                                        <div class="col-md-6">
                                          <input type="text" class="form-control datepicker" id="date" name="date" value="<?php echo ($check_res['date']) ? text(date('Y-m-d', strtotime($check_res['date']))) : date('Y-m-d'); ?>" autocomplete="off">
                                          <small class="text-danger date_error"></small>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="form-group">
                                        <label for="date_birth" class="col-md-5"><?php echo xlt('DOB'); ?></label>
                                        <div class="col-md-6">
                                          <input type="text" class="form-control datepicker" id="date_birth" name="date_birth" autocomplete="off" value="<?php echo text($patient_DOB); ?>">
                                          <small class="text-danger date_birth_error"></small>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="form-group">
                                        <label for="age" class="col-md-5"><?php echo xlt('Age'); ?></label>
                                        <div class="col-md-6">
                                          <input type="text" class="form-control" id="age" name="age" value="<?php echo text($patient_Age); ?>">
                                          <small class="text-danger age_error"></small>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>

                                </div>

                                
                                
                            </div>


                            <div class="clearfix"></div>

                            

                            <hr>
                            <div class="col-md-12 margin-top-20">
                                <?php 
                                    $diagnoses = ($check_res['dsm_diagnoses']) ? explode('|', $check_res['dsm_diagnoses']) : array();
                                 ?>
                                <h4 class="field-heading"><?php echo xlt('DSM-V DIAGNOSES'); ?></h4>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" class="form-control dsm_diagnoses" name="dsm_diagnoses[]" value="<?php echo text($diagnoses[0]); ?>">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control dsm_diagnoses" name="dsm_diagnoses[]" value="<?php echo text($diagnoses[1]); ?>">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control dsm_diagnoses" name="dsm_diagnoses[]" value="<?php echo text($diagnoses[2]); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" class="form-control dsm_diagnoses" name="dsm_diagnoses[]" value="<?php echo text($diagnoses[3]); ?>">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control dsm_diagnoses" name="dsm_diagnoses[]" value="<?php echo text($diagnoses[4]); ?>">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control dsm_diagnoses" name="dsm_diagnoses[]" value="<?php echo text($diagnoses[5]); ?>">
                                    </div>
                                </div>
                                <small class="text-danger center_align dms_diagnnoses_error"></small>
                                <div class="clearfix"></div>
                            </div>

                            <div class="clearfix"></div>

                            <hr>
                            <div class="col-md-12 padding-20 ">
                                <p class="bold"><?php echo xlt('When was the cosumer\'s last history & physical examination?'); ?></p>
                                <div class="form-group " style="padding-left: 20px">
                                    <label class="radio-inline margin-right-40">
                                        <input type="radio" name="last_examination" id="last_examination1" value="current_year" <?php echo ($check_res['last_examination'] == 'current_year') ? "checked": ""; ?> > <?php echo xlt('Current Year'); ?>
                                    </label>
                                    <label class="radio-inline margin-right-40">
                                      <input type="radio" name="last_examination" id="last_examination2" value="prior_year" <?php echo ($check_res['last_examination'] == 'prior_year') ? "checked": ""; ?>> <?php echo xlt('Prior Year'); ?>
                                    </label>
                                    <label class="radio-inline margin-right-40">
                                      <input type="radio" name="last_examination" id="last_examination3" value="longer_prior_year" <?php echo ($check_res['last_examination'] == 'longer_prior_year') ? "checked": ""; ?> > <?php echo xlt('Longer Than Prior Year'); ?>
                                    </label>
                                    <label class="radio-inline">
                                      <input type="radio" name="last_examination" id="last_examination4" value="unknown" <?php echo ($check_res['last_examination'] == 'unknown') ? "checked": ""; ?> > <?php echo xlt('Unknown'); ?>
                                    </label>
                                </div>
                            </div>

                            <div class="clearfix"></div>

                            <hr>
                            <div class="col-md-12">
                                <p class="bold"><?php echo xlt('Please list the providers who are delivering psychotherapy and/or medication management.'); ?></p>
                                <div class="form-group">
                                    <label for="providers_medication_mgt"><?php echo xlt('Medication Management:'); ?></label>
                                    <textarea name="providers_medication_mgt" id="providers_medication_mgt" rows="3" class="form-control"><?php echo text($check_res['providers_medication_mgt']); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="providers_psychotherapy"><?php echo xlt('Psychotherapy:'); ?></label>
                                    <textarea name="providers_psychotherapy" id="providers_psychotherapy" rows="3" class="form-control"><?php echo text($check_res['providers_psychotherapy']); ?></textarea>
                                </div>
                            </div>

                            <div class="clearfix"></div>

                            <hr>
                            <div class="col-md-12 row_other">
                                <div class="form-group">
                                    <label for="critical_strengths"><?php echo xlt('What are the consumer\'s critical strengths?'); ?></label>
                                    <textarea name="critical_strengths" id="critical_strengths" rows="3" class="form-control" ><?php echo text($check_res['critical_strengths']); ?></textarea>
                                </div>
                            </div>

                            <div class="clearfix"></div>

                            <hr>

                            <div class="col-md-12">
                                <p class="bold"><?php echo xlt('Assessment of the member\'s difficulty functioning in any of the following areas?'); ?></p>
                                <div class="form-group">
                                    <label for="educational " class="col-sm-3"><?php echo xlt('Vocational/Educational:'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="educational" id="educational" class="form-control" value="<?php echo text($check_res['educational']); ?>">
                                        <small class="text-danger educational_error"></small>
                                    </div>                                    
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="financial" class="col-sm-3"><?php echo xlt('Financial:'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="financial" id="financial" class="form-control" value="<?php echo text($check_res['financial']); ?>">
                                        <small class="text-danger financial_error"></small>
                                    </div>                                    
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="family" class="col-sm-3"><?php echo xlt('Family:'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="family" id="family" class="form-control" value="<?php echo text($check_res['family']); ?>">
                                        <small class="text-danger family_error"></small>
                                    </div>                                    
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="social_support" class="col-sm-3"><?php echo xlt('Social Supports:'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="social_support" id="social_support" class="form-control" value="<?php echo text($check_res['social_support']); ?>">
                                        <small class="text-danger social_support_error"></small>
                                    </div>                                    
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="housing" class="col-sm-3"><?php echo xlt('Housing:'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="housing" id="housing" class="form-control" value="<?php echo text($check_res['housing']); ?>">
                                        <small class="text-danger housing_error"></small>
                                    </div>                                    
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="living_skills" class="col-sm-3"><?php echo xlt('Basic Living Skills:'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="living_skills" id="living_skills" class="form-control" value="<?php echo text($check_res['living_skills']); ?>">
                                        <small class="text-danger living_skills_error"></small>
                                    </div>                                    
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="community" class="col-sm-3"><?php echo xlt('Community/Legal:'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="community" id="community" class="form-control" value="<?php echo text($check_res['community']); ?>">
                                        <small class="text-danger community_error"></small>
                                    </div>                                    
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="relationships" class="col-sm-3"><?php echo xlt('Relationships:'); ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="relationships" id="relationships" class="form-control" value="<?php echo text($check_res['relationships']); ?>" >
                                        <small class="text-danger relationships_error"></small>
                                    </div>                                    
                                </div>
                            </div>

                            <div class="clearfix"></div>
                            <hr>
                            <div class="col-md-12 margin-top-40">
                                <p class="bold"><?php echo xlt('Function areas to be addressed in this plan year:'); ?></p>
                                <div class="form-group margin-top-20" style="padding-left: 20px;">
                                    <?php $areas_addressed = ($check_res['areas_to_be_addressed']) ? explode('|', $check_res['areas_to_be_addressed']) : array(); ?>
                                    <label class="checkbox-inline margin-right-10">                                       
                                        <input type="checkbox" name="areas_to_be_addressed[]" id="areas_to_be_addressed1" value="Educational" <?php echo (in_array('Educational', $areas_addressed) ) ? "checked": ""; ?> > <?php echo xlt('Vocational/Educational'); ?>
                                    </label>
                                    <label class="checkbox-inline margin-right-10">
                                      <input type="checkbox" name="areas_to_be_addressed[]" id="areas_to_be_addressed2" value="Financial" <?php echo (in_array('Financial', $areas_addressed) ) ? "checked": ""; ?> > <?php echo xlt('Financial'); ?>
                                    </label>
                                    <label class="checkbox-inline margin-right-10">
                                      <input type="checkbox" name="areas_to_be_addressed[]" id="areas_to_be_addressed3" value="Family" <?php echo (in_array('Family', $areas_addressed) ) ? "checked": ""; ?>  > <?php echo xlt('Family'); ?>
                                    </label>

                                    <label class="checkbox-inline margin-right-10">
                                      <input type="checkbox" name="areas_to_be_addressed[]" id="areas_to_be_addressed4" value="Social Supports" <?php echo (in_array('Social Supports', $areas_addressed) ) ? "checked": ""; ?> > <?php echo xlt('Social Supports'); ?>
                                    </label>
                                    <label class="checkbox-inline margin-right-10">
                                      <input type="checkbox" name="areas_to_be_addressed[]" id="areas_to_be_addressed5" value="Housing" <?php echo (in_array('Financial', $areas_addressed) ) ? "checked": ""; ?> > <?php echo xlt('Housing'); ?>
                                    </label>
                                    <label class="checkbox-inline margin-right-10">
                                      <input type="checkbox" name="areas_to_be_addressed[]" id="areas_to_be_addressed6" value="Basic Living Skills" <?php echo (in_array('Basic Living Skills', $areas_addressed) ) ? "checked": ""; ?> > <?php echo xlt('Basic Living Skills'); ?>
                                    </label>

                                    <label class="checkbox-inline margin-right-10">
                                      <input type="checkbox" name="areas_to_be_addressed[]" id="areas_to_be_addressed7" value="Community" <?php echo (in_array('Community', $areas_addressed) ) ? "checked": ""; ?> > <?php echo xlt('Community/Legal'); ?>
                                    </label>
                                    <label class="checkbox-inline margin-right-10">
                                      <input type="checkbox" name="areas_to_be_addressed[]" id="areas_to_be_addressed8" value="Health" <?php echo (in_array('Health', $areas_addressed) ) ? "checked": ""; ?>  > <?php echo xlt('Health/Medical'); ?>
                                    </label>
                                    <label class="checkbox-inline">
                                      <input type="checkbox" name="areas_to_be_addressed[]" id="areas_to_be_addressed9" value="Relationships" <?php echo (in_array('Relationships', $areas_addressed) ) ? "checked": ""; ?> > <?php echo xlt('Relationships'); ?>
                                    </label>
                                </div>
                                <div class="clearfix">&nbsp;</div>
                                <p class="margin-top-40"><?php echo xlt('All areas checked above will be addressed as needed thoughout the plan year although they may not be outlined in the major problem areas focused on below.'); ?></p>
                                <p><?php echo xlt('This plan will be addressed both CBRS including skills training and symptom management as well as Case Management activities such as linking, coordinating and advocating for the person served.'); ?></p>
                            </div>

                            <div class="clearfix">&nbsp;</div>
                            
                            <div class="padding-20 work-on">
                                <h4 class="field-heading"><?php echo xlt('GOALS/OBJECTIVES SECTION'); ?></h4>
                                <p><?php echo xlt('Objectives must address the emotional, behavioral, and skill training needs identified by the member'); ?></p>
                            </div>
                            

                            <div class="col-md-12"> <!-- beginning of Problem #1 -->
                                <h4><?php echo xlt('Problem Area 1#'); ?></h4>
                                <div class="form-group">
                                    <label for="problem_1_description"><?php echo xlt('Choose (see above) (BRIEF description of issues will be addressed in the following goal)'); ?></label>
                                    <textarea name="problem_1_description" id="problem_1_description" rows="3" class="form-control"><?php echo text($check_res['problem_1_description']); ?></textarea>
                                    <small class="text-danger problem_1_description_error"></small>
                                </div>
                                <div class="form-group">
                                    <label for="problem_1_client_goal"><?php echo xlt('Client\'s statement of overall goal or need:'); ?></label>
                                    <textarea name="problem_1_client_goal" id="problem_1_client_goal" rows="3" class="form-control"><?php echo text($check_res['problem_1_client_goal']); ?></textarea>
                                    <small class="text-danger problem_1_client_goal_error"></small>
                                </div>
                                <div class="form-group">
                                    <label for="problem_1_objectives"><?php echo xlt('TX Objectives (must be concrete and measurable)'); ?></label>
                                    <textarea name="problem_1_objectives" id="problem_1_objectives" rows="3" class="form-control"><?php echo text($check_res['problem_1_objectives']); ?></textarea>
                                    <small class="text-danger problem_1_objectives_error"></small>
                                </div>
                                <div class="form-group">
                                    <label for="problem_1_objectives_tasks"><?php echo xlt('Tasks to complete objectives: (including type and frequency of treatment)'); ?></label>
                                    <textarea name="problem_1_objectives_tasks" id="problem_1_objectives_tasks" rows="3" class="form-control"><?php echo text($check_res['problem_1_objectives_tasks']); ?></textarea>
                                    <small class="text-danger problem_1_objectives_tasks_error"></small>
                                </div>
                                <div class="margin-top-20">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="" class="col-sm-6"><?php echo xlt('Date of Review'); ?></label>
                                            <div class="col-sm-6">
                                                <input type="text" name="problem_1_date_review" class="form-control datepicker" autocomplete="off" value="<?php echo text($check_res['problem_1_date_review']); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="problem_1_progress_code" class="col-sm-5"><?php echo xlt('Progress Code'); ?></label>
                                            <div class="col-sm-7">
                                                <select name="problem_1_progress_code" id="problem_1_progress_code" class="form-control">
                                                    <?php foreach($progress_codes as $key => $code): ?>
                                                        <option value="<?php echo $key; ?>" <?php echo ($check_res['problem_1_progress_code'] == $key) ? "selected": ""; ?>  ><?php echo $code; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="problem_1_comments" class="col-sm-4"><?php echo xlt('Comments'); ?></label>
                                            <div class="col-sm-8">
                                                <textarea name="problem_1_comments" class="form-control" id="problem_1_comments" rows="3"><?php echo text($check_res['problem_1_comments']); ?></textarea>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="clearfix"></div>
                                <div class="margin-top-10">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="" class="col-sm-6"><?php echo xlt('Target Date for Attainment:'); ?></label>
                                            <div class="col-sm-6">
                                                <input type="text" name="problem_1_date_target" class="form-control datepicker" autocomplete="off" value="<?php echo text($check_res['problem_1_date_target']); ?>" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="" class="col-sm-4"><?php echo xlt('Completion Date:'); ?></label>
                                            <div class="col-sm-8">
                                                <input type="text" name="problem_1_date_completion" class="form-control datepicker" autocomplete="off" value="<?php echo text($check_res['problem_1_date_completion']); ?>" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="clearfix"></div>
                            </div>  
                            <div class="clearfix"></div> <!-- end of Problem 1 -->

                            <hr>

                            <div class="col-md-12"> <!-- beginning of Problem 2 -->
                                <h4><?php echo xlt('Problem Area 2#'); ?></h4>
                                <div class="form-group">
                                    <label for="problem_2_description"><?php echo xlt('Choose (see above) (BRIEF description of issues will be addressed in the following goal)'); ?></label>
                                    <textarea name="problem_2_description" id="problem_2_description" rows="3" class="form-control"><?php echo text($check_res['problem_2_description']); ?></textarea>
                                    <small class="text-danger problem_2_description_error"></small>
                                </div>
                                <div class="form-group">
                                    <label for="problem_2_client_goal"><?php echo xlt('Client\'s statement of overall goal or need:'); ?></label>
                                    <textarea name="problem_2_client_goal" id="problem_2_client_goal" rows="3" class="form-control"><?php echo text($check_res['problem_2_client_goal']); ?></textarea>
                                    <small class="text-danger problem_2_client_goal_error"></small>
                                </div>
                                <div class="form-group">
                                    <label for="problem_2_objectives"><?php echo xlt('TX Objectives (must be concrete and measurable)'); ?></label>
                                    <textarea name="problem_2_objectives" id="problem_2_objectives" rows="3" class="form-control"><?php echo text($check_res['problem_2_objectives']); ?></textarea>
                                    <small class="text-danger problem_2_objectives_error"></small>
                                </div>
                                <div class="form-group">
                                    <label for="problem_2_objectives_tasks"><?php echo xlt('Tasks to complete objectives: (including type and frequency of treatment)'); ?></label>
                                    <textarea name="problem_2_objectives_tasks" id="problem_2_objectives_tasks" rows="3" class="form-control"><?php echo text($check_res['problem_2_objectives_tasks']); ?></textarea>
                                    <small class="text-danger problem_2_objectives_tasks_error"></small>
                                </div>
                                <div class="margin-top-20">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="problem_2_date_review" class="col-sm-6"><?php echo xlt('Date of Review'); ?></label>
                                            <div class="col-sm-6">
                                                <input type="text" name="problem_2_date_review" class="form-control datepicker" autocomplete="off" value="<?php echo text($check_res['problem_2_date_review']); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="problem_2_progress_code" class="col-sm-5"><?php echo xlt('Progress Code'); ?></label>
                                            <div class="col-sm-7">
                                                <select name="problem_2_progress_code" id="problem_2_progress_code" class="form-control">
                                                    <?php foreach($progress_codes as $key => $code): ?>
                                                        <option value="<?php echo $key; ?>" <?php echo ($check_res['problem_2_progress_code'] == $key) ? "selected ": "";  ?>  ><?php echo $code; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="problem_2_comments" class="col-sm-4"><?php echo xlt('Comments'); ?></label>
                                            <div class="col-sm-8">
                                                <textarea name="problem_2_comments" class="form-control" id="problem_2_comments" rows="3"><?php echo text($check_res['problem_2_comments']); ?></textarea>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="clearfix"></div>
                                <div class="margin-top-10">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="problem_2_date_target" class="col-sm-6"><?php echo xlt('Target Date for Attainment:'); ?></label>
                                            <div class="col-sm-6">
                                                <input type="text" name="problem_2_date_target" class="form-control datepicker" autocomplete="off" value="<?php echo text($check_res['problem_2_date_target']); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="problem_2_date_completion" class="col-sm-4"><?php echo xlt('Completion Date:'); ?></label>
                                            <div class="col-sm-8">
                                                <input type="text" name="problem_2_date_completion" class="form-control datepicker" autocomplete="off" value="<?php echo text($check_res['problem_2_date_completion']); ?>" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="clearfix"></div>
                            </div>  
                            <div class="clearfix"></div> <!-- end of Problem 2 -->

                            <hr>

                            <div class="col-md-12"> <!-- beginning of Problem 3 -->
                                <h4><?php echo xlt('Problem Area 3#'); ?></h4>
                                <div class="form-group">
                                    <label for="problem_3_description"><?php echo xlt('Choose (see above) (BRIEF description of issues will be addressed in the following goal)'); ?></label>
                                    <textarea name="problem_3_description" id="problem_3_description" rows="3" class="form-control"><?php echo text($check_res['problem_3_description']); ?></textarea>
                                    <small class="text-danger problem_3_description_error"></small>
                                </div>
                                <div class="form-group">
                                    <label for="problem_3_client_goal"><?php echo xlt('Client\'s statement of overall goal or need:'); ?></label>
                                    <textarea name="problem_3_client_goal" id="problem_3_client_goal" rows="3" class="form-control"><?php echo text($check_res['problem_3_client_goal']); ?></textarea>
                                    <small class="text-danger problem_3_client_goal_error"></small>
                                </div>
                                <div class="form-group">
                                    <label for="problem_3_objectives"><?php echo xlt('TX Objectives (must be concrete and measurable)'); ?></label>
                                    <textarea name="problem_3_objectives" id="problem_3_objectives" rows="3" class="form-control"><?php echo text($check_res['problem_3_objectives']); ?></textarea>
                                    <small class="text-danger problem_3_objectives_error"></small>
                                </div>
                                <div class="form-group">
                                    <label for="problem_3_objectives_tasks"><?php echo xlt('Tasks to complete objectives: (including type and frequency of treatment)'); ?></label>
                                    <textarea name="problem_3_objectives_tasks" id="problem_3_objectives_tasks" rows="3" class="form-control"><?php echo text($check_res['problem_3_objectives_tasks']); ?></textarea>
                                    <small class="text-danger problem_3_objectives_tasks_error"></small>
                                </div>
                                <div class="margin-top-20">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="problem_3_date_review" class="col-sm-6"><?php echo xlt('Date of Review'); ?></label>
                                            <div class="col-sm-6">
                                                <input type="text" name="problem_3_date_review" class="form-control datepicker" autocomplete="off" value="<?php echo text($check_res['problem_3_date_review']); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="problem_3_progress_code" class="col-sm-5"><?php echo xlt('Progress Code'); ?></label>
                                            <div class="col-sm-7">
                                                <select name="problem_3_progress_code" id="problem_3_progress_code" class="form-control">
                                                    <?php foreach($progress_codes as $key => $code): ?>
                                                        <option value="<?php echo $key; ?>" <?php echo ($check_res['problem_3_progress_code'] == $key) ? "selected": ""; ?>  ><?php echo $code; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="problem_3_comments" class="col-sm-4"><?php echo xlt('Comments'); ?></label>
                                            <div class="col-sm-8">
                                                <textarea name="problem_3_comments" class="form-control" id="problem_3_comments" rows="3"><?php echo text($check_res['problem_3_comments']); ?></textarea>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                

                                <div class="clearfix"></div>
                                <div class="margin-top-10">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="problem_3_date_target" class="col-sm-6"><?php echo xlt('Target Date for Attainment:'); ?></label>
                                            <div class="col-sm-6">
                                                <input type="text" name="problem_3_date_target" class="form-control datepicker" autocomplete="off" value="<?php echo text($check_res['problem_3_date_target']); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="problem_3_date_completion" class="col-sm-4"><?php echo xlt('Completion Date:'); ?></label>
                                            <div class="col-sm-8">
                                                <input type="text" name="problem_3_date_completion" class="form-control datepicker" autocomplete="off" value="<?php echo text($check_res['problem_3_date_completion']); ?>" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="clearfix"></div>
                            </div>  
                            <div class="clearfix"></div> <!-- end of Problem 3 -->

                            <div class="col-md-12 margin-top-20">
                                <div class="form-group">
                                    <label for="individual_included" class="col-sm-5"><?php echo xlt('Individuals included in initial treatment planning or review:'); ?></label>
                                    <div class="col-sm-7">
                                        <input type="text" name="individual_included" id="individual_included" class="form-control" autocomplete="off" value="<?php echo text($check_res['individual_included']); ?>">
                                    </div>
                                </div>
                            </div>

                            <!--
                            <div class="col-md-12 margin-top-20" style="margin-top: 30px">
                                <div class="form-group">
                                    <input type="checkbox" name="status" id="status" value="completed">
                                    <label for="status" class=""><?php echo xlt('Mark as Complete'); ?></label>                                    
                                </div>
                            </div>
                            -->


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
                                <button type='submit'  class="btn btn-default btn-save" name="save_treatment_plan"><?php echo xlt('Save'); ?></button>
                                <button type="button" class="btn btn-link btn-cancel oe-opt-btn-separate-left" onclick="form_close_tab()"><?php echo xlt('Cancel');?></button>
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

                $('.btn-save').on('click', function(e){
                    e.preventDefault();
                    var errors = false;

                    // var review1 = $('#review1').prop('checked');
                    // var review2 = $('#review2').prop('checked');
                    // var review3 = $('#review3').prop('checked');
                    // var review4 = $('#review4').prop('checked');
                    // if((review1 == '') && (review2 == '') && (review3 == '') && (review4 == '')){
                    //     $('.review_error').text('Please enter your Review.');
                    //     errors = true;
                    // } else {
                    //     $('.review_error').text('');
                    //     errors = false;
                    // }

                    // var participant_name = $('#participant_name').val();
                    // if(participant_name == ''){
                    //     $('.participant_name_error').text('Please enter Participant Name.');   
                    //     errors = true;                     
                    // } else {
                    //     $('.participant_name_error').text('');
                    //     errors = false;
                    // }

                    // var medical_id = $('#medical_id').val();
                    // if(medical_id == ''){
                    //     $('.medical_id_error').text('Enter Medical ID.');  
                    //     errors = true;                      
                    // } else {
                    //     $('.medical_id_error').text('');
                    //     errors = false;
                    // }

                    // var date_birth = $('#date_birth').val();
                    // if(date_birth == ''){
                    //     $('.date_birth_error').text('Enter date of birth.');      
                    //     errors = true;                  
                    // } else {
                    //     $('.date_birth_error').text('');
                    //     errors = false;
                    // }

                    // var age = $('#age').val();
                    // if(age == ''){
                    //     $('.age_error').text('Enter age.'); 
                    //     errors = true;                       
                    // } else {
                    //     $('.age_error').text('');
                    //     errors = false;
                    // }

                    
                    // var examiner = $('#examiner').val();
                    // if(examiner == ''){
                    //     $('.examiner_error').text('Please enter examiner name.');    
                    //     errors = true;                    
                    // } else {
                    //     $('.examiner_error').text('');
                    //     errors = false;
                    // }


                    // var practitioner_id = $('#practitioner_id').val();
                    // if(practitioner_id == ''){
                    //     $('.practitioner_id_error').text('Please enter Practitioner ID.');    
                    //     errors = true;                    
                    // } else {
                    //     $('.practitioner_id_error').text('');
                    //     errors = false;
                    // }

                    // var date = $('#date').val();
                    // if(date == ''){
                    //     $('.date_error').text('Please enter date.');    
                    //     errors = true;                    
                    // } else {
                    //     $('.date_error').text('');
                    //     errors = false;
                    // }
                    
                    /*$('.dsm_diagnoses').each(function(){
                        if($(this).val() == ''){
                            $('.dsm_diagnoses_error').text('Please enter some diagnoses.');    
                            errors = true;
                        } else {
                            $('.dsm_diagnoses_error').text('');    
                            errors = false;
                        }
                    });*/
                    

                    // if(errors){
                    //     return;
                    // }                    

                    top.restoreSession();

                    $.ajax({
                        url: "<?php echo $rootdir; ?>/forms/<?php echo $folderName; ?>/save.php?id=<?php echo attr_url($formid); ?>",
                        type: 'POST',
                        data: $('form#encform').serialize(),
                        success: function(response){
                            //window.location.reload();
                            window.location.href = "<?php echo $rootdir; ?>/forms/<?php echo $folderName; ?>/redirect.php";
                        },
                        errors: function(response){
                            //console.log(response);
                        }
                    });

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

                            var formid = "<?php echo $_REQUEST['id']; ?>";
                            var formdir = "<?php echo $_REQUEST['formname']; ?>";
                            send_email_after_esign(formid, formdir);
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
                        pageTitle: "Case Management Treatment Plan",              // add title to print page
                        removeInline: false,        // remove inline styles from print elements
                        removeInlineSelector: "*",  // custom selectors to filter inline styles. removeInline must be true
                        printDelay: 333,            // variable print delay
                        header: "<h2>Case Management Treatment Plan</h2>",               // prefix to html
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


            function send_email_after_esign(formid, formdir)
            {                
                $.ajax({
                  url: "sendemail.php",
                  type: 'POST',
                  data: {
                    send_email: true,
                    pid: <?php echo $pid; ?>,
                    formdir: formdir,
                    formid: formid,
                  },
                  success: function(response){
                      $('.send_email').removeAttr('disabled');
                      console.log(response);
                  },
                  error: function(response){
                    $('.send_email').removeAttr('disabled');
                      console.log(response);
                  }
                });
            }
        </script>
    </body>
</html>
