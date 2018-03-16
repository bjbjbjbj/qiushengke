<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/3/9
 * Time: 上午10:33
 */

namespace App\Http\Controllers\PC\League;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;

class LeagueController extends BaseController{

    private function getLeagueData($lid,$sport=1) {
        $ch = curl_init();
        $url = env('MATCH_URL')."/static/league/$sport/$lid.json";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);//5秒超时
        $pc_json = curl_exec ($ch);
        curl_close ($ch);
        $pc_json = json_decode($pc_json,true);

        return $pc_json;
    }

    /********** 足球 ************/
    public function league(Request $request,$lid){
        $pc_json = $this->getLeagueData($lid);
        if (!empty($pc_json)) {
            $result = $pc_json;
            //联赛,杯赛
            if ($pc_json['league']['type'] == 1) {
                $this->html_var = array_merge($this->html_var,$result);
                return view('pc.league.league', $this->html_var);
            }
            else {
                $this->html_var = array_merge($this->html_var,$result);
                return view('pc.league.cup_league', $this->html_var);
            }
        }
        else {
            return abort(404);
        }
    }

    public function leagueSeason(Request $request,$season,$lid){
        $pc_json = $this->getLeagueData($lid);
        if (!empty($pc_json)) {
            $result = $pc_json;
            //联赛,杯赛
            if ($pc_json['league']['type'] == 1) {
                $this->html_var = array_merge($this->html_var,$result);
                return view('pc.league.league', $this->html_var);
            }
            else {
                $this->html_var = array_merge($this->html_var,$result);
                return view('pc.league.cup_league', $this->html_var);
            }
        }
        else {
            return abort(404);
        }
    }

    /************ 篮球 *************/
    public function leagueBK(Request $request,$lid){
        $pc_json = $this->getLeagueData($lid,2);
        if (!empty($pc_json)) {
            $result = $pc_json;
            $result['start'] = date_create()->getTimestamp();
            $result['lid'] = $lid;
            //联赛,杯赛
            if ($pc_json['league']['type'] == 1) {
                $this->html_var = array_merge($this->html_var,$result);
                return view('pc.league.league_bk', $this->html_var);
            }
            else {
                $this->html_var = array_merge($this->html_var,$result);
                return view('pc.league.league_bk', $this->html_var);
            }
        }
        else {
            return abort(404);
        }
    }

    public function leagueBKWithDate(Request $request,$lid){
        $start = $request->input('date');
        if (is_null($start))
        {
            return null;
        }
        $ch = curl_init();
        $url = env('MATCH_URL')."/static/league/2/$lid?date=" . $start;
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);//5秒超时
        $pc_json = curl_exec ($ch);
        curl_close ($ch);
        $pc_json = json_decode($pc_json,true);
        if (!empty($pc_json)) {
            $result = $pc_json;
            return view('pc.league.league_schedule_bk',array('matches'=>$result));
        }
        else {
            return null;
        }
    }
}