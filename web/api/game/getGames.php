<?php

include '../connection.php';

$response = array();
$response[DATA] = array();
$response[DATA]["games"] = array();
$response[DATA]["game"] = null;

if (!isset($_GET["uuid"])) {

    $response[SUCCESS] = CODE_ERROR_AUTH;
    $response[MESSAGE] = $lang ? CODE_ERROR_AUTH_OLD_EN : CODE_ERROR_AUTH_OLD_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}
$user_uid = $_GET["uuid"];

if (!isset($_GET["page"])) {

    $response[SUCCESS] = CODE_ERROR_NOT_FOUND_PARAMS;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_PARAMS_EN : CODE_ERROR_NOT_FOUND_PARAMS_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$gameId;

$m = new MongoClient($connection_url);
$link = $m->selectDB($db_name);

$collectionUser = $link->selectCollection(TABLE_USER);
$query = array('user_uid' => $user_uid);
$cursor = $collectionUser->find($query);

$ObjUser;

if ($cursor->count() <= 0) {

    $response[SUCCESS] = CODE_ERROR_AUTH;
    $response[MESSAGE] = $lang ? CODE_ERROR_AUTH_OLD_EN : CODE_ERROR_AUTH_OLD_RU;
    $m->close();
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

foreach ($cursor as $row) {
    $ObjUser = $row["_id"]->{'$id'};
    $gameId = isset($row["id_game"]) ? $row["id_game"]->{'$id'} : null;
    break;
}

$page = $_GET["page"];

$itemMax = MAX_ITEM * ($page + 1);

if ($itemMax > MAX_ITEM_LIST) {

    $response[SUCCESS] = CODE_COMPLITE;
    $response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
    $m->close();
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$collectionGame = $link->selectCollection(TABLE_GAMES);

$query = array(
    '$and' => array(
        array("progress" => GAME_PLAY),
        array('$or' => array(
                array(
                    '$and' => array(
                        array("id_parent" => new MongoId($ObjUser)),
                        array("id_child" => new MongoId($ObjUser))
                    )
                ),
                array("id_child" => null),
                array("id_parent" => new MongoId($ObjUser)),
                array("id_child" => new MongoId($ObjUser))
        ))));


$cursor = $collectionGame->find($query);

$counter = -1;

foreach ($cursor as $row) {
    $counter++;

    if ($counter >= (MAX_ITEM * $page) && $counter < $itemMax) {

        $game = array();
        $game["id"] = $row["_id"]->{'$id'};
        $game["name"] = $row["name"];
        $game["id_parent"] = $row["id_parent"]->{'$id'};
        $game["id_child"] = isset($row["id_child"]) ? $row["id_child"]->{'$id'} : "";
        $game["avatar_url"] = isset($row["avatar_url"]) ? $row["avatar_url"] : "";
        $game["password"] = isset($row["password"]) ? $row["password"] : "";
        $game["fraction"] = isset($row["fraction_parent"]) ? $row["fraction_parent"] : 1;

        $game["game_win"] = isset($row["game_win"]) ? $row["game_win"] : 0;
        $game["game_only"] = isset($row["game_only"]) ? $row["game_only"] : 0;
        $game["total"] = isset($row["total"]) ? $row["total"] : 0;

        array_push($response[DATA]["games"], $game);
    }
}



if ($gameId != null) {

    $query = array('_id' => new MongoId($gameId));
    $cursor = $collectionGame->find($query);

    foreach ($cursor as $row) {

        if ($row["progress"] != GAME_PLAY) {
            continue;
        }

        $game = array();
        $game["id"] = $row["_id"]->{'$id'};
        $game["name"] = $row["name"];
        $game["id_parent"] = $row["id_parent"]->{'$id'};
        $game["id_child"] = isset($row["id_child"]) ? $row["id_child"]->{'$id'} : "";
        $game["avatar_url"] = isset($row["avatar_url"]) ? $row["avatar_url"] : "";
        $game["password"] = isset($row["password"]) ? $row["password"] : "";
        $game["fraction"] = isset($row["fraction_parent"]) ? $row["fraction_parent"] : 1;

        $game["game_win"] = isset($row["game_win"]) ? $row["game_win"] : 0;
        $game["game_only"] = isset($row["game_only"]) ? $row["game_only"] : 0;
        $game["total"] = isset($row["total"]) ? $row["total"] : 0;

        $response[DATA]["game"] = $game;
    }
}



$query = array(
    '$or' => array(
        array("id_parent" => new MongoId($ObjUser)),
        array("id_child" => new MongoId($ObjUser))
    )
);


$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));
