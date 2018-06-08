<?php

include '../connection.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response[SUCCESS] = CODE_ERROR_METHTOD;
    $response[MESSAGE] = $lang ? CODE_ERROR_METHOD_EN : CODE_ERROR_METHOD_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}
$post = json_decode(file_get_contents("php://input"));

if (!isset($post->uuid) || !isset($post->id_game)) {
    $response[DATA] = null;
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND_PARAMS;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_PARAMS_EN : CODE_ERROR_NOT_FOUND_PARAMS_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$id = $post->uuid;
$device = $post->id_game;

$appPacket = isset($post->app_id)? $post->app_id : null ;

$appId = PACKET_ID_EMPTY;

switch ($appPacket) {
case PACKET_CHESS:
    $appId = PACKET_ID_CHESS;
    break;
case PACKET_CALC:
    $appId = PACKET_ID_CALC;
    break;
default :
    $appId = PACKET_ID_EMPTY;
    break;
}


$ObjUser = new MongoId($id);
$response = array();

$m = new MongoClient($connection_url);
$link = $m->selectDB($db_name);

$collectionUser = $link->selectCollection(TABLE_USER);
$query = array('_id' => $ObjUser);
$cursor = $collectionUser->find($query);

if ($cursor->count() <= 0) {
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_EN : CODE_ERROR_NOT_FOUND_RU;
    $m->close();
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$coins = 0;

foreach ($cursor as $row) {
    $coins = $row["coins"];
}

$collection = $link->selectCollection(TABLE_SHARE);
    $where = array('$and' => array(
        array('uuid' => $id),
        array('appId' => $appId),
        array('device' => $device)));

$cursor = $collection->find($where);

if ($cursor->count() <= 0) {

    $setQuery = array(
        '$set' => array(
            "coins" => new MongoInt32($coins + 5)
        )
    );

    $collectionUser->update($query, $setQuery);

    $query = array(
        "uuid" => $id,
        "device" => $device,
        "appId" => $appId
    );

    $collection->insert($query);
    $response[SUCCESS] = CODE_COMPLITE;
} else {
    $response[SUCCESS] = CODE_ERROR_VERIFY;
}

$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));

