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

$strMode = $_POST["hidMode"];
$dtFrom = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
// $dtTo = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	

if ($strMode != "") {
	$dtCreated = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
	$uid = $sessUserID;
	$i=0;
	while ($i <= $_POST["hidRowCtr"]) {		
		$sqlquery="exec sp_rpt_Occupancy_AsOf_Log '" . $_POST["DPC_txtDateFrom"] . "','" . $_POST["hidCode" . $i] . "','" . $_POST["txtRemarks" . $i] . "','" . $dtCreated . "','" . $uid . "'";	
		$process=odbc_exec($sqlconnect, $sqlquery);		
		// echo $sqlquery;die();
		$i++;
	}
}

if ($strMode == "GENERATE") {
	//$dtCreated = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
	$uid = $sessUserID;
	$sqlquery="exec sp_rpt_Occupancy_AsOf '" . $_POST["DPC_txtDateFrom"] . "','" . $dtCreated . "','" . $uid . "'";	
	$process=odbc_exec($sqlconnect, $sqlquery);			
	
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
	$my_report = $report_path . "t_occupancy.rpt"; // 
	//echo $my_report;
	//die();
	//rpt source file 
	$pdf_file = "t_occupancy_asof" . str_replace("/","",date("m/d/y/H/i/s", time())) . ".xls";
	$my_pdf = $pdf_path . $pdf_file; // RPT export to pdf file 
	$my_pdf_open = $pdf_link . $pdf_file;
	//echo $my_pdf_open;
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

	//------ Pass formula fields --------
	//$creport->FormulaFields->Item(1)->Text = ("'invoice_no'");
	
	$creport->ParameterFields(1)->AddCurrentValue ($_POST["DPC_txtDateFrom"]);
	//$creport->ParameterFields(1)->AddCurrentValue ("10/01/2010");
	// $creport->ParameterFields(2)->AddCurrentValue ($_POST["DPC_txtDateTo"]);
	//$creport->ParameterFields(2)->AddCurrentValue ("11/01/2010");
	$creport->ParameterFields(2)->AddCurrentValue ((string)$dtCreated);
	//$creport->ParameterFields(3)->AddCurrentValue ("10/11/2010");
	$creport->ParameterFields(3)->AddCurrentValue ($uid);
	//$creport->ParameterFields(4)->AddCurrentValue ("res");
	//$creport->ParameterFields(2)->AddCurrentValue (2000);

	//export to PDF process 
	$creport->ExportOptions->DiskFileName=$my_pdf; //export to pdf 
	$creport->ExportOptions->PDFExportAllPages=true; 
	$creport->ExportOptions->DestinationType=1; // export to file 
	$creport->ExportOptions->FormatType=30; // PDF type 
	$creport->Export(false); 
	
	//------ Release the variables ------ 
	$creport = null; 
	$crapp = null; 
	//$ObjectFactory = null; 
	
	echo "<script type=\"text/javascript\">window.open (\"" . $my_pdf_open . "\");</script>";
	//	die();
	$dtFrom = date($_POST["DPC_txtDateFrom"]);
	// $dtTo = date($_POST["DPC_txtDateTo"]);
}
else if ($strMode == "PRINT") {
	//$dtCreated = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
	$uid = $sessUserID;
	$sqlquery="exec sp_rpt_Occupancy_AsOf '" . $_POST["DPC_txtDateFrom"] . "','" . $dtCreated . "','" . $uid . "'";	
	$process=odbc_exec($sqlconnect, $sqlquery);			
	
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
	$my_report = $report_path . "t_occupancy_asof.rpt"; // 
	//echo $my_report;
	//die();
	//rpt source file 
	$pdf_file = "t_occupancy_asof" . str_replace("/","",date("m/d/y/H/i/s", time())) . ".pdf";
	$my_pdf = $pdf_path . $pdf_file; // RPT export to pdf file 
	$my_pdf_open = $pdf_link . $pdf_file;
	//echo $my_pdf_open;
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

	//------ Pass formula fields --------
	//$creport->FormulaFields->Item(1)->Text = ("'invoice_no'");
	
	$creport->ParameterFields(1)->AddCurrentValue ($_POST["DPC_txtDateFrom"]);
	//$creport->ParameterFields(1)->AddCurrentValue ("10/01/2010");
	// $creport->ParameterFields(2)->AddCurrentValue ($_POST["DPC_txtDateTo"]);
	//$creport->ParameterFields(2)->AddCurrentValue ("11/01/2010");
	$creport->ParameterFields(2)->AddCurrentValue ((string)$dtCreated);
	//$creport->ParameterFields(3)->AddCurrentValue ("10/11/2010");
	$creport->ParameterFields(3)->AddCurrentValue ($uid);
	//$creport->ParameterFields(4)->AddCurrentValue ("res");
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
	$dtFrom = date($_POST["DPC_txtDateFrom"]);
	// $dtTo = date($_POST["DPC_txtDateTo"]);
}

$sqlqueryList = "SELECT real_property_code,real_property_name,isnull((select top 1 remarks from rg_occupancy_asof_log 
	where real_property_code = a.real_property_code order by date_generated desc),'') as remarks FROM m_real_property a 
	where upper(ltrim(rtrim(isnull(space_type,'A')))) = 'W'  or real_property_code = 'TSL'
	order by real_property_name";	
//$sqlqueryList = "SELECT a.*,'a' as remarks  FROM m_real_property a order by real_property_name";	
$processList=odbc_exec($sqlconnect, $sqlqueryList);
//echo $sqlqueryList;
?>
<html> 
<head> 
<title>OCCUPANCY REPORT (AS OF)</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
<script type="text/javascript" src="library/datepickercontrol/datepickercontrol.js"></script>
<link type="text/css" rel="stylesheet" href="library/datepickercontrol/datepickercontrol_green.css">
</head> 
<body style="margin:'0';background-color: #F3F5B4;">
<form name="frmOccupancyAsOf" id="frmOccupancyAsOf" method="post">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">MANAGEMENT >> OCCUPANCY REPORT (AS OF)
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			 </a></li>	
			  <li class="li_nc"><a href="#" onClick="javascript:cmdPrint_OnClick()">|&nbsp;&nbsp;&nbsp;&nbsp;Print&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <li class="li_nc"><a href="#" onClick="javascript:cmdGenerate_OnClick()">Generate Excel File&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <li class="li_nc"><a href="#" onClick="javascript:window.close();">Close&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
		</ul>
	</div>
	<table>
		<tr>
			<td width="10"></td>
			<td>		
				<table>									
					<tr>
						<td class="fieldname_right">AS OF DATE (mm/dd/yyyy):</td>
						<td width="20"></td>
						<td><input type=text name="DPC_txtDateFrom" id="DPC_txtDateFrom" class="values" size="20" maxlength="10" value="<?php echo $dtFrom;?>">										
						</td>
					</tr>					
					<!-- <tr>
						<td class="fieldname_right">TO (mm/dd/yyyy):</td>
						<td width="20"></td>
						<td><input type=text name="DPC_txtDateTo" id="DPC_txtDateTo" class="values" size="20" maxlength="10" value="<?php echo $dtTo;?>">										
						</td>
					</tr>																			 -->
				</table>
			</td>
		</tr>
		<tr height="20">
			<td width="10"></td>
		</tr>
		<tr>
			<td width="10"></td>
			<td>
				<table width="900" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
					<tr height="30">							
						<td width="26%" class="tablehdr" align="center">&nbsp;Real Property&nbsp;
						</td>
						<td  width="74%" class="tablehdr" align="center">&nbsp;Footnotes/Remarks&nbsp;
						</td>
					</tr>
					<?php
					$ctr = 0;
					while(odbc_fetch_row($processList)) {
						$real_property_code = replacedoublequotes(odbc_result($processList,"real_property_code")); 
						$real_property_name = replacedoublequotes(odbc_result($processList,"real_property_name")); 
						$remarks = replacedoublequotes(odbc_result($processList,"remarks")); 
						//$remarks = "beh";
						
						$ctr = $ctr+1;
						if ($ctr%2==1) 
							$rowColor = "98fb98";	
						else
							$rowColor = "ffffe0";			
					?>
					<tr id="editRow<?php echo "$ctr";?>" name="editRow<?php echo "$ctr";?>" bgcolor="<?php echo "$rowColor" ?>">							
						<td width="26%" style="border:1px" class="values">&nbsp;<?php echo "$real_property_name";?>&nbsp;
							<input type="hidden" name="hidCode<?php echo "$ctr";?>" id="hidCode<?php echo "$ctr";?>" value="<?php echo "$real_property_code";?>">
						</td>
						<td width="74%" style="border:1px" class="values" align="center">
							<textarea name="txtRemarks<?php echo "$ctr";?>" id="txtRemarks<?php echo "$ctr";?>" class="values" rows="2" cols="100"><?php echo "$remarks";?></textarea>
						</td>					
					</tr>
					<?php } ?>										
				</table>
			</td>
		</tr>
	</table>
	<br>
	
	<input type="hidden" id="hidMode" name="hidMode">
	<input type="hidden" id="hidRowCtr" name="hidRowCtr" value=<?php echo $ctr;?>>
</form>
</body> 
</html>

<script language="javascript" src="jsp/function.js"></script>
<script type="text/javascript">
function hov(loc,cls) {   
	if(loc.className)   
	loc.className=cls;   
} 

function cmdInvoiceFrom_onClick() {
	window.open ("report_invoice_lookup.php?src=from","displayWindow","type=fullwindow,titlebar=no,scrollbars=yes");
	return false;
}

function cmdInvoiceTo_onClick() {
	window.open ("report_invoice_lookup.php?src=to","displayWindow","type=fullwindow,titlebar=no,scrollbars=yes");
	return false;
}

function cmdPrint_OnClick() {

	if (frmOccupancyAsOf.DPC_txtDateFrom.value != "") {
		if (isDate(frmOccupancyAsOf.DPC_txtDateFrom.value)==false) {
			frmOccupancyAsOf.DPC_txtDateFrom.focus()
			return false
		}
	}
	// if (frmOccupancyAsOf.DPC_txtDateTo.value != "") {
	// 	if (isDate(frmOccupancyAsOf.DPC_txtDateTo.value)==false) {
	// 		frmOccupancyAsOf.DPC_txtDateTo.focus()
	// 		return false
	// 	}
	// }
	
	// if (frmOccupancyAsOf.DPC_txtDateFrom.value != "" && frmOccupancyAsOf.DPC_txtDateTo.value != "") {
	// 	if (CompareDates(frmOccupancyAsOf.DPC_txtDateFrom.value,frmOccupancyAsOf.DPC_txtDateTo.value)==false) {
	// 		frmOccupancyAsOf.DPC_txtDateFrom.focus()
	// 		return false
	// 	}
	// }
		
	frmOccupancyAsOf.hidMode.value = "PRINT"
	frmOccupancyAsOf.submit()
}

function cmdGenerate_OnClick() {

	if (frmOccupancyAsOf.DPC_txtDateFrom.value != "") {
		if (isDate(frmOccupancyAsOf.DPC_txtDateFrom.value)==false) {
			frmOccupancyAsOf.DPC_txtDateFrom.focus()
			return false
		}
	}
	// if (frmOccupancyAsOf.DPC_txtDateTo.value != "") {
	// 	if (isDate(frmOccupancyAsOf.DPC_txtDateTo.value)==false) {
	// 		frmOccupancyAsOf.DPC_txtDateTo.focus()
	// 		return false
	// 	}
	// }
	
	// if (frmOccupancyAsOf.DPC_txtDateFrom.value != "" && frmOccupancyAsOf.DPC_txtDateTo.value != "") {
	// 	if (CompareDates(frmOccupancyAsOf.DPC_txtDateFrom.value,frmOccupancyAsOf.DPC_txtDateTo.value)==false) {
	// 		frmOccupancyAsOf.DPC_txtDateFrom.focus()
	// 		return false
	// 	}
	// }
		
	frmOccupancyAsOf.hidMode.value = "GENERATE"
	frmOccupancyAsOf.submit()
}

</script>
