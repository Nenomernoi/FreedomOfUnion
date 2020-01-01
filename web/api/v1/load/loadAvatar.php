<?php

include '../connection.php';

$m = new MongoClient($connection_url);
$link = $m->selectDB($db_name);

$response = array();
$collectionUser = $link->selectCollection(TABLE_AVATAR);
$cursor = $collectionUser->find();

$response[DATA] = array();
$response[DATA]["officers"] = array();
$response[DATA]["battles"] = array();

foreach ($cursor as $row) {

    $avatar = array();
    // $avatar["avatar_url"] = PATH_IMAGE . $row["url"];
    $avatar["avatar_url"] = $row["url"];


    if ($row["type"] == 1) {
        $avatar["nameRu"] = $row["nameRu"];
        $avatar["nameEn"] = $row["nameEn"];

        array_push($response[DATA]["officers"], $avatar);
    } else {

        array_push($response[DATA]["battles"], $avatar);
    }
}

$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;

$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));
