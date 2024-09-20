<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$formProcessingPage = "process-blog.php";

include_once("functions.inc.php");
set_error_handler("logError");

$loggedIn = true;
$username = "";

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

if (isset($_SESSION['username'])) {
    // logged in?)
    $username = sanitizeInput($_SESSION['username']);
    if (isAcceptedUsername($username)) {
        $loggedIn = true;
    }
}

// Are we editing the blog entry?
$rows = getBlogEntries();
$editMode = false;
$editEntry = null;
$showConsole = false; // for debugging purposes

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
<html lang="en-gb">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My blog entries</title>
    <link rel="stylesheet" type="text/css" href="screen.css" media="screen">
</head>
<body>

<main>

<?php 
// Debugging console
// Note: Session variables were not being saved on my computer
if ($showConsole == true):
    ?>
    <section id="debugConsole" class="toggle">
        <?php
        print "<h3>DEBUG:</h3>";
        $cookieParams = session_get_cookie_params();
        print "<p>Session ID: " . session_id() . "</p>";
        print "<p>Session Name: " . session_name() . "</p>";
        print "<p>Session Save Path: " . session_save_path() . "</p>";
        print "<p>Session Status: " . session_status() . "</p>";
        print "<p>Session Started: " . (isset($_SESSION['username']) ? 'Yes' : 'No') . "</p>";
        print "<p>Session Data: " . json_encode($_SESSION) . "</p>";
        print "<p>Cookie Params: " . json_encode($cookieParams) . "</p>";
        print "<p>BASE_URL: " . $base_url . "</p>";

        print ("PDO Drivers: <br>");
        print_r(PDO::getAvailableDrivers());

        print ("<hr>");
        ?>
    </section>
    <?php
endif;?>

<section id="profile">
    <?php 
    if ($showConsole):
    print ("<p><a href=\"#\" id=\"toggleConsole\">Hide console log</a></p>");
    endif;

    if ($loggedIn == true) {
        echo "<h2>Welcome back, " .htmlspecialchars($_SESSION['username']) . "!</h2>";
        echo "<p><a href=\"logout.php\">Logout</a></p>";
    } else {
        echo "<p>You are not logged in</p>";
        echo "<p><a href=\"login.php\">Login</a></p>";
    }
    ?>
</section>

<?php if ($loggedIn == true): ?>
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
                        <?=(new DateTime($record['updated_at']))->format('d-M-Y H:i:s');?>
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
    <form action="<?=$formProcessingPage;?>" method="post">
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
    const profileSection = document.getElementById('debugConsole');
    const toggleButton = document.getElementById('toggleConsole');

    toggleButton.addEventListener('click', function(e) {
        e.preventDefault();
        profileSection.classList.toggle('hidden');
        toggleButton.textContent = profileSection.classList.contains('hidden') ? 'Show console log' : 'Hide console log';
    });
});
</script>

</main>
</body>
</html>