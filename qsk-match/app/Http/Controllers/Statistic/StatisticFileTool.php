<?php
/**
 * Created by PhpStorm.
 * User: ricky
 * Date: 2018/2/26 0026
 * Time: 11:43
 */

namespace App\Http\Controllers\Statistic;


use Illuminate\Support\Facades\Storage;

class StatisticFileTool
{
    private static function putFile($disk, $filePatch, $data) {
        try {
            Storage::disk($disk)->put($filePatch, json_encode($data));
        } catch (\Exception $exception) {
            dump($exception->getMessage());
        }
    }

    private static function getFile($disk, $filePath) {
        $data = null;
        try {
            $data = json_decode(Storage::disk($disk)->get($filePath), true);
        } catch (\Exception $exception) {
//            dump($exception->getMessage());
        }
        return $data;
    }

    //============实时更改部分===============
    public static function putFileToLiveChange($data, $sport, $name) {
        self::putFile('change', "/$sport/$name.json", $data);
    }

    public static function getFileFromChange($sport, $name) {
        return self::getFile('change', "/$sport/$name.json");
    }

    //============比赛列表静态化===========================
    public static function putFileToSchedule($data, $sport, $type, $date = null) {
        if (!isset($data)) return;

        if (isset($date)) {
            $date = date("Ymd", strtotime($date));
        } else {
            $date = date("Ymd");
        }
        self::putFile('schedule', "/$date/$sport/$type.json", $data);
    }

    public static function getFileFromSchedule($date, $sport, $type) {
        if (isset($date)) {
            $date = date("Ymd", strtotime($date));
        } else {
            $date = date("Ymd");
        }
        return self::getFile('schedule', "/$date/$sport/$type.json");
    }

    //=============比赛详情新版的位置==========
    public static function putFileToTerminal($data, $sport, $mid, $name) {
        if (!isset($data)) return;

        $firstTag = substr($mid, 0, 2);
        $secondTag = substr($mid, 2, 2);
        self::putFile('terminal', "/$sport/$firstTag/$secondTag/$mid/$name.json", $data);
    }

    public static function getFileFromTerminal($sport, $mid, $name) {
        $firstTag = substr($mid, 0, 2);
        $secondTag = substr($mid, 2, 2);
        return self::getFile('terminal', "/$sport/$firstTag/$secondTag/$mid/$name.json");
    }

    //=================赛事相关的静态文件=======================
    public static function putFileToLeague($data, $sport, $lid) {
        if (!isset($data)) return;

        self::putFile('league', "/$sport/$lid.json", $data);
    }

    public static function getFileFromLeague($sport, $lid) {
        return self::getFile('league', "/$sport/$lid.json");
    }
}