<?php
/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/3/1
 * Time: 12:32
 */

namespace App\Http\Controllers\Statistic\Terminal\Tool;


use App\Http\Controllers\Statistic\OddCalculateTool;
use App\Models\LiaoGouModels\MatchLive;
use App\Models\LiaoGouModels\Odd;

class OddResultTool
{
    public static function matchOddResult($match, $sport = MatchLive::kSportFootball) {
        //盘路
        $resultOddResultH = self::matchBaseOdd($match,$match->hid, $sport);
        $resultOddResultA = self::matchBaseOdd($match,$match->aid, $sport);

        $rest = array();
        if (!isset($resultOddResultH) && !isset($resultOddResultA)) {
            $rest = NULL;
        } else {
            $rest['home'] = $resultOddResultH;
            $rest['away'] = $resultOddResultA;
        }

        return $rest;
    }

    /**
     * 获取当前比赛的,球队的盘路
     * @param $currentMatch 当前比赛,盘路需要这个比赛的lid与season搜索
     * @param $tid int 球队id
     * @param $sport int 比赛类型
     * @return array
     */
    private static function matchBaseOdd($currentMatch, $tid, $sport = MatchLive::kSportFootball){
        $result = array();
        $cid = Odd::default_calculate_cid;
        //总
        $query = MatchQueryTool::getMatchQueryBySport($sport);
        $query = MatchQueryTool::onMatchCommonSelect($query);
        $query = MatchQueryTool::onMatchOddLeftJoin($query, $cid, $sport, [1,2]);

        $matches = $query->where('lid',$currentMatch->lid)
            ->where('status',-1)
//            ->where('season',$currentMatch->season)
            ->where(function ($q) use($tid){
                $q->where('hid',$tid)
                    ->orwhere('aid',$tid);
            })->orderby('time','desc')
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
            if (isset($match->goalup2) && isset($match->goalmiddle2) && isset($match->goaldown2)){
                $temp = OddCalculateTool::getMatchSizeOddResult($match->hscore, $match->ascore, $match->goalmiddle2);
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
            if (isset($match->asiaup2) && isset($match->asiamiddle2) && isset($match->asiadown2)){
                $countAsia++;
                $sixAsia[] = OddCalculateTool::getMatchAsiaOddResult($match->hscore, $match->ascore, $match->asiamiddle2, $match->hid==$tid);
            }
            else{
                $countAsia++;
                $sixAsia[] = -1;
            }
            //大小球
            if (isset($match->goalup2) && isset($match->goalmiddle2) && isset($match->goaldown2)){
                $countEu++;
                $sixEu[] = OddCalculateTool::getMatchSizeOddResult($match->hscore, $match->ascore, $match->goalmiddle2);
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
            if (isset($match->goalup2) && isset($match->goalmiddle2) && isset($match->goaldown2)){
                $temp = OddCalculateTool::getMatchSizeOddResult($match->hscore, $match->ascore, $match->goalmiddle2);
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
            if (isset($match->goalup2) && isset($match->goalmiddle2) && isset($match->goaldown2)){
                $temp = OddCalculateTool::getMatchSizeOddResult($match->hscore, $match->ascore, $match->goalmiddle2);
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
}