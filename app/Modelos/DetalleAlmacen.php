<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class DetalleAlmacen extends Model
{
    protected $table = 'detallealmacen';
    public $timestamps=false;
    protected $primaryKey = 'id';
	public $incrementing = false;
	public $keyType = 'string';

    public function Proveedor(){
        return $this->belongsTo('App\Modelos\Proveedor','proveedor_id','id');
    }   

    public function Producto(){
        return $this->belongsTo('App\Modelos\Producto','producto_id','id');
    }  


}