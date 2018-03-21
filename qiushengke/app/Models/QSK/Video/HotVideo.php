<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/20
 * Time: 18:33
 */

namespace App\Models\QSK\Video;


use Illuminate\Database\Eloquent\Model;

class HotVideo extends Model
{
    const kStatusShow = 1, kStatusHide = 2;//1：显示，2：隐藏
    const kPlayerAuto = 1, kPlayerIFrame = 10, kPlayerM3U8 = 11, kPlayerFlv = 12, kPlayerRTMP = 13, kPlayerExLink = 14, kPlayerOther = 99;
    const kPlayerArray = [self::kPlayerAuto=>'自动选择', self::kPlayerIFrame=>'iframe', self::kPlayerM3U8=>'m3u8', self::kPlayerFlv=>'flv', self::kPlayerRTMP=>'rtmp',
        self::kPlayerExLink=>'外链', self::kPlayerOther=>'其他'];

    protected $connection = 'qsk';

    /**
     * 热门录像类型
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function type() {
        return $this->hasOne(HotVideoType::class, 'id', 'type_id');
    }

    public function statusCn() {
        $cn = '';
        $status = $this->status;
        switch ($status) {
            case 1:
                $cn = "显示";
                break;
            case 2:
                $cn = "隐藏";
                break;
        }
        return $cn;
    }



}