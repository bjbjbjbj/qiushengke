<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/27
 * Time: 15:40
 */

namespace App\Http\Controllers\PC\Anchor;


use App\Models\QSK\Anchor\AnchorRoom;
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
        $room = AnchorRoom::query()->find($id);
        $mobile = $request->input('mobile', 0);
        if (!isset($room) || $room->status == AnchorRoom::kStatusHide) {
            return response()->json(['code'=>-1, 'message'=>'no room']);
        }
        $isMobile = $mobile == 1 || self::isMobile($request);
        $url = $room->getResource($isMobile);
        if (empty($url)) {
            return response()->json(['code'=>-1, 'message'=>'no channel']);
        }
        return response()->json(['code'=>0, 'url'=>$url]);
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