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
			$sqlquery="exec sp_m_Charges 'DELETE','" . $_POST["hidCode" . $i] . "','','','','" . $sessUserID . "','" . $sessCompanyCode . "','" . $strIPAddr . "'";
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
		$strMsg = "Some records were not deleted. Remove assignment first from Unit Charges and/or Tenant Charges module.";
	}
	elseif ($j == 0 && $k > 0) {
		$strMsg = "Record/s deleted!";
	}
	elseif ($j > 0 && $k == 0) {
		$strMsg = "No record deleted! Remove assignment first from Unit Charges and/or Tenant Charges module.";
	}
	else
		$strMsg = "No record deleted!";
}

$strKeyword = replacesinglequote($_POST["txtKeyword"]);
$sqlquery="exec sp_m_Charges 'VIEW','','" . $strKeyword . "','','','','',''";
$process=odbc_exec($sqlconnect, $sqlquery);

if ($strMsg != ""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 
?>
<html> 
<head> 
<title>CHARGES LIST</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color: #F3F5B4;">
<form id="frmChargesList" name"frmChargesList" method="post">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">CHARGES - FIND
 			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  
			 
			 </a></li>	
			 <li class="li_nc"><a href="#" onClick="javascript:cmdSearch_OnClick();">|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Search&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>	  
			 <li class="li_nc"><a href="#" onClick="javascript:cmdDelete_OnClick();">Delete&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>	  
			 <li class="li_nc"><a href="#" onClick="javascript:change_loc('charges.php?menu_id=<?php echo $menu_id;?>')">Back&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>	  
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
							<td width="5%" class="tablehdr" align="center">&nbsp;Del&nbsp;
							</td>			
							<td  width="15%" class="tablehdr" align="center">&nbsp;Charge Code&nbsp;
							</td>
							<td width="40%" class="tablehdr" align="center">&nbsp;Description&nbsp;
							</td>							
							<td width="15%" class="tablehdr" align="center">&nbsp;GL Code&nbsp;
							</td>							
							<td width="20%" class="tablehdr" align="center">&nbsp;Type&nbsp;
							</td>							
						</tr>
						<?php
						while(odbc_fetch_row($process)){
							$code = trim(odbc_result($process,"charge_code")); 
							$desc = odbc_result($process,"charge_desc"); 
							$gl_code = odbc_result($process,"gl_code"); 
							$charge_type = odbc_result($process,"charge_type"); 
							$ctr = $ctr+1;
							
							if ($ctr%2==1) 
								$rowColor = "98fb98";	
							else
								$rowColor = "ffffe0";			
						?>
						<tr bgcolor="<?php echo "$rowColor" ?>">							
							<td width="5%" class="values" align="center">
								<input type="checkbox" name="chkDelete<?php echo "$ctr"?>" id="chkDelete<?php echo "$ctr"?>" value="<?php echo "$ctr"?>">								
							</td>
							<td width="15%" class="values">&nbsp;<a href="charges.php?menu_id=<?php echo $menu_id;?>&code=<?php echo "$code";?>&mode=FIND"><?php echo "$code";?></a>&nbsp;
							</td>
							<td width="40%" class="values">&nbsp;<?php echo "$desc";?>&nbsp;
							</td>							
							<td width="15%" class="values">&nbsp;<?php echo "$gl_code";?>&nbsp;
							</td>							
							<td width="20%" class="values">&nbsp;<?php echo "$charge_type";?>&nbsp;
							</td>							
						</tr>
						<input type="hidden" name="hidCode<?php echo "$ctr"?>" id="hidCode<?php echo "$ctr"?>" value="<?php echo "$code";?>">						
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
	frmChargesList.submit();
}

function cmdDelete_OnClick() {
	if (confirm("Are you sure you want to delete this record/s?")) {
		frmChargesList.hidMode.value = "DELETE";
		frmChargesList.submit();
	}
}

function txtKeyword_onKeyUp(e) {
	if (e.keyCode==13) {
		frmChargesList.submit();
	}
}
</script>
