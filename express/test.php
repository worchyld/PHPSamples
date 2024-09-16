<?php
ini_set('session.gc_maxlifetime', 3600); // 1 hour
session_start();

if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = 'testy';
    echo "Session ID: " . session_id() . "<br>";
    session_write_close();
}
else {
    print "<br>Found: " . $_SESSION['username'];
}

var_dump($_SESSION);