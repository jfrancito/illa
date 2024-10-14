<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    //
    protected $table = 'areas';
    public $timestamps=false;

    protected $primaryKey = 'id';
    public $incrementing = false;
}
