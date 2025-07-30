<?php
// Include configuration file
require_once 'config.php';

// Enable error reporting for debugging (remove in production)
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Set content type to JSON
header('Content-Type: application/json');

// Enable CORS if needed (adjust origins as necessary)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Function to sanitize input data
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Function to validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to validate required fields
function validateRequired($value) {
    return !empty(trim($value));
}

try {
    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST method is allowed');
    }

    // Get and sanitize form data
    $name = isset($_POST['name']) ? sanitizeInput($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitizeInput($_POST['phone']) : '';
    $subject = isset($_POST['subject']) ? sanitizeInput($_POST['subject']) : '';
    $message = isset($_POST['message']) ? sanitizeInput($_POST['message']) : '';

    // Validate required fields
    $errors = [];
    
    if (!validateRequired($name)) {
        $errors[] = 'Name is required';
    }
    
    if (!validateRequired($email)) {
        $errors[] = 'Email is required';
    } elseif (!validateEmail($email)) {
        $errors[] = 'Please enter a valid email address';
    }
    
    if (!validateRequired($subject)) {
        $errors[] = 'Subject is required';
    }
    
    if (!validateRequired($message)) {
        $errors[] = 'Message is required';
    }

    // Check for validation errors
    if (!empty($errors)) {
        throw new Exception('Validation failed: ' . implode(', ', $errors));
    }

    // Email configuration
    $to = RECIPIENT_EMAIL;
    $from = SENDER_EMAIL;
    $replyTo = $email;
    
    // Create email subject
    $emailSubject = 'Contact Form: ' . $subject;
    
    // Create email message
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
                <h2>New Contact Form Submission</h2>
            </div>
            <div class='content'>
                <div class='field'>
                    <div class='label'>Name:</div>
                    <div class='value'>" . htmlspecialchars($name) . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Email:</div>
                    <div class='value'>" . htmlspecialchars($email) . "</div>
                </div>";
    
    if (!empty($phone)) {
        $emailMessage .= "
                <div class='field'>
                    <div class='label'>Phone:</div>
                    <div class='value'>" . htmlspecialchars($phone) . "</div>
                </div>";
    }
    
    $emailMessage .= "
                <div class='field'>
                    <div class='label'>Subject:</div>
                    <div class='value'>" . htmlspecialchars($subject) . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Message:</div>
                    <div class='value'>" . nl2br(htmlspecialchars($message)) . "</div>
                </div>
            </div>
                         <div class='footer'>
                 <p>This email was sent from the contact form on " . WEBSITE_NAME . ".</p>
                 <p>Website: " . WEBSITE_URL . "</p>
                 <p>Sent on: " . date('Y-m-d H:i:s') . "</p>
             </div>
        </div>
    </body>
    </html>";

    // Email headers
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: ' . SENDER_NAME . ' <' . $from . '>',
        'Reply-To: ' . $replyTo,
        'X-Mailer: PHP/' . phpversion(),
        'X-Priority: 3',
        'Date: ' . date('r'),
    ];

    // Additional security headers
    $headers[] = 'X-Anti-Abuse: This header was added to track abuse, please include it in any abuse report';
    $headers[] = 'X-Anti-Abuse: Primary Hostname - ' . (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'unknown');
    $headers[] = 'X-Anti-Abuse: Original Domain - ' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'unknown');
    $headers[] = 'X-Anti-Abuse: Originating IP - ' . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown');

    // Convert headers array to string
    $headerString = implode("\r\n", $headers);

    // Send email
    $mailSent = mail($to, $emailSubject, $emailMessage, $headerString);

    if ($mailSent) {
        // Success response
        echo json_encode([
            'success' => true,
            'message' => 'Email sent successfully!'
        ]);
    } else {
        throw new Exception('Failed to send email. Please check your mail server configuration.');
    }

} catch (Exception $e) {
    // Error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Optional: Log the submission
if (ENABLE_LOGGING) {
    $logEntry = date('Y-m-d H:i:s') . " - Contact form submission from: $name ($email) - Subject: $subject - IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
    file_put_contents(LOG_FILE, $logEntry, FILE_APPEND | LOCK_EX);
}
?>