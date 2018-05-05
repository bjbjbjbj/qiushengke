<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/27
 * Time: 15:40
 */

namespace App\Http\Controllers\PC\Anchor;


use App\Models\QSK\Anchor\AnchorRoom;
use App\Models\QSK\Anchor\AnchorRoomMatches;
use App\Models\QSK\Match\BasketMatch;
use App\Models\QSK\Match\Match;
use App\Models\QSK\Match\MatchLive;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AnchorRoomController extends Controller
{

    /**
     * 根据房间号获取直播间的源链接
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function liveUrl(Request $request, $id) {
        $param = explode('-', $id);

        if (count($param) != 3) {
            return response()->json(['code'=>-1, 'message'=>'param error']);
        }

        $room_id = $param[0];
        $mid = $param[1];
        $sport = $param[2];

        $armQuery = AnchorRoomMatches::query()->where('room_id', $room_id)->where('mid', $mid)->where('sport', $sport);
        $arm = $armQuery->first();
        if (!isset($arm)) {
            return response()->json(['code'=>-1, 'message'=>'no room']);
        }

        $room = AnchorRoom::query()->find($room_id);
        $mobile = $request->input('mobile', 0);
        if (!isset($room) || $room->status == AnchorRoom::kStatusHide) {
            return response()->json(['code'=>-1, 'message'=>'no room']);
        }

        $isMobile = $mobile == 1 || self::isMobile($request);
        $url = $room->getResource($isMobile);
        if (empty($url)) {
            return response()->json(['code'=>-1, 'message'=>'no channel']);
        }
        $match = null;
        if ($sport == MatchLive::kSportFootball) {
            $match = Match::query()->find($mid);
        } else if ($sport == MatchLive::kSportBasketball) {
            $match = BasketMatch::query()->find($mid);
        }
        $match_time = null;
        $start_time = null;
        if (isset($arm->start_time)) {
            $start_time = strtotime($arm->start_time);
        }
        if (isset($match)) {
            $match_time = strtotime($match->time);
        }
        return response()->json(['code'=>0, 'type'=>$room['type'] , 'url'=>$url, 'start_time'=>$start_time, 'match_time'=>$match_time]);
    }


    public static function isMobile(Request $request) {
        $userAgent = $request->header('user_agent', '');
        if ($userAgent) {
            $userAgent = $request->header('user_agent', '');
            if (preg_match("/(iPad).*OS\s([\d_]+)/", $userAgent)) {
                return true;
            } else if (preg_match("/(iPhone\sOS)\s([\d_]+)/", $userAgent)){
                return true;
            } else if (preg_match("/(Android)\s+([\d.]+)/", $userAgent)){
                return true;
            }
        }
        return false;
    }

}