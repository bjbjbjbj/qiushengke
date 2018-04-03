<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/4/3
 * Time: 下午3:36
 */

namespace App\Http\Controllers\Admin\Match;

use App\Models\QSK\Match\BasketLeague;
use App\Models\QSK\Match\BasketMatch;
use App\Models\QSK\Match\League;
use App\Models\QSK\Match\Match;
use App\Models\QSK\Match\MatchLive;
use App\Models\QSK\Match\MatchLiveChannel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class LeagueController extends Controller{

    /**
     * 篮球赛事
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function basketballLeague(Request $request) {
        $l_name = $request->input("l_name");//赛事名称
        $leagues = BasketLeague::where('name','like','%'.$l_name.'%')->get();
        $rest['leagues'] = $leagues;
        $rest['sport'] = 2;
        return view('admin.match.leagues', $rest);
    }

    /**
     * 足球赛事
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function footballLeague(Request $request) {
        $l_name = $request->input("l_name");//赛事名称
        $leagues = League::where('name','like','%'.$l_name.'%')->get();
        $rest['leagues'] = $leagues;
        $rest['sport'] = 1;
        return view('admin.match.leagues', $rest);
    }

    /**
     * 保存赛事
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveLeague(Request $request) {
        $lid = $request->input('lid',0);//比赛id
        $sport = $request->input('sport');//竞技类型
        $color = $request->input('color');//

        //判断参数 开始
        if (empty($color)) {
            return response()->json(['code'=>401, 'msg'=>'必须填写颜色。']);
        }
        if($lid == 0){
            return response()->json(['code'=>401, 'msg'=>'赛事不能为空']);
        }

        if (1 == $sport) {
            $league = League::find($lid);
        }
        else{
            $league = BasketLeague::find($lid);
        }

        if (is_null($league)){
            return response()->json(['code'=>401, 'msg'=>'找不到对应赛事']);
        }

        $color = str_replace('#','',$color);

        $league->color = $color;
        $league->save();

        return response()->json(['code'=>200, 'msg'=>'保存赛事成功']);
    }

    /**
     * 删除线路
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delChannel(Request $request) {
        $id = $request->input('id');
        if (!is_numeric($id)) {
            return response()->json(['code'=>401, 'msg'=>'参数错误']);
        }
        $channel = MatchLiveChannel::query()->find($id);
        if (!isset($channel)) {
            return response()->json(['code'=>403, 'msg'=>'线路不存在']);
        }
        $matchLive = $channel->matchLive;
        $match_id = $matchLive->match_id;
        $sport = $matchLive->sport;
        $exception = DB::transaction(function () use ($channel, $matchLive) {
            $channel->delete();//删除当前线路
            $channels = MatchLive::liveChannels($matchLive->id);
            if (!isset($channels) || count($channels) == 0) {
                $matchLive->delete();//删除直播
            }
        });
        if (isset($exception)) {
            Log::error($exception);
            return response()->json(['code'=>500, 'msg'=>'删除线路失败']);
        }

        return response()->json(['code'=>200, 'msg'=>'删除线路成功']);
    }
}