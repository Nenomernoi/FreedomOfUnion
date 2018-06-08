<?php

include '../connection.php';

$m = new MongoClient($connection_url);
$link = $m->selectDB($db_name);

$response = array();
$collectionInfo = $link->selectCollection(TABLE_INFO);
$cursor = $collectionInfo->find();
$response[DATA] = array();

foreach ($cursor as $row) {
    $info = array();
    $info["_id"] = $row["_id"];
    $info["image"] = $row["image"];
    $info["name_eng"] = $row["name_eng"];
    $info["name_rus"] = $row["name_rus"];
    $info["first_rus"] = $row["first_rus"];
    $info["first_eng"] = $row["first_eng"];
    $info["main_eng"] = $row["main_eng"];
    $info["main_rus"] = $row["main_rus"];
    $info["href_rus"] = $row["href_rus"];
    $info["href_eng"] = $row["href_eng"];
    $info["fraction"] = $row["fraction"];

    array_push($response[DATA], $info);
}


$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;

$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));
