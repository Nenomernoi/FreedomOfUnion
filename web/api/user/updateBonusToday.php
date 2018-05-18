<?php

include '../connection.php';

if (!isset($_GET["id"]) || !isset($_GET["uuid"]) || !isset($_GET["remove"])) {
    $response[DATA] = null;
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND_PARAMS;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_PARAMS_EN : CODE_ERROR_NOT_FOUND_PARAMS_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$id = $_GET["id"];
$uuid = $_GET["uuid"];
$remove = $_GET["remove"];

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

$value = -1;
$coins = 0;

foreach ($cursor as $row) {

    $coins = $row["coins"];

    if (isset($row["bonus"])) {

        $bonus = $row["bonus"];

        if ($bonus["first"]["uuid"] == $uuid) {
            $value = $bonus["first"]["value"];
            break;
        }

        if ($bonus["second"]["uuid"] == $uuid) {
            $value = $bonus["second"]["value"];
            break;
        }

        if ($bonus["third"]["uuid"] == $uuid) {
            $value = $bonus["third"]["value"];
            break;
        }
    }
}

if ($value == -1) {
    
    $setQuery = array(
        '$set' => array(
            "bonus" => NULL,
            "date_time" => $dateToday->getTimestamp()
        )
    );
    $collectionUser->update($query, $setQuery);
    
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_PARAMS_EN : CODE_ERROR_NOT_FOUND_PARAMS_RU;
    $m->close();
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$setQuery;

if ($remove == 1) {
    $setQuery = array(
        '$set' => array(
            "coins" => new MongoInt32($coins + $value),
            "bonus" => NULL,
            "date_time" => $dateToday->getTimestamp()
        )
    );
} else {
    $setQuery = array(
        '$set' => array(
            "coins" => new MongoInt32($coins + $value),
            "date_time" => $dateToday->getTimestamp()
        )
    );
}

$collectionUser->update($query, $setQuery);

$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));

