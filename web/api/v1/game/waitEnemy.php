<?php

include '../connection.php';

include '../class/user.php';

include '../push/firebase.php';
include '../push/push.php';

$response = array();



if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response[DATA][ACHIVIMENTS_PATH] = NULL;
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

if (!isset($post->id_game)) {
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND_PARAMS;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_PARAMS_EN : CODE_ERROR_NOT_FOUND_PARAMS_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$idConGame = $post->id_game;

$registrationIds = null;
$idEnemy = null;
$idUser = null;

////////////////////////////////////////////////////////////////////////////////////////////////////

$m = new MongoClient($connection_url);
$link = $m->selectDB($db_name);

$collectionUser = $link->selectCollection(TABLE_USER);
$query = array('user_uid' => $user_uid);
$cursor = $collectionUser->find($query);

if ($cursor->count() <= 0) {
    $response[SUCCESS] = CODE_ERROR_AUTH;
    $response[MESSAGE] = $lang ? CODE_ERROR_AUTH_OLD_EN : CODE_ERROR_AUTH_OLD_RU;
    $m->close();
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

foreach ($cursor as $row) {
    $idUser = $row["_id"]->{'$id'};
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

foreach ($cursor as $row) {

    if (strcmp($row["id_parent"]->{'$id'}, $idUser) != 0) {
        $idEnemy = $row["id_parent"]->{'$id'};
    }
    if (strcmp($row["id_child"]->{'$id'}, $idUser) != 0) {
        $idEnemy = $row["id_child"]->{'$id'};
    }
}


$collectionAch = $link->selectCollection(TABLE_ACH_GAME);
$query = array('_id' => $idUser);
$setQuery = array(
    '$set' => array('isPatient' => TRUE)
);
$collectionAch->update($query, $setQuery);



$collectionUser = $link->selectCollection(TABLE_USER);
$query = array('_id' => new MongoId($idEnemy));
$cursor = $collectionUser->find($query);

if ($cursor->count() <= 0) {
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND_PARAMS;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_PARAMS_EN : CODE_ERROR_NOT_FOUND_PARAMS_RU;
    $m->close();
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

foreach ($cursor as $row) {
    $registrationIds = $row["tokenGcm"];
}


if (isset($registrationIds)) {

    $firebase = new Firebase();
    $push = new Push();

    $title = 'Freedom or Union';
    $message = $lang ? CODE_WAIT_ENEMY_EN : CODE_WAIT_ENEMY_RU;
    $push_type = 'wait';


    $push->setTitle($title);
    $push->setMessage($message);
    $push->setType($push_type);

    $dataTurn = array();
    $dataTurn[DATA][GAMES_PATH] = $idConGame;

    $push->setData($dataTurn);

    $json = $push->getPush();
    $res = $firebase->send($registrationIds, $json);

    $response[DATA][PUSH] = $res;
}

$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));
