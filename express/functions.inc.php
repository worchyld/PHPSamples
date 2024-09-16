<?php
// Common functions

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

function is_session_started() {
    if (php_sapi_name() === 'cli')
        return false;

    if (version_compare(phpversion(), '5.4.0', '>='))
        return session_status() === PHP_SESSION_ACTIVE;

    return session_id() !== '';
}

function getBlogEntries() {
    // Try to connect to db
    // on fail, dump the error to $content
    // on success: dump the expected fields to $content
    try {
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false
        ];
        $conn = new PDO("sqlite:blog.db", '','', $options);
        
        // create table if it does not exist
        $sql = "CREATE TABLE IF NOT EXISTS blog (
            ID INTEGER PRIMARY KEY,
            title VARCHAR(255),
            author VARCHAR(255),
            content TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $conn->exec($sql);

        // Get rows from db
        $sql = "SELECT * FROM blog ORDER BY created_AT DESC LIMIT 50";
        $result = $conn->query($sql);
        $rows = $result->fetchAll(PDO::FETCH_ASSOC);
        
        return $rows;
    } catch (PDOException $e) {
        $message = "Connection failed: " . $e->getMessage() . " -- (". $e->intl_get_error_message  .")\n";
        logError(0, $e->getMessage());
        return [];
    }
}