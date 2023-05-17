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

$map = json_decode(file_get_contents("${storageDir}/map.json"), true);

switch (strtolower($_SERVER['REQUEST_URI'])) {
    case '/metadata':
        saveMetadata();
        break;

    case '/page':
        savePage();
        break;

    default:
        header('Content-type: application/json');
        http_response_code(404);

        echo json_encode([
            'error' => 'Not found',
        ]);
}

function cleanUuid($input) {
    return preg_replace("/[^A-Za-z0-9- ]/", '', $input);
}

function savePage() {
    global $map, $storageDir, $extByMime;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        errorResponse(400, 'No data sent');
        exit;
    }

    list($header, $imageSrc) = explode(',', $data['image'], 2);
    $decodedData = base64_decode($imageSrc);

    $title = cleanUuid($data['title']);

    $number = str_pad($data['pageNumber'], 4, '0', STR_PAD_LEFT);
    $dir = $map[$title] ?? $title;

    if (!file_exists($storageDir . '/' . $dir)) {
        mkdir($storageDir . '/' . $dir);
    }

    file_put_contents(
        $storageDir . '/' . $dir . '/' . $number .
        (in_array(substr($number, strlen($number) - 4), $extByMime) ? '' : '.png'),
        $decodedData
    );

    successResponse();
}

function saveMetadata() {
    global $map, $storageDir;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        errorResponse(400, 'No data sent');
        exit;
    }

    $uuid = cleanUUid($data['uuid']);
    $slug = $data['slug'] ?? $uuid;

    $map[$uuid] = $slug;

    file_put_contents("${storageDir}/map.json", json_encode($map, JSON_PRETTY_PRINT));

    successResponse();
}

function successResponse() {
    header('Content-type: application/json');
    http_response_code(200);

    echo json_encode([
        'done' => '✓',
    ]);
}

function errorResponse($code, $message) {
    header('Content-type: application/json');
    http_response_code($code);

    echo json_encode([
        'error' => $message
    ]);
}
