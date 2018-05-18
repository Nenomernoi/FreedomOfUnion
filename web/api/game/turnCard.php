<?php

include '../connection.php';

include '../class/card.php';
include '../class/user.php';
include '../class/turn.php';

include '../class/userParam.php';

include './updateArchiviment.php';

include '../push/firebase.php';
include '../push/push.php';

$response = array();
$upd = new updateArchiviment();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response[DATA][ACHIVIMENTS_PATH] = NULL;
    $response[SUCCESS] = CODE_ERROR_METHTOD;
    $response[MESSAGE] = $lang ? CODE_ERROR_METHOD_EN : CODE_ERROR_METHOD_RU;
    $response[LEVEL] = 16;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}
$post = json_decode(file_get_contents("php://input"));

if (!isset($post->id_game) || !isset($post->card) || !isset($post->escape) || !isset($post->uuid)) {
    $response[DATA][CARD] = -1;
    $response[DATA][ACHIVIMENTS_PATH] = NULL;
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND_PARAMS;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_PARAMS_EN : CODE_ERROR_NOT_FOUND_PARAMS_RU;
    $response[LEVEL] = 27;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$isBot = isset($post->player_bot) ? $post->player_bot : NO_BOT;

$turn = array();

////////////// INIT NEW CARD AND TIME////////////////////////

$cardNew = mt_rand($isBot != BOT ? MIN_CARD : MAX_CARD_DEFENCE, $isBot != BOT ? MAX_CARD : MAX_CARD_ATACK);

$m = new MongoClient($connection_url);
$link = $m->selectDB($db_name);

$collectionIiCards = $link->selectCollection(TABLE_II_CARDS);
$cursor = $collectionIiCards->find(array('_id' => new MongoInt32($cardNew)));
foreach ($cursor as $row) {
    $cardNew = $row["id_old"];
    break;
}


$date = new DateTime();
$time = $date->getTimestamp();

//////////////////////////////////////////////////////


$idGame = $post->id_game;
$card = (int) $post->card;
$escape = $post->escape;
$user_uid = $post->uuid;

$registrationIds = null;
$idEnemy = null;

////////////////////////////////////////////
$progress = GAME_PLAY;

$collectionUser = $link->selectCollection(TABLE_USER);
$query = array('user_uid' => $user_uid);
$cursor = $collectionUser->find($query);

if ($cursor->count() <= 0) {
    $response[DATA][CARD] = NO_BOT;
    $response[DATA][ACHIVIMENTS_PATH] = NULL;
    $response[SUCCESS] = CODE_ERROR_AUTH;
    $response[MESSAGE] = $lang ? CODE_ERROR_AUTH_OLD_EN : CODE_ERROR_AUTH_OLD_RU;
    $m->close;
    $response[LEVEL] = 56;
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

$idGamer;

foreach ($cursor as $row) {
    $idGamer = $row["_id"]->{'$id'};
    break;
}

/////////////////////////////// INIT GAMERS AND GAME///////////////////////////////////////////

$collectionGame = $link->selectCollection(TABLE_GAMES);
$cursor = $collectionGame->find(array('_id' => new MongoId($idGame)));

if ($cursor->count() <= 0) {

    $query = array('_id' => new MongoId($idGamer));
    $setQuery = array(
        '$set' => array(
            "id_game" => null
        )
    );
    $collectionUser->update($query, $setQuery);

    $response[DATA][ACHIVIMENTS_PATH] = $upd->getLastAch($link, $idGamer);


    $response[DATA][PROGRESS] = $isBot == NO_BOT ? RESIGN : ($isBot == BOT ? RESIGN_PARENT : RESIGN_CHILD);
    $response[DATA][CARD] = $cardNew;
    $response[SUCCESS] = CODE_COMPLITE;
    $response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
    $response[LEVEL] = 79;
    $m->close();
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

//////////////////////////////////////////

$isWhoBully = $idGamer;

$game = null;
$parent = new User_param();
$child = new User_param();

$turns = array();

foreach ($cursor as $row) {

    if ($row["progress"] != GAME_PLAY) {
        $query = array('_id' => new MongoId($idGamer));
        $setQuery = array(
            '$set' => array(
                "id_game" => null
            )
        );
        $collectionUser->update($query, $setQuery);

        $collectionGame->remove($query);

///////////////////////////////////////////////////////////////////////////

        $response[DATA][ACHIVIMENTS_PATH] = $upd->getLastAch($link, $idGamer);


        $response[DATA][PROGRESS] = $isBot == NO_BOT ? $row["progress"] : ($isBot == 1 ? RESIGN_PARENT : RESIGN_CHILD);
        $response[DATA][CARD] = $cardNew;
        $response[SUCCESS] = CODE_COMPLITE;
        $response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
        $response[LEVEL] = 124;
        $m->close();
        die(json_encode($response, JSON_UNESCAPED_SLASHES));
    }

    $turns = $row["turns"];

    $atlas = $row["atlas"];

    $isWhoBully = $row["whoIsBully"];

    $parent->id = $row["id_parent"]->{'$id'};

    $parent->bank = $row['params']['bank'];
    $parent->money = $row['params']['money'];
    $parent->industry = $row['params']['industry'];
    $parent->techo = $row['params']['techo'];
    $parent->state = $row['params']['state'];
    $parent->units = $row['params']['units'];
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
    $parent->fraction = $row['fraction_parent'];

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

    $child->fraction = $row['fraction_child'];

    $progress = $row["progress"];


    $correctResourse = array(
        "child" => array(
            "id" => $row["id_child"]->{'$id'},
            "money" => $row['params']['money_enemy'],
            "techo" => $row['params']['techo_enemy'],
            "units" => $row['params']['units_enemy']
        ),
        "parent" => array(
            "id" => $row["id_parent"]->{'$id'},
            "money" => $row['params']['money'],
            "techo" => $row['params']['techo'],
            "units" => $row['params']['units']
        )
    );
}

//////////////////////////////////////////////////////////////////////

$fractions = array();
$fractions["parent"] = $parent->fraction;
$fractions["parent_id"] = $parent->id;
$fractions["child"] = $child->fraction;
$fractions["child_id"] = $child->id;
$response[DATA]["fraction"] = $fractions;

//////////////////////////////INIT ATLAS  AND REPLACE CARD ON NEW/ ///////////////////////////
$minCard = 0;
$maxCard = 5;

////// TURN CHILD?
$isChildTurn = (strcmp($child->id, $idGamer) == 0 && $isBot == NO_BOT 
        && strcmp($parent->id, $idGamer) != 0 ) || $isBot == BOT;

if ($isChildTurn) {
    $minCard = 5;
    $maxCard = 10;
}

//$response[DATA]["TEXT"] = "minCard->".$minCard." maxCard->".$maxCard."  ".$isChildTurn;

//is check on double card in atlas 

$queryCard = array('_id' => new MongoInt32($cardNew));
$cursorCard = $collectionIiCards->find($queryCard);
foreach ($cursorCard as $row) {
    $cardNew = $row["id_old"];
}

///////////////////////////////////////////////////////////

$replaceCards = array_slice($atlas, $minCard, $maxCard);

$where = array('$and' => array(
        array('_id' => array(
                '$gte' => $isBot != BOT ? MIN_CARD : MAX_CARD_DEFENCE,
                '$lte' => $isBot != BOT ? MAX_CARD : MAX_CARD_ATACK)
        ),
        array('id_old' => array('$nin' => $replaceCards))));
$cursor = $collectionIiCards->find($where);

$cardsArray = array();
foreach ($cursor as $row) {
    array_push($cardsArray, $row['id_old']);
}
$maxCards = count($cardsArray) - 1;

$cardNew = $cardsArray[mt_rand(0, $maxCards)];

///////////////////////////////////////////////////////////

$issetCardToAtlas = FALSE;

for ($i = $minCard; $i < $maxCard; ++$i) {

    if ($card == $atlas[$i]) {
        $atlas[$i] = $cardNew;
        $issetCardToAtlas = TRUE;
        break;
    }
}

if ($issetCardToAtlas == FALSE) {

    $response[DATA][ATLAS] = $atlas;

    $response[DATA][ACHIVIMENTS_PATH] = $upd->getLastAch($link, $idGamer);
    $response[DATA][PROGRESS] = $progress;
    $response[DATA][CARD] = $cardNew;
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND;
    $response[MESSAGE] = $lang ? CODE_ERROR_SYNC_EN : CODE_ERROR_SYNC_RU;
    $response[EXTRA_MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_GARD_EN : CODE_ERROR_NOT_FOUND_GARD_RU;
    $response[LEVEL] = 250;
    $m->close();
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

///////////////////////// INIT CARD////////////////////////////

$cardTurn = new Card();

$collectionCards = $link->selectCollection(TABLE_CARDS);
$cursor = $collectionCards->find(array('_id' => new MongoInt32($card)));

if ($cursor->count() <= 0) {

    $response[DATA][ATLAS] = $atlas;

    $response[DATA][ACHIVIMENTS_PATH] = $upd->getLastAch($link, $idGamer);
    $response[DATA][PROGRESS] = $progress;
    $response[DATA][CARD] = $cardNew;
    $response[SUCCESS] = CODE_ERROR_NOT_FOUND;
    $response[MESSAGE] = $lang ? CODE_ERROR_NOT_FOUND_GARD_EN : CODE_ERROR_NOT_FOUND_GARD_RU;
    $response[LEVEL] = 268;
    $m->close();
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}
foreach ($cursor as $row) {
    $cardTurn->initCard($row);
}


///////////////////////TURN ////////////////////////
///////////////////////////////////////////////////////////////////////////////////
////////////////////IF TURN ESCAPE //////////////////////////////////
if ($escape == TURN) {

    /////// COST CARD ////////////
    if ($isChildTurn) {
        $child->money -= $cardTurn->cost_money;
        $child->techo -= $cardTurn->cost_zavod;
        $child->units -= $cardTurn->cost_units;
    } else {
        $parent->money -= $cardTurn->cost_money;
        $parent->techo -= $cardTurn->cost_zavod;
        $parent->units -= $cardTurn->cost_units;
    }
/////////////// CHECK COST //////////////////////
    if ($child->money < 0 || $child->techo < 0 || $child->units < 0 ||
            $parent->money < 0 || $parent->techo < 0 || $parent->units < 0) {

        $response[DATA][CORRECT] = $correctResourse;

        $response[DATA][ACHIVIMENTS_PATH] = $upd->getLastAch($link, $idGamer);
        $response[DATA][PROGRESS] = $progress;
        $response[DATA][CARD] = $cardNew;
        $response[SUCCESS] = CODE_ERROR_NOT_ENOUGHT_MONEY;
        $response[MESSAGE] = $lang ? CODE_ERROR_SYNC_EN : CODE_ERROR_SYNC_RU;
        $response[EXTRA_MESSAGE] = $lang ? CODE_ERROR_NOT_ENOUGHT_RESOURSE_EN : CODE_ERROR_NOT_ENOUGHT_RESOURSE_RU;
        $response[LEVEL] = 302;
        $m->close();
        die(json_encode($response, JSON_UNESCAPED_SLASHES));
    }
    ///////////////// PARAMS CARD BACKGROUND /////////////////////
//////////////// CARD BONUSES /////////////////////////////////////
    if ($cardTurn->back_image == CARD_OFFICER) {
        if ($isChildTurn) {
            $child->moneySetBonus($card, $link, $parent);
        } else {
            $parent->moneySetBonus($card, $link, $child);
        }
    }

    if ($cardTurn->back_image == CARD_INDUSTRY) {
        if ($isChildTurn) {
            $child->techoSetBonus($card, $link, $parent);
        } else {
            $parent->techoSetBonus($card, $link, $child);
        }
    }

    if ($cardTurn->back_image == CARD_UNITS) {
        if ($isChildTurn) {
            $child->unitsSetBonus($card, $link, $parent);
        } else {
            $parent->unitsSetBonus($card, $link, $child);
        }
    }

    if ($cardTurn->back_image == CARD_BONUS) {

        if ($cardTurn->money_en == MINUS_ONE_BONUS) {
            if ($isChildTurn) {
                $child->moneyRemEnemyOneBonus($card, $link, $parent);
            } else {
                $parent->moneyRemEnemyOneBonus($card, $link, $child);
            }
        }
        if ($cardTurn->money_en == MINUS_TWICE_BONUS) {
            if ($isChildTurn) {
                $child->moneyRemTwoBonus($card, $link, $parent);
            } else {
                $parent->moneyRemTwoBonus($card, $link, $child);
            }
        }
        if ($cardTurn->money_en == MAX_BONUS_ME) {
            if ($isChildTurn) {
                $child->moneyMaxEnemyUser($card, $link, $parent);
            } else {
                $parent->moneyMaxEnemyUser($card, $link, $child);
            }
        }

        if ($cardTurn->zavod_en == MINUS_ONE_BONUS) {
            if ($isChildTurn) {
                $child->techoRemEnemyOneBonus($card, $link, $parent);
            } else {
                $parent->techoRemEnemyOneBonus($card, $link, $child);
            }
        }
        if ($cardTurn->zavod_en == MINUS_TWICE_BONUS) {
            if ($isChildTurn) {
                $child->techoRemTwoBonus($card, $link, $parent);
            } else {
                $parent->techoRemTwoBonus($card, $link, $child);
            }
        }
        if ($cardTurn->zavod_en == MAX_BONUS_ME) {
            if ($isChildTurn) {
                $child->techoMaxEnemyUser($card, $link, $parent);
            } else {
                $parent->techoMaxEnemyUser($card, $link, $child);
            }
        }

        if ($cardTurn->units_en == MINUS_ONE_BONUS) {
            if ($isChildTurn) {
                $child->unitsRemEnemyOneBonus($card, $link, $parent);
            } else {
                $parent->unitsRemEnemyOneBonus($card, $link, $child);
            }
        }
        if ($cardTurn->units_en == MINUS_TWICE_BONUS) {
            if ($isChildTurn) {
                $child->unitsRemTwoBonus($card, $link, $parent);
            } else {
                $parent->unitsRemTwoBonus($card, $link, $child);
            }
        }

        if ($cardTurn->units_en == MAX_BONUS_ME) {
            if ($isChildTurn) {
                $child->unitsMaxEnemyUser($card, $link, $parent);
            } else {
                $parent->unitsMaxEnemyUser($card, $link, $child);
            }
        }
    }


//////////////// CARD RES /////////////////////////////////////
    if ($cardTurn->back_image == CARD_RES) {

        ////////////////UNITS /////////////////////////////

        if ($cardTurn->units == PLUS_ONE) {
            if ($isChildTurn) {
                $child->state += 1;
            } else {
                $parent->state += 1;
            }
        }
        if ($cardTurn->units == MINUS_ONE) {
            if ($isChildTurn) {
                $child->state += $child->state == 1 ? 0 : -1;
            } else {
                $parent->state += $parent->state == 1 ? 0 : -1;
            }
        }
        if ($cardTurn->units_en == PLUS_ONE) {
            if (!$isChildTurn) {
                $child->state += 1;
            } else {
                $parent->state += 1;
            }
        }
        if ($cardTurn->units_en == MINUS_ONE) {
            if (!$isChildTurn) {
                $child->state += $child->state == 1 ? 0 : -1;
            } else {
                $parent->state += $parent->state == 1 ? 0 : -1;
            }
        }
         if ($cardTurn->units_en == EQUAL_MAX_OR_MIN_TWICE) {
            if (!$isChildTurn) {
                $child->state += $child->state == 1 ? 0 : -2;
            } else {
                $parent->state += $parent->state == 1 ? 0 : -2;
            }
        }
        if ($cardTurn->units == PLUS_TWICE) {
            if ($isChildTurn) {
                $child->state += 2;
            } else {
                $parent->state += 2;
            }
        }
        if ($cardTurn->units == EQUAL_MAX_OR_MIN_TWICE) {
            if ($parent->state > $child->state) {
                $child->state = $parent->state;
            } else {
                $parent->state = $child->state;
            }
        }
        if ($cardTurn->units == IF_LESS_PLUS_TWICE_ELSE_PLUS_ONE) {
            if ($isChildTurn) {
                if ($child->state < $parent->state) {
                    $child->state += 2;
                } else {
                    $child->state += 1;
                }
            } else {
                if ($parent->state < $child->state) {
                    $parent->state += 2;
                } else {
                    $parent->state += 1;
                }
            }
        }
        if ($cardTurn->units == IF_LESS_EQUAL_MAX) {
            if ($isChildTurn) {
                if ($child->state < $parent->state) {
                    $child->state = $parent->state;
                }
            } else {
                if ($parent->state < $child->state) {
                    $parent->state = $child->state;
                }
            }
        }
        ///////////////TECHNO ////////////////////////

        if ($cardTurn->zavod == PLUS_ONE) {
            if ($isChildTurn) {
                $child->industry += 1;
            } else {
                $parent->industry += 1;
            }
        }
        if ($cardTurn->zavod == MINUS_ONE) {
            if ($isChildTurn) {
                $child->industry += $child->industry == 1 ? 0 : -1;
            } else {
                $parent->industry += $parent->industry == 1 ? 0 : -1;
            }
        }
        if ($cardTurn->zavod_en == PLUS_ONE) {
            if (!$isChildTurn) {
                $child->industry += 1;
            } else {
                $parent->industry += 1;
            }
        }
        if ($cardTurn->zavod_en == MINUS_ONE) {
            if (!$isChildTurn) {
                $child->industry += $child->industry == 1 ? 0 : -1;
            } else {
                $parent->industry += $parent->industry == 1 ? 0 : -1;
            }
        }
        if ($cardTurn->zavod == PLUS_TWICE) {
            if ($isChildTurn) {
                $child->industry += 2;
            } else {
                $parent->industry += 2;
            }
        }
        if ($cardTurn->zavod == EQUAL_MAX_OR_MIN_TWICE) {
            if ($parent->industry > $child->industry) {
                $child->industry = $parent->industry;
            } else {
                $parent->industry = $child->industry;
            }
        }
        if ($cardTurn->zavod == IF_LESS_PLUS_TWICE_ELSE_PLUS_ONE) {
            if ($isChildTurn) {
                if ($child->industry < $parent->industry) {
                    $child->industry += 2;
                } else {
                    $child->industry += 1;
                }
            } else {
                if ($parent->industry < $child->industry) {
                    $parent->industry += 2;
                } else {
                    $parent->industry += 1;
                }
            }
        }
        if ($cardTurn->zavod == IF_LESS_EQUAL_MAX) {
            if ($isChildTurn) {
                if ($child->industry < $parent->industry) {
                    $child->industry = $parent->industry;
                }
            } else {
                if ($parent->industry < $child->industry) {
                    $parent->industry = $child->industry;
                }
            }
        }


        ////////////////////MONEY ///////////////////////

        if ($cardTurn->money == PLUS_ONE) {
            if ($isChildTurn) {
                $child->bank += 1;
            } else {
                $parent->bank += 1;
            }
        }
        if ($cardTurn->money_en == PLUS_ONE) {
            if (!$isChildTurn) {
                $child->bank += 1;
            } else {
                $parent->bank += 1;
            }
        }
        if ($cardTurn->money == MINUS_ONE) {
            if ($isChildTurn) {
                $child->bank += $child->bank == 1 ? 0 : -1;
            } else {
                $parent->bank += $parent->bank == 1 ? 0 : -1;
            }
        }
        if ($cardTurn->money_en == MINUS_ONE) {
            if (!$isChildTurn) {
                $child->bank += $child->bank == 1 ? 0 : -1;
            } else {
                $parent->bank += $parent->bank == 1 ? 0 : -1;
            }
        }
        if ($cardTurn->money == PLUS_TWICE) {
            if ($isChildTurn) {
                $child->bank += 2;
            } else {
                $parent->bank += 2;
            }
        }
        if ($cardTurn->money == EQUAL_MAX_OR_MIN_TWICE) {
            if ($parent->bank > $child->bank) {
                $child->bank = $parent->bank;
            } else {
                $parent->bank = $child->bank;
            }
        }
        if ($cardTurn->money == IF_LESS_PLUS_TWICE_ELSE_PLUS_ONE) {
            if ($isChildTurn) {
                if ($child->bank < $parent->bank) {
                    $child->bank += 2;
                } else {
                    $child->bank += 1;
                }
            } else {
                if ($parent->bank < $child->bank) {
                    $parent->bank += 2;
                } else {
                    $parent->bank += 1;
                }
            }
        }
        if ($cardTurn->money == IF_LESS_EQUAL_MAX) {
            if ($isChildTurn) {
                if ($child->bank < $parent->bank) {
                    $child->bank = $parent->bank;
                }
            } else {
                if ($parent->bank < $child->bank) {
                    $parent->bank = $child->bank;
                }
            }
        }
        //////////////////////////


        if ($cardTurn->esc != 0) {

            $collectionIiCards = $link->selectCollection(TABLE_II_CARDS);

            $cards = $parent->initAtlass($link, $atlas);

            $minCard = $cardTurn->row_tow == 0 ? 0 : 5;
            $maxCard = $cardTurn->row_tow == 0 ? 5 : 10;

            if ($isChildTurn) {
                $minCard = $cardTurn->row_tow == 0 ? 5 : 0;
                $maxCard = $cardTurn->row_tow == 0 ? 10 : 5;
            }

            $replaceCards = array_slice($atlas, $minCard, $maxCard);

            $where = array('$and' => array(
                    array('_id' => array(
                            '$gte' => $isBot != BOT ? MIN_CARD : MAX_CARD_DEFENCE,
                            '$lte' => $isBot != BOT ? MAX_CARD : MAX_CARD_ATACK)
                    ),
                    array('id_old' => array('$nin' => $replaceCards))));

            $cursor = $collectionIiCards->find($where);

            $cardsArray = array();
            foreach ($cursor as $row) {
                array_push($cardsArray, $row['id_old']);
            }

            $maxCards = count($cardsArray) - 1;

            switch ($cardTurn->esc) {

                ////RES CARDS

                case CARD_OFFICER:
                case CARD_INDUSTRY:
                case CARD_UNITS:

                    for ($i = $minCard; $i < $maxCard; ++$i) {
                        if ($cards[$i]->back_image == $cardTurn->esc) {
                            $atlas[$i] = $cardsArray[mt_rand(0, $maxCards)];
                        }
                    }
                    break;

                ////OTHER CARDS
            /*
                default :
                    for ($i = $minCard; $i < $maxCard; ++$i) {
                        if ($atlas[$i] == $cardNew) {
                            continue;
                        }
                        $atlas[$i] = $cardsArray[mt_rand(0, $maxCards)];
                    }
                    break;
            */
                    
                        }
        }


        //////
////////////////////////////////////////////////////
        if ($isChildTurn) {
            $child->updResourse($parent, $cardTurn);
        } else {
            $parent->updResourse($child, $cardTurn);
        }
    }



    //////////////// CARD ATACK /////////////////////////////////////
    ///////////////////  8 - default, 7 - north, 6 - south 

    if ($isChildTurn) {
        if ($cardTurn->back_image == CARD_CSA) {
            if ($child->fraction == NORTH) {
                $child->atack($parent, $cardTurn);
            } else {
                $child->atackBonus($parent, $cardTurn);
            }
        }

        if ($cardTurn->back_image == CARD_USA) {
            if ($child->fraction == SOUTH) {
                $child->atack($parent, $cardTurn);
            } else {
                $child->atackBonus($parent, $cardTurn);
            }
        }

        if ($cardTurn->back_image == CARD_CSA_USA) {
            $child->atack($parent, $cardTurn);
        }
    } else {
        if ($cardTurn->back_image == CARD_CSA) {
            if ($parent->fraction == NORTH) {
                $parent->atack($child, $cardTurn);
            } else {
                $parent->atackBonus($child, $cardTurn);
            }
        }

        if ($cardTurn->back_image == CARD_USA) {
            if ($parent->fraction == SOUTH) {
                $parent->atack($child, $cardTurn);
            } else {
                $parent->atackBonus($child, $cardTurn);
            }
        }

        if ($cardTurn->back_image == CARD_CSA_USA) {
            $parent->atack($child, $cardTurn);
        }
    }
}



$money = $isChildTurn ? $child->money_bon + $child->bank : $parent->money_bon + $parent->bank;
if ($money < 0) {
    $money = 0;
}
$techo = $isChildTurn ? $child->techo_bon + $child->industry : $parent->techo_bon + $parent->industry;
if ($techo < 0) {
    $techo = 0;
}
$units = $isChildTurn ? $child->units_bon + $child->state : $parent->units_bon + $parent->state;
if ($units < 0) {
    $units = 0;
}
$tower = $isChildTurn ? $child->tower_bon : $parent->tower_bon;
if ($tower < 0) {
    $tower = 0;
}
$rov = $isChildTurn ? $child->row_bon : $parent->row_bon;
if ($rov < 0) {
    $rov = 0;
}

///////////////////// INIT GCM KEY ///////////////////////////////


if (strcmp($parent->id, $idGamer) != 0) {
    $query = array('_id' => new MongoId($parent->id));
}
if (strcmp($child->id, $idGamer) != 0) {
    $query = array('_id' => new MongoId($child->id));
}
$cursor = $collectionUser->find($query);
foreach ($cursor as $row) {
    $registrationIds = $row["tokenGcm"];
    $idEnemy = $row["_id"]->{'$id'};
    break;
}

////// CHECK ON TURN AGAIN AND OTHER, YOU MUST DO

$isAgain = $cardTurn->again == 2 && $escape == TURN;

if (!$isAgain) {

    if ($isChildTurn) {
        $child->atackEnemyBonus($parent);

        $child->money += $money;
        $child->techo += $techo;
        $child->units += $units;
        $child->tower += $tower;
        $child->row += $rov;
    } else {
        $parent->atackEnemyBonus($child);

        $parent->money += $money;
        $parent->techo += $techo;
        $parent->units += $units;
        $parent->tower += $tower;
        $parent->row += $rov;
    }
}

/////////////////////////////// CHECK ON MINUS ////////////////

if ($parent->money < 0) {
    $parent->money = 0;
}
if ($parent->techo < 0) {
    $parent->techo = 0;
}
if ($parent->units < 0) {
    $parent->units = 0;
}
if ($parent->tower < 0) {
    $parent->tower = 0;
}
if ($parent->row < 0) {
    $parent->row = 0;
}

if ($child->money < 0) {
    $child->money = 0;
}
if ($child->techo < 0) {
    $child->techo = 0;
}
if ($child->units < 0) {
    $child->units = 0;
}
if ($child->tower < 0) {
    $child->tower = 0;
}
if ($child->row < 0) {
    $child->row = 0;
}

////////////////// CHECK TURN ////////////////////////////



if (!$isAgain) {

    $escape_back = 0;
    $card_back_last = -1;
    $idBack = null;

    $collectionCards = $link->selectCollection(TABLE_CARDS);
    $cardBack = new Card();


    $arTurns = array();

    $reversed = array_reverse($turns);

    foreach ($reversed as $item) {

        if (!isset($item) || $item["escape"] == 2) {
            continue;
        }

        $turn = new Turn();
        $turn->escape = $item["escape"];
        $turn->card = $item["card"];
        $turn->id_gamer = $item["id_gamer"];
        $turn->id_game = $item["id_game"];
        $turn->progress = $item["progress"];
        $turn->time = $item["time"];
        $turn->card_new = $item["card_new"];
        $turn->bot = $item["bot"];

        array_push($arTurns, $turn);

        if (sizeof($arTurns) == 2) {
            break;
        }
    }

    $result = $arTurns;

    if (sizeof($result) > 0) {

        $escape_back = array_values($result)[0]->escape;
        $card_back_last = array_values($result)[0]->card;
        $idBack = array_values($result)[0]->id_gamer;

        $query = array('_id' => new MongoInt32($card_back_last));

        $cursor = $collectionCards->find($query);
        foreach ($cursor as $row) {
            $cardBack->initCard($row);
        }

        if ($cardBack->esc == 9 && $escape_back == 0 && strcmp($idBack, $idGamer) == 0) {
            
       //        $response[DATA]["969"] = array();

            /////////////////////////////SAVE TURN FIREBASE ////////////////////
            $turn = array(
                "time" => $time,
                "card" => (int) $card,
                "card_new" => $cardNew,
                "escape" => (int) $escape,
                "progress" => $progress,
                "bot" => (int) $isBot,
                "id_gamer" => $idGamer,
                "id_game" => $idGame
            );

            array_push($turns, $turn);


            $params = array(
                "bank" => new MongoInt32($parent->bank),
                "money" => new MongoInt32($parent->money),
                "industry" => new MongoInt32($parent->industry),
                "techo" => new MongoInt32($parent->techo),
                "state" => new MongoInt32($parent->state),
                "units" => new MongoInt32($parent->units),
                "bonus_money_1" => new MongoInt32($parent->bonus_money_1),
                "bonus_money_2" => new MongoInt32($parent->bonus_money_2),
                "bonus_money_3" => new MongoInt32($parent->bonus_money_3),
                "bonus_techo_1" => new MongoInt32($parent->bonus_techo_1),
                "bonus_techo_2" => new MongoInt32($parent->bonus_techo_2),
                "bonus_techo_3" => new MongoInt32($parent->bonus_techo_3),
                "bonus_units_1" => new MongoInt32($parent->bonus_units_1),
                "bonus_units_2" => new MongoInt32($parent->bonus_units_2),
                "bonus_units_3" => new MongoInt32($parent->bonus_units_3),
                "tower" => new MongoInt32($parent->tower),
                "row" => new MongoInt32($parent->row),
                "money_bon" => new MongoInt32($parent->money_bon),
                "techo_bon" => new MongoInt32($parent->techo_bon),
                "units_bon" => new MongoInt32($parent->units_bon),
                "tower_bon" => new MongoInt32($parent->tower_bon),
                "row_bon" => new MongoInt32($parent->row_bon),
                "atack_bon" => new MongoInt32($parent->atack_bon),
                //////////////////////////////////////////////////////
                "bank_enemy" => new MongoInt32($child->bank),
                "money_enemy" => new MongoInt32($child->money),
                "industry_enemy" => new MongoInt32($child->industry),
                "techo_enemy" => new MongoInt32($child->techo),
                "state_enemy" => new MongoInt32($child->state),
                "units_enemy" => new MongoInt32($child->units),
                "bonus_money_enemy_1" => new MongoInt32($child->bonus_money_1),
                "bonus_money_enemy_2" => new MongoInt32($child->bonus_money_2),
                "bonus_money_enemy_3" => new MongoInt32($child->bonus_money_3),
                "bonus_techo_enemy_1" => new MongoInt32($child->bonus_techo_1),
                "bonus_techo_enemy_2" => new MongoInt32($child->bonus_techo_2),
                "bonus_techo_enemy_3" => new MongoInt32($child->bonus_techo_3),
                "bonus_units_enemy_1" => new MongoInt32($child->bonus_units_1),
                "bonus_units_enemy_2" => new MongoInt32($child->bonus_units_2),
                "bonus_units_enemy_3" => new MongoInt32($child->bonus_units_3),
                "tower_enemy" => new MongoInt32($child->tower),
                "row_enemy" => new MongoInt32($child->row),
                "money_bon_enemy" => new MongoInt32($child->money_bon),
                "techo_bon_enemy" => new MongoInt32($child->techo_bon),
                "units_bon_enemy" => new MongoInt32($child->units_bon),
                "tower_bon_enemy" => new MongoInt32($child->tower_bon),
                "row_bon_enemy" => new MongoInt32($child->row_bon),
                "atack_bon_enemy" => new MongoInt32($child->atack_bon)
            );


            $query = array('_id' => new MongoId($idGame));
            $setQuery = array(
                '$set' => array(
                    "date_time" => $dateToday->getTimestamp(),
                    "params" => $params,
                    "progress" => $progress,
                    "turns" => $turns,
                    "atlas" => $atlas
                )
            );
            $collectionGame->update($query, $setQuery);

            ///////////////////////////////////SEND PUSH ///////////////////////////////////

            if (isset($registrationIds) && $isBot == NO_BOT) {

                $dataTurns = array();
                if (!empty($turn)) {
                    array_push($dataTurns, $turn);
                }

                $dataTurn = array();
                $dataTurn[DATA]["fraction"] = $fractions;
                $dataTurn[DATA][ACHIVIMENTS_PATH] = $upd->getLastAch($link, $idEnemy);
                $dataTurn[DATA][TURNS_PATH] = $dataTurns;
                $dataTurn[DATA][PROGRESS] = $progress;
                $dataTurn[DATA][ATLAS] = $atlas;
                $dataTurn[SUCCESS] = CODE_COMPLITE;

                $firebase = new Firebase();
                $push = new Push();

                $title = 'Freedom or Union';
                $message = $lang ? CODE_TURN_ENEMY_EN : CODE_TURN_ENEMY_RU;
                $push_type = 'turn';

                $push->setTitle($title);
                $push->setMessage($message);
                $push->setType($push_type);

                $push->setData($dataTurn);

                $json = $push->getPush();
                $res = $firebase->send($registrationIds, $json);

                $response[DATA][PUSH] = $res;
            }
            /////////////////////////////

            $response[DATA]["fraction"] = $fractions;

            $response[DATA][TIME] = $time;
            $response[DATA][ATLAS] = $atlas;
            $response[DATA][PROGRESS] = $progress;
            $response[DATA][CARD] = $cardNew;
            $response[SUCCESS] = CODE_COMPLITE;
            $response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
            $response[LEVEL] = 854;
            $m->close();
            die(json_encode($response, JSON_UNESCAPED_SLASHES));
        }
    }
}





////////////////////// IS CHECK GAME OVER//////////////////////////////

if (($child->tower <= 0 || $child->tower >= MAX_TOWER) ||
        ($parent->tower <= 0 || $parent->tower >= MAX_TOWER) ||
        ($child->tower <= 0 && $parent->tower <= 0 )) {

    $total_parent = new User_statistic();
    $total_child = new User_statistic();

    $query = array('_id' => new MongoId($parent->id));
    $cursor = $collectionUser->find($query);
    foreach ($cursor as $row) {
        $total_parent->id = $parent->id;
        $total_parent->total = $row["total"];
        $total_parent->coins = $row["coins"];
        $total_parent->game_only = $row["game_only"];
        $total_parent->game_win = $row["game_win"];
        $total_parent->game_lose = $row["game_lose"];
        $total_parent->game_lose_win = $row["game_lose_win"];
        $total_parent->fraction = $row["fraction"];

        $total_parent->isStoneWall = $row["isStoneWall"];
        $total_parent->isAppomattox = $row["isAppomattox"];
        $total_parent->isTurtle = $row["isTurtle"];
        $total_parent->isMenacingLook = $row["isMenacingLook"];
        $total_parent->isPatient = $row["isPatient"];
        $total_parent->isMedusa = $row["isMedusa"];
        $total_parent->isDavid = $row["isDavid"];
        $total_parent->isBully = $row["isBully"];

        $total_parent->isBuilder1 = $row["isBuilder1"];
        $total_parent->isBuilder2 = $row["isBuilder2"];
        $total_parent->isBuilder3 = $row["isBuilder4"];
        $total_parent->isBuilder4 = $row["isBuilder4"];

        $total_parent->isCollector1 = $row["isCollector1"];
        $total_parent->isCollector2 = $row["isCollector2"];
        $total_parent->isCollector3 = $row["isCollector3"];
        $total_parent->isCollector4 = $row["isCollector4"];

        $total_parent->isMcClellan1 = $row["isMcClellan1"];
        $total_parent->isMcClellan2 = $row["isMcClellan2"];
        $total_parent->isMcClellan3 = $row["isMcClellan3"];
        $total_parent->isMcClellan4 = $row["isMcClellan4"];

        $total_parent->isGrant1 = $row["isGrant1"];
        $total_parent->isGrant2 = $row["isGrant2"];
        $total_parent->isGrant3 = $row["isGrant3"];
        $total_parent->isGrant4 = $row["isGrant4"];
    }

    $query = array('_id' => new MongoId($child->id));
    $cursor = $collectionUser->find($query);
    foreach ($cursor as $row) {
        $total_child->id = $child->id;
        $total_child->total = $row["total"];
        $total_child->coins = $row["coins"];
        $total_child->game_only = $row["game_only"];
        $total_child->game_win = $row["game_win"];
        $total_child->game_lose = $row["game_lose"];
        $total_child->game_lose_win = $row["game_lose_win"];
        $total_child->fraction = $row["fraction"];

        $total_child->isStoneWall = $row["isStoneWall"];
        $total_child->isAppomattox = $row["isAppomattox"];
        $total_child->isTurtle = $row["isTurtle"];
        $total_child->isMenacingLook = $row["isMenacingLook"];
        $total_child->isPatient = $row["isPatient"];
        $total_child->isMedusa = $row["isMedusa"];
        $total_child->isDavid = $row["isDavid"];
        $total_child->isBully = $row["isBully"];

        $total_child->isBuilder1 = $row["isBuilder1"];
        $total_child->isBuilder2 = $row["isBuilder2"];
        $total_child->isBuilder3 = $row["isBuilder4"];
        $total_child->isBuilder4 = $row["isBuilder4"];

        $total_child->isCollector1 = $row["isCollector1"];
        $total_child->isCollector2 = $row["isCollector2"];
        $total_child->isCollector3 = $row["isCollector3"];
        $total_child->isCollector4 = $row["isCollector4"];

        $total_child->isMcClellan1 = $row["isMcClellan1"];
        $total_child->isMcClellan2 = $row["isMcClellan2"];
        $total_child->isMcClellan3 = $row["isMcClellan3"];
        $total_child->isMcClellan4 = $row["isMcClellan4"];

        $total_child->isGrant1 = $row["isGrant1"];
        $total_child->isGrant2 = $row["isGrant2"];
        $total_child->isGrant3 = $row["isGrant3"];
        $total_child->isGrant4 = $row["isGrant4"];
    }


    $total_parent->game_only += 1;
    $total_child->game_only += 1;

    if (($child->tower <= 0 && $parent->tower <= 0) || ($child->tower >= MAX_TOWER && $parent->tower >= MAX_TOWER)) {
        $total_parent->total += 1;
        $total_child->total += 1;
        $total_parent->coins += 1;
        $total_child->coins += 1;
        $total_parent->game_lose_win += 1;
        $total_child->game_lose_win += 1;
        $progress = WINNER_LOSSER;
    }
    if (($child->tower <= 0 && $parent->tower > 0) || ($child->tower < MAX_TOWER && $parent->tower >= MAX_TOWER)) {
        $total_parent->total += 2;
        $total_parent->coins += 2;
        $total_parent->game_win += 1;
        $total_child->game_lose += 1;
        $progress = WINNER_PARENT;
    }
    if (($parent->tower <= 0 && $child->tower > 0) || ($parent->tower < MAX_TOWER && $child->tower > MAX_TOWER)) {
        $total_child->total += 2;
        $total_child->coins += 2;
        $total_child->game_win += 1;
        $total_parent->game_lose += 1;
        $progress = WINNER_CHILD;
    }

    $collectionUser = $link->selectCollection(TABLE_USER);

    $query = array('_id' => new MongoId($parent->id));
    $setQuery = array(
        '$set' => array(
            "total" => new MongoInt32($total_parent->total),
            "coins" => new MongoInt32($total_parent->coins),
            "game_lose_win" => new MongoInt32($total_parent->game_lose_win),
            "game_only" => new MongoInt32($total_parent->game_only),
            "game_win" => new MongoInt32($total_parent->game_win),
            "game_lose" => new MongoInt32($total_parent->game_lose),
            "id_game" => null
        )
    );
    $collectionUser->update($query, $setQuery);

    if ($isBot == NO_BOT) {

        $query = array('_id' => new MongoId($child->id));
        $setQuery = array(
            '$set' => array(
                "total" => new MongoInt32($total_child->total),
                "coins" => new MongoInt32($total_child->coins),
                "game_lose_win" => new MongoInt32($total_child->game_lose_win),
                "game_only" => new MongoInt32($total_child->game_only),
                "game_win" => new MongoInt32($total_child->game_win),
                "game_lose" => new MongoInt32($total_child->game_lose),
                "id_game" => null
            )
        );
        $collectionUser->update($query, $setQuery);
    }
}

if (strcmp($parent->id, $child->id) == 0 && $progress != GAME_PLAY) {

    $query = array('_id' => new MongoId($idGame));
    $cursor = $collectionGame->find($query);
    $collectionGame->remove($query);

    //UPDATE ACHIVIMENT
    $upd = new updateArchiviment();
    $resUpd = $upd->update($link, $parent, $child, $isWhoBully, $progress, $isChildTurn);

    $saveFull = $progress != RESIGN_PARENT && $progress != TIME_OUT_PARENT && $progress != WINNER_CHILD;
    $query = array('_id' => new MongoId($parent->id));
    $setQuery = array(
        '$set' => array(
            "date_time" => $dateToday->getTimestamp(),
            "isStoneWall" => new MongoInt32($total_parent->isStoneWall += $resUpd["parent"]["isStoneWall"] && $saveFull ? 1 : 0),
            "isBuilder1" => new MongoInt32($total_parent->isBuilder1 += $resUpd["parent"]["levelBuilder"] == 1 && $saveFull ? 1 : 0),
            "isBuilder2" => new MongoInt32($total_parent->isBuilder2 += $resUpd["parent"]["levelBuilder"] == 2 && $saveFull ? 1 : 0),
            "isBuilder3" => new MongoInt32($total_parent->isBuilder3 += $resUpd["parent"]["levelBuilder"] == 3 && $saveFull ? 1 : 0),
            "isBuilder4" => new MongoInt32($total_parent->isBuilder4 += $resUpd["parent"]["levelBuilder"] == 4 && $saveFull ? 1 : 0),
            "isAppomattox" => new MongoInt32($total_parent->isAppomattox += $resUpd["parent"]["isAppomattox"] ? 1 : 0),
            "isTurtle" => new MongoInt32($total_parent->isTurtle += $resUpd["parent"]["isTurtle"] ? 1 : 0),
            "isMenacingLook" => new MongoInt32($total_parent->isMenacingLook += $resUpd["parent"]["isMenacingLook"] ? 1 : 0),
            "isPatient" => new MongoInt32($total_parent->isPatient += $resUpd["parent"]["isPatient"] && $saveFull ? 1 : 0),
            "isMedusa" => new MongoInt32($total_parent->isMedusa += $resUpd["parent"]["isMedusa"] ? 1 : 0),
            "isDavid" => new MongoInt32($total_parent->isDavid += $resUpd["parent"]["isDavid"] && $saveFull ? 1 : 0),
            "isBully" => new MongoInt32($total_parent->isBully += $resUpd["parent"]["isBully"] && $saveFull ? 1 : 0),
            "isCollector1" => new MongoInt32($total_parent->isCollector1 += $resUpd["parent"]["levelCollector"] == 1 && $saveFull ? 1 : 0),
            "isCollector2" => new MongoInt32($total_parent->isCollector2 += $resUpd["parent"]["levelCollector"] == 2 && $saveFull ? 1 : 0),
            "isCollector3" => new MongoInt32($total_parent->isCollector3 += $resUpd["parent"]["levelCollector"] == 3 && $saveFull ? 1 : 0),
            "isCollector4" => new MongoInt32($total_parent->isCollector4 += $resUpd["parent"]["levelCollector"] == 4 && $saveFull ? 1 : 0),
            "isMcClellan1" => new MongoInt32($total_parent->isMcClellan1 += $resUpd["parent"]["levelMcClellan"] == 1 && $saveFull ? 1 : 0),
            "isMcClellan2" => new MongoInt32($total_parent->isMcClellan2 += $resUpd["parent"]["levelMcClellan"] == 2 && $saveFull ? 1 : 0),
            "isMcClellan3" => new MongoInt32($total_parent->isMcClellan3 += $resUpd["parent"]["levelMcClellan"] == 3 && $saveFull ? 1 : 0),
            "isMcClellan4" => new MongoInt32($total_parent->isMcClellan4 += $resUpd["parent"]["levelMcClellan"] == 4 && $saveFull ? 1 : 0),
            "isGrant1" => new MongoInt32($total_parent->isGrant1 += $resUpd["parent"]["levelGrant"] == 1 && $saveFull ? 1 : 0),
            "isGrant2" => new MongoInt32($total_parent->isGrant2 += $resUpd["parent"]["levelGrant"] == 2 && $saveFull ? 1 : 0),
            "isGrant3" => new MongoInt32($total_parent->isGrant3 += $resUpd["parent"]["levelGrant"] == 3 && $saveFull ? 1 : 0),
            "isGrant4" => new MongoInt32($total_parent->isGrant4 += $resUpd["parent"]["levelGrant"] == 4 && $saveFull ? 1 : 0)
        )
    );
    $collectionUser->update($query, $setQuery);

    ////////////////////////////////////////////////////////////////

    $response[DATA][ACHIVIMENTS_PATH] = $upd->getLastAch($link, $idGamer);

    //////////////////////////////////////////////////////////////////////
    $response[DATA][PROGRESS] = $progress;
    $response[DATA][CARD] = $cardNew;
    $response[SUCCESS] = CODE_COMPLITE;
    $response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
    $response[LEVEL] = 1097;
    $m->close();
    die(json_encode($response, JSON_UNESCAPED_SLASHES));
}

/////////////////////////////////////////////////////////
$params = array(
    "bank" => new MongoInt32($parent->bank),
    "money" => new MongoInt32($parent->money),
    "industry" => new MongoInt32($parent->industry),
    "techo" => new MongoInt32($parent->techo),
    "state" => new MongoInt32($parent->state),
    "units" => new MongoInt32($parent->units),
    "bonus_money_1" => new MongoInt32($parent->bonus_money_1),
    "bonus_money_2" => new MongoInt32($parent->bonus_money_2),
    "bonus_money_3" => new MongoInt32($parent->bonus_money_3),
    "bonus_techo_1" => new MongoInt32($parent->bonus_techo_1),
    "bonus_techo_2" => new MongoInt32($parent->bonus_techo_2),
    "bonus_techo_3" => new MongoInt32($parent->bonus_techo_3),
    "bonus_units_1" => new MongoInt32($parent->bonus_units_1),
    "bonus_units_2" => new MongoInt32($parent->bonus_units_2),
    "bonus_units_3" => new MongoInt32($parent->bonus_units_3),
    "tower" => new MongoInt32($parent->tower),
    "row" => new MongoInt32($parent->row),
    "money_bon" => new MongoInt32($parent->money_bon),
    "techo_bon" => new MongoInt32($parent->techo_bon),
    "units_bon" => new MongoInt32($parent->units_bon),
    "tower_bon" => new MongoInt32($parent->tower_bon),
    "row_bon" => new MongoInt32($parent->row_bon),
    "atack_bon" => new MongoInt32($parent->atack_bon),
    //////////////////////////////////////////////////////
    "bank_enemy" => new MongoInt32($child->bank),
    "money_enemy" => new MongoInt32($child->money),
    "industry_enemy" => new MongoInt32($child->industry),
    "techo_enemy" => new MongoInt32($child->techo),
    "state_enemy" => new MongoInt32($child->state),
    "units_enemy" => new MongoInt32($child->units),
    "bonus_money_enemy_1" => new MongoInt32($child->bonus_money_1),
    "bonus_money_enemy_2" => new MongoInt32($child->bonus_money_2),
    "bonus_money_enemy_3" => new MongoInt32($child->bonus_money_3),
    "bonus_techo_enemy_1" => new MongoInt32($child->bonus_techo_1),
    "bonus_techo_enemy_2" => new MongoInt32($child->bonus_techo_2),
    "bonus_techo_enemy_3" => new MongoInt32($child->bonus_techo_3),
    "bonus_units_enemy_1" => new MongoInt32($child->bonus_units_1),
    "bonus_units_enemy_2" => new MongoInt32($child->bonus_units_2),
    "bonus_units_enemy_3" => new MongoInt32($child->bonus_units_3),
    "tower_enemy" => new MongoInt32($child->tower),
    "row_enemy" => new MongoInt32($child->row),
    "money_bon_enemy" => new MongoInt32($child->money_bon),
    "techo_bon_enemy" => new MongoInt32($child->techo_bon),
    "units_bon_enemy" => new MongoInt32($child->units_bon),
    "tower_bon_enemy" => new MongoInt32($child->tower_bon),
    "row_bon_enemy" => new MongoInt32($child->row_bon),
    "atack_bon_enemy" => new MongoInt32($child->atack_bon)
);

/////////////////////////////SAVE TURN FIREBASE ////////////////////
$turn = array(
    "time" => $time,
    "card" => (int) $card,
    "card_new" => $cardNew,
    "escape" => (int) $escape,
    "progress" => $progress,
    "bot" => (int) $isBot,
    "id_gamer" => $idGamer,
    "id_game" => $idGame
);

array_push($turns, $turn);


$query = array('_id' => new MongoId($idGame));
$setQuery = array(
    '$set' => array(
        "date_time" => $dateToday->getTimestamp(),
        "params" => $params,
        "progress" => $progress,
        "atlas" => $atlas,
        "turns" => $turns
    )
);
$collectionGame->update($query, $setQuery);

/////////////////////////////
//UPDATE ACHIVIMENT
$upd = new updateArchiviment();
$resUpd = $upd->update($link, $parent, $child, $isWhoBully, $progress, $isChildTurn);

if ($progress != GAME_PLAY) {

    $saveFull = $progress != RESIGN_PARENT && $progress != TIME_OUT_PARENT && $progress != WINNER_CHILD;
    $query = array('_id' => new MongoId($parent->id));
    $setQuery = array(
        '$set' => array(
            "date_time" => $dateToday->getTimestamp(),
            "isStoneWall" => new MongoInt32($total_parent->isStoneWall += $resUpd["parent"]["isStoneWall"] && $saveFull ? 1 : 0),
            "isBuilder1" => new MongoInt32($total_parent->isBuilder1 += $resUpd["parent"]["levelBuilder"] == 1 && $saveFull ? 1 : 0),
            "isBuilder2" => new MongoInt32($total_parent->isBuilder2 += $resUpd["parent"]["levelBuilder"] == 2 && $saveFull ? 1 : 0),
            "isBuilder3" => new MongoInt32($total_parent->isBuilder3 += $resUpd["parent"]["levelBuilder"] == 3 && $saveFull ? 1 : 0),
            "isBuilder4" => new MongoInt32($total_parent->isBuilder4 += $resUpd["parent"]["levelBuilder"] == 4 && $saveFull ? 1 : 0),
            "isAppomattox" => new MongoInt32($total_parent->isAppomattox += $resUpd["parent"]["isAppomattox"] ? 1 : 0),
            "isTurtle" => new MongoInt32($total_parent->isTurtle += $resUpd["parent"]["isTurtle"] ? 1 : 0),
            "isMenacingLook" => new MongoInt32($total_parent->isMenacingLook += $resUpd["parent"]["isMenacingLook"] ? 1 : 0),
            "isPatient" => new MongoInt32($total_parent->isPatient += $resUpd["parent"]["isPatient"] && $saveFull ? 1 : 0),
            "isMedusa" => new MongoInt32($total_parent->isMedusa += $resUpd["parent"]["isMedusa"] ? 1 : 0),
            "isDavid" => new MongoInt32($total_parent->isDavid += $resUpd["parent"]["isDavid"] && $saveFull ? 1 : 0),
            "isBully" => new MongoInt32($total_parent->isBully += $resUpd["parent"]["isBully"] && $saveFull ? 1 : 0),
            "isCollector1" => new MongoInt32($total_parent->isCollector1 += $resUpd["parent"]["levelCollector"] == 1 && $saveFull ? 1 : 0),
            "isCollector2" => new MongoInt32($total_parent->isCollector2 += $resUpd["parent"]["levelCollector"] == 2 && $saveFull ? 1 : 0),
            "isCollector3" => new MongoInt32($total_parent->isCollector3 += $resUpd["parent"]["levelCollector"] == 3 && $saveFull ? 1 : 0),
            "isCollector4" => new MongoInt32($total_parent->isCollector4 += $resUpd["parent"]["levelCollector"] == 4 && $saveFull ? 1 : 0),
            "isMcClellan1" => new MongoInt32($total_parent->isMcClellan1 += $resUpd["parent"]["levelMcClellan"] == 1 && $saveFull ? 1 : 0),
            "isMcClellan2" => new MongoInt32($total_parent->isMcClellan2 += $resUpd["parent"]["levelMcClellan"] == 2 && $saveFull ? 1 : 0),
            "isMcClellan3" => new MongoInt32($total_parent->isMcClellan3 += $resUpd["parent"]["levelMcClellan"] == 3 && $saveFull ? 1 : 0),
            "isMcClellan4" => new MongoInt32($total_parent->isMcClellan4 += $resUpd["parent"]["levelMcClellan"] == 4 && $saveFull ? 1 : 0),
            "isGrant1" => new MongoInt32($total_parent->isGrant1 += $resUpd["parent"]["levelGrant"] == 1 && $saveFull ? 1 : 0),
            "isGrant2" => new MongoInt32($total_parent->isGrant2 += $resUpd["parent"]["levelGrant"] == 2 && $saveFull ? 1 : 0),
            "isGrant3" => new MongoInt32($total_parent->isGrant3 += $resUpd["parent"]["levelGrant"] == 3 && $saveFull ? 1 : 0),
            "isGrant4" => new MongoInt32($total_parent->isGrant4 += $resUpd["parent"]["levelGrant"] == 4 && $saveFull ? 1 : 0)
        )
    );
    $collectionUser->update($query, $setQuery);

    if (strcmp($parent->id, $child->id) != 0) {

        $saveFull = $progress != RESIGN_CHILD && $progress != TIME_OUT_CHILD && $progress != WINNER_PARENT;
        $query = array('_id' => new MongoId($child->id));
        $setQuery = array(
            '$set' => array(
                "date_time" => $dateToday->getTimestamp(),
                "isStoneWall" => new MongoInt32($total_child->isStoneWall += $resUpd["child"]["isStoneWall"] && $saveFull ? 1 : 0),
                "isBuilder1" => new MongoInt32($total_child->isBuilder1 += $resUpd["child"]["levelBuilder"] == 1 && $saveFull ? 1 : 0),
                "isBuilder2" => new MongoInt32($total_child->isBuilder2 += $resUpd["child"]["levelBuilder"] == 2 && $saveFull ? 1 : 0),
                "isBuilder3" => new MongoInt32($total_child->isBuilder3 += $resUpd["child"]["levelBuilder"] == 3 && $saveFull ? 1 : 0),
                "isBuilder4" => new MongoInt32($total_child->isBuilder4 += $resUpd["child"]["levelBuilder"] == 4 && $saveFull ? 1 : 0),
                "isAppomattox" => new MongoInt32($total_child->isAppomattox += $resUpd["child"]["isAppomattox"] ? 1 : 0),
                "isTurtle" => new MongoInt32($total_child->isTurtle += $resUpd["child"]["isTurtle"] ? 1 : 0),
                "isMenacingLook" => new MongoInt32($total_child->isMenacingLook += $resUpd["child"]["isMenacingLook"] ? 1 : 0),
                "isPatient" => new MongoInt32($total_child->isPatient += $resUpd["child"]["isPatient"] && $saveFull ? 1 : 0),
                "isMedusa" => new MongoInt32($total_child->isMedusa += $resUpd["child"]["isMedusa"] ? 1 : 0),
                "isDavid" => new MongoInt32($total_child->isDavid += $resUpd["child"]["isDavid"] && $saveFull ? 1 : 0),
                "isBully" => new MongoInt32($total_child->isBully += $resUpd["child"]["isBully"] && $saveFull ? 1 : 0),
                "isCollector1" => new MongoInt32($total_child->isCollector1 += $resUpd["child"]["levelCollector"] == 1 && $saveFull ? 1 : 0),
                "isCollector2" => new MongoInt32($total_child->isCollector2 += $resUpd["child"]["levelCollector"] == 2 && $saveFull ? 1 : 0),
                "isCollector3" => new MongoInt32($total_child->isCollector3 += $resUpd["child"]["levelCollector"] == 3 && $saveFull ? 1 : 0),
                "isCollector4" => new MongoInt32($total_child->isCollector4 += $resUpd["child"]["levelCollector"] == 4 && $saveFull ? 1 : 0),
                "isMcClellan1" => new MongoInt32($total_child->isMcClellan1 += $resUpd["child"]["levelMcClellan"] == 1 && $saveFull ? 1 : 0),
                "isMcClellan2" => new MongoInt32($total_child->isMcClellan2 += $resUpd["child"]["levelMcClellan"] == 2 && $saveFull ? 1 : 0),
                "isMcClellan3" => new MongoInt32($total_child->isMcClellan3 += $resUpd["child"]["levelMcClellan"] == 3 && $saveFull ? 1 : 0),
                "isMcClellan4" => new MongoInt32($total_child->isMcClellan4 += $resUpd["child"]["levelMcClellan"] == 4 && $saveFull ? 1 : 0),
                "isGrant1" => new MongoInt32($total_child->isGrant1 += $resUpd["child"]["levelGrant"] == 1 && $saveFull ? 1 : 0),
                "isGrant2" => new MongoInt32($total_child->isGrant2 += $resUpd["child"]["levelGrant"] == 2 && $saveFull ? 1 : 0),
                "isGrant3" => new MongoInt32($total_child->isGrant3 += $resUpd["child"]["levelGrant"] == 3 && $saveFull ? 1 : 0),
                "isGrant4" => new MongoInt32($total_child->isGrant4 += $resUpd["child"]["levelGrant"] == 4 && $saveFull ? 1 : 0))
        );
        $collectionUser->update($query, $setQuery);
    }

    ////////////////////////////////////////////////////////////////

    $response[DATA][ACHIVIMENTS_PATH] = $upd->getLastAch($link, $idGamer);
}


///////////////////////////////////SEND PUSH ///////////////////////////////////

if (isset($registrationIds) && $isBot == NO_BOT) {

    $dataTurns = array();
    if (!empty($turn)) {
        array_push($dataTurns, $turn);
    }

    $dataTurn = array();
    $dataTurn[DATA]["fraction"] = $fractions;
    $dataTurn[DATA][ACHIVIMENTS_PATH] = $upd->getLastAch($link, $idEnemy);
    $dataTurn[DATA][TURNS_PATH] = $dataTurns;
    $dataTurn[DATA][PROGRESS] = $progress;
    $dataTurn[DATA][ATLAS] = $atlas;
    $dataTurn[SUCCESS] = CODE_COMPLITE;

    $firebase = new Firebase();
    $push = new Push();

    $title = 'Freedom or Union';
    $message = $lang ? CODE_TURN_ENEMY_EN : CODE_TURN_ENEMY_RU;
    $push_type = 'turn';

    $push->setTitle($title);
    $push->setMessage($message);
    $push->setType($push_type);

    $push->setData($dataTurn);

    $json = $push->getPush();
    $res = $firebase->send($registrationIds, $json);

    $response[DATA][PUSH] = $res;
}


////////////////////////////////////////////////////
$response[DATA]["fraction"] = $fractions;
$response[DATA][TIME] = $time;
$response[DATA][ATLAS] = $atlas;
$response[DATA][PROGRESS] = $progress;
$response[DATA][CARD] = $cardNew;
$response[SUCCESS] = CODE_COMPLITE;
$response[MESSAGE] = $lang ? CODE_COMPLITE_EN : CODE_COMPLITE_RU;
$response[LEVEL] = 1278;
$m->close();
die(json_encode($response, JSON_UNESCAPED_SLASHES));

function compareOrder($a, $b) {
    return $b->time - $a->time;
}
