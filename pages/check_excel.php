<?php
require_once 'libs/PHPExcel/Classes/PHPExcel.php';

$strFile = "C:\\System\\rms\\pages\\ftp\\RMS Aging Upload template 03 31 2025.xlsx";

if (!file_exists($strFile)) {
    die("File not found: $strFile");
}

try {
    $inputFileType = PHPExcel_IOFactory::identify($strFile);
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objPHPExcel = $objReader->load($strFile);

    // Use first worksheet, no name needed
    $sheet = $objPHPExcel->getSheet(0);

    $highestRow = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();

    echo "<table border='1' cellpadding='3' cellspacing='0'>";
    echo "<tr><th>#</th><th>col1</th><th>col2</th><th>col3</th><th>Total</th><th>Current</th><th>Total Overdue</th><th>1-30</th><th>31-60</th><th>61-90</th><th>91-120</th><th>121-150</th><th>>151</th><th>Write Off</th><th>col14</th><th>col15</th></tr>";

    $ctr = 0;

    for ($row = 2; $row <= $highestRow; $row++) {
        $rowData = $sheet->rangeToArray("A{$row}:O{$row}", null, true, false);
        $rowData = $rowData[0];

        if (implode('', $rowData) == '') continue;

        $col1 = $rowData[0];
        $col2 = $rowData[1];
        $col3 = $rowData[2];
        $col4 = ($rowData[3] != "") ? $rowData[3] : 0;
        $col5 = ($rowData[4] != "") ? $rowData[4] : 0;
        $col6 = ($rowData[5] != "") ? $rowData[5] : 0;
        $col7 = ($rowData[6] != "") ? $rowData[6] : 0;
        $col8 = ($rowData[7] != "") ? $rowData[7] : 0;
        $col9 = ($rowData[8] != "") ? $rowData[8] : 0;
        $col10 = ($rowData[9] != "") ? $rowData[9] : 0;
        $col11 = ($rowData[10] != "") ? $rowData[10] : 0;
        $col12 = ($rowData[11] != "") ? $rowData[11] : 0;
        $col13 = ($rowData[12] != "") ? $rowData[12] : 0;
        $col14 = $rowData[13];
        $col15 = $rowData[14];

        echo "<tr>";
        echo "<td>" . (++$ctr) . "</td>";
        echo "<td>" . htmlspecialchars($col1) . "</td>";
        echo "<td>" . htmlspecialchars($col2) . "</td>";
        echo "<td>" . htmlspecialchars($col3) . "</td>";
        echo "<td>" . $col4 . "</td>";
        echo "<td>" . $col5 . "</td>";
        echo "<td>" . $col6 . "</td>";
        echo "<td>" . $col7 . "</td>";
        echo "<td>" . $col8 . "</td>";
        echo "<td>" . $col9 . "</td>";
        echo "<td>" . $col10 . "</td>";
        echo "<td>" . $col11 . "</td>";
        echo "<td>" . $col12 . "</td>";
        echo "<td>" . $col13 . "</td>";
        echo "<td>" . htmlspecialchars($col14) . "</td>";
        echo "<td>" . htmlspecialchars($col15) . "</td>";
        echo "</tr>";
    }

    echo "</table>";
    echo "<p>Read $ctr rows from first worksheet.</p>";

} catch (Exception $e) {
    die("Error reading Excel: " . $e->getMessage());
}
?>
