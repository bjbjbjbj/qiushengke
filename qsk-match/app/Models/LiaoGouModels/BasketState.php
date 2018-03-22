<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class BasketState extends Model
{
    protected $connection = 'liaogou_match';
    //
    public $timestamps = false;
    static public function saveDataWithWinData($ws,$id){
        $s = BasketState::where('win_id',$id)->first();
        if (!isset($s)) {
            $s = new BasketState();
            $s->id = $id;
            $s->zone = $ws->zone;
            $s->name = $ws->name;
            $s->win_id = $id;
            $s->save();
        }
    }
}
