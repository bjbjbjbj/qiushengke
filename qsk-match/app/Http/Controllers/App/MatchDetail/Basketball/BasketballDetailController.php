<?php
/**
 * Created by PhpStorm.
 * User: ricky
 * Date: 2018/2/7
 * Time: 12:58
 */

namespace App\Http\Controllers\App\MatchDetail\Basketball;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FileTool;
use App\Models\LiaoGouModels\BasketMatch;
use App\Models\LiaoGouModels\BasketTeam;
use App\Models\LiaoGouModels\MatchLive;
use App\Models\LiaoGouModels\MatchLiveChannel;

class BasketballDetailController extends Controller
{
    public function getTabs() {
        return ['match','base'];
    }

    //比赛基本信息
    public function match($id, $date) {
        $match = BasketMatch::query()->find($id);
        $data['mid'] = $match->id;
        $data['sport'] = MatchLive::kSportBasketball;
        $data['live_time_str'] = $match->getMatchCurTime(true);
        $data['hname'] = $match->hname;
        $data['aname'] = $match->aname;
        $data['time'] = strtotime($match->time);
        $data['status'] = $match->status;
        $data['statusStr'] = $match->getStatusText();
        $data['hscore'] = $match->hscore;
        $data['ascore'] = $match->ascore;
        $data['hicon'] = BasketTeam::getIconByTid($match->hid);
        $data['aicon'] = BasketTeam::getIconByTid($match->aid);
        $data['league'] = isset($match->league) ? $match->league->name : $match->win_lname;
        $data['hrank'] = $match->hrank;
        $data['arank'] = $match->arank;
        //小分
        $data['hscore_1st'] = $match->hscore_1st;
        $data['ascore_1st'] = $match->ascore_1st;
        $data['hscore_2nd'] = $match->hscore_2nd;
        $data['ascore_2nd'] = $match->ascore_2nd;
        $data['hscore_3rd'] = $match->hscore_3rd;
        $data['ascore_3rd'] = $match->ascore_3rd;
        $data['hscore_4th'] = $match->hscore_4th;
        $data['ascore_4th'] = $match->ascore_4th;
        $data['h_ot'] = (isset($match->h_ot) && strlen($match->h_ot) > 0) ? explode(',', $match->h_ot) : null;
        $data['a_ot'] = (isset($match->a_ot) && strlen($match->a_ot) > 0) ? explode(',', $match->a_ot) : null;

        $data['live'] = intval(MatchLive::getAppMatchLiveCountById($match->id, MatchLive::kSportBasketball));
        $data['live2'] = intval(MatchLive::getAppMatchLiveCountById($match->id, MatchLive::kSportBasketball,MatchLiveChannel::kUseHeitu));

        if (is_null($data) || count($data) <= 0) {
            $data = null;
        }
        $json = response()->json($data);
        FileTool::putFileToMatchDetail($json->getContent(), MatchLive::kSportBasketball, $id, 'match', '0');
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
        FileTool::putFileToMatchDetail($json->getContent(), MatchLive::kSportBasketball, $id, 'base', $date);
        return $json;
    }
}