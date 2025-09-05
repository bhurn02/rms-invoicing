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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>REPORTS</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=8" />
<link rel="stylesheet" type="text/css" media="screen,projection" href="reportsmenu.css" />
</head>
<body style="margin:0; background-color:#F3F5B4">
<div class="mainmenu">
<ul>	
	 <li class="li_nc"><a name="MODULE NAME">REPORTS
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			 </a></li>
      <li class="li_hc"><a href="#">Maintenance Listings</a>
	  	<ul class="ul_ch">
			<li class="li_hc"><a href="#" onClick="javascript:change_loc('report_realproperty.php?menu_id=13')">Real Property</a></li>
			<li class="li_hc"><a href="#">Units</a>
				<ul class="ul_ch">
					<li class="li_hc"><a href="#" onClick="javascript:change_loc('report_units.php?menu_id=13&status=O')">Occupied</a></li>
					<li class="li_hc"><a href="#" onClick="javascript:change_loc('report_units.php?menu_id=13&status=V')">Vacant</a></li>
					<li class="li_hc"><a href="#" onClick="javascript:change_loc('report_units.php?menu_id=13&status=R')">Reserved</a></li>
					<li class="li_hc"><a href="#" onClick="javascript:change_loc('report_units_customized.php?menu_id=13')">Customized</a></li>
				</ul>
			</li>
			<li class="li_hc"><a href="#" onClick="javascript:change_loc('report_charges.php?menu_id=13')">Charges</a></li>
			<li class="li_hc"><a href="#" onClick="javascript:change_loc('report_unit_charges_customized.php?menu_id=13')">Unit Charges</a></li>
			
			<li class="li_hc"><a href="#">Tenant</a>
				<ul class="ul_ch">
					<li class="li_hc"><a href="#" onClick="javascript:change_loc('report_tenant_customized.php?menu_id=13')">All</a></li>
					<li class="li_hc"><a href="#" onClick="javascript:change_loc('report_tenant_for_renewal.php?menu_id=13')">For Renewal</a></li>
				</ul>
			</li>
			
			<li class="li_hc"><a href="#" onClick="javascript:change_loc('report_tenant_charges_customized.php?menu_id=13')">Tenant Charges</a></li>
      	</ul>
	  </li>
      <li class="li_hc"><a href="#">Transaction Listings</a><ul class="ul_ch">
		 <li class="li_nc"><a name="TENANT_READING" onclick="javascript:change_loc('report_tenant_reading_customized.php?menu_id=13')">Tenant Reading</a></li>
         <li class="li_nc"><a name="INVOICE" onclick="javascript:change_loc('report_invoice_listing_customized.php?menu_id=13')">Invoice</a></li>
		 <li class="li_nc"><a name="OR LIST" onclick="javascript:change_loc('report_payment_list.php?menu_id=13')">OR</a></li>
      </ul></li>   
	   <li class="li_hc"><a href="#">Activity</a><ul class="ul_ch">		 
         <li class="li_nc"><a name="INVOICE" onclick="javascript:change_loc('report_invoice.php?menu_id=13')">Invoice</a></li>
		 <li class="li_nc"><a name="UNPAID_BILLS" onclick="javascript:change_loc('report_payment.php?menu_id=13')">OR - Unpaid Bills</a></li>
		 <li class="li_nc"><a name="ADVANCE_PAYMENT" onclick="javascript:change_loc('report_ar_payment.php?menu_id=13')">OR - Advance Payment</a></li>		 		 
      </ul></li>     
	  <li class="li_hc"><a href="#">Management</a><ul class="ul_ch">
	  	 <li class="li_nc"><a name="OCCUPANCY" onclick="javascript:change_loc('report_occupancy.php?menu_id=13')">Occupancy Report</a></li>
	  	 <li class="li_nc"><a name="OCCUPANCY" onclick="javascript:change_loc('report_occupancy_asof.php?menu_id=13')">Occupancy Report (As Of)</a></li>
	  	 <li class="li_nc"><a name="SOA" onclick="javascript:change_loc('report_soa.php?menu_id=13')">Statement of Account</a></li>		 
      </ul></li>     
	  <li class="li_hc"><a href="#">Notice</a><ul class="ul_ch">
	  	 <li class="li_nc"><a name="OCCUPANCY" onclick="javascript:change_loc('report_notice_renewal.php?menu_id=13')">Lease Agreement Renewal</a></li>
	  	 <li class="li_nc"><a name="SOA" onclick="javascript:change_loc('report_demand_notice_first.php?menu_id=13')">Demand to Pay - First Notice</a></li>		 
		 <li class="li_nc"><a name="SOA" onclick="javascript:change_loc('report_demand_notice_final.php?menu_id=13')">Demand to Pay - Final Notice</a></li>		 
      </ul></li>     
</ul>
</div>
</body>
</html>

<script type="text/javascript">

function change_loc(pFile) {
	window.open (pFile);
	return false;
}
	
</script>