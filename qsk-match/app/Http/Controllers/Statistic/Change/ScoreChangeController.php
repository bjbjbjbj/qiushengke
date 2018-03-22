<?php
namespace App\Http\Controllers\Statistic\Change;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Statistic\Schedule\MatchCommonTool;
use App\Http\Controllers\Statistic\StatisticFileTool;
use App\Http\Controllers\WinSpider\SpiderTools;
use App\Models\LiaoGouModels\BasketMatch;
use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\MatchLive;

/**
 * Created by PhpStorm.
 * User: ricky
 * Date: 2018/2/26 0026
 * Time: 11:00
 */
class ScoreChangeController extends Controller
{
    use SpiderTools, MatchCommonTool, MatchDataChangeTool;

    public function onScoreChangeStatic($sport) {
        switch ($sport) {
            case MatchLive::kSportBasketball:
//                $this->basketLiveChange();
                $this->basketPcLiveChange();
                break;
            case MatchLive::kSportFootball:
            default:
//                $this->footballLiveChange();
                $this->footballPcLiveChange();
                break;
        }
    }

    /**
     * 专门用来删除score.json数据中，已经完结的比赛
     */
    public function onUselessScoreDelete($sport) {
        $scoreData = StatisticFileTool::getFileFromChange($sport, "score");
        if (!is_array($scoreData)) return;

        $tempArray = array();
        $isFootball = $sport == MatchLive::kSportFootball;
        $tempDifTime = $sport == MatchLive::kSportBasketball ? 3600 * 4 : 3600 * 3;
        foreach ($scoreData as $key=>$scoreItem) {
            $match = StatisticFileTool::getFileFromTerminal($sport, $key, 'match');
            $status = $match['status'];
            $time = $match['time'];
            $scoreItem['status'] = $status;
            $scoreItem['timestamp'] = $time;
            if ((time() - $time) <= $tempDifTime) {
                if ($isFootball) {
                    $timehalf = $match['timehalf'];
                    $scoreItem['timestamphalf'] = $timehalf;
                    $scoreItem['time'] = Match::getMatchCurrentTimeByTimestamp($time, $timehalf, $status, true);
                }
                $tempArray[$key] = $scoreItem;
            }
        }
        StatisticFileTool::putFileToLiveChange($tempArray, $sport, "score");
    }

    /**
     * 足球比分更改的部分
     */
    public static function footballLiveItemChange($status, $dateStr, $dateHalfStr, $hscore,
                                                  $ascore, $hscorehalf, $ascorehalf, $hred, $ared,
                                                  $hyellow, $ayellow,$hcorner, $acorner, $lg_mid) {
        //比分更改的列表
        $items = array();

        if ($lg_mid <= 0) return $items;

        $lg_match = StatisticFileTool::getFileFromTerminal(MatchLive::kSportFootball, $lg_mid, 'match');
        if (isset($lg_match)) {
            if ($dateHalfStr != "") {
                $lg_match['timehalf'] = strtotime($dateHalfStr);
            }
            $currentTime = Match::getMatchCurrentTime(date('Y-m-d H:i:s', $lg_match['time']), date('Y-m-d H:i:s', $lg_match['timehalf']), $status, true);
            $hscorehalf = $hscorehalf == "" ? 0 : $hscorehalf;
            $ascorehalf = $ascorehalf == "" ? 0 : $ascorehalf;

            $lg_match['status'] = $status;
            $lg_match['current_time'] = $currentTime;
            $lg_match['hscore'] = $hscore;
            $lg_match['ascore'] = $ascore;
            $lg_match['hscorehalf'] = $hscorehalf;
            $lg_match['ascorehalf'] = $ascorehalf;

            $lg_match['h_corner'] = $hcorner;
            $lg_match['a_corner'] = $acorner;
            $lg_match['h_yellow'] = $hyellow;
            $lg_match['a_yellow'] = $ayellow;
            $lg_match['h_red'] = $hred;
            $lg_match['a_red'] = $ared;

            StatisticFileTool::putFileToTerminal($lg_match, MatchLive::kSportFootball, $lg_mid, 'match');

            $items['status'] = $status;
            $items['time'] = $currentTime;
            $items['timestamp'] = $lg_match['time'];
            $items['timestamphalf'] = $lg_match['timehalf'];

            $items['hname'] = $lg_match['hname'];
            $items['aname'] = $lg_match['aname'];
            $items['lname'] = $lg_match['league'];
            $items['hscore'] = $hscore;
            $items['ascore'] = $ascore;
            $items['hscorehalf'] = $hscorehalf;
            $items['ascorehalf'] = $ascorehalf;

            $items['h_corner'] = $hcorner;
            $items['a_corner'] = $acorner;
            $items['h_yellow'] = $hyellow;
            $items['a_yellow'] = $ayellow;
            $items['h_red'] = $hred;
            $items['a_red'] = $ared;
        }
        return $items;
    }

    /**
     * 足球比分 即时更新比赛数据
     */
    private function footballLiveChange()
    {
        $url = "http://txt.win007.com/phone/livechange.txt";
        $str = $this->spiderTextFromUrl($url);
        if (isset($str) && strlen($str) > 0) {
            $ss = explode("!", $str);
            if (count($ss) > 0) {
                $tempArray = StatisticFileTool::getFileFromChange(MatchLive::kSportFootball, 'score');
                foreach ($ss as $s) {
                    if(count(explode("^", $s)) >= 16) {
                        list(
                            $id, $status, $dateStr, $dateHalfStr, $hscore,
                            $ascore, $hscorehalf, $ascorehalf, $hred, $ared,
                            $hyellow, $ayellow, $asian, $var1, $hcorner,
                            $acorner) = explode("^", $s);
                        $lg_mid = Match::getMatchIdWith($id, 'win_id');
                        $dateStr = date('Y-m-d H:i:s',strtotime($dateStr));
                        if ($status > 2) {
                            $dateHalfStr = date('Y-m-d H:i:s', strtotime($dateHalfStr));
                        } else {
                            $dateHalfStr = "";
                        }
                        $items = self::footballLiveItemChange($status, $dateStr, $dateHalfStr, $hscore,
                            $ascore, $hscorehalf, $ascorehalf, $hred, $ared,
                            $hyellow, $ayellow, $hcorner, $acorner,$lg_mid);
                        if (count($items) > 0) {
                            $tempArray[$lg_mid] = $items;

                            //同时同步 比赛、统计数据
                            $this->footballMatchData($id, $lg_mid);

                            echo "footballLiveChange: lg_mid = $lg_mid <br>";
                        }
                    }
                }
                StatisticFileTool::putFileToLiveChange($tempArray, MatchLive::kSportFootball, 'score');
            }
        }
    }

    private function footballPcLiveChange() {
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
                $id = $m->win_id;
                if (array_key_exists($id, $win_matches)) {
                    $win_match = $win_matches[$id];
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
                    $staticItem = ScoreChangeController::footballLiveItemChange($win_match['status'], $m->time, $m->timehalf, $win_match['hscore'],
                        $win_match['ascore'], $win_match['hscorehalf'], $win_match['ascorehalf'], $win_match['hred'], $win_match['ared'],
                        $win_match['hyellow'], $win_match['ayellow'], $win_match['hcorner'], $win_match['acorner'], $lg_mid);
                    if (count($staticItem) > 0) {
                        $tempArray[$lg_mid] = $staticItem;

                        //同时同步 比赛、统计数据
                        $this->footballMatchData($id, $lg_mid);

                        echo "spiderLiveMatch: lg_mid = $lg_mid <br>";
                    }
                }
            }
            StatisticFileTool::putFileToLiveChange($tempArray, MatchLive::kSportFootball, 'score');
        }
        dump(time()-$lastTime);
    }

    /**
     * 篮球比分更改的部分
     */
    public static function basketLiveItemChange($id,$status,$timeStr,$hscore,$ascore,
                                                $hscore1st,$ascore1st,$hscore2nd,$ascore2nd,$hscore3rd,
                                                $ascore3rd, $hscore4th, $ascore4th,$h_ot,$a_ot,$lg_mid = 0) {

        //比分更改的列表
        $items = array();
        if ($lg_mid == 0) {
            $lg_mid = BasketMatch::getMatchIdWith($id, 'win_id');
        }
        $lg_match = StatisticFileTool::getFileFromTerminal(MatchLive::kSportBasketball, $lg_mid, 'match');
        if (isset($lg_match)) {
            $lg_match['status'] = $status;
            $lg_match['live_time_str'] = $timeStr;

            $hscore = self::getScore($hscore);
            $ascore = self::getScore($ascore);
            $hscore1st = self::getScore($hscore1st);
            $ascore1st = self::getScore($ascore1st);
            $hscore2nd = self::getScore($hscore2nd);
            $ascore2nd = self::getScore($ascore2nd);
            $hscore3rd = self::getScore($hscore3rd);
            $ascore3rd = self::getScore($ascore3rd);
            $hscore4th = self::getScore($hscore4th);
            $ascore4th = self::getScore($ascore4th);
            $h_ot = self::convertOtScore(self::getScore($h_ot));
            $a_ot = self::convertOtScore(self::getScore($a_ot));

            $lg_match['hscore'] = $hscore;
            $lg_match['ascore'] = $ascore;
            $lg_match['hscore_1st'] = $hscore1st;
            $lg_match['ascore_1st'] = $ascore1st;
            $lg_match['hscore_2nd'] = $hscore2nd;
            $lg_match['ascore_2nd'] = $ascore2nd;
            $lg_match['hscore_3rd'] = $hscore3rd;
            $lg_match['ascore_3rd'] = $ascore3rd;
            $lg_match['hscore_4th'] = $hscore4th;
            $lg_match['ascore_4th'] = $ascore4th;
            $lg_match['h_ot'] = $h_ot;
            $lg_match['a_ot'] = $a_ot;

            StatisticFileTool::putFileToTerminal($lg_match, MatchLive::kSportBasketball, $lg_mid, 'match');

            $items['status'] = $status;
            $items['time'] = $timeStr;
            $items['timestamp'] = $lg_match['time'];
            $items['hscore'] = $hscore;
            $items['ascore'] = $ascore;

            $items['hscores'] = [$hscore1st, $hscore2nd, $hscore3rd, $hscore4th];
            $items['ascores'] = [$ascore1st, $ascore2nd, $ascore3rd, $ascore4th];

            $items['h_ots'] = $h_ot;
            $items['a_ots'] = $a_ot;
        }
        return $items;
    }

    /**
     * 篮球比分 即时更新比赛数据
     */
    private function basketLiveChange()
    {
        $url = "http://txt.win007.com/phone/lqscore/lqlivechange.txt";
        $str = $this->spiderTextFromUrl($url);
        if (isset($str) && strlen($str) > 0) {
            $ss = explode("!", $str);
            if (count($ss) > 0) {
                $tempArray = StatisticFileTool::getFileFromChange(MatchLive::kSportBasketball, 'score');
                foreach ($ss as $s) {
                    if (count(explode("^", $s)) >= 19) {
                        list(
                            $id, $status, $timeStr, $hscore, $ascore,
                            $event, $hscore1st, $ascore1st, $hscore2nd, $ascore2nd,
                            $hscore3rd, $ascore3rd, $hscore4th, $ascore4th, $h_ot,
                            $a_ot, $asiaOdd, $enEvent, $ouOdd
                            ) = explode("^", $s);
                        $lg_mid = BasketMatch::getMatchIdWith($id, 'win_id');
                        $items = self::basketLiveItemChange($id, $status, $timeStr, $hscore, $ascore,
                            $hscore1st, $ascore1st, $hscore2nd, $ascore2nd,
                            $hscore3rd, $ascore3rd, $hscore4th, $ascore4th, $h_ot,
                            $a_ot, $lg_mid);
                        if (count($items) > 0) {
                            $tempArray[$lg_mid] = $items;

                            //同时同步 统计数据
                            $this->basketMatchData($id, $lg_mid);

                            echo "basketLiveChange: lg_mid = $lg_mid <br>";
                        }
                    }
                }
                StatisticFileTool::putFileToLiveChange($tempArray, MatchLive::kSportBasketball, 'score');
            }
        }
    }

    /**
     * 篮球比分 即时更新比赛数据
     */
    private function basketPcLiveChange()
    {
        $url = "http://lq3.win007.com/NBA/change.xml?".time()*1000;
        $content = $this->spiderTextFromUrlByWin007($url, true, "http://lq3.win007.com/nba.htm");
        preg_match_all('/<h>(.*?)<\\/h>/is', $content, $tempMatches);
        if (count($tempMatches) > 0) {
            $matches = $tempMatches[1];
            $tempArray = StatisticFileTool::getFileFromChange(MatchLive::kSportBasketball, 'score');
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

                    $h_ots = [$h_ot1, $h_ot2, $h_ot3];
                    $a_ots = [$a_ot1, $a_ot2, $a_ot3];
                    $h_ot = $this->convertOtScoreByScoreArray($h_ots);
                    $a_ot = $this->convertOtScoreByScoreArray($a_ots);

                    $lg_mid = BasketMatch::getMatchIdWith($id, 'win_id');
                    $items = self::basketLiveItemChange($id, $status, $timeStr, $hscore, $ascore,
                        $hscore1st, $ascore1st, $hscore2nd, $ascore2nd,
                        $hscore3rd, $ascore3rd, $hscore4th, $ascore4th, $h_ot,
                        $a_ot, $lg_mid);
                    if (count($items) > 0) {
                        $tempArray[$lg_mid] = $items;

                        //同时同步 统计数据
                        $this->basketMatchData($id, $lg_mid);

                        echo "basketLiveChange: lg_mid = $lg_mid <br>";
                    }
                }
            }
            StatisticFileTool::putFileToLiveChange($tempArray, MatchLive::kSportBasketball, 'score');
        }
    }

    private function convertOtScoreByScoreArray($scoreArray) {
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
}
