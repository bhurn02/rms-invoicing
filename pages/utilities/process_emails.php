<?php
require_once("system/functions.php");
require_once("system/mail_smtp.php");

header('Content-Type: application/json');

$dns = setup_dns(DB_HOST, DB_NAME);

$read_only = isset($_POST['read_only']) ? intval($_POST['read_only']) : 1;
$batchData = isset($_POST['batch']) ? json_decode($_POST['batch'], true) : [];

$logPath = __DIR__ . '/logs/invoice_send_' . date('Ymd') . '.log';
if (!file_exists(dirname($logPath))) {
    mkdir(dirname($logPath), 0777, true);
}

// Logging helper
function log_to_file($message, $path) {
    $clean = preg_replace('/[^\x20-\x7E]/', '', $message);
    $clean = str_replace(["\r", "\n", "\t"], ' ', $clean);
    $clean = strip_tags($clean);
    $clean = preg_replace('/[<>:"\/\\\\|?*]/', '', $clean);
    $clean = trim($clean);
    $timestamped = "[" . date('Y-m-d H:i:s') . "] $clean\n";

    if (file_exists($path)) {
        $existing = file_get_contents($path);
        file_put_contents($path, $timestamped . $existing);
    } else {
        file_put_contents($path, $timestamped);
    }
}

$successCount = 0;
$totalCount = count($batchData);
$errors = [];
$hasFailures = false;

foreach ($batchData as $entry) {
    $invoice_no   = $entry['invoice_no'];
    $tenant_code  = $entry['client_code'];
    $tenant_name  = $entry['tenant_name'];
    $subject      = $entry['report_title'] ?? 'Invoice Notice';
    $message      = "Please see attached invoice.";
    $email_raw    = trim($entry['email_address']);
    $filepath     = trim($entry['attachment_file'] ?? '');

    $original_email = $email_raw;
    $emails = extract_valid_emails($email_raw);

    if (empty($emails)) {
        $msg = "Invoice $invoice_no: Missing or invalid email address.";
        $errors[] = $msg;
        log_to_file("[SKIPPED] $msg", $logPath);
        $hasFailures = true;
        continue;
    }

    if ($email_raw !== implode("; ", $emails)) {
        $msg = "Invoice $invoice_no: Raw email partially sanitized. Original: [$original_email] → Sanitized: [" . implode("; ", $emails) . "]";
        $errors[] = $msg;
        log_to_file("[NOTICE] $msg", $logPath);
    }

    if (empty($filepath) || !file_exists($filepath)) {
        $msg = "Invoice $invoice_no: Attachment file not found at $filepath";
        $errors[] = $msg;
        log_to_file("[SKIPPED] $msg", $logPath);
        $hasFailures = true;
        continue;
    }

    $filename = basename($filepath);
    $attachments = [[
        'path' => $filepath,
        'name' => $filename,
        'encoding' => 'base64',
        'typeencoding' => 'application/octet-stream'
    ]];

    $cc = ['rms_sysadmin@tanholdings.com'];
    $bcc = ['aldrich_delossantos@tanholdings.com', 'nat_angeles@tanholdings.com'];
    
    // Add system BCC addresses from config
    if (defined('APP_MAIL_BCC') && is_array(APP_MAIL_BCC)) {
        $bcc = array_merge($bcc, APP_MAIL_BCC);
    }

    if ($read_only == 0) {
        log_to_file("[SENDING] Invoice $invoice_no to " . implode(", ", $emails), $logPath);

        $emailSent = sendSMTPMail(APP_MAIL_SENDER, APP_NAME, $emails, $tenant_name, $cc, $bcc, $subject, $message, $attachments);

        if ($emailSent) {
            $emailList = implode(";", $emails);

            $sql1 = "EXEC sp_u_Send_Invoice_Alert_Update @invoice_no='$invoice_no', @email_addr='$emailList'";
            mssql_resultset($sql1, $dns);

            $sql2 = "EXEC sp_s_EmailAlert_Log 
                @eal_date_time='" . gmdate('Y-m-d H:i:s') . "', 
                @eal_alert_type='INVOICE-$invoice_no',
                @eal_notice_no=0, 
                @eal_as_of='', 
                @eal_sap_code='', 
                @eal_detail_id=0, 
                @eal_tenant_code='$tenant_code',
                @eal_tenant_name='$tenant_name',
                @eal_email_add='$emailList',
                @eal_attachment='$filepath'";
            mssql_resultset($sql2, $dns);

            log_to_file("[SUCCESS] Invoice $invoice_no sent to $emailList", $logPath);
            $successCount++;
        } else {
            $msg = "Invoice $invoice_no: Email send failed.";
            $errors[] = $msg;
            log_to_file("[FAILED] $msg", $logPath);
            $hasFailures = true;
        }

    } else {
        log_to_file("[READ-ONLY] Simulated send for invoice $invoice_no", $logPath);
        $successCount++;
    }
}

if ($read_only && $totalCount > 0) {
    $delay = rand(1, 5);
    sleep($delay);
    log_to_file("[READ-ONLY] Batch delay of {$delay} second(s) applied.", $logPath);
}

echo json_encode([
    'status' => $hasFailures ? 'partial' : 'success',
    'processed' => $successCount,
    'total' => $totalCount,
    'errors' => $errors
]);
