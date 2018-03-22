<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class MatchForecast extends Model
{
    protected $connection = 'liaogou_match';

    const kForecastTypeAsian = 1;
    const kForecastTypeOu = 2;
    const kForecastTypeChina = 3;

}
