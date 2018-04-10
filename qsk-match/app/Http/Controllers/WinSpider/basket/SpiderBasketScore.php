<?php
/**
 * 爬赛事数据
 */
namespace App\Http\Controllers\WinSpider\basket;


use App\Models\LiaoGouModels\BasketLeague;
use App\Models\LiaoGouModels\BasketScore;
use App\Models\LiaoGouModels\BasketSeason;
use App\Models\LiaoGouModels\BasketStage;
use App\Models\LiaoGouModels\BasketTeam;
use Illuminate\Http\Request;

trait SpiderBasketScore
{
    /******************* 联赛赛相关 ***********************/
    private function leagueScore(Request $request) {
        $season = $request->input('season', '');
        $win_lid = $request->input('lid', 1);
        $lid = BasketLeague::getLeagueIdWithType($win_lid, 'win_id');
        if (is_null($season) || strlen($season) <= 0) {
            $season = BasketSeason::query()->where('lid', $lid)->orderBy('year', 'desc')->first();
            $season = isset($season) ? $season->name : "";
        }
        $url = "http://nba.win007.com/jsData/rank/$season/s".$win_lid.".js?version=".date('YmdH');
        $content = $this->spiderTextFromUrlByWin007($url, true, "http://nba.win007.com");

        preg_match_all('/var westData = \\[\\[(.*?)\\]\\];/is', $content, $westDatas);
        preg_match_all('/var eastData = \\[\\[(.*?)\\]\\];/is', $content, $eastDatas);

        $westDataArr = (isset($westDatas) && isset($westDatas[1])&& isset($westDatas[1][0])) ? explode('],[', $westDatas[1][0]) : [];
        $eastDataArr = (isset($eastDatas) && isset($eastDatas[1])&& isset($eastDatas[1][0])) ? explode('],[', $eastDatas[1][0]) : [];

        foreach ($westDataArr as $key=>$item) {
            $this->onLeagueScoreItemSave($win_lid, $lid, $key+1, $item, $season);
        }
        foreach ($eastDataArr as $key=>$item) {
            $this->onLeagueScoreItemSave($win_lid, $lid, $key+1, $item, $season, 1);
        }
    }

    private function onLeagueScoreItemSave($win_lid, $lid, $rank, $itemStr, $season, $zone = 0) {
        if (isset($itemStr) && strlen($itemStr) > 0 && count(explode(",", $itemStr)) >= 15) {
            list($tid, $win, $lose, $win_p, $lose_p,
                $win_diff, $goal, $fumble, $league_w, $league_l,
                $zone_w, $zone_l, $home_w, $home_l, $away_w,
                $away_l, $ten_w, $ten_l, $win_status, $win_reverse) = explode(",", $itemStr);
            $lg_team = BasketTeam::query()->where('win_id', $tid)->first();
            if (isset($lg_team)) {
                $lg_tid = $lg_team->id;
                $score = BasketScore::query()->where(['lid' => $lid, 'tid' =>$lg_tid, 'season'=>$season, 'zone'=>$zone])->first();
                if (!isset($score)) {
                    $score = new BasketScore();
                    $score->lid = $lid;
                    $score->tid = $lg_tid;
                    $score->season = $season;
                    $score->zone = $zone;
                }
                $score->win_lid = $win_lid;
                $score->win_tid = $tid;
                $score->rank = $rank;
                $score->count = $win + $lose;
                $score->win = $win;
                $score->lose = $lose;
                $score->goal = $goal;
                $score->fumble = $fumble;
                $score->win_diff = $win_diff;
                $score->home_bat_w = $home_w;
                $score->home_bat_l = $home_l;
                $score->away_bat_w = $away_w;
                $score->away_bat_l = $away_l;
                $score->league_bat_w = $league_w;
                $score->league_bat_l = $league_l;
                $score->zone_bat_w = $zone_w;
                $score->zone_bat_l = $zone_l;
                $score->ten_bat_w = $ten_w;
                $score->ten_bat_l = $ten_l;
                $score->win_status = $win_status > 0 ? $win_status : $win_status * $win_reverse;
                $score->save();
            }
        }
    }

    /******************* ***********************/

    /******************* 杯赛相关 ***********************/
    private function cupLeagueScore(Request $request) {
        $season = $request->input('season', '');
        $win_lid = $request->input('lid', 1);
        $lid = BasketLeague::getLeagueIdWithType($win_lid, 'win_id');
        if (is_null($season) || strlen($season) <= 0) {
            $season = BasketSeason::query()->where('lid', $lid)->orderBy('year', 'desc')->first();
            $season = isset($season) ? $season->name : "";
        }
        $url = "http://nba.win007.com/jsData/matchResult/$season/c".$win_lid.".js?version=".date('YmdH');
        $content = $this->spiderTextFromUrlByWin007($url, true, "http://nba.win007.com");

        //获取小组赛的组数数据
        preg_match_all('/jh\\["GH(.*?)\\"]/is', $content, $groupData);

        //只有有小组数据才保存小组积分
        if (isset($groupData) && isset($groupData[1]) && count($groupData[1]) > 0) {
            foreach ($groupData[1] as $stageId) {
                $lg_stage_id = BasketStage::getStageIdWithType($stageId, 'win_id');
                if ($lg_stage_id > 0) {
                    //获取有多少个小组和组名
                    preg_match_all('/jh\\["GH'.$stageId.'"\\] = \\[\\[(.*?)\\]\\];/is', $content, $groupItems);
                    if (isset($groupItems) && isset($groupItems[1]) && count($groupItems) > 0) {
                        $groupStr = $groupItems[1][0];
                        $groupStr = str_replace("'", "", $groupStr);
                        $groupStrs = explode("],[", $groupStr);

                        foreach ($groupStrs as $tempStr) {
                            if (count(explode(",",$tempStr)) >= 2) {
                                list($groupName, $groupId) = explode(",", $tempStr);
                                //获取每个小组的积分排名数据
                                preg_match_all('/jh\\["GS'.$stageId.'_'.$groupId.'"\\] = \\[\\[(.*?)\\]\\];/is', $content, $scoreData);
                                if (isset($scoreData) && isset($scoreData[1]) && count($scoreData[1]) > 0) {
                                    $scoreStr = $scoreData[1][0];
                                    $scoreStrs = explode("],[", $scoreStr);
                                    foreach ($scoreStrs as $key=>$itemStr) {
                                        $this->onCupLeagueScoreItemSave($win_lid, $lid, $groupName, $key+1, $itemStr, $season);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    private function onCupLeagueScoreItemSave($win_lid, $lid, $groupName, $rank, $itemStr, $season) {
        if (isset($itemStr) && strlen($itemStr) > 0 && count(explode(",", $itemStr)) >= 12) {
            list($tid, $count, $win, $lose, $win_p,
                $lose_p, $goal, $fumble, $diff, $win_status,
                $win_reverse, $status) = explode(",", $itemStr);
            $lg_team = BasketTeam::query()->where('win_id', $tid)->first();
            if (isset($lg_team)) {
                $lg_tid = $lg_team->id;
                $score = BasketScore::query()->where(['lid' => $lid, 'tid' =>$lg_tid, 'season'=>$season])->first();
                if (!isset($score)) {
                    $score = new BasketScore();
                    $score->lid = $lid;
                    $score->tid = $lg_tid;
                    $score->season = $season;
                }
                $score->group = $groupName;
                $score->win_lid = $win_lid;
                $score->win_tid = $tid;
                $score->rank = $rank;
                $score->count = $win + $lose;
                $score->win = $win;
                $score->lose = $lose;
                $score->goal = $goal;
                $score->fumble = $fumble;
                $score->win_status = $win_status > 0 ? $win_status : $win_status * $win_reverse;
                //是否晋级下一轮
                $score->status = $status == 1 ? 0 : -1;
                $score->save();
            }
        }
    }
}