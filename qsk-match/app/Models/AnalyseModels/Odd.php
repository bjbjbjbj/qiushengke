<?php

namespace App\Models\AnalyseModels;

use Illuminate\Database\Eloquent\Model;

class Odd extends Model
{
    protected $connection = 'analyse_match';
    const k_odd_type_asian = 1;//亚盘
    const k_odd_type_ou = 2;//大小
    const k_odd_type_europe = 3;//欧赔
    const k_odd_type_corner = 4;//角球
    const default_banker_id = 5;//默认公司id 取bet365(角球计算、历史同赔)
    const default_calculate_cid = 2;//默认计算用的博彩公司id 取sb

    const default_top_bankers = [1, 2, 3, 5, 7, 8, 12];
    const default_top_rank_bankerStr = "2,5,1,3,7,8,12";
    //
    //
    public $timestamps = false;

    public function changeConnection($conn)
    {
        $this->connection = $conn;
    }
}
