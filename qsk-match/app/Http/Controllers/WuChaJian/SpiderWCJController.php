<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2017/11/29
 * Time: 下午3:16
 */

namespace App\Http\Controllers\WuChaJian;

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

class SpiderWCJController extends Controller{
    public function index($action, Request $request)
    {
        if (method_exists($this, $action)) {
            $this->$action($request);
        } else {
            echo "Error: Not Found action 'SpiderWCJController->$action()'";
        }
    }

    /**
     * 爬直播
     */
    public function spiderLiveData()
    {
        $ql = QueryList::get('http://www.wuchajian.com/');
        $trs = $ql->find('div.data table tbody')->eq(0);
        $datas = $trs->children()->map(function ($item) {
            //用is判断节点类型
            if ($item->class == 'date') {
                //切割时间 2017年12月12日
                $td = $item->find('td:eq(0)');
                $timeStr = $td->text();
                $time = date_format(date_create_from_format('Y年m月d日', explode('日', $timeStr)[0].'日'), 'Y-m-d');
                return array('type' => 'date', 'data' => $time);
            }
            elseif ($item->class == 'against'){
                $type = $item->find('td img')->eq(0)->src;
                //足球
                if ($type == '/images/01.png'){
                    $league = $item->find('td.matcha a')->eq(0)->text();
                    $teamTds = $item->find('td.teama strong');
                    $hteam = $teamTds->eq(0)->text();
                    $ateam = $teamTds->eq(1)->text();
                    $timeStr = $item->find('td.tixing')->eq(0)->t;
                    $time = date_format(date_create_from_format('Y-m-d H:i', $timeStr), 'Y-m-d H:i');
//                    echo $hteam. ' ' . $ateam . ' ' . $time.'</br>';
                    $urls = $item->find('td.live_link a')->map(function ($m){
                        return $m->href;
                    });
                    $urlString = '';
                    for ($i = 0 ; $i < count($urls) ; $i++){
                        $url = $urls[$i];
                        if (stristr($url,'tv/qqlive') || stristr($url,'tv/qietv')){
                            $url = explode('/',$url)[count(explode('/',$url)) - 1];
                            $url = explode('.',$url)[0];
                            $urlString = $url;
                            break;
                        }
                    }
                    return array('type' => 'football', 'data' => array('hname'=>$hteam,'aname'=>$ateam,'time'=>$time,'league'=>$league,'url'=>$urlString));
                }
                //篮球
                else if($type == '/images/2.png'){
                    $league = $item->find('td.matcha a')->eq(0)->text();
                    $teamTds = $item->find('td.teama strong');
                    $hteam = $teamTds->eq(0)->text();
                    $ateam = $teamTds->eq(1)->text();
                    $timeStr = $item->find('td.tixing')->eq(0)->t;
                    $time = date_format(date_create_from_format('Y-m-d H:i', $timeStr), 'Y-m-d H:i');
//                    echo $hteam. ' ' . $ateam . ' ' . $time.'</br>';
                    $urls = $item->find('td.live_link a')->map(function ($m){
                        return $m->href;
                    });
                    $urlString = '';
                    for ($i = 0 ; $i < count($urls) ; $i++){
                        $url = $urls[$i];
                        if (stristr($url,'tv/qqlive') || stristr($url,'tv/qietv')|| stristr($url,'tv/tx-')){
                            $url = explode('/',$url)[count(explode('/',$url)) - 1];
                            $url = explode('.',$url)[0];
                            $urlString = $url;
                            break;
                        }
                    }
                    return array('type' => 'basketball', 'data' => array('hname'=>$hteam,'aname'=>$ateam,'time'=>$time,'league'=>$league,'url'=>$urlString));
                }
                else{
                    return array('type' => 'tmp', 'data' => 'other match');
                }
            }
            else{
                return array('type' => 'tmp', 'data' => 'other tr');
            }
        });

        $countNotMatch = 0;
        foreach ($datas as $data){
            if ($data['type'] == 'tmp' || $data['type'] == 'date'){
                continue;
            }
            $host = $data['data']['hname'];
            $away = $data['data']['aname'];
            $timeStr = $data['data']['time'];
            $leagueStr = $data['data']['league'];
            $liveStr = $data['data']['url'];
            if ($liveStr == '')
                continue;
            //篮球
            if ($data['type'] == 'basketball'){
                $match = BasketMatchesAfter::where('time', $timeStr)
                    ->where(function ($q) use($host,$away){
                        $q->where(function ($q2) use($host, $away){
                            $q2->where('hname','=',$host)
                                ->orwhere('aname','=',$away);
                        })
                            ->orwhere(function ($q2) use($host, $away){
                                $q2->where('aname','=',$host)
                                    ->orwhere('hname','=',$away);
                            });
                    })
                    ->first();
                //别名搜一次
                if (is_null($match)){
                    $team = LiaogouAlias::getAliasByName($host,1,LiaogouAlias::kFromWCJ,LiaogouAlias::kSportTypeBasket);
                    $hid = 0;
                    if (isset($team)){
                        $hid = $team->lg_id;
                    }
                    $team = LiaogouAlias::getAliasByName($away,1,LiaogouAlias::kFromWCJ,LiaogouAlias::kSportTypeBasket);
                    $aid = 0;
                    if (isset($team)){
                        $aid = $team->lg_id;
                    }
                    if ($hid > 0 || $aid > 0) {
                        $query = BasketMatchesAfter::where('time', $timeStr);
                        if ($hid > 0)
                            $query->where(function ($q) use($hid){
                                $q->where('hid', $hid)
                                    ->orwhere('aid',$hid);
                            });
                        if ($aid > 0)
                            $query->where(function ($q) use($aid){
                                $q->where('hid', $aid)
                                    ->orwhere('aid',$aid);
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
                    $matchLive->save();
                    MatchLiveChannel::saveSpiderChannel($match->id,MatchLive::kSportBasketball,MatchLiveChannel::kTypeWCJ,$liveStr,11,MatchLiveChannel::kPlatformAll,MatchLiveChannel::kPlayerCk,'wcj');
                    echo 'match ' . $host . ' vs ' .$away . ' 频道 '.$liveStr.'</br>';
                }
                else{
                    $alise = LiveAlias::getAliasByName($host,LiveAlias::kTypeTeam,LiveAlias::kFromWCJ,LiaogouAlias::kSportTypeBasket);
                    if (is_null($alise)){
                        $alise = LiaogouAlias::where('from',LiaogouAlias::kFromWCJ)
                            ->where('type',LiaogouAlias::kTypeTeam)
                            ->where('sport',LiaogouAlias::kSportTypeBasket)
                            ->where('target_name',$host)
                            ->first();
                        if (is_null($alise)){
                            $alise = new LiveAlias();
                            $alise->from = LiveAlias::kFromWCJ;
                            $alise->type = LiveAlias::kTypeTeam;
                            $alise->name = $host;
                            $alise->sport = LiaogouAlias::kSportTypeBasket;
                            $alise->content = '无插件 ' . $timeStr . ' ' . $leagueStr . ' ' . $host . ' vs ' . $away;
                            $alise->save();
                        }
                    }
                    $alise = LiveAlias::getAliasByName($away,LiveAlias::kTypeTeam,LiveAlias::kFromWCJ,LiaogouAlias::kSportTypeBasket);
                    if (is_null($alise)){
                        $alise = LiaogouAlias::where('from',LiaogouAlias::kFromWCJ)
                            ->where('type',LiaogouAlias::kTypeTeam)
                            ->where('target_name',$away)
                            ->where('sport',LiaogouAlias::kSportTypeBasket)
                            ->first();
                        if (is_null($alise)){
                            $alise = new LiveAlias();
                            $alise->from = LiveAlias::kFromWCJ;
                            $alise->type = LiveAlias::kTypeTeam;
                            $alise->name = $away;
                            $alise->sport = LiaogouAlias::kSportTypeBasket;
                            $alise->content = '无插件 ' . $timeStr . ' ' . $leagueStr . ' ' . $host . ' vs ' . $away;
                            $alise->save();
                        }
                    }
                    echo 'not match ' . $host . ' vs ' .$away . '</br>';
                    $countNotMatch++;
                }
            }
            else if ($data['type'] == 'football'){
                $match = MatchesAfter::where('time', $timeStr)
                    ->where(function ($q) use($host,$away){
                        $q->where(function ($q2) use($host, $away){
                            $q2->where('hname','=',$host)
                                ->orwhere('aname','=',$away);
                        })
                            ->orwhere(function ($q2) use($host, $away){
                                $q2->where('aname','=',$host)
                                    ->orwhere('hname','=',$away);
                            });
                    })
                    ->first();
                //别名搜一次
                if (is_null($match)){
                    $team = LiaogouAlias::getAliasByName($host,1,LiaogouAlias::kFromWCJ,LiaogouAlias::kSportTypeFootball);
                    $hid = 0;
                    if (isset($team)){
                        $hid = $team->lg_id;
                    }
                    $team = LiaogouAlias::getAliasByName($away,1,LiaogouAlias::kFromWCJ,LiaogouAlias::kSportTypeFootball);
                    $aid = 0;
                    if (isset($team)){
                        $aid = $team->lg_id;
                    }
                    if ($hid > 0 || $aid > 0) {
                        $query = MatchesAfter::where('time', $timeStr);
                        if ($hid > 0)
                            $query->where(function ($q) use($hid){
                                $q->where('hid', $hid)
                                    ->orwhere('aid',$hid);
                            });
                        if ($aid > 0)
                            $query->where(function ($q) use($aid){
                                $q->where('hid', $aid)
                                    ->orwhere('aid',$aid);
                            });
                        $match = $query->first();
                    }
                }

                if (isset($match)){
                    $matchLive = MatchLive::where('match_id',$match->id)
                        ->where('sport',MatchLive::kSportFootball)
                        ->first();
                    if (is_null($matchLive)){
                        $matchLive = new MatchLive();
                        $matchLive->match_id = $match->id;
                        $matchLive->sport = MatchLive::kSportFootball;
                    }
                    $matchLive->save();
                    MatchLiveChannel::saveSpiderChannel($match->id,MatchLive::kSportFootball,MatchLiveChannel::kTypeWCJ,$liveStr,11,MatchLiveChannel::kPlatformAll,MatchLiveChannel::kPlayerCk,'wcj');
                    echo 'match ' . $host . ' vs ' .$away . ' 频道 '.$liveStr.'</br>';
                }
                else{
                    $alise = LiveAlias::getAliasByName($host,LiveAlias::kTypeTeam,LiveAlias::kFromWCJ,LiaogouAlias::kSportTypeFootball);
                    if (is_null($alise)){
                        $alise = LiaogouAlias::where('from',LiaogouAlias::kFromWCJ)
                            ->where('type',LiaogouAlias::kTypeTeam)
                            ->where('sport',LiaogouAlias::kSportTypeFootball)
                            ->where('target_name',$host)
                            ->first();
                        if (is_null($alise)){
                            $alise = new LiveAlias();
                            $alise->from = LiveAlias::kFromWCJ;
                            $alise->type = LiveAlias::kTypeTeam;
                            $alise->name = $host;
                            $alise->sport = LiaogouAlias::kSportTypeFootball;
                            $alise->content = '无插件 ' . $timeStr . ' ' . $leagueStr . ' ' . $host . ' vs ' . $away;
                            $alise->save();
                        }
                    }
                    $alise = LiveAlias::getAliasByName($away,LiveAlias::kTypeTeam,LiveAlias::kFromWCJ,LiaogouAlias::kSportTypeFootball);
                    if (is_null($alise)){
                        $alise = LiaogouAlias::where('from',LiaogouAlias::kFromWCJ)
                            ->where('type',LiaogouAlias::kTypeTeam)
                            ->where('target_name',$away)
                            ->where('sport',LiaogouAlias::kSportTypeFootball)
                            ->first();
                        if (is_null($alise)){
                            $alise = new LiveAlias();
                            $alise->from = LiveAlias::kFromWCJ;
                            $alise->type = LiveAlias::kTypeTeam;
                            $alise->name = $away;
                            $alise->sport = LiaogouAlias::kSportTypeFootball;
                            $alise->content = '无插件 ' . $timeStr . ' ' . $leagueStr . ' ' . $host . ' vs ' . $away;
                            $alise->save();
                        }
                    }
                    echo 'not match ' . $host . ' vs ' .$away . '</br>';
                    $countNotMatch++;
                }
            }
        }
        echo 'not match count' . $countNotMatch. '</br>';
    }

    /**
     * jsong类型的
     */
    public function test2(){
        $ch = curl_init();

        $url = "http://info.zb.video.qq.com/?cnlid=100204100&host=qq.com&cmd=2&qq=0&stream=1&sdtfrom=113&callback=jsonp9";

        curl_setopt($ch, CURLOPT_URL,$url);
        //        curl_setopt($ch, CURLOPT_POST, 1);
        //        curl_setopt($ch, CURLOPT_POSTFIELDS,$vars);  //Post Fields
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = [
            'Accept:application/json, text/javascript, */*; q=0.01',
            'Accept-Language:zh-CN,zh;q=0.9,en;q=0.8',
            'Cache-Control:no-cache',
            'Connection:keep-alive',
//            'Host:w.zhibo.me:8088',
//            'Referer:http://www.zuqiu.me/tv/qqlive8.html',
//            'requestKey:ghl6seMfbp0PmFjSlFja1QkzMYqi8VMZ',
            'User-Agent:Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1',
            'X-Requested-With:XMLHttpRequest',
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $server_output = curl_exec ($ch);
        dump($server_output);

        curl_close ($ch);

        $result = json_decode($server_output, true);
        dump($result);
    }


    public function getLiveMatchQQLiveUrl(){
        $ch = curl_init();

        $url = "http://w.zhibo.me:8088/qqliveHD5.php";

        curl_setopt($ch, CURLOPT_URL,$url);
        //        curl_setopt($ch, CURLOPT_POST, 1);
        //        curl_setopt($ch, CURLOPT_POSTFIELDS,$vars);  //Post Fields
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = [
            'Accept:application/json, text/javascript, */*; q=0.01',
            'Accept-Language:zh-CN,zh;q=0.9,en;q=0.8',
            'Cache-Control:no-cache',
            'Connection:keep-alive',
            'Host:w.zhibo.me:8088',
            'Referer:http://www.zuqiu.me/tv/qqlive8.html',
//            'requestKey:ghl6seMfbp0PmFjSlFja1QkzMYqi8VMZ',
            'User-Agent:Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1',
            'X-Requested-With:XMLHttpRequest',
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $server_output = curl_exec ($ch);
        dump($server_output);

        curl_close ($ch);
    }
}