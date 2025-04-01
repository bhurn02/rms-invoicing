<?php
/*
Author:  Aldrich Delos Santos
Date:  October 13, 2015
Description: PHPMailer SMTP Mail Sender

FIXES:
	2023-12-07	Aldrich
	- Added retry functionality for sending mails

	2023-11-29	Aldrich
	- Added error handler and save it to php-mailer file logs
	- Added php mailer audit trail for sent mails

	2023-06-14	Aldrich
	- Improved attachment handling via array


SMTPDebug OPTIONS
level 0 = Turns off SMTP debugging
level 1 = client; will show you messages sent by the client
level 2  = client and server; will add server messages, it’s the recommended setting.
level 3 = client, server, and connection; will add details about the initial information, which might be useful for discovering STARTTLS failures
level 4 = low-level information. 	
*/

date_default_timezone_set("Pacific/Guam");

require_once('PHPMailer/PHPMailerAutoload.php');


// $results_messages = array();

$mail = new PHPMailer(true);
// $mail->CharSet = 'utf-8';
// ini_set('default_charset', 'UTF-8');

class phpmailerAppException extends phpmailerException
{
}


function sendSMTPMail($SenderEmail, $SenderName, $SendTo, $SendToName, $SendCC, $SendBCC, $Subject, $emailMessage, $attachments = array())
{

	/*
	-- ATTACHMENT FORMAT when passing as argument --

	$attachments = array(
		array(
			"path"=>"C:\VNC Sandbox\hr2v2 - revert etla batch POI 2023-05-30.sql"
			,"name"=>"test-file 1"
			,"encoding"=>"base64"
			,"type"=>"text/calendar"
		)
		,array(
			"path"=>"C:\VNC Sandbox\KRS - 3485154402276_attlog 20230522.txt"
			,"name"=>"test-file 2"
			,"encoding"=>"base64"
			,"type"=>"text/calendar"
		)
	);
	*/

	// $attachments=($attachments)?$attachments:array();
	// print_r($attachments);
	// die();

	$results_messages = array();

	$mail = new PHPMailer(true);
	$mail->CharSet = 'utf-8';
	ini_set('default_charset', 'UTF-8');
	try {
		
		$mail->isSMTP();
		$mail->SMTPDebug  = 2; /* Development Mode */
		//Ask for HTML-friendly debug output
		$mail->Debugoutput = 'html';

		/* FIX SSL Error */
		$mail->SMTPOptions = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			)
		);

		/*
		===========
		IT&E Setup
		===========
		*/
		// $mail->Host       = "smtp.ite.net";
		// $mail->Port       = "25";
		// $mail->SMTPSecure = "none";
		// $mail->SMTPAuth   = false;
		/*
		===========
		*/
		/*
		=================
		Outlook 365 Setup
		=================
		*/
		$mail->Host = "smtp.office365.com";
		$mail->Port = "587";
		$mail->SMTPSecure = "tls";
		$mail->SMTPAuth = true;
		//Username to use for SMTP authentication
		// $mail->Username = "aldrich_delossantos@tanholdings.com";
		// $mail->Password = "@alddel04241985";
		// $mail->Username = "xerox@tanholdings.com";
		$mail->Username = "hr2admin@tanholdings.com";
		// $mail->Username = "thc-spn3\\thcsmtp";
		// $mail->Username = "thcsmtp@tanholdings.com";
		//Password to use for SMTP authentication
		$mail->Password = "@welcome123";
		// $mail->Password = "@xrx123";
		// $mail->SMTPDebug = 0;
		// $mail->SMTPDebug  = 3;
		// $mail->Debugoutput = function ($str, $level) {
		// 	echo "debug level $level; message: $str"; }; //$mail->Debugoutput = 'echo';
		$mail->IsHTML(true);
		/*
		=================
		*/
		/*
		=================
		Tanholdings Setup
		=================
		*/
		// $mail->Host       = "mail.tanholdings.com";
		// $mail->Port       = "587";
		// $mail->SMTPSecure = "none";
		// $mail->SMTPAuth   = true;
		// //Username to use for SMTP authentication
		// $mail->Username = "aldrich_delossantos@tanholdings.com";
		// // $mail->Username = "thc-spn3\\thcsmtp";
		// // $mail->Username = "thcsmtp@tanholdings.com";
		// //Password to use for SMTP authentication
		// $mail->Password = "@welcome123";
		/*
		=================
		*/
		/*
		=================
		GMAIL Setup
		=================
		*/
		// $mail->Host       = "smtp.gmail.com";
		// $mail->Port       = "587";
		// $mail->SMTPSecure = "none";
		// $mail->SMTPAuth   = true;
		// //Username to use for SMTP authentication
		// $mail->Username = "lnt.rms.helpdesk@gmail.com";
		// //Password to use for SMTP authentication
		// $mail->Password = "@welcome123";
		/*
		=================
		*/

		$mail->addReplyTo($SenderEmail, $SenderName);
		$mail->setFrom($SenderEmail, $SenderName);
		// $mail->addAddress($SendTo, $SendToName);

		if (!empty($SendTo)) {
			if (is_array($SendTo)) {
				foreach ($SendTo as $recipientTo) {
					// $to = $recipientTo["Email"];
					// if(!PHPMailer::validateAddress($to)) {
					//   throw new phpmailerAppException("Email address " . $to . " is invalid -- aborting!");
					// }
					// $mail->addAddress($recipientTo["Email"], $recipientTo["FirstName"] . ' ' . $recipientTo["LastName"]);

					if (isset($recipientTo["Email"])) {
						$to = $recipientTo["Email"];
						if (!PHPMailer::validateAddress($to)) {
							throw new phpmailerAppException("Email address " . $to . " is invalid -- aborting!");
						}
						$mail->addAddress($recipientTo["Email"], $recipientTo["FirstName"] . ' ' . $recipientTo["LastName"]);
					} else {
						if (!PHPMailer::validateAddress($recipientTo)) {
							throw new phpmailerAppException("Email address " . $recipientTo . " is invalid -- aborting!");
						}
						$mail->addAddress($recipientTo);
					}

				}
			} else {
				$to = $SendTo;
				if (!PHPMailer::validateAddress($to)) {
					throw new phpmailerAppException("Email address " . $to . " is invalid -- aborting!");
				}
				$mail->addAddress($SendTo, $SendToName);
			}
		}

		if (!empty($SendCC)) {
			if (is_array($SendCC)) {
				foreach ($SendCC as $recipientCC) {
					// $mail->addCC($recipientCC["Email"]);
					if (!empty($recipientCC)) {
						$mail->addCC($recipientCC);
						// echo $recipientCC."<br>";
					}
					// if (isset($recipientCC["Email"])&&!empty($recipientCC["Email"])) {
					// 	$mail->addCC($recipientCC["Email"]);
					// } else {
					// }			
				}
			} else {
				$mail->addCC($SendCC);
			}
		}
		if (!empty($SendBCC)) {
			if (is_array($SendBCC)) {
				foreach ($SendBCC as $recipientBCC) {
					// echo $bccer["Email"];
					// $mail->addBCC($recipientBCC["Email"]);
					if (isset($recipientBCC["Email"])) {
						$mail->addBCC($recipientBCC["Email"]);
					} else {
						if (!empty($recipientBCC))
							$mail->addBCC($recipientBCC);
					}
				}
			} else {
				$mail->addBCC($SendBCC);
			}
		}

		// attachments
		if (!empty($attachments)) {
			if (is_array($attachments)) {
				foreach ($attachments as $key => $attachment) {
					if (!empty($attachment)) {
						$mail->addAttachment($attachment['path'], $attachment['name'], $attachment['encoding'], $attachment['type']);
						// echo "<pre>[$key]";
						// print_r($attachment['path']);
						// echo "</pre><br>";
					}
				}
				// die();
			} else {
				$mail->addAttachment($attachments);
			}
		}

		$mail->Subject = $Subject;
		$body = <<<EOT
{$emailMessage}
EOT;
		$mail->WordWrap = 78;
		$mail->msgHTML($body, dirname(__FILE__), true); //Create message bodies and embed images
		// $mail->addAttachment('images/phpmailer_mini.png','phpmailer_mini.png');  // optional name
		// $mail->addAttachment('images/phpmailer.png', 'phpmailer.png');  // optional name


		$isEmailSent = 0;

		// Set the maximum number of retries
		$maxRetries = 3;
		$retryCount = 0;
		$retryDelay = 3; // in seconds

		// Attempt to send the email with retries
		do {
			try {

				// ADDED AUDIT TRAIL
				$file_content = gmdate("Y-m-d H:i:s", time()) . "\n";
				$file_content .= "To: " . ((is_array($SendTo)) ? implode("; ", $SendTo) : $SendTo) . "\n";
				$file_content .= "CC: " . ((is_array($SendCC)) ? implode("; ", $SendCC) : $SendCC) . "\n";
				$file_content .= "BCC: " . ((is_array($SendBCC)) ? implode("; ", $SendBCC) : $SendBCC) . "\n";
				$file_content .= "SUBJECT: " . $Subject . "\n";
				file_put_contents(LOGS_DIRECTORY . 'smtp-rms.log', $file_content, FILE_APPEND | LOCK_EX);

				$mail->SMTPDebug = 3;
				$mail->Debugoutput = function ($str, $level) {
					file_put_contents(LOGS_DIRECTORY . 'smtp-rms.log', gmdate('Y-m-d H:i:s') . "\t$level\t$str\n", FILE_APPEND | LOCK_EX);
				};

				$mail->send();
				$results_messages[] = "Message has been sent using SMTP";
				$isEmailSent = 1;

				break; // If successful, exit the loop

			} catch (phpmailerException $e) {
				// throw new phpmailerAppException('Unable to send to: ' . $to . ': ' . $e->getMessage());

				$file = 'php-mailer-logs-rms-' . gmdate("YmdHi", time()) . '.log';
				$file_content = gmdate("Y-m-d H:i:s", time()) . "\n";
				$file_content .= "To: " . ((is_array($SendTo)) ? implode("; ", $SendTo) : $SendTo) . "\n";
				$file_content .= "CC: " . ((is_array($SendCC)) ? implode("; ", $SendCC) : $SendCC) . "\n";
				$file_content .= "BCC: " . ((is_array($SendBCC)) ? implode("; ", $SendBCC) : $SendBCC) . "\n";
				$file_content .= "SUBJECT: " . $Subject . "\n";
				$file_content .= $e->getMessage()."\n";			

				file_put_contents(LOGS_DIRECTORY . $file, $file_content);

				$mail->Debugoutput = function ($str, $level) use ($file) {
					file_put_contents(LOGS_DIRECTORY . $file, gmdate('Y-m-d H:i:s') . "\t$level\t$str\n", FILE_APPEND | LOCK_EX);
				};

				// Increment the retry count
				$retryCount++;

				file_put_contents(LOGS_DIRECTORY . "smtp-rms.log", gmdate('Y-m-d H:i:s') . "\tTRYING TO RESEND... Retry count($retryCount)\n", FILE_APPEND | LOCK_EX);

				// // Clear all recipients and attachments for the next attempt
				// $mail->clearAddresses();
				// $mail->clearAttachments();

				// Add a delay before the next attempt (optional)
				sleep($retryDelay);

				// echo '<pre>'; print_r($results_messages); echo '</pre>';
				// die($e->getMessage());
			}
		} while ($retryCount < $maxRetries);

		// Check if the maximum number of retries is reached
		if ($retryCount === $maxRetries) {	
			$file_content = gmdate("Y-m-d H:i:s", time()) . "\n";		
			$file_content .= "Maximum number of retries reached. Email could not be sent.\n";
			file_put_contents(LOGS_DIRECTORY . 'smtp-rms.log', $file_content, FILE_APPEND | LOCK_EX);
		}
		
	} catch (phpmailerAppException $e) {
		$results_messages[] = $e->errorMessage();

		$file = 'php-mailer-logs-rms-' . gmdate("YmdHi", time()) . '.log';
		$file_content = gmdate("Y-m-d H:i:s", time()) . "\n";
		$file_content .= "To: " . ((is_array($SendTo)) ? implode("; ", $SendTo) : $SendTo) . "\n";
		$file_content .= "CC: " . ((is_array($SendCC)) ? implode("; ", $SendCC) : $SendCC) . "\n";
		$file_content .= "BCC: " . ((is_array($SendBCC)) ? implode("; ", $SendBCC) : $SendBCC) . "\n";
		$file_content .= "SUBJECT: " . $Subject . "\n";
		$file_content .= $e->errorMessage(). "\n";

		file_put_contents(LOGS_DIRECTORY . $file, $file_content);

		$mail->Debugoutput = function ($str, $level) use ($file) {
			file_put_contents(LOGS_DIRECTORY . $file, gmdate('Y-m-d H:i:s') . "\t$level\t$str\n", FILE_APPEND | LOCK_EX);
		};
		// echo '<pre>'; print_r($array); echo '</pre>';
		// die('end of error');
	}

	return $isEmailSent;
	/* Development Mode */
	// if (count($results_messages) > 0) {
	//   echo "<h2>Run results</h2>\n";
	//   echo "<ul>\n";
	// foreach ($results_messages as $result) {
	//   echo "<li>$result</li>\n";
	// }
	// echo "</ul>\n";
	// }
}
?>