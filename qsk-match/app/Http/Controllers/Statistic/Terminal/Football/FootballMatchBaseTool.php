<?php
/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/2/28
 * Time: 17:45
 */

namespace App\Http\Controllers\Statistic\Terminal\Football;


use App\Http\Controllers\Statistic\MatchLiveTool;
use App\Http\Controllers\Statistic\StatisticFileTool;
use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\MatchLive;
use App\Models\LiaoGouModels\Odd;

trait FootballMatchBaseTool
{
    //比赛基本信息
    private function match($id, $isRest = false) {
        $match = Match::from("matches as m")
            ->join('leagues', 'lid', '=', 'leagues.id')
            ->addSelect('m.id as mid', 'm.hid', 'm.aid', 'leagues.name as league')
            ->leftjoin('odds as asia', function ($join) {
            $join->on('m.id', '=', 'asia.mid');
            $join->where('asia.type', '=', Odd::k_odd_type_asian);
            $join->where('asia.cid', '=', Odd::default_calculate_cid);
            })->leftjoin('odds as ou', function ($join) {
                $join->on('m.id', '=', 'ou.mid');
                $join->where('ou.type', '=', Odd::k_odd_type_europe);
                $join->where('ou.cid', '=', Odd::default_calculate_cid);
            })->leftjoin('odds as goal', function ($join) {
                $join->on('m.id', '=', 'goal.mid');
                $join->where('goal.type', '=', Odd::k_odd_type_ou);
                $join->where('goal.cid', '=', Odd::default_calculate_cid);
            })->leftjoin('odds as corner', function ($join) {
                $join->on('m.id', '=', 'corner.mid');
                $join->where('corner.type', '=', Odd::k_odd_type_corner);
                $join->where('corner.cid', '=', Odd::default_banker_id);
            })
            ->addSelect('asia.up1 as asiaup1', 'asia.middle1 as asiamiddle1', 'asia.down1 as asiadown1',
                'asia.up2 as asiaup2', 'asia.middle2 as asiamiddle2', 'asia.down2 as asiadown2',
                'ou.up1 as ouup1', 'ou.middle1 as oumiddle1', 'ou.down1 as oudown1',
                'ou.up2 as ouup2', 'ou.middle2 as oumiddle2', 'ou.down2 as oudown2',
                'goal.up1 as goalup1', 'goal.middle1 as goalmiddle1', 'goal.down1 as goaldown1',
                'goal.up2 as goalup2', 'goal.middle2 as goalmiddle2', 'goal.down2 as goaldown2',
                'corner.up1 as cornerup1', 'corner.middle1 as cornermiddle1', 'corner.down1 as cornerdown1',
                'corner.up2 as cornerup2', 'corner.middle2 as cornermiddle2', 'corner.down2 as cornerdown2')
            ->leftJoin("teams as home", "m.hid", "home.id")
            ->leftJoin("teams as away", "m.aid", "away.id")
            ->addSelect('m.lid', 'm.status', 'm.genre','m.betting_num',
                'm.time', 'm.timehalf', 'm.hname','m.aname',
                'm.round', 'm.hrank', 'm.arank',
                'm.hscore','m.ascore', 'm.hscorehalf','m.ascorehalf')
            ->addSelect("home.icon as hicon", "away.icon as aicon")
            ->where('m.id', $id)->first();

        if (isset($match)) {
            $match->sport = MatchLive::kSportFootball;
            $match->current_time = $match->getCurMatchTime(true);
            $match->time = strtotime($match->time);
            if (isset($match->timehalf)) {
                $match->timehalf = strtotime($match->timehalf);
            } else {
                $match->timehalf = $match->time;
            }
            $match->statusStr = $match->getStatusText();

            $liveCountArray = MatchLiveTool::getMatchLiveCountById($id, MatchLive::kSportFootball);
            $match->live = $liveCountArray['live'];
            $match->pc_live = $liveCountArray['pc_live'];

            if (!$isRest) {
                StatisticFileTool::putFileToTerminal($match, MatchLive::kSportFootball, $id, 'match');
            }
        }
        return $match;
    }
}