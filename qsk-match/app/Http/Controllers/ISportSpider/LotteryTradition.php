<?php

namespace App\Http\Controllers\ISportSpider;


use App\Models\ISportModels\TraditionBonus;
use App\Models\ISportModels\TraditionFourteen;
use App\Models\ISportModels\TraditionFourteenIssue;
use Illuminate\Http\Request;

trait LotteryTradition{

    public function spiderTraditionForLastThree(Request $request) {
        $type = $request->input("type", "wilo");
        $issueData = $this->getLastIssueData();

        $this->spiderTraditionBySingleIssue($issueData->issue_next, $type);
        $this->spiderTraditionBySingleIssue($issueData->id, $type);
        $this->spiderTraditionBySingleIssue($issueData->issue_last, $type);
        //还要爬取前一期的前一期
        $tempData = TraditionFourteenIssue::query()->where("id", $issueData->issue_last)->first();
        if (isset($tempData)) {
            $this->spiderTraditionBySingleIssue($tempData->issue_last, $type);
        }
    }

    //爬最新的3期
    private function spiderTraditionIssueForNext(Request $request) {
        $spiderCount = $request->input("spider_count", 1);
        $nextCount = $request->input("next_count", 3);

        $issue = $request->input("issue");
        if (!isset($issue)) {
            $issueData = $this->getLastIssueData(1);
            $issue = $issueData->id;
        }
        $this->spiderTraditionBySingleIssue($issue);

        echo "spiderTraditionIssueForNext: issue = $issue , spiderCount = $spiderCount <br>";

        if ($spiderCount < $nextCount) {
            $request->merge(["spider_count" => ($spiderCount + 1)]);
            $issueData = TraditionFourteenIssue::query()->find($issue);
            $request->merge(["issue" => $issueData->issue_next]);
            $this->spiderTraditionIssueForNext($request);
        }
    }

    //爬前3期开奖结果
    private function spiderTraditionIssueForLast(Request $request) {
        $last_count = $request->input("last_count", 3);

        $url = "http://i.sporttery.cn/wap/fb_lottery/fb_lottery_issue_info?type=14&f_callback=";
        $response = $this->spiderTextFromUrl($url);
        $jsonResult = json_decode($response);
        $count = 0;
        if (isset($jsonResult) && isset($jsonResult->result)) {
            $datas = $jsonResult->result;
            foreach ($datas as $data) {
                if ($count >= $last_count) break;
                if (isset($data->issue)) {
                    $issue = $data->issue;
                    $fourteenIssue = TraditionFourteenIssue::query()->where("id", $issue)->first();
                    if (!isset($fourteenIssue)){
                        $fourteenIssue = $this->saveFourteenIssue($issue);
                    }
                    if (isset($fourteenIssue)) {
                        $fourteenIssue->fourteen_result_url = self::isport_host_url . $data->url;
                        $this->spiderTraditionFourteenResultDetail($issue, $fourteenIssue);
                        $fourteenIssue->save();
                        $count++;
                    }
                }
            }
        }

        $url = "http://i.sporttery.cn/wap/fb_lottery/fb_lottery_issue_info?type=9&f_callback=";
        $response = $this->spiderTextFromUrl($url);
        $jsonResult = json_decode($response);
        $count = 0;
        if (isset($jsonResult) && isset($jsonResult->result)) {
            $datas = $jsonResult->result;
            foreach ($datas as $data) {
                if ($count >= $last_count) return;
                if (isset($data->issue)) {
                    $fourteenIssue = TraditionFourteenIssue::query()->where("id", $data->issue)->first();
                    if (isset($fourteenIssue)) {
                        $fourteenIssue->nine_result_url = self::isport_host_url . $data->url;
                        $fourteenIssue->save();
                        $count++;
                    }
                }
            }
        }
    }

    private function spiderFourteenResultDetail(Request $request) {
        $issue = $request->input("issue");
        $this->spiderTraditionFourteenResultDetail($issue);
    }

    private function spiderTraditionFourteenResultDetail($issue, $fourteenIssue = null) {
        $url = "http://www.lottery.gov.cn/api/lottery_kj_detail.jspx?_ltype=9&_term=$issue";
        $response = $this->spiderTextFromUrl($url);
        $jsonResult = json_decode($response);
        if (isset($jsonResult) && isset($jsonResult[0]) && isset($jsonResult[0]->lottery)) {
            $lottery = $jsonResult[0]->lottery;
            if (is_null($fourteenIssue)) {
                $fourteenIssue = TraditionFourteenIssue::query()->where("id", $issue)->first();
            }
            if (isset($fourteenIssue)) {
                $fourteenIssue->result = str_replace(" ", "", $this->getItem($lottery, "number", true, ""));
                //已经有prize_time了，不再需要open_time
//                $open_time = $this->getItem($lottery, "openTime", true);
//                if (isset($open_time)) {
//                    $open_time = $this->getItem($open_time, "time", true);
//                    if ($open_time > 0) {
//                        $fourteenIssue->open_time = date("Y-m-d H:i:s", $open_time / 1000);
//                    }
//                }
                $fourteenIssue->fourteen_total_sales = $this->getItem($lottery, "totalSales", true);
                $fourteenIssue->nine_total_sales = $this->getItem($lottery, "totalSales2", true);
                $fourteenIssue->save();

                echo "spiderTraditionFourteenResultDetail: issue = $issue, has save TraditionFourteenIssue result<br>";
            }

            //奖金信息
            if (isset($jsonResult[0]->details)) {
                $details = $jsonResult[0]->details;
                foreach ($details as $detail) {
                    $num = $this->getItem($detail, "num", true);
                    if (in_array($num, [901, 902, 903])) {
                        $bonus = TraditionBonus::query()->where("issue", $issue)->where("type", $num)->first();
                        if (!isset($bonus)) {
                            $bonus = new TraditionBonus();
                            $bonus->issue = $issue;
                            $bonus->type = $num;
                        }
                        $bonus->all_money = $this->getItem($detail, "allmoney", true);
                        $bonus->level = $this->getItem($detail, "level", true);
                        $bonus->money = $this->getItem($detail, "money", true);
                        $bonus->piece = $this->getItem($detail, "piece", true);
                        $bonus->save();
                    }
                }
                echo "spiderTraditionFourteenResultDetail: issue = $issue, has save TraditionBonus result<br>";
            }

            //赛果
            if (isset($jsonResult[0]->matchResults)) {
                $matchResults = $jsonResult[0]->matchResults;
                $order_num = 1;
                foreach ($matchResults as $result) {
                    $traditionMatch = TraditionFourteen::query()->where("issue", $issue)
                        ->where("show", 1)->where("order_num", $order_num)->first();
                    if (isset($traditionMatch)) {
                        $traditionMatch->result = $this->getItem($result, "results", true, $traditionMatch->result);
                        $traditionMatch->save();
                    }
                    $order_num++;
                }
                echo "spiderTraditionFourteenResultDetail: issue = $issue, has save TraditionFourteen result<br>";
            }
        }
    }

    private function spiderTraditionByIssue(Request $request) {
        $issueData = $this->getLastIssueData();
        $issue = $request->input("issue", $issueData->issue);
        $type = $request->input("type", "wilo");
        $this->spiderTraditionBySingleIssue($issue, $type);
    }

    //获取截止购买前的最近一期
    private function getLastIssueData($offset = 0) {
        $issueData = TraditionFourteenIssue::query()->where("end_time", ">", date_create())->orderBy("end_time", "asc")->offset($offset)->first();
        if (!isset($issueData)) {
            $issueData = TraditionFourteenIssue::query()->orderBy("end_time", "desc")->first();
        }
        return $issueData;
    }

    //根据期数爬取任九数据
    private function spiderTraditionBySingleIssue($issue, $type = "wilo")
    {
        //保存期数信息
        $this->saveFourteenIssue($issue);

        $url = "http://i.sporttery.cn/wap/fb_lottery/fb_lottery_match?key=$type&num=$issue&f_callback=";

        $response = $this->spiderTextFromUrl($url);
        $jsonResult = json_decode($response);
        if (isset($jsonResult) && isset($jsonResult->result)) {
            $datas = $jsonResult->result;
            $orderNum = 1;
            //先把原先的数据隐藏
            $this->hideFourteenMatch($issue);
            foreach ($datas as $key=>$match){
                $i_id = null;
                if (isset($match->mid)) {
                    $i_id = $match->mid;
                }
                $this->saveFourteenMatch($match, $issue, $i_id, $orderNum);
                $orderNum++;
            }
        }
    }

    //保存任九比赛期数信息
    private function saveFourteenIssue($issueNum) {
        $url = "http://i.sporttery.cn/wap/fb_lottery/fb_lottery_nums?key=wilo&num=$issueNum&f_callback=";

        $response = $this->spiderTextFromUrl($url);
        $jsonResult = json_decode($response);

        $issueData = null;

        if (isset($jsonResult) && isset($jsonResult->result)) {
            $data = $jsonResult->result;
            $issueData = TraditionFourteenIssue::query()->find($issueNum);
            if (!isset($issueData)) {
                $issueData = new TraditionFourteenIssue();
                $issueData->id = $issueNum;
            }

            $prizeTime = $this->getItem($data, "prize", true);
            if (isset($prizeTime)) {
                $issueData->prize_time = str_replace("/", "-", $prizeTime);
            }
            $startTime = $this->getItem($data, "start", true);
            if (isset($startTime)) {
                $issueData->start_time = str_replace("/", "-", $startTime);
            }
            $endTime = $this->getItem($data, "end", true);
            if (isset($endTime)) {
                $issueData->end_time = str_replace("/", "-", $endTime);
            }

            $issueData->issue_last = $this->getItem($data, "last", true, $issueData['issue_last']);
            $issueData->issue_next = $this->getItem($data, "next", true, $issueData['issue_next']);

            $issueData->save();
        }
        return $issueData;
    }

    //保存单场任九比赛
    private function saveFourteenMatch($match, $issue, $i_id, $orderNum) {
        if (is_null($i_id)) {
            $fourteen = TraditionFourteen::query()->where("issue", $issue)
                ->where("hname", $match->h_cn)->where("aname", $match->a_cn)->first();
        } else {
            $fourteen = TraditionFourteen::query()->where("issue", $issue)
                ->where(function ($q) use ($i_id, $match) {
                    $q->where("i_id", $i_id)
                        ->orWhere(function ($q) use($match) {
                           $q->where("hname", $match->h_cn)->where("aname", $match->a_cn);
                        });
                })->first();
        }
        if (!isset($fourteen)) {
            $fourteen = new TraditionFourteen();
            $fourteen->issue = $issue;
        }
        $fourteen->i_id = $i_id;
        $fourteen->show = 1;
        $fourteen->order_num = $orderNum;
        $fourteen->lname = $this->getItem($match, "league", true, $fourteen['lname']);
        $fourteen->hname = $this->getItem($match, "h_cn", true, $fourteen['hname']);
        $fourteen->aname = $this->getItem($match, "a_cn", true, $fourteen['aname']);

        $dateStr = $this->getItem($match, 'date', true);
        if (is_null($dateStr)) {
            $time = $fourteen['time'];
        } else {
            $time = $this->getItem($match, 'date', true).' '.$this->getItem($match, 'time', true);
        }
        $fourteen->time = $time;

        $result = $this->getItem($match, "result", true, $fourteen['result']);
        if (is_numeric($result)) {
            $fourteen->result = $result;
        }
        $fourteen->prize_str = $this->getItem($match, "prize_str", true, $fourteen['prize_str']);

        $h_odd = $this->getItem($match, "h", true, $fourteen['h_odd']);
        $a_odd = $this->getItem($match, "a", true, $fourteen['a_odd']);
        $d_odd = $this->getItem($match, "d", true, $fourteen['d_odd']);
        if (is_numeric($h_odd)) {
            $fourteen->h_odd = $h_odd;
        }
        if (is_numeric($a_odd)) {
            $fourteen->a_odd = $a_odd;
        }
        if (is_numeric($d_odd)) {
            $fourteen->d_odd = $d_odd;
        }

        //比分
        $score = $this->getItem($match, 'full', true);
        if (isset($score)) {
            $scores = explode(":", $score);
            if (count($scores) == 2) {
                $fourteen->hscore = $scores[0];
                $fourteen->ascore = $scores[1];
            }
        }

        $fourteen->save();

        echo "saveFourteenMatch: issue = $issue , i_id = $i_id <br>";
    }

    /**
     * 根据传入的isport id 把该期数其余的比赛设置成隐藏状态
     * @param string $issue
     */
    private function hideFourteenMatch($issue) {
        $fourteens = TraditionFourteen::query()->where("issue", $issue)->get();
        foreach ($fourteens as $fourteen) {
            $fourteen->show = 0;
            $fourteen->save();
            echo "hideFourteenMatch: issue = $issue , i_id = $fourteen->i_id <br>";
        }
    }
}