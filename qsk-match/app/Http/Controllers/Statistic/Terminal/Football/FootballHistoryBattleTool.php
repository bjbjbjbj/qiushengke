<?php
/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/2/27
 * Time: 18:21
 */

namespace App\Http\Controllers\Statistic\Terminal\Football;


use App\Http\Controllers\Statistic\OddCalculateTool;
use App\Http\Controllers\Statistic\Terminal\Tool\HistoryBattleTool;
use App\Models\AnalyseModels\Match;
use App\Models\AnalyseModels\Odd;
use App\Models\LiaoGouModels\MatchLive;

trait FootballHistoryBattleTool
{
    private function matchHistoryBattle($match, $history_battle, $reset = false) {
        if (!$reset) {
            //如果有数据了则不再更新数据
            if (isset($history_battle)) {
                return $history_battle;
            }
        }

        return HistoryBattleTool::matchHistoryBattleData($match, MatchLive::kSportFootball);
    }
}