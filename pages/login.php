<?php session_start();
//session_id();
// die(PHP_VERSION);
include("functions.php");
$sqlconnect = connection();

$strMsg = "";
$strMsg2 = "";
$strMode = $_POST["hidMode"];

$sqlquery="select * from s_company";
$process=odbc_exec($sqlconnect, $sqlquery);
while (odbc_fetch_row($process)) {	
	$cboCompanyArr .= "<option value=\"" . trim(odbc_result($process,"company_code")) . "\">" . trim(strtoupper(odbc_result($process,"company_name"))) . "</option>";
}

if ($strMode == "SUBMIT") {
	$strUserName = replacesinglequote($_POST["txtUserName"]);
	$strPassword = replacesinglequote($_POST["txtPassword"]);
	$strCompany = replacesinglequote($_POST["cboCompany"]);		

	$sqlquery="exec sp_s_Login '" . $strUserName . "','" . $strPassword . "','" . $strCompany . "','" . $_SERVER["REMOTE_ADDR"] . "'";	
	$process=odbc_exec($sqlconnect, $sqlquery);
	if (odbc_fetch_row($process)) {
		$strMsg = odbc_result($process,"x");				
		if ($strMsg == "1") {
			$strMsg2 = "";	
			//session_register("userid");
			//session_register("username");
			//$userid=strtoupper($strUserName);
			//$username=odbc_result($process,"username");
			
			//$_SESSION['userid']=strtoupper($strUserName);
			//$_SESSION['username']=odbc_result($process,"username");
			setcookie("userid",strtoupper($strUserName),0);
			setcookie("username",odbc_result($process,"username"),0);
			setcookie("company_code",$_POST["cboCompany"],0);
		}
		else
			$strMsg2 = $strMsg;
	}
}
			
?>
<html> 
<head> 
<title>LOGIN</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';" onLoad="javascript:page_onload()">
<form name="frmLogin" id="frmLogin" method="post">
	<div width="100%" height="100%" style="background-color:#000000;border:1px solid black" >
		<img src="images/rms banner copy.jpg" height="16%" width="100%" >
	</div>
	<table height="100%" cellspacing="0">
		<tr>
			<td width="350" bgcolor="#98DC65"></td>
			<td width="150"></td>
			<td>
				<table>
					<tr>
						<td>
							<table cellpadding="1">
								<tr>
									<td class="fieldname" align="right">User Name</td>
									<td width="5"></td>
									<td><input type=text name="txtUserName" id="txtUserName" class="values" onKeyUp="javascript:txtUserName_onKeyUp(event)" style="background-color: #FFFFB7;" size="40"></td>
								</tr>		
								<tr>
									<td class="fieldname" align="right">Password</td>
									<td width="5"></td>
									<td><input type="password" name="txtPassword" id="txtPassword" class="values" onKeyUp="javascript:txtPassword_onKeyUp(event)" style="background-color:  #FFFFB7;" size="40"></td>
								</tr>			
								<tr>
									<td class="fieldname" align="right">Company</td>
									<td width="5"></td>										
									<td>
										<select name="cboCompany" id="cboCompany" class="values" onKeyUp="javascript:cboCompany_onKeyUp(event)">
										<?php echo $cboCompanyArr;?>
										</select>
									</td>
								</tr>		
								<tr>
									<td class="fieldname">&nbsp;</td>
									<td width="5"></td>
									<td>
										<input type="button" name="cmdSubmit" id="cmdSubmit" class="btn" onClick="javascript:cmdSubmit_onClick()" onmouseover="hov(this,'btn btnhov')" onmouseout="hov(this,'btn')" value=" SUBMIT ">
									</td>
								</tr>		
								<tr>
									<td class="fieldname">&nbsp;</td>
									<td width="5"></td>
									<td class="required">&nbsp;										
										
									</td>
								</tr>
								<tr>
									<td class="fieldname">&nbsp;</td>
									<td width="5"></td>
									<td class="required">										
										<?php echo $strMsg2; ?>
									</td>
								</tr>						
							</table>
						</td>
					</tr>
					<tr height="130">
						<td></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<input type="hidden" id="hidMode" name="hidMode">
	<input type="hidden" id="hidMsg" name="hidMsg" value="<?php echo $strMsg; ?>">
	<input type="hidden" id="hidIsLogged" name="hidIsLogged" value="" runat="server">
</form>
</body> 
</html>

<script type="text/javascript">
function page_onload() {	
	if(frmLogin.hidMsg.value == "1") {
		if(frmLogin.hidIsLogged.value != "") {
			if(confirm("A logon instance of this user already exists." + String.fromCharCode(13) + "This maybe caused by non terminated session (user did not logout through the system)." + String.fromCharCode(13) + "Make it a habit to exit the system properly via the Logout menu." + String.fromCharCode(13) + String.fromCharCode(13) + "Do you wish to terminate the previous session?"))
			{
				frmLogin.hidIsLogged.value	= "2";
				window.location.href ="index.htm";
			}			
		}
		else {
				frmLogin.hidIsLogged.value = "2";
				window.location.href ="index.htm";
			}
	}
}

function hov(loc,cls) {  
	if(loc.className)   
	loc.className=cls;   
} 

function cmdSubmit_onClick() {
	frmLogin.hidMode.value = "SUBMIT";
	frmLogin.submit();
}

function txtUserName_onKeyUp(e) {
	if (e.keyCode==13) {
		frmLogin.hidMode.value = "SUBMIT";
		frmLogin.submit();
	}
}

function txtPassword_onKeyUp(e) {
	if (e.keyCode==13) {
		frmLogin.hidMode.value = "SUBMIT";
		frmLogin.submit();
	}
}

function cboCompany_onKeyUp(e) {
	if (e.keyCode==13) {
		frmLogin.hidMode.value = "SUBMIT";
		frmLogin.submit();
	}
}

function change_loc(pFile) {
	parent.frames[2].location = pFile;
	return false;
}
</script>
