<?php
/**
 * Setup Script for Gmail Contact Form
 * 
 * This script helps configure Gmail credentials and test the system
 */

// Define setup mode to bypass config validation
define('SETUP_MODE', true);

$step = $_GET['step'] ?? 1;
$success_message = '';
$error_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($_POST['action']) {
        case 'save_config':
            $result = saveConfiguration($_POST);
            if ($result['success']) {
                $success_message = $result['message'];
                $step = 3; // Move to testing step
            } else {
                $error_message = $result['message'];
            }
            break;
            
        case 'test_connection':
            $result = testConnection();
            if ($result['success']) {
                $success_message = $result['message'];
            } else {
                $error_message = $result['message'];
            }
            break;
            
        case 'test_email':
            $result = sendTestEmail($_POST['test_email']);
            if ($result['success']) {
                $success_message = $result['message'];
            } else {
                $error_message = $result['message'];
            }
            break;
    }
}

function saveConfiguration($data) {
    try {
        $config_content = file_get_contents('config.php');
        
        // Replace configuration values
        $replacements = [
            "define('GMAIL_USERNAME', 'your-email@gmail.com');" => "define('GMAIL_USERNAME', '" . addslashes($data['gmail_username']) . "');",
            "define('GMAIL_PASSWORD', 'your-app-password');" => "define('GMAIL_PASSWORD', '" . addslashes($data['gmail_password']) . "');",
            "define('GMAIL_RECIPIENT', 'your-email@gmail.com');" => "define('GMAIL_RECIPIENT', '" . addslashes($data['gmail_recipient']) . "');",
        ];
        
        foreach ($replacements as $search => $replace) {
            $config_content = str_replace($search, $replace, $config_content);
        }
        
        // Backup original config
        if (!file_exists('config.php.backup')) {
            copy('config.php', 'config.php.backup');
        }
        
        file_put_contents('config.php', $config_content);
        
        return ['success' => true, 'message' => 'Configuration saved successfully!'];
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error saving configuration: ' . $e->getMessage()];
    }
}

function testConnection() {
    try {
        require_once 'config.php';
        require_once 'EmailSender.php';
        
        $emailSender = new EmailSender();
        
        // Test IMAP connection
        $reflection = new ReflectionClass($emailSender);
        $method = $reflection->getMethod('connectIMAP');
        $method->setAccessible(true);
        $connection = $method->invoke($emailSender);
        
        if ($connection) {
            imap_close($connection);
            return ['success' => true, 'message' => 'Successfully connected to Gmail IMAP!'];
        } else {
            return ['success' => false, 'message' => 'Failed to connect to Gmail IMAP.'];
        }
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Connection test failed: ' . $e->getMessage()];
    }
}

function sendTestEmail($recipient) {
    try {
        require_once 'config.php';
        require_once 'EmailSender.php';
        
        $emailSender = new EmailSender();
        
        $result = $emailSender->sendToGmail(
            $recipient,
            'Test Email from Contact Form Setup',
            'This is a test email sent during the setup process. If you receive this email, your configuration is working correctly!',
            GMAIL_USERNAME,
            'Setup Test'
        );
        
        return $result;
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Test email failed: ' . $e->getMessage()];
    }
}

function checkRequirements() {
    $requirements = [
        'PHP Version >= 7.0' => version_compare(PHP_VERSION, '7.0.0', '>='),
        'IMAP Extension' => extension_loaded('imap'),
        'OpenSSL Extension' => extension_loaded('openssl'),
        'MBString Extension' => extension_loaded('mbstring'),
        'Config File Writable' => is_writable('config.php'),
        'Logs Directory Writable' => is_writable('logs') || mkdir('logs', 0755, true),
        'Uploads Directory Writable' => is_writable('uploads') || mkdir('uploads', 0755, true),
    ];
    
    return $requirements;
}

$requirements = checkRequirements();
$all_requirements_met = !in_array(false, $requirements);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gmail Contact Form Setup</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; line-height: 1.6; }
        
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #2c3e50; margin-bottom: 10px; }
        .header p { color: #666; }
        
        .step-indicator { display: flex; justify-content: center; margin-bottom: 30px; }
        .step { padding: 10px 20px; margin: 0 5px; border-radius: 25px; background: #ecf0f1; color: #7f8c8d; }
        .step.active { background: #3498db; color: white; }
        .step.completed { background: #27ae60; color: white; }
        
        .card { background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .card-header { padding: 20px; border-bottom: 1px solid #eee; }
        .card-header h2 { color: #2c3e50; }
        .card-body { padding: 20px; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #2c3e50; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px; border: 2px solid #e1e5e9; border-radius: 5px; font-size: 14px; }
        .form-group input:focus, .form-group textarea:focus { border-color: #3498db; outline: none; }
        .form-group small { color: #666; }
        
        .btn { display: inline-block; padding: 12px 24px; background: #3498db; color: white; text-decoration: none; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; }
        .btn:hover { background: #2980b9; }
        .btn-success { background: #27ae60; }
        .btn-success:hover { background: #219a52; }
        .btn-warning { background: #f39c12; }
        .btn-warning:hover { background: #e67e22; }
        
        .alert { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        
        .requirement { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #eee; }
        .requirement:last-child { border-bottom: none; }
        .requirement .status { font-weight: bold; }
        .requirement .status.pass { color: #27ae60; }
        .requirement .status.fail { color: #e74c3c; }
        
        .navigation { text-align: center; margin-top: 30px; }
        .navigation .btn { margin: 0 10px; }
        
        @media (max-width: 768px) {
            .container { padding: 10px; }
            .step-indicator { flex-wrap: wrap; }
            .step { margin: 5px; }
            .navigation .btn { display: block; margin: 10px 0; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Gmail Contact Form Setup</h1>
            <p>Configure your Gmail settings to start receiving contact form submissions</p>
        </div>
        
        <div class="step-indicator">
            <div class="step <?php echo $step >= 1 ? 'active' : ''; ?>">1. Requirements</div>
            <div class="step <?php echo $step >= 2 ? 'active' : ''; ?>">2. Configuration</div>
            <div class="step <?php echo $step >= 3 ? 'active' : ''; ?>">3. Testing</div>
            <div class="step <?php echo $step >= 4 ? 'active' : ''; ?>">4. Complete</div>
        </div>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <?php if ($step == 1): ?>
            <!-- Step 1: Requirements Check -->
            <div class="card">
                <div class="card-header">
                    <h2>Step 1: System Requirements</h2>
                </div>
                <div class="card-body">
                    <?php foreach ($requirements as $req => $status): ?>
                        <div class="requirement">
                            <span><?php echo $req; ?></span>
                            <span class="status <?php echo $status ? 'pass' : 'fail'; ?>">
                                <?php echo $status ? '✅ Pass' : '❌ Fail'; ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php if (!$all_requirements_met): ?>
                        <div class="alert alert-warning">
                            Some requirements are not met. Please ensure all extensions are installed and directories are writable.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="navigation">
                <?php if ($all_requirements_met): ?>
                    <a href="?step=2" class="btn">Next: Configuration</a>
                <?php else: ?>
                    <button class="btn" disabled>Fix Requirements First</button>
                <?php endif; ?>
            </div>
            
        <?php elseif ($step == 2): ?>
            <!-- Step 2: Gmail Configuration -->
            <div class="card">
                <div class="card-header">
                    <h2>Step 2: Gmail Configuration</h2>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <strong>Important:</strong> Before proceeding, make sure you have:
                        <ul style="margin-top: 10px; margin-left: 20px;">
                            <li>Enabled 2-factor authentication on your Gmail account</li>
                            <li>Generated an App Password for this application</li>
                            <li>Enabled IMAP access in your Gmail settings</li>
                        </ul>
                    </div>
                    
                    <form method="POST">
                        <input type="hidden" name="action" value="save_config">
                        
                        <div class="form-group">
                            <label for="gmail_username">Gmail Username (Email Address)</label>
                            <input type="email" id="gmail_username" name="gmail_username" required>
                            <small>Your Gmail email address</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="gmail_password">Gmail App Password</label>
                            <input type="password" id="gmail_password" name="gmail_password" required>
                            <small>Use an App Password, NOT your regular Gmail password</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="gmail_recipient">Recipient Email</label>
                            <input type="email" id="gmail_recipient" name="gmail_recipient" required>
                            <small>Where contact form submissions will be sent (usually the same as username)</small>
                        </div>
                        
                        <button type="submit" class="btn">Save Configuration</button>
                    </form>
                </div>
            </div>
            
            <div class="navigation">
                <a href="?step=1" class="btn">Previous</a>
            </div>
            
        <?php elseif ($step == 3): ?>
            <!-- Step 3: Testing -->
            <div class="card">
                <div class="card-header">
                    <h2>Step 3: Test Your Configuration</h2>
                </div>
                <div class="card-body">
                    <p>Now let's test if your Gmail configuration is working correctly.</p>
                    
                    <!-- Test Connection -->
                    <div style="margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px;">
                        <h3>Test IMAP Connection</h3>
                        <p>This will test if we can connect to your Gmail account using IMAP.</p>
                        <form method="POST" style="margin-top: 10px;">
                            <input type="hidden" name="action" value="test_connection">
                            <button type="submit" class="btn">Test Connection</button>
                        </form>
                    </div>
                    
                    <!-- Test Email -->
                    <div style="margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px;">
                        <h3>Send Test Email</h3>
                        <p>This will send a test email to verify that the system can send emails successfully.</p>
                        <form method="POST" style="margin-top: 10px;">
                            <input type="hidden" name="action" value="test_email">
                            <div class="form-group">
                                <label for="test_email">Test Email Address</label>
                                <input type="email" id="test_email" name="test_email" required>
                                <small>Enter an email address to receive the test email</small>
                            </div>
                            <button type="submit" class="btn btn-success">Send Test Email</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="navigation">
                <a href="?step=2" class="btn">Previous</a>
                <a href="?step=4" class="btn btn-success">Next: Complete</a>
            </div>
            
        <?php else: ?>
            <!-- Step 4: Complete -->
            <div class="card">
                <div class="card-header">
                    <h2>Setup Complete!</h2>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <strong>Congratulations!</strong> Your Gmail contact form is now configured and ready to use.
                    </div>
                    
                    <h3>What's Next?</h3>
                    <ul style="margin: 15px 0 15px 20px;">
                        <li>Test the contact form: <a href="contact-form.html" target="_blank">contact-form.html</a></li>
                        <li>Access the admin dashboard: <a href="admin.php" target="_blank">admin.php</a></li>
                        <li>Customize the form design by editing <code>css/contact-form.css</code></li>
                        <li>Modify email templates in <code>config.php</code></li>
                    </ul>
                    
                    <h3>Security Recommendations:</h3>
                    <ul style="margin: 15px 0 15px 20px;">
                        <li>Change the admin password in <code>admin.php</code></li>
                        <li>Set <code>DEBUG_MODE</code> to <code>false</code> in <code>config.php</code> for production</li>
                        <li>Consider implementing additional security measures like CAPTCHA</li>
                        <li>Regularly monitor the logs for suspicious activity</li>
                    </ul>
                </div>
            </div>
            
            <div class="navigation">
                <a href="contact-form.html" class="btn btn-success" target="_blank">Test Contact Form</a>
                <a href="admin.php" class="btn" target="_blank">Open Admin Dashboard</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>