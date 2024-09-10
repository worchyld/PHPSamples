<?php
file_put_contents('debug.log', 'PHP script executed at ' . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

function displayError($errno, $errstr) {
    echo "<code>Error: [$errno] $errstr</code>";
}

function writeContentsToFile($file, $contents) {
    file_put_contents('processing.log', 'Content recieved: ' . $contents . "\n", FILE_APPEND);
}

// set exception handler?
set_error_handler("displayError");

// force to uppercase
$request_method = mb_strtoupper($_SERVER['REQUEST_METHOD']);

try {
    if ($request_method == 'POST') {

        // stricter check : is it a post check, does it exist, is it a string, etc.
        // check the fields are expected
        $blogAuthor = (string) $_POST['blogAuthor'];
        $blogTitle = (string) $_POST['blogTitle'];
        $blogContent = (string) $_POST['blogContent'];
            
        $blogAuthor = htmlspecialchars( trim($blogAuthor) , ENT_QUOTES, 'UTF-8' ); 
        $blogTitle = htmlspecialchars( trim($blogTitle) , ENT_QUOTES, 'UTF-8' );
        $blogContent = htmlspecialchars( trim($blogContent) , ENT_QUOTES, 'UTF-8' );    

        // checks the bytes vs check the length
        // mb_strlen: checks the # of chars
        if (str_len( $blogAuthor ) > 5000) {
            trigger_error("author: string len > 5000", E_USER_WARNING);
        }
        if (str_len( $blogTitle ) > 5000) {
            trigger_error("title: string len > 5000", E_USER_WARNING);
        }
        if (str_len( $blogContent ) > 5000) {
            trigger_error("content: string len > 5000", E_USER_WARNING);
        }
        
        print ("REACHED FORM PROCESSING\n");
        print ("Attempting to write to file ...");

        $contents = "Recieved:\n";
        $contents .= "author: ". $blogAuthor ."\n";
        $contents .= "title: ". $blogTitle ."\n";
        $contents .= "content: ". $blogContent ."\n";

        writeContentsToFile();
    }
}
catch (exception $e) {
    //code to handle the exception
    trigger_error("File error -- ", E_USER_WARNING);
}