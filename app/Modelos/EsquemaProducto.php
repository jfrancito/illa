<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class EsquemaProducto extends Model
{
    //
     protected $table = 'esquemaproducto';
    public $timestamps=false;
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';

    public function Producto(){
        return $this->belongsTo('App\Modelos\Producto','producto_id','id');
    }

    public function Extorno(){
        return $this->belongsTo('App\Modelos\LogExtornar','id','idtabla');
    }

    public function Detalle(){
        return $this->hasMany('App\Modelos\DetalleCompra','compra_id','id')->whereActivo(1);
    }

    public function DetalleEsquema(){
        return $this->hasMany('App\Modelos\DetalleEsquemaProducto','esquemaproducto_id','id')->whereActivo(1);
    }


    public function Proveedor(){
        return $this->belongsTo('App\Modelos\Proveedor','proveedor_id','id');
    }   

    public function scopeCodProveedor($query,$proveedor_id){
        if(trim($proveedor_id) != ''){
            $query->where('proveedor_id', '=', $proveedor_id);
        }
    }
 
     public function scopeCodEstado($query,$estado_id){
        if(trim($estado_id) != ''){
            $query->where('estado_id', '=', $estado_id);
        }
    }   
}
