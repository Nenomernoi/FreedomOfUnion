<?php

include '../connection.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response[DATA][ACHIVIMENTS_PATH] = NULL;
    $response[SUCCESS] = CODE_ERROR_METHTOD;
    $response[MESSAGE] = $lang ? CODE_ERROR_METHOD_EN : CODE_ERROR_METHOD_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}
$post = json_decode(file_get_contents("php://input"));

if (!isset($post->uuid)) {
    $response[DATA][ACHIVIMENTS_PATH] = NULL;
    $response[SUCCESS] = CODE_ERROR_AUTH;
    $response[MESSAGE] = $lang ? CODE_ERROR_AUTH_OLD_EN : CODE_ERROR_AUTH_OLD_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}
$user_uid = $post->uuid;

if (!isset($post->id_game)) {
    $response[DATA][ACHIVIMENTS_PATH] = NULL;
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND_PARAMS;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_PARAMS_EN : CODE_ERROR_NOT_FOUND_PARAMS_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$idConGame = $post->id_game;


////////////////////////////////////////////////////////////////////////////////////////////////////
$m = new MongoClient($connection_url);
$link = $m->selectDB($db_name);

$collectionUser = $link->selectCollection(TABLE_USER);

$query = array('user_uid' => $user_uid);
$cursor = $collectionUser->find($query);

if ($cursor->count() <= 0) {
    $response[SUCCESS] = CODE_ERROR_AUTH;
    $response[MESSAGE] = $lang ? CODE_ERROR_AUTH_OLD_EN : CODE_ERROR_AUTH_OLD_RU;
    $m->close;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

////////////////////////////////////////////////////////////////////////////////////////////////////


$collectionGame = $link->selectCollection(TABLE_GAMES);
$query = array('_id' => new MongoId($idConGame));
$cursor = $collectionGame->find($query);


if ($cursor->count() <= 0) {

    $response[SUCCESS] = CODE_COMPLITE;
    $response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
    $m->close();
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}
////////////////////////////////////////////////////////////////////////////////////////////////////

$query = array('_id' => new MongoId($idConGame));
$collectionGame->remove($query);

////////////////////////////////////////////////////////////////////////////////////////////////////

$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));
