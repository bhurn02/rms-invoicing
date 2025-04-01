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
//$dtBillingFrom = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
//$dtBillingTo = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
$dtBillingFrom = "";	
$dtBillingTo = "";	
$strSortBy = "";
$strMsg = "";


if ($strMode != "") {	
	$dtBillingFrom = $_POST["DPC_txtDateFrom"];
	$dtBillingTo = $_POST["DPC_txtDateTo"];
	$strSortBy = $_POST["cboSortBy"];

}

$sqlquery="exec sp_t_ORProcessing_AdvancePayment_List '','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $strSortBy . "'";	
$process=odbc_exec($sqlconnect, $sqlquery);

//echo $sqlquery;
if ($strMsg != "" && $strMode==""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 


?>
<html> 
<head> 
<title>OR PROCESSING - ADVANCE PAYMENT</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
<script type="text/javascript" src="library/datepickercontrol/datepickercontrol.js"></script>
<link type="text/css" rel="stylesheet" href="library/datepickercontrol/datepickercontrol_green.css">
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color: #F3F5B4;">
<form name="frmORProcessing" id="frmORProcessing" method="post">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a href="#" onClick="javascript:change_loc('payment_main.php')"><u>OR PROCESSING</u></a></li>	
			  <li class="li_nc"><a name="MODULE NAME">>>&nbsp;&nbsp;&nbsp;&nbsp;ADVANCE PAYMENT
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			 </a></li>	
			  <li class="li_nc"><a href="#" onClick="javascript:cmdSearch_OnClick()">|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Build List&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>			  			  
			  <li class="li_nc"><a href="#" onClick="javascript:cmdApply_OnClick(0)">Apply Invoice&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>				  
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
										<td class="fieldname" align="right">OR DATE FROM (mm/dd/yyyy) :</td>
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
										<td width="10">&nbsp;</td>
										<td>
											<select name="cboSortBy" id="cboSortBy" class="values" onChange="javascript:cmdSearch_OnClick()">												
												<?php if ($strSortBy == "" || $strSortBy == "OR NO.") {?>
													<option selected value="OR NO.">OR No.</option>
													<option value="OR DATE">OR Date</option>
													<option value="CLIENT">Client</option>
													<option value="UNIT NO.">Unit No.</option>
												<?php } else if ($strSortBy == "OR DATE") { ?>
													<option value="OR NO.">OR No.</option>
													<option selected value="OR DATE">OR Date</option>
													<option value="CLIENT">Client</option>
													<option value="UNIT NO.">Unit No.</option>
												<?php } else if ($strSortBy == "CLIENT") { ?>
													<option value="OR NO.">OR No.</option>
													<option value="OR DATE">OR Date</option>
													<option selected value="CLIENT">Client</option>
													<option value="UNIT NO.">Unit No.</option>
												<?php } else { ?>
													<option value="OR NO.">OR No.</option>
													<option value="OR DATE">OR Date</option>
													<option value="CLIENT">Client</option>
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
					<table width="900" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
						<tr height="30">				
							<td width="4%" class="tablehdr" align="center">Sel
							</td>			
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
						while(odbc_fetch_row($process)){
							$strORNo = odbc_result($process,"or_no");
							$dtOR = odbc_result($process,"or_date");
							$strClientName = replacedoublequotes(odbc_result($process,"client_name"));							
							$unit_no = replacedoublequotes(odbc_result($process,"unit_no"));							
							$dblTotalAmount = odbc_result($process,"total_amount");
							$dblDeductedAmount = odbc_result($process,"deducted_amount");
							$dblBalanceAmount =  odbc_result($process,"balance_amount");
							$status = odbc_result($process,"status");
							
							if ($dblBalanceAmount > 0) {
								$ctr = $ctr+1;
								
								if ($ctr%2==1) 
									$rowColor = "98fb98";	
								else
									$rowColor = "ffffe0";	
						?>
						<tr bgcolor="<?php echo "$rowColor"; ?>" style="cursor:hand;" title="Click to Apply Invoice on this OR" onDblClick="javascript:cmdApply_OnClick(<?php echo $ctr;?>)" onClick="javascript:chkSel(<?php echo $ctr;?>)">							
							<td width="4%" class="values" align="center">
								<input type="checkbox" name="chkSelect<?php echo $ctr;?>" id="chkSelect<?php echo $ctr;?>" value="<?php echo $ctr;?>" onClick="javascript:chkSel(<?php echo $ctr;?>)">								
								<input type="hidden" name="hidStatus<?php echo $ctr;?>" id="hidStatus<?php echo $ctr;?>" value="<?php echo "$status";?>">						
							</td>
							<td width="12%" class="values">&nbsp;<?php echo "$strORNo";?>
								<input type="hidden" name="hidORNo<?php echo $ctr;?>" id="hidORNo<?php echo $ctr;?>" value="<?php echo "$strORNo";?>">														
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
							</td>					
							<td width="9%" class="values" align="right">&nbsp;<?php echo "$dblDeductedAmount";?>&nbsp;
							</td>											
						</tr>						
						<?php } } ?>
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
<script language="javascript">
function hov(loc,cls) {   
	if(loc.className)   
	loc.className=cls;   
} 

function save_text()
   {
   var w = frmORProcessing.cboRealProperty.selectedIndex;
   frmORProcessing.hidRealPropertyName.value = frmORProcessing.cboRealProperty.options[w].text;
   }

function txtKeyword_onKeyUp(e) {
	if (e.keyCode==13) {
		cmdSearch_onClick();
	}
}

function chkSelectAll_OnClick() {
	var ctr
	ctr = frmORProcessing.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmORProcessing.chkSelect" + i);
		obj1 = eval("frmORProcessing.hidStatus" + i);
		if (frmORProcessing.chkSelectAll.checked == true) {
			if (obj1.value == "P")
				obj.checked = true;
		}
		else {
			obj.checked = false;
		}
	}
}

function cmdSearch_OnClick() {
	if (frmORProcessing.DPC_txtDateFrom.value == "" && frmORProcessing.DPC_txtDateTo.value != "") {
		alert("Please provide OR Date From")
		frmORProcessing.DPC_txtDateFrom.focus()
		return false
	}
	if (frmORProcessing.DPC_txtDateFrom.value != "") {
		if (isDate(frmORProcessing.DPC_txtDateFrom.value)==false) {
			frmORProcessing.DPC_txtDateFrom.focus()
			return false
		}
	}
	
	if (frmORProcessing.DPC_txtDateFrom.value != "" && frmORProcessing.DPC_txtDateTo.value == "") {
		alert("Please provide OR Date To")
		frmORProcessing.DPC_txtDateTo.focus()
		return false
	}
	
	if (frmORProcessing.DPC_txtDateTo.value != "") {
		if (isDate(frmORProcessing.DPC_txtDateTo.value)==false) {
			frmORProcessing.DPC_txtDateTo.focus()
			return false
		}
	}
	
	if (frmORProcessing.DPC_txtDateFrom.value != "" && frmORProcessing.DPC_txtDateTo.value != "") {
		if (CompareDates(frmORProcessing.DPC_txtDateFrom.value,frmORProcessing.DPC_txtDateTo.value)==false) {
			frmORProcessing.DPC_txtDateFrom.focus()
			return false
		}
	}
	
	frmORProcessing.hidMode.value = "BUILD";
	frmORProcessing.submit();
}

function chkSel(i) {	
	obj = eval("frmORProcessing.chkSelect" + i);
	if (obj.checked == true) {
		obj.checked = false;
	}
	else
		obj.checked = true;
}

function cmdApply_OnClick(ctr) {
	var j
	j=0
	k=0
	if (ctr == 0) {
		totalctr = frmORProcessing.hidRowCtr.value;
		for (i=1;i<=totalctr;i++) {
			obj = eval("frmORProcessing.chkSelect" + i);
			if (obj.checked == true) {
				j++;
				k = i;
			}
		}
	}
	else {
		k = ctr;
		j = 1;
	}

	if (j > 1) {
		alert("Select one OR only");
	}
	else if (j == 0) {
		alert("Select an OR first");
	}
	else if (j == 1) {
		obj = eval("frmORProcessing.hidORNo" + k);
		parent.frames[2].location = "or_processing_advance_payment_apply_invoice.php?menu_id=" + frmORProcessing.hidMenuID.value + "&or_no=" + obj.value;
	}
}

function cmdORBreakdown_OnClick(pCtr) {
	obj = eval("frmORProcessing.hidORNo" + pCtr);
	obj1 = eval("frmORProcessing.hidClientName" + pCtr);
	parent.frames[2].location = "advance_payment_or_processing_breakdown_main.php?menu_id=" + frmORProcessing.hidMenuID.value + "&invoice_no=" + obj.value + "&client_name=" + obj1.value;
	return false;
}

</script>