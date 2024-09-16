<?php
session_start();
$cookieParams = session_get_cookie_params();

// Display the cookie parameters
echo "Session Cookie Parameters:<br>";
echo "Lifetime: " . $cookieParams['lifetime'] . " seconds<br>";
echo "Path: " . $cookieParams['path'] . "<br>";
echo "Domain: " . $cookieParams['domain'] . "<br>";
echo "Secure: " . ($cookieParams['secure'] ? 'Yes' : 'No') . "<br>";
echo "HttpOnly: " . ($cookieParams['httponly'] ? 'Yes' : 'No') . "<br>";

// In PHP 7.3.0 and later, you'll also get the SameSite parameter
if (isset($cookieParams['samesite'])) {
    echo "SameSite: " . $cookieParams['samesite'] . "<br>";
}

// You can also var_dump the entire array
var_dump($cookieParams);
var_dump($_SESSION);