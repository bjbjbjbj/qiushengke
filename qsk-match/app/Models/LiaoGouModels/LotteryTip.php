<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 17/3/3
 * Time: 下午12:49
 */

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;
class LotteryTip extends Model
{
    protected $connection = 'liaogou_lottery';
    public $timestamps = false;

    protected $hidden = ['id','mid'];

    protected $primarykey = 'id';

    const k_lottery_tip_type_win = 1;//彩客
    const k_lottery_tip_type_8win = 2;//章鱼
    const k_lottery_tip_type_cube = 3;//魔方
    const k_lottery_tip_type_leisu = 4;//雷速

    public static function lotteryTipsCount($mid){
        return LotteryTip::query()->where('mid', $mid)->count();
    }
}
