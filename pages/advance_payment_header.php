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

$strORNo = "";
$dtOR = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
$strClientCode = "";
$strClientName = "";
$dblAmount = "0.00";
$dblBalanceAmount = "0.00";
$dblDeductedAmount = "0.00";
$strDocumentNo = "";
$strPaymentMode = "";
$strBankName = "";
$strRemarks = "";
$strStatus = "";
$strStatusDesc = "";
$blnVoid = "";
$dblPaymentDetailCnt = 0;
$dblTotalPaymentDetailAmount = "0.00";
$dblDeductedAmount = "0.00";
$dblBalanceAmount = "0.00";

$strMsg = "";
$strSaveMode = "";
$strMode = $_POST["hidMode"];
$strSaveMode = $_POST["hidSaveMode"];

if ($_GET["mode"] == "FIND") {
	$strORNo = $_GET["or_no"];
	$strMode = "FIND";
}

//echo $strMode;
if ($strMode != "") {	
	if ($strMode != "FIND") {
		$strORNo = replacesinglequote($_POST["hidORNo"]);
	}
	$dtOR = $_POST["DPC_txtORDate"];
	$strClientCode = replacesinglequote($_POST["hidClientCode"]);
	$strClientName = $_POST["hidClientName"];
	$dblAmount = $_POST["txtAmount"];
	$strPaymentMode = replacesinglequote($_POST["cboPaymentMode"]);
	$strBankName = replacesinglequote($_POST["txtBankName"]);
	$strRemarks = replacesinglequote($_POST["txtRemarks"]);	
	if ($_POST["chkVoid"] == "on") {
		$blnVoid = "V";
	}
	else {
		$blnVoid = "";		
	}

	//echo replacesinglequote($_POST["hidClientCode"]);
	switch ($strMode) {
		case "SAVE":
			if ($strSaveMode != "EDIT") {
				$strORNo = replacesinglequote($_POST["txtORNo"]);
				$sqlquery="exec sp_t_Advance_Payment_Header 'FIND','" . $strORNo . "','" . $dtOR . "','" . $strClientCode . "'," . $dblAmount . ",'" . $strDocumentNo . "','" . $strPaymentMode . "','" . $strBankName . "','" . $strRemarks . "','" . $strStatus . "','" . $strAccountNo . "',0,0,'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
				$process=odbc_exec($sqlconnect, $sqlquery);
				if (odbc_fetch_row($process)) {
						if (odbc_result($process,"x") == 1) 
							$strMsg = "OR No already exists!";
				}
			}
			//echo $sqlquery;
			//echo $strClientCode;
			//die();
			if ($strMsg == "") {
				$sqlquery="exec sp_t_Advance_Payment_Header_Save 'SAVE','" . $strORNo . "','" . $dtOR . "','" . $strClientCode . "'," . $dblAmount . ",'" . $strDocumentNo . "','" . $strPaymentMode . "','" . $strBankName . "','" . $strRemarks . "','" . $strStatus . "','" . $strAccountNo . "',0,0,'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
				$process=odbc_exec($sqlconnect, $sqlquery);
				$strMsg = "Record saved!";					
				//echo $sqlquery;
				if ($strSaveMode != "EDIT") {
					if (odbc_fetch_row($process)) {
						$strORNo = odbc_result($process,"or_no");
					}
				}
				//$strMode = "RETRIEVE";
			}
			break;
		
		case "DELETE":
			$sqlquery="exec sp_t_Advance_Payment_Header_Delete 'DELETE','" . $strORNo . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			//echo $sqlquery;
			if (odbc_fetch_row($process)) {
				if (odbc_result($process,"x") == 1) {
					$strMsg = "Record is already posted and/or with system-generated OR No.";
					$strORNo = odbc_result($process,"or_no");
					$strMode = "RETRIEVE";
					$strSaveMode = "EDIT";
				}
				else {
					$strMsg = "Record deleted!";
					$strORNo = "";
					$dtOR = "";
					$strClientCode = "";
					$strClientName = "";
					$dblAmount = "0.00";
					$strDocumentNo = "";
					$strPaymentMode = "";
					$strBankName = "";
					$strRemarks = "";
					$strStatus = "";
					$strStatusDesc = "";
					$dblPaymentDetailCnt = 0;
					$dblTotalPaymentDetailAmount = "0.00";
					$dblDeductedAmount = "0.00";
					$dblBalanceAmount = "0.00";
					$blnVoid = "";
					$strMode = "";
					$strSaveMode = "";
				}
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
			$my_report = $report_path . "t_or_advance_payment.rpt"; // 
			//echo $my_report;
			//die();
			//rpt source file 
			$pdf_file = "t_or_advance_payment" . str_replace("/","",date("m/d/y/H/i/s", time())) . ".pdf";
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
			//end print
			$strMode = "RETRIEVE";
			break;
		}
		
		if ($strMode == "RETRIEVE" || $strMode == "FIND") {
			$sqlquery="exec sp_t_Advance_Payment_Header_Retrieve 'RETRIEVE','" . $strORNo . "'";	
			//echo $sqlquery;
			$process=odbc_exec($sqlconnect, $sqlquery);
			if (odbc_fetch_row($process)) {
					$strORNo = odbc_result($process,"or_no");
					
					if (odbc_result($process,"or_date") == "" || date("m/d/Y",(strtotime(odbc_result($process,"or_date"))+60*60*24*($OFFSET))) == "01/01/1970")	
						$dtOR = "";
					else
						$dtOR = date("m/d/Y",(strtotime(odbc_result($process,"or_date"))+60*60*24*($OFFSET)));	
						
					$strClientCode = odbc_result($process,"client_code");
					$strClientName = replacedoublequotes(odbc_result($process,"client_name"));					
					$dblAmount = odbc_result($process,"amount");					
					$strDocumentNo = odbc_result($process,"document_no");
					$strPaymentMode = odbc_result($process,"mode_of_payment");
					$strBankName = odbc_result($process,"bank_name");
					$strRemarks = odbc_result($process,"remarks");
					$strStatus = odbc_result($process,"status");
					$strStatusDesc = odbc_result($process,"status_desc");
					$dblPaymentDetailCnt = odbc_result($process,"payment_detail_cnt");
					$dblTotalPaymentDetailAmount = $dblAmount;
					$dblBalanceAmount = odbc_result($process,"balance_amount");
					$dblDeductedAmount = odbc_result($process,"deducted_amount");
					
					if (odbc_result($process,"status") == "V") {
						$blnVoid = "checked";							
					}
					else {
						$blnVoid = "";	
					}
					$strMode = "RETRIEVE";
					$strSaveMode = "EDIT";
			}
			else {
				$strMsg = "No record found!";
				$strMode = "";
			}
	}
}
//echo $strMode;
if ($strMode!= "") {
	if ($strMode=="SAVE") {
		$i = 1;
		if ($_POST["hidIsCash"] == 1 || $_POST["hidIsCharge"] == 1 || $_POST["hidIsCheck"] == 1) {
			while ($i <= $_POST["hidRowCtr"]) {
				$intARModeID = $_POST["hidEditARModeID" . $i];
				$strPaymentModeType = $_POST["hidEditPaymentModeType" . $i];
				$dblPaymentModeAmount = $_POST["txtEditAmount" . $i];
				if ($strPaymentModeType == "1") {
					$strAccountNo = "";
					$strBankName = "";
				}
				else {
					$strAccountNo = replacesinglequote($_POST["txtEditAccountNo" . $i]);
					$strBankName = replacesinglequote($_POST["txtEditBankName" . $i]);
				}
				$sqlqueryPaymentMode="exec sp_t_Advance_Payment_Header_Save_Payment_Mode 'SAVE_PAYMENT_MODE','" . $strORNo . "','" . $dtOR . "','" . $strClientCode . "',0,'" . $strDocumentNo . "','" . $strPaymentModeType . "','" . $strBankName . "','" . $strStatus . "','" . $strAccountNo . "'," . $intARModeID . "," . $dblPaymentModeAmount . ",'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
				$processPaymentMode=odbc_exec($sqlconnect, $sqlqueryPaymentMode);
				//echo $sqlqueryPaymentMode;
				$i++;
			}
		}
		//echo $_POST["hidIsCash"];
		if ($_POST["hidIsCash"] == 0) {
			if ($_POST["txtEditAmountCash"] > 0) {
					$intARModeID = 0;
					$strPaymentModeType = "1";
					$dblPaymentModeAmount = $_POST["txtEditAmountCash"];				
					$strAccountNo = "";
					$strBankName = "";
					$sqlqueryPaymentMode="exec sp_t_Advance_Payment_Header_Save_Payment_Mode 'SAVE_PAYMENT_MODE','" . $strORNo . "','" . $dtOR . "','" . $strClientCode . "',0,'" . $strDocumentNo . "','" . $strPaymentModeType . "','" . $strBankName . "','" . $strStatus . "','" . $strAccountNo . "'," . $intARModeID . "," . $dblPaymentModeAmount . ",'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					$processPaymentMode=odbc_exec($sqlconnect, $sqlqueryPaymentMode);
					//echo $sqlqueryPaymentMode;
			}
		}
		
		if ($_POST["hidIsCharge"] == 0) {
			if ($_POST["txtEditAmountCharge"] > 0) {
					$intARModeID = 0;
					$strPaymentModeType = "2";
					$dblPaymentModeAmount = $_POST["txtEditAmountCharge"];				
					$strAccountNo = replacesinglequote($_POST["txtEditAccountNoCharge"]);
					$strBankName = replacesinglequote($_POST["txtEditBankNameCharge"]);
					$sqlqueryPaymentMode="exec sp_t_Advance_Payment_Header_Save_Payment_Mode 'SAVE_PAYMENT_MODE','" . $strORNo . "','" . $dtOR . "','" . $strClientCode . "',0,'" . $strDocumentNo . "','" . $strPaymentModeType . "','" . $strBankName . "','" . $strStatus . "','" . $strAccountNo . "'," . $intARModeID . "," . $dblPaymentModeAmount . ",'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					$processPaymentMode=odbc_exec($sqlconnect, $sqlqueryPaymentMode);
					//echo $sqlqueryPaymentMode;
			}
		}
		
		if ($_POST["hidIsCheck"] == 0) {
			if ($_POST["txtEditAmountCheck"] > 0) {
					$intARModeID = 0;
					$strPaymentModeType = "3";
					$dblPaymentModeAmount = $_POST["txtEditAmountCheck"];				
					$strAccountNo = replacesinglequote($_POST["txtEditAccountNoCheck"]);
					$strBankName = replacesinglequote($_POST["txtEditBankNameCheck"]);
					$sqlqueryPaymentMode="exec sp_t_Advance_Payment_Header_Save_Payment_Mode 'SAVE_PAYMENT_MODE','" . $strORNo . "','" . $dtOR . "','" . $strClientCode . "',0,'" . $strDocumentNo . "','" . $strPaymentModeType . "','" . $strBankName . "','" . $strStatus . "','" . $strAccountNo . "'," . $intARModeID . "," . $dblPaymentModeAmount . ",'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					$processPaymentMode=odbc_exec($sqlconnect, $sqlqueryPaymentMode);
					//echo $sqlqueryPaymentMode;
				}
		}
		
		if ($_POST["txtAddAmount"] > 0) {
				$intARModeID = 0;
				$strPaymentModeType = $_POST["cboAddPaymentMode"];
				$dblPaymentModeAmount = $_POST["txtAddAmount"];				
				$strAccountNo = replacesinglequote($_POST["txtAddAccountNo"]);
				$strBankName = replacesinglequote($_POST["txtAddBankName"]);
				$sqlqueryPaymentMode="exec sp_t_Advance_Payment_Header_Save_Payment_Mode 'SAVE_PAYMENT_MODE','" . $strORNo . "','" . $dtOR . "','" . $strClientCode . "',0,'" . $strDocumentNo . "','" . $strPaymentModeType . "','" . $strBankName . "','" . $strStatus . "','" . $strAccountNo . "'," . $intARModeID . "," . $dblPaymentModeAmount . ",'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
				$processPaymentMode=odbc_exec($sqlconnect, $sqlqueryPaymentMode);
				//echo $sqlqueryPaymentMode;
			}
		$strMode = "RETRIEVE";
		//echo $sqlqueryPaymentMode;
	}

	if ($strMode=="RETRIEVE" || $strMode=="FIND" || $strMode=="SAVE") {
		$sqlqueryPaymentMode="exec sp_t_Advance_Payment_Header_Retrieve_Payment_Mode 'RETRIEVE_PAYMENT_MODE','" . $strORNo . "'";	
		$processPaymentMode=odbc_exec($sqlconnect, $sqlqueryPaymentMode);
		//echo $sqlqueryPaymentMode;
	}
}

if ($strMsg != ""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 

?>
<html> 
<head> 
<title>ADVANCE PAYMENT</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
<script type="text/javascript" src="library/datepickercontrol/datepickercontrol.js"></script>
<link type="text/css" rel="stylesheet" href="library/datepickercontrol/datepickercontrol_green.css">
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color: #F3F5B4;">
<form name="frmAdvancePaymentHdr" id="frmAdvancePaymentHdr" method="post" action="advance_payment_header.php?menu_id=<?php echo $menu_id;?>">
	<div class="mainmenu">	
		<ul>
			 <li class="li_nc"><a href="#" onClick="javascript:change_loc('payment_main.php')"><u>PAYMENT</u></a></li>	
			 <li class="li_nc"><a name="MODULE NAME">>>&nbsp;&nbsp;&nbsp;ADVANCE
			  &nbsp;&nbsp;&nbsp;
			 </a></li>
			  <?php if ($strMode != "RETRIEVE") { ?>
				    <li class="li_nc"><a href="#" onClick="javascript:cmdRetrieve_OnClick()">|&nbsp;&nbsp;&nbsp;Retrieve&nbsp;&nbsp;&nbsp;|</a></li>			  
			  <?php } else { ?>			
				    <li><a name="Retrieve" style="color: #666666">|&nbsp;&nbsp;&nbsp;Retrieve&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } ?>	
			  			  
			   <?php if ($strMode == "RETRIEVE") { ?>
			   		<?php if ($strStatus == "") { ?>
						<li class="li_nc"><a href="#" onClick="javascript:cmdSave_OnClick()">Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
						<li class="li_nc"><a href="#" onClick="javascript:cmdDelete_OnClick()">Delete&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
				   <?php } else { ?>			
						<li><a name="Save" style="color: #666666">Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
						<li><a name="Delete" style="color: #666666">Delete&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
				  <?php } ?>	
				  
			  <?php } else { ?>			
			  		<li class="li_nc"><a href="#" onClick="javascript:cmdSave_OnClick()">Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>					
					<li><a name="Delete" style="color: #666666">Delete&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } ?>	
			  <li class="li_nc"><a href="#" onClick="javascript:change_loc('advance_payment_header_list.php?menu_id=<?php echo $menu_id;?>')">Find&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  
			  <?php if ($strMode != "RETRIEVE") { ?>
			  		<li><a name="Print" style="color: #666666">Print&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } else { ?>	
			  		<li class="li_nc"><a href="#" onClick="javascript:cmdPrint_OnClick()">Print&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>			
			  <?php } ?>	
			  
			  <li class="li_nc"><a href="#" onClick="javascript:cmdCancel_OnClick()">Cancel&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;||</a></li>			
			  <?php if ($strMode != "RETRIEVE") { ?>
			  	<li><a name="Detail" style="color: #666666">Detail&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } else { ?>	
			  	<li class="li_nc"><a href="#" target="_self" style="color: #FFFF33" onClick="javascript:change_loc('advance_payment_detail.php?menu_id=<?php echo $menu_id;?>&mode=FIND&menu=DETAIL&or_no=<?php echo $strORNo; ?>')">Detail&nbsp;&nbsp;&nbsp;&nbsp;<font color="white">|</font></a></li>
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
										<td class="fieldname">OR NO. :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<?php if ($strMode == "RETRIEVE") {?>
											<td><input type=text name="txtORNo" id="txtORNo" disabled class="values" size="20" maxlength="20" value="<?php echo $strORNo;?>"></td>
										<?php } else {?>
											<td><input type=text name="txtORNo" id="txtORNo" class="values" size="20" maxlength="20" value="<?php echo $strORNo;?>"></td>
										<?php } ?>
										<input type="hidden" name="hidORNo" id="hidORNo" value="<?php echo $strORNo;?>">
									</tr>
									<tr>
										<td class="fieldname">DATE (mm/dd/yyyy) :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="DPC_txtORDate" id="DPC_txtORDate" class="values" size="20" maxlength="10" value="<?php echo $dtOR;?>"></td>
									</tr>
									<tr>
										<td class="fieldname">CLIENT :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtClientName" id="txtClientName" disabled class="values" size="60" value="<?php echo $strClientName;?>">
										<input type="hidden" name="hidClientCode" id="hidClientCode" value="<?php echo $strClientCode;?>">
										<input type="hidden" name="hidClientName" id="hidClientName" value="<?php echo $strClientName;?>">
										<?php if ($dblPaymentDetailCnt == 0) {  ?>
											<img id="cmdClientSearch" name="cmdClientSearch" onClick="javascript:cmdClientSearch_onClick();" src="images/icon_search.gif" style="cursor:hand" alt="Client Lookup">
										<?php } ?>
										</td>
									</tr>										
									<tr>
										<td class="fieldname">TOTAL AMOUNT :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtAmount" id="txtAmount" class="values" style="text-align:right" size="20" value="<?php echo $dblAmount;?>"></td>
									</tr>	
									<tr>
										<td class="fieldname">BALANCE :</td>
										<td width="20"></td>
										<td class="values">
											<table><tr><td width="140" class="values" style="text-align:right">
												$<?php echo $dblBalanceAmount;?>
											</td></tr></table>
										</td>
									</tr>
									<tr>
										<td class="fieldname">DEDUCTED :</td>
										<td width="20"></td>
										<td class="values">
											<table><tr><td width="140" class="values" style="text-align:right">
												$<?php echo $dblDeductedAmount;?>
											</td></tr></table>
										</td>
									</tr>																											
									<tr>
										<td class="fieldname">REMARKS :</td>
										<td width="20"></td>
										<td><textarea name="txtRemarks" id="txtRemarks" class="values" rows="3" cols="60"><?php echo $strRemarks;?></textarea></td>
									</tr>					
									<?php if ($strStatus == "") {?>
										<tr>
											<td class="fieldname">&nbsp;</td>
											<td width="20"></td>
											<td class="values">
												<input type="checkbox" name="chkVoid" id="chkVoid" <?php echo $blnVoid;?>>VOID
											</td>
										</tr>				
									<?php } else { ?>		
										<tr>
											<td class="fieldname">STATUS :</td>
											<td width="20"></td>
											<td class="values" style="color:red"><b><?php echo $strStatusDesc;?></b>
											</td>
										</tr>					
									<?php } ?>													
								</table>
								<p></p>								
						        <table width="650" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
                                  <tr height="30">
                                    <td width="18%" class="tablehdr" align="center">&nbsp;Mode of Payment<em class="requiredYellow">*</em>&nbsp; </td>
                                    <td  width="21%" class="tablehdr" align="center">&nbsp;Amount<em class="requiredYellow">*</em>&nbsp; </td>
                                    <td  width="24%" class="tablehdr" align="center">&nbsp;Account No.&nbsp; </td>
                                    <td  width="37%" class="tablehdr" align="center">Bank Name </td>
                                  </tr>
                                  <?php
										$ctr = 0;								
										$rowColor = "";	
										$is_cash = 0;
										$is_charge = 0;
										$is_check = 0;
										while(odbc_fetch_row($processPaymentMode)) {										
											$ar_mode_id = odbc_result($processPaymentMode,"ar_mode_id"); 	
											$payment_mode_type = trim(odbc_result($processPaymentMode,"payment_mode_type")); 	
											$payment_mode_type_desc = trim(odbc_result($processPaymentMode,"payment_mode_type_desc")); 	
											$amount = odbc_result($processPaymentMode,"amount"); 	
											$account_no = trim(odbc_result($processPaymentMode,"account_no")); 	
											$bank_name = trim(odbc_result($processPaymentMode,"bank_name")); 	
											
											if ($payment_mode_type=="1")
												$is_cash = 1;
											
											if ($payment_mode_type=="2")
												$is_charge = 1;
												
											if ($payment_mode_type=="3")
												$is_check = 1;
											
											$ctr = $ctr+1;
											
											if ($ctr%2==1) 
												$rowColor = "98fb98";	
											else
												$rowColor = "ffffe0";			
									?>
                                  <tr id="editRow<?php echo "$ctr";?>" name="editRow<?php echo "$ctr";?>" bgcolor="<?php echo "$rowColor"; ?>">
                                    <td width="18%" style="border:1px" class="values">&nbsp;&nbsp;<?php echo "$payment_mode_type_desc";?>
                                        <input type="hidden" id="hidEditARModeID<?php echo "$ctr";?>" name="hidEditARModeID<?php echo "$ctr";?>" value="<?php echo $ar_mode_id;?>">
                                        <input type="hidden" id="hidEditPaymentModeType<?php echo "$ctr";?>" name="hidEditPaymentModeType<?php echo "$ctr";?>" value="<?php echo $payment_mode_type;?>">
                                    </td>
									<?php if ($dblTotalPaymentDetailAmount > 0 && $strStatus == "") { ?>
										<td width="21%" style="border:1px" class="values" align="center"><input type=text name="txtEditAmount<?php echo "$ctr";?>" id="txtEditAmount<?php echo "$ctr";?>" class="values" style="text-align:right" size="10" value="<?php echo $amount;?>">
										</td>
									<?php } else {?>
										<td width="21%" style="border:1px" class="values" align="center"><input type=text name="txtEditAmount<?php echo "$ctr";?>" id="txtEditAmount<?php echo "$ctr";?>" disabled class="values" style="text-align:right" size="10" value="<?php echo $amount;?>">
										</td>
									<?php } ?>
                                    <?php if ($payment_mode_type=="1") { ?>
                                    <td width="24%" style="border:1px" class="values">&nbsp;</td>
                                    <td width="37%" style="border:1px" class="values">&nbsp;</td>
                                    <?php } else { ?>
										<?php if ($dblTotalPaymentDetailAmount > 0 && $strStatus == "") { ?>
											<td width="24%" style="border:1px" class="values" align="center">&nbsp;
												<input type=text name="txtEditAccountNo<?php echo "$ctr";?>" id="txtEditAccountNo<?php echo "$ctr";?>" class="values" size="20" maxlength="20" value="<?php echo $account_no;?>">
											</td>
											<td width="37%" style="border:1px" class="values" align="center"><input type=text name="txtEditBankName<?php echo "$ctr";?>" id="txtEditBankName<?php echo "$ctr";?>" class="values" size="30" maxlength="50" value="<?php echo $bank_name;?>">
											</td>
									 	<?php } else { ?>
											<td width="24%" style="border:1px" class="values" align="center">&nbsp;
												<input type=text name="txtEditAccountNo<?php echo "$ctr";?>" id="txtEditAccountNo<?php echo "$ctr";?>" disabled class="values" size="20" maxlength="20" value="<?php echo $account_no;?>">
											</td>
											<td width="37%" style="border:1px" class="values" align="center"><input type=text name="txtEditBankName<?php echo "$ctr";?>" id="txtEditBankName<?php echo "$ctr";?>" disabled class="values" size="30" maxlength="50" value="<?php echo $bank_name;?>">
											</td>
										<?php } ?>
                                    <?php } ?>
                                  </tr>
                                  <?php } ?>
                                  <?php if ($is_cash==0) { 
											
											if ($rowColor == "ffffe0" || $rowColor == "")
												$rowColor = "98fb98";	 
											else
												$rowColor = "ffffe0";
									?>
                                  <tr id="editRowCash" name="editRowCash" bgcolor="<?php echo $rowColor; ?>">
                                    <td width="18%" style="border:1px" class="values">&nbsp;&nbsp;CASH
                                        <input type="hidden" id="hidEditCashPaymentModeID" name="hidEditCashPaymentModeID" value="">
                                        <input type="hidden" id="hidEditCashPaymentModeType" name="hidEditCashPaymentModeType" value="1">
                                    </td>
                                    <?php if ($dblTotalPaymentDetailAmount > 0 && $strStatus == "") { ?>
                                    <td width="21%" style="border:1px" class="values" align="center"><input type=text name="txtEditAmountCash" id="txtEditAmountCash" class="values" style="text-align:right" size="10" value="0.00">
                                    </td>
                                    <?php } else { ?>
                                    <td width="21%" style="border:1px" class="values" align="center"><input type=text name="txtEditAmountCash" id="txtEditAmountCash" disabled class="values" style="text-align:right" size="10" value="0.00">
                                    </td>
                                    <?php } ?>
                                    <td width="24%" style="border:1px" class="values">&nbsp;</td>
                                    <td width="37%" style="border:1px" class="values">&nbsp;</td>
                                  </tr>
                                  <?php } ?>
                                  <?php if ($is_charge==0) { 
										if ($rowColor == "ffffe0")
											$rowColor = "98fb98";	 
										else
											$rowColor = "ffffe0";
									?>
                                  <tr id="editRowCharge<?php echo "$ctr";?>" name="editRowCharge<?php echo "$ctr";?>" bgcolor="<?php echo "$rowColor"; ?>">
                                    <td width="18%" style="border:1px" class="values">&nbsp;&nbsp;CHARGE
                                        <input type="hidden" id="hidEditChargePaymentModeID<?php echo "$ctr";?>" name="hidEditChargePaymentModeID<?php echo "$ctr";?>" value="">
                                        <input type="hidden" id="hidEditChargePaymentModeType<?php echo "$ctr";?>" name="hidEditChargePaymentModeType<?php echo "$ctr";?>" value="1">
                                    </td>
                                    <?php if ($dblTotalPaymentDetailAmount > 0 && $strStatus == "") { ?>
                                    <td width="21%" style="border:1px" class="values" align="center"><input type=text name="txtEditAmountCharge" id="txtEditAmountCharge" class="values" style="text-align:right" size="10" value="0.00">
                                    </td>
                                    <td width="24%" style="border:1px" class="values" align="center">&nbsp;
                                        <input type=text name="txtEditAccountNoCharge" id="txtEditAccountNoCharge" class="values" size="20" maxlength="20" value="">
                                    </td>
                                    <td width="37%" style="border:1px" class="values" align="center"><input type=text name="txtEditBankNameCharge" id="txtEditBankNameCharge" class="values" size="30" maxlength="50" value="">
                                    </td>
                                    <?php } else { ?>
                                    <td width="21%" style="border:1px" class="values" align="center"><input type=text name="txtEditAmountCharge" id="txtEditAmountCharge" disabled class="values" style="text-align:right" size="10" value="0.00">
                                    </td>
                                    <td width="24%" style="border:1px" class="values" align="center">&nbsp;
                                        <input type=text name="txtEditAccountNoCharge" id="txtEditAccountNoCharge" disabled class="values" size="20" maxlength="20" value="">
                                    </td>
                                    <td width="37%" style="border:1px" class="values" align="center"><input type=text name="txtEditBankNameCharge" id="txtEditBankNameCharge" disabled class="values" size="30" maxlength="50" value="">
                                    </td>
                                    <?php } ?>
                                  </tr>
                                  <?php } ?>
                                  <?php if ($is_check==0) { 
										if ($rowColor == "ffffe0")
											$rowColor = "98fb98";	 
										else
											$rowColor = "ffffe0";
									?>
                                  <tr id="editRowCheck<?php echo "$ctr";?>" name="editRowCheck<?php echo "$ctr";?>" bgcolor="<?php echo "$rowColor"; ?>">
                                    <td width="18%" style="border:1px" class="values">&nbsp;&nbsp;CHECK
                                        <input type="hidden" id="hidEditCheckPaymentModeID<?php echo "$ctr";?>" name="hidEditCheckPaymentModeID<?php echo "$ctr";?>" value="">
                                        <input type="hidden" id="hidEditCheckPaymentModeType<?php echo "$ctr";?>" name="hidEditCheckPaymentModeType<?php echo "$ctr";?>" value="1">
                                    </td>
                                    <?php if ($dblTotalPaymentDetailAmount > 0 && $strStatus == "") { ?>
                                    <td width="21%" style="border:1px" class="values" align="center"><input type=text name="txtEditAmountCheck" id="txtEditAmountCheck" class="values" style="text-align:right" size="10" value="0.00">
                                    </td>
                                    <td width="24%" style="border:1px" class="values" align="center">&nbsp;
                                        <input type=text name="txtEditAccountNoCheck" id="txtEditAccountNoCheck" class="values" size="20" maxlength="20"  value="">
                                    </td>
                                    <td width="37%" style="border:1px" class="values" align="center"><input type=text name="txtEditBankNameCheck" id="txtEditBankNameCheck" class="values" size="30" maxlength="50" value="">
                                    </td>
                                    <?php } else { ?>
                                    <td width="21%" style="border:1px" class="values" align="center"><input type=text name="txtEditAmountCheck" id="txtEditAmountCheck" class="values" disabled style="text-align:right" size="10" value="0.00">
                                    </td>
                                    <td width="24%" style="border:1px" class="values" align="center">&nbsp;
                                        <input type=text name="txtEditAccountNoCheck" id="txtEditAccountNoCheck" class="values" disabled size="20" maxlength="20"  value="">
                                    </td>
                                    <td width="37%" style="border:1px" class="values" align="center"><input type=text name="txtEditBankNameCheck" id="txtEditBankNameCheck" class="values" disabled size="30" maxlength="50" value="">
                                    </td>
                                    <?php } ?>
                                  </tr>
                                  <?php } ?>
                                  <?php
											if ($rowColor == "ffffe0")
												$rowColor = "98fb98";	 
											else
												$rowColor = "ffffe0";
												
									?>
                                  <?php if ($dblTotalPaymentDetailAmount > 0 && $strStatus == "") { ?>
                                  <tr id="addRow" name="addRow" bgcolor="<?php echo "$rowColor"; ?>">
                                    <td width="18%" style="border:1px" >&nbsp;
                                        <select id="cboAddPaymentMode" name="cboAddPaymentMode" class="values">
                                          <option value="" selected>&nbsp;</option>
                                          <option value="1" >CASH</option>
                                          <option value="2" >CHARGE</option>
                                          <option value="3">CHECK</option>
                                        </select>
                                    </td>
                                    <td width="21%" style="border:1px" class="values" align="center"><input type=text name="txtAddAmount" id="txtAddAmount" class="values" style="text-align:right" size="10" value="0.00">
                                    </td>
                                    <td width="24%" style="border:1px" class="values" align="center">&nbsp;
                                        <input type=text name="txtAddAccountNo" id="txtAddAccountNo" class="values" size="20" maxlength="20"  value="">
                                    </td>
                                    <td width="37%" style="border:1px" class="values" align="center"><input type=text name="txtAddBankName" id="txtAddBankName" class="values" size="30" maxlength="50" value="">
                                    </td>
                                  </tr>
                                  <?php } else { ?>
                                  <tr id="addRow" name="addRow" bgcolor="<?php echo "$rowColor"; ?>">
                                    <td width="18%" style="border:1px" >&nbsp;
                                        <select id="cboAddPaymentMode" name="cboAddPaymentMode" class="values" disabled>
                                          <option value="" selected>&nbsp;</option>
                                          <option value="1" >CASH</option>
                                          <option value="2" >CHARGE</option>
                                          <option value="3">CHECK</option>
                                        </select>
                                    </td>
                                    <td width="21%" style="border:1px" class="values" align="center"><input type=text name="txtAddAmount" id="txtAddAmount" class="values" style="text-align:right" disabled size="10" value="0.00">
                                    </td>
                                    <td width="24%" style="border:1px" class="values" align="center">&nbsp;
                                        <input type=text name="txtAddAccountNo" id="txtAddAccountNo" class="values" disabled size="20" maxlength="20"  value="">
                                    </td>
                                    <td width="37%" style="border:1px" class="values" align="center"><input type=text name="txtAddBankName" id="txtAddBankName" class="values" disabled size="30" maxlength="50" value="">
                                    </td>
                                  </tr>
                                  <?php } ?>
                                </table></td>
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
	<input type="hidden" id="hidTotalPaymentDetailAmount" name="hidTotalPaymentDetailAmount" value=<?php echo $dblTotalPaymentDetailAmount; ?>>
	<input type="hidden" id="hidDeductedAmount" name="hidDeductedAmount" value=<?php echo $dblDeductedAmount; ?>>
	<input type="hidden" id="hidRowCtr" name="hidRowCtr" value=<?php echo $ctr;?>>
	<input type="hidden" id="hidIsCash" name="hidIsCash" value=<?php echo $is_cash;?>>
	<input type="hidden" id="hidIsCharge" name="hidIsCharge" value=<?php echo $is_charge;?>>
	<input type="hidden" id="hidIsCheck" name="hidIsCheck" value=<?php echo $is_check;?>>
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
	if (frmAdvancePaymentHdr.DPC_txtORDate.value == "") {
		alert("OR Date is required")
		frmAdvancePaymentHdr.DPC_txtORDate.focus()
		return false
	}
	if (frmAdvancePaymentHdr.DPC_txtORDate.value != "") {
		if (isDate(frmAdvancePaymentHdr.DPC_txtORDate.value)==false){
			frmAdvancePaymentHdr.DPC_txtORDate.focus()
			return false
		}
	}
	if (frmAdvancePaymentHdr.txtClientName.value == "") {
		alert("Client is required")
		frmAdvancePaymentHdr.cmdClientSearch.focus()
		return false
	}
	
	if (frmAdvancePaymentHdr.txtAmount.value == "" || Number(frmAdvancePaymentHdr.txtAmount.value)==0) {
		alert("Amount is required")
		frmAdvancePaymentHdr.txtAmount.focus()
		return false
	}
	
	if (isNaN(frmAdvancePaymentHdr.txtAmount.value)) {
		alert("Invalid numeric value")
		frmAdvancePaymentHdr.txtAmount.focus()
		return false
	}
	
	if (Number(frmAdvancePaymentHdr.txtAmount.value) < Number(frmAdvancePaymentHdr.hidDeductedAmount.value)) {
		alert("Total Amount should be greater than or equal the Deducted Amount")
		frmAdvancePaymentHdr.txtAmount.focus()
		return false
	}
	
	var j,dblTmpTotal
	j=0
	dblTmpTotal = 0
	totalctr = frmAdvancePaymentHdr.hidRowCtr.value;
	for (i=1;i<=totalctr;i++) {
		obj = eval("frmAdvancePaymentHdr.txtEditAmount" + i);
		if (isNaN(obj.value)) {
			alert("Invalid numeric value")
			obj.focus()
			return false
		}
		dblTmpTotal = dblTmpTotal + Number(obj.value)
	}
	
	if (frmAdvancePaymentHdr.hidIsCash.value == 0) {
		obj = eval("frmAdvancePaymentHdr.txtEditAmountCash");
		if (isNaN(obj.value)) {
			alert("Invalid numeric value")
			obj.focus()
			return false
		}
		if (Number(obj.value) > 0)
			dblTmpTotal = dblTmpTotal + Number(obj.value)
	}

	if (frmAdvancePaymentHdr.hidIsCharge.value == 0) {		
		obj = eval("frmAdvancePaymentHdr.txtEditAmountCharge");	
		if (isNaN(obj.value)) {
			alert("Invalid numeric value")
			obj.focus()
			return false
		}
		if (Number(obj.value) > 0)
			dblTmpTotal = dblTmpTotal + Number(obj.value)
	}
	
	if (frmAdvancePaymentHdr.hidIsCheck.value == 0) {		
		obj = eval("frmAdvancePaymentHdr.txtEditAmountCheck");	
		if (isNaN(obj.value)) {
			alert("Invalid numeric value")
			obj.focus()
			return false
		}	
		if (Number(obj.value) > 0)
			dblTmpTotal = dblTmpTotal + Number(obj.value)
	}
	
	obj = eval("frmAdvancePaymentHdr.txtAddAmount");	
	if (isNaN(obj.value)) {
		alert("Invalid numeric value")
		obj.focus()
		return false
	}	
	if (Number(obj.value) > 0 && frmAdvancePaymentHdr.cboAddPaymentMode.value=="") {
		alert("Mode of Payment is required")
		frmAdvancePaymentHdr.cboAddPaymentMode.focus()
		return false
	}
	
	if (Number(obj.value) > 0 && frmAdvancePaymentHdr.cboAddPaymentMode.value!="") {
		dblTmpTotal = dblTmpTotal + Number(obj.value)
	}
		
	if (dblTmpTotal != frmAdvancePaymentHdr.hidTotalPaymentDetailAmount.value) {
		alert("Amounts' total should be equal to $" + frmAdvancePaymentHdr.hidTotalPaymentDetailAmount.value)
		return false;
	}	
	
	if (frmAdvancePaymentHdr.chkVoid.checked == true) {
		if (confirm("Are you sure you want to void this OR?")) {
			frmAdvancePaymentHdr.hidMode.value = "SAVE";
			frmAdvancePaymentHdr.submit();		
		}
	}
	else {
		frmAdvancePaymentHdr.hidMode.value = "SAVE";
		frmAdvancePaymentHdr.submit();
	}
}

function cmdRetrieve_OnClick() {
	frmAdvancePaymentHdr.hidORNo.value = frmAdvancePaymentHdr.txtORNo.value;
	frmAdvancePaymentHdr.hidMode.value = "RETRIEVE";
	frmAdvancePaymentHdr.submit();
}

function cmdDelete_OnClick() {
	if (confirm("Are you sure you want to delete this record?")) {
		frmAdvancePaymentHdr.hidMode.value = "DELETE";
		frmAdvancePaymentHdr.submit();
	}
}

function cmdCancel_OnClick() {
	parent.frames[2].location = "advance_payment_header.php?menu_id=" + frmAdvancePaymentHdr.hidMenuID.value;
}

function cmdPrint_OnClick() {
	frmAdvancePaymentHdr.hidMode.value = "PRINT";
	frmAdvancePaymentHdr.submit();
}

function change_loc(pFile) {
	parent.frames[2].location = pFile;
	return false;
}

function cmdClientSearch_onClick() {
	window.open ("advance_payment_search_client.php?menu_id=" + frmAdvancePaymentHdr.hidMenuID.value,"displayWindow","type=fullwindow,titlebar=no,scrollbars=yes");
	return false;
}

/**
 * DHTML date validation script. Courtesy of SmartWebby.com (http://www.smartwebby.com/dhtml/)
 */
// Declaring valid date character, minimum year and maximum year
var dtCh= "/";
var minYear=1900;
var maxYear=2100;

function isInteger(s){
	var i;
    for (i = 0; i < s.length; i++){   
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}

function stripCharsInBag(s, bag){
	var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++){   
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

function daysInFebruary (year){
	// February has 29 days in any year evenly divisible by four,
    // EXCEPT for centurial years which are not also divisible by 400.
    return (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
}
function DaysArray(n) {
	for (var i = 1; i <= n; i++) {
		this[i] = 31
		if (i==4 || i==6 || i==9 || i==11) {this[i] = 30}
		if (i==2) {this[i] = 29}
   } 
   return this
}

function isDate(dtStr){
	var daysInMonth = DaysArray(12)
	var pos1=dtStr.indexOf(dtCh)
	var pos2=dtStr.indexOf(dtCh,pos1+1)
	var strMonth=dtStr.substring(0,pos1)
	var strDay=dtStr.substring(pos1+1,pos2)
	var strYear=dtStr.substring(pos2+1)
	strYr=strYear
	if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1)
	if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1)
	for (var i = 1; i <= 3; i++) {
		if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1)
	}
	month=parseInt(strMonth)
	day=parseInt(strDay)
	year=parseInt(strYr)
	if (pos1==-1 || pos2==-1){
		alert("The date format should be : mm/dd/yyyy")
		return false
	}
	if (strMonth.length<1 || month<1 || month>12){
		alert("Please enter a valid month")
		return false
	}
	if (strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){
		alert("Please enter a valid day")
		return false
	}
	if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){
		alert("Please enter a valid 4 digit year between "+minYear+" and "+maxYear)
		return false
	}
	if (dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){
		alert("Please enter a valid date")
		return false
	}
return true
}

function ValidateForm(){
	var dt=document.frmSample.txtDate
	if (isDate(dt.value)==false){
		dt.focus()
		return false
	}
    return true
 }
</script>