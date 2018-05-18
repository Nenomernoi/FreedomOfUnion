<?php

include '../connection.php';

if (!isset($_GET["id"])) {
    $response[DATA] = null;
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND_PARAMS;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_PARAMS_EN : CODE_ERROR_NOT_FOUND_PARAMS_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$id = $_GET["id"];
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

$human = array();
$unifornList = array();
$unifornBuyList = array();

foreach ($cursor as $row) {

    $human["id"] = $row["_id"]->{'$id'};
    $human["name"] = $row["name"];
    $human["email"] = $row["email"];
    $human["avatar_url"] = $row["avatar_url"] != null ? $row["avatar_url"] : "";
    $human["fraction"] = $row["fraction"];
    $human["game_only"] = $row["game_only"];
    $human["game_win"] = $row["game_win"];
    $human["game_lose"] = $row["game_lose"];
    $human["game_lose_win"] = $row["game_lose_win"];
    $human["total"] = $row["total"];
    $human["coins"] = $row["coins"];

    if ($row["uniform_buy"] != null) {
        $unifornList = $row["uniform_buy"];
    }

    if ($row["uniform_eq"] != null) {
        $unifornBuyList = $row["uniform_eq"];
    }

    $achiviments = array();

    $achiviments["isStoneWall"] = $row["isStoneWall"];
    $achiviments["isAppomattox"] = $row["isAppomattox"];
    $achiviments["isTurtle"] = $row["isTurtle"];
    $achiviments["isMenacingLook"] = $row["isMenacingLook"];
    $achiviments["isPatient"] = $row["isPatient"];
    $achiviments["isMedusa"] = $row["isMedusa"];
    $achiviments["isDavid"] = $row["isDavid"];
    $achiviments["isBully"] = $row["isBully"];

    $achiviments["isBuilder1"] = $row["isBuilder1"];
    $achiviments["isBuilder2"] = $row["isBuilder2"];
    $achiviments["isBuilder3"] = $row["isBuilder3"];
    $achiviments["isBuilder4"] = $row["isBuilder4"];

    $achiviments["isCollector1"] = $row["isCollector1"];
    $achiviments["isCollector2"] = $row["isCollector2"];
    $achiviments["isCollector3"] = $row["isCollector3"];
    $achiviments["isCollector4"] = $row["isCollector4"];

    $achiviments["isMcClellan1"] = $row["isMcClellan1"];
    $achiviments["isMcClellan2"] = $row["isMcClellan2"];
    $achiviments["isMcClellan3"] = $row["isMcClellan3"];
    $achiviments["isMcClellan4"] = $row["isMcClellan4"];

    $achiviments["isGrant1"] = $row["isGrant1"];
    $achiviments["isGrant2"] = $row["isGrant2"];
    $achiviments["isGrant3"] = $row["isGrant3"];
    $achiviments["isGrant4"] = $row["isGrant4"];

    $human[ACHIVIMENTS_PATH] = $achiviments;
}

////////////////////// униформа которая одета

$human["uniform_buy"] = $unifornList;

////////////////////// униформа которая BUY

$human["uniform_eq"] = $unifornBuyList;

/////////////////////////

$collectionUserPosition = $link->selectCollection(TABLE_USER);
$cursorPosition = $collectionUserPosition->find();
$cursorPosition->sort(array('total' => -1));


if ($cursorPosition->count() <= 0) {
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_EN : CODE_ERROR_NOT_FOUND_RU;
    $m->close();
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$counter = -1;

foreach ($cursorPosition as $row) {
    $counter++;

    if ($id != $row["_id"]->{'$id'}) {
        continue;
    }
    $human["position"] = $counter + 1;
    break;
}

$response[DATA] = $human;
$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;

$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));
