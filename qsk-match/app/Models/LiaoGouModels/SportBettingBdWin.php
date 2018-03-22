<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class SportBettingBdWin extends Model
{
    protected $connection = 'liaogou_lottery';
    public $timestamps = false;

    static public function saveDataWithWinData($wbd){
        $sportBetting = SportBettingBdWin::where('num', '=', $wbd->num)
            ->where('issue_num', '=', $wbd->issue_num)->first();
        if (is_null($sportBetting)) {
            $sportBetting = new SportBettingBdWin();
        }
        foreach ($wbd->getAttributes() as $key => $value){
            if ($key != 'id' && $key != 'mid')
                $sportBetting[$key] = $value;
        }
        $sportBetting->save();
    }
}
