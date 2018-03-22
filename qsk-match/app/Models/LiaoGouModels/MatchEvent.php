<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class MatchEvent extends Model
{
    protected $connection = 'liaogou_match';
    //
    public $timestamps = false;
    public function changeConnection($conn)
    {
        $this->connection = $conn;
    }
    static public function saveDataWithWinData($wme, $mid = 0){
        if ($mid == 0) {
            $mid = Match::getMatchIdWith($wme->mid, 'win_id');
        }

        $me = MatchEvent::where('mid', '=', $mid)
            ->where('kind', '=', $wme->Kind)
            ->where('happen_time', '=', $wme->happenTime)
            ->where('is_home', '=', $wme->isHome)
            ->first();
        if (!isset($me)) {
            $me = new MatchEvent();
            $me->mid = $mid;
        }
        foreach ($wme->getAttributes() as $key => $value){
            if ($key != 'mid' && $key != 'id')
                $me[$key] = $value;
        }
        $me->save();
    }
}
