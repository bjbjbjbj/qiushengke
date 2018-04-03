<?php
/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/3/1
 * Time: 11:50
 */

namespace App\Http\Controllers\Statistic\Terminal\Tool;

use App\Models\AnalyseModels\BasketMatch;
use App\Models\AnalyseModels\Match;
use App\Models\LiaoGouModels\MatchLive;

class MatchQueryTool
{
    public static function getMatchQueryBySport($sport = MatchLive::kSportFootball) {
        if ($sport == MatchLive::kSportBasketball) {
            $query = BasketMatch::from('basket_matches as m');
        } else {
            $query = Match::from("matches as m");
        }
        return $query;
    }

    public static function getDBPreStrBySport($sport = MatchLive::kSportFootball) {
        if ($sport == MatchLive::kSportBasketball) {
            $DBStrPre = "basket_";
        } else {
            $DBStrPre = "";
        }
        return $DBStrPre;
    }

    public static function onDiffSelectBySport($query, $sport = MatchLive::kSportFootball) {
        if ($sport == MatchLive::kSportBasketball) {
            $query->addSelect('m.hscore_1st','m.ascore_1st', 'm.hscore_2nd','m.ascore_2nd',
                    'm.hscore_3rd','m.ascore_3rd', 'm.hscore_4th','m.ascore_4th',
                    'm.h_ot','m.a_ot');
        } else {
            $query->addSelect('m.hscorehalf','m.ascorehalf');
        }
        return $query;
    }

    public static function onMatchCommonSelect($query) {
        $query->addSelect('m.id', 'm.id as mid','m.hid','m.aid', 'm.time','m.status','m.lid',
            'm.hscore','m.ascore', 'm.hname','m.aname');
        return $query;
    }

    public static function onMatchLeagueLeftJoin($query, $sport = MatchLive::kSportFootball) {
        $DBStrPre = self::getDBPreStrBySport($sport);
        $query->leftJoin($DBStrPre.'leagues as l', 'm.lid', '=', 'l.id')
            ->addSelect('l.name as league', 'l.color as color');
        return $query;
    }

    public static function onMatchOddLeftJoin($query, $cid, $sport = MatchLive::kSportFootball, $typeArray = [1,2,3]) {
        $DBStrPre = self::getDBPreStrBySport($sport);
        if (in_array(1, $typeArray)) {
            $query->leftJoin($DBStrPre . 'odds as asia', function ($join) use ($cid) {
                $join->on('m.id', '=', 'asia.mid');
                $join->where('asia.cid', $cid)->where('asia.type', 1);
            })->addSelect(
                'asia.up1 as asiaup1', 'asia.middle1 as asiamiddle1', 'asia.down1 as asiadown1',
                'asia.up2 as asiaup2', 'asia.middle2 as asiamiddle2', 'asia.down2 as asiadown2');
        }
        if (in_array(2, $typeArray)) {
            $query->leftJoin($DBStrPre . 'odds as goal', function ($join) use ($cid) {
                $join->on('m.id', '=', 'goal.mid');
                $join->where('goal.cid', $cid)->where('goal.type', 2);
            })->addSelect('goal.up1 as goalup1', 'goal.middle1 as goalmiddle1', 'goal.down1 as goaldown1',
                'goal.up2 as goalup2', 'goal.middle2 as goalmiddle2', 'goal.down2 as goaldown2');
        }
        if (in_array(3, $typeArray)) {
            $query->leftJoin($DBStrPre . 'odds as ou', function ($join) use ($cid) {
                $join->on('m.id', '=', 'ou.mid');
                $join->where('ou.cid', $cid)->where('ou.type', 3);
            })->addSelect('ou.up1 as ouup1', 'ou.middle1 as oumiddle1', 'ou.down1 as oudown1',
                    'ou.up2 as ouup2', 'ou.middle2 as oumiddle2', 'ou.down2 as oudown2');
        }
        if (in_array(4, $typeArray)) {
            $query->leftJoin($DBStrPre . 'odds as corner', function ($join) use ($cid) {
                $join->on('m.id', '=', 'corner.mid');
                $join->where('corner.cid', $cid)->where('corner.type', 4);
            })->addSelect('corner.up1 as cornerup1', 'corner.middle1 as cornermiddle1', 'corner.down1 as cornerdown1',
                'corner.up2 as cornerup2', 'corner.middle2 as cornermiddle2', 'corner.down2 as cornerdown2');
        }
        return $query;
    }
}