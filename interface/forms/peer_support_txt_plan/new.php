<?php
/**
 * The data will be stored in peer_support_txt_plan form.
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
//require_once("date_qualifier_options.php");
require_once("$srcdir/options.inc.php");

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Core\Header;

$folderName = 'peer_support_txt_plan';
$tableName = 'form_' . $folderName;

$returnurl = 'encounter_top.php';
$formid = 0 + (isset($_GET['id']) ? $_GET['id'] : 0);
$check_res = $formid ? formFetch($tableName, $formid) : array();

$modalities = (!empty($check_res['modalities'])) ? explode('|', $check_res['modalities']) : array();
$responsible = (!empty($check_res['responsible'])) ? explode('|', $check_res['responsible']) : array();

?>
<html>
<head>
	<title><?php echo xlt("Peer Support Treatment Plan"); ?></title>
	<?php Header::setupHeader(['datetime-picker', 'opener']); ?>
	<link rel="stylesheet" href="<?php echo $web_root; ?>/library/css/bootstrap-timepicker.min.css">
	<link rel="stylesheet" href="../../../style_custom.css">
</head>
<body class="body_top">
	<div class="container">
		<div class="row">
			<div class="page-header">
				<h2><?php echo xlt('Peer Support Treatment Plan'); ?></h2>
			</div>
		</div>
		<?php
			$current_date = date('Y-m-d');

			$patient_id = ( isset($_SESSION['alert_notify_pid']) && $_SESSION['alert_notify_pid'] ) ? $_SESSION['alert_notify_pid'] : '';
			$pid = ( isset($_SESSION['pid']) && $_SESSION['pid'] ) ? $_SESSION['pid'] : 0;
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
			<form method="post" id="my_treatment_plan_form" class="frm-peer-support-treatment-plan" name="my_treatment_plan_form" >
				<input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
				<input type="hidden" name="pid" value="<?php echo $pid; ?>">
				<input type="hidden" name="encounter" value="<?php echo $encounter; ?>">
				<input type="hidden" name="user" value="<?php echo $_SESSION['authUser']; ?>">

				<fieldset style="padding:20px!important">
            <div class="col-12">
              <div class="form-group mod-radiobtn">
                <div class="row">
                  <div class="col-sm-3">
                    <div class="radio">
                      <label>
                        <input type="radio" id="review1" name="review" value="90 Day Review" <?php echo ($check_res['review'] == '90 Day Review') ? "checked": ""; ?> > 
                        <?php echo xlt('90 DAY REVIEW'); ?> 
                      </label>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="radio">
                      <label>
                        <input type="radio" id="review2" name="review" value="180 Day Review" <?php echo ($check_res['review'] == '180 Day Review') ? "checked": ""; ?> > 
                        <?php echo xlt('180 DAY REVIEW'); ?> 
                      </label>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="radio">
                      <label>
                        <input type="radio" id="review3" name="review" value="270 Day Review" <?php echo ($check_res['review'] == '270 Day Review') ? "checked": ""; ?> > 
                        <?php echo xlt('270 DAY REVIEW'); ?> 
                      </label>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="radio">
                      <label>
                        <input type="radio" id="review4" name="review" value="Other Review" <?php echo ($check_res['review'] == 'Other Review') ? "checked": ""; ?> > 
                        <?php echo xlt('OTHER REVIEW'); ?> 
                      </label>
                    </div>
                  </div>
                </div>
                <small class="text-danger review_error"></small>
              </div>

              <div class="form-group">
                <div class="row">
                  <div class="col-sm-5">
                    <label for="participant_name"><?php echo xlt('Participant Name:'); ?></label>
                    <input type="text" class="form-control" id="participant_name" name="participant_name" value="<?php echo text($patient_full_name); ?>">
                    <small class="text-danger participant_name_error"></small>
                  </div>
                  <div class="col-sm-5">
                    <label for="medica_id"><?php echo xlt('Medicaid #:'); ?></label>
                    <input type="text" class="form-control" id="medica_id" name="medica_id" value="<?php echo text($check_res['medica_id']); ?>">
                    <small class="text-danger medica_id_error"></small>
                  </div>
                  <div class="col-sm-2">
                    <label for="diagnosis_codes"><?php echo xlt('Diagnosis Codes(s):'); ?></label>
                    <input type="text" class="form-control" id="diagnosis_codes" name="diagnosis_codes" value="<?php echo text($check_res['diagnosis_codes']); ?>">
                    <small class="text-danger diagnosis_codes_error"></small>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="row">
                  <div class="col-sm-4">
                    <label for="type_of_service"><?php echo xlt('Type of Service:'); ?></label>
                    <input type="text" class="form-control" id="type_of_service" name="type_of_service" value="<?php echo text($check_res['type_of_service']); ?>">
                  </div>
                  <div class="col-sm-4">
                    <label for="examiner"><?php echo xlt('Examiner:'); ?></label>
                    <input type="text" class="form-control" id="examiner" name="examiner" value="<?php echo text($check_res['examiner']); ?>">
                    <small class="text-danger examiner_error"></small>
                  </div>
                  <div class="col-sm-2">
                    <label for="date"><?php echo xlt('Date:'); ?></label>
                    <input type="text" class="form-control datepicker" id="date" name="date" value="<?php echo text($check_res['date']); ?>">
                    <small class="text-danger date_error"></small>
                  </div>
                  <div class="col-sm-2">
                    <label for="dob"><?php echo xlt('DOB:'); ?></label>
                    <input type="text" class="form-control" id="dob" name="dob" value="<?php echo text($patient_DOB); ?>">
                    <small class="text-danger dob_error"></small>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="reasons_seeking_peer_support"><?php echo xlt('Reason for Seeking Peer Support (Clients own Words):'); ?></label>
                <textarea id="reasons_seeking_peer_support" class="form-control" rows="3" name="reasons_seeking_peer_support"><?php echo text($check_res['reasons_seeking_peer_support']); ?></textarea>
              </div>

              <hr/>

              <h4><?php echo xlt('Goals Section'); ?></h4>
              <div class="form-group">
                <label for="goal1_client_stmt"><?php echo xlt('Client’s statement of overall goal or need (Must be in client’s own words):'); ?></label>
                <textarea id="goal1_client_stmt" class="form-control" rows="3" name="goal1_client_stmt"><?php echo text($check_res['goal1_client_stmt']); ?></textarea>
              </div>

              <div class="form-group">
                <label for="goal1"><?php echo xlt('Goal #1 (Must be in client’s own words):'); ?></label>
                <textarea id="goal1" class="form-control" rows="3" name="goal1"><?php echo text($check_res['goal1']); ?></textarea>
              </div>

              <h4><?php echo xlt('OBJECTIVES'); ?></h4>
              <p><?php echo xlt('Objectives must address the emotional, behavioral, and skill training needs identified by the member'); ?></p>

              <h5><?php echo xlt('(Objective 1.1)'); ?></h5>
              <div class="form-group">
                <label for="obj1_steps"><?php echo xlt('What are the steps that the member is going to take to achieve this objective:'); ?></label>
                <textarea id="obj1_steps" class="form-control" rows="3" name="obj1_steps"><?php echo text($check_res['obj1_steps']); ?></textarea>
              </div>
              <div class="form-group">
                <label for="obj1_intervention"><?php echo xlt('What interventions will the Peer Support Specialist provide to help them achieve this Objective:'); ?></label>
                <textarea id="obj1_intervention" class="form-control" rows="3" name="obj1_intervention"><?php echo text($check_res['obj1_intervention']); ?></textarea>
              </div>
              <div class="row">
                <div class="col-sm-4">
                  <label for="obj1_freequency"><?php echo xlt('Frequency:'); ?></label>
                  <input type="text" class="form-control" id="obj1_freequency" name="obj1_freequency" value="<?php echo text($check_res['obj1_freequency']); ?>">
                </div>
                <div class="col-sm-4">
                  <label for="obj1_duration"><?php echo xlt('Duration:'); ?></label>
                  <input type="text" class="form-control" id="obj1_duration" name="obj1_duration" value="<?php echo text($check_res['obj1_duration']); ?>">
                </div>
                <div class="col-sm-4">
                  <label for="obj1_place"><?php echo xlt('Place'); ?>:</label>
                  <input type="text" class="form-control" id="obj1_place" name="obj1_place" value="<?php echo text($check_res['obj1_place']); ?>">
                </div>
              </div>

              <h5><?php echo xlt('(Objective 1.2)'); ?></h5>
              <div class="form-group">
                <label for="obj1_2_steps"><?php echo xlt('What are the steps that the member is going to take to achieve this objective:'); ?></label>
                <textarea id="obj1_2_steps" class="form-control" rows="3" name="obj1_2_steps"><?php echo text($check_res['obj1_2_steps']); ?></textarea>
              </div>
              <div class="form-group">
                <label for="obj1_2_intervention"><?php echo xlt('What interventions will the Peer Support Specialist provide to help them achieve this Objective:'); ?></label>
                <textarea id="obj1_2_intervention" class="form-control" rows="3" name="obj1_2_intervention"><?php echo text($check_res['obj1_2_intervention']); ?></textarea>
              </div>
              <div class="row">
                <div class="col-sm-4">
                  <label for="obj1_2_freequency"><?php echo xlt('Frequency:'); ?></label>
                  <input type="text" class="form-control" id="obj1_2_freequency" name="obj1_2_freequency" value="<?php echo text($check_res['obj1_2_freequency']); ?>">
                </div>
                <div class="col-sm-4">
                  <label for="obj1_2_duration"><?php echo xlt('Duration:'); ?></label>
                  <input type="text" class="form-control" id="obj1_2_duration" name="obj1_2_duration" value="<?php echo text($check_res['obj1_2_duration']); ?>">
                </div>
                <div class="col-sm-4">
                  <label for="obj1_2_place"><?php echo xlt('Place:'); ?></label>
                  <input type="text" class="form-control" id="obj1_2_place" name="obj1_2_place" value="<?php echo text($check_res['obj1_2_place']); ?>">
                </div>
              </div>

              <hr/>

              <div class="form-group">
                <label for="goal2_client_stmt"><?php echo xlt('Client’s statement of overall goal or need (Must be in client’s own words):'); ?></label>
                <textarea id="goal2_client_stmt" class="form-control" rows="3" name="goal2_client_stmt"><?php echo text($check_res['goal2_client_stmt']); ?></textarea>
              </div>
              <div class="form-group">
                <label for="goal2"><?php echo xlt('Goal #2 (Must be in client’s own words):'); ?></label>
                <textarea id="goal2" class="form-control" rows="3" name="goal2"><?php echo text($check_res['goal2']); ?></textarea>
              </div>

              <h4><?php echo xlt('OBJECTIVES'); ?></h4>
              <p><?php echo xlt('Objectives must address the emotional, behavioral, and skill training needs identified by the member'); ?></p>

              <h5><?php echo xlt('(Objective 2.1'); ?>)</h5>
              <div class="form-group">
                <label for="obj2_steps"><?php echo xlt('What are the steps that the member is going to take to achieve this objective:'); ?></label>
                <textarea id="obj2_steps" class="form-control" rows="3" name="obj2_steps"><?php echo text($check_res['obj2_steps']); ?></textarea>
              </div>
              <div class="form-group">
                <label for="obj2_intervention"><?php echo xlt('What interventions will the Peer Support Specialist provide to help them achieve this Objective:'); ?></label>
                <textarea id="obj2_intervention" class="form-control" rows="3" name="obj2_intervention"><?php echo text($check_res['obj2_intervention']); ?></textarea>
              </div>
              <div class="row">
                <div class="col-sm-4">
                  <label for="obj2_freequency"><?php echo xlt('Frequency:'); ?></label>
                  <input type="text" class="form-control" id="obj2_freequency" name="obj2_freequency" value="<?php echo text($check_res['obj2_freequency']); ?>">
                </div>
                <div class="col-sm-4">
                  <label for="obj2_duration"><?php echo xlt('Duration:'); ?></label>
                  <input type="text" class="form-control" id="obj2_duration" name="obj2_duration" value="<?php echo text($check_res['obj2_duration']); ?>">
                </div>
                <div class="col-sm-4">
                  <label for="obj2_place"><?php echo xlt('Place:'); ?></label>
                  <input type="text" class="form-control" id="obj2_place" name="obj2_place" value="<?php echo text($check_res['obj2_place']); ?>">
                </div>
              </div>

    	        <h5><?php echo xlt('(Objective 2.2)'); ?></h5>
    	        <div class="form-group">
    	          <label for="obj2_2_steps"><?php echo xlt('What are the steps that the member is going to take to achieve this objective:'); ?></label>
    	          <textarea id="obj2_2_steps" class="form-control" rows="3" name="obj2_2_steps"><?php echo text($check_res['obj2_2_steps']); ?> </textarea>
    	        </div>
    	        <div class="form-group">
    	          <label for="obj2_2_intervention"><?php echo xlt('What interventions will the Peer Support Specialist provide to help them achieve this Objective:'); ?></label>
    	          <textarea id="obj2_2_intervention" class="form-control" rows="3" name="obj2_2_intervention"><?php echo text($check_res['obj2_2_intervention']); ?></textarea>
    	        </div>
    	        <div class="row">
    	          <div class="col-sm-4">
    	            <label for="obj2_2_freequency"><?php echo xlt('Frequency:'); ?></label>
    	            <input type="text" class="form-control" id="obj2_2_freequency" name="obj2_2_freequency" value="<?php echo text($check_res['obj2_2_freequency']); ?>">
    	          </div>
    	          <div class="col-sm-4">
    	            <label for="obj2_2_duration"><?php echo xlt('Duration:'); ?></label>
    	            <input type="text" class="form-control" id="obj2_2_duration" name="obj2_2_duration" value="<?php echo text($check_res['obj2_2_duration']); ?>">
    	          </div>
    	          <div class="col-sm-4">
    	            <label for="obj2_2_place"><?php echo xlt('Place:'); ?></label>
    	            <input type="text" class="form-control" id="obj2_2_place" name="obj2_2_place" value="<?php echo text($check_res['obj2_2_place']); ?>">
    	          </div>
    	        </div>
    	        
    	        <h5><?php echo xlt('(Objective 2.3)'); ?></h5>
    	        <div class="form-group">
    	          <label for="obj2_3_steps"><?php echo xlt('What are the steps that the member is going to take to achieve this objective:'); ?></label>
    	          <textarea id="obj2_3_steps" class="form-control" rows="3" name="obj2_3_steps"><?php echo text($check_res['obj2_3_steps']); ?></textarea>
    	        </div>
    	        <div class="form-group">
    	          <label for="obj2_3_intervention"><?php echo xlt('What interventions will the Peer Support Specialist provide to help them achieve this Objective:'); ?></label>
    	          <textarea id="obj2_3_intervention" class="form-control" rows="3" name="obj2_3_intervention"><?php echo text($check_res['obj2_3_intervention']); ?></textarea>
    	        </div>
    	        <div class="row">
    	          <div class="col-sm-4">
    	            <label for="obj2_3_freequency"><?php echo xlt('Frequency'); ?>:</label>
    	            <input type="text" class="form-control" id="obj2_3_freequency" name="obj2_3_freequency" value="<?php echo text($check_res['obj2_3_freequency']); ?>">
    	          </div>
    	          <div class="col-sm-4">
    	            <label for="obj2_3_duration"><?php echo xlt('Duration:'); ?></label>
    	            <input type="text" class="form-control" id="obj2_3_duration" name="obj2_3_duration" value="<?php echo text($check_res['obj2_3_duration']); ?>">
    	          </div>
    	          <div class="col-sm-4">
    	            <label for="obj2_3_place"><?php echo xlt('Place:'); ?></label>
    	            <input type="text" class="form-control" id="obj2_3_place" name="obj2_3_place" value="<?php echo text($check_res['obj2_3_place']); ?>">
    	          </div>
    	        </div>

    	        <hr/>

              <div class="form-group">
                <label for="short_term_goal_client"><?php echo xlt('Short-term goal for Client:'); ?></label>
                <textarea id="short_term_goal_client" class="form-control" rows="3" name="short_term_goal_client"><?php echo text($check_res['short_term_goal_client']); ?></textarea>
              </div>
              <div class="form-group">
                <label for="short_term_goal"><?php echo xlt('Short-term Goal (Must be in client’s own words):'); ?></label>
                <textarea id="short_term_goal" class="form-control" rows="3" name="short_term_goal"><?php echo text($check_res['short_term_goal']); ?></textarea>
              </div>

              <h5><?php echo xlt('(Objective S.1)'); ?></h5>
              <div class="form-group">
                <label for="stg_obj_steps"><?php echo xlt('What are the steps that the member is going to take to achieve this objective:'); ?></label>
                <textarea id="stg_obj_steps" class="form-control" rows="3" name="stg_obj_steps"><?php echo text($check_res['stg_obj_steps']); ?></textarea>
              </div>
              <div class="form-group">
                <label for="stg_obj_intervention"><?php echo xlt('What interventions will the Peer Support Specialist provide to help them achieve this Objective:'); ?></label>
                <textarea id="stg_obj_intervention" class="form-control" rows="3" name="stg_obj_intervention"><?php echo text($check_res['stg_obj_intervention']); ?></textarea>
              </div>

              <div class="form-group">
                <div class="row">
                  <div class="col-sm-4">
                    <label for="stg_obj_frequency"><?php echo xlt('Frequency:'); ?></label>
                    <input type="text" class="form-control" id="stg_obj_frequency" name="stg_obj_frequency" value="<?php echo text($check_res['stg_obj_frequency']); ?>">
                  </div>
                  <div class="col-sm-4">
                    <label for="stg_obj_duration"><?php echo xlt('Duration'); ?>:</label>
                    <input type="text" class="form-control" id="stg_obj_duration" name="stg_obj_duration" value="<?php echo text($check_res['stg_obj_duration']); ?>">
                  </div>
                  <div class="col-sm-4">
                    <label for="stg_obj_place"><?php echo xlt('Place'); ?>:</label>
                    <input type="text" class="form-control" id="stg_obj_place" name="stg_obj_place" value="<?php echo text($check_res['stg_obj_place']); ?>">
                  </div>
                </div>
              </div>

              <div class="form-group">
                <p><?php echo xlt('Modalities to be Used to Achieve Objectives'); ?></p>
                <div class="mod-checkbox">
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" id="modalities1" name="modalities[]" value="Individual Therapy" <?php (in_array('Individual Therapy', $modalities)) ? 'checked' : '' ?>> 
                      <?php echo xlt('Individual Therapy'); ?> 
                    </label>
                  </div>
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" id="modalities2" name="modalities[]" value="Family Therapy" <?php (in_array('Family Therapy', $modalities)) ? 'checked' : '' ?>> 
                      <?php echo xlt('Family Therapy'); ?>
                    </label>
                  </div>
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" id="modalities3" name="modalities[]" value="Family w/o client" <?php (in_array('Family w/o client', $modalities)) ? 'checked' : '' ?>> 
                      <?php echo xlt('Family w/o client'); ?>
                    </label>
                  </div>
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" id="modalities4" name="modalities[]" value="Group" <?php (in_array('Group', $modalities)) ? 'checked' : '' ?>> 
                      <?php echo xlt('Group'); ?>
                    </label>
                  </div>
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" id="modalities5" name="modalities[]" value="CBRS Skills Training" <?php (in_array('CBRS Skills Training', $modalities)) ? 'checked' : '' ?>> 
                      <?php echo xlt('CBRS Skills Training'); ?> 
                    </label>
                  </div>
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" id="modalities6" name="modalities[]" value="Peer Support Services" <?php (in_array('Peer Support Services', $modalities)) ? 'checked' : '' ?>> 
                      <?php echo xlt('Peer Support Services'); ?> 
                    </label>
                  </div>
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" id="modalities7" name="modalities[]" value="Service Coordination" <?php (in_array('Service Coordination', $modalities)) ? 'checked' : '' ?>>
                      <?php echo xlt('Service Coordination'); ?>
                    </label>
                  </div>
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" id="modalities10" name="modalities[]" value="Medication Management" <?php (in_array('Medication Management', $modalities)) ? 'checked' : '' ?>>
                      <?php echo xlt('Medication Management'); ?>
                    </label>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-sm-6">
                  <label for="target_date"><?php echo xlt('Target Date for Attainment of Goals:'); ?></label>
                  <input type="text" class="form-control" id="target_date" name="target_date" value="<?php echo text($check_res['target_date']); ?>">
                </div>
                <div class="col-sm-6">
                  <label><?php echo xlt(''); ?>Who is responsible:</label>
                  <div class="mod-checkbox">
                    <div class="checkbox">
                      <label>
                        <input type="checkbox" id="responsible" name="responsible[]" value="Client" <?php (in_array('Client', $responsible)) ? 'checked' : '' ?> >
                        <?php echo xlt('Client'); ?>
                      </label>
                    </div>
                    <div class="checkbox">
                      <label>
                        <input type="checkbox" id="responsible2" name="responsible[]" value="Peer Support Provider" <?php (in_array('Peer Support Provider', $responsible)) ? 'checked' : '' ?> >
                        <?php echo xlt('Peer Support Provider'); ?>
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="individuals_included_plan_rev"><?php echo xlt('Individuals included in initial treatment planning or review:'); ?></label>
                <textarea id="individuals_included_plan_rev" class="form-control" rows="3" name="individuals_included_plan_rev"><?php echo text($check_res['individuals_included_plan_rev']); ?></textarea>
              </div>

              <div class="form-group">
                <label for="svcs_coordinated"><?php echo xlt('How will services be coordinated with those delivered by other providers and agencies?'); ?></label>
                <textarea id="svcs_coordinated" class="form-control" rows="3" name="svcs_coordinated"><?php echo text($check_res['svcs_coordinated']); ?></textarea>
              </div>

              <div class="form-group">
                <label for="linkgs_peer_support"><?php echo xlt('Linkages with peer support services and other community resources:'); ?></label>
                <textarea id="linkgs_peer_support" class="form-control" rows="3" name="linkgs_peer_support"><?php echo text($check_res['linkgs_peer_support']); ?></textarea>
              </div>

              <h5><?php echo xlt('FOR REVIEWS ONLY'); ?></h5>
              <div class="form-group">
                <label for="for_rev_progress"><?php echo xlt('Progress toward the Objectives'); ?>:</label>
                <textarea id="for_rev_progress" class="form-control" rows="3" name="for_rev_progress"><?php echo text($check_res['for_rev_progress']); ?></textarea>
              </div>
              <div class="form-group">
                <label for="for_rev_changes"><?php echo xlt('Changes, amendments or deletions to Objectives:'); ?></label>
                <textarea id="for_rev_changes" class="form-control" rows="3" name="for_rev_changes"><?php echo text($check_res['for_rev_changes']); ?></textarea>
              </div>
              <div class="form-group">
                <label for="for_rev_obj"><?php echo xlt('New Objective:'); ?></label>
                <textarea id="for_rev_obj" class="form-control" rows="3" name="for_rev_obj"><?php echo text($check_res['for_rev_obj']); ?></textarea>
              </div>

              <div class="form-group">
                <div class="row">
                  <div class="col-sm-3">
                    <label for="for_rev_modality"><?php echo xlt('Task Modality:'); ?></label>
                    <input type="text" class="form-control" id="for_rev_modality" name="for_rev_modality" value="<?php echo text($check_res['for_rev_modality']); ?>">
                  </div>
                  <div class="col-sm-3">
                    <label for="for_rev_frequency"><?php echo xlt('Frequency:'); ?></label>
                    <input type="text" class="form-control" id="for_rev_frequency" name="for_rev_frequency" value="<?php echo text($check_res['for_rev_frequency']); ?>">
                  </div>
                  <div class="col-sm-3">
                    <label for="for_rev_duraction"><?php echo xlt('Duration:'); ?></label>
                    <input type="text" class="form-control" id="for_rev_duraction" name="for_rev_duraction" value="<?php echo text($check_res['for_rev_duraction']); ?>">
                  </div>
                  <div class="col-sm-3">
                    <label for="for_rev_place"><?php echo xlt('Place:'); ?></label>
                    <input type="text" class="form-control" id="for_rev_place" name="for_rev_place" value="<?php echo text($check_res['for_rev_place']); ?>">
                  </div>
                </div>
              </div>

            </div>
        </fieldset>

				<div class="form-group clearfix">
					<div class="col-sm-12 col-sm-offset-1 position-override">
						<div class="btn-group oe-opt-btn-group-pinch" role="group">
							<button type='submit'  class="btn btn-default btn-save" name="save_treatment_plan"><?php echo xlt('Save'); ?></button>
							<button 
								type="button" 
								class="btn btn-link btn-cancel oe-opt-btn-separate-left" 
								onclick="top.restoreSession(); parent.closeTab(window.name, false);">
								<?php echo xlt('Cancel');?>		
							</button>
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

			$('.btn-save').on('click', function(e){
					e.preventDefault();

					var errors = false;

					// var review1 = $('#review1').prop('checked');
					// var review2 = $('#review2').prop('checked');
					// var review3 = $('#review3').prop('checked');
					// var review4 = $('#review4').prop('checked');
					// if((review1 == '') && (review2 == '') && (review3 == '') && (review4 == '')){
					// 	$('.review_error').text('Please enter your Review.');
					// 	errors = true;
					// } else {
					// 	$('.review_error').text('');
					// 	errors = false;
					// }

					// var participant_name = $('#participant_name').val();
					// if(participant_name == ''){
					// 	$('.participant_name_error').text('Please enter Participant Name.');   
					// 	errors = true;                     
					// } else {
					// 	$('.participant_name_error').text('');
					// 	errors = false;
					// }

					// var medical_id = $('#medica_id').val();
					// if(medical_id == ''){
					// 	$('.medica_id_error').text('Enter Medical ID.');  
					// 	errors = true;                      
					// } else {
					// 	$('.medica_id_error').text('');
					// 	errors = false;
					// }

					// var date_birth = $('#dob').val();
					// if(date_birth == ''){
					// 	$('.dob_error').text('Enter date of birth.');      
					// 	errors = true;                  
					// } else {
					// 	$('.dob_error').text('');
					// 	errors = false;
					// }
					
					// var examiner = $('#examiner').val();
					// if(examiner == ''){
					// 	$('.examiner_error').text('Please enter examiner name.');    
					// 	errors = true;                    
					// } else {
					// 	$('.examiner_error').text('');
					// 	errors = false;
					// }

					// var date = $('#date').val();
					// if(date == ''){
					// 	$('.date_error').text('Please enter date.');    
					// 	errors = true;                    
					// } else {
					// 	$('.date_error').text('');
					// 	errors = false;
					// }

					// if(errors){
					// 	return;
					// }                    

					top.restoreSession();

					$.ajax({
						url: "<?php echo $rootdir; ?>/forms/<?php echo $folderName; ?>/save.php?id=<?php echo attr_url($formid); ?>",
						type: 'POST',
						data: $('form#my_treatment_plan_form').serialize(),
						success: function(response){
							//console.log(response);
							//window.location.reload();
							window.location.href = "<?php echo $rootdir; ?>/forms/<?php echo $folderName; ?>/redirect.php";
						},
						errors: function(response){
							//console.log(response);
						}
					});

			});
		});
	</script>
</body>
</html>
