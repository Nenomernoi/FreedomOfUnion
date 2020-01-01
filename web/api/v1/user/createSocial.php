<?php

include '../connection.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response[SUCCESS] = CODE_ERROR_METHTOD;
    $response[MESSAGE] = $lang ? CODE_ERROR_METHOD_EN : CODE_ERROR_METHOD_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}
$post = json_decode(file_get_contents("php://input"));

if (!isset($post->name) || !isset($post->token) || !isset($post->tokenGcm) || !isset($post->fraction) || !isset($post->avatar_url)) {
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND_PARAMS;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_PARAMS_EN : CODE_ERROR_NOT_FOUND_PARAMS_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}



$name = $post->name;
$email = isset($post->email) ? $post->email : "";
$fraction = $post->fraction;
$avatar_url = $post->avatar_url;

$tokenGcm = $post->tokenGcm;
$token = $post->token;

$user = array();

$uuid = uniqid("", true);

$m = new MongoClient($connection_url);
$link = $m->selectDB($db_name);

$collectionUser = $link->selectCollection(TABLE_USER);
$query = array('token' => $token);
$cursor = $collectionUser->find($query);


if ($cursor->count() > 0) {

    $collectionUser = $link->selectCollection(TABLE_USER);
    $query = array('token' => $token);
    $setQuery = array('$set' => array("user_uid" => $uuid, "date_time" => $dateToday->getTimestamp()));
    $collectionUser->update($query, $setQuery);

    $query = array('token' => $token);
    $cursor = $collectionUser->find($query);



    foreach ($cursor as $row) {
        $user["id"] = $row["_id"]->{'$id'};
        $user["coins"] = $row["coins"];
        $user["uuid"] = $row["user_uid"];
        $user["token"] = $row["token"];
        $user["avatar_url"] = $row["avatar_url"] != null ? $row["avatar_url"] : "";
        $user["game_only"] = $row["game_only"];
        $user["no_ads"] = $row["no_ads"];
        $user["id_game"] = isset($row["id_game"]) ? $row["id_game"]->{'$id'} : "";
        $user["game_win"] = $row["game_win"];
        $user["email"] = $row["email"];
        $user["game_lose"] = $row["game_lose"];
        $user["game_lose_win"] = $row["game_lose_win"];
        $user["name"] = $row["name"];
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

    /// response answer
    $response[DATA] = $user;
    $response[SUCCESS] = CODE_COMPLITE;
    $response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
    $m->close();
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

/////// insert USER ////////

$uniformBuyDefault = array('hat' => 0, 'uniform' => 0, 'gun' => 0, 'riffle' => 0);


$query = array(
    "token" => $token,
    "tokenGcm" => $tokenGcm,
    "avatar_url" => $avatar_url,
    "user_uid" => $uuid,
    "name" => $name,
    "email" => $email,
    "id_game" => null,
    "no_ads" => new MongoInt32(0),
    "date_time" => $dateToday->getTimestamp(),
    "today" => $dateToday->getTimestamp(),
    "game_only" => new MongoInt32(0),
    "game_win" => new MongoInt32(0),
    "game_win_bot" => new MongoInt32(0),
    "game_lose" => new MongoInt32(0),
    "game_lose_win" => new MongoInt32(0),
    "total" => new MongoInt32(0),
    "coins" => new MongoInt32(0),
    "fraction" => new MongoInt32($fraction),
    "uniform_buy" => array(),
    "uniform_eq" => $uniformBuyDefault,
    //ACHIVIMENTS
    "isStoneWall" => new MongoInt32(0),
    "isBuilder1" => new MongoInt32(0),
    "isBuilder2" => new MongoInt32(0),
    "isBuilder3" => new MongoInt32(0),
    "isBuilder4" => new MongoInt32(0),
    "isAppomattox" => new MongoInt32(0),
    "isTurtle" => new MongoInt32(0),
    "isMenacingLook" => new MongoInt32(0),
    "isPatient" => new MongoInt32(0),
    "isMedusa" => new MongoInt32(0),
    "isDavid" => new MongoInt32(0),
    "isBully" => new MongoInt32(0),
    "isCollector1" => new MongoInt32(0),
    "isCollector2" => new MongoInt32(0),
    "isCollector3" => new MongoInt32(0),
    "isCollector4" => new MongoInt32(0),
    "isMcClellan1" => new MongoInt32(0),
    "isMcClellan2" => new MongoInt32(0),
    "isMcClellan3" => new MongoInt32(0),
    "isMcClellan4" => new MongoInt32(0),
    "isGrant1" => new MongoInt32(0),
    "isGrant2" => new MongoInt32(0),
    "isGrant3" => new MongoInt32(0),
    "isGrant4" => new MongoInt32(0)
);

$collectionUser->insert($query);

$id_game = $query["_id"];
$id = $id_game->{'$id'};
$ObjStatus = new MongoId($id);

/// get id and uuid

$collectionUser = $link->selectCollection(TABLE_USER);
$query = array('token' => $token);
$cursor = $collectionUser->find($query);

foreach ($cursor as $row) {
    $user["id"] = $row["_id"]->{'$id'};
    $user["coins"] = $row["coins"];
    $user["uuid"] = $row["user_uid"];
    $user["token"] = $row["token"];
    $user["avatar_url"] = $row["avatar_url"] != null ? $row["avatar_url"] : "";
    $user["game_only"] = $row["game_only"];
    $user["no_ads"] = $row["no_ads"];
    $user["id_game"] = isset($row["id_game"]) ? $row["id_game"]->{'$id'} : "";
    $user["email"] = $row["email"];
    $user["game_win"] = $row["game_win"];
    $user["game_lose"] = $row["game_lose"];
    $user["game_lose_win"] = $row["game_lose_win"];
    $user["name"] = $row["name"];
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

/////////////// CREATE ACHIVIEMENT ////////////////////////

$query = array(
    '_id' => $user["id"],
    'isStoneWall' => TRUE,
    'isAppomattox' => FALSE,
    'isTurtle' => FALSE,
    'isPatient' => FALSE,
    'isMenacingLook' => FALSE,
    'isMedusa' => FALSE,
    'isBully' => FALSE,
    'isDavid' => FALSE,
    'isBuilder' => 0,
    'levelBuilder' => 0,
    'isMcClellan' => 0,
    'levelMcClellan' => 0,
    'isCollector' => 0,
    'levelCollector' => 0,
    'isGrant' => 0,
    'levelGrant' => 0);

$collectionAch = $link->selectCollection(TABLE_ACH_GAME);
$collectionAch->insert($query);

/////////////////////////////////////////////


/// response answer
$response[DATA] = $user;
$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));
