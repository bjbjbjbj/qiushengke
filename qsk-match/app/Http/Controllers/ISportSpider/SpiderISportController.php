<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2017/9/19
 * Time: 下午3:47
 */

namespace App\Http\Controllers\ISportSpider;

use App\Http\Controllers\WinSpider\SpiderTools;
use App\Models\ISportModels\LotteryFootballMatch;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class SpiderISportController extends Controller
{
    use SpiderTools, ISportFootball, LotteryTradition;

    const isport_host_url = "http://www.sporttery.cn";

    const k_type_wdl = "had"; //胜平负
    const k_type_asia_wdl = "hhad"; //让分胜平负
    const k_type_score = "crs"; //比分
    const k_type_total_goal = "ttg"; //总进球输
    const k_type_half_wdl = "hafu"; //半全场

    public function index($action, Request $request)
    {
        if (method_exists($this, $action)) {
            $this->$action($request);
        } else {
            echo "Error: Not Found action 'SpiderISportController->$action()'";
        }
    }

    /**
     * @return array userData
     */
    private function getUserData()
    {
        return array('username' => '11000000', 'password' => 'test_passwd');
    }

    //根据时间重爬竞彩（比赛+盘口）信息
    private function spiderMatchAndOddsByDate(Request $request) {
        $dateStr = $request->input('date', date_format(date_create(), "Y-m-d"));
        $date = date_create($dateStr);
        $startTime = date_format(date_time_set($date, 0, 0, 0), "Y-m-d H:i:s");
        dump($startTime);
        $endTime = date_format(date_time_set($date, 23, 59, 59), "Y-m-d H:i:s");
        dump($endTime);
        $matches = LotteryFootballMatch::query()
            ->where("time", ">=", $startTime)
            ->where("time", "<", $endTime)
            ->orderBy("time", "desc")->get();
        $count = $request->input('count', 1);
        if (count($matches) <=0) {
            echo "spider spiderOddsByDate by date = $dateStr"." match count = 0, spider count = $count </br>";

            if ($count > 1) return;

            $this->spiderFootballMatchesByDate($request);
            $request->merge(['count' => ($count + 1)]);
            $this->spiderMatchAndOddsByDate($request);
            return;
        }
        foreach ($matches as $match) {
            $i_id = $match->i_id;
            $request->merge(['mid' => $i_id, 'reset' => true]);
            $this->spiderFootballOdds($request);
            echo "spider spiderOddsByDate by date = $dateStr".", i_id = $i_id"."</br>";
        }

        if ($request->input('auto')) {
            $nextDate = date('Y-m-d', strtotime('-1 day', strtotime($dateStr)));
            dump($date, $nextDate);
            echo "<script language=JavaScript> location.replace(location.href.replace('date=$dateStr', 'date=$nextDate'));</script>";
            exit;
        }
    }

    //早晚10:00各一次执行
    private function spiderTraditionOnce(Request $request) {
        $this->spiderTraditionIssueForNext($request);
        $this->spiderTraditionIssueForLast($request);
    }

    private function getItem($data, $key, $isJson = false, $default = null) {
        if ($isJson) {
            if (isset($data->$key)) {
                return $data->$key;
            }
        } else {
            if (array_has($data, $key)) {
                return $data[$key];
            }
        }
        return $default;
    }
}