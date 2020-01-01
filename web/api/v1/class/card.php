<?php

class Card {

    public $content;
    public $name;
    //////////////
    public $atack;
    public $again;
    public $esc;
    public $row_tow;
    public $cost_units;
    public $cost_zavod;
    public $cost_money;
    public $id;
    public $money;
    public $units;
    public $zavod;
    public $money_in;
    public $zavod_in;
    public $money_en;
    public $zavod_en;
    public $units_in;
    public $units_en;
    public $money_en_in;
    public $zavod_en_in;
    public $units_en_in;
    public $at_def_pl_tow;
    public $at_def_pl_rov;
    public $at_def_en_tow;
    public $at_def_en_rov;
    public $back_image;

    public function initCard($row) {

        $this->id = $row["id_old"];
        $this->content = $row["main_rus"];
        $this->name = $row["name_rus"];
        $this->atack = isset($row["atack"]) ? $row["atack"] : 0;
        $this->again = isset($row["again"]) ? $row["again"] : 0;
        $this->esc = isset($row["esc"]) ? $row["esc"] : 0;
        $this->row_tow = isset($row["row_tow"]) ? $row["row_tow"] : 0;
        $this->cost_units = isset($row["cost_units"]) ? $row["cost_units"] : 0;
        $this->cost_zavod = isset($row["cost_zavod"]) ? $row["cost_zavod"] : 0;
        $this->cost_money = isset($row["cost_money"]) ? $row["cost_money"] : 0;
        $this->money = isset($row["money"]) ? $row["money"] : 0;
        $this->units = isset($row["units"]) ? $row["units"] : 0;
        $this->zavod = isset($row["zavod"]) ? $row["zavod"] : 0;
        $this->money_in = isset($row["money_in"]) ? $row["money_in"] : 0;
        $this->zavod_in = isset($row["zavod_in"]) ? $row["zavod_in"] : 0;
        $this->units_in = isset($row["units_in"]) ? $row["units_in"] : 0;
        $this->money_en = isset($row["moey_en"]) ? $row["moey_en"] : 0;
        $this->zavod_en = isset($row["zavod_en"]) ? $row["zavod_en"] : 0;
        $this->units_en = isset($row["units_en"]) ? $row["units_en"] : 0;
        $this->money_en_in = isset($row["money_en_in"]) ? $row["money_en_in"] : 0;
        $this->zavod_en_in = isset($row["zavod_en_in"]) ? $row["zavod_en_in"] : 0;
        $this->units_en_in = isset($row["units_en_in"]) ? $row["units_en_in"] : 0;
        $this->at_def_pl_tow = isset($row["at_def_pl_tow"]) ? $row["at_def_pl_tow"] : 0;
        $this->at_def_pl_rov = isset($row["at_def_pl_rov"]) ? $row["at_def_pl_rov"] : 0;
        $this->at_def_en_tow = isset($row["at_def_en_tow"]) ? $row["at_def_en_tow"] : 0;
        $this->at_def_en_rov = isset($row["at_def_en_rov"]) ? $row["at_def_en_rov"] : 0;
        $this->back_image = isset($row["back_image"]) ? $row["back_image"] : 0;
    }

    public function printString() {
        echo " atack='$this->atack'
 again='$this->again'
 esc='$this->esc'
 row_tow='$this->row_tow'
 cost_units='$this->cost_units'
 cost_zavod='$this->cost_zavod'
 cost_money='$this->cost_money'
 id='$this->id'
 money='$this->money'
 units='$this->units'
 zavod='$this->zavod'
 money_in='$this->money_in'
 zavod_in='$this->zavod_in'
 money_en='$this->money_en'
 zavod_en='$this->zavod_en'
 units_in='$this->units_in'
 units_en='$this->units_en'
 money_en_in='$this->money_en_in'
 zavod_en_in='$this->zavod_en_in'
 units_en_in='$this->units_en_in'
 at_def_pl_tow='$this->at_def_pl_tow'
 at_def_pl_rov='$this->at_def_pl_rov'
 at_def_en_tow='$this->at_def_en_tow'
 at_def_en_rov='$this->at_def_en_rov'
 back_image='$this->back_image' ";

        echo nl2br("\n\n");
    }

}
