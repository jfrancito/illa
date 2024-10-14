<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    protected $table = 'cotizaciones';
    public $timestamps=false;
    protected $primaryKey = 'id';
	public $incrementing = false;
	public $keyType = 'string';

    public function Extorno(){
        return $this->belongsTo('App\Modelos\LogExtornar','idtabla','id')->whereTabla('cotizaciones')->whereActivo(1);
    }


    public function Emision(){
        return $this->belongsTo('App\Modelos\LogEmitir','idtabla','id')->whereTabla('cotizaciones')->whereActivo(1);
    }

    public function Aprobacion(){
        return $this->belongsTo('App\Modelos\LogAprobar','idtabla','id')->whereTabla('cotizaciones')->whereActivo(1);
    }

    public function Estado(){
        return $this->belongsTo('App\Modelos\Categoria','id','estado_id')->whereTipo_categoria('ESTADO_GENERAL');
    }

    public function Detalle(){
        return $this->hasMany('App\Modelos\DetalleCotizacion','cotizacion_id','id');
    }

    public function ContDetalleActivos(){
        return (int)$this->hasMany('App\Modelos\DetalleCotizacion','cotizacion_id','id')->whereActivo(1)->count();
    }
    
    public function ContDetalleCategoriasActivos(){
        return (int)$this->hasMany('App\Modelos\DetalleCotizacion','cotizacion_id','id')->whereIspadre(1)->whereActivo(1)->count();
    }
    
    public function ContDetalleServiciosActivos(){
        return (int)$this->hasMany('App\Modelos\DetalleCotizacion','cotizacion_id','id')->whereIspadre(0)->whereActivo(1)->count();
    }

    public function Moneda()
    {
        return $this->belongsTo('App\Modelos\Moneda','moneda_id','id');
    }
}
