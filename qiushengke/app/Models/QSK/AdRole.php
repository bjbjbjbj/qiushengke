<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/14
 * Time: 11:34
 */

namespace App\Models\QSK;


use Illuminate\Database\Eloquent\Model;

class AdRole extends Model
{
    protected $connection = "qsk";

    public function resources() {
        //$related, $table = null, $foreignKey = null, $relatedKey = null, $relation = null
        return $this->belongsToMany(AdResource::class, 'ad_role_resources', 'ro_id', 're_id');
    }
}