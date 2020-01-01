<?php

include '../connection.php';

include '../class/user.php';

include './updateArchiviment.php';

include '../push/firebase.php';
include '../push/push.php';

$response = array();
$upd = new updateArchiviment();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response[DATA][ACHIVIMENTS_PATH] = NULL;
    $response[SUCCESS] = CODE_ERROR_METHTOD;
    $response[MESSAGE] = $lang ? CODE_ERROR_METHOD_EN : CODE_ERROR_METHOD_RU;
    $response[LEVEL] = 15;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}
$post = json_decode(file_get_contents("php://input"));

if (!isset($post->uuid)) {
    $response[DATA][ACHIVIMENTS_PATH] = NULL;
    $response[SUCCESS] = CODE_ERROR_AUTH;
    $response[MESSAGE] = $lang ? CODE_ERROR_AUTH_OLD_EN : CODE_ERROR_AUTH_OLD_RU;
    $response[LEVEL] = 24;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}
$user_uid = $post->uuid;

if (!isset($post->id_game)) {
    $response[DATA][ACHIVIMENTS_PATH] = NULL;
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND_PARAMS;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_PARAMS_EN : CODE_ERROR_NOT_FOUND_PARAMS_RU;
    $response[LEVEL] = 34;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$idConGame = $post->id_game;

$isBot = $post->isBot;
$progress = $post->progress;

$registrationIds = null;

////////////////////////////////////////////////////////////////////////////////////////////////////
$m = new MongoClient($connection_url);
$link = $m->selectDB($db_name);

$collectionUser = $link->selectCollection(TABLE_USER);

$query = array('user_uid' => $user_uid);
$cursor = $collectionUser->find($query);

if ($cursor->count() <= 0) {
    $response[DATA][ACHIVIMENTS_PATH] = NULL;
    $response[SUCCESS] = CODE_ERROR_AUTH;
    $response[MESSAGE] = $lang ? CODE_ERROR_AUTH_OLD_EN : CODE_ERROR_AUTH_OLD_RU;
    $m->close;
    $response[LEVEL] = 56;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$stat = null;

$idUser = null;
$idEnemy = null;

foreach ($cursor as $row) {
    $idUser = $row["_id"]->{'$id'};

    $stat["game_only"] = $row["game_only"];
    $stat["game_win"] = $row["game_win"];
    $stat["game_lose"] = $row["game_lose"];
    $stat["game_lose_win"] = $row["game_lose_win"];
}
////////////////////////////////////////////////////////////////////////////////////////////////////

$isWhoBully = $idUser;

$collectionGame = $link->selectCollection(TABLE_GAMES);
$query = array('_id' => new MongoId($idConGame));
$cursor = $collectionGame->find($query);


if ($cursor->count() <= 0) {

    $response[DATA]["stat"] = $stat;
    $response[DATA]["progress"] = WINNER_PARENT;
    $response[DATA][ACHIVIMENTS_PATH] = $upd->getLastAch($link, $idUser);
    $response[SUCCESS] = CODE_COMPLITE;
    $response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
    $m->close();
    $response[LEVEL] = 88;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$id_parent = null;
$id_child = null;

$progressOld = GAME_PLAY;
$turns = array();

foreach ($cursor as $row) {
    $id_parent = $row["id_parent"]->{'$id'};
    if (isset($row["id_child"])) {
        $id_child = $row["id_child"]->{'$id'};
    }

    $turns = $row["turns"];

    $progressOld = $row["progress"];

    $isWhoBully = $row["whoIsBully"];
}

///REMOVE GAME NOT SECOND GAMER
if ($progressOld == GAME_PLAY && $id_child == null) {
    $query = array('_id' => new MongoId($idUser));
    $setQuery = array(
        '$set' => array(
            "id_game" => null
        )
    );
    $collectionUser->update($query, $setQuery);

    $query = array('_id' => new MongoId($idConGame));
    $collectionGame->remove($query);

    $response[DATA]["stat"] = $stat;
    $response[DATA]["progress"] = GAME_OVER;
    $response[DATA][ACHIVIMENTS_PATH] = $upd->getLastAch($link, $idUser);
    $response[SUCCESS] = CODE_COMPLITE;
    $response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
    $m->close();
    $response[LEVEL] = 131;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

if ($progressOld != GAME_PLAY) {

    $query = array('_id' => new MongoId($idUser));
    $setQuery = array(
        '$set' => array(
            "id_game" => null
        )
    );
    $collectionUser->update($query, $setQuery);

    $query = array('_id' => new MongoId($idConGame));
    $collectionGame->remove($query);

    $response[DATA][ACHIVIMENTS_PATH] = $upd->getLastAch($link, $idUser);
    $response[DATA][PROGRESS] = $progressOld;
    $response[DATA]["stat"] = $stat;
    $response[SUCCESS] = CODE_COMPLITE;
    $response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
    $m->close();
    $response[LEVEL] = 164;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$query = array('_id' => new MongoId($idConGame));

$date = new DateTime();
$time = $date->getTimestamp();


$turn = array(
    "time" => $time,
    "card" => 0,
    "card_new" => 0,
    "escape" => 0,
    "progress" => $progress,
    "bot" => 0,
    "id_gamer" => $idUser,
    "id_game" => $idConGame
);


$collectionGame->remove($query);

///////////////////////////////////////////////////////

$totalParent = new User_statistic();
$totalChild = new User_statistic();

$query = array('_id' => new MongoId($id_parent));
$cursor = $collectionUser->find($query);

foreach ($cursor as $row) {
    $totalParent->id = $id_parent;

    if (strcmp($row["user_uid"], $user_uid) != 0) {
        $registrationIds = $row["tokenGcm"];
        $idEnemy = $id_parent;
    }

    $totalParent->total = $row["total"];
    $totalParent->coins = $row["coins"];
    $totalParent->game_only = $row["game_only"];
    $totalParent->game_win = $row["game_win"];
    $totalParent->game_lose = $row["game_lose"];
    $totalParent->game_lose_win = $row["game_lose_win"];
    $totalParent->fraction = $row["fraction"];


    $totalParent->isStoneWall = $row["isStoneWall"];
    $totalParent->isAppomattox = $row["isAppomattox"];
    $totalParent->isTurtle = $row["isTurtle"];
    $totalParent->isMenacingLook = $row["isMenacingLook"];
    $totalParent->isPatient = $row["isPatient"];
    $totalParent->isMedusa = $row["isMedusa"];
    $totalParent->isDavid = $row["isDavid"];
    $totalParent->isBully = $row["isBully"];

    $totalParent->isBuilder1 = $row["isBuilder1"];
    $totalParent->isBuilder2 = $row["isBuilder2"];
    $totalParent->isBuilder3 = $row["isBuilder4"];
    $totalParent->isBuilder4 = $row["isBuilder4"];

    $totalParent->isCollector1 = $row["isCollector1"];
    $totalParent->isCollector2 = $row["isCollector2"];
    $totalParent->isCollector3 = $row["isCollector3"];
    $totalParent->isCollector4 = $row["isCollector4"];

    $totalParent->isMcClellan1 = $row["isMcClellan1"];
    $totalParent->isMcClellan2 = $row["isMcClellan2"];
    $totalParent->isMcClellan3 = $row["isMcClellan3"];
    $totalParent->isMcClellan4 = $row["isMcClellan4"];

    $totalParent->isGrant1 = $row["isGrant1"];
    $totalParent->isGrant2 = $row["isGrant2"];
    $totalParent->isGrant3 = $row["isGrant3"];
    $totalParent->isGrant4 = $row["isGrant4"];
}

if ($id_child != null) {

    $query = array('_id' => new MongoId($id_child));
    $cursor = $collectionUser->find($query);

    foreach ($cursor as $row) {
        $totalChild->id = $id_child;

        if (strcmp($row["user_uid"], $user_uid) != 0) {
            $registrationIds = $row["tokenGcm"];
            $idEnemy = $id_child;
        }

        $totalChild->total = $row["total"];
        $totalChild->coins = $row["coins"];
        $totalChild->game_only = $row["game_only"];
        $totalChild->game_win = $row["game_win"];
        $totalChild->game_lose = $row["game_lose"];
        $totalChild->game_lose_win = $row["game_lose_win"];
        $totalChild->fraction = $row["fraction"];

        $totalChild->isStoneWall = $row["isStoneWall"];
        $totalChild->isAppomattox = $row["isAppomattox"];
        $totalChild->isTurtle = $row["isTurtle"];
        $totalChild->isMenacingLook = $row["isMenacingLook"];
        $totalChild->isPatient = $row["isPatient"];
        $totalChild->isMedusa = $row["isMedusa"];
        $totalChild->isDavid = $row["isDavid"];
        $totalChild->isBully = $row["isBully"];

        $totalChild->isBuilder1 = $row["isBuilder1"];
        $totalChild->isBuilder2 = $row["isBuilder2"];
        $totalChild->isBuilder3 = $row["isBuilder4"];
        $totalChild->isBuilder4 = $row["isBuilder4"];

        $totalChild->isCollector1 = $row["isCollector1"];
        $totalChild->isCollector2 = $row["isCollector2"];
        $totalChild->isCollector3 = $row["isCollector3"];
        $totalChild->isCollector4 = $row["isCollector4"];

        $totalChild->isMcClellan1 = $row["isMcClellan1"];
        $totalChild->isMcClellan2 = $row["isMcClellan2"];
        $totalChild->isMcClellan3 = $row["isMcClellan3"];
        $totalChild->isMcClellan4 = $row["isMcClellan4"];

        $totalChild->isGrant1 = $row["isGrant1"];
        $totalChild->isGrant2 = $row["isGrant2"];
        $totalChild->isGrant3 = $row["isGrant3"];
        $totalChild->isGrant4 = $row["isGrant4"];
    }
}


if ($progressOld != GAME_PLAY) {

    $query = array('_id' => new MongoId($idUser));
    $setQuery = array(
        '$set' => array(
            "id_game" => null
        )
    );
    $collectionUser->update($query, $setQuery);

    $query = array('_id' => new MongoId($idConGame));
    $collectionGame->remove($query);

    $response[DATA][ACHIVIMENTS_PATH] = $upd->getLastAch($link, $idUser);

    $stat["game_only"] = strcmp($id_parent, $idUser) == 0 ? $totalParent->game_only : $totalChild->game_only;
    $stat["game_win"] = strcmp($id_parent, $idUser) == 0 ? $totalParent->game_win : $totalChild->game_win;
    $stat["game_lose"] = strcmp($id_parent, $idUser) == 0 ? $totalParent->game_lose : $totalChild->game_lose;
    $stat["game_lose_win"] = strcmp($id_parent, $idUser) == 0 ? $totalParent->game_lose_win : $totalChild->game_lose_win;

    $response[DATA]["stat"] = $stat;
    $response[SUCCESS] = CODE_COMPLITE;
    $response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
    $m->close();
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}


if (strcmp($id_parent, $idUser) == 0) {

    //UPDATE ACHIVIMENT
    $upd = new updateArchiviment();

    $resUpd = $upd->removeGame($link, $isWhoBully, $id_parent, $id_child, $progress);


    $totalParent->game_only += 1;
    $totalParent->game_lose += 1;

    $saveFull = $progress != RESIGN_PARENT && $progress != TIME_OUT_PARENT;

    $query = array('_id' => new MongoId($id_parent));
    $setQuery = array(
        '$set' => array(
            "total" => new MongoInt32($totalParent->total),
            "coins" => new MongoInt32($totalParent->coins),
            "game_lose_win" => new MongoInt32($totalParent->game_lose_win),
            "game_only" => new MongoInt32($totalParent->game_only),
            "game_win" => new MongoInt32($totalParent->game_win),
            "game_lose" => new MongoInt32($totalParent->game_lose),
            "id_game" => null,
            "date_time" => $dateToday->getTimestamp(),
            "isStoneWall" => new MongoInt32($totalParent->isStoneWall += $resUpd["parent"]["isStoneWall"] && $saveFull ? 1 : 0),
            "isBuilder1" => new MongoInt32($totalParent->isBuilder1 += $resUpd["parent"]["levelBuilder"] == 1 && $saveFull ? 1 : 0),
            "isBuilder2" => new MongoInt32($totalParent->isBuilder2 += $resUpd["parent"]["levelBuilder"] == 2 && $saveFull ? 1 : 0),
            "isBuilder3" => new MongoInt32($totalParent->isBuilder3 += $resUpd["parent"]["levelBuilder"] == 3 && $saveFull ? 1 : 0),
            "isBuilder4" => new MongoInt32($totalParent->isBuilder4 += $resUpd["parent"]["levelBuilder"] == 4 && $saveFull ? 1 : 0),
            "isAppomattox" => new MongoInt32($totalParent->isAppomattox += $resUpd["parent"]["isAppomattox"] ? 1 : 0),
            "isTurtle" => new MongoInt32($totalParent->isTurtle += $resUpd["parent"]["isTurtle"] ? 1 : 0),
            "isMenacingLook" => new MongoInt32($totalParent->isMenacingLook += $resUpd["parent"]["isMenacingLook"] ? 1 : 0),
            "isPatient" => new MongoInt32($totalParent->isPatient += $resUpd["parent"]["isPatient"] ? 1 : 0),
            "isMedusa" => new MongoInt32($totalParent->isMedusa += $resUpd["parent"]["isMedusa"] ? 1 : 0),
            "isDavid" => new MongoInt32($totalParent->isDavid += $resUpd["parent"]["isDavid"] && $saveFull ? 1 : 0),
            "isBully" => new MongoInt32($totalParent->isBully += $resUpd["parent"]["isBully"] && $saveFull ? 1 : 0),
            "isCollector1" => new MongoInt32($totalParent->isCollector1 += $resUpd["parent"]["levelCollector"] == 1 && $saveFull ? 1 : 0),
            "isCollector2" => new MongoInt32($totalParent->isCollector2 += $resUpd["parent"]["levelCollector"] == 2 && $saveFull ? 1 : 0),
            "isCollector3" => new MongoInt32($totalParent->isCollector3 += $resUpd["parent"]["levelCollector"] == 3 && $saveFull ? 1 : 0),
            "isCollector4" => new MongoInt32($totalParent->isCollector4 += $resUpd["parent"]["levelCollector"] == 4 && $saveFull ? 1 : 0),
            "isMcClellan1" => new MongoInt32($totalParent->isMcClellan1 += $resUpd["parent"]["levelMcClellan"] == 1 && $saveFull ? 1 : 0),
            "isMcClellan2" => new MongoInt32($totalParent->isMcClellan2 += $resUpd["parent"]["levelMcClellan"] == 2 && $saveFull ? 1 : 0),
            "isMcClellan3" => new MongoInt32($totalParent->isMcClellan3 += $resUpd["parent"]["levelMcClellan"] == 3 && $saveFull ? 1 : 0),
            "isMcClellan4" => new MongoInt32($totalParent->isMcClellan4 += $resUpd["parent"]["levelMcClellan"] == 4 && $saveFull ? 1 : 0),
            "isGrant1" => new MongoInt32($totalParent->isGrant1 += $resUpd["parent"]["levelGrant"] == 1 && $saveFull ? 1 : 0),
            "isGrant2" => new MongoInt32($totalParent->isGrant2 += $resUpd["parent"]["levelGrant"] == 2 && $saveFull ? 1 : 0),
            "isGrant3" => new MongoInt32($totalParent->isGrant3 += $resUpd["parent"]["levelGrant"] == 3 && $saveFull ? 1 : 0),
            "isGrant4" => new MongoInt32($totalParent->isGrant4 += $resUpd["parent"]["levelGrant"] == 4 && $saveFull ? 1 : 0)
        )
    );
    $collectionUser->update($query, $setQuery);

    $stat["game_only"] = $totalParent->game_only;
    $stat["game_win"] = $totalParent->game_win;
    $stat["game_lose"] = $totalParent->game_lose;
    $stat["game_lose_win"] = $totalParent->game_lose_win;

    $response[DATA]["stat"] = $stat;

    if ($id_child != null) {

        if (strcmp($id_child, $id_parent) != 0) {

            $saveFull = $progress != RESIGN_CHILD && $progress != TIME_OUT_CHILD;

            $totalChild->game_only += 1;
            $totalChild->game_win += 1;
            $totalChild->total += TOTAL_WIN;
            $totalChild->coins += COINS_WIN;

            $query = array('_id' => new MongoId($id_child));
            $setQuery = array(
                '$set' => array(
                    "total" => new MongoInt32($totalChild->total),
                    "coins" => new MongoInt32($totalChild->coins),
                    "game_lose_win" => new MongoInt32($totalChild->game_lose_win),
                    "game_only" => new MongoInt32($totalChild->game_only),
                    "game_win" => new MongoInt32($totalChild->game_win),
                    "game_lose" => new MongoInt32($totalChild->game_lose),
                    "id_game" => null,
                    "date_time" => $dateToday->getTimestamp(),
                    "isStoneWall" => new MongoInt32($totalChild->isStoneWall += $resUpd["child"]["isStoneWall"] && $saveFull ? 1 : 0),
                    "isBuilder1" => new MongoInt32($totalChild->isBuilder1 += $resUpd["child"]["levelBuilder"] == 1 && $saveFull ? 1 : 0),
                    "isBuilder2" => new MongoInt32($totalChild->isBuilder2 += $resUpd["child"]["levelBuilder"] == 2 && $saveFull ? 1 : 0),
                    "isBuilder3" => new MongoInt32($totalChild->isBuilder3 += $resUpd["child"]["levelBuilder"] == 3 && $saveFull ? 1 : 0),
                    "isBuilder4" => new MongoInt32($totalChild->isBuilder4 += $resUpd["child"]["levelBuilder"] == 4 && $saveFull ? 1 : 0),
                    "isAppomattox" => new MongoInt32($totalChild->isAppomattox += $resUpd["child"]["isAppomattox"] ? 1 : 0),
                    "isTurtle" => new MongoInt32($totalChild->isTurtle += $resUpd["child"]["isTurtle"] ? 1 : 0),
                    "isMenacingLook" => new MongoInt32($totalChild->isMenacingLook += $resUpd["child"]["isMenacingLook"] ? 1 : 0),
                    "isPatient" => new MongoInt32($totalChild->isPatient += $resUpd["child"]["isPatient"] ? 1 : 0),
                    "isMedusa" => new MongoInt32($totalChild->isMedusa += $resUpd["child"]["isMedusa"] ? 1 : 0),
                    "isDavid" => new MongoInt32($totalChild->isDavid += $resUpd["child"]["isDavid"] && $saveFull ? 1 : 0),
                    "isBully" => new MongoInt32($totalChild->isBully += $resUpd["child"]["isBully"] && $saveFull ? 1 : 0),
                    "isCollector1" => new MongoInt32($totalChild->isCollector1 += $resUpd["child"]["levelCollector"] == 1 && $saveFull ? 1 : 0),
                    "isCollector2" => new MongoInt32($totalChild->isCollector2 += $resUpd["child"]["levelCollector"] == 2 && $saveFull ? 1 : 0),
                    "isCollector3" => new MongoInt32($totalChild->isCollector3 += $resUpd["child"]["levelCollector"] == 3 && $saveFull ? 1 : 0),
                    "isCollector4" => new MongoInt32($totalChild->isCollector4 += $resUpd["child"]["levelCollector"] == 4 && $saveFull ? 1 : 0),
                    "isMcClellan1" => new MongoInt32($totalChild->isMcClellan1 += $resUpd["child"]["levelMcClellan"] == 1 && $saveFull ? 1 : 0),
                    "isMcClellan2" => new MongoInt32($totalChild->isMcClellan2 += $resUpd["child"]["levelMcClellan"] == 2 && $saveFull ? 1 : 0),
                    "isMcClellan3" => new MongoInt32($totalChild->isMcClellan3 += $resUpd["child"]["levelMcClellan"] == 3 && $saveFull ? 1 : 0),
                    "isMcClellan4" => new MongoInt32($totalChild->isMcClellan4 += $resUpd["child"]["levelMcClellan"] == 4 && $saveFull ? 1 : 0),
                    "isGrant1" => new MongoInt32($totalChild->isGrant1 += $resUpd["child"]["levelGrant"] == 1 && $saveFull ? 1 : 0),
                    "isGrant2" => new MongoInt32($totalChild->isGrant2 += $resUpd["child"]["levelGrant"] == 2 && $saveFull ? 1 : 0),
                    "isGrant3" => new MongoInt32($totalChild->isGrant3 += $resUpd["child"]["levelGrant"] == 3 && $saveFull ? 1 : 0),
                    "isGrant4" => new MongoInt32($totalChild->isGrant4 += $resUpd["child"]["levelGrant"] == 4 && $saveFull ? 1 : 0))
            );
            $collectionUser->update($query, $setQuery);
        }
    }
}

if ($id_child != null) {
    if (strcmp($id_child, $idUser) == 0 && strcmp($id_child, $id_parent) != 0) {

        //UPDATE ACHIVIMENT

        $resUpd = $upd->removeGame($link, $isWhoBully,$id_parent, $id_child,  $progress);


        $totalChild->game_only += 1;
        $totalChild->game_lose += 1;

        $saveFull = $progress != RESIGN_CHILD && $progress != TIME_OUT_CHILD;

        $query = array('_id' => new MongoId($id_child));
        $setQuery = array(
            '$set' => array(
                "total" => new MongoInt32($totalChild->total),
                "coins" => new MongoInt32($totalChild->coins),
                "game_lose_win" => new MongoInt32($totalChild->game_lose_win),
                "game_only" => new MongoInt32($totalChild->game_only),
                "game_win" => new MongoInt32($totalChild->game_win),
                "game_lose" => new MongoInt32($totalChild->game_lose),
                "id_game" => null,
                "date_time" => $dateToday->getTimestamp(),
                "isStoneWall" => new MongoInt32($totalChild->isStoneWall += $resUpd["child"]["isStoneWall"] && $saveFull ? 1 : 0),
                "isBuilder1" => new MongoInt32($totalChild->isBuilder1 += $resUpd["child"]["levelBuilder"] == 1 && $saveFull ? 1 : 0),
                "isBuilder2" => new MongoInt32($totalChild->isBuilder2 += $resUpd["child"]["levelBuilder"] == 2 && $saveFull ? 1 : 0),
                "isBuilder3" => new MongoInt32($totalChild->isBuilder3 += $resUpd["child"]["levelBuilder"] == 3 && $saveFull ? 1 : 0),
                "isBuilder4" => new MongoInt32($totalChild->isBuilder4 += $resUpd["child"]["levelBuilder"] == 4 && $saveFull ? 1 : 0),
                "isAppomattox" => new MongoInt32($totalChild->isAppomattox += $resUpd["child"]["isAppomattox"] ? 1 : 0),
                "isTurtle" => new MongoInt32($totalChild->isTurtle += $resUpd["child"]["isTurtle"] ? 1 : 0),
                "isMenacingLook" => new MongoInt32($totalChild->isMenacingLook += $resUpd["child"]["isMenacingLook"] ? 1 : 0),
                "isPatient" => new MongoInt32($totalChild->isPatient += $resUpd["child"]["isPatient"] ? 1 : 0),
                "isMedusa" => new MongoInt32($totalChild->isMedusa += $resUpd["child"]["isMedusa"] ? 1 : 0),
                "isDavid" => new MongoInt32($totalChild->isDavid += $resUpd["child"]["isDavid"] && $saveFull ? 1 : 0),
                "isBully" => new MongoInt32($totalChild->isBully += $resUpd["child"]["isBully"] && $saveFull ? 1 : 0),
                "isCollector1" => new MongoInt32($totalChild->isCollector1 += $resUpd["child"]["levelCollector"] == 1 && $saveFull ? 1 : 0),
                "isCollector2" => new MongoInt32($totalChild->isCollector2 += $resUpd["child"]["levelCollector"] == 2 && $saveFull ? 1 : 0),
                "isCollector3" => new MongoInt32($totalChild->isCollector3 += $resUpd["child"]["levelCollector"] == 3 && $saveFull ? 1 : 0),
                "isCollector4" => new MongoInt32($totalChild->isCollector4 += $resUpd["child"]["levelCollector"] == 4 && $saveFull ? 1 : 0),
                "isMcClellan1" => new MongoInt32($totalChild->isMcClellan1 += $resUpd["child"]["levelMcClellan"] == 1 && $saveFull ? 1 : 0),
                "isMcClellan2" => new MongoInt32($totalChild->isMcClellan2 += $resUpd["child"]["levelMcClellan"] == 2 && $saveFull ? 1 : 0),
                "isMcClellan3" => new MongoInt32($totalChild->isMcClellan3 += $resUpd["child"]["levelMcClellan"] == 3 && $saveFull ? 1 : 0),
                "isMcClellan4" => new MongoInt32($totalChild->isMcClellan4 += $resUpd["child"]["levelMcClellan"] == 4 && $saveFull ? 1 : 0),
                "isGrant1" => new MongoInt32($totalChild->isGrant1 += $resUpd["child"]["levelGrant"] == 1 && $saveFull ? 1 : 0),
                "isGrant2" => new MongoInt32($totalChild->isGrant2 += $resUpd["child"]["levelGrant"] == 2 && $saveFull ? 1 : 0),
                "isGrant3" => new MongoInt32($totalChild->isGrant3 += $resUpd["child"]["levelGrant"] == 3 && $saveFull ? 1 : 0),
                "isGrant4" => new MongoInt32($totalChild->isGrant4 += $resUpd["child"]["levelGrant"] == 4 && $saveFull ? 1 : 0))
        );
        $collectionUser->update($query, $setQuery);

        $stat["game_only"] = $totalChild->game_only;
        $stat["game_win"] = $totalChild->game_win;
        $stat["game_lose"] = $totalChild->game_lose;
        $stat["game_lose_win"] = $totalChild->game_lose_win;

        $response[DATA]["stat"] = $stat;

        $totalParent->game_only += 1;
        $totalParent->game_win += 1;
        $totalParent->total += TOTAL_WIN;
        $totalParent->coins += COINS_WIN;

        $saveFull = $progress != RESIGN_PARENT && $progress != TIME_OUT_PARENT;

        $query = array('_id' => new MongoId($id_parent));
        $setQuery = array(
            '$set' => array(
                "total" => new MongoInt32($totalParent->total),
                "coins" => new MongoInt32($totalParent->coins),
                "game_lose_win" => new MongoInt32($totalParent->game_lose_win),
                "game_only" => new MongoInt32($totalParent->game_only),
                "game_win" => new MongoInt32($totalParent->game_win),
                "game_lose" => new MongoInt32($totalParent->game_lose),
                "id_game" => null,
                "date_time" => $dateToday->getTimestamp(),
                "isStoneWall" => new MongoInt32($totalParent->isStoneWall += $resUpd["parent"]["isStoneWall"] && $saveFull ? 1 : 0),
                "isBuilder1" => new MongoInt32($totalParent->isBuilder1 += $resUpd["parent"]["levelBuilder"] == 1 && $saveFull ? 1 : 0),
                "isBuilder2" => new MongoInt32($totalParent->isBuilder2 += $resUpd["parent"]["levelBuilder"] == 2 && $saveFull ? 1 : 0),
                "isBuilder3" => new MongoInt32($totalParent->isBuilder3 += $resUpd["parent"]["levelBuilder"] == 3 && $saveFull ? 1 : 0),
                "isBuilder4" => new MongoInt32($totalParent->isBuilder4 += $resUpd["parent"]["levelBuilder"] == 4 && $saveFull ? 1 : 0),
                "isAppomattox" => new MongoInt32($totalParent->isAppomattox += $resUpd["parent"]["isAppomattox"] ? 1 : 0),
                "isTurtle" => new MongoInt32($totalParent->isTurtle += $resUpd["parent"]["isTurtle"] ? 1 : 0),
                "isMenacingLook" => new MongoInt32($totalParent->isMenacingLook += $resUpd["parent"]["isMenacingLook"] ? 1 : 0),
                "isPatient" => new MongoInt32($totalParent->isPatient += $resUpd["parent"]["isPatient"] ? 1 : 0),
                "isMedusa" => new MongoInt32($totalParent->isMedusa += $resUpd["parent"]["isMedusa"] ? 1 : 0),
                "isDavid" => new MongoInt32($totalParent->isDavid += $resUpd["parent"]["isDavid"] && $saveFull ? 1 : 0),
                "isBully" => new MongoInt32($totalParent->isBully += $resUpd["parent"]["isBully"] && $saveFull ? 1 : 0),
                "isCollector1" => new MongoInt32($totalParent->isCollector1 += $resUpd["parent"]["levelCollector"] == 1 && $saveFull ? 1 : 0),
                "isCollector2" => new MongoInt32($totalParent->isCollector2 += $resUpd["parent"]["levelCollector"] == 2 && $saveFull ? 1 : 0),
                "isCollector3" => new MongoInt32($totalParent->isCollector3 += $resUpd["parent"]["levelCollector"] == 3 && $saveFull ? 1 : 0),
                "isCollector4" => new MongoInt32($totalParent->isCollector4 += $resUpd["parent"]["levelCollector"] == 4 && $saveFull ? 1 : 0),
                "isMcClellan1" => new MongoInt32($totalParent->isMcClellan1 += $resUpd["parent"]["levelMcClellan"] == 1 && $saveFull ? 1 : 0),
                "isMcClellan2" => new MongoInt32($totalParent->isMcClellan2 += $resUpd["parent"]["levelMcClellan"] == 2 && $saveFull ? 1 : 0),
                "isMcClellan3" => new MongoInt32($totalParent->isMcClellan3 += $resUpd["parent"]["levelMcClellan"] == 3 && $saveFull ? 1 : 0),
                "isMcClellan4" => new MongoInt32($totalParent->isMcClellan4 += $resUpd["parent"]["levelMcClellan"] == 4 && $saveFull ? 1 : 0),
                "isGrant1" => new MongoInt32($totalParent->isGrant1 += $resUpd["parent"]["levelGrant"] == 1 && $saveFull ? 1 : 0),
                "isGrant2" => new MongoInt32($totalParent->isGrant2 += $resUpd["parent"]["levelGrant"] == 2 && $saveFull ? 1 : 0),
                "isGrant3" => new MongoInt32($totalParent->isGrant3 += $resUpd["parent"]["levelGrant"] == 3 && $saveFull ? 1 : 0),
                "isGrant4" => new MongoInt32($totalParent->isGrant4 += $resUpd["parent"]["levelGrant"] == 4 && $saveFull ? 1 : 0)
            )
        );
        $collectionUser->update($query, $setQuery);
    }
}

////////////////////////////////////////////////////////////////

$response[DATA][ACHIVIMENTS_PATH] = $upd->getLastAch($link, $idUser);

///////////////////////////////////SEND PUSH ///////////////////////////////////


if (isset($registrationIds) && $isBot == NO_BOT) {

    $firebase = new Firebase();
    $push = new Push();

    $title = 'Freedom or Union';
    $message = $lang ? CODE_TURN_ENEMY_EN : CODE_TURN_ENEMY_RU;
    $push_type = 'turn';

    $push->setTitle($title);
    $push->setMessage($message);
    $push->setType($push_type);

    $dataTurns = array();
    if (!empty($turn)) {
        array_push($dataTurns, $turn);
    }

    $dataTurn = array();
    $dataTurn[DATA][ACHIVIMENTS_PATH] = $upd->getLastAch($link, $idEnemy);
    $dataTurn[DATA][TURNS_PATH] = $dataTurns;
    $dataTurn[DATA][PROGRESS] = $progress;
    $dataTurn[SUCCESS] = CODE_COMPLITE;

    $push->setData($dataTurn);

    $json = $push->getPush();
    $res = $firebase->send($registrationIds, $json);

    $response[DATA]["push"] = $res;
}

////////////////////////////////////////////////////////////////

$response[LEVEL] = 560;

$response[DATA]["progress"] = $progress;
$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));
