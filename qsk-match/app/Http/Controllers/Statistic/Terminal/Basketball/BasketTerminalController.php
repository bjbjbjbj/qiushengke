<?php
namespace App\Http\Controllers\Statistic\Terminal\Basketball;


use App\Http\Controllers\Statistic\Change\MatchDataChangeTool;
use App\Http\Controllers\Statistic\SpiderTools;
use App\Http\Controllers\Statistic\StatisticFileTool;
use App\Models\LiaoGouModels\BasketLeague;
use App\Models\LiaoGouModels\BasketMatch;
use App\Models\LiaoGouModels\MatchLive;
use Illuminate\Support\Facades\Redis;

/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/2/28
 * Time: 19:44
 */
class BasketTerminalController
{
    use SpiderTools, BasketMatchBaseTool, BasketHistoryBattleTool, BasketOddResultTool, BasketOddTool,
        BasketRecentBattleTool, BasketScheduleTool, BasketRankTool, MatchDataChangeTool;

    const basketball_names = ['match', 'analyse', 'odd'];

    public function getData($mid, $name, $isReset = false) {
        if (!in_array($name, self::basketball_names)) {
            return null;
        }
        $data = array();
        switch ($name) {
            case 'match':
                $data = $this->match($mid, $isReset);
                break;
            case 'analyse':
                $match = BasketMatch::query()->find($mid);
                $data = $this->getAnalyseData($match);
                break;
            case 'odd':
                $data = $this->oddIndex($mid);
                break;
        }

        return $data;
    }

    public function onMatchAnalyseDataStatic($date = null, $saveCount = 6) {
//        set_time_limit(0);
//        dump(date('Y-m-d H:i:s'));
        if (is_null($date)) {
            $date = date('Ymd');
        }
        $schedule = StatisticFileTool::getFileFromSchedule($date, MatchLive::kSportBasketball, 'all');

        $isToday = date('Ymd') == date('Ymd', strtotime($date));

        $matches = isset($schedule['matches']) ? $schedule['matches'] : [];
        if (count($matches) <= 0) return;

        $key = "football_analyse_".$date."_static";
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
        if ($curSaveCount < $saveCount) {
            $length = $totalSaveCount - $saveCount;
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
    public function analyseDataStatic($mid, $typeArray = [1, 2], $reset = true) {
        $match = BasketMatch::query()->find($mid);
        if (isset($match)) {
            $analyse = $this->getAnalyseData($match, $typeArray, $reset);
            StatisticFileTool::putFileToTerminal($analyse, MatchLive::kSportBasketball, $mid, 'analyse');

            $matchJson = $this->getData($mid, "match", $reset);
            StatisticFileTool::putFileToTerminal($matchJson, MatchLive::kSportBasketball, $mid, 'match');

            echo "mid = $mid match analyse data static success<br>";
        }
    }

    private function getAnalyseData($match, $typeArray = [1, 2], $reset = false) {
        $analyse = StatisticFileTool::getFileFromTerminal(MatchLive::kSportBasketball, $match->id, 'analyse');
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