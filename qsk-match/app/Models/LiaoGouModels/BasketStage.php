<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class BasketStage extends Model
{
    //
    protected $connection = 'liaogou_match';
    public $timestamps = false;

    static public function saveDataWithWinData($ws){
        $s = BasketStage::where('win_id',$ws->id)->first();
        if (is_null($s)){
            $s = new BasketStage();
            $s->win_id = $ws->id;
            $lid = BasketLeague::getLeagueIdWithType($ws->lid,'win_id');
            $s->lid = $lid;
        }
        foreach ($ws->getAttributes() as $key => $value){
            if ($key != 'id' && $key != 'lid')
                $s[$key] = $value;
        }
        $s->save();
    }

    static public function getStageIdWithType($id,$from){
        $team = BasketStage::where($from,$id)->first();
        if (isset($team))
            return $team->id;
        else{
            return null;
        }
    }
}
