<?php
namespace App\Http\Controllers\App\MatchDetail;

use App\Http\Controllers\App\MatchDetail\Basketball\BasketballDetailController;
use App\Http\Controllers\App\MatchDetail\Football\FootballDetailController;
use App\Models\LiaoGouModels\BasketMatch;
use App\Models\LiaoGouModels\BasketMatchesAfter;
use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\MatchesAfter;
use App\Models\LiaoGouModels\MatchLive;
use Illuminate\Support\Facades\Redis;

/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/1/30
 * Time: 14:14
 */
class DetailController
{
    public function index($date, $sport, $mid, $tab) {
        if ($sport == MatchLive::kSportBasketball) {
            $controller = new BasketballDetailController();
        } else {
            $controller = new FootballDetailController();
        }
        if (isset($controller)) {
            if (in_array($tab, $controller->getTabs())) {
                if ($tab == 'match') {
                    if ($date == '0') {
                        return $controller->match($mid, $date);
                    }
                } else {
                    if ($sport == MatchLive::kSportBasketball) {
                        $match = BasketMatch::query()->find($mid);
                    } else {
                        $match = Match::query()->find($mid);
                    }
                    if (isset($match)) {
                        $matchDate = date('Ymd', strtotime($match->time));
                        if ($date == $matchDate) {
                            return $controller->$tab($mid, $date);
                        }
                    }
                }
            }
        }
        return null;
    }

    public function statistic($method) {
        if (method_exists($this, $method)) {
            $this->$method();
        } else {
            echo "Error: Not Found action 'DetailController->$method()'";
        }
    }

    //================足球数据静态化=======================

    /**
     * 正在进行的比赛 1分钟执行一次
     * 比赛事件、赔率、同赔
     */
    public function footballGoing() {
        $footballController = new FootballDetailController();

        $matches = MatchesAfter::query()
            ->join('leagues', 'matches_afters.lid', '=', 'leagues.id')
            ->select('matches_afters.*')
            ->where(function ($q){
                $q->where("status", '>', 0)
                    ->orwhere(function ($q2){
                        $q2->where('status', '<', 0)
                            ->where('time', '>', strtotime('-3 hour'));
                    });
            })
            ->orderBy('leagues.hot', 'desc')
            ->orderBy('status', 'desc')
            ->orderBy('time', 'asc')->take(20)->get();
        foreach ($matches as $match) {
            $id = $match->id;
            $date = date('Ymd', strtotime($match->time));
            $footballController->match($id, $date);
            $footballController->event($id, $date);
            $footballController->odd($id, $date);
            $footballController->oddIndex($id, $date);
            $footballController->sameOdd($id, $date);
        }
        dump(count($matches));
    }

    /**
     * 未开始的比赛 5分钟执行一次
     * 基本信息、角球信息、球队风格
     */
    public function footballUnstart() {
        $footballController = new FootballDetailController();

        $key = 'football_detail_time_unstart';
//        Redis::del($key);
        $value = Redis::get($key);
        $saveTime = date('Y-m-d H:i:s', strtotime('-2 hours'));
        $saveCount = 0;
        if (isset($value)) {
            $value = json_decode($value);
            if (isset($value->time) && strtotime($saveTime) < strtotime($value->time)) {
                $saveTime = $value->time;
            }
            $saveCount = $value->count;
        }

        $matches = MatchesAfter::query()->join('leagues', function ($join) {
            $join->on('matches_afters.lid', '=', 'leagues.id')
                ->where('leagues.hot', '=', 1);
        })->select('matches_afters.*')->where("status", '=', 0)
            ->where('time', '>=', $saveTime)
            ->orderBy('time', 'asc')->take(100)->get();

        $count = 0;
        foreach ($matches as $match) {
            if ($count >= 5) break;

            $time = $match->time;
            if (strtotime($time) > strtotime($saveTime)) {
                $count++;
            }
            $id = $match->id;
            $date = date('Ymd', strtotime($time));
            $footballController->match($id, $date);
            $footballController->base($id, $date);
            $footballController->corner($id, $date);
            $footballController->style($id, $date);

            $saveCount++;
        }
        if (isset($time)) {
            $saveTime = $time;
        }
        Redis::set($key, json_encode(['time'=>$saveTime, 'count'=>$saveCount]));
        if ($saveCount > 200) {
            Redis::del($key);
        }
        dump($saveTime, $saveCount);
    }

    /**
     * 已结束的比赛 10分钟执行一次
     * 所有都执行一遍
     */
    public function footballDone() {

        $key = 'football_detail_time_done';
//        Redis::del($key);
        $value = Redis::get($key);

        $saveTime = date('Y-m-d H:i:s', strtotime('+2 hours'));
        $saveCount = 0;
        if (isset($value)) {
            $value = json_decode($value);
            if (isset($value->time) && strtotime($saveTime) < strtotime($value->time)) {
                $saveTime = $value->time;
            }
            $saveCount = $value->count;
        }

        $matches = Match::query()->where("status", '=', -1)
            ->where('time', '<=', $saveTime)
            ->orderBy('time', 'desc')->take(100)->get();

        $count = 0;

        $footballController = new FootballDetailController();
        foreach ($matches as $match) {
            if ($count >= 6) break;

            $time = $match->time;
            if (strtotime($time) < strtotime($saveTime)) {
                $count++;
            }
            $id = $match->id;
            $date = date('Ymd', strtotime($time));
            $footballController->match($id, $date);
            $footballController->event($id, $date);
            $footballController->odd($id, $date);
            $footballController->oddIndex($id, $date);
            $footballController->sameOdd($id, $date);
            $footballController->base($id, $date);
//            $footballController->corner($id, $date);
//            $footballController->style($id, $date);

            $saveCount++;
        }
        if (isset($time)) {
            $saveTime = $time;
        }
        Redis::set($key, json_encode(['time'=>$saveTime, 'count'=>$saveCount]));
        if ($saveCount > 200) {
            Redis::del($key);
        }
        dump($saveCount, $saveCount);
    }

    //================篮球数据静态化=======================

    /**
     * 正在进行的比赛 1分钟执行一次
     * 比赛事件、赔率、同赔
     */
    public function basketballGoing() {
        $controller = new BasketballDetailController();

        $matches = BasketMatchesAfter::query()
            ->where(function ($q){
                $q->where("status", '>', 0)
                    ->orwhere(function ($q2){
                        $q2->where('status', '<', 0)
                            ->where('time', '>', strtotime('-4 hour'));
                    });
            })
            ->orderBy('status', 'desc')
            ->orderBy('time', 'asc')->take(20)->get();
        foreach ($matches as $match) {
            $id = $match->id;
            $date = date('Ymd', strtotime($match->time));
            $controller->match($id, $date);
            $controller->base($id, $date);
        }
        dump(count($matches));
    }

    /**
     * 未开始的比赛 5分钟执行一次
     * 基本信息、角球信息、球队风格
     */
    public function basketballUnstart() {
        $controller = new BasketballDetailController();

        $key = 'basketball_detail_time_unstart';
//        Redis::del($key);
        $value = Redis::get($key);
        $saveTime = date('Y-m-d H:i:s', strtotime('-2 hours'));
        $saveCount = 0;
        if (isset($value)) {
            $value = json_decode($value);
            if (isset($value->time) && strtotime($saveTime) < strtotime($value->time)) {
                $saveTime = $value->time;
            }
            $saveCount = $value->count;
        }

        $matches = BasketMatchesAfter::query()
            ->where("status", '=', 0)
            ->where('time', '>=', $saveTime)
            ->orderBy('time', 'asc')->take(100)->get();

        $count = 0;
        foreach ($matches as $match) {
            if ($count >= 5) break;

            $time = $match->time;
            if (strtotime($time) > strtotime($saveTime)) {
                $count++;
            }
            $id = $match->id;
            $date = date('Ymd', strtotime($time));
            $controller->match($id, $date);
            $controller->base($id, $date);

            $saveCount++;
        }
        if (isset($time)) {
            $saveTime = $time;
        }
        Redis::set($key, json_encode(['time'=>$saveTime, 'count'=>$saveCount]));
        if ($saveCount > 200) {
            Redis::del($key);
        }
        dump($saveTime, $saveCount);
    }

    /**
     * 已结束的比赛 10分钟执行一次
     * 所有都执行一遍
     */
    public function basketballDone() {
        $key = 'basketball_detail_time_done';
//        Redis::del($key);
        $value = Redis::get($key);

        $saveTime = date('Y-m-d H:i:s', strtotime('+2 hours'));
        $saveCount = 0;
        if (isset($value)) {
            $value = json_decode($value);
            if (isset($value->time) && strtotime($saveTime) < strtotime($value->time)) {
                $saveTime = $value->time;
            }
            $saveCount = $value->count;
        }

        $matches = BasketMatch::query()->where("status", '=', -1)
            ->where('time', '<=', $saveTime)
            ->orderBy('time', 'desc')->take(100)->get();

        $count = 0;

        $controller = new BasketballDetailController();
        foreach ($matches as $match) {
            if ($count >= 6) break;

            $time = $match->time;
            if (strtotime($time) < strtotime($saveTime)) {
                $count++;
            }
            $id = $match->id;
            $date = date('Ymd', strtotime($time));
            $controller->match($id, $date);
            $controller->base($id, $date);

            $saveCount++;
        }
        if (isset($time)) {
            $saveTime = $time;
        }
        Redis::set($key, json_encode(['time'=>$saveTime, 'count'=>$saveCount]));
        if ($saveCount > 200) {
            Redis::del($key);
        }
        dump($saveCount, $saveCount);
    }
}