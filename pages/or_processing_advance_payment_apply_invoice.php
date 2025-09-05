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

$strORNo = $_GET["or_no"];
$strMode = trim($_POST["hidMode"]);
$strSaveMode = $_POST["hidSaveMode"];
//$dtBillingFrom = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
//$dtBillingTo = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
$dtBillingFrom = "";
$dtBillingTo = "";	
$strMsg = "";

if ($strMode != "") {	
	$dtBillingFrom = $_POST["DPC_txtDateFrom"];
	$dtBillingTo = $_POST["DPC_txtDateTo"];
	$strSortBy = $_POST["cboSortBy"];

	//echo $strSortBy;
	switch ($strMode) {		
		case "SAVE":
			$i = 1;
			$j = 0;
			while ($i <= $_POST["hidRowCtr"]) {				
				if (($_POST["txtAmount" . strval($i)]) > 0) {		
					$strInvoiceNo = $_POST["hidInvoiceNo" . strval($i)];			
					$intInvoiceDetailID = $_POST["hidInvoiceDetailID" . strval($i)];			
					$dblAmount = $_POST["txtAmount" . strval($i)];			
					$sqlquery="exec sp_t_ORProcessing_AdvancePayment_Save '" . $strORNo . "','" . $strInvoiceNo . "'," . $intInvoiceDetailID . "," . $dblAmount . ",'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					//echo $sqlquery;
					$process=odbc_exec($sqlconnect, $sqlquery);
					$j++;
				}
				$i++;
				$strMsg = "Payment/s applied! Invoice/s tagged can be viewed in Payment module.";
			}
		break;
	}
}	

$sqlqueryOR="exec sp_t_ORProcessing_AdvancePayment_List '" . $strORNo . "','','',''";	
$processOR=odbc_exec($sqlconnect, $sqlqueryOR);

//echo $sqlquery;
if ($strMsg != "" && $strMode==""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 

?>
<html> 
<head> 
<title>ADVANCE PAYMENT - OR PROCESSING</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
<script type="text/javascript" src="library/datepickercontrol/datepickercontrol.js"></script>
<link type="text/css" rel="stylesheet" href="library/datepickercontrol/datepickercontrol_green.css">
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color: #F3F5B4;">
<form name="frmORProcessingAPInvoice" id="frmORProcessingAPInvoice" method="post" action="or_processing_advance_payment_apply_invoice.php?menu_id=<?php echo $menu_id;?>&or_no=<?php echo $strORNo;?>">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a href="#" onClick="javascript:change_loc('payment_main.php')"><u>OR PROCESSING</u></a></li>	
			  <li class="li_nc"><a name="MODULE NAME">>></a></li>	
			  <li class="li_nc"><a href="#" onClick="javascript:change_loc('or_processing_advance_payment_list.php?menu_id=12')"><u>ADVANCE PAYMENT</u></a></li>	
			  <li class="li_nc"><a name="MODULE NAME">>>&nbsp;&nbsp;&nbsp;&nbsp;APPLY INVOICE
			  &nbsp;&nbsp;	
			 </a></li>	
			  <li class="li_nc"><a href="#" onClick="javascript:cmdBuild_OnClick()">|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Build List&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>			  			  
			  <li class="li_nc"><a href="#" onClick="javascript:cmdSave_OnClick()">Save&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>			  			  
			  <li class="li_nc"><a href="#" onClick="javascript:change_loc('or_processing_advance_payment_list.php?menu_id=12')">Back&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>				  
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
								<table width="850" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
									<tr height="30">																
										<td  width="12%" class="tablehdr" align="center">OR No.
										</td>
										<td  width="10%" class="tablehdr" align="center">Date
										</td>
										<td width="31%" class="tablehdr" align="center">Client
										</td>	
										<td width="17%" class="tablehdr" align="center">Unit
										</td>			
										<td width="9%" class="tablehdr" align="center">Total Amount
										</td>												
										<td width="8%" class="tablehdr" align="center">Balance Amount
										</td>			
										<td width="9%" class="tablehdr" align="center">Deducted Amount
										</td>								
									</tr>
									<?php
									while(odbc_fetch_row($processOR)){
										$strORNo = odbc_result($processOR,"or_no");
										$dtOR = odbc_result($processOR,"or_date");
										$strClientName = replacedoublequotes(odbc_result($processOR,"client_name"));							
										$unit_no = replacedoublequotes(odbc_result($processOR,"unit_no"));							
										$dblTotalAmount = odbc_result($processOR,"total_amount");
										$dblDeductedAmount = odbc_result($processOR,"deducted_amount");
										$dblBalanceAmount =  odbc_result($processOR,"balance_amount");
										$status = odbc_result($processOR,"status");
										
										if ($dblBalanceAmount > 0) {
											$ctr = $ctr+1;
											
											if ($ctr%2==1) 
												$rowColor = "98fb98";	
											else
												$rowColor = "ffffe0";	
									?>
									<tr bgcolor="<?php echo "$rowColor"; ?>">																	
										<td width="12%" class="values">&nbsp;<?php echo "$strORNo";?>
											<input type="hidden" name="hidInvoiceNo<?php echo $ctr;?>" id="hidInvoiceNo<?php echo $ctr;?>" value="<?php echo "$strORNo";?>">														
										</td>
										<td width="10%" class="values" align="center">&nbsp;<?php echo "$dtOR";?>&nbsp;
										</td>
										<td width="31%" class="values">&nbsp;<?php echo "$strClientName";?>&nbsp;
											<input type="hidden" name="hidClientName<?php echo $ctr;?>" id="hidClientName<?php echo $ctr;?>" value="<?php echo "$strClientName";?>">														
										</td>	
										<td width="17%" class="values">&nbsp;<?php echo "$unit_no";?>
										</td>														
										<td width="9%" class="values" align="right">&nbsp;<?php echo "$dblTotalAmount";?>&nbsp;
										</td>					
										<td width="8%" class="values" align="right">&nbsp;<?php echo "$dblBalanceAmount";?>&nbsp;
											<input type="hidden" name="hidBalanceAmount" id="hidBalanceAmount" value=<?php echo "$dblBalanceAmount";?>>														
										</td>					
										<td width="9%" class="values" align="right">&nbsp;<?php echo "$dblDeductedAmount";?>&nbsp;
										</td>											
									</tr>						
									<?php } } ?>
								</table>		
								<br>		
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
										<td class="fieldname" align="right">SORT BY :</td>
										<td width="10"></td>
										<td>
											<select name="cboSortBy" id="cboSortBy" class="values" onChange="javascript:cmdBuild_OnClick()">												
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
										<td class="fieldname" align="right">&nbsp;</td>
										<td width="10">&nbsp;</td>
										<td>&nbsp;</td>
									</tr>																			
								</table>
							</td>
						</tr>						
					</table>			
					<table>
						<tr>
							<td class="values" style="color:#CC0000">Note: Invoices listed have balance amounts greater than or equal than the OR balance amount.</td>
						</tr>
					</table>	
					<table width="1000" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
						<tr height="30">									
							<td width="4%" class="tablehdr" align="center">&nbsp;Sel&nbsp;
							</td>			
							<td  width="12%" class="tablehdr" align="center">Invoice No.
							</td>
							<td  width="8%" class="tablehdr" align="center">Date
							</td>
							<td width="19%" class="tablehdr" align="center">Tenant
							</td>		
							<td width="14%" class="tablehdr" align="center">Unit
							</td>	
							<td width="13%" class="tablehdr" align="center">Charge
							</td>			
							<td width="8%" class="tablehdr" align="center">&nbsp;Amount to be Applied&nbsp;
							</td>												
							<td width="7%" class="tablehdr" align="center">Balance Amount
							</td>			
							<td width="7%" class="tablehdr" align="center">Paid Amount
							</td>	
							<td width="8%" class="tablehdr" align="center">Total Amount
							</td>								
						</tr>
						<?php
						$sqlquery="exec sp_t_ORProcessing_AdvancePayment_InvoiceList '" . $strORNo . "','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $strSortBy . "'";	
						$process=odbc_exec($sqlconnect, $sqlquery);
						//echo $sqlquery;			
						$ctr = 0;
						$total_or_list_amount = 0;
						while(odbc_fetch_row($process)){
							$strInvoiceNo = odbc_result($process,"invoice_no");
							$dtInvoice = odbc_result($process,"invoice_date");							
							$invoice_detail_id = odbc_result($process,"invoice_detail_id");							
							$strClientName = replacedoublequotes(odbc_result($process,"client_name"));							
							$dblTotalChargeAmount = odbc_result($process,"total_amount");
							$dblPaidAmount = odbc_result($process,"paid_amount");
							$dblBalanceAmount =  odbc_result($process,"balance_amount");
							$unit_no = odbc_result($process,"unit_no");
							$charge_desc = odbc_result($process,"charge_desc");
							
							$total_or_list_amount = $total_or_list_amount + $dblBalanceAmount;
							if ($dblBalanceAmount > 0) {
								$ctr = $ctr+1;
								
								if ($ctr%2==1) 
									$rowColor = "98fb98";	
								else
									$rowColor = "ffffe0";	
						?>
						<tr bgcolor="<?php echo "$rowColor"; ?>">			
							<td width="4%" align="center" style="border:1px">
								<input type="checkbox" name="chkSelect<?php echo "$ctr";?>" id="chkSelect<?php echo "$ctr";?>" onClick="javascript:chkSelect_OnClick(<?php echo "$ctr";?>);" title="Apply Total Balance Amount">
							</td>											
							<td width="12%" class="values">&nbsp;<?php echo "$strInvoiceNo";?>
								<input type="hidden" name="hidInvoiceNo<?php echo $ctr;?>" id="hidInvoiceNo<?php echo $ctr;?>" value="<?php echo "$strInvoiceNo";?>">														
								<input type="hidden" name="hidInvoiceDetailID<?php echo "$ctr";?>" id="hidInvoiceDetailID<?php echo "$ctr";?>" value="<?php echo "$invoice_detail_id";?>">					
							</td>
							<td width="8%" class="values" align="center">&nbsp;<?php echo "$dtInvoice";?>&nbsp;
							</td>
							<td width="19%" class="values">&nbsp;<?php echo "$strClientName";?>&nbsp;
								<input type="hidden" name="hidClientName<?php echo $ctr;?>" id="hidClientName<?php echo $ctr;?>" value="<?php echo "$strClientName";?>">														
							</td>	
							<td width="14%" class="values">&nbsp;<?php echo "$unit_no";?>
							</td>	
							<td width="13%" class="values">&nbsp;<?php echo "$charge_desc";?>
							</td>																			
							<td width="8%" class="values" align="center">&nbsp;
								<input type="text" name="txtAmount<?php echo $ctr;?>" id="txtAmount<?php echo $ctr;?>" class="values" style="text-align:right" size="6" maxlength="11" value="0.00" onKeyUp="javascript:DisplayTotalORAmount();">
							</td>	
							<td width="7%" class="values" align="right">&nbsp;<?php echo "$dblBalanceAmount";?>&nbsp;	
								<input type="hidden" name="hidInvoiceBalanceAmount<?php echo $ctr;?>" id="hidInvoiceBalanceAmount<?php echo $ctr;?>" value=<?php echo "$dblBalanceAmount";?>>														
							</td>					
							<td width="7%" class="values" align="right">&nbsp;<?php echo "$dblPaidAmount";?>&nbsp;
							</td>					
							<td width="8%" class="values" align="right">&nbsp;<?php echo "$dblTotalChargeAmount";?>&nbsp;
							</td>											
						</tr>						
						<?php } } 				
							if ($ctr%2==1) 
								$rowColor = "ffffe0";			
							else
								$rowColor = "98fb98";	
								
							$total_or_list_amount = numberformat($total_or_list_amount);
						?>
						<tr bgcolor="<?php echo "$rowColor" ?>">
							<td width="4%" align="center">&nbsp;
								
							</td>
							<td width="12%" style="border:1px" class="values">&nbsp;
							</td>
							<td width="8%" style="border:1px" class="values" align="left">&nbsp;
							</td>
							<td width="19%" style="border:1px" class="values">&nbsp;<b>TOTAL AMOUNT</b>
							</td>							
							<td width="14%" style="border:1px" class="values">&nbsp;
							</td>		
							<td width="13%" style="border:1px" class="values">&nbsp;
							</td>					
							<td width="8%" style="border:1px" class="values" align="right">
								<input type="text" name="txtTotalORAmount" id="txtTotalORAmount" class="values" style="text-align:right; font-weight:bold; border:none; background-color:<?php echo "$rowColor" ?>;" size="6" readonly value="$0.00">								
								
							</td>				
							<td width="7%" style="border:1px" class="values" align="right"><b>&nbsp;$<?php echo $total_or_list_amount;?>&nbsp;</b>
							</td>
							<td width="7%" style="border:1px" class="values">&nbsp;
							</td>			
							<td width="8%" class="values" align="right">&nbsp;
							</td>	
						</tr>
					</table>				
					<table>
						<tr>
							<td class="values">&nbsp;
								<input type="checkbox" name="chkSelectAll" id="chkSelectAll" onClick="javascript:chkSelectAll_OnClick();" style="cursor:hand">&nbsp;APPLY ALL
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
	<input type="hidden" id="hidORNo" name="hidORNo" value=<?php echo $strORNo;?>>
</form>
</body> 
</html>

<script language="javascript" src="jsp/function.js"></script>
<script language="javascript">
function hov(loc,cls) {   
	if(loc.className)   
	loc.className=cls;   
} 

function save_text()
   {
   var w = frmORProcessingAPInvoice.cboRealProperty.selectedIndex;
   frmORProcessingAPInvoice.hidRealPropertyName.value = frmORProcessingAPInvoice.cboRealProperty.options[w].text;
   }

function txtKeyword_onKeyUp(e) {
	if (e.keyCode==13) {
		cmdSearch_onClick();
	}
}

function chkSelect_OnClick(i) {
	obj = eval("frmORProcessingAPInvoice.chkSelect" + i);
	obj1 = eval("frmORProcessingAPInvoice.txtAmount" + i);
	obj2 = eval("frmORProcessingAPInvoice.hidInvoiceBalanceAmount" + i);
	if (obj.checked == true) {			
		obj1.value = obj2.value;
	}
	else {
		obj1.value = "0.00";
	}
	DisplayTotalORAmount();
}

function chkSelectAll_OnClick() {
	var ctr
	ctr = frmORProcessingAPInvoice.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmORProcessingAPInvoice.chkSelect" + i);
		obj1 = eval("frmORProcessingAPInvoice.txtAmount" + i);
		obj2 = eval("frmORProcessingAPInvoice.hidInvoiceBalanceAmount" + i);
		if (frmORProcessingAPInvoice.chkSelectAll.checked == true) {
			obj.checked = true;			
			obj1.value = obj2.value;
		}
		else {
			obj.checked = false;
			obj1.value = "0.00";
		}
	}
	DisplayTotalORAmount();
}

function cmdBuild_OnClick() {
	if ((frmORProcessingAPInvoice.DPC_txtDateFrom.value == "" && frmORProcessingAPInvoice.DPC_txtDateTo.value != "")) {
		alert("Please provide Invoice Date From")
		frmORProcessingAPInvoice.DPC_txtDateFrom.focus()
		return false
	}
	if ((frmORProcessingAPInvoice.DPC_txtDateFrom.value != "" && frmORProcessingAPInvoice.DPC_txtDateTo.value == "")) {
		alert("Please provide Invoice Date To")
		frmORProcessingAPInvoice.DPC_txtDateTo.focus()
		return false
	}
	
	if (frmORProcessingAPInvoice.DPC_txtDateFrom.value != "") {
		if (isDate(frmORProcessingAPInvoice.DPC_txtDateFrom.value)==false) {
			frmORProcessingAPInvoice.DPC_txtDateFrom.focus()
			return false
		}
	}
	
	if (frmORProcessingAPInvoice.DPC_txtDateTo.value != "") {
		if (isDate(frmORProcessingAPInvoice.DPC_txtDateTo.value)==false) {
			frmORProcessingAPInvoice.DPC_txtDateTo.focus()
			return false
		}
	}
	
	if (frmORProcessingAPInvoice.DPC_txtDateFrom.value != "" && frmORProcessingAPInvoice.DPC_txtDateTo.value != "") {
		if (CompareDates(frmORProcessingAPInvoice.DPC_txtDateFrom.value,frmORProcessingAPInvoice.DPC_txtDateTo.value)==false) {
			frmORProcessingAPInvoice.DPC_txtDateFrom.focus()
			return false
		}
	}
	
	frmORProcessingAPInvoice.hidMode.value = "BUILD";
	frmORProcessingAPInvoice.submit();
}

function DisplayTotalORAmount() {
	var j
	var dblTotal
	j=0
	dblTotal = 0
	totalctr = frmORProcessingAPInvoice.hidRowCtr.value;
	for (i=1;i<=totalctr;i++) {
		obj = eval("frmORProcessingAPInvoice.txtAmount" + i);
		if (isNaN(obj.value)) {
			alert("Invalid numeric value")
			obj.focus()
			return false;
		}		
		dblTotal = dblTotal + Number(obj.value);
	}
	frmORProcessingAPInvoice.txtTotalORAmount.value ="$" + numberWithCommas(dblTotal.toFixed(2));
}

function cmdSave_OnClick() {
	var j
	var dblTotal
	j=0
	dblTotal = 0
	totalctr = frmORProcessingAPInvoice.hidRowCtr.value;
	for (i=1;i<=totalctr;i++) {
		obj = eval("frmORProcessingAPInvoice.txtAmount" + i);
		if (isNaN(obj.value)) {
			alert("Invalid numeric value")
			obj.focus()
			return false;
		}
		obj1 = eval("frmORProcessingAPInvoice.hidInvoiceBalanceAmount" + i);
		if (Number(obj.value) > Number(obj1.value)) {
			alert("Amount to be Applied should not be greater than the Invoice Balance Amount")
			obj.focus()
			return false;
		}
		dblTotal = dblTotal + Number(obj.value);
	}
	if (dblTotal == 0) {
		alert("Please input at least one Amount to be Applied")
		return false;
	}
	if (dblTotal > frmORProcessingAPInvoice.hidBalanceAmount.value) {
		alert("Total Amount to be Applied should not be greater than the OR Balance Amount")
		return false;
	}
	if (confirm("Are you sure you want to apply these invoice/s?")) {
		frmORProcessingAPInvoice.hidMode.value = "SAVE";
		frmORProcessingAPInvoice.submit();
	}
}

function cmdCancel_OnClick() {
	parent.frames[2].location = "advance_payment_or_posting.php?menu_id=" + frmORProcessingAPInvoice.hidMenuID.value;
	frmORProcessingAPInvoice.submit();
}

function cmdORBreakdown_OnClick(pCtr) {
	obj = eval("frmORProcessingAPInvoice.hidInvoiceNo" + pCtr);
	obj1 = eval("frmORProcessingAPInvoice.hidClientName" + pCtr);
	parent.frames[2].location = "advance_payment_or_processing_breakdown_main.php?menu_id=" + frmORProcessingAPInvoice.hidMenuID.value + "&invoice_no=" + obj.value + "&client_name=" + obj1.value;
	return false;
}

</script>