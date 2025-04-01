<?php //session_start();
//session_id();
$sessUserID = "";
$sessUserName = "";
//if (isset($_COOKIE['PHPSESSID'])) {
//	echo $_COOKIE['PHPSESSID'];
//}
if (isset($_COOKIE['userid'])) {
	$sessUserID = $_COOKIE['userid'];
	$sessUserName = $_COOKIE['username'];
}
if (trim($sessUserID) == "" || $_POST["hidIsLogged"] != "") {
	echo "<script> parent.frames[2].location = (\"" . "accessnotallowed.htm" .  "\");</script>";
	exit();
}
else {
	$_POST["hidIsLogged"] = "";
}

if ($_POST["hidMode"] == "OUT") {
	setcookie("userid","",time()-3600);
	setcookie("username","",time()-3600);
	$_POST["hisIsLogged"] = "";
	echo "<script>parent.frames.location = (\"" . "login.php" .  "\");</script>";
}

//$sessUserName = $username;

//echo $sessUserName;
?>

<style type="text/css" media="screen">
input.parentmenu {   
	color:#050;   
	font: bold 84% 'Arial';   
	font-size:13px;
	color:black;
	background-color:white;
	border: 1px solid;   
	border-color: white; 
	
	filter:progid:DXImageTransform.Microsoft.Gradient   
	(GradientType=0,StartColorStr='#696969',EndColorStr='#696969');   
}   
input.submenu1 {   
	color:#050;   
	font: bold 84% 'Arial';   
	font-size:13px;
	color:black;
	background-color:white;
	border: 1px solid;   
	border-color: green;  	
	filter:progid:DXImageTransform.Microsoft.Gradient   
	(GradientType=0,StartColorStr='#ffffff',EndColorStr='#ffffff');  
}   
.buttonscontainer {width: 150px;}

.buttons a {color: white;
background-color: black;
padding: 2px;
padding-left: 3px;
display: block;
border-bottom: 1px solid green;
font: 13px Tahoma, sans-serif;
font-weight: bold;
text-decoration: none;
text-align: center;}

.buttons a:hover {background-color: white;
color: black;
text-decoration: none;}

.buttonscontainerchild {width: 150px;}

.buttonschild a {color: white;
background-color: green;
padding: 2px;
padding-left: 3px;
display: block;
border-bottom: 1px solid black;
font: 13px Tahoma, sans-serif;
font-weight: bold;
text-decoration: none;
text-align: center;}

.buttonschild a:hover {background-color: yellow;
color: black;
text-decoration: none;}

a.menu {color: white;
background-color: black;
padding: 2px;
padding-left: 3px;
display: block;
border-bottom: 1px solid green;
font: 13px Tahoma, sans-serif;
font-weight: bold;
text-decoration: none;
text-align: center;}

a.menuhov {background-color: white;
color: black;
text-decoration: none;
cursor:hand;}

a.menuhovhdr {background-color: white;
color: black;
text-decoration: none;
}

a.submenu {color: white;
background-color: green;
padding: 2px;
padding-left: 3px;
display: block;
border-bottom: 1px solid black;
font: 13px Tahoma, sans-serif;
font-weight: bold;
text-decoration: none;
text-align: center;
}

a.submenuhov {background-color: yellow;
color: black;
text-decoration: none;
cursor:hand;
}
</style>

<html>
<head>
<title>RMS SYSTEM</title>
</head>
<body bgcolor="#98DC65" style="border:1px solid; border-color:black; border-top:none; border-bottom:none" background="images/bg1.jpg">
<form id="frmMenu" name="frmMenu" method="post" action="menu.php" runat="server">
	<div style="font: 14px Tahoma, sans-serif;color:white; font-weight:bold; border-bottom: 1px solid #000000;width:90%;">
		Welcome,&nbsp;<?php echo $sessUserName; ?>
	</div>
	<br>
	<div class="buttonscontainer">
		<div class="buttons" align="center">
		<a name="HOME" onClick="javascript:change_loc('home.php')" class="menu" onMouseOver="hov(this,'a menuhov')" onMouseOut="hov(this,'menu')">HOME</a>&nbsp;
		<a name="CONFIGURATION" onClick="javascript:change_loc('configuration.php?menu_id=16')" class="menu" onMouseOver="hov(this,'a menuhov')" onMouseOut="hov(this,'menu')">CONFIGURATION</a>&nbsp;
		<a name="MAINTENANCE" class="menu" onMouseOver="hov(this,'a menuhovhdr')" onMouseOut="hov(this,'menu')">MAINTENANCE</a>
		</div>					
	</div>
	<div class="buttonscontainerchild" align="right" style="vertical-align:middle">		
		<div class="buttonschild" align="right" style="width:90%">
			<a name="REAL PROPERTY" onClick="javascript:change_loc('realproperty.php?menu_id=1')" class="submenu" onMouseOver="hov(this,'a submenuhov')" onMouseOut="hov(this,'submenu')">REAL PROPERTY</a>
		</div>
		<div class="buttonschild" align="right" style="width:90%">
			<a name="UNITS" onClick="javascript:change_loc('units.php?menu_id=2')" class="submenu" onMouseOver="hov(this,'a submenuhov')" onMouseOut="hov(this,'submenu')">UNITS</a>
		</div>
		<div class="buttonschild" align="right" style="width:90%">
			<a name="CHARGES" onClick="javascript:change_loc('charges.php?menu_id=15')" class="submenu" onMouseOver="hov(this,'a submenuhov')" onMouseOut="hov(this,'submenu')">CHARGES</a>
		</div>
		<div class="buttonschild" align="right" style="width:90%">
			<a name="UNIT CHARGES" onClick="javascript:change_loc('unit_charges.php?menu_id=3')" class="submenu" onMouseOver="hov(this,'a submenuhov')" onMouseOut="hov(this,'submenu')">UNIT CHARGES</a>
		</div>
		<div class="buttonschild" align="right" style="width:90%">
			<a name="TENANT" onClick="javascript:change_loc('tenant.php?menu_id=4')" class="submenu" onMouseOver="hov(this,'a submenuhov')" onMouseOut="hov(this,'submenu')">TENANT</a>
		</div>
		<div class="buttonschild" align="right" style="width:90%">
			<a name="TENANT CHARGES" onClick="javascript:change_loc('tenant_charges.php?menu_id=5')" class="submenu" onMouseOver="hov(this,'a submenuhov')" onMouseOut="hov(this,'submenu')">TENANT CHARGES</a>
		</div>
	</div>&nbsp;
	<div class="buttonscontainer">
		<div class="buttons" align="center">
			<a name="BILLING" class="menu" onMouseOver="hov(this,'a menuhovhdr')" onMouseOut="hov(this,'menu')">BILLING</a>
		</div>					
	</div>
	<div class="buttonscontainerchild" align="right" style="vertical-align:middle">		
		<div class="buttonschild" align="right" style="width:90%">
			<a name="TENANT READING" onClick="javascript:change_loc('tenant_reading.php?menu_id=6')" class="submenu" onMouseOver="hov(this,'a submenuhov')" onMouseOut="hov(this,'submenu')">TENANT READING</a>
			<a name="GENERATE INVOICE" onClick="javascript:change_loc('generate_invoice.php?menu_id=7')" class="submenu" onMouseOver="hov(this,'a submenuhov')" onMouseOut="hov(this,'submenu')">GENERATE INVOICE</a>
			<a name="INVOICE" onClick="javascript:change_loc('invoice_header.php?menu_id=8')" class="submenu" onMouseOver="hov(this,'a submenuhov')" onMouseOut="hov(this,'submenu')">INVOICE</a>			
			<a name="SAP_UPLOADING" onClick="javascript:change_loc('sap_uploading.php?menu_id=9')" class="submenu" onMouseOver="hov(this,'a submenuhov')" onMouseOut="hov(this,'submenu')">SAP UPLOADING</a>
		</div>
	</div>&nbsp;
	<div class="buttonscontainer">
		<div class="buttons" align="center">
			<a name="COLLECTION" class="menu" onMouseOver="hov(this,'a menuhovhdr')" onMouseOut="hov(this,'menu')">COLLECTION</a>
		</div>					
	</div>
	<div class="buttonscontainerchild" align="right" style="vertical-align:middle">		
		<div class="buttonschild" align="right" style="width:90%">			
			<a name="PAYMENT" onClick="javascript:change_loc('tenant_deposit.php?menu_id=17')" class="submenu" onMouseOver="hov(this,'a submenuhov')" onMouseOut="hov(this,'submenu')">SECURITY DEPOSIT</a>
		</div>
		<div class="buttonschild" align="right" style="width:90%">			
			<a name="PAYMENT" onClick="javascript:change_loc('payment_main.php')" class="submenu" onMouseOver="hov(this,'a submenuhov')" onMouseOut="hov(this,'submenu')">PAYMENT</a>
		</div>
		<div class="buttonschild" align="right" style="width:90%">			
			<a name="PAYMENT" onClick="javascript:change_loc('or_sap_uploading.php?menu_id=18')" class="submenu" onMouseOver="hov(this,'a submenuhov')" onMouseOut="hov(this,'submenu')">OR SAP UPLOADING</a>
		</div>
	</div>&nbsp;
	<div class="buttonscontainer">
		<div class="buttons" align="center">
			<a name="UTILITY" class="menu" onMouseOver="hov(this,'a menuhovhdr')" onMouseOut="hov(this,'menu')">UTILITY</a>
		</div>					
	</div>
	<div class="buttonscontainerchild" align="right" style="vertical-align:middle">		
		<div class="buttonschild" align="right" style="width:90%">			
			<a name="SEND INVOICE" onClick="javascript:change_loc('utility_send_invoice.php?menu_id=22')" class="submenu" onMouseOver="hov(this,'a submenuhov')" onMouseOut="hov(this,'submenu')">INVOICE ALERT</a>
		</div>
		<div class="buttonschild" align="right" style="width:90%">			
			<a name="UNPOST INVOICE" onClick="javascript:change_loc('unpost_invoice.php?menu_id=19')" class="submenu" onMouseOver="hov(this,'a submenuhov')" onMouseOut="hov(this,'submenu')">UNPOST INVOICE</a>
		</div>
		<div class="buttonschild" align="right" style="width:90%">			
			<a name="UNVOID INVOICE" onClick="javascript:change_loc('unvoid_invoice.php?menu_id=20')" class="submenu" onMouseOver="hov(this,'a submenuhov')" onMouseOut="hov(this,'submenu')">UNVOID INVOICE</a>
		</div>
		<div class="buttonschild" align="right" style="width:90%">			
			<a name="UNVOID INVOICE" onClick="javascript:change_loc('upload_aging.php?menu_id=21')" class="submenu" onMouseOver="hov(this,'a submenuhov')" onMouseOut="hov(this,'submenu')">UPLOAD AGING</a>
		</div>
		<div class="buttonschild" align="right" style="width:90%">			
			<a name="CHARGE AMOUNT MASS UPDATE" onClick="javascript:change_loc('update_charge_amount.php?menu_id=23')" class="submenu" onMouseOver="hov(this,'a submenuhov')" onMouseOut="hov(this,'submenu')">CHARGE AMOUNT MASS UPDATE</a>
		</div>
	</div>&nbsp;
	<div class="buttonscontainer">
		<div class="buttons" align="center">
			<a name="REPORTS" onClick="javascript:change_loc('reports.php?menu_id=13')" class="menu" onMouseOver="hov(this,'a menuhov')" onMouseOut="hov(this,'menu')">REPORTS</a>
		</div>					
	</div>&nbsp;
	<div class="buttonscontainer">
		<div class="buttons" align="center">
			<a name="REPORTS" onClick="javascript:change_loc('security_menu.php?menu_id=14')" class="menu" onMouseOver="hov(this,'a menuhov')" onMouseOut="hov(this,'menu')">SECURITY</a>
		</div>					
	</div>&nbsp;
	<div class="buttonscontainer">
		<div class="buttons" align="center">
			<a name="CHANGE_PASSWORD" onClick="javascript:change_loc('change_password.php')" class="menu" onMouseOver="hov(this,'a menuhov')" onMouseOut="hov(this,'menu')">CHANGE PASSWORD</a>
		</div>					
	</div>&nbsp;
	<div class="buttonscontainer">
		<div class="buttons" align="center">
		<a name="LOG OUT" onClick="javascript:log_out()" class="menu" onMouseOver="hov(this,'a menuhov')" onMouseOut="hov(this,'menu')">LOG OUT</a>
		</div>					
	</div>
	<input type="hidden" name="hidMode" id="hidMode">
</form>
</body>
</html>

<script type="text/javascript">
function hov(loc,cls) {   
	if(loc.className)   
	loc.className=cls;   
} 

function change_loc(pFile) {
	parent.frames[2].location = pFile;
	return false;
}

function log_out() {
	if (confirm("Are you sure you want to log out?")) {		
		frmMenu.hidMode.value = "OUT"
		frmMenu.submit();		
		return false;
	}
}

function log_out2() {
	if (confirm("Are you sure you want to log out?")) {		
		<?php setcookie("userid","",time()-3600);
			setcookie("username","",time()-3600);
		?> 
		parent.frames.location ="login.php";					
		return false;
	}
}
</script>


