# Contact Form Setup Instructions

Your contact form is now ready to send emails directly to Gmail! Follow these steps to set it up:

## Step 1: Create EmailJS Account

1. Go to [EmailJS.com](https://www.emailjs.com/)
2. Sign up for a free account
3. Verify your email address

## Step 2: Set Up Email Service

1. In your EmailJS dashboard, go to **Email Services**
2. Click **Add New Service**
3. Choose **Gmail** 
4. Click **Connect Account** and authorize with your Gmail
5. Note the **Service ID** (you'll need this later)

## Step 3: Create Email Template

1. Go to **Email Templates** in your dashboard
2. Click **Create New Template**
3. Use this template content:

```
Subject: {{subject}}

From: {{from_name}}
Email: {{from_email}}

Message:
{{message}}

---
This message was sent from your contact form.
```

4. Save the template and note the **Template ID**

## Step 4: Get Your Public Key

1. Go to **Account** > **General**
2. Find your **Public Key** in the API Keys section

## Step 5: Update Your Code

Open `script.js` and replace these values:

```javascript
// Line 3: Replace YOUR_PUBLIC_KEY
publicKey: "your_actual_public_key_here",

// Line 25: Replace with your Gmail
to_email: 'youremail@gmail.com'

// Line 28: Replace YOUR_SERVICE_ID and YOUR_TEMPLATE_ID
emailjs.send('your_service_id', 'your_template_id', templateParams)
```

## Step 6: Test Your Form

1. Open `index.html` in your web browser
2. Fill out the form and submit
3. Check your Gmail for the message!

## Free Tier Limits

EmailJS free tier includes:
- 200 emails per month
- No credit card required
- Perfect for personal contact forms

## Troubleshooting

If emails aren't sending:
1. Check browser console for errors
2. Verify all IDs are correct in `script.js`
3. Make sure your EmailJS service is connected to Gmail
4. Check your EmailJS dashboard for failed sends

Your form will now send emails directly to your Gmail without any backend server!