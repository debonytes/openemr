<?php
/*
 * NotificationBase
 *
 * @author	  J. Alvin Harris <jalvin.code@gmail.com>
 * @author	  Sherwin Gaddis <>
 * 
 * This if the base class for Notifications to be sent
 */

namespace OpenEMR\Notifications;

//use PHPMailerOAuthGoogle;
use PHPMailer\PHPMailer\PHPMailer;
use OpenEMR\Common\Crypto\CryptoGen;

class NotificationBase extends PHPMailer
{
    public $Mailer;
    public $SMTPAuth;
    public $Host;
    public $Username;
    public $Password;
    public $Port;
    public $CharSet;

    public function __construct()
    {
        //Maybe later do some epic stuff

    }
	
	/****************************************
	 * RUN
	 * Send all neccessary emails
	 * get all data, create the body, call sendNotification
	 ****************************************/
	public function run()
	{
		// while ()
		//	 sendNotification($body, $email, $recipientName='', $subject='', $bcc='', $bccName='', $reply='', $replyName='')
		return;
	}

	/****************************************
	 * BUILD BODY
	 * defined by the child with variable length parameters or 
	 * not at all and $body is edited in run
	 ****************************************/
    /*
	public function buildBody()
    {
		$body = '';
		return $body;
    }
	*/

    /****************************************
	 * SEND NOTIFICATION
     * Send email to patient and a copy to the office
     * @param $body			the email body
	 * @param $email		the recipent's email address
	 * @param $recipentName the name of the recipent
	 * @param $subject		the email subject
	 * @param $bcc			the blind carbon copy to one other person
	 * @param $bccName		the name of bcc recipent
	 * @param $reply        the reply email address: 
								'sender' to use sending email
								''		 to use no email
								or use other valid email address
	 * @param $replyName	name to reply to
	 ****************************************/
    public function sendNotification($body, $email, $recipientName='', $subject='', $bcc='', $bccName='', $reply='', $replyName='')
    {
        $mail = new PHPMailer(TRUE);
        try {
            //$mail->SMTPDebug = 1; // uncomment for debugging
            $mail->isSMTP();
            $mail->IsHTML(true);
            $mail->Host = $GLOBALS['SMTP_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $GLOBALS['SMTP_USER'];
            $cryptoGen = new CryptoGen();
            $mail->Password = $cryptoGen->decryptStandard($GLOBALS['SMTP_PASS']);
            $mail->SMTPSecure = $GLOBALS['SMTP_SECURE'];
            $mail->Port = $GLOBALS['SMTP_PORT'];

            $mail->setFrom($GLOBALS['SMTP_USER'], $GLOBALS['patient_reminder_sender_name']);
            $mail->addAddress($email, $recipientName);
            $mail->Subject = $subject;
            $mail->Body = $body;
			if ($bcc != '')
			{
	            $mail->addBCC($bcc, $bccName);
			}

			if ($reply == 'sender')
			{
        		$mail->addReplyTo($GLOBALS['SMTP_USER'], $GLOBALS['SMTP_USER']);
			}
			elseif ($reply != '')
			{
        		$mail->addReplyTo($reply, $replyName);
			}
		    
            $mail->send();
            return '<br><br>Message Sent. Please check email for results';
        }
        catch (Exception $e)
        {
            echo "Message could not be sent";
            echo "<pre>";
            echo "Mailer error: " . $mail->ErrorInfo;

        }
    }
}
