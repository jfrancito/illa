<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class DetalleEsquemaProducto extends Model
{
      protected $table = 'detalleesquemaproducto';
    public $timestamps=false;
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';
}
