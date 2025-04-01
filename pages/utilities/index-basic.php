<?php
/*
Author:     Aldrich Delos Santos
Email:      bhurndls@yahoo.com
Date:       2023-06-15

This sample will send an email notification to the approvers sharing the same employee listings.
*/

require_once("system".DIRECTORY_SEPARATOR."functions.php");
require_once("system".DIRECTORY_SEPARATOR."mail_smtp.php");

$server = DB_HOST;
$db = DB_NAME;

$dns = setup_dns(DB_HOST,DB_NAME);

$read_only = (isset($_GET['read_only'])) ? $_GET['read_only'] : 1;

// Start time
$startTime = microtime(true);

// Print the current date and time
echo "<strong>";
echo "Start time: " . date("Y-m-d H:i:s", $startTime) . "<br>";
echo "</strong><hr>";

/* FOR TESTING
// Specify the path to the JSON file
$jsonFile = $_SERVER['APPL_PHYSICAL_PATH']."etlar".DIRECTORY_SEPARATOR."data".DIRECTORY_SEPARATOR."clockout-reminder-1.json";

// Read the file contents
$jsonString = file_get_contents($jsonFile);

// // Convert JSON data to array
$data = json_decode($jsonString, true);
END TESTING */

// SQL query to fetch data
// $sqlquery = "EXEC sp_t_ClockoutReminder @CompanyPk=1021, @Interval=30";
// $sqlquery = "EXEC sp_t_ClockoutReminder @CompanyPk=1021, @ActualDate='06/15/2023', @Interval=30, @CurrDate='2023-06-15 00:31:00'"; // PRESELECTED SCHEDULE
$sqlquery = "EXEC sp_u_Send_Invoice_Alert_List"; // PRESELECTED SCHEDULE
$result = mssql_resultset($sqlquery,$dns);



// echo "JSON Data: <br>";
// echo "<pre>";
// print_r($data);
// echo "</pre><br><hr>";
echo "record(s) found (".count($result).")<br><br>";
// echo "<pre>";
// print_r($result);
// echo "</pre><br><hr>";
// die();
// sleep(5);

$invalid_file_ctr = 0;
$invalid_email_ctr = 0;

// foreach ($data as $entry) {
foreach ($result as $entry) {

    $invoice_no = $entry['invoice_no'];
    $tenant_code = $entry['client_code'];

    // $email_add = explode(";",$entry['email_add']);

    // converts value to array for multiple email addresses and also remove whitespaces
    $email_add = array_map('trim', explode(";",$entry['email_add'])); 
    
    // $sendTo = 'aldrich_delossantos@tanholdings.com';
    $tenant_name = $entry['tenant_name'];    
    $subject = $entry['report_title'];
    $message = "Monthly Invoice";    
    $CCTo = array(
        'rms_sysadmin@tanholdings.com'
    );    
    $BccTo = array(        
        'aldrich_delossantos@tanholdings.com'
        ,'nat_angeles@tanholdings.com'
    );    
    $filename = $entry['pdf_file_name'].'.pdf';
    $attachment_file = ATTACHMENT_DIRECTORY.$filename;

    $attachments = [];    
    $attachments[] = [
        'path' => $attachment_file,
        // 'path' => createTemporaryICSFile($icsContent),
        'name' => $filename,
        'encoding' => 'base64',
        'typeencoding' => 'application/octet-stream' // MIME type for PDF
        // 'typeencoding' => 'text/calendar' // MIME type for iCalendar
    ];

    echo "<pre>";
    print_r($attachments);
    echo "</pre><br>";

    if (empty($entry['email_add'])) {
        echo "No email address found. Email not sent.";
        $invalid_email_ctr++;        
    }

    // Check if the file exists before sending the email
    if (file_exists($attachment_file) && !empty($entry['email_add'])) {
        // Code to send email with attachment
        echo "Attachment file exists.<br>";
        // Output confirmation message
        echo "Email will be sent to ".$tenant_name." (".((is_array($email_add))?implode(";",$email_add):$email_add).")"."<br>";


        if ($read_only) {
            echo "Email not sent. READ-ONLY mode is enabled.<br>";            
        } else {
            // // Send the email
            $emailSent = sendSMTPMail(APP_MAIL_SENDER, APP_NAME, $email_add, $tenant_name, $CCTo, $BccTo, $subject, $message, $attachments);

            if ($emailSent) {        
                // echo 'Email sent successfully!<br>';
                echo '<h4 style="color:#47c36a;">Email sent successfully!</h4><br>';

                $sqlquery = "EXEC sp_u_Send_Invoice_Alert_Update @invoice_no='$invoice_no', @email_addr='".((is_array($email_add))?implode(";",$email_add):$email_add)."'";
                // $sqlquery = "EXEC sp_u_Send_Invoice_Alert_Update @invoice_no='$invoice_no', @email_addr='$email_add'";
                $result = mssql_resultset($sqlquery,$dns);
                
                
                $sqlquery = "EXEC sp_s_EmailAlert_Log @eal_date_time='" .gmdate('Y-m-d H:i:s'). "', @eal_alert_type='INVOICE-$invoice_no', @eal_notice_no=0, @eal_as_of='',@eal_sap_code='', @eal_detail_id=0, @eal_tenant_code'$tenant_code', @eal_tenant_name='$tenant_name', @eal_email_add='".((is_array($email_add))?implode(";",$email_add):$email_add)."', @eal_attachment='$attachment_file'";
                // $sqlquery = "EXEC sp_s_EmailAlert_Log @eal_date_time='" .gmdate('Y-m-d H:i:s'). "', @eal_alert_type='INVOICE-$invoice_no', @eal_notice_no=0, @eal_as_of='',@eal_sap_code='', @eal_detail_id=0, @eal_tenant_code'$tenant_code', @eal_tenant_name='$tenant_name', @eal_email_add='$email_add', @eal_attachment='$attachment_file'";
                $result = mssql_resultset($sqlquery,$dns);
            } else {
                echo '<h4 style="color:red;">Error encountered.</h4><br>';
                // echo 'Error: ' . $mail->ErrorInfo;
            }
        }
    } else {
        echo "Attachment file does not exist. Email not sent.";
        $invalid_file_ctr++;
    }
    
    

    // sleep(3);
}
// end of loop $result
echo "<hr><br>";
echo "Invalid email address count: ".$invalid_email_ctr."<br>";
echo "Invalid file count: ".$invalid_file_ctr."<br><br>";

// End time
$endTime = microtime(true);

// Print the end date and time
echo "<hr><br><strong>";
echo "End time: " . date("Y-m-d H:i:s", $endTime) . "<br>";

// Calculate and print the duration
$duration = $endTime - $startTime;

// Convert the duration to hours, minutes, seconds, and milliseconds
$hours = floor($duration / 3600);
$minutes = floor(($duration % 3600) / 60);
$seconds = floor($duration % 60);
$milliseconds = round(($duration - floor($duration)) * 1000);

// Format the duration as hh:mm:ss.sss
$formattedDuration = sprintf('%02d:%02d:%02d.%03d', $hours, $minutes, $seconds, $milliseconds);

echo "Duration: " . $formattedDuration . " seconds<br>";
echo "</strong>";
