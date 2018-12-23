<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

use App\models\Weather;

// REQUIRING AUTOLOAD
require_once 'vendor/autoload.php';

$weather = new Weather();

// Show page based on route
if (isset($_GET['command'])) {
    $command = $_GET['command'];
    if ($command === 'search') {
        $query = $_GET['keyword'];
        // search location by query
        require_once 'app/api/search.php';
    }

    if ($command === 'location') {
        $woeid = $_GET['woeid'];
        // search location by woeid
        require_once 'app/api/weather.php';
    }
} else {
    // show weather of Istanbul, Berlin, London, Helsinki, Dublin, Vancouver
    require_once 'app/api/home.php';
}
