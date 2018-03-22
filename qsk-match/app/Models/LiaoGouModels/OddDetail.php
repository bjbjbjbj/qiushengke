<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class OddDetail extends Model
{
    //
    public $timestamps = false;

    static public function saveDataWithWinData($wo){
        $mid = Match::getMatchIdWith($wo->mid,'win_id');
        if ($mid > 0) {
            $cid = Banker::getBankerIdWithType($wo->cid, 'win_id');
            $o = OddDetail::where(["mid" => $mid, "cid" => $cid, "type" => $wo->type])->first();
            if (!isset($o)) {
                $o = new OddDetail();
                $o->mid = $mid;
                $o->cid = $cid;
                $o->type = $wo->type;
                $o->detail = $wo->detail;
                $o->win_id = $wo->id;
            }
            if (!$o->save()) {
                echo 'match ' . $mid . ' save error' . '</br>';
            }
        }
        else{
            dump('win_mid '. $wo->mid .' not found');
        }
    }
}
