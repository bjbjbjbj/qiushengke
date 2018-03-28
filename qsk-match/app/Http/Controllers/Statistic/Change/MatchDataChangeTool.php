<?php
/**
 * Created by PhpStorm.
 * User: ricky
 * Date: 2018/2/26 0026
 * Time: 17:30
 */

namespace App\Http\Controllers\Statistic\Change;


use App\Http\Controllers\Statistic\StatisticFileTool;
use App\Models\LiaoGouModels\BasketMatchesAfter;
use App\Models\LiaoGouModels\MatchLive;
use App\Models\LiaoGouModels\MatchLineup;

trait MatchDataChangeTool
{
    public function matchDataStatic($sport, $win_mid, $lg_mid) {
        switch ($sport) {
            case MatchLive::kSportBasketball:
                $this->basketMatchData($win_mid, $lg_mid);
                break;
            case MatchLive::kSportFootball:
            default:
                $this->footballMatchData($win_mid, $lg_mid, [1,2]);
                break;
        }
    }
    //=================足球相关=====================

    public static function dataPercent($acount, $bcount) {
        if ($acount + $bcount <= 0) {
            return 0;
        }
        return number_format($acount / ($acount + $bcount), 2);
    }

    /**
     * 更新比赛数据,例如事件等
     */
    public function footballMatchData($win_id, $lg_mid, $filterArray = [2])
    {
        $url = "http://txt.win007.com/phone/airlive/cn/" . substr($win_id, 0, 1) . "/" . substr($win_id, 1, 2) . "/$win_id.htm";
//        echo $url."<br>";
        $str = $this->spiderTextFromUrl($url);
        if ($str) {
            $jm = json_decode($str);
            if ($jm) {
                if (in_array(1, $filterArray)) {
                    self::saveFootballLineup($lg_mid, $jm->Lineup);
//                    echo "lg_mid = $lg_mid has fill lineup data! <br>";
                }
                if (in_array(2, $filterArray)) {
                    //统计数据
                    $techData = array();
                    foreach ($jm->listItemsTech as $item) {
                        $techItem = array();
                        $techItem['name'] = $item->Name;
                        $techItem['h'] = $item->HomeData;
                        $techItem['a'] = $item->AwayData;
                        $techItem['h_p'] = self::dataPercent(intval($item->HomeData), intval($item->AwayData));
                        $techItem['a_p'] = self::dataPercent(intval($item->AwayData), intval($item->HomeData));

                        $techData[] = $techItem;
                    }

                    //比赛事件
                    $events = array();
                    foreach ($jm->listItems as $item) {
                        $event = array();
                        $event['is_home'] = $item->isHome;
                        $event['kind'] = $item->Kind;
                        $event['happen_time'] = $item->happenTime;
                        $event['player_name_j'] = $item->PlayerNameJ;
                        $event['player_name_j2'] = $item->PlayerName2J;

                        $events[] = $event;
                    }
                    $eventData['events'] = $events;
                    $eventData['last_event_time'] = isset($event) ? $event['happen_time'] : '';//最后的事件时间

                    $techJson['tech'] = $techData;
                    $techJson['event'] = $eventData;
                    StatisticFileTool::putFileToTerminal($techJson, MatchLive::kSportFootball, $lg_mid, 'tech');

//                    echo "lg_mid = $lg_mid has fill event and statistic data! <br>";
                }
            }
        }
    }

    /**
     * 阵容
     */
    public static function saveFootballLineup($lg_mid, $lineupJson){
        $lineup = MatchLineup::query()->where('id', '=', $lg_mid)->first();
        $lineupArray = array();
        $lineupBackArray = array();
        if (isset($lineupJson->Home)) {
            if (isset($lineup) && isset($lineup->h_lineup)) {
                $first = explode(',', $lineup->h_first);
                foreach ($lineupJson->Home as $homeJson) {
                    $num = $homeJson->Num;
                    $name = $homeJson->Name;
                    $isFirst = in_array($num, $first);
                    $lineupArray[] = array('num' => $num, 'name' => $name, 'first' => $isFirst ? 1 : 0);
                }
                foreach ($lineupJson->Home_bak as $backupJson) {
                    $num = $backupJson->Num;
                    $name = $backupJson->Name;
                    $lineupBackArray[] = array('num' => $num, 'name' => $name);
                }
            }
        }
        $result['home']['first'] = $lineupArray;
        $result['home']['back'] = $lineupBackArray;
        $result['home']['h_first'] = empty($lineup->h_first) ? [] : explode(',', $lineup->h_first);

        $lineupArray = array();
        $lineupBackArray = array();
        if (isset($lineupJson->Guest)) {
            if (isset($lineup) && isset($lineup->a_lineup)) {
                $first = explode(',', $lineup->a_first);
                foreach ($lineupJson->Guest as $awayJson) {
                    $num = $awayJson->Num;
                    $name = $awayJson->Name;
                    $isFirst = in_array($num, $first);

                    $lineupArray[] = array('num' => $num, 'name' => $name, 'first' => $isFirst ? 1 : 0);
                }
                foreach ($lineupJson->Guest_bak as $backupJson) {
                    $num = $backupJson->Num;
                    $name = $backupJson->Name;
                    $lineupBackArray[] = array('num' => $num, 'name' => $name);
                }
            }
        }
        $result['away']['first'] = $lineupArray;
        $result['away']['back'] = $lineupBackArray;
        $result['away']['h_first'] = empty($lineup->a_first) ? [] : explode(',', $lineup->a_first);
        if (isset($lineup->h_lineup_percent))
            $result['h_lineup_per'] = $lineup->h_lineup_percent;
        if (isset($lineup->a_lineup_percent))
            $result['a_lineup_per'] = $lineup->a_lineup_percent;

        $isEmpty = count($result['home']['first']) == 0 && count($result['away']['first']);
        if (!$isEmpty) {
            StatisticFileTool::putFileToTerminal($result, MatchLive::kSportFootball, $lg_mid, 'lineup');
            echo "footballLineup: lg_mid = $lg_mid <br>";
        }
    }

    //======================篮球相关========================

    /**
     * 专门用来爬取篮球比赛统计数据
     * 5min执行一次
     */
    public function onBasketTechDataSpider() {
        $matches = BasketMatchesAfter::query()
            ->select("basket_matches_afters.*")
            ->join('basket_leagues', function ($join){
                $join->on('basket_leagues.id', 'basket_matches_afters.lid')
                    ->where('hot', 1);
            })
            ->where("basket_matches_afters.time", ">=", date('Y-m-d H:i:s', strtotime('-4 hours')))
            ->where("basket_matches_afters.time", "<", date('Y-m-d H:i:s'))
            ->where('basket_matches_afters.status', '>=', -1)
            ->orderByRaw('if(basket_matches_afters.status=50,2.5,basket_matches_afters.status) desc')
            ->orderBy('basket_matches_afters.time', 'asc')
            ->get();
        echo 'spiderBasketTechData ' . count($matches) . '</br>';
        foreach ($matches as $match) {
            $this->basketMatchData($match->win_id, $match->id);
        }
    }

    public function basketMatchData($win_id, $lg_mid) {
        $this->basketPlayerTech($win_id, $lg_mid);
        $this->basketTeamTech($win_id, $lg_mid);
    }

    //篮球球员统计项
    private function playerTechStatic($s) {
        $techItem = array();
        if(count(explode("^", $s)) >= 22) { //球员技术统计
            list(
                $pid, $name, $nameBig, $nameEn, $var1,
                $location, $min, $fg_in, $fg_all, $_3pt_in,
                $_3pt_all, $ft_int, $ft_all, $oreb, $dreb,
                $reb, $ast, $pf, $stl, $to,
                $blk, $pts) = explode("^", $s);
            $techItem['type'] = 'player';
            $techItem['name'] = $name;
            $techItem['location'] = $location;
            $techItem['min'] = $min;
            $techItem['fg'] = $fg_in.'-'.$fg_all;
            $techItem['3pt'] = $_3pt_in.'-'.$_3pt_all;
            $techItem['ft'] = $ft_int.'-'.$ft_all;
            $techItem['off'] = $oreb;
            $techItem['def'] = $dreb;
            $techItem['tot'] = $reb;
            $techItem['ast'] = $ast;
            $techItem['stl'] = $stl;
            $techItem['blk'] = $blk;
            $techItem['to'] = $to;
            $techItem['pf'] = $pf;
            $techItem['pts'] = $pts;
        } else if (count(explode("^", $s)) >= 16) {//球队整体技术统计
            list(
                $min, $fg_in, $fg_all, $_3pt_in, $_3pt_all,
                $ft_int, $ft_all, $oreb, $dreb, $reb,
                $ast, $pf, $stl, $to, $blk,
                $pts) = explode("^", $s);
            $techItem['type'] = 'total';
            $techItem['min'] = $min;
            $techItem['fg'] = $fg_in.'-'.$fg_all;
            $techItem['3pt'] = $_3pt_in.'-'.$_3pt_all;
            $techItem['ft'] = $ft_int.'-'.$ft_all;
            $techItem['off'] = $oreb;
            $techItem['def'] = $dreb;
            $techItem['tot'] = $reb;
            $techItem['ast'] = $ast;
            $techItem['stl'] = $stl;
            $techItem['blk'] = $blk;
            $techItem['to'] = $to;
            $techItem['pf'] = $pf;
            $techItem['pts'] = $pts;
        } else if (count(explode("^", $s)) >= 6) {//命中率技术统计
            list($var1, $fg_p, $_3pt_p, $ft_p, $var2, $var3) = explode("^", $s);
            $techItem['type'] = 'ratio';
            $techItem['fg_p'] = $fg_p;
            $techItem['3pt_p'] = $_3pt_p;
            $techItem['ft_p'] = $ft_p;;
        }
        return $techItem;
    }

    //篮球球员统计数据
    private function basketPlayerTech($win_id, $lg_mid) {
        $url = "http://txt.win007.com/jsdata/tech/".substr($win_id, 0, 1)."/".substr($win_id, 1, 2)."/$win_id.js";
        $str = $this->spiderTextFromUrl($url);
        if ($str) {
            $ss = explode("$", $str);
            if (count($ss) >= 3) {
                $homePlayerTechStrs = explode('!', $ss[1]);
                $homePlayerTechData = array();
                foreach ($homePlayerTechStrs as $s) {
                    $homePlayerTechData[] = $this->playerTechStatic($s);
                }
                $awayPlayerTechStrs = explode("!", $ss[2]);
                $awayPlayerTechData = array();
                foreach ($awayPlayerTechStrs as $s) {
                    $awayPlayerTechData[] = $this->playerTechStatic($s);
                }
                $playerData = ['home'=>$homePlayerTechData, 'away'=>$awayPlayerTechData];
                StatisticFileTool::putFileToTerminal($playerData, MatchLive::kSportBasketball, $lg_mid, 'player');

//                echo "lg_mid = $lg_mid has fill player tech data! <br>";
            }
        }
    }

    //篮球球队统计数据
    private function basketTeamTech($win_id, $lg_mid) {
        $url = "http://txt.win007.com/phone/LqTeamTechnic.aspx?id=$win_id";
        $str = $this->spiderTextFromUrl($url);
        if ($str) {
            $ss = explode("$$", $str);
            if (count($ss) >= 2) {
                $staticStrs = explode('!', $ss[1]);
                $teamTechData = array();
                foreach ($staticStrs as $s) {
                    if (count(explode("^", $s)) >= 3) {
                        $techItem = array();
                        list($home_data, $name, $away_data) = explode("^", $s);
                        $techItem['name'] = $name;
                        $techItem['h'] = $home_data;
                        $techItem['a'] = $away_data;
                        if (str_contains($home_data, '/') || str_contains($home_data, '%')) {
                            preg_match_all("/(?:\\()(.*)(?:\\))/i", $home_data, $result);
                            if (isset($result[1]) && isset($result[1][0])) {
                                $techItem['h_p'] = number_format(intval(str_replace("%", "", $result[1][0])) / 100,2);
                            } else {
                                $techItem['h_p'] = 0;
                            }
                            preg_match_all("/(?:\\()(.*)(?:\\))/i", $away_data, $result);
                            if (isset($result[1]) && isset($result[1][0])) {
                                $techItem['a_p'] = number_format(intval(str_replace("%", "", $result[1][0])) / 100,2);
                            } else {
                                $techItem['a_p'] = 0;
                            }
                        } else {
                            $techItem['h_p'] = self::dataPercent($home_data, $away_data);
                            $techItem['a_p'] = self::dataPercent($away_data, $home_data);
                        }

                        $teamTechData[] = $techItem;
                    }
                }
                if (count($teamTechData) > 0) {
                    StatisticFileTool::putFileToTerminal($teamTechData, MatchLive::kSportBasketball, $lg_mid, 'tech');
//                    echo "lg_mid = $lg_mid has fill team tech data! <br>";
                }
            }
        }
    }
}