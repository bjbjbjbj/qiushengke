<?php
/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/3/1
 * Time: 15:33
 */

namespace App\Http\Controllers\Statistic;


use App\Models\LiaoGouModels\BasketMatch;
use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\MatchLive;
use App\Models\LiveModels\AnchorRoomMatch;

class MatchLiveTool
{

    //根据传入的比赛id, 获取比赛推荐的数量z
    public static function getMatchLiveCountByIds($mids,$isForPc = false, $sport = MatchLive::kSportFootball) {
        //是否有直播
        $query = AnchorRoomMatch::query()
            ->leftJoin('anchor_rooms as ar', 'anchor_room_matches.room_id', 'ar.id')
            ->whereIn('mid', $mids)
            ->where('sport', $sport)
            ->whereIn('ar.status', [1,2]);

        $liveCounts = $query
            ->selectRaw('mid as id, count(room_id) as count')
            ->groupBy('mid', 'sport')->get()
            ->mapWithKeys(function ($item){
                return [$item->id => $item->count];
            })->all();
        return $liveCounts;
    }

    //根据传入的时间范围，获取直播平台的数量
    public static function getMatchLiveCountByDate($dateArray,$isForPc = false,$sport = MatchLive::kSportFootball) {
        if ($sport == MatchLive::kSportBasketball) {
            $query = BasketMatch::query()->select('basket_matches.id')
                ->whereBetween('status', [-1, 100]);
        } else {
            $query = Match::query()->select('matches.id')
                ->whereBetween('status', [-1, 4]);
        }

        $startDate = isset($dateArray['startDate2']) ? $dateArray['startDate2'] : $dateArray['startDate'];

        $normalIds = $query->whereBetween('time', [$startDate, $dateArray['endDate']])
            ->orderBy('time', 'desc')->get()
            ->map(function ($item){
                return $item->id;
            })->all();
        return self::getMatchLiveCountByIds($normalIds,$isForPc, $sport);
    }


    //根据传入的时间范围，获取直播平台的数量
    public static function getMatchLiveCountById($mid,$sport = MatchLive::kSportFootball) {
        $liveCountArray = array();
        $pcLiveCount = 0;
        $wapLiveCount = 0;
        //是否有直播 开始
        $lives = AnchorRoomMatch::query()
            ->select('anchor_room_matches.*')
            ->leftJoin('anchor_rooms as ar', 'anchor_room_matches.room_id', 'ar.id')
            ->where('mid', $mid)
            ->where('sport', $sport)
            ->whereIn('ar.status', [1,2])->get();
        if (isset($lives) && count($lives) > 0) {
            foreach ($lives as $live) {
                $pcLiveCount++;
                $wapLiveCount++;
            }
        }
        $liveCountArray['pc_live'] = $pcLiveCount;
        $liveCountArray['live'] = $wapLiveCount;

        return $liveCountArray;
    }
}