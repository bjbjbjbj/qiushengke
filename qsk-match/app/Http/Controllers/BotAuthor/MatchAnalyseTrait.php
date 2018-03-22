<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 16/11/28
 * Time: 上午10:29
 */

namespace App\Http\Controllers\BotAuthor;

use App\Models\LiaoGouModels\LeagueSub;
use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\MatchAnalyse;
use App\Models\LiaoGouModels\MatchMoroAnalyse;
use App\Models\LiaoGouModels\Score;
use App\Models\LiaoGouModels\Season;
use App\Models\LiaoGouModels\TeamAnalyse;

/*
 * 比赛分析
 */

trait MatchAnalyseTrait
{

    private function processDoneMatch($m)
    {
        $hta = TeamAnalyse::query()->where(['tid' => $m->hid, 'mid' => $m->id])->first();
        $ata = TeamAnalyse::query()->where(['tid' => $m->aid, 'mid' => $m->id])->first();
        if (empty($hta) || empty($ata)) {
            $asian = $m->defaultAsianOdd();
            $ou = $m->defaultOuOdd();
            if (isset($asian) && isset($ou)) {
                if (empty($hta)) {
                    $hta = new TeamAnalyse();
                    $hta->tid = $m->hid;
                    $hta->mid = $m->id;
                    $hta->lid = $m->lid;
                    $hta->time = $m->time;
                    $hta->home = $m->neutral == 0 ? 1 : 0;
                    $hta->goal = $m->hscore;
                    $hta->fumble = $m->ascore;
                    $hta->total = $m->hscore + $m->ascore;
                    $hta->diff = $m->hscore - $m->ascore;
                    $hta->asian = $asian->middle1;
                    $hta->asian_end = $asian->middle2;
                    $hta->ou = $ou->middle1;
                    $hta->ou_end = $ou->middle2;
                    $hta->save();
                }

                if (empty($ata)) {
                    $hta = new TeamAnalyse();
                    $hta->tid = $m->aid;
                    $hta->mid = $m->id;
                    $hta->lid = $m->lid;
                    $hta->time = $m->time;
                    $hta->home = 0;
                    $hta->goal = $m->ascore;
                    $hta->fumble = $m->hscore;
                    $hta->total = $m->hscore + $m->ascore;
                    $hta->diff = $m->ascore - $m->hscore;
                    $hta->asian = $asian->middle1 * -1;
                    $hta->asian_end = $asian->middle2 * -1;
                    $hta->ou = $ou->middle1;
                    $hta->ou_end = $ou->middle1;
                    $hta->save();
                }
            }
        }
    }

    private function processDoneMatchBy($tid)
    {
        $ms = Match::query()
            ->where('time', '<', date_create('-1 day'))
            ->where('time', '>', date_create('-1 year'))
            ->where('status', -1)
//            ->where('lid', '>', 0)
            ->where('genre', '>', Match::k_genre_all)
            ->where(function ($query) use ($tid) {
                $query->where('hid', '=', $tid)
                    ->orWhere('aid', '=', $tid);
            })
            ->get();
        foreach ($ms as $m) {
            $this->processDoneMatch($m);
        }
    }

    private function analyseMatch(Match $match)
    {
        $ma = MatchAnalyse::query()->find($match->id);
        if (isset($ma)) {
            return;
        } else {
            if (!isset($match->leagueData)
                || $match->leagueData->type != 1
            ) {
                return;
            }
            $ma = new MatchAnalyse();
            $ma->id = $match->id;
        }
        $asian = $match->defaultAsianOdd();
        $ou = $match->defaultOuOdd();
        if (isset($asian) && isset($ou)) {
            $ma->time = $match->time;
            $ma->home = $match->hname;
            $ma->away = $match->aname;
            $ma->league = $match->win_lname;
            $ma->home_rank = $match->hrank;
            $ma->away_rank = $match->arank;
            if (isset($match->lsid) && $match->lsid > 0) {
                $ls = LeagueSub::query()
                    ->where('lid', $match->lid)
                    ->where('subid', $match->lsid)
                    ->where('season', $match->season)
                    ->where('type', 1)
                    ->first();
                if (isset($ls)) {
                    $ma->sub_league = $ls->name;
                    $ma->total_round = $ls->total_round;
                    $ma->curr_round = $ls->curr_round;
                }
            } else {
                $season = Season::query()->where('lid', $match->lid)->orderBy('year', 'desc')->first();
                if (isset($season)) {
                    $ma->total_round = $season->total_round;
                    $ma->curr_round = $season->curr_round;
                }
            }

            //球队积分状态
            $query = Score::query()
                ->where('lid', $match->lid)
                ->where('tid', $match->hid);
            if (isset($match->season)) {
                $query->where('season', $match->season);
            }
            if (isset($match->lsid)) {
                $query->where('lsid', $match->lsid);
            }
            if (isset($match->stage)) {
                $query->where('stage', $match->stage);
            }
            if (isset($match->group)) {
                $query->where('group', $match->group);
            }
            $hscore = $query->first();
            if (isset($hscore)) {
                $ma->home_score = $hscore->score;
                $ma->home_status = $hscore->name;
            }

            $query = Score::query()
                ->where('lid', $match->lid)
                ->where('tid', $match->aid);
            if (isset($match->season)) {
                $query->where('season', $match->season);
            }
            if (isset($match->lsid)) {
                $query->where('lsid', $match->lsid);
            }
            if (isset($match->stage)) {
                $query->where('stage', $match->stage);
            }
            if (isset($match->group)) {
                $query->where('group', $match->group);
            }
            $ascore = $query->first();
            if (isset($ascore)) {
                $ma->away_score = $ascore->score;
                $ma->away_status = $ascore->name;
            }

            //算法-同实力
            $htas = TeamAnalyse::query()
                ->where('tid', $match->hid)
//                ->where('home', $match->neutral == 0 ? 1 : 0)
                ->where('time', '>', date_create('-1 year'))
                ->whereRaw('abs(' . $asian->middle2 . '- asian_end) < 0.5')
                ->orderBy('time', 'desc')
                ->take(16)
                ->get();
            $atas = TeamAnalyse::query()
                ->where('tid', $match->aid)
//                ->where('home', 0)
                ->where('time', '>', date_create('-1 year'))
                ->whereRaw('abs(' . ($asian->middle2 * -1) . '- asian_end) < 0.5')
                ->orderBy('time', 'desc')
                ->take(16)
                ->get();
            if ($htas->count() > 2 && $atas->count() > 2) {
                $hgoal = round((($htas->sum('goal') - $htas->max('goal') - $htas->min('goal')) / ($htas->count() - 2) +
                        ($atas->sum('fumble') - $atas->max('fumble') - $atas->min('fumble')) / ($atas->count() - 2)) / 2, 2);
                $hgoal = round(round($hgoal * 4, 0) / 4, 2);
                $agoal = round((($atas->sum('goal') - $atas->max('goal') - $atas->min('goal')) / ($atas->count() - 2) +
                        ($htas->sum('fumble') - $htas->max('fumble') - $htas->min('fumble')) / ($htas->count() - 2)) / 2, 2);
                $agoal = round(round($agoal * 4, 0) / 4, 2);

                $ma->level_home_count = $htas->count();
                $ma->level_away_count = $atas->count();
                $ma->level_home_goal = $hgoal;
                $ma->level_away_goal = $agoal;
                $ma->level_total_goal = ($hgoal + $agoal);
                $ma->level_diff_goal = ($hgoal - $agoal);
            }

            //算法-近12场
            $htas = TeamAnalyse::query()
                ->where('tid', $match->hid)
//                ->where('home', $match->neutral == 0 ? 1 : 0)
                ->where('lid', $match->lid)
                ->where('time', '>', date_create('-1 year'))
                ->orderBy('time', 'desc')
                ->take(16)
                ->get();
            $atas = TeamAnalyse::query()
                ->where('tid', $match->aid)
//                ->where('home', 0)
                ->where('lid', $match->lid)
                ->where('time', '>', date_create('-1 year'))
                ->orderBy('time', 'desc')
                ->take(16)
                ->get();
            if ($htas->count() > 7 && $atas->count() > 7) {
                $hgoal = round((($htas->sum('goal') - $htas->max('goal') - $htas->min('goal')) / ($htas->count() - 2) +
                        ($atas->sum('fumble') - $atas->max('fumble') - $atas->min('fumble')) / ($atas->count() - 2)) / 2, 2);
                $hgoal = round(round($hgoal * 4, 0) / 4, 2);
                $agoal = round((($atas->sum('goal') - $atas->max('goal') - $atas->min('goal')) / ($atas->count() - 2) +
                        ($htas->sum('fumble') - $htas->max('fumble') - $htas->min('fumble')) / ($htas->count() - 2)) / 2, 2);
                $agoal = round(round($agoal * 4, 0) / 4, 2);

                $ma->lately_home_count = $htas->count();
                $ma->lately_away_count = $atas->count();
                $ma->lately_home_goal = $hgoal;
                $ma->lately_away_goal = $agoal;
                $ma->lately_total_goal = ($hgoal + $agoal);
                $ma->lately_diff_goal = ($hgoal - $agoal);
            }

            if (!isset($ma->level_home_count) || !isset($ma->lately_home_count)) {
                return;
            }

            //球队最近比赛状态
            $htas = TeamAnalyse::query()
                ->where('tid', $match->hid)
                ->where('time', '>', date_create('-1 year'))
                ->orderBy('time', 'desc')
                ->get();
            if ($htas->count() > 9) {
                $home_wins = 0;
                $home_home_wins = 0;
                $home_not_loses = 0;
                $home_home_not_loses = 0;
                foreach ($htas as $hta) {
                    if ($hta->diff > 0) {
                        $home_wins++;
                        $home_not_loses++;
                        if ($hta->home == 1) {
                            $home_home_wins++;
                            $home_home_not_loses++;
                        }
                    } elseif ($hta->diff == 0) {
                        $home_not_loses++;
                        if ($hta->home == 1) {
                            $home_home_not_loses++;
                        }
                        if ($home_wins >= 0) {
                            $ma->home_wins = $home_wins;
                            $home_wins = -9999;
                        }
                        if ($hta->home == 1) {
                            if ($home_home_wins >= 0) {
                                $ma->home_home_wins = $home_home_wins;
                                $home_home_wins = -9999;
                            }
                        }
                    } else {
                        if ($home_wins >= 0) {
                            $ma->home_wins = $home_wins;
                            $home_wins = -9999;
                        }
                        if ($hta->home == 1) {
                            if ($home_home_wins >= 0) {
                                $ma->home_home_wins = $home_home_wins;
                                $home_home_wins = -9999;
                            }
                        }
                        if ($home_not_loses >= 0) {
                            $ma->home_not_loses = $home_not_loses;
                            $home_not_loses = -9999;
                        }
                        if ($hta->home == 1) {
                            if ($home_home_not_loses >= 0) {
                                $ma->home_home_not_loses = $home_home_not_loses;
                                $home_home_not_loses = -9999;
                            }
                        }
                    }
                }
            }


            $atas = TeamAnalyse::query()
                ->where('tid', $match->aid)
                ->where('time', '>', date_create('-1 year'))
                ->orderBy('time', 'desc')
                ->get();
            if ($atas->count() > 9) {
                $away_wins = 0;
                $away_away_wins = 0;
                $away_not_loses = 0;
                $away_away_not_loses = 0;
                foreach ($atas as $ata) {
                    if ($ata->diff > 0) {
                        $away_wins++;
                        $away_not_loses++;
                        if ($ata->home == 0) {
                            $away_away_wins++;
                            $away_away_not_loses++;
                        }
                    } elseif ($ata->diff == 0) {
                        $away_not_loses++;
                        if ($ata->home == 1) {
                            $away_away_not_loses++;
                        }
                        if ($away_wins >= 0) {
                            $ma->away_wins = $away_wins;
                            $away_wins = -9999;
                        }
                        if ($ata->home == 0) {
                            if ($away_away_wins >= 0) {
                                $ma->away_away_wins = $away_away_wins;
                                $away_away_wins = -9999;
                            }
                        }
                    } else {
                        if ($away_wins >= 0) {
                            $ma->away_wins = $away_wins;
                            $away_wins = -9999;
                        }
                        if ($ata->home == 1) {
                            if ($away_away_wins >= 0) {
                                $ma->away_away_wins = $away_away_wins;
                                $away_away_wins = -9999;
                            }
                        }
                        if ($away_not_loses >= 0) {
                            $ma->away_not_loses = $away_not_loses;
                            $away_not_loses = -9999;
                        }
                        if ($ata->home == 1) {
                            if ($away_away_not_loses >= 0) {
                                $ma->away_away_not_loses = $away_away_not_loses;
                                $away_away_not_loses = -9999;
                            }
                        }
                    }
                }
            }

            //球队最近是否有比赛
            $homeLatelyDoneMatch = Match::query()
                ->where('time', '>', date('Y-m-d H:i:s', strtotime('-4 day', strtotime($match->time))))
                ->where('time', '<', $match->time)
                ->where(function ($query) use ($match) {
                    $query->where('hid', '=', $match->hid)
                        ->orWhere('aid', '=', $match->hid);
                })
                ->where('status', -1)
                ->orderBy('time', 'desc')
                ->first();
            if (isset($homeLatelyDoneMatch)) {
                $ma->home_lately_done_time = $homeLatelyDoneMatch->time;
                $ma->home_lately_done_mid = $homeLatelyDoneMatch->id;
            }
            $homeLatelyWaitMatch = Match::query()
                ->where('time', '<', date('Y-m-d H:i:s', strtotime('+4 day', strtotime($match->time))))
                ->where('time', '>', $match->time)
                ->where(function ($query) use ($match) {
                    $query->where('hid', '=', $match->hid)
                        ->orWhere('aid', '=', $match->hid);
                })
                ->where('status', 0)
                ->orderBy('time')
                ->first();
            if (isset($homeLatelyWaitMatch)) {
                $ma->home_lately_wait_time = $homeLatelyWaitMatch->time;
                $ma->home_lately_wait_mid = $homeLatelyWaitMatch->id;
            }
            $awayLatelyDoneMatch = Match::query()
                ->where('time', '>', date('Y-m-d H:i:s', strtotime('-4 day', strtotime($match->time))))
                ->where('time', '<', $match->time)
                ->where(function ($query) use ($match) {
                    $query->where('hid', '=', $match->aid)
                        ->orWhere('aid', '=', $match->aid);
                })
                ->where('status', -1)
                ->orderBy('time', 'desc')
                ->first();
            if (isset($awayLatelyDoneMatch)) {
                $ma->away_lately_done_time = $awayLatelyDoneMatch->time;
                $ma->away_lately_done_mid = $awayLatelyDoneMatch->id;
            }
            $awayLatelyWaitMatch = Match::query()
                ->where('time', '<', date('Y-m-d H:i:s', strtotime('+4 day', strtotime($match->time))))
                ->where('time', '>', $match->time)
                ->where(function ($query) use ($match) {
                    $query->where('hid', '=', $match->aid)
                        ->orWhere('aid', '=', $match->aid);
                })
                ->where('status', 0)
                ->orderBy('time')
                ->first();
            if (isset($awayLatelyWaitMatch)) {
                $ma->away_lately_wait_time = $awayLatelyWaitMatch->time;
                $ma->away_lately_wait_mid = $awayLatelyWaitMatch->id;
            }

            $ma->save();
        }
    }

    private function analyseMatchMoro(Match $match)
    {
        if (!isset($match->leagueData) || $match->leagueData->type != 1) {
            return;
        }
        $ma = MatchMoroAnalyse::query()->find($match->id);
        if (!isset($ma)) {
            $ma = new MatchMoroAnalyse();
            $ma->id = $match->id;
        }
        $asian = $match->defaultAsianOdd();
        $ou = $match->defaultOuOdd();
        if (isset($asian) && isset($ou)) {
            $ma->time = $match->time;
            $ma->home = $match->hname;
            $ma->away = $match->aname;
            $ma->league = $match->win_lname;
            $ma->home_rank = $match->hrank;
            $ma->away_rank = $match->arank;
            if (isset($match->lsid) && $match->lsid > 0) {
                $ls = LeagueSub::query()
                    ->where('lid', $match->lid)
                    ->where('subid', $match->lsid)
                    ->where('season', $match->season)
                    ->where('type', 1)
                    ->first();
                if (isset($ls)) {
                    $ma->sub_league = $ls->name;
                    $ma->total_round = $ls->total_round;
                    $ma->curr_round = $ls->curr_round;
                }
            } else {
                $season = Season::query()->where('lid', $match->lid)->orderBy('year', 'desc')->first();
                if (isset($season)) {
                    $ma->total_round = $season->total_round;
                    $ma->curr_round = $season->curr_round;
                }
            }

            //球队积分状态
            $query = Score::query()
                ->where('lid', $match->lid)
                ->where('tid', $match->hid);
            if (isset($match->season)) {
                $query->where('season', $match->season);
            }
            if (isset($match->lsid)) {
                $query->where('lsid', $match->lsid);
            }
            if (isset($match->stage)) {
                $query->where('stage', $match->stage);
            }
            if (isset($match->group)) {
                $query->where('group', $match->group);
            }
            $hscore = $query->first();
            if (isset($hscore)) {
                $ma->home_score = $hscore->score;
                $ma->home_status = $hscore->name;
            }

            $query = Score::query()
                ->where('lid', $match->lid)
                ->where('tid', $match->aid);
            if (isset($match->season)) {
                $query->where('season', $match->season);
            }
            if (isset($match->lsid)) {
                $query->where('lsid', $match->lsid);
            }
            if (isset($match->stage)) {
                $query->where('stage', $match->stage);
            }
            if (isset($match->group)) {
                $query->where('group', $match->group);
            }
            $ascore = $query->first();
            if (isset($ascore)) {
                $ma->away_score = $ascore->score;
                $ma->away_status = $ascore->name;
            }

            //算法-同实力
            $htas = TeamAnalyse::query()
                ->where('tid', $match->hid)
//                ->where('home', $match->neutral == 0 ? 1 : 0)
                ->where('time', '>', date_create('-1 year'))
                ->whereRaw('abs(' . $asian->middle2 . '- asian_end) < 0.5')
//                    ->orderByRaw('abs(' . $asian->middle2 . '- asian_end)')
//                    ->take(10)
                ->get();
            $atas = TeamAnalyse::query()
                ->where('tid', $match->aid)
//                ->where('home', 0)
                ->where('time', '>', date_create('-1 year'))
                ->whereRaw('abs(' . $asian->middle2 . '- asian_end) < 0.5')
//                    ->orderByRaw('abs(' . $asian->middle2 . '- asian_end)')
//                    ->take(10)
                ->get();
            if ($htas->count() > 2 && $atas->count() > 2) {
                $hgoal = round((($htas->sum('goal') - $htas->max('goal') - $htas->min('goal')) / ($htas->count() - 2) +
                        ($atas->sum('fumble') - $atas->max('fumble') - $atas->min('fumble')) / ($atas->count() - 2)) / 2, 2);
                $hgoal = round(round($hgoal * 4, 0) / 4, 2);
                $agoal = round((($atas->sum('goal') - $atas->max('goal') - $atas->min('goal')) / ($atas->count() - 2) +
                        ($htas->sum('fumble') - $htas->max('fumble') - $htas->min('fumble')) / ($htas->count() - 2)) / 2, 2);
                $agoal = round(round($agoal * 4, 0) / 4, 2);

                $ma->level_home_count = $htas->count();
                $ma->level_away_count = $atas->count();
                $ma->level_home_goal = $hgoal;
                $ma->level_away_goal = $agoal;
                $ma->level_total_goal = ($hgoal + $agoal);
                $ma->level_diff_goal = ($hgoal - $agoal);
            }

            //算法-近12场
            $htas = TeamAnalyse::query()
                ->where('tid', $match->hid)
//                ->where('home', $match->neutral == 0 ? 1 : 0)
                ->where('lid', $match->lid)
                ->where('time', '>', date_create('-1 year'))
                ->orderBy('time', 'desc')
                ->take(12)
                ->get();
            $atas = TeamAnalyse::query()
                ->where('tid', $match->aid)
//                ->where('home', 0)
                ->where('lid', $match->lid)
                ->where('time', '>', date_create('-1 year'))
                ->orderBy('time', 'desc')
                ->take(12)
                ->get();
            if ($htas->count() > 7 && $atas->count() > 7) {
                $hgoal = round((($htas->sum('goal') - $htas->max('goal') - $htas->min('goal')) / ($htas->count() - 2) +
                        ($atas->sum('fumble') - $atas->max('fumble') - $atas->min('fumble')) / ($atas->count() - 2)) / 2, 2);
                $hgoal = round(round($hgoal * 4, 0) / 4, 2);
                $agoal = round((($atas->sum('goal') - $atas->max('goal') - $atas->min('goal')) / ($atas->count() - 2) +
                        ($htas->sum('fumble') - $htas->max('fumble') - $htas->min('fumble')) / ($htas->count() - 2)) / 2, 2);
                $agoal = round(round($agoal * 4, 0) / 4, 2);

                $ma->lately_home_count = $htas->count();
                $ma->lately_away_count = $atas->count();
                $ma->lately_home_goal = $hgoal;
                $ma->lately_away_goal = $agoal;
                $ma->lately_total_goal = ($hgoal + $agoal);
                $ma->lately_diff_goal = ($hgoal - $agoal);
            }

            if (!isset($ma->level_home_count) || !isset($ma->lately_home_count)) {
                return;
            }

            //球队最近比赛状态
            $htas = TeamAnalyse::query()
                ->where('tid', $match->hid)
                ->where('time', '>', date_create('-1 year'))
                ->orderBy('time', 'desc')
                ->get();
            if ($htas->count() > 9) {
                $home_wins = 0;
                $home_home_wins = 0;
                $home_not_loses = 0;
                $home_home_not_loses = 0;
                foreach ($htas as $hta) {
                    if ($hta->diff > 0) {
                        $home_wins++;
                        $home_not_loses++;
                        if ($hta->home == 1) {
                            $home_home_wins++;
                            $home_home_not_loses++;
                        }
                    } elseif ($hta->diff == 0) {
                        $home_not_loses++;
                        if ($hta->home == 1) {
                            $home_home_not_loses++;
                        }
                        if ($home_wins >= 0) {
                            $ma->home_wins = $home_wins;
                            $home_wins = -9999;
                        }
                        if ($hta->home == 1) {
                            if ($home_home_wins >= 0) {
                                $ma->home_home_wins = $home_home_wins;
                                $home_home_wins = -9999;
                            }
                        }
                    } else {
                        if ($home_wins >= 0) {
                            $ma->home_wins = $home_wins;
                            $home_wins = -9999;
                        }
                        if ($hta->home == 1) {
                            if ($home_home_wins >= 0) {
                                $ma->home_home_wins = $home_home_wins;
                                $home_home_wins = -9999;
                            }
                        }
                        if ($home_not_loses >= 0) {
                            $ma->home_not_loses = $home_not_loses;
                            $home_not_loses = -9999;
                        }
                        if ($hta->home == 1) {
                            if ($home_home_not_loses >= 0) {
                                $ma->home_home_not_loses = $home_home_not_loses;
                                $home_home_not_loses = -9999;
                            }
                        }
                    }
                }
            }


            $atas = TeamAnalyse::query()
                ->where('tid', $match->aid)
                ->where('time', '>', date_create('-1 year'))
                ->orderBy('time', 'desc')
                ->get();
            if ($atas->count() > 9) {
                $away_wins = 0;
                $away_away_wins = 0;
                $away_not_loses = 0;
                $away_away_not_loses = 0;
                foreach ($atas as $ata) {
                    if ($ata->diff > 0) {
                        $away_wins++;
                        $away_not_loses++;
                        if ($ata->home == 0) {
                            $away_away_wins++;
                            $away_away_not_loses++;
                        }
                    } elseif ($ata->diff == 0) {
                        $away_not_loses++;
                        if ($ata->home == 1) {
                            $away_away_not_loses++;
                        }
                        if ($away_wins >= 0) {
                            $ma->away_wins = $away_wins;
                            $away_wins = -9999;
                        }
                        if ($ata->home == 0) {
                            if ($away_away_wins >= 0) {
                                $ma->away_away_wins = $away_away_wins;
                                $away_away_wins = -9999;
                            }
                        }
                    } else {
                        if ($away_wins >= 0) {
                            $ma->away_wins = $away_wins;
                            $away_wins = -9999;
                        }
                        if ($ata->home == 1) {
                            if ($away_away_wins >= 0) {
                                $ma->away_away_wins = $away_away_wins;
                                $away_away_wins = -9999;
                            }
                        }
                        if ($away_not_loses >= 0) {
                            $ma->away_not_loses = $away_not_loses;
                            $away_not_loses = -9999;
                        }
                        if ($ata->home == 1) {
                            if ($away_away_not_loses >= 0) {
                                $ma->away_away_not_loses = $away_away_not_loses;
                                $away_away_not_loses = -9999;
                            }
                        }
                    }
                }
            }

            //球队最近是否有比赛
            $homeLatelyDoneMatch = Match::query()
                ->where('time', '>', date('Y-m-d H:i:s', strtotime('-4 day', strtotime($match->time))))
                ->where('time', '<', $match->time)
                ->where(function ($query) use ($match) {
                    $query->where('hid', '=', $match->hid)
                        ->orWhere('aid', '=', $match->hid);
                })
                ->where('status', -1)
                ->orderBy('time', 'desc')
                ->first();
            if (isset($homeLatelyDoneMatch)) {
                $ma->home_lately_done_time = $homeLatelyDoneMatch->time;
                $ma->home_lately_done_mid = $homeLatelyDoneMatch->id;
            }
            $homeLatelyWaitMatch = Match::query()
                ->where('time', '<', date('Y-m-d H:i:s', strtotime('+4 day', strtotime($match->time))))
                ->where('time', '>', $match->time)
                ->where(function ($query) use ($match) {
                    $query->where('hid', '=', $match->hid)
                        ->orWhere('aid', '=', $match->hid);
                })
                ->where('status', 0)
                ->orderBy('time')
                ->first();
            if (isset($homeLatelyWaitMatch)) {
                $ma->home_lately_wait_time = $homeLatelyWaitMatch->time;
                $ma->home_lately_wait_mid = $homeLatelyWaitMatch->id;
            }
            $awayLatelyDoneMatch = Match::query()
                ->where('time', '>', date('Y-m-d H:i:s', strtotime('-4 day', strtotime($match->time))))
                ->where('time', '<', $match->time)
                ->where(function ($query) use ($match) {
                    $query->where('hid', '=', $match->aid)
                        ->orWhere('aid', '=', $match->aid);
                })
                ->where('status', -1)
                ->orderBy('time', 'desc')
                ->first();
            if (isset($awayLatelyDoneMatch)) {
                $ma->away_lately_done_time = $awayLatelyDoneMatch->time;
                $ma->away_lately_done_mid = $awayLatelyDoneMatch->id;
            }
            $awayLatelyWaitMatch = Match::query()
                ->where('time', '<', date('Y-m-d H:i:s', strtotime('+4 day', strtotime($match->time))))
                ->where('time', '>', $match->time)
                ->where(function ($query) use ($match) {
                    $query->where('hid', '=', $match->aid)
                        ->orWhere('aid', '=', $match->aid);
                })
                ->where('status', 0)
                ->orderBy('time')
                ->first();
            if (isset($awayLatelyWaitMatch)) {
                $ma->away_lately_wait_time = $awayLatelyWaitMatch->time;
                $ma->away_lately_wait_mid = $awayLatelyWaitMatch->id;
            }

            $ma->save();
        }
    }

}