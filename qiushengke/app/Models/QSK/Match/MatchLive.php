<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/20
 * Time: 12:52
 */

namespace App\Models\QSK\Match;

use Illuminate\Database\Eloquent\Model;

class MatchLive extends Model
{
    protected $connection = 'qsk';
    const kSportFootball = 1, kSportBasketball = 2;//1：足球，2：篮球

    const kShow = 1, kHide = 2;
    const kPlatformAll = 1, kPlatformPc = 2, kPlatformPhone = 3;
    const FootballPrivateArray = [8, 11, 26, 29, 31, 46, 73, 77, 139];//德甲，法甲，西甲，意甲，英超，中超，欧冠，欧罗巴，亚冠。
    const BasketballPrivateArray = [1, 2, 3, 4, 5];//NBA, WNBA, NBA明星赛, CBA, CBA明星赛

    public function football() {
        return $this->hasOne(Match::class, 'id', 'match_id');
    }

    public function basketball() {
        return $this->hasOne(BasketMatch::class, 'id', 'match_id');
    }

    public static function liveChannels($liveId) {
        return MatchLiveChannel::query()->where('live_id', $liveId)->orderBy('od')->get();
    }

    public function hasWapChannel() {
        $query = MatchLiveChannel::query()->where('live_id', $this->id)->where('show', MatchLiveChannel::kShow);
        $query->where(function ($orQ) {
            $orQ->where('platform', MatchLiveChannel::kPlatformAll);
            $orQ->orWhere('platform', MatchLiveChannel::kPlatformWAP);
        });
        return $query->count() > 0;
    }

    /**
     * 是否有直播线路
     * @param int $use      使用的网站
     * @param int $platform 平台（PC、WAP）默认获取PC的线路
     * @return bool
     */
    public function hasChannel($use = 0, $platform = MatchLiveChannel::kPlatformPC) {
        $query = MatchLiveChannel::query()->where('live_id', $this->id)->where('show', MatchLiveChannel::kShow);
        $query->where(function ($orQ) use ($platform) {
            $orQ->where('platform', MatchLiveChannel::kPlatformAll);
            $orQ->orWhere('platform', $platform);
        });
        if ($use != 0) {
            $query->where(function ($orQ) use ($use) {
                $orQ->where('use', MatchLiveChannel::kUseAll);
                $orQ->orWhere('use', $use);
            });
        }
        return $query->count() > 0;
    }

    public function getMatch($id = null) {
        if (!isset($id)) $id = $this->match_id;
        if ($this->sport == self::kSportFootball) {
            return Match::query()->find($id);
        } else if ($this->sport == self::kSportBasketball) {
            return BasketMatch::query()->find($id);
        }
    }

    public function getLeagueName($id) {
        $match = $this->getMatch($id);
        if (isset($match)) {
            return $match->getLeagueName();
        }
        return "";
    }

    public function getStatusText($id) {
        $match = $this->getMatch($id);
        if (isset($match)) {
            return $match->getStatusText();
        }
        return "";
    }

    /**
     * 视频线路总数量
     */
    public function channelCount() {
        return MatchLiveChannel::query()->where('live_id', $this->id)->count();
    }

    public function channels() {
        $array = [];
        $query = MatchLiveChannel::query()->where(function ($orQuery) {
            $orQuery->where('platform', MatchLiveChannel::kPlatformAll);
            $orQuery->orWhere('platform', MatchLiveChannel::kPlatformPC);
        });
        $query->where('live_id', $this->id);
        $query->where('show', MatchLiveChannel::kShow);
        $query->selectRaw('*, ifnull(od, 99)');
        $query->orderBy('od');
        $channels = $query->limit(10)->get();
        $channelsName = ['线路一','线路二','线路三','线路四','线路五','线路六','线路七','线路八','线路九','线路十'];
        for ($index = 0 ; $index < count($channels) ; $index++) {
            $channel = $channels[$index];
            $tmp = $channel->channelArray();
            $tmp['id'] = $channel->id;
            $tmp['type'] = $channel->type;
            $tmp['name'] = strlen($tmp['name']) > 0 ? $tmp['name'] : $channelsName[$index];
            $array[] = $tmp;
        }
        return $array;
    }

    /**
     * 看球吗用channel,因为liaogou现在不支持tt直播,这个临时
     * @return array
     */
    public function kChannels() {
        $array = [];
        $query = MatchLiveChannel::query()->where(function ($orQuery) {
            $orQuery->where('platform', MatchLiveChannel::kPlatformAll);
            $orQuery->orWhere('platform', MatchLiveChannel::kPlatformPC);
        });
        $query->where(function ($orQuery) {
            $orQuery->where('use', MatchLiveChannel::kUseAll);
            $orQuery->orWhere('use', MatchLiveChannel::kUseAiKQ);
            $orQuery->orWhere('use', MatchLiveChannel::kUseLg310);
        });
        $query->where('live_id', $this->id);
        $query->where('show', MatchLiveChannel::kShow);
        $query->selectRaw('*, ifnull(od, 99)');
        $query->orderBy('od');
        $channels = $query->limit(10)->get();
        $channelsName = ['线路一','线路二','线路三','线路四','线路五','线路六','线路七','线路八','线路九','线路十'];
        for ($index = 0 ; $index < count($channels) ; $index++) {
            $channel = $channels[$index];
            $tmp = $channel->channelArray();
            $tmp['id'] = $channel->id;
            $tmp['type'] = $channel->type;
            $tmp['name'] = strlen($tmp['name']) > 0 ? $tmp['name'] : $channelsName[$index];
            $tmp['mid'] = $this->match_id;
            $tmp['sport'] = $this->sport;
            $tmp['impt'] = $this->impt;
            $array[] = $tmp;
        }
        return $array;
    }

    /**
     * 手机端的直播线路
     * @return array
     */
    public function mChannels() {
        $array = [];
        $query = MatchLiveChannel::query()->where(function ($orQuery) {
            $orQuery->where('platform', MatchLiveChannel::kPlatformAll);
            $orQuery->orWhere('platform', MatchLiveChannel::kPlatformWAP);
        });
        $query->where(function ($orQuery) {
            $orQuery->where('use',MatchLiveChannel::kUseAll);
            $orQuery->orWhere('use', MatchLiveChannel::kUseAiKQ);
            $orQuery->orWhere('use',MatchLiveChannel::kUseLg310);
        });
        $query->where('live_id', $this->id);
        $query->where('show', MatchLiveChannel::kShow);
        $query->selectRaw('*, ifnull(od, 99)');
        $query->orderBy('od');
        $channels = $query->limit(6)->get();
        $channelsName = ['线路一','线路二','线路三','线路四','线路五','线路六','线路七','线路八','线路九','线路十'];
        for ($index = 0 ; $index < count($channels) ; $index++) {
            $channel = $channels[$index];
            $tmp = $channel->channelArray();
            $tmp['name'] = strlen($tmp['name']) > 0 ? $tmp['name'] : $channelsName[$index];
            $tmp['id'] = $channel->id;
            $tmp['type'] = $channel->type;
            $tmp['mid'] = $this->match_id;
            $tmp['sport'] = $this->sport;
            $array[] = $tmp;
        }
        return $array;
    }


    /**
     * 看球吗用channel,因为liaogou现在不支持tt直播,这个临时
     * @return array
     */
    public function kAiKqChannels() {
        $array = [];
        $query = MatchLiveChannel::query()->where(function ($orQuery) {
            $orQuery->where('platform', MatchLiveChannel::kPlatformAll);
            $orQuery->orWhere('platform', MatchLiveChannel::kPlatformPC);
        });
        $query->where(function ($orQuery) {
            $orQuery->where('use',MatchLiveChannel::kUseAll);
            $orQuery->orWhere('use',MatchLiveChannel::kUseAiKQ);
        });
        $query->where('live_id', $this->id);
        $query->where('show', MatchLiveChannel::kShow);
        $query->where('isPrivate', MatchLiveChannel::kIsPrivate);
        $query->selectRaw('*, ifnull(od, 99)');
        $query->orderBy('od');
        $channels = $query->limit(10)->get();
        $channelsName = ['线路一','线路二','线路三','线路四','线路五','线路六','线路七','线路八','线路九','线路十'];
        for ($index = 0 ; $index < count($channels) ; $index++) {
            $channel = $channels[$index];
            $tmp = $channel->channelArray();
            $tmp['id'] = $channel->id;
            $tmp['type'] = $channel->type;
            $tmp['name'] = strlen($tmp['name']) > 0 ? $tmp['name'] : $channelsName[$index];
            $tmp['mid'] = $this->match_id;
            $tmp['sport'] = $this->sport;
            $tmp['impt'] = $this->impt;
            $array[] = $tmp;
        }
        return $array;
    }

    /**
     * 手机端的直播线路
     * @return array
     */
    public function mAiKqChannels() {
        $array = [];
        $query = MatchLiveChannel::query()->where(function ($orQuery) {
            $orQuery->where('platform', MatchLiveChannel::kPlatformAll);
            $orQuery->orWhere('platform', MatchLiveChannel::kPlatformWAP);
        });
        $query->where(function ($orQuery) {
            $orQuery->where('use',MatchLiveChannel::kUseAll);
            $orQuery->orWhere('use',MatchLiveChannel::kUseAiKQ);
        });
        $query->where('live_id', $this->id);
        $query->where('show', MatchLiveChannel::kShow);
        $query->where('isPrivate', MatchLiveChannel::kIsPrivate);
        $query->selectRaw('*, ifnull(od, 99)');
        $query->orderBy('od');
        $channels = $query->limit(6)->get();
        $channelsName = ['线路一','线路二','线路三','线路四','线路五','线路六','线路七','线路八','线路九','线路十'];
        for ($index = 0 ; $index < count($channels) ; $index++) {
            $channel = $channels[$index];
            $tmp = $channel->channelArray();
            $tmp['name'] = strlen($tmp['name']) > 0 ? $tmp['name'] : $channelsName[$index];
            $tmp['id'] = $channel->id;
            $tmp['type'] = $channel->type;
            $tmp['mid'] = $this->match_id;
            $tmp['sport'] = $this->sport;
            $array[] = $tmp;
        }
        return $array;
    }

    /**
     * 看球吗用channel,因为liaogou现在不支持tt直播,这个临时
     * @return array
     */
    public function kHeiTuChannels() {
        $array = [];
        $query = MatchLiveChannel::query()->where(function ($orQuery) {
            $orQuery->where('platform', MatchLiveChannel::kPlatformAll);
            $orQuery->orWhere('platform', MatchLiveChannel::kPlatformPC);
        });
        $query->where(function ($orQuery) {
            $orQuery->where('use',MatchLiveChannel::kUseAll);
            $orQuery->orWhere('use',MatchLiveChannel::kUseHeiTu);
        });
        $query->where('live_id', $this->id);
        $query->where('show', MatchLiveChannel::kShow);
        $query->selectRaw('*, ifnull(od, 99)');
        $query->orderBy('od');
        $channels = $query->limit(10)->get();
        $channelsName = ['线路一','线路二','线路三','线路四','线路五','线路六','线路七','线路八','线路九','线路十'];
        for ($index = 0 ; $index < count($channels) ; $index++) {
            $channel = $channels[$index];
            $tmp = $channel->channelArray();
            $tmp['id'] = $channel->id;
            $tmp['type'] = $channel->type;
            $tmp['name'] = strlen($tmp['name']) > 0 ? $tmp['name'] : $channelsName[$index];
            $tmp['mid'] = $this->match_id;
            $tmp['sport'] = $this->sport;
            $array[] = $tmp;
        }
        return $array;
    }

    /**
     * 手机端的直播线路
     * @return array
     */
    public function mHeiTuChannels() {
        $array = [];
        $query = MatchLiveChannel::query()->where(function ($orQuery) {
            $orQuery->where('platform', MatchLiveChannel::kPlatformAll);
            $orQuery->orWhere('platform', MatchLiveChannel::kPlatformWAP);
        });
        $query->where(function ($orQuery) {
            $orQuery->where('use',MatchLiveChannel::kUseAll);
            $orQuery->orWhere('use',MatchLiveChannel::kUseHeiTu);
        });
        $query->where('live_id', $this->id);
        $query->where('show', MatchLiveChannel::kShow);
        $query->selectRaw('*, ifnull(od, 99)');
        $query->orderBy('od');
        $channels = $query->limit(6)->get();
        $channelsName = ['线路一','线路二','线路三','线路四','线路五','线路六','线路七','线路八','线路九','线路十'];
        for ($index = 0 ; $index < count($channels) ; $index++) {
            $channel = $channels[$index];
            $tmp = $channel->channelArray();
            $tmp['name'] = strlen($tmp['name']) > 0 ? $tmp['name'] : $channelsName[$index];
            $tmp['id'] = $channel->id;
            $tmp['type'] = $channel->type;
            $tmp['mid'] = $this->match_id;
            $tmp['sport'] = $this->sport;
            $array[] = $tmp;
        }
        return $array;
    }

    public static function isLive($mid, $sport = MatchLive::kSportFootball) {
        $count = MatchLive::query()->where('sport', $sport)
            ->where('match_id', $mid)->get()->count();
        return $count > 0;
    }

    public static function getMatchLives($mids, $sport = self::kSportFootball) {
        $lives = MatchLive::query()->where('sport', $sport)
            ->whereIn('match_id', $mids)
            ->select('match_id')
            ->get();
        return $lives;
    }
}