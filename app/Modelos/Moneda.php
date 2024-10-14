<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Moneda extends Model
{
    protected $table = 'monedas';
    public $timestamps=false;
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';
}
