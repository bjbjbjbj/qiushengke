<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class MatchPlayer extends Model
{
    protected $connection = 'liaogou_match';
    //
    public $timestamps = false;

    static public function saveDataWithWinData($wp, $lid = 0, $tid = 0){
        if ($lid == 0) {
            $lid = League::getLeagueIdWithType($wp->lid, 'win_id');
        }
        if ($tid == 0) {
            $tid = Team::getTeamIdWithType($wp->tid, 'win_id');
        }

        if ($lid > 0 && $tid > 0) {
            $player = MatchPlayer::where('lid', '=', $lid)
                ->where('tid', '=', $tid)
                ->where('num', '=', $wp->num)
                ->first();
            if (!isset($player)) {
                $player = new MatchPlayer();
                $player->lid = $lid;
                $player->tid = $tid;
                $player->name = $wp->name;
                $player->num = $wp->num;
            } else {
                $player->lid = $lid;
                $player->tid = $tid;
                $player->num = $wp->num;
                $player->name  = $wp->name;
            }
            $player->save();
        }
    }
}
