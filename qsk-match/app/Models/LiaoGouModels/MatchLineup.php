<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class MatchLineup extends Model
{
    protected $connection = 'liaogou_match';
    //
    public $timestamps = false;
    public function changeConnection($conn)
    {
        $this->connection = $conn;
    }
    static public function saveDataWithWinData($wml, $mid = 0){
        if($mid == 0) {
            $mid = Match::getMatchIdWith($wml->id, 'win_id');
        }
        if ($mid > 0){
            $ml = MatchLineup::find($mid);
            if (!isset($ml)) {
                $ml = new MatchLineup();
                $ml->id = $mid;
                $lid = League::getLeagueIdWithType($wml->lid,'win_id');
                $hid = Team::getTeamIdWithType($wml->h_id,'win_id');
                $aid = Team::getTeamIdWithType($wml->a_id,'win_id');
                $ml->lid = $lid;
                $ml->h_id = $hid;
                $ml->a_id = $aid;
            }
            foreach ($wml->getAttributes() as $key => $value){
                if ($key != 'id' && $key != 'lid' && $key != 'h_id' && $key != 'a_id' &&
                    $key != 'h_lineup_percent' && $key != 'a_lineup_percent' && $key != 'h_goal' && $key != 'a_goal' &&
                    $key != 'h_first' && $key != 'a_first' &&
                    $key != 'h_against' && $key != 'a_against')
                    $ml[$key] = $value;
            }
            $ml->save();
        }
    }
}
