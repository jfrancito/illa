<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class OrdenVenta extends Model
{
	protected $table = 'ordenventas';
	public $timestamps=false;
	protected $primaryKey = 'id';
	public $incrementing = false;
	public $keyType = 'string';

	public function Extorno(){
		return $this->belongsTo('App\Modelos\LogExtornar','id','idtabla');
	}

	public function Detalle(){
		return $this->hasMany('App\Modelos\DetalleOrdenVenta','ordenventa_id','id')->whereActivo(1);
	}

	public function Cliente(){
		return $this->belongsTo('App\Modelos\Cliente','cliente_id','id');
	}   

	public function scopeCodCliente($query,$cliente_id){
		if(trim($cliente_id) != ''){
			$query->where('cliente_id', '=', $cliente_id);
		}
	}

	 public function scopeCodEstado($query,$estado_id){
		if(trim($estado_id) != ''){
			$query->where('estado_id', '=', $estado_id);
		}
	}   
}
