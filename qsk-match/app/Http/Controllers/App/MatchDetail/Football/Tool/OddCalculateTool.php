<?php
/**
 * Created by PhpStorm.
 * User: ricky
 * Date: 17/02/13
 * Time: 下午17:02
 */

namespace App\Http\Controllers\App\MatchDetail\Football\Tool;

use App\Http\Controllers\Controller;
use App\Models\LiaoGouModels\Odd;
use Illuminate\Support\Facades\DB;

class OddCalculateTool extends Controller
{
    /**
     * 获取终盘盘口
     */
    public static function getOddMiddle2($mid, $type, $cid = Odd::default_calculate_cid)
    {
        $odd = Odd::query()->where('mid', $mid)->where('cid', $cid)->where('type', $type)->first();
        if (isset($odd)) {
            $middle = $odd->middle2;
        } else {
            $middle = NULL;
        }
        return $middle;
    }

    /**
     * 获取盘口
     */
    public static function getOdd($mid, $type, $cid = Odd::default_calculate_cid)
    {
        $odd = Odd::query()->where('mid', $mid)->where('cid', $cid)->where('type', $type)->first();
        return $odd;
    }

    /**
     * 比赛结果 type 1亚盘 2大小球 3欧赔 4角球
     */
    public static function getMatchOddResult($type, $hscore, $ascore, $middle, $isHomeTeam = true, $withHalf = false) {
        $result = -1;
        switch ($type) {
            case '1':
                $result = self::getMatchAsiaOddResult($hscore, $ascore, $middle, $isHomeTeam, $withHalf);
                break;
            case '3':
                $result = self::getMatchResult($hscore, $ascore, $isHomeTeam);
                break;
            case '2':
            case '4':
                $result = self::getMatchSizeOddResult($hscore, $ascore, $middle, $withHalf);
                break;
        }
        return $result;
    }

    /**
     * 获取比赛胜平负的数据
     *
     * 统一用 3 1 0 来标识 胜平负
     */
    public static function getMatchResult($hscore, $ascore, $isHomeTeam = true)
    {
        $result = -1;
        if (isset($hscore) && isset($ascore) && $hscore >= 0 && $ascore >= 0) {
            $count = $hscore - $ascore;
            if ($isHomeTeam) {
                if ($count < 0) {
                    $result = 0; //负
                } else if ($count == 0) {
                    $result = 1; //平
                } else {
                    $result = 3; //胜
                }
            } else {
                if ($count < 0) {
                    $result = 3; //胜
                } else if ($count == 0) {
                    $result = 1; //平
                } else {
                    $result = 0; //负
                }
            }
        }
        return $result;
    }

    /**
     * 获取让球盘赢盘的数据
     *
     * 统一用 3 1 0 来标识 胜平负
     *
     * withHalf boolean 是否返回半红半黑结果
     */
    public static function getMatchAsiaOddResult($hscore, $ascore, $middle, $isHomeTeam = true, $withHalf = false)
    {
        $result = -1;
        if (isset($hscore) && isset($ascore) && $hscore >= 0 && $ascore >= 0 && isset($middle)) {
            $count = $hscore - $middle - $ascore;
            if ($isHomeTeam) {
                if ($count < 0) {
                    if ($withHalf) {
                        $result = $count > -0.5 ? -0.5 : 0; //半黑
                    } else {
                        $result = 0; //输
                    }
                } else if ($count == 0) {
                    $result = 1; //走
                } else {
                    if ($withHalf) {
                        $result = $count < 0.5 ? 0.5 : 3; //半红
                    } else {
                        $result = 3; //赢
                    }
                }
            } else {
                if ($count < 0) {
                    if ($withHalf) {
                        $result = $count > -0.5 ? 0.5 : 0; //半红
                    } else {
                        $result = 3; //赢
                    }
                } else if ($count == 0) {
                    $result = 1; //走
                } else {
                    if ($withHalf) {
                        $result = $count < 0.5 ? -0.5 : 3; //半红
                    } else {
                        $result = 0; //输
                    }
                }
            }
        }
        return $result;
    }

    /**
     * 获取大小球盘赢盘的数据
     *
     * 统一用 3 1 0 来标识 大 走 小
     *
     * withHalf boolean 是否返回半红半黑结果
     */
    public static function getMatchSizeOddResult($hscore, $ascore, $middle, $withHalf = false)
    {
        $result = -1;
        if (isset($middle) && isset($hscore) && isset($ascore) && $hscore >= 0 && $ascore >= 0) {
            $count = $hscore + $ascore - $middle;
            if ($count < 0) {
                if ($withHalf) {
                    $result = $count > -0.5 ? -0.5 : 0; //半黑
                } else {
                    $result = 0; //小
                }
            } else if ($count == 0) {
                $result = 1; //走
            } else {
                if ($withHalf) {
                    $result = $count < 0.5 ? 0.5 : 3; //半红
                } else {
                    $result = 3; //大
                }
            }
        }
        return $result;
    }

    /**
     * 硬盘率 大球率 小球率
     * @param $win int 赢场
     * @param $draw int 平(走水)场
     * @param $lose int 输场
     * @param $isWin boolean 是否是计算胜率 默认是
     * @param $isIncludeDraw boolean 是否是包括走水 默认否
     * @return float 胜率
     */
    public static function getOddWinPercent($win, $draw, $lose, $isWin = true, $isIncludeDraw = true)
    {
        if ($win >= 0 && $lose >= 0 && $draw >= 0) {
            if ($isIncludeDraw) {
                if ($win + $lose + $draw <= 0) {
                    return 0;
                }
                if ($isWin) {
                    return $win/($win+$draw+$lose);
                } else {
                    return $lose/($win+$draw+$lose);
                }
            } else {
                if ($win + $lose <= 0) {
                    return 0;
                }
                if ($isWin) {
                    return $win/($win+$lose);
                } else {
                    return $lose/($win+$lose);
                }
            }
        }
        return 0;
    }

    /**
     * 获取上下盘的数据
     *
     * 统一用 3 1 0 来标识 上 平 下
     */
    public static function getMatchUpDownOddResult($middle, $isHomeTeam = true)
    {
        $result = -1;
        if (isset($middle)) {
            if ($isHomeTeam) {
                if ($middle > 0) {
                    $result = 3; //上
                } else if ($middle == 0) {
                    $result = 1; //平
                } else {
                    $result = 0; //下
                }
            } else {
                if ($middle > 0) {
                    $result = 0; //下
                } else if ($middle == 0) {
                    $result = 1; //平
                } else {
                    $result = 3; //上
                }
            }
        }
        return $result;
    }

    /**
     * 获取结果
     *
     * @param $hscore int 主队得分
     * @param $ascore int 客队得分
     * @param $middle float 盘口
     * @param $isHomeTeam boolean 是否是主队
     * @param $type int 0胜平负 1亚盘 2大小球
     * @return int 3胜 1平 0负
     */
    public static function getResult($hscore, $ascore, $middle, $isHomeTeam, $type)
    {
        $result = -1;
        if (isset($hscore) && isset($ascore) && $hscore >= 0 && $ascore >= 0) {
            switch ($type) {
                case 0: //胜平负
                    $result = self::getMatchResult($hscore, $ascore, $isHomeTeam);
                    break;
                case 1: //亚盘
                    $result = self::getMatchAsiaOddResult($hscore, $ascore, $middle, $isHomeTeam);
                    break;
                case 2: //大小球
                    $result = self::getMatchSizeOddResult($hscore, $ascore, $middle);
                    break;
            }
        }
        switch ($result) {
            case 0:
                return $type == 1 ? '输' : ($type == 2 ? '小' : '负');
            case 1:
                return $type == 1 || $type == 2 ? '走' : '平';
            case 3:
                return $type == 1 ? '赢' : ($type == 2 ? '大' : '胜');
            default:
                return '-';
        }
    }

    /**
     * 获取搜索历史同陪query语句
     * @param $up 上
     * @param $middle 中
     * @param $down 下
     * @param $type 类型1压 2大小 3欧
     * @param $time 比赛时间
     * @param $size 多少条
     */
    public static function queryForHistorySameOdd($up, $middle, $down, $type,$time, $size){
//        $query = Match::
//        select('matches.*',
//            'odds.up1 as up1','odds.middle1 as middle1','odds.down1 as down1',
//            'odds.up2 as up2','odds.middle2 as middle2','odds.down2 as down2')
//            ->join('odds',function ($q) use($up,$middle,$down,$type){
//                $q->on('matches.id','=','odds.mid');
//                $q->where('odds.type',$type)
//                    ->where('odds.cid',8)
//                    ->where("odds.up1",$up)
//                    ->where("odds.middle1",$middle)
//                    ->where("odds.down1",$down);
//                $q ->where('matches.status',-1)
//                    ->where('matches.time','<',date_create());
//            })
//            ->orderby('matches.time','desc')
//            ->take($size);
        //框架有bug,不能搜float
        $startTime = date_create($time);
        $startTime = date_format(date_sub($startTime, date_interval_create_from_date_string('2 year')), 'Y-m-d H:i');
        $query = DB::connection("liaogou_match")->select('select matches.*, odds.id as odd_id,
        odds.up1 as up1, odds.middle1 as middle1, odds.down1 as down1,
        odds.up2 as up2, odds.middle2 as middle2, odds.down2 as down2 from matches '.
        'join odds on (matches.id = odds.mid and odds.cid = 8 and odds.type = '. $type .
            ' and odds.up1 = '. $up .' and odds.middle1='.$middle.' and odds.down1 = '.$down.' and matches.status = -1'.
            ' and matches.time < \''.$time.'\' and matches.time >  \''.$startTime.'\') order by matches.time desc limit '.$size);
        return $query;
    }

    /**
     * 获取搜索历史同陪query语句
     * @param $up 上
     * @param $middle 中
     * @param $down 下
     * @param $type 类型1压 2大小 3欧
     * @param $time 比赛时间
     * @param $size 多少条
     */
    public static function queryForHistorySameOddForDetail($up, $middle, $down, $type,$time, $size){
        $startTime = date_create($time);
        $startTime = date_format(date_sub($startTime, date_interval_create_from_date_string('2 year')), 'Y-m-d H:i');
        $db = DB::connection("liaogou_match");
        $query = $db->select('select matches.*, leagues.name as lname,
        odds.up1 as up1, odds.middle1 as middle1, odds.down1 as down1,
        odds.up2 as up2, odds.middle2 as middle2, odds.down2 as down2 from matches '.
            'left join leagues on leagues.id = matches.lid '.
            'join odds on (matches.id = odds.mid and odds.cid = 8 and odds.type = '. $type .
            ' and odds.up1 = '. $up .' and odds.middle1='.$middle.' and odds.down1 = '.$down.' and matches.status = -1'.
            ' and matches.time < \''. $time .'\' and matches.time >  \''.$startTime.'\') order by matches.time desc limit '.$size);
        return $query;
    }

    /**
     * 根据欧赔获取胜平负的概率
     * @param $odd Odd 盘口实体
     * @param $type int 类型 3胜 1平 0负
     * @param $isFirst boolean 是否是初盘
     * @return int|string 百分比
     */
    public static function getOuPercent($odd, $type = 3, $isFirst = true) {
        if (isset($odd)) {
            if ($isFirst){
                $tempPer = $type == 1 ? $odd['middle1'] : ($type == 0 ? $odd['down1']:$odd['up1']);
                $backPercent = self::getBackPercent($odd, true);
            }else{
                $tempPer = $type == 1 ? $odd['middle2'] : ($type == 0 ? $odd['down2']:$odd['up2']);
                $backPercent = self::getBackPercent($odd, false);
            }
            if ($tempPer != 0) {
                return sprintf('%.2f', $backPercent / $tempPer);
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }

    /**
     * 根据欧赔获取返还率
     * @param $odd Odd 盘口
     * @param $isFirst boolean 是否是初盘
     * @return int|string 百分比
     */
    public static function getBackPercent($odd, $isFirst = false){
        if (!isset($odd)) {
            return 0;
        }
        if ($isFirst){
            $top = $odd['up1']*$odd['middle1']*$odd['down1'];
            $bottom = $odd['up1']*$odd['middle1'] + $odd['up1']*$odd['down1'] + $odd['middle1']*$odd['down1'];
        } else {
            $top = $odd['up2']*$odd['middle2']*$odd['down2'];
            $bottom = $odd['up2']*$odd['middle2'] + $odd['up2']*$odd['down2'] + $odd['middle2']*$odd['down2'];
        }
        if ($bottom != 0) {
            return sprintf('%.2f', $top / $bottom *100);
        }
        return 0;
    }

    /**
     * 根据初终盘计算凯利指数
     * @param $odd Odd 某公司欧赔盘口
     * @param $odds array 多家公司的平均盘口
     * @return mixed float 凯利指数
     */
    public static function getKaiLiOdd($odd, $odds, $type = 3, $isFirst = false) {
        if (!isset($odd) || !isset($odds) || count($odds) <= 0){
            return 0;
        }
        if ($isFirst){
            $tempPer = $type == 1 ? $odd['middle1'] : ($type == 0 ? $odd['down1']:$odd['up1']);
        }else {
            $tempPer = $type == 1 ? $odd['middle2'] : ($type == 0 ? $odd['down2'] : $odd['up2']);
        }
        $totalPercent = 0;
        foreach ($odds as $temp){
            $totalPercent += self::getOuPercent($temp, $type, $isFirst)/100;
        }
        $avgPercent = $totalPercent/count($odds);
        return sprintf('%.2f', $tempPer*$avgPercent);
    }
}