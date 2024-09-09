<?php
file_put_contents('debug.log', 'PHP script executed at ' . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

print ("REACHED FORM PROCESSING\n");

var_dump($_POST);