<?php

include '../connection.php';
require_once '../InAppBilling/verify_market_in_app.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response[SUCCESS] = CODE_ERROR_METHTOD;
    $response[MESSAGE] = $lang ? CODE_ERROR_METHOD_EN : CODE_ERROR_METHOD_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}
$post = json_decode(file_get_contents("php://input"));

if (!isset($post->uuid)) {
    $response[SUCCESS] = CODE_ERROR_AUTH;
    $response[MESSAGE] = $lang ? CODE_ERROR_AUTH_OLD_EN : CODE_ERROR_AUTH_OLD_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}
$user_uid = $post->uuid;

if (!isset($post->key) || !isset($post->responseData) || !isset($post->signature)) {
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND_PARAMS;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_PARAMS_EN : CODE_ERROR_NOT_FOUND_PARAMS_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$responseData = $post->responseData;
$signature = $post->signature;

$valid = verify_market_in_app($responseData, $signature, PUBLIC_KEY);

if (!$valid) {
    $response[SUCCESS] = CODE_ERROR_VERIFY;
    $response[MESSAGE] = $lang ? CODE_ERROR_VERIFY_EN : CODE_ERROR_VERIFY_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$key = $post->key;

$m = new MongoClient($connection_url);
$link = $m->selectDB($db_name);


$collectionUser = $link->selectCollection(TABLE_USER);
$query = array('user_uid' => $user_uid);
$cursor = $collectionUser->find($query);

$coins = 0;

foreach ($cursor as $row) {
    $coins = $row["coins"];
}


$query = array('user_uid' => $user_uid);

switch ($key) {
    case NO_ADS:
        $coins += 50;
        break;
    case COINS_100:
        $coins += 100;
        break;
    case COINS_500:
        $coins += 500;
        break;
    case COINS_1000:
        $coins += 1000;
        break;
    case COINS_5000:
        $coins += 5000;
        break;
    case COINS_10000:
        $coins += 10000;
        break;
}

$setQuery = array(
    '$set' => array(
        "coins" => new MongoInt32($coins),
        "no_ads" => new MongoInt32(1),
        "date_time" => $dateToday->getTimestamp()
    )
);

$collectionUser->update($query, $setQuery);

$response[DATA] = $coins;
$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));
