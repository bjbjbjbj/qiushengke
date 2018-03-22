<?php
/**
 * 爬赔率数据
 * Created by PhpStorm.
 * User: ricky
 * Date: 17/9/6
 * Time: 16:03
 */
namespace App\Http\Controllers\WinSpider\basket;

use App\Http\Controllers\FileTool;
use App\Http\Controllers\Statistic\Change\ScoreChangeController;
use App\Http\Controllers\Statistic\StatisticFileTool;
use App\Models\LiaoGouModels\BasketMatchesAfter;
use App\Models\LiaoGouModels\MatchLive;
use App\Models\WinModels\BasketLeague;
use App\Models\WinModels\BasketMatch;

trait SpiderBasketSchedule
{
    /**
     * 爬当前比赛赛程
     * @param int $type 类型0全部 1一级
     */
    private function matchLiveSchedule($type = 0)
    {
        $url = "http://txt.win007.com/phone/lqscore/schedule_0_$type.txt";
        $str = $this->spiderTextFromUrl($url);
        if ($str) {
            $ss = explode("$$", $str);
            //赛事
            $leagues = explode("!", $ss[count($ss) == 3 ? 1 : 0]);
            foreach ($leagues as $league) {
                if (count(explode("^", $league)) >= 3) {
                    list($name, $lid, $hot) = explode("^", $league);
                    $l = BasketLeague::find($lid);
                    if (!isset($l)) {
                        $l = new BasketLeague();
                        $l->id = $lid;
                        $l->name = $name;
                        $l->hot = $hot;
                        $l->save();
                        \App\Models\LiaoGouModels\BasketLeague::saveDataWithWinData($l);
                    }
                    if ($l->hot != $hot) {
                        $l->hot = $hot;
                        $l->save();
                        \App\Models\LiaoGouModels\BasketLeague::saveDataWithWinData($l);
                    }
                }
//                echo "$league<br>";
            }
            //比赛
            $matches = explode("!", $ss[count($ss) == 3 ? 2 : 1]);
            foreach ($matches as $match) {
                if (count(explode("^", $match)) >= 23) {
                    list(
                        $id, $lid, $color, $dateStr, $status,
                        $timeStr, $hname, $aname, $hscore, $ascore,
                        $a1, $a2, $a3, $a4, $a5,
                        $hscore1st, $ascore1st, $hscore2nd, $ascore2nd, $hscore3rd,
                        $ascore3rd, $hscore4th, $ascore4th,$hot,$aot
                        ) = explode("^", $match);
                    $m = BasketMatch::find($id);
                    if (!isset($m)) {
                        $m = new BasketMatch();
                        $m->id = $id;
                        $m->is_odd = 0;
                        $m->save();
                    }
                    $m->lname = BasketMatch::getLeagueName($m);
                    //赛季名称
                    $m->season = BasketMatch::getLastSeasonName($m);
                    $m->lid = $lid;
                    $date = date('Y-m-d H:i:s', strtotime($dateStr));
                    $m->time = $date;
                    $m->status = $status;
                    $m->neutral = str_contains($hname, "(中)") ? 1 : 0;
                    $m->hname = $hname;
                    $m->aname = $aname;
                    $m->hscore = $this->getScore($hscore);
                    $m->ascore = $this->getScore($ascore);
                    $m->hscore_1st = $this->getScore($hscore1st);
                    $m->ascore_1st = $this->getScore($ascore1st);
                    $m->hscore_2nd = $this->getScore($hscore2nd);
                    $m->ascore_2nd = $this->getScore($ascore2nd);
                    $m->hscore_3rd = $this->getScore($hscore3rd);
                    $m->ascore_3rd = $this->getScore($ascore3rd);
                    $m->hscore_4th = $this->getScore($hscore4th);
                    $m->ascore_4th = $this->getScore($ascore4th);
                    if (isset($hot) && (!isset($m->h_ot) || !str_contains(",",$m->h_ot)))
                        $m->h_ot = $hot;
                    if (isset($aot) && (!isset($m->a_ot) || !str_contains(",",$m->a_ot)))
                        $m->a_ot = $aot;
                    $m->save();

                    //同时保存到liaogou表
                    \App\Models\LiaoGouModels\BasketMatch::saveWithWinData($m);
                }
            }
        }
    }

    /**
     * 根据日期爬赛程
     * @param null $date
     */
    private function matchDateSchedule($date = NULL, $isLastSeason = false)
    {
        if ($date == NULL) {
            $date = date("Y-m-d");
        }
        $url = "http://txt.win007.com/phone/lqschedule.aspx?date=$date";
        $str = $this->spiderTextFromUrl($url);
        if ($str) {
            $ss = explode("$$", $str);
            if (count($ss) == 2) {
                //赛事
                $leagues = explode("!", $ss[0]);
                foreach ($leagues as $league) {
                    if (count(explode("^", $league)) >= 3) {
                        list($name, $lid, $hot) = explode("^", $league);
                        $l = BasketLeague::find($lid);
                        if (!isset($l)) {
                            $l = new BasketLeague();
                            $l->id = $lid;
                            $l->name = $name;
                            $l->hot = $hot;
                            $l->save();
                            \App\Models\LiaoGouModels\BasketLeague::saveDataWithWinData($l);
                        }
                        if ($l->hot != $hot) {
                            $l->hot = $hot;
                            $l->save();
                            \App\Models\LiaoGouModels\BasketLeague::saveDataWithWinData($l);
                        }
                    }
//                echo "$league<br>";
                }
                //比赛
                $matches = explode("!", $ss[1]);
                foreach ($matches as $match) {
                    if (count(explode("^", $match)) >= 23) {
                        list(
                            $id, $lid, $color, $dateStr, $status,
                            $timeStr, $hname, $aname, $hscore, $ascore,
                            $a1, $a2, $a3, $a4, $a5,
                            $hscore1st, $ascore1st, $hscore2nd, $ascore2nd, $hscore3rd,
                            $ascore3rd, $hscore4th, $ascore4th
                            ) = explode("^", $match);
                        $m = BasketMatch::find($id);
                        if (!isset($m)) {
                            $m = new BasketMatch();
                            $m->id = $id;
                            $m->is_odd = 0;
                            $m->save();
                        }
                        //赛事名称
                        $m->lname = BasketMatch::getLeagueName($m);
                        //赛季名称
                        if ($isLastSeason) {
                            $m->season = BasketMatch::getLastSeasonName($m);
                        }
                        $m->lid = $lid;
                        $date = date('Y-m-d H:i:s', strtotime($dateStr));
                        $m->time = $date;
                        $m->status = $status;
                        $m->neutral = str_contains($hname, "(中)") ? 1 : 0;
                        $m->hname = $hname;
                        $m->aname = $aname;
                        $m->hscore = $this->getScore($hscore);
                        $m->ascore = $this->getScore($ascore);
                        $m->hscore_1st = $this->getScore($hscore1st);
                        $m->ascore_1st = $this->getScore($ascore1st);
                        $m->hscore_2nd = $this->getScore($hscore2nd);
                        $m->ascore_2nd = $this->getScore($ascore2nd);
                        $m->hscore_3rd = $this->getScore($hscore3rd);
                        $m->ascore_3rd = $this->getScore($ascore3rd);
                        $m->hscore_4th = $this->getScore($hscore4th);
                        $m->ascore_4th = $this->getScore($ascore4th);
                        $m->save();
                        //同时保存到liaogou表
                        \App\Models\LiaoGouModels\BasketMatch::saveWithWinData($m);
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
        $url = "http://txt.win007.com/phone/lqscore/lqlivechange.txt";
        $str = $this->spiderTextFromUrl($url);
        if ($str) {
            $ss = explode("!", $str);
            if (count($ss) > 0) {
                $tempArray = StatisticFileTool::getFileFromChange(MatchLive::kSportBasketball, 'score');
                if (!is_array($tempArray)) $tempArray = array();

                $win_ids = array();
                $win_matches = array();
                foreach ($ss as $s) {
                    if (count(explode("^", $s)) >= 19) {
                        list(
                            $id, $status, $timeStr, $hscore, $ascore,
                            $event, $hscore1st, $ascore1st, $hscore2nd, $ascore2nd,
                            $hscore3rd, $ascore3rd, $hscore4th, $ascore4th, $h_ot,
                            $a_ot, $asiaOdd, $enEvent, $ouOdd
                            ) = explode("^", $s);
                        if ($status == 0) {
                            continue;
                        }
                        $win_ids[] = $id;
                        $win_matches[$id] = ['status'=>$status,'timeStr'=>$timeStr,'hscore'=>$hscore,'ascore'=>$ascore,
                            'hscore1st'=>$hscore1st,'ascore1st'=>$ascore1st,'hscore2nd'=>$hscore2nd,
                            'ascore2nd'=>$ascore2nd,'hscore3rd'=>$hscore3rd,'ascore3rd'=>$ascore3rd,
                            'hscore4th'=>$hscore4th,'ascore4th'=>$ascore4th,'h_ot'=>$h_ot,'a_ot'=>$a_ot];
                        echo "$s<br>";
                    }
                }

                $lg_matches = \App\Models\LiaoGouModels\BasketMatch::query()->whereIn('win_id', $win_ids)->get();
                $lg_match_afters = BasketMatchesAfter::query()->whereIn('win_id', $win_ids)->get();
                echo "win_ids count = ".count($win_ids)."; lg_matches count = ".count($lg_matches)."; lg_match_afters count = ".count($lg_match_afters)."<br>";
                $match_afters = array();
                foreach ($lg_match_afters as $tempMatch) {
                    $match_afters[$tempMatch->id] = $tempMatch;
                }
                foreach ($lg_matches as $m) {
                    $id = $m->win_id;
                    if (array_key_exists($id, $win_matches)) {
                        $winMatch = $win_matches[$id];
                        if (isset($winMatch)) {
                            $status = $winMatch['status'];
                            $timeStr = $winMatch['timeStr'];
                            $hscore = $winMatch['hscore'];
                            $ascore = $winMatch['ascore'];
                            $hscore1st = $winMatch['hscore1st'];
                            $ascore1st = $winMatch['ascore1st'];
                            $hscore2nd = $winMatch['hscore2nd'];
                            $ascore2nd = $winMatch['ascore2nd'];
                            $hscore3rd = $winMatch['hscore3rd'];
                            $ascore3rd = $winMatch['ascore3rd'];
                            $hscore4th = $winMatch['hscore4th'];
                            $ascore4th = $winMatch['ascore4th'];
                            $h_ot = $winMatch['h_ot'];
                            $a_ot = $winMatch['a_ot'];

                            if ($m->status == 2 && !isset($m->timehalf) && $status > 2) {
                                $m->timehalf = date("Y-m-d H:i:s");
                            }
                            $m->status = $status;
                            $m->live_time_str = $timeStr;
                            $m->hscore = $this->getScore($hscore);
                            $m->ascore = $this->getScore($ascore);
                            $m->hscore_1st = $this->getScore($hscore1st);
                            $m->ascore_1st = $this->getScore($ascore1st);
                            $m->hscore_2nd = $this->getScore($hscore2nd);
                            $m->ascore_2nd = $this->getScore($ascore2nd);
                            $m->hscore_3rd = $this->getScore($hscore3rd);
                            $m->ascore_3rd = $this->getScore($ascore3rd);
                            $m->hscore_4th = $this->getScore($hscore4th);
                            $m->ascore_4th = $this->getScore($ascore4th);
                            $m->h_ot = $this->getScore($h_ot);
                            $m->a_ot = $this->getScore($a_ot);
                            $m->save();

                            $lg_mid = $m->id;

                            //同时保存到冗余表
                            if (array_key_exists($lg_mid, $match_afters)) {
                                $matches_after = $match_afters[$lg_mid];
                                $matches_after->status = $m->status;
                                $matches_after->live_time_str = $m->live_time_str;
                                $matches_after->hscore = $m->hscore;
                                $matches_after->ascore = $m->ascore;
                                $matches_after->hscore_1st = $m->hscore_1st;
                                $matches_after->ascore_1st = $m->ascore_1st;
                                $matches_after->hscore_2nd = $m->hscore_2nd;
                                $matches_after->ascore_2nd = $m->ascore_2nd;
                                $matches_after->hscore_3rd = $m->hscore_3rd;
                                $matches_after->ascore_3rd = $m->ascore_3rd;
                                $matches_after->hscore_4th = $m->hscore_4th;
                                $matches_after->ascore_4th = $m->ascore_4th;
                                $matches_after->h_ot = $m->h_ot;
                                $matches_after->a_ot = $m->a_ot;
                                $matches_after->save();
                            }

                            $staticItem = ScoreChangeController::basketLiveItemChange($id, $status, $timeStr, $hscore, $ascore,
                                $hscore1st, $ascore1st, $hscore2nd, $ascore2nd,
                                $hscore3rd, $ascore3rd, $hscore4th, $ascore4th, $h_ot, $a_ot, $lg_mid);
                            if (count($staticItem) > 0) {
                                $tempArray[$lg_mid] = $staticItem;
                                echo "spiderLiveMatch: lg_mid = $lg_mid <br>";
                            }
                        }
                    }
                }
                StatisticFileTool::putFileToLiveChange($tempArray, MatchLive::kSportBasketball, 'score');
            }
        }
        dump(time()-$lastTime);
    }

    /**
     * 网页版的比分更新
     */
    private function matchPcLiveChange() {
        $lastTime = time();
        $url = "http://lq3.win007.com/NBA/change.xml?".time()*1000;
        $content = $this->spiderTextFromUrlByWin007($url, true, "http://lq3.win007.com/nba.htm");
        preg_match_all('/<h>(.*?)<\\/h>/is', $content, $tempMatches);

        if (count($tempMatches) > 0) {
            $matches = $tempMatches[1];
            $tempArray = StatisticFileTool::getFileFromChange(MatchLive::kSportBasketball, 'score');
            if (!is_array($tempArray)) $tempArray = array();

            $win_ids = array();
            $win_matches = array();
            foreach ($matches as $matchStr) {
                $matchStr = str_replace("<![CDATA[","",$matchStr);
                $matchStr = str_replace("]]>","",$matchStr);
                if (count(explode("^", $matchStr)) >= 23) {
                    list($id, $status, $timeStr, $hscore, $ascore,
                        $hscore1st, $ascore1st, $hscore2nd, $ascore2nd, $hscore3rd,
                        $ascore3rd, $hscore4th, $ascore4th, $b, $eventStr,
                        $c, $h_ot1, $a_ot1, $h_ot2, $a_ot2,
                        $h_ot3, $a_ot3, $others) = explode("^", $matchStr);

                    if ($status == 0) {
                        continue;
                    }
                    $win_ids[] = $id;
                    $win_matches[$id] = ['status'=>$status,'timeStr'=>$timeStr,'hscore'=>$hscore,'ascore'=>$ascore,
                        'hscore1st'=>$hscore1st,'ascore1st'=>$ascore1st,'hscore2nd'=>$hscore2nd,
                        'ascore2nd'=>$ascore2nd,'hscore3rd'=>$hscore3rd,'ascore3rd'=>$ascore3rd,
                        'hscore4th'=>$hscore4th,'ascore4th'=>$ascore4th,
                        'h_ot'=>$this->convertOtScore([$h_ot1,$h_ot2,$h_ot3]),'a_ot'=>$this->convertOtScore([$a_ot1,$a_ot2,$a_ot3])];

                    echo "$matchStr<br>";
                }
            }
            $lg_matches = \App\Models\LiaoGouModels\BasketMatch::query()->whereIn('win_id', $win_ids)->get();
            $lg_match_afters = BasketMatchesAfter::query()->whereIn('win_id', $win_ids)->get();
            echo "win_ids count = ".count($win_ids)."; lg_matches count = ".count($lg_matches)."; lg_match_afters count = ".count($lg_match_afters)."<br>";
            $match_afters = array();
            foreach ($lg_match_afters as $tempMatch) {
                $match_afters[$tempMatch->id] = $tempMatch;
            }
            foreach ($lg_matches as $m) {
                $id = $m->win_id;
                if (array_key_exists($id, $win_matches)) {
                    $winMatch = $win_matches[$id];
                    if (isset($winMatch)) {
                        $status = $winMatch['status'];
                        $timeStr = $winMatch['timeStr'];
                        $hscore = $winMatch['hscore'];
                        $ascore = $winMatch['ascore'];
                        $hscore1st = $winMatch['hscore1st'];
                        $ascore1st = $winMatch['ascore1st'];
                        $hscore2nd = $winMatch['hscore2nd'];
                        $ascore2nd = $winMatch['ascore2nd'];
                        $hscore3rd = $winMatch['hscore3rd'];
                        $ascore3rd = $winMatch['ascore3rd'];
                        $hscore4th = $winMatch['hscore4th'];
                        $ascore4th = $winMatch['ascore4th'];
                        $h_ot = $winMatch['h_ot'];
                        $a_ot = $winMatch['a_ot'];

                        if ($m->status == 2 && !isset($m->timehalf) && $status > 2) {
                            $m->timehalf = date_format(date_create(), "Y-m-d H:i");
                        }
                        $m->status = $status;
                        $m->live_time_str = $timeStr;
                        $m->hscore = $this->getScore($hscore);
                        $m->ascore = $this->getScore($ascore);
                        $m->hscore_1st = $this->getScore($hscore1st);
                        $m->ascore_1st = $this->getScore($ascore1st);
                        $m->hscore_2nd = $this->getScore($hscore2nd);
                        $m->ascore_2nd = $this->getScore($ascore2nd);
                        $m->hscore_3rd = $this->getScore($hscore3rd);
                        $m->ascore_3rd = $this->getScore($ascore3rd);
                        $m->hscore_4th = $this->getScore($hscore4th);
                        $m->ascore_4th = $this->getScore($ascore4th);
                        $m->h_ot = $this->getScore($h_ot);
                        $m->a_ot = $this->getScore($a_ot);
                        $m->save();

                        $lg_mid = $m->id;

                        //同时保存到冗余表
                        if (array_key_exists($lg_mid, $match_afters)) {
                            $matches_after = $match_afters[$lg_mid];
                            $matches_after->status = $m->status;
                            $matches_after->live_time_str = $m->live_time_str;
                            $matches_after->hscore = $m->hscore;
                            $matches_after->ascore = $m->ascore;
                            $matches_after->hscore_1st = $m->hscore_1st;
                            $matches_after->ascore_1st = $m->ascore_1st;
                            $matches_after->hscore_2nd = $m->hscore_2nd;
                            $matches_after->ascore_2nd = $m->ascore_2nd;
                            $matches_after->hscore_3rd = $m->hscore_3rd;
                            $matches_after->ascore_3rd = $m->ascore_3rd;
                            $matches_after->hscore_4th = $m->hscore_4th;
                            $matches_after->ascore_4th = $m->ascore_4th;
                            $matches_after->h_ot = $m->h_ot;
                            $matches_after->a_ot = $m->a_ot;
                            $matches_after->save();
                        }

                        $staticItem = ScoreChangeController::basketLiveItemChange($id, $status, $timeStr, $hscore, $ascore,
                            $hscore1st, $ascore1st, $hscore2nd, $ascore2nd,
                            $hscore3rd, $ascore3rd, $hscore4th, $ascore4th, $h_ot, $a_ot, $lg_mid);
                        if (count($staticItem) > 0) {
                            $tempArray[$lg_mid] = $staticItem;
                            echo "spiderLiveMatch: lg_mid = $lg_mid <br>";
                        }
                    }
                }
            }
            StatisticFileTool::putFileToLiveChange($tempArray, MatchLive::kSportBasketball, 'score');
        }
        dump(time()-$lastTime);
    }

    /**
     * 即时更新比赛数据
     */
    private function matchLiveChangeToFile()
    {
        $url = "http://txt.win007.com/phone/lqscore/lqlivechange.txt";
        $str = $this->spiderTextFromUrl($url);
        $tempArray = array();
        if ($str) {
            $ss = explode("!", $str);
            if (count($str) > 0) {
                foreach ($ss as $s) {
                    if (count(explode("^", $s)) >= 19) {
                        list(
                            $id, $status, $timeStr, $hscore, $ascore,
                            $event, $hscore1st, $ascore1st, $hscore2nd, $ascore2nd,
                            $hscore3rd, $ascore3rd, $hscore4th, $ascore4th, $h_ot,
                            $a_ot, $asiaOdd, $enEvent, $ouOdd
                            ) = explode("^", $s);
                        $lg_m = \App\Models\LiaoGouModels\BasketMatch::getMatchWith($id, "win_id");
                        if (isset($lg_m)) {
                            $items = array();
                            $items['status'] = $status;
                            $items['time'] = $timeStr;
                            $items['hscore'] = $hscore;
                            $items['ascore'] = $ascore;

                            $items['hscores'] = [$hscore1st, $hscore2nd, $hscore3rd, $hscore4th];
                            $items['ascores'] = [$ascore1st, $ascore2nd, $ascore3rd, $ascore4th];

                            $items['h_ots'] = (isset($h_ot)&&strlen($h_ot)>0) ? explode(',', $h_ot) : [];
                            $items['a_ots'] = (isset($a_ot)&&strlen($a_ot)>0) ? explode(',', $a_ot) : [];

                            $tempArray[$lg_m->id] = $items;
                        }
                    }
                }
            }
        }
        FileTool::putFileToLiveScore($tempArray, FileTool::kBasketball);
    }

    private function getScore($score)
    {
        if (isset($score) && strlen($score) > 0) {
            return $score;
        }
        return NULL;
    }

    private function convertOtScore($scoreArray) {
        $ot = '';
        foreach ($scoreArray as $ot_item) {
            if (isset($ot_item) && strlen($ot_item) > 0) {
                $ot .= $ot_item.",";
            }
        }
        if (strlen($ot) > 0) {
            $ot = substr($ot, 0, -1);
        }
        return $ot;
    }

    /**
     * 根据比赛id重新爬取比分数据
     */
    private function matchTechnicByMid($mid)
    {
        $m = null;
        $url = "http://apk.win007.com/phone/lqteamtechnic.aspx?id=$mid";
        $str = $this->spiderTextFromUrl($url);
        if ($str) {
            $ss = explode("$$", $str);
            if (count($str) > 0) {
                $s = $ss[0];
                if (count(explode("^", $s)) >= 12) {
                    $lgm = null;
                    list(
                        $status, $hscore, $ascore, $hscore1st, $hscore2nd,
                        $hscore3rd, $hscore4th, $a1, $ascore1st,$ascore2nd,
                        $ascore3rd, $ascore4th, $a2
                        ) = explode("^", $s);
                    $m = BasketMatch::find($mid);
                    if (isset($m)) {
                        if ($m->status == 2 && !isset($m->timehalf) && $status > 2) {
                            $m->timehalf = date_format(date_create(), "Y-m-d H:i");
                        }
                        $m->status = $status;
                        $m->hscore = $this->getScore($hscore);
                        $m->ascore = $this->getScore($ascore);
                        $m->hscore_1st = $this->getScore($hscore1st);
                        $m->ascore_1st = $this->getScore($ascore1st);
                        $m->hscore_2nd = $this->getScore($hscore2nd);
                        $m->ascore_2nd = $this->getScore($ascore2nd);
                        $m->hscore_3rd = $this->getScore($hscore3rd);
                        $m->ascore_3rd = $this->getScore($ascore3rd);
                        $m->hscore_4th = $this->getScore($hscore4th);
                        $m->ascore_4th = $this->getScore($ascore4th);
                        $m->save();

                        //同时保存到liaogou表
                        \App\Models\LiaoGouModels\BasketMatch::saveWithWinData($m);
                    }
                    echo "$s<br>";
                }
            }
        }
        return $m;
    }
}