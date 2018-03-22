<?php
/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/2/27
 * Time: 18:17
 */

namespace App\Http\Controllers\Statistic\Terminal\Basketball;


use App\Http\Controllers\Statistic\Terminal\Tool\ScheduleTool;
use App\Models\LiaoGouModels\MatchLive;

trait BasketScheduleTool
{
    /**
     * 比赛双方未来赛程
     */
    private function matchSchedule($match, $schedule, $reset = false){
        if (!$reset) {
            //如果有数据了则不再更新数据
            if (isset($schedule)) {
                return $schedule;
            }
        }

        return ScheduleTool::matchSchedule($match, MatchLive::kSportBasketball);
    }
}