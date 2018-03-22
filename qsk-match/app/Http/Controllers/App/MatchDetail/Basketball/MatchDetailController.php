<?php
namespace App\Http\Controllers\App\MatchDetail\Basketball;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FileTool;
use App\Models\LiaoGouModels\BasketMatch;
use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\MatchAnalysisSameOdd;
use App\Models\LiaoGouModels\MatchEvent;
use App\Models\LiaoGouModels\MatchLineup;
use App\Models\LiaoGouModels\MatchLive;
use App\Models\LiaoGouModels\MatchLiveChannel;
use App\Models\LiaoGouModels\MatchPlayer;
use App\Models\LiaoGouModels\Odd;
use App\Models\LiaoGouModels\Score;
use App\Models\LiaoGouModels\Team;

class MatchDetailController extends Controller {

    public function matchDetailJson($id) {
        $match = BasketMatch::where('basket_matches.id', '=', $id)
            ->leftjoin('basket_leagues', 'lid', '=', 'basket_leagues.id')
            ->select('basket_matches.*', 'basket_matches.id as id', 'basket_leagues.name as lname'
                )
            ->first();
        if (is_null($match)){
            return [];
        }
        //是否有直播 开始
        $live = MatchLive::query()->where('sport', MatchLive::kSportBasketball)
            ->where('match_id', $id)
            ->first();
        if (isset($live)) {
            $channels = MatchLive::liveChannels($live->id);
            foreach ($channels as $channel) {
                $platform = $channel->platform;
                if ($platform == MatchLiveChannel::kPlatformAll) {
                    $match->pc_live = true;
                    $match->wap_live = true;
                } else if ($platform == MatchLiveChannel::kPlatformPC) {
                    $match->pc_live = true;
                } else if ($platform == MatchLiveChannel::kPlatformWAP) {
                    $match->wap_live = true;
                }
            }
        } else {
            $match->pc_live = false;
            $match->wap_live = false;
        }
        //是否有直播 结束

        //时间
        $match->current_time = $match->time;

        if (isset($match->hid)) {
            $team = Team::where('id', '=', $match->hid)->first();
            $match['hteam'] = $team;
        }
        if (isset($match->aid)) {
            $team = Team::where('id', '=', $match->aid)->first();
            $match['ateam'] = $team;
        }

        //基本信息
        $matchBase = new MatchDetailBaseController();
        $base = $matchBase->matchDetailBaseData($id);
        $json = [];
        $json['base'] = $base;
        $json['match'] = $match;
        return $json;
    }

    protected function dataPercent($match, $hkey, $akey) {
        if (!isset($match) || empty($hkey) || empty($akey) || !isset($match[$hkey]) || !isset($match[$akey])
            || ($match[$hkey] + $match[$akey]) == 0
        ) {
            return 0;
        }
        return $match[$hkey] / ($match[$hkey] + $match[$akey]);
    }
}