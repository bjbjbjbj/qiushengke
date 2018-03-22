<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class MatchData extends Model
{
    protected $connection = 'liaogou_match';
    //
    public $timestamps = false;
    public function changeConnection($conn)
    {
        $this->connection = $conn;
    }
    static public function saveDataWithWinData($omd,$mid = 0,$win_mid = 0){
        if ($mid == 0){
            if ($win_mid == 0) {
                $win_mid = $omd->id;
            }
            $match = Match::getMatchWith($win_mid,'win_id');
            $mid = $match->id;
        } else {
            $match = Match::query()->find($mid);
        }
        $md = MatchData::find($mid);
        if (!isset($md)) {
            $md = new MatchData();
        }
        foreach ($omd->getAttributes() as $key => $value){
            if ($key != 'id') {
                if ($key == 'referee_id') {
                    if ($value == -1 && ($match->status != 0)) {
                        $md[$key] = -1;
                    } if ($value > 0) {
                        $value = Referee::getRefIdWith($value, 'win_id');
                        $md[$key] = $value;
                    }
                } else {
                    $md[$key] = $value;
                }
            }
        }
        $md->id = $mid;
        $md->save();
    }
}
