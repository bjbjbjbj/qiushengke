<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class Banker extends Model
{
    //
    protected $connection = 'liaogou_match';
    public $timestamps = false;

    static public function getBankerIdWithType($id,$from){
        $banker = Banker::where($from,$id)->first();
        if (isset($banker))
            return $banker->id;
        else
            return 0;
    }

    static public function saveDataWithWinData($wb,$id){
        $bank = Banker::where('win_id',$id)->first();
        if (!isset($bank)) {
            $bank = new Banker();
            $bank->name = $wb->name;
            $bank->win_id = $id;
            $bank->save();
        }
    }
}
