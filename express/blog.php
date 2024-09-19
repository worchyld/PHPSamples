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

// Are we editing the blog entry?
$editMode = false;
$editEntry = null;

$request_method = mb_strtoupper(sanitizeInput($_SERVER['REQUEST_METHOD']));

if ( $request_method == 'GET' ) {
    if ( (isset($_GET['edit']) && 
        (is_numeric($_GET['edit'])) )
        ) {
        $editMode = true;
        $editID = intval( trim($_GET['edit']) );
        $editEntry = getBlogEntry($editID);
    }
}
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
        .hidden {
            display: none;
        }
    </style>
</head>
<body>

<section id="profile" class="toggle">
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
    
    print "<h3>DEBUG:</h3>";
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

<p><a href="#" id="toggleProfile">Show console log</a></p>

<?php if ($showBlog == true): ?>
<section id="blogEntries">
    <h2>Blog entries</h2>
    <p>Total entries: <?=count($rows);?></p>

    <?php
    if ((count($rows) == 0) || ($rows == NULL)):?>
        <p>No blog articles yet</p>
    <?php else: ?>
        <ul class="blog-list">
        <?php foreach ($rows as $record): ?>
                <li class="blog-meta">
                    <h3><?=htmlspecialchars($record['title']);?></h3>
                    <p class="blog-meta">
                        By <?=htmlspecialchars($record['author']);?> on 
                        <?=(new DateTime($record['created_at']))->format('d-M-Y H:i:s');?>
                    </p>
                    <p class="blog-excerpt"><?=htmlspecialchars(substr($record['content'], 0, 100));?>...</p>
                    <div class="blog-actions">
                        <a href="?edit=<?=urlencode($record['ID']);?>">Edit</a>
                        <a href="delete-blog-entry.php?id=<?=urlencode($record['ID']);?>" 
                        onclick="return confirm('Confirm: Delete?');">Delete</a>
                    </div>
                </li>
                <?php
        endforeach;?>
        </ul>
        <?php
    endif; ?>
    
</section>

<hr>

<?php
if ($editMode == true) {
    $formTitle = "Edit blog entry";
    $record['author'] = htmlspecialchars($editEntry['author']);
    $record['title'] = htmlspecialchars($editEntry['title']);
    $record['content'] = htmlspecialchars($editEntry['content']);
} else {
    $formTitle = "Add new blog entry";
    $record['author'] = '';
    $record['title'] = '';
    $record['content'] = '';
}
?>

<section id="blogForm">
    <h2><?=$formTitle;?></h2>

    <!-- Form using POST -->
    <form action="/<?=$formProcessingPage;?>" method="post">
    <?php if ($editMode): ?>
            <input type="hidden" id="editId" name="editId" value="<?=htmlspecialchars($editEntry['ID']);?>">
        <?php endif; ?>

        <p></p>
            <label for="blogAuthor">Author</label><br>
            <input type="text" name="blogAuthor" id="blogAuthor" placeholder="Enter author" maxlength="125" value="<?= $editMode ? htmlspecialchars($editEntry['author']) : '' ?>">
        </p>
        <p>
            <label for="blogTitle">Page title:<br>
                <input type="text" name="blogTitle" id="blogTitle" placeholder="Enter page title" maxlength="125" value="<?= $editMode ? htmlspecialchars($editEntry['title']) : '' ?>">
            </label>
        </p>
        <p></p>
            <label for="blogContent">Page content:<br>
                <textarea name="blogContent" id="blogContent" placeholder="Enter blog content"><?= $editMode ? htmlspecialchars($editEntry['content']) : '' ?></textarea>
            </label>
        </p>
        <input class="submit" type="submit" value="<?= $editMode ? 'Update blog' : 'Post blog' ?>">
    </form>
</section>

<?php endif; ?>


<script type="text/javascript" language="javascript" charset="utf-8">
document.addEventListener('DOMContentLoaded', function() {
    const profileSection = document.getElementById('profile');
    const toggleButton = document.getElementById('toggleProfile');

    toggleButton.addEventListener('click', function(e) {
        e.preventDefault();
        profileSection.classList.toggle('hidden');
        toggleButton.textContent = profileSection.classList.contains('hidden') ? 'Show console log' : 'Hide console log';
    });
});
</script>
</body>
</html>