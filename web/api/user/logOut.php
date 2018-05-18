<?php

include '../connection.php';

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

$todayDate = date('Y-m-d H:i:s');
$currentTime = time($todayDate);
$timeNew = $currentTime - 120 * 60;


$m = new MongoClient($connection_url);
$link = $m->selectDB($db_name);


$collectionUser = $link->selectCollection(TABLE_USER);
$query = array('user_uid' => $user_uid);
$setQuery = array(
    '$set' => array(
        "tokenGcm" => "",
        "date_time" => date('Y-m-d H:i:s', $timeNew)
    )
);
$collectionUser->update($query, $setQuery);

$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));
