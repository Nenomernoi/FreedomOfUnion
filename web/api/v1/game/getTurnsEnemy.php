<?php

include '../connection.php';
include './updateArchiviment.php';

$response = array();
$upd = new updateArchiviment();

if (!isset($_GET["id_gamer"])) {
    $response[DATA][ACHIVIMENTS_PATH] = NULL;
    $response[SUCCESS] = CODE_ERROR_AUTH;
    $response[LEVEL] = 13;
    $response[MESSAGE] = $lang ? CODE_ERROR_AUTH_OLD_EN : CODE_ERROR_AUTH_OLD_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

if (!isset($_GET["time"]) || !isset($_GET["id_game"])) {
    $response[DATA][ACHIVIMENTS_PATH] = NULL;
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND_PARAMS;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_PARAMS_EN : CODE_ERROR_NOT_FOUND_PARAMS_RU;
    $response[LEVEL] = 23;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$time = $_GET["time"];
$idGame = $_GET["id_game"];
$idUser = $_GET["id_gamer"];

$response[DATA] = array();

$response[DATA][ACHIVIMENTS_PATH] = NULL;
$idEnemy = null;

$m = new MongoClient($connection_url);
$link = $m->selectDB($db_name);

$turns = array();
$atlas = array();

/////GET TURNS

$collectionGame = $link->selectCollection(TABLE_GAMES);
$query = array('_id' => new MongoId($idGame));
$cursor = $collectionGame->find($query);

if ($cursor->count() <= 0) {
    $m->close();
    $response[DATA][TURNS_PATH] = array();
    $response[LEVEL] = 51;
    $response[SUCCESS] = CODE_COMPLITE;
    $response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

foreach ($cursor as $row) {
    $turns = $row[TURNS_PATH];
    $atlas = $row[ATLAS];
}

$turnCount = sizeof($turns);

$response[DATA][TURNS_PATH] = array();

$reversed = array_reverse($turns);

foreach ($reversed as $turn) {

    if ($turn[TIME] > $time) {

        array_push($response[DATA][TURNS_PATH], $turn);

        if ($turn[PROGRESS] != GAME_PLAY) {

            $collectionGame = $link->selectCollection(TABLE_GAMES);
            $query = array('_id' => new MongoId($idGame));
            $collectionGame->remove($query);

            $response[DATA][ACHIVIMENTS_PATH] = $upd->getLastAch($link, $idUser);
        }
    } else {
        break;
    }
}

$response[DATA][TURNS_PATH] = array_reverse($response[DATA][TURNS_PATH]);
$response[DATA][TURN_COUNT] = $turnCount + 1;
$response[LEVEL] = 122;
$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));
