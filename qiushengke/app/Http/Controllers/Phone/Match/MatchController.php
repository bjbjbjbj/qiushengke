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
    /**
     * 通过请求自己的链接静态化pc终端，主要是解决 文件权限问题。
     */
    public static function curlToHtml() {
        $ch = curl_init();
        $url = asset('/api/static/wap/football/one');
        echo $url;
        if (!is_null($url)) {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 25);
            curl_exec($ch);
            curl_close($ch);
        }
    }

    /**
     * 通过请求自己的链接静态化pc终端，主要是解决 文件权限问题。
     */
    public static function curlToHtml5() {
        $ch = curl_init();
        $url = asset('/api/static/wap/football/five');
        echo $url;
        if (!is_null($url)) {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 25);
            curl_exec($ch);
            curl_close($ch);
        }
    }

    /**
     * 静态化
     * @param Request $request
     */
    public function staticOneMin(Request $request){
        //即时
        $html = $this->immediate_f($request);
        if (isset($html) && strlen($html) > 0)
            Storage::disk("public")->put("/wap/match/foot/schedule/immediate.html", $html);

        //篮球
        $html = $this->immediate_bk($request,'t');
        if (isset($html) && strlen($html) > 0)
            Storage::disk("public")->put("/wap/match/basket/schedule/immediate_t.html", $html);
        $html = $this->immediate_bk($request,'l');
        if (isset($html) && strlen($html) > 0)
            Storage::disk("public")->put("/wap/match/basket/schedule/immediate_l.html", $html);
    }

    /**
     * 静态化
     * @param Request $request
     */
    public function staticFiveMin(Request $request){
        //赛程
        $tomorrow = date('Ymd', strtotime('+1 days'));
        $html = $this->schedule_f($request,$tomorrow);
        if (isset($html) && strlen($html) > 0)
            Storage::disk("public")->put("/wap/match/foot/schedule/".$tomorrow."/schedule.html", $html);
        //赛果
        $yesterday = date('Ymd', strtotime('-1 days'));
        $html = $this->result_f($request,$yesterday);
        if (isset($html) && strlen($html) > 0)
            Storage::disk("public")->put("/wap/match/foot/schedule/".$yesterday."/result.html", $html);

        //篮球
        //赛程
        $tomorrow = date('Ymd', strtotime('+1 days'));
        $html = $this->schedule_bk($request,$tomorrow,'t');
        if (isset($html) && strlen($html) > 0)
            Storage::disk("public")->put("/wap/match/basket/schedule/".$tomorrow."/schedule_t.html", $html);
        $tomorrow = date('Ymd', strtotime('+1 days'));
        $html = $this->schedule_bk($request,$tomorrow,'t');
        if (isset($html) && strlen($html) > 0)
            Storage::disk("public")->put("/wap/match/basket/schedule/".$yesterday."/schedule_l.html", $html);

        //赛果
        $tomorrow = date('Ymd', strtotime('-1 days'));
        $html = $this->result_bk($request,$tomorrow,'t');
        if (isset($html) && strlen($html) > 0)
            Storage::disk("public")->put("/wap/match/basket/schedule/".$tomorrow."/result_t.html", $html);
        $yesterday = date('Ymd', strtotime('-1 days'));
        $html = $this->result_bk($request,$yesterday,'l');
        if (isset($html) && strlen($html) > 0)
            Storage::disk("public")->put("/wap/match/basket/schedule/".$yesterday."/result_l.html", $html);
    }

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
            return redirect('/500.html');
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
        //如果传入的时间大于今天 则跳转到schedule
        if (strtotime(date('Ymd')) < strtotime($dateStr)) {
            $url = $request->getPathInfo();
            $commonStr = "/wap/match/$sport/schedule/";
            $paramStr = str_replace($commonStr, '', $url);
            $paramStr = str_replace("result", "schedule", $paramStr);
            return redirect($commonStr.$paramStr);
        } //如果传入的时间是今天 则跳转到immediate
        else if (strtotime(date('Ymd')) == strtotime($dateStr)) {
            $url = $request->getPathInfo();
            $commonStr = "/wap/match/$sport/schedule/";
            $paramStr = str_replace($commonStr, '', $url);
            $paramStr = str_replace($dateStr.'/', '', $paramStr);
            $paramStr = str_replace("result", "immediate", $paramStr);
            return redirect($commonStr.$paramStr);
        }

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
        //如果传入的时间小于今天 则跳转到result
        if (strtotime(date('Ymd')) > strtotime($dateStr)) {
            $url = $request->getPathInfo();
            $commonStr = "/wap/match/$sport/schedule/";
            $paramStr = str_replace($commonStr, '', $url);
            $paramStr = str_replace("schedule", "result", $paramStr);
            return redirect($commonStr.$paramStr);
        } //如果传入的时间是今天 则跳转到immediate
        else if (strtotime(date('Ymd')) == strtotime($dateStr)) {
            $url = $request->getPathInfo();
            $commonStr = "/wap/match/$sport/schedule/";
            $paramStr = str_replace($commonStr, '', $url);
            $paramStr = str_replace($dateStr.'/', '', $paramStr);
            $paramStr = str_replace("schedule", "immediate", $paramStr);
            return redirect($commonStr.$paramStr);
        }

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
        else{
            return redirect('/500.html');
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
        //赛事,pc wap不一样
        $filter = array();
        //全部
        $o_leagues = $pc_json['filter'][0]['data'];
        foreach ($o_leagues as $item){
            $py = 'All';
            $lid = $item['id'];
            $name = $item['name'];
            $count = $item['count'];
            $filter[$py][] = ["id" => $lid, "name" => $name, "count" => $count];
        }
        //NBA
        $o_leagues = $pc_json['filter'][3]['data'];
        foreach ($o_leagues as $item){
            $py = 'NBA';
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