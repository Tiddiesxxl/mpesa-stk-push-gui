# M-Pesa STK Push GUI - Deployment Guide

## 🚀 Complete Setup with Callback Integration

### **Files Overview**
- `mpesa_stk_gui.php` - Main terminal application
- `callback.php` - Webhook callback handler  
- `install.sh` - Installation script
- `test_callback.php` - Callback testing utility
- `README.md` - Documentation
- `CHANGELOG.md` - Feature history

### **🌐 Callback URL Setup**

Your callback endpoint is configured as:
```
https://rika.ayubxxl.site/swift/callback.php
```

#### **Server Requirements:**
1. **HTTPS enabled** (✅ Cloudflare Tunnel provides this)
2. **PHP 7.0+** with cURL extension
3. **Write permissions** for log files
4. **30-second response time** capability

#### **File Placement:**
```bash
# Place callback.php in your web directory
/var/www/html/swift/callback.php

# Ensure proper permissions
chmod 644 /var/www/html/swift/callback.php
chmod 755 /var/www/html/swift/
```

### **🔧 Installation Steps**

#### **1. Terminal Application Setup:**
```bash
# Clone or download files to your local machine
chmod +x install.sh
./install.sh

# Run the application
./mpesa
```

#### **2. Callback Handler Setup:**
```bash
# On your server (where callback.php is hosted)
sudo mkdir -p /var/www/html/swift/
sudo cp callback.php /var/www/html/swift/
sudo chown www-data:www-data /var/www/html/swift/callback.php
sudo chmod 644 /var/www/html/swift/callback.php

# Create log directories with proper permissions
sudo touch /var/www/html/swift/callback_log.txt
sudo touch /var/www/html/swift/callback_transactions.json
sudo chown www-data:www-data /var/www/html/swift/callback_*.{txt,json}
sudo chmod 666 /var/www/html/swift/callback_*.{txt,json}
```

#### **3. Test Callback Functionality:**
```bash
# Run the test script locally
php test_callback.php

# Check if callback files are created
ls -la callback_*.{txt,json}
```

### **🎯 Features Included**

#### **Enhanced STK Push Process:**
1. **Initiate Payment** → STK Push sent with callback URL
2. **Real-time Animation** → Spinning loader with countdown
3. **Callback Monitoring** → Checks for payment status every 2 seconds
4. **Instant Confirmation** → Shows success/failure immediately
5. **Receipt Display** → M-Pesa receipt number and service fees

#### **Callback System Features:**
- ✅ **HTTPS Security** - SSL/TLS encryption required
- ✅ **Comprehensive Logging** - All callbacks logged with timestamps
- ✅ **Error Handling** - Graceful handling of malformed data
- ✅ **Status Tracking** - Real-time payment status updates
- ✅ **Receipt Capture** - M-Pesa receipt numbers stored
- ✅ **Service Fee Tracking** - Monitor transaction costs
- ✅ **Channel Information** - Payment routing details

#### **Menu Structure:**
```
1. Initiate STK Push Payment     ← Enhanced with callback waiting
2. View Transaction History      ← Shows callback status
3. View Callback Logs           ← NEW: Real-time callback activity
4. Check Account Balance        ← API key verification
5. Bulk Payment Upload          ← CSV batch processing
6. Update API Key              ← Secure credential management
7. Error Code Reference        ← Complete error documentation
8. Help & Documentation        ← Updated with callback info
9. Exit
```

### **📊 Callback Data Structure**

#### **Successful Payment:**
```json
{
  "success": true,
  "transaction_id": 12847,
  "external_reference": "ORDER-12345",
  "status": "completed",
  "service_fee": 15.00,
  "result": {
    "ResultCode": 0,
    "Amount": 1500,
    "MpesaReceiptNumber": "SAE3YULR0Y",
    "Phone": "254798765432"
  },
  "channel_info": {
    "channel_type": "paybill",
    "channel_name": "Main Business Account"
  }
}
```

#### **Failed Payment:**
```json
{
  "success": false,
  "transaction_id": 12847,
  "external_reference": "ORDER-12345",
  "status": "failed",
  "result": {
    "ResultCode": 1032,
    "ResultDesc": "Request cancelled by user"
  }
}
```

### **🔍 Testing & Verification**

#### **1. Test Callback Handler:**
```bash
# Test with curl (replace with your actual URL)
curl -X POST https://rika.ayubxxl.site/swift/callback.php \
  -H "Content-Type: application/json" \
  -H "User-Agent: SWIFT-WALLET-CALLBACK-FORWARDER/1.0" \
  -d '{"success":true,"transaction_id":123,"external_reference":"TEST-123","status":"completed"}'
```

#### **2. Monitor Logs:**
```bash
# Check callback logs
tail -f /var/www/html/swift/callback_log.txt

# Check transaction updates
cat /var/www/html/swift/callback_transactions.json
```

#### **3. Test Payment Flow:**
1. Run the terminal application
2. Initiate a small test payment (KES 1)
3. Watch the real-time animation
4. Complete or cancel the payment on your phone
5. Verify callback is received and processed

### **🛡️ Security Considerations**

#### **Callback Security:**
- ✅ HTTPS-only endpoint
- ✅ User-Agent verification
- ✅ JSON validation
- ✅ Input sanitization
- ✅ Error logging without sensitive data

#### **API Key Security:**
- ✅ Local storage in config.php
- ✅ Masked display in interface
- ✅ No logging of API keys

### **📈 Monitoring & Maintenance**

#### **Log Rotation:**
```bash
# Add to crontab for log rotation
0 0 * * 0 /usr/bin/find /var/www/html/swift/ -name "*.txt" -mtime +30 -delete
```

#### **Health Checks:**
- Monitor callback response times
- Check log file sizes
- Verify HTTPS certificate validity
- Test callback endpoint regularly

### **🚨 Troubleshooting**

#### **Common Issues:**

1. **Callback not received:**
   - Check HTTPS certificate
   - Verify URL accessibility
   - Check server logs for errors
   - Ensure proper file permissions

2. **Permission errors:**
   ```bash
   sudo chown -R www-data:www-data /var/www/html/swift/
   sudo chmod 755 /var/www/html/swift/
   sudo chmod 666 /var/www/html/swift/*.{txt,json}
   ```

3. **Timeout issues:**
   - Check server response time
   - Verify network connectivity
   - Monitor server resources

#### **Debug Mode:**
Enable detailed logging by checking the callback logs:
```bash
tail -f /var/www/html/swift/callback_log.txt
```

### **🎉 Ready to Use!**

Your M-Pesa STK Push GUI is now fully configured with:
- ✅ Real-time callback integration
- ✅ Professional terminal interface
- ✅ Comprehensive error handling
- ✅ Complete transaction tracking
- ✅ Secure webhook processing

**Start processing payments with confidence!** 🚀