<?php
/**
 * Example Email Configuration File
 * 
 * COPY this file to 'config.php' and update the values below with your actual Gmail credentials
 * 
 * IMPORTANT SECURITY NOTES:
 * - Never commit config.php to version control (add it to .gitignore)
 * - Use Gmail App Passwords, not your regular password
 * - Keep your credentials secure and private
 */

return [
    // SMTP Settings for Gmail
    'SMTP_HOST' => 'smtp.gmail.com',
    'SMTP_PORT' => 587,
    'SMTP_SECURE' => 'tls', // or 'ssl' for port 465
    
    // Your Gmail credentials (REPLACE THESE!)
    'SMTP_USERNAME' => 'your-email@gmail.com',          // Your Gmail address
    'SMTP_PASSWORD' => 'abcd efgh ijkl mnop',           // Your 16-character Gmail App Password
    
    // Email settings
    'FROM_EMAIL' => 'your-email@gmail.com',             // Your Gmail address (sender)
    'FROM_NAME' => 'Your Website Contact Form',         // Sender name
    'TO_EMAIL' => 'recipient@gmail.com',                // Where to receive contact form emails
    'TO_NAME' => 'Website Owner',                       // Recipient name
    
    // Optional: Reply-to email (if different from FROM_EMAIL)
    'REPLY_TO_EMAIL' => '',                             // Leave empty to use FROM_EMAIL
    'REPLY_TO_NAME' => '',                              // Leave empty to use FROM_NAME
    
    // Email template settings
    'SUBJECT_PREFIX' => '[Contact Form]',               // Prefix for email subjects
    'EMAIL_FOOTER' => 'This email was sent from your website contact form.', // Footer text
];

/**
 * SETUP INSTRUCTIONS:
 * 
 * 1. Copy this file to 'config.php'
 * 2. Enable 2-factor authentication on your Gmail account
 * 3. Generate an App Password:
 *    - Go to Google Account settings
 *    - Security > 2-Step Verification > App passwords
 *    - Generate a new app password for "Mail"
 *    - Use this 16-character password in SMTP_PASSWORD
 * 4. Update all the email addresses and names
 * 5. Test the setup by running: php setup.php
 * 
 * TROUBLESHOOTING:
 * - Make sure 2FA is enabled on your Gmail account
 * - Use App Password, not your regular Gmail password
 * - Check that "Less secure app access" is disabled
 * - Verify your internet connection
 * - Check Gmail's SMTP settings are correct
 */
?>