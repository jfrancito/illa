<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class EntidadFinanciera extends Model
{
     protected $table = 'entidadfinancieras';
    public $timestamps=false;
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $keyType = 'string';
}
