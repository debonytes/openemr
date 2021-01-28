<?php
/**
 * Treatment Plan Annual form.
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

$folderName = 'counselor_treatment_plan_review';
$tableName = 'form_' . $folderName;
$form_plan = 'form_counselor_treatment_plan';


$returnurl = 'encounter_top.php';
$formid = 0 + (isset($_GET['id']) ? $_GET['id'] : 0);

$formStmt = "SELECT id FROM forms WHERE form_id=? AND formdir=?";
$form = sqlQuery($formStmt, array($formid, $folderName));

$GLOBALS['pid'] = empty($GLOBALS['pid']) ? $form['pid'] : $GLOBALS['pid'];

$check_res = $formid ? formFetch($tableName, $formid) : array();



/* checking the last record */
$last_record_query = "SELECT * FROM {$tableName} WHERE pid=? ORDER BY timestamp DESC LIMIT 1";
$last_record = sqlQuery($last_record_query, array($pid));

$days_review_arr = array('90 Day Review', '180 Day Review', '270 Day Review', 'Other Review');
$counter = 0;

if( empty($last_record) ){
    $review_days = $days_review_arr[0];
    $last_record_query = "SELECT * FROM {$form_plan} WHERE pid=? ORDER BY timestamp DESC LIMIT 1";
    $last_record = sqlQuery($last_record_query, array($pid));

    if( empty($last_record) ){
        die(xlt('Please create a new Counselor Treatment Plan for this Client.'));
    }
} else {

    if($check_res){
        $review_days = $check_res['day_review'];
    } else {
        $query = "SELECT COUNT(id) as id FROM {$tableName} WHERE pid=?";
        $result = sqlQuery($query, array($pid));

        $counter = $result['id'];

        $key = array_search($last_record['day_review'], $days_review_arr);
        $review_days = $days_review_arr[ $counter ];
    }    
}


$is_group = ($attendant_type == 'gid') ? true : false;

$esignApi = new Api();
// Create the ESign instance for this form
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
        <title><?php echo xlt("Counselor Treatment Plan Review"); ?></title>

        <?php Header::setupHeader(['datetime-picker', 'opener', 'esign', 'common']); ?>
        <link rel="stylesheet" href="<?php echo $web_root; ?>/library/css/bootstrap-timepicker.min.css">
        <link rel="stylesheet" href="../../../style_custom.css">
    </head>
    <body class="body_top">
        <div class="container">
            <div class="row">
                <div class="page-header">
                    <h2><?php echo xlt('Counselor Treatment Plan Review'); ?></h2>
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



            
            ?>
            <div class="row">

                
                
                <form method="post" id="my_counselor_treatment_plan_form" name="my_counselor_treatment_plan_form" action="<?php echo $rootdir; ?>/forms/<?php echo $folderName; ?>/save.php?id=<?php echo attr_url($formid); ?>">          

                
                    <input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
                    <input type="hidden" name="pid" value="<?php echo $pid; ?>">
                    <input type="hidden" name="encounter" value="<?php echo $encounter; ?>">
                    <input type="hidden" name="user" value="<?php echo $user_id; ?>">
                    <input type="hidden" name="authorized" value="<?php echo $userauthorized; ?>">
                    <input type="hidden" name="activity" value="1">

                    <fieldset>
                        <legend class=""><?php echo xlt('Counselor Treatment Plan Review'); ?></legend>
                            
                            <div class="col-md-12" style="margin-top: 0; margin-bottom: 20px">
                                <div class="col-sm-2">
                                    <div class="radio">
                                        <label>
                                          <input type="radio" name="day_review" value="90 Day Review" <?php echo ( ($check_res['day_review'] == '90 Day Review') || ($review_days == '90 Day Review') ) ? 'checked': ''; ?>  <?php echo ($review_days == '90 Day Review') ? '':'disabled'; ?> > <?php echo xlt('90 Day Review'); ?>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="radio">
                                        <label>
                                          <input type="radio" name="day_review" value="180 Day Review" <?php echo ( ($check_res['day_review'] == '180 Day Review') || ($review_days == '180 Day Review') ) ? 'checked': ''; ?>  <?php echo ($review_days == '180 Day Review') ? '':'disabled'; ?> > <?php echo xlt('180 Day Review'); ?>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="radio">
                                        <label>
                                          <input type="radio" name="day_review" value="270 Day Review" <?php echo ( ($check_res['day_review'] == '270 Day Review') || ($review_days == '270 Day Review')  ) ? 'checked': ''; ?> <?php echo ($review_days == '270 Day Review') ? '':'disabled'; ?> > <?php echo xlt('270 Day Review'); ?>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="radio">
                                        <label>
                                          <input type="radio" name="day_review" value="Other Review" <?php echo ( ($check_res['day_review'] == 'Other Review') || ($counter > 3) ) ? 'checked': ''; ?> <?php echo ($review_days == 'Other Review') ? '':'disabled'; ?> > <?php echo xlt('Other Review'); ?>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="radio">
                                        <label for="" class="col-sm-8"><?php echo xlt('If update/review initial plan date:'); ?></label>
                                        <div class="col-sm-4">
                                            <input type="text" name="initial_plan_date" class="form-control newDatePicker" value="<?php echo ( $check_res['initial_plan_date'] ) ? date('m/d/Y', strtotime($check_res['initial_plan_date'])): date('m/d/Y') ; ?>" autocomplete="off">
                                        </div>                                        
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div class="col-md-12" >
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="" class="col-sm-5"><?php echo xlt('Participant Name:'); ?> </label>
                                        <div class="col-sm-7">
                                            <input type="text"  id="name" class="form-control" value="<?php echo text($patient_full_name); ?>" readonly>
                                            <input type="hidden" name="name" value="<?php echo text($patient_full_name); ?>" >
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="medicaid" class="col-sm-4"><?php echo xlt('Medicaid#:'); ?> </label>
                                        <div class="col-sm-8">
                                            <input type="text" id="medicaid" name="medicaid" class="form-control" value="<?php echo ($check_res['medicaid']) ? text($check_res['medicaid']) : text($last_record['medicaid']); ?>" >
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="diagnosis_code" class="col-sm-5"><?php echo xlt('Diagnosis Code(s):'); ?> </label>
                                        <div class="col-sm-7">
                                            <input type="text" id="diagnosis_code" name="diagnosis_code" class="form-control" value="<?php echo ($check_res['diagnosis_code']) ? text($check_res['diagnosis_code']) : text($last_record['diagnosis_code']); ?>" >
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div class="col-md-12">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Type of Service:'); ?> </label>
                                        <div class="col-sm-8">
                                            <select name="type_service" class="form-group">
                                                <option value=""><?php echo xlt('Choose'); ?></option>
                                                <?php $type_service = ($check_res['type_service']) ? text($check_res['type_service']) : text($last_record['type_service']); ?>
                                                <option value="Counseling" <?php echo ($type_service == 'Counseling') ? 'selected': ''; ?> ><?php echo xlt('Counseling'); ?></option>
                                                <option value="Mental Health Clinic" <?php echo ($type_service == 'Mental Health Clinic') ? 'selected': ''; ?>><?php echo xlt('Mental Health Clinic'); ?></option>
                                            </select>                   
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="examiner" class="col-sm-3 "><?php echo xlt('Examiner'); ?></label>
                                        <div class="col-sm-9">
                                            <?php $examiner = ($check_res['examiner']) ? text($check_res['examiner']) : text($last_record['examiner']); ?>
                                            <select name="examiner" id="examiner" class="form-control">
                                                <?php echo get_examiner_name_dregree($examiner); ?>
                                            </select>                                            
                                            <small class="text-danger examiner_error"></small>
                                        </div>                                    
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="" class="col-sm-2"><?php echo xlt('Date'); ?></label>
                                        <div class="col-sm-10">
                                            <input type="text" name="date" class="form-control newDatePicker" value="<?php echo ( $check_res['date'] ) ? date('m/d/Y', strtotime($check_res['date'])): date('m/d/Y'); ?>" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="dob" class="col-sm-2"><?php echo xlt('DOB'); ?></label>
                                        <div class="col-sm-10">
                                            <input type="text" name="dob" class="form-control newDatePicker" value="<?php echo ( $check_res['dob'] ) ? date('m/d/Y', strtotime($check_res['dob'])) : date('m/d/Y', strtotime($last_record['dob'])); ?>" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="clearfix"></div>

                            <div class="col-md-12" style="margin-top: 30px">
                                <div class="form-group">
                                    <label for=""><?php echo xlt('Problem Area'); ?></label>
                                    <?php $problem_arr = array('A. Psychiatric/Mental/Emotional', 'B. Medical/Health', 'C. Vocational', 'D. Money Management/Finances', 'E. Social Relationships', 'F. Family', 'G. Basic Living Skills', 'H. Housing', 'I. Community/Legal', 'J. Other'); 

                                        $problem_area = ($check_res['problem_area']) ? text($check_res['problem_area']) : text($last_record['problem_area']);

                                    ?>
                                    <select name="problem_area" >
                                        <option value=""><?php echo xlt('Choose'); ?></option>
                                        <?php foreach($problem_arr as $problem): ?>
                                            <option value="<?php echo $problem; ?>" <?php echo ($problem == $problem_area) ? 'selected': ''; ?> ><?php echo xlt($problem); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for=""><?php echo xlt('(Use following space to specifically describe a prioritized list of issues from the Comprehensive Assessment that will be addressed in the following goal including behavior relating and area of impairment)'); ?></label>
                                    <textarea name="describe_comprehensive_assessment" rows="3" class="form-control"><?php echo ($check_res['describe_comprehensive_assessment']) ? text($check_res['describe_comprehensive_assessment']) : text($last_record['describe_comprehensive_assessment']); ?></textarea>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div class="col-md-12">
                                <h3 class="text-center"><?php echo xlt('GOAL SECTION'); ?></h3>

                                <div class="form-group">
                                    <label for=""><?php echo xlt('Client\'s statement of overall goal or need (Must be in client\'s own words):'); ?></label>
                                    <textarea name="overall_goal" rows="3" class="form-control"><?php echo ($check_res['overall_goal']) ? text($check_res['overall_goal']) : text($last_record['overall_goal']); ?></textarea>
                                </div>
                            </div>

                            <div class="col-md-12" style="margin-top: 20px">
                                <h3 class="text-center"><?php echo xlt('OBJECTIVES'); ?></h3>
                                <h4 class="text-center"><?php echo xlt('Objectives must address the emotional, behavioral, and skill training needs identified by the member'); ?></h4>
                            </div>

                            <!------------------- OBJECTIVE 1  ------------------------->

                            <!-- Overall Objective 1 -->
                            <div class="col-md-12" style="margin-top: 10px; padding-top: 10px;">
                                <div style="border-top: 1px solid #aaa;">&nbsp;</div>
                                <div class="form-group">
                                    <label for=""><?php echo xlt('Objective 1:'); ?></label>
                                    <input type="text" name="overall_obj1" value="<?php echo ($check_res['overall_obj1']) ? text($check_res['overall_obj1']) : text($last_record['overall_obj1']); ?>">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-5"><?php echo xlt('Task Modality:'); ?></label>
                                        <div class="col-sm-7">
                                            <?php $task_modality_obj1 = ($check_res['overall_obj1_task_modality']) ? text($check_res['overall_obj1_task_modality']) : text($last_record['overall_obj1_task_modality']); ?>
                                            <select name="overall_obj1_task_modality" id="" class="form-control">
                                                <?php echo get_task_modality($task_modality_obj1); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>                                
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Frequency:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $overall_obj1_frequency = ($check_res['overall_obj1_frequency']) ? text($check_res['overall_obj1_frequency']) : text($last_record['overall_obj1_frequency']); ?>
                                            <select name="overall_obj1_frequency" class="form-control">
                                                <?php echo get_weekly_frequency($overall_obj1_frequency); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Duration:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $overall_obj1_duration = ($check_res['overall_obj1_duration']) ? text($check_res['overall_obj1_duration']) : text($last_record['overall_obj1_duration']); ?>
                                            <select name="overall_obj1_duration" id="" class="form-control">
                                                <?php echo get_treatment_duration($overall_obj1_duration); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-3"><?php echo xlt('Place:'); ?></label>
                                        <div class="col-sm-9">
                                            <?php $overall_obj1_place = ($check_res['overall_obj1_place']) ? text($check_res['overall_obj1_place']) : text($last_record['overall_obj1_place']); ?>
                                            <select name="overall_obj1_place" id="" class="form-control">
                                                <?php echo get_task_place($overall_obj1_place); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                            </div>


                            <!-- Overall 90 Day Review -->
                            <div class="col-md-12" style="margin-top: 10px; padding-top: 10px;">
                                <div >&nbsp;</div>
                                <div class="form-group">
                                    <label for=""><?php echo xlt('90 Day Review:'); ?></label>
                                    <?php $overall_obj1_90_review = ($check_res['overall_obj1_90_review']) ? text($check_res['overall_obj1_90_review']) : text($last_record['overall_obj1_90_review']); ?>
                                    <input type="text" name="overall_obj1_90_review" value="<?php echo $overall_obj1_90_review; ?>">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-5"><?php echo xlt('Task Modality:'); ?></label>
                                        <div class="col-sm-7">
                                            <?php $overall_obj1_90_review_task_modality = ($check_res['overall_obj1_90_review_task_modality']) ? text($check_res['overall_obj1_90_review_task_modality']) : text($last_record['overall_obj1_90_review_task_modality']); ?>
                                            <select name="overall_obj1_90_review_task_modality" class="form-control">
                                                <?php echo get_task_modality($overall_obj1_90_review_task_modality); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>                                
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Frequency:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $overall_obj1_90_review_frequency = ($check_res['overall_obj1_90_review_frequency']) ? text($check_res['overall_obj1_90_review_frequency']) : text($last_record['overall_obj1_90_review_frequency']); ?>
                                            <select name="overall_obj1_90_review_frequency" class="form-control">
                                                <?php echo get_weekly_frequency($overall_obj1_90_review_frequency); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Duration:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $overall_obj1_90_review_duration = ($check_res['overall_obj1_90_review_duration']) ? text($check_res['overall_obj1_90_review_duration']) : text($last_record['overall_obj1_90_review_duration']); ?>
                                            <select name="overall_obj1_90_review_duration" id="" class="form-control">
                                                <?php echo get_treatment_duration($overall_obj1_90_review_duration); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-3"><?php echo xlt('Place:'); ?></label>
                                        <div class="col-sm-9">
                                            <?php $overall_obj1_90_review_place = ($check_res['overall_obj1_90_review_place']) ? text($check_res['overall_obj1_90_review_place']) : text($last_record['overall_obj1_90_review_place']); ?>
                                            <select name="overall_obj1_90_review_place" id="" class="form-control">
                                                <?php echo get_task_place($overall_obj1_90_review_place); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                            </div>


                            <!-- Overall 180 Day Review -->
                            <div class="col-md-12" style="margin-top: 10px; padding-top: 10px;">
                                <div >&nbsp;</div>
                                <div class="form-group">
                                    <label for=""><?php echo xlt('180 Day Review:'); ?></label>
                                    <?php $overall_obj1_180_review = ($check_res['overall_obj1_180_review']) ? text($check_res['overall_obj1_180_review']) : text($last_record['overall_obj1_180_review']); ?>
                                    <input type="text" name="overall_obj1_180_review" value="<?php echo $overall_obj1_180_review; ?>">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-5"><?php echo xlt('Task Modality:'); ?></label>
                                        <div class="col-sm-7">
                                            <?php $overall_obj1_180_review_task_modality = ($check_res['overall_obj1_180_review_task_modality']) ? text($check_res['overall_obj1_180_review_task_modality']) : text($last_record['overall_obj1_180_review_task_modality']); ?>
                                            <select name="overall_obj1_180_review_task_modality" class="form-control">
                                                <?php echo get_task_modality($overall_obj1_180_review_task_modality); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>                                
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Frequency:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $overall_obj1_180_review_frequency = ($check_res['overall_obj1_180_review_frequency']) ? text($check_res['overall_obj1_180_review_frequency']) : text($last_record['overall_obj1_180_review_frequency']); ?>
                                            <select name="overall_obj1_180_review_frequency" class="form-control">
                                                <?php echo get_weekly_frequency($overall_obj1_180_review_frequency); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Duration:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $overall_obj1_180_review_duration = ($check_res['overall_obj1_180_review_duration']) ? text($check_res['overall_obj1_180_review_duration']) : text($last_record['overall_obj1_180_review_duration']); ?>
                                            <select name="overall_obj1_180_review_duration" id="" class="form-control">
                                                <?php echo get_treatment_duration($overall_obj1_180_review_duration); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-3"><?php echo xlt('Place:'); ?></label>
                                        <div class="col-sm-9">
                                            <?php $overall_obj1_180_review_place = ($check_res['overall_obj1_180_review_place']) ? text($check_res['overall_obj1_180_review_place']) : text($last_record['overall_obj1_180_review_place']); ?>
                                            <select name="overall_obj1_180_review_place" id="" class="form-control">
                                                <?php echo get_task_place($overall_obj1_180_review_place); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                            </div>

                            <!-- Overall 270 Day Review -->
                            <div class="col-md-12" style="margin-top: 10px; padding-top: 10px; ">
                                <div >&nbsp;</div>
                                <div class="form-group">
                                    <label for=""><?php echo xlt('270 Day Review:'); ?></label>
                                    <?php $overall_obj1_270_review = ($check_res['overall_obj1_270_review']) ? text($check_res['overall_obj1_270_review']) : text($last_record['overall_obj1_270_review']); ?>
                                    <input type="text" name="overall_obj1_270_review" value="<?php echo $overall_obj1_270_review; ?>">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-5"><?php echo xlt('Task Modality:'); ?></label>
                                        <div class="col-sm-7">
                                            <?php $overall_obj1_270_review_task_modality = ($check_res['overall_obj1_270_review_task_modality']) ? text($check_res['overall_obj1_270_review_task_modality']) : text($last_record['overall_obj1_270_review_task_modality']); ?>
                                            <select name="overall_obj1_270_review_task_modality" class="form-control">
                                                <?php echo get_task_modality($overall_obj1_270_review_task_modality); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>                                
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Frequency:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $overall_obj1_270_review_frequency = ($check_res['overall_obj1_270_review_frequency']) ? text($check_res['overall_obj1_270_review_frequency']) : text($last_record['overall_obj1_270_review_frequency']); ?>
                                            <select name="overall_obj1_270_review_frequency" class="form-control">
                                                <?php echo get_weekly_frequency($overall_obj1_270_review_frequency); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Duration:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $overall_obj1_270_review_duration = ($check_res['overall_obj1_270_review_duration']) ? text($check_res['overall_obj1_270_review_duration']) : text($last_record['overall_obj1_270_review_duration']); ?>
                                            <select name="overall_obj1_270_review_duration" id="" class="form-control">
                                                <?php echo get_treatment_duration($overall_obj1_270_review_duration); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-3"><?php echo xlt('Place:'); ?></label>
                                        <div class="col-sm-9">
                                            <?php $overall_obj1_270_review_place = ($check_res['overall_obj1_270_review_place']) ? text($check_res['overall_obj1_270_review_place']) : text($last_record['overall_obj1_270_review_place']); ?>
                                            <select name="overall_obj1_270_review_place" id="" class="form-control">
                                                <?php echo get_task_place($overall_obj1_270_review_place); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix">&nbsp;</div>

                            <!------------------- OBJECTIVE 2  ------------------------->                           

                            <!-- Objective 2 -->
                            <div class="col-md-12" style="margin-top: 10px; padding-top: 10px;">
                                <div style="border-top: 1px solid #aaa;">&nbsp;</div>
                                <div class="form-group">
                                    <label for=""><?php echo xlt('Objective 2:'); ?></label>
                                    <?php $overall_obj2 = ($check_res['overall_obj2']) ? text($check_res['overall_obj2']) : text($last_record['overall_obj2']); ?>
                                    <input type="text" name="overall_obj2" value="<?php echo $overall_obj2; ?>">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-5"><?php echo xlt('Task Modality:'); ?></label>
                                        <div class="col-sm-7">
                                            <?php $overall_obj2_task_modality = ($check_res['overall_obj2_task_modality']) ? text($check_res['overall_obj2_task_modality']) : text($last_record['overall_obj2_task_modality']); ?>
                                            <select name="overall_obj2_task_modality" id="" class="form-control">
                                                <?php echo get_task_modality($overall_obj2_task_modality); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>                                
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Frequency:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $overall_obj2_frequency = ($check_res['overall_obj2_frequency']) ? text($check_res['overall_obj2_frequency']) : text($last_record['overall_obj2_frequency']); ?>
                                            <select name="overall_obj2_frequency" class="form-control">
                                                <?php echo get_weekly_frequency($overall_obj2_frequency); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Duration:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $overall_obj2_duration = ($check_res['overall_obj2_duration']) ? text($check_res['overall_obj2_duration']) : text($last_record['overall_obj2_duration']); ?>
                                            <select name="overall_obj2_duration" id="" class="form-control">
                                                <?php echo get_treatment_duration($overall_obj2_duration); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-3"><?php echo xlt('Place:'); ?></label>
                                        <div class="col-sm-9">
                                            <?php $overall_obj2_place = ($check_res['overall_obj2_place']) ? text($check_res['overall_obj2_place']) : text($last_record['overall_obj2_place']); ?>
                                            <select name="overall_obj2_place" id="" class="form-control">
                                                <?php echo get_task_place($overall_obj2_place); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                            </div>


                            <!-- Objective 2 90 Day Review -->
                            <div class="col-md-12" style="margin-top: 10px; padding-top: 10px;">
                                <div >&nbsp;</div>
                                <div class="form-group">
                                    <label for=""><?php echo xlt('90 Day Review:'); ?></label>
                                    <?php $overall_obj2_90_review = ($check_res['overall_obj2_90_review']) ? text($check_res['overall_obj2_90_review']) : text($last_record['overall_obj2_90_review']); ?>
                                    <input type="text" name="overall_obj2_90_review" value="<?php echo $overall_obj2_90_review; ?>">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-5"><?php echo xlt('Task Modality:'); ?></label>
                                        <div class="col-sm-7">
                                            <?php $overall_obj2_90_review_task_modality = ($check_res['overall_obj2_90_review_task_modality']) ? text($check_res['overall_obj2_90_review_task_modality']) : text($last_record['overall_obj2_90_review_task_modality']); ?>
                                            <select name="overall_obj2_90_review_task_modality" class="form-control">
                                                <?php echo get_task_modality($overall_obj2_90_review_task_modality); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>                                
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Frequency:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $overall_obj2_90_review_frequency = ($check_res['overall_obj2_90_review_frequency']) ? text($check_res['overall_obj2_90_review_frequency']) : text($last_record['overall_obj2_90_review_frequency']); ?>
                                            <select name="overall_obj2_90_review_frequency" class="form-control">
                                                <?php echo get_weekly_frequency($overall_obj2_90_review_frequency); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Duration:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $overall_obj2_90_review_duration = ($check_res['overall_obj2_90_review_duration']) ? text($check_res['overall_obj2_90_review_duration']) : text($last_record['overall_obj2_90_review_duration']); ?>
                                            <select name="overall_obj2_90_review_duration" id="" class="form-control">
                                                <?php echo get_treatment_duration($overall_obj2_90_review_duration); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-3"><?php echo xlt('Place:'); ?></label>
                                        <div class="col-sm-9">
                                            <?php $overall_obj2_90_review_place = ($check_res['overall_obj2_90_review_place']) ? text($check_res['overall_obj2_90_review_place']) : text($last_record['overall_obj2_90_review_place']); ?>
                                            <select name="overall_obj2_90_review_place" id="" class="form-control">
                                                <?php echo get_task_place($overall_obj2_90_review_place); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                            </div>


                            <!-- Overall Obj2 180 Day Review -->
                            <div class="col-md-12" style="margin-top: 10px; padding-top: 10px;">
                                <div >&nbsp;</div>
                                <div class="form-group">
                                    <label for=""><?php echo xlt('180 Day Review:'); ?></label>
                                    <?php $overall_obj2_180_review = ($check_res['overall_obj2_180_review']) ? text($check_res['overall_obj2_180_review']) : text($last_record['overall_obj2_180_review']); ?>
                                    <input type="text" name="overall_obj2_180_review" value="<?php echo $overall_obj2_180_review; ?>">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-5"><?php echo xlt('Task Modality:'); ?></label>
                                        <div class="col-sm-7">
                                            <?php $overall_obj2_180_review_task_modality = ($check_res['overall_obj2_180_review_task_modality']) ? text($check_res['overall_obj2_180_review_task_modality']) : text($last_record['overall_obj2_180_review_task_modality']); ?>
                                            <select name="overall_obj2_180_review_task_modality" class="form-control">
                                                <?php echo get_task_modality($overall_obj2_180_review_task_modality); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>                                
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Frequency:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $overall_obj2_180_review_frequency = ($check_res['overall_obj2_180_review_frequency']) ? text($check_res['overall_obj2_180_review_frequency']) : text($last_record['overall_obj2_180_review_frequency']); ?>
                                            <select name="overall_obj2_180_review_frequency" class="form-control">
                                                <?php echo get_weekly_frequency($overall_obj2_180_review_frequency); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Duration:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $overall_obj2_180_review_duration = ($check_res['overall_obj2_180_review_duration']) ? text($check_res['overall_obj2_180_review_duration']) : text($last_record['overall_obj2_180_review_duration']); ?>
                                            <select name="overall_obj2_180_review_duration" id="" class="form-control">
                                                <?php echo get_treatment_duration($overall_obj2_180_review_duration); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-3"><?php echo xlt('Place:'); ?></label>
                                        <div class="col-sm-9">
                                            <?php $overall_obj2_180_review_place = ($check_res['overall_obj2_180_review_place']) ? text($check_res['overall_obj2_180_review_place']) : text($last_record['overall_obj2_180_review_place']); ?>
                                            <select name="overall_obj2_180_review_place" id="" class="form-control">
                                                <?php echo get_task_place($overall_obj2_180_review_place); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                            </div>

                            <!-- Overall Obj2 270 Day Review -->
                            <div class="col-md-12" style="margin-top: 10px; padding-top: 10px; ">
                                <div>&nbsp;</div>
                                <div class="form-group">
                                    <label for=""><?php echo xlt('270 Day Review:'); ?></label>
                                    <?php $overall_obj2_270_review = ($check_res['overall_obj2_270_review']) ? text($check_res['overall_obj2_270_review']) : text($last_record['overall_obj2_270_review']); ?>
                                    <input type="text" name="overall_obj2_270_review" value="<?php echo $overall_obj2_270_review; ?>">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-5"><?php echo xlt('Task Modality:'); ?></label>
                                        <div class="col-sm-7">
                                            <?php $overall_obj2_270_review_task_modality = ($check_res['overall_obj2_270_review_task_modality']) ? text($check_res['overall_obj2_270_review_task_modality']) : text($last_record['overall_obj2_270_review_task_modality']); ?>
                                            <select name="overall_obj2_270_review_task_modality" class="form-control">
                                                <?php echo get_task_modality($overall_obj2_270_review_task_modality); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>                                
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Frequency:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $overall_obj2_270_review_frequency = ($check_res['overall_obj2_270_review_frequency']) ? text($check_res['overall_obj2_270_review_frequency']) : text($last_record['overall_obj2_270_review_frequency']); ?>
                                            <select name="overall_obj2_270_review_frequency" class="form-control">
                                                <?php echo get_weekly_frequency($overall_obj2_270_review_frequency); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Duration:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $overall_obj2_270_review_duration = ($check_res['overall_obj2_270_review_duration']) ? text($check_res['overall_obj2_270_review_duration']) : text($last_record['overall_obj2_270_review_duration']); ?>
                                            <select name="overall_obj2_270_review_duration" id="" class="form-control">
                                                <?php echo get_treatment_duration($overall_obj2_270_review_duration); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-3"><?php echo xlt('Place:'); ?></label>
                                        <div class="col-sm-9">
                                            <?php $overall_obj2_270_review_place = ($check_res['overall_obj2_270_review_place']) ? text($check_res['overall_obj2_270_review_place']) : text($last_record['overall_obj2_270_review_place']); ?>
                                            <select name="overall_obj2_270_review_place" id="" class="form-control">
                                                <?php echo get_task_place($overall_obj2_270_review_place); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                            </div>

                            <div class="clearfix"></div>

                            <!-------------------- SHORT-TERM GOAL ------------------------>

                            <div class="col-md-12" style="margin-top: 30px">
                                <div style="border-top: 1px solid #aaa;">&nbsp;</div>
                                <div class="form-group">
                                    <label for=""><?php echo xlt('Short-term goal for Client:'); ?></label>
                                    <?php $short_term_goal = ($check_res['short_term_goal']) ? text($check_res['short_term_goal']) : text($last_record['short_term_goal']); ?>
                                    <textarea name="short_term_goal" rows="3" class="form-control"><?php echo $short_term_goal; ?></textarea>
                                </div>                                
                            </div>

                            <!---------------- Short Term Obj1 ----------------------->
                            <div class="col-md-12" style="margin-top: 10px; padding-top: 10px;">
                                <div style="border-top: 1px solid #aaa;">&nbsp;</div>
                                <div class="form-group">
                                    <label for=""><?php echo xlt('Objective 1:'); ?></label>
                                    <?php $short_term_obj1 = ($check_res['short_term_obj1']) ? text($check_res['short_term_obj1']) : text($last_record['short_term_obj1']); ?>
                                    <input type="text" name="short_term_obj1" value="<?php echo $short_term_obj1; ?>">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-5"><?php echo xlt('Task Modality:'); ?></label>
                                        <div class="col-sm-7">
                                            <?php $short_term_obj1_task_modality = ($check_res['short_term_obj1_task_modality']) ? text($check_res['short_term_obj1_task_modality']) : text($last_record['short_term_obj1_task_modality']); ?>
                                            <select name="short_term_obj1_task_modality" id="" class="form-control">
                                                <?php echo get_task_modality($short_term_obj1_task_modality); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>                                
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Frequency:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $short_term_obj1_frequency = ($check_res['short_term_obj1_frequency']) ? text($check_res['short_term_obj1_frequency']) : text($last_record['short_term_obj1_frequency']); ?>
                                            <select name="short_term_obj1_frequency" class="form-control">
                                                <?php echo get_weekly_frequency($short_term_obj1_frequency); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Duration:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $short_term_obj1_duration = ($check_res['short_term_obj1_duration']) ? text($check_res['short_term_obj1_duration']) : text($last_record['short_term_obj1_duration']); ?>
                                            <select name="short_term_obj1_duration" id="" class="form-control">
                                                <?php echo get_treatment_duration($short_term_obj1_duration); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-3"><?php echo xlt('Place:'); ?></label>
                                        <div class="col-sm-9">
                                            <?php $short_term_obj1_place = ($check_res['short_term_obj1_place']) ? text($check_res['short_term_obj1_place']) : text($last_record['short_term_obj1_place']); ?>
                                            <select name="short_term_obj1_place" id="" class="form-control">
                                                <?php echo get_task_place($short_term_obj1_place); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                            </div>


                            <!-- Short Term Obj1 90 Day Review -->
                            <div class="col-md-12" style="margin-top: 10px; padding-top: 10px;">
                                <div >&nbsp;</div>
                                <div class="form-group">
                                    <label for=""><?php echo xlt('90 Day Review:'); ?></label>
                                    <?php $short_term_obj1_90_review = ($check_res['short_term_obj1_90_review']) ? text($check_res['short_term_obj1_90_review']) : text($last_record['short_term_obj1_90_review']); ?>
                                    <input type="text" name="short_term_obj1_90_review" value="<?php echo $short_term_obj1_90_review; ?>">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-5"><?php echo xlt('Task Modality:'); ?></label>
                                        <div class="col-sm-7">
                                            <?php $short_term_obj1_90_review_task_modality = ($check_res['short_term_obj1_90_review_task_modality']) ? text($check_res['short_term_obj1_90_review_task_modality']) : text($last_record['short_term_obj1_90_review_task_modality']); ?>
                                            <select name="short_term_obj1_90_review_task_modality" class="form-control">
                                                <?php echo get_task_modality($short_term_obj1_90_review_task_modality); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>                                
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Frequency:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $short_term_obj1_90_review_frequency = ($check_res['short_term_obj1_90_review_frequency']) ? text($check_res['short_term_obj1_90_review_frequency']) : text($last_record['short_term_obj1_90_review_frequency']); ?>
                                            <select name="short_term_obj1_90_review_frequency" class="form-control">
                                                <?php echo get_weekly_frequency($short_term_obj1_90_review_frequency); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Duration:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $short_term_obj1_90_review_duration = ($check_res['short_term_obj1_90_review_duration']) ? text($check_res['short_term_obj1_90_review_duration']) : text($last_record['short_term_obj1_90_review_duration']); ?>
                                            <select name="short_term_obj1_90_review_duration" id="" class="form-control">
                                                <?php echo get_treatment_duration($short_term_obj1_90_review_duration); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-3"><?php echo xlt('Place:'); ?></label>
                                        <div class="col-sm-9">
                                            <?php $short_term_obj1_90_review_place = ($check_res['short_term_obj1_90_review_place']) ? text($check_res['short_term_obj1_90_review_place']) : text($last_record['short_term_obj1_90_review_place']); ?>
                                            <select name="short_term_obj1_90_review_place" id="" class="form-control">
                                                <?php echo get_task_place($short_term_obj1_90_review_place); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                            </div>


                            <!-- Short Term Obj1 180 Day Review -->
                            <div class="col-md-12" style="margin-top: 10px; padding-top: 10px;">
                                <div >&nbsp;</div>
                                <div class="form-group">
                                    <label for=""><?php echo xlt('180 Day Review:'); ?></label>
                                    <?php $short_term_obj1_180_review = ($check_res['short_term_obj1_180_review']) ? text($check_res['short_term_obj1_180_review']) : text($last_record['short_term_obj1_180_review']); ?>
                                    <input type="text" name="short_term_obj1_180_review" value="<?php echo $short_term_obj1_180_review; ?>">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-5"><?php echo xlt('Task Modality:'); ?></label>
                                        <div class="col-sm-7">
                                            <?php $short_term_obj1_180_review_task_modality = ($check_res['short_term_obj1_180_review_task_modality']) ? text($check_res['short_term_obj1_180_review_task_modality']) : text($last_record['short_term_obj1_180_review_task_modality']); ?>
                                            <select name="short_term_obj1_180_review_task_modality" class="form-control">
                                                <?php echo get_task_modality($short_term_obj1_180_review_task_modality); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>                                
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Frequency:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $short_term_obj1_180_review_frequency = ($check_res['short_term_obj1_180_review_frequency']) ? text($check_res['short_term_obj1_180_review_frequency']) : text($last_record['short_term_obj1_180_review_frequency']); ?>
                                            <select name="short_term_obj1_180_review_frequency" class="form-control">
                                                <?php echo get_weekly_frequency($short_term_obj1_180_review_frequency); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Duration:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $short_term_obj1_180_review_duration = ($check_res['short_term_obj1_180_review_duration']) ? text($check_res['short_term_obj1_180_review_duration']) : text($last_record['short_term_obj1_180_review_duration']); ?>
                                            <select name="short_term_obj1_180_review_duration" id="" class="form-control">
                                                <?php echo get_treatment_duration($short_term_obj1_180_review_duration); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-3"><?php echo xlt('Place:'); ?></label>
                                        <div class="col-sm-9">
                                            <?php $short_term_obj1_180_review_place = ($check_res['short_term_obj1_180_review_place']) ? text($check_res['short_term_obj1_180_review_place']) : text($last_record['short_term_obj1_180_review_place']); ?>
                                            <select name="short_term_obj1_180_review_place" id="" class="form-control">
                                                <?php echo get_task_place($short_term_obj1_180_review_place); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                            </div>

                            <!-- Short Term Obj1 270 Day Review -->
                            <div class="col-md-12" style="margin-top: 10px; padding-top: 10px; ">
                                <div>&nbsp;</div>
                                <div class="form-group">
                                    <label for=""><?php echo xlt('270 Day Review:'); ?></label>
                                    <?php $short_term_obj1_270_review = ($check_res['short_term_obj1_270_review']) ? text($check_res['short_term_obj1_270_review']) : text($last_record['short_term_obj1_270_review']); ?>
                                    <input type="text" name="short_term_obj1_270_review" value="<?php echo $short_term_obj1_270_review; ?>">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-5"><?php echo xlt('Task Modality:'); ?></label>
                                        <div class="col-sm-7">
                                            <?php $short_term_obj1_270_review_task_modality = ($check_res['short_term_obj1_270_review_task_modality']) ? text($check_res['short_term_obj1_270_review_task_modality']) : text($last_record['short_term_obj1_270_review_task_modality']); ?>
                                            <select name="short_term_obj1_270_review_task_modality" class="form-control">
                                                <?php echo get_task_modality($short_term_obj1_270_review_task_modality); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>                                
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Frequency:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $short_term_obj1_270_review_frequency = ($check_res['short_term_obj1_270_review_frequency']) ? text($check_res['short_term_obj1_270_review_frequency']) : text($last_record['short_term_obj1_270_review_frequency']); ?>
                                            <select name="short_term_obj1_270_review_frequency" class="form-control">
                                                <?php echo get_weekly_frequency($short_term_obj1_270_review_frequency); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Duration:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $short_term_obj1_270_review_duration = ($check_res['short_term_obj1_270_review_duration']) ? text($check_res['short_term_obj1_270_review_duration']) : text($last_record['short_term_obj1_270_review_duration']); ?>
                                            <select name="short_term_obj1_270_review_duration" id="" class="form-control">
                                                <?php echo get_treatment_duration($short_term_obj1_270_review_duration); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-3"><?php echo xlt('Place:'); ?></label>
                                        <div class="col-sm-9">
                                            <?php $short_term_obj1_270_review_place = ($check_res['short_term_obj1_270_review_place']) ? text($check_res['short_term_obj1_270_review_place']) : text($last_record['short_term_obj1_270_review_place']); ?>
                                            <select name="short_term_obj1_270_review_place" id="" class="form-control">
                                                <?php echo get_task_place($short_term_obj1_270_review_place); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                            </div>

                            <div class="clearfix"></div>

                             <!---------------- Short Term Obj2 ----------------------->
                            <div class="col-md-12" style="margin-top: 10px; padding-top: 10px;">
                                <div style="border-top: 1px solid #aaa;">&nbsp;</div>
                                <div class="form-group">
                                    <label for=""><?php echo xlt('Objective 2:'); ?></label>
                                    <?php $short_term_obj2 = ($check_res['short_term_obj2']) ? text($check_res['short_term_obj2']) : text($last_record['short_term_obj2']); ?>
                                    <input type="text" name="short_term_obj2" value="<?php echo $short_term_obj2; ?>">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-5"><?php echo xlt('Task Modality:'); ?></label>
                                        <div class="col-sm-7">
                                            <?php $short_term_obj2_task_modality = ($check_res['short_term_obj2_task_modality']) ? text($check_res['short_term_obj2_task_modality']) : text($last_record['short_term_obj2_task_modality']); ?>
                                            <select name="short_term_obj2_task_modality" id="" class="form-control">
                                                <?php echo get_task_modality($short_term_obj2_task_modality); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>                                
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Frequency:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $short_term_obj2_frequency = ($check_res['short_term_obj2_frequency']) ? text($check_res['short_term_obj2_frequency']) : text($last_record['short_term_obj2_frequency']); ?>
                                            <select name="short_term_obj2_frequency" class="form-control">
                                                <?php echo get_weekly_frequency($short_term_obj2_frequency); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Duration:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $short_term_obj2_duration = ($check_res['short_term_obj2_duration']) ? text($check_res['short_term_obj2_duration']) : text($last_record['short_term_obj2_duration']); ?>
                                            <select name="short_term_obj2_duration" id="" class="form-control">
                                                <?php echo get_treatment_duration($short_term_obj2_duration); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-3"><?php echo xlt('Place:'); ?></label>
                                        <div class="col-sm-9">
                                            <?php $short_term_obj2_place = ($check_res['short_term_obj2_place']) ? text($check_res['short_term_obj2_place']) : text($last_record['short_term_obj2_place']); ?>
                                            <select name="short_term_obj2_place" id="" class="form-control">
                                                <?php echo get_task_place($short_term_obj2_place); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                            </div>


                            <!-- Short Term Obj2 90 Day Review -->
                            <div class="col-md-12" style="margin-top: 10px; padding-top: 10px;">
                                <div >&nbsp;</div>
                                <div class="form-group">
                                    <label for=""><?php echo xlt('90 Day Review:'); ?></label>
                                    <?php $short_term_obj2_90_review = ($check_res['short_term_obj2_90_review']) ? text($check_res['short_term_obj2_90_review']) : text($last_record['short_term_obj2_90_review']); ?>
                                    <input type="text" name="short_term_obj2_90_review" value="<?php echo $short_term_obj2_90_review; ?>">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-5"><?php echo xlt('Task Modality:'); ?></label>
                                        <div class="col-sm-7">
                                            <?php $short_term_obj2_90_review_task_modality = ($check_res['short_term_obj2_90_review_task_modality']) ? text($check_res['short_term_obj2_90_review_task_modality']) : text($last_record['short_term_obj2_90_review_task_modality']); ?>
                                            <select name="short_term_obj2_90_review_task_modality" class="form-control">
                                                <?php echo get_task_modality($short_term_obj2_90_review_task_modality); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>                                
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Frequency:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $short_term_obj2_90_review_frequency = ($check_res['short_term_obj2_90_review_frequency']) ? text($check_res['short_term_obj2_90_review_frequency']) : text($last_record['short_term_obj2_90_review_frequency']); ?>
                                            <select name="short_term_obj2_90_review_frequency" class="form-control">
                                                <?php echo get_weekly_frequency($short_term_obj2_90_review_frequency); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Duration:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $short_term_obj2_90_review_duration = ($check_res['short_term_obj2_90_review_duration']) ? text($check_res['short_term_obj2_90_review_duration']) : text($last_record['short_term_obj2_90_review_duration']); ?>
                                            <select name="short_term_obj2_90_review_duration" id="" class="form-control">
                                                <?php echo get_treatment_duration($short_term_obj2_90_review_duration); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-3"><?php echo xlt('Place:'); ?></label>
                                        <div class="col-sm-9">
                                            <?php $short_term_obj2_90_review_place = ($check_res['short_term_obj2_90_review_place']) ? text($check_res['short_term_obj2_90_review_place']) : text($last_record['short_term_obj2_90_review_place']); ?>
                                            <select name="short_term_obj2_90_review_place" id="" class="form-control">
                                                <?php echo get_task_place($short_term_obj2_90_review_place); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                            </div>


                            <!-- Short Term Obj2 180 Day Review -->
                            <div class="col-md-12" style="margin-top: 10px; padding-top: 10px;">
                                <div >&nbsp;</div>
                                <div class="form-group">
                                    <label for=""><?php echo xlt('180 Day Review:'); ?></label>
                                    <?php $short_term_obj2_180_review = ($check_res['short_term_obj2_180_review']) ? text($check_res['short_term_obj2_180_review']) : text($last_record['short_term_obj2_180_review']); ?>
                                    <input type="text" name="short_term_obj2_180_review" value="<?php echo $short_term_obj2_180_review; ?>">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-5"><?php echo xlt('Task Modality:'); ?></label>
                                        <div class="col-sm-7">
                                            <?php $short_term_obj2_180_review_task_modality = ($check_res['short_term_obj2_180_review_task_modality']) ? text($check_res['short_term_obj2_180_review_task_modality']) : text($last_record['short_term_obj2_180_review_task_modality']); ?>
                                            <select name="short_term_obj2_180_review_task_modality" class="form-control">
                                                <?php echo get_task_modality($short_term_obj2_180_review_task_modality); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>                                
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Frequency:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $short_term_obj2_180_review_frequency = ($check_res['short_term_obj2_180_review_frequency']) ? text($check_res['short_term_obj2_180_review_frequency']) : text($last_record['short_term_obj2_180_review_frequency']); ?>
                                            <select name="short_term_obj2_180_review_frequency" class="form-control">
                                                <?php echo get_weekly_frequency($short_term_obj2_180_review_frequency); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Duration:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $short_term_obj2_180_review_duration = ($check_res['short_term_obj2_180_review_duration']) ? text($check_res['short_term_obj2_180_review_duration']) : text($last_record['short_term_obj2_180_review_duration']); ?>
                                            <select name="short_term_obj2_180_review_duration" id="" class="form-control">
                                                <?php echo get_treatment_duration($short_term_obj2_180_review_duration); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-3"><?php echo xlt('Place:'); ?></label>
                                        <div class="col-sm-9">
                                            <?php $short_term_obj2_180_review_place = ($check_res['short_term_obj2_180_review_place']) ? text($check_res['short_term_obj2_180_review_place']) : text($last_record['short_term_obj2_180_review_place']); ?>
                                            <select name="short_term_obj2_180_review_place" id="" class="form-control">
                                                <?php echo get_task_place($short_term_obj2_180_review_place); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                            </div>

                            <!-- Short Term Obj2 270 Day Review -->
                            <div class="col-md-12" style="margin-top: 10px; padding-top: 10px; ">
                                <div>&nbsp;</div>
                                <div class="form-group">
                                    <label for=""><?php echo xlt('270 Day Review:'); ?></label>
                                    <?php $short_term_obj2_270_review = ($check_res['short_term_obj2_270_review']) ? text($check_res['short_term_obj2_270_review']) : text($last_record['short_term_obj2_270_review']); ?>
                                    <input type="text" name="short_term_obj2_270_review" value="<?php echo $short_term_obj2_270_review; ?>">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-5"><?php echo xlt('Task Modality:'); ?></label>
                                        <div class="col-sm-7">
                                            <?php $short_term_obj2_270_review_task_modality = ($check_res['short_term_obj2_270_review_task_modality']) ? text($check_res['short_term_obj2_270_review_task_modality']) : text($last_record['short_term_obj2_270_review_task_modality']); ?>
                                            <select name="short_term_obj2_270_review_task_modality" class="form-control">
                                                <?php echo get_task_modality($short_term_obj2_270_review_task_modality); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>                                
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Frequency:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $short_term_obj2_270_review_frequency = ($check_res['short_term_obj2_270_review_frequency']) ? text($check_res['short_term_obj2_270_review_frequency']) : text($last_record['short_term_obj2_270_review_frequency']); ?>
                                            <select name="short_term_obj2_270_review_frequency" class="form-control">
                                                <?php echo get_weekly_frequency($short_term_obj2_270_review_frequency); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-4"><?php echo xlt('Duration:'); ?></label>
                                        <div class="col-sm-8">
                                            <?php $short_term_obj2_270_review_duration = ($check_res['short_term_obj2_270_review_duration']) ? text($check_res['short_term_obj2_270_review_duration']) : text($last_record['short_term_obj2_270_review_duration']); ?>
                                            <select name="short_term_obj2_270_review_duration" id="" class="form-control">
                                                <?php echo get_treatment_duration($short_term_obj2_270_review_duration); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="" class="col-sm-3"><?php echo xlt('Place:'); ?></label>
                                        <div class="col-sm-9">
                                            <?php $short_term_obj2_270_review_place = ($check_res['short_term_obj2_270_review_place']) ? text($check_res['short_term_obj2_270_review_place']) : text($last_record['short_term_obj2_270_review_place']); ?>
                                            <select name="short_term_obj2_270_review_place" id="" class="form-control">
                                                <?php echo get_task_place($short_term_obj2_270_review_place); ?>
                                            </select>
                                        </div>                                        
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>         

                            <div class="col-md-12" style="margin-top: 20px">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="" class="col-sm-6"><?php echo xlt('Target Date for Attainment of Goals:'); ?></label>
                                        <div class="col-sm-6">
                                            <?php $target_date_goal = ($check_res['target_date_goal']) ? text($check_res['target_date_goal']) : text($last_record['target_date_goal']); ?>
                                            <input type="text" name="target_date_goal" value="<?php echo $target_date_goal; ?>" class="form-control datepicker" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="radio-inline">
                                          <strong><?php echo xlt('Who is responsible: '); ?></strong>
                                        </label>
                                        <?php $who_responsible = ($check_res['who_responsible']) ? text($check_res['who_responsible']) : text($last_record['who_responsible']); ?>
                                        <label class="radio-inline">
                                          <input type="radio" name="who_responsible"  value="Client" <?php echo ($who_responsible == 'Client') ? 'checked':''; ?> > <?php echo xlt('Client'); ?>
                                        </label>
                                        <label class="radio-inline">
                                          <input type="radio" name="who_responsible"  value="Therapist" <?php echo ($who_responsible == 'Therapist') ? 'checked':''; ?> > <?php echo xlt('Therapist'); ?>
                                        </label>
                                        <label class="radio-inline">
                                          <strong><?php echo xlt('Other:'); ?></strong>
                                        </label>
                                        <label class="radio-inline">
                                            <?php $who_responsible_other = ($check_res['who_responsible_other']) ? text($check_res['who_responsible_other']) : text($last_record['who_responsible_other']); ?>
                                          <input type="text" name="who_responsible_other"  value="<?php echo $who_responsible_other; ?>">
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="clearfix"></div>   

                            <div class="col-md-12" style="margin-top: 20px">                               

                                <div class="form-group">
                                    <label for="" class="col-sm-7"><?php echo xlt('Individual present at treatment planning meeting:'); ?></label>
                                    <div class="col-sm-5">
                                        <?php $individual_present = ($check_res['individual_present']) ? text($check_res['individual_present']) : text($last_record['individual_present']); ?>
                                        <input type="text" name="individual_present" class="form-control" value="<?php echo $individual_present; ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-7"><?php echo xlt('How will services be coordinated with those delivered by other providers and agencies?'); ?></label>
                                    <div class="col-sm-5">
                                        <?php $services_coordinated = ($check_res['services_coordinated']) ? text($check_res['services_coordinated']) : text($last_record['services_coordinated']); ?>
                                        <input type="text" name="services_coordinated" class="form-control" value="<?php echo $services_coordinated; ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-7"><?php echo xlt('Linkages with peer support services and other community resources: '); ?></label>
                                    <div class="col-sm-5">
                                        <?php $linkage_service = ($check_res['linkage_service']) ? text($check_res['linkage_service']) : text($last_record['linkage_service']); ?>
                                        <input type="text" name="linkage_service" class="form-control" value="<?php echo $linkage_service; ?>">
                                    </div>
                                </div>

                            </div>


                            <!-- FOR REVIEWS ONLY  -->
                            <div class="col-md-12" style="margin-top: 30px">
                                <h3 class="text-center"><?php echo xlt('FOR REVIEWS ONLY'); ?></h3>

                                <h4><?php echo xlt('Progress toward the Objectives:'); ?></h4>

                                <div class="form-group">
                                    <label for=""><?php echo xlt('90 Day Review:'); ?></label>
                                    <?php $progress_90_review = ($check_res['progress_90_review']) ? text($check_res['progress_90_review']) : text($last_record['progress_90_review']); ?>
                                    <textarea name="progress_90_review"  rows="3" class="form-control"><?php echo $progress_90_review; ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for=""><?php echo xlt('180 Day Review:'); ?></label>
                                    <?php $progress_180_review = ($check_res['progress_180_review']) ? text($check_res['progress_180_review']) : text($last_record['progress_180_review']); ?>
                                    <textarea name="progress_180_review"  rows="3" class="form-control"><?php echo $progress_180_review; ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for=""><?php echo xlt('270 Day Review:'); ?></label>
                                    <?php $progress_270_review = ($check_res['progress_270_review']) ? text($check_res['progress_270_review']) : text($last_record['progress_270_review']); ?>
                                    <textarea name="progress_270_review"  rows="3" class="form-control"><?php echo $progress_270_review; ?></textarea>
                                </div>

                                <h4 style="margin-top: 20px"><?php echo xlt('Explain Changes, amendments or deletion to Objectives:'); ?></h4>

                                <div class="form-group">
                                    <label for=""><?php echo xlt('90 Day Review:'); ?></label>
                                    <?php $changes_90_review = ($check_res['changes_90_review']) ? text($check_res['changes_90_review']) : text($last_record['changes_90_review']); ?>
                                    <textarea name="changes_90_review"  rows="3" class="form-control"><?php echo $changes_90_review; ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for=""><?php echo xlt('180 Day Review:'); ?></label>
                                    <?php $changes_180_review = ($check_res['changes_180_review']) ? text($check_res['changes_180_review']) : text($last_record['changes_180_review']); ?>
                                    <textarea name="changes_180_review"  rows="3" class="form-control"><?php echo $changes_180_review; ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for=""><?php echo xlt('270 Day Review:'); ?></label>
                                    <?php $changes_270_review = ($check_res['changes_270_review']) ? text($check_res['changes_270_review']) : text($last_record['changes_270_review']); ?>
                                    <textarea name="changes_270_review"  rows="3" class="form-control"><?php echo $changes_270_review; ?></textarea>
                                </div>

                            </div>

                            <!-- AFTERCARE PLAN FOR CLIENT -->
                            <div class="col-md-12" style="margin-top: 30px">
                                <div class="form-group">
                                    <label for=""><?php echo xlt('AFTERCARE PLAN FOR CLIENT: (What services or resources you anticipate being able to refer the participant to after the provision of Medicaid services is over; how will the participant maintain without Medicaid services or very minimal services.)'); ?></label>
                                    <?php $aftercare_plan = ($check_res['aftercare_plan']) ? text($check_res['aftercare_plan']) : text($last_record['aftercare_plan']); ?>
                                    <textarea name="aftercare_plan"  rows="3" class="form-control"><?php echo $aftercare_plan; ?></textarea>
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
                                <button type='submit'  class="btn btn-default btn-save" name="save_progress_notes"><?php echo xlt('Save'); ?></button>
                                <button type="button" class="btn btn-link btn-cancel oe-opt-btn-separate-left" onclick="form_close_tab()"><?php echo xlt('Cancel');?></button>
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
