<?php
/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/2/27
 * Time: 18:48
 */

namespace App\Http\Controllers\Statistic\Terminal\Football;


use App\Models\LiaoGouModels\Moro\WsAlias;
use App\Models\LiaoGouModels\Moro\WsMatch;

trait FootballStyleTool
{
    use WhoscoredAliases;

    private function teamStyleData($match, $teamStyle, $reset = false) {
        if (!$reset) {
            //如果有数据了则不再更新数据
            if (isset($teamStyle) && count($teamStyle) > 0) {
                return $teamStyle;
            }
        }

        $ws = WsMatch::where('mid','=',$match->win_id)->first();
        if (!isset($ws)) {
            return null;
        }
        $result = array();
        $charactersString = $ws->characters;
        $hcharacters = json_decode($charactersString,true);
        //翻译
        $array = $hcharacters['homeCharacter']['strengths'];
        for ($i = 0 ; $i < count($array) ; $i++){
            $tmp = $hcharacters['homeCharacter']['strengths'][$i];
            $name = array_key_exists($tmp['name'],$this->kCharacteristicsCN)?$this->kCharacteristicsCN[$tmp['name']]:$tmp['name'];
            $strength = array();
            $strength['name'] = isset($name)?$name:$tmp['name'];
            $strength['level'] = $tmp['level'];
            $strength['isOffensive'] = $tmp['isOffensive'];
            $result['home']['strengths'][] = $strength;
        }
        $array = $hcharacters['homeCharacter']['weaknesses'];
        for ($i = 0 ; $i < count($array) ; $i++){
            $tmp = $hcharacters['homeCharacter']['weaknesses'][$i];
            $name = array_key_exists($tmp['name'],$this->kCharacteristicsCN)?$this->kCharacteristicsCN[$tmp['name']]:$tmp['name'];
            $strength = array();
            $strength['name'] = $name;
            $strength['level'] = $tmp['level'];
            $strength['isOffensive'] = $tmp['isOffensive'];
            $result['home']['weaknesses'][] = $strength;
        }
        $array = $hcharacters['homeCharacter']['styles'];
        for ($i = 0 ; $i < count($array) ; $i++){
            $tmp = $hcharacters['homeCharacter']['styles'][$i];
            $name = array_key_exists($tmp['name'],$this->kStyleCN)?$this->kStyleCN[$tmp['name']]:$tmp['name'];
            $strength = array();
            $strength['name'] = isset($name)?$name:$tmp['name'];
            $strength['level'] = $tmp['level'];
            $strength['isOffensive'] = $tmp['isOffensive'];
            $result['home']['styles'][] = $strength;
        }
        //客队
        $array = $hcharacters['awayCharacter']['strengths'];
        for ($i = 0 ; $i < count($array) ; $i++){
            $tmp = $hcharacters['awayCharacter']['strengths'][$i];
            $name = array_key_exists($tmp['name'],$this->kCharacteristicsCN)?$this->kCharacteristicsCN[$tmp['name']]:$tmp['name'];
            $strength = array();
            $strength['name'] = isset($name)?$name:$tmp['name'];
            $strength['level'] = $tmp['level'];
            $strength['isOffensive'] = $tmp['isOffensive'];
            $result['away']['strengths'][] = $strength;
        }
        $array = $hcharacters['awayCharacter']['weaknesses'];
        for ($i = 0 ; $i < count($array) ; $i++){
            $tmp = $hcharacters['awayCharacter']['weaknesses'][$i];
            $name = array_key_exists($tmp['name'],$this->kCharacteristicsCN)?$this->kCharacteristicsCN[$tmp['name']]:$tmp['name'];
            $strength = array();
            $strength['name'] = $name;
            $strength['level'] = $tmp['level'];
            $strength['isOffensive'] = $tmp['isOffensive'];
            $result['away']['weaknesses'][] = $strength;
        }
        $array = $hcharacters['awayCharacter']['styles'];
        for ($i = 0 ; $i < count($array) ; $i++){
            $tmp = $hcharacters['awayCharacter']['styles'][$i];
            $name = array_key_exists($tmp['name'],$this->kStyleCN)?$this->kStyleCN[$tmp['name']]:$tmp['name'];
            $strength = array();
            $strength['name'] = isset($name)?$name:$tmp['name'];
            $strength['level'] = $tmp['level'];
            $strength['isOffensive'] = $tmp['isOffensive'];
            $result['away']['styles'][] = $strength;
        }

        //预测
        $array = $hcharacters['matchForeCastItems'];
        for ($i = 0 ; $i < count($array) ; $i++){
            $tmp = $hcharacters['matchForeCastItems'][$i];
            $name = str_replace('  ', ' ',$tmp['sentence']);
            foreach ($this->kForecastCN as $key=>$string){
                if (strstr($name, $key)) {
                    $teamName = str_replace($key, '', $name);
                    $teamName = substr($teamName,0,strlen($teamName) - 1);
                    if (strlen($teamName) > 0) {
                        $teamName = WsAlias::getAlias($teamName);
                    }
                    $name = $teamName.$string;
                }
            }
            $strength = array();
            $strength['sentence'] = $name;
            $strength['score'] = $tmp['score'];
            $result['case'][] = $strength;
        }

        return $result;
    }
}