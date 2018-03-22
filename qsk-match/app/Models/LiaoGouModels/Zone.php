<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $connection = 'liaogou_match';
    //
    public $timestamps = false;

    static public function saveDataWithWinData($wz,$id){
        $z = Zone::where('win_id',$id)->first();
        if (!isset($z)) {
            $z = new Zone();
            $z->id = $id;
            $z->name = $wz->name;
            $z->save();
        }
    }
}
