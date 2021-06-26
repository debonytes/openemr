<?php
require_once "../globals.php";
require_once "$srcdir/patient.inc";
require_once "$srcdir/options.inc.php";

if(empty($_POST['pc_eid'])){
    return;
}

if(isset($_POST['pc_eid'])){
    $request_deletion = '';
    $id = intval($_POST['pc_eid']);     
       
    $new_query = "SELECT reason FROM forms_deletion WHERE pc_eid = ?";    
    $row = sqlQuery($new_query, array($id));
    if( !empty($row) ){
        $request_deletion = $row['reason'];
    }

    echo $request_deletion;
}