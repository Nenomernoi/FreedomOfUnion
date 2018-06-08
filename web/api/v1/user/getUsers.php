<?php

include '../connection.php';
$response = array();
$response[DATA] = array();


if (!isset($_GET["page"]) || !isset($_GET["uuid"])) {
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND_PARAMS;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_PARAMS_EN : CODE_ERROR_NOT_FOUND_PARAMS_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$user_uid = $_GET["uuid"];
$page = $_GET["page"];

$itemMax = MAX_ITEM * ($page + 1);

if ($itemMax > MAX_ITEM_LIST) {
    $response[SUCCESS] = CODE_COMPLITE;
    $response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}


$m = new MongoClient($connection_url);
$link = $m->selectDB($db_name);


$collectionUser = $link->selectCollection(TABLE_USER);


$query = array('user_uid' => $user_uid);
$setQuery = array(
    '$set' => array(
        "date_time" => $dateToday->getTimestamp()
    )
);
$collectionUser->update($query, $setQuery);



$cursor = $collectionUser->find();
$cursor->sort(array('total' => -1))->limit($itemMax);

$counter = -1;

foreach ($cursor as $row) {
    $counter++;

    if ($counter >= (MAX_ITEM * $page) && $counter < $itemMax) {
        $human = array();
        $human["id"] = $row["_id"]->{'$id'};
        $human["name"] = $row["name"];
        $human["avatar_url"] = $row["avatar_url"] != null ? $row["avatar_url"] : "";
        $human["fraction"] = $row["fraction"];

        $timeOld = $row["date_time"];
        $human["isOnline"] = $dateToday->getTimestamp() - $timeOld < 60 * 60;

        $human["game_only"] = $row["game_only"];
        $human["game_win"] = $row["game_win"];
        $human["game_lose"] = $row["game_lose"];
        $human["game_lose_win"] = $row["game_lose_win"];
        $human["total"] = $row["total"];

        $human["position"] = $counter + 1;

        array_push($response[DATA], $human);
    }
}




$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));
