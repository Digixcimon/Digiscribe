<?php
/**
 * Email Debug Tool
 * 
 * This script helps diagnose email sending issues with your contact form.
 * It performs various tests and provides detailed information about what might be wrong.
 */

// Include configuration
require_once 'config.php';

// Set content type
header('Content-Type: text/html; charset=UTF-8');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Debug Tool</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 0 auto; padding: 20px; background: #f5f5f5; }
        .debug-section { margin: 20px 0; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { border-left: 5px solid #28a745; background-color: #d4edda; }
        .warning { border-left: 5px solid #ffc107; background-color: #fff3cd; }
        .error { border-left: 5px solid #dc3545; background-color: #f8d7da; }
        .info { border-left: 5px solid #17a2b8; background-color: #d1ecf1; }
        h1 { color: #333; text-align: center; }
        h2 { color: #555; margin-top: 0; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        .config-value { font-weight: bold; color: #007bff; }
        .test-result { margin: 10px 0; padding: 10px; border-radius: 4px; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin: 5px; }
        .btn:hover { background: #0056b3; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto; }
        .log-entry { margin: 5px 0; padding: 8px; background: #f8f9fa; border-radius: 3px; font-family: monospace; font-size: 12px; }
    </style>
</head>
<body>
    <h1>🔧 Email Debug Tool</h1>
    <p style="text-align: center; color: #666;">This tool helps diagnose why your contact form emails aren't being sent.</p>

    <?php
    // Function to test mail configuration
    function testMailConfiguration() {
        $results = [];
        
        // Check if mail function exists
        $results['mail_function'] = function_exists('mail');
        
        // Check sendmail path
        $sendmail_path = ini_get('sendmail_path');
        $results['sendmail_path'] = $sendmail_path;
        
        // Check SMTP settings
        $smtp_host = ini_get('SMTP');
        $smtp_port = ini_get('smtp_port');
        $results['smtp_host'] = $smtp_host;
        $results['smtp_port'] = $smtp_port;
        
        return $results;
    }

    // Function to send test email
    function sendTestEmail($to, $subject, $message, $headers) {
        $start_time = microtime(true);
        $result = mail($to, $subject, $message, $headers);
        $execution_time = microtime(true) - $start_time;
        
        return [
            'success' => $result,
            'execution_time' => $execution_time,
            'error' => $result ? null : error_get_last()
        ];
    }

    // Test 1: Configuration Check
    echo '<div class="debug-section info">';
    echo '<h2>📋 Configuration Check</h2>';
    echo '<div class="test-result">';
    echo '<strong>Current Configuration:</strong><br>';
    echo 'Recipient Email: <span class="config-value">' . RECIPIENT_EMAIL . '</span><br>';
    echo 'Sender Email: <span class="config-value">' . SENDER_EMAIL . '</span><br>';
    echo 'Website Name: <span class="config-value">' . WEBSITE_NAME . '</span><br>';
    echo 'Debug Mode: <span class="config-value">' . (DEBUG_MODE ? 'ON' : 'OFF') . '</span><br>';
    echo 'Logging: <span class="config-value">' . (ENABLE_LOGGING ? 'ON' : 'OFF') . '</span><br>';
    
    // Validate email addresses
    if (!filter_var(RECIPIENT_EMAIL, FILTER_VALIDATE_EMAIL)) {
        echo '<div class="test-result error">❌ Invalid recipient email address!</div>';
    } else {
        echo '<div class="test-result success">✅ Recipient email format is valid</div>';
    }
    
    if (!filter_var(SENDER_EMAIL, FILTER_VALIDATE_EMAIL)) {
        echo '<div class="test-result error">❌ Invalid sender email address!</div>';
    } else {
        echo '<div class="test-result success">✅ Sender email format is valid</div>';
    }
    echo '</div>';
    echo '</div>';

    // Test 2: PHP Mail Configuration
    echo '<div class="debug-section">';
    echo '<h2>🐘 PHP Mail Configuration</h2>';
    $mail_config = testMailConfiguration();
    
    if ($mail_config['mail_function']) {
        echo '<div class="test-result success">✅ PHP mail() function is available</div>';
    } else {
        echo '<div class="test-result error">❌ PHP mail() function is NOT available</div>';
    }
    
    echo '<div class="test-result info">';
    echo '<strong>Mail Settings:</strong><br>';
    echo 'Sendmail Path: <code>' . ($mail_config['sendmail_path'] ?: 'Not set') . '</code><br>';
    echo 'SMTP Host: <code>' . ($mail_config['smtp_host'] ?: 'Not set') . '</code><br>';
    echo 'SMTP Port: <code>' . ($mail_config['smtp_port'] ?: 'Not set') . '</code><br>';
    echo '</div>';
    echo '</div>';

    // Test 3: Server Environment
    echo '<div class="debug-section">';
    echo '<h2>🖥️ Server Environment</h2>';
    echo '<div class="test-result info">';
    echo '<strong>Server Information:</strong><br>';
    echo 'PHP Version: <code>' . PHP_VERSION . '</code><br>';
    echo 'Operating System: <code>' . php_uname() . '</code><br>';
    echo 'Server Software: <code>' . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . '</code><br>';
    echo 'User Agent: <code>' . ($_SERVER['HTTP_USER_AGENT'] ?? 'Command Line') . '</code><br>';
    echo 'Server Name: <code>' . ($_SERVER['SERVER_NAME'] ?? 'localhost') . '</code><br>';
    echo '</div>';
    echo '</div>';

    // Test 4: Test Email Sending
    echo '<div class="debug-section">';
    echo '<h2>📧 Email Sending Test</h2>';
    
    if (isset($_GET['test']) && $_GET['test'] === 'simple') {
        echo '<h3>Simple Test Email</h3>';
        $simple_result = sendTestEmail(
            RECIPIENT_EMAIL,
            'Test Email from Debug Tool',
            'This is a simple test email. If you receive this, basic email functionality is working.',
            'From: ' . SENDER_EMAIL
        );
        
        if ($simple_result['success']) {
            echo '<div class="test-result success">✅ Simple email sent successfully!</div>';
            echo '<div class="test-result info">Execution time: ' . number_format($simple_result['execution_time'], 3) . ' seconds</div>';
        } else {
            echo '<div class="test-result error">❌ Simple email failed to send</div>';
            if ($simple_result['error']) {
                echo '<div class="test-result error">Error: ' . htmlspecialchars(print_r($simple_result['error'], true)) . '</div>';
            }
        }
    } elseif (isset($_GET['test']) && $_GET['test'] === 'full') {
        echo '<h3>Full Contact Form Test</h3>';
        
        // Simulate a contact form submission
        $test_name = 'Debug Test User';
        $test_email = 'test@example.com';
        $test_subject = 'Debug Test Submission';
        $test_message = 'This is a test message from the debug tool to simulate a real contact form submission.';
        
        // Create the same email that would be sent by the contact form
        $emailSubject = 'Contact Form: ' . $test_subject;
        $emailMessage = "
        <html>
        <head>
            <title>Contact Form Submission</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: " . EMAIL_TEMPLATE_HEADER_COLOR . "; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .field { margin-bottom: 15px; }
                .label { font-weight: bold; color: #555; }
                .value { margin-top: 5px; padding: 10px; background: white; border-left: 4px solid " . EMAIL_TEMPLATE_ACCENT_COLOR . "; }
                .footer { text-align: center; padding: 20px; color: #777; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>New Contact Form Submission (DEBUG TEST)</h2>
                </div>
                <div class='content'>
                    <div class='field'>
                        <div class='label'>Name:</div>
                        <div class='value'>" . htmlspecialchars($test_name) . "</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Email:</div>
                        <div class='value'>" . htmlspecialchars($test_email) . "</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Subject:</div>
                        <div class='value'>" . htmlspecialchars($test_subject) . "</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Message:</div>
                        <div class='value'>" . nl2br(htmlspecialchars($test_message)) . "</div>
                    </div>
                </div>
                <div class='footer'>
                    <p>This email was sent from the contact form on " . WEBSITE_NAME . " (DEBUG TEST).</p>
                    <p>Website: " . WEBSITE_URL . "</p>
                    <p>Sent on: " . date('Y-m-d H:i:s') . "</p>
                </div>
            </div>
        </body>
        </html>";

        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . SENDER_NAME . ' <' . SENDER_EMAIL . '>',
            'Reply-To: ' . $test_email,
            'X-Mailer: PHP/' . phpversion(),
            'X-Priority: 3',
            'Date: ' . date('r'),
        ];
        
        $headerString = implode("\r\n", $headers);
        
        $full_result = sendTestEmail(RECIPIENT_EMAIL, $emailSubject, $emailMessage, $headerString);
        
        if ($full_result['success']) {
            echo '<div class="test-result success">✅ Full contact form email sent successfully!</div>';
            echo '<div class="test-result info">Execution time: ' . number_format($full_result['execution_time'], 3) . ' seconds</div>';
        } else {
            echo '<div class="test-result error">❌ Full contact form email failed to send</div>';
            if ($full_result['error']) {
                echo '<div class="test-result error">Error: ' . htmlspecialchars(print_r($full_result['error'], true)) . '</div>';
            }
        }
    } else {
        echo '<p>Click the buttons below to test email sending:</p>';
        echo '<a href="?test=simple" class="btn">Send Simple Test Email</a>';
        echo '<a href="?test=full" class="btn">Send Full Contact Form Test</a>';
    }
    echo '</div>';

    // Test 5: Check for common issues
    echo '<div class="debug-section">';
    echo '<h2>🔍 Common Issues Check</h2>';
    
    $issues = [];
    
    // Check for localhost/development environment
    $server_name = $_SERVER['SERVER_NAME'] ?? 'localhost';
    if (in_array($server_name, ['localhost', '127.0.0.1', '::1'])) {
        $issues[] = 'You are running on localhost. Many hosting providers and email services block emails from localhost.';
    }
    
    // Check for missing MTA
    if (!$mail_config['sendmail_path'] && !$mail_config['smtp_host']) {
        $issues[] = 'No sendmail path or SMTP configuration found. Your server may not have a mail transfer agent configured.';
    }
    
    // Check email domain
    $recipient_domain = substr(strrchr(RECIPIENT_EMAIL, "@"), 1);
    $sender_domain = substr(strrchr(SENDER_EMAIL, "@"), 1);
    
    if ($recipient_domain === 'example.com' || $sender_domain === 'example.com') {
        $issues[] = 'You are using example.com email addresses. Please update config.php with real email addresses.';
    }
    
    if (empty($issues)) {
        echo '<div class="test-result success">✅ No common issues detected</div>';
    } else {
        foreach ($issues as $issue) {
            echo '<div class="test-result warning">⚠️ ' . htmlspecialchars($issue) . '</div>';
        }
    }
    echo '</div>';

    // Test 6: Show recent logs
    if (ENABLE_LOGGING && file_exists(LOG_FILE)) {
        echo '<div class="debug-section">';
        echo '<h2>📝 Recent Log Entries</h2>';
        $log_content = file_get_contents(LOG_FILE);
        $log_lines = array_slice(explode("\n", trim($log_content)), -10); // Last 10 lines
        
        if (!empty($log_lines[0])) {
            foreach ($log_lines as $line) {
                if (trim($line)) {
                    echo '<div class="log-entry">' . htmlspecialchars($line) . '</div>';
                }
            }
        } else {
            echo '<div class="test-result info">No log entries found</div>';
        }
        echo '</div>';
    }

    // Test 7: Recommendations
    echo '<div class="debug-section">';
    echo '<h2>💡 Recommendations</h2>';
    echo '<div class="test-result info">';
    echo '<strong>To fix email issues, try these solutions:</strong><br><br>';
    echo '1. <strong>Update Configuration:</strong> Make sure config.php has your real email addresses<br>';
    echo '2. <strong>Check Hosting Provider:</strong> Contact your hosting provider to ensure email sending is enabled<br>';
    echo '3. <strong>Use SMTP:</strong> Consider using SMTP instead of the PHP mail() function for better reliability<br>';
    echo '4. <strong>Check Spam Folders:</strong> Test emails might end up in spam/junk folders<br>';
    echo '5. <strong>Domain Authentication:</strong> Ensure your domain has proper SPF, DKIM, and DMARC records<br>';
    echo '6. <strong>Server Logs:</strong> Check your server\'s mail logs for more detailed error information<br>';
    echo '7. <strong>Third-party Services:</strong> Consider using email services like SendGrid, Mailgun, or Amazon SES<br>';
    echo '</div>';
    echo '</div>';

    // Test 8: Quick fixes
    echo '<div class="debug-section">';
    echo '<h2>🔧 Quick Configuration Check</h2>';
    echo '<div class="test-result info">';
    echo '<strong>Current config.php values that need attention:</strong><br><br>';
    
    if (RECIPIENT_EMAIL === 'your-email@example.com') {
        echo '❌ <code>RECIPIENT_EMAIL</code> is still set to default value<br>';
    }
    if (SENDER_EMAIL === 'noreply@yourdomain.com') {
        echo '❌ <code>SENDER_EMAIL</code> is still set to default value<br>';
    }
    if (WEBSITE_NAME === 'Your Website') {
        echo '❌ <code>WEBSITE_NAME</code> is still set to default value<br>';
    }
    if (WEBSITE_URL === 'https://yourwebsite.com') {
        echo '❌ <code>WEBSITE_URL</code> is still set to default value<br>';
    }
    
    echo '<br><strong>To fix these:</strong><br>';
    echo '1. Edit the <code>config.php</code> file<br>';
    echo '2. Replace placeholder values with your actual information<br>';
    echo '3. Save the file and test again<br>';
    echo '</div>';
    echo '</div>';
    ?>

    <div class="debug-section info">
        <h2>🚀 Next Steps</h2>
        <ol>
            <li>Fix any issues identified above</li>
            <li>Update your <code>config.php</code> file with real email addresses</li>
            <li>Test with the buttons above to send test emails</li>
            <li>Check your email inbox (and spam folder)</li>
            <li>If still not working, contact your hosting provider about email configuration</li>
            <li>Consider using SMTP or a third-party email service for better reliability</li>
        </ol>
        <p><strong>Important:</strong> Remove this debug file from production servers for security!</p>
    </div>

    <div style="text-align: center; margin-top: 30px;">
        <a href="contact.html" class="btn">Test Contact Form</a>
        <a href="test_setup.php" class="btn">Run Setup Test</a>
    </div>
</body>
</html>