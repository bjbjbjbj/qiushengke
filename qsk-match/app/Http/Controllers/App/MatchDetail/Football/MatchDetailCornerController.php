<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 17/2/5
 * Time: 下午4:45
 */
namespace App\Http\Controllers\App\MatchDetail\Football;

use App\Http\Controllers\App\MatchDetail\Football\Tool\RecentBattleTool;
use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\Odd;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redis;


class MatchDetailCornerController extends Controller{

    use RecentBattleTool;

    /**
     * 返回页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function matchDetailCorner(Request $request,$mid){
        //缓存
        $cacheSession = $mid.'_matchDetailCorner';
        $match = Match::where('id',$mid)->first();
        $rest = array();
        if (is_null($match)){
            return view('pc.match.matchDetail.components.corner',$rest);
        }
        else{
            if (is_null($match->hid) || is_null($match->aid))
                return view('pc.match.matchDetail.components.corner',$rest);
        }
        $rest = $this->cornerData($match);
        return view('pc.match.matchDetail.components.corner',$rest);
    }

    public function cornerData($match) {
        $mid = $match->id;
        //缓存
        $cacheSession = $mid.'_matchDetailCorner';

        //队名
        $rest['hname'] = $match->hname;
        $rest['aname'] = $match->aname;
        $rest['hid'] = $match->hid;
        $rest['aid'] = $match->aid;
        $rest['lid'] = $match->lid;
        //即时盘口
        $odd = $this->matchDetailCornerNow($match);

        if (Redis::exists($cacheSession)){
            $cache = Redis::get($cacheSession);
            $cache = json_decode($cache,true);
            $cache['hname'] = $match->hname;
            $cache['aname'] = $match->aname;
            $cache['hid'] = $match->hid;
            $cache['aid'] = $match->aid;
            $cache['odd'] = $odd;
            $cache['match'] = $match;
//            dump($cache);
            return $cache;
        }

        //统计
        $resultAnaylse = $this->matchCornerAnaylse($match);

        //交往
        //交往战绩
        $resultHistoryBattle = $this->matchCornerHistoryBattle($match);
        //新返回格式
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
        $resultHistoryBattleResult = $this->matchCornerHistoryBattleResult($resultHistoryBattle,$match->hid,$match->aid,$match->lid);

        //最近
        //最近战绩
        $resultRecentlyBattle = $this->recentCornerBattle($match);

        $rest['odd'] = $odd;
        $rest['anaylse'] = $resultAnaylse;
        $rest['historyBattle'] = $resultHistoryBattleNew;
        $rest['historyBattleResult'] = $resultHistoryBattleResult;
        $rest['recentBattle'] = $resultRecentlyBattle;
        Redis::set($cacheSession,json_encode($rest));
        //设置过期时间 12小时
        Redis::expire($cacheSession, 60*60*12);
        $rest['match'] = $match;
        return $rest;
    }

    /**
     * 根据球队统计近20 30数据
     * @param $match
     * @param $tid
     * @return mixed
     */
    private function matchCornerAnaylseWithTid($match, $tid){
        $cid = Odd::default_calculate_cid;
        //主队
        $matches = Match::where(function ($q) use($match,$tid){
            $q->where('hid',$tid)
                ->orwhere('aid',$tid);
        })
            ->where('time','<',$match->time)
            ->leftjoin('odds as corner',function ($join) use($cid){
                $join->on('matches.id', '=', 'corner.mid');
                $join->where('corner.type','=',4);
                $join->where('corner.cid','=',$cid);
            })
            ->leftjoin('leagues','lid', '=', 'leagues.id')
            ->leftjoin('match_datas', 'matches.id', '=', 'match_datas.id')
            ->select('matches.*', 'leagues.name as league',
                'match_datas.h_corner as h_corner','match_datas.a_corner as a_corner',
                'match_datas.h_half_corner as h_half_corner','match_datas.a_half_corner as a_half_corner',
                'corner.up1 as up2','corner.middle1 as middle2','corner.down1 as down2')
            ->orderby('time','desc')
            ->take(30)
            ->get();

        //10
        $total10 = 0;
        $get10 = 0;
        $lose10 = 0;
        $leave10 = 0;
        $big10 = 0;

        //20
        $total20 = 0;
        $get20 = 0;
        $lose20 = 0;
        $leave20 = 0;
        $big20 = 0;
        //30
        $total30 = 0;
        $get30 = 0;
        $lose30 = 0;
        $leave30 = 0;
        $big30 = 0;
        for ($i = 0 ; $i < count($matches) ; $i++){
            $tmp = $matches[$i];
            //有数据
            if (isset($tmp['h_corner']) && isset($tmp['a_corner']) && isset($tmp['middle2'])){
                if ($i < 10) {
                    $total10++;
                }
                if ($i < 20) {
                    $total20++;
                }
                $total30++;

                //主队
                if ($tmp->hid == $tid){
                    $isBig = false;
                    if ($tmp['h_corner'] + $tmp['a_corner'] > $tmp->middle2) {
                        $isBig = true;
                    };

                    if ($i < 20) {
                        $get20 += $tmp['h_corner'];
                        $lose20 += $tmp['a_corner'];
                        $leave20 += ($tmp['h_corner'] - $tmp['a_corner']);
                        if ($isBig)
                            $big20++;
                    }
                    if ($i < 10) {
                        $get10 += $tmp['h_corner'];
                        $lose10 += $tmp['a_corner'];
                        $leave10 += ($tmp['h_corner'] - $tmp['a_corner']);
                        if ($isBig)
                            $big10++;
                    }
                    $get30 += $tmp['h_corner'];
                    $lose30 += $tmp['a_corner'];
                    $leave30 += ($tmp['h_corner'] - $tmp['a_corner']);
                    if ($isBig)
                        $big30++;
                }
                else{
                    $isBig = false;
                    if ($tmp['h_corner'] + $tmp['a_corner'] < $tmp->middle2) {
                        $isBig = true;
                    };

                    if ($i < 20) {
                        $get20 += $tmp['a_corner'];
                        $lose20 += $tmp['h_corner'];
                        $leave20 += ($tmp['a_corner'] - $tmp['h_corner']);
                        if ($isBig)
                            $big20++;
                    }
                    if ($i < 10) {
                        $get10 += $tmp['a_corner'];
                        $lose10 += $tmp['h_corner'];
                        $leave10 += ($tmp['a_corner'] - $tmp['h_corner']);
                        if ($isBig)
                            $big10++;
                    }
                    $get30 += $tmp['a_corner'];
                    $lose30 += $tmp['h_corner'];
                    $leave30 += ($tmp['a_corner'] - $tmp['h_corner']);
                    if ($isBig)
                        $big30++;
                }
            }
        }

        $getPer = $total10 > 0 ? number_format($get10/$total10,2):"-";
        $losePer = $total10 > 0 ? number_format($lose10/$total10,2):"-";
        $leavePer = $total10 > 0 ? number_format($leave10/$total10,2):"-";
        $bigPer = $total10 > 0 ? number_format(100.0*$big10/$total10,2):"-";
        if ($total10 > 0) {
            $result['10'] = array('get' => $getPer, 'lose' => $losePer, 'leave' => $leavePer, 'big' => $bigPer);
        }
        $getPer = $total20 > 0 ? number_format($get20/$total20,2):"-";
        $losePer = $total20 > 0 ? number_format($lose20/$total20,2):"-";
        $leavePer = $total20 > 0 ? number_format($leave20/$total20,2):"-";
        $bigPer = $total20 > 0 ? number_format(100.0*$big20/$total20,2):"-";
        if ($total20 > 10) {
            $result['20'] = array('get' => $getPer, 'lose' => $losePer, 'leave' => $leavePer, 'big' => $bigPer);
        }
        $getPer = $total30 > 0 ? number_format($get30/$total30,2):"-";
        $losePer = $total30 > 0 ? number_format($lose30/$total30,2):"-";
        $leavePer = $total30 > 0 ? number_format($leave30/$total30,2):"-";
        $bigPer = $total30 > 0 ? number_format(100.0*$big30/$total30,2):"-";
        if ($total30 > 20) {
            $result['30'] = array('get' => $getPer, 'lose' => $losePer, 'leave' => $leavePer, 'big' => $bigPer);
        }
        if (isset($result)){
            return $result;
        }
        else
            return null;
        return $result;
    }

    /**
     * 统计近20 30 数据
     * @param $match
     * @return null
     */
    private function matchCornerAnaylse($match){
        $result['home'] = $this->matchCornerAnaylseWithTid($match,$match->hid);
        $result['away'] = $this->matchCornerAnaylseWithTid($match,$match->aid);
        if (isset($result) && ((isset($result['away']) && isset($result['away']['10'])) || (isset($result['home']) && $result['home']['10'])))
            return $result;
        else
            return null;
    }

    /**
     * 历史交锋统计
     * @param $matches
     * @param $hid
     * @param $aid
     * @param $lid
     * @return array
     */
    private function matchCornerHistoryBattleResult($matches, $hid, $aid, $lid){
        $result = array();
        //全部
        $win1 = 0;
        $draw1 = 0;
        $lose1 = 0;
        //同主客
        $win2 = 0;
        $draw2 = 0;
        $lose2 = 0;
        //同赛事
        $win3 = 0;
        $draw3 = 0;
        $lose3 = 0;
        //同主客赛事
        $win4 = 0;
        $draw4 = 0;
        $lose4 = 0;
        foreach ($matches as $match){
            if (isset($match['middle2'])) {
                if ($match->h_corner + $match->a_corner > $match['middle2']) {
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

                    //全部
                    $win1++;
                } elseif ($match->h_corner + $match->a_corner < $match['middle2']) {
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
                    //全部
                    $lose1++;
                } else {
                    //同主客
                    if ($match->hid == $hid && $match->aid == $aid) {
                        $draw2++;
                    }
                    //同赛事
                    if ($match->lid == $lid) {
                        $draw3++;
                    }
                    //同主客同赛事
                    if (($match->hid == $hid && $match->aid == $aid) && $match->lid == $lid) {
                        $draw4++;
                    }
                    //全部
                    $draw1++;
                }
            }
        }
        $allPer = ($win1+$draw1+$lose1) == 0 ? '-' : number_format((100.0*$win1/($win1 + $lose1 + $draw1)),2);
        $teamPer = ($win2+$draw2+$lose2) == 0 ? '-' : number_format((100.0*$win2/($win2 + $lose2 + $draw2)),2);
        $leaguePer = ($win3+$draw3+$lose3) == 0 ? '-' : number_format((100.0*$win3/($win3 + $lose3 + $draw3)),2);
        $bothPer = ($win4+$draw4+$lose4) == 0 ? '-' : number_format((100.0*$win4/($win4 + $lose4 + $draw4)),2);
        $result = array('all'=>array('win'=>$win1,'draw'=>$draw1,'lose'=>$lose1,'winPercent'=>$allPer),
            'team'=>array('win'=>$win2,'draw'=>$draw2,'lose'=>$lose2,'winPercent'=>$teamPer),
            'league'=>array('win'=>$win3,'draw'=>$draw3,'lose'=>$lose3,'winPercent'=>$leaguePer),
            'both'=>array('win'=>$win4,'draw'=>$draw4,'lose'=>$lose4,'winPercent'=>$bothPer)
        );
        return $result;
    }

    /**
     * 双方历史比赛列表
     * @param $match
     * @return mixed
     */
    private function matchCornerHistoryBattle($match){
        $cid = Odd::default_calculate_cid;
        $matches = Match::where(function ($q) use($match){
            $q->where('hid',$match->hid)
                ->where('aid',$match->aid)
                ->where('time','<',$match->time);
        })
            ->orwhere(function ($q) use($match){
                $q->where('hid',$match->aid)
                    ->where('aid',$match->hid)
                    ->where('time','<',$match->time);
            })
            ->leftjoin('odds as corner',function ($join) use($cid){
                $join->on('matches.id', '=', 'corner.mid');
                $join->where('corner.type','=',4);
                $join->where('corner.cid','=',$cid);
            })
            ->leftjoin('leagues','lid', '=', 'leagues.id')
            ->join('match_datas', function ($q){
                $q->on('matches.id', '=', 'match_datas.id');
                $q->whereNotNull('h_corner')
                    ->whereNotNull('a_corner');
            })
            ->select('matches.*', 'leagues.name as league',
                'match_datas.h_corner as h_corner','match_datas.a_corner as a_corner',
                'match_datas.h_half_corner as h_half_corner','match_datas.a_half_corner as a_half_corner',
                'corner.up1 as up2','corner.middle1 as middle2','corner.down1 as down2')
            ->orderby('time','desc')
            ->get();
        return $matches;
    }

    /**
     * 即时盘口
     * @param $match
     * @return mixed
     */
    private function matchDetailCornerNow($match){
        $odd = Odd::where('type',4)
            ->where('mid',$match->id)
            ->first();
        return $odd;
    }
}