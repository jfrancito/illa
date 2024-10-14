<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class LogEmision extends Model
{
    protected $table = 'logemitir';
    public $timestamps=false;
    protected $primaryKey = 'id';
}
