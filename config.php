<?php
/**
 * Email Configuration File
 * 
 * This file contains all the email configuration settings.
 * Update these settings according to your requirements.
 */

// Email Settings
define('RECIPIENT_EMAIL', 'your-email@example.com'); // Replace with your actual email address
define('SENDER_NAME', 'Contact Form'); // Name that appears in the "From" field
define('SENDER_EMAIL', 'noreply@yourdomain.com'); // Replace with your domain email

// Website Settings
define('WEBSITE_NAME', 'Your Website'); // Your website name
define('WEBSITE_URL', 'https://yourwebsite.com'); // Your website URL

// Email Template Settings
define('EMAIL_TEMPLATE_HEADER_COLOR', '#667eea'); // Header background color
define('EMAIL_TEMPLATE_ACCENT_COLOR', '#667eea'); // Accent color for borders

// Security Settings
define('ENABLE_LOGGING', true); // Set to true to enable contact form logging
define('LOG_FILE', 'contact_submissions.log'); // Log file name

// Rate Limiting Settings (optional - requires session support)
define('ENABLE_RATE_LIMITING', false); // Set to true to enable basic rate limiting
define('MAX_SUBMISSIONS_PER_HOUR', 5); // Maximum submissions per IP per hour

// Notification Settings
define('ENABLE_AUTO_REPLY', true); // Send auto-reply to form submitter
define('AUTO_REPLY_SUBJECT', 'Thank you for contacting us!'); // Auto-reply subject

// SMTP Settings (if you want to use SMTP instead of PHP mail function)
// Note: You'll need to install PHPMailer or similar library to use SMTP
define('USE_SMTP', false); // Set to true to use SMTP
define('SMTP_HOST', 'smtp.gmail.com'); // SMTP server
define('SMTP_PORT', 587); // SMTP port (587 for TLS, 465 for SSL)
define('SMTP_USERNAME', 'your-email@gmail.com'); // SMTP username
define('SMTP_PASSWORD', 'your-app-password'); // SMTP password (use app password for Gmail)
define('SMTP_ENCRYPTION', 'tls'); // 'tls' or 'ssl'

// Validation Settings
define('MIN_MESSAGE_LENGTH', 10); // Minimum message length
define('MAX_MESSAGE_LENGTH', 5000); // Maximum message length
define('ALLOWED_FILE_TYPES', 'jpg,jpeg,png,pdf,doc,docx'); // If you add file upload later

// Honeypot field name for spam protection
define('HONEYPOT_FIELD', 'website_url'); // Hidden field name to catch bots

// reCAPTCHA Settings (if you want to add reCAPTCHA later)
define('ENABLE_RECAPTCHA', false); // Set to true to enable reCAPTCHA
define('RECAPTCHA_SITE_KEY', 'your-site-key'); // Your reCAPTCHA site key
define('RECAPTCHA_SECRET_KEY', 'your-secret-key'); // Your reCAPTCHA secret key

// Debug Settings
define('DEBUG_MODE', false); // Set to true for debugging (shows detailed error messages)
define('SEND_TEST_EMAILS', false); // Set to true to send test emails to yourself

// Backup Email (optional)
define('BACKUP_EMAIL', ''); // Additional email to receive copies (leave empty to disable)

/**
 * Function to get configuration value with fallback
 */
function getConfig($key, $default = null) {
    return defined($key) ? constant($key) : $default;
}

/**
 * Function to validate configuration
 */
function validateConfig() {
    $errors = [];
    
    if (!filter_var(RECIPIENT_EMAIL, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid recipient email address';
    }
    
    if (USE_SMTP && (!SMTP_HOST || !SMTP_USERNAME || !SMTP_PASSWORD)) {
        $errors[] = 'SMTP configuration is incomplete';
    }
    
    return $errors;
}

// Initialize configuration validation
if (DEBUG_MODE) {
    $configErrors = validateConfig();
    if (!empty($configErrors)) {
        error_log('Contact Form Configuration Errors: ' . implode(', ', $configErrors));
    }
}
?>