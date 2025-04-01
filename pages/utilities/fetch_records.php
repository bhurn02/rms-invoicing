<?php
require_once("system".DIRECTORY_SEPARATOR."functions.php");

$dns = setup_dns(DB_HOST, DB_NAME);
$sqlquery = "EXEC sp_u_Send_Invoice_Alert_List";
$result = mssql_resultset($sqlquery, $dns);

// echo "<pre>";
// print_r($result);
// echo "</pre>";
// die();

$records = [];
$invalidEmails = 0;
$invalidAttachments = 0;

foreach ($result as $entry) {
    $email_add = trim($entry['email_add']);
    $attachment_file = ATTACHMENT_DIRECTORY . $entry['pdf_file_name'] . '.pdf';
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
    ];
}

echo json_encode([
    'totalRecords' => count($records),
    'invalidEmails' => $invalidEmails,
    'invalidAttachments' => $invalidAttachments,
    'records' => $records,
]);
