<?php

namespace App\Models\AnalyseModels;

use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    //
    protected $connection = 'analyse_match';
    public $timestamps = false;

    static public function getStageIdWithType($id,$from){
        $team = Stage::where($from,$id)->first();
        if (isset($team))
            return $team->id;
        else{
            return null;
        }
    }
}
