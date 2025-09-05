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
$intStatus = 0;
$intEmailSent = 0;
$strEmailSent = "";

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
	$intStatus = $_POST["cboStatus"];
	if (isset($_POST["chkEmailSent"])) {	
		$intEmailSent = 1;
		$strEmailSent = "checked";
	}
	
	//echo $strRealPropertyName;
	if ($strMode == "TAG" || $strMode == "UNTAG") {			
		$i = 1;
		$j = 0;
		while ($i <= $_POST["hidRowCtr"]) {
			if (isset($_POST["chkSelect" . strval($i)])) {					
				$strInvoiceNo = $_POST["hidInvoiceNo" . strval($i)];
				$sqlquery="exec sp_u_Send_Invoice_Save '" . $strMode . "','" . $strInvoiceNo . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
				//echo $sqlquery;
				$process=odbc_exec($sqlconnect, $sqlquery);					
				$j++;
			}
			$i++;
		}
		if ($strMode == "TAG") 
			$msg = "tagged";
		elseif ($strMode == "UNTAG") 
			$msg = "untagged";
			//echo $strMode;
		if ($j > 0)
			$strMsg = "Invoice/s " . $msg . "!";
		else
			$strMsg = "No invoice " . $msg . "!";
		
		$strMode = "SEARCH";
	}
}	

if ($strMsg != ""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 

//echo $strTagged;
//echo $sqlquery;
?>
<html> 
<head> 
<title>INVOICE ALERT TAGGING</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
<script type="text/javascript" src="library/datepickercontrol/datepickercontrol.js"></script>
<link type="text/css" rel="stylesheet" href="library/datepickercontrol/datepickercontrol_green.css">
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color: #F3F5B4;">
<form name="frmInvoiceAlertTag" id="frmInvoiceAlertTag" method="post">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">INVOICE ALERT TAGGING
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			 </a></li>	
			  <li class="li_nc"><a href="#" onClick="javascript:cmdSearch_OnClick()">|&nbsp;&nbsp;&nbsp;Search&nbsp;&nbsp;&nbsp;|</a></li>			  			  
			  <li class="li_nc"><a href="#" onClick="javascript:cmdSave_OnClick(1)">Tag&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>			  			  
			  <li class="li_nc"><a href="#" onClick="javascript:cmdSave_OnClick(0)">Untag&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>			  			  
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
										<td class="fieldname" align="right">STATUS :</td>
										<td width="10"></td>
										<td>
											<select name="cboStatus" id="cboStatus" class="values" onChange="javascript:cmdSearch_OnClick()">												
												<?php if ($intStatus == 0) {?>
													<option selected value=0>All</option>
													<option value=1>Tagged</option>
													<option value=2>Untagged</option>
												<?php } else if ($intStatus == 1) { ?>
													<option value=0>All</option>
													<option selected value=1>Tagged</option>
													<option value=2>Untagged</option>
												<?php } else if ($intStatus == 2) { ?>
													<option value=0>All</option>
													<option value=1>Tagged</option>
													<option selected value=2>Untagged</option>
												<?php } ?>
											</select>
										</td>
									</tr>			
									<tr>
										<td class="fieldname" align="right">&nbsp;</td>
										<td width="10"></td>
										<td class="values"><input type="checkbox" name="chkEmailSent" id="chkEmailSent" class="values" <?php echo $strEmailSent; ?> onClick="javascript:cmdSearch_OnClick()">E-mail Sent</td>
									</tr>	
									<tr>
										<td class="fieldname" align="right">SORT BY :</td>
										<td width="10"></td>
									  <td>
											<select name="cboSortBy" id="cboSortBy" class="values" onChange="javascript:cmdSearch_OnClick()">												
												<?php if ($strSortBy == "" || $strSortBy == "INVOICE NO.") {?>
													<option selected value="INVOICE NO.">Invoice No.</option>
													<option value="INVOICE DATE">Invoice Date</option>
													<option value="CLIENT">Client</option>
													<option value="UNIT NO.">Unit No.</option>
													<option value="STATUS">Status</option>
												<?php } else if ($strSortBy == "INVOICE DATE") { ?>
													<option value="INVOICE NO.">Invoice No.</option>
													<option selected value="INVOICE DATE">Invoice Date</option>
													<option value="CLIENT">Client</option>
													<option value="UNIT NO.">Unit No.</option>
													<option value="STATUS">Status</option>
												<?php } else if ($strSortBy == "CLIENT") { ?>
													<option value="INVOICE NO.">Invoice No.</option>
													<option value="INVOICE DATE">Invoice Date</option>
													<option selected value="CLIENT">Client</option>
													<option value="UNIT NO.">Unit No.</option>
													<option value="STATUS">Status</option>
												<?php } else if ($strSortBy == "UNIT NO.") { ?>
													<option value="INVOICE NO.">Invoice No.</option>
													<option value="INVOICE DATE">Invoice Date</option>
													<option selected value="CLIENT">Client</option>
													<option selected value="UNIT NO.">Unit No.</option>
													<option value="STATUS">Status</option>
												<?php } else { ?>
													<option value="INVOICE NO.">Invoice No.</option>
													<option value="INVOICE DATE">Invoice Date</option>
													<option value="CLIENT">Client</option>
													<option value="UNIT NO.">Unit No.</option>
													<option selected value="STATUS">Status</option>
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
					<table width="1100" style="border:1px solid #556b2f;padding: 30px 10px 5px; width:auto;">
						<tr height="30">
							<td width="41" align="center"  class="tablehdr">&nbsp;Sel&nbsp;
							</td>
							<td width="110" align="center"  class="tablehdr">&nbsp;Invoice No.&nbsp;
							</td>
							<td width="101" align="center"  class="tablehdr">&nbsp;Date&nbsp;
							</td>													
							<td width="272" align="center"  class="tablehdr">&nbsp;Client&nbsp;
							</td>							
							<td width="119" align="center"  class="tablehdr">&nbsp;Unit No.&nbsp;
							</td>	
							<td width="80" align="center"  class="tablehdr">&nbsp;Total Amount&nbsp;
							</td>													
							<td width="65" align="center"  class="tablehdr">&nbsp;Tagged&nbsp;
							</td>
							<?php if ($intEmailSent == 1) { ?>
								<td width="65" align="center"  class="tablehdr">&nbsp;E-mail Sent&nbsp;
								</td>	
								<td width="120" align="center"  class="tablehdr">&nbsp;E-mail Address&nbsp;
								</td>
								<td width="120" align="center" class="tablehdr">&nbsp;Date & Time Sent&nbsp;
								</td>								
							<?php } ?>
						</tr>	
						<?php	
						$ctr = 0;		
						$sqlquery="exec sp_u_Send_Invoice_Search 'SEARCH','','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $strRealPropertyCode . "','" . $strSortBy . "'," . $intStatus . "," . $intEmailSent . ",'','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
						$process=odbc_exec($sqlconnect, $sqlquery);			
						//echo $sqlquery;
						while(odbc_fetch_row($process)) {
							$ctr++;
							$invoice_no = odbc_result($process,"invoice_no"); 
							$invoice_date = date("m/d/Y",(strtotime(odbc_result($process,"invoice_date"))+60*60*24*($OFFSET)));	
							$client_code = odbc_result($process,"client_code"); 
							$client_name = odbc_result($process,"client_name"); 
							$unit_no = odbc_result($process,"unit_no"); 	
							$total_amount = odbc_result($process,"total_amount"); 	
							$tagged = odbc_result($process,"tagged"); 	
							$email_sent = odbc_result($process,"email_sent"); 	
							$email_addr = odbc_result($process,"email_addr"); 	
							//$date_sent = odbc_result($process,"date_email_sent");
							$date_sent = date("m/d/Y H:i:s A",(strtotime(odbc_result($process,"date_email_sent"))+60*60*24*($OFFSET)));	 	
							$disSelect = "";
							$style = "border:1px;cursor:hand;";
							
							//if ($email_sent == 1) {
							//	$disSelect = "disabled";
							//	$style = "border:1px;cursor:none;";
							//}
							
							if ($ctr%2==1) 
								$rowColor = "98fb98";	
							else
								$rowColor = "ffffe0";		
							
						?>
							<tr bgcolor="<?php echo "$rowColor" ?>">
								<td align="center" style="<?php echo $style;?>">
									<input type="checkbox" name="chkSelect<?php echo "$ctr" ?>" id="chkSelect<?php echo "$ctr" ?>" <?php echo $disSelect;?>>
									<input type="hidden" name="hidInvoiceNo<?php echo "$ctr" ?>" id="hidInvoiceNo<?php echo "$ctr" ?>" value="<?php echo "$invoice_no" ?>">
									<input type="hidden" name="hidTagged<?php echo "$ctr" ?>" id="hidTagged<?php echo "$ctr" ?>" value=<?php echo "$tagged"; ?>>
								</td>
								<td  style="border:1px" class="values">&nbsp;<?php echo "$invoice_no";?>&nbsp;
								</td>
								<td style="border:1px" class="values" align="center">&nbsp;<?php echo "$invoice_date";?>&nbsp;
								</td>							
								<td  style="border:1px" class="values">&nbsp;<?php echo "$client_name";?>&nbsp;
								</td>							
								<td  style="border:1px" class="values">&nbsp;<?php echo "$unit_no";?>&nbsp;
								</td>								
								<td style="border:1px" class="values" align="right"><?php echo numberformat($total_amount);?>&nbsp;
								</td>
								<?php if ($tagged==1) { ?>
									<td  style="border:1px" class="values" align="center">&nbsp;
										<img src="images/check.gif">
									</td>
								<?php } else { ?>
									<td  style="border:1px" class="values" align="center">&nbsp;
									</td>
								<?php } ?>
								<?php if ($intEmailSent == 1) { 
									if ($email_sent == 1) {
								?>
									<td  style="border:1px" class="values" align="center">&nbsp;
										<img src="images/check.gif">
									</td>
								<?php } else { ?>
									<td  style="border:1px" class="values" align="center">&nbsp;
									</td>
								<?php } ?>
									<td  style="border:1px" class="values">&nbsp;<?php echo "$email_addr";?>&nbsp;
									</td>	
									<td  style="border:1px" class="values" align="center">&nbsp;<?php echo "$date_sent";?>&nbsp;
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
   var w = frmInvoiceAlertTag.cboRealProperty.selectedIndex;
   frmInvoiceAlertTag.hidRealPropertyName.value = frmInvoiceAlertTag.cboRealProperty.options[w].text;
   cmdSearch_OnClick();
   }

function txtKeyword_onKeyUp(e) {
	if (e.keyCode==13) {
		cmdSearch_onClick();
	}
}

function chkSelectAll_OnClick() {
	var ctr
	ctr = frmInvoiceAlertTag.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmInvoiceAlertTag.chkSelect" + i);
		if (frmInvoiceAlertTag.chkSelectAll.checked == true) {
			obj.checked = true;
		}
		else {
			obj.checked = false;
		}
	}
}

function cmdSearch_OnClick() {
	if (frmInvoiceAlertTag.DPC_txtDateFrom.value == "") {
		alert("Please provide Invoice Date From")
		frmInvoiceAlertTag.DPC_txtDateFrom.focus()
		return false
	}
	if (frmInvoiceAlertTag.DPC_txtDateFrom.value != "") {
		if (isDate(frmInvoiceAlertTag.DPC_txtDateFrom.value)==false) {
			frmInvoiceAlertTag.DPC_txtDateFrom.focus()
			return false
		}
	}
	
	if (frmInvoiceAlertTag.DPC_txtDateTo.value == "") {
		alert("Please provide Invoice Date To")
		frmInvoiceAlertTag.DPC_txtDateTo.focus()
		return false
	}
	if (frmInvoiceAlertTag.DPC_txtDateTo.value != "") {
		if (isDate(frmInvoiceAlertTag.DPC_txtDateTo.value)==false) {
			frmInvoiceAlertTag.DPC_txtDateTo.focus()
			return false
		}
	}
		
	if (frmInvoiceAlertTag.DPC_txtDateFrom.value != "" && frmInvoiceAlertTag.DPC_txtDateTo.value != "") {
		if (CompareDates(frmInvoiceAlertTag.DPC_txtDateFrom.value,frmInvoiceAlertTag.DPC_txtDateTo.value)==false) {
			frmInvoiceAlertTag.DPC_txtDateFrom.focus()
			return false
		}
	}

	frmInvoiceAlertTag.hidMode.value = "SEARCH";
	frmInvoiceAlertTag.submit();
}

function cmdSave_OnClick(intTag) {
	var j,strDesc,$strMsg
	j=0
	totalctr = frmInvoiceAlertTag.hidRowCtr.value;
	for (i=1;i<=totalctr;i++) {
		obj = eval("frmInvoiceAlertTag.chkSelect" + i);
		if (obj.checked == true) {
			j++;
		}
	}
	
	if (intTag == 1) {
		strDesc = "TAG"
		strMsg = "Are you sure you want to TAG this invoice/s for e-mail alert?"		
	}
	else {
		strDesc = "UNTAG"
		strMsg = "Are you sure you want to UNTAG this invoice/s for e-mail alert? Note: If invoice is already sent and untagged, e-mail information will be erased. You may TAG again if you wish to resend."
	}
		
	if (j > 0) {
		if (confirm(strMsg)) {
			frmInvoiceAlertTag.hidMode.value = strDesc;
			frmInvoiceAlertTag.submit();
		}
	}
	else {
		alert("Select invoice/s to " + strDesc + " first");
	}
}

function cmdClose_OnClick() {
	parent.frames[2].location = "blank.htm";
	return false;
}

</script>