<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 17/2/16
 * Time: 下午3:43
 */
namespace App\Http\Controllers\WinSpider;

use App\Http\Controllers\Statistic\Change\ScoreChangeController;
use App\Http\Controllers\Statistic\StatisticFileTool;
use App\Models\LiaoGouModels\MatchesAfter;
use App\Models\LiaoGouModels\MatchLive;
use App\Models\WinModels\League;
use App\Models\WinModels\Match;
use App\Models\WinModels\MatchData;
use Illuminate\Support\Facades\Redis;

trait SpiderSchedule
{
    private function matchLiveScheduleForBetting($type){
        $url = "http://txt.win007.com/phone/schedule_0_$type.txt";
        $str = $this->spiderTextFromUrl($url);
        if ($str) {
            if ($type == 2 || $type == 4 || $type == 3){
                $str = substr($str,16);
            }
            $ss = explode("$$", $str);
            //比赛
            $matches = explode("!", $ss[count($ss) == 2 ? 1 : 0]);
            foreach ($matches as $match) {
                list($id) = explode("^", $match);
                dump($id);
                $m = Match::find($id);
                if (isset($m)) {
                    dump($m);
                    $m->genre = $m->genre | 1 << $type;
                    $m->save();
                    \App\Models\LiaoGouModels\Match::saveWithWinData($m);
                }
            }
        }
    }

    /**
     * 爬当前比赛赛程
     * @param int $type 类型0全部 1一级 2足彩 3竞彩 4北单
     * @param bool $spiderReferee 是否需要爬裁判
     */
    private function matchLiveSchedule($type = 0,$spiderReferee = true)
    {
        $url = "http://txt.win007.com/phone/schedule_0_$type.txt";
        $str = $this->spiderTextFromUrl($url);
        if ($str) {
            $ss = explode("$$", $str);
            //赛事
            $leagues = explode("!", $ss[count($ss) == 3 ? 1 : 0]);
            foreach ($leagues as $league) {
                if(count(explode("^", $league)) >= 3) {
                    list($name, $lid, $hot) = explode("^", $league);
                    $l = League::find($lid);
                    if (!isset($l)) {
                        $l = new League();
                        $l->id = $lid;
                        $l->name = $name;
                        $l->hot = $hot;
                        $l->save();
                    }
                    if ($l->hot != $hot) {
                        $l->hot = $hot;
                        $l->save();
                    }
                    \App\Models\LiaoGouModels\League::saveDataWithWinData($l);
                }
//                echo "$league<br>";
            }
            //比赛
            $matches = explode("!", $ss[count($ss) == 3 ? 2 : 1]);
            foreach ($matches as $match) {
                if (count(explode('^',$ss[0])) >= 27) {
                    list($id, $lid, $status, $dateStr, $dateHalfStr,
                        $hname, $aname, $hscore, $ascore, $hscorehalf,
                        $ascorehalf, $hred, $ared, $hyellow, $ayellow,
                        $asian, $var1, $var2, $lineup, $hrank,
                        $arank, $var4, $var5, $var6, $var7,
                        $hcorner, $acorner) = explode("^", $match);

                    if (is_null($asian) || strlen($asian) <= 0) continue;

                    $m = Match::find($id);
                    if (!isset($m)) {
                        $m = new Match();
                        $m->id = $id;
                        $m->is_odd = 0;
                    }
                    $m->lid = $lid;
                    $date = date('Y-m-d H:i:s',strtotime($dateStr));
                    $m->time = $date;
                    if ($dateHalfStr != "") {
                        $datehalf = date('Y-m-d H:i:s',strtotime($dateHalfStr));
                        $m->timehalf = $datehalf;
                    }

//                $m->status = $status;
                    $m->setStatus($status);
                    $m->hname = $hname;
                    $m->aname = $aname;
                    $m->hrank = $hrank == "" ? 0 : $hrank;
                    $m->arank = $arank == "" ? 0 : $arank;
                    $m->hscore = $hscore;
                    $m->ascore = $ascore;
                    $m->hscorehalf = $hscorehalf == "" ? 0 : $hscorehalf;
                    $m->ascorehalf = $ascorehalf == "" ? 0 : $ascorehalf;
                    $m->neutral = str_contains($hname, "(中)") ? 1 : 0;
                    $m->genre = $m->genre | 1 << $type;
                    if ($m->has_lineup > 1) {

                    } else {
                        $m->has_lineup = $lineup;
                    }
                    $m->save();
                    $lgm = \App\Models\LiaoGouModels\Match::saveWithWinData($m);

                    if ($status > 0 || $status == -1) {
                        //数据
                        $md = MatchData::find($id);
                        if (!isset($md)) {
                            $md = new MatchData();
                            $md->id = $id;
                        }
                        $md->h_corner = $hcorner;
                        $md->a_corner = $acorner;
                        $md->h_yellow = $hyellow;
                        $md->a_yellow = $ayellow;
                        $md->h_red = $hred;
                        $md->a_red = $ared;
                        $md->save();
                        \App\Models\LiaoGouModels\MatchData::saveDataWithWinData($md, $lgm->id);
                    }
//                echo "$match<br>";
                }
            }
        }
    }

    /**
     * 根据日期爬赛程
     * @param null $date
     */
    private function matchDateSchedule($date = NULL)
    {
        if ($date == NULL) {
            $date = date("Y-m-d");
        }
        $url = "http://txt.win007.com/phone/scheduleByDate.aspx?date=$date";
        $str = $this->spiderTextFromUrl($url);
        if ($str) {
            $ss = explode("$$", $str);
            if (count($ss) == 3) {
                //赛事
                $leagues = explode("!", $ss[1]);
                foreach ($leagues as $league) {
                    if(count(explode("^", $league)) >= 3) {
                        list($name, $lid, $hot) = explode("^", $league);
                        $l = League::find($lid);
                        if (!isset($l)) {
                            $l = new League;
                            $l->id = $lid;
                            $l->name = $name;
                            $l->hot = $hot;
                            $l->save();
                        }
                        if ($l->hot != $hot) {
                            $l->hot = $hot;
                            $l->save();
                        }
                        \App\Models\LiaoGouModels\League::saveDataWithWinData($l);
                    }
//                    echo "$league<br>";
                }
                //比赛
                $matches = explode("!", $ss[2]);
                foreach ($matches as $match) {
                    if(count(explode("^", $match)) >= 15) {
                        list(
                            $id, $lid, $status, $dateStr, $dateHalfStr,
                            $hname, $aname, $hscore, $ascore, $hscorehalf,
                            $ascorehalf, $hred, $ared, $hyellow, $ayellow) = explode("^", $match);
                        $m = Match::find($id);
                        if (!isset($m)) {
                            $m = new Match();
                            $m->id = $id;
                            $m->is_odd = 0;
                        }
                        $m->lid = $lid;
                        $date = date('Y-m-d H:i:s',strtotime($dateStr));
                        $m->time = $date;
                        if ($dateHalfStr != "") {
                            $datehalf = date('Y-m-d H:i:s',strtotime($dateHalfStr));
                            $m->timehalf = $datehalf;
                        }

//                    $m->status = $status;
                        $m->setStatus($status);
                        $m->hname = $hname;
                        $m->aname = $aname;
                        $m->hscore = $hscore;
                        $m->ascore = $ascore;
                        $m->hscorehalf = $hscorehalf == "" ? 0 : $hscorehalf;
                        $m->ascorehalf = $ascorehalf == "" ? 0 : $ascorehalf;
                        $m->neutral = str_contains($hname, "(中)") ? 1 : 0;
                        $l = League::find($lid);
                        if (isset($l) && 1 == $l->hot) {
                            if (is_null($m->genre))
                                $m->genre = 3;
                        } else {
                            if (is_null($m->genre))
                                $m->genre = 1;
                        }
                        $m->save();
                        $lgm = \App\Models\LiaoGouModels\Match::saveWithWinData($m);

                        if ($status > 0 || $status == -1) {
                            //数据
                            $md = MatchData::find($id);
                            if (!isset($md)) {
                                $md = new MatchData();
                                $md->id = $id;
                            }
                            $md->h_yellow = $hyellow;
                            $md->a_yellow = $ayellow;
                            $md->h_red = $hred;
                            $md->a_red = $ared;
                            $md->save();
                            \App\Models\LiaoGouModels\MatchData::saveDataWithWinData($md, $lgm->id);
                        }
//                    echo "$match<br>";
                    }
                }
            }
        }
    }

    /**
     * 即时更新比赛数据
     */
    private function matchLiveChange()
    {
        $lastTime = time();
        $key = "matchLiveChange_foot";
        $url = "http://txt.win007.com/phone/livechange.txt";
        $str = $this->spiderTextFromUrl($url);
        $length = Redis::get($key);
        if ($str) {
            if (strlen($str) == $length) {
                return;
            } else {
                Redis::set($key, strlen($str));
            }

            $ss = explode("!", $str);
            if (count($ss) > 0) {
                $tempArray = StatisticFileTool::getFileFromChange(MatchLive::kSportFootball, 'score');
                if (!is_array($tempArray)) $tempArray = array();

                $win_ids = array();
                $win_matches = array();
                foreach ($ss as $s) {
                    if(count(explode("^", $s)) >= 16) {
                        $lgm = null;
                        list(
                            $id, $status, $dateStr, $dateHalfStr, $hscore,
                            $ascore, $hscorehalf, $ascorehalf, $hred, $ared,
                            $hyellow, $ayellow, $asian, $var1, $hcorner,
                            $acorner) = explode("^", $s);
                        if ($status == 0) {
                            continue;
                        }
                        $win_ids[] = $id;
                        $win_matches[$id] = ['status'=>$status,'dateStr'=>$dateStr,'dateHalfStr'=>$dateHalfStr,'hscore'=>$hscore,'ascore'=>$ascore,
                        'hscorehalf'=>$hscorehalf,'ascorehalf'=>$ascorehalf,'hred'=>$hred,'ared'=>$ared,'hyellow'=>$hyellow,'ayellow'=>$ayellow,
                        'hcorner'=>$hcorner,'acorner'=>$acorner,'data'=>$s];

                        echo "$s<br>";
                    }
                }
                $lg_matches = \App\Models\LiaoGouModels\Match::query()->whereIn('win_id', $win_ids)->get();
                $match_afters = \App\Models\LiaoGouModels\MatchesAfter::query()->whereIn('win_id', $win_ids)->get();
                $tempMatchAfters = array();
                echo "win_ids count = ".count($win_ids)."; lg_matches count = ".count($lg_matches)."; lg_match_afters count = ".count($match_afters)."<br>";
                foreach ($match_afters as $match_after) {
                    $tempMatchAfters[$match_after->id] = $match_after;
                }
                foreach ($lg_matches as $m) {
                    if (array_key_exists($m->win_id, $win_matches)) {
                        $win_match = $win_matches[$m->win_id];
                        $date = date('Y-m-d H:i:s',strtotime($win_match['dateStr']));
                        $m->time = $date;
                        if ($win_match['dateHalfStr'] != "") {
                            $datehalf = date('Y-m-d H:i:s',strtotime($win_match['dateHalfStr']));
                            $m->timehalf = $datehalf;
                        }
                        $m->status = $win_match['status'];
//                        $m->setStatus($win_match['status']);
                        $m->hscore = $win_match['hscore'];
                        $m->ascore = $win_match['ascore'];
                        $m->hscorehalf = $win_match['hscorehalf'] == "" ? 0 : $win_match['hscorehalf'];
                        $m->ascorehalf = $win_match['ascorehalf'] == "" ? 0 : $win_match['ascorehalf'];
                        $m->save();

                        $lg_mid = $m->id;

                        //同时保存到冗余表
                        if (array_key_exists($lg_mid, $tempMatchAfters)) {
                            $mf = $tempMatchAfters[$lg_mid];
                            $mf->time = $m->time;
                            $mf->timehalf = $m->timehalf;
                            $mf->status = $m->status;
                            $mf->hscore = $m->hscore;
                            $mf->ascore = $m->ascore;
                            $mf->hscorehalf = $m->hscorehalf;
                            $mf->ascorehalf = $m->ascorehalf;
                            $mf->save();
                        }


                        //数据
//                        $md = \App\Models\LiaoGouModels\MatchData::query()->find($lg_mid);
//                        if (!isset($md)) {
//                            $md = new MatchData();
//                            $md->id = $lg_mid;
//                        }
//                        $md->h_corner = $win_match['hcorner'];
//                        $md->a_corner = $win_match['acorner'];
//                        $md->h_yellow = $win_match['hyellow'];
//                        $md->a_yellow = $win_match['ayellow'];
//                        $md->h_red = $win_match['hred'];
//                        $md->a_red = $win_match['ared'];
//                        $md->save();

                        //静态数据
                        $staticItem = ScoreChangeController::footballLiveItemChange($win_match['status'], $m->time, $m->timehalf, $win_match['hscore'],
                            $win_match['ascore'], $win_match['hscorehalf'], $win_match['ascorehalf'], $win_match['hred'], $win_match['ared'],
                            $win_match['hyellow'], $win_match['ayellow'], $win_match['hcorner'], $win_match['acorner'], $lg_mid);
                        if (count($staticItem) > 0) {
                            $tempArray[$lg_mid] = $staticItem;

                            echo "spiderLiveMatch: lg_mid = $lg_mid <br>";
                        }
                    }
                }
//                dump($tempArray);
                StatisticFileTool::putFileToLiveChange($tempArray, MatchLive::kSportFootball, 'score');
            }
        }
        dump(time()-$lastTime);
    }

    /**
     * 网页版的比分更新
     */
    private function matchPcLiveChange() {
        $lastTime = time();
        $url = "http://live.titan007.com/vbsxml/change.xml?r=007".time()*1000;
        $content = $this->spiderTextFromUrlByWin007($url, true);
        preg_match_all('/<h>(.*?)<\\/h>/is', $content, $tempMatches);

        if (count($tempMatches) > 0) {
            $matches = $tempMatches[1];
            $tempArray = StatisticFileTool::getFileFromChange(MatchLive::kSportFootball, 'score');
            if (!is_array($tempArray)) $tempArray = array();

            $win_ids = array();
            $win_matches = array();
            foreach ($matches as $matchStr) {
                $matchStr = str_replace("<![CDATA[","",$matchStr);
                $matchStr = str_replace("]]>","",$matchStr);
                if(count(explode("^", $matchStr)) >= 19) {
                    $lgm = null;
                    list(
                        $id, $status, $hscore, $ascore, $hscorehalf,
                        $ascorehalf, $hred, $ared, $timeStr, $halfDateStr,
                        $a, $b, $hyellow, $ayellow, $dateStr,
                        $c, $hcorner, $acorner, $d) = explode("^", $matchStr);
                    if ($status == 0) {
                        continue;
                    }
                    $win_ids[] = $id;
                    $dateStr = date("Y-m-d", strtotime(date("Y")."-".$dateStr))." ".date("H:i:s", strtotime($timeStr));
                    if ($status >= 2) {
                        $dateHalfStr = "";
                        $halfDates = explode(",", $halfDateStr);
                        if (count($halfDates) >= 6) {
                            $halfDateStr = $halfDates[0] . "-" . ($halfDates[1]+1) . "-" . $halfDates[2] . " " . $halfDates[3] . ":" . $halfDates[4] . ":" . $halfDates[5];
                            $dateHalfStr = date("Y-m-d H:i:s", strtotime($halfDateStr));
                        }
                    } else {
                        $dateHalfStr = "";
                    }
                    $win_matches[$id] = ['status'=>$status,'dateStr'=>$dateStr,'dateHalfStr'=>$dateHalfStr,'hscore'=>$hscore,'ascore'=>$ascore,
                        'hscorehalf'=>$hscorehalf,'ascorehalf'=>$ascorehalf,'hred'=>$hred,'ared'=>$ared,'hyellow'=>$hyellow,'ayellow'=>$ayellow,
                        'hcorner'=>$hcorner,'acorner'=>$acorner];

                    echo "$matchStr<br>";
                }
            }
            $lg_matches = \App\Models\LiaoGouModels\Match::query()->whereIn('win_id', $win_ids)->get();
            $match_afters = \App\Models\LiaoGouModels\MatchesAfter::query()->whereIn('win_id', $win_ids)->get();
            $tempMatchAfters = array();
            echo "win_ids count = ".count($win_ids)."; lg_matches count = ".count($lg_matches)."; lg_match_afters count = ".count($match_afters)."<br>";
            foreach ($match_afters as $match_after) {
                $tempMatchAfters[$match_after->id] = $match_after;
            }
            foreach ($lg_matches as $m) {
                if (array_key_exists($m->win_id, $win_matches)) {
                    $win_match = $win_matches[$m->win_id];
                    $date = $win_match['dateStr'];
                    $m->time = $date;
                    if ($win_match['dateHalfStr'] != "") {
                        $m->timehalf = $win_match['dateHalfStr'];
                    }
                    $m->status = $win_match['status'];
//                        $m->setStatus($win_match['status']);
                    $m->hscore = $win_match['hscore'];
                    $m->ascore = $win_match['ascore'];
                    $m->hscorehalf = $win_match['hscorehalf'] == "" ? 0 : $win_match['hscorehalf'];
                    $m->ascorehalf = $win_match['ascorehalf'] == "" ? 0 : $win_match['ascorehalf'];
                    $m->save();

                    $lg_mid = $m->id;

                    //同时保存到冗余表
                    if (array_key_exists($lg_mid, $tempMatchAfters)) {
                        $mf = $tempMatchAfters[$lg_mid];
                        $mf->time = $m->time;
                        $mf->timehalf = $m->timehalf;
                        $mf->status = $m->status;
                        $mf->hscore = $m->hscore;
                        $mf->ascore = $m->ascore;
                        $mf->hscorehalf = $m->hscorehalf;
                        $mf->ascorehalf = $m->ascorehalf;
                        $mf->save();
                    }


                    //数据
//                        $md = \App\Models\LiaoGouModels\MatchData::query()->find($lg_mid);
//                        if (!isset($md)) {
//                            $md = new MatchData();
//                            $md->id = $lg_mid;
//                        }
//                        $md->h_corner = $win_match['hcorner'];
//                        $md->a_corner = $win_match['acorner'];
//                        $md->h_yellow = $win_match['hyellow'];
//                        $md->a_yellow = $win_match['ayellow'];
//                        $md->h_red = $win_match['hred'];
//                        $md->a_red = $win_match['ared'];
//                        $md->save();

                    //静态数据
                    $staticItem = ScoreChangeController::footballLiveItemChange($win_match['status'], $win_match['dateStr'], $win_match['dateHalfStr'], $win_match['hscore'],
                        $win_match['ascore'], $win_match['hscorehalf'], $win_match['ascorehalf'], $win_match['hred'], $win_match['ared'],
                        $win_match['hyellow'], $win_match['ayellow'], $win_match['hcorner'], $win_match['acorner'], $lg_mid);
                    if (count($staticItem) > 0) {
                        $tempArray[$lg_mid] = $staticItem;

                        echo "spiderLiveMatch: lg_mid = $lg_mid <br>";
                    }
                }
            }
            StatisticFileTool::putFileToLiveChange($tempArray, MatchLive::kSportFootball, 'score');
        }
        dump(time()-$lastTime);
    }
}