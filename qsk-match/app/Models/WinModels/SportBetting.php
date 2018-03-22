<?php

namespace App\Models\WinModels;

use Illuminate\Database\Eloquent\Model;

class SportBetting extends Model
{
    //
    protected $connection = 'win_matches';
    public $timestamps = false;

    protected $hidden = ['id','hid','aid','lid','mid'];
}
