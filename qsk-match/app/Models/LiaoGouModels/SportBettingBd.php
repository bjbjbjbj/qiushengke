<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class SportBettingBd extends Model
{
    protected $connection = 'liaogou_lottery';
    public $timestamps = false;

    static public function saveDataWithWinData($wbd,$has_result){
        $sportBetting = SportBettingBd::where('num', '=', $wbd->num)
            ->where('issue_num', '=', $wbd->issue_num)->first();
        dump($sportBetting);
        if (is_null($sportBetting)) {
            $sportBetting = new SportBettingBd();
        }
        foreach ($wbd->getAttributes() as $key => $value){
            if ($key != 'id' && $key != 'mid')
                $sportBetting[$key] = $value;
        }

        $match = Match::getMatchWith($wbd->mid,'win_id');
        if (isset($match)) {
            $sportBetting->mid = $match->id;
            $match->genre = $match->genre | 1 << 4;
            $match->save();
        }
        $sportBetting->has_result = $has_result;
        $sportBetting->save();
    }
}
