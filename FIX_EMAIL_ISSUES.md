# 🔧 How to Fix Email Sending Issues

## The Problem
Your contact form emails aren't being sent because of configuration issues. Here's how to fix it:

## 🚨 Main Issues Identified

1. **Default placeholder email addresses** - You're using `your-email@example.com` and `noreply@yourdomain.com`
2. **No real email configuration** - The system needs your actual email addresses

## ✅ Quick Fix (3 Steps)

### Step 1: Configure Your Email Settings
Open `setup_config.php` in your browser and enter:
- **Your Email Address**: Where you want to receive contact form submissions
- **Sender Email**: An email from your domain (e.g., `noreply@yourdomain.com`)
- **Website Name**: Your website's name
- **Website URL**: Your website's URL

### Step 2: Test Email Sending
After configuring, click the test links to send a test email and verify it works.

### Step 3: Try Your Contact Form
Visit `contact.html` and submit a test message to see if it reaches your email.

## 🛠️ Manual Configuration (Alternative)

If you prefer to edit files manually:

1. **Edit `config.php`** and change these lines:
   ```php
   define('RECIPIENT_EMAIL', 'your-actual-email@gmail.com'); // Your real email
   define('SENDER_EMAIL', 'contact@yourdomain.com');        // Your domain email
   define('WEBSITE_NAME', 'Your Website Name');             // Your site name
   define('WEBSITE_URL', 'https://yourwebsite.com');        // Your site URL
   ```

2. **Save the file** and test again

## 🔍 Debugging Tools Available

1. **Setup Configuration**: `setup_config.php` - Easy configuration interface
2. **Email Debug Tool**: `debug_email.php` - Comprehensive email testing
3. **System Test**: `test_setup.php` - Check server capabilities

## 📧 Common Email Issues & Solutions

### Issue: "Emails not reaching inbox"
**Solutions:**
- Check spam/junk folders
- Use a domain email for sender (not gmail/yahoo)
- Verify your hosting provider allows email sending

### Issue: "mail() function returns false"
**Solutions:**
- Check if sendmail is installed: `which sendmail`
- Verify PHP mail configuration: `php -i | grep mail`
- Contact your hosting provider

### Issue: "Emails go to spam"
**Solutions:**
- Set up SPF records for your domain
- Use your domain email as sender
- Add proper email headers (already included)

## 🚀 Production Deployment Tips

1. **Use SMTP instead of mail()** for better reliability:
   ```php
   define('USE_SMTP', true);
   define('SMTP_HOST', 'smtp.gmail.com');
   define('SMTP_USERNAME', 'your@gmail.com');
   define('SMTP_PASSWORD', 'your-app-password');
   ```

2. **Set up domain authentication**:
   - SPF record: `v=spf1 include:_spf.google.com ~all`
   - DKIM signing (through your email provider)
   - DMARC policy

3. **Use professional email services**:
   - Google Workspace
   - Microsoft 365
   - SendGrid
   - Mailgun
   - Amazon SES

## 🔐 Security Checklist

- ✅ Input sanitization (already implemented)
- ✅ Email validation (already implemented)
- ✅ CSRF protection ready
- ✅ Rate limiting available
- ✅ Logging system included

## 📝 Testing Steps

1. **Open** `setup_config.php` in your browser
2. **Enter** your real email addresses
3. **Save** the configuration
4. **Click** "Send a test email" link
5. **Check** your email inbox (and spam folder)
6. **Test** the contact form at `contact.html`

## 🆘 Still Not Working?

If emails still aren't sending after configuration:

1. **Check server requirements**:
   - PHP mail() function enabled
   - Sendmail or SMTP server configured
   - Firewall allows email ports (25, 587, 465)

2. **Hosting provider issues**:
   - Some hosts disable mail() function
   - Contact support to enable email sending
   - Ask about SMTP server details

3. **Try SMTP instead**:
   - Install PHPMailer: `composer require phpmailer/phpmailer`
   - Configure SMTP settings in `config.php`
   - Use a reliable email service

4. **Alternative solutions**:
   - Use third-party services (Formspree, Netlify Forms)
   - Set up email forwarding through your domain
   - Use JavaScript with email APIs

## 📞 Need Help?

If you're still having issues:

1. Run `debug_email.php` and share the results
2. Check your hosting provider's documentation
3. Look at server error logs
4. Consider hiring a developer for SMTP setup

---

**Remember**: Remove setup and debug files (`setup_config.php`, `debug_email.php`, `test_setup.php`) from production servers for security!