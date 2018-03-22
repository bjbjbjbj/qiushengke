<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 17/2/24
 * Time: 上午11:04
 */

namespace App\Http\Controllers\LiaogouLottery;

use App\Models\LiaoGouModels\BettingNumMid;
use App\Models\LiaoGouModels\LotteryTip;
use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\SpiderLotteryTips;
use App\Models\LiaoGouModels\SportBetting;
use Illuminate\Http\Request;

//include_once('../WinSpider/lib/simple_html_dom.php');

trait SpiderBettingNews{
//中超别名
    public $zhongchao = [
        '贵州智诚'=>'贵州恒丰智诚',
        '上海申花'=>'上海绿地申花'];

    //重爬数据用
    public function spiderBettingNewsWithMid($mid){
        if (is_null($mid))
            return;

        if (isset($mid)){
            $bettings = SpiderLotteryTips::where('mid', $mid)->get();
        }
        $count = count($bettings);
        echo '一共' .$count. '场<br>';
        foreach ($bettings as $betting){
            if (isset($betting->betting_issue_num)) {
                $this->spiderBetting8WinTips($betting->betting_issue_num);
                $this->spiderBettingTips($betting->betting_issue_num);
            }
            $this->spiderBettingCubeTipsByCubeId($betting);
            $this->spiderBettingLeisuTipsByLeisuId($betting);
            $betting->has_tip = 1;
            $betting->save();
        }
    }

    public function spiderBettingNews(){
        $bettings = SpiderLotteryTips::whereNull('has_tip')
            ->take(10)
            ->get();
        $count = count($bettings);
        echo '一共' .$count. '场<br>';
        foreach ($bettings as $betting){
            echo 'mid '.$betting->mid.'</br>';
            if (isset($betting->betting_issue_num)) {
                $this->spiderBetting8WinTips($betting->betting_issue_num);
                $this->spiderBettingTips($betting->betting_issue_num);
            }
            $this->spiderBettingCubeTipsByCubeId($betting);
            $this->spiderBettingLeisuTipsByLeisuId($betting);
            //            return;
            $betting->has_tip = 1;
            $betting->save();
        }
    }

    /*
     * 刷先哪些比赛需要爬爆料
     */
    public function spiderBettingTipsFrame(Request $request){
        //先是竞彩的需要
        $date = date_create();
        $bettings = SportBetting::where('deadline', '>', $date)
            ->where('deadline', '<', date_format(date_add(date_create(), date_interval_create_from_date_string('1 day')), 'Y-m-d H:i:s'))
            ->orderby('deadline', 'asc')
            ->get();
        echo '一共'.count($bettings).'场竞彩'.'</br>';
        foreach ($bettings as $betting){
            $log = SpiderLotteryTips::where('mid',$betting->mid)->first();
            if (isset($log)){
                if (is_null($log->betting_issue_num)|| $betting->betting_issue_num != $log->betting_issue_num)
                {
                    $log->betting_issue_num = $betting->betting_issue_num;
                    $log->save();
                }
            }
            else{
                $log = new SpiderLotteryTips();
                $log->mid = $betting->mid;
                $log->betting_issue_num = $betting->betting_issue_num;
                $log->save();
            }
        }

        //足球魔方
        $this->spiderCubeMatchIds();
        //雷速
        $this->spiderLeisuMatchIds();
    }

    /********** 各个网站情报 **********/
    //竞彩情报
    public function spiderBettingTips($issueNum){
        $curl = curl_init();
        //設置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, 'http://310win.fox008.com/analysis/detail/'.$issueNum.'.html');
        //設置首標
        curl_setopt($curl,CURLOPT_HEADER,1);
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
        $resultDivs = $html->find('div.data_newswz');
        dump('http://310win.fox008.com/analysis/detail/'.$issueNum.'.html');
        if (isset($resultDivs) && !empty($resultDivs)) {
            $match = SportBetting::where('betting_issue_num','=',$issueNum)
                ->where('type',0)
                ->first();
            if (isset($match) && isset($match->mid)) {
                //实力
                $lis1 = $resultDivs[0]->find('td')[0]->find('li');
                $lis2 = $resultDivs[0]->find('td')[count($resultDivs[0]->find('td')) - 1]->find('li');
                //先删后加
                $tips = LotteryTip::where('type','实力')
                    ->where('mid',$match->mid)
                    ->where('from',LotteryTip::k_lottery_tip_type_win)
                    ->where('lottery_type',1)
                    ->get();
                foreach ($tips as $tip){
                    $tip->delete();
                }
                foreach ($lis1 as $li) {
//                    dump($li->plaintext);
                    $tip = new LotteryTip();
                    $tip->from = LotteryTip::k_lottery_tip_type_win;
                    $tip->lottery_type = 1;
                    $tip->type = '实力';
                    $tip->is_host = 1;
                    $tip->mid = $match->mid;
                    $tip->text = $li->plaintext;
                    $tip->save();
                }
//                dump('---');
                //数据
                foreach ($lis2 as $li) {
//                    dump($li->plaintext);
                    $tip = new LotteryTip();
                    $tip->from = LotteryTip::k_lottery_tip_type_win;
                    $tip->lottery_type = 1;
                    $tip->type = '实力';
                    $tip->is_host = 0;
                    $tip->mid = $match->mid;
                    $tip->text = $li->plaintext;
                    $tip->save();
                }
//                dump('************');
                $lis1 = $resultDivs[1]->find('td')[0]->find('li');
                $lis2 = $resultDivs[1]->find('td')[count($resultDivs[1]->find('td')) - 1]->find('li');
                $tips = LotteryTip::where('type','数据')
                    ->where('mid',$match->mid)
                    ->where('from',LotteryTip::k_lottery_tip_type_win)
                    ->where('lottery_type',1)
                    ->get();
                foreach ($tips as $tip){
                    $tip->delete();
                }
                foreach ($lis1 as $li) {
//                    dump($li->plaintext);
                    $tip = new LotteryTip();
                    $tip->from = LotteryTip::k_lottery_tip_type_win;
                    $tip->lottery_type = 1;
                    $tip->type = "数据";
                    $tip->is_host = 1;
                    $tip->mid = $match->mid;
                    $tip->text = $li->plaintext;
                    $tip->save();
                }
//                dump('---');
                foreach ($lis2 as $li) {
//                    dump($li->plaintext);
                    $tip = new LotteryTip();
                    $tip->from = LotteryTip::k_lottery_tip_type_win;
                    $tip->lottery_type = 1;
                    $tip->type = "数据";
                    $tip->is_host = 0;
                    $tip->mid = $match->mid;
                    $tip->text = $li->plaintext;
                    $tip->save();
                }
//                dump('************');
                //近况
                $tips = LotteryTip::where('type','近况伤停')
                    ->where('mid',$match->mid)
                    ->where('from',LotteryTip::k_lottery_tip_type_win)
                    ->where('lottery_type',1)
                    ->get();
                foreach ($tips as $tip){
                    $tip->delete();
                }
                $lis1 = $resultDivs[2]->find('td')[0]->find('li');
                $lis2 = $resultDivs[2]->find('td')[count($resultDivs[2]->find('td')) - 1]->find('li');
                foreach ($lis1 as $li) {
//                    dump($li->plaintext);
                    $tip = new LotteryTip();
                    $tip->from = LotteryTip::k_lottery_tip_type_win;
                    $tip->lottery_type = 1;
                    $tip->type = "近况伤停";
                    $tip->is_host = 1;
                    $tip->mid = $match->mid;
                    $tip->text = $li->plaintext;
                    $tip->save();
                }
//                dump('---');
                foreach ($lis2 as $li) {
//                    dump($li->plaintext);
                    $tip = new LotteryTip();
                    $tip->from = LotteryTip::k_lottery_tip_type_win;
                    $tip->lottery_type = 1;
                    $tip->type = "近况伤停";
                    $tip->is_host = 0;
                    $tip->mid = $match->mid;
                    $tip->text = $li->plaintext;
                    $tip->save();
                }
            }
        }
        else{
            echo 'spider spiderBettingTips error'.'</br>';
        }
    }

    //章鱼情报
    public function spiderBetting8WinTips($issueNum){
        $num = substr($issueNum,strlen($issueNum) - 3,3);
        $date = substr($issueNum,0,strlen($issueNum) - 3);
        $week = date_create($date);
        $tmpIssueNum = $date.date('N',$week->getTimestamp()).$num;
        $curl = curl_init();
        //設置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, 'http://cms.8win.com/zybl/'.$tmpIssueNum);
        //設置首標
        curl_setopt($curl,CURLOPT_HEADER,1);
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
        $resultDivs = $html->find('div.bl-con');
        dump('http://cms.8win.com/zybl/'.$tmpIssueNum);
        if (isset($resultDivs) && !empty($resultDivs)) {
            $match = SportBetting::where('betting_issue_num','=',$issueNum)
                ->where('type',0)
                ->first();
            if (isset($match) && isset($match->mid)) {
                //先删后加
                $tips = LotteryTip::where('from',LotteryTip::k_lottery_tip_type_8win)
                    ->where('lottery_type',1)
                    ->where('mid',$match->mid)
                    ->get();
                foreach ($tips as $tip){
                    $tip->delete();
                }
                //实力
                foreach ($resultDivs as $content) {
                    $title = $content->find('h3')[0]->find('a')[0]->plaintext;
                    $isHost = $content->find('p.bl-con-time')[0]->find('span.zhuke')[0]->plaintext;
                    $isHost = str_replace(' ', '', $isHost);
                    $type = $content->find('p.bl-con-time')[0]->find('span.blType')[0]->plaintext;
                    $type = str_replace('】','',$type);
                    $type = str_replace('【','',$type);
                    $type = str_replace(' ','',$type);
                    $text = $content->find('div.abstract')[0]->plaintext;
                    $text = str_replace(' ', '', $text);
//                    dump($title.' ' .$isHost. ' '.$type . ' '. $text);

                    $tip = new LotteryTip();
                    $tip->from = LotteryTip::k_lottery_tip_type_8win;
                    $tip->lottery_type = 1;
                    $tip->type = $type;
                    $tip->is_host = strpos('主', $isHost) !== false;
                    $tip->mid = $match->mid;
                    $tip->text = $text;
                    $tip->title = $title;
//                    dump($title.' ' .$isHost. ' '.$type . ' '. $text);
                    $tip->save();
                }
            }
        }
        else{
            echo 'spider spiderBettingTips error'.'</br>';
        }
    }

    //足球魔方
    //爬取足球魔方比赛id(今天和明天的)
    public function spiderCubeMatchIds(){
        $nowHour = date('H');
        if ($nowHour <= 10){
            $today = date('Ymd', strtotime('-1 days'));
            $tomorrow = date('Ymd');
        } else {
            $today = date('Ymd');
            $tomorrow = date('Ymd', strtotime('+1 days'));
        }
        //今天的比赛
        $this->spiderCubeMatchIdsByDate($today);
        //明天的比赛
        $this->spiderCubeMatchIdsByDate($tomorrow);
    }

    //爬取足球魔方比赛id(今天和明天的)
    public function spiderCubeMatchIdsByDate($date){
        //今天的比赛
        $page = 1;
        $hasContent = true;
        while ($hasContent){
            $hasContent = $this->spiderCubeMatchIdsByDateAndPage($date, $page);
            $page++;
        }
    }

    //根据时间 爬取足球魔方比赛id
    private function spiderCubeMatchIdsByDateAndPage($date, $page){
        $curl = curl_init();
        //設置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, 'https://www.huanhuba.com/cube/'.$date.'-p'.$page.'.html');
        //設置首標
        curl_setopt($curl,CURLOPT_HEADER,1);
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
//        dump($html);
        $resultDivs = $html->find('a.mfcell');
        if (!isset($resultDivs) || count($resultDivs) <= 0){
            echo 'this list is empty! '.'https://www.huanhuba.com/cube/'.$date.'-p'.$page.'.html'.'</br>';
            return false;
        }
//        dump('http://www.huanhuba.com/cube/'.$date.'-p'.$page.'.html');
        foreach ($resultDivs as $content){
            $a = $content;
            $cubeMid = str_replace('.html','',str_replace('/cube/','',$a->getAttribute('href')));

            $timeContent = $a->find('span.timestamp')[0];
            echo $timeContent->plaintext.'</br>';
            $weekText = explode(' &nbsp; ', $timeContent->plaintext)[0];
            $timeText = explode(' &nbsp; ', $timeContent->plaintext)[1];
            $num = substr($weekText, -3);
            $dateStr = str_replace('-', '', $date);
            echo 'cube='.$cubeMid.'; num='.$num.'; date='.$dateStr.'; time='.$timeText.'</br>';

            //竞彩
            $sportBetting = SportBetting::query()
                ->where('num', $num)
                ->whereRaw('substring(betting_issue_num, 1, 8)='.$dateStr)->first();
            if (isset($sportBetting)){
                $spiderLotteryTip = SpiderLotteryTips::findByCubeId($cubeMid);
                if (is_null($spiderLotteryTip)){
                    $spiderLotteryTip = SpiderLotteryTips::findByMatchId($sportBetting->mid);
                }
                if (!isset($spiderLotteryTip)){
                    $spiderLotteryTip = new SpiderLotteryTips();
                }
                $spiderLotteryTip->betting_issue_num = $sportBetting->betting_issue_num;
                $spiderLotteryTip->mid = $sportBetting->mid;
                $spiderLotteryTip->cube_mid = $cubeMid;
                $spiderLotteryTip->save();
            }
            else{
                //其他
                $leisuTimeStrings = explode(' ',$timeText);
                $matchTime = date_create(substr($date, 0,4).'-'.$leisuTimeStrings[0].' '.$leisuTimeStrings[1]);
                $matchTime2 = date_add(date_create(substr($date, 0,4).'-'.$leisuTimeStrings[0].' '.$leisuTimeStrings[1]),date_interval_create_from_date_string('1 year'));
                $hname = str_replace(' ','',$a->find('div.hometeam')[0]->plaintext);
                if (array_key_exists($hname,$this->zhongchao)) {
                    $hname = $this->zhongchao[$hname];
                }
                $aname = str_replace(' ','',$a->find('div.awayteam')[0]->plaintext);
                if (array_key_exists($aname,$this->zhongchao)) {
                    $aname = $this->zhongchao[$aname];
                }
                echo 'hname='.$hname.' aname='.$aname.' time='.date_format($matchTime,'y-m-d h:i').'</br>';
                $match = Match::where(function ($q)use($matchTime,$matchTime2){
                    $q->where('time',date_format($matchTime,'Y-m-d H:i'))
                        ->orwhere('time',date_format($matchTime2,'Y-m-d H:i'));
                })
                    ->where(function ($q) use($hname,$aname){
                        $q->where('win_hname',$hname)
                            ->orwhere('win_aname',$aname);
                    })
                    ->first();
                if (isset($match)) {
                    $spiderLotteryTip = SpiderLotteryTips::findByCubeId($cubeMid);
                    if (is_null($spiderLotteryTip)){
                        $spiderLotteryTip = SpiderLotteryTips::findByMatchId($match->id);
                    }
                    if (!isset($spiderLotteryTip)) {
                        $spiderLotteryTip = new SpiderLotteryTips();
                    }
                    $spiderLotteryTip->mid = $match->id;
                    $spiderLotteryTip->cube_mid = $cubeMid;
                    $spiderLotteryTip->save();
                }
                else{
                    echo $hname .' '.$aname.' 没有找到比赛窝'.'</br>';
                }
            }
        }
        return true;
    }

    //足球魔方情报
    public function spiderBettingCubeTipsByCubeId($bettings, $isRefresh = false){
        if (is_null($bettings) || is_null($bettings->cube_mid)){
            return;
        }
        //模拟登陆
        $post_data = ['username'=>'88886666@qq.com', 'password'=>'111111'];
        $post_curl = curl_init();
        //設置你需要抓取的URL
        curl_setopt($post_curl, CURLOPT_URL, 'http://market.huanhuba.com/Inhome/user/cubegoal_dologin');
        //設置cURL參數，要求結果保存到字符串中還是輸出到屏幕上。
        curl_setopt($post_curl, CURLOPT_RETURNTRANSFER, 1);
        //設置首標
        curl_setopt($post_curl,CURLOPT_HEADER,1);
        // post数据
        curl_setopt($post_curl, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($post_curl, CURLOPT_POSTFIELDS, $post_data);
        //運行cURL，請求網頁
        $output = curl_exec($post_curl);
        //關閉URL請求
        curl_close($post_curl);
        $lines = explode("\r\n", $output);
        $cookies = '';
        foreach ($lines as $line) {
            if (preg_match('#Set-Cookie: (PHPSESSID=[0-9a-z]+;).*#', $line, $matches)) {
                $cookies .= $matches[1];
            }
            else if (preg_match('#Set-Cookie: (SSID=[0-9a-z]+;).*#', $line, $matches)) {
                $cookies .= $matches[1];
            }
            else if (preg_match('#Set-Cookie: (app_users=[0-9a-z]+;).*#', $line, $matches)) {
                $cookies .= $matches[1];
            }
            else if (preg_match('#Set-Cookie: (app_user_new=[0-9a-z]+;).*#', $line, $matches)){
                $cookies .= $matches[1];
            }
        }
//        $cookies .='cloudwise_client_id=fe5eeeae-f8a4-c717-b9f1-de34c79c5d70';
//        dump($cookies);
        $cubeMid = $bettings->cube_mid;
        $mid = $bettings->mid;

        $curl = curl_init();
        //設置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, 'https://www.huanhuba.com/cube/'.$cubeMid.'.html');
        //设置cookie
        curl_setopt($curl, CURLOPT_COOKIE, 'ci_session=hv049k1akdj7rkl3p6fntd8b9si7pvds;');
//        curl_setopt($curl, CURLOPT_COOKIE, 'app_user_new=1124401; domain=.huanhuba.com; path=/');
//        curl_setopt($curl, CURLOPT_COOKIE, 'app_users=1124401; domain=.huanhuba.com; path=/');
        //設置首標
        curl_setopt($curl,CURLOPT_HEADER,1);
        //設置cURL參數，要求結果保存到字符串中還是輸出到屏幕上。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //運行cURL，請求網頁
        $data = curl_exec($curl);
        //關閉URL請求
        curl_close($curl);
        //顯示獲得的數據
        $html = new \simple_html_dom();
        $html->load($data);
        dump('https://www.huanhuba.com/cube/'.$cubeMid.'.html');

        if (count($html->find('div.share-page')) > 0) {
            $resultDiv = $html->find('div.share-page')[0];
        }
        if (isset($resultDiv) && !empty($resultDiv)) {
            //先删后加
            $tips = LotteryTip::where('from',LotteryTip::k_lottery_tip_type_cube)
                ->where('lottery_type',1)
                ->where('mid',$mid)
                ->get();
            foreach ($tips as $tip){
                $tip->delete();
            }
            //结论
            $info1 = $html->find('div.main-content')[0]->find('h1');
            if(isset($info1) && count($info1)>0){
                $result = $info1[0];
            }

            $typeRank = 1;
            if (isset($result)) {
                $resultText = $result->plaintext;
                $type = '分析结论';
                $this->spiderCubeTipsByString($resultText, $mid, $type, -1, $typeRank);
                dump($type, $typeRank);
                $typeRank++;
            }

            //赛果预测
            $match_magic = $resultDiv->find('div.magic-count')[0];
            if (isset($match_magic) && count($match_magic->find('div.magic-title')) > 0) {
                $type = $match_magic->find('div.magic-title')[0]->plaintext;
                dump($type, $typeRank);
                $this->onLotteryTipSave($type, 1, $mid, $match_magic->find('div.home-percent')[0]->plaintext, $typeRank);
                $this->onLotteryTipSave($type, 1, $mid, $match_magic->find('div.home-dec')[0]->find('h3')[0]->plaintext, $typeRank);
                $this->onLotteryTipSave($type, 0, $mid, $match_magic->find('div.away-percent')[0]->plaintext, $typeRank);
                $this->onLotteryTipSave($type, 0, $mid, $match_magic->find('div.away-dec')[0]->find('h3')[0]->plaintext, $typeRank);
                $typeRank++;
            }
            //tips
            $tipsContents = $resultDiv->find('div.team-power');
            foreach ($tipsContents as $content) {
                $type = $content->find('div.magic-title')[0]->plaintext;
                dump($type, $typeRank);

                //主
                $homeContent = $content->find('div.home-team-magic')[0];
                $this->onLotteryTipSave($type, 1, $mid, $homeContent->find('div.team-title')[0]->find('h3')[0]->plaintext, $typeRank);//百分比
                $this->spiderCubeTipsByCurl($homeContent, $mid, $type, 1, $typeRank);
                //客
                $awayContent = $content->find('div.away-team-magic')[0];
                $this->onLotteryTipSave($type, 0, $mid, $awayContent->find('div.team-title')[0]->find('h3')[0]->plaintext, $typeRank);//百分比
                $this->spiderCubeTipsByCurl($awayContent, $mid, $type, 0, $typeRank);
                $typeRank++;
            }
            //内参
            $innerContents = $resultDiv->find('div.jbm-box');
            foreach ($innerContents as $content){
                $heads = $content->children;
                if (!isset($heads) || count($heads) < 1){
                    continue;
                }
                $type = $heads[0]->plaintext;
                dump($type, $typeRank);
                $hasContent = false;
                //主
                $home = $content->find('div.match-home-content');
                if (isset($home) && count($home) > 0){
                    $homeP = $home[0]->find('p');
                    if (isset($homeP)&&count($homeP)>0){
                        $homeText = $homeP[0]->plaintext;
                        $this->spiderCubeTipsByString($homeText, $mid, $type, 1, $typeRank);
                        $hasContent = true;
                    }
                }
                //客
                $away = $content->find('div.match-away-content');
                if (isset($away) && count($away) > 0){
                    $awayP = $away[0]->find('p');
                    if (isset($awayP)&&count($awayP)>0){
                        $awayText = $awayP[0]->plaintext;
                        $this->spiderCubeTipsByString($awayText, $mid, $type, 0, $typeRank);
                        $hasContent = true;
                    }
                }
                //综合
                $multi = $content->find('div.medium');
                if (isset($multi) && count($multi) > 0){
                    $multiP = $multi[0]->find('p');
                    if (isset($multiP)&&count($multiP)>0) {
                        $multiText = $multiP[0]->plaintext;
                        $this->spiderCubeTipsByString($multiText, $mid, $type, -1, $typeRank);
                        $hasContent = true;
                    }
                }
                if ($hasContent){
                    $typeRank++;
                }
            }
        }
        else{
            echo 'spider spiderBettingTips error'.'</br>';
        }
    }

    private function spiderCubeTipsByCurl($content, $mid, $type, $isHost, $rank){
        if ($isHost){
            $dec_ps = $content->find('div.home-dec')[0]->find('p');
        } else{
            $dec_ps = $content->find('div.away-dec')[0]->find('p');
        }
        foreach ($dec_ps as $p){
            $this->onLotteryTipSave($type, $isHost, $mid, $p->plaintext, $rank);
        }
    }

    private function spiderCubeTipsByString($str, $mid, $type, $isHost, $rank){
        $strs = explode('【', $str);
        foreach ($strs as $text){
            $tempText = str_replace(' ', '', $text);
            $tempText = str_replace('"', '', $tempText);
            if (isset($tempText)&&''!=$tempText){
                if (str_contains($tempText, '】')){
                    $this->onLotteryTipSave($type, $isHost, $mid, '【'.$tempText, $rank);
                }else{
                    $this->onLotteryTipSave($type, $isHost, $mid, $tempText, $rank);
                }
            }
        }
    }

    private function onLotteryTipSave($type, $isHost, $mid, $text, $rank, $from = LotteryTip::k_lottery_tip_type_cube, $lotteryType = 1, $title = NULL){
        $tip = new LotteryTip();
        $tip->type_rank = $rank;
        $tip->from = $from;
        $tip->lottery_type = $lotteryType;
        $tip->type = $type;
        $tip->is_host = $isHost;
        $tip->mid = $mid;
        $tip->text = $text;
        $tip->title = $title;
        $tip->save();
    }

    //雷速id与料狗matchid对应
    private function spiderLeisuMatchIds(){
//        $nowHour = date('H');
//        if ($nowHour <= 10){
//            $today = date('Ymd', strtotime('-1 days'));
//            $tomorrow = date('Ymd');
//        } else {
        //雷速是按自然日来获取比赛信息的
        $today = date('Ymd');
        $tomorrow = date('Ymd', strtotime('+1 days'));
//        }
        //今天
        $this->spiderLeisuMatchIdsByDate($today);
        //明天
        $this->spiderLeisuMatchIdsByDate($tomorrow);
    }

    //雷速id与料狗matchid对应 by date
    private function spiderLeisuMatchIdsByDate($date){
        $url = 'https://api.leisu.com/app/square/schedule?app=2&d='.$date.'&platform=2&ver=2.1.1';
        $str = $this->spiderTextFromUrl($url);
        if (isset($str)){
            $jm = json_decode($str);
            $list = $jm->list;
            foreach ($list as $data){
                $dt = new \DateTime();
                $dt->setTimestamp($data->time);
                $hname = $data->home->name;
                $aname = $data->away->name;
                $match = Match::where('time',$dt)
                    ->where(function ($q)use($hname,$aname){
                        $q->where('win_hname',$hname)
                            ->orwhere('win_aname',$aname);
                    })
                    ->first();
                $leisuId = $data->sid;

                if (isset($match)) {
                    $spiderLotteryTip = SpiderLotteryTips::findByLeisuId($leisuId);
                    //这场比赛有没有记录
                    if (is_null($spiderLotteryTip)){
                        $spiderLotteryTip = SpiderLotteryTips::findByMatchId($match->id);
                    }
                    if (!isset($spiderLotteryTip)) {
                        $spiderLotteryTip = new SpiderLotteryTips();
                    } else {
                    }
                    $spiderLotteryTip->mid = $match->id;
                    $spiderLotteryTip->leisu_mid = $leisuId;
                    $spiderLotteryTip->save();
                }
                else{
                    echo $hname.' '.$aname.' 没有找到比赛窝'.'</br>';
                }
            }
        }
    }

    //爬雷速爆料数据
    private function spiderBettingLeisuTipsByLeisuId($log){
        if (is_null($log->leisu_mid))
            return;
        $url = 'https://api.leisu.com/app/live/matchinfo?sid='.$log->leisu_mid.'&app=2&platform=2&ver=2.1.1';
        $str = $this->spiderTextFromUrl($url);
        if (isset($str)) {
            $jm = json_decode($str);
            if (isset($jm)) {
                //先删后加
                $tips = LotteryTip::where('from',LotteryTip::k_lottery_tip_type_leisu)
                    ->where('mid',$log->mid)
                    ->get();
                foreach ($tips as $tip){
                    $tip->delete();
                }

                //有利
                $goods = $jm->good;
                //主队
                $homes = $goods->home;
                foreach ($homes as $data){
                    $this->onLotteryTipSave('有利情报',1,$log->mid,$data[1],1,LotteryTip::k_lottery_tip_type_leisu,1);
                }
                //客队
                $aways = $goods->away;
                foreach ($aways as $data){
                    $this->onLotteryTipSave('有利情报',0,$log->mid,$data[1],1,LotteryTip::k_lottery_tip_type_leisu,1);
                }

                //不利
                $bads = $jm->bad;
                //主队
                $homes = $bads->home;
                foreach ($homes as $data){
                    $this->onLotteryTipSave('不利情报',1,$log->mid,$data[1],2,LotteryTip::k_lottery_tip_type_leisu,1);
                }
                //客队
                $aways = $bads->away;
                foreach ($aways as $data){
                    $this->onLotteryTipSave('不利情报',0,$log->mid,$data[1],2,LotteryTip::k_lottery_tip_type_leisu,1);
                }

                //中立
                $neutrals = $jm->neutral;
                foreach ($neutrals as $data){
                    $this->onLotteryTipSave('中立情报',-1,$log->mid,$data[1],3,LotteryTip::k_lottery_tip_type_leisu,1);
                }
            }
        }
    }
}