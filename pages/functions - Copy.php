<?php
function connection() {
	$dsn="rms";
	//$server = "localhost";
	//$db = "rms";
	$username="sa";
	$password="@dmin^";
	$sqlconnect=odbc_connect($dsn,$username,$password);
	return $sqlconnect;
}

function cn() {
	$server = "localhost";
	$db = "rms";
	$username="sa";
	$password="@dmin^";
	$report_path="D:\\System\\RMS\\pages\\reports\\";
	//$report_path = "C:\\";
	$pdf_path="D:\\System\\RMS\\pages\\reports\\pdf\\";
	$pdf_link = "reports/pdf/";
	
	$cn=array("1"=>$server,"2"=>$db,"3"=>$username,"4"=>$password,"5"=>$report_path,"6"=>$pdf_path,"7"=>$pdf_link);
	return $cn;
}

function ftp() {
	$host = '192.168.110.15';
	$usr = 'rms-spn1\ftp_user';
	$pwd = '0ftpuse123';
	$path = 'D:\\System\\rms\\pages\\ftp\\';
	
	$ftp=array("1"=>$host,"2"=>$usr,"3"=>$pwd,"4"=>$path);
	return $ftp;
}

function buttons($pNewPage,$pPageType) {
	  if ($pPageType==1) {
		  echo "<li class='li_nc'><a href='#' target='_self' >|&nbsp;&nbsp;&nbsp;Retrieve&nbsp;&nbsp;&nbsp;|</a></li>";
		  echo "<li class='li_nc'><a href='#' target='_self' >Save&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>";
		  echo "<li class='li_nc'><a href='#' target='_self' >Delete&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>";
		  echo "<li class='li_nc'><a href='#' onClick='javascript:change_loc(\"" . $pNewPage . "\")'>Find&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>";
		  echo "<li class='li_nc'><a href='#'  >Cancel&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>";
	  }
	  else {
		  echo "<li class='li_nc'><a href='#' onClick='javascript:change_loc(\"" . $pNewPage . "\")'>|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Back&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</a></li>";
	  }
 }

function menuaccess($module_id,$user_id) {
	$menu = 0;
	$sqlconnect = connection();
	$sqlquery="exec sp_s_Menu_Access " . $module_id . ",'" . $user_id . "'";	
	$process=odbc_exec($sqlconnect, $sqlquery);
	if (odbc_fetch_row($process)) {
			$menu = odbc_result($process,"x");
	}
	return $menu;
}

function replacesinglequote2($pStr) {
	return trim(str_replace("'","''",$pStr));
}

function replacesinglequote3($pStr) {
	return trim(str_replace("\""," ",trim(str_replace("'"," ",$pStr))));
}

function replacesinglequote($pStr) {
	//$strVar =  trim(str_replace("''"," ",$pStr));
	return trim(str_replace("'","''",$pStr));
	//$strVar = trim(str_replace("\"","",$strVar));
	//return $strVar;
}

function replacedoublequotes($pStr) { // used for data display
	$strVar = trim(str_replace("''","'",$pStr));
	$strVar = trim(str_replace("\"","",$strVar));
	return $strVar;
}

function replacedoublequotesforlists($pStr) { // used in links, lists
	$strVar = trim(str_replace("''","'",$pStr));
	$strVar = trim(str_replace("'","",$pStr));
	$strVar = trim(str_replace("\"","",$strVar));
	return $strVar;
}


function replacedoublequotes4($pStr) {
	return trim(str_replace("''","'",$pStr));
}

function replacedoublequote($pStr) {
	return trim(str_replace("'","",trim(str_replace("''"," ",$pStr))));
}

function findsinglequote($pStr) {
	return strpos(trim($pStr),"'");
}

function finddoublequote($pStr) {
	return strpos(trim($pStr),"\"");
}

function numberformat($pNum) {
	return number_format($pNum,2,'.',',');
}
 ?>