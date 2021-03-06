<?php

include '../connection.php';

include '../push/firebase.php';
include '../push/push.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
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

$m = new MongoClient($connection_url);
$link = $m->selectDB($db_name);

//////////////////////////////////////////////////////////////////////

$collectionUser = $link->selectCollection(TABLE_USER);
$query = array('user_uid' => $user_uid);
$cursor = $collectionUser->find($query);

if ($cursor->count() <= 0) {
    $response[SUCCESS] = CODE_ERROR_AUTH;
    $response[MESSAGE] = $lang ? CODE_ERROR_AUTH_OLD_EN : CODE_ERROR_AUTH_OLD_RU;
    $m->close();
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

/////////////////////////////////////////////////////////////////////////

$collectionGame = $link->selectCollection(TABLE_GAMES);
$query = array('_id' => new MongoId($idConGame));
$cursor = $collectionGame->find($query);

if ($cursor->count() <= 0) {
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_GAME_EN : CODE_ERROR_NOT_FOUND_GAME_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$id_parent = null;

foreach ($cursor as $row) {
    $id_parent = $row["id_parent"]->{'$id'};

    if ($row["progress"] != GAME_PLAY) {

        $query = array('user_uid' => $user_uid);
        $setQuery = array(
            '$set' => array(
                "id_game" => null,
                "date_time" => $dateToday->getTimestamp()
            )
        );
        $collectionUser->update($query, $setQuery);

        $collectionGame->remove($query);

        ///////////////////////////////////////////////////////////////////////////

        $response[SUCCESS] = CODE_ERROR_NOT_FOUND;
        $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_GAME_EN : CODE_ERROR_NOT_FOUND_GAME_RU;
        $m->close();
        die(json_encode($response, JSON_UNESCAPED_SLASHES));
    }
}

$collectionUser = $link->selectCollection(TABLE_USER);
$query = array('user_uid' => $user_uid);
$cursor = $collectionUser->find($query);


$idGame = null;
$ObjUser = null;
$childAvatar;
$childName;
$childFraction;

foreach ($cursor as $row) {
    $ObjUser = $row["_id"]->{'$id'};
    $idGame = isset($row["id_game"]) ? $row["id_game"]->{'$id'} : null;
    $childName = $row["name"];
    $childAvatar = $row["avatar_url"];
    $childFraction = $row["fraction"];
}

if ($idGame != null) {
    // ПРОВЕРКА  ОБНОВЛЕНИЕ ИЛИ УДАЛЕНИЕ 
    $query = array('_id' => new MongoId($idGame));
    $cursor = $collectionGame->find($query);

    foreach ($cursor as $row) {
        if ($row["progress"] == GAME_PLAY) {
            $response[SUCCESS] = CODE_ERROR_YOU_IN_GAME;
            $response[MESSAGE] = $lang ? CODE_ERROR_YOU_IN_GAME_EN : CODE_ERROR_YOU_IN_GAME_RU;
            $m->close();
            die(json_encode($response, JSON_UNESCAPED_SLASHES));
            
        } else {

            $query = array('user_uid' => $user_uid);
            $setQuery = array(
                '$set' => array(
                    "id_game" => null,
                    "date_time" => $dateToday->getTimestamp()
                )
            );
            $collectionUser->update($query, $setQuery);

            $collectionGame->remove($query);

            ///////////////////////////////////////////////////////////////////////////
        }
    }
}

$query = array('user_uid' => $user_uid);
$setQuery = array(
    '$set' => array(
        "id_game" => new MongoId($idConGame),
        "date_time" => $dateToday->getTimestamp()
    )
);
$collectionUser->update($query, $setQuery);

///////////////////// GET ID PLAYER AND ENEMY ///////////////
$id_child = $ObjUser;

$unifornParent = array();
$unifornChild = array();

$query = array('$or' => array(
        array("_id" => new MongoId($id_parent)),
        array("_id" => new MongoId($ObjUser))
    )
);
$cursor = $collectionUser->find($query);

$fractionParent;

$mightParent = 0;
$mightChild = 0;

foreach ($cursor as $row) {
    $fractionParent = $row["fraction"];
    if ($row["uniform_buy"] != null) {
        if ($id_parent == $row["_id"]->{'$id'}) {
            $unifornParent = $row["uniform_buy"];
            $mightParent = $row["game_only"]/($row["game_win"] > 0 ? $row["game_win"] : 1);
        }
        if ($id_child == $row["_id"]->{'$id'}) {
            $unifornChild = $row["uniform_buy"];
            $mightChild = $row["game_only"]/($row["game_win"] > 0 ? $row["game_win"] : 1);
        }
    }
}
/// Определяем сильнейшего игрока
$whoIsBully = $mightParent == $mightChild ? "" : $mightParent > $mightChild ? $id_parent : $id_child;

////////////////////PARAMS UPDATE ///////////

$tehnoUn = 0;
$unitsUn = 0;
$moneyUn = 0;
$fortUn = 0;
$rovUn = 0;
$rovEnemyUn = 0;


$collectionUniform = $link->selectCollection(TABLE_UNIFORM);


foreach ($unifornParent as $objUniform) {

    $queryUniform = array("fraction_id" => new MongoInt32($objUniform));
    $cursorUniform = $collectionUniform->find($queryUniform);

    foreach ($cursorUniform as $row) {
        $tehnoUn += $row["zavod"];
        $unitsUn += $row["units"];
        $moneyUn += $row["money"];
        $fortUn += $row["tower"];
        $rovUn += $row["row"];
        $rovEnemyUn += $row["row_enemy"];
        break;
    }
}

$tehnoUnEn = 0;
$unitsUnEn = 0;
$moneyUnEn = 0;
$fortUnEn = 0;
$rovUnEn = 0;
$rovEnemyUnEn = 0;


foreach ($unifornChild as $objUniform) {

    $queryUniform = array("fraction_id" => new MongoInt32($objUniform));
    $cursorUniform = $collectionUniform->find($queryUniform);

    foreach ($cursorUniform as $row) {
        $tehnoUnEn += $row["zavod"];
        $unitsUnEn += $row["units"];
        $moneyUnEn += $row["money"];
        $fortUnEn += $row["tower"];
        $rovUnEn += $row["row"];
        $rovEnemyUnEn += $row["row_enemy"];
        break;
    }
}


$params = array(
    "bank" => new MongoInt32(DEFAULT_MINE),
    "money" => new MongoInt32(DEFAULT_RES + $moneyUn),
    "industry" => new MongoInt32(DEFAULT_MINE),
    "techo" => new MongoInt32(DEFAULT_RES + $tehnoUn),
    "state" => new MongoInt32(DEFAULT_MINE),
    "units" => new MongoInt32(DEFAULT_RES + $unitsUn),
    "bonus_money_1" => new MongoInt32(EMPTY_CARD),
    "bonus_money_2" => new MongoInt32(EMPTY_CARD),
    "bonus_money_3" => new MongoInt32(EMPTY_CARD),
    "bonus_techo_1" => new MongoInt32(EMPTY_CARD),
    "bonus_techo_2" => new MongoInt32(EMPTY_CARD),
    "bonus_techo_3" => new MongoInt32(EMPTY_CARD),
    "bonus_units_1" => new MongoInt32(EMPTY_CARD),
    "bonus_units_2" => new MongoInt32(EMPTY_CARD),
    "bonus_units_3" => new MongoInt32(EMPTY_CARD),
    "tower" => new MongoInt32(DEFAULT_TOWER + $fortUn),
    "row" => new MongoInt32(DEFAULT_ROV + $rovUn + $rovEnemyUnEn),
    "money_bon" => new MongoInt32(0),
    "techo_bon" => new MongoInt32(0),
    "units_bon" => new MongoInt32(0),
    "tower_bon" => new MongoInt32(0),
    "row_bon" => new MongoInt32(0),
    "atack_bon" => new MongoInt32(0),
    "fraction" => new MongoInt32($fractionParent),
    /////////////////////////////////////////
    "bank_enemy" => new MongoInt32(DEFAULT_MINE),
    "money_enemy" => new MongoInt32(DEFAULT_RES + $moneyUnEn),
    "industry_enemy" => new MongoInt32(DEFAULT_MINE),
    "techo_enemy" => new MongoInt32(DEFAULT_RES + $tehnoUnEn),
    "state_enemy" => new MongoInt32(DEFAULT_MINE),
    "units_enemy" => new MongoInt32(DEFAULT_RES + $unitsUnEn),
    "bonus_money_enemy_1" => new MongoInt32(EMPTY_CARD),
    "bonus_money_enemy_2" => new MongoInt32(EMPTY_CARD),
    "bonus_money_enemy_3" => new MongoInt32(EMPTY_CARD),
    "bonus_techo_enemy_1" => new MongoInt32(EMPTY_CARD),
    "bonus_techo_enemy_2" => new MongoInt32(EMPTY_CARD),
    "bonus_techo_enemy_3" => new MongoInt32(EMPTY_CARD),
    "bonus_units_enemy_1" => new MongoInt32(EMPTY_CARD),
    "bonus_units_enemy_2" => new MongoInt32(EMPTY_CARD),
    "bonus_units_enemy_3" => new MongoInt32(EMPTY_CARD),
    "tower_enemy" => new MongoInt32(DEFAULT_TOWER + $fortUnEn),
    "row_enemy" => new MongoInt32(DEFAULT_ROV + $rovUnEn + $rovEnemyUn),
    "money_bon_enemy" => new MongoInt32(0),
    "techo_bon_enemy" => new MongoInt32(0),
    "units_bon_enemy" => new MongoInt32(0),
    "tower_bon_enemy" => new MongoInt32(0),
    "row_bon_enemy" => new MongoInt32(0),
    "atack_bon_enemy" => new MongoInt32(0),
    "fraction_enemy" => new MongoInt32($childFraction)
);

/////////////////////////////////////


$query = array('_id' => new MongoId($idConGame));

$setQuery = array(
    '$set' => array(
        "id_child" => new MongoId($ObjUser),
        "name_child" => $childName,
        "avatar_child" => $childAvatar,
        "fraction_child" => new MongoInt32($childFraction),
        "date_time" => $dateToday->getTimestamp(),
        'whoIsBully' => $whoIsBully,
        "params" => $params
    )
);
$collectionGame->update($query, $setQuery);

///////////////  Create Achiviments CACHE ///////////////////////

$achiviments = array('isStoneWall' => TRUE,
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
$query = array('_id' => $id_parent);
$setQuery = array(
    '$set' => $achiviments
);
$collectionAch->update($query, $setQuery);
$query = array('_id' => $id_child);
$collectionAch->update($query, $setQuery);

/////////////////////////////////////////////////////////////////////



$query = array('_id' => new MongoId($id_parent));
$cursor = $collectionUser->find($query);

$registrationIds;
foreach ($cursor as $row) {
    $registrationIds = $row["tokenGcm"];
}

if (isset($registrationIds)) {

    $firebase = new Firebase();
    $push = new Push();

    $title = 'Freedom or Union';
    $message = $lang ? CODE_CONNECTED_ENEMY_EN : CODE_CONNECTED_ENEMY_RU;
    $push_type = 'game';


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
