<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/18
 * Time: 15:03
 */

namespace App\Models\QSK\Match;


use Illuminate\Database\Eloquent\Model;

class BasketLeague extends Model
{

    protected $connection = 'qsk';

    const kStatusBook = 2, kStatusHide = 1;//1：可预约赛事不显示该联赛的比赛，2：可预约赛事显示该联赛的比赛。
    const kMain = 1;//主流联赛
    const kHot = 1;//热门联赛

    /**
     * 获取预约赛事的id数组
     * @return array
     */
    public static function getBookLids() {
        $query = self::query()->where('status', self::kStatusBook)->select('id');
        $array = $query->get()->toArray();
        $ids = [];
        foreach ($array as $id) {
            $ids[] = $id['id'];
        }
        return $ids;
    }
}