<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/14
 * Time: 12:50
 */

namespace App\Models\QSK\Anchor;


use Illuminate\Database\Eloquent\Model;

/**
 * 主播实体类
 * Class Anchor
 * @package App\Models\QSK\Anchor
 */
class Anchor extends Model
{
    const kStatusValid = 1;//有效主播
    const kStatusUnValid = -1;//无效主播
    const icon_disk = 'icon';

    protected $connection = 'qsk';

    /**
     * 获取主播的直播间
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rooms() {
        return $this->hasMany(AnchorRoom::class, 'anchor_id', 'id')->where('status','<>', AnchorRoom::kStatusHide);
    }


    public static function bookRooms() {
        $array = [];
        $anchors = self::query()->where('status', self::kStatusValid)->get();
        foreach ($anchors as $anchor) {
            $rooms = $anchor->rooms;
            $anchorName = $anchor->name;
            foreach ($rooms as $room) {
                $livePlatform = $room->livePlatform;

                $obj['anchorName'] = $anchorName;//主播名称
                $obj['type'] = $room->type;//直播间类型
                $obj['typeCn'] = $livePlatform->name;//直播间类型中文
                $obj['roomName'] = $room->name;

                $array[$room->id] = $obj;//房间类型 - 房间名称 - 主播名称
            }
        }
        return $array;
    }

}

