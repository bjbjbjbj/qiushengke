<?php
/**
 * Created by PhpStorm.
 * User: ricky
 * Date: 2017/3/3
 * Time: 14:59
 */
namespace App\Http\Controllers\App\MatchDetail\Football\Tool;


use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\Odd;

trait RecentBattleTool{

    /**
     * 角球的近期战绩计算（包括同主客和同赛事）默认取6场
     */
    public function recentCornerBattle($outMatch, $cid = Odd::default_calculate_cid, $count = 6) {
        //查询所有的需要的比赛
        $allMatches = Match::query()
            ->leftJoin('odds as goal', function ($join) use ($cid) {
                $join->on('matches.id', '=', 'goal.mid');
                $join->where('goal.cid', $cid)
                    ->where('goal.type', 4);
            })->leftJoin('leagues', 'matches.lid', '=', 'leagues.id')
            ->join('match_datas', function ($q) {
                $q->on('matches.id', '=', 'match_datas.id');
                $q->whereNotNull('h_corner')
                    ->whereNotNull('a_corner');
            })
            ->select('matches.*', 'leagues.name as league',
                'match_datas.h_corner as h_corner', 'match_datas.a_corner as a_corner',
                'match_datas.h_half_corner as h_half_corner', 'match_datas.a_half_corner as a_half_corner',
                'goal.up1 as goalup2', 'goal.middle1 as goalmiddle2', 'goal.down1 as goaldown2')
            ->where('matches.status', -1)
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
        $homeMatches = $this->initMatches();
        //客队
        $awayMatches = $this->initMatches();
        foreach ($allMatches as $inMatch) {
            $homeMatches = $this->calculateMatches($homeMatches, $outMatch, $inMatch, $count, true, true);
            $awayMatches = $this->calculateMatches($awayMatches, $outMatch, $inMatch, $count, false, true);
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
    public function recentBaseBattle($outMatch, $cid = Odd::default_banker_id, $count = 20) {
        $lastTime = $this->getBaseLastTime($outMatch, $count);
        //查询所有的需要的比赛
        $allMatches = Match::query()
            ->leftJoin('odds as asia', function ($join) use ($cid) {
                $join->on('matches.id', '=', 'asia.mid');
                $join->where('asia.cid', $cid)->where('asia.type', 1);
            })->leftJoin('odds as goal', function ($join) use ($cid) {
                $join->on('matches.id', '=', 'goal.mid');
                $join->where('goal.cid', $cid)->where('goal.type', 2);
            })->leftJoin('odds as ou', function ($join) use ($cid) {
                $join->on('matches.id', '=', 'ou.mid');
                $join->where('ou.cid', $cid)->where('ou.type', 3);
            })->leftJoin('leagues', 'matches.lid', '=', 'leagues.id')
            ->select('matches.*', 'leagues.name as league',
                'asia.up1 as up1', 'asia.middle1 as middle1', 'asia.down1 as down1',
                'asia.up2 as up2', 'asia.middle2 as middle2', 'asia.down2 as down2',
                'goal.up1 as goalUp1', 'goal.middle1 as goalMiddle1', 'goal.down1 as goalDown1',
                'goal.up2 as goalUp2', 'goal.middle2 as goalMiddle2', 'goal.down2 as goalDown2',
                'ou.up1 as ouUp1', 'ou.middle1 as ouMiddle1', 'ou.down1 as ouDown1',
                'ou.up2 as ouUp2', 'ou.middle2 as ouMiddle2', 'ou.down2 as ouDown2')
            ->where('matches.status', -1)
            ->where(function($q) use($outMatch) {
                $q->where('hid', $outMatch->hid)
                    ->orwhere('hid', $outMatch->aid)
                    ->orwhere('aid', $outMatch->hid)
                    ->orwhere('aid', $outMatch->aid);
            })
            ->where('time', '<', $outMatch->time)
            ->where('time', '>=', $lastTime)
            ->orderBy('time', 'desc')->get();
        //主队
        $homeMatches = $this->initMatches();
        //客队
        $awayMatches = $this->initMatches();
        foreach ($allMatches as $inMatch) {
            $homeMatches = $this->calculateMatches($homeMatches, $outMatch, $inMatch, $count);
            $awayMatches = $this->calculateMatches($awayMatches, $outMatch, $inMatch, $count, false);
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
    private function calculateMatches($matches, $outMatch, $inMatch, $count, $isHomeTeam = true, $isCorner = false) {
        if ($isHomeTeam) {
            $hid = $outMatch->hid;
        } else {
            $hid = $outMatch->aid;
        }
        $isHome = $inMatch->hid == $hid;
        if ($isCorner) {
            $asia_result = -1;
            $goal_result = OddCalculateTool::getMatchSizeOddResult($inMatch->h_corner, $inMatch->a_corner, $inMatch->goalmiddle2);
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
            $matches['statistic']['all'] = $this->selfAddWinDrawLose($matches['statistic']['all'], $isHome, $inMatch->hscore, $inMatch->ascore, $ou_result, $asia_result, $goal_result);
        }
        //同主客
        $isSameHA = ($isHomeTeam && $isHome) || (!$isHomeTeam && !$isHome);
        $sameHACount = count($matches['sameHA']);
        if ($sameHACount < $count &&$isAll && $isSameHA) {
            $matches['sameHA'][] = $inMatch;
            $matches['statistic']['sameHA'] = $this->selfAddWinDrawLose($matches['statistic']['sameHA'], $isHome, $inMatch->hscore, $inMatch->ascore, $ou_result, $asia_result, $goal_result);
        }
        //同赛事
        $isSameL = $inMatch->lid == $outMatch->lid;
        $sameLCount = count($matches['sameL']);
        if ($sameLCount < $count && $isAll && $isSameL) {
            $matches['sameL'][] = $inMatch;
            $matches['statistic']['sameL'] = $this->selfAddWinDrawLose($matches['statistic']['sameL'], $isHome, $inMatch->hscore, $inMatch->ascore, $ou_result, $asia_result, $goal_result);
        }
        //同主客+同赛事
        $sameHALCount = count($matches['sameHAL']);
        if ($sameHALCount < $count && $isAll && $isSameHA && $isSameL) {
            $matches['sameHAL'][] = $inMatch;
            $matches['statistic']['sameHAL'] = $this->selfAddWinDrawLose($matches['statistic']['sameHAL'], $isHome, $inMatch->hscore, $inMatch->ascore, $ou_result, $asia_result, $goal_result);
        }

        return $matches;
    }

    /**
     * 初始化需要填充的比赛数据（包括主队和客队）
     */
    private function initMatches() {
        $matches['all'] = array();
        $matches['sameHA'] = array();
        $matches['sameL'] = array();
        $matches['sameHAL'] = array();
        $matches['statistic'] = $this->initAllHAL();
        return $matches;
    }

    /**
     * 获取角球情况需要的离现在最远的时间
     */
    private function getCornerLastTime($match, $count) {
        $homeMatches = Match::query()
            ->join('match_datas', function ($q){
                $q->on('matches.id', '=', 'match_datas.id');
                $q->whereNotNull('h_corner')
                    ->whereNotNull('a_corner');
            })
            ->where('hid', $match->hid)
            ->where('time','<',$match->time)
            ->where('lid',$match->lid)
            ->where('matches.status',-1)
            ->orderby('time','desc')
            ->take($count)
            ->get();
        $awayMatches = Match::query()
            ->join('match_datas', function ($q){
                $q->on('matches.id', '=', 'match_datas.id');
                $q->whereNotNull('h_corner')
                    ->whereNotNull('a_corner');
            })
            ->where('aid', $match->aid)
            ->where('time','<',$match->time)
            ->where('lid',$match->lid)
            ->where('matches.status',-1)
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
     * 获取基本情况需要的离现在最远的时间
     */
    private function getBaseLastTime($match, $count) {
        $homeMatches = Match::where('hid', $match->hid)
            ->where('time','<',$match->time)
            ->where('lid',$match->lid)
            ->where('matches.status',-1)
            ->orderby('time','desc')
            ->take($count)
            ->get();
        $awayMatches = Match::where('aid', $match->aid)
            ->where('time','<',$match->time)
            ->where('lid',$match->lid)
            ->where('matches.status',-1)
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
    private function initAllHAL()
    {
        $temp['all'] = $this->insertWinDrawLose();
        $temp['sameHA'] = $this->insertWinDrawLose();
        $temp['sameL'] = $this->insertWinDrawLose();
        $temp['sameHAL'] = $this->insertWinDrawLose();
        return $temp;
    }

    /**
     * 自增计算胜平负
     * @param $count int 传入的数量
     * @param $type int 类型 1.win 2.draw 3.lose
     * @param $result int 3 1 0
     * @return int 计算后的数量
     */
    private function addWinDrawLoseCount($count, $type, $result)
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
    private function selfAddWinDrawLose($statistic, $isHome, $hscore, $ascore, $ou_result, $asia_result, $goal_result)
    {
        if (!isset($statistic)) {
            $statistic = $this->insertWinDrawLose();
        }
        $win = $this->addWinDrawLoseCount($statistic['win'], 1, $ou_result);
        $draw = $this->addWinDrawLoseCount($statistic['draw'], 2, $ou_result);
        $lose = $this->addWinDrawLoseCount($statistic['lose'], 3, $ou_result);
        $asiawin = $this->addWinDrawLoseCount($statistic['asiawin'], 1, $asia_result);
        $asiadraw = $this->addWinDrawLoseCount($statistic['asiadraw'], 2, $asia_result);
        $asialose = $this->addWinDrawLoseCount($statistic['asialose'], 3, $asia_result);
        $goalbig = $this->addWinDrawLoseCount($statistic['goalbig'], 1, $goal_result);
        $goaldraw = $this->addWinDrawLoseCount($statistic['goaldraw'], 2, $goal_result);
        $goalsmall = $this->addWinDrawLoseCount($statistic['goalsmall'], 3, $goal_result);
        $goal = $statistic['goal'] + ($isHome ? $hscore : $ascore);
        $against = $statistic['against'] + ($isHome ? $ascore : $hscore);
        return $this->insertWinDrawLose($win, $draw, $lose, $asiawin, $asiadraw, $asialose, $goalbig, $goaldraw, $goalsmall, $goal, $against);
    }

    /**
     * 把胜平负等信息装载到一个对象里
     */
    private function insertWinDrawLose($win = 0, $draw = 0, $lose = 0,
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
        $statistic['win_percent'] = $total > 0 ? $this->onPercentFormat($win / $total) : 0;
        $statistic['draw_percent'] = $total > 0 ? $this->onPercentFormat($draw / $total) : 0;
        $statistic['lose_percent'] = $total > 0 ? $this->onPercentFormat($lose / $total) : 0;
        $statistic['asiawin_percent'] = $asiatotal > 0 ? $this->onPercentFormat($asiawin / $asiatotal) : 0;
        $statistic['asiadraw_percent'] = $asiatotal > 0 ? $this->onPercentFormat($asiadraw / $asiatotal) : 0;
        $statistic['asialose_percent'] = $asiatotal > 0 ? $this->onPercentFormat($asialose / $asiatotal) : 0;
        $statistic['goalbig_percent'] = $goaltotal > 0 ? $this->onPercentFormat($goalbig / $goaltotal) : 0;
        $statistic['goaldraw_percent'] = $goaltotal > 0 ? $this->onPercentFormat($goaldraw / $goaltotal) : 0;
        $statistic['goalsmall_percent'] = $goaltotal > 0 ? $this->onPercentFormat($goalsmall / $goaltotal) : 0;
        return $statistic;
    }

    /**
     * 格式化百分比
     */
    private function onPercentFormat($percent) {
        return sprintf("%.1f", $percent * 100);
    }
}