<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class BasketOddsAfter extends Model
{
    protected $connection = 'liaogou_match';
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
     * @param $match BasketMatch 料狗比赛
     * @param $cid int 料狗博彩公司id
     * @param $wo Odd win下的odd
     */
    static public function saveDataWithWinData($match, $cid, $wo)
    {
        if (isset($match)) {
            $mid = $match->id;
            //如果比赛结束了，或者比赛时间超过了未来的5天，则不再更新盘口信息
            if (!$match->isCanAddToAfter() || !in_array($cid, BasketOdd::default_article_bankers)) {
                echo 'mid = '.$mid.', match status = ' . $match->status . '</br>';
                return;
            }

            $o = BasketOddsAfter::where(["mid" => $mid, "cid" => $cid, "type" => $wo->type])->first();
            if (!isset($o)) {
                $o = new BasketOddsAfter();
                $o->mid = $mid;
                $o->cid = $cid;
                $o->type = $wo->type;
            }
            $o->match_time = $match->time;
            $o->up1 = $wo->up1;
            $o->up2 = $wo->up2;
            $o->middle1 = $wo->middle1;
            $o->middle2 = $wo->middle2;
            $o->down1 = $wo->down1;
            $o->down2 = $wo->down2;
            if (!$o->save()) {
                echo 'match ' . $mid . ' save error' . '</br>';
            }
        } else {
            dump('win_mid ' . $wo->mid . ' not found');
        }
    }

    static public function saveDataWithLgData($match, $lg_odd) {
        if (isset($match)) {
            $mid = $match->id;
            $cid = $lg_odd->cid;
            //如果比赛结束了，或者比赛时间超过了未来的5天，则不再更新盘口信息
            if (!$match->isCanAddToAfter() || !in_array($cid, BasketOdd::default_article_bankers)) {
                echo 'mid = '.$mid.', match status = ' . $match->status . '</br>';
                return;
            }

            $o = BasketOddsAfter::where(["mid" => $mid, "cid" => $cid, "type" => $lg_odd->type])->first();
            if (!isset($o)) {
                $o = new BasketOddsAfter();
                $o->mid = $mid;
                $o->cid = $cid;
                $o->type = $lg_odd->type;
            }
            $o->match_time = $match->time;
            $o->up1 = $lg_odd->up1;
            $o->up2 = $lg_odd->up2;
            $o->middle1 = $lg_odd->middle1;
            $o->middle2 = $lg_odd->middle2;
            $o->down1 = $lg_odd->down1;
            $o->down2 = $lg_odd->down2;
            if (!$o->save()) {
                echo 'match ' . $mid . ' save error' . '</br>';
            }
        }
    }

    public static function deleteUselessData($takeCount) {
        //删除12小时前数据
        $query = BasketOddsAfter::query()->where("match_time", "<", date_create("-12 hours"))
            ->orderby('match_time', 'asc');
        if ($takeCount > 0) {
            $query->take($takeCount);
        }
        $tempQuery = clone $query;
        $query->delete();

        echo "本次共删除BasketOddAfters ".$tempQuery->get()->count()." 条数据</br>";
    }
}
