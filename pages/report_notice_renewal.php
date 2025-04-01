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
		echo "<script> parent.frames.location = \"" . "accessnotallowed.htm" .  "\";</script>";
		exit(); 
	}
}

//end access
$sqlconnect = connection();
$strIPAddr = $_SERVER["REMOTE_ADDR"];
$menu_id = $_GET["menu_id"];

$dtNotice = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));
$dtFrom = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));
$dtTo = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));

$strMode = $_POST["hidMode"];

$sqlquerycbo="select * from m_real_property order by real_property_name";
$processcbo=odbc_exec($sqlconnect, $sqlquerycbo);
while (odbc_fetch_row($processcbo)) {
	$cbocharge .= "<option value=\"" . trim(odbc_result($processcbo,"real_property_code")) . "\">" . trim(strtoupper(odbc_result($processcbo,"real_property_name"))) . "</option>";
}

if ($strMode != "") {
	$dtNotice = $_POST["DPC_txtNotice"];
	$dtFrom = $_POST["DPC_txtDateFrom"];
	$dtTo = $_POST["DPC_txtDateTo"];
	$strRealPropertyCode = $_POST["cboRealProperty"];
	$strRealPropertyName = $_POST["hidRealPropertyName"];
	$strSortBy = $_POST["cboSortBy"];
	$strSearchValue = $_POST["txtSearchValue"];
	
	switch ($strMode) {
		case "SEARCH":		
			$sqlquery="exec sp_rpt_TenantForRenewalNotice_Search '" . $dtFrom . "','" . $dtTo . "','" . $strRealPropertyCode . "','" . $strSortBy . "','" . $strSearchValue . "'";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			
			break;
			
		case "PRINT":		
			$sqlquery="exec sp_rpt_TenantForRenewalNotice_Save 'DELETE',''";	
			$process=odbc_exec($sqlconnect, $sqlquery);					
			$i = 0;
			while ($i <= $_POST["hidRowCtr"]) {
				if (isset($_POST["chkSelect" . strval($i)])) {					
					$strCode = $_POST["hidCode" . strval($i)];
					$sqlquery="exec sp_rpt_TenantForRenewalNotice_Save 'SAVE','" . $strCode . "'";	
					//echo $sqlquery;
					$process=odbc_exec($sqlconnect, $sqlquery);				
				}
				$i++;
			}
						//die();
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
			$my_report = $report_path . "t_tenant_for_renewal.rpt"; // 
			//echo $my_report;
			//die();
			//rpt source file 
			$pdf_file = "t_tenant_for_renewal" . str_replace("/","",date("m/d/y/H/i/s", time())) . ".pdf";
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
			$creport->ParameterFields(1)->AddCurrentValue ($_POST["DPC_txtNotice"]);
			$creport->ParameterFields(2)->AddCurrentValue ($_POST["DPC_txtDateFrom"]);
			$creport->ParameterFields(3)->AddCurrentValue ($_POST["DPC_txtDateTo"]);
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
			
			$dtNotice = $_POST["DPC_txtNotice"];
			$dtFrom = $_POST["DPC_txtDateFrom"];
			$dtTo = $_POST["DPC_txtDateTo"];
			
			$sqlquery="exec sp_rpt_TenantForRenewalNotice_Search '" . $dtFrom . "','" . $dtTo . "','" . $strRealPropertyCode . "','" . $strSortBy . "','" . $strSearchValue . "'";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			
			break;
	}
}

?>
<html> 
<head> 
<title>NOTICE > LEASE AGREEMENT RENEWAL</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
<script type="text/javascript" src="library/datepickercontrol/datepickercontrol.js"></script>
<link type="text/css" rel="stylesheet" href="library/datepickercontrol/datepickercontrol_green.css">
</head> 
<body style="margin:'0';background-color: #F3F5B4;">
<form name="frmRenewal" id="frmRenewal" method="post">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">NOTICE > LEASE AGREEMENT RENEWAL
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			 </a></li>	
			  <li class="li_nc"><a href="#" onClick="javascript:cmdSearch_OnClick()">|&nbsp;&nbsp;&nbsp;&nbsp;Search&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <li class="li_nc"><a href="#" onClick="javascript:cmdPrint_OnClick()">Print&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <li class="li_nc"><a href="#" onClick="javascript:window.close();">Close&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
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
										<td class="fieldname_right">NOTICE DATE (mm/dd/yyyy):</td>
										<td width="20"></td>
										<td><input type=text name="DPC_txtNotice" id="DPC_txtNotice" class="values" size="20" maxlength="10" value="<?php echo $dtNotice;?>">										
										</td>
									</tr>													
									<tr>
										<td class="fieldname_right">EXPIRY DATE FROM (mm/dd/yyyy):</td>
										<td width="20"></td>
										<td><input type=text name="DPC_txtDateFrom" id="DPC_txtDateFrom" class="values" size="20" maxlength="10" value="<?php echo $dtFrom;?>">										
										</td>
									</tr>					
									<tr>
										<td class="fieldname_right">TO (mm/dd/yyyy):</td>
										<td width="20"></td>
										<td><input type=text name="DPC_txtDateTo" id="DPC_txtDateTo" class="values" size="20" maxlength="10" value="<?php echo $dtTo;?>">										
										</td>
									</tr>			
									<tr>
										<td class="fieldname" align="right">REAL PROPERTY :</td>
										<td width="10"></td>
									  <td>
											<select name="cboRealProperty" id="cboRealProperty" class="values" onChange="javascript:save_text();">
												<?php if ($strRealPropertyCode!= "") { ?>
													<option selected value="<?php echo $strRealPropertyCode; ?>"><?php echo $strRealPropertyName; ?></option>
												<?php } ?>
												<option value="">- ALL -</option>
												<?php echo $cbocharge; ?>
											</select>
										</td>
									</tr>			
									<tr>
										<td class="fieldname" align="right">SEARCH AND SORT BY :</td>
										<td width="10"></td>
									  <td>
											<select name="cboSortBy" id="cboSortBy" class="values" onChange="javascript:save_text();">												
												<?php if ($strSortBy == "" || $strSortBy == "TENANT") {?>
													<option selected value="TENANT">Tenant</option>
													<option value="BUILDING CODE">Building Code</option>
													<option value="UNIT NO.">Unit No.</option>
												<?php } else if ($strSortBy == "BUILDING CODE") { ?>
													<option value="TENANT">Tenant</option>
													<option selected value="BUILDING CODE">Building Code</option>
													<option value="UNIT NO.">Unit No.</option>
												<?php } else { ?>
													<option value="TENANT">Tenant</option>
													<option value="BUILDING CODE">Building Code</option>
													<option selected value="UNIT NO.">Unit No.</option>
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
										<td class="fieldname_right">&nbsp;</td>
										<td width="20"></td>
										<td>&nbsp;										
										</td>
									</tr>																	
								</table>
								<table width="1000" style="border:1px solid #556b2f;padding: 30px 10px 5px; width:auto;">
									<tr height="30">
										<td width="31" align="center"  class="tablehdr">&nbsp;Sel&nbsp;
										</td>
										<td width="400" align="center"  class="tablehdr">&nbsp;Tenant&nbsp;
										</td>
										<td width="314" align="center"  class="tablehdr">&nbsp;Real Property&nbsp;
										</td>													
										<td width="130" align="center"  class="tablehdr">&nbsp;Building Code&nbsp;
										</td>							
										<td width="101" align="center"  class="tablehdr">&nbsp;Unit No.&nbsp;
										</td>														
									</tr>	
									<?php	
									$ctr = 0;		
									//echo $sqlquery;
									while(odbc_fetch_row($process)) {
										$ctr++;
										$tenant_code = odbc_result($process,"tenant_code"); 
										$tenant_name = odbc_result($process,"tenant_name"); 
										$real_property_name = odbc_result($process,"real_property_name"); 	
										$building_code = odbc_result($process,"building_code"); 	
										$unit_no = odbc_result($process,"unit_no"); 	
										
										if ($ctr%2==1) 
											$rowColor = "98fb98";	
										else
											$rowColor = "ffffe0";		
										
									?>
										<tr bgcolor="<?php echo "$rowColor" ?>">
											<td align="center" style="border:1px;cursor:hand;">
												<input type="checkbox" name="chkSelect<?php echo "$ctr" ?>" id="chkSelect<?php echo "$ctr" ?>">
												<input type="hidden" name="hidCode<?php echo "$ctr" ?>" id="hidCode<?php echo "$ctr" ?>" value="<?php echo "$tenant_code" ?>">
											</td>
											<td  style="border:1px" class="values">&nbsp;<?php echo "$tenant_name";?>&nbsp;
											</td>
											<td style="border:1px" class="values" align="left">&nbsp;<?php echo "$real_property_name";?>&nbsp;
											</td>							
											<td  style="border:1px" class="values">&nbsp;<?php echo "$building_code";?>&nbsp;
											</td>							
											<td  style="border:1px" class="values">&nbsp;<?php echo "$unit_no";?>
											</td>
										</tr>
									<?php }						
										if ($ctr%2==1) 
											$rowColor = "ffffe0";			
										else
											$rowColor = "98fb98";	
											
									?>						
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
		</td>
		</tr>
	</table>
	<input type="hidden" id="hidMode" name="hidMode">
	<input type="hidden" id="hidSaveMode" name="hidSaveMode" value="<?php echo $strSaveMode; ?>">
	<input type="hidden" id="hidMenuID" name="hidMenuID" value=<?php echo $menu_id;?>>
	<input type="hidden" id="hidRealPropertyName" name="hidRealPropertyName" value=<?php echo $strRealPropertyName;?>>
	<input type="hidden" id="hidRowCtr" name="hidRowCtr" value=<?php echo $ctr;?>>
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
   var w = frmRenewal.cboRealProperty.selectedIndex;
   frmRenewal.hidRealPropertyName.value = frmRenewal.cboRealProperty.options[w].text;
   cmdSearch_OnClick();
   }

function txtKeyword_onKeyUp(e) {
	if (e.keyCode==13) {
		save_text();
	}
}

function cmdSearch_OnClick() {
	frmRenewal.hidMode.value = "SEARCH"
	frmRenewal.submit()
}

function cmdPrint_OnClick() {

	if (frmRenewal.DPC_txtNotice.value == "") {
		alert("Notice Date is required")		
		frmRenewal.DPC_txtNotice.focus()
		return false
	}
	
	if (frmRenewal.DPC_txtNotice.value != "") {
		if (isDate(frmRenewal.DPC_txtNotice.value)==false) {
			frmRenewal.DPC_txtNotice.focus()
			return false
		}
	}

	if (frmRenewal.DPC_txtDateFrom.value != "") {
		if (isDate(frmRenewal.DPC_txtDateFrom.value)==false) {
			frmRenewal.DPC_txtDateFrom.focus()
			return false
		}
	}
	if (frmRenewal.DPC_txtDateTo.value != "") {
		if (isDate(frmRenewal.DPC_txtDateTo.value)==false) {
			frmRenewal.DPC_txtDateTo.focus()
			return false
		}
	}
	
	if (frmRenewal.DPC_txtDateFrom.value != "" && frmRenewal.DPC_txtDateTo.value != "") {
		if (CompareDates(frmRenewal.DPC_txtDateFrom.value,frmRenewal.DPC_txtDateTo.value)==false) {
			frmRenewal.DPC_txtDateFrom.focus()
			return false
		}
	}
	
	var j
	j=0
	totalctr = frmRenewal.hidRowCtr.value;	
	for (i=1;i<=totalctr;i++) {
		obj = eval("frmRenewal.chkSelect" + i);
		if (obj.checked == true) {
			j++;
		}
	}
	if (j > 0) {
		frmRenewal.hidMode.value = "PRINT"
		frmRenewal.submit()
	}
	else {
		alert("Select tenants first");
	}
		
}

function chkSelectAll_OnClick() {
	var ctr
	ctr = frmRenewal.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmRenewal.chkSelect" + i);
		if (frmRenewal.chkSelectAll.checked == true) {
			obj.checked = true;
		}
		else {
			obj.checked = false;
		}
	}
}

</script>
