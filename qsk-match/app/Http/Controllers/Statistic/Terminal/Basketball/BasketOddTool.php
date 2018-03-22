<?php
/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/2/28
 * Time: 17:55
 */

namespace App\Http\Controllers\Statistic\Terminal\Basketball;


use App\Http\Controllers\Statistic\OddCalculateTool;
use App\Http\Controllers\Statistic\StatisticFileTool;
use App\Models\LiaoGouModels\Banker;
use App\Models\LiaoGouModels\BasketOdd;
use App\Models\LiaoGouModels\MatchLive;
use Illuminate\Support\Facades\DB;

trait BasketOddTool
{
    public function oddIndex($mid) {
        $bankers = BasketOdd::where('mid',$mid)
            ->whereIn('cid',[2, 5, 8])
            ->groupby('cid')
            ->select('cid')
            ->get();

        $ids = array();
        foreach ($bankers as $tmp){
            $ids[] = $tmp['cid'];
        }
        $bankers = Banker::query()->select('id', 'name', 'rank')
            ->whereIn('id',$ids)
            ->orderBy(DB::raw('FIELD(id, 2, 5, 8)'))
            ->get();

        $odds = array();
        if (count($bankers) > 0){
            $odds = BasketOdd::query()->select('cid', 'type', 'up1', 'up2', 'middle1', 'middle2', 'down1', 'down2')
                ->where('mid',$mid)
                ->where('type','<',4)
                ->get();
        }

        foreach ($odds as $odd) {
            $cid = $odd->cid;
            $type = $odd->type;

            $odd->up1 = OddCalculateTool::formatOddItem($odd, 'up1');
            $odd->up2 = OddCalculateTool::formatOddItem($odd, 'up2');
            $odd->down1 = OddCalculateTool::formatOddItem($odd, 'down1');
            $odd->down2 = OddCalculateTool::formatOddItem($odd, 'down2');

            $odd->makeHidden(['cid', 'type']);
            foreach ($bankers as $banker) {
                if ($banker['id'] == $cid) {
                    if ($type == 1) {
                        $banker['asia'] = $odd;
                    } elseif ($type == 2) {
                        $banker['goal'] = $odd;
                    } elseif ($type == 3) {
                        $banker['ou'] = $odd;
                    }
                }
            }
        }

        if (isset($bankers) && count($bankers) > 0) {
            StatisticFileTool::putFileToTerminal($bankers, MatchLive::kSportBasketball, $mid, 'odd');
        } else {
            $bankers = null;
        }

        return $bankers;
    }
}