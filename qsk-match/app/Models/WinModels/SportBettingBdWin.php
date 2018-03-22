<?php

namespace App\Models\WinModels;

use Illuminate\Database\Eloquent\Model;

class SportBettingBdWin extends Model
{
    protected $connection = 'win_matches';
    public $timestamps = false;
}
