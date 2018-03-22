<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $connection = 'liaogou_match';
    //
    public $timestamps = false;
    static public function saveDataWithWinData($ws,$id){
        $s = State::where('win_id',$id)->first();
        if (!isset($s)) {
            $s = new State();
            $s->id = $id;
            $s->zone = $ws->zone;
            $s->name = $ws->name;
            $s->save();
        }
    }
}
