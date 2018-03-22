<?php

namespace App\Models\AnalyseModels;

use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    protected $connection = 'analyse_match';
    //

    public $timestamps = false;
}
