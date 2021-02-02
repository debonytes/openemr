<?php
/**
 * Progress Note form view.php
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Jacob T Paul <jacob@zhservices.com>
 * @copyright Copyright (c) 2015 Z&H Consultancy Services Private Limited <sam@zhservices.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

$folderName = 'counselor_comprehensive_assessment';
$formid = 0 + (isset($_GET['id']) ? $_GET['id'] : 0);
$table_id = $_SESSION['formID_' . $folderName .'_'. $formid];
if(!empty($table_id)){
    require("readonly.php");
} else {    
    require("new.php");
}
