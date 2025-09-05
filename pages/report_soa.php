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
$dtFrom = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
$dtTo = date("m/d/Y",(strtotime(date("m/d/y", time()))+60*60*24*($OFFSET)));	
$dtToday = strval(date("m/d/y H:i:s", time()));
$strMode = $_POST["hidMode"];

if ($strMode == "PRINT") {
	$sqlquery="exec sp_rpt_StatementOfAccountProc '" . $_POST["hidClientCode"] . "','" . $_POST["DPC_txtDateTo"] . "','" . $_POST["DPC_txtDateTo"] . "','" . $sessUserID . "','" . $dtToday . "'";	
	$process=odbc_exec($sqlconnect, $sqlquery);
	//echo $sqlquery;
	//exit();
	
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
	$my_report = $report_path . "t_statement_of_account.rpt"; // 
	//echo $my_report;
	//die();
	//rpt source file 
	$pdf_file = "t_statement_of_account" . str_replace("/","",date("m/d/y/H/i/s", time())) . ".pdf";
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
	$creport->ParameterFields(1)->AddCurrentValue ($_POST["hidClientCode"]);
	$creport->ParameterFields(2)->AddCurrentValue ($_POST["DPC_txtDateTo"]);
	$creport->ParameterFields(3)->AddCurrentValue ($_POST["DPC_txtDateTo"]);
	$creport->ParameterFields(4)->AddCurrentValue ($sessUserID);
	$creport->ParameterFields(5)->AddCurrentValue (date($dtToday));
	$strClientCode = $_POST["hidClientCode"];
	$strClientName = $_POST["txtClientName"];
	$dtFrom = $_POST["DPC_txtDateTo"];
	$dtTo = $_POST["DPC_txtDateTo"];
	//$creport->ParameterFields(4)->AddCurrentValue (dtFrom);
	//$creport->ParameterFields(5)->AddCurrentValue ($dtTo);
	//$creport->ParameterFields(6)->AddCurrentValue ($dtFrom);
	//$creport->ParameterFields(7)->AddCurrentValue ($dtTo);
	//$creport->ParameterFields(8)->AddCurrentValue (dtFrom);
	//$creport->ParameterFields(9)->AddCurrentValue ($dtTo);
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
	
}

?>
<html> 
<head> 
<title>STATEMENT OF ACCOUNT</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
<script type="text/javascript" src="library/datepickercontrol/datepickercontrol.js"></script>
<link type="text/css" rel="stylesheet" href="library/datepickercontrol/datepickercontrol_green.css">
</head> 
<body style="margin:'0';background-color: #F3F5B4;">
<form name="frmSOA" id="frmSOA" method="post">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">STATEMENT OF ACCOUNT
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;
			 </a></li>	
			  <li class="li_nc"><a href="#" onClick="javascript:cmdPrint_OnClick()">|&nbsp;&nbsp;&nbsp;&nbsp;Print&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
			  <li class="li_nc"><a href="#" onClick="javascript:window.close();">Close&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>
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
										<td class="fieldname_right">CLIENT :</td>
										<td width="20"></td>
										<td><input type=text name="txtClientName" id="txtClientName" class="values" size="50" maxlength="100" value="<?php echo $strClientName;?>">
										<img id="cmdClientLookup" name="cmdClientLookup" onClick="javascript:cmdClientLookup_onClick();" src="images/icon_search.gif" style="cursor:hand" alt="Client Lookup">
										<input type=hidden name="hidClientCode" id="hidClientCode" value="<?php echo $strClientCode;?>">
										</td>
									</tr>																							
									<tr>
										<td class="fieldname_right">STATEMENT AS OF (mm/dd/yyyy):</td>
										<td width="20"></td>
										<td><input type=text name="DPC_txtDateTo" id="DPC_txtDateTo" class="values" size="20" maxlength="10" value="<?php echo $dtTo;?>">										
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
</form>
</body> 
</html>

<script type="text/javascript">
function hov(loc,cls) {   
	if(loc.className)   
	loc.className=cls;   
} 

function cmdClientLookup_onClick() {
	window.open ("report_soa_lookup.php?menu_id=" + frmSOA.hidMenuID.value + "&src=from","displayWindow","type=fullwindow,titlebar=no,scrollbars=yes");
	return false;
}

function cmdPrint_OnClick() {

	if (frmSOA.DPC_txtDateTo.value == "") {
		alert ("Date To is required")
		frmSOA.DPC_txtDateTo.focus()
		return false
	}
	
	if (frmSOA.DPC_txtDateTo.value != "") {
		if (isDate(frmSOA.DPC_txtDateTo.value)==false) {
			frmSOA.DPC_txtDateTo.focus()
			return false
		}
	}
	
	if (frmSOA.txtClientName.value == "") {
		frmSOA.hidClientCode.value = ""
	}
	
	frmSOA.hidMode.value = "PRINT"
	frmSOA.submit()
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
