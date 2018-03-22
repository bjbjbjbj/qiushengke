<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/5
 * Time: 19:30
 */

namespace App\Console\Commands\MatchStatic;


use App\Http\Controllers\Statistic\Schedule\ScheduleDataController;
use App\Models\LiaoGouModels\MatchLive;
use Illuminate\Console\Command;

class BasketballImmediateCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'static_basket_immediate:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '篮球当天比赛列表。';

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
        $controller = new ScheduleDataController();
        $controller->onMatchesStaticByDate(MatchLive::kSportBasketball);
    }

}