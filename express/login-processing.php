<?php
session_start();
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

function is_session_started()
{
    if (php_sapi_name() === 'cli')
        return false;

    if (version_compare(phpversion(), '5.4.0', '>='))
        return session_status() === PHP_SESSION_ACTIVE;

    return session_id() !== '';
}
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

print "<p>Cookie params:</p>";
        $cookieParams = session_get_cookie_params();
        var_dump($cookieParams);

        //header('Location: blog.php');
        exit();
    }
} catch (Exception $e) {
    //logError($e->getMessage());
    //print $e->getMessage();    
    header('Location: login.php');
    exit();
}
