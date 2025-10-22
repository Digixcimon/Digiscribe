# 📧 Direct Gmail Contact Form Setup Guide

This contact form sends emails directly to your Gmail account without any third-party services. It uses Gmail's SMTP server for secure email delivery.

## 🚀 Quick Start

### 1. Install Dependencies

```bash
# Install Python dependencies
pip install -r requirements.txt
```

### 2. Configure Gmail

#### Step 1: Enable 2-Factor Authentication
1. Go to your [Google Account settings](https://myaccount.google.com/)
2. Navigate to **Security**
3. Enable **2-Step Verification** if not already enabled

#### Step 2: Create App Password
1. In Google Account Security settings
2. Go to **2-Step Verification** → **App passwords**
3. Select **Mail** from the dropdown
4. Generate a new app password
5. **Copy the 16-character password** (you'll need this for the .env file)

### 3. Configure Environment Variables

```bash
# Copy the example environment file
cp .env.example .env

# Edit the .env file with your credentials
nano .env
```

Update the `.env` file with your information:

```env
GMAIL_ADDRESS=your-actual-email@gmail.com
GMAIL_APP_PASSWORD=your-16-character-app-password
RECIPIENT_EMAIL=where-to-receive-form-submissions@gmail.com
```

### 4. Run the Application

```bash
# Start the Flask server
python app.py
```

The application will be available at: **http://localhost:5000**

## 📁 File Structure

```
├── contact-form.html      # Modern HTML contact form with validation
├── app.py                # Flask backend that handles email sending
├── requirements.txt      # Python dependencies
├── .env.example         # Environment variables template
├── .env                 # Your actual credentials (create this)
└── SETUP_GUIDE.md       # This setup guide
```

## 🛡️ Security Features

- **App Password**: Uses Gmail App Passwords instead of your main password
- **SSL/TLS Encryption**: All email communication is encrypted
- **Server-side Validation**: Input validation on both client and server
- **Environment Variables**: Credentials stored securely in environment file
- **No Third-party Services**: Direct connection to Gmail SMTP

## 📋 Form Features

- **Modern UI**: Beautiful, responsive design
- **Real-time Validation**: Client-side form validation
- **Required Fields**: Name, email, subject, and message
- **Optional Phone**: Phone number field with validation
- **Loading States**: Visual feedback during form submission
- **Success Messages**: Clear confirmation when email is sent
- **Error Handling**: Detailed error messages for troubleshooting

## 🎨 Customization

### Styling
The form uses embedded CSS for easy customization. Key design elements:
- Gradient background (`#667eea` to `#764ba2`)
- Clean white form container
- Smooth animations and transitions
- Mobile-responsive design

### Email Template
The email template includes:
- Professional HTML formatting
- Sender information with reply-to functionality
- Timestamp and organized field display
- Both HTML and plain text versions

### Form Fields
Current fields:
- **Name** (required)
- **Email** (required)
- **Phone** (optional)
- **Subject** (required, dropdown)
- **Message** (required, minimum 10 characters)

## 🔧 Troubleshooting

### Common Issues

#### "Authentication failed"
- Ensure you're using an **App Password**, not your regular Gmail password
- Verify 2-Factor Authentication is enabled on your Google account
- Double-check the credentials in your `.env` file

#### "Connection to Gmail server failed"
- Check your internet connection
- Verify Gmail SMTP settings (smtp.gmail.com:587)
- Ensure your firewall isn't blocking outbound connections

#### "Invalid recipient email address"
- Verify the `RECIPIENT_EMAIL` in your `.env` file
- Ensure the email format is correct

### Testing the Setup

1. **Health Check**: Visit `http://localhost:5000/health` to verify configuration
2. **Test Email**: Fill out and submit the contact form
3. **Check Gmail**: Look for the email in your specified recipient inbox

## 🚀 Production Deployment

For production deployment, consider:

### Environment
- Use a production WSGI server (like Gunicorn)
- Set `debug=False` in `app.py`
- Use environment variables for all sensitive data

### Security
- Enable HTTPS/SSL certificates
- Implement rate limiting to prevent spam
- Add CAPTCHA for additional spam protection
- Use a reverse proxy (nginx) for better security

### Example Production Command
```bash
# Install production server
pip install gunicorn

# Run with Gunicorn
gunicorn -w 4 -b 0.0.0.0:5000 app:app
```

## 📧 Email Delivery

### What Happens When Form is Submitted
1. Client-side validation checks all fields
2. Form data is sent to Flask backend via AJAX
3. Server validates data again
4. Email is composed with professional formatting
5. Email is sent via Gmail SMTP with SSL encryption
6. User receives success confirmation

### Email Format
- **Subject**: "Contact Form Submission: [Subject]"
- **From**: Your Gmail address
- **Reply-To**: Form submitter's email
- **Content**: Beautifully formatted HTML with all form data

## 🎯 Benefits

✅ **No Third-party Dependencies**: Direct Gmail integration  
✅ **Secure**: Uses Gmail's secure SMTP with App Passwords  
✅ **Professional**: Clean, modern form design  
✅ **Reliable**: Built on Flask and Gmail's infrastructure  
✅ **Customizable**: Easy to modify styling and functionality  
✅ **Mobile-friendly**: Responsive design works on all devices  
✅ **Fast**: Minimal dependencies, quick load times  

## 📞 Support

If you encounter any issues:
1. Check the troubleshooting section above
2. Verify your Gmail App Password setup
3. Test with the health check endpoint
4. Review server logs for specific error messages

The application provides detailed error messages to help diagnose any issues.