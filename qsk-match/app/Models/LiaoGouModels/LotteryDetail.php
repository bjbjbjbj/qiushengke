<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class LotteryDetail extends Model
{
    protected $connection = 'liaogou_lottery';
    public $timestamps = false;

    protected $hidden = ['id','mid','lid'];

    protected $primarykey = 'id';
}
