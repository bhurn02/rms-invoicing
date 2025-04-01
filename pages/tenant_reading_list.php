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
//echo $_POST["hidRowCtr"];
//echo $strMode;
if ($strMode == "DELETE") {
	$i = 1;
	$j = 0;
	$k = 0;
	while ($i <= $_POST["hidRowCtr"]) {
		//echo $_POST["chkDelete" . strval($i)] . "a";
		if (isset($_POST["chkDelete" . strval($i)])) {
			$sqlquery="exec sp_t_TenantReading_Delete 'DELETE'," . $_POST["hidReadingID" . $i] . ",'" . $sessUserID . "','" . $sessCompanyCode . "','" . $strIPAddr . "'";	
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
		$strMsg = "Some records were not deleted. These had been invoiced already.";
	}
	elseif ($j == 0 && $k > 0) {
		$strMsg = "Record/s deleted!";
	}
	elseif ($j > 0 && $k == 0) {
		$strMsg = "No record deleted! These had been invoiced already.";
	}
	else
		$strMsg = "No record deleted!";
}

$src = $_GET["src"];
$strSearchList = replacesinglequote($_POST["cboSearchList"]);
$strKeyword = replacesinglequote($_POST["txtKeyword"]);
$function_id = 2;
$sqlquerycbo="select * from s_module_functions_search_list where function_id = " . $function_id . " order by xorder";
$processcbo=odbc_exec($sqlconnect, $sqlquerycbo);
while (odbc_fetch_row($processcbo)) {
	if (replacedoublequotes($strSearchList) == replacedoublequotes(trim(odbc_result($processcbo,"column_code")))) {
		$cbosearchlist .= "<option selected value=\"" . trim(odbc_result($processcbo,"column_code")) . "\">" . trim(strtoupper(odbc_result($processcbo,"column_name"))) . "</option>";
	}
	else {
		$cbosearchlist .= "<option value=\"" . trim(odbc_result($processcbo,"column_code")) . "\">" . trim(strtoupper(odbc_result($processcbo,"column_name"))) . "</option>";
	}
}
$strKeyword = replacesinglequote($_POST["txtKeyword"]);
$sqlquery="exec sp_t_TenantReading_List '','" . $strSearchList . "','" . $strKeyword . "'," . $function_id . "";
$process=odbc_exec($sqlconnect, $sqlquery);
//echo $sqlquery;
if ($strMsg != ""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 

?>
<html> 
<head> 
<title>TENANT READING LIST</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color: #F3F5B4;">
<form id="frmTenantReadingList" name"frmTenantReadingList" method="post" action="tenant_reading_list.php?menu_id=<?php echo $menu_id;?>">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">TENANT READING - FIND
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			 </a></li>	
			 <li class="li_nc"><a href="#" onClick="javascript:cmdSearch_OnClick();">|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Search&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>	  
			 <li class="li_nc"><a href="#" onClick="javascript:cmdDelete_OnClick();">Delete&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>	  
			 <li class="li_nc"><a href="#" onClick="javascript:change_loc('tenant_reading.php?menu_id=<?php echo $menu_id;?>')">Back&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>	  
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
									<option value="">(ALL)</option>
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
					<table width="1000" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
						<tr height="30">				
							<td width="4%" class="tablehdr" align="center">Del
							</td>			
							<td  width="7%" class="tablehdr" align="center">Reading ID
							</td>
							<td width="10%" class="tablehdr" align="center">Tenant Code
							</td>
							<td width="24%" class="tablehdr" align="center">Tenant Name
							</td>							
							<td width="8%" class="tablehdr" align="center">Real Property Code
							</td>						
							<td width="9%" class="tablehdr" align="center">Building Code
							</td>							
							<td width="7%" class="tablehdr" align="center">Unit No.
							</td>	
							<td width="19%" class="tablehdr" align="center">Bill To
							</td>	
							<td width="6%" class="tablehdr" align="center">Reading From
							</td>							
							<td width="6%" class="tablehdr" align="center">Reading To
							</td>													
						</tr>
						<?php
						while(odbc_fetch_row($process)){
							$reading_id = trim(odbc_result($process,"reading_id")); 
							$tenant_code = replacedoublequotesforlists(odbc_result($process,"tenant_code")); 
							$tenant_name = replacedoublequotesforlists(odbc_result($process,"tenant_name")); 
							$real_property_code = replacedoublequotesforlists(odbc_result($process,"real_property_code")); 
							$building_code = replacedoublequotesforlists(odbc_result($process,"building_code")); 
							$unit_no = replacedoublequotesforlists(odbc_result($process,"unit_no")); 
							$bill_to = replacedoublequotesforlists(odbc_result($process,"bill_to")); 
							$reading_date_from = odbc_result($process,"date_from"); 
							$reading_date_to = odbc_result($process,"date_to"); 
							$billing_date_from = odbc_result($process,"billing_date_from"); 
							$billing_date_to = odbc_result($process,"billing_date_to"); 
							$invoice_no = odbc_result($process,"with_invoice"); 
							$ctr = $ctr+1;
							
							if ($ctr%2==1) 
								$rowColor = "98fb98";	
							else
								$rowColor = "ffffe0";			
						?>
						<tr bgcolor="<?php echo "$rowColor" ?>">							
							<td width="4%" class="values" align="center">
								<?php if ($invoice_no == 0) { ?>
									<input type="checkbox" name="chkDelete<?php echo "$ctr";?>" id="chkDelete<?php echo "$ctr";?>" value="<?php echo "$ctr";?>">								
								<?php } else { ?>
									<input type="checkbox" name="chkDelete<?php echo "$ctr";?>" disabled id="chkDelete<?php echo "$ctr";?>" value="<?php echo "$ctr";?>">								
								<?php } ?>
							</td>
							<td width="7%" class="values">&nbsp;<a href="tenant_reading.php?menu_id=<?php echo $menu_id;?>&reading_id=<?php echo "$reading_id";?>&mode=FIND"><?php echo "$reading_id";?></a>&nbsp;
							</td>
							<td width="10%" class="values">&nbsp;<?php echo "$tenant_code";?>&nbsp;
							</td>							
							<td width="24%" class="values">&nbsp;<?php echo "$tenant_name";?>&nbsp;
							</td>							
							<td width="8%" class="values">&nbsp;<?php echo "$real_property_code";?>&nbsp;
							</td>							
							<td width="9%" class="values">&nbsp;<?php echo "$building_code";?>&nbsp;
							</td>		
							<td width="7%" class="values">&nbsp;<?php echo "$unit_no";?>&nbsp;
							</td>							
							<td width="19%" class="values">&nbsp;<?php echo "$bill_to";?>&nbsp;
							</td>				
							<td width="6%" class="values">&nbsp;<?php echo "$reading_date_from";?>&nbsp;
							</td>		
							<td width="6%" class="values">&nbsp;<?php echo "$reading_date_to";?>&nbsp;
							</td>		
						</tr>
						<input type="hidden" name="hidReadingID<?php echo "$ctr";?>" id="hidReadingID<?php echo "$ctr";?>" value=<?php echo "$reading_id";?>>	
						<?php } ?>
					</table>	
					<table>
						<tr>
							<td class="values">&nbsp;
								<input type="checkbox" name="chkSelectAll" id="chkSelectAll" onClick="javascript:chkSelectAll_OnClick();">SELECT ALL
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
	<input type="hidden" id="hidRowCtr" name="hidRowCtr" value=<?php echo $ctr;?>>
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
	frmTenantReadingList.submit();
}

function chkSelectAll_OnClick() {
	var ctr
	ctr = frmTenantReadingList.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmTenantReadingList.chkDelete" + i);
		if (frmTenantReadingList.chkSelectAll.checked == true) {
			if (obj.disabled == false)
				obj.checked = true;
		}
		else {
			obj.checked = false;
		}
	}
}

function cmdDelete_OnClick() {
	var j
	j=0
	totalctr = frmTenantReadingList.hidRowCtr.value;
	for (i=1;i<=totalctr;i++) {
		obj = eval("frmTenantReadingList.chkDelete" + i);
		if (obj.checked == true) {
			j++;
		}
	}
	if (j > 0) {
		if (confirm("Are you sure you want to delete this record/s?")) {
			frmTenantReadingList.hidMode.value = "DELETE";
			frmTenantReadingList.submit();
		}
	}
	else {
		alert("Deleting is not allowed this time");
	}
}

function txtKeyword_onKeyUp(e) {
	if (e.keyCode==13) {
		frmTenantReadingList.submit();
	}
}
</script>
