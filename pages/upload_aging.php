<?php
include("functions.php");
require_once 'libs/PHPExcel/Classes/PHPExcel.php';

$sessUserID = isset($_COOKIE['userid']) ? $_COOKIE['userid'] : "";
$sessUserName = isset($_COOKIE['username']) ? $_COOKIE['username'] : "";
$sessCompanyCode = isset($_COOKIE['company_code']) ? $_COOKIE['company_code'] : "";

if (trim($sessUserID) == "") {
    echo "<script> parent.frames.location = 'accessnotallowed.htm';</script>";
    exit();
} else {
    $menu_access = menuaccess($_GET["menu_id"], trim($sessUserID));
    if ($menu_access == 0) {
        echo "<script> parent.frames[2].location = 'accessnotallowed.htm';</script>";
        exit();
    }
}

$sqlconnect = connection();
$strIPAddr = $_SERVER["REMOTE_ADDR"];
$menu_id = $_GET["menu_id"];

$uid = $sessUserID;
$company_code = $sessCompanyCode;

$strMode = isset($_POST["hidMode"]) ? trim($_POST["hidMode"]) : "";
$dtAsOf = isset($_POST["DPC_txtDateFrom"]) ? $_POST["DPC_txtDateFrom"] : date("m/d/Y");
$strSearchValue = isset($_POST["txtSearchValue"]) ? $_POST["txtSearchValue"] : "";

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'upload';

$strMsg = "";
$blankWarning = "";
$previewData = array();
$strFile = "";
$searchResults = array();

if ($strMode == "UPLOAD") {
    $ftp = ftp();
    $path = $ftp[4];

    if (isset($_FILES["txtFile"]) && $_FILES["txtFile"]["error"] == UPLOAD_ERR_OK) {
        $fileName = basename($_FILES["txtFile"]["name"]);
        $strFile = $path . $fileName;

        if (!move_uploaded_file($_FILES["txtFile"]["tmp_name"], $strFile)) {
            $strMsg = "Failed to save uploaded file.";
        } else {
            try {
                $inputFileType = PHPExcel_IOFactory::identify($strFile);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($strFile);
                $sheet = $objPHPExcel->getSheet(0);
                $highestRow = $sheet->getHighestRow();

                for ($row = 2; $row <= $highestRow; $row++) {
                    $data = $sheet->rangeToArray("A{$row}:O{$row}", null, true, false);
                    $rowData = $data[0];

                    $col0 = strtolower(trim($rowData[0]));
                    $col1 = strtolower(trim($rowData[1]));
                    if ($col0 == "account no." && $col1 == "tenants") continue;

                    $previewData[] = $rowData;
                }

                $invalidCount = 0;
                foreach ($previewData as $row) {
                    $acc = trim($row[0]);
                    $tenant = trim($row[1]);
                    if ($acc === "" || $tenant === "") {
                        $invalidCount++;
                    }
                }

                if ($invalidCount > 0) {
                    $blankWarning = "$invalidCount row" . ($invalidCount > 1 ? "s have" : " has") . " missing Account No. or Tenant Name and will not be saved.";
                }

            } catch (Exception $e) {
                $strMsg = "Error reading Excel file: " . $e->getMessage();
            }
        }
    } else {
        $strMsg = "No file uploaded or upload error.";
    }
} elseif ($strMode == "CONFIRM") {
    $strFile = $_POST['hidUploadedFile'];
    if (!file_exists($strFile)) {
        $strMsg = "Uploaded file not found.";
    } else {
        try {
            $inputFileType = PHPExcel_IOFactory::identify($strFile);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($strFile);
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $ctr = 0;
            $hdr_id = null;

            for ($row = 2; $row <= $highestRow; $row++) {
                $data = $sheet->rangeToArray("A{$row}:O{$row}", null, true, false);
                $rowData = $data[0];

                $col0 = strtolower(trim($rowData[0]));
                $col1 = strtolower(trim($rowData[1]));
                if ($col0 == "account no." && $col1 == "tenants") continue;

                $acc = trim($rowData[0]);
                $tenant = trim($rowData[1]);
                if ($acc === "" || $tenant === "") continue;

                if ($hdr_id === null) {
                    $sqlquery = "exec sp_u_UploadAgingHeader_Save 0, '$strFile', '$dtAsOf', '$uid', '$company_code', '$strIPAddr'";
                    // die($sqlquery);
                    $process = odbc_exec($sqlconnect, $sqlquery);
                    if (odbc_fetch_row($process)) {
                        $hdr_id = odbc_result($process, "wth_hdr_id");
                    }
                }

                $cols = array();
                foreach ($rowData as $col) {
                    $cols[] = ($col === null || trim($col) === "") ? 0 : replacesinglequote($col);
                }

                while (count($cols) < 15) $cols[] = "";

                // Ensure numeric values for columns 3-12, convert invalid characters to zero
                for ($i = 3; $i <= 12; $i++) {
                    if (isset($cols[$i])) {
                        $numericValue = $cols[$i];
                        // Remove any non-numeric characters except decimal point and minus sign
                        $cleaned = preg_replace('/[^0-9.-]/', '', $numericValue);
                        // Convert to float and check if it's a valid number
                        $floatValue = floatval($cleaned);
                        if (is_numeric($cleaned) && $floatValue >= 0) {
                            $cols[$i] = $floatValue;
                        } else {
                            $cols[$i] = 0; // Convert invalid values like "-" to zero
                        }
                    } else {
                        $cols[$i] = 0;
                    }
                }

                $sqlquery = "exec sp_u_UploadAgingDetail_Save $hdr_id, 
                    '{$cols[0]}', '{$cols[1]}', '{$cols[2]}', {$cols[3]}, {$cols[4]}, {$cols[5]}, {$cols[6]},
                    {$cols[7]}, {$cols[8]}, {$cols[9]}, {$cols[10]}, {$cols[11]}, {$cols[12]}, 
                    '{$cols[13]}', '{$cols[14]}'";
                // echo "$sqlquery <br><br>";
                odbc_exec($sqlconnect, $sqlquery);
                $ctr++;
            }

            $strMsg = "Successfully uploaded $ctr records.";
        } catch (Exception $e) {
            $strMsg = "Error processing file: " . $e->getMessage();
        }
    }
} elseif ($strMode == "SEARCH") {
    $sqlquery = "exec sp_u_UploadAging_Search '$dtAsOf', 'TENANT', '$strSearchValue'";
	$searchResults = mssql_resultset($sqlquery);
    // $process = odbc_exec($sqlconnect, $sqlquery);
    // while (odbc_fetch_row($process)) {
    //     $row = array();
    //     for ($i = 1; $i <= odbc_num_fields($process); $i++) {
    //         $row[] = odbc_result($process, $i);
    //     }
    //     $searchResults[] = $row;
    // }

	// echo "<pre>";
	// print_r($searchResults);
	// echo "</pre>";
	// die();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Upload & Search Aging Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    <style>
        .invalid-row td { background-color: #ffd6d6 !important; }
    </style>
</head>
<body class="container my-4">

<h2>Upload & Search Aging Report</h2>

<?php if ($strMsg != ""): ?>
    <div class="alert alert-info"><?php echo htmlspecialchars($strMsg); ?></div>
<?php endif; ?>

<?php if ($blankWarning != ""): ?>
    <div class="alert alert-warning alert-dismissible fade show">
        <?php echo $blankWarning; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<ul class="nav nav-tabs mb-3" id="tabs">
    <li class="nav-item">
        <a class="nav-link <?php if ($strMode != 'SEARCH') echo 'active'; ?>" href="#upload" data-bs-toggle="tab">Upload</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php if ($strMode == 'SEARCH') echo 'active'; ?>" href="#search" data-bs-toggle="tab">Search</a>
    </li>
</ul>

<div class="tab-content">
    <!-- Upload Tab -->
    <div class="tab-pane fade <?php if ($strMode != 'SEARCH') echo 'show active'; ?>" id="upload">
        <form method="post" enctype="multipart/form-data" class="mb-4">
            <input type="hidden" name="hidMode" value="UPLOAD">
            <div class="mb-3">
                <label class="form-label">Excel File:</label>
                <input type="file" name="txtFile" accept=".xls,.xlsx" class="form-control"<?php if ($strMode == 'CONFIRM') echo ' disabled'; ?>>
            </div>
            <div class="row g-2 mb-3">
                <div class="col-md-3">
                    <label class="form-label">As of Date:</label>
                    <input type="text" id="DPC_txtDateFrom" name="DPC_txtDateFrom" value="<?php echo htmlspecialchars($dtAsOf); ?>" class="form-control" required>
                </div>
                <div class="col-md-9 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">Upload</button>
                    <?php if (count($previewData) > 0): ?>
                        <button type="submit" name="hidMode" value="CONFIRM" class="btn btn-success">Confirm Upload</button>
                        <input type="hidden" name="hidUploadedFile" value="<?php echo htmlspecialchars($strFile); ?>">
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <?php if (count($previewData) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="table-secondary">
                        <tr>
                            <th>#</th>
                            <th>Account No.</th><th>Tenant Name</th><th>Real Property</th><th>Total</th><th>Current</th>
                            <th>Total Overdue</th><th>From 1 To 30</th><th>From 31 To 60</th><th>From 61 To 90</th>
                            <th>From 91 To 120</th><th>From 121 To 150</th><th>Over 151</th>
                            <th>For Write-Off</th><th>Remarks</th><th>Notice No.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach ($previewData as $row): ?>
                            <?php
                                $acc = trim($row[0]);
                                $tenant = trim($row[1]);
                                $isInvalid = ($acc === "" || $tenant === "");
                            ?>
                            <tr class="<?php echo $isInvalid ? 'invalid-row' : ''; ?>">
                                <td><?php echo $i++; ?></td>
                                <?php foreach ($row as $cell): ?>
                                    <td><?php echo htmlspecialchars($cell); ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Search Tab -->
    <div class="tab-pane fade <?php if ($strMode == 'SEARCH') echo 'show active'; ?>" id="search">
		<form method="post" class="mb-3">
			<input type="hidden" name="hidMode" value="SEARCH">
			<div class="row g-3">
				<div class="col-md-3">
					<label class="form-label">As of Date:</label>
					<input type="text" id="searchDate" name="DPC_txtDateFrom" value="<?php echo htmlspecialchars($dtAsOf); ?>" class="form-control" required>
				</div>
				<div class="col-md-4">
					<label class="form-label">Sort By:</label>
					<select name="cboSortBy" class="form-select">
						<option value="TENANT"<?php if ($strSortBy == "TENANT") echo " selected"; ?>>Tenant</option>
						<option value="REAL PROPERTY"<?php if ($strSortBy == "REAL PROPERTY") echo " selected"; ?>>Real Property</option>
						<option value="SAP ACCOUNT CODE"<?php if ($strSortBy == "SAP ACCOUNT CODE") echo " selected"; ?>>SAP Account Code</option>
						<option value="NOTICE NO."<?php if ($strSortBy == "NOTICE NO.") echo " selected"; ?>>Notice No.</option>
					</select>
				</div>
				<div class="col-md-3">
					<label class="form-label">Search Value:</label>
					<input type="text" name="txtSearchValue" value="<?php echo htmlspecialchars($strSearchValue); ?>" class="form-control">
				</div>
				<div class="col-md-2 d-flex align-items-end">
					<button type="submit" class="btn btn-secondary">Search</button>
				</div>
			</div>
		</form>


        <?php if (count($searchResults) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
					<thead class="table-secondary">
						<tr>
							<th>#</th>
							<th>Account No.</th><th>Tenant Name</th><th>Real Property</th><th>Total</th><th>Current</th>
							<th>Total Overdue</th><th>From 1 To 30</th><th>From 31 To 60</th><th>From 61 To 90</th>
							<th>From 91 To 120</th><th>From 121 To 150</th><th>Over 151</th>
							<th>For Write-Off</th><th>Remarks</th><th>Notice No.</th>
						</tr>
					</thead>
                    <tbody>
						<?php for ($j = 0; $j < count($searchResults); $j++): ?>
							<tr>
								<td><?php echo $j + 1; ?></td>
								<td><?php echo htmlspecialchars($searchResults[$j]['wta_sap_code']); ?></td>
								<td><?php echo htmlspecialchars($searchResults[$j]['wta_tenant_name']); ?></td>
								<td><?php echo htmlspecialchars($searchResults[$j]['wta_real_property_name']); ?></td>
								<td align="right"><?php echo number_format($searchResults[$j]['wta_total_balance'], 2); ?></td>
								<td align="right"><?php echo number_format($searchResults[$j]['wta_curr_balance'], 2); ?></td>
								<td align="right"><?php echo number_format($searchResults[$j]['wta_total_overdue'], 2); ?></td>
								<td align="right"><?php echo number_format($searchResults[$j]['wta_aging_1_30'], 2); ?></td>
								<td align="right"><?php echo number_format($searchResults[$j]['wta_aging_31_60'], 2); ?></td>
								<td align="right"><?php echo number_format($searchResults[$j]['wta_aging_61_90'], 2); ?></td>
								<td align="right"><?php echo number_format($searchResults[$j]['wta_aging_91_120'], 2); ?></td>
								<td align="right"><?php echo number_format($searchResults[$j]['wta_aging_121_150'], 2); ?></td>
								<td align="right"><?php echo number_format($searchResults[$j]['wta_aging_over_151'], 2); ?></td>
								<td align="right"><?php echo number_format($searchResults[$j]['wta_write_off'], 2); ?></td>
								<td><?php echo htmlspecialchars($searchResults[$j]['wta_remarks']); ?></td>
								<td><?php echo htmlspecialchars($searchResults[$j]['wta_notice_no']); ?></td>
							</tr>
						<?php endfor; ?>
						</tbody>
                </table>
            </div>
        <?php elseif ($strMode == 'SEARCH'): ?>
            <p class="text-muted">No results found.</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    flatpickr("#DPC_txtDateFrom", { dateFormat: "m/d/Y" });
    flatpickr("#searchDate", { dateFormat: "m/d/Y" });
</script>
</body>
</html>
