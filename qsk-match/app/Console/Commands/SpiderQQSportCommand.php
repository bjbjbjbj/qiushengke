<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/2/28
 * Time: 下午5:00
 */

namespace App\Console\Commands;


use App\Http\Controllers\QQSport\SpiderQQSportController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class SpiderQQSportCommand extends Command
{


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spider_input_qqsport:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自动爬腾讯体育';

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
        $lz = new SpiderQQSportController();
        $lz->spiderFootball();
        $lz->spiderBasketBall();
    }



}