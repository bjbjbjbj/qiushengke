<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/17
 * Time: 21:03
 */

namespace App\Models\QSK\Match;


use App\Models\QSK\Anchor\AnchorRoomMatches;
use Illuminate\Database\Eloquent\Model;

/**
 * 只保留 1天前 - 3天内的比赛
 * Class Match
 * @package App\Models\QSK\Match
 */
class Match extends Model
{
    protected $connection = 'qsk_match';

    const k_genre_all = 1;//全部
    const k_genre_yiji = 2;//一级
    const k_genre_zucai = 4;//足彩
    const k_genre_jingcai = 8;//竞彩
    const k_genre_beijing = 16;//北京单场
    //
    public $timestamps = false;

    protected $hidden = ['win_id'];

    /**
     * 获取预约了该比赛的主播直播间
     * @return array
     */
    public function liveRooms() {
        $mid = $this->id;
        $rooms = AnchorRoomMatches::getRooms($mid, AnchorRoomMatches::kSportFootball);
        return $rooms;
    }

}