<?php
ob_start();
session_start();
header('Content-Type: text/php; charset=UTF-8');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function logError($message) {
    if (is_array($message)) {
        $message = json_encode($message);
    }
    $fullMsg = date('Y-m-d H:i:s') . " - " . $message . "\n\n";
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
        throw new Exception("Invalid request method: $request_method");
    }

    $requiredFields = ['username', 'password'];
    $sanitizedInput = [];

    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {

        } else {
            $sanitizedInput[$field] = sanitizeInput($_POST[$field]);
        }
    }

    $username = $sanitizedInput['username'];
    $password = $sanitizedInput['password'];
    
    if ($username === 'admin' && $password === 'admin') {
        
        $_SERVER['username'] = $username;

        print "Logged in -- <a href=\"blog.php\">Blog</a>";

        header('Location: blog.php');
        exit();
    }
} catch (Exception $e) {
    logError($e->getMessage());
    print $e->getMessage();    
    header('Location: login.php');
    exit();
}

ob_end_flush();