<?php
/**
 * Setup and Test Script for Contact Form Gmail Integration
 * 
 * This script helps you:
 * 1. Verify your configuration
 * 2. Test the email functionality
 * 3. Check if all dependencies are installed
 */

// Check if PHPMailer is installed
if (!file_exists('vendor/autoload.php')) {
    die("❌ PHPMailer is not installed. Please run 'composer install' first.\n");
}

require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

echo "🚀 Contact Form Setup & Test Script\n";
echo "===================================\n\n";

// Check if config file exists
if (!file_exists('config.php')) {
    die("❌ Configuration file 'config.php' not found. Please create it first.\n");
}

// Load configuration
$config = require_once 'config.php';

// Validate configuration
$requiredKeys = ['SMTP_HOST', 'SMTP_PORT', 'SMTP_USERNAME', 'SMTP_PASSWORD', 'FROM_EMAIL', 'TO_EMAIL'];
$missingKeys = [];

foreach ($requiredKeys as $key) {
    if (!isset($config[$key]) || empty($config[$key]) || strpos($config[$key], 'your-') !== false) {
        $missingKeys[] = $key;
    }
}

if (!empty($missingKeys)) {
    echo "❌ Configuration incomplete. Please update these values in config.php:\n";
    foreach ($missingKeys as $key) {
        echo "   - $key\n";
    }
    echo "\n📋 Instructions:\n";
    echo "1. Open config.php in a text editor\n";
    echo "2. Replace placeholder values with your actual Gmail credentials\n";
    echo "3. For Gmail App Password: Go to Google Account > Security > 2-Step Verification > App passwords\n";
    echo "4. Generate a new app password and use it in SMTP_PASSWORD\n";
    exit;
}

echo "✅ Configuration file loaded successfully\n";
echo "📧 Testing email configuration...\n\n";

// Test email function
function sendTestEmail($config) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = $config['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['SMTP_USERNAME'];
        $mail->Password = $config['SMTP_PASSWORD'];
        $mail->SMTPSecure = $config['SMTP_SECURE'] === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = $config['SMTP_PORT'];
        
        // Recipients
        $mail->setFrom($config['FROM_EMAIL'], $config['FROM_NAME'] ?? 'Contact Form Test');
        $mail->addAddress($config['TO_EMAIL'], $config['TO_NAME'] ?? 'Website Owner');
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Test Email - Contact Form Setup';
        $mail->Body = '
            <h2>🎉 Test Email Successful!</h2>
            <p>If you\'re reading this, your contact form email configuration is working correctly.</p>
            <hr>
            <p><strong>Test Details:</strong></p>
            <ul>
                <li>Sent from: ' . htmlspecialchars($config['FROM_EMAIL']) . '</li>
                <li>Sent to: ' . htmlspecialchars($config['TO_EMAIL']) . '</li>
                <li>Date: ' . date('Y-m-d H:i:s') . '</li>
                <li>Server: ' . htmlspecialchars($config['SMTP_HOST']) . ':' . $config['SMTP_PORT'] . '</li>
            </ul>
            <p>Your contact form is ready to use! 🚀</p>
        ';
        
        $mail->send();
        return ['success' => true, 'message' => 'Test email sent successfully!'];
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => "Test email failed: {$mail->ErrorInfo}"];
    }
}

// Run the test
$result = sendTestEmail($config);

if ($result['success']) {
    echo "✅ " . $result['message'] . "\n";
    echo "📬 Check your inbox at: " . $config['TO_EMAIL'] . "\n\n";
    echo "🎉 Setup Complete!\n";
    echo "Your contact form is ready to use. You can now:\n";
    echo "1. Open index.html in a web browser\n";
    echo "2. Fill out the contact form\n";
    echo "3. Submit it to test the full functionality\n\n";
    echo "💡 Tips:\n";
    echo "- Make sure your web server supports PHP (Apache, Nginx, etc.)\n";
    echo "- The form uses AJAX, so JavaScript must be enabled\n";
    echo "- For production, remove error reporting from send_email.php\n";
} else {
    echo "❌ " . $result['message'] . "\n\n";
    echo "🔧 Troubleshooting:\n";
    echo "1. Verify your Gmail credentials in config.php\n";
    echo "2. Make sure 2-factor authentication is enabled on your Gmail account\n";
    echo "3. Use an App Password (not your regular Gmail password)\n";
    echo "4. Check that 'Less secure app access' is disabled (use App Password instead)\n";
    echo "5. Verify your internet connection\n";
}

echo "\n📚 For more help, check the README.md file\n";
?>