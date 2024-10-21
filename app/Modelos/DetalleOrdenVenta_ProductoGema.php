<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class DetalleOrdenVenta_ProductoGema extends Model
{
	protected $table = 'detalleordenventas_productogemas';
	public $timestamps=false;
	protected $primaryKey = 'id';
	public $incrementing = false;
	public $keyType = 'string';

	public function OrdenVenta(){
		return $this->belongsTo('App\Modelos\OrdenVenta','ordenventa_id','id');
	}

	public function DetalleOrdenVenta(){
		return $this->belongsTo('App\Modelos\DetalleOrdenVenta','detalleordenventa_id','id');
	}
}
