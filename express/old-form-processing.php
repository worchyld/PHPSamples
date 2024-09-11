<?php
// Set proper content type for the response
header('Content-Type: text/html; charset=UTF-8');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
set_error_handler("displayError");
error_reporting(E_ALL);

function debugLog($message) {
    // $message = 'PHP script executed at ' . date('Y-m-d H:i:s') . "\n"
    file_put_contents('debug.log', $message, FILE_APPEND);
}

function displayError($errno, $errstr) {
    //echo "<code">Error: [$errno] $errstr</code>";
    echo "<p style='color: red;'>Error: $message</p>";
}

// Function to sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Function to write contents to file
function writeContentsToFile($contents) {
    $logFile = 'processing.log';
    if (file_put_contents($logFile, $contents . "\n", FILE_APPEND) === false) {
        throw new Exception("Failed to write to log file: $logFile");
    }
}

// force to uppercase
$request_method = mb_strtoupper($_SERVER['REQUEST_METHOD']);

try {
    debugLog("Script execution started");

    if ($request_method !== 'POST') {
        throw new Exception("Invalid request method: $request_method");
    }

    $expectedFields = ['blogAuthor', 'blogTitle', 'blogContent'];
    $sanitizedInput = [];

    foreach ($expectedFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("Missing required field: $field");
        }
        $sanitizedInput[$field] = sanitizeInput($_POST[$field]);
    }

    // Check input lengths
    $maxLengths = [
        'blogAuthor' => 100,
        'blogTitle' => 200,
        'blogContent' => 5000
    ];

    foreach ($maxLengths as $field => $maxLength) {
        if (mb_strlen($sanitizedInput[$field]) > $maxLength) {
            throw new Exception("$field exceeds maximum length of $maxLength characters");
        }
    }

    print ("REACHED FORM PROCESSING\n");
    print ("Attempting to write to file ...");
    
    // Prepare content for logging
    $logContent = "Received at " . date('Y-m-d H:i:s') . ":\n";
    $logContent .= "Author: {$sanitizedInput['blogAuthor']}\n";
    $logContent .= "Title: {$sanitizedInput['blogTitle']}\n";
    $logContent .= "Content: {$sanitizedInput['blogContent']}\n";
    $logContent .= str_repeat('-', 50) . "\n";

    writeContentsToFile($logContent);

    // Success response
    echo "<h2>Form Processed Successfully</h2>";
    echo "<p>Thank you for your submission, {$sanitizedInput['blogAuthor']}!</p>";
    echo "<p>Your blog titled '{$sanitizedInput['blogTitle']}' has been received.</p>";

    debugLog("Form processed successfully");
}
catch (exception $e) {
    //code to handle the exception
    //trigger_error("File error -- ", E_USER_WARNING);
    displayError($e->getMessage());
    debugLog("Error: " . $e->getMessage());
}