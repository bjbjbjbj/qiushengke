<?php
/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/2/27
 * Time: 18:55
 */

namespace App\Http\Controllers\Statistic\Terminal\Football;


use App\Http\Controllers\Statistic\OddCalculateTool;
use App\Models\AnalyseModels\Match;
use App\Models\AnalyseModels\MatchAnalysisSameOdd;
use App\Models\AnalyseModels\Odd;

trait FootballSameOddTool
{
    /**
     * 比赛的历史同赔
     */
    public function sameOdd($match, $same_odd, $reset = false) {
        if (!$reset) {
            //如果有数据了则不再更新数据
            if (isset($same_odd)) {
                return $same_odd;
            }
        }

        $mid = $match->id;

        $rest['asia'] = null;
        $rest['goal'] = null;
        $rest['ou'] = null;

        //同赔
        $sameOdd = $this->getSameOdd(1, $mid);
        if (isset($sameOdd['sameOdd']) && count($sameOdd['sameOdd']) > 0) {
            $rest['asia'] = $sameOdd['sameOdd'];
        }
        $sameOdd2 = $this->getSameOdd(2, $mid);
        if (isset($sameOdd2['sameOdd']) && count($sameOdd2['sameOdd']) > 0) {
            $rest['goal']= $sameOdd2['sameOdd'];
        }
        $sameOdd3 = $this->getSameOdd(3, $mid);
        if (isset($sameOdd2['sameOdd']) && count($sameOdd2['sameOdd']) > 0) {
            $rest['ou'] = $sameOdd3['sameOdd'];
        }

        return $rest;
    }

    /**
     * 历史同赔
     * @param $type
     * @param $id
     * @return mixed
     */
    private function getSameOdd($type,$id){
        if ($type == 1 || 2 == $type || 3 == $type) {
            //同赔
            //亚盘赢
            $asiaWinCount = 0;
            $asiaWinCount10 = 0;
            $asiaWinCount20 = 0;
            $asiaWinCount30 = 0;
            //亚盘走
            $asiaDrawCount = 0;
            $asiaDrawCount10 = 0;
            $asiaDrawCount20 = 0;
            $asiaDrawCount30 = 0;
            //亚盘输
            $asiaLoseCount = 0;
            $asiaLoseCount10 = 0;
            $asiaLoseCount20 = 0;
            $asiaLoseCount30 = 0;
            //亚赔
            $bet365 = OddCalculateTool::getOdd($id, $type, Odd::default_banker_id);
            $match = Match::where('id', $id)->first();
            if (isset($bet365) && isset($match)) {
                $sameOddMatches = array();
                $sameOdd = MatchAnalysisSameOdd::where('mid',$id)
                    ->where('cid',Odd::default_calculate_cid)
                    ->first();
                if (isset($sameOdd)){
                    switch ($type){
                        case 1:{
                            if (isset($sameOdd->analysis_asia_ids)) {
                                $ids = explode(',',$sameOdd->analysis_asia_ids);
                                $sameOddMatches = Match::whereIn('matches.id',$ids)
                                    ->leftjoin('leagues',function ($q){
                                        $q->on('leagues.id','=','matches.lid');
                                    })
                                    ->join('odds',function ($q){
                                        $q->on('odds.mid','=','matches.id')
                                            ->where('odds.type',1)
                                            ->where('odds.cid',Odd::default_banker_id);
                                    })
                                    ->addSelect('matches.id','matches.time','matches.status','matches.hid','matches.aid','matches.lid',
                                        'matches.hscore','matches.ascore','matches.hscorehalf','matches.ascorehalf',
                                        'matches.hname','matches.aname', 'leagues.name as league')
                                    ->addSelect('odds.id as odd_id',
                                        'odds.up1 as up1','odds.middle1 as middle1','odds.down1 as down1',
                                        'odds.up2 as up2','odds.middle2 as middle2','odds.down2 as down2')
                                    ->orderby('matches.time','desc')
                                    ->orderby('matches.id','desc')
                                    ->get();
                            }
                        }
                            break;
                        case 2:{
                            if (isset($sameOdd->analysis_goal_ids)) {
                                $ids = explode(',',$sameOdd->analysis_goal_ids);
                                $sameOddMatches = Match::whereIn('matches.id',$ids)
                                    ->leftjoin('leagues',function ($q){
                                        $q->on('leagues.id','=','matches.lid');
                                    })
                                    ->join('odds',function ($q){
                                        $q->on('odds.mid','=','matches.id')
                                            ->where('odds.type',2)
                                            ->where('odds.cid',Odd::default_banker_id);
                                    })
                                    ->addSelect('matches.id','matches.time','matches.status','matches.hid','matches.aid','matches.lid',
                                        'matches.hscore','matches.ascore','matches.hscorehalf','matches.ascorehalf',
                                        'matches.hname','matches.aname', 'leagues.name as league')
                                    ->addSelect('odds.id as odd_id',
                                        'odds.up1 as up1','odds.middle1 as middle1','odds.down1 as down1',
                                        'odds.up2 as up2','odds.middle2 as middle2','odds.down2 as down2')
                                    ->orderby('matches.time','desc')
                                    ->orderby('matches.id','desc')
                                    ->get();
                            }
                        }
                            break;
                        case 3:{
                            if (isset($sameOdd->analysis_ou_ids)) {
                                $ids = explode(',',$sameOdd->analysis_ou_ids);
                                $sameOddMatches = Match::whereIn('matches.id',$ids)
                                    ->leftjoin('leagues',function ($q){
                                        $q->on('leagues.id','=','matches.lid');
                                    })
                                    ->join('odds',function ($q){
                                        $q->on('odds.mid','=','matches.id')
                                            ->where('odds.type',3)
                                            ->where('odds.cid',Odd::default_banker_id);
                                    })
                                    ->addSelect('matches.id','matches.time','matches.status','matches.hid','matches.aid','matches.lid',
                                        'matches.hscore','matches.ascore','matches.hscorehalf','matches.ascorehalf',
                                        'matches.hname','matches.aname', 'leagues.name as league')
                                    ->addSelect('odds.id as odd_id',
                                        'odds.up1 as up1','odds.middle1 as middle1','odds.down1 as down1',
                                        'odds.up2 as up2','odds.middle2 as middle2','odds.down2 as down2')
                                    ->orderby('matches.time','desc')
                                    ->orderby('matches.id','desc')
                                    ->get();
                            }
                        }
                            break;
                    }
                }

                //如果记录没有
                if (count($sameOddMatches) == 0){
                    $query = OddCalculateTool::queryForHistorySameOddForDetail($bet365->up1, $bet365->middle1, $bet365->down1, $bet365->type,
                        date_format(date_sub(date_create($match->time), date_interval_create_from_date_string('1 days')), 'Y-m-d H:i')
                        , 20, $id);
                    //同赔赛事
                    $sameOddMatches = $query;
                }

                $asiaIds = array();
                $goalIds = array();
                $ouIds = array();
                $result = array();
                if (count($sameOddMatches) > 5) {
                    $count = 0;
                    foreach ($sameOddMatches as $sameOddMatch) {
                        $sameOddMatch->up1 = OddCalculateTool::formatOddItem($sameOddMatch, 'up1');
                        $sameOddMatch->up2 = OddCalculateTool::formatOddItem($sameOddMatch, 'up2');
                        $sameOddMatch->down1 = OddCalculateTool::formatOddItem($sameOddMatch, 'down1');
                        $sameOddMatch->down2 = OddCalculateTool::formatOddItem($sameOddMatch, 'down2');

                        switch ($type) {
                            case 1: {
                                switch (OddCalculateTool::getMatchAsiaOddResult($sameOddMatch->hscore, $sameOddMatch->ascore, $sameOddMatch->middle1, true)) {
                                    case 3:
                                        if ($count < 10) {
                                            $asiaWinCount10++;
                                        }
                                        if ($count < 20) {
                                            $asiaWinCount20++;
                                        }
                                        if ($count < 30) {
                                            $asiaWinCount30++;
                                        }
                                        $asiaWinCount++;
                                        $result[] = 3;
                                        break;
                                    case 1:
                                        if ($count < 10) {
                                            $asiaDrawCount10++;
                                        }
                                        if ($count < 20) {
                                            $asiaDrawCount20++;
                                        }
                                        if ($count < 30) {
                                            $asiaDrawCount30++;
                                        }
                                        $asiaDrawCount++;
                                        $result[] = 1;
                                        break;
                                    case 0:
                                        if ($count < 10) {
                                            $asiaLoseCount10++;
                                        }
                                        if ($count < 20) {
                                            $asiaLoseCount20++;
                                        }
                                        if ($count < 30) {
                                            $asiaLoseCount30++;
                                        }
                                        $asiaLoseCount++;
                                        $result[] = 0;
                                        break;
                                }
                                $asiaIds[] = $sameOddMatch->id;
                                break;
                            }
                            case 2: {
                                switch (OddCalculateTool::getMatchSizeOddResult($sameOddMatch->hscore, $sameOddMatch->ascore, $sameOddMatch->middle1)) {
                                    case 3:
                                        if ($count < 10) {
                                            $asiaWinCount10++;
                                        }
                                        if ($count < 20) {
                                            $asiaWinCount20++;
                                        }
                                        if ($count < 30) {
                                            $asiaWinCount30++;
                                        }
                                        $asiaWinCount++;
                                        $result[] = 3;
                                        break;
                                    case 1:
                                        if ($count < 10) {
                                            $asiaDrawCount10++;
                                        }
                                        if ($count < 20) {
                                            $asiaDrawCount20++;
                                        }
                                        if ($count < 30) {
                                            $asiaDrawCount30++;
                                        }
                                        $asiaDrawCount++;
                                        $result[] = 1;
                                        break;
                                    case 0:
                                        if ($count < 10) {
                                            $asiaLoseCount10++;
                                        }
                                        if ($count < 20) {
                                            $asiaLoseCount20++;
                                        }
                                        if ($count < 30) {
                                            $asiaLoseCount30++;
                                        }
                                        $asiaLoseCount++;
                                        $result[] = 0;
                                        break;
                                }
                                $goalIds[] = $sameOddMatch->id;
                                break;
                            }
                            case 3: {
                                switch (OddCalculateTool::getMatchResult($sameOddMatch->hscore, $sameOddMatch->ascore, true)) {
                                    case 3:
                                        if ($count < 10) {
                                            $asiaWinCount10++;
                                        }
                                        if ($count < 20) {
                                            $asiaWinCount20++;
                                        }
                                        if ($count < 30) {
                                            $asiaWinCount30++;
                                        }
                                        $asiaWinCount++;
                                        $result[] = 3;
                                        break;
                                    case 1:
                                        if ($count < 10) {
                                            $asiaDrawCount10++;
                                        }
                                        if ($count < 20) {
                                            $asiaDrawCount20++;
                                        }
                                        if ($count < 30) {
                                            $asiaDrawCount30++;
                                        }
                                        $asiaDrawCount++;
                                        $result[] = 1;
                                        break;
                                    case 0:
                                        if ($count < 10) {
                                            $asiaLoseCount10++;
                                        }
                                        if ($count < 20) {
                                            $asiaLoseCount20++;
                                        }
                                        if ($count < 30) {
                                            $asiaLoseCount30++;
                                        }
                                        $asiaLoseCount++;
                                        $result[] = 0;
                                        break;
                                }
                                $ouIds[] = $sameOddMatch->id;
                                break;
                            }
                        }
                        $count++;
                    }
                    $matches = array();
                    for ($i = 0; $i < min($count, count($sameOddMatches)); $i++) {
                        $matches[] = $sameOddMatches[$i];
                    }
                    //结果
                    $rest['sameOdd'] = array(
                        'win' => number_format(100.0 * $asiaWinCount / count($sameOddMatches), 0),
                        'draw' => number_format(100.0 * $asiaDrawCount / count($sameOddMatches), 0),
                        'lose' => number_format(100.0 * $asiaLoseCount / count($sameOddMatches), 0),
                        'win10' => number_format(100.0 * $asiaWinCount10 / min(10, count($sameOddMatches)), 0),
                        'draw10' => number_format(100.0 * $asiaDrawCount10 / min(10, count($sameOddMatches)), 0),
                        'lose10' => number_format(100.0 * $asiaLoseCount10 / min(10, count($sameOddMatches)), 0),
                        'win20' => number_format(100.0 * $asiaWinCount20 / min(20, count($sameOddMatches)), 0),
                        'draw20' => number_format(100.0 * $asiaDrawCount20 / min(20, count($sameOddMatches)), 0),
                        'lose20' => number_format(100.0 * $asiaLoseCount20 / min(20, count($sameOddMatches)), 0),
                        'win30' => number_format(100.0 * $asiaWinCount30 / min(30, count($sameOddMatches)), 0),
                        'draw30' => number_format(100.0 * $asiaDrawCount30 / min(30, count($sameOddMatches)), 0),
                        'lose30' => number_format(100.0 * $asiaLoseCount30 / min(30, count($sameOddMatches)), 0),
                        'count' => count($sameOddMatches),
                        'matches' => $matches,
                        'result' => $result
                    );
                    $sameOdd = MatchAnalysisSameOdd::where('mid', $id)
                        ->where('cid',Odd::default_calculate_cid)
                        ->first();
                    if (isset($sameOdd)) {
                        switch ($type) {
                            case 1:
                                $sameOdd->asia_win = $rest['sameOdd']['win10'];
                                $sameOdd->asia_lose = $rest['sameOdd']['lose10'];
                                $sameOdd->analysis_asia_ids = implode(',',$asiaIds);
                                break;
                            case 2:
                                $sameOdd->goal_big = $rest['sameOdd']['win10'];
                                $sameOdd->goal_small = $rest['sameOdd']['lose10'];
                                $sameOdd->analysis_goal_ids = implode(',',$goalIds);
                                break;
                            case 3:
                                $sameOdd->match_win = $rest['sameOdd']['win10'];
                                $sameOdd->match_draw = $rest['sameOdd']['draw10'];
                                $sameOdd->match_lose = $rest['sameOdd']['lose10'];
                                $sameOdd->analysis_ou_ids = implode(',',$ouIds);
                                break;
                        }
                        $sameOdd->save();
                    }
                    return $rest;
                }
            }
        }
        return null;
    }
}