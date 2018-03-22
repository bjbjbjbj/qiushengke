<?php
/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/2/27
 * Time: 19:04
 */

namespace App\Http\Controllers\Statistic\Terminal\Football;


use App\Http\Controllers\Statistic\OddCalculateTool;
use App\Http\Controllers\Statistic\Terminal\Tool\RecentBattleTool;
use App\Models\AnalyseModels\Match;
use App\Models\AnalyseModels\Odd;

trait FootballCornerTool
{
    private function matchCornerRecentBattle($match, $corner_recent_battle, $reset = false) {
        if (!$reset) {
            //如果有数据了则不再更新数据
            if (isset($corner_recent_battle)) {
                return $corner_recent_battle;
            }
        }

        //最近战绩
        $resultRecentlyBattle = RecentBattleTool::recentCornerBattle($match);

        return $resultRecentlyBattle;
    }

    /**
     * 根据球队统计近20 30数据
     * @param $match
     * @param $tid
     * @return mixed
     */
    private function matchCornerAnalyseWithTid($match, $tid){
        $matches = Match::query()->where(function ($q) use($match,$tid){
            $q->where('hid',$tid)
                ->orwhere('aid',$tid);
        })
            ->where('time','<',$match->time)
            ->leftjoin('odds as corner',function ($join){
                $join->on('matches.id', '=', 'corner.mid');
                $join->where('corner.type','=',Odd::k_odd_type_corner);
                $join->where('corner.cid','=',Odd::default_banker_id);
            })
            ->leftjoin('leagues','lid', '=', 'leagues.id')
            ->leftjoin('match_datas', 'matches.id', '=', 'match_datas.id')
            ->addSelect('matches.id','matches.time','matches.status','matches.hid','matches.aid','matches.lid',
                'matches.hscore','matches.ascore','matches.hscorehalf','matches.ascorehalf',
                'matches.hname','matches.aname', 'leagues.name as league')
            ->addSelect('match_datas.h_corner as h_corner','match_datas.a_corner as a_corner',
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

            $tmp->up2 = OddCalculateTool::formatOddItem($tmp, 'up2');
            $tmp->down2 = OddCalculateTool::formatOddItem($tmp, 'down2');

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

        return isset($result) ? $result : null;
    }

    /**
     * 统计近20 30 数据
     * @param $match
     * @return null
     */
    private function matchCornerAnalyse($match, $corner_analyse, $reset = false){
        if (!$reset) {
            //如果有数据了则不再更新数据
            if (isset($corner_analyse)) {
                return $corner_analyse;
            }
        }

        $result['home'] = $this->matchCornerAnalyseWithTid($match,$match->hid);
        $result['away'] = $this->matchCornerAnalyseWithTid($match,$match->aid);

        if (isset($result) && ((isset($result['away']) && isset($result['away']['10'])) || (isset($result['home']) && $result['home']['10']))){
            return $result;
        }

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
     */
    private function matchCornerHistoryBattle($match, $corner_history_battle, $reset = false){
        if (!$reset) {
            //如果有数据了则不再更新数据
            if (isset($corner_history_battle)) {
                return $corner_history_battle;
            }
        }

        //交往战绩
        $resultHistoryBattle = Match::query()->where(function ($q) use($match){
            $q->where('hid',$match->hid)
                ->where('aid',$match->aid)
                ->where('time','<',$match->time);
        })
            ->orwhere(function ($q) use($match){
                $q->where('hid',$match->aid)
                    ->where('aid',$match->hid)
                    ->where('time','<',$match->time);
            })
            ->leftjoin('odds as corner',function ($join){
                $join->on('matches.id', '=', 'corner.mid');
                $join->where('corner.type','=',Odd::k_odd_type_corner);
                $join->where('corner.cid','=',Odd::default_banker_id);
            })
            ->leftjoin('leagues','lid', '=', 'leagues.id')
            ->join('match_datas', function ($q){
                $q->on('matches.id', '=', 'match_datas.id');
                $q->whereNotNull('h_corner')
                    ->whereNotNull('a_corner');
            })
            ->addSelect('matches.id','matches.time','matches.status','matches.hid','matches.aid','matches.lid',
                'matches.hscore','matches.ascore','matches.hscorehalf','matches.ascorehalf',
                'matches.hname','matches.aname', 'leagues.name as league')
            ->addSelect('match_datas.h_corner as h_corner','match_datas.a_corner as a_corner',
                'match_datas.h_half_corner as h_half_corner','match_datas.a_half_corner as a_half_corner',
                'corner.up1 as up2','corner.middle1 as middle2','corner.down1 as down2')
            ->orderby('time','desc')
            ->get();
        //新返回格式
        //处理交往战绩
        $resultNHNL = array();
        $resultSHNL = array();
        $resultNHSL = array();
        $resultSHSL = array();
        foreach ($resultHistoryBattle as $tmp){
            $tmp->up2 = OddCalculateTool::formatOddItem($tmp, 'up2');
            $tmp->down2 = OddCalculateTool::formatOddItem($tmp, 'down2');

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

        $result['historyBattle'] = $resultHistoryBattleNew;
        $result['historyBattleResult'] = $resultHistoryBattleResult;

        return $result;
    }
}