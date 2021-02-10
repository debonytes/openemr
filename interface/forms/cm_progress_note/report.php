<?php
/**
 * Clinical instructions form report.php
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Jacob T Paul <jacob@zhservices.com>
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2015 Z&H Consultancy Services Private Limited <sam@zhservices.com>
 * @copyright Copyright (c) 2019 Brady Miller <brady.g.miller@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */


require_once(dirname(__FILE__).'/../../globals.php');
require_once($GLOBALS["srcdir"]."/api.inc");
require_once("$srcdir/patient.inc");

function cm_progress_note_report($pid, $encounter, $cols, $id)
{
    $count = 0;
    $patient_full_name = '';
    $data = formFetch("form_cm_progress_note", $id);
    if ($data) {

        $pid = ( $data['pid'] ) ? $data['pid'] : 0;
        if($pid) {
          $patient = getPatientData($pid);
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
        ?>
        <table style='border-collapse:collapse;border-spacing:0;width: 100%;'>
            <tr>
                <td align='center' style='border:1px solid #ccc;padding:4px;'><span class=bold><?php echo xlt('Name'); ?></span></td>
                <td align='center' style='border:1px solid #ccc;padding:4px;'><span class=bold><?php echo xlt('Billing Code'); ?></span></td>
            </tr>
            <tr>
                <td style='border:1px solid #ccc;padding:4px;'><span class=text><?php echo nl2br(text($patient_full_name)); ?></span></td>
                <td style='border:1px solid #ccc;padding:4px;'><span class=text><?php echo nl2br(text($data['billing_code'])); ?></span></td>
            </tr>
        </table>
        <?php
    }
}
?>