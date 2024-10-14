<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';
    public $timestamps=false;
    protected $primaryKey = 'id';
	public $incrementing = false;
	public $keyType = 'string';

    public function categoria()
    {
        return  $this->belongsTo('App\Modelos\Categoria','categoria_id','id');
    }

    public function subcategoria()
    {
        return  $this->belongsTo('App\Modelos\Categoria','subcategoria_id','id');
    }
}
