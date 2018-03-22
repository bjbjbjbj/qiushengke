<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 16/12/23
 * Time: 下午4:57
 */
namespace App\Http\Controllers\WinSpider;

use App\Models\WinModels\SportBettingBd;
use App\Models\WinModels\SportBettingBdWin;
use App\Models\WinModels\SportBettingSpiderLog;

include_once('lib/simple_html_dom.php');

trait SpiderBdLottery
{
    public function spiderBDcurrentMatch(){
        $log = SportBettingSpiderLog::where('type', '=', 2)
            ->orderby('issue_num', 'desc')
            ->first();
        if (isset($log)) {
            //爬当天的北单未开始的比赛
            $curl = curl_init();
            //設置你需要抓取的URL
            curl_setopt($curl, CURLOPT_URL, 'http://www.310win.com/buy/DanChang.aspx?TypeID=5&issueNum='.$log->issue_num);
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
                $this->bdCurrentData($nis, $resultDivs,$log->issue_num);
                $this->bdCurrentData($nis2, $resultDivs,$log->issue_num);
            } else {
                echo 'spider spiderSportBettingScore error ' . $log->issue_num . '</br>';
            }
        }
    }

    /**
     * 北单单场列表
     * @param $nis
     * @param null $resultDivs
     */
    private function bdCurrentData($nis, $resultDivs = null,$issue_num)
    {
        foreach ($nis as $ni) {
            //期数
            $num = $ni->getAttribute('id');
            $num = substr($num, 4);
            $sportBetting = SportBettingBd::where('issue_num', '=', $issue_num)
                ->where('num', '=', $num)
                ->first();
            if (is_null($sportBetting)) {
                $sportBetting = new SportBettingBd();
                $sportBetting->num = $num;
                $sportBetting->issue_num = $issue_num;
            }
            $tds = $ni->find('td');
            $lname = strip_tags($tds[1]->find('a')[0]->plaintext);
            $sportBetting->league = $lname;

            //主队
            $a = $tds[4]->find('a')[0];
            $hname = strip_tags($a->plaintext);
            $sportBetting->hname = $hname;
            $hid = $a->getAttribute('onmouseout');
            $hid = explode('_', $hid)[2];
            $hid = str_replace('\');', '', $hid);
//            LiaogouAlias::bindingLotteryTeam($hid,$hname);
            //比赛id
            $mid = $a->getAttribute('href');
            $mid = str_replace('/Handle/Panlu.aspx?id=', '', $mid);
            if (is_null($mid)){
                $a = $tds[count($tds) - 4 - 1]->find('a')[0];
            }
            $sportBetting->mid = $mid;
            //客队
            $a = $tds[7]->find('a')[0];
            $aname = strip_tags($a->plaintext);
            $sportBetting->aname = $aname;
            $aid = $a->getAttribute('onmouseout');
            $aid = explode('_', $aid)[2];
            $aid = str_replace('\');', '', $aid);
//            LiaogouAlias::bindingLotteryTeam($aid,$aname);

//            dump($sportBetting);

            $sportBetting->save();
            \App\Models\LiaoGouModels\SportBettingBd::saveDataWithWinData($sportBetting,0);
        }
    }

    /**
     * 爬历史数据框架,主要用于获取足彩期数对应id,结果等需要用这个id爬
     */
    public function spiderSportBettingBDHistoryFrame()
    {
        $curl = curl_init();
        //設置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, 'http://www.310win.com/beijingdanchang/rangqiushengpingfu/kaijiang_dc_all.html');
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
        $resultDivs = $html->find('#dropIssue');
        if (!is_null($resultDivs)) {
            //遍历所有历史足彩数据
            $items = current($resultDivs)->find('option');
            foreach ($items as $item) {
                echo 'spider lotter ' . $item->value . '</br>';
                $log = SportBettingSpiderLog::where('issue_num', '=', $item->value)
                    ->where('type', '=', 2)
                    ->first();
                if (is_null($log)) {
                    $log = new SportBettingSpiderLog();
                    $log->issue_num = $item->value;
                    $log->type = 2;
                    $log->save();
                }
                if ($item->getAttribute('selected') == 'selected') {
                    $currentItem = $item;
                }
            }
            //当前的保存一次
            if (isset($currentItem)) {
                $div = $html->find('#lottery_container');
                if (count($div) == 1) {
                    $nis = current($div)->find('tr.ni');
                    $this->soreData($nis, $currentItem->value);
                    $nis = current($div)->find('tr.ni2');
                    $this->soreData($nis, $currentItem->value);
                    $log = SportBettingSpiderLog::where('issue_num', '=', $currentItem->value)
                        ->where('type', '=', 2)
                        ->first();
                    if (is_null($log)) {
                        $log = new SportBettingSpiderLog();
                        $log->issue_num = $currentItem->value;
                        $log->type = 2;
                    }
                    $log->spider = 31;
                    $log->save();
                }
            }
        } else {
            echo 'spider lottery no data ' . '</br>';
        }
    }

    /**
     * 爬当天的北单数据和北单胜负过关数据
     */
    public function spiderCurrentSportBettingBD()
    {
        $log = SportBettingSpiderLog::where('type', '=', 2)
            ->orderby('issue_num', 'desc')
            ->first();
        if (isset($log)) {
            //爬当天的北单数据
            $this->spiderByIssueNum($log->issue_num);
        }
        echo '<br>';
        $log = SportBettingSpiderLog::where('type', '=', 3)
            ->orderby('issue_num', 'desc')
            ->first();
        if (isset($log)) {
            //爬当天的北单胜负数据
            $this->spiderByIssueNumWin($log->issue_num);
        }
    }

    /**
     * 爬最新的历史北单数据
     */
    public function spiderSportBettingBD()
    {
        $log = SportBettingSpiderLog::where('type', '=', 2)
            ->where(function ($q) {
                $q->wherenull('spider')
                    ->orwhere('spider', '<', 31);
            })
            ->orderby('issue_num', 'desc')
            ->first();
        if (isset($log)) {
            //爬历史的,就是最近的上一期
            $this->spiderByIssueNum($log->issue_num);
        } else {
            //爬最新的一期
            $log = SportBettingSpiderLog::query()->where('type', '=', 2)->orderBy('issue_num', 'desc')->first();
            $this->spiderByIssueNum($log->issue_num);
//            echo 'beidan history count is ' . count($log) . ' not need spider history';
        }
    }

    /**
     * @param $issue_num
     */
    private function spiderByIssueNum($issue_num)
    {
        if (0 == strlen($issue_num)) {
            echo 'no issue num';
            return;
        }
        echo 'spider ' . 'http://www.310win.com/beijingdanchang/kaijiang_dc_' . $issue_num . '_all.html';
        $curl = curl_init();
        //設置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, 'http://www.310win.com/beijingdanchang/kaijiang_dc_' . $issue_num . '_all.html');
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
        $resultDivs = $html->find('#dropIssue');
        if (!is_null($resultDivs)) {
            $div = $html->find('#lottery_container');
            if (count($div) == 1) {
                $nis = current($div)->find('tr.ni');
                $this->soreData($nis, $issue_num);
                $nis = current($div)->find('tr.ni2');
                $this->soreData($nis, $issue_num);

                $log = SportBettingSpiderLog::where('issue_num', '=', $issue_num)
                    ->where('type', '=', 2)
                    ->first();
                if (is_null($log)) {
                    $log = new SportBettingSpiderLog();
                    $log->issue_num = $issue_num;
                    $log->type = 2;
                }
                $log->spider = 31;
                $log->save();
            }
        } else {
            echo 'spider lottery no data ' . '</br>';
        }
    }

    /**
     * 处理北单数据,除了胜负过关
     * @param $nis
     */
    private function soreData($nis, $issue_num)
    {
        foreach ($nis as $ni) {
            $tds = $ni->find('td');
            //编号
            $num = strip_tags($tds[0]->plaintext);
            //看看有没有记录
            $sportBetting = SportBettingBd::where('num', '=', $num)
                ->where('issue_num', '=', $issue_num)->first();
            if (is_null($sportBetting)) {
                $sportBetting = new SportBettingBd();
                $sportBetting->issue_num = $issue_num;
                $sportBetting->num = $num;
            }
            //赛事
            $sportBetting->league = strip_tags($tds[1]->plaintext);
            //时间
            $time = strip_tags($tds[2]->plaintext);
            $currentYear = date_format(date_create()->setTimestamp(strtotime("now")), 'Y');
            //今年这个时候
            $bj = $currentYear . '-' . $time;
            //大于就是还在今年内
            if ($bj > date_format(date_create()->setTimestamp(strtotime("now")), 'Y-m-d H:i')) {
                $time = ($currentYear + 1) . '-' . $time;
            } else {
                $time = $bj;
            }
            $sportBetting->time = $time;

            //主队,可能有让球
            $hname = strip_tags($tds[3]->plaintext);
            if (1 == count($tds[3]->find('font'))) {
                $sportBetting->handicap = strip_tags($tds[3]->find('font')[0]->plaintext);
            }
            $sportBetting->hname = explode('(', $hname)[0];
            $sportBetting->hname = str_replace('  		        ', '', $sportBetting->hname);

            //客队
            $sportBetting->aname = strip_tags($tds[5]->plaintext);

            //得分
            $scores = strip_tags($tds[4]->find('span')[0]->plaintext);
            $scores = str_replace(' ', '', $scores);
            $tmp = $scores;
            if (isset($scores) && 2 == count(explode('-', $scores))) {
                $iscancel = false;
            } else {
                $iscancel = true;
            }
//            $iscancel = false;
//            if ('取消' == $scores) {
//                //取消比赛
//                $iscancel = true;
//            }

            if (!$iscancel) {
                if (2 == count(explode('-', $scores))) {
                    $sportBetting->hscore = explode('-', $scores)[0];
                    $sportBetting->ascore = explode('-', $scores)[1];
                }

                $scores = strip_tags($tds[4]->plaintext);
                $scores = str_replace(' ', '', $scores);
                $scores = str_replace(array("\r", "\n"), "", $scores);
                if (count(explode($tmp, $scores)) > 2) {
                    $scores = $tmp;
                } else {
                    $scores = str_replace($tmp, '', $scores);
                }
                if (2 == count(explode('-', $scores))) {
                    $sportBetting->h_half_score = explode('-', $scores)[0];
                    $sportBetting->a_half_score = explode('-', $scores)[1];
                }
            }

            //胜平负
            if (!$iscancel)
                $sportBetting->asia_result = strip_tags($tds[6]->find('span')[0]->plaintext);
            $sportBetting->asia_result_odd = strip_tags($tds[6]->find('a')[0]->plaintext);

            //进球数
            if (!$iscancel)
                $sportBetting->goal_result = strip_tags($tds[7]->find('span')[0]->plaintext);
            $sportBetting->goal_result_odd = strip_tags($tds[7]->find('a')[0]->plaintext);

            //上下单双
            if (!$iscancel)
                $sportBetting->win_double_result = strip_tags($tds[8]->find('span')[0]->plaintext);
            $sportBetting->win_double_result_odd = strip_tags($tds[8]->find('a')[0]->plaintext);

            //比分
            if (!$iscancel)
                $sportBetting->score_result = strip_tags($tds[9]->find('span')[0]->plaintext);
            $sportBetting->score_result_odd = strip_tags($tds[9]->find('a')[0]->plaintext);

            //半全场胜负
            if (!$iscancel)
                $sportBetting->half_score_result = strip_tags($tds[10]->find('span')[0]->plaintext);
            $sportBetting->half_score_result_odd = strip_tags($tds[10]->find('a')[0]->plaintext);

            //mid
            $a = $tds[12]->find('a')[0];
            $mid = $a->getAttribute('href');
            $mid = explode('/', $mid);
            $mid = end($mid);
            $mid = explode('.', $mid);
            $mid = $mid[0];
            $sportBetting->mid = $mid;

            $sportBetting->save();
            \App\Models\LiaoGouModels\SportBettingBd::saveDataWithWinData($sportBetting,1);
        }
    }

    //以下 北单胜负过关
    /**
     * 爬历史数据框架,主要用于获取足彩期数对应id,结果等需要用这个id爬
     */
    public function spiderSportBettingBDWinHistoryFrame()
    {
        $curl = curl_init();
        //設置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, 'http://www.310win.com/others/kaijiang_dc_11.html');
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
        $resultDivs = $html->find('#dropIssue');
        if (!is_null($resultDivs)) {
            //遍历所有历史足彩数据
            $items = current($resultDivs)->find('option');
            foreach ($items as $item) {
                echo 'spider lotter ' . $item->value . '</br>';
                $log = SportBettingSpiderLog::where('issue_num', '=', $item->value)
                    ->where('type', '=', 3)
                    ->first();
                if (is_null($log)) {
                    $log = new SportBettingSpiderLog();
                    $log->issue_num = $item->value;
                    $log->type = 3;
                    $log->save();
                }
                if ($item->getAttribute('selected') == 'selected') {
                    $currentItem = $item;
                }
            }
            //当前的保存一次
            if (isset($currentItem)) {
                $div = $html->find('#lottery_container');
                if (count($div) == 1) {
                    $nis = current($div)->find('tr.ni');
                    $this->soreWinData($nis, $currentItem->value);
                    $nis = current($div)->find('tr.ni2');
                    $this->soreWinData($nis, $currentItem->value);
                    $log = SportBettingSpiderLog::where('issue_num', '=', $currentItem->value)
                        ->where('type', '=', 3)
                        ->first();
                    if (is_null($log)) {
                        $log = new SportBettingSpiderLog();
                        $log->issue_num = $currentItem->value;
                        $log->type = 3;
                    }
                    $log->spider = 1;
                    $log->save();
                }
            }
        } else {
            echo 'spider lottery no data ' . '</br>';
        }
    }

    /**
     * 爬最新的历史北单胜负过关数据
     */
    public function spiderSportBettingBDWin()
    {
        $log = SportBettingSpiderLog::where('type', '=', 3)
            ->where(function ($q) {
                $q->whereNull('spider')
                    ->orwhere('spider', '<', 1);
            })
            ->orderby('issue_num', 'desc')
            ->first();
        if (isset($log)) {
            //爬历史的,就是最近的上一期
            $this->spiderByIssueNumWin($log->issue_num);
        } else {
            //爬最新的一期
            $log = SportBettingSpiderLog::query()->where('type', '=', 3)->orderBy('issue_num', 'desc')->first();
            $this->spiderByIssueNumWin($log->issue_num);
//            echo 'beidan history count is ' . count($log) . ' not need spider history';
        }
    }

    /**
     * @param $issue_num
     */
    private function spiderByIssueNumWin($issue_num)
    {
        if (0 == strlen($issue_num)) {
            echo 'no issue num';
            return;
        }
        echo 'spider ' . 'http://www.310win.com/others/kaijiang_dc_11_' . $issue_num . '.html';
        $curl = curl_init();
        //設置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, 'http://www.310win.com/others/kaijiang_dc_11_' . $issue_num . '.html');
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
        $resultDivs = $html->find('#dropIssue');
        if (!is_null($resultDivs)) {
            $div = $html->find('#lottery_container');
            if (count($div) == 1) {
                $nis = current($div)->find('tr.ni');
                $this->soreWinData($nis, $issue_num);
                $nis = current($div)->find('tr.ni2');
                $this->soreWinData($nis, $issue_num);

                $log = SportBettingSpiderLog::where('issue_num', '=', $issue_num)
                    ->where('type', '=', 3)
                    ->first();
                if (is_null($log)) {
                    $log = new SportBettingSpiderLog();
                    $log->issue_num = $issue_num;
                    $log->type = 3;
                }
                $log->spider = 1;
                $log->save();
            }
        } else {
            echo 'spider lottery no data ' . '</br>';
        }
    }

    /**
     * 处理北单胜负过关
     * @param $nis
     */
    private function soreWinData($nis, $issue_num)
    {
        foreach ($nis as $ni) {
            $tds = $ni->find('td');
            //类似 足球 篮球等
            $type = str_replace(' ', '', strip_tags($tds[1]->plaintext));

            //编号
            $num = strip_tags($tds[0]->plaintext);

            //看看有没有记录
            $sportBetting = SportBettingBdWin::where('num', '=', $num)
                ->where('issue_num', '=', $issue_num)->first();
            if (is_null($sportBetting)) {
                $sportBetting = new SportBettingBdWin();
                $sportBetting->issue_num = $issue_num;
                $sportBetting->num = $num;
                //以前0是足球,这里兼容一下
                if ('足球' == $type) {
                    $sportBetting->type = 0;
                }
                else{
                    $sportBetting->type = $type;
                }
            }
            //赛事
            $sportBetting->league = strip_tags($tds[2]->plaintext);
            //时间
            $time = trim(strip_tags($tds[3]->plaintext));
            $currentYear = date_format(date_create()->setTimestamp(strtotime("now")), 'Y');
            //今年这个时候
            $bj = $currentYear . '-' . $time;
            //大于就是还在今年内
            if ($bj > date_format(date_create()->setTimestamp(strtotime("now")), 'Y-m-d H:i')) {
                $time = ($currentYear + 1) . '-' . $time;
            } else {
                $time = $bj;
            }
            $sportBetting->time = $time;

            //主队
            $hname = strip_tags($tds[4]->plaintext);
            $sportBetting->hname = explode('(', $hname)[0];
            $sportBetting->hname = str_replace(' ', '', $sportBetting->hname);
            $sportBetting->hname = trim($sportBetting->hname);

            //客队
            $sportBetting->aname = strip_tags($tds[6]->plaintext);

            //得分
            $scores = strip_tags($tds[5]->find('span')[0]->plaintext);
            $scores = str_replace(' ', '', $scores);
            $isCancel = false;
            if ('取消' == $scores) {
                $isCancel = true;
            }
            if (!$isCancel) {
                if (2 == count(explode('-', $scores))) {
                    $sportBetting->hscore = explode('-', $scores)[0];
                    $sportBetting->ascore = explode('-', $scores)[1];
                }
            }
            //让球
            $sportBetting->handicap = strip_tags($tds[7]->plaintext);
            //结果
            if (!$isCancel)
                $sportBetting->result = strip_tags($tds[8]->find('span')[0]->plaintext);
            //sp
            $sportBetting->sp = strip_tags($tds[9]->find('u')[0]->plaintext);

            $sportBetting->save();
            \App\Models\LiaoGouModels\SportBettingBdWin::saveDataWithWinData($sportBetting);
        }
    }
}