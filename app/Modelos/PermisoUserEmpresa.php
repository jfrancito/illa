<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class PermisoUserEmpresa extends Model
{
         protected $table = 'permisouserempresas';
    public $timestamps=false;
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';
}
