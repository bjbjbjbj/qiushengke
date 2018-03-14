<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/3/1
 * Time: 下午4:28
 */
namespace App\Http\Controllers\PC\Match;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;

class MatchController extends BaseController
{
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
        $startDate = date('Ymd');
        $nextDate = date('Ymd',strtotime('1 day'));
        $lastDate = date('Ymd', strtotime('-1 day'));

        if ($order == 't'){
            $order = 'all';
        }
        else{
            $order = 'league';
        }

        $ch = curl_init();
        $url = 'http://match.liaogou168.com/static/schedule/'.$startDate.'/'.$sport.'/'.$order.'.json';
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);//5秒超时
        $pc_json = curl_exec ($ch);
        curl_close ($ch);
        $pc_json = json_decode($pc_json,true);
        if (!empty($pc_json)) {
            $result['total'] = count($pc_json['matches']);
            $sortData = $this->sortMatch($pc_json,$sport);
            $result = array_merge($result,$sortData);
            $result['sport'] = $sport;
            $result['nextDate'] = $nextDate;
            $result['lastDate'] = $lastDate;
            $this->html_var = array_merge($this->html_var,$result);
            if ($sport == 1)
                return view('pc.match.immediate',$this->html_var);
            else
                return view('pc.match.immediate_bk',$this->html_var);
        }
        else {
            return abort(404);
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

        $ch = curl_init();
        $url = 'http://match.liaogou168.com/static/schedule/'.$startDate.'/'.$sport.'/'.$order.'.json';
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);//5秒超时
        $pc_json = curl_exec ($ch);
        curl_close ($ch);
        $pc_json = json_decode($pc_json,true);
        if (!empty($pc_json)) {
            $result['total'] = count($pc_json['matches']);
            $sortData = $this->sortMatch($pc_json,$sport);
            $result = array_merge($result,$sortData);
            $result['sport'] = $sport;
            $result['nextDate'] = $nextDate;
            $result['lastDate'] = $lastDate;
            $result['calendar'] = $calendar;
            $this->html_var = array_merge($this->html_var,$result);
            if($sport == 1)
                return view('pc.match.result',$this->html_var);
            else
                return view('pc.match.result_bk',$this->html_var);
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

        $ch = curl_init();
        $url = 'http://match.liaogou168.com/static/schedule/'.$startDate.'/'.$sport.'/'.$order.'.json';
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);//5秒超时
        $pc_json = curl_exec ($ch);
        curl_close ($ch);
        $pc_json = json_decode($pc_json,true);
        if (!empty($pc_json)) {
            $result['total'] = count($pc_json['matches']);
            $sortData = $this->sortMatch($pc_json,$sport);
            $result = array_merge($result,$sortData);
            $result['sport'] = $sport;
            $result['nextDate'] = $nextDate;
            $result['lastDate'] = $lastDate;
            $result['calendar'] = $calendar;
            $this->html_var = array_merge($this->html_var,$result);
            if ($sport == 1)
                return view('pc.match.schedule',$this->html_var);
            else
                return view('pc.match.schedule_bk',$this->html_var);
        }
    }

    public function test(Request $request){
        $ch = curl_init();
        $url = 'http://match.liaogou168.com/static/change/2/score.json';
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);//5秒超时
        $pc_json = curl_exec ($ch);
        curl_close ($ch);
        return $pc_json;
    }

    public function test2(Request $request){
        $ch = curl_init();
        $url = 'http://match.liaogou168.com/static/change/2/roll.json';
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);//5秒超时
        $pc_json = curl_exec ($ch);
        curl_close ($ch);
        return $pc_json;
    }

    private function sortMatch($pc_json,$sport){
        if ($sport == 1)
        {
            return self::_sortMatch($pc_json);
        }
        else{
            return self::_sortMatchBK($pc_json);
        }
    }

    private function _sortMatchBK($pc_json){
        //比赛列表用
        $matches = array();
        foreach ($pc_json['matches'] as $match){
            $matches[] = $match;
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
        $top = array();
        $live = array();
        $after = array();
        $end = array();
        $matches = array();
        foreach ($pc_json['matches'] as $match){
            $matches[] = $match;
            if ($match['status'] > 0){
                $live[] = $match;
            }
            elseif ($match['status'] == 0){
                $after[] = $match;
            }
            elseif ($match['status'] == -1){
                $end[] = $match;
            }
            //缺顶置
        }
        $result['topMatches'] = $top;
        $result['liveMatches'] = $live;
        $result['afterMatches'] = $after;
        $result['endMatches'] = $end;
        $result['matches'] = $matches;
        //赛事
        $filter = array();
        $o_leagues = $pc_json['filter'][0]['data'];
        foreach ($o_leagues as $item){
            $py = $item['py'];
            $lid = $item['id'];
            $name = $item['name'];
            $count = $item['count'];
            $isFive = isset($item['isFive'])?$item['isFive']:0;
            $isFirst = isset($item['isFirst'])?$item['isFirst']:0;
            if (!isset($leagues[$py][$lid])) {
                $filter[$py][$lid] = ["id" => $lid, "name" => $name, "count" => $count, "isFive" => $isFive, "isFirst" => $isFirst];
            }
        }
        $result['filter'] = $filter;
        $result['odd'] = $pc_json['odd'];
        return $result;
    }
}