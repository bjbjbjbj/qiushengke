<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2017/12/6
 * Time: 下午12:28
 */

namespace App\Http\Controllers\WinSpider\basket;

use App\Models\LiaoGouModels\Match;
use App\Models\WinModels\Banker;
use App\Models\WinModels\BasketMatch;
use App\Models\WinModels\BasketOdd;
use App\Models\WinModels\BasketSeason;
use App\Models\WinModels\BasketStage;
use App\Models\WinModels\BasketState;
use App\Models\WinModels\BasketTeam;
use Illuminate\Http\Request;
use QL\QueryList;

trait SpiderBasketTeam
{
    /**
     * 填充球队数据,球探爬下来只有id,需要填充
     */
    public function spiderTeam(){
        $teams = BasketTeam::where(function ($q){
            $q->whereNull('name_china');
        })
            ->orderby('created_at','asc')
            ->take(10)
            ->get();
        foreach ($teams as $team){
            self::spiderTeamWithId($team->id);
        }
    }

    /**
     * 根据球队id爬数据
     * @param Request $request
     */
    public function spiderTeamById(Request $request){
        $tid = $request->input('id',0);
        self::spiderTeamWithId($tid);
    }

    public function spiderTeamWithId($tid){
        if ($tid == 0){
            echo '参数有误';
            return;
        }
        $url = 'http://nba.win007.com/jsData/teamInfo/teamDetail/td'.$tid.'.js';

        echo $url . '</br>';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if (substr($url, 0, 5) == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.98 Safari/537.36");
//        curl_setopt($ch, CURLOPT_USERAGENT, "WSMobile/1.5.1 (iPad; iOS 10.2; Scale/2.00)");
        curl_setopt($ch, CURLOPT_REFERER, "http://nba.win007.com/cn/Team/Summary.aspx?TeamID=".$tid);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        $response = curl_exec($ch);
        if ($error = curl_error($ch)) {
            die($error);
        }
        curl_close($ch);

        list($head, $content) = explode("\r\n\r\n", $response, 2);
        $sDatas = explode(";", $content);
        for ($i = 0 ; $i < count($sDatas) ; $i++) {
//            dump($data);
            $data = $sDatas[$i];
            switch ($i){
                case 1:{
                    if (str_contains($data, "=")) {
                        list($key, $value) = explode("=", $data, 2);
                        $datas = explode(',',$value);
                        $team = BasketTeam::find($tid);
                        if (is_null($team)){
                            $team = new BasketTeam();
                            $team->id = $tid;
                        }
                        $team->name_china = str_replace('\'','',$datas[1]);
                        $team->name_hk = str_replace('\'','',$datas[2]);
                        $team->name_en = str_replace('\'','',$datas[3]);
                        $team->name_china_short = str_replace('\'','',$datas[4]);
                        $team->name_hk_short = str_replace('\'','',$datas[5]);
                        $team->name_en_short = str_replace('\'','',$datas[6]);
                        $team->icon = str_replace('\'','',$datas[9]);
                        $team->city = str_replace('\'','',$datas[11]);
                        $team->establish = str_replace('\'','',$datas[14]);
                        $team->gym = str_replace('\'','',$datas[12]);
                        $team->link = str_replace('\'','',$datas[10]);
                        $team->save();
                        \App\Models\LiaoGouModels\BasketTeam::saveWithWinData($team,$tid,$team->name_china,true);
                    }
                }
                    break;
            }
        }
    }
}