# M-Pesa STK Push Terminal GUI

[![PHP Version](https://img.shields.io/badge/PHP-7.0%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![SWIFT-WALLET](https://img.shields.io/badge/Powered%20by-SWIFT--WALLET-orange.svg)](https://swiftwallet.co.ke)

A beautiful, feature-rich terminal-based GUI for processing M-Pesa STK Push payments using the SWIFT-WALLET API. Built with PHP, featuring real-time callbacks, comprehensive error handling, and professional transaction management.


## Features

### Beautiful Terminal Interface**
- Colorful, intuitive menus and forms
- Real-time payment animations
- Professional error displays with suggestions
- Progress indicators and status updates

### Enhanced Phone Number Support**
- **01XXXXXXXX** - Safaricom services (0100-0129)
- **07XXXXXXXX** - Safaricom mobile (0701-0729, 0740-0749, 0790-0799)
- **254XXXXXXXXX** - International format
- Automatic validation and formatting

### Real-Time Callback Integration**
- Instant payment confirmations
- M-Pesa receipt capture
- Service fee tracking
- 2-minute timeout with graceful handling
- Comprehensive webhook logging

### Professional Features**
- **Bulk Payment Processing** - CSV upload for multiple payments
- **Transaction History** - Detailed logs with filtering and export
- **Error Code Reference** - Complete SWIFT-WALLET error documentation
- **Account Management** - API key management and validation

### 🛡Security & Reliability**
- HTTPS-only callback endpoints
- Comprehensive input validation
- Secure API key storage
- Error handling for all scenarios

## Quick Start

### Prerequisites
- PHP 7.0+ with cURL extension
- SWIFT-WALLET account ([Sign up here](https://swiftwallet.co.ke))
- Terminal/Command line access

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/Tiddiesxxl/mpesa-stk-push-gui.git
   cd mpesa-stk-push-gui
   ```

2. **Run the installer**
   ```bash
   chmod +x install.sh
   ./install.sh
   ```

3. **Configure your API key**
   ```bash
   # The installer will create config.php from the sample
   # Edit it with your SWIFT-WALLET API key
   nano config.php
   ```

4. **Start the application**
   ```bash
   ./mpesa
   # or
   php mpesa_stk_gui.php
   ```

## Usage

### Basic Payment Flow
1. **Select "Initiate STK Push Payment"**
2. **Enter customer's Safaricom number** (supports 01XX, 07XX, 254XX)
3. **Enter payment amount** (minimum KES 1)
4. **Add optional customer details**
5. **Confirm transaction**
6. **Watch real-time animation** while customer completes payment
7. **Receive instant confirmation** with M-Pesa receipt

### Advanced Features

#### Bulk Payments
```bash
# Create sample CSV
Select option 5 → Enter 'create'

# Upload your CSV file
phone_number,amount,customer_name,reference
0712345678,1500,John Doe,ORDER-001
0798765432,2000,Jane Smith,ORDER-002
```

#### Transaction History
- **Summary View** - Last 10 transactions
- **Detailed View** - Complete transaction information
- **Filter by Status** - Success/Failed transactions
- **CSV Export** - Download transaction reports

## Configuration

### API Key Setup
```php
// config.php
$apiKey = 'your_swift_wallet_api_key_here';
```

### Callback URL (Optional)
```php
// For custom callback endpoints
$callbackUrl = 'https://yourdomain.com/callback.php';
```

## Callback Integration

### Setup Webhook Endpoint
1. **Upload `callback.php`** to your web server
2. **Ensure HTTPS** is enabled
3. **Set proper permissions**
   ```bash
   chmod 644 callback.php
   chown www-data:www-data callback.php
   ```

### Callback Features
- ✅ Real-time payment status updates
- ✅ M-Pesa receipt number capture
- ✅ Service fee tracking
- ✅ Comprehensive error logging
- ✅ Automatic transaction updates

## Error Handling

### HTTP Status Codes
| Code | Meaning | Description |
|------|---------|-------------|
| 200/201 | Success | Payment initiated successfully |
| 400 | Bad Request | Invalid parameters |
| 401 | Unauthorized | Invalid API key |
| 402 | Payment Required | Insufficient balance |
| 500 | Server Error | SWIFT-WALLET server issues |

### Smart Error Suggestions
The application provides context-aware troubleshooting tips for each error type.

## Testing

### Test Callback Functionality
```bash
php test_callback.php
```

### Validate Phone Numbers
```bash
php demo.php
```

## Project Structure

```
mpesa-stk-push-gui/
├── mpesa_stk_gui.php      # Main application
├── callback.php           # Webhook handler
├── config.sample.php      # Configuration template
├── install.sh            # Installation script
├── test_callback.php     # Testing utilities
├── demo.php              # Phone validation demo
├── README.md             # Documentation
├── CHANGELOG.md          # Version history
├── DEPLOYMENT_GUIDE.md   # Deployment instructions
├── CONTRIBUTING.md       # Contribution guidelines
└── LICENSE               # MIT License
```

## Contributing

We welcome contributions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

### Areas for Contribution
- Payment provider integrations
- Web-based interface
- Multi-language support
- Performance optimizations
- Documentation improvements

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

### Getting Help
- **Documentation**: Check the [README](README.md) and [Deployment Guide](DEPLOYMENT_GUIDE.md)
- **Bug Reports**: Open an [issue](https://github.com/Tiddiesxxl/mpesa-stk-push-gui/issues)
- **Feature Requests**: Use [GitHub Discussions](https://github.com/Tiddiesxxl/mpesa-stk-push-gui/discussions)

### SWIFT-WALLET Support
- **Website**: [swiftwallet.co.ke](https://swiftwallet.co.ke)
- **Dashboard**: [swiftwallet.co.ke/dashboard](https://swiftwallet.co.ke/dashboard)
- **API Docs**: Check your SWIFT-WALLET dashboard

## Acknowledgments

- **SWIFT-WALLET** for providing the M-Pesa API integration
- **Safaricom** for the M-Pesa platform
- **PHP Community** for excellent documentation and tools

## Related Projects

- [SWIFT-WALLET PHP SDK](https://github.com/swift-wallet/php-sdk)
- [M-Pesa API Documentation](https://developer.safaricom.co.ke/)

