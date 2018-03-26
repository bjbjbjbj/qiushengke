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
            ->selectRaw('unix_timestamp(matches.time) as time')
            ->addSelect('anchors.name as anchors_name')
            ->addSelect('anchor_rooms.name as anchor_room',
                'anchor_rooms.id as anchor_room_id')
            ->orderby('matches.time','asc')
            ->orderby('matches.id','desc')
            ->get();
        //整理成比赛+主播数据格式
        $tmpMid = 0;
        $FResult = array();
        foreach ($fLives as $fLive){
            $mid = $fLive['id'];
            if ($mid == $tmpMid){
                //已经有比赛了,加主播
                $item = $FResult[count($FResult) - 1];
                $item['anchors'][] = array(
                    'name'=>$fLive['anchors_name'],
                    'room_id'=>$fLive['anchor_room_id']);
                $FResult[count($FResult) - 1] = $item;
            }
            else{
                //没有比赛,创比赛
                $item = array();
                $item['mid'] = $fLive['id'];
                $item['sport'] = 1;
                $item['lid'] = $fLive['lid'];
                $item['round'] = $fLive['round'];
                $item['h_icon'] = $fLive['h_icon'];
                $item['hname'] = $fLive['hname'];
                $item['a_icon'] = $fLive['a_icon'];
                $item['aname'] = $fLive['aname'];
                $item['lname'] = $fLive['lname'];
                $item['lid'] = $fLive['lid'];
                $item['time'] = $fLive['time'];
                $item['status'] = $fLive['status'];
                $item['anchors'] = array();
                $item['anchors'][] = array(
                    'name'=>$fLive['anchors_name'],
                    'room_id'=>$fLive['anchor_room_id']);
                $tmpMid = $mid;
                $FResult[] = $item;
            }
        }

        $basketball = FileTool::matchListDataJson($startDate,2)['matches'];
        $bids = array();
        foreach ($basketball as $match){
            $bids[] = $match['mid'];
        }
        //根据比赛拿直播
        $bLives = AnchorRoomMatches::whereIn('mid',$bids)
            ->where('sport',2)
            ->join('anchor_rooms',function ($q){
                $q->on('anchor_room_matches.room_id','=','anchor_rooms.id');
            })
            ->join('anchors',function ($q){
                $q->on('anchor_rooms.anchor_id','=','anchors.id');
            })
            ->join('basket_matches as matches',function ($q){
                $q->on('matches.id','=','anchor_room_matches.mid');
            })
            ->where('matches.status','>=',0)
            ->addSelect('matches.*')
            ->selectRaw('unix_timestamp(matches.time) as time')
            ->addSelect('anchors.name as anchors_name')
            ->addSelect('anchor_rooms.name as anchor_room',
                'anchor_rooms.id as anchor_room_id')
            ->orderby('matches.time','asc')
            ->orderby('matches.id','desc')
            ->get();
        //整理成比赛+主播数据格式
        $tmpMid = 0;
        $BResult = array();
        foreach ($bLives as $bLive){
            $mid = $bLive['id'];
            if ($mid == $tmpMid){
                //已经有比赛了,加主播
                $item = $BResult[count($BResult) - 1];
                $item['anchors'][] = array(
                    'name'=>$bLive['anchors_name'],
                    'room_id'=>$bLive['anchor_room_id']);
                $BResult[count($BResult) - 1] = $item;
            }
            else{
                //没有比赛,创比赛
                $item = array();
                $item['mid'] = $bLive['id'];
                $item['sport'] = 2;
                $item['h_icon'] = $bLive['h_icon'];
                $item['hname'] = $bLive['hname'];
                $item['round'] = $bLive['round'];
                $item['a_icon'] = $bLive['a_icon'];
                $item['aname'] = $bLive['aname'];
                $item['lname'] = $bLive['lname'];
                $item['lid'] = $bLive['lid'];
                $item['time'] = $bLive['time'];
                $item['status'] = $bLive['status'];
                $item['anchors'] = array();
                $item['anchors'][] = array(
                    'name'=>$bLive['anchors_name'],
                    'room_id'=>$bLive['anchor_room_id']);
                $tmpMid = $mid;
                $BResult[] = $item;
            }
        }

        $m_Result = array_merge($BResult,$FResult);
        //构建推荐比赛数据(足球篮球合并)
        $resultMatch = array_sort($m_Result, function ($a, $b) {
            $time_a = $a['time'];
            $time_b = $b['time'];
            return $time_a - $time_b;
        });

        $result['matches'] = $resultMatch;

        //构建按赛事分类的比赛
        $leagues = array();
        foreach ($resultMatch as $match){
            $lid = $match['lid'];
            $sport = $match['sport'];
            if (isset($leagues[$sport.'_'.$lid])){
                //已有赛事
                $league = $leagues[$sport.'_'.$lid];
                $league['matches'][] = $match;
                $leagues[$sport.'_'.$lid] = $league;
            }
            else{
                //未有赛事
                $league = array();
                $league['name'] = $match['lname'];
                $league['round'] = $match['round'];
                $league['id'] = $match['lid'];
                $league['matches'][] = $match;
                $leagues[$sport.'_'.$lid] = $league;
            }
        }

        $result['leagues'] = $leagues;

        $result = array_merge($result,$this->html_var);
        return view('pc.anchor.index',$result);
    }
}