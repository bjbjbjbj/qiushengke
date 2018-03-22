<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class OddHalf extends Model
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

    public function match()
    {
        return $this->hasOne('App\Models\LiaoGouModels\Match', 'id', 'mid');
    }

    static public function saveDataWithWinData($wo)
    {
        $match = Match::getMatchWith($wo->mid, 'win_id');
        if (isset($match)) {
            $mid = $match->id;
            $cid = Banker::getBankerIdWithType($wo->cid, 'win_id');
            $o = OddHalf::where(["mid" => $mid, "cid" => $cid, "type" => $wo->type])->first();
            if (!isset($o)) {
                $o = new OddHalf();
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

    static public function saveDataWithLgData($match, $lg_odd)
    {
        if (isset($lg_odd)) {
            $o = OddHalf::where(["mid" => $lg_odd->mid, "cid" => $lg_odd->cid, "type" => $lg_odd->type])->first();
            if (!isset($o)) {
                $o = new OddHalf();
                $o->mid = $lg_odd->mid;
                $o->cid = $lg_odd->cid;
                $o->type = $lg_odd->type;
            }
            $o->up1 = $lg_odd->up1;
            $o->up2 = $lg_odd->up2;
            $o->middle1 = $lg_odd->middle1;
            $o->middle2 = $lg_odd->middle2;
            $o->down1 = $lg_odd->down1;
            $o->down2 = $lg_odd->down2;
            if (!$o->save()) {
                echo 'match ' . $lg_odd->mid . ' save error' . '</br>';
            }

            //同时把信息保存到冗余表
            OddsAfter::saveDataWithLgData($match, $lg_odd);
        }
    }
}
