<?php
namespace App\Http\Controllers\Statistic\Terminal;
use App\Http\Controllers\Statistic\Terminal\Basketball\BasketTerminalController;
use App\Http\Controllers\Statistic\Terminal\Football\FootballTerminalController;
use App\Models\LiaoGouModels\BasketMatch;
use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\MatchLive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;


/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/2/28
 * Time: 17:30
 */
class MatchTerminalController
{
    public function index($sport, $a, $b, $mid, $tab) {
        if ($this->isIndexForMid($a, $b, $mid)) {
            if ($sport == MatchLive::kSportBasketball) {
                $controller = new BasketTerminalController();
            } else {
                $controller = new FootballTerminalController();
            }
            if (isset($controller)) {
                $data = $controller->getData($mid, $tab);
                return Response::json($data);
            }
        }
        return null;
    }

    private function isIndexForMid($a, $b, $mid) {
        $index1 = substr($mid, 0, 2);
        $index2 = substr($mid, 2, 2);

        return ($a == $index1) && ($index2 == $b);
    }

    public function onStatic(Request $request, $type, $sport, $key, $saveCount = 6) {
        $isBasket = $sport == MatchLive::kSportBasketball;
        if ($isBasket) {
            $controller = new BasketTerminalController();
        } else {
            $controller = new FootballTerminalController();
        }
        if ($type == 'match') {
            $controller->analyseDataStatic($key, [1,2], true);
        } else if ($type == 'date') {
            $isReset = false;
            if (isset($request)) {
                $isReset = $request->input('reset', false);
                $saveCount = $request->input('count', $saveCount);
            }
            $controller->onMatchAnalyseDataStatic($key, $saveCount, $isReset);
        } else if ($type == 'tech') {
            if ($isBasket) {
                $match = BasketMatch::query()->find($key);
            } else {
                $match = Match::query()->find($key);
            }
            if (isset($match)) {
                $controller->matchDataStatic($sport, $match->win_id, $match->id);
            } else {
                echo "the key of input is error!<br>";
            }
        } else {
            echo "the type of input is error!<br>";
        }
    }
}