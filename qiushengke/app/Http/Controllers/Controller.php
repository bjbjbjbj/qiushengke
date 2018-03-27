<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected $html_var = [];

    function __construct()
    {
        if (Storage::disk("public")->exists('/league/foot/sub.json')) {
            $json_str = Storage::disk("public")->get('/league/foot/sub.json');
            $json = json_decode($json_str, true);
            if ($json && strlen($json_str) > 0) {
                $footLeague = $json;
            }
        }
        if (!isset($footLeague)){
            //默认的,假设后台挂了
            $footLeague = [
                array('id'=>'360','name'=>'中超','type'=>1,'url'=>'/league/foot/360.html'),
                array('id'=>'1','name'=>'英超','type'=>1,'url'=>'/league/foot/1.html'),
                array('id'=>'42','name'=>'西甲','type'=>1,'url'=>'/league/foot/42.html'),
                array('id'=>'30','name'=>'意甲','type'=>1,'url'=>'/league/foot/30.html'),
                array('id'=>'64','name'=>'法甲','type'=>1,'url'=>'/league/foot/64.html'),
                array('id'=>'51','name'=>'德甲','type'=>1,'url'=>'/league/foot/51.html'),
//                array('id'=>'642','name'=>'亚冠','type'=>2),
//                array('id'=>'602','name'=>'欧冠','type'=>2),
//                array('id'=>'564','name'=>'世界杯','type'=>2)
            ];
        }
        $this->html_var['footLeagues'] = $footLeague;

        if (Storage::disk("public")->exists('/league/basket/sub.json')) {
            $json_str = Storage::disk("public")->get('/league/basket/sub.json');
            $json = json_decode($json_str, true);
            if ($json && strlen($json_str) > 0) {
                $basketLeague = $json;
            }
        }
        if (!isset($basketLeague)){
            $basketLeague = [
                array('id'=>'1','name'=>'NBA','type'=>1,'url'=>'/league/basket/1.html'),
                array('id'=>'4','name'=>'CBA','type'=>1,'url'=>'/league/basket/4.html'),
                array('id'=>'89','name'=>'Euro','type'=>1,'url'=>'/league/basket/89.html'),
            ];
        }
        $this->html_var['basketLeagues'] = $basketLeague;
    }
}
