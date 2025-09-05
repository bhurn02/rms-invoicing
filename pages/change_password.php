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

$strCompany = $sessCompanyCode;
$strUserID = $sessUserID;
$uid = $sessUserID;
$strMode = $_POST["hidMode"];
$strSaveMode = $_POST["hidSaveMode"];

if ($strMode != "") {	
	$strNewPassword = replacesinglequote($_POST["txtNewPassword"]);
	$strConfirmPassword = replacesinglequote($_POST["txtConfirmPassword"]);
	
	//echo $strSaveMode;
	if ($strMode == "SAVE") {
		$sqlquery="exec sp_s_User 'CHANGE_PASSWORD','" . $strUserID . "','','','" . $strMI . "','" . $strNewPassword . "',0,'','','" . $strCompany . "','" . $uid . "','" . $strIPAddr . "'";	
		$process=odbc_exec($sqlconnect, $sqlquery);
		$strMsg = "New Password saved!";
		$strMode = "";
		$strSaveMode = "";
	}
}
$sqlquery="exec sp_s_User 'RETRIEVE_USER_PWD','" . $strUserID . "','','','" . $strMI . "','" . $strNewPassword . "',0,'','','" . $strCompany . "','" . $uid . "','" . $strIPAddr . "'";	
//echo $sqlquery;
$process=odbc_exec($sqlconnect, $sqlquery);
if (odbc_fetch_row($process)) {
		$strOldPassword = odbc_result($process,"user_password");
		$strMode = "RETRIEVE";
		$strSaveMode = "EDIT";
		//echo $strOldPassword;
}

if ($strMsg != ""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 

?>
<html> 
<head> 
<title>CHANGE PASSWORD</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color: #F3F5B4;">
<form name="frmChangePassword" id="frmChangePassword" method="post" action="change_password.php">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">CHANGE PASSWORD
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
										<td class="fieldname">OLD PASSWORD :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=password name="txtOldPassword" id="txtOldPassword" class="values" size="30" maxlength="15">
										<input type=hidden name="hidUserID" id="hidUserID" value="<?php echo $strUserID; ?>">
										</td>
									</tr>	
									<tr>
										<td class="fieldname">NEW PASSWORD :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=password name="txtNewPassword" id="txtNewPassword" class="values" size="30" maxlength="15"></td>
									</tr>				
									<tr>
										<td class="fieldname">CONFIRM NEW PASSWORD :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=password name="txtConfirmPassword" id="txtConfirmPassword" class="values" size="30" maxlength="15"></td>
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
	<input type="hidden" id="hidOldPassword" name="hidOldPassword" value="<?php echo $strOldPassword; ?>">
	<input type="hidden" id="hidMenuID" name="hidMenuID" value=<?php echo $menu_id;?>>
</form>
</body> 
</html>

<script type="text/javascript">
function hov(loc,cls) {   
	if(loc.className)   
	loc.className=cls;   
} 

function chkActive_OnClick() {
	if (frmChangePassword.chkActive.checked==false)
		frmChangePassword.txtDateInactive.disabled = false
	else
		frmChangePassword.txtDateInactive.disabled = true
}

function cmdSave_OnClick() {	
	if (trim(frmChangePassword.txtOldPassword.value) == "") {
		alert("Old Password is required")
		frmChangePassword.txtOldPassword.focus()
		return false
	}
	
	if (frmChangePassword.txtOldPassword.value != frmChangePassword.hidOldPassword.value) {
		alert("Incorrect Old Password")
		frmChangePassword.txtOldPassword.focus()
		return false
	}	
	if (trim(frmChangePassword.txtNewPassword.value) == "") {
		alert("New Password is required")
		frmChangePassword.txtNewPassword.focus()
		return false
	}
	if (trim(frmChangePassword.txtConfirmPassword.value) == "") {
		alert("Confirm Password is required")
		frmChangePassword.txtConfirmPassword.focus()
		return false
	}
	if (trim(frmChangePassword.txtNewPassword.value) != trim(frmChangePassword.txtConfirmPassword.value)) {
		alert("Passwords do not match")
		frmChangePassword.txtPassword.focus()
		return false
	}
	frmChangePassword.hidMode.value = "SAVE";
	frmChangePassword.submit();
}

function cmdRetrieve_OnClick() {
	frmChangePassword.hidUserID.value = frmChangePassword.txtUserID.value;
	frmChangePassword.hidMode.value = "RETRIEVE";
	frmChangePassword.submit();
}

function cmdCancel_OnClick() {
	parent.frames[2].location = "users.php?menu_id=" + frmChangePassword.hidMenuID.value;
	return false;
}

function cmdDelete_OnClick() {
	if (frmChangePassword.hidUserID.value == "") {
		alert("No record to be deleted!")
		return false
	}
	else {
		if (confirm("Are you sure you want to delete this record?")) {
			frmChangePassword.hidMode.value = "DELETE";
			frmChangePassword.submit();
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
