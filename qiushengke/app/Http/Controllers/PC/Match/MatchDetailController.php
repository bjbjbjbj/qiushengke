<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/3/12
 * Time: 上午11:30
 */
namespace App\Http\Controllers\PC\Match;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;

class MatchDetailController extends BaseController{
    public function matchDetail(Request $request,$mid){
        if (is_null($mid)) {
            return abort(404);
        }
        $ch = curl_init();
        $first = substr($mid,0,2);
        $second = substr($mid,2,2);
        $url = 'http://match.liaogou168.com/static/terminal/1/'.$first.'/'.$second.'/'.$mid.'/match.json';
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);//5秒超时
        $match = curl_exec ($ch);
        curl_close ($ch);
        $match = json_decode($match,true);
        if (empty($match)) {
            abort(404);
        }
        $result = array();
        $result['match'] = $match;

        //基本数据
        $url = 'http://match.liaogou168.com/static/terminal/1/'.$first.'/'.$second.'/'.$mid.'/analyse.json';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);//5秒超时
        $analyse = curl_exec ($ch);
        curl_close ($ch);
        $analyse = json_decode($analyse,true);
        $result['analyse'] = $analyse;

        //统计
        $url = 'http://match.liaogou168.com/static/terminal/1/'.$first.'/'.$second.'/'.$mid.'/tech.json';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);//5秒超时
        $tech = curl_exec ($ch);
        curl_close ($ch);
        $tech = json_decode($tech,true);
        $result['tech'] = $tech;

        //阵容
        $url = 'http://match.liaogou168.com/static/terminal/1/'.$first.'/'.$second.'/'.$mid.'/lineup.json';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);//5秒超时
        $lineup = curl_exec ($ch);
        curl_close ($ch);
        $lineup = json_decode($lineup,true);
        $result['lineup'] = $lineup;

        $url = 'http://match.liaogou168.com/static/terminal/1/'.$first.'/'.$second.'/'.$mid.'/analyse.json';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);//5秒超时
        $analyse = curl_exec ($ch);
        curl_close ($ch);
        $analyse = json_decode($analyse,true);
        $result['analyse'] = $analyse;

        dump($result);

        $this->html_var = array_merge($this->html_var,$result);
//        dump($this->html_var);
        return view('pc.match_detail.match_detail',$this->html_var);
    }
}