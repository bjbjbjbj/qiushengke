<?php

namespace App\Models\WinModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class Odd extends Model
{
    protected $connection = 'win_matches';
    const k_odd_type_asian = 1;//亚盘
    const k_odd_type_ou = 2;//大小
    const k_odd_type_europe = 3;//欧赔
    const k_odd_type_corner = 4;//角球
    const default_banker_id = 3;//默认公司id 取sb
    const default_calculate_cid = 8;//默认计算用的博彩公司id 取bet365
    //
    public $timestamps = false;

    //后台推荐文章可选的几家博彩公司
    const default_article_cids = [3, 8, 23]; //SB、bet365、金宝博

    protected $guarded = ['id'];

    static public function getCache($mid,$cid,$type){
        $key = 'odd_'.$mid.'_'.$cid.'_'.$type;
        //有缓存直接返回
//        if (Redis::exists($key)){
//            $odd = Redis::get($key);
//            $json = json_decode($odd,true);
//            if (isset($json)) {
//                $odd = new Odd($json);
//                return $odd;
//            }
//        }

        $odd = Odd::where('mid',$mid)
            ->where('cid',$cid)
            ->where('type',$type)
            ->first();
//        dump($odd);
//        Redis::set($key,$odd);
//        Redis::expire($key, 3 * 60 * 60);
        return $odd;
    }

    static public function updateCache($odd=null, $mid, $cid, $type, $up1, $up2, $middle1, $middle2, $down1, $down2, $isOddHalf=false){
        $key = 'odd_'.$mid.'_'.$cid.'_'.$type;
        //有缓存只要更新就好
        $down2 = trim($down2);
        if (is_null($odd)) {
            $odd = Odd::where('mid', $mid)
                ->where('cid', $cid)
                ->where('type', $type)
                ->first();
        }
        if (!isset($odd)) {
            $odd = new Odd();
            $odd->mid = $mid;
            $odd->cid = $cid;
            $odd->type = $type;
        }
        if (!self::isStrEmpty($up1)) {
            $up1 = str_replace(array("\n","\t"," "),'',$up1);
            $odd->up1 = $up1;
        }
        if (!self::isStrEmpty($up2)) {
            $up2 = str_replace(array("\n","\t"," "),'',$up2);
            $odd->up2 = $up2;
        }
        if (!self::isStrEmpty($middle1)) {
            $middle1 = str_replace(array("\n","\t"," "),'',$middle1);
            $odd->middle1 = $middle1;
        }
        if (!self::isStrEmpty($middle2)) {
            $middle2 = str_replace(array("\n","\t"," "),'',$middle2);
            $odd->middle2 = $middle2;
        }
        if (!self::isStrEmpty($down1)) {
            $down1 = str_replace(array("\n","\t"," "),'',$down1);
            $odd->down1 = $down1;
        }
        if (!self::isStrEmpty($down2)) {
            $down2 = str_replace(array("\n","\t"," "),'',$down2);
            $odd->down2 = $down2;
        }
        if (!self::isStrEmpty($odd->middle1)) {
            $odd->save();
            if ($isOddHalf) {
                \App\Models\LiaoGouModels\OddHalf::saveDataWithWinData($odd);
            } else {
                \App\Models\LiaoGouModels\Odd::saveDataWithWinData($odd);
            }
        }
//        Redis::set($key,$odd);
//        Redis::expire($key, 3 * 60*60);
    }

    private static function isStrEmpty($str) {
        return !isset($str) || strlen($str) <= 0;
    }


}
