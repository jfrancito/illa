<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class LogExtornar extends Model
{
    protected $table = 'logextornar';
    public $timestamps=false;
    protected $primaryKey = 'id';
    // public $incrementing = false;
    // public $keyType = 'string';
}
