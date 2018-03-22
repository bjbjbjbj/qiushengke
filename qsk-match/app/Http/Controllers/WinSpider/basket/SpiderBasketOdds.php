<?php
/**
 * 爬赔率数据
 * Created by PhpStorm.
 * User: ricky
 * Date: 17/9/6
 * Time: 19:03
 */
namespace App\Http\Controllers\WinSpider\basket;

use App\Http\Controllers\FileTool;
use App\Http\Controllers\Statistic\Change\OddChangeController;
use App\Http\Controllers\Statistic\StatisticFileTool;
use App\Models\LiaoGouModels\BasketMatchesAfter;
use App\Models\LiaoGouModels\MatchLive;
use App\Models\WinModels\Banker;
use App\Models\WinModels\BasketMatch;
use App\Models\WinModels\BasketOdd;
use App\Models\WinModels\Odd;
use Illuminate\Http\Request;

trait SpiderBasketOdds
{
    /**
     * 每天的盘口与水位列表
     * @param string $date
     */
    private function handicapDays($date = '',$cid = 3)
    {
        if (!array_key_exists($cid, OddChangeController::win_cid_convert_array)) return;

        $tempCid = BasketOdd::banker_convert_array[$cid];
        if (is_null($tempCid) || !is_numeric($tempCid)) {
            echo "handicapDays: cid = $cid is error <br>";
            return;
        }
        $url = "http://txt.win007.com/phone/lqodds.aspx?date=$date&type=0&companyid=$tempCid";
        $str = $this->spiderTextFromUrl($url);
        if ($str) {
            $ss = explode("$$", $str);
            if (count($ss) >= 2) {
                $asians = explode("!", $ss[1]);

                //静态化数据用到的公司信息
                $lg_banker = OddChangeController::win_cid_convert_array[$cid];
                $lg_cid = $lg_banker['id'];

                $temData = array();
                $win_mids = array();
                foreach ($asians as $asian) {
                    if (count(explode("^", $asian)) >= 24) {

                        list($mid, $lid, $dateStr, $hname, $aname,
                            $status, $hscore, $ascore, $asiaUp1, $asiaMiddle1,
                            $asiaDown1, $asiaUp2, $asiaMiddle2, $asiaDown2, $goalUp1,
                            $goalMiddle1, $goalDown1, $goalUp2, $goalMiddle2, $goalDown2,
                            $europeUp1, $europeDown1, $europeUp2, $europeDown2
                            ) = explode("^", $asian);

                        if (array_key_exists($mid, $win_mids)) {
                            $lg_match = $win_mids[$mid];
                        } else {
                            $lg_match = \App\Models\LiaoGouModels\BasketMatch::getMatchWith($mid, 'win_id');
                            $win_mids[$mid] = $lg_match;
                        }
                        if (!isset($lg_match)) continue;

                        $lg_mid = $lg_match->id;

                        $temData[1]['keys'][] = "(mid=".$lg_mid." and cid=".$lg_cid." and type=1)";
                        $temData[1]['odds'][$lg_mid."_".$lg_cid."_1"] = ['match'=>$lg_match, 'cid'=>$lg_cid,'type'=>1,
                            'up1'=>$asiaUp1, 'middle1'=>$asiaMiddle1, 'down1'=>$asiaDown1,
                            'up2'=>$asiaUp2, 'middle2'=>$asiaMiddle2, 'down2'=>$asiaDown2];

                        $temData[2]['keys'][] = "(mid=".$lg_mid." and cid=".$lg_cid." and type=2)";
                        $temData[2]['odds'][$lg_mid."_".$lg_cid."_2"] = ['match'=>$lg_match, 'cid'=>$lg_cid,'type'=>2,
                            'up1'=>$goalUp1, 'middle1'=>$goalMiddle1, 'down1'=>$goalDown1,
                            'up2'=>$goalUp2, 'middle2'=>$goalMiddle2, 'down2'=>$goalDown2];

                        $temData[3]['keys'][] = "(mid=".$lg_mid." and cid=".$lg_cid." and type=3)";
                        $temData[3]['odds'][$lg_mid."_".$lg_cid."_3"] = ['match'=>$lg_match, 'cid'=>$lg_cid,'type'=>3,
                            'up1'=>$europeUp1, 'middle1'=>null, 'down1'=>$europeDown1,
                            'up2'=>$europeUp2, 'middle2'=>null, 'down2'=>$europeDown2];

                        //静态化数据处理
//                        OddChangeController::basketOddItemDays($asian, $lg_banker, $lg_match->id);
                    }
//                    echo "$asian<br>";
                }
                foreach ($temData as $data) {
                    $this->onLgOddsUpdate($data['keys'], $data['odds']);
                }
            }
        }
    }

    /**
     * 盘口与水位变化
     * @param int $type 参考handicapDays
     */
    private function handicapChange($type = 1)
    {
        switch ($type) {
            case 1 : {//亚盘
                $url = "http://txt.win007.com/phone/txt/lqoddschange3.txt";
                break;
            }
            case 2 : {//大小球
                $url = "http://txt.win007.com/phone/txt/lqoddschange2.txt";
                break;
            }
            case 3 : {//欧赔
                $url = "http://txt.win007.com/phone/txt/lqoddschange1.txt";
                break;
            }
            default : {
                return;
            }
        }
        $str = $this->spiderTextFromUrl($url);

        $strs = explode("$$", $str);
        $roll = StatisticFileTool::getFileFromChange(MatchLive::kSportBasketball, "roll");

        foreach ($strs as $key=>$str) {
            $type = -1;
            switch ($key) {
                case 0:
                    $type = 1;
                    break;
                case 1:
                    $type = 2;
                    break;
                case 2:
                    $type = 3;
                    break;
            }
            if ($type > 0) {
                $roll = $this->spiderOddChangeDetail($str, $type, $roll);
            }
        }

        StatisticFileTool::putFileToLiveChange($roll, MatchLive::kSportBasketball, 'roll');
    }

    private function spiderOddChangeDetail($str, $type, $roll) {
        $ss = explode("!", $str);
        if (count($ss) > 0) {
            $tempKeyArray = array();
            $oddArray = array();
            $win_mids = array();
            $cid = 3; //默认是SB;
            foreach ($ss as $s) {
                $count = count(explode("^", $s));
                if ($type == 1 || $type == 2) {
                    if ($count >= 4) {
                        list($mid, $up2, $middle2, $down2) = explode("^", $s);
                    } else {
                        continue;
                    }
                } else if ($type == 3) {
                    if ($count >= 3) {
                        $middle2 = 0;
                        list($mid, $up2, $down2) = explode("^", $s);
                    } else {
                        continue;
                    }
                } else {
                    continue;
                }

                if (array_key_exists($mid, $win_mids)) {
                    $lg_match = $win_mids[$mid];
                } else {
                    $lg_match = \App\Models\LiaoGouModels\BasketMatch::getMatchWith($mid, 'win_id');
                    $win_mids[$mid] = $lg_match;
                }
                if (!isset($lg_match) || !array_key_exists($cid, OddChangeController::win_cid_convert_array)) continue;

                $lg_mid = $lg_match->id;
                $lg_cid = OddChangeController::win_cid_convert_array[$cid]['id'];

                if (isset($lg_match) && $lg_match->status == 0) {
                    $tempKeyArray[] = "(mid=".$lg_mid." and cid=".$lg_cid." and type=".$type.")";
                    $oddArray[$lg_mid."_".$lg_cid."_".$type] = ['match'=>$lg_match, 'cid'=>$lg_cid,'type'=>$type,
                        'up1'=>null, 'middle1'=>null, 'down1'=>null, 'up2'=>$up2, 'middle2'=>$middle2, 'down2'=>$down2];
//                        $this->sortOddData($mid, $cid, $type, null, $up2, null, $middle2, null, $down2);
//                    $this->sortOddData($mid, 3, $type, null, $up2, null, $middle2, null, $down2);
//                    echo "$s<br>";

                    $roll = OddChangeController::basketOddItemChange($type, $lg_mid, $lg_cid, $middle2, $up2, $down2, $roll);
                }
            }
            $this->onLgOddsUpdate($tempKeyArray, $oddArray);
        }
        return $roll;
    }

    /**
     * 根据比赛实时爬取未开赛的赔率数据
     */
    private function spiderOddsByMatches(Request $request)
    {
        $type = $request->input("type");
        $matches = BasketMatch::query()->where("status", 0)
            ->where("time", "<", date_create("+ 1days"))
            ->get();
        foreach ($matches as $match) {
            if (isset($type)) {
                $this->oddsWithMatchAndType($match->id, $type);
            } else {
                $this->oddsWithMatchAndType($match->id, 1);
                $this->oddsWithMatchAndType($match->id, 2);
            }
        }
    }

    /**
     * 根据比赛爬赔率数据
     */
    private function oddsWithMatchAndType($mid, $type)
    {
        switch ($type) {
            case 1 : {//亚盘
                $url = "http://ios.win007.com/phone/lqhandicap2.aspx?id=$mid";
                break;
            }
            case 2 : {//大小球
                $url = "http://ios.win007.com/phone/lqoverUnder.aspx?id=$mid";
                break;
            }
            case 3 : {//欧赔
                $url = "http://ios.win007.com/phone/lq1x2.aspx?id=$mid";
                break;
            }
            default : {
                return;
            }
        }
        $str = $this->spiderTextFromUrl($url);
        if ($str) {
            $ss = explode("!", $str);
            if (count($ss) > 0) {
                $lg_match = \App\Models\LiaoGouModels\BasketMatch::getMatchWith($mid, 'win_id');
                if (!isset($lg_match)) return;

                $lg_mid = $lg_match->id;
                $tempKeyArray = array();
                $oddArray = array();
                foreach ($ss as $s) {
                    $count = count(explode("^", $s));
//                        echo "$s<br>";
                    switch ($type) {
                        case 1 : {//亚盘
                            if ($count >= 8) {
                                list($name, $oid, $up1, $middle1, $down1,
                                    $up2, $middle2, $down2) = explode("^", $s);
                                break;
                            } else {
                                return;
                            }
                        }
                        case 2 : {//大小球
                            if ($count >= 8) {
                                list($name, $oid, $up1, $middle1, $down1, $up2, $middle2, $down2) = explode("^", $s);
                                break;
                            } else {
                                return;
                            }
                        }
                        case 3 : {//欧赔
                            if ($count >= 6) {
                                $middle1 = null;
                                $middle2 = null;
                                list($name, $oid, $up1, $down1, $up2, $down2) = explode("^", $s);
                                break;
                            } else {
                                return;
                            }
                        }
                        default : {
                            return;
                        }
                    }

                    $name = str_replace('澳门', '澳彩', $name);
//                        $name = str_replace('SB', 'ＳＢ', $name);
                    $name = str_replace('皇冠', 'ＳＢ', $name);
                    $name = str_replace('Crown', 'ＳＢ', $name);
                    $name = str_replace('12BET', '12bet/大发', $name);
                    $name = str_replace('利记sbobet', '利记', $name);
                    $name = str_replace('bet 365', 'Bet365', $name);

                    if ($up1 != '' && $up2 != '' && $middle1 != '' && $middle2 != '' && $down1 != '' && $down2 != '') {
                        $banker = Banker::where(["name" => $name])->first();
                        if (isset($banker)) {
                            $cid = $banker->id;
                            if (array_key_exists($cid, OddChangeController::win_cid_convert_array)) {
                                $lg_cid = OddChangeController::win_cid_convert_array[$cid]['id'];
                                $tempKeyArray[] = "(mid=" . $lg_mid . " and cid=" . $lg_cid . " and type=" . $type . ")";
                                $oddArray[$lg_mid . "_" . $lg_cid . "_" . $type] = ['match' => $lg_match, 'cid' => $lg_cid, 'type' => $type,
                                    'up1' => $up1, 'middle1' => $middle1, 'down1' => $down1, 'up2' => $up2, 'middle2' => $middle2, 'down2' => $down2];
//                                $this->sortOddData($mid, $cid, $type, $up1, $up2, $middle1, $middle2, $down1, $down2);
                            }
                        }
                    }
                }
                $this->onLgOddsUpdate($tempKeyArray, $oddArray);
            }
        }
    }

    private function onLgOddsUpdate($tempKeyArray, $oddArray) {
        //如果无数据，则不执行下面的部分
        if (count($tempKeyArray) <= 0) return;

        $tempQueryStr = implode(" or", $tempKeyArray);
        $odds = \App\Models\LiaoGouModels\BasketOdd::query()->whereRaw("$tempQueryStr")->get();

        foreach ($odds as $odd) {
            $key = $odd->mid."_".$odd->cid."_".$odd->type;
            $changeOdd = $oddArray[$key];

            \App\Models\LiaoGouModels\BasketOdd::updateLgOdd($odd,$changeOdd['match'],$changeOdd['cid'],$changeOdd['type'],
                $changeOdd['up1'],$changeOdd['up2'],$changeOdd['middle1'],$changeOdd['middle2'],$changeOdd['down1'],$changeOdd['down2']);

            $oddArray[$key] = null;
        }
        foreach ($oddArray as $changeOdd) {
            if (isset($changeOdd)) {
                \App\Models\LiaoGouModels\BasketOdd::updateLgOdd(null,$changeOdd['match'],$changeOdd['cid'],$changeOdd['type'],
                    $changeOdd['up1'],$changeOdd['up2'],$changeOdd['middle1'],$changeOdd['middle2'],$changeOdd['down1'],$changeOdd['down2']);
            }
        }
    }

    private function sortOddData($mid, $cid, $type, $up1, $up2, $middle1, $middle2, $down1, $down2)
    {
        $odd = BasketOdd::updateCache($mid, $cid, $type, $up1, $up2, $middle1, $middle2, $down1, $down2);
    }

    /**
     * SB全场（不需要半场）实时盘口与水位(只会取10:00 ~次日10:00的比赛)
     */
    private function handicapCurrentDay(){

        $url = "http://lq3.win007.com/NBA/sbOddsDataBsk.js?".time()*1000;

        echo "url = ".$url."</br>";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if (substr($url, 0, 5) == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.98 Safari/537.36");
//        curl_setopt($ch, CURLOPT_USERAGENT, "WSMobile/1.5.1 (iPad; iOS 10.2; Scale/2.00)");
        curl_setopt($ch, CURLOPT_REFERER, "http://lq3.win007.com/nba_big.htm");
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        $response = curl_exec($ch);
        if ($error = curl_error($ch)) {
            die($error);
        }
        curl_close($ch);

        list($head, $content) = explode("\r\n\r\n", $response, 2);
        $sDatas = explode(";", $content);
        echo "共有 ".count($sDatas)." 场比赛</br>";

        $tempArray = array();
        $win_mids = array();
        $cid = Odd::default_banker_id;
        if (!array_key_exists($cid, OddChangeController::win_cid_convert_array)) return;

        foreach ($sDatas as $data) {
            if (str_contains($data, "=")) {
                list($mid, $odds) = explode("=", $data, 2);
                $length = strlen($odds);
                if (preg_match("/(?<=\\[)(.*)(?=\\])/i", $mid, $temps) && $length > 4) {
                    $mid = $temps[0];

                    if (array_key_exists($mid, $win_mids)) {
                        $lg_match = $win_mids[$mid];
                    } else {
                        $lg_match = \App\Models\LiaoGouModels\BasketMatch::getMatchWith($mid, 'win_id');
                        $win_mids[$mid] = $lg_match;
                    }
                    if (!isset($lg_match) || !array_key_exists($cid, OddChangeController::win_cid_convert_array)) continue;

                    $lg_mid = $lg_match->id;
                    $lg_cid = OddChangeController::win_cid_convert_array[$cid]['id'];

                    $oddList = explode("],[", substr($odds, 2, strlen($odds) - 4));

                    //全场 亚盘 type = 1
                    list($up1, $middle1, $down1, $up2, $middle2, $down2) = explode(",", $oddList[0]);
                    $tempArray[1]['keys'][] = "(mid=".$lg_mid." and cid=".$lg_cid." and type=1)";
                    $tempArray[1]['odds'][$lg_mid."_".$lg_cid."_1"] = ['match'=>$lg_match, 'cid'=>$lg_cid,'type'=>1,
                        'up1'=>$up1, 'middle1'=>$middle1, 'down1'=>$down1, 'up2'=>$up2, 'middle2'=>$middle2, 'down2'=>$down2];
//                    $this->sortOddData($mid, BasketOdd::default_banker_id, 1, $up1, $up2, $middle1, $middle2, $down1, $down2);
                    //全场 大小球 type = 2
                    list($up1, $middle1, $down1, $up2, $middle2, $down2) = explode(",", $oddList[1]);
                    $tempArray[2]['keys'][] = "(mid=".$lg_mid." and cid=".$lg_cid." and type=2)";
                    $tempArray[2]['odds'][$lg_mid."_".$lg_cid."_2"] = ['match'=>$lg_match, 'cid'=>$lg_cid,'type'=>2,
                        'up1'=>$up1, 'middle1'=>$middle1, 'down1'=>$down1, 'up2'=>$up2, 'middle2'=>$middle2, 'down2'=>$down2];
//                    $this->sortOddData($mid, BasketOdd::default_banker_id, 2, $up1, $up2, $middle1, $middle2, $down1, $down2);
                }
            }
        }
        foreach ($tempArray as $type=>$data) {
            $this->onLgOddsUpdate($data['keys'], $data['odds']);
        }
    }

    /**
     * SB滚球盘盘口与水位
     */
    private function liveHandicapLiveChange(){

        $url = "http://lq3.win007.com/NBA/sbOddsDataBsk.js?".time()*1000;

        $content = $this->spiderTextFromUrlByWin007($url, false, "http://lq3.win007.com/nba.htm");
//        dump($content);
//        return;
        $sDatas = explode(";", $content);
        echo "共有 ".count($sDatas)." 场比赛</br>";
        $gunQiuArray = array();
        foreach ($sDatas as $data) {
            if (str_contains($data, "=")) {
                list($mid, $odds) = explode("=", $data, 2);
                $length = strlen($odds);
                if (preg_match("/(?<=\\[)(.*)(?=\\])/i", $mid, $temps) && $length > 4) {
                    $mid = $temps[0];
                    $oddList = explode("],[", substr($odds, 2, strlen($odds) - 4));
                    list($up1, $middle1, $down1, $up2, $middle2, $down2, $up, $middle, $down) = explode(",", $oddList[0], 9);
                    if (!isset($up) || strlen($up) <= 0) {
                        continue;
                    }
                    echo $mid.'<br>';
                    //全场
                    $array['all'][1] = ['up'=>$up, 'middle'=>$middle, 'down'=>$down]; //亚盘
                    list($up1, $middle1, $down1, $up2, $middle2, $down2, $up, $middle, $down) = explode(",", $oddList[1], 9);
                    $array['all'][2] = ['up'=>$up, 'middle'=>$middle, 'down'=>$down]; //大小球

                    //半场
                    list($up1, $middle1, $down1, $up2, $middle2, $down2, $up, $middle, $down) = explode(",", $oddList[2], 9);
                    $array['half'][1] = ['up'=>$up, 'middle'=>$middle, 'down'=>$down]; //亚盘
                    list($up1, $middle1, $down1, $up2, $middle2, $down2, $up, $middle, $down) = explode(",", $oddList[3], 9);
                    $array['half'][2] = ['up'=>$up, 'middle'=>$middle, 'down'=>$down]; //大小球

                    $tmpMatch = BasketMatchesAfter::where('win_id','=',$mid)->first();
                    if (isset($tmpMatch)) {
                        $l_mid = $tmpMatch['id'];
                        FileTool::putFileToLiveOdd($l_mid, $array,date('Ymd', strtotime($tmpMatch['time'])), FileTool::kBasketball);
                        $gunQiuArray[$l_mid] = $array;
                    }

                    //半场 亚盘 type = 11
//                    list($up1, $middle1, $down1, $up2, $middle2, $down2, $other) = explode(",", $oddList[3], 7);
                    //半场 大小球 type = 12
//                    list($up1, $middle1, $down1, $up2, $middle2, $down2, $other) = explode(",", $oddList[4], 7);
//                    dump($up1, $middle1, $down1, $up2, $middle2, $down2);
                    //半场 欧赔 type = 13
//                    list($up1, $middle1, $down1, $up2, $middle2, $down2, $other) = explode(",", $oddList[5], 7);
                }
            }
        }
        FileTool::putFileToTotalLiveOdd($gunQiuArray, FileTool::kBasketball);
    }
}