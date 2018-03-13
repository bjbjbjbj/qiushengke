<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/3
 * Time: 17:16
 */
namespace App\Http\Middleware;

use App\Models\QSK\AdAccount;
use Closure;
use Illuminate\Http\Request;

class AdminAuthVerify
{

    public function handle(Request $request, Closure $next)
    {
        if ($this->hasAuth($request)) {
            $account = $request->_account;
            if (isset($account)) {
                if ($account->id != 1) {
                    $url = $request->url();
                    $start = stripos($url, '/admin');
                    $action = substr($url, $start);
                    if ($action != "/admin" && $action != "/admin/") {//首页每个角色都应该可以访问。
                        //答题串关，有主页权限则拥有所有答题串关的权限
                        $hasAccess = $account->hasAccess($action);
                        if (!$hasAccess) {
                            $method = $request->method();
                            if (strtolower($method) == 'post') {
                                return response()->json(["code"=>403, "msg"=>"没有权限"]);
                            } else {
                                return redirect('/admin/no_role.html');
                            }
                        }
                    }
                }
            }
            return $next($request);
        } else {
            return redirect('/admin/sign.html/?target=' . urlencode(request()->fullUrl()));
        }
    }

    /**
     * 判断是登录
     * @param Request $request
     * @return bool
     */
    protected function hasAuth(Request $request)
    {
        $account = session(AdAccount::QSK_ADMIN_AUTH_SESSION_KEY);
        if (isset($account)) {
            $request->_account = $account;
            return true;
        }

        if ($request->has(AdAccount::QSK_ADMIN_AUTH_TOKEN_KEY)) {
            $token = $request->input(AdAccount::QSK_ADMIN_AUTH_TOKEN_KEY);
        } else {
            $token = $request->cookie(AdAccount::QSK_ADMIN_AUTH_TOKEN_KEY);
        }
        if (isset($token)) {
            $account = AdAccount::query()->where('token', $token)->first();
            if (isset($account) && $account->status == AdAccount::kStatusValid) {
                if (strtotime($account->expired_at) > strtotime('now')) {
                    session([AdAccount::QSK_ADMIN_AUTH_SESSION_KEY => $account]);
                    $request->_account = $account;
                    return true;
                }
            }
        }
        return false;
    }

}