# M-Pesa STK Push Terminal GUI

A beautiful terminal-based GUI application for initiating M-Pesa STK Push payments using the SWIFT-WALLET API. This application provides an intuitive interface for processing mobile payments directly from your terminal.

## Features

- 🎨 **Beautiful Terminal Interface** - Clean, colorful, and user-friendly GUI
- 📱 **Safaricom Number Validation** - Automatic validation of Kenyan mobile numbers
- 💰 **STK Push Integration** - Direct integration with SWIFT-WALLET API
- 📊 **Transaction History** - View recent transaction logs
- 🔐 **Secure API Key Management** - Safe storage and management of credentials
- ✅ **Input Validation** - Comprehensive validation for all user inputs
- 🎯 **Error Handling** - Clear error messages and recovery options

## Prerequisites

Before using this application, ensure you have:

1. **PHP CLI** (version 7.0 or higher) with cURL extension
2. **SWIFT-WALLET Account** - Sign up at [swiftwallet.co.ke](https://swiftwallet.co.ke)
3. **API Key** - Obtain from your SWIFT-WALLET dashboard
4. **Active Payment Channel** - Configure Paybill, Till Number, or Bank Settlement
5. **Sufficient Wallet Balance** - To cover transaction fees

## Installation

### Quick Install (Linux/macOS)

```bash
# Make the installer executable
chmod +x install.sh

# Run the installer
./install.sh
```

### Manual Installation

1. **Check PHP Installation:**
   ```bash
   php --version
   php -m | grep curl
   ```

2. **Make the script executable:**
   ```bash
   chmod +x mpesa_stk_gui.php
   ```

3. **Create a launcher (optional):**
   ```bash
   echo '#!/bin/bash' > mpesa
   echo 'php mpesa_stk_gui.php' >> mpesa
   chmod +x mpesa
   ```

## Usage

### Starting the Application

```bash
# Using the launcher
./mpesa

# Or directly with PHP
php mpesa_stk_gui.php
```

### First-Time Setup

1. Run the application
2. Enter your SWIFT-WALLET API key when prompted
3. The API key will be saved securely for future use

### Main Menu Options

1. **Initiate STK Push Payment**
   - Enter customer's Safaricom number
   - Specify payment amount
   - Add optional customer details
   - Confirm and send STK push

2. **View Transaction History**
   - See recent transactions
   - Check payment status
   - Review transaction details

3. **Update API Key**
   - Change your SWIFT-WALLET API key
   - Secure credential management

4. **Help & Documentation**
   - View supported phone formats
   - Understand the payment process
   - Get troubleshooting tips

## Supported Phone Number Formats

The application accepts Safaricom numbers in these formats:

- **01XXXXXXXX** (10 digits starting with 01)
- **07XXXXXXXX** (10 digits starting with 07)
- **254XXXXXXXXX** (12 digits starting with 254)

### Valid Safaricom Prefixes

- **0100-0129** - Safaricom services
- **0701-0729** - Safaricom mobile numbers
- **0740-0749** - Safaricom mobile numbers  
- **0790-0799** - Safaricom mobile numbers

## Transaction Process

1. **Input Validation** - Phone number and amount are validated
2. **Transaction Summary** - Review details before confirmation
3. **API Request** - Secure request sent to SWIFT-WALLET
4. **STK Push** - Customer receives payment prompt on phone
5. **PIN Entry** - Customer enters M-Pesa PIN to complete payment
6. **Confirmation** - Transaction status is logged and displayed

## Configuration

### API Key Storage

The application stores your API key in `config.php`:

```php
<?php
$apiKey = 'your_api_key_here';
```

### Transaction Logging

All transactions are logged to `transactions.log` in JSON format:

```json
{
  "timestamp": "2024-01-15 14:30:25",
  "phone": "0798765432",
  "amount": 1500,
  "customer": "John Doe",
  "reference": "STK-20240115143025-1234",
  "status": "initiated",
  "data": {...}
}
```

## Error Handling

The application handles various error scenarios:

- **Invalid Phone Numbers** - Clear validation messages
- **Network Issues** - Connection timeout handling
- **API Errors** - Detailed error reporting
- **Invalid Amounts** - Input validation and correction

## Security Features

- **Secure API Key Storage** - Keys are stored locally and securely
- **Input Sanitization** - All inputs are validated and sanitized
- **HTTPS Communication** - All API calls use secure HTTPS
- **No Sensitive Data Logging** - API keys and sensitive data are not logged

## Troubleshooting

### Common Issues

1. **"PHP is not installed"**
   ```bash
   # Ubuntu/Debian
   sudo apt-get install php-cli php-curl
   
   # CentOS/RHEL
   sudo yum install php-cli php-curl
   
   # macOS
   brew install php
   ```

2. **"cURL extension not found"**
   ```bash
   # Ubuntu/Debian
   sudo apt-get install php-curl
   
   # CentOS/RHEL
   sudo yum install php-curl
   ```

3. **"Permission denied"**
   ```bash
   chmod +x mpesa_stk_gui.php
   chmod +x mpesa
   ```

4. **"API Key invalid"**
   - Check your SWIFT-WALLET dashboard
   - Ensure the API key is copied correctly
   - Verify your account is active

### Getting Help

- **SWIFT-WALLET Support**: Visit [swiftwallet.co.ke](https://swiftwallet.co.ke)
- **API Documentation**: Check your SWIFT-WALLET dashboard
- **Account Issues**: Contact SWIFT-WALLET support team

## File Structure

```
.
├── mpesa_stk_gui.php    # Main application file
├── install.sh           # Installation script
├── README.md           # This documentation
├── config.php          # API key configuration (created on first run)
├── transactions.log    # Transaction history (created automatically)
└── mpesa              # Launcher script (created by installer)
```

## Requirements

- **PHP**: 7.0 or higher
- **Extensions**: cURL, JSON
- **Operating System**: Linux, macOS, or Windows with PHP CLI
- **Network**: Internet connection for API calls
- **Account**: Active SWIFT-WALLET account with API access

## License

This project is open source and available under the MIT License.

## Contributing

Contributions are welcome! Please feel free to submit issues, feature requests, or pull requests.

---

**Made with ❤️ for the Kenyan developer community**