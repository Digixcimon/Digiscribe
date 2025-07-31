<?php
session_start();

// Simple authentication (replace with proper authentication in production)
$admin_password = 'admin123'; // Change this password!

if (isset($_POST['login'])) {
    if ($_POST['password'] === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $error = 'Invalid password';
    }
}

if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

$is_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'];

if (!$is_logged_in) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Admin Login</title>
        <style>
            body { font-family: Arial, sans-serif; background: #f5f5f5; }
            .login-form { max-width: 400px; margin: 100px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
            input[type="password"] { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
            button { width: 100%; padding: 12px; background: #007cba; color: white; border: none; border-radius: 5px; cursor: pointer; }
            .error { color: red; margin-top: 10px; }
        </style>
    </head>
    <body>
        <div class="login-form">
            <h2>Admin Login</h2>
            <form method="POST">
                <input type="password" name="password" placeholder="Enter admin password" required>
                <button type="submit" name="login">Login</button>
                <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Include required files
require_once 'config.php';
require_once 'EmailSender.php';

// Handle actions
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'clear_logs':
            if (file_exists('logs/submissions.log')) {
                file_put_contents('logs/submissions.log', '');
            }
            header('Location: admin.php?msg=Logs cleared');
            exit;
            
        case 'test_email':
            try {
                $emailSender = new EmailSender();
                $result = $emailSender->sendToGmail(
                    GMAIL_RECIPIENT,
                    'Test Email from Contact Form',
                    'This is a test email to verify the system is working correctly.',
                    GMAIL_USERNAME,
                    'System Test'
                );
                
                if ($result['success']) {
                    header('Location: admin.php?msg=Test email sent successfully');
                } else {
                    header('Location: admin.php?error=' . urlencode($result['message']));
                }
            } catch (Exception $e) {
                header('Location: admin.php?error=' . urlencode($e->getMessage()));
            }
            exit;
    }
}

// Get statistics
function getStats() {
    $stats = array(
        'total_submissions' => 0,
        'successful_submissions' => 0,
        'failed_submissions' => 0,
        'today_submissions' => 0
    );
    
    if (file_exists('logs/submissions.log')) {
        $lines = file('logs/submissions.log', FILE_IGNORE_NEW_LINES);
        $today = date('Y-m-d');
        
        foreach ($lines as $line) {
            if (empty($line)) continue;
            
            $entry = json_decode($line, true);
            if ($entry) {
                $stats['total_submissions']++;
                
                if ($entry['success']) {
                    $stats['successful_submissions']++;
                } else {
                    $stats['failed_submissions']++;
                }
                
                if (strpos($entry['timestamp'], $today) === 0) {
                    $stats['today_submissions']++;
                }
            }
        }
    }
    
    return $stats;
}

// Get recent submissions
function getRecentSubmissions($limit = 20) {
    $submissions = array();
    
    if (file_exists('logs/submissions.log')) {
        $lines = file('logs/submissions.log', FILE_IGNORE_NEW_LINES);
        $lines = array_reverse($lines); // Show newest first
        
        $count = 0;
        foreach ($lines as $line) {
            if (empty($line) || $count >= $limit) continue;
            
            $entry = json_decode($line, true);
            if ($entry) {
                $submissions[] = $entry;
                $count++;
            }
        }
    }
    
    return $submissions;
}

// Get recent emails from Gmail
function getRecentEmails() {
    try {
        $emailSender = new EmailSender();
        return $emailSender->getRecentEmails(10);
    } catch (Exception $e) {
        return array('error' => $e->getMessage());
    }
}

$stats = getStats();
$recent_submissions = getRecentSubmissions();
$recent_emails = getRecentEmails();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Contact Form Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; }
        
        .header { background: #2c3e50; color: white; padding: 20px; }
        .header h1 { margin-bottom: 10px; }
        .header .actions { float: right; }
        .header .actions a, .header .actions form { display: inline-block; margin-left: 10px; }
        .header .actions a { color: white; text-decoration: none; padding: 8px 15px; background: #3498db; border-radius: 5px; }
        .header .actions button { padding: 8px 15px; background: #e74c3c; color: white; border: none; border-radius: 5px; cursor: pointer; }
        
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
        .stat-card h3 { font-size: 2em; margin-bottom: 10px; }
        .stat-card p { color: #666; }
        .stat-success h3 { color: #27ae60; }
        .stat-error h3 { color: #e74c3c; }
        .stat-total h3 { color: #3498db; }
        .stat-today h3 { color: #f39c12; }
        
        .section { background: white; margin-bottom: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .section-header { padding: 20px; border-bottom: 1px solid #eee; }
        .section-header h2 { color: #2c3e50; }
        .section-content { padding: 20px; }
        
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        .table th { background: #f8f9fa; font-weight: 600; }
        
        .status-success { color: #27ae60; font-weight: bold; }
        .status-error { color: #e74c3c; font-weight: bold; }
        
        .message { padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        .btn { display: inline-block; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
        .btn:hover { background: #2980b9; }
        .btn-danger { background: #e74c3c; }
        .btn-danger:hover { background: #c0392b; }
        
        .clearfix::after { content: ""; display: table; clear: both; }
        
        @media (max-width: 768px) {
            .header .actions { float: none; margin-top: 10px; }
            .container { padding: 10px; }
            .table { font-size: 14px; }
            .table th, .table td { padding: 8px; }
        }
    </style>
</head>
<body>
    <div class="header clearfix">
        <h1>Contact Form Admin Dashboard</h1>
        <div class="actions">
            <a href="contact-form.html" target="_blank">View Form</a>
            <a href="?action=test_email">Send Test Email</a>
            <a href="?action=clear_logs" onclick="return confirm('Are you sure you want to clear all logs?')" class="btn-danger">Clear Logs</a>
            <form method="POST" style="display: inline;">
                <button type="submit" name="logout">Logout</button>
            </form>
        </div>
    </div>
    
    <div class="container">
        <?php if (isset($_GET['msg'])): ?>
            <div class="message success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="message error"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>
        
        <!-- Statistics -->
        <div class="stats">
            <div class="stat-card stat-total">
                <h3><?php echo $stats['total_submissions']; ?></h3>
                <p>Total Submissions</p>
            </div>
            <div class="stat-card stat-success">
                <h3><?php echo $stats['successful_submissions']; ?></h3>
                <p>Successful Submissions</p>
            </div>
            <div class="stat-card stat-error">
                <h3><?php echo $stats['failed_submissions']; ?></h3>
                <p>Failed Submissions</p>
            </div>
            <div class="stat-card stat-today">
                <h3><?php echo $stats['today_submissions']; ?></h3>
                <p>Today's Submissions</p>
            </div>
        </div>
        
        <!-- Recent Submissions -->
        <div class="section">
            <div class="section-header">
                <h2>Recent Form Submissions</h2>
            </div>
            <div class="section-content">
                <?php if (empty($recent_submissions)): ?>
                    <p>No submissions found.</p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>IP Address</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_submissions as $submission): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($submission['timestamp']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['name']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['email']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($submission['subject'], 0, 50)) . (strlen($submission['subject']) > 50 ? '...' : ''); ?></td>
                                    <td><?php echo htmlspecialchars($submission['ip']); ?></td>
                                    <td class="<?php echo $submission['success'] ? 'status-success' : 'status-error'; ?>">
                                        <?php echo $submission['success'] ? 'Success' : 'Failed'; ?>
                                        <?php if (!$submission['success'] && !empty($submission['error'])): ?>
                                            <br><small><?php echo htmlspecialchars($submission['error']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Recent Emails -->
        <div class="section">
            <div class="section-header">
                <h2>Recent Emails (via POP3)</h2>
            </div>
            <div class="section-content">
                <?php if (isset($recent_emails['error'])): ?>
                    <p class="message error">Error fetching emails: <?php echo htmlspecialchars($recent_emails['error']); ?></p>
                <?php elseif (empty($recent_emails)): ?>
                    <p>No emails found.</p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>From</th>
                                <th>Subject</th>
                                <th>Preview</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_emails as $email): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($email['date']); ?></td>
                                    <td><?php echo htmlspecialchars($email['from']); ?></td>
                                    <td><?php echo htmlspecialchars($email['subject']); ?></td>
                                    <td><?php echo htmlspecialchars($email['body']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- System Information -->
        <div class="section">
            <div class="section-header">
                <h2>System Information</h2>
            </div>
            <div class="section-content">
                <table class="table">
                    <tr>
                        <td><strong>PHP Version</strong></td>
                        <td><?php echo PHP_VERSION; ?></td>
                    </tr>
                    <tr>
                        <td><strong>IMAP Extension</strong></td>
                        <td><?php echo extension_loaded('imap') ? '✅ Enabled' : '❌ Disabled'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>OpenSSL Extension</strong></td>
                        <td><?php echo extension_loaded('openssl') ? '✅ Enabled' : '❌ Disabled'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>MBString Extension</strong></td>
                        <td><?php echo extension_loaded('mbstring') ? '✅ Enabled' : '❌ Disabled'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Gmail Configuration</strong></td>
                        <td><?php echo (GMAIL_USERNAME !== 'your-email@gmail.com') ? '✅ Configured' : '❌ Not Configured'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Upload Directory</strong></td>
                        <td><?php echo is_writable('uploads') || mkdir('uploads', 0755, true) ? '✅ Writable' : '❌ Not Writable'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Logs Directory</strong></td>
                        <td><?php echo is_writable('logs') || mkdir('logs', 0755, true) ? '✅ Writable' : '❌ Not Writable'; ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>