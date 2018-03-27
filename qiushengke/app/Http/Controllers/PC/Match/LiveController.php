<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/3/14
 * Time: 下午5:14
 */

namespace App\Http\Controllers\PC\Match;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Controllers\PC\CommonTool;
use App\Http\Controllers\PC\FileTool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LiveController extends BaseController{
    /**
     * 静态化
     * @param Request $request
     * @param $sport
     * @param $mid
     */
    public function staticLiveDetail(Request $request,$sport,$mid){
        $first = substr($mid,0,2);
        $second = substr($mid,2,2);
        if ($sport == 1){
            $html = $this->liveDetail($request,$first,$second,$mid);
            if (isset($html) && strlen($html) > 0) {
                $path = CommonTool::matchLivePathWithId($mid,$sport);
                Storage::disk("public")->put($path, $html);
            }
        }
        else{
            $html = $this->liveDetail_bk($request,$first,$second,$mid);
            if (isset($html) && strlen($html) > 0) {
                $path = CommonTool::matchLivePathWithId($mid,$sport);
                Storage::disk("public")->put($path, $html);
            }
        }
    }

    /**
     * 足球直播终端
     * @param Request $request
     * @param $first
     * @param $second
     * @param $mid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function liveDetail(Request $request,$first,$second,$mid){
        $this->html_var['match'] = MatchDetailController::matchDetailData($mid,'match');
        $this->html_var['tech'] = MatchDetailController::matchDetailData($mid,'tech');
        $this->html_var['roll'] = MatchDetailController::matchDetailData($mid,'roll');
        $this->html_var['sport'] = 1;
        $this->html_var['lives'] = array(['name'=>'bj','id'=>18528]);
        return view('pc.live.live',$this->html_var);
    }

    /**
     * 篮球直播终端
     * @param Request $request
     * @param $first
     * @param $second
     * @param $mid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function liveDetail_bk(Request $request,$first,$second,$mid){
        $this->html_var['match'] = MatchDetailController::matchDetailData($mid,'match',2);
        if (is_null($this->html_var['match']))
        {
            abort(404);
        }
        $this->html_var['tech'] = MatchDetailController::matchDetailData($mid,'tech',2);
        $this->html_var['roll'] = MatchDetailController::matchDetailData($mid,'roll',2);
        $this->html_var['players'] = MatchDetailController::matchDetailData($mid,'player',2);
        $this->html_var['sport'] = 2;
        $this->html_var['lives'] = array();
        return view('pc.live.live_bk',$this->html_var);
    }

    /**
     * 播放器
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function player(Request $request){
        return view('pc.live.player',$this->html_var);
    }
}