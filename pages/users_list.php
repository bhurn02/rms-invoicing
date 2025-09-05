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

$strMode = $_POST["hidMode"];
$strMsg = "";
if ($strMode == "DELETE") {
	$i = 1;
	$j = 0;
	$k = 0;
	while ($i <= $_POST["hidRowCtr"]) {
		if (isset($_POST["chkDelete" . strval($i)])) {
			$sqlquery="exec sp_s_User 'DELETE','" . $_POST["hidUserID" . strval($i)] . "','','','','',0,'','','','" . $sessUserID . "','" . $strIPAddr . "'";
			//echo $sqlquery;
			$process=odbc_exec($sqlconnect, $sqlquery);
			if (odbc_fetch_row($process)) {
				if (odbc_result($process,"x") == 1) 
					$j++;
				else
					$k++;
			}
		}
		$i++;
	}
	
	if ($j > 0 && $k > 0) {
		$strMsg = "Some records were not deleted. It had been used in other modules.";
	}
	elseif ($j == 0 && $k > 0) {
		$strMsg = "Record/s deleted!";
	}
	elseif ($j > 0 && $k == 0) {
		$strMsg = "No record deleted! It had been used in other modules.";
	}
	else
		$strMsg = "No record deleted!";
}

$strKeyword = replacesinglequote($_POST["txtKeyword"]);
$sqlquery="exec sp_s_User 'VIEW','','" . $strKeyword . "','','','',0,'','','','',''";	
$process=odbc_exec($sqlconnect, $sqlquery);

if ($strMsg != ""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 

?>
<html> 
<head> 
<title>USERS LIST</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color: #F3F5B4;">
<form id="frmUsersList" name"frmUsersList" method="post">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">USER MAINTENANCE - FIND
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;			  
			 </a></li>	
			 <li class="li_nc"><a href="#" onClick="javascript:cmdSearch_OnClick();">|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Search&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>	  
			 <li class="li_nc"><a href="#" onClick="javascript:cmdDelete_OnClick();">Delete&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>	  
			 <li class="li_nc"><a href="#" onClick="javascript:change_loc('users.php?menu_id=<?php echo $menu_id;?>')">Back&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>	  
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
							<td class="fieldname">Type keyword here :</td>
							<td width="5"></td>
							<td><input type=text name="txtKeyword" id="txtKeyword" class="values" size="40" value="<?php echo $strKeyword ?>" onKeyUp="javascript:txtKeyword_onKeyUp(event)"></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table width="600" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
						<tr height="30">				
							<td width="7%" class="tablehdr" align="center">&nbsp;Del&nbsp;
							</td>			
							<td  width="25%" class="tablehdr" align="center">&nbsp;User ID&nbsp;
							</td>
							<td width="46%" class="tablehdr" align="center">&nbsp;Name&nbsp;
							</td>
							<td width="22%" class="tablehdr" align="center">&nbsp;User Group&nbsp;
							</td>
						</tr>
						<?php
						while(odbc_fetch_row($process)){
							$user_id = odbc_result($process,"user_id"); 
							$fullname = odbc_result($process,"fullname"); 
							$group_desc = odbc_result($process,"group_desc"); 
							$ctr = $ctr+1;
							
							if ($ctr%2==1) 
								$rowColor = "98fb98";	
							else
								$rowColor = "ffffe0";			
						?>
						<tr bgcolor="<?php echo "$rowColor" ?>">							
							<td width="7%" class="values" align="center">
								<input type="checkbox" name="chkDelete<?php echo "$ctr"?>" id="chkDelete<?php echo "$ctr"?>" value="<?php echo "$ctr"?>">								
							</td>
							<td width="25%" class="values">&nbsp;<a href="users.php?menu_id=<?php echo $menu_id;?>&userid=<?php echo "$user_id";?>&mode=FIND"><?php echo "$user_id";?></a>&nbsp;
							</td>
							<td width="46%" class="values">&nbsp;<?php echo "$fullname";?>&nbsp;
							</td>
							<td width="22%" class="values">&nbsp;<?php echo "$group_desc";?>&nbsp;
							</td>
						</tr>
						<input type="hidden" name="hidUserID<?php echo "$ctr"?>" id="hidUserID<?php echo "$ctr"?>" value="<?php echo "$user_id";?>">						
						<?php } ?>
					</table>	
				</td>
			</tr>
		</table>
		</td>
		</tr>
	</table>
	<input type="hidden" id="hidMode" name="hidMode">
	<input type="hidden" id="hidRowCtr" name="hidRowCtr" value=<?php echo $ctr?>>
</form>
</body> 
</html>

<script type="text/javascript">

function change_loc(pFile) {
	parent.frames[2].location = pFile;
	return false;
}

function hov(loc,cls) {   
	if(loc.className)   
	loc.className=cls;   
} 

function cmdSearch_OnClick() {
	frmUsersList.submit();
}

function cmdDelete_OnClick() {
	if (confirm("Are you sure you want to delete this record/s?")) {
		frmUsersList.hidMode.value = "DELETE";
		frmUsersList.submit();
	}
}

function txtKeyword_onKeyUp(e) {
	if (e.keyCode==13) {
		frmUsersList.submit();
	}
}
</script>
