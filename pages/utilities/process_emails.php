<?php
require_once("system/functions.php");
require_once("system/mail_smtp.php");

header('Content-Type: application/json');

// ✅ Connect to DB
$dns = setup_dns(DB_HOST, DB_NAME);

// ✅ Read flags and batch data
$read_only = isset($_POST['read_only']) ? intval($_POST['read_only']) : 1;
$batchData = isset($_POST['batch']) ? json_decode($_POST['batch'], true) : [];

// ✅ Setup log file
$logPath = __DIR__ . '/logs/invoice_send_' . date('Ymd') . '.log';
if (!file_exists(dirname($logPath))) {
    mkdir(dirname($logPath), 0777, true);
}

// ✅ Logging helper with safe characters
function log_to_file($message, $path) {
    $clean = preg_replace('/[^\x20-\x7E]/', '', $message);
    $clean = str_replace(["\r", "\n", "\t"], ' ', $clean);
    $clean = strip_tags($clean);
    $clean = preg_replace('/[<>:"\/\\\\|?*]/', '', $clean);
    $clean = trim($clean);
    $timestamped = "[" . date('Y-m-d H:i:s') . "] $clean\n";

    // Prepend by reading + writing
    if (file_exists($path)) {
        $existing = file_get_contents($path);
        file_put_contents($path, $timestamped . $existing);
    } else {
        file_put_contents($path, $timestamped);
    }
}


// ✅ Process each invoice record
$successCount = 0;
$totalCount = count($batchData);
$hasFailures = false;

foreach ($batchData as $entry) {
    $invoice_no   = $entry['invoice_no'];
    $tenant_code  = $entry['client_code'];
    $tenant_name  = $entry['tenant_name'];
    $subject      = $entry['report_title'] ?? 'Invoice Notice';
    $message      = "Please see attached invoice.";
    $email_raw    = trim($entry['email_address']);
    $filepath     = trim($entry['attachment_file'] ?? '');

    // ✅ Parse and validate emails
    $emails = array_filter(array_map('trim', explode(";", $email_raw)), function ($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    });

    if (empty($emails)) {
        log_to_file("[SKIPPED] Invoice $invoice_no - missing or invalid email", $logPath);
        $hasFailures = true;
        continue;
    }

    if (empty($filepath) || !file_exists($filepath)) {
        log_to_file("[SKIPPED] Invoice $invoice_no - file not found: $filepath", $logPath);
        $hasFailures = true;
        continue;
    }

    // ✅ Prepare attachment
    $filename = basename($filepath);
    $attachments = [[
        'path' => $filepath,
        'name' => $filename,
        'encoding' => 'base64',
        'typeencoding' => 'application/octet-stream'
    ]];

    // ✅ Recipients
    $cc = ['rms_sysadmin@tanholdings.com'];
    $bcc = ['aldrich_delossantos@tanholdings.com', 'nat_angeles@tanholdings.com'];

    if ($read_only == 0) {
        log_to_file("[SENDING] Invoice $invoice_no to " . implode(", ", $emails), $logPath);

        $emailSent = sendSMTPMail(APP_MAIL_SENDER, APP_NAME, $emails, $tenant_name, $cc, $bcc, $subject, $message, $attachments);

        if ($emailSent) {
            $emailList = implode(";", $emails);

            // ✅ Update invoice status
            $sql1 = "EXEC sp_u_Send_Invoice_Alert_Update @invoice_no='$invoice_no', @email_addr='$emailList'";
            mssql_resultset($sql1, $dns);

            // ✅ Log to alert table
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
            log_to_file("[FAILED] Invoice $invoice_no - email send failed", $logPath);
            $hasFailures = true;
        }

    } else {
        log_to_file("[READ-ONLY] Simulated send for invoice $invoice_no", $logPath);
        $successCount++;
    }
}

// ✅ Final response
echo json_encode([
    'status' => $hasFailures ? 'partial' : 'success',
    'processed' => $successCount,
    'total' => $totalCount
]);
