<?php
namespace App\Http\Controllers\Statistic\Terminal\Football;

use App\Models\AnalyseModels\Match;
use App\Models\AnalyseModels\Score;
use App\Models\AnalyseModels\Team;


/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/2/27
 * Time: 17:39
 */
trait FootballRankTool
{
    private function rankStatistic($match, $rank, $reset = false) {
        if (!$reset) {
            //如果有数据了则不再更新数据
            if (isset($rank)) {
                return $rank;
            }
        }

        //排名
        $resultRank = array();
        $resultRank['host'] = $this->matchBaseRankWithTid($match,$match->hid);
        $resultRank['away'] = $this->matchBaseRankWithTid($match,$match->aid);

        if (isset($resultRank['host']['all'])){
            $match['hLeagueRank'] = $resultRank['host']['all']['rank'];
        } else{
            $match['hLeagueRank'] = 0;
        }
        if (isset($resultRank['host']['league'])){
            $match['hLeagueName'] = $resultRank['host']['league']['name'];
        } else{
            $match['hLeagueName'] = null;
        }
        unset($resultRank['host']['league']);

        if (isset($resultRank['away']['all'])){
            $match['aLeagueRank'] = $resultRank['away']['all']['rank'];
        } else{
            $match['aLeagueRank'] = 0;
        }
        if (isset($resultRank['away']['league'])){
            $match['aLeagueName'] = $resultRank['away']['league']['name'];
        } else{
            $match['aLeagueName'] = null;
        }
        unset($resultRank['away']['league']);

        $rest['leagueRank']['aLeagueName'] = $match['aLeagueName'];
        $rest['leagueRank']['hLeagueName'] = $match['hLeagueName'];
        $rest['leagueRank']['aLeagueRank'] = $match['aLeagueRank'];
        $rest['leagueRank']['hLeagueRank'] = $match['hLeagueRank'];

        $rest['rank'] = $resultRank;

        return $rest;
    }

    //联赛排名
    private function matchBaseRankWithTid($match,$tid){
        $resultRank = array();
        //获取球队所属联赛及排名
        $tmp = Team::getLeagueMatch($tid,$match->time);
        if (!is_null($tmp['match'])){
            $match = $tmp['match'];
        }

        $resultRank['league'] = $tmp['league'];

        $h_rank = Score::query()
            ->select("kind", "count", 'score', 'goal', 'fumble', 'win', 'draw', 'lose', 'rank')
            ->where('lid','=',$match->lid)
            ->where('season','=',$match->season)
            ->where('tid','=',$tid)
            ->where(function ($q) use ($match){
                if (isset($match->lsid)){
                    $q->where('lsid', '=', $match->lsid);
                } else {
                    $q->whereNull('lsid');
                }
            })->get();
        foreach ($h_rank as $rank){
            $kind = $rank->kind;
            $rank->makeHidden('kind');

            if (1 == $kind){
                $resultRank['home'] = $rank;
            }
            else if(2 == $kind){
                $resultRank['guest'] = $rank;
            }
            else if(is_null($kind)){
                $resultRank['all'] = $rank;
            }
        }
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