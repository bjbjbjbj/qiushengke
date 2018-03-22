<?php

namespace App\Models\WinModels;

use App\Http\Controllers\Tool\MatchControllerTool;
use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
    public $incrementing = false;
    protected $connection = 'win_matches';
    const k_genre_all = 1;//全部
    const k_genre_yiji = 2;//一级
    const k_genre_zucai = 4;//足彩
    const k_genre_jingcai = 8;//竞彩
    const k_genre_beijing = 16;//北京单场
    //
    public $timestamps = false;

    protected $hidden = ['id'];

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
        return $this->hasOne('App\Models\WinModels\MatchData', 'id', 'id');
    }

    public function leagueData()
    {
        return $this->belongsTo('App\Models\WinModels\League', 'lid', 'id');
    }

    public function forecast()
    {
        return $this->hasOne('App\Models\WinModels\MatchesForecast', 'id');
    }

    public function setStatus($status)
    {
        $isToOver = (!isset($this->status) || $this->status != -1) && $status == -1;
        $this->status = $status;

        return $isToOver;
    }
//    public function setStatus($status)
//    {
//        //比赛状态是否转变成结束
//        $isToOver = false;
//        try {
//            if ((!isset($this->status) || $this->status != -1) && $status == -1) {
//                $isToOver = true;
//                $matchTool = new MatchControllerTool();
//                $this->status = $status;
//                $matchTool->onStatusToOver($this);
//            } else if ((!isset($this->status) || $this->status == -14) && $status == 0) {
//                $this->has_lineup = NULL;
//                $this->inflexion = NULL;
////                $this->same_odd = NULL;
//                //同赔重置
//                $liaogou = \App\Models\LiaoGouModels\Match::where('win_id',$this->id)->first();
//                if (isset($liaogou)){
//                    $liaogou->same_odd = NULL;
//                    $liaogou->save();
//                }
//
//                $this->status = $status;
//            } else {
//                $this->status = $status;
//            }
//        } catch (\Exception $e) {
//            echo $e;
//        } finally {
//            $this->status = $status;
//        }
//        return $isToOver;
//    }
}
