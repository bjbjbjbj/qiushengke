<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class BasketMatchesAfter extends Model
{
    protected $connection = 'liaogou_match';
    //
//    public $timestamps = false;

    protected $hidden = ['id', 'win_id'];

    public function changeConnection($conn)
    {
        $this->connection = $conn;
    }

    static public function getMatchWith($id, $from)
    {
        $match = BasketMatchesAfter::where($from, $id)->first();
        if (isset($match)) {
            return $match;
        } else {
            return null;
        }
    }

    static public function getMatchIdWith($id, $from)
    {
        $match = BasketMatchesAfter::where($from, $id)->first();
        if (isset($match)) {
            return $match->id;
        } else {
            return 0;
        }
    }

    /**
     * 判断比赛是否已经结束
     */
    public function isMatchOver() {
        return $this->status == -1;
    }

    /**
     * 判断比赛是否应该加入冗余表
     */
    public function isCanAddToAfter()
    {
        if (is_string($this->time)) {
            $time = strtotime($this->time);
        } else {
            $time = date_timestamp_get($this->time);
        }
        return $this->status >= 0 && ($time > date_create("-4 hours")->getTimestamp() && $time < date_create("+2 days")->getTimestamp());
    }

    /**
     * @param $m BasketMatch 料狗的比赛表
     */
    static public function saveWithWinData($m){
        if (!isset($m)) return;

//        $odds = $m->oddsAfters;
//        if (count($odds) <= 0) {
//            //如果未开盘不插入冗余表
//            return;
//        }

        $match = BasketMatchesAfter::query()->find($m->id);
        $isShouldSave = false;
        if (isset($match)) {
            $isShouldSave = true;
        } elseif ($m->isCanAddToAfter()) {
            $match = new BasketMatchesAfter();
            $isShouldSave = true;
        }
        if ($isShouldSave) {
            foreach ($m->getAttributes() as $key => $value) {
                $match[$key] = $value;
            }
            $match->save();
//            echo "match after mid =".$m->id." has saved</br>";
        }
    }

    static public function deleteUselessData($takeCount) {
        //删除12小时前数据
        $query = BasketMatchesAfter::query()->where("time", "<", date_create("-12 hours"))
            ->orderby('time', 'asc');
        if ($takeCount > 0) {
            $query->take($takeCount);
        }
        $tempQuery = clone $query;
        $query->delete();

        echo "本次共删除BasketMatchesAfters ".$tempQuery->get()->count()." 条数据</br>";
    }
}
