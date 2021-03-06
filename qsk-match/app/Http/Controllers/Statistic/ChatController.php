<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/3/19
 * Time: 下午12:25
 */

namespace App\Http\Controllers\Statistic;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;
use GatewayClient\Gateway;

class ChatController extends BaseController{
    public function __construct()
    {
        Gateway::$registerAddress = '172.18.233.205:1238';
//        Gateway::$registerAddress = '127.0.0.1:1238';
    }
    public function postChat(Request $request){

        $roomId = $request->input('roomId');
        $data = array();
        $data['icon'] = $request->input('icon');
        $data['id'] = $request->input('id');
        $data['nickname'] = $request->input('nickname');
        $data['content'] = $request->input('content');
        $data['type'] = $request->input('type');
        Gateway::sendToGroup([$roomId],json_encode($data));
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:7172/1/push/room?rid=".$roomId);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch, CURLOPT_POST, 1);
//        curl_setopt($ch, CURLOPT_POSTFIELDS , json_encode($data));
//        $output = curl_exec($ch);
//        curl_close($ch);
//        return response()->json(array('code'=>'0','message'=>$output));
    }
}
