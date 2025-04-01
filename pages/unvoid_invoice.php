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

$dtBillingFrom = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
$dtBillingTo = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
$strRealPropertyCode = "";
$strMsg = "";

$sqlquerycbo="select * from m_real_property order by real_property_name";
$processcbo=odbc_exec($sqlconnect, $sqlquerycbo);
while (odbc_fetch_row($processcbo)) {
	$cbocharge .= "<option value=\"" . trim(odbc_result($processcbo,"real_property_code")) . "\">" . trim(strtoupper(odbc_result($processcbo,"real_property_name"))) . "</option>";
}

if ($strMode != "") {	
	$dtBillingFrom = $_POST["DPC_txtDateFrom"];
	$dtBillingTo = $_POST["DPC_txtDateTo"];
	$strRealPropertyCode = $_POST["cboRealProperty"];
	$strRealPropertyName = $_POST["hidRealPropertyName"];
	$strSortBy = $_POST["cboSortBy"];

	//echo $strRealPropertyName;
	switch ($strMode) {
		case "SEARCH":						
			$sqlquery="exec sp_u_Unvoid_Invoice_Search_View 'SEARCH','','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $strRealPropertyCode . "','" . $strSortBy . "','','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			//echo $sqlquery;
			break;
			
		case "UNVOID":						
			$i = 1;
			$j = 0;
			while ($i <= $_POST["hidRowCtr"]) {
				if (isset($_POST["chkSelect" . strval($i)])) {					
					$strInvoiceNo = $_POST["hidInvoiceNo" . strval($i)];
					$sqlquery="exec sp_u_Unvoid_Invoice 'UNVOID','" . $strInvoiceNo . "','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $strRealPropertyCode . "','','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					//echo $sqlquery;
					$process=odbc_exec($sqlconnect, $sqlquery);					
					$j++;
				}
				$i++;
			}
			if ($j > 0)
				$strMsg = "Invoice/s unvoided!";
			else
				$strMsg = "No invoice unvoided!";
			
			$sqlquery="exec sp_u_Unvoid_Invoice_Search_View 'SEARCH','','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $strRealPropertyCode . "','" . $strSortBy . "','','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			break;
		
	}
}	

//echo $sqlquery;
if ($strMsg != ""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 

?>
<html> 
<head> 
<title>UNVOID INVOICE</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
<script type="text/javascript" src="library/datepickercontrol/datepickercontrol.js"></script>
<link type="text/css" rel="stylesheet" href="library/datepickercontrol/datepickercontrol_green.css">
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color: #F3F5B4;">
<form name="frmUnvoidInvoice" id="frmUnvoidInvoice" method="post">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">UNVOID INVOICE
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			 </a></li>	
			  <li class="li_nc"><a href="#" onClick="javascript:cmdSearch_OnClick()">|&nbsp;&nbsp;&nbsp;Search&nbsp;&nbsp;&nbsp;|</a></li>			  			  
			  <li class="li_nc"><a href="#" onClick="javascript:cmdUnpost_OnClick()">Unvoid&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>			  			  
			  <li class="li_nc"><a href="#" onClick="javascript:cmdClose_OnClick()">Close&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>				  
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
										<td class="fieldname" align="right">INVOICE DATE FROM (mm/dd/yyyy) :</td>
										<td width="10"></td>
										<td><input type=text name="DPC_txtDateFrom" id="DPC_txtDateFrom" class="values" size="20" maxlength="10" value="<?php echo $dtBillingFrom; ?>"></td>
									</tr>	
									<tr>
										<td class="fieldname" align="right">TO (mm/dd/yyyy) :</td>
										<td width="10"></td>
										<td><input type=text name="DPC_txtDateTo" id="DPC_txtDateTo" class="values" size="20" maxlength="10"  value="<?php echo $dtBillingTo; ?>"></td>
									</tr>																																														
									<tr>
										<td class="fieldname" align="right">REAL PROPERTY :</td>
										<td width="10"></td>
									  <td>
											<select name="cboRealProperty" id="cboRealProperty" class="values" onChange="javascript:save_text();">
												<?php if ($strRealPropertyCode!= "") { ?>
													<option selected value="<?php echo $strRealPropertyCode; ?>"><?php echo $strRealPropertyName; ?></option>
												<?php } ?>
												<option value="">- ALL -</option>
												<?php echo $cbocharge; ?>
											</select>
										</td>
									</tr>			
									<tr>
										<td class="fieldname" align="right">SORT BY :</td>
										<td width="10"></td>
									  <td>
											<select name="cboSortBy" id="cboSortBy" class="values" onChange="javascript:cmdSearch_OnClick()">												
												<?php if ($strSortBy == "" || $strSortBy == "INVOICE NO.") {?>
													<option selected value="INVOICE NO.">Invoice No.</option>
													<option value="INVOICE DATE">Invoice Date</option>
													<option value="TENANT">Tenant</option>
													<option value="UNIT NO.">Unit No.</option>
												<?php } else if ($strSortBy == "INVOICE DATE") { ?>
													<option value="INVOICE NO.">Invoice No.</option>
													<option selected value="INVOICE DATE">Invoice Date</option>
													<option value="TENANT">Tenant</option>
													<option value="UNIT NO.">Unit No.</option>
												<?php } else if ($strSortBy == "TENANT") { ?>
													<option value="INVOICE NO.">Invoice No.</option>
													<option value="INVOICE DATE">Invoice Date</option>
													<option selected value="TENANT">Tenant</option>
													<option value="UNIT NO.">Unit No.</option>
												<?php } else { ?>
													<option value="INVOICE NO.">Invoice No.</option>
													<option value="INVOICE DATE">Invoice Date</option>
													<option value="TENANT">Tenant</option>
													<option selected value="UNIT NO.">Unit No.</option>
												<?php } ?>
											</select>
										</td>
									</tr>						
									<tr>
										<td class="fieldname">&nbsp;</td>
										<td width="10">&nbsp;</td>
										<td>&nbsp;</td>
									</tr>																	
								</table>
							</td>
						</tr>						
					</table>					
					<table width="900" style="border:1px solid #556b2f;padding: 30px 10px 5px; width:auto;">
						<tr height="30">
							<td width="41" align="center"  class="tablehdr">&nbsp;Sel&nbsp;
							</td>
							<td width="146" align="center"  class="tablehdr">&nbsp;Invoice No.&nbsp;
							</td>
							<td width="101" align="center"  class="tablehdr">&nbsp;Date&nbsp;
							</td>													
							<td width="272" align="center"  class="tablehdr">&nbsp;Tenant&nbsp;
							</td>							
							<td width="119" align="center"  class="tablehdr">&nbsp;Unit No.&nbsp;
							</td>	
							<?php
								$ctrCharge = 0;
								$sqlqueryCharges="select * from m_charges where charge_code in (select charge_code from z_tmp_unvoid_invoice_search where isnull(total_amount,0) > 0) order by charge_desc";	
								$processCharges=odbc_exec($sqlconnect, $sqlqueryCharges);
								$arrCharge = array();
								while(odbc_fetch_row($processCharges)){
									$charge_code_hdr = odbc_result($processCharges,"charge_code"); 
									$charge_desc_hdr = strtoupper(odbc_result($processCharges,"charge_desc")); 
									$ctrCharge++;
									$arrCharge[$ctrCharge] = $charge_code_hdr;
									//echo $charge_code;
							?>
								<td  align="center"  class="tablehdr">&nbsp;<?php echo $charge_desc_hdr; ?>&nbsp;
									<input type="hidden" name="hidChargeCode<?php echo $ctrCharge; ?>" id="hidChargeCode<?php echo $ctrCharge; ?>" value="<?php echo $charge_code_hdr; ?>" >
								</td>				
							<?php } ?>															
						</tr>	
						<?php	
						$ctr = 0;		
						$sqlquery="exec sp_u_Unvoid_Invoice_Search 'SEARCH','','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $strRealPropertyCode . "','" . $strSortBy . "','','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
						$process=odbc_exec($sqlconnect, $sqlquery);			
						//echo $sqlquery;
						while(odbc_fetch_row($process)) {
							$ctr++;
							$invoice_no = odbc_result($process,"invoice_no"); 
							$invoice_date = date("m/d/Y",(strtotime(odbc_result($process,"invoice_date"))+60*60*24*($OFFSET)));	
							$client_code = odbc_result($process,"client_code"); 
							$client_name = odbc_result($process,"client_name"); 
							$tenant_code = odbc_result($process,"tenant_code"); 
							$tenant_name = odbc_result($process,"tenant_name"); 
							$unit_no = odbc_result($process,"unit_no"); 	
							
							if ($ctr%2==1) 
								$rowColor = "98fb98";	
							else
								$rowColor = "ffffe0";		
							
						?>
							<tr bgcolor="<?php echo "$rowColor" ?>">
								<td align="center" style="border:1px;cursor:hand;">
									<input type="checkbox" name="chkSelect<?php echo "$ctr" ?>" id="chkSelect<?php echo "$ctr" ?>">
									<input type="hidden" name="hidInvoiceNo<?php echo "$ctr" ?>" id="hidInvoiceNo<?php echo "$ctr" ?>" value="<?php echo "$invoice_no" ?>">
								</td>
								<td  style="border:1px" class="values">&nbsp;<?php echo "$invoice_no";?>&nbsp;
								</td>
								<td style="border:1px" class="values" align="center">&nbsp;<?php echo "$invoice_date";?>&nbsp;
								</td>							
								<td  style="border:1px" class="values" title="Bill To:&nbsp;<?php echo "$client_name";?> (<?php echo "$client_code";?>)">&nbsp;<?php echo "$tenant_name";?>&nbsp;
								</td>							
								<td  style="border:1px" class="values">&nbsp;<?php echo "$unit_no";?>
								</td>
						<?php 							
							$iCtr = 0;							
							while ($ctrCharge !=  $iCtr) {
								$iCtr++;											
								//$charge_code = odbc_result($process,$_POST["hidChargeCode"] . $iCtr); 							
								//$charge_desc = odbc_result($process,"charge_desc"); 							
								//$total_amount = odbc_result($process,$_POST["hidChargeCode" . $iCtr]); 							
								$total_amount = odbc_result($process,$arrCharge[$iCtr]); 							
								//echo $arrCharge[4];
								//echo $total_amount;	
								//$total_list_amount = $total_list_amount + $total_amount;
							?>								
								<td style="border:1px" class="values" align="right">&nbsp;<?php echo numberformat($total_amount);?>&nbsp;
								</td>																
							<?php } ?>	
						</tr>
						<?php }						
							if ($ctr%2==1) 
								$rowColor = "ffffe0";			
							else
								$rowColor = "98fb98";	
								
							$total_list_amount = numberformat($total_list_amount);
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
	<input type="hidden" id="hidRealPropertyName" name="hidRealPropertyName" value=<?php echo $strRealPropertyName;?>>
	<input type="hidden" id="hidDateUploaded" name="hidDateUploaded" value="<?php echo $date_uploaded ; ?>">
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
   var w = frmUnvoidInvoice.cboRealProperty.selectedIndex;
   frmUnvoidInvoice.hidRealPropertyName.value = frmUnvoidInvoice.cboRealProperty.options[w].text;
   cmdSearch_OnClick();
   }

function txtKeyword_onKeyUp(e) {
	if (e.keyCode==13) {
		cmdSearch_onClick();
	}
}

function chkSelectAll_OnClick() {
	var ctr
	ctr = frmUnvoidInvoice.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmUnvoidInvoice.chkSelect" + i);
		if (frmUnvoidInvoice.chkSelectAll.checked == true) {
			obj.checked = true;
		}
		else {
			obj.checked = false;
		}
	}
}

function cmdSearch_OnClick() {
	if (frmUnvoidInvoice.DPC_txtDateFrom.value == "") {
		alert("Please provide Invoice Date From")
		frmUnvoidInvoice.DPC_txtDateFrom.focus()
		return false
	}
	if (frmUnvoidInvoice.DPC_txtDateFrom.value != "") {
		if (isDate(frmUnvoidInvoice.DPC_txtDateFrom.value)==false) {
			frmUnvoidInvoice.DPC_txtDateFrom.focus()
			return false
		}
	}
	
	if (frmUnvoidInvoice.DPC_txtDateTo.value == "") {
		alert("Please provide Invoice Date To")
		frmUnvoidInvoice.DPC_txtDateTo.focus()
		return false
	}
	if (frmUnvoidInvoice.DPC_txtDateTo.value != "") {
		if (isDate(frmUnvoidInvoice.DPC_txtDateTo.value)==false) {
			frmUnvoidInvoice.DPC_txtDateTo.focus()
			return false
		}
	}
		
	if (frmUnvoidInvoice.DPC_txtDateFrom.value != "" && frmUnvoidInvoice.DPC_txtDateTo.value != "") {
		if (CompareDates(frmUnvoidInvoice.DPC_txtDateFrom.value,frmUnvoidInvoice.DPC_txtDateTo.value)==false) {
			frmUnvoidInvoice.DPC_txtDateFrom.focus()
			return false
		}
	}

	frmUnvoidInvoice.hidMode.value = "SEARCH";
	frmUnvoidInvoice.submit();
}

function cmdUnpost_OnClick() {
	var j
	j=0
	totalctr = frmUnvoidInvoice.hidRowCtr.value;
	for (i=1;i<=totalctr;i++) {
		obj = eval("frmUnvoidInvoice.chkSelect" + i);
		if (obj.checked == true) {
			j++;
		}
	}

	if (j > 0) {
		if (confirm("Are you sure you want to UNVOID these invoices?")) {
			frmUnvoidInvoice.hidMode.value = "UNVOID";
			frmUnvoidInvoice.submit();
		}
	}
	else {
		alert("Select invoice/s to unvoid first");
	}
}

function cmdClose_OnClick() {
	parent.frames[2].location = "blank.htm";
	return false;
}

</script>