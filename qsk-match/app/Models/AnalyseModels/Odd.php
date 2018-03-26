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
    const default_banker_id = 7;//默认公司id 取bet365(角球计算、历史同赔)
    const default_calculate_cid = 6;//默认计算用的博彩公司id 取sb

    //澳门、sb、立博、bet365、易胜博、韦德、金宝博
    const default_top_bankers = [5, 6, 17, 7, 8, 9, 12];
    //sb、bet365、澳门、立博、易胜博、韦德、金宝博
    const default_top_rank_bankerStr = "6,7,5,17,8,9,12";
    //
    //
    public $timestamps = false;

    public function changeConnection($conn)
    {
        $this->connection = $conn;
    }
}
