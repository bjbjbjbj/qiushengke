<?php

namespace App\Models\AnalyseModels;

use Illuminate\Database\Eloquent\Model;

class Banker extends Model
{
    //
    protected $connection = 'analyse_match';
    public $timestamps = false;

    static public function getBankerIdWithType($id,$from){
        $banker = Banker::where($from,$id)->first();
        if (isset($banker))
            return $banker->id;
        else
            return 0;
    }
}
