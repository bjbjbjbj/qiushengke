<?php
/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/2/28
 * Time: 19:46
 */

namespace App\Http\Controllers\Statistic\Terminal\Basketball;


use App\Http\Controllers\Statistic\MatchLiveTool;
use App\Http\Controllers\Statistic\StatisticFileTool;
use App\Models\LiaoGouModels\BasketMatch;
use App\Models\LiaoGouModels\BasketTeam;
use App\Models\LiaoGouModels\Odd;
use App\Models\LiaoGouModels\MatchLive;
use App\Models\LiaoGouModels\MatchLiveChannel;

trait BasketMatchBaseTool
{
    //比赛基本信息
    public function match($id, $isReset = false) {
        $match = BasketMatch::from("basket_matches as m")
            ->leftjoin('basket_leagues', 'lid', '=', 'basket_leagues.id')
            ->addSelect('m.id as mid', 'm.hid', 'm.aid', 'basket_leagues.name as league',
                'basket_leagues.hot', 'basket_leagues.system')
            ->leftjoin('basket_odds as asia', function ($join) {
                $join->on('m.id', '=', 'asia.mid');
                $join->where('asia.type', '=', Odd::k_odd_type_asian);
                $join->where('asia.cid', '=', Odd::default_calculate_cid);
            })
            ->leftjoin('basket_odds as ou', function ($join) {
                $join->on('m.id', '=', 'ou.mid');
                $join->where('ou.type', '=', Odd::k_odd_type_europe);
                $join->where('ou.cid', '=', Odd::default_calculate_cid);
            })->leftjoin('basket_odds as goal', function ($join) {
                $join->on('m.id', '=', 'goal.mid');
                $join->where('goal.type', '=', Odd::k_odd_type_ou);
                $join->where('goal.cid', '=', Odd::default_calculate_cid);
            })
            ->addSelect('asia.up1 as asiaup1', 'asia.middle1 as asiamiddle1', 'asia.down1 as asiadown1',
                'asia.up2 as asiaup2', 'asia.middle2 as asiamiddle2', 'asia.down2 as asiadown2',
                'ou.up1 as ouup1', 'ou.middle1 as oumiddle1', 'ou.down1 as oudown1',
                'ou.up2 as ouup2', 'ou.middle2 as oumiddle2', 'ou.down2 as oudown2',
                'goal.up1 as goalup1', 'goal.middle1 as goalmiddle1', 'goal.down1 as goaldown1',
                'goal.up2 as goalup2', 'goal.middle2 as goalmiddle2', 'goal.down2 as goaldown2')
            ->leftJoin("basket_teams as home", "m.hid", "home.id")
            ->leftJoin("basket_teams as away", "m.aid", "away.id")
            ->addSelect('m.lid', 'm.status', 'm.live_time_str','m.betting_num',
                'm.time', 'm.hname','m.aname','m.hscore','m.ascore',
                'm.hscore_1st','m.ascore_1st', 'm.hscore_2nd','m.ascore_2nd',
                'm.hscore_3rd','m.ascore_3rd', 'm.hscore_4th','m.ascore_4th',
                'm.h_ot','m.a_ot'
            )->addSelect("home.icon as hicon", "away.icon as aicon")
            ->where('m.id', $id)->first();

        if (isset($match)) {
            $match->sport = MatchLive::kSportBasketball;
            $match->live_time_str = $match->getMatchCurTime(true);
            $match->time = strtotime($match->time);
            $match->statusStr = BasketMatch::getStatusTextCn($match->status, $match->system == 1);
            $match->hicon = BasketTeam::getIcon($match->hicon);
            $match->aicon = BasketTeam::getIcon($match->aicon);
            $match->h_ot = (isset($match->h_ot) && strlen($match->h_ot) > 0) ? explode(',', $match->h_ot) : null;
            $match->a_ot = (isset($match->a_ot) && strlen($match->a_ot) > 0) ? explode(',', $match->a_ot) : null;

            $liveCountArray = MatchLiveTool::getMatchLiveCountById($id, MatchLive::kSportBasketball);
            $match->live = $liveCountArray['live'];
            $match->pc_live = $liveCountArray['pc_live'];

            if (!$isReset) {
                StatisticFileTool::putFileToTerminal($match, MatchLive::kSportBasketball, $id, 'match');
            }
        }

        return $match;
    }
}