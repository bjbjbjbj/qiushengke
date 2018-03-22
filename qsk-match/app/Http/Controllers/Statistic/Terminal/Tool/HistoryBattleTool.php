<?php
namespace App\Http\Controllers\Statistic\Terminal\Tool;

use App\Http\Controllers\Statistic\OddCalculateTool;
use App\Models\LiaoGouModels\MatchLive;
use App\Models\LiaoGouModels\Odd;

/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/3/1
 * Time: 11:22
 */
class HistoryBattleTool
{
    public static function matchHistoryBattleData($match, $sport = MatchLive::kSportFootball)
    {
        //交往战绩
        $resultHistoryBattle = self::matchBaseHistoryBattle($match, $sport);
        //处理交往战绩
        $resultNHNL = array();
        $resultSHNL = array();
        $resultNHSL = array();
        $resultSHSL = array();
        foreach ($resultHistoryBattle as $tmp) {
            $tmp = OddCalculateTool::formatOddData($tmp);

            if ($tmp['hid'] == $match['hid'] && $tmp['aid'] == $match['aid']) {
                if ($tmp['lid'] == $match['lid']) {
                    $resultSHSL[] = $tmp;
                }
                $resultSHNL[] = $tmp;
            }

            if ($tmp['lid'] == $match['lid']) {
                $resultNHSL[] = $tmp;
            }
            $resultNHNL[] = $tmp;
        }
        $resultHistoryBattleNew = array();
        $resultHistoryBattleNew['nhnl'] = $resultNHNL;
        $resultHistoryBattleNew['shnl'] = $resultSHNL;
        $resultHistoryBattleNew['nhsl'] = $resultNHSL;
        $resultHistoryBattleNew['shsl'] = $resultSHSL;

        //计算输赢
        $resultHistoryBattleResult = self::matchBaseHistoryBattleResult($resultHistoryBattle, $match->hid, $match->aid, $match->lid);

        $rest = array();
        if (count($resultNHNL) + count($resultSHNL) + count($resultNHSL) + count($resultSHSL) > 0) {
            $rest['historyBattle'] = $resultHistoryBattleNew;
            $rest['historyBattleResult'] = $resultHistoryBattleResult;
        }

        return $rest;
    }

    /**
     * 双方对阵历史比赛
     * @param $match
     * @return mixed
     */
    private static function matchBaseHistoryBattle($match, $sport = MatchLive::kSportFootball)
    {
        $query = MatchQueryTool::getMatchQueryBySport($sport);

        $query = MatchQueryTool::onMatchCommonSelect($query);
        $query = MatchQueryTool::onDiffSelectBySport($query, $sport);
        $query = MatchQueryTool::onMatchLeagueLeftJoin($query, $sport);
        $query = MatchQueryTool::onMatchOddLeftJoin($query, Odd::default_calculate_cid, $sport);

        $matches = $query->where(function ($q) use ($match) {
            $q->where('status', -1)
                ->where('hid', $match->hid)
                ->where('aid', $match->aid)
                ->where('time', '<', $match->time);
        })
            ->orwhere(function ($q) use ($match) {
                $q->where('status', -1)
                    ->where('hid', $match->aid)
                    ->where('aid', $match->hid)
                    ->where('time', '<', $match->time);
            })->orderby('time', 'desc')
            ->get();
        return $matches;
    }

    /**
     * 双方交往比赛对阵统计
     * @param $matches
     * @param $hid
     * @param $aid
     * @param $lid
     * @return array
     */
    private static function matchBaseHistoryBattleResult($matches, $hid, $aid, $lid)
    {
        $result = array();
        //全部
        $win1 = 0;
        $draw1 = 0;
        $lose1 = 0;
        $oddwin1 = 0;
        $odddraw1 = 0;
        $oddlose1 = 0;
        //同主客
        $win2 = 0;
        $draw2 = 0;
        $lose2 = 0;
        $oddwin2 = 0;
        $odddraw2 = 0;
        $oddlose2 = 0;
        //同赛事
        $win3 = 0;
        $draw3 = 0;
        $lose3 = 0;
        $oddwin3 = 0;
        $odddraw3 = 0;
        $oddlose3 = 0;
        //同主客赛事
        $win4 = 0;
        $draw4 = 0;
        $lose4 = 0;
        $oddwin4 = 0;
        $odddraw4 = 0;
        $oddlose4 = 0;
        foreach ($matches as $match) {
            //胜负
            $temp = OddCalculateTool::getMatchResult($match->hscore, $match->ascore, $match->hid == $hid);
            switch ($temp) {
                case 3:
                    //全部
                    $win1++;
                    //同主客
                    if ($match->hid == $hid && $match->aid == $aid) {
                        $win2++;
                    }
                    //同赛事
                    if ($match->lid == $lid) {
                        $win3++;
                    }
                    //同主客 同赛事
                    if (($match->hid == $hid && $match->aid == $aid) && $match->lid == $lid) {
                        $win4++;
                    }
                    break;
                case 1:
                    //全部
                    $draw1++;
                    //同主客
                    if ($match->hid == $hid && $match->aid == $aid) {
                        $draw2++;
                    }
                    //同赛事
                    if ($match->lid == $lid) {
                        $draw3++;
                    }
                    //同主客 同赛事
                    if (($match->hid == $hid && $match->aid == $aid) && $match->lid == $lid) {
                        $draw4++;
                    }
                    break;
                case 0:
                    //全部
                    $lose1++;
                    //同主客
                    if ($match->hid == $hid && $match->aid == $aid) {
                        $lose2++;
                    }
                    //同赛事
                    if ($match->lid == $lid) {
                        $lose3++;
                    }
                    //同主客 同赛事
                    if (($match->hid == $hid && $match->aid == $aid) && $match->lid == $lid) {
                        $lose4++;
                    }
                    break;
            }

            //赢盘率
            if (isset($match['asiamiddle2'])) {
                $temp = OddCalculateTool::getMatchAsiaOddResult($match['hscore'], $match['ascore'], $match['asiamiddle2'], $match->hid == $hid);
                switch ($temp) {
                    case 3:
                        //同主客
                        if ($match->hid == $hid && $match->aid == $aid) {
                            $oddwin2++;
                        }
                        //同赛事
                        if ($match->lid == $lid) {
                            $oddwin3++;
                        }
                        //同主客 同赛事
                        if (($match->hid == $hid && $match->aid == $aid) && $match->lid == $lid) {
                            $oddwin4++;
                        }
                        $oddwin1++;
                        break;
                    case 1:
                        //同主客
                        if ($match->hid == $hid && $match->aid == $aid) {
                            $odddraw2++;
                        }
                        //同赛事
                        if ($match->lid == $lid) {
                            $odddraw3++;
                        }
                        //同主客 同赛事
                        if (($match->hid == $hid && $match->aid == $aid) && $match->lid == $lid) {
                            $odddraw4++;
                        }
                        $odddraw1++;
                        break;
                    case 0:
                        //同主客
                        if ($match->hid == $hid && $match->aid == $aid) {
                            $oddlose2++;
                        }
                        //同赛事
                        if ($match->lid == $lid) {
                            $oddlose3++;
                        }
                        //同主客 同赛事
                        if (($match->hid == $hid && $match->aid == $aid) && $match->lid == $lid) {
                            $oddlose4++;
                        }
                        $oddlose1++;
                        break;
                }
            }
        }
        $allPer = ($win1 + $draw1 + $lose1) == 0 ? '-' : number_format((100.0 * OddCalculateTool::getOddWinPercent($win1, $draw1, $lose1, true, true)), 1);
        $teamPer = ($win2 + $draw2 + $lose2) == 0 ? '-' : number_format((100.0 * OddCalculateTool::getOddWinPercent($win2, $draw2, $lose2, true, true)), 1);
        $leaguePer = ($win3 + $draw3 + $lose3) == 0 ? '-' : number_format((100.0 * OddCalculateTool::getOddWinPercent($win3, $draw3, $lose3, true, true)), 1);
        $bothPer = ($win4 + $draw4 + $lose4) == 0 ? '-' : number_format((100.0 * OddCalculateTool::getOddWinPercent($win4, $draw4, $lose4, true, true)), 1);
        $allPerOdd = ($oddwin1 + $odddraw1 + $oddlose1) == 0 ? '-' : number_format((100.0 * OddCalculateTool::getOddWinPercent($oddwin1, $odddraw1, $oddlose1)), 1);
        $teamPerOdd = ($oddwin2 + $odddraw2 + $oddlose2) == 0 ? '-' : number_format((100.0 * OddCalculateTool::getOddWinPercent($oddwin2, $odddraw2, $oddlose2)), 1);
        $leaguePerOdd = ($oddwin3 + $odddraw3 + $oddlose3) == 0 ? '-' : number_format((100.0 * OddCalculateTool::getOddWinPercent($oddwin3, $odddraw3, $oddlose3)), 1);
        $bothPerOdd = ($oddwin4 + $odddraw4 + $oddlose4) == 0 ? '-' : number_format((100.0 * OddCalculateTool::getOddWinPercent($oddwin4, $odddraw4, $oddlose4)), 1);
        $result = array(
            'all' => array('win' => $win1, 'draw' => $draw1, 'lose' => $lose1, 'winPercent' => $allPer, 'oddPercent' => $allPerOdd),
            'team' => array('win' => $win2, 'draw' => $draw2, 'lose' => $lose2, 'winPercent' => $teamPer, 'oddPercent' => $teamPerOdd),
            'league' => array('win' => $win3, 'draw' => $draw3, 'lose' => $lose3, 'winPercent' => $leaguePer, 'oddPercent' => $leaguePerOdd),
            'both' => array('win' => $win4, 'draw' => $draw4, 'lose' => $lose4, 'winPercent' => $bothPer, 'oddPercent' => $bothPerOdd)
        );
        return $result;
    }

}