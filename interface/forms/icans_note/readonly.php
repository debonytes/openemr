<?php
/**
 * ICANS Note form.
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
require_once("$srcdir/encounter.inc");
require_once("$srcdir/group.inc");
require_once("$srcdir/api.inc");
require_once("$srcdir/acl.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/options.inc.php");
require_once $GLOBALS['srcdir'].'/ESign/Api.php';

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Core\Header;
use ESign\Api;

$folderName = 'icans_note';
$tableName = 'form_' . $folderName;


$returnurl = 'encounter_top.php';
$formid = 0 + (isset($_GET['id']) ? $_GET['id'] : 0);
$check_res = $formid ? formFetch($tableName, $formid) : array();




?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo xlt("ICANS Note"); ?></title>

        <?php Header::setupHeader(['datetime-picker', 'opener', 'esign']); ?>
        <link rel="stylesheet" href="<?php echo $web_root; ?>/library/css/bootstrap-timepicker.min.css">
        <style>
            .margin-left-40{
                margin-left: 300px;
            }
            .margin-right-40{
                margin-right: 40px;
            }
            @media print{
                .margin-left-40{
                    margin-left: 40px;
                }
                .margin-right-40{
                    margin-right: 20px;
                }
                .col-md-2 {
                    width: 16.66666667%;
                }
                .col-md-10 {
                    width: 83.33333333%;
                }
                .col-md-6 {
                    width: 50%;
                }
                .col-md-4 {
                    width: 33.3333%;
                }
                .col-md-3 {
                    width: 25%;
                }
                .col-md-8 {
                    width: 66.66666667%;
                }
                .col-md-9 {
                    width: 75%;
                }
                .col-md-12 {
                    width: 100%;
                }
                .form-group {
                    margin-bottom: 5px!important;
                }
                label {
                    padding: 0 5px!important;
                }
                label {
                    display: inline-block;
                    max-width: 100%;
                    margin-bottom: 5px;
                    font-weight: normal;
                }
                .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9 {
                    float: left;
                }
                .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9 {
                    float: left;
                }
                .row_other{
                    margin-top: 40px;
                }
                h3{
                    font-size: 20px;
                }
                .brief_mental_status{
                    margin-top: 40px;
                    padding-top: 20px;
                }
                .treatment_diagnostic_row{
                    margin-top: 30px;
                }
                input[type=text]{
                    border: none;
                    font-weight: bold;
                    border-bottom: 1px solid #333;
                    border-bottom-right-radius: 0;
                    border-bottom-left-radius: 0;
                }
                .form-control{
                    border: none;
                    font-size: 16px;
                    font-weight: bold;
                    background: none;
                }
                textarea.form-control{
                    border: 1px solid #333;
                }
                .session-focus{
                    margin-top: 100px;
                    padding-top: 40px;
                }
                .col-md-offset-2 {
                    margin-left: 0;
                }
                .checkbox-inline, .radio-inline {
                    position: relative;
                    display: inline-block;
                    padding-left: 20px;
                    margin-bottom: 0;
                    font-weight: 400;
                    vertical-align: middle;
                    cursor: pointer;
                }

                .full-width{
                    width: 100%;
                    margin: 0;
                }

            }
            @page {
              margin: 2cm;
            }

           
        </style>
    </head>
    <body class="body_top">
        <div class="container">
            <div class="row">
                <div class="page-header">
                    <h2><?php echo xlt('ICANS Note'); ?></h2>
                </div>
            </div>
            <?php
            $current_date = date('Y-m-d');

            if( $_SESSION['from_dashboard'] ){
                $patient_full_name = ($check_res['name']) ? $check_res['name'] : '';
            } else {

                $patient_id = ( $_SESSION['alert_notify_pid'] ) ? $_SESSION['alert_notify_pid'] : '';
                $pid = ( $_SESSION['pid'] ) ? $_SESSION['pid'] : 0;
                if($patient_id) {
                  $patient = getPatientData($patient_id);
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

            }



           
            ?>
            <div class="row">
                
                <form method="post" id="my_progress_notes_form" name="my_progress_notes_form" action="<?php echo $rootdir; ?>/forms/<?php echo $folderName; ?>/save.php?id=<?php echo attr_url($formid); ?>">
            

                
                    <input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
                    <input type="hidden" name="pid" value="<?php echo $pid; ?>">
                    <input type="hidden" name="encounter" value="<?php echo $encounter; ?>">
                    <input type="hidden" name="user" value="<?php echo $_SESSION['authUser']; ?>">
                    <input type="hidden" name="authorized" value="<?php echo $userauthorized; ?>">
                    <input type="hidden" name="activity" value="1">

                    <fieldset style="padding-top: 20px!important;" class="form_content">
                       
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="col-md-5 "><?php echo xlt('Client Name'); ?></label>
                                    <div class="col-md-6">
                                        <input type="text"  id="name" class="form-control" value="<?php echo text($patient_full_name); ?>" readonly disabled>
                                        <input type="hidden" name="name" value="<?php echo text($patient_full_name); ?>" >
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="examiner" class="col-md-5 "><?php echo xlt('Examiner'); ?></label>
                                    <div class="col-md-6">
                                        <?php $counselor = get_provider_details($check_res['examiner']); ?>
                                        <input type="text" name="examiner" id="examiner" class="form-control" value="<?php echo text($counselor['lname']) . ', ' . text($counselor['fname']) ; ?>" disabled>
                                        <small class="text-danger cbrs_error"></small>
                                    </div>   
                                    <div class="clearfix"></div>                                 
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="billing_code" class="col-md-5 "><?php echo xlt('Billing Code'); ?></label>
                                    <div class="col-md-6">
                                        <input type="text" name="billing_code" id="billing_code"  class="form-control" value="<?php echo ($check_res['billing_code']) ? text($check_res['billing_code']) : 'H0031'; ?>" disabled>
                                    </div>   
                                    <div class="clearfix"></div>                                 
                                </div>
                                
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="col-md-5"><?php echo xlt('Date of Service'); ?></label>
                                    <div class="col-md-6">
                                        <input type="text" name="dateofservice" id="dateofservice" class="form-control datepicker" value="<?php echo ($check_res['dateofservice']) ? text(date('m/d/Y', strtotime($check_res['dateofservice']))) : text(date('m/d/Y')); ?>" autocomplete="off" disabled>
                                        <small class="text-danger date_error"></small>
                                    </div>   
                                    <div class="clearfix"></div>                                 
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="location" class="col-md-5 "><?php echo xlt('Location'); ?></label>
                                    <div class="col-md-6">
                                        <input type="text" name="location" id="location" class="form-control " value="<?php echo ($check_res['location']) ? text($check_res['location']) : ''; ?>" autocomplete="off" disabled>
                                        <small class="text-danger starttime_error"></small>
                                    </div>    
                                    <div class="clearfix"></div>                                
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="session_number" class="col-md-5 "><?php echo xlt('Session #'); ?></label>
                                    <div class="col-md-6">
                                        <input type="text" name="session_number" id="session_number" class="form-control" value="<?php echo ($check_res['session_number']) ? text($check_res['session_number']) : ''; ?>" autocomplete="off" disabled>
                                        <small class="text-danger endtime_error"></small>
                                    </div>   
                                    <div class="clearfix"></div>                                 
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="duration" class="col-md-5 "><?php echo xlt('Duration'); ?></label>
                                    <div class="col-md-6">
                                        <input type="text" id="duration" class="form-control" name="duration" value="<?php echo ($check_res['duration']) ? text($check_res['duration']) : ''; ?>" disabled>
                                        <small class="text-danger duration_error"></small>
                                    </div>       
                                    <div class="clearfix"></div>                             
                                </div>
                            </div>

                           
                            <div class="clearfix"></div>

                            <div class="col-md-12 margin-top-20">
                                <label for="service_note"><?php echo xlt('Service Note:'); ?></label>
                                <textarea name="service_note" id="service_note" rows="4" class="form-control" disabled><?php echo ($check_res['service_note']) ? text($check_res['service_note']) : ''; ?></textarea>
                                <small class="text-danger narrative_services_error"></small>
                            </div>

                            <div class="clearfix">&nbsp;</div>
                            
                    </fieldset>

                    

                    <div class="form-group clearfix">
                        <div class="col-sm-12 col-sm-offset-1 position-override">
                            <div class="btn-group oe-opt-btn-group-pinch" role="group">                               
                                <button type="button" class="btn btn-link btn-cancel oe-opt-btn-separate-left" onclick="top.restoreSession(); parent.closeTab(window.name, false);"><?php echo xlt('Cancel');?></button>
                                <a href="#" class="btn btn-default" id="print" style="margin-left: 18px">Print</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <script src="<?php echo $web_root; ?>/library/js/bootstrap-timepicker.min.js"></script>
        <script src="<?php echo $web_root; ?>/library/js/printThis.js"></script>
        <script language="javascript">
            $(document).ready(function(){

                $('.datepicker').datetimepicker({
                  <?php $datetimepicker_timepicker = false; ?>
                  <?php $datetimepicker_showseconds = false; ?>
                  <?php $datetimepicker_formatInput = false; ?>
                  <?php require($GLOBALS['srcdir'] . '/js/xl/jquery-datetimepicker-2-5-4.js.php'); ?>
                  <?php // can add any additional javascript settings to datetimepicker here; need to prepend first setting with a comma ?>
                });

                $("#print").on('click', function(){
                    $('.form_content').printThis({
                        debug: false,               // show the iframe for debugging
                        importCSS: true,            // import parent page css
                        importStyle: true,         // import style tags
                        printContainer: false,       // print outer container/$.selector
                        loadCSS: "",                // path to additional css file - use an array [] for multiple
                        pageTitle: "ICANS Note",              // add title to print page
                        removeInline: false,        // remove inline styles from print elements
                        removeInlineSelector: "*",  // custom selectors to filter inline styles. removeInline must be true
                        printDelay: 333,            // variable print delay
                        header: "<h2>ICANS Note</h2>",               // prefix to html
                        footer: null,               // postfix to html
                        base: false,                // preserve the BASE tag or accept a string for the URL
                        formValues: true,           // preserve input/form values
                        canvas: false,              // copy canvas content
                        doctypeString: '<!DOCTYPE html>',       // enter a different doctype for older markup
                        removeScripts: false,       // remove script tags from print content
                        copyTagClasses: false,      // copy classes from the html & body tag
                        beforePrintEvent: null,     // function for printEvent in iframe
                        beforePrint: null,          // function called before iframe is filled
                        afterPrint: null            // function called before iframe is removed
                    });
                });

            });
        </script>
    </body>
</html>
