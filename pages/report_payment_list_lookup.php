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
		echo "<script> parent.frames.location = \"" . "accessnotallowed.htm" .  "\";</script>";
		exit(); 
	}
}

//end access
$sqlconnect = connection();
$strIPAddr = $_SERVER["REMOTE_ADDR"];
$menu_id = $_GET["menu_id"];

$strSrc = $_GET["src"];
$strMode = $_POST["hidMode"];
$strMsg = "";
if ($strMode == "VOID") {
	$i = 1;
	$j = 0;
	$k = 0;
	while ($i <= $_POST["hidRowCtr"]) {
		if (isset($_POST["chkSelect" . strval($i)])) {
			$sqlquery="exec sp_t_Invoice 'VOID','" . $_POST["hidCode" . $i] . "','','','','','','','','" . $strKeyword . "','','',''";
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

$strKeyword = replacesinglequote($_POST["txtKeyword"]);
$sqlquery="exec sp_t_Payment_Header 'VIEW','','','',0,'','','','" . $strKeyword . "','','',0,0,'','',''";
$process=odbc_exec($sqlconnect, $sqlquery);

if ($strMsg != ""){
	echo "<script>alert(\"" . $strMsg .  "\");</script>";
} 

?>
<html> 
<head> 
<title>OR - UNPAID BILLS LOOKUP</title> 
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="buttons.css" type="text/css">
<link rel="stylesheet" type="text/css" media="screen,projection" href="buttonmenu.css">
</head> 
<body style="border:1px solid; border-color:gray; border-top:none; border-top-color:none; margin:'0';background-color: #F3F5B4;">
<form id="frmARReportLookup" name"frmARReportLookup" method="post">
	<div class="mainmenu">	
		<ul>
			  <li class="li_nc"><a name="MODULE NAME">OR - UNPAID BILLS LOOKUP
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			  
			 
			 </a></li>	
			 <li class="li_nc"><a href="#" onClick="javascript:cmdSearch_OnClick();">|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Search&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>	  			 
			 <li class="li_nc"><a href="#" onClick="javascript:window.close()">Close&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>	  
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
					<table width="650" style="border:1px solid #556b2f;padding: 30px 10px 5px;">
						<tr height="30">														
							<td  width="21%" class="tablehdr" align="center">&nbsp;OR No.&nbsp;
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
							$or_no = trim(odbc_result($process,"or_no")); 
							$or_date = odbc_result($process,"or_date"); 
							$client_name = odbc_result($process,"client_name"); 
							$strStatus = odbc_result($process,"status"); 
							$strStatusDesc = odbc_result($process,"status_desc"); 
							$ctr = $ctr+1;
							
							if ($ctr%2==1) 
								$rowColor = "98fb98";	
							else
								$rowColor = "ffffe0";			
						?>
						<tr bgcolor="<?php echo "$rowColor" ?>">														
							<td width="21%" class="values">&nbsp;<a name="or_no" style="cursor:hand; color:#0033FF; text-decoration:underline" onClick="javascript:invoice('<?php echo "$or_no";?>','<?php echo "$or_no";?>');"><?php echo "$or_no";?></a>&nbsp;
							</td>
							<td width="13%" class="values" align="center">&nbsp;<?php echo "$or_date";?>&nbsp;
							</td>
							<td width="47%" class="values">&nbsp;<?php echo "$client_name";?>&nbsp;
							</td>							
							<td width="14%" class="values">&nbsp;<?php echo "$strStatusDesc";?>&nbsp;
							</td>							
						</tr>
						<input type="hidden" name="hidCode<?php echo "$ctr;"?>" id="hidCode<?php echo "$ctr;"?>" value="<?php echo "$or_no";?>">						
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
	<input type="hidden" id="hidSrc" name="hidSrc" value="<?php echo $strSrc;?>">
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
	frmARReportLookup.submit();
}

function invoice(pORNo) {
	if (frmARReportLookup.hidSrc.value=="from") 
		window.opener.frmInvoiceReport.txtORNo.value = pORNo;
	else
		window.opener.frmInvoiceReport.txtORNo.value = pORNo;
	window.close();
}

function txtKeyword_onKeyUp(e) {
	if (e.keyCode==13) {
		frmARReportLookup.submit();
	}
}
</script>
