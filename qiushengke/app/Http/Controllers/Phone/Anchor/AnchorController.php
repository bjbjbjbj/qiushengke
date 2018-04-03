<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/3/21
 * Time: 下午6:45
 */
namespace App\Http\Controllers\Phone\Anchor;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Controllers\PC\CommonTool;
use App\Http\Controllers\PC\FileTool;
use App\Models\QSK\Anchor\AnchorRoom;
use App\Models\QSK\Anchor\MatchLive;
use App\Models\QSK\Anchor\MatchLiveChannel;
use App\Models\QSK\Match\BasketMatch;
use App\Models\QSK\Match\Match;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AnchorController extends BaseController{

    public function staticIndex(Request $request){
        $html = $this->anchorIndex($request);
        if ($html && strlen($html) > 0){
            Storage::disk('public')->put('/wap/anchor/index.html',$html);
        }
    }

    private $liveMatchIds;
    private $liveFootMatches;
    private $liveBasketMatches;

    public function anchorIndex(Request $request){
        //赛事直播
        $this->liveMatchIds = array();
        $this->liveFootMatches = array();
        $this->liveBasketMatches = array();

        Match::query()->where('status', '>', 0)
            ->orderBy('time', 'asc')
            ->get()->each(function ($item) {
                $item->sport = 1;
                $this->liveMatchIds[] = $item->id;
                $this->liveFootMatches[$item->id] = $item;
            });
        BasketMatch::query()->where('status', '>', 0)
            ->orderBy('time', 'asc')
            ->get()->map(function ($item){
                $item->sport = 2;
                $this->liveMatchIds[] = $item->id;
                $this->liveBasketMatches[$item->id] = $item;
            });

        $lives = MatchLive::query()
            ->selectRaw('match_id as mid, sport, count(channel.id) as count')
            ->leftJoin('match_live_channels as channel', 'match_lives.id', 'channel.live_id')
            ->groupBy('match_id', 'sport')
            ->where('show', MatchLiveChannel::kShow)
            ->whereIn('match_id', $this->liveMatchIds)
            ->whereIn('channel.platform', [1, 3])
            ->get();

        $result['lives'] = array();

        foreach ($lives as $live) {
            $sport = $live->sport;
            $mid = $live->id;
            if ($sport == 2) {
                $live['match'] = $this->liveBasketMatches[$mid];
            } else {
                $live['match'] = $this->liveFootMatches[$mid];
            }
            $result['lives'][] = $live;
        }

        //主播直播
        $anchorRooms = AnchorRoom::query()
            ->whereIn('status', [2])->get();

        $result['anchorRooms'] = $anchorRooms;

        $result = array_merge($result,$this->html_var);
        return view('phone.anchor.index',$result);
    }
}