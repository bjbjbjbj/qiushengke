<?php
namespace App\Http\Controllers\Kanbisai;
use App\Models\LiaoGouModels\BasketLeague;
use App\Models\LiaoGouModels\BasketMatchesAfter;
use App\Models\LiaoGouModels\BasketTeam;
use App\Models\LiaoGouModels\League;
use App\Models\LiaoGouModels\LiaogouAlias;
use App\Models\LiaoGouModels\LiveAlias;
use App\Models\LiaoGouModels\MatchesAfter;
use App\Models\LiaoGouModels\MatchLive;
use App\Models\LiaoGouModels\MatchLiveChannel;
use App\Models\LiaoGouModels\Team;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use QL\QueryList;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/29
 * Time: 16:59
 */
class SpiderController extends Controller
{
    public function index($action, Request $request)
    {
        if (method_exists($this, $action)) {
            $this->$action($request);
        } else {
            echo "Error: Not Found action 'SpiderISportController->$action()'";
        }
    }

    /**
     * 抓取低调看的多线路比赛。
     * @param Request $request
     */
    public function zhiBo(Request $request) {
        $host = 'http://www.kanbisai.tv/';
        $ql = QueryList::getInstance()->get('http://www.kanbisai.tv/');

        $matches = $ql->find('div.list');
        $as = $matches->find('a.schedule');

        $leagues = $as->find('span.game-type')->texts();
        $urls = $as->attrs("href");
        $h_names = $as->find('div.team-left span')->texts();
        $a_names = $as->find('div.team-right span')->texts();
        $times = $as->find('div.time')->texts();

        $year = date('Y');
        $controller = new \App\Http\Controllers\Sportstream365\SpiderController();
        foreach ($leagues as $index=>$league) {
            if (preg_match('/^节目$/', $league)) {
                continue;
            }
            $league = str_replace('常规赛', '', $league);
            $h_name = $h_names[$index];
            $a_name = $a_names[$index];
            $time = $times[$index];
            $url = $urls[$index];
            if (empty($time) || empty($h_name) || empty($a_name) || empty($url)) {
                continue;
            }
            $start_date = $year . '-' . $time . ':00';
            if (strtotime($start_date) < strtotime('-3 hours')) {
                continue;
            }
            $matches = $this->findChannel($url);
            if (count($matches) == 0) {
                continue;
            }

            $content = '看比赛：' . $league . ' ' . $h_name . ' VS ' . $a_name . '（' . $time . '）';

            //是否篮球
            $basket = BasketLeague::hasLeague($league);
            if ($basket) {//是篮球赛事
                $sport = MatchLive::kSportBasketball;
            } else {
                $football = League::hasLeague($league);
                if (isset($football) && $football) {
                    $sport = MatchLive::kSportFootball;
                } else {
                    continue;
                }
            }

            $lgTeam1 = $controller->saveAlias($h_name, $content, $sport, LiaogouAlias::kFromKBS, LiveAlias::kFromKBS);
            $lgTeam2 = $controller->saveAlias($a_name, $content, $sport, LiaogouAlias::kFromKBS, LiveAlias::kFromKBS);

            if (isset($lgTeam1) || isset($lgTeam2)) {//
                if ($sport == LiaogouAlias::kSportTypeFootball) {
                    $matchQuery = MatchesAfter::query();
                } else {
                    $matchQuery = BasketMatchesAfter::query();
                }

                if (isset($lgTeam1)) {
                    $matchQuery->where(function ($orQuery) use ($lgTeam1) {
                        $orQuery->where('hid', $lgTeam1->lg_id);
                        $orQuery->orWhere('aid', $lgTeam1->lg_id);
                    });
                } else if (isset($lgTeam2)) {
                    $matchQuery->where(function ($orQuery) use ($lgTeam2) {
                        $orQuery->where('hid', $lgTeam2->lg_id);
                        $orQuery->orWhere('aid', $lgTeam2->lg_id);
                    });
                }
                $matchQuery->where('time', '>', date('Y-m-d', strtotime('-2 hour')));
                $matchQuery->where('time', '<', date('Y-m-d', strtotime('+1 days')) . ' 10:00:00');
                $lgMatch = $matchQuery->first();
                if (isset($lgMatch)) {
                    foreach ($matches as $match) {
                        MatchLiveChannel::saveSpiderChannel($lgMatch->id, $sport, MatchLiveChannel::kTypeKBS, $match, 100, MatchLiveChannel::kPlatformPC, MatchLiveChannel::kPlayerIFrame, "");
                    }
                }
            }
            echo $content . $url . '<br/>';
        }

    }


    public function findChannel($url) {
        $array = [];
        try {
            $ql = QueryList::getInstance()->get($url);
            $ps = $ql->find('div.signal p[onclick]');
            $clicks = $ps->attrs('onclick');
            if (isset($clicks) && count($clicks) > 0) {
                foreach ($clicks as $click) {
                    if (preg_match('/\'wx\'/', $click)) {
                        continue;
                    }
                    preg_match('/\'.*\',\'.*\'/', $click, $match);
                    if (isset($match) && count($match) > 0) {
                        $array[] = str_replace("'", '', $match[0]);
                    }
                }
            }
        } catch (\Exception $e) {

        }
        return $array;
    }

}