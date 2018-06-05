<?php

include '../connection.php';

include '../class/card.php';
include '../class/user.php';
include '../class/userParam.php';

include './updateArchiviment.php';

include '../push/firebase.php';
include '../push/push.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response[DATA][ACHIVIMENTS_PATH] = NULL;
    $response[SUCCESS] = CODE_ERROR_METHTOD;
    $response[MESSAGE] = $lang ? CODE_ERROR_METHOD_EN : CODE_ERROR_METHOD_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}
$post = json_decode(file_get_contents("php://input"));

if (!isset($post->id_game) || !isset($post->card) || !isset($post->uuid)) {
    $response[DATA][ACHIVIMENTS_PATH] = NULL;
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND_PARAMS;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_PARAMS_EN : CODE_ERROR_NOT_FOUND_PARAMS_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$upd = new updateArchiviment();

$progress = GAME_PLAY;
$isBot = NO_BOT;
$turns = array();
$registrationIds = null;
$idEnemy = null;

if (isset($post->player_bot)) {
    $isBot = $post->player_bot;
}

$idGame = $post->id_game;
$cards = $post->cards;
$user_uid = $post->uuid;

/////////////////////////////// INIT GAMERS AND GAME///////////////////////////////////////////
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

$idGamer;

foreach ($cursor as $row) {
    $idGamer = $row["_id"]->{'$id'};
    break;
}

//////////////////////////////////////////////////////////////////////////////

$collectionGame = $link->selectCollection(TABLE_GAMES);
$query = array('_id' => new MongoId($idGame));
$cursor = $collectionGame->find($query);

if ($cursor->count() <= 0) {
    $response[DATA][ACHIVIMENTS_PATH] = $upd->getLastAch($link, $idGamer);
    $response[PROGRESS] = WINNER_PARENT;
    $response[SUCCESS] = CODE_COMPLITE;
    $response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
    $m->close();
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$parent = new User_param();
$child = new User_param();

$turns = array();

$tenserTurns = array();
$tenserTurns["game"] = $idGame;
$tenserTurns["gamer"] = $idGamer;
$tenserTurns["mode"] = 2;
$tenserTurns["card"] = $cards;

foreach ($cursor as $row) {

    $progress = $row[PROGRESS];

    if ($progress != GAME_PLAY) {

        $collectionGame = $link->selectCollection(TABLE_GAMES);
        $query = array('_id' => new MongoId($idGame));
        $cursor = $collectionGame->find($query);
        $collectionGame->remove($query);

//////////////////////////////////////////////////////////////////////////

        $response[DATA][ACHIVIMENTS_PATH] = $upd->getLastAch($link, $idGamer);

        $date = new DateTime();
        $response[DATA][TIME] = $date->getTimestamp();
        $response[DATA][PROGRESS] = $progress;
        $response[SUCCESS] = CODE_COMPLITE;
        $response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
        $m->close();
        die(json_encode($response, JSON_UNESCAPED_SLASHES));
    }



    $tenserTurns["state"] = $row;

    $turns = $row["turns"];

    $parent->id = $row["id_parent"]->{'$id'};

    $parent->bank = $row['params']['bank'];
    $parent->money = $row['params']['money'];
    $parent->industry = $row['params']['industry'];
    $parent->techo = $row['params']['techo'];
    $parent->state = $row['params']['state'];
    $parent->units = $row['params']['units'];
    $parent->bonus_money_1 = $row['params']['bonus_money_1'];
    $parent->bonus_money_2 = $row['params']['bonus_money_2'];
    $parent->bonus_money_3 = $row['params']['bonus_money_3'];
    $parent->bonus_techo_1 = $row['params']['bonus_techo_1'];
    $parent->bonus_techo_2 = $row['params']['bonus_techo_2'];
    $parent->bonus_techo_3 = $row['params']['bonus_techo_3'];
    $parent->bonus_units_1 = $row['params']['bonus_units_1'];
    $parent->bonus_units_2 = $row['params']['bonus_units_2'];
    $parent->bonus_units_3 = $row['params']['bonus_units_3'];
    $parent->tower = $row['params']['tower'];
    $parent->row = $row['params']['row'];
    $parent->fraction = $row['fraction_parent'];

    $parent->money_bon = $row['params']['money_bon'];
    $parent->techo_bon = $row['params']['techo_bon'];
    $parent->units_bon = $row['params']['units_bon'];
    $parent->tower_bon = $row['params']['tower_bon'];
    $parent->row_bon = $row['params']['row_bon'];
    $parent->atack_bon = $row['params']['atack_bon'];
//////////////////////////////////////////////////////////
    $child->id = $row["id_child"]->{'$id'};

    $child->bank = $row['params']['bank_enemy'];
    $child->money = $row['params']['money_enemy'];
    $child->industry = $row['params']['industry_enemy'];
    $child->techo = $row['params']['techo_enemy'];
    $child->units = $row['params']['units_enemy'];
    $child->state = $row['params']['state_enemy'];
    $child->bonus_money_1 = $row['params']['bonus_money_enemy_1'];
    $child->bonus_money_2 = $row['params']['bonus_money_enemy_2'];
    $child->bonus_money_3 = $row['params']['bonus_money_enemy_3'];
    $child->bonus_techo_1 = $row['params']['bonus_techo_enemy_1'];
    $child->bonus_techo_2 = $row['params']['bonus_techo_enemy_2'];
    $child->bonus_techo_3 = $row['params']['bonus_techo_enemy_3'];
    $child->bonus_units_1 = $row['params']['bonus_units_enemy_1'];
    $child->bonus_units_2 = $row['params']['bonus_units_enemy_2'];
    $child->bonus_units_3 = $row['params']['bonus_units_enemy_3'];
    $child->tower = $row['params']['tower_enemy'];
    $child->row = $row['params']['row_enemy'];

    $child->money_bon = $row['params']['money_bon_enemy'];
    $child->techo_bon = $row['params']['techo_bon_enemy'];
    $child->units_bon = $row['params']['units_bon_enemy'];
    $child->tower_bon = $row['params']['tower_bon_enemy'];
    $child->row_bon = $row['params']['row_bon_enemy'];
    $child->atack_bon = $row['params']['atack_bon_enemy'];

    $child->fraction = $row['fraction_child'];
}

//////////////////////////////////////////////////////////////////////////////

$collectionTensor = $link->selectCollection(TABLE_TURNS);
$collectionTensor->insert($tenserTurns);

//////////////////////////////////////////////////////////////////////////////

$turnCount = sizeof($turns);

////// TURN CHILD?
$isChildTurn = (strcmp($child->id, $idGamer) == 0 && $isBot == -1) || $isBot == BOT;

//////////////////////////////////////////////////
foreach ($cards as $card) {

    if ($isChildTurn) {
        $child->remOneBonus($card, $link, $parent);
    } else {
        $parent->remOneBonus($card, $link, $child);
    }
}

/////////////////////////////////////////////////////////
$params = array(
    "bank" => new MongoInt32($parent->bank),
    "money" => new MongoInt32($parent->money),
    "industry" => new MongoInt32($parent->industry),
    "techo" => new MongoInt32($parent->techo),
    "state" => new MongoInt32($parent->state),
    "units" => new MongoInt32($parent->units),
    "bonus_money_1" => new MongoInt32($parent->bonus_money_1),
    "bonus_money_2" => new MongoInt32($parent->bonus_money_2),
    "bonus_money_3" => new MongoInt32($parent->bonus_money_3),
    "bonus_techo_1" => new MongoInt32($parent->bonus_techo_1),
    "bonus_techo_2" => new MongoInt32($parent->bonus_techo_2),
    "bonus_techo_3" => new MongoInt32($parent->bonus_techo_3),
    "bonus_units_1" => new MongoInt32($parent->bonus_units_1),
    "bonus_units_2" => new MongoInt32($parent->bonus_units_2),
    "bonus_units_3" => new MongoInt32($parent->bonus_units_3),
    "tower" => new MongoInt32($parent->tower),
    "row" => new MongoInt32($parent->row),
    "money_bon" => new MongoInt32($parent->money_bon),
    "techo_bon" => new MongoInt32($parent->techo_bon),
    "units_bon" => new MongoInt32($parent->units_bon),
    "tower_bon" => new MongoInt32($parent->tower_bon),
    "row_bon" => new MongoInt32($parent->row_bon),
    "atack_bon" => new MongoInt32($parent->atack_bon),
    //////////////////////////////////////////////////////
    "bank_enemy" => new MongoInt32($child->bank),
    "money_enemy" => new MongoInt32($child->money),
    "industry_enemy" => new MongoInt32($child->industry),
    "techo_enemy" => new MongoInt32($child->techo),
    "state_enemy" => new MongoInt32($child->state),
    "units_enemy" => new MongoInt32($child->units),
    "bonus_money_enemy_1" => new MongoInt32($child->bonus_money_1),
    "bonus_money_enemy_2" => new MongoInt32($child->bonus_money_2),
    "bonus_money_enemy_3" => new MongoInt32($child->bonus_money_3),
    "bonus_techo_enemy_1" => new MongoInt32($child->bonus_techo_1),
    "bonus_techo_enemy_2" => new MongoInt32($child->bonus_techo_2),
    "bonus_techo_enemy_3" => new MongoInt32($child->bonus_techo_3),
    "bonus_units_enemy_1" => new MongoInt32($child->bonus_units_1),
    "bonus_units_enemy_2" => new MongoInt32($child->bonus_units_2),
    "bonus_units_enemy_3" => new MongoInt32($child->bonus_units_3),
    "tower_enemy" => new MongoInt32($child->tower),
    "row_enemy" => new MongoInt32($child->row),
    "money_bon_enemy" => new MongoInt32($child->money_bon),
    "techo_bon_enemy" => new MongoInt32($child->techo_bon),
    "units_bon_enemy" => new MongoInt32($child->units_bon),
    "tower_bon_enemy" => new MongoInt32($child->tower_bon),
    "row_bon_enemy" => new MongoInt32($child->row_bon),
    "atack_bon_enemy" => new MongoInt32($child->atack_bon)
);

//////////////////////////
///////////////////// INIT GCM KEY ///////////////////////////////


if (strcmp($parent->id, $idGamer) != 0) {
    $query = array('_id' => new MongoId($parent->id));
}
if (strcmp($child->id, $idGamer) != 0) {
    $query = array('_id' => new MongoId($child->id));
}
$cursor = $collectionUser->find($query);
foreach ($cursor as $row) {
    $registrationIds = $row["tokenGcm"];
    $idEnemy = $row["_id"]->{'$id'};
    break;
}

//////////////////////////////INIT ATLAS AND REPLACE CARD ON NEW/ ///////////////////////////

$date = new DateTime();
foreach ($cards as $card) {

    $time = $date->getTimestamp();
    $turn = array(
        "time" => $time,
        "turn_count" => $turnCount + 1,
        "card" => (int) $card,
        "card_new" => 0,
        "escape" => 2,
        "progress" => $progress,
        "bot" => (int) $isBot,
        "id_gamer" => $idGamer,
        "id_game" => $idGame
    );
    array_push($turns, $turn);
}

/////////////////////////////SAVE DB  ////////////////////

$query = array('_id' => new MongoId($idGame));
$setQuery = array(
    '$set' => array(
        "date_time" => $dateToday->getTimestamp(),
        "params" => $params,
        "progress" => $progress,
        "turns" => $turns
    )
);
$collectionGame->update($query, $setQuery);


if (isset($registrationIds) && $isBot == NO_BOT) {

    $dataTurns = array();
    if (!empty($turn)) {
        array_push($dataTurns, $turn);
    }

    $dataTurn = array();
    $dataTurn[DATA][ACHIVIMENTS_PATH] = $upd->getLastAch($link, $idEnemy);
    $dataTurn[DATA][TURNS_PATH] = $dataTurns;
    $dataTurn[DATA][PROGRESS] = $progress;
    $dataTurn[DATA][TURN_COUNT] = $turnCount + 1;
    $dataTurn[SUCCESS] = CODE_COMPLITE;

    $firebase = new Firebase();
    $push = new Push();

    $title = 'Freedom or Union';
    $message = $lang ? CODE_TURN_ENEMY_EN : CODE_TURN_ENEMY_RU;
    $push_type = 'turn';

    $push->setTitle($title);
    $push->setMessage($message);
    $push->setType($push_type);

    $push->setData($dataTurn);

    $json = $push->getPush();
    $res = $firebase->send($registrationIds, $json);

    $response[DATA][PUSH] = $res;
}


$response[DATA][ACHIVIMENTS_PATH] = NULL;
$response[DATA][PROGRESS] = $progress;
$response[DATA][TIME] = $time;
$response[DATA][TURN_COUNT] = $turnCount + 1;
$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));
