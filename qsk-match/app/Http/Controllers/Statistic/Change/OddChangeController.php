<?php
/**
 * Created by PhpStorm.
 * User: ricky
 * Date: 2018/2/26 0026
 * Time: 12:10
 */

namespace App\Http\Controllers\Statistic\Change;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Statistic\StatisticFileTool;
use App\Http\Controllers\Statistic\SpiderTools;
use App\Models\LiaoGouModels\BasketMatch;
use App\Models\LiaoGouModels\Match;
use App\Models\LiaoGouModels\MatchLive;
use App\Models\WinModels\BasketOdd;
use Illuminate\Support\Facades\Redis;

class OddChangeController extends Controller
{
    use SpiderTools;

    const win_cid_convert_array = [
        1=>['id'=>5, 'name'=>'澳门', 'rank'=>3],
        3=>['id'=>6, 'name'=>'Crown', 'rank'=>2],
        4=>['id'=>17, 'name'=>'立博', 'rank'=>7],
        8=>['id'=>7, 'name'=>'Bet365', 'rank'=>1],
        9=>['id'=>3, 'name'=>'威廉希尔', 'rank'=>10],
        12=>['id'=>8, 'name'=>'易胜博', 'rank'=>5],
        14=>['id'=>9, 'name'=>'韦德', 'rank'=>4],
        23=>['id'=>12, 'name'=>'金宝博', 'rank'=>6]
    ];

    function bankerRankSort($a, $b) {
        return $a['rank'] > $b['rank'] ? 1 : -1;
    }

    //盘口变化
    public function oddChangeStatistic($sport, $isDebug = true) {
        switch ($sport) {
            case MatchLive::kSportBasketball:
                $this->basketOddChange(1, $isDebug);
                $this->basketOddChange(2, $isDebug);
                $this->basketOddChange(3, $isDebug);
                break;
            case MatchLive::kSportFootball:
            default:
                $this->footballOddChange(1, $isDebug);
                $this->footballOddChange(2, $isDebug);
                $this->footballOddChange(3, $isDebug);
                break;
        }
    }

    //获取最近几天的盘口情况
    public function oddDaysChangeStatistic($sport, $isDebug = true) {
        set_time_limit(0);
        $lastTime = time();
        switch ($sport) {
            case MatchLive::kSportBasketball:
                foreach (range(0, 1) as $i) {
                    $dateStr = date_format(date_create("+ $i days"), 'Y-m-d');
                    $this->basketOddDays($dateStr, 3, $isDebug); //SB
                    $this->basketOddDays($dateStr, 8, $isDebug); //bet365
                    $this->basketOddDays($dateStr, 14, $isDebug); //韦德
                }
                break;
            case MatchLive::kSportFootball:
            default:
                foreach (range(0, 1) as $i) {
                    $dateStr = date_format(date_create("+ $i days"), 'Y-m-d');
                    $this->footballOddDays(1, $dateStr, $isDebug);
                    $this->footballOddDays(2, $dateStr, $isDebug);
                    $this->footballOddDays(3, $dateStr, $isDebug);
                }
                break;
        }
        dump(time() - $lastTime);
    }

    //获取实时的滚球盘数据
    public function rollListChangeStatic($sport, $isDebug = true) {
        switch ($sport) {
            case MatchLive::kSportBasketball:
                $this->basketRollListChange($isDebug);
                break;
            case MatchLive::kSportFootball:
            default:
                $this->footballRollListChange($isDebug);
                break;
        }
    }

    //获取比赛列表SB盘口变化的数据
    public function rollChangeStatic($sport, $isDebug = true) {
        $lastTime = time();
        foreach (range(0, 20) as $key) {
            switch ($sport) {
                case MatchLive::kSportBasketball:
                    $this->basketRollChange($isDebug);
                    break;
                case MatchLive::kSportFootball:
                default:
                    $this->footballRollChange($isDebug);
                    break;
            }
            sleep(1);
        }
        dump(time()-$lastTime);
    }

    /**
     * 盘口改变单项
     */
    public static function footballOddItemChange($type, $lg_mid, $lg_cid, $middle2, $up2, $down2, $roll = null, $isDebug = false) {
        if ($lg_mid <= 0) return $roll;

        if ($lg_cid == 2) {
            //只有当cid==2(SB)盘口改变的时候才保存到终端
            if (in_array($type, [1,2,3])) {
                $lg_match = StatisticFileTool::getFileFromTerminal(MatchLive::kSportFootball, $lg_mid, 'match');
                if (isset($lg_match)) {
                    if ($type == 3) {
                        $lg_match['ouup2'] = $up2;
                        $lg_match['oumiddle2'] = $middle2;
                        $lg_match['oudown2'] = $down2;
                    } else if ($type == 2) {
                        $lg_match['goalup2'] = $up2;
                        $lg_match['goalmiddle2'] = $middle2;
                        $lg_match['goaldown2'] = $down2;
                    } else {
                        $lg_match['asiaup2'] = $up2;
                        $lg_match['asiamiddle2'] = $middle2;
                        $lg_match['asiadown2'] = $down2;
                    }
                    StatisticFileTool::putFileToTerminal($lg_match, MatchLive::kSportFootball, $lg_mid, 'match');
                }

                //盘口改变
                $rollItem = isset($roll)&&isset($roll[$lg_mid]) ? $roll[$lg_mid] : array();
                $rollItem['all'][$type]['up2'] = $up2;
                $rollItem['all'][$type]['middle2'] = $middle2;
                $rollItem['all'][$type]['down2'] = $down2;

                $roll[$lg_mid] = $rollItem;
            }
        }

        //终端，盘口信息
        $lg_match_odd = StatisticFileTool::getFileFromTerminal(MatchLive::kSportFootball, $lg_mid, 'odd');
        if (isset($lg_match_odd)) {
            $lg_match_odd = is_array($lg_match_odd) ? $lg_match_odd : array();
            foreach ($lg_match_odd as $key=>$banker) {
                if ($lg_cid == $banker['id']) {
                    if ($type == 1 && isset($lg_match_odd['asia'])) {
                        $banker['asia']['up2'] = $up2;
                        $banker['asia']['middle2'] = $middle2;
                        $banker['asia']['down2'] = $down2;
                    } else if ($type == 2 && isset($lg_match_odd['goal'])) {
                        $banker['goal']['up2'] = $up2;
                        $banker['goal']['middle2'] = $middle2;
                        $banker['goal']['down2'] = $down2;
                    } else if ($type == 3 && isset($banker['ou'])) {
                        $banker['ou']['up2'] = $up2;
                        $banker['ou']['middle2'] = $middle2;
                        $banker['ou']['down2'] = $down2;
                    }
                    $lg_match_odd[$key] = $banker;
                }
            }
            StatisticFileTool::putFileToTerminal($lg_match_odd, MatchLive::kSportFootball, $lg_mid, 'odd');

            if ($isDebug) {
                echo "footballOddItemChange: lg_mid = $lg_mid; cid = $lg_cid; type =$type; <br>";
            }
        }

        return $roll;
    }

    /**
     * 足球 盘口与水位变化
     */
    private function footballOddChange($type = 1, $isDebug = false)
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
                $win_mids = array();
                foreach ($ss as $s) {
                    if (count(explode("^", $s)) >= 5) {
                        switch ($type) {
                            case 1 : //亚盘
                            case 2 : //大小球
                                list($mid, $cid, $middle2, $up2, $down2) = explode("^", $s);
                                break;
                            case 3 : //欧赔
                                list($mid, $cid, $up2, $middle2, $down2) = explode("^", $s);
                                break;
                            default :
                                return;
                        }
                        if (array_key_exists($mid, $win_mids)) {
                            $lg_mid = $win_mids[$mid];
                        } else {
                            $lg_mid = Match::getMatchIdWith($mid, 'win_id');
                            $win_mids[$mid] = $lg_mid;
                        }
                        if ($lg_mid <= 0 || !array_key_exists($cid, self::win_cid_convert_array)) continue;

                        $lg_cid = self::win_cid_convert_array[$cid]['id'];

                        $roll = self::footballOddItemChange($type, $lg_mid, $lg_cid, $middle2, $up2, $down2, $roll, $isDebug);
                    }
                }
                StatisticFileTool::putFileToLiveChange($roll, MatchLive::kSportFootball, 'roll');
            }
        }
    }

    /**
     * 每日盘口变化 单项处理
     * @param $lg_odds
     * @param $type
     */
    public static function footballOddItemDays($lg_odds, $type, $isDebug = false) {
        foreach ($lg_odds as $lg_mid=> $lg_odd) {

            if ($lg_mid <= 0) continue;

            $lg_match_odd = StatisticFileTool::getFileFromTerminal(MatchLive::kSportFootball, $lg_mid, 'odd');

            if (!is_array($lg_match_odd)) $lg_match_odd = array();

            foreach ($lg_odd as $cid=>$odd) {
                $lg_banker = self::win_cid_convert_array[$cid];
                if (!isset($lg_banker)) continue;

                if (isset($lg_match_odd[$lg_banker['rank']])){
                    $oddItem = $lg_match_odd[$lg_banker['rank']];
                } else {
                    $oddItem = $lg_banker;
                }
                if ($type == 1) {
                    $oddItem['asia'] = $odd;
                } else if ($type == 2) {
                    $oddItem['goal'] = $odd;
                } else if ($type == 3) {
                    $oddItem['ou'] = $odd;
                }
                $lg_match_odd[$lg_banker['rank']] = $oddItem;
            }

            StatisticFileTool::putFileToTerminal($lg_match_odd, MatchLive::kSportFootball, $lg_mid, 'odd');

            if ($isDebug) {
                echo "footballOddItemDays: lg_mid = $lg_mid <br>";
            }
        }
    }

    /**
     * 组合单个盘口信息
     */
    public static function onFootballOddItemConvert($up1, $middle1, $down1, $up2, $middle2, $down2) {
        $odd = array();
        $odd['up1'] = $up1;
        $odd['up2'] = $up2;
        $odd['middle1'] = $middle1;
        $odd['middle2'] = $middle2;
        $odd['down1'] = $down1;
        $odd['down2'] = $down2;

        return $odd;
    }

    /**
     * 足球 每天的盘口与水位列表
     */
    private function footballOddDays($type = 1, $date = '', $isDebug = false)
    {
        $url = "http://txt.win007.com/phone/odds.aspx?date=$date&type=0&odds=$type&companyid=1,3,4,8,9,14,23";
        $str = $this->spiderTextFromUrl($url);
        if ($str) {
            $ss = explode("$$", $str);
            if (count($ss) == 3) {
                $asians = explode("!", $ss[2]);
                $lg_odds = array();
                $win_mids = array();
                foreach ($asians as $asian) {
                    if (count(explode("^", $asian)) >= 9) {
                        switch ($type) {
                            case 1 : //亚盘
                            case 2 : //大小球
                                list($mid, $cid, $oid, $middle1, $up1, $down1, $middle2, $up2, $down2) = explode("^", $asian);
                                break;
                            case 3 : //欧赔
                                list($mid, $cid, $oid, $up1, $middle1, $down1, $up2, $middle2, $down2) = explode("^", $asian);
                                break;
                            default:
                                return;
                        }

                        if (array_key_exists($mid, $win_mids)) {
                            $lg_mid = $win_mids[$mid];
                        } else {
                            $lg_mid = Match::getMatchIdWith($mid, 'win_id');
                        }
                        if ($lg_mid <= 0 || !array_key_exists($cid, self::win_cid_convert_array)) continue;

                        $win_mids[$mid] = $lg_mid;

                        $lg_odds[$lg_mid][$cid] = self::onFootballOddItemConvert($up1, $middle1, $down1, $up2, $middle2, $down2);
                    }
                }
                dump(count($lg_odds));
                self::footballOddItemDays($lg_odds, $type, $isDebug);
            }
        }
    }

    /**
     * 足球 SB滚球盘盘口与水位
     */
    private function footballRollListChange($isDebug = false){

        $url = "http://live.titan007.com/vbsxml/sbOddsData.js?r=007".time()*1000;

        $content = $this->spiderTextFromUrlByWin007($url);

        $sDatas = explode(";", $content);
//        echo "共有 ".count($sDatas)." 场比赛</br>";
        $gunQiuArray = array();
        foreach ($sDatas as $data) {
            if (str_contains($data, "=")) {
                list($mid, $odds) = explode("=", $data, 2);
                $length = strlen($odds);
                if (preg_match("/(?<=\\[)(.*)(?=\\])/i", $mid, $temps) && $length > 4) {
                    $mid = $temps[0];
                    $lg_mid = Match::getMatchIdWith($mid, 'win_id');
                    if ($lg_mid <= 0) continue;

                    $oddList = explode("],[", substr($odds, 2, strlen($odds) - 4));

                    list($up1, $middle1, $down1, $up2, $middle2, $down2, $up, $middle, $down) = explode(",", $oddList[0], 9);
                    if (!isset($up) || strlen($up) <= 0) {
                        continue;
                    }
//                    echo $mid.'<br>';
                    //全场
                    $array['all'][1] = ['up1'=>$up1, 'middle1'=>$middle1, 'down1'=>$down1,
                        'up2'=>$up2, 'middle2'=>$middle2, 'down2'=>$down2,
                        'up'=>$up, 'middle'=>$middle, 'down'=>$down]; //亚盘
                    list($up1, $middle1, $down1, $up2, $middle2, $down2, $up, $middle, $down) = explode(",", $oddList[2], 9);
                    $array['all'][2] = ['up1'=>$up1, 'middle1'=>$middle1, 'down1'=>$down1,
                        'up2'=>$up2, 'middle2'=>$middle2, 'down2'=>$down2,
                        'up'=>$up, 'middle'=>$middle, 'down'=>$down];; //大小球
                    list($up1, $middle1, $down1, $up2, $middle2, $down2, $up, $middle, $down) = explode(",", $oddList[1], 9);
                    $array['all'][3] = ['up1'=>$up1, 'middle1'=>$middle1, 'down1'=>$down1,
                        'up2'=>$up2, 'middle2'=>$middle2, 'down2'=>$down2,
                        'up'=>$up, 'middle'=>$middle, 'down'=>$down];; //欧赔

                    //半场
                    list($up1, $middle1, $down1, $up2, $middle2, $down2, $up, $middle, $down) = explode(",", $oddList[3], 9);
                    $array['half'][1] = ['up1'=>$up1, 'middle1'=>$middle1, 'down1'=>$down1,
                        'up2'=>$up2, 'middle2'=>$middle2, 'down2'=>$down2,
                        'up'=>$up, 'middle'=>$middle, 'down'=>$down];; //亚盘
                    list($up1, $middle1, $down1, $up2, $middle2, $down2, $up, $middle, $down) = explode(",", $oddList[4], 9);
                    $array['half'][2] = ['up1'=>$up1, 'middle1'=>$middle1, 'down1'=>$down1,
                        'up2'=>$up2, 'middle2'=>$middle2, 'down2'=>$down2,
                        'up'=>$up, 'middle'=>$middle, 'down'=>$down];; //大小球
                    list($up1, $middle1, $down1, $up2, $middle2, $down2, $up, $middle, $down) = explode(",", $oddList[5], 9);
                    $array['half'][3] = ['up1'=>$up1, 'middle1'=>$middle1, 'down1'=>$down1,
                        'up2'=>$up2, 'middle2'=>$middle2, 'down2'=>$down2,
                        'up'=>$up, 'middle'=>$middle, 'down'=>$down];; //欧赔

                    StatisticFileTool::putFileToTerminal($array, MatchLive::kSportFootball, $lg_mid, 'roll');
//                    dump($lg_mid);
                    $gunQiuArray[$lg_mid] = $array;

                    if ($isDebug) {
                        echo "footballRollOddChange: lg_mid = $lg_mid <br>";
                    }
                }
            }
        }
        StatisticFileTool::putFileToLiveChange($gunQiuArray, MatchLive::kSportFootball, 'roll');
    }

    /**
     * 足球比赛列表，SB滚球盘 盘口与水位 实时变化
     */
    private function footballRollChange($isDebug = false) {
        $url = "http://live.titan007.com/vbsxml/ch_goalBf3.xml?r=007".time()*1000;

        $content = $this->spiderTextFromUrlByWin007($url);

        $key = "static_foot_roll_change";
        $data = Redis::get($key);
        if ($data == strlen($content)) {
            echo "there nothing change!<br>";
            return;
        } else {
            $data = strlen($content);
            Redis::set($key, $data);
        }

        preg_match_all("/<match>(.*?)<\\/match>/is", $content, $tempItems);
        if (isset($tempItems) && isset($tempItems[1]) && isset($tempItems[1][0])) {
            $tempStr = $tempItems[1][0];
            $tempStr = str_replace("<m>", "", $tempStr);
            $matches = explode("</m>", $tempStr);

            $rollData = StatisticFileTool::getFileFromChange(MatchLive::kSportFootball, 'roll');
            if (!is_array($rollData)) $rollData = array();
            foreach ($matches as $itemStr) {
                if (count(explode(",", $itemStr)) >= 13) {
                    list($mid, $aisaid, $asiamiddle, $asiaup, $asiadown,
                        $ouid, $ouup, $oumiddle, $oudown, $goalid,
                        $goalmiddle, $goalup, $goaldown, $other) = explode(",", $itemStr);
                    $lg_mid = Match::getMatchIdWith($mid, "win_id");
                    if ($lg_mid > 0) {
                        if (array_key_exists($lg_mid, $rollData)) {
                            $itemData = $rollData[$lg_mid];
                        } else {
                            $itemData = array();
                        }
                        $itemData['all'][1]['up'] = $asiaup;
                        $itemData['all'][1]['middle'] = $asiamiddle;
                        $itemData['all'][1]['down'] = $asiadown;
                        $itemData['all'][2]['up'] = $goalup;
                        $itemData['all'][2]['middle'] = $goalmiddle;
                        $itemData['all'][2]['down'] = $goaldown;
                        $itemData['all'][3]['up'] = $ouup;
                        $itemData['all'][3]['middle'] = $oumiddle;
                        $itemData['all'][3]['down'] = $oudown;

                        $rollData[$lg_mid] = $itemData;

                        StatisticFileTool::putFileToTerminal($itemData, MatchLive::kSportFootball,$lg_mid, 'roll');
                        if ($isDebug) {
                            echo "footballListOddChange: lg_mid = $lg_mid <br>";
                        }
                    }
                }
            }
            StatisticFileTool::putFileToLiveChange($rollData, MatchLive::kSportFootball, 'roll');
        }
    }

    //======================篮球相关=======================

    /**
     * 篮球盘口变化 单项处理
     */
    public static function basketOddItemChange($type, $lg_mid, $lg_cid, $middle2, $up2, $down2, $roll = null, $isDebug = false) {
        if ($lg_mid <= 0) return $roll;

        if (in_array($type, [1,2,3])) {
            $lg_match = StatisticFileTool::getFileFromTerminal(MatchLive::kSportBasketball, $lg_mid, 'match');
            if (isset($lg_match)) {
                if ($type == 3) {
                    $lg_match['ouup2'] = $up2;
                    $lg_match['oumiddle2'] = $middle2;
                    $lg_match['oudown2'] = $down2;
                }else if ($type == 2) {
                    $lg_match['goalup2'] = $up2;
                    $lg_match['goalmiddle2'] = $middle2;
                    $lg_match['goaldown2'] = $down2;
                } else {
                    $lg_match['asiaup2'] = $up2;
                    $lg_match['asiamiddle2'] = $middle2;
                    $lg_match['asiadown2'] = $down2;
                }
                StatisticFileTool::putFileToTerminal($lg_match, MatchLive::kSportBasketball, $lg_mid, 'match');
            }

            //盘口改变
            $rollItem = isset($roll)&&isset($roll[$lg_mid]) ? $roll[$lg_mid] : array();
            $rollItem['all'][$type]['up2'] = $up2;
            $rollItem['all'][$type]['middle2'] = $middle2;
            $rollItem['all'][$type]['down2'] = $down2;

            $roll[$lg_mid] = $rollItem;

            //终端，盘口信息
            $lg_match_odd = StatisticFileTool::getFileFromTerminal(MatchLive::kSportBasketball, $lg_mid, 'odd');
            if (isset($lg_match_odd) && is_array($lg_match_odd)) {
                foreach ($lg_match_odd as $key=>$banker) {
                    if ($lg_cid == $banker['id']) {
                        if ($type == 1 && isset($lg_match_odd['asia'])) {
                            $banker['asia']['up2'] = $up2;
                            $banker['asia']['middle2'] = $middle2;
                            $banker['asia']['down2'] = $down2;
                        } else if ($type == 2 && isset($lg_match_odd['goal'])) {
                            $banker['goal']['up2'] = $up2;
                            $banker['goal']['middle2'] = $middle2;
                            $banker['goal']['down2'] = $down2;
                        } else if ($type == 3 && isset($banker['ou'])) {
                            $banker['ou']['up2'] = $up2;
                            $banker['ou']['middle2'] = $middle2;
                            $banker['ou']['down2'] = $down2;
                        }
                        $lg_match_odd[$key] = $banker;
                    }
                }
                StatisticFileTool::putFileToTerminal($lg_match_odd, MatchLive::kSportBasketball, $lg_mid, 'odd');

                if ($isDebug) {
                    echo "basketOddItemChange: lg_mid = $lg_mid; cid = $lg_cid; type =$type; <br>";
                }
            }
        }

        return $roll;
    }

    /**
     * 篮球 盘口与水位变化
     */
    private function basketOddChange($type = 1, $isDebug = false)
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

        $cid = \App\Models\WinModels\Odd::default_banker_id;
        $strs = explode("$$", $str);
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
                $ss = explode("!", $str);
                if (count($ss) > 0) {
                    $roll = StatisticFileTool::getFileFromChange(MatchLive::kSportBasketball, "roll");
                    $win_mids = array();
                    foreach ($ss as $s) {
                        $count = count(explode("^", $s));
                        switch ($type) {
                            case 1 :
                            case 2 :
                                if ($count >= 4) {
                                    list($mid, $up2, $middle2, $down2) = explode("^", $s);
                                    break;
                                } else {
                                    return;
                                }
                            case 3 :
                                if ($count >= 3) {
                                    $middle2 = 0;
                                    list($mid, $up2, $down2) = explode("^", $s);
                                    break;
                                } else {
                                    return;
                                }
                            default :
                                return;
                        }
                        if (array_key_exists($mid, $win_mids)) {
                            $lg_match = $win_mids[$mid];
                        } else {
                            $lg_match = BasketMatch::getMatchWith($mid, 'win_id');
                            $win_mids[$mid] = $lg_match;
                        }
                        if (!isset($lg_match) || !array_key_exists($cid, self::win_cid_convert_array)) continue;

                        if ($lg_match->status == 0) {
                            $lg_mid = $lg_match->id;
                            $lg_cid = self::win_cid_convert_array[$cid]['id'];

                            $roll = self::basketOddItemChange($type, $lg_mid, $lg_cid, $middle2, $up2, $down2, $roll, $isDebug);
                        }
                    }
                    StatisticFileTool::putFileToLiveChange($roll, MatchLive::kSportBasketball, 'roll');
                }
            }
        }
    }

    /**
     * 篮球每日盘口改变 单项处理
     * @param $itemStr
     * @param $lg_banker
     */
    public static function basketOddItemDays($itemStr, $lg_banker, $lg_mid = 0, $isDebug = false) {
        if (!isset($lg_banker)) return;

        list($mid, $lid, $dateStr, $hname, $aname,
            $status, $hscore, $ascore, $asiaUp1, $asiaMiddle1,
            $asiaDown1, $asiaUp2, $asiaMiddle2, $asiaDown2, $goalUp1,
            $goalMiddle1, $goalDown1, $goalUp2, $goalMiddle2, $goalDown2,
            $europeUp1, $europeDown1, $europeUp2, $europeDown2
            ) = explode("^", $itemStr);

        if ($lg_mid <= 0) {
            $lg_mid = BasketMatch::getMatchIdWith($mid, "win_id");
        }
        if ($lg_mid <= 0) return;

        $lg_match_odd = StatisticFileTool::getFileFromTerminal(MatchLive::kSportBasketball, $lg_mid, 'odd');
        if (!is_array($lg_match_odd)) $lg_match_odd = array();

        $oddItem = $lg_banker;
        $oddItem['asia'] = array();
        $oddItem['goal'] = array();
        $oddItem['ou'] = array();
        $oddItem['asia']['up1'] = $asiaUp1;
        $oddItem['asia']['up2'] = $asiaUp2;
        $oddItem['asia']['middle1'] = $asiaMiddle1;
        $oddItem['asia']['middle2'] = $asiaMiddle2;
        $oddItem['asia']['down1'] = $asiaDown1;
        $oddItem['asia']['down2'] = $asiaDown2;
        $oddItem['goal']['up1'] = $goalUp1;
        $oddItem['goal']['up2'] = $goalUp2;
        $oddItem['goal']['middle1'] = $goalMiddle1;
        $oddItem['goal']['middle2'] = $goalMiddle2;
        $oddItem['goal']['down1'] = $goalDown1;
        $oddItem['goal']['down2'] = $goalDown2;
        $oddItem['ou']['up1'] = $europeUp1;
        $oddItem['ou']['up2'] = $europeUp2;
        $oddItem['ou']['down1'] = $europeDown1;
        $oddItem['ou']['down2'] = $europeDown2;

        $lg_match_odd[$lg_banker['rank']] = $oddItem;

        StatisticFileTool::putFileToTerminal($lg_match_odd, MatchLive::kSportBasketball, $lg_mid, 'odd');

        if ($isDebug) {
            echo "basketOddItemDays: lg_mid = $lg_mid <br>";
        }
    }

    /**
     * 篮球 每天的盘口与水位列表
     */
    private function basketOddDays($date = '',$cid = 3, $isDebug = false)
    {
        $tempCid = BasketOdd::banker_convert_array[$cid];
        if (is_null($tempCid) || !is_numeric($tempCid)) {
//            echo "handicapDays: cid = $cid is error <br>";
            return;
        }

        $lg_banker = self::win_cid_convert_array[$cid];
        if (!isset($lg_banker)) {
            return;
        }

        $url = "http://txt.win007.com/phone/lqodds.aspx?date=$date&type=0&companyid=$tempCid";
        $str = $this->spiderTextFromUrl($url);
        if ($str) {
            $ss = explode("$$", $str);
            if (count($ss) >= 2) {
                $asians = explode("!", $ss[1]);
                foreach ($asians as $asian) {
                    if (count(explode("^", $asian)) >= 24) {
                        self::basketOddItemDays($asian, $lg_banker, $isDebug);
                    }
                }
            }
        }
    }

    /**
     * 篮球 SB滚球盘盘口与水位
     */
    private function basketRollListChange($isDebug = false){

        $url = "http://lq3.win007.com/NBA/sbOddsDataBsk.js?".time()*1000;

        $content = $this->spiderTextFromUrlByWin007($url, false, "http://lq3.win007.com/nba.htm");
//        dump($content);
//        return;
        $sDatas = explode(";", $content);
//        echo "共有 ".count($sDatas)." 场比赛</br>";
        $gunQiuArray = array();
        foreach ($sDatas as $data) {
            if (str_contains($data, "=")) {
                list($mid, $odds) = explode("=", $data, 2);
                $length = strlen($odds);
                if (preg_match("/(?<=\\[)(.*)(?=\\])/i", $mid, $temps) && $length > 4) {
                    $mid = $temps[0];
                    $lg_mid = BasketMatch::getMatchIdWith($mid, "win_id");
                    if ($lg_mid <= 0) continue;

                    $oddList = explode("],[", substr($odds, 2, strlen($odds) - 4));
                    list($up1, $middle1, $down1, $up2, $middle2, $down2, $up, $middle, $down) = explode(",", $oddList[0], 9);
                    if (!isset($up) || strlen($up) <= 0) {
                        continue;
                    }
//                    echo $mid.'<br>';
                    //全场
                    $array['all'][1] = ['up1'=>$up1, 'middle1'=>$middle1, 'down1'=>$down1,
                        'up2'=>$up2, 'middle2'=>$middle2, 'down2'=>$down2,
                        'up'=>$up, 'middle'=>$middle, 'down'=>$down]; //亚盘
                    list($up1, $middle1, $down1, $up2, $middle2, $down2, $up, $middle, $down) = explode(",", $oddList[1], 9);
                    $array['all'][2] = ['up1'=>$up1, 'middle1'=>$middle1, 'down1'=>$down1,
                        'up2'=>$up2, 'middle2'=>$middle2, 'down2'=>$down2,
                        'up'=>$up, 'middle'=>$middle, 'down'=>$down]; //大小球

                    $ouOdd = \App\Models\LiaoGouModels\BasketOdd::query()->where('mid', $lg_mid)->where('cid', 2)->where('type',3)->first();//欧盘
                    if (isset($ouOdd)) {
                        $array['all'][3] = ['up1'=>number_format($ouOdd->up1,2), 'down1'=>number_format($ouOdd->down1,2),
                            'up2'=>number_format($ouOdd->up2,2), 'down2'=>number_format($ouOdd->down2,2)]; //大小球
                    }


                    //半场
                    list($up1, $middle1, $down1, $up2, $middle2, $down2, $up, $middle, $down) = explode(",", $oddList[2], 9);
                    $array['half'][1] = ['up1'=>$up1, 'middle1'=>$middle1, 'down1'=>$down1,
                        'up2'=>$up2, 'middle2'=>$middle2, 'down2'=>$down2,
                        'up'=>$up, 'middle'=>$middle, 'down'=>$down]; //亚盘
                    list($up1, $middle1, $down1, $up2, $middle2, $down2, $up, $middle, $down) = explode(",", $oddList[3], 9);
                    $array['half'][2] = ['up1'=>$up1, 'middle1'=>$middle1, 'down1'=>$down1,
                        'up2'=>$up2, 'middle2'=>$middle2, 'down2'=>$down2,
                        'up'=>$up, 'middle'=>$middle, 'down'=>$down]; //大小球

                    StatisticFileTool::putFileToTerminal($array, MatchLive::kSportBasketball, $lg_mid, 'roll');
                    $gunQiuArray[$lg_mid] = $array;

                    if ($isDebug) {
                        echo "basketRollOddChange: lg_mid = $lg_mid <br>";
                    }
                }
            }
        }
        StatisticFileTool::putFileToLiveChange($gunQiuArray, MatchLive::kSportBasketball, 'roll');
    }

    /**
     * 足球比赛列表，SB滚球盘 盘口与水位 实时变化
     */
    private function basketRollChange($isDebug = false) {
        $url = "http://lq3.win007.com/NBA/ch_nbaGoal3.xml?r=".time()*1000;

        $content = $this->spiderTextFromUrlByWin007($url);

        $key = "static_basket_roll_change";
        $data = Redis::get($key);
        if ($data == strlen($content)) {
            echo "there nothing change!<br>";
            return;
        } else {
            $data = strlen($content);
            Redis::set($key, $data);
        }

        preg_match_all("/<match>(.*?)<\\/match>/is", $content, $tempItems);
        if (isset($tempItems) && isset($tempItems[1]) && isset($tempItems[1][0])) {
            $tempStr = $tempItems[1][0];
            $tempStr = str_replace("<m>", "", $tempStr);
            $matches = explode("</m>", $tempStr);

            $rollData = StatisticFileTool::getFileFromChange(MatchLive::kSportBasketball, 'roll');
            if (!is_array($rollData)) $rollData = array();
            foreach ($matches as $itemStr) {
                if (count(explode(",", $itemStr)) >= 13) {
                    list($mid, $asiamiddle, $asiaup, $asiadown, $goalmiddle,
                        $goalup, $goaldown, $asiamiddlehalf, $asiauphalf, $asiadownhalf,
                        $goalmiddlehalf, $goaluphalf, $goalupdonw) = explode(",", $itemStr);
                    $lg_mid = BasketMatch::getMatchIdWith($mid, "win_id");
                    if ($lg_mid > 0) {
                        if (array_key_exists($lg_mid, $rollData)) {
                            $itemData = $rollData[$lg_mid];
                        } else {
                            $itemData = array();
                        }
                        $itemData['all'][1]['up'] = $asiaup;
                        $itemData['all'][1]['middle'] = $asiamiddle;
                        $itemData['all'][1]['down'] = $asiadown;
                        $itemData['all'][2]['up'] = $goalup;
                        $itemData['all'][2]['middle'] = $goalmiddle;
                        $itemData['all'][2]['down'] = $goaldown;
                        $itemData['half'][1]['up'] = $asiauphalf;
                        $itemData['half'][1]['middle'] = $asiamiddlehalf;
                        $itemData['half'][1]['down'] = $asiadownhalf;
                        $itemData['half'][2]['up'] = $goaluphalf;
                        $itemData['half'][2]['middle'] = $goalmiddlehalf;
                        $itemData['half'][2]['down'] = $goalupdonw;

                        $rollData[$lg_mid] = $itemData;

                        StatisticFileTool::putFileToTerminal($itemData, MatchLive::kSportBasketball, $lg_mid, 'roll');
                        if ($isDebug) {
                            echo "footballListOddChange: lg_mid = $lg_mid <br>";
                        }
                    }
                }
            }
            StatisticFileTool::putFileToLiveChange($rollData, MatchLive::kSportBasketball, 'roll');
        }
    }
}