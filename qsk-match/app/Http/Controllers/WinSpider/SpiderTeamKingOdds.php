<?php
/**
 * Created by PhpStorm.
 * User: ricky
 * Date: 17/01/05
 * Time: 15:57
 */

namespace App\Http\Controllers\WinSpider;


use App\Http\Controllers\Tool\OddCalculateTool;
use App\Models\LiaoGouModels\League;
use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\Odd;
use App\Models\LiaoGouModels\Season;
use App\Models\LiaoGouModels\TeamOddResult;
use App\Models\LiaoGouModels\TeamOddResultLog;

use Illuminate\Http\Request;

trait SpiderTeamKingOdds
{

    public $bankerIds = Odd::default_top_bankers;

    /**
     * 根据比赛id 获取所需所有博彩公司的盘口信息
     */
    private function getOddsByMid($mid)
    {
        return Odd::query()->leftJoin('odds as size', function ($join) {
            $join->on('odds.mid', '=', 'size.mid')
                ->on('odds.cid', '=', 'size.cid')
                ->where('size.type', 2);
        })->select('odds.*', 'size.middle2 as sizemiddle2')
            ->where('odds.mid', $mid)
            ->where('odds.type', 1)->whereIn('odds.cid', $this->bankerIds)->get();
    }

    /**
     * 根据传入的比赛信息给盘王表设置需要计算的标识
     */
    private function setTeamOddResultNeedCalculateByMatch($match, $isToOver = false) {
        if (!isset($match) || !$isToOver) {
            return;
        }
        $league = League::query()->find($match->lid);
        if (isset($league) && (1 != $league->type || 1 != $league->odd)) { //只取联赛 和 需要开盘的
            return;
        }
        foreach ($this->bankerIds as $bankerId) {
            $this->setTeamOddResultNeedCalculateByTid($bankerId, $match, $match->hid);
            $this->setTeamOddResultNeedCalculateByTid($bankerId, $match, $match->aid);
        }
    }

    private function setTeamOddResultNeedCalculateByTid($cid, $match, $tid) {
        $seasonName = $match->season;
        if (!isset($seasonName)) {
            $lastSeason = $this->getLastSeason($match->lid);
            if (isset($lastSeason)) {
                $seasonName = $lastSeason->name;
            }
        }
        if (!isset($seasonName)) {
            return;
        }
        $result = TeamOddResult::query()
            ->where('cid', $cid)
            ->where('lid', $match->lid)
            ->where('season', $seasonName)
            ->where('tid', $tid)->first();
        if (isset($result)) {
            if (isset($result->last_match_time) && $result->last_match_time >= $match->time) {
                //如果球队盘王表 里 保存的最新比赛的时间 >= 传入的比赛时间 则不执行下面代码
                return;
            } else {
                $result->need_calculate = 1;
                $result->save();
            }
        } else {//需要新建
            $this->initLog($match->lid, $cid);
            $this->saveTeamOddResultByMatch($match, $tid, $seasonName, $cid);
        }
    }

    /**
     * 添加完成的比赛到球队盘王表里
     */
    private function addMatchOddToTeamOddResult($match)
    {
        if (!isset($match) || $match->status != -1) {
            return;
        }
        $league = League::query()->find($match->lid);
        if (isset($league) && (1 != $league->type || 1 != $league->odd)) { //只取联赛 和 需要开盘的
            return;
        }

        $odds = $this->getOddsByMid($match->id);
        foreach ($odds as $odd) {
            $this->addMatchOddToTeamOddResultByTid($odd->cid, $odd->middle2, $odd->sizemiddle2, $match, $match->hid);
            $this->addMatchOddToTeamOddResultByTid($odd->cid, $odd->middle2, $odd->sizemiddle2, $match, $match->aid);
        }
    }

    /**
     * 根据球队id添加完成的比赛到球队盘王表
     */
    private function addMatchOddToTeamOddResultByTid($cid, $asiamiddle2, $sizemiddle2, $match, $tid)
    {
        $seasonName = $match->season;
        if (!isset($seasonName)) {
            $lastSeason = $this->getLastSeason($match->lid);
            if (isset($lastSeason)) {
                $seasonName = $lastSeason->name;
            }
        }
        if (!isset($seasonName)) {
            return;
        }
        $result = TeamOddResult::query()
            ->where('cid', $cid)
            ->where('lid', $match->lid)
            ->where('season', $seasonName)
            ->where('tid', $tid)->first();

        if (isset($result)) {
            $this->addTeamOddByLastMatch($result, $match, $asiamiddle2, $sizemiddle2);
        } else {//不再新建
//            $this->initLog($match->lid, $cid);
//            $this->saveTeamOddResultByMatch($match, $match->hid, $match->season, $cid);
        }
    }

    /**
     * 通过最新的完结比赛添加 盘王信息
     */
    private function addTeamOddByLastMatch($teamOddResult, $match, $asiamiddle2, $sizemiddle2)
    {
        if (isset($teamOddResult->last_match_time) && $teamOddResult->last_match_time >= $match->time) {
            //如果球队盘王表 里 保存的最新比赛的时间 >= 传入的比赛时间 则不执行下面代码
            return;
        } else {
            $teamOddResult->last_match_time = $match->time;
        }
        //如果最新的已完结的比赛其实已经算过了，
        //说明比赛应该是认为改变了状态，应该重新计算整个球队的盘王信息
        if (str_contains($match->id, $teamOddResult->match_ids)) {
            $this->saveTeamOddResultByTeam($teamOddResult, 1);
            return;
        }
        $isHomeTeam = $teamOddResult->tid == $match->hid;

        $this->saveSingleTeamResult($teamOddResult, $match, $asiamiddle2, $sizemiddle2, $isHomeTeam);
    }

    /**
     * 添加球队盘王 结果信息
     */
    private function addTeamOddResult($cid, $lid, $seasonName, $match, $asiamiddle2, $sizemiddle2, $isHomeTeam)
    {
        if (!isset($match)) {
            echo "addTeamOddResult: match is null!<br>";
            return;
        }
        $tid = $isHomeTeam ? $match->hid : $match->aid;
        //主队
        $teamOddResult = TeamOddResult::query()
            ->where('cid', $cid)
            ->where('lid', $lid)
            ->where('season', $seasonName)
            ->where('tid', $tid)->first();
        if (!isset($teamOddResult)) {
            $teamOddResult = new TeamOddResult();
            $teamOddResult->tid = $tid;
            $teamOddResult->lid = $lid;
            $teamOddResult->cid = $cid;
            $teamOddResult->season = $seasonName;
            $teamOddResult->last_match_time = $match->time;
        }
        $teamOddResult->fill_status = -1;

        $this->saveSingleTeamResult($teamOddResult, $match, $asiamiddle2, $sizemiddle2, $isHomeTeam);
    }

    private function saveSingleTeamResult($teamOddResult, $match, $asiamiddle2, $sizemiddle2, $isHomeTeam) {

        $teamOddResult->match_ids = $this->addMatchIds($teamOddResult->match_ids, $match->id);

        $this->addTeamAsiaResult($teamOddResult, $match, $asiamiddle2, $isHomeTeam, true);
        $this->addTeamSizeResult($teamOddResult, $match, $sizemiddle2, $isHomeTeam, true);

        if (!isset($teamOddResult->last_match_time) || $teamOddResult->last_match_time < $match->time) {
            $teamOddResult->last_match_time = $match->time;
        }
        $teamOddResult->save();
    }

    /**
     * 添加球队比赛让球盘结果信息
     */
    private function addTeamAsiaResult($teamOddResult, $match, $middle2, $isHomeTeam, $isNotReset = false)
    {
        $isFill = $isNotReset || isset($teamOddResult->fill_status) && $teamOddResult->fill_status == -1;
        $count = $this->initCount($teamOddResult->count, $isFill);
        $lose_h = $this->initCount($teamOddResult->lose_h, $isFill);
        $draw_h = $this->initCount($teamOddResult->draw_h, $isFill);
        $win_h = $this->initCount($teamOddResult->win_h, $isFill);
        $lose_a = $this->initCount($teamOddResult->lose_a, $isFill);
        $draw_a = $this->initCount($teamOddResult->draw_a, $isFill);
        $win_a = $this->initCount($teamOddResult->win_a, $isFill);
        $up = $this->initCount($teamOddResult->up, $isFill);
        $middle = $this->initCount($teamOddResult->middle, $isFill);
        $down = $this->initCount($teamOddResult->down, $isFill);

        $result = OddCalculateTool::getMatchAsiaOddResult($match->hscore, $match->ascore, $middle2, $isHomeTeam);
        switch ($result) {
            case 0:
                if ($isHomeTeam) {
                    $lose_h++;
                } else {
                    $lose_a++;
                }
                break;
            case 1:
                if ($isHomeTeam) {
                    $draw_h++;
                } else {
                    $draw_a++;
                }
                break;
            case 3:
                if ($isHomeTeam) {
                    $win_h++;
                } else {
                    $win_a++;
                }
                break;
        }
        $result = OddCalculateTool::getMatchUpDownOddResult($middle2, $isHomeTeam);
        switch ($result) {
            case 3:
                $up++;
                break;
            case 1:
                $middle++;
                break;
            case 0:
                $down++;
                break;
        }

        $teamOddResult->up = $up;
        $teamOddResult->middle = $middle;
        $teamOddResult->down = $down;

        $this->calculateResult($teamOddResult, true, $count + 1, $win_h, $lose_h, $draw_h, $win_a, $draw_a, $lose_a);
    }

    /**
     * 添加大小球盘口球队数据
     */
    private function addTeamSizeResult($teamOddResult, $match, $middle2, $isHomeTeam, $isNotReset = false)
    {
        $isFill = $isNotReset || isset($teamOddResult->fill_status) && $teamOddResult->fill_status == -1;
        $count = $this->initCount($teamOddResult->count, $isFill);
        $lose_h = $this->initCount($teamOddResult->t_goal_h_lose, $isFill);
        $lose_a = $this->initCount($teamOddResult->t_goal_a_lose, $isFill);
        $draw_h = $this->initCount($teamOddResult->t_goal_h_draw, $isFill);
        $draw_a = $this->initCount($teamOddResult->t_goal_a_draw, $isFill);
        $win_h = $this->initCount($teamOddResult->t_goal_h_win, $isFill);
        $win_a = $this->initCount($teamOddResult->t_goal_a_win, $isFill);

        $result = OddCalculateTool::getMatchSizeOddResult($match->hscore, $match->ascore, $middle2);
        switch ($result) {
            case 0:
                if ($isHomeTeam) {
                    $lose_h++;
                } else {
                    $lose_a++;
                }
                break;
            case 1:
                if ($isHomeTeam) {
                    $draw_h++;
                } else {
                    $draw_a++;
                }
                break;
            case 3:
                if ($isHomeTeam) {
                    $win_h++;
                } else {
                    $win_a++;
                }
                break;
        }

        $this->calculateResult($teamOddResult, false, $count + 1, $win_h, $lose_h, $draw_h, $win_a, $draw_a, $lose_a);
    }

    /**
     * 初始化 单一 log
     */
    private function initLog($lid, $cid)
    {
        $log = TeamOddResultLog::query()
            ->where('lid', $lid)
            ->where('cid', $cid)
            ->first();
        if (!isset($log)) {
            $log = new TeamOddResultLog();
            $log->lid = $lid;
            $log->cid = $cid;
            $log->fill_status = 0;
        }
        $log->save();
    }

    /**
     * 保存球队让球盘 和 大小球 结果
     */
    private function saveTeamOddResultByMatch($match, $tid, $seasonName, $cid)
    {
        $teamOddResult = TeamOddResult::query()
            ->where('cid', $cid)
            ->where('tid', $tid)
            ->where('lid', $match->lid)
            ->where('season', $seasonName)
            ->first();
        if (!isset($teamOddResult)) {
            $teamOddResult = new TeamOddResult();
            $teamOddResult->cid = $cid;
            $teamOddResult->tid = $tid;
            $teamOddResult->lid = $match->lid;
            $teamOddResult->season = $seasonName;
        }

        $lastTime = $teamOddResult->last_match_time;
        if (isset($lastTime) && $lastTime >= $match->time) {
            return;
        }

        $teamOddResult->last_match_time = $match->time;

        echo 'saveTeamOddResultByMatch: cid:' . $cid . ',lid:' . $match->lid . ',tid:' . $tid . '<br>';

        //让球盘+大小球结果
        $this->putOddCountResult($teamOddResult, true);
        $teamOddResult->save();
    }

    /**
     * 获取让球盘赢盘(主客队) 数量结果
     */
    private function putOddCountResult($teamOddResult, $isForce = false)
    {
        if (!isset($teamOddResult)) {
            echo "putOddCountResult: teamOddResult is null<br>";
            return;
        }
        if ($teamOddResult->fill_status == 3 && !$isForce) {
            echo "putOddCountResult: cid =" . $teamOddResult->cid . ",tid=" . $teamOddResult->tid . ' has saved!<br>';
            return;
        }

        $matchIds = '';

        $season = Season::query()->where("lid", $teamOddResult->lid)->where("name", $teamOddResult->season)->first();

        //获取主场比赛数据
        $h_matches = Match::query()
            ->where('lid', $teamOddResult->lid)
            ->where('season', $teamOddResult->season)
            ->where('status', -1)
//            ->whereNotNull('round')
            ->where('hid', $teamOddResult->tid)
            ->leftjoin('odds as asia', function ($join) use ($teamOddResult) {
                $join->on('matches.id', '=', 'asia.mid');
                $join->where('asia.type', '=', 1);
                $join->where('asia.cid', '=', $teamOddResult->cid);
            })
            ->leftjoin('odds as size', function ($join) use ($teamOddResult) {
                $join->on('matches.id', '=', 'size.mid');
                $join->where('size.type', '=', 2);
                $join->where('size.cid', '=', $teamOddResult->cid);
            })
            ->select('matches.*', 'asia.middle2 as asiamiddle2', 'size.middle2 as sizemiddle2')
            ->orderBy('time', 'desc')->get();
        //上下盘数据
        $up = 0;
        $middle = 0;
        $down = 0;

        $lose_h = 0;
        $draw_h = 0;
        $win_h = 0;
        $t_goal_lose_h = 0;
        $t_goal_draw_h = 0;
        $t_goal_win_h = 0;

        //主场比赛场次
        $h_count = 0;
        if (isset($h_matches) && count($h_matches) > 0) {
            foreach ($h_matches as $match) {

                $matchIds = $this->addMatchIds($matchIds, $match->id);

                $result = OddCalculateTool::getMatchAsiaOddResult($match->hscore, $match->ascore, $match->asiamiddle2, true);
                switch ($result) {
                    case 0:
                        $lose_h++;
                        break;
                    case 1:
                        $draw_h++;
                        break;
                    case 3:
                        $win_h++;
                        break;
                }
                $result = OddCalculateTool::getMatchSizeOddResult($match->hscore, $match->ascore, $match->sizemiddle2);
                switch ($result) {
                    case 0:
                        $t_goal_lose_h++;
                        break;
                    case 1:
                        $t_goal_draw_h++;
                        break;
                    case 3:
                        $t_goal_win_h++;
                        break;
                }
                $result = OddCalculateTool::getMatchUpDownOddResult($match->asiamiddle2, true);
                switch ($result) {
                    case 3:
                        $up++;
                        break;
                    case 1:
                        $middle++;
                        break;
                    case 0:
                        $down++;
                        break;
                }
                $h_count++;

                //轮次不可靠，用season表的start
                if (isset($season) && isset($season->start)) {
                    if ($match->time < $season->start) break;
                }
//                if ($match->round == 1) {
//                    //倒叙遍历到第一轮，跳出循环
//                    break;
//                }
            }
        }
        //获取客场比赛数据
        $a_matches = Match::query()
            ->where('lid', $teamOddResult->lid)
            ->where('season', $teamOddResult->season)
//            ->whereNotNull('round')
            ->where('status', -1)
            ->where('aid', $teamOddResult->tid)
            ->leftjoin('odds as asia', function ($join) use ($teamOddResult) {
                $join->on('matches.id', '=', 'asia.mid');
                $join->where('asia.type', '=', 1);
                $join->where('asia.cid', '=', $teamOddResult->cid);
            })
            ->leftjoin('odds as size', function ($join) use ($teamOddResult) {
                $join->on('matches.id', '=', 'size.mid');
                $join->where('size.type', '=', 2);
                $join->where('size.cid', '=', $teamOddResult->cid);
            })
            ->select('matches.*', 'asia.middle2 as asiamiddle2', 'size.middle2 as sizemiddle2')
            ->orderBy('time', 'desc')
            ->get();
        $lose_a = 0;
        $draw_a = 0;
        $win_a = 0;
        $t_goal_lose_a = 0;
        $t_goal_draw_a = 0;
        $t_goal_win_a = 0;

        //客场比赛场次
        $a_count = 0;
        if (isset($a_matches) && count($a_matches) > 0) {
            foreach ($a_matches as $match) {

                $matchIds = $this->addMatchIds($matchIds, $match->id);

                $result = OddCalculateTool::getMatchAsiaOddResult($match->hscore, $match->ascore, $match->asiamiddle2, false);
                switch ($result) {
                    case 0:
                        $lose_a++;
                        break;
                    case 1:
                        $draw_a++;
                        break;
                    case 3:
                        $win_a++;
                        break;
                }
                $result = $result = OddCalculateTool::getMatchSizeOddResult($match->hscore, $match->ascore, $match->sizemiddle2);
                switch ($result) {
                    case 0:
                        $t_goal_lose_a++;
                        break;
                    case 1:
                        $t_goal_draw_a++;
                        break;
                    case 3:
                        $t_goal_win_a++;
                        break;
                }
                $result = OddCalculateTool::getMatchUpDownOddResult($match->asiamiddle2, false);
                switch ($result) {
                    case 3:
                        $up++;
                        break;
                    case 1:
                        $middle++;
                        break;
                    case 0:
                        $down++;
                        break;
                }
                $a_count++;
                //轮次不可靠，用season表的start
                if (isset($season) && isset($season->start)) {
                    if ($match->time < $season->start) break;
                }
//                if ($match->round == 1) {
//                    //倒叙遍历到第一轮，跳出循环
//                    break;
//                }
            }
        }
        //填充最新比赛时间
        if (count($h_matches) > 0 && (count($a_matches) <= 0 || $h_matches[0]->time > $a_matches[0]->time)) {
            $teamOddResult->last_match_time = $h_matches[0]->time;
        } else if (count($a_matches) > 0 && (count($h_matches) <= 0 || $a_matches[0]->time > $h_matches[0]->time)) {
            $teamOddResult->last_match_time = $a_matches[0]->time;
        }

        $teamOddResult->up = $up;
        $teamOddResult->middle = $middle;
        $teamOddResult->down = $down;

        $teamOddResult->match_ids = $matchIds;

        $this->calculateResult($teamOddResult, true, $h_count + $a_count, $win_h, $lose_h, $draw_h, $win_a, $draw_a, $lose_a);
        $teamOddResult->fill_status = 1;

        $this->calculateResult($teamOddResult, false, $h_count + $a_count, $t_goal_win_h, $t_goal_lose_h, $t_goal_draw_h, $t_goal_win_a, $t_goal_draw_a, $t_goal_lose_a);
        $teamOddResult->fill_status = 3;
        echo "putAsiaOddCountResult: total=" . $teamOddResult->total . ',count=' . $teamOddResult->count . '<br>';
    }

    /**
     * 计算结果
     */
    private function calculateResult($teamOddResult, $isAsia, $count, $win_h, $lose_h, $draw_h, $win_a, $draw_a, $lose_a)
    {
        $win = $win_h + $win_a;
        $draw = $draw_h + $draw_a;
        $lose = $lose_h + $lose_a;
        $h_total = $win_h + $lose_h + $draw_h;
        $a_total = $win_a + $lose_a + $draw_a;
        $total = $h_total + $a_total;

        $win_percent = OddCalculateTool::getOddWinPercent($win, $draw, $lose);
        $win_h_percent = OddCalculateTool::getOddWinPercent($win_h, $draw_h, $lose_h);
        $win_a_percent = OddCalculateTool::getOddWinPercent($win_a, $draw_a, $lose_a);
        $lose_percent = OddCalculateTool::getOddWinPercent($win, $draw, $lose, false);
        $lose_h_percent = OddCalculateTool::getOddWinPercent($win_h, $draw_h, $lose_h, false);
        $lose_a_percent = OddCalculateTool::getOddWinPercent($win_a, $draw_a, $lose_a, false);

        if ($isAsia) {
            $teamOddResult->win = $win;
            $teamOddResult->draw = $draw;
            $teamOddResult->lose = $lose;
            $teamOddResult->win_a = $win_a;
            $teamOddResult->draw_a = $draw_a;
            $teamOddResult->lose_a = $lose_a;
            $teamOddResult->win_h = $win_h;
            $teamOddResult->draw_h = $draw_h;
            $teamOddResult->lose_h = $lose_h;
            $teamOddResult->asia_win_percent = $win_percent;
            $teamOddResult->asia_a_win_percent = $win_a_percent;
            $teamOddResult->asia_h_win_percent = $win_h_percent;
            $teamOddResult->asia_lose_percent = $lose_percent;
            $teamOddResult->asia_a_lose_percent = $lose_a_percent;
            $teamOddResult->asia_h_lose_percent = $lose_h_percent;
            $teamOddResult->count = $count;
            $teamOddResult->odd_status = $count == ($h_total + $a_total) ? 1 : 0;
            $teamOddResult->total = $total;
        } else {
            $teamOddResult->t_goal_win = $win;
            $teamOddResult->t_goal_draw = $draw;
            $teamOddResult->t_goal_lose = $lose;
            $teamOddResult->t_goal_a_win = $win_a;
            $teamOddResult->t_goal_a_draw = $draw_a;
            $teamOddResult->t_goal_a_lose = $lose_a;
            $teamOddResult->t_goal_h_win = $win_h;
            $teamOddResult->t_goal_h_draw = $draw_h;
            $teamOddResult->t_goal_h_lose = $lose_h;
            $teamOddResult->t_goal_win_percent = $win_percent;
            $teamOddResult->t_goal_a_win_percent = $win_a_percent;
            $teamOddResult->t_goal_h_win_percent = $win_h_percent;
            $teamOddResult->t_goal_lose_percent = $lose_percent;
            $teamOddResult->t_goal_a_lose_percent = $lose_a_percent;
            $teamOddResult->t_goal_h_lose_percent = $lose_h_percent;
        }
    }

    /**
     * 重置球队盘口信息
     */
    private function resetTeamOddResult($teamOddResult)
    {
        $teamOddResult->win = 0;
        $teamOddResult->draw = 0;
        $teamOddResult->lose = 0;
        $teamOddResult->win_a = 0;
        $teamOddResult->draw_a = 0;
        $teamOddResult->lose_a = 0;
        $teamOddResult->win_h = 0;
        $teamOddResult->draw_h = 0;
        $teamOddResult->lose_h = 0;
        $teamOddResult->asia_win_percent = 0;
        $teamOddResult->asia_a_win_percent = 0;
        $teamOddResult->asia_h_win_percent = 0;
        $teamOddResult->count = 0;
        $teamOddResult->odd_status = 0;
        $teamOddResult->total = 0;
        $teamOddResult->t_goal_win = 0;
        $teamOddResult->t_goal_draw = 0;
        $teamOddResult->t_goal_lose = 0;
        $teamOddResult->t_goal_a_win = 0;
        $teamOddResult->t_goal_a_draw = 0;
        $teamOddResult->t_goal_a_lose = 0;
        $teamOddResult->t_goal_h_win = 0;
        $teamOddResult->t_goal_h_draw = 0;
        $teamOddResult->t_goal_h_lose = 0;
        $teamOddResult->t_goal_win_percent = 0;
        $teamOddResult->t_goal_a_win_percent = 0;
        $teamOddResult->t_goal_h_win_percent = 0;
        $teamOddResult->t_goal_lose_percent = 0;
        $teamOddResult->t_goal_a_lose_percent = 0;
        $teamOddResult->t_goal_h_lose_percent = 0;
    }

    /**
     * 根据赛事保存球队是否计算完毕的标示
     */
    private function saveTeamOddResultFillStatus($lid, $cid, $seasonName)
    {
        $results = TeamOddResult::query()
            ->where('lid', $lid)
            ->where('cid', $cid)
            ->where('season', $seasonName)
            ->get();
        if (isset($results) && count($results) > 0) {
            foreach ($results as $result) {
                $result->fill_status = 0;
                $result->save();
            }
        }
    }

    /**
     * 通过所有赛事当前赛事的所有比赛计算盘王
     */
    private function saveTeamOddResultByLeague($tempLogs, $lid, $season, $isAuto)
    {
        if (!isset($tempLogs) || count($tempLogs) <= 0) {
            echo 'saveTeamOddResultByLeague : tempLogs is null!<br>';
            return;
        }

        if (!isset($season)) {
            $season = $this->getLastSeason($lid);
        }
        $query = Match::query();
        foreach ($tempLogs as $log) {
            $query->leftjoin('odds as asia' . $log->cid, function ($join) use ($log) {
                $join->on('matches.id', '=', 'asia' . $log->cid . '.mid');
                $join->where('asia' . $log->cid . '.type', '=', 1);
                $join->where('asia' . $log->cid . '.cid', '=', $log->cid);
            })->leftjoin('odds as size' . $log->cid, function ($join) use ($log) {
                $join->on('matches.id', '=', 'size' . $log->cid . '.mid');
                $join->where('size' . $log->cid . '.type', '=', 2);
                $join->where('size' . $log->cid . '.cid', '=', $log->cid);
            })->addSelect('asia' . $log->cid . '.middle2 as asia' . $log->cid . 'middle2', 'size' . $log->cid . '.middle2 as size' . $log->cid . 'middle2');
        }
        $matches = $query->addSelect('matches.*')
            ->where('lid', $lid)
            ->where('season', $season->name)
            ->where('status', '-1')
            ->whereNotNull('hid')
            ->whereNotNull('aid')
            ->orderBy('time', 'desc')->get();
        echo 'lid=' . $lid . ';season=' . $season->name . ' match count:' . count($matches) . '<br>';

        foreach ($tempLogs as $log) {
            if (isset($matches) && count($matches) > 0) {
                //重置状态
                $this->saveTeamOddResultFillStatus($log->lid, $log->cid, $season->name);
                //重置数据
                $this->resetTeamOddResultByLeague($log->lid, $log->cid, $season->name);
                foreach ($matches as $match) {
                    $this->addTeamOddCountResult($log->cid, $log->lid, $match, $match['asia' . $log->cid . 'middle2'], $match['size' . $log->cid . 'middle2'], $season->name);
                    if ($match->time < $season->start) {
                        //比赛时间小于 当前赛季开始时间 则退出循环
                        break;
                    }
                }
                //保存完成，重置状态
                $this->saveTeamOddResultFillStatus($log->lid, $log->cid, $season->name);
                $this->saveLeagueOddResultLog($log, false, false);
            } else {
                $this->saveLeagueOddResultLog($log, true, false);
            }
        }
        if ($isAuto) {
            $this->refreshCurrentPage();
        }
    }

    /**
     * 根据赛事id和赛季清空所保存的盘王信息
     */
    private function resetTeamOddResultByLeague($lid, $cid, $seasonName)
    {
        if (isset($lid) && isset($cid)) {
            if (!isset($seasonName)) {
                $seasonName = $this->getLastSeason($lid)->name;
            }
            $results = TeamOddResult::query()
                ->where('cid', '=', $cid)
                ->where('lid', '=', $lid)
                ->where('season', '=', $seasonName)->get();
            foreach ($results as $result) {
                $this->resetTeamOddResult($result);
                $result->save();
            }
        }
    }

    private function getLastSeason($lid)
    {
        return Season::query()
            ->where('lid', $lid)
            ->where(function ($q) {//只取今年和去年有比赛的赛事
                $q->where('year', date('Y'))
                    ->orwhere('year', date('Y') - 1);
            })
            ->orderBy('year', 'desc')
            ->first();
    }

    private function saveTeamOddResultByTeam($teamOddResult, $reset)
    {
        if (!isset($teamOddResult)) {
            echo 'saveTeamOddResultByTeam: teamOddResult is null!<br>';
            return;
        }
        $this->initLog($teamOddResult->lid, $teamOddResult->cid);

        if (isset($teamOddResult) && $reset == 1) {
            //强制重置状态
            $teamOddResult->fill_status = 0;
        }
        //让球盘+大小球结果
        $this->putOddCountResult($teamOddResult);

        $teamOddResult->save();
    }

    /**
     * 保存赛事 盘口 数据 log
     */
    private function saveLeagueOddResultLog($log, $isEmpty, $isAuto)
    {
        if (isset($log)) {
            $log->fill_status = 1;
            $log->save();
        }
        if ($isEmpty) {
            echo 'saveTeamOddResult:' . $log . ' match is empty!<br>';
        } else {
            echo 'saveTeamOddResult:' . $log . ' save success!<br>';
        }
        if ($isAuto) {
            $this->refreshCurrentPage();
        }
    }

    //刷新当前页面
    private function refreshCurrentPage()
    {
        echo "<script language=JavaScript> location.replace(location.href);</script>";
        exit;
    }

    /**
     * 初始化数据
     */
    private function initCount($count, $isNotReset)
    {
        $temp = 0;
        if ($isNotReset) {
            if (isset($count)) {
                $temp = $count;
            }
        }
        return $temp;
    }

    /**
     * 根据比赛计算球队 盘王信息
     */
    private function addTeamOddCountResult($cid, $lid, $match, $asiamiddle2, $sizemiddle2, $seasonName)
    {
        echo 'add odd by cid=' . $cid . ';match=' . $match->id . ';hid=' . $match->hid . ';aid=' . $match->aid . '<br>';

        //主队
        $this->addTeamOddResult($cid, $lid, $seasonName, $match, $asiamiddle2, $sizemiddle2, true);

        //客队
        $this->addTeamOddResult($cid, $lid, $seasonName, $match, $asiamiddle2, $sizemiddle2, false);
    }

    /**
     * 根据需要的主流赛事填充的log表(这个方法只需要执行一次)
     */
    private function initTeamOddLogByLeague(Request $request, $isMain)
    {
        if ($isMain) {
            $leagues = League::query()
                ->where('type', 1)
                ->where('odd', 1)
                ->where('main', 1)
                ->orderBy('id')
                ->get();
        } else {
            $leagues = League::query()
                ->where('type', 1)
                ->where('odd', 1)
                ->orderBy('id')
                ->get();
        }
        $logCount = TeamOddResultLog::query()->count();
        if ($logCount / count($this->bankerIds) < count($leagues)) {
            foreach ($leagues as $league) {
                foreach ($this->bankerIds as $cid) {
                    $this->initLog($league->id, $cid);
                }
            }
            echo 'init count:' . count($leagues) . ' league to log! <br>';
            $this->fillTeamOddResultByMainLeague($request, $isMain);
        } else {
            echo 'calculate main league team odd result complete!';
        }
    }

    /**
     * 重置log表
     */
    public function resetTeamOddLogByLeague(Request $request)
    {
        $isMain = $request->input('isMain', 0) == 1;

        $leagues = League::query()
            ->where('type', 1)
            ->where('odd', 1)
            ->where(function ($q) use ($isMain) {
                if ($isMain) {
                    $q->where('main', 1);
                }
            })
            ->orderBy('id')
            ->get();
        foreach ($leagues as $league) {
            foreach ($this->bankerIds as $cid) {
                $this->initLog($league->id, $cid);
            }
        }
        echo 'init count:' . count($leagues) . ' league to log! <br>';
    }

    /**
     * 添加比赛id
     */
    public function addMatchIds($matchIds, $mid) {
        if (!isset($matchIds) || strlen($matchIds) == 0) {
            $matchIds = $mid;
        }else if (!str_contains($mid, $matchIds)) {
            $matchIds .= ','.$mid;
        }
        return $matchIds;
    }
}