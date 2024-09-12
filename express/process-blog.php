<?php
// Process the form, redirect back to blog.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function logError($message) {
    // $message = 'PHP script executed at ' . date('Y-m-d H:i:s') . "\n"
    file_put_contents('debug.log', date('Y-m-d H:i:s') . " - " . $message . "\n", FILE_APPEND);
    // $trace = debug_backtrace();
    // $file = $trace[0]['file'];
    // $line = $trace[0]['line'];
    // $message = date('Y-m-d H:i:s') . "' - Error: " . $errno . ", " . $errstr . " in " . $file . " on line " . $line . "\n";
    // file_put_contents('debug.log', $message, FILE_APPEND);    
}

// Function to sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// force to uppercase
$request_method = mb_strtoupper($_SERVER['REQUEST_METHOD']);

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

    // Connect to the database
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false
    ];
    $conn = new PDO("sqlite:blog.db", '', '', $options);

    // Prepare the SQL statement
    $sql = 'INSERT into blog (author, title, content) VALUES (:author, :title, :content)';
    $preparedStatement = $conn->prepare($sql);

    $preparedStatement->execute([
        ':author' => $sanitizedInput['blogAuthor'],
        ':title' => $sanitizedInput['blogTitle'],
        ':content' => $sanitizedInput['blogContent']
    ]);

    logError("\nSaved entry to database");
    
    header("Location: blog.php");
    exit();
}
catch (exception $e) {
    // Log the error
    logError($e->getMessage());
    header("Location: blog.php");
    exit();
}