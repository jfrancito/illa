<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    protected $table = 'almacen';
    public $timestamps=false;
    protected $primaryKey = 'id';
	public $incrementing = false;
	public $keyType = 'string';

    public function Detalle(){
        return $this->hasMany('App\Modelos\DetalleAlmacen','almacen_id','id')->whereActivo(1);
    }


}