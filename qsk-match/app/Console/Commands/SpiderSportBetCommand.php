<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/6
 * Time: 18:30
 */

namespace App\Console\Commands;


use App\Http\Controllers\Ballbar\SpiderBallbarController;
use App\Http\Controllers\WinSpider\SpiderController;
use Illuminate\Console\Command;

class SpiderSportBetCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spider_sport365:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '爬sport365直播';

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
        //
        //Log::info(date("Y-m-d H:i:s") . "测试");
        $spiderController = new \App\Http\Controllers\Sportstream365\SpiderController();
        $spiderController->spider365();
        $spiderController->spider365BasketBall();
    }

}