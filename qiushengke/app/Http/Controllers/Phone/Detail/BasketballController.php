<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/29
 * Time: 10:28
 */

namespace App\Http\Controllers\Phone\Detail;


use App\Http\Controllers\Controller as BaseController;
use App\Http\Controllers\PC\Match\MatchDetailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BasketballController extends BaseController
{
    const tabs = ["base", "analyse", "odd"];
/**
 * 通过请求自己的链接静态化pc终端，主要是解决 文件权限问题。
 * @param $mid
 */
    public static function curlToHtml($mid) {
        $ch = curl_init();
        $url = asset('/api/static/wap/basketball/detail/' . $mid);
        echo $url;
        if (is_null($url))
            return;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 25);
        curl_exec ($ch);
        curl_close ($ch);
    }

    /**
     * 静态化
     * @param Request $request
     * @param $mid int
     */
    public function staticMatchDetailBK(Request $request,$mid){
        $first = substr($mid,0,2);
        $second = substr($mid,2,2);
        $html = $this->detail($request,$first,$second,$mid);
        if (isset($html)) {
            Storage::disk("public")->put("/wap/match/basket/detail/" . $first . "/" . $second . "/" . $mid . ".html", $html);
        }
        //比赛详情cell
        foreach (self::tabs as $tab) {
            $tabHtml = $this->detailCell($request, $first, $second, $mid, $tab);
            if (isset($tabHtml)) {
                Storage::disk("public")->put("/wap/match/basket/detail/" . $first . "/" . $second . "/" . $mid . "/". $tab .".html", $tabHtml);
            }
        }
    }

    /**
     * 篮球终端
     * @param Request $request
     * @param $sub1
     * @param $sub2
     * @param $mid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail(Request $request, $sub1, $sub2, $mid){
        $match = MatchDetailController::matchDetailData($mid, 'match', 2);
        if (empty($match)) {
            abort(404);
        }
        $result = array();
        $result['match'] = $match;
        //基本数据
        $result['analyse'] = MatchDetailController::matchDetailData($mid, 'analyse', 2);
        //统计
        $result['tech'] = MatchDetailController::matchDetailData($mid, 'tech', 2);
        //阵容;
        $result['players'] = MatchDetailController::matchDetailData($mid, 'player', 2);
        //盘口
        $result['odds'] = MatchDetailController::matchDetailData($mid, 'odd', 2);
        $result['first'] = substr($mid, 0, 2);
        $result['second'] = substr($mid, 2, 2);
        $result['mid'] = $mid;
        $this->html_var = array_merge($this->html_var,$result);

        return view('phone.detail.basketball.match_bk', $this->html_var);
    }

    public function detailCell(Request $request, $sub1, $sub2, $mid, $tab) {
        $match = MatchDetailController::matchDetailData($mid, 'match', 2);
        if (empty($match)) {
            abort(404);
        }
        $result = array();
        $result['match'] = $match;
        $views = "";
        switch ($tab) {
            case "base":
                $result['tech'] = MatchDetailController::matchDetailData($mid, 'tech', 2);
                $views = 'phone.detail.basketball.cell.match_cell';
                break;
            case "analyse":
                $result['analyse'] = MatchDetailController::matchDetailData($mid, 'analyse', 2);
                $result['odds'] = MatchDetailController::matchDetailData($mid, 'odd', 2);
                $views = 'phone.detail.basketball.cell.data_cell';
                break;
            case "odd":
                $result['odds'] = MatchDetailController::matchDetailData($mid, 'odd', 2);
                $views = 'phone.detail.football.cell.odd_cell';
                break;
        }
        $result['first'] = substr($mid, 0, 2);
        $result['second'] = substr($mid, 2, 2);
        $result['mid'] = $mid;
        $result['show'] = true;
        $this->html_var = array_merge($this->html_var,$result);

        $this->html_var['views'] = view($views, $this->html_var);
        return view("phone.detail.basketball.match_bk_cell", $this->html_var);
    }

    /**
     * 获取足球终端 Data 模块的赔率
     * @param Request $request
     * @param $sub1
     * @param $sub2
     * @param $mid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function dataOdd(Request $request, $sub1, $sub2, $mid) {
        $result['odds'] = MatchDetailController::matchDetailData($mid, 'odd', 2);
        if (!isset($result['odds'])) {
            return;
        }
        $result['isBasket'] = true;
        $this->html_var = array_merge($this->html_var,$result);

        $html['odd_html'] =  response(view('phone.detail.football.cell.data_odd_cell', $this->html_var))->getContent();
        $html['index_html'] = response(view('phone.detail.football.cell.odd_index_cell', $this->html_var))->getContent();
        return response()->json($html);
    }
}