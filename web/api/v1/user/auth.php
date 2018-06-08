<?php

include '../connection.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response[SUCCESS] = CODE_ERROR_METHTOD;
    $response[MESSAGE] = $lang ? CODE_ERROR_METHOD_EN : CODE_ERROR_METHOD_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}
$post = json_decode(file_get_contents("php://input"));

if (!isset($post->token) || !isset($post->tokenGcm)) {
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND_PARAMS;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_PARAMS_EN : CODE_ERROR_NOT_FOUND_PARAMS_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$tokenGcm = $post->tokenGcm;
$token = $post->token;

$uuid = uniqid("", true);

$m = new MongoClient($connection_url);
$link = $m->selectDB($db_name);

$collection = $link->selectCollection(TABLE_USER);
$query = array('token' => $token);
$cursor = $collection->find($query);

if ($cursor->count() <= 0) {
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_EN : CODE_ERROR_NOT_FOUND_RU;
    $m->close();
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$setQuery = array('$set' => array(
        "tokenGcm" => $tokenGcm,
        "token" => $token,
        "user_uid" => $uuid,
        "date_time" => $dateToday->getTimestamp()
    )
);
$collection->update($query, $setQuery);


foreach ($cursor as $row) {

    $user = array();

    $user["id"] = $row["_id"]->{'$id'};
    $user["uuid"] = $row["user_uid"];
    $user["token"] = $token;
    $user["name"] = $row["name"];
    $user["no_ads"] = $row["no_ads"];

    $user["coins"] = $row["coins"];
    $user["avatar_url"] = $row["avatar_url"] != null ? $row["avatar_url"] : "";
    $user["game_only"] = $row["game_only"];
    $user["game_win"] = $row["game_win"];
    $user["id_game"] = isset($row["id_game"]) ? $row["id_game"]->{'$id'} : "";
    $user["game_lose"] = $row["game_lose"];
    $user["game_lose_win"] = $row["game_lose_win"];
    $user["fraction"] = $row["fraction"];

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
    $achiviments["isBuilder3"] = $row["isBuilder4"];
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

    $user[ACHIVIMENTS_PATH] = $achiviments;
}

$response[DATA] = $user;
$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));

