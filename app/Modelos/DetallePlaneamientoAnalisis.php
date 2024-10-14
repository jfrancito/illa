<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class DetallePlaneamientoAnalisis extends Model
{
    protected $table = 'detalleplaneamientoanalisis';
    public $timestamps=false;
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';
}
