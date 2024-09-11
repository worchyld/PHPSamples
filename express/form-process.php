<?php
// Process the form, redirect back to blog.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
set_error_handler("recordError");
error_reporting(E_ALL);

function debugLog($message) {
    // $message = 'PHP script executed at ' . date('Y-m-d H:i:s') . "\n"
    file_put_contents('debug.log', $message, FILE_APPEND);
}

function recordError($errno, $errstr) {
    $message = "Error: [$errno] $errstr";
    debugLog($message);
}

// Function to sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
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

    debugLog("REACHED FORM PROCESSING\n");
    debugLog("Attempting to write to file ...");
        
    // Prepare content for logging
    $logContent = "Received at " . date('Y-m-d H:i:s') . ":\n";
    $logContent .= "Author: {$sanitizedInput['blogAuthor']}\n";
    $logContent .= "Title: {$sanitizedInput['blogTitle']}\n";
    $logContent .= "Content: {$sanitizedInput['blogContent']}\n";
    $logContent .= str_repeat('-', 50) . "\n";

    debugLog($logContent);
    
    // Write to DB
    writeToDB($sanitizedInput);
}
catch (exception $e) {
    displayError($e->getMessage());
    debugLog("Error: " . $e->getMessage());
}
finally {
    header("Location: form.php");
}