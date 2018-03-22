<?php
/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/2/27
 * Time: 18:35
 */

namespace App\Http\Controllers\Statistic\Terminal\Football;


use App\Http\Controllers\Statistic\OddCalculateTool;
use App\Http\Controllers\Statistic\Terminal\Tool\OddResultTool;
use App\Models\AnalyseModels\Match;
use App\Models\AnalyseModels\Odd;

trait FootballOddResultTool
{
    private function matchOddResult($match, $odd_result, $reset = false) {
        if (!$reset) {
            //如果有数据了则不再更新数据
            if (isset($odd_result)) {
                return $odd_result;
            }
        }

        return OddResultTool::matchOddResult($match);
    }
}