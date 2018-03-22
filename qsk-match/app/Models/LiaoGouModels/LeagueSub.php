<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class LeagueSub extends Model
{
    protected $connection = 'liaogou_match';
    //
    public $timestamps = false;

    static public function saveDataWithWinData($wls){
        $lid = League::getLeagueIdWithType($wls->lid,'win_id');
        if ($lid > 0) {
            $q = LeagueSub::where(['lid' => $lid, 'subid' => $wls->subid, 'season' => $wls->season]);
            $lss = $q->get();
            if (count($lss) > 1){
                foreach ($lss as $a){
                    $a->delete();
                }
            }
            $ls = $q->first();
            if (!isset($ls)) {
                $ls = new LeagueSub();
                $ls->lid = $lid;
                $ls->subid = $wls->subid;
                $ls->season = $wls->season;
            }
            foreach ($wls->getAttributes() as $key => $value) {
                if ($key != 'id' && $key != 'lid')
                    $ls[$key] = $value;
            }
            $ls->save();
        }
    }
}
