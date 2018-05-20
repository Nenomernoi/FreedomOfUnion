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

if (!isset($post->avatar_url)) {
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND_PARAMS;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_PARAMS_EN : CODE_ERROR_NOT_FOUND_PARAMS_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$password = null;

if (isset($post->password)) {
    $password = $post->password;
}

$avatar_url = $post->avatar_url;

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

$gameOnly = 0;
$gameWin = 0;
$total = 0;
$fraction = 1;

$name = "Game" . $dateToday->getTimestamp();
$id_gamer;
$parentAvatar;
$parentName;
$parentFraction;

$idGame = null;
foreach ($cursor as $row) {
    $id_gamer = $row["_id"]->{'$id'};
    if (isset($row["id_game"])) {
        if ($row["id_game"] != null) {
            $idGame = $row["id_game"]->{'$id'};
        }
    }
    $name = $row["name"];
    $parentName = $row["name"];
    $parentAvatar = $row["avatar_url"];
    $gameOnly = $row["game_only"];
    $gameWin = $row["game_win"];
    $total = $row["total"];
    $fraction = $row["fraction"];
    $parentFraction = $row["fraction"];
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
/*
  $atlas = array(
  15,
  20,
  16,
  17,
  21,
  20,
  23,
  24,
  25,
  26
  );
 */

///*
$atlas = array();
array_push($atlas, mt_rand(MIN_CARD, MAX_CARD));

do {
    $value = mt_rand(MIN_CARD, MAX_CARD);
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
//*/

$collectionGame = $link->selectCollection(TABLE_GAMES);

$query = array(
    "name" => $name,
    "avatar_url" => $avatar_url,
    "password" => $password,
    "progress" => GAME_PLAY,
    "id_parent" => new MongoId($id_gamer),
    "name_parent" => $parentName,
    "avatar_parent" => $parentAvatar,
    "fraction_parent" => new MongoInt32($parentFraction),
    "id_child" => null,
    "game_win" => new MongoInt32($gameWin),
    "game_only" => new MongoInt32($gameOnly),
    "total" => new MongoInt32($total),
    "fraction" => $fraction,
    "date_time" => $dateToday->getTimestamp(),
    "atlas" => $atlas,
     'whoIsBully' => $id_gamer,
    "turns" => array()
);
$collectionGame->insert($query);
$idGame = $query["_id"]->{'$id'};


$query = array('user_uid' => $user_uid);
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

/////////////////////////////////////////////////////////////////////

$response[DATA] = $idGame;
$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));
