<?php

namespace App\Models\AnalyseModels;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $connection = 'analyse_match';
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
