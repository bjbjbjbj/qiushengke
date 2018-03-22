<?php

namespace App\Http\Controllers\LiaogouAnalyse;

use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\Odd;
use Illuminate\Routing\Controller;
use App\Http\Controllers\WinSpider\SpiderTeamKingOdds;
use App\Models\LiaoGouModels\League;
use App\Models\LiaoGouModels\Season;
use App\Models\LiaoGouModels\Score;
use App\Models\LiaoGouModels\TeamOddResult;
use App\Models\LiaoGouModels\TeamOddResultLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class TeamOddResultController extends Controller
{
    use SpiderTeamKingOdds;

    public function index($action, Request $request)
    {
        if (method_exists($this, $action)) {
            return $this->$action($request);
        } else {
            echo "Error: Not Found action 'TeamOddResultController->$action()'";
        }
    }

    /**
     * 只填充主流赛事球队盘口结果
     */
    public function fillTeamOddResultByMainLeague(Request $request, $isMain = true)
    {
        $cid = $request->input('cid');
        $query = TeamOddResultLog::query()
            ->where(function ($q) {
                $q->whereNull('fill_status')
                    ->orwhere('fill_status', 0);
            });
        if (isset($cid)) {
            $query->where('cid', $cid);
        }
        $logs = $query->orderBy('id')->get()->unique('lid');
        if (count($logs) > 0) {
            echo 'fillTeamOddResultByMainLeague: remain count= '.count($logs).'<br>';
            $season = Season::query()
                ->where('lid', $logs[0]->lid)
                ->where(function ($q) {//只取今年和去年有比赛的赛事
                    $q->where('year', date('Y'))
                        ->orwhere('year', date('Y') - 1);
                })
                ->orderBy('year', 'desc')
                ->first();
            $query = TeamOddResultLog::query()
                ->where('lid', $logs[0]->lid)
                ->where(function ($q) {
                    $q->whereNull('fill_status')
                        ->orwhere('fill_status', 0);
                });
            if (isset($cid)) {
                $query->where('cid', $cid);
            }
            $tempLogs = $query->orderBy('cid', 'asc')->get();
            if (isset($season)) {
                $this->saveTeamOddResultByLeague($tempLogs, $logs[0]->lid, $season, true);
            } else {
                echo 'the last season of league' . $logs[0]->lid . '  is empty!' . '<br>';
                foreach ($tempLogs as $log) {
                    $this->saveLeagueOddResultLog($log, true, false);
                }
                $this->refreshCurrentPage();
            }
        } else {
            $this->initTeamOddLogByLeague($request, $isMain);
        }
    }

    /**
     * 重置log表
     */
    public function resetTeamOddResultLog(Request $request)
    {
        $cid = $request->input('cid');
        if (isset($cid)) {
            $cids = explode(',', $cid);
            //强制初始化 log表
            $resultLogs = TeamOddResultLog::query()->whereIn('cid', $cids)->get();
        } else {
            //强制初始化 log表
            $resultLogs = TeamOddResultLog::all();
        }
        if (isset($resultLogs)) {
            foreach ($resultLogs as $log) {
                $log->fill_status = 0;
                $log->save();
            }
        }
        echo 'complete!';
    }

    /**
     * 根据赛事填充球队盘口结果
     */
    public function fillTeamOddResultByAllLeague(Request $request)
    {
        $this->fillTeamOddResultByMainLeague($request, false);
    }

    /**
     * 根据赛事id填充球队盘口结果
     */
    public function fillTeamOddResultByLeague(Request $request)
    {
        $lid = $request->input('lid');
        if (!isset($lid)) {
            $lname = $request->input('lname');
            if (isset($lname)) {
                $league = League::query()->where('name', '=', $lname)->first();
                $lid = $league->id;
            }
        }
        if (!isset($lid)) {
            echo 'error: league name and league id is null! <br>';
            return;
        }
        if (!isset($league)) {
            $league = League::query()->find($lid);
        }
        if (!isset($league) || $league->type != 1 || $league->odd != 1){
            echo 'error: league type is not 1 or league odd is not 1! <br>';
            return;
        }
        $season = Season::query()
            ->where('lid', $lid)
            ->where(function ($q) {//只取今年和去年有比赛的赛事
                $q->where('year', date('Y'))
                    ->orwhere('year', date('Y') - 1);
            })
            ->orderBy('year', 'desc')
            ->first();

        if (isset($season)) {
            foreach ($this->bankerIds as $cid) {
                $this->initLog($lid, $cid);
            }
            $logs = TeamOddResultLog::query()->where('lid', $lid)->orderBy('cid', 'asc')->get();
            $this->saveTeamOddResultByLeague($logs, $lid, $season, false);
        } else {
            echo 'the last season of league' . $lid . '  is empty!' . '<br>';
        }
    }

    /**
     * 准确的填充某一球队的盘口信息
     */
    public function fillTeamOddResultByTeam(Request $request)
    {
        $lid = $request->input('lid');
        $league = League::query()->find($lid);
        if (!isset($league)) {
            $league = League::query()->find($lid);
        }
        if (!isset($league) || $league->type != 1 || $league->odd != 1){
            echo 'error: league type is not 1 or league odd is not 1! <br>';
            return;
        }
        $tid = $request->input('tid');
        $seasonName = $request->input('season');
        $reset = $request->input('reset');
        if(!isset($seasonName)) {
            $seasonName = $this->getLastSeason($lid)->name;
        }

        //查看积分表是否有赛事和球队对应的信息
        $score = Score::query()->where(['lid'=> $lid, 'tid' => $tid, 'season'=>$seasonName])->first();
        if (!isset($score)) {
            echo 'fillTeamOddResultByTeam: lid:' . $lid . ',tid:' . $tid . 'could not found!<br>';
        }
        foreach ($this->bankerIds as $cid) {
            $teamOddResult = TeamOddResult::query()
                ->where('cid', $cid)
                ->where('tid', $tid)
                ->where('lid', $lid)
                ->where('season', $seasonName)
                ->first();
            if (!isset($teamOddResult)) {
                $teamOddResult = new TeamOddResult();
                $teamOddResult->cid = $cid;
                $teamOddResult->tid = $tid;
                $teamOddResult->lid = $lid;
                $teamOddResult->season = $seasonName;
            }
            echo 'fillTeamOddResultByTeam: cid:'.$cid.',lid:' . $lid . ',tid:' . $tid . '<br>';
            $this->saveTeamOddResultByTeam($teamOddResult, $reset);
        }
    }

    /**
     * 重新填充 未填充完成的 盘王
     */
    public function fillUndoneOddResult()
    {
        $temp = TeamOddResult::query()
            ->where('odd_status', 0)
            ->where('fill_status', '<', 3)
            ->orderBy('lid')->first(); //取未填充完成的赛事
        if (isset($temp)) {
            $this->saveTeamOddResultByTeam($temp, 1);
//            if ($temp->count == 0 || $temp->total == 0) {
//                $temp->fill_status = 3;
//                $temp->save();
//            }
            //再次执行
            $this->refreshCurrentPage();
        } else {
            echo 'fillUndoneOddResult complete! <br>';
        }
    }

    /**
     * 刪除无效数据
     */
    public function delErrorResults()
    {
        TeamOddResultLog::query()
            ->join('leagues', 'team_odd_result_logs.lid', '=', 'leagues.id')
            ->where('leagues.type', '!=', '1')
            ->orwhere('leagues.odd', '=', '0')
            ->delete();
        echo 'delete log complete! <br>';
        TeamOddResult::query()
            ->join('leagues', 'team_odd_results.lid', '=', 'leagues.id')
            ->where('leagues.type', '!=', '1')
            ->orwhere('leagues.odd', '=', '0')
            ->delete();

        echo 'delete teamOddResult complete! <br>';
    }

    /**
     * 重置大于现在时间的盘王时间
     */
    public function resetLastMatchTime()
    {
        $date = date_format(date_create(), 'y-m-d');
        $results = TeamOddResult::query()
            ->where('last_match_time', '>=', $date)
            ->orderBy('last_match_time', 'desc')
            ->get();

        foreach ($results as $result) {
            $result->last_match_time = $date;
            $result->save();
        }
        echo 'reset last match time complete! <br>';
    }

    /**
     * 定时计算标识了需要计算的项
     */
    public function calculateTeamOddResultByTag(Request $request){
        echo date('Y-m-d H:i:s', time()).'<br>';
        $this->setTeamOddResultNeedCalculateByMatchDate($request, true);

        $results = TeamOddResult::query()
            ->where("need_calculate", 1)
            ->orderByRaw("field(id,".Odd::default_top_rank_bankerStr.")")
            ->orderBy('last_match_time')
            ->take(120)->get();

        foreach ($results as $result) {
            $this->saveTeamOddResultByTeam($result, 1);
            $result->need_calculate = 0;
            $result->save();
        }
        echo date('Y-m-d H:i:s', time()).'<br>';
        if ($request->input("auto") == 1 && count($results) > 0) {
            $this->refreshCurrentPage();
        } else {
            echo "calculateTeamOddResultByTag complete<br>";
        }
    }

    /**
     * 为两周前~3天前的last_match_time 盘王设置需要计算的标识
     */
    public function autoSetTeamOddResultNeedCalculate(Request $request) {
        $startDate = $request->input("start", date_create("- 2 weeks"));
        $endDate = $request->input("end", date_create("- 3 days"));
        $results = TeamOddResult::query()
            ->whereBetween("last_match_time", [$startDate, $endDate])
            ->where("need_calculate", 0)
            ->take(500)->get();

        foreach ($results as $result) {
            $result->need_calculate = 1;
            $result->save();
        }
        if ($request->input("auto") == 1 && count($results) > 0) {
            $this->refreshCurrentPage();
        } else {
            echo "autoSetTeamOddResultNeedCalculate complete";
        }
    }

    /**
     * 根据比赛时间段，设置盘完需要计算的标识
     */
    public function setTeamOddResultNeedCalculateByMatchDate(Request $request, $isNotNeedRedis = false) {

        $mids = array();
        $redisMids = array();
        $key = "team_odd_result_mids";

        $startDate = $request->input("start", date_format(date_create("-4 hours"), "Y-m-d H:i"));
        $endDate = $request->input("end", date_format(date_create("-2.5 hours"), "Y-m-d H:i"));

        if ($isNotNeedRedis) {
            $mids = $this->getNeedToCalculateMids($mids, $startDate, $endDate);
            dump('mids count = ' . count($mids));
        } else {
            $value = Redis::get($key);
            if (isset($value)) {
                $redisMids = array_merge($redisMids, json_decode($value));
            }
            $default_count = 30;
//        dump(date_create());
            if (count($redisMids) <= 0) {
                $mids = $this->getNeedToCalculateMids($mids, $startDate, $endDate);

                Redis::setEx($key, 24 * 60 * 60, json_encode($mids));
                $redisMids = array_merge($redisMids, $mids);
            }
            $mids = array_slice($redisMids, 0, $default_count);

            dump('mids count = ' . count($redisMids));
        }

        $matches = Match::query()->whereIn("id", $mids)->get();

        foreach ($matches as $match) {
            $this->setTeamOddResultNeedCalculateByMatch($match, true);
        }
//        dump(date_create());
        if ($isNotNeedRedis) {
            echo "setTeamOddResultNeedCalculateByMatchDate complete<br>";
        } else {
            //删除redis中将要爬取的lid
            $redisMids = array_slice($redisMids, count($mids));
            Redis::set($key, json_encode($redisMids));

            if ($request->input("auto") == 1 && count($redisMids) > 0) {
                $this->refreshCurrentPage();
            } else {
                echo "setTeamOddResultNeedCalculateByMatchDate complete<br>";
            }
        }
    }

    private function getNeedToCalculateMids($mids, $startDate, $endDate) {

        $query = Match::query()
            ->join("leagues", "leagues.id", "matches.lid")
            ->where("leagues.type", 1)
            ->where("leagues.odd", 1)
            ->whereBetween("matches.time", [$startDate, $endDate])
            ->where("status", -1)
            ->orderBy("matches.time", "desc");
        $tempQuery = clone $query;
        $matchQuery = clone $query;
        $hids = $query->selectRaw("matches.hid as tid")->get()->unique("tid")->toArray();
        $aids = $tempQuery->selectRaw("matches.aid as tid")->get()->unique("tid")->toArray();
        $tids = collect($hids)->merge($aids)->unique("tid")->flatten()->all();

        $matches = $matchQuery->select("matches.*")
            ->where(function ($q) use ($tids){
                foreach ($tids as $tid) {
                    $q->orWhereRaw("matches.id in(select t.id from (select m.id from matches as m join leagues as l on l.id = m.lid where (m.hid = $tid or m.aid = $tid) and m.status = -1 and l.type = 1 and l.odd = 1 order by m.time desc limit 1) as t)");
                }
            })->get();

        foreach ($matches as $match) {
            array_push($mids, $match->id);
        }

        return $mids;
    }
}
