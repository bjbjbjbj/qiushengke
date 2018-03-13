<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/10
 * Time: 11:52
 */

namespace App\Models\QSK;


use Illuminate\Database\Eloquent\Model;

class AdAccount extends Model
{
    const QSK_ADMIN_AUTH_SESSION_KEY = "QSK_ADMIN_AUTH_SESSION_KEY";
    const QSK_ADMIN_AUTH_TOKEN_KEY = "QSK_ADMIN_AUTH_TOKEN_KEY";
    const kStatusValid = 1;//有效用户
    const kStatusUnValid = -1;//无效用户


    protected $connection = 'qsk';

    public static function shaPassword($salt, $password)
    {
        return sha1($salt . $password);
    }

    public static function generateToken()
    {
        $token = uniqid('qsk-', true);
        $account = AdAccount::query()->where("token", $token)->first();
        if (empty($account)) {
            return $token;
        } else {
            return self::generateToken();
        }
    }

    /**
     * 用户拥有的角色
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles() {
        //belongsToMany($related, $table = null, $foreignKey = null, $relatedKey = null, $relation = null)
        return $this->belongsToMany(AdRole::class, 'ad_role_accounts', 'account_id', 'role_id');
    }

    /**
     * 用户是否拥有权限
     * @param $role_id
     * @return bool
     */
    public function hasRole($role_id) {
        $roles = $this->roles;
        if (isset($roles) && count($roles) > 0) {
            foreach ($roles as $role) {
                if ($role_id == $role->id) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 是否有权限访问。
     * @param $action
     * @return bool
     */
    public function hasAccess($action) {
        $query = AdRoleAccount::query();
        $query->join('ad_role_resources', function ($join) {
            $join->on('ad_role_resources.ro_id', '=', 'ad_role_accounts.role_id');
        });
        $query->join('ad_resources', function ($join) {
            $join->on('ad_role_resources.re_id', '=', 'ad_resources.id');
        });
        $query->where('ad_role_accounts.account_id', $this->id);
        $query->where('ad_resources.action', $action);
        return $query->count() > 0;
    }

    public function firstMenus() {
        //获取用户所有的一级目录
        $query = AdResource::query();
        $query->where('ad_resources.is_menu', AdResource::IS_MENU);
        $query->where('ad_resources.menu_level', AdResource::FIRST_LEVEL);
        if ($this->id != 1) {//不是超级管理员
            $query->join('ad_role_resources', function ($join) {
                $join->on('ad_resources.id', '=', 'ad_role_resources.re_id');
            });
            $query->join('ad_role_accounts', function ($join) {
                $join->on('ad_role_resources.ro_id', '=', 'ad_role_accounts.role_id');
            });
            $query->where('ad_role_accounts.account_id', $this->id);
        }
        $query->orderBy("ad_resources.od")->orderBy("ad_resources.id");
        $query->select("ad_resources.*");
        return $query->get();
    }

}