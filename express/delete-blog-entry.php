<?php
header('Content-Type: text/php; charset=UTF-8');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once("functions.inc.php");
set_error_handler("logError");

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
    if ($statement->rowCount() > 0) {
        logError("Blog entry with ID $id deleted successfully");
        $message = "Blog entry deleted successfully";
    } else {
        $message = "No blog entry found with that ID";
    }
    logError($message);

    //header("Location:" . BASE_URL . "blog.php");
    redirect();
    exit();

} catch (Exception $e) {
    logError($e->getMessage());
    logError($e->getTraceAsString());
    //header("Location:" . BASE_URL . "blog.php");
    redirect();
    exit();
}
?>