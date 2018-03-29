<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/3/28
 * Time: 下午6:02
 */
namespace App\Http\Controllers\Phone\Match;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Controllers\PC\FileTool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MatchController extends BaseController{
    //篮球
    public function immediate_bk(Request $request,$order){
        return $this->immediate($request,'basket',$order);
    }

    public function result_bk(Request $request,$dateStr,$order){
        return $this->result($request,'basket',$dateStr,$order);
    }

    public function schedule_bk(Request $request,$dateStr,$order){
        return $this->schedule($request,'basket',$dateStr,$order);
    }

    //足球
    public function immediate_f(Request $request){
        return $this->immediate($request,'foot');
    }

    public function result_f(Request $request,$dateStr){
        return $this->result($request,'foot',$dateStr);
    }

    public function schedule_f(Request $request,$dateStr){
        return $this->schedule($request,'foot',$dateStr);
    }

    /**
     * 即时列表
     * @param Request $request
     * @param String $sport foot basket
     * @param string $order 篮球用 t按时间l按赛事
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function immediate(Request $request,$sport,$order = 't'){
        if ('basket' == $sport) {
            $sport = 2;
        } else{
            $sport = 1;
        }

        if ($order == 't'){
            $order = 'all';
        }
        else{
            $order = 'league';
        }

        $startDate = date('Ymd');
        $nextDate = date('Ymd',strtotime('1 day'));
        $lastDate = date('Ymd', strtotime('-1 day'));

        $pc_json = FileTool::matchListDataJson($startDate,$sport);
        if (!empty($pc_json)) {
            $result['total'] = count($pc_json['matches']);
            $sortData = $this->sortMatch($pc_json,$sport,$order);
            $result = array_merge($result,$sortData);
            $result['sport'] = $sport;
            $result['nextDate'] = $nextDate;
            $result['lastDate'] = $lastDate;
            $result['currDate'] = date('Y-m-d');
            $this->html_var = array_merge($this->html_var,$result);
            if ($sport == 1)
                return view('phone.match.immediate',$this->html_var);
            else
                return view('phone.match.immediate_bk',$this->html_var);
        }
        else {
//            return abort(500);
        }
    }

    /**
     * 赛果
     * @param Request $request
     * @param $sport
     * @param $dateStr
     * @param string $order 篮球用 t按时间l按赛事
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function result(Request $request,$sport, $dateStr,$order = 't'){
        if ('basket' == $sport) {
            $sport = 2;
        } else{
            $sport = 1;
        }
        $startDate = $dateStr;

        $nextDate = date('Ymd',strtotime('1 day'));
        $lastDate = date('Ymd', strtotime('-1 day'));
        $today = date('Ymd');

        //日期
        $calendar = array();
        for ($i = 7 ; $i > 0 ; $i--){
            $day = time($today) - ($i)*24*3600;
            $weeks = array('周日','周一','周二','周三','周四','周五','周六');
            $calendar[] = array(
                'dateStr'=>date('m月d日',$day),
                'date'=>date('Ymd',$day),
                'w'=>$weeks[date('w',$day)],
                'on'=>$startDate == date('Ymd',$day)
            );
        }

        if ($order == 't'){
            $order = 'all';
        }
        else{
            $order = 'league';
        }

        $pc_json = FileTool::matchListDataJson($startDate,$sport);
        if (!empty($pc_json)) {
            $result['total'] = count($pc_json['matches']);
            $sortData = $this->sortMatch($pc_json,$sport,$order);
            $result = array_merge($result,$sortData);
            $result['sport'] = $sport;
            $result['nextDate'] = $nextDate;
            $result['lastDate'] = $lastDate;
            $result['calendar'] = $calendar;
            $result['currDate'] = date('Y-m-d',strtotime($dateStr));
            $this->html_var = array_merge($this->html_var,$result);
            if($sport == 1)
                return view('phone.match.result',$this->html_var);
            else
                return view('phone.match.result_bk',$this->html_var);
        }
    }

    /**
     * 赛程
     * @param Request $request
     * @param $sport
     * @param $dateStr
     * @param string $order 篮球用 t按时间l按赛事
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function schedule(Request $request,$sport, $dateStr,$order = 't'){
        if ('basket' == $sport) {
            $sport = 2;
        } else{
            $sport = 1;
        }
        $startDate = $dateStr;

        $nextDate = date('Ymd',strtotime('1 day'));
        $lastDate = date('Ymd', strtotime('-1 day'));
        $today = date('Ymd');

        if ($order == 't'){
            $order = 'all';
        }
        else{
            $order = 'league';
        }

        //日期
        $calendar = array();
        for ($i = 0 ; $i < 7 ; $i++){
            $day = time($today) + ($i + 1)*24*3600;
            $weeks = array('周日','周一','周二','周三','周四','周五','周六');
            $calendar[] = array(
                'dateStr'=>date('m月d日',$day),
                'date'=>date('Ymd',$day),
                'w'=>$weeks[date('w',$day)],
                'on'=>$startDate == date('Ymd',$day)
            );
        }

        if ($order == 't'){
            $order = 'all';
        }
        else{
            $order = 'league';
        }

        $pc_json = FileTool::matchListDataJson($startDate,$sport);
        if (!empty($pc_json)) {
            $result['total'] = count($pc_json['matches']);
            $sortData = $this->sortMatch($pc_json,$sport,$order);
            $result = array_merge($result,$sortData);
            $result['sport'] = $sport;
            $result['nextDate'] = $nextDate;
            $result['lastDate'] = $lastDate;
            $result['calendar'] = $calendar;
            $result['currDate'] = date('Y-m-d',strtotime($dateStr));
            $this->html_var = array_merge($this->html_var,$result);
            if ($sport == 1)
                return view('phone.match.schedule',$this->html_var);
            else
                return view('phone.match.schedule_bk',$this->html_var);
        }
    }

    private function sortMatch($pc_json,$sport,$order){
        if ($sport == 1)
        {
            return self::_sortMatch($pc_json);
        }
        else{
            return self::_sortMatchBK($pc_json,$order);
        }
    }

    private function _sortMatchBK($pc_json,$order){
        //比赛列表用
        $matches = array();
        if ($order == 'league'){
            foreach ($pc_json['l_matches'] as $match){
                $matches[] = $match;
            }
        }
        else{
            foreach ($pc_json['matches'] as $match){
                $matches[] = $match;
            }
        }
        $result['matches'] = $matches;
        //赛事
        $filter = array();
        $o_leagues = $pc_json['filter'][0]['data'];
        foreach ($o_leagues as $item){
            $py = $item['py'];
            $lid = $item['id'];
            $name = $item['name'];
            $count = $item['count'];
            $isNBA = isset($item['isFive'])?$item['isFive']:0;
            if (!isset($leagues[$py][$lid])) {
                $filter[$py][$lid] = ["id" => $lid, "name" => $name, "count" => $count, "isNBA" => $isNBA];
            }
        }
        $result['filter'] = $filter;
        return $result;
    }

    private function _sortMatch($pc_json){
        //比赛列表用
        $matches = array();
        foreach ($pc_json['matches'] as $match){
            $matches[] = $match;
        }
        $result['matches'] = $matches;
        //赛事,pc wap不一样
        $filter = array();
        //重要
        $o_leagues = $pc_json['filter'][2]['data'];
        foreach ($o_leagues as $item){
            $py = 'Important';
            $lid = $item['id'];
            $name = $item['name'];
            $count = $item['count'];
            $filter[$py][] = ["id" => $lid, "name" => $name, "count" => $count];
        }
        //竞彩
        $o_leagues = $pc_json['filter'][1]['data'];
        foreach ($o_leagues as $item){
            $py = 'Lottery';
            $lid = $item['id'];
            $name = $item['name'];
            $count = $item['count'];
            $filter[$py][] = ["id" => $lid, "name" => $name, "count" => $count];
        }
        //全部
        $o_leagues = $pc_json['filter'][0]['data'];
        foreach ($o_leagues as $item){
            $py = 'All';
            $lid = $item['id'];
            $name = $item['name'];
            $count = $item['count'];
            $filter[$py][] = ["id" => $lid, "name" => $name, "count" => $count];
        }
        //自定义
        $o_leagues = $pc_json['filter'][0]['data'];
        foreach ($o_leagues as $item){
            $py = 'Self';
            $lid = $item['id'];
            $name = $item['name'];
            $count = $item['count'];
            $filter[$py][] = ["id" => $lid, "name" => $name, "count" => $count];
        }
        $result['filter'] = $filter;
        $result['odd'] = $pc_json['odd'];
        return $result;
    }
}