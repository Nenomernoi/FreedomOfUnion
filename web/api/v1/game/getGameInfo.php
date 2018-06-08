<?php

include '../connection.php';

$response = array();

if (!isset($_GET["uuid"])) {
    $response[SUCCESS] = CODE_ERROR_AUTH;
    $response[MESSAGE] = $lang ? CODE_ERROR_AUTH_OLD_EN : CODE_ERROR_AUTH_OLD_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}
$user_uid = $_GET["uuid"];

if (!isset($_GET["id_game"])) {
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND_PARAMS;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_PARAMS_EN : CODE_ERROR_NOT_FOUND_PARAMS_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$idConGame = $_GET["id_game"];



//////////////////////////////////////////////////////////////////////////

$m = new MongoClient($connection_url);
$link = $m->selectDB($db_name);

//////////////////////////////////////////////////////////////////////////

$collectionUser = $link->selectCollection(TABLE_USER);
$query = array('user_uid' => $user_uid);
$cursor = $collectionUser->find($query);

if ($cursor->count() <= 0) {
    $response[SUCCESS] = CODE_ERROR_AUTH;
    $response[MESSAGE] = $lang ? CODE_ERROR_AUTH_OLD_EN : CODE_ERROR_AUTH_OLD_RU;
    $m->close();
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$idGamer;
$fraction;

foreach ($cursor as $row) {
    $idGamer = $row["_id"]->{'$id'};
    $fraction = $row["fraction"];
    break;
}

//////////////////////////////////////////////////////////////////////////

$collectionGame = $link->selectCollection(TABLE_GAMES);
$query = array('_id' => new MongoId($idConGame));
$cursor = $collectionGame->find($query);

if ($cursor->count() <= 0) {
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_GAME_EN : CODE_ERROR_NOT_FOUND_GAME_RU;
    $m->close();
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$id_parent = null;
$parent = null;
$child = null;
$turns = array();
$atlas = array();


foreach ($cursor as $row) {

    $turns = $row["turns"];
    $atlas = $row["atlas"];

    if ($row["progress"] == GAME_PLAY) {

        $game["id"] = $row["_id"]->{'$id'};
        $game["avatar"] = $row["avatar_url"];
        $game["progress"] = $row["progress"];

        $parent["id"] = $row["id_parent"]->{'$id'};
        $parent["name"] = $row["name_parent"];
        $parent["avatar"] = $row["avatar_parent"];
        $parent["fraction"] = $row["fraction_parent"];


        $child["id"] = $row["id_child"]->{'$id'};
        $child["name"] = $row["name_child"];
        $child["avatar"] = $row["avatar_child"];
        $child["fraction"] = $row["fraction_child"];

        if (isset($row['params'])) {


            $parent["bank"] = $row['params']['bank'];
            $parent["money"] = $row['params']['money'];
            $parent["industry"] = $row['params']['industry'];
            $parent["techo"] = $row['params']['techo'];
            $parent["state"] = $row['params']['state'];
            $parent["units"] = $row['params']['units'];
            $parent["state"] = $row['params']['state'];
            $parent["bonus_money_1"] = $row['params']['bonus_money_1'];
            $parent["bonus_money_2"] = $row['params']['bonus_money_2'];
            $parent["bonus_money_3"] = $row['params']['bonus_money_3'];
            $parent["bonus_techo_1"] = $row['params']['bonus_techo_1'];
            $parent["bonus_techo_2"] = $row['params']['bonus_techo_2'];
            $parent["bonus_techo_3"] = $row['params']['bonus_techo_3'];
            $parent["bonus_units_1"] = $row['params']['bonus_units_1'];
            $parent["bonus_units_2"] = $row['params']['bonus_units_2'];
            $parent["bonus_units_3"] = $row['params']['bonus_units_3'];
            $parent["tower"] = $row['params']['tower'];
            $parent["row"] = $row['params']['row'];

            $parent["money_bon"] = $row['params']['money_bon'];
            $parent["techo_bon"] = $row['params']['techo_bon'];
            $parent["units_bon"] = $row['params']['units_bon'];
            $parent["tower_bon"] = $row['params']['tower_bon'];
            $parent["row_bon"] = $row['params']['row_bon'];
            $parent["atack_bon"] = $row['params']['atack_bon'];


            $child["bank"] = $row['params']['bank_enemy'];
            $child["money"] = $row['params']['money_enemy'];
            $child["industry"] = $row['params']['industry_enemy'];
            $child["techo"] = $row['params']['techo_enemy'];
            $child["state"] = $row['params']['state_enemy'];
            $child["units"] = $row['params']['units_enemy'];
            $child["state"] = $row['params']['state_enemy'];
            $child["bonus_money_1"] = $row['params']['bonus_money_enemy_1'];
            $child["bonus_money_2"] = $row['params']['bonus_money_enemy_2'];
            $child["bonus_money_3"] = $row['params']['bonus_money_enemy_3'];
            $child["bonus_techo_1"] = $row['params']['bonus_techo_enemy_1'];
            $child["bonus_techo_2"] = $row['params']['bonus_techo_enemy_2'];
            $child["bonus_techo_3"] = $row['params']['bonus_techo_enemy_3'];
            $child["bonus_units_1"] = $row['params']['bonus_units_enemy_1'];
            $child["bonus_units_2"] = $row['params']['bonus_units_enemy_2'];
            $child["bonus_units_3"] = $row['params']['bonus_units_enemy_3'];
            $child["tower"] = $row['params']['tower_enemy'];
            $child["row"] = $row['params']['row_enemy'];

            $child["money_bon"] = $row['params']['money_bon_enemy'];
            $child["techo_bon"] = $row['params']['techo_bon_enemy'];
            $child["units_bon"] = $row['params']['units_bon_enemy'];
            $child["tower_bon"] = $row['params']['tower_bon_enemy'];
            $child["row_bon"] = $row['params']['row_bon_enemy'];
            $child["atack_bon"] = $row['params']['atack_bon_enemy'];
        }

        $game["parent"] = $parent;
        $game["child"] = $child;

        $game["tower_max"] = MAX_TOWER;

        $response[DATA]["game"] = $game;
    } else {

        $collectionGame->remove($query);

        ///////////////////////////////////////////////////////////////////////////

        $response[SUCCESS] = CODE_ERROR_NOT_FOUND;
        $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_GAME_EN : CODE_ERROR_NOT_FOUND_GAME_RU;
        $m->close();
        die(json_encode($response, JSON_UNESCAPED_SLASHES));
    }
}

$turnCount = sizeof($turns);

if (strcmp($parent["id"], $idGamer) == 0) {
    $parent["fraction"] = $fraction;
} else {
    $child["fraction"] = $fraction;
}

$fractionTitleUpd = strcmp($parent["id"], $idGamer) == 0 ? "fraction_parent" : "fraction_child";
$fractionTitle = strcmp($parent["id"], $idGamer) == 0 ? $fraction : $parent["fraction"];
$setQuery = array(
    '$set' => array(
        "fraction" => $fractionTitle,
        $fractionTitleUpd => $fraction
    )
);
$collectionGame->update($query, $setQuery);


/////GET TURNS

$reversed = array_reverse($turns);

$response[DATA][TURNS_PATH] = array();

$lastTime = 0;


foreach ($reversed as $item) {

    if (!isset($item)) {
        continue;
    }

    $lastTime = $item["time"] > $lastTime ? $item["time"] : $lastTime;

    if ($item["escape"] == 2) {
        continue;
    }

    array_push($response[DATA][TURNS_PATH], $item);

    if (sizeof($response[DATA][TURNS_PATH]) == 2) {
        break;
    }
}

$response[DATA][TURNS_PATH] = array_reverse($response[DATA][TURNS_PATH]);
$response[DATA][TURN_COUNT] = $turnCount + 1;
$response[DATA][ATLAS] = $atlas;
$response[DATA][LAST_TIME] = $lastTime;
$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));
