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

    public function getStatusText()
    {
        //0未开始,1上半场,2中场休息,3下半场,-1已结束,-14推迟,-11待定,-10一支球队退赛
        $status = $this->status;
        return self::getStatusTextCn($status);
    }

    public function league() {
        return $this->hasOne(BasketLeague::class, 'id', 'lid');
    }

    public function getLeagueName() {
        $league_name = $this->lname;
        if (empty($league_name)) {
            $league_name = $this->win_lname;
        }
        if (empty($league_name)) {
            $league = $this->league;
            $league_name = isset($league) ? $league->name : '';
        }
        return $league_name;
    }

    public static function getStatusTextCn($status, $system = 0) {
        switch ($status) {
            case 0:
                return "未开始";
            case 1:
                return $system == 1 ? "上半场" : "第一节";
            case 2:
                return "第二节";
            case 3:
                return $system == 1 ? "下半场" : "第三节";
            case 4:
                return "第四节";
            case 5:
                return "加时1";
            case 6:
                return "加时2";
            case 7:
                return "加时3";
            case 50:
                return "中场休息";
            case -1:
                return "已结束";
            case -5:
                return "推迟";
            case -2:
                return "待定";
            case -12:
                return "腰斩";
            case -10:
                return "退赛";
            case -99:
                return "异常";
        }
        return '';
    }
}