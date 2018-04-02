<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/2/8
 * Time: 18:00
 */

namespace App\Console\Match\Football;

use App\Http\Controllers\CacheInterface\FootballInterface;
use App\Http\Controllers\PC\FileTool;
use App\Http\Controllers\PC\Index\FootballController;
use App\Http\Controllers\PC\Match\MatchDetailController;
use App\Http\Controllers\StaticHtml\FootballDetailController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class FootballDetailCommands extends Command
{
    const PC_REDIS_KEY = 'PC_FOOTBALL_DETAIL_SCHEDULE_KEY';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fb_detail_cache:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '足球终端页缓存 10分钟一次';

    /**
     * Create a new command instance.
     * HotMatchCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 定时任务，将进行中的比赛静态化。
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        $this->staticSchedule();
        $this->staticResult();
    }

    public function staticSchedule(){
        $startDate = date('Y-m-d', strtotime('+1 days'));
        $json = FileTool::matchListDataJson($startDate,1);
        $matches = isset($json['matches']) ? $json['matches'] : [];
        $key = self::PC_REDIS_KEY . date('Ymd') . floor(date('H') / 4);
        $excMidStr = Redis::get($key);
        $excArray = json_decode($excMidStr, true);;
        if (is_null($excArray)) {
            $excArray = [];
        }
        $excIndex = 0;
        //每10分钟一次，一次缓存15场比赛。
        foreach ($matches as $match) {
            if ($excIndex > 15) break;
            $id = $match['mid'];
            if (in_array($id, $excArray)) {
                continue;
            }
            $excArray[] = $id;
            MatchDetailController::curlToHtml($id);
            //wap
            \App\Http\Controllers\Phone\Detail\FootballController::curlToHtml($id);
            $excIndex++;
            sleep(1);
        }
        Redis::setEx($key, 4 * 60 * 60, json_encode($excArray));
    }

    public function staticResult(){
        $startDate = date('Y-m-d', strtotime('-1 days'));
        $json = FileTool::matchListDataJson($startDate,1);
        $matches = isset($json['matches']) ? $json['matches'] : [];
        $key = self::PC_REDIS_KEY . date('Ymd') . floor(date('H') / 4);
        $excMidStr = Redis::get($key);
        $excArray = json_decode($excMidStr, true);;
        if (is_null($excArray)) {
            $excArray = [];
        }
        $excIndex = 0;
        //每10分钟一次，一次缓存15场比赛。
        foreach ($matches as $match) {
            if ($excIndex > 15) break;
            $id = $match['mid'];
            if (in_array($id, $excArray)) {
                continue;
            }
            $excArray[] = $id;
            MatchDetailController::curlToHtml($id);
            //wap
            \App\Http\Controllers\Phone\Detail\FootballController::curlToHtml($id);
            $excIndex++;
            sleep(1);
        }
        Redis::setEx($key, 4 * 60 * 60, json_encode($excArray));
    }
}