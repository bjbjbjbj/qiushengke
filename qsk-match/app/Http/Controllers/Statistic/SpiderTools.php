<?php
/**
 * Created by PhpStorm.
 * User: maozhijun
 * Date: 16/9/12
 * Time: 23:55
 */

namespace App\Http\Controllers\Statistic;


use App\Models\WinModels\SpiderLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

trait SpiderTools
{


    private function spiderTextFromUrl($url)
    {
//        echo "=========spider url========<br>$url<br>";
        $str = "";
//        $count = $this->countErrorLog($url);
//        if ($count >= static::SPIDER_ERROR_LIMIT){
//            echo "Error: Unable to open url:{$url}<br> without limit:".static::SPIDER_ERROR_LIMIT;
//            return;
//        }
        try {
//            ini_set('user_agent', \GuzzleHttp\default_user_agent());
//            $handle = @fopen($url, "r");
//            if ($handle) {
//                while (($buffer = fgets($handle, 4096)) !== false) {
//                    $str .= $buffer;
//                }
//                if (!feof($handle)) {
////                    echo "Error: unexpected fgets() fail<br>";
//                }
//                fclose($handle);
//            } else {
////                echo "Error: Unable to open url:{$url}<br>";
//                $this->addErrorLog($url);
//            }
            $str = $this->spiderTextFromUrlByWin007($url, true);
        } catch (\Exception $e) {
            $this->addErrorLog($url);
        }
        return $str;
    }

    private function addErrorLog($url){
        $log = SpiderLog::find($url);
        if (!isset($log)){
            $log = new SpiderLog();
            $log->url = $url;
            $log->count = 1;
        }else{
            $log->count += 1;
        }
        $log->save();

        //24小时相同url只收一次
        if (strlen(env('ERROR_MAIL','')) > 0) {
            $key = 'spider_error_' . md5($url);
            if (!Redis::exists($key)) {
                Redis::set($key, '1');
                Redis::expire($key, 60 * 60 * 24);
                $mails = env('ERROR_MAIL', '');
                $mails = explode(',',$mails);
                //发送爬虫错误邮件
                foreach ($mails as $mail){
                    Mail::raw(date_format(date_create(),'Y-m-d H:i') . ' 的时候 ' . $url . ' 出错了', function ($message) use($mail) {
                        //暂时只发给自己先
                        $message->to($mail, '开发者')
                            ->subject('料狗爬虫出错了！');
                    });
                }
            }
        }
    }

    private function countErrorLog($url){
        $log = SpiderLog::find($url);
        if (!isset($log)){
            return 0;
        }else{
            return $log->count;
        }
    }

    /**
     * 根据post请求爬取html
     */
    public function spiderTextFromUrlByPost($url, $data)
    {
//        echo "=========spider url========<br>$url<br>";

        $html = "";
        try {
            //生成url-encode后的请求字符串，将数组转换为字符串
            $data = http_build_query($data);
            $opts = array('http' => array('method' => 'POST',
                'header' => "Content-type: application/x-www-form-urlencoded\r\n"
                    . "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.98 Safari/537.36\r\n"
                    . "Content-Length: " . strlen($data) . "\r\n",
                'content' => $data));
            //生成请求的句柄文件
            $context = stream_context_create($opts);
            $html = file_get_contents($url, false, $context);
        } catch (\Exception $e) {

        }
        return $html;
    }

    /**
     * 专门用来爬取球探网页数据
     */
    public function spiderTextFromUrlByWin007($url, $shouldUtf8 = false, $referee = 'http://live.titan007.com/') {
//        echo "url = ".$url."</br>";

        $content = '';
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            if (substr($url, 0, 5) == 'https') {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
            curl_setopt($ch, CURLINFO_CONTENT_TYPE, 'text/html; charset=utf-8');
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.98 Safari/537.36");
//        curl_setopt($ch, CURLOPT_USERAGENT, "WSMobile/1.5.1 (iPad; iOS 10.2; Scale/2.00)");
            //这个必须加上去，否则请求会404的
            curl_setopt($ch, CURLOPT_REFERER, $referee);
            curl_setopt($ch, CURLOPT_COOKIESESSION, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            $response = curl_exec($ch);
            if ($error = curl_error($ch)) {
                die($error);
            }
            curl_close($ch);

            list($head, $content) = explode("\r\n\r\n", $response, 2);
            //加上这行，可以解决中文乱码问题
            if ($shouldUtf8) {
                $content = mb_convert_encoding($content, 'utf-8', 'utf-8,GBK,UTF-8,ASCII');
            }
        } catch (\Exception $e) {

        }
        return $content;
    }
}