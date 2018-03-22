<?php

namespace App\Models\AnalyseModels;

use Illuminate\Database\Eloquent\Model;

class BasketLeague extends Model
{
    //
    protected $connection = 'analyse_match';
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
