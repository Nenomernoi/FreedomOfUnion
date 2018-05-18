<?php

include '../connection.php';

$m = new MongoClient($connection_url);
$link = $m->selectDB($db_name);

$response = array();
$collectionUser = $link->selectCollection(TABLE_HISTORY);
$cursor = $collectionUser->find();

$response[DATA] = array();
$response[DATA]["history"] = array();

foreach ($cursor as $row) {

    $avatar = array();
    $avatar["avatar_url"] = $row["image"];
    //    $avatar["avatar_url"] = PATH_IMAGE . $row["image"];
    $avatar["nameRu"] = $row["name_rus"];
    $avatar["nameEn"] = $row["name_en"];
    $avatar["date"] = $row["date"];

    array_push($response[DATA]["history"], $avatar);
}

$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));
