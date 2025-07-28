# Contact Form with Gmail Integration

A beautiful, responsive contact form that sends emails directly to Gmail using PHP and PHPMailer. Features modern design, client-side validation, and secure email delivery.

## Features

- ✨ **Modern Design**: Beautiful gradient background with glass-morphism effects
- 📱 **Fully Responsive**: Works perfectly on desktop, tablet, and mobile devices
- ✅ **Client-side Validation**: Real-time form validation with JavaScript
- 🔒 **Secure Email Delivery**: Uses PHPMailer with Gmail SMTP
- 🚀 **AJAX Submission**: No page refresh required
- 🎨 **Smooth Animations**: Loading states and transitions
- 📊 **Character Counter**: Live character count for message field
- 🛡️ **Input Sanitization**: Server-side validation and sanitization

## Files Structure

```
contact-form/
├── index.html          # Main HTML form
├── style.css           # CSS styling
├── script.js           # JavaScript validation and AJAX
├── send_email.php      # PHP email handler
├── config.php          # Email configuration
├── composer.json       # PHP dependencies
└── README.md           # This file
```

## Setup Instructions

### 1. Install Dependencies

First, install PHPMailer using Composer:

```bash
composer install
```

If you don't have Composer installed, [download it here](https://getcomposer.org/download/).

### 2. Configure Gmail App Password

To send emails through Gmail, you need to set up an App Password:

1. Go to [Google Account settings](https://myaccount.google.com)
2. Click "Security" in the left sidebar
3. Under "Signing in to Google", click "2-Step Verification"
4. Enable 2-Step Verification if not already enabled
5. At the bottom of the page, click "App passwords"
6. Select "Mail" for the app and "Other" for the device
7. Enter a name like "Contact Form" and click "Generate"
8. Copy the 16-character password (this is your App Password)

### 3. Update Configuration

Edit `config.php` and update the following settings:

```php
'SMTP_USERNAME' => 'your-email@gmail.com',          // Your Gmail address
'SMTP_PASSWORD' => 'your-16-character-app-password', // Your Gmail App Password
'FROM_EMAIL' => 'your-email@gmail.com',             // Your Gmail address
'TO_EMAIL' => 'recipient@gmail.com',                // Where to send emails
```

### 4. Set Permissions

Make sure your web server has write permissions for the logs directory (if using error logging):

```bash
mkdir logs
chmod 755 logs
```

### 5. Upload to Web Server

Upload all files to your web server that supports PHP (version 7.4 or higher).

## Usage

1. Open `index.html` in your web browser
2. Fill out the contact form
3. Click "Send Message"
4. The form will validate input and send the email
5. You'll receive a confirmation message

## Form Fields

- **Full Name** (required): Sender's name
- **Email Address** (required): Sender's email for replies
- **Phone Number** (optional): Contact phone number
- **Subject** (required): Email subject line
- **Message** (required): Main message content

## Customization

### Styling

Edit `style.css` to customize the appearance:
- Change colors in the gradient backgrounds
- Modify form dimensions and spacing
- Update fonts and typography

### Form Fields

In `index.html`, you can:
- Add or remove form fields
- Change field labels and placeholders
- Modify validation requirements

### Email Template

In `send_email.php`, customize the HTML email template in the `$htmlBody` variable.

### Configuration Options

In `config.php`, you can adjust:
- Message length limits
- Rate limiting settings
- Debug and logging options
- Phone field requirements

## Security Features

- **Input Sanitization**: All user input is sanitized
- **Email Validation**: Server-side email format validation
- **Rate Limiting**: Prevents spam submissions (configurable)
- **CSRF Protection**: Forms use POST method only
- **Error Logging**: Logs errors without exposing sensitive information

## Troubleshooting

### Common Issues

1. **"Authentication failed" error**
   - Make sure you're using an App Password, not your regular Gmail password
   - Verify 2-factor authentication is enabled
   - Check that the Gmail address and App Password are correct

2. **"Connection failed" error**
   - Verify your server allows outbound SMTP connections
   - Check firewall settings for port 587
   - Try using port 465 with SSL instead of TLS

3. **Form not submitting**
   - Check browser console for JavaScript errors
   - Verify all required fields are filled
   - Ensure PHP is properly installed and configured

4. **Emails not received**
   - Check spam/junk folder
   - Verify the recipient email address is correct
   - Check server error logs

### Debug Mode

Enable debug mode in `config.php` for detailed error messages:

```php
'DEBUG_MODE' => true,
```

**Important**: Disable debug mode in production environments.

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+
- Internet Explorer 11+ (with reduced features)

## License

This project is open source and available under the [MIT License](LICENSE).

## Support

For issues and questions:
1. Check the troubleshooting section above
2. Review the configuration settings
3. Check server error logs
4. Verify Gmail App Password setup

## Contributing

Feel free to submit issues, fork the repository, and create pull requests for improvements.