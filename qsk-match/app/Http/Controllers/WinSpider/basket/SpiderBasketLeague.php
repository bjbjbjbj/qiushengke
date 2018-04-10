<?php
/**
 * 爬赛事数据
 */
namespace App\Http\Controllers\WinSpider\basket;

use App\Models\WinModels\Banker;
use App\Models\WinModels\BasketLeague;
use App\Models\WinModels\BasketMatch;
use App\Models\WinModels\BasketOdd;
use App\Models\WinModels\BasketSeason;
use App\Models\WinModels\BasketStage;
use App\Models\WinModels\BasketState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

trait SpiderBasketLeague
{
    /******************* 联赛赛相关 ***********************/
    /**
     * 单独爬联赛类(季前赛季后赛那种)赛程
     * @param Request $request
     */
    public function spiderLeagueSchedule(Request $request){
        $lid = $request->input('lid',0);
        $season = $request->input('season','');
        $kind = $request->input('kind',0);
        $m = $request->input('m','');
        if ($lid == 0 || $season == ''){
            echo '参数有误';
            return;
        }
        self::spiderLeagueWithData($lid,$season,$kind,$m);
    }

    /**
     * 爬联赛数据(最原始的,全部都是调用距
     * @param $lid
     * @param $season
     * @param $kind
     * @param $m
     * @return array
     */
    public function spiderLeagueWithData($lid,$season,$kind,$m){
        $url = 'http://ios.win007.com/phone/LqSaiCheng2.aspx?sclassid='.$lid.'&Season='.$season;

        if ($kind > 0){
            $url = $url.'&kind='.$kind;
        }
        if ($m != ''){
            if ($kind == 2){
                $url = $url . '&pid=' . $m;
            }
            else {
                $url = $url . '&m=' . $m;
            }
        }

        $str = $this->spiderTextFromUrl($url);
        if ($str) {
            $ss = explode("$$", $str);
            if (count($ss) != 3)
            {
                echo '爬赛程格式有问题';
                return;
            }
            //阶段
            $kinds = $ss[0];
            $kinds = explode('!',$kinds);

            $hasKinds = array();
            $currentKind = 0;
            foreach ($kinds as $tmp){
                list($kindId, $isCurrent) = explode("^", $tmp);
                $hasKinds[] = $kindId;
                if ($isCurrent){
                    $currentKind = $kindId;
                }
            }
            //保存阶段
            $basketSeason = BasketSeason::where('name',$season)
                ->where('lid',$lid)
                ->first();
            if ($kind == 0) {
                $basketSeason->kind = $currentKind;
            }
            $basketSeason->total_kind = implode(',',$hasKinds);
            $basketSeason->save();
            \App\Models\LiaoGouModels\BasketSeason::saveDataWithWinData($basketSeason);

            $months = array();
            //是否有子阶段id
            if ($currentKind == 2){
                $stages = $ss[1];
                $stages = explode('!',$stages);
                foreach ($stages as $stage){
                    //1886^东部第一圈^7^0
                    list($pid,$pname,$count,$isCurrent) = explode('^',$stage);
                    $state = BasketStage::find($pid);
                    if (is_null($state)){
                        $state = new BasketStage();
                        $state->id = $pid;
                    }
                    $state->current = $isCurrent?1:0;
                    $state->lid = $lid;
                    $state->season = $season;
                    $state->name = $pname;
                    $state->kind = $currentKind;
                    $state->count = $count;
                    $state->save();
                    \App\Models\LiaoGouModels\BasketStage::saveDataWithWinData($state,$pid);
                    if (!$isCurrent){
                        $months[] = $pid;
                    }
                    if ($isCurrent){
                        $isCurrentPid = $pid;
                    }
                }
            }
            else{
                $stages = $ss[1];
                $stages = explode('!',$stages);
                foreach ($stages as $stage){
                    list($month,$isCurrent) = explode('^',$stage);
                    if (!$isCurrent) {
                        $months[] = $month;
                    }
                }
            }

            //保存比赛
            $matches = $ss[2];
            $matches = explode('!',$matches);
            foreach ($matches as $match){
                if (count(explode("^", $match)) != 7){
                    continue;
                }
                list($dateStr,$status,$hname,$aname,$hscore,$ascore, $mid) = explode("^", $match);
                //20171201083000^-1^亚特兰大老鹰^克里夫兰骑士^114^121^290160
                $tmp = BasketMatch::find($mid);
                if (is_null($tmp)){
                    $tmp = new BasketMatch();
                    $tmp->id = $mid;
                }
                $date = date('Y-m-d H:i:s', strtotime($dateStr));
                $tmp->time = $date;
                $tmp->lid = $lid;
                $tmp->season = $season;
                $tmp->stage = $currentKind == 2 ? (isset($isCurrentPid)?$isCurrentPid:$currentKind) :$currentKind;
                $tmp->kind = $kind;
                $tmp->hname = $hname;
                $tmp->aname = $aname;
                $tmp->hscore = $hscore == '' ? null : $hscore;
                $tmp->ascore = $ascore == '' ? null : $ascore;
                $tmp->status = $status;
                $tmp->save();
                echo $hname . 'vs'.$aname.'</br>';
                \App\Models\LiaoGouModels\BasketMatch::saveWithWinData($tmp);
            }
            return array('kind'=>isset($currentKind)?$currentKind:0,'array'=>$months);
        }
        return;
    }

    /**
     * 根据比赛id 赛季爬整个联赛类比赛数据
     * @param Request $request
     */
    public function spiderFullLeagueSchedule(Request $request){
        $lid = $request->input('lid',0);
        $seasonName = $request->input('season','');
        self::spiderFullLeagueScheduleData($lid,$seasonName);
    }

    private function spiderFullLeagueScheduleData($lid,$seasonName){
        if ($lid == 0) {
            echo 'lid 不能为空';
            return;
        }
        if ($seasonName == '') {
            echo 'season 不能为空';
            return;
        }

        //看看有多少阶段
        $season = BasketSeason::where('lid',$lid)
            ->where('name',$seasonName)
            ->first();
        if ($season == null){
            echo '没找到赛程';
            return;
        }

        if (is_null($season->total_kind)){
            $this->spiderLeagueWithData($lid,$season->name,0,'');
            $season = BasketSeason::where('lid',$lid)
                ->where('name',$seasonName)
                ->first();
        }
        $kinds = explode(',',$season->total_kind);
        foreach ($kinds as $kind){
            switch ($kind){
                case 1:
                case 3:
                    //按月份
                {
                    $months = $this->spiderLeagueWithData($lid,$season->name,$kind,'')['array'];
                    foreach ($months as $month){
                        $this->spiderLeagueWithData($lid,$season->name,$kind,$month);
                    }
                }
                    break;
                case 2:{
                    $months = $this->spiderLeagueWithData($lid,$season->name,$kind,'')['array'];
                    foreach ($months as $month){
                        $this->spiderLeagueWithData($lid,$season->name,$kind,$month);
                    }
                }
                    break;
            }
        }
    }

    /******************* ***********************/

    /******************* 杯赛相关 ***********************/
    /**
     * 爬杯赛赛事(全明星之类)赛程
     * @param $request
     */
    public function spiderCupFullSchedule($request){
        $lid = $request->input('lid',0);
        $seasonName = $request->input('season','');
        self::spiderCupFullScheduleData($lid,$seasonName);
    }

    private function spiderCupFullScheduleData($lid,$seasonName){
        //看看有多少阶段
        $season = BasketSeason::where('lid',$lid)
            ->where('name',$seasonName)
            ->first();
        if ($season == null){
            echo '没找到赛程';
            return;
        }

        if (is_null($season->total_kind)){
            $this->spiderCupScheduleData($lid,$season->name,'');
            $season = BasketSeason::where('lid',$lid)
                ->where('name',$seasonName)
                ->first();
        }
        $kinds = explode(',',$season->total_kind);
        foreach ($kinds as $kind){
            $this->spiderCupScheduleData($lid,$season->name,$kind);
        }
    }

    /**
     * 爬杯赛赛程(最根
     * @param $lid
     * @param $seasonName
     * @param $groupId
     * @return mixed
     */
    public function spiderCupScheduleData($lid,$seasonName,$groupId){
        if ($lid == 0) {
            echo 'lid 不能为空';
            return;
        }
        if ($seasonName == '') {
            echo 'season 不能为空';
            return;
        }
        $url = 'http://ios.win007.com/phone/LqCupSaiCheng.aspx?ID='.$lid.'&season='.$seasonName.'&subversion=1&groupId='.$groupId;
        $str = $this->spiderTextFromUrl($url);
        if ($str) {
            $ss = explode("$$", $str);
            if (count($ss) != 3)
            {
                echo '爬赛程格式有问题';
                return;
            }
            //阶段
            $kinds = $ss[0];
            $kinds = explode('!',$kinds);

            $hasKinds = array();
            $currentKind = 0;
            $months = array();
            foreach ($kinds as $tmp){
                if (count(explode("^", $tmp)) < 3) {
                    continue;
                }
                list($kindId,$stageName, $isCurrent) = explode("^", $tmp);
                $hasKinds[] = $kindId;
                if ($isCurrent == 'True'){
                    $currentKind = $kindId;
                }
                $state = BasketStage::find($kindId);
                if (is_null($state)){
                    $state = new BasketStage();
                    $state->id = $kindId;
                }
                $state->current = $isCurrent == 'True'?1:0;
                $state->lid = $lid;
                $state->season = $seasonName;
                $state->name = $stageName;
                $state->kind = 4;
                $state->count = 0;
                $state->save();
                \App\Models\LiaoGouModels\BasketStage::saveDataWithWinData($state,$kindId);
                if (!$isCurrent == 'True'){
                    $months[] = $kindId;
                }
                if ($isCurrent == 'True'){
                    $isCurrentPid = $kindId;
                }
            }
            //保存阶段
            $basketSeason = BasketSeason::where('name',$seasonName)
                ->where('lid',$lid)
                ->first();
            if ($groupId == '') {
                $basketSeason->kind = $currentKind;
            }
            $basketSeason->total_kind = implode(',',$hasKinds);
            $basketSeason->save();
            \App\Models\LiaoGouModels\BasketSeason::saveDataWithWinData($basketSeason);


            //保存比赛
            $matches = $ss[2];
            $matches = explode('!',$matches);
            foreach ($matches as $match){
                if (count(explode("^", $match)) != 10){
                    echo '格式不对 ' . $match . ' ' . count(explode("^", $match)).'</br>';
                    continue;
                }
                list($groupName,$dateStr,$hname,$aname,$status,$hscore,$ascore,$a,$b, $mid) = explode("^", $match);
                //20171201083000^-1^亚特兰大老鹰^克里夫兰骑士^114^121^290160
                $tmp = BasketMatch::find($mid);
                if (is_null($tmp)){
                    $tmp = new BasketMatch();
                    $tmp->id = $mid;
                }
                $date = date('Y-m-d H:i:s', strtotime($dateStr));
                //小组名称
                if (isset($groupName) && strlen($groupName) > 0) {
                    $tmp->group = $groupName;
                }
                $tmp->time = $date;
                $tmp->lid = $lid;
                $tmp->season = $seasonName;
                $tmp->stage = $isCurrentPid;
                $tmp->hname = $hname;
                $tmp->aname = $aname;
                $tmp->hscore = $hscore == '' ? null : $hscore;
                $tmp->ascore = $ascore == '' ? null : $ascore;
                $tmp->status = $status;
                $tmp->save();
                \App\Models\LiaoGouModels\BasketMatch::saveWithWinData($tmp);
            }
            return array('kind'=>isset($currentKind)?$currentKind:0,'array'=>$months);
        }
        return;
    }
    /******************* ***********************/
    /***  通用 ****/
    /**
     * 爬赛事赛程,填充用
     */
    public function spiderScheduleWithSeason(){
        $season = BasketSeason::query()
            ->wherenull('spider_at')
            ->orderby('year','desc')
            ->orderby('lid','asc')
            ->first();
        if (isset($season)) {
            echo 'spider '.$season->lid.' ' .$season->name.'</br>';
//            $request->merge(array('lid'=>$season->lid,'season'=>$season->name));

            $league = BasketLeague::find($season->lid);
            if (isset($league)){
                if ($league->type == 1){
                    echo '联赛 '. '</br>';
//                    $this->spiderFullLeagueSchedule($request);
                    $this->spiderFullLeagueScheduleData($season->lid,$season->name);
                    $season->spider_at = date("Y-m-d H:i:s");
                    $season->save();
                    \App\Models\LiaoGouModels\BasketSeason::saveDataWithWinData($season);
                }
                else if($league->type == 2){
                    echo '杯赛 '. '</br>';
                    $this->spiderCupFullScheduleData($season->lid,$season->name);
                    $season->spider_at = date("Y-m-d H:i:s");
                    $season->save();
                    \App\Models\LiaoGouModels\BasketSeason::saveDataWithWinData($season);
                }
            }
        }
    }

    /**
     * 爬赛事赛程,填充用
     */
    public function spiderScheduleWithSeasonForSchedule(){
        $season = BasketSeason::query()
            ->wherenull('spider_at')
            ->orderby('lid','asc')
            ->orderby('year','desc')->first();
        if (isset($season)) {
            echo 'spider '.$season->lid.' ' .$season->name.'</br>';
//            $request->merge(array('lid'=>$season->lid,'season'=>$season->name));

            $league = BasketLeague::find($season->lid);
            if (isset($league)){
                if ($league->type == 1){
                    echo '联赛 '. '</br>';
//                    $this->spiderFullLeagueSchedule($request);
                    self::spiderFullLeagueScheduleData($season->lid,$season->name);
                    $season->spider_at = date("Y-m-d H:i:s");
                    $season->save();
                    \App\Models\LiaoGouModels\BasketSeason::saveDataWithWinData($season);
                }
                else if($league->type == 2){
                    echo '杯赛 '. '</br>';
//                    $this->spiderCupFullSchedule($request);
                    self::spiderFullCupScheduleData($season->lid,$season->name);
                    $season->spider_at = date("Y-m-d H:i:s");
                    $season->save();
                    \App\Models\LiaoGouModels\BasketSeason::saveDataWithWinData($season);
                }
            }
        }
    }

    /********* ***********/

    private function onBasketLeagueHistorySpider(Request $request) {
        set_time_limit(0);
        $key = "basket_league_schedule_history";
        $lg_leagues = Redis::get($key);
        if (isset($lg_leagues)) {
            $lg_leagues = json_decode($lg_leagues, true);
        } else {
            $lg_leagues = \App\Models\LiaoGouModels\BasketLeague::query()
                ->select('win_id', 'type')
                ->where('hot', 1)->get()->toArray();
        }
        if (count($lg_leagues) <= 0) {
            echo "league history spider complete!! <br>";
            return;
        }
        dump(count($lg_leagues));
        $lg_league = $lg_leagues[0];
        $win_lid = $lg_league['win_id'];
        $type = $lg_league['type'];
        $request->merge(['lid'=>$win_lid]);
        $seasons = BasketSeason::query()->where('lid', $win_lid)->orderBy('year', 'desc')->get();
        foreach ($seasons as $season) {
            $request->merge(['season'=>$season->name]);
            if ($type == 1) {
                $this->spiderFullLeagueSchedule($request);
            } else if ($type == 2) {
                $this->spiderCupFullSchedule($request);
            }
        }
        $lg_leagues = array_slice($lg_leagues, 1);
        Redis::set($key, json_encode($lg_leagues));
    }
}