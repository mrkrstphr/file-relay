<?php

function cors() {
    
    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }
    
    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            // may also be using PUT, PATCH, HEAD etc
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
        
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    
        exit(0);
    }
}

cors();

$extByMime = [
    'image/jpeg' => '.jpg',
    'image/jpg' => '.jpg',
    'image/png' => '.png',
];

$storageDir = '/data';
// $storageDir = './out';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    die('The server is up, but you didn\'t send any data... ¯\_(ツ)_/¯');
}

list($header, $imageSrc) = explode(',', $data['image'], 2);
$decodedData = base64_decode($imageSrc);

$title = preg_replace("/[^A-Za-z0-9 ]/", '', $data['title']);
$number = str_pad($data['pageNumber'], 4, '0', STR_PAD_LEFT);

if (!file_exists($storageDir . '/' . $title)) {
    mkdir($storageDir . '/' . $title);
}

file_put_contents(
    $storageDir . '/' . $title . '/' . $number .
    (in_array(substr($number, strlen($number) - 4), $extByMime) ? '' : '.png'),
    $decodedData
);

echo "✓";
