<?php
require_once "../globals.php";
require_once "$srcdir/patient.inc";
require_once "$srcdir/options.inc.php";

if(isset($_POST['formid'])){
    $id = intval($_POST['formid']);

    //$checkRecord = sqlQuery("SELECT * FROM forms WHERE id = ?", array($id));

    //$dir = 'form_' . $checkRecord['formdir'];

    $confirmation = $_POST['confirmation'];
    $reason = strip_tags($_POST['reason']);

    if($confirmation == 'insert'){
        $date_requested = date('Y-m-d H:i:s');
        $new_query = "INSERT INTO forms_deletion (pc_eid, reason, date_requested) VALUES (?,?,?)";    
        $new_addl = sqlInsert($new_query, array($id, $reason, $date_requested));
        $request_deletion = 'insert';
        $message = "New record requested for deletion.";
    } elseif($confirmation == 'cancel') {
        $sql_query = "DELETE FROM forms_deletion WHERE pc_eid = ?";
        $delete_query = sqlQuery($sql_query, array($id));
        $message = "Request for deletion has been removed.";
        $request_deletion = 'cancel';
    } else {
        $request_deletion = 'none';
    }

    echo $request_deletion;
}