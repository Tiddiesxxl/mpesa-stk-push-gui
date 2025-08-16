#!/bin/bash

# M-Pesa STK Push GUI Installation Script

echo "╔══════════════════════════════════════════════════════════════╗"
echo "║              M-Pesa STK Push GUI Installer                   ║"
echo "║                Powered by SWIFT-WALLET                       ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed. Please install PHP CLI first."
    echo "   Ubuntu/Debian: sudo apt-get install php-cli php-curl"
    echo "   CentOS/RHEL: sudo yum install php-cli php-curl"
    echo "   macOS: brew install php"
    exit 1
fi

# Check PHP version
PHP_VERSION=$(php -r "echo PHP_VERSION;")
echo "✓ PHP $PHP_VERSION detected"

# Check if cURL extension is available
if ! php -m | grep -q curl; then
    echo "❌ PHP cURL extension is not installed."
    echo "   Ubuntu/Debian: sudo apt-get install php-curl"
    echo "   CentOS/RHEL: sudo yum install php-curl"
    exit 1
fi

echo "✓ PHP cURL extension is available"

# Make the main script executable
chmod +x mpesa_stk_gui.php

# Create a simple launcher script
cat > mpesa << 'EOF'
#!/bin/bash
cd "$(dirname "$0")"
php mpesa_stk_gui.php
EOF

chmod +x mpesa

echo "✓ Installation completed successfully!"
echo ""
echo "📋 Next Steps:"
echo "1. Get your API key from SWIFT-WALLET dashboard"
echo "2. Run the application: ./mpesa"
echo "3. Enter your API key when prompted"
echo ""
echo "🚀 To start the application, run: ./mpesa"