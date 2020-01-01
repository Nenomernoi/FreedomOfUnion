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

$host = 'ds031832.mlab.com';
$db_name = 'heroku_f0kmhqcc';
$username = 'heroku_f0kmhqcc';
$password = '37l24grmhrodif6438ne4p3jik';
$port = '31832';
$connection_url = "mongodb://heroku_f0kmhqcc:37l24grmhrodif6438ne4p3jik@ds031832.mlab.com:31832/heroku_f0kmhqcc";

