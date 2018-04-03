<?php
namespace App\Http\Controllers\Statistic\Schedule;

use App\Models\LiaoGouModels\BasketMatch;
use App\Models\LiaoGouModels\BasketMatchesAfter;
use App\Models\LiaoGouModels\BasketOdd;
use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\MatchesAfter;
use App\Models\LiaoGouModels\MatchLive;
use App\Models\LiaoGouModels\Odd;

/**
 * Created by PhpStorm.
 * User: ricky
 * Date: 2018/1/3
 * Time: 11:24
 */
trait MatchCommonTool
{
    //===============通用部分=================

    function isSport($sport) {
        return in_array($sport, [MatchLive::kSportFootball, MatchLive::kSportBasketball]);
    }

    function getMatchType($sport = MatchLive::kSportFootball) {
        if ($sport == MatchLive::kSportBasketball) {
            return ['all', 'first', 'nba', 'lottery'];
        } else {
            return ['all', 'first', 'foot', 'lottery', 'bj'];
        }
    }

    function isMatchType($type, $sport = MatchLive::kSportFootball) {
        return in_array($type, $this->getMatchType($sport));
    }

    //php获取中文字符拼音首字母
    function getFirstCharter($str)
    {
        if (empty($str)) {
            return '';
        }
        $fchar = ord($str{0});
        if ($fchar >= ord('A') && $fchar <= ord('z')) return strtoupper($str{0});
        $s1 = iconv('UTF-8', 'gbk//ignore', $str);
        $s2 = iconv('gbk', 'UTF-8', $s1);
        $s = $s2 == $str ? $s1 : $str;
        $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
        if ($asc >= -20319 && $asc <= -20284) return 'A';
        if ($asc >= -20283 && $asc <= -19776) return 'B';
        if ($asc >= -19775 && $asc <= -19219) return 'C';
        if ($asc >= -19218 && $asc <= -18711) return 'D';
        if ($asc >= -18710 && $asc <= -18527) return 'E';
        if ($asc >= -18526 && $asc <= -18240) return 'F';
        if ($asc >= -18239 && $asc <= -17923) return 'G';
        if ($asc >= -17922 && $asc <= -17418) return 'H';
        if ($asc >= -17417 && $asc <= -16475) return 'J';
        if ($asc >= -16474 && $asc <= -16213) return 'K';
        if ($asc >= -16212 && $asc <= -15641) return 'L';
        if ($asc >= -15640 && $asc <= -15166) return 'M';
        if ($asc >= -15165 && $asc <= -14923) return 'N';
        if ($asc >= -14922 && $asc <= -14915) return 'O';
        if ($asc >= -14914 && $asc <= -14631) return 'P';
        if ($asc >= -14630 && $asc <= -14150) return 'Q';
        if ($asc >= -14149 && $asc <= -14091) return 'R';
        if ($asc >= -14090 && $asc <= -13319) return 'S';
        if ($asc >= -13318 && $asc <= -12839) return 'T';
        if ($asc >= -12838 && $asc <= -12557) return 'W';
        if ($asc >= -12556 && $asc <= -11848) return 'X';
        if ($asc >= -11847 && $asc <= -11056) return 'Y';
        if ($asc >= -11055 && $asc <= -10247) return 'Z';
        return null;
    }

    /**
     * 排序
     */
    function py_sort($a, $b)
    {
        if ($a['py']==$b['py']) {
            return $a['id']>$b['id']?1:-1;
        }
        return ($a['py']>$b['py'])?1:-1;
    }

    /**
     * betting_num的排序
     */
    function betting_num_sort($a, $b) {
        $weekArray = ['周日' => 0, '周一' => 1, '周二' => 2, '周三' => 3, '周四' => 4, '周五' => 5, '周六' => 6];

        $a_status = $a['status'];
        $b_status = $b['status'];
        if ($a_status > 0) $a_status = 1;
        if ($b_status > 0) $b_status = 1;

        if ($a_status == $b_status) {
            if (isset($a['betting_num']) && isset($b['betting_num'])) {
                $a_week = substr($a['betting_num'], 0, -3);
                $a_num = substr($a['betting_num'], -3);
                $b_week = substr($b['betting_num'], 0, -3);
                $b_num = substr($b['betting_num'], -3);

                $a_week_index = $weekArray[$a_week];
                $b_week_index = $weekArray[$b_week];
                if ($a_week_index == $b_week_index) {
                    return ($a_num > $b_num) ? 1 : -1;
                } else {
                    if (abs($a_week_index - $b_week_index) == 6) {
                        return $weekArray[$a_week] > $weekArray[$b_week] ? 1 : -1;
                    } else {
                        return $weekArray[$a_week] < $weekArray[$b_week] ? 1 : -1;
                    }
                }
            } else {
                return 0;
            }
        } else {
            return ($a_status < $b_status) ? 1 : -1;
        }
    }

    //根据传入的status信息 帅选比赛列表
    private function onMatchFilterByStatus($query, $status) {
        if ($status == -1) { //结果列表
            $query->where('m.status', '<=', -1);
        } else if ($status == 0) { //赛程列表
            $query->where('m.status', 0);
        }
        return $query;
    }

    //根据传入的时间（2018-01-01），返回状态信息
    private function convertDateToStatus($date) {
        if (isset($date) && strlen($date) > 0) {
            $nowDate = date('Y-m-d');
            if (strtotime($date) > strtotime($nowDate)) {
                $status = 0;
            } else if (strtotime($date) == strtotime($nowDate)){
                $status = 1;
            } else {
                $status = -1;
            }
        } else {
            $status = 100;
        }
        return $status;
    }

    //根据传入的时间（2018-01-01），返回 实际需要请求的时间范围
    private function convertDateToDateRange($date, $sport = MatchLive::kSportFootball) {
        $array = array();
        if (isset($date) && strlen($date) > 0) {
            $nowDate = date("Y-m-d");

            $hours = $sport == MatchLive::kSportBasketball ? 12 : 10;
            $hourStr = "$hours:00";

            if (strtotime($date) == strtotime($nowDate)){
                if (date('H') < $hours) {
                    $array['startDate'] = date('Y-m-d', strtotime('-1 day')). ' '.$hourStr;
                    $array['endDate'] = date('Y-m-d'). ' '.$hourStr;
                } else {
                    $array['startDate'] = date('Y-m-d'). ' '.$hourStr;
                    $array['endDate'] = date('Y-m-d', strtotime('+1 day')). ' '.$hourStr;
                    $array['startDate2'] = date('Y-m-d H:i:s', strtotime('-6 hour'));
                }
            } else {
                $array['startDate'] = date('Y-m-d', strtotime($date)). ' '.$hourStr;
                $array['endDate'] = date('Y-m-d', strtotime('+1 day', strtotime($date))). ' '.$hourStr;
            }
        }
        return $array;
    }

    //筛选比赛列表的 时间范围
    private function onMatchFilterByDate($query, $date = '', $sport = MatchLive::kSportFootball) {
        //时间相关
        if (isset($date) && strlen($date) > 0) {
            $dateArray = $this->convertDateToDateRange($date, $sport);
            $startDate = $dateArray['startDate'];
            $endDate = $dateArray['endDate'];

            if (strtotime($date) == strtotime(date('Y-m-d'))) {
                $startDate2 = isset($dateArray['startDate2']) ? $dateArray['startDate2'] : null;

                if (is_null($startDate2)) {
                    $startDate2 = date('Y-m-d H:i:s', strtotime('-4 hours'));
                }

                $query->where(function ($q) use ($startDate, $startDate2, $endDate){
                    if (strtotime($startDate2) < strtotime($startDate)) {
                        $q->where(function ($q2) use ($startDate2, $startDate) {
                            $q2->where('m.time', '>=', $startDate2)
                                ->where('m.time', '<', $startDate)
                                ->where('m.status', '>', 0);
                        })->orWhere(function ($q3) use ($startDate, $endDate){
                            $q3->where('m.time', '>=', $startDate)
                                ->where('m.time', '<', $endDate);
                        });
                    } else {
                        $q->where(function ($q2) use ($startDate2, $startDate) {
                            $q2->where('m.time', '>=', $startDate)
                                ->where('m.time', '<', $startDate2)
                                ->where('m.status', '<', 0);
                        })->orWhere(function ($q3) use ($startDate2, $endDate){
                            $q3->where('m.time', '>=', $startDate2)
                                ->where('m.time', '<', $endDate);
                        });
                    }
                });
            } else {
                $query->where("m.time", ">=", $startDate)
                    ->where("m.time", "<", $endDate);
            }
        }
        return $query;
    }

    private function onCommonOrderBy($query) {
        $query->orderByRaw('if(m.status=50,2.5,m.status) desc')
            ->orderBy('m.time', 'asc')
            ->orderBy('l.type', 'asc')
            ->orderBy('l.id', 'asc');

        return $query;
    }

    /**
     * 前端盘口显示
     * @return mixed|string
     */
    private function getHandicapCn($handicap, $default = "", $type = Odd::k_odd_type_asian, $sport = MatchLive::kSportFootball, $isHome = true)
    {
        if ($sport == MatchLive::kSportFootball) {
            if ($type == Odd::k_odd_type_asian) {
                return Odd::panKouText($handicap, !$isHome);
            } else if ($type == Odd::k_odd_type_ou) {//大小球
                if ($handicap * 100 % 100 == 0) {
                    return round($handicap);
                }
                $handicap = round($handicap, 2);
                if ($handicap * 100 % 50 == 0) {//尾数为0.5的直接返回
                    return $handicap;
                }
                $tempHandicap = round($handicap);//四舍五入
                $intHandicap = floor($handicap);//取整
                if ($tempHandicap == $intHandicap) {//比较 四舍五入 与 取整大小，尾数为 0.25 则为相同
                    return $intHandicap . '/' . $intHandicap . '.5';
                } else {//否则尾数为0.75
                    return $intHandicap . '.5/' . ($intHandicap + 1);
                }
            } else if ($type == Odd::k_odd_type_europe) {//竞彩
                if ($handicap > 0) {
                    return "+" . $handicap;
                } else if ($handicap == 0) {
                    return "不让球";
                } else {
                    return $handicap;
                }
            }
        } elseif ($sport == MatchLive::kSportBasketball) {
            if ($type == Odd::k_odd_type_asian) {
                return BasketOdd::panKouText($handicap, !$isHome);
            } else if ($type == Odd::k_odd_type_ou) {//大小球
                return (($handicap * 100 % 100 == 0) ? round($handicap) : round($handicap, 2));
            } else if ($type == Odd::k_odd_type_europe) {//竞彩
                if ($handicap > 0) {
                    return "+" . $handicap;
                } else if ($handicap == 0) {
                    return "不让分";
                } else {
                    return $handicap;
                }
            }
        }
        return $default;
    }

    private function getMatchOdds($handicap, $type, $sport = MatchLive::kSportFootball)
    {
        $typeCn = "";
        $typeValue = "";
        $sort = -1;
        if ($type == Odd::k_odd_type_asian) {
            if (!isset($handicap)) {
                $typeCn = "未开盘";
                $typeValue = "Odd_Asia_none";
            } else {
                if ($handicap > 3) {
                    $typeCn = "三球以上";
                    $sort = 13;
                    $typeValue = "Odd_Asia_$sort";
                } else if ($handicap < -3) {
                    $typeCn = "受三球以上";
                    $sort = 13;
                    $typeValue = "Odd_Asia_Negative_$sort";
                } else {
                    $typeCn = $this->getHandicapCn($handicap, '', $type, $sport);
                    $temp = $handicap > 0 ? "Odd_Asia" : "Odd_Asia_Negative";
                    $sort = (abs($handicap) * 4);
                    $typeValue = $temp . "_" . $sort;
                }
            }
        } else if ($type = Odd::k_odd_type_ou) {
            if (!isset($handicap)) {
                $typeCn = "未开盘";
                $typeValue = "Odd_Goal_none";
            } else {
                if ($handicap < 2) {
                    $typeCn = "2球以下";
                    $sort = 7;
                    $typeValue = "Odd_Goal_$sort";
                } else if ($handicap > 4) {
                    $typeCn = "4球以上";
                    $sort = 17;
                    $typeValue = "Odd_Goal_$sort";
                } else {
                    $typeCn = $this->getHandicapCn($handicap, '', $type, $sport) . "球";
                    $sort = (abs($handicap) * 4);
                    $typeValue = "Odd_Goal_" . $sort;
                }
            }
        }
        $matchOdds['sort'] = $sort;
        $matchOdds['typeCn'] = $typeCn;
        $matchOdds['typeValue'] = $typeValue;
        return $matchOdds;
    }

    //=====================足球部分=================

    //获取足球比赛数据的统一请求部分
    //获取足球比赛数据的统一请求部分
    private function getMatchCommonQuery($date = '') {
        $status = $this->convertDateToStatus($date);

        $query = $this->getBaseMatchCommonQuery($status);

        //状态相关
        $query = $this->onMatchFilterByStatus($query, $status);

        //时间相关
        $query = $this->onMatchFilterByDate($query, $date);

        return $query;
    }

    private function getBaseMatchCommonQuery($status = 100) {
        if ($status == 1) {
            $query = MatchesAfter::from('matches_afters as m');
        } else {
            $query = Match::from("matches as m");
        }

        $query->join('leagues as l', 'lid', '=', 'l.id')
            ->addSelect('m.id as mid', 'm.hid', 'm.aid', 'm.win_id as win_id', 'l.name as league', 'l.color as color');
        //暂时没有点击进去的,这里可以先无视teamid
//                ->whereNotNull("hid")
//                ->whereNotNull("aid")
        //性能问题,这里用join效率更高
        $query->leftjoin('odds as asia', function ($join) {
            $join->on('m.id', '=', 'asia.mid');
            $join->where('asia.type', '=', Odd::k_odd_type_asian);
            $join->where('asia.cid', '=', Odd::default_calculate_cid);
        })->leftjoin('odds as ou', function ($join) {
                $join->on('m.id', '=', 'ou.mid');
                $join->where('ou.type', '=', Odd::k_odd_type_europe);
                $join->where('ou.cid', '=', Odd::default_calculate_cid);
            })->leftjoin('odds as goal', function ($join) {
                $join->on('m.id', '=', 'goal.mid');
                $join->where('goal.type', '=', Odd::k_odd_type_ou);
                $join->where('goal.cid', '=', Odd::default_calculate_cid);
            })->leftjoin('odds as corner', function ($join) {
                $join->on('m.id', '=', 'corner.mid');
                $join->where('corner.type', '=', Odd::k_odd_type_corner);
                $join->where('corner.cid', '=', Odd::default_banker_id);
            })
            ->addSelect('asia.up1 as asiaup1', 'asia.middle1 as asiamiddle1', 'asia.down1 as asiadown1',
                'asia.up2 as asiaup2', 'asia.middle2 as asiamiddle2', 'asia.down2 as asiadown2',
                'ou.up1 as ouup1', 'ou.middle1 as oumiddle1', 'ou.down1 as oudown1',
                'ou.up2 as ouup2', 'ou.middle2 as oumiddle2', 'ou.down2 as oudown2',
                'goal.up1 as goalup1', 'goal.middle1 as goalmiddle1', 'goal.down1 as goaldown1',
                'goal.up2 as goalup2', 'goal.middle2 as goalmiddle2', 'goal.down2 as goaldown2',
                'corner.up1 as cornerup1', 'corner.middle1 as cornermiddle1', 'corner.down1 as cornerdown1',
                'corner.up2 as cornerup2', 'corner.middle2 as cornermiddle2', 'corner.down2 as cornerdown2');

        //角球和红黄牌统计数据
        $query->leftjoin('match_datas', 'm.id', '=', 'match_datas.id')
            ->addSelect('match_datas.h_corner as h_corner', 'match_datas.a_corner as a_corner')
            ->addSelect('match_datas.h_red as h_red', 'match_datas.a_red as a_red')
            ->addSelect('match_datas.h_yellow as h_yellow', 'match_datas.a_yellow as a_yellow');

        $query->leftJoin("teams as home", "m.hid", "home.id")
            ->leftJoin("teams as away", "m.aid", "away.id")
            ->addSelect('m.lid', 'm.status', 'm.genre','m.betting_num',
                'm.time', 'm.timehalf', 'm.hname','m.aname',
                'm.round', 'm.hrank', 'm.arank', 'm.has_lineup',
                'm.hscore','m.ascore', 'm.hscorehalf','m.ascorehalf')
            ->addSelect("home.icon as hicon", "away.icon as aicon");

        return $query;
    }

    //=====================篮球部分=================

    //获取篮球比赛数据的统一请求部分
    private function getBasketCommonQuery($date = '') {
        $status = $this->convertDateToStatus($date);

        $query = $this->getBaseBasketCommonQuery($status);

        //状态相关
        $query = $this->onMatchFilterByStatus($query, $status);

        //时间相关
        $query = $this->onMatchFilterByDate($query, $date, MatchLive::kSportBasketball);

        return $query;
    }

    private function getBaseBasketCommonQuery($status = 100) {
        if ($status == 1) {
            $query = BasketMatchesAfter::from('basket_matches_afters as m');
        } else {
            $query = BasketMatch::from("basket_matches as m");
        }

        $query->leftjoin('basket_leagues as l', 'lid', '=', 'l.id')
            ->addSelect('m.id as mid', 'm.hid', 'm.aid', 'm.win_id as win_id', 'l.name as league',
                'l.hot', 'l.system');
        //暂时没有点击进去的,这里可以先无视teamid
//                ->whereNotNull("hid")
//                ->whereNotNull("aid")
        //性能问题,这里用join效率更高
        $query->leftjoin('basket_odds as asia', function ($join) {
            $join->on('m.id', '=', 'asia.mid');
            $join->where('asia.type', '=', Odd::k_odd_type_asian);
            $join->where('asia.cid', '=', Odd::default_calculate_cid);
        })
            ->leftjoin('basket_odds as ou', function ($join) {
                $join->on('m.id', '=', 'ou.mid');
                $join->where('ou.type', '=', Odd::k_odd_type_europe);
                $join->where('ou.cid', '=', Odd::default_calculate_cid);
            })->leftjoin('basket_odds as goal', function ($join) {
                $join->on('m.id', '=', 'goal.mid');
                $join->where('goal.type', '=', Odd::k_odd_type_ou);
                $join->where('goal.cid', '=', Odd::default_calculate_cid);
            })
            ->addSelect('asia.up1 as asiaup1', 'asia.middle1 as asiamiddle1', 'asia.down1 as asiadown1',
                'asia.up2 as asiaup2', 'asia.middle2 as asiamiddle2', 'asia.down2 as asiadown2',
                'ou.up1 as ouup1', 'ou.middle1 as oumiddle1', 'ou.down1 as oudown1',
                'ou.up2 as ouup2', 'ou.middle2 as oumiddle2', 'ou.down2 as oudown2',
                'goal.up1 as goalup1', 'goal.middle1 as goalmiddle1', 'goal.down1 as goaldown1',
                'goal.up2 as goalup2', 'goal.middle2 as goalmiddle2', 'goal.down2 as goaldown2');

        $query->leftJoin("basket_teams as home", "m.hid", "home.id")
            ->leftJoin("basket_teams as away", "m.aid", "away.id")
            ->addSelect('m.lid', 'm.status', 'm.live_time_str','m.betting_num',
                'm.time', 'm.hname','m.aname','m.hscore','m.ascore',
                'm.hscore_1st','m.ascore_1st', 'm.hscore_2nd','m.ascore_2nd',
                'm.hscore_3rd','m.ascore_3rd', 'm.hscore_4th','m.ascore_4th',
                'm.h_ot','m.a_ot'
            )
            ->addSelect("home.icon as hicon", "away.icon as aicon");

        return $query;
    }

    private static function convertOtScore($otStr) {
        if (!isset($otStr) || strlen($otStr) == 0) {
            return null;
        }
        $array = explode(',', $otStr);
        $scores = [];
        foreach ($array as $item) {
            $scores[] = intval($item);
        }
        return $scores;
    }

    private static function getScore($score)
    {
        if (isset($score) && strlen($score) > 0) {
            return $score;
        }
        return NULL;
    }
}