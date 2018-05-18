<?php

include '../connection.php';

$response = array();

$m = new MongoClient($connection_url);
$link = $m->selectDB($db_name);

$collectionUniform = $link->selectCollection(TABLE_UNIFORM);
$cursor = $collectionUniform->find();
$response[DATA] = array();

foreach ($cursor as $row) {
    $uniform = array();
    $uniform["_id"] = $row["_id"];
    $uniform["image"] = $row["image"];
    $uniform["name_eng"] = $row["name_eng"];
    $uniform["name_rus"] = $row["name_rus"];
    $uniform["type"] = $row["type"];
    $uniform["zavod"] = $row["zavod"];
    $uniform["units"] = $row["units"];
    $uniform["money"] = $row["money"];
    $uniform["tower"] = $row["tower"];
    $uniform["row"] = $row["row"];
    $uniform["row_enemy"] = $row["row_enemy"];
    $uniform["cost"] = $row["cost"];
    $uniform["fraction_id"] = $row["fraction_id"];
    $uniform["fraction"] = $uniform["type"] != GUN && $uniform["type"] != RIFFLE ? $row["fraction"] : 0;

    array_push($response[DATA], $uniform);
}
$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;

$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));
