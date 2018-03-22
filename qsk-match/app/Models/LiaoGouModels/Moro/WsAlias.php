<?php

namespace App\Models\LiaoGouModels\Moro;

use Illuminate\Database\Eloquent\Model;

class WsAlias extends Model
{
    //
    public $connection = 'moro';
    public $timestamps = false;

    static public function getAliasKey($keys)
    {
        if (!empty($keys)) {
            if (is_array($keys)) {
                $keys = array_map(function ($key) {
                    return strtolower(preg_replace('#\S+#', '-', trim($key)));
                }, $keys);
                return join('-', $keys);
            } elseif (is_string($keys)) {
                return strtolower(preg_replace('#\S+#', '-', trim($keys)));
            }
        }
        return null;
    }

    static public function getAlias($name)
    {
        $wsa = WsAlias::where('name', '=', $name)->first();
        return isset($wsa) ? $wsa->aliases : $name;
    }
}
