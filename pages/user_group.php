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
$intGroupCode = 0;
$strDescription = "";
$strMode = $_POST["hidMode"];
$strSaveMode = $_POST["hidSaveMode"];

if ($_GET["mode"] == "FIND") {
	$intGroupCode = replacesinglequote($_GET["group_code"]);
	$strMode = "FIND";
}

if ($strMode != "") {
	if ($strMode != "FIND") {
		$intGroupCode = replacesinglequote($_POST["hidGroupCode"]);
	}
	$strDescription = replacesinglequote($_POST["txtDescription"]);
	$uid = $sessUserID;
	
	//echo $strSaveMode;
	switch ($strMode) {
		case "SAVE":
			if ($strSaveMode != "EDIT") {
				$sqlquery="exec sp_s_User_Group 'FIND','" . $intGroupCode . "','" . $strDescription . "','" . $strCompany . "','" . $uid . "','" . $strIPAddr . "'";
				$process=odbc_exec($sqlconnect, $sqlquery);
				if (odbc_fetch_row($process)) {
					if (odbc_result($process,"x") == 1) {
						$strMsg = "User Group already exists!";
					}
				}
			}
			
			if ($strMsg == "") {				
				$sqlquery="exec sp_s_User_Group 'SAVE','" . $intGroupCode . "','" . $strDescription . "','" . $strCompany . "','" . $uid . "','" . $strIPAddr . "'";
				$process=odbc_exec($sqlconnect, $sqlquery);
				$strMsg = "Record saved!";
				$intGroupCode = 0;
				$strDescription = "";
				$strMode = "";
				$strSaveMode = "";
			}
			
			//die();
			break;
		
		case "DELETE":
			$sqlquery="exec sp_s_User_Group 'DELETE','" . $intGroupCode . "','" . $strDescription . "','" . $strCompany . "','" . $uid . "','" . $strIPAddr . "'";
			$process=odbc_exec($sqlconnect, $sqlquery);
			if (odbc_fetch_row($process)) {
				if (odbc_result($process,"x") == 1) 
					$strMsg = "Record cannot be deleted. User Group has been assigned in User module.";
				else
					$strMsg = "Record deleted!";
					$intGroupCode = 0;
					$strDescription = "";
					$strMode = "";
					$strSaveMode = "";
			}
			break;
			
		case "RETRIEVE" || "FIND":
			$sqlquery="exec sp_s_User_Group 'RETRIEVE','" . $intGroupCode . "','" . $strDescription . "','" . $strCompany . "','" . $uid . "','" . $strIPAddr . "'";
			$process=odbc_exec($sqlconnect, $sqlquery);
			//echo $sqlquery;
			if (odbc_fetch_row($process)) {
					$intGroupCode = odbc_result($process,"group_code");
					$strDescription = odbc_result($process,"group_desc");					
					$strMode = "RETRIEVE";
					$strSaveMode = "EDIT";
			}
			else {
				$strMsg = "No record found!";
				$strMode = "";
				$strSaveMode = "";
			}
			break;
	}
}

if ($strMsg != ""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 

?>
<html> 
<head> 
<title>USER GROUP</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color: #F3F5B4;">
<form name="frmUserGroup" id="frmUserGroup" method="post" action="user_group.php?menu_id=<?php echo $menu_id;?>">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a href="#" onClick="javascript:change_loc('security_menu.php?menu_id=<?php echo $menu_id;?>')"><u>SECURITY</u></a></li>	
			  <li class="li_nc"><a name="MODULE NAME">>>&nbsp;&nbsp;&nbsp;USER GROUP
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			 </a></li>	
			  <li class="li_nc"><a href="#" onClick="javascript:cmdRetrieve_OnClick()">|&nbsp;&nbsp;&nbsp;Retrieve&nbsp;&nbsp;&nbsp;|</a></li>
			  <li class="li_nc"><a href="#" onClick="javascript:cmdSave_OnClick()">Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php if ($strMode != "RETRIEVE") { ?>
					<li><a name="Delete" style="color: #666666">Delete&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } else { ?>			
					<li class="li_nc"><a href="#" onClick="javascript:cmdDelete_OnClick()">Delete&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <?php } ?>	
			  <li class="li_nc"><a href="#" onClick="javascript:change_loc('user_group_list.php?menu_id=<?php echo $menu_id;?>')">Find&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
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
										<td class="fieldname">GROUP CODE :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtGroupCode" id="txtGroupCode" class="values" disabled size="10" value=<?php echo $intGroupCode; ?>></td>
										<input type=hidden name="hidGroupCode" id="hidGroupCode" value="<?php echo $intGroupCode; ?>">
									</tr>		
									<tr>
										<td class="fieldname">DESCRIPTION :<em class="requiredRed">*</em></td>
										<td width="20"></td>
										<td><input type=text name="txtDescription" id="txtDescription" class="values" size="50" maxlength="50" value="<?php echo $strDescription; ?>"></td>
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
	if (frmUserGroup.hidSaveMode.value == "EDIT") {
		if (trim(frmUserGroup.hidGroupCode.value) == "") {
			alert("Group Code is required")
			return false
		}	
	}
	if (trim(frmUserGroup.txtDescription.value) == "") {
		alert("Description is required")
		frmUserGroup.txtDescription.focus()
		return false
	}	
	frmUserGroup.hidMode.value = "SAVE";
	frmUserGroup.submit();
}

function cmdRetrieve_OnClick() {
	frmUserGroup.hidGroupCode.value = frmUserGroup.txtGroupCode.value;
	frmUserGroup.hidMode.value = "RETRIEVE";
	frmUserGroup.submit();
}

function cmdCancel_OnClick() {
	parent.frames[2].location = "user_group.php?menu_id=" + frmUserGroup.hidMenuID.value;
	return false;
}

function cmdDelete_OnClick() {
	if (frmUserGroup.hidGroupCode.value == "") {
		alert("No record to be deleted!")
		return false
	}
	else {
		if (confirm("Are you sure you want to delete this record?")) {
			frmUserGroup.hidMode.value = "DELETE";
			frmUserGroup.submit();
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
