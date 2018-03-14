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
        $match = $this->matchDetailData($mid, 'match');
        if (empty($match)) {
            abort(404);
        }
        $result = array();
        $result['match'] = $match;

        //基本数据
        $result['analyse'] = $this->matchDetailData($mid, 'analyse');

        //统计
        $result['tech'] = $this->matchDetailData($mid, 'tech');

        //阵容;
        $result['lineup'] = $this->matchDetailData($mid, 'lineup');

        dump($result);

        $this->html_var = array_merge($this->html_var,$result);
//        dump($this->html_var);
        return view('pc.match_detail.match_detail',$this->html_var);
    }

    public function basketDetail(Request $request,$first,$second,$mid) {
        if (is_null($mid)) {
            return abort(404);
        }
        $match = $this->matchDetailData($mid, 'match', 2);
        if (empty($match)) {
            abort(404);
        }
        $result = array();
        $result['match'] = $match;

        //基本数据
        $result['analyse'] = $this->matchDetailData($mid, 'analyse', 2);

        //统计
        $result['tech'] = $this->matchDetailData($mid, 'tech', 2);

        //球员统计
        $result['players'] = $this->matchDetailData($mid, 'player', 2);

        $this->html_var = array_merge($this->html_var,$result);
//        dump($this->html_var);
        return view('pc.match_detail.match_detail_bk',$this->html_var);
    }

    /**
     * 终端页数据
     * @param $id
     * @param $name
     * @param $sport
     * @return array
     */
    private function matchDetailData($id, $name, $sport = 1){
        $ch = curl_init();
        $url = env('MATCH_URL')."/static/terminal/$sport/".substr($id,0,2)."/".substr($id,2,2)."/$id/$name.json";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);//5秒超时
        $json = curl_exec ($ch);
        curl_close ($ch);
        $json = json_decode($json, true);

        return $json;
    }
}