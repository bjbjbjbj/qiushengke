<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/3/20
 * Time: 上午11:47
 */

namespace App\Console\Match;


use App\Http\Controllers\PC\League\LeagueController;
use App\Http\Controllers\PC\Match\MatchController;
use App\Http\Controllers\PC\Match\MatchDetailController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class HourCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hour_cache:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '一小时一次';

    /**
     * Create a new command instance.
     * HotMatchCommand constructor.
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
        MatchDetailController::staticHour();

        LeagueController::flushSubLeagueJson();
    }
}