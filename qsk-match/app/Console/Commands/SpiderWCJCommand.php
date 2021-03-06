<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/6
 * Time: 18:30
 */

namespace App\Console\Commands;


use App\Http\Controllers\Ballbar\SpiderBallbarController;
use App\Http\Controllers\TTZB\SpiderTTZBController;
use App\Http\Controllers\WinSpider\SpiderController;
use App\Http\Controllers\WuChaJian\SpiderWCJController;
use Illuminate\Console\Command;

class SpiderWCJCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spider_wcj:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '爬无插件';

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
        $spiderController = new SpiderWCJController();
        $spiderController->spiderLiveData();
    }

}