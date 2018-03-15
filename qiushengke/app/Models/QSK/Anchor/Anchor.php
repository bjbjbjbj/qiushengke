<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/3/14
 * Time: 12:50
 */

namespace App\Models\QSK\Anchor;


use Illuminate\Database\Eloquent\Model;

/**
 * 主播实体类
 * Class Anchor
 * @package App\Models\QSK\Anchor
 */
class Anchor extends Model
{
    const kStatusValid = 1;//有效主播
    const kStatusUnValid = -1;//无效主播
    const icon_disk = 'icon';

    protected $connection = 'qsk';

    /**
     * 获取主播的直播间
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rooms() {
        return $this->hasMany(AnchorRoom::class, 'anchor_id', 'id')->where('status', AnchorRoom::kStatusShow);
    }

}

