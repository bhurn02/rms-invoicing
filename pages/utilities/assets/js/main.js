// main.js
let allRecords = [], batchSize = 5, currentBatch = 0, isCancelled = false, startTime;

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

function reloadPendingInvoices() {
  $('#recordTable').empty();
  $('#batchLogTable').empty();
  $('#emailCount').addClass('d-none').text('');
  $('#emailDuration').text('');
  $('#progressBar').css('width', '0%').text('0%');
  $('#progressContainer').addClass('d-none');

  $.ajax({
    url: 'fetch_records.php',
    method: 'GET',
    dataType: 'json',
    success: function (res) {
      allRecords = res.records;
      currentBatch = 0;
      isCancelled = false;

      $('#totalRecords').text(res.totalRecords);
      $('#invalidEmails').text(res.invalidEmails);
      $('#invalidAttachments').text(res.invalidAttachments);

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

      if (allRecords.length > 0) {
        $('#startProcess').removeClass('d-none');
      }
    },
    error: function () {
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
    $('#progressContainer').removeClass('d-none');
    $('#cancelProcess').removeClass('d-none');
    $('#batchLogTable').empty();
    $('#emailCount').text('').addClass('d-none');
    $('#emailDuration').text('');
    startTime = new Date();
    sendNextBatch();
  });

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
