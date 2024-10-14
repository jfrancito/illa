<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class CuentasEmpresa extends Model
{
    //cuentasempresa
    protected $table = 'cuentasempresa';
    public $timestamps=false;
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';

    public function Entidad(){
        return $this->belongsTo('App\Modelos\EntidadFinanciera','entidad_id','id');
    }

     public function Moneda(){
        return $this->belongsTo('App\Modelos\Moneda','moneda_id','id');
    }

}
