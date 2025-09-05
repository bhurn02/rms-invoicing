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
$uid = $sessUserID;
$company_code = $sessCompanyCode;
if ($strMode == "VOID") {
	$i = 1;
	$j = 0;
	$k = 0;
	while ($i <= $_POST["hidRowCtr"]) {
		if (isset($_POST["chkSelect" . strval($i)])) {
			$sqlquery="exec sp_t_Invoice 'VOID','" . $_POST["hidCode" . $i] . "','','','','','','','','" . $strKeyword . "','','" . $uid . "','" . $company_code . "','" . $strIPAddr . "'";	
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
	if ($j > 0) {
		$strMsg = "Some invoices were not voided.";
	}
	elseif ($k > 0) {
		$strMsg = "Invoice/s voided!";
	}
	else
		$strMsg = "No invoice voided!";
}

$src = $_GET["src"];
$strSearchList = replacesinglequote($_POST["cboSearchList"]);
$strKeyword = replacesinglequote($_POST["txtKeyword"]);
$function_id = 3;

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
	$sqlquery="exec sp_t_Invoice_List 'VIEW_STAT','" . $strSearchList . "','" . $strKeyword . "'," . $function_id . "";
}
else {	
	$sqlquery="exec sp_t_Invoice_List 'VIEW','" . $strSearchList . "','" . $strKeyword . "'," . $function_id . "";
}

$process=odbc_exec($sqlconnect, $sqlquery);

if ($strMsg != ""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 

?>
<html> 
<head> 
<title>INVOICE LIST</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color: #F3F5B4;">
<form id="frmChargesList" name"frmChargesList" method="post">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">INVOICE - FIND
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  
			 </a></li>	
			 <li class="li_nc"><a href="#" onClick="javascript:cmdSearch_OnClick();">|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Search&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>	  
			 <li class="li_nc"><a href="#" onClick="javascript:change_loc('invoice_detail.php?menu_id=<?php echo $menu_id;?>')">Back&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>	  
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
					<table width="650" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
						<tr height="30">																				
							<td  width="21%" class="tablehdr" align="center">&nbsp;Invoice No.&nbsp;
							</td>
							<td  width="13%" class="tablehdr" align="center">&nbsp;Date&nbsp;
							</td>
							<td width="47%" class="tablehdr" align="center">&nbsp;Client&nbsp;
							</td>		
							<td width="14%" class="tablehdr" align="center">&nbsp;Status&nbsp;
							</td>				
						</tr>
						<?php
						while(odbc_fetch_row($process)){
							$invoice_no = trim(odbc_result($process,"invoice_no")); 
							$invoice_date = odbc_result($process,"invoice_date"); 
							$tenant_name = odbc_result($process,"tenant_name"); 
							$strStatus = odbc_result($process,"status"); 
							$strStatusDesc = odbc_result($process,"status_desc"); 
							$ctr = $ctr+1;
							
							if ($ctr%2==1) 
								$rowColor = "98fb98";	
							else
								$rowColor = "ffffe0";			
						?>
						<tr bgcolor="<?php echo "$rowColor" ?>">														
							<td width="21%" class="values">&nbsp;<a href="invoice_detail.php?menu_id=<?php echo $menu_id;?>&invoice_no=<?php echo "$invoice_no";?>&mode=FIND"><?php echo "$invoice_no";?></a>&nbsp;
							</td>
							<td width="13%" class="values" align="center">&nbsp;<?php echo "$invoice_date";?>&nbsp;
							</td>
							<td width="47%" class="values">&nbsp;<?php echo "$tenant_name";?>&nbsp;
							</td>				
							<td width="14%" class="values">&nbsp;<?php echo "$strStatusDesc";?>&nbsp;
							</td>					
						</tr>
						<input type="hidden" name="hidCode<?php echo "$ctr;"?>" id="hidCode<?php echo "$ctr;"?>" value="<?php echo "$invoice_no";?>">						
						<?php } ?>
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
	frmChargesList.submit();
}

function cmdVoid_OnClick() {
	if (confirm("Are you sure you want to void this invoice/s?")) {
		frmChargesList.hidMode.value = "VOID";
		frmChargesList.submit();
	}
}

function txtKeyword_onKeyUp(e) {
	if (e.keyCode==13) {
		frmChargesList.submit();
	}
}
</script>
