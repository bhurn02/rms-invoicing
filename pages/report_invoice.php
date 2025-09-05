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
$menu_id = $_GET["menu_id"];

$strMode = $_POST["hidMode"];

if ($strMode == "PRINT") {
	$cn = cn();
	$server = $cn[1];
	$db = $cn[2];
	$username = $cn[3];
	$password = $cn[4];
	$report_path= $cn[5];
	$pdf_path= $cn[6];
	$pdf_link= $cn[7];
	
	//$dtGenerated = date("m/d/Y H:i:s",(strtotime(date("m/d/y H:i:s", time()))+60*60*24*($OFFSET)));
	$dtGenerated = strval(date("m/d/y H:i:s", time()));
	//$dtGenerated = "06/18/2013";
	$sqlquery="exec sp_rpt_Invoice_Proc '" . $_POST["txtInvoiceNoFrom"] . "','" . $_POST["DPC_txtDateFrom"] . "','" . $_POST["DPC_txtDateTo"] . "'," . $_POST["cboStatus"] . ",'" . $sessUserID . "','" . $dtGenerated . "'";	
	//echo $sqlquery;
	$process=odbc_exec($sqlconnect, $sqlquery);	
	//- Variables - for your RPT and PDF 
	//echo "Print Report Test"; 
	$my_report = $report_path . "t_invoice.rpt"; // 
	//echo $my_report;
	//die();
	//rpt source file 
	$pdf_file = "t_invoice" . str_replace("/","",date("m/d/y/H/i/s", time())) . ".pdf";
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

	//------ Pass formula fields --------
	//$creport->FormulaFields->Item(1)->Text = ("'invoice_no'");
	$creport->ParameterFields(1)->AddCurrentValue ($_POST["txtInvoiceNoFrom"]);
	$creport->ParameterFields(2)->AddCurrentValue ($_POST["DPC_txtDateFrom"]);
	$creport->ParameterFields(3)->AddCurrentValue ($_POST["DPC_txtDateTo"]);
	$creport->ParameterFields(4)->AddCurrentValue ($sessUserID);
	//echo $dtGenerated;
	//die();
	//$creport->ParameterFields(5)->AddCurrentValue ("06/18/2013");
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
	
	$strInvoiceNoFrom = $_POST["txtInvoiceNoFrom"];
	$dtFrom = $_POST["DPC_txtDateFrom"];
	$dtTo = $_POST["DPC_txtDateTo"];
	$intStatus = $_POST["cboStatus"];
}

?>
<html> 
<head> 
<title>INVOICE PRINTING</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
<script type="text/javascript" src="library/datepickercontrol/datepickercontrol.js"></script>
<link type="text/css" rel="stylesheet" href="library/datepickercontrol/datepickercontrol_green.css">
</head> 
<body style="margin:'0';background-color: #F3F5B4;">
<form name="frmInvoiceReport" id="frmInvoiceReport" method="post">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">INVOICE PRINTING
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;
			 </a></li>	
			  <li class="li_nc"><a href="#" onClick="javascript:cmdPrint_OnClick()">|&nbsp;&nbsp;&nbsp;&nbsp;Print&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <li class="li_nc"><a href="#" onClick="javascript:window.close();">Close&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
		</ul>
	</div>
	<table>
		<tr>
		<td width="10"></td>
		<td>
		<table>
			<tr>
				<td>
					<table>
						<tr>
							<td>
								<table>
									<tr>
										<td class="fieldname_right">INVOICE NO :</td>
										<td width="20"></td>
										<td><input type=text name="txtInvoiceNoFrom" id="txtInvoiceNoFrom" class="values" size="20" maxlength="20" value="<?php echo $strInvoiceNoFrom;?>">
										<img id="cmdInvoiceFrom" name="cmdInvoiceFrom" onClick="javascript:cmdInvoiceFrom_onClick();" src="images/icon_search.gif" style="cursor:hand" alt="Invoice Lookup">
										</td>
									</tr>														
									<tr>
										<td class="fieldname_right">DATE FROM (mm/dd/yyyy) :</td>
										<td width="20"></td>
										<td><input type=text name="DPC_txtDateFrom" id="DPC_txtDateFrom" class="values" size="20" maxlength="10" value="<?php echo $dtFrom;?>">										
										</td>
									</tr>					
									<tr>
										<td class="fieldname_right">TO (mm/dd/yyyy) :</td>
										<td width="20"></td>
										<td><input type=text name="DPC_txtDateTo" id="DPC_txtDateTo" class="values" size="20" maxlength="10" value="<?php echo $dtTo;?>">										
										</td>
									</tr>		
									<tr>
										<td class="fieldname_right">E-MAIL ALERT STATUS :</td>
										<td width="20"></td>
										<td class="values">
											<select name="cboStatus" id="cboStatus" class="values">
												<?php if ($intStatus == 0) { ?>
													<option selected value=0>ALL</option>
													<option value=1>SENT</option>
													<option value=2>UNSENT</option>
												<?php } elseif ($intStatus == 1) { ?>
													<option value=0>ALL</option>
													<option selected value=1>SENT</option>
													<option value=2>UNSENT</option>
												<?php } elseif ($intStatus == 2) { ?>
													<option value=0>ALL</option>
													<option value=1>SENT</option>
													<option selected value=2>UNSENT</option>
												<?php } ?>
											</select>
										</td>
									</tr>																			
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		</td>
		</tr>
	</table>
	<input type="hidden" id="hidMode" name="hidMode">
	<input type="hidden" id="hidSaveMode" name="hidSaveMode" value="<?php echo $strSaveMode; ?>">
	<input type="hidden" id="hidMenuID" name="hidMenuID" value=<?php echo $menu_id;?>>
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
	window.open ("report_invoice_lookup.php?menu_id=" + frmInvoiceReport.hidMenuID.value + "&src=from","displayWindow","type=fullwindow,titlebar=no,scrollbars=yes");
	return false;
}

function cmdInvoiceTo_onClick() {
	window.open ("report_invoice_lookup.php?menu_id=" + frmInvoiceReport.hidMenuID.value + "&src=to","displayWindow","type=fullwindow,titlebar=no,scrollbars=yes");
	return false;
}

function cmdPrint_OnClick() {

	if (frmInvoiceReport.DPC_txtDateFrom.value != "") {
		if (isDate(frmInvoiceReport.DPC_txtDateFrom.value)==false) {
			frmInvoiceReport.DPC_txtDateFrom.focus()
			return false
		}
	}
	if (frmInvoiceReport.DPC_txtDateTo.value != "") {
		if (isDate(frmInvoiceReport.DPC_txtDateTo.value)==false) {
			frmInvoiceReport.DPC_txtDateTo.focus()
			return false
		}
	}
	
	if (frmInvoiceReport.DPC_txtDateFrom.value != "" && frmInvoiceReport.DPC_txtDateTo.value != "") {
		if (CompareDates(frmInvoiceReport.DPC_txtDateFrom.value,frmInvoiceReport.DPC_txtDateTo.value)==false) {
			frmInvoiceReport.DPC_txtDateFrom.focus()
			return false
		}
	}
		
	frmInvoiceReport.hidMode.value = "PRINT"
	frmInvoiceReport.submit()
}

</script>
