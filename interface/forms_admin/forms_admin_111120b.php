<?php
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.




//INCLUDES, DO ANY ACTIONS, THEN GET OUR DATA
require_once("../globals.php");
require_once("$srcdir/acl.inc");
require_once("$phpgacl_location/gacl_api.class.php");
require_once("$srcdir/registry.inc");

use OpenEMR\Common\Csrf\CsrfUtils;

//=========================================================================
// Create a Table for Additional Fields
//=========================================================================
global $extraCatTable, $dbLink;
$dbLink = $GLOBALS['dbh'];
$extraCatTable = 'openemr_postcalendar_categories_extra';
$catTblSql = "SHOW TABLES LIKE '".$extraCatTable."'";
$catTblResult = array_column(mysqli_fetch_all($dbLink->query($catTblSql)),0);
/* If table not exists, create... */
if(empty($catTblResult)) { 
    $sqlStr = "CREATE TABLE IF NOT EXISTS `$extraCatTable` (
      `id` bigint(20) NOT NULL AUTO_INCREMENT,
      `pc_catid` bigint(20) DEFAULT NULL,
      `registry_form_id` bigint(20) DEFAULT NULL,
      PRIMARY KEY (id)
    ) ENGINE=InnoDB";
    
    sqlStatement(rtrim("$sqlStr"));
    $catTblSql = "SHOW TABLES LIKE '".$extraCatTable."'";
    $catTblResult = array_column(mysqli_fetch_all($dbLink->query($catTblSql)),0);
}


if ($_GET['method'] == "enable") {
    if (!CsrfUtils::verifyCsrfToken($_GET["csrf_token_form"])) {
        CsrfUtils::csrfNotVerified();
    }
    updateRegistered($_GET['id'], "state=1");
} elseif ($_GET['method'] == "disable") {
    if (!CsrfUtils::verifyCsrfToken($_GET["csrf_token_form"])) {
        CsrfUtils::csrfNotVerified();
    }
    updateRegistered($_GET['id'], "state=0");
} elseif ($_GET['method'] == "install_db") {
    if (!CsrfUtils::verifyCsrfToken($_GET["csrf_token_form"])) {
        CsrfUtils::csrfNotVerified();
    }
    $dir = getRegistryEntry($_GET['id'], "directory");
    if (installSQL("$srcdir/../interface/forms/{$dir['directory']}")) {
        updateRegistered($_GET['id'], "sql_run=1");
    } else {
        $err = xl('ERROR: could not open table.sql, broken form?');
    }
} elseif ($_GET['method'] == "register") {
    if (!CsrfUtils::verifyCsrfToken($_GET["csrf_token_form"])) {
        CsrfUtils::csrfNotVerified();
    }
    registerForm($_GET['name']) or $err=xl('error while registering form!');
}


if( isset($_POST['registryformid']) && $_POST['registryformid'] ) {
    foreach($_POST['registryformid'] as $k=>$val) {
        $formID = $val;
        if( isset($_POST['calendar_cat_'.$val]) ) {
            $calendar_cat_id = ($_POST['calendar_cat_'.$val] && $_POST['calendar_cat_'.$val]>0) ? $_POST['calendar_cat_'.$val] : '';
            if($calendar_cat_id) {
                $formInfo = get_assigned_form_info($calendar_cat_id);
                if($formInfo) {
                    sqlQuery("UPDATE $extraCatTable SET registry_form_id=".$formID." WHERE pc_catid=".$calendar_cat_id);
                } else {
                    sqlQuery("INSERT INTO $extraCatTable SET pc_catid=?,registry_form_id=?", array($calendar_cat_id, $formID));
                }
            }
        }
    }
}


$absPath = str_replace("library","",$srcdir); /* will result: /Library/WebServer/www/openemr/ */

function get_assigned_form_info($catid,$field=null) {
    global $extraCatTable, $dbLink;
    if( empty($catid) ) return '';

    $catTblSql = "SHOW TABLES LIKE '".$extraCatTable."'";
    $catTblResult = array_column(mysqli_fetch_all($dbLink->query($catTblSql)),0);
    if($catTblResult) {
        $res = sqlQuery("SELECT pc_catid, registry_form_id FROM openemr_postcalendar_categories_extra WHERE pc_catid=".$catid);
        if($res) {
            if($field) {
                return ($res) ? $res[$field] : ''; 
            } else {
                return ($res) ? $res : ''; 
            }
        }
        
    } else {
        return '';
    }
}


function isTableExists($tblName) {
    global $dbLink;
    $link = $dbLink;
    $sqlquery = "SHOW TABLES LIKE '".$tblName."'";
    //$listdbtables = array_column(mysqli_fetch_all($link->query('SHOW TABLES')),0); /* list all tables */
    $listdbtables = array_column(mysqli_fetch_all($link->query($sqlquery)),0);
    return $listdbtables;
}

function get_postcalendar_categories() {
    global $dbLink;
    $link = $dbLink;
    $sqlquery = "SELECT pc_catid, pc_catname FROM openemr_postcalendar_categories ORDER BY pc_catname ASC";
    $result = $link->query($sqlquery);
    $calendar_categories = array();
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $calendar_categories[] = $row;
        
      }
    }
    return ($calendar_categories) ? $calendar_categories : '';
}

/* Get Form Categories */
$registryFields = sqlListFields("registry");
$hasCatOrderField = array();
if($registryFields) {
    foreach($registryFields as $rf) {
       if($rf=='category_order') {
            $hasCatOrderField[] = 1;
       }
    }
}

/* MAKE SURE 'category_order' field exists otherwise add this category in the table */
if(empty($hasCatOrderField)) {
    $sql_action = "ALTER TABLE `registry` ADD `category_order` INT NULL DEFAULT NULL AFTER `aco_spec`";
    sqlStatement($sql_action);
}

function get_registry_form_categories() {
    global $dbLink;
    $sqlquery = "SELECT category FROM registry WHERE TRIM(IFNULL(category,'')) <> '' GROUP BY category ORDER BY category ASC";
    $result = $dbLink->query($sqlquery);
    $categories = array();
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $categories[] = $row['category'];
      }
    }
    return ($categories) ? $categories : '';
}

function get_sorted_form_categories() {
    global $dbLink;
    $sqlquery = "SELECT * FROM globals WHERE gl_name='form_tabs_custom_order'";
    $result = sqlquery($sqlquery);
    $sortCatData = ( isset($result['gl_value']) && $result['gl_value'] ) ? @unserialize($result['gl_value']) : '';
    return $sortCatData;
}


function updateTblDB($dir){
    $folderName = basename($dir);
    $tblName = "form_" . $folderName  . "_BAK";
    if( isTableExists($tblName) ) {
        return '';
    } else {
        $sqlFile = $dir."/update-table.sql";
        if( file_exists($sqlFile) ) {
            if ($sqlarray = @file($sqlFile)) {
                $sql = implode("", $sqlarray);
                $sqla = explode(";", $sql);
                foreach ($sqla as $sqlq) {
                    if (strlen($sqlq) > 5) {
                        sqlStatement(rtrim("$sqlq"));
                    }
                }
            }
            return true;
        } else {
            return false;
        }
    }
}

if ($_GET['method'] == "updateDb") {
    if (!CsrfUtils::verifyCsrfToken($_GET["csrf_token_form"])) {
        CsrfUtils::csrfNotVerified();
    }
    $dir = getRegistryEntry($_GET['id'], "directory");
    $formDIR = $dir['directory'];
    $formPATH = $absPath . 'interface/forms/' . $formDIR;
    if (updateTblDB($formPATH)) {
    } else {
        $err = xl('ERROR: could not open table.sql, broken form?');
    }
}

if ($_POST['method'] == "form_cat_reorder" && isset($_POST['categoryorder']) ) {
    $sortedItems = array();
    $n=1; foreach( $_POST['categoryorder'] as $k=>$val ) {
        $val = trim($val);
        sqlStatement("UPDATE registry SET category_order = '".$n."' WHERE category = '".$val."'");
        $sortedItems[$n] = $val;
        $n++;
    }

    /* Check if 'form_tabs_custom_order' on `globals` table */
    $sortedItemsVal = ($sortedItems) ? serialize($sortedItems) : '';
    $globalData = sqlQuery("SELECT * FROM globals WHERE gl_name='form_tabs_custom_order'");
    if($globalData) {
        sqlStatement("UPDATE globals SET gl_value = '".$sortedItemsVal."' WHERE gl_name = 'form_tabs_custom_order'");
    } else {
        sqlStatement("INSERT INTO `globals` (`gl_name`, `gl_index`, `gl_value`) VALUES ('form_tabs_custom_order', '0', '".$sortedItemsVal."')");
    }
}




$bigdata = getRegistered("%") or $bigdata = false;
    
//START OUT OUR PAGE....
?>

<?php
$is_updated = array();
if (!empty($_POST)) {
    if (!CsrfUtils::verifyCsrfToken($_POST["csrf_token_form"])) {
        CsrfUtils::csrfNotVerified();
    }
    
    foreach ($_POST as $key => $val) {
        if (preg_match('/nickname_(\d+)/', $key, $matches)) {
            sqlQuery("update registry set nickname = ? where id = ?", array($val, $matches[1]));
            $is_updated[] = $matches[1];
        } else if (preg_match('/category_(\d+)/', $key, $matches)) {
            sqlQuery("update registry set category = ? where id = ?", array($val, $matches[1]));
            $is_updated[] = $matches[1];
        } else if (preg_match('/priority_(\d+)/', $key, $matches)) {
            sqlQuery("update registry set priority = ? where id = ?", array($val, $matches[1]));
            $is_updated[] = $matches[1];
        } else if (preg_match('/aco_spec_(\d+)/', $key, $matches)) {
            sqlQuery("update registry set aco_spec = ? where id = ?", array($val, $matches[1]));
            $is_updated[] = $matches[1];
        } else if (preg_match('/formtitle_(\d+)/', $key, $matches)) {
            sqlQuery("update registry set name = ? where id = ?", array($val, $matches[1]));
            $is_updated[] = $matches[1];
        }
    }
}
?>
<html>
<head>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" href="../../public/assets/font-awesome/css/font-awesome.min.css" type="text/css">
<link rel="stylesheet" type="text/css" href="../../public/assets/jquery-ui/jquery-ui.min.css">
<style type="text/css">
a.link_submit,
a.link_submit:active,
a.link_submit:visited {
    color: #00C;
}
#formMessage {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 200;
}
#formMessage .messagediv {
    background: #fdffe7;
    border: 1px solid #d4da95;
    padding: 8px 10px;
    font-size: 15px;
    max-width: 100%;
    width: 100%;
    color: #136938;
     -webkit-animation-duration: 1s;
    animation-duration: 1s;
    -webkit-animation-fill-mode: both;
    animation-fill-mode: both;
}
#formMessage .errormsg {
    background: #ffdcdc;
    border: 1px solid #ff9c9c;
    color: #7d0101;
    padding: 5px 10px;
    font-size: 15px;
}
#closeFormMsg {
    display: inline-block;
    cursor: pointer;
    width: 18px;
    height: 18px;
    background: #4c4c4c;
    border-radius: 100px;
    text-align: center;
    color: #FFF;
    font-size: 11px;
    font-weight: bold;
    line-height: 16px;
    position: relative;
    right: -5px;
    top: -1px;
}
.fadeIn {
  -webkit-animation-name: fadeIn;
  animation-name: fadeIn;
}
#formAdmin input.updateBtn {
    cursor: pointer;
    display: inline-block;
    padding: 4px 10px;
    position: relative;
    top: -8px;
    left: 5px;
    font-size: 14px;
    text-transform: capitalize;
}
table.tableStyle thead td {
    font-size: 11px;
    background: #353535;
    color: #FFF;
    padding: 5px 5px;
}
@keyframes fadeIn {
  from {
    opacity: 0;
  }

  to {
    opacity: 1;
  }
}
.lds-spinner {
  color: official;
  display: inline-block;
  position: relative;
  width: 80px;
  height: 80px;
}
.lds-spinner div {
  transform-origin: 40px 40px;
  animation: lds-spinner 1.2s linear infinite;
}
.lds-spinner div:after {
  content: " ";
  display: block;
  position: absolute;
  top: 3px;
  left: 37px;
  width: 6px;
  height: 18px;
  border-radius: 20%;
  background: #fff;
}
.lds-spinner div:nth-child(1) {
  transform: rotate(0deg);
  animation-delay: -1.1s;
}
.lds-spinner div:nth-child(2) {
  transform: rotate(30deg);
  animation-delay: -1s;
}
.lds-spinner div:nth-child(3) {
  transform: rotate(60deg);
  animation-delay: -0.9s;
}
.lds-spinner div:nth-child(4) {
  transform: rotate(90deg);
  animation-delay: -0.8s;
}
.lds-spinner div:nth-child(5) {
  transform: rotate(120deg);
  animation-delay: -0.7s;
}
.lds-spinner div:nth-child(6) {
  transform: rotate(150deg);
  animation-delay: -0.6s;
}
.lds-spinner div:nth-child(7) {
  transform: rotate(180deg);
  animation-delay: -0.5s;
}
.lds-spinner div:nth-child(8) {
  transform: rotate(210deg);
  animation-delay: -0.4s;
}
.lds-spinner div:nth-child(9) {
  transform: rotate(240deg);
  animation-delay: -0.3s;
}
.lds-spinner div:nth-child(10) {
  transform: rotate(270deg);
  animation-delay: -0.2s;
}
.lds-spinner div:nth-child(11) {
  transform: rotate(300deg);
  animation-delay: -0.1s;
}
.lds-spinner div:nth-child(12) {
  transform: rotate(330deg);
  animation-delay: 0s;
}
@keyframes lds-spinner {
  0% {
    opacity: 1;
  }
  100% {
    opacity: 0;
  }
}
div#loader {
    width: 100%;
    height: 100%;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 300;
    background: rgba(0,0,0,.85);
    text-align: center;
    display: none;
}
.loadertxt {
    margin-top: 10%;
    display: inline-block;
    color: #FFF;
}
.formTopLinks {
    position: relative;
    z-index: 5;
}
span.formMiscOption {
    display: inline-block;
    position: relative;
    bottom: -1px;
}
span.formMiscOption a {
    display: inline-block;
    color: #00c;
    padding: 6px 8px;
    border: 1px solid #b5b5b5;
}
span.formMiscOption a.active {
    color: #ababab;
    border-bottom-color: #f0fbf5;
}
.toggleContent {
    padding: 15px;
    border: 1px solid #b5b5b5;
    margin: 0 0;
}
.toggleContent h2.formHead {
    font-size: 16px;
    margin: 0 0 10px;
}
.toggleContent,
.toggleContent * {
    box-sizing: border-box;
}
.toggleContent table {
    width: 100%;
    max-width: 100%;
    border-collapse: collapse;
}
.toggleContent table td {
    border: 1px solid #FFF;
}
.toggleContent table tbody td {
    padding: 2px 3px;
}
.toggleContent table .formTitle input {
    width: 100%;
    margin: 0 0!important;
}
.customHR {
    border-top: 5px solid #CCC!important;
    margin: 25px 0;
}
</style>
</head>
<body class="body_top">
<div id="loader"><div class="loadertxt"><div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div><div class="txt">Saving data...Please wait.</div></div></div>
<span class="title"><?php echo xlt('Forms Administration');?></span>
<br><br>
<div id="formMessage">
<?php //ERROR REPORTING
if ($err) {
    echo "<div class='errormsg'><span class='bold'>" . text($err) . "</span></div><br><br>\n";
}
?>
</div>

<?php 
$calendar_categories = get_postcalendar_categories();  
?>
<?php //REGISTERED SECTION ?>
<div class="formTopLinks">
 <span class="formMiscOption bold"><a href="#" id="registeredFormsBtn" data-content="#registeredData" class="toggleLink active"><?php echo xlt('Registered/Unregistered');?></span></a>
 <span class="formMiscOption bold"><a href="#" id="sortFormTab" data-content="#formCatsData" class="toggleLink">Reorder Form Tabs</a></span>   
</div>

<div id="registeredData" class="screenWrap toggleContent">
    <div class="formInner">
    <h2 class="formHead"><?php echo xlt('Registered');?></h2>
    <form method=POST action ='./forms_admin.php' id="formAdmin" data-baseurl="<?php echo $rootdir; ?>/forms_admin/forms_admin.php?formstat=updated">
    <i><?php echo xlt('click here to update priority, category, nickname and access control settings'); ?></i>
    <input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
    <input type='submit' name='update' value='<?php echo xla('update'); ?>' class="updateBtn"><br>
    <table border=0 cellpadding=1 cellspacing=2 width="500" class="tableStyle registeredEntries">
        <thead>
            <tr>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td><?php echo xlt('Priority'); ?> </td>
                <td><?php echo xlt('Category'); ?> </td>
                <td><?php echo xlt('Nickname'); ?> </td>
                <td><?php echo xlt('Assign Calendar Category'); ?></td>
                <td><?php echo xlt('Access Control'); ?></td>
            </tr>
        </thead>
    <?php
    $color="#CCCCCC";
    if ($bigdata != false) {
        foreach ($bigdata as $registry) {
            $priority_category = sqlQuery(
                "select priority, category, nickname, aco_spec from registry where id = ?",
                array($registry['id'])
            );
            $formDIR = $registry['directory'];
            $file_tbl_update = $absPath."interface/forms/".$formDIR."/update-table.sql";
            $formTblName = "form_" . $formDIR;
            $must_update_tbl = false;
            if(file_exists($file_tbl_update)) {
                $formTblNameBAK = "form_" . $formDIR . "_BAK";
                if( $res = isTableExists($formTblNameBAK) ) {
                    $must_update_tbl = false;
                } else {
                    $must_update_tbl = true;
                }
            }
            ?>
          <tr class="registeredFormItem" id="row-id-<?php echo text($registry['id']); ?>" data-id="<?php echo text($registry['id']); ?>" data-directory="<?php echo text($formDIR); ?>">
        <td bgcolor="<?php echo $color; ?>" width="2%">
          <span class='text'><?php echo text($registry['id']); ?></span>
          <input type="hidden" name="registryformid[]" value="<?php echo text($registry['id']); ?>">
        </td>
        <td bgcolor="<?php echo attr($color); ?>" width="30%">
          <span class='bold formTitle'><input type="text" name="formtitle_<?php echo text($registry['id']); ?>" value="<?php echo text(xl_form_title($registry['name'])); ?>"></span>
        </td>
            <?php
            if ($registry['sql_run'] == 0) {
                echo "<td bgcolor='" . attr($color) . "' width='10%'><span class='text'>" . xlt('registered') . "</span>";
            } elseif ($registry['state'] == "0") {
                echo "<td bgcolor='#FFCCCC' width='10%' class='tdStatz'><a class='link_submit statz' href='./forms_admin.php?id=" . attr_url($registry['id']) . "&method=enable&csrf_token_form=" . attr_url(CsrfUtils::collectCsrfToken()) . "'>" . xlt('activate') . "</a>";
            } else {
                echo "<td bgcolor='#CCFFCC' width='10%' class='tdStatz'><a class='link_submit statz' href='./forms_admin.php?id=" . attr_url($registry['id']) . "&method=disable&csrf_token_form=" . attr_url(CsrfUtils::collectCsrfToken()) . "'>" . xlt('deactivate') . "</a>";
            }
            ?></td>
            <td bgcolor="<?php echo attr($color); ?>" width="10%">
          <span class='text'><?php
            if ($registry['unpackaged']) {
                echo xlt('PHP extracted');
            } else {
                echo xlt('PHP compressed');
            }
            ?></span>
            </td>
            <td bgcolor="<?php echo attr($color); ?>" width="10%">
            <?php
            if ($registry['sql_run']) {
                echo "<span class='text dbstat dbInstalled'>" . xlt('DB installed') . "</span>";            
                if($must_update_tbl) {
                    echo "<div style='margin:5px 0;width:80px;'><a data-form='".text(xl_form_title($registry['name']))."' href='#' data-table='form_".$formDIR."' data-action='./forms_admin.php?id=".attr_url($registry['id'])."&method=updateDb&csrf_token_form=".attr_url(CsrfUtils::collectCsrfToken())."' class='updateDb' style='font-size:11px;color:red;text-decoration:underline;'>" . xlt('Update Table') . "</a></div>";
                }
            } else {
                echo "<a data-form='".text(xl_form_title($registry['name']))."' class='link_submit dbstat startDbInstall dbUninstalled' href='./forms_admin.php?id=" . attr_url($registry['id']) . "&method=install_db&csrf_token_form=" . attr_url(CsrfUtils::collectCsrfToken()) . "'>" . xlt('install DB') . "</a>";
            }
            ?>
            </td>
            <?php
            echo "<td><input type='text' size='4'  name='priority_" . attr($registry['id']) . "' value='" . attr($priority_category['priority']) . "'></td>";
            echo "<td><input type='text' size='10' name='category_" . attr($registry['id']) . "' value='" . attr($priority_category['category']) . "' class='catNameField'></td>";
            echo "<td><input type='text' size='10' name='nickname_" . attr($registry['id']) . "' value='" . attr($priority_category['nickname']) . "'></td>";
            
            echo "<td>";
            echo "<select name='calendar_cat_" . attr($registry['id']) . "' style='width:150px;'>";
            echo "<option value='-1'>---</option>";
            if($calendar_categories) {
                foreach($calendar_categories as $c) {
                    $c_id = $c['pc_catid'];
                    $c_form_id = get_assigned_form_info($c_id,'registry_form_id');
                    $c_selected = ($registry['id']==$c_form_id) ? ' selected':'';
                    echo "<option value='".$c['pc_catid']."'".$c_selected.">".$c['pc_catname']."</option>";
                }
            }
            echo "</select>";
            echo "</td>";

            echo "<td>";
            echo "<select name='aco_spec_" . attr($registry['id']) . "' style='width:280px;'>";
            echo "<option value=''></option>";
            echo gen_aco_html_options($priority_category['aco_spec']);
            echo "</select>";
            echo "</td>";
            ?>
          </tr>
            <?php
            if ($color=="#CCCCCC") {
                $color="#999999";
            } else {
                $color="#CCCCCC";
            }
        } //end of foreach
    }
    ?>
    </table>

    <div class="customHR"></div>

    <?php  //UNREGISTERED SECTION ?>
    <h2 class="formHead"><?php echo xlt('Unregistered');?></h2>
    <table border=0 cellpadding=1 cellspacing=2 width="500" class="unRegisteredEntries tableStyle">
    <?php
    $dpath = "$srcdir/../interface/forms/";
    $dp = opendir($dpath);
    $color="#CCCCCC";
    for ($i=0; false != ($fname = readdir($dp)); $i++) {
        if ($fname != "." && $fname != ".." && $fname != "CVS" && $fname != "LBF" &&
        (is_dir($dpath.$fname) || stristr($fname, ".tar.gz") ||
        stristr($fname, ".tar") || stristr($fname, ".zip") ||
        stristr($fname, ".gz"))) {
            $inDir[$i] = $fname;
        }
    }

    // ballards 11/05/2005 fixed bug in removing registered form from the list
    if ($bigdata != false) {
        foreach ($bigdata as $registry) {
            $key = array_search($registry['directory'], $inDir) ;  /* returns integer or FALSE */
            unset($inDir[$key]);
        }
    }

    foreach ($inDir as $fname) {
        if (stristr($fname, ".tar.gz") || stristr($fname, ".tar") || stristr($fname, ".zip") || stristr($fname, ".gz")) {
            $phpState = "PHP compressed";
        } else {
            $phpState =  "PHP extracted";
        }
        ?>
        <tr>
            <td bgcolor="<?php echo $color?>" width="1%">
                <span class=text> </span>
            </td>
            <td bgcolor="<?php echo $color?>" width="20%">
                <?php
                    $form_title_file = @file($GLOBALS['srcdir']."/../interface/forms/$fname/info.txt");
                if ($form_title_file) {
                        $form_title = $form_title_file[0];
                } else {
                    $form_title = $fname;
                }
                ?>
                <span class="bold newForm"><?php echo text(xl_form_title($form_title)); ?></span>
            </td>
            <td bgcolor="<?php echo $color?>" width="10%"><?php
            if ($phpState == "PHP extracted") {
                echo '<a data-form="'.text(xl_form_title($form_title)).'" class="link_submit registerFormBtn" href="./forms_admin.php?name=' . attr_url($fname) . '&method=register&csrf_token_form=' . attr_url(CsrfUtils::collectCsrfToken()) . '">' . xlt('register') . '</a>';
            } else {
                echo '<span class=text>' . xlt('n/a') . '</span>';
            }
            ?></td>
            <td bgcolor="<?php echo $color?>" width="20%">
                <span class=text><?php echo xlt($phpState); ?></span>
            </td>
            <td bgcolor="<?php echo $color?>" width="10%">
                <span class=text><?php echo xlt('n/a'); ?></span>
            </td>
        </tr>
        <?php
        if ($color=="#CCCCCC") {
                $color="#999999";
        } else {
            $color="#CCCCCC";
        }

        flush();
    }//end of foreach
    ?>
    </table>
    </form>
  </div>
</div>

<style type="text/css">
.reorderWrapper ul {
    margin: 0 0;
    padding: 0 0;
    list-style: none;
}
.reorderWrapper li.ui-sortable-placeholder{
    border: 1px dashed #a3a74d;
    background: #feffeb;
    height: 31px;
    padding: 0 0;
}

.reorderWrapper ul li span.ui-icon {
    background-image: none!important;
}
.reorderWrapper ul li {
    padding: 6px 10px 6px 32px;
    margin: 5px 0;
    position: relative;
}
.reorderWrapper ul li span.fa {
    display: none;
    font-size: 13px;
    line-height: 1;
    position: absolute;
    top: 9px;
    left: 10px;
}
.reorderWrapper ul li em {
    display: inline-block;
    font-style: normal;
    color: #03ab4a;
    font-size: 13px;
    line-height: 1;
    position: absolute;
    top: 9px;
    left: 7px;
}
.formSubmitBtn {
    margin: 5px 0 10px;
    padding: 5px 12px;
    text-transform: capitalize;
}
</style>
<?php 
    $formCategoriesSorted = get_sorted_form_categories();
    $formCategories = get_registry_form_categories(); 
    $formCategoriesList = array();
    $exceptions = array('Miscellaneous','Layout Based');
    $otherCats = implode(",",$exceptions);

    if($formCategories) {
      array_push($formCategories,'Miscellaneous','Layout Based');
      if($formCategoriesSorted) {
        $actualList = array_values($formCategories);
        $sorted = array_values($formCategoriesSorted);
        $newCats = array();
        
        foreach($formCategories as $k=>$v) {
          if( !in_array($v,$formCategoriesSorted) ) {
            $newCats[] = $v;
          }
        }

        if($newCats) {
          $formCategoriesList = array_merge($sorted,$newCats);
        } else {
          $formCategoriesList = $sorted;
        }

        foreach($formCategoriesList as $k=>$v) {
          if(!in_array($v,$actualList) ) {
            unset($formCategoriesList[$k]);
          }
        }
        
      } else {
        $formCategoriesList = $formCategories;
      }
    }
?>
<div id="formCatsData" class="form-reorder-wrap reorderWrapper toggleContent" style="display:none;">
    <div class="reorder-content">
        <h2 class="formHead">Reorder Form Tabs:</h2>
        <?php if ($formCategoriesList) { ?>
        <form action="./forms_admin.php" method="POST" id="reorderFormTabs" data-baseurl="<?php echo $rootdir; ?>/forms_admin/forms_admin.php?formstat=updated">
            <input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
            <input type="hidden" name="method" value="form_cat_reorder">
            <input type='submit' name='updatefrmtab' value='<?php echo xla('update'); ?>' class="updateBtnFrmTab formSubmitBtn">
            <ul id="formTabsSortable">
                <?php $ctr=1; foreach ($formCategoriesList as $cat) { ?>
                    <li class="ui-state-default">
                        <span class="fa fa-arrows"></span><em class="num">[<?php echo $ctr;?>]</em> <strong><?php echo $cat;?></strong>
                        <input type="hidden" name="categoryorder[<?php echo $ctr;?>]" class="catsortInput" value="<?php echo $cat;?>">
                    </li>
                <?php $ctr++; } ?>
            </ul>
        </form>  
        <?php } else { ?>
            <p><strong style="color:red">No categories added.</strong></p>
        <?php } ?>
    </div>
</div>

<script src="../../public/assets/jquery-1-10-2/jquery.min.js"></script>
<script src="../../public/assets/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript">
var pageURL = '<?php echo $rootdir; ?>/forms_admin/forms_admin.php';
var params={};location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(s,k,v){params[k]=v});
jQuery(document).ready(function($){

    if( typeof params.formstat!='undefined' && params.formstat=='updated' ) {
        var baseURL = $("form#formAdmin").attr('data-baseurl');
        var newBaseURL = baseURL.replace('?formstat=updated','');
        window.history.replaceState = newBaseURL;
    }

    /* Activate / Deactivate */
    $(document).on("click","a.statz",function(e){
        e.preventDefault();
        var target = $(this);
        var currentText = target.text().toLowerCase();
        var actionLink = target.attr("href");
        var newLink = actionLink;
        var row = $(this).parents("tr.registeredFormItem");
        var formName = row.find(".formTitle input").val();
        var confirmMessage = '';
        var newActionType = 'deactivate';
        if ( ~actionLink.indexOf("enable") ) {
            confirmMessage = "Are you sure you want to ACTIVATE `" + formName + "`?";
            newLink = actionLink.replace("method=enable","method=disable");
            newActionType = 'deactivate';
        } else {
            confirmMessage = "Are you sure you want to DEACTIVATE `" + formName + "`?";
            newLink = actionLink.replace("method=disable","method=enable");
            newActionType = 'activate';
        }

        if ( confirm(confirmMessage) ) {

            $.ajax({
              url: actionLink,
              type: 'POST',
              beforeSend:function(){
                  $("#loader").show();
              },
              success: function(response){
                  var message = '<div class="messagediv fadeIn"><i class="fa fa-fw fa-check"></i> <strong>Forms Administration Updated.</strong> <a id="closeFormMsg">x</a></div>';
                  $("#formMessage").html(message);
                  target.attr("href",newLink);
                  target.text(newActionType);
                  if(currentText=='activate') {
                    target.parents("td.tdStatz").css("background-color","#FFC9CC");
                  } else {
                    target.parents("td.tdStatz").css("background-color","#CCFFCC");
                  }
                 

                  setTimeout(function(){
                      $("#loader").hide();
                  },500);
              },
              errors: function(response){

              }
          });

        } else {
            return false;
        }
    });

    /* Register a Form */
    $(document).on("click","a.registerFormBtn",function(e){
        e.preventDefault();
        var target = $(this);
        var actionLink = target.attr("href");
        var formName = target.attr("data-form");
        if ( confirm("Are you sure you want to register the `"+formName+"`?") ) {

            $.ajax({
            url: actionLink,
            type: 'POST',
            beforeSend:function(){
              $("#loader").show();
            },
            success: function(response){
              var message = '<div class="messagediv fadeIn"><i class="fa fa-fw fa-check"></i> <strong>Forms Administration Updated.</strong> <a id="closeFormMsg">x</a></div>';
              $("#registeredData").load(pageURL+" .formInner",function(){
                $("#formMessage").html(message);
                setTimeout(function(){
                  $("#loader").hide();
                },500);
              });
            },
            errors: function(response){

            }
        });

      } else {
        return false;
      }
    });

    /* Install Database */
    $(document).on("click","a.startDbInstall",function(e){
        e.preventDefault();
        var target = $(this);
        var actionLink = target.attr("href");
        var formName = target.attr("data-form");
        if ( confirm("Are you sure you want to install the table for `"+formName+"`?") ) {
          $.ajax({
          url: actionLink,
          type: 'POST',
          beforeSend:function(){
            $("#loader").show();
          },
          success: function(response){
            var message = '<div class="messagediv fadeIn"><i class="fa fa-fw fa-check"></i> <strong>Forms Administration Updated.</strong> <a id="closeFormMsg">x</a></div>';
            $("#registeredData").load(pageURL+" .formInner",function(){
              $("#formMessage").html(message);
              setTimeout(function(){
                $("#loader").hide();
              },500);
            });
          },
          errors: function(response){

          }
        });

      } else {
        return false;
      }
    });


    $(document).on("click","a.updateDb",function(e){
        e.preventDefault();
        var target = $(this);
        var action = $(this).attr("data-action");
        var tableName = $(this).attr("data-table");
        if (confirm("IMPORTANT NOTE!!!\nThe data and structure will be deleted and will be replaced with a new one. Are you sure you want to update the table `"+tableName+"`?")) {
            //window.location.href = action;
            // target.hide();
            $.get(action,function(){
                var message = '<div class="messagediv fadeIn"><i class="fa fa-fw fa-check"></i> <strong>Forms Administration Updated.</strong></div>';
                $("#formMessage").html(message);
                target.remove();
            });
        } else {
            return false;
        }
    });
    $(document).on("submit","form#formAdmin",function(e){
        e.preventDefault();
        var formAction = $(this).attr("action");
        var formData = $(this).serialize();
        var baseURL = $(this).attr('data-baseurl');
        $.ajax({
            url: formAction,
            type: 'POST',
            data: formData,
            beforeSend:function(){
                $("#loader").show();
            },
            success: function(response){
                var message = '<div class="messagediv fadeIn"><i class="fa fa-fw fa-check"></i> <strong>Forms Administration Updated.</strong> <a id="closeFormMsg">x</a></div>';
                $("#formMessage").html(message);
                $("#formCatsData").load(formAction+" .reorder-content",function(){
                    doSortFormTabs();
                });

                setTimeout(function(){
                    $("#loader").hide();
                },500);
            },
            errors: function(response){

            }
        });
    });
    $(document).on("click","#closeFormMsg",function(e){
        e.preventDefault();
        $("#formMessage").html("");
    });

    /* REORDER FORM TABS */

    doSortFormTabs();
    function doSortFormTabs() {
        $( "#formTabsSortable" ).sortable({
            placeholder: "ui-state-highlight",
            update: function( event, ui ) {
                var i = 1;
                $("#formTabsSortable li").each(function(){
                    $(this).find(".num").text("["+i+"]");
                    $(this).find("input.catsortInput").attr("name","categoryorder["+i+"]");
                    i++;
                });
            }
        });
        $( "#formTabsSortable" ).disableSelection();
    }
    


    $(document).on("click","a.toggleLink",function(e){
        e.preventDefault();
        var content = $(this).attr("data-content");
        $("a.toggleLink").removeClass("active");
        $(".toggleContent").hide();
        if( $(content).length>0 ) {
            $(this).addClass("active");
            $(".toggleContent"+content).show();
        }
    });


    $(document).on("submit","form#reorderFormTabs",function(e){
        e.preventDefault();
        var formAction = $(this).attr("action");
        var formData = $(this).serialize();
        var baseURL = $(this).attr('data-baseurl');
        $.ajax({
            url: formAction,
            type: 'POST',
            data: formData,
            beforeSend:function(){
                $("#loader").show();
            },
            success: function(response){
                var message = '<div class="messagediv fadeIn"><i class="fa fa-fw fa-check"></i> <strong>Forms Administration Updated.</strong> <a id="closeFormMsg">x</a></div>';
                $("#formMessage").html(message);
                setTimeout(function(){
                    $("#loader").hide();
                },500);
            },
            errors: function(response){

            }
        });
    });

});
</script>
</body>
</html>
