<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/2/24
 * Time: 16:08
 */

namespace App\Console\Commands;


use App\Http\Controllers\LongZhu\LongZhuController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class SpiderLongZhuCommand extends Command
{


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spider_input_lz:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自动填入龙珠直播间的比赛线路';

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
        $lz = new LongZhuController();
        $lz->spiderFootball(new Request());
    }



}