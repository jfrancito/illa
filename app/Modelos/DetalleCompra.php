<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{
    protected $table = 'detallecompras';
    public $timestamps=false;
    protected $primaryKey = 'id';
	public $incrementing = false;
	public $keyType = 'string';

    public function Producto(){
        return $this->belongsTo('App\Modelos\Producto','producto_id','id');
    }

}
