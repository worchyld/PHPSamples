<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$formProcessingPage = "process-blog.php";

include_once("functions.inc.php");
set_error_handler("logError");

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

$rows = getBlogEntries();
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
    $showBlog = true;
    if (isset($_SESSION['username'])) {
        echo "<h2>Profile</h2>";
        echo "<p>Welcome, " . htmlspecialchars($_SESSION['username']) . "!</p>";
        echo "<p><a href=\"logout.php\">Logout</a></p>";
    } else {
        //$showBlog = false;
        echo "<p>You are not logged in</p>";
        echo "<p><a href=\"login.php\">Login</a></p>";
    }
    
    print "<p>Cookie params:</p>";
    $cookieParams = session_get_cookie_params();

    print "<p>Session ID: " . session_id() . "</p>";
    print "<p>Session Name: " . session_name() . "</p>";
    print "<p>Session Save Path: " . session_save_path() . "</p>";
    print "<p>Session Status: " . session_status() . "</p>";
    print "<p>Session Started: " . (isset($_SESSION['username']) ? 'Yes' : 'No') . "</p>";
    print "<p>Session Data: " . json_encode($_SESSION) . "</p>";
    print "<p>Cookie Params: " . json_encode($cookieParams) . "</p>";

    print ("<hr>");    
    ?>
</section>

<?php if ($showBlog == true): ?>
<section id="blogEntries">
    <h2>List of existing blog entries</h2>
    <p>Found: <?=count($rows);?> blog entries</p>

    <?php
    if ((count($rows) == 0) || ($rows == NULL)) {
        print("<p>No blog articles yet</p>");
    } else {
        foreach ($rows as $record):
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
                    <a href="delete-blog-entry.php?id=<?=urlencode($record['ID']);?>" onclick="return confirm('Are you sure you want to delete this entry?');">[Delete]</a>
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

<?php endif; ?>

</body>
</html>