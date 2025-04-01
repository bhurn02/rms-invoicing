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

$strReadingID = "";
$intReadingID = 0;
$strTenantCode = "";
$strTenantName = "";
$strChargeCode = "";
$strChargeDesc = "";
$dtReadingFrom = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
$dtReadingTo = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
$dblPrevReading = 0;
$dblCurrentReading = 0;
$strRemarks = "";	
$dtBillingFrom = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
$dtBillingTo = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
$strInvoiceNo = "";	

$strMsg = "";
$strSaveMode = "";
$strMode = $_POST["hidMode"];
$strSaveMode = $_POST["hidSaveMode"];

if ($_GET["mode"] == "FIND") {
	$strReadingID = $_GET["reading_id"];
	$intReadingID = $_GET["reading_id"];
	$strMode = "FIND";
}

if ($strMode != "") {
	if ($strMode != "FIND") {
		$strReadingID = replacesinglequote($_POST["hidReadingID"]);
		$intReadingID = replacesinglequote($_POST["hidReadingID"]);
	}
	$strTenantCode = replacesinglequote($_POST["hidTenantCode"]);
	$strTenantName = replacesinglequote($_POST["hidTenantName"]);
	$strChargeCode = replacesinglequote($_POST["hidChargeCode"]);
	$strChargeDesc = replacesinglequote($_POST["hidChargeDesc"]);
	$dtReadingFrom = $_POST["DPC_txtReadingFrom"];
	$dtReadingTo = $_POST["DPC_txtReadingTo"];
	$dblPrevReading = $_POST["txtPrevReading"];
	$dblCurrentReading = $_POST["txtCurrentReading"];
	$dtBillingFrom = $_POST["DPC_txtBillingFrom"];
	$dtBillingTo = $_POST["DPC_txtBillingTo"];
	$strRemarks = replacesinglequote($_POST["txtRemarks"]);		
	$uid = $sessUserID;
	$company_code = $sessCompanyCode;
	
	//echo $strMode;
	switch ($strMode) {
		case "SAVE":
			if ($strSaveMode != "EDIT") {
				$intReadingID = 0;
				$sqlquery="exec sp_t_TenantReading 'FIND'," . $intReadingID . ",'" . $strTenantCode . "','" . $strChargeCode . "','" . $dtReadingFrom . "','" . $dtReadingTo . "'," . $dblPrevReading . "," . $dblCurrentReading . ",'" . $strRemarks . "','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
				$process=odbc_exec($sqlconnect, $sqlquery);
				if (odbc_fetch_row($process)) {
						if (odbc_result($process,"x") == 1) 
							$strMsg = "Tenant Reading already exists!";
				}
			}
			
			if ($strMsg == "") {
				$sqlquery="exec sp_t_TenantReading_Save 'SAVE'," . $intReadingID . ",'" . $strTenantCode . "','" . $dtReadingFrom . "','" . $dtReadingTo . "'," . $dblPrevReading . "," . $dblCurrentReading . ",'" . $strRemarks . "','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
				$process=odbc_exec($sqlconnect, $sqlquery);
				if (odbc_fetch_row($process)) {
					$intReadingID = odbc_result($process,"reading_id");
				}
				//echo $intReadingID;
				$i = 1;
				while ($i <= $_POST["hidRowCtr"]) {
					$strChargeCode = replacesinglequote($_POST["hidEditChargeCode" . strval($i)]);		
					$sqlquery="exec sp_t_TenantReading_Charges_Save 'SAVE'," . $intReadingID . ",0,'" . $strChargeCode . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					$process=odbc_exec($sqlconnect, $sqlquery);		
					//echo $sqlquery;			
					if (odbc_fetch_row($process)) {
						if (odbc_result($process,"x") == 1) 
							$j++;
						else
							$k++;
					}
					$i++;
				}
				
				
				if ($_POST["cboAddCharge"] != "") {
					$sqlquery="exec sp_t_TenantReading_Charges_Save 'SAVE'," . $intReadingID . ",0,'" . $_POST["cboAddCharge"] . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					$process=odbc_exec($sqlconnect, $sqlquery);
					//echo $sqlquery;
					if (odbc_fetch_row($process)) {
						if (odbc_result($process,"x") == 1) 
							$j++;
						else
							$k++;
					}
				}
				if ($j > 0) {
					$strMsg = "Some charges were not saved. Add these charges first in Tenant Charges module.";
				}
				elseif ($k > 0) {
					$strMsg = "Record saved!";
				}
				else
					$strMsg = "Record saved!";
					
				//$strMsg = "Record saved!";				
				$strMode = "RETRIEVE";
				$strSaveMode = "EDIT";
			}			
			//echo $sqlquery;
			//die();
			break;
		
		case "DELETE":
			$i = 1;
			$j = 0;
			$k = 0;
			while ($i <= $_POST["hidRowCtr"]) {
				if (isset($_POST["chkDelete" . strval($i)])) {
					$intChargeID = $_POST["hidEditChargeID" . strval($i)];		
					$sqlquery="exec sp_t_TenantReading_Charges_Delete 'DELETE'," . $intReadingID . "," . $intChargeID . ",'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
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
			if ($j > 0) {
				$strMsg = "Some charges were not deleted.";
			}
			elseif ($k > 0) {
				$strMsg = "Charge/s deleted!";
			}
			else
				$strMsg = "No charge deleted!";
				
			$strMode = "RETRIEVE";
			break;
	}
			
	if ($strMode == "RETRIEVE" || $strMode == "FIND") {
		$sqlquery="exec sp_t_TenantReading_Retrieve 'RETRIEVE'," . $intReadingID . "";	
		//echo $sqlquery;
		$process=odbc_exec($sqlconnect, $sqlquery);
		if (odbc_fetch_row($process)) {
				$intReadingID = odbc_result($process,"reading_id");
				$strReadingID = odbc_result($process,"reading_id");
				$strTenantCode = odbc_result($process,"tenant_code");
				$strTenantName = odbc_result($process,"tenant_name");
				
				if (odbc_result($process,"date_from") == "" || date("m/d/Y",(strtotime(odbc_result($process,"date_from"))+60*60*24*($OFFSET))) == "01/01/1970")
					$dtReadingFrom = "";
				else
					$dtReadingFrom = date("m/d/Y",(strtotime(odbc_result($process,"date_from"))+60*60*24*($OFFSET)));
				
				if (odbc_result($process,"date_to") == "" || date("m/d/Y",(strtotime(odbc_result($process,"date_to"))+60*60*24*($OFFSET))) == "01/01/1970")
					$dtReadingTo = "";
				else
					$dtReadingTo = date("m/d/Y",(strtotime(odbc_result($process,"date_to"))+60*60*24*($OFFSET)));
				
				$dblPrevReading = odbc_result($process,"prev_reading");
				$dblCurrentReading = odbc_result($process,"current_reading");
				$strRemarks = replacedoublequotes(odbc_result($process,"remarks"));
				$with_invoice = odbc_result($process,"with_invoice");
				if ($with_invoice == 1) 
					$strInvoiceNo = "1";
				else
					$strInvoiceNo = "";
					
				if (odbc_result($process,"billing_date_from") == "" || date("m/d/Y",(strtotime(odbc_result($process,"billing_date_from"))+60*60*24*($OFFSET))) == "01/01/1970")
					$dtBillingFrom = "";
				else
					$dtBillingFrom = date("m/d/Y",(strtotime(odbc_result($process,"billing_date_from"))+60*60*24*($OFFSET)));
					
				if (odbc_result($process,"billing_date_to") == "" || date("m/d/Y",(strtotime(odbc_result($process,"billing_date_to"))+60*60*24*($OFFSET))) == "01/01/1970")
					$dtBillingTo = "";
				else
					$dtBillingTo = date("m/d/Y",(strtotime(odbc_result($process,"billing_date_to"))+60*60*24*($OFFSET)));
					
				$sqlqueryCharges="exec sp_t_TenantReading_Charges_Retrieve 'RETRIEVE'," . $intReadingID . "";	
				$processCharges=odbc_exec($sqlconnect, $sqlqueryCharges);
				//echo $sqlqueryCharges;
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

if ($intReadingID == 0) {
	$sqlquery="exec sp_s_TenantReading_Default_Retrieve 'RETRIEVE'";	
	//echo $sqlquery;
	$process=odbc_exec($sqlconnect, $sqlquery);
	if (odbc_fetch_row($process)) {			
		if (odbc_result($process,"trd_date_from") == "" || date("m/d/Y",(strtotime(odbc_result($process,"trd_date_from"))+60*60*24*($OFFSET))) == "01/01/1970")
			$dtReadingFrom = "";
		else
			$dtReadingFrom = date("m/d/Y",(strtotime(odbc_result($process,"trd_date_from"))+60*60*24*($OFFSET)));
		
		if (odbc_result($process,"trd_date_to") == "" || date("m/d/Y",(strtotime(odbc_result($process,"trd_date_to"))+60*60*24*($OFFSET))) == "01/01/1970")
			$dtReadingTo = "";
		else
			$dtReadingTo = date("m/d/Y",(strtotime(odbc_result($process,"trd_date_to"))+60*60*24*($OFFSET)));
					
		if (odbc_result($process,"trd_billing_date_from") == "" || date("m/d/Y",(strtotime(odbc_result($process,"trd_billing_date_from"))+60*60*24*($OFFSET))) == "01/01/1970")
			$dtBillingFrom = "";
		else
			$dtBillingFrom = date("m/d/Y",(strtotime(odbc_result($process,"trd_billing_date_from"))+60*60*24*($OFFSET)));
			
		if (odbc_result($process,"trd_billing_date_to") == "" || date("m/d/Y",(strtotime(odbc_result($process,"trd_billing_date_to"))+60*60*24*($OFFSET))) == "01/01/1970")
			$dtBillingTo = "";
		else
			$dtBillingTo = date("m/d/Y",(strtotime(odbc_result($process,"trd_billing_date_to"))+60*60*24*($OFFSET)));
			
		$sqlqueryCharges="exec sp_s_TenantReading_Default_Retrieve 'CHARGES'";	
		//echo $sqlquery;
		$processCharges=odbc_exec($sqlconnect, $sqlqueryCharges);
	}
}

if ($strMsg != ""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 

?>
<html> 
<head> 
<title>TENANT READING</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
<script type="text/javascript" src="library/datepickercontrol/datepickercontrol.js"></script>
<link type="text/css" rel="stylesheet" href="library/datepickercontrol/datepickercontrol_green.css">
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color: #F3F5B4;">
<form name="frmTenantReading" id="frmTenantReading" method="post" action="tenant_reading.php?menu_id=<?php echo $menu_id;?>">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">TENANT READING
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			 </a></li>				  
			 <?php if ($strMode != "RETRIEVE") { ?>
			  	<li class="li_nc"><a href="#" onClick="javascript:cmdRetrieve_OnClick()">|&nbsp;&nbsp;&nbsp;Retrieve&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } else { ?>		
			  	<li><a name="Retrieve" style="color: #666666">|&nbsp;&nbsp;&nbsp;Retrieve&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } ?>	
			  
			  <?php if ($strInvoiceNo == "") {?>
			  		<li class="li_nc"><a href="#" onClick="javascript:cmdSave_OnClick()">Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php }else { ?> 	
			  		<li><a name="Save" style="color: #666666">Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } ?> 	
			  
			  <?php if ($strMode != "RETRIEVE") { ?>
					<li><a name="Delete" style="color: #666666">Delete Charge&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } else { ?>			
			  		<?php if ($strInvoiceNo == "") {?>
						<li class="li_nc"><a href="#" onClick="javascript:cmdDelete_OnClick()">Delete Charge&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
					<?php }else { ?> 	
						<li><a name="Delete" style="color: #666666">Delete Charge&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
					 <?php } ?> 
			  <?php } ?>	
			  <li class="li_nc"><a href="#" onClick="javascript:change_loc('tenant_reading_list.php?menu_id=<?php echo $menu_id;?>')">Find&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <li class="li_nc"><a href="#" onClick="javascript:cmdCancel_OnClick()">Cancel&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <li class="li_nc"><a href="#" onClick="javascript:change_loc('tenant_reading_defaults.php?menu_id=<?php echo $menu_id;?>')">Set Defaults&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
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
										<td width="114" class="fieldname">READING ID :<em class="requiredRed">*</em></td>
										<td width="51"></td>
										<?php if ($strMode != "RETRIEVE") { ?>
											<td><input type=text name="txtReadingID" id="txtReadingID" class="values" size="20" value="<?php echo $strReadingID;?>"></td>
										<?php } else { ?>
											<td width="384"><input type=text name="txtReadingID" id="txtReadingID" disabled class="values" size="20" value="<?php echo $strReadingID;?>"></td>
										<?php } ?>
										<input type="hidden" name="hidReadingID" id="hidReadingID" value="<?php echo $intReadingID;?>">
									</tr>
									<tr>
										<td class="fieldname">TENANT :<em class="requiredRed">*</em></td>
										<td width="120"></td>										
										<td><input type=text name="txtTenantName" id="txtTenantName" disabled class="values" size="60" value="<?php echo $strTenantName;?>">
										<?php if (trim($strInvoiceNo) == "") {?>
											<img id="cmdTenantSearch" name="cmdTenantSearch" onClick="javascript:cmdTenantSearch_onClick();" src="images/icon_search.gif" style="cursor:hand" alt="Tenant Charges Lookup">
										<?php } else { ?>
											<img id="cmdTenantSearch" name="cmdTenantSearch" src="images/icon_search_disabled.gif">
										<?php } ?>
										</td>
										<input type="hidden" name="hidTenantCode" id="hidTenantCode" value="<?php echo $strTenantCode;?>">
										<input type="hidden" name="hidTenantName" id="hidTenantName" value="<?php echo $strTenantName;?>">
									</tr>	
								</table>				
								<table width="600">
									<tr>
										<td width="222" class="fieldname">READING FROM (mm/dd/yyyy) :<em class="requiredRed">*</em></td>
										<td width="11"></td>
										<td width="351"><input type=text name="DPC_txtReadingFrom" id="DPC_txtReadingFrom" class="values" size="20" maxlength="10" value="<?php echo $dtReadingFrom;?>"></td>
									</tr>
									<tr>
										<td class="fieldname">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TO (mm/dd/yyyy) :<em class="requiredRed">*</em></td>
										<td width="11"></td>
										<td><input type=text name="DPC_txtReadingTo" id="DPC_txtReadingTo" class="values" size="20" maxlength="10" value="<?php echo $dtReadingTo;?>"></td>
									</tr>	
									<tr>
										<td class="fieldname">BILLING FROM (mm/dd/yyyy) :<em class="requiredRed">*</em></td>
										<td width="11"></td>
										<td><input type=text name="DPC_txtBillingFrom" id="DPC_txtBillingFrom" class="values" size="20" maxlength="10" value="<?php echo $dtBillingFrom;?>"></td>
									</tr>
									<tr>
										<td class="fieldname">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TO (mm/dd/yyyy) :<em class="requiredRed">*</em></td>
										<td width="11"></td>
										<td><input type=text name="DPC_txtBillingTo" id="DPC_txtBillingTo" class="values" size="20" maxlength="10" value="<?php echo $dtBillingTo;?>"></td>
									</tr>				
								</table>		
								<table>
									<tr>
										<td width="240"></td>
										<td>
											<table width="550" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
												<tr height="30">
													<td width="8%" class="tablehdr" align="center">&nbsp;Del&nbsp;
													</td>
													<td width="22%" class="tablehdr" align="center">&nbsp;Charge Code<em class="requiredYellow">*</em>&nbsp;
													</td>
													<td  width="45%" class="tablehdr" align="center">&nbsp;Description<em class="requiredYellow">*</em>&nbsp;
													</td>							
													<td  width="25%" class="tablehdr" align="center">&nbsp;Invoice No.&nbsp;
													</td>							
												</tr>
												<?php
												$ctr = 0;
												while(odbc_fetch_row($processCharges)) {
													$charge_id = odbc_result($processCharges,"trc_charge_id"); 
													$charge_code = odbc_result($processCharges,"charge_code"); 
													$charge_desc = strtoupper(odbc_result($processCharges,"charge_desc")); 
													$invoice_no = trim(strtoupper(odbc_result($processCharges,"trc_invoice_no"))); 
													$invoice_detail_id = odbc_result($processCharges,"trc_invoice_detail_id"); 
														
													$ctr = $ctr+1;
													
													if ($ctr%2==1) 
														$rowColor = "98fb98";	
													else
														$rowColor = "ffffe0";			
												?>
												<tr id="editRow<?php echo "$ctr";?>" name="editRow<?php echo "$ctr";?>" bgcolor="<?php echo "$rowColor" ?>">
													<td width="8%" align="center" style="border:1px">
														<?php if ($invoice_no == "") { ?>
															<input type="checkbox" name="chkDelete<?php echo "$ctr";?>" id="chkDelete<?php echo "$ctr";?>" style="cursor:hand">
														<?php } else { ?>
															<input type="checkbox" name="chkDelete<?php echo "$ctr";?>" id="chkDelete<?php echo "$ctr";?>" disabled>
														<?php } ?>
													</td>
													<td width="22%" style="border:1px" class="values">
														&nbsp;<?php echo "$charge_code";?>
														<input type="hidden" id="hidEditChargeID<?php echo "$ctr";?>" name="hidEditChargeID<?php echo "$ctr";?>" value="<?php echo $charge_id;?>">
														<input type="hidden" id="hidEditChargeCode<?php echo "$ctr";?>" name="hidEditChargeCode<?php echo "$ctr";?>" value="<?php echo $charge_code;?>">
													</td>
													<td width="45%" style="border:1px" class="values">
														&nbsp;<?php echo "$charge_desc";?>
													</td>							
													<td width="25%" style="border:1px" class="values">
														&nbsp;<?php echo "$invoice_no";?>
														<input type="hidden" id="hidEditInvoiceNo<?php echo "$ctr";?>" name="hidEditInvoiceNo<?php echo "$ctr";?>" value="<?php echo $invoice_no;?>">
														<input type="hidden" id="hidEditInvoiceDetailID<?php echo "$ctr";?>" name="hidEditInvoiceDetailID<?php echo "$ctr";?>" value="<?php echo $invoice_detail_id;?>">
													</td>							
												</tr>
												<?php } ?>
												<?php
														if (($ctr + 1)%2==1) 
															$rowColor = "98fb98";								
														else
															$rowColor = "ffffe0";	
														
														if ($intReadingID ==0) {
															$sqlquerycbo="exec sp_s_TenantReading_Default_Retrieve 'ADD_CHARGES_LIST'";															
														}
														else {
															$sqlquerycbo="exec sp_s_TenantReading_AddCharges_List " . $intReadingID . "";	
														}
														$processcbo=odbc_exec($sqlconnect, $sqlquerycbo);
														while (odbc_fetch_row($processcbo)) {
															$cbocharge .= "<option value=\"" . trim(odbc_result($processcbo,"charge_code")) . "\">" . trim(strtoupper(odbc_result($processcbo,"charge_desc"))) . "</option>";
														}
												?>
												<tr id="addRow" name="addRow" bgcolor="<?php echo "$rowColor" ?>">
													<td width="8%" align="center" style="border:1px" >&nbsp;
													</td>
													<td width="22%" style="border:1px" class="values">&nbsp;
													</td>
													<td width="45%" style="border:1px" class="values">&nbsp;
														<select name="cboAddCharge" id="cboAddCharge" class="values">
															<option value="">- Select Charge -</option>
															<?php echo $cbocharge; ?>
														</select>
													</td>							
													<td width="25%" style="border:1px" class="values">&nbsp;
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
								<table width="600">																										
									<tr>
										<td width="218" class="fieldname">CURRENT READING :<em class="requiredRed">*</em></td>
										<td width="13"></td>
										<td width="353"><input type=text name="txtCurrentReading" id="txtCurrentReading" class="values" style="text-align:right" size="20" maxlength="10" value="<?php echo $dblCurrentReading;?>"></td>
									</tr>	
									<tr>
										<td class="fieldname">PREVIOUS READING :<em class="requiredRed">*</em></td>
										<td width="13"></td>
										<td><input type=text name="txtPrevReading" id="txtPrevReading" class="values" style="text-align:right" size="20" maxlength="10" value="<?php echo $dblPrevReading;?>"></td>
									</tr>																	
									<tr>
										<td class="fieldname">USAGE :</td>
										<td width="13"></td>
										<td class="values">
											<input type=text name="txtUsage" id="txtUsage" class="values" style="text-align:right; background-color:#F3F5B4; border:none" readonly size="20" value="<?php echo ($dblCurrentReading - $dblPrevReading) ;?>">&nbsp;
										</td>
									</tr>																			
									<tr>
										<td class="fieldname">REMARKS :</td>
										<td width="13"></td>
										<td><textarea name="txtRemarks" id="txtRemarks" class="values" rows="3" cols="40"><?php echo $strRemarks;?></textarea></td>
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
	<input type="hidden" id="hidRowCtr" name="hidRowCtr" value=<?php echo $ctr;?>>
</form>
</body> 
</html>

<script language="javascript" src="jsp/function.js"></script>
<script language="javascript">
function hov(loc,cls) {   
	if(loc.className)   
	loc.className=cls;   
} 

function cmdRetrieve_OnClick() {
	frmTenantReading.hidReadingID.value = frmTenantReading.txtReadingID.value;
	frmTenantReading.hidMode.value = "RETRIEVE";
	frmTenantReading.submit();
}

function cmdSave_OnClick() {
	if (trim(frmTenantReading.txtTenantName.value) == "") {
		alert("Tenant is required")
		frmTenantReading.cmdTenantSearch.focus()
		return false
	}
	if (trim(frmTenantReading.DPC_txtReadingFrom.value) == "") {
		alert("Date From is required")
		frmTenantReading.DPC_txtReadingFrom.focus()
		return false
	}
	if (isDate(frmTenantReading.DPC_txtReadingFrom.value)==false){
		frmTenantReading.DPC_txtReadingFrom.focus()
		return false
	}
	if (trim(frmTenantReading.DPC_txtReadingTo.value) == "") {
		alert("Date To is required")
		frmTenantReading.DPC_txtReadingTo.focus()
		return false
	}
	if (isDate(frmTenantReading.DPC_txtReadingTo.value)==false){
		frmTenantReading.DPC_txtReadingTo.focus()
		return false
	}
	
	if (trim(frmTenantReading.DPC_txtReadingFrom.value) != "" && trim(frmTenantReading.DPC_txtReadingTo.value) != "") {
		if (CompareDates(trim(frmTenantReading.DPC_txtReadingFrom.value),trim(frmTenantReading.DPC_txtReadingTo.value)) == false) {
			frmTenantReading.DPC_txtReadingFrom.focus();
			return false;
		}
	}
	
	if (trim(frmTenantReading.DPC_txtBillingFrom.value) == "") {
		alert("Billing From is required")
		frmTenantReading.DPC_txtBillingFrom.focus()
		return false
	}
	if (isDate(frmTenantReading.DPC_txtBillingFrom.value)==false){
		frmTenantReading.DPC_txtBillingFrom.focus()
		return false
	}
	if (trim(frmTenantReading.DPC_txtBillingTo.value) == "") {
		alert("Billing To is required")
		frmTenantReading.DPC_txtBillingTo.focus()
		return false
	}
	if (isDate(frmTenantReading.DPC_txtBillingTo.value)==false){
		frmTenantReading.DPC_txtBillingTo.focus()
		return false
	}

	if (trim(frmTenantReading.DPC_txtBillingFrom.value) != "" && trim(frmTenantReading.DPC_txtBillingTo.value) != "") {
		if (CompareDates(trim(frmTenantReading.DPC_txtBillingFrom.value),trim(frmTenantReading.DPC_txtBillingTo.value)) == false) {
			frmTenantReading.DPC_txtBillingFrom.focus();
			return false;
		}
	}

	if (frmTenantReading.cboAddCharge.value == "" && Number(frmTenantReading.hidRowCtr.value) == 0) {
		alert("Charge is required")
		frmTenantReading.cboAddCharge.focus()
		return false
	}
	
	if (isNaN(frmTenantReading.txtPrevReading.value)) {
		alert("Invalid numeric value")
		frmTenantReading.txtPrevReading.focus()
		return false
	}
	
	if (isNaN(frmTenantReading.txtCurrentReading.value)) {
		alert("Invalid numeric value")
		frmTenantReading.txtCurrentReading.focus()
		return false
	}
	
	if (Number(frmTenantReading.txtCurrentReading.value) <= Number(frmTenantReading.txtPrevReading.value)) {
		alert("Current Reading should be greater than Previous Reading")
		frmTenantReading.txtPrevReading.focus()
		return false
	}
	
	frmTenantReading.hidMode.value = "SAVE";
	frmTenantReading.submit();
}

function cmdDelete_OnClick() {
	var j
	j=0
	totalctr = frmTenantReading.hidRowCtr.value;
	if (totalctr > 1) {
		for (i=1;i<=totalctr;i++) {
			obj = eval("frmTenantReading.chkDelete" + i);
			if (obj.checked == true) {
				j++;
			}
		}
		if (j > 0) {
			if (confirm("Are you sure you want to delete this charge/s?")) {
				frmTenantReading.hidMode.value = "DELETE";
				frmTenantReading.submit();
			}
		}
		else {
			alert("Please select charge to delete");
			return false;
		}
	}
	else {
			alert("At least one charge should be retained. Deleting is not allowed this time.");
			return false;
		}
}

function cmdCancel_OnClick() {
	parent.frames[2].location = "tenant_reading.php?menu_id=" + frmTenantReading.hidMenuID.value;
	return false;
}

function cmdTenantSearch_onClick() {
	window.open ("tenant_search_reading.php?menu_id=" + frmTenantReading.hidMenuID.value,"displayWindow","type=fullwindow,titlebar=no,scrollbars=yes");
	return false;
}

function chkSelectAll_OnClick() {
	var ctr
	ctr = frmTenantReading.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmTenantReading.chkDelete" + i);
		obj1 = eval("frmTenantReading.hidEditInvoiceNo" + i);
		if (frmTenantReading.chkSelectAll.checked == true && obj1.value == "") {
			obj.checked = true;
		}
		else {
			obj.checked = false;
		}
	}
}

</script>