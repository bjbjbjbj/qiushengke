<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class OddsAfter extends Model
{
    protected $connection = 'liaogou_match';
    const k_odd_type_asian = 1;//亚盘
    const k_odd_type_ou = 2;//大小
    const k_odd_type_europe = 3;//欧赔
    const k_odd_type_corner = 4;//角球

    const default_article_bankers = [6,7,12];
    //
    //
//    public $timestamps = false;

    public function changeConnection($conn)
    {
        $this->connection = $conn;
    }

    public function match()
    {
        return $this->hasOne('App\Models\LiaoGouModels\Match', 'id', 'mid');
    }

    /**
     * @param $match Match 料狗比赛
     * @param $lg_odd Odd liaogou下的odd
     */
    static public function saveDataWithLgData($match, $lg_odd)
    {
        if (isset($match)) {
            $mid = $match->id;
            //如果比赛结束了，或者比赛时间超过了未来的3天，则不再更新盘口信息
            if (!$match->isCanAddToAfter() || !in_array($lg_odd->cid, self::default_article_bankers)) {
//                echo 'reject add to odd_afters: mid = '.$mid.', match status = ' . $match->status . ', cid = ' . $lg_odd->cid . '</br>';
                return;
            }

            $o = OddsAfter::where(["mid" => $lg_odd->mid, "cid" => $lg_odd->cid, "type" => $lg_odd->type])->first();
            $change = false;
            if (!isset($o)) {
                $o = new OddsAfter();
                $o->mid = $mid;
                $o->cid = $lg_odd->cid;
                $o->type = $lg_odd->type;
                $change = true;
            }

            if (!$change && (
                    $o->match_time != $match->time ||
                    $o->up1 != $lg_odd->up1||
                    $o->up2 != $lg_odd->up2||
                    $o->middle1 != $lg_odd->middle1||
                    $o->middle2 != $lg_odd->middle2 ||
                    $o->down1 != $lg_odd->down1 ||
                    $o->down2 != $lg_odd->down2)
            ){
                $change = true;
            }

            $o->match_time = $match->time;
            $o->up1 = $lg_odd->up1;
            $o->up2 = $lg_odd->up2;
            $o->middle1 = $lg_odd->middle1;
            $o->middle2 = $lg_odd->middle2;
            $o->down1 = $lg_odd->down1;
            $o->down2 = $lg_odd->down2;
            if ($change) {
                if (!$o->save()) {
                    echo 'match ' . $mid . ' save error' . '</br>';
                }
            }
        }
    }

    /**
     * @param $match Match 料狗比赛
     * @param $cid int 料狗博彩公司id
     * @param $wo Odd win下的odd
     */
    static public function saveDataWithWinData($match, $cid, $wo)
    {
        if (isset($match)) {
            $mid = $match->id;
            //如果比赛结束了，或者比赛时间超过了未来的3天，则不再更新盘口信息
            if (!$match->isCanAddToAfter() || !in_array($cid, self::default_article_bankers)) {
                echo 'reject add to odd_afters: mid = '.$mid.', match status = ' . $match->status . ', cid = ' . $cid . '</br>';
                return;
            }

            $o = OddsAfter::where(["mid" => $mid, "cid" => $cid, "type" => $wo->type])->first();
            $change = false;
            if (!isset($o)) {
                $o = new OddsAfter();
                $o->mid = $mid;
                $o->cid = $cid;
                $o->type = $wo->type;
                $change = true;
            }

            if (!$change && (
                    $o->match_time != $match->time ||
                    $o->up1 != $wo->up1||
                    $o->up2 != $wo->up2||
                    $o->middle1 != $wo->middle1||
                    $o->middle2 != $wo->middle2 ||
                    $o->down1 != $wo->down1 ||
                    $o->down2 != $wo->down2)
            ){
                $change = true;
            }

            $o->match_time = $match->time;
            $o->up1 = $wo->up1;
            $o->up2 = $wo->up2;
            $o->middle1 = $wo->middle1;
            $o->middle2 = $wo->middle2;
            $o->down1 = $wo->down1;
            $o->down2 = $wo->down2;
            if ($change) {
                if (!$o->save()) {
                    echo 'match ' . $mid . ' save error' . '</br>';
                }
            }
        } else {
            dump('win_mid ' . $wo->mid . ' not found');
        }
    }

    public static function deleteUselessData($takeCount) {
        //删除4小时前数据
        $query = OddsAfter::query()
            ->where(function ($q) {
                $q->where("match_time", "<", date_create("-4 hours"))
                    ->orWhereNotIn("cid", self::default_article_bankers);
            })
            ->orderby('match_time', 'asc');
        if ($takeCount > 0) {
            $query->take($takeCount);
        }
        $tempQuery = clone $query;
        $query->delete();

        echo "本次共删除OddAfters ".$tempQuery->get()->count()." 条数据</br>";
    }

    public static function deleteUselessData2($takeCount) {
        //删除24小时前数据
        $query = OddsAfter::query()->where("match_time", ">", date_create("+3 days"))
            ->orderby('match_time', 'asc');
        if ($takeCount > 0) {
            $query->take($takeCount);
        }
        $tempQuery = clone $query;
        $query->delete();

        echo "本次共删除OddAfters ".$tempQuery->get()->count()." 条数据</br>";
    }
}
