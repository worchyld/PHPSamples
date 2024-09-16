<?php
ini_set('session.gc_maxlifetime', 3600); // 1 hour
session_set_cookie_params(3600);

session_start();

if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = 'testy';
    echo "Session ID: " . session_id() . "<br>";
    session_write_close();
}

var_dump($_SESSION);