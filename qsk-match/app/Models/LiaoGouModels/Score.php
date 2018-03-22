<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    protected $connection = 'liaogou_match';
    //
    public $timestamps = false;
    public function changeConnection($conn)
    {
        $this->connection = $conn;
    }
    static public function saveCupDataWithWinData($ws){
        $lid = League::getLeagueIdWithType($ws->lid,'win_id');
        $tid = Team::getTeamIdWithType($ws->tid,'win_id');
        $sid = Stage::getStageIdWithType($ws->stage,'win_id');
        if ($lid > 0 && $tid > 0) {
            $s = Score::where(['lid' => $lid, 'season' => $ws->season, 'stage' => $ws->stage, 'tid' => $tid])->first();
            if (!isset($s)) {
                $s = new Score();
                $s->lid = $lid;
                $s->season = $ws->season;
                $s->stage = $ws->stage;
                $s->tid = $tid;
                $s->win_tid = $ws->tid;
                $s->win_stage = $ws->stage;
                $s->win_lid = $ws->lid;
            }

            foreach ($ws->getAttributes() as $key => $value) {
                if ($key != 'id' && $key != 'lid' && $key != 'tid' && $key != 'stage')
                    $s[$key] = $value;
            }
            $s->stage = $sid;
            $s->win_stage = $ws->stage;
            $s->save();
        }
    }

    static public function saveLeagueDataWithWinData($ws, $lid){
        $s = Score::query()->where("win_id", $ws->id)->first();
        if (isset($s)) {
            foreach ($ws->getAttributes() as $key => $value) {
                if ($key != 'id' && $key != 'lid' && $key != 'tid' && $key != 'stage')
                    $s[$key] = $value;
            }
            $s->win_stage = $ws->stage;
            $s->save();
            return;
        }

//        $lid = League::getLeagueIdWithType($ws->lid,'win_id');
        $tid = Team::getTeamIdWithType($ws->tid,'win_id');
        $sid = Stage::getStageIdWithType($ws->stage,'win_id');
        if ($lid > 0 && $tid > 0) {
            $q = Score::where(['lid' => $lid, 'season' => $ws->season, 'lsid' => $ws->lsid, 'tid' => $tid]);
            if (is_null($ws->kind)){
                $q->whereNull('kind');
            }
            else{
                $q->where('kind',$ws->kind);
            }
            $ss = $q->get();
            if (count($ss) > 1){
                dump('delete');
                foreach ($ss as $a){
                    $a->delete();
                }
            }
            $s = $q->first();
            if (!isset($s)) {
                $s = new Score();
                $s->lid = $lid;
                $s->season = $ws->season;
                $s->lsid = $ws->lsid;
                $s->tid = $tid;
                $s->win_tid = $ws->tid;
                $s->win_lid = $ws->lid;
                $s->win_stage = $ws->stage;
            }

            foreach ($ws->getAttributes() as $key => $value) {
                if ($key != 'id' && $key != 'lid' && $key != 'tid' && $key != 'stage')
                    $s[$key] = $value;
            }
            $s->win_id = $ws->id;
            $s->stage = $sid;
            $s->win_stage = $ws->stage;
            $s->save();
        }
    }
}
