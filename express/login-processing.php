<?php
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
        /*
        $_SESSION['username'] = $username;
        session_write_close();
        logError("Session set: " . json_encode($_SESSION)); 
        print "<p>Session ID: " . session_id() . "</p>";
        print "<p>Session Name: " . session_name() . "</p>";
        print "<p>Session Save Path: " . session_save_path() . "</p>";
        print "<p>Session Status: " . session_status() . "</p>";
        print "<p>Session Started: " . (isset($_SESSION['username']) ? 'Yes' : 'No') . "</p>";
        print "<p>Session Data: " . json_encode($_SESSION) . "</p>";
        print "<p>Cookie Params: " . json_encode(session_get_cookie_params()) . "</p>";
        print ("<hr>");
        print "<a href='blog.php'>Go to Blog</a>";
        */
        //header("Location:" . BASE_URL . "blog.php");
        redirect();
        exit();
    }
} catch (Exception $e) {
   // header("Location:" . BASE_URL . "login.php");
    redirect();
    exit();
}
