<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/25
 * Time: 17:23
 */

namespace App\Models\QSK\Match;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MatchLiveChannel extends Model
{
    protected $connection = 'qsk';

    const kTypeSS365 = 1, kTypeTTZB = 2, kTypeBallBar = 3, kTypeWCJ = 4, kTypeDDK = 5, kTypeKBS = 6,kTypeCCTVAPP = 7, kTypeLZ = 8, kTypeCode = 9, kTypeQQ = 10, kTypeOther = 99;//直播类型，1、ss365，2、天天直播，3、波吧，4、无插件，5、低调看，6、看比赛,7、cctv5app, 8、龙珠直播, 9、高清, 10、腾讯体育。
    const kPlayerAuto = 1, kPlayerIFrame = 11, kPlayerCk = 12, kPlayerM3u8 = 13, kPlayerFlv = 14, kPlayerRTMP = 15, kPlayerExLink = 16, kPlayerClappr = 17, kPlayerQQSport = 100;//播放方式，1、自动播放，11、iFrame嵌入。12、ck播放器播放，13、M3U8，14、FLV，15、RTMP，16、跳转外链,17、Clappr播放器,100腾讯体育用
    const kAutoSpider = 1, kAutoHand = 2;//1、爬虫获取的，2、手动录入的。
    const kShow = 1, kHide = 2;//1、显示该直播链接，0、不显示直播链接
    const kIsPrivate = 2, kIsNotPrivate = 1;//1、无版权，2、有版权
    const kPlatformAll = 1, kPlatformPC = 2, kPlatformWAP = 3;//1、全部平台显示，2、在PC端显示，3、在WAP端显示。
    const kUseAll = 1, kUseAiKQ = 2, kUseHeiTu = 3, kUseLg310 = 4;//使用的平台。
    const kHasAd = 1, kNoAd = 2;//1：有播放器广告，2：无播放器广告
    const kTypeArray = [self::kTypeSS365, self::kTypeTTZB, self::kTypeBallBar, self::kTypeWCJ, self::kTypeDDK, self::kTypeKBS,self::kTypeCCTVAPP, self::kTypeLZ, self::kTypeCode,self::kTypeQQ, self::kTypeOther];
//    const kTypeArrayCn = [self::kTypeSS365=>'ss365', self::kTypeTTZB=>'天天直播', self::kTypeBallBar=>'ballbar', self::kTypeCCTVAPP=>'CCTV', self::kTypeLZ=>'龙珠', self::kTypeCode=>'高清验证',self::kTypeQQ=>'腾讯体育', self::kTypeOther=>'其他'];//self::kTypeWCJ=>'无插件', self::kTypeDDK=>'低调看', self::kTypeKBS=>'看比赛',
    const kTypeArrayCn = [self::kTypeOther=>'其他'];//self::kTypeWCJ=>'无插件', self::kTypeDDK=>'低调看', self::kTypeKBS=>'看比赛',
    const kPlayerArray = [self::kPlayerAuto, self::kPlayerIFrame, self::kPlayerCk, self::kPlayerM3u8, self::kPlayerFlv, self::kPlayerRTMP, self::kPlayerExLink,self::kPlayerClappr];

    public function matchLive() {
        return $this->hasOne(MatchLive::class, 'id', 'live_id');
    }

    public function channelArray() {
        $type = $this->type;
        $array = ['name'=>$this->name, 'player'=>$this->player, 'channelId'=>$this->id, 'use'=>$this->use
            , 'pri'=>$this->isPrivate, 'platform'=>$this->platform, 'ad'=>$this->ad];
        switch ($type) {
            case self::kTypeSS365:
                $array['link'] = 'https://sportstream365.com';
                $array['type'] = $type;
                $array['id'] = $this->content;
                $array['name'] = $this->name == 'ss365' ? '' : $this->name;
                break;
            case self::kTypeWCJ:
                $array['link'] = 'wcj';
                $array['type'] = $type;
                $array['id'] = $this->id;
                $array['name'] = $this->name == 'wcj' ? '' : $this->name;
                break;
            case self::kTypeTTZB:
                $array['link'] = 'bztt';
                $array['type'] = $type;
                $array['id'] = $this->id;
                $array['name'] = $this->name == 'ttzb' ? '' : $this->name;
                break;
            case self::kTypeBallBar:
                $array['link'] = $this->content;
                $array['type'] = $type;
                $array['id'] = '';
                $array['name'] = $this->name == 'ballbar' ? '' : $this->name;
                break;
            case self::kTypeKBS://看比赛使用iFrame播放
                $array['link'] = $this->getKBSLink();
                $array['type'] = $type;
                $array['id'] = $this->id;
                $array['name'] = $this->name;
                break;
            case self::kTypeDDK://低调看
                $array['link'] = $this->content;
                $array['type'] = $type;
                $array['id'] = $this->id;
                $array['name'] = $this->name;
                break;
            case self::kTypeOther:
                $array['link'] = $this->content;
                $array['type'] = $type;
                $array['id'] = '';
                $array['name'] = $this->name;
                break;
        }
        return $array;
    }

    /**
     * 解释看比赛的url
     * @return mixed|string
     */
    function getKBSLink()
    {
        //$content = $this->content;
        $content = 'http://api.kanbisai.tv/zhibo/goodgame.php?id=138781';
        $link = '';
        if (empty($content)) {
            return $link;
        }
        $link = str_replace('，', ',', $content);
        $array = explode(',', $link);
        if (!is_array($array) || count($array) < 2) {
            return $link;
        }

        $player = $array[0];
        $vid = $array[1];

        switch ($player) {
            case 'qie':
                $link = 'http://api.kanbisai.tv/zhibo/qie.php?id=' . $vid;
                break;
            case 'zhangyu':
                $link = 'http://api.kanbisai.tv/zhibo/zhangyu.php?id=' . $vid;
                break;
            case 'tv':
                $link = 'http://api.kanbisai.tv/zhibo/tv.php?id=' . $vid;
                break;
            case 'pptv':
                $link = 'http://api.kanbisai.tv/zhibo/pptv.php?id=' . $vid;
                break;
            case 'ppvip':
                $link = 'http://api.kanbisai.tv/zhibo/ppvip.php?id=' . $vid;
                break;
            case 'levip':
                $link = 'http://api.kanbisai.tv/zhibo/levip.php?id=' . $vid;
                break;
            case 'letv':
                $link = 'http://api.kanbisai.tv/zhibo/letv.php?id=' . $vid;
                break;
            case 'zhibotv':
                $link = 'http://api.kanbisai.tv/zhibo/zhibotv.php?id=' . $vid;
                break;
            case 'longzhu':
                $link = 'http://api.kanbisai.tv/zhibo/longzhu.php?id=' . $vid;
                break;
            case 'longzhugf':
                $link = 'http://api.kanbisai.tv/zhibo/longzhugf.php?id=' . $vid;
                break;
            case 'iframe':
                $link = 'http://api.kanbisai.tv/zhibo/iframe.php?id=' . $vid;
                break;
            case 'qq':
                $link = 'http://api.kanbisai.tv/zhibo/qq.php?id=' . $vid;
                break;
            case 'bestv':
                $link = 'http://api.kanbisai.tv/zhibo/bestv.php?id=' . $vid;
                break;
            case 'pc':
                $link = 'http://api.kanbisai.tv/zhibo/pc.php?id=' . $vid;
                break;
            case 'els':
                $link = 'http://api.kanbisai.tv/zhibo/els.php?id=' . $vid;
                break;
            case 'sina':
                $link = 'http://api.kanbisai.tv/zhibo/sina.php?id=' . $vid;
                break;
            case 'm3u8':
                $link = 'http://api.kanbisai.tv/zhibo/m3u8.php?id=' . $vid;
                break;
            case 'cntv':
                $link = 'http://api.kanbisai.tv/zhibo/cntv.php?id=' . $vid;
                break;
            case 'url':
                $link = 'http://api.kanbisai.tv/zhibo/url.php?id=' . $vid;
                break;
            case 'room':
                $link = 'http://api.kanbisai.tv/zhibo/room.php?id=' . $vid;
                break;
            case 'jrs':
                $link = 'http://api.kanbisai.tv/zhibo/jrs.php?id=' . $vid;
                break;
            case 'ballbar':
                $link = 'https://www.ballbar.cc/live/' . $vid;
                break;
            case 'player':
                $link = 'http://api.kanbisai.tv/zhibo/player.html?str=' . $vid;
                break;
            case 'huajiao':
                $link = 'http://api.kanbisai.tv/zhibo/huajiao.php?id=' . $vid;
                break;
            case 'line':
                $link = 'http://api.kanbisai.tv/live/line' . $vid;
                break;
            case 'ss':
                $link = 'http://api.kanbisai.tv/zhibo/ss.php?id=' . $vid;
                break;
            case 'no':
                $link = 'http://api.kanbisai.tv/zhibo/no.php';
                break;
            case 'goodgame':
                $link = 'http://api.kanbisai.tv/zhibo/goodgame.php?id=' . $vid;
                break;
            case 'rus':
                $link = 'http://russian15487545158795348735674799536595131454464587523554.com/live/' . $vid;
                break;
            case 'dog':
                $link = 'http://api.kanbisai.tv/zhibo/dog.php?id=' . $vid;
                break;
        }
        return $link;
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
     * @return mixed       返回保存是否成功，成功返回 null，失败返回 $exception
     */
    public static function saveSpiderChannel($matchId, $sport, $channelType, $content, $od, $platform, $player, $name, $show = self::kShow) {
        $exception = DB::transaction(function () use ($matchId, $sport, $channelType, $content, $od, $platform, $player, $name, $show) {
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
                    $channel->isPrivate = self::kIsNotPrivate;
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