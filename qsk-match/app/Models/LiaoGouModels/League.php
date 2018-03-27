<?php

namespace App\Models\LiaoGouModels;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    //
    protected $connection = 'liaogou_match';
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

    static public function saveDataWithWinData($wl){
        $l = League::where('win_id',$wl->id)->first();
        if (!isset($l)) {
            $l = new League();
            $l->win_id = $wl->id;
        }
        foreach ($wl->getAttributes() as $key => $value){
            if ($key != 'id')
                $l[$key] = $value;
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

    /**
     * 五大联赛id
     */
    public static function getFiveLids() {
//        return [31,26,29,8,11];
        return [42,1,30,64,61];
    }
}
