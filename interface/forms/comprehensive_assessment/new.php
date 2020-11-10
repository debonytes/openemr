<?php
/**
 * Comprehensive Assessment form.
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

$folderName = 'comprehensive_assessment';
$tableName = 'form_' . $folderName;


$returnurl = 'encounter_top.php';
$formid = 0 + (isset($_GET['id']) ? $_GET['id'] : 0);
$check_res = $formid ? formFetch($tableName, $formid) : array();
?>
<html>
    <head>
        <title><?php echo xlt("Progress Note"); ?></title>

        <?php Header::setupHeader(['datetime-picker', 'opener']); ?>
        <link rel="stylesheet" href="<?php echo $web_root; ?>/library/css/bootstrap-timepicker.min.css">
        <link rel="stylesheet" href="../../../style_custom.css">
    </head>
    <body class="body_top">
        <div class="container">
            <div class="row">
                <div class="page-header">
                    <h2><?php echo xlt('Comprehensive Diagnostic Assessment'); ?></h2>
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
                
                <form method="post" id="my_comprehensive_assessment_form" name="my_comprehensive_assessment_form" action="<?php echo $rootdir; ?>/forms/<?php echo $folderName; ?>/save.php?id=<?php echo attr_url($formid); ?>">          

                
                    <input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
                    <input type="hidden" name="pid" value="<?php echo $pid; ?>">
                    <input type="hidden" name="encounter" value="<?php echo $encounter; ?>">
                    <input type="hidden" name="user" value="<?php echo $user_id; ?>">
                    <input type="hidden" name="authorized" value="<?php echo $userauthorized; ?>">
                    <input type="hidden" name="activity" value="1">

                    <fieldset>
                        <legend class=""><?php echo xlt('Comprehensive Diagnostic Assessment'); ?></legend>
                            
                            <div class="col-md-12" style="margin-top: 10px">
                                <div class="col-sm-6">
                                    <label class="checkbox-inline"><strong><?php echo xlt('Type of CDA:'); ?></strong> </label>
                                    <label class="checkbox-inline">
                                      <input type="radio" id="type_cda_1" name="type_cda" value="New" <?php echo ($check_res['type_cda'] == 'New') ? 'checked':''; ?> > <?php echo xlt('New'); ?>
                                    </label>
                                    <label class="checkbox-inline">
                                      <input type="radio" id="type_cda_2" name="type_cda" value="Updated" <?php echo ($check_res['type_cda'] == 'Updated') ? 'checked':''; ?> > <?php echo xlt('Updated'); ?>
                                    </label>
                                    <label class="checkbox-inline">
                                      <input type="radio" id="type_cda_3"  name="type_cda" value="Annual Review" <?php echo ($check_res['type_cda'] == 'Annual Review') ? 'checked':''; ?> > <?php echo xlt('Annual Review'); ?>
                                    </label>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-7 control-label"> <?php echo xlt('If update or review original CDA date:'); ?> </label>
                                        <div class="col-sm-5">
                                          <input type="text" class="form-control datepicker" name="cda_date" value="<?php echo text($check_res['cda_date']); ?>" >
                                        </div>
                                    </div>
                                </div>   
                                <div class="clearfix"></div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="name" class="col-sm-5 "><?php echo xlt('Participant Name'); ?></label>
                                        <div class="col-sm-7">
                                            <input type="text"  id="name" class="form-control" value="<?php echo text($patient_full_name); ?>" readonly>
                                            <input type="hidden" name="name" value="<?php echo text($patient_full_name); ?>" >
                                        </div>                                    
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="medicaid" class="col-sm-3 "><?php echo xlt('Medicaid #'); ?></label>
                                        <div class="col-sm-9">
                                            <input type="text" name="medicaid" id="medicaid" class="form-control" value="<?php echo text($check_res['medicaid']); ?>">
                                            <small class="text-danger medicaid_error"></small>
                                        </div>                                    
                                    </div>
                                </div> 
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="" class="col-sm-3 "><?php echo xlt('Date'); ?></label>
                                        <div class="col-sm-9">
                                            <input type="text" name="date" id="date" class="form-control datepicker" value="<?php echo text(date('Y-m-d', strtotime($check_res['date'])  )); ?>" autocomplete="off">
                                            <small class="text-danger date_error"></small>
                                        </div>                                    
                                    </div>
                                </div>
                                <div class="clearfix"></div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="age" class="col-sm-3 "><?php echo xlt('Age'); ?></label>
                                        <div class="col-sm-9">
                                            <input type="text" id="age" class="form-control" name="age" value="<?php echo text($check_res['age']); ?>">
                                            <small class="text-danger age_error"></small>
                                        </div>                                    
                                    </div>

                                    <div class="form-group">
                                        <label for="sex" class="col-sm-3 "><?php echo xlt('Sex'); ?></label>
                                        <div class="col-sm-9">
                                            <select name="sex" id="sex" class="form-control">
                                                <option value=""><?php echo xlt('Choose'); ?></option>
                                                <option value="Male" <?php echo ($check_res['sex'] == 'Male') ? 'selected':''; ?> ><?php echo xlt('Male'); ?></option>
                                                <option value="Female" <?php echo ($check_res['sex'] == 'Female') ? 'selected':''; ?> ><?php echo xlt('Female'); ?></option>
                                                <option value="Other" <?php echo ($check_res['sex'] == 'Other') ? 'selected':''; ?> ><?php echo xlt('Other'); ?></option>
                                                <option value="No-Answer" <?php echo ($check_res['sex'] == 'No-Answer') ? 'selected':''; ?> ><?php echo xlt('Prefer not to answer'); ?></option>
                                            </select>                                            
                                            <small class="text-danger _error"></small>
                                        </div>                                    
                                    </div>

                                </div>
                               
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="ethnicity" class="col-sm-3 "><?php echo xlt('Ethnicity'); ?></label>
                                        <div class="col-sm-9">
                                            <select name="ethnicity" id="ethnicity" class="form-control">
                                                <option value=""><?php echo xlt('Choose'); ?></option>
                                                <option value="White" <?php echo ($check_res['ethnicity'] == 'White') ? 'selected':''; ?> ><?php echo xlt('White/Caucasian'); ?></option>
                                                <option value="Hispanic" <?php echo ($check_res['ethnicity'] == 'Hispanic') ? 'selected':''; ?> ><?php echo xlt('Hispanic'); ?></option>
                                                <option value="African-American" <?php echo ($check_res['ethnicity'] == 'African-American') ? 'selected':''; ?> ><?php echo xlt('African-American'); ?></option>
                                                <option value="Asian-American" <?php echo ($check_res['ethnicity'] == 'Asian-American') ? 'selected':''; ?> ><?php echo xlt('Asian-American'); ?></option>
                                                <option value="Other" <?php echo ($check_res['ethnicity'] == 'Other') ? 'selected':''; ?> ><?php echo xlt('Other'); ?></option>
                                            </select>                                            
                                            <small class="text-danger _error"></small>
                                        </div>                                    
                                    </div>

                                    <div class="form-group">
                                        <label for="ethnicity_other" class="col-sm-3 "><?php echo xlt('Other Enthicity'); ?></label>
                                        <div class="col-sm-9">
                                            <input type="text" name="ethnicity_other" id="ethnicity_other" class="form-control" value="<?php echo text($check_res['ethnicity_other']); ?>">
                                            <small class="text-danger ethnicity_other_error"></small>
                                        </div>                                    
                                    </div>

                                </div>
                                                               
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="dob" class="col-sm-3 "><?php echo xlt('DOB'); ?></label>
                                        <div class="col-sm-9">
                                            <input type="text" name="dob" id="dob" class="form-control datepicker" value="<?php echo text($check_res['dob']); ?>" autocomplete="off">
                                            <small class="text-danger dob_error"></small>
                                        </div>                                    
                                    </div>
                                </div>
                                <div class="clearfix"></div>

                                <!-- Examiner -->
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="examiner" class="col-sm-3 "><?php echo xlt('Examiner'); ?></label>
                                        <div class="col-sm-9">
                                            <select name="examiner" id="examiner" class="form-control">
                                                <?php echo get_examiner_name_dregree($check_res['examiner']); ?>
                                            </select>                                            
                                            <small class="text-danger examiner_error"></small>
                                        </div>                                    
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="region" class="col-sm-3 "><?php echo xlt('Region'); ?></label>
                                        <div class="col-sm-9">
                                            <input type="text" name="region" id="region" class="form-control " value="<?php echo text($check_res['region']); ?>" >
                                            <small class="text-danger region_error"></small>
                                        </div>                                    
                                    </div>
                                </div>
                                
                                <!-- Agency -->
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="agency" class="col-sm-3 "><?php echo xlt('Agency'); ?></label>
                                        <div class="col-sm-9">
                                            <select name="agency" id="agency" class="form-control">
                                                <option value=""><?php echo xlt('Choose'); ?></option>
                                                <option value="Kuna Counseling Center, LLC" <?php echo ($check_res['agency'] == 'Kuna Counseling Center, LLC') ? 'selected':''; ?> ><?php echo xlt('Kuna Counseling Center, LLC'); ?></option>
                                                <option value="Real Solutions Counseling, LLC" <?php echo ($check_res['agency'] == 'Real Solutions Counseling, LLC') ? 'selected':''; ?> ><?php echo xlt('Real Solutions Counseling, LLC'); ?></option>                                                
                                            </select>                                            
                                            <small class="text-danger agency_error"></small>
                                        </div>                                    
                                    </div>
                                </div>
                                <div class="clearfix"></div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="marital_status" class="col-sm-4 "><?php echo xlt('Marital Status'); ?></label>
                                        <div class="col-sm-8">
                                            <select name="marital_status" id="marital_status" class="form-control">
                                                <option value=""><?php echo xlt('Choose'); ?></option>
                                                <option value="Single" <?php echo ($check_res['marital_status'] == 'Single') ? 'selected':''; ?> ><?php echo xlt('Single'); ?></option>
                                                <option value="Married" <?php echo ($check_res['marital_status'] == 'Married') ? 'selected':''; ?> ><?php echo xlt('Married'); ?></option>
                                                <option value="Divorced" <?php echo ($check_res['marital_status'] == 'Divorced') ? 'selected':''; ?> ><?php echo xlt('Divorced'); ?></option>
                                                <option value="Separated" <?php echo ($check_res['marital_status'] == 'Separated') ? 'selected':''; ?> ><?php echo xlt('Separated'); ?></option>
                                                <option value="Widowed" <?php echo ($check_res['marital_status'] == 'Widowed') ? 'selected':''; ?> ><?php echo xlt('Widowed'); ?></option>
                                            </select>                                            
                                            <small class="text-danger marital_status_error"></small>
                                        </div>                                    
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="referral_source" class="col-sm-4 "><?php echo xlt('Referral Source'); ?></label>
                                        <div class="col-sm-8">
                                            <input type="text" name="referral_source" id="referral_source" class="form-control " value="<?php echo text($check_res['referral_source']); ?>" >
                                            <small class="text-danger referral_source_error"></small>
                                        </div>                                    
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div ><strong><?php echo xlt('Participation Status:'); ?></strong> </div>
                                    <label class="checkbox-inline">
                                      <input type="radio"  name="participation_status" value="Voluntary" <?php echo ($check_res['participation_status'] == 'Voluntary') ? 'checked': ''; ?> > <?php echo xlt('Voluntary'); ?>
                                    </label>
                                    <label class="checkbox-inline">
                                      <input type="radio"  name="participation_status" value="Involuntary"  <?php echo ($check_res['participation_status'] == 'Involuntary') ? 'checked': ''; ?> > <?php echo xlt('Involuntary'); ?>
                                    </label>
                                </div>
                                <div class="clearfix"></div>

                            </div>

                            <div class="clearfix"></div>

                            

                            <div class="clearfix"></div>

                            <!-- PSYCHIATRIC AND MEDICAL HISTORY -->
                            <div class="col-md-12 margin-top-20" style="margin-top: 20px">
                                <h3 class="text-center"><?php echo xlt('PSYCHIATRIC AND MEDICAL HISTORY'); ?></h3>
                                <div style="margin-top: 10px;">
                                    <h4><?php echo xlt('PSYCHIATRIC SECTION'); ?></h4>
                                    <div class="form-group">
                                        <label for="behave_treatment_history"><?php echo xlt('Behavioral health treatment history (previous dx, age of onset, any treatment):'); ?></label>
                                        <textarea name="behave_treatment_history"  rows="3" class="form-control"><?php echo text($check_res['behave_treatment_history']); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="behave_treatment_history"><?php echo xlt('Behavioral health family history (previous dx, age of onset, any treatment):'); ?></label>
                                        <textarea name="behave_family_history"  rows="3" class="form-control"><?php echo text($check_res['behave_family_history']); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="behave_treatment_history"><?php echo xlt('Any previous history of abuse (self or witness):'); ?></label>
                                        <textarea name="history_abuse"  rows="3" class="form-control"><?php echo text($check_res['history_abuse']); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label class="checkbox-inline"><strong><?php echo xlt('Has client had counseling or CBRS at a different agency in the past year?'); ?></strong></label>
                                        <label class="checkbox-inline">
                                          <input type="radio"  name="agency_past_year" value="Yes" <?php echo ($check_res['agency_past_year'] == 'Yes') ? 'checked': ''; ?>  > <?php echo xlt('Yes'); ?>
                                        </label>
                                        <label class="checkbox-inline">
                                          <input type="radio"  name="agency_past_year" value="No" <?php echo ($check_res['agency_past_year'] == 'No') ? 'checked': ''; ?> > <?php echo xlt('No'); ?>
                                        </label>
                                        <label class="checkbox-inline">
                                          <strong><?php echo xlt('If so, where?'); ?></strong>
                                        </label>
                                        <label class="checkbox-inline">
                                          <input type="text" class="form-control"  name="agency_past_year_place" value="<?php echo text($check_res['agency_past_year_place']); ?>">
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <div><strong><?php echo xlt('Development history/problems (mental or physical problems):'); ?></strong></div>
                                        <div class="radio" style="margin-top: 0">
                                          <label>
                                            <input type="radio" name="devt_history" value="met_milestones" <?php echo ($check_res['devt_history'] == 'met_milestones') ? 'checked':''; ?> >
                                            <?php echo xlt('Met milestones at appropriate times of life'); ?>
                                          </label>
                                        </div>
                                        <div class="radio">
                                          <label>
                                            <input type="radio" name="devt_history" value="not_met_milestones" <?php echo ($check_res['devt_history'] == 'not_met_milestones') ? 'checked':''; ?> >
                                            <?php echo xlt('Did not meet milestones at appropriate times of life'); ?>
                                          </label>
                                        </div>
                                        <p><?php echo xlt('Explain if did not meet the milestones:'); ?></p>
                                        <textarea name="devt_history_details"  rows="3" class="form-control"><?php echo text($check_res['devt_history_details']); ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <div><strong><?php echo xlt('If the child is an adolescent, give a sexual behavior history:'); ?></strong></div>
                                        <textarea name="adult_sexual_behavior"  rows="3" class="form-control"><?php echo text($check_res['adult_sexual_behavior']); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <div><strong><?php echo xlt('If the child (under 18), please give number of hours per day spent on electronics (include TV, computer, tablet, phone, video games):'); ?></strong></div>
                                        <input type="text" name="child_gadget_hours" value="<?php echo text($check_res['child_gadget_hours']); ?>">
                                    </div>
                                </div>

                                <div style="margin-top: 20px;">
                                    <h4><?php echo xlt('MEDICAL SECTION'); ?></h4>
                                    <div><strong><?php echo xlt('Does the recipient report any of the following? Check all that apply:'); ?></strong></div>
                                    <?php $recipient_report  = explode('|', $check_res['recipient_report']);  ?>
                                    <ul style="list-style-type: none; padding: 0; columns: 4;  -webkit-columns: 4;  -moz-columns: 4;">
                                        <li>
                                            <div class="checkbox">
                                              <label>
                                                <input type="checkbox" value="Head injury" name="recipient_report[]" <?php echo (in_array('Head injury', $recipient_report)) ? 'checked': '';  ?> >
                                                <?php echo xlt('Head injury/stroke'); ?>
                                              </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="checkbox">
                                              <label>
                                                <input type="checkbox" value="Loss of consciousness" name="recipient_report[]" <?php echo (in_array('Loss of consciousness', $recipient_report)) ? 'checked': '';  ?> >
                                                <?php echo xlt('Loss of consciousness'); ?>
                                              </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="checkbox">
                                              <label>
                                                <input type="checkbox" value="Respiratory problems" name="recipient_report[]" <?php echo (in_array('Respiratory problems', $recipient_report)) ? 'checked': '';  ?> >
                                                <?php echo xlt('Respiratory problems'); ?>
                                              </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="checkbox">
                                              <label>
                                                <input type="checkbox" value="Heart problems" name="recipient_report[]" <?php echo (in_array('Heart problems', $recipient_report)) ? 'checked': '';  ?> >
                                                <?php echo xlt('Heart/Vascular problems'); ?>
                                              </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="checkbox">
                                              <label>
                                                <input type="checkbox" value="Liver disease" name="recipient_report[]" <?php echo (in_array('Liver disease', $recipient_report)) ? 'checked': '';  ?> >
                                                <?php echo xlt('Liver disease'); ?>
                                              </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="checkbox">
                                              <label>
                                                <input type="checkbox" value="Thyroid problems" name="recipient_report[]" <?php echo (in_array('Thyroid problems', $recipient_report)) ? 'checked': '';  ?> >
                                                <?php echo xlt('Thyroid problems'); ?>
                                              </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="checkbox">
                                              <label>
                                                <input type="checkbox" value="Cancer" name="recipient_report[]" <?php echo (in_array('Cancer', $recipient_report)) ? 'checked': '';  ?> >
                                                <?php echo xlt('Cancer'); ?>
                                              </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="checkbox">
                                              <label>
                                                <input type="checkbox" value="Diabetes" name="recipient_report[]" <?php echo (in_array('Diabetes', $recipient_report)) ? 'checked': '';  ?> >
                                                <?php echo xlt('Diabetes'); ?>
                                              </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="checkbox">
                                              <label>
                                                <input type="checkbox" value="Sleep problems" name="recipient_report[]" <?php echo (in_array('Sleep problems', $recipient_report)) ? 'checked': '';  ?> >
                                                <?php echo xlt('Sleep problems'); ?>
                                              </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="checkbox">
                                              <label>
                                                <input type="checkbox" value="Weight change" name="recipient_report[]" <?php echo (in_array('Weight change', $recipient_report)) ? 'checked': '';  ?> >
                                                <?php echo xlt('Weight change'); ?>
                                              </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="checkbox">
                                              <label>
                                                <input type="checkbox" value="Chronic pain" name="recipient_report[]" <?php echo (in_array('Chronic pain', $recipient_report)) ? 'checked': '';  ?> >
                                                <?php echo xlt('Chronic pain'); ?>
                                              </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="checkbox">
                                              <label>
                                                <input type="checkbox" value="Enuresis" name="recipient_report[]" <?php echo (in_array('Enuresis', $recipient_report)) ? 'checked': '';  ?> >
                                                <?php echo xlt('Enuresis/Encopresis'); ?>
                                              </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="checkbox">
                                              <label>
                                                <input type="checkbox" value="Allergies" name="recipient_report[]" <?php echo (in_array('Allergies', $recipient_report)) ? 'checked': '';  ?> >
                                                <?php echo xlt('Allergies'); ?>
                                              </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="checkbox">
                                              <label>
                                                <input type="checkbox" value="Hypertension" name="recipient_report[]" <?php echo (in_array('Hypertension', $recipient_report)) ? 'checked': '';  ?> >
                                                <?php echo xlt('Hypertension'); ?>
                                              </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="checkbox">
                                              <label>
                                                <input type="checkbox" value="Parasites" name="recipient_report[]" <?php echo (in_array('Parasites', $recipient_report)) ? 'checked': '';  ?> >
                                                <?php echo xlt('Parasites/scabies/lice'); ?>
                                              </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="checkbox">
                                              <label>
                                                <input type="checkbox" value="STD" name="recipient_report[]" <?php echo (in_array('STD', $recipient_report)) ? 'checked': '';  ?> >
                                                <?php echo xlt('STD'); ?>
                                              </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="checkbox">
                                              <label>
                                                <input type="checkbox" value="Seizures" name="recipient_report[]" <?php echo (in_array('Seizures', $recipient_report)) ? 'checked': '';  ?> >
                                                <?php echo xlt('Seizures'); ?>
                                              </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="checkbox">
                                              <label>
                                                <input type="checkbox" value="Kidney disease" name="recipient_report[]" <?php echo (in_array('Kidney disease', $recipient_report)) ? 'checked': '';  ?> >
                                                <?php echo xlt('Kidney disease'); ?>
                                              </label>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="checkbox">
                                              <label>
                                                <input type="checkbox" value="Appetite change" name="recipient_report[]" <?php echo (in_array('Appetite change', $recipient_report)) ? 'checked': '';  ?> >
                                                <?php echo xlt('Appetite change'); ?>
                                              </label>
                                            </div>
                                        </li>
                                    </ul>

                                    <div class="form-group">
                                        <div><strong><?php echo xlt('Medical history (explain any above marked):'); ?></strong></div>
                                        <textarea name="medical_history"  rows="3" class="form-control"><?php echo text($check_res['medical_history']); ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <div><strong><?php echo xlt('Allergies to medications and other substances:'); ?></strong></div>
                                        <textarea name="allergies_medication"  rows="3" class="form-control"><?php echo text($check_res['allergies_medication']); ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <div><strong><?php echo xlt('Family medical history:'); ?></strong></div>
                                        <textarea name="family_medical_history"  rows="3" class="form-control"><?php echo text($check_res['family_medical_history']); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <div><strong><?php echo xlt('Self-report of infectious diseases (previous or current):'); ?></strong></div>
                                        <textarea name="infectious_diseases"  rows="3" class="form-control"><?php echo text($check_res['infectious_diseases']); ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <div><strong><?php echo xlt('Medication history including past and current medications (what medications, purpose, dose, effects, compliance):'); ?></strong></div>
                                        <textarea name="medication_history"  rows="3" class="form-control"><?php echo text($check_res['medication_history']); ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label class="checkbox-inline"><strong><?php echo xlt('Name and contact information of current primary care physician: '); ?></strong></label>
                                        <label class="checkbox-inline">
                                          <input type="text" class="form-control" name="name_physician" value="<?php echo text($check_res['name_physician']); ?>"> 
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <label class="checkbox-inline"><strong><?php echo xlt('Health and Physical obtained from the medical provider'); ?></strong></label>
                                        <label class="checkbox-inline">
                                          <input type="radio"  name="obtain_medical_provider" value="Yes" <?php echo ($check_res['obtain_medical_provider'] == 'Yes') ? 'checked': ''; ?> > <?php echo xlt('Yes'); ?>
                                        </label>
                                        <label class="checkbox-inline">
                                          <input type="radio"  name="obtain_medical_provider" value="No" <?php echo ($check_res['obtain_medical_provider'] == 'No') ? 'checked': ''; ?> > <?php echo xlt('No'); ?>
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <label class="checkbox-inline"><strong><?php echo xlt('Name and contact information of current primary psychiatrist/medication manager: '); ?></strong></label>
                                        <label class="checkbox-inline">
                                          <input type="text" class="form-control" name="name_current_psychiatrist" value="<?php echo text($check_res['name_current_psychiatrist']); ?>"> 
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <label class="checkbox-inline"><strong><?php echo xlt('Name and contact information of past primary psychiatrist/medication manager: '); ?></strong></label>
                                        <label class="checkbox-inline">
                                          <input type="text" class="form-control" name="name_past_psychiatrist" value="<?php echo text($check_res['name_past_psychiatrist']); ?>"> 
                                        </label>
                                    </div>

                                </div>
                            </div>

                            <div class="clearfix">&nbsp;</div>

                            <!-- CURRENT ISSUES -->
                            <div class="col-md-12" style="margin-top: 20px">
                                <h3 class="text-center"><?php echo xlt('CURRENT ISSUES'); ?></h3>
                                <div style="margin-top: 10px">
                                    <h4><?php echo xlt('PSYCHIATRIC SECTION'); ?></h4>
                                    <div class="form-group">
                                        <div><strong><?php echo xlt('Presenting problem including current Symptoms of each diagnosis (current condition, onset, duration, and frequency including assessments administered and results):'); ?></strong></div>
                                        <textarea name="current_symptoms"  rows="3" class="form-control"><?php echo text($check_res['current_symptoms']); ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label class="checkbox-inline"><strong><?php echo xlt('CADIC Completed'); ?></strong></label>
                                        <label class="checkbox-inline">
                                          <input type="radio"  name="cadic_completed" value="Yes" <?php echo ($check_res['cadic_completed'] == 'Yes') ? 'checked': ''; ?> > <?php echo xlt('Yes'); ?>
                                        </label>
                                        <label class="checkbox-inline">
                                          <input type="radio"  name="cadic_completed" value="No" <?php echo ($check_res['cadic_completed'] == 'No') ? 'checked': ''; ?> > <?php echo xlt('No'); ?>
                                        </label>
                                        <label class="checkbox-inline">
                                          <input type="text"  name="cadic_completed_details" value="<?php echo text($check_res['cadic_completed_details']); ?>"  > 
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <label class="checkbox-inline"><strong><?php echo xlt('Child Depression Screening Completed'); ?></strong></label>
                                        <label class="checkbox-inline">
                                          <input type="radio"  name="child_depression" value="Yes" <?php echo ($check_res['child_depression'] == 'Yes') ? 'checked': ''; ?> > <?php echo xlt('Yes'); ?>
                                        </label>
                                        <label class="checkbox-inline">
                                          <input type="radio"  name="child_depression" value="No" <?php echo ($check_res['child_depression'] == 'No') ? 'checked': ''; ?> > <?php echo xlt('No'); ?>
                                        </label>
                                        <label class="checkbox-inline">
                                          <input type="text"  name="child_depression_details" value="<?php echo text($check_res['child_depression_details']); ?>"  > 
                                        </label>
                                    </div>

                                    <h5 style="margin-top: 20px"><strong><?php echo xlt('Risk Assessment'); ?></strong></h5>    
                                    <div class="form-group">
                                        
                                        <label class="checkbox-inline"><strong><?php echo xlt('Current Suicidal Ideation'); ?></strong></label>
                                        <label class="checkbox-inline">
                                          <input type="radio"  name="current_suicidal_ideation" value="No" <?php echo ($check_res['current_suicidal_ideation'] == 'No') ? 'checked': ''; ?> > <?php echo xlt('No'); ?>
                                        </label>
                                        <label class="checkbox-inline">
                                          <input type="radio"  name="current_suicidal_ideation" value="Yes" <?php echo ($check_res['current_suicidal_ideation'] == 'Yes') ? 'checked': ''; ?> > <?php echo xlt('Yes'); ?>
                                        </label>                                        
                                    </div>
                                    <div class="form-group">
                                        <label for=""><?php echo xlt('If Yes, please explain:'); ?></label>
                                        <textarea name="current_suicidal_ideation_details"  rows="3" class="form-control"><?php echo text($check_res['current_suicidal_ideation_details']); ?></textarea>
                                    </div>


                                    <div class="form-group">                                        
                                        <label class="checkbox-inline"><strong><?php echo xlt('Current Homicidal Ideation'); ?></strong></label>
                                        <label class="checkbox-inline">
                                          <input type="radio"  name="current_homicidal_ideation" value="No" <?php echo ($check_res['current_homicidal_ideation'] == 'No') ? 'checked': ''; ?> > <?php echo xlt('No'); ?>
                                        </label>
                                        <label class="checkbox-inline">
                                          <input type="radio"  name="current_homicidal_ideation" value="Yes" <?php echo ($check_res['current_homicidal_ideation'] == 'Yes') ? 'checked': ''; ?> > <?php echo xlt('Yes'); ?>
                                        </label>                                        
                                    </div>
                                    <div class="form-group">
                                        <label for=""><?php echo xlt('If Yes, please explain:'); ?></label>
                                        <textarea name="current_homicidal_ideation_details"  rows="3" class="form-control"><?php echo text($check_res['current_homicidal_ideation_details']); ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <div><strong><?php echo xlt('History of suicidal or homicidal ideation and/or attempts (include how many, when, and method):'); ?></strong></div>
                                        <textarea name="history_suicidal_attempts"  rows="3" class="form-control"><?php echo text($check_res['history_suicidal_attempts']); ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <div><strong><?php echo xlt('Safety Plan, if current risk is identified:'); ?></strong></div>
                                        <div class="radio" style="margin-top: 0">
                                          <label>
                                            <input type="radio" name="safety_plan" value="no_need" <?php echo ($check_res['safety_plan'] == 'no_need') ? 'checked': ''; ?> >
                                            Does not currently appear in need of a safety plan
                                          </label>
                                        </div>
                                        <div class="radio" style="margin-top: 0">
                                          <label>
                                            <input type="radio" name="safety_plan" value="presented_treatment" <?php echo ($check_res['safety_plan'] == 'presented_treatment') ? 'checked': ''; ?> >
                                            Presented to treatment with current safety plan and was reviewed during assessment
                                          </label>
                                        </div>
                                        <div class="radio" style="margin-top: 0">
                                          <label>
                                            <input type="radio" name="safety_plan" value="plan_developed" <?php echo ($check_res['safety_plan'] == 'plan_developed') ? 'checked': ''; ?> >
                                            Plan was developed
                                          </label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div><strong><?php echo xlt('Legal History (charges, probation/parole, CPS, divorse, legal custody, victim in criminal cases, etc.):'); ?></strong></div>
                                        <textarea name="legal_history"  rows="3" class="form-control"><?php echo text($check_res['legal_history']); ?></textarea>
                                    </div>

                                    <h5 style="margin-top: 20px"><strong><?php echo xlt('Family History'); ?></strong></h5>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="checkbox-inline"><strong><?php echo xlt('Client reports their family to be supportive'); ?></strong></label>
                                            <label class="checkbox-inline">
                                              <input type="radio"  name="client_family_support" value="Yes" <?php echo ($check_res['client_family_support'] == 'Yes') ? 'checked': ''; ?> > <?php echo xlt('Yes'); ?>
                                            </label>
                                            <label class="checkbox-inline">
                                              <input type="radio"  name="client_family_support" value="No" <?php echo ($check_res['client_family_support'] == 'No') ? 'checked': ''; ?> > <?php echo xlt('No'); ?>
                                            </label>                                        
                                        </div>

                                        <div class="form-group">
                                            <label class="checkbox-inline"><strong><?php echo xlt('Client currently lives with:'); ?></strong></label>
                                            <label class="checkbox-inline">
                                              <input type="text" class="form-control"  name="client_lives_with" value="<?php echo text($check_res['client_lives_with']); ?>"  >
                                            </label>                                                                             
                                        </div>

                                        <div class="form-group">
                                            <label class="checkbox-inline"><strong><?php echo xlt('Adult client grew up with:'); ?></strong></label>
                                            <label class="checkbox-inline">
                                              <input type="text" class="form-control"   name="adult_grew_with" value="<?php echo text($check_res['adult_grew_with']); ?>"  >
                                            </label>                                                                             
                                        </div>

                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="checkbox-inline"><strong><?php echo xlt('Client reports their childhood to be:'); ?></strong></label>
                                            <label class="checkbox-inline">
                                              <input type="text" class="form-control"   name="client_childhood" value="<?php echo text($check_res['client_childhood']); ?>"  >
                                            </label>                                                                             
                                        </div>
                                        <div class="form-group">
                                            <label class="checkbox-inline"><strong><?php echo xlt('Childhood punishments (past and present):'); ?></strong></label>
                                            <label class="checkbox-inline">
                                              <input type="text" class="form-control"   name="childhood_punishment" value="<?php echo text($check_res['childhood_punishment']); ?>"  >
                                            </label>                                                                             
                                        </div>

                                    </div>
                                    <div class="clearfix"></div>

                                    <div class="form-group">
                                        <div><strong><?php echo xlt('Other family information:'); ?></strong></div>
                                        <textarea name="other_family_info"  rows="3" class="form-control"><?php echo text($check_res['other_family_info']); ?></textarea>
                                    </div>
                                    

                                    <div class="form-group" style="margin-top: 20px">
                                        <label class="checkbox-inline"><strong><?php echo xlt('Religious Preference:'); ?></strong></label>
                                        <label class="checkbox-inline">
                                          <input type="text" class="form-control"   name="religious_preference" value="<?php echo text($check_res['religious_preference']); ?>"  >
                                        </label>                                                                             
                                    </div>
                                    <div class="form-group">
                                        <div><strong><?php echo xlt('Assessment of spiritual issues impacting treatment:'); ?></strong></div>
                                        <textarea name="assess_spiritual"  rows="3" class="form-control"><?php echo text($check_res['assess_spiritual']); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <div><strong><?php echo xlt('Assessment of vocational/educational issues impacting treatment:'); ?></strong></div>
                                        <textarea name="assess_vocational"  rows="3" class="form-control"><?php echo text($check_res['assess_vocational']); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <div><strong><?php echo xlt('Assessment of cultural issues impacting treatment:'); ?></strong></div>
                                        <textarea name="assess_cultural"  rows="3" class="form-control"><?php echo text($check_res['assess_cultural']); ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <div><strong><?php echo xlt('Assessment of client strengths and other resilience factors including social support network:'); ?></strong></div>
                                        <textarea name="assess_social"  rows="3" class="form-control"><?php echo text($check_res['assess_social']); ?></textarea>

                                        <label class="checkbox-inline">
                                          <input type="radio"  name="strength_identify" value="identify_easily" <?php echo ($check_res['strength_identify'] == 'identify_easily') ? 'checked': ''; ?> > <?php echo xlt('Client identified strengths easily'); ?>
                                        </label>
                                        <label class="checkbox-inline">
                                          <input type="radio"  name="strength_identify" value="identify_struggle" <?php echo ($check_res['strength_identify'] == 'identify_struggle') ? 'checked': ''; ?> > <?php echo xlt('Client struggled to identify strengths'); ?>
                                        </label>
                                    </div>

                                    <div class="form-group" style="margin-top: 20px">
                                        <div><strong><?php echo xlt('Client readiness to participate in treatment as well as current resources:'); ?></strong></div>

                                        <div style="padding-left: 20px">

                                            <label class="radio">
                                              <input type="radio"  name="client_readiness" value="identify_the_need" <?php echo ($check_res['client_readiness'] == 'identify_the_need') ? 'checked': ''; ?> > <?php echo xlt('Client identified the need for treatment as evidenced by: '); ?>
                                                <input type="text" name="client_identify_evidence" value="<?php echo text($check_res['client_identify_evidence']); ?>">
                                            </label>

                                            <label class="radio">
                                              <input type="radio"  name="client_readiness" value="struggle_identify_the_need" <?php echo ($check_res['client_readiness'] == 'struggle_identify_the_need') ? 'checked': ''; ?> > <?php echo xlt('Client struggled to identify the need for treatment as evidenced by: '); ?>
                                                <input type="text" name="client_struggle_evidence" value="<?php echo text($check_res['client_struggle_evidence']); ?>">
                                            </label>
                                            
                                        </div>
                                        
                                    </div>

                                    <div class="form-group">
                                        <div><strong><?php echo xlt('Activities needed to improve the member\'s readiness for treatment:'); ?></strong></div>
                                        <textarea name="activities_readiness" rows="3" class="form-control"><?php echo text($check_res['activities_readiness']); ?></textarea>
                                    </div>


                                </div>


                                <div style="margin-top: 30px">
                                    <h4><?php echo xlt('ALCOHOL/DRUGS HISTORY'); ?></h4>

                                    <!-- Illegal drug use/abuse -->
                                    <div class="rows">
                                        <div class="col-sm-3">
                                            <strong><?php echo xlt('Illegal drug use/abuse'); ?></strong>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label class="checkbox-inline"><strong><?php echo xlt('Current'); ?></strong></label>
                                                <label class="checkbox-inline">
                                                  <input type="radio"  name="illegal_drug_current" value="Yes" <?php echo ($check_res['illegal_drug_current'] == 'Yes') ? 'checked': ''; ?> > <?php echo xlt('Yes'); ?>
                                                </label>
                                                <label class="checkbox-inline">
                                                  <input type="radio"  name="illegal_drug_current" value="No" <?php echo ($check_res['illegal_drug_current'] == 'No') ? 'checked': ''; ?> > <?php echo xlt('No'); ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label class="checkbox-inline"><strong><?php echo xlt('Past 12 Months:'); ?></strong></label>
                                                <label class="checkbox-inline">
                                                  <input type="radio"  name="illegal_drug_past_12" value="Yes" <?php echo ($check_res['illegal_drug_past_12'] == 'Yes') ? 'checked': ''; ?> > <?php echo xlt('Yes'); ?>
                                                </label>
                                                <label class="checkbox-inline">
                                                  <input type="radio"  name="illegal_drug_past_12" value="No" <?php echo ($check_res['illegal_drug_past_12'] == 'No') ? 'checked': ''; ?> > <?php echo xlt('No'); ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label class="checkbox-inline"><strong><?php echo xlt('Lifetime:'); ?></strong></label>
                                                <label class="checkbox-inline">
                                                  <input type="radio"  name="illegal_drug_lifetime" value="Yes" <?php echo ($check_res['illegal_drug_lifetime'] == 'Yes') ? 'checked': ''; ?> > <?php echo xlt('Yes'); ?>
                                                </label>
                                                <label class="checkbox-inline">
                                                  <input type="radio"  name="illegal_drug_lifetime" value="No" <?php echo ($check_res['illegal_drug_lifetime'] == 'No') ? 'checked': ''; ?> > <?php echo xlt('No'); ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Prescriptive drug use/abuse -->
                                    <div class="rows">
                                        <div class="col-sm-3">
                                            <strong><?php echo xlt('Prescriptive drug use/abuse'); ?></strong>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label class="checkbox-inline"><strong><?php echo xlt('Current:'); ?></strong></label>
                                                <label class="checkbox-inline">
                                                  <input type="radio"  name="prescriptive_drug_current" value="Yes" <?php echo ($check_res['prescriptive_drug_current'] == 'Yes') ? 'checked': ''; ?> > <?php echo xlt('Yes'); ?>
                                                </label>
                                                <label class="checkbox-inline">
                                                  <input type="radio"  name="prescriptive_drug_current" value="No" <?php echo ($check_res['prescriptive_drug_current'] == 'No') ? 'checked': ''; ?> > <?php echo xlt('No'); ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label class="checkbox-inline"><strong><?php echo xlt('Past 12 Months:'); ?></strong></label>
                                                <label class="checkbox-inline">
                                                  <input type="radio"  name="prescriptive_drug_past_12" value="Yes" <?php echo ($check_res['prescriptive_drug_past_12'] == 'Yes') ? 'checked': ''; ?> > <?php echo xlt('Yes'); ?>
                                                </label>
                                                <label class="checkbox-inline">
                                                  <input type="radio"  name="prescriptive_drug_past_12" value="No" <?php echo ($check_res['prescriptive_drug_past_12'] == 'No') ? 'checked': ''; ?> > <?php echo xlt('No'); ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label class="checkbox-inline"><strong><?php echo xlt('Lifetime:'); ?></strong></label>
                                                <label class="checkbox-inline">
                                                  <input type="radio"  name="prescriptive_drug_lifetime" value="Yes" <?php echo ($check_res['prescriptive_drug_lifetime'] == 'Yes') ? 'checked': ''; ?> > <?php echo xlt('Yes'); ?>
                                                </label>
                                                <label class="checkbox-inline">
                                                  <input type="radio"  name="prescriptive_drug_lifetime" value="No" <?php echo ($check_res['prescriptive_drug_lifetime'] == 'No') ? 'checked': ''; ?> > <?php echo xlt('No'); ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Alcohol drug use/abuse -->
                                    <div class="rows" style="margin-bottom: 20px">
                                        <div class="col-sm-3">
                                            <strong><?php echo xlt('Alcohol drug use/abuse'); ?></strong>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label class="checkbox-inline"><strong><?php echo xlt('Current:'); ?></strong></label>
                                                <label class="checkbox-inline">
                                                  <input type="radio"  name="alcohol_drug_current" value="Yes" <?php echo ($check_res['alcohol_drug_current'] == 'Yes') ? 'checked': ''; ?> > <?php echo xlt('Yes'); ?>
                                                </label>
                                                <label class="checkbox-inline">
                                                  <input type="radio"  name="alcohol_drug_current" value="No" <?php echo ($check_res['alcohol_drug_current'] == 'No') ? 'checked': ''; ?> > <?php echo xlt('No'); ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label class="checkbox-inline"><strong><?php echo xlt('Past 12 Months:'); ?></strong></label>
                                                <label class="checkbox-inline">
                                                  <input type="radio"  name="alcohol_drug_past_12" value="Yes" <?php echo ($check_res['alcohol_drug_past_12'] == 'Yes') ? 'checked': ''; ?> > <?php echo xlt('Yes'); ?>
                                                </label>
                                                <label class="checkbox-inline">
                                                  <input type="radio"  name="alcohol_drug_past_12" value="No" <?php echo ($check_res['alcohol_drug_past_12'] == 'No') ? 'checked': ''; ?> > <?php echo xlt('No'); ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label class="checkbox-inline"><strong><?php echo xlt('Lifetime:'); ?></strong></label>
                                                <label class="checkbox-inline">
                                                  <input type="radio"  name="alcohol_drug_lifetime" value="Yes" <?php echo ($check_res['alcohol_drug_lifetime'] == 'Yes') ? 'checked': ''; ?> > <?php echo xlt('Yes'); ?>
                                                </label>
                                                <label class="checkbox-inline">
                                                  <input type="radio"  name="alcohol_drug_lifetime" value="No" <?php echo ($check_res['alcohol_drug_lifetime'] == 'No') ? 'checked': ''; ?> > <?php echo xlt('No'); ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="clearfix"></div>

                                    <div class="" style="width: 100%; padding-top: 20px;">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th><?php echo xlt('Substance'); ?></th>
                                                    <th><?php echo xlt('Age first use'); ?></th>
                                                    <th><?php echo xlt('Age last use'); ?></th>
                                                    <th><?php echo xlt('Frequency'); ?></th>
                                                    <th><?php echo xlt('Amount'); ?></th>
                                                    <th><?php echo xlt('Method'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><?php echo xlt('Caffeine'); ?></td>
                                                    <td>
                                                        <input type="text" name="caffeine_age_first" value="<?php echo text($check_res['caffeine_age_first']); ?>" style="width: 140px">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="caffeine_age_last" value="<?php echo text($check_res['caffeine_age_last']); ?>" style="width: 140px" >
                                                    </td>
                                                    <td>
                                                        <input type="text" name="caffeine_frequency" value="<?php echo text($check_res['caffeine_frequency']); ?>" style="width: 140px" >
                                                    </td>
                                                    <td>
                                                        <input type="text" name="caffeine_amount" value="<?php echo text($check_res['caffeine_amount']); ?>" style="width: 140px" >
                                                    </td>
                                                    <td>
                                                        <input type="text" name="caffeine_method" value="<?php echo text($check_res['caffeine_method']); ?>" style="width: 140px">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><?php echo xlt('Tobacco'); ?></td>
                                                    <td>
                                                        <input type="text" name="tobacco_age_first" value="<?php echo text($check_res['tobacco_age_first']); ?>" style="width: 140px" >
                                                    </td>
                                                    <td>
                                                        <input type="text" name="tobacco_age_last" value="<?php echo text($check_res['tobacco_age_last']); ?>" style="width: 140px" >
                                                    </td>
                                                    <td>
                                                        <input type="text" name="tobacco_frequency" value="<?php echo text($check_res['tobacco_frequency']); ?>" style="width: 140px" >
                                                    </td>
                                                    <td>
                                                        <input type="text" name="tobacco_amount" value="<?php echo text($check_res['tobacco_amount']); ?>" style="width: 140px" >
                                                    </td>
                                                    <td>
                                                        <input type="text" name="tobacco_method" value="<?php echo text($check_res['tobacco_method']); ?>" style="width: 140px" >
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><?php echo xlt('Alcohol'); ?></td>
                                                    <td>
                                                        <input type="text" name="alcohol_age_first" value="<?php echo text($check_res['alcohol_age_first']); ?>" style="width: 140px">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="alcohol_age_last" value="<?php echo text($check_res['alcohol_age_last']); ?>" style="width: 140px">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="alcohol_frequency" value="<?php echo text($check_res['alcohol_frequency']); ?>" style="width: 140px">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="alcohol_amount" value="<?php echo text($check_res['alcohol_amount']); ?>" style="width: 140px">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="alcohol_method" value="<?php echo text($check_res['alcohol_method']); ?>" style="width: 140px">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><?php echo xlt('Prescription drugs'); ?>
                                                        <input type="text" name="prescription_drugs" value="<?php echo text($check_res['prescription_drugs']); ?>" style="width: 140px" >
                                                    </td>
                                                    <td>
                                                        <input type="text" name="prescription_age_first" value="<?php echo text($check_res['prescription_age_first']); ?>" style="width: 140px" >
                                                    </td>
                                                    <td>
                                                        <input type="text" name="prescription_age_last" value="<?php echo text($check_res['prescription_age_last']); ?>" style="width: 140px" >
                                                    </td>
                                                    <td>
                                                        <input type="text" name="prescription_frequency" value="<?php echo text($check_res['prescription_frequency']); ?>" style="width: 140px" >
                                                    </td>
                                                    <td>
                                                        <input type="text" name="prescription_amount" value="<?php echo text($check_res['prescription_amount']); ?>" style="width: 140px" >
                                                    </td>
                                                    <td>
                                                        <input type="text" name="prescription_method" value="<?php echo text($check_res['prescription_method']); ?>" style="width: 140px" >
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><?php echo xlt('Other'); ?></td>
                                                    <td>
                                                        <input type="text" name="other1_age_first" value="<?php echo text($check_res['other1_age_first']); ?>" style="width: 140px" >
                                                    </td>
                                                    <td>
                                                        <input type="text" name="other1_age_last" value="<?php echo text($check_res['other1_age_last']); ?>" style="width: 140px" >
                                                    </td>
                                                    <td>
                                                        <input type="text" name="other1_frequency" value="<?php echo text($check_res['other1_frequency']); ?>" style="width: 140px" >
                                                    </td>
                                                    <td>
                                                        <input type="text" name="other1_amount" value="<?php echo text($check_res['other1_amount']); ?>" style="width: 140px" >
                                                    </td>
                                                    <td>
                                                        <input type="text" name="other1_method" value="<?php echo text($check_res['other1_method']); ?>" style="width: 140px" >
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><?php echo xlt('Other'); ?></td>
                                                    <td>
                                                        <input type="text" name="other2_age_first" value="<?php echo text($check_res['other2_age_first']); ?>" style="width: 140px" >
                                                    </td>
                                                    <td>
                                                        <input type="text" name="other2_age_last" value="<?php echo text($check_res['other2_age_last']); ?>" style="width: 140px" >
                                                    </td>
                                                    <td>
                                                        <input type="text" name="other2_frequency" value="<?php echo text($check_res['other2_frequency']); ?>" style="width: 140px" >
                                                    </td>
                                                    <td>
                                                        <input type="text" name="other2_amount" value="<?php echo text($check_res['other2_amount']); ?>" style="width: 140px" >
                                                    </td>
                                                    <td>
                                                        <input type="text" name="other2_method" value="<?php echo text($check_res['other2_method']); ?>" style="width: 140px" >
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><?php echo xlt('Other'); ?></td>
                                                    <td>
                                                        <input type="text" name="other3_age_first" value="<?php echo text($check_res['other3_age_first']); ?>" style="width: 140px" >
                                                    </td>
                                                    <td>
                                                        <input type="text" name="other3_age_last" value="<?php echo text($check_res['other3_age_last']); ?>" style="width: 140px" >
                                                    </td>
                                                    <td>
                                                        <input type="text" name="other3_frequency" value="<?php echo text($check_res['other3_frequency']); ?>" style="width: 140px" >
                                                    </td>
                                                    <td>
                                                        <input type="text" name="other3_amount" value="<?php echo text($check_res['other3_amount']); ?>" style="width: 140px" >
                                                    </td>
                                                    <td>
                                                        <input type="text" name="other3_method" value="<?php echo text($check_res['other3_method']); ?>" style="width: 140px" >
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="rows" style="margin: 20px 0 20px">
                                        <h5><strong><?php echo xlt('Referral for Community Based Services'); ?></strong></h5>
                                        <?php $referral_comm_services = explode('|', $check_res['referral_comm_services']); ?>
                                        <div class="form-group">
                                            <div class="checkbox">
                                              <label>
                                                <input type="checkbox" value="send_referral_cbs" name="referral_comm_services[]" <?php echo (in_array('send_referral_cbs', $referral_comm_services)) ? 'checked':''; ?> >
                                                Send Referral to CBS services
                                              </label>
                                            </div>
                                            <div class="checkbox">
                                              <label>
                                                <input type="checkbox" value="not_applicable" name="referral_comm_services[]" <?php echo (in_array('not_applicable', $referral_comm_services)) ? 'checked':''; ?> >
                                                Not Applicable - not eligible for services as CBRS, CM, Peer Support, etc. Not a behavior disorder such as ADHD, ODD, CDD, etc. or a persistent and severe diagnosis such as Depression, Anxiety, Bipolar, PTSD Schizophrenic, Personality Disorder, etc. 
                                              </label>
                                            </div>
                                            <div class="checkbox">
                                              <label>
                                                <input type="checkbox" value="send_referral_development" name="referral_comm_services[]" <?php echo (in_array('send_referral_development', $referral_comm_services)) ? 'checked':''; ?> >
                                                Send Referral to Developmental Disability Services - has disability diagnosis such as Spectrum Disorder, Delayed Intellectual Functioning, Intellectual Disability, etc.
                                              </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- MENTAL STATUS EXAM -->
                            <div class="col-md-12" style="margin-top: 30px">
                                <h3 class="text-center"><?php echo xlt('MENTAL STATUS EXAM'); ?></h3>
                                <div class="bs-example bs-example-tabs" data-example-id="togglable-tabs" style="border: 1px solid #aaa; padding: 10px 20px"> 
                                    <ul class="nav nav-tabs" id="myTabs" role="tablist"> 
                                        <li role="presentation" class="active">
                                            <a href="#home" id="home-tab" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="true"><strong><?php echo xlt('General Observations'); ?></strong></a>
                                        </li> 
                                        <li role="presentation" class="">
                                            <a href="#thinking" role="tab" id="thinking-tab" data-toggle="tab" aria-controls="thinking" aria-expanded="false"><strong><?php echo xlt('Thinking'); ?></strong></a>
                                        </li> 
                                        <li role="presentation" class="">
                                            <a href="#emotion" role="tab" id="emotion-tab" data-toggle="tab" aria-controls="emotion" aria-expanded="false"><strong><?php echo xlt('Emotion'); ?></strong></a>
                                        </li>
                                        <li role="presentation" class="">
                                            <a href="#cognition" role="tab" id="cognition-tab" data-toggle="tab" aria-controls="cognition" aria-expanded="false"><strong><?php echo xlt('Cognition'); ?></strong></a>
                                        </li>
                                        
                                    </ul> 

                                    <div class="tab-content" id="myTabContent"> 
                                        <div class="tab-pane fade active in" role="tabpanel" id="home" aria-labelledby="home-tab" style="padding: 10px"> 

                                             <h4><?php echo xlt('General Observations'); ?></h4>

                                            <div class="rows">
                                                <?php $appearance_weight = explode('|', $check_res['appearance_weight']); ?>
                                                <div class="col-sm-3">
                                                    <strong><?php echo xlt('Appearance'); ?></strong>
                                                </div>
                                                <div class="col-sm-3">
                                                    <strong><?php echo xlt('Weight'); ?></strong>
                                                    <div class="form-group">
                                                        <label class="">
                                                          <input type="checkbox" name="appearance_weight[]" value="Underweight"  <?php echo (in_array('Underweight',$appearance_weight)) ? 'checked':''; ?> > <?php echo xlt('Underweight'); ?>
                                                        </label>
                                                        <label class="">
                                                          <input type="checkbox" name="appearance_weight[]" value="Overweight" <?php echo (in_array('Overweight',$appearance_weight)) ? 'checked':''; ?> > <?php echo xlt('Overweight'); ?>
                                                        </label>
                                                        <label class="">
                                                          <input type="checkbox" name="appearance_weight[]" value="WNL" <?php echo (in_array('WNL',$appearance_weight)) ? 'checked':''; ?> > <?php echo xlt('WNL'); ?>
                                                        </label>
                                                        <label class="">
                                                          <input type="checkbox" name="appearance_weight[]" value="Other" <?php echo (in_array('Other',$appearance_weight)) ? 'checked':''; ?> > <?php echo xlt('Other'); ?>
                                                        </label>
                                                        <input type="text" name="appearance_weight_other" value="<?php echo text($check_res['appearance_weight_other']); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <?php $appearance_hygiene = explode('|', $check_res['appearance_hygiene']); ?>
                                                    <strong><?php echo xlt('Hygiene/Grooming'); ?></strong>
                                                    <div class="form-group">
                                                        <label class="">
                                                          <input type="checkbox" name="appearance_hygiene[]" value="Neat" <?php echo (in_array('Neat',$appearance_hygiene)) ? 'checked':''; ?> > <?php echo xlt('Neat'); ?>
                                                        </label>
                                                        <label class="">
                                                          <input type="checkbox" name="appearance_hygiene[]" value="Disheveled" <?php echo (in_array('Disheveled',$appearance_hygiene)) ? 'checked':''; ?> > <?php echo xlt('Disheveled'); ?>
                                                        </label>
                                                        <label class="">
                                                          <input type="checkbox" name="appearance_hygiene[]" value="Unkempt" <?php echo (in_array('Unkempt',$appearance_hygiene)) ? 'checked':''; ?> > <?php echo xlt('Unkempt'); ?>
                                                        </label>
                                                        <label class="">
                                                          <input type="checkbox" name="appearance_hygiene[]" value="Dirty" <?php echo (in_array('Dirty',$appearance_hygiene)) ? 'checked':''; ?> > <?php echo xlt('Dirty'); ?>
                                                        </label>
                                                        <label class="">
                                                          <input type="checkbox" name="appearance_hygiene[]" value="Other" <?php echo (in_array('Other',$appearance_hygiene)) ? 'checked':''; ?> > <?php echo xlt('Other'); ?>
                                                        </label>
                                                        <input type="text" name="appearance_hygiene_other" value="<?php echo text($check_res['appearance_hygiene_other']); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <strong><?php echo xlt('Dressed appropriate for age and season:'); ?></strong>
                                                    <div class="form-group">
                                                        <label class="">
                                                          <input type="radio" name="appearance_dress" value="Yes" <?php echo ($check_res['appearance_dress'] == 'Yes') ? 'checked':''; ?> > <?php echo xlt('Yes'); ?>
                                                        </label>
                                                        <label class="">
                                                          <input type="radio" name="appearance_dress" value="No" <?php echo ($check_res['appearance_dress'] == 'No') ? 'checked':''; ?> > <?php echo xlt('No'); ?>
                                                        </label>
                                                        <input type="text" name="appearance_dress_other" value="<?php echo text($check_res['appearance_dress_other']); ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="clearfix"></div>

                                            <!-- Mental Speech -->
                                            <div class="rows" style="border-top: 1px solid #888; padding-top: 20px; margin-top: 20px;">
                                                <div class="col-sm-3">
                                                    <strong><?php echo xlt('Speech'); ?></strong>
                                                </div>
                                                <div class="col-sm-3">
                                                    <?php $speech_rate = explode('|', $check_res['speech_rate']); ?>
                                                    <strong><?php echo xlt('Rate'); ?></strong>
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="speech_rate[]" value="Slow" <?php echo (in_array('Slow', $speech_rate)) ? 'checked':''; ?> > <?php echo xlt('Slow'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="speech_rate[]" value="Rapid" <?php echo (in_array('Rapid', $speech_rate)) ? 'checked':''; ?> > <?php echo xlt('Rapid'); ?>
                                                        </label>
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="speech_rate[]" value="Pauses" <?php echo (in_array('Pauses', $speech_rate)) ? 'checked':''; ?> > <?php echo xlt('Pauses'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="speech_rate[]" value="WNL" <?php echo (in_array('WNL', $speech_rate)) ? 'checked':''; ?> > <?php echo xlt('WNL'); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <?php $speech_volume = explode('|', $check_res['speech_volume']); ?>
                                                    <strong><?php echo xlt('Volume'); ?></strong>
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="speech_volume[]" value="Loud" <?php echo (in_array('Loud', $speech_volume)) ? 'checked':''; ?> > <?php echo xlt('Loud'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="speech_volume[]" value="Soft" <?php echo (in_array('Soft', $speech_volume)) ? 'checked':''; ?> > <?php echo xlt('Soft'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="speech_volume[]" value="Whisper" <?php echo (in_array('Whisper', $speech_volume)) ? 'checked':''; ?> > <?php echo xlt('Whisper'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="speech_volume[]" value="WNL" <?php echo (in_array('WNL', $speech_volume)) ? 'checked':''; ?> > <?php echo xlt('WNL'); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">                                        
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="speech_volume[]" value="Pressured" <?php echo (in_array('Pressured', $speech_volume)) ? 'checked':''; ?> > <?php echo xlt('Pressured'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="speech_volume[]" value="Slurred" <?php echo (in_array('Slurred', $speech_volume)) ? 'checked':''; ?> > <?php echo xlt('Slurred'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="speech_volume[]" value="Stuttering" <?php echo (in_array('Stuttering', $speech_volume)) ? 'checked':''; ?> > <?php echo xlt('Stuttering'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="speech_volume[]" value="Talking to self" <?php echo (in_array('Talking to self', $speech_volume)) ? 'checked':''; ?> > <?php echo xlt('Talking to self'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="speech_volume[]" value="Controlled Speech Impediment" <?php echo (in_array('Controlled Speech Impediment', $speech_volume)) ? 'checked':''; ?> > <?php echo xlt('Controlled Speech Impediment'); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>

                                            <!-- Mental Motor Activity -->
                                            <div class="rows" style="border-top: 1px solid #888; padding-top: 20px; margin-top: 20px;">
                                                <?php $motor_activity = explode('|', $check_res['motor_activity']); ?>
                                                <div class="col-sm-3">
                                                    <strong><?php echo xlt('Motor Activity'); ?></strong>
                                                </div>
                                                <div class="col-sm-3">                                        
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="motor_activity[]" value="Normal" <?php echo (in_array('Normal', $motor_activity)) ? 'checked':''; ?> > <?php echo xlt('Normal'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="motor_activity[]" value="Abnormal Movements" <?php echo (in_array('Abnormal Movements', $motor_activity)) ? 'checked':''; ?> > <?php echo xlt('Abnormal Movements'); ?>
                                                        </label>
                                                        
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">                                        
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="motor_activity[]" value="Decreased" <?php echo (in_array('Decreased', $motor_activity)) ? 'checked':''; ?> > <?php echo xlt('Decreased'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="motor_activity[]" value="Increased" <?php echo (in_array('Increased', $motor_activity)) ? 'checked':''; ?> > <?php echo xlt('Increased'); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="motor_activity[]" value="Catatonia" <?php echo (in_array('Catatonia', $motor_activity)) ? 'checked':''; ?> > <?php echo xlt('Catatonia'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="motor_activity[]" value="Agitation" <?php echo (in_array('Agitation', $motor_activity)) ? 'checked':''; ?> > <?php echo xlt('Agitation/restless'); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>

                                            <!-- Mental Eye Contact -->
                                            <div class="rows" style="border-top: 1px solid #888; padding-top: 20px; margin-top: 20px;">
                                                <?php $eye_contact = explode('|', $check_res['eye_contact']); ?>
                                                <div class="col-sm-3">
                                                    <strong><?php echo xlt('Eye Contact'); ?></strong>
                                                </div>
                                                <div class="col-sm-3">                                        
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="eye_contact[]" value="Normal" <?php echo (in_array('Normal', $eye_contact)) ? 'checked':''; ?> > <?php echo xlt('Normal'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="eye_contact[]" value="Inconsistent" <?php echo (in_array('Inconsistent', $eye_contact)) ? 'checked':''; ?> > <?php echo xlt('Inconsistent'); ?>
                                                        </label>                                            
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">                                        
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="eye_contact[]" value="Decreased" <?php echo (in_array('Decreased', $eye_contact)) ? 'checked':''; ?> > <?php echo xlt('Decreased'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="eye_contact[]" value="Excessive" <?php echo (in_array('Excessive', $eye_contact)) ? 'checked':''; ?> > <?php echo xlt('Excessive'); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="eye_contact[]" value="Avoidant" <?php echo (in_array('Avoidant', $eye_contact)) ? 'checked':''; ?> > <?php echo xlt('Avoidant'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="eye_contact[]" value="Intrusive" <?php echo (in_array('Intrusive', $eye_contact)) ? 'checked':''; ?> > <?php echo xlt('Intrusive'); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>

                                            <!-- Mental Cooperativeness -->
                                            <div class="rows" style="border-top: 1px solid #888; padding-top: 20px; margin-top: 20px;">
                                                <?php $cooperativeness = explode('|', $check_res['cooperativeness']); ?>
                                                <div class="col-sm-3">
                                                    <strong><?php echo xlt('Cooperativeness'); ?></strong>
                                                </div>
                                                <div class="col-sm-3">                                        
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="cooperativeness[]" value="Cooperative" <?php echo (in_array('Cooperative', $cooperativeness)) ? 'checked':''; ?> > <?php echo xlt('Cooperative'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="cooperativeness[]" value="Uncooperative" <?php echo (in_array('Uncooperative', $cooperativeness)) ? 'checked':''; ?> > <?php echo xlt('Uncooperative'); ?>
                                                        </label>                                            
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">                                        
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="cooperativeness[]" value="Hostile" <?php echo (in_array('Hostile', $cooperativeness)) ? 'checked':''; ?> > <?php echo xlt('Hostile'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="cooperativeness[]" value="Reluctant" <?php echo (in_array('Reluctant', $cooperativeness)) ? 'checked':''; ?> > <?php echo xlt('Reluctant'); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label class=""><?php echo xlt('Other:'); ?></label>                                        
                                                        <input type="text" name="cooperativeness_other" value="<?php echo text($check_res['cooperativeness_other']); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div> <!-- home-tab -->

                                        <div class="tab-pane fade" role="tabpanel" id="thinking" aria-labelledby="thinking-tab" style="padding: 10px"> 
                                             <h4><?php echo xlt('Thinking'); ?></h4>

                                            <!-- Thought Process -->
                                            <div class="rows" style="border-top: 1px solid #888; padding-top: 20px; margin-top: 20px;">
                                                <?php $thought_process = explode('|', $check_res['thought_process']); ?>
                                                <div class="col-sm-3">
                                                    <strong><?php echo xlt('Thought Process'); ?></strong>
                                                </div>
                                                <div class="col-sm-3">                                        
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="thought_process[]" value="Logical" <?php echo (in_array('Logical', $thought_process)) ? 'checked':'';  ?> > <?php echo xlt('Logical/Coherent'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="thought_process[]" value="Vague" <?php echo (in_array('Vague', $thought_process)) ? 'checked':'';  ?> > <?php echo xlt('Vague'); ?>
                                                        </label>     
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="thought_process[]" value="Incoherent"  <?php echo (in_array('Incoherent', $thought_process)) ? 'checked':'';  ?> > <?php echo xlt('Incoherent'); ?>
                                                        </label>                                       
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">                                        
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="thought_process[]" value="Disorganized" <?php echo (in_array('Disorganized', $thought_process)) ? 'checked':'';  ?> > <?php echo xlt('Disorganized'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="thought_process[]" value="Bizarre" <?php echo (in_array('Bizarre', $thought_process)) ? 'checked':'';  ?> > <?php echo xlt('Bizarre'); ?>
                                                        </label>
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="thought_process[]" value="Repeated Thought" <?php echo (in_array('Repeated Thought', $thought_process)) ? 'checked':'';  ?> > <?php echo xlt('Repeated Thought'); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="thought_process[]" value="Tangential" <?php echo (in_array('Tangential', $thought_process)) ? 'checked':'';  ?> > <?php echo xlt('Tangential'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="thought_process[]" value="Distracted" <?php echo (in_array('Distracted', $thought_process)) ? 'checked':'';  ?> > <?php echo xlt('Distracted'); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>

                                            <!-- Thought Content -->
                                            <div class="rows" style="border-top: 1px solid #888; padding-top: 20px; margin-top: 20px;">
                                                <?php $thought_content = explode('|', $check_res['thought_content']); ?>
                                                <div class="col-sm-3">
                                                    <strong><?php echo xlt('Thought Content'); ?></strong>
                                                </div>
                                                <div class="col-sm-3">                                        
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="thought_content[]" value="Appropriate" <?php echo (in_array('Appropriate', $thought_content)) ? 'checked':'';  ?> > <?php echo xlt('Appropriate'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="thought_content[]" value="Future Oriented" <?php echo (in_array('Future Oriented', $thought_content)) ? 'checked':'';  ?> > <?php echo xlt('Future Oriented'); ?>
                                                        </label>     
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="thought_content[]" value="Ruminating"  <?php echo (in_array('Ruminating', $thought_content)) ? 'checked':'';  ?> > <?php echo xlt('Ruminating'); ?>
                                                        </label>  
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="thought_content[]" value="Obsessions"  <?php echo (in_array('Obsessions', $thought_content)) ? 'checked':'';  ?> > <?php echo xlt('Obsessions'); ?>
                                                        </label>                                      
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">                                        
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="thought_content[]" value="Depersonalization" <?php echo (in_array('Depersonalization', $thought_content)) ? 'checked':'';  ?> > <?php echo xlt('Depersonalization'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="thought_content[]" value="Fears" <?php echo (in_array('Fears', $thought_content)) ? 'checked':'';  ?> > <?php echo xlt('Fears/Phobias'); ?>
                                                        </label>
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="thought_content[]" value="Self-Harm" <?php echo (in_array('Self-Harm', $thought_content)) ? 'checked':'';  ?> > <?php echo xlt('Self-Harm'); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label class="checkbox" style="margin-bottom: 0">
                                                          <input type="checkbox" name="thought_content[]" value="Suicidal" <?php echo (in_array('Suicidal', $thought_content)) ? 'checked':'';  ?> > <?php echo xlt('Suicidal/Homicidal Ideation'); ?>
                                                        </label>  
                                                        <div style="padding-left: 20px; margin-top: -7px">
                                                            <label class="checkbox">
                                                              <input type="checkbox" name="thought_content[]" value="Plan" <?php echo (in_array('Plan', $thought_content)) ? 'checked':'';  ?> > <?php echo xlt('Plan'); ?>
                                                            </label>
                                                            <label class="checkbox">
                                                              <input type="checkbox" name="thought_content[]" value="Means" <?php echo (in_array('Means', $thought_content)) ? 'checked':'';  ?> > <?php echo xlt('Means'); ?>
                                                            </label>
                                                            <label class="checkbox">
                                                              <input type="checkbox" name="thought_content[]" value="Able to Contract" <?php echo (in_array('Able to Contract', $thought_content)) ? 'checked':'';  ?> > <?php echo xlt('Able to Contract'); ?>
                                                            </label>                                                
                                                        </div>                                      
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>


                                            <!-- Perception -->
                                            <div class="rows" style="border-top: 1px solid #888; padding-top: 20px; margin-top: 20px;">
                                                <?php $thought_perception = explode('|', $check_res['thought_perception']); ?>
                                                <div class="col-sm-3">
                                                    <strong><?php echo xlt('Perception'); ?></strong>
                                                </div>
                                                <div class="col-sm-3">                                        
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="thought_perception[]" value="Appropriate" <?php echo (in_array('Appropriate', $thought_perception)) ? 'checked':'';  ?> > <?php echo xlt('Appropriate'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="thought_perception[]" value="Distorted" <?php echo (in_array('Distorted', $thought_perception)) ? 'checked':'';  ?> > <?php echo xlt('Distorted'); ?>
                                                        </label>     
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="thought_perception[]" value="Inconsistent"  <?php echo (in_array('Inconsistent', $thought_perception)) ? 'checked':'';  ?> > <?php echo xlt('Inconsistent'); ?>
                                                        </label>  
                                                                                        
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">                                        
                                                    <div class="form-group">
                                                        <label class="checkbox" style="margin-bottom: 0">
                                                          <input type="checkbox" name="thought_perception[]" value="Delusions" <?php echo (in_array('Delusions', $thought_perception)) ? 'checked':'';  ?> > <?php echo xlt('Delusions'); ?>
                                                        </label>      
                                                        <div style="padding-left: 20px; margin-top: -7px">
                                                            <label class="checkbox">
                                                              <input type="checkbox" name="thought_perception[]" value="Paranoid" <?php echo (in_array('Paranoid', $thought_perception)) ? 'checked':'';  ?> > <?php echo xlt('Paranoid'); ?>
                                                            </label>
                                                            <label class="checkbox">
                                                              <input type="checkbox" name="thought_perception[]" value="Grandiose" <?php echo (in_array('Grandiose', $thought_perception)) ? 'checked':'';  ?> > <?php echo xlt('Grandiose'); ?>
                                                            </label>
                                                            <label class="checkbox">
                                                              <input type="checkbox" name="thought_perception[]" value="Bizarre" <?php echo (in_array('Bizarre', $thought_perception)) ? 'checked':'';  ?> > <?php echo xlt('Bizarre'); ?>
                                                            </label>
                                                        </div>                                  
                                                        
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label class="checkbox" style="margin-bottom: 0">
                                                          <input type="checkbox" name="thought_perception[]" value="Hallucinations" <?php echo (in_array('Hallucinations', $thought_perception)) ? 'checked':'';  ?> > <?php echo xlt('Hallucinations'); ?>
                                                        </label>  
                                                        <div style="padding-left: 20px; margin-top: -7px">
                                                            <label class="checkbox">
                                                              <input type="checkbox" name="thought_perception[]" value="Auditory" <?php echo (in_array('Auditory', $thought_perception)) ? 'checked':'';  ?> > <?php echo xlt('Auditory'); ?>
                                                            </label>
                                                            <label class="checkbox">
                                                              <input type="checkbox" name="thought_perception[]" value="Visual" <?php echo (in_array('Visual', $thought_perception)) ? 'checked':'';  ?> > <?php echo xlt('Visual'); ?>
                                                            </label>
                                                            <label class="checkbox">
                                                              <input type="checkbox" name="thought_perception[]" value="Olfactory" <?php echo (in_array('Olfactory', $thought_perception)) ? 'checked':'';  ?> > <?php echo xlt('Olfactory'); ?>
                                                            </label>  
                                                            <label class="checkbox">
                                                              <input type="checkbox" name="thought_perception[]" value="Other" <?php echo (in_array('Other', $thought_perception)) ? 'checked':'';  ?> > <?php echo xlt('Other'); ?>
                                                              <input type="text" name="thought_perception_other" value="<?php echo text($check_res['thought_perception_other']); ?>" style="width: 125px;">
                                                            </label>                                              
                                                        </div>                                      
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div> <!-- thinking-tab -->

                                        <div class="tab-pane fade" role="tabpanel" id="emotion" aria-labelledby="emotion-tab" style="padding: 10px"> 

                                            <!-- EMOTION -->
                                            <h4><?php echo xlt('Emotion'); ?></h4>

                                            <!-- Mood -->
                                            <div class="rows" style="border-top: 1px solid #888; padding-top: 20px; margin-top: 20px;">
                                                <?php $mood = explode('|', $check_res['mood']); ?>
                                                <div class="col-sm-3">
                                                    <strong><?php echo xlt('Mood'); ?></strong>
                                                </div>
                                                <div class="col-sm-3">                                        
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="mood[]" value="Calm" <?php echo (in_array('Calm', $mood)) ? 'checked':'';  ?> > <?php echo xlt('Calm'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="mood[]" value="Apathetic" <?php echo (in_array('Apathetic', $mood)) ? 'checked':'';  ?> > <?php echo xlt('Apathetic'); ?>
                                                        </label>     
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="mood[]" value="Distraught"  <?php echo (in_array('Distraught', $mood)) ? 'checked':'';  ?> > <?php echo xlt('Distraught'); ?>
                                                        </label>                                       
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">                                        
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="mood[]" value="Angry" <?php echo (in_array('Angry', $mood)) ? 'checked':'';  ?> > <?php echo xlt('Angry'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="mood[]" value="Hopeless" <?php echo (in_array('Hopeless', $mood)) ? 'checked':'';  ?> > <?php echo xlt('Hopeless'); ?>
                                                        </label>
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="mood[]" value="Anxious" <?php echo (in_array('Anxious', $mood)) ? 'checked':'';  ?> > <?php echo xlt('Anxious'); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="mood[]" value="Cheerful" <?php echo (in_array('Cheerful', $mood)) ? 'checked':'';  ?> > <?php echo xlt('Cheerful'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="mood[]" value="Despondent" <?php echo (in_array('Despondent', $mood)) ? 'checked':'';  ?> > <?php echo xlt('Despondent/Sad'); ?>
                                                        </label>
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="mood[]" value="Irritable" <?php echo (in_array('Irritable', $mood)) ? 'checked':'';  ?> > <?php echo xlt('Irritable'); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>

                                            <!-- Affect -->
                                            <div class="rows" style="border-top: 1px solid #888; padding-top: 20px; margin-top: 20px;">
                                                <?php $affect = explode('|', $check_res['affect']); ?>
                                                <div class="col-sm-3">
                                                    <strong><?php echo xlt('Affect'); ?></strong>
                                                </div>
                                                <div class="col-sm-3">                                        
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="affect[]" value="Congruent to mood" <?php echo (in_array('Congruent to mood', $affect)) ? 'checked':'';  ?> > <?php echo xlt('Congruent to mood'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="affect[]" value="Hostile" <?php echo (in_array('Hostile', $affect)) ? 'checked':'';  ?> > <?php echo xlt('Hostile'); ?>
                                                        </label>     
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="affect[]" value="Agitated"  <?php echo (in_array('Agitated', $affect)) ? 'checked':'';  ?> > <?php echo xlt('Agitated'); ?>
                                                        </label>                                       
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">                                        
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="affect[]" value="Labile" <?php echo (in_array('Labile', $affect)) ? 'checked':'';  ?> > <?php echo xlt('Labile'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="affect[]" value="Tearful" <?php echo (in_array('Tearful', $affect)) ? 'checked':'';  ?> > <?php echo xlt('Tearful'); ?>
                                                        </label>
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="affect[]" value="Expansive" <?php echo (in_array('Expansive', $affect)) ? 'checked':'';  ?> > <?php echo xlt('Expansive'); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="affect[]" value="Inappropriate" <?php echo (in_array('Inappropriate', $affect)) ? 'checked':'';  ?> > <?php echo xlt('Inappropriate'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="affect[]" value="Blunted" <?php echo (in_array('Blunted', $affect)) ? 'checked':'';  ?> > <?php echo xlt('Blunted'); ?>
                                                        </label>
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="affect[]" value="Flat" <?php echo (in_array('Flat', $affect)) ? 'checked':'';  ?> > <?php echo xlt('Flat'); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                             
                                        </div> <!-- emotion-tab -->

                                        <div class="tab-pane fade" role="tabpanel" id="cognition" aria-labelledby="cognition-tab" style="padding: 10px"> 
                                             <!-- COGNITION -->
                                            <h4><?php echo xlt('Cognition'); ?></h4>

                                            <!-- Orientation / Attention -->
                                            <div class="rows" style="border-top: 1px solid #888; padding-top: 20px; margin-top: 20px;">
                                                <?php $orientation = explode('|', $check_res['orientation']); ?>
                                                <div class="col-sm-3">
                                                    <strong><?php echo xlt('Orientation / Attention'); ?></strong>
                                                </div>
                                                <div class="col-sm-3">                                        
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="orientation[]" value="Oriented to time" <?php echo (in_array('Oriented to time', $orientation)) ? 'checked':'';  ?> > <?php echo xlt('Oriented to time'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="orientation[]" value="Oriented to person" <?php echo (in_array('Oriented to person', $orientation)) ? 'checked':'';  ?> > <?php echo xlt('Oriented to person'); ?>
                                                        </label>                         
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">                                        
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="orientation[]" value="Oriented to place" <?php echo (in_array('Oriented to place', $orientation)) ? 'checked':'';  ?> > <?php echo xlt('Oriented to place'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="orientation[]" value="Oriented to situation" <?php echo (in_array('Oriented to situation', $orientation)) ? 'checked':'';  ?> > <?php echo xlt('Oriented to situation'); ?>
                                                        </label>                                            
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="orientation[]" value="Inattentive" <?php echo (in_array('Inattentive', $orientation)) ? 'checked':'';  ?> > <?php echo xlt('Inattentive'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="orientation[]" value="No intact" <?php echo (in_array('No intact', $orientation)) ? 'checked':'';  ?> > <?php echo xlt('No intact'); ?>
                                                        </label>                                            
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>

                                            <!-- Memory -->
                                            <div class="rows" style="border-top: 1px solid #888; padding-top: 20px; margin-top: 20px;">
                                                <div class="col-sm-3">
                                                    <strong><?php echo xlt('Memory'); ?></strong>
                                                </div>
                                                <div class="col-sm-3">   
                                                    <strong><?php echo xlt('Immediate'); ?></strong>
                                                    <?php $memory_immediate = explode('|', $check_res['memory_immediate']); ?>
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="memory_immediate[]" value="Intact" <?php echo (in_array('Intact', $memory_immediate)) ? 'checked':'';  ?> > <?php echo xlt('Intact'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="memory_immediate[]" value="Not Intact" <?php echo (in_array('Not Intact', $memory_immediate)) ? 'checked':'';  ?> > <?php echo xlt('Not Intact'); ?>
                                                        </label>                         
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">     
                                                    <strong><?php echo xlt('Recent'); ?></strong>   
                                                    <?php $memory_recent = explode('|', $check_res['memory_recent']); ?>
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="memory_recent[]" value="Intact" <?php echo (in_array('Intact', $memory_recent)) ? 'checked':'';  ?> > <?php echo xlt('Intact'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="memory_recent[]" value="Not Intact" <?php echo (in_array('Not Intact', $memory_recent)) ? 'checked':'';  ?> > <?php echo xlt('Not Intact'); ?>
                                                        </label>                                            
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <strong><?php echo xlt('Remote'); ?></strong>
                                                    <?php $memory_remote = explode('|', $check_res['memory_remote']); ?>
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="memory_remote[]" value="Intact" <?php echo (in_array('Intact', $memory_remote)) ? 'checked':'';  ?> > <?php echo xlt('Intact'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="memory_remote[]" value="Not Intact" <?php echo (in_array('Not Intact', $memory_remote)) ? 'checked':'';  ?> > <?php echo xlt('Not Intact'); ?>
                                                        </label>                                            
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>

                                            <!-- Insight / Judgement -->
                                            <div class="rows" style="border-top: 1px solid #888; padding-top: 20px; margin-top: 20px;">
                                                <div class="col-sm-3">
                                                    <strong><?php echo xlt('Insight / Judgement'); ?></strong>
                                                </div>
                                                <div class="col-sm-3">
                                                    <?php $insight = explode('|', $check_res['insight']); ?>
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="insight[]" value="Intact" <?php echo (in_array('Intact', $insight)) ? 'checked':'';  ?> > <?php echo xlt('Intact'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="insight[]" value="Blames Others" <?php echo (in_array('Blames Others', $insight)) ? 'checked':'';  ?> > <?php echo xlt('Blames Others'); ?>
                                                        </label>  
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="insight[]" value="Poor Impulse Control" <?php echo (in_array('Poor Impulse Control', $insight)) ? 'checked':'';  ?> > <?php echo xlt('Poor Impulse Control'); ?>
                                                        </label> 
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="insight[]" value="Denies Problem" <?php echo (in_array('Denies Problem', $insight)) ? 'checked':'';  ?> > <?php echo xlt('Denies Problem'); ?>
                                                        </label> 
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="insight[]" value="Blames Self" <?php echo (in_array('Blames Self', $insight)) ? 'checked':'';  ?> > <?php echo xlt('Blames Self'); ?>
                                                        </label>                        
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">     
                                                    <span style="margin-left: -12px"><strong><?php echo xlt('Awareness of symptoms and impact on functioning:'); ?></strong></span>   
                                                    <?php $insight_awareness_symptoms = explode('|', $check_res['insight_awareness_symptoms']); ?>
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="insight_awareness_symptoms[]" value="Poor" <?php echo (in_array('Poor', $insight_awareness_symptoms)) ? 'checked':'';  ?> > <?php echo xlt('Poor'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="insight_awareness_symptoms[]" value="Limited" <?php echo (in_array('Limited', $insight_awareness_symptoms)) ? 'checked':'';  ?> > <?php echo xlt('Limited'); ?>
                                                        </label>    
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="insight_awareness_symptoms[]" value="Good" <?php echo (in_array('Good', $insight_awareness_symptoms)) ? 'checked':'';  ?> > <?php echo xlt('Good'); ?>
                                                        </label>                                        
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <span style="margin-left: -12px"><strong><?php echo xlt('Awareness of need for treatment: '); ?></strong></span>
                                                    <?php $insight_awareness_need = explode('|', $check_res['insight_awareness_need']); ?>
                                                    <div class="form-group">
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="insight_awareness_need[]" value="Poor" <?php echo (in_array('Poor', $insight_awareness_need)) ? 'checked':'';  ?> > <?php echo xlt('Poor'); ?>
                                                        </label>                                        
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="insight_awareness_need[]" value="Limited" <?php echo (in_array('Limited', $insight_awareness_need)) ? 'checked':'';  ?> > <?php echo xlt('Limited'); ?>
                                                        </label>      
                                                        <label class="checkbox">
                                                          <input type="checkbox" name="insight_awareness_need[]" value="Good" <?php echo (in_array('Good', $insight_awareness_need)) ? 'checked':'';  ?> > <?php echo xlt('Good'); ?>
                                                        </label>                                       
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>

                                            <!-- Vegetative Signs -->
                                            <div class="rows" style="border-top: 1px solid #888; padding-top: 20px; margin-top: 20px;">
                                                <div class="col-sm-3">
                                                    <strong><?php echo xlt('Vegetative Signs'); ?></strong>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                         <label for="" class="col-sm-4"><?php echo xlt('Appetite:'); ?></label> 
                                                         <div class="col-sm-8">
                                                             <select name="vegetative_appetite"  class="form-control">
                                                                 <option value=""><?php echo xlt('Choose'); ?></option>
                                                                 <option value="Increased" <?php echo ($check_res['vegetative_appetite'] == 'Increased') ? 'selected': ''; ?> ><?php echo xlt('Increased'); ?></option>
                                                                 <option value="Unchanged" <?php echo ($check_res['vegetative_appetite'] == 'Unchanged') ? 'selected': ''; ?> ><?php echo xlt('Unchanged'); ?></option>
                                                                 <option value="Decreased" <?php echo ($check_res['vegetative_appetite'] == 'Decreased') ? 'selected': ''; ?> ><?php echo xlt('Decreased'); ?></option>
                                                                 <option value="Fluctuates" <?php echo ($check_res['vegetative_appetite'] == 'Fluctuates') ? 'selected': ''; ?> ><?php echo xlt('Fluctuates'); ?></option>
                                                             </select>
                                                         </div>                                                                  
                                                    </div>
                                                </div>
                                                <div class="col-sm-3"> 
                                                    <div class="form-group">
                                                        <label for="" class="col-sm-4"><?php echo xlt('Sleep:'); ?></label> 
                                                        <div class="col-sm-8">
                                                             <select name="vegetative_sleep"  class="form-control">
                                                                 <option value=""><?php echo xlt('Choose'); ?></option>
                                                                 <option value="Hypersomnia" <?php echo ($check_res['vegetative_sleep'] == 'Hypersomnia') ? 'selected': ''; ?> ><?php echo xlt('Hypersomnia'); ?></option>
                                                                 <option value="Hyposomnia" <?php echo ($check_res['vegetative_sleep'] == 'Hyposomnia') ? 'selected': ''; ?> ><?php echo xlt('Hyposomnia'); ?></option>
                                                                 <option value="Disturbed" <?php echo ($check_res['vegetative_sleep'] == 'Disturbed') ? 'selected': ''; ?> ><?php echo xlt('Disturbed'); ?></option>
                                                                 <option value="Unchanged" <?php echo ($check_res['vegetative_sleep'] == 'Unchanged') ? 'selected': ''; ?> ><?php echo xlt('Unchanged'); ?></option>
                                                             </select>
                                                        </div>                                              
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">                                        
                                                    <div class="form-group">
                                                        <label for="" class="col-sm-4"><?php echo xlt('Concentration:'); ?></label> 
                                                        <div class="col-sm-8">
                                                             <select name="vegetative_concentration"  class="form-control">
                                                                 <option value=""><?php echo xlt('Choose'); ?></option>
                                                                 <option value="Decreased" <?php echo ($check_res['vegetative_concentration'] == 'Decreased') ? 'selected': ''; ?> ><?php echo xlt('Decreased'); ?></option>
                                                                 <option value="Unchanged" <?php echo ($check_res['vegetative_concentration'] == 'Unchanged') ? 'selected': ''; ?> ><?php echo xlt('Unchanged'); ?></option>
                                                             </select>
                                                        </div>                                 
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div> <!-- cognition-tab -->
                                        
                                    </div> 
                                </div>
                            </div>

                           

                            <!-- DIAGOSTIC SECTION -->
                            <div class="col-md-12" style="margin-top: 30px">
                                <h3 class="text-center"><?php echo xlt('DIAGOSTIC SECTION'); ?></h3>

                                <h4><?php echo xlt('DSM-5/ICD-10 ASSESSMENT'); ?></h4>
                                <?php $dsm_5_code = explode('|', $check_res['dsm_5_code']); ?>
                                <div class="col-md-6">
                                    <div class="col-sm-6">
                                        <strong><?php echo xlt('DSM 5 Code'); ?></strong>
                                        <div class="form-group">
                                            <input type="text" name="dsm_5_code[]" class="form-control" value="<?php echo text($dsm_5_code[0]); ?>">
                                            <input type="text" name="dsm_5_code[]" class="form-control" value="<?php echo text($dsm_5_code[1]); ?>">
                                            <input type="text" name="dsm_5_code[]" class="form-control" value="<?php echo text($dsm_5_code[2]); ?>">
                                            <input type="text" name="dsm_5_code[]" class="form-control" value="<?php echo text($dsm_5_code[3]); ?>">                                            
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <strong><?php echo xlt('Disorder'); ?></strong>
                                        <?php $dsm_5_code_disorder = explode('|', $check_res['dsm_5_code_disorder']); ?>
                                        <div class="form-group">
                                            <input type="text" name="dsm_5_code_disorder[]" class="form-control" value="<?php echo text($dsm_5_code_disorder[0]); ?>">
                                            <input type="text" name="dsm_5_code_disorder[]" class="form-control" value="<?php echo text($dsm_5_code_disorder[1]); ?>">
                                            <input type="text" name="dsm_5_code_disorder[]" class="form-control" value="<?php echo text($dsm_5_code_disorder[2]); ?>">
                                            <input type="text" name="dsm_5_code_disorder[]" class="form-control" value="<?php echo text($dsm_5_code_disorder[3]); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="col-sm-6">
                                        <strong><?php echo xlt('DSM 5 Code'); ?></strong>
                                        <div class="form-group">
                                            <input type="text" name="dsm_5_code[]" class="form-control" value="<?php echo text($dsm_5_code[4]); ?>">
                                            <input type="text" name="dsm_5_code[]" class="form-control" value="<?php echo text($dsm_5_code[5]); ?>">
                                            <input type="text" name="dsm_5_code[]" class="form-control" value="<?php echo text($dsm_5_code[6]); ?>">
                                            <input type="text" name="dsm_5_code[]" class="form-control" value="<?php echo text($dsm_5_code[7]); ?>">                                            
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <strong><?php echo xlt('Disorder'); ?></strong>
                                        <div class="form-group">
                                            <input type="text" name="dsm_5_code_disorder[]" class="form-control" value="<?php echo text($dsm_5_code_disorder[4]); ?>">
                                            <input type="text" name="dsm_5_code_disorder[]" class="form-control" value="<?php echo text($dsm_5_code_disorder[5]); ?>">
                                            <input type="text" name="dsm_5_code_disorder[]" class="form-control" value="<?php echo text($dsm_5_code_disorder[6]); ?>">
                                            <input type="text" name="dsm_5_code_disorder[]" class="form-control" value="<?php echo text($dsm_5_code_disorder[7]); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-sm-12">
                                    <div class="form-group" style="margin-top: 10px">
                                        <label for=""><?php echo xlt('Type of education provided to members/families about prognosis and outcomes from their diagnosis:'); ?></label>
                                        <textarea name="dsm_5_type_education" rows="3" class="form-control"><?php echo text($check_res['dsm_5_type_education']); ?></textarea>
                                    </div>
                                </div>                                
                            </div>

                            <!-- TREATMENT RECOMMEDATION -->
                            <div class="col-md-12" style="margin-top: 30px">
                                <h3 class="text-center"><?php echo xlt('TREATMENT RECOMMEDATION'); ?></h3>
                                <p><?php echo xlt('A. Recommended level of care and types of services to address issues outlined including intensity, frequency, and duration of each service (length of time for a specific service in a single encounter):'); ?></p>

                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th><?php echo xlt('Service Type'); ?></th>
                                            <th><?php echo xlt('Intensity'); ?></th>
                                            <th><?php echo xlt('Frequency'); ?></th>
                                            <th><?php echo xlt('Duration'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="width: 25%">
                                                <select name="service_type_1" class="form-control">
                                                    <?php echo get_service_type($check_res['service_type_1']); ?>                 
                                                </select>
                                            </td>
                                            <td style="width: 25%">
                                                <select name="service_type_1_intencity" class="form-control" >
                                                    <?php echo get_intensity_hours($check_res['service_type_1_intencity']); ?>
                                                </select>
                                                <div>
                                                    <?php echo xlt('Other:'); ?> <input type="text" name="service_type_1_intencity_other" value="<?php echo text($check_res['service_type_1_intencity_other']); ?>" >
                                                </div>
                                            </td>
                                            <td style="width: 25%">
                                                <select name="service_type_1_frequency" class="form-control" >
                                                    <?php echo get_weekly_frequency($check_res['service_type_1_frequency']); ?>
                                                </select>
                                                <div>
                                                    <?php echo xlt('Other:'); ?> <input type="text" name="service_type_1_frequency_other" value="<?php echo text($check_res['service_type_1_frequency_other']); ?>" >
                                                </div>
                                                
                                            </td>
                                            <td style="width: 25%">
                                                <select name="service_type_1_duration" id="" class="form-control">
                                                    <?php echo get_treatment_duration($check_res['service_type_1_duration']); ?>
                                                </select>
                                                <div>
                                                    <?php echo xlt('Other:'); ?> <input type="text" name="service_type_1_duration_other" value="<?php echo text($check_res['service_type_1_duration_other']); ?>" >
                                                </div>
                                                
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 25%">
                                                <select name="service_type_2" class="form-control">
                                                    <?php echo get_service_type($check_res['service_type_2']); ?>                 
                                                </select>
                                            </td>
                                            <td style="width: 25%">
                                                <select name="service_type_2_intencity" class="form-control" >
                                                    <?php echo get_intensity_hours($check_res['service_type_2_intencity']); ?>
                                                </select>
                                                <div>
                                                    <?php echo xlt('Other:'); ?> <input type="text" name="service_type_2_intencity_other" value="<?php echo text($check_res['service_type_2_intencity_other']); ?>" >
                                                </div>
                                            </td>
                                            <td style="width: 25%">
                                                <select name="service_type_2_frequency" class="form-control" >
                                                    <?php echo get_weekly_frequency($check_res['service_type_2_frequency']); ?>
                                                </select>
                                                <div>
                                                    <?php echo xlt('Other:'); ?> <input type="text" name="service_type_2_frequency_other" value="<?php echo text($check_res['service_type_2_frequency_other']); ?>" >
                                                </div>
                                                
                                            </td>
                                            <td style="width: 25%">
                                                <select name="service_type_2_duration" id="" class="form-control">
                                                    <?php echo get_treatment_duration($check_res['service_type_2_duration']); ?>
                                                </select>
                                                <div>
                                                    <?php echo xlt('Other:'); ?> <input type="text" name="service_type_2_duration_other" value="<?php echo text($check_res['service_type_2_duration_other']); ?>" >
                                                </div>
                                                
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 25%">
                                                <select name="service_type_3" class="form-control">
                                                    <?php echo get_service_type($check_res['service_type_3']); ?>                 
                                                </select>
                                            </td>
                                            <td style="width: 25%">
                                                <select name="service_type_3_intencity" class="form-control" >
                                                    <?php echo get_intensity_hours($check_res['service_type_3_intencity']); ?>
                                                </select>
                                                <div>
                                                    <?php echo xlt('Other:'); ?> <input type="text" name="service_type_3_intencity_other" value="<?php echo text($check_res['service_type_3_intencity_other']); ?>" >
                                                </div>
                                            </td>
                                            <td style="width: 25%">
                                                <select name="service_type_3_frequency" class="form-control" >
                                                    <?php echo get_weekly_frequency($check_res['service_type_3_frequency']); ?>
                                                </select>
                                                <div>
                                                    <?php echo xlt('Other:'); ?> <input type="text" name="service_type_3_frequency_other" value="<?php echo text($check_res['service_type_3_frequency_other']); ?>" >
                                                </div>
                                                
                                            </td>
                                            <td style="width: 25%">
                                                <select name="service_type_3_duration" id="" class="form-control">
                                                    <?php echo get_treatment_duration($check_res['service_type_3_duration']); ?>
                                                </select>
                                                <div>
                                                    <?php echo xlt('Other:'); ?> <input type="text" name="service_type_3_duration_other" value="<?php echo text($check_res['service_type_3_duration_other']); ?>" >
                                                </div>
                                                
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 25%">
                                                <select name="service_type_4" class="form-control">
                                                    <?php echo get_service_type($check_res['service_type_4']); ?>                 
                                                </select>
                                            </td>
                                            <td style="width: 25%">
                                                <select name="service_type_4_intencity" class="form-control" >
                                                    <?php echo get_intensity_hours($check_res['service_type_4_intencity']); ?>
                                                </select>
                                                <div>
                                                    <?php echo xlt('Other:'); ?> <input type="text" name="service_type_4_intencity_other" value="<?php echo text($check_res['service_type_4_intencity_other']); ?>" >
                                                </div>
                                            </td>
                                            <td style="width: 25%">
                                                <select name="service_type_4_frequency" class="form-control" >
                                                    <?php echo get_weekly_frequency($check_res['service_type_4_frequency']); ?>
                                                </select>
                                                <div>
                                                    <?php echo xlt('Other:'); ?> <input type="text" name="service_type_4_frequency_other" value="<?php echo text($check_res['service_type_4_frequency_other']); ?>" >
                                                </div>
                                                
                                            </td>
                                            <td style="width: 25%">
                                                <select name="service_type_4_duration" id="" class="form-control">
                                                    <?php echo get_treatment_duration($check_res['service_type_4_duration']); ?>
                                                </select>
                                                <div>
                                                    <?php echo xlt('Other:'); ?> <input type="text" name="service_type_4_duration_other" value="<?php echo text($check_res['service_type_4_duration_other']); ?>" >
                                                </div>
                                                
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 25%">
                                                <select name="service_type_5" class="form-control">
                                                    <?php echo get_service_type($check_res['service_type_5']); ?>                 
                                                </select>
                                            </td>
                                            <td style="width: 25%">
                                                <select name="service_type_5_intencity" class="form-control" >
                                                    <?php echo get_intensity_hours($check_res['service_type_5_intencity']); ?>
                                                </select>
                                                <div>
                                                    <?php echo xlt('Other:'); ?> <input type="text" name="service_type_5_intencity_other" value="<?php echo text($check_res['service_type_5_intencity_other']); ?>" >
                                                </div>
                                            </td>
                                            <td style="width: 25%">
                                                <select name="service_type_5_frequency" class="form-control" >
                                                    <?php echo get_weekly_frequency($check_res['service_type_5_frequency']); ?>
                                                </select>
                                                <div>
                                                    <?php echo xlt('Other:'); ?> <input type="text" name="service_type_5_frequency_other" value="<?php echo text($check_res['service_type_5_frequency_other']); ?>" >
                                                </div>
                                                
                                            </td>
                                            <td style="width: 25%">
                                                <select name="service_type_5_duration" id="" class="form-control">
                                                    <?php echo get_treatment_duration($check_res['service_type_5_duration']); ?>
                                                </select>
                                                <div>
                                                    <?php echo xlt('Other:'); ?> <input type="text" name="service_type_5_duration_other" value="<?php echo text($check_res['service_type_5_duration_other']); ?>" >
                                                </div>
                                                
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 25%">
                                                <select name="service_type_6" class="form-control">
                                                    <?php echo get_service_type($check_res['service_type_6']); ?>                 
                                                </select>
                                            </td>
                                            <td style="width: 25%">
                                                <select name="service_type_6_intencity" class="form-control" >
                                                    <?php echo get_intensity_hours($check_res['service_type_6_intencity']); ?>
                                                </select>
                                                <div>
                                                    <?php echo xlt('Other:'); ?> <input type="text" name="service_type_6_intencity_other" value="<?php echo text($check_res['service_type_6_intencity_other']); ?>" >
                                                </div>
                                            </td>
                                            <td style="width: 25%">
                                                <select name="service_type_6_frequency" class="form-control" >
                                                    <?php echo get_weekly_frequency($check_res['service_type_6_frequency']); ?>
                                                </select>
                                                <div>
                                                    <?php echo xlt('Other:'); ?> <input type="text" name="service_type_6_frequency_other" value="<?php echo text($check_res['service_type_6_frequency_other']); ?>" >
                                                </div>
                                                
                                            </td>
                                            <td style="width: 25%">
                                                <select name="service_type_6_duration" id="" class="form-control">
                                                    <?php echo get_treatment_duration($check_res['service_type_6_duration']); ?>
                                                </select>
                                                <div>
                                                    <?php echo xlt('Other:'); ?>                                                    
                                                        <input type="text" name="service_type_6_duration_other" value="<?php echo text($check_res['service_type_6_duration_other']); ?>" >
                                                     
                                                </div>
                                                
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                
                            </div>

                            <!-- MEDICAL NECESSITY CRITERIA -->
                            <div class="com-md-12" style="margin-top: 30px"> 
                                <h3 class="text-center"><?php echo xlt('MEDICAL NECESSITY CRITERIA'); ?></h3>
                                <div style="padding: 10px 20px;">   
                                    <p style="font-size: 1.2em" ><?php echo xlt('The assessment must provide documentation of the medical necessity for each service to be provided including, but not limited to, psychotherapy, psychological/neuropsychological assessment, CBRS, partial care, and service coordination. Medical necessity must be established for participant to receive enhanced services. Medical necessity must be documented by the following criteria: the service represents the least restrictive setting and other services have failed or are not appropriate for the clinical needs of the participant, and documentation that the services can reasonably be expected to improve the participant\'s condition or prevent further regression so that the current level of care is no longer necessary or maybe reduced.'); ?></p>
                                </div>
                                <div style="padding: 10px 20px;"> 
                                     <table class="table  table-bordered">  
                                            <tbody> 
                                                <tr>    
                                                    <td style="width: 30%">
                                                        <select name="service_criteria_1">
                                                            <?php echo get_service_type($check_res['service_criteria_1']); ?>
                                                        </select>
                                                    </td>
                                                    <td style="width: 70%">
                                                        <div style="padding: 10px">   
                                                            Medical necessity documenttation: Due to information in Psychiatric section of current diagnosis and symptoms ( reference if necessary ), what negative consequences may occur if treatment not implemented? 
                                                            <textarea name="service_criteria_1_consequence" rows="3" class="form-control"><?php echo text($check_res['service_criteria_1_consequence']); ?></textarea>   
                                                        </div>  
                                                        
                                                    </td>
                                                </tr>   
                                                <tr>    
                                                    <td style="width: 30%">
                                                        <select name="service_criteria_2">
                                                            <?php echo get_service_type($check_res['service_criteria_2']); ?>
                                                        </select>
                                                    </td>
                                                    <td style="width: 70%">
                                                        <div style="padding: 10px">   
                                                            Medical necessity documenttation: Due to information in Psychiatric section of current diagnosis and symptoms ( reference if necessary ), what negative consequences may occur if treatment not implemented? 
                                                            <textarea name="service_criteria_2_consequence" rows="3" class="form-control"><?php echo text($check_res['service_criteria_2_consequence']); ?></textarea>   
                                                        </div>  
                                                        
                                                    </td>
                                                </tr>
                                                <tr>    
                                                    <td style="width: 30%">
                                                        <select name="service_criteria_3">
                                                            <?php echo get_service_type($check_res['service_criteria_3']); ?>
                                                        </select>
                                                    </td>
                                                    <td style="width: 70%">
                                                        <div style="padding: 10px">   
                                                            Medical necessity documenttation: Due to information in Psychiatric section of current diagnosis and symptoms ( reference if necessary ), what negative consequences may occur if treatment not implemented? 
                                                            <textarea name="service_criteria_3_consequence" rows="3" class="form-control"><?php echo text($check_res['service_criteria_3_consequence']); ?></textarea>   
                                                        </div>  
                                                        
                                                    </td>
                                                </tr>
                                                <tr>    
                                                    <td style="width: 30%">
                                                        <select name="service_criteria_4">
                                                            <?php echo get_service_type($check_res['service_criteria_4']); ?>
                                                        </select>
                                                    </td>
                                                    <td style="width: 70%">
                                                        <div style="padding: 10px">   
                                                            Medical necessity documenttation: Due to information in Psychiatric section of current diagnosis and symptoms ( reference if necessary ), what negative consequences may occur if treatment not implemented? 
                                                            <textarea name="service_criteria_4_consequence" rows="3" class="form-control"><?php echo text($check_res['service_criteria_4_consequence']); ?></textarea>   
                                                        </div>  
                                                        
                                                    </td>
                                                </tr>
                                                <tr>    
                                                    <td style="width: 30%">
                                                        <select name="service_criteria_5">
                                                            <?php echo get_service_type($check_res['service_criteria_5']); ?>
                                                        </select>
                                                    </td>
                                                    <td style="width: 70%">
                                                        <div style="padding: 10px">   
                                                            Medical necessity documenttation: Due to information in Psychiatric section of current diagnosis and symptoms ( reference if necessary ), what negative consequences may occur if treatment not implemented? 
                                                            <textarea name="service_criteria_5_consequence" rows="3" class="form-control"><?php echo text($check_res['service_criteria_5_consequence']); ?></textarea>   
                                                        </div>  
                                                        
                                                    </td>
                                                </tr>
                                                <tr>    
                                                    <td style="width: 30%">
                                                        <select name="service_criteria_6">
                                                            <?php echo get_service_type($check_res['service_criteria_6']); ?>
                                                        </select>
                                                    </td>
                                                    <td style="width: 70%">
                                                        <div style="padding: 10px">   
                                                            Medical necessity documenttation: Due to information in Psychiatric section of current diagnosis and symptoms ( reference if necessary ), what negative consequences may occur if treatment not implemented? 
                                                            <textarea name="service_criteria_6_consequence" rows="3" class="form-control"><?php echo text($check_res['service_criteria_6_consequence']); ?></textarea>   
                                                        </div>  
                                                        
                                                    </td>
                                                </tr>
                                            </tbody>    
                                     </table>      
                                </div>

                                <div style="padding: 10px 20px;">  
                                    <div class="form-group">    
                                        <label for=""><?php echo xlt('ANNUAL ASSESSMENT (Document why a new or updated assessment is more appropriate for participant):'); ?></label>
                                        <textarea name="annual_assessment" rows="3" class="form-control"><?php echo text($check_res['annual_assessment']); ?></textarea>
                                    </div>   
                                </div>

                                <div style="padding: 10px 20px;">  
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <label for="" class="col-sm-6">Clinician's Signature, Degree, and Credentials</label>
                                            <div class="col-sm-6">
                                                <select name="name_examiner" class="form-control">
                                                    <?php echo get_examiner_name_dregree($check_res['name_examiner']); ?>
                                                </select>
                                            </div>
                                            
                                        </div>
                                    </div>  
                                    <div class="col-sm-4">
                                        <div class="form-group">    
                                            <label for="" class="col-sm-3"><?php echo xlt('Date:'); ?></label>
                                            <div class="col-sm-9">  
                                                    <input type="text" name="date_examine" class="form-control datepicker" value="<?php echo text($check_res['date_examine']); ?>" autocomplete="off">
                                            </div>                                            
                                        </div>  
                                    </div>  
                                </div>
                                
                            </div>

                            
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

                var today = new Date();

                

            });
        </script>
    </body>
</html>
