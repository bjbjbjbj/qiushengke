<?php

namespace App\Models\AnalyseModels;

use App\Http\Controllers\Tool\MatchControllerTool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Match extends Model
{
    protected $connection = 'analyse_match';
    const k_genre_all = 1;//全部
    const k_genre_yiji = 2;//一级
    const k_genre_zucai = 4;//足彩
    const k_genre_jingcai = 8;//竞彩
    const k_genre_beijing = 16;//北京单场
    //
    public $timestamps = false;

    protected $hidden = ['id','win_id'];

    public function changeConnection($conn)
    {
        $this->connection = $conn;
    }

    public function getLeagueAttribute()
    {
        return $this->attributes['league'];
    }

    public function setLeagueAttribute($league)
    {
        return $this->attributes['league'] = $league;
    }

    public function matchData()
    {
        return $this->hasOne('App\Models\LiaoGouModels\MatchData', 'id', 'id');
    }

    public function oddsAfters() {
        return $this->hasMany('App\Models\LiaoGouModels\OddsAfter', 'mid', 'id');
    }

    public function leagueData()
    {
        return $this->belongsTo('App\Models\LiaoGouModels\League', 'lid', 'id');
    }

    public function forecast()
    {
        return $this->hasOne('App\Models\LiaoGouModels\MatchesForecast', 'id');
    }

    /**
     *默认让球盘口赔率
     */
    public function defaultAsianOdd()
    {
        $asian = Odd::query()
            ->where('mid', $this->id)
            ->where('cid', Odd::default_banker_id)
            ->where('type', Odd::k_odd_type_asian)
            ->first();
        return $asian;
    }

    /**
     *默认大小球盘口赔率
     */
    public function defaultOuOdd()
    {
        $ou = Odd::query()
            ->where('mid', $this->id)
            ->where('cid', Odd::default_banker_id)
            ->where('type', Odd::k_odd_type_ou)
            ->first();
        return $ou;
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
        return $this->status >= 0 && ($time > date_create("-2 hours")->getTimestamp() && $time < date_create("+2 days")->getTimestamp());
    }

    /**
     * 判断比赛是否已经结束
     */
    public function isMatchOver() {
        return $this->status == -1;
    }

    static public function getMatchWith($id,$from){
        $match = Match::where($from,$id)->first();
        if (isset($match)){
            return $match;
        }
        else{
            return null;
        }
    }

    static public function getMatchIdWith($id,$from){
        $match = Match::where($from,$id)->first();
        if (isset($match)){
            return $match->id;
        }
        else{
            return 0;
        }
    }

    //=================比赛接口相关===========================

    public function getStatusText()
    {
        //0未开始,1上半场,2中场休息,3下半场,-1已结束,-14推迟,-11待定,-10一支球队退赛
        $status = $this->status;
        return self::getStatusTextCn($status);
    }

    public static function getStatusTextCn($status) {
        switch ($status) {
            case 0:
                return "未开始";
            case 1:
                return "上半场";
            case 2:
                return "中场";
            case 3:
                return "下半场";
            case 4:
                return "加时";
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
        return '';
    }

    public function getCurMatchTime($isApp = false) {
        return self::getMatchCurrentTime($this->time, $this->timehalf, $this->status, $isApp);
    }

    //获取足球比赛的即时时间
    public static function getMatchCurrentTime($time, $timehalf, $status, $isApp = false)
    {
        $time = strtotime(isset($timehalf)? $timehalf : $time);
        $timehalf = strtotime($timehalf);
        $now = strtotime(date('Y-m-d H:i:s'));
        if ($status < 0 || $status == 2 || $status == 4) {
            $matchTime = self::getStatusTextCn($status);
        }elseif ($status == 1) {
            $diff = ($now - $time) > 0 ? ($now - $time) : 0;
            if ($isApp) {
                $matchTime = (floor(($diff) % 86400 / 60)) > 45 ? ('45\'+') : ((floor(($diff) % 86400 / 60)) . '\'');
            } else {
                $matchTime = (floor(($diff) % 86400 / 60)) > 45 ? ('45+' . '<span class="minute">' . '\'') : ((floor(($diff) % 86400 / 60)) . '<span class="minute">' . '\'');
            }
        } elseif ($status == 3) {
            $diff = ($now - $timehalf) > 0 ? ($now - $timehalf) : 0;
            if ($isApp) {
                $matchTime = (floor(($diff) % 86400 / 60)) > 45 ? ('90\'+') : ((floor(($diff) % 86400 / 60) + 45) . '\'');
            } else {
                $matchTime = (floor(($diff) % 86400 / 60)) > 45 ? ('90+' . '<span class="minute">' . '\'') : ((floor(($diff) % 86400 / 60) + 45) . '<span class="minute">' . '\'');
            }
        } else {
//            $matchTime = substr($match->time, 11, 5);
            $matchTime = '';
        }
        return $matchTime;
    }
}
