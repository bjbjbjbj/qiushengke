<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class MatchEuropePrediction extends Model
{
    protected $connection = 'liaogou_analyse';
    //
    public $timestamps = false;

    protected $hidden = ['id'];
}
