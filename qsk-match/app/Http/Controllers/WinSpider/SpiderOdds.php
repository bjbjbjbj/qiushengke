<?php
/**
 * 爬赔率数据
 * Created by PhpStorm.
 * User: BJ
 * Date: 17/2/16
 * Time: 下午3:20
 */
namespace App\Http\Controllers\WinSpider;

use App\Http\Controllers\FileTool;
use App\Http\Controllers\Statistic\Change\OddChangeController;
use App\Http\Controllers\Statistic\StatisticFileTool;
use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\MatchesAfter;
use App\Models\LiaoGouModels\MatchLive;
use App\Models\LiaoGouModels\OddsAfter;
use App\Models\WinModels\Banker;
use App\Models\WinModels\Odd;
use App\Models\WinModels\OddDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait SpiderOdds
{

    /**
     * 每天的盘口与水位列表
     * @param int $type 类型 1 亚盘 2 大小球 3欧赔 4角球(不是这里爬,在比赛详情)
     * @param string $date
     */
    private function handicapDays($type = 1, $date = '')
    {
        $url = "http://txt.win007.com/phone/odds.aspx?date=$date&type=0&odds=$type&companyid=1,3,4,8,9,14,23";
        $str = $this->spiderTextFromUrl($url);
        if ($str) {
            $ss = explode("$$", $str);
            if (count($ss) == 3) {
                $asians = explode("!", $ss[2]);
                $lg_odds = array();
                $tempKeyArray = array();
                $oddArray = array();
                $win_mids = array();
                foreach ($asians as $asian) {
                    if (count(explode("^", $asian)) >= 9) {
                        switch ($type) {
                            case 1 : {//亚盘
                                list(
                                    $mid, $cid, $oid, $middle1, $up1,
                                    $down1, $middle2, $up2, $down2) = explode("^", $asian);
                                break;
                            }
                            case 2 : {//大小球
                                list($mid, $cid, $oid, $middle1, $up1, $down1, $middle2, $up2, $down2) = explode("^", $asian);
                                break;
                            }
                            case 3 : {//欧赔
                                list($mid, $cid, $oid, $up1, $middle1, $down1, $up2, $middle2, $down2) = explode("^", $asian);
                                break;
                            }
                            default:
                                return;
                        }

                        if (!array_key_exists($cid, OddChangeController::win_cid_convert_array)) continue;

                        if (array_key_exists($mid, $win_mids)) {
                            $lg_match = $win_mids[$mid];
                        } else {
                            $lg_match = Match::getMatchWith($mid, 'win_id');
                            $win_mids[$mid] = $lg_match;
                        }
                        if (!isset($lg_match)) continue;

                        $lg_mid = $lg_match->id;
                        $lg_cid = OddChangeController::win_cid_convert_array[$cid]['id'];

                        $tempKeyArray[] = "(mid=".$lg_mid." and cid=".$lg_cid." and type=".$type.")";
                        $oddArray[$lg_mid."_".$lg_cid."_".$type] = ['match'=>$lg_match, 'cid'=>$lg_cid,'type'=>$type,
                            'up1'=>$up1, 'middle1'=>$middle1, 'down1'=>$down1, 'up2'=>$up2, 'middle2'=>$middle2, 'down2'=>$down2];
//                        $this->sortOddData($mid, $cid, $type, $up1, $up2, $middle1, $middle2, $down1, $down2);

                        //静态化数据相关
//                        if (!isset($lg_odds[$cid])) {
//                            $lg_odds[$cid] = array();
//                        }
//                        $lg_odds[$lg_mid][$cid] = OddChangeController::onFootballOddItemConvert($up1, $middle1, $down1, $up2, $middle2, $down2);
                    }
//                    echo "$asian<br>";
                }
                //静态化盘口变化的信息
//                OddChangeController::footballOddItemDays($lg_odds, $type);

                $this->onLgOddsUpdate($tempKeyArray, $oddArray);
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
                $url = "http://txt.win007.com/phone/txt/asianchange.txt";
                break;
            }
            case 2 : {//大小球
                $url = "http://txt.win007.com/phone/txt/ouchange.txt";
                break;
            }
            case 3 : {//欧赔
                $url = "http://txt.win007.com/phone/txt/eurochange.txt";
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
                $roll = StatisticFileTool::getFileFromChange(MatchLive::kSportFootball, "roll");
                $tempKeyArray = array();
                $oddArray = array();
                $win_mids = array();
                foreach ($ss as $s) {
                    if (count(explode("^", $s)) >= 5) {
                        switch ($type) {
                            case 1 : {//亚盘
                                list($mid, $cid, $middle2, $up2, $down2) = explode("^", $s);
                                break;
                            }
                            case 2 : {//大小球
                                list($mid, $cid, $middle2, $up2, $down2) = explode("^", $s);
                                break;
                            }
                            case 3 : {//欧赔
                                list($mid, $cid, $up2, $middle2, $down2) = explode("^", $s);
                                break;
                            }
                            default : {
                                return;
                            }
                        }

                        if (!array_key_exists($cid, OddChangeController::win_cid_convert_array)) continue;

                        if (array_key_exists($mid, $win_mids)) {
                            $lg_match = $win_mids[$mid];
                        } else {
                            $lg_match = Match::getMatchWith($mid, 'win_id');
                            $win_mids[$mid] = $lg_match;
                        }
                        if (!isset($lg_match)) continue;

                        $lg_mid = $lg_match->id;
                        $lg_cid = OddChangeController::win_cid_convert_array[$cid]['id'];

                        $tempKeyArray[] = "(mid=".$lg_mid." and cid=".$lg_cid." and type=".$type.")";
                        $oddArray[$lg_mid."_".$lg_cid."_".$type] = ['match'=>$lg_match, 'cid'=>$lg_cid,'type'=>$type,
                            'up1'=>null, 'middle1'=>null, 'down1'=>null, 'up2'=>$up2, 'middle2'=>$middle2, 'down2'=>$down2];
//                        $this->sortOddData($mid, $cid, $type, null, $up2, null, $middle2, null, $down2);

//                        echo "$s<br>";
                        //静态数据相关
                        $roll = OddChangeController::footballOddItemChange($type, $lg_mid, $lg_cid, $middle2, $up2, $down2, $roll);
                    }
                }
                StatisticFileTool::putFileToLiveChange($roll, MatchLive::kSportFootball, 'roll');

//                dump($tempKeyArray, $oddArray);
                $this->onLgOddsUpdate($tempKeyArray, $oddArray);
            }
        }
    }

    /**
     * 盘口变化与水位走势(暂时没用到?)
     * @param $mid 比赛ID
     * @param $cid 博彩公司ID
     * @param int $type 参考handicapDays
     */
    private function handicapChangeDetail($mid, $cid, $type = 1,$time)
    {
        switch ($type) {
            case 1 : {//亚盘
                $url = "http://ios.win007.com/Phone/AsianDetail.aspx?CompanyID=$cid&ScheID=$mid";
                break;
            }
            case 2 : {//大小球
                $url = "http://ios.win007.com/Phone/OuDetail.aspx?CompanyID=$cid&ScheID=$mid";
                break;
            }
            case 3 : {//欧赔
                $url = "http://ios.win007.com/Phone/1x2EuroDetail.aspx?CompanyID=$cid&ScheID=$mid";
                break;
            }
            default : {
                return;
            }
        }
        $str = $this->spiderTextFromUrl($url);
        if ($str) {
            $o = OddDetail::where(["mid" => $mid, "cid" => $cid, "type" => $type])->first();
            if (!isset($o)) {
                $o = new OddDetail();
                $o->mid = $mid;
                $o->cid = $cid;
                $o->type = $type;
                $o->time = $time;
            }
            $o->detail = $str;
            $o->save();
            \App\Models\LiaoGouModels\OddDetail::saveDataWithWinData($o);
//            echo "$o<br>";
        }
    }

    /**
     * 根据比赛爬赔率数据
     * @param int $mid 比赛id
     * @param int $type 参考参考handicapDays
     */
    private function oddsWithMatchAndType($mid, $type)
    {
        switch ($type) {
            case 1 : {//亚盘
                $url = "http://ios.win007.com/phone/Handicap.aspx?ID=$mid&lang=0";
                break;
            }
            case 2 : {//大小球
                $url = "http://ios.win007.com/phone/OverUnder.aspx?ID=$mid&lang=0";
                break;
            }
            case 3 : {//欧赔
                $url = "http://ios.win007.com/phone/1x2.aspx?ID=$mid&lang=0";
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
                $lg_match = Match::getMatchWith($mid, 'win_id');
                if (!isset($lg_match)) return;

                $lg_mid = $lg_match->id;
                $tempKeyArray = array();
                $oddArray = array();
                foreach ($ss as $s) {
                    if(count(explode("^", $s)) >= 8) {
//                        echo "$s<br>";
                        switch ($type) {
                            case 1 : {//亚盘
                                list($name, $oid, $up1, $middle1, $down1,
                                    $up2, $middle2, $down2) = explode("^", $s);
                                break;
                            }
                            case 2 : {//大小球
                                list($name, $oid, $up1, $middle1, $down1, $up2, $middle2, $down2) = explode("^", $s);
                                break;
                            }
                            case 3 : {//欧赔
                                list($name, $oid, $up1, $middle1, $down1, $up2, $middle2, $down2) = explode("^", $s);
                                break;
                            }
                            default : {
                                return;
                            }
                        }
//                        $name = str_replace('澳门', '澳彩', $name);
                        $name = str_replace('SB', 'Crown', $name);
                        $name = str_replace('ＳＢ', 'Crown', $name);
                        $name = str_replace('12BET', '12bet', $name);
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
                }
                $this->onLgOddsUpdate($tempKeyArray, $oddArray);
            }
        }
    }

    private function sortOddData($odd=null,$mid, $cid, $type, $up1, $up2, $middle1, $middle2, $down1, $down2, $isOddHalf=false){
        $odd = Odd::updateCache($odd,$mid,$cid,$type,$up1,$up2,$middle1,$middle2,$down1,$down2,$isOddHalf);
    }

    private function onLgOddsUpdate($tempKeyArray, $oddArray) {
        //如果无数据，则不执行下面的部分
        if (count($tempKeyArray) <= 0) return;

        $tempQueryStr = implode(" or", $tempKeyArray);
        $odds = \App\Models\LiaoGouModels\Odd::query()->whereRaw("$tempQueryStr")->get();

        foreach ($odds as $odd) {
            $key = $odd->mid."_".$odd->cid."_".$odd->type;
            $changeOdd = $oddArray[$key];

            \App\Models\LiaoGouModels\Odd::updateLgOdd($odd,$changeOdd['match'],$changeOdd['cid'],$changeOdd['type'],
                $changeOdd['up1'],$changeOdd['up2'],$changeOdd['middle1'],$changeOdd['middle2'],$changeOdd['down1'],$changeOdd['down2']);

            $oddArray[$key] = null;
        }
        foreach ($oddArray as $changeOdd) {
            if (isset($changeOdd)) {
                \App\Models\LiaoGouModels\Odd::updateLgOdd(null,$changeOdd['match'],$changeOdd['cid'],$changeOdd['type'],
                    $changeOdd['up1'],$changeOdd['up2'],$changeOdd['middle1'],$changeOdd['middle2'],$changeOdd['down1'],$changeOdd['down2']);
            }
        }
    }

    /**
     * SB半场实时盘口与水位
     */
    private function halfHandicapLiveChange(){
        set_time_limit(0);

        $url = "http://live.titan007.com/vbsxml/sbOddsData.js?r=007".time()*1000;

        $content = $this->spiderTextFromUrlByWin007($url);

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
                        $lg_match = Match::getMatchWith($mid, 'win_id');
                        $win_mids[$mid] = $lg_match;
                    }
                    if (!isset($lg_match)) continue;

                    $lg_mid = $lg_match->id;
                    $lg_cid = OddChangeController::win_cid_convert_array[$cid]['id'];

                    $oddList = explode("],[", substr($odds, 2, strlen($odds) - 4));
                    //半场 亚盘 type = 11
//                    echo $oddList[3]."<br>";
                    list($up1, $middle1, $down1, $up2, $middle2, $down2, $other) = explode(",", $oddList[3], 7);
                    $type = 11;
                    $tempArray[$type]['keys'][] = "(mid=".$lg_mid." and cid=".$lg_cid." and type=".$type.")";
                    $tempArray[$type]['odds'][$lg_mid."_".$lg_cid."_".$type] = ['match'=>$lg_match, 'cid'=>$lg_cid,'type'=>$type,
                        'up1'=>$up1, 'middle1'=>$middle1, 'down1'=>$down1, 'up2'=>$up2, 'middle2'=>$middle2, 'down2'=>$down2];
//                    $this->sortOddData($mid, Odd::default_banker_id, 11, $up1, $up2, $middle1, $middle2, $down1, $down2, true);
                    //半场 大小球 type = 12
//                    echo $oddList[4]."<br>";
                    list($up1, $middle1, $down1, $up2, $middle2, $down2, $other) = explode(",", $oddList[4], 7);
                    $type = 12;
                    $tempArray[$type]['keys'][] = "(mid=".$lg_mid." and cid=".$lg_cid." and type=".$type.")";
                    $tempArray[$type]['odds'][$lg_mid."_".$lg_cid."_".$type] = ['match'=>$lg_match, 'cid'=>$lg_cid,'type'=>$type,
                        'up1'=>$up1, 'middle1'=>$middle1, 'down1'=>$down1, 'up2'=>$up2, 'middle2'=>$middle2, 'down2'=>$down2];
//                    $this->sortOddData($mid, Odd::default_banker_id, 12, $up1, $up2, $middle1, $middle2, $down1, $down2, true);
//                    dump($up1, $middle1, $down1, $up2, $middle2, $down2);
                    //半场 欧赔 type = 13
//                    echo $oddList[5]."<br>";
                    list($up1, $middle1, $down1, $up2, $middle2, $down2, $other) = explode(",", $oddList[5], 7);
                    $type = 13;
                    $tempArray[$type]['keys'][] = "(mid=".$lg_mid." and cid=".$lg_cid." and type=".$type.")";
                    $tempArray[$type]['odds'][$lg_mid."_".$lg_cid."_".$type] = ['match'=>$lg_match, 'cid'=>$lg_cid,'type'=>$type,
                        'up1'=>$up1, 'middle1'=>$middle1, 'down1'=>$down1, 'up2'=>$up2, 'middle2'=>$middle2, 'down2'=>$down2];
//                    $this->sortOddData($mid, Odd::default_banker_id, 13, $up1, $up2, $middle1, $middle2, $down1, $down2, true);
                }
            }
        }

        foreach ($tempArray as $type=>$data) {
            $this->onLgOddsUpdate($data['keys'], $data['odds']);
        }
    }

    /**
     * 删除oddAfter表中 不需要的信息
     */
    private function deleteUselessOddAfters(Request $request)
    {
        $count = 0;
        if ($request->has("count")) {
            $count = $request->get("count", 100);
        }
        OddsAfter::deleteUselessData($count);
//        OddsAfter::deleteUselessData2($count);
    }
}