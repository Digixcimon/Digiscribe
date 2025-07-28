<?php
// Set content type to JSON
header('Content-Type: application/json');

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Include PHPMailer autoloader
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load configuration
$config = require_once 'config.php';

// Validate and sanitize input
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

try {
    // Get and validate form data
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');

    // Validation
    $errors = [];

    if (strlen($name) < 2) {
        $errors[] = 'Name must be at least 2 characters long';
    }

    if (!validateEmail($email)) {
        $errors[] = 'Invalid email address';
    }

    if (strlen($subject) < 3) {
        $errors[] = 'Subject must be at least 3 characters long';
    }

    if (strlen($message) < 10) {
        $errors[] = 'Message must be at least 10 characters long';
    }

    // If there are validation errors, return them
    if (!empty($errors)) {
        echo json_encode([
            'success' => false, 
            'message' => 'Validation errors: ' . implode(', ', $errors)
        ]);
        exit;
    }

    // Create PHPMailer instance
    $mail = new PHPMailer(true);

    // Server settings
    $mail->isSMTP();
    $mail->Host = $config['SMTP_HOST'];
    $mail->SMTPAuth = true;
    $mail->Username = $config['SMTP_USERNAME'];
    $mail->Password = $config['SMTP_PASSWORD'];
    $mail->SMTPSecure = $config['SMTP_SECURE'] === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = $config['SMTP_PORT'];

    // Recipients
    $mail->setFrom($config['FROM_EMAIL'], $config['FROM_NAME']);
    $mail->addAddress($config['TO_EMAIL'], $config['TO_NAME']);
    $mail->addReplyTo($email, $name);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Contact Form: ' . $subject;
    
    // Create HTML email body
    $htmlBody = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; }
            .content { background: #f9f9f9; padding: 20px; }
            .field { margin-bottom: 15px; }
            .field strong { color: #667eea; }
            .message-box { background: white; padding: 15px; border-left: 4px solid #667eea; margin-top: 15px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>New Contact Form Submission</h2>
            </div>
            <div class='content'>
                <div class='field'>
                    <strong>Name:</strong> $name
                </div>
                <div class='field'>
                    <strong>Email:</strong> $email
                </div>";
                
    if (!empty($phone)) {
        $htmlBody .= "
                <div class='field'>
                    <strong>Phone:</strong> $phone
                </div>";
    }
    
    $htmlBody .= "
                <div class='field'>
                    <strong>Subject:</strong> $subject
                </div>
                <div class='field'>
                    <strong>Date:</strong> " . date('Y-m-d H:i:s') . "
                </div>
                <div class='message-box'>
                    <strong>Message:</strong><br>
                    " . nl2br($message) . "
                </div>
            </div>
        </div>
    </body>
    </html>";

    $mail->Body = $htmlBody;

    // Alternative plain text body
    $textBody = "New Contact Form Submission\n\n";
    $textBody .= "Name: $name\n";
    $textBody .= "Email: $email\n";
    if (!empty($phone)) {
        $textBody .= "Phone: $phone\n";
    }
    $textBody .= "Subject: $subject\n";
    $textBody .= "Date: " . date('Y-m-d H:i:s') . "\n\n";
    $textBody .= "Message:\n$message";

    $mail->AltBody = $textBody;

    // Send email
    $mail->send();

    // Success response
    echo json_encode([
        'success' => true,
        'message' => 'Thank you! Your message has been sent successfully. We\'ll get back to you soon.'
    ]);

} catch (Exception $e) {
    // Error response
    error_log("Mail Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Sorry, there was an error sending your message. Please try again later.'
    ]);
}
?>