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
$strTenantCode = "";
$strTenantName = "";
$strChargeCode = "";	
$strChargeDesc = "";		
$dblChargeAmount = 0;		
$strRemarks = "";
$strMsg = "";
$strMenu = "";

if ($_GET["mode"] == "FIND") {
	$strMenu = $_GET["menu"];
	$strORNo = $_GET["or_no"];
	$strClientCode = $_GET["client_code"];
	$strClientName = $_GET["client_name"];
	$strMode = "FIND";
}
//echo $strMode;
if ($strMode != "") {	
	//echo $strMode;
	switch ($strMode) {
	
		case "APPLY":
			$i = 1;
			$j = 0;
			$k = 0;
			$strORNo = $_POST["hidORNo"];
			while ($i <= $_POST["hidRowCtr"]) {
				if (isset($_POST["chkSelect" . strval($i)])) {		
					$strTenantCode = $_POST["hidTenantCode" . strval($i)];		
					$strChargeCode = $_POST["hidChargeCode" . strval($i)];		
					$sqlquery="exec sp_t_Advance_Payment_Detail 'ADD_ENTRY',0,'" . $strORNo . "','" . $strTenantCode . "','" . $strChargeCode . "',0,'','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
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
				$strMsg = "Some records were already added in this OR.";
			}
			elseif ($j == 0 && $k > 0) {
				$strMsg = "Record/s added!";
			}
			elseif ($j > 0 && $k == 0) {
				$strMsg = "All selected records were added already in this OR.";
			}
			else
				$strMsg = "No record added!";
				
			$strORNo = $_POST["hidORNo"];
			break;
	}
}

$strKeyWord = $_POST["txtKeyWord"];
$sqlquery="exec sp_t_Advance_Payment_Detail 'VIEW_ENTRY','0','" . $strORNo . "','','',0,'','" . $strKeyWord . "','',''";	
$process=odbc_exec($sqlconnect, $sqlquery);
//echo $sqlquery;

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
<form name="frmAdvancePaymentDtlAdd" id="frmAdvancePaymentDtlAdd" method="post" action="advance_payment_detail_add_entries.php?menu_id=<?php echo $menu_id;?>">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME"><?php echo strtoupper($strClientName); ?> >> ADVANCE PAYMENT DETAIL >> ADD CHARGES
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			 </a></li>	
			 <li class="li_nc"><a href="#" onClick="javascript:cmdSearch_OnClick()">|&nbsp;&nbsp;&nbsp;&nbsp;Search&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>			  			  
  			 <li class="li_nc"><a href="#" onClick="javascript:cmdApply_OnClick()">Apply&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>			  			  
			 <li class="li_nc"><a href="#" onClick="javascript:change_loc('advance_payment_detail.php?menu_id=<?php echo $menu_id;?>&mode=FIND&menu=DETAIL&or_no=<?php echo $strORNo;?>&client_code=<?php echo $strClientCode;?>&client_name=<?php echo $strClientName;?>')">Back&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
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
							<td class="fieldname">Type keyword here :</td>
							<td width="5"></td>
							<td><input type=text name="txtKeyword" id="txtKeyword" class="values" size="40" value="<?php echo $strKeyword ?>" onKeyUp="javascript:txtKeyword_onKeyUp(event)"></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table width="600" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
						<tr height="30">				
							<td width="7%" class="tablehdr" align="center">Sel
							</td>			
							<td  width="49%" class="tablehdr" align="center">Tenant
							</td>
							<td  width="27%" class="tablehdr" align="center">Charge
							</td>
							<td width="17%" class="tablehdr" align="center">Amount
							</td>														
						</tr>
						<?php
						while(odbc_fetch_row($process)){
							$strTenantCode = replacedoublequotes(odbc_result($process,"tenant_code"));
							$strTenantName = replacedoublequotes(odbc_result($process,"tenant_name"));
							$strChargeCode = replacedoublequotes(odbc_result($process,"charge_code"));
							$strChargeDesc = replacedoublequotes(odbc_result($process,"charge_desc"));
							$dblChargeAmount = odbc_result($process,"charge_amount");
							$ctr = $ctr+1;
							
							if ($ctr%2==1) 
								$rowColor = "98fb98";	
							else
								$rowColor = "ffffe0";			
						?>
						<tr bgcolor="<?php echo "$rowColor"; ?>">							
							<td width="7%" class="values" align="center">								
								<input type="checkbox" name="chkSelect<?php echo $ctr;?>" id="chkSelect<?php echo $ctr;?>" value="<?php echo $ctr;?>">								
							</td>
							<td width="49%" class="values">&nbsp;<?php echo "$strTenantName";?>
								<input type="hidden" name="hidTenantCode<?php echo "$ctr";?>" id="hidTenantCode<?php echo "$ctr";?>" value="<?php echo "$strTenantCode";?>">						
							</td>							
							<td width="27%" class="values">&nbsp;<?php echo "$strChargeDesc";?>&nbsp;
								<input type="hidden" name="hidChargeCode<?php echo "$ctr";?>" id="hidChargeCode<?php echo "$ctr";?>" value="<?php echo "$strChargeCode";?>">	
							</td>							
							<td width="17%" class="values" align="right">&nbsp;<?php echo "$dblChargeAmount";?>&nbsp;
							</td>		
						</tr>						
						<?php } ?>
					</table>	
					<table>
						<tr>
							<td class="values">&nbsp;&nbsp;
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
	<input type="hidden" id="hidORNo" name="hidORNo" value="<?php echo $strORNo;?>">
	<input type="hidden" id="hidClientCode" name="hidClientCode" value="<?php echo $strClientCode;?>">
	<input type="hidden" id="hidClientName" name="hidClientName" value="<?php echo $strClientName;?>">
</form>
</body> 
</html>

<script type="text/javascript">

function cmdSearch_OnClick() {
	frmAdvancePaymentDtlAdd.hidMode.value = "SEARCH";
	frmAdvancePaymentDtlAdd.submit();
}

function chkSelectAll_OnClick() {
	var ctr
	ctr = frmAdvancePaymentDtlAdd.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmAdvancePaymentDtlAdd.chkSelect" + i);
		if (frmAdvancePaymentDtlAdd.chkSelectAll.checked == true) {
			obj.checked = true;
		}
		else {
			obj.checked = false;
		}
	}
}

function editMode(ctr) {
	if (frmAdvancePaymentDtlAdd.hidSaveMode.value != "EDIT_CHARGE") {
		frmAdvancePaymentDtlAdd.hidCurRow.value = ctr;
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
		frmAdvancePaymentDtlAdd.chkSelectAll.disabled = true;
		frmAdvancePaymentDtlAdd.chkSelectAll.checked = false;
		frmAdvancePaymentDtlAdd.cboAddCharge.disabled = true;
		frmAdvancePaymentDtlAdd.txtAddChargeAmount.disabled = true;
		frmAdvancePaymentDtlAdd.hidSaveMode.value = "EDIT_CHARGE";
	}
}

function enabledisablechkboxes(pVal) {
	var ctr
	ctr = frmAdvancePaymentDtlAdd.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmAdvancePaymentDtlAdd.chkDelete" + i);		
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

function cmdFillupRemarks_OnClick(ctr) {
	frmAdvancePaymentDtlAdd.hidCurRow.value = ctr;
	obj = eval("frmAdvancePaymentDtlAdd.hidEditChargeCode" + ctr);
	frmAdvancePaymentDtlAdd.hidChargeCode.value = obj.value;
	obj = eval("frmAdvancePaymentDtlAdd.hidEditRecID" + ctr);
	frmAdvancePaymentDtlAdd.hidRecID.value = obj.value;
	parent.frames[2].location = "invoice_detail_reading.php?invoice_no=" + frmAdvancePaymentDtlAdd.hidInvoiceNo.value + "&charge_code=" + frmAdvancePaymentDtlAdd.hidChargeCode.value + "&rec_id=" + frmAdvancePaymentDtlAdd.hidRecID.value;
	return false;
}

function cmdSave_OnClick() {
	if (frmAdvancePaymentDtlAdd.hidInvoiceNo.value == "") {
		alert("Select Invoice first!")
		frmAdvancePaymentDtlAdd.txtInvoiceNo.focus()
		return false
	}
	
	if (frmAdvancePaymentDtlAdd.hidSaveMode.value == "EDIT_CHARGE") {
		ctr = frmAdvancePaymentDtlAdd.hidCurRow.value;
		
		obj = eval("frmAdvancePaymentDtlAdd.cboEditCharge" + ctr);
		if (obj.value == "") {
			alert("Charge is required")
			obj.focus()
			return false
		}
		frmAdvancePaymentDtlAdd.hidChargeCode.value = obj.value;
		
		obj = eval("frmAdvancePaymentDtlAdd.hidEditRecID" + ctr);
		frmAdvancePaymentDtlAdd.hidRecID.value = obj.value;
		
		obj = eval("frmAdvancePaymentDtlAdd.txtEditChargeAmount" + ctr);
		if (isNaN(obj.value) && obj.value != "") {
			alert("Invalid numeric value");
			obj.focus();
			return false;
		}
		obj = eval("frmAdvancePaymentDtlAdd.txtEditChargeAmount" + ctr);
		if (obj.value == "" || obj.value == 0) {
			alert("Charge Amount is required")
			obj.focus()
			return false
		}
		frmAdvancePaymentDtlAdd.hidChargeAmount.value = obj.value;
		frmAdvancePaymentDtlAdd.hidSaveMode.value = "EDIT_CHARGE";
	}
	else {
		if (frmAdvancePaymentDtlAdd.cboAddCharge.value == "") {
			alert("Charge is required")
			frmAdvancePaymentDtlAdd.cboAddCharge.focus()
			return false
		}
		if (isNaN(frmAdvancePaymentDtlAdd.txtAddChargeAmount.value) && frmAdvancePaymentDtlAdd.txtAddChargeAmount.value != "") {
			alert("Invalid numeric value")
			frmAdvancePaymentDtlAdd.txtAddChargeAmount.focus()
			return false
		}
		if (frmAdvancePaymentDtlAdd.txtAddChargeAmount.value=="" || frmAdvancePaymentDtlAdd.txtAddChargeAmount.value == 0) {
			alert("Charge Amount is required")
			frmAdvancePaymentDtlAdd.txtAddChargeAmount.focus()
			return false
		}
		frmAdvancePaymentDtlAdd.hidChargeCode.value = frmAdvancePaymentDtlAdd.cboAddCharge.value;
		frmAdvancePaymentDtlAdd.hidChargeAmount.value = frmAdvancePaymentDtlAdd.txtAddChargeAmount.value;
		frmAdvancePaymentDtlAdd.hidRecID.value = 0;
		frmAdvancePaymentDtlAdd.hidSaveMode.value = "EDIT";
	}
	frmAdvancePaymentDtlAdd.hidMode.value = "SAVE";
	frmAdvancePaymentDtlAdd.submit();
}

function cmdRetrieve_OnClick() {
	frmAdvancePaymentDtlAdd.hidMode.value = "RETRIEVE";
	frmAdvancePaymentDtlAdd.submit();
}

function cmdCancel_OnClick() {
	ctr = frmAdvancePaymentDtlAdd.hidCurRow.value;
	//alert(frmAdvancePaymentDtlAdd.hidSaveMode.value)
	if (frmAdvancePaymentDtlAdd.hidSaveMode.value == "EDIT_CHARGE") {
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
		
		frmAdvancePaymentDtlAdd.cboAddCharge.disabled = false;
		frmAdvancePaymentDtlAdd.txtAddChargeAmount.disabled = false;
		frmAdvancePaymentDtlAdd.chkSelectAll.disabled = false;
		frmAdvancePaymentDtlAdd.chkSelectAll.checked = false;
		frmAdvancePaymentDtlAdd.hidSaveMode.value = "";
		enabledisablechkboxes(1);
		return false;
	}
	else {
		parent.frames[2].location = "invoice_detail.php";
		return false;
	}
}

function cmdApply_OnClick() {
	var j
	j=0
	totalctr = frmAdvancePaymentDtlAdd.hidRowCtr.value;
	for (i=1;i<=totalctr;i++) {
		obj = eval("frmAdvancePaymentDtlAdd.chkSelect" + i);
		if (obj.checked == true) {
			j++;
		}
	}
	if (j <= 0) {
		alert("Select record/s first.");
		return false;
	}
	else {
		frmAdvancePaymentDtlAdd.hidMode.value = "APPLY";
		frmAdvancePaymentDtlAdd.submit();
	}
}

function change_loc(pFile) {
	parent.frames[2].location = pFile;
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

