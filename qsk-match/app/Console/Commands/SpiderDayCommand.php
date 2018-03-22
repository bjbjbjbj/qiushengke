<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/6
 * Time: 18:30
 */

namespace App\Console\Commands;


use App\Http\Controllers\Ballbar\SpiderBallbarController;
use App\Http\Controllers\TTZB\SpiderTTZBController;
use App\Http\Controllers\WinSpider\SpiderController;
use App\Models\LiaoGouModels\BasketMatchesAfter;
use App\Models\LiaoGouModels\MatchesAfter;
use App\Models\LiaoGouModels\MatchLive;
use App\Models\LiaoGouModels\MatchLiveChannel;
use Illuminate\Console\Command;

class SpiderDayCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'day:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '一天一次';

    /**
     * Create a new command instance.
     *
     * @return void
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
        //篮球 nba cba
        $matches = BasketMatchesAfter::whereIn('lid',[1,4])
            ->where('status',0)
            ->orderby('time','asc')
            ->get();
        foreach ($matches as $match){
            $matchLive = MatchLive::where('match_id',$match->id)
                ->where('sport',MatchLive::kSportBasketball)
                ->first();
            if (is_null($matchLive)){
                $matchLive = new MatchLive();
                $matchLive->match_id = $match->id;
                $matchLive->sport = MatchLive::kSportBasketball;
                $matchLive->save();
                $matchLive = MatchLive::where('match_id',$match->id)
                    ->where('sport',MatchLive::kSportBasketball)
                    ->first();
            }

            $query = MatchLiveChannel::query()->where(function ($orQuery) {
                $orQuery->where('platform', MatchLiveChannel::kPlatformAll);
                $orQuery->orWhere('platform', MatchLiveChannel::kPlatformPC);
            });
            $query->where('live_id', $matchLive->id);
            $query->where('show', MatchLiveChannel::kShow);
            $query->selectRaw('*, ifnull(od, 99)');
            $query->orderBy('od');
            $channels = $query->limit(1)->get();
            //有的不用填
            if (count($channels) == 0){
                echo '填 '. $match->win_lname .' ' . $match->hname.' '. $match->aname.'</br>';
                $path = 'rtmp://wv4.tp33.net:1935/sat/cn021';
                MatchLiveChannel::saveSpiderChannel($match->id,MatchLive::kSportBasketball,MatchLiveChannel::kTypeOther,$path,10,MatchLiveChannel::kPlatformAll,MatchLiveChannel::kPlayerRTMP,'赛事直播', MatchLiveChannel::kShow, 2);//有版权
            }
            else{
                echo $match->win_lname .' ' . $match->hname.' '. $match->aname.'</br>';
            }
        }

        //足球 英超、意甲、德甲、法甲、西甲、欧冠、欧联、中超、亚冠
        $matches = MatchesAfter::whereIn('lid',[8,11,26,29,31,46,73,77,139])
            ->where('status',0)
            ->orderby('time','asc')
            ->get();
        foreach ($matches as $match){
            $matchLive = MatchLive::where('match_id',$match->id)
                ->where('sport',MatchLive::kSportFootball)
                ->first();
            if (is_null($matchLive)){
                $matchLive = new MatchLive();
                $matchLive->match_id = $match->id;
                $matchLive->sport = MatchLive::kSportFootball;
                $matchLive->save();
                $matchLive = MatchLive::where('match_id',$match->id)
                    ->where('sport',MatchLive::kSportFootball)
                    ->first();
            }
            $query = MatchLiveChannel::query()->where(function ($orQuery) {
                $orQuery->where('platform', MatchLiveChannel::kPlatformAll);
                $orQuery->orWhere('platform', MatchLiveChannel::kPlatformPC);
            });
            $query->where('live_id', $matchLive->id);
            $query->where('show', MatchLiveChannel::kShow);
            $query->selectRaw('*, ifnull(od, 99)');
            $query->orderBy('od');
            $channels = $query->limit(1)->get();
            if (count($channels) == 0) {
                $path = 'rtmp://wv4.tp33.net:1935/sat/cn021';
                MatchLiveChannel::saveSpiderChannel($match->id, MatchLive::kSportFootball, MatchLiveChannel::kTypeOther, $path, 10, MatchLiveChannel::kPlatformAll, MatchLiveChannel::kPlayerRTMP, '赛事直播', MatchLiveChannel::kShow, 2);//有版权
                echo '填 ' . $match->win_lname .' ' . $match->hname.' '. $match->aname.'</br>';
            }
            else{
                echo $match->win_lname .' ' . $match->hname.' '. $match->aname.'</br>';
            }
        }
    }

}