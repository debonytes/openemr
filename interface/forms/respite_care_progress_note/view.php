<?php
/*
 * view.php for the viewing of information from the respite_care_progress_note
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Bo Huynh
 * @author    Terry Hill <terry@lilysystems.com>
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @author    Stephen Waite <stephen.waite@cmsvt.com>
 * @copyright Copyright (C) 2007 Bo Huynh
 * @copyright Copyright (C) 2016 Terry Hill <terry@lillysystems.com>
 * @copyright Copyright (C) 2017 Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (C) 2017 Stephen Waite <stephen.waite@cmsvt.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */
$folderName = 'respite_care_progress_note';
$formid = 0 + (isset($_GET['id']) ? $_GET['id'] : 0);
$table_id = $_SESSION['formID_' . $folderName .'_'. $formid];
if(!empty($table_id)){
    require("readonly.php");
} else {    
    require("new.php");
}

