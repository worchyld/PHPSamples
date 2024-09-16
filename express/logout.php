<?php
ob_start();
session_start();
session_unset();
session_destroy();
$_POST = [];
$_GET = [];
$_SESSION = [];
$_REQUEST = [];
header('Location: login.php');
exit();
ob_end_flush();
?>