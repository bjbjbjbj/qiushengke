<?php
/**
 * Created by PhpStorm.
 * User: ricky
 * Date: 17/9/7
 * Time: 15:32
 */

namespace App\Http\Controllers\WinSpider\basket;

use App\Models\WinModels\BasketLeague;
use App\Models\WinModels\BasketSeason;
use App\Models\WinModels\BasketState;

trait SpiderBasketOnce
{
    /**
     * 赛事列表
     */
    private function leaguesInfo()
    {
        $url = "http://txt.win007.com/phone/lqinfoindex.aspx";
        $str = $this->spiderTextFromUrl($url);
        if ($str) {
            $ss = explode("$$", $str);
            //国家地区
            $states = explode("!", $ss[0]);
            foreach ($states as $state) {
                list($zid, $name) = explode("^", $state);
                $s = BasketState::find($zid);
                dump($s);
                if (!isset($s)) {
                    $s = new BasketState();
                    $s->id = $zid;
                    $s->zone = $zid;
                    $s->name = $name;
                    $s->save();
                    \App\Models\LiaoGouModels\BasketState::saveDataWithWinData($s,$zid);
                }
                echo 'stats is '. $state . "<br>";
            }

            //赛事
            $leagues = explode("!", $ss[1]);
            foreach ($leagues as $league) {
                echo 'league is '.$league . "<br>";
                list($id, $sid, $name_long, $name, $type, $season) = explode("^", $league);
                $l = BasketLeague::where('id',$id)->first();
                if (is_null($l)) {
                    $l = new BasketLeague();
                    $l->id = $id;
                    $l->hot = 0;
                    $l->forecast = 0;
                    $l->state = $sid;
                    $l->name = $name;
                    $l->name_big = $name;
                    $l->name_long = $name_long;
                    $l->type = $type;
                }
                else{
                    $l->state = $sid;
                    $l->name = $name;
                    $l->name_big = $name;
                    $l->name_long = $name_long;
                    $l->type = $type;
                }
                $l->save();
                \App\Models\LiaoGouModels\BasketLeague::saveDataWithWinData($l,$id);
                //赛季
                $seasons = explode(",", $season);
                foreach ($seasons as $sea) {
                    $s = BasketSeason::where(['lid' => $id, 'name' => $sea])->first();
                    if (!isset($s)) {
                        $s = new BasketSeason();
                        $s->lid = $id;
                        $s->name = $sea;
                        $s->year = explode("-", $sea)[0];
//                        $s->spider_at = date_create();
//                        $s->status = 0;
                        $s->save();
                        \App\Models\LiaoGouModels\BasketSeason::saveDataWithWinData($s, true);
                    }
                    else{
                        \App\Models\LiaoGouModels\BasketSeason::saveDataWithWinData($s, true);
                    }
                }
            }
        }
    }
}