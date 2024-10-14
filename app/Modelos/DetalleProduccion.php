<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class DetalleProduccion extends Model
{
  protected $table = 'detalleproduccions';
    public $timestamps=false;
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';

    public function Unidad(){
        return $this->belongsTo('App\Modelos\Categoria','unidadmedida_id','id');
    }

    public function producto(){
        return $this->belongsTo('App\Modelos\Producto','producto_id','id');
    }

}
