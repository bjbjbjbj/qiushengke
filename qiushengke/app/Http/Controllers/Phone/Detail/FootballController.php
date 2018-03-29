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

class FootballController extends BaseController
{

    /**
     * 足球终端
     * @param Request $request
     * @param $sub1
     * @param $sub2
     * @param $mid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail(Request $request, $sub1, $sub2, $mid){
        $match = MatchDetailController::matchDetailData($mid, 'match');
        if (empty($match)) {
            abort(404);
        }
        $result = array();
        $result['match'] = $match;
//dump($result['match']);
        //基本数据
        $result['analyse'] = MatchDetailController::matchDetailData($mid, 'analyse');
        //统计
        $result['tech'] = MatchDetailController::matchDetailData($mid, 'tech');
        //阵容;
        $result['lineup'] = MatchDetailController::matchDetailData($mid, 'lineup');
        $this->html_var = array_merge($this->html_var,$result);
        return view('phone.detail.football.match', $this->html_var);
    }

}