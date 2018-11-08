<?php

namespace App\Models\LiaoGouModels;

use App\Models\LiaoGouModels\BasketMatch;
use Illuminate\Database\Eloquent\Model;

class BasketTeam extends Model
{
    protected $connection = 'liaogou_match';

    static public function saveWithWinData($wt,$wtid,$wname,$saveAlisa = true){
        if ($wtid <= 0)
            return;
        //先看球探对应的表有没有这个信息
        $t = BasketTeam::where('win_id',$wtid)->first();
        //已经保存过的不需要再保存
        if (isset($t) && isset($t->name_china_short)){
            return;
        }
        if (!isset($t)) {
            $t = new BasketTeam();
        }
        $tmp = null;
        //如果没有
        if (is_null($wt)){
            $wt = new  \App\Models\WinModels\BasketTeam();
            $wt->name_china = $wname;
            if ($saveAlisa){
                //下面保存
                $tmp = new LiaogouAlias();
                $tmp->type = 1;
                $tmp->from = 1;
                $tmp->sport = LiaogouAlias::kSportTypeBasket;
                $tmp->target_name = $wt->name;
                $tmp->lg_name = $wt->name;
                $tmp->win_id = $wtid;
            }
        }
        foreach ($wt->getAttributes() as $key => $value){
            if ($key != 'id' && $key != 'created_at' && $key != 'updated_at') {
                //icon不覆盖
                if ($key == 'icon' && isset($t['icon'])){

                }
                else {
                    $t[$key] = $value;
                }
            }
        }
        $t->win_id = $wtid;
        if (!$t->save()){
            echo 'team save error';
        }
        else{
            if ($saveAlisa) {
                if (is_null($tmp)) {
                    //6个名都保存
                    if (isset($t->name_china)){
                        self::saveAlias($wtid,$t->name_china,$t->name_china,$t->id);
                        if ($t->name_china != $wname){
                            self::saveAlias($wtid,$wname,$t->name_china,$t->id);
                        }
                        if (isset($t->name_china_short)){
                            self::saveAlias($wtid,$t->name_china_short,$t->name_china,$t->id);
                        }
                        if (isset($t->name_hk)){
                            self::saveAlias($wtid,$t->name_hk,$t->name_china,$t->id);
                        }
                        if (isset($t->name_hk_short)){
                            self::saveAlias($wtid,$t->name_hk_short,$t->name_china,$t->id);
                        }
                        if (isset($t->name_en)){
                            self::saveAlias($wtid,$t->name_en,$t->name_china,$t->id);
                        }
                        if (isset($t->name_en_short)){
                            self::saveAlias($wtid,$t->name_en_short,$t->name_china,$t->id);
                        }
                    }
                }
            }
            else{
                if (isset($tmp)) {
                    $tmp->lg_id = $t->id;
                    $tmp->save();
                }
            }
        }
    }

    static private function saveAlias($wtid,$targetName,$lgName,$tid){
        $tmp = LiaogouAlias::where('win_id', $wtid)
            ->where('target_name',$targetName)
            ->where('from', 1)
            ->where('type', 1)
            ->where('sport', LiaogouAlias::kSportTypeBasket)
            ->first();
        if (is_null($tmp)) {
            $tmp = new LiaogouAlias();
            $tmp->type = 1;
            $tmp->from = 1;
            $tmp->sport = LiaogouAlias::kSportTypeBasket;
            $tmp->target_name = $targetName;
            $tmp->lg_name = $lgName;
            $tmp->win_id = $wtid;
            $tmp->lg_id = $tid;
            $tmp->save();
        }
    }

    //===============比赛接口相关===========================
    public static function getIconByTid($tid) {
        $team = BasketTeam::query()->find($tid);
        if (isset($team)) {
            $icon = $team->icon;
        }
        if (isset($icon) && strlen($icon) > 0 && !str_contains($icon, '/files/team/noflag.gif')) {
            if (str_contains($icon, '.gif') && str_contains($icon, 'team/images/2005')) {
                return "";
            }
            if (str_contains($icon, 'nba_icon_'))
                return 'http://qiushengke.com/teamicon/nba/'.$icon.'.png';
            else if (str_contains($icon, 'http'))
                return $icon;
            else
                return 'http://nba.win007.com'.$icon;
        } else {
            return "";
        }
    }

    public static function getIcon($icon) {
        if (isset($icon) && strlen($icon) > 0 && !str_contains($icon, '/files/team/noflag.gif')) {
            if (str_contains($icon, '.gif') && str_contains($icon, 'team/images/2005')) {
                return "";
            }
            if (str_contains($icon, 'nba_icon_'))
                return 'http://qiushengke.com/teamicon/nba/'.$icon.'.png';
            else if (str_contains($icon, 'http'))
                return $icon;
            else
                return 'http://nba.win007.com'.$icon;
        } else {
            return "";
        }
    }
}
