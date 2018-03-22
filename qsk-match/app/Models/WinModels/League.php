<?php

namespace App\Models\WinModels;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    //

    public $incrementing = false;
    protected $connection = 'win_matches';
    public $timestamps = false;

    protected $hidden = ['create_at', "spider_at"];

    public function leagueSeasons()
    {
        return $this->hasMany('App\Models\WinModels\Season', 'lid', 'id');
    }

    public function leagueSeason($time)
    {
        $year = Carbon::parse($time)->format('Y');
        $season = $this->hasMany('App\Models\WinModels\Season', 'lid', 'id')
            ->where('year', $year)
            ->first();
        if (empty($season)) {
            $season = new Season();
        }
        return $season;
    }

    public function leagueState()
    {
        return $this->hasOne('App\Models\WinModels\State', 'id', 'state');
    }

    static public function getLeagueIdWithType($lid,$from){
        $league = League::where($from,$lid)->first();
        if (isset($league))
            return $league->id;
        else{
            return 0;
        }
    }
}
