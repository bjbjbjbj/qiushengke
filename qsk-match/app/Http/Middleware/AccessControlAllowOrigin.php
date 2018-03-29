<?php
/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/3/29
 * Time: 18:07
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AccessControlAllowOrigin
{
    /**
     *
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $response->header('Access-Control-Allow-Origin', env('WWW_URL'));
        if (starts_with(env('CMS_URL'), "https://")) {
            $response->header('Access-Control-Allow-Origin', str_replace("https://", "http://", env('WWW_URL')));
        } else {
            $response->header('Access-Control-Allow-Origin', str_replace("http://", "https://", env('WWW_URL')));
        }
        $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, Accept');
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS');
        $response->header('Access-Control-Allow-Credentials', 'true');

        return $next($request);
    }

}
