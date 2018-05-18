<?php

include '../connection.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response[SUCCESS] = CODE_ERROR_METHTOD;
    $response[MESSAGE] = $lang ? CODE_ERROR_METHOD_EN : CODE_ERROR_METHOD_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}
$post = json_decode(file_get_contents("php://input"));


if (!isset($post->uuid) || !isset($post->payment) || !isset($post->who_error)) {
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND_PARAMS;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_PARAMS_EN : CODE_ERROR_NOT_FOUND_PARAMS_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}
$user_uid = $post->uuid;
$payment = $post->payment;
$whoError = $post->who_error;

$m = new MongoClient($connection_url);
$link = $m->selectDB($db_name);

$collection = $link->selectCollection(TABLE_PAYMENT);

$query = array(
    "payment" => new MongoInt32($payment),
    "date_time" => $dateToday->getTimestamp(),
    "who_error" => $whoError,
    "uuid" => $user_uid
);
$collection->insert($query);

$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));
