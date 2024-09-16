<?php
// Turn off output buffering
ob_start();
header('Content-Type: text/php; charset=UTF-8');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function logError($message) {
    if (is_array($message)) {
        $message = json_encode($message);
    }
    $fullMsg = date('Y-m-d H:i:s') . " - " . $message . "\n";
    file_put_contents("debug.log", $fullMsg, FILE_APPEND);
}

// Function to sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

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

    // Connect to the database
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false
    ];
    $conn = new PDO("sqlite:blog.db", '', '', $options);

    // Prepare the SQL statement
    $sql = 'INSERT into blog (author, title, content, created_at) VALUES (:author, :title, :content, :date)';
    $preparedStatement = $conn->prepare($sql);

    $preparedStatement->execute([
        ':author' => $sanitizedInput['blogAuthor'],
        ':title' => $sanitizedInput['blogTitle'],
        ':content' => $sanitizedInput['blogContent'],
        ':date' => date('Y-m-d H:i:s')
    ]);

    logError("\nSaved entry to database");
    
    $url = "http://localhost:3000/";
    ob_clean();
    header("Location: " . $url . "blog.php");
    exit();
}
catch (exception $e) {
    // Log the error
    logError($e->getMessage());
    logError($e->getTraceAsString());

    $url = "http://localhost:3000/";
    ob_clean();
    header("Location: " . $url . "blog.php");
    exit();
}
ob_end_flush();