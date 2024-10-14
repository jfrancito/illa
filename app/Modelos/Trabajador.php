<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Trabajador extends Model
{
    //
     protected $table = 'trabajadores';
    public $timestamps=false;

    protected $primaryKey = 'id';
    public $incrementing = false;

 
    public function gerencia()
    {
        return $this->belongsTo('App\Modelos\Gerencia','gerencia_id','id');
    }
        public function area()
    {
        return $this->belongsTo('App\Modelos\Area','area_id','id');
    }

    public function cargo()
    {
        return $this->belongsTo('App\Modelos\Cargo');
    }


    public function tipomonedacts()
    {
        return $this->belongsTo('App\Modelos\Tipomoneda','tipomonedacts_id','id');
    }
   
    public function tipodocumento()
    {
        return $this->belongsTo('App\Modelos\Tipodocumento');
    }

    public function estadocivil()
    {
        return $this->belongsTo('App\Modelos\Estadocivil');
    }

    public function tipolicenciavehiculo()
    {
        return $this->belongsTo('App\Modelos\Tipolicenciavehiculo');
    }

    public function codigotelefono()
    {
        return $this->belongsTo('App\Modelos\Codigotelefono');
    }

    public function nacionalidad()
    {
        return $this->belongsTo('App\Modelos\Nacionalidad','nacionalidad_id','id');
    }

    ////DATOS DOMICILIO
    public function pais()
    {
        return $this->belongsTo('App\Modelos\Pais','pais_id','id');
    }

    public function departamento()
    {
        return $this->belongsTo('App\Modelos\Departamento','departamento_id','id');
    }

    public function tipovia()
    {
        return $this->belongsTo('App\Modelos\Tipovia');
    }

    public function horario()
    {
        return $this->belongsTo('App\Modelos\Horario');
    }

    public function tipozona()
    {
        return $this->belongsTo('App\Modelos\Tipozona');
    }


    public function tipotrabajador()
    {
        return $this->belongsTo('App\Modelos\Tipotrabajador');
    }

   
    public function motivobaja()
    {
        return $this->belongsTo('App\Modelos\Motivobaja');
    }


}
