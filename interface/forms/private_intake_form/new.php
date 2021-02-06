<?php
/**
 * Private Intake form.
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

$folderName = 'private_intake_form';
$tableName = 'form_' . $folderName;


$returnurl = 'encounter_top.php';
$formid = (isset($_GET['id']) ? $_GET['id'] : 0);

$formStmt = "SELECT id FROM forms WHERE form_id=? AND formdir=?";
$form = sqlQuery($formStmt, array($formid, $folderName));

//$GLOBALS['pid'] = empty($GLOBALS['pid']) ? $form['pid'] : $GLOBALS['pid'];

$check_res = $formid ? formFetch($tableName, $formid) : array();

//dd(print_r($check_res));

/* checking the last record */
if( empty($check_res) ){
    $last_record_query = "SELECT * FROM {$tableName} WHERE pid=? ORDER BY timestamp DESC LIMIT 1";
    $last_record = sqlQuery($last_record_query, array($pid));
} 


$is_group = ($attendant_type == 'gid') ? true : false;



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

$path_url = $_SERVER['REQUEST_SCHEME'] . '//' . $_SERVER['SERVER_NAME'];
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo xlt("Private Intake Form"); ?></title>

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
                .col-sm-12 {
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
                    padding-top: 40px;
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
                    <h2><?php echo xlt('Private Intake Form'); ?></h2>
                </div>
            </div>
           

            <?php
            $current_date = date('Y-m-d');

            if( $_SESSION['from_dashboard'] ){
                $patient_full_name = ($check_res['name']) ? $check_res['name'] : '';
            } else {

                $patient_id = ( $_SESSION['alert_notify_pid'] ) ? $_SESSION['alert_notify_pid'] : '';
                $pid = ( $_SESSION['pid'] ) ? $_SESSION['pid'] : 0;
                if($pid) {
                  $patient = getPatientData($patient_id);
                  //print_r($pid);
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
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name" class="col-sm-3 "><?php echo xlt('Client Name'); ?></label>
                                        <div class="col-sm-9">
                                            <input type="text"  id="name" class="form-control" value="<?php echo text($patient_full_name); ?>" readonly>
                                            <input type="hidden" name="name" value="<?php echo text($patient_full_name); ?>" >
                                        </div>                                    
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="age" class="col-sm-3 "><?php echo xlt('Age'); ?></label>
                                        <div class="col-sm-9">
                                            <input type="text" name="age" id="age" class="form-control" value="<?php echo ($check_res['age']) ? text($check_res['age']) : text($last_record['age']) ; ?>">
                                            <small class="text-danger counselor_error"></small>
                                        </div>                                    
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="gender" class="col-sm-3 "><?php echo xlt('Gender'); ?></label>
                                        <div class="col-sm-9">
                                            <label class="radio-inline" style="margin-right: 20px">
                                              <input type="radio" name="gender" id="inlineRadio1" value="Male"> Male
                                            </label>
                                            <label class="radio-inline">
                                              <input type="radio" name="gender" id="inlineRadio2" value="Female"> Femaile
                                            </label>                                            
                                        </div>                                    
                                    </div>
                                </div>
                            </div>
                            

                            <div class="clearfix"></div>

                            <div class="col-md-12 margin-top-20" style="margin-top: 30px" >
                                <div class="form-group">
                                    <label for="presenting_problem" class="control-label"><?php echo xlt('Presenting Problem'); ?></label>
                                    <textarea name="presenting_problem" id="presenting_problem" rows="3" class="form-control"><?php echo ($check_res['presenting_problem']) ? text($check_res['presenting_problem']) : text($last_record['presenting_problem']); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="history_presenting_problem" class="control-label"><?php echo xlt('History of Presenting Problem'); ?></label>
                                    <textarea name="history_presenting_problem" id="history_presenting_problem" rows="3" class="form-control"><?php echo ($check_res['history_presenting_problem']) ? text($check_res['history_presenting_problem']) : text($last_record['history_presenting_problem']); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="relevent_social_history" class="control-label"><?php echo xlt('Relevant Social History'); ?></label>
                                    <textarea name="relevent_social_history" id="relevent_social_history" rows="3" class="form-control"><?php echo ($check_res['relevent_social_history']) ? text($check_res['relevent_social_history']) : text($last_record['relevent_social_history']); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="family_history" class="control-label"><?php echo xlt('Family History'); ?></label>
                                    <textarea name="family_history" id="family_history" rows="3" class="form-control"><?php echo ($check_res['family_history']) ? text($check_res['family_history']) : text($last_record['family_history']); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="medications" class="control-label"><?php echo xlt('Medication'); ?></label>
                                    <textarea name="medications" id="medications" rows="3" class="form-control"><?php echo ($check_res['medications']) ? text($check_res['medications']) : text($last_record['medications']); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="prior_medical_history" class="control-label"><?php echo xlt('Prior Medical History'); ?></label>
                                    <textarea name="prior_medical_history" id="prior_medical_history" rows="3" class="form-control"><?php echo ($check_res['prior_medical_history']) ? text($check_res['prior_medical_history']) : text($last_record['prior_medical_history']); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="drug_history" class="control-label"><?php echo xlt('Drug History'); ?></label>
                                    <textarea name="drug_history" id="drug_history" rows="3" class="form-control"><?php echo ($check_res['drug_history']) ? text($check_res['drug_history']) : text($last_record['drug_history']); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="resources_strengths" class="control-label"><?php echo xlt('Resources and Strengths'); ?></label>
                                    <textarea name="resources_strengths" id="resources_strengths" rows="3" class="form-control"><?php echo ($check_res['resources_strengths']) ? text($check_res['resources_strengths']) : text($last_record['resources_strengths']); ?></textarea>
                                </div>
                            </div>

                            <div class="clearfix"></div>

                            <div class="treatment_diagnostic_row">
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
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="type_services" class="control-label"><?php echo xlt('Type of Services'); ?></label>
                                                <input type="text" class="form-control" name="type_services" id="type_services" value="<?php echo ($check_res['type_services']) ? text($check_res['type_services']) : text($last_record['type_services']) ; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="frequency" class="control-label"><?php echo xlt('Frequency'); ?></label>
                                                <input type="text" class="form-control" name="frequency" id="frequency" value="<?php echo ($check_res['frequency']) ? text($check_res['frequency']) : text($last_record['frequency']) ; ?>">
                                            </div>
                                        </div>
                                    </div>                                
                                </div> 
                            </div>

                            <div class="clearfix"></div>

                            <div class="col-md-12 margin-top-20 session-focus" style="margin-top: 30px">
                                <h3><?php echo xlt('Client Objectives / Therapeutic Goals'); ?></h3>  
                                <div class="form-group" style="margin-top: 20px">
                                    <textarea name="therapeutic_goals_1" id="therapeutic_goals_1" rows="4" class="form-control" placeholder="Client Objective 1."><?php echo text($check_res['therapeutic_goals_1']); ?></textarea>
                                    <div class="col-md-6 col-md-offset-6">
                                        <label for="" class="col-sm-6"><?php echo xlt('Estimated Date of Completion'); ?></label>
                                        <div class="col-sm-6">
                                            <input type="text" name="therapeutic_goals_1_date_completion" class="form-control newDatePicker" autocomplete="off" value="<?php echo ( $check_res['therapeutic_goals_1_date_completion'] ) ? date('m/d/Y', strtotime($check_res['therapeutic_goals_1_date_completion'])):''; ?>">
                                        </div>                                        
                                    </div>
                                </div> 
                                <div class="clearfix"></div>
                                <div class="form-group" style="margin-top: 20px">
                                    <textarea name="therapeutic_goals_2" id="therapeutic_goals_2" rows="4" class="form-control" placeholder="Client Objective 2."><?php echo text($check_res['therapeutic_goals_2']); ?></textarea>
                                    <div class="col-md-6 col-md-offset-6">
                                        <label for="" class="col-sm-6"><?php echo xlt('Estimated Date of Completion'); ?></label>
                                        <div class="col-sm-6">
                                            <input type="text" name="therapeutic_goals_2_date_completion" class="form-control newDatePicker" autocomplete="off" value="<?php echo ( $check_res['therapeutic_goals_2_date_completion'] ) ? date('m/d/Y', strtotime($check_res['therapeutic_goals_2_date_completion'])):''; ?>">
                                        </div>                                        
                                    </div>
                                </div>  
                                <div class="clearfix"></div>
                                <div class="form-group" style="margin-top: 20px">
                                    <textarea name="therapeutic_goals_3" id="therapeutic_goals_3" rows="4" class="form-control" placeholder="Client Objective 3."><?php echo text($check_res['therapeutic_goals_3']); ?></textarea>
                                    <div class="col-md-6 col-md-offset-6">
                                        <label for="" class="col-sm-6"><?php echo xlt('Estimated Date of Completion'); ?></label>
                                        <div class="col-sm-6">
                                            <input type="text" name="therapeutic_goals_3_date_completion" class="form-control newDatePicker" autocomplete="off" value="<?php echo ( $check_res['therapeutic_goals_3_date_completion'] ) ? date('m/d/Y', strtotime($check_res['therapeutic_goals_3_date_completion'])):''; ?>">
                                        </div>                                        
                                    </div>
                                </div>                           
                                
                            </div>

                            <div class="clearfix"></div>

                            <div class="col-md-12 margin-top-20" style="margin-top: 20px">
                                <div class="form-group">                                    
                                    <div class="col-sm-6">
                                        <span class="col-sm-6"><?php echo xlt('Signature of Provider:'); ?> </span>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control " name="signature_provider" id="signature_provider"  autocomplete="off">
                                            <small class="text-danger meet_again_date_error"></small>
                                        </div>                                        
                                    </div>

                                    <div class="col-sm-6">
                                        <span class="col-sm-3"><?php echo xlt('Date:'); ?> </span>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control newDatePicker" name="date" id="date" value="<?php echo ( $check_res['date'] ) ? date('m/d/Y', strtotime($check_res['date'])):''; ?>" autocomplete="off">
                                            <small class="text-danger meet_again_date_error"></small>
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
                                <?php                                    
                                    if (($esign->isButtonViewable() and $is_group == 0 and $authPostCalendarCategoryWrite) or ($esign->isButtonViewable() and $is_group and acl_check("groups", "glog", false, 'write') and $authPostCalendarCategoryWrite)) {
                                        if (!$aco_spec || acl_check($aco_spec[0], $aco_spec[1], '', 'write')) {
                                            echo $esign->buttonHtml();
                                        }
                                    }
                                ?>
                                <button type='submit'  class="btn btn-default btn-save" name="save_progress_notes"><?php echo xlt('Save'); ?></button>
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
                        pageTitle: "Private Intake Form",              // add title to print page
                        removeInline: false,        // remove inline styles from print elements
                        removeInlineSelector: "*",  // custom selectors to filter inline styles. removeInline must be true
                        printDelay: 333,            // variable print delay
                        header: "<h2>Private Intake Form</h2>",               // prefix to html
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
                //console.log('Session Dashboard: ' + session_dashboard);
                if(session_dashboard) {
                    //window.top.location.reload();
                    <?php //$_SESSION['from_dashboard'] = false; ?>
                    window.top.location.href = window.top.location;
                } else {
                   top.restoreSession(); 
                    parent.closeTab(window.name, false);
                }                
            }

            
        </script>
    </body>
</html>
