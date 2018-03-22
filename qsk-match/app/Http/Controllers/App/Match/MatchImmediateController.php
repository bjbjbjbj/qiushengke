<?php
namespace App\Http\Controllers\App\Match;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

/**
 * Created by PhpStorm.
 * User: ricky
 * Date: 2018/1/3
 * Time: 10:52
 */
class MatchImmediateController
{
    use MatchCommonTool;

    public function getMatch(Request $request)
    {
        //赛事id筛选
        $filterIds = null;
        if ($request->exists('id')) {
            $filterIds = explode(',', $request->input('id'));
        }

        $isForApp = $request->input('app', 0) == 1;

        $date = $request->input("date", date('Y-m-d'));

        $result = array();
        //直播入口数量
        $liveCounts = $this->getMatchLiveCountByDate($date);

        //需要返回类型
        $type = $request->input('type', 'all');
        if (!$this->isMatchType($type)) {
            $type = 'all';
        }

        if (is_null($filterIds)) {
            //
            $query = $this->getMatchCommonQuery($date);
            $query = $this->onCommonOrderBy($query);
            $matches = $query->get();

            //各个filter对应比赛
            $allResult = array();
            $firstResult = array();
            $footResult = array();
            $lotteryResult = array();
            $bjResult = array();
            //全部
            $all = array();
            //一级
            $first = array();
            //足彩
            $foot = array();
            //竞彩
            $lottery = array();
            //北单
            $bj = array();

            //全部 一级
            foreach ($matches as $match) {

                $isfilter = false;
                if (isset($filterIds)) {
                    if (in_array($match->lid, $filterIds)) {
                        $isfilter = true;
                    } else {
                    }
                } else {
                    $isfilter = true;
                }

                $istype = 'all' == $type ? true : false;
                //全部
                if (!array_key_exists($match['league'], $all)) {
                    $all[$match['league']] = ['py' => $this->getFirstCharter($match['league']), 'name' => $match['league'], 'id' => intval($match->lid)];
                }

                $tmp = $this->dataForApi($match, $liveCounts, $isForApp);
                $allResult[] = $tmp;
                if ($isfilter && $istype) {
                    $result[] = $tmp;
                }
                //一级
                if ($match->genre >> 1 & 1) {
                    $istype = $type == 'first';
                    if (!array_key_exists($match['league'], $first)) {
                        $first[$match['league']] = ['py' => $this->getFirstCharter($match['league']), 'name' => $match['league'], 'id' => intval($match->lid)];
                    }
                    $firstResult[] = $tmp;
                    if ($isfilter && $istype) {
                        $result[] = $tmp;
                    }
                }
            }
            //足彩
            $query = $this->getMatchCommonQuery($date)
                ->join('liaogou_lottery.lottery_details', 'm.id', '=', 'lottery_details.mid')
                ->join('liaogou_lottery.lotteries', function ($q) {
                    $q->on('lotteries.id', '=', 'lottery_details.lottery_id')
                        ->whereNull('lotteries.type');
                });
            $query = $this->onCommonOrderBy($query);

            $matches = $query->get();
            foreach ($matches as $match) {

                $isfilter = false;
                $match->makeVisible(['lid', 'mid']);
                if (isset($filterIds)) {
                    if (in_array($match->lid, $filterIds)) {
                        $isfilter = true;
                    } else {
                    }
                } else {
                    $isfilter = true;
                }
                $istype = 'foot' == $type;
                if (!array_key_exists($match['league'], $foot)) {
                    $foot[$match['league']] = ['py' => $this->getFirstCharter($match['league']), 'name' => $match['league'], 'id' => intval($match->lid)];
                }
                $tmp = $this->dataForApi($match, $liveCounts, $isForApp);
                $footResult[] = $tmp;

                if ($isfilter && $istype) {
                    $result[] = $tmp;
                }
            }
            //竞彩
            $query = $this->getMatchCommonQuery($date)
                ->join('liaogou_lottery.sport_bettings', function ($join) {
                    $join->on('m.id', '=', 'sport_bettings.mid')
                        ->where(function ($q){
                            $q->whereNull('sport_bettings.status')
                                ->orWhere('sport_bettings.status', '!=', '取消');
                        });
                });

            $matches = $query->orderBy("sport_bettings.issue_num", "asc")
                ->orderBy("sport_bettings.num", "asc")
                ->orderBy("m.time", "asc")
                ->get();

            foreach ($matches as $match) {

                $match->makeVisible(['lid', 'mid']);
                $isfilter = false;
                if (isset($filterIds)) {
                    if (in_array($match->lid, $filterIds)) {
                        $isfilter = true;
                    } else {

                    }
                } else {
                    $isfilter = true;
                }
                $istype = 'lottery' == $type ? true : false;
                if (!array_key_exists($match['league'], $lottery)) {
                    $lottery[$match['league']] = ['py' => $this->getFirstCharter($match['league']), 'name' => $match['league'], 'id' => intval($match->lid)];
                }

                $tmp = $this->dataForApi($match, $liveCounts, $isForApp);
                $lotteryResult[] = $tmp;

                if ($isfilter && $istype) {
                    $result[] = $tmp;
                }
            }
            //北单
            $query = $this->getMatchCommonQuery($date)
                ->join('liaogou_lottery.sport_betting_bds', 'm.id', '=', 'sport_betting_bds.mid');

            $matches = $query->orderBy("m.time", "asc")
                ->orderBy("sport_betting_bds.num", "asc")
                ->get();
            foreach ($matches as $match) {

                $match->makeVisible(['lid', 'mid']);
                $isfilter = false;
                if (isset($filterIds)) {
                    if (in_array($match->lid, $filterIds)) {
                        $isfilter = true;
                    } else {
                    }
                } else {
                    $isfilter = true;
                }
                $istype = 'bj' == $type ? true : false;
                if (!array_key_exists($match['league'], $bj)) {
                    $bj[$match['league']] = ['py' => $this->getFirstCharter($match['league']), 'name' => $match['league'], 'id' => intval($match->lid)];
                }

                $tmp = $this->dataForApi($match, $liveCounts, $isForApp);
                $bjResult[] = $tmp;

                if ($isfilter && $istype) {
                    $result[] = $tmp;
                }
            }

            usort($first, array($this, "my_sort"));
            usort($foot, array($this, "my_sort"));
            usort($lottery, array($this, "my_sort"));
            usort($bj, array($this, "my_sort"));
            usort($all, array($this, "my_sort"));

            //结果 filter的ids和比赛列表
            if ($isForApp) {
                $lids = array(
                    ['type'=>'all', 'name'=>'全部', 'data'=> $all],
                    ['type'=>'lottery', 'name'=>'竞彩', 'data'=> $lottery],
                    ['type'=>'first', 'name'=>'一级', 'data'=> $first],
                    ['type'=>'foot', 'name'=>'足彩', 'data'=> $foot],
                    ['type'=>'bj', 'name'=>'北单', 'data'=> $bj]);
            } else {
                $lids = array('all' => $all, 'first' => $first, 'foot' => $foot, 'lottery' => $lottery, 'bj' => $bj);
            }
//            if ($isIndex) {
//                $resultMatch = count($lotteryResult) > 0 ? $lotteryResult : (count($footResult) > 0 ? $footResult : $allResult);
//            } else {
//                $resultMatch = $result;
//            }
            return ['filter' => $lids, 'matches' => $result];
        } else {
            //竞彩特殊处理
            if ($type == 'lottery'){
                $query = $this->getMatchCommonQuery($date);
                $query->join('liaogou_lottery.sport_bettings', function ($join) {
                    $join->on('m.id', '=', 'sport_bettings.mid')
                        ->where(function ($q){
                            $q->whereNull('sport_bettings.status')
                                ->orWhere('sport_bettings.status', '!=', '取消');
                        });
                });
                if (!is_null($filterIds)) {
                    $query->whereIn('m.lid', $filterIds);
                }

                $query->orderBy("sport_bettings.issue_num", "asc")
                    ->orderBy("sport_bettings.num", "asc")
                    ->orderBy("m.time", "asc");
                $matches = $query->get();
                foreach ($matches as $match) {

                    $match->makeVisible(['lid', 'mid']);
                    $tmp = $this->dataForApi($match, $liveCounts, $isForApp);
                    $result[] = $tmp;
                }
            } else {
                $query = $this->getMatchCommonQuery($date)
                    ->whereIn('m.lid', $filterIds);

                $query = $this->onCommonOrderBy($query);

                $matches = $query->get();

                foreach ($matches as $match) {

                    $match->makeVisible(['lid', 'mid']);
                    $tmp = $this->dataForApi($match, $liveCounts, $isForApp);
                    $result[] = $tmp;
                }
            }

            return ['matches' => $result];
        }
    }

    public function matchesByIds(Request $request)
    {
        $ids = $request->input('ids');
        $ids = explode(',', $ids);
        $result = array();
        if (count($ids) > 0) {

            $isForApp = $request->input('app', 0) == 1;

            $matches = $this->getMatchCommonQuery()
                ->whereIn('m.id', $ids)
                ->orderBy('m.status', 'desc')
                ->orderBy('m.time', 'desc')->get();

            //文章推荐数量
            $articleCounts = $this->getMatchArticleCountByIds($ids);
            //直播入口数量
            $liveCounts = $this->getMatchLiveCountByIds($ids);

            foreach ($matches as $match) {
                $match->makeVisible(['lid', 'mid']);
                $tmp = $this->dataForApi($match, $articleCounts, $liveCounts, $isForApp);
                $result[] = $tmp;
            }
        }
        return $result;
    }

    private function dataForApi($match, $liveCounts = array(), $isForApp = false)
    {
        $match->current_time = $match->getCurMatchTime($isForApp);;

        $match->live = isset($liveCounts[$match->mid]) ? $liveCounts[$match->mid] : 0;
        $match->live2 = isset($liveCounts[$match->mid]) ? $liveCounts[$match->mid] : 0;

        $match->time = strtotime($match->time);

        $outMatch = array();
        foreach ($match->getAttributes() as $key => $value) {
            if (!in_array($key, ['timehalf'])) {
                if (in_array($key, ['mid', 'lid', 'status', 'hscore', 'ascore', 'hscorehalf', 'ascorehalf', 'article', 'live'])) {
                    $outMatch[$key] = intval($value);
                } else {
                    $outMatch[$key] = $value;
                }
            }
        }
        return $outMatch;
    }
}