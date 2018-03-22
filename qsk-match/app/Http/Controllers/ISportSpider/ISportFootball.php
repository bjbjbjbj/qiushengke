<?php

namespace App\Http\Controllers\ISportSpider;

use App\Models\ISportModels\LotteryFootballCrs;
use App\Models\ISportModels\LotteryFootballHad;
use App\Models\ISportModels\LotteryFootballHafu;
use App\Models\ISportModels\LotteryFootballHafus;
use App\Models\ISportModels\LotteryFootballHhad;
use App\Models\ISportModels\LotteryFootballMatch;
use App\Models\ISportModels\LotteryFootballTtgs;
use App\Models\LiaoGouModels\Match;
use Illuminate\Http\Request;

trait ISportFootball{

    private function getCookies(){
        $url = 'http://i.sporttery.cn/odds_calculator/get_odds?i_format=json&i_callback=&poolcode[]=hhad&poolcode[]=had&poolcode[]=crs&poolcode[]=ttg&poolcode[]=hafu';
//        $url = 'http://i.sporttery.cn/odds_calculator/get_odds?i_format=json&poolcode=hhad';
        echo $url . '</br>';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
        $str = curl_exec($ch);
        curl_close($ch);

        //设置默认的cookies,防止死循环
        $currentCookies = null;
        $lines = explode("\n", $str);
        foreach ($lines as $line) {
            $line = trim($line);
//            dump($line);
            if (preg_match('#Set-Cookie: .*#', $line, $matches)) {
                $currentCookies = str_replace('Set-Cookie: ', '', $line);
            }
        }
//        dump($currentCookies);
        return $currentCookies;
    }

    //单独爬竞彩胜平负盘口
    public function spiderFootballByType(Request $request) {

        $type = $request->input("type", 0);
        $typeArray = [0 => self::k_type_wdl, 1 => self::k_type_asia_wdl, 2 => self::k_type_score, 3 => self::k_type_total_goal, 4 => self::k_type_half_wdl];

        $types = explode(",", $type);
        $typeStr = "";
        if (count($types) > 1) {
            foreach ($types as $type) {
                if (array_has($typeArray, $type)) {
                    $tempStr = $typeArray[$type];
                    if (isset($tempStr)) {
                        $typeStr .= '&poolcode[]=' . $typeArray[$type];
                    }
                }
            }
        } else {
            if (array_has($typeArray, $type) && isset($typeArray[$type])) {
                $typeStr = '&poolcode[]='.$typeArray[$type];
            }
        }
        if (is_null($typeStr) || strlen($typeStr) <= 0) {
            $types = [0];
            $typeStr = '&poolcode[]='.self::k_type_wdl;
        }

        $cookies = $this->getCookies();
        if (is_null($cookies)){
            echo 'spiderFootball no cookies </br>';
        }
        $url = 'http://i.sporttery.cn/odds_calculator/get_odds?i_format=json&i_callback='.$typeStr;
//        $url = 'http://i.sporttery.cn/odds_calculator/get_odds?i_format=json&poolcode=hhad';
        echo $url . '</br>';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, ['upgrade-insecure-requests:1']);
        //是否有cookies
        if ($cookies){
//            dump('bj '.$cookies);
            curl_setopt($ch, CURLOPT_COOKIE, $cookies);
        }
        $str = curl_exec($ch);

//        $meta = curl_getinfo($ch);
//        dump($meta);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
//            $header = substr($str, 0, $header_size);
        $body = substr($str, $header_size);

        curl_close($ch);

        //设置默认的cookies,防止死循环
        $currentCookies = '';
        if ($cookies == null) {
            $lines = explode("\n", $str);
            foreach ($lines as $line) {
                $line = trim($line);
//                dump($line);
                if (preg_match('#Set-Cookie: .*#', $line, $matches)) {
                    $currentCookies = str_replace('Set-Cookie: ', '', $line);
                }
            }
//            dump($currentCookies);
        }

        if ($body) {
            $jm = (array)json_decode($body, true);
        }
        else{
            echo 'error isport spider body '. '</br>';
            return;
        }
        if (is_null($jm))
        {
            echo 'error isport spider json null '. '</br>';
            return;
        }
        else if (count($jm) == 0){
            //有可能是body 转不了
            echo 'error isport spider json error'. '</br>';
//            $this->spiderFootball($request,$currentCookies);
            return;
        }

        $datas = $jm['data'];
        foreach ($datas as $key=>$data){
            $i_id = $this->getItem($data, 'id');
            $lfm = LotteryFootballMatch::query()->where('i_id',$i_id)->first();
            if (!isset($lfm)) {
                $lfm = $this->saveMatch($data);
            }
            if (isset($lfm)){
                if (in_array(0, $types)) {//胜平负
                    $this->saveHad($data,$lfm);
                }
                if (in_array(1, $types)) {//让球胜平负
                    $this->saveHhad($data,$lfm);
                }
                if (in_array(2, $types)) {//比分
                    $this->saveCrs($data,$lfm);
                }
                if (in_array(3, $types)) {//进球
                    $this->saveTtg($data,$lfm);
                }
                if (in_array(4, $types)) {//半全场
                    $this->saveHafu($data,$lfm);
                }
            }
        }
    }

    //爬中国足彩数据
    public function spiderFootball(Request $request)
    {
        $cookies = $this->getCookies();
        if (is_null($cookies)){
            echo 'spiderFootball no cookies </br>';
        }
        $url = 'http://i.sporttery.cn/odds_calculator/get_odds?i_format=json&i_callback=&poolcode[]=hhad&poolcode[]=had&poolcode[]=crs&poolcode[]=ttg&poolcode[]=hafu';
//        $url = 'http://i.sporttery.cn/odds_calculator/get_odds?i_format=json&poolcode=hhad';
        echo $url . '</br>';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, ['upgrade-insecure-requests:1']);
        //是否有cookies
        if ($cookies){
//            dump('bj '.$cookies);
            curl_setopt($ch, CURLOPT_COOKIE, $cookies);
        }
        $str = curl_exec($ch);

//        $meta = curl_getinfo($ch);
//        dump($meta);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
//            $header = substr($str, 0, $header_size);
        $body = substr($str, $header_size);

        curl_close($ch);

        //设置默认的cookies,防止死循环
        $currentCookies = '';
        if ($cookies == null) {
            $lines = explode("\n", $str);
            foreach ($lines as $line) {
                $line = trim($line);
//                dump($line);
                if (preg_match('#Set-Cookie: .*#', $line, $matches)) {
                    $currentCookies = str_replace('Set-Cookie: ', '', $line);
                }
            }
//            dump($currentCookies);
        }

        if ($body) {
            $jm = (array)json_decode($body, true);
        }
        else{
            echo 'error isport spider body '. '</br>';
            return;
        }
        if (is_null($jm))
        {
            echo 'error isport spider json null '. '</br>';
            return;
        }
        else if (count($jm) == 0){
            //有可能是body 转不了
            echo 'error isport spider json error'. '</br>';
//            $this->spiderFootball($request,$currentCookies);
            return;
        }

        $datas = $jm['data'];
        foreach ($datas as $key=>$data){
            $lfm = $this->saveMatch($data);
            if (isset($lfm)){
                //让球胜平负
                $this->saveHhad($data,$lfm);
                //胜平负
                $this->saveHad($data,$lfm);
                //比分
                $this->saveCrs($data,$lfm);
                //进球
                $this->saveTtg($data,$lfm);
                //半全场
                $this->saveHafu($data,$lfm);
            }
        }
    }

    //根据比赛id获取盘口信息
    public function spiderFootballOdds(Request $request) {
        $mid = $request->input("mid");
        if (!isset($mid)) {
            echo "spider mid is null";
            return;
        }
        $reset = $request->input("reset", false);
        $userData = $this->getUserData();
        $url = "http://i.sporttery.cn/open_v1_0/fb_match_list/get_fb_result_odds/?username=" . $userData['username'] . "&password=" . $userData['password'] . "&format=json&callback=&m_id=$mid";

        $response = $this->spiderTextFromUrl($url);
        $jsonResult = json_decode($response);

        if (isset($jsonResult) && isset($jsonResult->data)) {
            $data = $jsonResult->data;
            $lfm = $this->saveMatch($data, true);
            if (isset($lfm)){
                //让球胜平负
                if (($reset || (is_null($lfm->spider) || !($lfm->spider >> 1 & 1)))
                    && isset($data->hhad) && isset($data->hhad->h) && strlen($data->hhad->h) > 0) {
                    $this->saveHhad($data, $lfm, true);
                }
                //胜平负
                if (($reset || (is_null($lfm->spider) || !($lfm->spider >> 0 & 1)))
                    && isset($data->had) && isset($data->had->h) && strlen($data->had->h) > 0) {
                    $this->saveHad($data, $lfm, true);
                }
                //比分
                $crsStr = "0000";
                if (($reset || (is_null($lfm->spider) || !($lfm->spider >> 2 & 1)))
                    && isset($data->crs) && isset($data->crs->$crsStr) && strlen($data->crs->$crsStr) > 0) {
                    $this->saveCrs($data, $lfm, true);
                }
                //进球
                if (($reset || (is_null($lfm->spider) || !($lfm->spider >> 3 & 1)))
                    && isset($data->ttg) && isset($data->ttg->s0) && strlen($data->ttg->s0) > 0) {
                    $this->saveTtg($data, $lfm, true);
                }
                //半全场
                if (($reset || (is_null($lfm->spider) || !($lfm->spider >> 4 & 1)))
                    && isset($data->hafu) && isset($data->hafu->aa) && strlen($data->hafu->aa) > 0) {
                    $this->saveHafu($data, $lfm, true);
                }
            }
        }
    }

    //爬当天的比赛列表数据(比赛是全的，数据不全)
    public function spiderFootballSimpleMatches(Request $request) {
        $url = "http://i.sporttery.cn/api/fb_match_info/get_matches?f_callback=";

        $response = $this->spiderTextFromUrl($url);
        $jsonResult = json_decode($response);
        if (isset($jsonResult) && isset($jsonResult->result)){
            $matches = $jsonResult->result;
            foreach ($matches as $match) {
                $lfm = LotteryFootballMatch::where('i_id',$match->id)->first();
                if (!isset($lfm)) {
                    $lfm = new LotteryFootballMatch();
                    $lfm->i_id = $match->id;
                }
                $lfm->num = $match->num;
                $lfm->lname = $match->l_cn;
                $lfm->hname = $match->h_cn;
                $lfm->aname = $match->a_cn;
                $lfm->save();
            }
        }
    }

    //爬历史比赛数据
    public function spiderFootballMatchesByDate(Request $request)
    {
        $dateTime = date_create();
        if (date_format($dateTime, "H") < 10) {
            $dateTime = date_sub($dateTime, date_interval_create_from_date_string("1 days"));
        }
        $date = $request->input("date", date_format($dateTime, "Y-m-d"));

        $userData = $this->getUserData();
        $url = "http://i.sporttery.cn/open_v1_0/fb_match_list/get_fb_match_result/?username=" . $userData['username'] . "&password=" . $userData['password'] . "&format=json&callback=&date=$date";

        $response = $this->spiderTextFromUrl($url);
        $jsonResult = json_decode($response);
        if (isset($jsonResult) && isset($jsonResult->data) && isset($jsonResult->data->result)) {
            $matches = $jsonResult->data->result;
            foreach ($matches as $match) {
                $lfm = $this->saveMatch($match, true);
                //同时把盘口信息也爬取了(只有当比赛已结束了才去爬取)
                if ($lfm->spider < 31 && $lfm->status_int < 0) {
                    $request->merge(['mid'=>$lfm->i_id]);
                    $this->spiderFootballOdds($request);
                }
            }
        }
    }

    //半全场
    private function saveHafu($outData,$lfm, $isJson = false){
        $data = $this->getItem($outData, 'hafu', $isJson);
        if (is_null($data)){
            return;
        }
        $hhad = LotteryFootballHafu::where('lfm_id',$lfm->id)->first();
        if (is_null($hhad)){
            $hhad = new LotteryFootballHafu();
            $hhad->lfm_id = $lfm->id;
        }

        $hhad = $this->savePrivateItem($hhad, 'hafu', $lfm, $outData, $data, $isJson);

        $hhad->cbt = $this->getItem($data, 'cbt', $isJson, $this->getObjectItem($hhad,'cbt'));
        $hhad->int = $this->getItem($data, 'int', $isJson, $this->getObjectItem($hhad,'int'));
        $hhad->vbt = $this->getItem($data, 'vbt', $isJson, $this->getObjectItem($hhad,'vbt'));

        $hhad->lose_lose = $this->getItem($data, 'aa', $isJson, $this->getObjectItem($hhad,'lose_lose'));
        $hhad->lose_draw = $this->getItem($data, 'ad', $isJson, $this->getObjectItem($hhad,'lose_draw'));
        $hhad->lose_win = $this->getItem($data, 'ah', $isJson, $this->getObjectItem($hhad,'lose_win'));
        $hhad->win_win = $this->getItem($data, 'hh', $isJson, $this->getObjectItem($hhad,'win_win'));
        $hhad->win_lose = $this->getItem($data, 'ha', $isJson, $this->getObjectItem($hhad,'win_lose'));
        $hhad->win_draw = $this->getItem($data, 'hd', $isJson, $this->getObjectItem($hhad,'win_draw'));
        $hhad->draw_lose = $this->getItem($data, 'da', $isJson, $this->getObjectItem($hhad,'draw_lose'));
        $hhad->draw_draw = $this->getItem($data, 'dd', $isJson, $this->getObjectItem($hhad,'draw_draw'));
        $hhad->draw_win = $this->getItem($data, 'dh', $isJson, $this->getObjectItem($hhad,'draw_win'));

        $hhad->save();

        echo "saveHafu : i_id = $lfm->i_id <br>";
    }

    //进球
    private function saveTtg($outData,$lfm, $isJson = false){
        $data = $this->getItem($outData, 'ttg', $isJson);
        if (is_null($data)){
            return;
        }
        $hhad = LotteryFootballTtgs::where('lfm_id',$lfm->id)->first();
        if (is_null($hhad)){
            $hhad = new LotteryFootballTtgs();
            $hhad->lfm_id = $lfm->id;
        }

        $hhad = $this->savePrivateItem($hhad, 'ttg', $lfm, $outData, $data, $isJson);

        $hhad->cbt = $this->getItem($data, 'cbt', $isJson, $this->getObjectItem($hhad,'cbt'));
        $hhad->int = $this->getItem($data, 'int', $isJson, $this->getObjectItem($hhad,'int'));
        $hhad->vbt = $this->getItem($data, 'vbt', $isJson, $this->getObjectItem($hhad,'vbt'));
        $hhad->zero = $this->getItem($data, 's0', $isJson, $this->getObjectItem($hhad,'zero'));
        $hhad->one = $this->getItem($data, 's1', $isJson, $this->getObjectItem($hhad,'one'));
        $hhad->two = $this->getItem($data, 's2', $isJson, $this->getObjectItem($hhad,'two'));
        $hhad->three = $this->getItem($data, 's3', $isJson, $this->getObjectItem($hhad,'three'));
        $hhad->four = $this->getItem($data, 's4', $isJson, $this->getObjectItem($hhad,'four'));
        $hhad->five = $this->getItem($data, 's5', $isJson, $this->getObjectItem($hhad,'five'));
        $hhad->six = $this->getItem($data, 's6', $isJson, $this->getObjectItem($hhad,'six'));
        $hhad->seven = $this->getItem($data, 's7', $isJson, $this->getObjectItem($hhad,'seven'));
        $hhad->save();
        echo "saveTtg : i_id = $lfm->i_id <br>";
    }

    //比分
    private function saveCrs($outData,$lfm,$isJson=false){
        $data = $this->getItem($outData, 'crs', $isJson);
        if (is_null($data)){
            return;
        }
        $hhad = LotteryFootballCrs::where('lfm_id',$lfm->id)->first();
        if (is_null($hhad)){
            $hhad = new LotteryFootballCrs();
            $hhad->lfm_id = $lfm->id;
        }

        $hhad = $this->savePrivateItem($hhad, 'crs', $lfm, $outData, $data, $isJson);

        $hhad->cbt = $this->getItem($data, 'cbt', $isJson, $this->getObjectItem($hhad,'cbt'));
        $hhad->int = $this->getItem($data, 'int', $isJson, $this->getObjectItem($hhad,'int'));
        $hhad->vbt = $this->getItem($data, 'vbt', $isJson, $this->getObjectItem($hhad,'vbt'));
        //胜
        $hhad->one_zero = $this->getItem($data, '0100', $isJson, $this->getObjectItem($hhad,'one_zero'));
        $hhad->two_zero = $this->getItem($data, '0200', $isJson, $this->getObjectItem($hhad,'two_zero'));
        $hhad->three_zero = $this->getItem($data, '0300', $isJson, $this->getObjectItem($hhad,'three_zero'));
        $hhad->four_zero = $this->getItem($data, '0400', $isJson, $this->getObjectItem($hhad,'four_zero'));
        $hhad->five_zero = $this->getItem($data, '0500', $isJson, $this->getObjectItem($hhad,'five_zero'));
        $hhad->two_one = $this->getItem($data, '0201', $isJson, $this->getObjectItem($hhad,'two_one'));
        $hhad->three_one = $this->getItem($data, '0301', $isJson, $this->getObjectItem($hhad,'three_one'));
        $hhad->three_two = $this->getItem($data, '0302', $isJson, $this->getObjectItem($hhad,'three_two'));
        $hhad->four_one = $this->getItem($data, '0401', $isJson, $this->getObjectItem($hhad,'four_one'));
        $hhad->four_two = $this->getItem($data, '0402', $isJson, $this->getObjectItem($hhad,'four_two'));
        $hhad->five_one = $this->getItem($data, '0501', $isJson, $this->getObjectItem($hhad,'five_one'));
        $hhad->five_two = $this->getItem($data, '0502', $isJson, $this->getObjectItem($hhad,'five_two'));
        $hhad->win_other = $this->getItem($data, '-1-h', $isJson, $this->getObjectItem($hhad,'win_other'));
        //平
        $hhad->zero_zero = $this->getItem($data, '0000', $isJson, $this->getObjectItem($hhad,'zero_zero'));
        $hhad->one_one = $this->getItem($data, '0101', $isJson, $this->getObjectItem($hhad,'one_one'));
        $hhad->two_two = $this->getItem($data, '0202', $isJson, $this->getObjectItem($hhad,'two_two'));
        $hhad->three_three = $this->getItem($data, '0303', $isJson, $this->getObjectItem($hhad,'three_three'));
        $hhad->draw_other = $this->getItem($data, '-1-d', $isJson, $this->getObjectItem($hhad,'draw_other'));
        //负
        $hhad->zero_one = $this->getItem($data, '0001', $isJson, $this->getObjectItem($hhad,'zero_one'));
        $hhad->zero_two = $this->getItem($data, '0002', $isJson, $this->getObjectItem($hhad,'zero_two'));
        $hhad->zero_three = $this->getItem($data, '0003', $isJson, $this->getObjectItem($hhad,'zero_three'));
        $hhad->zero_four = $this->getItem($data, '0004', $isJson, $this->getObjectItem($hhad,'zero_four'));
        $hhad->zero_five = $this->getItem($data, '0005', $isJson, $this->getObjectItem($hhad,'zero_five'));
        $hhad->one_two = $this->getItem($data, '0102', $isJson, $this->getObjectItem($hhad,'one_two'));
        $hhad->one_three = $this->getItem($data, '0103', $isJson, $this->getObjectItem($hhad,'one_three'));
        $hhad->two_three = $this->getItem($data, '0203', $isJson, $this->getObjectItem($hhad,'two_three'));
        $hhad->one_four = $this->getItem($data, '0104', $isJson, $this->getObjectItem($hhad,'one_four'));
        $hhad->two_four = $this->getItem($data, '0204', $isJson, $this->getObjectItem($hhad,'two_four'));
        $hhad->one_five = $this->getItem($data, '0105', $isJson, $this->getObjectItem($hhad,'one_five'));
        $hhad->two_five = $this->getItem($data, '0205', $isJson, $this->getObjectItem($hhad,'two_five'));
        $hhad->lose_other = $this->getItem($data, '-1-a', $isJson, $this->getObjectItem($hhad,'lose_other'));

        $hhad->save();
        echo "saveCrs : i_id = $lfm->i_id <br>";
    }

    //胜平负
    private function saveHad($outData,$lfm,$isJson=false){
        $data = $this->getItem($outData, 'had', $isJson);
        if (is_null($data)){
            return;
        }
        $hhad = LotteryFootballHad::where('lfm_id',$lfm->id)->first();
        if (is_null($hhad)){
            $hhad = new LotteryFootballHad();
            $hhad->lfm_id = $lfm->id;
        }

        $hhad = $this->savePrivateItem($hhad, 'had', $lfm, $outData, $data, $isJson);

        $hhad->cbt = $this->getItem($data, 'cbt', $isJson, $this->getObjectItem($hhad,'cbt'));
        $hhad->int = $this->getItem($data, 'int', $isJson, $this->getObjectItem($hhad,'int'));
        $hhad->vbt = $this->getItem($data, 'vbt', $isJson, $this->getObjectItem($hhad,'vbt'));
        $hhad->h_odd = $this->getItem($data, 'h', $isJson, $this->getObjectItem($hhad,'h_odd'));
        $hhad->a_odd = $this->getItem($data, 'a', $isJson, $this->getObjectItem($hhad,'a_odd'));
        $hhad->d_odd = $this->getItem($data, 'd', $isJson, $this->getObjectItem($hhad,'d_odd'));
        $hhad->h_trend = $this->getItem($data, 'h_trend', $isJson, $this->getObjectItem($hhad,'h_trend'));
        $hhad->a_trend = $this->getItem($data, 'a_trend', $isJson, $this->getObjectItem($hhad,'a_trend'));
        $hhad->d_trend = $this->getItem($data, 'd_trend', $isJson, $this->getObjectItem($hhad,'d_trend'));
        $hhad->save();

        echo "saveHad : i_id = $lfm->i_id <br>";
    }

    //让球胜平负
    private function saveHhad($outData,$lfm,$isJson=false){
        $data = $this->getItem($outData, 'hhad', $isJson);
        if (is_null($data)){
            return;
        }
        $hhad = LotteryFootballHhad::where('lfm_id',$lfm->id)->first();
        if (is_null($hhad)){
            $hhad = new LotteryFootballHhad();
            $hhad->lfm_id = $lfm->id;
        }
        $hhad = $this->savePrivateItem($hhad, 'hhad', $lfm, $outData, $data, $isJson);

        $hhad->cbt = $this->getItem($data, 'cbt', $isJson, $this->getObjectItem($hhad,'cbt'));
        $hhad->int = $this->getItem($data, 'int', $isJson, $this->getObjectItem($hhad,'int'));
        $hhad->vbt = $this->getItem($data, 'vbt', $isJson, $this->getObjectItem($hhad,'vbt'));
        $hhad->h_odd = $this->getItem($data, 'h', $isJson, $this->getObjectItem($hhad,'h_odd'));
        $hhad->a_odd = $this->getItem($data, 'a', $isJson, $this->getObjectItem($hhad,'a_odd'));
        $hhad->d_odd = $this->getItem($data, 'd', $isJson, $this->getObjectItem($hhad,'d_odd'));
        $fixedOdds = $this->getItem($data, 'fixedodds', $isJson, $this->getObjectItem($hhad,'fixed_odds'));
        if (is_null($fixedOdds)) {
            $fixedOdds = $this->getItem($outData, 'goalline', $isJson);
        }
        $hhad->fixed_odds = $fixedOdds;
        $hhad->h_trend = $this->getItem($data, 'h_trend', $isJson, $this->getObjectItem($hhad,'h_trend'));
        $hhad->a_trend = $this->getItem($data, 'a_trend', $isJson, $this->getObjectItem($hhad,'a_trend'));
        $hhad->d_trend = $this->getItem($data, 'd_trend', $isJson, $this->getObjectItem($hhad,'d_trend'));
        $hhad->save();

        echo "saveHhad : i_id = $lfm->i_id <br>";
    }

    //足彩比赛信息
    private function saveMatch($data,$isJson=false){
        $i_id = $this->getItem($data, 'id', $isJson);
        if (is_null($i_id)) {
            echo "save match data id is null";
            return null;
        }
        $lfm = LotteryFootballMatch::where('i_id',$i_id)->first();
        if (is_null($lfm)){
            $lfm = new LotteryFootballMatch();
            $lfm->i_id = $i_id;
        }
        //匹配比赛id
        $dateStr = $this->getItem($data, 'date', $isJson);
        if (is_null($dateStr)) {
            $time = $this->getObjectItem($lfm,'time');
        } else {
            $time = $this->getItem($data, 'date', $isJson).' '.$this->getItem($data, 'time', $isJson);
        }
        $num = $this->getItem($data, 'num', $isJson, $this->getObjectItem($lfm,'week').$this->getObjectItem($lfm,'num'));
        if (is_null($lfm->mid)){
            $start = date_add(date_create($time),date_interval_create_from_date_string('-1 hour'));
            $end = date_add(date_create($time),date_interval_create_from_date_string('1 hour'));
            $match = Match::where('time','>=',$start)
                ->where('time','<=',$end)
                ->where('betting_num',$num)
                ->first();
            if (isset($match)){
                $lfm->mid = $match->id;
            }else{
//                dump($data);
                echo $time. ' ' . $num . ' 找不到对应'.'</br>';
            }
        }
        $lfm->week = substr($num, 0, -3);
        $lfm->num = substr($num, -3);
        $lfm->time = $time;
        $lfm->hname = $this->getItem($data, 'h_cn', $isJson, $this->getObjectItem($lfm,'hname'));
        $lfm->aname = $this->getItem($data, 'a_cn', $isJson, $this->getObjectItem($lfm,'aname'));
        $lfm->lname = $this->getItem($data, 'l_cn', $isJson, $this->getObjectItem($lfm,'lname'));
        $lfm->hname_abbr = $this->getItem($data, 'h_cn_abbr', $isJson, $this->getObjectItem($lfm,'hname_abbr'));
        $lfm->aname_abbr = $this->getItem($data, 'a_cn_abbr', $isJson, $this->getObjectItem($lfm,'aname_abbr'));
        $lfm->lname_abbr = $this->getItem($data, 'l_cn_abbr', $isJson, $this->getObjectItem($lfm,'lname_abbr'));

        $order = $this->getItem($data, 'h_order', $isJson);
        if (isset($order)) {
            $order = str_replace('[', '', $order);
            $order = str_replace(']', '', $order);
            $lfm->h_order = $order;
            $order = $this->getObjectItem($data,'a_order');
            $order = str_replace('[', '', $order);
            $order = str_replace(']', '', $order);
            $lfm->a_order = $order;
        }
        $blockingTime = $this->getItem($data, 'b_date', $isJson, $this->getObjectItem($lfm,'blocking_time'));
        if (is_null($blockingTime)) {
            $date = date_create($dateStr);
            if (date('H', strtotime($time)) <= 10) {
                $date = date_sub($date, date_interval_create_from_date_string("1 days"));
            }
            $blockingTime = date_format($date, 'Y-m-d');
        }
        $lfm->blocking_time = $blockingTime;

        $status = $this->getItem($data, 'status', $isJson, $this->getObjectItem($lfm,'status'));
        $lfm->status = $status;
        $lfm->status_int = $this->convertStatusToInt($status, $this->getObjectItem($lfm,'status'));

        $lfm->background_color = $this->getItem($data, 'l_background_color', $isJson, $this->getObjectItem($lfm,'background_color'));

        $lfm->weather = $this->getItem($data, 'weather', $isJson, $this->getObjectItem($lfm,'weather'));
        $cityString = $this->getItem($data, 'weather_city', $isJson);
        if (isset($cityString)) {
            $city = explode('|', $cityString)[0];
            $lfm->city = $city;
        }
        $temperature = $this->getItem($data, 'temperature', $isJson);
        if (isset($temperature)) {
            $temperature = str_replace('&deg;', '', $temperature);
            $lfm->temperature = $temperature;
        }

        //比分
        $score = $this->getItem($data, 'final', $isJson);
        if (isset($score)) {
            $scores = explode(":", $score);
            if (count($scores) == 2) {
                $lfm->hscore = $scores[0];
                $lfm->ascore = $scores[1];
            }
        }
        $halfScore = $this->getItem($data, 'half', $isJson);
        if (isset($halfScore)) {
            $halfScores = explode(":", $halfScore);
            if (count($halfScores) == 2) {
                $lfm->hscorehalf = $halfScores[0];
                $lfm->ascorehalf = $halfScores[1];
            }
        }
        //状态
        $lfm->match_status = $this->getItem($data, 'match_status', $isJson, $this->getObjectItem($lfm,'match_status'));
        $lfm->result_status = $this->getItem($data, 'result_status', $isJson, $this->getObjectItem($lfm,'result_status'));
        $lfm->pool_status = $this->getItem($data, 'pool_status', $isJson, $this->getObjectItem($lfm,'pool_status'));

        $lfm->save();
        return $lfm;
    }

    private function getObjectItem($object, $key) {
        if ($object[$key]) {
            return $object[$key];
        }
        return null;
    }

    //单独处理single、i_id、result的保存
    private function savePrivateItem($hhad, $key, $lfm, $outData, $data, $isJson) {
        $result = $this->getItem($data, 'result', $isJson, $this->getObjectItem($hhad,'result'));
        if (is_null($result)) {
            $result = $this->getItem($outData, $key."_result", $isJson);
        }
        $hhad->result = $result;
        if (strlen($result) > 0 || $lfm->status_int < -1) {
            switch ($key) {
                case self::k_type_wdl:
                    $lfm->spider = $lfm->spider | 1 << 0;
                    break;
                case self::k_type_asia_wdl:
                    $lfm->spider = $lfm->spider | 1 << 1;
                    break;
                case self::k_type_score:
                    $lfm->spider = $lfm->spider | 1 << 2;
                    break;
                case self::k_type_total_goal:
                    $lfm->spider = $lfm->spider | 1 << 3;
                    break;
                case self::k_type_half_wdl:
                    $lfm->spider = $lfm->spider | 1 << 4;
                    break;
            }
            $lfm->save();
        }
        $status = $this->getItem($data, 'p_status', $isJson);
        if (is_null($status)) {
            $status = $lfm->status;
        }
        $hhad->status = $status;
        $hhad->i_id = $lfm->i_id;
//        $hhad->i_id = $this->getItem($data, 'p_id', $isJson, $this->getObjectItem($hhad,'i_id'));
        $single = $this->getItem($data, 'single', $isJson, $this->getObjectItem($hhad,'single'));
        if (is_null($single)) {
            $single = $this->getItem($outData, $key.'_single', $isJson);
        }
        $hhad->single = $single;

        return $hhad;
    }

    //转换状态
    private function convertStatusToInt($status, $status_int = 0) {
        switch ($status) {
            case "已完成":
                $status_int = -1;
                break;
            case "进行中":
                $status_int = 1;
                break;
            case "取消":
                $status_int = -14;
                break;
            default:
            case "Selling":
            $status_int = 0;
                break;
        }
        return $status_int;
    }

    //刷新当前页面
    private function refreshPage($lastDate, $nextDate)
    {

    }
}