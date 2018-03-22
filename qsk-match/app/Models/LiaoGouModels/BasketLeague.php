<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class BasketLeague extends Model
{
    //
    protected $connection = 'liaogou_match';
//    public $timestamps = false;

    static public function getLeagueIdWithType($lid,$from){
        $league = BasketLeague::where($from,$lid)->first();
        if (isset($league))
            return $league->id;
        else{
            return 0;
        }
    }

    static public function getLeagueWithType($lid,$from){
        $league = BasketLeague::where($from,$lid)->first();
        if (isset($league))
            return $league;
        else{
            return null;
        }
    }

    static public function saveDataWithWinData($wl,$lid = 0){
        if ($wl->id <= 0 && $lid > 0)
        {
            $wl = \App\Models\WinModels\BasketLeague::find($lid);
        }
        if (is_null($wl)){
            return;
        }
        $l = BasketLeague::where('win_id',$wl->id)->first();
        if (!isset($l)) {
            $l = new BasketLeague();
            $l->win_id = $wl->id;
        }
        foreach ($wl->getAttributes() as $key => $value){
            if ($key != 'id') {
                $l[$key] = $value;
            }
        }
        $l->save();
    }

    /**
     * 是否存在这名称的赛事，存在则 返回 true， 不存在则返回 false
     * @param $name
     * @return bool
     */
    public static function hasLeague($name) {
        $query = self::query();
        $like_name = '%' . $name . '%';
        $query->where('name', 'like', $like_name);
        $query->orWhere('name_big', 'like', $like_name);
        $query->orWhere('name_long', 'like', $like_name);
        return $query->count() > 0;
    }

}
