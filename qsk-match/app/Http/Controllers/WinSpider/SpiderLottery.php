<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 16/11/22
 * Time: 下午12:05
 */

namespace App\Http\Controllers\WinSpider;

use App\Models\LiaoGouModels\Lottery;
use App\Models\LiaoGouModels\LotteryBonus;
use App\Models\LiaoGouModels\LotteryDetail;
use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\MatchEuropePrediction;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;

include_once('lib/simple_html_dom.php');
trait SpiderLottery{
    /**
     * 填充奖金为空情况
     */
    public function spiderBounseNull(){
//        $bouns = LotteryBonus::where('basic_bonus','=','--')
//            ->groupBy('lid')
//            ->select('lid')
//            ->first();
//        if (isset($bouns)) {
            $lottery = Lottery::query()->where('detail', 'like', '%--%')->orderBy('id')->first();
//            $lottery = Lottery::where('id', $bouns->lid)->first();
            if (isset($lottery) && isset($lottery->win_id)){
                if (is_null($lottery->type) || 0 == $lottery->type){
                    $this->spiderLottery($lottery->win_id);
                    $this->spiderNow($lottery->issue_num);
                }
                else if (1 == $lottery->type){
                    $this->spiderLotteryFour($lottery->win_id);
                    $this->spiderFourNow($lottery->issue_num);
                }
                else if (2 == $lottery->type){
                    $this->spiderLotterySix($lottery->win_id);
                    $this->spiderSixNow($lottery->issue_num);
                }
            }
//        }
    }

    /**
     * 爬取最近几期的竞彩（包括任九、6场和4场）
     */
    public function fillLotteryByLastFilled(Request $request) {
        $count = $request->input('count', 3);
        $lotteries = DB::select("SELECT a.* FROM liaogou_lottery.lotteries a WHERE EXISTS (
    SELECT COUNT(*) FROM liaogou_lottery.lotteries
    WHERE ((lotteries.fill_match is not null and a.fill_match is not null) and (lotteries.type = a.type or (isnull(lotteries.type) and isnull(a.type)))) AND issue_num > a.issue_num HAVING COUNT(*) < $count
    ) and fill_match is not null ORDER BY a.type, a.issue_num desc;");

        foreach ($lotteries as $lottery) {
            $this->spiderLotteryByType($lottery);
        }
    }

    /**
     * 根据期数 和竞彩类型 爬取最新数据
     */
    public function fillLotteryByIssue(Request $request) {
        $type = $request->input('type', 0);
        $issueNum = $request->input('issue');
        $count = $request->input('count');
        if (isset($count) && $count > 0) {
            for ($i=0;$i<$count;$i++) {
                $this->spiderLotteryByIssue($type, $issueNum-$i);
            }
        }else {
            $this->spiderLotteryByIssue($type, $issueNum);
        }
    }

    /**
     * 根据期数 和竞彩类型 爬取最新数据
     */
    private function spiderLotteryByIssue($type, $issueNum) {
        if (isset($issueNum)) {
            $lottery = Lottery::query()->where(function ($q) use ($type) {
                if ($type == 0) {
                    $q->where('type', '0')
                        ->orwhereNull('type');
                } else {
                    $q->where('type', $type);
                }
            })->where('issue_num', $issueNum)->first();
            $this->spiderLotteryByType($lottery, $type, $issueNum);
        } else{
            echo 'issueNum is null!<br>';
        }
    }

    private function spiderLotteryByType($lottery, $type=0, $issueNum=0) {
        if (isset($lottery)) {
            if ($lottery->type) {
                if ($lottery->type == 1) {
                    $this->spiderLotteryFour($lottery->win_id);
                    $this->spiderFourNow($lottery->issue_num);
                } else if ($lottery->type == 2) {
                    $this->spiderLotterySix($lottery->win_id);
                    $this->spiderSixNow($lottery->issue_num);
                } else {
                    $this->spiderLottery($lottery->win_id);
                    $this->spiderNow($lottery->issue_num);
                }
            } else {
                $this->spiderLottery($lottery->win_id);
                $this->spiderNow($lottery->issue_num);
            }
        } else {
            echo 'type='.$type.' issueNum='.$issueNum.' Lottery is null!<br>';
        }
    }

    /**
     * 爬最新数据
     * @param $issueNum 期数
     */
    public function spiderNow($issueNum){
        echo 'http://www.310win.com/buy/toto9.aspx?issueNum='.($issueNum>0?$issueNum:"").'</br>';
        $curl = curl_init();
        //設置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, 'http://www.310win.com/buy/toto9.aspx?issueNum='.($issueNum>0?$issueNum:""));
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
        $resultDivs = $html->find('table.socai');
        if (isset($resultDivs) && !empty($resultDivs)) {
            //爬info
            $this->spiderByHtml($issueNum>0?$issueNum:$html->find('.SelectedIssue')[0]->plaintext,$html);
            //爬其他
            echo 'issue '. $issueNum .'</br>';
            if ($issueNum == 0)
            {
                //如果是当前,没有issueNum,看看Issue有多少未来的其他继续爬
                $issue = $html->find('.Issue');
                if (isset($issue))
                {
                    for ($i = 0 ; $i < count($issue);$i++)
                    {
                        $issue_num = strip_tags($html->find('.Issue')[$i]->plaintext);
                        echo 'issue num '. $issue_num . '</br>';
                        if ($issue_num) {
                            $lotteryInfo = Lottery::where('issue_num', '=', $issue_num)
                                ->whereNull('type')
                                ->first();
                            if (is_null($lotteryInfo)) {
                                $lotteryInfo = new Lottery();
                            }
                            $lotteryInfo->issue_num = $issue_num . '';
                            $lotteryInfo->save();
                        }
                    }
                }
            }
        }
        else{
            echo 'spider spiderNow error'.'</br>';
        }
    }

    /**
     * 通过html获取足彩数据(未开奖期数用)
     * @param $issueNum 期数id
     * @param $html 网页数据
     */
    private function spiderByHtml($issueNum, $html){
        $resultDivs = $html->find('table.socai');
        $lotteryInfo = Lottery::where('issue_num','=',$issueNum)
            ->whereNull('type')
            ->first();
        if(is_null($lotteryInfo)){
            $lotteryInfo = new Lottery();
        }
        if($issueNum) {
            $lotteryInfo->issue_num = $issueNum;
        }
        //未开始的比赛是11-11 11:11的形式,所以要加年回去
        $tmpTime = str_replace('当期截止：','',strip_tags($html->find('#labState')[0]->plaintext));
        $currentYear = date_format(date_create()->setTimestamp(strtotime("now")), 'Y');
        //今年这个时候
        $bj = $currentYear.'-'.$tmpTime;
        //大于就是还在今年内
        if ($bj >= date_format(date_create()->setTimestamp(strtotime("now")),'Y-m-d H:i')){
            $tmpTime = $bj;
        }
        else{
            //小于是下一年了
            $tmpTime = ($currentYear+1).'-'.$tmpTime;
        }
        if (strpos($tmpTime, '已返奖') !== false){

        }
        else{
            if (is_null($lotteryInfo->award_time))
                $lotteryInfo->award_time = $tmpTime;
        }
        $lotteryInfo->save();

        $hasMid = true;
        //爬每场比赛
        for ($index = 1 ; $index < 15; $index++)
        {
            $item = current($resultDivs)->find('#row_'.$index,0);
            //是否有结果
            $isEnd = count($item->find('.bred')) > 0 ? true : false;
            $i['num'] = $item->find('td')[0]->plaintext;
            $i['league'] = $item->find('td')[1]->plaintext;
            $lidString = $item->find('td')[1]->find('a')[0]->getAttribute('href');
            $lidString = explode('/',$lidString);
            //赛事别名
            $lid = explode('.',end($lidString))[0];
//            AliasController::bindLeagueIdWithName($lid,$i['league']);
            //未开始的比赛是11-11 11:11的形式,所以要加年回去
            $tmpTime = $item->find('td')[2]->plaintext;
            $currentYear = date_format(date_create()->setTimestamp(strtotime("now")), 'Y');
            //今年这个时候
            $bj = $currentYear.'-'.$tmpTime;
            if ($isEnd){
                //已开奖就是今年的
                if ($bj > date_format(date_create()->setTimestamp(strtotime("now")),'Y-m-d H:i')){
                    $tmpTime = ($currentYear-1).'-'.$tmpTime;
                }
                else{
                    $tmpTime = $bj;
                }
            }
            //大于就是还在今年内
            else if ($bj > date_format(date_create()->setTimestamp(strtotime("now")),'Y-m-d H:i')){
                $tmpTime = $bj;
            }
            else{
                //小于是下一年了
                $tmpTime = ($currentYear+1).'-'.$tmpTime;
            }
            $i['time'] = $tmpTime;
            $i['home'] = strip_tags($item->find('td')[3]->plaintext);
            $i['home'] = preg_replace('/\s/','',$i['home']);
            $i['guest'] = strip_tags($item->find('td')[4]->plaintext);
            $i['guest'] = preg_replace('/\s/','',$i['guest']);

            //获取比赛id
            $mid = explode('_',$item->find('td')[3]->find('a')[0]->getAttribute('id'))[1];
            //获取主队id
            $hidString = $item->find('td')[3]->find('a')[0]->getAttribute('onmouseover');
            $hidString = explode('.htm\',',$hidString)[0];
            $hid = explode('_',$hidString)[1];
//            AliasController::bindTeamIdWithName($hid,$i['home']);
//            LiaogouAlias::bindingLotteryTeam($hid,$i['home']);
            //获取客队id
            $aidString = $item->find('td')[4]->find('a')[0]->getAttribute('onmouseover');
            $aidString = explode('.htm\',',$aidString)[0];
            $aid = explode('_',$aidString)[1];
//            AliasController::bindTeamIdWithName($aid,$i['guest']);
//            LiaogouAlias::bindingLotteryTeam($aid,$i['guest']);

            $match = Match::getMatchWith($mid,'win_id');
            //每场比赛
            $lottery = LotteryDetail::where('lottery_id','=',$lotteryInfo->id)
                ->where(function ($q)use($match,$i){
                    if (!is_null($match)){
                        $q->where('mid','=',$match->id);
                    }
                    else{
                        $q->where('league','=',$i['league'])
                            ->where('hname','=',$i['home'])
                            ->where('aname','=',$i['guest']);
                    }
                })
                ->first();
            //没有的清空一次
            if (is_null($lottery)){
                $lottery = LotteryDetail::where('lottery_id','=',$lotteryInfo->id)
                    ->where('num','=',$i['num'])->get();
                if (count($lottery) > 0){
                    dump($lottery);
                    echo 'delete ';
                    foreach ($lottery as $tmp){
                        $tmp->delete();
                    }
                }
                $lottery = new  LotteryDetail();
            }

            $lottery->lottery_id = $lotteryInfo->id;

            if (!is_null($match)) {
                $lottery->mid = $match->id;
                //更新竞彩数据到表
                $match->genre = $match->genre | 1 << 2;
                $match->lname = $i['league'];
                $match->save();
            }
            else{
                dump(explode('_',$item->find('td')[3]->find('a')[0]->getAttribute('id'))[1]);
                $hasMid = false;
            }
            if ($isEnd){
                //里面还有td,这里要倒数的td
                $totalTdCount = count($item->find('td'));
                $score = strip_tags($item->find('td')[$totalTdCount - 1 - 2]->plaintext);
                $lottery->hscore = strlen(explode("-", $score)[0])>0?explode("-", $score)[0]:0;
                $lottery->ascore = strlen(explode("-", $score)[1])>0?explode("-", $score)[1]:0;

                $hscore = strip_tags($item->find('td')[$totalTdCount - 1 - 1]->plaintext);
                $lottery->hfscore = strlen(explode("-", $hscore)[0])>0?explode("-", $hscore)[0]:0;
                $lottery->afscore = strlen(explode("-", $hscore)[1])>0?explode("-", $hscore)[1]:0;
                $result = strip_tags($item->find('td')[$totalTdCount - 1]->plaintext);
                if (0 == strcmp('胜',$result))
                {
                    $lottery->result = 3;
                }
                else if (0 == strcmp('平',$result))
                {
                    $lottery->result = 1;
                }
                else if (0 == strcmp('负',$result))
                {
                    $lottery->result = 0;
                }
                else{
                    $lottery->result = '*';
                }
            }

            $lottery->aname = $i['guest'];
            $lottery->hname = $i['home'];
            $lottery->date = $i['time'];
            $lottery->league = $i['league'];
            $lottery->num = $i['num'];
            $lottery->save();

            //保存到欧赔计算表,自动爬odd
            if ($lottery->mid > 0) {
                $prediction = MatchEuropePrediction::where('id', '=', $lottery->mid)->first();
                if (is_null($prediction)){
                    $prediction = new MatchEuropePrediction();
                    $prediction->id = $match->id;
                    $prediction->save();
                }
            }
        }
        if ($hasMid) {
            $lotteryInfo->fill_match = 1;
            $lotteryInfo->save();
        }
    }


    /**
     * 爬历史数据框架,主要用于获取足彩期数对应id,结果等需要用这个id爬
    */
    public function spiderLotteryHistoryFrame(){
        $curl = curl_init();
        //設置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, 'http://www.310win.com/zucai/14changshengfucai/kaijiang_zc_1.html');
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
        $resultDivs = $html->find('#dropIssueNum');
        if (!is_null($resultDivs)){
            //遍历所有历史足彩数据
            $items = current($resultDivs)->find('option');
            foreach ($items as $item) {
                echo 'spider lotter '.$item->value.'</br>';
                $lotteryInfo = Lottery::where('win_id','=',$item->value)
                    ->whereNull('type')
                    ->first();
                if (is_null($lotteryInfo))
                {
                    $lotteryInfo = Lottery::where('issue_num','=',strip_tags($item->plaintext))
                        ->whereNull('type')
                        ->first();
                    $lotteryInfo = isset($lotteryInfo)?$lotteryInfo:new Lottery();
                    $lotteryInfo->win_id = $item->value;
                    //更新了win_id,爬一次比赛结果
                    $lotteryInfo->fill_match = null;
                }
                $lotteryInfo->issue_num = strip_tags($item->plaintext);
                $lotteryInfo->save();
            }
        }
        else{
            echo 'spider lottery no data '.'</br>';
        }
    }

    /**
     * 根据彩票id爬数据,例如这期多少人中等
     */
    public function spiderLotteryHistory(){
        $lotteryInfo = Lottery::whereNotNull('win_id')
            ->whereNull('result')
            ->whereNull('type')
            ->orderBy('issue_num','desc')
            ->first();
        echo $lotteryInfo;
        if (isset($lotteryInfo))
            $this->spiderLottery($lotteryInfo->win_id);
        else
            echo 'spider lottery done';
    }

    /**
     * 根据彩票id爬历史期数数据
     * @param $volId 彩票id
     */
    private function spiderLottery($volId)
    {
        $str = $this->spiderTextFromUrl('http://www.310win.com/Info/Result/Soccer.aspx?load=ajax&typeID=1&IssueID='.$volId);
        $str = str_replace('	','',$str);
        $jm = json_decode($str,true);
        if (is_null($jm))
        {
            echo 'error volid '. $volId . '</br>';
            return;
        }

        echo 'spider lottery info '.$jm['IssueNum'].'</br>';

        //投注信息
        $lotteryInfo = Lottery::where('win_id','=',$volId)
            ->whereNull('type')
            ->first();
        if (is_null($lotteryInfo))
        {
            $lotteryInfo = new Lottery();
            $lotteryInfo->win_id = $volId;
        }
        $lotteryInfo->issue_num = $jm['IssueNum'];
        if (is_null($lotteryInfo->award_time)) {
            $lotteryInfo->award_time = $jm['AwardTime'];
        }
        $lotteryInfo->end_time = $jm['CashInStopTime'];
        $lotteryInfo->result = $jm['Result'];//str_replace('*','-',$jm['Result']);
        $lotteryInfo->detail = strip_tags($jm['Bottom']);
        $lotteryInfo->save();

        //几等奖数据
        foreach ( $jm['Bonus'] as $unit ) {
            $bonus = LotteryBonus::where('lid','=',$lotteryInfo->id)
                ->where('grade','=',$unit['Grade'])
                ->first();
            if (is_null($bonus)) {
                $bonus = new LotteryBonus();
                $bonus->lid = $lotteryInfo->id;
            }
            $bonus->grade = $unit['Grade'];
            $bonus->basic_stakes = $unit['BasicStakes'];
            $bonus->basic_bonus = strip_tags($unit['BasicBonus']);
            $bonus->save();
        }
    }

    /**
     * 填充比赛,足彩数据爬回来不包括比赛,需要这个另外爬数据(lotterydetail表)
     */
    public function lotteryFillMatch(){
        $lottery = Lottery::whereNull('fill_match')
            ->whereNull('type')
            ->orderby('issue_num','desc')
            ->first();
        if (isset($lottery)){
            $this->spiderNow($lottery->issue_num);
        }
    }

    //以下为4场
    /**
     * 爬历史数据框架,主要用于获取足彩期数对应id,结果等需要用这个id爬
     */
    public function spiderLotteryFourHistoryFrame(){
        $curl = curl_init();
        //設置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, 'http://www.310win.com/zucai/4changjinqiucai/kaijiang_zc_4.html');
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
        $resultDivs = $html->find('#dropIssueNum');
        if (!is_null($resultDivs)){
            //遍历所有历史足彩数据
            $items = current($resultDivs)->find('option');
            foreach ($items as $item) {
                echo 'spider lotter '.$item->value.'</br>';
                $lotteryInfo = Lottery::where('win_id','=',$item->value)
                    ->where('type','=',1)
                    ->first();
                if (is_null($lotteryInfo))
                {
                    $lotteryInfo = Lottery::where('issue_num','=',strip_tags($item->plaintext))
                        ->where('type','=',1)
                        ->first();
                    $lotteryInfo = isset($lotteryInfo)?$lotteryInfo:new Lottery();
                    $lotteryInfo->win_id = $item->value;
                    $lotteryInfo->type = 1;
                    //更新了win_id,爬一次比赛结果
                    $lotteryInfo->fill_match = null;
                }
                $lotteryInfo->issue_num = strip_tags($item->plaintext);
                $lotteryInfo->save();
            }
        }
        else{
            echo 'spider lottery no data '.'</br>';
        }
    }

    /**
     * 根据彩票id爬数据,例如这期多少人中等
     */
    public function spiderLotteryFourHistory(){
        $lotteryInfo = Lottery::whereNotNull('win_id')
            ->whereNull('result')
            ->where('type','=',1)
            ->orderBy('issue_num','desc')
            ->first();
        echo $lotteryInfo;
        if (isset($lotteryInfo))
            $this->spiderLotteryFour($lotteryInfo->win_id);
        else
            echo 'spider lottery done';
    }

    /**
     * 根据彩票id爬历史期数数据
     * @param $volId 彩票id
     */
    private function spiderLotteryFour($volId)
    {
        $str = $this->spiderTextFromUrl('http://www.310win.com/Info/Result/Soccer.aspx?load=ajax&typeID=4&IssueID='.$volId);
        $str = str_replace('	','',$str);
        $jm = json_decode($str,true);
        if (is_null($jm) || is_null($jm['Table']))
        {
            echo 'error volid '. $volId . '</br>';
            return;
        }

        echo 'spider lottery info '.$jm['IssueNum'].'</br>';

        //投注信息
        $lotteryInfo = Lottery::where('win_id','=',$volId)
            ->where('type','=',1)
            ->first();
        if (is_null($lotteryInfo))
        {
            $lotteryInfo = new Lottery();
            $lotteryInfo->win_id = $volId;
            $lotteryInfo->type = 1;
        }
        $lotteryInfo->issue_num = $jm['IssueNum'];
        $lotteryInfo->award_time = $jm['AwardTime'];
        $lotteryInfo->end_time = $jm['CashInStopTime'];
        $lotteryInfo->result = $jm['Result'];//str_replace('*','-',$jm['Result']);
        $lotteryInfo->detail = strip_tags($jm['Bottom']);
        $lotteryInfo->save();

        //几等奖数据
        foreach ( $jm['Bonus'] as $unit ) {
            $bonus = LotteryBonus::where('lid','=',$lotteryInfo->id)
                ->where('grade','=',$unit['Grade'])
                ->first();
            if (is_null($bonus)) {
                $bonus = new LotteryBonus();
                $bonus->lid = $lotteryInfo->id;
            }
            $bonus->grade = $unit['Grade'];
            $bonus->basic_stakes = $unit['BasicStakes'];
            $bonus->basic_bonus = strip_tags($unit['BasicBonus']);
            $bonus->save();
        }
    }

    public function lotteryFourFillMatch(){
        $lottery = Lottery::whereNull('fill_match')
            ->where('type','=',1)
            ->orderby('issue_num','desc')
            ->first();
        if (isset($lottery)){
            $this->spiderFourNow($lottery->issue_num);
        }
    }

    /**
     * 爬最新数据
     * @param $issueNum 期数
     */
    public function spiderFourNow($issueNum){
        echo 'http://www.310win.com/buy/toto4.aspx?issueNum='.($issueNum>0?$issueNum:"").'</br>';
        $curl = curl_init();
        //設置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, 'http://www.310win.com/buy/toto4.aspx?issueNum='.($issueNum>0?$issueNum:""));
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
        $resultDivs = $html->find('table.socai');
        if (isset($resultDivs) && !empty($resultDivs)) {
            //爬info
            $this->spiderFourByHtml($issueNum>0?$issueNum:$html->find('.SelectedIssue')[0]->plaintext,$html);
            //爬其他
            echo 'issue '. $issueNum .'</br>';
            if ($issueNum == 0)
            {
                //如果是当前,没有issueNum,看看Issue有多少未来的其他继续爬
                $issue = $html->find('.Issue');
                if (isset($issue))
                {
                    for ($i = 0 ; $i < count($issue);$i++)
                    {
                        $issue_num = strip_tags($html->find('.Issue')[$i]->plaintext);
                        echo 'issue num '. $issue_num . '</br>';
                        if ($issue_num) {
                            $lotteryInfo = Lottery::where('issue_num', '=', $issue_num)
                                ->where('type','=',1)
                                ->first();
                            if (is_null($lotteryInfo)) {
                                $lotteryInfo = new Lottery();
                            }
                            $lotteryInfo->issue_num = $issue_num . '';
                            $lotteryInfo->save();
                        }
                    }
                }
            }
        }
        else{
            echo 'spider spiderNow error'.'</br>';
        }
    }

    /**
     * 通过html获取足彩数据(未开奖期数用)
     * @param $issueNum 期数id
     * @param $html 网页数据
     */
    private function spiderFourByHtml($issueNum, $html){
        $resultDivs = $html->find('table.socai');
        $lotteryInfo = Lottery::where('issue_num','=',$issueNum)
            ->where('type','=',1)
            ->first();
        if(is_null($lotteryInfo)){
            $lotteryInfo = new Lottery();
        }
        if($issueNum) {
            $lotteryInfo->issue_num = $issueNum;
        }
        //未开始的比赛是11-11 11:11的形式,所以要加年回去
        $tmpTime = str_replace('当期截止：','',strip_tags($html->find('#labState')[0]->plaintext));
        $currentYear = date_format(date_create()->setTimestamp(strtotime("now")), 'Y');
        //今年这个时候
        $bj = $currentYear.'-'.$tmpTime;
        //大于就是还在今年内
        if ($bj > date_format(date_create()->setTimestamp(strtotime("now")),'Y-m-d H:i')){
            $tmpTime = $bj;
        }
        else{
            //小于是下一年了
            $tmpTime = ($currentYear+1).'-'.$tmpTime;
        }
        if (strpos($tmpTime, '已返奖') !== false){

        }
        else{
            if (is_null($lotteryInfo->award_time))
                $lotteryInfo->award_time = $tmpTime;
        }
        $lotteryInfo->save();

        //爬每场比赛
        for ($index = 1 ; $index < 5; $index++)
        {
            $item = current($resultDivs)->find('#row_'.$index,0);
            //是否有结果
            $isEnd = count($item->find('.bred')) > 0 ? true : false;
            $i['num'] = $item->find('td')[0]->plaintext;
            $i['league'] = $item->find('td')[1]->plaintext;
            $lidString = $item->find('td')[1]->find('a')[0]->getAttribute('href');
            $lidString = explode('/',$lidString);
            //赛事别名
            $lid = explode('.',end($lidString))[0];
//            AliasController::bindLeagueIdWithName($lid,$i['league']);
            //未开始的比赛是11-11 11:11的形式,所以要加年回去
            $tmpTime = $item->find('td')[2]->plaintext;
            $currentYear = date_format(date_create()->setTimestamp(strtotime("now")), 'Y');
            //今年这个时候
            $bj = $currentYear.'-'.$tmpTime;
            if ($isEnd){
                //已开奖就是今年的
                if ($bj > date_format(date_create()->setTimestamp(strtotime("now")),'Y-m-d H:i')){
                    $tmpTime = ($currentYear-1).'-'.$tmpTime;
                }
                else{
                    $tmpTime = $bj;
                }
            }
            //大于就是还在今年内
            else if ($bj > date_format(date_create()->setTimestamp(strtotime("now")),'Y-m-d H:i')){
                $tmpTime = $bj;
            }
            else{
                //小于是下一年了
                $tmpTime = ($currentYear+1).'-'.$tmpTime;
            }
            $i['time'] = $tmpTime;
            $i['home'] = strip_tags($item->find('td')[3]->plaintext);
            $i['home'] = preg_replace('/\s/','',$i['home']);

            //获取比赛id
            $mid = explode('_',$item->find('td')[3]->find('a')[0]->getAttribute('id'))[1];
            //获取主队id
            $hidString = $item->find('td')[3]->find('a')[0]->getAttribute('onmouseover');
            $hidString = explode('.htm\',',$hidString)[0];
            $hid = explode('_',$hidString)[1];
//            AliasController::bindTeamIdWithName($hid,$i['home']);
//            LiaogouAlias::bindingLotteryTeam($hid,$i['home']);
            //获取客队id,结构问题,这个不在同一个tr
            $guestA = current($resultDivs)->find('#GuestTeam_'.$mid)[0];
            $i['guest'] = strip_tags($guestA->plaintext);
            $aidString = $guestA->getAttribute('onmouseover');
            $aidString = explode('.htm\',',$aidString)[0];
            $aid = explode('_',$aidString)[1];
//            AliasController::bindTeamIdWithName($aid,$i['guest']);
//            LiaogouAlias::bindingLotteryTeam($aid,$i['guest']);
            $guestTr = $guestA->parent()->parent();
            $match = Match::getMatchWith($mid,'win_id');
            //每场比赛
            $lottery = LotteryDetail::where('lottery_id','=',$lotteryInfo->id)
                ->where(function ($q)use($match,$i){
                    if (isset($match)){
                        $q->where('mid','=',$match->id);
                    }
                    else{
                        $q->where('league','=',$i['league'])
                            ->where('hname','=',$i['home'])
                            ->where('aname','=',$i['guest']);
                    }
                })
                ->first();
            if (is_null($lottery)){
                $lottery = new  LotteryDetail();
            }

            $lottery->lottery_id = $lotteryInfo->id;
            if (isset($match)) {
                $lottery->mid = $match->id;
                //更新竞彩数据到表
                $match->genre = $match->genre | 1 << 2;
                $match->lname = $i['league'];
                $match->save();
            }
//            dump($guestTr);
            if ($isEnd){
                //td倒数
                $totalTdCount = count($item->find('td'));
                $score = strip_tags($item->find('td')[$totalTdCount - 1 - 2]->plaintext);
                $lottery->hfscore = $score;
                $score = strip_tags($item->find('td')[$totalTdCount - 1 - 1]->plaintext);
                $lottery->hscore = $score;
                $lottery->result = strip_tags($item->find('td')[$totalTdCount - 1]->plaintext);

                $totalTdCount = count($guestTr->find('td'));
                $score = strip_tags($guestTr->find('td')[$totalTdCount - 1 - 2]->plaintext);
                $lottery->afscore = $score;
                $score = strip_tags($guestTr->find('td')[$totalTdCount - 1 - 1]->plaintext);
                $lottery->ascore = $score;
                $lottery->result_half = strip_tags($guestTr->find('td')[$totalTdCount - 1]->plaintext);
            }

            $lottery->aname = $i['guest'];
            $lottery->hname = $i['home'];
            $lottery->date = $i['time'];
            $lottery->league = $i['league'];
            $lottery->num = $i['num'];
            $lottery->save();
        }
        $lotteryInfo->fill_match = 1;
        $lotteryInfo->save();
    }

    //以下为6场
    /**
     * 爬历史数据框架,主要用于获取足彩期数对应id,结果等需要用这个id爬
     */
    public function spiderLotterySixHistoryFrame(){
        $curl = curl_init();
        //設置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, 'http://www.310win.com/zucai/4changjinqiucai/kaijiang_zc_3.html');
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
        $resultDivs = $html->find('#dropIssueNum');
        if (!is_null($resultDivs)){
            //遍历所有历史足彩数据
            $items = current($resultDivs)->find('option');
            foreach ($items as $item) {
                echo 'spider lotter '.$item->value.'</br>';
                $lotteryInfo = Lottery::where('win_id','=',$item->value)
                    ->where('type','=',2)
                    ->first();
                if (is_null($lotteryInfo))
                {
                    $lotteryInfo = Lottery::where('issue_num','=',strip_tags($item->plaintext))
                        ->where('type','=',2)
                        ->first();
                    $lotteryInfo = isset($lotteryInfo)?$lotteryInfo:new Lottery();
                    $lotteryInfo->win_id = $item->value;
                    $lotteryInfo->type = 2;
                    //更新了win_id,爬一次比赛结果
                    $lotteryInfo->fill_match = null;
                }
                $lotteryInfo->issue_num = strip_tags($item->plaintext);
                $lotteryInfo->save();
            }
        }
        else{
            echo 'spider lottery no data '.'</br>';
        }
    }

    /**
     * 根据彩票id爬数据,例如这期多少人中等
     */
    public function spiderLotterySixHistory(){
        $lotteryInfo = Lottery::whereNotNull('win_id')
            ->whereNull('result')
            ->where('type','=',2)
            ->orderBy('issue_num','desc')
            ->first();
        echo $lotteryInfo;
        if (isset($lotteryInfo))
            $this->spiderLotterySix($lotteryInfo->win_id);
        else
            echo 'spider lottery done';
    }

    /**
     * 根据彩票id爬历史期数数据
     * @param $volId 彩票id
     */
    private function spiderLotterySix($volId)
    {
        $str = $this->spiderTextFromUrl('http://www.310win.com/Info/Result/Soccer.aspx?load=ajax&typeID=3&IssueID='.$volId);
        $str = str_replace('	','',$str);
        $jm = json_decode($str,true);
        if (is_null($jm) || is_null($jm['Table']))
        {
            echo 'error volid '. $volId . '</br>';
            return;
        }

        echo 'spider lottery info '.$jm['IssueNum'].'</br>';

        //投注信息
        $lotteryInfo = Lottery::where('win_id','=',$volId)
            ->where('type','=',2)
            ->first();
        if (is_null($lotteryInfo))
        {
            $lotteryInfo = new Lottery();
            $lotteryInfo->win_id = $volId;
            $lotteryInfo->type = 2;
        }
        $lotteryInfo->issue_num = $jm['IssueNum'];
        $lotteryInfo->award_time = $jm['AwardTime'];
        $lotteryInfo->end_time = $jm['CashInStopTime'];
        $lotteryInfo->result = $jm['Result'];//str_replace('*','-',$jm['Result']);
        $lotteryInfo->detail = strip_tags($jm['Bottom']);
        $lotteryInfo->save();

        //几等奖数据
        foreach ( $jm['Bonus'] as $unit ) {
            $bonus = LotteryBonus::where('lid','=',$lotteryInfo->id)
                ->where('grade','=',$unit['Grade'])
                ->first();
            if (is_null($bonus)) {
                $bonus = new LotteryBonus();
                $bonus->lid = $lotteryInfo->id;
            }
            $bonus->grade = $unit['Grade'];
            $bonus->basic_stakes = $unit['BasicStakes'];
            $bonus->basic_bonus = strip_tags($unit['BasicBonus']);
            $bonus->save();
        }
    }

    public function lotterySixFillMatch(){
        $lottery = Lottery::whereNull('fill_match')
            ->where('type','=',2)
            ->orderby('issue_num','desc')
            ->first();
        if (isset($lottery)){
            $this->spiderSixNow($lottery->issue_num);
        }
    }

    /**
     * 爬最新数据
     * @param $issueNum 期数
     */
    public function spiderSixNow($issueNum){
        echo 'http://www.310win.com/buy/toto6.aspx?issueNum='.($issueNum>0?$issueNum:"").'</br>';
        $curl = curl_init();
        //設置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, 'http://www.310win.com/buy/toto6.aspx?issueNum='.($issueNum>0?$issueNum:""));
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
        $resultDivs = $html->find('table.socai');
        if (isset($resultDivs) && !empty($resultDivs)) {
            //爬info
            $this->spiderSixByHtml($issueNum>0?$issueNum:$html->find('.SelectedIssue')[0]->plaintext,$html);
            //爬其他
            echo 'issue '. $issueNum .'</br>';
            if ($issueNum == 0)
            {
                //如果是当前,没有issueNum,看看Issue有多少未来的其他继续爬
                $issue = $html->find('.Issue');
                if (isset($issue))
                {
                    for ($i = 0 ; $i < count($issue);$i++)
                    {
                        $issue_num = strip_tags($html->find('.Issue')[$i]->plaintext);
                        echo 'issue num '. $issue_num . '</br>';
                        if ($issue_num) {
                            $lotteryInfo = Lottery::where('issue_num', '=', $issue_num)
                                ->where('type','=',2)
                                ->first();
                            if (is_null($lotteryInfo)) {
                                $lotteryInfo = new Lottery();
                            }
                            $lotteryInfo->issue_num = $issue_num . '';
                            $lotteryInfo->save();
                        }
                    }
                }
            }
        }
        else{
            echo 'spider spiderNow error'.'</br>';
        }
    }

    /**
     * 通过html获取足彩数据(未开奖期数用)
     * @param $issueNum 期数id
     * @param $html 网页数据
     */
    private function spiderSixByHtml($issueNum, $html){
        $resultDivs = $html->find('table.socai');
        $lotteryInfo = Lottery::where('issue_num','=',$issueNum)
            ->where('type','=',2)
            ->first();
        if(is_null($lotteryInfo)){
            $lotteryInfo = new Lottery();
        }
        if($issueNum) {
            $lotteryInfo->issue_num = $issueNum;
        }
        //未开始的比赛是11-11 11:11的形式,所以要加年回去
        $tmpTime = str_replace('当期截止：','',strip_tags($html->find('#labState')[0]->plaintext));
        $currentYear = date_format(date_create()->setTimestamp(strtotime("now")), 'Y');
        //今年这个时候
        $bj = $currentYear.'-'.$tmpTime;
        //大于就是还在今年内
        if ($bj >= date_format(date_create()->setTimestamp(strtotime("now")),'Y-m-d H:i')){
            $tmpTime = $bj;
        }
        else{
            //小于是下一年了
            $tmpTime = ($currentYear+1).'-'.$tmpTime;
        }
        if (strpos($tmpTime, '已返奖') !== false){

        }
        else{
            if (is_null($lotteryInfo->award_time))
                $lotteryInfo->award_time = $tmpTime;
        }
        $lotteryInfo->save();

        //爬每场比赛
        for ($index = 1 ; $index < 7; $index++)
        {
            $item = current($resultDivs)->find('#row_'.$index,0);
            //是否有结果
            $isEnd = count($item->find('.bred')) > 0 ? true : false;
            $i['num'] = $item->find('td')[0]->plaintext;
            $i['league'] = $item->find('td')[1]->plaintext;
            $lidString = $item->find('td')[1]->find('a')[0]->getAttribute('href');
            $lidString = explode('/',$lidString);
            //赛事别名
            $lid = explode('.',end($lidString))[0];
//            AliasController::bindLeagueIdWithName($lid,$i['league']);
            //未开始的比赛是11-11 11:11的形式,所以要加年回去
            $tmpTime = $item->find('td')[2]->plaintext;
            $currentYear = date_format(date_create()->setTimestamp(strtotime("now")), 'Y');
            //今年这个时候
            $bj = $currentYear.'-'.$tmpTime;
            if (isset($lotteryInfo->award_time)){
                //已开奖就是今年的
                if ($bj > date_format(date_create()->setTimestamp(strtotime("now")),'Y-m-d H:i')){
                    $tmpTime = ($currentYear-1).'-'.$tmpTime;
                }
                else{
                    $tmpTime = $bj;
                }
            }
            //大于就是还在今年内
            else if ($bj > date_format(date_create()->setTimestamp(strtotime("now")),'Y-m-d H:i')){
                $tmpTime = $bj;
            }
            else{
                //小于是下一年了
                $tmpTime = ($currentYear+1).'-'.$tmpTime;
            }
            $i['time'] = $tmpTime;
            $i['home'] = strip_tags($item->find('td')[3]->plaintext);
            $i['home'] = preg_replace('/\s/','',$i['home']);
            $i['guest'] = strip_tags($item->find('td')[4]->plaintext);
            $i['guest'] = preg_replace('/\s/','',$i['guest']);

            //获取比赛id
            $mid = explode('_',$item->find('td')[3]->find('a')[0]->getAttribute('id'))[1];
            //获取主队id
            $hidString = $item->find('td')[3]->find('a')[0]->getAttribute('onmouseover');
            $hidString = explode('.htm\',',$hidString)[0];
            $hid = explode('_',$hidString)[1];
//            AliasController::bindTeamIdWithName($hid,$i['home']);
//            LiaogouAlias::bindingLotteryTeam($hid,$i['home']);
            //获取客队id
            $aidString = $item->find('td')[4]->find('a')[0]->getAttribute('onmouseover');
            $aidString = explode('.htm\',',$aidString)[0];
            $aid = explode('_',$aidString)[1];
//            AliasController::bindTeamIdWithName($aid,$i['guest']);
//            LiaogouAlias::bindingLotteryTeam($aid,$i['guest']);

            $match = Match::getMatchWith($mid,'win_id');
            //每场比赛
            $lottery = LotteryDetail::where('lottery_id','=',$lotteryInfo->id)
                ->where(function ($q)use($match,$i){
                    if (isset($match)){
                        $q->where('mid','=',$match->id);
                    }
                    else{
                        $q->where('league','=',$i['league'])
                            ->where('hname','=',$i['home'])
                            ->where('aname','=',$i['guest']);
                    }
                })
                ->first();
            if (is_null($lottery)){
                $lottery = new  LotteryDetail();
            }

            $lottery->lottery_id = $lotteryInfo->id;
            if (isset($match)) {
                $match->genre = $match->genre | 1 << 2;
                $match->lname = $i['league'];
                $lottery->mid = $match->id;
            }
//            dump($guestTr);

            if ($isEnd){
                //里面还有td,这里要倒数的td
                $totalTdCount = count($item->find('td'));
                $score = strip_tags($item->find('td')[$totalTdCount - 1 - 1]->plaintext);
                $lottery->hscore = strlen(explode("-", $score)[0])>0?explode("-", $score)[0]:0;
                $lottery->ascore = strlen(explode("-", $score)[1])>0?explode("-", $score)[1]:0;

                $hscore = strip_tags($item->find('td')[$totalTdCount - 1 - 2]->plaintext);
                $lottery->hfscore = strlen(explode("-", $hscore)[0])>0?explode("-", $hscore)[0]:0;
                $lottery->afscore = strlen(explode("-", $hscore)[1])>0?explode("-", $hscore)[1]:0;
                $results = strip_tags($item->find('td')[$totalTdCount - 1]->plaintext);
                $results = explode(' ',$results);
                if (count($results) == 2){
                    if (0 == strcmp('胜',$results[1]))
                    {
                        $lottery->result = 3;
                    }
                    else if (0 == strcmp('平',$results[1]))
                    {
                        $lottery->result = 1;
                    }
                    else if (0 == strcmp('负',$results[1]))
                    {
                        $lottery->result = 0;
                    }
                    else{
                        $lottery->result = '*';
                    }

                    if (0 == strcmp('胜',$results[0]))
                    {
                        $lottery->result_half = 3;
                    }
                    else if (0 == strcmp('平',$results[0]))
                    {
                        $lottery->result_half = 1;
                    }
                    else if (0 == strcmp('负',$results[0]))
                    {
                        $lottery->result_half = 0;
                    }
                    else{
                        $lottery->result_half = '*';
                    }
                }
            }

            $lottery->aname = $i['guest'];
            $lottery->hname = $i['home'];
            $lottery->date = $i['time'];
            $lottery->league = $i['league'];
            $lottery->num = $i['num'];
            $lottery->save();
        }
        $lotteryInfo->fill_match = 1;
        $lotteryInfo->save();
    }
}