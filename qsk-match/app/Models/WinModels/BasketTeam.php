<?php

namespace App\Models\WinModels;

use Illuminate\Database\Eloquent\Model;

class BasketTeam extends Model
{
    public $incrementing = false;
    protected $connection = 'win_matches';
}
