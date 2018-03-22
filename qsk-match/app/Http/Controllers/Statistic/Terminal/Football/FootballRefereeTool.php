<?php
/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/2/27
 * Time: 18:53
 */

namespace App\Http\Controllers\Statistic\Terminal\Football;


use App\Models\LiaoGouModels\Referee;
use App\Models\AnalyseModels\MatchData;

trait FootballRefereeTool
{
    /**
     * 裁判
     */
    public function matchDetailReferee($match, $referee, $reset = false)
    {
        if (!$reset) {
            //如果有数据了则不再更新数据
            if (isset($referee)) {
                return $referee;
            }
        }

        $mid = $match->id;
        $matchData = MatchData::query()->find($mid);
        if (isset($matchData)) {
            $refereeId = $matchData->referee_id;
            if (isset($refereeId) && $refereeId > 0) {
                $referee = Referee::query()->find($refereeId);
            }
        }
        if (!isset($referee)) {
            return null;
        }
        $rest['name'] = $referee->name;
        $rest['hname'] = $match->hname;
        $rest['aname'] = $match->aname;
        $h_wdl = Referee::getRefereeTeamWinPercent($match->time, $match->hid, $referee->id);
        $rest['h_wdl'] = $h_wdl['win'].'胜'.$h_wdl['draw'].'平'.$h_wdl['lose'].'负';
        $a_wdl = Referee::getRefereeTeamWinPercent($match->time, $match->aid, $referee->id);
        $rest['a_wdl'] = $a_wdl['win'].'胜'.$a_wdl['draw'].'平'.$a_wdl['lose'].'负';
        $rest['win_percent'] = sprintf("%.1f", $referee->lately_win*100/$referee->lately_count);
        $rest['yellow_avg'] = $referee->lately_h_yellow_avg + $referee->lately_a_yellow_avg;
        return $rest;
    }
}