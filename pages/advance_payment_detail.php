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
$dblTotalAmount = "0.00";
$dblBalanceAmount = "0.00";
$dblDeductedAmount = "0.00";
$intARDetailID = 0;
$dblChargeAmount = 0;	
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
			
			//$intARDetailID = $_POST["hidEditARDetailID" . strval($i)];											
			$strChargeCode = $_POST["cboAddCharge"];							
			$sqlquery="exec sp_t_Advance_Payment_Detail 'SAVE'," . $intARDetailID . ",'" . $strORNo . "','','" . $strChargeCode . "',0,'','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";
			$process=odbc_exec($sqlconnect, $sqlquery);				

			$strMsg = "Record saved!";
			
			$sqlquery="exec sp_t_Advance_Payment_Detail 'RETRIEVE_HDR',0,'" . $strORNo . "','','" . $strChargeCode . "',0,'','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			//echo $sqlquery;
			$process=odbc_exec($sqlconnect, $sqlquery);
			if (odbc_fetch_row($process)) {
					$strORNo = replacedoublequotes(odbc_result($process,"or_no"));
					$strClientCode = replacedoublequotes(odbc_result($process,"client_code"));
					$strClientName = replacedoublequotes(odbc_result($process,"client_name"));
					$dblTotalAmount = odbc_result($process,"amount");
					$dblDeductedAmount = odbc_result($process,"deducted_amount");
					$dblBalanceAmount = odbc_result($process,"balance_amount");
					$dblTotalDetailAmount = odbc_result($process,"total_detail_amount");
					$strStatus = odbc_result($process,"status");
					$strStatusDesc = odbc_result($process,"status_desc");
					$intTotalDetailCnt = odbc_result($process,"detail_cnt");
					$strMode = "RETRIEVE";
					$strSaveMode = "EDIT";
					
					$sqlqueryCharges="exec sp_t_Advance_Payment_Detail 'RETRIEVE_DTL',0,'" . $strORNo . "','','',0,'','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					$processCharges=odbc_exec($sqlconnect, $sqlqueryCharges);
					//echo $sqlqueryCharges;
			}
			else {
				$strMode = "";
			}
			break;
			
		case "DELETE":			
			$strORNo = $_POST["hidORNo"];
			$strClientCode = $_POST["hidClientCode"];
			$strClientName = $_POST["hidClientName"];
			$i = 1;
			$j = 0;
			$k = 0;
			while ($i <= $_POST["hidRowCtr"]) {			
				if (isset($_POST["chkDelete" . strval($i)])) {		
					$intARDetailID = $_POST["hidEditARDetailID" . strval($i)];																
					$strChargeCode = $_POST["hidEditChargeCode" . strval($i)];											
					$sqlquery="exec sp_t_Advance_Payment_Detail 'DELETE'," . $intARDetailID . ",'" . $strORNo . "','','" . $strChargeCode . "',0,'','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
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
				$strMsg = "Some records were not deleted because it had been posted in Payment module.";
			}
			elseif ($j == 0 && $k > 0) {
				$strMsg = "Record/s deleted!";
			}
			elseif ($j > 0 && $k == 0) {
				$strMsg = "No record deleted because it had been posted in Payment module!";
			}
			else
				$strMsg = "No record deleted!";
			
			$sqlquery="exec sp_t_Advance_Payment_Detail 'RETRIEVE_HDR',0,'" . $strORNo . "','','" . $strChargeCode . "',0,'','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			//echo $sqlquery;
			$process=odbc_exec($sqlconnect, $sqlquery);
			if (odbc_fetch_row($process)) {
					$strORNo = replacedoublequotes(odbc_result($process,"or_no"));
					$strClientCode = replacedoublequotes(odbc_result($process,"client_code"));
					$strClientName = replacedoublequotes(odbc_result($process,"client_name"));
					$dblTotalAmount = odbc_result($process,"amount");
					$dblDeductedAmount = odbc_result($process,"deducted_amount");
					$dblBalanceAmount = odbc_result($process,"balance_amount");
					$dblTotalDetailAmount = odbc_result($process,"total_detail_amount");
					$strStatus = odbc_result($process,"status");
					$strStatusDesc = odbc_result($process,"status_desc");
					$intTotalDetailCnt = odbc_result($process,"detail_cnt");
					$strMode = "RETRIEVE";
					$strSaveMode = "EDIT";
					
					$sqlqueryCharges="exec sp_t_Advance_Payment_Detail 'RETRIEVE_DTL',0,'" . $strORNo . "','','',0,'','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					$processCharges=odbc_exec($sqlconnect, $sqlqueryCharges);
					//echo $sqlqueryCharges;
			}
			else {
				$strMode = "";
			}
			break;
			
		case "RETRIEVE" || "FIND":
			$sqlquery="exec sp_t_Advance_Payment_Detail 'RETRIEVE_HDR',0,'" . $strORNo . "','','" . $strChargeCode . "',0,'','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			//echo $sqlquery;
			$process=odbc_exec($sqlconnect, $sqlquery);
			if (odbc_fetch_row($process)) {
					$strORNo = replacedoublequotes(odbc_result($process,"or_no"));
					$strClientCode = replacedoublequotes(odbc_result($process,"client_code"));
					$strClientName = replacedoublequotes(odbc_result($process,"client_name"));
					$dblTotalAmount = odbc_result($process,"amount");
					$dblDeductedAmount = odbc_result($process,"deducted_amount");
					$dblBalanceAmount = odbc_result($process,"balance_amount");
					$dblTotalDetailAmount = odbc_result($process,"total_detail_amount");
					$strStatus = odbc_result($process,"status");
					$strStatusDesc = odbc_result($process,"status_desc");
					$intTotalDetailCnt = odbc_result($process,"detail_cnt");
					$strMode = "RETRIEVE";
					$strSaveMode = "EDIT";
					
					$sqlqueryCharges="exec sp_t_Advance_Payment_Detail 'RETRIEVE_DTL',0,'" . $strORNo . "','','',0,'','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					$processCharges=odbc_exec($sqlconnect, $sqlqueryCharges);
					//echo $sqlqueryCharges;
			}
			else {
				$strMode = "";
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
<title>ADVANCE PAYMENT DETAIL</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
</head> 
<body style="margin:'0';background-color: #F3F5B4;">
<form name="frmAdvancePaymentDtl" id="frmAdvancePaymentDtl" method="post" action="advance_payment_detail.php?menu_id=<?php echo $menu_id;?>">
	<div class="mainmenu">	
		<ul>
			  <?php if ($strMode != "RETRIEVE" || $strStatus == "P") { ?>			  		
			  	  <li class="li_nc"><a name="MODULE NAME">ADVANCE PAYMENT</a></li>	
			  <?php } else { ?>						  
  				  <li class="li_nc"><a href="#" onClick="javascript:change_loc('advance_payment_header.php?menu_id=<?php echo $menu_id;?>&mode=FIND&or_no=<?php echo $strORNo; ?>')"><u>ADVANCE PAYMENT</u></a></li>	
			  <?php } ?>
			  <li class="li_nc"><a name="MODULE NAME">>>&nbsp;&nbsp;&nbsp;&nbsp;DETAIL
			  &nbsp;&nbsp;&nbsp;&nbsp;
		   	  	<?php if ($strMenu != "DETAIL") { ?>
					&nbsp;&nbsp;&nbsp;
				 <?php } ?>
			 </a></li>	
			 <?php if ($strMode != "RETRIEVE") { ?>
					<li class="li_nc"><a href="#" onClick="javascript:cmdRetrieve_OnClick()">|&nbsp;&nbsp;&nbsp;Retrieve&nbsp;&nbsp;&nbsp;|</a></li>			  
			 <?php } else { ?>		
			 		<li><a name="Retrieve" style="color: #666666">|&nbsp;&nbsp;&nbsp;Retrieve&nbsp;&nbsp;&nbsp;|</a></li>					
			  <?php } ?>
			 
			  <?php if ($strMode != "RETRIEVE" || $strStatus == "P") { ?>			  		
			  		<li><a name="Save" style="color: #666666">Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
					<li><a name="Delete" style="color: #666666">Delete&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } else { ?>	
					<li class="li_nc"><a href="#" onClick="javascript:cmdSave_OnClick()">Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
					<li class="li_nc"><a href="#" onClick="javascript:cmdDelete_OnClick()">Delete&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>					
			  <?php } ?>	
			  <li class="li_nc"><a href="#" onClick="javascript:change_loc('advance_payment_detail_list.php?menu_id=<?php echo $menu_id;?>')">Find&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <li class="li_nc"><a href="#" onClick="javascript:cmdCancel_OnClick()">Cancel&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>			  
			  <?php if ($strMenu == "DETAIL") { ?>
			  	<li class="li_nc"><a href="#" onClick="javascript:change_loc('advance_payment_header.php?menu_id=<?php echo $menu_id;?>&mode=FIND&or_no=<?php echo $strORNo; ?>')">Back&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
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
									<tr>
										<td class="fieldname">AMOUNT :</td>
										<td width="20"></td>
										<td class="values">
											<table><tr><td width="140" class="values" style="text-align:right">
												$<?php echo $dblTotalAmount;?>
											</td></tr></table>
										</td>
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
									<?php if ($strStatus != "") { ?>
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
					<table width="350" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
						<tr height="30">
							<td width="12%" class="tablehdr" align="center">&nbsp;Del&nbsp;
							</td>							
							<td  width="88%" class="tablehdr" align="center">&nbsp;Charge&nbsp;
							</td>																														
						</tr>
						<?php
						$ctr = 0;
						while(odbc_fetch_row($processCharges)) {
							$intARDetailID = odbc_result($processCharges,"ar_detail_id");							
							$strChargeCode = replacedoublequotes(odbc_result($processCharges,"charge_code"));
							$strChargeDesc = replacedoublequotes(odbc_result($processCharges,"charge_desc"));

							$ctr = $ctr+1;
							
							if ($ctr%2==1) 
								$rowColor = "98fb98";	
							else
								$rowColor = "ffffe0";			
						?>
						<tr id="editRow<?php echo "$ctr";?>" name="editRow<?php echo "$ctr";?>" style="cursor:hand" bgcolor="<?php echo "$rowColor" ?>">
							<td width="12%" align="center" style="border:1px">
								<input type="checkbox" name="chkDelete<?php echo "$ctr";?>" id="chkDelete<?php echo "$ctr";?>">
								<input type="hidden" id="hidEditARDetailID<?php echo "$ctr";?>" name="hidEditARDetailID<?php echo "$ctr";?>" value="<?php echo $intARDetailID;?>">
							</td>							
							<td width="88%" style="border:1px" class="values">&nbsp;<?php echo $strChargeDesc;?>
								<input type="hidden" name="hidEditChargeCode<?php echo "$ctr";?>" id="hidEditChargeCode<?php echo "$ctr";?>" value=<?php echo "$strChargeCode";?>>														
							</td>																	
						</tr>
						<?php } ?>
						<?php
								if ($ctr%2==1) 
									$rowColor = "ffffe0";	
								else
									$rowColor = "98fb98";								
						?>
						<tr id="addRow" name="addRow" bgcolor="<?php echo "$rowColor"; ?>">
							<td width="12%" align="center" style="border:1px" >&nbsp;
							</td>														
							<td width="88%" style="border:1px" class="values">&nbsp;
								<?php if ($strStatus == "") { ?>
									<select name="cboAddCharge" id="cboAddCharge" class="values">
										<option value="">- Select Charge -</option>
										<?php echo $cbocharge; ?>
									</select>
								<?php } else { ?>		
									<select name="cboAddCharge" id="cboAddCharge" class="values" disabled>
										<option value="">- Select Charge -</option>										
									</select>
								<?php } ?>		
							</td>
						</tr>	
					</table>
					<table>
						<tr>
							<td class="values">&nbsp;
								<?php if ($strStatus == "") { ?>
									<input type="checkbox" name="chkSelectAll" id="chkSelectAll" onClick="javascript:chkSelectAll_OnClick();">SELECT ALL
								<?php } else { ?>	
									<input type="checkbox" name="chkSelectAll" id="chkSelectAll" onClick="javascript:chkSelectAll_OnClick();" disabled>SELECT ALL
								<?php } ?>	
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
	ctr = frmAdvancePaymentDtl.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmAdvancePaymentDtl.chkDelete" + i);		
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
	if (frmAdvancePaymentDtl.hidORNo.value == "") {
		alert("Select OR first!")
		return false
	}
	
	if (frmAdvancePaymentDtl.cboAddCharge.value == "") {
		alert("Charge is required")
		frmAdvancePaymentDtl.cboAddCharge.focus()
		return false	
	}
	
	frmAdvancePaymentDtl.hidMode.value = "SAVE";
	frmAdvancePaymentDtl.submit();
}

function cmdRetrieve_OnClick() {
	frmAdvancePaymentDtl.hidMode.value = "RETRIEVE";
	frmAdvancePaymentDtl.submit();
}

function cmdCancel_OnClick() {	
	parent.frames[2].location = "advance_payment_detail.php?menu_id=" + frmAdvancePaymentDtl.hidMenuID.value;
	return false;
}

function cmdDelete_OnClick() {
	var j
	j=0
	totalctr = frmAdvancePaymentDtl.hidRowCtr.value;
	for (i=1;i<=totalctr;i++) {
		obj = eval("frmAdvancePaymentDtl.chkDelete" + i);
		if (obj.checked == true) {
			j++;
		}
	}
	if (j > 0) {
		if (confirm("Are you sure you want to delete this record/s?")) {
			frmAdvancePaymentDtl.hidMode.value = "DELETE";
			frmAdvancePaymentDtl.submit();
		}
	}
	else {
		alert("Select record/s to delete");
	}
}

function chkSelectAll_OnClick() {
	var ctr
	ctr = frmAdvancePaymentDtl.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmAdvancePaymentDtl.chkDelete" + i);
		if (frmAdvancePaymentDtl.chkSelectAll.checked == true) {
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

