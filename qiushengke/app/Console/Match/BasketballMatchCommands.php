<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/17
 * Time: 21:06
 */

namespace App\Console\Match;


use App\Models\QSK\Match\BasketLeague;
use App\Models\QSK\Match\BasketMatch;
use Illuminate\Console\Command;

class BasketballMatchCommands extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bb_matches_in_db:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '将1天前-3天内的篮球比赛录入数据库';

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
        $lid_array = BasketLeague::getBookLids();
        $lid = '';
        if (count($lid_array) > 0) {
            $lid = implode(',', $lid_array);
        }
        //
        $ch = curl_init();
        //$url = env('MATCH_URL') . "/api/qsk/basketball/matches.json?lid=" . $lid;
        $url = 'http://user.liaogou168.com:9020/api/qsk/basketball/matches.json?lid=' . $lid;
        //echo 'url = ' . $url . "\n";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 59);//5秒超时
        $json = curl_exec ($ch);
        curl_close ($ch);
        $matches = json_decode($json,true);
        if (!isset($matches)) {
            echo "返回数据为空\n";
            return;
        }

        foreach ($matches as $match) {
            $mid = $match['mid'];
            $qskMatch = BasketMatch::query()->find($mid);
            if (!isset($qskMatch)) {
                $qskMatch = new BasketMatch();
                $qskMatch->id = $mid;
            }
            $qskMatch->lid = $match['lid'];
            $qskMatch->win_lname = $match['win_lname'];
            $qskMatch->lname = $match['lname'];
            $qskMatch->betting_num = $match['betting_num'];
            $qskMatch->season = $match['season'];
            $qskMatch->time = $match['time'];
            $qskMatch->timehalf = $match['timehalf'];
            $qskMatch->status = $match['status'];
            $qskMatch->live_time_str = $match['live_time_str'];
            $qskMatch->hid = $match['hid'];
            $qskMatch->aid = $match['aid'];
            $qskMatch->hname = $match['hname'];
            $qskMatch->aname = $match['aname'];
            $qskMatch->hscore_1st = $match['hscore_1st'];
            $qskMatch->ascore_1st = $match['ascore_1st'];
            $qskMatch->hscore_2nd = $match['hscore_2nd'];
            $qskMatch->ascore_2nd = $match['ascore_2nd'];
            $qskMatch->hscore_3rd = $match['hscore_3rd'];
            $qskMatch->ascore_3rd = $match['ascore_3rd'];
            $qskMatch->hscore_4th = $match['hscore_4th'];
            $qskMatch->ascore_4th = $match['ascore_4th'];
            $qskMatch->h_ot = $match['h_ot'];
            $qskMatch->a_ot = $match['a_ot'];
            $qskMatch->created_at = $match['created_at'];
            $qskMatch->updated_at = $match['updated_at'];

            $qskMatch->save();
        }
    }

}