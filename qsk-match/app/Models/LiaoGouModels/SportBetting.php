<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class SportBetting extends Model
{
    protected $connection = 'liaogou_lottery';
    //
    public $timestamps = false;

    protected $hidden = ['id','hid','aid','lid','mid'];

    static public function saveDataWithWinData($wbd){
        $sportBetting = SportBetting::where('issue_num', '=', $wbd->issue_num)
            ->where('num', '=', $wbd->num)
            ->first();
        if (is_null($sportBetting)) {
            $sportBetting = new SportBetting();
        }

        foreach ($wbd->getAttributes() as $key => $value){
            if ($key != 'id' && $key != 'hid' && $key != 'aid') {
                $sportBetting[$key] = $value;
            }
        }
        //球队
        $hteam = Team::where('win_id',$wbd->hid)->first();
        if (isset($hteam)){
            $sportBetting->hid = $hteam->id;
        }
        $ateam = Team::where('win_id',$wbd->aid)->first();
        if (isset($ateam)){
            $sportBetting->aid = $ateam->id;
        }

        $match = Match::saveWithLotteryData($wbd);
        if (isset($match)) {
            $sportBetting->mid = $match->id;
        }
        $sportBetting->save();
    }
}
