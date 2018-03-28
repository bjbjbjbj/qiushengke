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
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LiveCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'live_detail_cache:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '静态化直播终端,10分钟一次,一次拿-1到+3小时的30场比赛';

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
        $this->cache(2);
    }

    private function cache($sport){
        $startDate = date('Ymd');
        $pc_json = FileTool::matchListDataJson($startDate,$sport);
        if (is_null($pc_json) || !isset($pc_json['matches'])) {
            echo '获取数据失败';
        }
        else{
            //直播终端
            $matches = $pc_json['matches'];
            $count = 0;
            foreach ($matches as $match){
                if ($count >= 30)
                    break;
                $mid = $match['mid'];
                $time = isset($match['time']) ? $match['time'] : 0;
                $now = time();
                if ($time == 0 ) {//只静态化赛前4小时内 的比赛终端。
                    continue;
                }
                $start_time = $time;//比赛时间
                $flg_1 = $start_time >= $now && $now + 5 * 60 * 60 >= $start_time;//开赛前1小时
                $flg_2 = $start_time <= $now && $start_time + 3 * 60 * 60  >= $now;//开赛后3小时
                $path = CommonTool::matchLivePathWithId($mid,$sport);
                $hasHtml = Storage::disk("public")->exists($path);
                //生成了就不自动加进去了
                if (!$hasHtml && ($flg_1 || $flg_2)) {
                    try {
                        echo '生成 ' . $sport . ' ' . $mid.'<br>';
                        LiveCommands::flushLiveDetailHtml($mid, $sport);
                    } catch (\Exception $exception) {
                        dump($exception);
                    }
                    $count++;
                }
            }
        }
    }

    public static function flushLiveDetailHtml($mid, $sport){
        $ch = curl_init();
        $url = asset('/api/static/live/' . $sport.'/'.$mid);
        echo $url . '<br>';
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);//8秒超时
        curl_exec ($ch);
        curl_close ($ch);
    }
}