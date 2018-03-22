<?php

namespace App\Http\Controllers\BotAuthor;

use App\Models\LiaoGouModels\MatchAnalyse;
use App\Models\LiaoGouModels\MatchForecast;
use App\Models\LiaoGouModels\Odd;
use Illuminate\Routing\Controller;
use App\Models\LiaoGouModels\Match;
use Illuminate\Http\Request;

class MoroController extends Controller
{

    use MatchAnalyseTrait;

    public function index(Request $request, $action)
    {
        if (method_exists($this, $action)) {
            return $this->$action($request);
        } else {
            echo "Error: Not Found action 'MoroController->$action()'";
        }
    }

    //处理将要开始的比赛
    public function processWillStartMatch(Request $request)
    {
        $offset = $request->input('offset', 8);//偏移
        $hours = $request->input('hour', 12) + $offset;
        $matches = Match::query()
            ->where('time', '<', date_create("+$hours hours"))
            ->where('time', '>', date_create("+$offset hours"))
            ->where('status', 0)
            ->where('lid', '>', 0)
            ->where('genre', '>', Match::k_genre_all)
            ->whereNotNull('hid')
            ->whereNotNull('aid')
            ->get();
        foreach ($matches as $match) {
            echo dump('==> ' . $match->win_lname . ' ' . $match->hname . ' VS ' . $match->aname);
            $this->processDoneMatchBy($match->hid);
            $this->processDoneMatchBy($match->aid);
            $this->analyseMatch($match);

        }
    }

    //处理将要开始的比赛
    public function processWillStartMatchMoro(Request $request)
    {
        $offset = $request->input('offset', 12);//偏移
        $hours = $request->input('hour', 12) + $offset;
        $matches = Match::query()
            ->where('time', '<', date_create("+$hours hours"))
            ->where('time', '>', date_create("+$offset hours"))
            ->where('status', 0)
            ->where('lid', '>', 0)
            ->where('genre', '>', Match::k_genre_all)
            ->whereNotNull('hid')
            ->whereNotNull('aid')
            ->get();
        foreach ($matches as $match) {
            echo dump('==> ' . $match->win_lname . ' ' . $match->hname . ' VS ' . $match->aname);
//            $this->processDoneMatchBy($match->hid);
//            $this->processDoneMatchBy($match->aid);
            $this->analyseMatchMoro($match);

        }
    }

    //预测比赛结果
    public function forecastResultOUDone()
    {
        $matches = Match::query()
            ->leftJoin('match_analyses', 'match_analyses.id', '=', 'matches.id')
            ->leftJoin('odds', 'odds.mid', '=', 'matches.id')
            ->where('matches.time', '<', date_create('-1 days'))
            ->where('matches.time', '>', date_create('-3 months'))
            ->where('matches.status', -1)
            ->where('odds.cid', Odd::default_banker_id)
            ->where('odds.type', Odd::k_odd_type_ou)
            ->where('match_analyses.lately_home_count', '>=', 8)
            ->where('match_analyses.lately_away_count', '>=', 8)
            ->where('match_analyses.level_home_count', '>=', 3)
            ->where('match_analyses.level_away_count', '>=', 3)
            ->selectRaw('match_analyses.*,matches.hscore,matches.ascore,odds.middle1,odds.middle2,odds.up2,odds.down2')
//            ->count();
//            ->take(10)
            ->get();
        $downTotal = 0;
        $upTotal = 0;
        $downHit = 0;
        $upHit = 0;
        foreach ($matches as $match) {
            if (in_array($match->middle2, [2.25, 3.25, 4.25])
                && $match->middle2 >= $match->middle1
            ) {//小球
                if ($match->lately_total_goal < $match->middle2
                    && $match->level_total_goal < $match->middle2
                ) {
                    $downTotal++;
                    if (($match->hscore + $match->ascore) < $match->middle2) {
                        $downHit++;
                    }
                }
            } elseif (in_array($match->middle2, [1.75, 2.5, 3.5, 2.75, 3.75, 4.75])
                && $match->middle2 <= $match->middle1
            ) {//大球
                if ($match->lately_total_goal - 0.25 > $match->middle2
                    && $match->level_total_goal - 0.25 > $match->middle2
                ) {
                    $upTotal++;
                    if (($match->hscore + $match->ascore) > $match->middle2) {
                        $upHit++;
                    }
                }
            }
        }
//        echo dump('total:' . $matches->count());
        echo dump('推荐小球总数:' . $downTotal . ' 命中:' . $downHit . ' 比例:' . (round($downHit / $downTotal * 100, 2)) . '%');
        echo dump('推荐大球总数:' . $upTotal . ' 命中:' . $upHit . ' 比例:' . (round($upHit / $upTotal * 100, 2)) . '%');

    }

    public function forecastResultAsianDone()
    {
        $matches = Match::query()
            ->leftJoin('match_analyses', 'match_analyses.id', '=', 'matches.id')
            ->leftJoin('odds', 'odds.mid', '=', 'matches.id')
            ->where('matches.time', '<', date_create('-1 days'))
            ->where('matches.time', '>', date_create('-3 months'))
            ->where('matches.status', -1)
            ->where('odds.cid', Odd::default_banker_id)
            ->where('odds.type', Odd::k_odd_type_asian)
            ->where('match_analyses.lately_home_count', '>=', 8)
            ->where('match_analyses.lately_away_count', '>=', 8)
            ->where('match_analyses.level_home_count', '>=', 3)
            ->where('match_analyses.level_away_count', '>=', 3)
            ->selectRaw('match_analyses.*,matches.hscore,matches.ascore,odds.middle1,odds.middle2,odds.up2,odds.down2')
//            ->count();
//            ->take(10)
            ->get();
        $downTotal = 0;
        $upTotal = 0;
        $downHit = 0;
        $upHit = 0;
        foreach ($matches as $match) {
            if (in_array($match->middle2, [2.25, 3.25, 4.25])
                && $match->middle2 >= $match->middle1
            ) {//小球
                if ($match->lately_total_goal < $match->middle2
                    && $match->level_total_goal < $match->middle2
                ) {
                    $downTotal++;
                    if (($match->hscore + $match->ascore) < $match->middle2) {
                        $downHit++;
                    }
                }
            } elseif (in_array($match->middle2, [1.75, 2.5, 3.5, 2.75, 3.75, 4.75])
//                && $match->middle2 <= $match->middle1
            ) {//大球
                if ($match->lately_total_goal - 0.25 > $match->middle2
                    && $match->level_total_goal - 0.25 > $match->middle2
                ) {
                    $upTotal++;
                    if (($match->hscore + $match->ascore) > $match->middle2) {
                        $upHit++;
                    }
                }
            }
        }
//        echo dump('total:' . $matches->count());
        echo dump('推荐小球总数:' . $downTotal . ' 命中:' . $downHit . ' 比例:' . (round($downHit / $downTotal * 100, 2)) . '%');
        echo dump('推荐大球总数:' . $upTotal . ' 命中:' . $upHit . ' 比例:' . (round($upHit / $upTotal * 100, 2)) . '%');

    }

    //预测比赛结果
    public function forecastResult()
    {
        $matches = Match::query()
            ->where('time', '<', date_create('+8 hours'))
            ->where('time', '>', date_create('+30 min'))
            ->where('status', 0)
            ->where('lid', '>', 0)
            ->where('genre', '>', Match::k_genre_all)
            ->whereNotNull('hid')
            ->whereNotNull('aid')
            ->orderBy('round', 'desc')
            ->get();

        foreach ($matches as $match) {
            $ma = MatchAnalyse::query()->find($match->id);
            if (!isset($ma) ||
                $ma->lately_home_count < 8 ||
                $ma->lately_away_count < 8 ||
                $ma->level_home_count < 3 ||
                $ma->level_away_count < 3
            ) {
                continue;
            }
            $asian = $match->defaultAsianOdd();
            $ou = $match->defaultOuOdd();
            if (isset($asian) && isset($ou)) {
                //上盘
                if (
                    ($ma->level_diff_goal >= $asian->middle2
                        && $ma->lately_diff_goal >= $asian->middle2
                        && in_array($asian->middle2, [0.5, 0.75, 1.5, 1.75, 2.5, 2.75])
                    )
                    ||
                    ($ma->level_diff_goal > $asian->middle2
                        && $ma->lately_diff_goal > $asian->middle2
                        && in_array($asian->middle2, [-3, -2.5, -2.25, -2, -1.5, -1.25, -1, -0.5, -0.25, 0, 1, 2, 3])
                    )
//                    && $asian->middle2 >= $asian->middle1
//                    && $asian->middle2 >= 0
                ) {
                    $mf = MatchForecast::query()
                        ->where('mid', $match->id)
                        ->where('type', MatchForecast::kForecastTypeAsian)
                        ->first();
                    if (!isset($mf)) {
                        $mf = new MatchForecast();
                        $mf->mid = $match->id;
                        $mf->type = MatchForecast::kForecastTypeAsian;
                        $mf->handicap = $asian->middle2;
                        $mf->up = $asian->up2;
                        $mf->middle = 0;
                        $mf->down = $asian->down2;
                        $mf->result = 1;
                        $mf->save();
                    }
                }

                //下盘
                if (
                    $ma->level_diff_goal < $asian->middle2 - 0.25
                    && $ma->lately_diff_goal < $asian->middle2 - 0.25
                    && in_array($asian->middle2, [-3, -2.75, -2.5, -2, -1.75, -1.5, -1, -0.75, -0.5, 0, 0.25, 0.5, 1, 1.25, 1.5, 2, 2.25, 2.5, 3])
//                    && $asian->middle2 <= $asian->middle1
//                    && $asian->middle2 <= 0
                ) {
                    $mf = MatchForecast::query()
                        ->where('mid', $match->id)
                        ->where('type', MatchForecast::kForecastTypeAsian)
                        ->first();
                    if (!isset($mf)) {
                        $mf = new MatchForecast();
                        $mf->mid = $match->id;
                        $mf->type = MatchForecast::kForecastTypeAsian;
                        $mf->handicap = $asian->middle2;
                        $mf->up = $asian->up2;
                        $mf->middle = 0;
                        $mf->down = $asian->down2;
                        $mf->result = 3;
                        $mf->save();
                    }
                }

                //大球
                if (
                    $ma->level_total_goal > $ou->middle2// + 0.25
                    && $ma->lately_total_goal > $ou->middle2// + 0.25
//                    && $ou->middle2 <= $ou->middle1//降盘
                    && in_array($ou->middle2, [1.75, 2, 2.5, 2.75, 3, 3.5, 3.75, 4, 4.5, 4.75])
                ) {
                    $mf = MatchForecast::query()
                        ->where('mid', $match->id)
                        ->where('type', MatchForecast::kForecastTypeOu)
                        ->first();
                    if (!isset($mf)) {
                        $mf = new MatchForecast();
                        $mf->mid = $match->id;
                        $mf->type = MatchForecast::kForecastTypeOu;
                        $mf->handicap = $ou->middle2;
                        $mf->up = $ou->up2;
                        $mf->middle = 0;
                        $mf->down = $ou->down2;
                        $mf->result = 1;
                        $mf->save();
                    }
                }

                //小球
                if (
                    $ma->level_total_goal < $ou->middle2
                    && $ma->lately_total_goal < $ou->middle2
//                    && $ou->middle2 >= $ou->middle1//升盘
                    && in_array($ou->middle2, [2, 2.25, 2.5, 3, 3.25, 3.5, 4, 4.25])
                ) {
                    $mf = MatchForecast::query()
                        ->where('mid', $match->id)
                        ->where('type', MatchForecast::kForecastTypeOu)
                        ->first();
                    if (!isset($mf)) {
                        $mf = new MatchForecast();
                        $mf->mid = $match->id;
                        $mf->type = MatchForecast::kForecastTypeOu;
                        $mf->handicap = $ou->middle2;
                        $mf->up = $ou->up2;
                        $mf->middle = 0;
                        $mf->down = $ou->down2;
                        $mf->result = 3;
                        $mf->save();
                    }
                }
            }
        }
    }

    //预测比赛结果列表
    public function forecasts(Request $request)
    {
        $query = MatchForecast::query()
            ->leftJoin('matches', 'matches.id', '=', 'match_forecasts.mid')
            ->orderBy('matches.time', 'desc')
            ->addSelect('match_forecasts.*')
            ->addSelect('matches.hscore')
            ->addSelect('matches.ascore')
            ->addSelect('matches.hname')
            ->addSelect('matches.aname')
            ->addSelect('matches.time')
            ->addSelect('matches.win_lname');
        if ($request->has('date')) {
            $query->where('matches.time', '>', date('Y-m-d', strtotime($request->date)) . ' 10:00:00');
            $query->where('matches.time', '<', date('Y-m-d', strtotime('+1day', strtotime($request->date))) . ' 10:00:00');
        }
        if ($request->has('hit')) {
            if ($request->hit == -1) {//未结算
                $query->whereNull('hit');
            } else if ($request->hit == 0) {//已结算
                $query->where('hit', '>', 0);
            } else {//指定比赛
                $query->where('hit', $request->hit);
            }
        }
        $mfs = $query->paginate();
        if ($request->has('date')) {
            $mfs->appends(['date' => $request->date]);
        }
        if ($request->has('hit')) {
            $mfs->appends(['hit' => $request->hit]);
        }
        return response()->json($mfs);
    }

    //预测比赛结果命中统计
    public function forecastHit()
    {
        $mfs = MatchForecast::query()
            ->leftJoin('matches', 'matches.id', '=', 'match_forecasts.mid')
            ->whereNull('match_forecasts.hit')
            ->where('matches.time', '<', date_create('2 hour'))
            ->where('matches.status', -1)
            ->addSelect('match_forecasts.*')
            ->addSelect('matches.hscore')
            ->addSelect('matches.ascore')
            ->get();

        foreach ($mfs as $mf) {
//            echo dump($mf);
            $diff = $mf->hscore - $mf->ascore;
            $total = $mf->hscore + $mf->ascore;
            if ($mf->type == MatchForecast::kForecastTypeAsian) {
                if ($mf->result == 1) {
                    if ($diff > $mf->handicap) {
                        if (($diff - 0.25) > $mf->handicap) {
                            $mf->hit = 10;
                            $mf->hit_odd = $mf->up;
                        } else {
                            $mf->hit = 11;
                            $mf->hit_odd = $mf->up / 2;
                        }
                    } elseif ($diff == $mf->handicap) {
                        $mf->hit = 20;
                        $mf->hit_odd = 0;
                    } else {
                        if (($diff + 0.25) < $mf->handicap) {
                            $mf->hit = 30;
                            $mf->hit_odd = -1;
                        } else {
                            $mf->hit = 31;
                            $mf->hit_odd = -0.5;
                        }
                    }
                } elseif ($mf->result == 3) {
                    if ($diff < $mf->handicap) {
                        if (($diff + 0.25) < $mf->handicap) {
                            $mf->hit = 10;
                            $mf->hit_odd = $mf->down;
                        } else {
                            $mf->hit = 11;
                            $mf->hit_odd = $mf->down / 2;
                        }
                    } elseif ($diff == $mf->handicap) {
                        $mf->hit = 20;
                        $mf->hit_odd = 0;
                    } else {
                        if (($diff - 0.25) > $mf->handicap) {
                            $mf->hit = 30;
                            $mf->hit_odd = -1;
                        } else {
                            $mf->hit = 31;
                            $mf->hit_odd = -0.5;
                        }
                    }
                }
            }

            if ($mf->type == MatchForecast::kForecastTypeOu) {
                if ($mf->result == 1) {
                    if ($total > $mf->handicap) {
                        if (($total - 0.25) > $mf->handicap) {
                            $mf->hit = 10;
                            $mf->hit_odd = $mf->up;
                        } else {
                            $mf->hit = 11;
                            $mf->hit_odd = $mf->up / 2;
                        }
                    } elseif ($total == $mf->handicap) {
                        $mf->hit = 20;
                        $mf->hit_odd = 0;
                    } else {
                        if (($total + 0.25) < $mf->handicap) {
                            $mf->hit = 30;
                            $mf->hit_odd = -1;
                        } else {
                            $mf->hit = 31;
                            $mf->hit_odd = -0.5;
                        }
                    }
                } elseif ($mf->result == 3) {
                    if ($total < $mf->handicap) {
                        if (($total + 0.25) < $mf->handicap) {
                            $mf->hit = 10;
                            $mf->hit_odd = $mf->down;
                        } else {
                            $mf->hit = 11;
                            $mf->hit_odd = $mf->down / 2;
                        }
                    } elseif ($total == $mf->handicap) {
                        $mf->hit = 20;
                        $mf->hit_odd = 0;
                    } else {
                        if (($total - 0.25) > $mf->handicap) {
                            $mf->hit = 30;
                            $mf->hit_odd = -1;
                        } else {
                            $mf->hit = 31;
                            $mf->hit_odd = -0.5;
                        }
                    }
                }
            }
            $mf->save();
        }
    }
}
