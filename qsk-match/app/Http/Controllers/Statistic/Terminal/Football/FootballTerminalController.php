<?php
/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/2/27
 * Time: 18:47
 */

namespace App\Http\Controllers\Statistic\Terminal\Football;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Statistic\Change\MatchDataChangeTool;
use App\Http\Controllers\Statistic\SpiderTools;
use App\Http\Controllers\Statistic\StatisticFileTool;
use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\MatchLive;
use Illuminate\Support\Facades\Redis;

class FootballTerminalController extends Controller
{
    use SpiderTools, FootballMatchBaseTool, FootballTeamAttributeTool, FootballRankTool, FootballHistoryBattleTool,
        FootballRecentBattleTool, FootballScheduleTool, FootballOddResultTool, FootballStyleTool,
        FootballRefereeTool, FootballCornerTool, FootballSameOddTool, FootballOddTool, MatchDataChangeTool;

    const football_names = ['match', 'analyse', 'odd'];

    public function getData($mid, $name, $isReset = false) {
        if (!in_array($name, self::football_names)) {
            return null;
        }
        $data = array();
        switch ($name) {
            case 'match':
                $data = $this->match($mid, $isReset);
                break;
            case 'analyse':
                $match = Match::query()->find($mid);
                $data = $this->getAnalyseData($match);
                break;
            case 'odd':
                $data = $this->oddIndex($mid);
                break;
        }

        return $data;
    }

    public function onMatchAnalyseDataStatic($date = null, $saveCount = 6, $isResetRedis = false) {
//        set_time_limit(0);
//        dump(date('Y-m-d H:i:s'));
        if (is_null($date)) {
            $date = date('Ymd');
        }
        $schedule = StatisticFileTool::getFileFromSchedule($date, MatchLive::kSportFootball, 'all');

        $matches = isset($schedule['matches']) ? $schedule['matches'] : [];
        if (count($matches) <= 0) return;

        $matches = collect($matches)->sort(function ($a,$b){
            if ($a['genre'] == $b['genre']) {
                if (isset($a['asiamiddle1']) || isset($b['asiamiddle1'])) {
                    return isset($a['asiamiddle1']) ? 0 : 1;
                } else {
                    return $a['time'] < $b['time'] ? 1 : 0;
                }
            } else {
                return $a['genre'] > $b['genre'] ? 0 : 1;
            }
        })->all();

        $curMatchCount = count($matches);
        echo "cur_match_count = $curMatchCount <br>";

        $key = "football_analyse_".$date."_static";
        if ($isResetRedis) {
            Redis::del($key);
        }
        $savedMids = json_decode(Redis::get($key));
        if (is_null($savedMids)) {
            $savedMids = [];
        }
        //上次总共保存的比赛数量
        $lastSaveCount = count($savedMids);

        $count = 0;
        $livingCount = 0;
        foreach ($matches as $match) {
            if ($count >= $saveCount) break;

            $time = $match['time'];
            $isToday = $time - time() <= 6*60;

            $mid = $match['mid'];
            if (!in_array($mid, $savedMids)) {
                if ($isToday) {//如果是当天的数据，则只刷新球队风格和同赔数据
                    $this->analyseDataStatic($mid, [2], true);
                } else {
                    $this->analyseDataStatic($mid);
                }
                $savedMids[] = $mid;
                $count++;
            }
        }
        $totalSaveCount = count($savedMids);
        $curSaveCount = $totalSaveCount - $lastSaveCount - $livingCount;
        echo "total_save_count = $totalSaveCount; cur_save_count = $curSaveCount <br>";
        if ($totalSaveCount > $curMatchCount || $curSaveCount < $saveCount) {
            if ($totalSaveCount > $curMatchCount) {
                $length = $totalSaveCount - $curMatchCount - $saveCount;
            } else {
                $length = $totalSaveCount - $saveCount;
            }
            if ($length > 0) {
                $savedMids = array_slice($savedMids, $saveCount, $length);
            } else {
                $savedMids = [];
            }
        }
        Redis::set($key,json_encode($savedMids));
        //设置过期时间 24小时
        Redis::expire($key, 24*60*60);

//        dump(date('Y-m-d H:i:s'));
    }

    /**
     * @param int $mid 比赛id
     * @param array $typeArray 1表示固定不变的部分，2表示可能会更改的部分（风格、同赔）
     */
    public function analyseDataStatic($mid, $typeArray = [1, 2], $reset = false) {
        $match = Match::query()->find($mid);
        if (isset($match)) {
            $analyse = $this->getAnalyseData($match, $typeArray, $reset);

            StatisticFileTool::putFileToTerminal($analyse, MatchLive::kSportFootball, $mid, 'analyse');

            $matchJson = $this->getData($mid, "match", $reset);
            StatisticFileTool::putFileToTerminal($matchJson, MatchLive::kSportFootball, $mid, 'match');
            echo "mid = $mid match analyse data static success<br>";
        }
    }

    private function getAnalyseData($match, $typeArray = [1, 2], $reset = false) {
        $analyse = StatisticFileTool::getFileFromTerminal(MatchLive::kSportFootball, $match->id, 'analyse');
        /**
         * 球队相关
         */
        if (in_array(1, $typeArray)) {
            //球队攻防能力 team_attribute
            $analyse['attribute'] = $this->matchTeamAttribute($match, $this->getJsonItemByName($analyse, 'attribute'), $reset);
        }
        if (in_array(2, $typeArray)) {
            //球队风格、场面预测 team_style
//            $analyse['ws'] = $this->teamStyleData($match, $this->getJsonItemByName($analyse, 'ws'), $reset);
            //裁判
            $analyse['referee'] = $this->matchDetailReferee($match, $this->getJsonItemByName($analyse, 'referee'), $reset);
        }
        /**
         * 分析相关
         */
        if (in_array(1, $typeArray)) {
            //积分排名 rank
            $analyse['rank'] = $this->rankStatistic($match, $this->getJsonItemByName($analyse, 'rank'), $reset);
            //交锋往绩 history_battle
            $analyse['historyBattle'] = $this->matchHistoryBattle($match, $this->getJsonItemByName($analyse, 'historyBattle'), $reset);
            //近期战绩 recent_battle
            $analyse['recentBattle'] = $this->matchBaseRecentlyBattle($match, $this->getJsonItemByName($analyse, 'recentBattle'), $reset);
            //赛事盘路 odd_result
            $analyse['oddResult'] = $this->matchOddResult($match, $this->getJsonItemByName($analyse, 'oddResult'), $reset);
            //未来赛程 schedule
            $analyse['schedule'] = $this->matchSchedule($match, $this->getJsonItemByName($analyse, 'schedule'), $reset);
        }
        /**
         * 历史同赔
         */
        if (in_array(2, $typeArray)) {
            //同赔 same_odd
            $analyse['sameOdd'] = $this->sameOdd($match, $this->getJsonItemByName($analyse, 'sameOdd'), $reset);
        }
        /**
         * 角球相关
         */
        if (in_array(1, $typeArray)) {
            //角球统计 corner_analyse
            $analyse['cornerAnalyse'] = $this->matchCornerAnalyse($match, $this->getJsonItemByName($analyse, 'cornerAnalyse'), $reset);
            //角球交锋往绩 corner_history_battle
            $analyse['cornerHistoryBattle'] = $this->matchCornerHistoryBattle($match, $this->getJsonItemByName($analyse, 'cornerHistoryBattle'), $reset);
            //角球近期战绩 corner_recent_battle
            $analyse['cornerRecentBattle'] = $this->matchCornerRecentBattle($match, $this->getJsonItemByName($analyse, 'cornerRecentBattle'), $reset);
        }

        return $analyse;
    }

    private function getJsonItemByName($jsonData, $name) {
        $outData = null;
        if (isset($jsonData) && isset($jsonData[$name])) {
            $outData = $jsonData[$name];
        }
        return $outData;
    }
}