<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/13
 * Time: 16:21
 */

namespace App\Models\QSK;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Link extends Model
{
    const kShow = 1, kHide = 2;


    public static function getLinks() {
        $query = self::query()->where('show', self::kShow);
        $query->selectRaw('name, url');
        $query->orderBy(DB::raw('ifNull(od, 999)'));
        $links = $query->get()->toArray();
        return $links;
    }

}