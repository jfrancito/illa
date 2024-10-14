<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class CajaDetalle extends Model
{
    protected $table = 'cajadetalle';
    public $timestamps=false;
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';
}
