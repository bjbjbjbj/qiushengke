<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected $html_var = [];

    function __construct()
    {
        $footLeague = [
            array('id'=>'46','name'=>'中超','type'=>1),
            array('id'=>'31','name'=>'英超','type'=>1),
            array('id'=>'26','name'=>'西甲','type'=>1),
            array('id'=>'29','name'=>'意甲','type'=>1),
            array('id'=>'11','name'=>'法甲','type'=>1),
            array('id'=>'8','name'=>'德甲','type'=>1),
            array('id'=>'139','name'=>'亚冠','type'=>2),
            array('id'=>'73','name'=>'欧冠','type'=>2),
            array('id'=>'57','name'=>'世界杯','type'=>2)
        ];
        $links = array();
        foreach ($footLeague as $link) {
            if ($link['type'] == 1) {
                $link['url'] = '/league/foot/' . $link['id'] . '.html';
            }
            elseif ($link['type'] == 2) {
                $link['url'] = '/cup_league/foot/' . $link['id'] . '.html';
            }
            $links[] = $link;
        }
        $this->html_var['footLeagues'] = $links;

        $basketLeague = [
            array('id'=>'1','name'=>'NBA','type'=>1),
            array('id'=>'4','name'=>'CBA','type'=>1),
            array('id'=>'2','name'=>'WNBA','type'=>1),
            array('id'=>'89','name'=>'欧锦赛','type'=>1),
        ];
        $links = array();
        foreach ($basketLeague as $link) {
            $link['url'] = '/league/basket/' . $link['id'] . '.html';
            $links[] = $link;
        }
        $this->html_var['basketLeagues'] = $links;
    }
}
