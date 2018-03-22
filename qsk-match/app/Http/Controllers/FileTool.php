<?php
/**
 * Created by PhpStorm.
 * User: ricky
 * Date: 2017/11/20
 * Time: 18:26
 */

namespace App\Http\Controllers;


use App\Models\LiaoGouModels\Match;
use Illuminate\Support\Facades\Storage;

class FileTool extends Controller
{
    const kLive = 2;
    const kBasketball = 1;
    const kFootball = 0;
    const fileNames = [self::kFootball=>'match', self::kBasketball=>'basket', self::kLive=>'live'];

    /**
     * 比分更新的部分
     * @param array $data 数据
     * @param int $fileIndex 比赛类型
     */
    public static function putFileToLiveScore($data, $sportType = self::kFootball) {
        $name = self::fileNames[$sportType];
        self::putFile(self::fileNames[self::kLive], "/score/".$name.".json", json_encode($data));
    }

    //及时比赛 滚球盘数据 相关的文件 大文件
    public static function putFileToTotalLiveOdd($data, $fileIndex = self::kFootball) {
        self::putFile(self::fileNames[$fileIndex], "/live/odd/roll.json", json_encode($data));
    }

    //及时比赛 滚球盘数据 相关的文件
    public static function putFileToLiveOdd($mid, $data,$date = null, $fileIndex = self::kFootball) {
        $date = isset($date) ? $date : date("Ymd");
        self::putFile(self::fileNames[$fileIndex], "/live/odd/$date/$mid.json", json_encode($data));
    }

    public static function getFileFromLiveOdd($date, $mid, $fileIndex = self::kFootball) {
        return self::getFile(self::fileNames[$fileIndex], "/live/odd/$date/$mid.json");
    }

    //及时比赛 比赛数据(统计) 相关的文件
    public static function putFileToLiveEvent($mid, $data,$date = null, $fileIndex = self::kFootball) {
        $date = isset($date) ? $date : date("Ymd");
        self::putFile(self::fileNames[$fileIndex], "/live/event/$date/$mid.json", json_encode($data));
    }

    public static function getFileFromLiveEvent($date, $mid, $fileIndex = self::kFootball) {
        return self::getFile(self::fileNames[$fileIndex], "/live/event/$date/$mid.json");
    }

    //及时比赛 提点数据 相关的文件
    public static function putFileToLiveAnalyse($mid, $data, $date = null, $fileIndex = self::kFootball) {
        $date = isset($date) ? $date : date("Ymd");
        self::putFile(self::fileNames[$fileIndex], "/live/analyse/$date/$mid.json", json_encode($data));
    }

    public static function getFileFromLiveAnalyse($date, $mid, $fileIndex = self::kFootball) {
        return self::getFile(self::fileNames[$fileIndex], "/live/analyse/$date/$mid.json");
    }

    private static function putFile($disk, $filePatch, $data) {
        try {
            Storage::disk($disk)->put($filePatch, $data);
        } catch (\Exception $exception) {
//            dump($exception->getMessage());
        }
    }

    private static function getFile($disk, $filePath) {
        $data = null;
        try {
            $data = Storage::disk($disk)->get($filePath);
        } catch (\Exception $exception) {

        }
        return $data;
    }

    //============比赛列表静态化===========================
    public static function putFileToMatches($data, $sport, $type, $date = null) {
        $date = isset($date) ? $date : date("Ymd");
        self::putFile('matches', "/$date/$sport/$type.json", $data);
    }

    //============比赛详情静态化===========================
    public static function putFileToMatchDetail($data, $sport, $mid, $type, $date = null) {
        $date = isset($date) ? $date : date("Ymd");
        self::putFile('detail', "/$date/$sport/$mid/$type.json", $data);
    }

    //=============比赛详情新版的位置==========
    public static function putFileToMatch($data, $sport, $mid, $name) {
        $firstTag = substr($mid, 0, 2);
        $secondTag = substr($mid, 2, 2);
        self::putFile('detail', "/$sport/$firstTag/$secondTag/$mid/$name.json", $data);
    }

    public static function getFileFromMatch($sport, $mid, $name) {
        $firstTag = substr($mid, 0, 2);
        $secondTag = substr($mid, 2, 2);
        dump($firstTag, $secondTag,$mid, $sport,$name);
        return self::getFile('detail', "/$sport/$firstTag/$secondTag/$mid/$name.json");
    }
}