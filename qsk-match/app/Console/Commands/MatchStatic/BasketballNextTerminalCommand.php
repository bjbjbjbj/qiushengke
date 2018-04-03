<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/5
 * Time: 19:30
 */

namespace App\Console\Commands\MatchStatic;


use App\Http\Controllers\Statistic\Schedule\ScheduleDataController;
use App\Http\Controllers\Statistic\Terminal\MatchTerminalController;
use App\Models\LiaoGouModels\MatchLive;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class BasketballNextTerminalCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'static_basket_next_terminal:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '篮球球次日比赛终端。';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $controller = new MatchTerminalController();
        $controller->onStatic(new Request(),'date', MatchLive::kSportBasketball, date('Ymd', strtotime('+1 day')), 6);
    }

}