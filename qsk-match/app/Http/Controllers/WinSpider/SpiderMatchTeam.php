<?php
/**
 * 用于爬球探比赛及比赛相关球队,这里的球队只是通过比赛id获取,故不分开
 * Created by PhpStorm.
 * User: BJ
 * Date: 17/2/16
 * Time: 下午12:29
 */

namespace App\Http\Controllers\WinSpider;

use App\Http\Controllers\FileTool;
use App\Http\Controllers\LiaogouAnalyse\MatchAnalyseTool;
use App\Http\Controllers\Statistic\Change\MatchDataChangeTool;
use App\Http\Controllers\Statistic\StatisticFileTool;
use App\Models\LiaoGouModels\MatchLive;
use App\Models\WinModels\MatchLineup;
use App\Models\WinModels\MatchEvent;
use App\Models\WinModels\MatchPlayer;
use App\Models\WinModels\Match;
use App\Models\WinModels\MatchData;
use App\Models\WinModels\Odd;
use App\Models\WinModels\Season;
use App\Models\WinModels\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait SpiderMatchTeam{
    use SpiderTeamKingOdds;
    /**
     * 根据球探id爬比赛数据,并更新到liaogou比赛库
     * @param $id
     * @param bool $team 是否需要爬球队
     * @param bool $data 是否需要爬比赛详情
     * @return \App\Models\Match|null 返回id对应球探库match数据
     */
    private function matchDetail($id, $team = false, $data = false, $refreshData = false)
    {
        $url = "http://txt.win007.com/phone/txt/analysisheader/cn/" . substr($id, 0, 1) . "/" . substr($id, 1, 2) . "/$id.txt";
        $str = $this->spiderTextFromUrl($url);
        if ($str) {
            if(count(explode("^", $str)) >= 26) {
                list(
                    $hname, $aname, $hnamebig, $anamebig, $status,
                    $dateStr, $var1, $var2, $hicon, $aicon,
                    $hscore, $ascore, $var3, $lid, $ltype,
                    $lname, $season, $hid, $aid, $var4,
                    $var5, $var6, $var7, $var8, $var9,
                    $datehalfstr
                    ) = explode("^", $str);
                $date = date('Y-m-d H:i:s', strtotime($dateStr));
                $datehalf = date('Y-m-d H:i:s', strtotime($datehalfstr));
                $m = Match::find($id);
                if (!isset($m)) {
                    $m = new Match();
                    $m->id = $id;
                    $m->is_odd = 0;
                }
                if ($lid != '') {
                    $m->lid = $lid;
                }
                if (!empty($season)) {
                    //创建该赛事赛季数据
                    $seasons = explode(",", $season);
                    foreach ($seasons as $sea) {
                        $s = Season::where(['lid' => $lid, 'name' => $sea])->first();
                        if (!isset($s)) {
                            $s = new Season();
                            $s->lid = $lid;
                            $s->name = $sea;
                            $s->year = explode("-", $sea)[0];
                            $s->save();
                            \App\Models\LiaoGouModels\Season::saveDataWithWinData($s, true);
                        }
                        else{
                            \App\Models\LiaoGouModels\Season::saveDataWithWinData($s, true);
                        }
                    }
                    //如果比赛没有season字段,用返回season字段,由于2009的赛事也会返回相同数据,没有标识所属赛季,故增加赛季与比赛时间是否匹配判断
                    if (empty($m->season)) {
                        $year = substr($dateStr, 0, 4);
                        $month = substr($dateStr, 4, 2);
                        echo "$season,year:$year,month:$month";
                        if ($year == date('Y')) { //只取今年的最新赛季
                            $sea = $seasons[0]; //只取最新的赛季
                            if (($year == $sea) ||
                                strstr($sea, $year)
                            ) {
                                $m->season = $sea;
                            }
                        }
                    }
                    //同样,因为比赛没有标识赛季,这里爬数据的时候加开始时间
                    //有逻辑问题,假设2015-2016 2016-2017赛季 2016-1比赛会归入2016-2017,而且2016-2017start会被提前
                    if (!empty($m->season)) {
                        $s = Season::where(['lid' => $lid, 'name' => $m->season])->first();
                        if (isset($s) && (empty($s->start) || $s->start > $dateStr)) {
                            $s->start = $dateStr;
                            $s->save();
                            \App\Models\LiaoGouModels\Season::saveDataWithStartTime($s);
                        }
                    }

                }
                $m->time = $date;
                $m->timehalf = $datehalf;
                $m->status = $status;
//                $m->setStatus($status);
                $m->hname = $hname;
                $m->aname = $aname;
                $m->hid = $hid != '' ? $hid : 0;
                $m->aid = $aid != '' ? $aid : 0;
                $m->hscore = $hscore;
                $m->ascore = $ascore;
                $m->neutral = str_contains($hname, "(中)") ? 1 : 0;
                $m->save();

//            echo "$str<br>";
                //球队详情
                if ($team) {
                    $this->teamDetail($hid, $id, $hname);
                    $this->teamDetail($aid, $id, $aname);
                }

                //保存到liaogou库
                $lgm = \App\Models\LiaoGouModels\Match::saveWithWinData($m);

                //如果liaogou没有tid,但是球探有,这个对球队信息要做一次保存操作
                if (isset($lgm)
                    && (is_null($lgm->hid) || is_null($lgm->aid))
                    && (isset($m->hid) && isset($m->aid))
                ) {
                    if (is_null($lgm->hid))
                        $this->teamDetail($m->hid, $id, $hname, true);
                    if (is_null($lgm->aid))
                        $this->teamDetail($m->aid, $id, $aname, true);

                    $lgm = \App\Models\LiaoGouModels\Match::saveWithWinData($m);
                }

                //盘王(这里的算法没有实际效果，弃用了)
//                if (isset($lgm)) {
////                    $this->addMatchOddToTeamOddResult($lgm);
//                    $this->setTeamOddResultNeedCalculateByMatch($lgm, $isToOver);
//                }

                //比赛数据
                if ($data) {
                    if (($lgm->genre >> 1 & 1)) {
                        $this->matchData($id, $refreshData);
                    } else {
                        echo "lg_mid = $lgm->id match data save skipped! <br>";
                    }
                }
                return $m;
            }
        }
        else{
//            $lgm = \App\Models\LiaoGouModels\Match::getMatchWith($id,'win_id');
//            $lgm->delete();
        }
        return null;
    }

    /**
     * 更新比赛数据,例如事件等
     * @param $id
     */
    private function matchData($id,$isReset = false)
    {
        $url = "http://txt.win007.com/phone/airlive/cn/" . substr($id, 0, 1) . "/" . substr($id, 1, 2) . "/$id.htm";
        $str = $this->spiderTextFromUrl($url);
        if ($str) {
            $jm = json_decode($str);
            if ($jm) {
                $match = Match::find($id);
                //数据
                if (!isset($match)) {
                    return;
                }
                $lg_match = \App\Models\LiaoGouModels\Match::getMatchWith($match->id, "win_id");
                if (!isset($lg_match)) {
                    $lg_match = \App\Models\LiaoGouModels\Match::saveWithWinData($match);
                }

                $lg_mid = isset($lg_match) ? $lg_match->id : 0;
                $lg_lid = isset($lg_match) ? $lg_match->lid : 0;
                $lg_hid = isset($lg_match) ? $lg_match->hid : 0;
                $lg_aid = isset($lg_match) ? $lg_match->aid : 0;

                $md = MatchData::find($id);
                if (!isset($md)) {
                    $md = new MatchData();
                    $md->id = $id;
                }
                //统计是否有更新,有才保存
                $anaylseChange = false;
                foreach ($jm->listItemsTech as $item) {
                    switch ($item->Name) {
                        case "角球": {
                            $anaylseChange = true;
                            $md->h_corner = $item->HomeData;
                            $md->a_corner = $item->AwayData;
                            break;
                        }
                        case "黄牌": {
                            $anaylseChange = true;
                            $md->h_yellow = $item->HomeData;
                            $md->a_yellow = $item->HomeData;
                            break;
                        }
                        case "红牌": {
                            $anaylseChange = true;
                            $md->h_red = $item->HomeData;
                            $md->a_red = $item->AwayData;
                            break;
                        }
                        case "射门": {
                            $anaylseChange = true;
                            $md->h_shoot = $item->HomeData;
                            $md->a_shoot = $item->AwayData;
                            break;
                        }
                        case "控球率": {
                            $anaylseChange = true;
                            $md->h_control = intval($item->HomeData);
                            $md->a_control = intval($item->AwayData);
                            break;
                        }
                        case "半场角球": {
                            $anaylseChange = true;
                            $md->h_half_corner = $item->HomeData;
                            $md->a_half_corner = $item->AwayData;
                            break;
                        }
                        case "射正": {
                            $anaylseChange = true;
                            $md->h_shoot_in_target = $item->HomeData;
                            $md->a_shoot_in_target = $item->AwayData;
                            break;
                        }
                        case "射门被挡": {
                            $anaylseChange = true;
                            $md->h_shoot_block = $item->HomeData;
                            $md->a_shoot_block = $item->AwayData;
                            break;
                        }
                        case "任意球": {
                            $anaylseChange = true;
                            $md->h_free = $item->HomeData;
                            $md->a_free = $item->AwayData;
                            break;
                        }
                        case "半场控球率": {
                            $anaylseChange = true;
                            $md->h_half_control = intval($item->HomeData);
                            $md->a_half_control = intval($item->AwayData);
                            break;
                        }
                        case "传球": {
                            $anaylseChange = true;
                            $md->h_pass = $item->HomeData;
                            $md->a_pass = $item->AwayData;
                            break;
                        }
                        case "传球成功率": {
                            $anaylseChange = true;
                            $md->h_pass_percent = intval($item->HomeData);
                            $md->a_pass_percent = intval($item->AwayData);
                            break;
                        }
                        case "犯规": {
                            $anaylseChange = true;
                            $md->h_foul = $item->HomeData;
                            $md->a_foul = $item->AwayData;
                            break;
                        }
                        case "越位": {
                            $anaylseChange = true;
                            $md->h_offside = $item->HomeData;
                            $md->a_offside = $item->AwayData;
                            break;
                        }
                        case "头球": {
                            $anaylseChange = true;
                            $md->h_head = $item->HomeData;
                            $md->a_head = $item->AwayData;
                            break;
                        }
                        case "头球成功": {
                            $anaylseChange = true;
                            $md->h_head_success = $item->HomeData;
                            $md->a_head_success = $item->AwayData;
                            break;
                        }
                        case "救球": {
                            $anaylseChange = true;
                            $md->h_diving = $item->HomeData;
                            $md->a_diving = $item->AwayData;
                            break;
                        }
                        case "铲球": {
                            $anaylseChange = true;
                            $md->h_tackle = $item->HomeData;
                            $md->a_tackle = $item->AwayData;
                            break;
                        }
                        case "换人数": {
                            $anaylseChange = true;
                            $md->h_change = $item->HomeData;
                            $md->a_change = $item->AwayData;
                            break;
                        }
                        case "过人": {
                            $anaylseChange = true;
                            $md->h_to_beat = $item->HomeData;
                            $md->a_to_beat = $item->AwayData;
                            break;
                        }
                        case "界外球": {
                            $anaylseChange = true;
                            $md->h_throw = $item->HomeData;
                            $md->a_throw = $item->AwayData;
                            break;
                        }
                        case "中柱": {
                            $anaylseChange = true;
                            $md->h_hit_the_post = $item->HomeData;
                            $md->a_hit_the_post = $item->AwayData;
                            break;
                        }
                    }
                }
                if ($anaylseChange) {
                    $md->save();
                    \App\Models\LiaoGouModels\MatchData::saveDataWithWinData($md, $lg_mid);
                }
                //首发
                $ml = MatchLineup::find($id);
                if (!isset($ml)) {
                    $ml = new MatchLineup();
                    $ml->id = $id;
                }
                $ml->lid = $match->lid;
                $ml->season = $match->season;
                $ml->h_id = $match->hid;
                $ml->a_id = $match->aid;
                $ml->h_score = $match->hscore;
                $ml->a_score = $match->ascore;
                $hlineup = array();
                //主队所有人的列表,key是nameJ,value是num
                $hlineupAll = array();
                //保存球员信息
                foreach ($jm->Lineup->Home as $item) {
                    array_push($hlineup, $item->Num);
                    $player = MatchPlayer::where('lid', '=', $match->lid)
                        ->where('tid', '=', $match->hid)
                        ->where('num', '=', $item->Num)
                        ->first();
                    if (!isset($player)) {
                        $player = new MatchPlayer();
                        $player->lid = $match->lid;
                        $player->tid = $match->hid;
                        $player->num = strlen($item->Num) > 0 ? $item->Num : 0;
                    }
                    $player->name = isset($item->Name) ? $item->Name : $item->NameF;
                    if ($player->num > 0) {
                        $player->save();
                        \App\Models\LiaoGouModels\MatchPlayer::saveDataWithWinData($player, $lg_lid, $lg_hid);
                    }
                    $hlineupAll[$item->NameF] = $item->Num;
                }
                $hasChangeLineUP = false;
                if (isset($ml->h_lineup) && strcmp($ml->h_lineup,join(",", $hlineup)) != 0) {
                    $hasChangeLineUP = true;
                }
                if (is_null($ml->h_lineup) && count($hlineup) > 0){
                    $hasChangeLineUP = true;
                }
                $ml->h_lineup = join(",", $hlineup);
                //替补
                $hlineupbak = array();
                foreach ($jm->Lineup->Home_bak as $item) {
                    array_push($hlineupbak, $item->Num);
                    $player = MatchPlayer::where('lid', '=', $match->lid)
                        ->where('tid', '=', $match->hid)
                        ->where('num', '=', $item->Num)
                        ->first();
                    if (!isset($player)) {
                        $player = new MatchPlayer();
                        $player->lid = $match->lid;
                        $player->tid = $match->hid;
                        $player->num = strlen($item->Num) > 0 ? $item->Num : 0;
                    }
                    $player->name = isset($item->Name) ? $item->Name : $item->NameF;
                    if ($player->num > 0) {
                        $player->save();
                        \App\Models\LiaoGouModels\MatchPlayer::saveDataWithWinData($player, $lg_lid, $lg_hid);
                    }
                    $hlineupAll[$item->NameF] = $item->Num;
                }
                $ml->h_lineup_bak = join(",", $hlineupbak);

                //客队首发
                $alineup = array();
                //客队所有人的列表,key是nameJ,value是num
                $alineupAll = array();
                foreach ($jm->Lineup->Guest as $item) {
                    array_push($alineup, $item->Num);
                    $player = MatchPlayer::where('lid', '=', $match->lid)
                        ->where('tid', '=', $match->aid)
                        ->where('num', '=', $item->Num)
                        ->first();
                    if (!isset($player)) {
                        $player = new MatchPlayer();
                        $player->lid = $match->lid;
                        $player->tid = $match->aid;
                        $player->num = strlen($item->Num) > 0 ? $item->Num : 0;
                    }
                    $player->name = isset($item->Name) ? $item->Name : $item->NameF;
                    if ($player->num > 0) {
                        $player->save();
                        \App\Models\LiaoGouModels\MatchPlayer::saveDataWithWinData($player, $lg_lid, $lg_aid);
                    }
                    $alineupAll[$item->NameF] = $item->Num;
                }

                if (isset($ml->a_lineup) && strcmp($ml->a_lineup,join(",", $alineup)) != 0) {
                    $hasChangeLineUP = true;
                }
                if (is_null($ml->a_lineup) && count($alineup) > 0){
                    $hasChangeLineUP = true;
                }
                $ml->a_lineup = join(",", $alineup);

                //替补
                $alineupbak = array();
                foreach ($jm->Lineup->Guest_bak as $item) {
                    array_push($alineupbak, $item->Num);
                    $player = MatchPlayer::where('lid', '=', $match->lid)
                        ->where('tid', '=', $match->aid)
                        ->where('num', '=', $item->Num)
                        ->first();
                    if (!isset($player)) {
                        $player = new MatchPlayer();
                        $player->lid = $match->lid;
                        $player->tid = $match->aid;
                        $player->num = strlen($item->Num) > 0 ? $item->Num : 0;
                    }
                    $player->name = isset($item->Name) ? $item->Name : $item->NameF;
                    if ($player->num > 0) {
                        $player->save();
                        \App\Models\LiaoGouModels\MatchPlayer::saveDataWithWinData($player, $lg_lid, $lg_aid);
                    }
                    $alineupAll[$item->NameF] = $item->Num;
                }
                $ml->a_lineup_bak = join(",", $alineupbak);

                //替补上场
                //主队替换
                $hlineupReplace = array();
                //客队替换
                $alineupReplace = array();
                //如果需要重置，先清空该比赛的事件
                if($isReset) {
                    MatchEvent::where('mid', '=', $match->id)->delete();
                    if ($lg_mid > 0) {
                        $lgevents = \App\Models\LiaoGouModels\MatchEvent::where('mid', '=', $lg_mid)->get();
                        foreach ($lgevents as $event) {
                            $event->delete();
                        }
                    }

                    //事件
                    foreach ($jm->listItems as $item) {
                        //11代表替补
                        if ($item->Kind == 11) {
                            //主队
                            if ($item->isHome == 1) {
                                foreach ($hlineupAll as $name => $num) {
                                    if ($name == $item->PlayerNameF) {
                                        array_push($hlineupReplace, $num);
                                    }
                                }
                            } //客队
                            else {
                                foreach ($alineupAll as $name => $num) {
                                    if ($name == $item->PlayerNameF) {
                                        array_push($alineupReplace, $num);
                                    }
                                }
                            }
                        }
                        $me = MatchEvent::where('mid', '=', $match->id)
                            ->where('kind', '=', $item->Kind)
                            ->where('happen_time', '=', $item->happenTime)
                            ->where('is_home', '=', $item->isHome)
                            ->first();
                        if (is_null($me)) {
                            $me = new MatchEvent();
                            $me->mid = $match->id;
                            $me->kind = $item->Kind;
                            $me->is_home = $item->isHome;
                            $me->happen_time = $item->happenTime;
                            $me->player_name_j = $item->PlayerNameJ;
                            $me->player_name_j2 = $item->PlayerName2J;
                            $me->player_name_f = $item->PlayerNameF;
                            $me->player_name_f2 = $item->PlayerName2F;
                            $me->player_name_sb = $item->PlayerNameSB;
                            $me->player_name_sb2 = $item->PlayerName2SB;
                            $me->save();
                            \App\Models\LiaoGouModels\MatchEvent::saveDataWithWinData($me, $lg_mid);
                        } else {
                            $me->mid = $match->id;
                            $me->kind = $item->Kind;
                            $me->is_home = $item->isHome;
                            $me->happen_time = $item->happenTime;
                            $me->player_name_j = $item->PlayerNameJ;
                            $me->player_name_j2 = $item->PlayerName2J;
                            $me->player_name_f = $item->PlayerNameF;
                            $me->player_name_f2 = $item->PlayerName2F;
                            $me->player_name_sb = $item->PlayerNameSB;
                            $me->player_name_sb2 = $item->PlayerName2SB;
                            $me->save();
                            \App\Models\LiaoGouModels\MatchEvent::saveDataWithWinData($me, $lg_mid);
                        }
                    }
                }

                if (isset($ml->h_lineup_replace) && $ml->h_lineup_replace != join(",", $hlineupReplace)) {
                    $hasChangeLineUP = true;
                }
                if (isset($ml->a_lineup_replace) && $ml->a_lineup_replace != join(",", $alineupReplace)) {
                    $hasChangeLineUP = true;
                }
                if (is_null($ml->h_lineup_replace) && count($hlineupReplace) > 0){
                    $hasChangeLineUP = true;
                }
                if (is_null($ml->a_lineup_replace) && count($alineupReplace) > 0){
                    $hasChangeLineUP = true;
                }
                $ml->h_lineup_replace = join(",", $hlineupReplace);
                $ml->a_lineup_replace = join(",", $alineupReplace);

                if ($ml->h_lineup != '' && $ml->a_lineup != '') {
                    //表示已经爬了阵容和球员了
                    $match->has_lineup = 4;
                    $match->save();

                    if (isset($lg_match)) {
                        $lg_match->has_lineup = 4;
                        $lg_match->save();
                    }
//                    \App\Models\LiaoGouModels\Match::saveWithWinData($match);
                    $ml->save();
                    \App\Models\LiaoGouModels\MatchLineup::saveDataWithWinData($ml, $lg_mid);

                    //更新首发率
                    if ($hasChangeLineUP) {
                        if (isset($lg_match))
                            MatchAnalyseTool::updateLineupPercent($lg_match);
                    }

                    //静态化阵容数据
                    MatchDataChangeTool::saveFootballLineup($lg_mid, $jm->Lineup);
                } else {
                    //有的比赛结束了,没有阵容就没有了,当他爬完,不然会卡住不继续爬
                    if ($match->status == -1) {
                        $match->has_lineup = 4;
                        $match->save();

                        if (isset($lg_match)) {
                            $lg_match->has_lineup = 4;
                            $lg_match->save();
                        }
                    } else {
                        $match->has_lineup = 0;
                        $match->save();

                        if (isset($lg_match)) {
                            $lg_match->has_lineup = 0;
                            $lg_match->save();
                        }
                    }
                }

                //角球赔率,固定bet365
                if (property_exists($jm, "CornerOdds") && property_exists($jm->CornerOdds, "Dx_Odds")) {
                    if ($jm->CornerOdds->Dx_Odds != null) {
                        $o = Odd::where(["mid" => $id, "cid" => Odd::default_calculate_cid, "type" => 4])->first();
                        if (!isset($o)) {
                            $o = new Odd;
                            $o->mid = $id;
                            $o->cid = Odd::default_calculate_cid;
                            $o->type = 4;
                        }
                        $o->up1 = $jm->CornerOdds->Dx_Odds->Cp_Up;
                        $o->up2 = $jm->CornerOdds->Dx_Odds->Js_Up;
                        $o->middle1 = $jm->CornerOdds->Dx_Odds->Cp_Goal;
                        $o->middle2 = $jm->CornerOdds->Dx_Odds->Js_Goal;
                        $o->down1 = $jm->CornerOdds->Dx_Odds->Cp_Down;
                        $o->down2 = $jm->CornerOdds->Dx_Odds->Js_Down;
                        if (isset($o->middle1) && isset($o->middle2)) {
                            $o->save();
                            \App\Models\LiaoGouModels\Odd::saveDataWithWinData($o, $lg_match);

                            //同步到静态化文件中
                            $staticMatch = StatisticFileTool::getFileFromTerminal(MatchLive::kSportFootball, $lg_mid, 'match');
                            if (isset($staticMatch)) {
                                $staticMatch['cornerup1'] = $o->up1;
                                $staticMatch['cornerup2'] = $o->up2;
                                $staticMatch['cornermiddle1'] = $o->middle1;
                                $staticMatch['cornermiddle2'] = $o->middle2;
                                $staticMatch['cornerdown1'] = $o->down1;
                                $staticMatch['cornerdown2'] = $o->down2;
                                StatisticFileTool::putFileToTerminal($staticMatch, MatchLive::kSportFootball, $lg_mid, 'match');
                            }
                        }

                        //
                    }
                }
            }
//            echo "$str<br>";
        }
    }

    private function spiderMatchEventByDate(Request $request) {
        $date = $request->input('date');
        $count = $request->input('count', -1);
        if (isset($date)) {
            $endTime = $date;
        } else {
            $endTime = date('Y-m-d H:i');
        }

        $limitQueryStr = '';
        $startTimeQueryStr = '';
        if ($count > 0) {
            $limitQueryStr = "limit $count";
        } else {
            $startTime = date('Y-m-d H:i', strtotime('-1 day', strtotime($endTime)));
            $startTimeQueryStr = "and time >= '$startTime'";
        }

        $matches = DB::connection('liaogou_match')->select("select matches.win_id, matches.time from matches
        left join liaogou_match.leagues as l on l.win_id = matches.lid
        where time < '$endTime' and l.odd = 1 and status = -1 $startTimeQueryStr order by time desc $limitQueryStr;");

        foreach ($matches as $match) {
            $this->matchEventData($match->win_id, false, false);
        }
        $matchTime = isset($match) ? $match->time : date('Y-m-d H:i:s', strtotime('-30 min', strtotime($endTime)));
        if ($request->input('auto') && strtotime($matchTime) >= strtotime('2015-01-01')) {
            $href = $request->getPathInfo()."?date=$matchTime&auto=1&count=$count";
            echo "<script language=JavaScript> location.href='$href';</script>";
            exit;
        }
    }

    //只填充 有裁判id，可是没有统计数据的比赛统计数据
    private function spiderMatchEventByReferee(Request $request) {
        $count = $request->input('count', 10);
        $matches = DB::connection('liaogou_match')->select("
        select m.win_id from
        (select id, referee_id from match_datas
        where referee_id > 0 and h_yellow is null and a_yellow is null
        order by id desc) as md
        left join matches as m on md.id = m.id
        where m.status = -1 limit $count
        ");

        foreach ($matches as $match) {
            $this->matchEventData($match->win_id, false, false);
        }
        if ($request->input('auto') && count($matches) > 0) {
            echo "<script language=JavaScript>location.reload();</script>";
            exit;
        }
    }

    private function matchEventData($id,$isReset = false, $isAddOdd = true)
    {
        $url = "http://txt.win007.com/phone/airlive/cn/" . substr($id, 0, 1) . "/" . substr($id, 1, 2) . "/$id.htm";
        $str = $this->spiderTextFromUrl($url);
        if ($str) {
            $jm = json_decode($str);
            if ($jm) {
//                $match = Match::find($id);
//                //数据
//                if (!isset($match)) {
//                    return;
//                }
                $lg_match = \App\Models\LiaoGouModels\Match::getMatchWith($id, "win_id");
                if (!isset($lg_match)) {
//                    $lg_match = \App\Models\LiaoGouModels\Match::saveWithWinData($match);
                    return;
                }

                $lg_mid = isset($lg_match) ? $lg_match->id : 0;

                $md = MatchData::find($id);
                if (!isset($md)) {
                    $md = new MatchData();
                    $md->id = $id;
                }
                //统计是否有更新,有才保存
                $anaylseChange = false;
                foreach ($jm->listItemsTech as $item) {
                    switch ($item->Name) {
                        case "角球": {
                            $anaylseChange = true;
                            $md->h_corner = $item->HomeData;
                            $md->a_corner = $item->AwayData;
                            break;
                        }
                        case "黄牌": {
                            $anaylseChange = true;
                            $md->h_yellow = $item->HomeData;
                            $md->a_yellow = $item->AwayData;
                            break;
                        }
                        case "红牌": {
                            $anaylseChange = true;
                            $md->h_red = $item->HomeData;
                            $md->a_red = $item->AwayData;
                            break;
                        }
                        case "射门": {
                            $anaylseChange = true;
                            $md->h_shoot = $item->HomeData;
                            $md->a_shoot = $item->AwayData;
                            break;
                        }
                        case "控球率": {
                            $anaylseChange = true;
                            $md->h_control = intval($item->HomeData);
                            $md->a_control = intval($item->AwayData);
                            break;
                        }
                        case "半场角球": {
                            $anaylseChange = true;
                            $md->h_half_corner = $item->HomeData;
                            $md->a_half_corner = $item->AwayData;
                            break;
                        }
                        case "射正": {
                            $anaylseChange = true;
                            $md->h_shoot_in_target = $item->HomeData;
                            $md->a_shoot_in_target = $item->AwayData;
                            break;
                        }
                        case "射门被挡": {
                            $anaylseChange = true;
                            $md->h_shoot_block = $item->HomeData;
                            $md->a_shoot_block = $item->AwayData;
                            break;
                        }
                        case "任意球": {
                            $anaylseChange = true;
                            $md->h_free = $item->HomeData;
                            $md->a_free = $item->AwayData;
                            break;
                        }
                        case "半场控球率": {
                            $anaylseChange = true;
                            $md->h_half_control = intval($item->HomeData);
                            $md->a_half_control = intval($item->AwayData);
                            break;
                        }
                        case "传球": {
                            $anaylseChange = true;
                            $md->h_pass = $item->HomeData;
                            $md->a_pass = $item->AwayData;
                            break;
                        }
                        case "传球成功率": {
                            $anaylseChange = true;
                            $md->h_pass_percent = intval($item->HomeData);
                            $md->a_pass_percent = intval($item->AwayData);
                            break;
                        }
                        case "犯规": {
                            $anaylseChange = true;
                            $md->h_foul = $item->HomeData;
                            $md->a_foul = $item->AwayData;
                            break;
                        }
                        case "越位": {
                            $anaylseChange = true;
                            $md->h_offside = $item->HomeData;
                            $md->a_offside = $item->AwayData;
                            break;
                        }
                        case "头球": {
                            $anaylseChange = true;
                            $md->h_head = $item->HomeData;
                            $md->a_head = $item->AwayData;
                            break;
                        }
                        case "头球成功": {
                            $anaylseChange = true;
                            $md->h_head_success = $item->HomeData;
                            $md->a_head_success = $item->AwayData;
                            break;
                        }
                        case "救球": {
                            $anaylseChange = true;
                            $md->h_diving = $item->HomeData;
                            $md->a_diving = $item->AwayData;
                            break;
                        }
                        case "铲球": {
                            $anaylseChange = true;
                            $md->h_tackle = $item->HomeData;
                            $md->a_tackle = $item->AwayData;
                            break;
                        }
                        case "换人数": {
                            $anaylseChange = true;
                            $md->h_change = $item->HomeData;
                            $md->a_change = $item->AwayData;
                            break;
                        }
                        case "过人": {
                            $anaylseChange = true;
                            $md->h_to_beat = $item->HomeData;
                            $md->a_to_beat = $item->AwayData;
                            break;
                        }
                        case "界外球": {
                            $anaylseChange = true;
                            $md->h_throw = $item->HomeData;
                            $md->a_throw = $item->AwayData;
                            break;
                        }
                        case "中柱": {
                            $anaylseChange = true;
                            $md->h_hit_the_post = $item->HomeData;
                            $md->a_hit_the_post = $item->AwayData;
                            break;
                        }
                    }
                }
                if (!isset($md->h_yellow)) {
                    $md->h_yellow = 0;
                    $anaylseChange = true;
                }
                if (!isset($md->a_yellow)) {
                    $md->a_yellow = 0;
                    $anaylseChange = true;
                }
                if (!isset($md->h_red)) {
                    $md->h_red = 0;
                    $anaylseChange = true;
                }
                if (!isset($md->a_red)) {
                    $md->a_red = 0;
                    $anaylseChange = true;
                }
                if ($anaylseChange) {
                    $md->save();
                    \App\Models\LiaoGouModels\MatchData::saveDataWithWinData($md, $lg_mid);
                }

                //如果需要重置，先清空该比赛的事件
                if($isReset) {
                    MatchEvent::where('mid', '=', $id)->delete();
                    if ($lg_mid > 0) {
                        \App\Models\LiaoGouModels\MatchEvent::where('mid', '=', $lg_mid)->delete();
                    }

                    //事件
                    foreach ($jm->listItems as $item) {
                        //11代表替补
                        if ($item->Kind == 11) {

                        }
                        $me = new MatchEvent();
                        $me->mid = $id;
                        $me->kind = $item->Kind;
                        $me->is_home = $item->isHome;
                        $me->happen_time = $item->happenTime;
                        $me->player_name_j = $item->PlayerNameJ;
                        $me->player_name_j2 = $item->PlayerName2J;
                        $me->player_name_f = $item->PlayerNameF;
                        $me->player_name_f2 = $item->PlayerName2F;
                        $me->player_name_sb = $item->PlayerNameSB;
                        $me->player_name_sb2 = $item->PlayerName2SB;
                        $me->save();
                        \App\Models\LiaoGouModels\MatchEvent::saveDataWithWinData($me, $lg_mid);
                    }
                }

                //角球赔率,固定bet365
                if ($isAddOdd && property_exists($jm, "CornerOdds") && property_exists($jm->CornerOdds, "Dx_Odds")) {
                    if ($jm->CornerOdds->Dx_Odds != null) {
                        $o = Odd::where(["mid" => $id, "cid" => Odd::default_calculate_cid, "type" => 4])->first();
                        if (!isset($o)) {
                            $o = new Odd;
                            $o->mid = $id;
                            $o->cid = Odd::default_calculate_cid;
                            $o->type = 4;
                        }
                        $o->up1 = $jm->CornerOdds->Dx_Odds->Cp_Up;
                        $o->up2 = $jm->CornerOdds->Dx_Odds->Js_Up;
                        $o->middle1 = $jm->CornerOdds->Dx_Odds->Cp_Goal;
                        $o->middle2 = $jm->CornerOdds->Dx_Odds->Js_Goal;
                        $o->down1 = $jm->CornerOdds->Dx_Odds->Cp_Down;
                        $o->down2 = $jm->CornerOdds->Dx_Odds->Js_Down;
                        if (isset($o->middle1) && isset($o->middle2)) {
                            $o->save();
                            \App\Models\LiaoGouModels\Odd::saveDataWithWinData($o, $lg_match);
                        }
                    }
                }
            }
//            echo "$str<br>";
        }
    }

    /**
     * 球队详情
     * @param $tid
     * @param $mid
     */
    private function teamDetail($tid, $mid, $name, $noAlisa = false)
    {
        $team = Team::find($tid);
        if (!isset($team)) {
            $url = "http://ios.win007.com/Phone/AllInterface.aspx?bfkind=1&scheid=$mid&teamID=$tid";
            dump($url);
            $str = $this->spiderTextFromUrl($url);
            if ($str) {
                $jm = json_decode($str);
                if ($jm) {
                    $team = new Team();
                    $team->id = $tid;
                    $team->city = $jm->City;
                    $team->establish = $jm->Establish;
                    $team->gym = $jm->Gym;
                    $team->icon = $jm->TeamImageUrl;
                    $team->name = $jm->TeamName;
                    $team->save();
                }
            }
            \App\Models\LiaoGouModels\Team::saveWithWinData($team,$tid,$name);
//            echo "$str<br>";
        }
        elseif ($noAlisa){
            \App\Models\LiaoGouModels\Team::saveWithWinData($team,$tid,$name,true);
        }
    }

    /**
     * 比赛角球、进球、危险进攻时间 事件爬取
     */
    private function liveMatchTimeEvent($id) {
        $url = "http://live.titan007.com/flashdata/get?id=$id&t=".(time()*1000);

        echo "url = ".$url."</br>";

        $content = $this->spiderTextFromUrlByWin007($url, true);

        $tempStrs = explode('!', $content);
        if (count($tempStrs) >= 5) {
            $array = array();

            list($mid, $weather, $temperature, $a, $hid, $aid, $hscore, $ascore, $status, $other) = explode("^", $tempStrs[0], 10);
            $dataStrs = explode('^', $tempStrs[4]);

            //进球区间(近30场)
            $goalStr = str_replace("%", "", $tempStrs[1]);
            $goalStr = str_replace(";1", "", $goalStr);
            $goalStrs = explode('^', $goalStr);
            foreach ($goalStrs as $str) {
                $array['goal_30'][] = ['data'=>$str, 'result'=>self::convertGoalResult($str)];
            }
            //进球区间(近50场)
            $goalStr = str_replace("%", "", $tempStrs[2]);
            $goalStr = str_replace(";1", "", $goalStr);
            $goalStrs = explode('^', $goalStr);
            foreach ($goalStrs as $str) {
                $array['goal_50'][] = ['data'=>$str, 'result'=>self::convertGoalResult($str)];
            }

//            dump($dataStrs);
            $array['weather'] = $weather;
            $array['temperature'] = $temperature;
            $lg_match = \App\Models\LiaoGouModels\Match::getMatchWith($id, 'win_id');
            if (!isset($lg_match)) {
                return;
            }
            $lg_hid = $lg_match->hid;
            $lg_aid = $lg_match->aid;

            $h_danger_count = 0;
            $a_danger_count = 0;

            foreach ($dataStrs as $dataStr) {
                $dataStr = trim($dataStr);
                if (strlen($dataStr) > 0) {
                    list($index, $type, $tid, $a, $time) = explode(',', $dataStr, 5);
                    if ($tid == $hid) {
                        $lg_tid = $lg_hid;
                    } else {
                        $lg_tid = $lg_aid;
                    }
                    switch ($type) {
                        case 1:
                            $typeStr = "danger";
                            if ($tid == $hid) {
                                $h_danger_count++;
                            } else {
                                $a_danger_count++;
                            }
                            break;
                        case 2:
                            $typeStr = "corner";
                            break;
                        case 3:
                            $typeStr = "goal";
                            break;
                    }
                    if (isset($typeStr)) {
                        $array[$typeStr][] = [$lg_tid => $time];
                    }
                }
            }

            $date = date('Ymd');
            $lg_mid = $lg_match->id;

            //比赛事件
            $event = FileTool::getFileFromLiveAnalyse($date, $lg_mid);
            if (!isset($event)) {
                $event = ['timeLine'=>$array];
            } else {
                $event = json_decode($event);
                $event->timeLine = $array;
            }
            FileTool::putFileToLiveAnalyse($lg_mid, $event);

            //提点数据
            $event_analyse = StatisticFileTool::getFileFromTerminal(MatchLive::kSportFootball, $lg_mid, 'event_analyse');
            if (!isset($event_analyse)) {
                $event_analyse = ['timeLine'=>$array];
            } else {
                $event_analyse['timeLine'] = $array;
            }
            StatisticFileTool::putFileToTerminal($event_analyse, MatchLive::kSportFootball, $lg_mid, 'event_analyse');
        }
    }

    private static function convertGoalResult($str) {
        $upPercent = 50;
        $downPercent = 25;
        list($h_goal, $h_lose, $a_goal, $a_lose) = explode(",", $str);
        $resultStrs = [];
        if ($h_goal + $h_lose >= $upPercent || $a_goal + $a_lose >= $upPercent) {
            $resultStrs[] = "goal_high";
        }
        if ($h_goal + $h_lose <= $downPercent && $a_goal + $a_lose <= $downPercent) {
            $resultStrs[] = "goal_low";
        }
        if ($h_goal + $a_lose >= $upPercent) {
            $resultStrs[] = "h_goal_high";
        }
        if ($a_goal + $h_lose >= $upPercent) {
            $resultStrs[] = "a_goal_high";
        }

        return $resultStrs;
    }
}