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

class BasketballController extends BaseController
{

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
        dump($result['odds']);
        $this->html_var = array_merge($this->html_var,$result);
        return view('phone.detail.football.cell.data_odd_cell', $this->html_var);
    }

    public function typeOdd(Request $request, $sub1, $sub2, $mid) {

    }

}