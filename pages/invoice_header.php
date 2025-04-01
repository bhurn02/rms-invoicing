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
		echo "<script> parent.frames[2].location = \"" . "accessnotallowed.htm" .  "\";</script>";
		exit(); 
	}
}

//end access
$sqlconnect = connection();
$strIPAddr = $_SERVER["REMOTE_ADDR"];
$menu_id = $_GET["menu_id"];

$strInvoiceNo = "";
$dtInvoice = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
$strTenantCode = "";
$strTenantName = "";
$strRealPropertyCode = "";
$strRealPropertyName = "";
$dtBillingFrom = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
$dtBillingTo = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
$dblTotalAmount = "0.00";
$strDocNo = "";
$strBillTo = "";
$strAccountNo = "";
$strRemarks = "";
$blnVoid = "";
$strStatus = "";
$strStatusDesc = "";
$dblInvoiceDetailCnt = 0;

$strMsg = "";
$strSaveMode = "";
$strMode = $_POST["hidMode"];
$strSaveMode = $_POST["hidSaveMode"];

$sqlquerycbo="select * from m_real_property order by real_property_name";
$processcbo=odbc_exec($sqlconnect, $sqlquerycbo);
while (odbc_fetch_row($processcbo)) {
	$cboRealProperty .= "<option value=\"" . trim(odbc_result($processcbo,"real_property_code")) . "\">" . trim(strtoupper(odbc_result($processcbo,"real_property_name"))) . "</option>";
}

if ($_GET["mode"] == "FIND") {
	$strInvoiceNo = $_GET["invoice_no"];
	$strMode = "FIND";
}

//echo $strMode;
if ($strMode != "") {	
	if ($strMode != "FIND") {
		$strInvoiceNo = replacesinglequote($_POST["hidInvoiceNo"]);
	}
	$dtInvoice = $_POST["DPC_txtInvoiceDate"];
	$strTenantCode = replacesinglequote($_POST["hidTenantCode"]);
	$strTenantName = replacesinglequote($_POST["hidTenantName"]);
	$strRealPropertyCode = replacesinglequote($_POST["hidRealPropertyCode"]);
	$strRealPropertyName = $_POST["hidRealPropertyName"];
	$dtBillingFrom = replacesinglequote($_POST["DPC_txtBillingFrom"]);
	$dtBillingTo = replacesinglequote($_POST["DPC_txtBillingTo"]);
	$strBillTo = "";
	$strAccountNo = replacesinglequote($_POST["txtAccountNo"]);
	$strRemarks = replacesinglequote($_POST["txtRemarks"]);	
	if ($_POST["chkVoid"] == "on") {
		$blnVoid = "V";
	}
	else {
		$blnVoid = "";		
	}
	$uid = $sessUserID;
	$company_code = $sessCompanyCode;
	//echo $strMode;
	switch ($strMode) {
		case "SAVE":
			if ($strSaveMode != "EDIT") {
				$strInvoiceNo = replacesinglequote($_POST["txtInvoiceNo"]);	
				if ($strInvoiceNo != "") {
					$sqlquery="exec sp_t_Invoice_Check 'FIND','" . $strInvoiceNo . "','" . $dtInvoice . "','" . $strTenantCode . "','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $strDocNo . "','" . $strBillTo . "','" . $strAccountNo . "','" . $strRemarks . "','" . $blnVoid . "','" . $strRealPropertyCode . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					$process=odbc_exec($sqlconnect, $sqlquery);
					if (odbc_fetch_row($process)) {
							if (odbc_result($process,"x") == 1) 
								$strMsg = "Invoice No. already exists!";
					}						
				}
				
				if ($blnVoid == "") {
					if ($strMsg =="") {
						$sqlquery="exec sp_t_Invoice_Check 'FIND_CLIENT_REAL_PROPERTY','" . $strInvoiceNo . "','" . $dtInvoice . "','" . $strTenantCode . "','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $strDocNo . "','" . $strBillTo . "','" . $strAccountNo . "','" . $strRemarks . "','" . $blnVoid . "','" . $strRealPropertyCode . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
						//echo $sqlquery;
						$process=odbc_exec($sqlconnect, $sqlquery);
						if (odbc_fetch_row($process)) {
								if (odbc_result($process,"x") == 1) {
									$strMsg = "No tenant under this client is occupying the selected real property!";
									$strRealPropertyCode = "";
									$strRealPropertyName = "";
								}
						}			
					}
					if ($strMsg =="") {
						$sqlquery="exec sp_t_Invoice_Check 'FIND_SAME_BILLING','" . $strInvoiceNo . "','" . $dtInvoice . "','" . $strTenantCode . "','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $strDocNo . "','" . $strBillTo . "','" . $strAccountNo . "','" . $strRemarks . "','" . $blnVoid . "','" . $strRealPropertyCode . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
						$process=odbc_exec($sqlconnect, $sqlquery);
						if (odbc_fetch_row($process)) {
								if (odbc_result($process,"x") == 1) 
									$strMsg = "Invoice with the same billing dates already exists!";
						}			
					}
				}
				
				
				if ($strMsg =="") {
					$sqlquery="exec sp_t_Invoice_Save 'SAVE_NEW','" . $strInvoiceNo . "','" . $dtInvoice . "','" . $strTenantCode . "','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $strDocNo . "','" . $strBillTo . "','" . $strAccountNo . "','" . $strRemarks . "','" . $blnVoid . "','" . $strRealPropertyCode . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					//echo $sqlquery;
					//die();
					$process=odbc_exec($sqlconnect, $sqlquery);
					if (odbc_fetch_row($process)) {
						$strInvoiceNo = odbc_result($process,"invoice_no");
					}
					$strMsg = "Record saved!";
					$strMode = "RETRIEVE";
				}
			}
			else {	
				if ($blnVoid == "") {			
					$sqlquery="exec sp_t_Invoice 'FIND_SAME_BILLING_EDIT','" . $strInvoiceNo . "','" . $dtInvoice . "','" . $strTenantCode . "','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $strDocNo . "','" . $strBillTo . "','" . $strAccountNo . "','" . $strRemarks . "','" . $blnVoid . "','" . $strRealPropertyCode . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					//echo $sqlquery;
					$process=odbc_exec($sqlconnect, $sqlquery);
					if (odbc_fetch_row($process)) {
						if (odbc_result($process,"x") == 1) 
							$strMsg = "Invoice with the same billing dates already exists!";						
					}		
				}
				
				if ($strMsg == "") {
					$sqlquery="exec sp_t_Invoice_Save 'SAVE_EDIT','" . $strInvoiceNo . "','" . $dtInvoice . "','" . $strTenantCode . "','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $strDocNo . "','" . $strBillTo . "','" . $strAccountNo . "','" . $strRemarks . "','" . $blnVoid . "','" . $strRealPropertyCode . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					$process=odbc_exec($sqlconnect, $sqlquery);
					$strMsg = "Record saved!";
					$strMode = "RETRIEVE";
				}
			}
			//echo $sqlquery;			
			//echo $sqlquery;
			break;
			
		case "PRINT":
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
			$sqlquery="exec sp_rpt_Invoice_Proc '" . $_POST["hidInvoiceNo"] . "','','',0,'" . $sessUserID . "','" . $dtGenerated . "'";	
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
			$creport->ParameterFields(1)->AddCurrentValue ($_POST["hidInvoiceNo"]);
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
			break;
	}
}
//echo $strMode;
//echo $strInvoiceNo;
if  ($strMode == "RETRIEVE" || $strMode == "FIND") {
	$sqlquery="exec sp_t_Invoice_Retrieve 'RETRIEVE','" . $strInvoiceNo . "'";	
	//echo $sqlquery;
	$process=odbc_exec($sqlconnect, $sqlquery);
	if (odbc_fetch_row($process)) {
			$strInvoiceNo = odbc_result($process,"invoice_no");
			
			if (odbc_result($process,"invoice_date") == "" || date("m/d/Y",(strtotime(odbc_result($process,"invoice_date"))+60*60*24*($OFFSET))) == "01/01/1970")	
				$dtInvoice = "";
			else
				$dtInvoice = date("m/d/Y",(strtotime(odbc_result($process,"invoice_date"))+60*60*24*($OFFSET)));	
				
			$strTenantCode = odbc_result($process,"client_code");
			$strTenantName = replacedoublequotes(odbc_result($process,"tenant_name"));
			$strRealPropertyCode = odbc_result($process,"real_property_code");
			$strRealPropertyName = odbc_result($process,"real_property_name");
			
			if (odbc_result($process,"billing_from") == "" || date("m/d/Y",(strtotime(odbc_result($process,"billing_from"))+60*60*24*($OFFSET))) == "01/01/1970")	
				$dtBillingFrom = "";
			else
				$dtBillingFrom = date("m/d/Y",(strtotime(odbc_result($process,"billing_from"))+60*60*24*($OFFSET)));	
			
			if (odbc_result($process,"billing_to") == "" || date("m/d/Y",(strtotime(odbc_result($process,"billing_to"))+60*60*24*($OFFSET))) == "01/01/1970")	
				$dtBillingTo = "";
			else
				$dtBillingTo = date("m/d/Y",(strtotime(odbc_result($process,"billing_to"))+60*60*24*($OFFSET)));	
			
			$dblTotalAmount = odbc_result($process,"total_amount");
			$strDocNo = odbc_result($process,"document_no");			
			$strAccountNo = odbc_result($process,"sap_code");
			$strRemarks = replacedoublequotes(odbc_result($process,"remarks"));
			$strStatus = odbc_result($process,"status");
			$strStatusDesc = odbc_result($process,"status_desc");
			$dblInvoiceDetailCnt = odbc_result($process,"invoice_detail_cnt");
			
			if (odbc_result($process,"status") == "V") {
				$blnVoid = "checked";							
			}
			else {
				$blnVoid = "";	
			}
			$strMode = "RETRIEVE";
			$strSaveMode = "EDIT";
	}
	else {
		$strMsg = "No record found!";
		$strMode = "";
	}
}

if ($strMsg != ""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 
?>
<html> 
<head> 
<title>INVOICE</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
<script type="text/javascript" src="library/datepickercontrol/datepickercontrol.js"></script>
<link type="text/css" rel="stylesheet" href="library/datepickercontrol/datepickercontrol_green.css">
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color: #F3F5B4;">
<form name="frmInvoiceHdr" id="frmInvoiceHdr" method="post" action="invoice_header.php?menu_id=<?php echo $menu_id;?>">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">INVOICE
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			 </a></li>	
			  <?php if ($strMode != "RETRIEVE") { ?>
				    <li class="li_nc"><a href="#" onClick="javascript:cmdRetrieve_OnClick()">|&nbsp;&nbsp;&nbsp;Retrieve&nbsp;&nbsp;&nbsp;|</a></li>			  			  		
			  <?php } else { ?>			
				    <li><a name="Retrieve" style="color: #666666">|&nbsp;&nbsp;&nbsp;Retrieve&nbsp;&nbsp;&nbsp;|</a></li>					
			  <?php } ?>	
			  <?php if ($strStatus == "") { ?>
				<li class="li_nc"><a href="#" onClick="javascript:cmdSave_OnClick()">Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			 <?php } else { ?>		
				 <li><a name="Save" style="color: #666666">Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } ?>
			  <li class="li_nc"><a href="#" onClick="javascript:change_loc('invoice_list.php?menu_id=<?php echo $menu_id;?>')">Find&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  
			  <?php if ($strMode != "RETRIEVE") { ?>
			  		<li><a name="Print" style="color: #666666">Print&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } else { ?>	
			  		<li class="li_nc"><a href="#" onClick="javascript:cmdPrint_OnClick()">Print&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>			
			  <?php } ?>	
			  
			  <li class="li_nc"><a href="#" onClick="javascript:cmdCancel_OnClick()">Cancel&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;||</a></li>			
			  <?php if ($strMode != "RETRIEVE") { ?>
			  	<li><a name="Detail" style="color: #666666">Detail&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } else { ?>	
			  	<li class="li_nc"><a href="#" target="_self" style="color: #FFFF33" onClick="javascript:change_loc('invoice_detail.php?menu_id=<?php echo $menu_id;?>&mode=FIND&menu=DETAIL&invoice_no=<?php echo $strInvoiceNo; ?>')">Detail&nbsp;&nbsp;&nbsp;&nbsp;<font color="white">|</font></a></li>
			  <?php } ?>	
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
										<td class="fieldname">INVOICE NO. :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<?php if ($strMode == "RETRIEVE") {?>
											<td><input type=text name="txtInvoiceNo" id="txtInvoiceNo" disabled class="values" size="20" maxlength="20" value="<?php echo $strInvoiceNo;?>"></td>
										<?php } else {?>
											<td><input type=text name="txtInvoiceNo" id="txtInvoiceNo" class="values" size="20" maxlength="20" value="<?php echo $strInvoiceNo;?>"></td>
										<?php } ?>
										<input type="hidden" name="hidInvoiceNo" id="hidInvoiceNo" value="<?php echo $strInvoiceNo;?>">
									</tr>
									<tr>
										<td class="fieldname">DATE (mm/dd/yyyy) :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="DPC_txtInvoiceDate" id="DPC_txtInvoiceDate" class="values" size="20" maxlength="10" value="<?php echo $dtInvoice;?>"></td>
									</tr>
									<tr>
										<td class="fieldname">BILL TO :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtTenantName" id="txtTenantName" disabled class="values" size="60" value="<?php echo $strTenantName;?>">
										<?php if ($dblInvoiceDetailCnt <= 0) { ?>
											<img id="cmdTenantSearch" name="cmdTenantSearch" onClick="javascript:cmdTenantSearch_onClick();" src="images/icon_search.gif" style="cursor:hand" alt="Client Lookup">
										<?php } ?>
										<input type="hidden" name="hidTenantCode" id="hidTenantCode" value="<?php echo $strTenantCode;?>">
										<input type="hidden" name="hidTenantName" id="hidTenantName" value="<?php echo $strTenantName;?>">
										</td>
									</tr>	
									<tr>
										<td class="fieldname">REAL PROPERTY :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtRealPropertyName" id="txtRealPropertyName" disabled class="values" size="60" value="<?php echo $strRealPropertyName;?>">																					
										</td>										
										<input type="hidden" name="hidRealPropertyCode" id="hidRealPropertyCode" value="<?php echo $strRealPropertyCode;?>">											
										<input type="hidden" name="hidRealPropertyName" id="hidRealPropertyName" value="<?php echo $strRealPropertyName;?>">											
									</tr>	
									<?php if ($strMode != "RETRIEVE" || $dblInvoiceDetailCnt <= 0) {?>
										<tr>
											<td class="fieldname">&nbsp;</td>
											<td width="20"></td>		
											<td >								
												<select id="cboRealProperty" name="cboRealProperty" class="values">
													<option value="">- Select Real Property -</option>
													<?php echo $cboRealProperty;?>
												</select>
											</td>
										</tr>	
									<?php } ?>
									<tr>
										<td class="fieldname">BILLING FROM (mm/dd/yyyy) :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="DPC_txtBillingFrom" id="DPC_txtBillingFrom" class="values" size="20" maxlength="10" value="<?php echo $dtBillingFrom;?>"></td>
									</tr>
									<tr>
										<td class="fieldname">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TO (mm/dd/yyyy) :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="DPC_txtBillingTo" id="DPC_txtBillingTo" class="values" size="20" maxlength="10" value="<?php echo $dtBillingTo;?>"></td>
									</tr>		
									<tr>
										<td class="fieldname">TOTAL AMOUNT :</td>
										<td width="20"></td>
										<td><input type=text name="txtTotalAmount" id="txtTotalAmount" disabled class="values" style="text-align:right" size="20" value="$<?php echo $dblTotalAmount;?>"></td>
									</tr>																																		
									<tr>
										<td class="fieldname">ACCOUNT CODE (SAP) :</td>
										<td width="20"></td>
										<td><input type=text name="txtAccountNo" id="txtAccountNo" class="values" size="30" maxlength="20" value="<?php echo $strAccountNo;?>"></td>
									</tr>												
									<tr>
										<td class="fieldname">REMARKS :</td>
										<td width="20"></td>
										<td><textarea name="txtRemarks" id="txtRemarks" class="values" rows="3" cols="40"><?php echo $strRemarks;?></textarea></td>
									</tr>								
									<tr>
										<?php if ($strStatus != "P") { ?>
											<td class="fieldname">&nbsp;</td>
											<td width="20"></td>
											<td class="values">
													<input type="checkbox" name="chkVoid" id="chkVoid" <?php echo $blnVoid;?>>VOID
											</td>
										<?php } else { ?> 
											<td class="fieldname">STATUS :</td>
											<td width="20"></td>
											<td class="values" style="color:red; font-weight:bold"><?php echo $strStatusDesc;?></td>
										<?php } ?> 
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

function cmdSave_OnClick() {
	if (trim(frmInvoiceHdr.DPC_txtInvoiceDate.value) == "") {
		alert("Invoice Date is required")
		frmInvoiceHdr.DPC_txtInvoiceDate.focus()
		return false
	}
	if (trim(frmInvoiceHdr.DPC_txtInvoiceDate.value) != "") {
		if (isDate(frmInvoiceHdr.DPC_txtInvoiceDate.value)==false){
			frmInvoiceHdr.DPC_txtInvoiceDate.focus()
			return false
		}
	}
	if (trim(frmInvoiceHdr.hidTenantName.value) == "") {
		alert("Bill To is required")
		frmInvoiceHdr.cmdTenantSearch.focus()
		return false
	}
	if (frmInvoiceHdr.chkVoid.checked != true) {
		if (trim(frmInvoiceHdr.hidRealPropertyCode.value) == "") {
			if (trim(frmInvoiceHdr.cboRealProperty.value) == "") {
				alert("Real Property is required")
				if (frmInvoiceHdr.cboRealProperty.disabled==false) {
					frmInvoiceHdr.cboRealProperty.focus()
				}
				return false
			}
		}
	}
	if (trim(frmInvoiceHdr.DPC_txtBillingFrom.value) == "") {
		alert("Billing From date is required")
		frmInvoiceHdr.DPC_txtBillingFrom.focus()
		return false
	}
	if (trim(frmInvoiceHdr.DPC_txtBillingFrom.value) != "") {
		if (isDate(frmInvoiceHdr.DPC_txtBillingFrom.value)==false){
			frmInvoiceHdr.DPC_txtBillingFrom.focus()
			return false
		}
	}
	if (trim(frmInvoiceHdr.DPC_txtBillingTo.value) == "") {
		alert("Billing To date is required")
		frmInvoiceHdr.DPC_txtBillingTo.focus()
		return false
	}
	if (trim(frmInvoiceHdr.DPC_txtBillingTo.value) != "") {
		if (isDate(frmInvoiceHdr.DPC_txtBillingTo.value)==false){
			frmInvoiceHdr.DPC_txtBillingTo.focus()
			return false
		}
	}
	
	if (frmInvoiceHdr.DPC_txtBillingFrom.value != "" && frmInvoiceHdr.DPC_txtBillingTo.value != "") {
		if (CompareDates(frmInvoiceHdr.DPC_txtBillingFrom.value,frmInvoiceHdr.DPC_txtBillingTo.value)==false) {
			frmInvoiceHdr.DPC_txtBillingFrom.focus()
			return false
		}
	}
	
	if (frmInvoiceHdr.chkVoid.checked == true) {
		if (confirm("Are you sure you want to void this invoice?")) {
			frmInvoiceHdr.hidMode.value = "SAVE";
			frmInvoiceHdr.submit();		
		}
	}
	else {
		if (trim(frmInvoiceHdr.hidRealPropertyCode.value) == "") {
			frmInvoiceHdr.hidRealPropertyCode.value = frmInvoiceHdr.cboRealProperty.value 
		}
		frmInvoiceHdr.hidMode.value = "SAVE";
		frmInvoiceHdr.submit();
	}
}

function cmdRetrieve_OnClick() {
	frmInvoiceHdr.hidInvoiceNo.value = frmInvoiceHdr.txtInvoiceNo.value;
	frmInvoiceHdr.hidMode.value = "RETRIEVE";
	frmInvoiceHdr.submit();
}

function cmdCancel_OnClick() {
	parent.frames[2].location = "invoice_header.php?menu_id=" + frmInvoiceHdr.hidMenuID.value;
}

function cmdPrint_OnClick() {
	frmInvoiceHdr.hidInvoiceNo.value = frmInvoiceHdr.txtInvoiceNo.value;
	frmInvoiceHdr.hidMode.value = "PRINT";
	frmInvoiceHdr.submit();
}

function cmdTenantSearch_onClick() {
	window.open ("invoice_header_client_lookup.php?menu_id=" + frmInvoiceHdr.hidMenuID.value,"displayWindow","type=fullwindow,titlebar=no,scrollbars=yes");
	return false;
}

function cmdRealPropertySearch_onClick() {
	window.open ("invoice_real_property_search.php?menu_id=" + frmInvoiceHdr.hidMenuID.value,"displayWindow","type=fullwindow,titlebar=no,scrollbars=yes");
	return false;
}

</script>