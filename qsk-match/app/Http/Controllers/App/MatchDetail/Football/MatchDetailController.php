<?php
namespace App\Http\Controllers\App\MatchDetail\Football;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FileTool;
use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\MatchAnalysisSameOdd;
use App\Models\LiaoGouModels\MatchEvent;
use App\Models\LiaoGouModels\MatchLineup;
use App\Models\LiaoGouModels\MatchLive;
use App\Models\LiaoGouModels\MatchLiveChannel;
use App\Models\LiaoGouModels\MatchPlayer;
use App\Models\LiaoGouModels\Odd;
use App\Models\LiaoGouModels\Score;
use App\Models\LiaoGouModels\Team;

class MatchDetailController extends Controller {

    public function matchDetailJson($id) {
        $match = Match::where('matches.id', '=', $id)
            ->leftjoin('leagues', 'lid', '=', 'leagues.id')
            ->leftjoin('match_datas', 'matches.id', '=', 'match_datas.id')
            ->select('matches.*', 'matches.id as id', 'leagues.name as lname',
                'match_datas.h_red as h_red','match_datas.a_red as a_red',
                'match_datas.h_yellow as h_yellow','match_datas.a_yellow as a_yellow',
                'match_datas.h_corner as h_corner','match_datas.a_corner as a_corner',
                'match_datas.h_shoot as h_shoot','match_datas.a_shoot as a_shoot',
                'match_datas.h_shoot_in_target as h_shoot_target','match_datas.a_shoot_in_target as a_shoot_target',
                'match_datas.h_control as h_control','match_datas.a_control as a_control',
                'match_datas.h_half_control as h_half_control','match_datas.a_half_control as a_half_control'
                )
            ->first();
        if (is_null($match)){
            return [];
        }
        //是否有直播 开始
        $live = MatchLive::query()->where('sport', MatchLive::kSportFootball)
            ->where('match_id', $id)
            ->first();
        if (isset($live)) {
            $channels = MatchLive::liveChannels($live->id);
            foreach ($channels as $channel) {
                $platform = $channel->platform;
                if ($platform == MatchLiveChannel::kPlatformAll) {
                    $match->pc_live = true;
                    $match->wap_live = true;
                } else if ($platform == MatchLiveChannel::kPlatformPC) {
                    $match->pc_live = true;
                } else if ($platform == MatchLiveChannel::kPlatformWAP) {
                    $match->wap_live = true;
                }
            }
        } else {
            $match->pc_live = false;
            $match->wap_live = false;
        }
        //是否有直播 结束

        //时间
        $match->current_time = $match->getCurMatchTime(true);

        if (isset($match->hid)) {
            $team = Team::where('id', '=', $match->hid)->first();
            $match['hteam'] = $team;
        }
        if (isset($match->aid)) {
            $team = Team::where('id', '=', $match->aid)->first();
            $match['ateam'] = $team;
        }

        //球队排名（取score表数据）
        $scores = Score::query()->where('lid', $match->lid)
            ->where('season', $match->season)
            ->where(function ($q) use ($match){
                if (isset($match->lsid)){
                    $q->where('lsid', '=', $match->lsid);
                } else {
                    $q->whereNull('lsid');
                }
            })
            ->where(function ($q) use ($match){
                $q->where('tid', $match->hid)
                    ->orwhere('tid',$match->aid);
            })
            ->whereNull('kind')
            ->get();
        foreach ($scores as $score){
            if ($score->tid == $match->hid) {
                $rest['hrank'] = $score->rank;
            } else if ($score->tid == $match->aid){
                $rest['arank'] = $score->rank;
            }
        }

        //基本信息
        $matchBase = new MatchDetailBaseController();
        $base = $matchBase->matchDetailBaseData($id);

        $match['hLeagueRank'] = $base['matches']['hLeagueRank'];
        $match['aLeagueRank'] = $base['matches']['aLeagueRank'];
        $match['hLeagueName'] = $base['matches']['hLeagueName'];
        $match['aLeagueName'] = $base['matches']['aLeagueName'];

        $json = [];
        $json['base'] = $base;

        //技术统计
        if (isset($match)) {

            //从文件获取统计数据
            $date = date('Ymd', strtotime($match->time));
            $statisticFile = FileTool::getFileFromLiveEvent($date, $id);
            if (isset($statisticFile)) {
                $statisticFile = json_decode($statisticFile);
            }
            if (isset($statisticFile) && isset($statisticFile->statistic)) {
                $statistics = $statisticFile->statistic;
                foreach ($statistics as $statisticStr) {
                    $statisticStrs = explode(",", $statisticStr);
                    $kind = $statisticStrs[0];
                    switch ($kind) {
                        case "14"://控球
                            $match->h_control = str_replace("%", "", $statisticStrs[0]);
                            $match->a_control = str_replace("%", "", $statisticStrs[1]);
                            break;
                        case "3"://射门
                            $match->h_shoot = $statisticStrs[0];
                            $match->a_shoot = $statisticStrs[1];
                            break;
                        case "4"://射正
                            $match->h_shoot_target = $statisticStrs[0];
                            $match->a_shoot_target = $statisticStrs[1];
                            break;
                        case "11"://黄牌
                            $match->h_yellow = $statisticStrs[0];
                            $match->a_yellow = $statisticStrs[1];
                            break;
                        case "13"://红牌
                            $match->h_red = $statisticStrs[0];
                            $match->a_red = $statisticStrs[1];
                            break;
                        case "6"://角球
                            $match->h_corner = $statisticStrs[0];
                            $match->a_corner = $statisticStrs[1];
                            break;
                        case "43"://进攻
                            break;
                        case "44"://危险进攻
                            break;
                    }
                }
            }

            $match['h_y_p'] = $this->dataPercent($match, 'h_yellow', 'a_yellow');
            $match['a_y_p'] = $this->dataPercent($match, 'a_yellow', 'h_yellow');
            $match['h_r_p'] = $this->dataPercent($match, 'h_red', 'a_red');
            $match['a_r_p'] = $this->dataPercent($match, 'a_red', 'h_red');
            $match['h_cor_p'] = $this->dataPercent($match, 'h_corner', 'a_corner');
            $match['a_cor_p'] = $this->dataPercent($match, 'a_corner', 'h_corner');
            $match['h_sh_p'] = $this->dataPercent($match, 'h_shoot', 'a_shoot');
            $match['a_sh_p'] = $this->dataPercent($match, 'a_shoot', 'h_shoot');
            $match['h_sht_p'] = $this->dataPercent($match, 'h_shoot_target', 'a_shoot_target');
            $match['a_sht_p'] = $this->dataPercent($match, 'a_shoot_target', 'h_shoot_target');
            $match['h_con_p'] = $this->dataPercent($match, 'h_control', 'a_control');
            $match['a_con_p'] = $this->dataPercent($match, 'a_control', 'h_control');
            $match['h_hcon_p'] = $this->dataPercent($match, 'h_half_control', 'h_half_control');
            $match['a_hcon_p'] = $this->dataPercent($match, 'a_half_control', 'a_half_control');
        }

        $json['match'] = $match;
        return $json;
    }

    public function matchBaseData($mid) {
        //阵容
        $lineup = $this->matchDetailLineup($mid);
        $rest['lineup'] = $lineup;
        $rest['aname'] = $lineup['aname'];
        $rest['hname'] = $lineup['hname'];

        $match = Match::query()->find($mid);
        $date = date('Ymd', strtotime($match->time));
        //事件
        $eventFile = FileTool::getFileFromLiveEvent($date, $mid);
        if (isset($eventFile)) {
            $eventFile = json_decode($eventFile);
        }
        if (isset($eventFile) && isset($eventFile->event)) {
            $tempEvents = $eventFile->event;
            $events = array();
            foreach ($tempEvents as $eventStr) {
                $eventStrs = explode(",", $eventStr);
                $event = array();
                $event['is_home'] = $eventStrs[0];
                $event['kind'] = $eventStrs[1];
                $event['happen_time'] = $eventStrs[2];

                $playerStr = $eventStrs[3];
                if (str_contains($playerStr, '^')) {
                    $playerStrs = explode("^", $playerStr);
                    $event['player_name_j'] = $playerStrs[0];
                    $event['player_name_j2'] = $playerStrs[1];
                } else {
                    $event['player_name_j'] = $playerStr;
                }
                $events[] = $event;
            }
            $rest['events'] = $events;
            $rest['last_event_time'] = count($events) == 0 ? '' : $events[count($events) - 1]['happen_time'];//最后的事件时间
        } else {
            $events = MatchEvent::query()->where('mid', $mid)->orderBy('happen_time', 'desc')->get();
            $rest['events'] = $events;
            $rest['last_event_time'] = count($events) == 0 ? '' : $events[count($events) - 1]->happen_time;//最后的事件时间
        }

        //技术统计
        if (isset($match)) {

            //从文件获取统计数据
            $date = date('Ymd', strtotime($match->time));
            $statisticFile = FileTool::getFileFromLiveEvent($date, $mid);
            if (isset($statisticFile)) {
                $statisticFile = json_decode($statisticFile);
            }
            if (isset($statisticFile) && isset($statisticFile->statistic)) {
                $statistics = $statisticFile->statistic;
                foreach ($statistics as $statisticStr) {
                    $statisticStrs = explode(",", $statisticStr);
                    $kind = $statisticStrs[0];
                    switch ($kind) {
                        case "14"://控球
                            $match->h_control = str_replace("%", "", $statisticStrs[0]);
                            $match->a_control = str_replace("%", "", $statisticStrs[1]);
                            break;
                        case "3"://射门
                            $match->h_shoot = $statisticStrs[0];
                            $match->a_shoot = $statisticStrs[1];
                            break;
                        case "4"://射正
                            $match->h_shoot_target = $statisticStrs[0];
                            $match->a_shoot_target = $statisticStrs[1];
                            break;
                        case "11"://黄牌
                            $match->h_yellow = $statisticStrs[0];
                            $match->a_yellow = $statisticStrs[1];
                            break;
                        case "13"://红牌
                            $match->h_red = $statisticStrs[0];
                            $match->a_red = $statisticStrs[1];
                            break;
                        case "6"://角球
                            $match->h_corner = $statisticStrs[0];
                            $match->a_corner = $statisticStrs[1];
                            break;
                        case "43"://进攻
                            break;
                        case "44"://危险进攻
                            break;
                    }
                }
            }

            $match['h_y_p'] = $this->dataPercent($match, 'h_yellow', 'a_yellow');
            $match['a_y_p'] = $this->dataPercent($match, 'a_yellow', 'h_yellow');
            $match['h_r_p'] = $this->dataPercent($match, 'h_red', 'a_red');
            $match['a_r_p'] = $this->dataPercent($match, 'a_red', 'h_red');
            $match['h_cor_p'] = $this->dataPercent($match, 'h_corner', 'a_corner');
            $match['a_cor_p'] = $this->dataPercent($match, 'a_corner', 'h_corner');
            $match['h_sh_p'] = $this->dataPercent($match, 'h_shoot', 'a_shoot');
            $match['a_sh_p'] = $this->dataPercent($match, 'a_shoot', 'h_shoot');
            $match['h_sht_p'] = $this->dataPercent($match, 'h_shoot_target', 'a_shoot_target');
            $match['a_sht_p'] = $this->dataPercent($match, 'a_shoot_target', 'h_shoot_target');
            $match['h_con_p'] = $this->dataPercent($match, 'h_control', 'a_control');
            $match['a_con_p'] = $this->dataPercent($match, 'a_control', 'h_control');
            $match['h_hcon_p'] = $this->dataPercent($match, 'h_half_control', 'h_half_control');
            $match['a_hcon_p'] = $this->dataPercent($match, 'a_half_control', 'a_half_control');
        }

        $rest['match'] = $match;

        return $rest;
    }

    /**
     * 阵容
     * @param $id
     * @return mixed
     */
    private function matchDetailLineup($id){
        $match = Match::where('id',$id)->first();
        $lineup = MatchLineup::where('id', '=', $id)->first();
        if (isset($match->hid)) {
            $lineupArray = array();
            $lineupBackArray = array();
            if (isset($lineup) && isset($lineup->h_lineup)) {
                $line = explode(',', $lineup->h_lineup);
                $first = explode(',', $lineup->h_first);
                $back = explode(',', $lineup->h_lineup_bak);
                foreach ($line as $num) {
                    $isFirst = false;
                    if (in_array($num, $first)) {
                        $isFirst = true;
                    }
                    $player = MatchPlayer::where('tid', '=', $match->hid)
                        ->where('lid', '=', $match->lid)
                        ->where('num', '=', $num)
                        ->first();
                    if (isset($player)) {
                        $lineupArray[] = array('num' => $num, 'name' => $player->name, 'first' => $isFirst ? 1 : 0);
                    } else {
                        $lineupArray[] = array('num' => $num, 'first' => $isFirst ? 1 : 0);
                    }
                }
                foreach ($back as $num) {
                    $player = MatchPlayer::where('tid', '=', $match->hid)
                        ->where('lid', '=', $match->lid)
                        ->where('num', '=', $num)
                        ->first();
                    $lineupBackArray[] = array('num' => $num, 'name' => $player->name);
                }
            }
        }
        $result['home']['first'] = $lineupArray;
        $result['home']['back'] = $lineupBackArray;
        $result['home']['h_first'] = empty($lineup->h_first) ? [] : explode(',', $lineup->h_first);
        if (isset($match->aid)) {
            $lineupArray = array();
            $lineupBackArray = array();
            if (isset($lineup) && isset($lineup->a_lineup)) {
                $line = explode(',', $lineup->a_lineup);
                $first = explode(',', $lineup->a_first);
                $back = explode(',', $lineup->a_lineup_bak);
                foreach ($line as $num) {
                    $isFirst = false;
                    if (in_array($num, $first)) {
                        $isFirst = true;
                    }
                    $player = MatchPlayer::where('tid', '=', $match->aid)
                        ->where('lid', '=', $match->lid)
                        ->where('num', '=', $num)
                        ->first();
                    if (isset($player)) {
                        $lineupArray[] = array('num' => $num, 'name' => $player->name, 'first' => $isFirst ? 1 : 0);
                    } else {
                        $lineupArray[] = array('num' => $num, 'first' => $isFirst ? 1 : 0);
                    }
                }
                foreach ($back as $num) {
                    $player = MatchPlayer::where('tid', '=', $match->aid)
                        ->where('lid', '=', $match->lid)
                        ->where('num', '=', $num)
                        ->first();
                    $lineupBackArray[] = array('num' => $num, 'name' => $player->name);
                }
            }
        }
        $result['away']['first'] = $lineupArray;
        $result['away']['back'] = $lineupBackArray;
        $result['away']['h_first'] = empty($lineup->a_first) ? [] : explode(',', $lineup->a_first);
        if (isset($lineup->h_lineup_percent))
            $result['h_lineup_per'] = $lineup->h_lineup_percent;
        if (isset($lineup->a_lineup_percent))
            $result['a_lineup_per'] = $lineup->a_lineup_percent;
        $result['hname'] = $match->hname;
        $result['aname'] = $match->aname;
        return $result;
    }

    protected function dataPercent($match, $hkey, $akey) {
        if (!isset($match) || empty($hkey) || empty($akey) || !isset($match[$hkey]) || !isset($match[$akey])
            || ($match[$hkey] + $match[$akey]) == 0
        ) {
            return 0;
        }
        return $match[$hkey] / ($match[$hkey] + $match[$akey]);
    }
}