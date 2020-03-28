<?php
include '../connection.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	$response[DATA][ACHIVIMENTS_PATH] = NULL;
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

$tenserTurns["id_parent"] = $post->uuid;
$tenserTurns["card"] = $post->card;
$tenserTurns["atlas"] = $post->cards;
$tenserTurns["mode"] = $post->escape;
$tenserTurns["params"] = $post->params;


$collectionTensor = $link->selectCollection(TABLE_TURNS);
$collectionTensor->insert($tenserTurns);


$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));