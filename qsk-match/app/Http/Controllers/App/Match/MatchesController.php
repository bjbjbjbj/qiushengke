<?php
namespace App\Http\Controllers\App\Match;

use App\Http\Controllers\App\AppCommonResponse;
use App\Http\Controllers\FileTool;
use App\Models\LiaoGouModels\MatchLive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

/**
 * Created by PhpStorm.
 * User: ricky
 * Date: 2018/1/3
 * Time: 17:30
 */
class MatchesController
{
    use MatchCommonTool;

    public function index(Request $request = null, $date, $sport, $type) {
        if (!$this->isSport($sport)) {
            return null;
        }
        if ($sport == MatchLive::kSportBasketball) {
            $controller = new BasketImmediateController();
        } elseif ($sport == MatchLive::kSportFootball) {
            $controller = new MatchImmediateController();
        }
        if (!isset($controller)) {
            return null;
        }
        if(is_null($date) || date('Ymd', strtotime($date)) != $date ) {
            $date = date('Ymd');
        }
        if (is_null($request)) {
            $request = new Request();
        }
        $request->merge(['app' => 1, "date"=>date('Y-m-d', strtotime($date)), 'type'=>$type]);

        $data = $controller->getMatch($request);
        return Response::json($data);
    }

    //=================静态化调用方法=========================

    public function onAllMatchStatistic($sport) {
        $types = $this->getMatchType($sport);
        foreach (range(-1, 1) as $index) {
            $date = date('Ymd', strtotime($index." days"));
            foreach ($types as $type) {
                $this->onMatchStatistic($date, $sport, $type);
            }
        }
        echo 'match statistic save success! <br>';
    }

    private function onMatchStatistic($date, $sport, $type) {

        if(is_null($date) || date('Ymd', strtotime($date)) != $date ) {
            $date = date('Ymd');
        }
        $data = $this->index(null, $date, $sport, $type);

        if (isset($data)) {
            FileTool::putFileToMatches($data->getContent(), $sport, $type, $date);
        }
    }
}