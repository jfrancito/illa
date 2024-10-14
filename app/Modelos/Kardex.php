<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Kardex extends Model
{
    protected $table = 'kardex';
    public $timestamps=false;
    protected $primaryKey = 'id';
	public $incrementing = false;
	public $keyType = 'string';

    public function scopeCodAlmacen($query,$almacen_id){
        if(trim($almacen_id) != ''){
            $query->where('almacen_id', '=', $almacen_id);
        }
    }
 
     public function scopeCodProducto($query,$producto_id){
        if(trim($producto_id) != ''){
            $query->where('producto_id', '=', $producto_id);
        }
    }   



}