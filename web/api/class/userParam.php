<?php

class User_param {

    public $id;
    public $bank;
    public $money;
    public $industry;
    public $techo;
    public $state;
    public $units;
    public $bonus_money_1;
    public $bonus_money_2;
    public $bonus_money_3;
    public $bonus_techo_1;
    public $bonus_techo_2;
    public $bonus_techo_3;
    public $bonus_units_1;
    public $bonus_units_2;
    public $bonus_units_3;
    public $tower;
    public $row;
    public $money_bon;
    public $techo_bon;
    public $units_bon;
    public $tower_bon;
    public $row_bon;
    public $atack_bon;
    public $fraction;

    public function atackBonus(&$userEnemy, &$cardTurn) {

        if ($cardTurn->atack < 0) {

            $atack = $cardTurn->atack;
            if ($atack >= -10) {
                $atack += -1;
            } else {
                if ($atack >= -20 && $atack < -10) {
                    $atack += -2;
                } else {
                    if ($atack >= -30 && $atack < -20) {
                        $atack += -3;
                    } else {
                        if ($atack >= -40 && $atack < -30) {
                            $atack += -6;
                        } else {
                            if ($atack >= -50 && $atack < -40) {
                                $atack += -9;
                            }
                        }
                    }
                }
            }


            $userEnemy->row += $atack;
            if ($userEnemy->row < 0) {
                $userEnemy->tower += $userEnemy->row;
                $userEnemy->row = 0;
            }

            $this->tower += $cardTurn->at_def_pl_tow;
            if ($this->tower < 0) {
                $this->tower = 0;
            }

            $this->row += $cardTurn->at_def_pl_rov;
            if ($this->row < 0) {
                $this->row = 0;
            }
        } else {

            $this->row += $cardTurn->at_def_pl_rov;
            if ($this->row < 0) {
                $this->row = 0;
            }
            $this->tower += $cardTurn->at_def_pl_tow;
            if ($this->tower < 0) {
                $this->tower = 0;
            }
            $userEnemy->row += $cardTurn->at_def_en_rov;
            if ($userEnemy->row < 0) {
                $userEnemy->row = 0;
            }
            $userEnemy->tower += $cardTurn->at_def_en_tow;
            if ($userEnemy->tower < 0) {
                $userEnemy->tower = 0;
            }
        }

        //// +  OR -  RESOURSE
        //// +  OR -  RESOURSE
        $this->updResourse($userEnemy, $cardTurn);
    }

    public function atack(&$userEnemy, &$cardTurn) {

        $buf = 0;

        if ($cardTurn->atack < 0) {
            $userEnemy->row += $cardTurn->atack;
            if ($userEnemy->row < 0) {
                $userEnemy->tower += $userEnemy->row;
                $userEnemy->row = 0;
            }

            $this->row += $cardTurn->at_def_pl_rov;
            if ($this->row < 0) {
                $buf = $this->row;
                $this->row = 0;
            }

            $this->tower += $cardTurn->at_def_pl_tow + $buf;
            if ($this->tower < 0) {
                $this->tower = 0;
            }

            //// +  OR -  RESOURSE
            $this->updResourse($userEnemy, $cardTurn);

            return;
        }

        //// +  OR -  TOWER AND ROW
        $this->row += $cardTurn->at_def_pl_rov;
        if ($this->row <= 0) {
            $buf = $cardTurn->row_tow != 1 ? $this->row : 0;
            $this->row = 0;
        }
        $this->tower += ($cardTurn->at_def_pl_tow + $buf);
        if ($this->tower < 0) {
            $this->tower = 0;
        }
        $buf = 0;
        $userEnemy->row += $cardTurn->at_def_en_rov;
        if ($userEnemy->row <= 0) {
            $buf = $cardTurn->row_tow != 1 ? $userEnemy->row : 0;
            $userEnemy->row = 0;
        }


        $userEnemy->tower += ($cardTurn->at_def_en_tow + $buf);
        if ($userEnemy->tower < 0) {
            $userEnemy->tower = 0;
        }

        //// +  OR -  RESOURSE
        $this->updResourse($userEnemy, $cardTurn);
    }

    public function atackEnemyBonus(&$userEnemy) {
        $userEnemy->row += $this->atack_bon;
        if ($userEnemy->row < 0) {
            $userEnemy->tower += $userEnemy->row;
            $userEnemy->row = 0;
        }
    }

    public function setBonus(&$bonus_1, &$bonus_2, &$bonus_3, $card, $link, &$userEnemy) {

        $bool = 0;
        $cardBuf = new Card();

        if ($bonus_1 == EMPTY_CARD) {
            $bonus_1 = $card;
            $bool = 1;
        }
        if ($bonus_2 == EMPTY_CARD && $bool == 0) {
            $bonus_2 = $card;
            $bool = 1;
        }
        if ($bonus_3 == EMPTY_CARD && $bool == 0) {
            $bonus_3 = $card;
            $bool = 1;
        }

        if ($bool == 1) {
            $this->getCard($cardBuf, $card, $link);
            $this->setBonusRes($userEnemy, $cardBuf);
        } else {
            $min = 0;

            if ($bonus_1 == $bonus_2 && $bonus_1 == $bonus_3) {
                $this->getCard($cardBuf, $bonus_1, $link);
                $this->remBonusRes($cardBuf, $userEnemy);

                $this->getCard($cardBuf, $card, $link);
                $this->setBonusRes($userEnemy, $cardBuf);
                $bonus_1 = $card;
            } else {
                if ($bonus_1 <= $bonus_2) {
                    if ($bonus_1 < $bonus_3) {
                        $min = & $bonus_1;
                    }
                    if ($bonus_1 > $bonus_3) {
                        $min = & $bonus_3;
                    }
                    if ($bonus_1 == $bonus_3) {
                        $min = & $bonus_1;
                    }
                } else {
                    if ($bonus_2 <= $bonus_3) {
                        $min = & $bonus_2;
                    }
                    if ($bonus_2 > $bonus_3) {
                        $min = & $bonus_3;
                    }
                }
            }

            if ($min != 0) {
                $this->getCard($cardBuf, $min, $link);
                $this->remBonusRes($cardBuf, $userEnemy);
                $min = $card;
                $this->getCard($cardBuf, $card, $link);
                $this->setBonusRes($userEnemy, $cardBuf);
            }
        }
    }

    ////////////////////// DEFAULT REMOVE MAX BONUS /////////////////////

    public function remOneBonus($card, $link, &$userEnemy) {

        $cardBuf = new Card();
        $this->getCard($cardBuf, $card, $link);
        $isEmpty = FALSE;

        if ($this->bonus_money_1 == $card) {
            $this->bonus_money_1 = 196;
            $isEmpty = TRUE;
        } else if ($this->bonus_money_2 == $card) {
            $this->bonus_money_2 = 196;
            $isEmpty = TRUE;
        } else if ($this->bonus_money_3 == $card) {
            $this->bonus_money_3 = 196;
            $isEmpty = TRUE;
        } else if ($this->bonus_techo_1 == $card) {
            $this->bonus_techo_1 = 196;
            $isEmpty = TRUE;
        } else if ($this->bonus_techo_2 == $card) {
            $this->bonus_techo_2 = 196;
            $isEmpty = TRUE;
        } else if ($this->bonus_techo_3 == $card) {
            $this->bonus_techo_3 = 196;
            $isEmpty = TRUE;
        } else if ($this->bonus_units_1 == $card) {
            $this->bonus_units_1 = 196;
            $isEmpty = TRUE;
        } else if ($this->bonus_units_2 == $card) {
            $this->bonus_units_2 = 196;
            $isEmpty = TRUE;
        } else if ($this->bonus_units_3 == $card) {
            $this->bonus_units_3 = 196;
            $isEmpty = TRUE;
        }

        if ($isEmpty) {
            $this->remBonusRes($cardBuf, $userEnemy);
        }
    }

    public function updResourse(&$userEnemy, &$cardTurn) {

        //// +  OR -  RESOURSE
        $this->money += $cardTurn->money_in;
        if ($this->money < 0) {
            $this->money = 0;
        }
        $userEnemy->money += $cardTurn->money_en_in;
        if ($userEnemy->money < 0) {
            $userEnemy->money = 0;
        }

        $this->techo += $cardTurn->zavod_in;
        if ($this->techo < 0) {
            $this->techo = 0;
        }
        $userEnemy->techo += $cardTurn->zavod_en_in;
        if ($userEnemy->techo < 0) {
            $userEnemy->techo = 0;
        }

        $this->units += $cardTurn->units_in;
        if ($this->units < 0) {
            $this->units = 0;
        }
        $userEnemy->units += $cardTurn->units_en_in;
        if ($userEnemy->units < 0) {
            $userEnemy->units = 0;
        }
    }

    ///////////////////////// DEFAULT REMOVE BONUS RESURSE /////////////////////
    public function remEnemyBonusRes(&$userEnemy, &$card) {

        $isEmpty = FALSE;

        if ($userEnemy->bonus_money_1 == $card->id) {
            $userEnemy->bonus_money_1 = 196;
            $isEmpty = TRUE;
        } else if ($userEnemy->bonus_money_2 == $card->id) {
            $userEnemy->bonus_money_2 = 196;
            $isEmpty = TRUE;
        } else if ($userEnemy->bonus_money_3 == $card->id) {
            $userEnemy->bonus_money_3 = 196;
            $isEmpty = TRUE;
        } else if ($userEnemy->bonus_techo_1 == $card->id) {
            $userEnemy->bonus_techo_1 = 196;
            $isEmpty = TRUE;
        } else if ($userEnemy->bonus_techo_2 == $card->id) {
            $userEnemy->bonus_techo_2 = 196;
            $isEmpty = TRUE;
        } else if ($userEnemy->bonus_techo_3 == $card->id) {
            $userEnemy->bonus_techo_3 = 196;
            $isEmpty = TRUE;
        } else if ($userEnemy->bonus_units_1 == $card->id) {
            $userEnemy->bonus_units_1 = 196;
            $isEmpty = TRUE;
        } else if ($userEnemy->bonus_units_2 == $card->id) {
            $userEnemy->bonus_units_2 = 196;
            $isEmpty = TRUE;
        } else if ($userEnemy->bonus_units_3 == $card->id) {
            $userEnemy->bonus_units_3 = 196;
            $isEmpty = TRUE;
        }

        if ($isEmpty) {
            $userEnemy->money_bon -= $card->money_in;
            $this->money_bon -= $card->money_en_in;

            $userEnemy->techo_bon -= $card->zavod_in;
            $this->techo_bon -= $card->zavod_en_in;

            $userEnemy->units_bon -= $card->units_in;
            $this->units_bon -= $card->units_en_in;

            $userEnemy->tower_bon -= $card->at_def_pl_tow;
            $this->tower_bon -= $card->at_def_en_tow;

            $userEnemy->row_bon -= $card->at_def_pl_rov;
            $this->row_bon -= $card->at_def_en_rov;

            $userEnemy->atack_bon -= $card->atack;
        }
    }

    ///////////////////////// DEFAULT SET BONUS RESURSE /////////////////////

    public function setBonusRes(&$userEnemy, &$cardSQL) {

        $this->money_bon += $cardSQL->money_in;
        $userEnemy->money_bon += $cardSQL->money_en_in;

        $this->techo_bon += $cardSQL->zavod_in;
        $userEnemy->techo_bon += $cardSQL->zavod_en_in;

        $this->units_bon += $cardSQL->units_in;
        $userEnemy->units_bon += $cardSQL->units_en_in;

        $this->tower_bon += $cardSQL->at_def_pl_tow;
        $userEnemy->tower_bon += $cardSQL->at_def_en_tow;

        $this->row_bon += $cardSQL->at_def_pl_rov;
        $userEnemy->row_bon += $cardSQL->at_def_en_rov;

        $this->atack_bon += $cardSQL->atack;
    }

    ///////////////////////// DEFAULT REMOVE BONUS RESURSE /////////////////////
    public function remBonusRes(&$cardSQL, &$userEnemy) {

        $this->money_bon -= $cardSQL->money_in;
        $userEnemy->money_bon -= $cardSQL->money_en_in;

        $this->techo_bon -= $cardSQL->zavod_in;
        $userEnemy->techo_bon -= $cardSQL->zavod_en_in;

        $this->units_bon -= $cardSQL->units_in;
        $userEnemy->units_bon -= $cardSQL->units_en_in;

        $this->tower_bon -= $cardSQL->at_def_pl_tow;
        $userEnemy->tower_bon -= $cardSQL->at_def_en_tow;

        $this->row_bon -= $cardSQL->at_def_pl_rov;
        $userEnemy->row_bon -= $cardSQL->at_def_en_rov;

        $this->atack_bon -= $cardSQL->atack;
    }

    public function isCheckBonus(&$bonus_1, &$bonus_2, &$bonus_3, &$card) {

        if (($bonus_1 == $card && $bonus_1 != 196) || ($bonus_2 == $card && $bonus_2 != 196) || ($bonus_3 == $card && $bonus_3 != 196)) {
            return 0;
        }
        return -1;
    }

    //////////////////////// MONEY //////////////

    public function moneySetBonus($card, $link, &$userEnemy) {
        if ($this->isCheckBonus($this->bonus_money_1, $this->bonus_money_2, $this->bonus_money_3, $card) == -1) {
            $this->setBonus($this->bonus_money_1, $this->bonus_money_2, $this->bonus_money_3, $card, $link, $userEnemy);
        }
    }

    public function moneyRemEnemyOneBonus($card, $link, &$userEnemy) {
        $max = $this->remOneEnemyBonus($userEnemy->bonus_money_1, $userEnemy->bonus_money_2, $userEnemy->bonus_money_3, $link, $userEnemy);
    }

    public function moneyRemTwoBonus($card, $link, &$userEnemy) {
        $this->remTwoBonus($userEnemy->bonus_money_1, $userEnemy->bonus_money_2, $userEnemy->bonus_money_3, $link, $userEnemy);
    }

    public function moneyMaxEnemyUser($card, $link, &$userEnemy) {
        $max = $this->remOneEnemyBonus($userEnemy->bonus_money_1, $userEnemy->bonus_money_2, $userEnemy->bonus_money_3, $link, $userEnemy);
        if ($max == EMPTY_CARD || $max != 0 && $max != $this->bonus_money_1 && $max != $this->bonus_money_2 && $max != $this->bonus_money_3) {
            $this->setBonus($this->bonus_money_1, $this->bonus_money_2, $this->bonus_money_3, $max, $link, $userEnemy);
        }
    }

    //////////////////// TECHNO /////////////////////

    public function techoSetBonus($card, $link, &$userEnemy) {
        if ($this->isCheckBonus($this->bonus_techo_1, $this->bonus_techo_2, $this->bonus_techo_3, $card) == -1) {
            $this->setBonus($this->bonus_techo_1, $this->bonus_techo_2, $this->bonus_techo_3, $card, $link, $userEnemy);
        }
    }

    public function techoRemEnemyOneBonus($card, $link, &$userEnemy) {
        $max = $this->remOneEnemyBonus($userEnemy->bonus_techo_1, $userEnemy->bonus_techo_2, $userEnemy->bonus_techo_3, $link, $userEnemy);
    }

    public function techoRemTwoBonus($card, $link, &$userEnemy) {
        $this->remTwoBonus($userEnemy->bonus_techo_1, $userEnemy->bonus_techo_2, $userEnemy->bonus_techo_3, $link, $userEnemy);
    }

    public function techoMaxEnemyUser($card, $link, &$userEnemy) {
        $max = $this->remOneEnemyBonus($userEnemy->bonus_techo_1, $userEnemy->bonus_techo_2, $userEnemy->bonus_techo_3, $link, $userEnemy);
        if ($max == EMPTY_CARD || $max != 0 && $max != $this->bonus_techo_1 && $max != $this->bonus_techo_2 && $max != $this->bonus_techo_3) {
            $this->setBonus($this->bonus_techo_1, $this->bonus_techo_2, $this->bonus_techo_3, $max, $link, $userEnemy);
        }
    }

    //////////////////// UNITS ///////////////////////

    public function unitsSetBonus($card, $link, &$userEnemy) {
        if ($this->isCheckBonus($this->bonus_units_1, $this->bonus_money_2, $this->bonus_units_3, $card) == -1) {
            $this->setBonus($this->bonus_units_1, $this->bonus_units_2, $this->bonus_units_3, $card, $link, $userEnemy);
        }
    }

    public function unitsRemEnemyOneBonus($card, $link, &$userEnemy) {
        $max = $this->remOneEnemyBonus($userEnemy->bonus_units_1, $userEnemy->bonus_units_2, $userEnemy->bonus_units_3, $link, $userEnemy);
    }

    public function unitsRemTwoBonus($card, $link, &$userEnemy) {
        $this->remTwoBonus($userEnemy->bonus_units_1, $userEnemy->bonus_units_2, $userEnemy->bonus_units_3, $link, $userEnemy);
    }

    public function unitsMaxEnemyUser($card, $link, &$userEnemy) {

        $max = $this->remOneEnemyBonus($userEnemy->bonus_units_1, $userEnemy->bonus_units_2, $userEnemy->bonus_units_3, $link, $userEnemy);

        if ($max == EMPTY_CARD || $max != 0 && $max != $this->bonus_units_1 && $max != $userEnemy->bonus_units_2 && $max != $userEnemy->bonus_units_3) {
            $this->setBonus($this->bonus_units_1, $this->bonus_units_2, $this->bonus_units_3, $max, $link, $userEnemy);
        }
    }

    ////////// INIT ARRAY ///////////////////

    public function &initAtlass($link, &$atlas) {
        $cards = array();

        for ($i = 0; $i < 10; ++$i) {
            $cardBuf = new Card();
            $this->getCard($cardBuf, $atlas[$i], $link);
            array_push($cards, $cardBuf);
        }
        return $cards;
    }

    ////////////////////// DEFAULT REMOVE MAX ENEMY BONUS /////////////////////

    public function &remOneEnemyBonus(&$bonus_1, &$bonus_2, &$bonus_3, $link, &$cardEnemy) {

        if ($bonus_1 == EMPTY_CARD) {
            $bonus_1 = 0;
        }
        if ($bonus_2 == EMPTY_CARD) {
            $bonus_2 = 0;
        }
        if ($bonus_3 == EMPTY_CARD) {
            $bonus_3 = 0;
        }

        $cardSQLBuf = new Card();
        $max = 0;

        if ($bonus_1 >= $bonus_2) {

            if ($bonus_1 > $bonus_3) {
                $max = $bonus_1;
            }
            if ($bonus_1 < $bonus_3) {
                $max = $bonus_3;
            }
            if ($bonus_1 == $bonus_3) {
                $max = $bonus_1;
            }
        } else {
            if ($bonus_2 >= $bonus_3) {
                $max = $bonus_2;
            }
            if ($bonus_2 < $bonus_3) {
                $max = $bonus_3;
            }
        }
        if ($max != 0 && $max != EMPTY_CARD) {
            $this->getCard($cardSQLBuf, $max, $link);
            $this->remEnemyBonusRes($cardEnemy, $cardSQLBuf);
        }


        if ($bonus_1 == 0) {
            $bonus_1 = EMPTY_CARD;
        }
        if ($bonus_2 == 0) {
            $bonus_2 = EMPTY_CARD;
        }
        if ($bonus_3 == 0) {
            $bonus_3 = EMPTY_CARD;
        }

        return $max;
    }

    ////////////////////// DEFAULT REMOVE TWO MAX BONUS /////////////////////// 

    public function remTwoBonus(&$bonus_1, &$bonus_2, &$bonus_3, $link, &$cardEnemy) {
        if ($bonus_1 == EMPTY_CARD) {
            $bonus_1 = 0;
        }
        if ($bonus_2 == EMPTY_CARD) {
            $bonus_2 = 0;
        }
        if ($bonus_3 == EMPTY_CARD) {
            $bonus_3 = 0;
        }

        $cardSQLBuf = new Card();

        $cards = array();
        array_push($cards, $bonus_1);
        array_push($cards, $bonus_2);
        array_push($cards, $bonus_3);

        rsort($cards);
        
        $max1 = $cards[0];
        $max2 = $cards[1];

        if ($max1 != 0) {
            $this->getCard($cardSQLBuf, $max1, $link);
            $this->remEnemyBonusRes($cardEnemy, $cardSQLBuf);
            $max1 = EMPTY_CARD;
        }
        if ($max2 != 0) {
            $this->getCard($cardSQLBuf, $max2, $link);
            $this->remEnemyBonusRes($cardEnemy, $cardSQLBuf);
            $max2 = EMPTY_CARD;
        }

        if ($bonus_1 == 0) {
            $bonus_1 = EMPTY_CARD;
        }
        if ($bonus_2 == 0) {
            $bonus_2 = EMPTY_CARD;
        }
        if ($bonus_3 == 0) {
            $bonus_3 = EMPTY_CARD;
        }
    }

    public function getCard(&$cardBuf, $card, $link) {
        $collectionCards = $link->selectCollection(TABLE_CARDS);
        $query = array('_id' => new MongoInt32($card));
        $cursor = $collectionCards->find($query);

        foreach ($cursor as $row) {
            $cardBuf->initCard($row);
        }
    }

}
