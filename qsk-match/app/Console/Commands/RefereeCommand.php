<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/5
 * Time: 19:30
 */

namespace App\Console\Commands;


use App\Http\Controllers\Tip\MatchTipController;
use App\Http\Controllers\WinSpider\SpiderController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class RefereeCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'referee_calculate:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '裁判计算相关';

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
        $spiderController = new SpiderController();
        $spiderController->spiderRefereeResult();
    }

}