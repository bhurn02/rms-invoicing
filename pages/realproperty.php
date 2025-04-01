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

$strCode = "";
$strName = "";
$strCompany = "";
$strDBA = "";
$strAddress1 = "";
$strAddress2 = "";
$strContactNo = "";
$strContactNo2 = "";
$intTotalUnits = "1";
$strLotSpace = "";
$strSpaceType = "A";
$strCostCenter = "";
$strRemarks = "";
$strMsg = "";
$strSaveMode = "";
$strMode = $_POST["hidMode"];
$strSaveMode = $_POST["hidSaveMode"];

if ($_GET["mode"] == "FIND") {
	$strCode = $_GET["code"];
	$strMode = "FIND";
}

if ($strMode != "") {	
	if ($strMode != "FIND") {
		$strCode = replacesinglequote($_POST["hidCode"]);
	}
	$strName = replacesinglequote($_POST["txtName"]);
	$strCompany = replacesinglequote($_POST["txtCompany"]);
	$strDBA = replacesinglequote($_POST["txtDBA"]);
	$strAddress1 = replacesinglequote($_POST["txtAddress1"]);
	$strAddress2 = replacesinglequote($_POST["txtAddress2"]);
	$strContactNo = replacesinglequote($_POST["txtContactNo"]);
	$strContactNo2 = replacesinglequote($_POST["txtContactNo2"]);
	$intTotalUnits = replacesinglequote($_POST["txtTotalUnits"]);
	$strSpaceType = replacesinglequote($_POST["cboSpaceType"]);
	$strCostCenter= replacesinglequote($_POST["txtCostCenter"]);
	$strLotSpace = replacesinglequote($_POST["txtLotSpace"]);
	$strRemarks = replacesinglequote($_POST["txtRemarks"]);
	$uid = $sessUserID;
	$company_code = $sessCompanyCode;
	
	//echo $strMode;
	//echo $strSaveMode;
	switch ($strMode) {
		case "SAVE":
			if ($strSaveMode != "EDIT") {		
				$strCode = replacesinglequote($_POST["txtCode"]);		
				$sqlquery="exec sp_m_RealProperty 'FIND','" . $strCode . "','" . $strName . "','" . $strCompany . "','" . $strDBA . "','" . $strAddress1 . "','" . $strAddress2 . "','" . $strContactNo . "','" . $strContactNo2 . "'," . $intTotalUnits . ",'" . $strLotSpace . "','" . $strSpaceType . "','" . $strRemarks . "','" . $strCostCenter . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
				$process=odbc_exec($sqlconnect, $sqlquery);
				if (odbc_fetch_row($process)) {
						if (odbc_result($process,"x") == 1) 
							$strMsg = "Real Property Code already exists!";
				}
			}
			
			if ($strMsg == "") {
				$sqlquery="exec sp_m_RealProperty 'SAVE','" . $strCode . "','" . $strName . "','" . $strCompany . "','" . $strDBA . "','" . $strAddress1 . "','" . $strAddress2 . "','" . $strContactNo . "','" . $strContactNo2 . "'," . $intTotalUnits . ",'" . $strLotSpace . "','" . $strSpaceType . "','" . $strRemarks . "','" . $strCostCenter . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
				$process=odbc_exec($sqlconnect, $sqlquery);
				$strMsg = "Record saved!";
				$strCode = "";
				$strName = "";
				$strCompany = "";
				$strDBA = "";
				$strAddress1 = "";
				$strAddress2 = "";
				$strContactNo = "";
				$strContactNo2 = "";
				$intTotalUnits = "1";
				$strLotSpace = "";
				$strSpaceType = "A";
				$strCostCenter = "";
				$strRemarks = "";
				$strMode = "";
				$strSaveMode = "";
			}
			//echo $sqlquery;
			//die();
			break;
		
		case "DELETE":
			$sqlquery="exec sp_m_RealProperty 'DELETE','" . $strCode . "','" . $strName . "','" . $strCompany . "','" . $strDBA . "','" . $strAddress1 . "','" . $strAddress2 . "','" . $strContactNo . "','" . $strContactNo2 . "'," . $intTotalUnits . ",'" . $strLotSpace . "','" . $strSpaceType . "','" . $strRemarks . "','" . $strCostCenter . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			if (odbc_fetch_row($process)) {
				if (odbc_result($process,"x") == 1) 
					$strMsg = "Record cannot be deleted. Remove assignment first from Units module.";
				else
					$strMsg = "Record deleted!";
					$strCode = "";
					$strName = "";
					$strCompany = "";
					$strDBA = "";
					$strAddress1 = "";
					$strAddress2 = "";
					$strContactNo = "";
					$strContactNo2 = "";
					$intTotalUnits = "1";
					$strLotSpace = "";
					$strSpaceType = "A";
					$strCostCenter = "";
					$strRemarks = "";
					$strMode = "";
					$strSaveMode = "";
			}
			break;
			
		case "RETRIEVE" || "FIND":
			$sqlquery="exec sp_m_RealProperty 'RETRIEVE','" . $strCode . "','" . $strName . "','" . $strCompany . "','" . $strDBA . "','" . $strAddress1 . "','" . $strAddress2 . "','" . $strContactNo . "','" . $strContactNo2 . "',0,'" . $strLotSpace . "','" . $strSpaceType . "','" . $strRemarks . "','" . $strCostCenter . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			if (odbc_fetch_row($process)) {
					$strCode = odbc_result($process,"real_property_code");
					$strName = odbc_result($process,"real_property_name");
					$strCompany = odbc_result($process,"real_property_company_name");
					$strDBA = odbc_result($process,"real_property_dba_name");
					$strAddress1 = odbc_result($process,"address1");
					$strAddress2 = odbc_result($process,"address2");
					$strContactNo = odbc_result($process,"contact_no1");
					$strContactNo2 = odbc_result($process,"contact_no2");
					$intTotalUnits = odbc_result($process,"tot_no_of_units");
					$strLotSpace = odbc_result($process,"lot_space");
					$strSpaceType = odbc_result($process,"space_type");
					$strRemarks = odbc_result($process,"remarks");
					$strCostCenter = odbc_result($process,"cost_center");
					$company_code = odbc_result($process,"company_code");
					$strMode = "RETRIEVE";
					$strSaveMode = "EDIT";
			}
			else {
				$strMsg = "No record found!";
				$strCode = "";
				$strMode = "";
			}
			break;
	}
}
//echo $sqlquery;
if ($strMsg != ""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 

?>
<html> 
<head> 
<title>REAL PROPERTY</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color: #F3F5B4;">
<form id="frmRealProperty" name="frmRealProperty" method="post" action="realproperty.php?menu_id=<?php echo $menu_id;?>">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">REAL PROPERTY
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  </a></li>	
			  <li class="li_nc"><a href="#" onClick="javascript:cmdRetrieve_OnClick()">|&nbsp;&nbsp;&nbsp;Retrieve&nbsp;&nbsp;&nbsp;|</a></li>
			  <li class="li_nc"><a href="#" onClick="javascript:cmdSave_OnClick()">Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php if ($strMode != "RETRIEVE") { ?>
					<li><a name="Delete" style="color: #666666">Delete&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } else { ?>			
					<li class="li_nc"><a href="#" onClick="javascript:cmdDelete_OnClick()">Delete&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } ?>	
			  <li class="li_nc"><a href="#" onClick="javascript:change_loc('realproperty_list.php?menu_id=<?php echo $menu_id;?>')">Find&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
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
							<td class="fieldname">REAL PROPERTY CODE :<em class="requiredRed">*</em></td>
							<td width="20"></td>
							<?php if ($strMode == "RETRIEVE") {?>
								<td><input type=text name="txtCode" id="txtCode" class="values" size="20" maxlength="5" disabled value="<?php echo $strCode?>"></td>
							<?php } else {?>
								<td><input type=text name="txtCode" id="txtCode" class="values" size="20" maxlength="10" value="<?php echo $strCode?>"></td>
							<?php } ?>
							<input type="hidden" id="hidCode" name="hidCode" value="<?php echo $strCode?>">
						</tr>					
						<tr>
							<td class="fieldname">REAL PROPERTY NAME :<em class="requiredRed">*</em></td>
							<td width="20"></td>
							<td><input type=text name="txtName" id="txtName" class="values" size="100" maxlength="100" value="<?php echo $strName?>"></td>
						</tr>	
						<tr>
							<td class="fieldname">COMPANY :<em class="requiredRed">*</em></td>
							<td width="20"></td>
							<td><input type=text name="txtCompany" id="txtCompany" class="values" size="70" maxlength="100" value="<?php echo $strCompany?>"></td>
						</tr>	
						<tr>
							<td class="fieldname">DBA :<em class="requiredRed">*</em></td>
							<td width="20"></td>
							<td><input type=text name="txtDBA" id="txtDBA" class="values" size="70" maxlength="100" value="<?php echo $strDBA?>"></td>
						</tr>	
						<tr>
							<td class="fieldname">TOTAL NO. OF UNITS :<em class="requiredRed">*</em></td>
							<td width="20"></td>
							<td><input type=text name="txtTotalUnits" id="txtTotalUnits" class="values" size="10" maxlength="5" value="<?php echo $intTotalUnits?>"></td>
						</tr>	
						<tr>
							<td class="fieldname">TYPE :<em class="requiredRed">*</em> </td>
							<td width="20"></td>
							<td>
								<select id="cboSpaceType" name="cboSpaceType" class="values">
									<?php if ($strSpaceType=="A") { ?>
										<option value="A" selected>APARTMENT</option>
									<?php } else { ?>
										<option value="A">APARTMENT</option>
									<?php } if ($strSpaceType=="O") { ?>
										<option value="O" selected>OFFICE</option>
									<?php } else { ?>
										<option value="O">OFFICE</option>
									<?php } if ($strSpaceType=="W") { ?>
										<option value="W" selected>WAREHOUSE</option>
									<?php } else { ?>
										<option value="W">WAREHOUSE</option>
									<?php } ?>
								</select>
							</td>
						</tr>		
						<tr>
							<td class="fieldname">COST CENTER :<em class="requiredRed">*</em></td>
							<td width="20"></td>
							<td><input type=text name="txtCostCenter" id="txtCostCenter" class="values" size="30" maxlength="20" value="<?php echo $strCostCenter?>"></td>
						</tr>	
						<tr>
							<td class="fieldname">LOT SPACE :</td>
							<td width="20"></td>
							<td><input type=text name="txtLotSpace" id="txtLotSpace" class="values" size="30" maxlength="20" value="<?php echo $strLotSpace?>"></td>
						</tr>
						<tr>
							<td class="fieldname">ADDRESS 1 :</td>
							<td width="20"></td>
							<td><input type=text name="txtAddress1" id="txtAddress1" class="values" size="70" maxlength="50" value="<?php echo $strAddress1?>"></td>
						</tr>					
						<tr>
							<td class="fieldname">ADDRESS 2 :</td>
							<td width="20"></td>
							<td><input type=text name="txtAddress2" id="txtAddress2" class="values" size="70" maxlength="50" value="<?php echo $strAddress2?>"></td>
						</tr>			
						<tr>
							<td class="fieldname">CONTACT NO. 1 :</td>
							<td width="20"></td>
							<td><input type=text name="txtContactNo" id="txtContactNo" class="values" size="30" maxlength="20" value="<?php echo $strContactNo?>"></td>
						</tr>		
						<tr>
							<td class="fieldname">CONTACT NO. 2 :</td>
							<td width="20"></td>
							<td><input type=text name="txtContactNo2" id="txtContactNo2" class="values" size="30" maxlength="20" value="<?php echo $strContactNo2?>"></td>
						</tr>								
						<tr>
							<td class="fieldname">REMARKS :</td>
							<td width="20"></td>
							<td><textarea name="txtRemarks" id="txtRemarks" class="values" rows="3" cols="40"><?php echo $strRemarks?></textarea></td>
						</tr>				
					</table>
				</td>
			</tr>
		</table>
		</td>
		</tr>
	</table>
	<input type="hidden" id="hidMode" name="hidMode">
	<input type="hidden" id="hidSaveMode" name="hidSaveMode" value="<?php echo $strSaveMode ?>">
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
	if (trim(frmRealProperty.txtCode.value) == "") {
		alert("Real Property Code is required")
		frmRealProperty.txtCode.focus()
		return false
	}
	if (trim(frmRealProperty.txtName.value) == "") {
		alert("Real Property Name is required")
		frmRealProperty.txtName.focus()
		return false
	}
	if (trim(frmRealProperty.txtCompany.value) == "") {
		alert("Company is required")
		frmRealProperty.txtCompany.focus()
		return false
	}
	if (trim(frmRealProperty.txtDBA.value) == "") {
		alert("DBA is required")
		frmRealProperty.txtDBA.focus()
		return false
	}
	if (isNaN(frmRealProperty.txtTotalUnits.value)) {
		alert("Please input a valid numeric value")
		frmRealProperty.txtTotalUnits.focus()
		return false
	}
	if (frmRealProperty.txtTotalUnits.value == 0 || trim(frmRealProperty.txtTotalUnits.value) == "") {
		alert("Total no. of Units is required")
		frmRealProperty.txtTotalUnits.focus()
		return false
	}
	if (trim(frmRealProperty.txtCostCenter.value) == "") {
		alert("Cost Center is required")
		frmRealProperty.txtCostCenter.focus()
		return false
	}
	frmRealProperty.hidMode.value = "SAVE";
	frmRealProperty.submit();
}

function cmdRetrieve_OnClick() {
	frmRealProperty.hidCode.value = frmRealProperty.txtCode.value;
	frmRealProperty.hidMode.value = "RETRIEVE";
	frmRealProperty.submit();
}

function cmdCancel_OnClick() {
	parent.frames[2].location = "realproperty.php?menu_id=" + frmRealProperty.hidMenuID.value;
	return false;
}

function cmdDelete_OnClick() {
	if (trim(frmRealProperty.txtCode.value) == "") {
		alert("Real Property Code is blank")
		frmRealProperty.txtCode.focus()
		return false
	}
	else {
		if (confirm("Are you sure you want to delete this record?")) {
			frmRealProperty.hidMode.value = "DELETE";
			frmRealProperty.submit();
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
