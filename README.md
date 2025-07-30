# PHP Contact Form with Email Integration

A modern, responsive contact form that sends emails directly using PHP. Features a beautiful UI, form validation, security measures, and comprehensive error handling.

## Features

- ✨ Modern, responsive design with smooth animations
- 📧 Direct email sending via PHP mail() function
- 🔒 Security measures (input sanitization, CSRF protection ready)
- ✅ Client-side and server-side validation
- 📱 Mobile-friendly responsive design
- 🎨 Beautiful UI with loading states and feedback messages
- 🛡️ Anti-spam measures (honeypot field ready)
- 📊 Optional logging and rate limiting
- 🔧 Easy configuration through config file

## Files Structure

```
├── contact.html          # Contact form HTML page
├── send_email.php        # PHP email handler
├── config.php           # Configuration file
├── css/
│   └── contact.css      # Stylesheet for the contact form
└── README.md           # This file
```

## Quick Setup

### 1. Basic Setup

1. **Upload files** to your web server
2. **Edit `config.php`** and update the email settings:
   ```php
   define('RECIPIENT_EMAIL', 'your-email@example.com'); // Your email address
   define('SENDER_EMAIL', 'noreply@yourdomain.com');   // Your domain email
   ```
3. **Open `contact.html`** in your browser to test the form

### 2. Server Requirements

- PHP 5.6 or higher (PHP 7.4+ recommended)
- Web server (Apache, Nginx, etc.)
- PHP mail() function enabled OR SMTP server access

### 3. Email Configuration

#### Option A: Using PHP mail() function (Default)
```php
// In config.php
define('USE_SMTP', false);
define('RECIPIENT_EMAIL', 'your-email@example.com');
```

#### Option B: Using SMTP (Recommended for production)
```php
// In config.php
define('USE_SMTP', true);
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_ENCRYPTION', 'tls');
```

> **Note:** For SMTP functionality, you'll need to install PHPMailer library.

## Configuration Options

### Email Settings
- `RECIPIENT_EMAIL`: Your email address to receive form submissions
- `SENDER_NAME`: Name displayed in the "From" field
- `SENDER_EMAIL`: Email address used as sender

### Security Settings
- `ENABLE_LOGGING`: Log all form submissions
- `ENABLE_RATE_LIMITING`: Limit submissions per IP
- `HONEYPOT_FIELD`: Hidden field name for spam protection

### Validation Settings
- `MIN_MESSAGE_LENGTH`: Minimum message length (default: 10)
- `MAX_MESSAGE_LENGTH`: Maximum message length (default: 5000)

## Customization

### Styling
Edit `css/contact.css` to customize the appearance:
- Colors: Modify CSS variables at the top of the file
- Layout: Adjust container width, padding, and spacing
- Animations: Customize transition effects and animations

### Form Fields
To add or modify form fields:
1. Update the HTML in `contact.html`
2. Update the PHP processing in `send_email.php`
3. Add corresponding CSS styles if needed

### Email Template
The email template is defined in `send_email.php`. You can customize:
- HTML structure
- Styling
- Content layout
- Additional information

## Security Features

### Input Sanitization
All form inputs are sanitized using:
- `trim()`: Remove whitespace
- `stripslashes()`: Remove backslashes
- `htmlspecialchars()`: Convert special characters

### Validation
- Server-side validation for all required fields
- Email format validation
- Message length validation
- Method verification (POST only)

### Anti-Spam Measures
- Honeypot field (hidden from users, attracts bots)
- Rate limiting (optional)
- Header injection prevention
- CSRF protection ready

## Troubleshooting

### Emails Not Sending

1. **Check PHP mail configuration:**
   ```bash
   php -m | grep mail
   ```

2. **Test mail function:**
   ```php
   if (mail('test@example.com', 'Test', 'Test message')) {
       echo 'Mail function works';
   } else {
       echo 'Mail function not working';
   }
   ```

3. **Check server mail logs:**
   ```bash
   tail -f /var/log/mail.log
   ```

### Common Issues

- **Permission denied**: Ensure web server can write to log files
- **SMTP authentication failed**: Check SMTP credentials
- **Form not submitting**: Check JavaScript console for errors
- **500 Internal Server Error**: Check PHP error logs

### Debug Mode
Enable debug mode in `config.php`:
```php
define('DEBUG_MODE', true);
```

## Advanced Features

### Adding reCAPTCHA
1. Get reCAPTCHA keys from Google
2. Update config.php with your keys
3. Add reCAPTCHA script to HTML
4. Implement validation in PHP

### File Upload Support
1. Add file input to HTML form
2. Update PHP to handle file uploads
3. Implement file validation and security checks

### Database Storage
1. Create database table for submissions
2. Update PHP to store data in database
3. Add admin panel to view submissions

## Performance Optimization

- Enable gzip compression
- Optimize CSS and JavaScript
- Use CDN for external resources
- Implement caching headers

## Security Best Practices

1. **Use HTTPS** for all form submissions
2. **Validate on server-side** always
3. **Sanitize all inputs** before processing
4. **Use prepared statements** if using database
5. **Implement rate limiting** to prevent abuse
6. **Regular security updates** for server software

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 11+
- Edge 16+
- IE 11+ (with polyfills)

## License

This project is open source and available under the [MIT License](LICENSE).

## Support

If you encounter any issues or need help:
1. Check the troubleshooting section
2. Review server error logs
3. Test with debug mode enabled
4. Verify server configuration

## Contributing

Feel free to submit issues, fork the repository, and create pull requests for any improvements.

---

**Note:** Remember to replace placeholder email addresses and configuration values with your actual information before deploying to production.