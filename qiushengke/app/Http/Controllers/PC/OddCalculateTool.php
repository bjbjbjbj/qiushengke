<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/3/5
 * Time: 下午5:47
 */

namespace App\Http\Controllers\PC;

class OddCalculateTool
{
    /**
     * 比赛结果 type 1亚盘 2大小球 3欧赔 4角球
     * @param $type
     * @param $hscore
     * @param $ascore
     * @param $middle
     * @param bool $isHomeTeam
     * @param bool $withHalf
     * @return float|int
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
     * 统一用 3 1 0 来标识 胜平负
     * @param $hscore
     * @param $ascore
     * @param bool $isHomeTeam
     * @return int
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
     * 统一用 3 1 0 来标识 胜平负
     * withHalf boolean 是否返回半红半黑结果
     * @param $hscore
     * @param $ascore
     * @param $middle
     * @param bool $isHomeTeam
     * @param bool $withHalf
     * @return float|int
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
     * 统一用 3 1 0 来标识 大 走 小
     * withHalf boolean 是否返回半红半黑结果
     * @param $hscore
     * @param $ascore
     * @param $middle
     * @param bool $withHalf
     * @return float|int
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
     * 统一用 3 1 0 来标识 上 平 下
     * @param $middle
     * @param bool $isHomeTeam
     * @return int
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
}