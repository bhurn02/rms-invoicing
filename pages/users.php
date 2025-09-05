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

$strCompany = $sessCompanyCode;
$strUserID = "";
$disUserID = "";
$strLastName = "";
$strFirstName = "";
$strMI = "";
$strUserGroupCode = 0;
$strUserGroupName = "";
$strPassword = "";
$blnActive = "checked";
$dtInactive = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
$disInactive = "disabled";
$strMode = $_POST["hidMode"];
$strSaveMode = $_POST["hidSaveMode"];

if ($_GET["mode"] == "FIND") {
	$strUserID = replacesinglequote($_GET["userid"]);
	$strLastName = replacesinglequote($_GET["userid"]);
	$strMode = "FIND";
}

if ($strMode != "") {
	if ($strMode != "FIND") {
		$strUserID = replacesinglequote($_POST["hidUserID"]);
	}
	$strLastName = replacesinglequote($_POST["txtLastName"]);
	$strFirstName = replacesinglequote($_POST["txtFirstName"]);
	$strMI = replacesinglequote($_POST["txtMI"]);
	$strUserGroupCode = $_POST["cboUserGroup"];
	$strPassword = replacesinglequote($_POST["txtPassword"]);
	if ($_POST["chkActive"] == "on") {
		$blnActive = "Y";
	}
	else {
		$blnActive = "N";		
	}
	if ($blnActive == "N") 
		$dtInactive = replacesinglequote($_POST["txtDateInactive"]);
	else
		$dtInactive = "";
	$uid = $sessUserID;
	
	//echo $strSaveMode;
	switch ($strMode) {
		case "SAVE":
			if ($strSaveMode != "EDIT") {
				$strUserID = replacesinglequote($_POST["txtUserID"]);
				$sqlquery="exec sp_s_User 'FIND','" . $strUserID . "','" . $strLastName . "','" . $strFirstName . "','" . $strMI . "','" . $strPassword . "'," . $strUserGroupCode . ",'" . $blnActive . "','" . $dtInactive . "','" . $strCompany . "','" . $uid . "','" . $strIPAddr . "'";	
				$process=odbc_exec($sqlconnect, $sqlquery);
				if (odbc_fetch_row($process)) {
					if (odbc_result($process,"x") == 1) {
						$strMsg = "User ID already exists!";
						if ($_POST["chkActive"] == "on") {
							$blnActive = "checked";
						}
						else {
							$blnActive = "";		
						}
					}
				}
			}
			
			if ($strMsg == "") {				
				$sqlquery="exec sp_s_User 'SAVE','" . $strUserID . "','" . $strLastName . "','" . $strFirstName . "','" . $strMI . "','" . $strPassword . "'," . $strUserGroupCode . ",'" . $blnActive . "','" . $dtInactive . "','" . $strCompany . "','" . $uid . "','" . $strIPAddr . "'";	
				$process=odbc_exec($sqlconnect, $sqlquery);
				$strMsg = "Record saved!";
				$strUserID = "";
				$strLastName = "";
				$strFirstName = "";
				$strMI = "";
				$strUserGroupCode = 0;
				$strUserGroupName = "";
				$strPassword = "";
				$blnActive = "checked";
				$dtInactive = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
				$disInactive = "disabled";
				$strMode = "";
				$strSaveMode = "";
			}
			
			//die();
			break;
		
		case "DELETE":
			$sqlquery="exec sp_s_User 'DELETE','" . $strUserID . "','" . $strLastName . "','" . $strFirstName . "','" . $strMI . "','" . $strPassword . "'," . $strUserGroupCode . ",'" . $blnActive . "','" . $dtInactive . "','" . $strCompany . "','" . $uid . "','" . $strIPAddr . "'";
			$process=odbc_exec($sqlconnect, $sqlquery);
			if (odbc_fetch_row($process)) {
				if (odbc_result($process,"x") == 1) 
					$strMsg = "Record cannot be deleted. User ID has been used in other modules.";
				else
					$strMsg = "Record deleted!";
					$strUserID = "";
					$strLastName = "";
					$strFirstName = "";
					$strMI = "";
					$strUserGroupCode = 0;
					$strUserGroupName = "";
					$strPassword = "";
					$blnActive = "checked";
					$dtInactive = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
					$disInactive = "disabled";
					$strMode = "";
					$strSaveMode = "";
			}
			break;
			
		case "RETRIEVE" || "FIND":
			$strLastName = $strUserID;
			$sqlquery="exec sp_s_User 'RETRIEVE','" . $strUserID . "','" . $strLastName . "','" . $strFirstName . "','" . $strMI . "','" . $strPassword . "',0,'" . $blnActive . "','" . $dtInactive . "','" . $strCompany . "','" . $uid . "','" . $strIPAddr . "'";
			$process=odbc_exec($sqlconnect, $sqlquery);
			//echo $sqlquery;
			if (odbc_fetch_row($process)) {
					$strUserID = odbc_result($process,"user_id");
					$disUserID = "disabled";
					$strLastName = odbc_result($process,"last_name");
					$strFirstName = odbc_result($process,"first_name");
					$strMI = odbc_result($process,"middle_initial");
					$strUserGroupCode = odbc_result($process,"group_code");
					$strUserGroupName = odbc_result($process,"group_desc");
					$strPassword = odbc_result($process,"user_password");										
					if (odbc_result($process,"is_active") == "Y") {
						$blnActive = "checked";	
						$dtInactive = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
						$disInactive = "disabled";
					}
					else {
						$blnActive = "";	
						$dtInactive = odbc_result($process,"date_inactive");
						$disInactive = "";
					}
					$strMode = "RETRIEVE";
					$strSaveMode = "EDIT";
			}
			else {
				$strMsg = "No record found!";
				$strLastName = "";
				$strMode = "";
				$strSaveMode = "";
			}
			break;
	}
}
//echo $strRealPropertyCode;
$sqlquery="select * from s_user_group order by group_desc";
$process=odbc_exec($sqlconnect, $sqlquery);
while (odbc_fetch_row($process)) {
	if (replacedoublequotes($strUserGroupCode) == replacedoublequotes(trim(odbc_result($process,"group_code"))))
		$cboUserGroup .= "<option selected value=\"" . trim(odbc_result($process,"group_code")) . "\">" . trim(strtoupper(odbc_result($process,"group_desc"))) . "</option>";
	else
		$cboUserGroup .= "<option value=\"" . trim(odbc_result($process,"group_code")) . "\">" . trim(strtoupper(odbc_result($process,"group_desc"))) . "</option>";
}

if ($strMsg != ""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 

?>
<html> 
<head> 
<title>USERS</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color: #F3F5B4;">
<form name="frmUser" id="frmUser" method="post" action="users.php?menu_id=<?php echo $menu_id;?>">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a href="#" onClick="javascript:change_loc('security_menu.php?menu_id=<?php echo $menu_id;?>')"><u>SECURITY</u></a></li>	
			  <li class="li_nc"><a name="MODULE NAME">>>&nbsp;&nbsp;&nbsp;USER MAINTENANCE
			  &nbsp;&nbsp;&nbsp;
			  
			 </a></li>	
			  <li class="li_nc"><a href="#" onClick="javascript:cmdRetrieve_OnClick()">|&nbsp;&nbsp;&nbsp;Retrieve&nbsp;&nbsp;&nbsp;|</a></li>
			  <li class="li_nc"><a href="#" onClick="javascript:cmdSave_OnClick()">Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php if ($strMode != "RETRIEVE") { ?>
					<li><a name="Delete" style="color: #666666">Delete&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } else { ?>			
					<li class="li_nc"><a href="#" onClick="javascript:cmdDelete_OnClick()">Delete&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } ?>	
			  <li class="li_nc"><a href="#" onClick="javascript:change_loc('users_list.php?menu_id=<?php echo $menu_id;?>')">Find&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
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
										<td class="fieldname">USER ID :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtUserID" id="txtUserID" class="values" <?php echo $disUserID;?> size="30" maxlength="15" value="<?php echo $strUserID; ?>"></td>
										<input type=hidden name="hidUserID" id="hidUserID" value="<?php echo $strUserID; ?>">
									</tr>		
									<tr>
										<td class="fieldname">LAST NAME :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtLastName" id="txtLastName" class="values" size="50" maxlength="50" value="<?php echo $strLastName; ?>"></td>
									</tr>	
									<tr>
										<td class="fieldname">FIRST NAME :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtFirstName" id="txtFirstName" class="values" size="50" maxlength="50" value="<?php echo $strFirstName; ?>"></td>
									</tr>			
									
									<tr>
										<td class="fieldname">MIDDLE INITIAL :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtMI" id="txtMI" class="values" size="10" maxlength="5" value="<?php echo $strMI; ?>"></td>
									</tr>														
									<tr>
										<td class="fieldname">USER GROUP :<em class="requiredRed">*</em></td>
										<td width="20"></td>										
										<td>
											<select name="cboUserGroup" id="cboUserGroup" class="values">
											<option value=0>- Select User Group -</option>
											<?php echo $cboUserGroup; ?>
											</select>
										</td>
										<input type="hidden" id="hidUserGroupCode" name="hidUserGroupCode" value="<?php echo $strUserGroupCode; ?>">
									</tr>					
									<tr>
										<td class="fieldname">PASSWORD :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=password name="txtPassword" id="txtPassword" class="values" size="30" maxlength="15" value="<?php echo $strPassword; ?>"></td>
									</tr>	
									<tr>
										<td class="fieldname">CONFIRM PASSWORD :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=password name="txtConfirmPassword" id="txtConfirmPassword" class="values" size="30" maxlength="15" value="<?php echo $strPassword; ?>"></td>
									</tr>				
									<tr>
										<td class="fieldname">&nbsp;</td>
										<td width="20"></td>
										<td class="values"><input name="chkActive" id="chkActive" type="checkbox" <?php echo $blnActive;?> onClick="javascript:chkActive_OnClick()">ACTIVE</td>
									</tr>	
				
									<tr>
										<td class="fieldname">DATE INACTIVE :</td>
										<td width="20"></td>
										<td><input type=text name="txtDateInactive" id="txtDateInactive" <?php echo $disInactive;?> class="values" size="20" maxlength="10" value="<?php echo $dtInactive; ?>"></td>
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

function chkActive_OnClick() {
	if (frmUser.chkActive.checked==false)
		frmUser.txtDateInactive.disabled = false
	else
		frmUser.txtDateInactive.disabled = true
}

function cmdSave_OnClick() {
	if (trim(frmUser.txtUserID.value) == "") {
		alert("User ID is required")
		frmUser.txtUserID.focus()
		return false
	}
	if (trim(frmUser.txtLastName.value) == "") {
		alert("Last Name is required")
		frmUser.txtLastName.focus()
		return false
	}
	if (trim(frmUser.txtFirstName.value) == "") {
		alert("First Name is required")
		frmUser.txtFirstName.focus()
		return false
	}
	if (trim(frmUser.txtMI.value) == "") {
		alert("Middle Initial is required")
		frmUser.txtMI.focus()
		return false
	}
	if (trim(frmUser.cboUserGroup.value) == "") {
		alert("User Group is required")
		frmUser.cboUserGroup.focus()
		return false
	}
	if (trim(frmUser.txtPassword.value) == "") {
		alert("Password is required")
		frmUser.txtPassword.focus()
		return false
	}
	if (trim(frmUser.txtConfirmPassword.value) == "") {
		alert("Confirm Password is required")
		frmUser.txtConfirmPassword.focus()
		return false
	}
	if (trim(frmUser.txtPassword.value) != trim(frmUser.txtConfirmPassword.value)) {
		alert("Passwords do not match")
		frmUser.txtPassword.focus()
		return false
	}
	if (frmUser.chkActive.value == false) {
		if (isDate(frmUser.txtDateInactive.value) == false) {
			frmUser.txtDateInactive.focus()
			return false
		}
	}
	frmUser.hidMode.value = "SAVE";
	frmUser.submit();
}

function cmdRetrieve_OnClick() {
	frmUser.hidUserID.value = frmUser.txtUserID.value;
	frmUser.hidMode.value = "RETRIEVE";
	frmUser.submit();
}

function cmdCancel_OnClick() {
	parent.frames[2].location = "users.php?menu_id=" + frmUser.hidMenuID.value;
	return false;
}

function cmdDelete_OnClick() {
	if (frmUser.hidUserID.value == "") {
		alert("No record to be deleted!")
		return false
	}
	else {
		if (confirm("Are you sure you want to delete this record?")) {
			frmUser.hidMode.value = "DELETE";
			frmUser.submit();
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
