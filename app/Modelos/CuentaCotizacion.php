<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class CuentaCotizacion extends Model
{
    //cuentascotizacion
    protected $table = 'cuentascotizacion';
    public $timestamps=false;
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';

    public function Cotizacion(){
        return $this->belongsTo('App\Modelos\Cotizacion','cotizacion_id','id');
    }
    public function Cuenta(){
        return $this->belongsTo('App\Modelos\EntidadFinanciera','entidad_id','id');
    }
}
