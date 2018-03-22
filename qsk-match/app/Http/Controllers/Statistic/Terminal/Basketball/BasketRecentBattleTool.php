<?php
/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/2/27
 * Time: 18:14
 */

namespace App\Http\Controllers\Statistic\Terminal\Basketball;


use App\Http\Controllers\Statistic\Terminal\Tool\RecentBattleTool;
use App\Models\LiaoGouModels\MatchLive;

trait BasketRecentBattleTool
{
    private function matchBaseRecentlyBattle($match, $recent_battle, $reset = false) {
        if (!$reset) {
            //如果有数据了则不再更新数据
            if (isset($recent_battle)) {
                return $recent_battle;
            }
        }

        //最近战绩
        $rest = RecentBattleTool::recentBaseBattle($match, MatchLive::kSportBasketball);

        return $rest;
    }
}