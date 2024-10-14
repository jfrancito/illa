<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class LogAprobacion extends Model
{
    protected $table = 'logaprobar';
    public $timestamps=false;
    protected $primaryKey = 'id';
}
