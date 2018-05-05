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

class BasketballController extends BaseController
{
    //足球比赛终端
    public function detail(Request $request) {
        $cdn = env('HOST');

        $mid = $request->input("id");
        $match = MatchDetailController::matchDetailData($mid, 'match', 2);
        if (is_null($match)) {
            return Response::json(AppCommonResponse::createAppCommonResponse(500, '参数错误'));
        }

        $first = substr($mid, 0, 2);
        $second = substr($mid, 2, 2);

        $reset = $match;
        //终端底部tab
        if ($match['status'] == 0) {
            $reset['tabs'] = [
                ["name"=>"分析", "url"=>$cdn."/wap/match/basket/detail/$first/$second/$mid/analyse.html"],
                ["name"=>"赛况", "url"=>$cdn."/wap/match/basket/detail/$first/$second/$mid/base.html"],
                ["name"=>"指数", "url"=>$cdn."/wap/match/basket/detail/$first/$second/$mid/odd.html"],
            ];
        } else {
            $reset['tabs'] = [
                ["name"=>"赛况", "url"=>$cdn."/wap/match/basket/detail/$first/$second/$mid/base.html"],
                ["name"=>"分析", "url"=>$cdn."/wap/match/basket/detail/$first/$second/$mid/analyse.html"],
                ["name"=>"指数", "url"=>$cdn."/wap/match/basket/detail/$first/$second/$mid/odd.html"],
            ];
        }

        return Response::json(AppCommonResponse::createAppCommonResponse(0, '', $reset, false));
    }
}