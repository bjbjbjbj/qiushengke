<?php

namespace App\Models\WinModels;

use Illuminate\Database\Eloquent\Model;

class SpiderLog extends Model
{
    //
    protected $connection = 'win_matches';
    protected $primaryKey = "url";
}
