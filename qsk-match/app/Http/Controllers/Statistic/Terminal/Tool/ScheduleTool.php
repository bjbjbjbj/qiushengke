<?php
/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/3/1
 * Time: 12:46
 */

namespace App\Http\Controllers\Statistic\Terminal\Tool;


use App\Models\LiaoGouModels\MatchLive;

class ScheduleTool
{
    /**
     * 比赛双方未来赛程
     * @param $match 当前比赛
     * @return array 分home away主客两个
     */
    public static function matchSchedule($match, $sport = MatchLive::kSportFootball){
        $result = array();
        $result['home'] = self::getMatchScheduleWithTid($match,$match->hid,$match->hname, $sport);
        $result['away'] = self::getMatchScheduleWithTid($match,$match->aid,$match->aname, $sport);
        return $result;
    }

    /**
     * 根据当前比赛 获取tid对应的未来赛程
     * @param $match
     * @param $tid
     * @param $name
     * @return mixed
     */
    private static function getMatchScheduleWithTid($match, $tid, $name, $sport = MatchLive::kSportFootball){
        $query = MatchQueryTool::getMatchQueryBySport($sport);
        $query = MatchQueryTool::onMatchCommonSelect($query);
        $query = MatchQueryTool::onMatchLeagueLeftJoin($query, $sport);

        $matches = $query->where(function ($q) use($match,$tid,$name){
            $q->where(function ($j) use($match,$tid){
                $j->where('m.hid',$tid)
                    ->orwhere('m.aid',$tid);
            })
                ->orwhere(function ($j) use($match,$name){
                    $j->where('m.hname',$name)
                        ->orwhere('m.aname',$name);
                });
        })
            ->where('m.time','>',$match->time)
            ->where('m.id','!=',$match->id)
            ->where('m.status',0)
            ->orderby('m.time','asc')
            ->take(3)
            ->get();
        foreach ($matches as $match){
            $dateCurrent = date_create();
            date_time_set($dateCurrent, 0, 0);
            $dateMatch = date_create($match->time);
            date_time_set($dateMatch, 0, 0);
            $match['day'] = $dateCurrent->diff($dateMatch)->days.'天';
        }
        return $matches;
    }
}