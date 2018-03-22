<?php

namespace App\Http\Controllers\Utils;

/**
 * Created by PhpStorm.
 * User: ricky
 * Date: 2017/6/12
 * Time: 17:54
 */
class DateUtils
{
    /**
     * 根据传入的星期几的index获取中文的星期
     * @param $index
     * @return mixed 中文的星期几 eg.星期一
     */
    public static function getTwoWordsWeek($index)
    {
        $week = array(
            "0" => "周日 ",
            "1" => "周一 ",
            "2" => "周二 ",
            "3" => "周三 ",
            "4" => "周四 ",
            "5" => "周五 ",
            "6" => "周六 "
        );
        return $week[$index];
    }

    /**
     * 根据传入的周几的index获取中文的星期
     * @param $index
     * @return mixed 中文的周几 eg.周一
     */
    public static function getThreeWordsWeek($index)
    {
        $week = array(
            "0" => "星期日 ",
            "1" => "星期一 ",
            "2" => "星期二 ",
            "3" => "星期三 ",
            "4" => "星期四 ",
            "5" => "星期五 ",
            "6" => "星期六 "
        );
        return $week[$index];
    }
}