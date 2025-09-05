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

$strCompanyCode = $sessCompanyCode;
$strCompanyName = "";
$strDBAName = "";
$strAddress1 = "";
$strAddress2 = "";
$strContactNo1 = "";
$strContactNo2 = "";
$strFaxNo = "";
$strORCompanyName = "";
$strORAddress1 = "";
$strORAddress2 = "";
$strORContactNo1 = "";
$strORContactNo2 = "";
$strPreparedBy = "";
$strApprovedBy = "";

$strMsg = "";
$strSaveMode = "";
$strMode = $_POST["hidMode"];
$strSaveMode = $_POST["hidSaveMode"];

if ($strMode=="SAVE") {
	$strCompanyName = replacesinglequote($_POST["txtCompanyName"]);
	$strDBAName = replacesinglequote($_POST["txtDBAName"]);
	$strAddress1 = replacesinglequote($_POST["txtAddress1"]);
	$strAddress2 = replacesinglequote($_POST["txtAddress2"]);
	$strContactNo1 = replacesinglequote($_POST["txtContactNo1"]);
	$strContactNo2 = replacesinglequote($_POST["txtContactNo2"]);
	$strFaxNo = replacesinglequote($_POST["txtFaxNo"]);
	$strORCompanyName = replacesinglequote($_POST["txtORCompanyName"]);
	$strORAddress1 = replacesinglequote($_POST["txtORAddress1"]);
	$strORAddress2 = replacesinglequote($_POST["txtORAddress2"]);
	$strORContactNo1 = replacesinglequote($_POST["txtORContactNo1"]);
	$strORContactNo2 = replacesinglequote($_POST["txtORContactNo2"]);
	$strPreparedBy = replacesinglequote($_POST["txtPreparedBy"]);
	$strApprovedBy = replacesinglequote($_POST["txtApprovedBy"]);
	
	$sqlquery="exec sp_s_Configuration 'SAVE','" . $strCompanyCode . "','" . $strCompanyName . "','" . $strDBAName . "','" . $strAddress1 . "','" . $strAddress2 . "','" . $strContactNo1 . "','" . $strContactNo2 . "','" . $strFaxNo . "','" . $strORCompanyName ."','". $strORAddress1 ."','" .$strORAddress2 ."','" . $strORContactNo1 ."','" .	$strORContactNo2 ."','" . $strPreparedBy ."','" . $strApprovedBy ."','" . $uid . "','" . $strIPAddr . "'";	
	$process=odbc_exec($sqlconnect, $sqlquery);
	$strMsg = "Record saved!";
	$strMode = "";
	$strSaveMode = "";
}
			
$sqlquery="exec sp_s_Configuration 'RETRIEVE','". $strCompanyCode . "','" . $strCompanyName . "','" . $strDBAName . "','" . $strAddress1 . "','" . $strAddress2 . "','" . $strContactNo1 . "','" . $strContactNo2 . "','" . $strFaxNo . "','" . $strORCompanyName ."','". $strORAddress1 ."','" .$strORAddress2 ."','" . $strORContactNo1 ."','" .	$strORContactNo2 ."','" . $strPreparedBy ."','" . $strApprovedBy ."','" . $uid . "','" . $strIPAddr . "'";	
//echo $sqlquery;
$process=odbc_exec($sqlconnect, $sqlquery);
if (odbc_fetch_row($process)) {
	$strCompanyName = odbc_result($process,"company_name");
	$strDBAName = odbc_result($process,"dba_name");
	$strAddress1 = odbc_result($process,"address1");
	$strAddress2 = odbc_result($process,"address2");
	$strContactNo1 = odbc_result($process,"contact_no1");
	$strContactNo2 = odbc_result($process,"contact_no2");
	$strFaxNo = odbc_result($process,"fax_no");
	$strORCompanyName = odbc_result($process,"or_company_name");
	$strORAddress1 = odbc_result($process,"or_company_address1");
	$strORAddress2 = odbc_result($process,"or_company_address2");
	$strORContactNo1 = odbc_result($process,"or_company_contact_no1");
	$strORContactNo2 = odbc_result($process,"or_company_contact_no2");
	$strPreparedBy = odbc_result($process,"prepared_by");
	$strApprovedBy = odbc_result($process,"approved_by");
}

if ($strMsg != ""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 

?>

<html> 
<head> 
<title>CONFIGURATION</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color: #F3F5B4;">
<form name="frmConfiguration" id="frmConfiguration" method="post" action="configuration.php?menu_id=<?php echo $menu_id;?>">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">CONFIGURATION
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			 </a></li>	
			  <li class="li_nc"><a href="#" onClick="javascript:cmdSave_OnClick()">|&nbsp;&nbsp;&nbsp;&nbsp;Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
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
										<td class="fieldname">COMPANY CODE :<em class="requiredRed">*</em></td>
										<td width="20"></td>										
										<td><input type=text name="txtCompanyCode" id="txtCompanyCode" disabled class="values" size="20" maxlength="10" value="<?php echo $strCompanyCode;?>"></td>										
										<input type="hidden" id="hidCompanyCode" name="hidCompanyCode" value="<?php echo $strCompanyCode;?>">
									</tr>
									<tr>
										<td class="fieldname">NAME :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtCompanyName" id="txtCompanyName" class="values" size="70" maxlength="100" value="<?php echo $strCompanyName;?>"></td>
									</tr>
									<tr>
										<td class="fieldname">DBA :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtDBAName" id="txtDBAName" class="values" size="70" maxlength="100" value="<?php echo $strDBAName;?>"></td>
									</tr>
									<tr>
										<td class="fieldname">ADDRESS 1 :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtAddress1" id="txtAddress1" class="values" size="60" maxlength="50" value="<?php echo $strAddress1;?>"></td>
									</tr>									
									<tr>
										<td class="fieldname">ADDRESS 2 :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtAddress2" id="txtAddress2" class="values" size="60" maxlength="50" value="<?php echo $strAddress2;?>"></td>
									</tr>									
									<tr>
										<td class="fieldname">CONTACT NO. 1 :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtContactNo1" id="txtContactNo1" class="values" size="30" maxlength="40" value="<?php echo $strContactNo1;?>"></td>
									</tr>		
									<tr>
										<td class="fieldname">CONTACT NO. 2 :</td>
										<td width="20"></td>
										<td><input type=text name="txtContactNo2" id="txtContactNo2" class="values" size="30" maxlength="40" value="<?php echo $strContactNo2;?>"></td>
									</tr>	
									<tr>
										<td class="fieldname">FAX NO. :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtFaxNo" id="txtFaxNo" class="values" size="30" maxlength="30" value="<?php echo $strFaxNo;?>"></td>
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
	<div style="background-color:#3C6500;border-top: 1px solid;border-bottom: 1px solid;border-color:green; height:1.5em; font-family:Verdana; font-weight:bold; font-size:0.9em;color:white;">&nbsp;&nbsp;&nbsp;OFFICIAL RECEIPT HEADER
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
										<td class="fieldname">COMPANY NAME :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtORCompanyName" id="txtORCompanyName" class="values" size="70" maxlength="100" value="<?php echo $strORCompanyName;?>"></td>
									</tr>										
									<tr>
										<td class="fieldname">ADDRESS 1 :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtORAddress1" id="txtORAddress1" class="values" size="60" maxlength="50" value="<?php echo $strORAddress1;?>"></td>
									</tr>	
									<tr>
										<td class="fieldname">ADDRESS 2 :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtORAddress2" id="txtORAddress2" class="values" size="60" maxlength="50" value="<?php echo $strORAddress2;?>"></td>
									</tr>	
									<tr>
										<td class="fieldname">CONTACT NO. 1 :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtORContactNo1" id="txtORContactNo1" class="values" size="30" maxlength="40" value="<?php echo $strORContactNo1;?>"></td>
									</tr>	
									<tr>
										<td class="fieldname">CONTACT NO. 2 :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtORContactNo2" id="txtORContactNo2" class="values" size="30" maxlength="40" value="<?php echo $strORContactNo2;?>"></td>
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
	<div style="background-color:#3C6500;border-top: 1px solid;border-bottom: 1px solid;border-color:green; height:1.5em; font-family:Verdana; font-weight:bold; font-size:0.9em;color:white;">&nbsp;&nbsp;&nbsp;REPORT FOOTER
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
										<td class="fieldname">PREPARED BY :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtPreparedBy" id="txtPreparedBy" class="values" size="50" maxlength="50" value="<?php echo $strPreparedBy;?>"></td>
									</tr>	
									<tr>
										<td class="fieldname">APPROVED BY :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtApprovedBy" id="txtApprovedBy" class="values" size="50" maxlength="50" value="<?php echo $strApprovedBy;?>"></td>
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
		</td>
		</tr>
	</table>
	<input type="hidden" id="hidMode" name="hidMode">
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
	if (trim(frmConfiguration.hidCompanyCode.value) == "") {
		alert("Company Code is required")
		return false
	}
	if (trim(frmConfiguration.txtCompanyName.value) == "") {
		alert("Company Name is required")
		return false
	}
	if (trim(frmConfiguration.txtDBAName.value) == "") {
		alert("DBA is required")
		return false
	}
	if (trim(frmConfiguration.txtAddress1.value) == "") {
		alert("Address 1 is required")
		return false
	}
	if (trim(frmConfiguration.txtAddress2.value) == "") {
		alert("Address 2 is required")
		return false
	}
	if (trim(frmConfiguration.txtContactNo1.value) == "") {
		alert("Contact No. 1 is required")
		return false
	}
	if (trim(frmConfiguration.txtFaxNo.value) == "") {
		alert("Fax No. is required")
		return false
	}
	if (trim(frmConfiguration.txtORCompanyName.value) == "") {
		alert("OR Company Name is required")
		return false
	}
	if (trim(frmConfiguration.txtORAddress1.value) == "") {
		alert("OR Company Address 1 is required")
		return false
	}
	if (trim(frmConfiguration.txtORAddress2.value) == "") {
		alert("OR Company Address 2 is required")
		return false
	}
	if (trim(frmConfiguration.txtORContactNo1.value) == "") {
		alert("OR Company Contact No. 1 is required")
		return false
	}
	if (trim(frmConfiguration.txtORContactNo2.value) == "") {
		alert("OR Company Contact No. 2 is required")
		return false
	}
	if (trim(frmConfiguration.txtPreparedBy.value) == "") {
		alert("Prepared By is required")
		return false
	}
	if (trim(frmConfiguration.txtApprovedBy.value) == "") {
		alert("Approved By is required")
		return false
	}

	frmConfiguration.hidMode.value = "SAVE";
	frmConfiguration.submit();
}

function cmdRetrieve_OnClick() {
	frmConfiguration.hidCompanyCode.value = frmConfiguration.txtCompanyCode.value;
	frmConfiguration.hidMode.value = "RETRIEVE";
	frmConfiguration.submit();
}

function cmdCancel_OnClick() {
	parent.frames[2].location = "configuration.php?menu_id=" + frmConfiguration.hidMenuID.value;
	return false;
}

function cmdDelete_OnClick() {
	if (frmConfiguration.txtCompanyCode.value == "") {
		alert("Tenant Code is blank")
		frmConfiguration.txtCompanyCode.focus()
		return false
	}
	else {
		if (confirm("Are you sure you want to delete this record?")) {
			frmConfiguration.hidMode.value = "DELETE";
			frmConfiguration.submit();
		}
	}
}

function change_loc(pFile) {
	parent.frames[2].location = pFile;
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