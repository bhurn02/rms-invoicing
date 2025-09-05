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
$strORNo = "";
$strStatusDesc = "";
$dblORAmountHeader = "0.00";
$dblTotalORAmount = 0;
$intPaymentDetailID = 0;
$strInvoiceNo = "";
$intInvoiceDetailID = 0;
$intInvoiceDetailChargesID = 0;		
$dblTotalChargeAmount = 0;	
$dblORAmount = 0;	
$intTotalDetailCnt = 0;
$strMsg = "";
$strMenu = "";

$sqlquerycbo="select * from m_charges order by charge_desc";
$processcbo=odbc_exec($sqlconnect, $sqlquerycbo);
while (odbc_fetch_row($processcbo)) {
	$cbocharge .= "<option value=\"" . trim(odbc_result($processcbo,"charge_code")) . "\">" . trim(strtoupper(odbc_result($processcbo,"charge_desc"))) . "</option>";
}

if ($_GET["mode"] == "FIND") {
	$strMenu = $_GET["menu"];
	$strORNo = $_GET["or_no"];
	$strMode = "FIND";
}

if ($strMode != "") {		
	if ($strMode != "FIND") {
		$strMenu =  $_POST["hidMenu"];
		$strORNo = $_POST["txtORNo"];
	}
		
	switch ($strMode) {
		
		case "SAVE":			
			$strORNo = $_POST["hidORNo"];
			$strClientCode = $_POST["hidClientCode"];
			$strClientName = $_POST["hidClientName"];
			$i = 1;
			while ($i <= $_POST["hidRowCtr"]) {			
				$intPaymentDetailID = $_POST["hidPaymentDetailID" . strval($i)];							
				$strInvoiceNo = $_POST["hidInvoiceNo" . strval($i)];							
				$intInvoiceDetailID = $_POST["hidInvoiceDetailID" . strval($i)];		
				$intInvoiceDetailChargesID = $_POST["hidInvoiceDetailChargesID" . strval($i)];		
				$dblORAmount = $_POST["txtORAmount" . strval($i)];		
				$sqlquery="exec sp_t_Payment_Detail 'SAVE'," . $intPaymentDetailID . ",'" . $strORNo . "','" . $strInvoiceNo . "'," . $intInvoiceDetailID . ",0," . $dblORAmount . ",'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
				$process=odbc_exec($sqlconnect, $sqlquery);				
				//echo $sqlquery;				
				$i++;
			}
			$strMsg = "Record saved!";
			
			$sqlquery="exec sp_t_Payment_Detail 'RETRIEVE_HDR',0,'" . $strORNo . "','" . $strInvoiceNo . "',0,0,0,'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			//echo $sqlquery;
			$process=odbc_exec($sqlconnect, $sqlquery);
			if (odbc_fetch_row($process)) {
					$strORNo = replacedoublequotes(odbc_result($process,"or_no"));
					$strClientCode = replacedoublequotes(odbc_result($process,"client_code"));
					$strClientName = replacedoublequotes(odbc_result($process,"client_name"));
					$dblORAmountHeader = odbc_result($process,"or_amount_header");
					$dblTotalORAmount = odbc_result($process,"total_or_amount");
					$intTotalDetailCnt = odbc_result($process,"payment_detail_total_rec");
					$strStatus = odbc_result($process,"status");
					$strStatusDesc = odbc_result($process,"status_desc");
					$strMode = "RETRIEVE";
					$strSaveMode = "EDIT";
					
					$sqlqueryCharges="exec sp_t_Payment_Detail 'RETRIEVE_DTL',0,'" . $strORNo . "','',0,0,0,'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					$processCharges=odbc_exec($sqlconnect, $sqlqueryCharges);
					//echo $sqlqueryCharges;
			}
			break;
			
		case "DELETE":			
			$strORNo = $_POST["hidORNo"];
			$strClientCode = $_POST["hidClientCode"];
			$strClientName = $_POST["hidClientName"];
			$i = 1;
			while ($i <= $_POST["hidRowCtr"]) {			
				if (isset($_POST["chkDelete" . strval($i)])) {
					$intPaymentDetailID = $_POST["hidPaymentDetailID" . strval($i)];							
					$strInvoiceNo = $_POST["hidInvoiceNo" . strval($i)];							
					$intInvoiceDetailID = $_POST["hidInvoiceDetailID" . strval($i)];		
					$intInvoiceDetailChargesID = $_POST["hidInvoiceDetailChargesID" . strval($i)];		
					$dblORAmount = $_POST["txtORAmount" . strval($i)];		
					$sqlquery="exec sp_t_Payment_Detail 'DELETE'," . $intPaymentDetailID . ",'" . $strORNo . "','" . $strInvoiceNo . "'," . $intInvoiceDetailID . ",0," . $dblORAmount . ",'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					$process=odbc_exec($sqlconnect, $sqlquery);				
				}
				//echo $sqlquery;				
				$i++;
			}
			$strMsg = "Record/s deleted!";
			
			$sqlquery="exec sp_t_Payment_Detail 'RETRIEVE_HDR',0,'" . $strORNo . "','" . $strInvoiceNo . "',0,0,0,'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			//echo $sqlquery;
			$process=odbc_exec($sqlconnect, $sqlquery);
			if (odbc_fetch_row($process)) {
					$strORNo = replacedoublequotes(odbc_result($process,"or_no"));
					$strClientCode = replacedoublequotes(odbc_result($process,"client_code"));
					$strClientName = replacedoublequotes(odbc_result($process,"client_name"));
					$dblORAmountHeader = odbc_result($process,"or_amount_header");
					$dblTotalORAmount = odbc_result($process,"total_or_amount");
					$intTotalDetailCnt = odbc_result($process,"payment_detail_total_rec");
					$strStatus = odbc_result($process,"status");
					$strStatusDesc = odbc_result($process,"status_desc");
					$strMode = "RETRIEVE";
					$strSaveMode = "EDIT";
					
					$sqlqueryCharges="exec sp_t_Payment_Detail 'RETRIEVE_DTL',0,'" . $strORNo . "','',0,0,0,'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					$processCharges=odbc_exec($sqlconnect, $sqlqueryCharges);
					//echo $sqlqueryCharges;
			}
			break;
			
		case "RETRIEVE" || "FIND":
			$sqlquery="exec sp_t_Payment_Detail 'RETRIEVE_HDR',0,'" . $strORNo . "','" . $strInvoiceNo . "',0,0,0,'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			//echo $sqlquery;
			$process=odbc_exec($sqlconnect, $sqlquery);
			if (odbc_fetch_row($process)) {
					$strORNo = replacedoublequotes(odbc_result($process,"or_no"));
					$strClientCode = replacedoublequotes(odbc_result($process,"client_code"));
					$strClientName = replacedoublequotes(odbc_result($process,"client_name"));
					$dblORAmountHeader = odbc_result($process,"or_amount_header");
					$dblTotalORAmount = odbc_result($process,"total_or_amount");
					$intTotalDetailCnt = odbc_result($process,"payment_detail_total_rec");
					$strStatus = odbc_result($process,"status");
					$strStatusDesc = odbc_result($process,"status_desc");
					$strMode = "RETRIEVE";
					$strSaveMode = "EDIT";
					
					$sqlqueryCharges="exec sp_t_Payment_Detail 'RETRIEVE_DTL',0,'" . $strORNo . "','',0,0,0,'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					$processCharges=odbc_exec($sqlconnect, $sqlqueryCharges);
					//echo $sqlqueryCharges;
			}
			else {
				$strMode = "";
			}
			break;
	}
}

if ($strMsg != ""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 

function replacesinglequote($pStr) {
	return trim(str_replace("'","''",$pStr));
}

function replacedoublequotes($pStr) {
	return trim(str_replace("''","'",$pStr));
}

?>
<html> 
<head> 
<title>PAYMENT DETAIL</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
</head> 
<body style="margin:'0';background-color: #F3F5B4;">
<form name="frmTenantDepositDtl" id="frmTenantDepositDtl" method="post" action="tenant_deposit_detail.php?menu_id=<?php echo $menu_id;?>">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">TENANT SECURITY DEPOSIT >> DETAIL
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		   	  	<?php if ($strMenu != "DETAIL") { ?>
					&nbsp;&nbsp;&nbsp;&nbsp;
				 <?php } ?>
			 </a></li>	
			 <?php if ($strMode != "RETRIEVE") { ?>
					<li class="li_nc"><a href="#" onClick="javascript:cmdRetrieve_OnClick()">|&nbsp;&nbsp;&nbsp;Retrieve&nbsp;&nbsp;&nbsp;|</a></li>			  
			 <?php } else { ?>		
			 		<li><a name="Retrieve" style="color: #666666">|&nbsp;&nbsp;&nbsp;Retrieve&nbsp;&nbsp;&nbsp;|</a></li>					
			  <?php } ?>
			 
			  <?php if ($strMode != "RETRIEVE" || $strStatus != "") { ?>			  		
			  		<li><a name="Save" style="color: #666666">Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
					<li><a name="Delete" style="color: #666666">Delete&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } else { ?>				  		
			  		 <?php if ($intTotalDetailCnt > 0) { ?>		
						<li class="li_nc"><a href="#" onClick="javascript:cmdSave_OnClick()">Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
						<li class="li_nc"><a href="#" onClick="javascript:cmdDelete_OnClick()">Delete&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
					<?php } else { ?>	
						<li><a name="Save" style="color: #666666">Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
						<li><a name="Delete" style="color: #666666">Delete&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
					 <?php } ?>	
			  <?php } ?>	
			  <li class="li_nc"><a href="#" onClick="javascript:change_loc('tenant_deposit_detail_list.php?menu_id=<?php echo $menu_id;?>')">Find&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <li class="li_nc"><a href="#" onClick="javascript:cmdCancel_OnClick()">Cancel&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>			  
			  <?php if ($strMenu == "DETAIL") { ?>
			  	<li class="li_nc"><a href="#" onClick="javascript:change_loc('tenant_deposit.php?menu_id=<?php echo $menu_id;?>&mode=FIND&or_no=<?php echo $strORNo; ?>')">Back&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
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
										<td class="fieldname">OR NO. :<em class="requiredRed">*</em></td>
										<td width="20">&nbsp;</td>		
										<?php if ($strMode == "RETRIEVE") {?>								
											<td>
												<input type=text name="txtORNo" id="txtORNo" disabled class="values" size="20" value="<?php echo $strORNo;?>">									    
											</td>
										<?php } else {?>								
											<td>
												<input type=text name="txtORNo" id="txtORNo" class="values" size="20" value="<?php echo $strORNo;?>">									    
											</td>
										<?php }?>	
									   <input type="hidden" id="hidORNo" name="hidORNo" value="<?php echo $strORNo;?>">
									</tr>	
									<tr>
										<td class="fieldname">CLIENT :<em class="requiredRed">*</em></td>
										<td width="20">&nbsp;</td>			
										<td><input type=text name="txtClientName" id="txtClientName" disabled class="values" size="60" value="<?php echo $strClientName;?>"></td>										
										<input type="hidden" id="hidClientCode" name="hidClientCode" value="<?php echo $strClientCode;?>">
									</tr>													
									<?php if ($strStatusDesc != "") { ?>
										<tr>
											<td class="fieldname">STATUS :</td>
											<td width="20">&nbsp;</td>			
											<td class="values" style="color:red"><b><?php echo $strStatusDesc;?></b>
										</tr>					
									<?php } ?>														
								</table>
							</td>
						</tr>
					</table>
					<p></p>
					<table width="900" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
						<tr height="30">
							<td width="4%" class="tablehdr" align="center">&nbsp;Del&nbsp;
							</td>
							<td width="14%" class="tablehdr" align="center">&nbsp;Real Property Code / Building Code / Unit No.&nbsp;
							</td>
							<td  width="9%" class="tablehdr" align="center">&nbsp;Date&nbsp;
							</td>
							<td  width="22%" class="tablehdr" align="center">&nbsp;Tenant&nbsp;
							</td>
							<td  width="19%" class="tablehdr" align="center">&nbsp;Charge&nbsp;
							</td>
							<td  width="9%" class="tablehdr" align="center">&nbsp;OR Amount&nbsp;
							</td>
							<td width="7%" class="tablehdr" align="center">Balance Amount
							</td>			
							<td width="8%" class="tablehdr" align="center">Paid Amount
							</td>	
							<td width="8%" class="tablehdr" align="center">Total Amount
							</td>	
						</tr>
						<?php
						$ctr = 0;
						while(odbc_fetch_row($processCharges)) {
							$intPaymentDetailID = odbc_result($processCharges,"payment_detail_id");
							$strInvoiceNo = odbc_result($processCharges,"invoice_no");
							$dtInvoice = odbc_result($processCharges,"invoice_date");
							$intInvoiceDetailID = odbc_result($processCharges,"invoice_detail_id");
							$intInvoiceDetailChargesID = odbc_result($processCharges,"invoice_detail_charges_id");
							$strTenantCode = replacedoublequotes(odbc_result($processCharges,"tenant_code"));
							$strTenantName = replacedoublequotes(odbc_result($processCharges,"tenant_name"));
							$strChargeCode = replacedoublequotes(odbc_result($processCharges,"charge_code"));
							$strChargeDesc = replacedoublequotes(odbc_result($processCharges,"charge_desc"));
							$dblORAmount = odbc_result($processCharges,"or_amount");
							$dblTotalChargeAmount = odbc_result($processCharges,"total_charge_amount");
							$dblPaidAmount = odbc_result($processCharges,"paid_amount");
							$dblBalanceAmount = odbc_result($processCharges,"balance_amount");
							if ($dblBalanceAmount == 0) 
								$dblBalanceAmount = ".00";
							$ctr = $ctr+1;
							
							if ($ctr%2==1) 
								$rowColor = "98fb98";	
							else
								$rowColor = "ffffe0";			
						?>
						<tr id="editRow<?php echo "$ctr";?>" name="editRow<?php echo "$ctr";?>" style="cursor:hand" bgcolor="<?php echo "$rowColor" ?>">
							<td width="4%" align="center" style="border:1px">
								<input type="checkbox" name="chkDelete<?php echo "$ctr";?>" id="chkDelete<?php echo "$ctr";?>">
								<input type="hidden" id="hidEditRecID<?php echo "$ctr";?>" name="hidEditRecID<?php echo "$ctr";?>" value="<?php echo $rec_id;?>">
							</td>
							<td width="14%" style="border:1px" class="values" align="left">
								&nbsp;<?php echo $strInvoiceNo;?>
								<input type="hidden" name="hidPaymentDetailID<?php echo "$ctr";?>" id="hidPaymentDetailID<?php echo "$ctr";?>" value=<?php echo "$intPaymentDetailID";?>>						
								<input type="hidden" name="hidInvoiceNo<?php echo "$ctr";?>" id="hidInvoiceNo<?php echo "$ctr";?>" value="<?php echo "$strInvoiceNo";?>">						
								<input type="hidden" name="hidInvoiceDetailID<?php echo "$ctr";?>" id="hidInvoiceDetailID<?php echo "$ctr";?>" value="<?php echo "$intInvoiceDetailID";?>">						
								<input type="hidden" name="hidInvoiceDetailChargesID<?php echo "$ctr";?>" id="hidInvoiceDetailChargesID<?php echo "$ctr";?>" value="<?php echo "$intInvoiceDetailChargesID";?>">						
							</td>
							<td width="9%" style="border:1px" class="values" align="center">
								<?php echo $dtInvoice;?>
							</td>
							<td width="22%" style="border:1px" class="values">&nbsp;<?php echo $strTenantName;?>
							</td>
							<td width="19%" style="border:1px" class="values" align="left">
								&nbsp;<?php echo $strChargeDesc;?>
							</td>	
							<?php if ($strStatus != "") {?>
								<td width="9%" style="border:1px" class="values" align="center">								
									<?php echo $dblORAmount;?>
								</td>	
							<?php } else {?>
								<td width="9%" style="border:1px" class="values" align="center">								
									<input type="text" name="txtORAmount<?php echo $ctr;?>" id="txtORAmount<?php echo $ctr;?>" class="values" style="text-align:right" size="6" maxlength="11" value="<?php echo $dblORAmount;?>">
								</td>	
							<?php } ?>
							<td width="7%" style="border:1px" class="values" align="right">
								<?php echo $dblBalanceAmount;?>&nbsp;
								<input type="hidden" name="hidBalanceAmount<?php echo $ctr;?>" id="hidBalanceAmount<?php echo $ctr;?>" value=<?php echo $dblBalanceAmount;?>>
							</td>		
							<td width="8%" style="border:1px" class="values" align="right">
								<?php echo $dblPaidAmount;?>&nbsp;								
							</td>	
							<td width="8%" style="border:1px" class="values" align="right">
								<?php echo $dblTotalChargeAmount;?>&nbsp;
							</td>					
						</tr>
						<?php } ?>
						<?php
								if ($ctr%2==1) 
									$rowColor = "ffffe0";	
								else
									$rowColor = "98fb98";								
						?>
						
						<?php
								if ($ctr%2==1) 
									$rowColor = "ffffe0";	
								else
									$rowColor = "98fb98";								
									
							if ($ctr > 0) {
						?>
						
						<tr id="" name="" bgcolor="<?php echo "$rowColor"; ?>">
							<td width="4%" align="center" style="border:1px" >&nbsp;
							</td>
							<td width="14%" style="border:1px" class="values">&nbsp;<b>TOTAL AMOUNT</b>
							</td>
							<td width="9%" style="border:1px" class="values">&nbsp;								
							</td>
							<td width="22%" style="border:1px" class="values">&nbsp;
							</td>
							<td width="19%" style="border:1px" class="values">&nbsp;
							</td>
							<td width="9%" style="border:1px" class="values" align="right"><b>$<?php echo "$dblTotalORAmount"; ?></b>&nbsp;&nbsp;					
							</td>
							<td width="7%" style="border:1px" class="values" align="left">&nbsp;								
							</td>
							<td width="8%" style="border:1px" class="values" align="left">&nbsp;								
							</td>
							<td width="8%" style="border:1px" class="values" align="left">&nbsp;								
							</td>
						</tr>
						<?php
							}
								if (($ctr + 1)%2==1) 
									$rowColor = "ffffe0";	
								else
									$rowColor = "98fb98";								
						?>						
					</table>
					<table>
						<tr>
							<td class="values">&nbsp;
								<input type="checkbox" name="chkSelectAll" id="chkSelectAll" onClick="javascript:chkSelectAll_OnClick();">SELECT ALL
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
	<input type="hidden" id="hidMenu" name="hidMenu" value="<?php echo $strMenu; ?>">
	<input type="hidden" id="hidChargeCode" name="hidChargeCode">
	<input type="hidden" id="hidRecID" name="hidRecID">
	<input type="hidden" id="hidChargeAmount" name="hidChargeAmount">
	<input type="hidden" id="hidMenuID" name="hidMenuID" value=<?php echo $menu_id;?>>
</form>
</body> 
</html>

<script type="text/javascript">

function enabledisablechkboxes(pVal) {
	var ctr
	ctr = frmTenantDepositDtl.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmTenantDepositDtl.chkDelete" + i);		
		obj.checked = false;
		if (pVal==0)
			obj.disabled = true;			
		else
			obj.disabled = false;		
	}
}

function hov(loc,cls) {   
	if(loc.className)   
	loc.className=cls;   
} 

function cmdSave_OnClick() {
	if (frmTenantDepositDtl.hidORNo.value == "") {
		alert("Select OR first!")
		return false
	}
	
	var j
	var dblTotal
	j=0
	dblTotal = 0
	totalctr = frmTenantDepositDtl.hidRowCtr.value;
	for (i=1;i<=totalctr;i++) {
		obj = eval("frmTenantDepositDtl.txtORAmount" + i);
		if (obj.value == "" || obj.value == 0) {
			alert("OR Amount is required")
			obj.focus()
			return false
		}
		if (isNaN(obj.value)) {
			alert("Invalid numeric value")
			obj.focus()
			return false;
		}
		obj1 = eval("frmTenantDepositDtl.hidBalanceAmount" + i);
		if (Number(obj.value)>Number(obj1.value)) {
			alert("OR Amount should not be greater than the Balance Amount")
			obj.focus()
			return false;
		}
		dblTotal = dblTotal + Number(obj.value);
	}
	
	frmTenantDepositDtl.hidMode.value = "SAVE";
	frmTenantDepositDtl.submit();
}

function cmdRetrieve_OnClick() {
	frmTenantDepositDtl.hidMode.value = "RETRIEVE";
	frmTenantDepositDtl.submit();
}

function cmdCancel_OnClick() {	
	parent.frames[2].location = "tenant_deposit_detail.php?menu_id=" + frmTenantDepositDtl.hidMenuID.value;
	return false;
}

function cmdDelete_OnClick() {
	var j
	j=0
	totalctr = frmTenantDepositDtl.hidRowCtr.value;
	for (i=1;i<=totalctr;i++) {
		obj = eval("frmTenantDepositDtl.chkDelete" + i);
		if (obj.checked == true) {
			j++;
		}
	}
	if (j > 0) {
		if (confirm("Are you sure you want to delete this record/s?")) {
			frmTenantDepositDtl.hidMode.value = "DELETE";
			frmTenantDepositDtl.submit();
		}
	}
	else {
		alert("Select record/s to delete");
	}
}

function chkSelectAll_OnClick() {
	var ctr
	ctr = frmTenantDepositDtl.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmTenantDepositDtl.chkDelete" + i);
		if (frmTenantDepositDtl.chkSelectAll.checked == true) {
			obj.checked = true;
		}
		else {
			obj.checked = false;
		}
	}
}

function change_loc(pFile) {
	parent.frames[2].location = pFile;
	return false;
}
</script>

