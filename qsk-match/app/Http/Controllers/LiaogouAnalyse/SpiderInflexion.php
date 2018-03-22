<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 16/11/15
 * Time: 下午7:06
 * 拐点用
 */
namespace App\Http\Controllers\LiaogouAnalyse;

use App\Http\Controllers\Tool\OddCalculateTool;
use App\Models\LiaoGouModels\Inflexion;
use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\MatchAnalysisSameOdd;
use App\Models\LiaoGouModels\Odd;
use Illuminate\Http\Request;

/*
 * 拐点计算,不属于球探库逻辑,独立
 */
trait SpiderInflexion
{
    //澳彩 SB 立博 Bet365 易胜博 韦德 金宝博


    //一天执行一次，删除1天之前不需要的数据
    private function delHistoryInflexions() {
        Inflexion::query()->where('time', '<', date_format(date_create('-1 days'), 'y-m-d'))->delete();
    }

    /**
     * 获取比赛查询的相同部分
     */
    private function getMatchQuery() {
        return Match::query()
            ->join('leagues', function($join) {
                $join->on('matches.lid', '=', 'leagues.id');
                $join->where('leagues.odd', '=', 1); //只获取开盘的赛事
            })
            ->select('matches.*')
            ->whereNotNull("hid")
            ->whereNotNull("aid")
            ->whereNotNull("hname")
            ->whereNotNull("aname")
            ->where("status", "=", 0);
    }

    public function analyseByDate(Request $request){
        $reset = $request->input("isReset", 0);
        $mid = $request->input("mid");
        $query = $this->getMatchQuery();
        if (isset($mid)) {
            $query->where('id', '=', $mid);
        } else {
            $date = $request->input("date");
            if (isset($date)) {
                $date = date_create($date);
            } else {
                $date = date_create();
            }
            date_time_set($date, 10, 0);
            $startTime = date_format($date, 'Y-m-d H:i:s');
            $endTime = date_format(date_add($date, date_interval_create_from_date_string('1 day')), 'Y-m-d H:i:s');
            $query->where("time", ">", $startTime)
                ->where('time','<', $endTime);
        }

        if ($reset == 0) {
            $query->whereNull('inflexion');
        }

        $matches = $query->orderBy("time", "asc")->get();

        $this->analyseInflexion($matches);
    }

    public function analyseForOneHour(Request $request){
        $reset = $request->input("isReset", 0);
        $date = date_create();
        $query = $this->getMatchQuery();
        $query->where("time", ">", date_format(date_add($date, date_interval_create_from_date_string('2 hour')), 'Y-m-d H:i:s'))
            ->where('time','<', date_format(date_add($date, date_interval_create_from_date_string('26 hour')), 'Y-m-d H:i:s'));
        if ($reset == 0) {
            $query->whereNull('inflexion');
        }

        $matches = $query->orderBy("time", "asc")->take(50)->get();

        $this->analyseInflexion($matches);
    }

    /**
     * 同赔定时任务
     * @param $request
     */
    public function analyseSameOddForOneHour(Request $request){
        $reset = $request->input("isReset", 0);
        $date = date_create();
        $query = $this->getMatchQuery();
        $query = $query->where("time", ">", date_format(date_add($date, date_interval_create_from_date_string('2 hour')), 'Y-m-d H:i:s'))
            ->where('time','<', date_format(date_add($date, date_interval_create_from_date_string('26 hour')), 'Y-m-d H:i:s'));

        if ($reset == 0) {
            $query->whereNull('same_odd');
        }

        $matches = $query->take(50)->get();
        //同赔
        $this->analyseSameOdd($matches);
    }

    /**
     * 同赔分析
     * @param $matches
     */
    private function analyseSameOdd($matches){
        dump('start');
        if (0 == count($matches)){
            echo 'in 24 hours no match need analyse same odd';
            return;
        }
        $i = 0;
        foreach ($matches as $match){
            $asiaOdds = array();
            $goalOdds = array();
            $ouOdds = array();
            $result = array();
            //亚盘赢
            $asiaWinCount = 0;
            //亚盘走
            $asiaDrawCount = 0;
            //亚盘输
            $asiaLoseCount = 0;
            //亚赔
            $odd = OddCalculateTool::getOdd($match->id,1,Odd::default_banker_id);
            if (isset($odd)) {
                dump($i++ . ' '.$match->id . ' ' . $odd->up1.' '.$odd->middle1. ' ' . $odd->down1);
                $query = OddCalculateTool::queryForHistorySameOdd($odd->up1,$odd->middle1,$odd->down1,1,
                    date_format(date_sub(date_create($match->time),date_interval_create_from_date_string('1 days')),'Y-m-d H:i')
                    ,30,$match->id);
                //同赔赛事
                $sameOddMatches = $query;
//                dump($sameOddMatches);
                if(count($sameOddMatches) >= 5) {
                    for ($i = 0 ; $i < count($sameOddMatches) ; $i++) {
                        $sameOddMatch = $sameOddMatches[$i];
                        if ($i < 10){
                            switch (OddCalculateTool::getMatchAsiaOddResult($sameOddMatch->hscore, $sameOddMatch->ascore, $sameOddMatch->middle2)) {
                                case 3:
                                    $asiaWinCount++;
                                    break;
                                case 1:
                                    $asiaDrawCount++;
                                    break;
                                case 0:
                                    $asiaLoseCount++;
                                    break;
                            }
                        }
                        $asiaOdds[] = $sameOddMatch->id;
                    }
                    //结果
                    $result['asia'] = array(
                        'win' => number_format(100.0 * $asiaWinCount /  min(10,count($sameOddMatches)),0),
                        'draw' => number_format(100.0 * $asiaDrawCount / min(10,count($sameOddMatches)),0),
                        'lose' => number_format(100.0 * $asiaLoseCount / min(10,count($sameOddMatches)),0));
                    dump($result);
                }
                dump('done asia');
            }
            //大小球
            //赢
            $winCount = 0;
            //走
            $drawCount = 0;
            //输
            $loseCount = 0;
            $odd = OddCalculateTool::getOdd($match->id,2, Odd::default_banker_id);
            if (isset($odd)) {
                $query = OddCalculateTool::queryForHistorySameOdd($odd->up1,$odd->middle1,$odd->down1,2,
                    date_format(date_sub(date_create($match->time),date_interval_create_from_date_string('1 days')),'Y-m-d H:i')
                    ,30,$match->id);
                //同赔赛事
                $sameOddMatches = $query;
//                dump($sameOddMatches);
                if(count($sameOddMatches) >= 5) {
                    for ($i = 0 ; $i < count($sameOddMatches) ; $i++) {
                        $sameOddMatch = $sameOddMatches[$i];
                        if ($i < 10) {
                            switch (OddCalculateTool::getMatchSizeOddResult($sameOddMatch->hscore, $sameOddMatch->ascore, $sameOddMatch->middle2)) {
                                case 3:
                                    $winCount++;
                                    break;
                                case 1:
                                    $drawCount++;
                                    break;
                                case 0:
                                    $loseCount++;
                                    break;
                            }
                        }
                        $goalOdds[] = $sameOddMatch->id;
                    }
                    //结果
                    $result['goal'] = array(
                        'win' => number_format(100.0 * $winCount / min(10,count($sameOddMatches)),0),
                        'draw' => number_format(100.0 * $drawCount / min(10,count($sameOddMatches)),0),
                        'lose' => number_format(100.0 * $loseCount / min(10,count($sameOddMatches)),0));
                }
                dump('done goal');
            }
            //欧赔
            //赢
            $winCount = 0;
            //走
            $drawCount = 0;
            //输
            $loseCount = 0;
            $odd = OddCalculateTool::getOdd($match->id,3, Odd::default_banker_id);
            if (isset($odd)) {
                $query = OddCalculateTool::queryForHistorySameOdd($odd->up1,$odd->middle1,$odd->down1,3,
                    date_format(date_sub(date_create($match->time),date_interval_create_from_date_string('1 days')),'Y-m-d H:i')
                    ,30,$match->id);
                //同赔赛事
                $sameOddMatches = $query;
//                dump($sameOddMatches);
                if (count($sameOddMatches) >= 5) {
                    for ($i = 0 ; $i < count($sameOddMatches) ; $i++) {
                        $sameOddMatch = $sameOddMatches[$i];
                        if ($i < 10) {
                            switch (OddCalculateTool::getMatchResult($sameOddMatch->hscore, $sameOddMatch->ascore)) {
                                case 3:
                                    $winCount++;
                                    break;
                                case 1:
                                    $drawCount++;
                                    break;
                                case 0:
                                    $loseCount++;
                                    break;
                            }
                        }
                        $ouOdds[] = $sameOddMatch->id;
                    }
                    //结果
                    $result['ou'] = array(
                        'win' => number_format(100.0 * $winCount / min(10,count($sameOddMatches)),0),
                        'draw' => number_format(100.0 * $drawCount / min(10,count($sameOddMatches)),0),
                        'lose' => number_format(100.0 * $loseCount / min(10,count($sameOddMatches)),0));
                }
                dump('done ou');
            }

            //保存到表
            $sameOdd = MatchAnalysisSameOdd::where('mid',$match->id)
                ->where('cid', Odd::default_banker_id)
                ->first();
            if (is_null($sameOdd)){
                $sameOdd = new MatchAnalysisSameOdd();
                $sameOdd->cid = Odd::default_banker_id;
                $sameOdd->mid = $match->id;
            }
            if (isset($result['asia'])) {
                $sameOdd->asia_win = $result['asia']['win'];
                $sameOdd->asia_lose = $result['asia']['lose'];
                $sameOdd->analysis_asia_ids = implode(',',$asiaOdds);
            }
            if (isset($result['goal'])) {
                $sameOdd->goal_big = $result['goal']['win'];
                $sameOdd->goal_small = $result['goal']['lose'];
                $sameOdd->analysis_goal_ids = implode(',',$goalOdds);
            }
            if (isset($result['ou'])) {
                $sameOdd->match_win = $result['ou']['win'];
                $sameOdd->match_draw = $result['ou']['draw'];
                $sameOdd->match_lose = $result['ou']['lose'];
                $sameOdd->analysis_ou_ids = implode(',',$ouOdds);
            }
            $sameOdd->time = $match->time;
            $sameOdd->save();

            $match->same_odd = 1;
            $match->save();
            dump('done');
        }
    }

    /**
     * 拐点分析
     * @param $matches 需要分析的比赛
     */
    private function analyseInflexion($matches) {
        if(0 == count($matches)){
            echo 'in 24 hours no match need analyse';
            return;
        }
        $result = array();
        $size = 4;
//        dump(count($matches));
//        dump(date_format(date_create(), 'y-m-d h:i:s'));
        foreach ($this->bankerIds as $cid) {
            foreach ($matches as $match) {
                //主队
                $resultHost = $this->getResultOfTeam($match->id, $match->time, $cid, $match->hid, $size, true);
                if ($resultHost != null && count($resultHost) > 0) {
                    $result = array_merge($result, $resultHost);
                }
                //客队
                $resultAway = $this->getResultOfTeam($match->id, $match->time, $cid, $match->aid, $size, false);
                if ($resultAway != null && count($resultAway) > 0) {
                    $result = array_merge($result, $resultAway);
                }
            }
        }
//        dump(date_format(date_create(), 'y-m-d h:i:s'));
//        dump($result);
        //保存到分析表
        foreach ($result as $analyse){
            $isHost = $analyse['isHost']?1:0;
            $inflexion = Inflexion::where('mid','=', $analyse['mid'])
                ->where('cid','=',$analyse['cid'])
                ->where('is_host','=',$isHost)->first();
            if (is_null($inflexion)){
                $inflexion = new Inflexion();
                $inflexion->mid = $analyse['mid'];
                $inflexion->cid = $analyse['cid'];
            }
            $change = false;
            //亚盘
            if (isset($analyse['resultOddCount'])){
                if ($analyse['resultOddCount'] > 0) {
                    $inflexion->asia_win = $analyse['resultOddCount'];
                    $inflexion->asia_lose = 0;
                    $change = true;
                } else{
                    $inflexion->asia_lose = -$analyse['resultOddCount'];
                    $inflexion->asia_win = 0;
                    $change = true;
                }

                if (isset($analyse['asia_ids'])){
                    $inflexion->asia_ids = implode(',',$analyse['asia_ids']);
                }
            }
            //输赢
            if (isset($analyse['resultWinCount'])){
                if ($analyse['resultWinCount'] > 0) {
                    $inflexion->match_win = $analyse['resultWinCount'];
                    $inflexion->match_lose = 0;
                    $change = true;
                } else{
                    $inflexion->match_win = 0;
                    $inflexion->match_lose = -$analyse['resultWinCount'];
                    $change = true;
                }

                if (isset($analyse['result_ids'])){
                    $inflexion->result_ids = implode(',',$analyse['result_ids']);
                }
            }
            if (isset($analyse['resultDrawCount'])){
                $inflexion->match_draw = $analyse['resultDrawCount'];
                $change = true;

                if (isset($analyse['result_ids'])){
                    $inflexion->result_ids = implode(',',$analyse['result_ids']);
                }
            }
            //大小球
            if (isset($analyse['resultGoal'])){
                if ($analyse['resultGoal'] > 0) {
                    $inflexion->goal_big = $analyse['resultGoal'];
                    $inflexion->goal_small = 0;
                    $change = true;
                } else{
                    $inflexion->goal_small = -$analyse['resultGoal'];
                    $inflexion->goal_big = 0;
                    $change = true;
                }

                if (isset($analyse['goal_ids'])){
                    $inflexion->goal_ids = implode(',',$analyse['goal_ids']);
                }
            }
            if ($change) {
                $inflexion->time = $analyse['time'];
                $inflexion->is_host = $analyse['isHost']?1:0;
                echo 'result ' .$inflexion . '</br>';
                $inflexion->save();
            }
            else{
                echo 'no match matches'.'</br>';
            }
        }
        foreach ($matches as $match){
            echo 'match '.$match . '</br>';
            $match->inflexion = 1;
            $match->save();
        }
    }

    private function getResultOfTeam($mid, $time, $cid, $tid, $size,$isHost){
        $result = array();
        $recentMatches = $this->getRecentMatches($tid, $cid);
        $tmp['mid'] = $mid;
        $tmp['cid'] = $cid;
        $tmp['time'] = $time;
        $tmp['isHost'] = $isHost;

        //保存比赛id
        $asiaIds = array();
        $goalIds = array();
        $resultIds = array();

        //亚盘
        $hodd = $this->countOfContinueAsiaOdd($tid,$recentMatches);
        if ($size <= abs($hodd['count']))
        {
            foreach ($hodd['matchIds'] as $tmpId){
                $asiaIds[] = $tmpId;
            }
            $tmp['asia_ids'] = $asiaIds;
            $tmp['resultOddCount'] = $hodd['count'];
        }
        //胜平负
        $hodd = $this->countOfContinueResultOdd($tid,$recentMatches);
        if ($size <= abs($hodd['win']) || $size <= $hodd['draw'])
        {
            foreach ($hodd['matchIds'] as $tmpId){
                $resultIds[] = $tmpId;
            }
            $tmp['result_ids'] = $resultIds;
            $tmp['resultDrawCount'] = $hodd['draw'];
            $tmp['resultWinCount'] = $hodd['win'];
        }
        //大小球
        $hodd = $this->countOfGoal($recentMatches);
        if($size <= abs($hodd['count'])) {
            foreach ($hodd['matchIds'] as $tmpId){
                $goalIds[] = $tmpId;
            }
            $tmp['goal_ids'] = $goalIds;
            $tmp['resultGoal'] = $hodd['count'];
        }

        array_push($result, $tmp);
        return $result;
    }

    /**
     * 最近比赛
     * @param $tid 球队id
     * @return mixed 最近比赛数组
     */
    public function getRecentMatches($tid, $cid)
    {
        $date = date_create();
        //tid这个联赛,这个赛季,所有结束的比赛
        $recentHostMatches = Match::query()
            ->leftJoin('odds as asia', function ($join) use($cid) {
                $join->on('matches.id', '=', 'asia.mid');
                $join->where('asia.cid', $cid)->where('asia.type', 1);
            })->leftJoin('odds as goal', function ($join) use($cid) {
                $join->on('matches.id', '=', 'goal.mid');
                $join->where('goal.cid', $cid)->where('goal.type', 2);
            })->select('matches.*', 'asia.middle2 as asiamiddle2', 'goal.middle2 as goalmiddle2')
            //不需要相同比赛
//            where('lid','=',$matchData->lid)
//            ->where('season','=',$matchData->season)
//            where('time','>',date_format(date_sub($date, date_interval_create_from_date_string('18 months')), 'Y-m-d H:i:s'))
            ->where('status', '=', -1)
            ->where(function ($q)use($tid){
                $q->where('hid','=', $tid)
                    ->orWhere('aid','=', $tid);
            })
            ->orderBy('time', 'desc')
            ->take(50)
            ->get();
        return $recentHostMatches;
    }

    /**
     * 最近比赛亚盘连X多少场
     * @param $tid 球队id
     * @param $recentHostMatches 这个球队最近的比赛
     * @return int 赢正数 输负数
     */
    public function countOfContinueAsiaOdd($tid, $recentHostMatches)
    {
//        echo 'bj: '. $tid .' ' .$recentHostMatches.'</br>';
        if (null == $recentHostMatches)
        {
            return 0;
        }
        //亚盘连X数
        $oddCount = 0;
        //走连续
        $isWin = 0;
        //记录统计的是哪些比赛
        $markMatchIds = array();

        foreach ($recentHostMatches as $tmp)
        {
            if ($tmp->asiamiddle2)
            {
                $middle = $tmp->asiamiddle2;
                $isHome = $tmp->hid == $tid;
                $isGuest = $tmp->aid == $tid;
                //很特殊情况,假设那些最近比赛都没有这个tid,感觉这个方法可能被误解,先这样做一下
                if ($isGuest || $isHome) {
                    $result = OddCalculateTool::getMatchAsiaOddResult($tmp->hscore, $tmp->ascore, $middle, $isHome);
                    if (3 == $result)
                    {
                        //赢
                        if ($isWin == 0 || $isWin == 1) {
                            $markMatchIds[] = $tmp->id;
                            $oddCount++;
                            $isWin = 1;
                        }
                        else
                            break;
                    }
                    else if(0 == $result){
                        //输
                        if ($isWin == 0 || $isWin == -1) {
                            $markMatchIds[] = $tmp->id;
                            $oddCount--;
                            $isWin = -1;
                        }
                        else
                            break;
                    }
                }
            }
        }
        return array('count'=>$oddCount,'matchIds'=>$markMatchIds);
    }

    /**
     * 最近比赛连赢多少场,输多少场,array('win'=>1,'dran'=>0)
     * @param $tid 球队id
     * @param $recentHostMatches 这个球队最近的比赛
     * @return 连续赢x场,负数为连输
     */
    public function countOfContinueResultOdd($tid,$recentHostMatches)
    {
//        echo 'bj: '. $tid .' ' .$recentHostMatches.'</br>';
        //连胜数
        $winCount = 0;
        //连平数
        $drawCount = 0;
        //记录统计的是哪些比赛
        $markMatchIds = array();
        for($i = 0; $i < count($recentHostMatches) ; $i++)
        {
            $tmp = $recentHostMatches[$i];
            $result = OddCalculateTool::getMatchResult($tmp->hscore, $tmp->ascore, $tmp->hid == $tid);
            switch ($result) {
                case 3://胜
                    if ($i == abs($winCount) && $winCount >= 0 && $drawCount == 0) {
                        $markMatchIds[] = $tmp->id;
                        $winCount++;
                    }
                    break;
                case 1://平
                    if ($i == abs($drawCount) && $winCount == 0) {
                        $markMatchIds[] = $tmp->id;
                        $drawCount++;
                    }
                    break;
                case 0://负
                    if ($i == abs($winCount) && $winCount <= 0 && $drawCount == 0) {
                        $markMatchIds[] = $tmp->id;
                        $winCount--;
                    }
                    break;
            }
        }
        $result = array('win'=>$winCount,'draw'=>$drawCount,'matchIds'=>$markMatchIds);
//        echo 'bj: '. $tid .' ' .$result['win'].' '. $result['draw'].'</br>';
        return $result;
    }

    /**
     * 连续大小球
     * @param $recentHostMatches 球队最近比赛
     * @return int 赢正数 输负数
     */
    public function countOfGoal($recentHostMatches)
    {
        $result = 0;
        //走要连续 1赢 -1负 0平
        $isWin = 0;
        //记录统计的是哪些比赛
        $markMatchIds = array();
        for($i = 0; $i < count($recentHostMatches) ; $i++)
        {
            $tmp = $recentHostMatches[$i];
            if ($tmp->goalmiddle2)
            {
                $middle = $tmp->goalmiddle2;
                $goalResult = OddCalculateTool::getMatchSizeOddResult($tmp->hscore, $tmp->ascore, $middle);
                if (3 == $goalResult)
                {
                    //赢
                    if ($isWin == 1 || $isWin == 0) {
                        $markMatchIds[] = $tmp->id;
                        $result++;
                        $isWin = 1;
                    }
                    else
                        break;
                }
                elseif (0 ==$goalResult){
                    //输
                    if ($isWin == -1 || $isWin == 0) {
                        $markMatchIds[] = $tmp->id;
                        $result--;
                        $isWin = -1;
                    }
                    else
                        break;
                }
            }
            else{
                break;
            }
        }
        return array('count'=>$result,'matchIds'=>$markMatchIds);
    }
}