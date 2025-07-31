<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gmail Contact Form System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; padding: 40px; }
        .header { text-align: center; color: white; margin-bottom: 40px; }
        .header h1 { font-size: 3rem; margin-bottom: 10px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
        .header p { font-size: 1.2rem; opacity: 0.9; }
        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .card { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); transition: transform 0.3s ease; }
        .card:hover { transform: translateY(-5px); }
        .card h2 { color: #2c3e50; margin-bottom: 15px; }
        .card p { color: #666; margin-bottom: 20px; line-height: 1.6; }
        .btn { display: inline-block; padding: 12px 24px; background: #3498db; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; transition: background 0.3s ease; }
        .btn:hover { background: #2980b9; }
        .btn-primary { background: #667eea; }
        .btn-primary:hover { background: #5a67d8; }
        .btn-success { background: #27ae60; }
        .btn-success:hover { background: #219a52; }
        .btn-warning { background: #f39c12; }
        .btn-warning:hover { background: #e67e22; }
        .status { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .status h3 { color: #2c3e50; margin-bottom: 15px; }
        .feature { display: flex; align-items: center; margin-bottom: 10px; }
        .feature::before { content: '✅'; margin-right: 10px; font-size: 1.2rem; }
        @media (max-width: 768px) {
            .header h1 { font-size: 2rem; }
            .container { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Gmail Contact Form</h1>
            <p>Professional contact form system with PHP, IMAP, and POP3 integration</p>
        </div>
        
        <div class="cards">
            <div class="card">
                <h2>🚀 Setup</h2>
                <p>Get started by configuring your Gmail credentials and testing the system. The setup wizard will guide you through the process step by step.</p>
                <a href="setup.php" class="btn btn-primary">Start Setup</a>
            </div>
            
            <div class="card">
                <h2>📝 Contact Form</h2>
                <p>Test the contact form functionality. Users can submit messages with attachments, and you'll receive them directly in your Gmail inbox.</p>
                <a href="contact-form.html" class="btn btn-success">View Contact Form</a>
            </div>
            
            <div class="card">
                <h2>📊 Admin Dashboard</h2>
                <p>Monitor form submissions, view statistics, check recent emails, and manage the system. Includes comprehensive logging and testing tools.</p>
                <a href="admin.php" class="btn btn-warning">Open Dashboard</a>
            </div>
        </div>
        
        <div class="status">
            <h3>System Features</h3>
            <div class="feature">Direct Gmail integration using IMAP, POP3, and SMTP</div>
            <div class="feature">File attachments with security validation</div>
            <div class="feature">Responsive design for all devices</div>
            <div class="feature">Rate limiting and spam protection</div>
            <div class="feature">Automatic reply emails to users</div>
            <div class="feature">Comprehensive logging and monitoring</div>
            <div class="feature">Admin dashboard with statistics</div>
            <div class="feature">Easy setup wizard for configuration</div>
        </div>
    </div>
</body>
</html>