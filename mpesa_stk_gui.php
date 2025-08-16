<?php
/**
 * M-Pesa STK Push Terminal GUI
 * A simple terminal-based interface for initiating M-Pesa payments via SWIFT-WALLET API
 */

class MpesaSTKGUI {
    private $apiKey;
    private $apiUrl = 'https://swiftwallet.co.ke/pay-app-v2/payments.php';
    private $callbackUrl = 'https://rika.ayubxxl.site/swift/callback.php';
    private $callbackTransactionFile = 'callback_transactions.json';
    
    public function __construct() {
        $this->clearScreen();
        $this->showHeader();
        $this->loadConfig();
    }
    
    private function clearScreen() {
        // Clear screen for both Unix and Windows
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            system('cls');
        } else {
            system('clear');
        }
    }
    
    private function showHeader() {
        echo "\033[1;34m"; // Blue color
        echo "╔══════════════════════════════════════════════════════════════╗\n";
        echo "║                    M-PESA STK PUSH GUI                       ║\n";
        echo "║                  Powered by SWIFT-WALLET                     ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n";
        echo "\033[0m"; // Reset color
        echo "\n";
    }
    
    private function loadConfig() {
        // Check if config file exists
        if (file_exists('config.php')) {
            include 'config.php';
            $this->apiKey = $apiKey ?? null;
            
            // Check if using sample config
            if ($this->apiKey === 'your_swift_wallet_api_key_here') {
                echo "\033[1;33m⚠️  You're using the sample configuration!\033[0m\n";
                echo "Please update config.php with your actual SWIFT-WALLET API key.\n\n";
                $this->apiKey = null;
            }
        } else {
            // Create config from sample if it doesn't exist
            if (file_exists('config.sample.php')) {
                echo "\033[1;33m📋 Creating config.php from sample...\033[0m\n";
                copy('config.sample.php', 'config.php');
                echo "✓ config.php created. Please edit it with your API key.\n\n";
            }
        }
        
        if (empty($this->apiKey)) {
            $this->setupApiKey();
        }
    }
    
    private function setupApiKey() {
        echo "\033[1;33m"; // Yellow color
        echo "⚠️  API Key Setup Required\n";
        echo "\033[0m"; // Reset color
        echo "Please enter your SWIFT-WALLET API Key: ";
        $this->apiKey = trim(fgets(STDIN));
        
        if (empty($this->apiKey)) {
            echo "\033[1;31mError: API Key cannot be empty!\033[0m\n";
            exit(1);
        }
        
        // Save API key to config file
        $configContent = "<?php\n\$apiKey = '" . addslashes($this->apiKey) . "';\n";
        file_put_contents('config.php', $configContent);
        
        echo "\033[1;32m✓ API Key saved successfully!\033[0m\n\n";
    }
    
    public function run() {
        while (true) {
            $this->showMenu();
            $choice = $this->getInput("Select an option: ");
            
            switch ($choice) {
                case '1':
                    $this->initiateSTKPush();
                    break;
                case '2':
                    $this->viewTransactionHistory();
                    break;
                case '3':
                    $this->showCallbackLogs();
                    break;
                case '4':
                    $this->checkAccountBalance();
                    break;
                case '5':
                    $this->bulkPaymentUpload();
                    break;
                case '6':
                    $this->updateApiKey();
                    break;
                case '7':
                    $this->showErrorCodes();
                    break;
                case '8':
                    $this->showHelp();
                    break;
                case '9':
                    echo "\033[1;32mThank you for using M-Pesa STK Push GUI!\033[0m\n";
                    exit(0);
                default:
                    echo "\033[1;31mInvalid option. Please try again.\033[0m\n";
            }
            
            echo "\nPress Enter to continue...";
            fgets(STDIN);
            $this->clearScreen();
            $this->showHeader();
        }
    }
    
    private function showMenu() {
        echo "\033[1;36m"; // Cyan color
        echo "┌─────────────────────────────────────────────────────────────┐\n";
        echo "│                        MAIN MENU                           │\n";
        echo "├─────────────────────────────────────────────────────────────┤\n";
        echo "│  1. Initiate STK Push Payment                               │\n";
        echo "│  2. View Transaction History                                │\n";
        echo "│  3. View Callback Logs                                     │\n";
        echo "│  4. Check Account Balance                                   │\n";
        echo "│  5. Bulk Payment Upload                                     │\n";
        echo "│  6. Update API Key                                          │\n";
        echo "│  7. Error Code Reference                                    │\n";
        echo "│  8. Help & Documentation                                    │\n";
        echo "│  9. Exit                                                    │\n";
        echo "└─────────────────────────────────────────────────────────────┘\n";
        echo "\033[0m"; // Reset color
        echo "\n";
    }
    
    private function initiateSTKPush() {
        echo "\033[1;35m"; // Magenta color
        echo "╔══════════════════════════════════════════════════════════════╗\n";
        echo "║                    INITIATE STK PUSH                         ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n";
        echo "\033[0m"; // Reset color
        echo "\n";
        
        // Get phone number with validation
        $phoneNumber = $this->getValidPhoneNumber();
        
        // Get amount
        $amount = $this->getValidAmount();
        
        // Get optional details
        $customerName = $this->getInput("Customer Name (optional): ");
        $reference = $this->getInput("External Reference (optional): ");
        
        if (empty($reference)) {
            $reference = 'STK-' . date('YmdHis') . '-' . rand(1000, 9999);
        }
        
        // Confirm transaction details
        $this->showPaymentSummary($phoneNumber, $amount, $customerName, $reference);
        
        $confirm = strtolower($this->getInput("Confirm transaction? (y/n): "));
        
        if ($confirm === 'y' || $confirm === 'yes') {
            $this->processSTKPush($phoneNumber, $amount, $customerName, $reference);
        } else {
            echo "\033[1;33mTransaction cancelled.\033[0m\n";
        }
    }
    
    private function getValidPhoneNumber() {
        while (true) {
            $phone = $this->getInput("Enter Safaricom phone number (01XXXXXXXX, 07XXXXXXXX or 254XXXXXXXXX): ");
            
            if ($this->validateSafaricomNumber($phone)) {
                return $this->formatPhoneNumber($phone);
            }
            
            echo "\033[1;31mInvalid Safaricom number! Please use format 01XXXXXXXX, 07XXXXXXXX or 254XXXXXXXXX\033[0m\n";
        }
    }
    
    private function validateSafaricomNumber($phone) {
        // Remove any spaces or special characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Check for valid Safaricom prefixes (including 01XX series)
        $safaricomPrefixes = [
            // 01XX series
            '0100', '0101', '0102', '0103', '0104', '0105', '0106', '0107', '0108', '0109',
            '0110', '0111', '0112', '0113', '0114', '0115', '0116', '0117', '0118', '0119',
            '0120', '0121', '0122', '0123', '0124', '0125', '0126', '0127', '0128', '0129',
            // 07XX series
            '0701', '0702', '0703', '0704', '0705', '0706', '0707', '0708', '0709', 
            '0710', '0711', '0712', '0713', '0714', '0715', '0716', '0717', '0718', 
            '0719', '0720', '0721', '0722', '0723', '0724', '0725', '0726', '0727', 
            '0728', '0729',
            // 074X series
            '0740', '0741', '0742', '0743', '0744', '0745', '0746', '0747', '0748', '0749',
            // 079X series
            '0790', '0791', '0792', '0793', '0794', '0795', '0796', '0797', '0798', '0799'
        ];
        
        // Format 0XXXXXXXXX (10 digits starting with 01 or 07)
        if (strlen($phone) == 10 && (substr($phone, 0, 2) == '01' || substr($phone, 0, 2) == '07')) {
            $prefix = substr($phone, 0, 4);
            return in_array($prefix, $safaricomPrefixes);
        }
        
        // Format 254XXXXXXXXX (12 digits)
        if (strlen($phone) == 12 && substr($phone, 0, 3) == '254') {
            $prefix = '0' . substr($phone, 3, 3);
            return in_array($prefix, $safaricomPrefixes);
        }
        
        return false;
    }
    
    private function formatPhoneNumber($phone) {
        // Remove any spaces or special characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Convert 254XXXXXXXXX to 07XXXXXXXX format
        if (strlen($phone) == 12 && substr($phone, 0, 3) == '254') {
            return '0' . substr($phone, 3);
        }
        
        return $phone;
    }
    
    private function getValidAmount() {
        while (true) {
            $amount = $this->getInput("Enter amount (KES, minimum 1): ");
            
            if (is_numeric($amount) && $amount >= 1) {
                return (int)$amount;
            }
            
            echo "\033[1;31mInvalid amount! Please enter a number greater than or equal to 1.\033[0m\n";
        }
    }
    
    private function showPaymentSummary($phone, $amount, $customerName, $reference) {
        echo "\n\033[1;36m"; // Cyan color
        echo "┌─────────────────────────────────────────────────────────────┐\n";
        echo "│                   TRANSACTION SUMMARY                       │\n";
        echo "├─────────────────────────────────────────────────────────────┤\n";
        printf("│  Phone Number: %-40s │\n", $phone);
        printf("│  Amount: KES %-38s │\n", number_format($amount));
        printf("│  Customer: %-42s │\n", $customerName ?: 'N/A');
        printf("│  Reference: %-41s │\n", $reference);
        echo "└─────────────────────────────────────────────────────────────┘\n";
        echo "\033[0m"; // Reset color
        echo "\n";
    }
    
    private function processSTKPush($phoneNumber, $amount, $customerName, $reference) {
        echo "\033[1;33mProcessing STK Push...\033[0m\n";
        
        $data = [
            'amount' => $amount,
            'phone_number' => $phoneNumber,
            'external_reference' => $reference,
            'callback_url' => $this->callbackUrl
        ];
        
        if (!empty($customerName)) {
            $data['customer_name'] = $customerName;
        }
        
        $response = $this->makeAPIRequest($data);
        
        if ($response['success']) {
            $this->logTransaction($phoneNumber, $amount, $customerName, $reference, 'initiated', $response['data']);
            $this->showSuccessMessage($response['data']);
            
            // Wait for callback with animation
            $this->waitForCallback($reference, $phoneNumber, $amount);
        } else {
            $this->showErrorMessage($response);
        }
    }
    
    private function makeAPIRequest($data) {
        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            return [
                'success' => false, 
                'error' => 'Network Error: ' . $error,
                'error_code' => 'NETWORK_ERROR',
                'http_code' => 0
            ];
        }
        
        $result = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'success' => false, 
                'error' => 'Invalid JSON response from server',
                'error_code' => 'INVALID_RESPONSE',
                'http_code' => $httpCode
            ];
        }
        
        // Handle different HTTP status codes
        switch ($httpCode) {
            case 200:
                if (isset($result['success']) && $result['success']) {
                    return ['success' => true, 'data' => $result, 'http_code' => $httpCode];
                } else {
                    return [
                        'success' => false, 
                        'error' => $result['error'] ?? 'Request failed',
                        'error_code' => 'REQUEST_FAILED',
                        'http_code' => $httpCode
                    ];
                }
                
            case 201:
                return ['success' => true, 'data' => $result, 'http_code' => $httpCode];
                
            case 400:
                return [
                    'success' => false,
                    'error' => 'Bad Request: ' . ($result['error'] ?? 'Invalid request parameters'),
                    'error_code' => 'BAD_REQUEST',
                    'http_code' => $httpCode,
                    'details' => $result['details'] ?? 'Check your input parameters'
                ];
                
            case 401:
                return [
                    'success' => false,
                    'error' => 'Unauthorized: Invalid or missing API key',
                    'error_code' => 'UNAUTHORIZED',
                    'http_code' => $httpCode,
                    'details' => 'Please check your API key in the settings'
                ];
                
            case 402:
                return [
                    'success' => false,
                    'error' => 'Payment Required: Insufficient service wallet balance',
                    'error_code' => 'INSUFFICIENT_BALANCE',
                    'http_code' => $httpCode,
                    'details' => 'Please top up your SWIFT-WALLET account'
                ];
                
            case 500:
                return [
                    'success' => false,
                    'error' => 'Internal Server Error: Server error occurred',
                    'error_code' => 'SERVER_ERROR',
                    'http_code' => $httpCode,
                    'details' => 'Please try again later or contact support'
                ];
                
            default:
                return [
                    'success' => false,
                    'error' => 'HTTP Error ' . $httpCode . ': ' . ($result['error'] ?? 'Unknown error'),
                    'error_code' => 'HTTP_ERROR_' . $httpCode,
                    'http_code' => $httpCode
                ];
        }
    }
    
    private function showSuccessMessage($data) {
        echo "\n\033[1;32m"; // Green color
        echo "╔══════════════════════════════════════════════════════════════╗\n";
        echo "║                    ✓ SUCCESS                                ║\n";
        echo "╠══════════════════════════════════════════════════════════════╣\n";
        printf("║  STK Push initiated successfully!                           ║\n");
        printf("║  Reference: %-46s ║\n", $data['reference'] ?? 'N/A');
        printf("║  Transaction ID: %-40s ║\n", $data['transaction_id'] ?? 'N/A');
        echo "║                                                              ║\n";
        echo "║  The customer will receive a prompt on their phone to       ║\n";
        echo "║  enter their M-Pesa PIN to complete the payment.            ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n";
        echo "\033[0m"; // Reset color
    }
    
    private function showErrorMessage($response) {
        $error = is_array($response) ? $response['error'] : $response;
        $errorCode = is_array($response) ? ($response['error_code'] ?? 'UNKNOWN') : 'UNKNOWN';
        $httpCode = is_array($response) ? ($response['http_code'] ?? 0) : 0;
        $details = is_array($response) ? ($response['details'] ?? '') : '';
        
        echo "\n\033[1;31m"; // Red color
        echo "╔══════════════════════════════════════════════════════════════╗\n";
        echo "║                    ✗ ERROR                                  ║\n";
        echo "╠══════════════════════════════════════════════════════════════╣\n";
        
        // Error message
        $errorLines = explode("\n", wordwrap($error, 58));
        foreach ($errorLines as $line) {
            printf("║  %-58s ║\n", $line);
        }
        
        // Error details
        if (!empty($details)) {
            echo "║                                                              ║\n";
            echo "║  \033[1;33mDetails:\033[1;31m                                             ║\n";
            $detailLines = explode("\n", wordwrap($details, 58));
            foreach ($detailLines as $line) {
                printf("║  %-58s ║\n", $line);
            }
        }
        
        // Error code and HTTP status
        echo "║                                                              ║\n";
        printf("║  Error Code: %-45s ║\n", $errorCode);
        if ($httpCode > 0) {
            printf("║  HTTP Status: %-44s ║\n", $httpCode);
        }
        
        echo "╚══════════════════════════════════════════════════════════════╝\n";
        echo "\033[0m"; // Reset color
        
        // Show helpful suggestions based on error code
        $this->showErrorSuggestions($errorCode);
    }
    
    private function showErrorSuggestions($errorCode) {
        echo "\n\033[1;33m💡 Suggestions:\033[0m\n";
        
        switch ($errorCode) {
            case 'UNAUTHORIZED':
                echo "• Check your API key in the settings (Option 5)\n";
                echo "• Verify your SWIFT-WALLET account is active\n";
                echo "• Ensure you copied the API key correctly\n";
                break;
                
            case 'INSUFFICIENT_BALANCE':
                echo "• Top up your SWIFT-WALLET account balance\n";
                echo "• Check your account balance (Option 3)\n";
                echo "• Contact SWIFT-WALLET support if balance seems incorrect\n";
                break;
                
            case 'BAD_REQUEST':
                echo "• Verify the phone number format is correct\n";
                echo "• Check that the amount is valid (minimum KES 1)\n";
                echo "• Ensure all required fields are filled\n";
                break;
                
            case 'NETWORK_ERROR':
                echo "• Check your internet connection\n";
                echo "• Try again in a few moments\n";
                echo "• Verify firewall settings allow HTTPS connections\n";
                break;
                
            case 'SERVER_ERROR':
                echo "• This is a temporary server issue\n";
                echo "• Try again in a few minutes\n";
                echo "• Contact SWIFT-WALLET support if problem persists\n";
                break;
                
            default:
                echo "• Check the Error Code Reference (Option 6) for more details\n";
                echo "• Try the operation again\n";
                echo "• Contact support if the problem continues\n";
        }
        echo "\n";
    }
    
    private function logTransaction($phone, $amount, $customer, $reference, $status, $data = null) {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'phone' => $phone,
            'amount' => $amount,
            'customer' => $customer,
            'reference' => $reference,
            'status' => $status,
            'data' => $data
        ];
        
        // Add error information if available
        if (is_array($data) && isset($data['error_code'])) {
            $logEntry['error_code'] = $data['error_code'];
            $logEntry['http_code'] = $data['http_code'] ?? null;
            $logEntry['error_message'] = $data['error'] ?? null;
        }
        
        $logFile = 'transactions.log';
        file_put_contents($logFile, json_encode($logEntry) . "\n", FILE_APPEND | LOCK_EX);
    }
    
    private function viewTransactionHistory() {
        echo "\033[1;35m"; // Magenta color
        echo "╔══════════════════════════════════════════════════════════════╗\n";
        echo "║                  TRANSACTION HISTORY                         ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n";
        echo "\033[0m"; // Reset color
        echo "\n";
        
        $logFile = 'transactions.log';
        
        if (!file_exists($logFile)) {
            echo "\033[1;33mNo transaction history found.\033[0m\n";
            return;
        }
        
        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $transactions = array_reverse($lines); // Show latest first
        
        if (empty($transactions)) {
            echo "\033[1;33mNo transactions found.\033[0m\n";
            return;
        }
        
        echo "\033[1;36mTransaction History Options:\033[0m\n";
        echo "1. View summary (last 10)\n";
        echo "2. View detailed history\n";
        echo "3. Filter by status\n";
        echo "4. Export to CSV\n\n";
        
        $option = $this->getInput("Select option (1-4): ");
        
        switch ($option) {
            case '1':
                $this->showTransactionSummary($transactions);
                break;
            case '2':
                $this->showDetailedHistory($transactions);
                break;
            case '3':
                $this->filterTransactionsByStatus($transactions);
                break;
            case '4':
                $this->exportTransactionsToCSV($transactions);
                break;
            default:
                $this->showTransactionSummary($transactions);
        }
    }
    
    private function showTransactionSummary($transactions) {
        echo "\n\033[1;36mLast 10 Transactions:\033[0m\n";
        echo "┌─────┬─────────────────────┬─────────────┬──────────┬───────────┐\n";
        echo "│ \033[1;33m#\033[0m   │ \033[1;33mTimestamp\033[0m           │ \033[1;33mPhone\033[0m       │ \033[1;33mAmount\033[0m   │ \033[1;33mStatus\033[0m    │\n";
        echo "├─────┼─────────────────────┼─────────────┼──────────┼───────────┤\n";
        
        $count = 0;
        foreach ($transactions as $line) {
            $transaction = json_decode($line, true);
            if ($transaction && $count < 10) {
                $count++;
                $status = strtoupper($transaction['status']);
                $statusColor = $status === 'INITIATED' ? "\033[1;32m" : ($status === 'FAILED' ? "\033[1;31m" : "\033[1;33m");
                
                printf("│ %-3d │ %-19s │ %-11s │ %-8s │ %s%-9s\033[0m │\n",
                    $count,
                    $transaction['timestamp'],
                    $transaction['phone'],
                    'KES ' . number_format($transaction['amount']),
                    $statusColor,
                    $status
                );
            }
        }
        
        echo "└─────┴─────────────────────┴─────────────┴──────────┴───────────┘\n";
        
        if (count($transactions) > 10) {
            echo "\033[1;33m... (" . (count($transactions) - 10) . " more transactions available)\033[0m\n";
        }
    }
    
    private function showDetailedHistory($transactions) {
        $limit = min(5, count($transactions));
        echo "\n\033[1;36mDetailed Transaction History (Last $limit):\033[0m\n";
        
        $count = 0;
        foreach ($transactions as $line) {
            $transaction = json_decode($line, true);
            if ($transaction && $count < $limit) {
                $count++;
                echo "\n\033[1;36m[$count] Transaction Details:\033[0m\n";
                echo "• Timestamp: " . $transaction['timestamp'] . "\n";
                echo "• Phone: " . $transaction['phone'] . "\n";
                echo "• Amount: KES " . number_format($transaction['amount']) . "\n";
                echo "• Customer: " . ($transaction['customer'] ?: 'N/A') . "\n";
                echo "• Reference: " . $transaction['reference'] . "\n";
                
                $status = strtoupper($transaction['status']);
                $statusColor = $status === 'INITIATED' ? "\033[1;32m" : ($status === 'FAILED' ? "\033[1;31m" : "\033[1;33m");
                echo "• Status: " . $statusColor . $status . "\033[0m\n";
                
                if (isset($transaction['error_code'])) {
                    echo "• Error Code: \033[1;31m" . $transaction['error_code'] . "\033[0m\n";
                    if (isset($transaction['http_code'])) {
                        echo "• HTTP Code: " . $transaction['http_code'] . "\n";
                    }
                    if (isset($transaction['error_message'])) {
                        echo "• Error: " . $transaction['error_message'] . "\n";
                    }
                }
                
                echo "─────────────────────────────────────────────────────────────\n";
            }
        }
    }
    
    private function filterTransactionsByStatus($transactions) {
        echo "\n\033[1;36mFilter by Status:\033[0m\n";
        echo "1. Initiated (successful)\n";
        echo "2. Failed\n";
        echo "3. All\n\n";
        
        $filter = $this->getInput("Select filter (1-3): ");
        $statusFilter = '';
        
        switch ($filter) {
            case '1':
                $statusFilter = 'initiated';
                break;
            case '2':
                $statusFilter = 'failed';
                break;
            default:
                $statusFilter = 'all';
        }
        
        $filtered = [];
        foreach ($transactions as $line) {
            $transaction = json_decode($line, true);
            if ($transaction && ($statusFilter === 'all' || $transaction['status'] === $statusFilter)) {
                $filtered[] = $transaction;
            }
        }
        
        if (empty($filtered)) {
            echo "\033[1;33mNo transactions found with status: $statusFilter\033[0m\n";
            return;
        }
        
        echo "\n\033[1;36mFiltered Results (" . count($filtered) . " transactions):\033[0m\n";
        foreach (array_slice($filtered, 0, 10) as $index => $transaction) {
            $status = strtoupper($transaction['status']);
            $statusColor = $status === 'INITIATED' ? "\033[1;32m" : "\033[1;31m";
            
            echo ($index + 1) . ". " . $transaction['timestamp'] . " | ";
            echo $transaction['phone'] . " | ";
            echo "KES " . number_format($transaction['amount']) . " | ";
            echo $statusColor . $status . "\033[0m\n";
        }
    }
    
    private function exportTransactionsToCSV($transactions) {
        $filename = 'transaction_export_' . date('Y-m-d_H-i-s') . '.csv';
        $handle = fopen($filename, 'w');
        
        // CSV header
        fputcsv($handle, ['Timestamp', 'Phone', 'Amount', 'Customer', 'Reference', 'Status', 'Error Code', 'HTTP Code']);
        
        foreach ($transactions as $line) {
            $transaction = json_decode($line, true);
            if ($transaction) {
                fputcsv($handle, [
                    $transaction['timestamp'],
                    $transaction['phone'],
                    $transaction['amount'],
                    $transaction['customer'] ?? '',
                    $transaction['reference'],
                    $transaction['status'],
                    $transaction['error_code'] ?? '',
                    $transaction['http_code'] ?? ''
                ]);
            }
        }
        
        fclose($handle);
        echo "\033[1;32m✓ Transactions exported to: $filename\033[0m\n";
        echo "Total records: " . count($transactions) . "\n";
    }
    
    private function checkAccountBalance() {
        echo "\033[1;35m"; // Magenta color
        echo "╔══════════════════════════════════════════════════════════════╗\n";
        echo "║                   CHECK ACCOUNT BALANCE                      ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n";
        echo "\033[0m"; // Reset color
        echo "\n";
        
        echo "\033[1;33mChecking account balance...\033[0m\n";
        
        // Note: This would require a balance endpoint from SWIFT-WALLET
        // For now, we'll show a placeholder message
        echo "\033[1;33m⚠️  Balance check feature requires additional API endpoint\033[0m\n";
        echo "Please check your balance directly in the SWIFT-WALLET dashboard:\n";
        echo "🌐 https://swiftwallet.co.ke/dashboard\n\n";
        
        echo "\033[1;36mAccount Information:\033[0m\n";
        echo "• API Key: " . substr($this->apiKey, 0, 10) . "...\n";
        echo "• Status: Connected\n";
        echo "• Last Activity: " . date('Y-m-d H:i:s') . "\n";
    }
    
    private function bulkPaymentUpload() {
        echo "\033[1;35m"; // Magenta color
        echo "╔══════════════════════════════════════════════════════════════╗\n";
        echo "║                   BULK PAYMENT UPLOAD                       ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n";
        echo "\033[0m"; // Reset color
        echo "\n";
        
        echo "\033[1;36mBulk Payment Format (CSV):\033[0m\n";
        echo "phone_number,amount,customer_name,reference\n";
        echo "0712345678,1500,John Doe,ORDER-001\n";
        echo "0798765432,2000,Jane Smith,ORDER-002\n\n";
        
        $csvFile = $this->getInput("Enter CSV file path (or 'create' to create sample): ");
        
        if (strtolower($csvFile) === 'create') {
            $this->createSampleCSV();
            return;
        }
        
        if (!file_exists($csvFile)) {
            echo "\033[1;31mFile not found: $csvFile\033[0m\n";
            return;
        }
        
        $this->processBulkPayments($csvFile);
    }
    
    private function createSampleCSV() {
        $sampleData = "phone_number,amount,customer_name,reference\n";
        $sampleData .= "0712345678,1500,John Doe,ORDER-001\n";
        $sampleData .= "0798765432,2000,Jane Smith,ORDER-002\n";
        $sampleData .= "0101234567,1000,Bob Wilson,ORDER-003\n";
        
        $filename = 'sample_bulk_payments.csv';
        file_put_contents($filename, $sampleData);
        
        echo "\033[1;32m✓ Sample CSV file created: $filename\033[0m\n";
        echo "Edit this file with your payment data and upload it using this option.\n";
    }
    
    private function processBulkPayments($csvFile) {
        $handle = fopen($csvFile, 'r');
        if (!$handle) {
            echo "\033[1;31mCannot open file: $csvFile\033[0m\n";
            return;
        }
        
        // Skip header row
        $header = fgetcsv($handle);
        
        $payments = [];
        $lineNumber = 2;
        
        while (($data = fgetcsv($handle)) !== FALSE) {
            if (count($data) >= 2) {
                $phone = trim($data[0]);
                $amount = trim($data[1]);
                $customer = isset($data[2]) ? trim($data[2]) : '';
                $reference = isset($data[3]) ? trim($data[3]) : 'BULK-' . date('YmdHis') . '-' . $lineNumber;
                
                if ($this->validateSafaricomNumber($phone) && is_numeric($amount) && $amount >= 1) {
                    $payments[] = [
                        'phone' => $this->formatPhoneNumber($phone),
                        'amount' => (int)$amount,
                        'customer' => $customer,
                        'reference' => $reference,
                        'line' => $lineNumber
                    ];
                } else {
                    echo "\033[1;33mSkipping invalid entry on line $lineNumber: $phone, $amount\033[0m\n";
                }
            }
            $lineNumber++;
        }
        
        fclose($handle);
        
        if (empty($payments)) {
            echo "\033[1;31mNo valid payments found in CSV file.\033[0m\n";
            return;
        }
        
        echo "\033[1;36mFound " . count($payments) . " valid payments.\033[0m\n";
        $confirm = strtolower($this->getInput("Process all payments? (y/n): "));
        
        if ($confirm === 'y' || $confirm === 'yes') {
            $this->executeBulkPayments($payments);
        } else {
            echo "\033[1;33mBulk payment cancelled.\033[0m\n";
        }
    }
    
    private function executeBulkPayments($payments) {
        $successful = 0;
        $failed = 0;
        
        echo "\n\033[1;36mProcessing bulk payments...\033[0m\n";
        echo "Progress: ";
        
        foreach ($payments as $index => $payment) {
            $data = [
                'amount' => $payment['amount'],
                'phone_number' => $payment['phone'],
                'external_reference' => $payment['reference']
            ];
            
            if (!empty($payment['customer'])) {
                $data['customer_name'] = $payment['customer'];
            }
            
            $response = $this->makeAPIRequest($data);
            
            if ($response['success']) {
                $successful++;
                echo "\033[1;32m✓\033[0m";
                $this->logTransaction($payment['phone'], $payment['amount'], $payment['customer'], $payment['reference'], 'initiated', $response['data']);
            } else {
                $failed++;
                echo "\033[1;31m✗\033[0m";
                $this->logTransaction($payment['phone'], $payment['amount'], $payment['customer'], $payment['reference'], 'failed', $response);
            }
            
            // Small delay to avoid overwhelming the API
            usleep(500000); // 0.5 seconds
        }
        
        echo "\n\n\033[1;36mBulk Payment Summary:\033[0m\n";
        echo "• Total Payments: " . count($payments) . "\n";
        echo "• Successful: \033[1;32m$successful\033[0m\n";
        echo "• Failed: \033[1;31m$failed\033[0m\n";
        
        if ($failed > 0) {
            echo "\nCheck transaction history for failed payment details.\n";
        }
    }
    
    private function showErrorCodes() {
        echo "\033[1;35m"; // Magenta color
        echo "╔══════════════════════════════════════════════════════════════╗\n";
        echo "║                   ERROR CODE REFERENCE                      ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n";
        echo "\033[0m"; // Reset color
        echo "\n";
        
        echo "\033[1;36mHTTP Status Codes:\033[0m\n";
        echo "┌──────────┬─────────────────┬─────────────────────────────────┐\n";
        echo "│ \033[1;33mCode\033[0m     │ \033[1;33mMeaning\033[0m         │ \033[1;33mDescription\033[0m                     │\n";
        echo "├──────────┼─────────────────┼─────────────────────────────────┤\n";
        echo "│ 200      │ OK              │ Request processed successfully  │\n";
        echo "│ 201      │ Created         │ Payment initiated successfully  │\n";
        echo "│ 400      │ Bad Request     │ Invalid request parameters      │\n";
        echo "│ 401      │ Unauthorized    │ Invalid or missing API key      │\n";
        echo "│ 402      │ Payment Req.    │ Insufficient wallet balance     │\n";
        echo "│ 500      │ Server Error    │ Server error occurred           │\n";
        echo "└──────────┴─────────────────┴─────────────────────────────────┘\n\n";
        
        echo "\033[1;36mApplication Error Codes:\033[0m\n";
        echo "• \033[1;33mNETWORK_ERROR\033[0m - Connection or network issues\n";
        echo "• \033[1;33mINVALID_RESPONSE\033[0m - Server returned invalid data\n";
        echo "• \033[1;33mREQUEST_FAILED\033[0m - Request failed for unknown reason\n";
        echo "• \033[1;33mBAD_REQUEST\033[0m - Invalid input parameters\n";
        echo "• \033[1;33mUNAUTHORIZED\033[0m - API key authentication failed\n";
        echo "• \033[1;33mINSUFFICIENT_BALANCE\033[0m - Not enough funds in wallet\n";
        echo "• \033[1;33mSERVER_ERROR\033[0m - SWIFT-WALLET server issues\n\n";
        
        echo "\033[1;36mTroubleshooting Tips:\033[0m\n";
        echo "1. \033[1;33m401 Unauthorized\033[0m - Check API key in settings\n";
        echo "2. \033[1;33m402 Payment Required\033[0m - Top up your wallet balance\n";
        echo "3. \033[1;33m400 Bad Request\033[0m - Verify phone number and amount\n";
        echo "4. \033[1;33m500 Server Error\033[0m - Try again later\n";
        echo "5. \033[1;33mNetwork errors\033[0m - Check internet connection\n";
    }
    
    private function updateApiKey() {
        echo "\033[1;35m"; // Magenta color
        echo "╔══════════════════════════════════════════════════════════════╗\n";
        echo "║                    UPDATE API KEY                            ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n";
        echo "\033[0m"; // Reset color
        echo "\n";
        
        echo "Current API Key: " . substr($this->apiKey, 0, 10) . "...\n\n";
        $newApiKey = $this->getInput("Enter new API Key: ");
        
        if (!empty($newApiKey)) {
            $this->apiKey = $newApiKey;
            
            // Save new API key to config file
            $configContent = "<?php\n\$apiKey = '" . addslashes($this->apiKey) . "';\n";
            file_put_contents('config.php', $configContent);
            
            echo "\033[1;32m✓ API Key updated successfully!\033[0m\n";
        } else {
            echo "\033[1;31mAPI Key update cancelled.\033[0m\n";
        }
    }
    
    private function showHelp() {
        echo "\033[1;35m"; // Magenta color
        echo "╔══════════════════════════════════════════════════════════════╗\n";
        echo "║                    HELP & DOCUMENTATION                      ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n";
        echo "\033[0m"; // Reset color
        echo "\n";
        
        echo "\033[1;36mSupported Phone Number Formats:\033[0m\n";
        echo "• 01XXXXXXXX (10 digits starting with 01)\n";
        echo "• 07XXXXXXXX (10 digits starting with 07)\n";
        echo "• 254XXXXXXXXX (12 digits starting with 254)\n\n";
        
        echo "\033[1;36mValid Safaricom Prefixes:\033[0m\n";
        echo "• 0100-0129 (Safaricom services)\n";
        echo "• 0701-0729 (Safaricom mobile)\n";
        echo "• 0740-0749 (Safaricom mobile)\n";
        echo "• 0790-0799 (Safaricom mobile)\n\n";
        
        echo "\033[1;36mTransaction Process:\033[0m\n";
        echo "1. Enter valid Safaricom number\n";
        echo "2. Enter amount (minimum KES 1)\n";
        echo "3. Optionally enter customer name and reference\n";
        echo "4. Confirm transaction details\n";
        echo "5. STK Push is sent to customer's phone\n";
        echo "6. Real-time waiting animation with callback monitoring\n";
        echo "7. Customer enters M-Pesa PIN to complete payment\n";
        echo "8. Automatic callback confirmation and receipt display\n\n";
        
        echo "\033[1;36mCallback System:\033[0m\n";
        echo "• Callback URL: " . $this->callbackUrl . "\n";
        echo "• Real-time payment status updates\n";
        echo "• Automatic receipt generation\n";
        echo "• Service fee tracking\n";
        echo "• M-Pesa receipt number capture\n";
        echo "• 2-minute timeout with graceful handling\n\n";
        
        echo "\033[1;36mRequirements:\033[0m\n";
        echo "• Valid SWIFT-WALLET API Key\n";
        echo "• Active internet connection\n";
        echo "• Sufficient wallet balance for transaction fees\n\n";
        
        echo "\033[1;36mSupport:\033[0m\n";
        echo "• Visit: https://swiftwallet.co.ke\n";
        echo "• Check your dashboard for API keys and settings\n";
    }
    
    private function waitForCallback($reference, $phoneNumber, $amount) {
        echo "\n\033[1;36m"; // Cyan color
        echo "╔══════════════════════════════════════════════════════════════╗\n";
        echo "║                 WAITING FOR PAYMENT CONFIRMATION            ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n";
        echo "\033[0m"; // Reset color
        echo "\n";
        
        echo "\033[1;33m📱 Customer should now see M-Pesa prompt on their phone\033[0m\n";
        echo "Reference: $reference\n";
        echo "Phone: $phoneNumber\n";
        echo "Amount: KES " . number_format($amount) . "\n\n";
        
        echo "Waiting for payment confirmation";
        
        $maxWaitTime = 120; // 2 minutes
        $checkInterval = 2; // Check every 2 seconds
        $elapsed = 0;
        $animationChars = ['⠋', '⠙', '⠹', '⠸', '⠼', '⠴', '⠦', '⠧', '⠇', '⠏'];
        $animationIndex = 0;
        
        while ($elapsed < $maxWaitTime) {
            // Show spinning animation
            echo "\r\033[1;33mWaiting for payment confirmation " . $animationChars[$animationIndex] . " (" . ($maxWaitTime - $elapsed) . "s remaining)\033[0m";
            
            // Check for callback
            $callbackStatus = $this->checkCallbackStatus($reference);
            
            if ($callbackStatus) {
                echo "\r\033[K"; // Clear the line
                $this->showCallbackResult($callbackStatus, $reference);
                return;
            }
            
            sleep($checkInterval);
            $elapsed += $checkInterval;
            $animationIndex = ($animationIndex + 1) % count($animationChars);
        }
        
        // Timeout reached
        echo "\r\033[K"; // Clear the line
        echo "\n\033[1;33m⏰ Timeout reached. Payment may still be processing.\033[0m\n";
        echo "You can check the transaction status later in the transaction history.\n";
        echo "Reference: $reference\n";
    }
    
    private function checkCallbackStatus($reference) {
        if (!file_exists($this->callbackTransactionFile)) {
            return false;
        }
        
        $content = file_get_contents($this->callbackTransactionFile);
        $transactions = json_decode($content, true);
        
        if (!$transactions || !isset($transactions[$reference])) {
            return false;
        }
        
        return $transactions[$reference];
    }
    
    private function showCallbackResult($callbackData, $reference) {
        if ($callbackData['status'] === 'completed') {
            echo "\n\033[1;32m"; // Green color
            echo "╔══════════════════════════════════════════════════════════════╗\n";
            echo "║                    ✅ PAYMENT SUCCESSFUL                     ║\n";
            echo "╠══════════════════════════════════════════════════════════════╣\n";
            printf("║  Reference: %-46s ║\n", $reference);
            printf("║  M-Pesa Receipt: %-40s ║\n", $callbackData['mpesa_receipt'] ?? 'N/A');
            printf("║  Amount: KES %-42s ║\n", number_format($callbackData['amount'] ?? 0));
            printf("║  Service Fee: KES %-37s ║\n", number_format($callbackData['service_fee'] ?? 0));
            printf("║  Channel: %-45s ║\n", $callbackData['channel_info']['channel_name'] ?? 'Default');
            echo "║                                                              ║\n";
            echo "║  🎉 Payment has been confirmed by M-Pesa!                   ║\n";
            echo "╚══════════════════════════════════════════════════════════════╝\n";
            echo "\033[0m"; // Reset color
            
            // Update local transaction log
            $this->logTransaction(
                '', // Phone not needed for update
                $callbackData['amount'] ?? 0,
                '',
                $reference,
                'completed',
                $callbackData
            );
            
        } else {
            echo "\n\033[1;31m"; // Red color
            echo "╔══════════════════════════════════════════════════════════════╗\n";
            echo "║                    ❌ PAYMENT FAILED                         ║\n";
            echo "╠══════════════════════════════════════════════════════════════╣\n";
            printf("║  Reference: %-46s ║\n", $reference);
            printf("║  Reason: %-49s ║\n", $callbackData['result_desc'] ?? 'Unknown error');
            printf("║  Service Fee: KES %-37s ║\n", number_format($callbackData['service_fee'] ?? 0));
            echo "║                                                              ║\n";
            echo "║  The customer cancelled or failed to complete payment.      ║\n";
            echo "╚══════════════════════════════════════════════════════════════╝\n";
            echo "\033[0m"; // Reset color
            
            // Update local transaction log
            $this->logTransaction(
                '', // Phone not needed for update
                $callbackData['amount'] ?? 0,
                '',
                $reference,
                'failed',
                $callbackData
            );
        }
        
        echo "\nCallback received at: " . ($callbackData['updated_at'] ?? 'Unknown') . "\n";
    }
    
    private function showCallbackLogs() {
        echo "\033[1;35m"; // Magenta color
        echo "╔══════════════════════════════════════════════════════════════╗\n";
        echo "║                    CALLBACK LOGS                             ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n";
        echo "\033[0m"; // Reset color
        echo "\n";
        
        $logFile = 'callback_log.txt';
        
        if (!file_exists($logFile)) {
            echo "\033[1;33mNo callback logs found.\033[0m\n";
            echo "Callback URL: " . $this->callbackUrl . "\n";
            return;
        }
        
        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $recentLines = array_slice(array_reverse($lines), 0, 20); // Show last 20 entries
        
        echo "\033[1;36mRecent Callback Activity (Last 20 entries):\033[0m\n";
        echo "Callback URL: " . $this->callbackUrl . "\n\n";
        
        foreach ($recentLines as $index => $line) {
            // Color code different types of log entries
            if (strpos($line, 'SUCCESS') !== false) {
                echo "\033[1;32m" . ($index + 1) . ". " . $line . "\033[0m\n";
            } elseif (strpos($line, 'FAILED') !== false) {
                echo "\033[1;31m" . ($index + 1) . ". " . $line . "\033[0m\n";
            } elseif (strpos($line, 'ERROR') !== false || strpos($line, 'error') !== false) {
                echo "\033[1;31m" . ($index + 1) . ". " . $line . "\033[0m\n";
            } else {
                echo "\033[1;37m" . ($index + 1) . ". " . $line . "\033[0m\n";
            }
        }
        
        if (count($lines) > 20) {
            echo "\n\033[1;33m... (" . (count($lines) - 20) . " more entries in log file)\033[0m\n";
        }
    }
    
    private function getInput($prompt) {
        echo $prompt;
        return trim(fgets(STDIN));
    }
}

// Check if PHP CLI is being used
if (php_sapi_name() !== 'cli') {
    die("This application must be run from the command line.\n");
}

// Start the application
try {
    $app = new MpesaSTKGUI();
    $app->run();
} catch (Exception $e) {
    echo "\033[1;31mFatal Error: " . $e->getMessage() . "\033[0m\n";
    exit(1);
}
?>