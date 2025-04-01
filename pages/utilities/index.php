<?php
/*
Author:     Aldrich Delos Santos
Date:       2023-06-15
*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>RMS Invoice Sending</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <style>
    .switch {
      position: relative;
      display: inline-block;
      width: 50px;
      height: 24px;
    }
    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }
    .slider {
      position: absolute;
      cursor: pointer;
      inset: 0;
      background-color: #ccc;
      transition: 0.4s;
      border-radius: 24px;
    }
    .slider:before {
      position: absolute;
      content: "";
      height: 18px;
      width: 18px;
      left: 3px;
      bottom: 3px;
      background-color: white;
      transition: 0.4s;
      border-radius: 50%;
      box-shadow: 0 1px 3px rgba(0,0,0,0.3);
    }
    input:checked + .slider {
      background-color: #26a69a;
    }
    input:checked + .slider:before {
      transform: translateX(26px);
    }
    .slider.round {
      border-radius: 24px;
    }
  </style>
</head>
<body>
<div class="container mt-5">
  <h1 class="text-center mb-4">RMS Invoice Sending</h1>

  <!-- ✅ Material-style Test Mode Switch -->
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
  </div>

  <div id="loading" class="text-center d-none">
    <div class="spinner-border text-primary" role="status"></div>
    <p>Loading records...</p>
  </div>

  <div id="stats" class="mb-3">
    <p><strong>Total Records:</strong> <span id="totalRecords">0</span></p>
    <p><strong>Invalid Emails:</strong> <span id="invalidEmails">0</span></p>
    <p><strong>Invalid Attachments:</strong> <span id="invalidAttachments">0</span></p>
  </div>

  <div class="progress mb-4 d-none" id="progressContainer">
    <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%;">0%</div>
  </div>

  <p id="emailCount" class="fw-bold d-none"></p>

  <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
    <button id="startProcess" class="btn btn-primary btn-lg d-none">
      <i class="bi bi-send"></i> Send Emails
    </button>
    <button id="cancelProcess" class="btn btn-secondary btn-lg d-none">
      <i class="bi bi-x-circle"></i> Cancel
    </button>
    <button id="refreshRecords" class="btn btn-outline-secondary btn-sm" title="Refresh invoice list">
      <i class="bi bi-arrow-clockwise"></i> Refresh
    </button>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>Invoice No</th>
          <th>Client Code</th>
          <th>Tenant Name</th>
          <th>Email Address</th>
          <th>Attachment</th>
        </tr>
      </thead>
      <tbody id="recordTable"></tbody>
    </table>
  </div>

  <div id="log" class="mt-3"></div>
  <p id="emailDuration" class="fw-bold mt-2"></p>
</div>

<script>
let allRecords = [];
let batchSize = 5;
let currentBatch = 0;
let isCancelled = false;
let startTime;

function formatDuration(ms) {
  const totalSeconds = Math.floor(ms / 1000);
  const hours = Math.floor(totalSeconds / 3600);
  const minutes = Math.floor((totalSeconds % 3600) / 60);
  const seconds = totalSeconds % 60;
  return `${hours}h ${minutes}m ${seconds}s`;
}

function sendNextBatch() {
  if (isCancelled) {
    const cancelTime = new Date();
    const duration = cancelTime - startTime;
    $('#log').append('<p class="text-warning">Process cancelled by the user.</p>');
    $('#emailDuration').text(`Duration before cancel: ${formatDuration(duration)}`);
    resetButtons();
    setTimeout(reloadPendingInvoices, 500);
    return;
  }

  const batch = allRecords.slice(currentBatch, currentBatch + batchSize);
  if (batch.length === 0) {
    const endTime = new Date();
    const duration = endTime - startTime;
    $('#emailDuration').text(`Total Duration: ${formatDuration(duration)}`);
    $('#log').append('<p class="text-success">All emails processed.</p>');
    $('#progressBar').css('width', '100%').text('100%');
    resetButtons();
    return;
  }

  $.ajax({
    url: 'process_emails.php',
    method: 'POST',
    data: {
      batch: JSON.stringify(batch),
      read_only: $('#readOnlyToggle').is(':checked') ? 1 : 0
    },
    dataType: 'json',
    success: function (response) {
      currentBatch += batch.length;
      const percent = Math.min((currentBatch / allRecords.length) * 100, 100).toFixed(2);
      $('#progressBar').css('width', percent + '%').text(`${percent}%`);
      $('#emailCount').removeClass('d-none').text(`${currentBatch} / ${allRecords.length} processed`);

      const logMsg = response.status === 'success'
        ? `<p class="text-success">Batch ${Math.ceil(currentBatch / batchSize)} OK</p>`
        : `<p class="text-danger">Batch ${Math.ceil(currentBatch / batchSize)} had issues</p>`;
      $('#log').append(logMsg);

      setTimeout(sendNextBatch, 300);
    },
    error: function () {
      $('#log').append(`<p class="text-danger">Error sending batch ${Math.ceil(currentBatch / batchSize)}.</p>`);
      resetButtons();
    }
  });
}

function resetButtons() {
  $('#startProcess').prop('disabled', false).text('Send Emails').removeClass('d-none');
  $('#cancelProcess').addClass('d-none').prop('disabled', false).text('Cancel');
}

function reloadPendingInvoices() {
  $('#loading').removeClass('d-none').html(`
    <div class="spinner-border text-warning" role="status"></div>
    <p>Reloading pending invoices...</p>
  `);
  $('#recordTable').empty();
  $('#log').html('');
  $('#emailDuration').text('');
  $('#progressBar').css('width', '0%').text('0%');
  $('#emailCount').text('').addClass('d-none');
  $('#progressContainer').addClass('d-none');

  $.ajax({
    url: 'fetch_records.php',
    method: 'GET',
    dataType: 'json',
    success: function (response) {
      $('#loading').addClass('d-none');
      allRecords = response.records;
      currentBatch = 0;
      isCancelled = false;

      $('#totalRecords').text(response.totalRecords);
      $('#invalidEmails').text(response.invalidEmails);
      $('#invalidAttachments').text(response.invalidAttachments);

      response.records.forEach(record => {
        $('#recordTable').append(`
          <tr>
            <td>${record.invoice_no}</td>
            <td>${record.client_code}</td>
            <td>${record.tenant_name}</td>
            <td>${record.email_address}</td>
            <td>${record.attachment_status}</td>
          </tr>
        `);
      });

      if (allRecords.length > 0) {
        $('#startProcess').removeClass('d-none');
      } else {
        $('#log').append('<p class="text-muted">No pending invoices to send.</p>');
      }

      $('#cancelProcess').addClass('d-none').prop('disabled', false).text('Cancel');
    },
    error: function () {
      $('#loading').html('<p class="text-danger">Failed to reload records.</p>');
    }
  });
}

$(document).ready(function () {
  $('#loading').removeClass('d-none');

  $.ajax({
    url: 'fetch_records.php',
    method: 'GET',
    dataType: 'json',
    success: function (response) {
      $('#loading').addClass('d-none');
      allRecords = response.records;

      $('#totalRecords').text(response.totalRecords);
      $('#invalidEmails').text(response.invalidEmails);
      $('#invalidAttachments').text(response.invalidAttachments);

      response.records.forEach(record => {
        $('#recordTable').append(`
          <tr>
            <td>${record.invoice_no}</td>
            <td>${record.client_code}</td>
            <td>${record.tenant_name}</td>
            <td>${record.email_address}</td>
            <td>${record.attachment_status}</td>
          </tr>
        `);
      });

      if (allRecords.length > 0) {
        $('#startProcess').removeClass('d-none');
      }
    },
    error: function () {
      $('#loading').html('<p class="text-danger">Failed to fetch records.</p>');
    }
  });

  $('#startProcess').on('click', function () {
    $(this).prop('disabled', true).text('Processing...');
    $('#progressContainer').removeClass('d-none');
    $('#cancelProcess').removeClass('d-none');
    $('#log').html('');
    $('#emailCount').text('').addClass('d-none');
    $('#emailDuration').text('');
    currentBatch = 0;
    isCancelled = false;
    startTime = new Date();
    sendNextBatch();
  });

  $('#cancelProcess').on('click', function () {
    isCancelled = true;
    $('#cancelProcess').prop('disabled', true).text('Cancelling...');
  });

  $('#refreshRecords').on('click', function () {
    $('#log').append('<p class="text-info">Manual refresh requested.</p>');
    reloadPendingInvoices();
  });

  $('#readOnlyToggle').on('change', function () {
    const isChecked = $(this).is(':checked');
    $('#modeLabel').text(
      isChecked ? 'Emails will not be sent' : 'LIVE MODE – Emails will be sent'
    ).toggleClass('text-danger', isChecked).toggleClass('text-success', !isChecked);
  });
});
</script>
</body>
</html>
