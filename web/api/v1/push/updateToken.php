<?php

include '../connection.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response[SUCCESS] = CODE_ERROR_METHTOD;
    $response[MESSAGE] = $lang ? CODE_ERROR_METHOD_EN : CODE_ERROR_METHOD_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}
$post = json_decode(file_get_contents("php://input"));


if (!isset($post->token)) {
    $response[SUCCESS] = CODE_ERROR_AUTH;
    $response[MESSAGE] = $lang ? CODE_ERROR_AUTH_OLD_EN : CODE_ERROR_AUTH_OLD_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}
$token = $post->token;

if (!isset($post->tokenGcm)) {
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND_PARAMS;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_PARAMS_EN : CODE_ERROR_NOT_FOUND_PARAMS_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$tokenGCM =$post->tokenGcm;

$m = new MongoClient($connection_url);
$link = $m->selectDB($db_name);

$collection = $link->selectCollection(TABLE_USER);
$query = array('token' => $token);
$cursor = $collection->find($query);

if ($cursor->count() <= 0) {

    $response[SUCCESS] = CODE_ERROR_NOT_FOUND;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_EN : CODE_ERROR_NOT_FOUND_RU;
    $m->close();
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$setQuery = array('$set' => array(
        "tokenGcm" => $tokenGCM,
        "date_time" => $dateToday->getTimestamp()
    )
);
$collection->update($query, $setQuery);

$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));
