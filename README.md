# Gmail Contact Form with PHP, IMAP, and POP3

A complete contact form solution that sends submissions directly to Gmail using IMAP, POP3, and SMTP protocols. Features a modern responsive design, file attachments, admin dashboard, and comprehensive logging.

## Features

- ✅ **Direct Gmail Integration** - Sends emails directly to your Gmail account
- ✅ **IMAP & POP3 Support** - Uses both protocols for reliable email handling
- ✅ **File Attachments** - Support for PDF, DOC, images, and text files
- ✅ **Responsive Design** - Modern, mobile-friendly interface
- ✅ **Admin Dashboard** - View submissions, logs, and system status
- ✅ **Rate Limiting** - Prevents spam and abuse
- ✅ **Auto-Reply** - Automatic confirmation emails to users
- ✅ **Comprehensive Logging** - Track all submissions and errors
- ✅ **Security Features** - Input validation, file type checking, and more
- ✅ **Easy Setup** - Step-by-step configuration wizard

## Prerequisites

Before installing, ensure your server meets these requirements:

- **PHP 7.0 or higher**
- **IMAP extension** (`php-imap`)
- **OpenSSL extension** (`php-openssl`)
- **MBString extension** (`php-mbstring`)
- **Web server** (Apache, Nginx, etc.)
- **Gmail account** with 2-factor authentication enabled

## Quick Start

### 1. Download and Extract

Download all files to your web server directory.

### 2. Set Up Gmail

1. **Enable 2-Factor Authentication** on your Gmail account
2. **Generate App Password**:
   - Go to Google Account settings
   - Security → 2-Step Verification → App passwords
   - Generate a new app password for "Mail"
3. **Enable IMAP/POP3**:
   - Gmail Settings → Forwarding and POP/IMAP
   - Enable IMAP access
   - Enable POP access (optional)

### 3. Run Setup Wizard

1. Open `setup.php` in your web browser
2. Follow the 4-step setup process:
   - Check system requirements
   - Configure Gmail credentials
   - Test connections
   - Complete setup

### 4. Test the Form

1. Open `contact-form.html` to test the form
2. Access `admin.php` to view submissions (password: `admin123`)

## File Structure

```
├── contact-form.html      # Main contact form
├── process-form.php       # Form processing script
├── config.php            # Configuration file
├── EmailSender.php       # Email handling class
├── admin.php             # Admin dashboard
├── setup.php             # Setup wizard
├── css/
│   └── contact-form.css  # Form styling
├── uploads/              # File upload directory
├── logs/                 # Log files
└── README.md            # This file
```

## Configuration

### Gmail Settings

Edit `config.php` to configure your Gmail account:

```php
// Gmail Account Settings
define('GMAIL_USERNAME', 'your-email@gmail.com');
define('GMAIL_PASSWORD', 'your-app-password');
define('GMAIL_RECIPIENT', 'your-email@gmail.com');
```

### Application Settings

Customize various aspects of the application:

```php
// Security Settings
define('MAX_ATTEMPTS_PER_HOUR', 5);
define('ENABLE_RATE_LIMITING', true);

// File Upload Settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png']);

// Email Templates
define('AUTO_REPLY_SUBJECT', 'Thank you for contacting us!');
```

## How It Works

### Form Submission Process

1. **User fills out form** with name, email, message, etc.
2. **Client-side validation** ensures required fields are complete
3. **File upload handling** (if attachment provided)
4. **Server-side processing**:
   - Input sanitization and validation
   - Rate limiting check
   - File type and size validation
5. **Email sending**:
   - Compose email with form data
   - Send via SMTP to Gmail
   - Verify delivery via IMAP
6. **Auto-reply** sent to user
7. **Logging** of submission details
8. **JSON response** sent back to form

### Email Protocols Used

- **SMTP** - For sending emails to Gmail
- **IMAP** - For verifying sent emails and accessing mailbox
- **POP3** - For retrieving recent emails in admin dashboard

## Admin Dashboard

Access the admin dashboard at `admin.php` to:

- View submission statistics
- Browse recent form submissions
- Check recent emails via POP3
- Monitor system status
- Send test emails
- Clear logs

**Default admin password: `admin123`** (change this in `admin.php`)

## Security Features

### Input Validation
- Email format validation
- Required field checking
- HTML entity encoding
- SQL injection prevention

### File Upload Security
- File type restriction
- File size limits
- Unique filename generation
- Temporary file cleanup

### Rate Limiting
- IP-based submission limits
- Configurable time windows
- Automatic cleanup of old records

### Data Protection
- Session-based admin authentication
- Secure password handling
- Error message sanitization

## Customization

### Styling the Form

Edit `css/contact-form.css` to customize the appearance:

```css
/* Change color scheme */
:root {
    --primary-color: #667eea;
    --secondary-color: #764ba2;
}

/* Modify form layout */
.form-wrapper {
    max-width: 600px;
    padding: 30px;
}
```

### Email Templates

Modify email templates in `config.php`:

```php
define('AUTO_REPLY_BODY', '
Dear {name},

Thank you for your message. We will respond within 24 hours.

Best regards,
Your Team
');
```

### Adding Form Fields

1. Add HTML input to `contact-form.html`
2. Update `process-form.php` to handle new field
3. Modify email template to include new data

## Troubleshooting

### Common Issues

**"Configuration Error" message:**
- Ensure Gmail credentials are set in `config.php`
- Use App Password, not regular password
- Check that 2FA is enabled on Gmail account

**"Missing required PHP extensions":**
- Install `php-imap`: `sudo apt-get install php-imap`
- Install `php-openssl`: `sudo apt-get install php-openssl`
- Install `php-mbstring`: `sudo apt-get install php-mbstring`
- Restart web server after installation

**Emails not sending:**
- Verify Gmail App Password is correct
- Check that IMAP is enabled in Gmail settings
- Review error logs in `logs/submissions.log`
- Test connection using setup wizard

**File uploads not working:**
- Ensure `uploads/` directory is writable
- Check PHP upload limits in `php.ini`
- Verify file types are allowed in config

### Debug Mode

Enable debug mode in `config.php` for detailed error messages:

```php
define('DEBUG_MODE', true);
```

**Note:** Disable debug mode in production environments.

### Log Files

Check these log files for troubleshooting:

- `logs/submissions.log` - Form submission logs
- `logs/rate_limit.json` - Rate limiting data
- Web server error logs

## Production Deployment

### Security Checklist

- [ ] Change admin password in `admin.php`
- [ ] Set `DEBUG_MODE` to `false` in `config.php`
- [ ] Ensure proper file permissions (644 for files, 755 for directories)
- [ ] Consider adding CAPTCHA for additional spam protection
- [ ] Set up SSL/HTTPS for encrypted data transmission
- [ ] Regularly monitor logs for suspicious activity
- [ ] Backup configuration files

### Performance Optimization

- Enable PHP OPcache
- Configure web server caching
- Optimize image files
- Consider CDN for static assets
- Regular log file rotation

## API Reference

### POST /process-form.php

Processes contact form submissions.

**Parameters:**
- `name` (required) - Full name
- `email` (required) - Email address
- `subject` (required) - Message subject
- `message` (required) - Message content
- `phone` (optional) - Phone number
- `priority` (optional) - Priority level (normal, high, urgent)
- `subscribe` (optional) - Newsletter subscription
- `attachment` (optional) - File attachment

**Response:**
```json
{
    "success": true,
    "message": "Your message has been sent successfully!"
}
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is open source and available under the [MIT License](LICENSE).

## Support

For support and questions:

1. Check the troubleshooting section
2. Review log files for errors
3. Test with the setup wizard
4. Ensure all requirements are met

## Changelog

### Version 1.0.0
- Initial release
- Gmail IMAP/POP3 integration
- Contact form with file uploads
- Admin dashboard
- Setup wizard
- Comprehensive logging
- Rate limiting
- Auto-reply functionality

---

**Made with ❤️ for seamless Gmail integration**