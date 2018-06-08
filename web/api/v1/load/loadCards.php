<?php

include '../connection.php';

$response = array();

$m = new MongoClient($connection_url);
$link = $m->selectDB($db_name);

$collectioncardDeck = $link->selectCollection(TABLE_CARDS);
$cursor = $collectioncardDeck->find();
$response[DATA]["cards"] = array();

foreach ($cursor as $row) {
    $card = array();
    $card["_id"] = $row["_id"];
    $card["id_old"] = $row["id_old"];
    $card["image"] = isset($row["image"]) ? $row["image"] : "";
    $card["back_image"] = isset($row["back_image"]) ? $row["back_image"] : "";
    $card["name_eng"] = isset($row["name_eng"]) ? $row["name_eng"] : "";
    $card["name_rus"] = isset($row["name_rus"]) ? $row["name_rus"] : "";
    $card["main_eng"] = isset($row["main_eng"]) ? $row["main_eng"] : "";
    $card["main_rus"] = isset($row["main_rus"]) ? $row["main_rus"] : "";
    $card["atack"] = isset($row["atack"]) ? $row["atack"] : 0;
    $card["again"] = isset($row["again"]) ? $row["again"] : 0;
    $card["esc"] = isset($row["esc"]) ? $row["esc"] : 0;
    $card["row_tow"] = isset($row["row_tow"]) ? $row["row_tow"] : 0;
    $card["cost_units"] = isset($row["cost_units"]) ? $row["cost_units"] : 0;
    $card["cost_zavod"] = isset($row["cost_zavod"]) ? $row["cost_zavod"] : 0;
    $card["cost_money"] = isset($row["cost_money"]) ? $row["cost_money"] : 0;
    $card["type"] = isset($row["type"]) ? $row["type"] : 0;
    $card["zavod"] = isset($row["zavod"]) ? $row["zavod"] : 0;
    $card["units"] = isset($row["units"]) ? $row["units"] : 0;
    $card["money"] = isset($row["money"]) ? $row["money"] : 0;
    $card["zavod_in"] = isset($row["zavod_in"]) ? $row["zavod_in"] : 0;
    $card["units_in"] = isset($row["units_in"]) ? $row["units_in"] : 0;
    $card["money_in"] = isset($row["money_in"]) ? $row["money_in"] : 0;
    $card["zavod_en"] = isset($row["zavod_en"]) ? $row["zavod_en"] : 0;
    $card["units_en"] = isset($row["units_en"]) ? $row["units_en"] : 0;
    $card["moey_en"] = isset($row["moey_en"]) ? $row["moey_en"] : 0;
    $card["zavod_en_in"] = isset($row["zavod_en_in"]) ? $row["zavod_en_in"] : 0;
    $card["units_en_in"] = isset($row["units_en_in"]) ? $row["units_en_in"] : 0;
    $card["money_en_in"] = isset($row["money_en_in"]) ? $row["money_en_in"] : 0;
    $card["at_def_pl_tow"] = isset($row["at_def_pl_tow"]) ? $row["at_def_pl_tow"] : 0;
    $card["at_def_pl_rov"] = isset($row["at_def_pl_rov"]) ? $row["at_def_pl_rov"] : 0;
    $card["at_def_en_tow"] = isset($row["at_def_en_tow"]) ? $row["at_def_en_tow"] : 0;
    $card["at_def_en_rov"] = isset($row["at_def_en_rov"]) ? $row["at_def_en_rov"] : 0;

    array_push($response[DATA]["cards"], $card);
}

$collectionIiCardDeck = $link->selectCollection(TABLE_II_CARDS);
$cursor = $collectionIiCardDeck->find();
$response[DATA]["ii_cards"] = array();

foreach ($cursor as $row) {
    $ii_cards = array();
    $ii_cards["_id"] = $row["_id"];
    $ii_cards["id_old"] = $row["id_old"];
    array_push($response[DATA]["ii_cards"], $ii_cards);
}

$response[DATA]["max_card_atack"] = MAX_CARD_ATACK;
$response[DATA]["max_card_defence"] = MAX_CARD_DEFENCE;
$response[DATA]["max_card"] = MAX_CARD;

$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;

$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));
