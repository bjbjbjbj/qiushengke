<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/20
 * Time: 16:38
 */

namespace App\Models\QSK\Video;


use Illuminate\Database\Eloquent\Model;

/**
 * 热门录像类型
 * Class VideoType
 * @package App\Models\QSK\Video
 */
class HotVideoType extends Model
{
    const kStatusShow = 1, kStatusHide = 2;//1:显示，2：隐藏。

    protected $connection = 'qsk';

    /**
     * 获取所有显示的录像类型
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getAllTypes() {
        $query = self::query();
        $query->where('status', self::kStatusShow);
        $query->selectRaw('*, ifNull(od, 999) as n_od');
        return $query->orderBy('n_od')->get();
    }
}