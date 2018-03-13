<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/10
 * Time: 11:50
 */

namespace App\Http\Controllers\Admin\Auth;


use App\Models\QSK\AdAccount;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{

    /**
     * 登陆页面、登陆逻辑
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function sign(Request $request) {
        $method = $request->getMethod();
        $target = $request->input("target", '/admin');

        if (strtolower($method) == "get") {//跳转到登录页面
            return view('admin.auth.sign', ['target'=>$target]);
        }

        $account = $request->input("account", '');
        $password = $request->input("password");
        $remember = $request->input("remember", 0);

        $returnInput = ['account'=>$account];

        $account = AdAccount::query()->where("account", $account)->first();
        if (!isset($account)) {
            return back()->withInput($returnInput)->with(["error" => "账户或密码错误"]);
        }

        $salt = $account->salt;
        $pw = $account->password;
        //判断是否登录
        if ($pw != AdAccount::shaPassword($salt, $password)) {
            return back()->withInput($returnInput)->with(["error" => "账户或密码错误"]);
        }

        $token = AdAccount::generateToken();
        $account->token = $token;
        if ($remember == 1) {
            $account->expired_at = date_create('7 day');
        } else {
            $account->expired_at = date_create('30 min');
        }

        if ($account->save()) {
            session([AdAccount::QSK_ADMIN_AUTH_SESSION_KEY => $account]);//登录信息保存在session
            if ($remember == 1) {
                $c = cookie(AdAccount::QSK_ADMIN_AUTH_TOKEN_KEY, $token, 60 * 24 * 7, '/', null, false, true);
                return response()->redirectTo($target)->withCookies([$c]);
            } else {
                return response()->redirectTo($target);
            }
        }
        return back()->withInput($returnInput)->with(["error" => "账户或密码错误"]);
    }

    /**
     * 退出登陆
     * @return $this
     */
    public function logout() {
        session([AdAccount::QSK_ADMIN_AUTH_TOKEN_KEY=>null]);
        $c = cookie(AdAccount::QSK_ADMIN_AUTH_TOKEN_KEY, '', 1, '/', null, false, true);
        return response()->redirectTo('/admin/sign.html')->withCookies([$c]);
    }

}