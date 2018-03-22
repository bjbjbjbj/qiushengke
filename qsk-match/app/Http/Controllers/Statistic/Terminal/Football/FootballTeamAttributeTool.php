<?php
/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/2/27
 * Time: 18:41
 */

namespace App\Http\Controllers\Statistic\Terminal\Football;


use App\Models\AnalyseModels\Match;

trait FootballTeamAttributeTool
{
    private function matchTeamAttribute($match, $teamAttribute, $reset = false) {
        if (!$reset) {
            //如果有数据了则不再更新数据
            if (isset($teamAttribute) && (count($teamAttribute['home']) > 0 || count($teamAttribute['away']) > 0)) {
                return $teamAttribute;
            }
        }

        $rest['home'] = $this->teamAttributeWithTid($match->hid,$match->time,true,$match->lid);
        $rest['away'] = $this->teamAttributeWithTid($match->aid,$match->time,true,$match->lid);

        return $rest;
    }

    /**
     * 球队能力
     * @param $tid
     * @param $time
     * @param $isHost
     * @param $lid
     * @return mixed
     */
    private function teamAttributeWithTid($tid,$time,$isHost,$lid){
        if ($tid <= 0){
            return null;
        }
        //全部
        $matches = Match::query()->select('id as mid', 'hid','aid','hscore','ascore')
            ->where(function ($q) use($tid,$time){
            $q->where('hid',$tid)
                ->orwhere('aid',$tid);
        })
            ->where('status',-1)
            ->where('time','<',$time)
            ->orderby('time','desc')
            ->take(30)
            ->get();
        $rest['all'] = $this->resultOfTeamAttribute($matches,$tid);
        $rest['all']['matches'] = $matches;

        //同主客
        $matches = Match::query()->select('id as mid', 'hid','aid','hscore','ascore')
            ->where(($isHost?'hid':'aid'),$tid)
            ->where('status',-1)
            ->where('time','<',$time)
            ->orderby('time','desc')
            ->take(30)
            ->get();
        $rest['host'] = $this->resultOfTeamAttribute($matches,$tid);
        $rest['host']['matches'] = $matches;

        //同赛事
        $matches = Match::query()->select('id as mid', 'hid','aid','hscore','ascore')
            ->where(function ($q) use($tid,$time){
            $q->where('hid',$tid)
                ->orwhere('aid',$tid);
        })
            ->where('lid',$lid)
            ->where('status',-1)
            ->where('time','<',$time)
            ->orderby('time','desc')
            ->take(30)
            ->get();
        $rest['league'] = $this->resultOfTeamAttribute($matches,$tid);
        $rest['league']['matches'] = $matches;

        //同主客同赛事
        $matches = Match::query()->select('id as mid', 'hid','aid','hscore','ascore')
            ->where(($isHost?'hid':'aid'),$tid)
            ->where('lid',$lid)
            ->where('status',-1)
            ->where('time','<',$time)
            ->orderby('time','desc')
            ->take(30)
            ->get();
        $rest['both'] = $this->resultOfTeamAttribute($matches,$tid);
        $rest['both']['matches'] = $matches;

        return $rest;
    }

    private function resultOfTeamAttribute($matches,$tid,$count = 10){
        $goalCount = 0;
        $missCount = 0;
        $goalMatchCount = 0;
        $missMatchCount = 0;
        $winCount = 0;
        $drawCount = 0;
        $loseCount = 0;
        $index = 0;
        foreach ($matches as $match){
            if ($index >= $count) break;

//            dump($match->hname.':'.$match->hscore.' '.$match->aname.':'.$match->ascore);
            if ($match->hid == $tid){
                if ($match->hscore > 0){
                    $goalMatchCount++;
                    $goalCount += $match->hscore;
                }
                if ($match->ascore > 0){
                    $missMatchCount++;
                    $missCount += $match->ascore;
                }
                if ($match->hscore > $match->ascore){
                    $winCount++;
                }
                elseif($match->hscore < $match->ascore){
                    $loseCount++;
                }
                else{
                    $drawCount++;
                }
            }
            else{
                if ($match->ascore > 0){
                    $goalMatchCount++;
                    $goalCount += $match->ascore;
                }
                if ($match->hscore > 0){
                    $missMatchCount++;
                    $missCount += $match->hscore;
                }
                if ($match->hscore < $match->ascore){
                    $winCount++;
                }
                elseif($match->hscore > $match->ascore){
                    $loseCount++;
                }
                else{
                    $drawCount++;
                }
            }
            $index++;
        }
//        dump(number_format($goalCount/count($matches),1). ' ' .number_format($missCount/count($matches),1) .' '. $goalMatchCount .' '. $missMatchCount);
        return array(
            'avgGoal'=>count($matches)>0?number_format($goalCount/count($matches),1):0,
            'avgMiss'=>count($matches)>0?number_format($missCount/count($matches),1):0,
            'avgGoalMatch'=>$goalMatchCount,
            'avgMissMatch'=>$missMatchCount,
            'win'=>$winCount,
            'draw'=>$drawCount,
            'lose'=>$loseCount,
        );
    }
}