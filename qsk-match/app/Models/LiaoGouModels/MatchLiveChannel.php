<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/26
 * Time: 18:00
 */

namespace App\Models\LiaoGouModels;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MatchLiveChannel extends Model
{
    protected $connection = 'liaogou_match';

    const kTypeSS365 = 1, kTypeTTZB = 2, kTypeBallBar = 3, kTypeWCJ = 4, kTypeDDK = 5, kTypeKBS = 6, kTypeCCTV = 7, kTypeLZ = 8,kTypeCode = 9, kTypeQQ = 10, kTypeOther = 99;//直播类型，1、ss365，2、天天直播，3、波吧，4、无插件, 5、低调看， 6、看比赛，7、CCTV，8、龙珠。
    const kPlayerAuto = 1, kPlayerIFrame = 11, kPlayerCk = 12, kPlayerM3u8 = 13, kPlayerFlv = 14, kPlayerRTMP = 15;//播放方式，1、iFrame嵌入。2、ck播放器播放
    const kAutoSpider = 1, kAutoHand = 2;//1、爬虫获取的，2、手动录入的。
    const kShow = 1, kHide = 2;//1、显示该直播链接，0、不显示直播链接
    const kPrivate = 2, kNotPrivate = 1;//2：有版权，1：无版权。
    const kUseAll = 1, kUseAikq = 2, kUseHeitu = 3, kUse310 = 4;//1：全部，2：爱看球，3：黑土，4：310.
    const kPlatformAll = 1, kPlatformPC = 2, kPlatformWAP = 3;//1、全部平台显示，2、在PC端显示，3、在WAP端显示。
    const kTypeArray = [self::kTypeSS365, self::kTypeTTZB, self::kTypeBallBar, self::kTypeWCJ, self::kTypeOther];

    public function matchLive() {
        return $this->hasOne(MatchLive::class, 'id', 'live_id');
    }

    /**
     * @param $matchId     比赛ID
     * @param $sport       竞技类型
     * @param $channelType 线路类型
     * @param $content     线路内容
     * @param $od          线路排序
     * @param $platform    线路平台
     * @param $player      线路播放
     * @param $name        线路名称
     * @param $show
     * @param $isPrivate   是否有版权，1：无版权，2：有版权。配合 $use 使用，一般有版权的 $use 用爱看球。
     * @param $use         网站专用，1：通用，2：爱看球，3：黑土，4：lg310。其他：待添加
     * @return mixed       返回保存是否成功，成功返回 null，失败返回 $exception
     */
    public static function saveSpiderChannel($matchId, $sport, $channelType, $content, $od, $platform, $player, $name, $show = self::kShow, $isPrivate = 1, $use = 1) {
        $exception = DB::transaction(function () use ($matchId, $sport, $channelType, $content, $od, $platform, $player, $name, $show, $isPrivate, $use) {
            $live = MatchLive::query()->where('match_id', $matchId)->where('sport', $sport)->first();
            if (isset($live)) {
                $live_id = $live->id;
                $channel = self::query()->where('live_id', $live_id)->where('type', $channelType)->where('content', $content)->first();
                if (!isset($channel)) {
                    $channel = new MatchLiveChannel();
                    $channel->live_id = $live_id;
                    $channel->type = $channelType;
                    $channel->name = $name;
                    $channel->content = $content;
                    $channel->platform = $platform;
                    $channel->player = $player;
                    $channel->od = $od;
                    $channel->auto = self::kAutoSpider;
                    $channel->show = $show;
                    $channel->isPrivate = $isPrivate;
                    $channel->use = $use;
//                    dump($channel);
                    $channel->save();
                }
            } else {
                $live = new MatchLive();
                $live->match_id = $matchId;
                $live->sport = $sport;
                $live->save();

                $channel = new MatchLiveChannel();
                $channel->live_id = $live->id;
                $channel->type = $channelType;
                $channel->name = $name;
                $channel->content = $content;
                $channel->platform = $platform;
                $channel->player = $player;
                $channel->od = $od;
                $channel->auto = self::kAutoSpider;
                $channel->show = $show;
                $channel->save();
            }
        });
        return $exception;
    }

}