<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Include configuration and email class
require_once 'config.php';
require_once 'EmailSender.php';

// Initialize response array
$response = array('success' => false, 'message' => '');

try {
    // Check if request is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST requests are allowed.');
    }
    
    // Validate required fields
    $required_fields = ['name', 'email', 'subject', 'message'];
    $missing_fields = [];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $missing_fields[] = ucfirst($field);
        }
    }
    
    if (!empty($missing_fields)) {
        throw new Exception('Missing required fields: ' . implode(', ', $missing_fields));
    }
    
    // Sanitize input data
    $form_data = array(
        'name' => sanitize_input($_POST['name']),
        'email' => sanitize_input($_POST['email']),
        'phone' => sanitize_input($_POST['phone'] ?? ''),
        'subject' => sanitize_input($_POST['subject']),
        'message' => sanitize_input($_POST['message']),
        'priority' => sanitize_input($_POST['priority'] ?? 'normal'),
        'subscribe' => isset($_POST['subscribe']) ? 'yes' : 'no'
    );
    
    // Validate email format
    if (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address format.');
    }
    
    // Handle file attachment
    $attachment = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $attachment = handle_file_upload($_FILES['attachment']);
    }
    
    // Create email sender instance
    $emailSender = new EmailSender();
    
    // Prepare email content
    $email_subject = "[Contact Form] " . $form_data['subject'];
    $email_body = prepare_email_body($form_data);
    
    // Send email to Gmail
    $result = $emailSender->sendToGmail(
        GMAIL_RECIPIENT,
        $email_subject,
        $email_body,
        $form_data['email'],
        $form_data['name'],
        $attachment,
        $form_data['priority']
    );
    
    if ($result['success']) {
        // Log successful submission
        log_submission($form_data, true);
        
        // Send auto-reply to user
        $emailSender->sendAutoReply($form_data['email'], $form_data['name']);
        
        $response['success'] = true;
        $response['message'] = 'Your message has been sent successfully! We\'ll get back to you soon.';
    } else {
        throw new Exception($result['message']);
    }
    
} catch (Exception $e) {
    // Log error
    error_log("Contact Form Error: " . $e->getMessage());
    log_submission($form_data ?? [], false, $e->getMessage());
    
    $response['message'] = $e->getMessage();
}

// Clean up uploaded file if exists
if (isset($attachment) && file_exists($attachment['tmp_path'])) {
    unlink($attachment['tmp_path']);
}

// Return JSON response
echo json_encode($response);

/**
 * Sanitize input data
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Handle file upload
 */
function handle_file_upload($file) {
    $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain', 'image/jpeg', 'image/png', 'image/jpg'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if ($file['size'] > $max_size) {
        throw new Exception('File size too large. Maximum allowed size is 5MB.');
    }
    
    if (!in_array($file['type'], $allowed_types)) {
        throw new Exception('Invalid file type. Allowed types: PDF, DOC, DOCX, TXT, JPG, JPEG, PNG.');
    }
    
    // Create uploads directory if it doesn't exist
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Generate unique filename
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $unique_filename = uniqid() . '_' . time() . '.' . $file_extension;
    $upload_path = $upload_dir . $unique_filename;
    
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        throw new Exception('Failed to upload file.');
    }
    
    return array(
        'original_name' => $file['name'],
        'tmp_path' => $upload_path,
        'size' => $file['size'],
        'type' => $file['type']
    );
}

/**
 * Prepare email body content
 */
function prepare_email_body($data) {
    $body = "New contact form submission:\n\n";
    $body .= "Name: " . $data['name'] . "\n";
    $body .= "Email: " . $data['email'] . "\n";
    $body .= "Phone: " . ($data['phone'] ?: 'Not provided') . "\n";
    $body .= "Priority: " . ucfirst($data['priority']) . "\n";
    $body .= "Newsletter Subscription: " . $data['subscribe'] . "\n";
    $body .= "Subject: " . $data['subject'] . "\n\n";
    $body .= "Message:\n" . $data['message'] . "\n\n";
    $body .= "---\n";
    $body .= "Submitted on: " . date('Y-m-d H:i:s') . "\n";
    $body .= "IP Address: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . "\n";
    $body .= "User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown') . "\n";
    
    return $body;
}

/**
 * Log form submissions
 */
function log_submission($data, $success, $error_message = '') {
    $log_entry = array(
        'timestamp' => date('Y-m-d H:i:s'),
        'name' => $data['name'] ?? '',
        'email' => $data['email'] ?? '',
        'subject' => $data['subject'] ?? '',
        'success' => $success,
        'error' => $error_message,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
    );
    
    $log_file = 'logs/submissions.log';
    
    // Create logs directory if it doesn't exist
    if (!is_dir('logs')) {
        mkdir('logs', 0755, true);
    }
    
    file_put_contents($log_file, json_encode($log_entry) . "\n", FILE_APPEND | LOCK_EX);
}
?>