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

class FootballScheduleCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'static_foot_schedule:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '足球昨日和明日的比赛列表。';

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
        $controller->onMatchesStaticByDate(MatchLive::kSportFootball, date('Y-m-d', strtotime('-1 day')));
        $controller->onMatchesStaticByDate(MatchLive::kSportFootball, date('Y-m-d', strtotime('+1 day')));
    }

}