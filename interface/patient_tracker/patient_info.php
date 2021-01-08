<?php
//session_start();
require_once "../globals.php";
require_once("$srcdir/pid.inc");
require_once("$srcdir/encounter.inc");
require_once("$srcdir/forms.inc");

use OpenEMR\Tabs\TabsWrapper;
use OpenEMR\Core\Header;

if (isset($_GET["set_encounter"])) {
    // The billing page might also be setting a new pid.
    if (isset($_GET["set_pid"])) {
        $set_pid=$_GET["set_pid"];
    } else if (isset($_GET["pid"])) {
        $set_pid=$_GET["pid"];
    } else {
        $set_pid=false;
    }

    if ($set_pid && $set_pid != $_SESSION["pid"]) {
        setpid($set_pid);
    }

    setencounter($_GET["set_encounter"]);

    $_SESSION['from_dashboard'] = true;
}

/*
$tabset = new TabsWrapper('enctabs');
$tabset->declareInitialTab(
    xl('Summary'),
    "<iframe frameborder='0' style='height:95.3%;width:100%;' src='forms.php'>Oops</iframe>"
);
// We might have been invoked to load a particular encounter form.
// In that case it will be the second tab, and removable.
if (!empty($_GET['formname'])) {
    $url = $rootdir . "/patient_file/encounter/load_form.php?formname=" . attr_url($_GET['formname']);
    $tabset->declareInitialTab(
        $_GET['formdesc'],
        "<iframe name='enctabs-2' frameborder='0' style='height:95.3%;width:100%;' src='$url'>Oops</iframe>",
        true
    );
}

// This is for making the page title which will be picked up as the tab label.
$dateres = getEncounterDateByEncounter($encounter);
$encounter_date = date("Y-m-d", strtotime($dateres["date"]));
*/

?>