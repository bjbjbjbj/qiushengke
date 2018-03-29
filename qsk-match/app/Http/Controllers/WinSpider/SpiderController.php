<?php

namespace App\Http\Controllers\WinSpider;

use App\Http\Controllers\FileTool;
use App\Http\Controllers\LiaogouAnalyse\SpiderInflexion;
use App\Http\Controllers\LiaogouLottery\SpiderBettingNews;
use App\Http\Controllers\Statistic\Change\MatchDataChangeTool;
use App\Http\Controllers\Statistic\StatisticFileTool;
use App\Models\LiaoGouModels\LotteryDetail;
use App\Models\LiaoGouModels\LotteryTip;
use App\Models\LiaoGouModels\MatchesAfter;
use App\Models\LiaoGouModels\MatchLive;
use App\Models\LiaoGouModels\Odd;
use App\Models\LiaoGouModels\SportBetting;
use App\Models\WinModels\MatchEvent;
use App\Models\WinModels\League;
use App\Models\WinModels\LeagueSub;
use App\Models\WinModels\Match;
use App\Models\WinModels\MatchData;
use App\Models\WinModels\MatchLineup;
use App\Models\WinModels\OddDetail;
use App\Models\WinModels\Season;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class SpiderController extends Controller
{
    use SpiderTools, SpiderOnce, SpiderMatchTeam, SpiderSchedule, SpiderLeague, SpiderOdds, SpiderLottery, SpiderInflexion,
        SpiderSportBetting, SpiderBdLottery, SpiderBasketSportBetting, SpiderReferee, SpiderBettingNews;

    const SPIDER_ERROR_LIMIT = 4;

    public function index($action, Request $request)
    {
        if (method_exists($this, $action)) {
            $this->$action($request);
        } else {
            echo "Error: Not Found action 'SpiderController->$action()'";
        }
    }

    /**
     * 比赛赔率详情
     */
    private function spiderOddDetail(Request $request)
    {
        //3小时之内的比赛
        $matches = Match::where('status', '=', 0)
            ->where("time", ">", date_create('+15 min'))
            ->where("time", "<", date_create('+3 hour'))
            ->where('lid', '>', 0)
            ->where('genre', '>', Match::k_genre_all)
            ->whereNotNull('hid')
            ->whereNotNull('aid')
            ->orderby('lid', 'asc')
            ->take(50)
            ->get();
        echo '一共' . count($matches) . '场需要爬';
        foreach ($matches as $match) {
            //bet365
            $this->handicapChangeDetail($match->id, \App\Models\WinModels\Odd::default_calculate_cid, 1, $match->time);
            $this->handicapChangeDetail($match->id, \App\Models\WinModels\Odd::default_calculate_cid, 2, $match->time);
//            $this->handicapChangeDetail($match->id,\App\Models\WinModels\Odd::default_calculate_cid,3,$match->time);
            //sb
            $this->handicapChangeDetail($match->id, \App\Models\WinModels\Odd::default_banker_id, 1, $match->time);
            $this->handicapChangeDetail($match->id, \App\Models\WinModels\Odd::default_banker_id, 2, $match->time);
//            $this->handicapChangeDetail($match->id,\App\Models\WinModels\Odd::default_banker_id,3,$match->time);
        }
    }

    /**
     * 删除比赛赔率详情
     */
    private function deleteOddDetail(Request $request)
    {
        //删除24小时前数据
        $odds = OddDetail::where("time", "<", date_create('-1 day'))
            ->orderby('time', 'asc')
            ->get();
        foreach ($odds as $odd) {
            $odd->delete();
            $lg = \App\Models\LiaoGouModels\OddDetail::where('win_id', $odd->id)
                ->first();
            if (isset($lg)) {
                $lg->delete();
            }
        }
    }

    /**
     * 刷新当天的赛事赛程
     * 每小时执行一次
     */
    private function spiderCurrentLeagueSchedule()
    {
        $lids = $this->getRecentLids('league_schedule', -1, 25, 24);
        foreach ($lids as $lid) {
            $league = League::find($lid);
//            dump($league);
            if ($league) {
                $se = Season::where(["lid" => $lid])->orderBy('year', 'desc')->get()->first();
                if (isset($se)) {
                    $season = $se->name;
                } else {
                    echo "invalid season:league=" . $lid;
                    continue;
                }
                if ($league->type == 1) {
                    if ($league->sub == 1) {
                        $lss = LeagueSub::where(['lid' => $lid, 'season' => $season])->get();
                        if (count($lss) > 0) {
                            foreach ($lss as $ls) {
                                $this->leagueSchedule($lid, $season, NULL, $ls->subid);
                            }
                        } else {
                            $this->leagueSchedule($lid, $season);
                        }
                    } else {
                        $this->leagueSchedule($lid, $season);
                    }
                } elseif ($league->type == 2) {
                    $this->cupSchedule($lid, $season);
                }
            }
        }
    }

    private function spiderCurrentLeagueRankById(Request $request)
    {
        $type = $request->input("type");
        $lid = $request->input("lid");
        $season = $request->input("season");
        if (!isset($seasonString)){
            $season = Season::where(["lid" => $lid])->orderBy('year', 'desc')->orderBy('name', 'desc')->first()->name;
        }
        $lss = LeagueSub::where(['lid' => $lid, 'season' => $season])->get();
        if (count($lss) > 0) {
            foreach ($lss as $ls) {
                $this->leagueRanking($type, $lid, $season, $ls->subid);
            }
        } else {
            $this->leagueRanking($type, $lid, $season);
        }
    }

    /**
     * 刷新当天的赛事积分
     * 每小时执行一次
     */
    private function spiderCurrentLeagueRank(Request $request)
    {
        $type = $request->input("type", -1);

        $keyHead = "league_rank_" . $type;
        $lids = $this->getRecentLids($keyHead, 1, 30, 24, true);
        foreach ($lids as $lid) {
            $league = League::find($lid);
            if ($league) {
                if ($league->type == 1) {
                    $lid = $league->id;
                    $se = Season::where(["lid" => $lid])->orderBy('year', 'desc')->get()->first();
                    if (isset($se)) {
                        $season = $se->name;
                    } else {
                        echo "invalid season:league=" . $lid;
                        continue;
                    }
                    if ($league->sub == 1) {
                        $lss = LeagueSub::where(['lid' => $lid, 'season' => $season])->get();
                        if (count($lss) > 0) {
                            foreach ($lss as $ls) {
                                if ($type >= 0) {
                                    $this->leagueRanking($type, $lid, $season, $ls->subid);
                                } else {
                                    $this->leagueRanking(0, $lid, $season, $ls->subid);
                                    $this->leagueRanking(1, $lid, $season, $ls->subid);
                                    $this->leagueRanking(2, $lid, $season, $ls->subid);
                                }
                            }
                        } else {
                            if ($type >= 0) {
                                $this->leagueRanking($type, $lid, $season);
                            } else {
                                $this->leagueRanking(0, $lid, $season);
                                $this->leagueRanking(1, $lid, $season);
                                $this->leagueRanking(2, $lid, $season);
                            }
                        }
                    } else {
                        if ($type >= 0) {
                            $this->leagueRanking($type, $lid, $season);
                        } else {
                            $this->leagueRanking(0, $lid, $season);
                            $this->leagueRanking(1, $lid, $season);
                            $this->leagueRanking(2, $lid, $season);
                        }
                    }
                } elseif ($league->type == 2) {
                }
            }
        }
    }

    private function getRecentLids($keyHead, $leagueType = -1, $count = 10, $timeHours = 36, $isSub = false)
    {
        $date = date_create();
        $key = $keyHead . "_" . date_format($date, "Y-m-d");
        $value = Redis::get($key);
        $redisLis = array();
        if (isset($value)) {
            $redisLis = array_merge($redisLis, json_decode($value));
        }
        $lids = array();
        if (count($redisLis) <= 0) {
            $query = Match::query()->select("matches.*");
            if ($leagueType > 0) {
                $query->join("leagues", function ($join) use($leagueType) {
                    $join->on("leagues.id", "=", "matches.lid")
                        ->where("leagues.type", $leagueType);
                });
            }
            $startDate = date_format($isSub ? date_sub(date_create(), date_interval_create_from_date_string((2 + $timeHours) . ' hour')) : date_add($date, date_interval_create_from_date_string('2 hour')), 'Y-m-d H:i:s');
            $endDate = date_format($isSub ? date_sub(date_create(), date_interval_create_from_date_string('2 hour')) : date_add($date, date_interval_create_from_date_string((2 + $timeHours) . ' hour')), 'Y-m-d H:i:s');

            dump($startDate, $endDate);

            $query->where("time", ">=", $startDate)
                ->where("time", "<=", $endDate)
                ->orderby('time', $isSub ? 'desc' : "asc");
            $matches = $query->get()->unique("lid");
            foreach ($matches as $match) {
                array_push($lids, $match->lid);
            }
            Redis::setEx($key, 24 * 60 * 60, json_encode($lids));
            $redisLis = array_merge($redisLis, $lids);
        }
        $lids = array_slice($redisLis, 0, $count);

        dump('lids count = ' . count($redisLis));

        //删除redis中将要爬取的lid
        $redisLis = array_slice($redisLis, count($lids));
        Redis::set($key, json_encode($redisLis));

        return $lids;
    }

    /**
     * 测试用的 暂时保留
     */
    private function spiderMatchDetailById(Request $request)
    {
        $this->matchDetail($request->input('mid'), true, true, true);
    }

    /**
     * 当前比赛详情信息
     * 5分钟一次
     */
    private function spiderCurrentMatchDetail()
    {
        $key = "current_match_detail_mids";
        $value = Redis::get($key);
        $redisMids = array();
        if (isset($value)) {
            $redisMids = array_merge($redisMids, json_decode($value));
        }

        $mids = array();
        $default_count = 10;
//        dump(date_create());
        if (count($redisMids) <= 0) {
            $matches = Match::query()->select("matches.*")
                ->join("leagues", function ($join) {
                    $join->on("leagues.id", '=', "matches.lid")
                        ->where("leagues.hot", 1);
                })
                ->where("matches.status", ">", 0)
                ->orderBy("matches.time", "asc")->get();
            if (count($matches) <= $default_count) {
                echo "hot match count is less than 10 <br>";
                $matches = Match::query()->where("status", ">", 0)->orderBy("time", "asc")->get();
            }
            foreach ($matches as $match) {
                array_push($mids, $match->id);
            }
            Redis::setEx($key, 24 * 60 * 60, json_encode($mids));
            $redisMids = array_merge($redisMids, $mids);
        }
        $mids = array_slice($redisMids, 0, $default_count);

        dump('mids count1 = ' . count($redisMids));

        $matches = Match::query()->whereIn("id", $mids)->get();
        foreach ($matches as $match) {
            $this->matchDetail($match->id, true, true, false);
        }
//        dump(date_create());
        //删除redis中将要爬取的lid
        $redisMids = array_slice($redisMids, count($mids));
        Redis::set($key, json_encode($redisMids));

        dump('mids count2 = ' . count($redisMids));
//        Redis::del($key);
    }

    /**
     * 处理无效的比赛
     * 5分钟一次
     */
    private function processInvalidMatch()
    {
        $matches = Match::where("status", ">", -1)
            ->where("time", "<", date_create('-4 hours'))
            ->orderBy("time", "desc")
            ->take(10)
            ->get();
        foreach ($matches as $match) {
            $match1 = $this->matchDetail($match->id, false, false);
            if ($match1 && is_object($match1->time)) {
                $match1->time = date_format($match1->time, 'Y-m-d H:i:s');
            }
            if (empty($match1) || ($match1->status > -1 && strtotime($match1->time) < strtotime('-1 days'))) {
                $id = $match->id;
                echo "delete match by id : $match->id".'<br>';
                $match->status = -99;
                $match->save();
//                $match->delete();

                $match = \App\Models\LiaoGouModels\Match::getMatchWith($id, 'win_id');
                if (isset($match)) {
//                    $match->delete();
                    $match->status = -99;
                    $match->save();
                }
            }
        }
    }

    /**
     * 当前比赛阵容等数据
     * 5分钟一次
     */
    private function spiderCurrentMatchLineup()
    {
        //未开始而且有阵容数据的才爬
        $matches = Match::query()->where("time", ">", date('Y-m-d H:i:s', strtotime('-16 hour')))
            ->where("time", "<", date('Y-m-d H:i:s', strtotime('+1 hour')))
            ->where('has_lineup', '>', 0)
            ->where('status', '>=', -1)
            ->get();
        echo 'spiderCurrentMatchLineup ' . count($matches) . '</br>';
        foreach ($matches as $match) {
            $leauges = League::where('id', '=', $match->lid)->first();
            if (1 == $leauges->lineup_fill) {
                if (is_null($match->hid) || is_null($match->aid))
                    $this->matchDetail($match->id, true, true);
                else
                    $this->matchDetail($match->id, false, true);
            }
        }
    }

    /**
     * 当前正在进行的比赛的阵容等数据
     * 10分钟一次
     */
    private function spiderCurrentHasStartMatchLineup()
    {
        $date = date_create();
        //未开始而且有阵容数据的才爬
        $matches = Match::where("time", ">", date_format(date_add($date, date_interval_create_from_date_string('-1 hour')), 'Y-m-d H:i:s'))
            ->where("time", "<", date_format(date_add($date, date_interval_create_from_date_string('1 hour')), 'Y-m-d H:i:s'))
            ->where('has_lineup', '>', 0)
            ->where('status', '=', 1)
            ->get();
        echo 'spiderCurrentMatchLineup ' . count($matches) . '</br>';
        foreach ($matches as $match) {
            $leauges = League::where('id', '=', $match->lid)->first();
            $lineup = MatchLineup::where('id', $match->id)->first();
            if (1 == $leauges->lineup_fill && is_null($lineup->h_lineup) && is_null($lineup->a_lineup)) {
                if (is_null($match->hid) || is_null($match->aid))
                    $this->matchDetail($match->id, true, true);
                else
                    $this->matchDetail($match->id, false, true);
            }
        }
    }

    /**
     * 爬比赛列表是否有阵容
     */
    private function spiderLiveHasLineup()
    {
        $this->matchLiveSchedule(0, false);
    }

    /**
     * 爬裁判
     */
    private function spiderReferee()
    {
        //未开始而且有阵容数据的才爬
        $matches = Match::where("time", ">", date_create('-2 hours'))
            ->where("time", "<", date_create('+6 hours'))
            ->where('status', '=', 0)
            ->get();
        echo 'spiderReferee 有' . count($matches) . '场需要爬裁判';
        foreach ($matches as $m) {
            $md = MatchData::find($m->id);
            if (isset($md)) {
                $this->spiderRefereeData($md, $m->id);
            }
        }
    }


    /**
     * 计算裁判数据
     */
    public function spiderRefereeResult()
    {
        $startDate = date('Y-m-d H:i', strtotime('-2 hours'));
        $endDate = date('Y-m-d H:i', strtotime('+12 hours'));

        //未开始而且有阵容数据的才爬
        $matches = DB::connection('liaogou_match')->select("
        select md.referee_id from
        (select * from matches where time > '$startDate' and time < '$endDate' and status = 0) as m
        left join match_datas as md on m.id = md.id
        where md.referee_id > 0");

        echo 'spiderRefereeResult 有' . count($matches) . '场需要计算裁判<br>';
        foreach ($matches as $m) {
            $this->calculateRefereeResult($m->referee_id);
        }
    }

    /**
     * 填充旧比赛阵容等数据
     * 1小时一次
     */
    private function spiderFillMatchLineup()
    {
        //今天有比赛的赛事
        $date = date_create();
        $query = Match::join('leagues', function ($join) {
            $join->on('matches.lid', '=', 'leagues.id')
            ->where('leagues.lineup_fill', '=', 1);
        })
            ->select('matches.*', 'leagues.lineup_fill as lineup_fill');
        $query->where("time", ">", date_format($date, 'Y-m-d H:i:s'))
            ->where("time", "<", date_format(date_add($date, date_interval_create_from_date_string('12 hour')), 'Y-m-d H:i:s'));
        $currentMatches = $query
            ->orderby('time', 'asc')
            ->get()
            ->unique('lid');
        echo 'total match ' . count($currentMatches) . '</br>';
        $currentLeague = array();
        foreach ($currentMatches as $match) {
            echo 'match lid ' . $match->lid . '</br>';
            if ($match->lineup_fill == 1) {
                $currentLeague[] = $match->lid;
            }
        }
        $leauges = $currentLeague;
        if (0 < count($leauges)) {
            //已结束而且没有lineup字段的才爬
            for ($i = 0; $i < count($leauges); $i++) {
                $leauge = $leauges[$i];
                $season = Season::where('lid', '=', $leauge)
                    ->orderby('year', 'desc')
                    ->first();
                if (isset($season)) {
                    $tmpQuery = Match::where('lid', '=', $leauge)
                        ->where('season', '=', $season->name)
                        ->where('status', '=', -1)
                        ->where(function ($q) {
                            $q->whereNull('has_lineup')
                                ->orwhere('has_lineup', '<', 4);
                        })
                        ->orderby('time', 'desc');
                    if (isset($season->start)) {
                        $tmpQuery->where('time', '>=', $season->start);
                    }

                    $matches = $tmpQuery
                        ->take(10)
                        ->get();
                    echo 'match lid ' . $leauge . ' season ' . $season->name . ' total ' . count($matches) . '</br>';
                    if (count($matches) > 0) {
                        break;
                    }
                }
            }
            echo 'spider fill match line up count ' . count($matches) . '</br>';
            foreach ($matches as $match) {
                echo 'spider ' . $match->id . '</br>';
                $lineup = MatchLineup::find($match->id);
                if (is_null($lineup)) {
                    echo 'no lineup ' . $match->id . '</br>';
                    if (is_null($match->hid) || is_null($match->aid)) {
                        $this->matchDetail($match->id, true, true);
                    } else {
                        $this->matchDetail($match->id, false, true);
                    }
                } //以前没爬队员,所以要再爬一次
                else if ($match->has_lineup < 4 || is_null($match->has_lineup)) {
                    echo 'has lineup ' . $match->id . '</br>';
                    if (is_null($match->hid) || is_null($match->aid)) {
                        $this->matchDetail($match->id, true, true);
                    } else {
                        $this->matchDetail($match->id, false, true);
                    }
                }
            }
        } else {
            echo 'spider fill match no league';
        }
    }

    /**
     * 根据lid爬阵容
     * @param Request $request
     */
    public function spiderLeagueLineup(Request $request)
    {
        $leauge = League::where('id', '=', $request->input('id'))
            ->where('lineup_fill', '=', 1)
            ->first();
        if (is_null($leauge)) {
            echo 'league not find';
            return;
        }
        $season = Season::where('lid', '=', $leauge->id)
            ->orderby('year', 'desc')
            ->first();
        if (is_null($season)) {
            echo 'sesaon not find';
            return;
        }

        //已结束而且没有lineup字段的才爬
        $matches = Match::
        where('lid', '=', $leauge->id)
            ->where('season', '=', $season->name)
            ->where('status', '=', -1)
            ->where(function ($q) {
                $q->whereNull('has_lineup')
                    ->orwhere('has_lineup', '<', 4);
            })
            ->orderby('time', 'desc')
            ->get();
        echo 'spider fill match line up count ' . count($matches) . '</br>';
        foreach ($matches as $match) {
            echo 'spider ' . $match->id . '</br>';
            $lineup = MatchLineup::find($match->id);
            if (is_null($lineup)) {
                echo 'no lineup ' . $match->id . '</br>';
                if (is_null($match->hid) || is_null($match->aid))
                    $this->matchDetail($match->id, true, true);
                else
                    $this->matchDetail($match->id, false, true);
            } //以前没爬队员,所以要再爬一次
            else if ($match->has_lineup < 4 || is_null($match->has_lineup)) {
                echo 'has lineup ' . $match->id . '</br>';
                if (is_null($match->hid) || is_null($match->aid)) {
                    $this->matchDetail($match->id, true, true);
                } else {
                    $this->matchDetail($match->id, false, true);
                }
            }
        }
        echo 'fill line up success';
    }

    /**
     * 当前比赛详情信息
     * 1小时一次
     */
    private function spiderMatchDetail()
    {
        $date = date_create();
        $matches = Match::where("time", ">", date_format($date, 'Y-m-d H:i:s'))
            ->where("time", "<", date_format(date_add($date, date_interval_create_from_date_string('24 hour')), 'Y-m-d H:i:s'))
            ->where(function ($q) {
                $q->whereNull("hid")
                    ->orwhereNull("aid");
            })
            ->get();
        echo 'matche count ' . count($matches);
        foreach ($matches as $match) {
            if (is_null($match->hid) || is_null($match->aid)) {
                $this->matchDetail($match->id, true, false);
            } else {
                $this->matchDetail($match->id, false, false);
            }
        }
    }

    /**
     * 填充没有球队ID的比赛
     * 10分钟一次
     */
    private function spiderFillTeamMatch(Request $request)
    {
        $count = $request->input('count', 10);
        $matches = \App\Models\LiaoGouModels\Match::where("status", "=", -1)
            ->where(function ($q) {
                $q->whereNull("hid")
                    ->orwhereNull("aid");
            })
            ->orderBy('time', 'desc')
            ->take($count)
            ->get();
        foreach ($matches as $match) {
            echo $match->hname . ' VS ' . $match->aname . '<br>';
            $this->matchDetail($match->win_id, true, true);
        }
        if ($request->input('auto', 0) == 1) {
            echo "<script language=JavaScript>window.location.reload();</script>";
            exit;
        }
    }


    /**
     * 填充未来5天没有球队ID的比赛
     * 10分钟一次
     */
    public function spiderFillAfterTeamMatch()
    {
        $matches = Match::where("status", "=", 0)
            ->where(function ($q) {
                $q->whereNull("hid")
                    ->orwhereNull("aid");
            })
            ->where('time', '>=', date('Y-m-d H:i', strtotime('+2 hour')))
            ->where('time', '<=', date('Y-m-d H:i', strtotime('+6 day')))
            ->orderBy('time', 'asc')
            ->take(10)
            ->get();
        foreach ($matches as $match) {
            echo $match->hname . ' VS ' . $match->aname . '<br>';
            $this->matchDetail($match->id, true, false);
        }
    }

    /**
     * 填充赔率
     */
    private function spiderFillOddMatch(Request $request)
    {
        $matches = \App\Models\LiaoGouModels\Match::query()
            ->select('matches.*')
            ->join('leagues', function ($join){
                $join->on('matches.lid', '=', 'leagues.id')
                    ->where(function ($q){
                        $q->where("leagues.hot", "=", 1)
                        ->orWhere('leagues.main', '=', 1);
                    });
            })->where("matches.is_odd", "=", 0)
            ->where("matches.status", "=", -1)
            ->orderBy('matches.time', 'desc')
            ->take(50)
            ->get();
        foreach ($matches as $match) {
            echo $match->hname . ' VS ' . $match->aname . '<br>';
            $this->oddsWithMatchAndType($match->win_id, 1);
            $this->oddsWithMatchAndType($match->win_id, 2);
            $this->oddsWithMatchAndType($match->win_id, 3);
            $match->is_odd = 1;
            $match->save();
        }

        if ($request->input('auto', 0) == 1) {
            echo "<script language=JavaScript>window.location.reload();</script>";
            exit;
        }
    }

    /**
     * 单独爬比赛id对应赔率
     * @param Request $request
     */
    private function spiderOddForMatchId(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id <= 0) {
            echo '请输入id';
        } else {
            $match = \App\Models\LiaoGouModels\Match::where('win_id', $id)->first();
            if (is_null($match)) {
                echo '找不到比赛';
            } else {
                $this->oddsWithMatchAndType($id, 1);
                $this->oddsWithMatchAndType($id, 2);
                $this->oddsWithMatchAndType($id, 3);
            }
        }
    }

    /**
     * 每天的比赛
     * 每5分钟执行一次
     */
    private function spiderLiveSchedule()
    {
        $this->matchLiveSchedule(0);
        $this->matchLiveSchedule(1);
//        $this->matchLiveScheduleForBetting(2);
//        $this->matchLiveScheduleForBetting(3);
//        $this->matchLiveScheduleForBetting(4);
    }

    /**
     * 专门用来删除冗余比赛表的多余数据的接口（包括matches_afters, odds_afters, baskets_afters）
     */
    private function deleteUselessAllAfters(Request $request)
    {
        if (!$request->has("count")) {
            Input::merge(array('count' => 1000));
        }
        //matches_afters表
        $this->deleteUselessMatchAfters($request);
        //odds_afters表
        $this->deleteUselessOddAfters($request);
    }

    /**
     * 删除比赛冗余表中的多余数据
     */
    private function deleteUselessMatchAfters(Request $request)
    {
        $count = 0;
        if ($request->has("count")) {
            $count = $request->get("count", 100);
        }
        MatchesAfter::deleteUselessData($count);
//        MatchesAfter::deleteUselessData2($count);
    }

    /**
     * 完结的比赛
     * 每30分钟执行一次
     */
    private function spiderDoneSchedule()
    {
        $this->matchDateSchedule();
    }

    /**
     * 未来3个比赛日的赛程
     * 每1小时执行一次
     */
    private function spiderNextSchedule()
    {
        foreach (range(1, 3) as $i) {
            $date = date_create();
            $dateStr = date_format(date_add($date, date_interval_create_from_date_string("$i days")), 'Y-m-d');
            $this->matchDateSchedule($dateStr);
        }
    }

    /**
     * 每日盘口
     * 每3小时执行一次
     */
    private function spiderHandicap7Days()
    {
        set_time_limit(0);
        $lastTime = time();
        foreach (range(1, 7) as $i) {
            $dateStr = date_format(date_create("+ $i days"), 'Y-m-d');
            $this->handicapDays(1, $dateStr);
            $this->handicapDays(2, $dateStr);
            $this->handicapDays(3, $dateStr);
        }
        dump(time() - $lastTime);
    }

    /**
     * 每日盘口
     * 每5分钟执行一次
     */
    private function spiderHandicapDays()
    {
        set_time_limit(0);
        $lastTime = time();
        $this->handicapDays(1);
        $this->handicapDays(2);
        $this->handicapDays(3);
        dump(time() - $lastTime);
    }

    /**
     * 盘口改变
     * 每s执行一次
     */
    private function spiderHandicapChange()
    {
        $lastTime = time();
        $this->handicapChange(1);
        $this->handicapChange(2);
        $this->handicapChange(3);

        //实时比赛事件
        $this->spiderLiveTimeEvent();

        dump(time() - $lastTime);
    }

    /**
     * 比赛直播
     * 每分钟执行一次
     */
    private function spiderMatchLiveChange()
    {
//        $this->matchLiveChange();
        $this->matchPcLiveChange();

        //滚球盘
//        $this->liveHandicapLiveChange();
    }

    /**
     * 分析拐点数据
     */
    private function spiderAnalyseForOneHour(Request $request)
    {
        $this->analyseForOneHour($request);
    }

    /******************* 足彩 *****************/

    /**
     * 爬历史足彩记录,根据彩票id爬数据,例如这期多少人中等
     * 每1小时一次
     */
    private function spiderFillLottery()
    {
        $this->spiderLotteryHistory();
    }

    /**
     * 爬最新3场足彩
     * 每1天一次
     */
    private function spiderCurrentLottery()
    {
        $this->spiderNow(0);
    }

    /**
     * 爬中国竞彩赔率
     */
    private function spiderChinaLotteryOdd()
    {
        $matches = SportBetting::whereNull('status')
            ->whereNotNull('odd')
            ->where('deadline', '>=', date_create())
            ->orderBy('deadline', 'desc')
            ->get();
        $i = 0;
        dump('total ' . count($matches));
        foreach ($matches as $sport) {
            if ($i > 5)
                break;
            $match = \App\Models\LiaoGouModels\Match::where('id', $sport['mid'])->first();
            if (isset($match)) {
                $mid = $match->win_id;
                $odd = Odd::where('mid', '=', $match['id'])
                    ->where('type', '=', 3)
                    ->where('cid', '=', 17)
                    ->first();
                if (is_null($odd)) {
                    $this->oddsWithMatchAndType($mid, 3);
                    $i++;
                } else {
//                    echo 'has '.'</br>';
                }
            }
        }
    }

    /**
     * 爬足彩赔率
     * 每天一次
     */
    private function spiderLotteryOdd()
    {
        $date = date_create();
        $lotteryDetails = LotteryDetail::whereNull('result')
            ->where('date', '>', date_format(date_add($date, date_interval_create_from_date_string('24 hour')), 'Y-m-d 0:00'))
            ->orderby('date', 'asc')
            ->get();
        foreach ($lotteryDetails as $lotteryDetail) {
            dump($lotteryDetail);
            $match = \App\Models\LiaoGouModels\Match::where('id', $lotteryDetail['mid'])->first();
            if (isset($match)) {
                $mid = $match->win_id;
                $odd = Odd::where('mid', '=', $lotteryDetail['mid'])
                    ->where('type', '=', 3)
                    ->where('cid', '=', Odd::default_calculate_cid)
                    ->first();
                if (is_null($odd)) {
                    dump('no odd');
                    $this->oddsWithMatchAndType($mid, 3);
                }
            }
        }
    }

    /**
     * 填充比赛,足彩数据爬回来不包括比赛,需要这个另外爬数据(lotterydetail表)
     * 每1小时一次
     */
    private function spiderLotteryFillMatch()
    {
        $this->lotteryFillMatch();
    }

    /**
     * 爬历史数据框架,主要用于获取足彩期数对应id,结果等需要用这个id爬
     */
    private function spiderLotteryInit()
    {
        $this->spiderLotteryHistoryFrame();
    }

    /**
     * 爬某期北单数据
     * @param Request $request
     */
    private function spiderFillBDByIssueNum(Request $request)
    {
        if ($request->exists('issue_num')) {
            $this->spiderByIssueNum($request->input('issue_num'));
        }
    }

    /**
     * 爬某期北单胜负过关数据
     * @param Request $request
     */
    private function spiderFillBDWinByIssueNum(Request $request)
    {
        if ($request->exists('issue_num')) {
            $this->spiderByIssueNumWin($request->input('issue_num'));
        }
    }

    private function spiderLeagueRefresh(Request $request)
    {
        set_time_limit(0);
        $lid = $request->input('lid');
        $season = $request->input('season');
        $this->leagueRefreshById($lid, $season);
    }

    private function spiderTeamDetail(Request $request) {
        $lid = $request->input('tid');
        $this->spiderTeamDetailByHtml($lid, true);
    }

    /******************* end 足彩 *****************/

    /******************* 刷新数据用(例如刷新球探某场数据) *****************/
    private function refreshWinMatch(Request $request)
    {
        $mid = \App\Models\LiaoGouModels\Match::find($request->input('id', 0));
        if (isset($mid)) {
            $mid = $mid->win_id;
        }
        if ($mid > 0) {
            $this->matchDetail($mid, true, true, true);
        }
    }

    /******************* 刷新数据用end *****************/

    private function fillLgMatchTid(Request $request)
    {
        $matches = \App\Models\LiaoGouModels\Match::where(function ($q) {
            $q->wherenull('hid')
                ->orwherenull('aid');
        })
            ->where('time', '>', '2017-1-1')
            ->where('time', '<', date_format(date_create(), 'Y-m-d'))
            ->orderby('time', 'desc')
            ->take(50)
            ->get();
        dump(count($matches));
        echo 'matche count ' . count($matches);
        foreach ($matches as $match) {
            $this->matchDetail($match->win_id, false, false);
        }
    }

    public function refreshTipByMid(Request $request)
    {
        $mid = $request->input('mid');
        if (!isset($mid)) {
            return back()->with(['code' => 403, 'msg' => '传入的id为空']);
        }
        try {
            ob_start();
            $this->spiderBettingNewsWithMid($mid);
            $count = LotteryTip::lotteryTipsCount($mid);
            ob_clean();
            ob_end_flush();
        } catch (\Exception $e) {
            return response(['code' => 403, 'msg' => $e->getMessage()]);
        }
        if (isset($count) && $count > 0) {
            return response(['code' => 0, 'msg' => '重爬数据成功']);
        } else {
            return response(['code' => 403, 'msg' => '未能爬取到有效的数据']);
        }
    }

    public function convertErrorMatches(Request $request) {
        $mids = Match::query()->where('status', -99)->select('id as mid')->get();

        $lms = \App\Models\LiaoGouModels\Match::query()
            ->where('status', '>', -1)
            ->whereIn('win_id', $mids)->take(100)->get();
        echo 'convert lgm count = '.count($lms).'<br>';
        foreach ($lms as $ms) {
            $ms->status = -99;
            $ms->save();
        }
    }

    /**
     * 当前比赛事件
     * 1分钟一次
     */
    private function spiderMatchEvent(){
        $matches = Match::where("status", ">", 0)
            ->where("time", "<", date_create('+ 3 hours'))
            ->where("time", ">", date_create('- 3 hours'))
            ->orderBy("time", "desc")
            ->get();
        foreach ($matches as $match) {
            echo $match->hname . ' ' . $match->aname . ' ' . $match->time. '</br>';
            $this->matchEventData($match->id, false);
        }
    }

    /**
     * 同步结束的比赛事件
     * 10分钟一次
     */
    public function spiderSaveMatchEvent(){
        $start = date("Y-m-d h:00:00", strtotime('-1 days'));
        $end = date("Y-m-d h:00:00", strtotime('-2 hours'));

        $redisKey = "match_event_id";
//        Redis::del($redisKey);

        $value = Redis::get($redisKey);
        if (isset($value)) {
            $mid = $value;
        }
        dump($value, $start, $end);
        $query = Match::where("status", "<", 0)
            ->where("time", "<", $end)
            ->where("time", ">=", $start);
        if (isset($mid)) {
            $query->where('id', '>', $mid);
        }
        $matches = $query->orderBy("id", "asc")->get();

        dump('总共有比赛：'.count($matches)." 场");
        $count = 0;
        foreach ($matches as $match) {
            if ($count == 20)
                break;
            $event = MatchEvent::where('mid', '=', $match->id)->first();
            if (is_null($event)) {
                $count++;
                $this->matchEventData($match->id, true);
            }
            Redis::set($redisKey, $match->id);
        }
        dump("已经保存比赛：$count 场");
    }

    /**
     * 实时爬取正在比赛的 时间事件（角球、进球、危险进攻）
     * 5s一次
     */
    private function spiderLiveTimeEvent(Request $request = null) {
        if (isset($request)) {
            $matches = MatchLive::getLiveFootballMatches($request->input('table', 'matches_afters'), $request->input('diff', 3));
        } else {
            $matches = MatchLive::getLiveFootballMatches();
        }
        echo "live match count = ".count($matches). "<br>";
        foreach ($matches as $match) {
            $this->liveMatchTimeEvent($match->win_id);
        }
    }

    const win_event_types = [
        3=>'射门', 4=>'射正',5=>'犯规',6=>'角球',9=>'越位',
        11=>'黄牌', 13=>'红牌',14=>'控球率',15=>'头球',16=>'救球',
        34=>'射门不中', 35=>'中柱',36=>'头球成功',37=>'射门被档',38=>'铲球',
        39=>'过人', 40=>'界外球',41=>'传球',42=>'传球成功率',43=>'进攻',
        44=>'危险进攻'
    ];

    /**
     * 实时爬取 比赛统计事件（红黄牌、进球、换人）
     * 30s一次
     */
    private function spiderLiveStatisticEvent() {
        $time = time() * 100;
        $url = "http://live.titan007.com/vbsxml/detail.js?r=007$time";

        $content = $this->spiderTextFromUrlByWin007($url, true);

        $datas = explode(";\r\n", $content);
        $tempLgIds = array();
        $tempLgTime = array();
        $dataArray = array();
        $staticArray = array();
        foreach ($datas as $dataStr) {
            $dataStr = trim($dataStr);
            if (str_contains($dataStr, "=")) {
                $tempStrs = explode("=", $dataStr);
                if (str_contains($dataStr, "rq[")) {
                    if (count($tempStrs) > 1) {
                        $tempStr = str_replace('"', '', $tempStrs[1]);
                        list($mid, $home, $type, $time, $CH_name, $playIds, $ch_name) = explode('^', $tempStr);
                        if (!isset($tempLgIds[$mid])) {
                            $tmpMatch = \App\Models\LiaoGouModels\Match::getMatchWith($mid, 'win_id');
                            if (isset($tmpMatch)) {
                                $tempLgIds[$mid] = $tmpMatch->id;
                                $tempLgTime[$mid] = date('Ymd', strtotime($tmpMatch->time));
                            }
                            else{
                                $tempLgIds[$mid] = 0;
                                $tempLgTime[$mid] = '0';
                            }
                        }
                        $ch_name = trim($ch_name);
                        $ch_name = str_replace("↓", "", $ch_name);
                        $ch_name = str_replace("↑", "^", $ch_name);
                        $dataArray[$mid]['event'][] = implode(",", [$home, $type, $time, $ch_name]);

                        $eventItem = ['is_home'=>$home, 'kind'=>$type,'happen_time'=>$time];
                        if (str_contains($ch_name, "^")) {
                            list($player_name_j, $player_name_j2) = explode("^", $ch_name);
                            $eventItem['player_name_j'] = $player_name_j;
                            $eventItem['player_name_j2'] = $player_name_j2;
                        } else {
                            $eventItem['player_name_j'] = $ch_name;
                            $eventItem['player_name_j2'] = "";
                        }
                        if (!isset($staticArray[$mid]['event'])) {
                            $staticArray[$mid]['event'] = array();
                        }
                        $staticArray[$mid]['event']['events'][] = $eventItem;
                        if (isset($staticArray[$mid]['event']['last_event_time'])) {
                            $lastEventTime = $staticArray[$mid]['event']['last_event_time'];
                            if ($lastEventTime < $time) {
                                $staticArray[$mid]['event']['last_event_time'] = $time;
                            }
                        } else {
                            $staticArray[$mid]['event']['last_event_time'] = $time;
                        }
                    }
                } else if (str_contains($dataStr, "tc[")) {
                    if (count($tempStrs) > 1) {
                        $tempStr = str_replace('"', '', $tempStrs[1]);
                        list($mid, $tempData) = explode('^', $tempStr);
                        if (!isset($tempLgIds[$mid])) {
                            $tmpMatch = \App\Models\LiaoGouModels\Match::getMatchWith($mid, 'win_id');
                            if (isset($tmpMatch)) {
                                $tempLgIds[$mid] = $tmpMatch->id;
                                $tempLgTime[$mid] = date('Ymd', strtotime($tmpMatch->time));
                            }
                            else{
                                $tempLgIds[$mid] = 0;
                                $tempLgTime[$mid] = '0';
                            }
                        }
                        $datas = explode(";", $tempData);
                        $dataArray[$mid]['statistic'] = $datas;
                        $lg_mid = $tempLgIds[$mid];
                        $matchData = \App\Models\LiaoGouModels\MatchData::query()->where('id', $lg_mid)->first();
                        foreach ($datas as $aa) {
                            list($type, $h_count, $a_count) = explode(',', $aa);
                            if (isset($matchData)) {
                                if ($type == 43) { //进攻
                                    $matchData->h_attack = $h_count;
                                    $matchData->a_attack = $a_count;
                                } else if ($type == 44) { //危险进攻
                                    $matchData->h_danger_attack = $h_count;
                                    $matchData->a_danger_attack = $a_count;
                                }
                            }

                            //技术统计静态化
                            if (array_has(self::win_event_types, $type)) {
                                $staticItem = ['name' => self::win_event_types[$type], 'h'=>$h_count, 'a'=>$a_count,
                                'h_p'=>MatchDataChangeTool::dataPercent(intval($h_count), intval($a_count)),
                                    'a_p'=>MatchDataChangeTool::dataPercent(intval($a_count), intval($h_count))];

                                $staticArray[$mid]['tech'][] = $staticItem;
                            }
                        }
                        if (isset($matchData)) {
                            $matchData->save();
                        }
                    }
                }
            }
        }

        foreach ($staticArray as $mid=>$techData) {
            if (isset($tempLgIds[$mid]) && $tempLgIds[$mid] > 0) {
                echo "lg_mid = ".$tempLgIds[$mid]."<br>";
                StatisticFileTool::putFileToTerminal($techData, MatchLive::kSportFootball, $tempLgIds[$mid], 'tech');
            }
        }
    }

    /**
     * 定时删除 实时比赛相关的数据(SB滚球盘数据)
     */
    private function delMatchLiveFile() {
        $date = date("Ymd", strtotime('-3 days'));
        try {
            Storage::disk('match')->deleteDirectory("/live/odd/$date");
            Storage::disk('match')->deleteDirectory("/live/analyse/$date");
            Storage::disk('match')->deleteDirectory("/live/event/$date");
        } catch (Exception $exception) {
            dump($exception->getMessage());
        }
    }
}
