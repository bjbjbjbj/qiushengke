<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/3/20
 * Time: 下午5:07
 */

namespace App\Http\Controllers\PC;

use Illuminate\Support\Facades\Storage;

class FileTool{
    /************** 本地文件处理 *****************/
    /**
     * 获取缓存路径
     * @param $date = ''   时间格式：20180101
     * @param $sport = 1   竞技类型 1.足球，2.篮球
     * @return string 返回缓存的路径
     */
    public static function getMatchListStoragePatch($date = '', $sport = 1) {
        if (empty($date)) {
            $date = date('Ymd');
        }
        if ($sport == CommonTool::kSportBasketball) {
            $sport = 'basketball';
        } else {
            $sport = 'football';
        }
        $patch = '/static/schedule/'.$date.'/'.$sport.'/all.json';
        return $patch;
    }

    /**
     * 获取列表json
     * @param string $date
     * @param int $sport
     * @return mixed
     */
    public static function matchListDataJson($date = '',$sport) {
        try {
            $patch = FileTool::getMatchListStoragePatch($date, $sport);
            $patch = '/public' . $patch;
            $json = Storage::get($patch);
            if (is_null($json)){
                $json = self::curlData(env('MATCH_URL').'/static/schedule/'.$date.'/'.$sport.'/all.json');
                return $json;
            }
            else{
                return $json;
            }
        } catch (\Exception $exception) {
            $json = self::curlData(env('MATCH_URL').'/static/schedule/'.$date.'/'.$sport.'/all.json');
            return $json;
        }
    }

    public static function matchDetailStoragePatch($first,$second,$mid,$sport,$name){
        $patch = "/static/terminal/$sport/".$first."/".$second."/".$mid."/$name.json";
        return $patch;
    }

    /**
     * 获取比赛终端数据
     * @param $first
     * @param $second
     * @param $mid
     * @param $sport
     * @param $name
     * @return mixed
     */
    public static function matchDetailJson($first,$second,$mid,$sport,$name){
        try {
            $patch = FileTool::matchDetailStoragePatch($first,$second,$mid,$sport,$name);
//            $patch = '/public' . $patch;
            $patch = public_path($patch);
            $json = file_get_contents($patch);//Storage::get($patch);
            if (is_null($json)){
                $json = self::curlData(env('MATCH_URL')."/static/terminal/$sport/".$first."/".$second."/".$mid."/$name.json",10);
                return $json;
            }
            else{
                return json_decode($json, true);
            }
        } catch (\Exception $exception) {
            $json = self::curlData(env('MATCH_URL')."/static/terminal/$sport/".$first."/".$second."/".$mid."/$name.json",10);
            return $json;
        }
    }

    /**
     * 请求match接口
     * @param $url
     * @param $timeout
     * @return mixed
     */
    public static function curlData($url,$timeout = 5){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);//5秒超时
        $pc_json = curl_exec ($ch);
        curl_close ($ch);
        $pc_json = json_decode($pc_json,true);
        return $pc_json;
    }
}