<?php
require_once("system/functions.php");
require_once("system/mail_smtp.php");

// Set timezone
date_default_timezone_set("Pacific/Guam");

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $action = $_POST['action'];
    $response = ['status' => 'error', 'message' => 'Unknown action'];
    
    if ($action === 'test_email') {
        $to = trim($_POST['to'] ?? '');
        $subject = trim($_POST['subject'] ?? 'SMTP Test Email');
        $message = trim($_POST['message'] ?? 'This is a test email to verify SMTP configuration.');
        $cc = trim($_POST['cc'] ?? '');
        $bcc = trim($_POST['bcc'] ?? '');
        $test_attachment = isset($_POST['test_attachment']) ? (bool)$_POST['test_attachment'] : false;
        
        if (empty($to)) {
            $response = ['status' => 'error', 'message' => 'Recipient email is required'];
        } else {
            // Validate email addresses
            $to_emails = extract_valid_emails($to);
            if (empty($to_emails)) {
                $response = ['status' => 'error', 'message' => 'Invalid recipient email address'];
            } else {
                // Prepare CC and BCC
                $cc_emails = !empty($cc) ? extract_valid_emails($cc) : [];
                $bcc_emails = !empty($bcc) ? extract_valid_emails($bcc) : [];
                
                // Add system BCC addresses from config
                if (defined('APP_MAIL_BCC') && is_array(APP_MAIL_BCC)) {
                    $bcc_emails = array_merge($bcc_emails, APP_MAIL_BCC);
                }
                
                // Create test attachment if requested
                $attachments = [];
                if ($test_attachment) {
                    $test_file = __DIR__ . '/test_attachment.txt';
                    $test_content = "This is a test attachment file.\nCreated on: " . date('Y-m-d H:i:s') . "\nSMTP Test Email";
                    file_put_contents($test_file, $test_content);
                    
                    $attachments = [[
                        'path' => $test_file,
                        'name' => 'test_attachment.txt',
                        'encoding' => 'base64',
                        'type' => 'text/plain'
                    ]];
                }
                
                // Send email
                $sender_email = APP_MAIL_SENDER ?? 'rms_noreply@tanholdings.com';
                $sender_name = APP_NAME ?? 'RMS System';
                
                $emailSent = sendSMTPMail(
                    $sender_email, 
                    $sender_name, 
                    $to_emails, 
                    'Test Recipient', 
                    $cc_emails, 
                    $bcc_emails, 
                    $subject, 
                    $message, 
                    $attachments
                );
                
                // Clean up test file
                if ($test_attachment && file_exists($test_file)) {
                    unlink($test_file);
                }
                
                if ($emailSent) {
                    $response = [
                        'status' => 'success', 
                        'message' => 'Test email sent successfully!',
                        'details' => [
                            'to' => implode(', ', $to_emails),
                            'cc' => !empty($cc_emails) ? implode(', ', $cc_emails) : 'None',
                            'bcc' => !empty($bcc_emails) ? implode(', ', $bcc_emails) : 'None',
                            'subject' => $subject,
                            'attachment' => $test_attachment ? 'Yes (test_attachment.txt)' : 'No'
                        ]
                    ];
                } else {
                    $response = ['status' => 'error', 'message' => 'Failed to send test email. Check logs for details.'];
                }
            }
        }
    }
    
    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMTP Mail Test - RMS System</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .config-info {
            background: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
            border-left: 4px solid #007bff;
        }
        .config-info h3 {
            margin-top: 0;
            color: #495057;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #555;
        }
        input[type="text"], input[type="email"], textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        textarea {
            height: 100px;
            resize: vertical;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn {
            background: #007bff;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 4px;
            display: none;
        }
        .result.success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .result.error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .result-details {
            margin-top: 10px;
            font-size: 14px;
        }
        .result-details strong {
            display: inline-block;
            width: 100px;
        }
        .loading {
            display: none;
            text-align: center;
            color: #007bff;
        }
        .log-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 10px;
            border-radius: 4px;
            margin-top: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>SMTP Mail Test</h1>
        
        <div class="config-info">
            <h3>Current SMTP Configuration</h3>
            <p><strong>Host:</strong> smtp.office365.com</p>
            <p><strong>Port:</strong> 587</p>
            <p><strong>Security:</strong> TLS</p>
            <p><strong>Username:</strong> rms_noreply@tanholdings.com</p>
            <p><strong>Password:</strong> [Hidden for security]</p>
        </div>

        <form id="testForm">
            <div class="form-group">
                <label for="to">Recipient Email *</label>
                <input type="email" id="to" name="to" required 
                       placeholder="test@example.com or multiple emails separated by comma">
            </div>

            <div class="form-group">
                <label for="cc">CC (Optional)</label>
                <input type="text" id="cc" name="cc" 
                       placeholder="cc@example.com or multiple emails separated by comma">
            </div>

            <div class="form-group">
                <label for="bcc">BCC (Optional)</label>
                <input type="text" id="bcc" name="bcc" 
                       placeholder="bcc@example.com or multiple emails separated by comma">
            </div>

            <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" id="subject" name="subject" 
                       value="SMTP Test Email - <?php echo date('Y-m-d H:i:s'); ?>">
            </div>

            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" placeholder="Enter your test message here...">This is a test email to verify SMTP configuration.

Test Details:
- Sent from: RMS System
- Timestamp: <?php echo date('Y-m-d H:i:s'); ?>
- SMTP Server: smtp.office365.com
- Authentication: rms_noreply@tanholdings.com

If you receive this email, the SMTP configuration is working correctly.</textarea>
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" id="test_attachment" name="test_attachment" value="1">
                    <label for="test_attachment">Include test attachment</label>
                </div>
            </div>

            <button type="submit" class="btn" id="sendBtn">Send Test Email</button>
        </form>

        <div class="loading" id="loading">
            <p>Sending test email...</p>
        </div>

        <div class="result" id="result">
            <div id="resultMessage"></div>
            <div class="result-details" id="resultDetails"></div>
        </div>

        <div class="log-info">
            <strong>Note:</strong> Email sending logs are saved to the logs directory. 
            Check <code>smtp-rms.log</code> for detailed SMTP communication logs and 
            <code>php-mailer-logs-rms-*.log</code> for error logs if sending fails.
        </div>
    </div>

    <script>
        document.getElementById('testForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'test_email');
            
            const sendBtn = document.getElementById('sendBtn');
            const loading = document.getElementById('loading');
            const result = document.getElementById('result');
            const resultMessage = document.getElementById('resultMessage');
            const resultDetails = document.getElementById('resultDetails');
            
            // Show loading state
            sendBtn.disabled = true;
            loading.style.display = 'block';
            result.style.display = 'none';
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                loading.style.display = 'none';
                sendBtn.disabled = false;
                
                result.className = 'result ' + data.status;
                resultMessage.textContent = data.message;
                
                if (data.details) {
                    let detailsHtml = '<h4>Email Details:</h4>';
                    for (const [key, value] of Object.entries(data.details)) {
                        detailsHtml += `<p><strong>${key.charAt(0).toUpperCase() + key.slice(1)}:</strong> ${value}</p>`;
                    }
                    resultDetails.innerHTML = detailsHtml;
                } else {
                    resultDetails.innerHTML = '';
                }
                
                result.style.display = 'block';
            })
            .catch(error => {
                loading.style.display = 'none';
                sendBtn.disabled = false;
                
                result.className = 'result error';
                resultMessage.textContent = 'An error occurred: ' + error.message;
                resultDetails.innerHTML = '';
                result.style.display = 'block';
            });
        });
    </script>
</body>
</html>
