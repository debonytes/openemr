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

function counselor_treatment_plan_review_report($pid, $encounter, $cols, $id)
{
    $count = 0;
    $data = formFetch("form_counselor_treatment_plan_review", $id);
    if ($data) {
        ?>
        <table style='border-collapse:collapse;border-spacing:0;width: 100%;'>
            <tr>
                <td align='center' style='border:1px solid #ccc;padding:4px;'><span class=bold><?php echo xlt('Name'); ?></span></td>
                <td align='center' style='border:1px solid #ccc;padding:4px;'><span class=bold><?php echo xlt('Medicaid'); ?></span></td>
            </tr>
            <tr>
                <td style='border:1px solid #ccc;padding:4px;'><span class=text><?php echo nl2br(text($data['name'])); ?></span></td>
                <td style='border:1px solid #ccc;padding:4px;'><span class=text><?php echo nl2br(text($data['medicaid'])); ?></span></td>
            </tr>
        </table>
        <?php
    }
}
?>