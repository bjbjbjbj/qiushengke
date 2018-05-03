<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/3/21
 * Time: 下午6:45
 */
namespace App\Http\Controllers\PC\Anchor;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Controllers\PC\CommonTool;
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
    public function staticIndex(Request $request){
        $html = $this->anchorIndex($request);
        if ($html && strlen($html) > 0){
            Storage::disk('public')->put('/anchor/index.html',$html);
        }
    }

    public function anchorIndex(Request $request){
        $result = array();
        //推荐
        $anchors = Anchor::where('status', 1)->get();
        $result['anchors'] = $anchors;

        //正在直播和右侧的推荐都依赖这堆比赛
        $FResult = $this->_matches(1);
        $BResult = $this->_matches(2);

        //正在直播,分开足球篮球
        //比赛正在进行或预约开播时间已经开始的

        $livings = $this->_livingAnchor($FResult,1);
        $result['f_livings'] = $livings;
        $livings = $this->_livingAnchor($BResult,2);
        $result['b_livings'] = $livings;

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

    //获取正在开播主播
    private function _livingAnchor($FResult,$sport){
        $livingMids = array();
        $readyMids = array();
        foreach ($FResult as $match){
            if ($match['status'] == 0){
                $readyMids[] = $match['mid'];
            }
            else if ($match['status'] > 0){
                $livingMids[] = $match['mid'];
            }
        }
        $livings = AnchorRoom::join('anchor_room_matches',function ($q) use($sport){
            $q->on('anchor_rooms.id','=','anchor_room_matches.room_id');
            $q->where('anchor_room_matches.sport',$sport);
        })
            ->where(function ($q) use($livingMids){
                $q->whereIn('anchor_room_matches.mid',$livingMids);
            })
            ->orwhere(function ($q) use($readyMids){
                $q->whereIn('anchor_room_matches.mid',$readyMids)
                    ->where('anchor_room_matches.start_time','<',date('Y-m-d H:i:s'));
            })
            ->addselect('anchor_room_matches.sport as sport')
            ->addselect('anchor_room_matches.mid as mid')
            ->addselect('anchor_rooms.*')->get();
        ;
        return $livings;
    }

    //获取今天未开始或者正在打的比赛
    private function _matches($sport){
        $matchStr = $sport == 1 ? 'matches' : 'basket_matches';
        $teamStr = $sport == 1 ? 'teams' : 'basket_teams';
        //比赛
        //先拿全部比赛
        $startDate = date('Ymd');
        $football = FileTool::matchListDataJson($startDate,$sport)['matches'];
        if (is_null($football)){
            return array();
        }
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
            ->leftjoin($teamStr.' as hteam',function ($q)use($matchStr){
                $q->on($matchStr.'.hid','hteam.id');
            })
            ->leftjoin($teamStr.' as ateam',function ($q)use($matchStr){
                $q->on($matchStr.'.aid','ateam.id');
            })
            ->addSelect($matchStr.'.*')
            ->addSelect('hteam.icon as h_icon')
            ->addSelect('ateam.icon as a_icon')
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
                $item['h_icon'] = $sport == 1 ?$fLive['h_icon'] : CommonTool::getIconBK($fLive['h_icon']);
                $item['hname'] = $fLive['hname'];
                $item['a_icon'] = $sport == 1 ?$fLive['a_icon'] : CommonTool::getIconBK($fLive['a_icon']);
                $item['aname'] = $fLive['aname'];
                $item['lname'] = isset($fLive['lname'])?$fLive['lname']:$fLive['win_lname'];
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