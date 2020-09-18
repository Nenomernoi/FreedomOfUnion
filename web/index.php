<link rel="shortcut icon" href="ic_launcher.ico"/>
<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require('api/v1/tables/html_table.class.php');

$int196 = 196;

$host = 'bjfgndqk66vjawc-mongodb.services.clever-cloud.com';
$db_name = 'bjfgndqk66vjawc';
$username = 'u6mrbfvmew2h0q0kgpau';
$password = 'GLp8YC7R2X9L8cLXzzHh';
$port = '27017';
$connection_url = "mongodb://u6mrbfvmew2h0q0kgpau:GLp8YC7R2X9L8cLXzzHh@bjfgndqk66vjawc-mongodb.services.clever-cloud.com:27017/bjfgndqk66vjawc";

$m = new MongoClient($connection_url);
$link = $m->selectDB($db_name);





$response = array();
$collection_name = "user";
$collectionUser = $link->selectCollection($collection_name);
$cursor = $collectionUser->find()->limit(100);
$cursor->sort(array('total' => -1));

$response["human_table"] = array();

$counter = 0;

$tbl = new HTML_Table('', 'demoTbl', array('border' => 1, 'cellpadding' => 4, 'cellspacing' => 0, 'bgcolor' => 'gray'));
$tbl->addCaption('Freedom or Union', 'cap', array('id' => 'tblCap'));

$tbl->addRow();
$tbl->addCell('<font color="#33BBEE">' . 'Position' . '</font>');
$tbl->addCell('<font color="#33BBEE">' . 'Name' . '</font>');
$tbl->addCell('<font color="#33BBEE">' . 'Game only' . '</font>');
$tbl->addCell('<font color="#33BBEE">' . 'Game win' . '</font>');
$tbl->addCell('<font color="#33BBEE">' . 'Game Dead heat' . '</font>');
$tbl->addCell('<font color="#33BBEE">' . 'Game lose' . '</font>');
$tbl->addCell('<font color="#33BBEE">' . 'Total' . '</font>');
$tbl->addCell('<font color="#33BBEE">' . 'Id' . '</font>');
$tbl->addCell('<font color="#33BBEE">' . 'Uuid' . '</font>');



foreach ($cursor as $row) {

    $counter++;

    $tbl->addRow();
    $tbl->addCell('<b>' . $counter . '</b>');
    $tbl->addCell('<font color="#33BBEE">' . '<b>' . $row["name"] . '</b>' . '</font>');
    $tbl->addCell('<b>' . $row["game_only"] . '</b>');
    $tbl->addCell('<b>' . $row["game_win"] . '</b>');
    $tbl->addCell('<b>' . $row["game_lose_win"] . '</b>');
    $tbl->addCell('<b>' . $row["game_lose"] . '</b>');
    $tbl->addCell('<b>' . $row["total"] . '</b>');
   // $tbl->addCell('<b>' . $row["_id"]->{'$id'} . '</b>');
   // $tbl->addCell('<b>' . $row["user_uid"] . '</b>');
}

$m->close();
die($tbl->display());
