<link rel="shortcut icon" href="../../ic_launcher.ico"/>
<title>Freedom or union</title>
<?php
include '../class/constants.php';
include '../class/card.php';
include '../class/user.php';
include '../class/userParam.php';
require('../tables/html_table.class.php');

$int196 = 196;
$host = 'ds031832.mlab.com';
$db_name = 'heroku_f0kmhqcc';
$username = 'heroku_f0kmhqcc';
$password = '37l24grmhrodif6438ne4p3jik';
$port = '31832';
$connection_url = "mongodb://heroku_f0kmhqcc:37l24grmhrodif6438ne4p3jik@ds031832.mlab.com:31832/heroku_f0kmhqcc";


$m = new MongoClient($connection_url);
$link = $m->selectDB($db_name);

////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////

$collectionGame = $link->selectCollection(TABLE_GAMES);
$cursor = $collectionGame->find();

if ($cursor->count() <= 0) {
    $response[MESSAGE] = CODE_COMPLITE_EN;
    $m->close();
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$turns = array();
$atlas = array();

$parent = new User_param();
$child = new User_param();

foreach ($cursor as $row) {

    $turns = $row["turns"];
    $atlas = $row["atlas"];

    $parent->id = $row["id_parent"]->{'$id'};

    $parent->bank = $row['params']['bank'];
    $parent->money = $row['params']['money'];
    $parent->industry = $row['params']['industry'];
    $parent->techo = $row['params']['techo'];
    $parent->state = $row['params']['state'];
    $parent->units = $row['params']['units'];
    $parent->state = $row['params']['state'];
    $parent->bonus_money_1 = $row['params']['bonus_money_1'];
    $parent->bonus_money_2 = $row['params']['bonus_money_2'];
    $parent->bonus_money_3 = $row['params']['bonus_money_3'];
    $parent->bonus_techo_1 = $row['params']['bonus_techo_1'];
    $parent->bonus_techo_2 = $row['params']['bonus_techo_2'];
    $parent->bonus_techo_3 = $row['params']['bonus_techo_3'];
    $parent->bonus_units_1 = $row['params']['bonus_units_1'];
    $parent->bonus_units_2 = $row['params']['bonus_units_2'];
    $parent->bonus_units_3 = $row['params']['bonus_units_3'];
    $parent->tower = $row['params']['tower'];
    $parent->row = $row['params']['row'];

    $parent->money_bon = $row['params']['money_bon'];
    $parent->techo_bon = $row['params']['techo_bon'];
    $parent->units_bon = $row['params']['units_bon'];
    $parent->tower_bon = $row['params']['tower_bon'];
    $parent->row_bon = $row['params']['row_bon'];
    $parent->atack_bon = $row['params']['atack_bon'];
//////////////////////////////////////////////////////////
    $child->id = $row["id_child"]->{'$id'};

    $child->bank = $row['params']['bank_enemy'];
    $child->money = $row['params']['money_enemy'];
    $child->industry = $row['params']['industry_enemy'];
    $child->techo = $row['params']['techo_enemy'];
    $child->state = $row['params']['state_enemy'];
    $child->units = $row['params']['units_enemy'];
    $child->state = $row['params']['state_enemy'];
    $child->bonus_money_1 = $row['params']['bonus_money_enemy_1'];
    $child->bonus_money_2 = $row['params']['bonus_money_enemy_2'];
    $child->bonus_money_3 = $row['params']['bonus_money_enemy_3'];
    $child->bonus_techo_1 = $row['params']['bonus_techo_enemy_1'];
    $child->bonus_techo_2 = $row['params']['bonus_techo_enemy_2'];
    $child->bonus_techo_3 = $row['params']['bonus_techo_enemy_3'];
    $child->bonus_units_1 = $row['params']['bonus_units_enemy_1'];
    $child->bonus_units_2 = $row['params']['bonus_units_enemy_2'];
    $child->bonus_units_3 = $row['params']['bonus_units_enemy_3'];
    $child->tower = $row['params']['tower_enemy'];
    $child->row = $row['params']['row_enemy'];

    $child->money_bon = $row['params']['money_bon_enemy'];
    $child->techo_bon = $row['params']['techo_bon_enemy'];
    $child->units_bon = $row['params']['units_bon_enemy'];
    $child->tower_bon = $row['params']['tower_bon_enemy'];
    $child->row_bon = $row['params']['row_bon_enemy'];
    $child->atack_bon = $row['params']['atack_bon_enemy'];

    break;
}

$card_act_last = -1;
$escape = -1;
$escape_back = -1;
$card_back_last = -1;


$result = array_reverse($turns);


if (!empty($result[1])) {
    if ($result[1]["escape"] != 2) {
        $escape_back = $result[1]["escape"];
        $card_back_last = $result[1]["card"];
    }
}
if (!empty($result[0])) {
    if ($result[0]["escape"] != 2) {
        $escape = $result[0]["escape"];
        $card_act_last = $result[0]["card"];
    }
}


///////////////////////// INIT CARD////////////////////////////
if (!empty($result)) {
    $cardLast = new Card();
    $cardBack = new Card();

    $collection_name = "cards";
    $collectionCards = $link->selectCollection($collection_name);

    $query = array('_id' => new MongoInt32($card_act_last));
    $cursor = $collectionCards->find($query);

    foreach ($cursor as $row) {
        $cardLast->initCard($row);
    }
    $query = array('_id' => new MongoInt32($card_back_last));
    $cursor = $collectionCards->find($query);

    foreach ($cursor as $row) {
        $cardBack->initCard($row);
    }
}


/////CREATE TABLE

$tbl = new HTML_Table('', 'demoTbl', array('border' => 1, 'cellpadding' => 4, 'cellspacing' => 0, 'bgcolor' => 'gray'));
$tbl->addCaption('Game parametr', 'cap', array('id' => 'tblCap'));

$tbl->addRow();
$tbl->addCell('<font color="#80FF00">PLAYER</font>   ' . $parent->id, 'foot', 'data', array('colspan' => 10));
$tbl->addCell('<font color="brown">ENEMY [BOT]</font>  ' . $child->id, 'foot', 'data', array('colspan' => 10));

$tbl->addRow();
$tbl->addCell("");
$tbl->addCell("");

$card = new Card();

$collection_name = "cards";
$collectionCards = $link->selectCollection($collection_name);

$query = array('_id' => new MongoInt32($atlas[5]));
$cursor = $collectionCards->find($query);

foreach ($cursor as $row) {
    $card->initCard($row);
}

$tbl->addCell('<font color="#80FF00">' . ($card->name . ' ' . $atlas[5]) . '</font>');
$query = array('_id' => new MongoInt32($atlas[6]));
$cursor = $collectionCards->find($query);

foreach ($cursor as $row) {
    $card->initCard($row);
}
$tbl->addCell('<font color="#80FF00">' . ($card->name . ' ' . $atlas[6]) . '</font>');

$query = array('_id' => new MongoInt32($atlas[7]));
$cursor = $collectionCards->find($query);

foreach ($cursor as $row) {
    $card->initCard($row);
}$tbl->addCell('<font color="#80FF00">' . ($card->name . ' ' . $atlas[7]) . '</font>');
$query = array('_id' => new MongoInt32($atlas[8]));
$cursor = $collectionCards->find($query);

foreach ($cursor as $row) {
    $card->initCard($row);
}
$tbl->addCell('<font color="#80FF00">' . ($card->name . ' ' . $atlas[8]) . '</font>');
$query = array('_id' => new MongoInt32($atlas[9]));
$cursor = $collectionCards->find($query);

foreach ($cursor as $row) {
    $card->initCard($row);
}
$tbl->addCell('<font color="#80FF00">' . ($card->name . ' ' . $atlas[9]) . '</font>');
$tbl->addCell("");
$tbl->addCell("");

$tbl->addCell("");
$tbl->addCell("");

$tbl->addCell("");
$tbl->addCell("");
$query = array('_id' => new MongoInt32($atlas[0]));
$cursor = $collectionCards->find($query);

foreach ($cursor as $row) {
    $card->initCard($row);
}
$tbl->addCell('<font color="#99FF99">' . ($card->name . ' ' . $atlas[0]) . '</font>');
$query = array('_id' => new MongoInt32($atlas[1]));
$cursor = $collectionCards->find($query);

foreach ($cursor as $row) {
    $card->initCard($row);
}
$tbl->addCell('<font color="#99FF99">' . ($card->name . ' ' . $atlas[1]) . '</font>');
$query = array('_id' => new MongoInt32($atlas[2]));
$cursor = $collectionCards->find($query);

foreach ($cursor as $row) {
    $card->initCard($row);
}
$tbl->addCell('<font color="#99FF99">' . ($card->name . ' ' . $atlas[2]) . '</font>');
$query = array('_id' => new MongoInt32($atlas[3]));
$cursor = $collectionCards->find($query);

foreach ($cursor as $row) {
    $card->initCard($row);
}
$tbl->addCell('<font color="#99FF99">' . ($card->name . ' ' . $atlas[3]) . '</font>');
$query = array('_id' => new MongoInt32($atlas[4]));
$cursor = $collectionCards->find($query);

foreach ($cursor as $row) {
    $card->initCard($row);
}
$tbl->addCell('<font color="#99FF99">' . ($card->name . ' ' . $atlas[4]) . '</font>');
$tbl->addCell("");
$tbl->addCell("");

$tbl->addRow();
$tbl->addCell('<font color="#26FF26">' . ($parent->bonus_money_1 != 196 ? $parent->bonus_money_1 : "None") . '</font>');
$tbl->addCell('<font color="#26FF26">' . ($parent->money_bon > 0 ? "+" . $parent->money_bon : "" . $parent->money_bon) . '</font>');
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell('<font color="#26FF26">' . ($child->money_bon > 0 ? "+" . $child->money_bon : "" . $child->money_bon) . '</font>');
$tbl->addCell('<font color="#26FF26">' . ($child->bonus_money_1 != 196 ? $child->bonus_money_1 : "None") . '</font>');

$tbl->addCell("");
$tbl->addCell("");

$tbl->addCell('<font color="#26FF26">' . ($child->bonus_money_1 != 196 ? $child->bonus_money_1 : "None") . '</font>');
$tbl->addCell('<font color="#26FF26">' . ($child->money_bon > 0 ? "+" . $child->money_bon : "" . $child->money_bon) . '</font>');
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell('<font color="#26FF26">' . ($parent->money_bon > 0 ? "+" . $parent->money_bon : "" . $parent->money_bon) . '</font>');
$tbl->addCell('<font color="#26FF26">' . ($parent->bonus_money_1 != 196 ? $parent->bonus_money_1 : "None") . '</font>');


$tbl->addRow();
$tbl->addCell('<font color="#26FF26">' . ($parent->bonus_money_2 != 196 ? $parent->bonus_money_2 : "None") . '</font>');
$tbl->addCell('<font color="#26FF26">' . ($parent->bank) . '</font>');
$tbl->addCell('<font color="#FFBF00">' . ($parent->tower_bon > 0 ? "+" . $parent->tower_bon : "" . $parent->tower_bon) . '</font>');
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell('<font color="#FFBF00">' . ($child->tower_bon > 0 ? "+" . $child->tower_bon : "" . $child->tower_bon) . '</font>');
$tbl->addCell('<font color="#26FF26">' . ($child->bank) . '</font>');
$tbl->addCell('<font color="#26FF26">' . ($child->bonus_money_2 != 196 ? $child->bonus_money_2 : "None") . '</font>');

$tbl->addCell("");
$tbl->addCell("");

$tbl->addCell('<font color="#26FF26">' . ($child->bonus_money_2 != 196 ? $child->bonus_money_2 : "None") . '</font>');
$tbl->addCell('<font color="#26FF26">' . ($child->bank) . '</font>');
$tbl->addCell('<font color="#FFBF00">' . ($child->tower_bon > 0 ? "+" . $child->tower_bon : "" . $child->tower_bon) . '</font>');
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell('<font color="#FFBF00">' . ($parent->tower_bon > 0 ? "+" . $parent->tower_bon : "" . $parent->tower_bon) . '</font>');
$tbl->addCell('<font color="#26FF26">' . ($parent->bank) . '</font>');
$tbl->addCell('<font color="#26FF26">' . ($parent->bonus_money_2 != 196 ? $parent->bonus_money_2 : "None") . '</font>');


$tbl->addRow();
$tbl->addCell('<font color="#26FF26">' . ($parent->bonus_money_3 != 196 ? $parent->bonus_money_3 : "None") . '</font>');
$tbl->addCell('<font color="#26FF26">' . ($parent->money) . '</font>');
$tbl->addCell('<font color="#FFBF00">' . ($parent->tower) . '</font>');
$tbl->addCell("");
$tbl->addCell($escape_back);
$tbl->addCell("");
$tbl->addCell('<font color="#FFBF00">' . ($child->tower) . '</font>');
$tbl->addCell('<font color="#26FF26">' . ($child->money) . '</font>');
$tbl->addCell('<font color="#26FF26">' . ($child->bonus_money_3 != 196 ? $child->bonus_money_3 : "None") . '</font>');

$tbl->addCell("");
$tbl->addCell("");

$tbl->addCell('<font color="#26FF26">' . ($child->bonus_money_3 != 196 ? $child->bonus_money_3 : "None") . '</font>');
$tbl->addCell('<font color="#26FF26">' . ($child->money) . '</font>');
$tbl->addCell('<font color="#FFBF00">' . ($child->tower) . '</font>');
$tbl->addCell("");
$tbl->addCell($escape_back);
$tbl->addCell("");
$tbl->addCell('<font color="#FFBF00">' . ($parent->tower) . '</font>');
$tbl->addCell('<font color="#26FF26">' . ($parent->money) . '</font>');
$tbl->addCell('<font color="#26FF26">' . ($parent->bonus_money_3 != 196 ? $parent->bonus_money_3 : "None") . '</font>');



$tbl->addRow();
$tbl->addCell('<font color="brown">' . ($parent->bonus_techo_1 != 196 ? $parent->bonus_techo_1 : "None") . '</font>');
$tbl->addCell('<font color="brown">' . ($parent->techo_bon > 0 ? "+" . $parent->techo_bon : "" . $parent->techo_bon) . '</font>');
$tbl->addCell("");
$tbl->addCell('<font color="red">' . ($parent->atack_bon) . '</font>');
$tbl->addCell($card_back_last != -1 ?$cardBack->id :"". nl2br("\t") . $card_back_last != -1 ? $cardBack->content : "");
$tbl->addCell('<font color="red">' . ($child->atack_bon) . '</font>');
$tbl->addCell("");
$tbl->addCell('<font color="brown">' . ($child->techo_bon > 0 ? "+" . $child->techo_bon : "" . $child->techo_bon) . '</font>');
$tbl->addCell('<font color="brown">' . ($child->bonus_techo_1 != 196 ? $child->bonus_techo_1 : "None") . '</font>');

$tbl->addCell("");
$tbl->addCell("");

$tbl->addCell('<font color="brown">' . ($child->bonus_techo_1 != 196 ? $child->bonus_techo_1 : "None") . '</font>');
$tbl->addCell('<font color="brown">' . ($child->techo_bon > 0 ? "+" . $child->techo_bon : "" . $child->techo_bon) . '</font>');
$tbl->addCell("");
$tbl->addCell('<font color="red">' . ($child->atack_bon) . '</font>');
$tbl->addCell($card_back_last != -1 ?$cardBack->id :"". nl2br("\t") . $card_back_last != -1 ? $cardBack->content : "");
$tbl->addCell('<font color="red">' . ($parent->atack_bon) . '</font>');
$tbl->addCell("");
$tbl->addCell('<font color="brown">' . ($parent->techo_bon > 0 ? "+" . $parent->techo_bon : "" . $parent->techo_bon) . '</font>');
$tbl->addCell('<font color="brown">' . ($parent->bonus_techo_1 != 196 ? $parent->bonus_techo_1 : "None") . '</font>');



$tbl->addRow();
$tbl->addCell('<font color="brown">' . ($parent->bonus_techo_2 != 196 ? $parent->bonus_techo_2 : "None") . '</font>');
$tbl->addCell('<font color="brown">' . $parent->industry . '</font>');
$tbl->addCell('<font color="#33BBEE">' . ($parent->row) . '</font>');
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell('<font color="#33BBEE">' . ($child->row) . '</font>');
$tbl->addCell('<font color="brown">' . $child->industry . '</font>');
$tbl->addCell('<font color="brown">' . ($child->bonus_techo_2 != 196 ? $child->bonus_techo_2 : "None") . '</font>');

$tbl->addCell("");
$tbl->addCell("");

$tbl->addCell('<font color="brown">' . ($child->bonus_techo_2 != 196 ? $child->bonus_techo_2 : "None") . '</font>');
$tbl->addCell('<font color="brown">' . $child->industry . '</font>');
$tbl->addCell('<font color="#33BBEE">' . ($child->row) . '</font>');
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell('<font color="#33BBEE">' . ($parent->row) . '</font>');
$tbl->addCell('<font color="brown">' . $parent->industry . '</font>');
$tbl->addCell('<font color="brown">' . ($parent->bonus_techo_2 != 196 ? $parent->bonus_techo_2 : "None") . '</font>');




$tbl->addRow();
$tbl->addCell('<font color="brown">' . ($parent->bonus_techo_3 != 196 ? $parent->bonus_techo_3 : "None") . '</font>');
$tbl->addCell('<font color="brown">' . $parent->techo . '</font>');
$tbl->addCell('<font color="#33BBEE">' . ($parent->row_bon > 0 ? "+" . $parent->row_bon : "" . $parent->row_bon) . '</font>');
$tbl->addCell("");
$tbl->addCell($escape);
$tbl->addCell("");
$tbl->addCell('<font color="#33BBEE">' . ($child->row_bon > 0 ? "+" . $child->row_bon : "" . $child->row_bon) . '</font>');
$tbl->addCell('<font color="brown">' . $child->techo . '</font>');
$tbl->addCell('<font color="brown">' . ($child->bonus_techo_3 != 196 ? $child->bonus_techo_3 : "None") . '</font>');

$tbl->addCell("");
$tbl->addCell("");

$tbl->addCell('<font color="brown">' . ($child->bonus_techo_3 != 196 ? $child->bonus_techo_3 : "None") . '</font>');
$tbl->addCell('<font color="brown">' . $child->techo . '</font>');
$tbl->addCell('<font color="#33BBEE">' . ($child->row_bon > 0 ? "+" . $child->row_bon : "" . $child->row_bon) . '</font>');
$tbl->addCell("");
$tbl->addCell($escape);
$tbl->addCell("");
$tbl->addCell('<font color="#33BBEE">' . ($parent->row_bon > 0 ? "+" . $parent->row_bon : "" . $parent->row_bon) . '</font>');
$tbl->addCell('<font color="brown">' . $parent->techo . '</font>');
$tbl->addCell('<font color="brown">' . ($parent->bonus_techo_3 != 196 ? $parent->bonus_techo_3 : "None") . '</font>');



$tbl->addRow();
$tbl->addCell('<font color="blue">' . ($parent->bonus_units_1 != 196 ? $parent->bonus_units_1 : "None") . '</font>');
$tbl->addCell('<font color="blue">' . ($parent->units_bon > 0 ? "+" . $parent->units_bon : "" . $parent->units_bon) . '</font>');
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell($card_act_last != -1 ?$cardLast->id :"" . nl2br("\t") . $card_act_last != -1 ? $cardLast->content : "");
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell('<font color="blue">' . ($child->units_bon > 0 ? "+" . $child->units_bon : "" . $child->units_bon) . '</font>');
$tbl->addCell('<font color="blue">' . ($child->bonus_units_1 != 196 ? $child->bonus_units_1 : "None") . '</font>');

$tbl->addCell("");
$tbl->addCell("");

$tbl->addCell('<font color="blue">' . ($child->bonus_units_1 != 196 ? $child->bonus_units_1 : "None") . '</font>');
$tbl->addCell('<font color="blue">' . ($child->units_bon > 0 ? "+" . $child->units_bon : "" . $child->units_bon) . '</font>');
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell($card_act_last != -1 ?$cardLast->id :"" . nl2br("\t") . $card_act_last != -1 ? $cardLast->content : "");
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell('<font color="blue">' . ($parent->units_bon > 0 ? "+" . $parent->units_bon : "" . $parent->units_bon) . '</font>');
$tbl->addCell('<font color="blue">' . ($parent->bonus_units_1 != 196 ? $parent->bonus_units_1 : "None") . '</font>');


$tbl->addRow();
$tbl->addCell('<font color="blue">' . ($parent->bonus_units_2 != 196 ? $parent->bonus_units_2 : "None") . '</font>');
$tbl->addCell('<font color="blue">' . ($parent->state) . '</font>');
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell('<font color="blue">' . ($child->state) . '</font>');
$tbl->addCell('<font color="blue">' . ($child->bonus_units_2 != 196 ? $child->bonus_units_2 : "None") . '</font>');

$tbl->addCell("");
$tbl->addCell("");

$tbl->addCell('<font color="blue">' . ($child->bonus_units_2 != 196 ? $child->bonus_units_2 : "None") . '</font>');
$tbl->addCell('<font color="blue">' . ($child->state) . '</font>');
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell('<font color="blue">' . ($parent->state) . '</font>');
$tbl->addCell('<font color="blue">' . ($parent->bonus_units_2 != 196 ? $parent->bonus_units_2 : "None") . '</font>');

$tbl->addRow();
$tbl->addCell('<font color="blue">' . ($parent->bonus_units_3 != 196 ? $parent->bonus_units_3 : "None") . '</font>');
$tbl->addCell('<font color="blue">' . ($parent->units) . '</font>');
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell('<font color="blue">' . ($child->units) . '</font>');
$tbl->addCell('<font color="blue">' . ($child->bonus_units_3 != 196 ? $child->bonus_units_3 : "None") . '</font>');

$tbl->addCell("");
$tbl->addCell("");

$tbl->addCell('<font color="blue">' . ($child->bonus_units_3 != 196 ? $child->bonus_units_3 : "None") . '</font>');
$tbl->addCell('<font color="blue">' . ($child->units) . '</font>');
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell("");
$tbl->addCell('<font color="blue">' . ($parent->units) . '</font>');
$tbl->addCell('<font color="blue">' . ($parent->bonus_units_3 != 196 ? $parent->bonus_units_3 : "None") . '</font>');

$tbl->addRow();

$tbl->addCell("");
$tbl->addCell("");
$query = array('_id' => new MongoInt32($atlas[0]));
$cursor = $collectionCards->find($query);

foreach ($cursor as $row) {
    $card->initCard($row);
}
$tbl->addCell('<font color="#99FF99">' . ($card->name . ' ' . $atlas[0]) . '</font>');
$query = array('_id' => new MongoInt32($atlas[1]));
$cursor = $collectionCards->find($query);

foreach ($cursor as $row) {
    $card->initCard($row);
}
$tbl->addCell('<font color="#99FF99">' . ($card->name . ' ' . $atlas[1]) . '</font>');
$query = array('_id' => new MongoInt32($atlas[2]));
$cursor = $collectionCards->find($query);

foreach ($cursor as $row) {
    $card->initCard($row);
}
$tbl->addCell('<font color="#99FF99">' . ($card->name . ' ' . $atlas[2]) . '</font>');
$query = array('_id' => new MongoInt32($atlas[3]));
$cursor = $collectionCards->find($query);

foreach ($cursor as $row) {
    $card->initCard($row);
}
$tbl->addCell('<font color="#99FF99">' . ($card->name . ' ' . $atlas[3]) . '</font>');
$query = array('_id' => new MongoInt32($atlas[4]));
$cursor = $collectionCards->find($query);

foreach ($cursor as $row) {
    $card->initCard($row);
}
$tbl->addCell('<font color="#99FF99">' . ($card->name . ' ' . $atlas[4]) . '</font>');
$tbl->addCell("");
$tbl->addCell("");

$tbl->addCell("");
$tbl->addCell("");


$tbl->addCell("");
$tbl->addCell("");
$query = array('_id' => new MongoInt32($atlas[5]));
$cursor = $collectionCards->find($query);

foreach ($cursor as $row) {
    $card->initCard($row);
}

$tbl->addCell('<font color="#80FF00">' . ($card->name . ' ' . $atlas[5]) . '</font>');
$query = array('_id' => new MongoInt32($atlas[6]));
$cursor = $collectionCards->find($query);

foreach ($cursor as $row) {
    $card->initCard($row);
}
$tbl->addCell('<font color="#80FF00">' . ($card->name . ' ' . $atlas[6]) . '</font>');

$query = array('_id' => new MongoInt32($atlas[7]));
$cursor = $collectionCards->find($query);

foreach ($cursor as $row) {
    $card->initCard($row);
}$tbl->addCell('<font color="#80FF00">' . ($card->name . ' ' . $atlas[7]) . '</font>');
$query = array('_id' => new MongoInt32($atlas[8]));
$cursor = $collectionCards->find($query);

foreach ($cursor as $row) {
    $card->initCard($row);
}
$tbl->addCell('<font color="#80FF00">' . ($card->name . ' ' . $atlas[8]) . '</font>');
$query = array('_id' => new MongoInt32($atlas[9]));
$cursor = $collectionCards->find($query);

foreach ($cursor as $row) {
    $card->initCard($row);
}
$tbl->addCell('<font color="#80FF00">' . ($card->name . ' ' . $atlas[9]) . '</font>');
$tbl->addCell("");
$tbl->addCell("");

$m->close();
die($tbl->display());
