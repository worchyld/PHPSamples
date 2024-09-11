<?php
// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$sql = 'CREATE DATABASE if not exists "Weather.db";';

try {
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false
    ]; 
    $conn = new PDO("sqlite:Weather.db", '','', $options);

    // Drop the table if it already exists
    $sql = 'DROP TABLE IF EXISTS "Weather";';
    $conn->exec($sql);
    echo "Table dropped<br>";
  
    // Create tables
    $sql = "CREATE TABLE IF NOT EXISTS weather (
        id INTEGER PRIMARY KEY,
        description TEXT
    )";
    $conn->exec($sql);
    echo "Table created successfully<br>";

    // Insert
    $sql = "INSERT INTO weather (description) VALUES ('Snowy')";
    $conn->exec($sql);
  
    // Get rows from weather
    $sql = "SELECT * FROM weather";
    $result = $conn->query($sql);
    echo "Results::<br>";
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: ". $row['id'] . "<br>";
        echo "Description: " . $row['description'] . "<br>";
    }
    $conn = null;
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    echo $e->intl_get_error_message;
}
