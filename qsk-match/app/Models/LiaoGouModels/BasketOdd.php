<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class BasketOdd extends Model
{
    protected $connection = 'liaogou_match';
    const k_odd_type_asian = 1;//亚盘
    const k_odd_type_ou = 2;//大小
    const k_odd_type_europe = 3;//欧赔
    const k_odd_type_corner = 4;//角球
    const default_banker_id = 5;//默认公司id 取bet365(角球计算、历史同赔)
    const default_calculate_cid = 2;//默认计算用的博彩公司id 取sb

    const default_article_bankers = [2,5,8];

    //
    public $timestamps = false;

    protected $guarded = ['id'];

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
    public static function panKouText($middle, $isAway = false, $isGoal = false) {
        if ($isGoal || $middle == 0){
            $prefix = "";
        } else{
            if ($isAway){
                $prefix = $middle < 0 ? "让" : "受让";
            }else{
                $prefix = $middle < 0 ? "受让" : "让";
            }
        }
        return $prefix . abs($middle) . '分';
    }

    static public function saveDataWithWinData($wo)
    {
        $match = BasketMatch::getMatchWith($wo->mid, 'win_id');
        if (isset($match)) {
            $mid = $match->id;
            $cid = Banker::getBankerIdWithType($wo->cid, 'win_id');
            if (!in_array($cid, self::default_article_bankers)) {
                echo  "cid = $cid is not need </br>";
                return;
            }
            $o = BasketOdd::where(["mid" => $mid, "cid" => $cid, "type" => $wo->type])->first();
            if (!isset($o)) {
                $o = new BasketOdd();
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
            BasketOddsAfter::saveDataWithWinData($match, $cid, $wo);
        } else {
            dump('win_mid ' . $wo->mid . ' not found');
        }
    }

    static public function updateLgOdd($odd=null, $match, $cid, $type, $up1, $up2, $middle1, $middle2, $down1, $down2) {
        if (isset($match)) {
            $mid = $match->id;
            if (!in_array($cid, self::default_article_bankers)) {
                echo  "cid = $cid is not need </br>";
                return;
            }
            if (is_null($odd)) {
                $odd = BasketOdd::where(["mid" => $mid, "cid" => $cid, "type" => $type])->first();
            }
            if (!isset($odd)) {
                $odd = new BasketOdd();
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

            if (!$odd->save()) {
                echo 'match ' . $mid . ' save error' . '</br>';
            }

            //同时把信息保存到冗余表
            BasketOddsAfter::saveDataWithLgData($match, $odd);
        }
    }

    private static function isStrEmpty($str) {
        return !isset($str) || strlen($str) <= 0;
    }
}
