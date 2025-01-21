<?php
header('Content-Type: application/json');

// Function to log errors to a file
function logErrorToFile($message, $stack, $timestamp) {
    $logFile = 'error.txt';
    $logMessage = "Timestamp: $timestamp" . PHP_EOL;
    $logMessage .= "Message: $message" . PHP_EOL;
    $logMessage .= "Stack Trace: $stack" . PHP_EOL;
    $logMessage .= str_repeat("-", 80) . PHP_EOL;

    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

try {
    // Decode the incoming JSON data
    $errorData = json_decode(file_get_contents('php://input'), true);

    if (isset($errorData['message']) && isset($errorData['stack']) && isset($errorData['timestamp'])) {
        // Log the error details to the file
        logErrorToFile($errorData['message'], $errorData['stack'], $errorData['timestamp']);
        echo json_encode(['success' => true, 'message' => 'Error logged successfully.']);
    } else {
        throw new Exception('Invalid error data received.');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
