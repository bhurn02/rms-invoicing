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

$strMode = trim($_POST["hidMode"]);
$strSaveMode = $_POST["hidSaveMode"];
$strCode = "";
$strName = "";
$strChargeCode = "";		
$dblChargeAmount = 0;	
$dblTotalChargeAmount = "0.00";
$strMsg = "";
//echo $strMode;
//echo $sqlqueryCharges;
$sqlquerycbo="select * from m_charges order by charge_desc";
$processcbo=odbc_exec($sqlconnect, $sqlquerycbo);
while (odbc_fetch_row($processcbo)) {
	$cbocharge .= "<option value=\"" . trim(odbc_result($processcbo,"charge_code")) . "\">" . trim(strtoupper(odbc_result($processcbo,"charge_desc"))) . "</option>";
}

if ($_GET["mode"] == "FIND") {
	$strCode = $_GET["code"];
	$strMode = "FIND";
}

if ($strMode != "") {
	if ($strMode != "FIND") {
		if ($strMode != "RETRIEVE") {
			$strCode = replacesinglequote($_POST["hidCode"]);
		}		
		else {
			$strCode = replacesinglequote($_POST["txtCode"]);
		}		
		$uid = $sessUserID;
		$company_code = $sessCompanyCode;
	}
	//echo $strSaveMode;
	switch ($strMode) {
		case "SAVE":
			$strCode = replacesinglequote($_POST["hidCode"]);
			$strChargeCode = replacesinglequote($_POST["hidChargeCode"]);		
			$dblChargeAmount = $_POST["hidChargeAmount"];		
			if ($strSaveMode == "ADD_CHARGE") {				
				$sqlquery="exec sp_m_TenantCharges 'FIND','" . $strCode . "','" . $strChargeCode . "'," . $dblChargeAmount . ",'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
				$process=odbc_exec($sqlconnect, $sqlquery);
				if (odbc_fetch_row($process)) {
						if (odbc_result($process,"x") == 1) 
							$strMsg = "Charge is already assigned in this tenant!";
				}
			}
			
			if ($strMsg == "") {
				$sqlquery="exec sp_m_TenantCharges 'SAVE','" . $strCode . "','" . $strChargeCode . "'," . $dblChargeAmount . ",'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
				$process=odbc_exec($sqlconnect, $sqlquery);
				$strMsg = "Record saved!";		
			}
			//echo $sqlquery;
			$sqlquery="exec sp_m_TenantCharges 'RETRIEVE_TENANT','" . $strCode . "','" . $strChargeCode . "'," . $dblChargeAmount . ",'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			if (odbc_fetch_row($process)) {
					$strCode = replacedoublequotes(odbc_result($process,"tenant_code"));
					$strName = replacedoublequotes(odbc_result($process,"tenant_name"));
					$dblTotalChargeAmount = odbc_result($process,"total_amount");
					$strMode = "RETRIEVE";
					$strSaveMode = "EDIT";
					
					$sqlqueryCharges="exec sp_m_TenantCharges 'RETRIEVE','" . $strCode . "','" . $strChargeCode . "'," . $dblChargeAmount . ",'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					$processCharges=odbc_exec($sqlconnect, $sqlqueryCharges);
			}
			else {
				$strMode = "";
			}		
			break;
			//echo $sqlquery;
		case "DELETE":
			$i = 1;
			$j = 0;
			$k = 0;
			while ($i <= $_POST["hidRowCtr"]) {
				if (isset($_POST["chkDelete" . strval($i)])) {
					$strChargeCode = replacesinglequote($_POST["hidEditChargeCode" . strval($i)]);		
					$dblChargeAmount = 0;		
					$sqlquery="exec sp_m_TenantCharges 'DELETE','" . $strCode . "','" . $strChargeCode . "'," . $dblChargeAmount . ",'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
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
			if ($j > 0) {
				$strMsg = "Some records were not deleted.";
			}
			elseif ($k > 0) {
				$strMsg = "Record/s deleted!";
			}
			else
				$strMsg = "No record deleted!";
				
			$sqlquery="exec sp_m_TenantCharges 'RETRIEVE_TENANT','" . $strCode . "','" . $strChargeCode . "'," . $dblChargeAmount . ",'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			if (odbc_fetch_row($process)) {
					$strCode = replacedoublequotes(odbc_result($process,"tenant_code"));
					$strName = replacedoublequotes(odbc_result($process,"tenant_name"));
					$dblTotalChargeAmount = odbc_result($process,"total_amount");
					$strMode = "RETRIEVE";
					$strSaveMode = "EDIT";
					
					$sqlqueryCharges="exec sp_m_TenantCharges 'RETRIEVE','" . $strCode . "','" . $strChargeCode . "'," . $dblChargeAmount . ",'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					$processCharges=odbc_exec($sqlconnect, $sqlqueryCharges);
			}
			else {
				$strMode = "";
			}
			break;
			
		case "RETRIEVE" || "FIND":
			$sqlquery="exec sp_m_TenantCharges 'RETRIEVE_TENANT','" . $strCode . "','" . $strChargeCode . "'," . $dblChargeAmount . ",'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			//echo $sqlquery;			
			$process=odbc_exec($sqlconnect, $sqlquery);
			if (odbc_fetch_row($process)) {
					$strCode = replacedoublequotes(odbc_result($process,"tenant_code"));
					$strName = replacedoublequotes(odbc_result($process,"tenant_name"));
					$dblTotalChargeAmount = odbc_result($process,"total_amount");
					$strMode = "RETRIEVE";
					$strSaveMode = "EDIT";
					
					$sqlqueryCharges="exec sp_m_TenantCharges 'RETRIEVE','" . $strCode . "','" . $strChargeCode . "'," . $dblChargeAmount . ",'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					$processCharges=odbc_exec($sqlconnect, $sqlqueryCharges);
			}
			else {
				$strMode = "";
			}
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
<title>CHARGES</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
</head> 
<body style="margin:'0';background-color: #F3F5B4;">
<form name="frmTenantCharges" id="frmTenantCharges" method="post" action="tenant_charges.php?menu_id=<?php echo $menu_id;?>">
	<div class="mainmenu">	
		<ul>
			  <?php if ($strMode != "RETRIEVE") { ?>
					<li class="li_nc"><a name="MODULE NAME">TENANT</a></li>	
			  <?php } else { ?>		
			  		<li class="li_nc"><a href="#" onClick="javascript:change_loc('tenant.php?menu_id=4&mode=FIND&menu=DETAIL&code=<?php echo $strCode; ?>')"><u>TENANT</u></a></li>	
			  <?php } ?>				   			 
			  <li class="li_nc"><a name="MODULE NAME">>>&nbsp;&nbsp;&nbsp;CHARGES&nbsp;&nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;	</a></li>
			  <li class="li_nc"><a href="#" onClick="javascript:cmdRetrieve_OnClick()">|&nbsp;&nbsp;&nbsp;Retrieve&nbsp;&nbsp;&nbsp;|</a></li>			  
			  <?php if ($strMode != "RETRIEVE") { ?>
			  		<li><a name="Save" style="color: #666666">Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
					<li><a name="Delete" style="color: #666666">Delete&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } else { ?>			
			  		<li class="li_nc"><a href="#" onClick="javascript:cmdSave_OnClick()">Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
					<li class="li_nc"><a href="#" onClick="javascript:cmdDelete_OnClick()">Delete&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } ?>	
			  <li class="li_nc"><a href="#" onClick="javascript:change_loc('tenant_charges_list.php?menu_id=<?php echo $menu_id;?>')">Find&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
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
										<td class="fieldname">TENANT CODE :<em class="requiredRed">*</em></td>
										<td width="20">&nbsp;</td>		
										<?php if ($strMode == "RETRIEVE") {?>								
											<td>
												<input type=text name="txtCode" id="txtCode" disabled class="values" size="20" value="<?php echo $strCode;?>">									    
											</td>
										<?php } else {?>								
											<td>
												<input type=text name="txtCode" id="txtCode" class="values" size="20" value="<?php echo $strCode;?>">									    
											</td>
										<?php }?>	
									   <input type="hidden" id="hidCode" name="hidCode" value="<?php echo $strCode;?>">
									</tr>	
									<tr>
										<td class="fieldname">NAME :<em class="requiredRed">*</em></td>
										<td width="20">&nbsp;</td>			
										<?php if ($strMode == "RETRIEVE") {?>									
											<td><input type=text name="txtName" id="txtName" disabled class="values" size="60" value="<?php echo $strName;?>"></td>
										<?php } else {?>									
											<td><input type=text name="txtName" id="txtName" class="values" size="60" value="<?php echo $strName;?>"></td>
										<?php } ?>									
									</tr>																					
								</table>
							</td>
						</tr>
					</table>
					<p></p>
					<table width="700" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
						<tr height="30">
							<td width="5%" class="tablehdr" align="center">&nbsp;Del&nbsp;
							</td>
							<td width="19%" class="tablehdr" align="center">&nbsp;Charge Code<em class="requiredYellow">*</em>&nbsp;
							</td>
							<td  width="36%" class="tablehdr" align="center">&nbsp;Description<em class="requiredYellow">*</em>&nbsp;
							</td>
							<td  width="21%" class="tablehdr" align="center">&nbsp;GL Code&nbsp;
							</td>
							<td  width="19%" class="tablehdr" align="center">&nbsp;Amount<em class="requiredYellow">*</em>&nbsp;
							</td>
						</tr>
						<?php
						$ctr = 0;
						while(odbc_fetch_row($processCharges)) {
							$charge_code = odbc_result($processCharges,"charge_code"); 
							$charge_desc = strtoupper(odbc_result($processCharges,"charge_desc")); 
							$gl_code = odbc_result($processCharges,"gl_code"); 
							if (is_null(odbc_result($processCharges,"charge_amount")))
								$charge_amount = "0.00";
							else
								$charge_amount = odbc_result($processCharges,"charge_amount"); 
								
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
							<td width="21%" style="border:1px" class="values">&nbsp;<?php echo "$gl_code";?>&nbsp;
							</td>
							<td width="19%" style="border:1px" class="values" align="right">
								<span id="spChargeAmount<?php echo "$ctr";?>" name="spChargeAmount<?php echo "$ctr";?>" style="cursor:hand;visibility:'';display:''"><?php echo "$charge_amount";?>&nbsp;</span>
								<span id="spEditChargeAmount<?php echo "$ctr";?>" name="spEditChargeAmount<?php echo "$ctr";?>" style="visibility:hidden;display:none">
									<input type=text name="txtEditChargeAmount<?php echo "$ctr";?>" id="txtEditChargeAmount<?php echo "$ctr";?>" class="values" style="text-align:right" size="10" value="<?php echo "$charge_amount";?>">
								</span>
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
						?>					
						<tr id="" name="" bgcolor="<?php echo "$rowColor"; ?>">
							<td width="5%" align="center" style="border:1px" >&nbsp;
							</td>
							<td width="19%" style="border:1px" class="values">&nbsp;<b>TOTAL AMOUNT</b>
							</td>
							<td width="36%" style="border:1px" class="values">&nbsp;								
							</td>
							<td width="21%" style="border:1px" class="values">&nbsp;
							</td>							
							<td width="19%" style="border:1px" class="values" align="right"><b>$<?php echo "$dblTotalChargeAmount"; ?></b>&nbsp;						
							</td>							
						</tr>
						<?php
								if (($ctr + 1)%2==1) 
									$rowColor = "ffffe0";	
								else
									$rowColor = "98fb98";								
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
							<td width="21%" style="border:1px" class="values">&nbsp;
							</td>
							<td width="19%" style="border:1px" class="values" align="right">
								<input type=text name="txtAddChargeAmount" id="txtAddChargeAmount" class="values" style="text-align:right" size="15" value="0.00">&nbsp;
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
	<input type="hidden" id="hidMode" name="hidMode">
	<input type="hidden" id="hidSaveMode" name="hidSaveMode" value="<?php echo $strSaveMode; ?>">
	<input type="hidden" id="hidRowCtr" name="hidRowCtr" value=<?php echo $ctr;?>>
	<input type="hidden" id="hidCurRow" name="hidCurRow">
	<input type="hidden" id="hidChargeCode" name="hidChargeCode">
	<input type="hidden" id="hidChargeAmount" name="hidChargeAmount">
	<input type="hidden" id="hidMenuID" name="hidMenuID" value=<?php echo $menu_id;?>>
</form>
</body> 
</html>

<script type="text/javascript">
function editMode(ctr) {
	if (frmTenantCharges.hidSaveMode.value != "EDIT_CHARGE") {
		frmTenantCharges.hidCurRow.value = ctr;
		
		obj = eval("spChargeAmount" + ctr);
		obj.style.visibility = "hidden"
		obj.style.display = "none"
		
		obj = eval("spEditChargeAmount" + ctr);
		obj.style.visibility = "visible"
		obj.style.display = ""
		
		enabledisablechkboxes(0);
		frmTenantCharges.chkSelectAll.disabled = true;
		frmTenantCharges.chkSelectAll.checked = false;
		frmTenantCharges.cboAddCharge.disabled = true;
		frmTenantCharges.txtAddChargeAmount.disabled = true;
		frmTenantCharges.hidSaveMode.value = "EDIT_CHARGE";
	}
}

function enabledisablechkboxes(pVal) {
	var ctr
	ctr = frmTenantCharges.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmTenantCharges.chkDelete" + i);		
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
	if (frmTenantCharges.hidCode.value == "") {
		alert("Select Tenant first!")
		frmTenantCharges.txtCode.focus()
		return false
	}
	
	if (frmTenantCharges.hidSaveMode.value == "EDIT_CHARGE") {
		ctr = frmTenantCharges.hidCurRow.value;
		
		obj = eval("frmTenantCharges.hidEditChargeCode" + ctr);		
		frmTenantCharges.hidChargeCode.value = obj.value;
		
		obj = eval("frmTenantCharges.txtEditChargeAmount" + ctr);
		if (trim(obj.value) == "" || Number(obj.value)==0) {
			alert("Amount is required")
			obj.focus()
			return false
		}
		if (isNaN(obj.value)) {
			alert("Invalid numeric value")
			obj.focus()
			return false
		}
		frmTenantCharges.hidChargeAmount.value = obj.value;
		frmTenantCharges.hidSaveMode.value = "EDIT_CHARGE";
	}
	else {
		if (frmTenantCharges.cboAddCharge.value == "") {
			alert("Charge is required")
			frmTenantCharges.cboAddCharge.focus()
			return false
		}
		if (trim(frmTenantCharges.txtAddChargeAmount.value) == "" || Number(frmTenantCharges.txtAddChargeAmount.value)==0) {
			alert("Amount is required")
			frmTenantCharges.txtAddChargeAmount.focus()
			return false
		}
		if (isNaN(frmTenantCharges.txtAddChargeAmount.value)) {
			alert("Invalid numeric value")
			frmTenantCharges.txtAddChargeAmount.focus()
			return false
		}
		frmTenantCharges.hidChargeCode.value = frmTenantCharges.cboAddCharge.value;
		frmTenantCharges.hidChargeAmount.value = frmTenantCharges.txtAddChargeAmount.value;
		frmTenantCharges.hidSaveMode.value = "ADD_CHARGE";
	}
	frmTenantCharges.hidMode.value = "SAVE";
	frmTenantCharges.submit();
}

function cmdRetrieve_OnClick() {
	frmTenantCharges.hidMode.value = "RETRIEVE";
	frmTenantCharges.submit();
}

function cmdCancel_OnClick() {
	ctr = frmTenantCharges.hidCurRow.value;
	//alert(frmTenantCharges.hidSaveMode.value)
	if (frmTenantCharges.hidSaveMode.value == "EDIT_CHARGE") {
		obj = eval("spEditChargeAmount" + ctr);
		obj.style.visibility = "hidden"
		obj.style.display = "none"
		
		obj = eval("spChargeAmount" + ctr);
		obj.style.visibility = "visible"
		obj.style.display = ""
		
		frmTenantCharges.cboAddCharge.disabled = false;
		frmTenantCharges.txtAddChargeAmount.disabled = false;
		frmTenantCharges.chkSelectAll.disabled = false;
		frmTenantCharges.chkSelectAll.checked = false;
		frmTenantCharges.hidSaveMode.value = "";
		enabledisablechkboxes(1);
		return false;
	}
	else {
		parent.frames[2].location = "tenant_charges.php?menu_id=" + frmTenantCharges.hidMenuID.value;
		return false;
	}
}

function cmdDelete_OnClick() {
	var j
	j=0
	totalctr = frmTenantCharges.hidRowCtr.value;
	for (i=1;i<=totalctr;i++) {
		obj = eval("frmTenantCharges.chkDelete" + i);
		if (obj.checked == true) {
			j++;
		}
	}
	if (j > 0) {
		if (confirm("Are you sure you want to delete this record/s?")) {
			frmTenantCharges.hidMode.value = "DELETE";
			frmTenantCharges.submit();
		}
	}
	else {
		alert("Deleting is not allowed this time");
	}
}

function chkSelectAll_OnClick() {
	var ctr
	ctr = frmTenantCharges.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmTenantCharges.chkDelete" + i);
		if (frmTenantCharges.chkSelectAll.checked == true) {
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

function trim(stringToTrim) {
	return stringToTrim.replace(/^\s+|\s+$/g,"");
}
</script>

