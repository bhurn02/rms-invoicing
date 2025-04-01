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
		echo "<script> parent.frames(2).location = \"" . "accessnotallowed.htm" .  "\";</script>";
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
		case "PRINT":
			$cn = cn();
			$server = $cn[1];
			$db = $cn[2];
			$username = $cn[3];
			$password = $cn[4];
			$report_path= $cn[5];
			$pdf_path= $cn[6];
			$pdf_link= $cn[7];
			
			//- Variables - for your RPT and PDF 
			//echo "Print Report Test"; 
			$my_report = $report_path . "m_tenant_charges.rpt"; // 
			//echo $my_report;
			//die();
			//rpt source file 
			$pdf_file = "m_tenant_charges" . str_replace("/","",date("m/d/y/H/i/s", time())) . ".pdf";
			$my_pdf = $pdf_path . $pdf_file; // RPT export to pdf file 
			$my_pdf_open = $pdf_link . $pdf_file;
			//echo $pdf_file;
			//die();
			//-Create new COM object-depends on your Crystal Report version 
			//$ObjectFactory= new COM("CrystalRuntime.Application") or die ("Error on load"); // call COM port 
			$crapp= new COM("CrystalRuntime.Application") or die ("Error on load"); // call COM port 
			//$crapp = $ObjectFactory-> CreateObject("CrystalRuntime.Application"); // create an instance for Crystal 
			$creport = $crapp->OpenReport($my_report, 1); // call rpt report 
			
			// to refresh data before 
			
			//- Set database logon info - must have 
			$creport->Database->Tables(1)->SetLogOnInfo($server, $db,$username, $password); 
			//- field prompt or else report will hang - to get through 
			$creport->EnableParameterPrompting = 0; 
			
			//- DiscardSavedData - to refresh then read records 
			$creport->DiscardSavedData; 
			$creport->ReadRecords(); 
			
			//------ Pass formula fields --------
			$i = 1;
			$j = 1;
			$k = 1;
			$strCondition = "";
			$strSortBy = "";
			$strGroupBy = "";
			//echo $_POST["hidRowCtr"];
			//exit();
			while ($i <= $_POST["hidRowCtr"]) {		
				if (trim($_POST["txtCondition" . $i]) != "") {
					if (trim($strCondition) != "")		
						$strCondition = $strCondition . " and ";
					
					if ($_POST["cboCondition" . $i] == "Like")	
						$strCondition = $strCondition .  $_POST["hidCondition" . $i] . " " . $_POST["cboCondition" . $i] . " ''%" . replacesinglequote($_POST["txtCondition" . $i]) . "%''";					
					else
						$strCondition = $strCondition .  $_POST["hidCondition" . $i] . " " . $_POST["cboCondition" . $i] . " ''" . replacesinglequote($_POST["txtCondition" . $i]) . "''";											
				}
				$i++;
			}
			
			while ($j <= $_POST["hidRowCtr2"]) {			
				if (isset($_POST["chkSelSort" . strval($j)])) {	
					if (trim($strSortBy) != "")	{	
						$strSortBy = $strSortBy . " , ";
					}
						
					$strSortBy = $strSortBy .  $_POST["hidSortBy" . $j];					
				}
				$j++;				
			}
			
			while ($k <= $_POST["hidRowCtr3"]) {			
				if (isset($_POST["chkSelGroup" . strval($k)])) {						
					$strGroupBy = $_POST["hidGroupBy" . $k];					
				}
				$k++;				
			}
			
			//echo $strCondition . " " . $strSortBy . " " . $strGroupBy;
			//exit();
			
			$creport->ParameterFields(1)->AddCurrentValue ($strCondition);
			$creport->ParameterFields(2)->AddCurrentValue ($strSortBy);
			$creport->ParameterFields(3)->AddCurrentValue ($strGroupBy);
			//$creport->ParameterFields(2)->AddCurrentValue (2000);
			
			//export to PDF process 
			$creport->ExportOptions->DiskFileName=$my_pdf; //export to pdf 
			$creport->ExportOptions->PDFExportAllPages=true; 
			$creport->ExportOptions->DestinationType=1; // export to file 
			$creport->ExportOptions->FormatType=31; // PDF type 
			$creport->Export(false); 
			
			//------ Release the variables ------ 
			$creport = null; 
			$crapp = null; 
			//$ObjectFactory = null; 
			
			$strMode = "";
			echo "<script type=\"text/javascript\">window.open (\"" . $my_pdf_open . "\");</script>";
			break;
			
	}
}

$sqlqueryCharges="SELECT * FROM rg_table_fields where module_id=5 order by xorder";	
$processCharges=odbc_exec($sqlconnect, $sqlqueryCharges);
$processSort=odbc_exec($sqlconnect, $sqlqueryCharges);
$processGroup=odbc_exec($sqlconnect, $sqlqueryCharges);
//echo $strMode;

if ($strMsg != ""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 

?>
<html> 
<head> 
<title>TENANT CHARGES LIST REPORT</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
</head> 
<body style="margin:'0';background-color: #F3F5B4;">
<form name="frmUnitCharges" id="frmUnitCharges" method="post" action="report_tenant_charges_customized.php?menu_id=<?php echo $menu_id;?>">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">TENANT CHARGES LIST REPORT
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			 </a></li>				 
			  <li class="li_nc"><a href="#" onClick="javascript:cmdPrint_OnClick()">|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Print&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <li class="li_nc"><a href="#" onClick="javascript:window.close();">Close&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>			  
		</ul>
	</div>
	<table>
		<tr>
			<td width="10"></td>
			<td>
				<table>
					<tr valign="top">
						<td>					
							<table width="500" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
								<tr height="30">							
									<td width="31%" class="tablehdr" align="center">&nbsp;Set Filter To&nbsp;
									</td>
									<td  width="15%" class="tablehdr" align="center">&nbsp;Condition&nbsp;
									</td>
									<td  width="45%" class="tablehdr" align="center">&nbsp;Value&nbsp;
									</td>							
								</tr>
								<?php
								$ctr = 0;
								while(odbc_fetch_row($processCharges)) {
									$field_id = odbc_result($processCharges,"field_id"); 
									$field_name = odbc_result($processCharges,"field_name"); 
									$field_desc = odbc_result($processCharges,"field_desc"); 
									
									$ctr = $ctr+1;
									
									if ($ctr%2==1) 
										$rowColor = "98fb98";	
									else
										$rowColor = "ffffe0";			
								?>
								<tr id="editRow<?php echo "$ctr";?>" name="editRow<?php echo "$ctr";?>" style="cursor:hand" bgcolor="<?php echo "$rowColor" ?>">							
									<td width="31%" style="border:1px" class="values">&nbsp;<?php echo "$field_desc";?>&nbsp;
									<input type="hidden" id="hidCondition<?php echo "$ctr";?>" name="hidCondition<?php echo "$ctr";?>" value="<?php echo $field_name;?>">
									</td>
									<td width="15%" style="border:1px" class="values" align="center">
										<select id="cboCondition<?php echo "$ctr";?>" name="cboCondition<?php echo "$ctr";?>" class="values">
											<option value="Like">Like</option>
											<option value="=">=</option>									
											<option value="=">></option>									
											<option value="=">< </option>									
											<option value="=">>=</option>									
											<option value="="><=</option>									
										</select>
									</td>
									<td width="45%" style="border:1px" class="values" align="center">
										<input type="text" id="txtCondition<?php echo "$ctr";?>" name="txtCondition<?php echo "$ctr";?>" class="values" size="30">
									</td>							
								</tr>
								<?php } ?>										
							</table>
						</td>
						<td width="10">
						<td>					
						<td>					
							<table width="250" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
								<tr height="30">
									<td width="17%" class="tablehdr" align="center">&nbsp;Sel&nbsp;
									</td>
									<td width="83%" class="tablehdr" align="center">&nbsp;Sort By&nbsp;
									</td>													
								</tr>
								<?php
								$ctr2 = 0;
								while(odbc_fetch_row($processSort)) {
									$field_id = odbc_result($processSort,"field_id"); 
									$field_name = odbc_result($processSort,"field_name"); 
									$field_desc = odbc_result($processSort,"field_desc"); 
									
									$ctr2 = $ctr2+1;
									
									if ($ctr2%2==1) 
										$rowColor = "98fb98";	
									else
										$rowColor = "ffffe0";			
								?>
								<tr id="editRow<?php echo "$ctr2";?>" name="editRow<?php echo "$ctr2";?>" style="cursor:hand" bgcolor="<?php echo "$rowColor" ?>">
									<td width="17%" align="center" style="border:1px">
										<input type="checkbox" name="chkSelSort<?php echo "$ctr2";?>" id="chkSelSort<?php echo "$ctr2";?>">
									</td>
									<td width="83%" style="border:1px" class="values">&nbsp;<?php echo "$field_desc";?>&nbsp;
										<input type="hidden" id="hidSortBy<?php echo "$ctr2";?>" name="hidSortBy<?php echo "$ctr2";?>" value="<?php echo $field_name;?>">
									</td>												
								</tr>
								<?php } ?>
								<?php
										if ($ctr2%2==1) 
											$rowColor = "ffffe0";	
										else
											$rowColor = "98fb98";								
								?>						
							</table>
						</td>
						<td width="10">&nbsp;
						</td>
						<td>					
							<table width="250" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
								<tr height="30">
									<td width="18%" class="tablehdr" align="center">&nbsp;Sel&nbsp;
									</td>
									<td width="82%" class="tablehdr" align="center">&nbsp;Group By&nbsp;
									</td>													
								</tr>
								<?php
								$ctr3 = 0;
								while(odbc_fetch_row($processGroup)) {
									$field_id = odbc_result($processGroup,"field_id"); 
									$field_name = odbc_result($processGroup,"field_name"); 
									$field_desc = odbc_result($processGroup,"field_desc"); 
									
									$ctr3 = $ctr3+1;
									
									if ($ctr3%2==1) 
										$rowColor = "98fb98";	
									else
										$rowColor = "ffffe0";					
								?>
								<tr id="trGroup<?php echo "$ctr3";?>" name="trGroup<?php echo "$ctr3";?>" bgcolor="<?php echo "$rowColor" ?>">
									<td width="18%" align="center" style="border:1px;">
										<input type="checkbox" name="chkSelGroup<?php echo "$ctr3";?>" id="chkSelGroup<?php echo "$ctr3";?>" onClick="javascript:chkSelGroup_OnClick(<?php echo $ctr3;?>)">
									</td>
									<td width="82%" style="border:1px" class="values">&nbsp;<?php echo "$field_desc";?>&nbsp;
										<input type="hidden" id="hidGroupBy<?php echo "$ctr3";?>" name="hidGroupBy<?php echo "$ctr3";?>" value="<?php echo $field_name;?>">
									</td>												
								</tr>
								<?php } ?>			
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
	<input type="hidden" id="hidRowCtr2" name="hidRowCtr2" value=<?php echo $ctr2;?>>
	<input type="hidden" id="hidRowCtr3" name="hidRowCtr3" value=<?php echo $ctr3;?>>
	<input type="hidden" id="hidCurRow" name="hidCurRow">
	<input type="hidden" id="hidChargeCode" name="hidChargeCode">
	<input type="hidden" id="hidChargeAmount" name="hidChargeAmount">
	<input type="hidden" id="hidMenuID" name="hidMenuID" value=<?php echo $menu_id;?>>
</form>
</body> 
</html>

<script language="javascript" src="jsp/function.js"></script>
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

function chkSelGroup_OnClick(ctr) {
	var tmpCtr
	tmpCtr = frmUnitCharges.hidRowCtr3.value;
	obj2 = eval("frmUnitCharges.chkSelGroup" + ctr);		
	for (i=1;i<=tmpCtr;i++) {
		obj = eval("frmUnitCharges.chkSelGroup" + i);				
		if (obj2.checked == true) {
			if (i!=ctr)
				obj.disabled = true;
			else
				obj.disabled = false;
		}
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
		parent.frames(2).location = "unit_charges.php?menu_id=" + frmUnitCharges.hidMenuID.value;
		return false;
	}
}

function cmdPrint_OnClick() {
	frmUnitCharges.hidMode.value = "PRINT";
	frmUnitCharges.submit();
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

function cmdClose_OnClick() {
	window.close();
}

</script>

