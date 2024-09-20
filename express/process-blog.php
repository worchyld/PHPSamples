<?php
// Turn off output buffering
ob_start();
header('Content-Type: text/php; charset=UTF-8');
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1 From php.net
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past From php.net
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'functions.inc.php';

// force to uppercase
$request_method = mb_strtoupper(sanitizeInput($_SERVER['REQUEST_METHOD']));

try {
    if ($request_method !== 'POST') {
        throw new Exception("\nInvalid request method: $request_method");
    }

    // Validate and try to sanitize input
    $requiredFields = ['blogAuthor', 'blogTitle', 'blogContent'];
    $sanitizedInput = [];

    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("Missing required field: $field");
           }
           $sanitizedInput[$field] = sanitizeInput($_POST[$field]);
       }

    // Check if an edit request
    if (isset($_POST['editId'])) {
        // Update the record
        $sanitizedInput['editId'] = intval($_POST['editId']);
        $rowsAffected = updateBlogEntry($sanitizedInput);
    } else {
        // Insert the record
        $rowsAffected = insertBlogEntry($sanitizedInput);
    }
    
    if ($rowsAffected > 0) {
        logError("\nSaved entry to database");
    } else {
        throw new Exception("Failed to save entry to database");
    }
    
    $url = "http://localhost:3000/";
    ob_clean();
    //header("Location: " . $url . "blog.php");
    redirect();
    exit();
}
catch (exception $e) {
    // Log the error
    logError($e->getMessage());
    logError($e->getTraceAsString());

    $url = "http://localhost:3000/";
    ob_clean();
    //header("Location: " . $url . "blog.php");
    redirect();
    exit();
}
ob_end_flush();