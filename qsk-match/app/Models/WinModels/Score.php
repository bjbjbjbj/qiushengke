<?php

namespace App\Models\WinModels;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    //
    protected $connection = 'win_matches';
    public $timestamps = false;
}
