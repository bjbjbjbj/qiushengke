<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    protected $connection = 'liaogou_match';
    //

    public $timestamps = false;

    protected $hidden = ['win_id'];

    /**
     * 更新赛季轮次
     * @param $ws
     */
    static public function saveDataWithRound($ws){
        $lid = League::getLeagueIdWithType($ws->lid,'win_id');
        if ($lid > 0) {
            $s = Season::where(['lid' => $lid, 'name' => $ws->name])->first();
            if (isset($s)) {
                $s->total_round = $ws->total_round;
                $s->curr_round = $ws->curr_round;
                $s->save();
            }
            else{
                $s = new Season();
                $s->lid = $lid;
                $s->name = $ws->name;
                $s->total_round = $ws->total_round;
                $s->curr_round = $ws->curr_round;
                $s->save();
            }
        }
    }


    /**
     * 更新赛季开始时间
     * @param $ws
     */
    static public function saveDataWithStartTime($ws){
        $lid = League::getLeagueIdWithType($ws->lid,'win_id');
        if ($lid > 0) {
            $s = Season::where(['lid' => $lid, 'name' => $ws->name])->first();
            if (isset($s)) {
                $s->start = $ws->start;
                $s->save();
            }
        }
    }

    /**
     * 根据球探season数据创建或修改season数据
     * @param $ws 球探数据
     * @param bool $isNew 是否需要创建
     */
    static public function saveDataWithWinData($ws, $isNew = false){
        $lid = League::getLeagueIdWithType($ws->lid,'win_id');
        //料狗库有赛事id再保存
        if ($lid > 0) {
            $s = Season::where(['lid' => $lid, 'name' => $ws->name])->first();
            //索引原因,如果修改了非索引字段save会报错?
            if ($isNew){
                if (!isset($s)) {
                    $s = new Season();
                    $s->lid = $lid;
                    foreach ($ws->getAttributes() as $key => $value){
                        if ($key != 'id' && $key != 'lid')
                            $s[$key] = $value;
                    }
                    $s->save();
                }
            }
            else{
                if (!isset($s)) {
                    $s = new Season();
                    $s->lid = $lid;
                }
                foreach ($ws->getAttributes() as $key => $value){
                    if ($key != 'id' && $key != 'lid')
                        $s[$key] = $value;
                }
                $s->save();
            }
        }
    }
}
