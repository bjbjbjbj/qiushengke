<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2017/12/6
 * Time: 下午12:28
 */

namespace App\Http\Controllers\WinSpider\basket;

use App\Models\LiaoGouModels\Match;
use App\Models\WinModels\Banker;
use App\Models\WinModels\BasketMatch;
use App\Models\WinModels\BasketOdd;
use App\Models\WinModels\BasketSeason;
use App\Models\WinModels\BasketStage;
use App\Models\WinModels\BasketState;
use App\Models\WinModels\BasketTeam;
use Illuminate\Http\Request;
use QL\QueryList;

trait SpiderBasketMatch
{
    /**
     * 填充liaogoumatch篮球没有比赛id数据
     */
    public function fillLiaogouMatchToday(){
        $matches = \App\Models\LiaoGouModels\BasketMatch::where(function ($q){
            $q->whereNull('hid')
                ->orwhereNull('aid');
        })
            ->where('time','>',date_create())
            ->where('time','<',date_create('+1 day'))
            ->orderby('time','asc')
            ->take(10)
            ->get();
//        $matches = \App\Models\LiaoGouModels\BasketMatch::where('win_id',303373)->get();
        foreach ($matches as $match){
            echo 'win_id '.$match->win_id.'</br>';
            $tmp = BasketMatch::find($match->win_id);
            if (isset($tmp)){
                \App\Models\LiaoGouModels\BasketMatch::saveWithWinData($tmp);
            }
        }
    }

    /**
     * 填充winmatch比赛没有hid的数据
     */
    public function spiderMatchToday(){
        $matches = BasketMatch::where(function ($q){
            $q->where(function ($q2){
                $q2->whereNull('hid')
                    ->orwhereNull('aid');
            });
//                ->orwhere(function ($q2){
//                    $q2->whereNull('hscore_1st')
//                        ->orwhereNull('ascore_1st');
//                });

        })
            ->where('time','>',date_create())
            ->where('time','<',date_create('+1 day'))
            ->orderby('time','asc')
            ->take(10)
            ->get();
        foreach ($matches as $match){
            echo 'win_id '.$match->id.'</br>';
            self::spiderMatchWithId($match->id);

            $mid = $match->id;
            if (!isset($mid)) {
                echo "mid is null <br>";
                return;
            }
            //只爬取亚盘和大小球的盘口数据
            $this->oddsWithMatchAndType($mid, 1);
            $this->oddsWithMatchAndType($mid, 2);
        }
    }

    /**
     * 填充liaogoumatch篮球没有比赛id数据
     */
    public function fillLiaogouMatch(Request $request){
        $count = $request->input('count', 50);
        $matches = \App\Models\LiaoGouModels\BasketMatch::where(function ($q){
            $q->whereNull('hid')
                ->orwhereNull('aid');
        })
            ->orderby('time','asc')
            ->take($count)
            ->get();
        foreach ($matches as $match){
            echo 'win_id '.$match->win_id.'</br>';
            $tmp = BasketMatch::find($match->win_id);
            if (isset($tmp)){
                \App\Models\LiaoGouModels\BasketMatch::saveWithWinData($tmp);
            }
        }

        if ($request->input('auto', 0) == 1) {
            echo "<script language=JavaScript>window.location.reload();</script>";
            exit;
        }
    }

    /**
     * 填充winmatch比赛没有hid的数据
     */
    public function spiderMatch(){
        $matches = BasketMatch::where(function ($q){
            $q->whereNull('hid')
                ->orwhereNull('aid');
        })
//            ->orwhere(function ($q){
//                $q->whereNull('hscore_1st')
//                    ->orwhereNull('ascore_1st');
//            })
            ->orderby('time','asc')
            ->take(10)
            ->get();
        foreach ($matches as $match){
            echo 'win_id '.$match->id.'</br>';
            self::spiderMatchWithId($match->id);

            $mid = $match->id;
            if (!isset($mid)) {
                echo "mid is null <br>";
                return;
            }
            //只爬取亚盘和大小球的盘口数据
            $this->oddsWithMatchAndType($mid, 1);
            $this->oddsWithMatchAndType($mid, 2);
        }
    }

    /**
     * 根据比赛id爬数据
     * @param $mid
     */
    public function spiderMatchWithId($mid){
        if ($mid == 0){
            echo '参数有误';
            return;
        }
        $url = 'http://nba.win007.com/cn/Tech/TechTxtLive.aspx?matchid='.$mid;

        echo $url . '</br>';

        $ql = QueryList::get($url);
        $ha = $ql->find('td a.o_team')->eq(0)->href;
        $hid = explode('=',$ha)[1];
        $aa = $ql->find('td a.o_team')->eq(1)->href;
        $aid = explode('=',$aa)[1];

        $htrs = $ql->find('table.t_bf tr')->eq(1);
        $hdata = $htrs->find('td')->map(function ($td){
            return $td->text();
        });
        $atrs = $ql->find('table.t_bf tr')->eq(2);
        $adata = $atrs->find('td')->map(function ($td){
            return $td->text();
        });

        if ($hid <= 0 || $aid <=0){
            echo 'hid '.$hid .' aid ' . $aid . 'is error'.'</br>';
            return;
        }

        $match = BasketMatch::find($mid);
        if (isset($match)){
            $match->hid = $hid;
            $match->aid = $aid;
            $ot = array();
            for ($i = 0 ; $i < count($hdata) ; $i++){
                if ($i == 0)
                    continue;
                if ($i == count($hdata) - 1)
                    break;
                switch ($i){
                    case 1:
                        if ($hdata[$i] != '') {
                            $match->hscore_1st = $hdata[$i];
                        }
                        break;
                    case 2:
                        if ($hdata[$i] != '') {
                            $match->hscore_2nd = $hdata[$i];
                        }
                        break;
                    case 3:
                        if ($hdata[$i] != '') {
                            $match->hscore_3rd = $hdata[$i];
                        }
                        break;
                    case 4:
                        if ($hdata[$i] != '') {
                            $match->hscore_4th = $hdata[$i];
                        }
                        break;
                    default:
                        if ($hdata[$i] != '') {
                            $ot[] = $hdata[$i];
                        }
                        break;
                }
            }

            $aot = array();
            for ($i = 0 ; $i < count($adata) ; $i++){
                if ($i == 0)
                    continue;
                if ($i == count($adata) - 1)
                    break;
                switch ($i){
                    case 1:
                        if ($hdata[$i] != '') {
                            $match->ascore_1st = $adata[$i];
                        }
                        break;
                    case 2:
                        if ($hdata[$i] != '') {
                            $match->ascore_2nd = $adata[$i];
                        }
                        break;
                    case 3:
                        if ($hdata[$i] != '') {
                            $match->ascore_3rd = $adata[$i];
                        }
                        break;
                    case 4:
                        if ($hdata[$i] != '') {
                            $match->ascore_4th = $adata[$i];
                        }
                        break;
                    default:
                        if ($hdata[$i] != '') {
                            $aot[] = $adata[$i];
                        }
                        break;
                }
            }
            $match->h_ot = implode(',',$ot);
            $match->a_ot = implode(',',$aot);
            $match->save();
            \App\Models\LiaoGouModels\BasketMatch::saveWithWinData($match);
            //保存球队
            $team = BasketTeam::find($hid);
            if (is_null($team)){
                $team = new BasketTeam();
                $team->id = $hid;
                $team->save();
            }
            $team = BasketTeam::find($aid);
            if (is_null($team)){
                $team = new BasketTeam();
                $team->id = $aid;
                $team->save();
            }
        }
    }

    /**
     * 根据比赛id爬数据
     * @param Request $request
     */
    public function spiderMatchById(Request $request){
        $mid = $request->input('id',0);
        self::spiderMatchWithId($mid);
    }
}