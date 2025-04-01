<?php
/*
Author:  Aldrich Delos Santos
Date:  October 13, 2015
Description: PHPMailer SMTP Mail Sender
*/

date_default_timezone_set("Pacific/Guam");

require_once('PHPMailer/PHPMailerAutoload.php');


// $results_messages = array();
 
$mail = new PHPMailer(true);
// $mail->CharSet = 'utf-8';
// ini_set('default_charset', 'UTF-8');
 
class phpmailerAppException extends phpmailerException {}


function sendSMTPMail($SenderEmail,$SenderName,$SendTo,$SendToName,$SendCC,$SendBCC,$Subject,$emailMessage,$attachments=array()){ 

// $attachments=($attachments)?$attachments:array();
	// print_r($attachments);
	// die();

$results_messages = array();
 
$mail = new PHPMailer(true);
$mail->CharSet = 'utf-8';
ini_set('default_charset', 'UTF-8');
try {
// $to = $SendTo;
// if(!PHPMailer::validateAddress($to)) {
//   throw new phpmailerAppException("Email address " . $to . " is invalid -- aborting!");
// }
$mail->isSMTP();
// $mail->SMTPDebug  = 2; /* Development Mode */
//Ask for HTML-friendly debug output
// $mail->Debugoutput = 'html';

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
$mail->Host       = "smtp.office365.com";
$mail->Port       = "587";
$mail->SMTPSecure = "tls";
$mail->SMTPAuth   = true;
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
$mail->SMTPDebug  = 0;
// $mail->SMTPDebug  = 3;
$mail->Debugoutput = function($str, $level) {echo "debug level $level; message: $str";}; //$mail->Debugoutput = 'echo';
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
// $mail->Username = "lnt.etlar.helpdesk@gmail.com";
// //Password to use for SMTP authentication
// $mail->Password = "@welcome123";
/*
=================
*/

$mail->addReplyTo($SenderEmail, $SenderName);
$mail->setFrom($SenderEmail, $SenderName);
// $mail->addAddress($SendTo, $SendToName);

if (!empty($SendTo)){
	if (is_array($SendTo)){
		foreach($SendTo as $recipientTo){	       
			// $to = $recipientTo["Email"];
			// if(!PHPMailer::validateAddress($to)) {
			//   throw new phpmailerAppException("Email address " . $to . " is invalid -- aborting!");
			// }
			// $mail->addAddress($recipientTo["Email"], $recipientTo["FirstName"] . ' ' . $recipientTo["LastName"]);

			if (isset($recipientTo["Email"])) {
				$to = $recipientTo["Email"];				
				if(!PHPMailer::validateAddress($to)) {
				  throw new phpmailerAppException("Email address " . $to . " is invalid -- aborting!");
				}
				$mail->addAddress($recipientTo["Email"], $recipientTo["FirstName"] . ' ' . $recipientTo["LastName"]);
			} else {				
				if(!PHPMailer::validateAddress($recipientTo)) {
				  throw new phpmailerAppException("Email address " . $recipientTo . " is invalid -- aborting!");
				}
				$mail->addAddress($recipientTo);
			}
			
	    }
	} else {
		$to = $SendTo;
		if(!PHPMailer::validateAddress($to)) {
		  throw new phpmailerAppException("Email address " . $to . " is invalid -- aborting!");
		}
		$mail->addAddress($SendTo, $SendToName);
	}	
}

if (!empty($SendCC)){
	if (is_array($SendCC)){		
		foreach($SendCC as $recipientCC){
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
if (!empty($SendBCC)){
	if (is_array($SendBCC)){
	    foreach($SendBCC as $recipientBCC){
	       // echo $bccer["Email"];
			// $mail->addBCC($recipientBCC["Email"]);
			if (isset($recipientBCC["Email"])) {
				$mail->addBCC($recipientBCC["Email"]);
			} else {
				if (!empty($recipientBCC))
					$mail->addCC($recipientBCC);				
			}
	    }
    } else {
    	$mail->addBCC($SendBCC);
    }
}

// attachments
if (!empty($attachments)){
	if (is_array($attachments)){
		foreach($attachments as $attachment){	
			if (!empty($attachment)) {
				$mail->addAttachment($attachment);
				// echo $attachment."<br>";
			}					
	    }
	    // die();
	} else {
		$mail->addAttachment($attachments);
	}	
}       

$mail->Subject  = $Subject;
$body = <<<EOT
{$emailMessage}
EOT;
$mail->WordWrap = 78;
$mail->msgHTML($body, dirname(__FILE__), true); //Create message bodies and embed images
// $mail->addAttachment('images/phpmailer_mini.png','phpmailer_mini.png');  // optional name
// $mail->addAttachment('images/phpmailer.png', 'phpmailer.png');  // optional name
 

$isEmailSent = 0;
try {
  $mail->send();
  $results_messages[] = "Message has been sent using SMTP"; 
  $isEmailSent = 1;
}
catch (phpmailerException $e) {
  throw new phpmailerAppException('Unable to send to: ' . $to. ': '.$e->getMessage());  
  // echo '<pre>'; print_r($results_messages); echo '</pre>';
  // die($e->getMessage());
}
}
catch (phpmailerAppException $e) {
  $results_messages[] = $e->errorMessage();
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