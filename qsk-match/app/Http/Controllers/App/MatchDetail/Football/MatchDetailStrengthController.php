<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2017/10/31
 * Time: 下午3:46
 */

namespace App\Http\Controllers\App\MatchDetail\Football;

use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\MatchData;
use App\Models\LiaoGouModels\Moro\WsAlias;
use App\Models\LiaoGouModels\Moro\WsMatch;
use App\Models\LiaoGouModels\Referee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class MatchDetailStrengthController extends Controller{
    use WhoscoredAliases;
    /**
     * 强弱项
     * @param Request $request
     * @return 结果
     */
    public function matchDetailStrength(Request $request,$mid){
        $match = Match::where('id','=',$mid)->first();
        $ws = WsMatch::where('mid','=',$match->win_id)->first();
        if (isset($ws)) {
            $result = array();
            $charactersString = $ws->characters;
            $hcharacters = json_decode($charactersString,true);
            //翻译
            $array = $hcharacters['homeCharacter']['strengths'];
            for ($i = 0 ; $i < count($array) ; $i++){
                $tmp = $hcharacters['homeCharacter']['strengths'][$i];
                $name = array_key_exists($tmp['name'],$this->kCharacteristicsCN)?$this->kCharacteristicsCN[$tmp['name']]:$tmp['name'];
                $strength = array();
                $strength['name'] = isset($name)?$name:$tmp['name'];
                $strength['level'] = $tmp['level'];
                $strength['isOffensive'] = $tmp['isOffensive'];
                $result['home']['strengths'][] = $strength;
            }
            $array = $hcharacters['homeCharacter']['weaknesses'];
            for ($i = 0 ; $i < count($array) ; $i++){
                $tmp = $hcharacters['homeCharacter']['weaknesses'][$i];
                $name = array_key_exists($tmp['name'],$this->kCharacteristicsCN)?$this->kCharacteristicsCN[$tmp['name']]:$tmp['name'];
                $strength = array();
                $strength['name'] = $name;
                $strength['level'] = $tmp['level'];
                $strength['isOffensive'] = $tmp['isOffensive'];
                $result['home']['weaknesses'][] = $strength;
            }
            $array = $hcharacters['homeCharacter']['styles'];
            for ($i = 0 ; $i < count($array) ; $i++){
                $tmp = $hcharacters['homeCharacter']['styles'][$i];
                $name = array_key_exists($tmp['name'],$this->kStyleCN)?$this->kStyleCN[$tmp['name']]:$tmp['name'];
                $strength = array();
                $strength['name'] = isset($name)?$name:$tmp['name'];
                $strength['level'] = $tmp['level'];
                $strength['isOffensive'] = $tmp['isOffensive'];
                $result['home']['styles'][] = $strength;
            }
            //客队
            $array = $hcharacters['awayCharacter']['strengths'];
            for ($i = 0 ; $i < count($array) ; $i++){
                $tmp = $hcharacters['awayCharacter']['strengths'][$i];
                $name = array_key_exists($tmp['name'],$this->kCharacteristicsCN)?$this->kCharacteristicsCN[$tmp['name']]:$tmp['name'];
                $strength = array();
                $strength['name'] = isset($name)?$name:$tmp['name'];
                $strength['level'] = $tmp['level'];
                $strength['isOffensive'] = $tmp['isOffensive'];
                $result['away']['strengths'][] = $strength;
            }
            $array = $hcharacters['awayCharacter']['weaknesses'];
            for ($i = 0 ; $i < count($array) ; $i++){
                $tmp = $hcharacters['awayCharacter']['weaknesses'][$i];
                $name = array_key_exists($tmp['name'],$this->kCharacteristicsCN)?$this->kCharacteristicsCN[$tmp['name']]:$tmp['name'];
                $strength = array();
                $strength['name'] = $name;
                $strength['level'] = $tmp['level'];
                $strength['isOffensive'] = $tmp['isOffensive'];
                $result['away']['weaknesses'][] = $strength;
            }
            $array = $hcharacters['awayCharacter']['styles'];
            for ($i = 0 ; $i < count($array) ; $i++){
                $tmp = $hcharacters['awayCharacter']['styles'][$i];
                $name = array_key_exists($tmp['name'],$this->kStyleCN)?$this->kStyleCN[$tmp['name']]:$tmp['name'];
                $strength = array();
                $strength['name'] = isset($name)?$name:$tmp['name'];
                $strength['level'] = $tmp['level'];
                $strength['isOffensive'] = $tmp['isOffensive'];
                $result['away']['styles'][] = $strength;
            }

            //预测
            $array = $hcharacters['matchForeCastItems'];
            for ($i = 0 ; $i < count($array) ; $i++){
                $tmp = $hcharacters['matchForeCastItems'][$i];
                $name = str_replace('  ', ' ',$tmp['sentence']);
                foreach ($this->kForecastCN as $key=>$string){
                    if (strstr($name, $key)) {
                        $teamName = str_replace($key, '', $name);
                        $teamName = substr($teamName,0,strlen($teamName) - 1);
                        if (strlen($teamName) > 0) {
                            $teamName = WsAlias::getAlias($teamName);
                        }
                        $name = $teamName.$string;
                    }
                }
                $strength = array();
                $strength['sentence'] = $name;
                $strength['score'] = $tmp['score'];
                $result['case'][] = $strength;
            }
            $rest['ws'] = $result;
        }

        //裁判
        $rest['referee'] = $this->matchDetailReferee($mid);

        $od_type = 1;
        $rest['sameOdd'] = null;
        $rest['sameOdd2'] = null;
        $rest['sameOdd3'] = null;

        //同赔
        $sameOdd = MatchDetailOddController::getSameOdd(1, $mid);
        if (isset($sameOdd['sameOdd']) && count($sameOdd['sameOdd']) > 0) {
            $od_type = 1;
            $rest['sameOdd'] = $sameOdd['sameOdd'];
        } else {
            $sameOdd2 = MatchDetailOddController::getSameOdd(2, $mid);
            if (isset($sameOdd2['sameOdd']) && count($sameOdd2['sameOdd']) > 0) {
                $od_type = 2;
                $rest['sameOdd2'] = $sameOdd2['sameOdd'];
            } else {
                $sameOdd3 = MatchDetailOddController::getSameOdd(3, $mid);
                if (isset($sameOdd2['sameOdd']) && count($sameOdd2['sameOdd']) > 0) {
                    $od_type = 3;
                    $rest['sameOdd3'] = $sameOdd3['sameOdd'];
                }
            }
        }

        $rest['od_type'] = $od_type;
        $rest['match'] = $match;
        return view('pc.match.matchDetail.components.characteristic',$rest);
    }

    public function teamStyleData($mid) {
        $rest = [];
        $match = Match::where('id','=',$mid)->first();
        $ws = WsMatch::where('mid','=',$match->win_id)->first();
        if (isset($ws)) {
            $rest['match'] = $match;
            $result = array();
            $charactersString = $ws->characters;
            $hcharacters = json_decode($charactersString,true);
            //翻译
            $array = $hcharacters['homeCharacter']['strengths'];
            for ($i = 0 ; $i < count($array) ; $i++){
                $tmp = $hcharacters['homeCharacter']['strengths'][$i];
                $name = array_key_exists($tmp['name'],$this->kCharacteristicsCN)?$this->kCharacteristicsCN[$tmp['name']]:$tmp['name'];
                $strength = array();
                $strength['name'] = isset($name)?$name:$tmp['name'];
                $strength['level'] = $tmp['level'];
                $strength['isOffensive'] = $tmp['isOffensive'];
                $result['home']['strengths'][] = $strength;
            }
            $array = $hcharacters['homeCharacter']['weaknesses'];
            for ($i = 0 ; $i < count($array) ; $i++){
                $tmp = $hcharacters['homeCharacter']['weaknesses'][$i];
                $name = array_key_exists($tmp['name'],$this->kCharacteristicsCN)?$this->kCharacteristicsCN[$tmp['name']]:$tmp['name'];
                $strength = array();
                $strength['name'] = $name;
                $strength['level'] = $tmp['level'];
                $strength['isOffensive'] = $tmp['isOffensive'];
                $result['home']['weaknesses'][] = $strength;
            }
            $array = $hcharacters['homeCharacter']['styles'];
            for ($i = 0 ; $i < count($array) ; $i++){
                $tmp = $hcharacters['homeCharacter']['styles'][$i];
                $name = array_key_exists($tmp['name'],$this->kStyleCN)?$this->kStyleCN[$tmp['name']]:$tmp['name'];
                $strength = array();
                $strength['name'] = isset($name)?$name:$tmp['name'];
                $strength['level'] = $tmp['level'];
                $strength['isOffensive'] = $tmp['isOffensive'];
                $result['home']['styles'][] = $strength;
            }
            //客队
            $array = $hcharacters['awayCharacter']['strengths'];
            for ($i = 0 ; $i < count($array) ; $i++){
                $tmp = $hcharacters['awayCharacter']['strengths'][$i];
                $name = array_key_exists($tmp['name'],$this->kCharacteristicsCN)?$this->kCharacteristicsCN[$tmp['name']]:$tmp['name'];
                $strength = array();
                $strength['name'] = isset($name)?$name:$tmp['name'];
                $strength['level'] = $tmp['level'];
                $strength['isOffensive'] = $tmp['isOffensive'];
                $result['away']['strengths'][] = $strength;
            }
            $array = $hcharacters['awayCharacter']['weaknesses'];
            for ($i = 0 ; $i < count($array) ; $i++){
                $tmp = $hcharacters['awayCharacter']['weaknesses'][$i];
                $name = array_key_exists($tmp['name'],$this->kCharacteristicsCN)?$this->kCharacteristicsCN[$tmp['name']]:$tmp['name'];
                $strength = array();
                $strength['name'] = $name;
                $strength['level'] = $tmp['level'];
                $strength['isOffensive'] = $tmp['isOffensive'];
                $result['away']['weaknesses'][] = $strength;
            }
            $array = $hcharacters['awayCharacter']['styles'];
            for ($i = 0 ; $i < count($array) ; $i++){
                $tmp = $hcharacters['awayCharacter']['styles'][$i];
                $name = array_key_exists($tmp['name'],$this->kStyleCN)?$this->kStyleCN[$tmp['name']]:$tmp['name'];
                $strength = array();
                $strength['name'] = isset($name)?$name:$tmp['name'];
                $strength['level'] = $tmp['level'];
                $strength['isOffensive'] = $tmp['isOffensive'];
                $result['away']['styles'][] = $strength;
            }

            //预测
            $array = $hcharacters['matchForeCastItems'];
            for ($i = 0 ; $i < count($array) ; $i++){
                $tmp = $hcharacters['matchForeCastItems'][$i];
                $name = str_replace('  ', ' ',$tmp['sentence']);
                foreach ($this->kForecastCN as $key=>$string){
                    if (strstr($name, $key)) {
                        $teamName = str_replace($key, '', $name);
                        $teamName = substr($teamName,0,strlen($teamName) - 1);
                        if (strlen($teamName) > 0) {
                            $teamName = WsAlias::getAlias($teamName);
                        }
                        $name = $teamName.$string;
                    }
                }
                $strength = array();
                $strength['sentence'] = $name;
                $strength['score'] = $tmp['score'];
                $result['case'][] = $strength;
            }
            $rest['ws'] = $result;
        }
        return $rest;
    }

    /**
     * 裁判
     * @param $mid
     * @return null
     */
    public function matchDetailReferee($mid)
    {
        $match = Match::query()->find($mid);
        $matchData = MatchData::query()->find($mid);
        if (isset($matchData)) {
            $refereeId = $matchData->referee_id;
            if (isset($refereeId) && $refereeId > 0) {
                $referee = Referee::query()->find($refereeId);
            }
        }
        if (!isset($referee)) {
            return null;
        }
        $rest['name'] = $referee->name;
        $rest['hname'] = $match->hname;
        $rest['aname'] = $match->aname;
        $h_wdl = Referee::getRefereeTeamWinPercent($match->time, $match->hid, $referee->id);
        $rest['h_wdl'] = $h_wdl['win'].'胜'.$h_wdl['draw'].'平'.$h_wdl['lose'].'负';
        $a_wdl = Referee::getRefereeTeamWinPercent($match->time, $match->aid, $referee->id);
        $rest['a_wdl'] = $a_wdl['win'].'胜'.$a_wdl['draw'].'平'.$a_wdl['lose'].'负';
        $rest['win_percent'] = sprintf("%.1f", $referee->lately_win*100/$referee->lately_count);
        $rest['yellow_avg'] = $referee->lately_h_yellow_avg + $referee->lately_a_yellow_avg;
        return $rest;
    }

    /**
     * 裁判统计用
     * @param $count
     * @return int
     */
    private function getCount($count) {
        if (isset($count)) {
            return $count;
        }
        return 0;
    }

    /**
     * 球队能力
     * @param $tid
     * @param $time
     * @param $isHost
     * @param $lid
     * @param $cache
     * @return null
     */
    static public function teamAttributeWithTid($tid,$time,$isHost,$lid,$cache){
        if ($tid <= 0){
            return null;
        }
        //全部
        $matches = Match::where(function ($q) use($tid,$time){
            $q->where('hid',$tid)
                ->orwhere('aid',$tid);
        })
            ->where('status',-1)
            ->where('time','<',$time)
            ->orderby('time','desc')
            ->take(10)
            ->get();
        $rest['all'] = MatchDetailStrengthController::resultOfTeamAttribute($matches,$tid);

        //同主客
        $matches = Match::
        where(($isHost?'hid':'aid'),$tid)
            ->where('status',-1)
            ->where('time','<',$time)
            ->orderby('time','desc')
            ->take(10)
            ->get();
        $rest['host'] = MatchDetailStrengthController::resultOfTeamAttribute($matches,$tid);

        //同赛事
        $matches = Match::where(function ($q) use($tid,$time){
            $q->where('hid',$tid)
                ->orwhere('aid',$tid);
        })
            ->where('lid',$lid)
            ->where('status',-1)
            ->where('time','<',$time)
            ->orderby('time','desc')
            ->take(10)
            ->get();
        $rest['league'] = MatchDetailStrengthController::resultOfTeamAttribute($matches,$tid);

        //同主客同赛事
        $matches = Match::
        where(($isHost?'hid':'aid'),$tid)
            ->where('lid',$lid)
            ->where('status',-1)
            ->where('time','<',$time)
            ->orderby('time','desc')
            ->take(10)
            ->get();
        $rest['both'] = MatchDetailStrengthController::resultOfTeamAttribute($matches,$tid);

        Redis::set($cache,json_encode($rest));
        //设置过期时间 12小时
        Redis::expire($cache, 60*60*12);
        return $rest;
    }

    static private function resultOfTeamAttribute($matches,$tid){
        $goalCount = 0;
        $missCount = 0;
        $goalMatchCount = 0;
        $missMatchCount = 0;
        $winCount = 0;
        $drawCount = 0;
        $loseCount = 0;
        foreach ($matches as $match){
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