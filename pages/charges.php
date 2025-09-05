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
$strDesc = "";
$strGLCode = "";
$strChargeType = "";
$strMsg = "";
$strMode = $_POST["hidMode"];
$strSaveMode = $_POST["hidSaveMode"];

if ($_GET["mode"] == "FIND") {
	$strCode = replacesinglequote($_GET["code"]);
	$strMode = "FIND";
}

if ($strMode != "") {	
	if ($strMode != "FIND") {
		$strCode = replacesinglequote($_POST["hidCode"]);		
	}
	$strDesc = replacesinglequote($_POST["txtDesc"]);
	$strGLCode = replacesinglequote($_POST["txtGLCode"]);
	$strChargeType = replacesinglequote($_POST["cboChargeType"]);
	$uid = $sessUserID;
	$company_code = $sessCompanyCode;
	//echo $strSaveMode;
	switch ($strMode) {
		case "SAVE":
			//echo $strSaveMode;
			if ($strSaveMode != "EDIT") {
				$strCode = replacesinglequote($_POST["txtCode"]);
				$sqlquery="exec sp_m_Charges 'FIND','" . $strCode . "','" . $strDesc . "','" . $strGLCode . "','" . $strChargeType . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";					
				$process=odbc_exec($sqlconnect, $sqlquery);
				if (odbc_fetch_row($process)) {
						if (odbc_result($process,"x") == 1) 
							$strMsg = "Charge already exists!";
				}
			}
			//echo $sqlquery;
			if ($strMsg == "") {
				$sqlquery="exec sp_m_Charges 'SAVE','" . $strCode . "','" . $strDesc . "','" . $strGLCode . "','" . $strChargeType . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";
				$process=odbc_exec($sqlconnect, $sqlquery);
				$strMsg = "Record saved!";					
				$strCode = "";
				$strDesc = "";
				$strGLCode = "";
				$strChargeType = "";
				$strMode = "";
				$strSaveMode = "";
			}
			
			break;
		
		case "DELETE":
			$sqlquery="exec sp_m_Charges 'DELETE','" . $strCode . "','" . $strDesc . "','" . $strGLCode . "','" . $strChargeType . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";
			$process=odbc_exec($sqlconnect, $sqlquery);
			if (odbc_fetch_row($process)) {
				if (odbc_result($process,"x") == 1) 
					$strMsg = "Record cannot be deleted. Remove assignment first from Unit Charges and/or Tenant Charges module.";
				else
					$strMsg = "Record deleted!";					
					$strCode = "";
					$strDesc = "";
					$strGLCode = "";
					$strChargeType = "";
					$strMode = "";
					$strSaveMode = "";
			}
			break;
			
		case "RETRIEVE" || "FIND":
			$sqlquery="exec sp_m_Charges 'RETRIEVE','" . $strCode . "','" . $strDesc . "','" . $strGLCode . "','" . $strChargeType . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";
			$process=odbc_exec($sqlconnect, $sqlquery);
			if (odbc_fetch_row($process)) {
					$strCode = replacedoublequotes(odbc_result($process,"charge_code"));
					$strDesc = replacedoublequotes(odbc_result($process,"charge_desc"));
					$strGLCode = replacedoublequotes(odbc_result($process,"gl_code"));
					$strChargeType = replacedoublequotes(odbc_result($process,"charge_type"));
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
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color: #F3F5B4;">
<form name="frmCharges" id="frmCharges" method="post" action="charges.php?menu_id=<?php echo $menu_id;?>">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">CHARGES
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
			  <li class="li_nc"><a href="#" onClick="javascript:change_loc('charges_list.php?menu_id=<?php echo $menu_id;?>')">Find&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
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
										<td class="fieldname">CHARGE CODE :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<?php if ($strMode == "RETRIEVE") {?>
											<td><input type=text name="txtCode" id="txtCode" disabled class="values" size="10" maxlength="5" value="<?php echo $strCode;?>"></td>
										<?php } else {?>
											<td><input type=text name="txtCode" id="txtCode" class="values" size="10" maxlength="5" value="<?php echo $strCode;?>"></td>
										<?php } ?>
										<input type="hidden" id="hidCode" name="hidCode" value="<?php echo $strCode;?>">
									</tr>	
									<tr>
										<td class="fieldname">DESCRIPTION :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtDesc" id="txtDesc" class="values" size="70" maxlength="50" value="<?php echo $strDesc;?>"></td>
									</tr>														
									<tr>
										<td class="fieldname">GL CODE :</td>
										<td width="20"></td>
										<td><input type=text name="txtGLCode" id="txtGLCode" class="values" size="30" maxlength="50" value="<?php echo $strGLCode;?>"></td>
									</tr>
									<tr>
										<td class="fieldname">CHARGE TYPE :</td>
										<td width="20"></td>
										<td>
											<select name="cboChargeType" id="cboChargeType" class="values">
												<?php if ($strChargeType=="F") { ?>
													<option selected value="F">Fixed Rate</option>
													<option value="U">Based on Usage</option>
												<?php } elseif ($strChargeType=="U") { ?>
													<option  value="F">Fixed Rate</option>
													<option selected value="U">Based on Usage</option>
												<?php } else { ?>
													<option value="F">Fixed Rate</option>
													<option value="U">Based on Usage</option>
												<?php } ?>
											</select>
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
</form>
</body> 
</html>

<script type="text/javascript">
function hov(loc,cls) {   
	if(loc.className)   
	loc.className=cls;   
} 

function cmdSave_OnClick() {
	if (frmCharges.hidSaveMode.value != "EDIT") {
		if (frmCharges.txtCode.value == "") {
			alert("Charge Code is required")
			frmCharges.txtCode.focus()
			return false
		}		
	}
	if (frmCharges.txtDesc.value == "") {
			alert("Description is required")
			frmCharges.txtDesc.focus()
			return false
		}
	frmCharges.hidMode.value = "SAVE";
	frmCharges.submit();
}

function cmdRetrieve_OnClick() {
	frmCharges.hidCode.value = frmCharges.txtCode.value;
	frmCharges.hidMode.value = "RETRIEVE";
	frmCharges.submit();
}

function cmdCancel_OnClick() {
	parent.frames[2].location = "charges.php?menu_id=" + frmCharges.hidMenuID.value;
	return false;
}

function cmdDelete_OnClick() {
	if (frmCharges.txtCode.value == "") {
		alert("No record to be deleted!")
		frmCharges.txtCode.focus()
		return false
	}
	else {
		if (confirm("Are you sure you want to delete this record?")) {
			frmCharges.hidMode.value = "DELETE";
			frmCharges.submit();
		}
	}
}

function change_loc(pFile) {
	parent.frames[2].location = pFile;
	return false;
}
</script>
