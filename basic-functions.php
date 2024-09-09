<?php
// Thur 05 Sep 24
/**
String
Integer
Array
Boolean
Resource
Null

Task 1 - 
Write a function to calculate the value of two integers together using addition. Return this value to your script.
Use var dump on the returned value from your script.
Use var_dump on each of the other variables defined and view the results of this in your browser.

**/

$x = 1;
$y = 2;

function add($x, $y) {
  return $x + $y;
}

$total = add($x, $y);
var_dump($total);
var_dump($x);
var_dump($y);

/* Output:
int(3)
int(1)
int(2)
*/

//---------

/**
Task 2 - 
Write two functions which handle all aspects of writing to and reading from a text file specifically. For best practice, it may be easier to create these as two separate PHP files first which each handle only one area.
For the reading of a text file, save the content of this into a variable, and echo the content of the variable out onto the page.

You may wish to create your own text file for this.
The text file should contain quotes, one per line, from characters in your favourite movie.
**/

readThisFile("quotes.txt");
writeToFile("quotes.txt", "New line is entered");

function readThisFile($filename = "quotes.txt") {
    if (file_exists("quotes.txt")) {        
        // open the file for reading
        $file = fopen($filename, "r");

        // read the file
        $quotes = fread($file, filesize("quotes.txt"));

        print_r($quotes);

        // close the file
        fclose($file);

        return $quotes;
    }
}

function writeToFile($file, $contents) {
    // Open the file to get existing content
    $current = file_get_contents($file);
    // Append a new person to the file
    $current .= $contents."\n";
    // Write the contents back to the file
    file_put_contents($file, $current);

    // :Archived write to file
    // open the file for writing
    // $file = fopen("quotes.txt", "a") or die("Cannot read");
    // $addQuote = "This is a fresh new quote";
    //file_put_contents($file, $addQuote, $file);
    //fwrite( $file, $addQuote ) or die("cannot write");
    //fclose($file);
}

/*
Task 3 -
Modify the script and select a random quote from the text file. Change the output and only echo the randomised quote out on the page. This should now change each time you view the page.
If there are no quotes, output a standard error message instead.
*/

$quotes = getRandomLineFromFile("quotes.txt");

function getRandomLineFromFile($file = "quotes.txt") {    
    $lines = file($file);
    echo $lines[array_rand($lines)];
}
