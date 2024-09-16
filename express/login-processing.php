<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("functions.inc.php");

if (!is_session_started()) {
    session_set_cookie_params([
        'lifetime' => 3600,
        'path' => '/sessions/',
        'domain' => 'localhost',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
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

        print "<p>Cookie params:</p>";
        $cookieParams = session_get_cookie_params();
        var_dump($cookieParams);

        //header('Location: blog.php');
        exit();
    }
} catch (Exception $e) {
    header('Location: login.php');
    exit();
}
