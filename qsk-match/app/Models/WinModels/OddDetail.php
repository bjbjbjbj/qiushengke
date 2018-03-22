<?php

namespace App\Models\WinModels;

use Illuminate\Database\Eloquent\Model;

class OddDetail extends Model
{
    protected $connection = 'win_matches';
    //
    public $timestamps = false;
}
