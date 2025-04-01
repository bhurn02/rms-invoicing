<?php
require_once("system/functions.php");

// ✅ Setup DB connection
$dns = setup_dns(DB_HOST, DB_NAME);

// ✅ Execute stored procedure
$sqlquery = "EXEC sp_u_Send_Invoice_Alert_List";
$result = mssql_resultset($sqlquery, $dns);

// ✅ Prepare response
$records = [];
$invalidEmails = 0;
$invalidAttachments = 0;

foreach ($result as $entry) {
    $email_add = trim($entry['email_add']);
    $pdf_file_name = trim($entry['pdf_file_name'] ?? '');
    $attachment_file = ATTACHMENT_DIRECTORY . $pdf_file_name . '.pdf';

    $attachment_status = file_exists($attachment_file) ? "Valid" : "Invalid";

    if (empty($email_add)) {
        $invalidEmails++;
    }
    if (!file_exists($attachment_file)) {
        $invalidAttachments++;
    }

    $records[] = [
        'invoice_no' => $entry['invoice_no'],
        'client_code' => $entry['client_code'],
        'tenant_name' => $entry['tenant_name'],
        'email_address' => $email_add ?: "No Email",
        'attachment_status' => $attachment_status,
        'attachment_file' => $attachment_file, // ✅ included
        'pdf_file_name' => $pdf_file_name,
        'report_title' => $entry['report_title'] ?? '',
    ];
}

// ✅ Return JSON
echo json_encode([
    'totalRecords' => count($records),
    'invalidEmails' => $invalidEmails,
    'invalidAttachments' => $invalidAttachments,
    'records' => $records,
]);
