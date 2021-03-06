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
use App\Models\WinModels\Score;
use App\Models\WinModels\Season;
use App\Models\WinModels\Stage;
use App\Models\WinModels\State;
use App\Models\WinModels\Zone;
use App\Models\WinModels\Banker;
use Google\Protobuf\Internal\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

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
    private function cupAll(Request $request)
    {
        $lid = $request->input('lid', -1);
        if ($lid > 0) {
            $leagues = League::where(["type" => 2, 'id'=>$lid])->take(1)->get();
        } else {
            $leagues = League::where(["type" => 2])->orderBy('spider_at', 'asc')->take(1)->get();
        }
        foreach ($leagues as $league) {
            echo $league->id . ":";
            $seasons = Season::where(["lid" => $league->id])->orderBy('year', 'desc')->get();
            foreach ($seasons as $season) {
                echo $season->name . "<br>";
                $stages = Stage::where(["lid" => $league->id, "season" => $season->name])->get();
                if (count($stages) > 0) {
                    foreach ($stages as $stage) {
                        $this->cupSchedule($season->lid, $season->name, $stage->id);
                    }
                } else {
                    $this->cupSchedule($season->lid, $season->name);
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
    private function leagueInit(Request $request)
    {
        $lid = $request->input('lid', -1);
        if ($lid > 0) {
            $leagues = League::where(["type" => 1, 'id'=>$lid])->take(1)->get();
        } else {
            $leagues = League::where(["type" => 1])->orderBy('spider_at', 'asc')->take(10)->get();
        }
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
    private function leagueAll(Request $request)
    {
        set_time_limit(0);
        $lid = $request->input('lid', -1);
        if ($lid > 0) {
            $leagues = League::where(["type" => 1, 'id'=>$lid])->take(1)->get();
        } else {
            $leagues = League::where(["type" => 1])->orderBy('spider_at', 'asc')->take(10)->get();
        }
//        $leagues = League::where(["type" => 1, 'sub' => 1,'id'=>619])->orderBy('spider_at', 'asc')->take(1)->get();
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

    private function delUselessScore(Request $request) {
        $lid = $request->input("lid", -1);
        if ($lid > 0) {
            //赛季
            $season = \App\Models\LiaoGouModels\Season::query()
                ->select("name", "year", "total_round", "curr_round", "start")
                ->where('lid',$lid)
                ->orderby('year','desc')
                ->first();
            if (is_null($season)){
                return null;
            }
            $stages = \App\Models\LiaoGouModels\Stage::where(["lid" => $lid, "season" => $season->name])->get();
            foreach ($stages as $stage) {
                $scores = \App\Models\LiaoGouModels\Score::where(['lid' => $lid, 'season' => $season->name, 'stage' => $stage->id])->get();
                $tid_array = array();
                foreach ($scores as $score) {
                    $tid = $score->tid;
                    if (in_array($tid, $tid_array)) {
                        $score->delete();
                        echo "score_id = $score->id ,lg_score delete success!";
                    } else {
                        $tid_array[] = $tid;
                    }
                }
            }
        }
    }

    private function onLeagueHistorySpider(Request $request) {
        $key = "league_schedule_history2";
        $lg_leagues = Redis::get($key);
        if (isset($lg_leagues)) {
            $lg_leagues = json_decode($lg_leagues, true);
        }
        if (!is_array($lg_leagues) || count($lg_leagues) <= 0) {
            $lg_leagues = \App\Models\LiaoGouModels\League::query()
                ->select('win_id', 'type')
                ->where(function ($q) {
                    $q->where('main', 1)
                        ->orWhere('hot', 1);
                })->get()->toArray();
        }
        echo 'league count = '.count($lg_leagues).'<br>';
        $lg_league = $lg_leagues[0];
        $win_lid = $lg_league['win_id'];
        $type = $lg_league['type'];
        $request->merge(['lid'=>$win_lid]);
        if ($type == 1) {
            $this->leagueAll($request);
        } else if ($type == 2) {
            $this->cupAll($request);
        }
        $lg_leagues = array_slice($lg_leagues, 1);
        Redis::set($key, json_encode($lg_leagues));
        if ($request->input('auto', 0) == 1) {
            echo "<script language=JavaScript>window.location.reload();</script>";
            exit;
        }
    }

    private function onLeagueTestSpider() {
        $url = "http://121.10.245.38/phone/FBDataBase/LeaguePoints.aspx?sclassid=381&season=2014&lang=1";

        echo "url = ".$url."</br>";

        $content = '';
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            if (substr($url, 0, 5) == 'https') {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
            curl_setopt($ch, CURLINFO_CONTENT_TYPE, 'application/utf-8');
//            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.98 Safari/537.36");
//        curl_setopt($ch, CURLOPT_USERAGENT, "WSMobile/1.5.1 (iPad; iOS 10.2; Scale/2.00)");
            //这个必须加上去，否则请求会404的
//            curl_setopt($ch, CURLOPT_REFERER, $referee);
            curl_setopt($ch, CURLOPT_COOKIESESSION, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            $response = curl_exec($ch);
            if ($error = curl_error($ch)) {
                die($error);
            }
            curl_close($ch);

            list($head, $content) = explode("\r\n\r\n", $response, 2);
            dump($head);

            //加上这行，可以解决中文乱码问题
//            $content = mb_convert_encoding($content, 'utf-8', 'x-protobuf,utf-8,GBK,UTF-8,ASCII');
        } catch (\Exception $e) {

        }
        dump($content);

        $test = new Message();
        $test->mergeFromString($content);

        dump($test);
    }
}