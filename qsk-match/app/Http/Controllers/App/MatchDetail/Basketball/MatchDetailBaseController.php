<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 17/2/5
 * Time: 下午4:34
 */

namespace App\Http\Controllers\App\MatchDetail\Basketball;

use App\Http\Controllers\App\MatchDetail\Basketball\Tool\RecentBattleTool;
use App\Http\Controllers\App\MatchDetail\Football\Tool\OddCalculateTool;
use App\Models\LiaoGouModels\Banker;
use App\Models\LiaoGouModels\BasketMatch;
use App\Models\LiaoGouModels\BasketOdd;
use App\Models\LiaoGouModels\BasketTeam;
use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\MatchLive;
use App\Models\LiaoGouModels\Odd;
use App\Models\LiaoGouModels\Score;
use App\Models\LiaoGouModels\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\View\View;

class MatchDetailBaseController extends Controller {

    use RecentBattleTool;

    public function matchLive(Request $request,$mid){
        $lives = MatchLive::where('match_id', $mid)
            ->where('sport', MatchLive::kSportBasketball)
            ->count();
        if ($lives > 0) {
            $match['live'] = 1;
        } else {
            $match['live'] = 0;
        }
        $m = BasketMatch::find($mid);
        if (isset($m)) {
            if ($m->status <= 0) {
                $match['live'] = 0;
            }
        }
        else{
            $match['live'] = 0;
        }
        return \Illuminate\Support\Facades\Response::json($match);
    }

    /**
     * 赔率公司
     * @param Request $request
     * @return View
     */
    public function matchOddBankerList(Request $request,$mid){
        $rest = $this->matchOddBankerData($mid);
        return view('pc.match.matchDetail.components.odd', $rest);
    }

    public function matchOddBankerData($mid) {
        $rest = array();
        $bankers = BasketOdd::where('mid',$mid)
            ->whereIn('cid',[2, 5, 12])
            ->groupby('cid')
            ->select('cid')
            ->get();

        $ids = array();
        foreach ($bankers as $tmp){
            $ids[] = $tmp['cid'];
        }
        $bankers = Banker::whereIn('id',$ids)
            ->orderBy(DB::raw('FIELD(id, 2, 5, 12)'))
            ->get();
        $rest['bankers'] = $bankers;

        $odds = array();
        if (count($bankers) > 0){
            $odds = BasketOdd::where('mid',$mid)
                ->where('type','<',4)
                ->get();
        }
        $tmp = array();
        foreach ($odds as $odd){
            foreach ($bankers as $banker){
                if ($banker['id'] == $odd['cid']){
                    if ($odd['type'] == 1){
                        $banker['asia'] = $odd;
                    }
                    elseif ($odd['type'] == 2){
                        $banker['goal'] = $odd;
                    }
                    elseif ($odd['type'] == 3){
                        $banker['ou'] = $odd;
                    }
                }
            }
        }

        $rest['bankers'] = $bankers;
        $rest['mid'] = $mid;
        return $rest;
    }

    /**
     * 新seo用静态页面返回数据
     * @param $mid
     * @return array
     */
    public function matchDetailBaseData($mid){
        //缓存
        $cacheSession = $mid.'_basket_matchDetailBase';
        $match = BasketMatch::where('basket_matches.id','=',$mid)
            ->leftjoin('basket_leagues','lid', '=', 'basket_leagues.id')
            ->select('basket_matches.*', 'basket_leagues.type as leagueType')
            ->first();

        $odd = BasketOdd::where('mid','=',$mid)
            ->where('type','=',Odd::k_odd_type_europe)
            ->selectRaw('avg(up1) as up1,avg(middle1) as middle1,avg(down1) as down1')
            ->first();

//        $sameOdd = MatchAnalysisSameOdd::where('mid',$mid)->first();

//        dump($sameOdd);

        if (Redis::exists($cacheSession)){
            $cache = Redis::get($cacheSession);
            $cache = json_decode($cache,true);
            $cache['host'] = $match->hname;
            $cache['away'] = $match->aname;
//            if (isset($cache['leagueRank'])){
//                $resultRank = $cache['leagueRank'];
//                if (isset($resultRank['hLeagueRank'])){
//                    $match['hLeagueRank'] = $resultRank['hLeagueRank'];
//                }
//                else{
//                    $match['hLeagueRank'] = 0;
//                }
//                if (isset($resultRank['hLeagueName'])){
//                    $match['hLeagueName'] = $resultRank['hLeagueName'];
//                }
//                else{
//                    $match['hLeagueName'] = null;
//                }
//                unset($resultRank['host']['league']);
//
//                if (isset($resultRank['aLeagueRank'])){
//                    $match['aLeagueRank'] = $resultRank['aLeagueRank'];
//                }
//                else{
//                    $match['aLeagueRank'] = 0;
//                }
//                if (isset($resultRank['aLeagueName'])){
//                    $match['aLeagueName'] = $resultRank['aLeagueName'];
//                }
//                else{
//                    $match['aLeagueName'] = null;
//                }
//            }
            $cache['matches'] = $match;
            $cache['aid'] = $match->aid;
            $cache['hid'] = $match->hid;
            $cache['lid'] = $match->lid;
            $cache['odd'] = $odd;
            return $cache;
        }

        $rest = array();
        if (is_null($match)){
            $rest['host'] = $match->hname;
            $rest['away'] = $match->aname;
            $rest['nodata'] = 1;
            return $rest;
        }
        else{
            if (is_null($match->hid) || is_null($match->aid)) {
                $rest['host'] = $match->hname;
                $rest['away'] = $match->aname;
                $rest['nodata'] = 1;
                $rest['matches'] = $match;
                return $rest;
            }
        }

        //队名
        $rest['host'] = $match->hname;
        $rest['away'] = $match->aname;
        $rest['hid'] = $match->hid;
        $rest['aid'] = $match->aid;
        $rest['lid'] = $match->lid;

        //交往战绩
        $resultHistoryBattle = $this->matchBaseHistoryBattle($match);
        //处理交往战绩
        $resultNHNL = array();
        $resultSHNL = array();
        $resultNHSL = array();
        $resultSHSL = array();
        foreach ($resultHistoryBattle as $tmp){
            if ($tmp['hid'] == $match['hid'] && $tmp['aid'] == $match['aid']){
                if ($tmp['lid'] == $match['lid']){
                    $resultSHSL[] = $tmp;
                }
                $resultSHNL[] = $tmp;
            }

            if ($tmp['lid'] == $match['lid']){
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
        $resultHistoryBattleResult = $this->matchBaseHistoryBattleResult($resultHistoryBattle,$match->hid,$match->aid,$match->lid);

        //最近战绩
        $resultRecentlyBattle = $this->recentBaseBattle($match);

        //盘路
        $resultOddResultH = $this->matchBaseOdd($match,$match->hid);
        $resultOddResultA = $this->matchBaseOdd($match,$match->aid);

        //未来赛程
        $resultSchedule = $this->matchSchedule($match);

        //攻防能力
//        $cacheSessionh = $mid.'_'.$match->hid.'_matchDetailBase';
//        $cacheSessiona = $mid.'_'.$match->aid.'_matchDetailBase';
//        if (Redis::exists($cacheSessionh)){
//            $cache = Redis::get($cacheSessionh);
//            $rest['attribute']['home'] = json_decode($cache,true);
//        }
//        else{
//            $rest['attribute']['home'] = MatchDetailStrengthController::teamAttributeWithTid($match->hid,$match->time,true,$match->lid,$cacheSessionh);
//        }
//
//        if (Redis::exists($cacheSessiona)){
//            $cache = Redis::get($cacheSessiona);
//            $rest['attribute']['away'] = json_decode($cache,true);
//        }
//        else{
//            $rest['attribute']['away'] = MatchDetailStrengthController::teamAttributeWithTid($match->aid,$match->time,false,$match->lid,$cacheSessiona);
//        }

        //结果
        if (count($resultNHNL) + count($resultSHNL) + count($resultNHSL) + count($resultSHSL) > 0) {
            $rest['historyBattle'] = $resultHistoryBattleNew;
            $rest['historyBattleResult'] = $resultHistoryBattleResult;
        }
        $rest['recentBattle'] = $resultRecentlyBattle;
        if (!isset($resultOddResultH) && !isset($resultOddResultA)) {
            $rest['oddResult'] = NULL;
        } else {
            $rest['oddResult']['home'] = $resultOddResultH;
            $rest['oddResult']['away'] = $resultOddResultA;
        }

        $rest['schedule'] = $resultSchedule;
        $rest['matches'] = $match;

        $rest['odd'] = $odd;

        Redis::set($cacheSession,json_encode($rest));
        //设置过期时间 12小时
        Redis::expire($cacheSession, 60*60*12);
        return $rest;
    }

    /**
     * 获取当前比赛的,球队的盘路
     * @param $currentMatch 当前比赛,盘路需要这个比赛的lid与season搜索
     * @param $tid 球队id
     * @return array
     */
    private function matchBaseOdd($currentMatch, $tid){
        $result = array();
        $cid = BasketOdd::default_calculate_cid;
        //总
        $matches = BasketMatch::where('lid',$currentMatch->lid)
            ->where('status',-1)
//            ->where('season',$currentMatch->season)
            ->where(function ($q) use($tid){
                $q->where('hid',$tid)
                    ->orwhere('aid',$tid);
            })
            ->join('basket_odds as asia',function ($join) use($cid){
                $join->on('basket_matches.id', '=', 'asia.mid');
                $join->where('asia.type','=',1);
                $join->where('asia.cid','=',$cid);
            })
            ->leftjoin('basket_odds as ou',function ($join) use($cid){
                $join->on('basket_matches.id', '=', 'ou.mid');
                $join->where('ou.type','=',2);
                $join->where('ou.cid','=',$cid);
            })
            ->where('time','<',$currentMatch->time)
            ->select('basket_matches.*',
                'asia.up1 as asiaup1','asia.middle1 as asiamiddle1','asia.down1 as asiadown1',
                'asia.up2 as asiaup2','asia.middle2 as asiamiddle2','asia.down2 as asiadown2',
                'ou.up1 as ouup1','ou.middle1 as oumiddle1','ou.down1 as oudown1',
                'ou.up2 as ouup2','ou.middle2 as oumiddle2','ou.down2 as oudown2')
            ->orderby('time','desc')
            ->take(30)->get();
        //如果比赛场次为空则返回空
        if(count($matches) <=0) {
            return NULL;
        }
        //主
        $homeMatches = array();
        //客
        $awayMatches = array();
        $asiaWin = 0;
        $asiaDraw = 0;
        $asiaLose = 0;
        $euWin = 0;
        $euDraw = 0;
        $euLose = 0;
        foreach ($matches as $match){
            if ($match->hid == $tid) {
                $homeMatches[] = $match;
            } else{
                $awayMatches[] = $match;
            }
            //亚盘
            if (isset($match->asiaup2) && isset($match->asiamiddle2) && isset($match->asiadown2)){
                $temp = OddCalculateTool::getMatchAsiaOddResult($match->hscore, $match->ascore, $match->asiamiddle2, $match->hid==$tid);
                switch ($temp) {
                    case 3:
                        $asiaWin++;
                        break;
                    case 1:
                        $asiaDraw++;
                        break;
                    case 0:
                        $asiaLose++;
                        break;
                }
            }
            //大小球
            if (isset($match->ouup2) && isset($match->oumiddle2) && isset($match->oudown2)){
                $temp = OddCalculateTool::getMatchSizeOddResult($match->hscore, $match->ascore, $match->oumiddle2);
                switch ($temp) {
                    case 3:
                        $euWin++;
                        break;
                    case 1:
                        $euDraw++;
                        break;
                    case 0:
                        $euLose++;
                        break;
                }
            }
        }
        $percent = ($asiaLose+$asiaDraw+$asiaWin) > 0 ? number_format((100.0*OddCalculateTool::getOddWinPercent($asiaWin, $asiaDraw, $asiaLose)),2) : '-';
        $percentGoalB = ($euLose+$euDraw+$euWin) > 0 ? number_format((100.0*OddCalculateTool::getOddWinPercent($euWin, $euDraw, $euLose)),2) : '-';
        $percentGoalS = ($euLose+$euDraw+$euWin) > 0 ? number_format((100.0*OddCalculateTool::getOddWinPercent($euWin, $euDraw, $euLose, false)),2) : '-';
        $result['all'] = array('asiaWin'=>$asiaWin,'asiaDraw'=>$asiaDraw,'asiaLose'=>$asiaLose,
            'asiaPercent'=>$percent,
            'goalBig'=>$euWin,'goalSmall'=>$euLose,'goalBigPercent'=>$percentGoalB,'goalSmallPercent'=>$percentGoalS);
        //近6,直接用上面的match
        $sixAsia = array();
        $sixEu = array();
        $sixResult = array();
        $countAsia = 0;
        $countEu = 0;
        foreach ($matches as $match){
            if ($countAsia >= 6 && $countEu >=6)
                break;
            //亚盘
            if (isset($match->asiaup1) && isset($match->asiamiddle1) && isset($match->asiadown1)){
                $countAsia++;
//                dump($match->id.' '.$match->hscore.' '. $match->ascore.' '. $match->asiamiddle2.' ' . ($match->hid==$tid) .' '.OddCalculateTool::getMatchAsiaOddResult($match->hscore, $match->ascore, $match->asiamiddle2, $match->hid==$tid));
                $sixAsia[] = OddCalculateTool::getMatchAsiaOddResult($match->hscore, $match->ascore, $match->asiamiddle1, $match->hid==$tid);
            }
            else{
                $countAsia++;
                $sixAsia[] = -1;
            }
            //大小球
            if (isset($match->ouup1) && isset($match->oumiddle1) && isset($match->oudown1)){
                $countEu++;
                $sixEu[] = OddCalculateTool::getMatchSizeOddResult($match->hscore, $match->ascore, $match->oumiddle1);
            }
            else{
                $countEu++;
                $sixEu[] = -1;
            }
            //胜负
            if (count($sixResult) < 6) {
//                dump($match->hname .' '.$match->hscore.' '.$match->aname .' '.$match->ascore);
                $sixResult[] = OddCalculateTool::getMatchResult($match->hscore, $match->ascore, $match->hid == $tid);
            }
        }
//        dump($sixAsia);
        if (count($sixAsia) < 6){
            $count = count($sixAsia);
            for ($i = 0 ;$i < 6 - $count ; $i++)
                $sixAsia[] = -1;
        }
        if (count($sixEu) < 6){
            $count = count($sixEu);
            for ($i = 0 ;$i < 6 - $count ; $i++)
                $sixEu[] = -1;
        }
        $result['six'] = array('asia'=>$sixAsia,'goal'=>$sixEu,'result'=>$sixResult);
        $sixResult = array();
        //主
        $matches = $homeMatches;
        $asiaWin = 0;
        $asiaDraw = 0;
        $asiaLose = 0;
        $euWin = 0;
        $euDraw = 0;
        $euLose = 0;
        foreach ($matches as $match){
            //亚盘
            if (isset($match->asiaup2) && isset($match->asiamiddle2) && isset($match->asiadown2)){
                $temp = OddCalculateTool::getMatchAsiaOddResult($match->hscore, $match->ascore, $match->asiamiddle2, $match->hid==$tid);
                switch ($temp) {
                    case 3:
                        $asiaWin++;
                        break;
                    case 1:
                        $asiaDraw++;
                        break;
                    case 0:
                        $asiaLose++;
                        break;
                }
            }
            //大小球
            if (isset($match->ouup2) && isset($match->oumiddle2) && isset($match->oudown2)){
                $temp = OddCalculateTool::getMatchSizeOddResult($match->hscore, $match->ascore, $match->oumiddle2);
                switch ($temp) {
                    case 3:
                        $euWin++;
                        break;
                    case 1:
                        $euDraw++;
                        break;
                    case 0:
                        $euLose++;
                        break;
                }
            }
            //胜负
            if (count($sixResult) < 6) {
                $sixResult[] = OddCalculateTool::getMatchResult($match->hscore, $match->ascore, $match->hid == $tid);
            }
        }
        $percent = ($asiaLose+$asiaDraw+$asiaWin) > 0 ? number_format((100.0*OddCalculateTool::getOddWinPercent($asiaWin, $asiaDraw, $asiaLose)),2) : '-';
        $percentGoalB = ($euLose+$euDraw+$euWin) > 0 ? number_format((100.0*OddCalculateTool::getOddWinPercent($euWin, $euDraw, $euLose)),2) : '-';
        $percentGoalS = ($euLose+$euDraw+$euWin) > 0 ? number_format((100.0*OddCalculateTool::getOddWinPercent($euWin, $euDraw, $euLose, false)),2) : '-';
        $result['home'] = array(
            'asiaWin'=>$asiaWin,'asiaDraw'=>$asiaDraw,'asiaLose'=>$asiaLose,
            'asiaPercent'=>$percent,
            'goalBig'=>$euWin,'goalSmall'=>$euLose,'goalBigPercent'=>$percentGoalB,
            'goalSmallPercent'=>$percentGoalS,
            'sixResult'=>$sixResult
        );
        $sixResult = array();
        //客
        $matches = $awayMatches;
        $asiaWin = 0;
        $asiaDraw = 0;
        $asiaLose = 0;
        $euWin = 0;
        $euDraw = 0;
        $euLose = 0;
        foreach ($matches as $match){
            //亚盘
            if (isset($match->asiaup2) && isset($match->asiamiddle2) && isset($match->asiadown2)){
                $temp = OddCalculateTool::getMatchAsiaOddResult($match->hscore, $match->ascore, $match->asiamiddle2, $match->hid==$tid);
                switch ($temp) {
                    case 3:
                        $asiaWin++;
                        break;
                    case 1:
                        $asiaDraw++;
                        break;
                    case 0:
                        $asiaLose++;
                        break;
                }
            }
            //大小球
            if (isset($match->ouup2) && isset($match->oumiddle2) && isset($match->oudown2)){
                $temp = OddCalculateTool::getMatchSizeOddResult($match->hscore, $match->ascore, $match->oumiddle2);
                switch ($temp) {
                    case 3:
                        $euWin++;
                        break;
                    case 1:
                        $euDraw++;
                        break;
                    case 0:
                        $euLose++;
                        break;
                }
            }
            //胜负
            if (count($sixResult) < 6) {
                $sixResult[] = OddCalculateTool::getMatchResult($match->hscore, $match->ascore, $match->hid == $tid);
            }
        }
        $percent = ($asiaLose+$asiaDraw+$asiaWin) > 0 ? number_format((100.0*OddCalculateTool::getOddWinPercent($asiaWin, $asiaDraw, $asiaLose)),2) : '-';
        $percentGoalB = ($euLose+$euDraw+$euWin) > 0 ? number_format((100.0*OddCalculateTool::getOddWinPercent($euWin, $euDraw, $euLose)),2) : '-';
        $percentGoalS = ($euLose+$euDraw+$euWin) > 0 ? number_format((100.0*OddCalculateTool::getOddWinPercent($euWin, $euDraw, $euLose, false)),2) : '-';
        $result['away'] = array(
            'asiaWin'=>$asiaWin,'asiaDraw'=>$asiaDraw,'asiaLose'=>$asiaLose,
            'asiaPercent'=>$percent,
            'goalBig'=>$euWin,'goalSmall'=>$euLose,'goalBigPercent'=>$percentGoalB,
            'goalSmallPercent'=>$percentGoalS,
            'sixResult'=>$sixResult
        );
        return $result;
    }

    /**
     * 比赛双方未来赛程
     * @param $match 当前比赛
     * @return array 分home away主客两个
     */
    private function matchSchedule($match){
        $result = array();
        $result['home'] = $this->getMatchScheduleWithTid($match,$match->hid,$match->hname);
        $result['away'] = $this->getMatchScheduleWithTid($match,$match->aid,$match->aname);
        return $result;
    }

    /**
     * 根据当前比赛 获取tid对应的未来赛程
     * @param $match
     * @param $tid
     * @param $name
     * @return mixed
     */
    private function getMatchScheduleWithTid($match, $tid, $name){
        $matches = BasketMatch::where(function ($q) use($match,$tid,$name){
            $q->where(function ($j) use($match,$tid){
                $j->where('hid',$tid)
                    ->orwhere('aid',$tid);
            })
                ->orwhere(function ($j) use($match,$name){
                    $j->where('hname',$name)
                        ->orwhere('aname',$name);
                });
        })
            ->where('time','>',$match->time)
            ->where('basket_matches.id','!=',$match->id)
            ->where('status',0)
            ->orderby('time','asc')
            ->take(3)
            ->leftjoin('basket_leagues','lid', '=', 'basket_leagues.id')
            ->select('basket_matches.*', 'basket_leagues.name as league')
            ->get();
        foreach ($matches as $match){
            $dateCurrent = date_create();
            date_time_set($dateCurrent, 0, 0);
            $dateMatch = date_create($match->time);
            date_time_set($dateMatch, 0, 0);
            $match['day'] = $dateCurrent->diff($dateMatch)->days.'天';
        }
        return $matches;
    }

    /**
     * match比赛双方最近比赛数组
     * @param $match 当前比赛
     * @return array 分主客 各自分全部 同主客 同赛事 同主客赛事
     */
    private function matchBaseRecentlyBattle($match){
        $result['home'] = $this->matchBaseRecentlyBattleWithIsHost($match,true);
        $result['away'] = $this->matchBaseRecentlyBattleWithIsHost($match,false);
        return $result;
    }

    /**
     * 获取比赛单方最近比赛数组
     * @param $match 比赛
     * @param $isHost 是否主队
     * @return array
     */
    private function matchBaseRecentlyBattleWithIsHost($match, $isHost){
        if ($isHost)
            $tid = $match->hid;
        else
            $tid = $match->aid;
        $cid = BasketOdd::default_calculate_cid;
        $result = array();

        //表名
        $matchDBStringPre = 'basket_';

        //主队
        //最近10场
        $matches = BasketMatch::where(function ($q) use($match,$tid){
            $q->where('hid',$tid)
                ->orwhere('aid',$tid);
        })
            ->leftjoin($matchDBStringPre.'leagues','lid', '=', $matchDBStringPre.'leagues.id')
            ->leftjoin($matchDBStringPre.'odds as corner',function ($join) use($cid,$matchDBStringPre){
                $join->on($matchDBStringPre.'matches.id', '=', 'corner.mid');
                $join->where('corner.type','=',1);
                $join->where('corner.cid','=',$cid);
            })
            ->leftjoin($matchDBStringPre.'odds as goal',function ($join) use($cid,$matchDBStringPre){
                $join->on($matchDBStringPre.'matches.id', '=', 'goal.mid');
                $join->where('goal.type','=',2);
                $join->where('goal.cid','=',$cid);
            })
            ->where('time','<',$match->time)
            ->where($matchDBStringPre.'matches.status',-1)
            ->select($matchDBStringPre.'matches.*', $matchDBStringPre.'leagues.name as league',
                'goal.middle2 as goalMiddle2',
                'corner.up2 as up2','corner.middle2 as middle2','corner.down2 as down2')
            ->orderby('time','desc')
            ->take(10)
            ->get();
        $result['all'] = $matches;
        //同主客
        $matches = BasketMatch::where($isHost?'hid':'aid',$tid)
            ->leftjoin($matchDBStringPre.'leagues','lid', '=', $matchDBStringPre.'leagues.id')
            ->leftjoin($matchDBStringPre.'odds as corner',function ($join) use($cid,$matchDBStringPre){
                $join->on($matchDBStringPre.'matches.id', '=', 'corner.mid');
                $join->where('corner.type','=',1);
                $join->where('corner.cid','=',$cid);
            })
            ->leftjoin($matchDBStringPre.'odds as goal',function ($join) use($cid,$matchDBStringPre){
                $join->on($matchDBStringPre.'matches.id', '=', 'goal.mid');
                $join->where('goal.type','=',2);
                $join->where('goal.cid','=',$cid);
            })
            ->where('time','<',$match->time)
            ->select($matchDBStringPre.'matches.*', $matchDBStringPre.'leagues.name as league',
                'goal.middle2 as goalMiddle2',
                'corner.up2 as up2','corner.middle2 as middle2','corner.down2 as down2')
            ->orderby('time','desc')
            ->where($matchDBStringPre.'matches.status',-1)
            ->take(10)
            ->get();
        $result['team'] = $matches;
        //同赛事
        $matches = BasketMatch::where(function ($q) use($match,$tid){
            $q->where('hid',$tid)
                ->orwhere('aid',$tid);
        })
            ->where('lid',$match->lid)
            ->leftjoin($matchDBStringPre.'leagues','lid', '=', $matchDBStringPre.'leagues.id')
            ->leftjoin($matchDBStringPre.'odds as corner',function ($join) use($cid,$matchDBStringPre){
                $join->on($matchDBStringPre.'matches.id', '=', 'corner.mid');
                $join->where('corner.type','=',1);
                $join->where('corner.cid','=',$cid);
            })
            ->leftjoin($matchDBStringPre.'odds as goal',function ($join) use($cid,$matchDBStringPre){
                $join->on($matchDBStringPre.'matches.id', '=', 'goal.mid');
                $join->where('goal.type','=',2);
                $join->where('goal.cid','=',$cid);
            })
            ->where('time','<',$match->time)
            ->select($matchDBStringPre.'matches.*', $matchDBStringPre.'leagues.name as league',
                'goal.middle2 as goalMiddle2',
                'corner.up2 as up2','corner.middle2 as middle2','corner.down2 as down2')
            ->orderby('time','desc')
            ->where($matchDBStringPre.'matches.status',-1)
            ->take(10)
            ->get();
        $result['league'] = $matches;
        //同主客 同赛事
        $matches = BasketMatch::where($isHost?'hid':'aid',$tid)
            ->where('lid',$match->lid)
            ->leftjoin($matchDBStringPre.'leagues','lid', '=', $matchDBStringPre.'leagues.id')
            ->leftjoin($matchDBStringPre.'odds as corner',function ($join) use($cid,$matchDBStringPre){
                $join->on($matchDBStringPre.'matches.id', '=', 'corner.mid');
                $join->where('corner.type','=',1);
                $join->where('corner.cid','=',$cid);
            })
            ->leftjoin($matchDBStringPre.'odds as goal',function ($join) use($cid,$matchDBStringPre){
                $join->on($matchDBStringPre.'matches.id', '=', 'goal.mid');
                $join->where('goal.type','=',2);
                $join->where('goal.cid','=',$cid);
            })
            ->where('time','<',$match->time)
            ->select($matchDBStringPre.'matches.*', $matchDBStringPre.'leagues.name as league',
                'goal.middle2 as goalMiddle2',
                'corner.up2 as up2','corner.middle2 as middle2','corner.down2 as down2')
            ->where('lid',$match->lid)
            ->where($matchDBStringPre.'matches.status',-1)
            ->orderby('time','desc')
            ->take(10)
            ->get();
        $result['both'] = $matches;
        return $result;
    }

    /**
     * 根据matchBaseRecentlyBattle结果与tid计算最近比赛输赢情况
     * @param $allMatches
     * @param $tid
     * @return array
     */
    private function matchBaseRecentlyBattleResult($allMatches, $tid){
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

        foreach ($allMatches['all'] as $match){
            $temp = OddCalculateTool::getMatchResult($match->hscore, $match->ascore, $tid==$match->hid);
            switch ($temp) {
                case 3:
                    $win1++;
                    break;
                case 1:
                    $draw1++;
                    break;
                case 0:
                    $lose1++;
                    break;
            }

            if (isset($match['middle2'])){
                $temp = OddCalculateTool::getMatchAsiaOddResult($match['hscore'], $match['ascore'], $match['middle2'], $match['hid'] == $tid);
                switch ($temp) {
                    case 3:
                        $oddwin1++;
                        break;
                    case 1:
                        $odddraw1++;
                        break;
                    case 0:
                        $oddlose1++;
                        break;
                }
            }
        }

        foreach ($allMatches['team'] as $match){
            $temp = OddCalculateTool::getMatchResult($match->hscore, $match->ascore, $tid==$match->hid);
            switch ($temp) {
                case 3:
                    $win2++;
                    break;
                case 1:
                    $draw2++;
                    break;
                case 0:
                    $lose2++;
                    break;
            }

            if (isset($match['middle2'])){
                $temp = OddCalculateTool::getMatchAsiaOddResult($match['hscore'], $match['ascore'], $match['middle2'], $match['hid'] == $tid);
                switch ($temp) {
                    case 3:
                        $oddwin2++;
                        break;
                    case 1:
                        $odddraw2++;
                        break;
                    case 0:
                        $oddlose2++;
                        break;
                }
            }
        }

        foreach ($allMatches['league'] as $match){
            $temp = OddCalculateTool::getMatchResult($match->hscore, $match->ascore, $tid==$match->hid);
            switch ($temp) {
                case 3:
                    $win3++;
                    break;
                case 1:
                    $draw3++;
                    break;
                case 0:
                    $lose3++;
                    break;
            }

            if (isset($match['middle2'])){
                $temp = OddCalculateTool::getMatchAsiaOddResult($match['hscore'], $match['ascore'], $match['middle2'], $match['hid'] == $tid);
                switch ($temp) {
                    case 3:
                        $oddwin3++;
                        break;
                    case 1:
                        $odddraw3++;
                        break;
                    case 0:
                        $oddlose3++;
                        break;
                }
            }
        }

        foreach ($allMatches['both'] as $match){
            $temp = OddCalculateTool::getMatchResult($match->hscore, $match->ascore, $tid==$match->hid);
            switch ($temp) {
                case 3:
                    $win4++;
                    break;
                case 1:
                    $draw4++;
                    break;
                case 0:
                    $lose4++;
                    break;
            }

            if (isset($match['middle2'])){
                $temp = OddCalculateTool::getMatchAsiaOddResult($match['hscore'], $match['ascore'], $match['middle2'], $match['hid'] == $tid);
                switch ($temp) {
                    case 3:
                        $oddwin4++;
                        break;
                    case 1:
                        $odddraw4++;
                        break;
                    case 0:
                        $oddlose4++;
                        break;
                }
            }
        }

        $allPer = ($win1+$draw1+$lose1) == 0 ? '-' : number_format((100.0*OddCalculateTool::getOddWinPercent($win1, $draw1, $lose1,true,true)),1);
        $teamPer = ($win2+$draw2+$lose2) == 0 ? '-' : number_format((100.0*OddCalculateTool::getOddWinPercent($win2, $draw2, $lose2,true,true)),1);
        $leaguePer = ($win3+$draw3+$lose3) == 0 ? '-' : number_format((100.0*OddCalculateTool::getOddWinPercent($win3, $draw3, $lose3,true,true)),1);
        $bothPer = ($win4+$draw4+$lose4) == 0 ? '-' : number_format((100.0*OddCalculateTool::getOddWinPercent($win4, $draw4, $lose4,true,true)),1);
        $allPerOdd = ($oddwin1+$odddraw1+$oddlose1) == 0 ? '-' : number_format((100.0*OddCalculateTool::getOddWinPercent($oddwin1, $odddraw1, $oddlose1)),1);
        $teamPerOdd = ($oddwin2+$odddraw2+$oddlose2) == 0 ? '-' : number_format((100.0*OddCalculateTool::getOddWinPercent($oddwin2, $odddraw2, $oddlose2)),1);
        $leaguePerOdd = ($oddwin3+$odddraw3+$oddlose3) == 0 ? '-' : number_format((100.0*OddCalculateTool::getOddWinPercent($oddwin3, $odddraw3, $oddlose3)),1);
        $bothPerOdd = ($oddwin4+$odddraw4+$oddlose4) == 0 ? '-' : number_format((100.0*OddCalculateTool::getOddWinPercent($oddwin4, $odddraw4, $oddlose4)),1);
        $result = array(
            'all'=>array('win'=>$win1,'draw'=>$draw1,'lose'=>$lose1,'winPercent'=>$allPer,'oddPercent'=>$allPerOdd),
            'team'=>array('win'=>$win2,'draw'=>$draw2,'lose'=>$lose2,'winPercent'=>$teamPer,'oddPercent'=>$teamPerOdd),
            'league'=>array('win'=>$win3,'draw'=>$draw3,'lose'=>$lose3,'winPercent'=>$leaguePer,'oddPercent'=>$leaguePerOdd),
            'both'=>array('win'=>$win4,'draw'=>$draw4,'lose'=>$lose4,'winPercent'=>$bothPer,'oddPercent'=>$bothPerOdd)
        );
        return $result;
    }

    /**
     * 双方交往比赛对阵统计
     * @param $matches
     * @param $hid
     * @param $aid
     * @param $lid
     * @return array
     */
    private function matchBaseHistoryBattleResult($matches, $hid, $aid, $lid){
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
        foreach ($matches as $match){
            //胜负
            $temp = OddCalculateTool::getMatchResult($match->hscore, $match->ascore, $match->hid == $hid);
            switch ($temp){
                case 3:
                    //全部
                    $win1++;
                    //同主客
                    if ($match->hid == $hid && $match->aid == $aid){
                        $win2++;
                    }
                    //同赛事
                    if ($match->lid == $lid){
                        $win3++;
                    }
                    //同主客 同赛事
                    if (($match->hid == $hid && $match->aid == $aid) && $match->lid == $lid){
                        $win4++;
                    }
                    break;
                case 1:
                    //全部
                    $draw1++;
                    //同主客
                    if ($match->hid == $hid && $match->aid == $aid){
                        $draw2++;
                    }
                    //同赛事
                    if ($match->lid == $lid){
                        $draw3++;
                    }
                    //同主客 同赛事
                    if (($match->hid == $hid && $match->aid == $aid) && $match->lid == $lid){
                        $draw4++;
                    }
                    break;
                case 0:
                    //全部
                    $lose1++;
                    //同主客
                    if ($match->hid == $hid && $match->aid == $aid){
                        $lose2++;
                    }
                    //同赛事
                    if ($match->lid == $lid){
                        $lose3++;
                    }
                    //同主客 同赛事
                    if (($match->hid == $hid && $match->aid == $aid) && $match->lid == $lid){
                        $lose4++;
                    }
                    break;
            }

            //赢盘率
            if (isset($match['middle2'])){
                $temp = OddCalculateTool::getMatchAsiaOddResult($match['hscore'], $match['ascore'], $match['middle2'], $match->hid == $hid);
                switch ($temp) {
                    case 3:
                        //同主客
                        if ($match->hid == $hid && $match->aid == $aid){
                            $oddwin2++;
                        }
                        //同赛事
                        if ($match->lid == $lid){
                            $oddwin3++;
                        }
                        //同主客 同赛事
                        if (($match->hid == $hid && $match->aid == $aid) && $match->lid == $lid){
                            $oddwin4++;
                        }
                        $oddwin1++;
                        break;
                    case 1:
                        //同主客
                        if ($match->hid == $hid && $match->aid == $aid){
                            $odddraw2++;
                        }
                        //同赛事
                        if ($match->lid == $lid){
                            $odddraw3++;
                        }
                        //同主客 同赛事
                        if (($match->hid == $hid && $match->aid == $aid) && $match->lid == $lid){
                            $odddraw4++;
                        }
                        $odddraw1++;
                        break;
                    case 0:
                        //同主客
                        if ($match->hid == $hid && $match->aid == $aid){
                            $oddlose2++;
                        }
                        //同赛事
                        if ($match->lid == $lid){
                            $oddlose3++;
                        }
                        //同主客 同赛事
                        if (($match->hid == $hid && $match->aid == $aid) && $match->lid == $lid){
                            $oddlose4++;
                        }
                        $oddlose1++;
                        break;
                }
            }
        }
        $allPer = ($win1+$draw1+$lose1) == 0 ? '-' : number_format((100.0*OddCalculateTool::getOddWinPercent($win1, $draw1, $lose1, true, true)),1);
        $teamPer = ($win2+$draw2+$lose2) == 0 ? '-' : number_format((100.0*OddCalculateTool::getOddWinPercent($win2, $draw2, $lose2, true, true)),1);
        $leaguePer = ($win3+$draw3+$lose3) == 0 ? '-' : number_format((100.0*OddCalculateTool::getOddWinPercent($win3, $draw3, $lose3, true, true)),1);
        $bothPer = ($win4+$draw4+$lose4) == 0 ? '-' : number_format((100.0*OddCalculateTool::getOddWinPercent($win4, $draw4, $lose4, true, true)),1);
        $allPerOdd = ($oddwin1+$odddraw1+$oddlose1) == 0 ? '-' : number_format((100.0*OddCalculateTool::getOddWinPercent($oddwin1, $odddraw1, $oddlose1)),1);
        $teamPerOdd = ($oddwin2+$odddraw2+$oddlose2) == 0 ? '-' : number_format((100.0*OddCalculateTool::getOddWinPercent($oddwin2, $odddraw2, $oddlose2)),1);
        $leaguePerOdd = ($oddwin3+$odddraw3+$oddlose3) == 0 ? '-' : number_format((100.0*OddCalculateTool::getOddWinPercent($oddwin3, $odddraw3, $oddlose3)),1);
        $bothPerOdd = ($oddwin4+$odddraw4+$oddlose4) == 0 ? '-' : number_format((100.0*OddCalculateTool::getOddWinPercent($oddwin4, $odddraw4, $oddlose4)),1);
        $result = array(
            'all'=>array('win'=>$win1,'draw'=>$draw1,'lose'=>$lose1,'winPercent'=>$allPer,'oddPercent'=>$allPerOdd),
            'team'=>array('win'=>$win2,'draw'=>$draw2,'lose'=>$lose2,'winPercent'=>$teamPer,'oddPercent'=>$teamPerOdd),
            'league'=>array('win'=>$win3,'draw'=>$draw3,'lose'=>$lose3,'winPercent'=>$leaguePer,'oddPercent'=>$leaguePerOdd),
            'both'=>array('win'=>$win4,'draw'=>$draw4,'lose'=>$lose4,'winPercent'=>$bothPer,'oddPercent'=>$bothPerOdd)
        );
        return $result;
    }

    /**
     * 双方对阵历史比赛
     * @param $match
     * @return mixed
     */
    private function matchBaseHistoryBattle($match){
        $matchDBStringPre = 'basket_';

        $cid = BasketOdd::default_calculate_cid;
        $matches = BasketMatch::where(function ($q) use($match){
            $q->where('status',-1)
                ->where('hid',$match->hid)
                ->where('aid',$match->aid)
                ->where('time','<',$match->time);
        })
            ->orwhere(function ($q) use($match){
                $q->where('status',-1)
                    ->where('hid',$match->aid)
                    ->where('aid',$match->hid)
                    ->where('time','<',$match->time);
            })
            ->leftjoin($matchDBStringPre.'leagues','lid', '=', $matchDBStringPre.'leagues.id')
            ->leftjoin($matchDBStringPre.'odds as asia',function ($join) use($cid,$matchDBStringPre){
                $join->on($matchDBStringPre.'matches.id', '=', 'asia.mid');
                $join->where('asia.type','=',1);
                $join->where('asia.cid','=',$cid);
            })
            ->leftjoin($matchDBStringPre.'odds as goal',function ($join) use($cid,$matchDBStringPre){
                $join->on($matchDBStringPre.'matches.id', '=', 'goal.mid');
                $join->where('goal.type','=',2);
                $join->where('goal.cid','=',$cid);
            })
            ->leftjoin($matchDBStringPre.'odds as ou',function ($join) use($cid,$matchDBStringPre){
                $join->on($matchDBStringPre.'matches.id', '=', 'ou.mid');
                $join->where('ou.type','=',3);
                $join->where('ou.cid','=',$cid);
            })
            ->select($matchDBStringPre.'matches.*', $matchDBStringPre.'leagues.name as league',
                'goal.up2 as goalUp2','goal.middle2 as goalMiddle2','goal.down2 as goalDown2',
                'goal.up1 as goalUp1','goal.middle1 as goalMiddle1','goal.down1 as goalDown1',
                'asia.up2 as up2','asia.middle2 as middle2','asia.down2 as down2',
                'asia.up1 as up1','asia.middle1 as middle1','asia.down1 as down1',
                'ou.up2 as ouUp2','ou.middle2 as ouMiddle2','ou.down2 as ouDown2',
                'ou.up1 as ouUp1','ou.middle1 as ouMiddle1','ou.down1 as ouDown1')
            ->orderby('time','desc')
            ->get();
        return $matches;
    }

    /**
     * 比赛双方排名
     * @param $match
     * @return array
     */
    private function matchBaseRank($match){
        $resultRank = array();
        $resultRank['host'] = $this->matchBaseRankWithTid($match,$match->hid);
        $resultRank['away'] = $this->matchBaseRankWithTid($match,$match->aid);
        return $resultRank;
    }

    //联赛排名
    private function matchBaseRankWithTid($match,$tid){
        $resultRank = array();
        //获取球队所属联赛及排名
        $tmp = BasketTeam::getLeagueMatch($tid,$match->time);
        if (!is_null($tmp['match'])){
            $match = $tmp['match'];
        }

        $resultRank['league'] = $tmp['league'];

//        $h_rank = Score::where('lid','=',$match->lid)
//            ->where('season','=',$match->season)
//            ->where('tid','=',$tid)
//            ->where(function ($q) use ($match){
//                if (isset($match->lsid)){
//                    $q->where('lsid', '=', $match->lsid);
//                } else {
//                    $q->whereNull('lsid');
//                }
//            })
//            ->get();
//        foreach ($h_rank as $rank){
//            if (1 == $rank->kind){
//                $resultRank['home'] = $rank;
//            }
//            else if(2 == $rank->kind){
//                $resultRank['guest'] = $rank;
//            }
//            else if(is_null($rank->kind)){
//                $resultRank['all'] = $rank;
//            }
//        }
        //最近6场
        $sixMatches = Match::where('status',-1)
            ->where('season', '=', $match->season)
            ->where('lid', '=', $match->lid)
            ->where(function ($q) use($match,$tid){
                $q ->where('hid',$tid)
                    ->orwhere('aid',$tid);
            })
            ->orderby('time','desc')
            ->take(6)
            ->get();
        if (isset($sixMatches)){
            $goal = 0;
            $fumble = 0;
            $win = 0;
            $draw = 0;
            $lose = 0;
            foreach ($sixMatches as $sixMatch){
                if ($sixMatch->hid == $tid){
                    $goal += $sixMatch->hscore;
                    $fumble += $sixMatch->ascore;
                    if ($sixMatch->hscore > $sixMatch->ascore)
                        $win++;
                    elseif ($sixMatch->hscore < $sixMatch->ascore)
                        $lose++;
                    else
                        $draw++;
                }
                else{
                    $goal += $sixMatch->ascore;
                    $fumble += $sixMatch->hscore;
                    if ($sixMatch->hscore > $sixMatch->ascore)
                        $lose++;
                    elseif ($sixMatch->hscore < $sixMatch->ascore)
                        $win++;
                    else
                        $draw++;
                }
            }

            $resultRank['six'] = array('count'=>count($sixMatches),
                'fumble'=>$fumble,
                'goal'=>$goal,
                'win'=>$win,
                'draw'=>$draw,
                'lose'=>$lose,
                'score'=>$win*3+$draw);

            $sixMatches = null;
        }
        return $resultRank;
    }
}