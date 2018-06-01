<?php

include '../connection.php';

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
$id_parent = null;

foreach ($cursor as $row) {
    $id_parent = $row["id_parent"]->{'$id'};
    $idGame = isset($row["id_game"]) ? $row["id_game"]->{'$id'} : null;

    $collectionGame = $link->selectCollection(TABLE_GAMES);
    $query = array('_id' => new MongoId($idGame));
    $cursorGame = $collectionGame->find($query);
    foreach ($cursorGame as $rowGame) {

        $collectionGame->remove($query);

        $isBot = strcmp($rowGame["id_parent"]->{'$id'}, $rowGame["id_child"]->{'$id'}) == 0;


///////////////////// INIT GCM KEY ///////////////////////////////

        $registrationIds = null;
        $idEnemy = null;
        $queryReg = null;
        
        if (strcmp($parent->id, $idGamer) != 0) {
            $queryReg = array('_id' => new MongoId($parent->id));
        }
        if (strcmp($child->id, $idGamer) != 0) {
            $queryReg = array('_id' => new MongoId($child->id));
        }
        $cursorReg = $collectionUser->find($queryReg);
        foreach ($cursorReg as $rowReg) {
            $registrationIds = $rowReg["tokenGcm"];
            $idEnemy = $rowReg["_id"]->{'$id'};
        }

//////////////////////////////////////////////////////////////

        if ($rowGame[PROGRESS] != GAME_PLAY || $isBot) {
            break;
        }

        $date = new DateTime();
        $time = $date->getTimestamp();


        $turn = array(
            "time" => $time,
            "card" => 0,
            "card_new" => 0,
            "escape" => 0,
            "progress" => RESIGN,
            "bot" => 0,
            "id_gamer" => $id_parent,
            "id_game" => $idGame
        );

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
        $dataTurn[DATA][PROGRESS] = RESIGN;
        $dataTurn[SUCCESS] = CODE_COMPLITE;

        $push->setData($dataTurn);

        $json = $push->getPush();
        $res = $firebase->send($registrationIds, $json);
    }
}


$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));

