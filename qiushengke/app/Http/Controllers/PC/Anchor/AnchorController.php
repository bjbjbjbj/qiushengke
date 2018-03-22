<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/3/21
 * Time: 下午6:45
 */
namespace App\Http\Controllers\PC\Anchor;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Controllers\PC\FileTool;
use App\Models\QSK\Anchor\Anchor;
use App\Models\QSK\Anchor\AnchorRoom;
use App\Models\QSK\Anchor\AnchorRoomMatches;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class AnchorController extends BaseController{
    public function anchorIndex(Request $request){
        $result = array();
        //推荐
        $anchors = Anchor::where('status', 1)->get();
        $result['anchors'] = $anchors;
        //正在直播
        $livings = AnchorRoom::where('status', 1)->get();
        $result['livings'] = $livings;
        //比赛
        //先拿全部比赛
        $startDate = date('Ymd');
        $football = FileTool::matchListDataJson($startDate,1)['matches'];
        $fids = array();
        foreach ($football as $match){
            $fids[] = $match['mid'];
        }
        $fids[] = 1020706;
        //根据比赛拿直播
        $fLives = AnchorRoomMatches::whereIn('mid',$fids)
            ->where('sport',1)
            ->join('anchor_rooms',function ($q){
                $q->on('anchor_room_matches.room_id','=','anchor_rooms.id');
            })
            ->join('anchors',function ($q){
                $q->on('anchor_rooms.anchor_id','=','anchors.id');
            })
            ->join('matches',function ($q){
                $q->on('matches.id','=','anchor_room_matches.mid');
            })
            ->where('matches.status','>=',0)
            ->addSelect('matches.*')
            ->addSelect('anchors.name as anchors_name')
            ->orderby('matches.time','asc')
            ->orderby('matches.id','desc')
            ->get();
        //整理成比赛+主播数据格式
        foreach ($fLives as $fLive){
            
        }

        //构建推荐比赛数据(足球篮球合并)
        $result = array_merge($result,$this->html_var);
        return view('pc.anchor.index',$result);
    }
}