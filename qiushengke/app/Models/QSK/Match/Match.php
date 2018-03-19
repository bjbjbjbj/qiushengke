<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/17
 * Time: 21:03
 */

namespace App\Models\QSK\Match;


use Illuminate\Database\Eloquent\Model;

/**
 * 只保留 1天前 - 3天内的比赛
 * Class Match
 * @package App\Models\QSK\Match
 */
class Match extends Model
{
    protected $connection = 'qsk';

    const k_genre_all = 1;//全部
    const k_genre_yiji = 2;//一级
    const k_genre_zucai = 4;//足彩
    const k_genre_jingcai = 8;//竞彩
    const k_genre_beijing = 16;//北京单场
    //
    public $timestamps = false;

    protected $hidden = ['win_id'];

}