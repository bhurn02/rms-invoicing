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

$dtAsOf = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));
$dtNotice = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));
$dtPaymentDue = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));

$strMode = $_POST["hidMode"];

$sqlquerycbo="select * from m_real_property order by real_property_name";
$processcbo=odbc_exec($sqlconnect, $sqlquerycbo);
while (odbc_fetch_row($processcbo)) {
	$cbocharge .= "<option value=\"" . trim(odbc_result($processcbo,"real_property_code")) . "\">" . trim(strtoupper(odbc_result($processcbo,"real_property_name"))) . "</option>";
}

if ($strMode != "") {
	$dtAsOf = $_POST["DPC_txtAsOf"];
	$dtNotice = $_POST["DPC_txtNotice"];	
	$dtPaymentDue = $_POST["DPC_txtPaymentDue"];
	$strSortBy = $_POST["cboSortBy"];
	$strSearchValue = $_POST["txtSearchValue"];
	
	switch ($strMode) {
		case "SEARCH":		
			$sqlquery="exec sp_rpt_TenantForPaymentNotice_Search '" . $dtAsOf . "','" . $strSortBy . "','" . $strSearchValue . "'";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			//echo $sqlquery;
			break;
			
		case "PRINT":		
			$sqlquery="exec sp_rpt_TenantForPaymentNotice_Save 'DELETE',0";	
			$process=odbc_exec($sqlconnect, $sqlquery);					
			$i = 0;
			while ($i <= $_POST["hidRowCtr"]) {
				if (isset($_POST["chkSelect" . strval($i)])) {					
					$strCode = $_POST["hidCode" . strval($i)];
					$sqlquery="exec sp_rpt_TenantForPaymentNotice_Save 'SAVE'," . $strCode . "";	
					//echo $sqlquery;
					$process=odbc_exec($sqlconnect, $sqlquery);				
				}
				$i++;
			}
						
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
			$my_report = $report_path . "t_tenant_demand_notice_first.rpt"; // 
			//echo $my_report;
			//die();
			//rpt source file 
			$pdf_file = "t_tenant_demand_notice_first" . str_replace("/","",date("m/d/y/H/i/s", time())) . ".pdf";
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
			$creport->ParameterFields(1)->AddCurrentValue ($_POST["DPC_txtAsOf"]);
			$creport->ParameterFields(2)->AddCurrentValue ($_POST["DPC_txtNotice"]);
			$creport->ParameterFields(3)->AddCurrentValue ($_POST["DPC_txtPaymentDue"]);
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
			
			$sqlquery="exec sp_rpt_TenantForPaymentNotice_Search '" . $dtAsOf . "','" . $strSortBy . "','" . $strSearchValue . "'";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			
			break;
	}
}

?>
<html> 
<head> 
<title>NOTICE > DEMAND TO PAY - FIRST NOTICE</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
<script type="text/javascript" src="library/datepickercontrol/datepickercontrol.js"></script>
<link type="text/css" rel="stylesheet" href="library/datepickercontrol/datepickercontrol_green.css">
</head> 
<body style="margin:'0';background-color: #F3F5B4;">
<form name="frmFirstNotice" id="frmFirstNotice" method="post">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">NOTICE > DEMAND TO PAY - FIRST NOTICE
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
										<td class="fieldname_right">AGING AS OF (mm/dd/yyyy):</td>
										<td width="20"></td>
										<td><input type=text name="DPC_txtAsOf" id="DPC_txtAsOf" class="values" size="20" maxlength="10" value="<?php echo $dtAsOf;?>">										
										</td>
									</tr>			
									<tr>
										<td class="fieldname_right">NOTICE DATE (mm/dd/yyyy):</td>
										<td width="20"></td>
										<td><input type=text name="DPC_txtNotice" id="DPC_txtNotice" class="values" size="20" maxlength="10" value="<?php echo $dtNotice;?>">										
										</td>
									</tr>													
									<tr>
										<td class="fieldname_right">PAYMENT DUE DATE (mm/dd/yyyy):</td>
										<td width="20"></td>
										<td><input type=text name="DPC_txtPaymentDue" id="DPC_txtPaymentDue" class="values" size="20" maxlength="10" value="<?php echo $dtPaymentDue;?>">										
										</td>
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
										<td class="fieldname_right">&nbsp;</td>
										<td width="20"></td>
										<td>&nbsp;										
										</td>
									</tr>																	
								</table>
								<table width="3000" style="border:1px solid #556b2f;padding: 30px 10px 5px; width:auto;">
									<tr height="30">
										<td width="37" align="center"  class="tablehdr">&nbsp;Sel&nbsp;
										</td>
										<td width="91" align="center"  class="tablehdr">&nbsp;#&nbsp;
										</td>
										<td width="223" align="center"  class="tablehdr">&nbsp;Account No.&nbsp;
										</td>
										<td width="155" align="center"  class="tablehdr">&nbsp;Tenant Name&nbsp;
										</td>													
										<td width="414" align="center"  class="tablehdr">&nbsp;Real Property&nbsp;
										</td>							
										<td width="182" align="center"  class="tablehdr">&nbsp;Total&nbsp;
										</td>	
										<td width="182" align="center"  class="tablehdr">&nbsp;Current&nbsp;
										</td>	
										<td width="182" align="center"  class="tablehdr">&nbsp;Total Overdue&nbsp;
										</td>	
										<td width="182" align="center"  class="tablehdr">&nbsp;From 1 To 30&nbsp;
										</td>	
										<td width="182" align="center"  class="tablehdr">&nbsp;From 31 To 60&nbsp;
										</td>	
										<td width="182" align="center"  class="tablehdr">&nbsp;From 61 To 90&nbsp;
										</td>	
										<td width="182" align="center"  class="tablehdr">&nbsp;From 91 To 120&nbsp;
										</td>	
										<td width="182" align="center"  class="tablehdr">&nbsp;From 121 To 150&nbsp;
										</td>	
										<td width="182" align="center"  class="tablehdr">&nbsp;Over 151&nbsp;
										</td>	
										<td width="182" align="center"  class="tablehdr">&nbsp;For Write-Off&nbsp;
										</td>	
										<td width="192" align="center"  class="tablehdr">&nbsp;Remarks&nbsp;
										</td>													
									</tr>	
									<?php	
									$ctr = 0;		
									//echo $sqlquery;
									while(odbc_fetch_row($process)) {
										$ctr++;
										$wta_rec_id = odbc_result($process,"wta_rec_id"); 
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
											<td align="center" style="border:1px;cursor:hand;">
												<input type="checkbox" name="chkSelect<?php echo "$ctr" ?>" id="chkSelect<?php echo "$ctr" ?>">
												<input type="hidden" name="hidCode<?php echo "$ctr" ?>" id="hidCode<?php echo "$ctr" ?>" value="<?php echo "$wta_rec_id" ?>">
											</td>
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
   var w = frmFirstNotice.cboRealProperty.selectedIndex;
   frmFirstNotice.hidRealPropertyName.value = frmFirstNotice.cboRealProperty.options[w].text;
   cmdSearch_OnClick();
   }

function txtKeyword_onKeyUp(e) {
	if (e.keyCode==13) {
		cmdSearch_OnClick();
	}
}

function cmdSearch_OnClick() {
	if (frmFirstNotice.DPC_txtAsOf.value == "") {
		alert("Aging As Of is required")		
		frmFirstNotice.DPC_txtAsOf.focus()
		return false
	}
	
	if (frmFirstNotice.DPC_txtAsOf.value != "") {
		if (isDate(frmFirstNotice.DPC_txtAsOf.value)==false) {
			frmFirstNotice.DPC_txtAsOf.focus()
			return false
		}
	}
	
	frmFirstNotice.hidMode.value = "SEARCH"
	frmFirstNotice.submit()
}

function cmdPrint_OnClick() {

	if (frmFirstNotice.DPC_txtNotice.value == "") {
		alert("Notice Date is required")		
		frmFirstNotice.DPC_txtNotice.focus()
		return false
	}
	
	if (frmFirstNotice.DPC_txtNotice.value != "") {
		if (isDate(frmFirstNotice.DPC_txtNotice.value)==false) {
			frmFirstNotice.DPC_txtNotice.focus()
			return false
		}
	}
	
	if (frmFirstNotice.DPC_txtPaymentDue.value == "") {
		alert("Payment Due Date is required")		
		frmFirstNotice.DPC_txtPaymentDue.focus()
		return false
	}

	if (frmFirstNotice.DPC_txtPaymentDue.value != "") {
		if (isDate(frmFirstNotice.DPC_txtPaymentDue.value)==false) {
			frmFirstNotice.DPC_txtPaymentDue.focus()
			return false
		}
	}
	
	var j
	j=0
	totalctr = frmFirstNotice.hidRowCtr.value;
	for (i=1;i<=totalctr;i++) {
		obj = eval("frmFirstNotice.chkSelect" + i);
		if (obj.checked == true) {
			j++;
		}
	}

	if (j > 0) {
		frmFirstNotice.hidMode.value = "PRINT"
		frmFirstNotice.submit()
	}
	else {
		alert("Select tenants first");
	}
		
}

function chkSelectAll_OnClick() {
	var ctr
	ctr = frmFirstNotice.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmFirstNotice.chkSelect" + i);
		if (frmFirstNotice.chkSelectAll.checked == true) {
			obj.checked = true;
		}
		else {
			obj.checked = false;
		}
	}
}

</script>
