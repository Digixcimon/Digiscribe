from flask import Flask, request, jsonify, render_template_string, send_from_directory
import smtplib
import ssl
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from datetime import datetime
import os
from dotenv import load_dotenv
import re

# Load environment variables
load_dotenv()

app = Flask(__name__)

# Gmail SMTP configuration
SMTP_SERVER = "smtp.gmail.com"
SMTP_PORT = 587

# Email configuration (from environment variables)
SENDER_EMAIL = os.getenv('GMAIL_ADDRESS')
SENDER_PASSWORD = os.getenv('GMAIL_APP_PASSWORD')  # Use App Password, not regular password
RECIPIENT_EMAIL = os.getenv('RECIPIENT_EMAIL', SENDER_EMAIL)  # Default to sender if not specified

def validate_email(email):
    """Validate email format"""
    pattern = r'^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$'
    return re.match(pattern, email) is not None

def validate_phone(phone):
    """Validate phone number format"""
    if not phone:
        return True  # Phone is optional
    # Remove spaces, dashes, parentheses
    clean_phone = re.sub(r'[\s\-\(\)]', '', phone)
    # Check if it's a valid international format
    pattern = r'^[\+]?[1-9][\d]{0,15}$'
    return re.match(pattern, clean_phone) is not None

def send_email(form_data):
    """Send email using Gmail SMTP"""
    try:
        # Validate required environment variables
        if not SENDER_EMAIL or not SENDER_PASSWORD:
            raise ValueError("Gmail credentials not configured. Please set GMAIL_ADDRESS and GMAIL_APP_PASSWORD environment variables.")
        
        # Create message
        message = MIMEMultipart("alternative")
        message["Subject"] = f"Contact Form Submission: {form_data['subject'].title()}"
        message["From"] = SENDER_EMAIL
        message["To"] = RECIPIENT_EMAIL
        message["Reply-To"] = form_data['email']
        
        # Create HTML email content
        html_content = f"""
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body {{ font-family: Arial, sans-serif; line-height: 1.6; color: #333; }}
                .email-container {{ max-width: 600px; margin: 0 auto; padding: 20px; }}
                .header {{ background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; text-align: center; }}
                .content {{ background: #f9f9f9; padding: 30px; border-radius: 8px; margin: 20px 0; }}
                .field {{ margin-bottom: 15px; }}
                .field-label {{ font-weight: bold; color: #555; }}
                .field-value {{ margin-top: 5px; padding: 10px; background: white; border-radius: 4px; border-left: 4px solid #667eea; }}
                .message-content {{ background: white; padding: 20px; border-radius: 8px; border: 1px solid #ddd; white-space: pre-wrap; }}
                .footer {{ text-align: center; color: #666; font-size: 12px; margin-top: 20px; }}
            </style>
        </head>
        <body>
            <div class="email-container">
                <div class="header">
                    <h1>📬 New Contact Form Submission</h1>
                    <p>Received on {datetime.now().strftime('%B %d, %Y at %I:%M %p')}</p>
                </div>
                
                <div class="content">
                    <div class="field">
                        <div class="field-label">👤 Full Name:</div>
                        <div class="field-value">{form_data['name']}</div>
                    </div>
                    
                    <div class="field">
                        <div class="field-label">📧 Email Address:</div>
                        <div class="field-value"><a href="mailto:{form_data['email']}">{form_data['email']}</a></div>
                    </div>
                    
                    {f'''<div class="field">
                        <div class="field-label">📱 Phone Number:</div>
                        <div class="field-value"><a href="tel:{form_data['phone']}">{form_data['phone']}</a></div>
                    </div>''' if form_data.get('phone') else ''}
                    
                    <div class="field">
                        <div class="field-label">📋 Subject:</div>
                        <div class="field-value">{form_data['subject'].title()}</div>
                    </div>
                    
                    <div class="field">
                        <div class="field-label">💬 Message:</div>
                        <div class="message-content">{form_data['message']}</div>
                    </div>
                </div>
                
                <div class="footer">
                    <p>This email was sent from your website contact form.</p>
                    <p>You can reply directly to this email to respond to the sender.</p>
                </div>
            </div>
        </body>
        </html>
        """
        
        # Create plain text version
        text_content = f"""
        New Contact Form Submission
        
        Received on: {datetime.now().strftime('%B %d, %Y at %I:%M %p')}
        
        Full Name: {form_data['name']}
        Email: {form_data['email']}
        {f"Phone: {form_data['phone']}" if form_data.get('phone') else ''}
        Subject: {form_data['subject'].title()}
        
        Message:
        {form_data['message']}
        
        ---
        This email was sent from your website contact form.
        You can reply directly to this email to respond to the sender.
        """
        
        # Convert to MIMEText objects
        text_part = MIMEText(text_content, "plain")
        html_part = MIMEText(html_content, "html")
        
        # Add parts to message
        message.attach(text_part)
        message.attach(html_part)
        
        # Create secure connection and send email
        context = ssl.create_default_context()
        
        with smtplib.SMTP(SMTP_SERVER, SMTP_PORT) as server:
            server.starttls(context=context)
            server.login(SENDER_EMAIL, SENDER_PASSWORD)
            server.send_message(message)
        
        return True, "Email sent successfully"
        
    except smtplib.SMTPAuthenticationError:
        return False, "Authentication failed. Please check your Gmail credentials and ensure you're using an App Password."
    except smtplib.SMTPRecipientsRefused:
        return False, "Invalid recipient email address."
    except smtplib.SMTPServerDisconnected:
        return False, "Connection to Gmail server failed."
    except Exception as e:
        return False, f"Failed to send email: {str(e)}"

@app.route('/')
def index():
    """Serve the contact form"""
    try:
        with open('contact-form.html', 'r') as f:
            return f.read()
    except FileNotFoundError:
        return "Contact form not found. Please ensure contact-form.html exists.", 404

@app.route('/send-email', methods=['POST'])
def send_email_route():
    """Handle form submission and send email"""
    try:
        # Get form data
        data = request.get_json()
        
        # Validate required fields
        required_fields = ['name', 'email', 'subject', 'message']
        for field in required_fields:
            if not data.get(field, '').strip():
                return jsonify({'error': f'{field.title()} is required'}), 400
        
        # Validate email format
        if not validate_email(data['email']):
            return jsonify({'error': 'Invalid email format'}), 400
        
        # Validate phone if provided
        if data.get('phone') and not validate_phone(data['phone']):
            return jsonify({'error': 'Invalid phone number format'}), 400
        
        # Validate message length
        if len(data['message'].strip()) < 10:
            return jsonify({'error': 'Message must be at least 10 characters long'}), 400
        
        # Send email
        success, message = send_email(data)
        
        if success:
            return jsonify({'message': 'Email sent successfully'}), 200
        else:
            return jsonify({'error': message}), 500
            
    except Exception as e:
        return jsonify({'error': f'Server error: {str(e)}'}), 500

@app.route('/health')
def health_check():
    """Health check endpoint"""
    return jsonify({
        'status': 'healthy',
        'timestamp': datetime.now().isoformat(),
        'gmail_configured': bool(SENDER_EMAIL and SENDER_PASSWORD)
    })

if __name__ == '__main__':
    # Check if environment variables are set
    if not SENDER_EMAIL or not SENDER_PASSWORD:
        print("⚠️  WARNING: Gmail credentials not configured!")
        print("Please create a .env file with:")
        print("GMAIL_ADDRESS=your-email@gmail.com")
        print("GMAIL_APP_PASSWORD=your-app-password")
        print("RECIPIENT_EMAIL=recipient@gmail.com (optional)")
        print("\n📱 To create an App Password:")
        print("1. Go to your Google Account settings")
        print("2. Security → 2-Step Verification → App passwords")
        print("3. Generate a new app password for 'Mail'")
        print("4. Use that password in your .env file")
    
    print(f"\n🚀 Starting Flask server...")
    print(f"📧 Gmail configured: {'✅' if SENDER_EMAIL and SENDER_PASSWORD else '❌'}")
    print(f"📬 Sender: {SENDER_EMAIL or 'Not configured'}")
    print(f"📭 Recipient: {RECIPIENT_EMAIL or 'Not configured'}")
    print(f"🌐 Access your form at: http://localhost:5000")
    
    app.run(debug=True, host='0.0.0.0', port=5000)