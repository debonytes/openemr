<?php
/* DAILY CODE RUN
 * @author	J. Alvin Harris <jalvin.code@gmail.com>
 *
 * This code is to be run daily by a bat file
 * Currently only email notifications are sent
 */

namespace OpenEMR\bat\dailCodeRun;

$ignoreAuth = true;
$cronJob = "default";
require_once('C:/xampp/htdocs/openemr/interface/globals.php');

use PHPMailer\PHPMailer\PHPMailer;
use OpenEMR\Common\Crypto\CryptoGen;
use OpenEMR\Notifications\Base;
use OpenEMR\Notifications\SendNotifications;

$debug				= true;		// turn on or off for debugging purposes
$continueAfterDebug = true;		// continue in debug or exit after test email 
$testEmail			= "jalvin.code@gmail.com";

// daily notifications in a try catch statement
try
{
	if ($debug)
	{
		echo "Sending notifications...\n";
	}

	$allNotifications = new SendNotifications;
	$allNotifications->run($debug, $continueAfterDebug, $testEmail);
}
catch (Exception $e) 
{
	echo 'Caught exception while Sending Notifications: ', $e->getMessage(), "\n", $e->getTraceAsString(), "\n";
}
//exit();

/* Placed in globals.php at about line 134

// This is a juryrig for cron jobs (ie dailyCodeRun.php). 
// No clue how to properly do it.
// But this allows cron job to run without encountering
// The error:
//		Invalid URLRequest with site id '' contains invalid characters.
	if (empty($tmp) and !empty($cronJob))
	{
		$tmp = $cronJob;
	}
*/
?>