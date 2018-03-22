<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 17/2/16
 * Time: 下午4:02
 */

namespace App\Http\Controllers\WinSpider;

use App\Models\WinModels\League;
use App\Models\WinModels\LeagueSub;
use App\Models\WinModels\Season;
use App\Models\WinModels\Stage;
use App\Models\WinModels\State;
use App\Models\WinModels\Zone;
use App\Models\WinModels\Banker;

trait SpiderOnce
{
    /**
     * 博彩公司列表
     */
    private function bankers()
    {
        $url = "http://ios.win007.com/phone/Company.aspx";
        $str = $this->spiderTextFromUrl($url);
        if ($str) {
            $bankers = explode("!", $str);
            foreach ($bankers as $banker) {
                list($id, $name, $var1, $var3, $var3) = explode("^", $banker);
                $bank = Banker::find($id);
                if (!isset($bank)) {
                    $bank = new Banker();
                    $bank->id = $id;
                    $bank->name = $name;
                    $bank->save();
                    \App\Models\LiaoGouModels\Banker::saveDataWithWinData($bank,$id);
                }
                echo $banker . "<br>";
            }
        }
    }

    /**
     * 赛事列表
     */
    private function leagues()
    {
        $url = "http://txt.win007.com/phone/InfoIndex.aspx";
        $str = $this->spiderTextFromUrl($url);
        if ($str) {
            $ss = explode("$$", $str);
            //区域
            $zones = explode("!", $ss[0]);
            foreach ($zones as $zone) {
                list($id, $name) = explode("^", $zone);
                $z = Zone::find($id);
                if (!isset($z)) {
                    $z = new Zone();
                    $z->id = $id;
                    $z->name = $name;
                    $z->save();
                    \App\Models\LiaoGouModels\Zone::saveDataWithWinData($z,$id);
                }
                echo $zone . "<br>";
            }
            //国家
            $states = explode("!", $ss[1]);
            foreach ($states as $state) {
                list($id, $zid, $name) = explode("^", $state);
                $s = State::find($id);
                if (!isset($s)) {
                    $s = new State();
                    $s->id = $id;
                    $s->zone = $zid;
                    $s->name = $name;
                    $s->save();
                    \App\Models\LiaoGouModels\State::saveDataWithWinData($s,$id);
                }
                echo $state . "<br>";
            }

            //赛事
            $leagues = explode("!", $ss[2]);
            foreach ($leagues as $league) {
                list($id, $sid, $name_long, $name, $type, $season) = explode("^", $league);
                $l = League::find($id);
                if (!isset($l)) {
                    $l = new League();
                    $l->id = $id;
                    $l->state = $sid;
                    $l->name = $name;
                    $l->name_big = $name;
                    $l->name_long = $name_long;
                    $l->type = $type;
                    $l->hot = 0;
                    $l->forecast = 0;
                    $l->create_at = date("Y-m-d H:i:s");
                    $l->spider_at = date("Y-m-d H:i:s");
                    $l->save();
                    \App\Models\LiaoGouModels\League::saveDataWithWinData($l);
                } else {
                    $l->state = $sid;
                    $l->name = $name;
                    $l->name_big = $name;
                    $l->name_long = $name_long;
                    $l->type = $type;
                    $l->save();
                    \App\Models\LiaoGouModels\League::saveDataWithWinData($l);
                }
                //赛季
                $seasons = explode(",", $season);
                foreach ($seasons as $sea) {
                    $s = Season::where(['lid' => $id, 'name' => $sea])->first();
                    if (!isset($s)) {
                        $s = new Season();
                        $s->lid = $id;
                        $s->name = $sea;
                        $s->year = explode("-", $sea)[0];
//                        $s->spider_at = date_create();
//                        $s->status = 0;
                        $s->save();
                        \App\Models\LiaoGouModels\Season::saveDataWithWinData($s,true);
                    }
                }
                echo $league . "<br>";
            }
        }
    }

    /**
     * 初始化杯赛赛程数据
     */
    private function cupInit()
    {
        $leagues = League::where(["type" => 2])->orderBy('spider_at', 'asc')->take(10)->get();
        foreach ($leagues as $league) {
            echo $league->id . ":";
            $seasons = Season::where(["lid" => $league->id])->orderBy('year', 'desc')->get();
            foreach ($seasons as $season) {
                echo $season->name . "<br>";
                $this->cupSchedule($season->lid, $season->name);
            }
            $league->spider_at = date("Y-m-d H:i:s");
            $league->save();
            echo "<br>";
        }
    }

    /**
     * 抓取全部杯赛赛程数据
     */
    private function cupAll()
    {
        $leagues = League::where(["type" => 2])->orderBy('spider_at', 'asc')->take(1)->get();
        foreach ($leagues as $league) {
            echo $league->id . ":";
            $seasons = Season::where(["lid" => $league->id])->orderBy('year', 'desc')->get();
            foreach ($seasons as $season) {
                echo $season->name . "<br>";
                $stages = Stage::where(["lid" => $league->id, "season" => $season->name])->get();
                foreach ($stages as $stage) {
                    $this->cupSchedule($season->lid, $season->name, $stage->id);
                }
            }
            $league->spider_at = date("Y-m-d H:i:s");
            $league->save();
            echo "<br>";
        }
    }

    /**
     * 初始化联赛赛程
     */
    private function leagueInit()
    {
        $leagues = League::where(["type" => 1])->orderBy('spider_at', 'asc')->take(10)->get();
        foreach ($leagues as $league) {
            echo $league->id . ":";
            $seasons = Season::where(["lid" => $league->id])->orderBy('year', 'desc')->get();
            foreach ($seasons as $season) {
                echo $season->name . "<br>";
                $this->leagueSchedule($season->lid, $season->name);
            }
            $league->spider_at = date("Y-m-d H:i:s");
            $league->save();
            echo "<br>";
        }
    }

    /**
     * 联赛赛程与积分
     */
    private function leagueAll()
    {
//        $leagues = \App\Models\League::where(["type" => 1])->orderBy('spider_at', 'asc')->take(1)->get();
        $leagues = League::where(["type" => 1, 'sub' => 1,'id'=>619])->orderBy('spider_at', 'asc')->take(1)->get();
        foreach ($leagues as $league) {
            echo $league->id . ":";
            $seasons = Season::where(["lid" => $league->id])->orderBy('year', 'desc')->get();
            foreach ($seasons as $season) {
                echo $season->name . "<br>";
                $subs = LeagueSub::where(["lid" => $league->id, "season" => $season->name])->get();
                if (count($subs) > 0) {
                    foreach ($subs as $sub) {
                        if ($sub->total_round > 0) {
                            foreach (range(1, $sub->total_round) as $round) {
                                $this->leagueSchedule($season->lid, $season->name, $round, $sub->subid);
                            }
                        } else {
                            $this->leagueSchedule($season->lid, $season->name, NULL, $sub->subid);
                        }
                        $this->leagueAllRanking($season->lid, $season->name, $sub->subid);
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
                }
            }
            $league->spider_at = date("Y-m-d H:i:s");
            $league->save();
            echo "<br>";
        }
    }
}