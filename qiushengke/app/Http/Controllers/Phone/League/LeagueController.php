<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/3/9
 * Time: 上午10:33
 */

namespace App\Http\Controllers\Phone\League;

use App\Http\Controllers\Controller as BaseController;
use App\Models\QSK\Article\LArticle;
use App\Models\QSK\Subject\SubjectLeague;
use App\Models\QSK\Video\HotVideo;
use App\Models\QSK\Video\HotVideoType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LeagueController extends BaseController{
    private function getLeagueData($lid,$sport=1) {
        $ch = curl_init();
        $url = env('MATCH_URL')."/static/league/$sport/$lid.json";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);//5秒超时
        $pc_json = curl_exec ($ch);
        curl_close ($ch);
        $pc_json = json_decode($pc_json,true);
        return $pc_json;
    }

    /************* 静态化 ***************/
    /**
     * 静态化
     * @param Request $request
     */
    public function staticFoot(Request $request){
        if (Storage::disk("public")->exists('/league/foot/sub.json')) {
            $json_str = Storage::disk("public")->get('/league/foot/sub.json');
            $json = json_decode($json_str, true);
            if ($json && strlen($json_str) > 0) {
                $footLeague = $json;
                foreach ($footLeague as $item){
                    LeagueController::flushLiveDetailHtml($item['id'],1);
                }
            }
        }
    }

    public function staticBasket(Request $request){
        if (Storage::disk("public")->exists('/league/basket/sub.json')) {
            $json_str = Storage::disk("public")->get('/league/basket/sub.json');
            $json = json_decode($json_str, true);
            if ($json && strlen($json_str) > 0) {
                $basketLeague = $json;
                foreach ($basketLeague as $item){
                    LeagueController::flushLiveDetailHtml($item['id'],2);
                }
            }
        }
    }

    public function staticLeague(Request $request,$sport,$id){
        if ($sport == 1){
            $pc_json = $this->getLeagueData($id);
            $html = $this->league($request,$id);
            if ($html && strlen($html) > 0){
                if ($pc_json['league']['type'] == 2)
                    Storage::disk("public")->put("/wap/cup_league/foot/".$id.".html", $html);
                else
                    Storage::disk("public")->put("/wap/league/foot/".$id.".html", $html);
            }
        }
        else{
            $html = $this->leagueBK($request,$id);
            if ($html && strlen($html) > 0) {
                Storage::disk("public")->put("/wap/league/basket/" . $id . ".html", $html);
            }
        }
    }

    /**
     * 静态化
     * @param $mid
     * @param int $sport
     */
    public static function flushLiveDetailHtml($mid, $sport = 1){
        $ch = curl_init();
        $url = asset('/api/static/wap/league/' . $sport.'/'.$mid);
        echo $url . '<br>';
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);//8秒超时
        curl_exec ($ch);
        curl_close ($ch);
    }

    /**
     * 静态化专题,足球篮球
     */
    public static function flushSubLeagueJson(){
        $ch = curl_init();
        $url = asset('/api/static/league/json');
        echo $url . '<br>';
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);//8秒超时
        curl_exec ($ch);
        curl_close ($ch);
    }

    public static function staticSubLeagueJson(Request $request){
        //足球
        $query = SubjectLeague::query();
        $query->where('sport', SubjectLeague::kSportFootball);
        $query->where('status', SubjectLeague::kStatusShow);
        $query->selectRaw('*, ifNull(subject_leagues.od, 999) as n_od');
        $query->orderBy('status')->orderBy('n_od');
        $leagues = $query->get();
        $result = array();
        foreach ($leagues as $league){
            if ($league['type'] == 1) {
                $url = '/league/foot/' . $league['lid'] . '.html';
            }
            elseif ($league['type'] == 2) {
                $url = '/cup_league/foot/' . $league['lid'] . '.html';
            }
            $result[] = array(
                'url'=>(isset($url)?$url:''),
                'id'=>$league->lid,
                'name'=>$league['name'],
                'type'=>$league['type']);
        }
        if (count($result) > 0){
            $result = json_encode($result);
            Storage::disk("public")->put("/league/foot/sub.json",$result);
        }
        //篮球
        $query = SubjectLeague::query();
        $query->where('sport', SubjectLeague::kSportBasketball);
        $query->where('status', SubjectLeague::kStatusShow);
        $query->selectRaw('*, ifNull(subject_leagues.od, 999) as n_od');
        $query->orderBy('status')->orderBy('n_od');
        $leagues = $query->get();
        $result = array();
        foreach ($leagues as $league){
            $url = '/league/basket/' . $league['lid'] . '.html';
            $result[] = array(
                'url'=>(isset($url)?$url:''),
                'id'=>$league->lid,
                'name'=>$league['name']);
        }
        if (count($result) > 0){
            $result = json_encode($result);
            Storage::disk("public")->put("/league/basket/sub.json",$result);
        }
    }

    /********** 足球 ************/
    /**
     * 赛事专题
     * @param Request $request
     * @param $lid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function league(Request $request,$lid){
        $pc_json = $this->getLeagueData($lid);
        if (!empty($pc_json)) {
            $result = $pc_json;
            $result['sport'] = 1;
            //专题
            $sl = SubjectLeague::where('lid',$lid)->first();
            if ($sl){
                //赛事视频
                $videos = HotVideo::where('s_lid',$sl->id)->get();
                $result['videos'] = $videos;
                //文章
                $articles = LArticle::where('s_lid',$sl->id)->get();
                $result['articles'] = $articles;
            }

            //联赛,杯赛
            if ($pc_json['league']['type'] == 1) {
                $this->html_var = array_merge($this->html_var,$result);
                return view('phone.league.football.league', $this->html_var);
            }
            else {
                $this->html_var = array_merge($this->html_var,$result);
                return view('phone.football.league.cup_league', $this->html_var);
            }
        }
        else {
            return abort(404);
        }
    }

    public function hotLeague(Request $request){
        $nextDate = date('Ymd',strtotime('1 day'));
        $lastDate = date('Ymd', strtotime('-1 day'));
        $this->html_var['lastDate'] = $lastDate;
        $this->html_var['nextDate'] = $nextDate;
        return view('phone.league.football.hot_league', $this->html_var);
    }
}