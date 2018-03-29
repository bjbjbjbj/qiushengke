<?php

namespace App\Models\LiaoGouModels;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $connection = 'liaogou_match';
    //
    public $timestamps = false;

    static public function getTeamIdWithType($id,$from){
        $team = Team::where($from,$id)->first();
        if (isset($team))
            return $team->id;
        else{
            return 0;
        }
    }

    static public function saveWithWinData($wt,$wtid,$wname,$noAlisa = false){
        $t = Team::where('win_id',$wtid)->first();
        if (!isset($t)) {
            $t = new Team();

        }
        $tmp = null;
        if (is_null($wt)){
            $wt = new  \App\Models\WinModels\Team();
            $wt->name = $wname;
            if ($noAlisa){
                //下面保存
            }
            else{
                $tmp = new LiaogouAlias();
                $tmp->type = 1;
                $tmp->from = 1;
                $tmp->target_name = $wt->name;
                $tmp->lg_name = $wt->name;
                $tmp->win_id = $wtid;
            }
        }
        foreach ($wt->getAttributes() as $key => $value){
            if ($key != 'id')
                $t[$key] = $value;
        }
        $t->win_id = $wtid;
        if (!$t->save()){
            echo 'team save error';
        }
        else{
            if ($noAlisa) {
                //看看别名表有没有
                $tmp = LiaogouAlias::where('win_id', $wtid)
                    ->where('from', 1)
                    ->where('type', 1)
                    ->first();
                if (is_null($tmp)) {
                    $tmp = new LiaogouAlias();
                    $tmp->type = 1;
                    $tmp->from = 1;
                    $tmp->target_name = $wt->name;
                    $tmp->lg_name = $wt->name;
                    $tmp->win_id = $wtid;
                    $tmp->lg_id = $t->id;
                    $tmp->save();
                }
            }
            else{
                if (isset($tmp)) {
                    $tmp->lg_id = $t->id;
                    $tmp->save();
                }
            }
        }
        return $t;
    }

    //============接口相关=================================
    /**
     * 获取球队联赛最后一场比赛
     * @param $id
     * @time 比赛时间
     * @return mixed
     */
    public static function getLeagueMatch($id,$time = null){
        $resultMatch = null;
        $resultLeague = null;
        $query = Match::query();
        $query->where(function ($q) use($id){
            $q->where('hid',$id)
                ->orwhere('aid',$id);
        });

        if(is_null($time)) {
            $time = date("Y-m-d H:i:s");
        }

        $query->where('time','<=',$time);
        $query->groupBy('lid');
        $query->selectRaw('max(id) as id, lid ,max(time) as time');
        $query->orderBy('time','desc');
        $matches = $query->take(5)->get();
        foreach ($matches as $match){
            $league = $match->leagueData;
            if(isset($league) && $league->type == 1){
                $resultMatch = Match::find($match->id);
                $resultLeague = $league;
                break;
            }
        }
        return array('match'=>$resultMatch,'league'=>$resultLeague);
    }

    //============接口相关===================
    public static function getIcon($icon) {
        return (isset($icon) && strlen($icon) > 0) ? $icon : '';
    }

    public static function getIconById($tid) {
        $team = self::query()->find($tid);
        $icon = isset($team) ? $team->icon : "";
        return (isset($icon) && strlen($icon) > 0) ? $icon : '';
    }
}
