<?php
namespace App\Http\Controllers\Statistic\Schedule;

use App\Http\Controllers\Statistic\Change\MatchDataChangeTool;
use App\Http\Controllers\Statistic\MatchLiveTool;
use App\Http\Controllers\Statistic\OddCalculateTool;
use App\Http\Controllers\Statistic\StatisticFileTool;
use App\Http\Controllers\Statistic\SpiderTools;
use App\Models\LiaoGouModels\BasketMatch;
use App\Models\LiaoGouModels\BasketTeam;
use App\Models\LiaoGouModels\League;
use App\Models\LiaoGouModels\MatchLive;
use App\Models\LiaoGouModels\Odd;
use Illuminate\Support\Facades\Response;


/**
 * Created by PhpStorm.
 * User: ricky
 * Date: 2018/2/25 0025
 * Time: 16:51
 */
class ScheduleDataController
{
    use SpiderTools, MatchCommonTool, MatchDataChangeTool;

    public function index($date, $sport, $type) {
        if (!$this->isSport($sport)) {
            return null;
        }
        $result = array();
        $date = date('Y-m-d', strtotime($date));
        if ($sport == MatchLive::kSportBasketball) {
            $result = $this->basketDateSchedule($date, $type);
        } elseif ($sport == MatchLive::kSportFootball) {
            $result = $this->footballDateSchedule($date, $type);
        }
        return Response::json($result);
    }

    public function onMatchesStatistic($sport) {
        set_time_limit(0);
        $lastTime = time();
        foreach (range(-1, 1) as $index) {
            $date = date('Y-m-d', strtotime("$index day"));
            $this->onMatchesStaticByDate($sport, $date);
        }
        dump((time()-$lastTime));
    }

    public function onMatchesStaticByDate($sport, $date = null) {
        if (!isset($date) || strlen($date) <= 0) {
            $date = date('Y-m-d');
        }
        switch ($sport) {
            case MatchLive::kSportBasketball:
                $this->basketDateSchedule($date);
                break;
            case MatchLive::kSportFootball:
            default:
                $this->footballDateSchedule($date);
                break;
        }
    }

    //根据时间获取足球 列表数据
    private function footballDateSchedule($date = NULL, $type = '')
    {
        if ($date == NULL) {
            $date = date("Y-m-d");
        }
        $isToday = $date == date('Y-m-d');

        $query = $this->getMatchCommonQuery($date);
        $matches = $this->onCommonOrderBy($query)->get();

        $allLeagues = array();
        $allMatches = array();
        $firstLeagues = array();
        $firstMatches = array();
        $lotteryLeagues = array();
        $lotteryMatches = array();
        $footLeagues = array();
        $footMatches = array();
        $bjLeagues = array();
        $bjMatches = array();

        $asiaUpOdds = array();
        $asiaDownOdds = array();
        $asiaMiddleOdds = array();
        $ouOdds = array();
        $oddFilter = array();

        //直播入口数量
        $liveCounts = MatchLiveTool::getMatchLiveCountByDate($this->convertDateToDateRange($date));
        $pcLiveCounts = MatchLiveTool::getMatchLiveCountByDate($this->convertDateToDateRange($date), true);

        foreach ($matches as $match) {
            $match = OddCalculateTool::formatOddData($match, [1,2,3,4]);

            $win_id = $match->win_id;
            $mid = $match->mid;
            $status = $match->status;
            $isHasLineup = $match->has_lineup > 0;

            $match->makeHidden('win_id', 'has_lineup');

            $match->current_time = $match->getCurMatchTime(true);
            $match->time = strtotime($match->time);
            if (isset($match->timehalf)) {
                $match->timehalf = strtotime($match->timehalf);
            } else {
                $match->timehalf = $match->time;
            }

            $match->live = isset($liveCounts[$mid]) ? $liveCounts[$mid] : 0;
            $match->live2 = isset($liveCounts[$mid]) ? $liveCounts[$mid] : 0;
            $match->pc_live = isset($pcLiveCounts[$mid]) ? $pcLiveCounts[$mid] : 0;
            $match->pc_live2 = isset($pcLiveCounts[$mid]) ? $pcLiveCounts[$mid] : 0;

            StatisticFileTool::putFileToTerminal($match, MatchLive::kSportFootball, $match->mid, 'match');

            $leagueName = $match['league'];
            $isFive = in_array($match['lid'], League::getFiveLids());
            $leagueFilter = ['py' => $this->getFirstCharter($leagueName), 'name' => $leagueName,
                'id' => $match['lid'], 'genre'=>$match->genre, 'isFive'=>$isFive, 'count'=>1];
            if (!array_key_exists($leagueName, $allLeagues)) {
                $allLeagues[$leagueName] = $leagueFilter;
            } else {
                $allLeagues[$leagueName]['count'] += 1;
            }
            $allMatches[] = $match;

            //============赔率筛选相关======================
            //让球
            $asiaOddArray = $this->getMatchOdds($match->asiamiddle2, Odd::k_odd_type_asian);
            $typeCn = $asiaOddArray['typeCn'];
            $sort = $asiaOddArray['sort'];
            if (strlen($typeCn) > 0) {
                if (str_contains($typeCn, "受")) {
                    if (isset($asiaDownOdds[$sort])) {
                        $asiaDownOdds[$sort]['count'] += 1;
                    } else {
                        $asiaDownOdds[$sort] = $asiaOddArray;
                        $asiaDownOdds[$sort]['count'] = 1;
                    }
                } elseif($typeCn == '未开盘' || $typeCn == '平手') {
                    if (isset($asiaMiddleOdds[$sort])) {
                        $asiaMiddleOdds[$sort]['count'] += 1;
                    } else {
                        $asiaMiddleOdds[$sort] = $asiaOddArray;
                        $asiaMiddleOdds[$sort]['count'] = 1;
                    }
                } else {
                    if (isset($asiaUpOdds[$sort])) {
                        $asiaUpOdds[$sort]['count'] += 1;
                    } else {
                        $asiaUpOdds[$sort] = $asiaOddArray;
                        $asiaUpOdds[$sort]['count'] = 1;
                    }
                }
            }

            //大小球
            $ouOddArray = $this->getMatchOdds($match->goalmiddle2, Odd::k_odd_type_ou);
            $typeCn = $ouOddArray['typeCn'];
            $sort = $ouOddArray['sort'];
            if (strlen($typeCn) > 0) {
                if (isset($ouOdds[$sort])) {
                    $ouOdds[$sort]['count'] += 1;
                } else {
                    $ouOdds[$sort] = $ouOddArray;
                    $ouOdds[$sort]['count'] = 1;
                }
            }

            $asiaOdds = array();
            if (count($asiaUpOdds) > 0) {
                ksort($asiaUpOdds);
                $asiaOdds['up'] = $asiaUpOdds;
            }
            if (count($asiaDownOdds) > 0) {
                ksort($asiaDownOdds);
                $asiaOdds['down'] = $asiaDownOdds;
            }
            if (count($asiaMiddleOdds) > 0) {
                ksort($asiaMiddleOdds);
                $asiaOdds['middle'] = $asiaMiddleOdds;
            }
            $oddFilter['asiaOdds'] = $asiaOdds;
            ksort($ouOdds);
            $oddFilter['ouOdds'] = $ouOdds;

            //==================================

            if ($match->genre >> 1 & 1) {//一级
                if (!array_key_exists($leagueName, $firstLeagues)) {
                    $leagueFilter['count'] = 1;
                    $firstLeagues[$leagueName] = $leagueFilter;
                } else {
                    $firstLeagues[$leagueName]['count'] += 1;
                }
                $firstMatches[] = $match;
            }
            if ($match->genre >> 2 & 1) {//足彩
                if (!array_key_exists($leagueName, $footLeagues)) {
                    $leagueFilter['count'] = 1;
                    $footLeagues[$leagueName] = $leagueFilter;
                } else {
                    $footLeagues[$leagueName]['count'] += 1;
                }
                $footMatches[] = $match;
            }
            if ($match->genre >> 3 & 1) {//竞彩
                if (!array_key_exists($leagueName, $lotteryLeagues)) {
                    $leagueFilter['count'] = 1;
                    $lotteryLeagues[$leagueName] = $leagueFilter;
                } else {
                    $lotteryLeagues[$leagueName]['count'] += 1;
                }
                $lotteryMatches[] = $match;
            }
            if ($match->genre >> 4 & 1) {//北单
                if (!array_key_exists($leagueName, $bjLeagues)) {
                    $leagueFilter['count'] = 1;
                    $bjLeagues[$leagueName] = $leagueFilter;
                } else {
                    $bjLeagues[$leagueName]['count'] += 1;
                }
                $bjMatches[] = $match;
            }
        }

        usort($allLeagues, array($this, "py_sort"));
        usort($firstLeagues, array($this, "py_sort"));
        usort($footLeagues, array($this, "py_sort"));
        usort($lotteryLeagues, array($this, "py_sort"));
        usort($bjLeagues, array($this, "py_sort"));

        //竞彩的比赛排序单独处理
        usort($lotteryMatches, array($this, "betting_num_sort"));

        $filterData = [
            ['type' => 'all', 'name' => '全部', 'data' => $allLeagues],
            ['type' => 'lottery', 'name' => '竞彩', 'data' => $lotteryLeagues],
            ['type' => 'first', 'name' => '一级', 'data' => $firstLeagues],
            ['type' => 'foot', 'name' => '足彩', 'data' => $footLeagues],
            ['type' => 'bj', 'name' => '北单', 'data' => $bjLeagues],
        ];

        StatisticFileTool::putFileToSchedule(['filter' => $filterData, 'odd'=>$oddFilter, 'matches' => $allMatches], MatchLive::kSportFootball, 'all', $date);
        StatisticFileTool::putFileToSchedule(['filter' => $filterData, 'matches' => $lotteryMatches], MatchLive::kSportFootball, 'lottery', $date);
        StatisticFileTool::putFileToSchedule(['filter' => $filterData, 'matches' => $firstMatches], MatchLive::kSportFootball, 'first', $date);
        StatisticFileTool::putFileToSchedule(['filter' => $filterData, 'matches' => $footMatches], MatchLive::kSportFootball, 'foot', $date);
        StatisticFileTool::putFileToSchedule(['filter' => $filterData, 'matches' => $bjMatches], MatchLive::kSportFootball, 'bj', $date);

        if (strlen($type) > 0) {
            switch ($type) {
                case "lottery":
                    $result = ['filter' => $filterData, 'matches' => $lotteryMatches];
                    break;
                case "first":
                    $result = ['filter' => $filterData, 'matches' => $firstMatches];
                    break;
                case "foot":
                    $result = ['filter' => $filterData, 'matches' => $footMatches];
                    break;
                case "bj'":
                    $result = ['filter' => $filterData, 'matches' => $bjMatches];
                    break;
                case "all":
                default:
                    $result = ['filter' => $filterData, 'odd'=>$oddFilter, 'matches' => $allMatches];
                    break;
            }
            return $result;
        }
    }

    //根据时间获取篮球 列表数据
    private function basketDateSchedule($date = NULL, $type = '')
    {
        if ($date == NULL) {
            $date = date("Y-m-d");
        }
        $isToday = $date == date('Y-m-d');

        //比赛
        $query = $this->getBasketCommonQuery($date);
        $matches = $this->onCommonOrderBy($query)->get();

        $allLeagues = array(); $allMatches = array();
        $firstLeagues = array(); $firstMatches = array();
        $lotteryLeagues = array(); $lotteryMatches = array();
        $nbaLeagues = array(); $nbaMatches = array();

        //直播入口数量
        $liveCounts = MatchLiveTool::getMatchLiveCountByDate($this->convertDateToDateRange($date, MatchLive::kSportBasketball), false, MatchLive::kSportBasketball);
        $pcLiveCounts = MatchLiveTool::getMatchLiveCountByDate($this->convertDateToDateRange($date, MatchLive::kSportBasketball), true, MatchLive::kSportBasketball);

        foreach ($matches as $match) {
            $match = OddCalculateTool::formatOddData($match);

            $win_id = $match->win_id;
            $mid = $match->mid;
            $status = $match->status;
            $hot = $match->hot;

            $match->makeHidden(['win_id', 'hot']);

            $match->live_time_str = BasketMatch::getMatchCurrentTime($match->live_time_str, $match->status, $match->system == 1, true);
            $match->time = strtotime($match->time);
            if (isset($match->betting_num)) {
                $match->betting_num = str_replace(" ", "", $match->betting_num);
            }

            $match->hicon = BasketTeam::getIcon($match->hicon);
            $match->aicon = BasketTeam::getIcon($match->aicon);

            $match->h_ot = self::convertOtScore($match->h_ot);
            $match->a_ot = self::convertOtScore($match->a_ot);

            $match->live = isset($liveCounts[$mid]) ? $liveCounts[$mid] : 0;
            $match->live2 = isset($liveCounts[$mid]) ? $liveCounts[$mid] : 0;
            $match->pc_live = isset($pcLiveCounts[$mid]) ? $pcLiveCounts[$mid] : 0;
            $match->pc_live2 = isset($pcLiveCounts[$mid]) ? $pcLiveCounts[$mid] : 0;

            StatisticFileTool::putFileToTerminal($match, MatchLive::kSportBasketball, $match->mid, 'match');

            $leagueName = $match['league'];
            $leagueFilter = ['py'=>$this->getFirstCharter($leagueName), 'name'=>$leagueName,
                'id'=>$match['lid'], 'count'=>1];
            if (!array_key_exists($leagueName, $allLeagues)) {
                $allLeagues[$leagueName] = $leagueFilter;
            } else {
                $allLeagues[$leagueName]['count'] += 1;
            }
            $allMatches[] = $match;

            if ($hot == 1) {//一级
                if (!array_key_exists($leagueName, $firstLeagues)) {
                    $leagueFilter['count'] = 1;
                    $firstLeagues[$leagueName] = $leagueFilter;
                } else {
                    $firstLeagues[$leagueName]['count'] += 1;
                }
                $firstMatches[] = $match;
            }
            if (isset($match->betting_num)) {//竞彩
                if (!array_key_exists($leagueName, $lotteryLeagues)) {
                    $leagueFilter['count'] = 1;
                    $lotteryLeagues[$leagueName] = $leagueFilter;
                } else {
                    $lotteryLeagues[$leagueName]['count'] += 1;
                }
                $lotteryMatches[] = $match;
            }
            if ($match->lid == 1) {//nba
                if (!array_key_exists($leagueName, $nbaLeagues)) {
                    $leagueFilter['count'] = 1;
                    $nbaLeagues[$leagueName] = $leagueFilter;
                } else {
                    $nbaLeagues[$leagueName]['count'] += 1;
                }
                $nbaMatches[] = $match;
            }
        }

        usort($allLeagues, array($this, "py_sort"));
        usort($firstLeagues, array($this, "py_sort"));
        usort($lotteryLeagues, array($this, "py_sort"));
        usort($nbaLeagues, array($this, "py_sort"));

        //竞彩的比赛排序单独处理
        usort($lotteryMatches, array($this, "betting_num_sort"));

        $filterData = [
            ['type'=>'all', 'name'=>'全部', 'data'=>$allLeagues],
            ['type'=>'first', 'name'=>'一级', 'data'=>$firstLeagues],
            ['type'=>'lottery', 'name'=>'竞彩', 'data'=>$lotteryLeagues],
            ['type'=>'nba', 'name'=>'NBA', 'data'=>$nbaLeagues],
        ];

        if ($isToday) {
            $leagueMatches = $allMatches;
            usort($leagueMatches, function ($a, $b){
                if ($a['hot'] == $b['hot']) {
                    if ($a['lid'] == $b['lid']) {
                        return ($a['time'] > $b['time']) ? 1 : -1;
                    } else {
                        return ($a['lid'] > $b['lid']) ? 1 : -1;
                    }
                } else {
                    return ($a['hot'] < $b['hot']) ? 1 : -1;
                }
            });

            StatisticFileTool::putFileToSchedule(['filter'=>$filterData, 'matches'=>$allMatches, 'l_matches'=>$leagueMatches], MatchLive::kSportBasketball, 'all', $date);
        } else {
            StatisticFileTool::putFileToSchedule(['filter'=>$filterData, 'matches'=>$allMatches], MatchLive::kSportBasketball, 'all', $date);
        }
        StatisticFileTool::putFileToSchedule(['filter'=>$filterData, 'matches'=>$lotteryMatches], MatchLive::kSportBasketball, 'lottery', $date);
        StatisticFileTool::putFileToSchedule(['filter'=>$filterData, 'matches'=>$firstMatches], MatchLive::kSportBasketball, 'first', $date);
        StatisticFileTool::putFileToSchedule(['filter'=>$filterData, 'matches'=>$nbaMatches], MatchLive::kSportBasketball, 'nba', $date);

        if (strlen($type) > 0) {
            switch ($type) {
                case "lottery":
                    $result = ['filter' => $filterData, 'matches' => $lotteryMatches];
                    break;
                case "first":
                    $result = ['filter' => $filterData, 'matches' => $firstMatches];
                    break;
                case "nba":
                    $result = ['filter' => $filterData, 'matches' => $nbaMatches];
                    break;
                case "all":
                default:
                    $result = ['filter' => $filterData, 'matches' => $allMatches];
                    break;
            }
            return $result;
        }
    }
}