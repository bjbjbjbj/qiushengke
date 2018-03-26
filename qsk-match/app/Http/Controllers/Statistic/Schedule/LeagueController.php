<?php
namespace App\Http\Controllers\Statistic\Schedule;

use App\Http\Controllers\Statistic\OddCalculateTool;
use App\Http\Controllers\Statistic\StatisticFileTool;
use App\Models\LiaoGouModels\BasketMatch;
use App\Models\LiaoGouModels\BasketLeague;
use App\Models\LiaoGouModels\BasketSeason;
use App\Models\LiaoGouModels\BasketTeam;
use App\Models\LiaoGouModels\League;
use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\MatchLive;
use App\Models\LiaoGouModels\Odd;
use App\Models\LiaoGouModels\Score;
use App\Models\LiaoGouModels\Season;
use App\Models\LiaoGouModels\Stage;
use Illuminate\Http\Request;

/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/3/9
 * Time: 上午10:11
 */

class LeagueController{

    const footLeague = [
        array('id'=>'360','win_id'=>60,'name'=>'中超','type'=>1),
        array('id'=>'1','win_id'=>36,'name'=>'英超','type'=>1),
        array('id'=>'42','win_id'=>31,'name'=>'西甲','type'=>1),
        array('id'=>'30','win_id'=>34,'name'=>'意甲','type'=>1),
        array('id'=>'64','win_id'=>11,'name'=>'法甲','type'=>1),
        array('id'=>'51','win_id'=>8,'name'=>'德甲','type'=>1),
        array('id'=>'642','win_id'=>192,'name'=>'亚冠','type'=>2),
        array('id'=>'602','win_id'=>103,'name'=>'欧冠','type'=>2),
        array('id'=>'564','win_id'=>75,'name'=>'世界杯','type'=>2)
    ];

    const basketLeague = [
        array('id'=>'1','win_id'=>1,'name'=>'NBA','type'=>1),
        array('id'=>'4','win_id'=>5,'name'=>'CBA','type'=>1),
        array('id'=>'2','win_id'=>2,'name'=>'WNBA','type'=>1),
        array('id'=>'89','win_id'=>7,'name'=>'Euro','type'=>1),
    ];

    public function onMatchLeagueScheduleStatic($sport, $lid) {
        if ($sport == MatchLive::kSportBasketball) {
            $data = $this->getLeagueScheduleBK($lid);
        } else {
            $data = $this->getLeagueSchedule($lid);
        }
        return response()->json($data);
    }

    public function onMatchLeagueManualStatic($sport) {
        $lastTime = time();
        $isBasket = $sport == MatchLive::kSportBasketball;
        if ($isBasket) {
            $leagueArray = self::basketLeague;
        } else {
            $leagueArray = self::footLeague;
        }
        foreach ($leagueArray as $league) {
            $lid = $league['id'];
            if ($isBasket) {
                $data = $this->getLeagueScheduleBK($lid);
            } else {
                $data = $this->getLeagueSchedule($lid);
            }
            StatisticFileTool::putFileToLeague($data, $sport, $lid);
        }
        dump(time()-$lastTime);
    }

    /************ 足球 ***********/
    //获取杯赛数据
    public function getLeagueSchedule($lid){
        //赛事
        $league = League::query()->select("id", "name", "type", "hot", "describe")->find($lid);
        if (is_null($league)){
            return null;
        }
        $result['league'] = $league;
        //赛季
        $season = Season::query()
            ->select("name", "year", "total_round", "curr_round", "start")
            ->where('lid',$lid)
            ->orderby('year','desc')
            ->first();
        if (is_null($season)){
            return null;
        }
        $seasonStr = $season->name;
        $result['season'] = $season;
        //整理比赛
        if ($league->type == 1){
            //联赛
            $result['schedule'] = $this->_getLeagueSchedule($league,$seasonStr,$season->total_round);
            //积分榜
            $result['score'] = $this->_getLeagueScore($league,$seasonStr);
        }
        else{
            //杯赛
            $result['stages'] = $this->_getCupLeagueSchedule($league,$seasonStr);
        }

        return $result;
    }

    //获取杯赛赛程
    private function _getCupLeagueSchedule($league,$seasonStr){
        $stages = Stage::where('lid',$league->id)
            ->where('season',$seasonStr)
            ->orderby('id','asc')
            ->get();

        $array = array();
        foreach ($stages as $stage){
            //看看是否主客互换(16强之类)
            $tmp = $this->_getCupLeagueStageMatches($league->id,$seasonStr,$stage->id,!is_null($stage['group']));
            if (!is_null($stage['group'])){
                $stage['groupMatch'] = $tmp['groupMatch'];
            }
            else if ($tmp['combo'] == 1 && is_null($stage['group'])){
                $stage['combo'] = $tmp['comboMatch'];
            }
            else{
                $stage['matches'] = $tmp['matches'];
            }
            $array[] = $stage;
        }

        return $array;
    }

    //获取比赛赛程对弈比赛列表
    private function _getCupLeagueStageMatches($lid,$season,$stage,$isGroup){
        $matches = Match::from("matches as m")
            ->addSelect('m.id as mid', 'm.hid', 'm.aid')
            ->leftjoin('odds as asia', function ($join) {
                $join->on('m.id', '=', 'asia.mid');
                $join->where('asia.type', '=', Odd::k_odd_type_asian);
                $join->where('asia.cid', '=', Odd::default_calculate_cid);
            })->leftjoin('odds as ou', function ($join) {
                $join->on('m.id', '=', 'ou.mid');
                $join->where('ou.type', '=', Odd::k_odd_type_europe);
                $join->where('ou.cid', '=', Odd::default_calculate_cid);
            })->leftjoin('odds as goal', function ($join) {
                $join->on('m.id', '=', 'goal.mid');
                $join->where('goal.type', '=', Odd::k_odd_type_ou);
                $join->where('goal.cid', '=', Odd::default_calculate_cid);
            })
            ->addSelect('asia.up1 as asiaup1', 'asia.middle1 as asiamiddle1', 'asia.down1 as asiadown1',
                'asia.up2 as asiaup2', 'asia.middle2 as asiamiddle2', 'asia.down2 as asiadown2',
                'ou.up1 as ouup1', 'ou.middle1 as oumiddle1', 'ou.down1 as oudown1',
                'ou.up2 as ouup2', 'ou.middle2 as oumiddle2', 'ou.down2 as oudown2',
                'goal.up1 as goalup1', 'goal.middle1 as goalmiddle1', 'goal.down1 as goaldown1',
                'goal.up2 as goalup2', 'goal.middle2 as goalmiddle2', 'goal.down2 as goaldown2')
            ->leftJoin("teams as home", "m.hid", "home.id")
            ->leftJoin("teams as away", "m.aid", "away.id")
            ->addSelect('m.group','m.lid', 'm.status', 'm.genre','m.betting_num',
                'm.time', 'm.timehalf', 'm.hname','m.aname',
                'm.round', 'm.hrank', 'm.arank',
                'm.hscore','m.ascore', 'm.hscorehalf','m.ascorehalf')
            ->addSelect("home.icon as hicon", "away.icon as aicon")
            ->where('lid',$lid)
            ->where('season',$season)
            ->where('stage',$stage)
            ->orderby('time','asc')
            ->get();

        $result = array();

        if (!$isGroup) {
            //看看是否主客互换(16强之类)
            foreach ($matches as $match) {
                $match = OddCalculateTool::formatOddData($match);
                $match->sport = MatchLive::kSportFootball;
                $match->current_time = $match->getCurMatchTime(true);
                $match->time = strtotime($match->time);
                if (isset($match->timehalf)) {
                    $match->timehalf = strtotime($match->timehalf);
                } else {
                    $match->timehalf = $match->time;
                }

                $hid = $match->hid;
                $aid = $match->aid;
                $key = $hid . '_' . $aid;
                $key2 = $aid . '_' . $hid;
                //先看看是否有之前的比赛是主客互换
                if (!array_key_exists($key, $result) && !array_key_exists($key2, $result)) {
                    $hname = $match->hname;
                    $aname = $match->aname;
                    $result[$key] = array();
                    $result[$key]['hname'] = $hname;
                    $result[$key]['aname'] = $aname;
                    $result[$key]['hid'] = $hid;
                    $result[$key]['aid'] = $aid;
                    $result[$key]['matches'] = array();
                    $result[$key]['matches'][] = $match;
                    if ($match->status == -1){
                        $result[$key]['hscore'] = $match->hscore;
                        $result[$key]['ascore'] = $match->ascore;
                    }
                } else {
                    $result[$key2]['matches'][] = $match;
                    if ($match->status == -1){
                        $result[$key2]['hscore'] += $match->ascore;
                        $result[$key2]['ascore'] += $match->hscore;
                    }
                }
            }
            if (count($result) * 2 == count($matches)) {
                return array('combo' => 1, 'comboMatch' => $result, 'matches' => $matches);
            } else {
                return array('combo' => 0, 'matches' => $matches);
            }
        }
        else{
            //小组赛
            foreach ($matches as $match){
                $match = OddCalculateTool::formatOddData($match);
                $match->sport = MatchLive::kSportFootball;
                $match->current_time = $match->getCurMatchTime(true);
                $match->time = strtotime($match->time);
                if (isset($match->timehalf)) {
                    $match->timehalf = strtotime($match->timehalf);
                } else {
                    $match->timehalf = $match->time;
                }

                $group = $match['group'];
                if (!array_key_exists($group,$result)){
                    $result[$group] = array();
                    $result[$group]['matches'] = array();
                }
                $result[$group]['matches'][] = $match;
            }
            ksort($result);

            //积分
            foreach ($result as $key=>$item){
                $scores = Score::from("scores as s")->where('lid',$lid)
                    ->leftjoin('teams as team', function ($join) {
                        $join->on('s.tid', '=', 'team.id');
                    })
                    ->addSelect('s.lid', 's.lsid', 's.season', 's.stage', 's.tid',
                        's.group','s.count','s.score','s.goal','s.fumble',
                        's.win','s.draw','s.lose','s.status','s.name',
                        's.rank','s.kind')
                    ->addSelect('team.name as tname','team.icon as ticon')
                    ->where('season',$season)
                    ->where('group',$key)
                    ->where('stage',$stage)
                    ->orderby('score','desc')
                    ->get();

                $result[$key]['scores'] = $scores;
            }

            return array('combo' => 0, 'groupMatch' => $result);
        }
    }

    //联赛积分
    private function _getLeagueScore($league,$seasonStr){
        $scores = Score::where('lid',$league->id)
            ->where('season',$seasonStr)
            ->leftjoin('teams as team', function ($join) {
                $join->on('scores.tid', '=', 'team.id');
            })
            ->whereNull('kind')
            ->addSelect('scores.*')
            ->addSelect('team.name as tname','team.icon as ticon')
            ->orderby('rank','asc')
            ->get();

        //近6场
        foreach ($scores as $score){
            $matches = Match::where(function ($q) use($score,$seasonStr){
                $q->where('hid',$score->tid)
                    ->orwhere('aid',$score->tid);
            })
                ->where('lid',$score->lid)
                ->where('season',$seasonStr)
                ->where('status',-1)
                ->orderby('time','desc')
                ->take(6)
                ->get();
            $result = array();
            foreach ($matches as $match){
                if ($match->hscore > $match->ascore){
                    if ($score->tid == $match->hid){
                        $result[] = 3;
                    }
                    else{
                        $result[] = 0;
                    }
                }
                else if ($match->hscore < $match->ascore){
                    if ($score->tid == $match->hid){
                        $result[] = 0;
                    }
                    else{
                        $result[] = 3;
                    }
                }
                else{
                    $result[] = 1;
                }
            }
            $score['six'] = $result;
        }

        return $scores;
    }

    //获取联赛赛程
    private function _getLeagueSchedule($league,$season,$total_round){
        $schedule = array();
        for ($i = 0 ; $i < $total_round ; $i++){
            $schedule[$i+1] = $this->_getLeagueScheduleMatch($league->id,$season,$i + 1);
        }
        return $schedule;
    }

    //联赛轮次比赛列表
    private function _getLeagueScheduleMatch($lid,$season,$round){
        $matches = Match::from("matches as m")
            ->addSelect('m.id as mid', 'm.hid', 'm.aid')
            ->leftjoin('odds as asia', function ($join) {
                $join->on('m.id', '=', 'asia.mid');
                $join->where('asia.type', '=', Odd::k_odd_type_asian);
                $join->where('asia.cid', '=', Odd::default_calculate_cid);
            })->leftjoin('odds as ou', function ($join) {
                $join->on('m.id', '=', 'ou.mid');
                $join->where('ou.type', '=', Odd::k_odd_type_europe);
                $join->where('ou.cid', '=', Odd::default_calculate_cid);
            })->leftjoin('odds as goal', function ($join) {
                $join->on('m.id', '=', 'goal.mid');
                $join->where('goal.type', '=', Odd::k_odd_type_ou);
                $join->where('goal.cid', '=', Odd::default_calculate_cid);
            })
            ->addSelect('asia.up1 as asiaup1', 'asia.middle1 as asiamiddle1', 'asia.down1 as asiadown1',
                'asia.up2 as asiaup2', 'asia.middle2 as asiamiddle2', 'asia.down2 as asiadown2',
                'ou.up1 as ouup1', 'ou.middle1 as oumiddle1', 'ou.down1 as oudown1',
                'ou.up2 as ouup2', 'ou.middle2 as oumiddle2', 'ou.down2 as oudown2',
                'goal.up1 as goalup1', 'goal.middle1 as goalmiddle1', 'goal.down1 as goaldown1',
                'goal.up2 as goalup2', 'goal.middle2 as goalmiddle2', 'goal.down2 as goaldown2')
            ->leftJoin("teams as home", "m.hid", "home.id")
            ->leftJoin("teams as away", "m.aid", "away.id")
            ->addSelect('m.lid', 'm.status', 'm.genre','m.betting_num',
                'm.time', 'm.timehalf', 'm.hname','m.aname',
                'm.round', 'm.hrank', 'm.arank',
                'm.hscore','m.ascore', 'm.hscorehalf','m.ascorehalf')
            ->addSelect("home.icon as hicon", "away.icon as aicon")
            ->where('lid',$lid)
            ->where('season',$season)
            ->where('round',$round)
            ->orderby('time','asc')
            ->get();

        $result = array();
        foreach ($matches as $match){
            $match = OddCalculateTool::formatOddData($match);
            $match->sport = MatchLive::kSportFootball;
            $match->current_time = $match->getCurMatchTime(true);
            $match->time = strtotime($match->time);
            if (isset($match->timehalf)) {
                $match->timehalf = strtotime($match->timehalf);
            } else {
                $match->timehalf = $match->time;
            }
            $result[] = $match;
        }

        return $result;
    }

    /************ 篮球 ***********/
    //获取篮球赛程,当前到后7天
    public function getLeagueScheduleBK($lid, $start = null){
        if (is_null($start)){
            $start = date('Y-m-d');
        }

        $end = strtotime($start) + 7*24*3600;
        $end = date('Y-m-d',$end);

        //赛事
        $league = BasketLeague::query()->select("id", "name","type","system","hot", "describe")->find($lid);
        if (is_null($league)){
            return null;
        }
        $result['league'] = $league;
        //赛季
        $season = BasketSeason::query()
            ->select("name", "year", "kind")
            ->where('lid',$lid)
            ->orderby('year','desc')
            ->first();
        if (is_null($season)){
            return null;
        }
        $seasonStr = $season->name;
        $result['season'] = $season;
        //整理比赛
//        if ($league->type == 1){
            //联赛
        $result['schedule'] = $this->_getLeagueSecheduleMatchBK($league->id,$seasonStr,$start,$end);
//        }
        return $result;
    }

    public function getLeagueScheduleBkByDate(Request $request, $lid) {
        //赛事
        $league = BasketLeague::query()->find($lid);
        if (is_null($league)){
            return null;
        }
        //赛季
        $season = BasketSeason::query()
            ->select("name", "year", "kind")
            ->where('lid',$lid)
            ->orderby('year','desc')
            ->first();
        if (is_null($season)){
            return null;
        }
        $seasonStr = $season->name;

        $start = $request->get('date', date('Y-m-d'));
        $end = date('Y-m-d',strtotime('+1 day', strtotime($start)));

        $matches = $this->_getLeagueSecheduleMatchBK($lid, $seasonStr, $start, $end);
        return response()->json($matches);
    }

    private function _getLeagueSecheduleMatchBK($lid,$seasonStr,$start,$end){
        $matches = BasketMatch::from("basket_matches as m")
            ->addSelect('m.id as mid', 'm.hid', 'm.aid')
            ->leftjoin('basket_odds as asia', function ($join) {
                $join->on('m.id', '=', 'asia.mid');
                $join->where('asia.type', '=', Odd::k_odd_type_asian);
                $join->where('asia.cid', '=', Odd::default_calculate_cid);
            })->leftjoin('basket_odds as goal', function ($join) {
                $join->on('m.id', '=', 'goal.mid');
                $join->where('goal.type', '=', Odd::k_odd_type_ou);
                $join->where('goal.cid', '=', Odd::default_calculate_cid);
            })->leftjoin('basket_odds as ou', function ($join) {
                $join->on('m.id', '=', 'ou.mid');
                $join->where('ou.type', '=', Odd::k_odd_type_europe);
                $join->where('ou.cid', '=', Odd::default_calculate_cid);
            })
            ->addSelect('asia.up1 as asiaup1', 'asia.middle1 as asiamiddle1', 'asia.down1 as asiadown1',
                'asia.up2 as asiaup2', 'asia.middle2 as asiamiddle2', 'asia.down2 as asiadown2',
                'ou.up1 as ouup1', 'ou.middle1 as oumiddle1', 'ou.down1 as oudown1',
                'ou.up2 as ouup2', 'ou.middle2 as oumiddle2', 'ou.down2 as oudown2',
                'goal.up1 as goalup1', 'goal.middle1 as goalmiddle1', 'goal.down1 as goaldown1',
                'goal.up2 as goalup2', 'goal.middle2 as goalmiddle2', 'goal.down2 as goaldown2')
            ->leftJoin("basket_teams as home", "m.hid", "home.id")
            ->leftJoin("basket_teams as away", "m.aid", "away.id")
            ->addSelect('m.lid', 'm.status','m.betting_num',
                'm.time', 'm.timehalf', 'm.hname','m.aname',
                'm.hscore','m.ascore')
            ->addSelect("home.icon as hicon", "away.icon as aicon")
            ->where('lid',$lid)
            ->where('season',$seasonStr)
            ->where('time','>=',$start)
            ->where('time','<',$end)
            ->orderby('time','asc')
            ->get();
        $result = array();
        foreach ($matches as $match){
            $match->makeHidden('timehalf');
            $match = OddCalculateTool::formatOddData($match);
            if (isset($match->betting_num)) {
                $match->betting_num = str_replace(" ", "", $match->betting_num);
            }
            $match->hicon = BasketTeam::getIcon($match->hicon);
            $match->aicon = BasketTeam::getIcon($match->aicon);

            $match->sport = MatchLive::kSportBasketball;
            $match->time = strtotime($match->time);

            $result[] = $match;
        }
        return $result;
    }
}