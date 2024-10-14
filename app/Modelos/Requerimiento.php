<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Requerimiento extends Model
{
    protected $table = 'requerimientos';
    public $timestamps=false;
    protected $primaryKey = 'id';
	public $incrementing = false;
	public $keyType = 'string';

    public function Cotizacion()
    {
        return $this->HasOne('App\Modelos\Cotizacion','lote','lote')->whereActivo(1);
    }

}
