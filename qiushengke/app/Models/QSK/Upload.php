<?php

namespace App\Models\QSK;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Upload extends Model
{
    protected $connection = "qsk";

    public function getUrl()
    {
        return Storage::disk($this->disks)->url($this->path);
    }
}
