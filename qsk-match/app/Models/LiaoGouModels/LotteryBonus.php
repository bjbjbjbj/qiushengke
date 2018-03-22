<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class LotteryBonus extends Model
{
    protected $connection = 'liaogou_lottery';
    public $timestamps = false;

    protected $hidden = ['id','lid'];

    protected $primarykey = 'id';
}
