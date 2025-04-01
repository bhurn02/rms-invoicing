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
	 <li class="li_nc"><a name="MODULE NAME">SECURITY
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			 </a></li>
      <li class="li_hc"><a href="#">|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;MENU&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a>
	  	<ul class="ul_ch">
			<li class="li_hc"><a href="#" onClick="javascript:change_loc('user_group.php?menu_id=14')">User Group</a></li>
			<li class="li_hc"><a href="#" onClick="javascript:change_loc('user_group_modules.php?menu_id=14')">User Group Modules</a></li>
			<li class="li_hc"><a href="#" onClick="javascript:change_loc('users.php?menu_id=14')">Users</a></li>			
      	</ul>
	  </li>
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