<?php

include '../class/ach.php';

class updateArchiviment {

    public function getLastAch($link, $parent) {

        $collectionUser = $link->selectCollection(TABLE_ACH_GAME);
        $query = array('_id' => $parent);
        $cursor = $collectionUser->find($query);

        $achParent = array();

        foreach ($cursor as $row) {
            $achParent = array('isStoneWall' => $row['isStoneWall'] ? 1 : 0,
                'isAppomattox' => $row['isAppomattox'] ? 1 : 0,
                'isTurtle' => $row['isTurtle'] ? 1 : 0,
                'isPatient' => $row['isPatient'] ? 1 : 0,
                'isMenacingLook' => $row['isMenacingLook'] ? 1 : 0,
                'isMedusa' => $row['isMedusa'] ? 1 : 0,
                'isBully' => $row['isBully'] ? 1 : 0,
                'isDavid' => $row['isDavid'] ? 1 : 0,
                'levelBuilder' => $row['levelBuilder'],
                'levelMcClellan' => $row['levelMcClellan'],
                'levelCollector' => $row['levelCollector'],
                'levelGrant' => $row['levelGrant']);
            break;
        }

        return $achParent;
    }

    private function getAch($link, $parent) {

        $collectionUser = $link->selectCollection(TABLE_ACH_GAME);
        $query = array('_id' => $parent);
        $cursor = $collectionUser->find($query);

        $achParent = new Ach();

        foreach ($cursor as $row) {
            $achParent->isStoneWall = $row['isStoneWall'];
            $achParent->isAppomattox = $row['isAppomattox'];
            $achParent->isTurtle = $row['isTurtle'];
            $achParent->isPatient = $row['isPatient'];
            $achParent->isMenacingLook = $row['isMenacingLook'];
            $achParent->isMedusa = $row['isMedusa'];
            $achParent->isBully = $row['isBully'];
            $achParent->isMcClellan = $row['isMcClellan'];
            $achParent->isCollector = $row['isCollector'];
            $achParent->isGrant = $row['isGrant'];
            $achParent->isDavid = $row['isDavid'];
            $achParent->isBuilder = $row['isBuilder'];
            $achParent->levelBuilder = $row['levelBuilder'];
            $achParent->levelMcClellan = $row['levelMcClellan'];
            $achParent->levelCollector = $row['levelCollector'];
            $achParent->levelGrant = $row['levelGrant'];
            break;
        }

        return $achParent;
    }

    public function update($link, &$parent, &$child, &$isWhoBully, &$progress, &$isChildTurn) {

        $childWin = ($progress == WINNER_CHILD || $progress == RESIGN_PARENT || $progress == TIME_OUT_PARENT) && $progress != WINNER_LOSSER;
        $parentWin = ($progress == WINNER_PARENT || $progress == RESIGN_CHILD || $progress == TIME_OUT_CHILD) && $progress != WINNER_LOSSER;

        $result = array();

        $achParent = $this->getAch($link, $parent->id);
        $achChild = $this->getAch($link, $child->id);

        $isBot = strcasecmp($parent->id, $child->id) == 0;

        $isBP = $achParent->isBuilder < $parent->row ? $parent->row : $achParent->isBuilder;
        $isBPL = 0;
        if ($isBP >= PETER_4) {
            $isBPL = 4;
        }
        if ($isBP < PETER_4 && $isBP >= PETER_3) {
            $isBPL = 3;
        }
        if ($isBP < PETER_3 && $isBP >= PETER_2) {
            $isBPL = 2;
        }
        if ($isBP < PETER_2 && $isBP >= PETER_1) {
            $isBPL = 1;
        }

        $isMP = $achParent->isMcClellan + ($isChildTurn ? 0 : 1);
        $isMPL = 0;

        if ($parentWin || $progress == GAME_PLAY) {

            if ($isMP >= MAC_CL_4) {
                $isMPL = 4;
            }
            if ($isMP < MAC_CL_4 && $isMP >= MAC_CL_3) {
                $isMPL = 3;
            }
            if ($isMP < MAC_CL_3 && $isMP >= MAC_CL_2) {
                $isMPL = 2;
            }
            if ($isMP < MAC_CL_2 && $isMP >= MAC_CL_1) {
                $isMPL = 1;
            }
        }

        $maxRes = $parent->money > $parent->units ? $parent->money : $parent->units;
        $maxResourse = $maxRes > $parent->techo ? $maxRes : $parent->techo;
        $isCP = $achParent->isCollector < $maxResourse ? $maxResourse : $achParent->isCollector;
        $isCPL = 0;
        if ($isCP >= HAUP_4) {
            $isCPL = 4;
        }
        if ($isCP < HAUP_4 && $isCP >= HAUP_3) {
            $isCPL = 3;
        }
        if ($isCP < HAUP_3 && $isCP >= HAUP_2) {
            $isCPL = 2;
        }
        if ($isCP < HAUP_2 && $isCP >= HAUP_1) {
            $isCPL = 1;
        }

        $isGP = $achParent->isGrant + ($isChildTurn ? 0 : 1);
        $isGPL = 0;
        if ($parentWin || $progress == GAME_PLAY) {
            if ($isGP <= GRANT_1 && $isGP >= GRANT_2) {
                $isGPL = 1;
            }
            if ($isGP < GRANT_2 && $isGP >= GRANT_3) {
                $isGPL = 2;
            }
            if ($isGP < GRANT_3 && $isGP >= GRANT_4) {
                $isGPL = 3;
            }
            if ($isGP < GRANT_4) {
                $isGPL = 4;
            }
        }

        $isWall = $achParent->isStoneWall;

        if ($isWall) {
            $isWall = $parent->row > 0;
        }

///////////////  Create Achiviments CACHE ///////////////////////

        $achivimentsP = array('isStoneWall' => $isWall,
            'isAppomattox' => $achParent->isAppomattox,
            'isTurtle' => $achParent->isTurtle,
            'isPatient' => $achParent->isPatient,
            'isMenacingLook' => $achParent->isMenacingLook,
            'isMedusa' => $achParent->isMedusa,
            'isBully' => $isBot || $progress == WINNER_LOSSER || $childWin  ? FALSE : $progress != GAME_PLAY && strcasecmp($parent->id, $isWhoBully) == 0 ? TRUE : $achParent->isBully,
            'isDavid' => $isBot || $progress == WINNER_LOSSER || $childWin ? FALSE : $progress != GAME_PLAY && strcasecmp($child->id, $isWhoBully) == 0 ? TRUE : $achParent->isDavid,
            'isBuilder' => $isBP,
            'levelBuilder' => $isBPL,
            'isMcClellan' => $isMP,
            'levelMcClellan' => $isMPL,
            'isCollector' => $isCP,
            'levelCollector' => $isCPL,
            'isGrant' => $isGP,
            'levelGrant' => $isGPL);


        $collectionAch = $link->selectCollection(TABLE_ACH_GAME);
        $query = array('_id' => $parent->id);
        $setQuery = array(
            '$set' => $achivimentsP
        );
        $collectionAch->update($query, $setQuery);

/////////////////////////////////////////////////////////////////////

        $result["parent"] = $achivimentsP;


        if (strcmp($parent->id, $child->id) != 0) {

            $isBC = $achChild->isBuilder < $child->row ? $child->row : $achChild->isBuilder;
            $isBCL = 0;
            if ($isBC >= PETER_4) {
                $isBCL = 4;
            }
            if ($isBC < PETER_4 && $isBC >= PETER_3) {
                $isBCL = 3;
            }
            if ($isBC < PETER_3 && $isBC >= PETER_2) {
                $isBCL = 2;
            }
            if ($isBC < PETER_2 && $isBC >= PETER_1) {
                $isBCL = 1;
            }

            $isMC = $achChild->isMcClellan + ($isChildTurn ? 1 : 0);
            $isMCL = 0;
            if ($childWin || $progress == GAME_PLAY) {
                if ($isMC >= MAC_CL_4) {
                    $isMCL = 4;
                }
                if ($isMC < MAC_CL_4 && $isMC >= MAC_CL_3) {
                    $isMCL = 3;
                }
                if ($isMC < MAC_CL_3 && $isMC >= MAC_CL_2) {
                    $isMCL = 2;
                }
                if ($isMC < MAC_CL_2 && $isMC >= MAC_CL_1) {
                    $isMCL = 1;
                }
            }
            $maxResC = $child->money > $child->units ? $child->money : $child->units;
            $maxResourseC = $maxResC > $child->techo ? $maxResC : $child->techo;
            $isCC = $achChild->isCollector < $maxResourseC ? $maxResourseC : $achChild->isCollector;
            $isCCL = 0;
            if ($isCC >= HAUP_4) {
                $isCCL = 4;
            }
            if ($isCC < HAUP_4 && $isCC >= HAUP_3) {
                $isCCL = 3;
            }
            if ($isCC < HAUP_3 && $isCC >= HAUP_2) {
                $isCCL = 2;
            }
            if ($isCC < HAUP_2 && $isCC >= HAUP_1) {
                $isCCL = 1;
            }

            $isGC = $achChild->isGrant + ($isChildTurn ? 1 : 0);
            $isGCL = 0;
            if ($childWin || $progress == GAME_PLAY) {
                if ($isGC <= GRANT_1 && $isGC >= GRANT_2) {
                    $isGCL = 1;
                }
                if ($isGC < GRANT_2 && $isGC >= GRANT_3) {
                    $isGCL = 2;
                }
                if ($isGC < GRANT_3 && $isGC >= GRANT_4) {
                    $isGCL = 3;
                }
                if ($isGC < GRANT_4) {
                    $isGCL = 4;
                }
            }


            $isWall = $achChild->isStoneWall;

            if ($isWall) {
                $isWall = $child->row > 0;
            }


            $achivimentsC = array('isStoneWall' => $isWall,
                'isAppomattox' => $achChild->isAppomattox,
                'isTurtle' => $achChild->isTurtle,
                'isPatient' => $achChild->isPatient,
                'isMenacingLook' => $achChild->isMenacingLook,
                'isMedusa' => $achChild->isMedusa,
                'isBully' => $isBot || $progress == WINNER_LOSSER || $parentWin ? FALSE : $progress != GAME_PLAY && strcasecmp($child->id, $isWhoBully) == 0 && empty($isWhoBully) ? TRUE : $achChild->isBully,
                'isDavid' => $isBot || $progress == WINNER_LOSSER || $parentWin ? FALSE : $progress != GAME_PLAY && strcasecmp($parent->id, $isWhoBully) != 0 && empty($isWhoBully) ? TRUE : $achChild->isDavid,
                'isBuilder' => $isBC,
                'levelBuilder' => $isBCL,
                'isMcClellan' => $isMC,
                'levelMcClellan' => $isMCL,
                'isCollector' => $isCC,
                'levelCollector' => $isCCL,
                'isGrant' => $isGC,
                'levelGrant' => $isGCL);

            $collectionAch = $link->selectCollection(TABLE_ACH_GAME);
            $query = array('_id' => $child->id);
            $setQuery = array(
                '$set' => $achivimentsC
            );
            $collectionAch->update($query, $setQuery);

            $result["child"] = $achivimentsC;
        }
        return $result;
    }

    public function removeGame(&$link, &$isWhoBully, &$parentId, &$childId, &$progress) {


        $achParent = $this->getAch($link, $parentId);
        $achChild = $this->getAch($link, $childId);

        $result = array();

        $parentWin = $progress == WINNER_PARENT || $progress == RESIGN_CHILD || $progress == TIME_OUT_CHILD ? TRUE : FALSE;
        $childWin = $progress == WINNER_CHILD || $progress == RESIGN_PARENT || $progress == TIME_OUT_PARENT ? TRUE : FALSE;

        $isBot = strcasecmp($parentId, $childId) == 0 ? TRUE : FALSE;

        $isParentBully = strcasecmp($parentId, $isWhoBully) == 0 ? TRUE : FALSE;
        $isChildBully = strcasecmp($childId, $isWhoBully) == 0 ? TRUE : FALSE;

        /*
        echo '  $progress->' . $progress;
        echo '  $isParentBully->' . $isParentBully;
        echo '  $isChildBully->' . $isChildBully;
        echo '  $parentWin->' . $parentWin;
        echo '  $childWin->' . $childWin;
        echo '  $parentId->' . $parentId;
        echo '  $childId->' . $childId;
        */

        $achivimentsP = array(
            'isStoneWall' => $achParent->isMcClellan > 15 && $achParent->isStoneWall ? TRUE : FALSE,
            /////////////////////////////////////////////////////////////
            'isPatient' => $achParent->isPatient,
            'isCollector' => $achParent->isCollector,
            'levelCollector' => $achParent->levelCollector,
            /////////////////////////////////////////////////////////////
            'isAppomattox' => $progress == RESIGN_PARENT ? TRUE : FALSE,
            'isTurtle' => $progress == TIME_OUT_PARENT ? TRUE : FALSE,
            'isMenacingLook' => $achParent->isMcClellan > MIN_TURN &&  $progress == RESIGN_CHILD ? TRUE : FALSE,
            'isMedusa' => $achParent->isMcClellan > MIN_TURN && $progress == TIME_OUT_CHILD ? TRUE : FALSE,
            /////////////////////////////////////////////////////////////
            'isBully' => $isBot || $progress == WINNER_LOSSER? FALSE : $achParent->isMcClellan > MIN_TURN && $parentWin && $isParentBully  ? TRUE : FALSE,
            'isDavid' => $isBot || $progress == WINNER_LOSSER? FALSE : $achParent->isMcClellan > MIN_TURN && $parentWin && $isChildBully ? TRUE : FALSE,
            /////////////////////////////////////////////////////////////
            'isBuilder' => $parentWin ? $achParent->isBuilder : FALSE,
            'levelBuilder' => $parentWin ? $achParent->levelBuilder : 0,
            /////////////////////////////////////////////////////////////
            'isMcClellan' => $parentWin ? $achParent->isMcClellan : FALSE,
            'levelMcClellan' => $parentWin ? $achParent->levelMcClellan : 0,
            /////////////////////////////////////////////////////////////
            'isGrant' => FALSE, //ПРИ СДАЧЕ  ГРАНТА НЕ ДАЮТ
            'levelGrant' => 0);


        $collectionAch = $link->selectCollection(TABLE_ACH_GAME);
        $query = array('_id' => ($parentId));
        $setQuery = array(
            '$set' => $achivimentsP
        );
        $collectionAch->update($query, $setQuery);

        $result["parent"] = $achivimentsP;


        if (strcmp($parentId, $childId) != 0) {

            $achivimentsC = array(
                'isStoneWall' => $achChild->isMcClellan > 15 && $achChild->isStoneWall ? TRUE : FALSE,
                'isPatient' => $achChild->isPatient,
                'isCollector' => $achChild->isCollector,
                'levelCollector' => $achChild->levelCollector,
                /////////////////////////////////////////////////////////////
                'isAppomattox' => $progress == RESIGN_CHILD ? TRUE : FALSE,
                'isTurtle' => $progress == TIME_OUT_CHILD ? TRUE : FALSE,
                'isMenacingLook' => $achChild->isMcClellan > 5 && $progress == RESIGN_PARENT ? TRUE : FALSE,
                'isMedusa' =>$achChild->isMcClellan > MIN_TURN &&  $progress == TIME_OUT_PARENT ? TRUE : FALSE,
                /////////////////////////////////////////////////////////////
                'isBully' => $achChild->isMcClellan > MIN_TURN && $childWin && $isChildBully ? TRUE : FALSE,
                'isDavid' => $achChild->isMcClellan > MIN_TURN && $childWin && $isParentBully ? TRUE : FALSE,
                /////////////////////////////////////////////////////////////
                'isBuilder' => $childWin ? $achChild->isBuilder : FALSE,
                'levelBuilder' => $childWin ? $achChild->levelBuilder : 0,
                /////////////////////////////////////////////////////////////
                'isMcClellan' => $childWin ? $achChild->isMcClellan  : FALSE,
                'levelMcClellan' => $childWin ? $achParent->levelMcClellan :  0,
                ///////////////////////////////////////////////////////
                'isGrant' => FALSE, //ПРИ СДАЧЕ  ГРАНТА НЕ ДАЮТ
                'levelGrant' => 0);

            $collectionAch = $link->selectCollection(TABLE_ACH_GAME);
            $query = array('_id' => ($childId));
            $setQuery = array(
                '$set' => $achivimentsC
            );
            $collectionAch->update($query, $setQuery);

            $result["child"] = $achivimentsC;
        }
        return $result;
    }

    public function initAchResponse($link, $idUser) {


        $collectionUser = $link->selectCollection(TABLE_USER);
        $query = array('_id' => new MongoId($idUser));
        $cursor = $collectionUser->find($query);

        $achv = array();

        foreach ($cursor as $row) {
            $achv["isStoneWall"] = $row["isStoneWall"];
            $achv["isAppomattox"] = $row["isAppomattox"];
            $achv["isTurtle"] = $row["isTurtle"];
            $achv["isMenacingLook"] = $row["isMenacingLook"];
            $achv["isPatient"] = $row["isPatient"];
            $achv["isMedusa"] = $row["isMedusa"];
            $achv["isDavid"] = $row["isDavid"];
            $achv["isBully"] = $row["isBully"];

            $achv["isBuilder1"] = $row["isBuilder1"];
            $achv["isBuilder2"] = $row["isBuilder2"];
            $achv["isBuilder3"] = $row["isBuilder3"];
            $achv["isBuilder4"] = $row["isBuilder4"];

            $achv["isCollector1"] = $row["isCollector1"];
            $achv["isCollector2"] = $row["isCollector2"];
            $achv["isCollector3"] = $row["isCollector3"];
            $achv["isCollector4"] = $row["isCollector4"];

            $achv["isMcClellan1"] = $row["isMcClellan1"];
            $achv["isMcClellan2"] = $row["isMcClellan2"];
            $achv["isMcClellan3"] = $row["isMcClellan3"];
            $achv["isMcClellan4"] = $row["isMcClellan4"];

            $achv["isGrant1"] = $row["isGrant1"];
            $achv["isGrant2"] = $row["isGrant2"];
            $achv["isGrant3"] = $row["isGrant3"];
            $achv["isGrant4"] = $row["isGrant4"];
        }

        return $achv;
    }

}
