<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/3/14
 * Time: 下午5:14
 */

namespace App\Http\Controllers\PC\Match;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;

class LiveController extends BaseController{
    public function liveDetail(Request $request,$first,$second,$mid){
        $this->html_var['match'] = MatchDetailController::matchDetailData($mid,'match');
        $this->html_var['tech'] = MatchDetailController::matchDetailData($mid,'tech');
        $this->html_var['roll'] = MatchDetailController::matchDetailData($mid,'roll');

        $ch = curl_init();
        $url = 'http://liaogou168.com/aik/lives/detailJson/'.$mid;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);//5秒超时
        $json = curl_exec ($ch);
        curl_close ($ch);
        $json = json_decode($json, true);
        $this->html_var['lives'] = $json['live']['channels'];
        $this->html_var['sport'] = 1;
        return view('pc.live.live',$this->html_var);
    }

    public function liveDetail_bk(Request $request,$first,$second,$mid){
        $this->html_var['match'] = MatchDetailController::matchDetailData($mid,'match',2);
        if (is_null($this->html_var['match']))
        {
            abort(404);
        }
        $this->html_var['tech'] = MatchDetailController::matchDetailData($mid,'tech',2);
        $this->html_var['roll'] = MatchDetailController::matchDetailData($mid,'roll',2);
        $this->html_var['players'] = MatchDetailController::matchDetailData($mid,'player',2);

        $ch = curl_init();
        $url = 'http://liaogou168.com/aik/lives/basketDetailJson/'.$mid;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);//5秒超时
        $json = curl_exec ($ch);
        curl_close ($ch);
        $json = json_decode($json, true);
        $this->html_var['lives'] = $json['live']['channels'];
        $this->html_var['sport'] = 2;
        return view('pc.live.live_bk',$this->html_var);
    }
}