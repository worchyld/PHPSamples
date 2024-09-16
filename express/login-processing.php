<?php
session_start();
ini_set('session.cookie_secure','Off');
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
    
    if ($username == 'admin' && $password == 'admin') {
        
        $_SESSION['username'] = $username;
        session_write_close();
        logError("Session set: " . json_encode($_SESSION)); // Log session data
        
        echo "Session ID: " . session_id() . "<br>";
        var_dump($_SESSION);

        //header('Location: blog.php');
        exit();
    }
} catch (Exception $e) {
    logError($e->getMessage());
    print $e->getMessage();    
    header('Location: login.php');
    exit();
}
