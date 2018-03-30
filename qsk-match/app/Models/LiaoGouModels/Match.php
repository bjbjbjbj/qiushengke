<?php

namespace App\Models\LiaoGouModels;

use App\Http\Controllers\Tool\MatchControllerTool;
use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
    protected $connection = 'liaogou_match';
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

    static public function saveWithWinData($m){
        $s = Stage::where('win_id',$m->stage)->first();
        if (isset($s))
            $stage = $s->id;
        else
            $stage = null;
        $league = League::getLeagueWithType($m->lid,'win_id');
        if (str_contains($m->hname, "(中)")){
            $hname = str_replace('(中)','',$m->hname);
        }
        else{
            $hname = $m['hname'];
        }
        $hname = str_replace('﻿','',$hname);

        $idString = "win_id";
        $match = Match::where($idString,$m->id)
            ->first();
        if (isset($match)) {
            foreach ($m->getAttributes() as $key => $value){
                if (Match::allowKey($key)) {
                    if ($key == 'genre'){
                        if (is_null($match['genre'])) {
                            $match[$key] = $value;
                        }
                    }
                    else{
                        $match[$key] = $value;
                    }
                }
            }

            if (isset($stage))
                $match->stage = $stage;
            else
                $match->stage = null;
            $match->win_id = $m->id;
            $match->lid = $league->id;
            $match->hname = $match->hname?:$hname;
            $match->aname = $match->aname?:$m->aname;

            if (is_null($match->hid)){
                $hteam = null;
                if ($m->hid > 0){
                    $hteam = LiaogouAlias::
                    join('teams',function ($q){
                        $q->on('teams.id','=','liaogou_aliases.lg_id');
                    })
                        ->where('liaogou_aliases.win_id','=',$m->hid)
                        ->where('liaogou_aliases.sport',1)
                        ->where('liaogou_aliases.type',1)
                        ->where('liaogou_aliases.from',1)
                        ->select('teams.id as tid','liaogou_aliases.*')
                        ->first();
                }
                if (is_null($hteam)){
                    $hteam = LiaogouAlias::
                    join('teams',function ($q){
                        $q->on('teams.id','=','liaogou_aliases.lg_id');
                    })
                        ->where('liaogou_aliases.target_name','=',$hname)
                        ->where('liaogou_aliases.sport',1)
                        ->where('liaogou_aliases.type',1)
                        ->where('liaogou_aliases.from',1)
                        ->select('teams.id as tid','liaogou_aliases.*')
                        ->first();
                }
                if (isset($hteam) && isset($hteam->tid)) {
                    $hid = $hteam->tid;
                    $match->hid = $hid;
                    $match->hname = $hteam->lg_name;
                }
                else{
                    //尝试看看有没有team对应球探的
                    if ($m->hid > 0) {
                        $team = Team::query()->where('win_id', $m->hid)->first();
                    } else {
                        $team = Team::where('name', $hname)->first();
                    }
                    if (isset($team)){
                        $alia = new LiaogouAlias();
                        $alia->type = 1;
                        $alia->from = 1;
                        $alia->sport = LiaogouAlias::kSportTypeFootball;
                        $alia->win_id = $team->win_id;
                        $alia->target_name = $hname;
                        $alia->lg_name = $hname;
                        $alia->lg_id = $team->id;
                        $alia->save();
                        $match->hid = $team->id;
                        $match->hname = $hname;
                    }
                }
            }
            if (is_null($match->aid)){
                $ateam = null;
                if ($m->aid > 0){
                    $ateam = LiaogouAlias::
                    join('teams',function ($q){
                        $q->on('teams.id','=','liaogou_aliases.lg_id');
                    })
                        ->where('liaogou_aliases.win_id','=',$m->aid)
                        ->where('liaogou_aliases.sport',1)
                        ->where('liaogou_aliases.type',1)
                        ->where('liaogou_aliases.from',1)
                        ->select('teams.id as tid','liaogou_aliases.*')
                        ->first();
                }
                if (is_null($ateam)){
                    $ateam = LiaogouAlias::
                    join('teams',function ($q){
                        $q->on('teams.id','=','liaogou_aliases.lg_id');
                    })
                        ->where('liaogou_aliases.lg_name','=',$m->aname)
                        ->where('liaogou_aliases.sport',1)
                        ->where('liaogou_aliases.type',1)
                        ->where('liaogou_aliases.from',1)
                        ->select('teams.id as tid','liaogou_aliases.*')
                        ->first();
                }
                if (isset($ateam) && isset($ateam->tid)) {
                    $aid = $ateam->tid;
                    $match->aid = $aid;
                    $match->aname = $ateam->lg_name;
                }
                else{
                    //尝试看看有没有team对应球探的
                    if ($m->aid > 0) {
                        $team = Team::query()->where('win_id', $m->aid)->first();
                    } else {
                        $team = Team::where('name', $m->aname)->first();
                    }
                    if (isset($team)){
                        $alia = new LiaogouAlias();
                        $alia->type = 1;
                        $alia->from = 1;
                        $alia->sport = LiaogouAlias::kSportTypeFootball;
                        $alia->win_id = $team->win_id;
                        $alia->target_name = $m->aname;
                        $alia->lg_name = $m->aname;
                        $alia->lg_id = $team->id;
                        $alia->save();
                        $match->aid = $team->id;
                        $match->aname = $m->aname;
                    }
                }
            }

            //球探名字还是要保存
            if (str_contains($m->hname, "(中)")){
                $hname = str_replace('(中)','',$m->hname);
            }
            else{
                $hname = $m['hname'];
            }
            $hname = str_replace('﻿','',$hname);
            $match->win_hname = $hname;
            $match->win_lname = $league->name;
            $match->win_aname = $m['aname'];
            $match->save();
            //同时保存到冗余表
            MatchesAfter::saveWithWinData($match);
            return $match;
        }

        //找不到比赛
        $hteam = null;
        if ($m->hid > 0){
            $hteam = LiaogouAlias::
            join('teams',function ($q){
                $q->on('teams.id','=','liaogou_aliases.lg_id');
            })
                ->where('liaogou_aliases.win_id','=',$m->hid)
                ->where('liaogou_aliases.sport',1)
                ->where('liaogou_aliases.type',1)
                ->where('liaogou_aliases.from',1)
                ->select('teams.id as tid','liaogou_aliases.*')
                ->first();
        }
        if (is_null($hteam)){
            $hteam = LiaogouAlias::
            join('teams',function ($q){
                $q->on('teams.id','=','liaogou_aliases.lg_id');
            })
                ->where('liaogou_aliases.target_name','=',$hname)
                ->where('liaogou_aliases.sport',1)
                ->where('liaogou_aliases.type',1)
                ->where('liaogou_aliases.from',1)
                ->select('teams.id as tid','liaogou_aliases.*')
                ->first();
        }
        $hid = 0;
        if (isset($hteam)&&isset($hteam->tid)) {
            $hid = $hteam->tid;
        }
        else{
            //尝试看看有没有team对应球探的
            if ($m->hid > 0) {
                $team = Team::query()->where('win_id', $m->hid)->first();
            } else {
                $team = Team::where('name',$hname)->first();
            }
            if (isset($team)){
                $alia = new LiaogouAlias();
                $alia->type = 1;
                $alia->from = 1;
                $alia->sport = LiaogouAlias::kSportTypeFootball;
                $alia->win_id = $team->win_id;
                $alia->target_name = $hname;
                $alia->lg_name = $hname;
                $alia->lg_id = $team->id;
                $alia->save();
                $hid = $team->id;
            }
        }

        $ateam = null;
        if ($m->aid > 0){
            $ateam = LiaogouAlias::
            join('teams',function ($q){
                $q->on('teams.id','=','liaogou_aliases.lg_id');
            })
                ->where('liaogou_aliases.win_id','=',$m->aid)
                ->where('liaogou_aliases.sport',1)
                ->where('liaogou_aliases.type',1)
                ->where('liaogou_aliases.from',1)
                ->select('teams.id as tid','liaogou_aliases.*')
                ->first();
        }
        if (is_null($ateam)){
            $ateam = LiaogouAlias::
            join('teams',function ($q){
                $q->on('teams.id','=','liaogou_aliases.lg_id');
            })
                ->where('liaogou_aliases.target_name','=',$m->aname)
                ->where('liaogou_aliases.sport',1)
                ->where('liaogou_aliases.type',1)
                ->where('liaogou_aliases.from',1)
                ->select('teams.id as tid','liaogou_aliases.*')
                ->first();
        }
        $aid = 0;
        if (isset($ateam) && isset($ateam->tid)) {
            $aid = $ateam->tid;
        }
        else{
            //尝试看看有没有team对应球探的
            if ($m->hid > 0) {
                $team = Team::query()->where('win_id', $m->aid)->first();
            } else {
                $team = Team::where('name',$m->aname)->first();
            }
            if (isset($team)){
                $alia = new LiaogouAlias();
                $alia->type = 1;
                $alia->from = 1;
                $alia->sport = LiaogouAlias::kSportTypeFootball;
                $alia->win_id = $team->win_id;
                $alia->target_name = $m->aname;
                $alia->lg_name = $m->aname;
                $alia->lg_id = $team->id;
                $alia->save();
                $aid = $team->id;
            }
        }

        if (is_null($league))
        {
            echo 'no liaogou league'.'</br>';
        }

        if ($hid > 0 && $aid > 0) {
            $match = Match::where('hid', $hid)
                ->where('aid', $aid)
                ->where('lid', $league->id)
                ->where('time', $m->time)
                ->first();
        }
        if (isset($match)) {
            foreach ($m->getAttributes() as $key => $value){
                if (Match::allowKey($key)) {
                    if ($key == 'genre'){
                        if (is_null($match['genre'])) {
                            $match[$key] = $value;
                        }
                    }
                    else{
                        $match[$key] = $value;
                    }
                }
            }

            if (isset($stage))
                $match->stage = $stage;
            else
                $match->stage = null;
            $match->lid = $league->id;
            $match->hname = $hteam->lg_name?:($match->hname?:$hname);
            $match->aname = $ateam->lg_name?:($match->aname?:$m->aname);

            if (str_contains($m->hname, "(中)")){
                $hname = str_replace('(中)','',$m->hname);
            }
            else{
                $hname = $m['hname'];
            }
            $hname = str_replace('﻿','',$hname);
            $match->win_hname = $hname;
            $match->win_lname = $league->name;
            $match->win_aname = $m['aname'];

            if ($match->win_id <= $m->id) {
                $match->win_id = $m->id;
                $match->save();
                //同时保存到冗余表
                MatchesAfter::saveWithWinData($match);
            }
            return $match;
        }

        $match = new Match();
        foreach ($m->getAttributes() as $key => $value){
            if (Match::allowKey($key))
                $match[$key] = $value;
        }

        if (isset($stage))
            $match->stage = $stage;
        else
            $match->stage = null;
        $match->win_id = $m->id;
        $match->lid = $league->id;
        $match->hname = isset($hteam->lg_name)?$hteam->lg_name:($match->hname?:$hname);
        $match->aname = isset($ateam->lg_name)?$ateam->lg_name:($match->aname?:$m->aname);

        if (str_contains($m->hname, "(中)")){
            $hname = str_replace('(中)','',$m->hname);
        }
        else{
            $hname = $m['hname'];
        }
        $hname = str_replace('﻿','',$hname);
        $match->win_hname = $hname;
        $match->win_lname = $league->name;
        $match->win_aname = $m['aname'];

        $match->save();
        //同时保存到冗余表
        MatchesAfter::saveWithWinData($match);

        return $match;
    }

    //专门用来填充竞彩比赛的 week num数据
    static public function saveWithWeekNum($win_id, $week, $num) {
        $match = Match::getMatchWith($win_id,'win_id');
        if (isset($match)) {
            $match->genre = $match->genre | 1 << 3;
            if (isset($week) && isset($num) &&
                strlen($week) > 0 && strlen($num) > 0){
                $match->betting_num = $week.$num;
            }
            $match->save();
            MatchesAfter::saveWithWinData($match);
        }
    }

    static public function saveWithLotteryData($wbd){
        $match = Match::getMatchWith($wbd->mid,'win_id');
        if (isset($match)) {
            $match->genre = $match->genre | 1 << 3;
            $match->lname = $wbd['league'];
            if (isset($wbd['week']) && isset($wbd['num']) &&
                strlen($wbd['week']) > 0 && strlen($wbd['num']) > 0){
                $match->betting_num = $wbd['week'].$wbd['num'];
            }
            $match->save();
            MatchesAfter::saveWithWinData($match);
        }
        return $match;
    }

    static private function allowKey($key){
        if ($key != 'id' && $key != 'win_id' &&
            $key != 'lid' && $key != 'hid' && $key != 'aid' &&
            $key != 'inflexion' && $key != 'stage' &&
            $key != 'has_sync' &&
            $key != 'hname' && $key != 'aname' && $key != 'is_odd')
        {
            return true;
        }
        return false;
    }

    private static function setStatus($match, $status)
    {
        if (!isset($match)) {
            return false;
        }
        //比赛状态是否转变成结束
        $isToOver = false;
        try {
            if ((!isset($match->status) || $match->status != -1) && $status == -1) {
                $isToOver = true;
                $matchTool = new MatchControllerTool();
                $match->status = $status;
                $matchTool->onStatusToOver($match);
            } else if ((!isset($match->status) || $match->status == -14) && $status == 0) {
                $match['has_lineup'] = NULL;
                $match['inflexion'] = NULL;
//                $this->same_odd = NULL;
                //同赔重置
                $match['same_odd'] = NULL;
                $match['status'] = $status;
            } else {
                $match['status'] = $status;
            }
        } catch (\Exception $e) {
            echo $e;
        } finally {
            $match['status'] = $status;
        }
        return $isToOver;
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
        return self::getMatchCurrentTimeByTimestamp($time, $timehalf, $status, $isApp);
    }

    public static function getMatchCurrentTimeByTimestamp($time, $timehalf, $status, $isApp = false) {
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
