<?php
/**
 * Created by PhpStorm.
 * User: ricky
 * Date: 16/12/23
 * Time: 下午15:51
 */

namespace App\Http\Controllers\WinSpider;

use App\Http\Controllers\Utils\DateUtils;
use App\Models\WinModels\SportBettingBasket;
use App\Models\WinModels\SportBettingSpiderLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

include_once('lib/simple_html_dom.php');

trait SpiderBasketSportBetting
{

    /*
     * 爬历史数据，一小时一次
     */
    public function spriderBasketSportBetting()
    {
        //倒数去爬没爬的
        $dates = SportBettingSpiderLog::
        where('type', '=', 1)
            ->where(function ($q) {
                $q->whereNull('spider')
                    ->orwhere('spider', '<', 15);
            })
            ->select('sport_betting_spider_logs.*',DB::raw('DATE_FORMAT(issue_num, \'%y-%m-%d\') AS tmp'))
            ->orderby('tmp', 'desc')
            ->get();
        if (isset($dates) && count($dates) > 1) {
            if (is_null($dates[1]->spider) || !($dates[1]->spider >> 0 & 1))
                $this->spiderBasketSportBettingOdd($dates[1]);//胜负
            if (is_null($dates[1]->spider) || !($dates[1]->spider >> 1 & 1))
                $this->spiderBasketSportBettingOddWithPoints($dates[1]);//让分胜负
            if (is_null($dates[1]->spider) || !($dates[1]->spider >> 2 & 1))
                $this->spiderBasketSportBettingDifferent($dates[1]);//胜分差
            if (is_null($dates[1]->spider) || !($dates[1]->spider >> 3 & 1))
                $this->spiderBasketSportBettingScoreSize($dates[1]);//大小分
        }
    }

    /**
     * 实时爬最新的竞彩
     * 更新今天数据，一小时一次
     */
    public function spiderBasketCurrentBetting(Request $request)
    {
        set_time_limit(0);
        $dateStr = $request->input('date');
        //倒数去爬没爬的
        $query = SportBettingSpiderLog::where('type', '=', 1)
//            ->where('spider', '=', 15)
            ->select('sport_betting_spider_logs.*',DB::raw('DATE_FORMAT(issue_num, \'%Y-%m-%d\') AS tmp'))
            ->orderby('tmp', 'desc')
            ->offset($request->input('offset', 0));
        if (isset($dateStr)) {
            $query->whereRaw('DATE_FORMAT(issue_num, \'%Y-%m-%d\')="'.$dateStr.'"');
        }
        $date = $query->first();
        if (isset($date)) {
            $this->spiderBasketSportBettingOdd($date);//胜负
            $this->spiderBasketSportBettingOddWithPoints($date);//让分胜负
            $this->spiderBasketSportBettingDifferent($date);//胜分差
            $this->spiderBasketSportBettingScoreSize($date);//大小分
        }
    }

    /**
     * 列表，一共有多少天要爬，一天一次
     */
    public function spiderBasketSportBettingFrame()
    {
        echo 'http://www.310win.com/buy/JingCaiBasket.aspx?typeID=111&oddstype=2' . '</br>';
        $curl = curl_init();
        //設置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, 'http://www.310win.com/buy/JingCaiBasket.aspx?typeID=111&oddstype=2');
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
            $log = SportBettingSpiderLog::query()
                ->where('type', 1)
                ->where('issue_num', '=', $option->value)
                ->first();;
            if (is_null($log)) {
                $log = new SportBettingSpiderLog();
                $log->issue_num = $option->value;
                $log->type = 1;
                $log->save();
            }
        }

        //顺路把这个胜平负的赔率爬了,今天即时的
        $resultDivs = $html->find('table.socai');
        if (isset($resultDivs) && !empty($resultDivs)) {
            //暂时没需要那最新的和预测,先不做
        }

        $this->spriderBasketSportBetting();
    }

    /**
     * 爬胜分差数据
     * @param $spiderLog
     */
    public function spiderBasketSportBettingDifferent($spiderLog)
    {
        if (is_null($spiderLog)) {
            echo 'spiderSportBettingDifferent no date' . '</br>';
            return;
        }
        echo 'http://www.310win.com/buy/JingCaiBasket.aspx?typeID=113&oddstype=2&date=' . (strlen($spiderLog->issue_num) > 0 ? $spiderLog->issue_num : "") . '</br>';
        $curl = curl_init();
        //設置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, 'http://www.310win.com/buy/JingCaiBasket.aspx?typeID=113&oddstype=2&date=' . (strlen($spiderLog->issue_num) > 0 ? $spiderLog->issue_num : ""));
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
                $name = explode('_', $child->getAttribute('id'))[0];
                if ($name == 'row') {
                    $nis[] = $child;
                }
                if ($name == 'row2') {
                    $nis2[] = $child;
                }
            }
            $this->soreScoreDifferentData($nis, $nis2);
            $spiderLog->spider = $spiderLog->spider | 1 << 2;
            $spiderLog->save();
        } else {
            echo 'spider spiderSportBettingOdd error ' . $spiderLog->issue_num . '</br>';
        }
    }

    /**
     * 爬大小分数据
     * @param $spiderLog
     */
    public function spiderBasketSportBettingScoreSize($spiderLog)
    {
        if (is_null($spiderLog)) {
            echo 'spiderSportBettingOdd no date' . '</br>';
            return;
        }
        echo 'http://www.310win.com/buy/JingCaiBasket.aspx?typeID=114&oddstype=2&date=' . (strlen($spiderLog->issue_num) > 0 ? $spiderLog->issue_num : "") . '</br>';
        $curl = curl_init();
        //設置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, 'http://www.310win.com/buy/JingCaiBasket.aspx?typeID=114&oddstype=2&date=' . (strlen($spiderLog->issue_num) > 0 ? $spiderLog->issue_num : ""));
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
            $this->soreScoreSize($nis);
            $nis = current($resultDivs)->find('tr.ni2');
            $this->soreScoreSize($nis);
            $spiderLog->spider = $spiderLog->spider | 1 << 3;
            $spiderLog->save();
        } else {
            echo 'spider spiderSportBettingScoreSize error ' . $spiderLog->issue_num . '</br>';
        }
    }

    /**
     * 爬胜负赔率
     * @param $spiderLog
     */
    public function spiderBasketSportBettingOdd($spiderLog)
    {
        if (is_null($spiderLog)) {
            echo 'spiderSportBettingOdd no date' . '</br>';
            return;
        }
        echo 'http://www.310win.com/buy/JingCaiBasket.aspx?typeID=111&oddstype=2&date=' . (strlen($spiderLog->issue_num) > 0 ? $spiderLog->issue_num : "") . '</br>';
        $curl = curl_init();
        //設置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, 'http://www.310win.com/buy/JingCaiBasket.aspx?typeID=111&oddstype=2&date=' . (strlen($spiderLog->issue_num) > 0 ? $spiderLog->issue_num : ""));
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
            $this->soreBasketOddData($nis);
            $nis = current($resultDivs)->find('tr.ni2');
            $this->soreBasketOddData($nis);
            $spiderLog->spider = $spiderLog->spider | 1 << 0;
            $spiderLog->save();
        } else {
            echo 'spider spiderSportBettingOdd error ' . $spiderLog->issue_num . '</br>';
        }
    }

    /**
     * 爬让球胜负赔率
     * @param $spiderLog
     */
    public function spiderBasketSportBettingOddWithPoints($spiderLog)
    {
        if (is_null($spiderLog)) {
            echo 'spiderSportBettingOddWithPoints no date' . '</br>';
            return;
        }
        echo 'http://www.310win.com/buy/JingCaiBasket.aspx?typeID=112&oddstype=2&date=' . (strlen($spiderLog->issue_num) > 0 ? $spiderLog->issue_num : "") . '</br>';
        $curl = curl_init();
        //設置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, 'http://www.310win.com/buy/JingCaiBasket.aspx?typeID=112&oddstype=2&date=' . (strlen($spiderLog->issue_num) > 0 ? $spiderLog->issue_num : ""));
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
            $this->soreOddDataWithPoints($nis);
            $nis = current($resultDivs)->find('tr.ni2');
            $this->soreOddDataWithPoints($nis);
            $spiderLog->spider = $spiderLog->spider | 1 << 1;
            $spiderLog->save();
        } else {
            echo 'spider spiderSportBettingOddWithPoints error ' . $spiderLog->issue_num . '</br>';
        }
    }

    private function soreBasketOddData($nis)
    {
        if (empty($nis)) {
            echo 'soreBasketOddData1: match is empty!' . '</br>';
            return;
        }
        foreach ($nis as $ni) {
            $issue_id = $ni->getAttribute('matchid');
            //期数
            $issue_num = substr($issue_id, 0, 4);
            //第几个
            $num = substr($issue_id, 4, 3);
            $sportBetting = SportBettingBasket::where('issue_num', '=', $issue_num)
                ->where('num', '=', $num)
                ->first();
            if (is_null($sportBetting)) {
                $sportBetting = new SportBettingBasket();
                $sportBetting->type = 1;
                $sportBetting->num = $num;
                $sportBetting->issue_num = $issue_num;
            }
            $tds = $ni->find('td');
            if (count($tds) <= 1) {
                echo 'soreBasketOddData2: match is empty!' . '</br>';
                return;
            }
            //赛事id
            if (isset($tds[1]->find('a')[0])) {
                $lid = $tds[1]->find('a')[0]->getAttribute('href');
                $tmp = explode('SclassID=', $lid);
                $lid = end($tmp);
                $sportBetting->lid = $lid;
                //赛事名称
                $lname = strip_tags($tds[1]->find('a')[0]->plaintext);
            } else {
                $lname = $tds[1]->plaintext;
            }
//                dump($matchid);
            $sportBetting->league = $lname;
            //时间
            $timeHour = strip_tags($tds[2]->plaintext);

            $date = $tds[3]->getAttribute('title');
            $this->saveTimeToSportBetting($date, $timeHour, $sportBetting);

            //主队
            $a = $tds[4]->find('a')[0];
            $hname = strip_tags($a->plaintext);
            $sportBetting->hname = $hname;
            $hid = $a->getAttribute('onmouseout');
            $hid = explode('_', $hid)[2];
            $hid = str_replace('\');', '', $hid);
            $sportBetting->hid = $hid;
            //比赛id
            $mid = $a->getAttribute('id');
            $mid = str_replace('HomeTeam_', '', $mid);
            $sportBetting->mid = $mid;
            //客队
            $a = $tds[6]->find('a')[0];
            $aname = strip_tags($a->plaintext);
            $sportBetting->aname = $aname;
            $aid = $a->getAttribute('onmouseout');
            $aid = explode('_', $aid)[2];
            $aid = str_replace('\');', '', $aid);
            $sportBetting->aid = $aid;
            //比分
            $score = strip_tags($tds[5]->plaintext);
            $scores = explode('-', $score);
            if (2 == count($scores) && ''!=$scores[0] && ''!=$scores[1]) {
                $sportBetting->hscore = $scores[0];
                $sportBetting->ascore = $scores[1];
            }
            //赔率
            $odd = array();
            $odd[] = strip_tags($tds[11]->find('span')[0]->plaintext) ?: '';
            $odd[] = strip_tags($tds[12]->find('span')[0]->plaintext) ?: '';
            if ($tds[11]->getAttribute('class') == 'bonus') {
                array_unshift($odd, 1);
            } else if ($tds[12]->getAttribute('class') == 'bonus') {
                array_unshift($odd, 2);
            }else{
                array_unshift($odd,0);
            }
            $sportBetting->odd = implode(',', $odd);
            $sportBetting->save();
            \App\Models\LiaoGouModels\SportBettingBasket::saveDataWithWinData($sportBetting);
        }
    }

    private function soreOddDataWithPoints($nis)
    {
        if (empty($nis)) {
            echo 'soreOddDataWithPoints1:match is empty!' . '</br>';
            return;
        }
        foreach ($nis as $ni) {
            $issue_id = $ni->getAttribute('matchid');
            //期数
            $issue_num = substr($issue_id, 0, 4);
            //第几个
            $num = substr($issue_id, 4, 3);
            $sportBetting = SportBettingBasket::where('issue_num', '=', $issue_num)
                ->where('num', '=', $num)
                ->first();
            if (is_null($sportBetting)) {
                $sportBetting = new SportBettingBasket();
                $sportBetting->type = 1;
                $sportBetting->num = $num;
                $sportBetting->issue_num = $issue_num;
            }
            $tds = $ni->find('td');
            if (count($tds) <= 1) {
                echo 'soreOddDataWithPoints2: match is empty!' . '</br>';
                return;
            }
            //赛事id
            if (isset($tds[1]->find('a')[0])) {
                $lid = $tds[1]->find('a')[0]->getAttribute('href');
                $tmp = explode('SclassID=', $lid);
                $lid = end($tmp);
                $sportBetting->lid = $lid;
                //赛事名称
                $lname = strip_tags($tds[1]->find('a')[0]->plaintext);
            } else {
                $lname = $tds[1]->plaintext;
            }
//                dump($matchid);
            $sportBetting->league = $lname;
            //时间
            $timeHour = strip_tags($tds[2]->plaintext);

            $date = $tds[3]->getAttribute('title');
            $this->saveTimeToSportBetting($date, $timeHour, $sportBetting);

            //主队
            $a = $tds[4]->find('a')[0];
            $hname = strip_tags($a->plaintext);
            $sportBetting->hname = $hname;
            $hid = $a->getAttribute('onmouseout');
            $hid = explode('_', $hid)[2];
            $hid = str_replace('\');', '', $hid);
            $sportBetting->hid = $hid;
            //比赛id
            $mid = $a->getAttribute('id');
            $mid = str_replace('HomeTeam_', '', $mid);
            $sportBetting->mid = $mid;
            //客队
            $a = $tds[6]->find('a')[0];
            $aname = strip_tags($a->plaintext);
            $sportBetting->aname = $aname;
            $aid = $a->getAttribute('onmouseout');
            $aid = explode('_', $aid)[2];
            $aid = str_replace('\');', '', $aid);
            $sportBetting->aid = $aid;
            //比分
            $score = strip_tags($tds[5]->plaintext);
            $scores = explode('-', $score);
            if (2 == count($scores) && ''!=$scores[0] && ''!=$scores[1]) {
                $sportBetting->hscore = $scores[0];
                $sportBetting->ascore = $scores[1];
            }
            //赔率
            $odd = array();
            $odd[] = strip_tags($tds[12]->find('span')[0]->find('font')[0]->plaintext) ?: '';
            $odd[] = strip_tags($tds[11]->find('span')[0]->plaintext) ?: '';
            $odd[] = strip_tags($tds[13]->find('span')[0]->plaintext) ?: '';
            if ($tds[11]->getAttribute('class') == 'bonus') {
                array_unshift($odd, 2);
            } else if ($tds[13]->getAttribute('class') == 'bonus') {
                array_unshift($odd, 3);
            }
            else{
                array_unshift($odd,0);
            }
            $sportBetting->asia_odd = implode(',', $odd);
            $sportBetting->save();
            \App\Models\LiaoGouModels\SportBettingBasket::saveDataWithWinData($sportBetting);
        }
    }

    private function soreScoreDifferentData($nis, $nis2)
    {
        if (empty($nis) || empty($nis2)) {
            echo 'soreScoreDifferentData1: match is empty!' . '</br>';
            return;
        }
        $count = count($nis);
        if ($count != count($nis2)) {
            echo 'spider soreScoreDifferentData error:raw count is not equal raw2!' . '</br>';
            return;
        }
        for ($i = 0; $i < $count; $i++) {
            $ni = $nis[$i];
            $ni2 = $nis2[$i];

            $issue_id = $ni->getAttribute('matchid');
            //期数
            $issue_num = substr($issue_id, 0, 4);
            //第几个
            $num = substr($issue_id, 4, 3);
            $sportBetting = SportBettingBasket::where('issue_num', '=', $issue_num)
                ->where('num', '=', $num)
                ->first();
            if (is_null($sportBetting)) {
                $sportBetting = new SportBettingBasket();
                $sportBetting->type = 1;
                $sportBetting->num = $num;
                $sportBetting->issue_num = $issue_num;
            }
            $tds = $ni->find('td');
            $tds2 = $ni2->find('td');
            if (count($tds) <= 1 || count($tds2) <= 1) {
                echo 'soreScoreDifferentData2: match is empty!' . '</br>';
                return;
            }
            //赛事id
            if (isset($tds[1]->find('a')[0])) {
                $lid = $tds[1]->find('a')[0]->getAttribute('href');
                $tmp = explode('SclassID=', $lid);
                $lid = end($tmp);
                $sportBetting->lid = $lid;
                //赛事名称
                $lname = strip_tags($tds[1]->find('a')[0]->plaintext);
            } else {
                $lname = $tds[1]->plaintext;
            }
            $sportBetting->league = $lname;
            //时间
            $timeHour = strip_tags($tds[2]->plaintext);

            $date = $tds[3]->getAttribute('title');
            $this->saveTimeToSportBetting($date, $timeHour, $sportBetting);
            //主队
            $a = $tds[4]->find('a')[0];
            $hname = strip_tags($a->plaintext);
            $sportBetting->hname = $hname;
            $hid = $a->getAttribute('href');
            $hid = explode('TeamID=', $hid)[1];
            $sportBetting->hid = $hid;
            //比赛id
            $mid = $a->getAttribute('id');
            $mid = str_replace('HomeTeam_', '', $mid);
            $sportBetting->mid = $mid;
            //客队
            $a = $tds2[0]->find('a')[0];
            $aname = strip_tags($a->plaintext);
            $sportBetting->aname = $aname;
            $aid = $a->getAttribute('href');
            $aid = explode('TeamID=', $aid)[1];
            $sportBetting->aid = $aid;
            //比分
            if ($tds[5]->plaintext != '')
                $sportBetting->hscore = $tds[5]->plaintext;
            if ($tds2[1]->plaintext != '')
                $sportBetting->ascore = $tds2[1]->plaintext;
            //赔率
            $odd = array();
            $hasAdd = false;
            for ($k = 0; $k < 6; $k++) {
                $odd[] = (strip_tags($tds[8 + $k]->find('span')[0]->plaintext) ?: '')
                    . '-'
                    . (strip_tags($tds2[2 + $k]->find('span')[0]->plaintext) ?: '');
                if (!$hasAdd) {
                    if ($tds[8 + $k]->getAttribute('class') == 'bonus') {
                        array_unshift($odd, ($k + 1) . '-0');
                        $hasAdd = true;
                    } else if ($tds2[2 + $k]->getAttribute('class') == 'bonus') {
                        array_unshift($odd, ($k + 1) . '-1');
                        $hasAdd = true;
                    }
                }
            }
            if (!$hasAdd) {
                array_unshift($odd, '0-0');
            }
            $sportBetting->different = implode(',', $odd);
            $sportBetting->save();
            \App\Models\LiaoGouModels\SportBettingBasket::saveDataWithWinData($sportBetting);
        }
    }

    private function soreScoreSize($nis)
    {
        if (empty($nis)) {
            echo 'soreScoreSize1: match is empty!' . '</br>';
            return;
        }
        foreach ($nis as $ni) {
            $issue_id = $ni->getAttribute('matchid');
            //期数
            $issue_num = substr($issue_id, 0, 4);
            //第几个
            $num = substr($issue_id, 4, 3);
            $sportBetting = SportBettingBasket::where('issue_num', '=', $issue_num)
                ->where('num', '=', $num)
                ->first();
            if (is_null($sportBetting)) {
                $sportBetting = new SportBettingBasket();
                $sportBetting->type = 1;
                $sportBetting->num = $num;
                $sportBetting->issue_num = $issue_num;
            }
            $tds = $ni->find('td');
            if (count($tds) <= 1) {
                echo 'soreScoreSize2: match is empty!' . '</br>';
                return;
            }
            //赛事id
            if (isset($tds[1]->find('a')[0])) {
                $lid = $tds[1]->find('a')[0]->getAttribute('href');
                $tmp = explode('SclassID=', $lid);
                $lid = end($tmp);
                $sportBetting->lid = $lid;
                //赛事名称
                $lname = strip_tags($tds[1]->find('a')[0]->plaintext);
            } else {
                $lname = $tds[1]->plaintext;
            }
//                dump($matchid);
            $sportBetting->league = $lname;
            //时间
            $timeHour = strip_tags($tds[2]->plaintext);

            $date = $tds[3]->getAttribute('title');
            $this->saveTimeToSportBetting($date, $timeHour, $sportBetting);

            //主队
            $a = $tds[4]->find('a')[0];
            $hname = strip_tags($a->plaintext);
            $sportBetting->hname = $hname;
            $hid = $a->getAttribute('onmouseout');
            $hid = explode('_', $hid)[2];
            $hid = str_replace('\');', '', $hid);
            $sportBetting->hid = $hid;
            //比赛id
            $mid = $a->getAttribute('id');
            $mid = str_replace('HomeTeam_', '', $mid);
            $sportBetting->mid = $mid;
            //客队
            $a = $tds[6]->find('a')[0];
            $aname = strip_tags($a->plaintext);
            $sportBetting->aname = $aname;
            $aid = $a->getAttribute('onmouseout');
            $aid = explode('_', $aid)[2];
            $aid = str_replace('\');', '', $aid);
            $sportBetting->aid = $aid;
            //比分
            $score = strip_tags($tds[5]->plaintext);
            $scores = explode('-', $score);
            if (2 == count($scores) && ''!=$scores[0] && ''!=$scores[1]) {
                $sportBetting->hscore = $scores[0];
                $sportBetting->ascore = $scores[1];
            }
            //赔率
            $odd = array();
            $odd[] = strip_tags($tds[12]->find('span')[0]->plaintext) ?: '';
            $odd[] = strip_tags($tds[11]->find('span')[0]->plaintext) ?: '';
            $odd[] = strip_tags($tds[13]->find('span')[0]->plaintext) ?: '';
            if ($tds[11]->getAttribute('class') == 'bonus') {
                array_unshift($odd, 2);
            } else if ($tds[13]->getAttribute('class') == 'bonus') {
                array_unshift($odd, 3);
            }
            else{
                array_unshift($odd,0);
            }
            $sportBetting->size = implode(',', $odd);
            $sportBetting->save();
            \App\Models\LiaoGouModels\SportBettingBasket::saveDataWithWinData($sportBetting);
        }
    }

    private function saveTimeToSportBetting($dateStr, $hourStr, $sportBetting) {
        if (strlen($hourStr) < 5) {
            return;
        }

        $deadlineDate = str_replace('截止时间：', '', $dateStr);
        $dateStr = substr($deadlineDate, 0, 10);

        $tempDate = $dateStr.' '.substr($hourStr, -5);
        if (strtotime($tempDate) < strtotime($deadlineDate)) {
            $tempDate = date('Y-m-d H:i:s', strtotime('+1 day', strtotime($tempDate)));
        }
        $sportBetting->time = $tempDate;

        if (date('H', strtotime($tempDate)) < 12) {
            $week = DateUtils::getTwoWordsWeek(date('w', strtotime('-1 day', strtotime($tempDate))));
        } else {
            $week = DateUtils::getTwoWordsWeek(date('w', strtotime($tempDate)));
        }

        $sportBetting->week = $week;
    }
}