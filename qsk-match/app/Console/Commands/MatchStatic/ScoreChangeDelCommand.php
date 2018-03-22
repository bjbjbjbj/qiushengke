<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/5
 * Time: 19:30
 */

namespace App\Console\Commands\MatchStatic;


use App\Http\Controllers\Statistic\Change\ScoreChangeController;
use App\Models\LiaoGouModels\MatchLive;
use Illuminate\Console\Command;

class ScoreChangeDelCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'static_score_change_del:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '比分改变删除无效数据。';

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
        $controller = new ScoreChangeController();
        $controller->onUselessScoreDelete(MatchLive::kSportFootball);
        $controller->onUselessScoreDelete(MatchLive::kSportBasketball);
    }

}