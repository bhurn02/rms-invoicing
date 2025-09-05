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
$strInvoiceNo = "";
$dblInvoiceAmount = "0.00";
$intInvoiceDetailID = 0;
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
$dblChargeAmount = 0;	
$dblTotalChargeAmount = 0;	
$strRemarks = "";
$strMsg = "";
$strMenu = "";
//echo $strMode;
//echo $sqlqueryCharges;
$sqlquerycbo="select * from m_charges order by charge_desc";
$processcbo=odbc_exec($sqlconnect, $sqlquerycbo);
while (odbc_fetch_row($processcbo)) {
	$cbocharge .= "<option value=\"" . trim(odbc_result($processcbo,"charge_code")) . "\">" . trim(strtoupper(odbc_result($processcbo,"charge_desc"))) . "</option>";
}

if ($_GET["mode"] == "FIND") {
	$strMenu = $_GET["menu"];
	$strInvoiceNo = $_GET["invoice_no"];
	$strMode = "FIND";
}

if ($strMode != "") {
	if ($strMode != "FIND") {
		if ($strMode != "RETRIEVE") {
			$strInvoiceNo = replacesinglequote($_POST["hidInvoiceNo"]);
		}		
		else {
			$strInvoiceNo = replacesinglequote($_POST["txtInvoiceNo"]);
		}		
		$uid = $sessUserID;
		$company_code = $sessCompanyCode;
	}
	//echo $strSaveMode;
	switch ($strMode) {
		case "SAVE":
			$strInvoiceNo = replacesinglequote($_POST["hidInvoiceNo"]);
			$intInvoiceDetailID = $_POST["hidInvoiceDetailID"];
			$intInvoiceDetailReadingID = $_POST["hidInvoiceDetailReadingID"];
			$strTenantCode = replacesinglequote($_POST["hidTenantCode"]);		
			$strChargeCode = replacesinglequote($_POST["hidChargeCode"]);		
			$intReadingID = replacesinglequote($_POST["hidReadingID"]);		
			$dblChargeAmount = $_POST["hidChargeAmount"];		
			$strRemarks = "";
			$sqlquery="exec sp_t_InvoiceDetail_Check 'FIND','" . $strInvoiceNo . "'," . $intInvoiceDetailID . "," . $intInvoiceDetailReadingID . ",'" . $strTenantCode . "','" . $strChargeCode . "','" . $strChargeType . "'," . $intReadingID . "";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			if (odbc_fetch_row($process)) {
					if (odbc_result($process,"x") == 1) 
						$strMsg = odbc_result($process,"msg");
			}
			//echo $sqlquery;
			if ($strMsg == "") {				
				$sqlquery="exec sp_t_InvoiceDetail_Save 'SAVE','" . $strInvoiceNo . "'," . $intInvoiceDetailID . "," . $intInvoiceDetailReadingID . ",'" . $strTenantCode . "','" . $strChargeCode . "','" . $strChargeType . "'," . $dblChargeAmount . "," . $intReadingID . ",'" . $strRemarks . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
				//echo $sqlquery;			
				//exit();
				$process=odbc_exec($sqlconnect, $sqlquery);
				$strMsg = "Record saved!";		
			}
			//echo $sqlquery;			
			$strMode = "RETRIEVE";
			$strSaveMode = "EDIT";
			break;
			//echo $strMode;
		case "DELETE":
			$i = 1;
			$j = 0;
			$k = 0;
			$strInvoiceNo = replacesinglequote($_POST["hidInvoiceNo"]);
			while ($i <= $_POST["hidRowCtr"]) {
				if (isset($_POST["chkDelete" . strval($i)])) {
					$strTenantCode = replacesinglequote($_POST["hidEditTenantCode" . strval($i)]);		
					$strChargeCode = replacesinglequote($_POST["hidEditChargeCode" . strval($i)]);		
					$intInvoiceDetailID = $_POST["hidEditInvoiceDetailID" . strval($i)];		
					$intInvoiceDetailReadingID = $_POST["hidEditInvoiceDetailReadingID" . strval($i)];		
					$intReadingID = $_POST["hidEditReadingID" . strval($i)];		
					$dblChargeAmount = 0;		
					$sqlquery="exec sp_t_InvoiceDetail_Delete 'DELETE','" . $strInvoiceNo . "'," . $intInvoiceDetailID . "," . $intInvoiceDetailReadingID . ",'" . $strTenantCode . "','" . $strChargeCode . "'," . $intReadingID . ",'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
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
			$pdf_link= $cn[7];
			
			//- Variables - for your RPT and PDF 
			//echo "Print Report Test"; 
			$my_report = $report_path . "t_invoice.rpt"; // 
			//echo $my_report;
			//die();
			//rpt source file 
			$pdf_file = "t_invoice" . str_replace("/","",date("m/d/y/H/i/s", time())) . ".pdf";
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
			$creport->ParameterFields(1)->AddCurrentValue ($_POST["hidInvoiceNo"]);
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
		$sqlquery="exec sp_t_Invoice_Retrieve 'RETRIEVE','" . $strInvoiceNo . "'";	
		//echo $sqlquery;
		$process=odbc_exec($sqlconnect, $sqlquery);
		if (odbc_fetch_row($process)) {
				$strInvoiceNo = replacedoublequotes(odbc_result($process,"invoice_no"));
				$strClientCode = replacedoublequotes(odbc_result($process,"client_code"));
				$strClientName = replacedoublequotes(odbc_result($process,"tenant_name"));
				$dblInvoiceAmount = odbc_result($process,"total_amount");
				$strStatus = replacedoublequotes(odbc_result($process,"status"));
				$strStatusDesc = replacedoublequotes(odbc_result($process,"status_desc"));
				$strMode = "RETRIEVE";
				$strSaveMode = "EDIT";
				
				$sqlquerycbo="exec sp_t_List_TenantsNotInInvoiceDetail '" . $strInvoiceNo . "'";
				$processcbo=odbc_exec($sqlconnect, $sqlquerycbo);
				while (odbc_fetch_row($processcbo)) {
					$cboTenant .= "<option value=\"" . trim(odbc_result($processcbo,"tenant_code")) . "\">" . trim(strtoupper(odbc_result($processcbo,"tenant_name"))) . "</option>";
				}
				//echo $sqlquerycbo;
				
				$sqlqueryCharges="exec sp_t_InvoiceDetail_Retrieve 'RETRIEVE','" . $strInvoiceNo . "'";	
				$processCharges=odbc_exec($sqlconnect, $sqlqueryCharges);
				//echo $sqlqueryCharges;
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
<title>INVOICE DETAIL</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
</head> 
<body style="margin:'0';background-color: #F3F5B4;">
<form name="frmInvoiceDtl" id="frmInvoiceDtl" method="post" action="invoice_detail.php?menu_id=<?php echo $menu_id;?>">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a href="#" onClick="javascript:change_loc('invoice_header.php?menu_id=<?php echo $menu_id;?>&mode=FIND&invoice_no=<?php echo $strInvoiceNo; ?>')"><u>INVOICE</u></a></li>	
			  <li class="li_nc"><a name="MODULE NAME">>>&nbsp;&nbsp;&nbsp;&nbsp;DETAIL
			  &nbsp;
			  <?php if ($strMenu != "DETAIL") { ?>
				  &nbsp;
			  <?php } ?>	
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
			  <li class="li_nc"><a href="#" onClick="javascript:change_loc('invoice_detail_list.php?menu_id=<?php echo $menu_id;?>')">Find&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  
			  <?php if ($strMode != "RETRIEVE") { ?>
			  		<li><a name="Print" style="color: #666666">Print&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } else { ?>	
			  		<li class="li_nc"><a href="#" onClick="javascript:cmdPrint_OnClick()">Print&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>			
			  <?php } ?>	
			  
			  <li class="li_nc"><a href="#" onClick="javascript:cmdCancel_OnClick()">Cancel&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>			  
			  <?php if ($strMenu == "DETAIL") { ?>
			  	<li class="li_nc"><a href="#" onClick="javascript:change_loc('invoice_header.php?menu_id=<?php echo $menu_id;?>&mode=FIND&invoice_no=<?php echo $strInvoiceNo; ?>')">Back&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
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
										<td class="fieldname">INVOICE NO. :<em class="requiredRed">*</em></td>
										<td width="20">&nbsp;</td>		
										<?php if ($strMode == "RETRIEVE" || $strMenu == "DETAIL") {?>								
											<td>
												<input type=text name="txtInvoiceNo" id="txtInvoiceNo" disabled class="values" size="20" value="<?php echo $strInvoiceNo;?>">									    
											</td>
										<?php } else {?>								
											<td>
												<input type=text name="txtInvoiceNo" id="txtInvoiceNo" class="values" size="20" value="<?php echo $strInvoiceNo;?>">									    
											</td>
										<?php }?>	
									   <input type="hidden" id="hidInvoiceNo" name="hidInvoiceNo" value="<?php echo $strInvoiceNo;?>">
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
					<table width="1100" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
						<tr height="30">
							<td width="3%" class="tablehdr" align="center">&nbsp;Del&nbsp;
							</td>
							<td width="31%" class="tablehdr" align="center">&nbsp;Tenant<em class="requiredYellow">*</em>&nbsp;
							</td>
							<td width="15%" class="tablehdr" align="center">&nbsp;Unit No.<em class="requiredYellow">*</em>&nbsp;
							</td>
							<td  width="17%" class="tablehdr" align="center">&nbsp;Charge<em class="requiredYellow">*</em>&nbsp;
							</td>
							<td  width="6%" class="tablehdr" align="center">&nbsp;Charge Type&nbsp;
							</td>
							<td  width="7%" class="tablehdr" align="center">Charge Amount<em class="requiredYellow">*</em>
							</td>
							<td  width="6%" class="tablehdr" align="center">&nbsp;Total Amount&nbsp;
							</td>
							<td  width="15%" class="tablehdr" align="center">&nbsp;Remarks&nbsp;
							</td>
						</tr>
						<?php
						$ctr = 0;
						while(odbc_fetch_row($processCharges)) {
							$invoice_detail_id = odbc_result($processCharges,"invoice_detail_id"); 
							$invoice_detail_reading_id = odbc_result($processCharges,"invoice_detail_reading_id"); 
							$reading_id = odbc_result($processCharges,"reading_id"); 
							$charge_code = replacedoublequotes(odbc_result($processCharges,"charge_code")); 
							$charge_desc = replacedoublequotes(strtoupper(odbc_result($processCharges,"charge_desc"))); 
							$charge_type_desc = replacedoublequotes(odbc_result($processCharges,"charge_type_desc")); 
							$tenant_name = replacedoublequotes(odbc_result($processCharges,"tenant_name")); 
							$unit_no = replacedoublequotes(odbc_result($processCharges,"unit_no")); 
							$tenant_code = odbc_result($processCharges,"tenant_code");
							$charge_type = odbc_result($processCharges,"charge_type");
							if (is_null(odbc_result($processCharges,"charge_amount")))
								$charge_amount = "0.00";
							else
								$charge_amount = odbc_result($processCharges,"charge_amount"); 
								
							if (is_null(odbc_result($processCharges,"total_charge_amount")))
								$total_charge_amount = "0.00";
							else
								$total_charge_amount = odbc_result($processCharges,"total_charge_amount"); 
							
							$remarks = odbc_result($processCharges,"remarks"); 
							
							$ctr = $ctr+1;
							
							if ($ctr%2==1) 
								$rowColor = "98fb98";	
							else
								$rowColor = "ffffe0";			
						?>
						<tr id="editRow<?php echo "$ctr";?>" name="editRow<?php echo "$ctr";?>" style="cursor:hand" bgcolor="<?php echo "$rowColor" ?>" onDblClick="javascript:editMode(<?php echo "$ctr";?>);">
							<td width="3%" align="center" style="border:1px">
								<input type="checkbox" name="chkDelete<?php echo "$ctr";?>" id="chkDelete<?php echo "$ctr";?>">
								<input type="hidden" id="hidEditInvoiceDetailID<?php echo "$ctr";?>" name="hidEditInvoiceDetailID<?php echo "$ctr";?>" value="<?php echo $invoice_detail_id;?>">
								<input type="hidden" id="hidEditInvoiceDetailReadingID<?php echo "$ctr";?>" name="hidEditInvoiceDetailReadingID<?php echo "$ctr";?>" value="<?php echo $invoice_detail_reading_id;?>">
								<input type="hidden" id="hidEditReadingID<?php echo "$ctr";?>" name="hidEditReadingID<?php echo "$ctr";?>" value="<?php echo $reading_id;?>">
							</td>
							<td width="31%" style="border:1px" class="values">
								&nbsp;<?php echo "$tenant_name";?>								
								<input type="hidden" id="hidEditTenantCode<?php echo "$ctr";?>" name="hidEditTenantCode<?php echo "$ctr";?>" value="<?php echo $tenant_code;?>">
								<input type="hidden" id="hidEditTenantName<?php echo "$ctr";?>" name="hidEditTenantName<?php echo "$ctr";?>" value="<?php echo $tenant_name;?>">
							</td>
							<td width="15%" style="border:1px" class="values">
								&nbsp;<?php echo "$unit_no";?>								
							</td>
							<td width="17%" style="border:1px" class="values">
								<span id="spCharge<?php echo "$ctr";?>" name="spCharge<?php echo "$ctr";?>" style="cursor:hand;visibility:'';display:''">&nbsp;<?php echo "$charge_desc";?>&nbsp;</span>
								<span id="spEditCharge<?php echo "$ctr";?>" name="spEditCharge<?php echo "$ctr";?>" style="visibility:hidden;display:none">&nbsp;
									<select name="cboEditCharge<?php echo "$ctr";?>" id="cboEditCharge<?php echo "$ctr";?>" class="values">
										<option selected value="<?php echo "$charge_code";?>"><?php echo "$charge_desc";?></option>					
										<option value="">- Select Charge -</option>										
										<?php echo $cbocharge; ?>
									</select>
								</span>
								<input type="hidden" id="hidEditChargeCode<?php echo "$ctr";?>" name="hidEditChargeCode<?php echo "$ctr";?>" value="<?php echo $charge_code;?>">
							</td>
							<td width="6%" style="border:1px" class="values">&nbsp;<?php echo "$charge_type_desc";?>&nbsp;
								<input type="hidden" id="hidEditChargeType<?php echo "$ctr";?>" name="hidEditChargeType<?php echo "$ctr";?>" value="<?php echo $charge_type;?>">
							</td>
							<td width="7%" style="border:1px" class="values" align="right">
								<span id="spChargeAmount<?php echo "$ctr";?>" name="spChargeAmount<?php echo "$ctr";?>" style="cursor:hand;visibility:'';display:''"><?php echo "$charge_amount";?>&nbsp;</span>
								<span id="spEditChargeAmount<?php echo "$ctr";?>" name="spEditChargeAmount<?php echo "$ctr";?>" style="visibility:hidden;display:none">
									<input type=text name="txtEditChargeAmount<?php echo "$ctr";?>" id="txtEditChargeAmount<?php echo "$ctr";?>" class="values" style="text-align:right" size="10" value="<?php echo "$charge_amount";?>">
								</span>
							</td>	
							<td width="6%" style="border:1px" class="values" align="right">
								<span id="spTotalChargeAmount<?php echo "$ctr";?>" name="spTotalChargeAmount<?php echo "$ctr";?>" style="cursor:hand;visibility:'';display:''"><?php echo "$total_charge_amount";?>&nbsp;</span>								
							</td>	
							<td width="15%" style="border:1px" class="values" align="left">
								<span id="spRemarks<?php echo "$ctr";?>" name="spRemarks<?php echo "$ctr";?>" style="cursor:hand;visibility:'';display:''">
									<?php if ($charge_type=="U") { ?>
										<img id="cmdFillupRemarks" name="cmdFillupRemarks" onClick="javascript:cmdFillupRemarks_OnClick(<?php echo "$ctr";?>);" src="images/page_edit.gif" style="cursor:hand" alt="Fill up Remarks">
									<?php } ?>
									<?php echo "$remarks";?>&nbsp;								
								</span>
								<span id="spEditRemarks<?php echo "$ctr";?>" name="spEditRemarks<?php echo "$ctr";?>" style="visibility:hidden;display:none">
									<?php echo "$remarks";?>&nbsp;										
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
							<td width="3%" align="center" style="border:1px" >&nbsp;
							</td>
							<td width="31%" style="border:1px" class="values">&nbsp;<b>TOTAL AMOUNT</b>
							</td>
							<td width="15%" style="border:1px" class="values">&nbsp;								
							</td>
							<td width="17%" style="border:1px" class="values">&nbsp;
							</td>
							<td width="6%" style="border:1px" class="values">&nbsp;
							</td>
							<td width="7%" style="border:1px" class="values">&nbsp;
							</td>
							<td width="6%" style="border:1px" class="values" align="right"><b>$<?php echo "$dblInvoiceAmount"; ?></b>&nbsp;						
							</td>
							<td width="15%" style="border:1px" class="values" align="left">&nbsp;								
							</td>
						</tr>
						<?php
								if (($ctr + 1)%2==1) 
									$rowColor = "ffffe0";	
								else
									$rowColor = "98fb98";								
						?>
						<tr id="addRow" name="addRow" bgcolor="<?php echo "$rowColor"; ?>">
							<td width="3%" align="center" style="border:1px" >&nbsp;
							</td>
							<td width="31%" style="border:1px" class="values">&nbsp;
								<select name="cboAddTenant" id="cboAddTenant" class="values">
									<option value="">- Select Tenant -</option>
									<?php echo $cboTenant; ?>
								</select>
								<input type="hidden" id="hidAddTenantCode" name="hidAddTenantCode">
								<input type="hidden" id="hidAddTenantName" name="hidAddTenantName">
							</td>
							<td width="15%" style="border:1px" class="values">&nbsp;
							</td>
							<td width="17%" style="border:1px" class="values">&nbsp;
								<select name="cboAddCharge" id="cboAddCharge" class="values">
									<option value="">- Select Charge -</option>
									<?php echo $cbocharge; ?>
								</select>
							</td>
							<td width="6%" style="border:1px" class="values">&nbsp;
							</td>
							<td width="7%" style="border:1px" class="values" align="center">
								<input type=text name="txtAddChargeAmount" id="txtAddChargeAmount" style="text-align:right" class="values" size="10" value="0.00">
							</td>
							<td width="6%" style="border:1px" class="values" align="left">&nbsp;								
							</td>
							<td width="15%" style="border:1px" class="values" align="left">&nbsp;								
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
	if (frmInvoiceDtl.hidSaveMode.value != "EDIT_CHARGE") {
		frmInvoiceDtl.hidCurRow.value = ctr;
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
		frmInvoiceDtl.chkSelectAll.disabled = true;
		frmInvoiceDtl.chkSelectAll.checked = false;
		frmInvoiceDtl.cboAddCharge.disabled = true;
		frmInvoiceDtl.txtAddChargeAmount.disabled = true;
		frmInvoiceDtl.hidSaveMode.value = "EDIT_CHARGE";
	}
}

function enabledisablechkboxes(pVal) {
	var ctr
	ctr = frmInvoiceDtl.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmInvoiceDtl.chkDelete" + i);		
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
	window.open ("invoice_detail_search_tenant.php?menu_id=" + frmInvoiceDtl.hidMenuID.value + "&invoice_no=" + frmInvoiceDtl.hidInvoiceNo.value +"&ctr="+ctr,"displayWindow","type=fullwindow,titlebar=no,scrollbars=yes");
	return false;
}


function cmdFillupRemarks_OnClick(ctr) {
	frmInvoiceDtl.hidCurRow.value = ctr;
	obj = eval("frmInvoiceDtl.hidEditChargeCode" + ctr);
	frmInvoiceDtl.hidChargeCode.value = obj.value;
	obj = eval("frmInvoiceDtl.hidEditInvoiceDetailID" + ctr);
	frmInvoiceDtl.hidInvoiceDetailID.value = obj.value;
	obj = eval("frmInvoiceDtl.hidEditInvoiceDetailReadingID" + ctr);
	frmInvoiceDtl.hidInvoiceDetailReadingID.value = obj.value;
	obj = eval("frmInvoiceDtl.hidEditReadingID" + ctr);
	frmInvoiceDtl.hidReadingID.value = obj.value;
	parent.frames[2].location = "invoice_detail_reading.php?menu_id=" + frmInvoiceDtl.hidMenuID.value + "&invoice_no=" + frmInvoiceDtl.hidInvoiceNo.value + "&charge_code=" + frmInvoiceDtl.hidChargeCode.value + "&invoice_detail_id=" + frmInvoiceDtl.hidInvoiceDetailID.value+ "&invoice_detail_reading_id=" + frmInvoiceDtl.hidInvoiceDetailReadingID.value+ "&reading_id=" + frmInvoiceDtl.hidReadingID.value;
	return false;
}

function cmdSave_OnClick() {
	if (frmInvoiceDtl.hidInvoiceNo.value == "") {
		alert("Select Invoice first!")
		return false
	}
	
	if (frmInvoiceDtl.hidSaveMode.value == "EDIT_CHARGE") {
		ctr = frmInvoiceDtl.hidCurRow.value;

		obj = eval("frmInvoiceDtl.hidEditTenantCode" + ctr);
		if (obj.value == "") {
			alert("Tenant is required")
			return false
		}
		
		frmInvoiceDtl.hidTenantCode.value = obj.value;
		
		obj = eval("frmInvoiceDtl.cboEditCharge" + ctr);
		if (obj.value == "") {
			alert("Charge is required")
			obj.focus()
			return false
		}
		frmInvoiceDtl.hidChargeCode.value = obj.value;
		
		obj = eval("frmInvoiceDtl.hidEditInvoiceDetailID" + ctr);
		frmInvoiceDtl.hidInvoiceDetailID.value = obj.value;
		
		obj = eval("frmInvoiceDtl.hidEditInvoiceDetailReadingID" + ctr);
		frmInvoiceDtl.hidInvoiceDetailReadingID.value = obj.value;
				
		obj = eval("frmInvoiceDtl.hidEditReadingID" + ctr);
		frmInvoiceDtl.hidReadingID.value = obj.value;
		
		obj = eval("frmInvoiceDtl.txtEditChargeAmount" + ctr);
		if (isNaN(obj.value) && obj.value != "") {
			alert("Invalid numeric value");
			obj.focus();
			return false;
		}
		obj = eval("frmInvoiceDtl.txtEditChargeAmount" + ctr);
		if (obj.value == "" || obj.value == 0) {
			alert("Charge Amount is required")
			obj.focus()
			return false
		}
		frmInvoiceDtl.hidChargeAmount.value = obj.value;
		frmInvoiceDtl.hidSaveMode.value = "EDIT_CHARGE";
	}
	else {
		if (frmInvoiceDtl.cboAddTenant.value == "") {
			alert("Tenant is required")
			frmInvoiceDtl.cboAddTenant.focus()
			return false
		}
		if (frmInvoiceDtl.cboAddCharge.value == "") {
			alert("Charge is required")
			frmInvoiceDtl.cboAddCharge.focus()
			return false
		}
		if (isNaN(frmInvoiceDtl.txtAddChargeAmount.value) && frmInvoiceDtl.txtAddChargeAmount.value != "") {
			alert("Invalid numeric value")
			frmInvoiceDtl.txtAddChargeAmount.focus()
			return false
		}
		if (frmInvoiceDtl.txtAddChargeAmount.value=="" || frmInvoiceDtl.txtAddChargeAmount.value == 0) {
			alert("Charge Amount is required")
			frmInvoiceDtl.txtAddChargeAmount.focus()
			return false
		}
		frmInvoiceDtl.hidTenantCode.value = frmInvoiceDtl.cboAddTenant.value;
		frmInvoiceDtl.hidChargeCode.value = frmInvoiceDtl.cboAddCharge.value;
		frmInvoiceDtl.hidChargeAmount.value = frmInvoiceDtl.txtAddChargeAmount.value;
		frmInvoiceDtl.hidInvoiceDetailID.value = 0;
		frmInvoiceDtl.hidInvoiceDetailReadingID.value = 0;
		frmInvoiceDtl.hidReadingID.value = 0;
		frmInvoiceDtl.hidSaveMode.value = "EDIT";
	}

	frmInvoiceDtl.hidMode.value = "SAVE";
	frmInvoiceDtl.submit();
}

function cmdRetrieve_OnClick() {
	frmInvoiceDtl.hidMode.value = "RETRIEVE";
	frmInvoiceDtl.submit();
}

function cmdPrint_OnClick() {
	frmInvoiceDtl.hidInvoiceNo.value = frmInvoiceDtl.txtInvoiceNo.value;
	frmInvoiceDtl.hidMode.value = "PRINT";
	frmInvoiceDtl.submit();
}

function cmdCancel_OnClick() {
	ctr = frmInvoiceDtl.hidCurRow.value;
	//alert(frmInvoiceDtl.hidSaveMode.value)
	if (frmInvoiceDtl.hidSaveMode.value == "EDIT_CHARGE") {
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
		
		frmInvoiceDtl.cboAddCharge.disabled = false;
		frmInvoiceDtl.txtAddChargeAmount.disabled = false;
		frmInvoiceDtl.chkSelectAll.disabled = false;
		frmInvoiceDtl.chkSelectAll.checked = false;
		frmInvoiceDtl.hidSaveMode.value = "";
		enabledisablechkboxes(1);
		return false;
	}
	else {
		parent.frames[2].location = "invoice_detail.php?menu_id=" + frmInvoiceDtl.hidMenuID.value;
		return false;
	}
}

function cmdDelete_OnClick() {
	var j
	j=0
	totalctr = frmInvoiceDtl.hidRowCtr.value;
	for (i=1;i<=totalctr;i++) {
		obj = eval("frmInvoiceDtl.chkDelete" + i);
		if (obj.checked == true) {
			j++;
		}
	}
	if (j > 0) {
		if (confirm("Are you sure you want to delete this record/s?")) {
			frmInvoiceDtl.hidMode.value = "DELETE";
			frmInvoiceDtl.submit();
		}
	}
	else {
		alert("Deleting is not allowed this time");
	}
}

function chkSelectAll_OnClick() {
	var ctr
	ctr = frmInvoiceDtl.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmInvoiceDtl.chkDelete" + i);
		if (frmInvoiceDtl.chkSelectAll.checked == true) {
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

