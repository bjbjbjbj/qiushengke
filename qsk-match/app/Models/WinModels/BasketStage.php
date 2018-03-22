<?php

namespace App\Models\WinModels;

use Illuminate\Database\Eloquent\Model;

class BasketStage extends Model
{
    //
    protected $connection = 'win_matches';
    public $timestamps = false;

    static public function saveDataWithWinData($ws){
        $s = BasketStage::where('win_id',$ws->id)->first();
        if (is_null($s)){
            $s = new Stage();
            $s->win_id = $ws->id;
            $lid = League::getLeagueIdWithType($ws->lid,'win_id');
            $s->lid = $lid;
        }
        foreach ($ws->getAttributes() as $key => $value){
            if ($key != 'id' && $key != 'lid')
                $s[$key] = $value;
        }
        $s->save();
    }

    static public function getStageIdWithType($id,$from){
        $team = Stage::where($from,$id)->first();
        if (isset($team))
            return $team->id;
        else{
            return null;
        }
    }
}
