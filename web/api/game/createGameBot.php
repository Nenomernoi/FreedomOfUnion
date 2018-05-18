<?php

include '../connection.php';
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

if (!isset($post->avatar_url) || !isset($post->avatar_bot) || !isset($post->name_bot)) {
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND_PARAMS;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_PARAMS_EN : CODE_ERROR_NOT_FOUND_PARAMS_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$avatar_url = $post->avatar_url;

$avatar_bot = $post->avatar_bot;
$name_bot = $post->name_bot;

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



$unifornBuyList = array();
$name = "Game" . $dateToday->getTimestamp();
$id_gamer;
$idGame = null;

$gameOnly = 0;
$gameWin = 0;
$total = 0;
$fraction = 1;

$parentAvatar;
$parentName;
$parentFraction;

foreach ($cursor as $row) {
    $id_gamer = $row["_id"]->{'$id'};
    if (isset($row["id_game"])) {
        if ($row["id_game"] != null) {
            $idGame = $row["id_game"]->{'$id'};
        }
    }
    $name = $row["name"];
    $gameOnly = $row["game_only"];
    $gameWin = $row["game_win"];
    $total = $row["total"];
    $fraction = $row["fraction"];
    $parentFraction = $row["fraction"];
    $parentName = $row["name"];
    $parentAvatar = $row["avatar_url"];

    if ($row["uniform_buy"] != null) {
        $unifornBuyList = $row["uniform_buy"];
    }
}

if ($idGame != null) {

    $collectionGame = $link->selectCollection(TABLE_GAMES);
    $query = array('_id' => new MongoId($idGame));
    $cursor = $collectionGame->find($query);

    $progress = GAME_OVER;

    foreach ($cursor as $row) {
        $progress = $row["progress"];
    }

    if ($progress == GAME_PLAY) {
        $response[DATA] = $idGame;
        $response[SUCCESS] = CODE_COMPLITE;
        $response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
        $m->close();
        die(json_encode($response, JSON_UNESCAPED_SLASHES));
    } else {

///////////////////////////////////////////////////////////////////////////
        $collectionGame->remove($query);

        $query = array('user_uid' => $user_uid);
        $cursor = $collectionUser->find($query);
        $setQuery = array(
            '$set' => array(
                "id_game" => null,
                "date_time" => $dateToday->getTimestamp()
            )
        );
        $collectionUser->update($query, $setQuery);
    }
}
///*
$atlas = array();
array_push($atlas, mt_rand(MIN_CARD, MAX_CARD));
do {
    $value = mt_rand(count($atlas) < 5 ? MIN_CARD : MAX_CARD_DEFENCE, count($atlas) < 5 ? MAX_CARD : MAX_CARD_ATACK);
    if (!in_array($value, $atlas)) {
        array_push($atlas, $value);
    }
} while (count($atlas) < 10);



$collectionIiCards = $link->selectCollection(TABLE_II_CARDS);

$query = array('$or' => array(
        array('_id' => new MongoInt32($atlas[0])),
        array('_id' => new MongoInt32($atlas[1])),
        array('_id' => new MongoInt32($atlas[2])),
        array('_id' => new MongoInt32($atlas[3])),
        array('_id' => new MongoInt32($atlas[4])),
        array('_id' => new MongoInt32($atlas[5])),
        array('_id' => new MongoInt32($atlas[6])),
        array('_id' => new MongoInt32($atlas[7])),
        array('_id' => new MongoInt32($atlas[8])),
        array('_id' => new MongoInt32($atlas[9]))
        ));

$cursor = $collectionIiCards->find($query);
$atlas = array();
foreach ($cursor as $row) {
    array_push($atlas, $row["id_old"]);
}
// */
/*
  $atlas = array(
  181,
  42,
  180,
  13,
  31,
  112,
  180,
  112,
  112,
  112
  );
 */


/////////// INIT BONUS UNIFORM //////////////////

$tehnoUn = 0;
$unitsUn = 0;
$moneyUn = 0;
$fortUn = 0;
$rovUn = 0;
$rovEnemyUn = 0;

$collectionUniform = $link->selectCollection(TABLE_UNIFORM);


foreach ($unifornBuyList as $objUniform) {

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
    "row" => new MongoInt32(DEFAULT_ROV + $rovUn + $rovEnemyUn),
    // "tower" => new MongoInt32(1),
    // "row" => new MongoInt32(1),
    "money_bon" => new MongoInt32(0),
    "techo_bon" => new MongoInt32(0),
    "units_bon" => new MongoInt32(0),
    "tower_bon" => new MongoInt32(0),
    "row_bon" => new MongoInt32(0),
    "atack_bon" => new MongoInt32(0),
    "fraction" => new MongoInt32($parentFraction),
    /////////////////////////////////////////
    "bank_enemy" => new MongoInt32(DEFAULT_MINE),
    "money_enemy" => new MongoInt32(DEFAULT_RES + $moneyUn),
    "industry_enemy" => new MongoInt32(DEFAULT_MINE),
    "techo_enemy" => new MongoInt32(DEFAULT_RES + $tehnoUn),
    "state_enemy" => new MongoInt32(DEFAULT_MINE),
    "units_enemy" => new MongoInt32(DEFAULT_RES + $unitsUn),
    "bonus_money_enemy_1" => new MongoInt32(EMPTY_CARD),
    "bonus_money_enemy_2" => new MongoInt32(EMPTY_CARD),
    "bonus_money_enemy_3" => new MongoInt32(EMPTY_CARD),
    "bonus_techo_enemy_1" => new MongoInt32(EMPTY_CARD),
    "bonus_techo_enemy_2" => new MongoInt32(EMPTY_CARD),
    "bonus_techo_enemy_3" => new MongoInt32(EMPTY_CARD),
    "bonus_units_enemy_1" => new MongoInt32(EMPTY_CARD),
    "bonus_units_enemy_2" => new MongoInt32(EMPTY_CARD),
    "bonus_units_enemy_3" => new MongoInt32(EMPTY_CARD),
    "tower_enemy" => new MongoInt32(DEFAULT_TOWER + $fortUn),
    "row_enemy" => new MongoInt32(DEFAULT_ROV + $rovUn + $rovEnemyUn),
    //  "tower_enemy" => new MongoInt32(1),
    //  "row_enemy" => new MongoInt32(1),
    "money_bon_enemy" => new MongoInt32(0),
    "techo_bon_enemy" => new MongoInt32(0),
    "units_bon_enemy" => new MongoInt32(0),
    "tower_bon_enemy" => new MongoInt32(0),
    "row_bon_enemy" => new MongoInt32(0),
    "atack_bon_enemy" => new MongoInt32(0),
    "fraction_enemy" => new MongoInt32($parentFraction == SOUTH ? NORTH : SOUTH)
);

$collectionGame = $link->selectCollection(TABLE_GAMES);

$query = array(
    "name" => "[B] " . $name,
    "avatar_url" => $avatar_url,
    "progress" => GAME_PLAY,
    "id_parent" => new MongoId($id_gamer),
    "name_parent" => $parentName,
    "avatar_parent" => $parentAvatar,
    "fraction_parent" => new MongoInt32($parentFraction),
    "id_child" => new MongoId($id_gamer),
    "name_child" => "[Bot] " . $name_bot,
    "avatar_child" => $avatar_bot,
    "fraction_child" => new MongoInt32($parentFraction == SOUTH ? NORTH : SOUTH),
    "game_win" => new MongoInt32($gameWin),
    "game_only" => new MongoInt32($gameOnly),
    "fraction" => $fraction,
    "total" => new MongoInt32($total),
    "date_time" => $dateToday->getTimestamp(),
    "atlas" => $atlas,
    "params" => $params,
    "turns" => array(),
    "whoIsBully"=>$id_gamer
);
$collectionGame->insert($query);
$idGame = $query["_id"]->{'$id'};


$query = array('user_uid' => $user_uid);
$cursor = $collectionUser->find($query);
$setQuery = array(
    '$set' => array(
        "id_game" => new MongoId($idGame),
        "date_time" => $dateToday->getTimestamp()
    )
);
$collectionUser->update($query, $setQuery);

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
$query = array('_id' => $id_gamer);
$setQuery = array(
    '$set' => $achiviments
);
$collectionAch->update($query, $setQuery);


$response[DATA] = $idGame;
$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));
