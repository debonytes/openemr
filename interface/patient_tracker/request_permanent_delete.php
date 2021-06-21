<?php
require_once "../globals.php";
require_once "$srcdir/patient.inc";
require_once "$srcdir/options.inc.php";

$adminuser = 'superjimgrigg';

if($_SESSION['authUser'] !== $adminuser){
    return;
}

if(isset($_POST['id'])){
    $id = intval($_POST['id']);

    $checkRecord = sqlQuery("SELECT * FROM forms WHERE id = ?", array($id));
    if($checkRecord){
        $dir_table  = 'form_' . $checkRecord['formdir'];
        $encounter  = $checkRecord['encounter'];
        $formid     = $checkRecord['form_id'];

        // deleting record as per form table
        $sql_query = "DELETE FROM {$dir_table} WHERE id = ?";
        $delete_query = sqlQuery($sql_query, array($formid));

        // deleting encounter
        $sql_query = "DELETE FROM form_encounter WHERE encounter = ?";
        $delete_query = sqlQuery($sql_query, array($encounter));

        // deleting record in form list
        $sql_query = "DELETE FROM forms WHERE id = ?";
        $delete_query = sqlQuery($sql_query, array($id));

        // deleting record in forms_deletion
        $sql_query = "DELETE FROM forms_deletion WHERE pc_eid = ?";
        $delete_query = sqlQuery($sql_query, array($id));

        echo 'deleted';
    }
    
}