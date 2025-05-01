<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true ");
header("Access-Control-Allow-Methods: OPTIONS, GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Depth, User-Agent, X-File-Size, X-Requested-With, If-Modified-Since, X-File-Name, Cache-Control");

// echo "<pre>";
// print_r($_SERVER);
// print_r($_POST);
// echo "</pre>";
// die();

require_once($_SERVER['APPL_PHYSICAL_PATH']."functions.php");

if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
    switch ($_GET['action']) {        

        case 'batchgenerateinvoicepdf':
            BatchGenerateInvoicePdf();
            break;
        
        
        default:
            # code...
            break;
    } 
}
else if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	switch ($_POST['action']) {                

        case 'batchgenerateinvoicepdf':
            BatchGenerateInvoicePdf();
            break;        
        
        default:
            # code...
            break;
    } 
} else {
	http_response_code(405);
}

// function eBulletinAdLists() {
//     echo construct_slides('slides');
// }

function BatchGenerateInvoicePdf(){		
	$hasError = 0;
    $return = $_POST;
    $retStatus = 200;
    $strResult="success";
    
    $results=array();		    
    // sleep(5);
    /* debuggers side */		
    try {
        $sqlquery = "sp_u_Send_Invoice_Alert_List";
        // die($sqlquery);
        $results = mssql_resultset($sqlquery);

        foreach ($results as $entry) {            
            $pdf_file_name = trim($entry['pdf_file_name'] ?? '');
            $attachment_file = INVOICE_DIRECTORY . $pdf_file_name . '.pdf';
        
            $attachment_status = file_exists($attachment_file) ? "Valid" : "Invalid";
        
            if (empty($email_add)) {
                $invalidEmails++;
            }
            if (!file_exists($attachment_file)) {
                $invalidAttachments++;
            }
        }
        
    }
    catch(Exception $e) {   
        $hasError = 1;
        $retStatus = 500;
        $strResult = 'Something went wrong on our API while retrieving Folio List. Please contact your administrator.';
    }

    /* Create a JSON File */		
    // $filename = "materials_summary_" . str_replace("/","",date("m/d/y/H/i/s", time()));
    // fnCreateJSONFile($results,$filename);

    $return["status"] = json_encode($retStatus);	  		  	
    $return["response"] = json_encode($strResult);
    $return["json"] = json_encode($results);
    echo json_encode($return);    
}

