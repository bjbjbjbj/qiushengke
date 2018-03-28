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
use App\Models\QSK\Match\BasketMatch;
use App\Models\QSK\Match\Match;
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

        $FResult = $this->_matches(1);
        $BResult = $this->_matches(2);
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

    private function _matches($sport){
        $matchStr = $sport == 1 ? 'matches' : 'basket_matches';
        //比赛
        //先拿全部比赛
        $startDate = date('Ymd');
        $football = FileTool::matchListDataJson($startDate,$sport)['matches'];
        $fids = array();
        foreach ($football as $match){
            $fids[] = $match['mid'];
        }
        //根据比赛拿直播

        if ($sport == 1){
            $query = Match::where($matchStr.'.status','>=',0);
        }
        else{
            $query = BasketMatch::where($matchStr.'.status','>=',0);
        }

        //把正在比赛的筛出来
        $matches = $query
            ->addSelect($matchStr.'.*')
            ->selectRaw('unix_timestamp('.$matchStr.'.time) as time')
            ->orderby($matchStr.'.time','asc')
            ->orderby($matchStr.'.id','desc')
            ->get();
        $fids = array();
        $result = array();
        foreach ($matches as $match){
            $fids[] = $match['id'];
            $result[$match['id']] = $match;
        }

        $fLives = AnchorRoomMatches::whereIn('mid',$fids)
            ->where('sport',$sport)
            ->join('anchor_rooms',function ($q){
                $q->on('anchor_room_matches.room_id','=','anchor_rooms.id');
            })
            ->join('anchors',function ($q){
                $q->on('anchor_rooms.anchor_id','=','anchors.id');
            })
            ->addSelect('anchor_room_matches.mid as mid')
            ->addSelect('anchors.name as anchors_name')
            ->addSelect('anchor_rooms.name as anchor_room',
                'anchor_rooms.id as anchor_room_id')
            ->get();
        //整理成比赛+主播数据格式
        $tmpMid = 0;

        //match live 结合,分库了,不能一次join
        $finalResult = array();
        foreach ($fLives as $fLive){
            $result[$fLive['mid']]['mid'] = $fLive['mid'];
            $result[$fLive['mid']]['anchors_name'] = $fLive['anchors_name'];
            $result[$fLive['mid']]['anchor_room'] = $fLive['anchor_room'];
            $result[$fLive['mid']]['anchor_room_id'] = $fLive['anchor_room_id'];
            $finalResult[] = $result[$fLive['mid']];
        }

        $FResult = array();
        foreach ($finalResult as $fLive){
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
                $item['sport'] = $sport;
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
        return $FResult;
    }
}