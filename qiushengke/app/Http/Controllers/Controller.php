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
        $json_str = Storage::disk("public")->get('/league/foot/sub.json');
        $json = json_decode($json_str,true);
        if ($json && strlen($json_str) > 0){
            $footLeague = $json;
        }
        else{
            //默认的,假设后台挂了
            $footLeague = [
                array('id'=>'360','name'=>'中超','type'=>1),
                array('id'=>'1','name'=>'英超','type'=>1),
                array('id'=>'42','name'=>'西甲','type'=>1),
                array('id'=>'30','name'=>'意甲','type'=>1),
                array('id'=>'64','name'=>'法甲','type'=>1),
                array('id'=>'51','name'=>'德甲','type'=>1),
                array('id'=>'642','name'=>'亚冠','type'=>2),
                array('id'=>'602','name'=>'欧冠','type'=>2),
                array('id'=>'564','name'=>'世界杯','type'=>2)
            ];
        }
        $this->html_var['footLeagues'] = $footLeague;

        $json_str = Storage::disk("public")->get('/league/basket/sub.json');
        $json = json_decode($json_str,true);
        if ($json && strlen($json_str) > 0){
            $basketLeague = $json;
        }
        else{
            $basketLeague = [
                array('id'=>'1','name'=>'NBA','type'=>1),
                array('id'=>'4','name'=>'CBA','type'=>1),
                array('id'=>'89','name'=>'Euro','type'=>1),
            ];
        }
        $this->html_var['basketLeagues'] = $basketLeague;
    }
}
