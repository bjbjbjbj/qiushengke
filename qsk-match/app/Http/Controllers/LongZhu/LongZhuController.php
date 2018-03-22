<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/2/25
 * Time: 15:40
 */

namespace App\Http\Controllers\LongZhu;


use App\Http\Controllers\TTZB\SpiderTTZBController;
use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\MatchLive;
use App\Models\LiaoGouModels\MatchLiveChannel;
use App\Models\LiaoGouModels\Team;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LongZhuController extends Controller
{

    const German_Channel = ['科隆'=>'http://hls1.plu.cn/azblive/kl/playlist.m3u8', '多特蒙德'=>'http://hls1.plu.cn/azblive/dtmd/playlist.m3u8'
        , '柏林赫塔'=>'http://hls1.plu.cn/azblive/blht/playlist.m3u8', '美因茨'=>'http://hls1.plu.cn/azblive/myc/playlist.m3u8',
        '门兴格拉德巴赫'=>'http://hls1.plu.cn/azblive/menxing/playlist.m3u8', '霍芬海姆'=>'http://hls1.plu.cn/azblive/hfhm/playlist.m3u8',
        '弗赖堡'=>'http://hls1.plu.cn/azblive/flb/playlist.m3u8', '沃尔夫斯堡'=>'http://hls1.plu.cn/azblive/wefsb/playlist.m3u8',
        '勒沃库森'=>'http://hls1.plu.cn/azblive/lwks/playlist.m3u8', '汉堡'=>'http://hls1.plu.cn/azblive/hb/playlist.m3u8',
        '奥格斯堡'=>'http://hls1.plu.cn/azblive/agsb/playlist.m3u8', '拜仁慕尼黑'=>'http://hls1.plu.cn/azblive/brmnh/playlist.m3u8'];//德甲联赛球队线路

    const Spain_Channel = ['阿拉维斯'=>'http://hls1.plu.cn/azblive/alws/playlist.m3u8', '西班牙人'=>'http://hls1.plu.cn/azblive/xbyr/playlist.m3u8',
        '皇家社会'=>'http://hls1.plu.cn/azblive/hjsh/playlist.m3u8', '塞维利亚'=>'http://hls1.plu.cn/azblive/swly/playlist.m3u8',
        '马德里竞技'=>'http://hls1.plu.cn/azblive/mdljj/playlist.m3u8', '拉斯帕尔马斯'=>'http://hls1.plu.cn/azblive/lspems/playlist.m3u8',
        '皇家贝蒂斯'=>'http://hls1.plu.cn/azblive/hjbds/playlist.m3u8', '埃瓦尔'=>'http://hls1.plu.cn/azblive/awe/playlist.m3u8',
        '比利亚雷亚尔'=>'http://hls1.plu.cn/azblive/blylgf/playlist.m3u8', '莱加内斯'=>'http://hls1.plu.cn/azblive/ljns/playlist.m3u8',
        '皇家马德里'=>'http://hls1.plu.cn/azblive/hjmdl/playlist.m3u8', '赫罗纳'=>'http://hls1.plu.cn/azblive/hln/playlist.m3u8',
        '拉斯帕尔马斯'=>'http://hls1.plu.cn/azblive/lspems/playlist.m3u8', '马拉加'=>'http://hls1.plu.cn/azblive/mlj/playlist.m3u8',
        '维戈塞尔塔'=>'http://hls1.plu.cn/azblive/wgset/playlist.m3u8', '瓦伦西亚'=>'http://hls1.plu.cn/azblive/wlxy/playlist.m3u8',
        '拉科鲁尼亚'=>'http://hls1.plu.cn/azblive/lklny/playlist.m3u8', '毕尔巴鄂竞技'=>'http://hls1.plu.cn/azblive/bebejj/playlist.m3u8',
        '赫塔菲'=>'http://hls1.plu.cn/azblive/htf/playlist.m3u8', '塞尔塔'=>'http://hls1.plu.cn/azblive/wgset/playlist.m3u8'];//西甲联赛球队线路

    const France_Channel = ['里昂'=>'http://hls1.plu.cn/azblive/la/playlist.m3u8', '南特'=>'http://hls1.plu.cn/azblive/nt/playlist.m3u8',
        '卡昂'=>'http://hls1.plu.cn/azblive/ka/playlist.m3u8', '尼斯'=>'http://hls1.plu.cn/azblive/ns/playlist.m3u8',
        '巴黎圣日耳曼'=>'http://hls1.plu.cn/azblive/blsrem/playlist.m3u8', '第戎'=>'http://hls1.plu.cn/azblive/dr/playlist.m3u8',
        '梅斯'=>'http://hls1.plu.cn/azblive/meisi/playlist.m3u8', '里尔'=>'http://hls1.plu.cn/azblive/lier/playlist.m3u8',
        '摩纳哥'=>'http://hls1.plu.cn/azblive/mng/playlist.m3u8', '图卢兹'=>'http://hls1.plu.cn/azblive/tlz/playlist.m3u8',
        '安格斯'=>'http://hls1.plu.cn/azblive/ar/playlist.m3u8', '斯特拉斯堡'=>'http://hls1.plu.cn/azblive/stlsb/playlist.m3u8',
        '马赛'=>'http://hls1.plu.cn/azblive/masai/playlist.m3u8', '蒙彼利埃'=>'http://hls1.plu.cn/azblive/mbla/playlist.m3u8',
        '尼斯'=>'http://hls1.plu.cn/azblive/ns/playlist.m3u8', '特鲁瓦 '=>'http://hls1.plu.cn/azblive/tlw/playlist.m3u8'];//法甲联赛球队线路

    const Italy_Channel = ['桑普多利亚'=>'http://hls1.plu.cn/azblive/spdly/playlist.m3u8', '博洛尼亚'=>'http://hls1.plu.cn/azblive/blny/playlist.m3u8',
        '拉齐奥'=>'http://hls1.plu.cn/azblive/lqa/playlist.m3u8', '森索罗'=>'http://hls1.plu.cn/azblive/ssl/playlist.m3u8',
        '卡利亚里'=>'http://hls1.plu.cn/azblive/klyl/playlist.m3u8', '国际米兰'=>'http://hls1.plu.cn/azblive/gjml/playlist.m3u8',
        '尤文图斯'=>'http://hls1.plu.cn/azblive/ywts/playlist.m3u8', '切沃'=>'http://hls1.plu.cn/azblive/qw/playlist.m3u8',
        '佛罗伦萨'=>'http://hls1.plu.cn/azblive/flls/playlist.m3u8', '克罗托内'=>'http://hls1.plu.cn/azblive/kltn/playlist.m3u8',
        '热那亚'=>'http://hls1.plu.cn/azblive/rny/playlist.m3u8', '那不勒斯'=>'http://hls1.plu.cn/azblive/nbls/playlist.m3u8',
        '都灵'=>'http://hls1.plu.cn/azblive/dl/playlist.m3u8', 'AC米兰'=>'http://hls1.plu.cn/azblive/acml/playlist.m3u8',
        '罗马'=>'http://hls1.plu.cn/azblive/lmgf/playlist.m3u8', '史帕尔'=>'http://hls1.plu.cn/azblive/spe/playlist.m3u8',
        '乌迪内斯'=>'http://hls1.plu.cn/azblive/wdns/playlist.m3u8', '热那亚'=>'http://hls1.plu.cn/azblive/rny/playlist.m3u8',
        '都灵'=>'http://hls1.plu.cn/azblive/dl/playlist.m3u8', '贝内文托'=>'http://hls1.plu.cn/azblive/bnwt/playlist.m3u8',
        '亚特兰大'=>'http://hls1.plu.cn/azblive/ytld/playlist.m3u8'];//意甲联赛球队线路

    //PPTV 联赛id  英超：3，西甲：43，德甲：65，意甲：7，法甲：19
    const Channel_Array = [
        65=>self::German_Channel,
        43=>self::Spain_Channel,
        19=>self::France_Channel,
        7=>self::Italy_Channel,
        //中超 TODO
    ];

    public function spiderFootball(Request $request) {
        //PPTV 联赛id  英超：3，西甲：43，德甲：65，意甲：7，法甲：19
        $league_array = [7, 19, 43, 65];
        $ch = curl_init();
        $url = "http://aplus.pptv.com/inapi/live/pg_sports?&cb=liveCenter_interface16&start=0&end=2&plt=web";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);

        $server_output = str_replace('liveCenter_interface16(', '', $server_output);
        $server_output = substr($server_output, 0, strlen($server_output) - 2);

        $data = json_decode($server_output, true);
        if (!isset($data)) {
            echo "无数据";
            return;
        }
        if (!isset($data['err']) || !isset($data['data']) || !isset($data['data']['list']) || $data['err'] != 0) {
            echo  "获取接口失败";
            return;
        }

        $list = $data['data']['list'];
        foreach ($list as $date=>$matches) {
            foreach ($matches as $match) {
                if (!isset($match['match']) || !isset($match['match']['id']) || !in_array($match['match']['id'], $league_array)) {
                    continue;
                }
//                $program_pay = $match['program_pay'];//是否需要付费，0免费，1付费。
//                if ($program_pay != 0) {
//                    continue;
//                }
                $pptv_lid = $match['match']['id'];//pptv联赛id
                $start_time = $match['start_time'];//pptv 开播时间
                $homeTeamTitle = $match['homeTeamTitle'];//主队名称

                $channel_array = self::Channel_Array[$pptv_lid];
                if (!isset($channel_array[$homeTeamTitle])) {
                    continue;
                }
                $channel = $channel_array[$homeTeamTitle];
                echo $homeTeamTitle . ':（' . date('Y-m-d H:i', $start_time) . '）' . $channel . '<br/>';
                $this->saveChannel($homeTeamTitle, $channel, $start_time);
            }
        }
    }

    /**
     * 保存龙珠直播的源
     * @param $homeTitle
     * @param $content
     * @param $start_time
     */
    public function saveChannel($homeTitle, $content, $start_time) {
        if (empty($homeTitle) || empty($content) || !is_numeric($start_time)) {
            echo '保存参数错误...';
            return;
        }
        $team = Team::query()->where('name', $homeTitle)->first();
        if (!isset($team)) {
            echo '球队（' . $homeTitle . '）不存在';
            return;
        }
        $tid = $team->id;
        echo ' 球队ID：' . $tid;
        $start_date = date('Y-m-d H:i', $start_time);
        $end_date = date('Y-m-d H:i', strtotime('+2 days'));
        $match = Match::query()->where('hid', $tid)->whereBetween('time', [$start_date, $end_date])->first();
        if (!isset($match)) {
            echo $tid . '比赛不存在..........<br/>';
            return;
        }
        $match_id = $match->id;
        //当前时间大于比赛时间10分钟后不再修改。
        if (time() - strtotime($match->time) > 60 * 10) {
            echo '比赛已开始..........';
            return;
        }
        echo $match->hname . ' VS ' . $match->aname . '<br/>';
        //查找是否存在channel
        $live = MatchLive::query()->where('match_id', $match_id)->where('sport', MatchLive::kSportFootball)->first();
        $isPrivate = in_array($match->lid, SpiderTTZBController::football_private_array) ? 2 : 1;
        $ch_name = '高清原声';
        $ch_show = $this->canOpen($content) ? MatchLiveChannel::kShow : MatchLiveChannel::kHide;
        if (!isset($live)) {
            echo '不存在Live....';
            //保存线路
            $ex = MatchLiveChannel::saveSpiderChannel($match_id, MatchLive::kSportFootball, MatchLiveChannel::kTypeLZ, $content, 10, MatchLiveChannel::kPlatformAll, MatchLiveChannel::kPlayerM3u8, $ch_name, $ch_show, $isPrivate, MatchLiveChannel::kUseAll);
            echo isset($ex) ? $ex->getMessage() : '无报错';
        } else {
            echo '存在Live....';
            //判断是否存在线路
            $ch = MatchLiveChannel::query()->where('live_id', $live->id)->where('content', $content)->first();
            if (!isset($ch)) {
                echo '不存在Channel....';
                //保存线路
                MatchLiveChannel::saveSpiderChannel($match_id, MatchLive::kSportFootball, MatchLiveChannel::kTypeLZ, $content, 10, MatchLiveChannel::kPlatformAll, MatchLiveChannel::kPlayerM3u8, $ch_name, $ch_show, $isPrivate, MatchLiveChannel::kUseAll);
            } else {
                echo '存在Channel....';
                if ($ch->show != MatchLiveChannel::kShow && $ch->auto != MatchLiveChannel::kAutoHand && $ch_show == MatchLiveChannel::kShow) {//线路不显示、线路没有人工修改、能打开视频。
                    $ch->show = MatchLiveChannel::kShow;
                    $ch->save();
                }
            }
        }
        echo '操作成功.....<br/>';
    }

    /**
     * 检查是否能打开M3U8的流
     * @param $url
     * @return bool
     */
    protected function canOpen($url) {
        //$url = 'http://live.hkstv.hk.lxdns.com/live/hks/playlist.m3u8?wsSession=17ea2a67e0e97ee180d69208-151947096616674&wsIPSercert=1c251b7d498d660c346ca9bc382d7cc4&wsMonitor=-1';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT,3);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        return !empty($server_output);
    }

}