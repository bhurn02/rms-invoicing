<?php
// echo phpinfo();
// die();
// require_once($_SERVER['APPL_PHYSICAL_PATH']."config.php");
// require_once($_SERVER['APPL_PHYSICAL_PATH']."config.php");
// require_once($_SERVER['APPL_PHYSICAL_PATH']."utilities".DIRECTORY_SEPARATOR."config.php");
require_once($_SERVER['APPL_PHYSICAL_PATH'] . "config.php");

$sessUserID=null;
$sessUserName=null;

function connection() {
	// $dsn="acs";
	$dsn=DSN_NAME;
	//$server = "localhost";
	//$db = "pmp";
	// $username="sa";
	// $password="@dmin^";
	$username=DB_USERNAME;
	$password=DB_PASSWORD;
	$sqlconnect=odbc_connect($dsn,$username,$password);
	return $sqlconnect;
}

function connectionTo($DSN_NAME) {
	// $dsn="acs";
	$dsn=$DSN_NAME;
	//$server = "localhost";
	//$db = "pmp";
	// $username="sa";
	// $password="@dmin^";
	$username=DB_USERNAME;
	$password=DB_PASSWORD;
	$sqlconnect=odbc_connect($dsn,$username,$password);
	return $sqlconnect;
}

function cn() {	
	// $server = "acs";
	$server = DSN_NAME;
	$db = "";
	// $username="sa";
	// $password="@dmin^";
	$username=DB_USERNAME;
	$password=DB_PASSWORD;
	// $report_path="C:\\System\\saipan tribune - acs web\\pages\\modules\\reports\\.rpt files\\";	
	// $pdf_path="C:\\System\\saipan tribune - acs web\\pages\\modules\\reports\\pdf\\";
	$report_path=dirname(__FILE__)."\\modules\\reports\\.rpt files\\";	
	$pdf_path=dirname(__FILE__)."\\modules\\reports\\pdf\\";
	$pdf_link = "reports/pdf/";
	$rpt_file_path = "reports/";
	$mainrpt_pdf_link = "pdf/";
	
	$cn=array("1"=>$server,"2"=>$db,"3"=>$username,"4"=>$password,"5"=>$report_path,"6"=>$pdf_path,"7"=>$pdf_link,"8"=>$rpt_file_path,"9"=>$mainrpt_pdf_link);
	
	return $cn;
}

function mssql_executequery($query,$dsn_name=null) {
	$hasError = 0;	
	$sqlconnect = ($dsn_name)?connectionTo($dsn_name):connection();
	$retStatus = 200;	

	try {		
		$process=odbc_exec($sqlconnect, $query);        				
		$retStatus==200;
	} 
	catch(Exception $e) { 
		print_r($e);  
	    $hasError = 1;
	    $retStatus = 500;
	    $strResult='Something went wrong while executing your query [mssql_executequery]. Please contact your administrator.';
	}

	odbc_free_result($process);
	odbc_close($sqlconnect);

	return $retStatus;
}

function mssql_resultset($query,$dsn_name=null) {	
	$sqlconnect = ($dsn_name)?connectionTo($dsn_name):connection();
	$resultset = array();

	try {		
		$process=odbc_exec($sqlconnect, $query);
		while($record=odbc_fetch_array($process)){
			$resultset[] = $record;
		}		
	} 
	catch(Exception $e) { 
		echo "<pre>";
		print_r($e); 
		echo "</pre>"; 	    
	}

	odbc_free_result($process);
	odbc_close($sqlconnect);

	return $resultset;
}

function setup_dns($server, $db) {
	return "DRIVER={ODBC Driver 17 for SQL Server};SERVER=$server;PORT=1433;DATABASE=$db";
}

function ftp() {
	$host = 'thc-resalie';
	$usr = 'ftp_user';
	$pwd = '';
	//$path = 'D:\\System\\poi-pax svc\\pages\\ftp\\';
	//$path = 'D:\\websites\\poi-pax svc\\pages\\modules\\transactions\\reports\\pdf\\';
	$path = 'C:\\Drive D\\System\\csis ver 2.0\\pages\\ftp\\';
	
	$ftp=array("1"=>$host,"2"=>$usr,"3"=>$pwd,"4"=>$path);
	return $ftp;
}

function rowcolor($no) {
	if ($no==1)
		$rowColor = "#D7DFE1";	
	else
		$rowColor = "white";	
	return $rowColor;
}

function loginimagepath() {
	$strImagePath = "images/";
	return $strImagePath;
}

function imagepath() {
	$strImagePath = "../../images/";
	return $strImagePath;
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

function numberformat_nocomma($pNum) {
	return number_format($pNum,2,'.','');
}

function ms_escape_string($data) {
    if ( !isset($data) or empty($data) ) return '';
    if ( is_numeric($data) ) return $data;

    $non_displayables = array(
        '/%0[0-8bcef]/',            // url encoded 00-08, 11, 12, 14, 15
        '/%1[0-9a-f]/',             // url encoded 16-31
        '/[\x00-\x08]/',            // 00-08
        '/\x0b/',                   // 11
        '/\x0c/',                   // 12
        '/[\x0e-\x1f]/'             // 14-31
    );
    foreach ( $non_displayables as $regex )
        $data = preg_replace( $regex, '', $data );
    $data = str_replace("'", "''", $data );
    return $data;
}

function strToDouble($str) {
	return (double)filter_var($str, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
}

function unformatMoney($str) {
    return preg_replace("/([^0-9\\.])/i", "", $str);
}

function array_flatten($array) { 
	if (!is_array($array)) { 
	  return FALSE; 
	} 
	$result = array(); 
	foreach ($array as $key => $value) { 
	  if (is_array($value)) { 
		$result = array_merge($result, array_flatten($value)); 
	  } 
	  else { 
		$result[$key] = $value; 
	  } 
	} 
	return $result; 
} 

function single_array($arr){
	$new_arr=array();
	foreach($arr as $key){
        if(is_array($key)){
            $arr1=single_array($key);
            foreach($arr1 as $k){
                $new_arr[]=$k;
            }
        }
        else{
            $new_arr[]=$key;
        }
    }
    return $new_arr;
}

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function get_string_after($string, $substring, $returnstring="") {
	$pos = strpos($string, $substring);
	$returnstring = ($returnstring)?$returnstring:$string;
	if ($pos === false)
	 return $returnstring;
	else  
	 return(substr($string, $pos+strlen($substring)));
}

function get_string_before($string, $substring) {
	$pos = strpos($string, $substring);
	if ($pos === false)
	 return $string;
	else  
	 return(substr($string, 0, $pos));
}

function recursive($array){
    foreach($array as $key => $value){
        //If $value is an array.
        if(is_array($value)){
            //We need to loop through it.
            recursive($value);
        } else{
            //It is not an array, so print it out.
            return $value;
        }
    }
}

function colsFromArray(array $array, $keys)
{
    if (!is_array($keys)) $keys = [$keys];
    $filter = function($k) use ($keys){
       return in_array($k,$keys);
    };
    return array_map(function ($el) use ($keys,$filter) {
        return array_filter($el, $filter, ARRAY_FILTER_USE_KEY );
    }, $array);
}

function date_diff_duration_decimal($startdate, $enddate) {
	$date1 = new DateTime($startdate);
	$date2 = new DateTime($enddate);

	$diff = $date2->diff($date1);

	$hours = round($diff->s / 3600 + $diff->i / 60 + $diff->h + $diff->days * 24, 2);
	return $hours;
}

function filterArray($needle,$haystack){
    foreach($haystack as $v){
        if (stripos($v, $needle) !== false) return true;
    };
    return false;
}

function utf8_encode_deep(&$input) {
	if (is_string($input)) {
		$input = utf8_encode($input);
	} else if (is_array($input)) {
		foreach ($input as &$value) {
			utf8_encode_deep($value);
		}
		
		unset($value);
	} else if (is_object($input)) {
		$vars = array_keys(get_object_vars($input));
		
		foreach ($vars as $var) {
			utf8_encode_deep($input->$var);
		}
	}
}

?>