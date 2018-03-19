<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/17
 * Time: 21:06
 */

namespace App\Console\Match;


use App\Models\QSK\Match\League;
use App\Models\QSK\Match\Match;
use Illuminate\Console\Command;

class FootballMatchCommands extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'football_matches_in_db:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '将1天前-3天内的比赛录入数据库';

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
        $lid_array = League::getBookLids();
        $lid = '';
        if (count($lid_array) > 0) {
            $lid = implode(',', $lid_array);
        }
        //
        $ch = curl_init();
        //$url = env('MATCH_URL') . "/api/qsk/football/matches.json?lid=" . $lid;
        $url = 'http://user.liaogou168.com:9020/api/qsk/football/matches.json?lid=' . $lid;
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
            $qskMatch = Match::query()->find($mid);
            if (!isset($qskMatch)) {
                $qskMatch = new Match();
                $qskMatch->id = $mid;
            }
            $qskMatch->lid = $match['lid'];
            $qskMatch->lsid = $match['lsid'];
            $qskMatch->season = $match['season'];
            $qskMatch->stage = $match['stage'];
            $qskMatch->round = $match['round'];
            $qskMatch->time = $match['time'];
            $qskMatch->timehalf = $match['timehalf'];
            $qskMatch->status = $match['status'];
            $qskMatch->hid = $match['hid'];
            $qskMatch->aid = $match['aid'];
            $qskMatch->hname = $match['hname'];
            $qskMatch->aname = $match['aname'];
            $qskMatch->hscore = $match['hscore'];
            $qskMatch->ascore = $match['ascore'];
            $qskMatch->hscorehalf = $match['hscorehalf'];
            $qskMatch->ascorehalf = $match['ascorehalf'];
            $qskMatch->hrank = $match['hrank'];
            $qskMatch->arank = $match['arank'];
            $qskMatch->neutral = $match['neutral'];
            $qskMatch->genre = $match['genre'];
            $qskMatch->has_lineup = $match['has_lineup'];
            $qskMatch->win_hname = $match['win_hname'];
            $qskMatch->win_aname = $match['win_aname'];
            $qskMatch->win_lname = $match['win_lname'];
            $qskMatch->lname = $match['lname'];
            $qskMatch->betting_num = $match['betting_num'];
            //$qskMatch->h_icon = $match['h_icon'];
            //$qskMatch->a_icon = $match['a_icon'];

            $qskMatch->save();
        }
    }

}