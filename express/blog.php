<?php
// Enable error reporting for debugging (remove in production)
header('Content-Type: text/html; charset=UTF-8');
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
set_error_handler("logError");
error_reporting(E_ALL);

$formProcessingPage = "process-blog.php";

function logError($errno, $errstr) {
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
        section#profile {
            border: 1px solid blue;
            padding: 1rem;
        }
        section#blogEntries {
            margin-top:1rem;
            border-top:1px solid #ccc;
        }
    </style>
</head>
<body>

<section id="profile">
    <?php
    if (isset($_SESSION['username'])) {
        echo "<h2>Profile</h2>";
        echo "<p>Welcome, " . htmlspecialchars($_SESSION['username']) . "!</p>";
        echo "<p><a href=\"logout.php\">Logout</a></p>";
    } else {
        echo "<p>You are not logged in</p>";
        echo "<p><a href=\"login.php\">Login</a></p>";
    }
    ?>
</section>

<section id="blogEntries">
    <h2>List of existing blog entries</h2>
    <p>Found: <?=count($rows);?> blog entries</p>

    <?php
    if ((count($rows) == 0) || ($rows == NULL)) {
        print("<p>No blog articles yet</p>");
    } else {
        foreach ($rows as $record):
            //var_dump($record);
            // force check if its an array
            if (is_array($record)) {?>
                <li>
                    ID: { <?=htmlspecialchars(intval($record['ID']));?>}
                Author: { <?=htmlspecialchars($record['author']);?> }<br>
                Title: { <?=htmlspecialchars($record['title']);?> }<br>
                Content: { <?=htmlspecialchars($record['content']);?> }<br>

                <?php
                $createdDate = new DateTime($record['created_at']);
                ?>
                Date: <?=$createdDate->format('d-M-Y h:m:s');?> 
                <a href="delete-blog.php?id=<?=urlencode($record['ID']);?>" onclick="return confirm('Are you sure you want to delete this entry?');">[Delete]</a>
                </li>
                <?php
            }
            else {
                print "<p>Record are not an array</p>";
            }
        endforeach;
    } // end if ?>
    </ul>
</section>

<hr>

<section id="blogForm">
    <h2>Add new blog entry</h2>

    <!-- Form using POST -->
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