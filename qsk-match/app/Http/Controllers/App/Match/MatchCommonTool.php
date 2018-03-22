<?php
namespace App\Http\Controllers\App\Match;

use App\Models\LiaoGouModels\BasketMatch;
use App\Models\LiaoGouModels\BasketMatchesAfter;
use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\MatchesAfter;
use App\Models\LiaoGouModels\MatchLive;
use App\Models\LiaoGouModels\MatchLiveChannel;
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
    function my_sort($a, $b)
    {
        if ($a['py']==$b['py']) {
            return 0;
        }
        return ($a['py']>$b['py'])?1:-1;
    }

    //根据传入的比赛id, 获取比赛推荐的数量
    private function getMatchLiveCountByIds($mids, $sport = MatchLive::kSportFootball,$platform = MatchLiveChannel::kUse310) {
        //是否有直播
        $query = MatchLive::query()
            ->selectRaw('match_id as id, count(channel.id) as count')
            ->leftJoin('match_live_channels as channel', 'match_lives.id', 'channel.live_id')
            ->whereIn('match_id', $mids)
            ->where('sport', $sport)
            ->where('show', MatchLiveChannel::kShow)
            ->whereIn('channel.platform', [1, 3]);

        switch ($platform){
            case MatchLiveChannel::kUse310:
                $query->where(function ($orQuery) {
                    $orQuery->where('use',MatchLiveChannel::kUseAll);
                    $orQuery->orWhere('use',MatchLiveChannel::kUse310);
                });
                break;
            case MatchLiveChannel::kUseAikq:{
                $query->where(function ($orQuery) {
                    $orQuery->where('use',MatchLiveChannel::kUseAll);
                    $orQuery->orWhere('use',MatchLiveChannel::kUseAiKQ);
                });
            }
                break;
            case MatchLiveChannel::kUseHeitu:{
                $query->where('use',MatchLiveChannel::kUseAll);
            }
                break;
            case MatchLiveChannel::kUseAll:
                break;
        }

        $liveCounts = $query
            ->groupBy('match_id', 'sport')->get()
            ->mapWithKeys(function ($item){
                return [$item->id => $item->count];
            })->all();
        return $liveCounts;
    }

    //根据传入的时间范围，获取直播平台的数量
    private function getMatchLiveCountByDate($date, $sport = MatchLive::kSportFootball) {
        if ($sport == MatchLive::kSportBasketball) {
            $query = BasketMatch::query()->select('basket_matches.id')
                ->whereBetween('status', [-1, 100]);
        } else {
            $query = Match::query()->select('matches.id')
                ->whereBetween('status', [-1, 4]);
        }

        $dateArray = $this->convertDateToDateRange($date, $sport);
        $startDate = isset($dateArray['startDate2']) ? $dateArray['startDate2'] : $dateArray['startDate'];

        $normalIds = $query->whereBetween('time', [$startDate, $dateArray['endDate']])
            ->orderBy('time', 'desc')->get()
            ->map(function ($item){
                return $item->id;
            })->all();
        return $this->getMatchLiveCountByIds($normalIds, $sport);
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
        $query->orderBy('m.status', 'desc')
            ->orderBy('m.time', 'asc');

        return $query;
    }

    //=====================足球部分=================

    //获取足球比赛数据的统一请求部分
    //获取足球比赛数据的统一请求部分
    private function getMatchCommonQuery($date = '') {

        $status = $this->convertDateToStatus($date);

        if ($status == 1) {
            $query = MatchesAfter::from('matches_afters as m');
        } else {
            $query = Match::from("matches as m");
        }

        $query->join('leagues', 'lid', '=', 'leagues.id')
            ->addSelect('m.id as mid', 'leagues.name as league');
        //暂时没有点击进去的,这里可以先无视teamid
//                ->whereNotNull("hid")
//                ->whereNotNull("aid")
        //性能问题,这里用join效率更高
        $query->leftjoin('odds as asia', function ($join) {
            $join->on('m.id', '=', 'asia.mid');
            $join->where('asia.type', '=', 1);
            $join->where('asia.cid', '=', Odd::default_calculate_cid);
        })
            ->leftjoin('odds as ou', function ($join) {
                $join->on('m.id', '=', 'ou.mid');
                $join->where('ou.type', '=', 3);
                $join->where('ou.cid', '=', Odd::default_calculate_cid);
            })
            ->addSelect('asia.up2 as asiaup2', 'asia.middle2 as asiamiddle2', 'asia.down2 as asiadown2',
                'ou.up2 as ouup2', 'ou.middle2 as oumiddle2', 'ou.down2 as oudown2');

        //角球和红黄牌统计数据
        $query->leftjoin('match_datas', 'm.id', '=', 'match_datas.id')
            ->addSelect('match_datas.h_corner as h_corner', 'match_datas.a_corner as a_corner')
            ->addSelect('match_datas.h_red as h_red', 'match_datas.a_red as a_red')
            ->addSelect('match_datas.h_yellow as h_yellow', 'match_datas.a_yellow as a_yellow');

        $query->leftJoin("teams as home", "m.hid", "home.id")
            ->leftJoin("teams as away", "m.aid", "away.id")
            ->addSelect('m.lid', 'm.status', 'm.genre','m.betting_num',
                'm.time', 'm.timehalf', 'm.hname','m.aname',
                'm.hscore','m.ascore', 'm.hscorehalf','m.ascorehalf')
            ->addSelect("home.icon as hicon", "away.icon as aicon");

        //状态相关
        $query = $this->onMatchFilterByStatus($query, $status);

        //时间相关
        $query = $this->onMatchFilterByDate($query, $date);

        return $query;
    }

    //=====================篮球部分=================

    //获取篮球比赛数据的统一请求部分
    private function getBasketCommonQuery($date = '') {
        $status = $this->convertDateToStatus($date);

        if ($status == 1) {
            $query = BasketMatchesAfter::from('basket_matches_afters as m');
        } else {
            $query = BasketMatch::from("basket_matches as m");
        }

        $query->leftjoin('basket_leagues', 'lid', '=', 'basket_leagues.id')
            ->addSelect('m.id as mid', 'basket_leagues.name as league',
                'basket_leagues.hot', 'basket_leagues.system');
        //暂时没有点击进去的,这里可以先无视teamid
//                ->whereNotNull("hid")
//                ->whereNotNull("aid")
        //性能问题,这里用join效率更高
        $query->leftjoin('basket_odds as asia', function ($join) {
            $join->on('m.id', '=', 'asia.mid');
            $join->where('asia.type', '=', 1);
            $join->where('asia.cid', '=', Odd::default_calculate_cid);
        })
            ->leftjoin('basket_odds as ou', function ($join) {
                $join->on('m.id', '=', 'ou.mid');
                $join->where('ou.type', '=', 3);
                $join->where('ou.cid', '=', Odd::default_calculate_cid);
            })
            ->addSelect('asia.up2 as asiaup2', 'asia.middle2 as asiamiddle2', 'asia.down2 as asiadown2',
                'ou.up2 as ouup2', 'ou.middle2 as oumiddle2', 'ou.down2 as oudown2');

        $query->leftJoin("basket_teams as home", "m.hid", "home.id")
            ->leftJoin("basket_teams as away", "m.aid", "away.id")
            ->addSelect('m.lid', 'm.status', 'm.live_time_str','m.betting_num',
                'm.time', 'm.timehalf', 'm.hname','m.aname','m.hscore','m.ascore',
                'm.hscore_1st','m.ascore_1st', 'm.hscore_2nd','m.ascore_2nd',
                'm.hscore_3rd','m.ascore_3rd', 'm.hscore_4th','m.ascore_4th',
                'm.h_ot','m.a_ot'
            )
            ->addSelect("home.icon as hicon", "away.icon as aicon");

        //状态相关
        $query = $this->onMatchFilterByStatus($query, $status);

        //时间相关
        $query = $this->onMatchFilterByDate($query, $date, MatchLive::kSportBasketball);

        return $query;
    }
}