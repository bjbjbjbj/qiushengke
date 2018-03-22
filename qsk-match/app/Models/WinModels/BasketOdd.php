<?php

namespace App\Models\WinModels;

use Illuminate\Database\Eloquent\Model;

class BasketOdd extends Model
{
    protected $connection = 'win_matches';
    const k_odd_type_asian = 1;//亚盘
    const k_odd_type_ou = 2;//大小
    const k_odd_type_europe = 3;//欧赔
    const k_odd_type_corner = 4;//角球
    const default_banker_id = 3;//默认公司id 取sb
    const default_calculate_cid = 8;//默认计算用的博彩公司id 取bet365

    /**
     * 篮球的banker id 和足球的略有不同
     * 相同：1澳门、3皇冠、8bet365
     * 不同：2易博胜、9韦德
     */
    const banker_convert_array = [1=>1, 3=>3, 8=>8, 12=>2, 14=>9];

    //
    public $timestamps = false;

    protected $guarded = ['id'];

    static public function updateCache($mid, $cid, $type, $up1, $up2, $middle1, $middle2, $down1, $down2)
    {
        $key = 'odd_' . $mid . '_' . $cid . '_' . $type;
        //有缓存只要更新就好
        $down2 = trim($down2);
        $odd = BasketOdd::where('mid', $mid)
            ->where('cid', $cid)
            ->where('type', $type)
            ->first();
        if (!isset($odd)) {
            $odd = new BasketOdd();
            $odd->mid = $mid;
            $odd->cid = $cid;
            $odd->type = $type;
        }
        if (!self::isStrEmpty($up1)) {
            $up1 = str_replace(array("\n", "\t", " "), '', $up1);
            $odd->up1 = $up1;
        }
        if (!self::isStrEmpty($up2)) {
            $up2 = str_replace(array("\n", "\t", " "), '', $up2);
            $odd->up2 = $up2;
        }
        if (!self::isStrEmpty($middle1)) {
            $middle1 = str_replace(array("\n", "\t", " "), '', $middle1);
            $odd->middle1 = $middle1;
        }
        if (!self::isStrEmpty($middle2)) {
            $middle2 = str_replace(array("\n", "\t", " "), '', $middle2);
            $odd->middle2 = $middle2;
        }
        if (!self::isStrEmpty($down1)) {
            $down1 = str_replace(array("\n", "\t", " "), '', $down1);
            $odd->down1 = $down1;
        }
        if (!self::isStrEmpty($down2)) {
            $down2 = str_replace(array("\n", "\t", " "), '', $down2);
            $odd->down2 = $down2;
        }
        if (($type != 3 && !self::isStrEmpty($odd->middle1)) || ($type == 3 && !self::isStrEmpty($odd->up1))) {
            $odd->save();
            \App\Models\LiaoGouModels\BasketOdd::saveDataWithWinData($odd);
        }
//        Redis::set($key,$odd);
//        Redis::expire($key, 3 * 60*60);
    }

    private static function isStrEmpty($str)
    {
        return !isset($str) || strlen($str) <= 0;
    }
}
