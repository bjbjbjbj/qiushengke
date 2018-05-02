<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/3/14
 * Time: 下午5:14
 */

namespace App\Http\Controllers\PC\Match;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Controllers\PC\Anchor\AnchorRoomController;
use App\Http\Controllers\PC\CommonTool;
use App\Http\Controllers\PC\FileTool;
use App\Models\QSK\Anchor\AnchorRoom;
use App\Models\QSK\Anchor\AnchorRoomMatches;
use App\Models\QSK\Match\MatchLive;
use App\Models\QSK\Match\MatchLiveChannel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LiveController extends BaseController{
    /**
     * 静态化
     * @param Request $request
     * @param $sport
     * @param $mid
     */
    public function staticLiveDetail(Request $request,$sport,$mid){
        $first = substr($mid,0,2);
        $second = substr($mid,2,2);
        if ($sport == 1){
            $html = $this->liveDetail($request,$first,$second,$mid);
            if (isset($html) && strlen($html) > 0) {
                $path = CommonTool::matchLivePathWithId($mid,$sport);
                Storage::disk("public")->put($path, $html);
            }
        }
        else{
            $html = $this->liveDetail_bk($request,$first,$second,$mid);
            if (isset($html) && strlen($html) > 0) {
                $path = CommonTool::matchLivePathWithId($mid,$sport);
                Storage::disk("public")->put($path, $html);
            }
        }
    }

    /**
     * 静态化比赛对应channel json
     * @param Request $request
     * @param $sport
     * @param $mid
     */
    public function staticLiveDetailJson(Request $request,$sport,$mid){
        $json = $this->_getChannelsByMidWithDB($mid,$sport);
        $json = json_encode($json);
        if ($json && strlen($json) > 0){
            Storage::disk('public')->put('/json/live/'.($sport == 1 ? 'foot':'basket').'/'.$mid.'.json',$json);
        }

        //聊天室json,默认生成吧,免得404
        $first = substr($mid,0,2);
        $second = substr($mid,2,2);
        $path = '/chat/json/'.($sport == 1 ? '1':'2').'/'.$first.'/'.$second.'/'.$mid.'_t.json';
        if (!Storage::disk('public')->exists($path)){
            $json = array();
            $json = json_encode($json);
            Storage::disk('public')->put($path,$json);
        }
        $path = '/chat/json/'.($sport == 1 ? '1':'2').'/'.$first.'/'.$second.'/'.$mid.'.json';
        if (!Storage::disk('public')->exists($path)){
            $json = array();
            $json = json_encode($json);
            Storage::disk('public')->put($path,$json);
        }
    }

    /**
     * 静态化rid对应房间json
     * @param Request $request
     * @param $rid roomid-matchid-sport
     */
    public function staticChannelJson(Request $request,$rid){
        $controller = new AnchorRoomController();
        $json = $controller->liveUrl($request,$rid);
        if ($json && $json->getStatusCode() == 200) {
            $json = $json->getData();
            if (isset($json)) {
                Storage::disk('public')->put('/json/live/channel/' . $rid . '.json', json_encode($json));
            }
        }

        //手机
        $request->merge(['mobile'=>1]);
        $json = $controller->liveUrl($request,$rid);
        if ($json && $json->getStatusCode() == 200) {
            $json = $json->getData();
            if (isset($json)) {
                Storage::disk('public')->put('/json/live/channel/mobile/' . $rid . '.json', json_encode($json));
            }
        }
    }

    /**
     * 爱看球内容版
     * 静态化rid对应房间json
     * @param Request $request
     * @param $rid
     */
    public function staticAKQChannelJson(Request $request,$rid){
        //电脑
        $room = MatchLiveChannel::query()->find($rid);
        if (!isset($room) || $room->show == MatchLiveChannel::kShow) {
            Storage::disk('public')->put('/json/live/AKQchannel/'.$rid.'.json',json_encode(array('code'=>-1, 'message'=>'no channel')));
        }
        $url = $room->content;
        if ($url && strlen($url) > 0){
            Storage::disk('public')->put('/json/live/AKQchannel/'.$rid.'.json',json_encode(array('code'=>0,'type'=>$room->type,'url'=>$url,'player'=>$room->player)));
        }
        else{
            Storage::disk('public')->put('/json/live/AKQchannel/'.$rid.'.json',json_encode(array('code'=>-1, 'message'=>'no channel')));
        }

        //手机
        $room = MatchLiveChannel::query()->find($rid);
        if (!isset($room) || $room->show == MatchLiveChannel::kShow) {
            Storage::disk('public')->put('/json/live/AKQchannel/mobile/'.$rid.'.json',json_encode(array('code'=>-1, 'message'=>'no channel')));
        }
        $url = $room->content;
        if ($url && strlen($url) > 0){
            Storage::disk('public')->put('/json/live/AKQchannel/mobile/'.$rid.'.json',json_encode(array('code'=>0,'type'=>$room->type,'url'=>$url,'player'=>$room->player)));
        }
        else{
            Storage::disk('public')->put('/json/live/AKQchannel/mobile/'.$rid.'.json',json_encode(array('code'=>-1, 'message'=>'no channel')));
        }
    }

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
        return view('pc.live.live',$this->html_var);
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
        return view('pc.live.live_bk',$this->html_var);
    }

    /****************** 播放器player相关 ********************/
    /**
     * 播放器
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function player(Request $request){
        return view('pc.live.player',$this->html_var);
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
        //爱看球频道
        $ams = MatchLive::where('match_id',$mid)
            ->join('match_live_channels',function ($q){
                $q->where('match_live_channels.show',MatchLiveChannel::kShow);
                $q->on('match_live_channels.live_id','match_lives.id');
            })
            ->where('sport',$sport)
            ->select('match_live_channels.*')
            ->get();
        $ams = $ams->toArray();

        //主播频道
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
        $room = $room->toArray();
        $result = array_merge($ams,$room);
        return $result;
    }
}