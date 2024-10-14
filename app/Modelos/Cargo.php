<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    //
    protected $table = 'cargos';
    public $timestamps=false;

    protected $primaryKey = 'id';
    public $incrementing = false;
}
