<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/2/8
 * Time: 18:00
 */

namespace App\Console\Match\Basketball;

use App\Http\Controllers\CacheInterface\FootballInterface;
use App\Http\Controllers\PC\FileTool;
use App\Http\Controllers\PC\Index\FootballController;
use App\Http\Controllers\PC\Match\MatchDetailController;
use App\Http\Controllers\Phone\Detail\BasketballController;
use App\Http\Controllers\StaticHtml\FootballDetailController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class BasketballDetailEndCommands extends Command
{
    const PC_REDIS_KEY = 'PC_BASKETBALL_DETAIL_SCHEDULE_KEY';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bb_detail_end_cache:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '篮球终端页缓存 10分钟一次 已结束的';

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
        $ch = curl_init();
        //$url = env('MATCH_URL') . "/api/qsk/basketball/matches.json?lid=" . $lid;
        $url = env('MATCH_URL')."/static/change/2/score.json";
        echo 'url = ' . $url . "\n";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);//5秒超时
        $json = curl_exec ($ch);
        curl_close ($ch);
        $matches = json_decode($json,true);

        $index = 0;
        foreach ($matches as $mid=>$match) {
            $status = $match['status'];
            if ($status == -1 && $index < 20) {//每次只做30个正在进行得比赛终端，其他随缘。
                BasketballController::curlToHtml($mid);
                $index++;
            }
        }
    }
}