<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Referee extends Model
{
    protected $connection = 'liaogou_match';
    //
    public $timestamps = false;

    static public function saveDataWithWinData($win_ref, $win_ref_id){
        $lg_ref = Referee::query()->where('win_id', $win_ref_id)->first();
        if (!isset($lg_ref)) {
            $lg_ref = new Referee();
        }
        foreach ($win_ref->getAttributes() as $key => $value){
            if ($key != 'id')
                $lg_ref[$key] = $value;
        }
        $lg_ref->win_id = $win_ref_id;
        $lg_ref->save();

        return $lg_ref;
    }

    static public function getRefIdWith($id,$from){
        $ref = Referee::where($from,$id)->first();
        if (isset($ref)){
            return $ref->id;
        }
        else{
            return 0;
        }
    }

    //===========接口相关======================
    //裁判执法球队的比赛
    public static function getRefereeTeamWinPercent($matchTime, $tid, $referee_id) {
        $matches = DB::connection('liaogou_match')->select("
        select m.*, md.referee_id from
        (select id, referee_id from match_datas where referee_id = $referee_id) as md
        left join matches as m on md.id = m.id
        where (m.hid = $tid or m.aid = $tid) and m.status = -1 and m.time < '$matchTime'
        order by time desc;");

        $count = count($matches);
        $winCount = 0;
        $drawCount = 0;
        if ($count >= 0) {
            $winCount = 0;
            $drawCount = 0;
            foreach ($matches as $match) {
                $diff = $match->hscore - $match->ascore;
                if ($diff == 0) {
                    $drawCount++;
                } else if ($match->hid = $tid && $diff > 0){
                    $winCount++;
                } else if ($match->aid == $tid && $diff < 0) {
                    $winCount++;
                }
            }
        }
        return ['win'=>$winCount,'draw'=>$drawCount, 'lose'=>$count-$winCount-$drawCount, 'count'=>$count];
    }
}
