<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/3/20
 * Time: 上午11:47
 */

namespace App\Console\Live;


use App\Http\Controllers\PC\CommonTool;
use App\Http\Controllers\PC\FileTool;
use App\Http\Controllers\PC\Match\LiveController;
use App\Http\Controllers\PC\Match\MatchController;
use App\Http\Controllers\PC\Match\MatchDetailController;
use App\Models\QSK\Anchor\AnchorRoomMatches;
use App\Models\QSK\Match\MatchLive;
use App\Models\QSK\Match\MatchLiveChannel;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LiveDetailJsonCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'live_detail_json_cache:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '静态化直播终端直播房间json,5分钟一次';

    /**
     * Create a new command instance.
     * HotMatchCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //足球
        $this->cache(1);
        //篮球
        $this->cache(2);
    }

    private function cache($sport){
        $startDate = date('Ymd');
        $pc_json = FileTool::matchListDataJson($startDate,$sport);
        $mids = array();
        if (is_null($pc_json) || !isset($pc_json['matches'])) {
            echo '获取数据失败';
        }
        else{
            //直播终端
            $matches = $pc_json['matches'];
            //先整理时间符合的mid
            foreach ($matches as $match){
                $mid = $match['mid'];
                $time = isset($match['time']) ? $match['time'] : 0;
                $now = time();
                if ($time == 0 ) {//只静态化赛前4小时内 的比赛终端。
                    continue;
                }
                $start_time = $time;//比赛时间
                $flg_1 = $start_time >= $now && $now + 5 * 60 * 60 >= $start_time;//开赛前5小时
                $flg_2 = $start_time <= $now && $start_time + 3 * 60 * 60  >= $now;//开赛后3小时
                if ($flg_1 || $flg_2) {
                    $mids[] = $mid;
                }
            }
        }
        //获取这些比赛id中有直播房间的比赛
        $resultMid = array();
        $ams = AnchorRoomMatches::whereIn('mid',$mids)
            ->select('mid')
            ->groupBy('mid')
            ->get();
        foreach ($ams as $mid){
            $resultMid[] = $mid['mid'];
        }

        //再把爱看球有的写进去
        //爱看球频道
        $ams = MatchLive::whereIn('match_id',$mids)
            ->join('match_live_channels',function ($q){
                $q->where('match_live_channels.show',MatchLiveChannel::kShow);
                $q->on('match_live_channels.live_id','match_lives.id');
            })
            ->where('sport',$sport)
            ->select('match_live_channels.id as room_id')
            ->get();
        //拼到mids
        foreach ($ams as $mid){
            if (!in_array($mid['room_id'],$resultMid)) {
                $resultMid[] = $mid['room_id'];
            }
        }

        //生成这些比赛id对应的有什么直播间的json,直播终端生成的时候用
        foreach ($resultMid as $mid){
            self::flushLiveDetailHtml($mid,$sport);
        }
    }

    public static function flushLiveDetailHtml($mid, $sport){
        $ch = curl_init();
        $url = asset('/api/static/live/detail/' . $sport.'/'.$mid);
        echo $url . '<br>';
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);//8秒超时
        curl_exec ($ch);
        curl_close ($ch);
    }
}