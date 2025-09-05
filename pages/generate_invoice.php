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

$uid = $sessUserID;
$company_code = $sessCompanyCode;

$strMode = trim($_POST["hidMode"]);
$strSaveMode = $_POST["hidSaveMode"];
$strRealPropertyCode = "";
$dtBillingFrom = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
$dtBillingTo = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
$dtInvoiceDate = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
$blnPrint = "";
$strMsg = "";

$sqlquerycbo="select * from m_real_property order by real_property_name";
$processcbo=odbc_exec($sqlconnect, $sqlquerycbo);
while (odbc_fetch_row($processcbo)) {
	$cbocharge .= "<option value=\"" . trim(odbc_result($processcbo,"real_property_code")) . "\">" . trim(strtoupper(odbc_result($processcbo,"real_property_name"))) . "</option>";
}

if ($strMode != "") {	
	$dtBillingFrom = $_POST["DPC_txtDateFrom"];
	$dtBillingTo = $_POST["DPC_txtDateTo"];
	$dtInvoiceDate = $_POST["DPC_txtInvoiceDate"];
	$strRealPropertyCode = $_POST["cboRealProperty"];
	$strRealPropertyName = $_POST["hidRealPropertyName"];	
	$strKeyword = $_POST["txtKeyword"];
	
	//echo $strRealPropertyName;
	switch ($strMode) {
		case "SEARCH":						
			//$sqlquery="exec sp_t_GenerateInvoice 'VIEW',null,null,null,'" . $strRealPropertyCode . "','','" . $strKeyword . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";
			$sqlquery="exec sp_t_GenerateInvoice 'VIEW','" . $dtBillingFrom . "','" . $dtBillingTo . "',null,'" . $strRealPropertyCode . "','','" . $strKeyword . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";
			$process=odbc_exec($sqlconnect, $sqlquery);
			//echo $sqlquery;
			break;
		
		case "PROCESS":
			$i = 1;
			$j = 0;
			while ($i <= $_POST["hidRowCtr"]) {
				if (isset($_POST["chkSelect" . strval($i)])) {					
					$sqlquery="exec sp_t_GenerateInvoice 'SAVE','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $dtInvoiceDate . "','" . $strRealPropertyCode . "','" . $_POST["hidCode" . strval($i)] . "','" . $strKeyword . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					// echo $sqlquery;
					// echo '<br>KALMAHAN MO MUNA. INAAYOS PA :)';die();
					// echo $sqlquery;die();
					$process=odbc_exec($sqlconnect, $sqlquery);
					$j++;
				}
				$i++;
			}
			if ($j > 0) {
				$strMsg = "Invoice/s generated for selected tenants! Check Edit Invoice module.";
			}
			else {
				$sqlquery="exec sp_t_GenerateInvoice 'SAVE_PER_PROPERTY','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $dtInvoiceDate . "','" . $strRealPropertyCode . "','','" . $strKeyword . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
				$process=odbc_exec($sqlconnect, $sqlquery);
				//echo $sqlquery;
				$strMsg = "Invoice/s generated for selected real property! Check Edit Invoice module.";
			}
			
			if (isset($_POST["chkPrint"])) {
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
				$sqlquery="exec sp_rpt_Invoice_Proc '" . $strRealPropertyCode . "','" . $dtBillingFrom . "','" . $dtBillingTo . "',0,'" . $sessUserID . "','" . $dtGenerated . "'";	
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
				$creport->ParameterFields(1)->AddCurrentValue ($strRealPropertyCode);
				$creport->ParameterFields(2)->AddCurrentValue ($dtBillingFrom);
				$creport->ParameterFields(3)->AddCurrentValue ($dtBillingTo);
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
			}
				
			break;
	}
	if ($_POST["chkPrint"] == "on") {
		$blnPrint = "checked"; }
	else {
		$blnPrint = ""; }
}	

//echo $sqlquery;
if ($strMsg != ""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 

?>
<html> 
<head> 
<title>GENERATE INVOICE</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
<script type="text/javascript" src="library/datepickercontrol/datepickercontrol.js"></script>
<link type="text/css" rel="stylesheet" href="library/datepickercontrol/datepickercontrol_green.css">
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color: #F3F5B4;">
<form name="frmGenInvoice" id="frmGenInvoice" method="post">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">GENERATE INVOICE
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			 </a></li>	
			  <li class="li_nc"><a href="#" onClick="javascript:cmdProcess_OnClick()">|&nbsp;&nbsp;&nbsp;Process&nbsp;&nbsp;&nbsp;|</a></li>			  			  
			  <li class="li_nc"><a href="#" onClick="javascript:cmdCancel_OnClick()">Cancel&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>				  
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
										<td class="fieldname">BILLING FROM (mm/dd/yyyy) :</td>
										<td width="10"></td>
										<td><input type=text name="DPC_txtDateFrom" id="DPC_txtDateFrom" class="values" size="20" maxlength="10" value="<?php echo $dtBillingFrom; ?>"></td>
									</tr>	
									<tr>
										<td class="fieldname">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TO (mm/dd/yyyy) :</td>
										<td width="10"></td>
										<td><input type=text name="DPC_txtDateTo" id="DPC_txtDateTo" class="values" size="20" maxlength="10"  value="<?php echo $dtBillingTo; ?>"></td>
									</tr>	
									<tr>
										<td class="fieldname">INVOICE DATE (mm/dd/yyyy) :</td>
										<td width="10"></td>
										<td><input type=text name="DPC_txtInvoiceDate" id="DPC_txtInvoiceDate" class="values" size="20" maxlength="10" value="<?php echo $dtInvoiceDate; ?>"></td>
									</tr>										
									<tr>
										<td class="fieldname">&nbsp;</td>
										<td width="10">&nbsp;</td>
										<td class="values"><input type="checkbox" name="chkPrint" id="chkPrint" <?php echo $blnPrint;?> >&nbsp;<b>PRINT</b></td>
									</tr>									
									<tr>
										<td class="fieldname">&nbsp;</td>
										<td width="10">&nbsp;</td>
										<td>&nbsp;</td>
									</tr>		
									<tr>
										<td class="fieldname">Select Real Property :</td>
										<td width="10"></td>
										<td>
											<select name="cboRealProperty" id="cboRealProperty" class="values" onChange="javascript:save_text();">
												<?php if ($strRealPropertyCode!= "") { ?>
													<option selected value="<?php echo $strRealPropertyCode; ?>"><?php echo $strRealPropertyName; ?></option>
												<?php } ?>
												<option value="">- ALL -</option>
												<?php echo $cbocharge; ?>
											</select>
											<img id="cmdSearch" name="cmdSearch" onClick="javascript:cmdSearch_onClick();" src="images/icon_textbox_search.gif" style="cursor:hand" alt="List Tenants">
										</td>
									</tr>											
									<tr>
										<td class="fieldname">Type keyword here :</td>
										<td width="10"></td>
										<td>
											<input type=text name="txtKeyword" id="txtKeyword" class="values" size="50" value="<?php echo $strKeyword ?>" onKeyUp="javascript:txtKeyword_onKeyUp(event)">											
										</td>
									</tr>		
								</table>
							</td>
						</tr>						
					</table>					
					<table width="800" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
						<tr height="30">
							<td width="5%" class="tablehdr" align="center">&nbsp;Sel&nbsp;
							</td>
							<td width="15%" class="tablehdr" align="center">&nbsp;Tenant Code&nbsp;
							</td>
							<td width="35%" class="tablehdr" align="center">&nbsp;Tenant Name&nbsp;
							</td>
							<td  width="25%" class="tablehdr" align="center">&nbsp;Real Property&nbsp;
							</td>
							<td  width="20%" class="tablehdr" align="center">&nbsp;Building/Unit No.&nbsp;
							</td>
						</tr>
						<?php
						while(odbc_fetch_row($process)){
							$code = odbc_result($process,"tenant_code"); 
							$name = odbc_result($process,"tenant_name"); 
							$real_property_code = odbc_result($process,"real_property_code"); 
							$real_property_name = odbc_result($process,"real_property_name"); 
							$building_code = odbc_result($process,"building_code"); 
							$unit_no = odbc_result($process,"unit_no"); 
							$ctr = $ctr+1;
							
							if ($ctr%2==1) 
								$rowColor = "98fb98";	
							else
								$rowColor = "ffffe0";			
						?>
						<tr bgcolor="<?php echo "$rowColor" ?>">
							<td width="5%" align="center" style="border:1px;cursor:hand;">
								<input type="checkbox" name="chkSelect<?php echo $ctr; ?>" id="chkSelect<?php echo $ctr; ?>">
								<input type="hidden" name="hidCode<?php echo $ctr; ?>" id="hidCode<?php echo $ctr; ?>" value="<?php echo $code; ?>">
							</td>
							<td width="15%" style="border:1px" class="values">&nbsp;<?php echo "$code";?>&nbsp;
							</td>
							<td width="35%" style="border:1px" class="values">&nbsp;<?php echo "$name";?>&nbsp;
							</td>
							<td width="25%" style="border:1px" class="values">&nbsp;<?php echo "$real_property_name";?>&nbsp;
							</td>
							<td width="20%" style="border:1px" class="values">&nbsp;<?php echo "$building_code";?>&nbsp;/&nbsp;<?php echo "$unit_no";?>
							</td>							
						</tr>
						<?php }
						?>
					</table>
					<table>
						<tr>
							<td class="values">&nbsp;
								<input type="checkbox" name="chkSelectAll" id="chkSelectAll" style="cursor:hand;" onClick="javascript:chkSelectAll_OnClick();">SELECT ALL
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
	<input type="hidden" id="hidRowCtr" name="hidRowCtr" value=<?php echo $ctr;?>>
	<input type="hidden" id="hidCurRow" name="hidCurRow">
	<input type="hidden" id="hidRealPropertyName" name="hidRealPropertyName" value="<?php echo $strRealPropertyName;?>">
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

function save_text()
   {
   var w = frmGenInvoice.cboRealProperty.selectedIndex;
   frmGenInvoice.hidRealPropertyName.value = frmGenInvoice.cboRealProperty.options[w].text;
   }

function txtKeyword_onKeyUp(e) {
	if (e.keyCode==13) {
		cmdSearch_onClick();
	}
}

function chkSelectAll_OnClick() {
	var ctr
	ctr = frmGenInvoice.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmGenInvoice.chkSelect" + i);
		if (frmGenInvoice.chkSelectAll.checked == true) {
			obj.checked = true;
		}
		else {
			obj.checked = false;
		}
	}
}

function cmdProcess_OnClick() {
	if (frmGenInvoice.DPC_txtDateFrom.value == "") {
		alert("Please provide Billing From date")
		frmGenInvoice.DPC_txtDateFrom.focus()
		return false
	}
	if (frmGenInvoice.DPC_txtDateFrom.value != "") {
		if (isDate(frmGenInvoice.DPC_txtDateFrom.value)==false) {
			frmGenInvoice.DPC_txtDateFrom.focus()
			return false
		}
	}
	
	if (frmGenInvoice.DPC_txtDateTo.value == "") {
		alert("Please provide Billing To date")
		frmGenInvoice.DPC_txtDateTo.focus()
		return false
	}
	if (frmGenInvoice.DPC_txtDateTo.value != "") {
		if (isDate(frmGenInvoice.DPC_txtDateTo.value)==false) {
			frmGenInvoice.DPC_txtDateTo.focus()
			return false
		}
	}
	
	if (frmGenInvoice.DPC_txtDateFrom.value != "" && frmGenInvoice.DPC_txtDateTo.value != "") {
		if (CompareDates(frmGenInvoice.DPC_txtDateFrom.value,frmGenInvoice.DPC_txtDateTo.value)==false) {
			frmGenInvoice.DPC_txtDateFrom.focus()
			return false
		}
	}
	
	if (frmGenInvoice.DPC_txtInvoiceDate.value == "") {
		alert("Please provide Invoice Date")
		frmGenInvoice.DPC_txtInvoiceDate.focus()
		return false
	}
	if (frmGenInvoice.DPC_txtInvoiceDate.value != "") {
		if (isDate(frmGenInvoice.DPC_txtInvoiceDate.value)==false) {
			frmGenInvoice.DPC_txtInvoiceDate.focus()
			return false
		}
	}

	if (confirm("Are you sure you want to generate invoices?\n There is no undo for this process.")) {
		frmGenInvoice.hidMode.value = "PROCESS";
		frmGenInvoice.submit();
	}
}

function cmdCancel_OnClick() {
	parent.frames[2].location = "generate_invoice.php?menu_id=" + frmUserGroupModule.hidMenuID.value;
	frmGenInvoice.submit();
}

function cmdSearch_onClick() {
	save_text();
	frmGenInvoice.hidMode.value = "SEARCH";
	frmGenInvoice.submit();
}

</script>