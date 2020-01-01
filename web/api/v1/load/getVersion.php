<?php

include '../class/constants.php';

if (!isset($_SERVER['HTTP_APPKEY']) || !isset($_SERVER["HTTP_LANG"])) {
    $response[SUCCESS] = CODE_ERROR_SECURITY;
    $response[MESSAGE] = CODE_ERROR_SECURITY_EN;
    die(json_encode($response));
}
$keyApp = $_SERVER['HTTP_APPKEY'];
$lang = $_SERVER["HTTP_LANG"] != RU;


if ($keyApp != KEY_APP_DEBUG && $keyApp != KEY_APP_RELEASE && $keyApp != KEY_APP_RELEASE_NO_ADS_SECOND
        && $keyApp != KEY_APP_DEBUG_WORK   && $keyApp != KEY_APP_RELEASE_NO_ADS) {
    $response[SUCCESS] = CODE_ERROR_SECURITY;
    $response[MESSAGE] = CODE_ERROR_SECURITY_EN;
    die(json_encode($response));
}
$response[DATA][VERSION] = array();

array_push($response[DATA][VERSION], array('serverId' => 1, 'version' => DATA_BASE_AVATAR_VERSION));
array_push($response[DATA][VERSION], array('serverId' => 2, 'version' => DATA_BASE_INFORMATION_VERSION));
array_push($response[DATA][VERSION], array('serverId' => 3, 'version' => DATA_BASE_UNIFORM_VERSION));
array_push($response[DATA][VERSION], array('serverId' => 4, 'version' => DATA_BASE_ACHIVIMENTS_VERSION));
array_push($response[DATA][VERSION], array('serverId' => 5, 'version' => DATA_BASE_CARDS_VERSION));
array_push($response[DATA][VERSION], array('serverId' => 6, 'version' => DATA_BASE_BANNER_VERSION));
array_push($response[DATA][VERSION], array('serverId' => 7, 'version' => DATA_BASE_HISTORY_VERSION));


$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;

die(json_encode($response));
