<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/3/21
 * Time: 下午5:27
 */

namespace App\Console\League;

use App\Http\Controllers\CacheInterface\FootballInterface;
use App\Http\Controllers\PC\FileTool;
use App\Http\Controllers\PC\Index\FootballController;
use App\Http\Controllers\PC\League\LeagueController;
use App\Http\Controllers\PC\Match\MatchDetailController;
use App\Http\Controllers\StaticHtml\FootballDetailController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class FootballCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'league_foot:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '足球赛事专题';

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
        $controller = new LeagueController();
        $controller->staticFoot(new Request());
    }
}