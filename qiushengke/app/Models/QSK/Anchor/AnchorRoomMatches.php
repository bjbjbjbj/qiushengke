<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/18
 * Time: 0:09
 */

namespace App\Models\QSK\Anchor;


use Illuminate\Database\Eloquent\Model;

class AnchorRoomMatches extends Model
{

    protected $connection = 'qsk';

    const kSportFootball = 1, kSportBasketball = 2;//1：足球，2：篮球

    public function room() {
        return $this->hasOne(AnchorRoom::class, 'id', 'room_id');
    }

    public static function getRooms($mid, $sport) {
        $query = self::query()->where('mid', $mid)->where('sport', $sport);
        $query->select('*')->selectRaw('ifNull(od, 999) as n_od');
        $query->orderBy('n_od');
        $arms = $query->get();
//        $array = [];
//        foreach ($arms as $arm) {
//            $obj = [];
//            $room = $arm->room;
//            $livePlatform = $room->livePlatform;
//            $anchor = $room->anchor;
//
//            $obj['anchorName'] = $anchor->name;//主播名称
//            $obj['type'] = $room->type;//直播间类型
//            $obj['typeCn'] = $livePlatform->name;//直播间类型中文
//            $obj['roomName'] = $room->name;
//
//            $array[$arm->id] = $obj;
//        }
        return $arms;
    }

}