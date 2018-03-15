<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/14
 * Time: 15:20
 */

namespace App\Models\QSK\Anchor;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class LivePlatform extends Model
{
    const kStatusShow = 1;//有效的直播平台
    const kStatusHide = 2;//无效的直播平台

    protected $connection = 'qsk';

    /**
     * 所有显示的平台
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function platforms() {
        return self::query()->where('status', self::kStatusShow)->get();
    }

}