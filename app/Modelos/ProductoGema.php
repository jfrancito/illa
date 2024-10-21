<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class ProductoGema extends Model
{
    protected $table = 'productogemas';
    public $timestamps=false;
    protected $primaryKey = 'id';
	public $incrementing = false;
	public $keyType = 'string';

    
}
