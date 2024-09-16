<?php
ob_start();
header('Content-Type: text/php; charset=UTF-8');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function logError($message) {
    $fullMsg = date('Y-m-d H:i:s') . " - " . $message . "\n";
    file_put_contents("debug.log", $fullMsg, FILE_APPEND);
}

try {
    // Check if an ID was provided
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        throw new Exception("Invalid ID provided");
    }

    $id = intval( trim($_GET['id']) );

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false
    ];
    $conn = new PDO("sqlite:blog.db", '','', $options);
    
    $statement = $conn->prepare("DELETE FROM blog WHERE ID = :id");
    $statement->execute([
        ':id' => $id
    ]);

    // Check if a row was actually deleted
    if ($stmt->rowCount() > 0) {
        logError("Blog entry with ID $id deleted successfully");
        $message = "Blog entry deleted successfully";
    } else {
        $message = "No blog entry found with that ID";
    }
    logError($message);

    header("Location: blog.php");
    exit();

} catch (Exception $e) {
    logError($e->getMessage());
    logError($e->getTraceAsString());
    header("Location: blog.php");
    exit();
}

ob_end_flush();
?>