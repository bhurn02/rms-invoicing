// main.js
let allRecords = [], batchSize = 5, currentBatch = 0, isCancelled = false, startTime;
let isGeneratingInvoices = false; // Add tracking for invoice generation

function formatDuration(ms) {
  const s = Math.floor(ms / 1000);
  return `0h ${Math.floor(s / 60)}m ${s % 60}s`;
}

function sendNextBatch() {
  if (isCancelled) {
    const cancelTime = new Date();
    const formatted = formatDuration(cancelTime - startTime);
    $('#emailDuration').text(`Duration before cancel: ${formatted}`);
    $('#totalDuration').text(formatted);
    $('#batchLogTable').prepend(`<tr class="table-warning fade-in"><td colspan="4" class="text-center fw-bold">Cancelled</td></tr>`);
    resetButtons();
    return;
  }

  const batch = allRecords.slice(currentBatch, currentBatch + batchSize);
  if (batch.length === 0) {
    const end = new Date();
    const duration = formatDuration(end - startTime);
    $('#totalDuration').text(duration);
    $('#emailDuration').text(`Total Duration: ${duration}`);
    $('#batchLogTable').prepend(`
        <tr class="fade-in">
          <td colspan="4" class="text-center fw-bold">
            <i class="bi bi-check-circle-fill text-success"></i> All emails processed
          </td>
        </tr>
      `);
      
    $('#progressBar').css('width', '100%').text('100%');
    resetButtons();
    return;
  }

  const batchStart = new Date();
  const startStr = batchStart.toLocaleTimeString();

  $.ajax({
    url: 'process_emails.php',
    method: 'POST',
    data: {
      batch: JSON.stringify(batch),
      read_only: $('#readOnlyToggle').is(':checked') ? 1 : 0
    },
    dataType: 'json',
    success: function (res) {
      const batchEnd = new Date();
      const dur = formatDuration(batchEnd - batchStart);
      currentBatch += batch.length;

      const percent = Math.min((currentBatch / allRecords.length) * 100, 100).toFixed(2);
      $('#progressBar').css('width', percent + '%').text(`${percent}%`);
      $('#emailCount').removeClass('d-none').text(`${currentBatch} / ${allRecords.length} processed`);

      let errorList = '';
      if (res.errors?.length) {
        errorList = '<ul class="mb-0 small text-danger">' + res.errors.map(e => `<li>${e}</li>`).join('') + '</ul>';
      }

      $('#batchLogTable').prepend(`
        <tr class="fade-in ${res.status === 'success' ? '' : 'error-row'}">
          <td>Batch ${Math.ceil(currentBatch / batchSize)}</td>
          <td>
            ${res.status === 'success' ? '<i class="bi bi-check-circle-fill text-success"></i> OK'
                                       : '<i class="bi bi-x-circle-fill text-danger"></i> Had issues'}
            ${errorList}
          </td>
          <td>${startStr}</td>
          <td>${dur}</td>
        </tr>
      `);
      
      

      setTimeout(sendNextBatch, 300);
    },
    error: function () {
      $('#batchLogTable').prepend(`
        <tr class="table-danger fade-in"><td colspan="4">Error sending batch</td></tr>
      `);
      resetButtons();
    }
  });
}

function resetButtons() {
  $('#startProcess').prop('disabled', false).text('Send Emails').removeClass('d-none');
  $('#cancelProcess').addClass('d-none').prop('disabled', false).text('Cancel');
}

function generateInvoices() {
  if (isGeneratingInvoices) return;
  
  isGeneratingInvoices = true;
  const $btn = $('#GenerateInvoice');
  $btn.prop('disabled', true).html('<i class="bi bi-gear-fill"></i> Generating...');
  
  $('#startProcess').addClass('d-none');
  
  // Show progress UI
  $('#invoiceProgressContainer').removeClass('d-none');
  $('#invoiceProgressBar').css('width', '0%').text('Starting...');
  $('#invoiceCount').removeClass('d-none').text('Preparing invoice generation...');
  $('#invoiceDuration').text('');
  
  const startTime = new Date();
  const startStr = startTime.toLocaleTimeString();

  // Add initial log entry to batch log
  $('#batchLogTable').prepend(`
    <tr class="fade-in">
      <td>Invoice Generation</td>
      <td>
        <i class="bi bi-info-circle-fill text-info"></i> Starting invoice generation
      </td>
      <td>${startStr}</td>
      <td>-</td>
    </tr>
  `);

  // Update progress bar to show activity
  $('#invoiceProgressBar').css('width', '25%').text('Processing...');

  var ajaxGeneratePDF = fetchData($.param({
    "action": 'batchgenerateinvoicepdf'
  }), localApi, "POST");
 
  $.when(ajaxGeneratePDF).then(function(response) {
    const endTime = new Date();
    const duration = formatDuration(endTime - startTime);
    
    // Update progress to complete
    $('#invoiceProgressBar').css('width', '100%').text('Complete');
    $('#invoiceCount').text('Generation complete');
    $('#invoiceDuration').text(`Total Duration: ${duration}`);

    // Log the result in batch log
    let statusClass = 'text-success';
    let statusIcon = 'check-circle-fill';
    let statusText = 'Success';
    
    if (!response || response.error) {
      statusClass = 'text-danger';
      statusIcon = 'x-circle-fill';
      statusText = 'Failed';
    }

    $('#batchLogTable').prepend(`
      <tr class="fade-in ${statusClass === 'text-danger' ? 'table-danger' : ''}">
        <td>Invoice Generation</td>
        <td>
          <i class="bi bi-${statusIcon} ${statusClass}"></i> ${statusText}
          ${response?.error ? `<div class="small text-danger">${response.error}</div>` : ''}
        </td>
        <td>${startStr}</td>
        <td>${duration}</td>
      </tr>
    `);

    // Reset after a short delay
    setTimeout(() => {
      resetInvoiceButtons();
      isGeneratingInvoices = false;
      if (!response?.error) {
        // Show send email button after successful generation
        $('#startProcess').removeClass('d-none');
        reloadPendingInvoicesWithoutLogs();
      }
    }, 1500);

  }).fail(function(jqXHR, textStatus, errorThrown) {
    const endTime = new Date();
    const duration = formatDuration(endTime - startTime);
    
    $('#invoiceProgressBar').css('width', '100%').addClass('bg-danger').text('Failed');
    $('#invoiceCount').text('Generation failed');
    $('#invoiceDuration').text(`Duration: ${duration}`);

    $('#batchLogTable').prepend(`
      <tr class="fade-in table-danger">
        <td>Invoice Generation</td>
        <td>
          <i class="bi bi-x-circle-fill text-danger"></i> Failed
          <div class="small text-danger">${textStatus}: ${errorThrown}</div>
        </td>
        <td>${startStr}</td>
        <td>${duration}</td>
      </tr>
    `);

    setTimeout(() => {
      resetInvoiceButtons();
      isGeneratingInvoices = false;
    }, 1500);
  });
}

function resetInvoiceButtons() {
  $('#GenerateInvoice').prop('disabled', false).html('<i class="bi bi-gear"></i> Generate Invoice');
  $('#invoiceProgressContainer').addClass('d-none');
  $('#invoiceProgressBar').css('width', '0%').text('0%').removeClass('bg-danger');
  $('#invoiceCount').addClass('d-none').text('');
  $('#invoiceDuration').text('');
}

function reloadPendingInvoices() {
  // Capture start time
  const loadStartTime = new Date();
  const startStr = loadStartTime.toLocaleTimeString();

  // Add initial loading log
  $('#batchLogTable').prepend(`
    <tr class="fade-in" id="initialLoadLogRow">
      <td>Initial Load</td>
      <td>
        <i class="bi bi-arrow-repeat text-info"></i> Fetching records...
      </td>
      <td>${startStr}</td>
      <td>-</td>
    </tr>
  `);

  // Show loading state in the table
  $('#recordTable').html(`
    <tr>
      <td colspan="5" class="text-center py-3">
        <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        Loading records...
      </td>
    </tr>
  `);

  // Show progress bar as loading
  $('#progressContainer').removeClass('d-none');
  $('#progressBar').addClass('progress-bar-striped progress-bar-animated').css('width', '30%').text('Loading...');

  $('#recordTable').empty();
  $('#emailCount').addClass('d-none').text('');
  $('#emailDuration').text('');
  $('#startProcess').addClass('d-none'); // Hide send email button initially

  $.ajax({
    url: 'fetch_records.php',
    method: 'GET',
    dataType: 'json',
    success: function (res) {
      const loadEndTime = new Date();
      const duration = formatDuration(loadEndTime - loadStartTime);

      allRecords = res.records;
      currentBatch = 0;
      isCancelled = false;

      $('#totalRecords').text(res.totalRecords);
      $('#invalidEmails').text(res.invalidEmails);
      $('#invalidAttachments').text(res.invalidAttachments);

      // Update the loading log with success status and duration
      $('#initialLoadLogRow').replaceWith(`
        <tr class="fade-in">
          <td>Initial Load</td>
          <td>
            <i class="bi bi-check-circle-fill text-success"></i> Loaded ${res.totalRecords} records
            ${res.invalidEmails > 0 ? `<div class="small text-warning">${res.invalidEmails} invalid emails</div>` : ''}
            ${res.invalidAttachments > 0 ? `<div class="small text-warning">${res.invalidAttachments} invalid attachments</div>` : ''}
          </td>
          <td>${startStr}</td>
          <td>${duration}</td>
        </tr>
      `);

      // Hide progress bar
      $('#progressBar').css('width', '100%').text('');
      // $('#progressBar').removeClass('progress-bar-striped progress-bar-animated').css('width', '100%').text('');
      $('#progressContainer').addClass('d-none');

      // Only show generate invoice and send email button if there are records
      let hideSend = false;
      if (res.totalRecords > 0) {
        if ((res.invalidEmails / res.totalRecords) > 0.1 || (res.invalidAttachments / res.totalRecords) > 0.1) {
          hideSend = true;
        }
      }
      if (allRecords.length > 0) {
        $('#GenerateInvoice').removeClass('d-none');
        if (!hideSend) {
          $('#startProcess').removeClass('d-none');
        } else {
          $('#startProcess').addClass('d-none');
        }
      }

      res.records.forEach(r => {
        $('#recordTable').append(`
          <tr>
            <td>${r.invoice_no}</td>
            <td>${r.client_code}</td>
            <td>${r.tenant_name}</td>
            <td>${r.email_address}</td>
            <td>${r.attachment_status}</td>
          </tr>
        `);
      });
    },
    error: function () {
      const loadEndTime = new Date();
      const duration = formatDuration(loadEndTime - loadStartTime);
      // Update the loading log with error status and duration
      $('#initialLoadLogRow').replaceWith(`
        <tr class="fade-in table-danger">
          <td>Initial Load</td>
          <td>
            <i class="bi bi-x-circle-fill text-danger"></i> Failed to load records
          </td>
          <td>${startStr}</td>
          <td>${duration}</td>
        </tr>
      `);
      // Hide progress bar
      $('#progressBar').css('width', '100%').text('');
      // $('#progressBar').removeClass('progress-bar-striped progress-bar-animated').css('width', '100%').text('');
      $('#progressContainer').addClass('d-none');
      alert('Failed to load records');
    }
  });
}

function reloadPendingInvoicesWithoutLogs() {
  // Capture start time
  const loadStartTime = new Date();
  const startStr = loadStartTime.toLocaleTimeString();

  // Add initial loading log
  $('#batchLogTable').prepend(`
    <tr class="fade-in" id="reloadLogRow">
      <td>Reload</td>
      <td>
        <i class="bi bi-arrow-repeat text-info"></i> Fetching records...
      </td>
      <td>${startStr}</td>
      <td>-</td>
    </tr>
  `);

  // Show progress bar as loading
  $('#progressContainer').removeClass('d-none');
  $('#progressBar').addClass('progress-bar-striped progress-bar-animated').css('width', '30%').text('Loading...');

  $('#recordTable').empty();
  $('#emailCount').addClass('d-none').text('');
  $('#emailDuration').text('');
  $('#startProcess').addClass('d-none'); // Hide send email button initially

  $.ajax({
    url: 'fetch_records.php',
    method: 'GET',
    dataType: 'json',
    success: function (res) {
      const loadEndTime = new Date();
      const duration = formatDuration(loadEndTime - loadStartTime);

      allRecords = res.records;
      currentBatch = 0;
      isCancelled = false;

      $('#totalRecords').text(res.totalRecords);
      $('#invalidEmails').text(res.invalidEmails);
      $('#invalidAttachments').text(res.invalidAttachments);

      // Update the loading log with success status and duration
      $('#reloadLogRow').replaceWith(`
        <tr class="fade-in">
          <td>Reload</td>
          <td>
            <i class="bi bi-check-circle-fill text-success"></i> Loaded ${res.totalRecords} records
            ${res.invalidEmails > 0 ? `<div class="small text-warning">${res.invalidEmails} invalid emails</div>` : ''}
            ${res.invalidAttachments > 0 ? `<div class="small text-warning">${res.invalidAttachments} invalid attachments</div>` : ''}
          </td>
          <td>${startStr}</td>
          <td>${duration}</td>
        </tr>
      `);

      // Hide progress bar
      $('#progressBar').css('width', '100%').text('');
      // $('#progressBar').removeClass('progress-bar-striped progress-bar-animated').css('width', '100%').text('');
      $('#progressContainer').addClass('d-none');

      // Only show generate invoice and send email button if there are records
      let hideSend = false;
      if (res.totalRecords > 0) {
        if ((res.invalidEmails / res.totalRecords) > 0.1 || (res.invalidAttachments / res.totalRecords) > 0.1) {
          hideSend = true;
        }
      }
      if (allRecords.length > 0) {
        $('#GenerateInvoice').removeClass('d-none');
        if (!hideSend) {
          $('#startProcess').removeClass('d-none');
        } else {
          $('#startProcess').addClass('d-none');
        }
      }

      res.records.forEach(r => {
        $('#recordTable').append(`
          <tr>
            <td>${r.invoice_no}</td>
            <td>${r.client_code}</td>
            <td>${r.tenant_name}</td>
            <td>${r.email_address}</td>
            <td>${r.attachment_status}</td>
          </tr>
        `);
      });
    },
    error: function () {
      const loadEndTime = new Date();
      const duration = formatDuration(loadEndTime - loadStartTime);
      // Update the loading log with error status and duration
      $('#reloadLogRow').replaceWith(`
        <tr class="fade-in table-danger">
          <td>Reload</td>
          <td>
            <i class="bi bi-x-circle-fill text-danger"></i> Failed to load records
          </td>
          <td>${startStr}</td>
          <td>${duration}</td>
        </tr>
      `);
      // Hide progress bar
      $('#progressBar').css('width', '100%').text('');
      // $('#progressBar').removeClass('progress-bar-striped progress-bar-animated').css('width', '100%').text('');
      $('#progressContainer').addClass('d-none');
      alert('Failed to load records');
    }
  });
}

function exportTable(format) {
  let rows = [];
  $('#batchLogTable tr').each(function () {
    const cols = $(this).find('td').map(function () {
      return $(this).text().trim().replace(/\s+/g, ' ');
    }).get();
    rows.push(cols.join(format === 'csv' ? ',' : '\t'));
  });

  const blob = new Blob([rows.join('\n')], { type: 'text/plain' });
  const url = URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.href = url;
  link.download = `batch_results.${format}`;
  link.click();
  URL.revokeObjectURL(url);
}

// Init
$(function () {
  $('body').toggleClass('dark-mode'); // ✅ Default to dark mode
  reloadPendingInvoices();

  $('#startProcess').on('click', function () {
    $(this).prop('disabled', true).text('Processing...');
    $('#GenerateInvoice').prop('disabled', true);
    $('#refreshRecords').prop('disabled', true);
    $('#progressContainer').removeClass('d-none');
    $('#cancelProcess').removeClass('d-none');
    $('#batchLogTable').empty();
    $('#emailCount').text('').addClass('d-none');
    $('#emailDuration').text('');
    startTime = new Date();
    sendNextBatch();
  });

  $('#GenerateInvoice').on('click', generateInvoices);

  $('#cancelProcess').on('click', function () {
    isCancelled = true;
    $(this).prop('disabled', true).text('Cancelling...');
  });

  $('#refreshRecords').on('click', reloadPendingInvoices);

  $('#readOnlyToggle').on('change', function () {
    const isTest = $(this).is(':checked');
    $('#modeLabel')
      .text(isTest ? 'Emails will not be sent' : 'LIVE MODE – Emails will be sent')
      .toggleClass('text-danger', isTest)
      .toggleClass('text-success', !isTest);
  });

  $('#tableSearch').on('keyup', function () {
    const keyword = $(this).val().toLowerCase();
    $('#recordTable tr').filter(function () {
      $(this).toggle($(this).text().toLowerCase().includes(keyword));
    });
  });

  $('#darkToggle').on('click', function () {
    $('body').toggleClass('dark-mode');
  });

  $('#exportCSV').on('click', () => exportTable('csv'));
  $('#exportTXT').on('click', () => exportTable('txt'));
});
