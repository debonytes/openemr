<?php
/*
 * This program saves data from the comprehensive_assessment
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Terry Hill <terry@lilysystems.com>
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @author    Jerry Padgett <sjpadgett@gmail.com>
 * @copyright Copyright (C) 2007 Bo Huynh
 * @copyright Copyright (C) 2016 Terry Hill <terry@lillysystems.com>
 * @copyright Copyright (C) 2018 Jerry Padgett <sjpadgett@gmail.com>
 * @copyright Copyright (C) 2019 Brady Miller <brady.g.miller@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General P
 */


require_once("../../globals.php");
require_once("$srcdir/api.inc");
require_once("$srcdir/forms.inc");



use OpenEMR\Common\Csrf\CsrfUtils;

if (!CsrfUtils::verifyCsrfToken($_POST["csrf_token_form"])) {
  CsrfUtils::csrfNotVerified();
}

$encounter = ( isset($_SESSION['encounter']) && $_SESSION['encounter'] ) ? true:false; 

$folderName = 'counselor_comprehensive_assessment';
$tableName = 'form_' . $folderName;
$form_textual_name = 'Comprehensive Diagnostic Assessment';

$table_fields = sqlListFields($tableName); /* An array of the table fields */

if (!$encounter) { // comes from globals.php
  die(xlt("Internal error: we do not seem to be in an encounter!"));
}

$id = formData('id', 'G') + 0;
$sets = '';

$exclude_fields = array('id');
$fieldValues = array();
$encounter = ( isset($_REQUEST['encounter']) && $_REQUEST['encounter'] ) ? $_REQUEST['encounter'] : '';

$dateFields = array('dob','date','date_examine');

$implode_arr = array('recipient_report', 'referral_comm_services', 'appearance_weight', 'appearance_hygiene', 'speech_rate', 'speech_volume', 'motor_activity', 'eye_contact', 'cooperativeness', 'thought_process', 'thought_content', 'thought_perception', 'mood', 'affect', 'orientation', 'memory_immediate', 'memory_recent', 'memory_remote', 'insight', 'insight_awareness_symptoms', 'insight_awareness_need', 'dsm_5_code', 'dsm_5_code_disorder');

/* UPDATE FIELDS */
if ($id) {

		$i=0; foreach( $_REQUEST as $field=>$val ) {

			$val = ($val) ? $val : NULL;

			if( in_array($field, $table_fields) ) {
				
				if($val) {

					if( in_array($field, $dateFields) ) {
						$dateVal = DateTime::createFromFormat('m/d/Y', $val);
						$val = $dateVal->format('Y-m-d');
					}

					if( is_numeric($val) ) {
						if( $val < 1 ) {
							$val = 0;
						}
					}

					if(in_array($field, $implode_arr)){
            $val = implode('|', $val);
          } 
				}

				$comma = ($i>0) ? ', ':'';
				$sets .= $comma . $field . ' = ?';
				$fieldValues[] = $val;
				$i++;
			}
		}

	sqlStatement("UPDATE $tableName SET $sets WHERE id = $id",$fieldValues);


} else {
	/* INSERT FIELDS */

	if( isset($_REQUEST) && $_REQUEST ) {

		if( $encounter ) {

			$i=0; foreach( $_REQUEST as $field=>$val ) {
				$val = ($val) ? $val : NULL;

				if( in_array($field, $table_fields) ) {
					
					if($val) {

						if( in_array($field, $dateFields) ) {
							$dateVal = DateTime::createFromFormat('m/d/Y', $val);
							$val = $dateVal->format('Y-m-d');
						}
				
						if( is_numeric($val) ) {
							if( $val < 1 ) {
								$val = 0;
							}
						}

						if(in_array($field, $implode_arr)){
	            $val = implode('|', $val);
	          } 

					}
					$comma = ($i>0) ? ', ':'';
					$sets .= $comma . $field . ' = ?';
					$fieldValues[] = $val;
					$i++;
				}
			}

			$newid = sqlInsert( 
				"INSERT INTO $tableName SET $sets",$fieldValues
			);

			addForm($encounter, $form_textual_name, $newid, $folderName, $_SESSION["pid"], $userauthorized);

		}

	}
}

formHeader("Redirecting....");
formJump();
formFooter();

