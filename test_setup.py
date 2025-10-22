#!/usr/bin/env python3
"""
Test script to verify Gmail contact form setup
"""

import os
import sys
from dotenv import load_dotenv
import re

def test_dependencies():
    """Test if required Python packages are installed"""
    print("🔍 Testing Python dependencies...")
    
    try:
        import flask
        print(f"✅ Flask {flask.__version__} installed")
    except ImportError:
        print("❌ Flask not installed. Run: pip install flask")
        return False
    
    try:
        import dotenv
        print(f"✅ python-dotenv installed")
    except ImportError:
        print("❌ python-dotenv not installed. Run: pip install python-dotenv")
        return False
    
    return True

def test_environment():
    """Test environment variables configuration"""
    print("\n🔍 Testing environment configuration...")
    
    # Load environment variables
    load_dotenv()
    
    gmail_address = os.getenv('GMAIL_ADDRESS')
    gmail_password = os.getenv('GMAIL_APP_PASSWORD')
    recipient_email = os.getenv('RECIPIENT_EMAIL')
    
    # Check Gmail address
    if not gmail_address:
        print("❌ GMAIL_ADDRESS not set in .env file")
        return False
    elif not re.match(r'^[a-zA-Z0-9._%+-]+@gmail\.com$', gmail_address):
        print(f"❌ GMAIL_ADDRESS '{gmail_address}' is not a valid Gmail address")
        return False
    else:
        print(f"✅ GMAIL_ADDRESS: {gmail_address}")
    
    # Check Gmail app password
    if not gmail_password:
        print("❌ GMAIL_APP_PASSWORD not set in .env file")
        print("📖 See SETUP_GUIDE.md for instructions on creating an App Password")
        return False
    elif len(gmail_password.replace(' ', '')) != 16:
        print(f"❌ GMAIL_APP_PASSWORD should be 16 characters (currently {len(gmail_password)} characters)")
        print("📖 Make sure you're using a Gmail App Password, not your regular password")
        return False
    else:
        print(f"✅ GMAIL_APP_PASSWORD: {'*' * len(gmail_password)} (length: {len(gmail_password)})")
    
    # Check recipient email
    if recipient_email:
        if not re.match(r'^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$', recipient_email):
            print(f"❌ RECIPIENT_EMAIL '{recipient_email}' is not a valid email address")
            return False
        else:
            print(f"✅ RECIPIENT_EMAIL: {recipient_email}")
    else:
        print(f"ℹ️  RECIPIENT_EMAIL not set (will default to {gmail_address})")
    
    return True

def test_smtp_connection():
    """Test connection to Gmail SMTP server"""
    print("\n🔍 Testing Gmail SMTP connection...")
    
    try:
        import smtplib
        import ssl
        
        load_dotenv()
        gmail_address = os.getenv('GMAIL_ADDRESS')
        gmail_password = os.getenv('GMAIL_APP_PASSWORD')
        
        if not gmail_address or not gmail_password:
            print("❌ Gmail credentials not configured")
            return False
        
        # Test connection
        context = ssl.create_default_context()
        with smtplib.SMTP("smtp.gmail.com", 587) as server:
            server.starttls(context=context)
            server.login(gmail_address, gmail_password)
            print("✅ Gmail SMTP connection successful")
            return True
            
    except smtplib.SMTPAuthenticationError:
        print("❌ Gmail authentication failed")
        print("📖 Check your App Password and ensure 2FA is enabled")
        return False
    except Exception as e:
        print(f"❌ Gmail connection failed: {str(e)}")
        return False

def test_files():
    """Test if required files exist"""
    print("\n🔍 Testing required files...")
    
    required_files = [
        'contact-form.html',
        'app.py',
        'requirements.txt',
        '.env.example'
    ]
    
    all_exist = True
    for file in required_files:
        if os.path.exists(file):
            print(f"✅ {file} exists")
        else:
            print(f"❌ {file} missing")
            all_exist = False
    
    # Check .env file
    if os.path.exists('.env'):
        print("✅ .env exists")
    else:
        print("⚠️  .env file not found")
        print("📋 Copy .env.example to .env and configure your credentials")
        all_exist = False
    
    return all_exist

def main():
    """Run all tests"""
    print("🧪 Contact Form Setup Test")
    print("=" * 40)
    
    tests = [
        ("Dependencies", test_dependencies),
        ("Files", test_files),
        ("Environment", test_environment),
        ("Gmail Connection", test_smtp_connection)
    ]
    
    results = []
    for test_name, test_func in tests:
        try:
            result = test_func()
            results.append((test_name, result))
        except Exception as e:
            print(f"❌ {test_name} test failed with error: {str(e)}")
            results.append((test_name, False))
    
    # Summary
    print("\n" + "=" * 40)
    print("📊 Test Summary:")
    
    all_passed = True
    for test_name, passed in results:
        status = "✅ PASS" if passed else "❌ FAIL"
        print(f"  {status} {test_name}")
        if not passed:
            all_passed = False
    
    print("\n" + "=" * 40)
    if all_passed:
        print("🎉 All tests passed! Your contact form is ready to use.")
        print("🚀 Run 'python app.py' or './run.sh' to start the application")
    else:
        print("⚠️  Some tests failed. Please fix the issues above.")
        print("📖 Check SETUP_GUIDE.md for detailed instructions")
    
    return 0 if all_passed else 1

if __name__ == "__main__":
    sys.exit(main())