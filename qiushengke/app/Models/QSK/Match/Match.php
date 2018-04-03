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

    public function getLeagueName() {
        $league_name = $this->lname;
        if (empty($league_name)) {
            $league = $this->league;
            $league_name = isset($league) ? $league->name : '';
        }
        return $league_name;
    }

    public function league()
    {
        return $this->hasOne('App\Models\QSK\Match\League', 'id', 'lid');
    }

    public function getStatusText()
    {
        //0未开始,1上半场,2中场休息,3下半场,-1已结束,-14推迟,-11待定,-10一支球队退赛
        $status = $this->status;
        switch ($status) {
            case 0:
                return "未开始";
            case 1:
                return "上半场";
            case 2:
                return "中场休息";
            case 3:
                return "下半场";
            case -1:
                return "已结束";
            case -14:
                return "推迟";
            case -11:
                return "待定";
            case -12:
                return "腰斩";
            case -10:
                return "退赛";
            case -99:
                return "异常";
        }
    }
}