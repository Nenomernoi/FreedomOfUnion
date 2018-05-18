<?php

include '../connection.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {

    $response[SUCCESS] = CODE_ERROR_METHTOD;
    $response[MESSAGE] = $lang ? CODE_ERROR_METHOD_EN : CODE_ERROR_METHOD_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}
$post = json_decode(file_get_contents("php://input"));


if (!isset($post->rating)) {
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND_PARAMS;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_PARAMS_EN : CODE_ERROR_NOT_FOUND_PARAMS_RU;

    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$raiting = $post->rating;
$content = isset($post->content) ? $post->content : "";
$user_uid = isset($post->uuid) ? $post->uuid : "";
$device = isset($post->device) ? $post->device : "";
$language = isset($post->lang) ? $post->lang : "";


$m = new MongoClient($connection_url);
$link = $m->selectDB($db_name);

$collection = $link->selectCollection(TABLE_RATING);

$query = array(
    "rating" => new MongoInt32($raiting),
    "content" => $content,
    "lang" => $language,
    "device" => $device,
    "user_uid" => $user_uid
);
$collection->insert($query);

$response[SUCCESS] = CODE_ERROR_AUTH;
$response[MESSAGE] = $lang ? CODE_ERROR_AUTH_EN : CODE_ERROR_AUTH_RU;
$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));
