<?php

class User_statistic {

    public $id;
    public $game_only;
    public $game_win;
    public $game_lose;
    public $game_lose_win;
    public $total;
    public $coins;
    public $fraction;
    public $isStoneWall;
    public $isBuilder1;
    public $isBuilder2;
    public $isBuilder3;
    public $isBuilder4;
    public $isAppomattox;
    public $isTurtle;
    public $isMenacingLook;
    public $isPatient;
    public $isMedusa;
    public $isDavid;
    public $isBully;
    public $isCollector1;
    public $isCollector2;
    public $isCollector3;
    public $isCollector4;
    public $isMcClellan1;
    public $isMcClellan2;
    public $isMcClellan3;
    public $isMcClellan4;
    public $isGrant1;
    public $isGrant2;
    public $isGrant3;
    public $isGrant4;

    public function printString(&$user) {
        echo "id='$user->id'
		game_only='$user->game_only'
		game_win='$user->game_win'
		game_lose='$user->game_lose'
		game_lose_win='$user->game_lose_win'
		total='$user->total'
		coins='$user->coins'
		south_north='$user->fraction' ";
        echo nl2br("\n\n");
    }

    public function printStr() {
        echo "id='$this->id'
		game_only='$this->game_only'
		game_win='$this->game_win'
		game_lose='$this->game_lose'
		game_lose_win='$this->game_lose_win'
		total='$this->total'
		coins='$this->coins'
		south_north='$this->fraction' ";
        echo nl2br("\n\n");
    }

}
