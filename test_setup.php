<?php
/**
 * Contact Form Setup Test
 * 
 * This file helps you test your server configuration and email setup.
 * Run this file in your browser to check if everything is working correctly.
 */

// Include configuration
require_once 'config.php';

// Set content type
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form Setup Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { background-color: #d4edda; border-color: #c3e6cb; color: #155724; }
        .warning { background-color: #fff3cd; border-color: #ffeaa7; color: #856404; }
        .error { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .info { background-color: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
        h1 { color: #333; }
        h2 { color: #555; margin-top: 0; }
        code { background: #f4f4f4; padding: 2px 4px; border-radius: 3px; }
        .config-value { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Contact Form Setup Test</h1>
    <p>This page tests your contact form setup and server configuration.</p>

    <?php
    // Test 1: PHP Version
    echo '<div class="test-section ' . (version_compare(PHP_VERSION, '5.6.0', '>=') ? 'success' : 'error') . '">';
    echo '<h2>PHP Version Test</h2>';
    echo '<p>Current PHP Version: <span class="config-value">' . PHP_VERSION . '</span></p>';
    if (version_compare(PHP_VERSION, '5.6.0', '>=')) {
        echo '<p>✓ PHP version is compatible</p>';
    } else {
        echo '<p>✗ PHP version is too old. Please upgrade to PHP 5.6 or higher.</p>';
    }
    echo '</div>';

    // Test 2: Configuration
    echo '<div class="test-section info">';
    echo '<h2>Configuration Test</h2>';
    echo '<p>Recipient Email: <span class="config-value">' . RECIPIENT_EMAIL . '</span></p>';
    echo '<p>Sender Email: <span class="config-value">' . SENDER_EMAIL . '</span></p>';
    echo '<p>Website Name: <span class="config-value">' . WEBSITE_NAME . '</span></p>';
    echo '<p>Debug Mode: <span class="config-value">' . (DEBUG_MODE ? 'Enabled' : 'Disabled') . '</span></p>';
    echo '<p>Logging: <span class="config-value">' . (ENABLE_LOGGING ? 'Enabled' : 'Disabled') . '</span></p>';
    
    $configErrors = validateConfig();
    if (empty($configErrors)) {
        echo '<p>✓ Configuration appears to be valid</p>';
    } else {
        echo '<p>⚠ Configuration issues found:</p><ul>';
        foreach ($configErrors as $error) {
            echo '<li>' . htmlspecialchars($error) . '</li>';
        }
        echo '</ul>';
    }
    echo '</div>';

    // Test 3: Mail Function
    echo '<div class="test-section ' . (function_exists('mail') ? 'success' : 'error') . '">';
    echo '<h2>Mail Function Test</h2>';
    if (function_exists('mail')) {
        echo '<p>✓ PHP mail() function is available</p>';
        
        // Test sending a simple email
        if (isset($_GET['test_email']) && $_GET['test_email'] === '1') {
            $testSubject = 'Contact Form Test Email';
            $testMessage = 'This is a test email from your contact form setup. If you receive this, your email configuration is working!';
            $testHeaders = 'From: ' . SENDER_EMAIL . "\r\n" . 'Content-Type: text/plain; charset=UTF-8';
            
            if (mail(RECIPIENT_EMAIL, $testSubject, $testMessage, $testHeaders)) {
                echo '<p>✓ Test email sent successfully to ' . RECIPIENT_EMAIL . '</p>';
            } else {
                echo '<p>✗ Failed to send test email. Check your mail server configuration.</p>';
            }
        } else {
            echo '<p><a href="?test_email=1">Click here to send a test email</a></p>';
        }
    } else {
        echo '<p>✗ PHP mail() function is not available. Check your server configuration.</p>';
    }
    echo '</div>';

    // Test 4: File Permissions
    echo '<div class="test-section">';
    echo '<h2>File Permissions Test</h2>';
    
    $writableDir = is_writable(dirname(__FILE__));
    echo '<div class="' . ($writableDir ? 'success' : 'warning') . '">';
    if ($writableDir) {
        echo '<p>✓ Current directory is writable (needed for logging)</p>';
    } else {
        echo '<p>⚠ Current directory is not writable. Logging may not work.</p>';
    }
    echo '</div>';
    
    // Test log file creation
    if (ENABLE_LOGGING) {
        $logFile = LOG_FILE;
        $canCreateLog = @file_put_contents($logFile, 'Test log entry - ' . date('Y-m-d H:i:s') . "\n", FILE_APPEND | LOCK_EX);
        echo '<div class="' . ($canCreateLog ? 'success' : 'error') . '">';
        if ($canCreateLog) {
            echo '<p>✓ Log file can be created/written to</p>';
        } else {
            echo '<p>✗ Cannot write to log file. Check directory permissions.</p>';
        }
        echo '</div>';
    }
    echo '</div>';

    // Test 5: Required Extensions
    echo '<div class="test-section">';
    echo '<h2>PHP Extensions Test</h2>';
    
    $requiredExtensions = ['filter', 'json'];
    $allExtensionsLoaded = true;
    
    foreach ($requiredExtensions as $ext) {
        $loaded = extension_loaded($ext);
        $allExtensionsLoaded = $allExtensionsLoaded && $loaded;
        echo '<p>' . ($loaded ? '✓' : '✗') . ' ' . $ext . ' extension: ' . ($loaded ? 'Loaded' : 'Not loaded') . '</p>';
    }
    
    echo '<div class="' . ($allExtensionsLoaded ? 'success' : 'error') . '">';
    if ($allExtensionsLoaded) {
        echo '<p>All required PHP extensions are loaded</p>';
    } else {
        echo '<p>Some required PHP extensions are missing</p>';
    }
    echo '</div>';
    echo '</div>';

    // Test 6: Form Files
    echo '<div class="test-section">';
    echo '<h2>Form Files Test</h2>';
    
    $requiredFiles = ['contact.html', 'send_email.php', 'css/contact.css', 'config.php'];
    $allFilesExist = true;
    
    foreach ($requiredFiles as $file) {
        $exists = file_exists($file);
        $allFilesExist = $allFilesExist && $exists;
        echo '<p>' . ($exists ? '✓' : '✗') . ' ' . $file . ': ' . ($exists ? 'Found' : 'Missing') . '</p>';
    }
    
    echo '<div class="' . ($allFilesExist ? 'success' : 'error') . '">';
    if ($allFilesExist) {
        echo '<p>All required files are present</p>';
    } else {
        echo '<p>Some required files are missing</p>';
    }
    echo '</div>';
    echo '</div>';

    // Summary
    echo '<div class="test-section info">';
    echo '<h2>Next Steps</h2>';
    echo '<ol>';
    echo '<li>Update <code>config.php</code> with your actual email addresses</li>';
    echo '<li>Test the contact form by opening <code>contact.html</code></li>';
    echo '<li>Check your email for test submissions</li>';
    echo '<li>Customize the styling in <code>css/contact.css</code> if needed</li>';
    echo '<li>Deploy to your production server</li>';
    echo '</ol>';
    echo '<p><strong>Security Note:</strong> Remove or restrict access to this test file in production!</p>';
    echo '</div>';
    ?>

    <div class="test-section info">
        <h2>Troubleshooting</h2>
        <ul>
            <li>If emails aren't being sent, check your server's mail configuration</li>
            <li>Check PHP error logs for detailed error messages</li>
            <li>Ensure your hosting provider allows email sending</li>
            <li>Consider using SMTP instead of the mail() function for better reliability</li>
            <li>Test with different email providers (some may block certain servers)</li>
        </ul>
    </div>
</body>
</html>