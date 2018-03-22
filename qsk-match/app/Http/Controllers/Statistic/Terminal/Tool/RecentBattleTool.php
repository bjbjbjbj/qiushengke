<?php
/**
 * Created by PhpStorm.
 * User: ricky
 * Date: 2017/3/3
 * Time: 14:59
 */
namespace App\Http\Controllers\Statistic\Terminal\Tool;


use App\Http\Controllers\Statistic\OddCalculateTool;
use App\Models\AnalyseModels\Odd;
use App\Models\LiaoGouModels\MatchLive;

class RecentBattleTool{

    /**
     * 角球的近期战绩计算（包括同主客和同赛事）默认取6场
     */
    public static function recentCornerBattle($outMatch, $cid = Odd::default_banker_id, $count = 6) {
        $DBStrPre = MatchQueryTool::getDBPreStrBySport();
        $query = MatchQueryTool::getMatchQueryBySport();

        $query = MatchQueryTool::onMatchCommonSelect($query);
        $query = MatchQueryTool::onMatchLeagueLeftJoin($query);

        //查询所有的需要的比赛
        $allMatches = $query->leftJoin($DBStrPre.'odds as corner', function ($join) use ($cid) {
            $join->on('m.id', '=', 'corner.mid');
            $join->where('corner.cid', $cid)
                ->where('corner.type', 4);
        })->join($DBStrPre.'match_datas as md', function ($q) {
                $q->on('m.id', '=', 'md.id');
                $q->whereNotNull('h_corner')
                    ->whereNotNull('a_corner');
            })
            ->addSelect('md.h_corner as h_corner','md.a_corner as a_corner',
                'md.h_half_corner as h_half_corner','md.a_half_corner as a_half_corner',
                'corner.up1 as up2','corner.middle1 as middle2','corner.down1 as down2')
            ->where('m.status', -1)
            ->where(function ($q) use ($outMatch) {
                $q->where('hid', $outMatch->hid)
                    ->orwhere('hid', $outMatch->aid)
                    ->orwhere('aid', $outMatch->hid)
                    ->orwhere('aid', $outMatch->aid);
            })
            ->where('time', '<', $outMatch->time)
//            ->where('time', '>=', $lastTime)
            ->orderBy('time', 'desc')->take($count*10)->get();
        //主队
        $homeMatches = self::initMatches();
        //客队
        $awayMatches = self::initMatches();
        foreach ($allMatches as $inMatch) {
            $inMatch->up2 = OddCalculateTool::formatOddItem($inMatch, 'up2');
            $inMatch->down2 = OddCalculateTool::formatOddItem($inMatch, 'down2');

            $homeMatches = self::calculateMatches($homeMatches, $outMatch, $inMatch, $count, true, true);
            $awayMatches = self::calculateMatches($awayMatches, $outMatch, $inMatch, $count, false, true);
            if (count($homeMatches['sameHAL']) >= $count && count($awayMatches['sameHAL']) >= $count) {
                break;
            }
        }
        $rest = NULL;
        if (count($homeMatches['all']) > 0) {
            $rest['home'] = $homeMatches;
        }
        if (count($awayMatches['all']) > 0) {
            $rest['away'] = $awayMatches;
        }
        return $rest;
    }

    /**
     * 基本情况的近期战绩计算（包括同主客和同赛事）默认取10场
     */
    public static function recentBaseBattle($outMatch, $sport = MatchLive::kSportFootball, $cid = Odd::default_calculate_cid, $count = 20) {
        $query = MatchQueryTool::getMatchQueryBySport($sport);

        $lastTime = self::getBaseLastTime($outMatch, $count, $sport);

        $query = MatchQueryTool::onMatchCommonSelect($query);
        $query = MatchQueryTool::onDiffSelectBySport($query, $sport);
        $query = MatchQueryTool::onMatchLeagueLeftJoin($query, $sport);
        $query = MatchQueryTool::onMatchOddLeftJoin($query, $cid, $sport);

        //查询所有的需要的比赛
        $allMatches = $query->where('m.status', -1)
            ->where(function($q) use($outMatch) {
                $q->where('m.hid', $outMatch->hid)
                    ->orwhere('m.hid', $outMatch->aid)
                    ->orwhere('m.aid', $outMatch->hid)
                    ->orwhere('m.aid', $outMatch->aid);
            })
            ->where('m.time', '<', $outMatch->time)
            ->where('m.time', '>=', $lastTime)
            ->orderBy('m.time', 'desc')->get();
        //主队
        $homeMatches = self::initMatches();
        //客队
        $awayMatches = self::initMatches();
        foreach ($allMatches as $inMatch) {
            $inMatch = OddCalculateTool::formatOddData($inMatch);

            $homeMatches = self::calculateMatches($homeMatches, $outMatch, $inMatch, $count);
            $awayMatches = self::calculateMatches($awayMatches, $outMatch, $inMatch, $count, false);
        }
        $rest = NULL;
        if (count($homeMatches['all']) > 0) {
            $rest['home'] = $homeMatches;
        }
        if (count($awayMatches['all']) > 0) {
            $rest['away'] = $awayMatches;
        }
        return $rest;
    }

    /**
     * 计算单个球队相关比赛的数据
     */
    private static function calculateMatches($matches, $outMatch, $inMatch, $count, $isHomeTeam = true, $isCorner = false) {
        if ($isHomeTeam) {
            $hid = $outMatch->hid;
        } else {
            $hid = $outMatch->aid;
        }
        $isHome = $inMatch->hid == $hid;
        if ($isCorner) {
            $asia_result = -1;
            $goal_result = OddCalculateTool::getMatchSizeOddResult($inMatch->h_corner, $inMatch->a_corner, $inMatch->middle2);
            $ou_result = -1;
        } else {
            $asia_result = OddCalculateTool::getMatchAsiaOddResult($inMatch->hscore, $inMatch->ascore, $inMatch->asiamiddle2, $isHome);
            $goal_result = OddCalculateTool::getMatchSizeOddResult($inMatch->hscore, $inMatch->ascore, $inMatch->goalmiddle2);
            $ou_result = OddCalculateTool::getMatchResult($inMatch->hscore, $inMatch->ascore, $isHome);
        }

        //全部
        $allCount = count($matches['all']);
        $isAll = ($isHome || $inMatch->aid == $hid);
        if ($allCount < $count && $isAll) {
            $matches['all'][] = $inMatch;
            $matches['statistic']['all'] = self::selfAddWinDrawLose($matches['statistic']['all'], $isHome, $inMatch->hscore, $inMatch->ascore, $ou_result, $asia_result, $goal_result);
        }
        //同主客
        $isSameHA = ($isHomeTeam && $isHome) || (!$isHomeTeam && !$isHome);
        $sameHACount = count($matches['sameHA']);
        if ($sameHACount < $count &&$isAll && $isSameHA) {
            $matches['sameHA'][] = $inMatch;
            $matches['statistic']['sameHA'] = self::selfAddWinDrawLose($matches['statistic']['sameHA'], $isHome, $inMatch->hscore, $inMatch->ascore, $ou_result, $asia_result, $goal_result);
        }
        //同赛事
        $isSameL = $inMatch->lid == $outMatch->lid;
        $sameLCount = count($matches['sameL']);
        if ($sameLCount < $count && $isAll && $isSameL) {
            $matches['sameL'][] = $inMatch;
            $matches['statistic']['sameL'] = self::selfAddWinDrawLose($matches['statistic']['sameL'], $isHome, $inMatch->hscore, $inMatch->ascore, $ou_result, $asia_result, $goal_result);
        }
        //同主客+同赛事
        $sameHALCount = count($matches['sameHAL']);
        if ($sameHALCount < $count && $isAll && $isSameHA && $isSameL) {
            $matches['sameHAL'][] = $inMatch;
            $matches['statistic']['sameHAL'] = self::selfAddWinDrawLose($matches['statistic']['sameHAL'], $isHome, $inMatch->hscore, $inMatch->ascore, $ou_result, $asia_result, $goal_result);
        }

        return $matches;
    }

    /**
     * 初始化需要填充的比赛数据（包括主队和客队）
     */
    private static function initMatches() {
        $matches['all'] = array();
        $matches['sameHA'] = array();
        $matches['sameL'] = array();
        $matches['sameHAL'] = array();
        $matches['statistic'] = self::initAllHAL();
        return $matches;
    }

    /**
     * 获取基本情况需要的离现在最远的时间
     */
    private static function getBaseLastTime($match, $count, $sport = MatchLive::kSportFootball) {
        $homeQuery = MatchQueryTool::getMatchQueryBySport($sport);
        $awayQuery = clone $homeQuery;

        $homeMatches = $homeQuery->where('hid', $match->hid)
            ->where('time','<',$match->time)
            ->where('lid',$match->lid)
            ->where('status',-1)
            ->orderby('time','desc')
            ->take($count)
            ->get();
        $awayMatches = $awayQuery->where('aid', $match->aid)
            ->where('time','<',$match->time)
            ->where('lid',$match->lid)
            ->where('status',-1)
            ->orderby('time','desc')
            ->take($count)
            ->get();
        $count = count($homeMatches);
        if ($count > 1) {
            $homeLastTime = $homeMatches[$count-1]->time;
        } else {
            $homeLastTime = $match->time;
        }
        $count = count($awayMatches);
        if ($count > 1) {
            $awayLastTime = $awayMatches[$count-1]->time;
        } else {
            $awayLastTime = $match->time;
        }
        return $homeLastTime > $awayLastTime ? $awayLastTime : $homeLastTime;
    }

    /**
     * 初始化状态 全部、同主客、同赛事、同主客+同赛事
     */
    private static function initAllHAL()
    {
        $temp['all'] = self::insertWinDrawLose();
        $temp['sameHA'] = self::insertWinDrawLose();
        $temp['sameL'] = self::insertWinDrawLose();
        $temp['sameHAL'] = self::insertWinDrawLose();
        return $temp;
    }

    /**
     * 自增计算胜平负
     * @param $count int 传入的数量
     * @param $type int 类型 1.win 2.draw 3.lose
     * @param $result int 3 1 0
     * @return int 计算后的数量
     */
    private static function addWinDrawLoseCount($count, $type, $result)
    {
        switch ($result) {
            case 3:
                if ($type == 1) $count++;
                break;
            case 1:
                if ($type == 2) $count++;
                break;
            case 0:
                if ($type == 3) $count++;
                break;
        }
        return $count;
    }

    /**
     * 自增胜平负计算
     */
    private static function selfAddWinDrawLose($statistic, $isHome, $hscore, $ascore, $ou_result, $asia_result, $goal_result)
    {
        if (!isset($statistic)) {
            $statistic = self::insertWinDrawLose();
        }
        $win = self::addWinDrawLoseCount($statistic['win'], 1, $ou_result);
        $draw = self::addWinDrawLoseCount($statistic['draw'], 2, $ou_result);
        $lose = self::addWinDrawLoseCount($statistic['lose'], 3, $ou_result);
        $asiawin = self::addWinDrawLoseCount($statistic['asiawin'], 1, $asia_result);
        $asiadraw = self::addWinDrawLoseCount($statistic['asiadraw'], 2, $asia_result);
        $asialose = self::addWinDrawLoseCount($statistic['asialose'], 3, $asia_result);
        $goalbig = self::addWinDrawLoseCount($statistic['goalbig'], 1, $goal_result);
        $goaldraw = self::addWinDrawLoseCount($statistic['goaldraw'], 2, $goal_result);
        $goalsmall = self::addWinDrawLoseCount($statistic['goalsmall'], 3, $goal_result);
        $goal = $statistic['goal'] + ($isHome ? $hscore : $ascore);
        $against = $statistic['against'] + ($isHome ? $ascore : $hscore);
        return self::insertWinDrawLose($win, $draw, $lose, $asiawin, $asiadraw, $asialose, $goalbig, $goaldraw, $goalsmall, $goal, $against);
    }

    /**
     * 把胜平负等信息装载到一个对象里
     */
    private static function insertWinDrawLose($win = 0, $draw = 0, $lose = 0,
                                       $asiawin = 0, $asiadraw = 0, $asialose = 0,
                                       $goalbig = 0, $goaldraw = 0, $goalsmall = 0,
                                       $goal = 0, $against = 0)
    {
        $total = $win + $draw + $lose;
        $asiatotal = $asiawin + $asiadraw + $asialose;
        $goaltotal = $goalbig + $goaldraw + $goalsmall;
        $statistic['total'] = $total;
        $statistic['asiatotal'] = $asiatotal;
        $statistic['goaltotal'] = $goaltotal;
        $statistic['win'] = $win;
        $statistic['draw'] = $draw;
        $statistic['lose'] = $lose;
        $statistic['asiawin'] = $asiawin;
        $statistic['asiadraw'] = $asiadraw;
        $statistic['asialose'] = $asialose;
        $statistic['goalbig'] = $goalbig;
        $statistic['goaldraw'] = $goaldraw;
        $statistic['goalsmall'] = $goalsmall;
        $statistic['goal'] = $goal;
        $statistic['against'] = $against;
        $statistic['win_percent'] = $total > 0 ? self::onPercentFormat($win / $total) : 0;
        $statistic['draw_percent'] = $total > 0 ? self::onPercentFormat($draw / $total) : 0;
        $statistic['lose_percent'] = $total > 0 ? self::onPercentFormat($lose / $total) : 0;
        $statistic['asiawin_percent'] = $asiatotal > 0 ? self::onPercentFormat($asiawin / $asiatotal) : 0;
        $statistic['asiadraw_percent'] = $asiatotal > 0 ? self::onPercentFormat($asiadraw / $asiatotal) : 0;
        $statistic['asialose_percent'] = $asiatotal > 0 ? self::onPercentFormat($asialose / $asiatotal) : 0;
        $statistic['goalbig_percent'] = $goaltotal > 0 ? self::onPercentFormat($goalbig / $goaltotal) : 0;
        $statistic['goaldraw_percent'] = $goaltotal > 0 ? self::onPercentFormat($goaldraw / $goaltotal) : 0;
        $statistic['goalsmall_percent'] = $goaltotal > 0 ? self::onPercentFormat($goalsmall / $goaltotal) : 0;
        return $statistic;
    }

    /**
     * 格式化百分比
     */
    private static function onPercentFormat($percent) {
        return sprintf("%.1f", $percent * 100);
    }
}