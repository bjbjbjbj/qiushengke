<?php

namespace App\Http\Controllers\WinSpiderManager;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SpriderManagerController extends Controller
{
    //
    public function spiderIndex(Request $request)
    {
        return view('admin.adminSpriderManager');
    }

    public function spiderLotteryIndex(Request $request)
    {
        return view('admin.adminLotterySpriderManager');
    }
}
