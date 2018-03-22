<?php

namespace App\Http\Controllers\WinSpider\basket;

use App\Http\Controllers\Utils\DateUtils;
use App\Http\Controllers\WinSpider\SpiderTools;
use App\Models\LiaoGouModels\BasketMatchesAfter;
use App\Models\LiaoGouModels\BasketOddsAfter;
use App\Models\WinModels\BasketMatch;
use Hamcrest\BaseMatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Input;

class SpiderBasketController extends Controller
{
    use SpiderTools, SpiderBasketOnce, SpiderBasketSchedule,
        SpiderBasketOdds, SpiderBasketLeague,SpiderBasketMatch,SpiderBasketTeam;

    const SPIDER_ERROR_LIMIT = 4;

    public function index($action, Request $request)
    {
        if (method_exists($this, $action)) {
            $this->$action($request);
        } else {
            echo "Error: Not Found action 'SpiderController->$action()'";
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
     * 比赛直播，比分刷新（添加到文件），每分钟执行一次
     */
    private function spiderMatchLiveChangeToFile()
    {
        foreach (range(0, 50) as $key) {
            $this->matchLiveChangeToFile();
            sleep(1);
        }
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

        $this->liveHandicapLiveChange();
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
}
