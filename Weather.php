<?php
// Simple weather reading app
// @author: AD
// @updated: 09 Sep 24

/**
1. Find out the IP address of the current user visiting the page.
Use this IP address to query an IP Location API service at the following page, and find out what city the user is from.
https://freeipapi.com/

2. Create a function to do this through a separate 'functions.php' file. 

3. Use the City and latitude/longitude coordinates to query the Weather API, and return the following information:

The weather forecast for today in that user's city.
The Weather forecast for yesterday
The Weather forecast for tomorrow

Modify the following API link to include information in the query string about the visitor:
https://api.open-meteo.com/v1/forecast?latitude=52.52&longitude=13.41&current=temperature_2m,wind_speed_10m&hourly=temperature_2m,relative_humidity_2m,wind_speed_10m

4. Output this information to the user in HTML.

5. Next, using the same script, create a text file which contains the retrieved weather information for this user.
**/

function displayError($errno, $errstr) {
    echo "<code>Error: [$errno] $errstr</code>";
}

function getJSONFromIPAddress($ipAddress = "63.139.12.38") {    
    $url = "https://freeipapi.com/api/json/$ipAddress";

    $response = file_get_contents($url);
    $json = json_decode($response, true);

    return $json;
}

function getLocation($longitude, $latitude) {
    $url = "https://api.open-meteo.com/v1/forecast?latitude=". $latitude."&longitude=". $longitude . "&current=temperature_2m,wind_speed_10m&hourly=temperature_2m,relative_humidity_2m,wind_speed_10m";

    //$page = file_get_contents($url) or die("Error: Cannot connect to the API");
    try {
        // run your code here
        $page = file_get_contents($url);
    }
    catch (exception $e) {
        //code to handle the exception
        trigger_error("Can't load URL", E_USER_WARNING);
    }
    finally {
        //optional code that always runs
    }

    return $page;
}


function saveToFile($file, $data) {
    try {
        // run your code here
        $file = fopen($file, "w");
        fwrite($file, $data);
        fclose($file);
    }
    catch (exception $e) {
        //code to handle the exception
        trigger_error("File error -- ", E_USER_WARNING);
    }
    finally {
        //optional code that always runs
    }
}

function displayHTML($result) {
    print "<!DOCTYPE html><html lang=\"en-gb\"><head><meta charset=\"UTF-8\"><title>Document</title>
</head>
<body>
<h1>Weather Forecast output</h1>
<section>". var_dump($result) . "</section>
</body>
</html>";
}

// :main code
set_error_handler("displayError");

$jsonData = getJSONFromIPAddress("63.139.12.38");
$result = getLocation($longitude = $jsonData['longitude'], $latitude = $jsonData['latitude']);

//saveToFile("weather.txt", $result);
displayHTML($result);
