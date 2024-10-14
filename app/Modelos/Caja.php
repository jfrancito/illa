<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    protected $table = 'caja';
    public $timestamps=false;
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';

}
