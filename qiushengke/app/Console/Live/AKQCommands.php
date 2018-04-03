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
use App\Models\QSK\Match\BasketMatch;
use App\Models\QSK\Match\Match;
use App\Models\QSK\Match\MatchLive;
use App\Models\QSK\Match\MatchLiveChannel;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AKQCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'akq_data:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '爱看球同步比赛频道,5分钟一次';

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
        $json = FileTool::curlData('http://liaogou168.com/qiushengke/api/matchList',10);
        if ($json['code'] == 0){
            $matches = $json['data']['matches'];
            foreach ($matches as $key=>$datas){
                foreach ($datas as $match){
                    //足球
                    if ($match['sport'] == 1){
                        $qskm = Match::where('win_id',$match['win_id'])->first();
                        if (isset($qskm)) {
                            MatchLiveChannel::saveSpiderChannel($qskm['id'],MatchLive::kSportFootball,MatchLiveChannel::kTypeOther,$match['channel_url'],1,MatchLiveChannel::kPlatformAll,MatchLiveChannel::kPlayerIFrame,'比赛直播', MatchLiveChannel::kShow, MatchLiveChannel::kIsNotPrivate, MatchLiveChannel::kUseAll);
                        }
                    }
                    //篮球
                    else{
                        $qskm = BasketMatch::where('win_id',$match['win_id'])->first();
                        if (isset($qskm)) {
                            MatchLiveChannel::saveSpiderChannel($qskm['id'],MatchLive::kSportBasketball,MatchLiveChannel::kTypeOther,$match['channel_url'],1,MatchLiveChannel::kPlatformAll,MatchLiveChannel::kPlayerIFrame,'比赛直播', MatchLiveChannel::kShow, MatchLiveChannel::kIsNotPrivate, MatchLiveChannel::kUseAll);
                        }
                    }
                }
            }
        }
    }
}