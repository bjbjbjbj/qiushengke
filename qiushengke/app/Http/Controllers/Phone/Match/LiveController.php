<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/3/30
 * Time: 上午11:35
 */
namespace App\Http\Controllers\Phone\Match;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Controllers\PC\FileTool;
use App\Http\Controllers\PC\Match\MatchDetailController;
use App\Models\QSK\Anchor\AnchorRoomMatches;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LiveController extends BaseController{
    /**
     * 足球直播终端
     * @param Request $request
     * @param $first
     * @param $second
     * @param $mid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function liveDetail(Request $request,$first,$second,$mid){
        $this->html_var['match'] = MatchDetailController::matchDetailData($mid,'match');
        $this->html_var['tech'] = MatchDetailController::matchDetailData($mid,'tech');
        $this->html_var['roll'] = MatchDetailController::matchDetailData($mid,'roll');
        $this->html_var['sport'] = 1;
        //直播
        $this->html_var['lives'] = $this->_getChannelsByMid($mid,1);
        return view('phone.live.live',$this->html_var);
    }

    /**
     * 篮球直播终端
     * @param Request $request
     * @param $first
     * @param $second
     * @param $mid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function liveDetail_bk(Request $request,$first,$second,$mid){
        $this->html_var['match'] = MatchDetailController::matchDetailData($mid,'match',2);
        if (is_null($this->html_var['match']))
        {
            abort(404);
        }
        $this->html_var['tech'] = MatchDetailController::matchDetailData($mid,'tech',2);
        $this->html_var['roll'] = MatchDetailController::matchDetailData($mid,'roll',2);
        $this->html_var['players'] = MatchDetailController::matchDetailData($mid,'player',2);
        $this->html_var['sport'] = 2;
        $this->html_var['lives'] = $this->_getChannelsByMid($mid,2);
        return view('phone.live.live_bk',$this->html_var);
    }

    /**
     * 根据比赛id获取有什么channels
     * @param $mid
     * @param int $sport
     * @return \Illuminate\Support\Collection|mixed
     */
    private function _getChannelsByMid($mid,$sport = 1){
        $path = '/json/live/' . ($sport == 1 ?'foot':'basket') . '/' . $mid .'.json';
        if (Storage::disk('public')->exists($path)){
            $json = Storage::disk('public')->get($path);
            return json_decode($json,true);
        }
        else{
            return array();
        }
    }

    /**
     * 根据比赛id获取有什么channels (查表)
     * @param $mid
     * @param $sport
     * @return \Illuminate\Support\Collection
     */
    private function _getChannelsByMidWithDB($mid,$sport){
        $room = AnchorRoomMatches::where('mid',$mid)
            ->where('sport',$sport)
            ->join('anchor_rooms',function ($q){
                $q->on('anchor_room_matches.room_id','anchor_rooms.id');
            })
            ->join('anchors',function ($q){
                $q->on('anchor_rooms.anchor_id','anchors.id');
            })
            ->addSelect('anchor_rooms.*')
            ->addSelect('anchors.name as anchor_name',
                'anchors.icon as anchor_icon',
                'anchors.name as anchor_name')
            ->get();
        return $room;
    }
}