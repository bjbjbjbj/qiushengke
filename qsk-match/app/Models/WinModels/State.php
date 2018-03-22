<?php

namespace App\Models\WinModels;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    //
    public $incrementing = false;
    protected $connection = 'win_matches';
    public $timestamps = false;
}
