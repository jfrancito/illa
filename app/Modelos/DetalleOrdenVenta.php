<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class DetalleOrdenVenta extends Model
{
	protected $table = 'detalleordenventas';
	public $timestamps=false;
	protected $primaryKey = 'id';
	public $incrementing = false;
	public $keyType = 'string';

	public function Producto(){
		return $this->belongsTo('App\Modelos\Producto','producto_id','id');
	}

	public function OrdenVenta(){
		return $this->belongsTo('App\Modelos\OrdenVenta','ordenventa_id','id');
	}
}
