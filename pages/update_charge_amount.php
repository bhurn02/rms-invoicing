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
$strChargeCode = "";
$intAll = 1;
$blnAll = "checked";
$disOld = "disabled";
$strMsg = "";

$sqlquerycbo="select * from m_real_property order by real_property_name";
$processcbo=odbc_exec($sqlconnect, $sqlquerycbo);
while (odbc_fetch_row($processcbo)) {
	$cborealproperty .= "<option value=\"" . trim(odbc_result($processcbo,"real_property_code")) . "\">" . trim(strtoupper(odbc_result($processcbo,"real_property_name"))) . "</option>";
}

$sqlquerycbo="select * from m_charges order by charge_desc";
$processcbo=odbc_exec($sqlconnect, $sqlquerycbo);
while (odbc_fetch_row($processcbo)) {
	$cbocharge .= "<option value=\"" . trim(odbc_result($processcbo,"charge_code")) . "\">" . trim(strtoupper(odbc_result($processcbo,"charge_desc"))) . "</option>";
}

if ($strMode != "") {	
	$strRealPropertyCode = $_POST["cboRealProperty"];
	$strChargeCode = $_POST["cboCharge"];
	
	if ($_POST["chkAll"] == "on")
		$intAll = 1;
	else
		$intAll = 0;
	
	$dblAmountFr = $_POST["hidOldAmount"];
	$dblAmountTo = $_POST["txtAmountTo"];

	//echo $strRealPropertyName;
	switch ($strMode) {
		case "PROCESS":						
			$sqlquery="exec sp_u_MassUpdateChargeAmount_Process '" . $strRealPropertyCode . "','" . $strChargeCode . "'," . $intAll . "," . $dblAmountFr . "," . $dblAmountTo . ",'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			//echo $sqlquery;
			$process=odbc_exec($sqlconnect, $sqlquery);					
			$strMsg = "Charge Amount Mass Update processed! Check Unit Charges and Owner/Tenant Charges modules.";
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
<title>CHARGE AMOUNT MASS UPDATE</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
<script type="text/javascript" src="library/datepickercontrol/datepickercontrol.js"></script>
<link type="text/css" rel="stylesheet" href="library/datepickercontrol/datepickercontrol_green.css">
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color: #F3F5B4;">
<form name="frmUpdateAmount" id="frmUpdateAmount" method="post">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">CHARGE AMOUNT MASS UPDATE
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			 </a></li>	
			  <li class="li_nc"><a href="#" onClick="javascript:cmdProcess_OnClick()">|&nbsp;&nbsp;&nbsp;Process&nbsp;&nbsp;&nbsp;|</a></li>			  			  
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
										<td class="fieldname" align="right">REAL PROPERTY :</td>
										<td width="10"></td>
									  	<td>
											<select name="cboRealProperty" id="cboRealProperty" class="values">
												<option value="">- ALL -</option>
												<?php echo $cborealproperty; ?>
											</select>
										</td>
									</tr>			
									<tr>
										<td class="fieldname" align="right">CHARGE :</td>
										<td width="10"></td>
									  	<td>
											<select name="cboCharge" id="cboCharge" class="values">
												<?php echo $cbocharge; ?>
											</select>
										</td>
									</tr>			
									<tr>
										<td class="fieldname" align="right">&nbsp;</td>
										<td width="10"></td>
									  	<td class="values"><input type=checkbox name="chkAll" id="chkAll" class="values" onClick="javascript:chkAll_OnClick()" <?php echo $blnAll;?> >ALL</td>
									</tr>			
									<tr>
										<td class="fieldname" align="right">ONLY WITH THIS OLD AMOUNT ($) :</td>
										<td width="10"></td>
										<td><input type=text name="txtAmountFr" id="txtAmountFr" class="values" style="text-align:right" <?php echo $disOld;?> size="15" maxlength="15" value="0.00"></td>
									</tr>	
									<tr>
										<td class="fieldname" align="right">TO NEW AMOUNT ($) :</td>
										<td width="10"></td>
										<td><input type=text name="txtAmountTo" id="txtAmountTo" class="values" style="text-align:right" size="15" maxlength="15"  value="0.00"></td>
									</tr>																																																										
									<tr>
										<td class="fieldname">&nbsp;</td>
										<td width="10">&nbsp;</td>
										<td class="values" style="color:#CC0000">Note: Unit Charges and Active Tenants with selected charge will be updated.</td>
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
	<input type="hidden" id="hidRowCtr" name="hidRowCtr" value=<?php echo $ctr;?>>
	<input type="hidden" id="hidCurRow" name="hidCurRow">
	<input type="hidden" id="hidRealPropertyName" name="hidRealPropertyName" value=<?php echo $strRealPropertyName;?>>
	<input type="hidden" id="hidDateUploaded" name="hidDateUploaded" value="<?php echo $date_uploaded ; ?>">
	<input type="hidden" id="hidMenuID" name="hidMenuID" value=<?php echo $menu_id;?>>
	<input type="hidden" id="hidOldAmount" name="hidOldAmount">
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
   var w = frmUpdateAmount.cboRealProperty.selectedIndex;
   frmUpdateAmount.hidRealPropertyName.value = frmUpdateAmount.cboRealProperty.options[w].text;
   cmdSearch_OnClick();
   }

function txtKeyword_onKeyUp(e) {
	if (e.keyCode==13) {
		cmdSearch_onClick();
	}
}

function chkAll_OnClick() {
	if (frmUpdateAmount.chkAll.checked == true) {
		frmUpdateAmount.txtAmountFr.disabled = true;
	}
	else {
		frmUpdateAmount.txtAmountFr.disabled = false;
	}
}

function cmdProcess_OnClick() {
	if (frmUpdateAmount.chkAll.checked == false) {
		if (isNaN(frmUpdateAmount.txtAmountFr.value)) {
			alert("Invalid numeric value on Old Amount")
			frmUpdateAmount.txtAmountFr.focus()
			return false
		}
		frmUpdateAmount.hidOldAmount.value = frmUpdateAmount.txtAmountFr.value
	}
	else {
		frmUpdateAmount.hidOldAmount.value = 0;
	}
	
	if (isNaN(frmUpdateAmount.txtAmountTo.value)) {
		alert("Invalid numeric value on New Amount")
		frmUpdateAmount.txtAmountTo.focus()
		return false
	}
	
	if(confirm("Are you sure you want to do Mass Update on this Charge? There is no UNDO for this process.")) {
		frmUpdateAmount.hidMode.value = "PROCESS";
		frmUpdateAmount.submit();
	}
}

function cmdClose_OnClick() {
	parent.frames[2].location = "blank.htm";
	return false;
}

</script>