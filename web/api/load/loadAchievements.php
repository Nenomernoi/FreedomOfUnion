<?php

include '../connection.php';

$m = new MongoClient($connection_url);
$link = $m->selectDB($db_name);

$response = array();
$collectionUser = $link->selectCollection(TABLE_ACH);
$cursor = $collectionUser->find();

$response[DATA] = array();

foreach ($cursor as $row) {

    $ach = array();
    $ach["serverId"] = $row["_id"];
    $ach["name_eng"] = isset($row["name_eng"]) ? $row["name_eng"] : "";
    $ach["name_rus"] = isset($row["name_rus"]) ? $row["name_rus"] : "";
    $ach["main_eng"] = isset($row["main_eng"]) ? $row["main_eng"] : "";
    $ach["main_rus"] = isset($row["main_rus"]) ? $row["main_rus"] : "";
    $ach["avatar_url"] = $row["image"];
  //  $ach["avatar_url"] = PATH_IMAGE . $row["image"];

    if ($ach["serverId"] == 2 || $ach["serverId"] == 10 ||
            $ach["serverId"] == 11 || $ach["serverId"] == 12) {
        $ach["rang"] = 4;
    } else {
        $ach["rang"] = 0;
    }
    array_push($response[DATA], $ach);
}

$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;

$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));
