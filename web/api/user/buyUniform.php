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

if (!isset($post->id_uniform) || !isset($post->type)) {
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND_PARAMS;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_PARAMS_EN : CODE_ERROR_NOT_FOUND_PARAMS_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$id_uniform = $post->id_uniform;


//////////////////////////////////////////////////////////////////////

$type = '';
$typeId = $post->type;

switch ($typeId) {
    case 0:
        $type = 'hat';
        break;
    case 1:
        $type = 'gun';
        break;
    case 2:
        $type = 'uniform';
        break;
    case 3:
        $type = 'riffle';
        break;
    default :

        $response[SUCCESS] = CODE_ERROR_NOT_FOUND_PARAMS;
        $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_PARAMS_EN : CODE_ERROR_NOT_FOUND_PARAMS_RU;
        die(json_encode($response, JSON_UNESCAPED_SLASHES));

        break;
}

////////////////////////////////////////////////////////////////////////////

$response = array();

$m = new MongoClient($connection_url);
$link = $m->selectDB($db_name);

$collectionUser = $link->selectCollection(TABLE_USER);
$query = array('user_uid' => $user_uid);
$cursor = $collectionUser->find($query);

if ($cursor->count() <= 0) {
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_EN : CODE_ERROR_NOT_FOUND_RU;
    $m->close();
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$idCurrentUser;
$costUser;
$unifornList = array();
$unifornEq = array();


foreach ($cursor as $row) {
    $idCurrentUser = $row["_id"]->{'$id'};
    $costUser = $row["coins"];

    if ($row["uniform_buy"] != null) {
        $unifornList = $row["uniform_buy"];
    }
    if ($row["uniform_eq"] != null) {
        $unifornEq = $row["uniform_eq"];
    }
}

//////////////// GET UNIFORM 
$collectionUniform = $link->selectCollection(TABLE_UNIFORM);
$queryUniform = array("fraction_id" => new MongoInt32($id_uniform));
$cursorUniform = $collectionUniform->find($queryUniform);

if ($cursorUniform->count() <= 0 && $id_uniform > 0) {
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_EQUIP_NAME_EN : CODE_ERROR_NOT_EQUIP_NAME_RU;
    $m->close();
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$idUniform = 0;
$costUniform = 0;

foreach ($cursorUniform as $row) {
    $idUniform = $row["fraction_id"];
    $costUniform = $row["cost"];
}

if ($costUser >= $costUniform) {

    array_push($unifornList, $idUniform);
    $unifornEq[$type] = $id_uniform != 0 ? $idUniform : 0;

    $costUser -= $costUniform;
    /////////////// UPDATE USER //////////////
    $query = array('_id' => new MongoId($idCurrentUser));

    $setQuery = array(
        '$set' => array(
            "coins" => $costUser,
            "uniform_buy" => $unifornList,
            "uniform_eq" => $unifornEq,
            "date_time" => $dateToday->getTimestamp()
        )
    );

    $collectionUser->update($query, $setQuery);

    $uniformUpdate = array('type' => $typeId, 'uniform' => $idUniform);
    $response[DATA]["uniform_update"] = $uniformUpdate;
    $response[DATA]["uniform_buy"] = $unifornList;
    $response[DATA]["uniform_eq"] = $unifornEq;

    $response[DATA]["cost"] = $costUser;

    $response[SUCCESS] = CODE_COMPLITE;
    $response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
    $m->close();
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
    
    
    
    
} else {


    $response[SUCCESS] = CODE_ERROR_NOT_ENOUGHT_MONEY;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_ENOUGHT_MONEY_EN : CODE_ERROR_NOT_ENOUGHT_MONEY_RU;
    $m->close();
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));
