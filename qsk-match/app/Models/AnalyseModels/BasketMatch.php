<?php

namespace App\Models\AnalyseModels;

use Illuminate\Database\Eloquent\Model;

class BasketMatch extends Model
{
    protected $connection = 'analyse_match';
    //
//    public $timestamps = false;

    protected $hidden = ['id', 'win_id'];

    public function changeConnection($conn)
    {
        $this->connection = $conn;
    }

    public function league() {
        return $this->hasOne(BasketLeague::class, 'id', 'lid');
    }

    /**
     * 判断比赛是否应该加入冗余表
     */
    public function isCanAddToAfter()
    {
        if (is_string($this->time)) {
            $time = strtotime($this->time);
        } else {
            $time = date_timestamp_get($this->time);
        }
        return $this->status >= 0 && ($time > date_create("-4 hours")->getTimestamp() && $time < date_create("+2 days")->getTimestamp());
    }

    static public function getMatchWith($id, $from)
    {
        $match = BasketMatch::where($from, $id)->first();
        if (isset($match)) {
            return $match;
        } else {
            return null;
        }
    }

    static public function getMatchIdWith($id, $from)
    {
        $match = BasketMatch::where($from, $id)->first();
        if (isset($match)) {
            return $match->id;
        } else {
            return 0;
        }
    }

    //===============比赛接口相关===============

    public function getStatusText()
    {
        //0未开始,1上半场,2中场休息,3下半场,-1已结束,-14推迟,-11待定,-10一支球队退赛
        $status = $this->status;
        $system = isset($this->league) ? $this->league->system : 0;

        return self::getStatusTextCn($status, $system == 1);
    }

    public static function getStatusTextCn($status, $isHalfFormat = false)
    {
        //0未开始,1上半场,2中场休息,3下半场,-1已结束,-14推迟,-11待定,-10一支球队退赛
        switch ($status) {
            case 0:
                return "未开始";
            case 1:
                return $isHalfFormat ? "上半场" : "第一节";
            case 2:
                return $isHalfFormat ? "" : "第二节";
            case 3:
                return $isHalfFormat ? "下半场" : "第三节";
            case 4:
                return $isHalfFormat ? "" : "第四节";
            case 5:
                return "加时1";
            case 6:
                return "加时2";
            case 7:
                return "加时3";
            case 8:
                return "加时4";
            case 50:
                return "中场";
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

    public function getMatchCurTime($isForApp = false, $isHalfFormat = false) {
        return self::getMatchCurrentTime($this->live_time_str, $this->status, $isHalfFormat, $isForApp);
    }

    /**
     * 获取比赛的状态或时间
     */
    public static function getMatchCurrentTime($live_time_str, $status, $isHalfFormat = false, $isForApp = false) {
        $str = '';
        if ($status > 0 && $status != 50) {
            if ($isForApp) {
                $str = $live_time_str;
            } else {
                if ($isHalfFormat) {
                    if ($status == 1) {
                        $str = '上半场 ' . $live_time_str;
                    } else if ($status == 3) {
                        $str = '下半场 ' . $live_time_str;
                    }
                } else {
                    $str = self::getStatusTextCn($status) . ' ' . $live_time_str;
                }
            }
        } else {
            $str = self::getStatusTextCn($status);
        }
        return $str;
    }
}
