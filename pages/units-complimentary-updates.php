<?php 
/**
 * FIXES:
 * 
 * 	2024-10-18	Aldrich
 * 	- Added settings for complimentary
 */
 
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

$strRealPropertyCode = "";
$strBldgCode = "";
$strUnitNo = "";
$strLotArea = "";
$intBedrooms = "0";
$strUnitType = "";
$strRemarks = "";
$blnReserved = "";
$blnComplimentary = (isset($_POST["chkComplimentary"]) && $_POST["chkComplimentary"] == "on")?1:0;
$strMsg = "";
$strMode = $_POST["hidMode"];
$strSaveMode = $_POST["hidSaveMode"];

if ($_GET["mode"] == "FIND") {
	$strRealPropertyCode = replacesinglequote($_GET["real_property_code"]);
	$strBldgCode = replacesinglequote($_GET["building_code"]);
	$strUnitNo = replacesinglequote($_GET["unit_no"]);
	$strMode = "FIND";
}

if ($strMode != "") {
	if ($strMode != "FIND") {
		$strRealPropertyCode = replacesinglequote($_POST["hidRealProperty"]);
		$strBldgCode = replacesinglequote($_POST["hidBldgCode"]);
		$strUnitNo = replacesinglequote($_POST["hidUnitNo"]);		
	}
	$strLotArea = replacesinglequote($_POST["txtLotArea"]);
	if ($_POST["txtBedrooms"] == "")
		$intBedrooms = 0;
	else
		$intBedrooms = replacesinglequote($_POST["txtBedrooms"]);
	$strUnitType = replacesinglequote($_POST["txtUnitType"]);
	$strRemarks = replacesinglequote($_POST["txtRemarks"]);
	if ($_POST["chkReserved"] == "on") {
		$blnReserved = "Y";
	}
	else {
		$blnReserved = "N";		
	}
	$uid = $sessUserID;
	$company_code = $sessCompanyCode;
	
	//echo $strSaveMode;
	switch ($strMode) {
		case "SAVE":
			if ($strSaveMode != "EDIT") {
				$strRealPropertyCode = replacesinglequote($_POST["cboRealProperty"]);
				$strBldgCode = replacesinglequote($_POST["txtBldgCode"]);
				$strUnitNo = replacesinglequote($_POST["txtUnitNo"]);		
				$sqlquery="exec sp_m_Units 'FIND','" . $strRealPropertyCode . "','" . $strBldgCode . "','" . $strUnitNo . "','" . $strLotArea . "'," . $intBedrooms . ",'" . $strUnitType . "','" . $strRemarks . "','" . $blnReserved . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "', ".$blnComplimentary."";	
				$process=odbc_exec($sqlconnect, $sqlquery);
				if (odbc_fetch_row($process)) {
						if (odbc_result($process,"x") == 1) 
							$strMsg = "Unit already exists!";
				}
			}
			
			if ($strMsg == "") {
				$sqlquery="exec sp_m_Units 'SAVE','" . $strRealPropertyCode . "','" . $strBldgCode . "','" . $strUnitNo . "','" . $strLotArea . "'," . $intBedrooms . ",'" . $strUnitType . "','" . $strRemarks . "','" . $blnReserved . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "', ".$blnComplimentary."";	
				$process=odbc_exec($sqlconnect, $sqlquery);
				$strMsg = "Record saved!";
				$strRealPropertyCode = "";
				$strBldgCode = "";
				$strUnitNo = "";
				$strLotArea = "";
				$intBedrooms = "0";
				$strUnitType = "";
				$strRemarks = "";
				$blnReserved = "";
				$strMode = "";
				$strSaveMode = "";
			}
			//echo $sqlquery;
			//die();
			break;
		
		case "DELETE":
			$sqlquery="exec sp_m_Units 'DELETE','" . $strRealPropertyCode . "','" . $strBldgCode . "','" . $strUnitNo . "','" . $strLotArea . "'," . $intBedrooms . ",'" . $strUnitType . "','" . $strRemarks . "','" . $blnReserved . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "', ".$blnComplimentary."";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			if (odbc_fetch_row($process)) {
				if (odbc_result($process,"x") == 1) 
					$strMsg = "Record cannot be deleted. Remove assignment first from Tenant module.";
				else
					$strMsg = "Record deleted!";
					$strRealPropertyCode = "";
					$strBldgCode = "";
					$strUnitNo = "";
					$strLotArea = "";
					$intBedrooms = "0";
					$strUnitType = "";
					$strRemarks = "";
					$blnReserved = "";
					$strMode = "";
					$strSaveMode = "";
			}
			break;
			
		case "RETRIEVE" || "FIND":
			$sqlquery="exec sp_m_Units 'RETRIEVE','" . $strRealPropertyCode . "','" . $strBldgCode . "','" . $strUnitNo . "','" . $strLotArea . "'," . $intBedrooms . ",'" . $strUnitType . "','" . $strRemarks . "','" . $blnReserved . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "', ".$blnComplimentary."";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			if (odbc_fetch_row($process)) {
					$strRealPropertyCode = odbc_result($process,"real_property_code");
					$strRealPropertyName = odbc_result($process,"real_property_name");
					$strBldgCode = odbc_result($process,"building_code");
					$strUnitNo = odbc_result($process,"unit_no");
					$strLotArea = odbc_result($process,"lot_area");
					$intBedrooms = odbc_result($process,"no_of_bedrooms");
					$strUnitType = odbc_result($process,"unit_type");					
					$strRemarks = odbc_result($process,"remarks");
					if (odbc_result($process,"is_reserved") == "Y") 
						$blnReserved = "checked";	
					else
						$blnReserved = "";	
					$strMode = "RETRIEVE";
					$strSaveMode = "EDIT";
			}
			else {
				$strMsg = "No record found!";
				$strMode = "";
				$strSaveMode = "";
			}
			break;
	}
}
//echo $strRealPropertyCode;
$sqlquery="select * from m_real_property order by real_property_name";
$process=odbc_exec($sqlconnect, $sqlquery);
while (odbc_fetch_row($process)) {
	//echo replacedoublequotes($strRealPropertyCode);
	//echo replacedoublequotes(trim(odbc_result($process,"real_property_code")));
	if (replacedoublequotes($strRealPropertyCode) == replacedoublequotes(trim(odbc_result($process,"real_property_code"))))
		$cborealproperty .= "<option selected value=\"" . trim(odbc_result($process,"real_property_code")) . "\">" . trim(strtoupper(odbc_result($process,"real_property_name"))) . "</option>";
	else
		$cborealproperty .= "<option value=\"" . trim(odbc_result($process,"real_property_code")) . "\">" . trim(strtoupper(odbc_result($process,"real_property_name"))) . "</option>";
}

if ($strMsg != ""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 

?>
<html> 
<head> 
<title>UNITS</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color: #F3F5B4;">
<form name="frmUnits" id="frmUnits" method="post" action="units.php?menu_id=<?php echo $menu_id;?>">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">UNITS
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			  
			 </a></li>	
			  <li class="li_nc"><a href="#" onClick="javascript:cmdRetrieve_OnClick()">|&nbsp;&nbsp;&nbsp;Retrieve&nbsp;&nbsp;&nbsp;|</a></li>
			  <li class="li_nc"><a href="#" onClick="javascript:cmdSave_OnClick()">Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php if ($strMode != "RETRIEVE") { ?>
					<li><a name="Delete" style="color: #666666">Delete&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } else { ?>			
					<li class="li_nc"><a href="#" onClick="javascript:cmdDelete_OnClick()">Delete&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } ?>	
			  <li class="li_nc"><a href="#" onClick="javascript:change_loc('units_list.php?menu_id=<?php echo $menu_id;?>')">Find&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
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
										<td width="20"></td>
										<?php if ($strMode == "RETRIEVE") {?>
											<td>
												<input type=text name="txtRealProperty" id="txtRealProperty" disabled class="values" size="50" value="<?php echo $strRealPropertyName; ?>">											
											</td>
										<?php } else {?>
											<td>
												<select name="cboRealProperty" id="cboRealProperty" class="values">
												<option value="">- Select Real Property -</option>
												<?php echo $cborealproperty; ?>
												</select>
											</td>
										<?php }?>
										<input type="hidden" id="hidRealProperty" name="hidRealProperty" value="<?php echo $strRealPropertyCode; ?>">
									</tr>					
									<tr>
										<td class="fieldname">BUILDING CODE :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<?php if ($strMode == "RETRIEVE") {?>
											<td><input type=text name="txtBldgCode" id="txtBldgCode" disabled class="values" size="20" maxlength="10" value="<?php echo $strBldgCode; ?>"></td>
										<?php } else {?>
												<td><input type=text name="txtBldgCode" id="txtBldgCode" class="values" size="20" maxlength="10" value="<?php echo $strBldgCode; ?>"></td>
										<?php }?>
										<input type="hidden" id="hidBldgCode" name="hidBldgCode" value="<?php echo $strBldgCode; ?>">
									</tr>	
									<tr>
										<td class="fieldname">UNIT NO. :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<?php if ($strMode == "RETRIEVE") {?>
											<td><input type=text name="txtUnitNo" id="txtUnitNo" disabled class="values" size="10" maxlength="10" value="<?php echo $strUnitNo; ?>"></td>
										<?php } else {?>
											<td><input type=text name="txtUnitNo" id="txtUnitNo" class="values" size="10" maxlength="10" value="<?php echo $strUnitNo; ?>"></td>
										<?php }?>
										<input type="hidden" id="hidUnitNo" name="hidUnitNo" value="<?php echo $strUnitNo; ?>">
									</tr>			
									<tr>
										<td class="fieldname">LOT AREA :</td>
										<td width="20"></td>
										<td><input type=text name="txtLotArea" id="txtLotArea" class="values" size="20" maxlength="20" value="<?php echo $strLotArea; ?>"></td>
									</tr>		
									<tr>
										<td class="fieldname">NO. OF BEDROOMS :</td>
										<td width="20"></td>
										<td><input type=text name="txtBedrooms" id="txtBedrooms" class="values" size="5" maxlength="5" value="<?php echo $intBedrooms; ?>"></td>
									</tr>		
									<tr>
										<td class="fieldname">UNIT TYPE :</td>
										<td width="20"></td>
										<td><input type=text name="txtUnitType" id="txtUnitType" class="values" size="30" maxlength="50" value="<?php echo $strUnitType; ?>"></td>
									</tr>																				
									<tr>
										<td class="fieldname">REMARKS :</td>
										<td width="20"></td>
										<td><textarea name="txtRemarks" id="txtRemarks" class="values" rows="3" cols="40"><?php echo $strRemarks; ?></textarea></td>
									</tr>			
									<tr>
										<td class="fieldname">&nbsp;</td>
										<td width="20"></td>
										<td class="values"><input name="chkReserved" id="chkReserved" type="checkbox" <?php echo $blnReserved;?>>RESERVED</td>
									</tr>	
									<tr>
										<td class="fieldname">&nbsp;</td>
										<td width="20"></td>
										<td class="values"><input name="chkComplimentary" id="chkComplimentary" type="checkbox" <?php echo ($blnComplimentary)?'checked':'';?>>COMPLIMENTARY</td>
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
</form>
</body> 
</html>

<script type="text/javascript">
function hov(loc,cls) {   
	if(loc.className)   
	loc.className=cls;   
} 

function cmdSave_OnClick() {
	if (frmUnits.hidSaveMode.value != "EDIT") {
		if (frmUnits.cboRealProperty.value == "") {
			alert("Real Property Code is required")
			frmUnits.cboRealProperty.focus()
			return false
		}
		if (frmUnits.txtBldgCode.value == "") {
			alert("Building Code is required")
			frmUnits.txtBldgCode.focus()
			return false
		}
		if (frmUnits.txtUnitNo.value == "") {
			alert("Unit No. is required")
			frmUnits.txtUnitNo.focus()
			return false
		}
	}
	if ((isNaN(frmUnits.txtBedrooms.value)) && (frmUnits.txtBedrooms.value != "")) {
		alert("Please input a valid numeric value")
		frmUnits.txtBedrooms.focus()
		return false
	}
	frmUnits.hidMode.value = "SAVE";
	frmUnits.submit();
}

function cmdRetrieve_OnClick() {
	frmUnits.hidRealProperty.value = frmUnits.cboRealProperty.value;
	frmUnits.hidBldgCode.value = frmUnits.txtBldgCode.value;
	frmUnits.hidUnitNo.value = frmUnits.txtUnitNo.value;
	frmUnits.hidMode.value = "RETRIEVE";
	frmUnits.submit();
}

function cmdCancel_OnClick() {
	parent.frames[2].location = "units.php?menu_id=" + frmUnits.hidMenuID.value;
	return false;
}

function cmdDelete_OnClick() {
	if (frmUnits.txtRealProperty.value == "") {
		alert("No record to be deleted!")
		frmUnits.txtRealProperty.focus()
		return false
	}
	else {
		if (confirm("Are you sure you want to delete this record?")) {
			frmUnits.hidMode.value = "DELETE";
			frmUnits.submit();
		}
	}
}

function change_loc(pFile) {
	parent.frames[2].location = pFile;
	return false;
}
</script>
