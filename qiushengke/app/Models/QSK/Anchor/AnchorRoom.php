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

}