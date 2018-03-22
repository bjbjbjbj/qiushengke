<?php

namespace App\Models\LiaoGouModels;

use App\Http\Controllers\Utils\DateUtils;
use Illuminate\Database\Eloquent\Model;

class SportBettingBasket extends Model
{
    protected $connection = 'liaogou_lottery';
    //
    public $timestamps = false;

    static public function saveDataWithWinData($wbd){
        $sportBetting = SportBettingBasket::where('issue_num', '=', $wbd->issue_num)
            ->where('num', '=', $wbd->num)
            ->first();
        if (is_null($sportBetting)) {
            $sportBetting = new SportBettingBasket();
        }

        foreach ($wbd->getAttributes() as $key => $value){
            if ($key != 'id')
                $sportBetting[$key] = $value;
        }

        $match = BasketMatch::saveWithLotteryData($wbd);
        if (isset($match)) {
            $sportBetting->mid = $match->id;
//            $sportBetting->time = $match->time;
        }

        $sportBetting->save();
    }
}
