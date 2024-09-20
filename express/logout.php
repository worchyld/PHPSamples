<?php
include_once("functions.inc.php");
session_start();
unset($_SESSION['username']);
session_unset();
session_destroy();
$_POST = [];
$_GET = [];
$_SESSION = [];
$_REQUEST = [];
//header('Location: login.php');
redirect("login.php");
exit();