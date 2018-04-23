<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/29
 * Time: 10:28
 */

namespace App\Http\Controllers\App\Detail;


use App\Http\Controllers\App\Model\AppCommonResponse;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Controllers\PC\Match\MatchDetailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class FootballController extends BaseController
{
    //足球比赛终端
    public function detail(Request $request) {
        $cdn = $this->html_var['cdn'];

        $mid = $request->input("id");
        $match = MatchDetailController::matchDetailData($mid, 'match');
        if (is_null($match)) {
            return Response::json(AppCommonResponse::createAppCommonResponse(500, '参数错误'));
        }

        $first = substr($mid, 0, 2);
        $second = substr($mid, 2, 4);

        $reset = $match;
        //终端底部tab
        if ($match['status'] == 0) {
            $reset['tabs'] = [
                ["name"=>"分析", "url"=>$cdn."/wap/match/foot/detail/$first/$second/$mid/analyse.html"],
                ["name"=>"赛况", "url"=>$cdn."/wap/match/foot/detail/$first/$second/$mid/base.html"],
                ["name"=>"指数", "url"=>$cdn."/wap/match/foot/detail/$first/$second/$mid/odd.html"],
                ["name"=>"同赔", "url"=>$cdn."/wap/match/foot/detail/$first/$second/$mid/same_odd.html"],
                ["name"=>"角球", "url"=>$cdn."/wap/match/foot/detail/$first/$second/$mid/corner.html"],
            ];
        } else {
            $reset['tabs'] = [
                ["name"=>"赛况", "url"=>$cdn."/wap/match/foot/detail/$first/$second/$mid/base.html"],
                ["name"=>"分析", "url"=>$cdn."/wap/match/foot/detail/$first/$second/$mid/analyse.html"],
                ["name"=>"指数", "url"=>$cdn."/wap/match/foot/detail/$first/$second/$mid/odd.html"],
                ["name"=>"同赔", "url"=>$cdn."/wap/match/foot/detail/$first/$second/$mid/same_odd.html"],
                ["name"=>"角球", "url"=>$cdn."/wap/match/foot/detail/$first/$second/$mid/corner.html"],
            ];
        }

        return Response::json(AppCommonResponse::createAppCommonResponse(0, '', $reset, false));
    }
}