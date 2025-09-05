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

$company_code = $sessCompanyCode;
$intGroupCode = 0;
$strDescription = "";
$strMode = $_POST["hidMode"];
$strSaveMode = $_POST["hidSaveMode"];

if ($_GET["mode"] == "FIND") {
	$intGroupCode = replacesinglequote($_GET["group_code"]);
	$strMode = "FIND";
}

if ($strMode != "") {
	$strGroupCode = replacesinglequote($_POST["txtGroupCode"]);
	if ($strMode != "FIND") {
		$intGroupCode = replacesinglequote($_POST["hidGroupCode"]);
	}
	$strDescription = replacesinglequote($_POST["txtDescription"]);
	$uid = $sessUserID;
	
	//echo $strSaveMode;
	switch ($strMode) {
		case "SAVE":			
			$sqlquery="exec sp_s_User_Group_Module 'DELETE'," . $intGroupCode . ",0,'" . $strGroupCode . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			while ($i <= $_POST["hidRowCtr"]) {
				if (isset($_POST["chkSelect" . strval($i)])) {
					$sqlquery="exec sp_s_User_Group_Module 'SAVE'," . $intGroupCode . "," . $_POST["hidModuleID" . strval($i)] . ",'','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					//echo $sqlquery;
					$process=odbc_exec($sqlconnect, $sqlquery);					
				}
				$i++;
			}
			$strMsg = "Records saved!";
			$intGroupCode = 0;
			$strDescription = "";
			$strMode = "";
			$strSaveMode = "";
			break;
			
		case "RETRIEVE":
			$sqlquery="exec sp_s_User_Group_Module 'RETRIEVE_GROUP',0,0,'" . $strGroupCode . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			//echo $sqlquery;
			if (odbc_fetch_row($process)) {
					$intGroupCode = odbc_result($process,"group_code");
					$strDescription = odbc_result($process,"group_desc");					
					$strMode = "RETRIEVE";
					$strSaveMode = "EDIT";
					
					$sqlqueryModule="exec sp_s_User_Group_Module 'RETRIEVE'," . $intGroupCode . ",0,'" . $strGroupCode . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					$processModule=odbc_exec($sqlconnect, $sqlqueryModule);
			}
			else {
				$strMsg = "No record found!";
				$strMode = "";
				$strSaveMode = "";
			}
			break;
		case "FIND":
			$sqlquery="exec sp_s_User_Group_Module 'FIND_GROUP'," . $intGroupCode . ",0,'','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			//echo $sqlquery;
			if (odbc_fetch_row($process)) {
					$intGroupCode = odbc_result($process,"group_code");
					$strDescription = odbc_result($process,"group_desc");					
					$strMode = "RETRIEVE";
					$strSaveMode = "EDIT";
					
					$sqlqueryModule="exec sp_s_User_Group_Module 'FIND'," . $intGroupCode . ",0,'','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					$processModule=odbc_exec($sqlconnect, $sqlqueryModule);
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
<title>USER GROUP MODULES</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
</head> 
<body style="margin:'0';background-color: #F3F5B4;">
<form name="frmUserGroupModule" id="frmUserGroupModule" method="post" action="user_group_modules.php?menu_id=<?php echo $menu_id;?>">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a href="#" onClick="javascript:change_loc('security_menu.php?menu_id=<?php echo $menu_id;?>')"><u>SECURITY</u></a></li>	
			  <li class="li_nc"><a name="MODULE NAME">>>&nbsp;&nbsp;&nbsp;&nbsp;USER GROUP MODULES
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;			 
			 </a></li>				  
			  <?php if ($strMode != "RETRIEVE") { ?>
				    <li class="li_nc"><a href="#" onClick="javascript:cmdRetrieve_OnClick()">|&nbsp;&nbsp;&nbsp;Retrieve&nbsp;&nbsp;&nbsp;|</a></li>			  
			  		<li><a name="Save" style="color: #666666">Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } else { ?>			
				    <li><a name="Retrieve" style="color: #666666">|&nbsp;&nbsp;&nbsp;Retrieve&nbsp;&nbsp;&nbsp;|</a></li>
					<li class="li_nc"><a href="#" onClick="javascript:cmdSave_OnClick()">Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } ?>	
			  <li class="li_nc"><a href="#" onClick="javascript:change_loc('user_group_modules_list.php?menu_id=<?php echo $menu_id;?>')">Find&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  			  
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
										<td class="fieldname">GROUP CODE :<em class="requiredRed">*</em></td>
										<td width="20">&nbsp;</td>		
										<?php if ($strMode == "RETRIEVE" || $strMenu == "DETAIL") {?>								
											<td>
												<input type=text name="txtGroupCode" id="txtGroupCode" disabled class="values" size="20" value="<?php echo $intGroupCode;?>">									    
											</td>
										<?php } else {?>								
											<td>
												<input type=text name="txtGroupCode" id="txtGroupCode" class="values" size="20" value="<?php echo $intGroupCode;?>">									    
											</td>
										<?php }?>	
									   <input type="hidden" id="hidGroupCode" name="hidGroupCode" value="<?php echo $intGroupCode;?>">
									</tr>	
									<tr>
										<td class="fieldname">DESCRIPTION :</td>
										<td width="20">&nbsp;</td>			
										<td><input type=text name="txtGroupDesc" id="txtGroupDesc" disabled class="values" size="60" value="<?php echo $strDescription;?>"></td>										
									</tr>									
								</table>
							</td>
						</tr>
					</table>
					<p></p>
					<table width="400" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
						<tr height="30">
							<td width="9%" class="tablehdr" align="center">&nbsp;Sel&nbsp;
							</td>
							<td width="91%" class="tablehdr" align="center">&nbsp;Module&nbsp;
							</td>							
						</tr>
						<?php
						$ctr = 0;
						while(odbc_fetch_row($processModule)) {
							$module_id = odbc_result($processModule,"module_id"); 
							$module_name = odbc_result($processModule,"module_name"); 
							$is_inc = odbc_result($processModule,"is_inc"); 
							
							$ctr = $ctr+1;
							
							if ($ctr%2==1) 
								$rowColor = "98fb98";	
							else
								$rowColor = "ffffe0";			
						?>
						<tr id="editRow<?php echo "$ctr";?>" name="editRow<?php echo "$ctr";?>" style="cursor:hand" bgcolor="<?php echo "$rowColor" ?>" onDblClick="javascript:editMode(<?php echo "$ctr";?>);">
							<td width="9%" align="center" style="border:1px">
								<?php if ($is_inc == 1) {?>
									<input type="checkbox" name="chkSelect<?php echo "$ctr";?>" id="chkSelect<?php echo "$ctr";?>" checked>
								<?php } else {?>
									<input type="checkbox" name="chkSelect<?php echo "$ctr";?>" id="chkSelect<?php echo "$ctr";?>">
								<?php } ?>
								<input type="hidden" id="hidModuleID<?php echo "$ctr";?>" name="hidModuleID<?php echo "$ctr";?>" value="<?php echo $module_id;?>">								
							</td>
							<td width="91%" style="border:1px" class="values">
								&nbsp;<?php echo "$module_name";?>																
							</td>								
						</tr>
						<?php } ?>						
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
	<input type="hidden" id="hidTenantCode" name="hidTenantCode">
	<input type="hidden" id="hidChargeCode" name="hidChargeCode">
	<input type="hidden" id="hidInvoiceDetailID" name="hidInvoiceDetailID">
	<input type="hidden" id="hidInvoiceDetailReadingID" name="hidInvoiceDetailReadingID">
	<input type="hidden" id="hidReadingID" name="hidReadingID">
	<input type="hidden" id="hidChargeAmount" name="hidChargeAmount">
	<input type="hidden" id="hidMenuID" name="hidMenuID" value=<?php echo $menu_id;?>>
</form>
</body> 
</html>

<script type="text/javascript">
function editMode(ctr) {
	if (frmUserGroupModule.hidSaveMode.value != "EDIT_CHARGE") {
		frmUserGroupModule.hidCurRow.value = ctr;
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
		frmUserGroupModule.chkSelectAll.disabled = true;
		frmUserGroupModule.chkSelectAll.checked = false;
		frmUserGroupModule.cboAddCharge.disabled = true;
		frmUserGroupModule.txtAddChargeAmount.disabled = true;
		frmUserGroupModule.hidSaveMode.value = "EDIT_CHARGE";
	}
}

function enabledisablechkboxes(pVal) {
	var ctr
	ctr = frmUserGroupModule.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmUserGroupModule.chkSelect" + i);		
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

function cmdTenantSearch_onClick(ctr) {
	window.open ("invoice_detail_search_tenant.php?invoice_no=" + frmUserGroupModule.hidInvoiceNo.value +"&ctr="+ctr,"displayWindow","type=fullwindow,titlebar=no,scrollbars=yes");
	return false;
}


function cmdFillupRemarks_OnClick(ctr) {
	frmUserGroupModule.hidCurRow.value = ctr;
	obj = eval("frmUserGroupModule.hidEditChargeCode" + ctr);
	frmUserGroupModule.hidChargeCode.value = obj.value;
	obj = eval("frmUserGroupModule.hidModuleID" + ctr);
	frmUserGroupModule.hidInvoiceDetailID.value = obj.value;
	obj = eval("frmUserGroupModule.hidEditInvoiceDetailReadingID" + ctr);
	frmUserGroupModule.hidInvoiceDetailReadingID.value = obj.value;
	obj = eval("frmUserGroupModule.hidEditReadingID" + ctr);
	frmUserGroupModule.hidReadingID.value = obj.value;
	parent.frames[2].location = "invoice_detail_reading.php?invoice_no=" + frmUserGroupModule.hidInvoiceNo.value + "&charge_code=" + frmUserGroupModule.hidChargeCode.value + "&invoice_detail_id=" + frmUserGroupModule.hidInvoiceDetailID.value+ "&invoice_detail_reading_id=" + frmUserGroupModule.hidInvoiceDetailReadingID.value+ "&reading_id=" + frmUserGroupModule.hidReadingID.value;
	return false;
}

function cmdSave_OnClick() {
	if (frmUserGroupModule.hidGroupCode.value == "") {
		alert("Select User Group first!")
		return false
	}
	frmUserGroupModule.hidMode.value = "SAVE";
	frmUserGroupModule.submit();
}

function cmdRetrieve_OnClick() {
	frmUserGroupModule.hidMode.value = "RETRIEVE";
	frmUserGroupModule.submit();
}

function cmdPrint_OnClick() {
	frmUserGroupModule.hidInvoiceNo.value = frmUserGroupModule.txtGroupCode.value;
	frmUserGroupModule.hidMode.value = "PRINT";
	frmUserGroupModule.submit();
}

function cmdCancel_OnClick() {
	ctr = frmUserGroupModule.hidCurRow.value;
	//alert(frmUserGroupModule.hidSaveMode.value)
	if (frmUserGroupModule.hidSaveMode.value == "EDIT_CHARGE") {
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
		
		frmUserGroupModule.cboAddCharge.disabled = false;
		frmUserGroupModule.txtAddChargeAmount.disabled = false;
		frmUserGroupModule.chkSelectAll.disabled = false;
		frmUserGroupModule.chkSelectAll.checked = false;
		frmUserGroupModule.hidSaveMode.value = "";
		enabledisablechkboxes(1);
		return false;
	}
	else {
		parent.frames[2].location = "user_group_modules.php?menu_id=" + frmUserGroupModule.hidMenuID.value;
		return false;
	}
}

function cmdDelete_OnClick() {
	var j
	j=0
	totalctr = frmUserGroupModule.hidRowCtr.value;
	for (i=1;i<=totalctr;i++) {
		obj = eval("frmUserGroupModule.chkSelect" + i);
		if (obj.checked == true) {
			j++;
		}
	}
	if (j > 0) {
		if (confirm("Are you sure you want to delete this record/s?")) {
			frmUserGroupModule.hidMode.value = "DELETE";
			frmUserGroupModule.submit();
		}
	}
	else {
		alert("Deleting is not allowed this time");
	}
}

function chkSelectAll_OnClick() {
	var ctr
	ctr = frmUserGroupModule.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmUserGroupModule.chkSelect" + i);
		if (frmUserGroupModule.chkSelectAll.checked == true) {
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
</script>

