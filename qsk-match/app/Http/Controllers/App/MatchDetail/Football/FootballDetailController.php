<?php
/**
 * Created by PhpStorm.
 * User: ricky
 * Date: 2018/2/7
 * Time: 12:58
 */

namespace App\Http\Controllers\App\MatchDetail\Football;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FileTool;
use App\Models\LiaoGouModels\Banker;
use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\MatchLive;
use App\Models\LiaoGouModels\MatchLiveChannel;
use App\Models\LiaoGouModels\Odd;
use App\Models\LiaoGouModels\Team;
use Illuminate\Support\Facades\DB;

class FootballDetailController extends Controller
{
    public function getTabs() {
        return ['match', 'base', 'event', 'odd', 'corner', 'style', 'oddIndex', 'sameOdd'];
    }

    //比赛基本信息
    public function match($id, $date) {
        $match = Match::query()->find($id);
        $data['mid'] = $match->id;
        $data['sport'] = MatchLive::kSportFootball;
        $data['current_time'] = $match->getCurMatchTime(true);
        $data['hname'] = $match->hname;
        $data['aname'] = $match->aname;
        $data['time'] = strtotime($match->time);
        $data['status'] = $match->status;
        $data['statusStr'] = $match->getStatusText();
        $data['hscore'] = $match->hscore;
        $data['ascore'] = $match->ascore;
        $data['hscorehalf'] = $match->hscorehalf;
        $data['ascorehalf'] = $match->ascorehalf;
        $data['hicon'] = Team::getIconById($match->hid);
        $data['aicon'] = Team::getIconById($match->aid);
        $data['league'] = isset($match->leagueData) ? $match->leagueData->name : $match->win_lname;
        $data['round'] = $match->round;
        $data['hrank'] = $match->hrank;
        $data['arank'] = $match->arank;

        $data['live'] = intval(MatchLive::getAppMatchLiveCountById($match->id));
        $data['live2'] = intval(MatchLive::getAppMatchLiveCountById($match->id,MatchLive::kSportFootball,MatchLiveChannel::kUseHeitu));

        if (is_null($data) || count($data) <= 0) {
            $data = null;
        }
        $json = response()->json($data);
        FileTool::putFileToMatchDetail($json->getContent(), MatchLive::kSportFootball, $id, 'match', '0');
        return $json;
    }

    //比赛基本信息
    public function base($id, $date) {
        $mController = new MatchDetailController();
        $data = $mController->matchDetailJson($id);

        if (is_null($data) || count($data) <= 0) {
            $data = null;
        }
        $json = response()->json($data);
        FileTool::putFileToMatchDetail($json->getContent(), MatchLive::kSportFootball, $id, 'base', $date);
        return $json;
    }

    /**
     * 比赛终端 事件、比赛数据。
     */
    public function event($id, $date) {
        $base = new MatchDetailController();
        $data = $base->matchBaseData($id);

        if (is_null($data) || count($data) <= 0) {
            $data = null;
        }
        $json = response()->json($data);
        FileTool::putFileToMatchDetail($json->getContent(), MatchLive::kSportFootball, $id, 'event', $date);
        return $json;
    }

    /**
     * 足球赔率
     */
    public function odd($id, $date) {
        $base = new MatchDetailBaseController();
        $data = $base->matchOddBankerData($id);

        if (is_null($data) || count($data) <= 0) {
            $data = null;
        }
        $json = response()->json($data);
        FileTool::putFileToMatchDetail($json->getContent(), MatchLive::kSportFootball, $id, 'odd', $date);

        return $json;
    }

    /**
     * 角球数据
     */
    public function corner($id, $date) {
        $match = Match::query()->find($id);
        if (!isset($match)) {
            return response()->json("[]");
        }
        $cornerController = new MatchDetailCornerController();
        $data = $cornerController->cornerData($match);

        if (is_null($data) || count($data) <= 0) {
            $data = null;
        }
        $json = response()->json($data);
        FileTool::putFileToMatchDetail($json->getContent(), MatchLive::kSportFootball, $id, 'corner', $date);

        return $json;
    }

    /**
     * 比赛双方球队风格数据
     */
    public function style($mid, $date) {
        $mdsc = new MatchDetailStrengthController();
        $data = $mdsc->teamStyleData($mid);

        if (is_null($data) || count($data) <= 0) {
            $data = null;
        }
        $json = response()->json($data);
        FileTool::putFileToMatchDetail($json->getContent(), MatchLive::kSportFootball, $mid, 'style', $date);
        return $json;
    }

    public function oddIndex($mid, $date) {
        $result = [];
        $bankers = Odd::where('mid',$mid)
            ->whereIn('cid',[2, 5, 12, 8, 1])
            ->groupby('cid')
            ->select('cid')
            ->get();

        $ids = array();
        foreach ($bankers as $tmp){
            $ids[] = $tmp['cid'];
        }
        $bankers = Banker::whereIn('id',$ids)
            ->orderBy(DB::raw('FIELD(id, 2, 5, 12, 8, 1)'))
            ->get();

        $odds = array();
        if (count($bankers) > 0){
            $odds = Odd::where('mid',$mid)
                ->where('type','<',4)
                ->get();
        }

        foreach ($odds as $odd) {
            foreach ($bankers as $banker) {
                if ($banker['id'] == $odd['cid']) {
                    if ($odd['type'] == 1) {
                        $banker['asia'] = $odd;
                    } elseif ($odd['type'] == 2) {
                        $banker['goal'] = $odd;
                    } elseif ($odd['type'] == 3) {
                        $banker['ou'] = $odd;
                    }
                }
            }
        }
        $result['bankers'] = $bankers;
        $result['odds'] = $bankers;
        //dump($result);

        if (is_null($result) || count($result) <= 0) {
            $data = null;
        }
        $json = response()->json($result);
        FileTool::putFileToMatchDetail($json->getContent(), MatchLive::kSportFootball, $mid, 'oddIndex', $date);

        return $json;
    }

    /**
     * 比赛的历史同赔
     */
    public function sameOdd($mid, $date) {
        $rest['sameOdd'] = null;
        $rest['sameOdd2'] = null;
        $rest['sameOdd3'] = null;

        //同赔
        $sameOdd = MatchDetailOddController::getSameOdd(1, $mid);
        if (isset($sameOdd['sameOdd']) && count($sameOdd['sameOdd']) > 0) {
            $rest['sameOdd'] = $sameOdd['sameOdd'];
        }
        $sameOdd2 = MatchDetailOddController::getSameOdd(2, $mid);
        if (isset($sameOdd2['sameOdd']) && count($sameOdd2['sameOdd']) > 0) {
            $rest['sameOdd2'] = $sameOdd2['sameOdd'];
        }
        $sameOdd3 = MatchDetailOddController::getSameOdd(3, $mid);
        if (isset($sameOdd2['sameOdd']) && count($sameOdd2['sameOdd']) > 0) {
            $rest['sameOdd3'] = $sameOdd3['sameOdd'];
        }

        if (is_null($rest) || count($rest) <= 0) {
            $data = null;
        }
        $json = response()->json($rest);
        FileTool::putFileToMatchDetail($json->getContent(), MatchLive::kSportFootball, $mid, 'sameOdd', $date);
        return $json;
    }
}