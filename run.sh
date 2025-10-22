#!/bin/bash

# Contact Form Startup Script
echo "🚀 Starting Contact Form Application..."

# Check if .env file exists
if [ ! -f ".env" ]; then
    echo "⚠️  .env file not found!"
    echo "📋 Copying .env.example to .env..."
    cp .env.example .env
    echo "✏️  Please edit .env with your Gmail credentials and run this script again."
    echo "📖 See SETUP_GUIDE.md for detailed instructions."
    exit 1
fi

# Check if virtual environment exists
if [ ! -d "venv" ]; then
    echo "📦 Creating Python virtual environment..."
    python3 -m venv venv
fi

# Activate virtual environment
echo "🔧 Activating virtual environment..."
source venv/bin/activate

# Check if Python dependencies are installed
echo "📦 Checking Python dependencies..."
if ! pip show flask python-dotenv >/dev/null 2>&1; then
    echo "📥 Installing Python dependencies..."
    pip install -r requirements.txt
fi

# Start the Flask application
echo "🌐 Starting Flask server on http://localhost:5000"
echo "📧 Check the setup guide if you need help configuring Gmail"
echo "⏹️  Press Ctrl+C to stop the server"
echo ""

python app.py