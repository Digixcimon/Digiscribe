<?php
/**
 * Configuration file for Gmail Contact Form
 * 
 * IMPORTANT: Before using this application, you need to:
 * 1. Enable 2-factor authentication on your Gmail account
 * 2. Generate an App Password for this application
 * 3. Update the constants below with your actual Gmail credentials
 * 4. Enable IMAP/POP3 access in your Gmail settings
 */

// Gmail IMAP Configuration
define('GMAIL_IMAP_HOST', 'imap.gmail.com');
define('GMAIL_IMAP_PORT', 993);
define('GMAIL_IMAP_ENCRYPTION', 'ssl');

// Gmail SMTP Configuration
define('GMAIL_SMTP_HOST', 'smtp.gmail.com');
define('GMAIL_SMTP_PORT', 587);
define('GMAIL_SMTP_ENCRYPTION', 'tls');

// Gmail POP3 Configuration
define('GMAIL_POP3_HOST', 'pop.gmail.com');
define('GMAIL_POP3_PORT', 995);
define('GMAIL_POP3_ENCRYPTION', 'ssl');

// Gmail Account Settings
// IMPORTANT: Replace these with your actual Gmail credentials
define('GMAIL_USERNAME', 'your-email@gmail.com');
define('GMAIL_PASSWORD', 'your-app-password'); // Use App Password, not regular password
define('GMAIL_RECIPIENT', 'your-email@gmail.com'); // Where to send form submissions

// Application Settings
define('APP_NAME', 'Contact Form Mailer');
define('FROM_NAME', 'Contact Form');
define('REPLY_TO_EMAIL', GMAIL_USERNAME);

// Security Settings
define('MAX_ATTEMPTS_PER_HOUR', 5); // Maximum form submissions per IP per hour
define('ENABLE_RATE_LIMITING', true);

// File Upload Settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png']);

// Email Templates
define('AUTO_REPLY_SUBJECT', 'Thank you for contacting us!');
define('AUTO_REPLY_BODY', '
Dear {name},

Thank you for reaching out to us! We have received your message and will get back to you as soon as possible.

Here\'s a summary of your submission:
Subject: {subject}
Message: {message}

We typically respond within 24-48 hours during business days.

Best regards,
The Support Team
');

// Database Configuration (Optional - for logging)
define('DB_HOST', 'localhost');
define('DB_NAME', 'contact_form');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');
define('USE_DATABASE', false); // Set to true if you want to use database logging

// Timezone
date_default_timezone_set('UTC');

// Error Reporting (Set to false in production)
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

/**
 * Validate configuration
 */
function validate_config() {
    $required_constants = [
        'GMAIL_USERNAME',
        'GMAIL_PASSWORD',
        'GMAIL_RECIPIENT'
    ];
    
    foreach ($required_constants as $constant) {
        if (!defined($constant) || empty(constant($constant))) {
            throw new Exception("Configuration error: {$constant} is not set or empty.");
        }
    }
    
    // Check if Gmail credentials are still default values
    if (GMAIL_USERNAME === 'your-email@gmail.com' || GMAIL_PASSWORD === 'your-app-password') {
        throw new Exception("Please update your Gmail credentials in config.php");
    }
    
    // Validate email format
    if (!filter_var(GMAIL_USERNAME, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid Gmail username format.");
    }
    
    if (!filter_var(GMAIL_RECIPIENT, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid Gmail recipient format.");
    }
}

/**
 * Rate limiting function
 */
function check_rate_limit() {
    if (!ENABLE_RATE_LIMITING) {
        return true;
    }
    
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $rate_limit_file = 'logs/rate_limit.json';
    
    // Create logs directory if it doesn't exist
    if (!is_dir('logs')) {
        mkdir('logs', 0755, true);
    }
    
    $current_time = time();
    $hour_ago = $current_time - 3600;
    
    // Load existing rate limit data
    $rate_data = [];
    if (file_exists($rate_limit_file)) {
        $content = file_get_contents($rate_limit_file);
        $rate_data = json_decode($content, true) ?: [];
    }
    
    // Clean old entries
    foreach ($rate_data as $ip_key => $timestamps) {
        $rate_data[$ip_key] = array_filter($timestamps, function($timestamp) use ($hour_ago) {
            return $timestamp > $hour_ago;
        });
        
        if (empty($rate_data[$ip_key])) {
            unset($rate_data[$ip_key]);
        }
    }
    
    // Check current IP
    if (!isset($rate_data[$ip])) {
        $rate_data[$ip] = [];
    }
    
    if (count($rate_data[$ip]) >= MAX_ATTEMPTS_PER_HOUR) {
        throw new Exception("Rate limit exceeded. Please try again later.");
    }
    
    // Add current timestamp
    $rate_data[$ip][] = $current_time;
    
    // Save updated rate limit data
    file_put_contents($rate_limit_file, json_encode($rate_data), LOCK_EX);
    
    return true;
}

// Validate configuration on include (only if not in setup mode)
if (!defined('SETUP_MODE')) {
    try {
        validate_config();
    } catch (Exception $e) {
        if (DEBUG_MODE) {
            die("Configuration Error: " . $e->getMessage() . "<br><br>Please run <a href='setup.php'>setup.php</a> to configure your Gmail settings.");
        } else {
            die("Configuration Error. Please run setup.php to configure your settings.");
        }
    }
}
?>