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

$uid = $sessUserID;
$company_code = $sessCompanyCode;

$strMode = trim($_POST["hidMode"]);
$strSaveMode = $_POST["hidSaveMode"];
$dtAsOf = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
$strSearchValue = "";

$strMsg = "";

if ($strMode != "") {	
	$dtAsOf = $_POST["DPC_txtDateFrom"];
	$strSortBy = $_POST["cboSortBy"];
	$strSearchValue = $_POST["txtSearchValue"];

	//echo $strRealPropertyName;
	switch ($strMode) {
		case "SEARCH":						
			$sqlquery="exec sp_u_UploadAging_Search '" . $dtAsOf . "','" . $strSortBy . "','" . $strSearchValue . "'";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			//echo $sqlquery;
			break;
		
		case "UPLOAD":
			$sqlquery="exec sp_u_UploadAgingHeader_CheckIfAlertSent '" . $dtAsOf . "'";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			//echo $sqlquery;
			if (odbc_fetch_row($process)) {
					if (odbc_result($process,"x") == 1) 
						$strMsg = odbc_result($process,"msg");
			}
			
			if ($strMsg == "") {
				$strFile = "D:\System\rms\docs\RMS Accounts Receivable Aging Schedule September 2012.xlsx";
				$dsnExcel = "Driver={Microsoft Excel Driver (*.xls, *.xlsx, *.xlsm, *.xlsb)};DBQ=" . $strFile . ";";
				$sqlconnectExcel=odbc_connect($dsnExcel,"","");
				$sqlquery = "select * from [RMS Aging$]";
				$processExcel=odbc_exec($sqlconnectExcel, $sqlquery);
				$ctr = 0;
				$dtNow = date("m/d/y H:i:s", time());	
				while(odbc_fetch_row($processExcel)){
					echo odbc_result($processExcel,1);
					if ($ctr == 0) {
						$sqlquery="exec sp_u_UploadAgingHeader_Save 0,'" . $strFile . "','" . $dtAsOf . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
						$process=odbc_exec($sqlconnect, $sqlquery);
						if(odbc_fetch_row($process)){
							$hdr_id = odbc_result($process,"wth_hdr_id");
						}
					}				
					if ($ctr > 2) {		
						//$sqlquery = "exec sp_u_UploadAgingDetail_Save " . $hdr_id . ",'" . replacesinglequote(odbc_result($processExcel,1)) . "','" . replacesinglequote(odbc_result($processExcel,2)) . "','" . replacesinglequote(odbc_result($processExcel,3)) . "','" . odbc_result($processExcel,4) . "',";
						//$sqlquery = $sqlquery . "'" . odbc_result($processExcel,5) . "','" . odbc_result($processExcel,6) . "','" . odbc_result($processExcel,7) . "',";
						//$sqlquery = $sqlquery . "'" . odbc_result($processExcel,8) . "','" . odbc_result($processExcel,9) . "','" . odbc_result($processExcel,10) . "','" . odbc_result($processExcel,11) . "',";
						//$sqlquery = $sqlquery . "'" . odbc_result($processExcel,12) . "','" . replacesinglequote(odbc_result($processExcel,13)) . "','" . replacesinglequote(odbc_result($processExcel,14)) . "'";	
						if (odbc_result($processExcel,4) == "")
							$excel_total = 0;
						else
							$excel_total = odbc_result($processExcel,4);
							
						if (odbc_result($processExcel,5) == "")
							$excel_current = 0;
						else
							$excel_current = odbc_result($processExcel,5);
							
						if (odbc_result($processExcel,6) == "")
							$excel_total_overdue = 0;
						else
							$excel_total_overdue = odbc_result($processExcel,6);
							
						if (odbc_result($processExcel,7) == "")
							$excel_1_30 = 0;
						else
							$excel_1_30 = odbc_result($processExcel,7);
							
						if (odbc_result($processExcel,8) == "")
							$excel_31_60 = 0;
						else
							$excel_31_60 = odbc_result($processExcel,8);
							
						if (odbc_result($processExcel,9) == "")
							$excel_61_90 = 0;
						else
							$excel_61_90 = odbc_result($processExcel,9);
							
						if (odbc_result($processExcel,10) == "")
							$excel_91_120 = 0;
						else
							$excel_91_120 = odbc_result($processExcel,10);
							
						if (odbc_result($processExcel,11) == "")
							$excel_121_150 = 0;
						else
							$excel_121_150 = odbc_result($processExcel,11);
							
						if (odbc_result($processExcel,12) == "")
							$excel_over_151 = 0;
						else
							$excel_over_151 = odbc_result($processExcel,12);
							
						if (odbc_result($processExcel,13) == "")
							$excel_write_off = 0;
						else
							$excel_write_off = odbc_result($processExcel,13);
						
						$sqlquery = "exec sp_u_UploadAgingDetail_Save " . $hdr_id . ",'" . replacesinglequote(odbc_result($processExcel,1)) . "','" . replacesinglequote(odbc_result($processExcel,2)) . "','" . replacesinglequote(odbc_result($processExcel,3)) . "'," . $excel_total . ",";
						$sqlquery = $sqlquery . "" . $excel_current . "," . $excel_total_overdue . "," . $excel_1_30 . ",";
						$sqlquery = $sqlquery . "" . $excel_31_60 . "," . $excel_61_90 . "," . $excel_91_120 . "," . $excel_121_150 . ",";
						$sqlquery = $sqlquery . "" . $excel_over_151 . "," . $excel_write_off . ",'" . replacesinglequote(odbc_result($processExcel,14)) . "'";	
						$processDetail = odbc_exec($sqlconnect, $sqlquery);
						//echo $sqlquery;					
					}
					//exit();
					$ctr++;
					//echo $processExcel->Fields(0);
					//echo odbc_result($processExcel,1); 				
				}
				if (($ctr-3)<0) {
					$ctr = 3; }
							
				$strMsg =  "Successfully uploaded " . strval($ctr-3) . " records."; 
			}
			break;
			
		case "UPLOAD_1":						
			// FTP access parameters
			set_time_limit(0);
			
			$ftp = ftp();
			$host = $ftp[1];
			$usr = $ftp[2];
			$pwd = $ftp[3];
			$path = $ftp[4];
			 
			// file to move:
			//$local_file = $_POST["txtFile"];
			$local_file = $_FILES["txtFile"]["tmp_name"];
			//$local_file = $_FILES["txtFile"]["name"];
			//$local_date_file = $signature_path . "\\" . $_POST["hidWONo"] . "-DATE.jpg";
			
			//$local_file = $signature_ftp_path. $_POST["hidWONo"] . ".jpg";
			//$local_date_file = $signature_ftp_path . $_POST["hidWONo"] . "-DATE.jpg";
			
			//echo $local_file;
			//exit();		
			//if (file_exists($local_file)) {
				
				$ftp_path = "RMS-" . str_replace("/","",$dtAsOf) . "-" . str_replace("/","",date("m/d/y/H/i/s", time())) .'.xlsx';
				//echo $local_file;
				//echo $_FILES["txtFile"];
				//echo $ftp_path;
				//exit();
				// connect to FTP server (port 21)
				$conn_id = ftp_connect($host, 21) or die ("Cannot connect to host");
				 
				// send access parameters
				ftp_login($conn_id, $usr, $pwd) or die("Cannot login");
				
				// turn on passive mode transfers (some servers need this)
				ftp_pasv ($conn_id, true);
				 
				// upload a file  
				//echo ftp_put($conn_id, $ftp_path, $local_file, FTP_BINARY) . "res";
				//ftp_put($conn_id, $ftp_path, $local_file, FTP_BINARY);
				echo $conn_id; 
				echo " 2 " . $ftp_path;
				echo " 3 " . $local_file;
				die();
				if (ftp_put($conn_id, $ftp_path, $local_file, FTP_BINARY)) {     
					//$rename_local_file = $signature_path . "\\" . $_POST["hidWONo"] . "_OK.jpg";
					//$rename_local_date_file = $signature_path . "\\" . $_POST["hidWONo"] . "-DATE_OK.jpg";
					
					//rename($local_file,$rename_local_file);
					//rename($local_date_file,$rename_local_date_file);
					$strFile = $path . $ftp_path;
					//echo $strFile;
					//die();
					$dsnExcel = "Driver={Microsoft Excel Driver (*.xls, *.xlsx, *.xlsm, *.xlsb)};DBQ=" . $strFile . ";";
					$sqlconnectExcel=odbc_connect($dsnExcel,"","");
					$sqlquery = "select * from [RMS Aging$]";
					$processExcel=odbc_exec($sqlconnectExcel, $sqlquery);
					$ctr = 0;
					$dtNow = date("m/d/y H:i:s", time());	
					while(odbc_fetch_row($processExcel)){
						//echo $processExcel->Fields(0);
						echo odbc_result($processExcel,0); 
						$ctr++;
					}
					
					//to sort seq by passenger name
					
					$strMsg =  "Successfully uploaded " . strval($ctr) . " records.";   
				} 
				else {     
					$strMsg = "There was a problem while uploading the aging report.";     
				}
					// check upload status:
					//print (!$upload) ? 'Cannot upload' : 'Upload complete';
					//print "\n";
							 
				// close the FTP stream
				ftp_close($conn_id);
			//}
			//else {
			//	$strMsg = "File does not exist.";
			//}
			
			$strMode = "";
		
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
<title>UPLOAD AGING REPORT</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
<script type="text/javascript" src="library/datepickercontrol/datepickercontrol.js"></script>
<link type="text/css" rel="stylesheet" href="library/datepickercontrol/datepickercontrol_green.css">
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color: #F3F5B4;">
<form name="frmUploadAging" id="frmUploadAging" method="post">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">UPLOAD AGING REPORT
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			 </a></li>	
			  <li class="li_nc"><a href="#" onClick="javascript:cmdSearch_OnClick()">|&nbsp;&nbsp;&nbsp;Search&nbsp;&nbsp;&nbsp;|</a></li>			  			  
			  <li class="li_nc"><a href="#" onClick="javascript:cmdUpload_OnClick()">Upload&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>			  			  
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
										<td class="fieldname" align="right">BROWSE FILE :<em class="requiredRed">*</em></td>
										<td width="10"></td>
										<td><input type=file name="txtFile" id="txtFile" class="values" size="50" value="<?php echo $local_file;?>"></td>
									</tr>	
									<tr>
										<td class="fieldname" align="right">AS OF (mm/dd/yyyy) :<em class="requiredRed">*</em></td>
										<td width="10"></td>
										<td><input type=text name="DPC_txtDateFrom" id="DPC_txtDateFrom" class="values" size="20" maxlength="10" value="<?php echo $dtAsOf; ?>"></td>
									</tr>	
									<tr>
										<td class="fieldname" align="right">SEARCH AND SORT BY :</td>
										<td width="10"></td>
									  <td>
											<select name="cboSortBy" id="cboSortBy" class="values">												
												<?php if ($strSortBy == "" || $strSortBy == "TENANT") {?>
													<option selected value="TENANT">Tenant</option>
													<option value="REAL PROPERTY">Real Property</option>													
													<option value="SAP ACCOUNT CODE">SAP Account Code</option>													
												<?php } else if ($strSortBy == "REAL PROPERTY") { ?>
													<option value="TENANT">Tenant</option>
													<option selected value="REAL PROPERTY">Real Property</option>													
													<option value="SAP ACCOUNT CODE">SAP Account Code</option>													
												<?php } else { ?>
													<option value="TENANT">Tenant</option>
													<option value="REAL PROPERTY">Real Property</option>													
													<option selected value="SAP ACCOUNT CODE">SAP Account Code</option>													
												<?php } ?>
											</select>
										</td>
									</tr>			
									<tr>
										<td class="fieldname" align="right">VALUE :</td>
										<td width="10"></td>
										<td><input type=text name="txtSearchValue" id="txtSearchValue" class="values" size="50" maxlength="100" value="<?php echo $strSearchValue; ?>" onKeyUp="javascript:txtKeyword_onKeyUp(event)"></td>
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
					<table width="3000" style="border:1px solid #556b2f;padding: 30px 10px 5px; width:auto;">
						<tr height="30">
							<td width="41" align="center"  class="tablehdr">&nbsp;#&nbsp;
							</td>
							<td width="146" align="center"  class="tablehdr">&nbsp;Account No.&nbsp;
							</td>
							<td width="101" align="center"  class="tablehdr">&nbsp;Tenant Name&nbsp;
							</td>													
							<td width="272" align="center"  class="tablehdr">&nbsp;Real Property&nbsp;
							</td>							
							<td width="119" align="center"  class="tablehdr">&nbsp;Total&nbsp;
							</td>	
							<td width="119" align="center"  class="tablehdr">&nbsp;Current&nbsp;
							</td>	
							<td width="119" align="center"  class="tablehdr">&nbsp;Total Overdue&nbsp;
							</td>	
							<td width="119" align="center"  class="tablehdr">&nbsp;From 1 To 30&nbsp;
							</td>	
							<td width="119" align="center"  class="tablehdr">&nbsp;From 31 To 60&nbsp;
							</td>	
							<td width="119" align="center"  class="tablehdr">&nbsp;From 61 To 90&nbsp;
							</td>	
							<td width="119" align="center"  class="tablehdr">&nbsp;From 91 To 120&nbsp;
							</td>	
							<td width="119" align="center"  class="tablehdr">&nbsp;From 121 To 150&nbsp;
							</td>	
							<td width="119" align="center"  class="tablehdr">&nbsp;Over 151&nbsp;
							</td>	
							<td width="119" align="center"  class="tablehdr">&nbsp;For Write-Off&nbsp;
							</td>	
							<td width="119" align="center"  class="tablehdr">&nbsp;Remarks&nbsp;
							</td>													
						</tr>	
						<?php	
						$ctr = 0;		
						//echo $sqlquery;
						while(odbc_fetch_row($process)) {
							$ctr++;
							$sap_code = replacedoublequotes(odbc_result($process,"wta_sap_code")); 
							$tenant_name = replacedoublequotes(odbc_result($process,"wta_tenant_name")); 
							$real_property = replacedoublequotes(odbc_result($process,"wta_real_property_name")); 
							if (odbc_result($process,"wta_total_balance")==0)
								$total_balance = "";
							else
								$total_balance = replacedoublequotes(odbc_result($process,"wta_total_balance")); 
								
							if (odbc_result($process,"wta_curr_balance")==0)
								$curr_balance = "";
							else
								$curr_balance = replacedoublequotes(odbc_result($process,"wta_curr_balance")); 
							
							if (odbc_result($process,"wta_total_overdue")==0)	
								$total_overdue = "";
							else	
								$total_overdue = replacedoublequotes(odbc_result($process,"wta_total_overdue")); 
								
							if (odbc_result($process,"wta_aging_1_30")==0)
								$aging_1_30 = "";
							else
								$aging_1_30 = replacedoublequotes(odbc_result($process,"wta_aging_1_30")); 
								
							if (odbc_result($process,"wta_aging_31_60")==0)
								$aging_31_60 = "";
							else
								$aging_31_60 = replacedoublequotes(odbc_result($process,"wta_aging_31_60")); 
								
							if (odbc_result($process,"wta_aging_61_90")==0)
								$aging_61_90 = "";
							else
								$aging_61_90 = replacedoublequotes(odbc_result($process,"wta_aging_61_90")); 
							
							if (odbc_result($process,"wta_aging_91_120")==0)
								$aging_91_120 = "";
							else
								$aging_91_120 = replacedoublequotes(odbc_result($process,"wta_aging_91_120")); 
								
							if (odbc_result($process,"wta_aging_121_150")==0)
								$aging_121_150 = "";
							else
								$aging_121_150 = replacedoublequotes(odbc_result($process,"wta_aging_121_150")); 
							
							if (odbc_result($process,"wta_aging_over_151")==0)
								$aging_over_151 = "";
							else
								$aging_over_151 = replacedoublequotes(odbc_result($process,"wta_aging_over_151")); 
								
							if (odbc_result($process,"wta_write_off")==0)
								$write_off = "";
							else
								$write_off = replacedoublequotes(odbc_result($process,"wta_write_off")); 
								
							$remarks = replacedoublequotes(odbc_result($process,"wta_remarks")); 
							
							if ($ctr%2==1) 
								$rowColor = "98fb98";	
							else
								$rowColor = "ffffe0";		
							
						?>
							<tr bgcolor="<?php echo "$rowColor" ?>">
								<td align="right" class="values"><?php echo "$ctr";?>&nbsp;</td>
								<td  style="border:1px" class="values">&nbsp;<?php echo "$sap_code";?>&nbsp;
								</td>
								<td style="border:1px" class="values">&nbsp;<?php echo "$tenant_name";?>&nbsp;
								</td>							
								<td  style="border:1px" class="values">&nbsp;<?php echo "$real_property";?>&nbsp;
								</td>							
								<td  style="border:1px" class="values" align="right"><?php echo "$total_balance";?>&nbsp;
								</td>
								<td  style="border:1px" class="values" align="right"><?php echo "$curr_balance";?>&nbsp;
								</td>
								<td  style="border:1px" class="values" align="right"><?php echo "$total_overdue";?>&nbsp;
								</td>
								<td  style="border:1px" class="values" align="right"><?php echo "$aging_1_30";?>&nbsp;
								</td>
								<td  style="border:1px" class="values" align="right"><?php echo "$aging_31_60";?>&nbsp;
								</td>
								<td  style="border:1px" class="values" align="right"><?php echo "$aging_61_90";?>&nbsp;
								</td>
								<td  style="border:1px" class="values" align="right"><?php echo "$aging_91_120";?>&nbsp;
								</td>
								<td  style="border:1px" class="values" align="right"><?php echo "$aging_121_150";?>&nbsp;
								</td>
								<td  style="border:1px" class="values" align="right"><?php echo "$aging_over_151";?>&nbsp;
								</td>
								<td  style="border:1px" class="values">&nbsp;<?php echo "$write_off";?>
								</td>
								<td  style="border:1px" class="values">&nbsp;<?php echo "$remarks";?>
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
   var w = frmUploadAging.cboRealProperty.selectedIndex;
   frmUploadAging.hidRealPropertyName.value = frmUploadAging.cboRealProperty.options[w].text;
   cmdSearch_OnClick();
   }

function txtKeyword_onKeyUp(e) {
	if (e.keyCode==13) {
		cmdSearch_OnClick();
	}
}

function chkSelectAll_OnClick() {
	var ctr
	ctr = frmUploadAging.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmUploadAging.chkSelect" + i);
		if (frmUploadAging.chkSelectAll.checked == true) {
			obj.checked = true;
		}
		else {
			obj.checked = false;
		}
	}
}

function cmdSearch_OnClick() {	
	frmUploadAging.hidMode.value = "SEARCH";
	frmUploadAging.submit();
}

function cmdUpload_OnClick() {
	if (trim(frmUploadAging.txtFile.value) == "") {
		alert("Select file first")
		frmUploadAging.txtFile.focus()
		return false
	}
	if (Right(frmUploadAging.txtFile.value,4) != ".xls" && Right(frmUploadAging.txtFile.value,5) != ".xlsx") {
		alert("Selected file should be in excel format (*.xls or *.xlsx)")
		frmUploadAging.txtFile.focus()
		return false
	}
	if (frmUploadAging.DPC_txtDateFrom.value == "") {
		alert("Please provide As Of Date")
		frmUploadAging.DPC_txtDateFrom.focus()
		return false
	}
	if (frmUploadAging.DPC_txtDateFrom.value != "") {
		if (isDate(frmUploadAging.DPC_txtDateFrom.value)==false) {
			frmUploadAging.DPC_txtDateFrom.focus()
			return false
		}
	}
	frmUploadAging.hidMode.value = "UPLOAD";
	frmUploadAging.submit();
}

function cmdClose_OnClick() {
	parent.frames(2).location = "blank.htm";
	return false;
}

</script>
