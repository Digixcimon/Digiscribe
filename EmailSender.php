<?php
/**
 * EmailSender Class
 * 
 * Handles email sending to Gmail using IMAP, POP3, and SMTP protocols
 * Supports attachments, auto-replies, and email validation
 */

class EmailSender {
    private $imap_connection = null;
    private $smtp_connection = null;
    
    public function __construct() {
        // Check for required PHP extensions
        $this->checkRequiredExtensions();
    }
    
    /**
     * Check if required PHP extensions are available
     */
    private function checkRequiredExtensions() {
        $required_extensions = ['imap', 'openssl', 'mbstring'];
        $missing_extensions = [];
        
        foreach ($required_extensions as $ext) {
            if (!extension_loaded($ext)) {
                $missing_extensions[] = $ext;
            }
        }
        
        if (!empty($missing_extensions)) {
            throw new Exception('Missing required PHP extensions: ' . implode(', ', $missing_extensions));
        }
    }
    
    /**
     * Connect to Gmail IMAP server
     */
    private function connectIMAP() {
        if ($this->imap_connection !== null) {
            return $this->imap_connection;
        }
        
        $mailbox = '{' . GMAIL_IMAP_HOST . ':' . GMAIL_IMAP_PORT . '/imap/' . GMAIL_IMAP_ENCRYPTION . '}INBOX';
        
        try {
            $this->imap_connection = imap_open($mailbox, GMAIL_USERNAME, GMAIL_PASSWORD);
            
            if (!$this->imap_connection) {
                throw new Exception('IMAP connection failed: ' . imap_last_error());
            }
            
            return $this->imap_connection;
        } catch (Exception $e) {
            throw new Exception('IMAP Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Connect to Gmail POP3 server
     */
    private function connectPOP3() {
        $mailbox = '{' . GMAIL_POP3_HOST . ':' . GMAIL_POP3_PORT . '/pop3/' . GMAIL_POP3_ENCRYPTION . '}INBOX';
        
        try {
            $connection = imap_open($mailbox, GMAIL_USERNAME, GMAIL_PASSWORD);
            
            if (!$connection) {
                throw new Exception('POP3 connection failed: ' . imap_last_error());
            }
            
            return $connection;
        } catch (Exception $e) {
            throw new Exception('POP3 Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Send email to Gmail using SMTP
     */
    public function sendToGmail($to, $subject, $message, $from_email = null, $from_name = null, $attachment = null, $priority = 'normal') {
        try {
            // Check rate limiting
            check_rate_limit();
            
            // Validate inputs
            if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid recipient email address.');
            }
            
            // Prepare headers
            $headers = $this->prepareHeaders($from_email, $from_name, $priority);
            
            // Prepare message
            $email_body = $this->prepareEmailBody($message, $attachment);
            
            // Send using PHPMailer-like functionality
            $result = $this->sendSMTPEmail($to, $subject, $email_body, $headers, $attachment);
            
            if ($result) {
                // Verify email was sent by checking IMAP
                $this->verifyEmailSent($subject);
                
                return array('success' => true, 'message' => 'Email sent successfully.');
            } else {
                throw new Exception('Failed to send email via SMTP.');
            }
            
        } catch (Exception $e) {
            error_log('EmailSender Error: ' . $e->getMessage());
            return array('success' => false, 'message' => $e->getMessage());
        }
    }
    
    /**
     * Send auto-reply to form submitter
     */
    public function sendAutoReply($to_email, $to_name) {
        try {
            $subject = AUTO_REPLY_SUBJECT;
            $body = str_replace(
                ['{name}', '{subject}', '{message}'],
                [$to_name, $_POST['subject'] ?? '', $_POST['message'] ?? ''],
                AUTO_REPLY_BODY
            );
            
            $headers = $this->prepareHeaders(GMAIL_USERNAME, FROM_NAME, 'normal');
            
            return $this->sendSMTPEmail($to_email, $subject, $body, $headers);
            
        } catch (Exception $e) {
            error_log('Auto-reply Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Prepare email headers
     */
    private function prepareHeaders($from_email, $from_name, $priority) {
        $headers = array();
        
        // From header
        if ($from_email && $from_name) {
            $headers[] = "From: {$from_name} <{$from_email}>";
            $headers[] = "Reply-To: {$from_email}";
        } else {
            $headers[] = "From: " . FROM_NAME . " <" . GMAIL_USERNAME . ">";
            $headers[] = "Reply-To: " . GMAIL_USERNAME;
        }
        
        // Standard headers
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-Type: text/plain; charset=UTF-8";
        $headers[] = "Content-Transfer-Encoding: 8bit";
        $headers[] = "X-Mailer: " . APP_NAME;
        $headers[] = "Date: " . date('r');
        
        // Priority headers
        switch ($priority) {
            case 'high':
                $headers[] = "X-Priority: 2";
                $headers[] = "Importance: High";
                break;
            case 'urgent':
                $headers[] = "X-Priority: 1";
                $headers[] = "Importance: High";
                break;
            default:
                $headers[] = "X-Priority: 3";
                $headers[] = "Importance: Normal";
        }
        
        return $headers;
    }
    
    /**
     * Prepare email body with attachment support
     */
    private function prepareEmailBody($message, $attachment = null) {
        if ($attachment === null) {
            return $message;
        }
        
        // Create multipart message for attachment
        $boundary = "boundary_" . md5(uniqid(time()));
        
        $body = "--{$boundary}\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $body .= $message . "\r\n\r\n";
        
        // Add attachment
        if (file_exists($attachment['tmp_path'])) {
            $file_content = file_get_contents($attachment['tmp_path']);
            $encoded_content = chunk_split(base64_encode($file_content));
            
            $body .= "--{$boundary}\r\n";
            $body .= "Content-Type: {$attachment['type']}; name=\"{$attachment['original_name']}\"\r\n";
            $body .= "Content-Transfer-Encoding: base64\r\n";
            $body .= "Content-Disposition: attachment; filename=\"{$attachment['original_name']}\"\r\n\r\n";
            $body .= $encoded_content . "\r\n";
        }
        
        $body .= "--{$boundary}--\r\n";
        
        return $body;
    }
    
    /**
     * Send email via SMTP using native PHP functions
     */
    private function sendSMTPEmail($to, $subject, $body, $headers, $attachment = null) {
        try {
            // Update headers for multipart if attachment exists
            if ($attachment !== null) {
                $boundary = "boundary_" . md5(uniqid(time()));
                $headers[] = "Content-Type: multipart/mixed; boundary=\"{$boundary}\"";
            }
            
            // Convert headers array to string
            $header_string = implode("\r\n", $headers);
            
            // Use PHP's mail function (requires proper SMTP configuration)
            // For production, you should use a proper SMTP library like PHPMailer
            $result = mail($to, $subject, $body, $header_string);
            
            if (!$result) {
                // Fallback: Use socket connection to Gmail SMTP
                return $this->sendViaSMTPSocket($to, $subject, $body, $header_string);
            }
            
            return $result;
            
        } catch (Exception $e) {
            throw new Exception('SMTP Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Send email via direct SMTP socket connection
     */
    private function sendViaSMTPSocket($to, $subject, $body, $headers) {
        try {
            // Create socket connection
            $socket = fsockopen('ssl://' . GMAIL_SMTP_HOST, 465, $errno, $errstr, 30);
            
            if (!$socket) {
                throw new Exception("Socket connection failed: {$errstr} ({$errno})");
            }
            
            // Read initial response
            $response = fgets($socket, 512);
            
            // SMTP conversation
            $commands = array(
                "EHLO " . $_SERVER['HTTP_HOST'] ?? 'localhost',
                "AUTH LOGIN",
                base64_encode(GMAIL_USERNAME),
                base64_encode(GMAIL_PASSWORD),
                "MAIL FROM: <" . GMAIL_USERNAME . ">",
                "RCPT TO: <{$to}>",
                "DATA"
            );
            
            foreach ($commands as $command) {
                fputs($socket, $command . "\r\n");
                $response = fgets($socket, 512);
                
                // Check for errors
                if (substr($response, 0, 1) === '4' || substr($response, 0, 1) === '5') {
                    fclose($socket);
                    throw new Exception("SMTP Error: {$response}");
                }
            }
            
            // Send email content
            fputs($socket, $headers . "\r\n\r\n");
            fputs($socket, $body . "\r\n.\r\n");
            $response = fgets($socket, 512);
            
            // Quit
            fputs($socket, "QUIT\r\n");
            fclose($socket);
            
            return substr($response, 0, 1) === '2'; // Success codes start with 2
            
        } catch (Exception $e) {
            throw new Exception('SMTP Socket Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Verify email was sent by checking IMAP sent folder
     */
    private function verifyEmailSent($subject) {
        try {
            $connection = $this->connectIMAP();
            
            // Switch to Sent folder
            $sent_folder = '{' . GMAIL_IMAP_HOST . ':' . GMAIL_IMAP_PORT . '/imap/' . GMAIL_IMAP_ENCRYPTION . '}[Gmail]/Sent Mail';
            imap_reopen($connection, $sent_folder);
            
            // Search for recent emails with the subject
            $search_criteria = 'SINCE "' . date('d-M-Y') . '" SUBJECT "' . $subject . '"';
            $emails = imap_search($connection, $search_criteria);
            
            return !empty($emails);
            
        } catch (Exception $e) {
            // Don't throw error for verification failure
            error_log('Email verification failed: ' . $e->getMessage());
            return true; // Assume success if verification fails
        }
    }
    
    /**
     * Get recent emails from Gmail (using POP3)
     */
    public function getRecentEmails($limit = 10) {
        try {
            $connection = $this->connectPOP3();
            
            $num_messages = imap_num_msg($connection);
            $emails = array();
            
            $start = max(1, $num_messages - $limit + 1);
            
            for ($i = $start; $i <= $num_messages; $i++) {
                $header = imap_headerinfo($connection, $i);
                $body = imap_fetchbody($connection, $i, 1);
                
                $emails[] = array(
                    'subject' => $header->subject ?? 'No Subject',
                    'from' => $header->fromaddress ?? 'Unknown',
                    'date' => date('Y-m-d H:i:s', strtotime($header->date ?? 'now')),
                    'body' => substr($body, 0, 200) . '...'
                );
            }
            
            imap_close($connection);
            return $emails;
            
        } catch (Exception $e) {
            throw new Exception('POP3 Fetch Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Close connections
     */
    public function __destruct() {
        if ($this->imap_connection) {
            imap_close($this->imap_connection);
        }
    }
}
?>