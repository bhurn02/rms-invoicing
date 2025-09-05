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
}

//end access

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>REPORTS</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=8" />
<link rel="stylesheet" type="text/css" media="screen,projection" href="reportsmenu.css" />
</head>
<body style="margin:0; background-color:#F3F5B4">
<div class="mainmenu">
<ul>	
	 <li class="li_nc"><a name="MODULE NAME">PAYMENT
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			 </a></li>
      <li class="li_hc"><a href="#" onClick="javascript:change_loc('payment_header.php?menu_id=10')">|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Unpaid Bills&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a>
	  </li>
      <li class="li_hc"><a href="#" onclick="javascript:change_loc('advance_payment_header.php?menu_id=11')">Advance Payment&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>     
	  <li class="li_hc"><a href="#">OR Processing&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a><ul class="ul_ch">
		  <li class="li_hc"><a href="#" onclick="javascript:change_loc('or_processing_advance_payment_list.php?menu_id=12')">Advance Payment</a></li> 
	  </ul></li>   		  
</ul>
</div>
</body>
</html>

<script type="text/javascript">

function change_loc(pFile) {
	parent.frames[2].location = pFile;
	return false;
}
	
</script>