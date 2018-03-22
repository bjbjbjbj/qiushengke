<?php

namespace App\Models\AnalyseModels;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    //
    protected $connection = 'analyse_match';
    public $timestamps = false;

    protected $hidden = ['create_at', "spider_at"];

    public function leagueSeasons()
    {
        return $this->hasMany('App\LiaoGouModels\Season', 'lid', 'id');
    }

    public function leagueSeason($time)
    {
        $year = Carbon::parse($time)->format('Y');
        $season = $this->hasMany('App\LiaoGouModels\Season', 'lid', 'id')
            ->where('year', $year)
            ->first();
        if (empty($season)) {
            $season = new Season();
        }
        return $season;
    }

    public function leagueState()
    {
        return $this->hasOne('App\LiaoGouModels\State', 'id', 'state');
    }

    static public function getLeagueIdWithType($lid,$from){
        $league = League::where($from,$lid)->first();
        if (isset($league))
            return $league->id;
        else{
            return 0;
        }
    }

    static public function getLeagueWithType($lid,$from){
        $league = League::where($from,$lid)->first();
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
