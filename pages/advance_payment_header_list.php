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

$uid = $sessUserID;
$company_code = $sessCompanyCode;

$strMode = $_POST["hidMode"];
$strMsg = "";
if ($strMode == "VOID") {
	$i = 1;
	$j = 0;
	$k = 0;
	while ($i <= $_POST["hidRowCtr"]) {
		if (isset($_POST["chkSelect" . strval($i)])) {
			$sqlquery="exec sp_t_Advance_Payment_Header 'VOID','" . $_POST["hidCode" . $i] . "','','',0,'','','','','','',0,0,'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";
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
elseif ($strMode == "POST") {
	$i = 1;
	$j = 0;
	$k = 0;
	while ($i <= $_POST["hidRowCtr"]) {
		if (isset($_POST["chkSelect" . strval($i)])) {
			$sqlquery="exec sp_t_Advance_Payment_Header 'POST','" . $_POST["hidCode" . $i] . "','','',0,'','','','','','',0,0,'" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";
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
	$strMsg = "Record/s posted!";	
}
$src = $_GET["src"];
$strSearchList = replacesinglequote($_POST["cboSearchList"]);
$strKeyword = replacesinglequote($_POST["txtKeyword"]);
$function_id = 7;

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

if ($src =="home") {
	$sqlquery="exec sp_t_Advance_Payment_Header_List 'VIEW_STAT','" . $strSearchList . "','" . $strKeyword . "'," . $function_id . "";
}
else {
	$sqlquery="exec sp_t_Advance_Payment_Header_List 'VIEW','" . $strSearchList . "','" . $strKeyword . "'," . $function_id . "";
}
$process=odbc_exec($sqlconnect, $sqlquery);
//echo $sqlquery;
if ($strMsg != ""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 
?>
<html> 
<head> 
<title>ADVANCE PAYMENT LIST</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color: #F3F5B4;">
<form id="frmPaymentHdrList" name"frmPaymentHdrList" method="post" action="advance_payment_header_list.php?menu_id=<?php echo $menu_id;?>">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">ADVANCE PAYMENT - FIND
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;			  
			 </a></li>	
			 <li class="li_nc"><a href="#" onClick="javascript:cmdSearch_OnClick();">|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Search&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>	  
			 <li class="li_nc"><a href="#" onClick="javascript:cmdPost_OnClick();">Post&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>	  
			 <li class="li_nc"><a href="#" onClick="javascript:cmdVoid_OnClick();">Void&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>	  			 
			 <li class="li_nc"><a href="#" onClick="javascript:change_loc('advance_payment_header.php?menu_id=<?php echo $menu_id;?>')">Back&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>	  
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
					<table width="600" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
						<tr height="30">				
							<td width="6%" class="tablehdr" align="center">&nbsp;Sel&nbsp;
							</td>			
							<td  width="19%" class="tablehdr" align="center">&nbsp;OR No.&nbsp;
							</td>
							<td  width="20%" class="tablehdr" align="center">&nbsp;Date&nbsp;
							</td>
							<td width="40%" class="tablehdr" align="center">&nbsp;Client&nbsp;
							</td>							
							<td width="15%" class="tablehdr" align="center">&nbsp;Status&nbsp;
							</td>							
						</tr>
						<?php
						while(odbc_fetch_row($process)){
							$or_no = trim(odbc_result($process,"or_no")); 
							$or_date = odbc_result($process,"or_date"); 
							$client_name = odbc_result($process,"client_name"); 
							$status = odbc_result($process,"status"); 
							$status_desc = odbc_result($process,"status_desc"); 
							$ctr = $ctr+1;
							
							if ($ctr%2==1) 
								$rowColor = "98fb98";	
							else
								$rowColor = "ffffe0";			
						?>
						<tr bgcolor="<?php echo "$rowColor"; ?>">							
							<td width="6%" class="values" align="center">
								<?php if ($status=="") { ?>
									<input type="checkbox" name="chkSelect<?php echo "$ctr";?>" id="chkSelect<?php echo "$ctr";?>" value="<?php echo "$ctr";?>">								
								<?php } else { ?>
									<input type="checkbox" name="chkSelect<?php echo "$ctr";?>" disabled id="chkSelect<?php echo "$ctr";?>" value="<?php echo "$ctr";?>">								
								<?php } ?>
							</td>
							<td width="19%" class="values">&nbsp;<a href="advance_payment_header.php?menu_id=<?php echo $menu_id;?>&or_no=<?php echo "$or_no";?>&mode=FIND"><?php echo "$or_no";?></a>&nbsp;
							</td>
							<td width="20%" class="values" align="center">&nbsp;<?php echo "$or_date";?>&nbsp;
							</td>
							<td width="40%" class="values">&nbsp;<?php echo "$client_name";?>&nbsp;
							</td>							
							<td width="15%" class="values">&nbsp;<?php echo "$status_desc";?>&nbsp;
							</td>							
						</tr>
						<input type="hidden" name="hidCode<?php echo "$ctr";?>" id="hidCode<?php echo "$ctr";?>" value="<?php echo "$or_no";?>">						
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

function chkSelectAll_OnClick() {
	var ctr
	ctr = frmPaymentHdrList.hidRowCtr.value;
	for (i=1;i<=ctr;i++) {
		obj = eval("frmPaymentHdrList.chkSelect" + i);
		if (frmPaymentHdrList.chkSelectAll.checked == true) {
			if (obj.disabled == false)
				obj.checked = true;
		}
		else {
			obj.checked = false;
		}
	}
}

function change_loc(pFile) {
	parent.frames[2].location = pFile;
	return false;
}

function hov(loc,cls) {   
	if(loc.className)   
	loc.className=cls;   
} 

function cmdSearch_OnClick() {
	frmPaymentHdrList.submit();
}

function cmdVoid_OnClick() {
	if (confirm("Are you sure you want to void this record/s?")) {
		frmPaymentHdrList.hidMode.value = "VOID";
		frmPaymentHdrList.submit();
	}
}

function cmdPost_OnClick() {
	if (confirm("Are you sure you want to post this record/s? There is no undo for this operation.")) {
		frmPaymentHdrList.hidMode.value = "POST";
		frmPaymentHdrList.submit();
	}
}

function cmdDelete_OnClick() {
	if (confirm("Are you sure you want to delete this record/s?")) {
		frmPaymentHdrList.hidMode.value = "DELETE";
		frmPaymentHdrList.submit();
	}
}

function txtKeyword_onKeyUp(e) {
	if (e.keyCode==13) {
		frmPaymentHdrList.submit();
	}
}
</script>
