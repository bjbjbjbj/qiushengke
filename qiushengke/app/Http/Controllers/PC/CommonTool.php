<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/3/5
 * Time: 上午11:06
 */

namespace App\Http\Controllers\PC;

class CommonTool
{
    //odd的
    const k_odd_type_asian = 1;//亚盘
    const k_odd_type_ou = 2;//大小
    const k_odd_type_europe = 3;//欧赔
    const k_odd_type_corner = 4;//角球

    //article的
    const kTypeAsian = 1;
    const kTypeOU = 2;
    const kTypeChina = 3;
    const kTypeBettingBall = 4;//篮球竞彩大小分

    //比赛类型
    const kSportFootball = 1, kSportBasketball = 2;//1：足球，2：篮球，其他待添加。

    public static function getImg($img_url) {
        if (!empty($img_url)) {
            $prefix = env('IMG_URL', 'http://img.liaogou168.com/');
            if (!str_contains($prefix, "http")) {
                $prefix = "http:" . $prefix;
            }
            return $prefix . $img_url;
        }
        return "";
    }

    /**
     * 根据比赛id返回path
     * @param $mid
     * @param int $sport
     * @return string
     */
    public static function matchPathWithId($mid,$sport=CommonTool::kSportFootball){
        $path = '';
        if ($mid > 1000) {
            $first = substr($mid,0,2);
            $second = substr($mid,2,2);
            if ($sport == 2) {
                $path = '/match/basket/' . $first . '/'. $second . '/' . $mid . '.html';
            } else {
                $path = '/match/foot/' . $first . '/'. $second . '/' . $mid . '.html';
            }
        }
        return $path;
    }

    /**
     * 前端盘口显示
     * @param $handicap
     * @param string $default
     * @param int $type
     * @param int $sport
     * @param bool $isHome
     * @return float|string
     */
    public static function getHandicapCn($handicap, $default = "", $type = CommonTool::k_odd_type_asian, $sport = CommonTool::kSportFootball, $isHome = true)
    {
        if ($sport == CommonTool::kSportFootball) {
            if ($type == CommonTool::k_odd_type_asian) {
                return CommonTool::panKouText($handicap, !$isHome);
            } else if ($type == CommonTool::k_odd_type_ou) {//大小球
                if ($handicap * 100 % 100 == 0) {
                    return round($handicap);
                }
                $handicap = round($handicap, 2);
                if ($handicap * 100 % 50 == 0) {//尾数为0.5的直接返回
                    return $handicap;
                }
                $tempHandicap = round($handicap);//四舍五入
                $intHandicap = floor($handicap);//取整
                if ($tempHandicap == $intHandicap) {//比较 四舍五入 与 取整大小，尾数为 0.25 则为相同
                    return $intHandicap . '/' . $intHandicap . '.5';
                } else {//否则尾数为0.75
                    return $intHandicap . '.5/' . ($intHandicap + 1);
                }
            } else if ($type == CommonTool::k_odd_type_europe) {//竞彩
                if ($handicap > 0) {
                    return "+" . $handicap;
                } else if ($handicap == 0) {
                    return "不让球";
                } else {
                    return $handicap;
                }
            }
        } elseif ($sport == CommonTool::kSportBasketball) {
            if ($type == CommonTool::k_odd_type_asian) {
                return CommonTool::panKouTextBK($handicap, !$isHome);
            } else if ($type == CommonTool::k_odd_type_ou) {//大小球
                return (($handicap * 100 % 100 == 0) ? round($handicap) : round($handicap, 2));
            } else if ($type == CommonTool::k_odd_type_europe) {//竞彩
                if ($handicap > 0) {
                    return "+" . $handicap;
                } else if ($handicap == 0) {
                    return "不让分";
                } else {
                    return $handicap;
                }
            } elseif ($type == CommonTool::kTypeBettingBall) {
                return (($handicap * 100 % 100 == 0) ? round($handicap) : round($handicap, 2));
            }
        }
        return $default;
    }

    /**
     * @param $float float 传入的小数
     * @param $notKeepZero boolean 是否不保留小数后的0
     * @return float 返回的保留两位有效数字后的结果
     */
    public static function float2Decimal($float, $notKeepZero = false){
        if (isset($float)) {
            if ($notKeepZero) {
                return round($float, 2);
            } else {
                return sprintf('%.2f', round($float, 2));
            }
        }
        return '-';
    }

    public static function colorOfUpDown($up1,$up2){
        if ($up2 > $up1){
            return 'red';
        }
        elseif ($up2 < $up1){
            return 'green';
        }
        return '';
    }

    public static function object_to_array($obj) {
        $object =  json_decode( json_encode( $obj),true);
        return  $object;
    }

    //根据赛事id返回背景色(rgb)
    public static function getLeagueBgRgb($lid) {
        if (isset($lid)) {
            $r = ($lid * 141) % 26 + ($lid * 71) % 121 + 0;
            $g = ($lid * 141) % 26 + ($lid * 71) % 51 + 0;
            $b = ($lid * 141) % 36 + ($lid * 71) % 86 + 0;
        } else {
            $r = 0;
            $g = 0;
            $b = 0;
        }

        return ['r'=>$r, 'g'=>$g, 'b'=>$b];
    }

    public static function objtoarr($obj){
        $ret = array();
        if (isset($obj)) {
            foreach ($obj as $key => $value) {
                if (gettype($value) == 'array' || gettype($value) == 'object') {
                    $ret[$key] = self::objtoarr($value);
                } else {
                    $ret[$key] = $value;
                }
            }
        }
        return $ret;
    }

    /**************** odd类迁移过来 ********************/
    /**
     * @param $middle float 盘口
     * @param bool $isAway 是否是客队
     * @param bool $isGoal 是否是大小球
     * @return string
     */
    public static function panKouText ($middle, $isAway = false, $isGoal = false) {
        if ($isGoal || $middle == 0){
            $prefix = "";
        } else{
            if ($isAway){
                $prefix = $middle < 0 ? "让" : "受让";
            }else{
                $prefix = $middle < 0 ? "受让" : "让";
            }
        }
        $text = $middle;
        $middle = abs($middle);
        switch ($middle) {
            case 7: $text = "七球"; break;
            case 6.75: $text = "六半/七球"; break;
            case 6.5: $text = "六球半"; break;
            case 6.25: $text = "六球/六半"; break;
            case 6: $text = "六球"; break;
            case 5.75: $text = "五半/六球"; break;
            case 5.5: $text = "五球半"; break;
            case 5.25: $text = "五球/五半"; break;
            case 5: $text = "五球"; break;
            case 4.75: $text = "四半/五球"; break;
            case 4.5: $text = "四球半"; break;
            case 4.25: $text = "四球/四半"; break;
            case 4: $text = "四球"; break;
            case 3.75: $text = "三半/四球"; break;
            case 3.5: $text = "三球半"; break;
            case 3.25: $text = "三球/三半"; break;
            case 3: $text = "三球"; break;
            case 2.75: $text = "两半/三球"; break;
            case 2.5: $text = "两球半"; break;
            case 2.25: $text = "两球/两半"; break;
            case 2: $text = "两球"; break;
            case 1.75: $text = "球半/两球"; break;
            case 1.5: $text = "球半"; break;
            case 1.25: $text = "一球/球半"; break;
            case 1: $text = "一球"; break;
            case 0.75: $text = "半/一"; break;
            case 0.5: $text = "半球"; break;
            case 0.25: $text = "平手/半球"; break;
            case 0: $text = "平手"; break;
        }
        if (!is_numeric($text)) {
            return $prefix . $text;
        }
        return $text;
    }

    /**
     * @param $middle float 盘口
     * @param bool $isAway 是否是客队
     * @param bool $isGoal 是否是大小球
     * @return string
     */
    public static function panKouTextBK ($middle, $isAway = false, $isGoal = false) {
        if ($isGoal || $middle == 0){
            $prefix = "";
        } else{
            if ($isAway){
                $prefix = $middle < 0 ? "让" : "受";
            }else{
                $prefix = $middle < 0 ? "受" : "让";
            }
        }
        $text = abs($middle) . '分';
        return $prefix . $text;
    }

    /**************** match类 *****************/
    /**
     * 获取足球比赛的即时时间
     * @param $time
     * @param $timehalf
     * @param $status
     * @return string
     */
    public static function getMatchCurrentTime($time, $timehalf, $status)
    {
        $time = strtotime(isset($timehalf)? $timehalf : $time);
        $timehalf = strtotime($timehalf);
        $now = strtotime(date('Y-m-d H:i:s'));
        if ($status < 0 || $status == 2 || $status == 4) {
            $matchTime = self::getStatusTextCn($status);
        }elseif ($status == 1) {
            $diff = ($now - $time) > 0 ? ($now - $time) : 0;
            $matchTime = (floor(($diff) % 86400 / 60)) > 45 ? ('45+' . '<span>' . '\''.'</span>') : ((floor(($diff) % 86400 / 60)) . '<span>' . '\''.'</span>');
        } elseif ($status == 3) {
            $diff = ($now - $timehalf) > 0 ? ($now - $timehalf) : 0;
            $matchTime = (floor(($diff) % 86400 / 60)) > 45 ? ('90+' . '<span>' . '\''.'</span>') : ((floor(($diff) % 86400 / 60) + 45) . '<span>' . '\''.'</span>');
        } else {
            $matchTime = '';
        }
        return $matchTime;
    }

    public static function getStatusTextCn($status) {
        switch ($status) {
            case 0:
                return "未开始";
            case 1:
                return "上半场";
            case 2:
                return "中场";
            case 3:
                return "下半场";
            case 4:
                return "加时";
            case -1:
                return "已结束";
            case -14:
                return "推迟";
            case -11:
                return "待定";
            case -12:
                return "腰斩";
            case -10:
                return "退赛";
            case -99:
                return "异常";
        }
        return '';
    }

    /************* matchcontroller 方法 ***************/
    /**
     * 获取比赛
     * @param $handicap
     * @param $type
     * @param int $sport
     * @return mixed
     */
    public static function getMatchOdds($handicap, $type, $sport = CommonTool::kSportFootball)
    {
        $typeCn = "";
        $typeValue = "";
        $sort = -1;
        if ($type == CommonTool::k_odd_type_asian) {
            if (!isset($handicap)) {
                $typeCn = "未开盘";
                $typeValue = "Odd_Asia_none";
            } else {
                if ($handicap > 3) {
                    $typeCn = "三球以上";
                    $sort = 13;
                    $typeValue = "Odd_Asia_$sort";
                } else if ($handicap < -3) {
                    $typeCn = "受三球以上";
                    $sort = 13;
                    $typeValue = "Odd_Asia_Negative_$sort";
                } else {
                    $typeCn = CommonTool::getHandicapCn($handicap, '', $type, $sport);
                    $temp = $handicap > 0 ? "Odd_Asia" : "Odd_Asia_Negative";
                    $sort = (abs($handicap) * 4);
                    $typeValue = $temp . "_" . $sort;
                }
            }
        } else if ($type = CommonTool::k_odd_type_ou) {
            if (!isset($handicap)) {
                $typeCn = "未开盘";
                $typeValue = "Odd_Goal_none";
            } else {
                if ($handicap < 2) {
                    $typeCn = "2球以下";
                    $sort = 7;
                    $typeValue = "Odd_Goal_$sort";
                } else if ($handicap > 4) {
                    $typeCn = "4球以上";
                    $sort = 17;
                    $typeValue = "Odd_Goal_$sort";
                } else {
                    $typeCn = CommonTool::getHandicapCn($handicap, '', $type, $sport) . "球";
                    $sort = (abs($handicap) * 4);
                    $typeValue = "Odd_Goal_" . $sort;
                }
            }
        }
        $matchOdds['sort'] = $sort;
        $matchOdds['typeCn'] = $typeCn;
        $matchOdds['typeValue'] = $typeValue;
        return $matchOdds;
    }

    //=============篮球================================================

    //获取篮球比赛的即时时间
    public static function getBasketCurrentTime($status, $liveStr, $isHalfFormat = false) {
        switch ($status) {
            case -1:
                $timeStr = '已结束';
                break;
            case 0:
                $timeStr = '';
                break;
            case 1:
                $timeStr = ($isHalfFormat ? '上半场 ' : '第一节 ').$liveStr;
                break;
            case 2:
                $timeStr = '第二节 '.$liveStr;
                break;
            case 3:
                $timeStr = ($isHalfFormat ? '下半场 ' : '第三节 ').$liveStr;
                break;
            case 4:
                $timeStr = '第四节 '.$liveStr;
                break;
            case 5:
                $timeStr = '加时1 '.$liveStr;
                break;
            case 6:
                $timeStr = '加时2 '.$liveStr;
                break;
            case 7:
                $timeStr = '加时3 '.$liveStr;
                break;
            case 8:
                $timeStr = '加时4 '.$liveStr;
                break;
            case 50:
            default:
                $timeStr = self::getStatusTextCnBK($status);
                break;
        }
        return $timeStr;
    }

    //获取篮球分数(默认'')
    public static function getBasketScore($score) {
        if (isset($score)) return $score;
        return '';
    }

    //获取篮球比赛 单个球队的半场分数
    public static function getBasketHalfScoreTxt($match, $isHome = true) {
        $status = $match['status'];
        if ($isHome) {
            $halfScore = $status == -1 ? (($match['hscore_1st'] + $match['hscore_2nd']) . " / " . ($match['hscore_3rd'] + $match['hscore_4th'])) : ($status > 2 ? (($match['hscore_1st'] + $match['hscore_2nd']) . " / " . ($match['hscore_3rd'] + $match['hscore_4th'])) : ($status > 0 ? ($match['hscore_1st'] + $match['hscore_2nd']) . ' / -' : ''));
        } else {
            $halfScore = $status == -1 ? (($match['ascore_1st'] + $match['ascore_2nd']) . " / " . ($match['ascore_3rd'] + $match['ascore_4th'])) : ($status > 2 ? (($match['ascore_1st'] + $match['ascore_2nd']) . " / " . ($match['ascore_3rd'] + $match['ascore_4th'])) : ($status > 0 ? ($match['ascore_1st'] + $match['ascore_2nd']) . ' / -' : ''));
        }

        return $halfScore;
    }

    //获取篮球比赛 半全场分差(总分)
    public static function getBasketScoreTxt($match, $isHalf = false, $isDiff = true) {
        $status = $match['status'];
        if ($isHalf) {
            if ($isDiff) {
                $txt = ($status == -1 || $status > 2) ? '半：' . ($match['hscore_1st'] + $match['hscore_2nd'] - $match['ascore_1st'] - $match['ascore_2nd']) : '';
            } else {
                $txt = ($status == -1 || $status > 2) ? '半：'.($match['hscore_1st']+$match['hscore_2nd']+$match['ascore_1st']+$match['ascore_2nd']) : '';
            }
        } else {
            if ($isDiff) {
                $txt = $status == -1 ? '全：' . ($match['hscore'] - $match['ascore']) : '';
            } else {
                $txt = $status == -1 ? '全：'.($match['hscore']+$match['ascore']) : '';
            }
        }
        return $txt;
    }

    //获取篮球比赛的加时
    public static function getBasketOtScore($ots) {
        return (!is_null($ots) && strlen($ots)>0) ? explode(',', $ots) : [];
    }

    public static function getStatusTextCnBK($status) {
        switch ($status) {
            case 0:
                return "未开始";
            case 1:
                return "第一节";
            case 2:
                return "第二节";
            case 3:
                return "第三节";
            case 4:
                return "第四节";
            case 5:
                return "加时1";
            case 6:
                return "加时2";
            case 7:
                return "加时3";
            case 50:
                return "中场";
            case -1:
                return "已结束";
            case -5:
                return "推迟";
            case -2:
                return "待定";
            case -12:
                return "腰斩";
            case -10:
                return "退赛";
            case -99:
                return "异常";
        }
        return '';
    }

    public static function getIconBK($icon) {
        if (isset($icon) && strlen($icon) > 0 && !str_contains($icon, '/files/team/noflag.gif')) {
            if (str_contains($icon, '.gif') && str_contains($icon, 'team/images/2005')) {
                return env('CDN_URL') . '/pc/img/icon_teamDefault.png';
            }
            return $icon;
        } else {
            return env('CDN_URL') . '/pc/img/icon_teamDefault.png';
        }
    }
}