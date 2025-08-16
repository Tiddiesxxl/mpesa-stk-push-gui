<?php
/**
 * SWIFT-WALLET M-Pesa STK Push Callback Handler
 * 
 * This script handles webhook callbacks from SWIFT-WALLET
 * URL: https://rika.ayubxxl.site/swift/callback.php
 * 
 * Security Requirements:
 * - HTTPS enabled
 * - Responds with HTTP 200 within 30 seconds
 * - Validates callback data structure
 */

// Set response headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Log file for callbacks
$logFile = '/var/www/html/swift/callback_log.txt';
$transactionFile = '/var/www/html/swift/callback_transactions.json';

// Function to log callback data
function logCallback($message, $data = null) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] $message";
    if ($data) {
        $logEntry .= " | Data: " . json_encode($data);
    }
    $logEntry .= "\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

// Function to update transaction status
function updateTransactionStatus($externalRef, $status, $mpesaReceipt, $amount, $serviceFee, $channelInfo, $resultDesc = null) {
    global $transactionFile;
    
    $transactions = [];
    if (file_exists($transactionFile)) {
        $content = file_get_contents($transactionFile);
        $transactions = json_decode($content, true) ?: [];
    }
    
    $transactionUpdate = [
        'external_reference' => $externalRef,
        'status' => $status,
        'mpesa_receipt' => $mpesaReceipt,
        'amount' => $amount,
        'service_fee' => $serviceFee,
        'channel_info' => $channelInfo,
        'result_desc' => $resultDesc,
        'updated_at' => date('Y-m-d H:i:s'),
        'callback_received' => true
    ];
    
    $transactions[$externalRef] = $transactionUpdate;
    file_put_contents($transactionFile, json_encode($transactions, JSON_PRETTY_PRINT), LOCK_EX);
}

// Function to send real-time notification (optional)
function sendRealTimeNotification($data) {
    // This could be used to send notifications via WebSocket, SSE, or other real-time methods
    $notificationFile = '/var/www/html/swift/notifications.json';
    
    $notification = [
        'type' => 'payment_callback',
        'timestamp' => date('Y-m-d H:i:s'),
        'data' => $data
    ];
    
    file_put_contents($notificationFile, json_encode($notification) . "\n", FILE_APPEND | LOCK_EX);
}

try {
    // Log the incoming request
    logCallback("Callback received", [
        'method' => $_SERVER['REQUEST_METHOD'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
        'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'Unknown',
        'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
    ]);
    
    // Only accept POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        logCallback("Invalid method: " . $_SERVER['REQUEST_METHOD']);
        echo json_encode(['ResultCode' => 1, 'ResultDesc' => 'Method not allowed']);
        exit;
    }
    
    // Verify User-Agent (optional security check)
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (strpos($userAgent, 'SWIFT-WALLET-CALLBACK-FORWARDER') === false) {
        logCallback("Suspicious User-Agent: $userAgent");
        // Continue processing but log the suspicious request
    }
    
    // Get the raw POST data
    $rawInput = file_get_contents('php://input');
    
    if (empty($rawInput)) {
        http_response_code(400);
        logCallback("Empty callback payload");
        echo json_encode(['ResultCode' => 1, 'ResultDesc' => 'Empty payload']);
        exit;
    }
    
    // Parse JSON payload
    $callbackData = json_decode($rawInput, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        logCallback("Invalid JSON payload", ['error' => json_last_error_msg(), 'raw' => $rawInput]);
        echo json_encode(['ResultCode' => 1, 'ResultDesc' => 'Invalid JSON']);
        exit;
    }
    
    // Log the complete callback data
    logCallback("Callback data received", $callbackData);
    
    // Validate required fields
    $requiredFields = ['transaction_id', 'external_reference', 'status', 'success'];
    foreach ($requiredFields as $field) {
        if (!isset($callbackData[$field])) {
            http_response_code(400);
            logCallback("Missing required field: $field", $callbackData);
            echo json_encode(['ResultCode' => 1, 'ResultDesc' => "Missing field: $field"]);
            exit;
        }
    }
    
    // Extract callback data
    $success = $callbackData['success'];
    $transactionId = $callbackData['transaction_id'];
    $externalReference = $callbackData['external_reference'];
    $checkoutRequestId = $callbackData['checkout_request_id'] ?? null;
    $merchantRequestId = $callbackData['merchant_request_id'] ?? null;
    $status = $callbackData['status'];
    $timestamp = $callbackData['timestamp'] ?? date('c');
    $serviceFee = $callbackData['service_fee'] ?? 0;
    $result = $callbackData['result'] ?? [];
    $channelInfo = $callbackData['channel_info'] ?? [];
    
    // Extract M-Pesa result data
    $resultCode = $result['ResultCode'] ?? null;
    $resultDesc = $result['ResultDesc'] ?? 'No description provided';
    $amount = $result['Amount'] ?? null;
    $mpesaReceiptNumber = $result['MpesaReceiptNumber'] ?? null;
    $phone = $result['Phone'] ?? null;
    $transactionDate = $result['TransactionDate'] ?? null;
    
    if ($success && $status === 'completed') {
        // Payment successful
        logCallback("Payment completed successfully", [
            'external_reference' => $externalReference,
            'transaction_id' => $transactionId,
            'amount' => $amount,
            'mpesa_receipt' => $mpesaReceiptNumber,
            'phone' => $phone,
            'service_fee' => $serviceFee,
            'channel_type' => $channelInfo['channel_type'] ?? 'unknown'
        ]);
        
        // Update transaction status
        updateTransactionStatus(
            $externalReference,
            'completed',
            $mpesaReceiptNumber,
            $amount,
            $serviceFee,
            $channelInfo,
            $resultDesc
        );
        
        // Send real-time notification
        sendRealTimeNotification([
            'status' => 'success',
            'external_reference' => $externalReference,
            'amount' => $amount,
            'mpesa_receipt' => $mpesaReceiptNumber,
            'phone' => $phone
        ]);
        
        // Log successful payment details
        logCallback("SUCCESS: Payment processed", [
            'reference' => $externalReference,
            'amount' => "KES " . number_format($amount),
            'receipt' => $mpesaReceiptNumber,
            'phone' => $phone,
            'fee' => "KES " . number_format($serviceFee),
            'channel' => $channelInfo['channel_name'] ?? 'Default'
        ]);
        
    } else {
        // Payment failed
        logCallback("Payment failed", [
            'external_reference' => $externalReference,
            'transaction_id' => $transactionId,
            'result_code' => $resultCode,
            'result_desc' => $resultDesc,
            'service_fee' => $serviceFee
        ]);
        
        // Update transaction status
        updateTransactionStatus(
            $externalReference,
            'failed',
            null,
            $amount,
            $serviceFee,
            $channelInfo,
            $resultDesc
        );
        
        // Send real-time notification
        sendRealTimeNotification([
            'status' => 'failed',
            'external_reference' => $externalReference,
            'error' => $resultDesc,
            'result_code' => $resultCode
        ]);
        
        // Log failure details
        logCallback("FAILED: Payment not completed", [
            'reference' => $externalReference,
            'reason' => $resultDesc,
            'code' => $resultCode,
            'fee' => "KES " . number_format($serviceFee)
        ]);
    }
    
    // Create a summary for easy reading
    $summary = [
        'timestamp' => date('Y-m-d H:i:s'),
        'reference' => $externalReference,
        'status' => $status,
        'success' => $success,
        'amount' => $amount ? "KES " . number_format($amount) : 'N/A',
        'receipt' => $mpesaReceiptNumber ?: 'N/A',
        'phone' => $phone ?: 'N/A',
        'service_fee' => "KES " . number_format($serviceFee),
        'channel' => $channelInfo['channel_name'] ?? 'Default',
        'result_desc' => $resultDesc
    ];
    
    logCallback("CALLBACK SUMMARY", $summary);
    
    // Always respond with HTTP 200 as required
    http_response_code(200);
    echo json_encode([
        'ResultCode' => 0,
        'ResultDesc' => 'Callback processed successfully',
        'timestamp' => date('Y-m-d H:i:s'),
        'processed' => true
    ]);
    
} catch (Exception $e) {
    // Log any errors
    logCallback("Callback processing error", [
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
    
    // Still respond with 200 to avoid retries
    http_response_code(200);
    echo json_encode([
        'ResultCode' => 0,
        'ResultDesc' => 'Callback received but processing failed',
        'error' => $e->getMessage()
    ]);
}

// Log completion
logCallback("Callback processing completed");
?>