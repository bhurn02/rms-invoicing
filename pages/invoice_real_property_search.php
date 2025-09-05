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
$strUnitSearch = "V";
if ($strMode == "SEARCH") {
	$strUnitSearch = $_POST["cboUnitSearch"];
}

$strKeyword = replacesinglequote($_POST["txtKeyword"]);
$sqlquery="exec sp_m_Tenant 'VACANT_UNITS','','" . $strKeyword . "','','','','','','','','','','','','','','','','','','','','','','','',''";
$process=odbc_exec($sqlconnect, $sqlquery);

if ($strMsg != ""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 

?>
<html> 
<head> 
<title>REAL PROPERTY - LOOKUP</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color: #F3F5B4;">
<form id="frmRealPropertyList" name"frmRealPropertyList" method="post">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">REAL PROPERTY - LOOKUP
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
					<table width="500" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
						<tr height="30">											
							<td  width="31%" class="tablehdr" align="center">&nbsp;Real Property Code&nbsp;
							</td>
							<td width="69%" class="tablehdr" align="center">&nbsp;Real Property Name&nbsp;
							</td>
						</tr>
						<?php
						while(odbc_fetch_row($process)){
							$unit_no = replacesinglequote(odbc_result($process,"unit_no")); 
							$building_code = replacesinglequote(odbc_result($process,"building_code")); 
							$real_property_code = replacesinglequote(odbc_result($process,"real_property_code")); 
							$real_property_name = odbc_result($process,"real_property_name"); 
							$ctr = $ctr+1;
							
							if ($ctr%2==1) 
								$rowColor = "98fb98";	
							else
								$rowColor = "ffffe0";			
						?>
						<tr bgcolor="<?php echo "$rowColor" ?>">														
							<td width="31%" class="values">&nbsp;<a name="realproperty" style="cursor:hand; color:#0033FF; text-decoration:underline" onClick="javascript:tenant('<?php echo "$real_property_code";?>','<?php echo "$real_property_name";?>','<?php echo "$building_code";?>','<?php echo "$unit_no";?>');"><?php echo "$real_property_name";?></a>&nbsp;
							</td>
							<td width="69%" class="values">&nbsp;<?php echo "$building_code";?>&nbsp;
							</td>							
						</tr>
						<input type="hidden" name="hidRealPropertyCode<?php echo "$ctr"?>" id="hidRealPropertyCode<?php echo "$ctr"?>" value="<?php echo "$real_property_code";?>">
						<input type="hidden" name="hidBldgCode<?php echo "$ctr"?>" id="hidBldgCode<?php echo "$ctr"?>" value="<?php echo "$building_code";?>">
						<input type="hidden" name="hidUnitNo<?php echo "$ctr"?>" id="hidUnitNo<?php echo "$ctr"?>" value="<?php echo "$unit_no";?>">
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
	frmRealPropertyList.hidMode.value = "SEARCH";
	frmRealPropertyList.submit();
}

function cmdClose_OnClick() {
	window.close();
}

function txtKeyword_onKeyUp(e) {
	if (e.keyCode==13) {
		frmRealPropertyList.submit();
	}
}

function tenant(pRealPropertyCode,pRealPropertyName,pBldgCode,pUnitNo) {
	window.opener.frmInvoiceHdr.txtRealPropertyName.value = pRealPropertyName;
	window.opener.frmInvoiceHdr.hidRealPropertyCode.value = pRealPropertyCode;
	window.close();
}

</script>
