<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 17/3/3
 * Time: 下午12:49
 */

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;
class SpiderLotteryTips extends Model
{
    protected $connection = 'liaogou_lottery';
    public $timestamps = false;

    protected $hidden = ['id','mid'];

    protected $primarykey = 'id';

    public static function findByCubeId($cubeId){
        return SpiderLotteryTips::query()->where('cube_mid', $cubeId)->first();
    }

    public static function findByLeisuId($leisuId){
        return SpiderLotteryTips::query()->where('leisu_mid', $leisuId)->first();
    }

    public static function findByMatchId($mid){
        return SpiderLotteryTips::query()->where('mid', $mid)->first();
    }

    public function sport_betting(){
        return SportBetting::query()->where('betting_issue_num', $this->betting_issue_num)->first();
    }
}
