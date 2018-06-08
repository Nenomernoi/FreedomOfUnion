<?php

include '../connection.php';

if (!isset($_GET["id"])) {
    $response[DATA] = null;
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND_PARAMS;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_PARAMS_EN : CODE_ERROR_NOT_FOUND_PARAMS_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$id = $_GET["id"];
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

$timestamp;

$bonus = NULL;

foreach ($cursor as $row) {
    $timestamp = isset($row["today"]) ? $row["today"] : $dateToday->getTimestamp();
    $bonus = isset($row["bonus"]) ? $row["bonus"] : NULL;
}

if ($bonus != NULL) {
    $response[DATA] = $bonus;
    $response[SUCCESS] = CODE_COMPLITE;
    $response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
    $m->close();
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
} else {
    $bonus = array();
}

$date = new DateTime();
$match_date = new DateTime();
$match_date->setTimestamp($timestamp);
$interval = $date->diff($match_date);

if ($interval->days == 0) {
    $response[DATA] = NULL;
} else {

    $rnd = rand(0, 100);
    $valueFisrt = $rnd < 95 ? 1 : 5;

    $rnd = rand(0, 100);
    $valueSecond = $rnd < 60 ? 1 : 2;
    $valueThird = $rnd > 60 ? 1 : 2;

    $first = array();
    $second = array();
    $third = array();

    $first["uuid"] = uniqid("", true);
    $second["uuid"] = uniqid("", true);
    $third["uuid"] = uniqid("", true);


    switch (rand(1, 3)) {
        case 1:
            $first["value"] = $valueFisrt;
            $second["value"] = $valueSecond;
            $third["value"] = $valueThird;
            break;
        case 2:
            $first["value"] = $valueSecond;
            $second["value"] = $valueFisrt;
            $third["value"] = $valueThird;
            break;
        case 3:
            $first["value"] = $valueThird;
            $second["value"] = $valueSecond;
            $third["value"] = $valueFisrt;
            break;
    }


    $bonus["first"] = $first;
    $bonus["second"] = $second;
    $bonus["third"] = $third;

    $setQuery = array('$set' => array(
            "today" => $dateToday->getTimestamp(),
            "date_time" => $dateToday->getTimestamp(),
            "bonus" => $bonus
        )
    );
    $response[DATA] = $bonus;
    $collectionUser->update($query, $setQuery);
}


$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));
