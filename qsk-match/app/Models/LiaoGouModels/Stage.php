<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    //
    protected $connection = 'liaogou_match';

    protected $hidden = ['win_id'];

    public $timestamps = false;

    static public function saveDataWithWinData($ws){
        $s = Stage::where('win_id',$ws->id)->first();
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
