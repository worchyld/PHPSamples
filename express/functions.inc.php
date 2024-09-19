<?php
// Common functions

// Yes, I know this is not good practice
function isAcceptedUsername($username) {
    return ((strtolower($username)) == "admin");
}

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

function connectToDB() {
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false
    ];
    $conn = new PDO("sqlite:blog.db", '', '', $options);

    return $conn;
}


function getBlogEntry($id) {
    // Try to connect to db
    // on fail, dump the error to $content
    // on success: dump the expected fields to $content
    try {
        $conn = connectToDB();

        // Get rows from db
        $sql = "SELECT * FROM blog WHERE ID = :id LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row;
    } catch (PDOException $e) {
        $message = "Connection failed: " . $e->getMessage() . " -- (". $e->intl_get_error_message  .")\n";
        logError(0, $e->getMessage());
        return [];
    }
}

function getBlogEntries() {
    // Try to connect to db
    // on fail, dump the error to $content
    // on success: dump the expected fields to $content
    try {
        $conn = connectToDB();
        
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

function updateBlogEntry($sanitizedInput) {
    // Connect to the database
    $conn = connectToDB();
    
    // Prepare the SQL statement
    $sql = 'UPDATE blog SET author = :author, title = :title, content = :content WHERE ID = :id';
    $stmt = $conn->prepare($sql);

    $stmt->execute(
        [
            ':author' => $sanitizedInput['blogAuthor'],
            ':title' => $sanitizedInput['blogTitle'],
            ':content' => $sanitizedInput['blogContent'],
            ':id' => $sanitizedInput['editId']
        ]
        );
 
    return $stmt->rowCount();
}

function insertBlogEntry($sanitizedInput) {
    // Connect to the database
    $conn = connectToDB();
    
    // Prepare the SQL statement
    $sql = 'INSERT into blog (author, title, content, created_at) VALUES (:author, :title, :content, :date)';
    $stmt = $conn->prepare($sql);

    $stmt->execute(
        [
            ':author' => $sanitizedInput['blogAuthor'],
            ':title' => $sanitizedInput['blogTitle'],
            ':content' => $sanitizedInput['blogContent'],
            ':date' => date('Y-m-d H:i:s')
        ]
    );

    return $stmt->rowCount();
}