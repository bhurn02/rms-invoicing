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
if ($strMode == "")
	$strMode = "RETRIEVE";
//echo $strMode;
if ($strMode != "") {				
	switch ($strMode) {
		
		case "SAVE":						
			$i = 1;
			
			while ($i <= $_POST["hidRowCtr1"]) {							
				$intInvoiceDetailID = $_POST["hidInvoiceDetailID" . strval($i)];						
				$dblORAmount = $_POST["txtORAmount" . strval($i)];		
				$strORNo = $_POST["hidORNo" . strval($i)];						
				$intPaymentDetailID = $_POST["hidPaymentDetailID" . strval($i)];						
				$sqlquery="exec sp_t_Advance_Payment_OR_Breakdown 'SAVE','" . $strInvoiceNo . "'," . $intInvoiceDetailID . "," . $dblORAmount . ",'" . $strORNo . "'," . $intPaymentDetailID . ",'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
				$process=odbc_exec($sqlconnect, $sqlquery);				
				//echo $sqlquery;				
				$i++;
			}
			$strMsg = "Record saved!";
			$strMode = "RETRIEVE_WITH_OR";
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
<form name="frmORBreakDown" id="frmORBreakDown" method="post" action="advance_payment_or_processing_breakdown.php?menu_id=<?php echo $menu_id;?>&invoice_no=<?php echo $strInvoiceNo;?>&client_name=<?php echo $strClientName;?>">
	<div class="mainmenu">	
		<ul>
			<li class="li_nc"><a name="MODULE NAME">ADVANCE PAYMENT >> OR PROCESSING >> BREAKDOWN
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			 </a></li>			
			<li class="li_nc"><a href="#" onClick="javascript:cmdBack_OnClick()">|&nbsp;&nbsp;&nbsp;&nbsp;Back&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>			
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
										<td width="20">&nbsp;</td>		
										<td>
											<input type=text name="txtInvoiceNo" id="txtInvoiceNo" disabled class="values" size="20" value="<?php echo $strInvoiceNo;?>">									    
										</td>
									   <input type="hidden" id="hidInvoiceNo" name="hidInvoiceNo" value="<?php echo $strInvoiceNo;?>">
									</tr>	
									<tr>
										<td class="fieldname">CLIENT :<em class="requiredRed">*</em></td>
										<td width="20">&nbsp;</td>			
										<td><input type=text name="txtClientName" id="txtClientName" disabled class="values" size="60" value="<?php echo $strClientName;?>"></td>										
									</tr>																																													
								</table>
							</td>
						</tr>
						<tr>
							<td class="values">&nbsp;
							</td>
						</tr>							
					</table>										
					</table>
					<table width="300" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
						<tr height="30">							
							<td  width="54%" class="tablehdr" align="center">&nbsp;OR No&nbsp;
							</td>
							<td  width="46%" class="tablehdr" align="center">&nbsp;Balance Amount&nbsp;
							</td>
						</tr>
						<?php
						$ctr = 0;
						$dblTotalORBalanceAmount = 0;
						if ($strMode != "") {
							$sqlqueryOR="exec sp_t_Advance_Payment_OR_Breakdown 'OR','" . $strInvoiceNo . "',0,0,'',0,'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
							$processOR=odbc_exec($sqlconnect, $sqlqueryOR);							
							//echo $sqlqueryOR;
						}

						while(odbc_fetch_row($processOR)) {							
							$or_no = odbc_result($processOR,"or_no");
							$dblORBalanceAmount = odbc_result($processOR,"or_balance_amount");
							$dblTotalORBalanceAmount = $dblTotalORBalanceAmount + $dblORBalanceAmount;
							$ctr = $ctr+1;
							
							if ($ctr%2==1) 
								$rowColor = "98fb98";	
							else
								$rowColor = "ffffe0";			
						?>
						<tr id="editRow<?php echo "$ctr";?>" name="editRow<?php echo "$ctr";?>" style="cursor:hand" bgcolor="<?php echo "$rowColor" ?>" onClick="javascript:row_OnClick('<?php echo $or_no;?>',<?php echo $dblORBalanceAmount;?>)">
							<td width="54%" style="border:1px" class="values" align="left">
								&nbsp;<?php echo $or_no;?>
								<input type="hidden" name="hidORNo<?php echo $ctr;?>" id="hidORNo<?php echo $ctr;?>" value="<?php echo $or_no;?>">						
							</td>	
							<td width="46%" style="border:1px" class="values" align="right">								
								<?php echo $dblORBalanceAmount;?>&nbsp;
							</td>	
						</tr>
						<?php } 
							if ($ctr%2==1) 
								$rowColor = "ffffe0";	
							else
								$rowColor = "98fb98";	
							
							if ($ctr > 0) {
						?>
						<tr bgcolor="<?php echo "$rowColor" ?>">
							<td width="54%" style="border:1px" class="values" align="left">
								&nbsp;<b>TOTAL AMOUNT</b>
							</td>	
							<td width="46%" style="border:1px" class="values" align="right">								
								<b>$<?php echo number_format($dblTotalORBalanceAmount,2);?>&nbsp;</b>
							</td>	
						</tr>
						<?php } ?>
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
		alert("Total OR Amount should not be greater than the Advance Payment Total Balance Amount")
		return false;
	}
	
	frmORBreakDown.hidMode.value = "SAVE";
	frmORBreakDown.submit();
}

function cmdRetrieve_OnClick() {
	frmORBreakDown.hidMode.value = "RETRIEVE";
	frmORBreakDown.submit();
}

function row_OnClick(pOrNo,pORAmount) {	
	parent.frames['bottomFrame'].location = "advance_payment_or_processing_breakdown_invoice.php?menu_id=" + frmORBreakDown.hidMenuID.value + "&invoice_no=" + frmORBreakDown.hidInvoiceNo.value + "&or_no=" + pOrNo + "&or_amount=" + pORAmount;
	return false;
}


function cmdBack_OnClick() {	
	parent.frames.location = "advance_payment_or_posting.php?menu_id=" + frmORBreakDown.hidMenuID.value;
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

