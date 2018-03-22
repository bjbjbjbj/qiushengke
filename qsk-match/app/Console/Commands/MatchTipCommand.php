<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/5
 * Time: 19:30
 */

namespace App\Console\Commands;


use App\Http\Controllers\Tip\MatchTipController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class MatchTipCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'match_tip:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '计算半全场、裁判数据定时任务。';

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
        $matchController = new MatchTipController();
        $matchController->halfFullCourtTip(new Request());//计算半全场数据
        $matchController->refereeTip(new Request());//计算裁判数据
    }

}