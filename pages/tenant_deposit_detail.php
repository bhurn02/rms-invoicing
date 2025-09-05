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
$uid = $sessUserID;
$company_code = $sessCompanyCode;
$strIPAddr = $_SERVER["REMOTE_ADDR"];
$menu_id = $_GET["menu_id"];

$strMode = trim($_POST["hidMode"]);
$strSaveMode = $_POST["hidSaveMode"];
$strORNo = "";
$dblTotalORAmount = "0.00";
$intDetailID = 0;
$intInvoiceDetailReadingID = 0;
$intReadingID = 0;
$strClientCode = "";
$strClientName = "";
$strStatus = "";
$strStatusDesc = "";
$strTenantCode = "";
$strTenantName = "";
$strChargeCode = "";		
$strChargeDesc = "";		
$strChargeType = "";		
$dblAmount = 0;	
$dblTotalChargeAmount = 0;	
$strRemarks = "";
$strMsg = "";
$strMenu = "";
//echo $strMode;
//echo $sqlqueryDetail;
$sqlquerycbo="select * from m_charges order by charge_desc";
$processcbo=odbc_exec($sqlconnect, $sqlquerycbo);
while (odbc_fetch_row($processcbo)) {
	$cbocharge .= "<option value=\"" . trim(odbc_result($processcbo,"charge_code")) . "\">" . trim(strtoupper(odbc_result($processcbo,"charge_desc"))) . "</option>";
}

if ($_GET["mode"] == "FIND") {
	$strMenu = $_GET["menu"];
	$strORNo = $_GET["or_no"];
	$strMode = "FIND";
}

if ($strMode != "") {
	if ($strMode != "FIND") {
		if ($strMode != "RETRIEVE") {
			$strORNo = replacesinglequote($_POST["hidORNo"]);
		}		
		else {
			$strORNo = replacesinglequote($_POST["txtORNo"]);
		}		
	}
	//echo $strSaveMode;
	switch ($strMode) {
		case "SAVE":
			$intDetailID = $_POST["hidDetailID"];
			$strORNo = replacesinglequote($_POST["hidORNo"]);
			$strRealPropertyCode = replacesinglequote($_POST["hidRealPropertyCode"]);		
			$strBuildingCode = replacesinglequote($_POST["hidBuildingCode"]);		
			$strUnitNo = replacesinglequote($_POST["hidUnitNo"]);		
			$dblAmount = $_POST["hidAmount"];		
						
			$sqlquery="exec sp_t_Security_Deposit_Detail 'SAVE','" . $strORNo . "'," . $intDetailID . ",'" . $strRealPropertyCode . "','" . $strBuildingCode . "','" . $strUnitNo . "'," . $dblAmount . ",'','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			$strMsg = "Record saved!";		
			$strMode = "RETRIEVE";
			$strSaveMode = "EDIT";
			break;
			//echo $strMode;
		case "DELETE":
			$i = 1;
			$j = 0;
			$k = 0;
			$strORNo = replacesinglequote($_POST["hidORNo"]);
			while ($i <= $_POST["hidRowCtr"]) {
				if (isset($_POST["chkDelete" . strval($i)])) {					
					$intDetailID = $_POST["hidEditDetailID" . strval($i)];		
					$dblAmount = 0;		
					$sqlquery="exec sp_t_Security_Deposit_Detail 'DELETE','" . $strORNo . "'," . $intDetailID . ",'" . $strRealPropertyCode . "','" . $strBuildingCode . "','" . $strUnitNo . "'," . $dblAmount . ",'','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
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
			if ($j > 0 && $k > 0) {
				$strMsg = "Some records were not deleted because it had been posted.";
			}
			elseif ($j == 0 && $k > 0) {
				$strMsg = "Record/s deleted!";
			}
			elseif ($j > 0 && $k == 0) {
				$strMsg = "No record deleted because it had been posted!";
			}
			else
				$strMsg = "No record deleted!";
				
			$strMode = "RETRIEVE";
			$strSaveMode = "EDIT";
			
			break;
			
		case "PRINT":
			$cn = cn();
			$server = $cn[1];
			$db = $cn[2];
			$username = $cn[3];
			$password = $cn[4];
			$report_path= $cn[5];
			$pdf_path= $cn[6];
			
			//- Variables - for your RPT and PDF 
			//echo "Print Report Test"; 
			$my_report = $report_path . "t_or_security_deposit.rpt"; // 
			//echo $my_report;
			//die();
			//rpt source file 
			$pdf_file = "t_or_security_deposit" . str_replace("/","",date("m/d/y/H/i/s", time())) . ".pdf";
			$my_pdf = $pdf_path . $pdf_file; // RPT export to pdf file 
			$my_pdf_open = "reports/" . $pdf_file;
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
			//$creport->FormulaFields->Item(1)->Text = ("'invoice_no'");
			$creport->ParameterFields(1)->AddCurrentValue ($_POST["hidORNo"]);
			$creport->ParameterFields(2)->AddCurrentValue ("");
			$creport->ParameterFields(3)->AddCurrentValue ("");
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
			
			echo "<script type=\"text/javascript\">window.open (\"" . $my_pdf_open . "\");</script>";
			$strMode="RETRIEVE";
			//end print
			break;
	}
			
	if ($strMode=="RETRIEVE" || $strMode== "FIND") {
		$sqlquery="exec sp_t_Security_Deposit_Detail 'RETRIEVE_HDR','" . $strORNo . "',0,'','','',0,'','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
		//echo $sqlquery;
		$process=odbc_exec($sqlconnect, $sqlquery);
		if (odbc_fetch_row($process)) {
				$strORNo = replacedoublequotes(odbc_result($process,"or_no"));
				$strClientCode = replacedoublequotes(odbc_result($process,"client_code"));
				$strClientName = replacedoublequotes(odbc_result($process,"client_name"));
				$dblTotalORAmount = odbc_result($process,"total_or_amount");
				$strStatus = replacedoublequotes(odbc_result($process,"status"));
				$strStatusDesc = replacedoublequotes(odbc_result($process,"status_desc"));
				$strMode = "RETRIEVE";
				$strSaveMode = "EDIT";
				
				$sqlqueryDetail="exec sp_t_Security_Deposit_Detail 'RETRIEVE_DTL','" . $strORNo . "',0,'','','',0,'','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
				$processDetail=odbc_exec($sqlconnect, $sqlqueryDetail);
				//echo $sqlqueryDetail;
		}
		else {
			$strMode = "";
		}
	}
}
else {
	$strCode = "";
	$strName = "";
}
//echo $strMode . "a";
if ($strMsg != ""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 

?>
<html> 
<head> 
<title>TENANT SECURITY DEPOSIT - DETAIL</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
</head> 
<body style="margin:'0';background-color: #F3F5B4;">
<form name="frmTenantDepositDtl" id="frmTenantDepositDtl" method="post" action="tenant_deposit_detail.php?menu_id=<?php echo $menu_id;?>">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">TENANT SECURITY DEPOSIT - DETAIL
			  &nbsp;&nbsp;&nbsp;&nbsp;
			 </a></li>				  
			  <?php if ($strMode != "RETRIEVE") { ?>
				    <li class="li_nc"><a href="#" onClick="javascript:cmdRetrieve_OnClick()">|&nbsp;&nbsp;&nbsp;Retrieve&nbsp;&nbsp;&nbsp;|</a></li>			  
			  		<li><a name="Save" style="color: #666666">Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
					<li><a name="Delete" style="color: #666666">Delete&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } else { ?>			
				    <li><a name="Retrieve" style="color: #666666">|&nbsp;&nbsp;&nbsp;Retrieve&nbsp;&nbsp;&nbsp;|</a></li>
					<?php if ($strStatus != "") { ?>
						<li><a name="Save" style="color: #666666">Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
						<li><a name="Delete" style="color: #666666">Delete&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
					<?php } else { ?>		
						<li class="li_nc"><a href="#" onClick="javascript:cmdSave_OnClick()">Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
						<li class="li_nc"><a href="#" onClick="javascript:cmdDelete_OnClick()">Delete&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
					<?php } ?>	
			  <?php } ?>	
			  <li class="li_nc"><a href="#" onClick="javascript:change_loc('tenant_deposit_detail_list.php?menu_id=<?php echo $menu_id;?>')">Find&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  
			  <?php if ($strMode != "RETRIEVE") { ?>
			  		<li><a name="Print" style="color: #666666">Print&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } else { ?>	
			  		<li class="li_nc"><a href="#" onClick="javascript:cmdPrint_OnClick()">Print&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>			
			  <?php } ?>	
			  
			  <li class="li_nc"><a href="#" onClick="javascript:cmdCancel_OnClick()">Cancel&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>			  
		  	  <li class="li_nc"><a href="#" onClick="javascript:change_loc('tenant_deposit.php?menu_id=<?php echo $menu_id;?>&mode=FIND&or_no=<?php echo $strORNo; ?>')">Back&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
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
										<td class="fieldname">OR NO. :<em class="requiredRed">*</em></td>
										<td width="20">&nbsp;</td>		
										<?php if ($strMode == "RETRIEVE" || $strMenu == "DETAIL") {?>								
											<td>
												<input type=text name="txtORNo" id="txtORNo" disabled class="values" size="20" value="<?php echo $strORNo;?>">									    
											</td>
										<?php } else {?>								
											<td>
												<input type=text name="txtORNo" id="txtORNo" class="values" size="20" value="<?php echo $strORNo;?>">									    
											</td>
										<?php }?>	
									   <input type="hidden" id="hidORNo" name="hidORNo" value="<?php echo $strORNo;?>">
									</tr>	
									<tr>
										<td class="fieldname">CLIENT :<em class="requiredRed">*</em></td>
										<td width="20">&nbsp;</td>			
										<td><input type=text name="txtClientName" id="txtClientName" disabled class="values" size="60" value="<?php echo $strClientName;?>"></td>										
										<input type="hidden" id="hidClientCode" name="hidClientCode" value="<?php echo $strClientCode;?>">
									</tr>
									<?php if ($strStatus != "") { ?> 
										<tr>		
											<td class="fieldname">STATUS :</td>
											<td width="20">&nbsp;</td>	
											<td class="values" style="color:red; font-weight:bold"><?php echo $strStatusDesc;?></td>
										</tr>																		
									<?php } ?> 	
								</table>
							</td>
						</tr>
					</table>
					<p></p>
					<table width="550" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
						<tr height="30">
							<td width="7%" class="tablehdr" align="center">&nbsp;Del&nbsp;
							</td>
							<td width="74%" class="tablehdr" align="center">&nbsp;Real Property / Building Code / Unit No.<em class="requiredYellow">*</em>&nbsp;
							</td>
							<td  width="19%" class="tablehdr" align="center">&nbsp;Amount<em class="requiredYellow">*</em>&nbsp;
							</td>							
						</tr>
						<?php
						$ctr = 0;
						while(odbc_fetch_row($processDetail)) {
							$detail_id = odbc_result($processDetail,"detail_id"); 
							$real_property_code = odbc_result($processDetail,"real_property_code"); 
							$real_property_name = odbc_result($processDetail,"real_property_name"); 
							$building_code = odbc_result($processDetail,"building_code"); 
							$unit_no = odbc_result($processDetail,"unit_no"); 
							$rbu_name = $real_property_name . " / " . $building_code . " / " . $unit_no;
							
							if (is_null(odbc_result($processDetail,"amount")))
								$amount = "0.00";
							else
								$amount = odbc_result($processDetail,"amount"); 
								
							$ctr = $ctr+1;
							
							if ($ctr%2==1) 
								$rowColor = "98fb98";	
							else
								$rowColor = "ffffe0";			
						?>
						<tr id="editRow<?php echo "$ctr";?>" name="editRow<?php echo "$ctr";?>" style="cursor:hand" bgcolor="<?php echo "$rowColor" ?>" onDblClick="javascript:editMode(<?php echo "$ctr";?>);">
							<td width="7%" align="center" style="border:1px">
								<input type="checkbox" name="chkDelete<?php echo "$ctr";?>" id="chkDelete<?php echo "$ctr";?>">
								<input type="hidden" id="hidEditDetailID<?php echo "$ctr";?>" name="hidEditDetailID<?php echo "$ctr";?>" value="<?php echo $detail_id;?>">								
							</td>
							<td width="74%" style="border:1px" class="values">
								&nbsp;<?php echo "$rbu_name ";?>								
								<input type="hidden" id="hidEditRealPropertyCode<?php echo "$ctr";?>" name="hidEditRealPropertyCode<?php echo "$ctr";?>" value="<?php echo $real_property_code;?>">
								<input type="hidden" id="hidEditBuildingCode<?php echo "$ctr";?>" name="hidEditBuildingCode<?php echo "$ctr";?>" value="<?php echo $building_code;?>">
								<input type="hidden" id="hidEditUnitNo<?php echo "$ctr";?>" name="hidEditUnitNo<?php echo "$ctr";?>" value="<?php echo $unit_no;?>">
							</td>
							
							<td width="19%" style="border:1px" class="values" align="right">
								<span id="spAmount<?php echo "$ctr";?>" name="spAmount<?php echo "$ctr";?>" style="cursor:hand;visibility:'';display:''"><?php echo "$amount";?>&nbsp;</span>
								<span id="spEditAmount<?php echo "$ctr";?>" name="spEditAmount<?php echo "$ctr";?>" style="visibility:hidden;display:none">
									<input type=text name="txtEditAmount<?php echo "$ctr";?>" id="txtEditAmount<?php echo "$ctr";?>" class="values" style="text-align:right" size="10" value="<?php echo "$amount";?>">
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
							<td width="7%" align="center" style="border:1px" >&nbsp;
							</td>
							<td width="74%" style="border:1px" class="values">&nbsp;<b>TOTAL AMOUNT</b>
							</td>
							<td width="19%" style="border:1px" class="values" align="right"><b>$<?php echo "$dblTotalORAmount"; ?></b>&nbsp;						
							</td>							
							
						</tr>
						<?php
								if (($ctr + 1)%2==1) 
									$rowColor = "ffffe0";	
								else
									$rowColor = "98fb98";								
						?>
						<tr id="addRow" name="addRow" bgcolor="<?php echo "$rowColor"; ?>">
							<td width="7%" align="center" style="border:1px" >&nbsp;
							</td>
							<td width="74%" style="border:1px" class="values">&nbsp;
								<input type=text name="txtAddDetail" id="txtAddDetail" disabled class="values" size="40" value="">								
								
								<span id="spAddDetail" name="spAddDetail" style="cursor:hand;visibility:'';display:''">
									<img id="cmdDetailAdd" name="cmdDetailAdd" onClick="javascript:cmdDetailAdd_onClick(0);" src="images/icon_search.gif" style="cursor:hand" alt="Unit Lookup">
								</span>
								<span id="spAddDetailDis" name="spAddDetailDis" style="cursor:none;visibility:hidden;display:none">
									<img id="cmdDetailAdd" name="cmdDetailAdd" src="images/icon_search_disabled.gif">
								</span>
								<input type="hidden" id="hidAddRealPropertyCode" name="hidAddRealPropertyCode">
								<input type="hidden" id="hidAddBuildingCode" name="hidAddBuildingCode">
								<input type="hidden" id="hidAddUnitNo" name="hidAddUnitNo">
						  </td>
							<td width="19%" style="border:1px" class="values" align="right">
								<input type=text name="txtAddAmount" id="txtAddAmount" style="text-align:right" class="values" size="10" value="0.00">&nbsp;
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
	<input type="hidden" id="hidDetailID" name="hidDetailID">
	<input type="hidden" id="hidRealPropertyCode" name="hidRealPropertyCode">
	<input type="hidden" id="hidBuildingCode" name="hidBuildingCode">
	<input type="hidden" id="hidUnitNo" name="hidUnitNo">
	<input type="hidden" id="hidAmount" name="hidAmount">
	<input type="hidden" id="hidMenuID" name="hidMenuID" value=<?php echo $menu_id;?>>
</form>
</body> 
</html>

<script type="text/javascript">

function trim(stringToTrim) {
	return stringToTrim.replace(/^\s+|\s+$/g,"");
}

function editMode(ctr) {
	if (frmTenantDepositDtl.hidSaveMode.value != "EDIT_DETAIL") {
		frmTenantDepositDtl.hidCurRow.value = ctr;
				
		obj = eval("spAmount" + ctr);
		obj.style.visibility = "hidden"
		obj.style.display = "none"
		
		obj = eval("spEditAmount" + ctr);
		obj.style.visibility = "visible"
		obj.style.display = ""
		
		enabledisablechkboxes(0);
		frmTenantDepositDtl.chkSelectAll.disabled = true;
		frmTenantDepositDtl.chkSelectAll.checked = false;	
		
		obj = eval("spAddDetail");
		obj.style.visibility = "hidden"
		obj.style.display = "none"
		
		obj = eval("spAddDetailDis");
		obj.style.visibility = "visible"
		obj.style.display = ""
			
		frmTenantDepositDtl.txtAddAmount.disabled = true;
		frmTenantDepositDtl.hidSaveMode.value = "EDIT_DETAIL";
	}
}

function enabledisablechkboxes(pVal) {
	var ctr
	ctr = frmTenantDepositDtl.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmTenantDepositDtl.chkDelete" + i);		
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

function cmdDetailAdd_onClick(ctr) {
	frmTenantDepositDtl.hidSaveMode.value = "ADD_DETAIL";
	window.open ("tenant_deposit_unit_search.php?menu_id=" + frmTenantDepositDtl.hidMenuID.value + "&or_no=" + frmTenantDepositDtl.hidORNo.value +"&ctr="+ctr,"displayWindow","type=fullwindow,titlebar=no,scrollbars=yes");
	return false;
}


function cmdFillupRemarks_OnClick(ctr) {
	frmTenantDepositDtl.hidCurRow.value = ctr;
	obj = eval("frmTenantDepositDtl.hidEditChargeCode" + ctr);
	frmTenantDepositDtl.hidChargeCode.value = obj.value;
	obj = eval("frmTenantDepositDtl.hidEditDetailID" + ctr);
	frmTenantDepositDtl.hidDetailID.value = obj.value;
	obj = eval("frmTenantDepositDtl.hidEditInvoiceDetailReadingID" + ctr);
	frmTenantDepositDtl.hidInvoiceDetailReadingID.value = obj.value;
	obj = eval("frmTenantDepositDtl.hidEditReadingID" + ctr);
	frmTenantDepositDtl.hidReadingID.value = obj.value;
	parent.frames[2].location = "invoice_detail_reading.php?menu_id=" + frmTenantDepositDtl.hidMenuID.value + "&invoice_no=" + frmTenantDepositDtl.hidORNo.value + "&charge_code=" + frmTenantDepositDtl.hidChargeCode.value + "&detail_id=" + frmTenantDepositDtl.hidDetailID.value+ "&invoice_detail_reading_id=" + frmTenantDepositDtl.hidInvoiceDetailReadingID.value+ "&reading_id=" + frmTenantDepositDtl.hidReadingID.value;
	return false;
}

function cmdSave_OnClick() {
	if (frmTenantDepositDtl.hidORNo.value == "") {
		alert("Select OR first!")
		return false
	}
	
	if (frmTenantDepositDtl.hidSaveMode.value == "EDIT_DETAIL") {
		ctr = frmTenantDepositDtl.hidCurRow.value;

		obj = eval("frmTenantDepositDtl.hidEditDetailID" + ctr);
		frmTenantDepositDtl.hidDetailID.value = obj.value;
		
		obj = eval("frmTenantDepositDtl.txtEditAmount" + ctr);
		if (isNaN(obj.value) && obj.value != "") {
			alert("Invalid numeric value");
			obj.focus();
			return false;
		}
		obj = eval("frmTenantDepositDtl.txtEditAmount" + ctr);
		if (obj.value == "" || obj.value == 0) {
			alert("Charge Amount is required")
			obj.focus()
			return false
		}
		frmTenantDepositDtl.hidAmount.value = obj.value;
		frmTenantDepositDtl.hidSaveMode.value = "EDIT_DETAIL";
	}
	else {
		if (trim(frmTenantDepositDtl.hidAddRealPropertyCode.value) == "") {
			alert("Unit is required")
			frmTenantDepositDtl.cmdDetailAdd.focus()
			return false
		}		
		if (isNaN(frmTenantDepositDtl.txtAddAmount.value) && frmTenantDepositDtl.txtAddAmount.value != "") {
			alert("Invalid numeric value")
			frmTenantDepositDtl.txtAddAmount.focus()
			return false
		}
		if (frmTenantDepositDtl.txtAddAmount.value=="" || frmTenantDepositDtl.txtAddAmount.value == 0) {
			alert("Amount is required")
			frmTenantDepositDtl.txtAddAmount.focus()
			return false
		}
		frmTenantDepositDtl.hidRealPropertyCode.value = frmTenantDepositDtl.hidAddRealPropertyCode.value;
		frmTenantDepositDtl.hidBuildingCode.value = frmTenantDepositDtl.hidAddBuildingCode.value;
		frmTenantDepositDtl.hidUnitNo.value = frmTenantDepositDtl.hidAddUnitNo.value;
		frmTenantDepositDtl.hidAmount.value = frmTenantDepositDtl.txtAddAmount.value;
		frmTenantDepositDtl.hidDetailID.value = 0;
		frmTenantDepositDtl.hidSaveMode.value = "EDIT";
	}

	frmTenantDepositDtl.hidMode.value = "SAVE";
	frmTenantDepositDtl.submit();
}

function cmdRetrieve_OnClick() {
	frmTenantDepositDtl.hidMode.value = "RETRIEVE";
	frmTenantDepositDtl.submit();
}

function cmdPrint_OnClick() {
	frmTenantDepositDtl.hidORNo.value = frmTenantDepositDtl.txtORNo.value;
	frmTenantDepositDtl.hidMode.value = "PRINT";
	frmTenantDepositDtl.submit();
}

function cmdCancel_OnClick() {
	ctr = frmTenantDepositDtl.hidCurRow.value;
	//alert(frmTenantDepositDtl.hidSaveMode.value)
	if (frmTenantDepositDtl.hidSaveMode.value == "EDIT_DETAIL") {
		
		obj = eval("spEditAmount" + ctr);
		obj.style.visibility = "hidden"
		obj.style.display = "none"
		
		obj = eval("spAmount" + ctr);
		obj.style.visibility = "visible"
		obj.style.display = ""
		
		frmTenantDepositDtl.txtAddAmount.disabled = false;
		
		obj = eval("spAddDetailDis");
		obj.style.visibility = "hidden"
		obj.style.display = "none"
		
		obj = eval("spAddDetail");
		obj.style.visibility = "visible"
		obj.style.display = ""
		
		frmTenantDepositDtl.chkSelectAll.disabled = false;
		frmTenantDepositDtl.chkSelectAll.checked = false;
		frmTenantDepositDtl.hidSaveMode.value = "";
		enabledisablechkboxes(1);
		return false;
	}
	else {
		parent.frames[2].location = "tenant_deposit_detail.php?menu_id=" + frmTenantDepositDtl.hidMenuID.value;
		return false;
	}
}

function cmdDelete_OnClick() {
	var j
	j=0
	totalctr = frmTenantDepositDtl.hidRowCtr.value;
	for (i=1;i<=totalctr;i++) {
		obj = eval("frmTenantDepositDtl.chkDelete" + i);
		if (obj.checked == true) {
			j++;
		}
	}
	if (j > 0) {
		if (confirm("Are you sure you want to delete this record/s?")) {
			frmTenantDepositDtl.hidMode.value = "DELETE";
			frmTenantDepositDtl.submit();
		}
	}
	else {
		alert("Deleting is not allowed this time");
	}
}

function chkSelectAll_OnClick() {
	var ctr
	ctr = frmTenantDepositDtl.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmTenantDepositDtl.chkDelete" + i);
		if (frmTenantDepositDtl.chkSelectAll.checked == true) {
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

