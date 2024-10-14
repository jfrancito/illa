<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class DetalleCotizacion extends Model
{
    protected $table = 'detallecotizaciones';
    public $timestamps=false;
    protected $primaryKey = 'id';
	public $incrementing = false;
	public $keyType = 'string';

    public function Unidad(){
        return $this->belongsTo('App\Modelos\Categoria','unidadmedida_id','id');
    }

}
