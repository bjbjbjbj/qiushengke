<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 16/12/22
 * Time: 下午12:43
 */

namespace App\Http\Controllers\WinSpider;

use App\Http\Controllers\LotteryTool;
use App\Models\LiaoGouModels\LiaogouAlias;
use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\MatchEuropePrediction;
use App\Models\WinModels\SportBetting;
use App\Models\WinModels\SportBettingSpiderLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

include_once('lib/simple_html_dom.php');

trait SpiderSportBetting
{
    use LotteryTool;

    public function spriderSportBetting()
    {
        //倒数去爬没爬的
        $dates = SportBettingSpiderLog::
        where('type', '=', 0)
            ->where(function ($q) {
                $q->whereNull('spider')
                    ->orwhere('spider', '<', 15);
            })
            ->select('sport_betting_spider_logs.*', DB::raw('DATE_FORMAT(issue_num, \'%y-%m-%d\') AS tmp'))
            ->orderby('tmp', 'desc')
            ->get();
        if (isset($dates) && count($dates) > 1) {
            if (is_null($dates[1]->spider) || !($dates[1]->spider >> 0 & 1))
                $this->spiderSportBettingOdd($dates[1]);
            if (is_null($dates[1]->spider) || !($dates[1]->spider >> 1 & 1))
                $this->spiderSportBettingGoal($dates[1]);
            if (is_null($dates[1]->spider) || !($dates[1]->spider >> 2 & 1))
                $this->spiderSportBettingScore($dates[1]);
            if (is_null($dates[1]->spider) || !($dates[1]->spider >> 3 & 1))
                $this->spiderSportBettingHalf($dates[1]);
        }

        $dates = SportBettingSpiderLog::
        where('type', '=', 0)
            ->select('sport_betting_spider_logs.*', DB::raw('DATE_FORMAT(issue_num, \'%y-%m-%d\') AS tmp'))
            ->orderby('tmp', 'desc')
            ->get();
        //更新最新2期
        if (isset($dates) && count($dates) > 0) {
            $this->spiderSportBettingOdd($dates[0]);
            $this->spiderSportBettingGoal($dates[0]);
            $this->spiderSportBettingScore($dates[0]);
            $this->spiderSportBettingHalf($dates[0]);
            if (count($dates) > 1) {
                $this->spiderSportBettingOdd($dates[1]);
                $this->spiderSportBettingGoal($dates[1]);
                $this->spiderSportBettingScore($dates[1]);
                $this->spiderSportBettingHalf($dates[1]);
            }
        }
    }

    public function spiderSportBettingMatchEuOdd()
    {
        //爬完顺路看看有没有odd没有爬就爬一下
        $date = date_create();
        $startDate = date_format(date_add($date, date_interval_create_from_date_string('15 min')), 'Y-m-d H:i');
        //没有结果的都要去做一下,任九和足彩的都在里面
        $bettings = MatchEuropePrediction::query()
            ->select('match_europe_predictions.*', 'match.time','match.win_id as win_id')
            ->whereNull('prediction_result')
            ->where('time', '>', $startDate)
            ->orderBy("time", "asc")
            ->leftJoin('qsk_match.matches as match', function ($join) {
                $join->on('match.id', '=', 'match_europe_predictions.id');
            })
            ->take(10)
            ->get();

        if (count($bettings) > 0) {
            $total = 0;
            $count = count($bettings);
            $last = 0;
            for ($i = 0; $i < $count; $i++) {
                $betting = $bettings[$i];
                if ($total >= 5)
                    break;
                $last++;
                if (is_null($betting->prediction_result)) {
                    $total++;
                    $this->oddsWithMatchAndType($betting->win_id, 3);
                    $this->analyseBetByMatchId($betting->id);
                }
            }
            echo 'odd count ' . count($bettings) . '</br>';
        } else {
            echo 'odd count ' . count($bettings) . '</br>';
        }
    }

    /**
     * 填充最新一期已经填充了的数据
     */
    public function spiderSportBettingLast()
    {
        $date = SportBettingSpiderLog::
        where('type', '=', 0)
            ->where('spider', '=', 15)
            ->select('sport_betting_spider_logs.*', DB::raw('DATE_FORMAT(issue_num, \'%y-%m-%d\') AS tmp'))
            ->orderby('tmp', 'desc')
            ->first();
        if (isset($date)) {
            $this->spiderSportBettingOdd($date);
            $this->spiderSportBettingGoal($date);
            $this->spiderSportBettingScore($date);
            $this->spiderSportBettingHalf($date);
        }
    }

    /**
     * 加载足彩竞猜框架,按日期
     */
    public function spiderSportBettingFrame()
    {
        echo 'http://www.310win.com/buy/jingcai.aspx?typeID=105&oddstype=2' . '</br>';
        $curl = curl_init();
        //設置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, 'http://www.310win.com/buy/jingcai.aspx?typeID=105&oddstype=2');
        //設置首標
        curl_setopt($curl, CURLOPT_HEADER, 1);
        //設置cURL參數，要求結果保存到字符串中還是輸出到屏幕上。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //運行cURL，請求網頁
        $data = curl_exec($curl);
        //關閉URL請求
        curl_close($curl);
        //顯示獲得的數據
//        var_dump($data);
        $html = new \simple_html_dom();
        $html->load($data);

        //有date多少数据
        $options = $html->find('div.tdRadio2 select option');
        foreach ($options as $option) {
            $log = SportBettingSpiderLog::where('issue_num', '=', $option->value)
                ->where('type', '=', 0)
                ->first();
            if (is_null($log)) {
                $log = new SportBettingSpiderLog();
                $log->issue_num = $option->value;
                $log->type = 0;
                $log->save();
            }
        }

        //顺路把这个胜平负的赔率爬了,今天即时的
        $resultDivs = $html->find('table.socai');
        if (isset($resultDivs) && !empty($resultDivs)) {
            //暂时没需要那最新的和预测,先不做
        }

        $this->spriderSportBetting();
    }

    /**
     * 爬比分数据
     * @param $date
     */
    public function spiderSportBettingScore($date)
    {
        if (is_null($date)) {
            echo 'spiderSportBettingOdd no date' . '</br>';
            return;
        }
        echo 'http://www.310win.com/buy/jingcai.aspx?typeID=102&oddstype=2&date=' . (strlen($date->issue_num) > 0 ? $date->issue_num : "") . '</br>';
        $curl = curl_init();
        //設置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, 'http://www.310win.com/buy/jingcai.aspx?typeID=102&oddstype=2&date=' . (strlen($date->issue_num) > 0 ? $date->issue_num : ""));
        //設置首標
        curl_setopt($curl, CURLOPT_HEADER, 1);
        //設置cURL參數，要求結果保存到字符串中還是輸出到屏幕上。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //運行cURL，請求網頁
        $data = curl_exec($curl);
        //關閉URL請求
        curl_close($curl);
        //顯示獲得的數據
//        var_dump($data);
        $html = new \simple_html_dom();
        $html->load($data);

        //数据
        $resultDivs = $html->find('table.socai');
        if (isset($resultDivs) && !empty($resultDivs)) {
            //爬info
            $nis = array();
            $nis2 = array();
            foreach (current($resultDivs)->children() as $child) {
                if ($child->getAttribute('class') == 'ni') {
                    $nis[] = $child;
                }
                if ($child->getAttribute('class') == 'ni2') {
                    $nis2[] = $child;
                }
            }
            $this->soreScoreData($nis, $resultDivs);
            $this->soreScoreData($nis2, $resultDivs);
            $date->spider = $date->spider | 1 << 2;
            $date->save();
        } else {
            echo 'spider spiderSportBettingScore error ' . $date->issue_num . '</br>';
        }
    }

    /**
     * 爬半全场数据
     * @param $date
     */
    public function spiderSportBettingHalf($date)
    {
        if (is_null($date)) {
            echo 'spiderSportBettingOdd no date' . '</br>';
            return;
        }
        echo 'http://www.310win.com/buy/jingcai.aspx?typeID=104&oddstype=2&date=' . (strlen($date->issue_num) > 0 ? $date->issue_num : "") . '</br>';
        $curl = curl_init();
        //設置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, 'http://www.310win.com/buy/jingcai.aspx?typeID=104&oddstype=2&date=' . (strlen($date->issue_num) > 0 ? $date->issue_num : ""));
        //設置首標
        curl_setopt($curl, CURLOPT_HEADER, 1);
        //設置cURL參數，要求結果保存到字符串中還是輸出到屏幕上。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //運行cURL，請求網頁
        $data = curl_exec($curl);
        //關閉URL請求
        curl_close($curl);
        //顯示獲得的數據
//        var_dump($data);
        $html = new \simple_html_dom();
        $html->load($data);

        //数据
        $resultDivs = $html->find('table.socai');
        if (isset($resultDivs) && !empty($resultDivs)) {
            //爬info
            $nis = current($resultDivs)->find('tr.nii');
            $this->soreHalfData($nis);
            $nis = current($resultDivs)->find('tr.nii2');
            $this->soreHalfData($nis);
            $date->spider = $date->spider | 1 << 3;
            $date->save();
        } else {
            echo 'spider spiderSportBettingHalf error ' . $date->issue_num . '</br>';
        }
    }

    /**
     * 爬进球数据
     * @param $date
     */
    public function spiderSportBettingGoal($date)
    {
        if (is_null($date)) {
            echo 'spiderSportBettingOdd no date' . '</br>';
            return;
        }
        echo 'http://www.310win.com/buy/jingcai.aspx?typeID=103&oddstype=2&date=' . (strlen($date->issue_num) > 0 ? $date->issue_num : "") . '</br>';
        $curl = curl_init();
        //設置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, 'http://www.310win.com/buy/jingcai.aspx?typeID=103&oddstype=2&date=' . (strlen($date->issue_num) > 0 ? $date->issue_num : ""));
        //設置首標
        curl_setopt($curl, CURLOPT_HEADER, 1);
        //設置cURL參數，要求結果保存到字符串中還是輸出到屏幕上。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //運行cURL，請求網頁
        $data = curl_exec($curl);
        //關閉URL請求
        curl_close($curl);
        //顯示獲得的數據
//        var_dump($data);
        $html = new \simple_html_dom();
        $html->load($data);

        //数据
        $resultDivs = $html->find('table.socai');
        if (isset($resultDivs) && !empty($resultDivs)) {
            //爬info
            $nis = current($resultDivs)->find('tr.nii');
            $this->soreGoalData($nis);
            $nis = current($resultDivs)->find('tr.nii2');
            $this->soreGoalData($nis);
            $date->spider = $date->spider | 1 << 1;
            $date->save();
        } else {
            echo 'spider spiderSportBettingGoal error ' . $date->issue_num . '</br>';
        }
    }

    /**
     * 爬胜平负赔率
     * @param $date
     */
    public function spiderSportBettingOdd($date)
    {
        if (is_null($date)) {
            echo 'spiderSportBettingOdd no date' . '</br>';
            return;
        }
        echo 'http://www.310win.com/buy/jingcai.aspx?typeID=105&oddstype=2&date=' . (strlen($date->issue_num) > 0 ? $date->issue_num : "") . '</br>';
        $curl = curl_init();
        //設置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, 'http://www.310win.com/buy/jingcai.aspx?typeID=105&oddstype=2&date=' . (strlen($date->issue_num) > 0 ? $date->issue_num : ""));
        //設置首標
        curl_setopt($curl, CURLOPT_HEADER, 1);
        //設置cURL參數，要求結果保存到字符串中還是輸出到屏幕上。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //運行cURL，請求網頁
        $data = curl_exec($curl);
        //關閉URL請求
        curl_close($curl);
        //顯示獲得的數據
//        var_dump($data);
        $html = new \simple_html_dom();
        $html->load($data);

        //数据
        $resultDivs = $html->find('table.socai');
        if (isset($resultDivs) && !empty($resultDivs)) {
            //爬info
            $nis = current($resultDivs)->find('tr.ni');
            $this->soreOddData($nis);
            $nis = current($resultDivs)->find('tr.ni2');
            $this->soreOddData($nis);
            $date->spider = $date->spider | 1 << 0;
            $date->save();
        } else {
            echo 'spider spiderSportBettingOdd error ' . $date->issue_num . '</br>';
        }
    }

    private function soreOddData($nis)
    {
        foreach ($nis as $ni) {
            $issue_id = $ni->getAttribute('matchid');
            //周几
            $week = $ni->getAttribute('name');
            //期数
            $issue_num = substr($issue_id, 0, 4);
            //第几个
            $num = substr($issue_id, 4, 3);
            $sportBetting = SportBetting::where('issue_num', '=', $issue_num)
                ->where('num', '=', $num)
                ->first();
            if (is_null($sportBetting)) {
                $sportBetting = new SportBetting();
                $sportBetting->type = 0;
                $sportBetting->num = $num;
                $sportBetting->issue_num = $issue_num;
            }
            $sportBetting->week = $week;
            $tds = $ni->find('td');
            //赛事id,暂时不保存,规则不固定
//                $lid = $tds[1]->find('a')[0]->getAttribute('href');
//                dump($matchid);
//                $tmp = explode('/',$lid);
//                $lid = end($tmp);
//                $lid = str_replace('.html','',$lid);
//                $sportBetting->lid = $lid;
            if (count($tds[1]->find('a')) > 0)
                $lname = strip_tags($tds[1]->find('a')[0]->plaintext);
            else{
                $lname = strip_tags($tds[1]->plaintext);
            }
//                dump($matchid);
            $sportBetting->league = $lname;
            //时间
            $time = $tds[2]->getAttribute('title');
            $time = str_replace('开赛时间：', '', $time);
//                $time = strtotime($time);
            $sportBetting->time = $time;
            $time = $tds[3]->getAttribute('title');
            $time = str_replace('截止时间：', '', $time);
            $sportBetting->deadline = $time;
            //是否完场
            $endString = $tds[3]->find('font');
            if (count($endString) > 0) {
                $endString = strip_tags($endString[0]->plaintext);
                if (isset($endString))
                    $sportBetting->status = $endString;
            } else {
                $endString = strip_tags($tds[3]->plaintext);
                //好蛋疼,这里他的完场和取消,完场有font套,取消没有,而且时间和取消的html结构一致,只能做一次判断
                if (isset($endString) && strlen($endString) > 0 && '取消' == $endString)
                    $sportBetting->status = $endString;
            }

            //主队
            $a = $tds[4]->find('a')[0];
            $hname = strip_tags($a->plaintext);
            $sportBetting->hname = $hname;
            $hid = $a->getAttribute('onmouseout');
            $hid = explode('_', $hid)[2];
            $hid = str_replace('\');', '', $hid);
            $sportBetting->hid = $hid;
            LiaogouAlias::bindingLotteryTeam($hid,$hname);
            //球队id
            $mid = $a->getAttribute('href');
            $mid = str_replace('/Handle/Panlu.aspx?id=', '', $mid);
            $sportBetting->mid = $mid;

            //保存到赔率表,自己自动计算赔率和爬数据
            $lgMid = Match::getMatchIdWith($sportBetting->mid,'win_id');
            if ($lgMid > 0) {
                $prediction = MatchEuropePrediction::where('id', '=', $lgMid)->first();
                if (is_null($prediction)) {
                    $prediction = new MatchEuropePrediction();
                    $prediction->id = $lgMid;
                    $prediction->save();
                }
            }

            //客队
            $a = $tds[7]->find('a')[0];
            $aname = strip_tags($a->plaintext);
            $sportBetting->aname = $aname;
            $aid = $a->getAttribute('onmouseout');
            $aid = explode('_', $aid)[2];
            $aid = str_replace('\');', '', $aid);
            $sportBetting->aid = $aid;
            LiaogouAlias::bindingLotteryTeam($aid,$aname);

            //爬情报用
            $a = $tds[11]->find('a')[3];
            if (isset($a)){
                $bettingIssueNum = $a->getAttribute('href');
                $bettingIssueNum = explode('/', $bettingIssueNum);
                $bettingIssueNum = $bettingIssueNum[count($bettingIssueNum) - 1];
                if (strpos($bettingIssueNum, '.html') !== false){
                    $bettingIssueNum = str_replace('.html','',$bettingIssueNum);
                    if (strlen($bettingIssueNum) > 0){
                        $sportBetting->betting_issue_num = $bettingIssueNum;
                    }
                }
            }

            //比分
            $score = strip_tags($tds[6]->plaintext);
            $scores = explode('-', $score);
            if (2 == count(explode('-', $score))) {
                if (strlen($scores[0]))
                    $sportBetting->hscore = $scores[0];
                if (strlen($scores[1]))
                    $sportBetting->ascore = $scores[1];
            }
            //赔率
            $oddTable = $ni->find('table.spfItem')[0];
            $tr = $oddTable->find('tr')[0];
            $tds = $tr->find('td');
            if (count($tds) == 5) {
                $odd = array();
                $odd[] = strip_tags($tds[1]->find('span')[0]->plaintext) ?: '';
                $odd[] = strip_tags($tds[2]->find('span')[0]->plaintext) ?: '';
                $odd[] = strip_tags($tds[3]->find('span')[0]->plaintext) ?: '';
                $sportBetting->odd_up2 = str_replace(' ','',$odd[0]);
                $sportBetting->odd_middle2 = str_replace(' ','',$odd[1]);
                $sportBetting->odd_down2 = str_replace(' ','',$odd[2]);
                if ($tds[1]->getAttribute('class') == 'bonus') {
                    array_unshift($odd, 1);
                    $sportBetting->odd_result = 1;
                } else if ($tds[2]->getAttribute('class') == 'bonus') {
                    array_unshift($odd, 2);
                    $sportBetting->odd_result = 2;
                } else if ($tds[3]->getAttribute('class') == 'bonus') {
                    array_unshift($odd, 3);
                    $sportBetting->odd_result = 3;
                } else {
                    $sportBetting->odd_result = 0;
                    array_unshift($odd, 0);
                }
                $sportBetting->odd = implode(',', $odd);
            } else {
                //没有开不让球盘
            }
            //让球赔率
            $tr = $oddTable->find('tr')[1];
            $tds = $tr->find('td');
            if (count($tds) == 5) {
                $odd = array();
                $odd[] = strip_tags($tds[0]->find('b')[0]->find('font')[0]->plaintext) ?: '';
                $odd[] = strip_tags($tds[1]->find('span')[0]->plaintext) ?: '';
                $odd[] = strip_tags($tds[2]->find('span')[0]->plaintext) ?: '';
                $odd[] = strip_tags($tds[3]->find('span')[0]->plaintext) ?: '';
                $sportBetting->asia = str_replace(' ','',$odd[0]);
                $sportBetting->asia_up2 = str_replace(' ','',$odd[1]);
                $sportBetting->asia_middle2 = str_replace(' ','',$odd[2]);
                $sportBetting->asia_down2 = str_replace(' ','',$odd[3]);
                if ($tds[1]->getAttribute('class') == 'bonus') {
                    array_unshift($odd, 2);
                    $sportBetting->asia_odd_result = 2;
                } else if ($tds[2]->getAttribute('class') == 'bonus') {
                    array_unshift($odd, 3);
                    $sportBetting->asia_odd_result = 3;
                } else if ($tds[3]->getAttribute('class') == 'bonus') {
                    array_unshift($odd, 4);
                    $sportBetting->asia_odd_result = 4;
                } else {
                    $sportBetting->asia_odd_result = 0;
                    array_unshift($odd, 0);
                }
                $sportBetting->asia_odd = implode(',', $odd);
            } else {
                //没有开让球盘
            }
//            dump($sportBetting);
            $sportBetting->save();
//            dump($sportBetting);
            \App\Models\LiaoGouModels\SportBetting::saveDataWithWinData($sportBetting);
        }
    }

    private function soreScoreData($nis, $resultDivs = null)
    {
        foreach ($nis as $ni) {
            $issue_id = $ni->getAttribute('matchid');
            $week = $ni->getAttribute('name');
            //期数
            $issue_num = substr($issue_id, 0, 4);
            //第几个
            $num = substr($issue_id, 4, 3);
            $sportBetting = SportBetting::where('issue_num', '=', $issue_num)
                ->where('num', '=', $num)
                ->first();
            if (is_null($sportBetting)) {
                $sportBetting = new SportBetting();
                $sportBetting->type = 0;
                $sportBetting->num = $num;
                $sportBetting->issue_num = $issue_num;
            }
            $sportBetting->week = $week;
            $tds = $ni->find('td');
            if (count($tds[1]->find('a')) > 0)
                $lname = strip_tags($tds[1]->find('a')[0]->plaintext);
            else{
                $lname = strip_tags($tds[1]->plaintext);
            }
            $sportBetting->league = $lname;

            //是否完场
            $endString = $tds[3]->find('font');
            if (count($endString) > 0) {
                $endString = strip_tags($endString[0]->plaintext);
                if (isset($endString))
                    $sportBetting->status = $endString;
            } else {
                $endString = strip_tags($tds[3]->plaintext);
                //好蛋疼,这里他的完场和取消,完场有font套,取消没有,而且时间和取消的html结构一致,只能做一次判断
                if (isset($endString) && strlen($endString) > 0 && '取消' == $endString)
                    $sportBetting->status = $endString;
            }

            //主队
            $a = $tds[4]->find('a')[0];
            $hname = strip_tags($a->plaintext);
            $sportBetting->hname = $hname;
            $hid = $a->getAttribute('onmouseout');
            $hid = explode('_', $hid)[2];
            $hid = str_replace('\');', '', $hid);
            $sportBetting->hid = $hid;
            LiaogouAlias::bindingLotteryTeam($hid,$hname);
            //球队id
            $mid = $a->getAttribute('href');
            $mid = str_replace('/Handle/Panlu.aspx?id=', '', $mid);
            $sportBetting->mid = $mid;
            //客队
            $a = $tds[6]->find('a')[0];
            $aname = strip_tags($a->plaintext);
            $sportBetting->aname = $aname;
            $aid = $a->getAttribute('onmouseout');
            $aid = explode('_', $aid)[2];
            $aid = str_replace('\');', '', $aid);
            $sportBetting->aid = $aid;
            LiaogouAlias::bindingLotteryTeam($aid,$aname);
            //比分
            $score = strip_tags($tds[5]->plaintext);
            $scores = explode('-', $score);
            if (2 == count(explode('-', $score))) {
                if (strlen($scores[0]))
                    $sportBetting->hscore = $scores[0];
                if (strlen($scores[1]))
                    $sportBetting->ascore = $scores[1];
            }
            //半场进球
            $score = strip_tags($tds[7]->plaintext);
            $scores = explode('-', $score);
            if (2 == count(explode('-', $score))) {
                if (strlen($scores[0]))
                    $sportBetting->h_half_score = $scores[0];
                if (strlen($scores[1]))
                    $sportBetting->a_half_score = $scores[1];
            }

            //进球
            $rowId = $ni->getAttribute('id');
            $hasResult = false;
            if (isset($rowId) && isset($resultDivs)) {
                $trId = str_replace('row', 'tr', $rowId);
                $table = current($resultDivs)->find('#' . $trId)[0]->find('table')[0];
                $tds = $table->find('td');
                $odds = array();
                $i = 1;
                foreach ($tds as $td) {
                    if ($td->getAttribute('class') == 'newchar12gb' || 0 == count($td->find('span'))) {
                        //不是数据
                    } else {
                        $odds[] = strip_tags($td->find('span')[0]->plaintext);
                        if ($td->getAttribute('class') == 'bonus') {
                            $hasResult = true;
                            array_unshift($odds, $i);
                        }
                        $i++;
                    }
                }
            }
            if (!$hasResult)
                array_unshift($odds, 0);
            $sportBetting->score = implode(',', $odds);

            $sportBetting->save();
            \App\Models\LiaoGouModels\SportBetting::saveDataWithWinData($sportBetting);
        }
    }

    private function soreHalfData($nis)
    {
        foreach ($nis as $ni) {
            $issue_id = $ni->getAttribute('matchid');
            $week = $ni->getAttribute('name');
            //期数
            $issue_num = substr($issue_id, 0, 4);
            //第几个
            $num = substr($issue_id, 4, 3);
            $sportBetting = SportBetting::where('issue_num', '=', $issue_num)
                ->where('num', '=', $num)
                ->first();
            if (is_null($sportBetting)) {
                $sportBetting = new SportBetting();
                $sportBetting->type = 0;
                $sportBetting->num = $num;
                $sportBetting->issue_num = $issue_num;
            }
            $sportBetting->week = $week;
            $tds = $ni->find('td');
            //赛事id,暂时不保存,规则不固定
//                $lid = $tds[1]->find('a')[0]->getAttribute('href');
//                dump($matchid);
//                $tmp = explode('/',$lid);
//                $lid = end($tmp);
//                $lid = str_replace('.html','',$lid);
//                $sportBetting->lid = $lid;
            if (count($tds[1]->find('a')) > 0)
                $lname = strip_tags($tds[1]->find('a')[0]->plaintext);
            else{
                $lname = strip_tags($tds[1]->plaintext);
            }
//                dump($matchid);
            $sportBetting->league = $lname;
            //时间
            $time = $tds[2]->getAttribute('title');
            $time = str_replace('开赛时间：', '', $time);
            $sportBetting->time = $time;
            $time = $tds[3]->getAttribute('title');
            $time = str_replace('截止时间：', '', $time);
            $sportBetting->deadline = $time;

            //是否完场
            $endString = $tds[3]->find('font');
            if (count($endString) > 0) {
                $endString = strip_tags($endString[0]->plaintext);
                if (isset($endString))
                    $sportBetting->status = $endString;
            } else {
                $endString = strip_tags($tds[3]->plaintext);
                //好蛋疼,这里他的完场和取消,完场有font套,取消没有,而且时间和取消的html结构一致,只能做一次判断
                if (isset($endString) && strlen($endString) > 0 && '取消' == $endString)
                    $sportBetting->status = $endString;
            }

            //主队
            $a = $tds[4]->find('a')[0];
            $hname = strip_tags($a->plaintext);
            $sportBetting->hname = $hname;
            $hid = $a->getAttribute('onmouseout');
            $hid = explode('_', $hid)[2];
            $hid = str_replace('\');', '', $hid);
            $sportBetting->hid = $hid;
            LiaogouAlias::bindingLotteryTeam($hid,$hname);
            //球队id
            $mid = $a->getAttribute('href');
            $mid = str_replace('/Handle/Panlu.aspx?id=', '', $mid);
            $sportBetting->mid = $mid;
            //客队
            $a = $tds[6]->find('a')[0];
            $aname = strip_tags($a->plaintext);
            $sportBetting->aname = $aname;
            $aid = $a->getAttribute('onmouseout');
            $aid = explode('_', $aid)[2];
            $aid = str_replace('\');', '', $aid);
            $sportBetting->aid = $aid;
            LiaogouAlias::bindingLotteryTeam($aid,$aname);
            //比分
            $score = strip_tags($tds[5]->plaintext);
            $scores = explode('-', $score);
            if (2 == count(explode('-', $score))) {
                if (strlen($scores[0]))
                    $sportBetting->hscore = $scores[0];
                if (strlen($scores[1]))
                    $sportBetting->ascore = $scores[1];
            }
            //半场进球
            $score = strip_tags($tds[7]->plaintext);
            $scores = explode('-', $score);
            if (2 == count(explode('-', $score))) {
                if (strlen($scores[0]))
                    $sportBetting->h_half_score = $scores[0];
                if (strlen($scores[1]))
                    $sportBetting->a_half_score = $scores[1];
            }
            //半全场
            $odds = array();
            $hasResult = false;
            for ($i = 0; $i < 9; $i++) {
                $odd = strip_tags($tds[$i + 9]->find('span')[0]->plaintext);
                $odds[] = $odd;
                if ($tds[$i + 9]->getAttribute('class') == 'bonus') {
                    array_unshift($odds, $i + 1);
                    $hasResult = true;
                }
            }
            if (!$hasResult)
                array_unshift($odds, 0);
            $sportBetting->half = implode(',', $odds);
            $sportBetting->save();
            \App\Models\LiaoGouModels\SportBetting::saveDataWithWinData($sportBetting);
        }
    }

    private function soreGoalData($nis)
    {
        foreach ($nis as $ni) {
            $issue_id = $ni->getAttribute('matchid');
            $week = $ni->getAttribute('name');
            //期数
            $issue_num = substr($issue_id, 0, 4);
            //第几个
            $num = substr($issue_id, 4, 3);
            $sportBetting = SportBetting::where('issue_num', '=', $issue_num)
                ->where('num', '=', $num)
                ->first();
            if (is_null($sportBetting)) {
                $sportBetting = new SportBetting();
                $sportBetting->type = 0;
                $sportBetting->num = $num;
                $sportBetting->issue_num = $issue_num;
            }
            $sportBetting->week = $week;
            $tds = $ni->find('td');
            //赛事id,暂时不保存,规则不固定
//                $lid = $tds[1]->find('a')[0]->getAttribute('href');
//                dump($matchid);
//                $tmp = explode('/',$lid);
//                $lid = end($tmp);
//                $lid = str_replace('.html','',$lid);
//                $sportBetting->lid = $lid;
            if (count($tds[1]->find('a')) > 0)
                $lname = strip_tags($tds[1]->find('a')[0]->plaintext);
            else{
                $lname = strip_tags($tds[1]->plaintext);
            }
//                dump($matchid);
            $sportBetting->league = $lname;
            //时间
            $time = $tds[2]->getAttribute('title');
            $time = str_replace('开赛时间：', '', $time);
//                $time = strtotime($time);
            $sportBetting->time = $time;
            $time = $tds[3]->getAttribute('title');
            $time = str_replace('截止时间：', '', $time);
            $sportBetting->deadline = $time;
            //主队
            $a = $tds[4]->find('a')[0];
            $hname = strip_tags($a->plaintext);
            $sportBetting->hname = $hname;
            $hid = $a->getAttribute('onmouseout');
            $hid = explode('_', $hid)[2];
            $hid = str_replace('\');', '', $hid);
            $sportBetting->hid = $hid;
            LiaogouAlias::bindingLotteryTeam($hid,$hname);
            //球队id
            $mid = $a->getAttribute('href');
            $mid = str_replace('/Handle/Panlu.aspx?id=', '', $mid);
            $sportBetting->mid = $mid;
            //客队
            $a = $tds[6]->find('a')[0];
            $aname = strip_tags($a->plaintext);
            $sportBetting->aname = $aname;
            $aid = $a->getAttribute('onmouseout');
            $aid = explode('_', $aid)[2];
            $aid = str_replace('\');', '', $aid);
            $sportBetting->aid = $aid;
            LiaogouAlias::bindingLotteryTeam($aid,$aname);
            //半场进球
            $score = strip_tags($tds[7]->plaintext);
            $scores = explode('-', $score);
            if (2 == count(explode('-', $score))) {
                if (strlen($scores[0]))
                    $sportBetting->h_half_score = $scores[0];
                if (strlen($scores[1]))
                    $sportBetting->a_half_score = $scores[1];
            }
            //进球数
            $odds = array();
            $hasResult = false;
            for ($i = 0; $i < 8; $i++) {
                $odd = strip_tags($tds[$i + 9]->find('span')[0]->plaintext);
                $odds[] = $odd;
                if ($tds[$i + 9]->getAttribute('class') == 'bonus') {
                    array_unshift($odds, $i + 1);
                    $hasResult = true;
                }
            }
            if (!$hasResult)
                array_unshift($odds, 0);
            $sportBetting->goal = implode(',', $odds);
            $sportBetting->save();
            \App\Models\LiaoGouModels\SportBetting::saveDataWithWinData($sportBetting);
        }
    }

    /**
     * 专门通过爬历史的进球数据，用来填充liaogou match历史竞彩比赛的week num
     */
    public function spiderForHistoryWeekNum(Request $request) {
        $date = $request->input('date');

        if (is_null($date)) {
            echo 'spiderSportBettingOdd no date' . '</br>';
            return;
        }
        echo 'http://www.310win.com/buy/jingcai.aspx?typeID=103&oddstype=2&date=' . (strlen($date) > 0 ? $date : "") . '</br>';
        $curl = curl_init();
        //設置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, 'http://www.310win.com/buy/jingcai.aspx?typeID=103&oddstype=2&date=' . (strlen($date) > 0 ? $date : ""));
        //設置首標
        curl_setopt($curl, CURLOPT_HEADER, 1);
        //設置cURL參數，要求結果保存到字符串中還是輸出到屏幕上。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //運行cURL，請求網頁
        $data = curl_exec($curl);
        //關閉URL請求
        curl_close($curl);
        //顯示獲得的數據
//        var_dump($data);
        $html = new \simple_html_dom();
        $html->load($data);

        //数据
        $resultDivs = $html->find('table.socai');
        if (isset($resultDivs) && !empty($resultDivs)) {
            $nis = current($resultDivs)->find('tr.nii');
            $this->soreWeekNum($nis);
            $nis = current($resultDivs)->find('tr.nii2');
            $this->soreWeekNum($nis);
        } else {
            echo 'spider spiderSportBettingGoal error ' . $date . '</br>';
        }

        if ($request->input('auto')) {
            $nextDate = date('Y-m-d', strtotime('-1 day', strtotime($date)));
            $this->refreshThisPage($date, $nextDate);
        }
    }

    private function soreWeekNum($nis) {
        foreach ($nis as $ni) {
            $issue_id = $ni->getAttribute('matchid');
            $week = $ni->getAttribute('name');
            //期数
            $issue_num = substr($issue_id, 0, 4);
            //第几个
            $num = substr($issue_id, 4, 3);

            //比赛id
            $tds = $ni->find('td');
            $a = $tds[4]->find('a')[0];
            $mid = $a->getAttribute('href');
            $mid = str_replace('/Handle/Panlu.aspx?id=', '', $mid);

            echo "win_id = $mid; week = $week; num = $num <br>";
            Match::saveWithWeekNum($mid, $week, $num);
        }
    }

    //刷新当前页面
    private function refreshThisPage($lastDate, $nextDate)
    {
        echo "<script language=JavaScript> location.replace(location.href.replace('date=$lastDate', 'date=$nextDate'));</script>";
        exit;
    }
}