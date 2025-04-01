<?php 
include("functions.php");
//access
$sessUserID = "";
$sessUserName = "";
$sessCompanyCode = "";

if (isset($_COOKIE['userid'])) {
	$sessUserID = $_COOKIE['userid'];
	$sessUserName = $_COOKIE['username'];
	$sessCompanyCode = $_COOKIE['company_code'];
}
if (trim($sessUserID) == "") {
	echo "<script> parent.frames.location = \"" . "accessnotallowed.htm" .  "\";</script>";
	exit(); }
else {
	$menu_access = menuaccess($_GET["menu_id"],trim($sessUserID));
	if ($menu_access==0) {
		echo "<script> parent.frames.location = \"" . "accessnotallowed.htm" .  "\";</script>";
		exit(); 
	}
}

//end access
$sqlconnect = connection();
$strIPAddr = $_SERVER["REMOTE_ADDR"];

$cn = cn();
$server = $cn[1];
$db = $cn[2];
$username = $cn[3];
$password = $cn[4];
$report_path= $cn[5];
$pdf_path= $cn[6];
$pdf_link= $cn[7];

//- Variables - for your RPT and PDF 
//echo "Print Report Test"; 
$my_report = $report_path . "t_tenant_reading.rpt"; // 
//echo $my_report;
//die();
//rpt source file 
$pdf_file = "t_tenant_reading" . str_replace("/","",date("m/d/y/H/i/s", time())) . ".pdf";
$my_pdf = $pdf_path . $pdf_file; // RPT export to pdf file 
$my_pdf_open = $pdf_link . $pdf_file;
//echo $pdf_file;
//die();
//-Create new COM object-depends on your Crystal Report version 
//$ObjectFactory= new COM("CrystalRuntime.Application") or die ("Error on load"); // call COM port 
$crapp= new COM("CrystalRuntime.Application") or die ("Error on load"); // call COM port 
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

echo "<script type=\"text/javascript\">window.location= (\"" . $my_pdf_open . "\");</script>"
?>
