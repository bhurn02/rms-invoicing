<?php
// phpinfo();
// exit;
// index.php
// echo PHP_INT_SIZE === 4 ? "32-bit PHP" : "64-bit PHP";
// die();
require_once("system/functions.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>RMS Invoice Dispatcher</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
  <div class="container-fluid mt-4 px-4">
    <h1 class="text-center mb-3">RMS Invoice Dispatcher</h1>

    <!-- Toggles -->
    <div class="text-center mb-4">
      <label class="form-label fw-semibold">Test Mode</label>
      <div class="d-flex justify-content-center align-items-center gap-2">
        <span class="text-muted small">OFF</span>
        <label class="switch">
          <input type="checkbox" id="readOnlyToggle" checked>
          <span class="slider round"></span>
        </label>
        <span class="text-muted small">ON</span>
      </div>
      <div><span id="modeLabel" class="text-danger fw-semibold small">Emails will not be sent</span></div>

      <div class="mt-2">
        <button id="darkToggle" class="btn btn-sm btn-outline-secondary">Toggle Dark Mode</button>
      </div>
    </div>

    <!-- Stats -->
    <div class="mb-3 small">
      <p><strong>Total Records:</strong> <span id="totalRecords">0</span></p>
      <p><strong>Invalid Emails:</strong> <span id="invalidEmails">0</span></p>
      <p><strong>Invalid Attachments:</strong> <span id="invalidAttachments">0</span></p>
      <p><strong>Total Duration:</strong> <span id="totalDuration">N/A</span></p>
    </div>

    <!-- Progress Bar -->
    <div class="progress mb-3 d-none" id="progressContainer">
      <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%;">0%</div>
    </div>
    <p id="emailCount" class="fw-bold small d-none"></p>

    <!-- Buttons -->
    <div class="d-flex justify-content-center gap-3 mb-4">
      <button id="GenerateInvoice" class="btn btn-primary btn-lg"><i class="bi bi-gear"></i> Generate Invoice</button>
      <button id="startProcess" class="btn btn-primary btn-lg d-none"><i class="bi bi-send"></i> Send Emails</button>
      <button id="cancelProcess" class="btn btn-secondary btn-lg d-none"><i class="bi bi-x-circle"></i> Cancel</button>
      <button id="refreshRecords" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-clockwise"></i> Refresh</button>
    </div>

    <!-- Invoice Generation Progress -->
    <div class="progress mb-3 d-none" id="invoiceProgressContainer">
      <div id="invoiceProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%;">0%</div>
    </div>
    <p id="invoiceCount" class="fw-bold small d-none"></p>
    <p id="invoiceDuration" class="fw-bold small"></p>

    <!-- Layout -->
    <div class="row">
      <!-- Main Table -->
      <div class="col-md-8">
        <div class="input-group input-group-sm mb-2">
          <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
          <input type="text" id="tableSearch" class="form-control" placeholder="Search invoices, tenant, email, etc...">
        </div>

        <div class="table-responsive">
          <table class="table table-bordered table-striped table-sm small scrollable-tbody">
            <thead class="table-dark">
              <tr>
                <th>Invoice No</th>
                <th>Client Code</th>
                <th>Tenant Name</th>
                <th>Email</th>
                <th>Attachment</th>
              </tr>
            </thead>
            <tbody id="recordTable"></tbody>
          </table>
        </div>
      </div>

      <!-- Batch Logs -->
      <div class="col-md-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h6 class="fw-bold mb-0">Batch Results</h6>
          <div>
            <button class="btn btn-sm btn-outline-primary me-1" id="exportCSV">Export CSV</button>
            <button class="btn btn-sm btn-outline-secondary" id="exportTXT">Export TXT</button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-sm table-bordered small scrollable-tbody">
            <thead class="table-light">
              <tr>
                <th>Batch</th>
                <th>Status</th>
                <th>Start</th>
                <th>Duration</th>
              </tr>
            </thead>
            <tbody id="batchLogTable"></tbody>
          </table>
        </div>
      </div>
    </div>

    <p id="emailDuration" class="fw-bold mt-3 small"></p>

    <!-- JS -->
     <script>
      var PARENT_URL = "<?php echo PARENT_URL?>";
      var localApi = PARENT_URL+"api/index.php";
            
      var fetchData = function(query, dataURL, requestType) {
          // Return the $.ajax promise
          return $.ajax({
          url: dataURL,
          type: requestType,
          data: query
      });
      }
  </script>
    <script src="assets/js/main.js"></script>
  </div>
</body>
</html>
