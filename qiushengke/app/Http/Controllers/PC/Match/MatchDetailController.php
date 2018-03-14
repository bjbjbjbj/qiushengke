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
    public function matchDetail(Request $request,$first,$second,$mid){
        if (is_null($mid)) {
            return abort(404);
        }

        $mfirst = substr($mid,0,2);
        $msecond = substr($mid,2,2);
        if ($first != $mfirst || $second != $msecond){
            return abort(404);
        }

        $url = 'http://match.liaogou168.com/static/terminal/1/'.$first.'/'.$second.'/'.$mid.'/match.json';
        $data = $this->_getDataWithUrl($url);
        if (empty($data)) {
            abort(404);
        }
        $result = array();
        $result['match'] = $data;

        //基本数据
        $url = 'http://match.liaogou168.com/static/terminal/1/'.$first.'/'.$second.'/'.$mid.'/analyse.json';
        $data = $this->_getDataWithUrl($url);
        $result['analyse'] = $data;

        //统计
        $url = 'http://match.liaogou168.com/static/terminal/1/'.$first.'/'.$second.'/'.$mid.'/tech.json';
        $data = $this->_getDataWithUrl($url);
        $result['tech'] = $data;

        //阵容
        $url = 'http://match.liaogou168.com/static/terminal/1/'.$first.'/'.$second.'/'.$mid.'/lineup.json';
        $data = $this->_getDataWithUrl($url);
        $result['lineup'] = $data;

        $url = 'http://match.liaogou168.com/static/terminal/1/'.$first.'/'.$second.'/'.$mid.'/analyse.json';
        $data = $this->_getDataWithUrl($url);
        $result['analyse'] = $data;
        $this->html_var = array_merge($this->html_var,$result);
        return view('pc.match_detail.match_detail',$this->html_var);
    }

    private function _getDataWithUrl($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);//5秒超时
        $analyse = curl_exec ($ch);
        curl_close ($ch);
        $analyse = json_decode($analyse,true);
        return $analyse;
    }
}