<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/18
 * Time: 16:21
 */

namespace App\Models\QSK\Match;


use App\Models\QSK\Anchor\AnchorRoomMatches;
use Illuminate\Database\Eloquent\Model;

class BasketMatch extends Model
{
    protected $connection = 'qsk_match';

    /**
     * 获取预约了该比赛的主播直播间
     * @return array
     */
    public function liveRooms() {
        $mid = $this->id;
        $rooms = AnchorRoomMatches::getRooms($mid, AnchorRoomMatches::kSportBasketball);
        return $rooms;
    }

}