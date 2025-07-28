# 🚀 Quick Start Guide - Contact Form with Gmail

Your complete contact form system is ready! Here's how to get it working in just a few minutes.

## 📁 What You Have

- **`index.html`** - Beautiful responsive contact form
- **`style.css`** - Modern styling with animations
- **`script.js`** - Client-side validation and AJAX submission
- **`send_email.php`** - Server-side email processing
- **`config.php`** - Email configuration (needs your Gmail setup)
- **`setup.php`** - Test script to verify everything works

## ⚡ 3-Step Setup

### Step 1: Gmail App Password
1. Go to your [Google Account](https://myaccount.google.com/)
2. Navigate to **Security** → **2-Step Verification** → **App passwords**
3. Generate a new app password for "Mail"
4. Copy the 16-character password (spaces don't matter)

### Step 2: Update Configuration
1. Open `config.php` in any text editor
2. Replace these values:
   ```php
   'SMTP_USERNAME' => 'your-actual-email@gmail.com',
   'SMTP_PASSWORD' => 'your-16-char-app-password',
   'FROM_EMAIL' => 'your-actual-email@gmail.com',
   'TO_EMAIL' => 'where-you-want-emails@gmail.com',
   ```

### Step 3: Test It
```bash
php setup.php
```

If you see "✅ Test email sent successfully!" - you're done! 🎉

## 🌐 Using the Form

1. **Open `index.html`** in any web browser
2. **Fill out the form** with test data
3. **Click Submit** - you should see a success message
4. **Check your email** - the message should arrive in seconds

## 🛠️ For Web Servers

- **Apache/Nginx**: Just upload all files to your web directory
- **Local Development**: Use `php -S localhost:8000` to start a local server
- **Production**: Remove error reporting from `send_email.php`

## 🎨 Features Included

- ✅ **Responsive Design** - Works on all devices
- ✅ **Real-time Validation** - Immediate feedback
- ✅ **AJAX Submission** - No page refresh
- ✅ **Loading States** - Professional user experience
- ✅ **Character Counter** - For message field
- ✅ **Email Templates** - HTML formatted emails
- ✅ **Security** - Input sanitization and validation

## 🔧 Troubleshooting

**Email not sending?**
- Verify 2FA is enabled on Gmail
- Double-check your App Password (not regular password)
- Run `php setup.php` to test configuration

**Form not submitting?**
- Check browser console for JavaScript errors
- Ensure PHP is running on your server
- Verify file permissions

**Styling issues?**
- All CSS is in `style.css`
- Mobile-first responsive design
- Easily customizable colors and layout

## 📞 Support

If you encounter any issues:
1. Check the detailed `README.md` file
2. Run the setup script: `php setup.php`
3. Verify your Gmail App Password setup

**You're all set! Your contact form is ready to receive messages! 🎉**