<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 17/2/16
 * Time: 下午3:53
 */
namespace App\Http\Controllers\WinSpider;
use App\Models\WinModels\Team;
use App\Models\WinModels\League;
use App\Models\WinModels\LeagueSub;
use App\Models\WinModels\Match;
use App\Models\WinModels\Score;
use App\Models\WinModels\Season;
use App\Models\WinModels\Stage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

trait SpiderLeague
{
    /**
     * 杯赛赛程和积分
     * @param $lid 赛事ID
     * @param $season 赛季
     * @param $stage 阶段
     */
    private function cupSchedule($lid, $season = NULL, $stage = NULL)
    {
        if ($season == NULL) {
            $se = Season::where(["lid" => $lid])->orderBy('year', 'desc')->get()->first();
            if (isset($se)) {
                $season = $se->name;
            } else {
                echo "invalid season";
                return;
            }
        }
        $url = "http://ios.win007.com/phone/CupSaiCheng.aspx?ID=$lid&Season=$season" . ($stage == NULL ? "" : "&GroupId=$stage");
        $str = $this->spiderTextFromUrl($url);
        if ($str) {
            $ss = explode("$$", $str);
            if ($ss[0] == "") {
                return;
            }
            //阶段
            $currStage = NULL;
            $stages = explode("!", $ss[0]);
            foreach ($stages as $st) {
                if(count(explode("^", $st)) >= 3) {
                    list($id, $name, $status) = explode("^", $st);
                    $s = Stage::find($id);
                    if (!isset($s)) {
                        $s = new Stage();
                        $s->id = $id;
                        $s->lid = $lid;
                        $s->season = $season;
                        $s->name = $name;
                        $s->status = ($status == 'False' ? 0 : 1);
                        $s->save();
                        \App\Models\LiaoGouModels\Stage::saveDataWithWinData($s);
                    } else {
                        $s->season = $season;
                        $s->status = ($status == 'False' ? 0 : 1);
                        $s->save();
                        \App\Models\LiaoGouModels\Stage::saveDataWithWinData($s);
                    }
                    $currStage = $id;
//                echo $st . "<br>";
                }
            }
            if ($stage == NULL) {
                $stage = $currStage;
            }

            //积分
            $rankings = explode("!", $ss[1]);
            if (count($rankings) > 1) {
                $groups = "";
                $group = "";
                foreach ($rankings as $ranking) {
                    $rs = explode("^", $ranking);
                    if (count($rs) >= 14) {//积分项
                        list(
                            $rank, $tid, $status, $name_big, $name_en,
                            $name, $count, $win, $draw, $lose,
                            $goal, $fumble, $diff, $score) = $rs;
                        $s = Score::where(['lid' => $lid, 'season' => $season, 'stage' => $stage, 'tid' => $tid])->first();
                        if (!isset($s)) {
                            $s = new Score();
                            $s->lid = $lid;
                            $s->season = $season;
                            $s->stage = $stage;
                            $s->tid = $tid;
                        }
                        $s->group = $group;
                        $s->score = $score;
                        $s->goal = $goal;
                        $s->fumble = $fumble;
                        $s->status = strlen($status) > 1 ? 0 : -1;
                        if ($s->status == 0) {
                            $s->name = "晋级下一轮";
                        } else {
                            $s->name = "";
                        }
                        $s->count = $count;
                        $s->win = $win;
                        $s->draw = $draw;
                        $s->lose = $lose;
                        $s->save();
                        \App\Models\LiaoGouModels\Score::saveCupDataWithWinData($s);
                    } else {//组名
                        $group = substr($rs[0], 0, 1);
                        $groups .= $group;
                    }
//                    echo $ranking . "<br>";
                }
                if (strlen($groups) > 1) {
                    $s = Stage::find($stage);
                    $s->group = $groups;
                    $s->save();
                    \App\Models\LiaoGouModels\Stage::saveDataWithWinData($s);
                }
            }

            //赛程
            $schedules = explode("!", $ss[2]);
            if (count($schedules) > 0) {
                foreach ($schedules as $schedule) {
                    $sches = explode("^", $schedule);
                    if (count($sches) >= 15) {
                        list(
                            $group, $dateStr, $hname, $hname_big, $aname,
                            $aname_big, $status, $hscore, $ascore, $hscore_half,
                            $ascore_half, $id, $var1, $hrank, $arank) = $sches;
                        $date = date('Y-m-d H:i:s', strtotime($dateStr));
                        $m = Match::find($id);
                        if (!isset($m)) {
                            $m = new Match();
                            $m->id = $id;
                            $m->is_odd = 0;
                        }
                        $m->lid = $lid;
                        $m->season = $season;
                        $m->stage = $stage;
                        $m->group = $group;
                        $m->time = $date;
//                        $m->status = $status;
                        $m->setStatus($status);
                        $m->hname = $hname;
                        $m->aname = $aname;
                        $m->hscore = $hscore;
                        $m->ascore = $ascore;
                        $m->hscorehalf = $hscore_half == "" ? "0" : $hscore_half;
                        $m->ascorehalf = $ascore_half == "" ? "0" : $ascore_half;
                        $m->hrank = $hrank == "" ? "0" : $hrank;
                        $m->arank = $arank == "" ? "0" : $arank;
                        $m->save();
//                        echo "$schedule<br>";
//                        dump($m);
                        \App\Models\LiaoGouModels\Match::saveWithWinData($m);
                    }
                }
            }
        }
    }

    /**
     * 联赛赛程
     * @param $lid 赛事ID
     * @param $season 赛季
     * @param null $round
     */
    private function leagueSchedule($lid, $season = NULL, $round = NULL, $subid = NULL)
    {
        if ($season == NULL) {
            $se = Season::where(["lid" => $lid])->orderBy('year', 'desc')->get()->first();
            if (isset($se)) {
                $season = $se->name;
            } else {
                echo "invalid season";
                return;
            }
        }
        $url = "http://ios.win007.com/phone/SaiCheng2.aspx?sclassid=$lid&season=$season" . ($round == NULL ? "" : "&round=$round") . ($subid == NULL ? "" : "&subid=$subid");
        $str = $this->spiderTextFromUrl($url);
        if ($str) {
            $ss = explode("$$", $str);

            //轮次处理
            if (count(explode("^", $ss[0])) == 2) {//普通联赛
                if ($round == NULL) {
                    if(count(explode("^", $ss[0])) >= 2) {
                        list($total_round, $round) = explode("^", $ss[0]);
                        $s = Season::where(['lid' => $lid, 'name' => $season])->first();
                        if (isset($s)) {
                            if ($total_round > $s->total_round) {
                                $s->total_round = $total_round;
                            }
                            $s->curr_round = $round;
                            $s->save();
                            \App\Models\LiaoGouModels\Season::saveDataWithRound($s);
                        }
                    }
                }
            } else {//子联赛
                $subs = explode("!", $ss[0]);
                foreach ($subs as $sub) {
                    if(count(explode("^", $sub)) >= 7) {
                        list(
                            $lsid, $subname, $subnamebig, $subtype, $subtotal,
                            $subcurr, $substatus) = explode("^", $sub);
                        $ls = LeagueSub::where(['lid' => $lid, 'subid' => $lsid, 'season' => $season])->first();
                        if (!isset($ls)) {
                            $ls = new LeagueSub();
                            $ls->lid = $lid;
                            $ls->subid = $lsid;
                            $ls->season = $season;
                        }
                        $ls->name = $subname;
                        $ls->type = $subtype;
                        $ls->total_round = $subtotal;
                        $ls->curr_round = $subcurr;
                        $ls->status = $substatus;
                        $ls->save();
                        \App\Models\LiaoGouModels\LeagueSub::saveDataWithWinData($ls);
                        if ($substatus == 1 && $round == NULL && $subcurr != 0) {
                            $round = $subcurr;
                            if ($subid == NULL && $lsid != 0) {
                                $subid = $lsid;
                            }
                        }
                    }
                }
                $league = League::find($lid);
                if (isset($league) && $league->sub != 1) {
                    $league->sub = 1;
                    $league->save();
                    \App\Models\LiaoGouModels\League::saveDataWithWinData($league);
                }
            }
//            echo $ss[0] . "<br>";

            //赛程
            if ($ss[1] != "") {
                $schedules = explode("!", $ss[1]);
                foreach ($schedules as $schedule) {
                    if(count(explode("^", $schedule)) >= 13) {
                        list(
                            $id, $dateStr, $hname, $hname_big, $aname,
                            $aname_big, $status, $hscore, $ascore, $hscore_half,
                            $ascore_half, $hrank, $arank) = explode("^", $schedule);
                        $date = date('Y-m-d H:i:s', strtotime($dateStr));
                        $m = Match::find($id);
                        if (!isset($m)) {
                            $m = new Match();
                            $m->id = $id;
                            $m->is_odd = 0;
                        }
                        $m->lid = $lid;
                        if ($subid) {
                            $m->lsid = $subid;
                        }
                        if ($season) {
                            $m->season = $season;
                        }
                        if ($round) {
                            $m->round = $round;
                        }
                        $m->time = $date;
//                    $m->status = $status;
                        $m->setStatus($status);
                        $m->hname = $hname;
                        $m->aname = $aname;
                        $m->hscore = $hscore;
                        $m->ascore = $ascore;
                        $m->hscorehalf = $hscore_half == "" ? "0" : mb_convert_kana($hscore_half, 'n');
                        $m->ascorehalf = $ascore_half == "" ? "0" : mb_convert_kana($ascore_half, 'n');
                        $m->hrank = $hrank == "" ? "0" : mb_convert_kana($hrank, 'n');
                        $m->arank = $arank == "" ? "0" : mb_convert_kana($arank, 'n');
                        $m->neutral = 0;
                        $m->save();
                        \App\Models\LiaoGouModels\Match::saveWithWinData($m);
//                echo "$schedule<br>";
                    }
                }
            }
        }
    }

    private function leagueRanking($type, $lid, $season = NULL, $subid = NULL) {
        switch ($type) {
            case 0://全部
                $this->leagueAllRanking($lid, $season, $subid);
                break;
            case 1://主场
                $this->leagueHomeRanking($lid, $season, $subid);
                break;
            case 2://客场
                $this->leagueAwayRanking($lid, $season, $subid);
                break;
        }
    }

    /**
     * 联赛积分
     * @param $lid string 赛事ID
     * @param $season 赛季
     */
    private function leagueAllRanking($lid, $season = NULL, $subid = NULL)
    {
        if ($season == NULL) {
            $se = Season::where(["lid" => $lid])->orderBy('year', 'desc')->get()->first();
            if (isset($se)) {
                $season = $se->name;
            } else {
                echo "invalid season";
                return;
            }
        }
        dump($subid);
        $url = "http://txt.win007.com/phone/Jifen2.aspx?sclassid=$lid&season=$season" . ($subid == NULL ? "" : "&subid=$subid");
        $str = $this->spiderTextFromUrl($url);
        if ($str) {
            $ss = explode("$$", $str);
            //存在子赛事
            if ($subid == NULL && strlen($ss[0]) > 0) {
                $lss = explode("!", $ss[0]);
                foreach ($lss as $ls) {
                    if(count(explode("^", $ls)) >= 4) {
                        list($lsid, $subname, $subnamebig, $substatus) = explode("^", $ls);
                        if ($substatus == 1) {
                            $subid = $lsid;
                            break;
                        }
//                    echo $ls . "<br>";
                    }
                }
            }

            //下赛季状态列表
            $tsa = array();
            if ($ss[1] != "") {
                $tss = explode("!", $ss[1]);
                foreach ($tss as $ts) {
                    if(count(explode("^", $ts)) >= 2) {
                        list($var1, $name) = explode("^", $ts);
                        array_push($tsa, $name);
//                    echo $ts . "<br>";
                    }
                }
            }

            //积分
            if ($ss[2] != '') {
                $scores = explode("!", $ss[2]);
                $lid = \App\Models\LiaoGouModels\League::getLeagueIdWithType($lid,'win_id');
                foreach ($scores as $sc) {
                    if(count(explode("^", $sc)) >= 13) {
                        list(
                            $rank, $tid, $name, $namebig, $count,
                            $win, $draw, $lose, $goal, $fumble,
                            $score, $var1, $status) = explode("^", $sc);
                        $s = Score::where(['lid' => $lid, 'season' => $season, 'lsid' => $subid, 'tid' => $tid])
                            ->whereNull('kind')->first();
                        if (!isset($s)) {
                            $s = new Score;
                            $s->lid = $lid;
                            $s->season = $season;
                            $s->lsid = $subid;
                            $s->tid = $tid;
                        }
                        $this->spiderTeamDetailByHtml($tid);
                        $s->score = $score;
                        $s->goal = $goal;
                        $s->fumble = $fumble;
                        $s->status = $status;
                        if ($status >= 0 && count($tsa) > $status) {
                            $s->name = $tsa[$status];
                        } else {
                            $s->name = "";
                        }
                        $s->count = $count;
                        $s->win = $win;
                        $s->draw = $draw;
                        $s->lose = $lose;
                        $s->rank = $rank;
                        $s->save();
                        \App\Models\LiaoGouModels\Score::saveLeagueDataWithWinData($s, $lid);
//                echo $sc . "<br>";
                    }
                }
            }
        }
    }


    /**
     * 联赛主场积分
     * @param $lid string 赛事ID
     * @param $season 赛季
     */
    private function leagueHomeRanking($lid, $season = NULL, $subid = NULL)
    {
        if ($season == NULL) {
            $se = Season::where(["lid" => $lid])->orderBy('year', 'desc')->get()->first();
            if (isset($se)) {
                $season = $se->name;
            } else {
                echo "invalid season";
                return;
            }
        }
        dump($subid);

        //主场积分
        $url = "http://txt.win007.com/phone/Jifen2.aspx?sclassid=$lid&season=$season&pointsKind=2&subVersion=2" . ($subid == NULL ? "" : "&subid=$subid");
        $str = $this->spiderTextFromUrl($url);
        if ($str) {
            $ss = explode("$$", $str);
            //存在子赛事
            if ($subid == NULL && strlen($ss[0]) > 0) {
                $lss = explode("!", $ss[0]);
                foreach ($lss as $ls) {
                    if(count(explode("^", $ls)) >= 4) {
                        list($lsid, $subname, $subnamebig, $substatus) = explode("^", $ls);
                        if ($substatus == 1) {
                            $subid = $lsid;
                            break;
                        }
//                    echo $ls . "<br>";
                    }
                }
            }

            //下赛季状态列表
            $tsa = array();
            if ($ss[1] != "") {
                $tss = explode("!", $ss[1]);
                foreach ($tss as $ts) {
                    if(count(explode("^", $ts)) >= 2) {
                        list($var1, $name) = explode("^", $ts);
                        array_push($tsa, $name);
//                    echo $ts . "<br>";
                    }
                }
            }

            //积分
            if ($ss[2] != '') {
                $scores = explode("!", $ss[2]);
                $lid = \App\Models\LiaoGouModels\League::getLeagueIdWithType($lid,'win_id');
                foreach ($scores as $sc) {
                    if(count(explode("^", $sc)) >= 13) {
                        list(
                            $rank, $tid, $name, $namebig, $count,
                            $win, $draw, $lose, $goal, $fumble,
                            $score, $var1, $status) = explode("^", $sc);
                        $s = Score::where(['lid' => $lid, 'season' => $season, 'lsid' => $subid, 'tid' => $tid, 'kind' => 1])->first();
                        if (!isset($s)) {
                            $s = new Score();
                            $s->lid = $lid;
                            $s->season = $season;
                            $s->lsid = $subid;
                            $s->tid = $tid;
                        }
                        $s->score = $score;
                        $s->goal = $goal;
                        $s->fumble = $fumble;
                        $s->status = $status;
                        if ($status >= 0 && count($tsa) > $status) {
                            $s->name = $tsa[$status];
                        } else {
                            $s->name = "";
                        }
                        $s->count = $count;
                        $s->win = $win;
                        $s->draw = $draw;
                        $s->lose = $lose;
                        $s->rank = $rank;
                        $s->kind = 1;
                        $s->save();
                        \App\Models\LiaoGouModels\Score::saveLeagueDataWithWinData($s, $lid);
//                echo $sc . "<br>";
                    }
                }
            }
        }
    }


    /**
     * 联赛客场积分
     * @param $lid string 赛事ID
     * @param $season 赛季
     */
    private function leagueAwayRanking($lid, $season = NULL, $subid = NULL)
    {
        if ($season == NULL) {
            $se = Season::where(["lid" => $lid])->orderBy('year', 'desc')->get()->first();
            if (isset($se)) {
                $season = $se->name;
            } else {
                echo "invalid season";
                return;
            }
        }
        dump($subid);
        //客场积分
        $url = "http://txt.win007.com/phone/Jifen2.aspx?sclassid=$lid&season=$season&pointsKind=3&subVersion=2" . ($subid == NULL ? "" : "&subid=$subid");
        $str = $this->spiderTextFromUrl($url);
        if ($str) {
            $ss = explode("$$", $str);
            //存在子赛事
            if ($subid == NULL && strlen($ss[0]) > 0) {
                $lss = explode("!", $ss[0]);
                foreach ($lss as $ls) {
                    if(count(explode("^", $ls)) >= 4) {
                        list($lsid, $subname, $subnamebig, $substatus) = explode("^", $ls);
                        if ($substatus == 1) {
                            $subid = $lsid;
                            break;
                        }
//                    echo $ls . "<br>";
                    }
                }
            }

            //下赛季状态列表
            $tsa = array();
            if ($ss[1] != "") {
                $tss = explode("!", $ss[1]);
                foreach ($tss as $ts) {
                    if(count(explode("^", $ts)) >= 2) {
                        list($var1, $name) = explode("^", $ts);
                        array_push($tsa, $name);
//                    echo $ts . "<br>";
                    }
                }
            }

            //积分
            if ($ss[2] != '') {
                $scores = explode("!", $ss[2]);
                $lid = \App\Models\LiaoGouModels\League::getLeagueIdWithType($lid,'win_id');
                foreach ($scores as $sc) {
                    if(count(explode("^", $sc)) >= 13) {
                        list(
                            $rank, $tid, $name, $namebig, $count,
                            $win, $draw, $lose, $goal, $fumble,
                            $score, $var1, $status) = explode("^", $sc);
                        $s = Score::where(['lid' => $lid, 'season' => $season, 'lsid' => $subid, 'tid' => $tid, 'kind' => 2])->first();
                        if (!isset($s)) {
                            $s = new Score();
                            $s->lid = $lid;
                            $s->season = $season;
                            $s->lsid = $subid;
                            $s->tid = $tid;
                        }
                        $s->score = $score;
                        $s->goal = $goal;
                        $s->fumble = $fumble;
                        $s->status = $status;
                        if ($status >= 0 && count($tsa) > $status) {
                            $s->name = $tsa[$status];
                        } else {
                            $s->name = "";
                        }
                        $s->count = $count;
                        $s->win = $win;
                        $s->draw = $draw;
                        $s->lose = $lose;
                        $s->rank = $rank;
                        $s->kind = 2;
                        $s->save();
                        \App\Models\LiaoGouModels\Score::saveLeagueDataWithWinData($s, $lid);
//                echo $sc . "<br>";
                    }
                }
            }
        }
    }

    /**
     * 强制爬一次赛事赛季比赛列表
     * @param $lid
     * @param null $seasonString
     */
    private function leagueRefreshById($lid, $seasonString = null){
        $league = League::where(["id" => $lid])->first();
        if (!isset($league)) return;

        echo $league->id . ":";
        if (isset($seasonString)){
            $season = Season::where(["lid" => $league->id,"name"=>$seasonString])->first();
        }
        else{
            $season = Season::where(["lid" => $league->id])->orderBy('year', 'desc')->orderBy('name', 'desc')->first();
        }

        if (is_null($season)){
            echo 'season is null';
            return;
        }

        echo $season->name . "<br>";
        if ($league->sub == 1) {
            $subs = LeagueSub::where(["lid" => $league->id, "season" => $season->name])->get();
        }

        if ($league->type == 1) {
            if ($league->sub == 1 && count($subs) > 0) {
                foreach ($subs as $sub) {
                    if ($sub->total_round > 0) {
                        foreach (range(1, $sub->total_round) as $round) {
                            $this->leagueSchedule($season->lid, $season->name, $round, $sub->subid);
                        }
                    } else {
                        $this->leagueSchedule($season->lid, $season->name, NULL, $sub->subid);
                    }
                    $this->leagueAllRanking($season->lid, $season->name, $sub->subid);
                    $this->leagueHomeRanking($season->lid, $season->name, $sub->subid);
                    $this->leagueAwayRanking($season->lid, $season->name, $sub->subid);
                }
            } else {
                if ($season->total_round > 0) {
                    foreach (range(1, $season->total_round) as $round) {
                        $this->leagueSchedule($season->lid, $season->name, $round);
                    }
                } else {
                    $this->leagueSchedule($season->lid, $season->name);
                }
                $this->leagueAllRanking($season->lid, $season->name);
                $this->leagueHomeRanking($season->lid, $season->name);
                $this->leagueAwayRanking($season->lid, $season->name);
            }
        } elseif ($league->type == 2) {
            $this->cupSchedule($lid, $season->name);
        }
        echo "<br>";
    }

    /**
     * 重新填充season表中的start数据
     */
    private function spiderSeasonStart(Request $request) {

        $lid = $request->input('lid');
        if (isset($lid)) {
            $this->saveSeasonStartByLid($lid);
            return;
        }

        $key =  "league_season_start";
        $value = Redis::get($key);
        $redisLids = array();
        if (isset($value)) {
            $redisLids = array_merge($redisLids, json_decode($value));
        }

        $default_count = 5;
        if (count($redisLids) <= 0) {
            $leagues = League::query()->select('id')->where('type', 1)->orderBy('id', 'asc')->get()->toArray();
            $lids = collect($leagues)->flatten(1)->all();
            Redis::setEx($key, 24*60*60, json_encode($lids));
            $redisLids = array_merge($redisLids, $lids);
        }
        $lids = array_slice($redisLids, 0, $default_count);
        dump($lids);
        foreach ($lids as $lid) {
            $this->saveSeasonStartByLid($lid);
        }

        //删除redis中将要爬取的lid
        $redisLids = array_slice($redisLids, count($lids));
        $count = count($redisLids);
        Redis::set($key, json_encode($redisLids));

        if ($count > 0 && $request->input('auto')) {
            echo "<script language=JavaScript> location.replace(location.href);</script>";
            exit;
        }
    }

    private function saveSeasonStartByLid($lid) {
        $seasons = Season::query()->where('lid', $lid)->orderBy('year')->get();
        foreach ($seasons as $season) {
            $seasonName = $season->name;
            $subLeagues = LeagueSub::query()->where('lid', $lid)->where('season', $seasonName)->where('type', 1)->orderBy('subid')->get();
            if (isset($subLeagues) && count($subLeagues) > 0) {
                foreach ($subLeagues as $subLeague) {
                    $this->saveSeasonStart($lid, $seasonName, $subLeague->subid);
                }
            } else {
                $this->saveSeasonStart($lid, $seasonName);
            }
        }
    }

    private function saveSeasonStart($lid, $seasonName, $subid = NULL) {
        $url = "http://ios.win007.com/phone/SaiCheng2.aspx?sclassid=$lid&season=$seasonName&round=1". ($subid == NULL ? "" : "&subid=$subid");
        try {
            $str = $this->spiderTextFromUrl($url);
            if ($str) {
                $ss = explode("$$", $str);
                //赛程
                if ($ss[1] != "") {
                    $schedules = explode("!", $ss[1]);
                    $schedule = $schedules[0];
                    if (count(explode("^", $schedule)) >= 13) {
                        list(
                            $id, $dateStr, $hname, $hname_big, $aname,
                            $aname_big, $status, $hscore, $ascore, $hscore_half,
                            $ascore_half, $hrank, $arank) = explode("^", $schedule);
                        $date = date('Y-m-d', strtotime($dateStr));
                        $season = Season::query()->where(['lid' => $lid, 'name' => $seasonName])->first();
                        if (is_null($season->start) || strtotime($season->start) > strtotime($date)) {
                            $season->start = $date;
                            $season->save();

                            \App\Models\LiaoGouModels\Season::saveDataWithStartTime($season);

                            echo "save lid = $lid, season = $seasonName, start = $date complete <br>";
                        }
                    }
                }
            }
        } catch (\Exception $e) {

        }
    }

    private function spiderTeamDetailByHtml($tid, $isReset = false) {
        $team = Team::query()->find($tid);
        if (isset($team) && !$isReset) {
            return;
        }
        $url = "http://zq.win007.com/jsData/teamInfo/teamDetail/tdl$tid.js?version=".date('YmdH');
        $data = $this->spiderTextFromUrlByWin007($url,true);
        if (isset($data) && strlen($data) > 0) {
            $strs = explode(";\n",$data);
            if (count($strs) > 0) {
                $teamDetail = str_replace("var teamDetail = [", "", $strs[0]);
                $teamDetail = str_replace("]", "", $teamDetail);
                if (count(explode("','", $teamDetail)) >= 14) {
                    list($name, $name_big, $name_en, $icon, $city,
                        $city_big, $city_en, $gym, $gym_big, $gym_en,
                        $gid, $establish, $official_web, $describe) = explode("','", $teamDetail);

                    if (!isset($team)) {
                        $team = new Team();
                        $team->id = $tid;
                        $team->name = str_replace("$tid,'", '', $name);
                    }
                    $team->name_big = $name_big;
                    if (strlen($icon) > 0) {
                        $team->icon = "http://zq.win007.com/image/team/".$icon;
                    }
                    $team->city = $city;
                    $team->establish = $establish;
                    $team->gym = $gym;
                    $team->describe = $describe;
                    $team->save();

                    \App\Models\LiaoGouModels\Team::saveWithWinData($team,$tid,$team->name);
                }
            }
        }
    }
}