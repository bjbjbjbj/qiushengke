<?php
/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/3/1
 * Time: 11:16
 */

namespace App\Http\Controllers\Statistic\Terminal\Basketball;


use App\Http\Controllers\Statistic\Terminal\Tool\HistoryBattleTool;
use App\Models\LiaoGouModels\MatchLive;

trait BasketHistoryBattleTool
{
    private function matchHistoryBattle($match, $history_battle, $reset = false) {
        if (!$reset) {
            //如果有数据了则不再更新数据
            if (isset($history_battle)) {
                return $history_battle;
            }
        }

        return HistoryBattleTool::matchHistoryBattleData($match, MatchLive::kSportBasketball);
    }
}