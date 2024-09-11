<?php
// Enable error reporting for debugging (remove in production)
header('Content-Type: text/html; charset=UTF-8');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
set_error_handler("logError");
error_reporting(E_ALL);

$formProcessingPage = "form-process.php";

function logError($errno, $errstr) {
    //echo "<code">Error: [$errno] $errstr</code>";
    $message =  "Error: ". ($errno) . ", " . ($errstr);
    file_put_contents('debug.log', $message, FILE_APPEND);
}

function connectToDB() {
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
            author VARCHAR(255),
            content TEXT
        )";
        $conn->exec($sql);    

        // Get rows from db
        $sql = "SELECT * FROM blog LIMIT 1";
        $result = $conn->query($sql);
        $rows = $result->fetchAll(PDO::FETCH_ASSOC);        
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        echo $e->intl_get_error_message;
    }
    return $rows;    
}

$rows = connectToDB();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My blog entries</title>
    <style type="text/css">
        body {
            font: 14px/1.5rem Arial, Helvetica, sans-serif;
        }
        p { margin-bottom: 1rem; }
        :placeholder-shown, :-moz-placeholder, ::-moz-placeholder {
            font: 14px/1.5rem Arial, Helvetica, sans-serif;
        }
        textarea {
            min-width: 20%;
            min-height: 10rem;
        }
        .submit {
            font-weight:900;
            padding:1em;
        }
    </style>
</head>
<body>

<section id="blogEntries">
    <h2>List of existing blog entries</h2>
    <ul>
        <? foreach ($rows as $record): ?>
            Author: { <?=$record['author'];?> }<br>
            Title: { <?=$record['title'];?> }<br>
            Content: { <?=$record['content'];?> }
        <? endforeach; ?>
    </ul>
</section>

<hr>

<section id="blogForm">
    <h2>Add new blog entry</h2>

    <!-- Form using empty action -->
     <!-- form-processing.php -->
    <form action="/<?=$formProcessingPage;?>" method="post">
        <p></p>
            <label for="blogAuthor">Author</label><br>
            <input type="text" name="blogAuthor" id="blogAuthor" placeholder="Enter author" maxlength="125">
        </p>
        <p>
            <label for="blogTitle">Page title:<br>
                <input type="text" name="blogTitle" id="blogTitle" placeholder="Enter page title" maxlength="125">
            </label>
        </p>
        <p></p>
            <label for="blogContent">Page content:<br>
                <textarea name="blogContent" id="blogContent" placeholder="Enter blog content"></textarea>
            </label>
        </p>
        <input class="submit" type="submit" value="Post blog">
    </form>
</section>

</body>
</html>