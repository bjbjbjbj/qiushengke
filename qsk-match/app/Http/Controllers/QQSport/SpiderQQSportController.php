<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/2/28
 * Time: 下午3:23
 */

namespace App\Http\Controllers\QQSport;


use App\Http\Controllers\TTZB\SpiderTTZBController;
use App\Models\LiaoGouModels\BasketMatch;
use App\Models\LiaoGouModels\BasketMatchesAfter;
use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\MatchesAfter;
use App\Models\LiaoGouModels\MatchLive;
use App\Models\LiaoGouModels\MatchLiveChannel;
use App\Models\LiaoGouModels\Team;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SpiderQQSportController extends Controller
{
//4=世界杯 57
//5=欧冠 73
//6=欧联杯 77
//8=英足总杯 69
//21=意甲 29
//22=德甲 8
//23=西甲 26
//24=法甲 11
//208=中超 46
//605=亚冠 139
//100000=NBA 1
//100008=CBA 4
    //足球
    const Football_lids = ['4'=>'57','5'=>'73','6'=>'77','8'=>'69','21'=>'29','22'=>'8','23'=>'26','24'=>'11','208'=>'46','605'=>'139'];
//    const Football_lids = ['8'=>'69'];
    //篮球,NBA先手录因为要带参数
//    const Basketball_lids = ['100000'=>'1','100008'=>'4'];
    const Basketball_lids = ['100008'=>'4'];

    //是否有版权
    const basketball_private_array = [1, 2, 3, 4, 5];//NBA, WNBA, NBA明星赛, CBA, CBA明星赛
    const football_private_array = [8,11,26,29,31,46,73,77];//足球 英超、意甲、德甲、法甲、西甲、欧冠、欧联、中超

    public function index($action, Request $request)
    {
        if (method_exists($this, $action)) {
            $this->$action($request);
        } else {
            echo "Error: Not Found action 'SpiderQQSportController->$action()'";
        }
    }

    /**
     * 爬足球
     */
    public function spiderFootBall(){
        $start = date_format(date_create(), "Y-m-d");
        $end = date_format(date_create('+1 day'), "Y-m-d");
        foreach (self::Football_lids as $qq_lid=>$lid){
            $this->spiderMatches($qq_lid,$lid,$start,$end,MatchLive::kSportFootball);
        }
    }

    /**
     * 爬篮球
     */
    public function spiderBasketBall(){
        $start = date_format(date_create(), "Y-m-d");
        $end = date_format(date_create('+1 day'), "Y-m-d");
        foreach (self::Basketball_lids as $qq_lid=>$lid){
            $this->spiderMatches($qq_lid,$lid,$start,$end,MatchLive::kSportBasketball);
        }
    }

    /**
     * 爬数据方法
     * @param $qq_lid 腾讯lid
     * @param $lid 料狗lid
     * @param $start
     * @param $end
     * @param $sport
     */
    private function spiderMatches($qq_lid,$lid,$start,$end,$sport){
        if (!in_array($sport,array(MatchLive::kSportFootball,MatchLive::kSportBasketball))){
            echo "类型错误";
            return;
        }
        $ch = curl_init();
        $url = "http://matchweb.sports.qq.com/kbs/list?columnId=".$qq_lid."&startTime=".$start."&endTime=".$end;
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);

        $data = json_decode($server_output, true);
        if (!isset($data) || !isset($data['data'])) {
            echo "无数据";
            return;
        }
        if (!isset($data['code']) || $data['code'] != 0) {
            echo  "获取接口失败";
            return;
        }
        $data = $data['data'];
        //处理数据
        foreach ($data as $key=>$matches){
            //找有没有对应的
            foreach ($matches as $match){
                $startTime = $match['startTime'];
                $home = $match['leftName'];
                $away = $match['rightName'];
                $liveId = $match['liveId'];
                //没有liveId,跳过
                if (is_null($liveId) || strlen($liveId) == 0) {
                    echo 'qq ' . $startTime . ' '.$home . ' vs '. $away .' 没有liveId'.'<br>';
                    continue;
                }

                if ($sport == 1)
                    $query = MatchesAfter::query();
                else if($sport == 2)
                    $query = BasketMatch::query();
                $query->where('lid',$lid);
                $query->where('time',$startTime);
                $query->where(function ($q) use($home,$away){
                    $q->where(function ($q2) use($home){
                        $q2->where('hname',$home)
                            ->orwhere('aname',$home);
                    });
                    $q->orwhere(function ($q2) use($away){
                        $q2->where('hname',$away)
                            ->orwhere('aname',$away);
                    });
                });
                $lg_matches = $query->get();
                if (count($lg_matches) == 1){
                    $lg_match = $lg_matches[0];
                    //找到比赛,填url
                    if ($sport == MatchLive::kSportFootball)
                        $isPrivate = in_array($lg_match->lid, self::football_private_array);
                    else
                        $isPrivate = in_array($lg_match->lid, self::basketball_private_array);
//                    $show = $isPrivate ? MatchLiveChannel::kHide : MatchLiveChannel::kShow;
                    //暂时都先隐藏,要试试有无问题
                    $show = MatchLiveChannel::kHide;
                    $private = $isPrivate ? MatchLiveChannel::kPrivate : MatchLiveChannel::kNotPrivate;
                    $liveStr = 'http://info.zb.video.qq.com/?cnlid='.$liveId.'&host=qq.com&cmd=2&qq=0&txvjsv=2.0&stream=2&system=1&sdtfrom=113';
                    MatchLiveChannel::saveSpiderChannel($lg_match->id,$sport,MatchLiveChannel::kTypeQQ,$liveStr,20,MatchLiveChannel::kPlatformAll,MatchLiveChannel::kPlayerCk,'视频直播', $show, $private, MatchLiveChannel::kUseAikq);
                    echo 'qq ' . $startTime .' '.$qq_lid.$match['matchDesc'] .' '.$home . ' vs '. $away .' 匹配 '.$lg_match->id.' '.$lg_match['time'].' '.$lg_match['hname'].' vs ',$lg_match['aname'].'<br>';
                    continue;
                }
                echo 'qq ' . $startTime .' '.$qq_lid.$match['matchDesc']. ' '.$home . ' vs '. $away .' 不匹配 '.'<br>';
            }
        }
    }
}