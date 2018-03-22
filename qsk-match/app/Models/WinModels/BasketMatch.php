<?php

namespace App\Models\WinModels;

use App\Http\Controllers\Tool\MatchControllerTool;
use Illuminate\Database\Eloquent\Model;

class BasketMatch extends Model
{
    public $incrementing = false;
    protected $connection = 'win_matches';
    //
    public $timestamps = false;

    protected $hidden = ['id'];

    /**
     * 获取联赛信息
     */
    public function league()
    {
        return $this->hasOne('App\Models\WinModels\BasketLeague', 'id', 'lid');
    }

    /**
     * 获取联赛的名称
     * @param $match BasketMatch
     * @return string
     */
    public static function getLeagueName($match) {
        $league = $match->league;
        if (isset($league)) {
            return $league->name;
        }
        return NULL;
    }

    /**
     * 获取最新的赛季名称
     * @param $match BasketMatch
     * @return string
     */
    public static function getLastSeasonName($match) {
        $league = $match->league;
        if (isset($league)) {
            return $league->getLastSeasonName();
        }
        return NULL;
    }
}
