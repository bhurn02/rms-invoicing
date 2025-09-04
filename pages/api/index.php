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
// echo __DIR__ . '/../utilities/system/functions.php';  // Check if the path is correct
// require_once(__DIR__ . '/../utilities/system/functions.php');
// require_once($_SERVER['APPL_PHYSICAL_PATH'] . 'utilities' . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'functions.php');
// echo $_SERVER['APPL_PHYSICAL_PATH'];
// exit;


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
		// echo 'YES';
        $sqlquery = "sp_u_Send_Invoice_Alert_List";
        // die($sqlquery);
        $results = mssql_resultset($sqlquery);
		$invalidEmails = 0;
		$invalidAttachments = 0;

		
		//access
		$sessUserID = "";
		$sessUserName = "";
		$sessCompanyCode = "";

		if (isset($_COOKIE['userid'])) {
			$sessUserID = $_COOKIE['userid'];
		}

		// echo $sessUserID;
		// if (trim($sessUserID) == "") {
		// 	echo "<script> parent.frames.location = \"" . "accessnotallowed.htm" .  "\";</script>";
		// 	exit(); }
		// else {
		// 	$menu_access = menuaccess($_GET["menu_id"],trim($sessUserID));
		// 	if ($menu_access==0) {
		// 		echo "<script> parent.frames[2].location = \"" . "accessnotallowed.htm" .  "\";</script>";
		// 		exit(); 
		// 	}
		// }
		// echo PHP_VERSION;

        foreach ($results as $entry) {            
			$email_add = trim($entry['email_add']);
            $pdf_file_name = trim(isset($entry['pdf_file_name']) ? $entry['pdf_file_name'] : '');
            // $attachment_file = INVOICE_DIRECTORY . $pdf_file_name . '.pdf';
			// $attachment_file = 'C:/System/rms/pages/utilities/pdf/' . $pdf_file_name . '.pdf';
			$attachment_file = $_SERVER['APPL_PHYSICAL_PATH'].'utilities/pdf/' . $pdf_file_name . '.pdf';
			// echo "Attachment file path: " . $attachment_file;
            $attachment_status = file_exists($attachment_file) ? "Valid" : "Invalid";
        
            if (empty($email_add)) {
                $invalidEmails++;
            }
            if (!file_exists($attachment_file)) {
                $invalidAttachments++;
            }

			if ($attachment_status === "Invalid") {
            $cn = cn();
			$server = $cn[1];
			$db = $cn[2];
			$username = $cn[3];
			$password = $cn[4];
			$report_path= $cn[5];
			$pdf_path= $cn[6];
			$pdf_link= $cn[7];
			
			$dtGenerated = strval(date("m/d/y H:i:s", time()));
			//$dtGenerated = "06/18/2013";
			$sqlquery="exec sp_rpt_Invoice_Proc '" . $entry["invoice_no"] . "','','',0,'" . $sessUserID . "','" . $dtGenerated . "'";	
			//echo $sqlquery;
			$process= mssql_resultset($sqlquery);	
			//- Variables - for your RPT and PDF 
			//echo "Print Report Test"; 
			$my_report = $report_path . "t_invoice.rpt"; // 
			//echo $my_report;
			//die();
			//rpt source file 
			// $pdf_file = "t_invoice" . str_replace("/","",date("m/d/y/H/i/s", time())) . ".pdf";
			// $my_pdf = $pdf_path . $pdf_file; // RPT export to pdf file 
			$my_pdf = $attachment_file;
			$my_pdf_open = $pdf_link . $attachment_file;
			//echo $pdf_file;
			//die();
			//-Create new COM object-depends on your Crystal Report version 
			//$ObjectFactory= new COM("CrystalRuntime.Application") or die ("Error on load"); // call COM port 
			$crapp= new COM("CrystalRuntime.Application") or die ("Error on load"); // call COM port 

			// try {
			// 	$crapp = new COM("CrystalRuntime.Application") or die ("Error on load");
			// 	$creport = $crapp->OpenReport($my_report, 1); // call rpt report
			// } catch (Exception $e) {
			// 	echo "Error with Crystal Reports COM: " . $e->getMessage();
			// 	// Optionally log the error to a file:
			// 	error_log("Crystal Reports COM error: " . $e->getMessage(), 3, "error_log.txt");
			// }

			//$crapp = $ObjectFactory-> CreateObject("CrystalRuntime.Application"); // create an instance for Crystal 
			$creport = $crapp->OpenReport($my_report, 1); // call rpt report 
			
			// to refresh data before 
			
			//- Set database logon info - must have 
			$creport->Database->Tables(1)->SetLogOnInfo($server, $db,$username, $password); 
			//- field prompt or else report will hang - to get through 
			$creport->EnableParameterPrompting = 0; 
			
			//- DiscardSavedData - to refresh then read records 
			$creport->DiscardSavedData; 
			$creport->ReadRecords(); 
		
			//------ Pass formula fields --------
			//$creport->FormulaFields->Item(1)->Text = ("'invoice_no'");
			$creport->ParameterFields(1)->AddCurrentValue ($entry["invoice_no"]);
			$creport->ParameterFields(2)->AddCurrentValue ("");
			$creport->ParameterFields(3)->AddCurrentValue ("");
			$creport->ParameterFields(4)->AddCurrentValue ($sessUserID);
			$creport->ParameterFields(5)->AddCurrentValue ($dtGenerated);
			//$creport->ParameterFields(2)->AddCurrentValue (2000);
		
			//export to PDF process 
			$creport->ExportOptions->DiskFileName=$my_pdf; //export to pdf 
			$creport->ExportOptions->PDFExportAllPages=true; 
			$creport->ExportOptions->DestinationType=1; // export to file 
			$creport->ExportOptions->FormatType=31; // PDF type 
			$creport->Export(false); 
			
			//------ Release the variables ------ 
			$creport = null; 
			$crapp = null; 
			//$ObjectFactory = null; 
			
			echo "<script type=\"text/javascript\">window.open (\"" . $my_pdf_open . "\");</script>";
			//end print
			$strMode = "RETRIEVE";
			// break;
			} else {
				$retStatus = 200;
        		$strResult = 'Skipped. File already exist.';
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

