<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/14
 * Time: 15:17
 */

namespace App\Models\QSK\Anchor;


use Illuminate\Database\Eloquent\Model;

class AnchorRoom extends Model
{
    const kStatusWait = 1;//未开播的直播间
    const kStatusPlay = 2;//正在直播的直播间
    const kStatusHide = -1;//不显示的直播间

    protected $connection = 'qsk';

    /**
     * 获取主播
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function anchor() {
        return $this->hasOne(Anchor::class, 'id', 'anchor_id');
    }

    /**
     * 获取直播平台
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function livePlatform() {
        return $this->hasOne(LivePlatform::class, 'id', 'type');
    }

    /**
     * 状态中文
     * @return string
     */
    public function statusCn() {
        $status = $this->status;
        $cn = "";
        switch ($status) {
            case self::kStatusWait:
                $cn = "未开播";
                break;
            case self::kStatusPlay:
                $cn = "播放中";
                break;
            case self::kStatusHide:
                $cn = "隐藏";
                break;
        }
        return $cn;
    }

    /**
     * 获取源链接
     * @param $isMobile
     * @return string
     */
    public function getResource($isMobile = 0) {
        $type = $this->type;//此直播间的平台ID 1：龙珠，2：章鱼，3：斗鱼，4：火猫，5:熊猫，6:iframe
        $content = $this->link;//房间ID/源链接等内容
        $url = "";
        switch ($type) {
            case 1:
                $url = $this->getLZResource($content, $isMobile);
                break;
            case 2:
                $url = $this->getZYResource($content);
                break;
            case 6:
                $url = $content;
                break;
        }
        return $url;
    }

    /**
     * 获取龙珠主播间的源
     * @param $roomId
     * @param $isMobile
     * @return string
     */
    protected function getLZResource($roomId, $isMobile = 0) {
        $api = "http://livestream.longzhu.com/live/getlivePlayurl?roomId=" . $roomId . "";
        $headers = [
//            "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8",
//            "Upgrade-Insecure-Requests:1",
//            "Accept-Language:zh-CN,zh;q=0.9",
//            "Cache-Control:max-age=0", "Connection:keep-alive",
//            "Host:livestream.longzhu.com",
//            "Cookie:UM_distinctid=161c7cd7096294-0a6516996dd4af-4353468-100200-161c7cd70988d6; _ma=OREN.2.225531597.1519475585; pluguest=43C3C924B726E3025AB077F4830FF746ECBB52408E67568815C9034945332456C17AE546EAFF01540D2C4AAAE4DE079AE269F42A59289342; __mtmc=2.654715993.1521100406; __mtmb=2.257901923.1522121377",
//            "If-Modified-Since:Tue, 27 Mar 2018 04:54:53 GMT",
//            "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8",
            "User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.186 Safari/537.36",
//            "Upgrade-Insecure-Requests:1",
            ];
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $api);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $out = curl_exec($ch);
        curl_close($ch);
        if (empty($out)) {
            return "";
        }
        $json = json_decode($out, true);
        if (is_null($json)) {
            return "";
        }
        if (!isset($json['playLines']) || !isset($json['playLines'][0]['urls'])) {
            return "";
        }
        $urls = $json['playLines'][0]['urls'];
        foreach ($urls as $url) {
            if (!isset($url['securityUrl']) || !isset($url['ext'])) {
                continue;
            }
            $ext = $url['ext'];
            if ($isMobile) {
                if ($ext == 'm3u8') {
                    return $url['securityUrl'];
                }
            } else {
                return $url['securityUrl'];
            }
        }
        return "";
    }

    /**
     * 获取章鱼的直播源，m3u8
     * @param $roomId
     * @return string
     */
    public function getZYResource($roomId) {
        $api = "http://m.zhangyu.tv/tv/" . $roomId;
        $headers = [
            "User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.186 Safari/537.36",
        ];
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $api);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $out = curl_exec($ch);
        curl_close($ch);

        $regex4 = "/<video(.*?)id=\"zyvideo\".*?>.*?<\/video>/ism";
        preg_match_all($regex4, $out, $matches);
        if(count($matches) < 2){
            return "";
        }
        $src = $matches[1][0];
        $src = trim($src);
        $src = str_replace('_src=', '', $src);
        $src = str_replace("'", '', $src);
        return $src;
    }
}