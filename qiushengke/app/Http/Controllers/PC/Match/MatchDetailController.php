<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/3/12
 * Time: 上午11:30
 */
namespace App\Http\Controllers\PC\Match;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Controllers\PC\FileTool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MatchDetailController extends BaseController{
    /**
     * 通过请求自己的链接静态化pc终端，主要是解决 文件权限问题。
     * @param $mid
     * @param $sport
     */
    public static function curlToHtml($mid,$sport = 1) {
        $ch = curl_init();
        if (1 == $sport)
            $url = asset('/api/static/football/detail/' . $mid);
        else if(2 == $sport){
            $url = asset('/api/static/basketball/detail/' . $mid);
        }
        echo $url;
        if (is_null($url))
            return;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 25);
        curl_exec ($ch);
        curl_close ($ch);
    }

    public static function staticHour(){
        $url = asset('/api/static/football/hour');
        if (!is_null($url)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 25);
            curl_exec($ch);
            curl_close($ch);
        }

        $url = asset('/api/static/error');
        if (!is_null($url)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 25);
            curl_exec($ch);
            curl_close($ch);
        }
    }

    /**
     * 静态化
     * @param Request $request
     */
    public function staticOddDetail(Request $request){
        $html = $this->oddDetail($request);
        if (isset($html) && strlen($html) > 0)
            Storage::disk("public")->put("/match/foot/odd.html", $html);
    }

    /**
     * 静态化
     * @param Request $request
     * @param $mid int
     */
    public function staticMatchDetail(Request $request,$mid){
        $first = substr($mid,0,2);
        $second = substr($mid,2,2);
        $html = $this->matchDetail($request,$first,$second,$mid);
        if (isset($html) && strlen($html) > 0)
            Storage::disk("public")->put("/match/foot/".$first."/".$second."/".$mid.".html", $html);
    }

    /**
     * 静态化
     * @param Request $request
     * @param $mid int
     */
    public function staticMatchDetailBK(Request $request,$mid){
        $first = substr($mid,0,2);
        $second = substr($mid,2,2);
        $html = $this->basketDetail($request,$first,$second,$mid);
        if (isset($html))
            Storage::disk("public")->put("/match/basket/".$first."/".$second."/".$mid.".html", $html);
    }

    /**
     * 赔率终端
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function oddDetail(Request $request){
        return view('pc.match_detail.odd_detail',$this->html_var);
    }

    /**
     * 足球终端
     * @param Request $request
     * @param $first
     * @param $second
     * @param $mid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
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

        $this->html_var = array_merge($this->html_var,$result);
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

        //赔率
        $odds = $this->matchDetailData($mid, 'odd', 2);
        if (isset($odds) && count($odds) > 0) {
            ksort($odds);
        }
        $result['odds'] = $odds;

        $this->html_var = array_merge($this->html_var,$result);
        return view('pc.match_detail.match_detail_bk',$this->html_var);
    }

    /**
     * 终端页数据
     * @param $id
     * @param $name
     * @param $sport
     * @return array
     */
    public static function matchDetailData($id, $name, $sport = 1){
        $json = FileTool::matchDetailJson(substr($id,0,2),substr($id,2,2),$id,$sport,$name);
        return $json;
    }

    const locations = ['G' => '后卫', 'F' => '前锋', 'C' => '中锋'];

    /**
     * 篮球位置转换
     * @param $index
     * @return mixed|string
     */
    public static function getPlayerLocationCn($index) {
        $locationStr = "";
        if (array_key_exists($index, self::locations)) {
            $locationStr = self::locations[$index];
        }
        return $locationStr;
    }
}