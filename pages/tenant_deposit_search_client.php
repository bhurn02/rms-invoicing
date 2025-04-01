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

$strMode = $_POST["hidMode"];
$strMsg = "";
if ($strMode == "DELETE") {
	$i = 1;
	$j = 0;
	$k = 0;
	while ($i <= $_POST["hidRowCtr"]) {
		if (isset($_POST["chkDelete" . strval($i)])) {
			$sqlquery="exec sp_m_Units 'DELETE','" . $_POST["hidRealPropertyCode" . $i] . "','" . $_POST["hidBldgCode" . $i] . "','" . $_POST["hidUnitNo" . $i] . "',0,'','',''";
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
		$strMsg = "Some records were not voided because it had been posted.";
	}
	elseif ($j == 0 && $k > 0) {
		$strMsg = "Record/s voided!";
	}
	elseif ($j > 0 && $k == 0) {
		$strMsg = "No record voided because it had been posted!";
	}
	else
		$strMsg = "No record voided!";
}


$strSearchList = replacesinglequote($_POST["cboSearchList"]);
$strKeyword = replacesinglequote($_POST["txtKeyword"]);
$function_id = 4;

$sqlquerycbo="select * from s_module_functions_search_list where function_id = " . $function_id . " order by xorder";
$processcbo=odbc_exec($sqlconnect, $sqlquerycbo);
while (odbc_fetch_row($processcbo)) {
	if (replacedoublequotes($strSearchList) == replacedoublequotes(trim(odbc_result($processcbo,"column_code")))) {
		$cbosearchlist .= "<option selected value=\"" . trim(odbc_result($processcbo,"column_code")) . "\">" . trim(strtoupper(odbc_result($processcbo,"column_name"))) . "</option>";
	}
	else {
		$cbosearchlist .= "<option value=\"" . trim(odbc_result($processcbo,"column_code")) . "\">" . trim(strtoupper(odbc_result($processcbo,"column_name"))) . "</option>";
		if ($strSearchList == "" && odbc_result($processcbo,"xorder") == 1)
			$strSearchList = trim(odbc_result($processcbo,"column_code"));
	}
}

$sqlquery="exec sp_t_List_Clients 'CLIENT_SEARCH','" . $strSearchList . "','" . $strKeyword . "'," . $function_id . "";
$process=odbc_exec($sqlconnect, $sqlquery);
//echo $sqlquery;
if ($strMsg != ""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 

?>
<html> 
<head> 
<title>CLIENT - LOOKUP</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color: #F3F5B4;">
<form id="frmSecDepositCientSearch" name"frmSecDepositCientSearch" method="post">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">TENANT SECURITY DEPOSIT >> CLIENT - LOOKUP
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			 </a></li>	
			 <li class="li_nc"><a href="#" onClick="javascript:cmdSearch_OnClick();">|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Search&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>	  
			 <li class="li_nc"><a href="#" onClick="javascript:cmdClose_OnClick()">Close&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>	  
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
							<td class="fieldname">Search by :</td>
							<td width="5"></td>
							<td>
								<select name="cboSearchList" id="cboSearchList" class="values">
									<?php echo $cbosearchlist; ?>
								</select>								
							</td>
							<td width="20" class="fieldname">&nbsp;&nbsp;=&nbsp;&nbsp;</td>
							<td>
								<input type=text name="txtKeyword" id="txtKeyword" class="values" size="40" value="<?php echo $strKeyword ?>" onKeyUp="javascript:txtKeyword_onKeyUp(event)">
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table width="700" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
						<tr height="30">											
							<td  width="50%" class="tablehdr" align="center">&nbsp;Client&nbsp;
							</td>
							<td width="30%" class="tablehdr" align="center">&nbsp;Real Property&nbsp;
							</td>
							<td width="20%" class="tablehdr" align="center">&nbsp;Building/Unit No.&nbsp;
							</td>
						</tr>
						<?php
						while(odbc_fetch_row($process)){
							$tenant_code = replacedoublequotesforlists(odbc_result($process,"tenant_code")); 
							$tenant_name = replacedoublequotesforlists(odbc_result($process,"tenant_name")); 
							$real_property_code = replacedoublequotesforlists(odbc_result($process,"real_property_code")); 
							$real_property_name = replacedoublequotesforlists(odbc_result($process,"real_property_name")); 
							$building_code = replacedoublequotesforlists(odbc_result($process,"building_code")); 
							$unit_no = replacedoublequotesforlists(odbc_result($process,"unit_no")); 
							$ctr = $ctr+1;
							
							if ($ctr%2==1) 
								$rowColor = "98fb98";	
							else
								$rowColor = "ffffe0";			
						?>
						<tr bgcolor="<?php echo "$rowColor" ?>">														
							<td width="50%" class="values">&nbsp;<a name="tenant" style="cursor:hand; color:#0033FF; text-decoration:underline" onClick="javascript:tenant('<?php echo "$tenant_code";?>','<?php echo "$tenant_name";?>');"><?php echo "$tenant_name";?></a>&nbsp;
							</td>
							<td width="30%" class="values">&nbsp;<?php echo "$real_property_name";?>&nbsp;
							</td>
							<td width="20%" class="values">&nbsp;<?php echo "$building_code";?>/<?php echo "$unit_no";?>&nbsp;
							</td>
						</tr>
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
	frmSecDepositCientSearch.submit();
}

function cmdClose_OnClick() {
	window.close();
}

function txtKeyword_onKeyUp(e) {
	if (e.keyCode==13) {
		frmSecDepositCientSearch.submit();
	}
}

function tenant(pTenantCode,pTenantName) {
	window.opener.frmTenantDeposit.hidClientCode.value = pTenantCode;
	window.opener.frmTenantDeposit.hidClientName.value = pTenantName;
	window.opener.frmTenantDeposit.txtClientName.value = pTenantName;
	window.close();
}

</script>
