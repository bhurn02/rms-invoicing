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

$strInvoiceNo = $_GET["invoice_no"];
$strClientName = $_GET["client_name"];
$strORNo = $_GET["or_no"];
$dblTotalORBalanceAmount = $_GET["or_amount"]; 
if ($strMode == "")
	$strMode = "RETRIEVE_WITH_OR";
//echo $strORNo;
if ($strMode != "") {				
	switch ($strMode) {
		
		case "SAVE":						
			$i = 1;
			
			while ($i <= $_POST["hidRowCtr1"]) {							
				$intInvoiceDetailID = $_POST["hidInvoiceDetailID" . strval($i)];						
				$dblORAmount = $_POST["txtORAmount" . strval($i)];		
				$strORNo = $_POST["hidORNo" . strval($i)];						
				$intPaymentDetailID = $_POST["hidPaymentDetailID" . strval($i)];						
				$sqlquery="exec sp_t_Advance_Payment_OR_Breakdown 'SAVE','" . $strInvoiceNo . "'," . $intInvoiceDetailID . "," . $dblORAmount . ",'" .  $_POST["hidORNo"] . "'," . $intPaymentDetailID . ",'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
				$process=odbc_exec($sqlconnect, $sqlquery);				
				//echo $sqlquery;				
				$i++;
			}
			$strMsg = "Record saved!";
			$strMode = "RETRIEVE_WITH_OR";
			$strORNo = $_POST["hidORNo"];
			$strInvoiceNo = $_POST["hidInvoiceNo"];
			$dblTotalORBalanceAmount = $_POST["hidTotalORBalanceAmount"]; 
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
					$sqlquery="exec sp_t_Payment_Detail 'DELETE'," . $intPaymentDetailID . ",'" . $strORNo . "','" . $strInvoiceNo . "'," . $intInvoiceDetailID . ",0," . $dblORAmount . ",'" . $uid . "'";	
					$process=odbc_exec($sqlconnect, $sqlquery);				
				}
				//echo $sqlquery;				
				$i++;
			}
			$strMsg = "Record/s deleted!";
			
			$sqlquery="exec sp_t_Payment_Detail 'RETRIEVE_HDR',0,'" . $strORNo . "','" . $strInvoiceNo . "',0,0,0,'" . $uid . "'";	
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
					
					$sqlqueryCharges="exec sp_t_Payment_Detail 'RETRIEVE_DTL',0,'" . $strORNo . "','',0,0,0,'" . $uid . "'";	
					$processCharges=odbc_exec($sqlconnect, $sqlqueryCharges);
					//echo $sqlqueryCharges;
			}
			break;
	}
}

if ($strMsg != ""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 

?>
<html> 
<head> 
<title>ADVANCE PAYMENT - OR PROCESSING BREAKDOWN</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
</head> 
<body style="margin:'0';background-color: #F3F5B4;">
<form name="frmORBreakDown" id="frmORBreakDown" method="post" action="advance_payment_or_processing_breakdown_invoice.php?menu_id=<?php echo $menu_id;?>&invoice_no=<?php echo $strInvoiceNo;?>&or_no=<?php echo $strORNo;?>&or_amount=<?php echo $dblTotalORBalanceAmount;?>">
	<div class="mainmenu">	  
		<ul>
			<li class="li_nc"><a name="MODULE NAME">OR No:&nbsp;<?php echo $strORNo; ?>
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;			  
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;			  
			 </a></li>
			<li class="li_nc"><a href="#" onClick="javascript:cmdSave_OnClick()">|&nbsp;&nbsp;&nbsp;&nbsp;Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
		</ul>
	</div>
	<table>
		<tr>
		<td width="10"></td>
		<td>
		<table>
			<tr>
				<td>					
					<table width="800" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
						<tr height="30">													
							<td  width="26%" class="tablehdr" align="center">&nbsp;Tenant&nbsp;
							</td>
							<td  width="16%" class="tablehdr" align="center">&nbsp;Charge&nbsp;
							</td>
							<td  width="13%" class="tablehdr" align="center">&nbsp;OR Amount&nbsp;
							</td>
							<td width="17%" class="tablehdr" align="center">Balance Amount
							</td>			
							<td width="16%" class="tablehdr" align="center">Paid Amount
							</td>	
							<td width="12%" class="tablehdr" align="center">Total Amount
							</td>	
						</tr>
						<?php
						$ctr1 = 0;
						odbc_close();
						$sqlconnect = connection();
						if ($strMode != "") {
							$sqlqueryInvoice="exec sp_t_Advance_Payment_OR_Breakdown '" . $strMode . "','" . $strInvoiceNo . "',0,0,'" . $strORNo . "',0,'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
							$processInvoice=odbc_exec($sqlconnect, $sqlqueryInvoice);
							//echo $sqlqueryInvoice;
						}
						$dblTotalORAmount = 0;
						while(odbc_fetch_row($processInvoice)) {							
							$intInvoiceDetailID = odbc_result($processInvoice,"invoice_detail_id");
							$intPaymentDetailID = odbc_result($processInvoice,"payment_detail_id");
							$strTenantCode = replacedoublequotes(odbc_result($processInvoice,"tenant_code"));
							$strTenantName = replacedoublequotes(odbc_result($processInvoice,"tenant_name"));
							$strChargeCode = replacedoublequotes(odbc_result($processInvoice,"charge_code"));
							$strChargeDesc = replacedoublequotes(odbc_result($processInvoice,"charge_desc"));
							$dblORAmount = odbc_result($processInvoice,"or_amount");;
							$dblTotalChargeAmount = odbc_result($processInvoice,"total_charge_amount");
							$dblPaidAmount = odbc_result($processInvoice,"paid_amount");
							$dblBalanceAmount = odbc_result($processInvoice,"balance_amount");
							if ($dblBalanceAmount == 0) 
								$dblBalanceAmount = ".00";
								
							$dblTotalORAmount = $dblTotalORAmount + $dblORAmount;
							
							$ctr1 = $ctr1+1;
							
							if ($ctr1%2==1) 
								$rowColor = "98fb98";	
							else
								$rowColor = "ffffe0";			
						?>
						<tr id="editRow<?php echo $ctr1;?>" name="editRow<?php echo $ctr1;?>" style="cursor:hand" bgcolor="<?php echo "$rowColor" ?>">
							<td width="26%" style="border:1px" class="values">&nbsp;<?php echo $strTenantName;?>
								<input type="hidden" name="hidInvoiceDetailID<?php echo $ctr1;?>" id="hidInvoiceDetailID<?php echo $ctr1;?>" value="<?php echo "$intInvoiceDetailID";?>">						
								<input type="hidden" name="hidPaymentDetailID<?php echo $ctr1;?>" id="hidPaymentDetailID<?php echo $ctr1;?>" value="<?php echo "$intPaymentDetailID";?>">						
							</td>
							<td width="16%" style="border:1px" class="values" align="left">
								&nbsp;<?php echo $strChargeDesc;?>
							</td>	
							<td width="13%" style="border:1px" class="values" align="right">								
								<input type="text" name="txtORAmount<?php echo $ctr1;?>" id="txtORAmount<?php echo $ctr1;?>" class="values" style="text-align:right" size="10" maxlength="11" value="<?php echo $dblORAmount;?>">&nbsp;
							</td>								
							<td width="17%" style="border:1px" class="values" align="right">
								<?php echo $dblBalanceAmount;?>&nbsp;
								<input type="hidden" name="hidBalanceAmount<?php echo $ctr1;?>" id="hidBalanceAmount<?php echo $ctr1;?>" value=<?php echo $dblBalanceAmount;?>>
							</td>		
							<td width="16%" style="border:1px" class="values" align="right">
								<?php echo $dblPaidAmount;?>&nbsp;								
							</td>	
							<td width="12%" style="border:1px" class="values" align="right">
								<?php echo $dblTotalChargeAmount;?>&nbsp;
							</td>					
						</tr>
						<?php } 
							if ($ctr1%2==1) 
								$rowColor = "ffffe0";	
							else
								$rowColor = "98fb98";								
									
							if ($ctr1 > 0) {
						?>
						
						<tr bgcolor="<?php echo "$rowColor"; ?>">
							<td width="26%" align="left" style="border:1px" class="values">&nbsp;<b>TOTAL AMOUNT</b>
							</td>
							<td width="16%" style="border:1px" class="values">&nbsp;
							</td>
							<td width="13%" style="border:1px" class="values" align="right"><b>$<?php echo number_format($dblTotalORAmount,2); ?></b>&nbsp;					
							</td>
							<td width="17%" style="border:1px" class="values">&nbsp;
							</td>
							<td width="16%" style="border:1px" class="values">&nbsp;
							</td>
							<td width="12%" style="border:1px" class="values" align="right">&nbsp;					
							</td>							
						</tr>
						<?php }	?>						
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
	<input type="hidden" id="hidRowCtr1" name="hidRowCtr1" value=<?php echo $ctr1;?>>
	<input type="hidden" id="hidCurRow" name="hidCurRow">
	<input type="hidden" id="hidMenu" name="hidMenu" value="<?php echo $strMenu; ?>">
	<input type="hidden" id="hidChargeCode" name="hidChargeCode">
	<input type="hidden" id="hidRecID" name="hidRecID">
	<input type="hidden" id="hidORNo" name="hidORNo" value="<?php echo $strORNo; ?>">
	<input type="hidden" id="hidInvoiceNo" name="hidInvoiceNo" value="<?php echo $strInvoiceNo; ?>">
	<input type="hidden" id="hidTotalORBalanceAmount" name="hidTotalORBalanceAmount" value=<?php echo number_format($dblTotalORBalanceAmount,2);?>>
	<input type="hidden" id="hidMenuID" name="hidMenuID" value=<?php echo $menu_id;?>>
</form>
</body> 
</html>

<script type="text/javascript">

function enabledisablechkboxes(pVal) {
	var ctr
	ctr = frmORBreakDown.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmORBreakDown.chkDelete" + i);		
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
	var j
	var dblTotal
	j=0
	dblTotal = 0
	totalctr = frmORBreakDown.hidRowCtr1.value;
	for (i=1;i<=totalctr;i++) {
		obj = eval("frmORBreakDown.txtORAmount" + i);		
		if (isNaN(obj.value)) {
			alert("Invalid numeric value")
			obj.focus()
			return false;
		}		
		obj1 = eval("frmORBreakDown.hidBalanceAmount" + i);		
		if (Number(obj.value)>Number(obj1.value)) {
			alert("OR Amount should not be greater than the Invoice Balance Amount")
			return false;
		}
		dblTotal = dblTotal + Number(obj.value);
	}
	if (Number(dblTotal)==0) {
		alert("No OR Amount inputted")
		return false;
	}

	if (Number(dblTotal)>Number(frmORBreakDown.hidTotalORBalanceAmount.value)) {
		alert("Total OR Amount should not be greater than the Advance Payment OR Balance Amount")
		return false;
	}
	
	frmORBreakDown.hidMode.value = "SAVE";
	frmORBreakDown.submit();
}

function cmdRetrieve_OnClick() {
	frmORBreakDown.hidMode.value = "RETRIEVE";
	frmORBreakDown.submit();
}

function cmdBack_OnClick() {	
	parent.frames[2].location = "advance_payment_or_posting.php?menu_id=" + frmORBreakDown.hidMenuID.value;
	return false;
}

function cmdDelete_OnClick() {
	var j
	j=0
	totalctr = frmORBreakDown.hidRowCtr.value;
	for (i=1;i<=totalctr;i++) {
		obj = eval("frmORBreakDown.chkDelete" + i);
		if (obj.checked == true) {
			j++;
		}
	}
	if (j > 0) {
		if (confirm("Are you sure you want to delete this record/s?")) {
			frmORBreakDown.hidMode.value = "DELETE";
			frmORBreakDown.submit();
		}
	}
	else {
		alert("Select record/s to delete");
	}
}

function chkSelectAll_OnClick() {
	var ctr
	ctr = frmORBreakDown.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmORBreakDown.chkDelete" + i);
		if (frmORBreakDown.chkSelectAll.checked == true) {
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

