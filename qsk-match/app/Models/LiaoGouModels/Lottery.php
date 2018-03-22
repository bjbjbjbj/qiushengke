<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class Lottery extends Model
{
    protected $connection = 'liaogou_lottery';
    public $timestamps = false;

    protected $primarykey = 'id';

    protected $hidden = ['win_id'];

    public function lotteryDatas()
    {
        return $this->hasMany('App\LiaoGouModels\LotteryDetail', 'lottery_id');
    }
}
