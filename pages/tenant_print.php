<?php
include("functions.php");

$sqlconnect = connection();
$strIPAddr = $_SERVER["REMOTE_ADDR"];
$menu_id = $_GET["menu_id"];
$strCode = $_GET["tenant_code"];

$sqlquery="exec sp_rpt_Tenant_Lease_Agreement '" . $strCode . "'";	
$process=odbc_exec($sqlconnect, $sqlquery);
if (odbc_fetch_row($process)) {
		$strCode = odbc_result($process,"tenant_code");
		$strName = odbc_result($process,"tenant_name");
		//$strName = $strName;
		//echo $strName;
		$strRealPropertyCode = odbc_result($process,"real_property_code");
		$strRealPropertyName = odbc_result($process,"real_property_name");
		$strBuildingCode = odbc_result($process,"building_code");
		$strUnitNo = strtoupper(odbc_result($process,"unit_no"));
		$strBillTo = odbc_result($process,"bill_to");
		if (trim($strBillTo) == trim ($strCode)) 
			$strBillToName = "";
		else {
			$strBillToName = odbc_result($process,"bill_to_name");
			$strName = $strBillToName;
		}
		
		$strContactNo1 = odbc_result($process,"contact_no1");
		$strContactNo2 = odbc_result($process,"contact_no2");
		$strAddress1 = odbc_result($process,"address1");	
		$strAddress2 = odbc_result($process,"address2");	
		$strRealPropertyDBAName = odbc_result($process,"real_property_dba_name");	
		$strRealPropertyAddress3 = odbc_result($process,"real_property_address3");	
		$intLeaseTerm = odbc_result($process,"lease_term");	
		$strLeaseTermUnit = odbc_result($process,"lease_term_unit");	
		$dblApartmentRent = odbc_result($process,"apt_rent_amount");	
		$dblWaterCharge = odbc_result($process,"water_charge");	
		$strMeterNo = odbc_result($process,"meter_number");	
		$strLastMeterReading = odbc_result($process,"last_meter_reading");	
		$dblSecurityDeposit = odbc_result($process,"security_deposit_amount");	
		
		//echo date("m/d/Y",(strtotime(odbc_result($process,"contract_eff_date"))+60*60*24*($OFFSET)));
		if (odbc_result($process,"contract_eff_date") == "" || date("m/d/Y",(strtotime(odbc_result($process,"contract_eff_date"))+60*60*24*($OFFSET))) == "01/01/1970")
			$dtEffDate = "";
		else
			$dtEffDate = date("m/d/Y",(strtotime(odbc_result($process,"contract_eff_date"))+60*60*24*($OFFSET)));
		
		if (odbc_result($process,"contract_expiry_date") == "" || date("m/d/Y",(strtotime(odbc_result($process,"contract_expiry_date"))+60*60*24*($OFFSET))) == "01/01/1970")	
			$dtExpiryDate = "";
		else
			$dtExpiryDate = date("m/d/Y",(strtotime(odbc_result($process,"contract_expiry_date"))+60*60*24*($OFFSET)));	
			
		$strSAPCode = odbc_result($process,"sap_code");	
		if (odbc_result($process,"is_sap_affiliate") == "Y") {
			$blnSAPAffiliate = "checked";	
			$strNewCode = odbc_result($process,"new_code");	
			$disNewCode = "";					
			$disBusinessArea = "";					
			$strBusinessArea = odbc_result($process,"business_area");	
		}
		else {
			$blnSAPAffiliate = "";	
			$strNewCode = "";	
			$strBusinessArea = "";	
		}
		
		if (odbc_result($process,"terminated") == "Y") {
			$blnTerminated = "checked";	
			$disdtTerminated = "";
			if (odbc_result($process,"date_terminated") == "" || date("m/d/Y",(strtotime(odbc_result($process,"date_terminated"))+60*60*24*($OFFSET))) == "01/01/1970") {
				$dtTerminated = "";
			}
			else {
				$dtTerminated = date("m/d/Y",(strtotime(odbc_result($process,"date_terminated"))+60*60*24*($OFFSET)));	
			}
		}
		else {
			$blnTerminated = "";	
			$dtTerminated = "";
		}
		
		if (odbc_result($process,"actual_move_in_date") == "" || date("m/d/Y",(strtotime(odbc_result($process,"actual_move_in_date"))+60*60*24*($OFFSET))) == "01/01/1970")	
			$dtActualMoveIn= "";
		else
			$dtActualMoveIn = date("m/d/Y",(strtotime(odbc_result($process,"actual_move_in_date"))+60*60*24*($OFFSET)));	
			
		$strEmailAdd = odbc_result($process,"email_add");	
		
		if (odbc_result($process,"is_affiliate_employee") == "Y") 
			$blnEmployee = "checked";	
		else
			$blnEmployee = "";
			
		$strEmployer = odbc_result($process,"employer");	
		$strTenantType = trim(odbc_result($process,"tenant_type"));					
}

//***PRINTING
require_once('library/fpdf/fpdf.php');
require_once('library/fpdi/fpdi.php');

// initiate FPDI
$pdf =& new FPDI();

//font
$pdf->AddFont('ArialNB','','arialnb.php');
$strFont = "ArialNB";
$strFontStyle = "";
$intFontSize = 11;

// add a page
// set the sourcefile
$pdf->setSourceFile('reports/pdf_template/template_tenant_lease_agreement.pdf');
$pdf->AddPage();
// import page 1
$tplIdx = $pdf->importPage(1);
// use the imported page and place it at point 10,10 with a width of 100 mm
$pdf->useTemplate($tplIdx, 0, 0);
// now write some text above the imported page
$pdf->SetFont($strFont,$strFontStyle,$intFontSize);
//$pdf->SetTextColor(255,0,0);
$pdf->SetXY(61, 34);
$pdf->Write(0, $strRealPropertyDBAName);
$pdf->SetXY(23, 43);
$pdf->Write(0, $strName);
$pdf->SetXY(23, 48);
$pdf->Write(0, $strAddress1);

$pdf->SetXY(178, 66);
$pdf->Write(0, trim($strUnitNo));
$pdf->SetXY(22, 71);
$pdf->Write(0, $strRealPropertyDBAName);
$pdf->SetXY(92, 71);
$pdf->Write(0, $strRealPropertyAddress3);

$pdf->SetXY(94, 111);
$pdf->Write(0, strtoupper(convert_number($intLeaseTerm) . " " . $strLeaseTermUnit));
$pdf->SetXY(145, 111);
$pdf->Write(0, $intLeaseTerm);
$pdf->SetXY(27, 115);
$pdf->Write(0, $dtEffDate);
$pdf->SetXY(77, 115);
$pdf->Write(0, $dtExpiryDate);
$pdf->SetXY(99, 138);
$pdf->Write(0, strtoupper($intLeaseTerm . " " . $strLeaseTermUnit));

$pdf->SetXY(86, 165);
$intApartmentRentPos = strpos($dblApartmentRent,".");
if ($intApartmentRentPos != false)
$dblApartmentRentCents = substr($dblApartmentRent,$intApartmentRentPos+1,strlen($dblApartmentRent)-$intApartmentRentPos);
//echo $dblSecurityDepositCents;
//exit();
if ($dblApartmentRentCents <> 0)
	$pdf->Write(0, strtoupper(convert_number($dblApartmentRent)) . " AND " . $dblApartmentRentCents . "/100 DOLLAR/S");
else
	$pdf->Write(0, strtoupper(convert_number($dblApartmentRent)) . " " . "DOLLAR/S");
	
$pdf->SetXY(177, 165);
$pdf->Write(0, $dblApartmentRent);

$pdf->SetXY(92, 210);
$intSecurityDepositCentsPos = strpos($dblSecurityDeposit,".");
if ($intSecurityDepositCentsPos != false)
$dblSecurityDepositCents = substr($dblSecurityDeposit,$intSecurityDepositCentsPos+1,strlen($dblSecurityDeposit)-$intSecurityDepositCentsPos);
//echo $dblSecurityDepositCents;
//exit();
if ($dblSecurityDepositCents <> 0)
	$pdf->Write(0, strtoupper(convert_number($dblSecurityDeposit)) . " AND " . $dblSecurityDepositCents . "/100 DOLLAR/S");
else
	$pdf->Write(0, strtoupper(convert_number($dblSecurityDeposit)) . " " . "DOLLAR/S");
$pdf->SetXY(175, 210);
$pdf->Write(0, $dblSecurityDeposit);

$pdf->SetXY(23, 265);
$pdf->Write(0, $strUnitNo);
$pdf->SetXY(66, 265);
$pdf->Write(0, strtoupper($strMeterNo . " (READING: " . $strLastMeterReading . ")"));

$footer = strtoupper(trim($strRealPropertyDBAName) . " UNIT NO. " . trim($strUnitNo) . " LEASE - " . trim($strName));
$pdf->SetFont($strFont,"",8);
$pdf->SetXY(22, 273);
$pdf->Write(0, $footer);
$pdf->SetFont($strFont,$strFontStyle,$intFontSize);

$pdf->AddPage();
// import page 1
$tplIdx = $pdf->importPage(2);
// use the imported page and place it at point 10,10 with a width of 100 mm
$pdf->useTemplate($tplIdx, 0, 0);
// now write some text above the imported page
//$pdf->SetFont('Arial');
//$pdf->SetTextColor(255,0,0);
$pdf->SetXY(99, 25);
$pdf->Write(0, $dblWaterCharge);

$pdf->SetFont($strFont,"",8);
$pdf->SetXY(22, 273);
$pdf->Write(0, $footer);
$pdf->SetFont($strFont,$strFontStyle,$intFontSize);

$pdf->AddPage();
$tplIdx = $pdf->importPage(3);
$pdf->useTemplate($tplIdx, 0, 0);

$pdf->SetFont($strFont,"",8);
$pdf->SetXY(22, 273);
$pdf->Write(0, $footer);
$pdf->SetFont($strFont,$strFontStyle,$intFontSize);

$pdf->AddPage();
$tplIdx = $pdf->importPage(4);
$pdf->useTemplate($tplIdx, 0, 0);
$pdf->SetXY(42, 245);
$pdf->Write(0, strtoupper($intLeaseTerm . " " . $strLeaseTermUnit));

$pdf->SetFont($strFont,"",8);
$pdf->SetXY(22, 273);
$pdf->Write(0, $footer);
$pdf->SetFont($strFont,$strFontStyle,$intFontSize);

$pdf->AddPage();
$tplIdx = $pdf->importPage(5);
$pdf->useTemplate($tplIdx, 0, 0);
$pdf->SetXY(123, 209);
$pdf->Write(0, strtoupper($strName));
$pdf->SetXY(44, 213);
$pdf->Write(0, strtoupper($strRealPropertyDBAName));

$pdf->SetFont($strFont,"",8);
$pdf->SetXY(22, 273);
$pdf->Write(0, $footer);
$pdf->SetFont($strFont,$strFontStyle,$intFontSize);


$pdf->AddPage();
$tplIdx = $pdf->importPage(6);
$pdf->useTemplate($tplIdx, 0, 0);
$pdf->SetXY(22, 60);
$pdf->Write(0, strtoupper($strRealPropertyDBAName . " UNIT NO.:"));
$pdf->SetXY(100, 60);
$pdf->Write(0, strtoupper($strUnitNo));
$pdf->SetXY(95, 61);
$pdf->Write(0, "_____________");
$pdf->SetXY(61, 213);
$pdf->Write(0, strtoupper($strName));

$pdf->SetFont($strFont,"",8);
$pdf->SetXY(22, 273);
$pdf->Write(0, $footer);
$pdf->SetFont($strFont,$strFontStyle,$intFontSize);

$pdf->Output($footer . ".pdf", 'D');


function convert_number($number) 
{ 
    if (($number < 0) || ($number > 999999999)) 
    { 
    throw new Exception("Number is out of range");
    } 

    $Gn = floor($number / 1000000);  /* Millions (giga) */ 
    $number -= $Gn * 1000000; 
    $kn = floor($number / 1000);     /* Thousands (kilo) */ 
    $number -= $kn * 1000; 
    $Hn = floor($number / 100);      /* Hundreds (hecto) */ 
    $number -= $Hn * 100; 
    $Dn = floor($number / 10);       /* Tens (deca) */ 
    $n = $number % 10;               /* Ones */ 

    $res = ""; 

    if ($Gn) 
    { 
        $res .= convert_number($Gn) . " Million"; 
    } 

    if ($kn) 
    { 
        $res .= (empty($res) ? "" : " ") . 
            convert_number($kn) . " Thousand"; 
    } 

    if ($Hn) 
    { 
        $res .= (empty($res) ? "" : " ") . 
            convert_number($Hn) . " Hundred"; 
    } 

    $ones = array("", "One", "Two", "Three", "Four", "Five", "Six", 
        "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", 
        "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", 
        "Nineteen"); 
    $tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty", 
        "Seventy", "Eighty", "Ninety"); 

    if ($Dn || $n) 
    { 
        if (!empty($res)) 
        { 
            $res .= " "; 
        } 

        if ($Dn < 2) 
        { 
            $res .= $ones[$Dn * 10 + $n]; 
        } 
        else 
        { 
            $res .= $tens[$Dn]; 

            if ($n) 
            { 
                $res .= "-" . $ones[$n]; 
            } 
        } 
    } 

    if (empty($res)) 
    { 
        $res = "zero"; 
    } 

    return $res; 
} 

?>
<html> 
<head> 
<title>TENANT</title> 
</head> 
<body>
	<div><?php echo "hello";?></div>
</body>
</html>
