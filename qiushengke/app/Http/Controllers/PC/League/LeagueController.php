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

    const footLeagueIcons = [
        46=>'https://gss2.bdstatic.com/-fo3dSag_xI4khGkpoWK1HF6hhy/baike/w%3D268%3Bg%3D0/sign=0b42678f1b3853438ccf8027ab28d743/0e2442a7d933c895ef2b0c98da1373f0830200ff.jpg',
        31=>'https://gss2.bdstatic.com/-fo3dSag_xI4khGkpoWK1HF6hhy/baike/w%3D268%3Bg%3D0/sign=8b5f7ac998ef76c6d0d2fc2da52d9ac7/2f738bd4b31c8701cf091a0e2f7f9e2f0608ff9f.jpg',
        26=>'https://gss0.bdstatic.com/94o3dSag_xI4khGkpoWK1HF6hhy/baike/w%3D268%3Bg%3D0/sign=3b57e13d5a0fd9f9a017526f1d16b317/d31b0ef41bd5ad6e0d6baaab8acb39dbb7fd3ce5.jpg',
        29=>'https://gss0.bdstatic.com/94o3dSag_xI4khGkpoWK1HF6hhy/baike/w%3D268%3Bg%3D0/sign=dd31376f9bef76c6d0d2fc2da52d9ac7/2f738bd4b31c8701996757a82c7f9e2f0608ffaf.jpg',
        11=>'https://gss0.bdstatic.com/-4o3dSag_xI4khGkpoWK1HF6hhy/baike/w%3D268%3Bg%3D0/sign=227ac2f0a2773912c4268267c022e125/cf1b9d16fdfaaf5127a705f2875494eef01f7a33.jpg',
        8=>'https://gss2.bdstatic.com/9fo3dSag_xI4khGkpoWK1HF6hhy/baike/w%3D268%3Bg%3D0/sign=257e40a291504fc2a25fb703dde6802c/b151f8198618367ad0e4b37724738bd4b31ce52f.jpg',
        139=>'https://gss3.bdstatic.com/-Po3dSag_xI4khGkpoWK1HF6hhy/baike/w%3D268%3Bg%3D0/sign=7cc73b66104c510faec4e51c58624210/7c1ed21b0ef41bd556ed0d5a5bda81cb38db3de6.jpg',
        73=>'https://gss2.bdstatic.com/-fo3dSag_xI4khGkpoWK1HF6hhy/baike/w%3D268%3Bg%3D0/sign=9a8c6d0c59df8db1bc2e7b623118ba69/7af40ad162d9f2d391c0487baeec8a136227cc55.jpg',
        57=>'https://gss2.bdstatic.com/-fo3dSag_xI4khGkpoWK1HF6hhy/baike/w%3D268%3Bg%3D0/sign=85d6561f3cd12f2ece05a96677f9b25f/a8773912b31bb051062384e03c7adab44bede0f7.jpg'
    ];
    const basketLeagueIcons = [
        1=>'https://gss2.bdstatic.com/9fo3dSag_xI4khGkpoWK1HF6hhy/baike/crop%3D0%2C0%2C600%2C396%3Bc0%3Dbaike80%2C5%2C5%2C80%2C26/sign=1d7f38adc0fcc3cea08f9373af75fab8/962bd40735fae6cd21e61fee07b30f2443a70fea.jpg',
        4=>'https://gss2.bdstatic.com/-fo3dSag_xI4khGkpoWK1HF6hhy/baike/c0%3Dbaike92%2C5%2C5%2C92%2C30/sign=4cfba9067a8da9775a228e79d138937c/96dda144ad345982b4dc8fa705f431adcbef84b4.jpg',
//        2=>'https://gss3.bdstatic.com/-Po3dSag_xI4khGkpoWK1HF6hhy/baike/c0%3Dbaike80%2C5%2C5%2C80%2C26/sign=283a8bccffdcd100d991f07313e22c75/622762d0f703918fb3111c0b563d269758eec42a.jpg',
        89=>'https://gss0.bdstatic.com/94o3dSag_xI4khGkpoWK1HF6hhy/baike/w%3D268%3Bg%3D0/sign=f9b2cd476681800a6ee58e08890e54c7/09fa513d269759ee15875403b8fb43166c22df26.jpg',
    ];

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