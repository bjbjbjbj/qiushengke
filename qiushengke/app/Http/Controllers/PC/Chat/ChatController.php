<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/3/19
 * Time: 下午12:25
 */

namespace App\Http\Controllers\PC\Chat;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class ChatController extends BaseController{
    public function getChat(Request $request,$first,$second,$mid){
        if ($mid < 1000){
            return array('code'=>-1,'message'=>'mid不能为空');
        }
        if (substr($mid,0,2) != $first || substr($mid,2,2) != $second){
            return array('code'=>-1,'message'=>'mid错误');
        }
        $key = 'chat_'. $mid;
        $datas = Redis::get($key);
        return $datas;
    }

    public function postChat(Request $request){
        $mid = $request->input('mid',0);
        $message = $request->input('message','');
        $user = $request->input('user','');
        $sport = $request->input('sport',0);
        if (($sport != 1 && $sport != 2) || $mid <= 0 || strlen($message) == 0 || strlen($user) == 0){
            return array('code'=>-1,'message'=>'不能为空');
        }
        //保存信息
        //总量
        $allKey = 'chat_'. $mid;
        //增量
        $key = 't_chat_'. $mid;

        $allDatas = Redis::get($allKey);
        if (is_null($allDatas)){
            $allDatas = array();
        }
        else{
            $allDatas = json_decode($allDatas, true);
        }
        $datas = Redis::get($key);
        if (is_null($datas)){
            $datas = array();
        }
        else{
            $datas = json_decode($datas, true);
        }
        $datas[] = array('user'=>$user,'time'=>date_create()->getTimestamp(),'content'=>$message);
        $allDatas[] = array('user'=>$user,'time'=>date_create()->getTimestamp(),'content'=>$message);
        Redis::setEx($key, 60, json_encode($datas));
        Redis::setEx($allKey, 24 * 60 * 60, json_encode($allDatas));
        try {
            //大数据
            Storage::disk("public")->put("/chat/json/$sport/" . substr($mid, 0, 2) . '/' . substr($mid, 2, 2) . '/' . $mid . ".json", json_encode($allDatas));
            //增量
            Storage::disk("public")->put("/chat/json/$sport/" . substr($mid, 0, 2) . '/' . substr($mid, 2, 2) . '/' . $mid . "_t.json", json_encode($datas));
        }
        catch (\Exception $e){
            return array('code'=>-1,'message'=>'保存失败');
        }
        return array('code'=>0,'message'=>'success');
    }
}
