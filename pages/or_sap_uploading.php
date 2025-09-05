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
$strSortby = "";
$strMsg = "";

$sqlquerycbo="select * from m_real_property order by real_property_name";
$processcbo=odbc_exec($sqlconnect, $sqlquerycbo);
while (odbc_fetch_row($processcbo)) {
	$cbocharge .= "<option value=\"" . trim(odbc_result($processcbo,"real_property_code")) . "\">" . trim(strtoupper(odbc_result($processcbo,"real_property_name"))) . "</option>";
}

if ($strMode != "") {	
	$dtBillingFrom = $_POST["DPC_txtDateFrom"];
	$dtBillingTo = $_POST["DPC_txtDateTo"];
	$strRealPropertyName = $_POST["hidRealPropertyName"];
	$strSortBy = $_POST["cboSortBy"];

	//echo $strRealPropertyName;
	switch ($strMode) {
		case "BUILD":						
			$sqlquery="exec sp_t_Payment_SAP_Uploading_Build 'BUILD','','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $strSortBy . "','','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			//echo $sqlquery;
			break;
			
		case "POST":						
			$i = 1;
			$j = 0;
			while ($i <= $_POST["hidRowCtr"]) {
				if (isset($_POST["chkSelect" . strval($i)])) {					
					$strInvoiceNo = $_POST["hidInvoiceNo" . strval($i)];
					$sqlquery="exec sp_t_Payment_SAP_Uploading 'POST','" . $strInvoiceNo . "','" . $dtBillingFrom . "','" . $dtBillingTo . "','','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					//echo $sqlquery;
					$process=odbc_exec($sqlconnect, $sqlquery);					
					$j++;
				}
				$i++;
			}
			if ($j > 0)
				$strMsg = "OR/s posted!";
			else
				$strMsg = "No OR posted!";
			
			$sqlquery="exec sp_t_Payment_SAP_Uploading 'BUILD','','" . $dtBillingFrom . "','" . $dtBillingTo . "','','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			break;
		
		case "GENERATE":
			$i = 1;
			$j = 0;
			$date_uploaded = date("m/d/y H:i:s", time());
			$sqlquery="exec sp_t_Payment_SAP_Uploading 'DELETE','','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $date_uploaded . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			while ($i <= $_POST["hidRowCtr"]) {
				if (isset($_POST["chkSelect" . strval($i)])) {					
					$strInvoiceNo = $_POST["hidInvoiceNo" . strval($i)];
					$sqlquery="exec sp_t_Payment_SAP_Uploading 'GENERATE','" . $strInvoiceNo . "','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $date_uploaded . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					//echo $sqlquery;
					$process=odbc_exec($sqlconnect, $sqlquery);
					$j++;
				}
				$i++;
			}
			if ($j == 0) {
				$strMsg = "No OR selected!";
			}
			else {
				//create file
				$strSAPTextFileName = "OR-SAP" .  str_replace("/","",date("m/d/y/H/i/s", time())) . ".prn" ;
				$strSAPTextFileHandle = fopen("sap/" . $strSAPTextFileName, 'w') or die("can't open file");
				unlink($strSAPTextFileHandle);
				$strAppendData = "";
				//$strSAPTextFileHandle = fopen($strSAPTextFileName, 'w') or die("can't open file");
				$sqlquery="exec sp_t_Payment_SAP_Uploading 'CREATE_FILE','','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $date_uploaded . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
				$process=odbc_exec($sqlconnect, $sqlquery);
				while(odbc_fetch_row($process)){
					$strAppendData = odbc_result($process,"doc_date") . 
									odbc_result($process,"posting_date") .
									odbc_result($process,"refdocno") .
									odbc_result($process,"company_code") .
									odbc_result($process,"currency") .
									odbc_result($process,"doctype") .
									odbc_result($process,"postingkey") .
									odbc_result($process,"account_code") .
									odbc_result($process,"amount") .
									odbc_result($process,"tax_code") .
									odbc_result($process,"buss_area") .
									odbc_result($process,"cost_center") .
									odbc_result($process,"job_order") .
									odbc_result($process,"baselndate") .
									odbc_result($process,"new_code") .
									odbc_result($process,"alloc") .
									odbc_result($process,"stext") . "\r\n"; 								
					fwrite($strSAPTextFileHandle, $strAppendData);
				}
				fclose($strSAPTextFileHandle);
				//end create file
				
				//download file
				// Send file headers
				//$strSAPTextFileName = "sap/" . $strSAPTextFileName;
				$type = filetype($strSAPTextFileName);
				header("Content-type: $type");
				header("Content-Disposition: attachment;filename=$strSAPTextFileName");
				header("Content-Transfer-Encoding: binary");
				header('Pragma: no-cache');
				header('Expires: 0');
				set_time_limit(0);
				readfile("sap/" . $strSAPTextFileName);
				
				$sqlquery="exec sp_t_Payment_SAP_Uploading 'DELETE','','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $_POST["hidDateUploaded"] . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
				$process=odbc_exec($sqlconnect, $sqlquery);
				
				exit();
			}
			//end download file
			
			$sqlquery="exec sp_t_Payment_SAP_Uploading 'BUILD','','" . $dtBillingFrom . "','" . $dtBillingTo . "','','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			
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
<title>OR SAP UPLOADING</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
<script type="text/javascript" src="library/datepickercontrol/datepickercontrol.js"></script>
<link type="text/css" rel="stylesheet" href="library/datepickercontrol/datepickercontrol_green.css">
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color: #F3F5B4;">
<form name="frmORSAPUploading" id="frmORSAPUploading" method="post">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">OR SAP UPLOADING
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			 </a></li>	
			  <li class="li_nc"><a href="#" onClick="javascript:cmdBuild_OnClick()">|&nbsp;&nbsp;&nbsp;Build OR List&nbsp;&nbsp;&nbsp;|</a></li>			  			  
			  <?php if ($strMode == "") { ?>
			  	<li><a name="Generate" style="color: #666666">Generate File&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } else { ?>
			  	<li class="li_nc"><a href="#" onClick="javascript:cmdGenerate_OnClick()">Generate File&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>			  			  
		      <?php } ?>
			  <li class="li_nc"><a href="#" onClick="javascript:cmdPost_OnClick()">Post&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>			  			  
			  <li class="li_nc"><a href="#" onClick="javascript:cmdClose_OnClick()">Close&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>				  
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
										<td class="fieldname" align="right">OR DATE FROM (mm/dd/yyyy) :</td>
										<td width="10"></td>
										<td><input type=text name="DPC_txtDateFrom" id="DPC_txtDateFrom" class="values" size="20" maxlength="10" value="<?php echo $dtBillingFrom; ?>"></td>
									</tr>	
									<tr>
										<td class="fieldname" align="right">TO (mm/dd/yyyy) :</td>
										<td width="10"></td>
										<td><input type=text name="DPC_txtDateTo" id="DPC_txtDateTo" class="values" size="20" maxlength="10"  value="<?php echo $dtBillingTo; ?>"></td>
									</tr>			
									<tr>
										<td class="fieldname" align="right">SORT BY :</td>
										<td width="10"></td>
									  <td>
											<select name="cboSortBy" id="cboSortBy" class="values" onChange="javascript:cmdBuild_OnClick()">												
												<?php if ($strSortBy == "" || $strSortBy == "OR NO.") {?>
													<option selected value="OR NO.">OR No.</option>
													<option value="OR DATE">OR Date</option>
													<option value="CLIENT">Client</option>
													<option value="UNIT NO.">Unit No.</option>
												<?php } else if ($strSortBy == "OR DATE") { ?>
													<option value="OR NO.">OR No.</option>
													<option selected value="OR DATE">OR Date</option>
													<option value="CLIENT">Client</option>
													<option value="UNIT NO.">Unit No.</option>
												<?php } else if ($strSortBy == "CLIENT") { ?>
													<option value="OR NO.">OR No.</option>
													<option value="OR DATE">OR Date</option>
													<option selected value="CLIENT">Client</option>
													<option value="UNIT NO.">Unit No.</option>
												<?php } else { ?>
													<option value="OR NO.">OR No.</option>
													<option value="OR DATE">OR Date</option>
													<option value="CLIENT">Client</option>
													<option selected value="UNIT NO.">Unit No.</option>
												<?php } ?>
											</select>
										</td>
									</tr>																																																							
									<tr>
										<td class="fieldname">&nbsp;</td>
										<td width="10">&nbsp;</td>
										<td>&nbsp;</td>
									</tr>																	
								</table>
							</td>
						</tr>						
					</table>					
					<table width="820" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
						<tr height="30">
							<td width="4%" class="tablehdr" align="center">&nbsp;Sel&nbsp;
							</td>
							<td width="13%" class="tablehdr" align="center">&nbsp;OR No.&nbsp;
							</td>
							<td width="11%" class="tablehdr" align="center">&nbsp;Date&nbsp;
							</td>
							<td  width="42%" class="tablehdr" align="center">&nbsp;Client&nbsp;
							</td>							
							<td  width="17%" class="tablehdr" align="center">&nbsp;Unit No.&nbsp;
							</td>							
							<td  width="13%" class="tablehdr" align="center">&nbsp;Total Amount&nbsp;
							</td>							
						</tr>
						<?php
						$total_or_list_amount = 0;
						while(odbc_fetch_row($process)){
							$or_no = odbc_result($process,"or_no"); 
							$or_date = date("m/d/Y",(strtotime(odbc_result($process,"or_date"))+60*60*24*($OFFSET)));	
							$client_code = odbc_result($process,"client_code"); 
							$client_name = odbc_result($process,"client_name"); 
							$unit_no = odbc_result($process,"unit_no"); 
							$total_amount = odbc_result($process,"total_amount"); 

							$ctr = $ctr+1;
							$total_or_list_amount = $total_or_list_amount + $total_amount;
							
							if ($ctr%2==1) 
								$rowColor = "98fb98";	
							else
								$rowColor = "ffffe0";			
						?>
						<tr bgcolor="<?php echo "$rowColor" ?>">
							<td width="4%" align="center" style="border:1px;cursor:hand;">
								<input type="checkbox" name="chkSelect<?php echo "$ctr" ?>" id="chkSelect<?php echo "$ctr" ?>">
								<input type="hidden" name="hidInvoiceNo<?php echo "$ctr" ?>" id="hidInvoiceNo<?php echo "$ctr" ?>" value="<?php echo "$or_no" ?>">
							</td>
							<td width="13%" style="border:1px" class="values">&nbsp;<?php echo "$or_no";?>&nbsp;
							</td>
							<td width="11%" style="border:1px" class="values" align="center">&nbsp;<?php echo "$or_date";?>&nbsp;
							</td>
							<td width="42%" style="border:1px" class="values">&nbsp;<?php echo "$client_name";?>&nbsp;
							</td>							
							<td width="17%" style="border:1px" class="values">&nbsp;<?php echo "$unit_no";?>
							</td>							
							<td width="13%" style="border:1px" class="values" align="right">&nbsp;<?php echo numberformat($total_amount);?>&nbsp;
							</td>							
						</tr>
						<?php }							
							if ($ctr%2==1) 
								$rowColor = "ffffe0";			
							else
								$rowColor = "98fb98";	
								
							$total_or_list_amount = numberformat($total_or_list_amount);
						?>
						<tr bgcolor="<?php echo "$rowColor" ?>">
							<td width="4%" align="center">&nbsp;
								
							</td>
							<td width="13%" style="border:1px" class="values">&nbsp;
							</td>
							<td width="11%" style="border:1px" class="values" align="center">&nbsp;
							</td>
							<td width="42%" style="border:1px" class="values">&nbsp;<b>TOTAL AMOUNT</b>
							</td>							
							<td width="17%" style="border:1px" class="values">&nbsp;
							</td>							
							<td width="13%" style="border:1px" class="values" align="right"><b>&nbsp;$<?php echo $total_or_list_amount;?>&nbsp;</b>
							</td>							
						</tr>
					</table>			
					<table>
						<tr>
							<td class="values">&nbsp;
								<input type="checkbox" name="chkSelectAll" id="chkSelectAll" style="cursor:hand;" onClick="javascript:chkSelectAll_OnClick();">SELECT ALL
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
   var w = frmORSAPUploading.cboRealProperty.selectedIndex;
   frmORSAPUploading.hidRealPropertyName.value = frmORSAPUploading.cboRealProperty.options[w].text;
   }

function txtKeyword_onKeyUp(e) {
	if (e.keyCode==13) {
		cmdSearch_onClick();
	}
}

function chkSelectAll_OnClick() {
	var ctr
	ctr = frmORSAPUploading.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmORSAPUploading.chkSelect" + i);
		if (frmORSAPUploading.chkSelectAll.checked == true) {
			obj.checked = true;
		}
		else {
			obj.checked = false;
		}
	}
}

function cmdBuild_OnClick() {
	if (frmORSAPUploading.DPC_txtDateFrom.value == "") {
		alert("Please provide OR Date From")
		frmORSAPUploading.DPC_txtDateFrom.focus()
		return false
	}
	if (frmORSAPUploading.DPC_txtDateFrom.value != "") {
		if (isDate(frmORSAPUploading.DPC_txtDateFrom.value)==false) {
			frmORSAPUploading.DPC_txtDateFrom.focus()
			return false
		}
	}
	
	if (frmORSAPUploading.DPC_txtDateTo.value == "") {
		alert("Please provide OR Date To")
		frmORSAPUploading.DPC_txtDateTo.focus()
		return false
	}
	if (frmORSAPUploading.DPC_txtDateTo.value != "") {
		if (isDate(frmORSAPUploading.DPC_txtDateTo.value)==false) {
			frmORSAPUploading.DPC_txtDateTo.focus()
			return false
		}
	}
		
	if (frmORSAPUploading.DPC_txtDateFrom.value != "" && frmORSAPUploading.DPC_txtDateTo.value != "") {
		if (CompareDates(frmORSAPUploading.DPC_txtDateFrom.value,frmORSAPUploading.DPC_txtDateTo.value)==false) {
			frmORSAPUploading.DPC_txtDateFrom.focus()
			return false
		}
	}

	frmORSAPUploading.hidMode.value = "BUILD";
	frmORSAPUploading.submit();
}

function cmdGenerate_OnClick() {
	var j
	j=0
	totalctr = frmORSAPUploading.hidRowCtr.value;
	for (i=1;i<=totalctr;i++) {
		obj = eval("frmORSAPUploading.chkSelect" + i);
		if (obj.checked == true) {
			j++;
		}
	}

	if (j > 0) {
		frmORSAPUploading.hidMode.value = "GENERATE";
		frmORSAPUploading.submit();
	}
	else {
		alert("Select OR/s first");
	}
}

function cmdPost_OnClick() {
	var j
	j=0
	totalctr = frmORSAPUploading.hidRowCtr.value;
	for (i=1;i<=totalctr;i++) {
		obj = eval("frmORSAPUploading.chkSelect" + i);
		if (obj.checked == true) {
			j++;
		}
	}

	if (j > 0) {
		if (confirm("Are you sure you want to post these OR/s? There is no undo for this process.")) {
			frmORSAPUploading.hidMode.value = "POST";
			frmORSAPUploading.submit();
		}
	}
	else {
		alert("Select OR/s to post first");
	}
}

function cmdClose_OnClick() {
	parent.frames[2].location = "blank.htm";
	return false;
}

function cmdSearch_onClick() {
	save_text();
	frmORSAPUploading.hidMode.value = "SEARCH";
	frmORSAPUploading.submit();
}

</script>