<?php

namespace App\Models\LiveModels;

use Illuminate\Database\Eloquent\Model;

class MatchLive extends Model
{
    protected $connection = 'qsk_live';

    const kSportFootball = 1, kSportBasketball = 2;

    //==============接口相关====================
    public static function liveChannels($liveId) {
        return MatchLiveChannel::query()->where('live_id', $liveId)->orderBy('od')->get();
    }

    /**
     * 根据传入的mid 获取手机直播频道的数量
     * @param int $mid 比赛id
     * @param int $sport 比赛类型
     * @param int platform 平台 lg 黑土 爱看球
     * @return int|mixed 直播频道数量
     */
    public static function getAppMatchLiveCountById($mid, $sport = MatchLive::kSportFootball, $platform = MatchLiveChannel::kUse310) {
        //是否有直播
        $query = MatchLive::query()
            ->selectRaw('match_id as id, count(channel.id) as count')
            ->leftJoin('match_live_channels as channel', 'match_lives.id', 'channel.live_id')
            ->where('match_id', $mid)
            ->where('sport', $sport)
            ->where('channel.show', MatchLiveChannel::kShow)
            ->whereIn('channel.platform', [1, 3]);

        switch ($platform){
            case MatchLiveChannel::kUse310:
                $query->where(function ($orQuery) {
                    $orQuery->where('use',MatchLiveChannel::kUseAll);
                    $orQuery->orWhere('use',MatchLiveChannel::kUse310);
                });
                break;
            case MatchLiveChannel::kUseAikq:{
                $query->where(function ($orQuery) {
                    $orQuery->where('use',MatchLiveChannel::kUseAll);
                    $orQuery->orWhere('use',MatchLiveChannel::kUseAiKQ);
                });
            }
                break;
            case MatchLiveChannel::kUseHeitu:{
                $query->where('use',MatchLiveChannel::kUseAll);
            }
                break;
            case MatchLiveChannel::kUseAll:
                break;
        }

        $liveCount = $query->groupBy('match_id', 'sport')->first();

        return isset($liveCount) ? $liveCount->count : 0;
    }
}
