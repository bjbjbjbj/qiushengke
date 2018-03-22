<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/5
 * Time: 19:30
 */

namespace App\Console\Commands\MatchStatic;


use App\Http\Controllers\Statistic\Change\OddChangeController;
use App\Http\Controllers\Statistic\Schedule\ScheduleDataController;
use App\Http\Controllers\Statistic\Terminal\MatchTerminalController;
use App\Models\LiaoGouModels\MatchLive;
use Illuminate\Console\Command;

class FootballRollListChangeCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'static_foot_roll_list_change:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '足球滚球盘列表改变。';

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
        $controller = new OddChangeController();
        $controller->rollListChangeStatic(MatchLive::kSportFootball, false);
    }

}