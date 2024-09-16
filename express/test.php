<?php
//ini_set('session.gc_maxlifetime', 3600); // 1 hour
$sessionPath = __DIR__ . '/sessions';
if (!file_exists($sessionPath)) {
    mkdir($sessionPath, 0777, true);
}
ini_set('session.save_path', $sessionPath);

function is_session_started()
{
    if (php_sapi_name() === 'cli')
        return false;

    if (version_compare(phpversion(), '5.4.0', '>='))
        return session_status() === PHP_SESSION_ACTIVE;

    return session_id() !== '';
}
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

print "is session started: " . is_session_started();

if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = 'testy';
    echo "<br>Session ID: " . session_id() . "<br>";
    session_write_close();
}
else {
    print "<br>Found: " . $_SESSION['username'];
}

var_dump($_SESSION);