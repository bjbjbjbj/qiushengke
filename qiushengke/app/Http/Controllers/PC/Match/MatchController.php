<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/3/1
 * Time: 下午4:28
 */
namespace App\Http\Controllers\PC\Match;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Controllers\PC\FileTool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MatchController extends BaseController
{
    public function test(Request $request){
        $url = $request->input('url');
        $json = FileTool::curlData($url,5);
        return $json;
    }

    /**
     * 通过请求自己的链接静态化pc终端，主要是解决 文件权限问题。
     */
    public static function curlToHtml() {
        $ch = curl_init();
        $url = asset('/api/static/football/one');
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
        $url = asset('/api/static/football/five');
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
            Storage::disk("public")->put("/match/foot/schedule/immediate.html", $html);

        //篮球
        $html = $this->immediate_bk($request,'t');
        if (isset($html) && strlen($html) > 0)
            Storage::disk("public")->put("/match/basket/schedule/immediate_t.html", $html);
        $html = $this->immediate_bk($request,'l');
        if (isset($html) && strlen($html) > 0)
            Storage::disk("public")->put("/match/basket/schedule/immediate_l.html", $html);
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
            Storage::disk("public")->put("/match/foot/schedule/".$tomorrow."/schedule.html", $html);
        //赛果
        $yesterday = date('Ymd', strtotime('-1 days'));
        $html = $this->result_f($request,$yesterday);
        if (isset($html) && strlen($html) > 0)
            Storage::disk("public")->put("/match/foot/schedule/".$yesterday."/result.html", $html);

        //篮球
        //赛程
        $tomorrow = date('Ymd', strtotime('+1 days'));
        $html = $this->schedule_bk($request,$tomorrow,'t');
        if (isset($html) && strlen($html) > 0)
            Storage::disk("public")->put("/match/basket/schedule/".$tomorrow."/schedule_t.html", $html);
        $tomorrow = date('Ymd', strtotime('+1 days'));
        $html = $this->schedule_bk($request,$tomorrow,'t');
        if (isset($html) && strlen($html) > 0)
            Storage::disk("public")->put("/match/basket/schedule/".$yesterday."/schedule_l.html", $html);

        //赛果
        $tomorrow = date('Ymd', strtotime('-1 days'));
        $html = $this->result_bk($request,$tomorrow,'t');
        if (isset($html) && strlen($html) > 0)
            Storage::disk("public")->put("/match/basket/schedule/".$tomorrow."/result_t.html", $html);
        $yesterday = date('Ymd', strtotime('-1 days'));
        $html = $this->result_bk($request,$yesterday,'l');
        if (isset($html) && strlen($html) > 0)
            Storage::disk("public")->put("/match/basket/schedule/".$yesterday."/result_l.html", $html);
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
            $result = array();
            $sortData = $this->sortMatch($pc_json,$sport,$order);
            $result = array_merge($result,$sortData);
            $result['total'] = count($sortData['matches']);
            $result['sport'] = $sport;
            $result['nextDate'] = $nextDate;
            $result['lastDate'] = $lastDate;
            $result['currDate'] = date('Y-m-d');
            $this->html_var = array_merge($this->html_var,$result);
            if ($sport == 1)
                return view('pc.match.immediate',$this->html_var);
            else
                return view('pc.match.immediate_bk',$this->html_var);
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
        //如果传入的时间大于今天 则跳转到schedule
        if (strtotime(date('Ymd')) < strtotime($dateStr)) {
            $url = $request->getPathInfo();
            $commonStr = "/match/$sport/schedule/";
            $paramStr = str_replace($commonStr, '', $url);
            $paramStr = str_replace("result", "schedule", $paramStr);
            return redirect($commonStr.$paramStr);
        } //如果传入的时间是今天 则跳转到immediate
        else if (strtotime(date('Ymd')) == strtotime($dateStr)) {
            $url = $request->getPathInfo();
            $commonStr = "/match/$sport/schedule/";
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
        //如果传入的时间小于今天 则跳转到result
        if (strtotime(date('Ymd')) > strtotime($dateStr)) {
            $url = $request->getPathInfo();
            $commonStr = "/match/$sport/schedule/";
            $paramStr = str_replace($commonStr, '', $url);
            $paramStr = str_replace("schedule", "result", $paramStr);
            return redirect($commonStr.$paramStr);
        } //如果传入的时间是今天 则跳转到immediate
        else if (strtotime(date('Ymd')) == strtotime($dateStr)) {
            $url = $request->getPathInfo();
            $commonStr = "/match/$sport/schedule/";
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
                return view('pc.match.schedule',$this->html_var);
            else
                return view('pc.match.schedule_bk',$this->html_var);
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
            elseif ($match['status'] <= -1){
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
            $isFirst = isset($item['genre'])?(($item['genre'] >> 1 & 1) == 1):0;
            if (!isset($leagues[$py][$lid])) {
                $filter[$py][$lid] = ["id" => $lid, "name" => $name, "count" => $count, "isFive" => $isFive, "isFirst" => $isFirst];
            }
        }
        $result['filter'] = $filter;
        $result['odd'] = $pc_json['odd'];
        return $result;
    }

    /**
     * 错误页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function error(Request $request){
        $this->html_var['error'] = 500;
        return view('pc.500',$this->html_var);
    }

    public function staticError(Request $request){
        $html = $this->error($request);
        if (isset($html) && strlen($html) > 0)
            Storage::disk("public")->put("500.html", $html);
    }
}