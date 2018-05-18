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

if (!isset($post->name) || !isset($post->avatar_url) || !isset($post->fraction)) {
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND_PARAMS;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_PARAMS_EN : CODE_ERROR_NOT_FOUND_PARAMS_RU;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$name = $post->name;
$avatar_url = $post->avatar_url;
$fraction = $post->fraction;

$m = new MongoClient($connection_url);
$link = $m->selectDB($db_name);


$collectionUser = $link->selectCollection(TABLE_USER);
$query = array('name' => $name);
$cursor = $collectionUser->find($query);

foreach ($cursor as $row) {
    $user_uuid = $row["user_uid"];
    if ($user_uid != $user_uuid) {
        $response[SUCCESS] = CODE_ERROR_BUSY_REGISTRATION;
        $response[MESSAGE] = $lang ? CODE_ERROR_BUSY_NAME_EN : CODE_ERROR_BUSY_NAME_RU;
        $m->close();
        die(json_encode($response, JSON_UNESCAPED_SLASHES));
    }
}


$query = array('user_uid' => $user_uid);
$setQuery = array(
    '$set' => array(
        "name" => $name,
        "fraction" => $fraction,
        "avatar_url" => $avatar_url,
        "date_time" => $dateToday->getTimestamp()
    )
);
$collectionUser->update($query, $setQuery);

$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));
