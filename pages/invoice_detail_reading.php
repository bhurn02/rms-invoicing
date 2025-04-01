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

$intInvoiceDetailID = 0;
$intInvoiceDetailReadingID = 0;
$intReadingID = 0;
$dtReadingFrom = "";
$dtReadingTo = "";	
$dblPrevReading = 0;
$dblCurrentReading = 0;
$strRemarks = "";	

$strMsg = "";
$strSaveMode = "";
$strMode = $_POST["hidMode"];
$strSaveMode = $_POST["hidSaveMode"];

$strInvoiceNo = $_GET["invoice_no"];
$strChargeCode = $_GET["charge_code"];
$intInvoiceDetailID = $_GET["invoice_detail_id"];
$intInvoiceDetailReadingID = $_GET["invoice_detail_reading_id"];
$intReadingID = $_GET["reading_id"];
$strReadingID = $_GET["reading_id"];
//$strMode = "RETRIEVE";

//echo $strMode;
if ($_POST["hidMode"] == "SAVE") {
	$dtReadingFrom = replacesinglequote($_POST["txtReadingFrom"]);
	$dtReadingTo = replacesinglequote($_POST["txtReadingTo"]);	
	if ($_POST["txtPrevReading"] == "")
		$dblPrevReading = 0;
	else
		$dblPrevReading = $_POST["txtPrevReading"];
		
	if ($_POST["txtCurrentReading"] == "")
		$dblCurrentReading = 0;
	else
		$dblCurrentReading = $_POST["txtCurrentReading"];
	
	$strRemarks = replacesinglequote($_POST["txtRemarks"]);	
}
	//echo $strMode;
	
if ($_POST["hidMode"] == "SAVE") {
	$sqlquery="exec sp_t_InvoiceDetailReading 'SAVE','" . $strInvoiceNo . "'," . $intInvoiceDetailID . "," . $intInvoiceDetailReadingID . "," . $intReadingID . ",'" . $dtReadingFrom . "','" . $dtReadingTo . "'," . $dblPrevReading . "," . $dblCurrentReading . ",'" . $strRemarks . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
	$process=odbc_exec($sqlconnect, $sqlquery);			
	$strMsg = "Record saved!";
}
$strMode = "RETRIEVE";
$strSaveMode = "EDIT";
//echo $sqlquery;

$sqlquery="exec sp_t_InvoiceDetailReading 'RETRIEVE','" . $strInvoiceNo . "'," . $intInvoiceDetailID . "," . $intInvoiceDetailReadingID . "," . $intReadingID . ",'" . $dtReadingFrom . "','" . $dtReadingTo . "'," . $dblPrevReading . "," . $dblCurrentReading . ",'" . $strRemarks . "','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
//echo $sqlquery;
$process=odbc_exec($sqlconnect, $sqlquery);
if (odbc_fetch_row($process)) {
		$strTenantName = strtoupper(trim(odbc_result($process,"tenant_name")));
		$strChargeDesc = strtoupper(trim(odbc_result($process,"charge_desc")));
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
		$strStatus = replacedoublequotes(odbc_result($process,"status"));
		$strMode = "RETRIEVE";
		$strSaveMode = "EDIT";
}

if ($strMsg != ""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 

?>
<html> 
<head> 
<title>INVOICE DETAIL - READING</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color: #F3F5B4;">
<form name="frmInvoiceDtlReading" id="frmInvoiceDtlReading" method="post">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">INVOICE DETAIL - READING
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			 </a></li>				
			 <?php if ($strStatus == "") {?>  
		  		<li class="li_nc"><a href="#" onClick="javascript:cmdSave_OnClick()">|&nbsp;&nbsp;&nbsp;&nbsp;Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>		   
			<?php } else { ?>  
				<li><a name="Save" style="color: #666666">|&nbsp;&nbsp;&nbsp;&nbsp;Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			<?php } ?>  
		  	<li class="li_nc"><a href="#" onClick="javascript:change_loc('')">Back&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
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
										<td width="20"></td>										
										<td><input type=text name="txtInvoiceNo" id="txtInvoiceNo" disabled class="values" size="20" value="<?php echo $strInvoiceNo;?>"></td>
										<input type="hidden" name="hidInvoiceNo" id="hidInvoiceNo" value="<?php echo $strInvoiceNo;?>">
									</tr>
									<tr>
										<td class="fieldname">READING ID :<em class="requiredRed">*</em></td>
										<td width="20"></td>										
										<td><input type=text name="txtReadingID" id="txtReadingID" disabled class="values" size="20" value="<?php echo $strReadingID;?>"></td>
										<input type="hidden" name="hidReadingID" id="hidReadingID" value="<?php echo $intReadingID;?>">
									</tr>
									<tr>
										<td class="fieldname">TENANT :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtTenantName" id="txtTenantName" class="values" size="60" disabled value="<?php echo $strTenantName;?>">
										<input type="hidden" name="hidTenantCode" id="hidTenantCode" value="<?php echo $strTenantCode;?>">
										</td>
									</tr>
									<tr>
										<td class="fieldname">CHARGE :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtChargeDesc" id="txtChargeDesc" class="values" size="60" disabled value="<?php echo $strChargeDesc;?>"></td>
									</tr>									
									<tr>
										<td class="fieldname">READING FROM (mm/dd/yyyy) :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtReadingFrom" id="txtReadingFrom" class="values" size="15" maxlength="10" value="<?php echo $dtReadingFrom;?>"></td>
									</tr>
									<tr>
										<td class="fieldname">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TO (mm/dd/yyyy) :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtReadingTo" id="txtReadingTo" class="values" size="15" maxlength="10" value="<?php echo $dtReadingTo;?>"></td>
									</tr>																						
									<tr>
										<td class="fieldname">CURRENT READING :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtCurrentReading" id="txtCurrentReading" class="values" style="text-align:right" size="15" maxlength="10" value="<?php echo $dblCurrentReading;?>"></td>
									</tr>		
									<tr>
										<td class="fieldname">PREVIOUS READING :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtPrevReading" id="txtPrevReading" class="values" style="text-align:right" size="15" maxlength="10" value="<?php echo $dblPrevReading;?>"></td>
									</tr>																											
									<tr>
										<td class="fieldname">USAGE :</td>
										<td width="20"></td>
										<td class="values"><?php echo ($dblCurrentReading - $dblPrevReading) ;?></td>
									</tr>		
									<tr>
										<td class="fieldname">REMARKS :</td>
										<td width="20"></td>
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
</form>
</body> 
</html>

<script type="text/javascript">
function hov(loc,cls) {   
	if(loc.className)   
	loc.className=cls;   
} 

function cmdSave_OnClick() {
	if (frmInvoiceDtlReading.txtReadingFrom.value == "") {
		alert("Date From is required")
		frmInvoiceDtlReading.txtReadingFrom.focus()
		return false
	}
	if (isDate(frmInvoiceDtlReading.txtReadingFrom.value)==false){
		frmInvoiceDtlReading.txtReadingFrom.focus()
		return false
	}
	if (frmInvoiceDtlReading.txtReadingTo.value == "") {
		alert("Date To is required")
		frmInvoiceDtlReading.txtReadingTo.focus()
		return false
	}
	if (isDate(frmInvoiceDtlReading.txtReadingTo.value)==false){
		frmInvoiceDtlReading.txtReadingTo.focus()
		return false
	}
	
	if (frmInvoiceDtlReading.txtPrevReading.value != "" && isNaN(frmInvoiceDtlReading.txtPrevReading.value)) {
		alert("Invalid numeric value")
		frmInvoiceDtlReading.txtPrevReading.focus()
		return false
	}
	
	if (frmInvoiceDtlReading.txtCurrentReading.value != "" && isNaN(frmInvoiceDtlReading.txtCurrentReading.value)) {
		alert("Invalid numeric value")
		frmInvoiceDtlReading.txtCurrentReading.focus()
		return false
	}
	
	if (Number(frmInvoiceDtlReading.txtCurrentReading.value) < Number(frmInvoiceDtlReading.txtPrevReading.value)) {
		alert("Current Reading should be greater than Previous Reading")
		frmInvoiceDtlReading.txtCurrentReading.focus()
		return false
	}
	
	frmInvoiceDtlReading.hidMode.value = "SAVE";
	frmInvoiceDtlReading.submit();
}

function change_loc(pFile) {
	parent.frames[2].location = "invoice_detail.php?menu_id=" + frmInvoiceDtlReading.hidMenuID.value + "&mode=FIND&invoice_no=" + frmInvoiceDtlReading.hidInvoiceNo.value;
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