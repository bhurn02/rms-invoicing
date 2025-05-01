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
        $sqlquery = "SELECT 
                        ROW_NUMBER() OVER (ORDER BY (SELECT NULL)) AS row_id
                        ,CAST(CASE WHEN folio_number='N/A' THEN '' ELSE folio_number END AS INT) FolioNo
                        ,F.folio_invoice_number
                        -- ,F.folio_date
                        ,CONVERT(VARCHAR(10),CAST(F.folio_date AS datetime),126) AS folio_date
                        ,F.folio_guest_name
                        ,CAST(F.folio_charges AS NUMERIC(18,2)) AS folio_charges
                        ,CAST(F.folio_tax AS NUMERIC(18,2)) AS folio_tax
                        ,ABS(CAST(folio_credit AS NUMERIC(18,2))) AS folio_total
                        ,S.status_color 
                        ,F.folio_status
                    FROM UploadedFolios F 
                    LEFT JOIN Statuses S ON S.status_for='reserve' AND S.status_name=F.folio_status 
                    WHERE LOWER(folio_status) NOT IN('void','cancel','no show','due out','pos')
                        AND CONVERT(datetime,folio_date) BETWEEN CONVERT(datetime,'".$return['start_date']."') AND CONVERT(datetime,'".$return['end_date']."') ORDER BY FolioNo";
        // die($sqlquery);
        $results = mssql_resultset($sqlquery);
        
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

