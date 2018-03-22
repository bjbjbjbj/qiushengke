<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 16/11/22
 * Time: 下午6:53
 */

namespace App\Http\Controllers;

use App\Models\LiaoGouModels\MatchEuropePrediction;
use App\Models\LiaoGouModels\Odd;

trait LotteryTool
{
    /**
     * 欧赔概率
     * @param $europe 欧盘盘口
     * @return float|int 返回盘口逆推百分百
     */
    public static function percentForEurope($europe){
        return 90/$europe;
    }

    /**
     * 根据金额返回任九投注策略
     * @param $cost 金额
     * @return array
     */
    public static function getBetPlan($cost){
        if ($cost == 0){
            return array(0,0);
        }
        if ($cost >= 2*19683){
            return array(0,9);
        }
        $cost = $cost/2;
        $dis = $cost;
        $resulti = 0;
        $resultj = 0;
        for ($i =0; $i < 9; $i++) {
            for ($j = 0; $j < 9; $j++) {
                $result = pow(2,$i) * pow(3,$j);
                if ($cost >= $result && $dis > ($cost - $result) && $i > $j){
                    $dis = $cost - $result;
                    $resulti = $i;
                    $resultj = $j;

//                    echo $resulti . ' ' .$resultj . ' '.$cost.' ' .$result.' ' .$dis.'</br>';
                }
                else{
//                    echo 'no ' .$resulti . ' ' .$resultj . ' '.$cost.' ' .$result.' ' .$dis.'</br>';
                }
            }
        }
        return array($resulti,$resultj);
    }

    /**
     * 概率倒序
     * @param $a
     * @param $b
     * @return int
     */
    public function my_sort_percent($a, $b)
    {
        if ($a['percent']==$b['percent'])
            return 0;
        return ($a['percent']>$b['percent'])?-1:1;
    }

    /**
     * 标准差倒序
     * 足彩竞猜专用,因为要尽量买今天的
     * @param $a
     * @param $b
     * @return int
     */
    public function my_sort($a, $b)
    {
        //时间
//        if ($a['data']['deadline'] > $b['data']['deadline'])
//        {
//            return 1;
//        }
//        elseif ($a['data']['deadline'] < $b['data']['deadline'])
//        {
//            return -1;
//        }

        if ($a['stand']==$b['stand'])
            return 0;
        return ($a['stand']>$b['stand'])?-1:1;
    }

    /**
     * 时间正序
     * @param $a
     * @param $b
     * @return int
     */
    public function sort_by_time($a, $b)
    {
        if ($a['time']==$b['time'])
            return 0;
        return ($a['time']>$b['time'])?1:-1;
    }

    /**
     * 标准差
     * @param $arr 数组
     * @return float|int 标准差
     */
    public function std_deviation($arr){
        $arr_size=count($arr);
        $mu=array_sum($arr)/$arr_size;
        $ans=0;
        foreach($arr as $elem){
            $ans+=pow(($elem-$mu),2);
        }
        return $ans/$arr_size;
    }

    /**
     * 根据欧赔倒推胜平负概率(默认用cid=5 bet365的赔率
     * @param $mid
     * @return 预测model
     */
    public function analyseBetByMatchId($mid){
        if ($mid <= 0)
            return null;
        $prediction = MatchEuropePrediction::where('id','=',$mid)->first();
        if (is_null($prediction)){
            $prediction = new MatchEuropePrediction();
            $prediction->id = $mid;
        }
        //默认cid是5作为预测结果保存,如果这里要修改或者这个预测算法改这里要做处理
        if (is_null($prediction->prediction_result)){
            $odd = Odd::where('mid','=',$mid)
                ->where('cid','=',Odd::default_calculate_cid)
                ->where('type','=',3)->first();
            //万一bet365没有,这个要用其他公司的去算这个比赛
            if(is_null($odd)){
                $odd = Odd::where('mid','=',$mid)
                    ->where('type','=',3)->first();
            }
            if (isset($odd)) {
                $prediction->prediction_result_win = LotteryTool::percentForEurope($odd->up1);
                $prediction->prediction_result_draw = LotteryTool::percentForEurope($odd->middle1);
                $prediction->prediction_result_lose = LotteryTool::percentForEurope($odd->down1);
                $prediction->prediction_result_count = 0;
                if ($prediction->prediction_result_win > 55 ||
                    $prediction->prediction_result_draw > 55 ||
                    $prediction->prediction_result_lose > 55
                ){
                    $prediction->prediction_result_count = 1;
                }
                else{
                    if ($prediction->prediction_result_win < 30 ||
                        $prediction->prediction_result_draw < 30 ||
                        $prediction->prediction_result_lose < 30){
                        $prediction->prediction_result_count = 2;
                    }
                    else {
                        $prediction->prediction_result_count = 3;
                    }
                }

                //按几率排
                $tmpPer = array(
                    array('result'=>3,'percent'=>$prediction->prediction_result_win),
                    array('result'=>1,'percent'=>$prediction->prediction_result_draw),
                    array('result'=>0,'percent'=>$prediction->prediction_result_lose)
                );
                usort($tmpPer,array($this,"my_sort_percent"));
                $string = '';
                for ($j = 0; $j < 3; $j++){
                    if ($j > 0){
                        $string = $string.'|'.$tmpPer[$j]['result'];
                    }
                    else{
                        $string = $tmpPer[$j]['result'];
                    }
                }
                $prediction->prediction_result = $string;
                $prediction->save();
            }
            else{
                //没有欧赔数据
                $prediction->save();
                return null;
            }
        }
        return $prediction;
    }
}