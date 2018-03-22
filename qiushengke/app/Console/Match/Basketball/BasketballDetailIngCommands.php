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
use App\Http\Controllers\StaticHtml\FootballDetailController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BasketballDetailIngCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bb_ing_detail_cache:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '篮球终端页缓存(进行中) 5分钟一次';

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
        echo '33333';
        return;
        $startDate = date('Ymd');
        $pc_json = FileTool::matchListDataJson($startDate,2);
        if (!isset($pc_json)) {
            return "暂无比赛";
        }
        $matches = isset($pc_json['matches']) ? $pc_json['matches'] : [];
        $index = 0;
        foreach ($matches as $match) {
            $status = $match['status'];
            if ($status > 0 && $index < 30) {//每次只做30个正在进行得比赛终端，其他随缘。
                $id = $match['mid'];
                MatchDetailController::curlToHtml($id,2);
                $index++;
            }
        }
    }
}