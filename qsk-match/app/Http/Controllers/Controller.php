<?php

namespace App\Http\Controllers;

use App\Models\LiaoGouModels\Match;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index(Request $request){
        $matches = Match::orderby('time','asc')
            ->take(10)
            ->get();
        dump($matches);
    }
}
