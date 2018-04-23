<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 17/5/8
 * Time: 下午8:00
 */
namespace App\Http\Controllers\App\Model;

class AppCommonResponse{
    public $code = 0;
    public $message = '';
    public $data = null;
    public $cookies = null;
    public $pageAble = true;

    public static function createAppCommonResponse($code=0,$msg='',$data='',$pageAble = true,$cookies = null){
        $result = new AppCommonResponse();
        $result->code = $code;
        $result->pageAble = $pageAble;
        $result->message = $msg;
        if ($pageAble && $data != '' && is_array($data))
        {
            $dataSafe = array();
            $keys = array_keys($data);
            if (count($keys) > 0 && $keys[0] == 1)
            {
                foreach ($data as $tmp){
                    if (!is_null($tmp))
                        $dataSafe[] = $tmp;
                }
            }
            else{
                $dataSafe = $data;
            }
        }
        else{
            $dataSafe = $data;
        }
        $result->data = $dataSafe;
        $result->cookies = $cookies;
        return $result;
    }
}