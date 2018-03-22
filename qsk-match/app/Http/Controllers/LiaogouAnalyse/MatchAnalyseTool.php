<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 16/11/28
 * Time: 上午10:29
 */

namespace App\Http\Controllers\LiaogouAnalyse;

use App\Http\Controllers\WinSpider\SpiderMatchTeam;
use App\Http\Controllers\WinSpider\SpiderTools;
use App\Models\LiaoGouModels\League;
use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\MatchLineup;
use App\Models\LiaoGouModels\MatchPlayer;
use App\Models\LiaoGouModels\Season;

/*
 * 阵容首发率,不属于球探数据,独立
 */
trait MatchAnalyseTool{
    use SpiderMatchTeam,SpiderTools;
    /**
     * 这个赛事里面这支球队除某matchid以外所有赛事
     * @param $leagueId 赛事id
     * @param $season 赛季
     * @param $teamId 球队id
     * @param $matchId 比赛id
     * @param $teamName 球队名字
     * @return mixed 比赛列表
     */
    public static function getMatchesByTeamId($leagueId, $season, $teamId, $matchId,$teamName){
//        echo 'lid '. $leagueId . ' season'.$season.' teamid'.$teamId.' team'.$teamName;
        //因为season字段错误,这里只能先去season拿开赛时间,如果之后数据都OK了可以无视
        $tmpSeason = Season::where('lid', '=', $leagueId)->first();

        $query = Match::where('lid','=',$leagueId)
            ->where('season','=',$season)
            ->where(function ($q) use($teamId,$teamName){
                if ($teamId > 0){
                    $q
                        ->where(function ($qb) use($teamId){
                            $qb->where('hid', '=', $teamId)
                                ->orwhere('aid', '=', $teamId);
                        })
                        ->orwhere(function ($qb)use($teamName){
                            $qb->where('hname', '=', $teamName)
                                ->orwhere('aname', '=', $teamName);
                        });
                }
                else{
                    $q->where(function ($qb)use($teamName){
                        $qb->where('hname', '=', $teamName)
                            ->orwhere('aname', '=', $teamName);
                    });
                }
            })
            ->where('id','!=',$matchId)
            ->where('status','=',-1)
            ->orderby('id','desc');

        if (isset($tmpSeason) && isset($tmpSeason->start)){
            $query->where('time','>=',$tmpSeason->start);
        }

        $matches = $query->get();
        return $matches;
    }

    /**
     * 返回阵容球员首发率
     * @param $currentLineup 当前阵容,数组
     * @param $otherMatches 其他比赛
     * @param $teamId 球队id
     * @param $teamName 球队名字
     * @param $currentMatch 当前比赛
     * @param $showPlayer 是否需要显示球员
     * @return array 这支球队阵容的首发频率 例如1号球员15场比赛内首发了13次array('lineup'=>array([1]=>13),'total'=>15);
     */
    private static function getLineupPercentPrivate($currentLineup, $otherMatches, $teamId, $teamName,$currentMatch,$showPlayer){
        $result = array();
        $total = count($otherMatches);
        if ($showPlayer){
            foreach ($currentLineup as $current){
                $player = MatchPlayer::where('lid',$currentMatch->lid)
                    ->where('tid',$teamId)
                    ->where('num',$current)
                    ->first();
                if (isset($player)){
                    $playerName = $player->name;
                }
                $result[] = array('num'=>$current,'name'=>isset($playerName)?$playerName:"",'lineup'=>0,'lineup_bak'=>0,'lineup_replace'=>0,'win'=>0,'lose'=>0,'draw'=>0,'goal'=>0,'against'=>0,'total'=>$total,'is_first'=>0);
            }
        }
        else{
            foreach ($currentLineup as $current){
                $result[] = array('num'=>$current,'name'=>"",'lineup'=>0,'lineup_bak'=>0,'lineup_replace'=>0,'win'=>0,'lose'=>0,'draw'=>0,'goal'=>0,'against'=>0,'total'=>$total,'is_first'=>0);
            }
        }
        //这两个暂时没用,算法被改了
        $totalWin = 0;
        $totalCurrentLineup = 0;
        $totalLineup = 0;
        //进球数
        $totalGoal = 0;
        //失球数
        $totalGoalAgainst = 0;
        $lineup = MatchLineup::find($currentMatch->id);
        foreach ($otherMatches as $match) {
            if ($match->hid == $teamId) {
                $totalGoal += $match->hscore;
                $totalGoalAgainst += $match->ascore;
            } else {
                $totalGoal += $match->ascore;
                $totalGoalAgainst += $match->hscore;
            }
            $matchLineup = MatchLineup::where('id', '=', $match->id)
                ->first();
            if (isset($matchLineup)) {
                //首发
                $lineups = $matchLineup->h_id == $teamId ? $matchLineup->h_lineup : $matchLineup->a_lineup;
                //替补
                $lineups_bak = $matchLineup->h_id == $teamId ? $matchLineup->h_lineup_bak : $matchLineup->a_lineup_bak;
                //替补上场
                $lineups_replace = $matchLineup->h_id == $teamId ? $matchLineup->h_lineup_replace : $matchLineup->a_lineup_replace;
            } else {
                $lineups = null;
            }
            if (isset($lineups)) {
                $lineups = explode(',', $lineups);
                $lineups_bak = explode(',', $lineups_bak);
                $lineups_replace = explode(',', $lineups_replace);
                $totalLineup += count($lineups);
                for ($i = 0; $i < count($currentLineup); $i++) {
                    $current = $currentLineup[$i];
                    if (in_array($current, $lineups)) {
                        $totalCurrentLineup++;
                        $result[$i]['lineup'] =  $result[$i]['lineup'] + 1;
                        if ($match->hname == $teamName && $match->hscore > $match->ascore){
                            $result[$i]['win'] =  $result[$i]['win'] + 1;
                        }
                        else if ($match->aname == $teamName && $match->ascore > $match->hscore){
                            $result[$i]['win'] =  $result[$i]['win'] + 1;
                        }
                        else if($match->ascore == $match->hscore){
                            $result[$i]['draw'] =  $result[$i]['draw'] + 1;
                        }
                        else{
                            $result[$i]['lose'] =  $result[$i]['lose'] + 1;
                        }

                        //进球失球数
                        if ($match->hname == $teamName){
                            $result[$i]['goal'] = $result[$i]['goal'] + $match->hscore;
                            $result[$i]['against'] = $result[$i]['against'] + $match->ascore;
                        }
                        else{
                            $result[$i]['goal'] = $result[$i]['goal'] + $match->ascore;
                            $result[$i]['against'] = $result[$i]['against'] + $match->hscore;
                        }
                    }

                    //替补
                    if (in_array($current, $lineups_bak)) {
                        $result[$i]['lineup_bak'] =  $result[$i]['lineup_bak'] + 1;
                    }

                    //替补上场
                    if (in_array($current, $lineups_replace)) {
                        $result[$i]['lineup_replace'] =  $result[$i]['lineup_replace'] + 1;
                    }
                }
                if (($match->hname == $teamName && $match->hscore > $match->ascore) ||
                    ($match->aname == $teamName && $match->ascore > $match->hscore)
                ) {
                    $totalWin++;
                }

            } else {
                //fixme test
//                echo 'here? '.$match->id.'</br>';
//                return array('msg'=>'mid '. $match->id .' 数据不全');
            }
        }

        //计算进球失球数
        if ($lineup->h_id == $teamId){
            $goal = 0;
            $against = 0;
            $totalLineupCount = 0;
            $firsts = array();
            foreach ($result as $eachPlayer){
                if ($eachPlayer['lineup'] > 0) {
                    $goal += $eachPlayer['goal'] / $eachPlayer['lineup'];
                    $against += $eachPlayer['against'] / $eachPlayer['lineup'];
                }
                if ($eachPlayer['lineup_bak'] > 0 && $eachPlayer['lineup'] > 0 && $eachPlayer['lineup']/($eachPlayer['lineup_replace']+$eachPlayer['lineup']) > 0.7){
                    $firsts[] = $eachPlayer['num'];
                    $totalLineupCount++;
                    $eachPlayer['is_first'] = 1;
                }
                else{
                    if ($eachPlayer['lineup_bak'] == 0){
                        if ($eachPlayer['lineup'] > 0) {
                            $firsts[] = $eachPlayer['num'];
                            $totalLineupCount++;
                        }
                        $eachPlayer['is_first'] = $eachPlayer['lineup'] > 0 ? 1 : 0;
                    }
                    else {
                        $eachPlayer['is_first'] = 0;
                    }
                }

//                echo 'player num ' . $eachPlayer['num'] . ' lineup ' . $eachPlayer['lineup'] . ' replace ' . $eachPlayer['lineup_replace'] . '</br>';
            }

            if (count($result) > 0) {
                $lineup->h_goal = $goal / count($result);
                $lineup->h_against = $against / count($result);
            }

            if (count($currentLineup) > 0){
                $lineup->h_first = implode(',',$firsts);
                $lineup->h_lineup_percent = 100*$totalLineupCount/11;
            }
        }
        else{
            $goal = 0;
            $against = 0;
            $totalLineupCount = 0;
            $firsts = array();
            foreach ($result as $eachPlayer){
                if ($eachPlayer['lineup'] > 0) {
                    $goal += $eachPlayer['goal'] / $eachPlayer['lineup'];
                    $against += $eachPlayer['against'] / $eachPlayer['lineup'];
                }
                if ($eachPlayer['lineup'] > 0 && $eachPlayer['lineup_bak'] > 0 && $eachPlayer['lineup']/($eachPlayer['lineup_replace']+$eachPlayer['lineup']) > 0.7){
                    $firsts[] = $eachPlayer['num'];
                    $totalLineupCount++;
                    $eachPlayer['is_first'] = 1;
                }else{
                    if ($eachPlayer['lineup_bak'] == 0){
                        if ($eachPlayer['lineup'] > 0) {
                            $firsts[] = $eachPlayer['num'];
                            $totalLineupCount++;
                        }
                        $eachPlayer['is_first'] = $eachPlayer['lineup'] > 0 ? 1 : 0;
                    }
                    else {
                        $eachPlayer['is_first'] = 0;
                    }
                }
            }
            if (count($result) > 0) {
                $lineup->a_goal = $goal / count($result);
                $lineup->a_against = $against / count($result);
            }

            if (count($currentLineup) > 0){
                $lineup->a_first = implode(',',$firsts);
                $lineup->a_lineup_percent = 100*$totalLineupCount/11;
            }
        }

        $lineup->save();
        \App\Models\LiaoGouModels\MatchLineup::saveDataWithWinData($lineup);
        return array('lineup'=>$result,'total'=>$total,'totalWin'=>$totalWin,'lineupPercent'=>(100*$totalCurrentLineup/11));
    }

    /**
     * 返回阵容球员首发率
     * @param $currentLineup 当前阵容,数组
     * @param $otherMatches 其他比赛
     * @param $teamId 球队id
     * @param $teamName 球队名字
     * @param $currentMatch 当前比赛
     * @return array 这支球队阵容的首发频率 例如1号球员15场比赛内首发了13次array('lineup'=>array([1]=>13),'total'=>15);
     */
    public function getLineupPercent($currentLineup, $otherMatches, $teamId, $teamName,$currentMatch){
        return MatchAnalyseTool::getLineupPercentPrivate($currentLineup,$otherMatches,$teamId,$teamName,$currentMatch,true);
    }

    /**
     * 更新当前比赛阵容首发率
     * @param $match 比赛
     * @param bool $isForce 强制刷新阵容
     */
    public static function updateLineupPercent($match,$isForce = false){
        if (is_null($match)) {
            echo 'updateLineupPercent match is null'.'</br>';
            return;
        }
        $date = date_format(date_create(), 'Y-m-d H:i:s');
        //在现在之后的才更新
        if ((is_null($match->time) ||
            $date > $match->time) && !$isForce){
            echo 'match time before now'.'</br>';
            return;
        }
        //联赛需要爬阵容的才更新
        $league = League::find($match->lid);
        if (isset($league) && $league->lineup_fill >= 1){
            $lineup = MatchLineup::where('id','=',$match->id)->first();
            if (is_null($lineup)){
                $lineup = new MatchLineup();
                $lineup->id = $match->id;
                $lineup->h_id = $match->hid;
                $lineup->a_id = $match->aid;
                $lineup->season = $match->season;
                $lineup->lid = $match->lid;
                $lineup->save();
            }
            if (isset($lineup)){
                //主队阵容列表
                if (isset($lineup->h_lineup)) {
                    $lineupList = explode(',', $lineup->h_lineup);
                }else{
                    $lineupList = array();
                }
                $matches = MatchAnalyseTool::getMatchesByTeamId($match->lid,$match->season,$match->hid,$match->id,$match->hname);
                MatchAnalyseTool::getLineupPercentPrivate($lineupList,$matches,$match->hid,$match->hname,$match,false);
            }
            if (isset($lineup)){
                //客队阵容列表
                if (isset($lineup->a_lineup)) {
                    $lineupList = explode(',', $lineup->a_lineup);
                }else{
                    $lineupList = array();
                }
                $matches = MatchAnalyseTool::getMatchesByTeamId($match->lid,$match->season,$match->aid,$match->id,$match->aname);
                MatchAnalyseTool::getLineupPercentPrivate($lineupList,$matches,$match->aid,$match->aname,$match,false);
            }
        }
    }

    /**
     * 返回这个比赛双方首发球员首发率
     * @param $matchId 比赛id
     * @return array 双方球队array('homeTeam'=>,'guestTeam'=>)
     */
    public function getStartingLineupDataByMatchId($matchId){
        $match = Match::where('id','=',$matchId)->first();
        if (isset($match)){
            $lineup = MatchLineup::where('id','=',$matchId)->first();
            if (isset($lineup) && isset($lineup->h_lineup)){
                //主队阵容列表
                $lineupList = explode(',',$lineup->h_lineup);
                $matches = MatchAnalyseTool::getMatchesByTeamId($match->lid,$match->season,$match->hid,$matchId,$match->hname);
                $hLineupResult = $this->getLineupPercent($lineupList,$matches,$match->hid,$match->hname,$match);
                if (!array_key_exists('msg',$hLineupResult))
                {
                    if ($hLineupResult['total'] > 0){
                        $tmp = array();
                        $tmpWin = array();
                        $tmpDraw = array();
                        $tmpLose = array();
                        foreach ($hLineupResult['lineup'] as $data){
                            if ($data['lineup'] > 0) {
                                $data['winPercent'] = 100*$data['win'] / $data['lineup'];
                                $data['drawPercent'] = 100*$data['draw'] / $data['lineup'];
                                $data['losePercent'] = 100*$data['lose'] / $data['lineup'];
                                $tmpWin[] = $data['winPercent'];
                                $tmpLose[] = $data['losePercent'];
                                $tmpDraw[] = $data['drawPercent'];
                            }
                            if ($data['total'] > 0)
                                $data['isFirst'] = $data['lineup']/$data['total'] > .7 ? 1 : 0;
                            $tmp[] = $data;
                        }
                        $hLineupResult['lineup'] = $tmp;
                        if (0 < count($tmpWin))
                            $hLineupResult['win'] = array_sum($tmpWin)/count($tmpWin);
                    }
                }
            }

            if (isset($lineup) && isset($lineup->a_lineup)){
                //客队阵容列表
                $lineupList = explode(',',$lineup->a_lineup);
                $matches = MatchAnalyseTool::getMatchesByTeamId($match->lid,$match->season,$match->aid,$matchId,$match->aname);
                $aLineupResult = $this->getLineupPercent($lineupList,$matches,$match->aid,$match->aname,$match);
                if (!array_key_exists('msg',$aLineupResult))
                {
                    if ($aLineupResult['total'] > 0){
                        $tmp = array();
                        $tmpWin = array();
                        $tmpDraw = array();
                        $tmpLose = array();
                        foreach ($aLineupResult['lineup'] as $data){
                            if ($data['lineup'] > 0) {
                                $data['winPercent'] = 100*$data['win'] / $data['lineup'];
                                $data['drawPercent'] = 100*$data['draw'] / $data['lineup'];
                                $data['losePercent'] = 100*$data['lose'] / $data['lineup'];
                                $tmpWin[] = $data['winPercent'];
                                $tmpLose[] = $data['losePercent'];
                                $tmpDraw[] = $data['drawPercent'];
                            }
                            if ($data['total'] > 0)
                                $data['isFirst'] = $data['lineup']/$data['total'] > .7 ? 1 : 0;
                            $tmp[] = $data;
                        }
                        $aLineupResult['lineup'] = $tmp;
                        if (0 < count($tmpWin))
                            $aLineupResult['win'] = array_sum($tmpWin)/count($tmpWin);
                    }
                }
            }
        }
        return array('homeTeam'=>isset($hLineupResult)?$hLineupResult:array(),'guestTeam'=>isset($aLineupResult)?$aLineupResult:array());
    }
}