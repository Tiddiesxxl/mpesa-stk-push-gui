<?php
/**
 * Demo script to test M-Pesa STK Push functionality
 * This script demonstrates the core validation functions
 */

// Include the main application class
require_once 'mpesa_stk_gui.php';

echo "M-Pesa STK Push - Demo & Test Script\n";
echo "====================================\n\n";

// Test phone number validation
echo "Testing Phone Number Validation:\n";
echo "---------------------------------\n";

$testNumbers = [
    '0798765432',    // Valid Safaricom (07XX)
    '0712345678',    // Valid Safaricom (07XX)
    '0112345678',    // Valid Safaricom (01XX)
    '254798765432',  // Valid international format
    '0798765',       // Invalid - too short
    '0898765432',    // Invalid - not Safaricom
    '254898765432',  // Invalid - not Safaricom international
    '0712-345-678',  // Valid with formatting
    '+254712345678', // Valid with country code
    '0101234567',    // Valid Safaricom (01XX)
];

// Create a reflection of the MpesaSTKGUI class to access private methods
$reflection = new ReflectionClass('MpesaSTKGUI');
$validateMethod = $reflection->getMethod('validateSafaricomNumber');
$validateMethod->setAccessible(true);
$formatMethod = $reflection->getMethod('formatPhoneNumber');
$formatMethod->setAccessible(true);

// Create an instance for testing
$gui = new MpesaSTKGUI();

foreach ($testNumbers as $number) {
    $isValid = $validateMethod->invoke($gui, $number);
    $formatted = $isValid ? $formatMethod->invoke($gui, $number) : 'N/A';
    
    printf("%-15s | %-8s | %s\n", 
        $number, 
        $isValid ? '✓ Valid' : '✗ Invalid',
        $formatted
    );
}

echo "\n";
echo "Valid Safaricom Prefixes:\n";
echo "-------------------------\n";
echo "• 0701-0729 (Safaricom mobile)\n";
echo "• 0740-0749 (Safaricom mobile)\n";
echo "• 0790-0799 (Safaricom mobile)\n\n";

echo "To run the full application:\n";
echo "1. Run: php mpesa_stk_gui.php\n";
echo "2. Or use the installer: ./install.sh\n";
echo "3. Then run: ./mpesa\n\n";

echo "Note: You'll need a valid SWIFT-WALLET API key to make actual payments.\n";
?>