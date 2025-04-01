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

$strChargeCode = "";
$strChargeDesc = "";
$dtReadingFrom = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
$dtReadingTo = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
$dtBillingFrom = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
$dtBillingTo = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	

$strMsg = "";
$strSaveMode = "";
$strMode = $_POST["hidMode"];
$strSaveMode = $_POST["hidSaveMode"];

if ($_GET["mode"] == "FIND") {
	$strReadingID = $_GET["reading_id"];
	$strMode = "FIND";
}

if ($strMode != "") {
	$dtReadingFrom = $_POST["txtReadingFrom"];
	$dtReadingTo = $_POST["txtReadingTo"];
	$dtBillingFrom = $_POST["txtBillingFrom"];
	$dtBillingTo = $_POST["txtBillingTo"];
	$uid = $sessUserID;
	$company_code = $sessCompanyCode;
	
	//echo $strMode;
	switch ($strMode) {
		case "SAVE":			
			
			if ($_POST["hidRowCtr"] == 0) {
				$sqlquery="exec sp_s_TenantReading_Default_Save 'SAVE','','" . $dtReadingFrom . "','" . $dtReadingTo . "','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
				$process=odbc_exec($sqlconnect, $sqlquery);
			}
			else {
				$i = 1;
				while ($i <= $_POST["hidRowCtr"]) {
					$strChargeCode = replacesinglequote($_POST["hidEditChargeCode" . strval($i)]);		
					$sqlquery="exec sp_s_TenantReading_Default_Save 'SAVE','" . $strChargeCode . "','" . $dtReadingFrom . "','" . $dtReadingTo . "','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					$process=odbc_exec($sqlconnect, $sqlquery);
					$i++;
				}
				
			}
			if ($_POST["cboAddCharge"] != "") {
				$sqlquery="exec sp_s_TenantReading_Default_Save 'SAVE','" . $_POST["cboAddCharge"] . "','" . $dtReadingFrom . "','" . $dtReadingTo . "','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
				$process=odbc_exec($sqlconnect, $sqlquery);
			}
			
			$strMsg = "Record saved!";				
			break;
		
		case "DELETE":
			$i = 1;
			$j = 0;
			while ($i <= $_POST["hidRowCtr"]) {
				if (isset($_POST["chkDelete" . strval($i)])) {
					$strChargeCode = replacesinglequote($_POST["hidEditChargeCode" . strval($i)]);		
					$sqlquery="exec sp_s_TenantReading_Default_Delete '" . $strChargeCode . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					//echo $sqlquery;
					$process=odbc_exec($sqlconnect, $sqlquery);
					$j++;
				}
				$i++;
			}
			if ($j > 0)
				$strMsg = "Charge deleted!";	
			break;
	}
}

$sqlquery="exec sp_s_TenantReading_Default_Retrieve 'RETRIEVE'";	
//echo $sqlquery;
$process=odbc_exec($sqlconnect, $sqlquery);
if (odbc_fetch_row($process)) {			
	if (odbc_result($process,"trd_date_from") == "" || date("m/d/Y",(strtotime(odbc_result($process,"trd_date_from"))+60*60*24*($OFFSET))) == "01/01/1970")
		$dtReadingFrom = "";
	else
		$dtReadingFrom = date("m/d/Y",(strtotime(odbc_result($process,"trd_date_from"))+60*60*24*($OFFSET)));
	
	if (odbc_result($process,"trd_date_to") == "" || date("m/d/Y",(strtotime(odbc_result($process,"trd_date_to"))+60*60*24*($OFFSET))) == "01/01/1970")
		$dtReadingTo = "";
	else
		$dtReadingTo = date("m/d/Y",(strtotime(odbc_result($process,"trd_date_to"))+60*60*24*($OFFSET)));
				
	if (odbc_result($process,"trd_billing_date_from") == "" || date("m/d/Y",(strtotime(odbc_result($process,"trd_billing_date_from"))+60*60*24*($OFFSET))) == "01/01/1970")
		$dtBillingFrom = "";
	else
		$dtBillingFrom = date("m/d/Y",(strtotime(odbc_result($process,"trd_billing_date_from"))+60*60*24*($OFFSET)));
		
	if (odbc_result($process,"trd_billing_date_to") == "" || date("m/d/Y",(strtotime(odbc_result($process,"trd_billing_date_to"))+60*60*24*($OFFSET))) == "01/01/1970")
		$dtBillingTo = "";
	else
		$dtBillingTo = date("m/d/Y",(strtotime(odbc_result($process,"trd_billing_date_to"))+60*60*24*($OFFSET)));
		
	$sqlqueryCharges="exec sp_s_TenantReading_Default_Retrieve 'CHARGES'";	
	//echo $sqlquery;
	$processCharges=odbc_exec($sqlconnect, $sqlqueryCharges);
}
	
if ($strMsg != ""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 

?>
<html> 
<head> 
<title>TENANT READING - SET DEFAULTS</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color: #F3F5B4;">
<form name="frmTenantReadingDef" id="frmTenantReadingDef" method="post" action="tenant_reading_defaults.php?menu_id=<?php echo $menu_id;?>">
	<div class="mainmenu">	
		<ul>			 
			<li class="li_nc"><a href="#" onClick="javascript:change_loc('tenant_reading.php?menu_id=4')"><u>TENANT READING</u></a></li>				 
			<li class="li_nc"><a name="MODULE NAME">>>&nbsp;&nbsp;&nbsp;SET DEFAULTS&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			</a></li>			  
			<li class="li_nc"><a href="#" onClick="javascript:cmdSave_OnClick()">|&nbsp;&nbsp;&nbsp;&nbsp;Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			<li class="li_nc"><a href="#" onClick="javascript:cmdDelete_OnClick()">Delete Charge&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>					
			<li class="li_nc"><a href="#" onClick="javascript:change_loc('tenant_reading.php?menu_id=4')">Back&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>					
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
										<td class="fieldname">READING FROM (mm/dd/yyyy) :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtReadingFrom" id="txtReadingFrom" class="values" size="20" maxlength="10" value="<?php echo $dtReadingFrom;?>"></td>
									</tr>
									<tr>
										<td class="fieldname">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TO (mm/dd/yyyy) :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtReadingTo" id="txtReadingTo" class="values" size="20" maxlength="10" value="<?php echo $dtReadingTo;?>"></td>
									</tr>																																																				
									<tr>
										<td class="fieldname">BILLING FROM (mm/dd/yyyy) :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtBillingFrom" id="txtBillingFrom" class="values" size="20" maxlength="10" value="<?php echo $dtBillingFrom;?>"></td>
									</tr>
									<tr>
										<td class="fieldname">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TO (mm/dd/yyyy) :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtBillingTo" id="txtBillingTo" class="values" size="20" maxlength="10" value="<?php echo $dtBillingTo;?>"></td>
									</tr>																																													
								</table>					
								<table>
									<tr>
										<td width="240"></td>
										<td>
											<table width="450" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
												<tr height="30">
													<td width="5%" class="tablehdr" align="center">&nbsp;Del&nbsp;
													</td>
													<td width="19%" class="tablehdr" align="center">&nbsp;Charge Code<em class="requiredYellow">*</em>&nbsp;
													</td>
													<td  width="36%" class="tablehdr" align="center">&nbsp;Description<em class="requiredYellow">*</em>&nbsp;
													</td>							
												</tr>
												<?php
												$ctr = 0;
												while(odbc_fetch_row($processCharges)) {
													$charge_code = odbc_result($processCharges,"charge_code"); 
													$charge_desc = strtoupper(odbc_result($processCharges,"charge_desc")); 
																											
													if ($charge_code != "") {
														$ctr = $ctr+1;
														if ($ctr%2==1) 
															$rowColor = "98fb98";	
														else
															$rowColor = "ffffe0";		
												?>
												<tr id="editRow<?php echo "$ctr";?>" name="editRow<?php echo "$ctr";?>" style="cursor:hand" bgcolor="<?php echo "$rowColor" ?>" onDblClick="javascript:editMode(<?php echo "$ctr";?>);">
													<td width="5%" align="center" style="border:1px">
														<input type="checkbox" name="chkDelete<?php echo "$ctr";?>" id="chkDelete<?php echo "$ctr";?>">
													</td>
													<td width="19%" style="border:1px" class="values">&nbsp;<?php echo "$charge_code";?>&nbsp;
													<input type="hidden" id="hidEditChargeCode<?php echo "$ctr";?>" name="hidEditChargeCode<?php echo "$ctr";?>" value="<?php echo $charge_code;?>">
													</td>
													<td width="36%" style="border:1px" class="values">
														<span id="spCharge<?php echo "$ctr";?>" name="spCharge<?php echo "$ctr";?>" style="cursor:hand;visibility:'';display:''">&nbsp;<?php echo "$charge_desc";?>&nbsp;</span>								
													</td>							
												</tr>
												<?php } } ?>
												<?php
														if (($ctr + 1)%2==1) 
															$rowColor = "98fb98";								
														else
															$rowColor = "ffffe0";	
															
														$sqlquerycbo="exec sp_s_TenantReading_Default_Retrieve 'ADD_CHARGES_LIST'";
														$processcbo=odbc_exec($sqlconnect, $sqlquerycbo);
														while (odbc_fetch_row($processcbo)) {
															$cbocharge .= "<option value=\"" . trim(odbc_result($processcbo,"charge_code")) . "\">" . trim(strtoupper(odbc_result($processcbo,"charge_desc"))) . "</option>";
														}

												?>
												<tr id="addRow" name="addRow" bgcolor="<?php echo "$rowColor" ?>">
													<td width="5%" align="center" style="border:1px" >&nbsp;
													</td>
													<td width="19%" style="border:1px" class="values">&nbsp;
													</td>
													<td width="36%" style="border:1px" class="values">&nbsp;
														<select name="cboAddCharge" id="cboAddCharge" class="values">
															<option value="">- Select Charge -</option>
															<?php echo $cbocharge; ?>
														</select>
													</td>							
												</tr>
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
				</td>
			</tr>
		</table>
		</td>
		</tr>
	</table>
	<input type="hidden" id="hidMode" name="hidMode">
	<input type="hidden" id="hidSaveMode" name="hidSaveMode" value="<?php echo $strSaveMode; ?>">
	<input type="hidden" id="hidMenuID" name="hidMenuID" value=<?php echo $menu_id;?>>
	<input type="hidden" id="hidRowCtr" name="hidRowCtr" value=<?php echo $ctr;?>>
</form>
</body> 
</html>

<script language="javascript" src="jsp/function.js"></script>
<script language="javascript">
function hov(loc,cls) {   
	if(loc.className)   
	loc.className=cls;   
} 

function cmdRetrieve_OnClick() {
	frmTenantReadingDef.hidReadingID.value = frmTenantReadingDef.txtReadingID.value;
	frmTenantReadingDef.hidMode.value = "RETRIEVE";
	frmTenantReadingDef.submit();
}

function cmdSave_OnClick() {
	if (trim(frmTenantReadingDef.txtReadingFrom.value) == "") {
		alert("Date From is required")
		frmTenantReadingDef.txtReadingFrom.focus()
		return false
	}
	if (isDate(frmTenantReadingDef.txtReadingFrom.value)==false){
		frmTenantReadingDef.txtReadingFrom.focus()
		return false
	}
	if (trim(frmTenantReadingDef.txtReadingTo.value) == "") {
		alert("Date To is required")
		frmTenantReadingDef.txtReadingTo.focus()
		return false
	}
	if (isDate(frmTenantReadingDef.txtReadingTo.value)==false){
		frmTenantReadingDef.txtReadingTo.focus()
		return false
	}
	
	if (trim(frmTenantReadingDef.txtReadingFrom.value) != "" && trim(frmTenantReadingDef.txtReadingTo.value) != "") {
		if (CompareDates(trim(frmTenantReadingDef.txtReadingFrom.value),trim(frmTenantReadingDef.txtReadingTo.value)) == false) {
			frmTenantReadingDef.txtReadingFrom.focus();
			return false;
		}
	}
		
	if (trim(frmTenantReadingDef.txtBillingFrom.value) == "") {
		alert("Billing From is required")
		frmTenantReadingDef.txtBillingFrom.focus()
		return false
	}
	if (isDate(frmTenantReadingDef.txtBillingFrom.value)==false){
		frmTenantReadingDef.txtBillingFrom.focus()
		return false
	}
	if (trim(frmTenantReadingDef.txtBillingTo.value) == "") {
		alert("Billing To is required")
		frmTenantReadingDef.txtBillingTo.focus()
		return false
	}
	if (isDate(frmTenantReadingDef.txtBillingTo.value)==false){
		frmTenantReadingDef.txtBillingTo.focus()
		return false
	}
	if (trim(frmTenantReadingDef.txtBillingFrom.value) != "" && trim(frmTenantReadingDef.txtBillingTo.value) != "") {
		if (CompareDates(trim(frmTenantReadingDef.txtBillingFrom.value),trim(frmTenantReadingDef.txtBillingTo.value)) == false) {
			frmTenantReadingDef.txtBillingFrom.focus();
			return false;
		}
	}
	
	frmTenantReadingDef.hidMode.value = "SAVE";
	frmTenantReadingDef.submit();
}

function cmdDelete_OnClick() {
	var j
	j=0
	totalctr = frmTenantReadingDef.hidRowCtr.value;
	for (i=1;i<=totalctr;i++) {
		obj = eval("frmTenantReadingDef.chkDelete" + i);
		if (obj.checked == true) {
			j++;
		}
	}
	if (j > 0) {
		if (confirm("Are you sure you want to delete this record/s?")) {
			frmTenantReadingDef.hidMode.value = "DELETE";
			frmTenantReadingDef.submit();
		}
	}
	else {
		alert("Deleting is not allowed this time");
	}
}

function cmdCancel_OnClick() {
	parent.frames[2].location = "tenant_reading.php?menu_id=" + frmTenantReadingDef.hidMenuID.value;
	return false;
}

function cmdTenantSearch_onClick() {
	window.open ("tenant_search_reading.php?menu_id=" + frmTenantReadingDef.hidMenuID.value,"displayWindow","type=fullwindow,titlebar=no,scrollbars=yes");
	return false;
}

function chkSelectAll_OnClick() {
	var ctr
	ctr = frmTenantReadingDef.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmTenantReadingDef.chkDelete" + i);
		if (frmTenantReadingDef.chkSelectAll.checked == true) {
			obj.checked = true;			
		}
		else {
			obj.checked = false;
		}
	}
}

</script>