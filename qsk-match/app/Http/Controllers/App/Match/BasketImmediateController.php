<?php
namespace App\Http\Controllers\App\Match;

use App\Models\LiaoGouModels\BasketMatch;
use App\Models\LiaoGouModels\BasketTeam;
use App\Models\LiaoGouModels\MatchLive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

/**
 * Created by PhpStorm.
 * User: ricky
 * Date: 2018/1/3
 * Time: 10:52
 */
class BasketImmediateController
{
    use MatchCommonTool;

    /**
     * @param Request $request
     * @return array
     */
    public function getMatch(Request $request)
    {
        //赛事id筛选
        $filterIds = null;
        if ($request->exists('id')) {
            $filterIds = explode(',', $request->input('id'));
        }

        $date = $request->input("date", date('Y-m-d'));

        $isForApp = $request->input('app', 0) == 1;

        $result = array();
        //直播入口数量
        $liveCounts = $this->getMatchLiveCountByDate($date, MatchLive::kSportBasketball);

        //需要返回类型
        $type = $request->input('type', 'all');
        if (!$this->isMatchType($type, MatchLive::kSportBasketball)) {
            $type = 'all';
        }

        if (is_null($filterIds)) {
            //通用
            $query = $this->getBasketCommonQuery($date);
            $query = $this->onCommonOrderBy($query);
            $matches = $query->get();

            //各个filter对应比赛
            $allResult = array();
            $firstResult = array();
            $nbaResult = array();
            $lotteryResult = array();
            //全部
            $all = array();
            //一级
            $first = array();
            //NBA
            $nba = array();
            //竞彩
            $lottery = array();
            //全部比赛进行分类操作,分成全部 一级 nba
            foreach ($matches as $match) {

                //转化成需要的格式
                $tmp = $this->dataForApi($match, $liveCounts, $isForApp);
                //全部
                if (!array_key_exists($match['league'], $all)) {
                    $all[$match['league']] = ['py' => $this->getFirstCharter($match['league']), 'name' => $match['league'], 'id' => intval($match->lid)];
                }
                $allResult[] = $tmp;

                //一级
                if ($match->hot == 1) {
                    if (!array_key_exists($match['league'], $first)) {
                        $first[$match['league']] = ['py' => $this->getFirstCharter($match['league']), 'name' => $match['league'], 'id' => intval($match->lid)];
                    }
                    $firstResult[] = $tmp;
                }
                //nba
                if ($match->lid == 1){
                    if (!array_key_exists($match['league'], $nba)) {
                        $nba[$match['league']] = ['py' => $this->getFirstCharter($match['league']), 'name' => $match['league'], 'id' => intval($match->lid)];
                    }
                    $nbaResult[] = $tmp;
                }
            }

            //竞彩,独立搜一次,需要join确定(暂时,看看能不能放到match表)
            $query = $this->getBasketCommonQuery($date)
                ->join('liaogou_lottery.sport_betting_baskets', 'm.id', '=', 'sport_betting_baskets.mid');

            $matches = $query->orderBy("sport_betting_baskets.issue_num", "asc")
                ->orderBy("sport_betting_baskets.num", "asc")
                ->orderBy("m.time", "asc")
                ->get();
            foreach ($matches as $match) {

                $match->makeVisible(['lid', 'mid']);
                if (!array_key_exists($match['league'], $lottery)) {
                    $lottery[$match['league']] = ['py' => $this->getFirstCharter($match['league']), 'name' => $match['league'], 'id' => intval($match->lid)];
                }
                $tmp = $this->dataForApi($match, $liveCounts, $isForApp);
                $lotteryResult[] = $tmp;
            }

            //排序
            usort($first, array($this, "my_sort"));
            usort($nba, array($this, "my_sort"));
            usort($lottery, array($this, "my_sort"));
            usort($all, array($this, "my_sort"));

            //结果 filter的ids和比赛列表
            if ($isForApp) {
                $lids = array(
                    ['type'=>'all', 'name'=>'全部', 'data'=> $all],
                    ['type'=>'first', 'name'=>'一级', 'data'=> $first],
                    ['type'=>'lottery', 'name'=>'竞彩', 'data'=> $lottery],
                    ['type'=>'nba', 'name'=>'NBA', 'data'=> $nba]);
            } else {
                $lids = array('all' => $all, 'first' => $first, 'nba' => $nba, 'lottery' => $lottery);
            }

            //结果比赛列表返回哪个
            if ($type == 'all'){
                $resultMatch = $allResult;
            }
            elseif ($type == 'first'){
                $resultMatch = $firstResult;
            }
            elseif ($type == 'lottery'){
                $resultMatch = $lotteryResult;
            }
            elseif ($type == 'nba'){
                $resultMatch = $nbaResult;
            }
            return ['filter' => $lids, 'matches' => $resultMatch];
        }
        else {
            //竞彩特殊处理
            if ($type == 'lottery'){
                $query = $this->getBasketCommonQuery($date);
                $query->join('liaogou_lottery.sport_betting_baskets', 'm.id', '=', 'sport_betting_baskets.mid');
                if (!is_null($filterIds)) {
                    $query->whereIn('m.lid', $filterIds);
                }

                $query->orderBy("sport_betting_baskets.issue_num", "asc")
                    ->orderBy("sport_betting_baskets.num", "asc")
                    ->orderBy("m.time", "asc");
                $matches = $query->get();
                foreach ($matches as $match) {

                    $match->makeVisible(['lid', 'mid']);
                    $tmp = $this->dataForApi($match, $liveCounts, $isForApp);
                    $result[] = $tmp;
                }
            }
            else{
                //其他正常检索就好
                $query = $this->getBasketCommonQuery($date);
                if (!is_null($filterIds)) {
                    $query->whereIn('m.lid', $filterIds);
                }

                $query = $this->onCommonOrderBy($query);
                $matches = $query->get();
                foreach ($matches as $match) {

                    $match->makeVisible(['lid', 'mid']);
                    //转化成需要的格式
                    $tmp = $this->dataForApi($match, $liveCounts, $isForApp);
                    if ($type == 'all') {
                        //全部
                        $result[] = $tmp;
                    }
                    //一级
                    if ($type == 'first' && $match->hot == 1) {
                        $result[] = $tmp;
                    }
                    //nba
                    if ($type == 'nba' && $match->lid == 1){
                        $result[] = $tmp;
                    }
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

            $matches = $this->getBasketCommonQuery()
                ->whereIn('m.id', $ids)
                ->orderBy('m.status', 'desc')
                ->orderBy('m.time', 'desc')->get();

            //直播入口数量
            $liveCounts = $this->getMatchLiveCountByIds($ids, MatchLive::kSportBasketball);

            foreach ($matches as $match) {
                $match->makeVisible(['lid', 'mid']);
                $tmp = $this->dataForApi($match, $liveCounts, $isForApp);
                $result[] = $tmp;
            }
        }
        return $result;
    }

    private function dataForApi($match, $liveCounts = array(), $isForApp = false)
    {
        $match->live_time_str = BasketMatch::getMatchCurrentTime($match->live_time_str, $match->status, $match->system == 1, $isForApp);

        $match->hicon = BasketTeam::getIcon($match->hicon);
        $match->aicon = BasketTeam::getIcon($match->aicon);

        $match->h_ot = $this->convertOtScore($match->h_ot);
        $match->a_ot = $this->convertOtScore($match->a_ot);

        $match->live = isset($liveCounts[$match->mid]) ? $liveCounts[$match->mid] : 0;
        $match->live2 = isset($liveCounts[$match->mid]) ? $liveCounts[$match->mid] : 0;

        if ($isForApp) {
            $match->time = strtotime($match->time);

            $outMatch = array();
            foreach ($match->getAttributes() as $key => $value) {
                if (!in_array($key, ['hot', 'timehalf'])) {
                    if (in_array($key, ['mid', 'lid', 'status', 'hscore', 'ascore',
                        'hscore_1st', 'ascore_1st', 'hscore_2nd', 'ascore_2nd',
                        'hscore_3rd', 'ascore_3rd', 'hscore_4th', 'ascore_4th',
                        'system', 'article', 'live'])) {
                        $outMatch[$key] = intval($value);
                    } else {
                        $outMatch[$key] = $value;
                    }
                }
            }
            return $outMatch;
        }
        return $match;
    }

    private function convertOtScore($otStr) {
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
}