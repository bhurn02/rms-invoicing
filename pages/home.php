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

//end access
$sqlconnect = connection();
$strIPAddr = $_SERVER["REMOTE_ADDR"];
$company_code = $sessCompanyCode;
$uid = $sessUserID;

$dtToday = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	

$reading_wout_invoice = 0;
$unposted_inv = 0;
$unposted_or_unpaid = 0;
$unposted_or_ar = 0;
$tenants_wout_charges = 0;
$units_wout_charges = 0;

$strMsg = "";
$strSaveMode = "";
$strMode = $_POST["hidMode"];
$strSaveMode = $_POST["hidSaveMode"];

$sqlquery="exec sp_s_Statistics";	
//echo $sqlquery;
$process=odbc_exec($sqlconnect, $sqlquery);
if (odbc_fetch_row($process)) {
		$reading_wout_invoice = odbc_result($process,"reading_wout_invoice");
		$unposted_inv = odbc_result($process,"unposted_inv");
		$unposted_or_unpaid = odbc_result($process,"unposted_or_unpaid");
		$unposted_or_ar = odbc_result($process,"unposted_or_ar");
		$tenants_wout_charges = odbc_result($process,"tenants_wout_charges");
		$units_wout_charges = odbc_result($process,"units_wout_charges");
}

if ($strMsg != ""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 

?>

<html> 
<head> 
<title>HOME</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color: #F3F5B4; background-image: url(images/rmslogoyellow.jpg)">
<form name="frmTenant" id="frmTenant" method="post" action="tenant.php?menu_id=<?php echo $menu_id;?>">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">TODAY IS : <?php echo $dtToday;?>
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			 </a></li>	
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
								<table cellspacing="0">
									<tr>
										<td width="100"></td>
										<td class="homestyle" style="border-bottom:1px solid; border-bottom-color:green;">&nbsp;</td>
										<td width="200" align="right" class="homestyle" style="border-bottom:1px solid; border-bottom-color:green;">&nbsp;</td>										
									</tr>
									<tr height="20">
										<td width="100">&nbsp;</td>
										<td>&nbsp;</td>
										<td width="200">&nbsp;</td>
									</tr>
									<tr>
										<td width="100"></td>
										<?php if ($reading_wout_invoice>0) {?>
											<td class="homestyle" style="border-bottom:1px solid; border-bottom-color:green;cursor:hand;" onClick="javascript:change_loc('tenant_reading_list.php?menu_id=6&src=home')">TENANT READINGS WITHOUT INVOICE :</td>
										<?php } else {?>
											<td class="homestyle" style="border-bottom:1px solid; border-bottom-color:green;">TENANT READINGS WITHOUT INVOICE :</td>
										<?php } ?>										
										<td width="200" align="right" class="homestyle" style="border-bottom:1px solid; border-bottom-color:green;"><?php echo $reading_wout_invoice;?></td>										
									</tr>
									<tr height="20">
										<td width="100">&nbsp;</td>
										<td>&nbsp;</td>
										<td width="200">&nbsp;</td>
									</tr>	
									<tr>
										<td width="100"></td>
										<?php if ($unposted_inv>0) {?>
											<td class="homestyle" style="border-bottom:1px solid; border-bottom-color:green;cursor:hand;" onClick="javascript:change_loc('invoice_list.php?menu_id=8&src=home')">UNPOSTED INVOICES :</td>
										<?php } else {?>
											<td class="homestyle" style="border-bottom:1px solid; border-bottom-color:green;">UNPOSTED INVOICES :</td>
										<?php } ?>		
										<td width="200" align="right" class="homestyle" style="border-bottom:1px solid; border-bottom-color:green;"><?php echo $unposted_inv;?></td>										
									</tr>
									<tr height="20">
										<td width="100">&nbsp;</td>
										<td>&nbsp;</td>
										<td width="200">&nbsp;</td>
									</tr>	
									<tr>
										<td width="100"></td>
										<?php if ($unposted_or_unpaid>0) {?>
											<td class="homestyle" style="border-bottom:1px solid; border-bottom-color:green;cursor:hand;" onClick="javascript:change_loc('payment_header_list.php?menu_id=10&src=home')">UNPOSTED OR FOR UNPAID BILLS :</td>
										<?php } else {?>
											<td class="homestyle" style="border-bottom:1px solid; border-bottom-color:green;">UNPOSTED OR FOR UNPAID BILLS :</td>
										<?php } ?>		
										<td width="200" align="right" class="homestyle" style="border-bottom:1px solid; border-bottom-color:green;"><?php echo $unposted_or_unpaid;?></td>										
									</tr>
									<tr height="20">
										<td width="100">&nbsp;</td>
										<td>&nbsp;</td>
										<td width="200">&nbsp;</td>
									</tr>	
									<tr>
										<td width="100"></td>
										<?php if ($unposted_or_ar>0) {?>
											<td class="homestyle" style="border-bottom:1px solid; border-bottom-color:green;cursor:hand;" onClick="javascript:change_loc('advance_payment_header_list.php?menu_id=11&src=home')">UNPOSTED OR FOR ADVANCE PAYMENTS :</td>
										<?php } else {?>
											<td class="homestyle" style="border-bottom:1px solid; border-bottom-color:green;">UNPOSTED OR FOR ADVANCE PAYMENTS :</td>
										<?php } ?>	
										<td width="200" align="right" class="homestyle" style="border-bottom:1px solid; border-bottom-color:green;"><?php echo $unposted_or_ar;?></td>										
									</tr>
									<tr height="20">
										<td width="100" height="23">&nbsp;</td>
										<td>&nbsp;</td>
										<td width="200">&nbsp;</td>
									</tr>	
									<tr>
										<td width="100"></td>
										<?php if ($tenants_wout_charges>0) {?>
											<td class="homestyle" style="border-bottom:1px solid; border-bottom-color:green;cursor:hand;" onClick="javascript:change_loc('tenant_list.php?menu_id=4&src=home')">TENANTS WITHOUT CHARGES :</td>
										<?php } else {?>
											<td class="homestyle" style="border-bottom:1px solid; border-bottom-color:green;">TENANTS WITHOUT CHARGES :</td>
										<?php } ?>	
										<td width="200" align="right" class="homestyle" style="border-bottom:1px solid; border-bottom-color:green;"><?php echo $tenants_wout_charges;?></td>										
									</tr>
									<tr height="20">
										<td width="100">&nbsp;</td>
										<td>&nbsp;</td>
										<td width="200">&nbsp;</td>
									</tr>	
									<tr>
										<td width="100"></td>
										<?php if ($units_wout_charges>0) {?>
											<td class="homestyle" style="border-bottom:1px solid; border-bottom-color:green;cursor:hand;" onClick="javascript:change_loc('units_list.php?menu_id=2&src=home')">UNITS WITHOUT CHARGES :</td>
										<?php } else {?>
											<td class="homestyle" style="border-bottom:1px solid; border-bottom-color:green;">UNITS WITHOUT CHARGES :</td>
										<?php } ?>	
										<td width="200" align="right" class="homestyle" style="border-bottom:1px solid; border-bottom-color:green;"><?php echo $units_wout_charges;?></td>										
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

function chkAffiliate_onClick() {
	if (frmTenant.chkAffiliate.checked == true) {
		frmTenant.txtNewCode.disabled = false		
		frmTenant.txtBusinessArea.disabled = false		
	}
	else {
		frmTenant.txtNewCode.disabled = true		
		frmTenant.txtBusinessArea.disabled = true		
	}
	return false
}


function chkTerminated_onClick() {
	if (frmTenant.chkTerminated.checked == true) {
		frmTenant.txtDateTerminated.disabled = false		
	}
	else {
		frmTenant.txtDateTerminated.disabled = true		
	}
	return false
}

function cmdSave_OnClick() {	
	if (trim(frmTenant.txtName.value) == "") {
		alert("Tenant Name is required")
		frmTenant.txtName.focus()
		return false
	}
	
	if (frmTenant.cboTenantType.value == "OC" || frmTenant.cboTenantType.value == "O") {
		if (frmTenant.hidRealProperty.value == "") {
			alert("Real Property is required. Please use the Real Property Look up button.")
			frmTenant.cmdRealPropertySearch.focus()
			return false
		}
		if (trim(frmTenant.txtBuildingCode.value) == "") {
			alert("Building Code is required. Please use the Real Property Look up button.")
			frmTenant.txtBuildingCode.focus()
			return false
		}
		if (trim(frmTenant.txtUnitNo.value) == "") {
			alert("Unit is required. Please use the Real Property Look up button.")
			frmTenant.txtUnitNo.focus()
			return false	
		}
	}
	
	if (trim(frmTenant.txtBuildingCode.value) != "" || trim(frmTenant.txtUnitNo.value) != "") {
		if (trim(frmTenant.hidRealProperty.value) == "" || trim(frmTenant.txtRealProperty.value) == "") {
			alert("Please provide Real Property by clicking the Real Property Look up button.")
			frmTenant.cmdRealPropertySearch.focus()
			return false
		}
		if (trim(frmTenant.txtBuildingCode.value) == "") {
			alert("Building Code is required. Please use the Real Property Look up button.")
			frmTenant.txtBuildingCode.focus()
			return false
		}
		if (trim(frmTenant.txtUnitNo.value) == "") {
			alert("Unit is required. Please use the Real Property Look up button.")
			frmTenant.txtUnitNo.focus()
			return false	
		}
	}
	
	if (frmTenant.cboTenantType.value == "O") {
		if (trim(frmTenant.txtBillTo.value) == "" || trim(frmTenant.hidBillTo.value) == "") {
			alert("Bill To is required. Please use the Client Look up button.")
			frmTenant.txtBillTo.focus()
			return false
		}
		
	}
	
	if (trim(frmTenant.txtSAPCode.value) == "") {
		alert("Account Code is required")
		frmTenant.txtSAPCode.focus()
		return false
	}
	
	if (frmTenant.chkAffiliate.checked ==true) {
		if (frmTenant.txtNewCode.value == "") {
			alert("New Code is required")
			frmTenant.txtNewCode.focus()
			return false
		}
		
		if (trim(frmTenant.txtBusinessArea.value) == "") {
			alert("Business Area is required")
			frmTenant.txtBusinessArea.focus()
			return false
		}
	}
		
	if (trim(frmTenant.txtEffDate.value) != "") {
		if (isDate(frmTenant.txtEffDate.value)==false){
			frmTenant.txtEffDate.focus()
			return false
		}
	}

	if (trim(frmTenant.txtExpiryDate.value) != "") {
		if (isDate(frmTenant.txtExpiryDate.value)==false) {
			frmTenant.txtExpiryDate.focus()
			return false
		}
	}
	
	if (trim(frmTenant.txtActualMoveInDate.value) != "") {
		if (isDate(frmTenant.txtActualMoveInDate.value)==false) {
			frmTenant.txtActualMoveInDate.focus()
			return false
		}
	}
	
	if (frmTenant.chkTerminated.checked ==true) {
		if (isDate(frmTenant.txtDateTerminated.value)==false){
			frmTenant.txtDateTerminated.focus()
			return false
		}
	}
	
	if (trim(frmTenant.txtEmailAdd.value) != "") {
		if (checkValidEmail(frmTenant.txtEmailAdd)==false){
			frmTenant.txtEmailAdd.focus()
			return false		
		}
	}

	if (trim(frmTenant.txtBuildingCode.value) == "" || trim(frmTenant.txtUnitNo.value) == "") {
		frmTenant.hidBuildingCode.value = ""
		frmTenant.hidUnitNo.value = ""
	}
	else {
		frmTenant.hidBuildingCode.value = frmTenant.txtBuildingCode.value
		frmTenant.hidUnitNo.value = frmTenant.txtUnitNo.value
	}
	
	if (trim(frmTenant.txtRealProperty.value) == "" || frmTenant.cboTenantType.value == "C") {
		frmTenant.hidRealProperty.value = ""
		frmTenant.hidBuildingCode.value = ""
		frmTenant.hidUnitNo.value = ""
	}
	frmTenant.hidMode.value = "SAVE";
	frmTenant.submit();
}

function cmdRetrieve_OnClick() {
	frmTenant.hidCode.value = frmTenant.txtCode.value;
	frmTenant.hidMode.value = "RETRIEVE";
	frmTenant.submit();
}

function cmdCancel_OnClick() {
	parent.frames[2].location = "tenant.php?menu_id=" + frmTenant.hidMenuID.value;
	return false;
}

function cmdDelete_OnClick() {
	if (frmTenant.txtCode.value == "") {
		alert("Tenant Code is blank")
		frmTenant.txtCode.focus()
		return false
	}
	else {
		if (confirm("Are you sure you want to delete this record?")) {
			frmTenant.hidMode.value = "DELETE";
			frmTenant.submit();
		}
	}
}

function change_loc(pFile) {
	parent.frames[2].location = pFile;
	return false;
}

function cmdRealPropertySearch_onClick() {
	window.open ("real_property_search.php?menu_id=" + frmTenant.hidMenuID.value,"displayWindow","type=fullwindow,titlebar=no,scrollbars=yes");
	return false;
}

function cmdBillToSearch_onClick() {
	window.open ("tenant_search_billto.php?menu_id=" + frmTenant.hidMenuID.value,"displayWindow","type=fullwindow,titlebar=no,scrollbars=yes");
	return false;
}

function trim(stringToTrim) {
	return stringToTrim.replace(/^\s+|\s+$/g,"");
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
 
 function checkValidation(formInput) {

    if (typeof(formInput) != "object") {
        alert("Validation not supported on this browser.");
        return(false);
    }

    var message;

    if (stringEmpty(formInput.value)) {
        message = "Error! There is no input value entered.";
        alert(message);
    } else if (noAtSign( formInput.value )) {
        message = "Error! The address \"" + formInput.value + "\" does not contain an '@' character.";
        alert(message);
    } else if (nothingBeforeAt(formInput.value)) {
        message = "Error! The address \"" + formInput.value;
        message += "\" must contain at least one character before the '@' character";
        alert(message);
    } else if (noLeftBracket(formInput.value)) {
        message = "Error! The address \"" + formInput.value;
        message += "\" contains a right square bracket ']',\nbut no corresponding left square bracket '['.";
        alert(message);
    } else if (noRightBracket(formInput.value)) {
        message = "Error! The address \"" + formInput.value;
        message += "\" contains a left square bracket '[',\nbut no corresponding right square bracket ']'.";
        alert( message);
    } else if (noValidPeriod(formInput.value)) {
        message = "Error! The address \"" + formInput.value + "\" must contain a period ('.') character.";
        alert(message);
    } else if (noValidSuffix(formInput.value)) {
        message = "Error! The address \"" + formInput.value;
        message += "\" must contain a two, three or four character suffix.";
        alert(message);
    } else {
        message = "Success! The email address \"" + formInput.value + "\" validates OK.";
        //alert(message);
		return (true);
    }	

    //var objType = typeof(formInput.focus);
    //if (objType == "object" || objType == "function") {
    //     formInput.focus();
    //}

    
}

function checkValidEmail (formField) {
    if ( checkValidation ( formField ) == true ) {
        return true;
    }
	else { return false;}
}

function stringEmpty (formField) {
    // CHECK THAT THE STRING IS NOT EMPTY
    if ( formField.length < 1 ) {
        return ( true );
    } else {
        return ( false );
    }
}

function noAtSign (formField) {
    // CHECK THAT THERE IS AN '@' CHARACTER IN THE STRING
    if (formField.indexOf ('@', 0) == -1) {
        return ( true )
    } else {
        return ( false );
    }
}

function nothingBeforeAt (formField) {
    // CHECK THERE IS AT LEAST ONE CHARACTER BEFORE THE '@' CHARACTER
    if ( formField.indexOf ( '@', 0 ) < 1 ) {
        return ( true )
    } else {
        return ( false );
    }
}

function noLeftBracket (formField) {
    // IF EMAIL ADDRESS IN FORM 'user@[255,255,255,0]', THEN CHECK FOR LEFT BRACKET
    if ( formField.indexOf ( '[', 0 ) == -1 && formField.charAt (formField.length - 1) == ']') {
        return ( true )
    } else {
        return ( false );
    }
}

function noRightBracket (formField) {
    // IF EMAIL ADDRESS IN FORM 'user@[255,255,255,0]', THEN CHECK FOR RIGHT BRACKET
    if (formField.indexOf ( '[', 0 ) > -1 && formField.charAt (formField.length - 1) != ']') {
        return ( true );
    } else {
        return ( false );
    }
}

function noValidPeriod (formField) {
    // IF EMAIL ADDRESS IN FORM 'user@[255,255,255,0]', THEN WE ARE NOT INTERESTED
    if (formField.indexOf ( '@', 0 ) > 1 && formField.charAt (formField.length - 1 ) == ']')
        return ( false );

    // CHECK THAT THERE IS AT LEAST ONE PERIOD IN THE STRING
    if (formField.indexOf ( '.', 0 ) == -1)
        return ( true );

    return ( false );
}

function noValidSuffix(formField) {
    // IF EMAIL ADDRESS IN FORM 'user@[255,255,255,0]', THEN WE ARE NOT INTERESTED
    if (formField.indexOf('@', 0) > 1 && formField.charAt(formField.length - 1) == ']') {
        return ( false );
    }

    // CHECK THAT THERE IS A TWO OR THREE CHARACTER SUFFIX AFTER THE LAST PERIOD
    var len = formField.length;
    var pos = formField.lastIndexOf ( '.', len - 1 ) + 1;
    if ( ( len - pos ) < 2 || ( len - pos ) > 4 ) {
        return ( true );
    } else {
        return ( false );
    }
}
</script>