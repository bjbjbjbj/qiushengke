<?php
namespace App\Http\Controllers\Statistic\Terminal\Basketball;

use App\Models\AnalyseModels\Match;
use App\Models\AnalyseModels\Score;
use App\Models\AnalyseModels\Team;


/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/2/27
 * Time: 17:39
 */
trait BasketRankTool
{
    private function rankStatistic($match, $rank, $reset = false) {
        if (!$reset) {
            //如果有数据了则不再更新数据
            if (isset($rank)) {
                return $rank;
            }
        }

        $win_id = $match->win_id;

        $url = "http://txt.win007.com/phone/basketball/lqanalysis/".substr($win_id, 0, 1)."/".substr($win_id, 1, 2)."/cn/$win_id.htm";
        $str = $this->spiderTextFromUrl($url);

        //排名
        $resultRank = array();

        if ($str) {
            $ss = explode("$$", $str);
            if (count($ss) >= 2) {
                $resultRank['host'] = $this->matchBaseRankWithTid($ss[0]);
                $resultRank['away'] = $this->matchBaseRankWithTid($ss[1]);
            }
        }

        return $resultRank;
    }

    //联赛排名
    private function matchBaseRankWithTid($rankStr){
        $resultRank = array();

        $rankItems = explode('!', $rankStr);
        foreach ($rankItems as $rankItem) {
            if (count(explode("^", $rankItem)) < 9) {
                continue;
            }
            list($type, $count, $win, $lose, $goal, $fumble, $diff, $rank, $win_p) = explode("^", $rankItem);
            $rankArray = ['count'=>$count, 'goal'=>$goal, 'fumble'=>$fumble, 'win'=>$win, 'lose'=>$lose, 'rank'=>$rank];

            if (str_contains($type, 'T')) {
                $resultRank['all'] = $rankArray;
            } else if (str_contains($type, 'A')) {
                $resultRank['home'] = $rankArray;
            } else if (str_contains($type, 'H')) {
                $resultRank['guest'] = $rankArray;
            } else if (str_contains($type, 'N')) {
                $resultRank['ten'] = $rankArray;
            }
        }

        return $resultRank;
    }
}