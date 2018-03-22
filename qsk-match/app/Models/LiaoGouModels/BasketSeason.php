<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class BasketSeason extends Model
{
    //
    protected $connection = 'liaogou_match';

    static public function saveDataWithWinData($ws, $isNew = false){
        $lid = BasketLeague::getLeagueIdWithType($ws->lid,'win_id');
        //料狗库有赛事id再保存
        if ($lid > 0) {
            $s = BasketSeason::where(['lid' => $lid, 'name' => $ws->name])->first();
            //索引原因,如果修改了非索引字段save会报错?
            if ($isNew){
                if (!isset($s)) {
                    $s = new BasketSeason();
                    $s->lid = $lid;
                    foreach ($ws->getAttributes() as $key => $value){
                        if ($key != 'id' && $key != 'lid' && $key != 'spider_at' && $key != 'updated_at' && $key != 'created_at')
                            $s[$key] = $value;
                    }
                    $s->save();
                }
            }
            else{
                if (!isset($s)) {
                    $s = new BasketSeason();
                    $s->lid = $lid;
                }
                foreach ($ws->getAttributes() as $key => $value){
                    if ($key != 'id' && $key != 'lid' && $key != 'spider_at' && $key != 'updated_at' && $key != 'created_at')
                        $s[$key] = $value;
                }
                $s->save();
            }
        }
    }
}
