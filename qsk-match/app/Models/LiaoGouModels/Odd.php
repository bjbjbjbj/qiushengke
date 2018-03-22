<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class Odd extends Model
{
    protected $connection = 'liaogou_match';
    const k_odd_type_asian = 1;//亚盘
    const k_odd_type_ou = 2;//大小
    const k_odd_type_europe = 3;//欧赔
    const k_odd_type_corner = 4;//角球
    const default_banker_id = 5;//默认公司id 取bet365(角球计算、历史同赔)
    const default_calculate_cid = 2;//默认计算用的博彩公司id 取sb

    const default_top_bankers = [1, 2, 3, 5, 7, 8, 12];
    const default_top_rank_bankerStr = "2,5,1,3,7,8,12";
    //
    //
    public $timestamps = false;

    public function changeConnection($conn)
    {
        $this->connection = $conn;
    }

    /**
     * @param $middle float 盘口
     * @param bool $isAway 是否是客队
     * @param bool $isGoal 是否是大小球
     * @return string
     */
    public static function panKouText ($middle, $isAway = false, $isGoal = false) {
        if ($isGoal || $middle == 0){
            $prefix = "";
        } else{
            if ($isAway){
                $prefix = $middle < 0 ? "让" : "受让";
            }else{
                $prefix = $middle < 0 ? "受让" : "让";
            }
        }
        $text = $middle;
        $middle = abs($middle);
        switch ($middle) {
            case 7: $text = "七球"; break;
            case 6.75: $text = "六半/七球"; break;
            case 6.5: $text = "六球半"; break;
            case 6.25: $text = "六球/六半"; break;
            case 6: $text = "六球"; break;
            case 5.75: $text = "五半/六球"; break;
            case 5.5: $text = "五球半"; break;
            case 5.25: $text = "五球/五半"; break;
            case 5: $text = "五球"; break;
            case 4.75: $text = "四半/五球"; break;
            case 4.5: $text = "四球半"; break;
            case 4.25: $text = "四球/四半"; break;
            case 4: $text = "四球"; break;
            case 3.75: $text = "三半/四球"; break;
            case 3.5: $text = "三球半"; break;
            case 3.25: $text = "三球/三半"; break;
            case 3: $text = "三球"; break;
            case 2.75: $text = "两半/三球"; break;
            case 2.5: $text = "两球半"; break;
            case 2.25: $text = "两球/两半"; break;
            case 2: $text = "两球"; break;
            case 1.75: $text = "球半/两球"; break;
            case 1.5: $text = "球半"; break;
            case 1.25: $text = "一球/球半"; break;
            case 1: $text = "一球"; break;
            case 0.75: $text = "半/一"; break;
            case 0.5: $text = "半球"; break;
            case 0.25: $text = "平手/半球"; break;
            case 0: $text = "平手"; break;
        }
        if (!is_numeric($text)) {
            return $prefix . $text;
        }
        return $text;
    }

    static public function saveDataWithWinData($wo, $match = null)
    {
        if ($match == null) {
            $match = Match::getMatchWith($wo->mid, 'win_id');
        }
        if (isset($match)) {
            $mid = $match->id;
            $cid = Banker::getBankerIdWithType($wo->cid, 'win_id');
            $o = Odd::where(["mid" => $mid, "cid" => $cid, "type" => $wo->type])->first();
            if (!isset($o)) {
                $o = new Odd();
                $o->mid = $mid;
                $o->cid = $cid;
                $o->type = $wo->type;
            }
            $o->up1 = $wo->up1;
            $o->up2 = $wo->up2;
            $o->middle1 = $wo->middle1;
            $o->middle2 = $wo->middle2;
            $o->down1 = $wo->down1;
            $o->down2 = $wo->down2;
            if (!$o->save()) {
                echo 'match ' . $mid . ' save error' . '</br>';
            }

            //同时把信息保存到冗余表
            OddsAfter::saveDataWithWinData($match, $cid, $wo);
        } else {
            dump('win_mid ' . $wo->mid . ' not found');
        }
    }

    static public function updateLgOdd($odd=null, $match, $cid, $type, $up1, $up2, $middle1, $middle2, $down1, $down2, $isOddHalf=false){
        $mid = $match->id;
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
            OddsAfter::saveDataWithLgData($match, $odd);
            if ($isOddHalf) {
                \App\Models\LiaoGouModels\OddHalf::saveDataWithLgData($match, $odd);
            }
        }
    }

    private static function isStrEmpty($str) {
        return !isset($str) || strlen($str) <= 0;
    }
}
