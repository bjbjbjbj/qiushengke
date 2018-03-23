<?php
namespace App\Http\Controllers\Tip;
use App\Http\Controllers\FileTool;
use App\Http\Controllers\Statistic\StatisticFileTool;
use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\MatchData;
use App\Models\LiaoGouModels\MatchesAfter;
use App\Models\LiaoGouModels\MatchLineup;
use App\Models\LiaoGouModels\MatchLive;
use App\Models\LiaoGouModels\Referee;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/1
 * Time: 18:59
 */
class MatchTipController extends Controller
{

    public function index(Request $request, $action) {
        if (method_exists($this, $action)) {
            $this->$action($request);
        } else {
            echo "Error: Not Found action 'SpiderISportController->$action()'";
        }
    }

    /**
     * 定时任务：首发提点
     * @param Request $request
     */
    public function lineupsTip(Request $request) {
        $mid = $request->input('mid');
        if (isset($mid)) {
            $match = Match::query()->find($mid);
            $lineup = MatchLineup::query()->find($mid);
            //echo $match->id . ' ' . $match->hname . ' VS ' . $match->aname . ' <br/>';
            if (isset($lineup)) {
                $this->createLineupIipFile($lineup, $match->hname, $match->aname, $match->time);
            }
            echo "lineupsTip mid = $mid <br>";
            return;
        }
        $query = MatchLive::query();
        $query->join('matches_afters', 'matches_afters.id', '=', 'match_lives.match_id');
        $query->where('match_lives.sport', MatchLive::kSportFootball);

        $query->where('matches_afters.status', '>=', 0);//比赛未开始
        $query->where('matches_afters.status', '<=', 4);//比赛加时
        $query->select(['matches_afters.id', 'matches_afters.hname', 'matches_afters.aname', 'matches_afters.time']);

        //$query = Match::query()->where('id', 952504)->orWhere('id', 985639)->orWhere('id', 610637);
        $matches = $query->get();
        foreach ($matches as $match) {
            $lineup = MatchLineup::query()->find($match->id);
            //echo $match->id . ' ' . $match->hname . ' VS ' . $match->aname . ' <br/>';
            if (!isset($lineup)) {
                continue;
            }
            $this->createLineupIipFile($lineup, $match->hname, $match->aname, $match->time);
        }
    }

    /**
     * 半全场胜负、进球概率统计
     * @param Request $request
     */
    public function halfFullCourtTip(Request $request) {
        $mid = $request->input('mid');
        if (isset($mid)) {
            $match = Match::query()->find($mid);
            //生成半全场统计数据
            $this->createHalfFullCourtTip($match);
            return;
        }

        $query = MatchLive::query();
        $query->join('matches_afters', 'matches_afters.id', '=', 'match_lives.match_id');
        $query->where('match_lives.sport', MatchLive::kSportFootball);
        $query->where('matches_afters.status', '>=', 0);//比赛未开始
        $query->where('matches_afters.status', '<=', 2);//比赛中场或之前
        $query->select(['matches_afters.id', 'matches_afters.hname', 'matches_afters.aname', 'matches_afters.time', 'matches_afters.hid', 'matches_afters.aid']);

        //$query = Match::query()->where('id', 952504)->orWhere('id', 985639)->orWhere('id', 610637);
        $matches = $query->get();
        foreach ($matches as $match) {
            //生成半全场统计数据
            $this->createHalfFullCourtTip($match);
        }
    }


    /**
     * 如何判定一位球员是否主力，参考首发工具。
     * 条件A：本场比赛【主队名字】/【客队名字】共派出X名主力球员首发，该阵容场均进M球失N球；
     * 结论文案1：首发阵容有所保留。（当X1＜8）
     * 结论文案2：首发阵容正常无异样。（当8≤X1≤10）
     * 结论文案3：全主力出击。（当X1=11）
     * X1和X2为0到11之间的整数
     * N和M取小数点后2位
     * 前段文案：本场比赛对阵双方已公布11人首发名单，【主队名字】共派出X名主力球员首发，结论文案，该阵容场均进N球失M球；【客队名字】共派出X名主力球员首发，结论文案，该阵容场均进N球失M球。
     * @param MatchLineup $lineup 对阵
     * @param $h_name             主队名称
     * @param $a_name             客队名称
     * @param $time               比赛时间
     */
    protected function createLineupIipFile(MatchLineup $lineup, $h_name, $a_name, $time) {
        if (isset($lineup) && (!empty($lineup->h_lineup) || !empty($lineup->a_lineup))) {
            $h_main_count = empty($lineup->h_first) ? 0 : count(explode(',', $lineup->h_first));//主队主力数
            $a_main_count = empty($lineup->a_first) ? 0 : count(explode(',', $lineup->a_first));//客队主力数
            $h_avg_goal = isset($lineup->h_goal) ? round($lineup->h_goal, 2) : 0;//主队阵容平均进球数
            $a_avg_goal = isset($lineup->a_goal) ? round($lineup->a_goal, 2) : 0;//客队阵容平均进球数
            $h_avg_fumble = isset($lineup->h_against) ? round($lineup->h_against, 2) : 0;//主队阵容平均失球数
            $a_avg_fumble = isset($lineup->a_against) ? round($lineup->a_against, 2) : 0;//客队阵容平均失球数

            if ($h_main_count == 11) {//主队结论
                $h_conclusion = "全主力出击";
            } else if ($h_main_count >= 8) {
                $h_conclusion = "首发阵容正常无异样";
            } else {
                $h_conclusion = "首发阵容有所保留";
            }

            if ($a_main_count == 11) {//客队结论
                $a_conclusion = "全主力出击";
            } else if ($a_main_count >= 8) {
                $a_conclusion = "首发阵容正常无异样";
            } else {
                $a_conclusion = "首发阵容有所保留";
            }

            $conclusion = "";
            if ($h_main_count > 0 && $a_main_count > 0) {
                $conclusion = "本场比赛对阵双方已公布11人首发名单，";
                $conclusion .= "【" . $h_name . "】共派出" . $h_main_count . "名主力球员首发，" . $h_conclusion . "，";
                $conclusion .= "该阵容场均进" . $h_avg_goal . "球失" . $h_avg_fumble . "球；";

                $conclusion .= "【" . $a_name . "】共派出" . $a_main_count . "名主力球员首发，" . $a_conclusion . "，";
                $conclusion .= "该阵容场均进" . $a_avg_goal . "球失" . $a_avg_fumble . "球。";
            }
            echo "h_main_count = $h_main_count; a_main_count = $a_main_count".'; text ='. $conclusion . '<br/>';
            //新版即时提点
            $event_analyse = StatisticFileTool::getFileFromTerminal(MatchLive::kSportFootball, $lineup->id, 'event_analyse');
            if (empty($event_analyse)) {
                echo 'data is empty!! <br>';
                $event_analyse = ['lineup'=>$conclusion];
            } else {
                $event_analyse['lineup'] = $conclusion;
            }
            StatisticFileTool::putFileToTerminal($event_analyse, MatchLive::kSportFootball, $lineup->id, 'event_analyse');
        }
    }

    /**
     * 生成半全场结论
     * @param $base_match  比赛实体
     */
    protected function createHalfFullCourtTip($base_match) {
        $tips = $this->createHalfFullCourtTipByTid($base_match);

        //新版提点数据
        $event_analyse = StatisticFileTool::getFileFromTerminal(MatchLive::kSportFootball, $base_match->id, 'event_analyse');
        if (!isset($event_analyse)) {
            $event_analyse = ['half_full_data'=>$tips];
        } else {
            $event_analyse['half_full_data'] = $tips;
        }
        StatisticFileTool::putFileToTerminal($event_analyse, MatchLive::kSportFootball, $base_match->id, 'event_analyse');
        //3.生成文件 结束
    }

    private function createHalfFullCourtTipByTid($base_match) {
        //双方对阵的历史数据 50条
        $hid = $base_match->hid;
        $aid = $base_match->aid;
        $homeName = $base_match->hname;
        $awayName = $base_match->aname;

        $homeMatches = Match::query()->where('status', -1)->where(function ($q) use($hid) {
            $q->where('hid', $hid)->orWhere('aid', $hid);
        })->orderBy('time', 'desc')->take(50)->get();

        $awayMatches = Match::query()->where('status', -1)->where(function ($q) use($aid) {
            $q->where('hid', $aid)->orWhere('aid', $aid);
        })->orderBy('time', 'desc')->take(50)->get();

        $h_match_count = count($homeMatches);//比赛总场数
        $a_match_count = count($awayMatches);//比赛总场数

        $h_half_win_count = 0; $a_half_win_count = 0;
        $h_half_win_win = 0; $a_half_win_win = 0;
        $h_half_win_draw = 0; $a_half_win_draw = 0;
        $h_half_win_lose = 0; $a_half_win_lose = 0;

        $h_half_draw_count = 0; $a_half_draw_count = 0;
        $h_half_draw_win = 0; $a_half_draw_win = 0;
        $h_half_draw_draw = 0; $a_half_draw_draw = 0;
        $h_half_draw_lose = 0; $a_half_draw_lose = 0;

        $h_half_lose_count = 0; $a_half_lose_count = 0;
        $h_half_lose_win = 0; $a_half_lose_win = 0;
        $h_half_lose_draw = 0; $a_half_lose_draw = 0;
        $h_half_lose_lose = 0; $a_half_lose_lose = 0;

        $goal_array = ["half_0"=>[] , "half_1"=>[], "half_2"=>[], "half_3"=>[], "half_4"=>[], "half_5"=>[], "half_6"=>[], "half_7"=>[], "half_8"=>[]];
        //A：半场胜-全场胜、半场胜-全场平、半场胜-全场负
        //B：半场平-全场胜、半场平-全场平、半场平-全场负
        //C：半场负-全场胜、半场负-全场平、半场负-全场胜
        /*
         * {
         *  half_win:{win: , draw:, lose:, conclusion:},
         *  half_draw:{win: , draw:, lose:, conclusion:},
         *  half_lose:{win: , draw:, lose:, conclusion:},
         *  goal_count:{0: {},1: {},2: {},3+: {}}
         * }
         */
        foreach ($homeMatches as $match) {
            $isHid = $match->hid == $hid;
            $h_h_half_score = $match->hscorehalf;
            $h_a_half_score = $match->ascorehalf;
            $h_h_score = $match->hscore;
            $h_a_score = $match->ascore;

            if ($isHid) {
                if ($h_h_half_score > $h_a_half_score) {
                    $h_half_win_count++;
                    if ($h_h_score > $h_a_score) {
                        $h_half_win_win++;
                    } else if ($h_h_score == $h_a_score) {
                        $h_half_win_draw++;
                    } else {
                        $h_half_win_lose++;
                    }
                } else if ($h_h_half_score == $h_a_half_score) {
                    $h_half_draw_count++;
                    if ($h_h_score > $h_a_score) {
                        $h_half_draw_win++;
                    } else if ($h_h_score == $h_a_score) {
                        $h_half_draw_draw++;
                    } else {
                        $h_half_draw_lose++;
                    }
                } else {
                    $h_half_lose_count++;
                    if ($h_h_score > $h_a_score) {
                        $h_half_lose_win++;
                    } else if ($h_h_score == $h_a_score) {
                        $h_half_lose_draw++;
                    } else {
                        $h_half_lose_lose++;
                    }
                }
            } else {
                if ($h_h_half_score < $h_a_half_score) {
                    $h_half_win_count++;
                    if ($h_h_score < $h_a_score) {
                        $h_half_win_win++;
                    } else if ($h_h_score == $h_a_score) {
                        $h_half_win_draw++;
                    } else {
                        $h_half_win_lose++;
                    }
                } else if ($h_h_half_score == $h_a_half_score) {
                    $h_half_draw_count++;
                    if ($h_h_score < $h_a_score) {
                        $h_half_draw_win++;
                    } else if ($h_h_score == $h_a_score) {
                        $h_half_draw_draw++;
                    } else {
                        $h_half_draw_lose++;
                    }
                } else {
                    $h_half_lose_count++;
                    if ($h_h_score < $h_a_score) {
                        $h_half_lose_win++;
                    } else if ($h_h_score == $h_a_score) {
                        $h_half_lose_draw++;
                    } else {
                        $h_half_lose_lose++;
                    }
                }
            }

            $half_goal_total = $h_h_half_score + $h_a_half_score;
            $goal_total = $h_h_score + $h_a_score;
            $goal_array = $this->halfFullCourtGoal($goal_array, 'half_' . $half_goal_total, $goal_total, true);
        }

        foreach ($awayMatches as $match) {
            $isHid = $match->hid == $aid;
            $a_h_half_score = $match->hscorehalf;
            $a_a_half_score = $match->ascorehalf;
            $a_h_score = $match->hscore;
            $a_a_score = $match->ascore;

            if ($isHid) {
                if ($a_h_half_score > $a_a_half_score) {
                    $a_half_win_count++;
                    if ($a_h_score > $a_a_score) {
                        $a_half_win_win++;
                    } else if ($a_h_score == $a_a_score) {
                        $a_half_win_draw++;
                    } else {
                        $a_half_win_lose++;
                    }
                } else if ($a_h_half_score == $a_a_half_score) {
                    $a_half_draw_count++;
                    if ($a_h_score > $a_a_score) {
                        $a_half_draw_win++;
                    } else if ($a_h_score == $a_a_score) {
                        $a_half_draw_draw++;
                    } else {
                        $a_half_draw_lose++;
                    }
                } else {
                    $a_half_lose_count++;
                    if ($a_h_score > $a_a_score) {
                        $a_half_lose_win++;
                    } else if ($a_h_score == $a_a_score) {
                        $a_half_lose_draw++;
                    } else {
                        $a_half_lose_lose++;
                    }
                }
            } else {
                if ($a_h_half_score < $a_a_half_score) {
                    $a_half_win_count++;
                    if ($a_h_score < $a_a_score) {
                        $a_half_win_win++;
                    } else if ($a_h_score == $a_a_score) {
                        $a_half_win_draw++;
                    } else {
                        $a_half_win_lose++;
                    }
                } else if ($a_h_half_score == $a_a_half_score) {
                    $a_half_draw_count++;
                    if ($a_h_score < $a_a_score) {
                        $a_half_draw_win++;
                    } else if ($a_h_score == $a_a_score) {
                        $a_half_draw_draw++;
                    } else {
                        $a_half_draw_lose++;
                    }
                } else {
                    $a_half_lose_count++;
                    if ($a_h_score < $a_a_score) {
                        $a_half_lose_win++;
                    } else if ($a_h_score == $a_a_score) {
                        $a_half_lose_draw++;
                    } else {
                        $a_half_lose_lose++;
                    }
                }
            }

            $half_goal_total = $a_h_half_score + $a_a_half_score;
            $goal_total = $a_h_score + $a_a_score;
            $goal_array = $this->halfFullCourtGoal($goal_array, 'half_' . $half_goal_total, $goal_total, false);
        }

        $enter = "<br>";

        //生成结论
        //1.胜负结论 开始
        $result_array = ['half_win'=>[], 'half_draw'=>[], 'half_lose'=>[]];
        //半场胜开始
        $h_half_win_full_win_percent = $h_half_win_count == 0 ? 0 : round($h_half_win_win / $h_half_win_count, 4) * 100;
        $h_half_win_full_draw_percent = $h_half_win_count == 0 ? 0 : round($h_half_win_draw / $h_half_win_count, 4) * 100;
        $h_half_win_full_lose_percent = $h_half_win_count == 0 ? 0 : round($h_half_win_lose / $h_half_win_count, 4) * 100;
        $a_half_lose_full_win_percent = $a_half_lose_count == 0 ? 0 : round($a_half_lose_win / $a_half_lose_count, 4) * 100;
        $a_half_lose_full_draw_percent = $a_half_lose_count == 0 ? 0 : round($a_half_lose_draw / $a_half_lose_count, 4) * 100;
        $a_half_lose_full_lose_percent = $a_half_lose_count == 0 ? 0 : round($a_half_lose_lose / $a_half_lose_count, 4) * 100;

        $half_win_result = "【".$homeName."】近" . $h_match_count . "场比赛，半场领先对手共出现" . $h_half_win_count . "次";
        if ($h_half_win_count > 0) {
            $half_win_result .= "，最终【".$homeName."】全场胜场次为（" . $h_half_win_win . "场，" . $h_half_win_full_win_percent . "%）；";
            $half_win_result .= "全场平场次为（" . $h_half_win_draw . "场，" . $h_half_win_full_draw_percent . "%）；";
            $half_win_result .= "全场负场次为（" . $h_half_win_lose . "场，" . $h_half_win_full_lose_percent . "%）。$enter";
        } else {
            $half_win_result .= "。$enter";
        }
        $half_win_result .= "【".$awayName."】近" . $a_match_count . "场比赛，半场落后对手共出现" . $a_half_lose_count . "次";
        if ($a_half_lose_count > 0) {
            $half_win_result .= "，最终【".$awayName."】全场胜场次为（" . $a_half_lose_win . "场，" . $a_half_lose_full_win_percent . "%）；";
            $half_win_result .= "全场平场次为（" . $a_half_lose_draw . "场，" . $a_half_lose_full_draw_percent . "%）；";
            $half_win_result .= "全场负场次为（" . $a_half_lose_lose . "场，" . $a_half_lose_full_lose_percent . "%）。";
        } else {
            $half_win_result .= "。";
        }

        $result_array['half_win']['count'] = [$h_half_win_count,$a_half_lose_count];
        $result_array['half_win']['full_win'] = [$h_half_win_win,$a_half_lose_win];
        $result_array['half_win']['full_draw'] = [$h_half_win_draw,$a_half_lose_draw];
        $result_array['half_win']['full_lose'] = [$h_half_win_lose,$a_half_lose_lose];
        $result_array['half_win']['result'] = $half_win_result;//结论
        //半场胜结束

        //半场平开始
        $h_half_draw_full_win_percent = $h_half_draw_count == 0 ? 0 : round($h_half_draw_win / $h_half_draw_count, 4) * 100;
        $h_half_draw_full_draw_percent = $h_half_draw_count == 0 ? 0 : round($h_half_draw_draw / $h_half_draw_count, 4) * 100;
        $h_half_draw_full_lose_percent = $h_half_draw_count == 0 ? 0 : round($h_half_draw_lose / $h_half_draw_count, 4) * 100;
        $a_half_draw_full_win_percent = $a_half_draw_count == 0 ? 0 : round($a_half_draw_win / $a_half_draw_count, 4) * 100;
        $a_half_draw_full_draw_percent = $a_half_draw_count == 0 ? 0 : round($a_half_draw_draw / $a_half_draw_count, 4) * 100;
        $a_half_draw_full_lose_percent = $a_half_draw_count == 0 ? 0 : round($a_half_draw_lose / $a_half_draw_count, 4) * 100;

        $half_draw_result = "【".$homeName."】近" . $h_match_count . "场比赛，半场暂时难分胜负共出现" . $h_half_draw_count . "次";
        if ($h_half_draw_count > 0) {
            $half_draw_result .= "，最终【".$homeName."】全场胜场次为（" . $h_half_draw_win . "场，" . $h_half_draw_full_win_percent . "%）；";
            $half_draw_result .= "全场平场次为（" . $h_half_draw_draw . "场，" . $h_half_draw_full_draw_percent . "%）；";
            $half_draw_result .= "全场负场次为（" . $h_half_draw_lose . "场，" . $h_half_draw_full_lose_percent . "%）。$enter";
        } else {
            $half_draw_result .= "。$enter";
        }
        $half_draw_result .= "【".$awayName."】近" . $a_match_count . "场比赛，半场暂时难分胜负共出现" . $a_half_draw_count . "次";
        if ($a_half_draw_count > 0) {
            $half_draw_result .= "，最终【".$awayName."】全场胜场次为（" . $a_half_draw_win . "场，" . $a_half_draw_full_win_percent . "%）；";
            $half_draw_result .= "全场平场次为（" . $a_half_draw_draw . "场，" . $a_half_draw_full_draw_percent . "%）；";
            $half_draw_result .= "全场负场次为（" . $a_half_draw_lose . "场，" . $a_half_draw_full_lose_percent . "%）。";
        } else {
            $half_draw_result .= "。";
        }

        $result_array['half_draw']['count'] = [$h_half_draw_count,$a_half_draw_count];
        $result_array['half_draw']['full_win'] = [$h_half_draw_win,$a_half_draw_win];
        $result_array['half_draw']['full_draw'] = [$h_half_draw_draw,$a_half_draw_draw];
        $result_array['half_draw']['full_lose'] = [$h_half_draw_lose,$a_half_draw_lose];
        $result_array['half_draw']['result'] = $half_draw_result;//结论
        //半场平结束

        //半场负开始
        $h_half_lose_full_win_percent = $h_half_lose_count == 0 ? 0 : round($h_half_lose_win / $h_half_lose_count, 4) * 100;
        $h_half_lose_full_draw_percent = $h_half_lose_count == 0 ? 0 : round($h_half_lose_draw / $h_half_lose_count, 4) * 100;
        $h_half_lose_full_lose_percent = $h_half_lose_count == 0 ? 0 : round($h_half_lose_lose / $h_half_lose_count, 4) * 100;
        $a_half_win_full_win_percent = $a_half_win_count == 0 ? 0 : round($a_half_win_win / $a_half_win_count, 4) * 100;
        $a_half_win_full_draw_percent = $a_half_win_count == 0 ? 0 : round($a_half_win_draw / $a_half_win_count, 4) * 100;
        $a_half_win_full_lose_percent = $a_half_win_count == 0 ? 0 : round($a_half_win_lose / $a_half_win_count, 4) * 100;

        $half_lose_result = "【".$homeName."】近" . $h_match_count . "场比赛，半场落后对手共出现" . $h_half_lose_count . "次";
        if ($h_half_lose_count > 0) {
            $half_lose_result .= "，最终【".$homeName."】全场胜场次为（" . $h_half_lose_win . "场，" . $h_half_lose_full_win_percent . "%）；";
            $half_lose_result .= "全场平场次为（" . $h_half_lose_draw . "场，" . $h_half_lose_full_draw_percent . "%）；";
            $half_lose_result .= "全场负场次为（" . $h_half_lose_lose . "场，" . $h_half_lose_full_lose_percent . "%）。$enter";
        } else {
            $half_lose_result .= "。$enter";
        }
        $half_lose_result .= "【".$awayName."】近" . $a_match_count . "场比赛，半场领先对手共出现" . $a_half_win_count . "次";
        if ($a_half_win_count > 0) {
            $half_lose_result .= "，最终【".$awayName."】全场胜场次为（" . $a_half_win_win . "场，" . $a_half_win_full_win_percent . "%）；";
            $half_lose_result .= "全场平场次为（" . $a_half_win_draw . "场，" . $a_half_win_full_draw_percent . "%）；";
            $half_lose_result .= "全场负场次为（" . $a_half_win_lose . "场，" . $a_half_win_full_lose_percent . "%）。";
        } else {
            $half_lose_result .= "。";
        }

        $result_array['half_lose']['count'] = [$h_half_lose_count,$a_half_win_count];
        $result_array['half_lose']['full_win'] = [$h_half_lose_win,$a_half_win_win];
        $result_array['half_lose']['full_draw'] = [$h_half_lose_draw,$a_half_win_draw];
        $result_array['half_lose']['full_lose'] = [$h_half_lose_lose,$a_half_win_lose];
        $result_array['half_lose']['result'] = $half_lose_result;//结论
        //半场负结束
        //1.胜负结论 结束

        //2.半场进球数 开始
        /**
         * 上半场比赛结束，目前两队总进球数为N。回顾两队近100场比赛，半场总进球为N共出现XX次，其中【主队】所参加的比赛最终全场总进球数 =N球（百分比%）、≥N+1球（百分比%）、≥N+2球（百分比%）、≥N+3球（百分比%）、≥N+4球（百分比%）；其中【客队名字】所参加的比赛最终全场总进球数 N球（百分比%）、≥N+1球（百分比%）、≥N+2球（百分比%）、≥N+3球（百分比%）、≥N+4球（百分比%）。
         */

        $goal_result_array = [];
        for ($ind = 0; $ind < 8; $ind++) {
            $half_goal_total = $ind;//半场总进球数
            $h_half_goal_count = isset($goal_array['half_' . $ind]["count"]['home']) ? $goal_array['half_' . $ind]["count"]['home'] : 0;
            $a_half_goal_count = isset($goal_array['half_' . $ind]["count"]['away']) ? $goal_array['half_' . $ind]["count"]['away'] : 0;

            $h_full_goal_n = isset($goal_array['half_' . $ind]["full_" . $ind]['home']) ? $goal_array['half_' . $ind]["full_" . $ind]['home'] : 0;//全场n进球
            $h_full_goal_n1 = isset($goal_array['half_' . $ind]["full_" . ($ind + 1)]['home']) ? $goal_array['half_' . $ind]["full_" . ($ind + 1)]['home'] : 0;//全场n+1进球
            $h_full_goal_n2 = isset($goal_array['half_' . $ind]["full_" . ($ind + 2)]['home']) ? $goal_array['half_' . $ind]["full_" . ($ind + 2)]['home'] : 0;//全场n+2进球
            $h_full_goal_n3 = isset($goal_array['half_' . $ind]["full_" . ($ind + 3)]['home']) ? $goal_array['half_' . $ind]["full_" . ($ind + 3)]['home'] : 0;//全场n+3进球
            $h_full_goal_n4 = isset($goal_array['half_' . $ind]["full_" . ($ind + 4)]['home']) ? $goal_array['half_' . $ind]["full_" . ($ind + 4)]['home'] : 0;//全场n+4进球
            $a_full_goal_n = isset($goal_array['half_' . $ind]["full_" . $ind]['away']) ? $goal_array['half_' . $ind]["full_" . $ind]['away'] : 0;//全场n进球
            $a_full_goal_n1 = isset($goal_array['half_' . $ind]["full_" . ($ind + 1)]['away']) ? $goal_array['half_' . $ind]["full_" . ($ind + 1)]['away'] : 0;//全场n+1进球
            $a_full_goal_n2 = isset($goal_array['half_' . $ind]["full_" . ($ind + 2)]['away']) ? $goal_array['half_' . $ind]["full_" . ($ind + 2)]['away'] : 0;//全场n+2进球
            $a_full_goal_n3 = isset($goal_array['half_' . $ind]["full_" . ($ind + 3)]['away']) ? $goal_array['half_' . $ind]["full_" . ($ind + 3)]['away'] : 0;//全场n+3进球
            $a_full_goal_n4 = isset($goal_array['half_' . $ind]["full_" . ($ind + 4)]['away']) ? $goal_array['half_' . $ind]["full_" . ($ind + 4)]['away'] : 0;//全场n+4进球

            $h_n_percent = $h_half_goal_count == 0 ? 0: round($h_full_goal_n / $h_half_goal_count, 4) * 100;
            $h_n1_percent = $h_half_goal_count == 0 ? 0: round($h_full_goal_n1 / $h_half_goal_count, 4) * 100;//>= n + 1球的百分比
            $h_n2_percent = $h_half_goal_count == 0 ? 0: round($h_full_goal_n2 / $h_half_goal_count, 4) * 100;//>= n + 2球的百分比
            $h_n3_percent = $h_half_goal_count == 0 ? 0: round($h_full_goal_n3 / $h_half_goal_count, 4) * 100;//>= n + 3球的百分比
            $h_n4_percent = $h_half_goal_count == 0 ? 0: round($h_full_goal_n4 / $h_half_goal_count, 4) * 100;//>= n + 4球的百分比
            $a_n_percent = $a_half_goal_count == 0 ? 0: round($a_full_goal_n / $a_half_goal_count, 4) * 100;
            $a_n1_percent = $a_half_goal_count == 0 ? 0: round($a_full_goal_n1 / $a_half_goal_count, 4) * 100;//>= n + 1球的百分比
            $a_n2_percent = $a_half_goal_count == 0 ? 0: round($a_full_goal_n2 / $a_half_goal_count, 4) * 100;//>= n + 2球的百分比
            $a_n3_percent = $a_half_goal_count == 0 ? 0: round($a_full_goal_n3 / $a_half_goal_count, 4) * 100;//>= n + 3球的百分比
            $a_n4_percent = $a_half_goal_count == 0 ? 0: round($a_full_goal_n4 / $a_half_goal_count, 4) * 100;//>= n + 4球的百分比

            $goal_result = "上半场比赛结束，目前两队总进球数为".$ind."。$enter";
            $goal_result .= "回顾【".$homeName."】近" . $h_match_count . "场比赛，半场总进球为" . $half_goal_total . "共出现" . $h_half_goal_count . "次";
            if ($h_half_goal_count > 0) {
                $goal_result .= "，最终全场总进球数" . $ind . "球（".$h_full_goal_n."场，" . $h_n_percent . "%）、";
                $goal_result .= ($ind + 1) . "球（".$h_full_goal_n1."场，" . $h_n1_percent . "%）、";
                $goal_result .= ($ind + 2) . "球（".$h_full_goal_n2."场，" . $h_n2_percent . "%）、";
                $goal_result .= ($ind + 3) . "球（".$h_full_goal_n3."场，" . $h_n3_percent . "%）、";
                $goal_result .= ($ind + 4) . "球（".$h_full_goal_n4."场，" . $h_n4_percent . "%）。$enter";
            } else {
                $goal_result .= "。$enter";
            }
            $goal_result .= "回顾【".$awayName."】近" . $a_match_count . "场比赛，半场总进球为" . $half_goal_total . "共出现" . $a_half_goal_count . "次";
            if ($a_half_goal_count > 0) {
                $goal_result .= "，最终全场总进球数" . $ind . "球（".$a_full_goal_n."场，" . $a_n_percent . "%）、";
                $goal_result .= ($ind + 1) . "球（".$a_full_goal_n1."场，" . $a_n1_percent . "%）、";
                $goal_result .= ($ind + 2) . "球（".$a_full_goal_n2."场，" . $a_n2_percent . "%）、";
                $goal_result .= ($ind + 3) . "球（".$a_full_goal_n3."场，" . $a_n3_percent . "%）、";
                $goal_result .= ($ind + 4) . "球（".$a_full_goal_n4."场，" . $a_n4_percent . "%）。";
            } else {
                $goal_result .= "。";
            }

            $goal_result_array["half_" . $ind]["count"] = [$h_half_goal_count, $a_half_goal_count];
            $goal_result_array["half_" . $ind]["full_" . $ind] = [$h_full_goal_n,$a_full_goal_n];
            $goal_result_array["half_" . $ind]["full_" . ($ind + 1)] = [$h_full_goal_n1,$a_full_goal_n1];
            $goal_result_array["half_" . $ind]["full_" . ($ind + 2)] = [$h_full_goal_n2,$a_full_goal_n2];
            $goal_result_array["half_" . $ind]["full_" . ($ind + 3)] = [$h_full_goal_n3,$a_full_goal_n3];
            $goal_result_array["half_" . $ind]["full_" . ($ind + 4)] = [$h_full_goal_n4,$a_full_goal_n4];
            $goal_result_array["half_" . $ind]["result"] = $goal_result;
        }
        //2.半场进球数 结束

        return ["win_lose_data"=>$result_array, "goal_data"=>$goal_result_array];
    }

    /**
     * 半场、全场进球总数 基础数据
     * @param array $goal_array
     * @param $key
     * @param $goal
     * @return array
     */
    protected function halfFullCourtGoal(array $goal_array, $key, $goal, $isHome) {
        $index = $isHome ? 'home' : 'away';
        if (isset($goal_array[$key]["count"][$index])) {
            $goal_array[$key]["count"][$index] += 1;
        } else {
            $goal_array[$key]["count"][$index] = 1;
        }

        $full_key = "full_$goal";
        if (isset($goal_array[$key][$full_key][$index])) {
            $goal_array[$key][$full_key][$index] += 1;
        } else {
            $goal_array[$key][$full_key][$index] = 1;
        }
        return $goal_array;
    }



    //============================裁判数据算法========================//

    /**
     * 定时任务：裁判提点
     * @param Request $request
     */
    public function refereeTip(Request $request) {
        $mid = $request->input('mid');
        if (isset($mid)) {
            $this->createRefereeTipFile($mid);
            return;
        }

        $key = "match_referee_tip_mids";
        $value = Redis::get($key);
        $redisMids = array();
        if (isset($value)) {
            $redisMids = array_merge($redisMids, json_decode($value));
        }

        $mids = array();
        $default_count = $request->input('count', 5);

        if (count($redisMids) <= 0) {
            $startData = date('Y-m-d H:i');
            $endData = date('Y-m-d H:i', strtotime('+24 hour'));

            $matches = DB::connection('liaogou_match')->select("
        select m.id from
        (select * from matches_afters where status = 0 and time > '$startData' and time <= '$endData') as m
        join match_lives as live on live.match_id = m.id and live.sport = ".MatchLive::kSportFootball."
        left join match_datas as md on md.id = m.id
        where md.referee_id > 0 order by m.time;");

            foreach ($matches as $match) {
                array_push($mids, $match->id);
            }
            Redis::setEx($key, 24 * 60 * 60, json_encode($mids));
            $redisMids = array_merge($redisMids, $mids);
        }
        $mids = array_slice($redisMids, 0, $default_count);
        echo 'mids count1 = ' . count($redisMids).'<br>';

        foreach ($mids as $mid) {
            $this->createRefereeTipFile($mid);
        }
        //删除redis中将要爬取的lid
        $redisMids = array_slice($redisMids, count($mids));
        Redis::set($key, json_encode($redisMids));

        echo 'mids count2 = ' . count($redisMids).'<br>';
    }

    /**
     * 裁判数据统一算法，填充到json文件中
     */
    public function createRefereeTipFile($mid) {
        $match = Match::query()->find($mid);
        if (is_null($match) || is_null($match->hid) || is_null($match->aid)) return;

        $matchData = MatchData::query()->find($mid);
        if (is_null($matchData) || is_null($matchData->referee_id)) return;

        $referee = Referee::query()->find($matchData->referee_id);
        if (is_null($referee)) return;

        $yellowForecast = $this->getRefereeYellowForecast($match, $referee);
        $WDLForecast = $this->getRefereeWDLForecast($match, $referee);
        $refereeArray = [];
        if (count($yellowForecast) > 0) {
            $refereeArray['yellow'] = $yellowForecast;
        }
        if (count($WDLForecast) > 0) {
            $refereeArray['wdl'] = $WDLForecast;
        }
        if (count($refereeArray) > 0) {

            //新版即时提点部分
            $event_analyse = StatisticFileTool::getFileFromTerminal(MatchLive::kSportFootball, $mid, 'event_analyse');
            if (empty($event_analyse)) {
                $event_analyse = ["referee" => $refereeArray];
            } else {
                $event_analyse["referee"] = $refereeArray;
            }
            StatisticFileTool::putFileToTerminal($event_analyse, MatchLive::kSportFootball, $mid, 'event_analyse');

            echo "createRefereeTipFile mid = $mid success<br>";
        } else {
            echo "createRefereeTipFile mid = $mid is null<br>";
        }
    }

    /**
     * 黄牌数预计
     */
    protected function getRefereeYellowForecast($match, $referee) {
        if (is_null($referee) || is_null($match)) return [];

        //裁判最近执法30场内的场均黄牌输
        //如果裁判最近执法小于10场则不再进行计算
        $count = $referee->recent_count;
        if ($count < 10) return [];
        $ref_yellow_avg = $referee->recent_h_yellow_avg + $referee->recent_a_yellow_avg;

        //主队近1年的比赛场均黄牌数
        $hid = $match->hid;
        $h_yellow_avg = $this->getTeamYellowAvg($hid);

        //客队近1年的比赛场均黄牌数
        $aid = $match->aid;
        $a_yellow_avg = $this->getTeamYellowAvg($aid);

        $yellowForecast = ['referee'=>$referee->name, 'h_name'=>$match->hname, 'a_name'=>$match->aname,
            'ref_yellow_avg'=>$ref_yellow_avg, 'h_yellow_avg'=>$h_yellow_avg, 'a_yellow_avg'=>$a_yellow_avg];

        return $yellowForecast;
    }

    //获取球队近1年的比赛场均黄牌数
    private function getTeamYellowAvg($tid) {
        $yellow_count = 0;
        $match_count = 0;
        $matches = Match::query()->where(function ($q) use($tid){
            $q->where('hid', $tid)->orWhere('aid', $tid);
        })->where('time', '>=', date('Y-m-d H:i', strtotime('-1 year')))
            ->where('status', -1)->orderBy('time', 'desc')->get();
        foreach ($matches as $tempMatch){
            $md = $tempMatch->matchData;
            if (isset($md) && ($md->h_yellow >= 0 && $md->a_yellow >= 0)) {
                $yellow_count += ($md->h_yellow + $md->a_yellow);
                $match_count++;
            }
        }
        return $match_count == 0 ? 0 : round($yellow_count/$match_count, 2);
    }

    /**
     * 裁判相关 胜平负倾向预计计算
     */
    protected function getRefereeWDLForecast($match, $referee) {
        if (is_null($referee) || is_null($match)) return [];

        //裁判最近执法30场内的胜率
        $count = $referee->recent_count;
        //如果裁判最近执法小于10场，则不再进行计算
        if ($count < 10) return [];

        $win = $referee->recent_win;
        $draw = $referee->recent_draw;
        $lose = $referee->recent_lose;

        $winPercent = $count == 0 ? 0 : round($win*100/$count, 2);
        $drawPercent = $count == 0 ? 0 : round($draw*100/$count, 2);
        $losePercent = $count == 0 ? 0 : round($lose*100/$count, 2);

        //裁判执法主队比赛
        $h_win_array = $this->getRefereeTeamWinPercent($match->time, $match->hid, $referee->id);
        //裁判执法客队比赛
        $a_win_array = $this->getRefereeTeamWinPercent($match->time, $match->aid, $referee->id);
        if (count($h_win_array) != 2 || count($a_win_array) != 2) {
            return [];
        }
        $refereeWDLForecast = ['referee'=>$referee->name, 'h_name'=>$match->hname, 'a_name'=>$match->aname,
            'win_p'=>$winPercent, 'draw_p'=>$drawPercent, 'lose_p'=>$losePercent, 'recent_count'=>$referee->recent_count,
            'h_win_count'=>$h_win_array[0], 'h_count'=>$h_win_array[1],
            'a_win_count'=>$a_win_array[0], 'a_count'=>$a_win_array[1]];

        return $refereeWDLForecast;
    }

    //裁判执法球队的比赛
    private function getRefereeTeamWinPercent($matchTime, $tid, $referee_id) {
        $matches = DB::connection('liaogou_match')->select("
        select m.*, md.referee_id from
        (select id, referee_id from match_datas where referee_id = $referee_id) as md
        left join matches as m on md.id = m.id
        where (m.hid = $tid or m.aid = $tid) and m.status = -1 and m.time < '$matchTime'
        order by time desc;");

        $count = count($matches);
        if ($count < 3) {
            return [];
        } else {
            $winCount = 0;
            foreach ($matches as $match) {
                $diff = $match->hscore - $match->ascore;
                if ($match->hid = $tid && $diff > 0){
                    $winCount++;
                } else if ($match->aid == $tid && $diff < 0) {
                    $winCount++;
                }
            }
            return [$winCount,$count];
        }
    }
}