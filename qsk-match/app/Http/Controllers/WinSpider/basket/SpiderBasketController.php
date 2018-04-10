<?php

namespace App\Http\Controllers\WinSpider\basket;

use App\Http\Controllers\Utils\DateUtils;
use App\Http\Controllers\WinSpider\SpiderTools;
use App\Models\LiaoGouModels\BasketMatchesAfter;
use App\Models\LiaoGouModels\BasketOddsAfter;
use App\Models\WinModels\BasketLeague;
use App\Models\WinModels\BasketMatch;
use App\Models\WinModels\BasketSeason;
use Hamcrest\BaseMatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redis;

class SpiderBasketController extends Controller
{
    use SpiderTools, SpiderBasketOnce, SpiderBasketSchedule, SpiderBasketScore,
        SpiderBasketOdds, SpiderBasketLeague,SpiderBasketMatch,SpiderBasketTeam;

    const SPIDER_ERROR_LIMIT = 4;

    public function index($action, Request $request)
    {
        if (method_exists($this, $action)) {
            $this->$action($request);
        } else {
            echo "Error: Not Found action 'SpiderBasketController->$action()'";
        }
    }

    /**
     * 每天的比赛
     * 每5分钟执行一次
     */
    private function spiderLiveSchedule()
    {
        $this->matchLiveSchedule(0);
        $this->matchLiveSchedule(1);
//        $this->matchLiveScheduleForBetting(2);
//        $this->matchLiveScheduleForBetting(3);
//        $this->matchLiveScheduleForBetting(4);
    }

    /**
     * 完结的比赛
     * 每30分钟执行一次
     */
    private function spiderDoneSchedule()
    {
        $this->matchDateSchedule(NULL, true);
    }

    /**
     * 未来2个比赛日的赛程
     * 每1小时执行一次
     */
    private function spiderNextSchedule()
    {
        foreach (range(1, 2) as $i) {
            $date = date_create();
            $dateStr = date_format(date_add($date, date_interval_create_from_date_string("$i days")), 'Y-m-d');
            $this->matchDateSchedule($dateStr, true);
        }
    }

    /**
     * 比赛直播
     * 每分钟执行一次
     */
    private function spiderMatchLiveChange()
    {
//        $this->matchLiveChange();
        $this->matchPcLiveChange();
    }

    /**
     * 未爬取的比赛错误数据
     */
    private function spiderMatchError() {
        $matches = BasketMatch::query()->where("status", ">", "-1")
            ->where("time", "<", date_create("-4 hours"))
            ->orderBy("time", "desc")->take(20)->get();
        foreach ($matches as $match) {
            $match1 = $this->matchTechnicByMid($match->id);
            if (empty($match1) || ($match1->status > -1 && strtotime($match1->time) < strtotime('-1 days'))) {
                $id = $match->id;
                echo "delete match by id : $match->id".'<br>';
                $match->status = -99;
                $match->save();
//                $match->delete();

                $match = \App\Models\LiaoGouModels\BasketMatch::getMatchWith($id, 'win_id');
                if (isset($match)) {
//                    $match->delete();
                    $match->status = -99;
                    $match->save();
                }
            }
        }
    }

    /**
     * 两日内的盘口
     * 每5分钟执行一次
     */
    private function spiderHandicap2Days()
    {
        set_time_limit(0);
        $lastTime = time();
        foreach (range(1, 2) as $i) {
            $dateStr = date_format(date_create("+ $i days"), 'Y-m-d');
            $this->handicapDays($dateStr, 3); //SB
            $this->handicapDays($dateStr, 8); //bet365
            $this->handicapDays($dateStr, 14); //韦德
        }
        dump(time() - $lastTime);
    }

    /**
     * 每日盘口
     * 每5分钟执行一次
     */
    private function spiderHandicapDays()
    {
        set_time_limit(0);
        $lastTime = time();
        $this->handicapDays('', 3); //SB
        $this->handicapDays('', 8); //bet365
        $this->handicapDays('', 14); //韦德
        dump(time() - $lastTime);
    }

    /**
     * 盘口改变
     * 每分钟执行一次
     */
    private function spiderHandicapChange()
    {
        $this->handicapChange(1);
        //下面这些不需要了
//        $this->handicapChange(2);
//        $this->handicapChange(3);

//        $this->liveHandicapLiveChange();
    }

    /**
     * 传入的mid为win_basket_match的id
     * 手动根据比赛爬取盘口信息
     */
    private function spiderOddsByMid(Request $request) {
        $mid = $request->input("mid");
        if (!isset($mid)) {
            echo "mid is null <br>";
            return;
        }
        if ($request->has("type")) {
            $this->oddsWithMatchAndType($mid, $request->input("type", 1));
        } else {
            //只爬取亚盘和大小球的盘口数据
            $this->oddsWithMatchAndType($mid, 1);
            $this->oddsWithMatchAndType($mid, 2);
        }
    }

    /**
     * 填充赔率
     */
    private function spiderFillOddMatch(Request $request)
    {
        $matches = \App\Models\LiaoGouModels\BasketMatch::query()
            ->select('basket_matches.*')
            ->join('basket_leagues', function ($join){
                $join->on('basket_matches.lid', '=', 'basket_leagues.id')
                    ->where("basket_leagues.hot", "=", 1);
            })->where(function ($q){
                $q->where("basket_matches.is_odd", "=", 0)
                    ->orWhereNull("basket_matches.is_odd");
            })->where("basket_matches.status", "=", -1)
            ->orderBy('basket_matches.time', 'desc')
            ->take(50)
            ->get();
        foreach ($matches as $match) {
            echo $match->hname . ' VS ' . $match->aname . '<br>';
            $request->merge(['mid'=>$match->win_id]);
            $this->spiderOddsByMid($request);
            $match->is_odd = 1;
            $match->save();
        }

        if ($request->input('auto', 0) == 1) {
            echo "<script language=JavaScript>window.location.reload();</script>";
            exit;
        }
    }

    /**
     * 重置盘口填充的标识
     */
    private function resetMatchIsOdd(Request $request)
    {
        $lid = $request->input('lid');
        $count = $request->input('count', 1000);
        $startTime = $request->input('start', '2004-01-01');
        $endTime = $request->input('end', date('Y-m-d'));
        $matches = \App\Models\LiaoGouModels\BasketMatch::query()
            ->where('lid', $lid)
            ->whereBetween('time', [$startTime, $endTime])
            ->where('status', -1)
            ->where('is_odd', 1)
            ->orderBy('time', 'asc')
            ->take($count)
            ->get();
        foreach ($matches as $match) {
            $match->is_odd = 0;
            $match->save();
        }
    }

    /**
     * 专门用来删除冗余比赛表的多余数据的接口（包括matches_afters, odds_afters, baskets_afters）
     */
    private function deleteUselessAllAfters(Request $request) {
        $count = 0;
        if ($request->has("count")) {
            $count = $request->get("count", 100);
        }
        //matches_afters表
        BasketMatchesAfter::deleteUselessData($count);
        //odds_afters表
        BasketOddsAfter::deleteUselessData($count);
    }

    /**
     * 刷新当天的赛事积分
     * 每小时执行一次
     */
    private function spiderCurrentLeagueRank(Request $request)
    {
        $type = $request->input("type", -1);

        $keyHead = "basket_league_rank_" . $type;
        $lids = $this->getRecentLids($keyHead, 1, 5, 24, true);
        foreach ($lids as $lid) {
            $league = BasketLeague::find($lid);
            if ($league) {
                if ($league->type == 1) {
                    $request->merge(['lid'=>$lid]);
                    $this->leagueScore($request);
                } elseif ($league->type == 2) {
                    $request->merge(['lid'=>$lid]);
                    $this->cupLeagueScore($request);
                }
            }
        }
    }

    private function getRecentLids($keyHead, $leagueType = -1, $count = 10, $timeHours = 36, $isSub = false)
    {
        $date = date_create();
        $key = $keyHead . "_" . date_format($date, "Y-m-d");
        $value = Redis::get($key);
        $redisLis = array();
        if (isset($value)) {
            $redisLis = array_merge($redisLis, json_decode($value));
        }
        $lids = array();
        if (count($redisLis) <= 0) {
            $query = BasketMatch::query()->select("basket_matches.*");
            if ($leagueType > 0) {
                $query->join("basket_leagues", function ($join) use($leagueType) {
                    $join->on("basket_leagues.id", "=", "basket_matches.lid")
                        ->where("basket_leagues.type", $leagueType);
                });
            }
            $startDate = date_format($isSub ? date_sub(date_create(), date_interval_create_from_date_string((2 + $timeHours) . ' hour')) : date_add($date, date_interval_create_from_date_string('2 hour')), 'Y-m-d H:i:s');
            $endDate = date_format($isSub ? date_sub(date_create(), date_interval_create_from_date_string('2 hour')) : date_add($date, date_interval_create_from_date_string((2 + $timeHours) . ' hour')), 'Y-m-d H:i:s');

            dump($startDate, $endDate);

            $query->where("time", ">=", $startDate)
                ->where("time", "<=", $endDate)
                ->orderby('time', $isSub ? 'desc' : "asc");
            $matches = $query->get()->unique("lid");
            foreach ($matches as $match) {
                array_push($lids, $match->lid);
            }
            Redis::setEx($key, 24 * 60 * 60, json_encode($lids));
            $redisLis = array_merge($redisLis, $lids);
        }
        $lids = array_slice($redisLis, 0, $count);

        dump('lids count = ' . count($redisLis));

        //删除redis中将要爬取的lid
        $redisLis = array_slice($redisLis, count($lids));
        Redis::set($key, json_encode($redisLis));

        return $lids;
    }
}
