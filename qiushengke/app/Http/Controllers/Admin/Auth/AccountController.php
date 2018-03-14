<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/10
 * Time: 16:52
 */

namespace App\Http\Controllers\Admin\Auth;


use App\Models\QSK\AdAccount;
use App\Models\QSK\AdRole;
use App\Models\QSK\AdRoleAccount;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{

    /**
     * 用户列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function accounts(Request $request) {
        $pageSize = $request->input('page_size', 20);
        $query = AdAccount::query()->orderBy('status', 'desc')->orderBy('id', 'desc');
        $page = $query->paginate($pageSize);
        $roles = AdRole::query()->get();
        return view('admin.account.list', ['page'=>$page, 'roles'=>$roles]);
    }

    public function saveAccount(Request $request) {
        $roles = $request->input("roles");//角色id 格式：id1,id2,id3,...,idn
        $id = $request->input('id');
        $account = $request->input('account');
        $email = $request->input('email');
        $name = $request->input('name');
        $status = $request->input('status');
        $password = $request->input('password');

        $role_array = [];
        if (!empty($roles)) {
            $role_array = explode(",", $roles);
        }

        //判断参数 开始
        if (empty($account)) {
            return back()->with('error', '用户名不能为空');
        }
        if (empty($name)) {
            return back()->with('error', '昵称不能为空');
        }
        if (!in_array($status, [AdAccount::kStatusValid, AdAccount::kStatusUnValid])) {
            return back()->with('error', '状态错误');
        }
        $validator = Validator::make($request->all(), ['email' => 'email']);//验证邮箱
        if ($validator->fails()) {
            return back()->with('error', '邮箱格式错误');
        }
        if (empty($password)) {
            return back()->with('error', '密码不能为空');
        }

        if (is_numeric($id)) {
            $adAccount = AdAccount::query()->find($id);
        }
        $isNew = !isset($adAccount);
        //判断参数 结束
        if ($isNew) {
            $adAccount = new AdAccount();
        }

        $adAccount->account = $account;
        $adAccount->email = $email;
        $adAccount->name = $name;
        $adAccount->status = $status;

        if (!$isNew) {
            if ($password != '******') {
                $password = sha1($request->input('password', ''));
                $salt = $adAccount->salt;
                $adAccount->password = sha1($salt . $password);
            }
        } else {
            $hasAccount = AdAccount::query()->where('account', $request->input('account', ''))->first();
            if (isset($hasAccount)) {
                return back()->with(['error' => '账号名已经存在']);
            }
            $hasEmail = AdAccount::query()->where('email', $request->input('email', ''))->first();
            if (isset($hasEmail)) {
                return back()->with(['error' => '邮箱已经存在']);
            }
            $password = sha1($password);
            $salt = uniqid('mm:', true);
            $adAccount->salt = $salt;
            $adAccount->password = sha1($salt . $password);
        }

        $exception = DB::transaction(function () use ($adAccount, $role_array) {
            $adAccount->save();
            $account_id = $adAccount->id;
            AdRoleAccount::query()->where("account_id", $account_id)->delete();//删除原来角色
            if (isset($role_array) && count($role_array) > 0) {
                foreach ($role_array as $role_id) {
                    $ra = new AdRoleAccount();
                    $ra->role_id = $role_id;
                    $ra->account_id = $account_id;
                    $ra->save();
                }
            }
        });

        $msg = $isNew ? '新建用户' : '更新用户';
        if (isset($exception)) {
            return back()->with(['error' => $msg . '失败']);
        }
        return back()->with(['success' => $msg . '成功']);
    }

}