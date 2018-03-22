<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 17/3/2
 * Time: 上午11:58
 */
namespace App\Http\Controllers\Tool;

use Illuminate\Routing\Controller;
use App\Http\Controllers\WinSpider\SpiderMatchTeam;
use App\Http\Controllers\WinSpider\SpiderTeamKingOdds;
use App\Http\Controllers\WinSpider\SpiderTools;

class MatchControllerTool extends Controller
{
//    use SpiderTeamKingOdds, SpiderMatchTeam, SpiderTools;
use SpiderMatchTeam,SpiderTools;
    /**
     * 比赛状态 改变成结束时执行的方法
     */
    public function onStatusToOver($match)
    {
        $this->addMatchOddToTeamOddResult($match);
        $this->matchData(isset($match->win_id)?$match->win_id:$match->id, true);
    }
}