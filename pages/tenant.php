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
$strRealPropertyCode = "";
$strRealPropertyName = "";
$strBuildingCode = "";
$strUnitNo = "";
$strRealBldgUnit = "";
$strBillTo = "";
$strBillToName = "";
$strContactNo1 = "";
$strContactNo2 = "";
$strAddress1 = "";
$strAddress2 = "SAIPAN, MP 96950";
$strLastMeterReading = "";
$dblSecurityDeposit = "0.00";
$strRemarks = "";
$dtEffDate = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
$dtExpiryDate = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
$strSAPCode = "";
$blnSAPAffiliate = "";
$strNewCode = "";
$disNewCode = "disabled";
$strBusinessArea = "";
$disBusinessArea = "disabled";
$blnSAPEmployeeBenefit = "";
$disCostCenter =  "disabled";
$strCostCenter = "";
$blnTerminated = "";
$dtTerminated = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
$disdtTerminated = "disabled";
$dtActualMoveIn= date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
$strEmailAdd = "";
$blnEmployee = "";
$blnNotifications = "";
$strEmployer = "";
$strTenantType = "";

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
		$strRealPropertyCode = replacesinglequote($_POST["hidRealProperty"]);
		$strRealPropertyName = replacesinglequote($_POST["txtRealProperty"]);
		$strBuildingCode = replacesinglequote($_POST["hidBuildingCode"]);
		$strUnitNo = replacesinglequote($_POST["hidUnitNo"]);
		$strBillTo = replacesinglequote($_POST["hidBillTo"]);
		$strBillToName = replacesinglequote($_POST["hidBillToName"]);
		$strContactNo1 = replacesinglequote($_POST["txtContactNo1"]);
		$strContactNo2 = replacesinglequote($_POST["txtContactNo2"]);
		$strAddress1 = replacesinglequote($_POST["txtAddress1"]);
		$strAddress2 = replacesinglequote($_POST["txtAddress2"]);
		$dtEffDate = $_POST["DPC_txtEffDate"];
		$dtExpiryDate = replacesinglequote($_POST["DPC_txtExpiryDate"]);
		$strSAPCode = replacesinglequote($_POST["txtSAPCode"]);		
		
		if ($_POST["chkAffiliate"] == "on") {
			$blnSAPAffiliate = "Y";
			$strNewCode = replacesinglequote($_POST["txtNewCode"]);		
			$strBusinessArea = replacesinglequote($_POST["txtBusinessArea"]);
		}
		else {
			$blnSAPAffiliate = "N";
			$strNewCode = "";		
			$strBusinessArea = "";
		}
		
		if ($_POST["chkEmployeeBenefit"] == "on") {
			$blnSAPEmployeeBenefit = "Y";
			$strCostCenter = replacesinglequote($_POST["txtCostCenter"]);
		}
		else {
			$blnSAPEmployeeBenefit = "N";
			$strCostCenter = replacesinglequote($_POST["txtCostCenter"]);
		}
		
		if ($_POST["chkTerminated"] == "on") {
			$blnTerminated = "Y";
			$dtTerminated = replacesinglequote($_POST["DPC_txtDateTerminated"]);
		}
		else {
			$blnTerminated = "N";		
			$dtTerminated = "";
		}
		$dtActualMoveIn = replacesinglequote($_POST["DPC_txtActualMoveInDate"]);
		$strEmailAdd = replacesinglequote($_POST["txtEmailAdd"]);
		if ($_POST["chkEmployee"] == "on") {
			$blnEmployee = "Y";
		}
		else {
			$blnEmployee = "N";		
		}		
		if ($_POST["chkNotifications"] == "on") {
			$blnNotifications = "Y";
		}
		else {
			$blnNotifications = "N";		
		}		
		$strEmployer = replacesinglequote($_POST["txtEmployer"]);
		$strTenantType = replacesinglequote($_POST["cboTenantType"]);
		$strLastMeterReading = replacesinglequote($_POST["txtLastMeterReading"]);
		if ($_POST["txtSecurityDeposit"] == "") 
			$dblSecurityDeposit = 0;
		else
			$dblSecurityDeposit = $_POST["txtSecurityDeposit"];
		$strRemarks = replacesinglequote($_POST["txtRemarks"]);
		$company_code = $sessCompanyCode;
		$uid = $sessUserID;
	
	//echo $dtEffDate;
	switch ($strMode) {
		case "SAVE":
			if ($strSaveMode != "EDIT") {
				$strCode = replacesinglequote($_POST["txtCode"]);
				$sqlquery="exec sp_m_Tenant 'FIND','" . $strCode . "','" . $strName . "','" . $strRealPropertyCode . "','" . $strBuildingCode . "','" . $strUnitNo . "','" . $strBillTo . "','" . $strContactNo1 . "','" . $strContactNo2 . "','" . $strAddress1 . "','" . $strAddress2 . "','" . $dtEffDate . "','" . $dtExpiryDate . "','" . $strSAPCode . "','" . $blnTerminated . "','" . $dtTerminated . "','" . $dtActualMoveIn . "','" . $strEmailAdd . "','" . $blnEmployee . "','" . $strEmployer . "','" . $strTenantType . "','','','','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
				$process=odbc_exec($sqlconnect, $sqlquery);
				//echo $sqlquery;
				if (odbc_fetch_row($process)) {
						if (odbc_result($process,"x") == 1) 
							$strMsg = odbc_result($process,"msg");
				}
			}
			
			//if ($strMsg == "") {
			//	$sqlquery="exec sp_m_Tenant 'CHECK_UNIT_AVAILABLE','" . $strCode . "','" . $strName . "','" . $strRealPropertyCode . "','" . $strBuildingCode . "','" . $strUnitNo . "','" . $strBillTo . "','" . $strContactNo1 . "','" . $strContactNo2 . "','" . $strAddress1 . "','" . $strAddress2 . "','" . $dtEffDate . "','" . $dtExpiryDate . "','" . $strSAPCode . "','" . $blnTerminated . "','" . $dtTerminated . "','" . $dtActualMoveIn . "','" . $strEmailAdd . "','" . $blnEmployee . "','" . $strEmployer . "','" . $strTenantType . "','','','','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			//	$process=odbc_exec($sqlconnect, $sqlquery);
				//echo $sqlquery;
			//	if (odbc_fetch_row($process)) {
			//			if (odbc_result($process,"x") == 1) 
			//				$strMsg = odbc_result($process,"msg");
			//	}
			//}
			
			if ($strMsg == "") {
				$sqlquery="exec sp_m_Tenant 'CHECK_UNIT','" . $strCode . "','" . $strName . "','" . $strRealPropertyCode . "','" . $strBuildingCode . "','" . $strUnitNo . "','" . $strBillTo . "','" . $strContactNo1 . "','" . $strContactNo2 . "','" . $strAddress1 . "','" . $strAddress2 . "','" . $dtEffDate . "','" . $dtExpiryDate . "','" . $strSAPCode . "','" . $blnTerminated . "','" . $dtTerminated . "','" . $dtActualMoveIn . "','" . $strEmailAdd . "','" . $blnEmployee . "','" . $strEmployer . "','" . $strTenantType . "','','','','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
				$process=odbc_exec($sqlconnect, $sqlquery);
				//echo $sqlquery;
				if (odbc_fetch_row($process)) {
						if (odbc_result($process,"x") == 1) 
							$strMsg = odbc_result($process,"msg");
				}
			}
			
			if ($strMsg == "") {
				$sqlquery="exec sp_m_Tenant_Save 'SAVE','" . $strCode . "','" . $strName . "','" . $strRealPropertyCode . "','" . $strBuildingCode . "','" . $strUnitNo . "','" . $strBillTo . "','" . $strContactNo1 . "','" . $strContactNo2 . "','" . $strAddress1 . "','" . $strAddress2 . "','" . $dtEffDate . "','" . $dtExpiryDate . "','" . $strSAPCode . "','" . $blnTerminated . "','" . $dtTerminated . "','" . $dtActualMoveIn . "','" . $strEmailAdd . "','" . $blnEmployee . "','" . $blnNotifications . "','" . $strEmployer . "','" . $strTenantType . "','" . $blnSAPAffiliate . "','" . $strNewCode . "','" . $strBusinessArea ."','" . $strLastMeterReading ."','" . $dblSecurityDeposit ."','" . $strRemarks ."','" . $blnSAPEmployeeBenefit . "','" . $strCostCenter . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
				$process=odbc_exec($sqlconnect, $sqlquery);
				//echo $sqlquery;
				//exit();
				if (odbc_fetch_row($process)) {
					$strCode = odbc_result($process,"tenant_code");
				}
				$strMsg = "Record saved!";
				$strMode = "FIND";
			}
			//echo $sqlquery;
			break;
		
		case "DELETE":
			$sqlquery="exec sp_m_Tenant_Delete 'DELETE','" . $strCode . "','" . $strName . "','" . $strRealPropertyCode . "','" . $strBuildingCode . "','" . $strUnitNo . "','" . $strTenantType . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			//echo $sqlquery;
			if (odbc_fetch_row($process)) {
				if (odbc_result($process,"x") == 1) 
					$strMsg = "Record cannot be deleted. Remove first from Invoice module.";
				else
					$strMsg = "Record deleted!";
					$strCode = "";
					$strName = "";
					$strRealPropertyCode = "";
					$strRealPropertyName = "";
					$strBuildingCode = "";
					$strUnitNo = "";
					$strRealBldgUnit = "";
					$strBillTo = "";
					$strBillToName = "";
					$strContactNo1 = "";
					$strContactNo2 = "";
					$strAddress1 = "";
					$strAddress2 = "";
					$dtEffDate = "";
					$dtExpiryDate = "";
					$strSAPCode = "";
					$blnSAPAffiliate = "";
					$strNewCode = "";
					$strBusinessArea = "";
					$blnSAPEmployeeBenefit = "";
					$strCostCenter = "";
					$blnTerminated = "";
					$dtTerminated = "";
					$dtActualMoveIn= "";
					$strEmailAdd = "";
					$blnEmployee = "";
					$blnNotifications = "";
					$strEmployer = "";
					$strTenantType = "";
					$strMode = "";
					$strSaveMode = "";
			}
			break;
			
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
			$my_report = $report_path . "m_tenant_lease_agreement.rpt"; // 
			//echo $my_report;
			//die();
			//rpt source file 
			$pdf_file = "m_tenant_lease_agreement" . str_replace("/","",date("m/d/y/H/i/s", time())) . ".pdf";
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
			//$creport->FormulaFields->Item(1)->Text = ("'invoice_no'");
			$creport->ParameterFields(1)->AddCurrentValue ($_POST["hidCode"]);
			//$creport->ParameterFields(2)->AddCurrentValue ("");
			//$creport->ParameterFields(3)->AddCurrentValue ("");
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
			
			$file = "tenant_print.php";
			echo "<script type=\"text/javascript\">window.open (\"" . $file . "\");</script>";
			$strMode == "FIND";
			$strCode = $_POST["hidCode"];
			//end print
			break;
	}
			
	if ($strMode == "RETRIEVE" || $strMode == "FIND") {
		if ($strMode == "RETRIEVE") {
			$sqlquery="exec sp_m_Tenant_Retrieve 'RETRIEVE','" . $strCode . "','" . $strName . "'";	
		}			
		else {
			$sqlquery="exec sp_m_Tenant_Retrieve 'RETRIEVE_FIND','" . $strCode . "','" . $strName . "'";	
		}			
		//echo $sqlquery;
		$process=odbc_exec($sqlconnect, $sqlquery);
		if (odbc_fetch_row($process)) {
				$strCode = odbc_result($process,"tenant_code");
				$strName = odbc_result($process,"tenant_name");
				//$strName = $strName;
				//echo $strName;
				$strRealPropertyCode = odbc_result($process,"real_property_code");
				$strRealPropertyName = odbc_result($process,"real_property_name");
				$strBuildingCode = odbc_result($process,"building_code");
				$strUnitNo = odbc_result($process,"unit_no");
				$strBillTo = odbc_result($process,"bill_to");
				if (trim($strBillTo) == trim ($strCode)) 
					$strBillToName = "";
				else
					$strBillToName = odbc_result($process,"bill_to_name");
				
				$strContactNo1 = odbc_result($process,"contact_no1");
				$strContactNo2 = odbc_result($process,"contact_no2");
				$strAddress1 = odbc_result($process,"address1");	
				$strAddress2 = odbc_result($process,"address2");	
				//echo date("m/d/Y",(strtotime(odbc_result($process,"contract_eff_date"))+60*60*24*($OFFSET)));
				if (odbc_result($process,"contract_eff_date") == "" || date("m/d/Y",(strtotime(odbc_result($process,"contract_eff_date"))+60*60*24*($OFFSET))) == "01/01/1970")
					$dtEffDate = "";
				else
					$dtEffDate = date("m/d/Y",(strtotime(odbc_result($process,"contract_eff_date"))+60*60*24*($OFFSET)));
				
				if (odbc_result($process,"contract_expiry_date") == "" || date("m/d/Y",(strtotime(odbc_result($process,"contract_expiry_date"))+60*60*24*($OFFSET))) == "01/01/1970")	
					$dtExpiryDate = "";
				else
					$dtExpiryDate = date("m/d/Y",(strtotime(odbc_result($process,"contract_expiry_date"))+60*60*24*($OFFSET)));	
					
				$strSAPCode = odbc_result($process,"sap_code");	
				if (odbc_result($process,"is_sap_affiliate") == "Y") {
					$blnSAPAffiliate = "checked";	
					$strNewCode = odbc_result($process,"new_code");	
					$disNewCode = "";					
					$disBusinessArea = "";					
					$strBusinessArea = odbc_result($process,"business_area");	
				}
				else {
					$blnSAPAffiliate = "";	
					$strNewCode = "";	
					$strBusinessArea = "";	
				}
				
				if (odbc_result($process,"is_employee_benefit") == "Y") {
					$blnSAPEmployeeBenefit = "checked";	
					$strCostCenter = odbc_result($process,"employee_benefit_cc");	
					$disCostCenter = "";
				}
				else {
					$blnSAPEmployeeBenefit = "";
					$strCostCenter  = "";
					$disCostCenter = "disabled";
				}
				
				if (odbc_result($process,"terminated") == "Y") {
					$blnTerminated = "checked";	
					$disdtTerminated = "";
					if (odbc_result($process,"date_terminated") == "" || date("m/d/Y",(strtotime(odbc_result($process,"date_terminated"))+60*60*24*($OFFSET))) == "01/01/1970") {
						$dtTerminated = "";
					}
					else {
						$dtTerminated = date("m/d/Y",(strtotime(odbc_result($process,"date_terminated"))+60*60*24*($OFFSET)));	
					}
				}
				else {
					$blnTerminated = "";	
					$dtTerminated = "";
				}
				
				if (odbc_result($process,"actual_move_in_date") == "" || date("m/d/Y",(strtotime(odbc_result($process,"actual_move_in_date"))+60*60*24*($OFFSET))) == "01/01/1970")	
					$dtActualMoveIn= "";
				else
					$dtActualMoveIn = date("m/d/Y",(strtotime(odbc_result($process,"actual_move_in_date"))+60*60*24*($OFFSET)));	
					
				$strEmailAdd = odbc_result($process,"email_add");	
				
				if (odbc_result($process,"is_affiliate_employee") == "Y") 
					$blnEmployee = "checked";	
				else
					$blnEmployee = "";
					
				if (odbc_result($process,"is_notifications") == "Y") 
					$blnNotifications = "checked";	
				else
					$blnNotifications = "";
					
				$strEmployer = odbc_result($process,"employer");	
				$strTenantType = trim(odbc_result($process,"tenant_type"));					
				$strLastMeterReading = odbc_result($process,"last_meter_reading");	
				$dblSecurityDeposit = odbc_result($process,"security_deposit_amount");	
				$strRemarks = odbc_result($process,"tenant_remarks");	
				$strMode = "RETRIEVE";
				$strSaveMode = "EDIT";
		}
		else {
			$strMsg = "No record found!";
			$strMode = "";
			$strSaveMode = "";
		}
	}
}


if ($strMsg != ""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 

?>

<html> 
<head> 
<title>TENANT</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
<script type="text/javascript" src="library/datepickercontrol/datepickercontrol.js"></script>
<link type="text/css" rel="stylesheet" href="library/datepickercontrol/datepickercontrol_green.css">
<style type="text/css">
	.tooltip {
        display:inline;
        position: relative;
        font-family:Arial,Helvetica,sans-serif;
        font-size:9pt;
    }
    .tooltip:hover:after {
        background: #333;
        background: rgba(0,0,0,.8);
        border-radius: 5px;
        top: 6px;
        color: #fff;
        content: attr(title);
        left: 5px;
        padding: 5px 15px;
        position: absolute;
        z-index: 98;
        width: 220px;
        /*width: auto;*/
    }
    .tooltip:hover:before {
        border: solid;
        border-color: #333 transparent;
        border-width: 0 6px 6px 6px;
        top: 40px;
        content: "";
        left: 25px;
        position: absolute;
        z-index: 99;
    }
</style>
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color:#F3F5B4;">
<form name="frmTenant" id="frmTenant" method="post" action="tenant.php?menu_id=<?php echo $menu_id;?>">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">TENANT (LEASE CONTRACT)
			  
			 </a></li>	
			  <li class="li_nc"><a href="#" onClick="javascript:cmdRetrieve_OnClick()">|&nbsp;&nbsp;&nbsp;Retrieve&nbsp;&nbsp;|</a></li>
			  <li class="li_nc"><a href="#" onClick="javascript:cmdSave_OnClick()">Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php if ($strMode != "RETRIEVE") { ?>
					<li><a name="Delete" style="color: #666666">Delete&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } else { ?>			
					<li class="li_nc"><a href="#" onClick="javascript:cmdDelete_OnClick()">Delete&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } ?>	
			  <li class="li_nc"><a href="#" onClick="javascript:change_loc('tenant_list.php?menu_id=<?php echo $menu_id;?>')">Find&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php if ($strMode != "RETRIEVE") { ?>
			  		<li><a name="Print" style="color: #666666">Print&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } else { ?>	
			  		<li class="li_nc"><a href="#" onClick="javascript:cmdPrint_OnClick()">Print&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>			
			  <?php } ?>	
			  <li class="li_nc"><a href="#" onClick="javascript:cmdCancel_OnClick()">Cancel&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php if ($strMode != "RETRIEVE") { ?>
			  	<li><a name="Charges" style="color: #666666">Charges&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } else { ?>	
			  	<li class="li_nc"><a href="#" target="_self" style="color: #FFFF33" onClick="javascript:change_loc('tenant_charges.php?menu_id=<?php echo $menu_id;?>&mode=FIND&menu=DETAIL&code=<?php echo $strCode; ?>')">Charges&nbsp;&nbsp;&nbsp;&nbsp;<font color="white">|</font></a></li>
			  <?php } ?>	
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
										<td class="fieldname">TENANT CODE :</td>
										<td width="20"></td>
										<?php if ($strMode == "RETRIEVE") {?>
											<td><input type=text name="txtCode" id="txtCode" disabled class="values" size="20" maxlength="10" value="<?php echo $strCode;?>"></td>
										<?php } else {?>
											<td><input type=text name="txtCode" id="txtCode" disabled class="values" size="20" maxlength="10" value="<?php echo $strCode;?>"></td>
										<?php } ?>
										<input type="hidden" id="hidCode" name="hidCode" value="<?php echo $strCode;?>">
									</tr>
									<tr>
										<td class="fieldname">TENANT NAME :<em class="requiredRed">*</em></td>
										<td width="20"></td>										
										<?php if (finddoublequote($strName) !== false && findsinglequote($strName) === false) { ?>
											<td><input type=text name="txtName" id="txtName" class="values" size="60" maxlength="100" value='<?php echo $strName;?>'></td>
										<?php } else { ?>
											<td><input type=text name="txtName" id="txtName" class="values" size="60" maxlength="100" value="<?php echo $strName;?>"></td>
										<?php } ?>
									</tr>
									<tr>
										<td class="fieldname">TYPE :<em class="requiredRed">*</em></td>
										<td width="20"></td>										
										<td>
										<select id="cboTenantType" name="cboTenantType" class="values" onChange="cboTenantType_onChange()">
											<?php if ($strTenantType=="OC") { ?>
												<option value="OC" selected>OCCUPANT & CLIENT</option>
											<?php } else { ?>
												<option value="OC">OCCUPANT & CLIENT</option>
											<?php } if ($strTenantType=="O") { ?>
												<option value="O" selected>OCCUPANT</option>
											<?php } else { ?>
												<option value="O">OCCUPANT</option>
											<?php } if ($strTenantType=="C") { ?>
												<option value="C" selected>CLIENT</option>
											<?php } else { ?>
												<option value="C">CLIENT</option>
											<?php } ?>
										</select>
									</tr>	
									<tr>
										<td class="fieldname">REAL PROPERTY :<em class="requiredBlue">*</em></td>
										<td width="20"></td>										
										<td><input type=text name="txtRealProperty" id="txtRealProperty" class="values" disabled size="55" value="<?php echo $strRealPropertyName;?>">
										<span id="spRealPropertyOne" name="spRealPropertyOne" style="visibility:'';display:''">
											<?php if ($strTenantType=="C") { ?>
												<img id="cmdRealPropertySearch" name="cmdRealPropertySearch" src="images/icon_search_disabled.gif">
											<?php } else { ?>
												<img id="cmdRealPropertySearch" name="cmdRealPropertySearch" onClick="javascript:cmdRealPropertySearch_onClick();" src="images/icon_search.gif" style="cursor:hand" alt="Real Property Lookup">
											<?php } ?>
										</span>
										<span id="spRealProperty" name="spRealProperty" style="visibility:hidden;display:none">
											<img id="cmdRealPropertySearch" name="cmdRealPropertySearch" onClick="javascript:cmdRealPropertySearch_onClick();" src="images/icon_search.gif" style="cursor:hand" alt="Real Property Lookup">
										</span>
										<span id="spRealPropertyDis" name="spRealPropertyDis" style="visibility:hidden;display:none">
											<img id="cmdRealPropertySearch" name="cmdRealPropertySearch" src="images/icon_search_disabled.gif">
										</span>										
										</td>
										<input type="hidden" id="hidRealProperty" name="hidRealProperty" value="<?php echo $strRealPropertyCode;?>">
									</tr>	
									<tr>
										<td class="fieldname">BUILDING :<em class="requiredBlue">*</em></td>
										<td width="20"></td>										
										<td><input type=text name="txtBuildingCode" id="txtBuildingCode" class="values" disabled size="20" value="<?php echo $strBuildingCode;?>"></td>
										<input type="hidden" id="hidBuildingCode" name="hidBuildingCode" value="<?php echo $strBuildingCode;?>">
									</tr>
									<tr>
										<td class="fieldname">UNIT NO. :<em class="requiredBlue">*</em></td>
										<td width="20"></td>										
										<td><input type=text name="txtUnitNo" id="txtUnitNo" class="values" disabled  size="10" value="<?php echo $strUnitNo;?>"></td>
										<input type="hidden" id="hidUnitNo" name="hidUnitNo" value="<?php echo $strUnitNo;?>">
									</tr>											
									<tr>
										<td class="fieldname">BILL TO :<em class="requiredBlue">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtBillTo" id="txtBillTo" class="values" disabled size="60" maxlength="100" value="<?php echo $strBillToName;?>">
										<span id="spBillToOne" name="spBillToOne" style="visibility:'';display:''">
											<?php if ($strTenantType=="O") { ?>
												<img id="cmdBillToSearch" name="cmdBillToSearch" onClick="javascript:cmdBillToSearch_onClick();" src="images/icon_search.gif" style="cursor:hand" alt="Client Lookup">										
											<?php } else { ?>
												<img id="cmdBillToSearch" name="cmdBillToSearch" src="images/icon_search_disabled.gif">
											<?php } ?>				
										</span>				
										<span id="spBillTo" name="spBillTo" style="visibility:none;display:none">											
											<img id="cmdBillToSearch" name="cmdBillToSearch" onClick="javascript:cmdBillToSearch_onClick();" src="images/icon_search.gif" style="cursor:hand" alt="Client Lookup">										
										</span>		
										<span id="spBillToDis" name="spBillToDis" style="visibility:none;display:none">											
											<img id="cmdBillToSearch" name="cmdBillToSearch" src="images/icon_search_disabled.gif">
										</span>								
										</td>
										<input type="hidden" id="hidBillTo" name="hidBillTo" value="<?php echo $strBillTo;?>">
										<input type="hidden" id="hidBillToName" name="hidBillToName" value="<?php echo $strBillToName;?>">
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
	<div style="background-color:#3C6500;border-top: 1px solid;border-bottom: 1px solid;border-color:green; height:1.5em; font-family:Verdana; font-weight:bold; font-size:0.9em;color:white;">&nbsp;&nbsp;&nbsp;SAP AFFILIATION
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
										<td class="fieldname">ACCOUNT CODE (SAP) :<em class="requiredBlue">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtSAPCode" id="txtSAPCode" class="values" size="30" maxlength="20" value="<?php echo $strSAPCode;?>"></td>
									</tr>	
									<tr>
										<td class="fieldname">&nbsp;</td>
										<td width="20"></td>
										<td class="values">
											<input name="chkAffiliate" id="chkAffiliate" type="checkbox" <?php echo $blnSAPAffiliate;?> onClick="javascript:chkAffiliate_onClick()">AFFILIATE
										</td>
									</tr>	
									<tr>
										<td class="fieldname">NEW CODE :</td>
										<td width="20"></td>
										<td><input type=text name="txtNewCode" id="txtNewCode" class="values" <?php echo $disNewCode;?> size="30" maxlength="30" value="<?php echo $strNewCode;?>"></td>
									</tr>	
									<tr>
										<td class="fieldname">BUSINESS AREA :</td>
										<td width="20"></td>
										<td><input type=text name="txtBusinessArea" id="txtBusinessArea" class="values" <?php echo $disBusinessArea;?> size="30" maxlength="30" value="<?php echo $strBusinessArea;?>"></td>
									</tr>			
									<tr>
										<td class="fieldname">&nbsp;</td>
										<td width="20"></td>
										<td class="values">
											<input name="chkEmployeeBenefit" id="chkEmployeeBenefit" type="checkbox" <?php echo $blnSAPEmployeeBenefit;?> onClick="javascript:chkEmployeeBenefit_onClick()">EMPLOYEE BENEFIT
										</td>
									</tr>					
									<tr>
										<td class="fieldname">COST CENTER :</td>
										<td width="20"></td>
										<td><input type=text name="txtCostCenter" id="txtCostCenter" class="values" <?php echo $disCostCenter;?> size="20" maxlength="7" value="<?php echo $strCostCenter;?>"></td>
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
	<div style="background-color:#3C6500;border-top: 1px solid;border-bottom: 1px solid;border-color:green; height:1.5em; font-family:Verdana; font-weight:bold; font-size:0.9em;color:white;">&nbsp;&nbsp;&nbsp;CONTRACT INFO
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
										<td class="fieldname">LAST METER READING :</td>
										<td width="20"></td>
										<td><input type=text name="txtLastMeterReading" id="txtLastMeterReading" class="values" size="30" maxlength="20" value="<?php echo $strLastMeterReading;?>"></td>
									</tr>	
									<tr>
										<td class="fieldname">SECURITY DEPOSIT ($) :</td>
										<td width="20"></td>
										<td><input type=text name="txtSecurityDeposit" id="txtSecurityDeposit" class="values" style="text-align:right" size="10" maxlength="10" value="<?php echo $dblSecurityDeposit;?>"></td>
									</tr>	
									<tr>
										<td class="fieldname">REMARKS :</td>
										<td width="20"></td>
										<td><textarea name="txtRemarks" id="txtRemarks" class="values" rows="3" cols="40"><?php echo $strRemarks;?></textarea></td>
									</tr>	
									<tr>
										<td class="fieldname">EFFECTIVITY (mm/dd/yyyy) :</td>
										<td width="20"></td>
										<td>
											<input type="text" id="DPC_txtEffDate" name="DPC_txtEffDate" class="values" size="20" maxlength="10" value="<?php echo $dtEffDate;?>">
										</td>
									</tr>	
									<tr>
										<td class="fieldname">EXPIRY (mm/dd/yyyy) :</td>
										<td width="20"></td>
										<td><input type=text name="DPC_txtExpiryDate" id="DPC_txtExpiryDate" class="values" size="20" maxlength="10" value="<?php echo $dtExpiryDate;?>"></td>
									</tr>	
									<tr>
										<td class="fieldname">ACTUAL MOVE IN (mm/dd/yyyy) :</td>
										<td width="20"></td>
										<td><input type=text name="DPC_txtActualMoveInDate" id="DPC_txtActualMoveInDate" class="values" size="20" maxlength="10" value="<?php echo $dtActualMoveIn;?>"></td>
									</tr>	
									<tr>
										<td class="fieldname">&nbsp;</td>
										<td width="20"></td>
										<td class="values">
											<input name="chkTerminated" id="chkTerminated" type="checkbox" <?php echo $blnTerminated;?> onClick="javascript:chkTerminated_onClick()">TERMINATED
										</td>
									</tr>	
									<tr>
										<td class="fieldname">DATE TERMINATED (mm/dd/yyyy):</td>
										<td width="20"></td>
										<td><input type=text name="DPC_txtDateTerminated" id="DPC_txtDateTerminated" class="values" <?php echo $disdtTerminated;?> size="20" maxlength="10" value="<?php echo $dtTerminated;?>"></td>
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
	<div style="background-color:#3C6500;border-top: 1px solid;border-bottom: 1px solid;border-color:green; height:1.5em; font-family:Verdana; font-weight:bold; font-size:0.9em;color:white;">&nbsp;&nbsp;&nbsp;BASIC INFO
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
										<td class="fieldname">ADDRESS 1 :</td>
										<td width="20"></td>
										<td><input type=text name="txtAddress1" id="txtAddress1" class="values" size="50" maxlength="50" value="<?php echo $strAddress1;?>"></td>
									</tr>								
									<tr>
										<td class="fieldname">ADDRESS 2 :</td>
										<td width="20"></td>
										<td><input type=text name="txtAddress2" id="txtAddress2" class="values" size="50" maxlength="50" value="<?php echo $strAddress2;?>"></td>
									</tr>	
									<tr>
										<td class="fieldname">CONTACT NO. 1 :</td>
										<td width="20"></td>
										<td><input type=text name="txtContactNo1" id="txtContactNo1" class="values" size="20" maxlength="20" value="<?php echo $strContactNo1;?>"></td>
									</tr>		
									<tr>
										<td class="fieldname">CONTACT NO. 2 :</td>
										<td width="20"></td>
										<td><input type=text name="txtContactNo2" id="txtContactNo2" class="values" size="20" maxlength="20" value="<?php echo $strContactNo2;?>"></td>
									</tr>											
									<tr>
										<td class="fieldname" title="Separate e-mail addresses with semicolon">E-MAIL ADDRESS :</td>
										<td width="20"></td>
										<!-- <td><input type=text name="txtEmailAdd" id="txtEmailAdd" class="values" size="50" maxlength="256" value="<?php //echo $strEmailAdd;?>"></td> -->
										<td><textarea name="txtEmailAdd" id="txtEmailAdd" class="values" rows="3" cols="51" maxlength="256" title="Separate e-mail addresses with semicolon"><?php echo $strEmailAdd;?></textarea></td>
									</tr>	
									<tr>
										<td class="fieldname">&nbsp;</td>
										<td width="20"></td>
										<td class="values">
											<input name="chkEmployee" id="chkEmployee" type="checkbox" <?php echo $blnEmployee;?>>AFFILIATE EMPLOYEE
										</td>
									</tr>		
									<tr>
										<td class="fieldname">&nbsp;</td>
										<td width="20"></td>
										<td class="values">
											<input name="chkNotifications" id="chkNotifications" type="checkbox" <?php echo $blnNotifications;?>>RECEIVE NOTIFICATIONS
										</td>
									</tr>			
									<tr>
										<td class="fieldname">EMPLOYER :</td>
										<td width="20"></td>
										<td><input type=text name="txtEmployer" id="txtEmployer" class="values" size="50" maxlength="100" value="<?php echo $strEmployer;?>"></td>
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
	<input type="hidden" id="hidRealPropertyCodeTmp" name="hidRealPropertyCodeTmp" value="<?php echo $strRealPropertyCode;?>">
	<input type="hidden" id="hidRealPropertyNameTmp" name="hidRealPropertyNameTmp" value="<?php echo $strRealPropertyName;?>">
	<input type="hidden" id="hidBuildingCodeTmp" name="hidBuildingCodeTmp" value="<?php echo $strBuildingCode;?>">
	<input type="hidden" id="hidUnitNoTmp" name="hidUnitNoTmp" value="<?php echo $strUnitNo;?>">
	<input type="hidden" id="hidBillToCodeTmp" name="hidBillToCodeTmp" value="<?php echo $strBillTo;?>">
	<input type="hidden" id="hidBillToNameTmp" name="hidBillToNameTmp" value="<?php echo $strBillToName;?>">
</form>
</body> 
</html>

<script language="javascript" src="jsp/function.js"></script>
<script type="text/javascript">

function hov(loc,cls) {   
	if(loc.className)   
	loc.className=cls;   
} 

function cboTenantType_onChange() {
	if (frmTenant.cboTenantType.value == "O" || frmTenant.cboTenantType.value == "OC") {
		obj = eval("spRealPropertyOne");
		obj.style.visibility = "hidden"
		obj.style.display = "none"
		
		obj = eval("spRealPropertyDis");
		obj.style.visibility = "hidden"
		obj.style.display = "none"
		
		obj = eval("spRealProperty");
		obj.style.visibility = "visible"
		obj.style.display = ""
		
		frmTenant.hidRealProperty.value = frmTenant.hidRealPropertyCodeTmp.value;
		frmTenant.hidBuildingCode.value = frmTenant.hidBuildingCodeTmp.value;
		frmTenant.hidUnitNo.value = frmTenant.hidUnitNoTmp.value;
		frmTenant.txtRealProperty.value = frmTenant.hidRealPropertyNameTmp.value ;
		frmTenant.txtBuildingCode.value = frmTenant.hidBuildingCodeTmp.value;
		frmTenant.txtUnitNo.value = frmTenant.hidUnitNoTmp.value;
			
		if (frmTenant.cboTenantType.value == "O") {
			obj = eval("spBillToOne");
			obj.style.visibility = "hidden"
			obj.style.display = "none"
			
			obj = eval("spBillToDis");
			obj.style.visibility = "hidden"
			obj.style.display = "none"
			
			obj = eval("spBillTo");
			obj.style.visibility = "visible"
			obj.style.display = ""
			
			frmTenant.hidBillTo.value = frmTenant.hidBillToCodeTmp.value;
			frmTenant.hidBillToName.value = frmTenant.hidBillToNameTmp.value;
			frmTenant.txtBillTo.value = frmTenant.hidBillToNameTmp.value;
		}
		else {
			obj = eval("spBillToOne");
			obj.style.visibility = "hidden"
			obj.style.display = "none"
			
			obj = eval("spBillTo");
			obj.style.visibility = "hidden"
			obj.style.display = "none"
			
			obj = eval("spBillToDis");
			obj.style.visibility = "visible"
			obj.style.display = ""
			
			frmTenant.hidBillTo.value = "";
			frmTenant.hidBillToName.value = "";
			frmTenant.txtBillTo.value = "";
		}
		 
	}
	else {
		obj = eval("spRealPropertyOne");
		obj.style.visibility = "hidden"
		obj.style.display = "none"
		
		obj = eval("spRealProperty");
		obj.style.visibility = "hidden"
		obj.style.display = "none"
		
		obj = eval("spRealPropertyDis");
		obj.style.visibility = "visible"
		obj.style.display = ""
		
		obj = eval("spBillToOne");
		obj.style.visibility = "hidden"
		obj.style.display = "none"
		
		obj = eval("spBillTo");
		obj.style.visibility = "hidden"
		obj.style.display = "none"
		
		obj = eval("spBillToDis");
		obj.style.visibility = "visible"
		obj.style.display = ""
		
		frmTenant.hidRealProperty.value = "";
		frmTenant.hidBuildingCode.value = "";
		frmTenant.hidUnitNo.value = "";
		frmTenant.txtRealProperty.value = "";
		frmTenant.txtBuildingCode.value = "";
		frmTenant.txtUnitNo.value = "";
		frmTenant.hidBillTo.value = "";
		frmTenant.hidBillToName.value = "";
		frmTenant.txtBillTo.value = "";
	}
}

function chkAffiliate_onClick() {
	if (frmTenant.chkAffiliate.checked == true) {
		frmTenant.txtNewCode.disabled = false		
		frmTenant.txtBusinessArea.disabled = false		
	}
	else {
		frmTenant.txtNewCode.disabled = true		
		frmTenant.txtBusinessArea.disabled = true		
	}
	return false
}

function chkEmployeeBenefit_onClick() {
	if (frmTenant.chkEmployeeBenefit.checked == true) {
		frmTenant.txtCostCenter.disabled = false		
	}
	else {
		frmTenant.txtCostCenter.disabled = true		
	}
	return false
}


function chkTerminated_onClick() {
	if (frmTenant.chkTerminated.checked == true) {
		frmTenant.DPC_txtDateTerminated.disabled = false		
	}
	else {
		frmTenant.DPC_txtDateTerminated.disabled = true		
	}
	return false
}

function cmdSave_OnClick() {	
	if (trim(frmTenant.txtName.value) == "") {
		alert("Tenant Name is required")
		frmTenant.txtName.focus()
		return false
	}
	
	if (frmTenant.cboTenantType.value == "OC" || frmTenant.cboTenantType.value == "O") {
		if (trim(frmTenant.hidRealProperty.value) == "") {
			alert("Real Property is required. Please use the Real Property Look up button.")			
			return false
		}
		if (trim(frmTenant.hidBuildingCode.value) == "") {
			alert("Building Code is required. Please use the Real Property Look up button.")
			return false
		}
		if (trim(frmTenant.hidUnitNo.value) == "") {
			alert("Unit is required. Please use the Real Property Look up button.")
			return false	
		}
	}
	
	if (trim(frmTenant.cboTenantType.value) == "O") {
		if (trim(frmTenant.hidBillTo.value) == "" || trim(frmTenant.hidBillTo.value) == trim(frmTenant.hidCode.value)) {
			alert("Bill To is required. Please use the Client Look up button.")
			return false
		}
	}
	
	if (frmTenant.cboTenantType.value != "O") {
		if (trim(frmTenant.txtSAPCode.value) == "") {
			alert("Account Code is required")
			frmTenant.txtSAPCode.focus()
			return false
		}
	}
	
	if (frmTenant.chkAffiliate.checked ==true) {
		if (frmTenant.txtNewCode.value == "") {
			alert("New Code is required")
			frmTenant.txtNewCode.focus()
			return false
		}
		
		if (trim(frmTenant.txtBusinessArea.value) == "") {
			alert("Business Area is required")
			frmTenant.txtBusinessArea.focus()
			return false
		}
	}
	
	if (isNaN(frmTenant.txtSecurityDeposit.value) && trim(frmTenant.txtSecurityDeposit.value)!="") {
		alert("Invalid numeric value")
		frmTenant.txtSecurityDeposit.focus()
		return false
	}
		
	if (trim(frmTenant.DPC_txtEffDate.value) != "") {
		if (isDate(frmTenant.DPC_txtEffDate.value)==false){
			frmTenant.DPC_txtEffDate.focus()
			return false
		}
	}

	if (trim(frmTenant.DPC_txtExpiryDate.value) != "") {
		if (isDate(frmTenant.DPC_txtExpiryDate.value)==false) {
			frmTenant.DPC_txtExpiryDate.focus()
			return false
		}
	}
	
	if (frmTenant.DPC_txtEffDate.value != "" && frmTenant.DPC_txtExpiryDate.value != "") {
		if (CompareDatesNoMsg(frmTenant.DPC_txtEffDate.value,frmTenant.DPC_txtExpiryDate.value)==false) {
			alert ("Contract Eff Date should be earlier than Contract Expiry Date")
			frmTenant.DPC_txtEffDate.focus()
			return false
		}
	}
	
	if (trim(frmTenant.DPC_txtActualMoveInDate.value) != "") {
		if (isDate(frmTenant.DPC_txtActualMoveInDate.value)==false) {
			frmTenant.DPC_txtActualMoveInDate.focus()
			return false
		}
	}
	
	if (frmTenant.DPC_txtActualMoveInDate.value != "" && frmTenant.DPC_txtExpiryDate.value != "") {
		if (CompareDatesNoMsg(frmTenant.DPC_txtActualMoveInDate.value,frmTenant.DPC_txtExpiryDate.value)==false) {
			alert ("Actual Move In Date should be earlier than Contract Expiry Date")
			frmTenant.DPC_txtActualMoveInDate.focus()
			return false
		}
	}
	
	if (frmTenant.chkTerminated.checked ==true) {
		if (isDate(frmTenant.DPC_txtDateTerminated.value)==false){
			frmTenant.DPC_txtDateTerminated.focus()
			return false
		}
	}
	
	if (trim(frmTenant.txtEmailAdd.value) != "") {
		if (checkValidEmail(frmTenant.txtEmailAdd)==false){
			frmTenant.txtEmailAdd.focus()
			return false		
		}
	}

	frmTenant.hidMode.value = "SAVE";
	frmTenant.submit();
}

function cmdRetrieve_OnClick() {
	frmTenant.hidCode.value = frmTenant.txtCode.value;
	frmTenant.hidMode.value = "RETRIEVE";
	frmTenant.submit();
}

function cmdPrint_OnClick() {
	frmTenant.hidCode.value = frmTenant.txtCode.value;
	window.open ("tenant_print.php?tenant_code=" + frmTenant.hidCode.value,"displayWindow","type=fullwindow,titlebar=no,scrollbars=yes");
	return false;
}


function cmdCancel_OnClick() {
	parent.frames[2].location = "tenant.php?menu_id=" + frmTenant.hidMenuID.value;
	return false;
}

function cmdDelete_OnClick() {
	if (frmTenant.txtCode.value == "") {
		alert("Tenant Code is blank")
		frmTenant.txtCode.focus()
		return false
	}
	else {
		if (confirm("Are you sure you want to delete this record?")) {
			frmTenant.hidMode.value = "DELETE";
			frmTenant.submit();
		}
	}
}

function cmdRealPropertySearch_onClick() {
	window.open ("real_property_search.php?menu_id=" + frmTenant.hidMenuID.value,"displayWindow","type=fullwindow,titlebar=no,scrollbars=yes");
	return false;
}

function cmdBillToSearch_onClick() {
	window.open ("tenant_search_billto.php?menu_id=" + frmTenant.hidMenuID.value,"displayWindow","type=fullwindow,titlebar=no,scrollbars=yes");
	return false;
}

</script>