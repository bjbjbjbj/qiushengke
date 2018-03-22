<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2017/10/31
 * Time: 下午5:01
 */
namespace App\Http\Controllers\App\MatchDetail\Football;

use App\Http\Controllers\App\MatchDetail\Football\Tool\OddCalculateTool;
use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\MatchAnalysisSameOdd;
use App\Models\LiaoGouModels\Odd;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MatchDetailOddController extends Controller{

    /**
     * 历史同赔
     * @param Request $request
     * @param $mid
     * @return view
     */
    public function sameOdd(Request $request,$mid){
        $result['data'] = MatchDetailOddController::getSameOdd($request->input('type',1),$mid)['sameOdd'];
        $result['type'] = $request->input('type',1);
        $result['count'] = $request->input('count',10);
        $result['result'] = '1';
        return view('pc.match.matchDetail.components.characteristic_odd_item',$result);
    }

    /**
     * 历史同赔
     * @param $type
     * @param $id
     * @param $count
     * @return null
     */
    static public function getSameOdd($type,$id,$count = 10){
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
            $bet365 = OddCalculateTool::getOdd($id, $type, Odd::default_calculate_cid);
            $match = Match::where('id', $id)->first();
            if (isset($bet365)) {
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
                                            ->where('odds.cid',Odd::default_calculate_cid);
                                    })
                                    ->select('matches.*','odds.id as odd_id','leagues.name as lname',
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
                                            ->where('odds.cid',Odd::default_calculate_cid);
                                    })
                                    ->select('matches.*','odds.id as odd_id','leagues.name as lname',
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
                                            ->where('odds.cid',Odd::default_calculate_cid);
                                    })
                                    ->select('matches.*','odds.id as odd_id','leagues.name as lname',
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