<?php

namespace App\Models\AnalyseModels;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    protected $connection = 'analyse_match';
    //
    public $timestamps = false;
    public function changeConnection($conn)
    {
        $this->connection = $conn;
    }
}
