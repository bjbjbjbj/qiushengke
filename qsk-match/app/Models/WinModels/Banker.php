<?php

namespace App\Models\WinModels;

use Illuminate\Database\Eloquent\Model;

class Banker extends Model
{
    //
    public $incrementing = false;
    protected $connection = 'win_matches';
    public $timestamps = false;

    static public function getBankerIdWithType($id,$from){
        $banker = Banker::where($from,$id)->first();
        if (isset($banker))
            return $banker->id;
        else
            return 0;
    }
}
