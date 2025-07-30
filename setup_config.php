<?php
/**
 * Configuration Setup Tool
 * 
 * This tool helps you set up your email configuration easily.
 */

// Handle form submission
if ($_POST) {
    $recipient_email = trim($_POST['recipient_email'] ?? '');
    $sender_email = trim($_POST['sender_email'] ?? '');
    $website_name = trim($_POST['website_name'] ?? '');
    $website_url = trim($_POST['website_url'] ?? '');
    
    $errors = [];
    
    // Validate inputs
    if (!filter_var($recipient_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid recipient email address';
    }
    
    if (!filter_var($sender_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid sender email address';
    }
    
    if (empty($website_name)) {
        $errors[] = 'Please enter your website name';
    }
    
    if (empty($website_url)) {
        $errors[] = 'Please enter your website URL';
    }
    
    if (empty($errors)) {
        // Read the current config file
        $config_content = file_get_contents('config.php');
        
        // Replace the placeholder values
        $config_content = str_replace(
            "define('RECIPIENT_EMAIL', 'your-email@example.com');",
            "define('RECIPIENT_EMAIL', '" . addslashes($recipient_email) . "');",
            $config_content
        );
        
        $config_content = str_replace(
            "define('SENDER_EMAIL', 'noreply@yourdomain.com');",
            "define('SENDER_EMAIL', '" . addslashes($sender_email) . "');",
            $config_content
        );
        
        $config_content = str_replace(
            "define('WEBSITE_NAME', 'Your Website');",
            "define('WEBSITE_NAME', '" . addslashes($website_name) . "');",
            $config_content
        );
        
        $config_content = str_replace(
            "define('WEBSITE_URL', 'https://yourwebsite.com');",
            "define('WEBSITE_URL', '" . addslashes($website_url) . "');",
            $config_content
        );
        
        // Write the updated config file
        if (file_put_contents('config.php', $config_content)) {
            $success = true;
        } else {
            $errors[] = 'Could not write to config.php file. Please check file permissions.';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form Configuration Setup</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h1 { color: #333; text-align: center; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        input[type="email"], input[type="text"], input[type="url"] { 
            width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 5px; 
            font-size: 16px; transition: border-color 0.3s;
        }
        input:focus { outline: none; border-color: #007bff; }
        .btn { 
            background: #007bff; color: white; padding: 15px 30px; border: none; 
            border-radius: 5px; font-size: 16px; cursor: pointer; width: 100%;
        }
        .btn:hover { background: #0056b3; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .help-text { font-size: 14px; color: #666; margin-top: 5px; }
        .next-steps { background: #e7f3ff; padding: 20px; border-radius: 5px; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 Email Configuration Setup</h1>
        
        <?php if (isset($success) && $success): ?>
            <div class="success">
                ✅ Configuration updated successfully! Your contact form is now ready to use.
            </div>
            
            <div class="next-steps">
                <h3>🚀 Next Steps:</h3>
                <ol>
                    <li><a href="debug_email.php?test=simple" target="_blank">Send a test email</a> to verify it's working</li>
                    <li><a href="contact.html" target="_blank">Try your contact form</a></li>
                    <li>Check your email inbox (and spam folder)</li>
                    <li>Remove this setup file from your server when you're done</li>
                </ol>
            </div>
        <?php else: ?>
            
            <?php if (!empty($errors)): ?>
                <div class="error">
                    ❌ Please fix the following errors:<br>
                    • <?php echo implode('<br>• ', $errors); ?>
                </div>
            <?php endif; ?>
            
            <p>Enter your email configuration details below to set up your contact form:</p>
            
            <form method="POST">
                <div class="form-group">
                    <label for="recipient_email">Your Email Address (where form submissions will be sent):</label>
                    <input type="email" id="recipient_email" name="recipient_email" 
                           value="<?php echo htmlspecialchars($_POST['recipient_email'] ?? ''); ?>" 
                           placeholder="your@email.com" required>
                    <div class="help-text">This is where you'll receive contact form submissions</div>
                </div>
                
                <div class="form-group">
                    <label for="sender_email">Sender Email Address (appears in the "From" field):</label>
                    <input type="email" id="sender_email" name="sender_email" 
                           value="<?php echo htmlspecialchars($_POST['sender_email'] ?? ''); ?>" 
                           placeholder="noreply@yourdomain.com" required>
                    <div class="help-text">Use your domain email for better deliverability (e.g., noreply@yourdomain.com)</div>
                </div>
                
                <div class="form-group">
                    <label for="website_name">Website Name:</label>
                    <input type="text" id="website_name" name="website_name" 
                           value="<?php echo htmlspecialchars($_POST['website_name'] ?? ''); ?>" 
                           placeholder="My Awesome Website" required>
                    <div class="help-text">This appears in the email templates</div>
                </div>
                
                <div class="form-group">
                    <label for="website_url">Website URL:</label>
                    <input type="url" id="website_url" name="website_url" 
                           value="<?php echo htmlspecialchars($_POST['website_url'] ?? ''); ?>" 
                           placeholder="https://yourwebsite.com" required>
                    <div class="help-text">Full URL to your website</div>
                </div>
                
                <button type="submit" class="btn">💾 Save Configuration</button>
            </form>
            
            <div class="next-steps">
                <h3>💡 Tips for better email delivery:</h3>
                <ul>
                    <li>Use an email address from your own domain for the sender email</li>
                    <li>Make sure your hosting provider allows email sending</li>
                    <li>Consider setting up SPF, DKIM, and DMARC records for your domain</li>
                    <li>Test with different email providers (Gmail, Yahoo, Outlook)</li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>