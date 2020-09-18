<?php

include 'class/constants.php';


if (!isset($_SERVER['HTTP_APPKEY']) || !isset($_SERVER["HTTP_LANG"])) {
    $response[SUCCESS] = CODE_ERROR_SECURITY;
    $response[MESSAGE] = CODE_ERROR_SECURITY_EN;
    die(json_encode($response));
}
$keyApp = $_SERVER['HTTP_APPKEY'];
$lang = $_SERVER["HTTP_LANG"] != RU;


if ($keyApp != KEY_APP_DEBUG && $keyApp != KEY_APP_RELEASE && $keyApp != KEY_APP_RELEASE_NO_ADS_SECOND 
        && $keyApp != KEY_APP_DEBUG_WORK  && $keyApp != KEY_APP_RELEASE_NO_ADS) {
    $response[SUCCESS] = CODE_ERROR_SECURITY;
    $response[MESSAGE] = CODE_ERROR_SECURITY_EN;
    die(json_encode($response));
}

$int196 = 196;

$dateToday = new DateTime();

$host = 'bjfgndqk66vjawc-mongodb.services.clever-cloud.com';
$db_name = 'bjfgndqk66vjawc';
$username = 'u6mrbfvmew2h0q0kgpau';
$password = 'GLp8YC7R2X9L8cLXzzHh';
$port = '27017';
$connection_url = "mongodb://u6mrbfvmew2h0q0kgpau:GLp8YC7R2X9L8cLXzzHh@bjfgndqk66vjawc-mongodb.services.clever-cloud.com:27017/bjfgndqk66vjawc";

