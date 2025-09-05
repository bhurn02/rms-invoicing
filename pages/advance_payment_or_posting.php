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
$strRealPropertyCode = "";
$strMsg = "";

$sqlquerycbo="select * from m_real_property order by real_property_name";
$processcbo=odbc_exec($sqlconnect, $sqlquerycbo);
while (odbc_fetch_row($processcbo)) {
	$cbocharge .= "<option value=\"" . trim(odbc_result($processcbo,"real_property_code")) . "\">" . trim(strtoupper(odbc_result($processcbo,"real_property_name"))) . "</option>";
}

if ($strMode != "") {	
	$dtBillingFrom = $_POST["txtDateFrom"];
	$dtBillingTo = $_POST["txtDateTo"];
	$strRealPropertyCode = $_POST["cboRealProperty"];
	$strRealPropertyName = $_POST["hidRealPropertyName"];

	//echo $strRealPropertyName;
	switch ($strMode) {
		case "BUILD":				
			$date_uploaded = date("m/d/y H:i:s", time());
			$sqlquery="exec sp_t_Advance_Payment_OR_Processing 'DELETE','','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $strRealPropertyCode . "','" . $_POST["hidDateUploaded"] . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			//echo $sqlquery;
			$process=odbc_exec($sqlconnect, $sqlquery);		
			$sqlquery="exec sp_t_Advance_Payment_OR_Processing 'BUILD','','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $strRealPropertyCode . "','" . $date_uploaded . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			$strMsg = "Please specify OR breakdown if there's a pencil icon under OR Breakdown column.";
			
			break;
		
		case "PROCESS":
			$i = 1;
			$j = 0;
			$sqlquery="exec sp_t_Advance_Payment_OR_Processing 'DELETE','','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $strRealPropertyCode . "','" . $_POST["hidDateUploaded"] . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			$date_uploaded = date("m/d/y H:i:s", time());
			while ($i <= $_POST["hidRowCtr"]) {				
				if (isset($_POST["chkSelect" . strval($i)])) {		
					$strInvoiceNo = $_POST["hidInvoiceNo" . strval($i)];			
					$sqlquery="exec sp_t_Advance_Payment_OR_Processing 'PROCESS','" . $strInvoiceNo . "','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $strRealPropertyCode . "','" . $date_uploaded . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
					//echo $sqlquery;
					$process=odbc_exec($sqlconnect, $sqlquery);
					$j++;
				}
				$i++;
			}
			
			$sqlquery="exec sp_t_Advance_Payment_OR_Processing 'CHECK_TMP','','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $strRealPropertyCode . "','" . $date_uploaded . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			if (odbc_fetch_row($process)) {
				if (odbc_result($process,"x")==1) {
					$j = 0;
					$strMsg = "Some invoice/s were not processed. Please specify OR breakdown by clicking the pencil icon.";
					$strMode = "PROCESS";
				}
			}
			
			if ($j > 0) {
				$strMsg = "OR's are processed. Check Unpaid Bills module.";
				$strMode = "";
			}
			
			$sqlquery="exec sp_t_Advance_Payment_OR_Processing 'BUILD','','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $strRealPropertyCode . "','" . $date_uploaded . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
			$process=odbc_exec($sqlconnect, $sqlquery);
			break;
	}
}	
else {
	$sqlquery="exec sp_t_Advance_Payment_OR_Processing 'DELETE_ALL','','" . $dtBillingFrom . "','" . $dtBillingTo . "','" . $strRealPropertyCode . "','','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
	//echo $sqlquery;
	$process=odbc_exec($sqlconnect, $sqlquery);		
}

//echo $sqlquery;
if ($strMsg != "" && $strMode==""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 


?>
<html> 
<head> 
<title>ADVANCE PAYMENT - OR PROCESSING</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color: #F3F5B4;">
<form name="frmORProcessing" id="frmORProcessing" method="post">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">ADVANCE PAYMENT >> OR PROCESSING
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			 </a></li>	
			  <li class="li_nc"><a href="#" onClick="javascript:cmdBuild_OnClick()">|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Build Invoice List&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>			  			  
			  <li class="li_nc"><a href="#" onClick="javascript:cmdProcess_OnClick()">Process&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>			  			  
			  <li class="li_nc"><a href="#" onClick="javascript:cmdCancel_OnClick()">Cancel&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>				  
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
										<td class="fieldname" align="right">INVOICE DATE FROM (mm/dd/yyyy) :</td>
										<td width="10"></td>
										<td><input type=text name="txtDateFrom" id="txtDateFrom" class="values" size="20" maxlength="10" value="<?php echo $dtBillingFrom; ?>"></td>
									</tr>	
									<tr>
										<td class="fieldname" align="right">TO (mm/dd/yyyy) :</td>
										<td width="10"></td>
										<td><input type=text name="txtDateTo" id="txtDateTo" class="values" size="20" maxlength="10"  value="<?php echo $dtBillingTo; ?>"></td>
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
										<td class="fieldname" align="right">&nbsp;</td>
										<td width="10">&nbsp;</td>
										<td>&nbsp;</td>
									</tr>																			
								</table>
							</td>
						</tr>						
					</table>			
					<?php if ($strMode != "" && $strMsg <> "") { ?>			
						<table>
							<tr>
								<td class="values" style="color:#CC0000"><?php echo $strMsg; ?></td>
							</tr>
						</table>	
					<?php } ?>	
					<table width="800" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
						<tr height="30">				
							<td width="4%" class="tablehdr" align="center">Sel
							</td>			
							<td  width="14%" class="tablehdr" align="center">Invoice No.
							</td>
							<td  width="9%" class="tablehdr" align="center">Date
							</td>
							<td width="25%" class="tablehdr" align="center">Client
							</td>														
							<td width="9%" class="tablehdr" align="center">Balance Amount
							</td>			
							<td width="9%" class="tablehdr" align="center">Paid Amount
							</td>	
							<td width="8%" class="tablehdr" align="center">Total Amount
							</td>	
							<td width="11%" class="tablehdr" align="center">&nbsp;OR Breakdown&nbsp;
							</td>	
						</tr>
						<?php
						while(odbc_fetch_row($process)){
							$strInvoiceNo = odbc_result($process,"invoice_no");
							$dtInvoice = odbc_result($process,"invoice_date");
							//$intInvoiceDetailID = odbc_result($process,"invoice_detail_id");
							$strClientName = replacedoublequotes(odbc_result($process,"client_name"));							
							$dblTotalChargeAmount = odbc_result($process,"total_charge_amount");
							$dblPaidAmount = odbc_result($process,"paid_amount");
							$dblBalanceAmount =  odbc_result($process,"balance_amount");
							$status = odbc_result($process,"status");
							$or_breakdown = odbc_result($process,"or_breakdown");
							
							if ($dblBalanceAmount > 0) {
								$ctr = $ctr+1;
								
								if ($ctr%2==1) 
									$rowColor = "98fb98";	
								else
									$rowColor = "ffffe0";	
						?>
						<tr bgcolor="<?php echo "$rowColor"; ?>">							
							<td width="4%" class="values" align="center">
								<?php if ($status!="") { ?>
									<input type="checkbox" name="chkSelect<?php echo $ctr;?>" id="chkSelect<?php echo $ctr;?>" value="<?php echo $ctr;?>">								
								<?php } else { ?>
									<input type="checkbox" name="chkSelect<?php echo $ctr;?>" disabled id="chkSelect<?php echo $ctr;?>" value="<?php echo $ctr;?>">								
								<?php } ?>
								<input type="hidden" name="hidStatus<?php echo $ctr;?>" id="hidStatus<?php echo $ctr;?>" value="<?php echo "$status";?>">						
							</td>
							<td width="14%" class="values">&nbsp;<?php echo "$strInvoiceNo";?>
								<input type="hidden" name="hidInvoiceNo<?php echo $ctr;?>" id="hidInvoiceNo<?php echo $ctr;?>" value="<?php echo "$strInvoiceNo";?>">														
							</td>
							<td width="9%" class="values" align="center">&nbsp;<?php echo "$dtInvoice";?>&nbsp;
							</td>
							<td width="25%" class="values">&nbsp;<?php echo "$strClientName";?>&nbsp;
								<input type="hidden" name="hidClientName<?php echo $ctr;?>" id="hidClientName<?php echo $ctr;?>" value="<?php echo "$strClientName";?>">														
							</td>														
							<td width="9%" class="values" align="right">&nbsp;<?php echo "$dblBalanceAmount";?>&nbsp;
							</td>					
							<td width="9%" class="values" align="right">&nbsp;<?php echo "$dblPaidAmount";?>&nbsp;
							</td>					
							<td width="8%" class="values" align="right">&nbsp;<?php echo "$dblTotalChargeAmount";?>&nbsp;
							</td>				
							<?php if ($or_breakdown == "Y") { ?>	
								<td width="11%" class="values" align="center">&nbsp;
									<img id="cmdORBreakdown" name="cmdORBreakdown" onClick="javascript:cmdORBreakdown_OnClick(<?php echo $ctr;?>);" src="images/page_edit.gif" style="cursor:hand" alt="OR Breakdown">
								</td>	
							<?php } else { ?>
								<td width="11%" class="values" align="center">&nbsp;
								</td>	
							<?php } ?>
						</tr>						
						<?php } } ?>
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

<script type="text/javascript">
function hov(loc,cls) {   
	if(loc.className)   
	loc.className=cls;   
} 

function save_text()
   {
   var w = frmORProcessing.cboRealProperty.selectedIndex;
   frmORProcessing.hidRealPropertyName.value = frmORProcessing.cboRealProperty.options[w].text;
   }

function txtKeyword_onKeyUp(e) {
	if (e.keyCode==13) {
		cmdSearch_onClick();
	}
}

function chkSelectAll_OnClick() {
	var ctr
	ctr = frmORProcessing.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmORProcessing.chkSelect" + i);
		obj1 = eval("frmORProcessing.hidStatus" + i);
		if (frmORProcessing.chkSelectAll.checked == true) {
			if (obj1.value == "P")
				obj.checked = true;
		}
		else {
			obj.checked = false;
		}
	}
}

function cmdBuild_OnClick() {
	if (frmORProcessing.txtDateFrom.value == "") {
		alert("Please provide Billing From date")
		frmORProcessing.txtDateFrom.focus()
		return false
	}
	if (frmORProcessing.txtDateFrom.value != "") {
		if (isDate(frmORProcessing.txtDateFrom.value)==false) {
			frmORProcessing.txtDateFrom.focus()
			return false
		}
	}
	
	if (frmORProcessing.txtDateTo.value == "") {
		alert("Please provide Billing To date")
		frmORProcessing.txtDateTo.focus()
		return false
	}
	if (frmORProcessing.txtDateTo.value != "") {
		if (isDate(frmORProcessing.txtDateTo.value)==false) {
			frmORProcessing.txtDateTo.focus()
			return false
		}
	}
	
	if (frmORProcessing.txtDateFrom.value > frmORProcessing.txtDateTo.value) {
		alert ("Date From should be earlier than Date To")
		frmInvoiceReport.txtDateFrom.focus()
		return false
	}
	
	frmORProcessing.hidMode.value = "BUILD";
	frmORProcessing.submit();
}

function cmdProcess_OnClick() {
	var j
	j=0
	totalctr = frmORProcessing.hidRowCtr.value;
	for (i=1;i<=totalctr;i++) {
		obj = eval("frmORProcessing.chkSelect" + i);
		if (obj.checked == true) {
			j++;
		}
	}

	if (j > 0) {
		if (confirm("Are you sure you want to process OR's for these invoices? There is no undo for this operation.")) {
			frmORProcessing.hidMode.value = "PROCESS";
			frmORProcessing.submit();
		}
	}
	else {
		alert("Select invoice/s first");
		return false;
	}
}

function cmdCancel_OnClick() {
	parent.frames[2].location = "advance_payment_or_posting.php?menu_id=" + frmORProcessing.hidMenuID.value;
	frmORProcessing.submit();
}

function change_loc(pFile) {
	parent.frames[2].location = pFile;
	return false;
}

function cmdORBreakdown_OnClick(pCtr) {
	obj = eval("frmORProcessing.hidInvoiceNo" + pCtr);
	obj1 = eval("frmORProcessing.hidClientName" + pCtr);
	parent.frames[2].location = "advance_payment_or_processing_breakdown_main.php?menu_id=" + frmORProcessing.hidMenuID.value + "&invoice_no=" + obj.value + "&client_name=" + obj1.value;
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