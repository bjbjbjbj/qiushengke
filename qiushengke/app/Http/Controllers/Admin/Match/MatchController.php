<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/1
 * Time: 18:37
 */

namespace App\Http\Controllers\Admin\Match;

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

class MatchController extends Controller
{
    public function todayBasketMatch(Request $request) {
        $t_name = $request->input("t_name");//球队名称
        $l_name = $request->input("l_name");//赛事名称
        $has_live = $request->input('has_live');//是否有直播链接
        $status = $request->input('status');//比赛状态
        $type = $request->input('type');//比赛类型 1：竞彩、2：精简

        $withSelect = true;
        $isMain = false;

        $startDate = date("Y-m-d", strtotime('-1 days'));
        $endDate = date('Y-m-d H:i:s', strtotime('3 days'));

        $sport = MatchLive::kSportBasketball;
        $match_table = 'basket_matches';

        if ($has_live == 1) {//有直播链接
            $query = MatchLive::query();
            $query->join('basket_matches', function ($join) use ($sport, $match_table) {
                $join->on('match_lives.match_id', '=', $match_table . '.id');
                $join->where('match_lives.sport', $sport);
            });
        } else {
            $query = BasketMatch::query();
            $query->leftJoin('match_lives', function ($join) use ($sport, $match_table) {
                $join->on('match_lives.match_id', '=', $match_table . '.id');
                $join->where('match_lives.sport', $sport);
            });

            if ($has_live == 2) {
                //无直播链接
                $query->whereNull('match_lives.id');
            }
            $query->where("time", ">=", $startDate)->where("time", "<", $endDate);
        }

        if ($withSelect) {
            $query->select($match_table . ".*", $match_table .".id as mid", $match_table . ".win_lname as league_name");
        }
        $query->addSelect(['match_lives.sport', 'match_lives.id as live_id']);

        if ($isMain) {
            $query->orderBy('leagues.main', 'desc');
        }
        if (!empty($t_name)) {
            $query->where(function ($orQuery) use ($t_name, $match_table) {
                $orQuery->where($match_table . '.hname', 'like', "%$t_name%");
                $orQuery->orWhere($match_table . '.aname', 'like', "%$t_name%");
            });
        }
        if ($status == 1) {//未开始
            $query->where($match_table . '.status', 0);
        } elseif ($status == 2) {//进行中
            $query->where(function ($orQuery) use ($match_table) {
                $orQuery->where($match_table . '.status', 1);//第一节
                $orQuery->orWhere($match_table.'.status', 2);//第二节
                $orQuery->orWhere($match_table.'.status', 3);//第三节
                $orQuery->orWhere($match_table.'.status', 4);//第四节
                $orQuery->orWhere($match_table.'.status', 5);//加时 第一节
                $orQuery->orWhere($match_table.'.status', 6);//加时 第二节
                $orQuery->orWhere($match_table.'.status', 7);//加时 第三节
                $orQuery->orWhere($match_table.'.status', 8);//加时 第四节
                $orQuery->orWhere($match_table.'.status', 50);//中场休息
            });
        } elseif ($status == 3) {
            $query->where($match_table.'.status', -1);//已结束
        }
        if ($type == 1) {
            $query->whereNotNull($match_table . '.betting_num');
        } /*elseif ($type == 2) {
            $query->where('matches.genre', '&', Match::k_genre_yiji);
        }*/
        if (!empty($l_name)) {
            $query->where($match_table . '.win_lname', 'like', '%' . $l_name . '%');
        }
        $query->orderBy($match_table. '.status', 'desc');
        $query->orderBy($match_table . '.time', 'asc');
        $query->orderBy($match_table . '.id', 'desc');

        $matches = $query->paginate(20);
        $matches->appends($request->all());
        $rest = ['matches'=>$matches, 'sport'=>MatchLive::kSportBasketball, 'types'=>MatchLiveChannel::kTypeArrayCn];
        $rest['private_arr'] = MatchLive::BasketballPrivateArray;
        return view('admin.match.live_matches', $rest);
    }

    /**
     * 今天的比赛，设置直播链接
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function todayMatch(Request $request) {
        $t_name = $request->input("t_name");//球队名称
        $l_name = $request->input("l_name");//赛事名称
        $has_live = $request->input('has_live');//是否有直播链接
        $status = $request->input('status');//比赛状态
        $type = $request->input('type');//比赛类型 1：竞彩、2：精简

        if (!empty($l_name)) {
            $lid_array = $this->getLeagueIdByName($l_name);
        }

        $withSelect = true;
        $isMain = false;

        $startDate = date("Y-m-d", strtotime('-1 days'));
        $endDate = date('Y-m-d H:i:s', strtotime('3 days'));

        if ($has_live == 1) {//有直播链接
            $query = MatchLive::query();
            $query->join('matches', function ($join) {
                $join->on('match_lives.match_id', '=', 'matches.id');
                $join->where('match_lives.sport', MatchLive::kSportFootball);
            });
        } else {
            $query = Match::query();
            $query->leftJoin('match_lives', function ($join) {
                $join->on('match_lives.match_id', '=', 'matches.id');
                $join->where('match_lives.sport', MatchLive::kSportFootball);
            });

            if ($has_live == 2) {
                //无直播链接
                $query->whereNull('match_lives.id');
            }
            $query->where("time", ">=", $startDate)->where("time", "<", $endDate);
        }

        $query->leftJoin("leagues", "matches.lid", "leagues.id");
        if ($withSelect) {
            $query->select("matches.*", "matches.id as mid", "leagues.name as league_name");
        }
        $query->addSelect(['match_lives.sport', 'match_lives.id as live_id']);

        if ($isMain) {
            $query->orderBy('leagues.main', 'desc');
        }
        if (!empty($t_name)) {
            $query->where(function ($orQuery) use ($t_name) {
                $orQuery->where('matches.hname', 'like', "%$t_name%");
                $orQuery->orWhere('matches.aname', 'like', "%$t_name%");
            });
        }
        if ($status == 1) {//未开始
            $query->where('matches.status', 0);
        } elseif ($status == 2) {//进行中
            $query->where(function ($orQuery) {
                $orQuery->where('status', 1);//上半场
                $orQuery->orWhere('status', 2);//中场
                $orQuery->orWhere('status', 3);//下半场
                $orQuery->orWhere('status', 4);//加时
            });
        } elseif ($status == 3) {
            $query->where('status', -1);
        }
        if ($type == 1) {
            $query->whereNotNull('matches.betting_num');
        } elseif ($type == 2) {
            $query->where('matches.genre', '&', Match::k_genre_yiji);
        }
        if (isset($lid_array) && count($lid_array) > 0) {
            $query->whereIn('lid', $lid_array);
        }
        $query->orderBy('status', 'desc');
        $query->orderBy('time', 'asc');
        $query->orderBy('id', 'desc');

        $matches = $query->paginate(20);
        $matches->appends($request->all());
        $rest = ['matches'=>$matches, 'sport'=>MatchLive::kSportFootball, 'types'=>MatchLiveChannel::kTypeArrayCn];
        $rest['private_arr'] = MatchLive::FootballPrivateArray;
        return view('admin.match.live_matches', $rest);
    }

    /**
     * 保存直播线路
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveChannel(Request $request) {
        $channel_id = $request->input('channel_id');//线路id
        $match_id = $request->input('match_id');//比赛id
        $sport = $request->input('sport');//竞技类型

        $type = $request->input('type');//线路类型
        $platform = $request->input('platform');//线路显示平台
        $isPrivate = $request->input('isPrivate',MatchLiveChannel::kIsNotPrivate);//是否有版权，2、有版权，2、无版权。
        $show = $request->input('show');//是否显示线路
        $od = $request->input('od');//线路排序
        $player = $request->input('player');//线路播放方式
        $content = $request->input('content');//线路内容
        $h_content = $request->input('h_content');//高清线路内容 当type = 9 时 即：高清验证类型，需要判断该链接是否为空
        $name = $request->input('name');//线路名称
        $use = $request->input('use',1);//使用这个线路的网站，1、通用，2、爱、3、黑土、4、310.
        $impt = $request->input('impt');//是否重点线路，1：普通，2：重点线路
        $ad = $request->input('ad', MatchLiveChannel::kNoAd);//

        //判断参数 开始
        if (!in_array($type, MatchLiveChannel::kTypeArray)) {
            return response()->json(['code'=>401, 'msg'=>'线路类型错误。']);
        }
        if (!in_array($platform, [MatchLiveChannel::kPlatformAll, MatchLiveChannel::kPlatformPC, MatchLiveChannel::kPlatformWAP])) {
            return response()->json(['code'=>401, 'msg'=>'平台参数错误。']);
        }
        if (!in_array($isPrivate, [MatchLiveChannel::kIsPrivate, MatchLiveChannel::kIsNotPrivate])) {
            return response()->json(['code'=>401, 'msg'=>'是否有版权参数错误。']);
        }
        if (!in_array($show, [MatchLiveChannel::kShow, MatchLiveChannel::kHide])) {
            return response()->json(['code'=>401, 'msg'=>'显示参数错误。']);
        }
        if (!empty($od) && !is_numeric($od)) {
            return response()->json(['code'=>401, 'msg'=>'排序必须为数字。']);
        }
        if (!in_array($player, MatchLiveChannel::kPlayerArray)) {
            return response()->json(['code'=>401, 'msg'=>'播放参数错误。']);
        }
        if (!in_array($ad, [MatchLiveChannel::kHasAd, MatchLiveChannel::kNoAd])) {
            return response()->json(['code'=>401, 'msg'=>'是否有广告参数错误。']);
        }
        if (empty($content)) {
            return response()->json(['code'=>401, 'msg'=>'必须填写线路内容。']);
        }
        if ($type == MatchLiveChannel::kTypeCode && empty($h_content)) {
            return response()->json(['code'=>401, 'msg'=>'必须填写高清线路内容。']);
        }
        if (!in_array($use, [1, 2, 3, 4])) {
            return response()->json(['code'=>401, 'msg'=>'观看网站参数错误。']);
        }
        if (!in_array($impt, [1, 2])) {
            return response()->json(['code'=>401, 'msg'=>'是否终端线路参数错误。']);
        }
        if (is_numeric($channel_id)) {
            $channel = MatchLiveChannel::query()->find($channel_id);
            if (!isset($channel)) {
                return response()->json(['code'=>403, 'msg'=>'线路不存在。']);
            }
        } else {//新建的线路
            if (!is_numeric($match_id)) {
                return response()->json(['code'=>401, 'msg'=>'比赛ID不能为空']);
            }
            if (!in_array($sport, [MatchLive::kSportFootball, MatchLive::kSportBasketball])) {
                return response()->json(['code'=>401, 'msg'=>'竞技类型错误']);
            }
            if ($sport == 1) {//足球
                $match = Match::query()->find($match_id);
            } else {
                $match = BasketMatch::query()->find($match_id);
            }
            if (!isset($match)) {
                return response()->json(['code'=>403, 'msg'=>'比赛不存在。']);
            }
        }
        //判断参数 结束

        if (!isset($channel)) {//新建线路
            $channel = new MatchLiveChannel();
        }
        if ($type != MatchLiveChannel::kTypeCode) {
            $h_content = '';
        }
        $channel->type = $type;
        $channel->platform = $platform;
        $channel->isPrivate = $isPrivate;
        $channel->show = $show;
        $channel->od = $od;
        $channel->name = $name;
        $channel->content = $content;
        $channel->h_content = $h_content;
        $channel->player = $player;
        $channel->auto = MatchLiveChannel::kAutoHand;//手动保存。
        $channel->use = $use;
        $channel->impt = $impt;
        $channel->ad = $ad;

        $exception = DB::transaction(function() use ($channel, $match_id, $sport) {
            if (!isset($channel->id)) {
                $live = MatchLive::query()->where('match_id', $match_id)->where('sport', $sport)->first();
                if (!isset($live)) {//查找是否有 直播
                    $live = new MatchLive();
                    $live->match_id = $match_id;
                    $live->sport = $sport;
                    $live->save();
                }
                $channel->live_id = $live->id;
            }
            $channel->save();
        });

        if (isset($exception)) {
            Log::error($exception);
            return response()->json(['code'=>500, 'msg'=>'保存线路失败']);
        }

        return response()->json(['code'=>200, 'msg'=>'保存线路成功']);
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

    protected function getLeagueIdByName($name) {
        $query = League::query();
        $query->where("name", 'like' , "%$name%");
        $leagues = $query->get();
        $l_array = [];
        foreach ($leagues as $league) {
            $l_array[] = $league->id;
        }
        return $l_array;
    }
}