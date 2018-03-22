<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class BasketMatch extends Model
{
    protected $connection = 'liaogou_match';
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

    public function oddsAfters() {
        return $this->hasMany('App\Models\LiaoGouModels\BasketOddsAfter', 'mid', 'id');
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

    static public function saveWithWinData($m)
    {
        if ($m->stage > 3){
            $stage = BasketStage::where('win_id',$m->stage)->first();
            if (isset($stage)){
                $m->stage = $stage->id;
            }
        }

        $league = BasketLeague::getLeagueWithType($m->lid, 'win_id');
        $lid = $league ? $league->id : NULL;
        if (str_contains($m->hname, "(中)")) {
            $hname = str_replace('(中)', '', $m->hname);
        } else {
            $hname = $m['hname'];
        }
        $hname = str_replace('﻿', '', $hname);

        $idString = "win_id";
        $match = BasketMatch::where($idString, $m->id)
            ->first();
        if (isset($match)) {
            foreach ($m->getAttributes() as $key => $value) {
                if (BasketMatch::allowKey($key)) {
                    $match[$key] = $value;
                }
            }

            $match->win_lname = $m->lname;
            $match->win_id = $m->id;
            $match->lid = $lid;
            $match->hname = $match->hname ?: $hname;
            $match->aname = $match->aname ?: $m->aname;

            //填充球队
            if (is_null($match->hid)){
                $hteam = null;
                if ($m->hid > 0){
                    $hteam = LiaogouAlias::query()
                        ->where('liaogou_aliases.win_id','=',$m->hid)
                        ->where('liaogou_aliases.type',LiaogouAlias::kTypeTeam)
                        ->where('liaogou_aliases.from',LiaogouAlias::kFromQiuTan)
                        ->where('liaogou_aliases.sport',LiaogouAlias::kSportTypeBasket)
                        ->first();
                }
                if (is_null($hteam)){
                    $hteam = LiaogouAlias::query()
                        ->where('liaogou_aliases.target_name','=',$hname)
                        ->where('liaogou_aliases.type',LiaogouAlias::kTypeTeam)
                        ->where('liaogou_aliases.from',LiaogouAlias::kFromQiuTan)
                        ->where('liaogou_aliases.sport',LiaogouAlias::kSportTypeBasket)
                        ->first();
                }
                if (isset($hteam) && isset($hteam->lg_id)) {
                    $hid = $hteam->lg_id;
                    $match->hid = $hid;
                    $match->hname = $hteam->lg_name;
                } else {
                    //尝试看看有没有team对应球探的
                    $team = BasketTeam::where('name_china',$hname)->first();
                    if (isset($team)){
                        $alia = new LiaogouAlias();
                        $alia->type = LiaogouAlias::kTypeTeam;
                        $alia->from = LiaogouAlias::kFromQiuTan;
                        $alia->sport = LiaogouAlias::kSportTypeBasket;
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
                    $ateam = LiaogouAlias::query()
                        ->where('liaogou_aliases.win_id','=',$m->aid)
                        ->where('liaogou_aliases.type',LiaogouAlias::kTypeTeam)
                        ->where('liaogou_aliases.from',LiaogouAlias::kFromQiuTan)
                        ->where('liaogou_aliases.sport',LiaogouAlias::kSportTypeBasket)
                        ->first();
                }
                if (is_null($ateam)){
                    $ateam = LiaogouAlias::query()
                        ->where('liaogou_aliases.target_name','=',$m->aname)
                        ->where('liaogou_aliases.type',LiaogouAlias::kTypeTeam)
                        ->where('liaogou_aliases.from',LiaogouAlias::kFromQiuTan)
                        ->where('liaogou_aliases.sport',LiaogouAlias::kSportTypeBasket)
                        ->first();
                }
                if (isset($ateam) && isset($ateam->lg_id)) {
                    $aid = $ateam->lg_id;
                    $match->aid = $aid;
                    $match->aname = $ateam->lg_name;
                } else {
                    //尝试看看有没有team对应球探的
                    $team = BasketTeam::where('name_china',$m->aname)->first();
                    if (isset($team)){
                        $alia = new LiaogouAlias();
                        $alia->type = LiaogouAlias::kTypeTeam;
                        $alia->from = LiaogouAlias::kFromQiuTan;
                        $alia->sport = LiaogouAlias::kSportTypeBasket;
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
            //
            if (isset($stage)) {
                $match->stage = $stage->id;
            }

            $match->save();
            //同时保存到冗余表
            BasketMatchesAfter::saveWithWinData($match);
            return $match;
        }

        if (is_null($league)) {
            echo 'no liaogou league' . '</br>';
        }

        $match = new BasketMatch();
        foreach ($m->getAttributes() as $key => $value) {
            if (BasketMatch::allowKey($key))
                $match[$key] = $value;
        }

        if (isset($stage)) {
            $match->stage = $stage->id;
        }
        $match->win_lname = $m->lname;
        $match->win_id = $m->id;
        $match->lid = $lid;
        $match->hname = $match->hname ?: $hname;
        $match->aname = $match->aname ?: $m->aname;

        //填充球队
        if (is_null($match->hid)){
            $hteam = null;
            if ($m->hid > 0){
                $hteam = LiaogouAlias::query()
                    ->where('liaogou_aliases.win_id','=',$m->hid)
                    ->where('liaogou_aliases.type',LiaogouAlias::kTypeTeam)
                    ->where('liaogou_aliases.from',LiaogouAlias::kFromQiuTan)
                    ->where('liaogou_aliases.sport',LiaogouAlias::kSportTypeBasket)
                    ->first();
            }
            if (is_null($hteam)){
                $hteam = LiaogouAlias::query()
                    ->where('liaogou_aliases.target_name','=',$hname)
                    ->where('liaogou_aliases.type',LiaogouAlias::kTypeTeam)
                    ->where('liaogou_aliases.from',LiaogouAlias::kFromQiuTan)
                    ->where('liaogou_aliases.sport',LiaogouAlias::kSportTypeBasket)
                    ->first();
            }
            if (isset($hteam) && isset($hteam->lg_id)) {
                $hid = $hteam->lg_id;
                $match->hid = $hid;
                $match->hname = $hteam->lg_name;
            }
        }
        if (is_null($match->aid)){
            $ateam = null;
            if ($m->aid > 0){
                $ateam = LiaogouAlias::query()
                    ->where('liaogou_aliases.win_id','=',$m->aid)
                    ->where('liaogou_aliases.type',LiaogouAlias::kTypeTeam)
                    ->where('liaogou_aliases.from',LiaogouAlias::kFromQiuTan)
                    ->where('liaogou_aliases.sport',LiaogouAlias::kSportTypeBasket)
                    ->first();
            }
            if (is_null($ateam)){
                $ateam = LiaogouAlias::query()
                    ->where('liaogou_aliases.target_name','=',$m->aname)
                    ->where('liaogou_aliases.type',LiaogouAlias::kTypeTeam)
                    ->where('liaogou_aliases.from',LiaogouAlias::kFromQiuTan)
                    ->where('liaogou_aliases.sport',LiaogouAlias::kSportTypeBasket)
                    ->first();
            }
            if (isset($ateam) && isset($ateam->lg_id)) {
                $aid = $ateam->lg_id;
                $match->aid = $aid;
                $match->aname = $ateam->lg_name;
            }
        }
        if (isset($stage)) {
            $match->stage = $stage->id;
        }

        $match->save();
        //同时保存到冗余表
        BasketMatchesAfter::saveWithWinData($match);

        return $match;
    }

    static public function saveWithLotteryData($wbd) {
        $match = BasketMatch::getMatchWith($wbd->mid,'win_id');
        if (isset($match)) {
            $match->lname = $wbd['league'];

            if (isset($wbd['week']) && isset($wbd['num']) &&
                strlen($wbd['week']) > 0 && strlen($wbd['num']) > 0){
                $match->betting_num = $wbd['week'].$wbd['num'];
            }
            if (isset($stage)) {
                $match->stage = $stage->id;
            }
            $match->save();
            //同步到冗余表
            BasketMatchesAfter::saveWithWinData($match);
        }
        return $match;
    }

    static private function allowKey($key)
    {
        if ($key != 'id' && $key != 'win_id' && $key != 'lid' && $key != 'lname'
            && $key != 'created_at' && $key != 'updated_at' && $key != 'stage'
            && $key != 'hid' && $key != 'aid') {
            return true;
        }
        return false;
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
