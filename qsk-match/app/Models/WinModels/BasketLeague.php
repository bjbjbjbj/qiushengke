<?php

namespace App\Models\WinModels;

use Illuminate\Database\Eloquent\Model;

class BasketLeague extends Model
{
    //

    public $incrementing = false;
    protected $connection = 'win_matches';
    public $timestamps = false;

    /**
     * 获取最新赛季信息
     */
    public function lastSeason()
    {
        return BasketSeason::query()->where("lid", $this->id)->orderBy("year", "desc")->first();
    }

    public function getLastSeasonName() {
        $lastSeason = $this->lastSeason();
        if (isset($lastSeason)) {
            return $lastSeason->name;
        }
        return NULL;
    }
}
