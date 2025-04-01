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
$strORNo = "";
$strStatusDesc = "";
$dblORAmountHeader = "0.00";
$dblTotalORAmount = 0;
$intPaymentDetailID = 0;
$strInvoiceNo = "";
$intInvoiceDetailID = 0;
$intInvoiceDetailChargesID = 0;		
$dblTotalChargeAmount = 0;	
$dblORAmount = 0;	
$intTotalDetailCnt = 0;
$strMsg = "";
$strMenu = "";

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
		$strMenu =  $_POST["hidMenu"];
		$strORNo = $_POST["txtORNo"];
	}
		
	switch ($strMode) {
		
		case "SAVE":			
			$strORNo = $_POST["hidORNo"];
			$strClientCode = $_POST["hidClientCode"];
			$strClientName = $_POST["hidClientName"];
			$i = 1;
			while ($i <= $_POST["hidRowCtr"]) {			
				$intPaymentDetailID = $_POST["hidPaymentDetailID" . strval($i)];							
				$strInvoiceNo = $_POST["hidInvoiceNo" . strval($i)];							
				$intInvoiceDetailID = $_POST["hidInvoiceDetailID" . strval($i)];		
				$intInvoiceDetailChargesID = $_POST["hidInvoiceDetailChargesID" . strval($i)];		
				$dblORAmount = $_POST["txtORAmount" . strval($i)];		
				
				if ($_POST["hidTransType" . strval($i)] == "U") {
					$sqlquery="exec sp_t_Payment_Detail 'SAVE'," . $intPaymentDetailID . ",'" . $strORNo . "','" . $strInvoiceNo . "'," . $intInvoiceDetailID . ",0," . $dblORAmount . ",'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					$process=odbc_exec($sqlconnect, $sqlquery);				
				}
				elseif ($_POST["hidTransType" . strval($i)] == "A") {
					//echo $_POST["hidTransType" . strval($i)];
					$sqlquery="exec sp_t_Payment_AdvancePayment_Save 'SAVE','" . $strORNo . "',0," . $dblORAmount . ",'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					$process=odbc_exec($sqlconnect, $sqlquery);									
				}
				elseif ($_POST["hidTransType" . strval($i)] == "S") {
					//echo $_POST["hidTransType" . strval($i)];
					$sqlquery="exec sp_t_Payment_SecurityDeposit_Save 'SAVE','" . $strORNo . "'," . $intPaymentDetailID . ",'" . $_POST["hidRealPropertyCode" . strval($i)] . "','" . $_POST["hidBuildingCode" . strval($i)] . "','" . $_POST["hidUnitNo" . strval($i)] . "'," . $dblORAmount . ",'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					$process=odbc_exec($sqlconnect, $sqlquery);				
					//echo $sqlquery;				
				}
				
				//echo $sqlquery;				
				$i++;
			}
			if ($_POST["cboAddCharge"] == "A"  && $_POST["txtAddORAmount"] != "" && $_POST["txtAddORAmount"] != 0) {
				$sqlquery="exec sp_t_Payment_AdvancePayment_Save 'SAVE','" . $strORNo . "',0," . $_POST["txtAddORAmount"] . ",'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
				$process=odbc_exec($sqlconnect, $sqlquery);				
				//echo $sqlquery;
			}
			elseif ($_POST["cboAddCharge"] == "S"  && $_POST["txtAddORAmount"] != "" && $_POST["txtAddORAmount"] != 0) {
				$arrUnit = explode("***",$_POST["cboAddUnit"]);
				$tmp_real_property_code = $arrUnit[0];
				$tmp_building_code = $arrUnit[1];
				$tmp_unit_no = $arrUnit[2];
				$sqlquery="exec sp_t_Payment_SecurityDeposit_Save 'SAVE','" . $strORNo . "',0,'" . $tmp_real_property_code . "','" . $tmp_building_code . "','" . $tmp_unit_no . "'," . $_POST["txtAddORAmount"] . ",'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
				$process=odbc_exec($sqlconnect, $sqlquery);				
				//echo $sqlquery;
			}
			$strMsg = "Record saved!";
			$strMode = "RETRIEVE";
			$strSaveMode = "EDIT";
			break;
			
		case "DELETE":			
			$strORNo = $_POST["hidORNo"];
			$strClientCode = $_POST["hidClientCode"];
			$strClientName = $_POST["hidClientName"];
			$i = 1;
			while ($i <= $_POST["hidRowCtr"]) {			
				if (isset($_POST["chkDelete" . strval($i)])) {
					$intPaymentDetailID = $_POST["hidPaymentDetailID" . strval($i)];							
					$strInvoiceNo = $_POST["hidInvoiceNo" . strval($i)];							
					$intInvoiceDetailID = $_POST["hidInvoiceDetailID" . strval($i)];		
					$intInvoiceDetailChargesID = $_POST["hidInvoiceDetailChargesID" . strval($i)];		
					$dblORAmount = $_POST["txtORAmount" . strval($i)];		
					$strTransType = $_POST["hidTransType" . strval($i)];
					
					if ($strTransType == "U") {
						$sqlquery="exec sp_t_Payment_Detail_Delete 'DELETE'," . $intPaymentDetailID . ",'" . $strORNo . "','" . $strInvoiceNo . "'," . $intInvoiceDetailID . ",0," . $dblORAmount . ",'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";							
					}
					elseif ($strTransType == "A") {
						$sqlquery="exec sp_t_Payment_AdvancePayment_Delete 'DELETE','" . $strORNo . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";													
					}		
					elseif ($strTransType == "S") {
						$sqlquery="exec sp_t_Payment_SecurityDeposit_Delete 'DELETE','" . $strORNo . "'," . $intPaymentDetailID  . ",'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";													
					}		
					if ($sqlquery!="") {
						$process=odbc_exec($sqlconnect, $sqlquery);				
						if (odbc_fetch_row($process)) {
							if (odbc_result($process,"x") == 1) 
								$j++;
							else
								$k++;
						}			
					}
				}				
				$i++;
			}
			if ($j > 0 && $k > 0) {
				$strMsg = "Some records were not deleted. It might be tagged already in other modules.";
			}
			elseif ($j == 0 && $k > 0) {
				$strMsg = "Record/s deleted!";
			}
			elseif ($j > 0 && $k == 0) {
				$strMsg = "No record deleted!  It might be tagged already in other modules.";
			}
			else
				$strMsg = "No record deleted!";	
			$strMode = "RETRIEVE";
			$strSaveMode = "EDIT";
			break;
		}
			
		if ($strMode == "RETRIEVE" || $strMode =="FIND") {
			$sqlquery="exec sp_t_Payment_Detail_Retrieve_Header 'RETRIEVE','" . $strORNo . "'";	
			//echo $sqlquery;
			$process=odbc_exec($sqlconnect, $sqlquery);
			if (odbc_fetch_row($process)) {
					$strORNo = replacedoublequotes(odbc_result($process,"or_no"));
					$strClientCode = replacedoublequotes(odbc_result($process,"client_code"));
					$strClientName = replacedoublequotes(odbc_result($process,"client_name"));
					$dblORAmountHeader = odbc_result($process,"or_amount_header");
					$dblTotalORAmount = odbc_result($process,"total_or_amount");
					$intTotalDetailCnt = odbc_result($process,"payment_detail_total_rec");
					$strStatus = odbc_result($process,"status");
					$strStatusDesc = odbc_result($process,"status_desc");
					$strMode = "RETRIEVE";
					$strSaveMode = "EDIT";
										
					//$sqlquerycbo="select distinct upper(ltrim(rtrim(tenant_name)) + ' (' + ltrim(rtrim(real_property_code)) + '/' + ltrim(rtrim(building_code)) + '/' + ltrim(rtrim(unit_no)) + ')') as tenant_name,ltrim(rtrim(real_property_code)) as real_property_code,ltrim(rtrim(building_code)) as building_code,ltrim(rtrim(unit_no)) as unit_no from m_tenant where (tenant_type = 'O' or tenant_type='OC') and bill_to = '" . $strClientCode . "' order by real_property_code,building_code,unit_no";
					$sqlquerycbo = "exec sp_t_List_UnitsNotInPaymentDetail '" . $strORNo . "'";		
					$processcbo=odbc_exec($sqlconnect, $sqlquerycbo);
					//echo $sqlquerycbo;
					while (odbc_fetch_row($processcbo)) {
						$cboUnit .= "<option value=\"" . trim(odbc_result($processcbo,"real_property_code")) . "***" . trim(odbc_result($processcbo,"building_code")) . "***" . trim(odbc_result($processcbo,"unit_no")) . "\">" . trim(strtoupper(odbc_result($processcbo,"real_property_code"))) . "/" . trim(strtoupper(odbc_result($processcbo,"building_code"))) . "/" . trim(strtoupper(odbc_result($processcbo,"unit_no"))) . "</option>";
					}

					$sqlqueryCharges="exec sp_t_Payment_Detail_Retrieve 'RETRIEVE','" . $strORNo . "'";	
					$processCharges=odbc_exec($sqlconnect, $sqlqueryCharges);
					//echo $sqlqueryCharges;
			}
			else {
				$strMode = "";
			}
		}
	
}

//echo $sqlquerycbo;
if ($strMsg != ""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 

?>
<html> 
<head> 
<title>PAYMENT DETAIL</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
</head> 
<body style="margin:'0';background-color: #F3F5B4;">
<form name="frmPaymentDtl" id="frmPaymentDtl" method="post" action="payment_detail.php?menu_id=<?php echo $menu_id;?>">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a href="#" onClick="javascript:change_loc('payment_header.php?menu_id=<?php echo $menu_id;?>&mode=FIND&or_no=<?php echo $strORNo; ?>')"><u>PAYMENT</u></a></li>	
			  <li class="li_nc"><a name="MODULE NAME">>>&nbsp;&nbsp;&nbsp;&nbsp;DETAIL
			  &nbsp;
		   	  	<?php if ($strMenu != "DETAIL") { ?>
					&nbsp;&nbsp;&nbsp;&nbsp;
				 <?php } ?>
			 </a></li>	
			 <?php if ($strMode != "RETRIEVE") { ?>
					<li class="li_nc"><a href="#" onClick="javascript:cmdRetrieve_OnClick()">|&nbsp;&nbsp;&nbsp;Retrieve&nbsp;&nbsp;&nbsp;|</a></li>			  
			 <?php } else { ?>		
			 		<li><a name="Retrieve" style="color: #666666">|&nbsp;&nbsp;&nbsp;Retrieve&nbsp;&nbsp;&nbsp;|</a></li>					
			  <?php } ?>
			 
			  <?php if ($strMode != "RETRIEVE" || $strStatus != "") { ?>
			  		<li><a name="ADD" style="color: #666666">Add Entries&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  		<li><a name="Save" style="color: #666666">Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
					<li><a name="Delete" style="color: #666666">Delete&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } else { ?>	
			  		<li class="li_nc"><a href="#" onClick="javascript:change_loc('payment_detail_add_entries.php?menu_id=<?php echo $menu_id;?>&mode=FIND&menu=DETAIL&or_no=<?php echo $strORNo;?>&client_code=<?php echo $strClientCode;?>&client_name=<?php echo $strClientName;?>')">Add Entries&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>			  		 
					<li class="li_nc"><a href="#" onClick="javascript:cmdSave_OnClick()">Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
					<li class="li_nc"><a href="#" onClick="javascript:cmdDelete_OnClick()">Delete&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } ?>	
			  <li class="li_nc"><a href="#" onClick="javascript:change_loc('payment_detail_list.php?menu_id=<?php echo $menu_id;?>')">Find&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <li class="li_nc"><a href="#" onClick="javascript:cmdCancel_OnClick()">Cancel&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>			  
			  <?php if ($strMenu == "DETAIL") { ?>
			  	<li class="li_nc"><a href="#" onClick="javascript:change_loc('payment_header.php?menu_id=<?php echo $menu_id;?>&mode=FIND&or_no=<?php echo $strORNo; ?>')">Back&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
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
										<td width="20">&nbsp;</td>		
										<?php if ($strMode == "RETRIEVE") {?>								
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
									<?php if ($strStatusDesc != "") { ?>
										<tr>
											<td class="fieldname">STATUS :</td>
											<td width="20">&nbsp;</td>			
											<td class="values" style="color:red"><b><?php echo $strStatusDesc;?></b>
										</tr>					
									<?php } ?>														
								</table>
							</td>
						</tr>
					</table>
					<p></p>
					<table width="900" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
						<tr height="30">
							<td width="4%" class="tablehdr" align="center">&nbsp;Sel&nbsp;
							</td>
							<td width="14%" class="tablehdr" align="center">&nbsp;Invoice No.&nbsp;
							</td>
							<td  width="9%" class="tablehdr" align="center">&nbsp;Date&nbsp;
							</td>
							<td  width="22%" class="tablehdr" align="center">&nbsp;Tenant&nbsp;
							</td>
							<td  width="19%" class="tablehdr" align="center">&nbsp;Charge&nbsp;
							</td>
							<td  width="9%" class="tablehdr" align="center">&nbsp;OR Amount&nbsp;
							</td>
							<td width="7%" class="tablehdr" align="center">Balance Amount
							</td>			
							<td width="8%" class="tablehdr" align="center">Paid Amount
							</td>	
							<td width="8%" class="tablehdr" align="center">Total Amount
							</td>	
						</tr>
						<?php
						$ctr = 0;
						while(odbc_fetch_row($processCharges)) {
							$strTransType = odbc_result($processCharges,"trans_type");
							$intPaymentDetailID = odbc_result($processCharges,"payment_detail_id");
							$strInvoiceNo = odbc_result($processCharges,"invoice_no");
							$dtInvoice = odbc_result($processCharges,"invoice_date");
							$intInvoiceDetailID = odbc_result($processCharges,"invoice_detail_id");
							$intInvoiceDetailChargesID = odbc_result($processCharges,"invoice_detail_charges_id");
							$strTenantCode = replacedoublequotes(odbc_result($processCharges,"tenant_code"));
							$strTenantName = replacedoublequotes(odbc_result($processCharges,"tenant_name"));
							$strChargeCode = replacedoublequotes(odbc_result($processCharges,"charge_code"));
							$strChargeDesc = replacedoublequotes(odbc_result($processCharges,"charge_desc"));
							
							$real_property_code = odbc_result($processCharges,"real_property_code");
							$building_code = odbc_result($processCharges,"building_code");
							$unit_no = odbc_result($processCharges,"unit_no");
							
							$dblORAmount = odbc_result($processCharges,"or_amount");
							$dblTotalChargeAmount = odbc_result($processCharges,"total_charge_amount");
							$dblPaidAmount = odbc_result($processCharges,"paid_amount");
							$dblBalanceAmount = odbc_result($processCharges,"balance_amount");
							if ($dblBalanceAmount == 0) 
								$dblBalanceAmount = ".00";
							$ctr = $ctr+1;
							
							if ($ctr%2==1) 
								$rowColor = "98fb98";	
							else
								$rowColor = "ffffe0";			
						?>
						<tr id="editRow<?php echo "$ctr";?>" name="editRow<?php echo "$ctr";?>" style="cursor:hand" bgcolor="<?php echo "$rowColor" ?>">
							<td width="4%" align="center" style="border:1px">
								<input type="checkbox" name="chkDelete<?php echo "$ctr";?>" id="chkDelete<?php echo "$ctr";?>">
								<input type="hidden" id="hidEditRecID<?php echo "$ctr";?>" name="hidEditRecID<?php echo "$ctr";?>" value="<?php echo $rec_id;?>">
							</td>
							<td width="14%" style="border:1px" class="values" align="left">
								&nbsp;<?php echo $strInvoiceNo;?>
								<input type="hidden" name="hidTransType<?php echo "$ctr";?>" id="hidTransType<?php echo "$ctr";?>" value="<?php echo "$strTransType";?>">						
								<input type="hidden" name="hidPaymentDetailID<?php echo "$ctr";?>" id="hidPaymentDetailID<?php echo "$ctr";?>" value=<?php echo "$intPaymentDetailID";?>>						
								<input type="hidden" name="hidInvoiceNo<?php echo "$ctr";?>" id="hidInvoiceNo<?php echo "$ctr";?>" value="<?php echo "$strInvoiceNo";?>">						
								<input type="hidden" name="hidInvoiceDetailID<?php echo "$ctr";?>" id="hidInvoiceDetailID<?php echo "$ctr";?>" value="<?php echo "$intInvoiceDetailID";?>">						
								<input type="hidden" name="hidInvoiceDetailChargesID<?php echo "$ctr";?>" id="hidInvoiceDetailChargesID<?php echo "$ctr";?>" value="<?php echo "$intInvoiceDetailChargesID";?>">						
							</td>
							<td width="9%" style="border:1px" class="values" align="center">
								<?php echo $dtInvoice;?>
							</td>
							<td width="22%" style="border:1px" class="values">&nbsp;<?php echo $strTenantName;?>
							</td>
							<td width="19%" style="border:1px" class="values" align="left">
								&nbsp;<?php echo $strChargeDesc;?>
							</td>	
							<?php if ($strStatus != "") {?>
								<td width="9%" style="border:1px" class="values" align="center">								
									<?php echo $dblORAmount;?>
								</td>	
							<?php } else {?>
								<td width="9%" style="border:1px" class="values" align="center">								
									<input type="text" name="txtORAmount<?php echo $ctr;?>" id="txtORAmount<?php echo $ctr;?>" class="values" style="text-align:right" size="6" maxlength="11" value="<?php echo $dblORAmount;?>">
								</td>	
							<?php } ?>
							<td width="7%" style="border:1px" class="values" align="right">
								<?php echo $dblBalanceAmount;?>&nbsp;
								<input type="hidden" name="hidBalanceAmount<?php echo $ctr;?>" id="hidBalanceAmount<?php echo $ctr;?>" value=<?php echo $dblBalanceAmount;?>>
							</td>		
							<td width="8%" style="border:1px" class="values" align="right">
								<?php echo $dblPaidAmount;?>&nbsp;								
							</td>	
							<td width="8%" style="border:1px" class="values" align="right">
								<?php echo $dblTotalChargeAmount;?>&nbsp;
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
									
							if ($ctr > 0) {
						?>
						
						<tr id="" name="" bgcolor="<?php echo "$rowColor"; ?>">
							<td width="4%" align="center" style="border:1px" >&nbsp;
							</td>
							<td width="14%" style="border:1px" class="values">&nbsp;<b>TOTAL AMOUNT</b>
							</td>
							<td width="9%" style="border:1px" class="values">&nbsp;								
							</td>
							<td width="22%" style="border:1px" class="values">&nbsp;
							</td>
							<td width="19%" style="border:1px" class="values">&nbsp;
							</td>
							<td width="9%" style="border:1px" class="values" align="right"><b>$<?php echo "$dblTotalORAmount"; ?></b>&nbsp;&nbsp;					
							</td>
							<td width="7%" style="border:1px" class="values" align="left">&nbsp;								
							</td>
							<td width="8%" style="border:1px" class="values" align="left">&nbsp;								
							</td>
							<td width="8%" style="border:1px" class="values" align="left">&nbsp;								
							</td>
						</tr>
						<?php
							}
								if (($ctr + 1)%2==1) 
									$rowColor = "ffffe0";	
								else
									$rowColor = "98fb98";								
						?>	
						<tr id="AddRow" name="AddRow" style="cursor:hand" bgcolor="<?php echo "$rowColor" ?>">
							<td width="4%" align="center" style="border:1px">&nbsp;
								
							</td>
							<td width="14%" style="border:1px" class="values" align="left">&nbsp;
								
							</td>
							<td width="9%" style="border:1px" class="values" align="center">&nbsp;
								
							</td>
							<td width="22%" style="border:1px" class="values">
								&nbsp;
								<select name="cboAddUnit" id="cboAddUnit" class="values">
									<option value="">- Select Unit -</option>
									<?php echo $cboUnit;?>
								</select>
								<input type="hidden" name="hidRealPropertyCode<?php echo $ctr;?>" id="hidRealPropertyCode<?php echo $ctr;?>" value=<?php echo $real_property_code;?>>
								<input type="hidden" name="hidBuildingCode<?php echo $ctr;?>" id="hidBuildingCode<?php echo $ctr;?>" value=<?php echo $building_code;?>>
								<input type="hidden" name="hidUnitNo<?php echo $ctr;?>" id="hidUnitNo<?php echo $ctr;?>" value=<?php echo $unit_no;?>>
							</td>
							<td width="19%" style="border:1px" class="values" align="left">
								&nbsp;
								<select name="cboAddCharge" id="cboAddCharge" class="values">
									<option value="A">ADVANCE PAYMENT</option>
									<option value="S">SECURITY DEPOSIT</option>
								</select>
							</td>								
							<td width="9%" style="border:1px" class="values" align="center">								
								<input type="text" name="txtAddORAmount" id="txtAddORAmount" class="values" style="text-align:right" size="6" maxlength="11" value="0.00">
							</td>	
							<td width="7%" style="border:1px" class="values" align="right">&nbsp;
								
							</td>		
							<td width="8%" style="border:1px" class="values" align="right">&nbsp;
								
							</td>	
							<td width="8%" style="border:1px" class="values" align="right">&nbsp;
								
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
	<input type="hidden" id="hidMenu" name="hidMenu" value="<?php echo $strMenu; ?>">
	<input type="hidden" id="hidChargeCode" name="hidChargeCode">
	<input type="hidden" id="hidRecID" name="hidRecID">
	<input type="hidden" id="hidChargeAmount" name="hidChargeAmount">
	<input type="hidden" id="hidMenuID" name="hidMenuID" value=<?php echo $menu_id;?>>
</form>
</body> 
</html>

<script type="text/javascript">

function enabledisablechkboxes(pVal) {
	var ctr
	ctr = frmPaymentDtl.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmPaymentDtl.chkDelete" + i);		
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

function cmdSave_OnClick() {
	if (frmPaymentDtl.hidORNo.value == "") {
		alert("Select OR first!")
		return false
	}
	
	var j
	var dblTotal
	j=0
	dblTotal = 0
	totalctr = frmPaymentDtl.hidRowCtr.value;
	for (i=1;i<=totalctr;i++) {
		obj = eval("frmPaymentDtl.txtORAmount" + i);
		if (obj.value == "" || obj.value == 0) {
			alert("OR Amount is required")
			obj.focus()
			return false
		}
		if (isNaN(obj.value)) {
			alert("Invalid numeric value")
			obj.focus()
			return false;
		}
		obj1 = eval("frmPaymentDtl.hidBalanceAmount" + i);
		obj2 = eval("frmPaymentDtl.hidTransType" + i);
		if ((Number(obj.value)>Number(obj1.value)) && obj2.value=="U") {
			alert("OR Amount should not be greater than the Balance Amount")
			obj.focus()
			return false;
		}
		dblTotal = dblTotal + Number(obj.value);
	}
	
	if (frmPaymentDtl.hidRowCtr.value == 0 && (frmPaymentDtl.txtAddORAmount.value == "" || frmPaymentDtl.txtAddORAmount.value == 0)) {
			alert("OR Amount is required")
			frmPaymentDtl.txtAddORAmount.focus()
			return false;
	}
	
	if (frmPaymentDtl.txtAddORAmount.value != "" && frmPaymentDtl.txtAddORAmount.value != 0) {
		if (frmPaymentDtl.cboAddCharge.value == "S") {
			if (frmPaymentDtl.cboAddUnit.value == "") {
				alert("Unit is required for Security Deposit payment")
				frmPaymentDtl.cboAddUnit.focus()
				return false;
			}
		}
		
		if (isNaN(frmPaymentDtl.txtAddORAmount.value)) {
			alert("Invalid numeric value in Advance Payment/Security Deposit")
			frmPaymentDtl.txtAddORAmount.focus()
			return false;
		}
	}
	
	frmPaymentDtl.hidMode.value = "SAVE";
	frmPaymentDtl.submit();
}

function cmdRetrieve_OnClick() {
	frmPaymentDtl.hidMode.value = "RETRIEVE";
	frmPaymentDtl.submit();
}

function cmdCancel_OnClick() {	
	parent.frames[2].location = "payment_detail.php?menu_id=" + frmPaymentDtl.hidMenuID.value;
	return false;
}

function cmdDelete_OnClick() {
	var j
	j=0
	totalctr = frmPaymentDtl.hidRowCtr.value;
	for (i=1;i<=totalctr;i++) {
		obj = eval("frmPaymentDtl.chkDelete" + i);
		if (obj.checked == true) {
			j++;
		}
	}
	if (j > 0) {
		if (confirm("Are you sure you want to delete this record/s?")) {
			frmPaymentDtl.hidMode.value = "DELETE";
			frmPaymentDtl.submit();
		}
	}
	else {
		alert("Select record/s to delete");
	}
}

function chkSelectAll_OnClick() {
	var ctr
	ctr = frmPaymentDtl.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmPaymentDtl.chkDelete" + i);
		if (frmPaymentDtl.chkSelectAll.checked == true) {
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

