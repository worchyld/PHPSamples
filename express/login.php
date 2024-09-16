<?php
ob_start();
header('Content-Type: text/html; charset=UTF-8');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
set_error_handler("logError");
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>

    <?php
    // Display error message if any
    if (isset($_GET['error'])) {
        echo "<p style='color: red;'>" . htmlspecialchars($_GET['error']) . "</p>";        
    }
    ?>

    <form action="login-processing.php" method="post">
        <div>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div>
            <input type="submit" value="Login">
        </div>
    </form>
</body>
</html>
<?php ob_end_flush(); ?>