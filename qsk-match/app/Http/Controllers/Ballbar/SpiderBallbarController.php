<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2017/11/29
 * Time: 下午3:16
 */

namespace App\Http\Controllers\Ballbar;

use App\Http\Controllers\TTZB\SpiderTTZBController;
use App\Models\LiaoGouModels\BasketMatchesAfter;
use App\Models\LiaoGouModels\LiaogouAlias;
use App\Models\LiaoGouModels\LiveAlias;
use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\MatchesAfter;
use App\Models\LiaoGouModels\MatchLive;
use App\Models\LiaoGouModels\MatchLiveChannel;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use QL\QueryList;

class SpiderBallbarController extends Controller{


    public function index($action, Request $request)
    {
        if (method_exists($this, $action)) {
            $this->$action($request);
        } else {
            echo "Error: Not Found action 'SpiderISportController->$action()'";
        }
    }

    /**
     * 爬直播
     */
    public function spiderLiveData() {
        $ql = QueryList::get('https://www.ballbar.cc/class/soccer');
        $divs = $ql->find('div#righteventbox');
        $spans = $ql->find('span.dateHeader');
        $dates = $spans->map(function ($span){
            $classString = $span->class;
            $classString = str_replace('dateHeader date','',$classString);
            return $classString;
        });
        //时间
        $times = array();
        foreach ($dates as $date){
            $times[] = $date;
        }
        $dates = array();
        //比赛列表
        for ($i = 0 ; $i < count($divs->elements) ; $i++){
            $div = $divs->eq($i);
            $dates[] = $div->find('ul li')->map(function ($li){
                return array(
                    'time'=>$li->find('span.eventtxt1')->eq(0)->text(),
                    'league'=>$li->find('span.eventtxt3')->eq(0)->text(),
                    'team'=>$li->find('span.eventtxt4')->eq(0)->text(),
                    'url'=>$li->find('span.eventtxt5 a')->eq(0)->attr('href'),
                    );
            });
        }

        /*
         * 最终链接形态
         * https://www.ballbar.cc/live/7240
         */

        $countNotMatch = 0;

        $totalCount = 0;

        for($i = 0 ; $i < count($times) ; $i++){
            if ($totalCount >= 3)
                break;
            $time = $times[$i];
            $tmp = $dates[$i];
            if (count($tmp) > 0 && $tmp[0]['time'] !=''){
                $totalCount++;
            }
            foreach ($tmp as $date) {
                if ($date['time'] != '') {
                    //开始匹配
                    $timeString = date_format(date_create_from_format('Ymd H:i', $time . $date['time']),'Y-m-d H:i:s');
                    if (count(explode(' VS ',$date['team'])) == 2) {
                        $host = explode(' VS ', $date['team'])[0];
                        $away = explode(' VS ', $date['team'])[1];
                    }
                    else if(count(explode(' - ',$date['team'])) == 2) {
                        $host = explode(' - ', $date['team'])[0];
                        $away = explode(' - ', $date['team'])[1];
                    }
                    else{
                        continue;
                    }
//                    $host = str_replace('【粤语直播】','',$host);
//                    $away = str_replace('【粤语直播】','',$away);
                    if(stristr('国语解说',$host) || stristr('国语解说',$away)) {
                        //不爬这个
                        continue;
                    }
                    $match = MatchesAfter::where('time', $timeString)
                        ->where(function ($q) use($host,$away){
                            $q->where(function ($q2) use($host, $away){
                                $q2->where('hname','=',$host)
                                    ->orwhere('aname','=',$away);
                            })->orwhere(function ($q2) use($host, $away){
                                $q2->where('win_hname','=',$host)
                                    ->orwhere('win_aname','=',$away);
                            });
                        })
                        ->first();
                    //别名搜一次
                    if (is_null($match)){
                        $team = LiaogouAlias::getAliasByName($host,1,LiaogouAlias::kFromBallBar);
                        $hid = 0;
                        if (isset($team)){
                            $hid = $team->lg_id;
                        }
                        $team = LiaogouAlias::getAliasByName($away,1,LiaogouAlias::kFromBallBar);
                        $aid = 0;
                        if (isset($team)){
                            $aid = $team->lg_id;
                        }
                        if ($hid > 0 || $aid > 0) {
                            $query = MatchesAfter::where('time', $timeString);
                            if ($hid > 0)
                                $query->where('hid', $hid);
                            if ($aid > 0)
                                $query->where('aid', $aid);
                            $match = $query->first();
                        }
                    }

                    if (isset($match)){
                        $matchLive = MatchLive::where('match_id',$match->id)->first();
                        if (is_null($matchLive)){
                            $matchLive = new MatchLive();
                            $matchLive->match_id = $match->id;
                            $matchLive->sport = 1;
                        }
                        $urlId = $date['url'];
                        $urlId = explode('/',$urlId);
                        $urlId = $urlId[count($urlId) - 1];
                        $urlId = explode('.',$urlId)[0];
                        $path = 'https://www.ballbar.cc/live/'.$urlId;
                        $matchLive->save();
                        $isPrivate = in_array($match->lid, SpiderTTZBController::football_private_array) ? 2 : 1;
                        MatchLiveChannel::saveSpiderChannel($match->id,MatchLive::kSportFootball,MatchLiveChannel::kTypeBallBar,$path,13,MatchLiveChannel::kPlatformPC,MatchLiveChannel::kPlayerIFrame,'标清直播', MatchLiveChannel::kShow, $isPrivate);
                        echo 'match ' . $host . ' vs ' .$away . '</br>';
                    }
                    else{
                        echo $host . ' ' .$away . '</br>';
                        $alise = LiveAlias::getAliasByName($host,LiveAlias::kTypeTeam,LiveAlias::kFromBallbar);
                        if (is_null($alise)){
                            $alise = LiaogouAlias::where('from',LiaogouAlias::kFromBallBar)
                                ->where('type',LiaogouAlias::kTypeTeam)
                                ->where('sport',LiaogouAlias::kSportTypeFootball)
                                ->where('target_name',$host)
                                ->first();
                            if (is_null($alise)){
                                $alise = new LiveAlias();
                                $alise->from = LiveAlias::kFromBallbar;
                                $alise->type = LiveAlias::kTypeTeam;
                                $alise->name = $host;
                                $alise->content = $timeString . ' ' . $date['league'] . ' ' . $host . ' vs ' . $away;
                                $alise->save();
                            }
                        }
                        $alise = LiveAlias::getAliasByName($away,LiveAlias::kTypeTeam,LiveAlias::kFromBallbar);
                        if (is_null($alise)){
                            $alise = LiaogouAlias::where('from',LiaogouAlias::kFromBallBar)
                                ->where('type',LiaogouAlias::kTypeTeam)
                                ->where('target_name',$away)
                                ->where('sport',LiaogouAlias::kSportTypeFootball)
                                ->first();
                            if (is_null($alise)){
                                $alise = new LiveAlias();
                                $alise->from = LiveAlias::kFromBallbar;
                                $alise->type = LiveAlias::kTypeTeam;
                                $alise->name = $away;
                                $alise->content = $timeString . ' ' . $date['league'] . ' ' . $host . ' vs ' . $away;
                                $alise->save();
                            }
                        }
                        $countNotMatch++;
                    }
                }
            }
        }
        dump($countNotMatch);
    }

    /**
     * 爬篮球直播
     */
    public function spiderBasketLiveData() {
        $ql = QueryList::get('https://www.ballbar.cc/class/basketball');
        $divs = $ql->find('div#righteventbox');
        $spans = $ql->find('span.dateHeader');
        $dates = $spans->map(function ($span){
            $classString = $span->class;
            $classString = str_replace('dateHeader date','',$classString);
            return $classString;
        });
        //时间
        $times = array();
        foreach ($dates as $date){
            $times[] = $date;
        }
        $dates = array();
        //比赛列表
        for ($i = 0 ; $i < count($divs->elements) ; $i++){
            $div = $divs->eq($i);
            $dates[] = $div->find('ul li')->map(function ($li){
                return array(
                    'class'=>$li->class,
                    'time'=>$li->find('span.eventtxt1')->eq(0)->text(),
                    'league'=>$li->find('span.eventtxt3')->eq(0)->text(),
                    'team'=>$li->find('span.eventtxt4')->eq(0)->text(),
                    'url'=>$li->find('span.eventtxt5 a')->eq(0)->attr('href'),
                );
            });
        }

        /*
         * 最终链接形态
         * https://www.ballbar.cc/live/7240
         */

        $countNotMatch = 0;

        $totalCount = 0;

        for($i = 0 ; $i < count($times) ; $i++){
            if ($totalCount >= 3)
                break;
            $time = $times[$i];
            $tmp = $dates[$i];
            if (count($tmp) > 0 && $tmp[0]['time'] !=''){
                $totalCount++;
            }
            foreach ($tmp as $date) {
                if ($date['time'] != '') {
                    //开始匹配
                    $timeString = date_format(date_create_from_format('Ymd H:i', $time . $date['time']),'Y-m-d H:i:s');
                    if (count(explode(' VS ',$date['team'])) == 2) {
                        $host = explode(' VS ', $date['team'])[0];
                        $away = explode(' VS ', $date['team'])[1];
                    }
                    else if(count(explode(' - ',$date['team'])) == 2){
                        $host = explode(' - ', $date['team'])[0];
                        $away = explode(' - ', $date['team'])[1];
                    }
//                    $host = str_replace('【粤语直播】','',$host);
//                    $away = str_replace('【粤语直播】','',$away);
                    $match = BasketMatchesAfter::where('time', $timeString)
                        ->where(function ($q) use($host,$away){
                            $q->where(function ($q2) use($host, $away){
                                $q2->where('hname','=',$host)
                                    ->where('aname','=',$away);
                            })->orwhere(function ($q2) use($host, $away){
                                $q2->where('short_hname','=',$host)
                                    ->where('short_aname','=',$away);
                            });
                        })
                        ->orwhere(function ($q) use($host,$away){
                            $q->where(function ($q2) use($host, $away){
                                $q2->where('hname','=',$away)
                                    ->where('aname','=',$host);
                            })->orwhere(function ($q2) use($host, $away){
                                $q2->where('short_hname','=',$away)
                                    ->where('short_aname','=',$host);
                            });
                        })
                        ->first();
                    //别名搜一次
                    if (is_null($match)){
                        $team = LiaogouAlias::getAliasByName($host,1,LiaogouAlias::kFromBallBar,LiaogouAlias::kSportTypeBasket);
                        $hid = 0;
                        if (isset($team)){
                            $hid = $team->lg_id;
                        }
                        $team = LiaogouAlias::getAliasByName($away,1,LiaogouAlias::kFromBallBar,LiaogouAlias::kSportTypeBasket);
                        $aid = 0;
                        if (isset($team)){
                            $aid = $team->lg_id;
                        }
                        if ($hid > 0 || $aid > 0) {
                            $query = BasketMatchesAfter::where('time', $timeString);
                            if ($hid > 0)
                                $query->where(function ($q) use($hid){
                                    $q->where('hid', $hid)
                                        ->orwhere('aid',$hid);
                                });
                            if ($aid > 0)
                                $query->where(function ($q) use($aid){
                                    $q->where('aid', $aid)
                                        ->orwhere('hid',$aid);
                                });
                            $match = $query->first();
                        }
                    }

                    if (isset($match)){
                        $matchLive = MatchLive::where('match_id',$match->id)
                            ->where('sport',MatchLive::kSportBasketball)
                            ->first();
                        if (is_null($matchLive)){
                            $matchLive = new MatchLive();
                            $matchLive->match_id = $match->id;
                            $matchLive->sport = MatchLive::kSportBasketball;
                        }
                        $urlId = $date['url'];
                        $urlId = explode('/',$urlId);
                        $urlId = $urlId[count($urlId) - 1];
                        $urlId = explode('.',$urlId)[0];
                        $path = 'https://www.ballbar.cc/live/'.$urlId;
                        $matchLive->save();
                        $isPrivate = in_array($match->lid, SpiderTTZBController::basketball_private_array) ? 2 : 1;
                        MatchLiveChannel::saveSpiderChannel($match->id,MatchLive::kSportBasketball,MatchLiveChannel::kTypeBallBar,$path,13,MatchLiveChannel::kPlatformPC,MatchLiveChannel::kPlayerIFrame,'标清直播', MatchLiveChannel::kShow, $isPrivate);
                        echo 'match ' . $host . ' vs ' .$away . '</br>';
                    }
                    else{
                        echo $host . ' ' .$away . '</br>';
                        $alise = LiveAlias::getAliasByName($host,LiveAlias::kTypeTeam,LiveAlias::kFromBallbar,LiaogouAlias::kSportTypeBasket);
                        if (is_null($alise)){
                            $alise = LiaogouAlias::where('from',LiaogouAlias::kFromBallBar)
                                ->where('type',LiaogouAlias::kTypeTeam)
                                ->where('sport',LiaogouAlias::kSportTypeBasket)
                                ->where('target_name',$host)
                                ->first();
                            if (is_null($alise)){
                                $alise = new LiveAlias();
                                $alise->from = LiveAlias::kFromBallbar;
                                $alise->type = LiveAlias::kTypeTeam;
                                $alise->name = $host;
                                $alise->sport = LiaogouAlias::kSportTypeBasket;
                                $alise->content = $timeString . ' ' . $date['league'] . ' ' . $host . ' vs ' . $away;
                                $alise->save();
                            }
                        }
                        $alise = LiveAlias::getAliasByName($away,LiveAlias::kTypeTeam,LiveAlias::kFromBallbar,LiaogouAlias::kSportTypeBasket);
                        if (is_null($alise)){
                            $alise = LiaogouAlias::where('from',LiaogouAlias::kFromBallBar)
                                ->where('type',LiaogouAlias::kTypeTeam)
                                ->where('target_name',$away)
                                ->where('sport',LiaogouAlias::kSportTypeBasket)
                                ->first();
                            if (is_null($alise)){
                                $alise = new LiveAlias();
                                $alise->from = LiveAlias::kFromBallbar;
                                $alise->type = LiveAlias::kTypeTeam;
                                $alise->name = $away;
                                $alise->sport = LiaogouAlias::kSportTypeBasket;
                                $alise->content = $timeString . ' ' . $date['league'] . ' ' . $host . ' vs ' . $away;
                                $alise->save();
                            }
                        }
                        $countNotMatch++;
                    }
                }
            }
        }
        dump($countNotMatch);
    }
}