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
$strInvoiceNo = "";
$dblInvoiceAmount = 0;
$intRecID = 0;
$strTenantCode = "";
$strTenantName = "";
$strChargeCode = "";		
$dblChargeAmount = 0;	
$dblTotalChargeAmount = 0;	
$strRemarks = "";
$strMsg = "";
$strMenu = "";

if ($_GET["mode"] == "FIND") {
	$strMenu = $_GET["menu"];
	$strORNo = $_GET["or_no"];
	$strClientCode = $_GET["client_code"];
	$strClientName = $_GET["client_name"];
	$strMode = "FIND";
}
//echo $strMode;
if ($strMode != "") {	
	//echo $strSaveMode;
	switch ($strMode) {
	
		case "APPLY":
			$i = 1;
			$j = 0;
			$k = 0;
			$strORNo = $_POST["hidORNo"];
			while ($i <= $_POST["hidRowCtr"]) {
				if (isset($_POST["chkSelect" . strval($i)])) {
					$strInvoiceNo = replacesinglequote($_POST["hidInvoiceNo" . strval($i)]);							
					$intInvoiceDetailID = $_POST["hidInvoiceDetailID" . strval($i)];		
					$intInvoiceDetailChargesID = $_POST["hidInvoiceDetailChargesID" . strval($i)];		
					$sqlquery="exec sp_t_Payment_Detail 'ADD_ENTRY',0,'" . $strORNo . "','" . $strInvoiceNo . "'," . $intInvoiceDetailID . ",0,0,'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					//echo $sqlquery;
					$process=odbc_exec($sqlconnect, $sqlquery);
					if (odbc_fetch_row($process)) {
						if (odbc_result($process,"x") == 1) 
							$j++;
						else
							$k++;
					}
				}
				$i++;
			}
			if ($j > 0 && $k > 0) {
				$strMsg = "Some records were already added in this OR.";
			}
			elseif ($j == 0 && $k > 0) {
				$strMsg = "Record/s added!";
			}
			elseif ($j > 0 && $k == 0) {
				$strMsg = "All selected records were added already in this OR.";
			}
			else
				$strMsg = "No record added!";
				
			$strORNo = $_POST["hidORNo"];
			$strClientCode = $_POST["hidClientCode"];
			$strClientName = $_POST["hidClientName"];
			$strInvoiceNoFrom = $_POST["hidInvoiceNoFrom"];
			$strInvoiceNoTo = $_POST["hidInvoiceNoTo"];
			$dtInvoiceFrom = $_POST["hidInvoiceDateFrom"];
			$dtInvoiceTo = $_POST["hidInvoiceDateTo"];
			//$sqlquery="exec sp_t_Payment_Detail 'VIEW_ADD_ENTRIES','0','" . $strORNo . "','" . $strKeyword . "',0,0,0,0,'" . $uid . "'";	
			$sqlquery="exec sp_t_Payment_Detail_AddEntries 'VIEW_ADD_ENTRIES','" . $strORNo . "','" . $strInvoiceNoFrom . "','" . $strInvoiceNoTo . "','" . $dtInvoiceFrom . "','" . $dtInvoiceTo . "'";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			
			break;
			
		case "RETRIEVE":
			$sqlquery="exec sp_t_Payment_Detail 'VIEW_ADD_ENTRIES','0','" . strORNo . "','',0,0,0,'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			//echo $sqlquery;
			$process=odbc_exec($sqlconnect, $sqlquery);
			if (odbc_fetch_row($process)) {
					$strInvoiceNo = replacedoublequotes(odbc_result($process,"invoice_no"));
					$dtInvoice = odbc_result($process,"invoice_date");
					$strTenantCode = replacedoublequotes(odbc_result($process,"tenant_code"));
					$strTenantName = replacedoublequotes(odbc_result($process,"tenant_name"));
					$strChargeCode = replacedoublequotes(odbc_result($process,"charge_code"));
					$strChargeDesc = replacedoublequotes(odbc_result($process,"charge_desc"));
					$dblTotalChargeAmount = odbc_result($process,"total_charge_amount");
					$dblPaidAmount = odbc_result($process,"paid_amount");
					$dblBalanceAmount = odbc_result($process,"balance_amount");
					$strMode = "RETRIEVE";
					$strSaveMode = "EDIT";
					
					$sqlqueryCharges="exec sp_t_InvoiceDetail 'RETRIEVE','" . $strInvoiceNo . "',0,'" . $strTenantCode . "','" . $strChargeCode . "',''," . $dblChargeAmount . ",'" . $strRemarks . "','" . $uid . "'";	
					$processCharges=odbc_exec($sqlconnect, $sqlqueryCharges);
					//echo $sqlqueryCharges;
			}
			else {
				$strMode = "";
			}
			break;
			
		case "FIND":			
			//$sqlquery="exec sp_t_Payment_Detail 'VIEW_ADD_ENTRIES','0','" . $strORNo . "','" . $strKeyword . "',0,0,0,0,'" . $uid . "'";	
			$sqlquery="exec sp_t_Payment_Detail_AddEntries 'VIEW_ADD_ENTRIES','" . $strORNo . "','','','','','',''";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			//echo $sqlquery;
			break;
			
		case "SEARCH":
			$strORNo = $_POST["hidORNo"];
			$strClientCode = $_POST["hidClientCode"];
			$strClientName = $_POST["hidClientName"];
			$strInvoiceNoFrom = $_POST["hidInvoiceNoFrom"];
			$strInvoiceNoTo = $_POST["hidInvoiceNoTo"];
			$dtInvoiceFrom = $_POST["hidInvoiceDateFrom"];
			$dtInvoiceTo = $_POST["hidInvoiceDateTo"];
			//$sqlquery="exec sp_t_Payment_Detail 'VIEW_ADD_ENTRIES','0','" . $strORNo . "','" . $strKeyword . "',0,0,0,0,'" . $uid . "'";	
			$sqlquery="exec sp_t_Payment_Detail_AddEntries 'VIEW_ADD_ENTRIES','" . $strORNo . "','" . $strInvoiceNoFrom . "','" . $strInvoiceNoTo . "','" . $dtInvoiceFrom . "','" . $dtInvoiceTo . "'";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			//echo $sqlquery;
			break;
	}
}
else {
	$strCode = "";
	$strName = "";
}

if ($strMsg != ""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 

?>
<html> 
<head> 
<title>INVOICE DETAIL</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
</head> 
<body style="margin:'0';background-color: #F3F5B4;">
<form name="frmPaymentDtlAdd" id="frmPaymentDtlAdd" method="post" action="payment_detail_add_entries.php?menu_id=<?php echo $menu_id;?>">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME"><?php echo strtoupper($strClientName); ?> >> PAYMENT DETAIL >> ADD ENTRIES
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			 </a></li>	
			 <li class="li_nc"><a href="#" onClick="javascript:cmdSearch_OnClick()">|&nbsp;&nbsp;&nbsp;&nbsp;Search&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>			  			  
  			 <li class="li_nc"><a href="#" onClick="javascript:cmdApply_OnClick()">Apply&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>			  			  
			 <li class="li_nc"><a href="#" onClick="javascript:change_loc('payment_detail.php?menu_id=<?php echo $menu_id;?>&mode=FIND&menu=DETAIL&or_no=<?php echo $strORNo;?>&client_code=<?php echo $strClientCode;?>&client_name=<?php echo $strClientName;?>')">Back&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
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
							<td class="fieldname" align="right">Invoice No. From :</td>
							<td width="5"></td>
							<td><input type=text name="txtInvoiceNoFrom" id="txtInvoiceNoFrom" class="values" size="20" maxlength="20" value="<?php echo $strInvoiceNoFrom; ?>"></td>
							<td width="10"></td>
							<td class="fieldname" align="right">Billing From (mm/dd/yyyy) :</td>
							<td width="5"></td>
							<td><input type=text name="txtInvoiceDateFrom" id="txtInvoiceDateFrom" class="values" size="20" maxlength="10" value="<?php echo $dtInvoiceFrom; ?>" ></td>
						</tr>
						<tr>
							<td class="fieldname" align="right">To :</td>
							<td width="5"></td>
							<td><input type=text name="txtInvoiceNoTo" id="txtInvoiceNoTo" class="values" size="20" maxlength="20" value="<?php echo $strInvoiceNoTo; ?>"></td>
							<td width="10"></td>
							<td class="fieldname" align="right">To (mm/dd/yyyy) :</td>
							<td width="5"></td>
							<td><input type=text name="txtInvoiceDateTo" id="txtInvoiceDateTo" class="values" size="20" maxlength="10" value="<?php echo $dtInvoiceTo; ?>"></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table width="900" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
						<tr height="30">				
							<td width="4%" class="tablehdr" align="center">Sel
							</td>			
							<td  width="11%" class="tablehdr" align="center">Invoice No.
							</td>
							<td  width="7%" class="tablehdr" align="center">Date
							</td>
							<td width="21%" class="tablehdr" align="center">Tenant
							</td>							
							<td width="15%" class="tablehdr" align="center">Charge
							</td>					
							<td width="7%" class="tablehdr" align="center">Balance Amount
							</td>			
							<td width="7%" class="tablehdr" align="center">Paid Amount
							</td>	
							<td width="8%" class="tablehdr" align="center">Total Amount
							</td>	
						</tr>
						<?php
						while(odbc_fetch_row($process)){
							$strInvoiceNo = odbc_result($process,"invoice_no");
							$dtInvoice = odbc_result($process,"invoice_date");
							$intInvoiceDetailID = odbc_result($process,"invoice_detail_id");
							//$intInvoiceDetailChargesID = odbc_result($process,"invoice_detail_charges_id");
							$strTenantCode = replacedoublequotes(odbc_result($process,"tenant_code"));
							$strTenantName = replacedoublequotes(odbc_result($process,"tenant_name"));
							$strChargeCode = replacedoublequotes(odbc_result($process,"charge_code"));
							$strChargeDesc = replacedoublequotes(odbc_result($process,"charge_desc"));
							$dblTotalChargeAmount = odbc_result($process,"total_charge_amount");
							$dblPaidAmount = odbc_result($process,"paid_amount");
							$dblBalanceAmount = $dblTotalChargeAmount - $dblPaidAmount;
							$status = odbc_result($process,"status");
							
							if ($dblBalanceAmount > 0) {
								$ctr = $ctr+1;
								
								if ($ctr%2==1) 
									$rowColor = "98fb98";	
								else
									$rowColor = "ffffe0";	
						?>
						<tr bgcolor="<?php echo "$rowColor"; ?>">							
							<td width="4%" class="values" align="center">
								<?php if ($status=="P") { ?>
									<input type="checkbox" name="chkSelect<?php echo "$ctr";?>" id="chkSelect<?php echo "$ctr";?>" value="<?php echo "$ctr";?>">								
								<?php } else { ?>
									<input type="checkbox" name="chkSelect<?php echo "$ctr";?>" disabled id="chkSelect<?php echo "$ctr";?>" value="<?php echo "$ctr";?>">								
								<?php } ?>
								<input type="hidden" name="hidStatus<?php echo "$ctr";?>" id="hidStatus<?php echo "$ctr";?>" value="<?php echo "$status";?>">						
							</td>
							<td width="11%" class="values">&nbsp;<?php echo "$strInvoiceNo";?>
								<input type="hidden" name="hidInvoiceNo<?php echo "$ctr";?>" id="hidInvoiceNo<?php echo "$ctr";?>" value="<?php echo "$strInvoiceNo";?>">						
								<input type="hidden" name="hidInvoiceDetailID<?php echo "$ctr";?>" id="hidInvoiceDetailID<?php echo "$ctr";?>" value="<?php echo "$intInvoiceDetailID";?>">						
								<input type="hidden" name="hidInvoiceDetailChargesID<?php echo "$ctr";?>" id="hidInvoiceDetailChargesID<?php echo "$ctr";?>" value="<?php echo "$intInvoiceDetailChargesID";?>">						
							</td>
							<td width="7%" class="values" align="center">&nbsp;<?php echo "$dtInvoice";?>&nbsp;
							</td>
							<td width="21%" class="values">&nbsp;<?php echo "$strTenantName";?>&nbsp;
							</td>							
							<td width="15%" class="values">&nbsp;<?php echo "$strChargeDesc";?>&nbsp;
							</td>		
							<td width="7%" class="values" align="right">&nbsp;<?php echo "$dblBalanceAmount";?>&nbsp;
							</td>					
							<td width="7%" class="values" align="right">&nbsp;<?php echo "$dblPaidAmount";?>&nbsp;
							</td>					
							<td width="8%" class="values" align="right">&nbsp;<?php echo "$dblTotalChargeAmount";?>&nbsp;
							</td>					
						</tr>						
						<?php } } ?>
					</table>	
					<table>
						<tr>
							<td class="values">&nbsp;&nbsp;
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
	<input type="hidden" id="hidORNo" name="hidORNo" value="<?php echo $strORNo;?>">
	<input type="hidden" id="hidClientCode" name="hidClientCode" value="<?php echo $strClientCode;?>">
	<input type="hidden" id="hidClientName" name="hidClientName" value="<?php echo $strClientName;?>">
	<input type="hidden" id="hidInvoiceNoFrom" name="hidInvoiceNoFrom" value="<?php echo $dtInvoiceNoFrom;?>">
	<input type="hidden" id="hidInvoiceNoTo" name="hidInvoiceNoTo" value="<?php echo $dtInvoiceNoTo;?>">
	<input type="hidden" id="hidInvoiceDateFrom" name="hidInvoiceDateFrom" value="<?php echo $dtInvoiceFrom;?>">
	<input type="hidden" id="hidInvoiceDateTo" name="hidInvoiceDateTo" value="<?php echo $dtInvoiceTo;?>">
</form>
</body> 
</html>

<script type="text/javascript">

function cmdSearch_OnClick() {
	if (frmPaymentDtlAdd.txtInvoiceNoFrom.value > frmPaymentDtlAdd.txtInvoiceNoTo.value) {
		alert("Invoice No. From should be greater than Invoice No. To")
		frmPaymentDtlAdd.txtInvoiceNoFrom.focus()
		return false
	}
	if (frmPaymentDtlAdd.txtInvoiceDateFrom.value != "") {
		if (isDate(frmPaymentDtlAdd.txtInvoiceDateFrom.value)==false){
			frmPaymentDtlAdd.txtInvoiceDateFrom.focus()
			return false
		}
	}
	if (frmPaymentDtlAdd.txtInvoiceDateTo.value != "") {
		if (isDate(frmPaymentDtlAdd.txtInvoiceDateTo.value)==false){
			frmPaymentDtlAdd.txtInvoiceDateTo.focus()
			return false
		}
	}
	if (frmPaymentDtlAdd.txtInvoiceDateFrom.value > frmPaymentDtlAdd.txtInvoiceDateTo.value) {
		alert("Date From should be earlier than Date To")
		frmPaymentDtlAdd.txtInvoiceDateFrom.focus()
		return false
	}
	frmPaymentDtlAdd.hidInvoiceNoFrom.value = frmPaymentDtlAdd.txtInvoiceNoFrom.value
	frmPaymentDtlAdd.hidInvoiceNoTo.value = frmPaymentDtlAdd.txtInvoiceNoTo.value
	frmPaymentDtlAdd.hidInvoiceDateFrom.value = frmPaymentDtlAdd.txtInvoiceDateFrom.value
	frmPaymentDtlAdd.hidInvoiceDateTo.value = frmPaymentDtlAdd.txtInvoiceDateTo.value
	frmPaymentDtlAdd.hidMode.value = "SEARCH";
	frmPaymentDtlAdd.submit();
}

function cmdApply_OnClick() {
	frmPaymentDtlAdd.hidInvoiceNoFrom.value = frmPaymentDtlAdd.txtInvoiceNoFrom.value
	frmPaymentDtlAdd.hidInvoiceNoTo.value = frmPaymentDtlAdd.txtInvoiceNoTo.value
	frmPaymentDtlAdd.hidInvoiceDateFrom.value = frmPaymentDtlAdd.txtInvoiceDateFrom.value
	frmPaymentDtlAdd.hidInvoiceDateTo.value = frmPaymentDtlAdd.txtInvoiceDateTo.value
	frmPaymentDtlAdd.hidMode.value = "APPLY";
	frmPaymentDtlAdd.submit();
}

function chkSelectAll_OnClick() {
	var ctr
	ctr = frmPaymentDtlAdd.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmPaymentDtlAdd.chkSelect" + i);
		obj1 = eval("frmPaymentDtlAdd.hidStatus" + i);
		if (frmPaymentDtlAdd.chkSelectAll.checked == true) {
			if (obj1.value == "P")
				obj.checked = true;
		}
		else {
			obj.checked = false;
		}
	}
}

function editMode(ctr) {
	if (frmPaymentDtlAdd.hidSaveMode.value != "EDIT_CHARGE") {
		frmPaymentDtlAdd.hidCurRow.value = ctr;
		obj = eval("spCharge" + ctr);
		obj.style.visibility = "hidden"
		obj.style.display = "none"
		
		obj = eval("spEditCharge" + ctr);
		obj.style.visibility = "visible"
		obj.style.display = ""
		
		obj = eval("spChargeAmount" + ctr);
		obj.style.visibility = "hidden"
		obj.style.display = "none"
		
		obj = eval("spEditChargeAmount" + ctr);
		obj.style.visibility = "visible"
		obj.style.display = ""
		
		obj = eval("spRemarks" + ctr);
		obj.style.visibility = "hidden"
		obj.style.display = "none"
		
		obj = eval("spEditRemarks" + ctr);
		obj.style.visibility = "visible"
		obj.style.display = ""
		
		enabledisablechkboxes(0);
		frmPaymentDtlAdd.chkSelectAll.disabled = true;
		frmPaymentDtlAdd.chkSelectAll.checked = false;
		frmPaymentDtlAdd.cboAddCharge.disabled = true;
		frmPaymentDtlAdd.txtAddChargeAmount.disabled = true;
		frmPaymentDtlAdd.hidSaveMode.value = "EDIT_CHARGE";
	}
}

function enabledisablechkboxes(pVal) {
	var ctr
	ctr = frmPaymentDtlAdd.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmPaymentDtlAdd.chkDelete" + i);		
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
	if (frmPaymentDtlAdd.hidInvoiceNo.value == "") {
		alert("Select Invoice first!")
		frmPaymentDtlAdd.txtInvoiceNo.focus()
		return false
	}
	
	if (frmPaymentDtlAdd.hidSaveMode.value == "EDIT_CHARGE") {
		ctr = frmPaymentDtlAdd.hidCurRow.value;
		
		obj = eval("frmPaymentDtlAdd.cboEditCharge" + ctr);
		if (obj.value == "") {
			alert("Charge is required")
			obj.focus()
			return false
		}
		frmPaymentDtlAdd.hidChargeCode.value = obj.value;
		
		obj = eval("frmPaymentDtlAdd.hidEditRecID" + ctr);
		frmPaymentDtlAdd.hidRecID.value = obj.value;
		
		obj = eval("frmPaymentDtlAdd.txtEditChargeAmount" + ctr);
		if (isNaN(obj.value) && obj.value != "") {
			alert("Invalid numeric value");
			obj.focus();
			return false;
		}
		obj = eval("frmPaymentDtlAdd.txtEditChargeAmount" + ctr);
		if (obj.value == "" || obj.value == 0) {
			alert("Charge Amount is required")
			obj.focus()
			return false
		}
		frmPaymentDtlAdd.hidChargeAmount.value = obj.value;
		frmPaymentDtlAdd.hidSaveMode.value = "EDIT_CHARGE";
	}
	else {
		if (frmPaymentDtlAdd.cboAddCharge.value == "") {
			alert("Charge is required")
			frmPaymentDtlAdd.cboAddCharge.focus()
			return false
		}
		if (isNaN(frmPaymentDtlAdd.txtAddChargeAmount.value) && frmPaymentDtlAdd.txtAddChargeAmount.value != "") {
			alert("Invalid numeric value")
			frmPaymentDtlAdd.txtAddChargeAmount.focus()
			return false
		}
		if (frmPaymentDtlAdd.txtAddChargeAmount.value=="" || frmPaymentDtlAdd.txtAddChargeAmount.value == 0) {
			alert("Charge Amount is required")
			frmPaymentDtlAdd.txtAddChargeAmount.focus()
			return false
		}
		frmPaymentDtlAdd.hidChargeCode.value = frmPaymentDtlAdd.cboAddCharge.value;
		frmPaymentDtlAdd.hidChargeAmount.value = frmPaymentDtlAdd.txtAddChargeAmount.value;
		frmPaymentDtlAdd.hidRecID.value = 0;
		frmPaymentDtlAdd.hidSaveMode.value = "EDIT";
	}
	frmPaymentDtlAdd.hidMode.value = "SAVE";
	frmPaymentDtlAdd.submit();
}

function cmdRetrieve_OnClick() {
	frmPaymentDtlAdd.hidMode.value = "RETRIEVE";
	frmPaymentDtlAdd.submit();
}

function cmdCancel_OnClick() {
	ctr = frmPaymentDtlAdd.hidCurRow.value;
	//alert(frmPaymentDtlAdd.hidSaveMode.value)
	if (frmPaymentDtlAdd.hidSaveMode.value == "EDIT_CHARGE") {
		obj = eval("spEditCharge" + ctr);
		obj.style.visibility = "hidden"
		obj.style.display = "none"
		
		obj = eval("spCharge" + ctr);
		obj.style.visibility = "visible"
		obj.style.display = ""
		
		obj = eval("spEditChargeAmount" + ctr);
		obj.style.visibility = "hidden"
		obj.style.display = "none"
		
		obj = eval("spChargeAmount" + ctr);
		obj.style.visibility = "visible"
		obj.style.display = ""
		
		obj = eval("spEditRemarks" + ctr);
		obj.style.visibility = "hidden"
		obj.style.display = "none"
		
		obj = eval("spRemarks" + ctr);
		obj.style.visibility = "visible"
		obj.style.display = ""
		
		frmPaymentDtlAdd.cboAddCharge.disabled = false;
		frmPaymentDtlAdd.txtAddChargeAmount.disabled = false;
		frmPaymentDtlAdd.chkSelectAll.disabled = false;
		frmPaymentDtlAdd.chkSelectAll.checked = false;
		frmPaymentDtlAdd.hidSaveMode.value = "";
		enabledisablechkboxes(1);
		return false;
	}
	else {
		parent.frames[2].location = "invoice_detail.php";
		return false;
	}
}

function cmdDelete_OnClick() {
	var j
	j=0
	totalctr = frmPaymentDtlAdd.hidRowCtr.value;
	for (i=1;i<=totalctr;i++) {
		obj = eval("frmPaymentDtlAdd.chkDelete" + i);
		if (obj.checked == true) {
			j++;
		}
	}
	if (j > 0) {
		if (confirm("Are you sure you want to delete this record/s?")) {
			frmPaymentDtlAdd.hidMode.value = "DELETE";
			frmPaymentDtlAdd.submit();
		}
	}
	else {
		alert("Deleting is not allowed this time");
	}
}

function change_loc(pFile) {
	parent.frames[2].location = pFile;
	return false;
}

/**
 * DHTML date validation script. Courtesy of SmartWebby.com (http://www.smartwebby.com/dhtml/)
 */
// Declaring valid date character, minimum year and maximum year
var dtCh= "/";
var minYear=1900;
var maxYear=2100;

function isInteger(s){
	var i;
    for (i = 0; i < s.length; i++){   
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}

function stripCharsInBag(s, bag){
	var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++){   
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

function daysInFebruary (year){
	// February has 29 days in any year evenly divisible by four,
    // EXCEPT for centurial years which are not also divisible by 400.
    return (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
}
function DaysArray(n) {
	for (var i = 1; i <= n; i++) {
		this[i] = 31
		if (i==4 || i==6 || i==9 || i==11) {this[i] = 30}
		if (i==2) {this[i] = 29}
   } 
   return this
}

function isDate(dtStr){
	var daysInMonth = DaysArray(12)
	var pos1=dtStr.indexOf(dtCh)
	var pos2=dtStr.indexOf(dtCh,pos1+1)
	var strMonth=dtStr.substring(0,pos1)
	var strDay=dtStr.substring(pos1+1,pos2)
	var strYear=dtStr.substring(pos2+1)
	strYr=strYear
	if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1)
	if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1)
	for (var i = 1; i <= 3; i++) {
		if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1)
	}
	month=parseInt(strMonth)
	day=parseInt(strDay)
	year=parseInt(strYr)
	if (pos1==-1 || pos2==-1){
		alert("The date format should be : mm/dd/yyyy")
		return false
	}
	if (strMonth.length<1 || month<1 || month>12){
		alert("Please enter a valid month")
		return false
	}
	if (strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){
		alert("Please enter a valid day")
		return false
	}
	if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){
		alert("Please enter a valid 4 digit year between "+minYear+" and "+maxYear)
		return false
	}
	if (dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){
		alert("Please enter a valid date")
		return false
	}
return true
}

function ValidateForm(){
	var dt=document.frmSample.txtDate
	if (isDate(dt.value)==false){
		dt.focus()
		return false
	}
    return true
 }
</script>

