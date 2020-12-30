<?php
session_start();
/**
 * CBRS Progress Notes form view.php
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Jacob T Paul <jacob@zhservices.com>
 * @copyright Copyright (c) 2015 Z&H Consultancy Services Private Limited <sam@zhservices.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */
$formid = 0 + (isset($_GET['id']) ? $_GET['id'] : 0);
if(isset($_SESSION['formID_' . $formid ])){
    require("readonly.php");
} else {
    require("new.php");
}


