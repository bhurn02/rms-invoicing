<?php
/*
Author:     Aldrich Delos Santos
Email:      bhurndls@yahoo.com
Date:       2023-06-15

This sample will send an email notification to the approvers sharing the same employee listings.
*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RMS Invoice Sending</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        table {
            table-layout: auto;
            white-space: nowrap;
        }
        th, td {
            text-align: left;
            padding: 8px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">RMS Invoice Sending</h1>

        <!-- Loading Indicator -->
        <div id="loading" class="text-center d-none">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p>Loading records, please wait...</p>
        </div>

        <!-- Stats -->
        <div id="stats" class="mb-4">
            <p><strong>Total Records:</strong> <span id="totalRecords">0</span></p>
            <p><strong>Invalid Email Addresses:</strong> <span id="invalidEmails">0</span></p>
            <p><strong>Invalid Attachments:</strong> <span id="invalidAttachments">0</span></p>
            <p><strong>Fetch Duration:</strong> <span id="fetchDuration">Calculating...</span></p>
        </div>

        <!-- Progress Bar -->
        <div class="progress mb-4 d-none" id="progressContainer">
            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;">0%</div>
        </div>

        <!-- Email Count -->
        <p id="emailCount" class="fw-bold d-none"></p>

        <!-- Buttons -->
        <div class="row mt-4 mb-4">
            <div class="col text-center">
                <button id="startProcess" class="btn btn-primary btn-lg px-4 py-2 d-none">
                    <i class="bi bi-send"></i> Send Emails
                </button>
                <button id="cancelProcess" class="btn btn-secondary btn-lg px-4 py-2 ms-3 d-none">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Invoice No</th>
                        <th>Client Code</th>
                        <th>Tenant Name</th>
                        <th>Email Address</th>
                        <th>Attachment</th>
                    </tr>
                </thead>
                <tbody id="recordTable">
                    <!-- Records will be dynamically added here -->
                </tbody>
            </table>
        </div>

        <!-- Logs -->
        <div id="log" class="mt-4"></div>

        <!-- Email Sending Duration -->
        <p id="emailDuration" class="fw-bold"></p>
    </div>

    <script>
        $(document).ready(function () {
            let emailProcessInterval;
            let isCancelled = false; // Track cancellation
            let startTime;
            let activeAjaxCalls = []; // Store active AJAX requests

            // Fetch records on page load
            const fetchStartTime = new Date();
            $('#loading').removeClass('d-none');
            $('#startProcess').addClass('d-none');

            $.ajax({
                url: 'fetch_records.php',
                method: 'GET',
                dataType: 'json',
                success: function (response) {
                    $('#totalRecords').text(response.totalRecords);
                    $('#invalidEmails').text(response.invalidEmails);
                    $('#invalidAttachments').text(response.invalidAttachments);

                    // Populate table
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

                    // Calculate and display fetch duration
                    const fetchEndTime = new Date();
                    const fetchDuration = Math.abs(fetchEndTime - fetchStartTime);
                    const fetchSeconds = Math.floor((fetchDuration / 1000) % 60);
                    const fetchMinutes = Math.floor((fetchDuration / (1000 * 60)) % 60);
                    const fetchHours = Math.floor((fetchDuration / (1000 * 60 * 60)) % 24);

                    $('#fetchDuration').text(`${fetchHours}h ${fetchMinutes}m ${fetchSeconds}s`);

                    // Show Send Emails button and hide loading
                    $('#loading').addClass('d-none');
                    $('#startProcess').removeClass('d-none');
                },
                error: function () {
                    $('#loading').html('<p class="text-danger">Failed to load records. Please try again.</p>');
                }
            });

            // Start email process
            $('#startProcess').on('click', function () {
                $(this).prop('disabled', true).text('Processing...');
                $('#loading').removeClass('d-none'); // Show loading indicator
                $('#progressContainer').removeClass('d-none');
                $('#cancelProcess').removeClass('d-none');
                $('#log').html('');
                startTime = new Date();
                isCancelled = false;

                // Start processing emails
                const processRequest = $.ajax({
                    url: 'process_emails.php',
                    method: 'POST',
                    dataType: 'json',
                    xhrFields: {
                        onprogress: function (event) {
                            const responseText = event.currentTarget.responseText;
                            const responses = responseText.trim().split('\n');

                            responses.forEach(res => {
                                try {
                                    const response = JSON.parse(res);
                                    if (response.status === "progress") {
                                        $('#emailCount').text(`${response.sent} / ${response.total} emails sent`).removeClass('d-none');
                                    } else if (response.status === "success") {
                                        $('#log').append(`<p class="text-success">All emails sent successfully!</p>`);
                                        $('#emailCount').text(`Total: ${response.total}, Sent: ${response.sent}`);
                                        resetButtons();
                                    }
                                } catch (e) {
                                    console.error("Invalid JSON Response:", res);
                                }
                            });
                        }
                    },
                    success: function (response) {
                        $('#loading').addClass('d-none');
                        if (response.status === "success") {
                            const endTime = new Date();
                            const duration = Math.abs(endTime - startTime);
                            const seconds = Math.floor((duration / 1000) % 60);
                            const minutes = Math.floor((duration / (1000 * 60)) % 60);
                            const hours = Math.floor((duration / (1000 * 60 * 60)) % 24);

                            $('#emailDuration').text(`Emails sent in ${hours}h ${minutes}m ${seconds}s`);
                            $('#log').append(`<p class="text-success">All emails sent successfully!</p>`);
                            alert('All emails sent successfully!');
                            resetButtons();
                        }
                    },
                    error: function () {
                        $('#log').append('<p class="text-danger">Error: Unable to start the process.</p>');
                        resetButtons();
                        $('#loading').addClass('d-none');
                    }
                });

                // Track the AJAX request
                activeAjaxCalls.push(processRequest);
            });

            // Poll progress independently
            function pollProgress() {
                clearInterval(emailProcessInterval);

                emailProcessInterval = setInterval(function () {
                    if (isCancelled) {
                        clearInterval(emailProcessInterval);
                        $('#log').append('<p class="text-danger">Process cancelled by the user.</p>');
                        resetButtons();
                        return;
                    }

                    const pollRequest = $.ajax({
                        url: 'check_progress.php',
                        method: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            const percentage = Math.min((data.processed / data.total) * 100, 100).toFixed(2);

                            // Update progress bar and logs
                            $('#progressBar').css('width', percentage + '%').text(`${percentage}%`);
                            $('#log').append(`<p>${data.message}</p>`);
                            $('#emailCount').text(`${data.processed} / ${data.total} emails sent`);

                            // Stop polling if the process is complete
                            if (data.processed >= data.total) {
                                clearInterval(emailProcessInterval);

                                const endTime = new Date();
                                const duration = Math.abs(endTime - startTime);
                                const seconds = Math.floor((duration / 1000) % 60);
                                const minutes = Math.floor((duration / (1000 * 60)) % 60);
                                const hours = Math.floor((duration / (1000 * 60 * 60)) % 24);

                                $('#emailDuration').text(`Emails sent in ${hours}h ${minutes}m ${seconds}s`);
                                alert('All emails sent successfully!');
                                resetButtons();
                            }
                        },
                        error: function () {
                            clearInterval(emailProcessInterval);
                            $('#log').append('<p class="text-danger">Error: Unable to fetch progress.</p>');
                            resetButtons();
                        }
                    });

                    // Track the AJAX request
                    activeAjaxCalls.push(pollRequest);
                }, 2000); // Poll every 2 seconds
            }

            // Cancel process
            $('#cancelProcess').on('click', function () {
                isCancelled = true;

                // Abort all active AJAX calls
                activeAjaxCalls.forEach(request => {
                    if (request && request.readyState !== 4) {
                        request.abort();
                    }
                });

                activeAjaxCalls = []; // Clear the array

                // Update UI
                $('#progressBar').css('width', '0%').text('Cancelled');
                $('#emailCount').text('Process cancelled').addClass('text-danger');
                $('#emailDuration').text('');
                $('#log').append('<p class="text-danger">Process cancelled by the user.</p>');
                resetButtons();
            });

            function resetButtons() {
                $('#startProcess').prop('disabled', false).text('Send Emails');
                $('#cancelProcess').addClass('d-none');
            }
        });
    </script>


</body>
</html>
