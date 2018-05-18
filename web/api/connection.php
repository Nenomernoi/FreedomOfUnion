<?php

include 'class/constants.php';


if (!isset($_SERVER['HTTP_APPKEY']) || !isset($_SERVER["HTTP_LANG"])) {
    $response[SUCCESS] = CODE_ERROR_SECURITY;
    $response[MESSAGE] = CODE_ERROR_SECURITY_EN;
    die(json_encode($response));
}
$keyApp = $_SERVER['HTTP_APPKEY'];
$lang = $_SERVER["HTTP_LANG"] != RU;


if ($keyApp != KEY_APP_DEBUG && $keyApp != KEY_APP_RELEASE && $keyApp != KEY_APP_DEBUG_WORK) {
    $response[SUCCESS] = CODE_ERROR_SECURITY;
    $response[MESSAGE] = CODE_ERROR_SECURITY_EN;
    die(json_encode($response));
}

$int196 = 196;
$username;
$password;
$host;
$db_name;
$url;
$port;

$connection_url = getenv("MONGOLAB_URI");

$dateToday = new DateTime();

if ($_SERVER['SERVER_NAME'] == "freedoom-or-union-9804.herokuapp.com") {
    $url = parse_url($connection_url);
    $host = $url["host"];
    $username = $url["user"];
    $password = $url["pass"];
    $db_name = preg_replace('/\/(.*)/', '$1', $url['path']);
} else {
    $host = 'ds049288.mongolab.com';
    $db_name = 'heroku_4fxvwmm2';
    $username = 'heroku_4fxvwmm2';
    $password = 'rc59onuvmpm6toh7ti2ht3rfhb';
    $port = '49288';
    $connection_url = "mongodb://heroku_4fxvwmm2:rc59onuvmpm6toh7ti2ht3rfhb@ds049288.mongolab.com:49288/heroku_4fxvwmm2";
}

