<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 17/3/3
 * Time: 下午12:49
 */

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;
class BettingNumMid extends Model
{
    protected $connection = 'liaogou_lottery';
    const from_cube = 1;

    public $timestamps = false;

    protected $hidden = ['id','mid'];

    protected $primarykey = 'id';

    public static function getCube($issueNum){
        return BettingNumMid::query()->where('betting_issue_num', $issueNum)->where('from', self::from_cube)->first();
    }

    public function sport_betting(){
        return SportBetting::query()->where('betting_issue_num', $this->betting_issue_num)->first();
    }
}
