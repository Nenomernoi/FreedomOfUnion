<?php

include '../connection.php';

$m = new MongoClient($connection_url);
$link = $m->selectDB($db_name);

$response = array();
$collectionUser = $link->selectCollection(TABLE_BANNER);
$cursor = $collectionUser->find();

$response[DATA] = array();

foreach ($cursor as $row) {

    $banner = array();
    $banner["serverId"] = $row["_id"]->{'$id'};
    $banner["name"] = $row["name"];
    $banner["avatar_url"] = PATH_IMAGE . $row["image"];
    $banner["page"] = $row["url"];

    array_push($response[DATA], $banner);
}

$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;


$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));
