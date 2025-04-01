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
$strRealPropertyCode = "";
$strBuildingCode = "";
$strUnitNo = "";
$strMsg = "";
//echo $strMode;
//echo $sqlqueryCharges;
$sqlquerycbo="select * from m_charges order by charge_desc";
$processcbo=odbc_exec($sqlconnect, $sqlquerycbo);
while (odbc_fetch_row($processcbo)) {
	$cbocharge .= "<option value=\"" . trim(odbc_result($processcbo,"charge_code")) . "\">" . trim(strtoupper(odbc_result($processcbo,"charge_desc"))) . "</option>";
}

if ($_GET["mode"] == "FIND") {
	$strRealPropertyCode = $_GET["real_property_code"];
	$strBuildingCode = $_GET["building_code"];
	$strUnitNo = $_GET["unit_no"];
	$strMode = "FIND";
}

if ($strMode != "") {
	if ($strMode != "FIND") {
		if ($strMode != "RETRIEVE") {
			$strRealPropertyCode = replacesinglequote($_POST["hidRealProperty"]);
			$strBuildingCode = replacesinglequote($_POST["hidBuildingCode"]);
			$strUnitNo = replacesinglequote($_POST["hidUnitNo"]);		
		}		
		else {
			$strRealPropertyCode = replacesinglequote($_POST["txtRealProperty"]);
			$strBuildingCode = replacesinglequote($_POST["txtBuildingCode"]);
			$strUnitNo = replacesinglequote($_POST["txtUnitNo"]);		
		}		
		$uid = $sessUserID;
		$company_code = $sessCompanyCode;
	}
	//echo $strMode;
	switch ($strMode) {
		case "SAVE":
			$strRealPropertyCode = replacesinglequote($_POST["hidRealProperty"]);
			$strBuildingCode = replacesinglequote($_POST["hidBuildingCode"]);
			$strUnitNo = replacesinglequote($_POST["hidUnitNo"]);		
			$strChargeCode = replacesinglequote($_POST["hidChargeCode"]);		
			$dblChargeAmount = $_POST["hidChargeAmount"];		
			if ($strSaveMode == "ADD_CHARGE") {				
				$sqlquery="exec sp_m_UnitCharges 'FIND','" . $strRealPropertyCode . "','" . $strBuildingCode . "','" . $strUnitNo . "','" . $strChargeCode . "',0,'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
				//echo $sqlquery;
				$process=odbc_exec($sqlconnect, $sqlquery);
				if (odbc_fetch_row($process)) {
						if (odbc_result($process,"x") == 1) 
							$strMsg = "Charge is already assigned in this unit!";
				}
			}
			
			if ($strMsg == "") {
				$sqlquery="exec sp_m_UnitCharges 'SAVE','" . $strRealPropertyCode . "','" . $strBuildingCode . "','" . $strUnitNo . "','" . $strChargeCode . "'," . $dblChargeAmount . ",'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
				$process=odbc_exec($sqlconnect, $sqlquery);
				$strMsg = "Record saved!";		
			}

			$strMode = "FIND";

			break;
			//echo $strMode;
		case "DELETE":
			$i = 1;
			$j = 0;
			$k = 0;
			while ($i <= $_POST["hidRowCtr"]) {
				if (isset($_POST["chkDelete" . strval($i)])) {
					$strChargeCode = replacesinglequote($_POST["hidEditChargeCode" . strval($i)]);		
					$dblChargeAmount = 0;		
					$sqlquery="exec sp_m_UnitCharges 'DELETE','" . $strRealPropertyCode . "','" . $strBuildingCode . "','" . $strUnitNo . "','" . replacesinglequote($_POST["hidEditChargeCode" . strval($i)]) ."',0,'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
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
				
			$strMode = "FIND";
			
			break;
	}
}
else {
	$strRealPropertyCode = "";
	$strBuildingCode = "";
	$strUnitNo = "";
}

if ($strMode == "RETRIEVE" || $strMode == "FIND") {
	if ($strMode == "RETRIEVE") 
		$sqlquery="exec sp_m_UnitCharges 'RETRIEVE_REAL_PROPERTY','" . $strRealPropertyCode . "','" . $strBuildingCode . "','" . $strUnitNo . "','',0,'','',''";	
	else
		$sqlquery="exec sp_m_UnitCharges 'FIND_REAL_PROPERTY','" . $strRealPropertyCode . "','" . $strBuildingCode . "','" . $strUnitNo . "','',0,'','',''";	
	//echo $sqlquery;
	$process=odbc_exec($sqlconnect, $sqlquery);
	if (odbc_fetch_row($process)) {
			$strRealPropertyCode = replacedoublequotes(odbc_result($process,"real_property_code"));
			$strRealPropertyName = replacedoublequotes(odbc_result($process,"real_property_name"));
			$strBuildingCode = replacedoublequotes(odbc_result($process,"building_code"));
			$strUnitNo = replacedoublequotes(odbc_result($process,"unit_no"));					
			
			if ($strMode == "RETRIEVE") 
				$sqlqueryCharges="exec sp_m_UnitCharges 'RETRIEVE','" . $strRealPropertyCode . "','" . $strBuildingCode . "','" . $strUnitNo . "','',0,'','',''";	
			else
				$sqlqueryCharges="exec sp_m_UnitCharges 'RETRIEVE_FIND','" . $strRealPropertyCode . "','" . $strBuildingCode . "','" . $strUnitNo . "','',0,'','',''";	
			//echo $sqlqueryCharges;
			$processCharges=odbc_exec($sqlconnect, $sqlqueryCharges);
			$strMode = "RETRIEVE";
			$strSaveMode = "EDIT";
	}
}
//echo $strMode;
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
<form name="frmUnitCharges" id="frmUnitCharges" method="post" action="unit_charges.php?menu_id=<?php echo $menu_id;?>">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">UNIT CHARGES
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			 </a></li>	
			  <li class="li_nc"><a href="#" onClick="javascript:cmdRetrieve_OnClick()">|&nbsp;&nbsp;&nbsp;Retrieve&nbsp;&nbsp;&nbsp;|</a></li>			  
			  <?php if ($strMode != "RETRIEVE") { ?>
			  		<li><a name="Save" style="color: #666666">Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
					<li><a name="Delete" style="color: #666666">Delete&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } else { ?>			
			  		<li class="li_nc"><a href="#" onClick="javascript:cmdSave_OnClick()">Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
					<li class="li_nc"><a href="#" onClick="javascript:cmdDelete_OnClick()">Delete&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } ?>	
			  <li class="li_nc"><a href="#" onClick="javascript:change_loc('unit_charges_list.php?menu_id=<?php echo $menu_id;?>')">Find&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
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
										<td class="fieldname">REAL PROPERTY :<em class="requiredRed">*</em></td>
										<td width="20">&nbsp;</td>		
										<?php if ($strMode == "RETRIEVE") {?>								
											<td>
												<input type=text name="txtRealProperty" id="txtRealProperty" disabled class="values" size="55" value="<?php echo $strRealPropertyName;?>">									    
											</td>
										<?php } else {?>								
											<td>
												<input type=text name="txtRealProperty" id="txtRealProperty" class="values" size="55" value="<?php echo $strRealPropertyName;?>">									    
											</td>
										<?php }?>	
									   <input type="hidden" id="hidRealProperty" name="hidRealProperty" value="<?php echo $strRealPropertyCode;?>">
									</tr>	
									<tr>
										<td class="fieldname">BUILDING :<em class="requiredRed">*</em></td>
										<td width="20">&nbsp;</td>			
										<?php if ($strMode == "RETRIEVE") {?>									
											<td><input type=text name="txtBuildingCode" id="txtBuildingCode" disabled class="values" size="20" value="<?php echo $strBuildingCode;?>"></td>
										<?php } else {?>									
											<td><input type=text name="txtBuildingCode" id="txtBuildingCode" class="values" size="20" value="<?php echo $strBuildingCode;?>"></td>
										<?php } ?>									
										<input type="hidden" id="hidBuildingCode" name="hidBuildingCode" value="<?php echo $strBuildingCode;?>">
									</tr>
									<tr>
										<td class="fieldname">UNIT NO. :<em class="requiredRed">*</em></td>
										<td width="20">&nbsp;</td>	
										<?php if ($strMode == "RETRIEVE") {?>										
											<td><input type=text name="txtUnitNo" id="txtUnitNo" disabled class="values" size="10" value="<?php echo $strUnitNo;?>"></td>
										<?php } else {?>
											<td><input type=text name="txtUnitNo" id="txtUnitNo" class="values" size="10" value="<?php echo $strUnitNo;?>"></td>
										<?php } ?>
										<input type="hidden" id="hidUnitNo" name="hidUnitNo" value="<?php echo $strUnitNo;?>">
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
							<td width="15%" class="tablehdr" align="center">&nbsp;Charge Code<em class="requiredYellow">*</em>&nbsp;
							</td>
							<td  width="40%" class="tablehdr" align="center">&nbsp;Description<em class="requiredYellow">*</em>&nbsp;
							</td>
							<td  width="30%" class="tablehdr" align="center">&nbsp;GL Code&nbsp;
							</td>
							<td  width="10%" class="tablehdr" align="center">&nbsp;Amount&nbsp;
							</td>
						</tr>
						<?php
						$ctr = 0;
						while(odbc_fetch_row($processCharges)) {
							$charge_code = odbc_result($processCharges,"charge_code"); 
							$charge_desc = odbc_result($processCharges,"charge_desc"); 
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
							<td width="15%" style="border:1px" class="values">&nbsp;<?php echo "$charge_code";?>&nbsp;
							<input type="hidden" id="hidEditChargeCode<?php echo "$ctr";?>" name="hidEditChargeCode<?php echo "$ctr";?>" value="<?php echo $charge_code;?>">
							</td>
							<td width="40%" style="border:1px" class="values">
								<span id="spCharge<?php echo "$ctr";?>" name="spCharge<?php echo "$ctr";?>" style="cursor:hand;visibility:'';display:''">&nbsp;<?php echo "$charge_desc";?>&nbsp;</span>								
							</td>
							<td width="30%" style="border:1px" class="values">&nbsp;<?php echo "$gl_code";?>&nbsp;
							</td>
							<td width="10%" style="border:1px" class="values" align="right">
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
						<tr id="addRow" name="addRow" bgcolor="<?php echo "$rowColor" ?>">
							<td width="5%" align="center" style="border:1px" >&nbsp;
							</td>
							<td width="25%" style="border:1px" class="values">&nbsp;
							</td>
							<td width="50%" style="border:1px" class="values">&nbsp;
								<select name="cboAddCharge" id="cboAddCharge" class="values">
									<option value="">- Select Charge -</option>
									<?php echo $cbocharge; ?>
								</select>
							</td>
							<td width="20%" style="border:1px" class="values">&nbsp;
							</td>
							<td width="20%" style="border:1px" class="values" align="center">
								<input type=text name="txtAddChargeAmount" id="txtAddChargeAmount" class="values" style="text-align:right" size="10" value="0.00">
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
	if (frmUnitCharges.hidSaveMode.value != "EDIT_CHARGE") {
		frmUnitCharges.hidCurRow.value = ctr;
		
		obj = eval("spChargeAmount" + ctr);
		obj.style.visibility = "hidden"
		obj.style.display = "none"
		
		obj = eval("spEditChargeAmount" + ctr);
		obj.style.visibility = "visible"
		obj.style.display = ""
		
		enabledisablechkboxes(0);
		frmUnitCharges.chkSelectAll.disabled = true;
		frmUnitCharges.chkSelectAll.checked = false;
		frmUnitCharges.cboAddCharge.disabled = true;
		frmUnitCharges.txtAddChargeAmount.disabled = true;
		frmUnitCharges.hidSaveMode.value = "EDIT_CHARGE";
	}
}

function enabledisablechkboxes(pVal) {
	var ctr
	ctr = frmUnitCharges.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmUnitCharges.chkDelete" + i);		
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
	if (frmUnitCharges.hidRealProperty.value == "" && frmUnitCharges.hidBuildingCode.value == "" && frmUnitCharges.hidUnitNo.value == "") {
		alert("Select Unit first!")
		frmUnitCharges.txtRealProperty.focus()
		return false
	}
	
	if (frmUnitCharges.hidSaveMode.value == "EDIT_CHARGE") {
		ctr = frmUnitCharges.hidCurRow.value;
		
		obj = eval("frmUnitCharges.hidEditChargeCode" + ctr);		
		frmUnitCharges.hidChargeCode.value = obj.value;
		
		obj = eval("frmUnitCharges.txtEditChargeAmount" + ctr);
		if (trim(obj.value) == "" || obj.value == 0) {
			alert("Amount is required")
			obj.focus()
			return false
		}
		if (isNaN(obj.value)) {
			alert("Invalid numeric value")
			obj.focus()
			return false
		}
		frmUnitCharges.hidChargeAmount.value = obj.value;
		frmUnitCharges.hidSaveMode.value = "EDIT_CHARGE";
	}
	else {
		if (frmUnitCharges.cboAddCharge.value == "") {
			alert("Charge is required")
			frmUnitCharges.cboAddCharge.focus()
			return false
		}
		if (trim(frmUnitCharges.txtAddChargeAmount.value) == "" || frmUnitCharges.txtAddChargeAmount.value == 0) {
			alert("Amount is required")
			frmUnitCharges.txtAddChargeAmount.focus()
			return false
		}
		if (isNaN(frmUnitCharges.txtAddChargeAmount.value)) {
			alert("Invalid numeric value")
			frmUnitCharges.txtAddChargeAmount.focus()
			return false
		}
		frmUnitCharges.hidChargeCode.value = frmUnitCharges.cboAddCharge.value;
		frmUnitCharges.hidChargeAmount.value = frmUnitCharges.txtAddChargeAmount.value;
		frmUnitCharges.hidSaveMode.value = "ADD_CHARGE";
	}
	frmUnitCharges.hidMode.value = "SAVE";
	frmUnitCharges.submit();
}

function cmdRetrieve_OnClick() {
	frmUnitCharges.hidMode.value = "RETRIEVE";
	frmUnitCharges.submit();
}

function cmdCancel_OnClick() {
	ctr = frmUnitCharges.hidCurRow.value;
	//alert(frmUnitCharges.hidSaveMode.value)
	if (frmUnitCharges.hidSaveMode.value == "EDIT_CHARGE") {
		
		obj = eval("spEditChargeAmount" + ctr);
		obj.style.visibility = "hidden"
		obj.style.display = "none"
		
		obj = eval("spChargeAmount" + ctr);
		obj.style.visibility = "visible"
		obj.style.display = ""
		
		frmUnitCharges.chkSelectAll.disabled = false;
		frmUnitCharges.chkSelectAll.checked = false;
		frmUnitCharges.cboAddCharge.disabled = false;
		frmUnitCharges.txtAddChargeAmount.disabled = false;
		frmUnitCharges.hidSaveMode.value = "";
		enabledisablechkboxes(1);
		return false;
	}
	else {
		parent.frames[2].location = "unit_charges.php?menu_id=" + frmUnitCharges.hidMenuID.value;
		return false;
	}
}

function cmdDelete_OnClick() {
	var j
	j=0
	totalctr = frmUnitCharges.hidRowCtr.value;
	for (i=1;i<=totalctr;i++) {
		obj = eval("frmUnitCharges.chkDelete" + i);
		if (obj.checked == true) {
			j++;
		}
	}
	if (j > 0) {
		if (confirm("Are you sure you want to delete this record/s?")) {
			frmUnitCharges.hidMode.value = "DELETE";
			frmUnitCharges.submit();
		}
	}
	else {
		alert("Deleting is not allowed this time");
	}
}

function chkSelectAll_OnClick() {
	var ctr
	ctr = frmUnitCharges.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmUnitCharges.chkDelete" + i);
		if (frmUnitCharges.chkSelectAll.checked == true) {
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

